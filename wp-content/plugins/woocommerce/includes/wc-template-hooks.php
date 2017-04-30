<?php
/**
 * WooCommerce Template Hooks
 *
 * Action/filter hooks used for WooCommerce functions/templates
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_filter( 'body_class', 'wc_body_class' );
add_filter( 'post_class', 'wc_product_post_class', 20, 3 );

/** 
 * WP Header
 *
 * @see  wc_products_rss_feed()
 * @see  wc_generator_tag()
 */
add_action( 'wp_head', 'wc_products_rss_feed' );
add_action( 'get_the_generator_html', 'wc_generator_tag', 10, 2 );
add_action( 'get_the_generator_xhtml', 'wc_generator_tag', 10, 2 );

/**
 * Content Wrappers
 *
 * @see woocommerce_output_content_wrapper()
 * @see woocommerce_output_content_wrapper_end()
 */
add_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
add_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/**
 * Sale flashes
 *
 * @see woocommerce_show_product_loop_sale_flash()
 * @see woocommerce_show_product_sale_flash()
 */
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

/**
 * Breadcrumbs
 *
 * @see woocommerce_breadcrumb()
 */
add_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

/**
 * Sidebar
 *
 * @see woocommerce_get_sidebar()
 */
add_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Archive descriptions
 *
 * @see woocommerce_taxonomy_archive_description()
 * @see woocommerce_product_archive_description()
 */
add_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
add_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );

/**
 * Products Loop
 *
 * @see woocommerce_result_count()
 * @see woocommerce_catalog_ordering()
 * @see woocommerce_reset_loop()
 */
add_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/**
 * Product Loop Items
 *
 * @see woocommerce_template_loop_add_to_cart()
 * @see woocommerce_template_loop_product_thumbnail()
 * @see woocommerce_template_loop_price()
 * @see woocommerce_template_loop_rating()
 */
add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

/**
 * Subcategories
 *
 * @see woocommerce_subcategory_thumbnail()
 */
add_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );

/**
 * Before Single Products Summary Div
 *
 * @see woocommerce_show_product_images()
 * @see woocommerce_show_product_thumbnails()
 */
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
add_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );

/**
 * After Single Products Summary Div
 *
 * @see woocommerce_output_product_data_tabs()
 * @see woocommerce_upsell_display()
 * @see woocommerce_output_related_products()
 */
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

/**
 * Product Summary Box
 *
 * @see woocommerce_template_single_title()
 * @see woocommerce_template_single_price()
 * @see woocommerce_template_single_excerpt()
 * @see woocommerce_template_single_meta()
 * @see woocommerce_template_single_sharing()
 */
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

/**
 * Product Add to cart
 *
 * @see woocommerce_template_single_add_to_cart()
 * @see woocommerce_simple_add_to_cart()
 * @see woocommerce_grouped_add_to_cart()
 * @see woocommerce_variable_add_to_cart()
 * @see woocommerce_external_add_to_cart()
 */
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
add_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
add_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
add_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );

/**
 * Pagination after shop loops
 *
 * @see woocommerce_pagination()
 */
add_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

/**
 * Product page tabs
 */
add_filter( 'woocommerce_product_tabs', 'woocommerce_default_product_tabs' );
add_filter( 'woocommerce_product_tabs', 'woocommerce_sort_product_tabs', 99 );

/**
 * Checkout
 *
 * @see woocommerce_checkout_login_form()
 * @see woocommerce_checkout_coupon_form()
 * @see woocommerce_order_review()
 */
add_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
add_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
add_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );

/**
 * Cart
 *
 * @see woocommerce_cross_sell_display()
 */
add_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

/**
 * Footer
 *
 * @see  wc_print_js()
 * @see woocommerce_demo_store()
 */
add_action( 'wp_footer', 'wc_print_js', 25 );
add_action( 'wp_footer', 'woocommerce_demo_store' );

/**
 * Order details
 *
 * @see woocommerce_order_details_table()
 * @see woocommerce_order_details_table()
 */
add_action( 'woocommerce_view_order', 'woocommerce_order_details_table', 10 );
add_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
add_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );
