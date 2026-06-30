<?php
/**
 * Server-side rendering of the `core/button` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/button` block on the server,
 *
 * @since 6.6.0
 *
 * @param array  $attributes The block attributes.
 * @param string $content    The block content.
 *
 * @return string The block content.
 */
function render_block_core_button( $attributes, $content ) {
	$p = new WP_HTML_Tag_Processor( $content );

	/*
	 * The button block can render an `<a>` or `<button>` and also has a
	 * `<div>` wrapper. Find the a or button tag.
	 */
	$tag = null;
	while ( $p->next_tag() ) {
		$tag = $p->get_tag();
		if ( 'A' === $tag || 'BUTTON' === $tag ) {
			break;
		}
	}

	/*
	 * If this happens, the likelihood is there's no block content,
	 * or the block has been modified by a plugin.
	 */
	if ( null === $tag ) {
		return $content;
	}

	// If the next token is the closing tag, the button is empty.
	$is_empty = true;
	while ( $p->next_token() && $tag !== $p->get_token_name() && $is_empty ) {
		if ( '#comment' !== $p->get_token_type() ) {
			/**
			 * Anything else implies this is not empty.
			 * This might include any text content (including a space),
			 * inline images or other HTML.
			 */
			$is_empty = false;
		}
	}

	/*
	 * When there's no text, render nothing for the block.
	 * See https://github.com/WordPress/gutenberg/issues/17221 for the
	 * reasoning behind this.
	 */
	if ( $is_empty ) {
		return '';
	}

	$width = $attributes['style']['dimensions']['width'] ?? null;

	if ( $width ) {
		// Resolve preset references to their actual values.
		$resolved_width = $width;
		$is_preset      = str_starts_with( $width, 'var:preset|dimension|' );

		if ( $is_preset ) {
			$slug              = substr( $width, strlen( 'var:preset|dimension|' ) );
			$dimension_presets = wp_get_global_settings(
				array( 'dimensions', 'dimensionSizes' ),
				array( 'block_name' => 'core/button' )
			);

			// Search origins in priority order: custom > theme > default.
			if ( is_array( $dimension_presets ) ) {
				foreach ( array( 'custom', 'theme', 'default' ) as $origin ) {
					if ( empty( $dimension_presets[ $origin ] ) || ! is_array( $dimension_presets[ $origin ] ) ) {
						continue;
					}
					foreach ( $dimension_presets[ $origin ] as $preset ) {
						if ( isset( $preset['slug'] ) && $preset['slug'] === $slug ) {
							$resolved_width = $preset['size'] ?? $width;
							break 2;
						}
					}
				}
			}
		}

		$is_percentage = str_ends_with( $resolved_width, '%' );

		$processor = new WP_HTML_Tag_Processor( $content );
		// Target the outer wrapper div.
		if ( $processor->next_tag( array( 'class_name' => 'wp-block-button' ) ) ) {
			$processor->add_class( 'has-custom-width' );
			$existing_style = $processor->get_attribute( 'style' );
			$existing_style = is_string( $existing_style ) ? $existing_style : '';

			if ( $is_percentage ) {
				$numeric_width = (float) $resolved_width;
				$processor->add_class( 'wp-block-button__width' );

				// Maintain legacy class for the standard percentage widths.
				$legacy_widths = array(
					'25%'  => 'wp-block-button__width-25',
					'50%'  => 'wp-block-button__width-50',
					'75%'  => 'wp-block-button__width-75',
					'100%' => 'wp-block-button__width-100',
				);
				if ( isset( $legacy_widths[ $resolved_width ] ) ) {
					$processor->add_class( $legacy_widths[ $resolved_width ] );
				}

				$width_style = "--wp--block-button--width: $numeric_width;";
				$processor->set_attribute( 'style', $width_style . ( $existing_style ? ' ' . $existing_style : '' ) );
			} else {
				$css_value   = $is_preset
					? 'var(--wp--preset--dimension--' . _wp_to_kebab_case( $slug ) . ')'
					: $width;
				$width_style = "width: $css_value;";
				$processor->set_attribute( 'style', $width_style . ( $existing_style ? ' ' . $existing_style : '' ) );
			}

			$content = $processor->get_updated_html();
		}
	}

	return $content;
}

/**
 * Registers the `core/button` block on server.
 *
 * @since 6.6.0
 */
function register_block_core_button() {
	register_block_type_from_metadata(
		__DIR__ . '/button',
		array(
			'render_callback' => 'render_block_core_button',
		)
	);
}
add_action( 'init', 'register_block_core_button' );
