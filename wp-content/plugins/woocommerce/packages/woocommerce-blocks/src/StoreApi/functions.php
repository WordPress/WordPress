<?php
/**
 * Helper functions for interacting with the Store API.
 *
 * This file is autoloaded via composer.json.
 */

use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;

if ( ! function_exists( 'woocommerce_store_api_register_endpoint_data' ) ) {

	/**
	 * Register endpoint data under a specified namespace.
	 *
	 * @see Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema::register_endpoint_data()
	 *
	 * @param array $args Args to pass to register_endpoint_data.
	 * @returns boolean|\WP_Error True on success, WP_Error on fail.
	 */
	function woocommerce_store_api_register_endpoint_data( $args ) {
		try {
			$extend = StoreApi::container()->get( ExtendSchema::class );
			$extend->register_endpoint_data( $args );
		} catch ( \Exception $error ) {
			return new \WP_Error( 'error', $error->getMessage() );
		}
		return true;
	}
}

if ( ! function_exists( 'woocommerce_store_api_register_update_callback' ) ) {

	/**
	 * Add callback functions that can be executed by the cart/extensions endpoint.
	 *
	 * @see Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema::register_update_callback()
	 *
	 * @param array $args Args to pass to register_update_callback.
	 * @returns boolean|\WP_Error True on success, WP_Error on fail.
	 */
	function woocommerce_store_api_register_update_callback( $args ) {
		try {
			$extend = StoreApi::container()->get( ExtendSchema::class );
			$extend->register_update_callback( $args );
		} catch ( \Exception $error ) {
			return new \WP_Error( 'error', $error->getMessage() );
		}
		return true;
	}
}

if ( ! function_exists( 'woocommerce_store_api_register_payment_requirements' ) ) {

	/**
	 * Registers and validates payment requirements callbacks.
	 *
	 * @see Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema::register_payment_requirements()
	 *
	 * @param array $args Args to pass to register_payment_requirements.
	 * @returns boolean|\WP_Error True on success, WP_Error on fail.
	 */
	function woocommerce_store_api_register_payment_requirements( $args ) {
		try {
			$extend = StoreApi::container()->get( ExtendSchema::class );
			$extend->register_payment_requirements( $args );
		} catch ( \Exception $error ) {
			return new \WP_Error( 'error', $error->getMessage() );
		}
		return true;
	}
}

if ( ! function_exists( 'woocommerce_store_api_get_formatter' ) ) {

	/**
	 * Returns a formatter instance.
	 *
	 * @see Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema::get_formatter()
	 *
	 * @param string $name Formatter name.
	 * @return Automattic\WooCommerce\StoreApi\Formatters\FormatterInterface
	 */
	function woocommerce_store_api_get_formatter( $name ) {
		return StoreApi::container()->get( ExtendSchema::class )->get_formatter( $name );
	}
}
