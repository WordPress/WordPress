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
 * @since 2.3.0
 * @global string $wp_version Used to check against the newest WordPress version.
 * @global wpdb   $wpdb
 * @global string $wp_local_package
 *
 * @param array $extra_stats Extra statistics to report to the WordPress.org API.
 * @param bool  $force_check Whether to bypass the transient cache and force a fresh update check. Defaults to false, true if $extra_stats is set.
 */
function wp_version_check( $extra_stats = array(), $force_check = false ) {
	if ( wp_installing() ) {
		return;
	}

	global $wpdb, $wp_local_package;
	// include an unmodified $wp_version
	include( ABSPATH . WPINC . '/version.php' );
	$php_version = phpversion();

	$current = get_site_transient( 'update_core' );
	$translations = wp_get_installed_translations( 'core' );

	// Invalidate the transient when $wp_version changes
	if ( is_object( $current ) && $wp_version != $current->version_checked )
		$current = false;

	if ( ! is_object($current) ) {
		$current = new stdClass;
		$current->updates = array();
		$current->version_checked = $wp_version;
	}

	if ( ! empty( $extra_stats ) )
		$force_check = true;

	// Wait 60 seconds between multiple version check requests
	$timeout = 60;
	$time_not_changed = isset( $current->last_checked ) && $timeout > ( time() - $current->last_checked );
	if ( ! $force_check && $time_not_changed ) {
		return;
	}

	/**
	 * Filter the locale requested for WordPress core translations.
	 *
	 * @since 2.8.0
	 *
	 * @param string $locale Current locale.
	 */
	$locale = apply_filters( 'core_version_check_locale', get_locale() );

	// Update last_checked for current to prevent multiple blocking requests if request hangs
	$current->last_checked = time();
	set_site_transient( 'update_core', $current );

	if ( method_exists( $wpdb, 'db_version' ) )
		$mysql_version = preg_replace('/[^0-9.].*/', '', $wpdb->db_version());
	else
		$mysql_version = 'N/A';

	if ( is_multisite() ) {
		$user_count = get_user_count();
		$num_blogs = get_blog_count();
		$wp_install = network_site_url();
		$multisite_enabled = 1;
	} else {
		$user_count = count_users();
		$user_count = $user_count['total_users'];
		$multisite_enabled = 0;
		$num_blogs = 1;
		$wp_install = home_url( '/' );
	}

	$query = array(
		'version'            => $wp_version,
		'php'                => $php_version,
		'locale'             => $locale,
		'mysql'              => $mysql_version,
		'local_package'      => isset( $wp_local_package ) ? $wp_local_package : '',
		'blogs'              => $num_blogs,
		'users'              => $user_count,
		'multisite_enabled'  => $multisite_enabled,
		'initial_db_version' => get_site_option( 'initial_db_version' ),
	);

	$post_body = array(
		'translations' => wp_json_encode( $translations ),
	);

	if ( is_array( $extra_stats ) )
		$post_body = array_merge( $post_body, $extra_stats );

	$url = $http_url = 'http://api.wordpress.org/core/version-check/1.7/?' . http_build_query( $query, null, '&' );
	if ( $ssl = wp_http_supports( array( 'ssl' ) ) )
		$url = set_url_scheme( $url, 'https' );

	$options = array(
		'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3 ),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
		'headers' => array(
			'wp_install' => $wp_install,
			'wp_blog' => home_url( '/' )
		),
		'body' => $post_body,
	);

	$response = wp_remote_post( $url, $options );
	if ( $ssl && is_wp_error( $response ) ) {
		trigger_error( __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.' ) . ' ' . __( '(WordPress could not establish a secure connection to WordPress.org. Please contact your server administrator.)' ), headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE );
		$response = wp_remote_post( $http_url, $options );
	}

	if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
		return;
	}

	$body = trim( wp_remote_retrieve_body( $response ) );
	$body = json_decode( $body, true );

	if ( ! is_array( $body ) || ! isset( $body['offers'] ) ) {
		return;
	}

	$offers = $body['offers'];

	foreach ( $offers as &$offer ) {
		foreach ( $offer as $offer_key => $value ) {
			if ( 'packages' == $offer_key )
				$offer['packages'] = (object) array_intersect_key( array_map( 'esc_url', $offer['packages'] ),
					array_fill_keys( array( 'full', 'no_content', 'new_bundled', 'partial', 'rollback' ), '' ) );
			elseif ( 'download' == $offer_key )
				$offer['download'] = esc_url( $value );
			else
				$offer[ $offer_key ] = esc_html( $value );
		}
		$offer = (object) array_intersect_key( $offer, array_fill_keys( array( 'response', 'download', 'locale',
			'packages', 'current', 'version', 'php_version', 'mysql_version', 'new_bundled', 'partial_version', 'notify_email', 'support_email', 'new_files' ), '' ) );
	}

	$updates = new stdClass();
	$updates->updates = $offers;
	$updates->last_checked = time();
	$updates->version_checked = $wp_version;

	if ( isset( $body['translations'] ) )
		$updates->translations = $body['translations'];

	set_site_transient( 'update_core', $updates );

	if ( ! empty( $body['ttl'] ) ) {
		$ttl = (int) $body['ttl'];
		if ( $ttl && ( time() + $ttl < wp_next_scheduled( 'wp_version_check' ) ) ) {
			// Queue an event to re-run the update check in $ttl seconds.
			wp_schedule_single_event( time() + $ttl, 'wp_version_check' );
		}
	}

	// Trigger background updates if running non-interactively, and we weren't called from the update handler.
	if ( defined( 'DOING_CRON' ) && DOING_CRON && ! doing_action( 'wp_maybe_auto_update' ) ) {
		do_action( 'wp_maybe_auto_update' );
	}
}

/**
 * Check plugin versions against the latest versions hosted on WordPress.org.
 *
 * The WordPress version, PHP version, and Locale is sent along with a list of
 * all plugins installed. Checks against the WordPress server at
 * api.wordpress.org. Will only check if WordPress isn't installing.
 *
 * @since 2.3.0
 * @global string $wp_version Used to notify the WordPress version.
 *
 * @param array $extra_stats Extra statistics to report to the WordPress.org API.
 */
function wp_update_plugins( $extra_stats = array() ) {
	if ( wp_installing() ) {
		return;
	}

	// include an unmodified $wp_version
	include( ABSPATH . WPINC . '/version.php' );

	// If running blog-side, bail unless we've not checked in the last 12 hours
	if ( !function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	$plugins = get_plugins();
	$translations = wp_get_installed_translations( 'plugins' );

	$active  = get_option( 'active_plugins', array() );
	$current = get_site_transient( 'update_plugins' );
	if ( ! is_object($current) )
		$current = new stdClass;

	$new_option = new stdClass;
	$new_option->last_checked = time();

	// Check for update on a different schedule, depending on the page.
	switch ( current_filter() ) {
		case 'upgrader_process_complete' :
			$timeout = 0;
			break;
		case 'load-update-core.php' :
			$timeout = MINUTE_IN_SECONDS;
			break;
		case 'load-plugins.php' :
		case 'load-update.php' :
			$timeout = HOUR_IN_SECONDS;
			break;
		default :
			if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
				$timeout = 0;
			} else {
				$timeout = 12 * HOUR_IN_SECONDS;
			}
	}

	$time_not_changed = isset( $current->last_checked ) && $timeout > ( time() - $current->last_checked );

	if ( $time_not_changed && ! $extra_stats ) {
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
		if ( ! $plugin_changed ) {
			return;
		}
	}

	// Update last_checked for current to prevent multiple blocking requests if request hangs
	$current->last_checked = time();
	set_site_transient( 'update_plugins', $current );

	$to_send = compact( 'plugins', 'active' );

	/**
	 * Filter the locales requested for plugin translations.
	 *
	 * @since 3.7.0
	 *
	 * @param array $locales Plugin locale. Default is current locale of the site.
	 */
	$locales = apply_filters( 'plugins_update_check_locales', array( get_locale() ) );

	if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
		$timeout = 30;
	} else {
		// Three seconds, plus one extra second for every 10 plugins
		$timeout = 3 + (int) ( count( $plugins ) / 10 );
	}

	$options = array(
		'timeout' => $timeout,
		'body' => array(
			'plugins'      => wp_json_encode( $to_send ),
			'translations' => wp_json_encode( $translations ),
			'locale'       => wp_json_encode( $locales ),
			'all'          => wp_json_encode( true ),
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);

	if ( $extra_stats ) {
		$options['body']['update_stats'] = wp_json_encode( $extra_stats );
	}

	$url = $http_url = 'http://api.wordpress.org/plugins/update-check/1.1/';
	if ( $ssl = wp_http_supports( array( 'ssl' ) ) )
		$url = set_url_scheme( $url, 'https' );

	$raw_response = wp_remote_post( $url, $options );
	if ( $ssl && is_wp_error( $raw_response ) ) {
		trigger_error( __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.' ) . ' ' . __( '(WordPress could not establish a secure connection to WordPress.org. Please contact your server administrator.)' ), headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE );
		$raw_response = wp_remote_post( $http_url, $options );
	}

	if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ) {
		return;
	}

	$response = json_decode( wp_remote_retrieve_body( $raw_response ), true );
	foreach ( $response['plugins'] as &$plugin ) {
		$plugin = (object) $plugin;
		if ( isset( $plugin->compatibility ) ) {
			$plugin->compatibility = (object) $plugin->compatibility;
			foreach ( $plugin->compatibility as &$data ) {
				$data = (object) $data;
			}
		}
	}
	unset( $plugin, $data );
	foreach ( $response['no_update'] as &$plugin ) {
		$plugin = (object) $plugin;
	}
	unset( $plugin );

	if ( is_array( $response ) ) {
		$new_option->response = $response['plugins'];
		$new_option->translations = $response['translations'];
		// TODO: Perhaps better to store no_update in a separate transient with an expiry?
		$new_option->no_update = $response['no_update'];
	} else {
		$new_option->response = array();
		$new_option->translations = array();
		$new_option->no_update = array();
	}

	set_site_transient( 'update_plugins', $new_option );
}

/**
 * Check theme versions against the latest versions hosted on WordPress.org.
 *
 * A list of all themes installed in sent to WP. Checks against the
 * WordPress server at api.wordpress.org. Will only check if WordPress isn't
 * installing.
 *
 * @since 2.7.0
 *
 * @param array $extra_stats Extra statistics to report to the WordPress.org API.
 */
function wp_update_themes( $extra_stats = array() ) {
	if ( wp_installing() ) {
		return;
	}

	// include an unmodified $wp_version
	include( ABSPATH . WPINC . '/version.php' );

	$installed_themes = wp_get_themes();
	$translations = wp_get_installed_translations( 'themes' );

	$last_update = get_site_transient( 'update_themes' );
	if ( ! is_object($last_update) )
		$last_update = new stdClass;

	$themes = $checked = $request = array();

	// Put slug of current theme into request.
	$request['active'] = get_option( 'stylesheet' );

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
		case 'upgrader_process_complete' :
			$timeout = 0;
			break;
		case 'load-update-core.php' :
			$timeout = MINUTE_IN_SECONDS;
			break;
		case 'load-themes.php' :
		case 'load-update.php' :
			$timeout = HOUR_IN_SECONDS;
			break;
		default :
			if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
				$timeout = 0;
			} else {
				$timeout = 12 * HOUR_IN_SECONDS;
			}
	}

	$time_not_changed = isset( $last_update->last_checked ) && $timeout > ( time() - $last_update->last_checked );

	if ( $time_not_changed && ! $extra_stats ) {
		$theme_changed = false;
		foreach ( $checked as $slug => $v ) {
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
		if ( ! $theme_changed ) {
			return;
		}
	}

	// Update last_checked for current to prevent multiple blocking requests if request hangs
	$last_update->last_checked = time();
	set_site_transient( 'update_themes', $last_update );

	$request['themes'] = $themes;

	/**
	 * Filter the locales requested for theme translations.
	 *
	 * @since 3.7.0
	 *
	 * @param array $locales Theme locale. Default is current locale of the site.
	 */
	$locales = apply_filters( 'themes_update_check_locales', array( get_locale() ) );

	if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
		$timeout = 30;
	} else {
		// Three seconds, plus one extra second for every 10 themes
		$timeout = 3 + (int) ( count( $themes ) / 10 );
	}

	$options = array(
		'timeout' => $timeout,
		'body' => array(
			'themes'       => wp_json_encode( $request ),
			'translations' => wp_json_encode( $translations ),
			'locale'       => wp_json_encode( $locales ),
		),
		'user-agent'	=> 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);

	if ( $extra_stats ) {
		$options['body']['update_stats'] = wp_json_encode( $extra_stats );
	}

	$url = $http_url = 'http://api.wordpress.org/themes/update-check/1.1/';
	if ( $ssl = wp_http_supports( array( 'ssl' ) ) )
		$url = set_url_scheme( $url, 'https' );

	$raw_response = wp_remote_post( $url, $options );
	if ( $ssl && is_wp_error( $raw_response ) ) {
		trigger_error( __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.' ) . ' ' . __( '(WordPress could not establish a secure connection to WordPress.org. Please contact your server administrator.)' ), headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE );
		$raw_response = wp_remote_post( $http_url, $options );
	}

	if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ) {
		return;
	}

	$new_update = new stdClass;
	$new_update->last_checked = time();
	$new_update->checked = $checked;

	$response = json_decode( wp_remote_retrieve_body( $raw_response ), true );

	if ( is_array( $response ) ) {
		$new_update->response     = $response['themes'];
		$new_update->translations = $response['translations'];
	}

	set_site_transient( 'update_themes', $new_update );
}

/**
 * Performs WordPress automatic background updates.
 *
 * @since 3.7.0
 */
function wp_maybe_auto_update() {
	include_once( ABSPATH . '/wp-admin/includes/admin.php' );
	include_once( ABSPATH . '/wp-admin/includes/class-wp-upgrader.php' );

	$upgrader = new WP_Automatic_Updater;
	$upgrader->run();
}

/**
 * Retrieves a list of all language updates available.
 *
 * @since 3.7.0
 *
 * @return array
 */
function wp_get_translation_updates() {
	$updates = array();
	$transients = array( 'update_core' => 'core', 'update_plugins' => 'plugin', 'update_themes' => 'theme' );
	foreach ( $transients as $transient => $type ) {
		$transient = get_site_transient( $transient );
		if ( empty( $transient->translations ) )
			continue;

		foreach ( $transient->translations as $translation ) {
			$updates[] = (object) $translation;
		}
	}
	return $updates;
}

/**
 * Collect counts and UI strings for available updates
 *
 * @since 3.3.0
 *
 * @return array
 */
function wp_get_update_data() {
	$counts = array( 'plugins' => 0, 'themes' => 0, 'wordpress' => 0, 'translations' => 0 );

	if ( $plugins = current_user_can( 'update_plugins' ) ) {
		$update_plugins = get_site_transient( 'update_plugins' );
		if ( ! empty( $update_plugins->response ) )
			$counts['plugins'] = count( $update_plugins->response );
	}

	if ( $themes = current_user_can( 'update_themes' ) ) {
		$update_themes = get_site_transient( 'update_themes' );
		if ( ! empty( $update_themes->response ) )
			$counts['themes'] = count( $update_themes->response );
	}

	if ( ( $core = current_user_can( 'update_core' ) ) && function_exists( 'get_core_updates' ) ) {
		$update_wordpress = get_core_updates( array('dismissed' => false) );
		if ( ! empty( $update_wordpress ) && ! in_array( $update_wordpress[0]->response, array('development', 'latest') ) && current_user_can('update_core') )
			$counts['wordpress'] = 1;
	}

	if ( ( $core || $plugins || $themes ) && wp_get_translation_updates() )
		$counts['translations'] = 1;

	$counts['total'] = $counts['plugins'] + $counts['themes'] + $counts['wordpress'] + $counts['translations'];
	$titles = array();
	if ( $counts['wordpress'] )
		$titles['wordpress'] = sprintf( __( '%d WordPress Update'), $counts['wordpress'] );
	if ( $counts['plugins'] )
		$titles['plugins'] = sprintf( _n( '%d Plugin Update', '%d Plugin Updates', $counts['plugins'] ), $counts['plugins'] );
	if ( $counts['themes'] )
		$titles['themes'] = sprintf( _n( '%d Theme Update', '%d Theme Updates', $counts['themes'] ), $counts['themes'] );
	if ( $counts['translations'] )
		$titles['translations'] = __( 'Translation Updates' );

	$update_title = $titles ? esc_attr( implode( ', ', $titles ) ) : '';

	$update_data = array( 'counts' => $counts, 'title' => $update_title );
	/**
	 * Filter the returned array of update data for plugins, themes, and WordPress core.
	 *
	 * @since 3.5.0
	 *
	 * @param array $update_data {
	 *     Fetched update data.
	 *
	 *     @type array   $counts       An array of counts for available plugin, theme, and WordPress updates.
	 *     @type string  $update_title Titles of available updates.
	 * }
	 * @param array $titles An array of update counts and UI strings for available updates.
	 */
	return apply_filters( 'wp_get_update_data', $update_data, $titles );
}

/**
 * Determines whether core should be updated.
 *
 * @since 2.8.0
 *
 * @global string $wp_version
 */
function _maybe_update_core() {
	// include an unmodified $wp_version
	include( ABSPATH . WPINC . '/version.php' );

	$current = get_site_transient( 'update_core' );

	if ( isset( $current->last_checked, $current->version_checked ) &&
		12 * HOUR_IN_SECONDS > ( time() - $current->last_checked ) &&
		$current->version_checked == $wp_version ) {
		return;
	}
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
	if ( isset( $current->last_checked ) && 12 * HOUR_IN_SECONDS > ( time() - $current->last_checked ) )
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
function _maybe_update_themes() {
	$current = get_site_transient( 'update_themes' );
	if ( isset( $current->last_checked ) && 12 * HOUR_IN_SECONDS > ( time() - $current->last_checked ) )
		return;
	wp_update_themes();
}

/**
 * Schedule core, theme, and plugin update checks.
 *
 * @since 3.1.0
 */
function wp_schedule_update_checks() {
	if ( ! wp_next_scheduled( 'wp_version_check' ) && ! wp_installing() )
		wp_schedule_event(time(), 'twicedaily', 'wp_version_check');

	if ( ! wp_next_scheduled( 'wp_update_plugins' ) && ! wp_installing() )
		wp_schedule_event(time(), 'twicedaily', 'wp_update_plugins');

	if ( ! wp_next_scheduled( 'wp_update_themes' ) && ! wp_installing() )
		wp_schedule_event(time(), 'twicedaily', 'wp_update_themes');
}

/**
 * Clear existing update caches for plugins, themes, and core.
 *
 * @since 4.1.0
 */
function wp_clean_update_cache() {
	if ( function_exists( 'wp_clean_plugins_cache' ) ) {
		wp_clean_plugins_cache();
	} else {
		delete_site_transient( 'update_plugins' );
	}
	wp_clean_themes_cache();
	delete_site_transient( 'update_core' );
}

if ( ( ! is_main_site() && ! is_network_admin() ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
	return;
}

add_action( 'admin_init', '_maybe_update_core' );
add_action( 'wp_version_check', 'wp_version_check' );
add_action( 'upgrader_process_complete', 'wp_version_check', 10, 0 );

add_action( 'load-plugins.php', 'wp_update_plugins' );
add_action( 'load-update.php', 'wp_update_plugins' );
add_action( 'load-update-core.php', 'wp_update_plugins' );
add_action( 'admin_init', '_maybe_update_plugins' );
add_action( 'wp_update_plugins', 'wp_update_plugins' );
add_action( 'upgrader_process_complete', 'wp_update_plugins', 10, 0 );

add_action( 'load-themes.php', 'wp_update_themes' );
add_action( 'load-update.php', 'wp_update_themes' );
add_action( 'load-update-core.php', 'wp_update_themes' );
add_action( 'admin_init', '_maybe_update_themes' );
add_action( 'wp_update_themes', 'wp_update_themes' );
add_action( 'upgrader_process_complete', 'wp_update_themes', 10, 0 );

add_action( 'update_option_WPLANG', 'wp_clean_update_cache' , 10, 0 );

add_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );

add_action( 'init', 'wp_schedule_update_checks' );
