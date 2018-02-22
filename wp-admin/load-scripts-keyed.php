<?php
/**
 * @package WordPress
 * @subpackage Administration
 * @since 4.9.4
 */
/**
 * Disable error reporting
 *
 * Set this to error_reporting( -1 ) for debugging.
 */
error_reporting(0);

define( 'SHORTINIT', true );
require( dirname(dirname( __FILE__ )) . '/wp-load.php' );
require( ABSPATH . WPINC . '/version.php' );

$integrity_key = $_GET['key'];

$query_string = $_SERVER['QUERY_STRING'];

/* Length of 'key=' plus '&' is 5 */
$load_request = substr( $query_string, strlen( $integrity_key ) + 5 );

$integrity = 'sha256-' . hash( 'sha256', $wp_version . $load_request . NONCE_KEY );

if ( $integrity !== $integrity_key ) {
	header("Etag: $wp_version");
	header('Content-Type: application/javascript; charset=UTF-8');
	header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
	header("Cache-Control: public, max-age=$expires_offset");
	exit;
}

/** Set ABSPATH for execution */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( dirname( __FILE__ ) ) . '/' );
}

$load = $_GET['load'];

if ( is_array( $load ) )
	$load = implode( '', $load );

$load = explode( ',', $load );

if ( empty($load) ) {
	header("Etag: $wp_version");
	header('Content-Type: application/javascript; charset=UTF-8');
	header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
	header("Cache-Control: public, max-age=$expires_offset");
	exit;
}

require( ABSPATH . 'wp-admin/includes/noop.php' );
require( ABSPATH . WPINC . '/script-loader.php' );
require( ABSPATH . WPINC . '/version.php' );

$compress = ( isset($_GET['c']) && $_GET['c'] );
$force_gzip = ( $compress && 'gzip' == $_GET['c'] );
$expires_offset = 31536000; // 1 year
$out = '';

$wp_scripts = new WP_Scripts();
wp_default_scripts($wp_scripts);

if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) === $wp_version ) {
	$protocol = $_SERVER['SERVER_PROTOCOL'];
	if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0' ) ) ) {
		$protocol = 'HTTP/1.0';
	}
	header( "$protocol 304 Not Modified" );
	exit;
}

foreach ( $load as $handle ) {
	if ( !array_key_exists($handle, $wp_scripts->registered) )
		continue;

	$path = ABSPATH . $wp_scripts->registered[$handle]->src;
	$out .= get_file($path) . "\n";
}

header("Etag: $wp_version");
header('Content-Type: application/javascript; charset=UTF-8');
header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
header("Cache-Control: public, max-age=$expires_offset");

if ( $compress && ! ini_get('zlib.output_compression') && 'ob_gzhandler' != ini_get('output_handler') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) ) {
	header('Vary: Accept-Encoding'); // Handle proxies
	if ( false !== stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') && function_exists('gzdeflate') && ! $force_gzip ) {
		header('Content-Encoding: deflate');
		$out = gzdeflate( $out, 3 );
	} elseif ( false !== stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzencode') ) {
		header('Content-Encoding: gzip');
		$out = gzencode( $out, 3 );
	}
}

echo $out;
exit;
