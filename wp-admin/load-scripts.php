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
error_reporting( 0 );

define( 'SHORTINIT', true );

require( dirname(dirname( __FILE__ )) . '/wp-load.php' );
require( ABSPATH . WPINC . '/pluggable.php' );
require( ABSPATH . WPINC . '/formatting.php' );
require( ABSPATH . WPINC . '/link-template.php' );
require( ABSPATH . WPINC . '/kses.php' );
require( ABSPATH . WPINC . '/version.php' );

$load = $_GET['load'];
if ( is_array( $load ) ) {
	$load = implode( '', $load );
}

$load = preg_replace( '/[^a-z0-9,_-]+/i', '', $load );

// Reduce cache surface by making unique and sorting

$targets = array_unique( explode( ',', $load ) );

sort( $targets );

if ( !count($targets) ) {
	// Allow client to get a cached empty response
	wp_redirect( admin_url( 'load-scripts-keyed.php?load[]=' ), 301 );
	exit;
}

$load = implode(',', $targets);

// TODO: actions, filters, cache surface reduction

$c = (int)$_GET['c'] ? 1 : 0;

/*
 * The query parameter 'ver' is not passed to load-scripts-keyed.php,
 * as the current version affects integrity, the URL already cache-
 * busts on a new WordPress version.
 */
$load_request = 'load%5B%5D=' . $load . '&c=' . $c;

$integrity = 'sha256-' . hash( 'sha256', $wp_version . $load_request . NONCE_KEY );

$url =  'load-scripts-keyed.php?key=' . $integrity . '&' . $load_request;

wp_redirect( admin_url( $url ), 301 );
