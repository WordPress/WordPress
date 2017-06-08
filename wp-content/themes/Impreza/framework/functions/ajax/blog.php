<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ajax method for blogs ajax pagination.
 */
add_action( 'wp_ajax_nopriv_us_ajax_blog', 'us_ajax_blog' );
add_action( 'wp_ajax_us_ajax_blog', 'us_ajax_blog' );
function us_ajax_blog() {

	WPBMap::addAllMappedShortcodes();

	// Filtering $template_vars, as is will be extracted to the template as local variables
	$template_vars = shortcode_atts( array(
		'query_args' => array(),
		'layout_type' => 'classic',
		'masonry' => FALSE,
		'title_size' => '',
		'metas' => array(),
		'columns' => 2,
		'content_type' => 'none',
		'show_read_more' => FALSE,
		'pagination' => 'regular',
		'el_class' => '',
	), us_maybe_get_post_json( 'template_vars' ) );

	// Filtering query_args
	if ( isset( $template_vars['query_args'] ) AND is_array( $template_vars['query_args'] ) ) {
		// Query Args keys, that won't be filtered
		$allowed_query_keys = array(
			// Blog listing shortcode requests
			'author_name',
			'us_portfolio_category',
			'category_name',
			// Archive requests
			'year',
			'monthnum',
			'day',
			'tag',
			// Search requests
			's',
			// Pagination
			'paged',
			'orderby',
			'posts_per_page',
			'post__not_in',
			// Custom users' queries
			'post_type',
		);
		$allowed_post_types = array( 'us_portfolio', 'post' );
		foreach ( $template_vars['query_args'] as $query_key => $query_val ) {
			if ( ! in_array( $query_key, $allowed_query_keys ) ) {
				unset( $template_vars['query_args'][ $query_key ] );
			}
		}
		if ( isset( $template_vars['query_args']['post_type'] ) ) {
			$is_allowed_post_type = FALSE;
			foreach ( $allowed_post_types as $allowed_post_type ) {
				if ( $template_vars['query_args']['post_type'] == $allowed_post_type OR ( is_array( $template_vars['query_args']['post_type'] ) AND count( $template_vars['query_args']['post_type'] ) == 1 AND $template_vars['query_args']['post_type'][0] == $allowed_post_type ) ) {
					$is_allowed_post_type = TRUE;
				}
			}
			if ( ! $is_allowed_post_type ) {
				unset( $template_vars['query_args']['post_type'] );
			}
		}
		if ( ! isset( $template_vars['query_args']['s'] ) AND ! isset( $template_vars['query_args']['post_type'] ) ) { // TODO: check if we ever set post_type in template_vars
			$template_vars['query_args']['post_type'] = 'post';
		}
		// Providing proper post statuses
		$template_vars['query_args']['post_status'] = array( 'publish' => 'publish' );
		$template_vars['query_args']['post_status'] += (array) get_post_stati( array( 'public' => TRUE ) );
		// Add private states if user is capable to view them
		if ( is_user_logged_in() AND current_user_can( 'read_private_posts' ) ) {
			$template_vars['query_args']['post_status'] += (array) get_post_stati( array( 'private' => TRUE ) );
		}
		$template_vars['query_args']['post_status'] = array_values( $template_vars['query_args']['post_status'] );
	}

	// Passing values that were filtered due to post protocol
	us_load_template( 'templates/blog/listing', $template_vars );

	// We don't use JSON to reduce data size
	die;
}
