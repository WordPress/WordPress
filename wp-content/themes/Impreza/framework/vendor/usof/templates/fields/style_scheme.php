<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Select Scheme
 *
 * Drop-down selector field.
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 *
 * @var $value string Current value
 */

// Could already be defined in parent function
if ( ! isset( $style_schemes ) ) {
	$style_schemes = us_config( 'style-schemes' );
}
if ( ! isset( $custom_style_schemes ) ) {
	$theme = wp_get_theme();
	if ( is_child_theme() ) {
		$theme = wp_get_theme( $theme->get( 'Template' ) );
	}
	$theme_name = $theme->get( 'Name' );

	$custom_style_schemes = get_option( 'usof_style_schemes_' . $theme_name );
	if ( ! is_array( $custom_style_schemes ) ) {
		$custom_style_schemes = array();
	}
}

$output = '<div class="usof-schemes"><ul class="usof-schemes-list">';
foreach ( $style_schemes as $key => &$style_scheme ) {
	$active_class = '';
	if ( $key == $value ) {
		$active_class = ' active';
	}
	$output .= '<li class="usof-schemes-item' . $active_class . '" data-id="' . $key . '">';
	$output .= '<div class="usof-schemes-item-title">' . $style_scheme['title'] . '</div>';
	$output .= '<div class="usof-schemes-item-colors">';
	$output .= '<i style="background-color:' . $style_scheme['values']['color_header_middle_bg'] . ';"></i>';
	$output .= '<i style="background-color:' . $style_scheme['values']['color_content_primary'] . ';"></i>';
	$output .= '<i style="background-color:' . $style_scheme['values']['color_content_secondary'] . ';"></i>';
	$output .= '<i style="background-color:' . $style_scheme['values']['color_content_bg'] . ';"></i>';
	$output .= '<i style="background-color:' . $style_scheme['values']['color_content_text'] . ';"></i>';
	$output .= '</div></li>';
}
foreach ( $custom_style_schemes as $key => &$style_scheme ) {
	$active_class = '';
	if ( 'custom-' . $key == $value ) {
		$active_class = ' active';
	}
	$output .= '<li class="usof-schemes-item type_custom' . $active_class . '" data-id="' . $key . '">';
	$output .= '<div class="usof-schemes-item-title">' . $style_scheme['title'] . '</div>';
	$output .= '<div class="usof-schemes-item-delete"></div>';
	$output .= '<div class="usof-schemes-item-colors">';
	$output .= '<i style="background-color:' . $style_scheme['values']['color_header_middle_bg'] . ';"></i>';
	$output .= '<i style="background-color:' . $style_scheme['values']['color_content_primary'] . ';"></i>';
	$output .= '<i style="background-color:' . $style_scheme['values']['color_content_secondary'] . ';"></i>';
	$output .= '<i style="background-color:' . $style_scheme['values']['color_content_bg'] . ';"></i>';
	$output .= '<i style="background-color:' . $style_scheme['values']['color_content_text'] . ';"></i>';
	$output .= '</div></li>';
}
$output .= '</ul>';
$output .= '<input type="hidden" name="' . $name . '" value="' . esc_attr( $value ) . '">';

// Save control
$output .= '<div class="usof-schemes-controls status_disabled">';
$output .= '<input type="text" id="style_scheme_name" value="" placeholder="' . __( 'Color Scheme Name', 'us' ) . '"/>';
$output .= '<button id="save_style_scheme" class="usof-button"><span>' . __( 'Save Color Scheme', 'us' ) . '</span>';
$output .= '<span class="usof-preloader"></span></button>';
$output .= '</div></div>';

// JSON data
$output .= '<div class="usof-form-row-control-schemes-json"' . us_pass_data_to_js( $style_schemes ) . '></div>';
$output .= '<div class="usof-form-row-control-custom-schemes-json"' . us_pass_data_to_js( $custom_style_schemes ) . '></div>';

$first_style_scheme = array_shift($style_schemes);
$style_scheme_colors = array_keys($first_style_scheme['values']);
$output .= '<div class="usof-form-row-control-colors-json"' . us_pass_data_to_js( $style_scheme_colors ) . '></div>';

$i18n = array(
	'delete_confirm' => __( 'Are you sure want to delete this Color Scheme?', 'us' ),
	'create_error_alert' => __( 'Please enter Color Scheme Name', 'us' ),
	'create_confirm' => __( 'Do you want save changes to the current Color Scheme?', 'us' ),
);
$output .= '<div class="usof-form-row-control-i18n"' . us_pass_data_to_js( $i18n ) . '></div>';

echo $output;
