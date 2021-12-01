<?php
/**
 * Align block support flag.
 *
 * @package WordPress
 * @since 5.6.0
 */

/**
 * Registers the align block attribute for block types that support it.
 *
 * @since 5.6.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_alignment_support( $block_type ) {
	$has_align_support = block_has_support( $block_type, array( 'align' ), false );
	if ( $has_align_support ) {
		if ( ! $block_type->attributes ) {
			$block_type->attributes = array();
		}

		if ( ! array_key_exists( 'align', $block_type->attributes ) ) {
			$block_type->attributes['align'] = array(
				'type' => 'string',
				'enum' => array( 'left', 'center', 'right', 'wide', 'full', '' ),
			);
		}
	}
}

/**
 * Adds CSS classes for block alignment to the incoming attributes array.
 * This will be applied to the block markup in the front-end.
 *
 * @since 5.6.0
 * @access private
 *
 * @param WP_Block_Type $block_type       Block Type.
 * @param array         $block_attributes Block attributes.
 * @return array Block alignment CSS classes and inline styles.
 */
function wp_apply_alignment_support( $block_type, $block_attributes ) {
	$attributes        = array();
	$has_align_support = block_has_support( $block_type, array( 'align' ), false );
	if ( $has_align_support ) {
		$has_block_alignment = array_key_exists( 'align', $block_attributes );

		if ( $has_block_alignment ) {
			$attributes['class'] = sprintf( 'align%s', $block_attributes['align'] );
		}
	}

	return $attributes;
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'align',
	array(
		'register_attribute' => 'wp_register_alignment_support',
		'apply'              => 'wp_apply_alignment_support',
	)
);
