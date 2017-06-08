<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: vc_tta_section
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
 * @param $atts ['title'] string Section title
 * @param $atts ['tab_id'] string Section slug
 * @param $atts ['icon'] string Icon
 * @param $atts ['i_position'] string Icon position: 'left' / 'right'
 * @param $atts ['active'] bool Tab is opened when page loads
 * @param $atts ['indents'] string Indents type: '' / 'none'
 * @param $atts ['bg_color'] string Background color
 * @param $atts ['text_color'] string Text color
 * @param $atts ['c_position'] string Control position (inherited from wrapping vc_tta_tabs shortcode): 'left' / 'right'
 * @param $atts ['title_tag'] string Title Tag Name (inherited from wrapping vc_tta_tabs shortcode): 'div' / 'h2'/ 'h3'/ 'h4'/ 'h5'/ 'h6'/ 'p'
 * @param $atts ['title_size'] string Title Size
 * @param $atts ['el_class'] string Extra class name
 */

// .w-tabs-section container additional classes and inner CSS-styles
$classes = '';
$inner_css = '';

global $us_tabs_atts, $us_tab_index;
// Tab indexes start from 1
$us_tab_index = isset( $us_tab_index ) ? ( $us_tab_index + 1 ) : 1;

// We could overload some of the atts at vc_tabs implementation, so apply them in here as well
if ( isset( $us_tab_index ) AND isset( $us_tabs_atts[ $us_tab_index - 1 ] ) ) {
	$atts = array_merge( $atts, $us_tabs_atts[ $us_tab_index - 1 ] );
}

$atts = us_shortcode_atts( $atts, 'vc_tta_section' );

if ( ! empty( $atts['bg_color'] ) ) {
	$inner_css .= 'background-color: ' . $atts['bg_color'] . ';';
}
if ( ! empty( $atts['text_color'] ) ) {
	$inner_css .= 'color: ' . $atts['text_color'] . ';';
}
if ( $inner_css != '' ) {
	$inner_css = ' style="' . $inner_css . '"';
	$classes .= ' color_custom';
}
if ( $atts['icon'] ) {
	$classes .= ' with_icon';
}
if ( $atts['indents'] == 'none' ) {
	$classes .= ' no_indents';
}
if ( $atts['active'] ) {
	$classes .= ' active';
}
if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}

$tab_id = '';
$item_tag = 'div';
$item_tag_href = '';
$tab_id = ( ! empty( $atts['tab_id'] ) ) ? ' id="' . $atts['tab_id'] . '"' : '';
if ( $tab_id != '' ) {
	$item_tag = 'a';
	$item_tag_href = ' href="#' . $atts['tab_id'] . '"';
}


$output = '<div class="w-tabs-section' . $classes . '"' . $tab_id . $inner_css . '>';

// In-tab header (for certain states)
$tabs_section_header_inline_css = ( isset( $atts['title_size'] ) AND $atts['title_size'] != '' ) ? ' style="font-size: ' . $atts['title_size'] . '"' : '';
$output .= '<' . $item_tag . $item_tag_href . ' class="w-tabs-section-header"' . $tabs_section_header_inline_css . '><div class="w-tabs-section-header-h">';
if ( $atts['c_position'] == 'left' ) {
	$output .= '<div class="w-tabs-section-control"></div>';
}
if ( $atts['icon'] AND $atts['i_position'] == 'left' ) {
	$output .= '<i class="' . us_prepare_icon_class( $atts['icon'] ) . '"></i>';
}
$output .= '<' . $atts['title_tag'] . ' class="w-tabs-section-title">' . $atts['title'] . '</' . $atts['title_tag'] . '>';
if ( $atts['icon'] AND $atts['i_position'] == 'right' ) {
	$output .= '<i class="' . us_prepare_icon_class( $atts['icon'] ) . '"></i>';
}
if ( $atts['c_position'] == 'right' ) {
	$output .= '<div class="w-tabs-section-control"></div>';
}
$output .= '</div></' . $item_tag . '>';
$output .= '<div class="w-tabs-section-content"><div class="w-tabs-section-content-h i-cf">' . do_shortcode( $content ) . '</div></div>';
$output .= '</div>';

echo $output;
