<?php
/**
 * Checkout Shortcode
 *
 * Used on the checkout page, the checkout shortcode displays the checkout process.
 *
 * @package WooCommerce\Shortcodes\Checkout
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode checkout class.
 */
class WC_Shortcode_Checkout {

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
		global $wp;

		// Check cart class is loaded or abort.
		if ( is_null( WC()->cart ) ) {
			return;
		}

		// Backwards compatibility with old pay and thanks link arguments.
		if ( isset( $_GET['order'] ) && isset( $_GET['key'] ) ) { // WPCS: input var ok, CSRF ok.
			wc_deprecated_argument( __CLASS__ . '->' . __FUNCTION__, '2.1', '"order" is no longer used to pass an order ID. Use the order-pay or order-received endpoint instead.' );

			// Get the order to work out what we are showing.
			$order_id = absint( $_GET['order'] ); // WPCS: input var ok.
			$order    = wc_get_order( $order_id );

			if ( $order && $order->has_status( 'pending' ) ) {
				$wp->query_vars['order-pay'] = absint( $_GET['order'] ); // WPCS: input var ok.
			} else {
				$wp->query_vars['order-received'] = absint( $_GET['order'] ); // WPCS: input var ok.
			}
		}

		// Handle checkout actions.
		if ( ! empty( $wp->query_vars['order-pay'] ) ) {

			self::order_pay( $wp->query_vars['order-pay'] );

		} elseif ( isset( $wp->query_vars['order-received'] ) ) {

			self::order_received( $wp->query_vars['order-received'] );

		} else {

			self::checkout();

		}
	}

	/**
	 * Show the pay page.
	 *
	 * @throws Exception When validate fails.
	 * @param int $order_id Order ID.
	 */
	private static function order_pay( $order_id ) {

		do_action( 'before_woocommerce_pay' );

		$order_id = absint( $order_id );

		// Pay for existing order.
		if ( isset( $_GET['pay_for_order'], $_GET['key'] ) && $order_id ) { // WPCS: input var ok, CSRF ok.
			try {
				$order_key = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : ''; // WPCS: input var ok, CSRF ok.
				$order     = wc_get_order( $order_id );

				// Order or payment link is invalid.
				if ( ! $order || $order->get_id() !== $order_id || ! hash_equals( $order->get_order_key(), $order_key ) ) {
					throw new Exception( __( 'Sorry, this order is invalid and cannot be paid for.', 'woocommerce' ) );
				}

				// Logged out customer does not have permission to pay for this order.
				if ( ! current_user_can( 'pay_for_order', $order_id ) && ! is_user_logged_in() ) {
					wc_print_notice( esc_html__( 'Please log in to your account below to continue to the payment form.', 'woocommerce' ), 'notice' );
					woocommerce_login_form(
						array(
							'redirect' => $order->get_checkout_payment_url(),
						)
					);
					return;
				}

				// Add notice if logged in customer is trying to pay for guest order.
				if ( ! $order->get_user_id() && is_user_logged_in() ) {
					// If order has does not have same billing email then current logged in user then show warning.
					if ( $order->get_billing_email() !== wp_get_current_user()->user_email ) {
						wc_print_notice( __( 'You are paying for a guest order. Please continue with payment only if you recognize this order.', 'woocommerce' ), 'error' );
					}
				}

				// Logged in customer trying to pay for someone else's order.
				if ( ! current_user_can( 'pay_for_order', $order_id ) ) {
					throw new Exception( __( 'This order cannot be paid for. Please contact us if you need assistance.', 'woocommerce' ) );
				}

				// Does not need payment.
				if ( ! $order->needs_payment() ) {
					/* translators: %s: order status */
					throw new Exception( sprintf( __( 'This order&rsquo;s status is &ldquo;%s&rdquo;&mdash;it cannot be paid for. Please contact us if you need assistance.', 'woocommerce' ), wc_get_order_status_name( $order->get_status() ) ) );
				}

				// Ensure order items are still stocked if paying for a failed order. Pending orders do not need this check because stock is held.
				if ( ! $order->has_status( wc_get_is_pending_statuses() ) ) {
					$quantities = array();

					foreach ( $order->get_items() as $item_key => $item ) {
						if ( $item && is_callable( array( $item, 'get_product' ) ) ) {
							$product = $item->get_product();

							if ( ! $product ) {
								continue;
							}

							$quantities[ $product->get_stock_managed_by_id() ] = isset( $quantities[ $product->get_stock_managed_by_id() ] ) ? $quantities[ $product->get_stock_managed_by_id() ] + $item->get_quantity() : $item->get_quantity();
						}
					}

					// Stock levels may already have been adjusted for this order (in which case we don't need to worry about checking for low stock).
					if ( ! $order->get_data_store()->get_stock_reduced( $order->get_id() ) ) {
						foreach ( $order->get_items() as $item_key => $item ) {
							if ( $item && is_callable( array( $item, 'get_product' ) ) ) {
								$product = $item->get_product();

								if ( ! $product ) {
									continue;
								}

								if ( ! apply_filters( 'woocommerce_pay_order_product_in_stock', $product->is_in_stock(), $product, $order ) ) {
									/* translators: %s: product name */
									throw new Exception( sprintf( __( 'Sorry, "%s" is no longer in stock so this order cannot be paid for. We apologize for any inconvenience caused.', 'woocommerce' ), $product->get_name() ) );
								}

								// We only need to check products managing stock, with a limited stock qty.
								if ( ! $product->managing_stock() || $product->backorders_allowed()  ) {
									continue;
								}

								// Check stock based on all items in the cart and consider any held stock within pending orders.
								$held_stock     = wc_get_held_stock_quantity( $product, $order->get_id() );
								$required_stock = $quantities[ $product->get_stock_managed_by_id() ];

								if ( ! apply_filters( 'woocommerce_pay_order_product_has_enough_stock', ( $product->get_stock_quantity() >= ( $held_stock + $required_stock ) ), $product, $order ) ) {
									/* translators: 1: product name 2: quantity in stock */
									throw new Exception( sprintf( __( 'Sorry, we do not have enough "%1$s" in stock to fulfill your order (%2$s available). We apologize for any inconvenience caused.', 'woocommerce' ), $product->get_name(), wc_format_stock_quantity_for_display( $product->get_stock_quantity() - $held_stock, $product ) ) );
								}
							}
						}
					}
				}

				WC()->customer->set_props(
					array(
						'billing_country'  => $order->get_billing_country() ? $order->get_billing_country() : null,
						'billing_state'    => $order->get_billing_state() ? $order->get_billing_state() : null,
						'billing_postcode' => $order->get_billing_postcode() ? $order->get_billing_postcode() : null,
					)
				);
				WC()->customer->save();

				$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

				if ( count( $available_gateways ) ) {
					current( $available_gateways )->set_current();
				}

				/**
				 * Allows the text of the submit button on the Pay for Order page to be changed.
				 *
				 * @param string $text The text of the button.
				 *
				 * @since 3.0.2
				 */
				$order_button_text = apply_filters( 'woocommerce_pay_order_button_text', __( 'Pay for order', 'woocommerce' ) );

				/**
				 * Triggered right before the Pay for Order form, after validation of the order and customer.
				 *
				 * @param WC_Order $order              The order that is being paid for.
				 * @param string   $order_button_text  The text for the submit button.
				 * @param array    $available_gateways All available gateways.
				 *
				 * @since 6.6
				 */
				do_action( 'before_woocommerce_pay_form', $order, $order_button_text, $available_gateways );

				wc_get_template(
					'checkout/form-pay.php',
					array(
						'order'              => $order,
						'available_gateways' => $available_gateways,
						'order_button_text'  => $order_button_text,
					)
				);

			} catch ( Exception $e ) {
				wc_print_notice( $e->getMessage(), 'error' );
			}
		} elseif ( $order_id ) {

			// Pay for order after checkout step.
			$order_key = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : ''; // WPCS: input var ok, CSRF ok.
			$order     = wc_get_order( $order_id );

			if ( $order && $order->get_id() === $order_id && hash_equals( $order->get_order_key(), $order_key ) ) {

				if ( $order->needs_payment() ) {

					wc_get_template( 'checkout/order-receipt.php', array( 'order' => $order ) );

				} else {
					/* translators: %s: order status */
					wc_print_notice( sprintf( __( 'This order&rsquo;s status is &ldquo;%s&rdquo;&mdash;it cannot be paid for. Please contact us if you need assistance.', 'woocommerce' ), wc_get_order_status_name( $order->get_status() ) ), 'error' );
				}
			} else {
				wc_print_notice( __( 'Sorry, this order is invalid and cannot be paid for.', 'woocommerce' ), 'error' );
			}
		} else {
			wc_print_notice( __( 'Invalid order.', 'woocommerce' ), 'error' );
		}

		do_action( 'after_woocommerce_pay' );
	}

	/**
	 * Show the thanks page.
	 *
	 * @param int $order_id Order ID.
	 */
	private static function order_received( $order_id = 0 ) {
		$order = false;

		// Get the order.
		$order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $order_id ) );
		$order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( wp_unslash( $_GET['key'] ) ) ); // WPCS: input var ok, CSRF ok.

		if ( $order_id > 0 ) {
			$order = wc_get_order( $order_id );
			if ( ! $order || ! hash_equals( $order->get_order_key(), $order_key ) ) {
				$order = false;
			}
		}

		// Empty awaiting payment session.
		unset( WC()->session->order_awaiting_payment );

		// In case order is created from admin, but paid by the actual customer, store the ip address of the payer
		// when they visit the payment confirmation page.
		if ( $order && $order->is_created_via( 'admin' ) ) {
			$order->set_customer_ip_address( WC_Geolocation::get_ip_address() );
			$order->save();
		}

		// Empty current cart.
		wc_empty_cart();

		wc_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );
	}

	/**
	 * Show the checkout.
	 */
	private static function checkout() {
		// Show non-cart errors.
		do_action( 'woocommerce_before_checkout_form_cart_notices' );

		// Check cart has contents.
		if ( WC()->cart->is_empty() && ! is_customize_preview() && apply_filters( 'woocommerce_checkout_redirect_empty_cart', true ) ) {
			return;
		}

		// Check cart contents for errors.
		do_action( 'woocommerce_check_cart_items' );

		// Calc totals.
		WC()->cart->calculate_totals();

		// Get checkout object.
		$checkout = WC()->checkout();

		if ( empty( $_POST ) && wc_notice_count( 'error' ) > 0 ) { // WPCS: input var ok, CSRF ok.

			wc_get_template( 'checkout/cart-errors.php', array( 'checkout' => $checkout ) );
			wc_clear_notices();

		} else {

			$non_js_checkout = ! empty( $_POST['woocommerce_checkout_update_totals'] ); // WPCS: input var ok, CSRF ok.

			if ( wc_notice_count( 'error' ) === 0 && $non_js_checkout ) {
				wc_add_notice( __( 'The order totals have been updated. Please confirm your order by pressing the "Place order" button at the bottom of the page.', 'woocommerce' ) );
			}

			wc_get_template( 'checkout/form-checkout.php', array( 'checkout' => $checkout ) );

		}
	}
}
