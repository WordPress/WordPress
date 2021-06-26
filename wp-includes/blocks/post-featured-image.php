<?php
/**
 * Server-side rendering of the `core/post-featured-image` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-featured-image` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the featured image for the current post.
 */
function render_block_core_post_featured_image( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}
	$post_ID = $block->context['postId'];

	$featured_image = get_the_post_thumbnail( $post_ID );
	if ( ! $featured_image ) {
		return '';
	}

	if ( isset( $attributes['isLink'] ) && $attributes['isLink'] ) {
		$featured_image = sprintf( '<a href="%1s">%2s</a>', get_the_permalink( $post_ID ), $featured_image );
	}

	$wrapper_attributes = get_block_wrapper_attributes();

	return '<figure ' . $wrapper_attributes . '>' . $featured_image . '</figure>';
}

/**
 * Registers the `core/post-featured-image` block on the server.
 */
function register_block_core_post_featured_image() {
	register_block_type_from_metadata(
		__DIR__ . '/post-featured-image',
		array(
			'render_callback' => 'render_block_core_post_featured_image',
		)
	);
}
add_action( 'init', 'register_block_core_post_featured_image' );
