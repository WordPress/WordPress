<?php
/**
 * Server-side rendering of the `core/post-comment` block.
 *
 * @package WordPress
 */

/**
 * Registers the `core/post-comment` block on the server.
 * We need to do this to make context available for inner blocks.
 */
function gutenberg_register_block_core_post_comment() {
	register_block_type_from_metadata(
		__DIR__ . '/post-comment'
	);
}
add_action( 'init', 'gutenberg_register_block_core_post_comment', 20 );
