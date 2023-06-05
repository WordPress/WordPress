<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legacy Payment Tokens.
 * Payment Tokens were introduced in 2.6.0 with create and update as methods.
 * Major CRUD changes occurred in 3.0, so these were deprecated (save and delete still work).
 * This legacy class is for backwards compatibility in case any code called ->read, ->update or ->create
 * directly on the object.
 *
 * @version  3.0.0
 * @package  WooCommerce\Classes
 * @category Class
 * @author   WooCommerce
 */
abstract class WC_Legacy_Payment_Token extends WC_Data {

	/**
	 * Sets the type of this payment token (CC, eCheck, or something else).
	 *
	 * @param string Payment Token Type (CC, eCheck)
	 */
	public function set_type( $type ) {
		wc_deprecated_function( 'WC_Payment_Token::set_type', '3.0.0', 'Type cannot be overwritten.' );
	}

	/**
	 * Read a token by ID.
	 * @deprecated 3.0.0 - Init a token class with an ID.
	 *
	 * @param int $token_id
	 */
	public function read( $token_id ) {
		wc_deprecated_function( 'WC_Payment_Token::read', '3.0.0', 'a new token class initialized with an ID.' );
		$this->set_id( $token_id );
		$data_store = WC_Data_Store::load( 'payment-token' );
		$data_store->read( $this );
	}

	/**
	 * Update a token.
	 * @deprecated 3.0.0 - Use ::save instead.
	 */
	public function update() {
		wc_deprecated_function( 'WC_Payment_Token::update', '3.0.0', 'WC_Payment_Token::save instead.' );
		$data_store = WC_Data_Store::load( 'payment-token' );
		try {
			$data_store->update( $this );
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Create a token.
	 * @deprecated 3.0.0 - Use ::save instead.
	 */
	public function create() {
		wc_deprecated_function( 'WC_Payment_Token::create', '3.0.0', 'WC_Payment_Token::save instead.' );
		$data_store = WC_Data_Store::load( 'payment-token' );
		try {
			$data_store->create( $this );
		} catch ( Exception $e ) {
			return false;
		}
	}

}
