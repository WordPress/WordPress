<?php
/**
 * Server-side rendering of the `core/comments-pagination-previous` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/comments-pagination-previous` block on the server.
 *
 * @since 6.0.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the previous posts link for the comments pagination.
 */
function gutenberg_render_block_core_comments_pagination_previous( $attributes, $content, $block ) {
	$default_label    = __( 'Older Comments' );
	$label            = isset( $attributes['label'] ) && ! empty( $attributes['label'] ) ? $attributes['label'] : $default_label;
	$pagination_arrow = get_comments_pagination_arrow( $block, 'previous' );
	if ( $pagination_arrow ) {
		$label = $pagination_arrow . $label;
	}

	$filter_link_attributes = static function () {
		return get_block_wrapper_attributes();
	};
	add_filter( 'previous_comments_link_attributes', $filter_link_attributes );

	$comment_vars           = build_comment_query_vars_from_block( $block );
	$previous_comments_link = get_previous_comments_link( $label, $comment_vars['paged'] ?? null );

	remove_filter( 'previous_comments_link_attributes', $filter_link_attributes );

	if ( ! isset( $previous_comments_link ) ) {
		return '';
	}

	return $previous_comments_link;
}

/**
 * Registers the `core/comments-pagination-previous` block on the server.
 *
 * @since 6.0.0
 */
function gutenberg_register_block_core_comments_pagination_previous() {
	register_block_type_from_metadata(
		__DIR__ . '/comments-pagination-previous',
		array(
			'render_callback' => 'gutenberg_render_block_core_comments_pagination_previous',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_comments_pagination_previous', 20 );
