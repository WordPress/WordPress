<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: vc_column_text
 *
 * Overloaded by UpSolution custom implementation.
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['el_class'] string Additional class
 * @param $atts ['css'] string Custom CSS
 */

$atts = us_shortcode_atts( $atts, 'vc_column_text' );

$classes = '';

if ( mb_substr( trim( $content ), 0, 1 ) != '<' ) {
	$content = '<p>' . $content . '</p>';
}

if ( function_exists( 'vc_shortcode_custom_css_class' ) AND ! empty( $atts['css'] ) ) {
	$classes .= ' ' . vc_shortcode_custom_css_class( $atts['css'] );
}

if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}

$output = '<div class="wpb_text_column ' . $classes . '">';
$output .= '<div class="wpb_wrapper">' . do_shortcode( shortcode_unautop( $content ) ) . '</div> ';
$output .= '</div> ';

echo $output;
