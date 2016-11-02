<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Color
 *
 * Simple color picker
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['text'] string Field additional text
 *
 * @var $value string Current value
 */

if ( preg_match( '~^\#([\da-f])([\da-f])([\da-f])$~', $value, $matches ) ) {
	$value = '#' . $matches[1] . $matches[1] . $matches[2] . $matches[2] . $matches[3] . $matches[3];
}

$output = '<div class="usof-color">';
$output .= '<div class="usof-color-preview" style="background: ' . $value . '"></div>';
$output .= '<input class="usof-color-value" type="text" name="' . $name . '" value="' . esc_attr( $value ) . '" />';
$output .= '<div class="usof-color-clear" title="' . us_translate_with_external_domain( 'Clear' ) . '"></div>';
$output .= '</div>';
if ( isset( $field['text'] ) AND ! empty( $field['text'] ) ) {
	$output .= '<div class="usof-color-text">' . $field['text'] . '</div>';
}

echo $output;
