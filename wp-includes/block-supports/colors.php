<?php
/**
 * Colors block support flag.
 *
 * @package WordPress
 * @since 5.6.0
 */

/**
 * Registers the style and colors block attributes for block types that support it.
 *
 * @since 5.6.0
 * @since 6.1.0 Improved $color_support assignment optimization.
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_colors_support( $block_type ) {
	$color_support = false;
	if ( $block_type instanceof WP_Block_Type ) {
		$color_support = $block_type->supports['color'] ?? false;
	}
	$has_text_colors_support       = true === $color_support ||
		( isset( $color_support['text'] ) && $color_support['text'] ) ||
		( is_array( $color_support ) && ! isset( $color_support['text'] ) );
	$has_background_colors_support = true === $color_support ||
		( isset( $color_support['background'] ) && $color_support['background'] ) ||
		( is_array( $color_support ) && ! isset( $color_support['background'] ) );
	$has_gradients_support         = $color_support['gradients'] ?? false;
	$has_link_colors_support       = $color_support['link'] ?? false;
	$has_button_colors_support     = $color_support['button'] ?? false;
	$has_heading_colors_support    = $color_support['heading'] ?? false;
	$has_color_support             = $has_text_colors_support ||
		$has_background_colors_support ||
		$has_gradients_support ||
		$has_link_colors_support ||
		$has_button_colors_support ||
		$has_heading_colors_support;

	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	if ( $has_color_support && ! array_key_exists( 'style', $block_type->attributes ) ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}

	if ( $has_background_colors_support && ! array_key_exists( 'backgroundColor', $block_type->attributes ) ) {
		$block_type->attributes['backgroundColor'] = array(
			'type' => 'string',
		);
	}

	if ( $has_text_colors_support && ! array_key_exists( 'textColor', $block_type->attributes ) ) {
		$block_type->attributes['textColor'] = array(
			'type' => 'string',
		);
	}

	if ( $has_gradients_support && ! array_key_exists( 'gradient', $block_type->attributes ) ) {
		$block_type->attributes['gradient'] = array(
			'type' => 'string',
		);
	}
}


/**
 * Adds CSS classes and inline styles for colors to the incoming attributes array.
 * This will be applied to the block markup in the front-end.
 *
 * @since 5.6.0
 * @since 6.1.0 Implemented the style engine to generate CSS and classnames.
 * @since 7.1.0 Suppresses color.gradient when background.gradient is supported and set.
 * @since 7.2.0 Skips `has-background` for a background color clipped to the text.
 * @access private
 *
 * @param WP_Block_Type        $block_type       Block type.
 * @param array<string, mixed> $block_attributes Block attributes.
 * @return array<string, mixed> Colors CSS classes and inline styles.
 */
function wp_apply_colors_support( WP_Block_Type $block_type, array $block_attributes ): array {
	$color_support = $block_type->supports['color'] ?? false;

	if (
		is_array( $color_support ) &&
		wp_should_skip_block_supports_serialization( $block_type, 'color' )
	) {
		return array();
	}

	$has_text_colors_support       = true === $color_support ||
		( isset( $color_support['text'] ) && $color_support['text'] ) ||
		( is_array( $color_support ) && ! isset( $color_support['text'] ) );
	$has_background_colors_support = true === $color_support ||
		( isset( $color_support['background'] ) && $color_support['background'] ) ||
		( is_array( $color_support ) && ! isset( $color_support['background'] ) );
	$has_gradients_support         = $color_support['gradients'] ?? false;
	$skip_text                     = wp_should_skip_block_supports_serialization( $block_type, 'color', 'text' );
	$skip_background               = wp_should_skip_block_supports_serialization( $block_type, 'color', 'background' );
	$skip_gradients                = wp_should_skip_block_supports_serialization( $block_type, 'color', 'gradients' );

	// background.php owns the CSS when a background gradient is set, so skip color.gradient.
	$has_background_gradient_support = block_has_support( $block_type, array( 'background', 'gradient' ), false );
	$has_background_gradient_value   = ! empty( $block_attributes['style']['background']['gradient'] );
	$has_background_gradient         = $has_background_gradient_support && $has_background_gradient_value;

	/*
	 * Mirror of the background support: a color clipped to the text paints the
	 * glyphs, not the box, so only a gradient still adds `has-background`.
	 * Read before the attributes are filtered for the helper.
	 */
	$clips_to_text = 'text' === ( $block_attributes['style']['background']['backgroundClip'] ?? null ) &&
		block_has_support( $block_type, array( 'background', 'backgroundClip' ), false ) &&
		! wp_should_skip_block_supports_serialization( $block_type, 'background', 'backgroundClip' );

	// The helper reads only `style.color`, so a non-array value counts as no color styles.
	if ( ! isset( $block_attributes['style']['color'] ) || ! is_array( $block_attributes['style']['color'] ) ) {
		unset( $block_attributes['style'] );
	}

	if ( ! $has_text_colors_support || $skip_text ) {
		unset( $block_attributes['textColor'], $block_attributes['style']['color']['text'] );
	}

	if ( ! $has_background_colors_support || $skip_background ) {
		unset( $block_attributes['backgroundColor'], $block_attributes['style']['color']['background'] );
	}

	if ( ! $has_gradients_support || $skip_gradients || $has_background_gradient ) {
		unset( $block_attributes['gradient'], $block_attributes['style']['color']['gradient'] );
	}

	$classes_and_styles = wp_get_color_classes_and_styles( $block_attributes );
	$has_gradient       = ! empty( $block_attributes['gradient'] ) || ! empty( $block_attributes['style']['color']['gradient'] );

	if ( $clips_to_text && ! $has_gradient && ! empty( $classes_and_styles['class'] ) ) {
		$classes = array_diff( explode( ' ', $classes_and_styles['class'] ), array( 'has-background' ) );
		if ( empty( $classes ) ) {
			unset( $classes_and_styles['class'] );
		} else {
			$classes_and_styles['class'] = implode( ' ', $classes );
		}
	}

	return $classes_and_styles;
}

/**
 * Returns color classes and inline styles for block attributes, like the JS
 * `getColorClassesAndStyles()`. Does not check block support or skipped
 * serialization.
 *
 * @since 7.3.0
 *
 * @param array<string, mixed> $block_attributes Block attributes.
 * @return array<string, string> Array with `class` and `style` keys, each present only when non-empty.
 *
 * @phpstan-param array{ style?: array{ color?: array<string, mixed>, ...<string, mixed> }, ...<string, mixed> } $block_attributes
 * @phpstan-return array{ class?: non-empty-string, style?: non-empty-string }
 */
function wp_get_color_classes_and_styles( $block_attributes ) {
	if ( ! is_array( $block_attributes ) ) {
		return array();
	}

	$color_block_styles = array();
	$preset_attributes  = array(
		'text'       => array( 'textColor', 'color' ),
		'background' => array( 'backgroundColor', 'color' ),
		'gradient'   => array( 'gradient', 'gradient' ),
	);

	foreach ( $preset_attributes as $feature => list( $preset_attribute, $preset_type ) ) {
		$preset_slug = $block_attributes[ $preset_attribute ] ?? null;

		// Preset slugs are strings in theme.json and the editor.
		$preset_value                   = is_string( $preset_slug ) && '' !== $preset_slug ? "var:preset|{$preset_type}|{$preset_slug}" : null;
		$custom_value                   = $block_attributes['style']['color'][ $feature ] ?? null;
		$color_block_styles[ $feature ] = $preset_value ?? $custom_value;
	}

	$attributes = array();
	$styles     = wp_style_engine_get_styles( array( 'color' => $color_block_styles ), array( 'convert_vars_to_classnames' => true ) );

	if ( ! empty( $styles['classnames'] ) ) {
		$attributes['class'] = $styles['classnames'];
	}

	if ( ! empty( $styles['css'] ) ) {
		$attributes['style'] = $styles['css'];
	}

	return $attributes;
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'colors',
	array(
		'register_attribute' => 'wp_register_colors_support',
		'apply'              => 'wp_apply_colors_support',
	)
);
