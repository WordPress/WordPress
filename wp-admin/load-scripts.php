<?php

/*
 * Disable error reporting.
 *
 * Set this to error_reporting( -1 ) for debugging.
 */
error_reporting( 0 );

// Set ABSPATH for execution.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

define( 'WPINC', 'wp-includes' );

$protocol = $_SERVER['SERVER_PROTOCOL'];
if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0', 'HTTP/3' ), true ) ) {
	$protocol = 'HTTP/1.0';
}

$load = $_GET['load'];
if ( is_array( $load ) ) {
	ksort( $load );
	$load = implode( '', $load );
}

$load = preg_replace( '/[^a-z0-9,_-]+/i', '', $load );
$load = array_unique( explode( ',', $load ) );

if ( empty( $load ) ) {
	header( "$protocol 400 Bad Request" );
	exit;
}

require ABSPATH . 'wp-admin/includes/noop.php';
require ABSPATH . WPINC . '/script-loader.php';
require ABSPATH . WPINC . '/version.php';

$expires_offset = 31536000; // 1 year.
$out            = '';

$wp_scripts = new WP_Scripts();
wp_default_scripts( $wp_scripts );
wp_default_packages_vendor( $wp_scripts );
wp_default_packages_scripts( $wp_scripts );

$etag = "WP:{$wp_version};";

foreach ( $load as $handle ) {
	if ( ! array_key_exists( $handle, $wp_scripts->registered ) ) {
		continue;
	}

	$ver   = $wp_scripts->registered[ $handle ]->ver ? $wp_scripts->registered[ $handle ]->ver : $wp_version;
	$etag .= "{$handle}:{$ver};";
}

/*
 * This is not intended to be cryptographically secure, just a fast way to get
 * a fixed length string based on the script versions. As this file does not
 * load the full WordPress environment, it is not possible to use the salted
 * wp_hash() function.
 */
$etag = 'W/"' . md5( $etag ) . '"';

if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) === $etag ) {
	header( "$protocol 304 Not Modified" );
	exit;
}

foreach ( $load as $handle ) {
	if ( ! array_key_exists( $handle, $wp_scripts->registered ) ) {
		continue;
	}

	$path = ABSPATH . $wp_scripts->registered[ $handle ]->src;
	$out .= get_file( $path ) . "\n";
}

header( "Etag: $etag" );
header( 'Content-Type: application/javascript; charset=UTF-8' );
header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $expires_offset ) . ' GMT' );
header( "Cache-Control: public, max-age=$expires_offset" );

echo $out;
exit;
