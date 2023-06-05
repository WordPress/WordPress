<?php
namespace Automattic\WooCommerce\Blocks\Payments\Integrations;

use Automattic\WooCommerce\Blocks\Assets\Api;

/**
 * Cash on Delivery (COD) payment method integration
 *
 * @since 3.0.0
 */
final class CashOnDelivery extends AbstractPaymentMethodType {
	/**
	 * Payment method name/id/slug (matches id in WC_Gateway_COD in core).
	 *
	 * @var string
	 */
	protected $name = 'cod';

	/**
	 * An instance of the Asset Api
	 *
	 * @var Api
	 */
	private $asset_api;

	/**
	 * Constructor
	 *
	 * @param Api $asset_api An instance of Api.
	 */
	public function __construct( Api $asset_api ) {
		$this->asset_api = $asset_api;
	}

	/**
	 * Initializes the payment method type.
	 */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_cod_settings', [] );
	}

	/**
	 * Returns if this payment method should be active. If false, the scripts will not be enqueued.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return filter_var( $this->get_setting( 'enabled', false ), FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Return enable_for_virtual option.
	 *
	 * @return boolean True if store allows COD payment for orders containing only virtual products.
	 */
	private function get_enable_for_virtual() {
		return filter_var( $this->get_setting( 'enable_for_virtual', false ), FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Return enable_for_methods option.
	 *
	 * @return array Array of shipping methods (string ids) that allow COD. (If empty, all support COD.)
	 */
	private function get_enable_for_methods() {
		$enable_for_methods = $this->get_setting( 'enable_for_methods', [] );
		if ( '' === $enable_for_methods ) {
			return [];
		}
		return $enable_for_methods;
	}


	/**
	 * Returns an array of scripts/handles to be registered for this payment method.
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles() {
		$this->asset_api->register_script(
			'wc-payment-method-cod',
			'build/wc-payment-method-cod.js'
		);
		return [ 'wc-payment-method-cod' ];
	}

	/**
	 * Returns an array of key=>value pairs of data made available to the payment methods script.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {
		return [
			'title'                    => $this->get_setting( 'title' ),
			'description'              => $this->get_setting( 'description' ),
			'enableForVirtual'         => $this->get_enable_for_virtual(),
			'enableForShippingMethods' => $this->get_enable_for_methods(),
			'supports'                 => $this->get_supported_features(),
		];
	}
}
