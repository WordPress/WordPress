<?php
/**
 * Border block support flag.
 *
 * @package WordPress
 * @since 5.8.0
 */

/**
 * Registers the style attribute used by the border feature if needed for block
 * types that support borders.
 *
 * @since 5.8.0
 * @since 6.1.0 Improved conditional blocks optimization.
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_border_support( $block_type ) {
	// Setup attributes and styles within that if needed.
	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	if ( block_has_support( $block_type, '__experimentalBorder' ) && ! array_key_exists( 'style', $block_type->attributes ) ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}

	if ( wp_has_border_feature_support( $block_type, 'color' ) && ! array_key_exists( 'borderColor', $block_type->attributes ) ) {
		$block_type->attributes['borderColor'] = array(
			'type' => 'string',
		);
	}
}

/**
 * Adds CSS classes and inline styles for border styles to the incoming
 * attributes array. This will be applied to the block markup in the front-end.
 *
 * @since 5.8.0
 * @since 6.1.0 Implemented the style engine to generate CSS and classnames.
 * @access private
 *
 * @param WP_Block_Type $block_type       Block type.
 * @param array         $block_attributes Block attributes.
 * @return array Border CSS classes and inline styles.
 */
function wp_apply_border_support( $block_type, $block_attributes ) {
	if ( wp_should_skip_block_supports_serialization( $block_type, 'border' ) ) {
		return array();
	}

	$border_block_styles      = array();
	$has_border_color_support = wp_has_border_feature_support( $block_type, 'color' );
	$has_border_width_support = wp_has_border_feature_support( $block_type, 'width' );

	// Border radius.
	if (
		wp_has_border_feature_support( $block_type, 'radius' ) &&
		isset( $block_attributes['style']['border']['radius'] ) &&
		! wp_should_skip_block_supports_serialization( $block_type, '__experimentalBorder', 'radius' )
	) {
		$border_radius = $block_attributes['style']['border']['radius'];

		if ( is_numeric( $border_radius ) ) {
			$border_radius .= 'px';
		}

		$border_block_styles['radius'] = $border_radius;
	}

	// Border style.
	if (
		wp_has_border_feature_support( $block_type, 'style' ) &&
		isset( $block_attributes['style']['border']['style'] ) &&
		! wp_should_skip_block_supports_serialization( $block_type, '__experimentalBorder', 'style' )
	) {
		$border_block_styles['style'] = $block_attributes['style']['border']['style'];
	}

	// Border width.
	if (
		$has_border_width_support &&
		isset( $block_attributes['style']['border']['width'] ) &&
		! wp_should_skip_block_supports_serialization( $block_type, '__experimentalBorder', 'width' )
	) {
		$border_width = $block_attributes['style']['border']['width'];

		// This check handles original unitless implementation.
		if ( is_numeric( $border_width ) ) {
			$border_width .= 'px';
		}

		$border_block_styles['width'] = $border_width;
	}

	// Border color.
	if (
		$has_border_color_support &&
		! wp_should_skip_block_supports_serialization( $block_type, '__experimentalBorder', 'color' )
	) {
		$preset_border_color          = array_key_exists( 'borderColor', $block_attributes ) ? "var:preset|color|{$block_attributes['borderColor']}" : null;
		$custom_border_color          = isset( $block_attributes['style']['border']['color'] ) ? $block_attributes['style']['border']['color'] : null;
		$border_block_styles['color'] = $preset_border_color ? $preset_border_color : $custom_border_color;
	}

	// Generates styles for individual border sides.
	if ( $has_border_color_support || $has_border_width_support ) {
		foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
			$border                       = isset( $block_attributes['style']['border'][ $side ] ) ? $block_attributes['style']['border'][ $side ] : null;
			$border_side_values           = array(
				'width' => isset( $border['width'] ) && ! wp_should_skip_block_supports_serialization( $block_type, '__experimentalBorder', 'width' ) ? $border['width'] : null,
				'color' => isset( $border['color'] ) && ! wp_should_skip_block_supports_serialization( $block_type, '__experimentalBorder', 'color' ) ? $border['color'] : null,
				'style' => isset( $border['style'] ) && ! wp_should_skip_block_supports_serialization( $block_type, '__experimentalBorder', 'style' ) ? $border['style'] : null,
			);
			$border_block_styles[ $side ] = $border_side_values;
		}
	}

	// Collect classes and styles.
	$attributes = array();
	$styles     = wp_style_engine_get_styles( array( 'border' => $border_block_styles ) );

	if ( ! empty( $styles['classnames'] ) ) {
		$attributes['class'] = $styles['classnames'];
	}

	if ( ! empty( $styles['css'] ) ) {
		$attributes['style'] = $styles['css'];
	}

	return $attributes;
}

/**
 * Checks whether the current block type supports the border feature requested.
 *
 * If the `__experimentalBorder` support flag is a boolean `true` all border
 * support features are available. Otherwise, the specific feature's support
 * flag nested under `experimentalBorder` must be enabled for the feature
 * to be opted into.
 *
 * @since 5.8.0
 * @access private
 *
 * @param WP_Block_Type $block_type    Block type to check for support.
 * @param string        $feature       Name of the feature to check support for.
 * @param mixed         $default_value Fallback value for feature support, defaults to false.
 * @return bool Whether the feature is supported.
 */
function wp_has_border_feature_support( $block_type, $feature, $default_value = false ) {
	// Check if all border support features have been opted into via `"__experimentalBorder": true`.
	if ( $block_type instanceof WP_Block_Type ) {
		$block_type_supports_border = isset( $block_type->supports['__experimentalBorder'] )
			? $block_type->supports['__experimentalBorder']
			: $default_value;
		if ( true === $block_type_supports_border ) {
			return true;
		}
	}

	// Check if the specific feature has been opted into individually
	// via nested flag under `__experimentalBorder`.
	return block_has_support( $block_type, array( '__experimentalBorder', $feature ), $default_value );
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'border',
	array(
		'register_attribute' => 'wp_register_border_support',
		'apply'              => 'wp_apply_border_support',
	)
);
