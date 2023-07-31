<?php
/**
 * The plugin API is located in this file, which allows for creating actions
 * and filters and hooking functions, and methods. The functions or methods will
 * then be run when the action or filter is called.
 *
 * The API callback examples reference functions, but can be methods of classes.
 * To hook methods, you'll need to pass an array one of two ways.
 *
 * Any of the syntaxes explained in the PHP documentation for the
 * {@link https://www.php.net/manual/en/language.pseudo-types.php#language.types.callback 'callback'}
 * type are valid.
 *
 * Also see the {@link https://developer.wordpress.org/plugins/ Plugin API} for
 * more information and examples on how to use a lot of these functions.
 *
 * This file should have no external dependencies.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.5.0
 */

// Initialize the filter globals.
require __DIR__ . '/class-wp-hook.php';

/** @var WP_Hook[] $wp_filter */
global $wp_filter;

/** @var int[] $wp_actions */
global $wp_actions;

/** @var int[] $wp_filters */
global $wp_filters;

/** @var string[] $wp_current_filter */
global $wp_current_filter;

if ( $wp_filter ) {
	$wp_filter = WP_Hook::build_preinitialized_hooks( $wp_filter );
} else {
	$wp_filter = array();
}

if ( ! isset( $wp_actions ) ) {
	$wp_actions = array();
}

if ( ! isset( $wp_filters ) ) {
	$wp_filters = array();
}

if ( ! isset( $wp_current_filter ) ) {
	$wp_current_filter = array();
}

/**
 * Adds a callback function to a filter hook.
 *
 * WordPress offers filter hooks to allow plugins to modify
 * various types of internal data at runtime.
 *
 * A plugin can modify data by binding a callback to a filter hook. When the filter
 * is later applied, each bound callback is run in order of priority, and given
 * the opportunity to modify a value by returning a new value.
 *
 * The following example shows how a callback function is bound to a filter hook.
 *
 * Note that `$example` is passed to the callback, (maybe) modified, then returned:
 *
 *     function example_callback( $example ) {
 *         // Maybe modify $example in some way.
 *         return $example;
 *     }
 *     add_filter( 'example_filter', 'example_callback' );
 *
 * Bound callbacks can accept from none to the total number of arguments passed as parameters
 * in the corresponding apply_filters() call.
 *
 * In other words, if an apply_filters() call passes four total arguments, callbacks bound to
 * it can accept none (the same as 1) of the arguments or up to four. The important part is that
 * the `$accepted_args` value must reflect the number of arguments the bound callback *actually*
 * opted to accept. If no arguments were accepted by the callback that is considered to be the
 * same as accepting 1 argument. For example:
 *
 *     // Filter call.
 *     $value = apply_filters( 'hook', $value, $arg2, $arg3 );
 *
 *     // Accepting zero/one arguments.
 *     function example_callback() {
 *         ...
 *         return 'some value';
 *     }
 *     add_filter( 'hook', 'example_callback' ); // Where $priority is default 10, $accepted_args is default 1.
 *
 *     // Accepting two arguments (three possible).
 *     function example_callback( $value, $arg2 ) {
 *         ...
 *         return $maybe_modified_value;
 *     }
 *     add_filter( 'hook', 'example_callback', 10, 2 ); // Where $priority is 10, $accepted_args is 2.
 *
 * *Note:* The function will return true whether or not the callback is valid.
 * It is up to you to take care. This is done for optimization purposes, so
 * everything is as quick as possible.
 *
 * @since 0.71
 *
 * @global WP_Hook[] $wp_filter A multidimensional array of all hooks and the callbacks hooked to them.
 *
 * @param string   $hook_name     The name of the filter to add the callback to.
 * @param callable $callback      The callback to be run when the filter is applied.
 * @param int      $priority      Optional. Used to specify the order in which the functions
 *                                associated with a particular filter are executed.
 *                                Lower numbers correspond with earlier execution,
 *                                and functions with the same priority are executed
 *                                in the order in which they were added to the filter. Default 10.
 * @param int      $accepted_args Optional. The number of arguments the function accepts. Default 1.
 * @return true Always returns true.
 */
function add_filter( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
	global $wp_filter;

	if ( ! isset( $wp_filter[ $hook_name ] ) ) {
		$wp_filter[ $hook_name ] = new WP_Hook();
	}

	$wp_filter[ $hook_name ]->add_filter( $hook_name, $callback, $priority, $accepted_args );

	return true;
}

/**
 * Calls the callback functions that have been added to a filter hook.
 *
 * This function invokes all functions attached to filter hook `$hook_name`.
 * It is possible to create new filter hooks by simply calling this function,
 * specifying the name of the new hook using the `$hook_name` parameter.
 *
 * The function also allows for multiple additional arguments to be passed to hooks.
 *
 * Example usage:
 *
 *     // The filter callback function.
 *     function example_callback( $string, $arg1, $arg2 ) {
 *         // (maybe) modify $string.
 *         return $string;
 *     }
 *     add_filter( 'example_filter', 'example_callback', 10, 3 );
 *
 *     /*
 *      * Apply the filters by calling the 'example_callback()' function
 *      * that's hooked onto `example_filter` above.
 *      *
 *      * - 'example_filter' is the filter hook.
 *      * - 'filter me' is the value being filtered.
 *      * - $arg1 and $arg2 are the additional arguments passed to the callback.
 *     $value = apply_filters( 'example_filter', 'filter me', $arg1, $arg2 );
 *
 * @since 0.71
 * @since 6.0.0 Formalized the existing and already documented `...$args` parameter
 *              by adding it to the function signature.
 *
 * @global WP_Hook[] $wp_filter         Stores all of the filters and actions.
 * @global int[]     $wp_filters        Stores the number of times each filter was triggered.
 * @global string[]  $wp_current_filter Stores the list of current filters with the current one last.
 *
 * @param string $hook_name The name of the filter hook.
 * @param mixed  $value     The value to filter.
 * @param mixed  ...$args   Optional. Additional parameters to pass to the callback functions.
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function apply_filters( $hook_name, $value, ...$args ) {
	global $wp_filter, $wp_filters, $wp_current_filter;

	if ( ! isset( $wp_filters[ $hook_name ] ) ) {
		$wp_filters[ $hook_name ] = 1;
	} else {
		++$wp_filters[ $hook_name ];
	}

	// Do 'all' actions first.
	if ( isset( $wp_filter['all'] ) ) {
		$wp_current_filter[] = $hook_name;

		$all_args = func_get_args(); // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
		_wp_call_all_hook( $all_args );
	}

	if ( ! isset( $wp_filter[ $hook_name ] ) ) {
		if ( isset( $wp_filter['all'] ) ) {
			array_pop( $wp_current_filter );
		}

		return $value;
	}

	if ( ! isset( $wp_filter['all'] ) ) {
		$wp_current_filter[] = $hook_name;
	}

	// Pass the value to WP_Hook.
	array_unshift( $args, $value );

	$filtered = $wp_filter[ $hook_name ]->apply_filters( $value, $args );

	array_pop( $wp_current_filter );

	return $filtered;
}

/**
 * Calls the callback functions that have been added to a filter hook, specifying arguments in an array.
 *
 * @since 3.0.0
 *
 * @see apply_filters() This function is identical, but the arguments passed to the
 *                      functions hooked to `$hook_name` are supplied using an array.
 *
 * @global WP_Hook[] $wp_filter         Stores all of the filters and actions.
 * @global int[]     $wp_filters        Stores the number of times each filter was triggered.
 * @global string[]  $wp_current_filter Stores the list of current filters with the current one last.
 *
 * @param string $hook_name The name of the filter hook.
 * @param array  $args      The arguments supplied to the functions hooked to `$hook_name`.
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function apply_filters_ref_array( $hook_name, $args ) {
	global $wp_filter, $wp_filters, $wp_current_filter;

	if ( ! isset( $wp_filters[ $hook_name ] ) ) {
		$wp_filters[ $hook_name ] = 1;
	} else {
		++$wp_filters[ $hook_name ];
	}

	// Do 'all' actions first.
	if ( isset( $wp_filter['all'] ) ) {
		$wp_current_filter[] = $hook_name;
		$all_args            = func_get_args(); // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
		_wp_call_all_hook( $all_args );
	}

	if ( ! isset( $wp_filter[ $hook_name ] ) ) {
		if ( isset( $wp_filter['all'] ) ) {
			array_pop( $wp_current_filter );
		}

		return $args[0];
	}

	if ( ! isset( $wp_filter['all'] ) ) {
		$wp_current_filter[] = $hook_name;
	}

	$filtered = $wp_filter[ $hook_name ]->apply_filters( $args[0], $args );

	array_pop( $wp_current_filter );

	return $filtered;
}

/**
 * Checks if any filter has been registered for a hook.
 *
 * When using the `$callback` argument, this function may return a non-boolean value
 * that evaluates to false (e.g. 0), so use the `===` operator for testing the return value.
 *
 * @since 2.5.0
 *
 * @global WP_Hook[] $wp_filter Stores all of the filters and actions.
 *
 * @param string                      $hook_name The name of the filter hook.
 * @param callable|string|array|false $callback  Optional. The callback to check for.
 *                                               This function can be called unconditionally to speculatively check
 *                                               a callback that may or may not exist. Default false.
 * @return bool|int If `$callback` is omitted, returns boolean for whether the hook has
 *                  anything registered. When checking a specific function, the priority
 *                  of that hook is returned, or false if the function is not attached.
 */
function has_filter( $hook_name, $callback = false ) {
	global $wp_filter;

	if ( ! isset( $wp_filter[ $hook_name ] ) ) {
		return false;
	}

	return $wp_filter[ $hook_name ]->has_filter( $hook_name, $callback );
}

/**
 * Removes a callback function from a filter hook.
 *
 * This can be used to remove default functions attached to a specific filter
 * hook and possibly replace them with a substitute.
 *
 * To remove a hook, the `$callback` and `$priority` arguments must match
 * when the hook was added. This goes for both filters and actions. No warning
 * will be given on removal failure.
 *
 * @since 1.2.0
 *
 * @global WP_Hook[] $wp_filter Stores all of the filters and actions.
 *
 * @param string                $hook_name The filter hook to which the function to be removed is hooked.
 * @param callable|string|array $callback  The callback to be removed from running when the filter is applied.
 *                                         This function can be called unconditionally to speculatively remove
 *                                         a callback that may or may not exist.
 * @param int                   $priority  Optional. The exact priority used when adding the original
 *                                         filter callback. Default 10.
 * @return bool Whether the function existed before it was removed.
 */
function remove_filter( $hook_name, $callback, $priority = 10 ) {
	global $wp_filter;

	$r = false;

	if ( isset( $wp_filter[ $hook_name ] ) ) {
		$r = $wp_filter[ $hook_name ]->remove_filter( $hook_name, $callback, $priority );

		if ( ! $wp_filter[ $hook_name ]->callbacks ) {
			unset( $wp_filter[ $hook_name ] );
		}
	}

	return $r;
}

/**
 * Removes all of the callback functions from a filter hook.
 *
 * @since 2.7.0
 *
 * @global WP_Hook[] $wp_filter Stores all of the filters and actions.
 *
 * @param string    $hook_name The filter to remove callbacks from.
 * @param int|false $priority  Optional. The priority number to remove them from.
 *                             Default false.
 * @return true Always returns true.
 */
function remove_all_filters( $hook_name, $priority = false ) {
	global $wp_filter;

	if ( isset( $wp_filter[ $hook_name ] ) ) {
		$wp_filter[ $hook_name ]->remove_all_filters( $priority );

		if ( ! $wp_filter[ $hook_name ]->has_filters() ) {
			unset( $wp_filter[ $hook_name ] );
		}
	}

	return true;
}

/**
 * Retrieves the name of the current filter hook.
 *
 * @since 2.5.0
 *
 * @global string[] $wp_current_filter Stores the list of current filters with the current one last
 *
 * @return string Hook name of the current filter.
 */
function current_filter() {
	global $wp_current_filter;

	return end( $wp_current_filter );
}

/**
 * Returns whether or not a filter hook is currently being processed.
 *
 * The function current_filter() only returns the most recent filter being executed.
 * did_filter() returns the number of times a filter has been applied during
 * the current request.
 *
 * This function allows detection for any filter currently being executed
 * (regardless of whether it's the most recent filter to fire, in the case of
 * hooks called from hook callbacks) to be verified.
 *
 * @since 3.9.0
 *
 * @see current_filter()
 * @see did_filter()
 * @global string[] $wp_current_filter Current filter.
 *
 * @param string|null $hook_name Optional. Filter hook to check. Defaults to null,
 *                               which checks if any filter is currently being run.
 * @return bool Whether the filter is currently in the stack.
 */
function doing_filter( $hook_name = null ) {
	global $wp_current_filter;

	if ( null === $hook_name ) {
		return ! empty( $wp_current_filter );
	}

	return in_array( $hook_name, $wp_current_filter, true );
}

/**
 * Retrieves the number of times a filter has been applied during the current request.
 *
 * @since 6.1.0
 *
 * @global int[] $wp_filters Stores the number of times each filter was triggered.
 *
 * @param string $hook_name The name of the filter hook.
 * @return int The number of times the filter hook has been applied.
 */
function did_filter( $hook_name ) {
	global $wp_filters;

	if ( ! isset( $wp_filters[ $hook_name ] ) ) {
		return 0;
	}

	return $wp_filters[ $hook_name ];
}

/**
 * Adds a callback function to an action hook.
 *
 * Actions are the hooks that the WordPress core launches at specific points
 * during execution, or when specific events occur. Plugins can specify that
 * one or more of its PHP functions are executed at these points, using the
 * Action API.
 *
 * @since 1.2.0
 *
 * @param string   $hook_name       The name of the action to add the callback to.
 * @param callable $callback        The callback to be run when the action is called.
 * @param int      $priority        Optional. Used to specify the order in which the functions
 *                                  associated with a particular action are executed.
 *                                  Lower numbers correspond with earlier execution,
 *                                  and functions with the same priority are executed
 *                                  in the order in which they were added to the action. Default 10.
 * @param int      $accepted_args   Optional. The number of arguments the function accepts. Default 1.
 * @return true Always returns true.
 */
function add_action( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
	return add_filter( $hook_name, $callback, $priority, $accepted_args );
}

/**
 * Calls the callback functions that have been added to an action hook.
 *
 * This function invokes all functions attached to action hook `$hook_name`.
 * It is possible to create new action hooks by simply calling this function,
 * specifying the name of the new hook using the `$hook_name` parameter.
 *
 * You can pass extra arguments to the hooks, much like you can with `apply_filters()`.
 *
 * Example usage:
 *
 *     // The action callback function.
 *     function example_callback( $arg1, $arg2 ) {
 *         // (maybe) do something with the args.
 *     }
 *     add_action( 'example_action', 'example_callback', 10, 2 );
 *
 *     /*
 *      * Trigger the actions by calling the 'example_callback()' function
 *      * that's hooked onto `example_action` above.
 *      *
 *      * - 'example_action' is the action hook.
 *      * - $arg1 and $arg2 are the additional arguments passed to the callback.
 *     do_action( 'example_action', $arg1, $arg2 );
 *
 * @since 1.2.0
 * @since 5.3.0 Formalized the existing and already documented `...$arg` parameter
 *              by adding it to the function signature.
 *
 * @global WP_Hook[] $wp_filter         Stores all of the filters and actions.
 * @global int[]     $wp_actions        Stores the number of times each action was triggered.
 * @global string[]  $wp_current_filter Stores the list of current filters with the current one last.
 *
 * @param string $hook_name The name of the action to be executed.
 * @param mixed  ...$arg    Optional. Additional arguments which are passed on to the
 *                          functions hooked to the action. Default empty.
 */
function do_action( $hook_name, ...$arg ) {
	global $wp_filter, $wp_actions, $wp_current_filter;

	if ( ! isset( $wp_actions[ $hook_name ] ) ) {
		$wp_actions[ $hook_name ] = 1;
	} else {
		++$wp_actions[ $hook_name ];
	}

	// Do 'all' actions first.
	if ( isset( $wp_filter['all'] ) ) {
		$wp_current_filter[] = $hook_name;
		$all_args            = func_get_args(); // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
		_wp_call_all_hook( $all_args );
	}

	if ( ! isset( $wp_filter[ $hook_name ] ) ) {
		if ( isset( $wp_filter['all'] ) ) {
			array_pop( $wp_current_filter );
		}

		return;
	}

	if ( ! isset( $wp_filter['all'] ) ) {
		$wp_current_filter[] = $hook_name;
	}

	if ( empty( $arg ) ) {
		$arg[] = '';
	} elseif ( is_array( $arg[0] ) && 1 === count( $arg[0] ) && isset( $arg[0][0] ) && is_object( $arg[0][0] ) ) {
		// Backward compatibility for PHP4-style passing of `array( &$this )` as action `$arg`.
		$arg[0] = $arg[0][0];
	}

	$wp_filter[ $hook_name ]->do_action( $arg );

	array_pop( $wp_current_filter );
}

/**
 * Calls the callback functions that have been added to an action hook, specifying arguments in an array.
 *
 * @since 2.1.0
 *
 * @see do_action() This function is identical, but the arguments passed to the
 *                  functions hooked to `$hook_name` are supplied using an array.
 *
 * @global WP_Hook[] $wp_filter         Stores all of the filters and actions.
 * @global int[]     $wp_actions        Stores the number of times each action was triggered.
 * @global string[]  $wp_current_filter Stores the list of current filters with the current one last.
 *
 * @param string $hook_name The name of the action to be executed.
 * @param array  $args      The arguments supplied to the functions hooked to `$hook_name`.
 */
function do_action_ref_array( $hook_name, $args ) {
	global $wp_filter, $wp_actions, $wp_current_filter;

	if ( ! isset( $wp_actions[ $hook_name ] ) ) {
		$wp_actions[ $hook_name ] = 1;
	} else {
		++$wp_actions[ $hook_name ];
	}

	// Do 'all' actions first.
	if ( isset( $wp_filter['all'] ) ) {
		$wp_current_filter[] = $hook_name;
		$all_args            = func_get_args(); // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
		_wp_call_all_hook( $all_args );
	}

	if ( ! isset( $wp_filter[ $hook_name ] ) ) {
		if ( isset( $wp_filter['all'] ) ) {
			array_pop( $wp_current_filter );
		}

		return;
	}

	if ( ! isset( $wp_filter['all'] ) ) {
		$wp_current_filter[] = $hook_name;
	}

	$wp_filter[ $hook_name ]->do_action( $args );

	array_pop( $wp_current_filter );
}

/**
 * Checks if any action has been registered for a hook.
 *
 * When using the `$callback` argument, this function may return a non-boolean value
 * that evaluates to false (e.g. 0), so use the `===` operator for testing the return value.
 *
 * @since 2.5.0
 *
 * @see has_filter() This function is an alias of has_filter().
 *
 * @param string                      $hook_name The name of the action hook.
 * @param callable|string|array|false $callback  Optional. The callback to check for.
 *                                               This function can be called unconditionally to speculatively check
 *                                               a callback that may or may not exist. Default false.
 * @return bool|int If `$callback` is omitted, returns boolean for whether the hook has
 *                  anything registered. When checking a specific function, the priority
 *                  of that hook is returned, or false if the function is not attached.
 */
function has_action( $hook_name, $callback = false ) {
	return has_filter( $hook_name, $callback );
}

/**
 * Removes a callback function from an action hook.
 *
 * This can be used to remove default functions attached to a specific action
 * hook and possibly replace them with a substitute.
 *
 * To remove a hook, the `$callback` and `$priority` arguments must match
 * when the hook was added. This goes for both filters and actions. No warning
 * will be given on removal failure.
 *
 * @since 1.2.0
 *
 * @param string                $hook_name The action hook to which the function to be removed is hooked.
 * @param callable|string|array $callback  The name of the function which should be removed.
 *                                         This function can be called unconditionally to speculatively remove
 *                                         a callback that may or may not exist.
 * @param int                   $priority  Optional. The exact priority used when adding the original
 *                                         action callback. Default 10.
 * @return bool Whether the function is removed.
 */
function remove_action( $hook_name, $callback, $priority = 10 ) {
	return remove_filter( $hook_name, $callback, $priority );
}

/**
 * Removes all of the callback functions from an action hook.
 *
 * @since 2.7.0
 *
 * @param string    $hook_name The action to remove callbacks from.
 * @param int|false $priority  Optional. The priority number to remove them from.
 *                             Default false.
 * @return true Always returns true.
 */
function remove_all_actions( $hook_name, $priority = false ) {
	return remove_all_filters( $hook_name, $priority );
}

/**
 * Retrieves the name of the current action hook.
 *
 * @since 3.9.0
 *
 * @return string Hook name of the current action.
 */
function current_action() {
	return current_filter();
}

/**
 * Returns whether or not an action hook is currently being processed.
 *
 * The function current_action() only returns the most recent action being executed.
 * did_action() returns the number of times an action has been fired during
 * the current request.
 *
 * This function allows detection for any action currently being executed
 * (regardless of whether it's the most recent action to fire, in the case of
 * hooks called from hook callbacks) to be verified.
 *
 * @since 3.9.0
 *
 * @see current_action()
 * @see did_action()
 *
 * @param string|null $hook_name Optional. Action hook to check. Defaults to null,
 *                               which checks if any action is currently being run.
 * @return bool Whether the action is currently in the stack.
 */
function doing_action( $hook_name = null ) {
	return doing_filter( $hook_name );
}

/**
 * Retrieves the number of times an action has been fired during the current request.
 *
 * @since 2.1.0
 *
 * @global int[] $wp_actions Stores the number of times each action was triggered.
 *
 * @param string $hook_name The name of the action hook.
 * @return int The number of times the action hook has been fired.
 */
function did_action( $hook_name ) {
	global $wp_actions;

	if ( ! isset( $wp_actions[ $hook_name ] ) ) {
		return 0;
	}

	return $wp_actions[ $hook_name ];
}

/**
 * Fires functions attached to a deprecated filter hook.
 *
 * When a filter hook is deprecated, the apply_filters() call is replaced with
 * apply_filters_deprecated(), which triggers a deprecation notice and then fires
 * the original filter hook.
 *
 * Note: the value and extra arguments passed to the original apply_filters() call
 * must be passed here to `$args` as an array. For example:
 *
 *     // Old filter.
 *     return apply_filters( 'wpdocs_filter', $value, $extra_arg );
 *
 *     // Deprecated.
 *     return apply_filters_deprecated( 'wpdocs_filter', array( $value, $extra_arg ), '4.9.0', 'wpdocs_new_filter' );
 *
 * @since 4.6.0
 *
 * @see _deprecated_hook()
 *
 * @param string $hook_name   The name of the filter hook.
 * @param array  $args        Array of additional function arguments to be passed to apply_filters().
 * @param string $version     The version of WordPress that deprecated the hook.
 * @param string $replacement Optional. The hook that should have been used. Default empty.
 * @param string $message     Optional. A message regarding the change. Default empty.
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function apply_filters_deprecated( $hook_name, $args, $version, $replacement = '', $message = '' ) {
	if ( ! has_filter( $hook_name ) ) {
		return $args[0];
	}

	_deprecated_hook( $hook_name, $version, $replacement, $message );

	return apply_filters_ref_array( $hook_name, $args );
}

/**
 * Fires functions attached to a deprecated action hook.
 *
 * When an action hook is deprecated, the do_action() call is replaced with
 * do_action_deprecated(), which triggers a deprecation notice and then fires
 * the original hook.
 *
 * @since 4.6.0
 *
 * @see _deprecated_hook()
 *
 * @param string $hook_name   The name of the action hook.
 * @param array  $args        Array of additional function arguments to be passed to do_action().
 * @param string $version     The version of WordPress that deprecated the hook.
 * @param string $replacement Optional. The hook that should have been used. Default empty.
 * @param string $message     Optional. A message regarding the change. Default empty.
 */
function do_action_deprecated( $hook_name, $args, $version, $replacement = '', $message = '' ) {
	if ( ! has_action( $hook_name ) ) {
		return;
	}

	_deprecated_hook( $hook_name, $version, $replacement, $message );

	do_action_ref_array( $hook_name, $args );
}

//
// Functions for handling plugins.
//

/**
 * Gets the basename of a plugin.
 *
 * This method extracts the name of a plugin from its filename.
 *
 * @since 1.5.0
 *
 * @global array $wp_plugin_paths
 *
 * @param string $file The filename of plugin.
 * @return string The name of a plugin.
 */
function plugin_basename( $file ) {
	global $wp_plugin_paths;

	// $wp_plugin_paths contains normalized paths.
	$file = wp_normalize_path( $file );

	arsort( $wp_plugin_paths );

	foreach ( $wp_plugin_paths as $dir => $realdir ) {
		if ( str_starts_with( $file, $realdir ) ) {
			$file = $dir . substr( $file, strlen( $realdir ) );
		}
	}

	$plugin_dir    = wp_normalize_path( WP_PLUGIN_DIR );
	$mu_plugin_dir = wp_normalize_path( WPMU_PLUGIN_DIR );

	// Get relative path from plugins directory.
	$file = preg_replace( '#^' . preg_quote( $plugin_dir, '#' ) . '/|^' . preg_quote( $mu_plugin_dir, '#' ) . '/#', '', $file );
	$file = trim( $file, '/' );
	return $file;
}

/**
 * Register a plugin's real path.
 *
 * This is used in plugin_basename() to resolve symlinked paths.
 *
 * @since 3.9.0
 *
 * @see wp_normalize_path()
 *
 * @global array $wp_plugin_paths
 *
 * @param string $file Known path to the file.
 * @return bool Whether the path was able to be registered.
 */
function wp_register_plugin_realpath( $file ) {
	global $wp_plugin_paths;

	// Normalize, but store as static to avoid recalculation of a constant value.
	static $wp_plugin_path = null, $wpmu_plugin_path = null;

	if ( ! isset( $wp_plugin_path ) ) {
		$wp_plugin_path   = wp_normalize_path( WP_PLUGIN_DIR );
		$wpmu_plugin_path = wp_normalize_path( WPMU_PLUGIN_DIR );
	}

	$plugin_path     = wp_normalize_path( dirname( $file ) );
	$plugin_realpath = wp_normalize_path( dirname( realpath( $file ) ) );

	if ( $plugin_path === $wp_plugin_path || $plugin_path === $wpmu_plugin_path ) {
		return false;
	}

	if ( $plugin_path !== $plugin_realpath ) {
		$wp_plugin_paths[ $plugin_path ] = $plugin_realpath;
	}

	return true;
}

/**
 * Get the filesystem directory path (with trailing slash) for the plugin __FILE__ passed in.
 *
 * @since 2.8.0
 *
 * @param string $file The filename of the plugin (__FILE__).
 * @return string the filesystem path of the directory that contains the plugin.
 */
function plugin_dir_path( $file ) {
	return trailingslashit( dirname( $file ) );
}

/**
 * Get the URL directory path (with trailing slash) for the plugin __FILE__ passed in.
 *
 * @since 2.8.0
 *
 * @param string $file The filename of the plugin (__FILE__).
 * @return string the URL path of the directory that contains the plugin.
 */
function plugin_dir_url( $file ) {
	return trailingslashit( plugins_url( '', $file ) );
}

/**
 * Set the activation hook for a plugin.
 *
 * When a plugin is activated, the action 'activate_PLUGINNAME' hook is
 * called. In the name of this hook, PLUGINNAME is replaced with the name
 * of the plugin, including the optional subdirectory. For example, when the
 * plugin is located in wp-content/plugins/sampleplugin/sample.php, then
 * the name of this hook will become 'activate_sampleplugin/sample.php'.
 *
 * When the plugin consists of only one file and is (as by default) located at
 * wp-content/plugins/sample.php the name of this hook will be
 * 'activate_sample.php'.
 *
 * @since 2.0.0
 *
 * @param string   $file     The filename of the plugin including the path.
 * @param callable $callback The function hooked to the 'activate_PLUGIN' action.
 */
function register_activation_hook( $file, $callback ) {
	$file = plugin_basename( $file );
	add_action( 'activate_' . $file, $callback );
}

/**
 * Sets the deactivation hook for a plugin.
 *
 * When a plugin is deactivated, the action 'deactivate_PLUGINNAME' hook is
 * called. In the name of this hook, PLUGINNAME is replaced with the name
 * of the plugin, including the optional subdirectory. For example, when the
 * plugin is located in wp-content/plugins/sampleplugin/sample.php, then
 * the name of this hook will become 'deactivate_sampleplugin/sample.php'.
 *
 * When the plugin consists of only one file and is (as by default) located at
 * wp-content/plugins/sample.php the name of this hook will be
 * 'deactivate_sample.php'.
 *
 * @since 2.0.0
 *
 * @param string   $file     The filename of the plugin including the path.
 * @param callable $callback The function hooked to the 'deactivate_PLUGIN' action.
 */
function register_deactivation_hook( $file, $callback ) {
	$file = plugin_basename( $file );
	add_action( 'deactivate_' . $file, $callback );
}

/**
 * Sets the uninstallation hook for a plugin.
 *
 * Registers the uninstall hook that will be called when the user clicks on the
 * uninstall link that calls for the plugin to uninstall itself. The link won't
 * be active unless the plugin hooks into the action.
 *
 * The plugin should not run arbitrary code outside of functions, when
 * registering the uninstall hook. In order to run using the hook, the plugin
 * will have to be included, which means that any code laying outside of a
 * function will be run during the uninstallation process. The plugin should not
 * hinder the uninstallation process.
 *
 * If the plugin can not be written without running code within the plugin, then
 * the plugin should create a file named 'uninstall.php' in the base plugin
 * folder. This file will be called, if it exists, during the uninstallation process
 * bypassing the uninstall hook. The plugin, when using the 'uninstall.php'
 * should always check for the 'WP_UNINSTALL_PLUGIN' constant, before
 * executing.
 *
 * @since 2.7.0
 *
 * @param string   $file     Plugin file.
 * @param callable $callback The callback to run when the hook is called. Must be
 *                           a static method or function.
 */
function register_uninstall_hook( $file, $callback ) {
	if ( is_array( $callback ) && is_object( $callback[0] ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Only a static class method or function can be used in an uninstall hook.' ), '3.1.0' );
		return;
	}

	/*
	 * The option should not be autoloaded, because it is not needed in most
	 * cases. Emphasis should be put on using the 'uninstall.php' way of
	 * uninstalling the plugin.
	 */
	$uninstallable_plugins = (array) get_option( 'uninstall_plugins' );
	$plugin_basename       = plugin_basename( $file );

	if ( ! isset( $uninstallable_plugins[ $plugin_basename ] ) || $uninstallable_plugins[ $plugin_basename ] !== $callback ) {
		$uninstallable_plugins[ $plugin_basename ] = $callback;
		update_option( 'uninstall_plugins', $uninstallable_plugins );
	}
}

/**
 * Calls the 'all' hook, which will process the functions hooked into it.
 *
 * The 'all' hook passes all of the arguments or parameters that were used for
 * the hook, which this function was called for.
 *
 * This function is used internally for apply_filters(), do_action(), and
 * do_action_ref_array() and is not meant to be used from outside those
 * functions. This function does not check for the existence of the all hook, so
 * it will fail unless the all hook exists prior to this function call.
 *
 * @since 2.5.0
 * @access private
 *
 * @global WP_Hook[] $wp_filter Stores all of the filters and actions.
 *
 * @param array $args The collected parameters from the hook that was called.
 */
function _wp_call_all_hook( $args ) {
	global $wp_filter;

	$wp_filter['all']->do_all_hook( $args );
}

/**
 * Builds a unique string ID for a hook callback function.
 *
 * Functions and static method callbacks are just returned as strings and
 * shouldn't have any speed penalty.
 *
 * @link https://core.trac.wordpress.org/ticket/3875
 *
 * @since 2.2.3
 * @since 5.3.0 Removed workarounds for spl_object_hash().
 *              `$hook_name` and `$priority` are no longer used,
 *              and the function always returns a string.
 *
 * @access private
 *
 * @param string                $hook_name Unused. The name of the filter to build ID for.
 * @param callable|string|array $callback  The callback to generate ID for. The callback may
 *                                         or may not exist.
 * @param int                   $priority  Unused. The order in which the functions
 *                                         associated with a particular action are executed.
 * @return string Unique function ID for usage as array key.
 */
function _wp_filter_build_unique_id( $hook_name, $callback, $priority ) {
	if ( is_string( $callback ) ) {
		return $callback;
	}

	if ( is_object( $callback ) ) {
		// Closures are currently implemented as objects.
		$callback = array( $callback, '' );
	} else {
		$callback = (array) $callback;
	}

	if ( is_object( $callback[0] ) ) {
		// Object class calling.
		return spl_object_hash( $callback[0] ) . $callback[1];
	} elseif ( is_string( $callback[0] ) ) {
		// Static calling.
		return $callback[0] . '::' . $callback[1];
	}
}
