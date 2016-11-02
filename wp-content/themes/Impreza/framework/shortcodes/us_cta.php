<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_cta
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['title'] string ActionBox title
 * @param $atts ['color'] string ActionBox color style: 'primary' / 'secondary' / 'light' / 'custom'
 * @param $atts ['bg_color'] string Background color
 * @param $atts ['text_color'] string Text color
 * @param $atts ['title_tag'] string Title Tag Name (inherited from wrapping vc_tta_tabs shortcode): 'h1' / 'h2'/ 'h3'/ 'h4'/ 'h5'/ 'h6'/ 'div'
 * @param $atts ['controls'] string Button(s) location: 'right' / 'bottom'
 * @param $atts ['btn_label'] string Button 1 label
 * @param $atts ['btn_link'] string Button 1 link in a serialized format: 'url:http%3A%2F%2Fwordpress.org|title:WP%20Website|target:_blank|rel:nofollow'
 * @param $atts ['btn_color'] string Button 1 color: 'primary' / 'secondary' / 'light' / 'contrast' / 'black' / 'white'
 * @param $atts ['btn_bg_color'] string Button 1 background color
 * @param $atts ['btn_text_color'] string Button 1 text color
 * @param $atts ['btn_style'] string Button 1 style: 'raised' / 'flat'
 * @param $atts ['btn_size'] string Button 1 size
 * @param $atts ['btn_icon'] string Button 1 icon
 * @param $atts ['btn_iconpos'] string Button 1 icon position: 'left' / 'right'
 * @param $atts ['second_button'] bool Has second button?
 * @param $atts ['btn2_label'] string Button 2 label
 * @param $atts ['btn2_link'] string Button 2 link in a serialized format: 'url:http%3A%2F%2Fwordpress.org|title:WP%20Website|target:_blank|rel:nofollow'
 * @param $atts ['btn2_color'] string Button 2 color: 'primary' / 'secondary' / 'light' / 'contrast' / 'black' / 'white'
 * @param $atts ['btn2_bg_color'] string Button 2 background color
 * @param $atts ['btn2_text_color'] string Button 2 text color
 * @param $atts ['btn2_style'] string Button 2 style: 'raised' / 'flat'
 * @param $atts ['btn2_size'] string Button 2 size
 * @param $atts ['btn2_icon'] string Button 2 icon
 * @param $atts ['btn2_iconpos'] string Button 2 icon position: 'left' / 'right'
 * @param $atts ['el_class'] string Extra class name
 */

$atts = us_shortcode_atts( $atts, 'us_cta' );

// .w-actionbox container additional classes and inner CSS-styles
$classes = '';
$inner_css = '';

$classes .= ' color_' . $atts['color'];
if ( $atts['color'] == 'custom' ) {
	if ( $atts['bg_color'] != '' ) {
		$inner_css .= 'background-color:' . $atts['bg_color'] . ';';
	}
	if ( $atts['text_color'] != '' ) {
		$inner_css .= 'color:' . $atts['text_color'] . ';';
	}
}
$classes .= ' controls_' . $atts['controls'];

if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}

// Button keys that will be parsed
$btn_prefixes = array( 'btn' );
if ( $atts['second_button'] ) {
	$btn_prefixes[] = 'btn2';
}

// Preparing buttons
$buttons = array();
foreach ( $btn_prefixes as $prefix ) {
	if ( empty( $atts[ $prefix . '_label' ] ) ) {
		continue;
	}
	$btn_classes = '';
	$btn_inner_css = '';
	$btn_classes .= ' color_' . $atts[ $prefix . '_color' ];
	if ( $atts[ $prefix . '_color' ] == 'custom' ) {
		if ( $atts[ $prefix . '_bg_color' ] != '' ) {
			$btn_inner_css .= 'background-color: ' . $atts[ $prefix . '_bg_color' ] . ';';
		}
		if ( $atts[ $prefix . '_text_color' ] != '' ) {
			$btn_inner_css .= 'color: ' . $atts[ $prefix . '_text_color' ] . ';';
		}
	}
	$btn_classes .= ' style_' . $atts[ $prefix . '_style' ];

	if ( ! empty( $atts[ $prefix . '_size' ] ) AND $atts[ $prefix . '_size' ] != '15px' ) {
		$btn_inner_css .= 'font-size: ' . $atts[ $prefix . '_size' ] . ';';
	}

	$icon_html = '';
	if ( ! empty( $atts[ $prefix . '_icon' ] ) ) {
		$btn_classes .= ' icon_at' . $atts[ $prefix . '_iconpos' ];
		$icon_html = '<i class="' . us_prepare_icon_class( $atts[ $prefix . '_icon' ] ) . '"></i>';
	} else {
		$btn_classes .= ' icon_none';
	}

	$link = us_vc_build_link( $atts[ $prefix . '_link' ] );

	$buttons[ $prefix ] = '<a class="w-btn' . $btn_classes . '" href="' . $link['url'] . '"';
	$buttons[ $prefix ] .= ( $link['target'] == '_blank' ) ? ' target="_blank"' : '';
	$buttons[ $prefix ] .= ( $link['rel'] == 'nofollow' ) ? ' rel="nofollow"' : '';
	$buttons[ $prefix ] .= empty( $link['title'] ) ? '' : ( ' title="' . esc_attr( $link['title'] ) . '"' );
	if ( ! empty( $btn_inner_css ) ) {
		$buttons[ $prefix ] .= ' style="' . $btn_inner_css . '"';
	}
	$buttons[ $prefix ] .= '>' . $icon_html . '<span class="w-btn-label">' . $atts[ $prefix . '_label' ] . '</span></a>';
}

if ( ! empty( $inner_css ) ) {
	$inner_css = ' style="' . $inner_css . '"';
}

$output = '<div class="w-actionbox' . $classes . '"' . $inner_css . '><div class="w-actionbox-text">';
if ( ! empty( $atts['title'] ) ) {
	$output .= '<' . $atts['title_tag'] . '>' . html_entity_decode( $atts['title'] ) . '</' . $atts['title_tag'] . '>';
}
if ( ! empty( $content ) ) {
	$output .= '<p>' . do_shortcode( $content ) . '</p>';
}
//if ( ! empty( $atts['message'] ) ) {
//	$output .= '<p>' . html_entity_decode( $atts['message'] ) . '</p>';
//}
$output .= '</div>';

if ( ! empty( $buttons ) ) {
	$output .= '<div class="w-actionbox-controls">' . implode( '', $buttons ) . '</div>';
}

$output .= '</div>';
echo $output;
