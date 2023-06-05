<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legacy Shipping Zone.
 *
 * @version  3.0.0
 * @package  WooCommerce\Classes
 * @category Class
 * @author   WooThemes
 */
abstract class WC_Legacy_Shipping_Zone extends WC_Data {

	/**
	 * Get zone ID
	 * @return int|null Null if the zone does not exist. 0 is the default zone.
	 * @deprecated 3.0
	 */
	public function get_zone_id() {
		wc_deprecated_function( 'WC_Shipping_Zone::get_zone_id', '3.0', 'WC_Shipping_Zone::get_id' );
		return $this->get_id();
	}

	/**
	 * Read a shipping zone by ID.
	 * @deprecated 3.0.0 - Init a shipping zone with an ID.
	 *
	 * @param int $zone_id
	 */
	public function read( $zone_id ) {
		wc_deprecated_function( 'WC_Shipping_Zone::read', '3.0', 'a shipping zone initialized with an ID.' );
		$this->set_id( $zone_id );
		$data_store = WC_Data_Store::load( 'shipping-zone' );
		$data_store->read( $this );
	}

	/**
	 * Update a zone.
	 * @deprecated 3.0.0 - Use ::save instead.
	 */
	public function update() {
		wc_deprecated_function( 'WC_Shipping_Zone::update', '3.0', 'WC_Shipping_Zone::save instead.' );
		$data_store = WC_Data_Store::load( 'shipping-zone' );
		try {
			$data_store->update( $this );
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Create a zone.
	 * @deprecated 3.0.0 - Use ::save instead.
	 */
	public function create() {
		wc_deprecated_function( 'WC_Shipping_Zone::create', '3.0', 'WC_Shipping_Zone::save instead.' );
		$data_store = WC_Data_Store::load( 'shipping-zone' );
		try {
			$data_store->create( $this );
		} catch ( Exception $e ) {
			return false;
		}
	}


}
