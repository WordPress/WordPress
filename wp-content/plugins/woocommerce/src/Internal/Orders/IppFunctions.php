<?php


namespace Automattic\WooCommerce\Internal\Orders;

use WC_Order;

/**
 * Class with methods for handling order In-Person Payments.
 */
class IppFunctions {

	/**
	 * Returns if order is eligible to accept In-Person Payments.
	 *
	 * @param WC_Order $order order that the conditions are checked for.
	 *
	 * @return bool true if order is eligible, false otherwise
	 */
	public static function is_order_in_person_payment_eligible( WC_Order $order ): bool {
		$has_status            = in_array( $order->get_status(), array( 'pending', 'on-hold', 'processing' ), true );
		$has_payment_method    = in_array( $order->get_payment_method(), array( 'cod', 'woocommerce_payments', 'none' ), true );
		$order_is_not_paid     = null === $order->get_date_paid();
		$order_is_not_refunded = empty( $order->get_refunds() );

		$order_has_no_subscription_products = true;
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();

			if ( is_object( $product ) && $product->is_type( 'subscription' ) ) {
				$order_has_no_subscription_products = false;
				break;
			}
		}

		return $has_status && $has_payment_method && $order_is_not_paid && $order_is_not_refunded && $order_has_no_subscription_products;
	}

	/**
	 * Returns if store is eligible to accept In-Person Payments.
	 *
	 * @return bool true if store is eligible, false otherwise
	 */
	public static function is_store_in_person_payment_eligible(): bool {
		$is_store_usa_based    = self::has_store_specified_country_currency( 'US', 'USD' );
		$is_store_canada_based = self::has_store_specified_country_currency( 'CA', 'CAD' );

		return $is_store_usa_based || $is_store_canada_based;
	}

	/**
	 * Checks if the store has specified country location and currency used.
	 *
	 * @param string $country country to compare store's country with.
	 * @param string $currency currency to compare store's currency with.
	 *
	 * @return bool true if specified country and currency match the store's ones. false otherwise
	 */
	public static function has_store_specified_country_currency( string $country, string $currency ): bool {
		return ( WC()->countries->get_base_country() === $country && get_woocommerce_currency() === $currency );
	}
}
