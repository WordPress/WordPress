<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output image element
 *
 * @var $img string Path to image or WP Attachment ID
 * @var $link string Link URL
 * @var $img_transparent string Path to image or WP Attachment ID for transparent header state
 * @var $height int
 * @var $height_tablets int
 * @var $height_mobiles int
 * @var $height_sticky int
 * @var $height_sticky_tablets int
 * @var $height_sticky_mobiles int
 * @var $width int
 * @var $design_options array
 * @var $id string
 */

$classes = '';
if ( isset( $design_options ) AND isset( $design_options['hide_for_sticky'] ) AND $design_options['hide_for_sticky'] ) {
	$classes .= ' hide-for-sticky';
}
if ( ! empty( $img_transparent ) ) {
	$classes .= ' with_transparent';
}
if ( isset( $id ) AND ! empty( $id ) ) {
	$classes .= ' ush_' . str_replace( ':', '_', $id );
}

$output = '<div class="w-img' . $classes . '">';
$link_atts = usof_get_link_atts( $link );
if ( ! empty( $link_atts['href'] ) ) {
	$output .= '<a class="w-img-h" href="' . esc_attr( $link_atts['href'] ) . '"';
	if ( ! empty( $link_atts['target'] ) ) {
		$output .= ' target="' . esc_attr( $link_atts['target'] ) . '"';
	}
	$output .= '>';
} else {
	$output .= '<div class="w-img-h">';
}
foreach ( array( 'img', 'img_transparent' ) as $key ) {
	$$key = preg_replace( '~\|full$~', '|large', $$key );
	if ( empty( $$key ) OR ! ( $image = usof_get_image_src( $$key ) ) ) {
		continue;
	}
	$for = ( $key == 'img' ) ? 'default' : substr( $key, 4 );
	$output .= '<img class="for_' . $for . '" src="' . esc_url( $image[0] ) . '"';
	if ( ! empty( $image[1] ) AND ! empty( $image[2] ) ) {
		$output .= ' width="' . $image[1] . '" height="' . $image[2] . '"';
	}
	if ( $img_alt = usof_get_image_alt( $$key ) ) {
		$output .= ' alt="' . esc_attr( $img_alt ) . '"';
	}
	$output .= ' />';
}
if ( ! empty( $link_atts['href'] ) ) {
	$output .= '</a>';
} else {
	$output .= '</div>';
}
$output .= '</div>';

echo $output;

