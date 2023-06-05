<?php

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications\Transformers;

use Automattic\WooCommerce\Admin\RemoteInboxNotifications\TransformerInterface;
use InvalidArgumentException;
use stdClass;

/**
 * Searches a given a given value in the array.
 *
 * @package Automattic\WooCommerce\Admin\RemoteInboxNotifications\Transformers
 */
class ArraySearch implements TransformerInterface {
	/**
	 * Search a given value in the array.
	 *
	 * @param mixed         $value a value to transform.
	 * @param stdClass|null $arguments required argument 'value'.
	 * @param string|null   $default default value.
	 *
	 * @throws InvalidArgumentException Throws when the required 'value' is missing.
	 *
	 * @return mixed|null
	 */
	public function transform( $value, stdClass $arguments = null, $default = null ) {
		$key = array_search( $arguments->value, $value, true );
		if ( false !== $key ) {
			return $value[ $key ];
		}

		return null;
	}

	/**
	 * Validate Transformer arguments.
	 *
	 * @param stdClass|null $arguments arguments to validate.
	 *
	 * @return mixed
	 */
	public function validate( stdClass $arguments = null ) {
		if ( ! isset( $arguments->value ) ) {
			return false;
		}

		return true;
	}
}
