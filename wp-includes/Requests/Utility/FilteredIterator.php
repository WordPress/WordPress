<?php
/**
 * Iterator for arrays requiring filtered values
 *
 * @package Requests\Utilities
 */

namespace WpOrg\Requests\Utility;

use ArrayIterator;
use ReturnTypeWillChange;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Utility\InputValidator;

/**
 * Iterator for arrays requiring filtered values
 *
 * @package Requests\Utilities
 */
final class FilteredIterator extends ArrayIterator {
	/**
	 * Callback to run as a filter
	 *
	 * @var callable
	 */
	private $callback;

	/**
	 * Create a new iterator
	 *
	 * @param array $data
	 * @param callable $callback Callback to be called on each value
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $data argument is not iterable.
	 */
	public function __construct($data, $callback) {
		if (InputValidator::is_iterable($data) === false) {
			throw InvalidArgument::create(1, '$data', 'iterable', gettype($data));
		}

		parent::__construct($data);

		if (is_callable($callback)) {
			$this->callback = $callback;
		}
	}

	/**
	 * @inheritdoc
	 *
	 * @phpcs:disable PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__unserializeFound
	 */
	#[ReturnTypeWillChange]
	public function __unserialize($data) {}
	// phpcs:enable

	public function __wakeup() {
		unset($this->callback);
	}

	/**
	 * Get the current item's value after filtering
	 *
	 * @return string
	 */
	#[ReturnTypeWillChange]
	public function current() {
		$value = parent::current();

		if (is_callable($this->callback)) {
			$value = call_user_func($this->callback, $value);
		}

		return $value;
	}

	/**
	 * @inheritdoc
	 */
	#[ReturnTypeWillChange]
	public function unserialize($data) {}
}
