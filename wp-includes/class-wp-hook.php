<?php
/**
 * Plugin API: WP_Hook class
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 4.7.0
 */

/**
 * Core class used to implement action and filter hook functionality.
 *
 * @since 4.7.0
 *
 * @see Iterator
 * @see ArrayAccess
 */
final class WP_Hook implements Iterator, ArrayAccess {

	/**
	 * Hook callbacks.
	 *
	 * @since 4.7.0
	 * @var array
	 */
	public $callbacks = array();

	/**
	 * The priority keys of actively running iterations of a hook.
	 *
	 * @since 4.7.0
	 * @var array
	 */
	private $iterations = array();

	/**
	 * The current priority of actively running iterations of a hook.
	 *
	 * @since 4.7.0
	 * @var array
	 */
	private $current_priority = array();

	/**
	 * Number of levels this hook can be recursively called.
	 *
	 * @since 4.7.0
	 * @var int
	 */
	private $nesting_level = 0;

	/**
	 * Flag for if we're currently doing an action, rather than a filter.
	 *
	 * @since 4.7.0
	 * @var bool
	 */
	private $doing_action = false;

	/**
	 * Adds a callback function to a filter hook.
	 *
	 * @since 4.7.0
	 *
	 * @param string   $hook_name     The name of the filter to add the callback to.
	 * @param callable $callback      The callback to be run when the filter is applied.
	 * @param int      $priority      The order in which the functions associated with a particular filter
	 *                                are executed. Lower numbers correspond with earlier execution,
	 *                                and functions with the same priority are executed in the order
	 *                                in which they were added to the filter.
	 * @param int      $accepted_args The number of arguments the function accepts.
	 */
	public function add_filter( $hook_name, $callback, $priority, $accepted_args ) {
		$idx = _wp_filter_build_unique_id( $hook_name, $callback, $priority );

		$priority_existed = isset( $this->callbacks[ $priority ] );

		$this->callbacks[ $priority ][ $idx ] = array(
			'function'      => $callback,
			'accepted_args' => $accepted_args,
		);

		// If we're adding a new priority to the list, put them back in sorted order.
		if ( ! $priority_existed && count( $this->callbacks ) > 1 ) {
			ksort( $this->callbacks, SORT_NUMERIC );
		}

		if ( $this->nesting_level > 0 ) {
			$this->resort_active_iterations( $priority, $priority_existed );
		}
	}

	/**
	 * Handles resetting callback priority keys mid-iteration.
	 *
	 * @since 4.7.0
	 *
	 * @param false|int $new_priority     Optional. The priority of the new filter being added. Default false,
	 *                                    for no priority being added.
	 * @param bool      $priority_existed Optional. Flag for whether the priority already existed before the new
	 *                                    filter was added. Default false.
	 */
	private function resort_active_iterations( $new_priority = false, $priority_existed = false ) {
		$new_priorities = array_keys( $this->callbacks );

		// If there are no remaining hooks, clear out all running iterations.
		if ( ! $new_priorities ) {
			foreach ( $this->iterations as $index => $iteration ) {
				$this->iterations[ $index ] = $new_priorities;
			}

			return;
		}

		$min = min( $new_priorities );

		foreach ( $this->iterations as $index => &$iteration ) {
			$current = current( $iteration );

			// If we're already at the end of this iteration, just leave the array pointer where it is.
			if ( false === $current ) {
				continue;
			}

			$iteration = $new_priorities;

			if ( $current < $min ) {
				array_unshift( $iteration, $current );
				continue;
			}

			while ( current( $iteration ) < $current ) {
				if ( false === next( $iteration ) ) {
					break;
				}
			}

			// If we have a new priority that didn't exist, but ::apply_filters() or ::do_action() thinks it's the current priority...
			if ( $new_priority === $this->current_priority[ $index ] && ! $priority_existed ) {
				/*
				 * ...and the new priority is the same as what $this->iterations thinks is the previous
				 * priority, we need to move back to it.
				 */

				if ( false === current( $iteration ) ) {
					// If we've already moved off the end of the array, go back to the last element.
					$prev = end( $iteration );
				} else {
					// Otherwise, just go back to the previous element.
					$prev = prev( $iteration );
				}

				if ( false === $prev ) {
					// Start of the array. Reset, and go about our day.
					reset( $iteration );
				} elseif ( $new_priority !== $prev ) {
					// Previous wasn't the same. Move forward again.
					next( $iteration );
				}
			}
		}

		unset( $iteration );
	}

	/**
	 * Removes a callback function from a filter hook.
	 *
	 * @since 4.7.0
	 *
	 * @param string   $hook_name The filter hook to which the function to be removed is hooked.
	 * @param callable $callback  The callback to be removed from running when the filter is applied.
	 * @param int      $priority  The exact priority used when adding the original filter callback.
	 * @return bool Whether the callback existed before it was removed.
	 */
	public function remove_filter( $hook_name, $callback, $priority ) {
		$function_key = _wp_filter_build_unique_id( $hook_name, $callback, $priority );

		$exists = isset( $this->callbacks[ $priority ][ $function_key ] );

		if ( $exists ) {
			unset( $this->callbacks[ $priority ][ $function_key ] );

			if ( ! $this->callbacks[ $priority ] ) {
				unset( $this->callbacks[ $priority ] );

				if ( $this->nesting_level > 0 ) {
					$this->resort_active_iterations();
				}
			}
		}

		return $exists;
	}

	/**
	 * Checks if a specific callback has been registered for this hook.
	 *
	 * When using the `$callback` argument, this function may return a non-boolean value
	 * that evaluates to false (e.g. 0), so use the `===` operator for testing the return value.
	 *
	 * @since 4.7.0
	 *
	 * @param string         $hook_name Optional. The name of the filter hook. Default empty.
	 * @param callable|false $callback  Optional. The callback to check for. Default false.
	 * @return bool|int If `$callback` is omitted, returns boolean for whether the hook has
	 *                  anything registered. When checking a specific function, the priority
	 *                  of that hook is returned, or false if the function is not attached.
	 */
	public function has_filter( $hook_name = '', $callback = false ) {
		if ( false === $callback ) {
			return $this->has_filters();
		}

		$function_key = _wp_filter_build_unique_id( $hook_name, $callback, false );

		if ( ! $function_key ) {
			return false;
		}

		foreach ( $this->callbacks as $priority => $callbacks ) {
			if ( isset( $callbacks[ $function_key ] ) ) {
				return $priority;
			}
		}

		return false;
	}

	/**
	 * Checks if any callbacks have been registered for this hook.
	 *
	 * @since 4.7.0
	 *
	 * @return bool True if callbacks have been registered for the current hook, otherwise false.
	 */
	public function has_filters() {
		foreach ( $this->callbacks as $callbacks ) {
			if ( $callbacks ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Removes all callbacks from the current filter.
	 *
	 * @since 4.7.0
	 *
	 * @param int|false $priority Optional. The priority number to remove. Default false.
	 */
	public function remove_all_filters( $priority = false ) {
		if ( ! $this->callbacks ) {
			return;
		}

		if ( false === $priority ) {
			$this->callbacks = array();
		} elseif ( isset( $this->callbacks[ $priority ] ) ) {
			unset( $this->callbacks[ $priority ] );
		}

		if ( $this->nesting_level > 0 ) {
			$this->resort_active_iterations();
		}
	}

	/**
	 * Calls the callback functions that have been added to a filter hook.
	 *
	 * @since 4.7.0
	 *
	 * @param mixed $value The value to filter.
	 * @param array $args  Additional parameters to pass to the callback functions.
	 *                     This array is expected to include $value at index 0.
	 * @return mixed The filtered value after all hooked functions are applied to it.
	 */
	public function apply_filters( $value, $args ) {
		if ( ! $this->callbacks ) {
			return $value;
		}

		$nesting_level = $this->nesting_level++;

		$this->iterations[ $nesting_level ] = array_keys( $this->callbacks );
		$num_args                           = count( $args );

		do {
			$this->current_priority[ $nesting_level ] = current( $this->iterations[ $nesting_level ] );
			$priority                                 = $this->current_priority[ $nesting_level ];

			foreach ( $this->callbacks[ $priority ] as $the_ ) {
				if ( ! $this->doing_action ) {
					$args[0] = $value;
				}

				// Avoid the array_slice() if possible.
				if ( 0 == $the_['accepted_args'] ) {
					$value = call_user_func( $the_['function'] );
				} elseif ( $the_['accepted_args'] >= $num_args ) {
					$value = call_user_func_array( $the_['function'], $args );
				} else {
					$value = call_user_func_array( $the_['function'], array_slice( $args, 0, (int) $the_['accepted_args'] ) );
				}
			}
		} while ( false !== next( $this->iterations[ $nesting_level ] ) );

		unset( $this->iterations[ $nesting_level ] );
		unset( $this->current_priority[ $nesting_level ] );

		$this->nesting_level--;

		return $value;
	}

	/**
	 * Calls the callback functions that have been added to an action hook.
	 *
	 * @since 4.7.0
	 *
	 * @param array $args Parameters to pass to the callback functions.
	 */
	public function do_action( $args ) {
		$this->doing_action = true;
		$this->apply_filters( '', $args );

		// If there are recursive calls to the current action, we haven't finished it until we get to the last one.
		if ( ! $this->nesting_level ) {
			$this->doing_action = false;
		}
	}

	/**
	 * Processes the functions hooked into the 'all' hook.
	 *
	 * @since 4.7.0
	 *
	 * @param array $args Arguments to pass to the hook callbacks. Passed by reference.
	 */
	public function do_all_hook( &$args ) {
		$nesting_level                      = $this->nesting_level++;
		$this->iterations[ $nesting_level ] = array_keys( $this->callbacks );

		do {
			$priority = current( $this->iterations[ $nesting_level ] );

			foreach ( $this->callbacks[ $priority ] as $the_ ) {
				call_user_func_array( $the_['function'], $args );
			}
		} while ( false !== next( $this->iterations[ $nesting_level ] ) );

		unset( $this->iterations[ $nesting_level ] );
		$this->nesting_level--;
	}

	/**
	 * Return the current priority level of the currently running iteration of the hook.
	 *
	 * @since 4.7.0
	 *
	 * @return int|false If the hook is running, return the current priority level.
	 *                   If it isn't running, return false.
	 */
	public function current_priority() {
		if ( false === current( $this->iterations ) ) {
			return false;
		}

		return current( current( $this->iterations ) );
	}

	/**
	 * Normalizes filters set up before WordPress has initialized to WP_Hook objects.
	 *
	 * The `$filters` parameter should be an array keyed by hook name, with values
	 * containing either:
	 *
	 *  - A `WP_Hook` instance
	 *  - An array of callbacks keyed by their priorities
	 *
	 * Examples:
	 *
	 *     $filters = array(
	 *         'wp_fatal_error_handler_enabled' => array(
	 *             10 => array(
	 *                 array(
	 *                     'accepted_args' => 0,
	 *                     'function'      => function() {
	 *                         return false;
	 *                     },
	 *                 ),
	 *             ),
	 *         ),
	 *     );
	 *
	 * @since 4.7.0
	 *
	 * @param array $filters Filters to normalize. See documentation above for details.
	 * @return WP_Hook[] Array of normalized filters.
	 */
	public static function build_preinitialized_hooks( $filters ) {
		/** @var WP_Hook[] $normalized */
		$normalized = array();

		foreach ( $filters as $hook_name => $callback_groups ) {
			if ( is_object( $callback_groups ) && $callback_groups instanceof WP_Hook ) {
				$normalized[ $hook_name ] = $callback_groups;
				continue;
			}

			$hook = new WP_Hook();

			// Loop through callback groups.
			foreach ( $callback_groups as $priority => $callbacks ) {

				// Loop through callbacks.
				foreach ( $callbacks as $cb ) {
					$hook->add_filter( $hook_name, $cb['function'], $priority, $cb['accepted_args'] );
				}
			}

			$normalized[ $hook_name ] = $hook;
		}

		return $normalized;
	}

	/**
	 * Determines whether an offset value exists.
	 *
	 * @since 4.7.0
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset An offset to check for.
	 * @return bool True if the offset exists, false otherwise.
	 */
	#[ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return isset( $this->callbacks[ $offset ] );
	}

	/**
	 * Retrieves a value at a specified offset.
	 *
	 * @since 4.7.0
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset The offset to retrieve.
	 * @return mixed If set, the value at the specified offset, null otherwise.
	 */
	#[ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return isset( $this->callbacks[ $offset ] ) ? $this->callbacks[ $offset ] : null;
	}

	/**
	 * Sets a value at a specified offset.
	 *
	 * @since 4.7.0
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value The value to set.
	 */
	#[ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		if ( is_null( $offset ) ) {
			$this->callbacks[] = $value;
		} else {
			$this->callbacks[ $offset ] = $value;
		}
	}

	/**
	 * Unsets a specified offset.
	 *
	 * @since 4.7.0
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset The offset to unset.
	 */
	#[ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
		unset( $this->callbacks[ $offset ] );
	}

	/**
	 * Returns the current element.
	 *
	 * @since 4.7.0
	 *
	 * @link https://www.php.net/manual/en/iterator.current.php
	 *
	 * @return array Of callbacks at current priority.
	 */
	#[ReturnTypeWillChange]
	public function current() {
		return current( $this->callbacks );
	}

	/**
	 * Moves forward to the next element.
	 *
	 * @since 4.7.0
	 *
	 * @link https://www.php.net/manual/en/iterator.next.php
	 *
	 * @return array Of callbacks at next priority.
	 */
	#[ReturnTypeWillChange]
	public function next() {
		return next( $this->callbacks );
	}

	/**
	 * Returns the key of the current element.
	 *
	 * @since 4.7.0
	 *
	 * @link https://www.php.net/manual/en/iterator.key.php
	 *
	 * @return mixed Returns current priority on success, or NULL on failure
	 */
	#[ReturnTypeWillChange]
	public function key() {
		return key( $this->callbacks );
	}

	/**
	 * Checks if current position is valid.
	 *
	 * @since 4.7.0
	 *
	 * @link https://www.php.net/manual/en/iterator.valid.php
	 *
	 * @return bool Whether the current position is valid.
	 */
	#[ReturnTypeWillChange]
	public function valid() {
		return key( $this->callbacks ) !== null;
	}

	/**
	 * Rewinds the Iterator to the first element.
	 *
	 * @since 4.7.0
	 *
	 * @link https://www.php.net/manual/en/iterator.rewind.php
	 */
	#[ReturnTypeWillChange]
	public function rewind() {
		reset( $this->callbacks );
	}

}
