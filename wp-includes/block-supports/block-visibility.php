<?php
/**
 * Block visibility block support flag.
 *
 * @package WordPress
 * @since 6.9.0
 */

/**
 * Render nothing if the block is hidden.
 *
 * @since 6.9.0
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 */
function wp_render_block_visibility_support( $block_content, $block ) {
	$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );

	if ( ! $block_type || ! block_has_support( $block_type, 'visibility', true ) ) {
		return $block_content;
	}

	if ( isset( $block['attrs']['metadata']['blockVisibility'] ) && false === $block['attrs']['metadata']['blockVisibility'] ) {
		return '';
	}

	return $block_content;
}

add_filter( 'render_block', 'wp_render_block_visibility_support', 10, 2 );
