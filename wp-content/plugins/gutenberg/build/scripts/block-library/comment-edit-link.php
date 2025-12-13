<?php
/**
 * Server-side rendering of the `core/comment-edit-link` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/comment-edit-link` block on the server.
 *
 * @since 6.0.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Return the post comment's date.
 */
function gutenberg_render_block_core_comment_edit_link( $attributes, $content, $block ) {
	if ( ! isset( $block->context['commentId'] ) || ! current_user_can( 'edit_comment', $block->context['commentId'] ) ) {
		return '';
	}

	$edit_comment_link = get_edit_comment_link( $block->context['commentId'] );

	$link_atts = '';

	if ( ! empty( $attributes['linkTarget'] ) ) {
		$link_atts .= sprintf( 'target="%s"', esc_attr( $attributes['linkTarget'] ) );
	}

	$classes = array();
	if ( isset( $attributes['textAlign'] ) ) {
		$classes[] = 'has-text-align-' . $attributes['textAlign'];
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	return sprintf(
		'<div %1$s><a href="%2$s" %3$s>%4$s</a></div>',
		$wrapper_attributes,
		esc_url( $edit_comment_link ),
		$link_atts,
		esc_html__( 'Edit' )
	);
}

/**
 * Registers the `core/comment-edit-link` block on the server.
 *
 * @since 6.0.0
 */
function gutenberg_register_block_core_comment_edit_link() {
	register_block_type_from_metadata(
		__DIR__ . '/comment-edit-link',
		array(
			'render_callback' => 'gutenberg_render_block_core_comment_edit_link',
		)
	);
}

add_action( 'init', 'gutenberg_register_block_core_comment_edit_link', 20 );
