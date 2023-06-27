<?php
/**
 * Server-side rendering of the `core/image` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/image` block on the server,
 * adding a data-id attribute to the element if core/gallery has added on pre-render.
 *
 * @param  array  $attributes The block attributes.
 * @param  string $content    The block content.
 * @return string Returns the block content with the data-id attribute added.
 */
function render_block_core_image( $attributes, $content ) {

	$processor = new WP_HTML_Tag_Processor( $content );
	$processor->next_tag( 'img' );

	if ( $processor->get_attribute( 'src' ) === null ) {
		return '';
	}

	if ( isset( $attributes['data-id'] ) ) {
		// Add the data-id="$id" attribute to the img element
		// to provide backwards compatibility for the Gallery Block,
		// which now wraps Image Blocks within innerBlocks.
		// The data-id attribute is added in a core/gallery `render_block_data` hook.
		$processor->set_attribute( 'data-id', $attributes['data-id'] );
	}

	return $processor->get_updated_html();
}

/**
 * Registers the `core/image` block on server.
 */
function register_block_core_image() {

	register_block_type_from_metadata(
		__DIR__ . '/image',
		array(
			'render_callback' => 'render_block_core_image',
		)
	);
}
add_action( 'init', 'register_block_core_image' );
