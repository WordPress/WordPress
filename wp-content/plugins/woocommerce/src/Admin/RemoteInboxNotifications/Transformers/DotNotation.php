<?php

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications\Transformers;

use Automattic\WooCommerce\Admin\RemoteInboxNotifications\TransformerInterface;
use InvalidArgumentException;
use stdClass;

/**
 * Find an array value by dot notation.
 *
 * @package Automattic\WooCommerce\Admin\RemoteInboxNotifications\Transformers
 */
class DotNotation implements TransformerInterface {

	/**
	 * Find given path from the given value.
	 *
	 * @param mixed         $value a value to transform.
	 * @param stdClass|null $arguments required argument 'path'.
	 * @param string|null   $default default value.
	 *
	 * @throws InvalidArgumentException Throws when the required 'path' is missing.
	 *
	 * @return mixed
	 */
	public function transform( $value, stdclass $arguments = null, $default = null ) {
		if ( is_object( $value ) ) {
			// if the value is an object, convert it to an array.
			$value = json_decode( wp_json_encode( $value ), true );
		}

		return $this->get( $value, $arguments->path, $default );
	}

	/**
	 * Find the given $path in $array by dot notation.
	 *
	 * @param array  $array an array to search in.
	 * @param string $path a path in the given array.
	 * @param null   $default default value to return if $path was not found.
	 *
	 * @return mixed|null
	 */
	public function get( $array, $path, $default = null ) {
		if ( isset( $array[ $path ] ) ) {
			return $array[ $path ];
		}

		foreach ( explode( '.', $path ) as $segment ) {
			if ( ! is_array( $array ) || ! array_key_exists( $segment, $array ) ) {
				return $default;
			}

			$array = $array[ $segment ];
		}

		return $array;
	}

	/**
	 * Validate Transformer arguments.
	 *
	 * @param stdClass|null $arguments arguments to validate.
	 *
	 * @return mixed
	 */
	public function validate( stdClass $arguments = null ) {
		if ( ! isset( $arguments->path ) ) {
			return false;
		}

		return true;
	}
}
