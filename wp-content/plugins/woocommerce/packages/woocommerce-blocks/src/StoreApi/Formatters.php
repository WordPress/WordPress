<?php
namespace Automattic\WooCommerce\StoreApi;

use \Exception;
use Automattic\WooCommerce\StoreApi\Formatters\DefaultFormatter;

/**
 * Formatters class.
 *
 * Allows formatter classes to be registered. Formatters are exposed to extensions via the ExtendSchema class.
 */
class Formatters {
	/**
	 * Holds an array of formatter class instances.
	 *
	 * @var array
	 */
	private $formatters = [];

	/**
	 * Get a new instance of a formatter class.
	 *
	 * @throws Exception An Exception is thrown if a non-existing formatter is used and the user is admin.
	 *
	 * @param string $name Name of the formatter.
	 * @return FormatterInterface Formatter class instance.
	 */
	public function __get( $name ) {
		if ( ! isset( $this->formatters[ $name ] ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG && current_user_can( 'manage_woocommerce' ) ) {
				throw new Exception( $name . ' formatter does not exist' );
			}
			return new DefaultFormatter();
		}
		return $this->formatters[ $name ];
	}

	/**
	 * Register a formatter class for usage.
	 *
	 * @param string $name Name of the formatter.
	 * @param string $class A formatter class name.
	 */
	public function register( $name, $class ) {
		$this->formatters[ $name ] = new $class();
	}
}
