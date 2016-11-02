<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ajax method for wooCommerce cart.
 */
add_action( 'wp_ajax_nopriv_us_ajax_cart', 'us_ajax_cart' );
add_action( 'wp_ajax_us_ajax_cart', 'us_ajax_cart' );
function us_ajax_cart() {
	if ( ! class_exists( 'woocommerce' ) ) {
		wp_send_json_error();
	}

	global $woocommerce;

	$count = $woocommerce->cart->cart_contents_count;

	$result = array( 'count' => $count );

	wp_send_json_success( $result );

}
