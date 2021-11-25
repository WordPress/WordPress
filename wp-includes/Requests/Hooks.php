<?php
/**
 * Handles adding and dispatching events
 *
 * @package Requests\EventDispatcher
 */

namespace WpOrg\Requests;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\HookManager;
use WpOrg\Requests\Utility\InputValidator;

/**
 * Handles adding and dispatching events
 *
 * @package Requests\EventDispatcher
 */
class Hooks implements HookManager {
	/**
	 * Registered callbacks for each hook
	 *
	 * @var array
	 */
	protected $hooks = [];

	/**
	 * Register a callback for a hook
	 *
	 * @param string $hook Hook name
	 * @param callable $callback Function/method to call on event
	 * @param int $priority Priority number. <0 is executed earlier, >0 is executed later
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $hook argument is not a string.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $callback argument is not callable.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $priority argument is not an integer.
	 */
	public function register($hook, $callback, $priority = 0) {
		if (is_string($hook) === false) {
			throw InvalidArgument::create(1, '$hook', 'string', gettype($hook));
		}

		if (is_callable($callback) === false) {
			throw InvalidArgument::create(2, '$callback', 'callable', gettype($callback));
		}

		if (InputValidator::is_numeric_array_key($priority) === false) {
			throw InvalidArgument::create(3, '$priority', 'integer', gettype($priority));
		}

		if (!isset($this->hooks[$hook])) {
			$this->hooks[$hook] = [
				$priority => [],
			];
		} elseif (!isset($this->hooks[$hook][$priority])) {
			$this->hooks[$hook][$priority] = [];
		}

		$this->hooks[$hook][$priority][] = $callback;
	}

	/**
	 * Dispatch a message
	 *
	 * @param string $hook Hook name
	 * @param array $parameters Parameters to pass to callbacks
	 * @return boolean Successfulness
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $hook argument is not a string.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $parameters argument is not an array.
	 */
	public function dispatch($hook, $parameters = []) {
		if (is_string($hook) === false) {
			throw InvalidArgument::create(1, '$hook', 'string', gettype($hook));
		}

		// Check strictly against array, as Array* objects don't work in combination with `call_user_func_array()`.
		if (is_array($parameters) === false) {
			throw InvalidArgument::create(2, '$parameters', 'array', gettype($parameters));
		}

		if (empty($this->hooks[$hook])) {
			return false;
		}

		if (!empty($parameters)) {
			// Strip potential keys from the array to prevent them being interpreted as parameter names in PHP 8.0.
			$parameters = array_values($parameters);
		}

		foreach ($this->hooks[$hook] as $priority => $hooked) {
			foreach ($hooked as $callback) {
				$callback(...$parameters);
			}
		}

		return true;
	}
}
