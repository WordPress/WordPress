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
 * api.wordpress.org server. Will only check if PHP has fsockopen enabled and WordPress isn't installing.
 *
 * @package WordPress
 * @since 2.3
 * @uses $wp_version Used to check against the newest WordPress version.
 *
 * @return mixed Returns null if update is unsupported. Returns false if check is too soon.
 */
function wp_version_check() {
	if ( !function_exists('fsockopen') || strpos($_SERVER['PHP_SELF'], 'install.php') !== false || defined('WP_INSTALLING') )
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

?>