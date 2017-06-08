<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_contacts
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['address'] string Addresss
 * @param $atts ['phone'] string Phone
 * @param $atts ['fax'] string Fax
 * @param $atts ['email'] string Email
 * @param $atts ['el_class'] string Extra class name
 */

$atts = us_shortcode_atts( $atts, 'us_contacts' );

// .w-contacts container additional classes and inner CSS-styles
$classes = '';

if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}
$output = '<div class="w-contacts' . $classes . '"><div class="w-contacts-list">';
if ( ! empty( $atts['address'] ) ) {
	$output .= '<div class="w-contacts-item for_address"><span class="w-contacts-item-value">' . $atts['address'] . '</span></div>';
}
if ( ! empty( $atts['phone'] ) ) {
	$output .= '<div class="w-contacts-item for_phone"><span class="w-contacts-item-value">' . $atts['phone'] . '</span></div>';
}
if ( ! empty( $atts['fax'] ) ) {
	$output .= '<div class="w-contacts-item for_fax"><span class="w-contacts-item-value">' . $atts['fax'] . '</span></div>';
}
if ( ! empty( $atts['email'] ) ) {
	$output .= '<div class="w-contacts-item for_email"><span class="w-contacts-item-value">';
	$output .= '<a href="mailto:' . $atts['email'] . '">' . $atts['email'] . '</a></span></div>';
}

$output .= '</div></div>';

echo $output;
