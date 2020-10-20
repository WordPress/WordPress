<?php
/**
 * Colors block support flag.
 *
 * @package WordPress
 */

/**
 * Registers the style and colors block attributes for block types that support it.
 *
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_colors_support( $block_type ) {
	$color_support = false;
	if ( property_exists( $block_type, 'supports' ) ) {
		$color_support = wp_array_get( $block_type->supports, array( '__experimentalColor' ), false );
	}
	$has_text_colors_support       = true === $color_support || ( is_array( $color_support ) && wp_array_get( $color_support, array( 'text' ), true ) );
	$has_background_colors_support = true === $color_support || ( is_array( $color_support ) && wp_array_get( $color_support, array( 'background' ), true ) );
	$has_gradients_support         = wp_array_get( $color_support, array( 'gradients' ), false );

	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	if ( $has_text_colors_support && ! array_key_exists( 'style', $block_type->attributes ) ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}

	if ( $has_background_colors_support && ! array_key_exists( 'backgroundColor', $block_type->attributes ) ) {
		$block_type->attributes['backgroundColor'] = array(
			'type' => 'string',
		);
	}

	if ( $has_text_colors_support && ! array_key_exists( 'textColor', $block_type->attributes ) ) {
		$block_type->attributes['textColor'] = array(
			'type' => 'string',
		);
	}

	if ( $has_gradients_support && ! array_key_exists( 'gradient', $block_type->attributes ) ) {
		$block_type->attributes['gradient'] = array(
			'type' => 'string',
		);
	}
}


/**
 * Add CSS classes and inline styles for colors to the incoming attributes array.
 * This will be applied to the block markup in the front-end.
 *
 * @access private
 *
 * @param  WP_Block_Type $block_type       Block type.
* @param  array         $block_attributes Block attributes.
 *
 * @return array Colors CSS classes and inline styles.
 */
function wp_apply_colors_support( $block_type, $block_attributes ) {
	$color_support                 = wp_array_get( $block_type->supports, array( '__experimentalColor' ), false );
	$has_text_colors_support       = true === $color_support || ( is_array( $color_support ) && wp_array_get( $color_support, array( 'text' ), true ) );
	$has_background_colors_support = true === $color_support || ( is_array( $color_support ) && wp_array_get( $color_support, array( 'background' ), true ) );
	$has_link_colors_support       = wp_array_get( $color_support, array( 'linkColor' ), false );
	$has_gradients_support         = wp_array_get( $color_support, array( 'gradients' ), false );
	$classes                       = array();
	$styles                        = array();

	// Text Colors.
	// Check support for text colors.
	if ( $has_text_colors_support ) {
		$has_named_text_color  = array_key_exists( 'textColor', $block_attributes );
		$has_custom_text_color = isset( $block_attributes['style']['color']['text'] );

		// Apply required generic class.
		if ( $has_custom_text_color || $has_named_text_color ) {
			$classes[] = 'has-text-color';
		}
		// Apply color class or inline style.
		if ( $has_named_text_color ) {
			$classes[] = sprintf( 'has-%s-color', $block_attributes['textColor'] );
		} elseif ( $has_custom_text_color ) {
			$styles[] = sprintf( 'color: %s;', $block_attributes['style']['color']['text'] );
		}
	}

	// Link Colors.
	if ( $has_link_colors_support ) {
		$has_link_color = isset( $block_attributes['style']['color']['link'] );
		// Apply required class and style.
		if ( $has_link_color ) {
			$classes[] = 'has-link-color';
			// If link is a named color.
			if ( strpos( $block_attributes['style']['color']['link'], 'var:preset|color|' ) !== false ) {
				// Get the name from the string and add proper styles.
				$index_to_splice = strrpos( $block_attributes['style']['color']['link'], '|' ) + 1;
				$link_color_name = substr( $block_attributes['style']['color']['link'], $index_to_splice );
				$styles[]        = sprintf( '--wp--style--color--link: var(--wp--preset--color--%s);', $link_color_name );
			} else {
				$styles[] = sprintf( '--wp--style--color--link: %s;', $block_attributes['style']['color']['link'] );
			}
		}
	}

	// Background Colors.
	if ( $has_background_colors_support ) {
		$has_named_background_color  = array_key_exists( 'backgroundColor', $block_attributes );
		$has_custom_background_color = isset( $block_attributes['style']['color']['background'] );

		// Apply required background class.
		if ( $has_custom_background_color || $has_named_background_color ) {
			$classes[] = 'has-background';
		}
		// Apply background color classes or styles.
		if ( $has_named_background_color ) {
			$classes[] = sprintf( 'has-%s-background-color', $block_attributes['backgroundColor'] );
		} elseif ( $has_custom_background_color ) {
			$styles[] = sprintf( 'background-color: %s;', $block_attributes['style']['color']['background'] );
		}
	}

	// Gradients.
	if ( $has_gradients_support ) {
		$has_named_gradient  = array_key_exists( 'gradient', $block_attributes );
		$has_custom_gradient = isset( $block_attributes['style']['color']['gradient'] );

		if ( $has_named_gradient || $has_custom_gradient ) {
			$classes[] = 'has-background';
		}
		// Apply required background class.
		if ( $has_named_gradient ) {
			$classes[] = sprintf( 'has-%s-gradient-background', $block_attributes['gradient'] );
		} elseif ( $has_custom_gradient ) {
			$styles[] = sprintf( 'background: %s;', $block_attributes['style']['color']['gradient'] );
		}
	}

	$attributes = array();
	if ( ! empty( $classes ) ) {
		$attributes['class'] = implode( ' ', $classes );
	}
	if ( ! empty( $styles ) ) {
		$attributes['style'] = implode( ' ', $styles );
	}

	return $attributes;
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'colors',
	array(
		'register_attribute' => 'wp_register_colors_support',
		'apply'              => 'wp_apply_colors_support',
	)
);
