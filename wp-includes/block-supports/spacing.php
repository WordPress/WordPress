<?php
/**
 * Spacing block support flag.
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
	$has_spacing_support = block_has_support( $block_type, array( 'spacing' ), false );

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
 * Add CSS classes for block spacing to the incoming attributes array.
 * This will be applied to the block markup in the front-end.
 *
 * @since 5.8.0
 * @access private
 *
 * @param WP_Block_Type $block_type       Block Type.
 * @param array         $block_attributes Block attributes.
 *
 * @return array Block spacing CSS classes and inline styles.
 */
function wp_apply_spacing_support( $block_type, $block_attributes ) {
	$has_padding_support = wp_has_spacing_feature_support( $block_type, 'padding' );
	$has_margin_support  = wp_has_spacing_feature_support( $block_type, 'margin' );
	$styles              = array();

	if ( $has_padding_support ) {
		$padding_value = _wp_array_get( $block_attributes, array( 'style', 'spacing', 'padding' ), null );
		if ( null !== $padding_value ) {
			foreach ( $padding_value as $key => $value ) {
				$styles[] = sprintf( 'padding-%s: %s;', $key, $value );
			}
		}
	}

	if ( $has_margin_support ) {
		$margin_value = _wp_array_get( $block_attributes, array( 'style', 'spacing', 'margin' ), null );
		if ( null !== $margin_value ) {
			foreach ( $margin_value as $key => $value ) {
				$styles[] = sprintf( 'margin-%s: %s;', $key, $value );
			}
		}
	}

	return empty( $styles ) ? array() : array( 'style' => implode( ' ', $styles ) );
}

/**
 * Checks whether the current block type supports the spacing feature requested.
 *
 * @since 5.8.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block type to check for support.
 * @param string        $feature    Name of the feature to check support for.
 * @param mixed         $default    Fallback value for feature support, defaults to false.
 *
 * @return boolean                  Whether or not the feature is supported.
 */
function wp_has_spacing_feature_support( $block_type, $feature, $default = false ) {
	// Check if the specific feature has been opted into individually
	// via nested flag under `spacing`.
	return block_has_support( $block_type, array( 'spacing', $feature ), $default );
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'spacing',
	array(
		'register_attribute' => 'wp_register_spacing_support',
		'apply'              => 'wp_apply_spacing_support',
	)
);
