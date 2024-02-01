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

	if ( array_key_exists( 'style', $block_type->attributes ) ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}

	if ( array_key_exists( 'shadow', $block_type->attributes ) ) {
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
 * @access private
 *
 * @param  WP_Block_Type $block_type       Block type.
 * @param  array         $block_attributes Block attributes.
 * @return array Shadow CSS classes and inline styles.
 */
function wp_apply_shadow_support( $block_type, $block_attributes ) {
	$has_shadow_support = block_has_support( $block_type, 'shadow', false );

	if ( ! $has_shadow_support ) {
		return array();
	}

	$shadow_block_styles = array();

	$custom_shadow                 = $block_attributes['style']['shadow'] ?? null;
	$shadow_block_styles['shadow'] = $custom_shadow;

	$attributes = array();
	$styles     = wp_style_engine_get_styles( $shadow_block_styles );

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
