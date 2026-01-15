<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName // Needed for WP_Block_Cloner helper class.
/**
 * Server-side rendering of the `core/block` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/block` block on server.
 *
 * @since 5.0.0
 *
 * @global WP_Embed $wp_embed
 *
 * @param array $attributes The block attributes.
 *
 * @return string Rendered HTML of the referenced block.
 */
function render_block_core_block( $attributes, $content, $block_instance ) {
	static $seen_refs = array();

	if ( empty( $attributes['ref'] ) ) {
		return '';
	}

	$reusable_block = get_post( $attributes['ref'] );
	if ( ! $reusable_block || 'wp_block' !== $reusable_block->post_type ) {
		return '';
	}

	if ( isset( $seen_refs[ $attributes['ref'] ] ) ) {
		// WP_DEBUG_DISPLAY must only be honored when WP_DEBUG. This precedent
		// is set in `wp_debug_mode()`.
		$is_debug = WP_DEBUG && WP_DEBUG_DISPLAY;

		return $is_debug ?
			// translators: Visible only in the front end, this warning takes the place of a faulty block.
			__( '[block rendering halted]' ) :
			'';
	}

	if ( 'publish' !== $reusable_block->post_status || ! empty( $reusable_block->post_password ) ) {
		return '';
	}

	$seen_refs[ $attributes['ref'] ] = true;

	// Handle embeds for reusable blocks.
	global $wp_embed;
	$content = $wp_embed->run_shortcode( $reusable_block->post_content );
	$content = $wp_embed->autoembed( $content );

	// Back compat.
	// For blocks that have not been migrated in the editor, add some back compat
	// so that front-end rendering continues to work.

	// This matches the `v2` deprecation. Removes the inner `values` property
	// from every item.
	if ( isset( $attributes['content'] ) ) {
		foreach ( $attributes['content'] as &$content_data ) {
			if ( isset( $content_data['values'] ) ) {
				$is_assoc_array = is_array( $content_data['values'] ) && ! wp_is_numeric_array( $content_data['values'] );

				if ( $is_assoc_array ) {
					$content_data = $content_data['values'];
				}
			}
		}
	}

	// This matches the `v1` deprecation. Rename `overrides` to `content`.
	if ( isset( $attributes['overrides'] ) && ! isset( $attributes['content'] ) ) {
		$attributes['content'] = $attributes['overrides'];
	}

	// Apply Block Hooks.
	$content = apply_block_hooks_to_content_from_post_object( $content, $reusable_block );

	/**
	 * We attach the blocks from $content as inner blocks to the Synced Pattern block instance.
	 * This ensures that block context available to the Synced Pattern block instance is provided to
	 * those blocks.
	 */
	$block_instance->parsed_block['innerBlocks']  = parse_blocks( $content );
	$block_instance->parsed_block['innerContent'] = array_fill( 0, count( $block_instance->parsed_block['innerBlocks'] ), null );
	if ( method_exists( $block_instance, 'refresh_context_dependents' ) ) {
		// WP_Block::refresh_context_dependents() was introduced in WordPress 6.8.
		$block_instance->refresh_context_dependents();
	} else {
		// This branch can be removed once Gutenberg requires WordPress 6.8 or later.
		if ( ! class_exists( 'WP_Block_Cloner' ) ) {
			// phpcs:ignore Gutenberg.Commenting.SinceTag.MissingClassSinceTag
			class WP_Block_Cloner extends WP_Block {
				/**
				 * Static methods of subclasses have access to protected properties
				 * of instances of the parent class.
				 * In this case, this gives us access to `available_context` and `registry`.
				 */
				// phpcs:ignore Gutenberg.Commenting.SinceTag.MissingMethodSinceTag
				public static function clone_instance( $instance ) {
					return new WP_Block(
						$instance->parsed_block,
						$instance->available_context,
						$instance->registry
					);
				}
			}
		}
		$block_instance = WP_Block_Cloner::clone_instance( $block_instance );
	}

	$content = $block_instance->render( array( 'dynamic' => false ) );
	unset( $seen_refs[ $attributes['ref'] ] );

	return $content;
}

/**
 * Registers the `core/block` block.
 *
 * @since 5.3.0
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
