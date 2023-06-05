<?php
/**
 * WooCommerce Coupons Functions
 *
 * Functions for coupon specific things.
 *
 * @package WooCommerce\Functions
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Utilities\StringUtil;

/**
 * Get coupon types.
 *
 * @return array
 */
function wc_get_coupon_types() {
	return (array) apply_filters(
		'woocommerce_coupon_discount_types',
		array(
			'percent'       => __( 'Percentage discount', 'woocommerce' ),
			'fixed_cart'    => __( 'Fixed cart discount', 'woocommerce' ),
			'fixed_product' => __( 'Fixed product discount', 'woocommerce' ),
		)
	);
}

/**
 * Get a coupon type's name.
 *
 * @param string $type Coupon type.
 * @return string
 */
function wc_get_coupon_type( $type = '' ) {
	$types = wc_get_coupon_types();
	return isset( $types[ $type ] ) ? $types[ $type ] : '';
}

/**
 * Coupon types that apply to individual products. Controls which validation rules will apply.
 *
 * @since  2.5.0
 * @return array
 */
function wc_get_product_coupon_types() {
	return (array) apply_filters( 'woocommerce_product_coupon_types', array( 'fixed_product', 'percent' ) );
}

/**
 * Coupon types that apply to the cart as a whole. Controls which validation rules will apply.
 *
 * @since  2.5.0
 * @return array
 */
function wc_get_cart_coupon_types() {
	return (array) apply_filters( 'woocommerce_cart_coupon_types', array( 'fixed_cart' ) );
}

/**
 * Check if coupons are enabled.
 * Filterable.
 *
 * @since  2.5.0
 *
 * @return bool
 */
function wc_coupons_enabled() {
	return apply_filters( 'woocommerce_coupons_enabled', 'yes' === get_option( 'woocommerce_enable_coupons' ) );
}

/**
 * Get coupon code by ID.
 *
 * @since 3.0.0
 * @param int $id Coupon ID.
 * @return string
 */
function wc_get_coupon_code_by_id( $id ) {
	$data_store = WC_Data_Store::load( 'coupon' );
	return empty( $id ) ? '' : (string) $data_store->get_code_by_id( $id );
}

/**
 * Get coupon ID by code.
 *
 * @since 3.0.0
 * @param string $code    Coupon code.
 * @param int    $exclude Used to exclude an ID from the check if you're checking existence.
 * @return int
 */
function wc_get_coupon_id_by_code( $code, $exclude = 0 ) {

	if ( StringUtil::is_null_or_whitespace( $code ) ) {
		return 0;
	}

	$data_store = WC_Data_Store::load( 'coupon' );
	$ids        = wp_cache_get( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_' . $code, 'coupons' );

	if ( false === $ids ) {
		$ids = $data_store->get_ids_by_code( $code );
		if ( $ids ) {
			wp_cache_set( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_' . $code, $ids, 'coupons' );
		}
	}

	$ids = array_diff( array_filter( array_map( 'absint', (array) $ids ) ), array( $exclude ) );

	return apply_filters( 'woocommerce_get_coupon_id_from_code', absint( current( $ids ) ), $code, $exclude );
}
