<?php
namespace Automattic\WooCommerce\Blocks\Utils;

/**
 * Class containing utility methods for dealing with the Cart and Checkout blocks.
 */
class CartCheckoutUtils {

	/**
	 * Checks if the default cart page is using the Cart block.
	 *
	 * @return bool true if the WC cart page is using the Cart block.
	 */
	public static function is_cart_block_default() {
		$cart_page_id = wc_get_page_id( 'cart' );
		return $cart_page_id && has_block( 'woocommerce/cart', $cart_page_id );
	}

	/**
	 * Checks if the default checkout page is using the Checkout block.
	 *
	 * @return bool true if the WC checkout page is using the Checkout block.
	 */
	public static function is_checkout_block_default() {
		$checkout_page_id = wc_get_page_id( 'checkout' );
		return $checkout_page_id && has_block( 'woocommerce/checkout', $checkout_page_id );
	}
}
