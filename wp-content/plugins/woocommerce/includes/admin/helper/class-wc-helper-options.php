<?php
/**
 * WooCommerce Admin Helper Options
 *
 * @package WooCommerce\Admin\Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Helper_Options Class
 *
 * An interface to the woocommerce_helper_data entry in the wp_options table.
 */
class WC_Helper_Options {
	/**
	 * The option name used to store the helper data.
	 *
	 * @var string
	 */
	private static $option_name = 'woocommerce_helper_data';

	/**
	 * Update an option by key
	 *
	 * All helper options are grouped in a single options entry. This method
	 * is not thread-safe, use with caution.
	 *
	 * @param string $key The key to update.
	 * @param mixed  $value The new option value.
	 *
	 * @return bool True if the option has been updated.
	 */
	public static function update( $key, $value ) {
		$options         = get_option( self::$option_name, array() );
		$options[ $key ] = $value;
		return update_option( self::$option_name, $options, true );
	}

	/**
	 * Get an option by key
	 *
	 * @see self::update
	 *
	 * @param string $key The key to fetch.
	 * @param mixed  $default The default option to return if the key does not exist.
	 *
	 * @return mixed An option or the default.
	 */
	public static function get( $key, $default = false ) {
		$options = get_option( self::$option_name, array() );
		if ( is_array( $options ) && array_key_exists( $key, $options ) ) {
			return $options[ $key ];
		}

		return $default;
	}
}
