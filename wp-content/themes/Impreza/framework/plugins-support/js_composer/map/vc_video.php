<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Modifying shortcode: vc_video
 *
 * @var $shortcode string Current shortcode name
 * @var $config array Shortcode's config
 *
 * @param $config ['atts'] array Shortcode's attributes and default values
 */

vc_remove_param( 'vc_video', 'title' );
vc_remove_param( 'vc_video', 'el_width' );
vc_remove_param( 'vc_video', 'el_aspect' );
vc_add_params( 'vc_video', array(
	array(
		'param_name' => 'link',
		'heading' => __( 'Video link', 'us' ),
		'description' => sprintf( __( 'Link to the video. More about supported formats at <a href="%s" target="_blank">WordPress codex page</a>.', 'us' ), 'http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F' ),
		'type' => 'textfield',
		'std' => $config['atts']['link'],
		'admin_label' => TRUE,
		'weight' => 60,
	),
	array(
		'param_name' => 'ratio',
		'heading' => __( 'Ratio', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			'16x9' => '16x9',
			'4x3' => '4x3',
			'3x2' => '3x2',
			'1x1' => '1x1',
		),
		'std' => $config['atts']['ratio'],
		'weight' => 50,
	),
	array(
		'param_name' => 'max_width',
		'heading' => __( 'Max Width in pixels', 'us' ),
		'type' => 'textfield',
		'std' => $config['atts']['max_width'],
		'admin_label' => TRUE,
		'weight' => 40,
	),
	array(
		'param_name' => 'align',
		'heading' => __( 'Video Alignment', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			__( 'Left', 'us' ) => 'left',
			__( 'Center', 'us' ) => 'center',
			__( 'Right', 'us' ) => 'right',
		),
		'std' => $config['atts']['align'],
		'dependency' => array( 'element' => 'max_width', 'not_empty' => TRUE ),
		'weight' => 30,
	),
) );

// Setting proper shortcode order in VC shortcodes listing
vc_map_update( 'vc_video', array( 'weight' => 210 ) );
