<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Checkboxes
 *
 * Multiple selector
 *
 * @var $id string Field ID
 * @var $name string Field name
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['options'] array List of key => title pairs
 *
 * @var $value array List of checked keys
 */

if ( ! is_array( $value ) ) {
	$value = array();
}

$output = '<ul class="usof-checkbox-list">';
foreach ( $field['options'] as $key => $option_title ) {
	$output .= '<li class="usof-checkbox">';
	$output .= '<input type="checkbox" id="' . $id . '_' . $key . '" name="' . $name . '" value="' . esc_attr( $key ) . '"';
	if ( in_array( $key, $value ) ) {
		$output .= ' checked="checked"';
	}
	$output .= '><label for="' . $id . '_' . $key . '">';
	$output .= '<span class="usof-checkbox-icon"></span><span class="usof-checkbox-text">' . $option_title . '</span>';
	$output .= '</label></li>';
}
$output .= '</ul>';

echo $output;
