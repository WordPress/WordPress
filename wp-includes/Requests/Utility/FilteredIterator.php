<?php
/**
 * Iterator for arrays requiring filtered values
 *
 * @package Requests
 * @subpackage Utilities
 */

/**
 * Iterator for arrays requiring filtered values
 *
 * @package Requests
 * @subpackage Utilities
 */
class Requests_Utility_FilteredIterator extends ArrayIterator {
	/**
	 * Callback to run as a filter
	 *
	 * @var callable
	 */
	protected $callback;

	/**
	 * Create a new iterator
	 *
	 * @param array $data
	 * @param callable $callback Callback to be called on each value
	 */
	public function __construct($data, $callback) {
		parent::__construct($data);

		$this->callback = $callback;
	}

	/**
	 * Get the current item's value after filtering
	 *
	 * @return string
	 */
	public function current() {
		$value = parent::current();
		$value = call_user_func($this->callback, $value);
		return $value;
	}

	/**
	 * @inheritdoc
	 */
	public function unserialize( $serialized ) {
	}

	/**
	 * @inheritdoc
	 */
	public function __unserialize( $serialized ) { // phpcs:ignore PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.MethodDoubleUnderscore,PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__unserializeFound
	}

	public function __wakeup() { // phpcs:ignore PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.MethodDoubleUnderscore,PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__wakeupFound
		unset( $this->callback );
	}
}
