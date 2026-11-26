<?php
/**
 * Spacing block support flag.
 *
 * For backwards compatibility, this remains separate to the dimensions.php
 * block support despite both belonging under a single panel in the editor.
 *
 * @package WordPress
 * @since 5.8.0
 */

/**
 * Registers the style block attribute for block types that support it.
 *
 * @since 5.8.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_spacing_support( $block_type ) {
	$has_spacing_support = block_has_support( $block_type, 'spacing', false );

	// Setup attributes and styles within that if needed.
	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	if ( $has_spacing_support && ! array_key_exists( 'style', $block_type->attributes ) ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}
}

/**
 * Adds CSS classes for block spacing to the incoming attributes array.
 * This will be applied to the block markup in the front-end.
 *
 * @since 5.8.0
 * @since 6.1.0 Implemented the style engine to generate CSS and classnames.
 * @access private
 *
 * @param WP_Block_Type $block_type       Block Type.
 * @param array         $block_attributes Block attributes.
 * @return array Block spacing CSS classes and inline styles.
 */
function wp_apply_spacing_support( $block_type, $block_attributes ) {
	if ( wp_should_skip_block_supports_serialization( $block_type, 'spacing' ) ) {
		return array();
	}

	if ( ! isset( $block_attributes['style']['spacing'] ) || ! is_array( $block_attributes['style']['spacing'] ) ) {
		return array();
	}

	$has_padding_support = block_has_support( $block_type, array( 'spacing', 'padding' ), false );
	$has_margin_support  = block_has_support( $block_type, array( 'spacing', 'margin' ), false );
	$skip_padding        = wp_should_skip_block_supports_serialization( $block_type, 'spacing', 'padding' );
	$skip_margin         = wp_should_skip_block_supports_serialization( $block_type, 'spacing', 'margin' );

	if ( ! $has_padding_support || $skip_padding ) {
		unset( $block_attributes['style']['spacing']['padding'] );
	}

	if ( ! $has_margin_support || $skip_margin ) {
		unset( $block_attributes['style']['spacing']['margin'] );
	}

	return wp_get_spacing_classes_and_styles( $block_attributes );
}

/**
 * Returns spacing classes and inline styles for block attributes, like the JS
 * `getSpacingClassesAndStyles()`. Does not check block support or skipped
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
function wp_get_spacing_classes_and_styles( $block_attributes ) {
	if ( ! is_array( $block_attributes ) ) {
		return array();
	}

	$spacing = $block_attributes['style']['spacing'] ?? null;
	if ( ! is_array( $spacing ) ) {
		return array();
	}

	$styles     = wp_style_engine_get_styles(
		array(
			'spacing' => array(
				'padding' => $spacing['padding'] ?? null,
				'margin'  => $spacing['margin'] ?? null,
			),
		)
	);
	$attributes = array();

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
	'spacing',
	array(
		'register_attribute' => 'wp_register_spacing_support',
		'apply'              => 'wp_apply_spacing_support',
	)
);
