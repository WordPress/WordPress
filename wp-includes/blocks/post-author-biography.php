<?php
/**
 * Server-side rendering of the `core/post-author-biography` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-author-biography` block on the server.
 *
 * @since 6.0.0
 *
 * @param  array    $attributes Block attributes.
 * @param  string   $content    Block default content.
 * @param  WP_Block $block      Block instance.
 * @return string Returns the rendered post author biography block.
 */
function render_block_core_post_author_biography( $attributes, $content, $block ) {
	if ( isset( $block->context['postId'] ) ) {
		$author_id = get_post_field( 'post_author', $block->context['postId'] );
	} else {
		$author_id = get_query_var( 'author' );
	}

	if ( empty( $author_id ) ) {
		return '';
	}

	$author_biography = get_the_author_meta( 'description', $author_id );
	if ( empty( $author_biography ) ) {
		return '';
	}

	$align_class_name   = empty( $attributes['textAlign'] ) ? '' : "has-text-align-{$attributes['textAlign']}";
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $align_class_name ) );

	return sprintf( '<div %1$s>', $wrapper_attributes ) . $author_biography . '</div>';
}

/**
 * Registers the `core/post-author-biography` block on the server.
 *
 * @since 6.0.0
 */
function register_block_core_post_author_biography() {
	register_block_type_from_metadata(
		__DIR__ . '/post-author-biography',
		array(
			'render_callback' => 'render_block_core_post_author_biography',
		)
	);
}
add_action( 'init', 'register_block_core_post_author_biography' );
