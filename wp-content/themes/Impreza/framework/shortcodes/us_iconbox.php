<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_iconbox
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['icon'] string Icon
 * @param $atts ['style'] string Icon style: 'default' / 'circle' / 'outlined'
 * @param $atts ['color'] string Icon color: 'primary' / 'secondary' / 'light' / 'contrast' / 'custom'
 * @param $atts ['icon_color'] string Icon color value
 * @param $atts ['bg_color'] string Icon circle color
 * @param $atts ['iconpos'] string Icon position: 'top' / 'left'
 * @param $atts ['size'] string Icon size: 'tiny' / 'small' / 'medium' / 'large' / 'huge'
 * @param $atts ['title'] string Title
 * @param $atts ['title_tag'] string Title Tag Name: 'div' / 'h2'/ 'h3'/ 'h4'/ 'h5'/ 'h6'/ 'p'
 * @param $atts ['title_size'] string Title Size
 * @param $atts ['link'] string Link in a serialized format: 'url:http%3A%2F%2Fwordpress.org|title:WP%20Website|target:_blank|rel:nofollow'
 * @param $atts ['img'] int Icon image (from WordPress media)
 * @param $atts ['el_class'] string Extra class name
 */
$atts = us_shortcode_atts( $atts, 'us_iconbox' );

$classes = '';
$icon_inner_css = '';

$classes .= ' iconpos_' . $atts['iconpos'];
$classes .= ' size_' . $atts['size'];
$classes .= ' style_' . $atts['style'];

$classes .= ' color_' . $atts['color'];
if ( $atts['color'] == 'custom' ) {
	if ( $atts['bg_color'] != '' ) {
		$icon_inner_css .= 'background-color: ' . $atts['bg_color'] . ';box-shadow: 0 0 0 2px ' . $atts['bg_color'] . ' inset;';
	}
	if ( $atts['icon_color'] != '' ) {
		$icon_inner_css .= 'color: ' . $atts['icon_color'] . ';';
	}
}

if ( $atts['title'] == '' AND $content == '' ) {
	$classes .= ' no_text';
}

// If image is set, using it as an icon
$icon_html = '';
if ( $atts['img'] != '' ) {
	$classes .= ' icontype_img';
	if ( is_numeric( $atts['img'] ) ) {
		$img = wp_get_attachment_image_src( intval( $atts['img'] ), 'full' );
		if ( $img !== FALSE ) {
			$icon_html = '<img src="' . $img[0] . '" width="' . $img[1] . '" height="' . $img[2] . '" alt="'.$atts['title'].'">';
		}
	} else {
		// Direct link to image is set in the shortcode attribute
		$icon_html = '<img src="' . $atts['img'] . '" alt="'.$atts['title'].'">';
	}
} else {
	$atts['icon'] = trim( $atts['icon'] );
	if ( $atts['icon'] != '' ) {
		$icon_html = '<i class="' . us_prepare_icon_class( $atts['icon'] ) . '"></i>';
	}
}

$link_opener = '';
$link_closer = '';
$link = us_vc_build_link( $atts['link'] );
if ( ! empty( $link['url'] ) ) {
	$link_target = ( $link['target'] == '_blank' ) ? ' target="_blank"' : '';
	$link_rel = ( $link['rel'] == 'nofollow' ) ? ' rel="nofollow"' : '';
	$link_title = empty( $link['title'] ) ? '' : ( ' title="' . esc_attr( $link['title'] ) . '"' );
	$link_opener = '<a class="w-iconbox-link" href="' . esc_url( $link['url'] ) . '"' . $link_target . $link_rel . $link_title . '>';
	$link_closer = '</a>';
}

if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}

if ( $icon_inner_css != '' ) {
	$icon_inner_css = ' style="' . $icon_inner_css . '"';
}

$output = '<div class="w-iconbox' . $classes . '">';
$output .= $link_opener;
$output .= '<div class="w-iconbox-icon"' . $icon_inner_css . '>' . $icon_html . '</div>';
if ( $atts['title'] != '' ) {
	$title_inner_css = '';
	if ( $atts['title_size'] != '' ) {
		$title_inner_css = ' style="font-size: ' . $atts['title_size'] . '"';
	}
	$output .= '<' . $atts['title_tag'] . ' class="w-iconbox-title"' . $title_inner_css . '>' . $atts['title'] . '</' . $atts['title_tag'] . '>';
}
$output .= $link_closer;
if ( $content != '' ) {
	$output .= '<div class="w-iconbox-text">' . do_shortcode( $content ) . '</div>';
}
$output .= '</div>';

echo $output;
