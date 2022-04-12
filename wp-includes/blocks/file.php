<?php
/**
 * Server-side rendering of the `core/file` block.
 *
 * @package WordPress
 */

/**
 * When the `core/file` block is rendering, check if we need to enqueue the `'wp-block-file-view` script.
 *
 * @param array  $attributes The block attributes.
 * @param string $content    The block content.
 *
 * @return string Returns the block content.
 */
function render_block_core_file( $attributes, $content ) {
	$should_load_view_script = ! empty( $attributes['displayPreview'] ) && ! wp_script_is( 'wp-block-file-view' );
	if ( $should_load_view_script ) {
		wp_enqueue_script( 'wp-block-file-view' );
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
