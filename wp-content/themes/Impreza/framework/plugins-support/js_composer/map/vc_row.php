<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Extending shortcode: vc_row
 *
 * @var $shortcode string Current shortcode name
 * @var $config array Shortcode's config
 *
 * @param $config ['atts'] array Shortcode's attributes and default values
 */
vc_remove_param( 'vc_row', 'full_width' );
vc_remove_param( 'vc_row', 'full_height' );
vc_remove_param( 'vc_row', 'content_placement' );
vc_remove_param( 'vc_row', 'video_bg' );
vc_remove_param( 'vc_row', 'video_bg_url' );
vc_remove_param( 'vc_row', 'video_bg_parallax' );
vc_remove_param( 'vc_row', 'gap' );
vc_remove_param( 'vc_row', 'columns_placement' );
vc_remove_param( 'vc_row', 'equal_height' );
vc_remove_param( 'vc_row', 'parallax_speed_video' );
vc_remove_param( 'vc_row', 'parallax_speed_bg' );
if ( ! vc_is_page_editable() ) {
	vc_remove_param( 'vc_row', 'parallax' );
	vc_remove_param( 'vc_row', 'parallax_image' );
}
vc_add_params( 'vc_row', array(
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
		'weight' => 190,
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
		'weight' => 180,
	),
	array(
		'param_name' => 'height',
		'heading' => __( 'Row Height', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			__( 'No paddings', 'us' ) => 'auto',
			__( 'Small paddings', 'us' ) => 'small',
			__( 'Medium paddings', 'us' ) => 'medium',
			__( 'Large paddings', 'us' ) => 'large',
			__( 'Huge paddings', 'us' ) => 'huge',
			__( 'Full Screen', 'us' ) => 'full',
		),
		'std' => $config['atts']['height'],
		'weight' => 170,
	),
	array(
		'param_name' => 'valign',
		'type' => 'checkbox',
		'dependency' => array( 'element' => 'height', 'value' => 'full' ),
		'value' => array( __( 'Center content of this row vertically', 'us' ) => 'center' ),
		( ( $config['atts']['valign'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['valign'],
		'weight' => 160,
	),
	array(
		'param_name' => 'width',
		'heading' => __( 'Full Width Content', 'us' ),
		'type' => 'checkbox',
		'value' => array( __( 'Stretch content of this row to the screen width', 'us' ) => 'full' ),
		( ( $config['atts']['width'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['width'],
		'weight' => 150,
	),
	array(
		'param_name' => 'color_scheme',
		'heading' => __( 'Row Color Style', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			__( 'Content colors', 'us' ) => '',
			__( 'Alternate Content colors', 'us' ) => 'alternate',
			__( 'Primary bg & White text', 'us' ) => 'primary',
			__( 'Secondary bg & White text', 'us' ) => 'secondary',
			__( 'Custom colors', 'us' ) => 'custom',
		),
		'std' => $config['atts']['color_scheme'],
		'weight' => 140,
	),
	array(
		'param_name' => 'us_bg_color',
		'heading' => __( 'Background Color', 'us' ),
		'type' => 'colorpicker',
		'std' => $config['atts']['us_bg_color'],
		'edit_field_class' => 'vc_col-sm-6',
		'dependency' => array( 'element' => 'color_scheme', 'value' => 'custom' ),
		'weight' => 130,
	),
	array(
		'param_name' => 'us_text_color',
		'heading' => __( 'Text Color', 'us' ),
		'type' => 'colorpicker',
		'std' => $config['atts']['us_text_color'],
		'dependency' => array( 'element' => 'color_scheme', 'value' => 'custom' ),
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 120,
	),
	array(
		'param_name' => 'us_bg_image',
		'heading' => __( 'Background Image', 'us' ),
		'type' => 'attach_image',
		'std' => $config['atts']['us_bg_image'],
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 100,
	),
	array(
		'param_name' => 'us_bg_size',
		'heading' => __( 'Background Image Size', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			__( 'Cover - Image will cover the whole area', 'us' ) => 'cover',
			__( 'Contain - Image will fit inside the area', 'us' ) => 'contain',
			__( 'Initial', 'us' ) => 'initial',
		),
		'std' => $config['atts']['us_bg_size'],
		'dependency' => array( 'element' => 'us_bg_image', 'not_empty' => TRUE ),
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 90,
	),
	array(
		'param_name' => 'us_bg_repeat',
		'heading' => __( 'Background Image Repeat', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			__( 'Repeat', 'us' ) => 'repeat',
			__( 'Repeat Horizontally', 'us' ) => 'repeat-x',
			__( 'Repeat Vertically', 'us' ) => 'repeat-y',
			__( 'Do Not Repeat', 'us' ) => 'no-repeat',
		),
		'std' => $config['atts']['us_bg_repeat'],
		'dependency' => array( 'element' => 'us_bg_image', 'not_empty' => TRUE ),
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 88,
	),
	array(
		'param_name' => 'us_bg_pos',
		'heading' => __( 'Background Image Position', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			__( 'Top Left', 'us' ) => 'top left',
			__( 'Top Center', 'us' ) => 'top center',
			__( 'Top Right', 'us' ) => 'top right',
			__( 'Center Left', 'us' ) => 'center left',
			__( 'Center Center', 'us' ) => 'center center',
			__( 'Center Right', 'us' ) => 'center right',
			__( 'Bottom Left', 'us' ) => 'bottom left',
			__( 'Bottom Center', 'us' ) => 'bottom center',
			__( 'Bottom Right', 'us' ) => 'bottom right',
		),
		'std' => $config['atts']['us_bg_pos'],
		'dependency' => array( 'element' => 'us_bg_image', 'not_empty' => TRUE ),
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 85,
	),
	array(
		'param_name' => 'us_bg_parallax',
		'heading' => __( 'Parallax Effect', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			__( 'None', 'us' ) => '',
			__( 'Vertical Parallax', 'us' ) => 'vertical',
			__( 'Horizontal Parallax', 'us' ) => 'horizontal',
			__( 'Still (Image doesn\'t move)', 'us' ) => 'still',
		),
		'std' => $config['atts']['us_bg_parallax'],
		'dependency' => array( 'element' => 'us_bg_image', 'not_empty' => TRUE ),
		'weight' => 80,
	),
	array(
		'param_name' => 'us_bg_parallax_width',
		'heading' => __( 'Parallax Background Width', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			'110%' => '110',
			'120%' => '120',
			'130%' => '130',
			'140%' => '140',
			'150%' => '150',
		),
		'std' => $config['atts']['us_bg_parallax_width'],
		'dependency' => array( 'element' => 'us_bg_parallax', 'value' => 'horizontal' ),
		'weight' => 70,
	),
	array(
		'param_name' => 'us_bg_parallax_reverse',
		'type' => 'checkbox',
		'value' => array( __( 'Reverse Vertical Parallax Effect', 'us' ) => TRUE ),
		( ( $config['atts']['us_bg_parallax_reverse'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['us_bg_parallax_reverse'],
		'dependency' => array( 'element' => 'us_bg_parallax', 'value' => 'vertical' ),
		'weight' => 60,
	),
	array(
		'param_name' => 'us_bg_video',
		'heading' => __( 'Background Video', 'us' ),
		'description' => __( 'Link to video file (mp4, webm, ogg)', 'us' ),
		'type' => 'textfield',
		'std' => $config['atts']['us_bg_video'],
		'weight' => 50,
	),
	array(
		'param_name' => 'us_bg_overlay_color',
		'heading' => __( 'Background Overlay', 'us' ),
		'type' => 'colorpicker',
		'std' => $config['atts']['us_bg_overlay_color'],
		'holder' => 'div',
		'weight' => 10,
	),
) );
if ( class_exists( 'Ultimate_VC_Addons' ) ) {
	vc_add_param( 'vc_row', array(
		'param_name' => 'us_notification',
		'type' => 'ult_param_heading',
		'text' => __( 'Background Image, Background Video, Background Overlay settings located below will override the settings located at "Background" and "Effect" tabs.', 'us' ),
		'edit_field_class' => 'ult-param-important-wrapper ult-dashicon vc_column vc_col-sm-12',
		'weight' => 110,
	) );
}

// Setting proper shortcode order in VC shortcodes listing
vc_map_update( 'vc_row', array( 'weight' => 390 ) );
