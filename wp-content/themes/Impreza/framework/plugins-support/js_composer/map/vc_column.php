<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Extending shortcode: vc_column
 *
 * @var $shortcode string Current shortcode name
 * @var $config array Shortcode's config
 *
 * @param $config ['atts'] array Shortcode's attributes and default values
 */
vc_add_params( 'vc_column', array(
	array(
		'param_name' => 'text_color',
		'heading' => __( 'Text Color', 'us' ),
		'type' => 'colorpicker',
		'std' => $config['atts']['text_color'],
		'weight' => 30,
	),
	array(
		'param_name' => 'animate',
		'heading' => __( 'Animation', 'us' ),
		'description' => __( 'Select animation type if you want this element to be animated when it enters into the browsers viewport. Note: Works only in modern browsers.', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			__( 'No Animation', 'us' ) => '',
			__( 'Fade', 'us' ) => 'fade',
			__( 'Appear From Center', 'us' ) => 'afc',
			__( 'Appear From Left', 'us' ) => 'afl',
			__( 'Appear From Right', 'us' ) => 'afr',
			__( 'Appear From Bottom', 'us' ) => 'afb',
			__( 'Appear From Top', 'us' ) => 'aft',
			__( 'Height From Center', 'us' ) => 'hfc',
			__( 'Width From Center', 'us' ) => 'wfc',
		),
		'std' => $config['atts']['animate'],
		'admin_label' => TRUE,
		'weight' => 20,
	),
	array(
		'param_name' => 'animate_delay',
		'heading' => __( 'Animation Delay', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			__( 'None', 'us' ) => '',
			__( '0.2 second', 'us' ) => '0.2',
			__( '0.4 second', 'us' ) => '0.4',
			__( '0.6 second', 'us' ) => '0.6',
			__( '0.8 second', 'us' ) => '0.8',
			__( '1 second', 'us' ) => '1',
		),
		'std' => $config['atts']['animate_delay'],
		'dependency' => array( 'element' => 'animate', 'not_empty' => TRUE ),
		'admin_label' => TRUE,
		'weight' => 10,
	),
) );
