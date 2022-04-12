<?php
/**
 * Server-side rendering of the `core/comments-pagination-next` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/comments-pagination-next` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the next comments link for the query pagination.
 */
function render_block_core_comments_pagination_next( $attributes, $content, $block ) {
	// Bail out early if the post ID is not set for some reason.
	if ( empty( $block->context['postId'] ) ) {
		return '';
	}

	$comment_vars     = build_comment_query_vars_from_block( $block );
	$max_page         = ( new WP_Comment_Query( $comment_vars ) )->max_num_pages;
	$default_label    = __( 'Newer Comments' );
	$label            = isset( $attributes['label'] ) && ! empty( $attributes['label'] ) ? $attributes['label'] : $default_label;
	$pagination_arrow = get_comments_pagination_arrow( $block, 'next' );

	$filter_link_attributes = function() {
		return get_block_wrapper_attributes();
	};
	add_filter( 'next_comments_link_attributes', $filter_link_attributes );

	if ( $pagination_arrow ) {
		$label .= $pagination_arrow;
	}

	$next_comments_link = get_next_comments_link( $label, $max_page );

	remove_filter( 'next_posts_link_attributes', $filter_link_attributes );

	if ( ! isset( $next_comments_link ) ) {
		return '';
	}
	return $next_comments_link;
}


/**
 * Registers the `core/comments-pagination-next` block on the server.
 */
function register_block_core_comments_pagination_next() {
	register_block_type_from_metadata(
		__DIR__ . '/comments-pagination-next',
		array(
			'render_callback' => 'render_block_core_comments_pagination_next',
		)
	);
}
add_action( 'init', 'register_block_core_comments_pagination_next' );
