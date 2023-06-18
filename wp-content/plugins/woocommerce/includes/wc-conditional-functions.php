<?php
/**
 * WooCommerce Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @package     WooCommerce\Functions
 * @version     2.3.0
 */

use Automattic\Jetpack\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Is_woocommerce - Returns true if on a page which uses WooCommerce templates (cart and checkout are standard pages with shortcodes and thus are not included).
 *
 * @return bool
 */
function is_woocommerce() {
	return apply_filters( 'is_woocommerce', is_shop() || is_product_taxonomy() || is_product() );
}

if ( ! function_exists( 'is_shop' ) ) {

	/**
	 * Is_shop - Returns true when viewing the product type archive (shop).
	 *
	 * @return bool
	 */
	function is_shop() {
		return ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) );
	}
}

if ( ! function_exists( 'is_product_taxonomy' ) ) {

	/**
	 * Is_product_taxonomy - Returns true when viewing a product taxonomy archive.
	 *
	 * @return bool
	 */
	function is_product_taxonomy() {
		return is_tax( get_object_taxonomies( 'product' ) );
	}
}

if ( ! function_exists( 'is_product_category' ) ) {

	/**
	 * Is_product_category - Returns true when viewing a product category.
	 *
	 * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
	 * @return bool
	 */
	function is_product_category( $term = '' ) {
		return is_tax( 'product_cat', $term );
	}
}

if ( ! function_exists( 'is_product_tag' ) ) {

	/**
	 * Is_product_tag - Returns true when viewing a product tag.
	 *
	 * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
	 * @return bool
	 */
	function is_product_tag( $term = '' ) {
		return is_tax( 'product_tag', $term );
	}
}

if ( ! function_exists( 'is_product' ) ) {

	/**
	 * Is_product - Returns true when viewing a single product.
	 *
	 * @return bool
	 */
	function is_product() {
		return is_singular( array( 'product' ) );
	}
}

if ( ! function_exists( 'is_cart' ) ) {

	/**
	 * Is_cart - Returns true when viewing the cart page.
	 *
	 * @return bool
	 */
	function is_cart() {
		$page_id = wc_get_page_id( 'cart' );

		return ( $page_id && is_page( $page_id ) ) || Constants::is_defined( 'WOOCOMMERCE_CART' ) || wc_post_content_has_shortcode( 'woocommerce_cart' );
	}
}

if ( ! function_exists( 'is_checkout' ) ) {

	/**
	 * Is_checkout - Returns true when viewing the checkout page.
	 *
	 * @return bool
	 */
	function is_checkout() {
		$page_id = wc_get_page_id( 'checkout' );

		return ( $page_id && is_page( $page_id ) ) || wc_post_content_has_shortcode( 'woocommerce_checkout' ) || apply_filters( 'woocommerce_is_checkout', false ) || Constants::is_defined( 'WOOCOMMERCE_CHECKOUT' );
	}
}

if ( ! function_exists( 'is_checkout_pay_page' ) ) {

	/**
	 * Is_checkout_pay - Returns true when viewing the checkout's pay page.
	 *
	 * @return bool
	 */
	function is_checkout_pay_page() {
		global $wp;

		return is_checkout() && ! empty( $wp->query_vars['order-pay'] );
	}
}

if ( ! function_exists( 'is_wc_endpoint_url' ) ) {

	/**
	 * Is_wc_endpoint_url - Check if an endpoint is showing.
	 *
	 * @param string|false $endpoint Whether endpoint.
	 * @return bool
	 */
	function is_wc_endpoint_url( $endpoint = false ) {
		global $wp;

		$wc_endpoints = WC()->query->get_query_vars();

		if ( false !== $endpoint ) {
			if ( ! isset( $wc_endpoints[ $endpoint ] ) ) {
				return false;
			} else {
				$endpoint_var = $wc_endpoints[ $endpoint ];
			}

			return isset( $wp->query_vars[ $endpoint_var ] );
		} else {
			foreach ( $wc_endpoints as $key => $value ) {
				if ( isset( $wp->query_vars[ $key ] ) ) {
					return true;
				}
			}

			return false;
		}
	}
}

if ( ! function_exists( 'is_account_page' ) ) {

	/**
	 * Is_account_page - Returns true when viewing an account page.
	 *
	 * @return bool
	 */
	function is_account_page() {
		$page_id = wc_get_page_id( 'myaccount' );

		return ( $page_id && is_page( $page_id ) ) || wc_post_content_has_shortcode( 'woocommerce_my_account' ) || apply_filters( 'woocommerce_is_account_page', false );
	}
}

if ( ! function_exists( 'is_view_order_page' ) ) {

	/**
	 * Is_view_order_page - Returns true when on the view order page.
	 *
	 * @return bool
	 */
	function is_view_order_page() {
		global $wp;

		$page_id = wc_get_page_id( 'myaccount' );

		return ( $page_id && is_page( $page_id ) && isset( $wp->query_vars['view-order'] ) );
	}
}

if ( ! function_exists( 'is_edit_account_page' ) ) {

	/**
	 * Check for edit account page.
	 * Returns true when viewing the edit account page.
	 *
	 * @since 2.5.1
	 * @return bool
	 */
	function is_edit_account_page() {
		global $wp;

		$page_id = wc_get_page_id( 'myaccount' );

		return ( $page_id && is_page( $page_id ) && isset( $wp->query_vars['edit-account'] ) );
	}
}

if ( ! function_exists( 'is_order_received_page' ) ) {

	/**
	 * Is_order_received_page - Returns true when viewing the order received page.
	 *
	 * @return bool
	 */
	function is_order_received_page() {
		global $wp;

		$page_id = wc_get_page_id( 'checkout' );

		return apply_filters( 'woocommerce_is_order_received_page', ( $page_id && is_page( $page_id ) && isset( $wp->query_vars['order-received'] ) ) );
	}
}

if ( ! function_exists( 'is_add_payment_method_page' ) ) {

	/**
	 * Is_add_payment_method_page - Returns true when viewing the add payment method page.
	 *
	 * @return bool
	 */
	function is_add_payment_method_page() {
		global $wp;

		$page_id = wc_get_page_id( 'myaccount' );

		return ( $page_id && is_page( $page_id ) && ( isset( $wp->query_vars['payment-methods'] ) || isset( $wp->query_vars['add-payment-method'] ) ) );
	}
}

if ( ! function_exists( 'is_lost_password_page' ) ) {

	/**
	 * Is_lost_password_page - Returns true when viewing the lost password page.
	 *
	 * @return bool
	 */
	function is_lost_password_page() {
		global $wp;

		$page_id = wc_get_page_id( 'myaccount' );

		return ( $page_id && is_page( $page_id ) && isset( $wp->query_vars['lost-password'] ) );
	}
}

if ( ! function_exists( 'is_ajax' ) ) {

	/**
	 * Is_ajax - Returns true when the page is loaded via ajax.
	 *
	 * @see wp_doing_ajax() for an equivalent function provided by WordPress since 4.7.0
	 * @return bool
	 */
	function is_ajax() {
		return function_exists( 'wp_doing_ajax' ) ? wp_doing_ajax() : Constants::is_defined( 'DOING_AJAX' );
	}
}

if ( ! function_exists( 'is_store_notice_showing' ) ) {

	/**
	 * Is_store_notice_showing - Returns true when store notice is active.
	 *
	 * @return bool
	 */
	function is_store_notice_showing() {
		return 'no' !== get_option( 'woocommerce_demo_store', 'no' );
	}
}

if ( ! function_exists( 'is_filtered' ) ) {

	/**
	 * Is_filtered - Returns true when filtering products using layered nav or price sliders.
	 *
	 * @return bool
	 */
	function is_filtered() {
		return apply_filters( 'woocommerce_is_filtered', ( count( WC_Query::get_layered_nav_chosen_attributes() ) > 0 || isset( $_GET['max_price'] ) || isset( $_GET['min_price'] ) || isset( $_GET['rating_filter'] ) ) ); // WPCS: CSRF ok.
	}
}

if ( ! function_exists( 'taxonomy_is_product_attribute' ) ) {

	/**
	 * Returns true when the passed taxonomy name is a product attribute.
	 *
	 * @uses   $wc_product_attributes global which stores taxonomy names upon registration
	 * @param  string $name of the attribute.
	 * @return bool
	 */
	function taxonomy_is_product_attribute( $name ) {
		global $wc_product_attributes;

		return taxonomy_exists( $name ) && array_key_exists( $name, (array) $wc_product_attributes );
	}
}

if ( ! function_exists( 'meta_is_product_attribute' ) ) {

	/**
	 * Returns true when the passed meta name is a product attribute.
	 *
	 * @param  string $name of the attribute.
	 * @param  string $value of the attribute.
	 * @param  int    $product_id to check for attribute.
	 * @return bool
	 */
	function meta_is_product_attribute( $name, $value, $product_id ) {
		$product = wc_get_product( $product_id );

		if ( $product && method_exists( $product, 'get_variation_attributes' ) ) {
			$variation_attributes = $product->get_variation_attributes();
			$attributes           = $product->get_attributes();
			return ( in_array( $name, array_keys( $attributes ), true ) && in_array( $value, $variation_attributes[ $attributes[ $name ]['name'] ], true ) );
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'wc_tax_enabled' ) ) {

	/**
	 * Are store-wide taxes enabled?
	 *
	 * @return bool
	 */
	function wc_tax_enabled() {
		return apply_filters( 'wc_tax_enabled', get_option( 'woocommerce_calc_taxes' ) === 'yes' );
	}
}

if ( ! function_exists( 'wc_shipping_enabled' ) ) {

	/**
	 * Is shipping enabled?
	 *
	 * @return bool
	 */
	function wc_shipping_enabled() {
		return apply_filters( 'wc_shipping_enabled', get_option( 'woocommerce_ship_to_countries' ) !== 'disabled' );
	}
}

if ( ! function_exists( 'wc_prices_include_tax' ) ) {

	/**
	 * Are prices inclusive of tax?
	 *
	 * @return bool
	 */
	function wc_prices_include_tax() {
		return wc_tax_enabled() && apply_filters( 'woocommerce_prices_include_tax', get_option( 'woocommerce_prices_include_tax' ) === 'yes' );
	}
}

/**
 * Simple check for validating a URL, it must start with http:// or https://.
 * and pass FILTER_VALIDATE_URL validation.
 *
 * @param  string $url to check.
 * @return bool
 */
function wc_is_valid_url( $url ) {

	// Must start with http:// or https://.
	if ( 0 !== strpos( $url, 'http://' ) && 0 !== strpos( $url, 'https://' ) ) {
		return false;
	}

	// Must pass validation.
	if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
		return false;
	}

	return true;
}

/**
 * Check if the home URL is https. If it is, we don't need to do things such as 'force ssl'.
 *
 * @since  2.4.13
 * @return bool
 */
function wc_site_is_https() {
	return false !== strstr( get_option( 'home' ), 'https:' );
}

/**
 * Check if the checkout is configured for https. Look at options, WP HTTPS plugin, or the permalink itself.
 *
 * @since  2.5.0
 * @return bool
 */
function wc_checkout_is_https() {
	return wc_site_is_https() || 'yes' === get_option( 'woocommerce_force_ssl_checkout' ) || class_exists( 'WordPressHTTPS' ) || strstr( wc_get_page_permalink( 'checkout' ), 'https:' );
}

/**
 * Checks whether the content passed contains a specific short code.
 *
 * @param  string $tag Shortcode tag to check.
 * @return bool
 */
function wc_post_content_has_shortcode( $tag = '' ) {
	global $post;

	return is_singular() && is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
}

/**
 * Check if reviews are enabled.
 *
 * @since 3.6.0
 * @return bool
 */
function wc_reviews_enabled() {
	return 'yes' === get_option( 'woocommerce_enable_reviews' );
}

/**
 * Check if reviews ratings are enabled.
 *
 * @since 3.6.0
 * @return bool
 */
function wc_review_ratings_enabled() {
	return wc_reviews_enabled() && 'yes' === get_option( 'woocommerce_enable_review_rating' );
}

/**
 * Check if review ratings are required.
 *
 * @since 3.6.0
 * @return bool
 */
function wc_review_ratings_required() {
	return 'yes' === get_option( 'woocommerce_review_rating_required' );
}

/**
 * Check if a CSV file is valid.
 *
 * @since 3.6.5
 * @param string $file       File name.
 * @param bool   $check_path If should check for the path.
 * @return bool
 */
function wc_is_file_valid_csv( $file, $check_path = true ) {
	/**
	 * Filter check for CSV file path.
	 *
	 * @since 3.6.4
	 * @param bool   $check_import_file_path If requires file path check. Defaults to true.
	 * @param string $file                   Path of the file to be checked.
	 */
	$check_import_file_path = apply_filters( 'woocommerce_csv_importer_check_import_file_path', true, $file );

	if ( $check_path && $check_import_file_path && false !== stripos( $file, '://' ) ) {
		return false;
	}

	/**
	 * Filter CSV valid file types.
	 *
	 * @since 3.6.5
	 * @param array $valid_filetypes List of valid file types.
	 */
	$valid_filetypes = apply_filters(
		'woocommerce_csv_import_valid_filetypes',
		array(
			'csv' => 'text/csv',
			'txt' => 'text/plain',
		)
	);

	$filetype = wp_check_filetype( $file, $valid_filetypes );

	if ( in_array( $filetype['type'], $valid_filetypes, true ) ) {
		return true;
	}

	return false;
}

/**
 * Check if the current theme is a block theme.
 *
 * @since 6.0.0
 * @return bool
 */
function wc_current_theme_is_fse_theme() {
	if ( function_exists( 'wp_is_block_theme' ) ) {
		return (bool) wp_is_block_theme();
	}
	if ( function_exists( 'gutenberg_is_fse_theme' ) ) {
		return (bool) gutenberg_is_fse_theme();
	}

	return false;
}

/**
 * Check if the current theme has WooCommerce support or is a FSE theme.
 *
 * @since 6.0.0
 * @return bool
 */
function wc_current_theme_supports_woocommerce_or_fse() {
	return (bool) current_theme_supports( 'woocommerce' ) || wc_current_theme_is_fse_theme();
}

/**
 * Given an element name, returns a class name.
 *
 * If the WP-related function is not defined or current theme is not a FSE theme, return empty string.
 *
 * @param string $element The name of the element.
 *
 * @since 7.0.1
 * @return string
 */
function wc_wp_theme_get_element_class_name( $element ) {
	if ( wc_current_theme_is_fse_theme() && function_exists( 'wp_theme_get_element_class_name' ) ) {
		return wp_theme_get_element_class_name( $element );
	}

	return '';
}

/**
 * Given an element name, returns true or false depending on whether the
 * current theme has styles for that element defined in theme.json.
 *
 * If the theme is not a block theme or the WP-related function is not defined,
 * return false.
 *
 * @param string $element The name of the element.
 *
 * @since 7.4.0
 * @return bool
 */
function wc_block_theme_has_styles_for_element( $element ) {
	if (
		! wc_current_theme_is_fse_theme() ||
		wc_wp_theme_get_element_class_name( $element ) === ''
	) {
		return false;
	}

	if ( function_exists( 'wp_get_global_styles' ) ) {
		$global_styles = wp_get_global_styles();
		if (
			array_key_exists( 'elements', $global_styles ) &&
			array_key_exists( $element, $global_styles['elements'] )
		) {
			return is_array( $global_styles['elements'][ $element ] );
		}
	}

	return false;
}
