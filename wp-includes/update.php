<?php

// A simple set of functions to check our version 1.0 update service

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

	$http_request  = "GET /core/version-check/1.0/?version=$wp_version&php=$php_version&locale=$locale HTTP/1.0\r\n";
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
		$body = trim( $response[1] );
		$body = str_replace(array("\r\n", "\r"), "\n", $body);

		$returns = explode("\n", $body);

		$new_option->response = $returns[0];
		if ( isset( $returns[1] ) )
			$new_option->url = $returns[1];
	}
	update_option( 'update_core', $new_option );
}

add_action( 'init', 'wp_version_check' );

?>