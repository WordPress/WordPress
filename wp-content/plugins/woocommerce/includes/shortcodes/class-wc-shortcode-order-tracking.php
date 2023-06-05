<?php
/**
 * Order Tracking Shortcode
 *
 * Lets a user see the status of an order by entering their order details.
 *
 * @package WooCommerce\Shortcodes\Order_Tracking
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode order tracking class.
 */
class WC_Shortcode_Order_Tracking {

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public static function get( $atts ) {
		return WC_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public static function output( $atts ) {
		// Check cart class is loaded or abort.
		if ( is_null( WC()->cart ) ) {
			return;
		}

		$atts        = shortcode_atts( array(), $atts, 'woocommerce_order_tracking' );
		$nonce_value = wc_get_var( $_REQUEST['woocommerce-order-tracking-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

		if ( isset( $_REQUEST['orderid'] ) && wp_verify_nonce( $nonce_value, 'woocommerce-order_tracking' ) ) { // WPCS: input var ok.

			$order_id    = empty( $_REQUEST['orderid'] ) ? 0 : ltrim( wc_clean( wp_unslash( $_REQUEST['orderid'] ) ), '#' ); // WPCS: input var ok.
			$order_email = empty( $_REQUEST['order_email'] ) ? '' : sanitize_email( wp_unslash( $_REQUEST['order_email'] ) ); // WPCS: input var ok.

			if ( ! $order_id ) {
				wc_print_notice( __( 'Please enter a valid order ID', 'woocommerce' ), 'error' );
			} elseif ( ! $order_email ) {
				wc_print_notice( __( 'Please enter a valid email address', 'woocommerce' ), 'error' );
			} else {
				$order = wc_get_order( apply_filters( 'woocommerce_shortcode_order_tracking_order_id', $order_id ) );

				if ( $order && $order->get_id() && is_a( $order, 'WC_Order' ) && strtolower( $order->get_billing_email() ) === strtolower( $order_email ) ) {
					do_action( 'woocommerce_track_order', $order->get_id() );
					wc_get_template(
						'order/tracking.php',
						array(
							'order' => $order,
						)
					);
					return;
				} else {
					wc_print_notice( __( 'Sorry, the order could not be found. Please contact us if you are having difficulty finding your order details.', 'woocommerce' ), 'error' );
				}
			}
		}

		wc_get_template( 'order/form-tracking.php' );
	}
}
