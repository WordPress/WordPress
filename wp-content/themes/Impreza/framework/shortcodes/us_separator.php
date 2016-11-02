<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_separator
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['type'] string Separator type: 'default' / 'fullwidth' / 'short' / 'invisible'
 * @param $atts ['size'] string Separator size: 'small' / 'medium' / 'large' / 'huge'
 * @param $atts ['thick'] string Line thickness: '1' / '2' / '3' / '4' / '5'
 * @param $atts ['style'] string Line style: 'solid' / 'dashed' / 'dotted' / 'double'
 * @param $atts ['color'] string Color style: 'border' / 'primary' / 'secondary' / 'custom'
 * @param $atts ['bdcolor'] string Border color value
 * @param $atts ['icon'] string Icon
 * @param $atts ['text'] string Title
 * @param $atts ['title_tag'] string Title Tag Name: 'h1' / 'h2'/ 'h3'/ 'h4'/ 'h5'/ 'h6'/ 'div'
 * @param $atts ['title_size'] string Title Size
 * @param $atts ['el_class'] string Extra class name
 */

$atts = us_shortcode_atts( $atts, 'us_separator' );

$classes = '';
$inner_css = '';

$classes .= ' type_' . $atts['type'] . ' size_' . $atts['size'] . ' thick_' . $atts['thick'] . ' style_' . $atts['style'];

$classes .= ' color_' . $atts['color'];
if ( $atts['color'] == 'custom' AND ! empty( $atts['bdcolor'] ) ) {
	$inner_css .= 'border-color: ' . $atts['bdcolor'] . '; color: ' . $atts['bdcolor'] . ';';
}

$inner_html = '';
$atts['icon'] = trim( $atts['icon'] );
if ( ! empty( $atts['icon'] ) ) {
	$classes .= ' cont_icon';
	$inner_html = '<i class="' . us_prepare_icon_class( $atts['icon'] ) . '"></i>';
} elseif ( ! empty( $atts['text'] ) ) {
	$classes .= ' cont_text';
	$title_inner_css = '';
	if ( $atts['title_size'] != '' ) {
		$title_inner_css = ' style="font-size: ' . $atts['title_size'] . '"';
	}
	$inner_html = '<' . $atts['title_tag'] . $title_inner_css .'>' . $atts['text'] . '</' . $atts['title_tag'] . '>';
} else {
	$classes .= ' cont_none';
}

if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}

$output = '<div class="w-separator' . $classes . '"';
if ( ! empty( $inner_css ) ) {
	$output .= ' style="' . $inner_css . '"';
}
$output .= '><span class="w-separator-h">' . $inner_html . '</span></div>';

echo $output;
