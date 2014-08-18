<?php
/**
 * WooCommerce Coupons Functions
 *
 * Functions for coupon specific things.
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	WooCommerce/Functions
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get coupon types.
 *
 * @return array
 */
function wc_get_coupon_types() {
	return (array) apply_filters( 'woocommerce_coupon_discount_types', array(
		'fixed_cart'      => __( 'Cart Discount', 'woocommerce' ),
		'percent'         => __( 'Cart % Discount', 'woocommerce' ),
		'fixed_product'   => __( 'Product Discount', 'woocommerce' ),
		'percent_product' => __( 'Product % Discount', 'woocommerce' )
	) );
}

/**
 * Get a coupon type's name.
 *
 * @param string $type (default: '')
 * @return string
 */
function wc_get_coupon_type( $type = '' ) {
	$types = wc_get_coupon_types();
	if ( isset( $types[ $type ] ) )
		return $types[ $type ];

	return '';
}
