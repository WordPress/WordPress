<?php

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications\Transformers;

use Automattic\WooCommerce\Admin\RemoteInboxNotifications\TransformerInterface;
use InvalidArgumentException;
use stdClass;

/**
 * Search array value by one of its key.
 *
 * @package Automattic\WooCommerce\Admin\RemoteInboxNotifications\Transformers
 */
class ArrayColumn implements TransformerInterface {
	/**
	 * Search array value by one of its key.
	 *
	 * @param mixed         $value a value to transform.
	 * @param stdClass|null $arguments required arguments 'key'.
	 * @param string|null   $default default value.
	 *
	 * @throws InvalidArgumentException Throws when the required argument 'key' is missing.
	 *
	 * @return mixed
	 */
	public function transform( $value, stdClass $arguments = null, $default = null ) {
		return array_column( $value, $arguments->key );
	}

	/**
	 * Validate Transformer arguments.
	 *
	 * @param stdClass|null $arguments arguments to validate.
	 *
	 * @return mixed
	 */
	public function validate( stdClass $arguments = null ) {
		if ( ! isset( $arguments->key ) ) {
			return false;
		}

		return true;
	}
}
