<?php
/**
 * Server-side rendering of the `core/pattern` block.
 *
 * @package WordPress
 */

/**
 *  Registers the `core/pattern` block on the server.
 */
function register_block_core_pattern() {
	register_block_type_from_metadata(
		__DIR__ . '/pattern',
		array(
			'render_callback' => 'render_block_core_pattern',
		)
	);
}

/**
 * Renders the `core/pattern` block on the server.
 *
 * @since 6.3.0 Backwards compatibility: blocks with no `syncStatus` attribute do not receive block wrapper.
 *
 * @param array $attributes Block attributes.
 *
 * @return string Returns the output of the pattern.
 */
function render_block_core_pattern( $attributes ) {
	if ( empty( $attributes['slug'] ) ) {
		return '';
	}

	$slug     = $attributes['slug'];
	$registry = WP_Block_Patterns_Registry::get_instance();

	if ( ! $registry->is_registered( $slug ) ) {
		return '';
	}

	$pattern = $registry->get_registered( $slug );
	$content = $pattern['content'];

	// Backward compatibility for handling Block Hooks and injecting the theme attribute in the Gutenberg plugin.
	// This can be removed when the minimum supported WordPress is >= 6.4.
	if ( defined( 'IS_GUTENBERG_PLUGIN' ) && IS_GUTENBERG_PLUGIN && ! function_exists( 'traverse_and_serialize_blocks' ) ) {
		$blocks  = parse_blocks( $content );
		$content = gutenberg_serialize_blocks( $blocks );
	}

	return do_blocks( $content );
}

add_action( 'init', 'register_block_core_pattern' );
