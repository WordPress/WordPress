<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_portfolio
 *
 * @var $shortcode string Current shortcode name
 * @var $config array Shortcode's config
 *
 * @param $config ['atts'] array Shortcode's attributes and default values
 */
$us_portfolio_categories = array();
$us_portfolio_categories_raw = get_categories( array(
	'taxonomy' => 'us_portfolio_category',
	'hierarchical' => 0,
) );
if ( $us_portfolio_categories_raw ) {
	foreach ( $us_portfolio_categories_raw as $portfolio_category_raw ) {
		if ( is_object( $portfolio_category_raw ) ) {
			$us_portfolio_categories[ $portfolio_category_raw->name ] = $portfolio_category_raw->slug;
		}
	}
}
vc_map( array(
	'base' => 'us_portfolio',
	'name' => __( 'Portfolio Grid', 'us' ),
	'icon' => 'icon-wpb-ui-separator-label',
	'category' => us_translate_with_external_domain( 'Content', 'js_composer' ),
	'weight' => 250,
	'params' => array(
		array(
			'param_name' => 'columns',
			'heading' => __( 'Columns', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
			),
			'std' => $config['atts']['columns'],
			'admin_label' => TRUE,
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 120,
		),
		array(
			'param_name' => 'orderby',
			'heading' => _x( 'Order', 'sequence of items', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'By date (newer first)', 'us' ) => 'date',
				__( 'By date (older first)', 'us' ) => 'date_asc',
				__( 'Alphabetically', 'us' ) => 'alpha',
				__( 'Random', 'us' ) => 'rand',
			),
			'std' => $config['atts']['orderby'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 110,
		),
		array(
			'param_name' => 'items',
			'heading' => __( 'Items Quantity', 'us' ),
			'description' => __( 'If left blank, will output all the items', 'us' ),
			'type' => 'textfield',
			'std' => $config['atts']['items'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 100,
		),
		array(
			'param_name' => 'pagination',
			'heading' => __( 'Pagination', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'No pagination', 'us' ) => 'none',
				__( 'Regular pagination', 'us' ) => 'regular',
				__( 'Load More Button', 'us' ) => 'ajax',
				__( 'Infinite Scroll', 'us' ) => 'infinite',
			),
			'std' => $config['atts']['pagination'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 90,
		),
		array(
			'param_name' => 'ratio',
			'heading' => __( 'Items Ratio', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( '4:3 (landscape)', 'us' ) => '4x3',
				__( '3:2 (landscape)', 'us' ) => '3x2',
				__( '1:1 (square)', 'us' ) => '1x1',
				__( '2:3 (portrait)', 'us' ) => '2x3',
				__( '3:4 (portrait)', 'us' ) => '3x4',
				__( 'Initial', 'us' ) => 'initial',
			),
			'std' => $config['atts']['ratio'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 80,
		),
		array(
			'param_name' => 'meta',
			'heading' => __( 'Items Meta', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Do not show', 'us' ) => '',
				__( 'Show Item date', 'us' ) => 'date',
				__( 'Show Item categories', 'us' ) => 'categories',
				__( 'Show Item description', 'us' ) => 'desc',
			),
			'std' => $config['atts']['meta'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 70,
		),
		array(
			'param_name' => 'with_indents',
			'type' => 'checkbox',
			'value' => array( __( 'Add indents between items', 'us' ) => TRUE ),
			( ( $config['atts']['with_indents'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['with_indents'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 50,
		),
		array(
			'param_name' => 'style',
			'heading' => __( 'Items Style', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				sprintf( __( 'Style %d', 'us' ), 1 ) => 'style_1',
				sprintf( __( 'Style %d', 'us' ), 2 ) => 'style_2',
				sprintf( __( 'Style %d', 'us' ), 3 ) => 'style_3',
				sprintf( __( 'Style %d', 'us' ), 4 ) => 'style_4',
				sprintf( __( 'Style %d', 'us' ), 5 ) => 'style_5',
				sprintf( __( 'Style %d', 'us' ), 6 ) => 'style_6',
				sprintf( __( 'Style %d', 'us' ), 7 ) => 'style_7',
				sprintf( __( 'Style %d', 'us' ), 8 ) => 'style_8',
				sprintf( __( 'Style %d', 'us' ), 9 ) => 'style_9',
				sprintf( __( 'Style %d', 'us' ), 10 ) => 'style_10',
				sprintf( __( 'Style %d', 'us' ), 11 ) => 'style_11',
				sprintf( __( 'Style %d', 'us' ), 12 ) => 'style_12',
				sprintf( __( 'Style %d', 'us' ), 13 ) => 'style_13',
				sprintf( __( 'Style %d', 'us' ), 14 ) => 'style_14',
				sprintf( __( 'Style %d', 'us' ), 15 ) => 'style_15',
				sprintf( __( 'Style %d', 'us' ), 16 ) => 'style_16',
				sprintf( __( 'Style %d', 'us' ), 17 ) => 'style_17',
				sprintf( __( 'Style %d', 'us' ), 18 ) => 'style_18',
			),
			'std' => $config['atts']['style'],
			'admin_label' => TRUE,
			'edit_field_class' => 'vc_col-sm-6',
			'group' => __( 'Styling', 'us' ),
			'weight' => 16,
		),
		array(
			'param_name' => 'align',
			'heading' => __( 'Items Text Alignment', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Left', 'us' ) => 'left',
				__( 'Center', 'us' ) => 'center',
				__( 'Right', 'us' ) => 'right',
			),
			'std' => $config['atts']['align'],
			'edit_field_class' => 'vc_col-sm-6',
			'group' => __( 'Styling', 'us' ),
			'weight' => 15,
		),
		array(
			'param_name' => 'title_size',
			'heading' => __( 'Items Title Size', 'us' ),
			'description' => sprintf(__( 'Examples: %s', 'us' ), '26px, 1.3em, 200%'),
			'type' => 'textfield',
			'std' => $config['atts']['title_size'],
			'edit_field_class' => 'vc_col-sm-6',
			'group' => __( 'Styling', 'us' ),
			'weight' => 14,
		),
		array(
			'param_name' => 'meta_size',
			'heading' => __( 'Items Meta Size', 'us' ),
			'description' => sprintf(__( 'Examples: %s', 'us' ), '26px, 1.3em, 200%'),
			'type' => 'textfield',
			'std' => $config['atts']['meta_size'],
			'edit_field_class' => 'vc_col-sm-6',
			'group' => __( 'Styling', 'us' ),
			'weight' => 13,
		),
		array(
			'param_name' => 'bg_color',
			'heading' => __( 'Items Background Color', 'us' ),
			'type' => 'colorpicker',
			'std' => $config['atts']['bg_color'],
			'edit_field_class' => 'vc_col-sm-6',
			'group' => __( 'Styling', 'us' ),
			'weight' => 12,
		),
		array(
			'param_name' => 'text_color',
			'heading' => __( 'Items Text Color', 'us' ),
			'type' => 'colorpicker',
			'std' => $config['atts']['text_color'],
			'edit_field_class' => 'vc_col-sm-6',
			'group' => __( 'Styling', 'us' ),
			'weight' => 11,
		),
		array(
			'param_name' => 'filter',
			'type' => 'checkbox',
			'value' => array( __( 'Enable filtering by category', 'us' ) => 'category' ),
			( ( $config['atts']['filter'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['filter'],
			'group' => __( 'Filtering', 'us' ),
			'weight' => 9,
		),
		array(
			'param_name' => 'filter_style',
			'heading' => __( 'Filter Bar Style', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				sprintf( __( 'Style %d', 'us' ), 1 ) => 'style_1',
				sprintf( __( 'Style %d', 'us' ), 2 ) => 'style_2',
				sprintf( __( 'Style %d', 'us' ), 3 ) => 'style_3',
			),
			'std' => $config['atts']['filter_style'],
			'group' => __( 'Filtering', 'us' ),
			'dependency' => array( 'element' => 'filter', 'not_empty' => TRUE ),
			'weight' => 8,
		),
	),

) );
if ( ! empty( $us_portfolio_categories ) ) {
	vc_add_param( 'us_portfolio', array(
		'param_name' => 'categories',
		'heading' => __( 'Display Items of selected categories', 'us' ),
		'type' => 'checkbox',
		'value' => $us_portfolio_categories,
		'std' => $config['atts']['categories'],
		'weight' => 30,
	) );
}
vc_add_param( 'us_portfolio', array(
	'param_name' => 'el_class',
	'heading' => us_translate_with_external_domain( 'Extra class name', 'js_composer' ),
	'description' => us_translate_with_external_domain( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
	'type' => 'textfield',
	'std' => $config['atts']['el_class'],
	'group' => __( 'Styling', 'us' ),
	'weight' => 10,
) );
