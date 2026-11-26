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

	$has_border_color_support  = wp_has_border_feature_support( $block_type, 'color' );
	$has_border_radius_support = wp_has_border_feature_support( $block_type, 'radius' );
	$has_border_style_support  = wp_has_border_feature_support( $block_type, 'style' );
	$has_border_width_support  = wp_has_border_feature_support( $block_type, 'width' );
	$skip_color                = wp_should_skip_block_supports_serialization( $block_type, '__experimentalBorder', 'color' );
	$skip_radius               = wp_should_skip_block_supports_serialization( $block_type, '__experimentalBorder', 'radius' );
	$skip_style                = wp_should_skip_block_supports_serialization( $block_type, '__experimentalBorder', 'style' );
	$skip_width                = wp_should_skip_block_supports_serialization( $block_type, '__experimentalBorder', 'width' );

	// The unset() calls below fail on a non-array style or border.
	if ( ! isset( $block_attributes['style']['border'] ) || ! is_array( $block_attributes['style']['border'] ) ) {
		unset( $block_attributes['style'] );
	}

	if ( ! $has_border_radius_support || $skip_radius ) {
		unset( $block_attributes['style']['border']['radius'] );
	}

	if ( ! $has_border_style_support || $skip_style ) {
		unset( $block_attributes['style']['border']['style'] );
	}

	if ( ! $has_border_width_support || $skip_width ) {
		unset( $block_attributes['style']['border']['width'] );
	}

	if ( ! $has_border_color_support || $skip_color ) {
		unset( $block_attributes['style']['border']['color'], $block_attributes['borderColor'] );
	}

	// Side values follow skipped serialization only, as on trunk.
	foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
		$has_side = isset( $block_attributes['style']['border'][ $side ] ) && is_array( $block_attributes['style']['border'][ $side ] );

		if ( ! $has_side || ( ! $has_border_color_support && ! $has_border_width_support ) ) {
			unset( $block_attributes['style']['border'][ $side ] );
			continue;
		}

		if ( $skip_width ) {
			unset( $block_attributes['style']['border'][ $side ]['width'] );
		}

		if ( $skip_color ) {
			unset( $block_attributes['style']['border'][ $side ]['color'] );
		}

		if ( $skip_style ) {
			unset( $block_attributes['style']['border'][ $side ]['style'] );
		}
	}

	return wp_get_border_classes_and_styles( $block_attributes );
}

/**
 * Returns border classes and inline styles for block attributes, like the JS
 * `getBorderClassesAndStyles()`. Does not check block support or skipped
 * serialization.
 *
 * @since 7.3.0
 *
 * @param array<string, mixed> $block_attributes Block attributes.
 * @return array<string, string> Array with `class` and `style` keys, each present only when non-empty.
 *
 * @phpstan-param array{ style?: array<string, mixed>, ...<string, mixed> } $block_attributes
 * @phpstan-return array{ class?: non-empty-string, style?: non-empty-string }
 */
function wp_get_border_classes_and_styles( $block_attributes ) {
	if ( ! is_array( $block_attributes ) ) {
		return array();
	}

	$border        = isset( $block_attributes['style']['border'] ) && is_array( $block_attributes['style']['border'] )
		? $block_attributes['style']['border']
		: array();
	$border_styles = array();

	// Unitless radius and width are from the original implementation.
	if ( isset( $border['radius'] ) ) {
		$border_styles['radius'] = is_numeric( $border['radius'] ) ? "{$border['radius']}px" : $border['radius'];
	}

	if ( isset( $border['style'] ) ) {
		$border_styles['style'] = $border['style'];
	}

	if ( isset( $border['width'] ) ) {
		$border_styles['width'] = is_numeric( $border['width'] ) ? "{$border['width']}px" : $border['width'];
	}

	// Preset slugs are strings in theme.json and the editor.
	$preset_slug            = $block_attributes['borderColor'] ?? null;
	$border_styles['color'] = is_string( $preset_slug ) && '' !== $preset_slug ? "var:preset|color|{$preset_slug}" : ( $border['color'] ?? null );

	foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
		if ( ! isset( $border[ $side ] ) || ! is_array( $border[ $side ] ) ) {
			continue;
		}
		$border_styles[ $side ] = array(
			'width' => $border[ $side ]['width'] ?? null,
			'color' => $border[ $side ]['color'] ?? null,
			'style' => $border[ $side ]['style'] ?? null,
		);
	}

	$attributes = array();
	$styles     = wp_style_engine_get_styles( array( 'border' => $border_styles ) );

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
		$block_type_supports_border = $block_type->supports['__experimentalBorder'] ?? $default_value;
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
