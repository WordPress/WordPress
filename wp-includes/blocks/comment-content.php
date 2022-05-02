<?php
/**
 * Server-side rendering of the `core/comment-content` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/comment-content` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Return the post comment's content.
 */
function render_block_core_comment_content( $attributes, $content, $block ) {
	if ( ! isset( $block->context['commentId'] ) ) {
		return '';
	}

	$comment            = get_comment( $block->context['commentId'] );
	$commenter          = wp_get_current_commenter();
	$show_pending_links = isset( $commenter['comment_author'] ) && $commenter['comment_author'];
	if ( empty( $comment ) ) {
		return '';
	}

	$args         = array();
	$comment_text = get_comment_text( $comment, $args );
	if ( ! $comment_text ) {
		return '';
	}

	/** This filter is documented in wp-includes/comment-template.php */
	$comment_text = apply_filters( 'comment_text', $comment_text, $comment, $args );

	$moderation_note = '';
	if ( '0' === $comment->comment_approved ) {
		$commenter = wp_get_current_commenter();

		if ( $commenter['comment_author_email'] ) {
			$moderation_note = __( 'Your comment is awaiting moderation.' );
		} else {
			$moderation_note = __( 'Your comment is awaiting moderation. This is a preview; your comment will be visible after it has been approved.' );
		}
		$moderation_note = '<p><em class="comment-awaiting-moderation">' . $moderation_note . '</em></p>';
		if ( ! $show_pending_links ) {
			$comment_text = wp_kses( $comment_text, array() );
		}
	}

	$classes = '';
	if ( isset( $attributes['textAlign'] ) ) {
		$classes .= 'has-text-align-' . $attributes['textAlign'];
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classes ) );

	return sprintf(
		'<div %1$s>%2$s%3$s</div>',
		$wrapper_attributes,
		$moderation_note,
		$comment_text
	);
}

/**
 * Registers the `core/comment-content` block on the server.
 */
function register_block_core_comment_content() {
	register_block_type_from_metadata(
		__DIR__ . '/comment-content',
		array(
			'render_callback' => 'render_block_core_comment_content',
		)
	);
}
add_action( 'init', 'register_block_core_comment_content' );
