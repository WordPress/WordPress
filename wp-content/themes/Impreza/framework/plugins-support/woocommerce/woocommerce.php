<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * WooCommerce Theme Support
 *
 * @link http://www.woothemes.com/woocommerce/
 */

add_action( 'after_setup_theme', 'us_woocommerce_support' );
function us_woocommerce_support() {
	add_theme_support( 'woocommerce' );
}

if ( ! class_exists( 'woocommerce' ) ) {
	return FALSE;
}

global $woocommerce;
if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
	define( 'WOOCOMMERCE_USE_CSS', FALSE );
} else {
	add_filter( 'woocommerce_enqueue_styles', 'us_woocommerce_dequeue_styles' );
	function us_woocommerce_dequeue_styles( $styles ) {
		$styles = array();

		return $styles;
	}
}

add_action( 'wp_enqueue_scripts', 'us_woocommerce_enqueue_styles' );
function us_woocommerce_enqueue_styles( $styles ) {
	global $us_template_directory_uri;
	wp_enqueue_style( 'us-woocommerce', $us_template_directory_uri . '/css/us.woocommerce.css', array(), US_THEMEVERSION, 'all' );
}

// Adjust markup for all woocommerce pages

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
if ( ! function_exists( 'us_woocommerce_before_main_content' ) ) {
	add_action( 'woocommerce_before_main_content', 'us_woocommerce_before_main_content', 10 );
	function us_woocommerce_before_main_content() {
		echo '<div class="l-main"><div class="l-main-h i-cf"><main class="l-content" itemscope="itemscope" itemprop="mainContentOfPage">';
		if ( is_post_type_archive( 'product' ) && 0 === absint( get_query_var( 'paged' ) ) ) {
			$shop_page   = get_post( wc_get_page_id( 'shop' ) );
			if ( $shop_page ) {

				$description = apply_filters( 'the_content', $shop_page->post_content );
				if ( $description ) {
					$has_own_sections = ( strpos( $description, ' class="l-section' ) !== FALSE );
					if ( ! $has_own_sections ) {
						$description = '<section class="l-section for_shop_description"><div class="l-section-h i-cf">' . $description . '</div></section>';
					}
					echo $description;

				}
			}
		}

		echo '<section id="shop" class="l-section for_shop"><div class="l-section-h i-cf">';
	}
}

remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
if ( ! function_exists( 'us_woocommerce_after_main_content' ) ) {
	add_action( 'woocommerce_after_main_content', 'us_woocommerce_after_main_content', 20 );
	function us_woocommerce_after_main_content() {
		$us_layout = US_Layout::instance();
		echo '</div></section></main>';
		if ( $us_layout->sidebar_pos == 'left' OR $us_layout->sidebar_pos == 'right' ) {

			if ( is_singular() ) {
				$sidebar_id = us_dynamic_sidebar_id( us_get_option( 'product_sidebar_id', 'default_sidebar' ) );
			} else {
				$sidebar_id = us_get_option( 'shop_sidebar_id', 'default_sidebar' );
				if ( ! is_search() AND ! is_tax() ) {
					$shop_page_sidebar_id = usof_meta( 'us_sidebar_id', array(), wc_get_page_id( 'shop' ) );
					if ( $shop_page_sidebar_id ) {
						$sidebar_id = $shop_page_sidebar_id;
					}
				}
			}
			echo '<aside class="l-sidebar at_' . $us_layout->sidebar_pos . ' ' . $sidebar_id . '" itemtype="https://schema.org/WPSideBar">';
			dynamic_sidebar( $sidebar_id );
			echo '</aside>';
		}
		echo '</div></div>';
	}
}

// Adjust markup for product in list
add_action( 'woocommerce_before_shop_loop_item', 'us_woocommerce_before_shop_loop_item', 20 );
function us_woocommerce_before_shop_loop_item() {
	echo '<div class="product-h">';
}

add_action( 'woocommerce_after_shop_loop_item', 'us_woocommerce_after_shop_loop_item', 20 );
function us_woocommerce_after_shop_loop_item() {
	echo '</div>';
}

add_action( 'woocommerce_before_shop_loop_item_title', 'us_woocommerce_before_shop_loop_item_title', 20 );
function us_woocommerce_before_shop_loop_item_title() {
	echo '<div class="product-meta">';
}

add_action( 'woocommerce_after_shop_loop_item_title', 'us_woocommerce_after_shop_loop_item_title', 20 );
function us_woocommerce_after_shop_loop_item_title() {
	echo '</div>';
}

/**
 * WooCommerce Extra Feature
 * --------------------------
 *
 * Change number of related products on product page
 * Set your own value for 'posts_per_page'
 *
 */
function woo_related_products_limit() {
	global $product;

	$args['posts_per_page'] = us_get_option( 'product_related_qty', 4 );

	return $args;
}

add_filter( 'woocommerce_output_related_products_args', 'us_related_products_args' );
function us_related_products_args( $args ) {
	$args['posts_per_page'] = us_get_option( 'product_related_qty', 4 );

	return $args;
}

// Remove WC sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

add_filter( 'woocommerce_cross_sells_total', 'us_woocommerce_cross_sells_total' );
add_filter( 'woocommerce_cross_sells_columns', 'us_woocommerce_cross_sells_total' );
function us_woocommerce_cross_sells_total( $count ) {
	return 4;
}

// Move cross sells bellow the shipping
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 10 );

// Remove WC lightbox
add_action( 'wp_print_scripts', 'us_deregister_woo_scripts', 100 );
function us_deregister_woo_scripts() {
	global $wp_scripts;
	if ( wp_script_is( 'prettyPhoto', 'registered' ) ) {
		wp_deregister_script( 'prettyPhoto' );
	}
	if ( wp_script_is( 'prettyPhoto-init', 'registered' ) ) {
		wp_deregister_script( 'prettyPhoto-init' );
	}
}

add_action( 'wp_print_styles', 'us_deregister_woo_styles', 100 );
function us_deregister_woo_styles() {
	if ( wp_style_is( 'woocommerce_prettyPhoto_css', 'registered' ) ) {
		wp_deregister_style( 'woocommerce_prettyPhoto_css' );
	}
}

add_filter( 'woocommerce_single_product_image_html', 'us_woo_fix_product_lightbox' );
add_filter( 'woocommerce_single_product_image_thumbnail_html', 'us_woo_fix_product_lightbox' );
function us_woo_fix_product_lightbox( $html ) {
	if ( 'yes' === get_option( 'woocommerce_enable_lightbox' ) ) {
		$html = preg_replace( '~ class="([^"]+)"~u', ' class="$1 with-lightbox"', $html );
	}

	$html = preg_replace( '~ data-rel="prettyPhoto([^"]+)?"~u', '', $html );

	return $html;
}

// Add breadcrumbs before product title
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_breadcrumb', 3 );

// Alter Cart - add total number
add_filter( 'add_to_cart_fragments', 'us_add_to_cart_fragments' );
function us_add_to_cart_fragments( $fragments ) {
	global $woocommerce;

	$fragments['a.cart-contents'] = '<a class="cart-contents" href="' . esc_url( $woocommerce->cart->get_cart_url() ) . '" ';
	$fragments['a.cart-contents'] .= 'title="' . __( 'View your shopping cart', 'us' ) . '">';
	$fragments['a.cart-contents'] .= sprintf( _n( '%d item', '%d items', $woocommerce->cart->cart_contents_count, 'us' ), $woocommerce->cart->cart_contents_count );
	$fragments['a.cart-contents'] .= ' - ' . $woocommerce->cart->get_cart_total() . '</a>';

	return $fragments;
}

add_action( 'body_class', 'us_wc_body_class' );
function us_wc_body_class( $classes ) {
	$classes[] = 'us-woo-shop_' . us_get_option( 'shop_listing_style', 'standard' );
	$classes[] = 'us-woo-cart_' . us_get_option( 'shop_cart', 'standard' );
	if ( us_get_option( 'shop_catalog', 0 ) == 1 ) {
		$classes[] = 'us-woo-catalog';
	}
	if ( is_single() ) {
		$classes[] = 'columns-' . us_get_option( 'product_related_qty', 4 );
	} else {
		$classes[] = 'columns-' . us_get_option( 'shop_columns', 4 );
	}

	return $classes;
}

// Pagination
if ( ! function_exists( 'woocommerce_pagination' ) ) {
	function woocommerce_pagination() {
		global $wp_query;
		if ( $wp_query->max_num_pages <= 1 ) {
			return;
		}
		echo '<div class="g-pagination">';
		the_posts_pagination( array(
			'prev_text' => '<',
			'next_text' => '>',
			'before_page_number' => '<span>',
			'after_page_number' => '</span>',
		) );
		echo '</div>';
	}
}

add_action( 'woocommerce_after_mini_cart', 'us_woocommerce_after_mini_cart' );

function us_woocommerce_after_mini_cart() {
	global $woocommerce;

	echo '<span class="us_mini_cart_amount" style="display: none;">' . $woocommerce->cart->cart_contents_count . '</span>';
}

function woocommerce_product_archive_description() {
	return '';
}
