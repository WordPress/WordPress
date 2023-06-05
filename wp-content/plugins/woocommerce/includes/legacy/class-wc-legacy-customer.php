<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legacy Customer.
 *
 * @version  3.0.0
 * @package  WooCommerce\Classes
 * @category Class
 * @author   WooThemes
 */
abstract class WC_Legacy_Customer extends WC_Data {

	/**
	 * __isset legacy.
	 * @param mixed $key
	 * @return bool
	 */
	public function __isset( $key ) {
		$legacy_keys = array(
			'id',
			'country',
			'state',
			'postcode',
			'city',
			'address_1',
			'address',
			'address_2',
			'shipping_country',
			'shipping_state',
			'shipping_postcode',
			'shipping_city',
			'shipping_address_1',
			'shipping_address',
			'shipping_address_2',
			'is_vat_exempt',
			'calculated_shipping',
		);
		$key = $this->filter_legacy_key( $key );
		return in_array( $key, $legacy_keys );
	}

	/**
	 * __get function.
	 * @param string $key
	 * @return string
	 */
	public function __get( $key ) {
		wc_doing_it_wrong( $key, 'Customer properties should not be accessed directly.', '3.0' );
		$key = $this->filter_legacy_key( $key );
		if ( in_array( $key, array( 'country', 'state', 'postcode', 'city', 'address_1', 'address', 'address_2' ) ) ) {
			$key = 'billing_' . $key;
		}
		return is_callable( array( $this, "get_{$key}" ) ) ? $this->{"get_{$key}"}() : '';
	}

	/**
	 * __set function.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set( $key, $value ) {
		wc_doing_it_wrong( $key, 'Customer properties should not be set directly.', '3.0' );
		$key = $this->filter_legacy_key( $key );

		if ( is_callable( array( $this, "set_{$key}" ) ) ) {
			$this->{"set_{$key}"}( $value );
		}
	}

	/**
	 * Address and shipping_address are aliased, so we want to get the 'real' key name.
	 * For all other keys, we can just return it.
	 * @since 3.0.0
	 * @param  string $key
	 * @return string
	 */
	private function filter_legacy_key( $key ) {
		if ( 'address' === $key ) {
			$key = 'address_1';
		}
		if ( 'shipping_address' === $key ) {
			$key = 'shipping_address_1';
		}

		return $key;
	}

	/**
	 * Sets session data for the location.
	 *
	 * @param string $country
	 * @param string $state
	 * @param string $postcode (default: '')
	 * @param string $city (default: '')
	 */
	public function set_location( $country, $state, $postcode = '', $city = '' ) {
		$this->set_billing_location( $country, $state, $postcode, $city );
		$this->set_shipping_location( $country, $state, $postcode, $city );
	}

	/**
	 * Get default country for a customer.
	 * @return string
	 */
	public function get_default_country() {
		wc_deprecated_function( 'WC_Customer::get_default_country', '3.0', 'wc_get_customer_default_location' );
		$default = wc_get_customer_default_location();
		return $default['country'];
	}

	/**
	 * Get default state for a customer.
	 * @return string
	 */
	public function get_default_state() {
		wc_deprecated_function( 'WC_Customer::get_default_state', '3.0', 'wc_get_customer_default_location' );
		$default = wc_get_customer_default_location();
		return $default['state'];
	}

	/**
	 * Set customer address to match shop base address.
	 */
	public function set_to_base() {
		wc_deprecated_function( 'WC_Customer::set_to_base', '3.0', 'WC_Customer::set_billing_address_to_base' );
		$this->set_billing_address_to_base();
	}

	/**
	 * Set customer shipping address to base address.
	 */
	public function set_shipping_to_base() {
		wc_deprecated_function( 'WC_Customer::set_shipping_to_base', '3.0', 'WC_Customer::set_shipping_address_to_base' );
		$this->set_shipping_address_to_base();
	}

	/**
	 * Calculated shipping.
	 * @param boolean $calculated
	 */
	public function calculated_shipping( $calculated = true ) {
		wc_deprecated_function( 'WC_Customer::calculated_shipping', '3.0', 'WC_Customer::set_calculated_shipping' );
		$this->set_calculated_shipping( $calculated );
	}

	/**
	 * Set default data for a customer.
	 */
	public function set_default_data() {
		wc_deprecated_function( 'WC_Customer::set_default_data', '3.0' );
	}

	/**
	 * Save data function.
	 */
	public function save_data() {
		$this->save();
	}

	/**
	 * Is the user a paying customer?
	 *
	 * @param int $user_id
	 *
	 * @return bool
	 */
	function is_paying_customer( $user_id = '' ) {
		wc_deprecated_function( 'WC_Customer::is_paying_customer', '3.0', 'WC_Customer::get_is_paying_customer' );
		if ( ! empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		return '1' === get_user_meta( $user_id, 'paying_customer', true );
	}

	/**
	 * Legacy get address.
	 */
	function get_address() {
		wc_deprecated_function( 'WC_Customer::get_address', '3.0', 'WC_Customer::get_billing_address_1' );
		return $this->get_billing_address_1();
	}

	/**
	 * Legacy get address 2.
	 */
	function get_address_2() {
		wc_deprecated_function( 'WC_Customer::get_address_2', '3.0', 'WC_Customer::get_billing_address_2' );
		return $this->get_billing_address_2();
	}

	/**
	 * Legacy get country.
	 */
	function get_country() {
		wc_deprecated_function( 'WC_Customer::get_country', '3.0', 'WC_Customer::get_billing_country' );
		return $this->get_billing_country();
	}

	/**
	 * Legacy get state.
	 */
	function get_state() {
		wc_deprecated_function( 'WC_Customer::get_state', '3.0', 'WC_Customer::get_billing_state' );
		return $this->get_billing_state();
	}

	/**
	 * Legacy get postcode.
	 */
	function get_postcode() {
		wc_deprecated_function( 'WC_Customer::get_postcode', '3.0', 'WC_Customer::get_billing_postcode' );
		return $this->get_billing_postcode();
	}

	/**
	 * Legacy get city.
	 */
	function get_city() {
		wc_deprecated_function( 'WC_Customer::get_city', '3.0', 'WC_Customer::get_billing_city' );
		return $this->get_billing_city();
	}

	/**
	 * Legacy set country.
	 *
	 * @param string $country
	 */
	function set_country( $country ) {
		wc_deprecated_function( 'WC_Customer::set_country', '3.0', 'WC_Customer::set_billing_country' );
		$this->set_billing_country( $country );
	}

	/**
	 * Legacy set state.
	 *
	 * @param string $state
	 */
	function set_state( $state ) {
		wc_deprecated_function( 'WC_Customer::set_state', '3.0', 'WC_Customer::set_billing_state' );
		$this->set_billing_state( $state );
	}

	/**
	 * Legacy set postcode.
	 *
	 * @param string $postcode
	 */
	function set_postcode( $postcode ) {
		wc_deprecated_function( 'WC_Customer::set_postcode', '3.0', 'WC_Customer::set_billing_postcode' );
		$this->set_billing_postcode( $postcode );
	}

	/**
	 * Legacy set city.
	 *
	 * @param string $city
	 */
	function set_city( $city ) {
		wc_deprecated_function( 'WC_Customer::set_city', '3.0', 'WC_Customer::set_billing_city' );
		$this->set_billing_city( $city );
	}

	/**
	 * Legacy set address.
	 *
	 * @param string $address
	 */
	function set_address( $address ) {
		wc_deprecated_function( 'WC_Customer::set_address', '3.0', 'WC_Customer::set_billing_address' );
		$this->set_billing_address( $address );
	}

	/**
	 * Legacy set address.
	 *
	 * @param string $address
	 */
	function set_address_2( $address ) {
		wc_deprecated_function( 'WC_Customer::set_address_2', '3.0', 'WC_Customer::set_billing_address_2' );
		$this->set_billing_address_2( $address );
	}
}
