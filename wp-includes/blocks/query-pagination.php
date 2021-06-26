<?php
/**
 * Server-side rendering of the `core/query-pagination` block.
 *
 * @package WordPress
 */

/**
 * Registers the `core/query-pagination` block on the server.
 */
function register_block_core_query_pagination() {
	register_block_type_from_metadata(
		__DIR__ . '/query-pagination'
	);
}
add_action( 'init', 'register_block_core_query_pagination' );
