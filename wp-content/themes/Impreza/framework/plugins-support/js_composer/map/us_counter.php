<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_counter
 *
 * @var $shortcode string Current shortcode name
 * @var $config array Shortcode's config
 *
 * @param $config ['atts'] array Shortcode's attributes and default values
 */
vc_map( array(
	'base' => 'us_counter',
	'name' => __( 'Counter', 'us' ),
	'icon' => 'icon-wpb-ui-separator',
	'category' => us_translate_with_external_domain( 'Content', 'js_composer' ),
	'weight' => 190,
	'params' => array(
		array(
			'param_name' => 'initial',
			'heading' => __( 'The initial number value', 'us' ),
			'type' => 'textfield',
			'std' => $config['atts']['initial'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 120,
		),
		array(
			'param_name' => 'target',
			'heading' => __( 'The final number value', 'us' ),
			'type' => 'textfield',
			'std' => $config['atts']['target'],
			'holder' => 'span',
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 110,
		),
		array(
			'param_name' => 'prefix',
			'heading' => __( 'Prefix (optional)', 'us' ),
			'description' => __( 'Text before number', 'us' ),
			'type' => 'textfield',
			'std' => $config['atts']['prefix'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 100,
		),
		array(
			'param_name' => 'suffix',
			'heading' => __( 'Suffix (optional)', 'us' ),
			'description' => __( 'Text after number', 'us' ),
			'type' => 'textfield',
			'std' => $config['atts']['suffix'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 90,
		),
		array(
			'param_name' => 'color',
			'heading' => __( 'Number Color', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Heading (theme color)', 'us' ) => 'text',
				__( 'Primary (theme color)', 'us' ) => 'primary',
				__( 'Secondary (theme color)', 'us' ) => 'secondary',
				__( 'Custom Color', 'us' ) => 'custom',
			),
			'std' => $config['atts']['color'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 80,
		),
		array(
			'param_name' => 'size',
			'heading' => __( 'Number Size', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Small', 'us' ) => 'small',
				__( 'Medium', 'us' ) => 'medium',
				__( 'Large', 'us' ) => 'large',
			),
			'std' => $config['atts']['size'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 70,
		),
		array(
			'param_name' => 'custom_color',
			'type' => 'colorpicker',
			'std' => $config['atts']['custom_color'],
			'dependency' => array( 'element' => 'color', 'value' => 'custom' ),
			'weight' => 60,
		),
		array(
			'param_name' => 'title',
			'heading' => __( 'Title', 'us' ),
			'type' => 'textfield',
			'std' => $config['atts']['title'],
			'holder' => 'span',
			'weight' => 50,
		),
		array(
			'param_name' => 'title_tag',
			'heading' => __( 'Title Tag Name', 'us' ),
			'description' => __( 'Used for SEO purposes', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				'h1' => 'h1',
				'h2' => 'h2',
				'h3' => 'h3',
				'h4' => 'h4',
				'h5' => 'h5',
				'h6' => 'h6',
				'p' => 'p',
				'div' => 'div',
			),
			'std' => $config['atts']['title_tag'],
			'edit_field_class' => 'vc_col-sm-6',
			'dependency' => array( 'element' => 'title', 'not_empty' => TRUE ),
			'weight' => 40,
		),
		array(
			'param_name' => 'title_size',
			'heading' => __( 'Title Size', 'us' ),
			'description' => sprintf(__( 'Examples: %s', 'us' ), '26px, 1.3em, 200%'),
			'type' => 'textfield',
			'std' => $config['atts']['title_size'],
			'edit_field_class' => 'vc_col-sm-6',
			'dependency' => array( 'element' => 'title', 'not_empty' => TRUE ),
			'weight' => 30,
		),
		array(
			'param_name' => 'align',
			'heading' => __( 'Alignment', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Left', 'us' ) => 'left',
				__( 'Center', 'us' ) => 'center',
				__( 'Right', 'us' ) => 'right',
			),
			'std' => $config['atts']['align'],
			'weight' => 20,
		),
		array(
			'param_name' => 'el_class',
			'heading' => us_translate_with_external_domain( 'Extra class name', 'js_composer' ),
			'description' => us_translate_with_external_domain( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
			'type' => 'textfield',
			'std' => $config['atts']['el_class'],
			'weight' => 10,
		),
	),
) );

