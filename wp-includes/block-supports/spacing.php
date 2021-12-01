<?php
/**
 * Spacing block support flag.

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
 * @return array Block spacing CSS classes and inline styles.
 */
function wp_apply_spacing_support( $block_type, $block_attributes ) {
	if ( wp_skip_spacing_serialization( $block_type ) ) {
		return array();
	}

	$has_padding_support = block_has_support( $block_type, array( 'spacing', 'padding' ), false );
	$has_margin_support  = block_has_support( $block_type, array( 'spacing', 'margin' ), false );
	$styles              = array();

	if ( $has_padding_support ) {
		$padding_value = _wp_array_get( $block_attributes, array( 'style', 'spacing', 'padding' ), null );
		if ( is_array( $padding_value ) ) {
			foreach ( $padding_value as $key => $value ) {
				$styles[] = sprintf( 'padding-%s: %s;', $key, $value );
			}
		} elseif ( null !== $padding_value ) {
			$styles[] = sprintf( 'padding: %s;', $padding_value );
		}
	}

	if ( $has_margin_support ) {
		$margin_value = _wp_array_get( $block_attributes, array( 'style', 'spacing', 'margin' ), null );
		if ( is_array( $margin_value ) ) {
			foreach ( $margin_value as $key => $value ) {
				$styles[] = sprintf( 'margin-%s: %s;', $key, $value );
			}
		} elseif ( null !== $margin_value ) {
			$styles[] = sprintf( 'margin: %s;', $margin_value );
		}
	}

	return empty( $styles ) ? array() : array( 'style' => implode( ' ', $styles ) );
}

/**
 * Checks whether serialization of the current block's spacing properties should
 * occur.
 *
 * @since 5.9.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block type.
 * @return bool Whether to serialize spacing support styles & classes.
 */
function wp_skip_spacing_serialization( $block_type ) {
	$spacing_support = _wp_array_get( $block_type->supports, array( 'spacing' ), false );

	return is_array( $spacing_support ) &&
		array_key_exists( '__experimentalSkipSerialization', $spacing_support ) &&
		$spacing_support['__experimentalSkipSerialization'];
}

/**
 * Renders the spacing gap support to the block wrapper, to ensure
 * that the CSS variable is rendered in all environments.
 *
 * @since 5.9.0
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 */
function wp_render_spacing_gap_support( $block_content, $block ) {
	$block_type      = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
	$has_gap_support = block_has_support( $block_type, array( 'spacing', 'blockGap' ), false );
	if ( ! $has_gap_support || ! isset( $block['attrs']['style']['spacing']['blockGap'] ) ) {
		return $block_content;
	}

	$gap_value = $block['attrs']['style']['spacing']['blockGap'];

	// Skip if gap value contains unsupported characters.
	// Regex for CSS value borrowed from `safecss_filter_attr`, and used here
	// because we only want to match against the value, not the CSS attribute.
	if ( preg_match( '%[\\\(&=}]|/\*%', $gap_value ) ) {
		return $block_content;
	}

	$style = sprintf(
		'--wp--style--block-gap: %s',
		esc_attr( $gap_value )
	);

	// Attempt to update an existing style attribute on the wrapper element.
	$injected_style = preg_replace(
		'/^([^>.]+?)(' . preg_quote( 'style="', '/' ) . ')(?=.+?>)/',
		'$1$2' . $style . '; ',
		$block_content,
		1
	);

	// If there is no existing style attribute, add one to the wrapper element.
	if ( $injected_style === $block_content ) {
		$injected_style = preg_replace(
			'/<([a-zA-Z0-9]+)([ >])/',
			'<$1 style="' . $style . '"$2',
			$block_content,
			1
		);
	};

	return $injected_style;
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'spacing',
	array(
		'register_attribute' => 'wp_register_spacing_support',
		'apply'              => 'wp_apply_spacing_support',
	)
);

add_filter( 'render_block', 'wp_render_spacing_gap_support', 10, 2 );
