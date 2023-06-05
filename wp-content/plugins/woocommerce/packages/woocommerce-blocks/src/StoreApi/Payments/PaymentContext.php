<?php
namespace Automattic\WooCommerce\StoreApi\Payments;

/**
 * PaymentContext class.
 */
class PaymentContext {
	/**
	 * Payment method ID.
	 *
	 * @var string
	 */
	protected $payment_method = '';

	/**
	 * Order object for the order being paid.
	 *
	 * @var \WC_Order
	 */
	protected $order;

	/**
	 * Holds data to send to the payment gateway to support payment.
	 *
	 * @var array Key value pairs.
	 */
	protected $payment_data = [];

	/**
	 * Magic getter for protected properties.
	 *
	 * @param string $name Property name.
	 */
	public function __get( $name ) {
		if ( in_array( $name, [ 'payment_method', 'order', 'payment_data' ], true ) ) {
			return $this->$name;
		}
		return null;
	}

	/**
	 * Set the chosen payment method ID context.
	 *
	 * @param string $payment_method Payment method ID.
	 */
	public function set_payment_method( $payment_method ) {
		$this->payment_method = (string) $payment_method;
	}

	/**
	 * Retrieve the payment method instance for the current set payment method.
	 *
	 * @return {\WC_Payment_Gateway|null} An instance of the payment gateway if it exists.
	 */
	public function get_payment_method_instance() {
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
		if ( ! isset( $available_gateways[ $this->payment_method ] ) ) {
			return;
		}
		return $available_gateways[ $this->payment_method ];
	}

	/**
	 * Set the order context.
	 *
	 * @param \WC_Order $order Order object.
	 */
	public function set_order( \WC_Order $order ) {
		$this->order = $order;
	}

	/**
	 * Set payment data context.
	 *
	 * @param array $payment_data Array of key value pairs of data.
	 */
	public function set_payment_data( $payment_data = [] ) {
		$this->payment_data = [];

		foreach ( $payment_data as $key => $value ) {
			$this->payment_data[ (string) $key ] = (string) $value;
		}
	}
}
