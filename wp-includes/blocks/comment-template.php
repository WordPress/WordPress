<?php
/**
 * Server-side rendering of the `core/comment-template` block.
 *
 * @package WordPress
 */

/**
 * Function that recursively renders a list of nested comments.
 *
 * @since 6.3.0 Changed render_block_context priority to `1`.
 *
 * @global int $comment_depth
 *
 * @param WP_Comment[] $comments        The array of comments.
 * @param WP_Block     $block           Block instance.
 * @return string
 */
function block_core_comment_template_render_comments( $comments, $block ) {
	global $comment_depth;
	$thread_comments       = get_option( 'thread_comments' );
	$thread_comments_depth = get_option( 'thread_comments_depth' );

	if ( empty( $comment_depth ) ) {
		$comment_depth = 1;
	}

	$content = '';
	foreach ( $comments as $comment ) {
		$comment_id           = $comment->comment_ID;
		$filter_block_context = static function ( $context ) use ( $comment_id ) {
			$context['commentId'] = $comment_id;
			return $context;
		};

		/*
		 * We set commentId context through the `render_block_context` filter so
		 * that dynamically inserted blocks (at `render_block` filter stage)
		 * will also receive that context.
		 *
		 * Use an early priority to so that other 'render_block_context' filters
		 * have access to the values.
		 */
		add_filter( 'render_block_context', $filter_block_context, 1 );

		/*
		 * We construct a new WP_Block instance from the parsed block so that
		 * it'll receive any changes made by the `render_block_data` filter.
		 */
		$block_content = ( new WP_Block( $block->parsed_block ) )->render( array( 'dynamic' => false ) );

		remove_filter( 'render_block_context', $filter_block_context, 1 );

		$children = $comment->get_children();

		/*
		 * We need to create the CSS classes BEFORE recursing into the children.
		 * This is because comment_class() uses globals like `$comment_alt`
		 * and `$comment_thread_alt` which are order-sensitive.
		 *
		 * The `false` parameter at the end means that we do NOT want the function
		 * to `echo` the output but to return a string.
		 * See https://developer.wordpress.org/reference/functions/comment_class/#parameters.
		 */
		$comment_classes = comment_class( '', $comment->comment_ID, $comment->comment_post_ID, false );

		// If the comment has children, recurse to create the HTML for the nested
		// comments.
		if ( ! empty( $children ) && ! empty( $thread_comments ) ) {
			if ( $comment_depth < $thread_comments_depth ) {
				++$comment_depth;
				$inner_content  = block_core_comment_template_render_comments(
					$children,
					$block
				);
				$block_content .= sprintf( '<ol>%1$s</ol>', $inner_content );
				--$comment_depth;
			} else {
				$block_content .= block_core_comment_template_render_comments(
					$children,
					$block
				);
			}
		}

		$content .= sprintf( '<li id="comment-%1$s" %2$s>%3$s</li>', $comment->comment_ID, $comment_classes, $block_content );
	}

	return $content;
}

/**
 * Renders the `core/comment-template` block on the server.
 *
 * @since 6.0.0
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

	if ( post_password_required( $block->context['postId'] ) ) {
		return;
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
 *
 * @since 6.0.0
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
