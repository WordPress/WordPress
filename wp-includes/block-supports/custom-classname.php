<?php
/**
 * Custom classname block support flag.
 *
 * @package WordPress
 */

/**
 * Registers the custom classname block attribute for block types that support it.
 *
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_custom_classname_support( $block_type ) {
	$has_custom_classname_support = true;
	if ( property_exists( $block_type, 'supports' ) ) {
		$has_custom_classname_support = _wp_array_get( $block_type->supports, array( 'customClassName' ), true );
	}
	if ( $has_custom_classname_support ) {
		if ( ! $block_type->attributes ) {
			$block_type->attributes = array();
		}

		if ( ! array_key_exists( 'className', $block_type->attributes ) ) {
			$block_type->attributes['className'] = array(
				'type' => 'string',
			);
		}
	}
}

/**
 * Add the custom classnames to the output.
 *
 * @access private
 *
 * @param  WP_Block_Type $block_type       Block Type.
 * @param  array         $block_attributes Block attributes.
 *
 * @return array Block CSS classes and inline styles.
 */
function wp_apply_custom_classname_support( $block_type, $block_attributes ) {
	$has_custom_classname_support = true;
	$attributes                   = array();
	if ( property_exists( $block_type, 'supports' ) ) {
		$has_custom_classname_support = _wp_array_get( $block_type->supports, array( 'customClassName' ), true );
	}
	if ( $has_custom_classname_support ) {
		$has_custom_classnames = array_key_exists( 'className', $block_attributes );

		if ( $has_custom_classnames ) {
			$attributes['class'] = $block_attributes['className'];
		}
	}

	return $attributes;
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'custom-classname',
	array(
		'register_attribute' => 'wp_register_custom_classname_support',
		'apply'              => 'wp_apply_custom_classname_support',
	)
);
