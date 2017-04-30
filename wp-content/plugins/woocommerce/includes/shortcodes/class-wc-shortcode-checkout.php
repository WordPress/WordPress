<?php
/**
 * Checkout Shortcode
 *
 * Used on the checkout page, the checkout shortcode displays the checkout process.
 *
 * @author 		WooThemes
 * @category 	Shortcodes
 * @package 	WooCommerce/Shortcodes/Checkout
 * @version     2.0.0
 */

class WC_Shortcode_Checkout {

	/**
	 * Get the shortcode content.
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function get( $atts ) {
		return WC_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $atts ) {
		global $woocommerce, $wp;

		// Check cart class is loaded or abort
		if ( is_null( WC()->cart ) ) {
			return;
		}

		// Backwards compat with old pay and thanks link arguments
		if ( isset( $_GET['order'] ) && isset( $_GET['key'] ) ) {
			_deprecated_argument( __CLASS__ . '->' . __FUNCTION__, '2.1', '"order" is no longer used to pass an order ID. Use the order-pay or order-received endpoint instead.' );

			// Get the order to work out what we are showing
			$order_id             = absint( $_GET['order'] );
			$order                = new WC_Order( $order_id );

			if ( $order->status == 'pending' )
				$wp->query_vars['order-pay'] = absint( $_GET['order'] );
			else
				$wp->query_vars['order-received'] = absint( $_GET['order'] );
		}

		// Handle checkout actions
		if ( ! empty( $wp->query_vars['order-pay'] ) ) {

			self::order_pay( $wp->query_vars['order-pay'] );

		} elseif ( isset( $wp->query_vars['order-received'] ) ) {

			self::order_received( $wp->query_vars['order-received'] );

		} else {

			self::checkout();

		}
	}

	/**
	 * Show the pay page
	 */
	private static function order_pay( $order_id ) {

		do_action( 'before_woocommerce_pay' );

		wc_print_notices();

		$order_id = absint( $order_id );

		// Handle payment
		if ( isset( $_GET['pay_for_order'] ) && isset( $_GET['key'] ) && $order_id ) {

			// Pay for existing order
			$order_key            = $_GET[ 'key' ];
			$order                = new WC_Order( $order_id );
			$valid_order_statuses = apply_filters( 'woocommerce_valid_order_statuses_for_payment', array( 'pending', 'failed' ), $order );

			if ( ! current_user_can( 'pay_for_order', $order_id ) ) {
				echo '<div class="woocommerce-error">' . __( 'Invalid order. If you have an account please log in and try again.', 'woocommerce' ) . ' <a href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '" class="wc-forward">' . __( 'My Account', 'woocommerce' ) . '</a>' . '</div>';
				return;
			}

			if ( $order->id == $order_id && $order->order_key == $order_key ) {

				if ( in_array( $order->status, $valid_order_statuses ) ) {

					// Set customer location to order location
					if ( $order->billing_country )
						WC()->customer->set_country( $order->billing_country );
					if ( $order->billing_state )
						WC()->customer->set_state( $order->billing_state );
					if ( $order->billing_postcode )
						WC()->customer->set_postcode( $order->billing_postcode );

					wc_get_template( 'checkout/form-pay.php', array( 'order' => $order ) );

				} else {

					$status = get_term_by('slug', $order->status, 'shop_order_status');

					wc_add_notice( sprintf( __( 'This order&rsquo;s status is &ldquo;%s&rdquo;&mdash;it cannot be paid for. Please contact us if you need assistance.', 'woocommerce' ), $status->name ), 'error' );
				}

			} else {
				wc_add_notice( __( 'Sorry, this order is invalid and cannot be paid for.', 'woocommerce' ), 'error' );
			}

		} elseif ( $order_id ) {

			// Pay for order after checkout step
			$order_key            = isset( $_GET['key'] ) ? wc_clean( $_GET['key'] ) : '';
			$order                = new WC_Order( $order_id );
			$valid_order_statuses = apply_filters( 'woocommerce_valid_order_statuses_for_payment', array( 'pending', 'failed' ), $order );

			if ( $order->id == $order_id && $order->order_key == $order_key ) {

				if ( in_array( $order->status, $valid_order_statuses ) ) {

					?>
					<ul class="order_details">
						<li class="order">
							<?php _e( 'Order:', 'woocommerce' ); ?>
							<strong><?php echo $order->get_order_number(); ?></strong>
						</li>
						<li class="date">
							<?php _e( 'Date:', 'woocommerce' ); ?>
							<strong><?php echo date_i18n(get_option('date_format'), strtotime($order->order_date)); ?></strong>
						</li>
						<li class="total">
							<?php _e( 'Total:', 'woocommerce' ); ?>
							<strong><?php echo $order->get_formatted_order_total(); ?></strong>
						</li>
						<?php if ($order->payment_method_title) : ?>
						<li class="method">
							<?php _e( 'Payment method:', 'woocommerce' ); ?>
							<strong><?php
								echo $order->payment_method_title;
							?></strong>
						</li>
						<?php endif; ?>
					</ul>

					<?php do_action( 'woocommerce_receipt_' . $order->payment_method, $order_id ); ?>

					<div class="clear"></div>
					<?php

				} else {

					$status = get_term_by('slug', $order->status, 'shop_order_status');

					wc_add_notice( sprintf( __( 'This order&rsquo;s status is &ldquo;%s&rdquo;&mdash;it cannot be paid for. Please contact us if you need assistance.', 'woocommerce' ), $status->name ), 'error' );
				}

			} else {
				wc_add_notice( __( 'Sorry, this order is invalid and cannot be paid for.', 'woocommerce' ), 'error' );
			}

		} else {
			wc_add_notice( __( 'Invalid order.', 'woocommerce' ), 'error' );
		}

		wc_print_notices();

		do_action( 'after_woocommerce_pay' );
	}

	/**
	 * Show the thanks page
	 */
	private static function order_received( $order_id = 0 ) {

		wc_print_notices();

		$order = false;

		// Get the order
		$order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $order_id ) );
		$order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( $_GET['key'] ) );

		if ( $order_id > 0 ) {
			$order = new WC_Order( $order_id );
			if ( $order->order_key != $order_key )
				unset( $order );
		}

		// Empty awaiting payment session
		unset( WC()->session->order_awaiting_payment );

		wc_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );
	}

	/**
	 * Show the checkout
	 */
	private static function checkout() {

		// Show non-cart errors
		wc_print_notices();

		// Check cart has contents
		if ( sizeof( WC()->cart->get_cart() ) == 0 )
			return;

		// Calc totals
		WC()->cart->calculate_totals();

		// Check cart contents for errors
		do_action('woocommerce_check_cart_items');

		// Get checkout object
		$checkout = WC()->checkout();

		if ( empty( $_POST ) && wc_notice_count( 'error' ) > 0 ) {

			wc_get_template( 'checkout/cart-errors.php', array( 'checkout' => $checkout ) );

		} else {

			$non_js_checkout = ! empty( $_POST['woocommerce_checkout_update_totals'] ) ? true : false;

			if ( wc_notice_count( 'error' ) == 0 && $non_js_checkout )
				wc_add_notice( __( 'The order totals have been updated. Please confirm your order by pressing the Place Order button at the bottom of the page.', 'woocommerce' ) );

			wc_get_template( 'checkout/form-checkout.php', array( 'checkout' => $checkout ) );

		}
	}
}
