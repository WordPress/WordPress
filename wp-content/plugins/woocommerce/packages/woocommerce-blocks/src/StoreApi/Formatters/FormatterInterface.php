<?php
namespace Automattic\WooCommerce\StoreApi\Formatters;

/**
 * FormatterInterface.
 */
interface FormatterInterface {
	/**
	 * Format a given value and return the result.
	 *
	 * @param mixed $value Value to format.
	 * @param array $options Options that influence the formatting.
	 * @return mixed
	 */
	public function format( $value, array $options = [] );
}
