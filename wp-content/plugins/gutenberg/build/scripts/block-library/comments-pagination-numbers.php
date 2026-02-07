<?php
/**
 * Server-side rendering of the `core/comments-pagination-numbers` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/comments-pagination-numbers` block on the server.
 *
 * @since 6.0.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the pagination numbers for the comments.
 */
function gutenberg_render_block_core_comments_pagination_numbers( $attributes, $content, $block ) {
	// Bail out early if the post ID is not set for some reason.
	if ( empty( $block->context['postId'] ) ) {
		return '';
	}

	$comment_vars = build_comment_query_vars_from_block( $block );

	$total   = ( new WP_Comment_Query( $comment_vars ) )->max_num_pages;
	$current = ! empty( $comment_vars['paged'] ) ? $comment_vars['paged'] : null;

	// Render links.
	$content = paginate_comments_links(
		array(
			'total'     => $total,
			'current'   => $current,
			'prev_next' => false,
			'echo'      => false,
		)
	);

	if ( empty( $content ) ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes();

	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$content
	);
}

/**
 * Registers the `core/comments-pagination-numbers` block on the server.
 *
 * @since 6.0.0
 */
function gutenberg_register_block_core_comments_pagination_numbers() {
	register_block_type_from_metadata(
		__DIR__ . '/comments-pagination-numbers',
		array(
			'render_callback' => 'gutenberg_render_block_core_comments_pagination_numbers',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_comments_pagination_numbers', 20 );
