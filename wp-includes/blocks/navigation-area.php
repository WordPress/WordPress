<?php
/**
 * Server-side rendering of the `core/navigation-area` block.
 *
 * @deprecated 5.9.0 See https://github.com/WordPress/gutenberg/issues/36524
 * @package WordPress
 */

/**
 * Registers the `core/navigation-area` block on the server.
 */
function register_block_core_navigation_area() {
	register_block_type_from_metadata(
		__DIR__ . '/navigation-area',
		array(
			'provides_context' => array(
				'navigationArea' => 'area',
			),
		)
	);
}
add_action( 'init', 'register_block_core_navigation_area' );
