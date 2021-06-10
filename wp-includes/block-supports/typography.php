<?php
/**
 * Typography block support flag.
 *
 * @package WordPress
 * @since 5.6.0
 */

/**
 * Registers the style and typography block attributes for block types that support it.
 *
 * @since 5.6.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_typography_support( $block_type ) {
	if ( ! property_exists( $block_type, 'supports' ) ) {
		return;
	}

	$typography_supports = _wp_array_get( $block_type->supports, array( 'typography' ), false );
	if ( ! $typography_supports ) {
		return;
	}

	$has_font_family_support     = _wp_array_get( $typography_supports, array( '__experimentalFontFamily' ), false );
	$has_font_size_support       = _wp_array_get( $typography_supports, array( 'fontSize' ), false );
	$has_font_style_support      = _wp_array_get( $typography_supports, array( '__experimentalFontStyle' ), false );
	$has_font_weight_support     = _wp_array_get( $typography_supports, array( '__experimentalFontWeight' ), false );
	$has_line_height_support     = _wp_array_get( $typography_supports, array( 'lineHeight' ), false );
	$has_text_decoration_support = _wp_array_get( $typography_supports, array( '__experimentalTextDecoration' ), false );
	$has_text_transform_support  = _wp_array_get( $typography_supports, array( '__experimentalTextTransform' ), false );

	$has_typography_support = $has_font_family_support
		|| $has_font_size_support
		|| $has_font_style_support
		|| $has_font_weight_support
		|| $has_line_height_support
		|| $has_text_decoration_support
		|| $has_text_transform_support;

	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	if ( $has_typography_support && ! array_key_exists( 'style', $block_type->attributes ) ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}

	if ( $has_font_size_support && ! array_key_exists( 'fontSize', $block_type->attributes ) ) {
		$block_type->attributes['fontSize'] = array(
			'type' => 'string',
		);
	}
}

/**
 * Add CSS classes and inline styles for typography features such as font sizes
 * to the incoming attributes array. This will be applied to the block markup in
 * the front-end.
 *
 * @since 5.6.0
 * @access private
 *
 * @param  WP_Block_Type $block_type       Block type.
 * @param  array         $block_attributes Block attributes.
 *
 * @return array Typography CSS classes and inline styles.
 */
function wp_apply_typography_support( $block_type, $block_attributes ) {
	if ( ! property_exists( $block_type, 'supports' ) ) {
		return array();
	}

	$typography_supports = _wp_array_get( $block_type->supports, array( 'typography' ), false );
	if ( ! $typography_supports ) {
		return array();
	}

	$skip_typography_serialization = _wp_array_get( $typography_supports, array( '__experimentalSkipSerialization' ), false );
	if ( $skip_typography_serialization ) {
		return array();
	}

	$attributes = array();
	$classes    = array();
	$styles     = array();

	$has_font_family_support     = _wp_array_get( $typography_supports, array( '__experimentalFontFamily' ), false );
	$has_font_size_support       = _wp_array_get( $typography_supports, array( 'fontSize' ), false );
	$has_font_style_support      = _wp_array_get( $typography_supports, array( '__experimentalFontStyle' ), false );
	$has_font_weight_support     = _wp_array_get( $typography_supports, array( '__experimentalFontWeight' ), false );
	$has_line_height_support     = _wp_array_get( $typography_supports, array( 'lineHeight' ), false );
	$has_text_decoration_support = _wp_array_get( $typography_supports, array( '__experimentalTextDecoration' ), false );
	$has_text_transform_support  = _wp_array_get( $typography_supports, array( '__experimentalTextTransform' ), false );

	if ( $has_font_size_support ) {
		$has_named_font_size  = array_key_exists( 'fontSize', $block_attributes );
		$has_custom_font_size = isset( $block_attributes['style']['typography']['fontSize'] );

		if ( $has_named_font_size ) {
			$classes[] = sprintf( 'has-%s-font-size', $block_attributes['fontSize'] );
		} elseif ( $has_custom_font_size ) {
			$styles[] = sprintf( 'font-size: %s;', $block_attributes['style']['typography']['fontSize'] );
		}
	}

	if ( $has_font_family_support ) {
		$has_font_family = isset( $block_attributes['style']['typography']['fontFamily'] );
		if ( $has_font_family ) {
			$font_family = $block_attributes['style']['typography']['fontFamily'];
			if ( strpos( $font_family, 'var:preset|font-family' ) !== false ) {
				// Get the name from the string and add proper styles.
				$index_to_splice  = strrpos( $font_family, '|' ) + 1;
				$font_family_name = substr( $font_family, $index_to_splice );
				$styles[]         = sprintf( 'font-family: var(--wp--preset--font-family--%s);', $font_family_name );
			} else {
				$styles[] = sprintf( 'font-family: %s;', $block_attributes['style']['typography']['fontFamily'] );
			}
		}
	}

	if ( $has_font_style_support ) {
		$font_style = wp_typography_get_css_variable_inline_style( $block_attributes, 'fontStyle', 'font-style' );
		if ( $font_style ) {
			$styles[] = $font_style;
		}
	}

	if ( $has_font_weight_support ) {
		$font_weight = wp_typography_get_css_variable_inline_style( $block_attributes, 'fontWeight', 'font-weight' );
		if ( $font_weight ) {
			$styles[] = $font_weight;
		}
	}

	if ( $has_line_height_support ) {
		$has_line_height = isset( $block_attributes['style']['typography']['lineHeight'] );
		if ( $has_line_height ) {
			$styles[] = sprintf( 'line-height: %s;', $block_attributes['style']['typography']['lineHeight'] );
		}
	}

	if ( $has_text_decoration_support ) {
		$text_decoration_style = wp_typography_get_css_variable_inline_style( $block_attributes, 'textDecoration', 'text-decoration' );
		if ( $text_decoration_style ) {
			$styles[] = $text_decoration_style;
		}
	}

	if ( $has_text_transform_support ) {
		$text_transform_style = wp_typography_get_css_variable_inline_style( $block_attributes, 'textTransform', 'text-transform' );
		if ( $text_transform_style ) {
			$styles[] = $text_transform_style;
		}
	}

	if ( ! empty( $classes ) ) {
		$attributes['class'] = implode( ' ', $classes );
	}
	if ( ! empty( $styles ) ) {
		$attributes['style'] = implode( ' ', $styles );
	}

	return $attributes;
}

/**
 * Generates an inline style for a typography feature e.g. text decoration,
 * text transform, and font style.
 *
 * @since 5.8.0
 * @access private
 *
 * @param array  $attributes   Block's attributes.
 * @param string $feature      Key for the feature within the typography styles.
 * @param string $css_property Slug for the CSS property the inline style sets.
 *
 * @return string              CSS inline style.
 */
function wp_typography_get_css_variable_inline_style( $attributes, $feature, $css_property ) {
	// Retrieve current attribute value or skip if not found.
	$style_value = _wp_array_get( $attributes, array( 'style', 'typography', $feature ), false );
	if ( ! $style_value ) {
		return;
	}

	// If we don't have a preset CSS variable, we'll assume it's a regular CSS value.
	if ( strpos( $style_value, "var:preset|{$css_property}|" ) === false ) {
		return sprintf( '%s:%s;', $css_property, $style_value );
	}

	// We have a preset CSS variable as the style.
	// Get the style value from the string and return CSS style.
	$index_to_splice = strrpos( $style_value, '|' ) + 1;
	$slug            = substr( $style_value, $index_to_splice );

	// Return the actual CSS inline style e.g. `text-decoration:var(--wp--preset--text-decoration--underline);`.
	return sprintf( '%s:var(--wp--preset--%s--%s);', $css_property, $css_property, $slug );
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'typography',
	array(
		'register_attribute' => 'wp_register_typography_support',
		'apply'              => 'wp_apply_typography_support',
	)
);
