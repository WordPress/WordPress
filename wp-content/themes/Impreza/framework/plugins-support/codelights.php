<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! class_exists( 'Vc_Manager' ) OR ! function_exists( 'cl_config' ) ) {
	return;
}

/**
 * CodeLights Compatibility
 * @link https://wordpress.org/plugins/codelights-shortcodes-and-widgets/
 */

add_action( 'vc_after_set_mode', 'us_provide_codelights_support' );
function us_provide_codelights_support() {

	$related_types = array(
		'html' => 'textarea_html',
		'textfield' => 'textfield',
		'textarea' => 'textarea',
		'select' => 'dropdown',
		'image' => 'attach_image',
		'images' => 'attach_images',
		'color' => 'colorpicker',
		'link' => 'vc_link',
		'checkboxes' => 'checkbox',
	);

	foreach ( cl_config( 'elements' ) as $name => $elm ) {
		$vc_elm = array(
			'name' => isset( $elm['title'] ) ? $elm['title'] : $name,
			'base' => $name,
			'description' => isset( $elm['description'] ) ? $elm['description'] : '',
			'class' => 'elm-' . $name,
			'category' => isset( $elm['category'] ) ? $elm['category'] : us_translate_with_external_domain( 'Content', 'js_composer' ),
			'icon' => isset( $elm['icon'] ) ? $elm['icon'] : '',
			'params' => array(),
		);

		if ( isset( $elm['params'] ) AND is_array( $elm['params'] ) ) {
			foreach ( $elm['params'] as $param_name => &$param ) {
				$vc_param = array(
					'type' => ( isset( $param['type'] ) AND isset( $related_types[$param['type']] ) ) ? $related_types[$param['type']] : 'textfield',
					'heading' => isset( $param['title'] ) ? $param['title'] : '',
					'param_name' => $param_name,
					'description' => isset( $param['description'] ) ? $param['description'] : '',
					'std' => isset( $param['std'] ) ? $param['std'] : '',
				);
				if ( isset( $param['classes'] ) AND ! empty( $param['classes'] ) ) {
					$vc_param['edit_field_class'] = preg_replace( '~(^|[^\w])cl_col~', '$1vc_col', $param['classes'] );
				}
				if ( isset( $param['group'] ) AND ! empty( $param['group'] ) ) {
					$vc_param['group'] = $param['group'];
				}
				if ( ( $vc_param['type'] == 'dropdown' OR $vc_param['type'] == 'checkbox' ) AND isset( $param['options'] ) ) {
					$vc_param['value'] = array_flip( $param['options'] );
				}
				// Proper dependency rules
				if ( isset( $param['show_if'] ) AND count( $param['show_if'] ) == 3 ) {
					$vc_param['dependency'] = array(
						'element' => $param['show_if'][0],
						'value' => $param['show_if'][2],
					);
				}
				$vc_elm['params'][] = $vc_param;
			}
		}

		vc_map( $vc_elm );
	}
}

add_filter( 'cl_image_sizes_select_values', 'us_add_image_sizes_to_codelights' );
function us_add_image_sizes_to_codelights( $sizes ) {
	return array_flip( us_image_sizes_select_values() );
}