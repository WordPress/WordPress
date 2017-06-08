<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Slider
 *
 * Slider-selector of the integer value within some range.
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['min'] int Minimum value
 * @param $field ['max'] int Maximum value
 * @param $field ['step'] float Sliding step
 * @param $field ['prefix'] string Number prefix
 * @param $field ['postfix'] string Number postfix
 *
 * @var $value string Current value
 */

$field['min'] = isset( $field['min'] ) ? floatval( $field['min'] ) : 0;
$field['max'] = isset( $field['max'] ) ? floatval( $field['max'] ) : 1000;

$output = '<div class="usof-slider" ';
$output .= 'data-min="' . $field['min'] . '" data-max="' . $field['max'] . '" ';
foreach ( array( 'step', 'prefix', 'postfix' ) as $field_name ) {
	if ( isset( $field[ $field_name ] ) AND ! empty( $field[ $field_name ] ) ) {
		$output .= 'data-' . $field_name . '="' . esc_attr( $field[ $field_name ] ) . '" ';
	}
}
$output .= '>';
// Using separate input for the value itself as text input may have a prefix or/and postfix
$output .= '<input type="hidden" name="' . $name . '" value="' . esc_attr( $value ) . '" />';
$output .= '<input type="text" value="';
if ( isset( $field['prefix'] ) AND ! empty( $field['prefix'] ) ) {
	$output .= esc_attr( $field['prefix'] );
}
$output .= esc_attr( $value );
if ( isset( $field['postfix'] ) AND ! empty( $field['postfix'] ) ) {
	$output .= esc_attr( $field['postfix'] );
}

$output .= '" />';
$output .= '<div class="usof-slider-box"><div class="usof-slider-box-h">';
if ( $field['max'] <= $field['min'] ) {
	// Wrong input parameters
	$offset = 100;
} else {
	$offset = ( min( $field['max'], max( $field['min'], $value ) ) - $field['min'] ) * 100 / ( $field['max'] - $field['min'] );
}
$output .= '<div class="usof-slider-range" style="left:' . $offset . '%;"><div class="usof-slider-runner" draggable="true"></div></div>';
$output .= '</div></div>';
$output .= '</div>';

echo $output;

