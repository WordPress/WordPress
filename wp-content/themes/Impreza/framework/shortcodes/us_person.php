<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_person
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['name'] string Name
 * @param $atts ['role'] string Role
 * @param $atts ['image'] int Photo (from WP Media Library)
 * @param $atts ['layout'] string Layout: 'card' / 'flat' / 'circle' / 'square'
 * @param $atts ['link'] string Link in a serialized format: 'url:http%3A%2F%2Fwordpress.org|title:WP%20Website|target:_blank|rel:nofollow'
 * @param $atts ['email'] string Email
 * @param $atts ['facebook'] string Facebook link
 * @param $atts ['twitter'] string Twitter link
 * @param $atts ['google_plus'] string Google+ link
 * @param $atts ['linkedin'] string LinkedIn link
 * @param $atts ['skype'] string Skype link
 * @param $atts ['custom_icon'] string Custom icon
 * @param $atts ['custom_link'] string Custom link
 * @param $atts ['el_class'] string Extra class name
 */

$atts = us_shortcode_atts( $atts, 'us_person' );

$classes = '';

$classes .= ' layout_' . $atts['layout'];

$img_html = '';
if ( is_numeric( $atts['image'] ) ) {
	$img = wp_get_attachment_image_src( intval( $atts['image'] ), 'tnail-1x1-small' );
	if ( $img !== FALSE ) {
		$img_html = '<img src="' . $img[0] . '" width="' . $img[1] . '" height="' . $img[2] . '" alt="' . esc_attr( $atts['name'] ) . '" itemprop="image">';
	}
} elseif ( ! empty( $atts['image'] ) ) {
	// Direct link to image is set in the shortcode attribute
	$img_html = '<img src="' . $atts['image'] . '" alt="' . esc_attr( $atts['name'] ) . '">';
}

$links_html = '';
if ( ! empty( $atts['email'] ) ) {
	$links_html .= '<a class="w-person-links-item" href="mailto:' . $atts['email'] . '"><i class="fa fa-envelope"></i></a>';
}
if ( ! empty( $atts['facebook'] ) ) {
	$links_html .= '<a class="w-person-links-item" href="' . esc_url( $atts['facebook'] ) . '" target="_blank"><i class="fa fa-facebook"></i></a>';
}
if ( ! empty( $atts['twitter'] ) ) {
	$links_html .= '<a class="w-person-links-item" href="' . esc_url( $atts['twitter'] ) . '" target="_blank"><i class="fa fa-twitter"></i></a>';
}
if ( ! empty( $atts['google_plus'] ) ) {
	$links_html .= '<a class="w-person-links-item" href="' . esc_url( $atts['google_plus'] ) . '" target="_blank"><i class="fa fa-google-plus"></i></a>';
}
if ( ! empty( $atts['linkedin'] ) ) {
	$links_html .= '<a class="w-person-links-item" href="' . esc_url( $atts['linkedin'] ) . '" target="_blank"><i class="fa fa-linkedin"></i></a>';
}
if ( ! empty( $atts['skype'] ) ) {
	// Skype link may be some http(s): or skype: link. If protocol is not set, adding "skype:"
	$skype_url = $atts['skype'];
	if ( strpos( $skype_url, ':' ) === FALSE ) {
		$skype_url = 'skype:' . esc_attr( $skype_url );
	}
	$links_html .= '<a class="w-person-links-item" href="' . $skype_url . '"><i class="fa fa-skype"></i></a>';
}
$atts['custom_icon'] = trim( $atts['custom_icon'] );
if ( ! empty( $atts['custom_icon'] ) AND ! empty( $atts['custom_link'] ) ) {
	$links_html .= '<a class="w-person-links-item" href="' . esc_url( $atts['custom_link'] ) . '" target="_blank"><i class="' . us_prepare_icon_class( $atts['custom_icon'] ) . '"></i></a>';
}
if ( ! empty( $links_html ) ) {
	$classes .= ' with_icons';
	$links_html = '<div class="w-person-links"><div class="w-person-links-list">' . $links_html . '</div></div>';
}

$link_start = $link_end = '';
$link = us_vc_build_link( $atts['link'] );

if ( ! empty( $link['url'] ) ) {
	$link_target = ( $link['target'] == '_blank' ) ? ' target="_blank"' : '';
	$link_rel = ( $link['rel'] == 'nofollow' ) ? ' rel="nofollow"' : '';
	$link_title = empty( $link['title'] ) ? '' : ( ' title="' . esc_attr( $link['title'] ) . '"' );
	$link_start = '<a class="w-person-link" href="' . esc_url( $link['url'] ) . '"' . $link_target . $link_rel . $link_title . '>';
	$link_end = '</a>';
}

$role_part = '';

if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}

$output = '<div class="w-person' . $classes . '" itemscope="itemscope" itemtype="https://schema.org/Person">';
$output .= '<div class="w-person-image">';
$output .= $link_start . $img_html . $link_end;
if ( ! in_array( $atts['layout'], array( 'card', 'flat' ) ) ) {
	$output .= $links_html;
}
$output .= '</div>';
$output .= '<div class="w-person-content">';
if ( ! empty( $atts['name'] ) ) {
	$output .= $link_start . '<h4 class="w-person-name" itemprop="name"><span>' . $atts['name'] . '</span></h4>' . $link_end;
}
if ( ! empty( $atts['role'] ) ) {
	$output .= '<div class="w-person-role" itemprop="jobTitle">' . $atts['role'] . '</div>';
}
if ( ! empty( $content ) ) {
	$output .= '<div class="w-person-description" itemprop="description">' . do_shortcode( $content ) . '</div>';
}
if ( in_array( $atts['layout'], array( 'card', 'flat' ) ) ) {
	$output .= $links_html;
}
$output .= '</div></div>';

echo $output;
