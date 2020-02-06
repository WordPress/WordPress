<?php
/**
 * Server-side rendering of the `core/shortcode` block.
 *
 * @package WordPress
 */

/**
 * Performs wpautop() on the shortcode block content.
 *
 * @param array  $attributes The block attributes.
 * @param string $content    The block content.
 *
 * @return string Returns the block content.
 */
function render_block_core_shortcode( $attributes, $content ) {
	return wpautop( $content );
}

/**
 * Registers the `core/shortcode` block on server.
 */
function register_block_core_shortcode() {
	$path     = __DIR__ . '/shortcode/block.json';
	$metadata = json_decode( file_get_contents( $path ), true );
	register_block_type(
		$metadata['name'],
		array_merge(
			$metadata,
			array(
				'render_callback' => 'render_block_core_shortcode',
			)
		)
	);
}
add_action( 'init', 'register_block_core_shortcode' );
