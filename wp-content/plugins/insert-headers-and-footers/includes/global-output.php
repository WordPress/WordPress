<?php
/**
 * Add hooks to output global scripts.
 *
 * @package WPCode
 */

add_action( 'wp_head', 'wpcode_global_frontend_header' );
add_action( 'wp_footer', 'wpcode_global_frontend_footer' );
add_action( 'wp_body_open', 'wpcode_global_frontend_body', 1 );

/**
 * Output the frontend head scripts.
 *
 * @return void
 */
function wpcode_global_frontend_header() {
	// Filter to prevent specific header output.
	if ( apply_filters( 'disable_ihaf_header', false ) ) {
		return;
	}
	wpcode_global_script_output( 'ihaf_insert_header' );
}

/**
 * Output the frontend footer scripts.
 *
 * @return void
 */
function wpcode_global_frontend_footer() {
	// Filter to prevent specific footer output.
	if ( apply_filters( 'disable_ihaf_footer', false ) ) {
		return;
	}
	wpcode_global_script_output( 'ihaf_insert_footer' );
}

/**
 * Output the frontend body scripts.
 *
 * @return void
 */
function wpcode_global_frontend_body() {
	// Filter to prevent specific body output.
	if ( apply_filters( 'disable_ihaf_body', false ) ) {
		return;
	}
	wpcode_global_script_output( 'ihaf_insert_body' );
}

/**
 * Output everything through this function to get a chance to apply some checks.
 *
 * @param string $option_name The option name to grab data from.
 *
 * @return void
 */
function wpcode_global_script_output( $option_name ) {
	// Ignore admin, feed, robots or trackbacks.
	if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
		return;
	}
	// Filter to prevent any output.
	if ( apply_filters( 'disable_ihaf', false ) ) {
		return;
	}

	$code = get_option( $option_name );
	if ( empty( $code ) || empty( trim( $code ) ) ) {
		return;
	}

	echo wp_unslash( $code );
}
