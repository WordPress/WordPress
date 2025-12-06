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
 * @since 6.0.0
 *
 * @param  array $context home link block context.
 * @return array Colors CSS classes and inline styles.
 */
function gutenberg_block_core_home_link_build_css_colors( $context ) {
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
 * @since 6.0.0
 *
 * @param  array $context Home link block context.
 * @return array Font size CSS classes and inline styles.
 */
function gutenberg_block_core_home_link_build_css_font_sizes( $context ) {
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
 * @since 6.0.0
 *
 * @param  array $context    Home link block context.
 * @return string The li wrapper attributes.
 */
function gutenberg_block_core_home_link_build_li_wrapper_attributes( $context ) {
	$colors          = gutenberg_block_core_home_link_build_css_colors( $context );
	$font_sizes      = gutenberg_block_core_home_link_build_css_font_sizes( $context );
	$classes         = array_merge(
		$colors['css_classes'],
		$font_sizes['css_classes']
	);
	$style_attribute = ( $colors['inline_styles'] . $font_sizes['inline_styles'] );
	$classes[]       = 'wp-block-navigation-item';

	if ( is_front_page() ) {
		$classes[] = 'current-menu-item';
	} elseif ( is_home() && ( (int) get_option( 'page_for_posts' ) !== get_queried_object_id() ) ) {
		// Edge case where the Reading settings has a posts page set but not a static homepage.
		$classes[] = 'current-menu-item';
	}

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
 * @since 6.0.0
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The saved content.
 * @param WP_Block $block      The parsed block.
 *
 * @return string Returns the post content with the home url added.
 */
function gutenberg_render_block_core_home_link( $attributes, $content, $block ) {
	if ( empty( $attributes['label'] ) ) {
		$attributes['label'] = __( 'Home' );
	}
	$aria_current = '';

	if ( is_front_page() ) {
		$aria_current = ' aria-current="page"';
	} elseif ( is_home() && ( (int) get_option( 'page_for_posts' ) !== get_queried_object_id() ) ) {
		// Edge case where the Reading settings has a posts page set but not a static homepage.
		$aria_current = ' aria-current="page"';
	}

	return sprintf(
		'<li %1$s><a class="wp-block-home-link__content wp-block-navigation-item__content" href="%2$s" rel="home"%3$s>%4$s</a></li>',
		gutenberg_block_core_home_link_build_li_wrapper_attributes( $block->context ),
		esc_url( home_url() ),
		$aria_current,
		wp_kses_post( $attributes['label'] )
	);
}

/**
 * Register the home block
 *
 * @since 6.0.0
 *
 * @uses gutenberg_render_block_core_home_link()
 * @throws WP_Error An WP_Error exception parsing the block definition.
 */
function gutenberg_register_block_core_home_link() {
	register_block_type_from_metadata(
		__DIR__ . '/home-link',
		array(
			'render_callback' => 'gutenberg_render_block_core_home_link',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_home_link', 20 );
