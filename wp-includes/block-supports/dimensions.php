<?php
/**
 * Dimensions block support flag.
 *
 * This does not include the `spacing` block support even though that visually
 * appears under the "Dimensions" panel in the editor. It remains in its
 * original `spacing.php` file for backwards compatibility.
 *
 * @package WordPress
 * @since 5.9.0
 */

/**
 * Registers the style block attribute for block types that support it.
 *
 * @since 5.9.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_dimensions_support( $block_type ) {
	// Setup attributes and styles within that if needed.
	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	// Check for existing style attribute definition e.g. from block.json.
	if ( array_key_exists( 'style', $block_type->attributes ) ) {
		return;
	}

	$has_dimensions_support = block_has_support( $block_type, array( '__experimentalDimensions' ), false );
	// Future block supports such as height & width will be added here.

	if ( $has_dimensions_support ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}
}

/**
 * Adds CSS classes for block dimensions to the incoming attributes array.
 * This will be applied to the block markup in the front-end.
 *
 * @since 5.9.0
 * @access private
 *
 * @param WP_Block_Type $block_type       Block Type.
 * @param array         $block_attributes Block attributes.
 * @return array Block dimensions CSS classes and inline styles.
 */
function wp_apply_dimensions_support( $block_type, $block_attributes ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	if ( wp_skip_dimensions_serialization( $block_type ) ) {
		return array();
	}

	$styles = array();

	// Height support to be added in near future.
	// Width support to be added in near future.

	return empty( $styles ) ? array() : array( 'style' => implode( ' ', $styles ) );
}

/**
 * Checks whether serialization of the current block's dimensions properties
 * should occur.
 *
 * @since 5.9.0
 * @access private
 *
 * @param WP_Block_type $block_type Block type.
 * @return bool Whether to serialize spacing support styles & classes.
 */
function wp_skip_dimensions_serialization( $block_type ) {
	$dimensions_support = _wp_array_get( $block_type->supports, array( '__experimentalDimensions' ), false );
	return is_array( $dimensions_support ) &&
		array_key_exists( '__experimentalSkipSerialization', $dimensions_support ) &&
		$dimensions_support['__experimentalSkipSerialization'];
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'dimensions',
	array(
		'register_attribute' => 'wp_register_dimensions_support',
		'apply'              => 'wp_apply_dimensions_support',
	)
);
