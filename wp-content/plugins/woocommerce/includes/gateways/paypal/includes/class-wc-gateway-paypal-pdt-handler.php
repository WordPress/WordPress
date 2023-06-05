<?php
/**
 * Class WC_Gateway_Paypal_PDT_Handler file.
 *
 * @package WooCommerce\Gateways
 */

use Automattic\Jetpack\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/class-wc-gateway-paypal-response.php';

/**
 * Handle PDT Responses from PayPal.
 */
class WC_Gateway_Paypal_PDT_Handler extends WC_Gateway_Paypal_Response {

	/**
	 * Identity token for PDT support
	 *
	 * @var string
	 */
	protected $identity_token;

	/**
	 * Receiver email address to validate.
	 *
	 * @var string Receiver email address.
	 */
	protected $receiver_email;

	/**
	 * Constructor.
	 *
	 * @param bool   $sandbox Whether to use sandbox mode or not.
	 * @param string $identity_token Identity token for PDT support.
	 */
	public function __construct( $sandbox = false, $identity_token = '' ) {
		add_action( 'woocommerce_thankyou_paypal', array( $this, 'check_response_for_order' ) );
		$this->identity_token = $identity_token;
		$this->sandbox        = $sandbox;
	}

	/**
	 * Set receiver email to enable more strict validation.
	 *
	 * @param string $receiver_email Email to receive PDT notification from.
	 */
	public function set_receiver_email( $receiver_email = '' ) {
		$this->receiver_email = $receiver_email;
	}

	/**
	 * Validate a PDT transaction to ensure its authentic.
	 *
	 * @param  string $transaction TX ID.
	 * @return bool|array False or result array if successful and valid.
	 */
	protected function validate_transaction( $transaction ) {
		$pdt = array(
			'body'        => array(
				'cmd' => '_notify-synch',
				'tx'  => $transaction,
				'at'  => $this->identity_token,
			),
			'timeout'     => 60,
			'httpversion' => '1.1',
			'user-agent'  => 'WooCommerce/' . Constants::get_constant( 'WC_VERSION' ),
		);

		// Post back to get a response.
		$response = wp_safe_remote_post( $this->sandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr', $pdt );

		if ( is_wp_error( $response ) || strpos( $response['body'], 'SUCCESS' ) !== 0 ) {
			return false;
		}

		// Parse transaction result data.
		$transaction_result  = array_map( 'wc_clean', array_map( 'urldecode', explode( "\n", $response['body'] ) ) );
		$transaction_results = array();

		foreach ( $transaction_result as $line ) {
			$line                            = explode( '=', $line );
			$transaction_results[ $line[0] ] = isset( $line[1] ) ? $line[1] : '';
		}

		if ( ! empty( $transaction_results['charset'] ) && function_exists( 'iconv' ) ) {
			foreach ( $transaction_results as $key => $value ) {
				$transaction_results[ $key ] = iconv( $transaction_results['charset'], 'utf-8', $value );
			}
		}

		return $transaction_results;
	}

	/**
	 * Check Response for PDT, taking the order id from the request.
	 *
	 * @deprecated 6.4 Use check_response_for_order instead.
	 */
	public function check_response() {
		global $wp;
		$order_id = apply_filters( 'woocommerce_thankyou_order_id', absint( $wp->query_vars['order-received'] ) );

		$this->check_response_for_order( $order_id );
	}

	/**
	 * Check Response for PDT.
	 *
	 * @since 6.4
	 *
	 * @param mixed $wc_order_id The order id to check the response against.
	 */
	public function check_response_for_order( $wc_order_id ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_REQUEST['tx'] ) ) {
			return;
		}

		$wc_order = wc_get_order( $wc_order_id );
		if ( ! $wc_order->needs_payment() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$transaction        = wc_clean( wp_unslash( $_REQUEST['tx'] ) );
		$transaction_result = $this->validate_transaction( $transaction );

		if ( $transaction_result ) {
			$status = strtolower( $transaction_result['payment_status'] );
			$amount = isset( $transaction_result['mc_gross'] ) ? $transaction_result['mc_gross'] : 0;
			$order  = $this->get_paypal_order( $transaction_result['custom'] );

			if ( ! $order ) {
				// No valid WC order found on tx data.
				return;
			}

			if ( $wc_order->get_id() !== $order->get_id() ) {
				/* translators: 1: order ID, 2: order ID. */
				WC_Gateway_Paypal::log( sprintf( __( 'Received PDT notification for order %1$d on endpoint for order %2$d.', 'woocommerce' ), $order->get_id(), $wc_order_id ), 'error' );
				return;
			}

			if ( 0 !== strcasecmp( trim( $transaction_result['receiver_email'] ), trim( $this->receiver_email ) ) ) {
				/* translators: 1: email address, 2: order ID . */
				WC_Gateway_Paypal::log( sprintf( __( 'Received PDT notification for another account: %1$s. Order ID: %2$d.', 'woocommerce' ), $transaction_result['receiver_email'], $order->get_id() ), 'error' );
				return;
			}

			// We have a valid response from PayPal.
			WC_Gateway_Paypal::log( 'PDT Transaction Status: ' . wc_print_r( $status, true ) );

			$order->add_meta_data( '_paypal_status', $status );
			$order->set_transaction_id( $transaction );

			if ( 'completed' === $status ) {
				if ( number_format( $order->get_total(), 2, '.', '' ) !== number_format( $amount, 2, '.', '' ) ) {
					WC_Gateway_Paypal::log( 'Payment error: Amounts do not match (amt ' . $amount . ')', 'error' );
					/* translators: 1: Payment amount */
					$this->payment_on_hold( $order, sprintf( __( 'Validation error: PayPal amounts do not match (amt %s).', 'woocommerce' ), $amount ) );
				} else {
					// Log paypal transaction fee and payment type.
					if ( ! empty( $transaction_result['mc_fee'] ) ) {
						$order->add_meta_data( 'PayPal Transaction Fee', wc_clean( $transaction_result['mc_fee'] ) );
					}
					if ( ! empty( $transaction_result['payment_type'] ) ) {
						$order->add_meta_data( 'Payment type', wc_clean( $transaction_result['payment_type'] ) );
					}

					$this->payment_complete( $order, $transaction, __( 'PDT payment completed', 'woocommerce' ) );
				}
			} else {
				if ( 'authorization' === $transaction_result['pending_reason'] ) {
					$this->payment_on_hold( $order, __( 'Payment authorized. Change payment status to processing or complete to capture funds.', 'woocommerce' ) );
				} else {
					/* translators: 1: Pending reason */
					$this->payment_on_hold( $order, sprintf( __( 'Payment pending (%s).', 'woocommerce' ), $transaction_result['pending_reason'] ) );
				}
			}
		} else {
			WC_Gateway_Paypal::log( 'Received invalid response from PayPal PDT' );
		}
	}
}
