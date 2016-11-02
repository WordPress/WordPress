<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Extending shortcode: vc_row_inner
 *
 * @var $shortcode string Current shortcode name
 * @var $config array Shortcode's config
 *
 * @param $config ['atts'] array Shortcode's attributes and default values
 */
vc_remove_param( 'vc_row_inner', 'gap' );
vc_remove_param( 'vc_row_inner', 'equal_height' );
vc_remove_param( 'vc_row_inner', 'content_placement' );
vc_add_params( 'vc_row_inner', array(
	array(
		'param_name' => 'content_placement',
		'heading' => __( 'Columns Content Position', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			__( 'Default', 'us' ) => 'default',
			__( 'Top', 'us' ) => 'top',
			__( 'Middle', 'us' ) => 'middle',
			__( 'Bottom', 'us' ) => 'bottom',
		),
		'std' => $config['atts']['content_placement'],
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 20,
	),
	array(
		'param_name' => 'columns_type',
		'heading' => __( 'Columns Layout', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			__( 'With Small gaps', 'us' ) => 'small',
			__( 'With Medium gaps', 'us' ) => 'medium',
			__( 'With Large gaps', 'us' ) => 'large',
			__( 'Boxed and without gaps', 'us' ) => 'none',
		),
		'std' => $config['atts']['columns_type'],
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 10,
	),
) );
