<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_portfolio
 *
 * Listing of portfolio items.
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['columns'] int Columns number: 2 / 3 / 4 / 5
 * @param $atts ['pagination'] string Pagination type: 'none' / 'regular' / 'ajax' / 'infinite'
 * @param $atts ['items'] int Number of items per page (left empty to display all the items)
 * @param $atts ['style'] string Items style: 'style_1' / 'style_2' / ... / 'style_N'
 * @param $atts ['align'] string Items text alignment: 'left' / 'center' / 'right'
 * @param $atts ['ratio'] string Items ratio: '3x2' / '4x3' / '1x1' / '2x3' / '3x4' / 'initial'
 * @param $atts ['meta'] string Items meta: '' / 'date' / 'categories' / 'desc'
 * @param $atts ['with_indents'] bool Add indents between items?
 * @param $atts ['orderby'] string Posts order: 'date' / 'rand'
 * @param $atts ['categories'] string Comma-separated list of categories slugs
 * @param $atts ['title_size'] string Title Font Size
 * @param $atts ['meta_size'] string Meta Font Size
 * @param $atts ['text_color'] string
 * @param $atts ['bg_color'] string
 * @param $atts ['el_class'] string Extra class name
 * @param $atts ['filter'] string Filter type: 'none' / 'category'
 * @param $atts ['filter_style'] string Filter Bar style: 'style_1' / 'style_2' / ... / 'style_N'
 */

$atts = us_shortcode_atts( $atts, 'us_portfolio' );

$template_vars = array(
	'categories' => $atts['categories'],
	'style_name' => $atts['style'],
	'columns' => $atts['columns'],
	'ratio' => $atts['ratio'],
	'metas' => array( 'title', $atts['meta'] ),
	'align' => $atts['align'],
	'with_indents' => $atts['with_indents'],
	'pagination' => $atts['pagination'],
	'orderby' => ( in_array( $atts['orderby'], array ( 'date', 'date_asc', 'alpha', 'rand' ) ) ) ? $atts['orderby'] : 'date',
	'perpage' => intval( $atts['items'] ),
	'title_size' => $atts['title_size'],
	'meta_size' => $atts['meta_size'],
	'text_color' => $atts['text_color'],
	'bg_color' => $atts['bg_color'],
	'el_class' => $atts['el_class'],
	'filter' => $atts['filter'],
	'filter_style' => $atts['filter_style'],
);

// Current page
if ( $atts['pagination'] == 'regular' ) {
	$request_paged = is_front_page() ? 'page' : 'paged';
	if ( get_query_var( $request_paged ) ) {
		$template_vars['page'] = get_query_var( $request_paged );
	}
}

us_load_template( 'templates/portfolio/listing', $template_vars );
