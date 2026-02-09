<?php
/**
 * Shared helper function for checking if navigation items should render.
 *
 * @package WordPress
 */

/**
 * Checks if a navigation item should render based on post status.
 *
 * @since 7.0.0
 *
 * @param array    $attributes The block attributes.
 * @param WP_Block $block      The parsed block.
 * @return bool True if the item should render, false otherwise.
 */
function block_core_shared_navigation_item_should_render( $attributes, $block ) {
	$navigation_link_has_id = isset( $attributes['id'] ) && is_numeric( $attributes['id'] );
	$is_post_type           = isset( $attributes['kind'] ) && 'post-type' === $attributes['kind'];
	$is_post_type           = $is_post_type || isset( $attributes['type'] ) && ( 'post' === $attributes['type'] || 'page' === $attributes['type'] );

	// Don't render the block's subtree if it is a draft or if the ID does not exist.
	if ( $is_post_type && $navigation_link_has_id ) {
		$post = get_post( $attributes['id'] );
		/**
		 * Filter allowed post_status for navigation link block to render.
		 *
		 * @since 6.8.0
		 *
		 * @param array    $post_status Array of allowed post statuses.
		 * @param array    $attributes  Block attributes.
		 * @param WP_Block $block       The parsed block.
		 */
		$allowed_post_status = (array) apply_filters(
			'render_block_core_navigation_link_allowed_post_status',
			array( 'publish' ),
			$attributes,
			$block
		);
		if ( ! $post || ! in_array( $post->post_status, $allowed_post_status, true ) ) {
			return false;
		}
	}

	return true;
}
