<?php
/**
 * Case-insensitive dictionary, suitable for HTTP headers
 *
 * @package Requests
 */

namespace WpOrg\Requests\Response;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;
use WpOrg\Requests\Utility\FilteredIterator;

/**
 * Case-insensitive dictionary, suitable for HTTP headers
 *
 * @package Requests
 */
class Headers extends CaseInsensitiveDictionary {
	/**
	 * Get the given header
	 *
	 * Unlike {@see \WpOrg\Requests\Response\Headers::getValues()}, this returns a string. If there are
	 * multiple values, it concatenates them with a comma as per RFC2616.
	 *
	 * Avoid using this where commas may be used unquoted in values, such as
	 * Set-Cookie headers.
	 *
	 * @param string $offset
	 * @return string|null Header value
	 */
	public function offsetGet($offset) {
		if (is_string($offset)) {
			$offset = strtolower($offset);
		}

		if (!isset($this->data[$offset])) {
			return null;
		}

		return $this->flatten($this->data[$offset]);
	}

	/**
	 * Set the given item
	 *
	 * @param string $offset Item name
	 * @param string $value Item value
	 *
	 * @throws \WpOrg\Requests\Exception On attempting to use dictionary as list (`invalidset`)
	 */
	public function offsetSet($offset, $value) {
		if ($offset === null) {
			throw new Exception('Object is a dictionary, not a list', 'invalidset');
		}

		if (is_string($offset)) {
			$offset = strtolower($offset);
		}

		if (!isset($this->data[$offset])) {
			$this->data[$offset] = [];
		}

		$this->data[$offset][] = $value;
	}

	/**
	 * Get all values for a given header
	 *
	 * @param string $offset
	 * @return array|null Header values
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed argument is not valid as an array key.
	 */
	public function getValues($offset) {
		if (!is_string($offset) && !is_int($offset)) {
			throw InvalidArgument::create(1, '$offset', 'string|int', gettype($offset));
		}

		$offset = strtolower($offset);
		if (!isset($this->data[$offset])) {
			return null;
		}

		return $this->data[$offset];
	}

	/**
	 * Flattens a value into a string
	 *
	 * Converts an array into a string by imploding values with a comma, as per
	 * RFC2616's rules for folding headers.
	 *
	 * @param string|array $value Value to flatten
	 * @return string Flattened value
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed argument is not a string or an array.
	 */
	public function flatten($value) {
		if (is_string($value)) {
			return $value;
		}

		if (is_array($value)) {
			return implode(',', $value);
		}

		throw InvalidArgument::create(1, '$value', 'string|array', gettype($value));
	}

	/**
	 * Get an iterator for the data
	 *
	 * Converts the internally stored values to a comma-separated string if there is more
	 * than one value for a key.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new FilteredIterator($this->data, [$this, 'flatten']);
	}
}
