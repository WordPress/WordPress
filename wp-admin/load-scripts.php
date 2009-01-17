<?php

/** Set ABSPATH for execution */
define( 'ABSPATH', dirname(dirname(__FILE__)) );
define( 'WPINC', '/wp-includes' );

/**
 * @ignore
 */
function __() {}

/**
 * @ignore
 */
function _c() {}

/**
 * @ignore
 */
function add_filter() {}

/**
 * @ignore
 */
function attribute_escape() {}

/**
 * @ignore
 */
function apply_filters() {}

/**
 * @ignore
 */
function get_option() {}

/**
 * @ignore
 */
function is_lighttpd_before_150() {}

/**
 * @ignore
 */
function add_action() {}

/**
 * @ignore
 */
function do_action_ref_array() {}

/**
 * @ignore
 */
function get_bloginfo() {}

/**
 * @ignore
 */
function is_admin() {return true;}

/**
 * @ignore
 */
function site_url() {}

/**
 * @ignore
 */
function admin_url() {}

/**
 * @ignore
 */
function wp_guess_url() {}

function get_file($path) {

	if ( function_exists('realpath') )
		$path = realpath($path);

	if ( ! $path || ! @is_file($path) )
		return '';

	return @file_get_contents($path);
}

// Discard any buffers
while ( @ob_end_clean() );

if ( isset($_GET['test']) && 1 == $_GET['test'] ) {
	if ( ini_get('zlib.output_compression') )
		exit('');

	if ( false !== strpos( strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'gzip') && function_exists('gzencode') ) {
		header('Content-Encoding: gzip');
		$out = gzencode( 'var wpCompressionTest = 1;', 3 );
	}
	
	if ( ! isset($out) )
		exit('');
	
	header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
	header( 'Pragma: no-cache' );
	header( 'Content-Type: application/x-javascript; charset=UTF-8' );
	echo $out;
	exit;
}

$load = preg_replace( '/[^a-z0-9,_-]*/i', '', $_GET['load'] );
$load = explode(',', $load);

if ( empty($load) )
	exit;

require(ABSPATH . '/wp-includes/script-loader.php');
require(ABSPATH . '/wp-includes/version.php');

$compress = ( isset($_GET['c']) && 1 == $_GET['c'] );
$expires_offset = 31536000;
$out = '';

$wp_scripts = new WP_Scripts();
wp_default_scripts($wp_scripts);

foreach( $load as $handle ) {
	if ( !array_key_exists($handle, $wp_scripts->registered) )
		continue;

	$path = ABSPATH . $wp_scripts->registered[$handle]->src;
	$out .= get_file($path) . "\n";
}

header('Content-Type: application/x-javascript; charset=UTF-8');
header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
header("Cache-Control: public, max-age=$expires_offset");
	
if ( $compress && ! ini_get('zlib.output_compression') && function_exists('gzencode') ) {
	header('Vary: Accept-Encoding'); // Handle proxies
	if ( false !== strpos( strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'gzip') ) {
		header('Content-Encoding: gzip');
		$out = gzencode( $out, 3 );
	}
}

echo $out;
exit;
