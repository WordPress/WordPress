<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * TablePress Support
 *
 * @link https://tablepress.org/
 */

add_action( 'wp_enqueue_scripts', 'us_dequeue_tablepress_default', 15 );

function us_dequeue_tablepress_default() {
	wp_dequeue_style( 'tablepress-default' );
}
