<?php
$GLOBALS['et_builder_used_in_wc_shop'] = false;

/**
 * Determines if current page is WooCommerce's shop page + uses builder.
 * NOTE: This has to be used after pre_get_post (et_builder_wc_pre_get_posts).
 * @return bool
 */
function et_builder_used_in_wc_shop() {
	global $et_builder_used_in_wc_shop;

	return apply_filters(
		'et_builder_used_in_wc_shop',
		$et_builder_used_in_wc_shop
	);
}

/**
 * Use page.php as template for  a page which uses builder & being set as shop page
 * @param  string path to template
 * @return string modified path to template
 */
function et_builder_wc_template_include( $template ) {
	// Detemine whether current page uses builder and set as
	if ( et_builder_used_in_wc_shop() && '' !== locate_template( 'page.php' ) ) {
		$template = locate_template( 'page.php' );
	}

	return $template;
}
add_action( 'template_include', 'et_builder_wc_template_include' );

/**
 * Overwrite WooCommerce's custom query in shop page if the page uses builder.
 * After proper shop page setup (page selection + permalink flushed), the original
 * page permalink will be recognized as is_post_type_archive by WordPress' rewrite
 * URL when it is being parsed. This causes is_page() detection fails and no way
 * to get actual page ID on pre_get_posts hook, unless by doing reverse detection:
 *
 * 1. Check if current page is product archive page. Most page will fail on this.
 * 2. Afterward, if wc_get_page_id( 'shop' ) returns a page ID, it means that
 *    current page is shop page (product post type archive) which is configured
 *    in custom page. Next, check whether Divi Builder is used on this page or not.
 *
 * @param object query object
 * @param void
 */
function et_builder_wc_pre_get_posts( $query ) {
	global $et_builder_used_in_wc_shop;

	if ( is_admin() ) {
		return;
	}

	if ( ! $query->is_main_query() ) {
		return;
	}

	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	if ( ! function_exists( 'wc_get_page_id' ) ) {
		return;
	}

	// Check if current page is product archive page. Most page will fail on this.
	// initially used is_shop(), but is_shop()'s is_page() check throws error at
	// this early hook on homepage if shop page !== page_on_front
	if ( ! is_post_type_archive( 'product' ) ) {
		return;
	}

	// Note that the following check is only performed on product archive page.
	$shop_page_id       = wc_get_page_id( 'shop' );
	$shop_page_object   = get_post( $shop_page_id );
	$is_shop_page_exist = isset( $shop_page_object->post_type ) && 'page' === $shop_page_object->post_type;

	if ( ! $is_shop_page_exist ) {
		return;
	}

	if ( ! et_pb_is_pagebuilder_used( $shop_page_id ) ) {
		return;
	}

	// Set et_builder_used_in_wc_shop() global to true
	$et_builder_used_in_wc_shop = true;

	// Overwrite page query. This overwrite enables is_page() and other standard
	// page-related function to work normally after pre_get_posts hook
	$query->set( 'page_id',        $shop_page_id );
	$query->set( 'post_type',      'page' );
	$query->set( 'posts_per_page', 1 );
	$query->set( 'wc_query',       null );
	$query->set( 'meta_query',     array() );

	$query->is_page              = true;
	$query->is_singular          = true;
	$query->is_post_type_archive = false;
	$query->is_archive           = false;

	// Avoid unwanted <p> at the beginning of the rendered builder
	remove_filter( 'the_content', 'wpautop' );
}
add_action( 'pre_get_posts', 'et_builder_wc_pre_get_posts' );

/**
 * Remove woocommerce body classes if current shop page uses builder.
 * woocommerce-page body class causes builder's shop column styling to be irrelevant.
 * @param  array body classes
 * @return array modified body classes
 */
function et_builder_wc_body_class( $classes ) {
	if ( et_builder_used_in_wc_shop() ) {
		$classes = array_diff( $classes, array( 'woocommerce', 'woocommerce-page' ) );
	}

	return $classes;
}
add_filter( 'body_class', 'et_builder_wc_body_class' );
