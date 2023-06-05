<?php
namespace Automattic\WooCommerce\Blocks\Payments;

use Automattic\WooCommerce\Blocks\Integrations\IntegrationRegistry;

/**
 * Class used for interacting with payment method types.
 *
 * @since 2.6.0
 */
final class PaymentMethodRegistry extends IntegrationRegistry {
	/**
	 * Integration identifier is used to construct hook names and is given when the integration registry is initialized.
	 *
	 * @var string
	 */
	protected $registry_identifier = 'payment_method_type';

	/**
	 * Retrieves all registered payment methods that are also active.
	 *
	 * @return PaymentMethodTypeInterface[]
	 */
	public function get_all_active_registered() {
		return array_filter(
			$this->get_all_registered(),
			function( $payment_method ) {
				return $payment_method->is_active();
			}
		);
	}

	/**
	 * Gets an array of all registered payment method script handles, but only for active payment methods.
	 *
	 * @return string[]
	 */
	public function get_all_active_payment_method_script_dependencies() {
		$script_handles  = [];
		$payment_methods = $this->get_all_active_registered();

		foreach ( $payment_methods as $payment_method ) {
			$script_handles = array_merge(
				$script_handles,
				is_admin() ? $payment_method->get_payment_method_script_handles_for_admin() : $payment_method->get_payment_method_script_handles()
			);
		}

		return array_unique( array_filter( $script_handles ) );
	}

	/**
	 * Gets an array of all registered payment method script data, but only for active payment methods.
	 *
	 * @return array
	 */
	public function get_all_registered_script_data() {
		$script_data     = [];
		$payment_methods = $this->get_all_active_registered();

		foreach ( $payment_methods as $payment_method ) {
			$script_data[ $payment_method->get_name() . '_data' ] = $payment_method->get_payment_method_data();
		}

		return array_filter( $script_data );
	}
}
