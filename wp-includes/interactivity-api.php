<?php
/**
 * Interactivity API: Functions and hooks
 *
 * @package WordPress
 * @subpackage Interactivity API
 */

/**
 * Registers the interactivity modules.
 */
function wp_interactivity_register_script_modules() {
	wp_register_script_module(
		'@wordpress/interactivity',
		includes_url( '/js/dist/interactivity.min.js' ),
		array()
	);

	wp_register_script_module(
		'@wordpress/interactivity-router',
		includes_url( '/js/dist/interactivity-router.min.js' ),
		array( '@wordpress/interactivity' )
	);
}

add_action( 'init', 'wp_interactivity_register_script_modules' );
