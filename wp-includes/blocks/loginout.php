<?php
/**
 * Server-side rendering of the `core/loginout` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/loginout` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the login-out link or form.
 */
function render_block_core_loginout( $attributes ) {

	// Build the redirect URL.
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	$classes  = is_user_logged_in() ? 'logged-in' : 'logged-out';
	$contents = wp_loginout(
		isset( $attributes['redirectToCurrent'] ) && $attributes['redirectToCurrent'] ? $current_url : '',
		false
	);

	// If logged-out and displayLoginAsForm is true, show the login form.
	if ( ! is_user_logged_in() && ! empty( $attributes['displayLoginAsForm'] ) ) {
		// Add a class.
		$classes .= ' has-login-form';

		// Get the form.
		$contents = wp_login_form( array( 'echo' => false ) );
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classes ) );

	return '<div ' . $wrapper_attributes . '>' . $contents . '</div>';
}

/**
 * Registers the `core/latest-posts` block on server.
 */
function register_block_core_loginout() {
	register_block_type_from_metadata(
		__DIR__ . '/loginout',
		array(
			'render_callback' => 'render_block_core_loginout',
		)
	);
}
add_action( 'init', 'register_block_core_loginout' );
