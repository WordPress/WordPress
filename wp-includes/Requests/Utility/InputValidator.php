<?php
/**
 * Input validation utilities.
 *
 * @package Requests\Utilities
 */

namespace WpOrg\Requests\Utility;

use ArrayAccess;
use CurlHandle;
use Traversable;

/**
 * Input validation utilities.
 *
 * @package Requests\Utilities
 */
final class InputValidator {

	/**
	 * Verify that a received input parameter is of type string or is "stringable".
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return bool
	 */
	public static function is_string_or_stringable($input) {
		return is_string($input) || self::is_stringable_object($input);
	}

	/**
	 * Verify whether a received input parameter is usable as an integer array key.
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return bool
	 */
	public static function is_numeric_array_key($input) {
		if (is_int($input)) {
			return true;
		}

		if (!is_string($input)) {
			return false;
		}

		return (bool) preg_match('`^-?[0-9]+$`', $input);
	}

	/**
	 * Verify whether a received input parameter is "stringable".
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return bool
	 */
	public static function is_stringable_object($input) {
		return is_object($input) && method_exists($input, '__toString');
	}

	/**
	 * Verify whether a received input parameter is _accessible as if it were an array_.
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return bool
	 */
	public static function has_array_access($input) {
		return is_array($input) || $input instanceof ArrayAccess;
	}

	/**
	 * Verify whether a received input parameter is "iterable".
	 *
	 * @internal The PHP native `is_iterable()` function was only introduced in PHP 7.1
	 * and this library still supports PHP 5.6.
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return bool
	 */
	public static function is_iterable($input) {
		return is_array($input) || $input instanceof Traversable;
	}

	/**
	 * Verify whether a received input parameter is a Curl handle.
	 *
	 * The PHP Curl extension worked with resources prior to PHP 8.0 and with
	 * an instance of the `CurlHandle` class since PHP 8.0.
	 * {@link https://www.php.net/manual/en/migration80.incompatible.php#migration80.incompatible.resource2object}
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return bool
	 */
	public static function is_curl_handle($input) {
		if (is_resource($input)) {
			return get_resource_type($input) === 'curl';
		}

		if (is_object($input)) {
			return $input instanceof CurlHandle;
		}

		return false;
	}
}
