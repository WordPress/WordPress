<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: vc_column
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
 * @param $atts ['width'] string Width in format: 1/2 (is set by visual composer renderer)
 * @param $atts ['text_color'] string Text color
 * @param $atts ['animate'] string Animation type: '' / 'fade' / 'afc' / 'afl' / 'afr' / 'afb' / 'aft' / 'hfc' / 'wfc'
 * @param $atts ['animate_delay'] float Animation delay (in seconds)
 * @param $atts ['el_class'] string Additional class
 * @param $atts ['offset'] string Visual Composer classes for responsive behaviour
 * @param $atts ['css'] string Custom CSS
 */

// $shorcode_base may be: 'vc_column' / 'vc_column_inner'
$atts = us_shortcode_atts( $atts, $shortcode_base );

$inner_css = '';

if ( function_exists( 'wpb_translateColumnWidthToSpan' ) ) {
	$width = wpb_translateColumnWidthToSpan( $atts['width'] );

} elseif ( function_exists( 'us_wpb_translateColumnWidthToSpan' ) ) {
	$width = us_wpb_translateColumnWidthToSpan( $atts['width'] );
}

if ( function_exists( 'vc_column_offset_class_merge' ) ) {
	$width = vc_column_offset_class_merge( $atts['offset'], $width );

} elseif ( function_exists( 'us_vc_column_offset_class_merge' ) ) {
	$width = us_vc_column_offset_class_merge( $atts['offset'], $width );
}
$classes = $width . ' wpb_column vc_column_container';

if ( function_exists( 'vc_shortcode_custom_css_has_property' ) AND vc_shortcode_custom_css_has_property( $atts['css'], array('border', 'background') ) ) {
	$classes .= ' vc_col-has-fill';
} elseif ( function_exists( 'us_vc_shortcode_custom_css_has_property' ) AND us_vc_shortcode_custom_css_has_property( $atts['css'], array('border', 'background') ) ) {
	$classes .= ' vc_col-has-fill';
}

if ( ! empty( $atts['animate'] ) ) {
	$classes .= ' animate_' . $atts['animate'];
	if ( ! empty( $atts['animate_delay'] ) ) {
		$atts['animate_delay'] = floatval( $atts['animate_delay'] );
		$classes .= ' d' . intval( $atts['animate_delay'] * 5 );
	}
}

if ( ! empty( $atts['css'] ) AND function_exists( 'vc_shortcode_custom_css_class' ) ) {
	$classes .= ' ' . vc_shortcode_custom_css_class( $atts['css'], ' ' );
}

if ( $atts['text_color'] != '' ) {
	$inner_css .= 'color:' . $atts['text_color'] . ';';
	$classes .= ' color_custom';
}

if ( ! empty( $inner_css ) ) {
	$inner_css = ' style="' . $inner_css . '"';
}

if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}

$output = '<div class="' . $classes . '"' . $inner_css . '><div class="vc_column-inner">' . do_shortcode( $content ) . '</div></div>';

echo $output;
