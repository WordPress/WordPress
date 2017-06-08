<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output single USOF Field
 *
 * Multiple selector
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 * @var $values array Set of values for the current and relevant fields
 */

if ( isset( $field['place_if'] ) AND ! $field['place_if'] ) {
	return;
}
if ( ! isset( $field['type'] ) ) {
	if ( WP_DEBUG ) {
		wp_die( $name . ' has no defined type' );
	}

	return;
}
$show_field = ( ! isset( $field['show_if'] ) OR usof_execute_show_if( $field['show_if'], $values ) );
if ( $field['type'] == 'wrapper_start' ) {
	$row_classes = '';
	if ( isset( $field['classes'] ) AND ! empty( $field['classes'] ) ) {
		$row_classes .= ' ' . $field['classes'];
	}
	echo '<div class="usof-form-wrapper ' . $name . $row_classes . '" data-name="' . $name . '" data-id="' . $id . '" ';
	echo 'style="display: ' . ( $show_field ? 'block' : 'none' ) . '">';
	if ( isset( $field['title'] ) AND ! empty( $field['title'] ) ) {
		echo '<div class="usof-form-wrapper-title">' . $field['title'] . '</div>';
	}
	echo '<div class="usof-form-wrapper-cont">';
	if ( isset( $field['show_if'] ) AND is_array( $field['show_if'] ) AND ! empty( $field['show_if'] ) ) {
		// Showing conditions
		echo '<div class="usof-form-wrapper-showif"' . us_pass_data_to_js( $field['show_if'] ) . '></div>';
	}

	return;
} elseif ( $field['type'] == 'wrapper_end' ) {
	echo '</div></div>';

	return;
}

$field['std'] = isset( $field['std'] ) ? $field['std'] : NULL;
$value = isset( $values[ $name ] ) ? $values[ $name ] : $field['std'];

$row_classes = ' type_' . $field['type'];
if ( $field['type'] != 'message' AND ( ! isset( $field['classes'] ) OR strpos( $field['classes'], 'desc_' ) === FALSE ) ) {
	$row_classes .= ' desc_3';
}
if ( isset( $field['classes'] ) AND ! empty( $field['classes'] ) ) {
	$row_classes .= ' ' . $field['classes'];
}
echo '<div class="usof-form-row' . $row_classes . '" data-name="' . $name . '" data-id="' . $id . '" ';
echo 'style="display: ' . ( $show_field ? 'block' : 'none' ) . '">';
if ( isset( $field['title'] ) AND ! empty( $field['title'] ) ) {
	echo '<div class="usof-form-row-title"><span>' . $field['title'] . '</span></div>';
}
echo '<div class="usof-form-row-field"><div class="usof-form-row-control">';
// Including the field control itself
us_load_template( 'vendor/usof/templates/fields/' . $field['type'], array(
	'name' => $name,
	'id' => $id,
	'field' => $field,
	'value' => $value,
) );
echo '</div><!-- .usof-form-row-control -->';
if ( isset( $field['description'] ) AND ! empty( $field['description'] ) ) {
	echo '<div class="usof-form-row-desc">';
	echo '<div class="usof-form-row-desc-icon"></div>';
	echo '<div class="usof-form-row-desc-text">' . $field['description'] . '</div>';
	echo '</div>';
}
echo '<div class="usof-form-row-state"></div>';
echo '</div>'; // .usof-form-row-field
if ( isset( $field['show_if'] ) AND is_array( $field['show_if'] ) AND ! empty( $field['show_if'] ) ) {
	// Showing conditions
	echo '<div class="usof-form-row-showif"' . us_pass_data_to_js( $field['show_if'] ) . '></div>';
}
echo '</div><!-- .usof-form-row -->';
