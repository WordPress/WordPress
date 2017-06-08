<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Font
 *
 * Select font
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['preview'] array
 * @param $field ['preview']['text'] string Preview text
 * @param $field ['preview']['size'] string Font size in css format
 *
 * @var $value array List of checked keys
 */

$output = '';

$font_value = explode( '|', $value, 2 );
if ( ! isset( $font_value[1] ) OR empty( $font_value[1] ) ) {
	$font_value[1] = '400,700';
}

if ( ! isset( $web_safe_fonts ) ) {
	$web_safe_fonts = array(
		'Georgia, serif',
		'"Palatino Linotype", "Book Antiqua", Palatino, serif',
		'"Times New Roman", Times, serif',
		'Arial, Helvetica, sans-serif',
		'Impact, Charcoal, sans-serif',
		'"Lucida Sans Unicode", "Lucida Grande", sans-serif',
		'Tahoma, Geneva, sans-serif',
		'"Trebuchet MS", Helvetica, sans-serif',
		'Verdana, Geneva, sans-serif',
		'"Courier New", Courier, monospace',
		'"Lucida Console", Monaco, monospace',
	);
}

if ( ! isset( $google_fonts ) ) {
	$google_fonts = us_config( 'google-fonts', array() );
	$output .= '<div class="usof-fonts-json"' . us_pass_data_to_js( $google_fonts ) . '></div>';
}

$output .= '<div class="usof-font">';
$output .= '<input type="hidden" name="' . $name . '" value="' . esc_attr( $value ) . '" />';
$output .= '<select>';
$output .= '<option value="none" ' . selected( $font_value[0], 'none', FALSE ) . '>' . __( 'Font not specified', 'us' ) . '</option>';
$output .= '<optgroup label="' . __( 'Web safe font combinations (do not need to be loaded)', 'us' ) . '">';
foreach ( $web_safe_fonts as $font_name ) {
	$output .= "<option value='" . esc_attr( $font_name ) . "' " . selected( $font_value[0], $font_name, FALSE ) . '>' . $font_name . '</option>';
}
$output .= '</optgroup>';
$output .= '<optgroup label="' . __( 'Custom fonts (loaded from Google Fonts)', 'us' ) . '">';
foreach ( $google_fonts as $font_name => &$tmp_font_value ) {
	$output .= '<option value="' . esc_attr( $font_name ) . '"' . selected( $font_value[0], $font_name, FALSE ) . '>' . $font_name . '</option>';
}
$output .= '</optgroup>';
$output .= '</select>';

// Font preview
$field['preview'] = isset( $field['preview'] ) ? $field['preview'] : array();
$field['preview']['text'] = isset( $field['preview']['text'] ) ? $field['preview']['text'] : '0123456789 ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz';
$field['preview']['size'] = isset( $field['preview']['size'] ) ? $field['preview']['size'] : '20px';
$output .= '<div class="usof-font-preview" style="font-size: ' . $field['preview']['size'] . '">' . $field['preview']['text'] . '</div>';

// Font weights
if ( ! isset( $font_weights ) ) {
	$font_weights = array(
		'100' => __( 'Thin 100', 'us' ),
		'100italic' => __( 'Thin 100 Italic', 'us' ),
		'200' => __( 'Extra-Light 200', 'us' ),
		'200italic' => __( 'Extra-Light 200 Italic', 'us' ),
		'300' => __( 'Light 300', 'us' ),
		'300italic' => __( 'Light 300 Italic', 'us' ),
		'400' => __( 'Normal 400', 'us' ),
		'400italic' => __( 'Normal 400 Italic', 'us' ),
		'500' => __( 'Medium 500', 'us' ),
		'500italic' => __( 'Medium 500 Italic', 'us' ),
		'600' => __( 'Semi-Bold 600', 'us' ),
		'600italic' => __( 'Semi-Bold 600 Italic', 'us' ),
		'700' => __( 'Bold 700', 'us' ),
		'700italic' => __( 'Bold 700 Italic', 'us' ),
		'800' => __( 'Extra-Bold 800', 'us' ),
		'800italic' => __( 'Extra-Bold 800 Italic', 'us' ),
		'900' => __( 'Ultra-Bold 900', 'us' ),
		'900italic' => __( 'Ultra-Bold 900 Italic', 'us' ),
	);
}
$font_value[1] = explode( ',', $font_value[1] );
$show_weights = (array) us_config( 'google-fonts.' . $font_value[0] . '.variants', array() );

$output .= '<ul class="usof-checkbox-list">';
foreach ( $font_weights as $font_weight => $font_title ) {
	$output .= '<li class="usof-checkbox' . ( in_array( $font_weight, $show_weights ) ? '' : ' hidden' ) . '" data-value="' . $font_weight . '">';
	$output .= '<input type="checkbox" id="' . $id . '_weight_' . $font_weight . '" ';
	$output .= 'value="' . $font_weight . '"';
	$output .= ( in_array( $font_weight, $font_value[1] ) ? ' checked' : '' );
	$output .= '><label for="' . $id . '_weight_' . $font_weight . '" ';
	$output .= 'class="' . ( in_array( $font_weight, $show_weights ) ? '' : 'hidden' ) . '">';
	$output .= '<span class="usof-checkbox-icon"></span><span class="usof-checkbox-text">';
	$output .= $font_title . '</span></label></li>';
}
$output .= '</ul>';

$output .= '</div>';

echo $output;
