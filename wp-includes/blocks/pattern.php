<?php
/**
 * Server-side rendering of the `core/pattern` block.
 *
 * @package WordPress
 */

/**
 *  Registers the `core/pattern` block on the server.
 *
 * @since 5.9.0
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
 * @global WP_Embed $wp_embed Used to process embedded content within patterns
 *
 * @param array $attributes Block attributes.
 *
 * @return string Returns the output of the pattern.
 */
function render_block_core_pattern( $attributes ) {
	static $seen_refs = array();

	if ( empty( $attributes['slug'] ) ) {
		return '';
	}

	$slug     = $attributes['slug'];
	$registry = WP_Block_Patterns_Registry::get_instance();

	if ( ! $registry->is_registered( $slug ) ) {
		return '';
	}

	if ( isset( $seen_refs[ $attributes['slug'] ] ) ) {
		// WP_DEBUG_DISPLAY must only be honored when WP_DEBUG. This precedent
		// is set in `wp_debug_mode()`.
		$is_debug = WP_DEBUG && WP_DEBUG_DISPLAY;

		return $is_debug ?
			// translators: Visible only in the front end, this warning takes the place of a faulty block. %s represents a pattern's slug.
			sprintf( __( '[block rendering halted for pattern "%s"]' ), $slug ) :
			'';
	}

	$pattern = $registry->get_registered( $slug );
	$content = $pattern['content'];

	$seen_refs[ $attributes['slug'] ] = true;

	$content = do_blocks( $content );

	global $wp_embed;
	$content = $wp_embed->autoembed( $content );

	unset( $seen_refs[ $attributes['slug'] ] );
	return $content;
}

add_action( 'init', 'register_block_core_pattern' );
