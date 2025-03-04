<?php
/**
 * Aria label block support flag.
 *
 * @package WordPress
 * @since 6.8.0
 */

/**
 * Registers the aria-label block attribute for block types that support it.
 *
 * @since 6.8.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_aria_label_support( $block_type ) {
	$has_aria_label_support = block_has_support( $block_type, array( 'ariaLabel' ), false );

	if ( ! $has_aria_label_support ) {
		return;
	}

	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	if ( ! array_key_exists( 'ariaLabel', $block_type->attributes ) ) {
		$block_type->attributes['ariaLabel'] = array(
			'type' => 'string',
		);
	}
}

/**
 * Add the aria-label to the output.
 *
 * @since 6.8.0
 * @access private
 *
 * @param WP_Block_Type $block_type       Block Type.
 * @param array         $block_attributes Block attributes.
 *
 * @return array Block aria-label.
 */
function wp_apply_aria_label_support( $block_type, $block_attributes ) {
	if ( ! $block_attributes ) {
		return array();
	}

	$has_aria_label_support = block_has_support( $block_type, array( 'ariaLabel' ), false );
	if ( ! $has_aria_label_support ) {
		return array();
	}

	$has_aria_label = array_key_exists( 'ariaLabel', $block_attributes );
	if ( ! $has_aria_label ) {
		return array();
	}
	return array( 'aria-label' => $block_attributes['ariaLabel'] );
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'aria-label',
	array(
		'register_attribute' => 'wp_register_aria_label_support',
		'apply'              => 'wp_apply_aria_label_support',
	)
);
