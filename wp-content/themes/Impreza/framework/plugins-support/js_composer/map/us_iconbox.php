<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_iconbox
 *
 * @var $shortcode string Current shortcode name
 * @var $config array Shortcode's config
 *
 * @param $config ['atts'] array Shortcode's attributes and default values
 * @param $config ['content'] string Shortcode's default content
 */
vc_map( array(
	'base' => 'us_iconbox',
	'name' => __( 'IconBox', 'us' ),
	'icon' => 'icon-wpb-ui-separator-label',
	'category' => us_translate_with_external_domain( 'Content', 'js_composer' ),
	'weight' => 280,
	'params' => array(
		array(
			'param_name' => 'icon',
			'heading' => __( 'Icon', 'us' ),
			'description' => sprintf( __( '%s or %s icon name', 'us' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>', '<a href="http://designjockey.github.io/material-design-fonticons/" target="_blank">Material Design</a>' ),
			'type' => 'textfield',
			'std' => $config['atts']['icon'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 120,
		),
		array(
			'param_name' => 'style',
			'heading' => __( 'Icon Style', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Simple', 'us' ) => 'default',
				__( 'Inside the Solid circle', 'us' ) => 'circle',
				__( 'Inside the Outlined circle', 'us' ) => 'outlined',
			),
			'std' => $config['atts']['style'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 110,
		),
		array(
			'param_name' => 'color',
			'heading' => __( 'Icon Color', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Primary (theme color)', 'us' ) => 'primary',
				__( 'Secondary (theme color)', 'us' ) => 'secondary',
				__( 'Light (theme color)', 'us' ) => 'light',
				__( 'Contrast (theme color)', 'us' ) => 'contrast',
				__( 'Custom colors', 'us' ) => 'custom',
			),
			'std' => $config['atts']['color'],
			'weight' => 100,
		),
		array(
			'param_name' => 'icon_color',
			'heading' => __( 'Icon Color', 'us' ),
			'type' => 'colorpicker',
			'std' => $config['atts']['icon_color'],
			'dependency' => array( 'element' => 'color', 'value' => 'custom' ),
			'weight' => 90,
		),
		array(
			'param_name' => 'bg_color',
			'heading' => __( 'Icon Circle Color', 'us' ),
			'type' => 'colorpicker',
			'std' => $config['atts']['bg_color'],
			'dependency' => array( 'element' => 'color', 'value' => 'custom' ),
			'weight' => 80,
		),
		array(
			'param_name' => 'iconpos',
			'heading' => __( 'Icon Position', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Top', 'us' ) => 'top',
				__( 'Left', 'us' ) => 'left',
			),
			'std' => $config['atts']['iconpos'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 70,
		),
		array(
			'param_name' => 'size',
			'heading' => __( 'Icon Size', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Tiny', 'us' ) => 'tiny',
				__( 'Small', 'us' ) => 'small',
				__( 'Medium', 'us' ) => 'medium',
				__( 'Large', 'us' ) => 'large',
				__( 'Huge', 'us' ) => 'huge',
			),
			'std' => $config['atts']['size'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 60,
		),
		array(
			'param_name' => 'title',
			'heading' => __( 'Title', 'us' ),
			'type' => 'textfield',
			'holder' => 'div',
			'std' => $config['atts']['title'],
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
			'weight' => 45,
		),
		array(
			'param_name' => 'title_size',
			'heading' => __( 'Title Size', 'us' ),
			'description' => sprintf(__( 'Examples: %s', 'us' ), '26px, 1.3em, 200%'),
			'type' => 'textfield',
			'std' => $config['atts']['title_size'],
			'edit_field_class' => 'vc_col-sm-6',
			'dependency' => array( 'element' => 'title', 'not_empty' => TRUE ),
			'weight' => 44,
		),
		array(
			'param_name' => 'content',
			'heading' => __( 'Iconbox content (optional)', 'us' ),
			'type' => 'textarea',
			'std' => $config['content'],
			'weight' => 40,
		),
		array(
			'param_name' => 'link',
			'heading' => __( 'Link (optional)', 'us' ),
			'type' => 'vc_link',
			'std' => $config['atts']['link'],
			'weight' => 30,
		),
		array(
			'param_name' => 'img',
			'heading' => __( 'Image (optional)', 'us' ),
			'description' => __( 'Set an image, which overrides the font icon', 'us' ),
			'type' => 'attach_image',
			'std' => $config['atts']['img'],
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
