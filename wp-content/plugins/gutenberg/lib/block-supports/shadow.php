<?php
/**
 * Shadow block support flag.
 *
 * @package gutenberg
 */

/**
 * Registers the style and shadow block attributes for block types that support it.
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function gutenberg_register_shadow_support( $block_type ) {
	$has_shadow_support = block_has_support( $block_type, array( 'shadow' ), false );

	if ( ! $has_shadow_support ) {
		return;
	}

	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	if ( $has_shadow_support && ! array_key_exists( 'style', $block_type->attributes ) ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}

	if ( $has_shadow_support && ! array_key_exists( 'shadow', $block_type->attributes ) ) {
		$block_type->attributes['shadow'] = array(
			'type' => 'string',
		);
	}
}

/**
 * Add CSS classes and inline styles for shadow features to the incoming attributes array.
 * This will be applied to the block markup in
 * the front-end.
 *
 * @param  WP_Block_Type $block_type       Block type.
 * @param  array         $block_attributes Block attributes.
 *
 * @return array Shadow CSS classes and inline styles.
 */
function gutenberg_apply_shadow_support( $block_type, $block_attributes ) {
	$has_shadow_support = block_has_support( $block_type, array( 'shadow' ), false );

	if (
		! $has_shadow_support ||
		wp_should_skip_block_supports_serialization( $block_type, 'shadow' )
	) {
		return array();
	}

	$shadow_block_styles = array();

	$custom_shadow                 = $block_attributes['style']['shadow'] ?? null;
	$shadow_block_styles['shadow'] = $custom_shadow;

	$attributes = array();
	$styles     = gutenberg_style_engine_get_styles( $shadow_block_styles );

	if ( ! empty( $styles['css'] ) ) {
		$attributes['style'] = $styles['css'];
	}

	return $attributes;
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'shadow',
	array(
		'register_attribute' => 'gutenberg_register_shadow_support',
		'apply'              => 'gutenberg_apply_shadow_support',
	)
);
