<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_message
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['color'] string Message box color: 'info' / 'attention' / 'success' / 'error' / 'custom'
 * @param $atts ['bg_color'] string Background color
 * @param $atts ['text_color'] string Text color
 * @param $atts ['icon'] string Icon
 * @param $atts ['closing'] bool Enable closing?
 * @param $atts ['el_class'] string Extra class name
 */

$atts = us_shortcode_atts( $atts, 'us_message' );

$classes = '';
$inner_css = '';

$icon_html = '';
$atts['icon'] = trim( $atts['icon'] );
if ( ! empty( $atts['icon'] ) ) {
	$icon_html = '<div class="w-message-icon"><i class="' . us_prepare_icon_class( $atts['icon'] ) . '"></i></div>';
	$classes .= ' with_icon';
}

$closer_html = '';
if ( $atts['closing'] ) {
	$classes .= ' with_close';
	$closer_html = '<div class="w-message-close"> &#10005; </div>';
}

if ( $atts['color'] == 'custom' ) {
	if ( ! empty( $atts['bg_color'] ) ) {
		$inner_css .= 'background-color:' . $atts['bg_color'] . ';';
	}
	if ( ! empty( $atts['text_color'] ) ) {
		$inner_css .= 'color:' . $atts['text_color'] . ';';
	}
}
$classes .= ' type_' . $atts['color'];

if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}

if ( ! empty( $inner_css ) ) {
	$inner_css = ' style="' . $inner_css . '"';
}

$output = '<div class="w-message' . $classes . '"' . $inner_css . '>' . $closer_html . $icon_html;
$output .= '<div class="w-message-body"><p>' . do_shortcode( $content ) . '</p></div></div>';

echo $output;
