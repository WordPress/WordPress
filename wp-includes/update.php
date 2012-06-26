<?php
/**
 * A simple set of functions to check our version 1.0 update service.
 *
 * @package WordPress
 * @since 2.3.0
 */

/**
 * Check WordPress version against the newest version.
 *
 * The WordPress version, PHP version, and Locale is sent. Checks against the
 * WordPress server at api.wordpress.org server. Will only check if WordPress
 * isn't installing.
 *
 * @package WordPress
 * @since 2.3.0
 * @uses $wp_version Used to check against the newest WordPress version.
 *
 * @return mixed Returns null if update is unsupported. Returns false if check is too soon.
 */
function wp_version_check() {
	if ( defined('WP_INSTALLING') )
		return;

	global $wpdb, $wp_local_package;
	include ABSPATH . WPINC . '/version.php'; // include an unmodified $wp_version
	$php_version = phpversion();

	$current = get_site_transient( 'update_core' );
	if ( ! is_object($current) ) {
		$current = new stdClass;
		$current->updates = array();
		$current->version_checked = $wp_version;
	}

	// Wait 60 seconds between multiple version check requests
	$timeout = 60;
	$time_not_changed = isset( $current->last_checked ) && $timeout > ( time() - $current->last_checked );
	if ( $time_not_changed )
		return false;

	$locale = apply_filters( 'core_version_check_locale', get_locale() );

	// Update last_checked for current to prevent multiple blocking requests if request hangs
	$current->last_checked = time();
	set_site_transient( 'update_core', $current );

	if ( method_exists( $wpdb, 'db_version' ) )
		$mysql_version = preg_replace('/[^0-9.].*/', '', $wpdb->db_version());
	else
		$mysql_version = 'N/A';

	if ( is_multisite( ) ) {
		$user_count = get_user_count( );
		$num_blogs = get_blog_count( );
		$wp_install = network_site_url( );
		$multisite_enabled = 1;
	} else {
		$user_count = count_users( );
		$user_count = $user_count['total_users'];
		$multisite_enabled = 0;
		$num_blogs = 1;
		$wp_install = home_url( '/' );
	}

	$query = array(
		'version'           => $wp_version,
		'php'               => $php_version,
		'locale'            => $locale,
		'mysql'             => $mysql_version,
		'local_package'     => isset( $wp_local_package ) ? $wp_local_package : '',
		'blogs'             => $num_blogs,
		'users'             => $user_count,
		'multisite_enabled' => $multisite_enabled
	);

	$url = 'http://api.wordpress.org/core/version-check/1.6/?' . http_build_query( $query, null, '&' );

	$options = array(
		'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3 ),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
		'headers' => array(
			'wp_install' => $wp_install,
			'wp_blog' => home_url( '/' )
		)
	);

	$response = wp_remote_get($url, $options);

	if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
		return false;

	$body = trim( wp_remote_retrieve_body( $response ) );
	$body = maybe_unserialize( $body );

	if ( ! is_array( $body ) || ! isset( $body['offers'] ) )
		return false;

	$offers = $body['offers'];

	foreach ( $offers as &$offer ) {
		foreach ( $offer as $offer_key => $value ) {
			if ( 'packages' == $offer_key )
				$offer['packages'] = (object) array_intersect_key( array_map( 'esc_url', $offer['packages'] ),
					array_fill_keys( array( 'full', 'no_content', 'new_bundled', 'partial' ), '' ) );
			elseif ( 'download' == $offer_key )
				$offer['download'] = esc_url( $value );
			else
				$offer[ $offer_key ] = esc_html( $value );
		}
		$offer = (object) array_intersect_key( $offer, array_fill_keys( array( 'response', 'download', 'locale',
			'packages', 'current', 'php_version', 'mysql_version', 'new_bundled', 'partial_version' ), '' ) );
	}

	$updates = new stdClass();
	$updates->updates = $offers;
	$updates->last_checked = time();
	$updates->version_checked = $wp_version;
	set_site_transient( 'update_core',  $updates);
}

/**
 * Check plugin versions against the latest versions hosted on WordPress.org.
 *
 * The WordPress version, PHP version, and Locale is sent along with a list of
 * all plugins installed. Checks against the WordPress server at
 * api.wordpress.org. Will only check if WordPress isn't installing.
 *
 * @package WordPress
 * @since 2.3.0
 * @uses $wp_version Used to notify the WordPress version.
 *
 * @return mixed Returns null if update is unsupported. Returns false if check is too soon.
 */
function wp_update_plugins() {
	include ABSPATH . WPINC . '/version.php'; // include an unmodified $wp_version

	if ( defined('WP_INSTALLING') )
		return false;

	// If running blog-side, bail unless we've not checked in the last 12 hours
	if ( !function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	$plugins = get_plugins();
	$active  = get_option( 'active_plugins', array() );
	$current = get_site_transient( 'update_plugins' );
	if ( ! is_object($current) )
		$current = new stdClass;

	$new_option = new stdClass;
	$new_option->last_checked = time();

	// Check for update on a different schedule, depending on the page.
	switch ( current_filter() ) {
		case 'load-update-core.php' :
			$timeout = 60; // 1 min
			break;
		case 'load-plugins.php' :
		case 'load-update.php' :
			$timeout = 3600; // 1 hour
			break;
		default :
			$timeout = 43200; // 12 hours
	}

	$time_not_changed = isset( $current->last_checked ) && $timeout > ( time() - $current->last_checked );

	if ( $time_not_changed ) {
		$plugin_changed = false;
		foreach ( $plugins as $file => $p ) {
			$new_option->checked[ $file ] = $p['Version'];

			if ( !isset( $current->checked[ $file ] ) || strval($current->checked[ $file ]) !== strval($p['Version']) )
				$plugin_changed = true;
		}

		if ( isset ( $current->response ) && is_array( $current->response ) ) {
			foreach ( $current->response as $plugin_file => $update_details ) {
				if ( ! isset($plugins[ $plugin_file ]) ) {
					$plugin_changed = true;
					break;
				}
			}
		}

		// Bail if we've checked recently and if nothing has changed
		if ( ! $plugin_changed )
			return false;
	}

	// Update last_checked for current to prevent multiple blocking requests if request hangs
	$current->last_checked = time();
	set_site_transient( 'update_plugins', $current );

	$to_send = (object) compact('plugins', 'active');

	$options = array(
		'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3),
		'body' => array( 'plugins' => serialize( $to_send ) ),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);

	$raw_response = wp_remote_post('http://api.wordpress.org/plugins/update-check/1.0/', $options);

	if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) )
		return false;

	$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );

	if ( is_array( $response ) )
		$new_option->response = $response;
	else
		$new_option->response = array();

	set_site_transient( 'update_plugins', $new_option );
}

/**
 * Check theme versions against the latest versions hosted on WordPress.org.
 *
 * A list of all themes installed in sent to WP. Checks against the
 * WordPress server at api.wordpress.org. Will only check if WordPress isn't
 * installing.
 *
 * @package WordPress
 * @since 2.7.0
 * @uses $wp_version Used to notify the WordPress version.
 *
 * @return mixed Returns null if update is unsupported. Returns false if check is too soon.
 */
function wp_update_themes() {
	include ABSPATH . WPINC . '/version.php'; // include an unmodified $wp_version

	if ( defined( 'WP_INSTALLING' ) )
		return false;

	$installed_themes = wp_get_themes();
	$last_update = get_site_transient( 'update_themes' );
	if ( ! is_object($last_update) )
		$last_update = new stdClass;

	$themes = array();
	$checked = array();

	// Put slug of current theme into request.
	$themes['current_theme'] = get_option( 'stylesheet' );

	foreach ( $installed_themes as $theme ) {
		$checked[ $theme->get_stylesheet() ] = $theme->get('Version');

		$themes[ $theme->get_stylesheet() ] = array(
			'Name'       => $theme->get('Name'),
			'Title'      => $theme->get('Name'),
			'Version'    => $theme->get('Version'),
			'Author'     => $theme->get('Author'),
			'Author URI' => $theme->get('AuthorURI'),
			'Template'   => $theme->get_template(),
			'Stylesheet' => $theme->get_stylesheet(),
		);
	}

	// Check for update on a different schedule, depending on the page.
	switch ( current_filter() ) {
		case 'load-update-core.php' :
			$timeout = 60; // 1 min
			break;
		case 'load-themes.php' :
		case 'load-update.php' :
			$timeout = 3600; // 1 hour
			break;
		default :
			$timeout = 43200; // 12 hours
	}

	$time_not_changed = isset( $last_update->last_checked ) && $timeout > ( time( ) - $last_update->last_checked );

	if ( $time_not_changed ) {
		$theme_changed = false;
		foreach ( $checked as $slug => $v ) {
			$update_request->checked[ $slug ] = $v;

			if ( !isset( $last_update->checked[ $slug ] ) || strval($last_update->checked[ $slug ]) !== strval($v) )
				$theme_changed = true;
		}

		if ( isset ( $last_update->response ) && is_array( $last_update->response ) ) {
			foreach ( $last_update->response as $slug => $update_details ) {
				if ( ! isset($checked[ $slug ]) ) {
					$theme_changed = true;
					break;
				}
			}
		}

		// Bail if we've checked recently and if nothing has changed
		if ( ! $theme_changed )
			return false;
	}

	// Update last_checked for current to prevent multiple blocking requests if request hangs
	$last_update->last_checked = time();
	set_site_transient( 'update_themes', $last_update );

	$options = array(
		'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3),
		'body'			=> array( 'themes' => serialize( $themes ) ),
		'user-agent'	=> 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);

	$raw_response = wp_remote_post( 'http://api.wordpress.org/themes/update-check/1.0/', $options );

	if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) )
		return false;

	$new_update = new stdClass;
	$new_update->last_checked = time( );
	$new_update->checked = $checked;

	$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );
	if ( is_array( $response ) )
		$new_update->response = $response;

	set_site_transient( 'update_themes', $new_update );
}

/*
 * Collect counts and UI strings for available updates
 *
 * @since 3.3.0
 *
 * @return array
 */
function wp_get_update_data() {
	$counts = array( 'plugins' => 0, 'themes' => 0, 'wordpress' => 0 );

	if ( current_user_can( 'update_plugins' ) ) {
		$update_plugins = get_site_transient( 'update_plugins' );
		if ( ! empty( $update_plugins->response ) )
			$counts['plugins'] = count( $update_plugins->response );
	}

	if ( current_user_can( 'update_themes' ) ) {
		$update_themes = get_site_transient( 'update_themes' );
		if ( ! empty( $update_themes->response ) )
			$counts['themes'] = count( $update_themes->response );
	}

	if ( function_exists( 'get_core_updates' ) && current_user_can( 'update_core' ) ) {
		$update_wordpress = get_core_updates( array('dismissed' => false) );
		if ( ! empty( $update_wordpress ) && ! in_array( $update_wordpress[0]->response, array('development', 'latest') ) && current_user_can('update_core') )
			$counts['wordpress'] = 1;
	}

	$counts['total'] = $counts['plugins'] + $counts['themes'] + $counts['wordpress'];
	$update_title = array();
	if ( $counts['wordpress'] )
		$update_title[] = sprintf(__('%d WordPress Update'), $counts['wordpress']);
	if ( $counts['plugins'] )
		$update_title[] = sprintf(_n('%d Plugin Update', '%d Plugin Updates', $counts['plugins']), $counts['plugins']);
	if ( $counts['themes'] )
		$update_title[] = sprintf(_n('%d Theme Update', '%d Theme Updates', $counts['themes']), $counts['themes']);

	$update_title = ! empty( $update_title ) ? esc_attr( implode( ', ', $update_title ) ) : '';

	return array( 'counts' => $counts, 'title' => $update_title );
}

function _maybe_update_core() {
	include ABSPATH . WPINC . '/version.php'; // include an unmodified $wp_version

	$current = get_site_transient( 'update_core' );

	if ( isset( $current->last_checked ) &&
		43200 > ( time() - $current->last_checked ) &&
		isset( $current->version_checked ) &&
		$current->version_checked == $wp_version )
		return;

	wp_version_check();
}
/**
 * Check the last time plugins were run before checking plugin versions.
 *
 * This might have been backported to WordPress 2.6.1 for performance reasons.
 * This is used for the wp-admin to check only so often instead of every page
 * load.
 *
 * @since 2.7.0
 * @access private
 */
function _maybe_update_plugins() {
	$current = get_site_transient( 'update_plugins' );
	if ( isset( $current->last_checked ) && 43200 > ( time() - $current->last_checked ) )
		return;
	wp_update_plugins();
}

/**
 * Check themes versions only after a duration of time.
 *
 * This is for performance reasons to make sure that on the theme version
 * checker is not run on every page load.
 *
 * @since 2.7.0
 * @access private
 */
function _maybe_update_themes( ) {
	$current = get_site_transient( 'update_themes' );
	if ( isset( $current->last_checked ) && 43200 > ( time( ) - $current->last_checked ) )
		return;

	wp_update_themes();
}

/**
 * Schedule core, theme, and plugin update checks.
 *
 * @since 3.1.0
 */
function wp_schedule_update_checks() {
	if ( !wp_next_scheduled('wp_version_check') && !defined('WP_INSTALLING') )
		wp_schedule_event(time(), 'twicedaily', 'wp_version_check');

	if ( !wp_next_scheduled('wp_update_plugins') && !defined('WP_INSTALLING') )
		wp_schedule_event(time(), 'twicedaily', 'wp_update_plugins');

	if ( !wp_next_scheduled('wp_update_themes') && !defined('WP_INSTALLING') )
		wp_schedule_event(time(), 'twicedaily', 'wp_update_themes');
}

if ( ( ! is_main_site() && ! is_network_admin() ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) )
	return;

add_action( 'admin_init', '_maybe_update_core' );
add_action( 'wp_version_check', 'wp_version_check' );

add_action( 'load-plugins.php', 'wp_update_plugins' );
add_action( 'load-update.php', 'wp_update_plugins' );
add_action( 'load-update-core.php', 'wp_update_plugins' );
add_action( 'admin_init', '_maybe_update_plugins' );
add_action( 'wp_update_plugins', 'wp_update_plugins' );

add_action( 'load-themes.php', 'wp_update_themes' );
add_action( 'load-update.php', 'wp_update_themes' );
add_action( 'load-update-core.php', 'wp_update_themes' );
add_action( 'admin_init', '_maybe_update_themes' );
add_action( 'wp_update_themes', 'wp_update_themes' );

add_action('init', 'wp_schedule_update_checks');
