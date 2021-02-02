<?php
/**
 * Server-side rendering of the `core/block` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/block` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Rendered HTML of the referenced block.
 */
function render_block_core_block( $attributes ) {
	static $seen_refs = array();

	if ( empty( $attributes['ref'] ) ) {
		return '';
	}

	$reusable_block = get_post( $attributes['ref'] );
	if ( ! $reusable_block || 'wp_block' !== $reusable_block->post_type ) {
		return '';
	}

	if ( isset( $seen_refs[ $attributes['ref'] ] ) ) {
		if ( ! is_admin() ) {
			trigger_error(
				sprintf(
					// translators: %s is the user-provided title of the reusable block.
					__( 'Could not render Reusable Block <strong>%s</strong>: blocks cannot be rendered inside themselves.' ),
					$reusable_block->post_title
				),
				E_USER_WARNING
			);
		}

		// WP_DEBUG_DISPLAY must only be honored when WP_DEBUG. This precedent
		// is set in `wp_debug_mode()`.
		$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG &&
			defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;

		return $is_debug ?
			// translators: Visible only in the front end, this warning takes the place of a faulty block.
			__( '[block rendering halted]' ) :
			'';
	}

	if ( 'publish' !== $reusable_block->post_status || ! empty( $reusable_block->post_password ) ) {
		return '';
	}

	$seen_refs[ $attributes['ref'] ] = true;

	$result = do_blocks( $reusable_block->post_content );
	unset( $seen_refs[ $attributes['ref'] ] );
	return $result;
}

/**
 * Registers the `core/block` block.
 */
function register_block_core_block() {
	register_block_type_from_metadata(
		__DIR__ . '/block',
		array(
			'render_callback' => 'render_block_core_block',
		)
	);
}
add_action( 'init', 'register_block_core_block' );
