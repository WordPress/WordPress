<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Modifying shortcode: vc_tta_accordion
 *
 * @var $shortcode string Current shortcode name
 * @var $config array Shortcode's config
 *
 * @param $config ['atts'] array Shortcode's attributes and default values
 */
if ( version_compare( WPB_VC_VERSION, '4.6', '<' ) ) {
	// Oops: the modified shorcode doesn't exist in current VC version. Doing nothing.
	return;
}

if ( ! vc_is_page_editable() ) {
	vc_remove_param( 'vc_tta_accordion', 'title' );
	vc_remove_param( 'vc_tta_accordion', 'style' );
	vc_remove_param( 'vc_tta_accordion', 'shape' );
	vc_remove_param( 'vc_tta_accordion', 'color' );
	vc_remove_param( 'vc_tta_accordion', 'no_fill' );
	vc_remove_param( 'vc_tta_accordion', 'spacing' );
	vc_remove_param( 'vc_tta_accordion', 'gap' );
	vc_remove_param( 'vc_tta_accordion', 'autoplay' );
	vc_remove_param( 'vc_tta_accordion', 'collapsible_all' );
	vc_remove_param( 'vc_tta_accordion', 'active_section' );
	vc_remove_param( 'vc_tta_accordion', 'c_align' );
	vc_remove_param( 'vc_tta_accordion', 'c_icon' );
	vc_remove_param( 'vc_tta_accordion', 'c_position' );
	vc_add_params( 'vc_tta_accordion', array(
		array(
			'param_name' => 'toggle',
			'heading' => __( 'Act as Toggles', 'us' ),
			'type' => 'checkbox',
			'value' => array( __( 'Allow several sections to be opened at the same time', 'us' ) => TRUE ),
			( ( $config['atts']['toggle'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['toggle'],
			'weight' => 60,
		),
		array(
			'param_name' => 'c_align',
			'heading' => __( 'Title Alignment', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Left', 'us' ) => 'left',
				__( 'Right', 'us' ) => 'right',
				__( 'Center', 'us' ) => 'center',
			),
			'std' => $config['atts']['c_align'],
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
			'weight' => 40,
		),
		array(
			'param_name' => 'title_size',
			'heading' => __( 'Title Size', 'us' ),
			'description' => sprintf(__( 'Examples: %s', 'us' ), '26px, 1.3em, 200%'),
			'type' => 'textfield',
			'std' => $config['atts']['title_size'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 30,
		),
		array(
			'param_name' => 'c_icon',
			'heading' => __( 'Icon', 'us' ),
			'description' => __( 'Select accordion navigation icon.', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'None', 'us' ) => '',
				__( 'Chevron', 'us' ) => 'chevron',
				__( 'Plus', 'us' ) => 'plus',
				__( 'Triangle', 'us' ) => 'triangle',
			),
			'std' => $config['atts']['c_icon'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 20,
		),
		array(
			'param_name' => 'c_position',
			'heading' => __( 'Icon Position', 'us' ),
			'description' => __( 'Select accordion navigation icon position.', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Left', 'us' ) => 'left',
				__( 'Right', 'us' ) => 'right',
			),
			'std' => $config['atts']['c_position'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 10,
		),
	) );
}

// Setting proper shortcode order in VC shortcodes listing
vc_map_update( 'vc_tta_accordion', array( 'weight' => 310 ) );
