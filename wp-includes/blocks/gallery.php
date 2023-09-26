<?php
/**
 * Server-side rendering of the `core/gallery` block.
 *
 * @package WordPress
 */

/**
 * Handles backwards compatibility for Gallery Blocks,
 * whose images feature a `data-id` attribute.
 *
 * Now that the Gallery Block contains inner Image Blocks,
 * we add a custom `data-id` attribute before rendering the gallery
 * so that the Image Block can pick it up in its render_callback.
 *
 * @param array $parsed_block The block being rendered.
 * @return array The migrated block object.
 */
function block_core_gallery_data_id_backcompatibility( $parsed_block ) {
	if ( 'core/gallery' === $parsed_block['blockName'] ) {
		foreach ( $parsed_block['innerBlocks'] as $key => $inner_block ) {
			if ( 'core/image' === $inner_block['blockName'] ) {
				if ( ! isset( $parsed_block['innerBlocks'][ $key ]['attrs']['data-id'] ) && isset( $inner_block['attrs']['id'] ) ) {
					$parsed_block['innerBlocks'][ $key ]['attrs']['data-id'] = esc_attr( $inner_block['attrs']['id'] );
				}
			}
		}
	}

	return $parsed_block;
}

add_filter( 'render_block_data', 'block_core_gallery_data_id_backcompatibility' );

/**
 * Adds a style tag for the --wp--style--unstable-gallery-gap var.
 *
 * The Gallery block needs to recalculate Image block width based on
 * the current gap setting in order to maintain the number of flex columns
 * so a css var is added to allow this.
 *
 * @param array  $attributes Attributes of the block being rendered.
 * @param string $content Content of the block being rendered.
 * @return string The content of the block being rendered.
 */
function block_core_gallery_render( $attributes, $content ) {
	$gap = $attributes['style']['spacing']['blockGap'] ?? null;
	// Skip if gap value contains unsupported characters.
	// Regex for CSS value borrowed from `safecss_filter_attr`, and used here
	// because we only want to match against the value, not the CSS attribute.
	if ( is_array( $gap ) ) {
		foreach ( $gap as $key => $value ) {
			// Make sure $value is a string to avoid PHP 8.1 deprecation error in preg_match() when the value is null.
			$value = is_string( $value ) ? $value : '';
			$value = $value && preg_match( '%[\\\(&=}]|/\*%', $value ) ? null : $value;

			// Get spacing CSS variable from preset value if provided.
			if ( is_string( $value ) && str_contains( $value, 'var:preset|spacing|' ) ) {
				$index_to_splice = strrpos( $value, '|' ) + 1;
				$slug            = _wp_to_kebab_case( substr( $value, $index_to_splice ) );
				$value           = "var(--wp--preset--spacing--$slug)";
			}

			$gap[ $key ] = $value;
		}
	} else {
		// Make sure $gap is a string to avoid PHP 8.1 deprecation error in preg_match() when the value is null.
		$gap = is_string( $gap ) ? $gap : '';
		$gap = $gap && preg_match( '%[\\\(&=}]|/\*%', $gap ) ? null : $gap;

		// Get spacing CSS variable from preset value if provided.
		if ( is_string( $gap ) && str_contains( $gap, 'var:preset|spacing|' ) ) {
			$index_to_splice = strrpos( $gap, '|' ) + 1;
			$slug            = _wp_to_kebab_case( substr( $gap, $index_to_splice ) );
			$gap             = "var(--wp--preset--spacing--$slug)";
		}
	}

	$unique_gallery_classname = wp_unique_id( 'wp-block-gallery-' );
	$processed_content        = new WP_HTML_Tag_Processor( $content );
	$processed_content->next_tag();
	$processed_content->add_class( $unique_gallery_classname );

	// --gallery-block--gutter-size is deprecated. --wp--style--gallery-gap-default should be used by themes that want to set a default
	// gap on the gallery.
	$fallback_gap = 'var( --wp--style--gallery-gap-default, var( --gallery-block--gutter-size, var( --wp--style--block-gap, 0.5em ) ) )';
	$gap_value    = $gap ? $gap : $fallback_gap;
	$gap_column   = $gap_value;

	if ( is_array( $gap_value ) ) {
		$gap_row    = isset( $gap_value['top'] ) ? $gap_value['top'] : $fallback_gap;
		$gap_column = isset( $gap_value['left'] ) ? $gap_value['left'] : $fallback_gap;
		$gap_value  = $gap_row === $gap_column ? $gap_row : $gap_row . ' ' . $gap_column;
	}

	// The unstable gallery gap calculation requires a real value (such as `0px`) and not `0`.
	if ( '0' === $gap_column ) {
		$gap_column = '0px';
	}

	// Set the CSS variable to the column value, and the `gap` property to the combined gap value.
	$gallery_styles = array(
		array(
			'selector'     => ".wp-block-gallery.{$unique_gallery_classname}",
			'declarations' => array(
				'--wp--style--unstable-gallery-gap' => $gap_column,
				'gap'                               => $gap_value,
			),
		),
	);

	wp_style_engine_get_stylesheet_from_css_rules(
		$gallery_styles,
		array(
			'context' => 'block-supports',
		)
	);
	return (string) $processed_content;
}
/**
 * Registers the `core/gallery` block on server.
 */
function register_block_core_gallery() {
	register_block_type_from_metadata(
		__DIR__ . '/gallery',
		array(
			'render_callback' => 'block_core_gallery_render',
		)
	);
}

add_action( 'init', 'register_block_core_gallery' );
