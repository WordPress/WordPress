<?php
/**
 * Server-side rendering of the `core/home-link` block.
 *
 * @package WordPress
 */

/**
 * Build an array with CSS classes and inline styles defining the colors
 * which will be applied to the home link markup in the front-end.
 *
 * @param  array $context home link block context.
 * @return array Colors CSS classes and inline styles.
 */
function block_core_home_link_build_css_colors( $context ) {
	$colors = array(
		'css_classes'   => array(),
		'inline_styles' => '',
	);

	// Text color.
	$has_named_text_color  = array_key_exists( 'textColor', $context );
	$has_custom_text_color = isset( $context['style']['color']['text'] );

	// If has text color.
	if ( $has_custom_text_color || $has_named_text_color ) {
		// Add has-text-color class.
		$colors['css_classes'][] = 'has-text-color';
	}

	if ( $has_named_text_color ) {
		// Add the color class.
		$colors['css_classes'][] = sprintf( 'has-%s-color', $context['textColor'] );
	} elseif ( $has_custom_text_color ) {
		// Add the custom color inline style.
		$colors['inline_styles'] .= sprintf( 'color: %s;', $context['style']['color']['text'] );
	}

	// Background color.
	$has_named_background_color  = array_key_exists( 'backgroundColor', $context );
	$has_custom_background_color = isset( $context['style']['color']['background'] );

	// If has background color.
	if ( $has_custom_background_color || $has_named_background_color ) {
		// Add has-background class.
		$colors['css_classes'][] = 'has-background';
	}

	if ( $has_named_background_color ) {
		// Add the background-color class.
		$colors['css_classes'][] = sprintf( 'has-%s-background-color', $context['backgroundColor'] );
	} elseif ( $has_custom_background_color ) {
		// Add the custom background-color inline style.
		$colors['inline_styles'] .= sprintf( 'background-color: %s;', $context['style']['color']['background'] );
	}

	return $colors;
}

/**
 * Build an array with CSS classes and inline styles defining the font sizes
 * which will be applied to the home link markup in the front-end.
 *
 * @param  array $context Home link block context.
 * @return array Font size CSS classes and inline styles.
 */
function block_core_home_link_build_css_font_sizes( $context ) {
	// CSS classes.
	$font_sizes = array(
		'css_classes'   => array(),
		'inline_styles' => '',
	);

	$has_named_font_size  = array_key_exists( 'fontSize', $context );
	$has_custom_font_size = isset( $context['style']['typography']['fontSize'] );

	if ( $has_named_font_size ) {
		// Add the font size class.
		$font_sizes['css_classes'][] = sprintf( 'has-%s-font-size', $context['fontSize'] );
	} elseif ( $has_custom_font_size ) {
		// Add the custom font size inline style.
		$font_sizes['inline_styles'] = sprintf( 'font-size: %s;', $context['style']['typography']['fontSize'] );
	}

	return $font_sizes;
}

/**
 * Builds an array with classes and style for the li wrapper
 *
 * @param  array $context    Home link block context.
 * @return string The li wrapper attributes.
 */
function block_core_home_link_build_li_wrapper_attributes( $context ) {
	$colors          = block_core_home_link_build_css_colors( $context );
	$font_sizes      = block_core_home_link_build_css_font_sizes( $context );
	$classes         = array_merge(
		$colors['css_classes'],
		$font_sizes['css_classes']
	);
	$classes[]       = 'wp-block-navigation-item';
	$style_attribute = ( $colors['inline_styles'] . $font_sizes['inline_styles'] );

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => implode( ' ', $classes ),
			'style' => $style_attribute,
		)
	);

	return $wrapper_attributes;
}

/**
 * Renders the `core/home-link` block.
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The saved content.
 * @param WP_Block $block      The parsed block.
 *
 * @return string Returns the post content with the home url added.
 */
function render_block_core_home_link( $attributes, $content, $block ) {
	if ( empty( $attributes['label'] ) ) {
		return '';
	}

	$aria_current = is_home() || ( is_front_page() && 'page' === get_option( 'show_on_front' ) ) ? ' aria-current="page"' : '';

	return sprintf(
		'<li %1$s><a class="wp-block-home-link__content wp-block-navigation-item__content" href="%2$s" rel="home"%3$s>%4$s</a></li>',
		block_core_home_link_build_li_wrapper_attributes( $block->context ),
		esc_url( home_url() ),
		$aria_current,
		wp_kses_post( $attributes['label'] )
	);
}

/**
 * Register the home block
 *
 * @uses render_block_core_home_link()
 * @throws WP_Error An WP_Error exception parsing the block definition.
 */
function register_block_core_home_link() {
	register_block_type_from_metadata(
		__DIR__ . '/home-link',
		array(
			'render_callback' => 'render_block_core_home_link',
		)
	);
}
add_action( 'init', 'register_block_core_home_link' );
