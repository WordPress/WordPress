<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_single_image
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['image'] int WordPress media library image ID
 * @param $atts ['size'] string Image size: 'large' / 'medium' / 'thumbnail' / 'full'
 * @param $atts ['align'] string Image alignment: '' / 'left' / 'center' / 'right'
 * @param $atts ['lightbox'] bool Enable lignbox with th original image on click
 * @param $atts ['link'] string Image link in a serialized format: 'url:http%3A%2F%2Fwordpress.org|title:WP%20Website|target:_blank|rel:nofollow'
 * @param $atts ['frame'] string Frame type: 'none' / 'phone6-1' / 'phone6-2' / 'phone6-3' / 'phone6-4'
 * @param $atts ['animate'] string Animation type: '' / 'fade' / 'afc' / 'afl' / 'afr' / 'afb' / 'aft' / 'hfc' / 'wfc'
 * @param $atts ['animate_delay'] float Animation delay (in seconds)
 * @param $atts ['el_class'] string Extra class name
 * @param $atts ['css'] string Custom CSS
 */

$atts = us_shortcode_atts( $atts, 'us_single_image' );

$classes = '';
$frame_classes = '';

// Link attributes' values
$link = array();

$img_id = intval( $atts['image'] );

if ( $img_id AND ( $image_html = wp_get_attachment_image( $img_id, $atts['size'] ) ) ) {
	// We got image
	if ( $atts['lightbox'] ) {
		$link['url'] = wp_get_attachment_image_src( $img_id, 'full' );
		$link['url'] = ( $link['url'] ) ? $link['url'][0] : $image[0];
		$link['ref'] = 'magnificPopup';
	}
} else {
	// In case of any image issue using placeholder so admin could understand it quickly
	// TODO Move placeholder URL to some config
	global $us_template_directory_uri;
	$placeholder_url = $us_template_directory_uri . '/framework/img/us-placeholder-square.png';
	$image_html = '<img src="' . $placeholder_url . '" width="500" height="500" alt="">';
}

if ( ! $atts['lightbox'] AND ! empty( $atts['link'] ) ) {
	// Passing params from vc_link field type
	$link = array_merge( $link, us_vc_build_link( $atts['link'] ) );
}

if ( ! empty( $link['url'] ) ) {
	$link_html = '<a href="' . esc_url( $link['url'] ) . '"';
	unset( $link['url'] );
	foreach ( $link as $key => $value ) {
		$link_html .= ' ' . $key . '="' . esc_attr( $value ) . '"';
	}
	$link_html .= '>';
	$image_html = $link_html . $image_html . '</a>';
}

if ( $atts['align'] != '' ) {
	$classes .= ' align_' . $atts['align'];
}
if ( $atts['animate'] != '' ) {
	$classes .= ' animate_' . $atts['animate'];
	if ( ! empty( $atts['animate_delay'] ) ) {
		$atts['animate_delay'] = floatval( $atts['animate_delay'] );
		$classes .= ' d' . intval( $atts['animate_delay'] * 5 );
	}
}

if ( $atts['frame'] != '' AND $atts['frame'] != 'none') {
	$classes .= ' us-frame-wrapper';
	$frame_classes .= ' ' . $atts['frame'];
}

if ( ! empty( $atts['css'] ) AND function_exists( 'vc_shortcode_custom_css_class' ) ) {
	$classes .= ' ' . vc_shortcode_custom_css_class( $atts['css'] );
}

if ( $atts['el_class'] != '' ) {
	$classes .= ' ' . $atts['el_class'];
}

$output = '<div class="w-image' . $classes . '">';
$output .= '<div class="us-frame' . $frame_classes . '">' . $image_html . '</div>';
$output .= '</div>';

echo $output;
