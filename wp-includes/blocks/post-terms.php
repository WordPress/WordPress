<?php
/**
 * Server-side rendering of the `core/post-terms` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-terms` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the filtered post terms for the current post wrapped inside "a" tags.
 */
function render_block_core_post_terms( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) || ! isset( $attributes['term'] ) ) {
		return '';
	}

	if ( ! is_taxonomy_viewable( $attributes['term'] ) ) {
		return '';
	}

	$post_terms = get_the_terms( $block->context['postId'], $attributes['term'] );
	if ( is_wp_error( $post_terms ) || empty( $post_terms ) ) {
		return '';
	}

	$classes = 'taxonomy-' . $attributes['term'];
	if ( isset( $attributes['textAlign'] ) ) {
		$classes .= ' has-text-align-' . $attributes['textAlign'];
	}

	$separator = empty( $attributes['separator'] ) ? ' ' : $attributes['separator'];

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classes ) );

	return get_the_term_list(
		$block->context['postId'],
		$attributes['term'],
		"<div $wrapper_attributes>",
		'<span class="wp-block-post-terms__separator">' . $separator . '</span>',
		'</div>'
	);
}

/**
 * Registers the `core/post-terms` block on the server.
 */
function register_block_core_post_terms() {
	register_block_type_from_metadata(
		__DIR__ . '/post-terms',
		array(
			'render_callback' => 'render_block_core_post_terms',
		)
	);
}
add_action( 'init', 'register_block_core_post_terms' );
