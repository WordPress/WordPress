<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Switch
 *
 * On-off switcher
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['options'] array Array of two key => title pairs
 * @param $field ['text'] array Additional text to show right near the switcher
 *
 * @var $value string Current value
 */

if ( ! isset( $field['options'] ) OR empty( $field['options'] ) ) {
	$field['options'] = array(
		TRUE => 'On',
		FALSE => 'Off',
	);
}
$field_keys = array_keys( $field['options'] );
if ( count( $field_keys ) < 2 ) {
	return;
}

$output = '<div class="usof-switcher">';
$output .= '<input type="hidden" name="' . $name . '" value="' . esc_attr( $field_keys[1] ) . '" />';
$output .= '<input type="checkbox" id="' . $id . '" name="' . $name . '"' . checked( $value, $field_keys[0], FALSE ) . ' value="' . esc_attr( $field_keys[0] ) . '">';
$output .= '<label for="' . $id . '"><span class="usof-switcher-box">';
$output .= '</span><span class="usof-switcher-button"></span>';
if ( isset( $field['text'] ) AND ! empty( $field['text'] ) ) {
	$output .= '<span class="usof-switcher-text">' . $field['text'] . '</span>';
}
$output .= '</label></div>';

echo $output;

