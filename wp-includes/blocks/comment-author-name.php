<?php
/**
 * Server-side rendering of the `core/comment-author-name` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/comment-author-name` block on the server.
 *
 * @since 6.0.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Return the post comment's author.
 */
function render_block_core_comment_author_name( $attributes, $content, $block ) {
	if ( ! isset( $block->context['commentId'] ) ) {
		return '';
	}

	$comment            = get_comment( $block->context['commentId'] );
	$commenter          = wp_get_current_commenter();
	$show_pending_links = isset( $commenter['comment_author'] ) && $commenter['comment_author'];
	if ( empty( $comment ) ) {
		return '';
	}

	$classes = array();
	if ( isset( $attributes['textAlign'] ) ) {
		$classes[] = 'has-text-align-' . $attributes['textAlign'];
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );
	$comment_author     = get_comment_author( $comment );
	$link               = get_comment_author_url( $comment );

	if ( ! empty( $link ) && ! empty( $attributes['isLink'] ) && ! empty( $attributes['linkTarget'] ) ) {
		$comment_author = sprintf( '<a rel="external nofollow ugc" href="%1s" target="%2s" >%3s</a>', esc_url( $link ), esc_attr( $attributes['linkTarget'] ), $comment_author );
	}
	if ( '0' === $comment->comment_approved && ! $show_pending_links ) {
		$comment_author = wp_kses( $comment_author, array() );
	}

	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$comment_author
	);
}

/**
 * Registers the `core/comment-author-name` block on the server.
 *
 * @since 6.0.0
 */
function register_block_core_comment_author_name() {
	register_block_type_from_metadata(
		__DIR__ . '/comment-author-name',
		array(
			'render_callback' => 'render_block_core_comment_author_name',
		)
	);
}
add_action( 'init', 'register_block_core_comment_author_name' );
