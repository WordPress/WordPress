<?php
/**
 * WooCommerce Payment Gateways
 *
 * Loads payment gateways via hooks for use in the store.
 *
 * @version 2.2.0
 * @package WooCommerce\Classes\Payment
 */

defined( 'ABSPATH' ) || exit;

/**
 * Payment gateways class.
 */
class WC_Payment_Gateways {

	/**
	 * Payment gateway classes.
	 *
	 * @var array
	 */
	public $payment_gateways = array();

	/**
	 * The single instance of the class.
	 *
	 * @var WC_Payment_Gateways
	 * @since 2.1.0
	 */
	protected static $_instance = null;

	/**
	 * Main WC_Payment_Gateways Instance.
	 *
	 * Ensures only one instance of WC_Payment_Gateways is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @return WC_Payment_Gateways Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'woocommerce' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce' ), '2.1' );
	}

	/**
	 * Initialize payment gateways.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Load gateways and hook in functions.
	 */
	public function init() {
		$load_gateways = array(
			'WC_Gateway_BACS',
			'WC_Gateway_Cheque',
			'WC_Gateway_COD',
		);

		if ( $this->should_load_paypal_standard() ) {
			$load_gateways[] = 'WC_Gateway_Paypal';
		}

		// Filter.
		$load_gateways = apply_filters( 'woocommerce_payment_gateways', $load_gateways );

		// Get sort order option.
		$ordering  = (array) get_option( 'woocommerce_gateway_order' );
		$order_end = 999;

		// Load gateways in order.
		foreach ( $load_gateways as $gateway ) {
			if ( is_string( $gateway ) && class_exists( $gateway ) ) {
				$gateway = new $gateway();
			}

			// Gateways need to be valid and extend WC_Payment_Gateway.
			if ( ! is_a( $gateway, 'WC_Payment_Gateway' ) ) {
				continue;
			}

			if ( isset( $ordering[ $gateway->id ] ) && is_numeric( $ordering[ $gateway->id ] ) ) {
				// Add in position.
				$this->payment_gateways[ $ordering[ $gateway->id ] ] = $gateway;
			} else {
				// Add to end of the array.
				$this->payment_gateways[ $order_end ] = $gateway;
				$order_end++;
			}
		}

		ksort( $this->payment_gateways );
	}

	/**
	 * Get gateways.
	 *
	 * @return array
	 */
	public function payment_gateways() {
		$_available_gateways = array();

		if ( count( $this->payment_gateways ) > 0 ) {
			foreach ( $this->payment_gateways as $gateway ) {
				$_available_gateways[ $gateway->id ] = $gateway;
			}
		}

		return $_available_gateways;
	}

	/**
	 * Get array of registered gateway ids
	 *
	 * @since 2.6.0
	 * @return array of strings
	 */
	public function get_payment_gateway_ids() {
		return wp_list_pluck( $this->payment_gateways, 'id' );
	}

	/**
	 * Get available gateways.
	 *
	 * @return array
	 */
	public function get_available_payment_gateways() {
		$_available_gateways = array();

		foreach ( $this->payment_gateways as $gateway ) {
			if ( $gateway->is_available() ) {
				if ( ! is_add_payment_method_page() ) {
					$_available_gateways[ $gateway->id ] = $gateway;
				} elseif ( $gateway->supports( 'add_payment_method' ) || $gateway->supports( 'tokenization' ) ) {
					$_available_gateways[ $gateway->id ] = $gateway;
				}
			}
		}

		return array_filter( (array) apply_filters( 'woocommerce_available_payment_gateways', $_available_gateways ), array( $this, 'filter_valid_gateway_class' ) );
	}

	/**
	 * Callback for array filter. Returns true if gateway is of correct type.
	 *
	 * @since 3.6.0
	 * @param object $gateway Gateway to check.
	 * @return bool
	 */
	protected function filter_valid_gateway_class( $gateway ) {
		return $gateway && is_a( $gateway, 'WC_Payment_Gateway' );
	}

	/**
	 * Set the current, active gateway.
	 *
	 * @param array $gateways Available payment gateways.
	 */
	public function set_current_gateway( $gateways ) {
		// Be on the defensive.
		if ( ! is_array( $gateways ) || empty( $gateways ) ) {
			return;
		}

		$current_gateway = false;

		if ( WC()->session ) {
			$current = WC()->session->get( 'chosen_payment_method' );

			if ( $current && isset( $gateways[ $current ] ) ) {
				$current_gateway = $gateways[ $current ];
			}
		}

		if ( ! $current_gateway ) {
			$current_gateway = current( $gateways );
		}

		// Ensure we can make a call to set_current() without triggering an error.
		if ( $current_gateway && is_callable( array( $current_gateway, 'set_current' ) ) ) {
			$current_gateway->set_current();
		}
	}

	/**
	 * Save options in admin.
	 */
	public function process_admin_options() {
		$gateway_order = isset( $_POST['gateway_order'] ) ? wc_clean( wp_unslash( $_POST['gateway_order'] ) ) : ''; // WPCS: input var ok, CSRF ok.
		$order         = array();

		if ( is_array( $gateway_order ) && count( $gateway_order ) > 0 ) {
			$loop = 0;
			foreach ( $gateway_order as $gateway_id ) {
				$order[ esc_attr( $gateway_id ) ] = $loop;
				$loop++;
			}
		}

		update_option( 'woocommerce_gateway_order', $order );
	}

	/**
	 * Determines if PayPal Standard should be loaded.
	 *
	 * @since 5.5.0
	 * @return bool Whether PayPal Standard should be loaded or not.
	 */
	protected function should_load_paypal_standard() {
		$paypal = new WC_Gateway_Paypal();
		return $paypal->should_load();
	}
}
