<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	WooCommerce/Functions
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function woocommerce_show_messages() {
	_deprecated_function( 'woocommerce_show_messages', '2.1', 'wc_print_notices' );
	wc_print_notices();
}
function woocommerce_weekend_area_js() {
	_deprecated_function( 'woocommerce_weekend_area_js', '2.1', '' );
}
function woocommerce_tooltip_js() {
	_deprecated_function( 'woocommerce_tooltip_js', '2.1', '' );
}
function woocommerce_datepicker_js() {
	_deprecated_function( 'woocommerce_datepicker_js', '2.1', '' );
}
function woocommerce_admin_scripts() {
	_deprecated_function( 'woocommerce_admin_scripts', '2.1', '' );
}
function woocommerce_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
	_deprecated_function( 'woocommerce_create_page', '2.1', 'wc_create_page' );
	return wc_create_page( $slug, $option, $page_title, $page_content, $post_parent );
}
function woocommerce_readfile_chunked( $file, $retbytes = true ) {
	_deprecated_function( 'woocommerce_readfile_chunked', '2.1', 'WC_Download_Handler::readfile_chunked()' );
	return WC_Download_Handler::readfile_chunked( $file, $retbytes );
}

/**
 * Formal total costs - format to the number of decimal places for the base currency.
 *
 * @access public
 * @param mixed $number
 * @deprecated 2.1
 * @return string
 */
function woocommerce_format_total( $number ) {
	_deprecated_function( __FUNCTION__, '2.1', 'wc_format_decimal()' );
	return wc_format_decimal( $number, get_option( 'woocommerce_price_num_decimals' ), false );
}

/**
 * Get product name with extra details such as SKU price and attributes. Used within admin.
 *
 * @access public
 * @param WC_Product $product
 * @deprecated 2.1
 * @return string
 */
function woocommerce_get_formatted_product_name( $product ) {
	_deprecated_function( __FUNCTION__, '2.1', 'WC_Product::get_formatted_name()' );
	return $product->get_formatted_name();
}

/**
 * Handle IPN requests for the legacy paypal gateway by calling gateways manually if needed.
 *
 * @access public
 * @return void
 */
function woocommerce_legacy_paypal_ipn() {
	if ( ! empty( $_GET['paypalListener'] ) && $_GET['paypalListener'] == 'paypal_standard_IPN' ) {

		WC()->payment_gateways();

		do_action( 'woocommerce_api_wc_gateway_paypal' );
	}
}
add_action( 'init', 'woocommerce_legacy_paypal_ipn' );

/**
 * Cart functions (soft deprecated)
 */
function woocommerce_protected_product_add_to_cart( $passed, $product_id ) {
	return wc_protected_product_add_to_cart( $passed, $product_id );
}
function woocommerce_empty_cart() {
	wc_empty_cart();
}
function woocommerce_load_persistent_cart( $user_login, $user = 0 ) {
	return wc_load_persistent_cart( $user_login, $user );
}
function woocommerce_add_to_cart_message( $product_id ) {
	wc_add_to_cart_message( $product_id );
}
function woocommerce_clear_cart_after_payment() {
	wc_clear_cart_after_payment();
}
function woocommerce_cart_totals_subtotal_html() {
	wc_cart_totals_subtotal_html();
}
function woocommerce_cart_totals_shipping_html() {
	wc_cart_totals_shipping_html();
}
function woocommerce_cart_totals_coupon_html( $coupon ) {
	wc_cart_totals_coupon_html( $coupon );
}
function woocommerce_cart_totals_order_total_html() {
	wc_cart_totals_order_total_html();
}
function woocommerce_cart_totals_fee_html( $fee ) {
	wc_cart_totals_fee_html( $fee );
}
function woocommerce_cart_totals_shipping_method_label( $method ) {
	return wc_cart_totals_shipping_method_label( $method );
}

/**
 * Core functions (soft deprecated)
 */
function woocommerce_get_template_part( $slug, $name = '' ) {
	wc_get_template_part( $slug, $name );
}
function woocommerce_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	wc_get_template( $template_name, $args, $template_path, $default_path );
}
function woocommerce_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	return wc_locate_template( $template_name, $template_path, $default_path );
}
function woocommerce_mail( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = "" ) {
	wc_mail( $to, $subject, $message, $headers, $attachments );
}

/**
 * Customer functions (soft deprecated)
 */
function woocommerce_disable_admin_bar( $show_admin_bar ) {
	return wc_disable_admin_bar( $show_admin_bar );
}
function woocommerce_create_new_customer( $email, $username = '', $password = '' ) {
	return wc_create_new_customer( $email, $username, $password );
}
function woocommerce_set_customer_auth_cookie( $customer_id ) {
	wc_set_customer_auth_cookie( $customer_id );
}
function woocommerce_update_new_customer_past_orders( $customer_id ) {
	return wc_update_new_customer_past_orders( $customer_id );
}
function woocommerce_paying_customer( $order_id ) {
	wc_paying_customer( $order_id );
}
function woocommerce_customer_bought_product( $customer_email, $user_id, $product_id ) {
	return wc_customer_bought_product( $customer_email, $user_id, $product_id );
}
function woocommerce_customer_has_capability( $allcaps, $caps, $args ) {
	return wc_customer_has_capability( $allcaps, $caps, $args );
}

/**
 * Formatting functions (soft deprecated)
 */
function woocommerce_sanitize_taxonomy_name( $taxonomy ) {
	return wc_sanitize_taxonomy_name( $taxonomy );
}
function woocommerce_get_filename_from_url( $file_url ) {
	return wc_get_filename_from_url( $file_url );
}
function woocommerce_get_dimension( $dim, $to_unit ) {
	return wc_get_dimension( $dim, $to_unit );
}
function woocommerce_get_weight( $weight, $to_unit ) {
	return wc_get_weight( $weight, $to_unit );
}
function woocommerce_trim_zeros( $price ) {
	return wc_trim_zeros( $price );
}
function woocommerce_round_tax_total( $tax ) {
	return wc_round_tax_total( $tax );
}
function woocommerce_format_decimal( $number, $dp = false, $trim_zeros = false ) {
	return wc_format_decimal( $number, $dp, $trim_zeros );
}
function woocommerce_clean( $var ) {
	return wc_clean( $var );
}
function woocommerce_array_overlay( $a1, $a2 ) {
	return wc_array_overlay( $a1, $a2 );
}
function woocommerce_price( $price, $args = array() ) {
	return wc_price( $price, $args );
}
function woocommerce_let_to_num( $size ) {
	return wc_let_to_num( $size );
}

/**
 * @return string
 */
function woocommerce_date_format() {
	return wc_date_format();
}
function woocommerce_time_format() {
	return wc_time_format();
}
function woocommerce_timezone_string() {
	return wc_timezone_string();
}
if ( ! function_exists( 'woocommerce_rgb_from_hex' ) ) {
	function woocommerce_rgb_from_hex( $color ) {
		return wc_rgb_from_hex( $color );
	}
}
if ( ! function_exists( 'woocommerce_hex_darker' ) ) {
	function woocommerce_hex_darker( $color, $factor = 30 ) {
		return wc_hex_darker( $color, $factor );
	}
}
if ( ! function_exists( 'woocommerce_hex_lighter' ) ) {
	function woocommerce_hex_lighter( $color, $factor = 30 ) {
		return wc_hex_lighter( $color, $factor );
	}
}
if ( ! function_exists( 'woocommerce_light_or_dark' ) ) {
	function woocommerce_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {
		return wc_light_or_dark( $color, $dark, $light );
	}
}
if ( ! function_exists( 'woocommerce_format_hex' ) ) {
	function woocommerce_format_hex( $hex ) {
		return wc_format_hex( $hex );
	}
}

/**
 * Order functions (soft deprecated)
 */
function woocommerce_get_order_id_by_order_key( $order_key ) {
	return wc_get_order_id_by_order_key( $order_key );
}
function woocommerce_downloadable_file_permission( $download_id, $product_id, $order ) {
	return wc_downloadable_file_permission( $download_id, $product_id, $order );
}
function woocommerce_downloadable_product_permissions( $order_id ) {
	wc_downloadable_product_permissions( $order_id );
}
function woocommerce_add_order_item( $order_id, $item ) {
	return wc_add_order_item( $order_id, $item );
}
function woocommerce_delete_order_item( $item_id ) {
	return wc_delete_order_item( $item_id );
}
function woocommerce_update_order_item_meta( $item_id, $meta_key, $meta_value, $prev_value = '' ) {
	return wc_update_order_item_meta( $item_id, $meta_key, $meta_value, $prev_value );
}
function woocommerce_add_order_item_meta( $item_id, $meta_key, $meta_value, $unique = false ) {
	return wc_add_order_item_meta( $item_id, $meta_key, $meta_value, $unique );
}
function woocommerce_delete_order_item_meta( $item_id, $meta_key, $meta_value = '', $delete_all = false ) {
	return wc_delete_order_item_meta( $item_id, $meta_key, $meta_value, $delete_all );
}
function woocommerce_get_order_item_meta( $item_id, $key, $single = true ) {
	return wc_get_order_item_meta( $item_id, $key, $single );
}
function woocommerce_cancel_unpaid_orders() {
	wc_cancel_unpaid_orders();
}
function woocommerce_processing_order_count() {
	return wc_processing_order_count();
}

/**
 * Page functions (soft deprecated)
 */
function woocommerce_get_page_id( $page ) {
	return wc_get_page_id( $page );
}
function woocommerce_get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
	return wc_get_endpoint_url( $endpoint, $value, $permalink );
}
function woocommerce_lostpassword_url( $url ) {
	return wc_lostpassword_url( $url );
}
function woocommerce_customer_edit_account_url() {
	return wc_customer_edit_account_url();
}
function woocommerce_nav_menu_items( $items, $args ) {
	return wc_nav_menu_items( $items, $args );
}
function woocommerce_nav_menu_item_classes( $menu_items, $args ) {
	return wc_nav_menu_item_classes( $menu_items, $args );
}
function woocommerce_list_pages( $pages ) {
	return wc_list_pages( $pages );
}

/**
 * Handle renamed filters
 */
global $wc_map_deprecated_filters;

$wc_map_deprecated_filters = array(
	'woocommerce_cart_item_class'       => 'woocommerce_cart_table_item_class',
	'woocommerce_cart_item_product_id'  => 'hook_woocommerce_in_cart_product_id',
	'woocommerce_cart_item_thumbnail'   => 'hook_woocommerce_in_cart_product_thumbnail',
	'woocommerce_cart_item_price'       => 'woocommerce_cart_item_price_html',
	'woocommerce_cart_item_name'        => 'woocommerce_in_cart_product_title',
	'woocommerce_order_item_class'      => 'woocommerce_order_table_item_class',
	'woocommerce_order_item_name'       => 'woocommerce_order_table_product_title',
	'woocommerce_order_amount_shipping' => 'woocommerce_order_amount_total_shipping',
	'woocommerce_package_rates'         => 'woocommerce_available_shipping_methods'
);

foreach ( $wc_map_deprecated_filters as $new => $old )
	add_filter( $new, 'woocommerce_deprecated_filter_mapping' );

function woocommerce_deprecated_filter_mapping( $data, $arg_1 = '', $arg_2 = '', $arg_3 = '' ) {
	global $wc_map_deprecated_filters;

	$filter = current_filter();

	if ( isset( $wc_map_deprecated_filters[ $filter ] ) )
		if ( has_filter( $wc_map_deprecated_filters[ $filter ] ) ) {
			$data = apply_filters( $wc_map_deprecated_filters[ $filter ], $data, $arg_1, $arg_2, $arg_3 );
			_deprecated_function( 'The ' . $wc_map_deprecated_filters[ $filter ] . ' filter', '2.1', $filter );
		}

	return $data;
}

/**
 * Alias functions/soft-deprecated function names (moving from woocommerce_ to wc_). These will be deprecated with notices in future updates.
 */

/**
 * Attribute functions - soft deprecated
 */
function woocommerce_product_dropdown_categories( $args = array(), $deprecated_hierarchical = 1, $deprecated_show_uncategorized = 1, $deprecated_orderby = '' ) {
	return wc_product_dropdown_categories( $args, $deprecated_hierarchical, $deprecated_show_uncategorized, $deprecated_orderby );
}
function woocommerce_walk_category_dropdown_tree( $a1 = '', $a2 = '', $a3 = '' ) {
	return wc_walk_category_dropdown_tree( $a1, $a2, $a3 );
}
function woocommerce_taxonomy_metadata_wpdbfix() {
	return wc_taxonomy_metadata_wpdbfix();
}
function woocommerce_order_terms( $the_term, $next_id, $taxonomy, $index = 0, $terms = null ) {
	return wc_reorder_terms( $the_term, $next_id, $taxonomy, $index, $terms );
}
function woocommerce_set_term_order( $term_id, $index, $taxonomy, $recursive = false ) {
	return wc_set_term_order( $term_id, $index, $taxonomy, $recursive );
}
function woocommerce_terms_clauses( $clauses, $taxonomies, $args ) {
	return wc_terms_clauses( $clauses, $taxonomies, $args );
}
function _woocommerce_term_recount( $terms, $taxonomy, $callback, $terms_are_term_taxonomy_ids ) {
	return _wc_term_recount( $terms, $taxonomy, $callback, $terms_are_term_taxonomy_ids );
}
function woocommerce_recount_after_stock_change( $product_id ) {
	return wc_recount_after_stock_change( $product_id );
}
function woocommerce_change_term_counts( $terms, $taxonomies, $args ) {
	return wc_change_term_counts( $terms, $taxonomies, $args );
}

/**
 * Product functions - soft deprecated
 */
function woocommerce_get_product_ids_on_sale() {
	return wc_get_product_ids_on_sale();
}
function woocommerce_get_featured_product_ids() {
	return wc_get_featured_product_ids();
}
function woocommerce_get_product_terms( $object_id, $taxonomy, $fields = 'all' ) {
	return wc_get_product_terms( $object_id, $taxonomy, array( 'fields' => $fields ) );
}
function woocommerce_product_post_type_link( $permalink, $post ) {
	return wc_product_post_type_link( $permalink, $post );
}
function woocommerce_placeholder_img_src() {
	return wc_placeholder_img_src();
}
function woocommerce_placeholder_img( $size = 'shop_thumbnail' ) {
	return wc_placeholder_img( $size );
}
function woocommerce_get_formatted_variation( $variation = '', $flat = false ) {
	return wc_get_formatted_variation( $variation, $flat );
}
function woocommerce_scheduled_sales() {
	return wc_scheduled_sales();
}
function woocommerce_get_attachment_image_attributes( $attr ) {
	return wc_get_attachment_image_attributes( $attr );
}
function woocommerce_prepare_attachment_for_js( $response ) {
	return wc_prepare_attachment_for_js( $response );
}
function woocommerce_track_product_view() {
	return wc_track_product_view();
}
