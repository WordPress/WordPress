<?php
namespace Automattic\WooCommerce\Blocks\Payments;

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

interface PaymentMethodTypeInterface extends IntegrationInterface {
	/**
	 * Returns if this payment method should be active. If false, the scripts will not be enqueued.
	 *
	 * @return boolean
	 */
	public function is_active();

	/**
	 * Returns an array of script handles to enqueue for this payment method in
	 * the frontend context
	 *
	 * @return string[]
	 */
	public function get_payment_method_script_handles();

	/**
	 * Returns an array of script handles to enqueue for this payment method in
	 * the admin context
	 *
	 * @return string[]
	 */
	public function get_payment_method_script_handles_for_admin();

	/**
	 * An array of key, value pairs of data made available to payment methods
	 * client side.
	 *
	 * @return array
	 */
	public function get_payment_method_data();

	/**
	 * Get array of supported features.
	 *
	 * @return string[]
	 */
	public function get_supported_features();
}
