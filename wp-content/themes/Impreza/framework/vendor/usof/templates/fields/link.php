<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Link
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
 *
 * @param $value ['url'] string Link URL
 * @param $value ['target'] string Link Target
 */

if ( ! is_array( $value ) ) {
	$value = array(
		'url' => is_string( $value ) ? $value : '',
	);
}
if ( ! isset( $value['url'] ) ) {
	$value['url'] = '';
}
if ( ! isset( $value['target'] ) ) {
	$value['target'] = '';
}
$output = '<input type="hidden" name="' . $name . '" value="' . esc_attr( json_encode($value) ) . '" />';
$output .= '<input type="text" value="' . esc_attr( $value['url'] ) . '"';
if ( isset( $field['placeholder'] ) AND ! empty( $field['placeholder'] ) ) {
	$output .= ' placeholder="' . esc_attr( $field['placeholder'] ) . '"';
}
$output .= ' />';

$output .= '<div class="usof-checkbox">';
$output .= '<input type="checkbox" id="' . $id . '_target"';
if ( $value['target'] == '_blank' ) {
	$output .= ' checked="checked"';
}
$output .= '><label for="' . $id . '_target">';
$output .= '<span class="usof-checkbox-icon"></span><span class="usof-checkbox-text">' . us_translate_with_external_domain( 'Open link in a new tab' ) . '</span>';
$output .= '</label>';
$output .= '</div>';

echo $output;
