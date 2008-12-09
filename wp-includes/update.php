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

	global $wp_version, $wpdb, $wp_local_package;
	$php_version = phpversion();

	$current = get_option( 'update_core' );
	if ( ! is_object($current) )
		$current = new stdClass;

	$locale = get_locale();
	if (
		isset( $current->last_checked ) &&
		43200 > ( time() - $current->last_checked ) &&
		$current->version_checked == $wp_version
	)
		return false;

	// Update last_checked for current to prevent multiple blocking requests if request hangs
	$current->last_checked = time();
	update_option( 'update_core', $current );

	if ( method_exists( $wpdb, 'db_version' ) )
		$mysql_version = preg_replace('/[^0-9.].*/', '', $wpdb->db_version($wpdb->users));
	else
		$mysql_version = 'N/A';
	$local_package = isset( $wp_local_package )? $wp_local_package : '';
	$url = "http://api.wordpress.org/core/version-check/1.3/?version=$wp_version&php=$php_version&locale=$locale&mysql=$mysql_version&local_package=$local_package";

	$options = array('timeout' => 3);
	$options['headers'] = array(
		'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
		'User-Agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
	);

	$response = wp_remote_request($url, $options);

	if ( is_wp_error( $response ) )
		return false;

	if ( 200 != $response['response']['code'] )
		return false;

	$body = trim( $response['body'] );
	$body = str_replace(array("\r\n", "\r"), "\n", $body);
	$new_options = array();
	foreach( explode( "\n\n", $body ) as $entry) {
		$returns = explode("\n", $entry);
		$new_option = new stdClass();
		$new_option->response = attribute_escape( $returns[0] );
		if ( isset( $returns[1] ) )
			$new_option->url = clean_url( $returns[1] );
		if ( isset( $returns[2] ) )
			$new_option->package = clean_url( $returns[2] );
		if ( isset( $returns[3] ) )
			$new_option->current = attribute_escape( $returns[3] );
		if ( isset( $returns[4] ) )
			$new_option->locale = attribute_escape( $returns[4] );
		$new_options[] = $new_option;
	}

	$updates = new stdClass();
	$updates->updates = $new_options;
	$updates->last_checked = time();
	$updates->version_checked = $wp_version;
	update_option( 'update_core',  $updates);
}
add_action( 'init', 'wp_version_check' );

/**
 * Check plugin versions against the latest versions hosted on WordPress.org.
 *
 * The WordPress version, PHP version, and Locale is sent along with a list of
 * all plugins installed. Checks against the WordPress server at
 * api.wordpress.org. Will only check if WordPress isn't installing.
 *
 * @package WordPress
 * @since 2.3.0
 * @uses $wp_version Used to notidy the WordPress version.
 *
 * @return mixed Returns null if update is unsupported. Returns false if check is too soon.
 */
function wp_update_plugins() {
	global $wp_version;

	if ( defined('WP_INSTALLING') )
		return false;

	// If running blog-side, bail unless we've not checked in the last 12 hours
	if ( !function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	$plugins = get_plugins();
	$active  = get_option( 'active_plugins' );
	$current = get_option( 'update_plugins' );
	if ( ! is_object($current) )
		$current = new stdClass;

	$new_option = '';
	$new_option->last_checked = time();
	$time_not_changed = isset( $current->last_checked ) && 43200 > ( time() - $current->last_checked );

	$plugin_changed = false;
	foreach ( $plugins as $file => $p ) {
		$new_option->checked[ $file ] = $p['Version'];

		if ( !isset( $current->checked[ $file ] ) ) {
			$plugin_changed = true;
			continue;
		}

		if ( strval($current->checked[ $file ]) !== strval($p['Version']) )
			$plugin_changed = true;
	}

	if ( isset ( $current->response ) && is_array( $current->response ) ) {
		foreach ( $current->response as $plugin_file => $update_details ) {
			if ( ! isset($plugins[ $plugin_file ]) ) {
				$plugin_changed = true;
			}
		}
	}

	// Bail if we've checked in the last 12 hours and if nothing has changed
	if ( $time_not_changed && !$plugin_changed )
		return false;

	// Update last_checked for current to prevent multiple blocking requests if request hangs
	$current->last_checked = time();
	update_option( 'update_plugins', $current );

	$to_send->plugins = $plugins;
	$to_send->active = $active;
	$send = serialize( $to_send );
	$body = 'plugins=' . urlencode( $send );

	$options = array('method' => 'POST', 'timeout' => 3, 'body' => $body);
	$options['headers'] = array(
		'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
		'Content-Length' => strlen($body),
		'User-Agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
	);

	$raw_response = wp_remote_request('http://api.wordpress.org/plugins/update-check/1.0/', $options);

	if ( is_wp_error( $raw_response ) )
		return false;

	if( 200 != $raw_response['response']['code'] ) {
		return false;
	}

	$response = unserialize( $raw_response['body'] );

	if ( false !== $response )
		$new_option->response = $response;
	else
		$new_option->response = array();

	update_option( 'update_plugins', $new_option );
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
 * @uses $wp_version Used to notidy the WordPress version.
 *
 * @return mixed Returns null if update is unsupported. Returns false if check is too soon.
 */
function wp_update_themes( ) {
	global $wp_version;

	if( defined( 'WP_INSTALLING' ) )
		return false;

	if( !function_exists( 'get_themes' ) )
		require_once( ABSPATH . 'wp-includes/theme.php' );

	$installed_themes = get_themes( );
	$current_theme = get_option( 'update_themes' );
	if ( ! is_object($current_theme) )
		$current_theme = new stdClass;

	$new_option = '';
	$new_option->last_checked = time( );
	$time_not_changed = isset( $current_theme->last_checked ) && 43200 > ( time( ) - $current_theme->last_checked );

	if( $time_not_changed )
		return false;

	// Update last_checked for current to prevent multiple blocking requests if request hangs
	$current_theme->last_checked = time();
	update_option( 'update_themes', $current_theme );

	$themes = array( );
	$themes['current_theme'] = $current_theme;
	foreach( (array) $installed_themes as $theme_title => $theme ) {
		$themes[$theme['Template']] = array( );

		foreach( (array) $theme as $key => $value ) {
			$themes[$theme['Template']][$key] = $value;
		}
	}

	$options = array(
		'method'		=> 'POST',
		'timeout'		=> 3,
		'body'			=> 'themes=' . urlencode( serialize( $themes ) )
	);
	$options['headers'] = array(
		'Content-Type'		=> 'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' ),
		'Content-Length'	=> strlen( $options['body'] ),
		'User-Agent'		=> 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);

	$raw_response = wp_remote_request( 'http://api.wordpress.org/themes/update-check/1.0/', $options );

	if( is_wp_error( $raw_response ) )
		return false;

	if( 200 != $raw_response['response']['code'] )
		return false;

	$response = unserialize( $raw_response['body'] );
	if( $response )
		$new_option->response = $response;

	update_option( 'update_themes', $new_option );
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
	$current = get_option( 'update_plugins' );
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
	$current = get_option( 'update_themes' );
	if( isset( $current->last_checked ) && 43200 > ( time( ) - $current->last_checked ) )
		return;

	wp_update_themes( );
}

add_action( 'load-plugins.php', 'wp_update_plugins' );
add_action( 'load-update.php', 'wp_update_plugins' );
add_action( 'admin_init', '_maybe_update_plugins' );
add_action( 'wp_update_plugins', 'wp_update_plugins' );

add_action( 'admin_init', '_maybe_update_themes' );
add_action( 'wp_update_themes', 'wp_update_themes' );

if ( !wp_next_scheduled('wp_update_plugins') && !defined('WP_INSTALLING') )
	wp_schedule_event(time(), 'twicedaily', 'wp_update_plugins');


if ( !wp_next_scheduled('wp_update_themes') && !defined('WP_INSTALLING') )
	wp_schedule_event(time(), 'twicedaily', 'wp_update_themes');

?>
