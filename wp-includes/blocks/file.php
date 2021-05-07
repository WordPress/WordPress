<?php
/**
 * Server-side rendering of the `core/file` block.
 *
 * @package WordPress
 */

/**
 * When the `core/file` block is rendering, check if we need to enqueue the `'wp-block-library-file` script.
 *
 * @param array $attributes The block attributes.
 * @param array $content    The block content.
 *
 * @return string Returns the block content.
 */
function render_block_core_file( $attributes, $content ) {
	if ( ! empty( $attributes['displayPreview'] ) ) {
		// Check if it's already enqueued, so we don't add the inline script multiple times.
		if ( ! wp_script_is( 'wp-block-library-file' ) ) {
			wp_enqueue_script( 'wp-block-library-file', plugins_url( 'file/frontend.js', __FILE__ ) );
		}
	}

	return $content;
}

/**
 * Registers the `core/file` block on server.
 */
function register_block_core_file() {
	register_block_type_from_metadata(
		__DIR__ . '/file',
		array(
			'render_callback' => 'render_block_core_file',
		)
	);
}
add_action( 'init', 'register_block_core_file' );
