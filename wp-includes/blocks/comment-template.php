<?php
/**
 * Server-side rendering of the `core/comment-template` block.
 *
 * @package WordPress
 */

/**
 * Function that recursively renders a list of nested comments.
 *
 * @param WP_Comment[] $comments    The array of comments.
 * @param WP_Block     $block           Block instance.
 * @return string
 */
function block_core_comment_template_render_comments( $comments, $block ) {
	$content = '';
	foreach ( $comments as $comment ) {

		$block_content = ( new WP_Block(
			$block->parsed_block,
			array(
				'commentId' => $comment->comment_ID,
			)
		) )->render( array( 'dynamic' => false ) );

		$children = $comment->get_children();

		// If the comment has children, recurse to create the HTML for the nested
		// comments.
		if ( ! empty( $children ) ) {
			$inner_content  = block_core_comment_template_render_comments(
				$children,
				$block
			);
			$block_content .= sprintf( '<ol>%1$s</ol>', $inner_content );
		}

		$content .= '<li>' . $block_content . '</li>';
	}

	return $content;

}

/**
 * Renders the `core/comment-template` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the HTML representing the comments using the layout
 * defined by the block's inner blocks.
 */
function render_block_core_comment_template( $attributes, $content, $block ) {
	// Bail out early if the post ID is not set for some reason.
	if ( empty( $block->context['postId'] ) ) {
		return '';
	}

	$comment_query = new WP_Comment_Query(
		build_comment_query_vars_from_block( $block )
	);

	// Get an array of comments for the current post.
	$comments = $comment_query->get_comments();
	if ( count( $comments ) === 0 ) {
		return '';
	}

	$comment_order = get_option( 'comment_order' );

	if ( 'desc' === $comment_order ) {
		$comments = array_reverse( $comments );
	}

	$wrapper_attributes = get_block_wrapper_attributes();

	return sprintf(
		'<ol %1$s>%2$s</ol>',
		$wrapper_attributes,
		block_core_comment_template_render_comments( $comments, $block )
	);
}

/**
 * Registers the `core/comment-template` block on the server.
 */
function register_block_core_comment_template() {
	register_block_type_from_metadata(
		__DIR__ . '/comment-template',
		array(
			'render_callback'   => 'render_block_core_comment_template',
			'skip_inner_blocks' => true,
		)
	);
}
add_action( 'init', 'register_block_core_comment_template' );
