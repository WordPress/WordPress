<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Gravity Forms Support
 *
 * @link http://www.gravityforms.com/
 */

if ( ! class_exists( 'GFForms' ) ) {
	return;
}

add_action( 'wp_enqueue_scripts', 'us_gravityforms_enqueue_styles' );
function us_gravityforms_enqueue_styles( $styles ) {
	global $us_template_directory_uri;
	wp_enqueue_style( 'us-gravityforms', $us_template_directory_uri . '/css/us.gravityforms.css', array(), US_THEMEVERSION, 'all' );
}
