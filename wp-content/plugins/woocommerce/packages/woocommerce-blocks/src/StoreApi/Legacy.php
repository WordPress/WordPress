<?php
namespace Automattic\WooCommerce\StoreApi;

use Automattic\WooCommerce\StoreApi\Payments\PaymentContext;
use Automattic\WooCommerce\StoreApi\Payments\PaymentResult;
use Automattic\WooCommerce\StoreApi\Utilities\NoticeHandler;
use Automattic\WooCommerce\Blocks\Package;

/**
 * Legacy class.
 */
class Legacy {
	/**
	 * Hook into WP lifecycle events.
	 */
	public function init() {
		add_action( 'woocommerce_rest_checkout_process_payment_with_context', array( $this, 'process_legacy_payment' ), 999, 2 );
	}

	/**
	 * Attempt to process a payment for the checkout API if no payment methods support the
	 * woocommerce_rest_checkout_process_payment_with_context action.
	 *
	 * @param PaymentContext $context Holds context for the payment.
	 * @param PaymentResult  $result  Result of the payment.
	 */
	public function process_legacy_payment( PaymentContext $context, PaymentResult &$result ) {
		if ( $result->status ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification
		$post_data = $_POST;

		// Set constants.
		wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );

		// Add the payment data from the API to the POST global.
		$_POST = $context->payment_data;

		// Call the process payment method of the chosen gateway.
		$payment_method_object = $context->get_payment_method_instance();

		if ( ! $payment_method_object instanceof \WC_Payment_Gateway ) {
			return;
		}

		$payment_method_object->validate_fields();

		// If errors were thrown, we need to abort.
		NoticeHandler::convert_notices_to_exceptions( 'woocommerce_rest_payment_error' );

		// Process Payment.
		$gateway_result = $payment_method_object->process_payment( $context->order->get_id() );

		// Restore $_POST data.
		$_POST = $post_data;

		// If `process_payment` added notices, clear them. Notices are not displayed from the API -- payment should fail,
		// and a generic notice will be shown instead if payment failed.
		wc_clear_notices();

		// Handle result.
		$result->set_status( isset( $gateway_result['result'] ) && 'success' === $gateway_result['result'] ? 'success' : 'failure' );

		// set payment_details from result.
		$result->set_payment_details( array_merge( $result->payment_details, $gateway_result ) );
		$result->set_redirect_url( $gateway_result['redirect'] );
	}
}
