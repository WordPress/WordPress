<?php
/**
 * Loads the WordPress environment and template.
 *
 * @package WordPress
 */

if ( !isset($wp_did_header) ) {

	$wp_did_header = true;

	require_once( dirname(__FILE__) . '/wp-load.php' );

	wp();
//echo '<pre>';  print_r($wp_query); echo '</pre>'; exit;

	require_once( ABSPATH . WPINC . '/template-loader.php' );

}

?>