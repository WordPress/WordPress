<?php
/**
 * A simple set of functions to check our version 1.0 update service
 *
 * @package WordPress
 * @since 2.3
 */

/**
 * wp_version_check() - Check WordPress version against the newest version.
 *
 * The WordPress version, PHP version, and Locale is sent. Checks against the WordPress server at
 * api.wordpress.org. Will only check if PHP has fsockopen enabled and WordPress isn't installing.
 *
 * @package WordPress
 * @since 2.3
 * @uses $wp_version Used to check against the newest WordPress version.
 *
 * @return mixed Returns null if update is unsupported. Returns false if check is too soon.
 */
function wp_version_check() {
	if ( !function_exists('fsockopen') || defined('WP_INSTALLING') )
		return;

	global $wp_version;
	$php_version = phpversion();

	$current = get_option( 'update_core' );
	$locale = get_locale();

	if (
		isset( $current->last_checked ) &&
		43200 > ( time() - $current->last_checked ) &&
		$current->version_checked == $wp_version
	)
		return false;

	$new_option = '';
	$new_option->last_checked = time(); // this gets set whether we get a response or not, so if something is down or misconfigured it won't delay the page load for more than 3 seconds, twice a day
	$new_option->version_checked = $wp_version;

	$http_request  = "GET /core/version-check/1.1/?version=$wp_version&php=$php_version&locale=$locale HTTP/1.0\r\n";
	$http_request .= "Host: api.wordpress.org\r\n";
	$http_request .= 'Content-Type: application/x-www-form-urlencoded; charset=' . get_option('blog_charset') . "\r\n";
	$http_request .= 'User-Agent: WordPress/' . $wp_version . '; ' . get_bloginfo('url') . "\r\n";
	$http_request .= "\r\n";

	$response = '';
	if ( false !== ( $fs = @fsockopen( 'api.wordpress.org', 80, $errno, $errstr, 3 ) ) && is_resource($fs) ) {
		fwrite( $fs, $http_request );
		while ( !feof( $fs ) )
			$response .= fgets( $fs, 1160 ); // One TCP-IP packet
		fclose( $fs );

		$response = explode("\r\n\r\n", $response, 2);
		if ( !preg_match( '|HTTP/.*? 200|', $response[0] ) )
			return false;

		$body = trim( $response[1] );
		$body = str_replace(array("\r\n", "\r"), "\n", $body);

		$returns = explode("\n", $body);

		$new_option->response = attribute_escape( $returns[0] );
		if ( isset( $returns[1] ) )
			$new_option->url = clean_url( $returns[1] );
		if ( isset( $returns[2] ) )
			$new_option->current = attribute_escape( $returns[2] );
	}
	update_option( 'update_core', $new_option );
}
add_action( 'init', 'wp_version_check' );

/**
 * wp_update_plugins() - Check plugin versions against the latest versions hosted on WordPress.org.
 *
 * The WordPress version, PHP version, and Locale is sent along with a list of all plugins installed.
 * Checks against the WordPress server at api.wordpress.org.
 * Will only check if PHP has fsockopen enabled and WordPress isn't installing.
 *
 * @package WordPress
 * @since 2.3
 * @uses $wp_version Used to notidy the WordPress version.
 *
 * @return mixed Returns null if update is unsupported. Returns false if check is too soon.
 */
function wp_update_plugins() {
	global $wp_version;

	if ( !function_exists('fsockopen') || defined('WP_INSTALLING') )
		return false;

	$current = get_option( 'update_plugins' );

	$time_not_changed = isset( $current->last_checked ) && 43200 > ( time() - $current->last_checked );

	// If running blog-side, bail unless we've not checked in the last 12 hours
	if ( !function_exists( 'get_plugins' ) ) {
		if ( $time_not_changed )
			return false;
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	$plugins = get_plugins();
	$active  = get_option( 'active_plugins' );

	$new_option = '';
	$new_option->last_checked = time();

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

	// Bail if we've checked in the last 12 hours and if nothing has changed
	if ( $time_not_changed && !$plugin_changed )
		return false;

	$to_send->plugins = $plugins;
	$to_send->active = $active;
	$send = serialize( $to_send );

	$request = 'plugins=' . urlencode( $send );
	$http_request  = "POST /plugins/update-check/1.0/ HTTP/1.0\r\n";
	$http_request .= "Host: api.wordpress.org\r\n";
	$http_request .= "Content-Type: application/x-www-form-urlencoded; charset=" . get_option('blog_charset') . "\r\n";
	$http_request .= "Content-Length: " . strlen($request) . "\r\n";
	$http_request .= 'User-Agent: WordPress/' . $wp_version . '; ' . get_bloginfo('url') . "\r\n";
	$http_request .= "\r\n";
	$http_request .= $request;

	$response = '';
	if( false != ( $fs = @fsockopen( 'api.wordpress.org', 80, $errno, $errstr, 3) ) && is_resource($fs) ) {
		fwrite($fs, $http_request);

		while ( !feof($fs) )
			$response .= fgets($fs, 1160); // One TCP-IP packet
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);
	}

	$response = unserialize( $response[1] );

	if ( $response )
		$new_option->response = $response;

	update_option( 'update_plugins', $new_option );
}
if ( defined( 'WP_ADMIN' ) && WP_ADMIN )
	add_action( 'admin_init', 'wp_update_plugins' );
else
	add_action( 'init', 'wp_update_plugins' );

?>