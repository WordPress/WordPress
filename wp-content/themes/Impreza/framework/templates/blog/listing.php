<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a single blog listing. Universal template that is used by all the possible blog posts listings.
 *
 * (!) $query_args should be filtered before passing to this template.
 *
 * @var $query_args array Arguments for the new WP_Query. If not set, current global $wp_query will be used instead.
 * @var $layout_type string Blog layout: classic / flat / tiles / cards / smallcircle / smallsquare / compact / related / latest
 * @var $columns int Number of columns 1 / 2 / 3 / 4
 * @var $masonry boolean Enable Masonry layout mode?
 * @var $metas array Meta data that should be shown: array('date', 'author', 'categories', 'tags', 'comments')
 * @var $title_size string Posts Title Size
 * @var $content_type string Content type: 'excerpt' / 'content' / 'none'
 * @var $show_read_more boolean Show "Read more" link after the excerpt?
 * @var $pagination string Pagination type: regular / none / ajax / infinite
 * @var $filter string Filter type: 'none' / 'category'
 * @var $filter_style string Filter Bar style: 'style_1' / 'style_2' / ... / 'style_N
 * @var $categories string Comma-separated list of categories slugs to show
 * @var $el_class string Additional classes that will be appended to the main .w-blog container
 *
 * @action Before the template: 'us_before_template:templates/blog/listing'
 * @action After the template: 'us_after_template:templates/blog/listing'
 * @filter Template variables: 'us_template_vars:templates/blog/listing'
 */


// Variables defaults and filtering
$layout_type = isset( $layout_type ) ? $layout_type : 'classic';
$masonry = ( in_array( $layout_type, array( 'classic', 'flat', 'tiles', 'cards' ) ) AND isset( $masonry ) ) ? $masonry : FALSE;
$columns = ( isset( $columns ) ) ? intval( $columns ) : 2;
$default_metas = array( 'date', 'author', 'categories', 'tags', 'comments' );
$metas = ( isset( $metas ) AND is_array( $metas ) ) ? array_intersect( $metas, $default_metas ) : $default_metas;
$title_size = ( isset( $title_size ) ) ? $title_size : '';
$content_type = isset( $content_type ) ? $content_type : 'excerpt';
$show_read_more = isset( $show_read_more ) ? $show_read_more : TRUE;
$pagination = isset( $pagination ) ? $pagination : 'regular';
$has_pagination = ( $pagination != 'none' );
$el_class = isset( $el_class ) ? $el_class : '';

if ($pagination == 'infinite') {
	$is_infinite = TRUE;
	$pagination = 'ajax';
}

// .w-blog container additional classes and inner CSS-styles
$classes = '';
$inner_css = '';

// Permalink for pagination render
$blog_permalink = get_permalink();

// Filtering and executing database query
global $wp_query;
$use_custom_query = isset( $query_args ) AND is_array( $query_args ) AND ! empty( $query_args );
if ( $use_custom_query ) {
	us_open_wp_query_context();
	$wp_query = new WP_Query( $query_args );
} else {
	$query_args = $wp_query->query;
	// Extracting query arguments from WP_Query that are not shown but relevant
	if ( ! isset( $query_args['post_type'] ) AND preg_match_all( '~\.post_type = \'([a-z0-9\_\-]+)\'~', $wp_query->request, $matches ) ) {
		$query_args['post_type'] = $matches[1];
	}
	if ( ! isset( $query_args['post_status'] ) AND preg_match_all( '~\.post_status = \'([a-z]+)\'~', $wp_query->request, $matches ) ) {
		$query_args['post_status'] = $matches[1];
	}
}

if ( ! have_posts() ) {
	// TODO Move to a separate variable
	_e( 'No posts were found.', 'us' );

	return;
}

$classes .= ' layout_' . $layout_type;

$classes .= ' cols_' . $columns;

if ( in_array( 'categories', $metas ) ) {
	$classes .= ' with_categories';
}

if ( ! empty( $el_class ) ) {
	$classes .= ' ' . $el_class;
}

// Grabbing all the categories with a single request
$categories_args = array(
	'hierarchical' => FALSE,
);
if ( ! empty( $categories ) ) {
	$categories_args['slug'] = explode( ',', $categories );
}
$filter_categories = get_categories( $categories_args );


$available_filter_styles = array( 'style_1', 'style_2', 'style_3' );
$filter_style = ( isset( $filter_style ) AND in_array( $filter_style, $available_filter_styles ) ) ? $filter_style : 'style_1';

$filter_html = '';
$filter = isset( $filter ) ? $filter : 'none';
if ( $filter == 'category' ) {
	// $categories_names already contains only the used categories
	if ( count( $filter_categories ) > 1 ) {
		$classes .= ' with_filters';
		$filter_html .= '<div class="g-filters ' . $filter_style . '"><div class="g-filters-list">';
		$filter_html .= '<div class="g-filters-item active" data-category="*"><span>' . __( 'All', 'us' ) . '</span></div>';
		foreach ( $filter_categories as $filter_category ) {
			$filter_html .= '<div class="g-filters-item" data-category="' . $filter_category->slug . '"><span>' . $filter_category->name . '</span></div>';
		}
		$filter_html .= '</div></div>';
	}
}

// We'll need the isotope script for masonry mode and for filtration
if ( ( ! empty( $filter_html ) AND ( $pagination == 'none' OR $wp_query->max_num_pages == 1 ) ) OR ( $masonry AND $columns > 1 ) ) {
	wp_enqueue_script( 'us-isotope' );
	$classes .= ' with_isotope';

	if ( ! $masonry ) {
		$classes .= ' isotope_fit_rows';
	}
}

if ( $masonry ) {
	$classes .= ' masonry';
}

?><div class="w-blog<?php echo $classes ?>" itemscope="itemscope" itemtype="https://schema.org/Blog"><?php
echo $filter_html;
if ( ( $wp_query->max_num_pages > 1 ) AND ( $pagination == 'regular' OR $filter == 'category' ) ) {
	?>
	<div class="w-blog-preloader">
		<div class="g-preloader type_1"></div>
	</div>
	<?php
}
?><div class="w-blog-list"><?php

// Preparing template settings for loop post template
$template_vars = array(
	'layout_type' => $layout_type,
	'masonry' => $masonry,
	'metas' => $metas,
	'title_size' => $title_size,
	'columns' => $columns,
	'content_type' => $content_type,
	'show_read_more' => $show_read_more,
);

// Start the loop.
while ( have_posts() ){
	the_post();

	us_load_template( 'templates/blog/listing-post', $template_vars );
}

?></div><?php

if ( $wp_query->max_num_pages > 1 ) {
	// Next page elements may have sliders, so we preloading the needed assets now
	// TODO On-demand ajax assets usage
	wp_enqueue_script( 'us-royalslider' );
	wp_enqueue_style('us-royalslider');

	// Passing g-loadmore options to JavaScript via onclick event
	$json_data = array(
		// Controller options
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'permalink_url' => $blog_permalink,
		'action' => 'us_ajax_blog',
		'max_num_pages' => $wp_query->max_num_pages,
		'infinite_scroll' => ( ( isset( $is_infinite ) ) ? $is_infinite : 0 ),
		// Blog listing template variables that will be passed to this file in the next call
		'template_vars' => array(
			'query_args' => $query_args,
			'layout_type' => $layout_type,
			'masonry' => $masonry,
			'metas' => $metas,
			'title_size' => $title_size,
			'columns' => $columns,
			'content_type' => $content_type,
			'show_read_more' => $show_read_more,
			'is_shortcode' => isset( $is_shortcode ) AND $is_shortcode,
		),
	);
	if ( $pagination != 'none' ) {
	?>
	<div class="w-blog-json hidden"<?php echo us_pass_data_to_js( $json_data ) ?>></div>
	<?php
	}
	if ( $pagination == 'regular' ) {
		?><div class="g-pagination"><?php
		the_posts_pagination( array(
			'prev_text' => '<',
			'next_text' => '>',
			'mid_size' => 3,
			'before_page_number' => '<span>',
			'after_page_number' => '</span>',
		) );
		?></div><?php
	} elseif ( $pagination == 'ajax' ) {
		?><div class="g-loadmore">
			<div class="g-loadmore-btn">
				<span><?php _e( 'Load More', 'us' ) ?></span>
			</div>
			<div class="g-preloader type_1"></div>
		</div><?php
	}
}

?></div><?php

if ( $use_custom_query ) {
	// Cleaning up
	us_close_wp_query_context();
}
