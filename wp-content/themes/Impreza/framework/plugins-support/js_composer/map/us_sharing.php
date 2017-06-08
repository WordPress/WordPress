<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_sharing
 *
 * @var $shortcode string Current shortcode name
 * @var $config array Shortcode's config
 *
 * @param $config ['atts'] array Shortcode's attributes and default values
 */
vc_map( array(
	'base' => 'us_sharing',
	'name' => __( 'Sharing Buttons', 'us' ),
	'icon' => 'icon-wpb-ui-sharing',
	'category' => us_translate_with_external_domain( 'Content', 'js_composer' ),
	'weight' => 185,
	'params' => array(
		array(
			'param_name' => 'type',
			'heading' => __( 'Buttons Type', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Simple', 'us' ) => 'simple',
				__( 'Solid', 'us' ) => 'solid',
				__( 'Outlined', 'us' ) => 'outlined',
				__( 'Fixed', 'us' ) => 'fixed',
			),
			'std' => $config['atts']['type'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 130,
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
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 120,
		),
		array(
			'param_name' => 'color',
			'heading' => __( 'Color Style', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Default brands colors', 'us' ) => 'default',
				__( 'Primary (theme color)', 'us' ) => 'primary',
				__( 'Secondary (theme color)', 'us' ) => 'secondary',
			),
			'std' => $config['atts']['color'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 110,
		),
		array(
			'param_name' => 'counters',
			'heading' => __( 'Share Counters', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Show counters', 'us' ) => 'show',
				__( 'Don\'t show counters', 'us' ) => 'hide',
			),
			'std' => $config['atts']['counters'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 100,
		),
		array(
			'param_name' => 'email',
			'type' => 'checkbox',
			'value' => array( 'Email' => TRUE ),
			( ( $config['atts']['email'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['email'],
			'edit_field_class' => 'vc_col-sm-4',
			'weight' => 90,
		),
		array(
			'param_name' => 'facebook',
			'type' => 'checkbox',
			'value' => array( 'Facebook' => TRUE ),
			( ( $config['atts']['facebook'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['facebook'],
			'edit_field_class' => 'vc_col-sm-4',
			'weight' => 80,
		),
		array(
			'param_name' => 'twitter',
			'type' => 'checkbox',
			'value' => array( 'Twitter' => TRUE ),
			( ( $config['atts']['twitter'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['twitter'],
			'edit_field_class' => 'vc_col-sm-4',
			'weight' => 70,
		),
		array(
			'param_name' => 'gplus',
			'type' => 'checkbox',
			'value' => array( 'Google+' => TRUE ),
			( ( $config['atts']['gplus'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['gplus'],
			'edit_field_class' => 'vc_col-sm-4',
			'weight' => 60,
		),
		array(
			'param_name' => 'linkedin',
			'type' => 'checkbox',
			'value' => array( 'LinkedIn' => TRUE ),
			( ( $config['atts']['linkedin'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['linkedin'],
			'edit_field_class' => 'vc_col-sm-4',
			'weight' => 50,
		),
		array(
			'param_name' => 'pinterest',
			'type' => 'checkbox',
			'value' => array( 'Pinterest' => TRUE ),
			( ( $config['atts']['pinterest'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['pinterest'],
			'edit_field_class' => 'vc_col-sm-4',
			'weight' => 40,
		),
		array(
			'param_name' => 'vk',
			'type' => 'checkbox',
			'value' => array( 'Vkontakte' => TRUE ),
			( ( $config['atts']['vk'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['vk'],
			'edit_field_class' => 'vc_col-sm-4',
			'weight' => 30,
		),
		array(
			'param_name' => 'url',
			'heading' => __( 'Sharing URL (optional)', 'us' ),
			'description' => __( 'If not specified, the opened page URL will be used by default', 'us' ),
			'type' => 'textfield',
			'std' => $config['atts']['url'],
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
