<?php
/**
 * Shared helper function for building CSS font sizes in navigation blocks.
 *
 * @package WordPress
 */

/**
 * Build an array with CSS classes and inline styles defining the font sizes
 * which will be applied to the navigation markup in the front-end.
 *
 * @since 7.1.0
 *
 * @param  array $context Navigation block context.
 * @return array Font size CSS classes and inline styles.
 */
function block_core_shared_navigation_build_css_font_sizes( $context ) {
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
		$font_sizes['inline_styles'] = sprintf(
			'font-size: %s;',
			wp_get_typography_font_size_value(
				array(
					'size' => $context['style']['typography']['fontSize'],
				)
			)
		);
	}

	return $font_sizes;
}
