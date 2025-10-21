<?php
/**
 * Server-side rendering of the `core/loginout` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/loginout` block on server.
 *
 * @since 5.8.0
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the login-out link or form.
 */
function render_block_core_loginout( $attributes ) {

	/*
	 * Build the redirect URL. This current url fetching logic matches with the core.
	 *
	 * @see https://github.com/WordPress/wordpress-develop/blob/6bf62e58d21739938f3bb3f9e16ba702baf9c2cc/src/wp-includes/general-template.php#L528.
	 */
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	$user_logged_in = is_user_logged_in();

	$classes  = $user_logged_in ? 'logged-in' : 'logged-out';
	$contents = wp_loginout(
		isset( $attributes['redirectToCurrent'] ) && $attributes['redirectToCurrent'] ? $current_url : '',
		false
	);

	// If logged-out and displayLoginAsForm is true, show the login form.
	if ( ! $user_logged_in && ! empty( $attributes['displayLoginAsForm'] ) ) {
		// Add a class.
		$classes .= ' has-login-form';

		// Get the form.
		$contents = wp_login_form( array( 'echo' => false ) );
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classes ) );

	return '<div ' . $wrapper_attributes . '>' . $contents . '</div>';
}

/**
 * Registers the `core/loginout` block on server.
 *
 * @since 5.8.0
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
