<?php defined('ABSPATH') OR die('This script cannot be accessed directly.');

/**
 * Shortcode: vc_row_inner
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
 * @param $atts ['content_placement'] string Columns Content Position: 'default' / 'top' / 'middle' / 'bottom'
 * @param $atts ['columns_type'] string Columns type: 'small' / 'medium' / 'large' / 'none'
 * @param $atts ['el_id'] string
 * @param $atts ['el_class'] string
 * @param $atts ['disable_element'] string
 * @param $atts ['css'] string
 */

$atts = us_shortcode_atts( $atts, 'vc_row_inner' );

if ( 'yes' === $atts['disable_element'] ) {
	return '';
}

$class_name = '';

// Offset modificator
$class_name .= ' offset_' . $atts['columns_type'];

if ( ! empty( $atts['content_placement'] ) AND $atts['content_placement'] != 'default' ) {
	$class_name .= ' valign_' . $atts['content_placement'];
}

// Preserving additional class for inner VC rows
if ( $shortcode_base == 'vc_row_inner' ) {
	$class_name .= ' vc_inner';
}

// Additional class set by a user in a shortcode attributes
if ( ! empty( $atts['el_class'] ) ) {
	$class_name .= ' ' . $atts['el_class'];
}

if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
	$class_name .= ' ' . vc_shortcode_custom_css_class( $atts['css'], ' ' );
}
$class_name = apply_filters( 'vc_shortcodes_css_class', $class_name, $shortcode_base, $atts );

$row_id_param = '';

$output = '<div class="g-cols wpb_row' . $class_name . '"';
if ( ! empty( $atts['el_id'] ) ) {
	$output .= ' id="' . $atts['el_id'] . '"';
}
$output .= '>';
$output .= do_shortcode( $content );
$output .= '</div>';

echo $output;
