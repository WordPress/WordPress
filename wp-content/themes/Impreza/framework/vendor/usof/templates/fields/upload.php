<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Upload
 *
 * Upload some file with the specified settings.
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['extension'] string Comma-separated list of available extensions
 * @param $field ['label'] string Main button label
 *
 * @var $value mixed Either full path to the file, or ID from WordPress media uploads
 */

$field['label'] = isset( $field['label'] ) ? $field['label'] : __( 'Set Image', 'us' );

$upload_image = '';
if ( ! empty( $value ) ) {
	$upload_image = usof_get_image_src( $value, 'medium' );
}

$output = '<div class="usof-upload">';
$output .= '<input type="hidden" name="' . $name . '" value="' . $value . '">';
$output .= '<div class="usof-button type_set" style="display: ' . ( $upload_image ? 'none' : 'inline-block' ) . '">';
$output .= '<span class="usof-button-label">' . $field['label'] . '</span>';
$output .= '</div>';
$output .= '<div class="usof-upload-container" style="display: ' . ( $upload_image ? 'block' : 'none' ) . '">';
if ( $upload_image ) {
	$output .= '<img src="' . esc_attr( $upload_image[0] ) . '" alt="" />';
} else {
	$output .= '<img src="" alt="" />';
}
$output .= '<div class="usof-upload-controls">';
$output .= '<div class="usof-button type_change"><span>' . __( 'Change', 'us' ) . '</span></div>';
$output .= '<div class="usof-button type_remove"><span>' . __( 'Remove', 'us' ) . '</span></div>';
$output .= '</div>';
$output .= '</div>';
$output .= '</div>';

echo $output;

unset( $upload_image );
