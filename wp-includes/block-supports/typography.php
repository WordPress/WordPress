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
	$has_letter_spacing_support  = _wp_array_get( $typography_supports, array( '__experimentalLetterSpacing' ), false );
	$has_line_height_support     = _wp_array_get( $typography_supports, array( 'lineHeight' ), false );
	$has_text_decoration_support = _wp_array_get( $typography_supports, array( '__experimentalTextDecoration' ), false );
	$has_text_transform_support  = _wp_array_get( $typography_supports, array( '__experimentalTextTransform' ), false );

	$has_typography_support = $has_font_family_support
		|| $has_font_size_support
		|| $has_font_style_support
		|| $has_font_weight_support
		|| $has_letter_spacing_support
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

	if ( $has_font_family_support && ! array_key_exists( 'fontFamily', $block_type->attributes ) ) {
		$block_type->attributes['fontFamily'] = array(
			'type' => 'string',
		);
	}
}

/**
 * Adds CSS classes and inline styles for typography features such as font sizes
 * to the incoming attributes array. This will be applied to the block markup in
 * the front-end.
 *
 * @since 5.6.0
 * @since 6.1.0 Used the style engine to generate CSS and classnames.
 * @access private
 *
 * @param WP_Block_Type $block_type       Block type.
 * @param array         $block_attributes Block attributes.
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

	if ( wp_should_skip_block_supports_serialization( $block_type, 'typography' ) ) {
		return array();
	}

	$has_font_family_support     = _wp_array_get( $typography_supports, array( '__experimentalFontFamily' ), false );
	$has_font_size_support       = _wp_array_get( $typography_supports, array( 'fontSize' ), false );
	$has_font_style_support      = _wp_array_get( $typography_supports, array( '__experimentalFontStyle' ), false );
	$has_font_weight_support     = _wp_array_get( $typography_supports, array( '__experimentalFontWeight' ), false );
	$has_letter_spacing_support  = _wp_array_get( $typography_supports, array( '__experimentalLetterSpacing' ), false );
	$has_line_height_support     = _wp_array_get( $typography_supports, array( 'lineHeight' ), false );
	$has_text_decoration_support = _wp_array_get( $typography_supports, array( '__experimentalTextDecoration' ), false );
	$has_text_transform_support  = _wp_array_get( $typography_supports, array( '__experimentalTextTransform' ), false );

	// Whether to skip individual block support features.
	$should_skip_font_size       = wp_should_skip_block_supports_serialization( $block_type, 'typography', 'fontSize' );
	$should_skip_font_family     = wp_should_skip_block_supports_serialization( $block_type, 'typography', 'fontFamily' );
	$should_skip_font_style      = wp_should_skip_block_supports_serialization( $block_type, 'typography', 'fontStyle' );
	$should_skip_font_weight     = wp_should_skip_block_supports_serialization( $block_type, 'typography', 'fontWeight' );
	$should_skip_line_height     = wp_should_skip_block_supports_serialization( $block_type, 'typography', 'lineHeight' );
	$should_skip_text_decoration = wp_should_skip_block_supports_serialization( $block_type, 'typography', 'textDecoration' );
	$should_skip_text_transform  = wp_should_skip_block_supports_serialization( $block_type, 'typography', 'textTransform' );
	$should_skip_letter_spacing  = wp_should_skip_block_supports_serialization( $block_type, 'typography', 'letterSpacing' );

	$typography_block_styles = array();
	if ( $has_font_size_support && ! $should_skip_font_size ) {
		$preset_font_size                    = array_key_exists( 'fontSize', $block_attributes )
			? "var:preset|font-size|{$block_attributes['fontSize']}"
			: null;
		$custom_font_size                    = isset( $block_attributes['style']['typography']['fontSize'] )
			? $block_attributes['style']['typography']['fontSize']
			: null;
		$typography_block_styles['fontSize'] = $preset_font_size ? $preset_font_size : wp_get_typography_font_size_value(
			array(
				'size' => $custom_font_size,
			)
		);
	}

	if ( $has_font_family_support && ! $should_skip_font_family ) {
		$preset_font_family                    = array_key_exists( 'fontFamily', $block_attributes )
			? "var:preset|font-family|{$block_attributes['fontFamily']}"
			: null;
		$custom_font_family                    = isset( $block_attributes['style']['typography']['fontFamily'] )
			? wp_typography_get_preset_inline_style_value( $block_attributes['style']['typography']['fontFamily'], 'font-family' )
			: null;
		$typography_block_styles['fontFamily'] = $preset_font_family ? $preset_font_family : $custom_font_family;
	}

	if (
		$has_font_style_support &&
		! $should_skip_font_style &&
		isset( $block_attributes['style']['typography']['fontStyle'] )
	) {
		$typography_block_styles['fontStyle'] = wp_typography_get_preset_inline_style_value(
			$block_attributes['style']['typography']['fontStyle'],
			'font-style'
		);
	}

	if (
		$has_font_weight_support &&
		! $should_skip_font_weight &&
		isset( $block_attributes['style']['typography']['fontWeight'] )
	) {
		$typography_block_styles['fontWeight'] = wp_typography_get_preset_inline_style_value(
			$block_attributes['style']['typography']['fontWeight'],
			'font-weight'
		);
	}

	if ( $has_line_height_support && ! $should_skip_line_height ) {
		$typography_block_styles['lineHeight'] = _wp_array_get( $block_attributes, array( 'style', 'typography', 'lineHeight' ) );
	}

	if (
		$has_text_decoration_support &&
		! $should_skip_text_decoration &&
		isset( $block_attributes['style']['typography']['textDecoration'] )
	) {
		$typography_block_styles['textDecoration'] = wp_typography_get_preset_inline_style_value(
			$block_attributes['style']['typography']['textDecoration'],
			'text-decoration'
		);
	}

	if (
		$has_text_transform_support &&
		! $should_skip_text_transform &&
		isset( $block_attributes['style']['typography']['textTransform'] )
	) {
		$typography_block_styles['textTransform'] = wp_typography_get_preset_inline_style_value(
			$block_attributes['style']['typography']['textTransform'],
			'text-transform'
		);
	}

	if (
		$has_letter_spacing_support &&
		! $should_skip_letter_spacing &&
		isset( $block_attributes['style']['typography']['letterSpacing'] )
	) {
		$typography_block_styles['letterSpacing'] = wp_typography_get_preset_inline_style_value(
			$block_attributes['style']['typography']['letterSpacing'],
			'letter-spacing'
		);
	}

	$attributes = array();
	$styles     = wp_style_engine_get_styles(
		array( 'typography' => $typography_block_styles ),
		array( 'convert_vars_to_classnames' => true )
	);

	if ( ! empty( $styles['classnames'] ) ) {
		$attributes['class'] = $styles['classnames'];
	}

	if ( ! empty( $styles['css'] ) ) {
		$attributes['style'] = $styles['css'];
	}

	return $attributes;
}

/**
 * Generates an inline style value for a typography feature e.g. text decoration,
 * text transform, and font style.
 *
 * Note: This function is for backwards compatibility.
 * * It is necessary to parse older blocks whose typography styles contain presets.
 * * It mostly replaces the deprecated `wp_typography_get_css_variable_inline_style()`,
 *   but skips compiling a CSS declaration as the style engine takes over this role.
 * @link https://github.com/wordpress/gutenberg/pull/27555
 *
 * @since 6.1.0
 *
 * @param string $style_value  A raw style value for a single typography feature from a block's style attribute.
 * @param string $css_property Slug for the CSS property the inline style sets.
 * @return string A CSS inline style value.
 */
function wp_typography_get_preset_inline_style_value( $style_value, $css_property ) {
	// If the style value is not a preset CSS variable go no further.
	if ( empty( $style_value ) || ! str_contains( $style_value, "var:preset|{$css_property}|" ) ) {
		return $style_value;
	}

	/*
	 * For backwards compatibility.
	 * Presets were removed in WordPress/gutenberg#27555.
	 * A preset CSS variable is the style.
	 * Gets the style value from the string and return CSS style.
	 */
	$index_to_splice = strrpos( $style_value, '|' ) + 1;
	$slug            = _wp_to_kebab_case( substr( $style_value, $index_to_splice ) );

	// Return the actual CSS inline style value,
	// e.g. `var(--wp--preset--text-decoration--underline);`.
	return sprintf( 'var(--wp--preset--%s--%s);', $css_property, $slug );
}

/**
 * Renders typography styles/content to the block wrapper.
 *
 * @since 6.1.0
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 */
function wp_render_typography_support( $block_content, $block ) {
	if ( ! isset( $block['attrs']['style']['typography']['fontSize'] ) ) {
		return $block_content;
	}

	$custom_font_size = $block['attrs']['style']['typography']['fontSize'];
	$fluid_font_size  = wp_get_typography_font_size_value( array( 'size' => $custom_font_size ) );

	/*
	 * Checks that $fluid_font_size does not match $custom_font_size,
	 * which means it's been mutated by the fluid font size functions.
	 */
	if ( ! empty( $fluid_font_size ) && $fluid_font_size !== $custom_font_size ) {
		// Replaces the first instance of `font-size:$custom_font_size` with `font-size:$fluid_font_size`.
		return preg_replace( '/font-size\s*:\s*' . preg_quote( $custom_font_size, '/' ) . '\s*;?/', 'font-size:' . esc_attr( $fluid_font_size ) . ';', $block_content, 1 );
	}

	return $block_content;
}

/**
 * Checks a string for a unit and value and returns an array
 * consisting of `'value'` and `'unit'`, e.g. array( '42', 'rem' ).
 *
 * @since 6.1.0
 *
 * @param string|int|float $raw_value Raw size value from theme.json.
 * @param array            $options   {
 *     Optional. An associative array of options. Default is empty array.
 *
 *     @type string   $coerce_to        Coerce the value to rem or px. Default `'rem'`.
 *     @type int      $root_size_value  Value of root font size for rem|em <-> px conversion. Default `16`.
 *     @type string[] $acceptable_units An array of font size units. Default `array( 'rem', 'px', 'em' )`;
 * }
 * @return array|null An array consisting of `'value'` and `'unit'` properties on success.
 *                    `null` on failure.
 */
function wp_get_typography_value_and_unit( $raw_value, $options = array() ) {
	if ( ! is_string( $raw_value ) && ! is_int( $raw_value ) && ! is_float( $raw_value ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			__( 'Raw size value must be a string, integer, or float.' ),
			'6.1.0'
		);
		return null;
	}

	if ( empty( $raw_value ) ) {
		return null;
	}

	// Converts numbers to pixel values by default.
	if ( is_numeric( $raw_value ) ) {
		$raw_value = $raw_value . 'px';
	}

	$defaults = array(
		'coerce_to'        => '',
		'root_size_value'  => 16,
		'acceptable_units' => array( 'rem', 'px', 'em' ),
	);

	$options = wp_parse_args( $options, $defaults );

	$acceptable_units_group = implode( '|', $options['acceptable_units'] );
	$pattern                = '/^(\d*\.?\d+)(' . $acceptable_units_group . '){1,1}$/';

	preg_match( $pattern, $raw_value, $matches );

	// Bails out if not a number value and a px or rem unit.
	if ( ! isset( $matches[1] ) || ! isset( $matches[2] ) ) {
		return null;
	}

	$value = $matches[1];
	$unit  = $matches[2];

	/*
	 * Default browser font size. Later, possibly could inject some JS to
	 * compute this `getComputedStyle( document.querySelector( "html" ) ).fontSize`.
	 */
	if ( 'px' === $options['coerce_to'] && ( 'em' === $unit || 'rem' === $unit ) ) {
		$value = $value * $options['root_size_value'];
		$unit  = $options['coerce_to'];
	}

	if ( 'px' === $unit && ( 'em' === $options['coerce_to'] || 'rem' === $options['coerce_to'] ) ) {
		$value = $value / $options['root_size_value'];
		$unit  = $options['coerce_to'];
	}

	/*
	 * No calculation is required if swapping between em and rem yet,
	 * since we assume a root size value. Later we might like to differentiate between
	 * :root font size (rem) and parent element font size (em) relativity.
	 */
	if ( ( 'em' === $options['coerce_to'] || 'rem' === $options['coerce_to'] ) && ( 'em' === $unit || 'rem' === $unit ) ) {
		$unit = $options['coerce_to'];
	}

	return array(
		'value' => round( $value, 3 ),
		'unit'  => $unit,
	);
}

/**
 * Internal implementation of CSS clamp() based on available min/max viewport
 * width and min/max font sizes.
 *
 * @since 6.1.0
 * @access private
 *
 * @param array $args {
 *     Optional. An associative array of values to calculate a fluid formula
 *     for font size. Default is empty array.
 *
 *     @type string $maximum_viewport_width Maximum size up to which type will have fluidity.
 *     @type string $minimum_viewport_width Minimum viewport size from which type will have fluidity.
 *     @type string $maximum_font_size      Maximum font size for any clamp() calculation.
 *     @type string $minimum_font_size      Minimum font size for any clamp() calculation.
 *     @type int    $scale_factor           A scale factor to determine how fast a font scales within boundaries.
 * }
 * @return string|null A font-size value using clamp() on success, otherwise null.
 */
function wp_get_computed_fluid_typography_value( $args = array() ) {
	$maximum_viewport_width_raw = isset( $args['maximum_viewport_width'] ) ? $args['maximum_viewport_width'] : null;
	$minimum_viewport_width_raw = isset( $args['minimum_viewport_width'] ) ? $args['minimum_viewport_width'] : null;
	$maximum_font_size_raw      = isset( $args['maximum_font_size'] ) ? $args['maximum_font_size'] : null;
	$minimum_font_size_raw      = isset( $args['minimum_font_size'] ) ? $args['minimum_font_size'] : null;
	$scale_factor               = isset( $args['scale_factor'] ) ? $args['scale_factor'] : null;

	// Normalizes the minimum font size in order to use the value for calculations.
	$minimum_font_size = wp_get_typography_value_and_unit( $minimum_font_size_raw );

	/*
	 * We get a 'preferred' unit to keep units consistent when calculating,
	 * otherwise the result will not be accurate.
	 */
	$font_size_unit = isset( $minimum_font_size['unit'] ) ? $minimum_font_size['unit'] : 'rem';

	// Normalizes the maximum font size in order to use the value for calculations.
	$maximum_font_size = wp_get_typography_value_and_unit(
		$maximum_font_size_raw,
		array(
			'coerce_to' => $font_size_unit,
		)
	);

	// Checks for mandatory min and max sizes, and protects against unsupported units.
	if ( ! $maximum_font_size || ! $minimum_font_size ) {
		return null;
	}

	// Uses rem for accessible fluid target font scaling.
	$minimum_font_size_rem = wp_get_typography_value_and_unit(
		$minimum_font_size_raw,
		array(
			'coerce_to' => 'rem',
		)
	);

	// Viewport widths defined for fluid typography. Normalize units.
	$maximum_viewport_width = wp_get_typography_value_and_unit(
		$maximum_viewport_width_raw,
		array(
			'coerce_to' => $font_size_unit,
		)
	);
	$minimum_viewport_width = wp_get_typography_value_and_unit(
		$minimum_viewport_width_raw,
		array(
			'coerce_to' => $font_size_unit,
		)
	);

	/*
	 * Build CSS rule.
	 * Borrowed from https://websemantics.uk/tools/responsive-font-calculator/.
	 */
	$view_port_width_offset = round( $minimum_viewport_width['value'] / 100, 3 ) . $font_size_unit;
	$linear_factor          = 100 * ( ( $maximum_font_size['value'] - $minimum_font_size['value'] ) / ( $maximum_viewport_width['value'] - $minimum_viewport_width['value'] ) );
	$linear_factor_scaled   = round( $linear_factor * $scale_factor, 3 );
	$linear_factor_scaled   = empty( $linear_factor_scaled ) ? 1 : $linear_factor_scaled;
	$fluid_target_font_size = implode( '', $minimum_font_size_rem ) . " + ((1vw - $view_port_width_offset) * $linear_factor_scaled)";

	return "clamp($minimum_font_size_raw, $fluid_target_font_size, $maximum_font_size_raw)";
}

/**
 * Returns a font-size value based on a given font-size preset.
 * Takes into account fluid typography parameters and attempts to return a CSS
 * formula depending on available, valid values.
 *
 * @since 6.1.0
 * @since 6.1.1 Adjusted rules for min and max font sizes.
 * @since 6.2.0 Added 'settings.typography.fluid.minFontSize' support.
 *
 * @param array $preset                     {
 *     Required. fontSizes preset value as seen in theme.json.
 *
 *     @type string           $name Name of the font size preset.
 *     @type string           $slug Kebab-case, unique identifier for the font size preset.
 *     @type string|int|float $size CSS font-size value, including units if applicable.
 * }
 * @param bool  $should_use_fluid_typography An override to switch fluid typography "on". Can be used for unit testing.
 *                                           Default is false.
 * @return string|null Font-size value or null if a size is not passed in $preset.
 */
function wp_get_typography_font_size_value( $preset, $should_use_fluid_typography = false ) {
	if ( ! isset( $preset['size'] ) ) {
		return null;
	}

	/*
	 * Catches empty values and 0/'0'.
	 * Fluid calculations cannot be performed on 0.
	 */
	if ( empty( $preset['size'] ) ) {
		return $preset['size'];
	}

	// Checks if fluid font sizes are activated.
	$typography_settings = wp_get_global_settings( array( 'typography' ) );
	if (
		isset( $typography_settings['fluid'] ) &&
		( true === $typography_settings['fluid'] || is_array( $typography_settings['fluid'] ) )
	) {
		$should_use_fluid_typography = true;
	}

	if ( ! $should_use_fluid_typography ) {
		return $preset['size'];
	}

	$fluid_settings = isset( $typography_settings['fluid'] ) && is_array( $typography_settings['fluid'] )
		? $typography_settings['fluid']
		: array();

	// Defaults.
	$default_maximum_viewport_width   = '1600px';
	$default_minimum_viewport_width   = '768px';
	$default_minimum_font_size_factor = 0.75;
	$default_scale_factor             = 1;
	$has_min_font_size                = isset( $fluid_settings['minFontSize'] ) && ! empty( wp_get_typography_value_and_unit( $fluid_settings['minFontSize'] ) );
	$default_minimum_font_size_limit  = $has_min_font_size ? $fluid_settings['minFontSize'] : '14px';

	// Font sizes.
	$fluid_font_size_settings = isset( $preset['fluid'] ) ? $preset['fluid'] : null;

	// A font size has explicitly bypassed fluid calculations.
	if ( false === $fluid_font_size_settings ) {
		return $preset['size'];
	}

	// Try to grab explicit min and max fluid font sizes.
	$minimum_font_size_raw = isset( $fluid_font_size_settings['min'] ) ? $fluid_font_size_settings['min'] : null;
	$maximum_font_size_raw = isset( $fluid_font_size_settings['max'] ) ? $fluid_font_size_settings['max'] : null;

	// Font sizes.
	$preferred_size = wp_get_typography_value_and_unit( $preset['size'] );

	// Protects against unsupported units.
	if ( empty( $preferred_size['unit'] ) ) {
		return $preset['size'];
	}

	/*
	 * Normalizes the minimum font size limit according to the incoming unit,
	 * in order to perform comparative checks.
	 */
	$minimum_font_size_limit = wp_get_typography_value_and_unit(
		$default_minimum_font_size_limit,
		array(
			'coerce_to' => $preferred_size['unit'],
		)
	);

	// Don't enforce minimum font size if a font size has explicitly set a min and max value.
	if ( ! empty( $minimum_font_size_limit ) && ( ! $minimum_font_size_raw && ! $maximum_font_size_raw ) ) {
		/*
		 * If a minimum size was not passed to this function
		 * and the user-defined font size is lower than $minimum_font_size_limit,
		 * do not calculate a fluid value.
		 */
		if ( $preferred_size['value'] <= $minimum_font_size_limit['value'] ) {
			return $preset['size'];
		}
	}

	// If no fluid max font size is available use the incoming value.
	if ( ! $maximum_font_size_raw ) {
		$maximum_font_size_raw = $preferred_size['value'] . $preferred_size['unit'];
	}

	/*
	 * If no minimumFontSize is provided, create one using
	 * the given font size multiplied by the min font size scale factor.
	 */
	if ( ! $minimum_font_size_raw ) {
		$calculated_minimum_font_size = round(
			$preferred_size['value'] * $default_minimum_font_size_factor,
			3
		);

		// Only use calculated min font size if it's > $minimum_font_size_limit value.
		if ( ! empty( $minimum_font_size_limit ) && $calculated_minimum_font_size <= $minimum_font_size_limit['value'] ) {
			$minimum_font_size_raw = $minimum_font_size_limit['value'] . $minimum_font_size_limit['unit'];
		} else {
			$minimum_font_size_raw = $calculated_minimum_font_size . $preferred_size['unit'];
		}
	}

	$fluid_font_size_value = wp_get_computed_fluid_typography_value(
		array(
			'minimum_viewport_width' => $default_minimum_viewport_width,
			'maximum_viewport_width' => $default_maximum_viewport_width,
			'minimum_font_size'      => $minimum_font_size_raw,
			'maximum_font_size'      => $maximum_font_size_raw,
			'scale_factor'           => $default_scale_factor,
		)
	);

	if ( ! empty( $fluid_font_size_value ) ) {
		return $fluid_font_size_value;
	}

	return $preset['size'];
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'typography',
	array(
		'register_attribute' => 'wp_register_typography_support',
		'apply'              => 'wp_apply_typography_support',
	)
);
