<?php
/**
 * Server-side rendering of the `core/form-input` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/form-input` block on server.
 *
 * @param array  $attributes The block attributes.
 * @param string $content The saved content.
 *
 * @return string The content of the block being rendered.
 */
function gutenberg_render_block_core_form_input( $attributes, $content ) {
	$visibility_permissions = 'all';
	if ( isset( $attributes['visibilityPermissions'] ) ) {
		$visibility_permissions = $attributes['visibilityPermissions'];
	}

	$user_logged_in = is_user_logged_in();

	if ( 'logged-in' === $visibility_permissions && ! $user_logged_in ) {
		return '';
	}
	if ( 'logged-out' === $visibility_permissions && $user_logged_in ) {
		return '';
	}
	return $content;
}

/**
 * Registers the `core/form-input` block on server.
 */
function gutenberg_register_block_core_form_input() {
	if ( ! gutenberg_is_experiment_enabled( 'gutenberg-form-blocks' ) ) {
		return;
	}
	register_block_type_from_metadata(
		__DIR__ . '/form-input',
		array(
			'render_callback' => 'gutenberg_render_block_core_form_input',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_form_input', 20 );
