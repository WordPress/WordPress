<?php
/**
 * Shadow block support flag.
 *
 * @package WordPress
 * @since 6.3.0
 */

/**
 * Registers the style and shadow block attributes for block types that support it.
 *
 * @since 6.3.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_shadow_support( $block_type ) {
	$has_shadow_support = block_has_support( $block_type, 'shadow', false );

	if ( ! $has_shadow_support ) {
		return;
	}

	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	if ( ! array_key_exists( 'style', $block_type->attributes ) ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}

	if ( ! array_key_exists( 'shadow', $block_type->attributes ) ) {
		$block_type->attributes['shadow'] = array(
			'type' => 'string',
		);
	}
}

/**
 * Add CSS classes and inline styles for shadow features to the incoming attributes array.
 * This will be applied to the block markup in the front-end.
 *
 * @since 6.3.0
 * @since 6.6.0 Return early if __experimentalSkipSerialization is true.
 * @access private
 *
 * @param WP_Block_Type $block_type       Block type.
 * @param array         $block_attributes Block attributes.
 * @return array Shadow CSS classes and inline styles.
 */
function wp_apply_shadow_support( $block_type, $block_attributes ) {
	$has_shadow_support = block_has_support( $block_type, 'shadow', false );

	if (
		! $has_shadow_support ||
		wp_should_skip_block_supports_serialization( $block_type, 'shadow' )
	) {
		return array();
	}

	return wp_get_shadow_classes_and_styles( $block_attributes );
}

/**
 * Returns shadow classes and inline styles for block attributes, like the JS
 * `getShadowClassesAndStyles()`. Does not check block support or skipped
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
function wp_get_shadow_classes_and_styles( $block_attributes ) {
	if ( ! is_array( $block_attributes ) ) {
		return array();
	}

	$shadow = $block_attributes['style']['shadow'] ?? null;

	$attributes = array();
	$styles     = wp_style_engine_get_styles( array( 'shadow' => $shadow ) );

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
	'shadow',
	array(
		'register_attribute' => 'wp_register_shadow_support',
		'apply'              => 'wp_apply_shadow_support',
	)
);
