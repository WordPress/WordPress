<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output text element
 *
 * @var $text string
 * @var $size int Text size
 * @var $size_tablets int Text size for tablets
 * @var $size_mobiles int Text size for mobiles
 * @var $link string Link
 * @var $icon string Font Awesome or Material Design icon
 * @var $font string Font Source
 * @var $color string Custom text color
 * @var $design_options array
 * @var $id string
 */

$classes = '';
if ( isset( $design_options ) AND isset( $design_options['hide_for_sticky'] ) AND $design_options['hide_for_sticky'] ) {
	$classes .= ' hide-for-sticky';
}
if ( isset( $id ) AND ! empty( $id ) ) {
	$classes .= ' ush_' . str_replace( ':', '_', $id );
}
if ( isset( $font ) AND ! empty( $font ) ) {
	$classes .= ' font_' . $font;
}
$output = '<div class="w-text' . $classes . '"><div class="w-text-h">';
if ( ! empty( $icon ) ) {
	$output .= '<i class="' . us_prepare_icon_class( $icon ) . '"></i>';
}
$link_atts = usof_get_link_atts( $link );
if ( ! empty( $link_atts['href'] ) ) {
	$output .= '<a class="w-text-value" href="' . esc_attr( $link_atts['href'] ) . '"';
	if ( ! empty( $link_atts['target'] ) ) {
		$output .= ' target="' . esc_attr( $link_atts['target'] ) . '"';
	}
	$output .= '>';
} else {
	$output .= '<span class="w-text-value">';
}
$output .= strip_tags( $text, '<strong><br>' . ( empty( $link ) ? '<a>' : '' ) );
if ( ! empty( $link_atts['href'] ) ) {
	$output .= '</a>';
} else {
	$output .= '</span>';
}
$output .= '</div></div>';

echo $output;
