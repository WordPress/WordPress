<?php
/**
 * Anchor block support flag.
 *
 * @package WordPress
 * @since 6.2.0
 */

/**
 * Registers the anchor block attribute for block types that support it.
 *
 * @since 6.2.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_anchor_support( $block_type ) {
	$has_anchor_support = _wp_array_get( $block_type->supports, array( 'anchor' ), true );
	if ( ! $has_anchor_support ) {
		return;
	}

	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	if ( ! array_key_exists( 'anchor', $block_type->attributes ) ) {
		$block_type->attributes['anchor'] = array(
			'type' => 'string',
		);
	}
}

/**
 * Add the anchor to the output.
 *
 * @since 6.2.0
 * @access private
 *
 * @param WP_Block_Type $block_type       Block Type.
 * @param array         $block_attributes Block attributes.
 * @return array Block anchor.
 */
function wp_apply_anchor_support( $block_type, $block_attributes ) {
	if ( ! $block_attributes ) {
		return array();
	}

	if ( wp_should_skip_block_supports_serialization( $block_type, 'anchor' ) ) {
		return array();
	}

	$has_anchor_support = _wp_array_get( $block_type->supports, array( 'anchor' ), true );
	if ( ! $has_anchor_support ) {
		return array();
	}

	$has_anchor = array_key_exists( 'anchor', $block_attributes );
	if ( ! $has_anchor ) {
		return array();
	}

	return array( 'id' => $block_attributes['anchor'] );
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'anchor',
	array(
		'register_attribute' => 'wp_register_anchor_support',
		'apply'              => 'wp_apply_anchor_support',
	)
);
