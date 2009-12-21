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
 * {@link http://us2.php.net/manual/en/language.pseudo-types.php#language.types.callback 'callback'}
 * type are valid.
 *
 * Also see the {@link http://codex.wordpress.org/Plugin_API Plugin API} for
 * more information and examples on how to use a lot of these functions.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.5
 */

/**
 * Hooks a function or method to a specific filter action.
 *
 * Filters are the hooks that WordPress launches to modify text of various types
 * before adding it to the database or sending it to the browser screen. Plugins
 * can specify that one or more of its PHP functions is executed to
 * modify specific types of text at these times, using the Filter API.
 *
 * To use the API, the following code should be used to bind a callback to the
 * filter.
 *
 * <code>
 * function example_hook($example) { echo $example; }
 * add_filter('example_filter', 'example_hook');
 * </code>
 *
 * In WordPress 1.5.1+, hooked functions can take extra arguments that are set
 * when the matching do_action() or apply_filters() call is run. The
 * $accepted_args allow for calling functions only when the number of args
 * match. Hooked functions can take extra arguments that are set when the
 * matching do_action() or apply_filters() call is run. For example, the action
 * comment_id_not_found will pass any functions that hook onto it the ID of the
 * requested comment.
 *
 * <strong>Note:</strong> the function will return true no matter if the
 * function was hooked fails or not. There are no checks for whether the
 * function exists beforehand and no checks to whether the <tt>$function_to_add
 * is even a string. It is up to you to take care and this is done for
 * optimization purposes, so everything is as quick as possible.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 0.71
 * @global array $wp_filter Stores all of the filters added in the form of
 *	wp_filter['tag']['array of priorities']['array of functions serialized']['array of ['array (functions, accepted_args)']']
 * @global array $merged_filters Tracks the tags that need to be merged for later. If the hook is added, it doesn't need to run through that process.
 *
 * @param string $tag The name of the filter to hook the $function_to_add to.
 * @param callback $function_to_add The name of the function to be called when the filter is applied.
 * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
 * @param int $accepted_args optional. The number of arguments the function accept (default 1).
 * @return boolean true
 */
function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	global $wp_filter, $merged_filters;

	$idx = _wp_filter_build_unique_id($tag, $function_to_add, $priority);
	$wp_filter[$tag][$priority][$idx] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
	unset( $merged_filters[ $tag ] );
	return true;
}

/**
 * Check if any filter has been registered for a hook.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 2.5
 * @global array $wp_filter Stores all of the filters
 *
 * @param string $tag The name of the filter hook.
 * @param callback $function_to_check optional.  If specified, return the priority of that function on this hook or false if not attached.
 * @return int|boolean Optionally returns the priority on that hook for the specified function.
 */
function has_filter($tag, $function_to_check = false) {
	global $wp_filter;

	$has = !empty($wp_filter[$tag]);
	if ( false === $function_to_check || false == $has )
		return $has;

	if ( !$idx = _wp_filter_build_unique_id($tag, $function_to_check, false) )
		return false;

	foreach ( (array) array_keys($wp_filter[$tag]) as $priority ) {
		if ( isset($wp_filter[$tag][$priority][$idx]) )
			return $priority;
	}

	return false;
}

/**
 * Call the functions added to a filter hook.
 *
 * The callback functions attached to filter hook $tag are invoked by calling
 * this function. This function can be used to create a new filter hook by
 * simply calling this function with the name of the new hook specified using
 * the $tag parameter.
 *
 * The function allows for additional arguments to be added and passed to hooks.
 * <code>
 * function example_hook($string, $arg1, $arg2)
 * {
 *		//Do stuff
 *		return $string;
 * }
 * $value = apply_filters('example_filter', 'filter me', 'arg1', 'arg2');
 * </code>
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 0.71
 * @global array $wp_filter Stores all of the filters
 * @global array $merged_filters Merges the filter hooks using this function.
 * @global array $wp_current_filter stores the list of current filters with the current one last
 *
 * @param string $tag The name of the filter hook.
 * @param mixed $value The value on which the filters hooked to <tt>$tag</tt> are applied on.
 * @param mixed $var,... Additional variables passed to the functions hooked to <tt>$tag</tt>.
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function apply_filters($tag, $value) {
	global $wp_filter, $merged_filters, $wp_current_filter;

	$args = array();
	$wp_current_filter[] = $tag;

	// Do 'all' actions first
	if ( isset($wp_filter['all']) ) {
		$args = func_get_args();
		_wp_call_all_hook($args);
	}

	if ( !isset($wp_filter[$tag]) ) {
		array_pop($wp_current_filter);
		return $value;
	}

	// Sort
	if ( !isset( $merged_filters[ $tag ] ) ) {
		ksort($wp_filter[$tag]);
		$merged_filters[ $tag ] = true;
	}

	reset( $wp_filter[ $tag ] );

	if ( empty($args) )
		$args = func_get_args();

	do {
		foreach( (array) current($wp_filter[$tag]) as $the_ )
			if ( !is_null($the_['function']) ){
				$args[1] = $value;
				$value = call_user_func_array($the_['function'], array_slice($args, 1, (int) $the_['accepted_args']));
			}

	} while ( next($wp_filter[$tag]) !== false );

	array_pop( $wp_current_filter );

	return $value;
}

/**
 * Removes a function from a specified filter hook.
 *
 * This function removes a function attached to a specified filter hook. This
 * method can be used to remove default functions attached to a specific filter
 * hook and possibly replace them with a substitute.
 *
 * To remove a hook, the $function_to_remove and $priority arguments must match
 * when the hook was added. This goes for both filters and actions. No warning
 * will be given on removal failure.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.2
 *
 * @param string $tag The filter hook to which the function to be removed is hooked.
 * @param callback $function_to_remove The name of the function which should be removed.
 * @param int $priority optional. The priority of the function (default: 10).
 * @param int $accepted_args optional. The number of arguments the function accpets (default: 1).
 * @return boolean Whether the function existed before it was removed.
 */
function remove_filter($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
	$function_to_remove = _wp_filter_build_unique_id($tag, $function_to_remove, $priority);

	$r = isset($GLOBALS['wp_filter'][$tag][$priority][$function_to_remove]);

	if ( true === $r) {
		unset($GLOBALS['wp_filter'][$tag][$priority][$function_to_remove]);
		if ( empty($GLOBALS['wp_filter'][$tag][$priority]) )
			unset($GLOBALS['wp_filter'][$tag][$priority]);
		unset($GLOBALS['merged_filters'][$tag]);
	}

	return $r;
}

/**
 * Remove all of the hooks from a filter.
 *
 * @since 2.7
 *
 * @param string $tag The filter to remove hooks from.
 * @param int $priority The priority number to remove.
 * @return bool True when finished.
 */
function remove_all_filters($tag, $priority = false) {
	global $wp_filter, $merged_filters;

	if( isset($wp_filter[$tag]) ) {
		if( false !== $priority && isset($$wp_filter[$tag][$priority]) )
			unset($wp_filter[$tag][$priority]);
		else
			unset($wp_filter[$tag]);
	}

	if( isset($merged_filters[$tag]) )
		unset($merged_filters[$tag]);

	return true;
}

/**
 * Retrieve the name of the current filter or action.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 2.5
 *
 * @return string Hook name of the current filter or action.
 */
function current_filter() {
	global $wp_current_filter;
	return end( $wp_current_filter );
}


/**
 * Hooks a function on to a specific action.
 *
 * Actions are the hooks that the WordPress core launches at specific points
 * during execution, or when specific events occur. Plugins can specify that
 * one or more of its PHP functions are executed at these points, using the
 * Action API.
 *
 * @uses add_filter() Adds an action. Parameter list and functionality are the same.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.2
 *
 * @param string $tag The name of the action to which the $function_to_add is hooked.
 * @param callback $function_to_add The name of the function you wish to be called.
 * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
 * @param int $accepted_args optional. The number of arguments the function accept (default 1).
 */
function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	return add_filter($tag, $function_to_add, $priority, $accepted_args);
}


/**
 * Execute functions hooked on a specific action hook.
 *
 * This function invokes all functions attached to action hook $tag. It is
 * possible to create new action hooks by simply calling this function,
 * specifying the name of the new hook using the <tt>$tag</tt> parameter.
 *
 * You can pass extra arguments to the hooks, much like you can with
 * apply_filters().
 *
 * @see apply_filters() This function works similar with the exception that
 * nothing is returned and only the functions or methods are called.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.2
 * @global array $wp_filter Stores all of the filters
 * @global array $wp_actions Increments the amount of times action was triggered.
 *
 * @param string $tag The name of the action to be executed.
 * @param mixed $arg,... Optional additional arguments which are passed on to the functions hooked to the action.
 * @return null Will return null if $tag does not exist in $wp_filter array
 */
function do_action($tag, $arg = '') {
	global $wp_filter, $wp_actions, $merged_filters, $wp_current_filter;

	if ( ! isset($wp_actions) )
		$wp_actions = array();
	
	if ( ! isset($wp_actions[$tag]) )
		$wp_actions[$tag] = 1;
	else
		++$wp_actions[$tag];

	$wp_current_filter[] = $tag;

	// Do 'all' actions first
	if ( isset($wp_filter['all']) ) {
		$all_args = func_get_args();
		_wp_call_all_hook($all_args);
	}

	if ( !isset($wp_filter[$tag]) ) {
		array_pop($wp_current_filter);
		return;
	}

	$args = array();
	if ( is_array($arg) && 1 == count($arg) && is_object($arg[0]) ) // array(&$this)
		$args[] =& $arg[0];
	else
		$args[] = $arg;
	for ( $a = 2; $a < func_num_args(); $a++ )
		$args[] = func_get_arg($a);

	// Sort
	if ( !isset( $merged_filters[ $tag ] ) ) {
		ksort($wp_filter[$tag]);
		$merged_filters[ $tag ] = true;
	}

	reset( $wp_filter[ $tag ] );

	do {
		foreach ( (array) current($wp_filter[$tag]) as $the_ )
			if ( !is_null($the_['function']) )
				call_user_func_array($the_['function'], array_slice($args, 0, (int) $the_['accepted_args']));

	} while ( next($wp_filter[$tag]) !== false );

	array_pop($wp_current_filter);
}

/**
 * Retrieve the number times an action is fired.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 2.1
 * @global array $wp_actions Increments the amount of times action was triggered.
 *
 * @param string $tag The name of the action hook.
 * @return int The number of times action hook <tt>$tag</tt> is fired
 */
function did_action($tag) {
	global $wp_actions;

	if ( ! isset( $wp_actions ) || ! isset( $wp_actions[$tag] ) )
		return 0;

	return $wp_actions[$tag];
}

/**
 * Execute functions hooked on a specific action hook, specifying arguments in an array.
 *
 * @see do_action() This function is identical, but the arguments passed to the
 * functions hooked to <tt>$tag</tt> are supplied using an array.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 2.1
 * @global array $wp_filter Stores all of the filters
 * @global array $wp_actions Increments the amount of times action was triggered.
 *
 * @param string $tag The name of the action to be executed.
 * @param array $args The arguments supplied to the functions hooked to <tt>$tag</tt>
 * @return null Will return null if $tag does not exist in $wp_filter array
 */
function do_action_ref_array($tag, $args) {
	global $wp_filter, $wp_actions, $merged_filters, $wp_current_filter;

	if ( ! isset($wp_actions) )
		$wp_actions = array();
	
	if ( ! isset($wp_actions[$tag]) )
		$wp_actions[$tag] = 1;
	else
		++$wp_actions[$tag];

	$wp_current_filter[] = $tag;

	// Do 'all' actions first
	if ( isset($wp_filter['all']) ) {
		$all_args = func_get_args();
		_wp_call_all_hook($all_args);
	}

	if ( !isset($wp_filter[$tag]) ) {
		array_pop($wp_current_filter);
		return;
	}

	// Sort
	if ( !isset( $merged_filters[ $tag ] ) ) {
		ksort($wp_filter[$tag]);
		$merged_filters[ $tag ] = true;
	}

	reset( $wp_filter[ $tag ] );

	do {
		foreach( (array) current($wp_filter[$tag]) as $the_ )
			if ( !is_null($the_['function']) )
				call_user_func_array($the_['function'], array_slice($args, 0, (int) $the_['accepted_args']));

	} while ( next($wp_filter[$tag]) !== false );

	array_pop($wp_current_filter);
}

/**
 * Check if any action has been registered for a hook.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 2.5
 * @see has_filter() has_action() is an alias of has_filter().
 *
 * @param string $tag The name of the action hook.
 * @param callback $function_to_check optional.  If specified, return the priority of that function on this hook or false if not attached.
 * @return int|boolean Optionally returns the priority on that hook for the specified function.
 */
function has_action($tag, $function_to_check = false) {
	return has_filter($tag, $function_to_check);
}

/**
 * Removes a function from a specified action hook.
 *
 * This function removes a function attached to a specified action hook. This
 * method can be used to remove default functions attached to a specific filter
 * hook and possibly replace them with a substitute.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.2
 *
 * @param string $tag The action hook to which the function to be removed is hooked.
 * @param callback $function_to_remove The name of the function which should be removed.
 * @param int $priority optional The priority of the function (default: 10).
 * @param int $accepted_args optional. The number of arguments the function accpets (default: 1).
 * @return boolean Whether the function is removed.
 */
function remove_action($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
	return remove_filter($tag, $function_to_remove, $priority, $accepted_args);
}

/**
 * Remove all of the hooks from an action.
 *
 * @since 2.7
 *
 * @param string $tag The action to remove hooks from.
 * @param int $priority The priority number to remove them from.
 * @return bool True when finished.
 */
function remove_all_actions($tag, $priority = false) {
	return remove_all_filters($tag, $priority);
}

//
// Functions for handling plugins.
//

/**
 * Gets the basename of a plugin.
 *
 * This method extracts the name of a plugin from its filename.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.5
 *
 * @access private
 *
 * @param string $file The filename of plugin.
 * @return string The name of a plugin.
 * @uses WP_PLUGIN_DIR
 */
function plugin_basename($file) {
	$file = str_replace('\\','/',$file); // sanitize for Win32 installs
	$file = preg_replace('|/+|','/', $file); // remove any duplicate slash
	$plugin_dir = str_replace('\\','/',WP_PLUGIN_DIR); // sanitize for Win32 installs
	$plugin_dir = preg_replace('|/+|','/', $plugin_dir); // remove any duplicate slash
	$mu_plugin_dir = str_replace('\\','/',WPMU_PLUGIN_DIR); // sanitize for Win32 installs
	$mu_plugin_dir = preg_replace('|/+|','/', $mu_plugin_dir); // remove any duplicate slash
	$file = preg_replace('#^' . preg_quote($plugin_dir, '#') . '/|^' . preg_quote($mu_plugin_dir, '#') . '/#','',$file); // get relative path from plugins dir
	$file = trim($file, '/');
	return $file;
}

/**
 * Gets the filesystem directory path (with trailing slash) for the plugin __FILE__ passed in
 * @package WordPress
 * @subpackage Plugin
 * @since 2.8
 *
 * @param string $file The filename of the plugin (__FILE__)
 * @return string the filesystem path of the directory that contains the plugin
 */
function plugin_dir_path( $file ) {
	return trailingslashit( dirname( $file ) );
}

/**
 * Gets the URL directory path (with trailing slash) for the plugin __FILE__ passed in
 * @package WordPress
 * @subpackage Plugin
 * @since 2.8
 *
 * @param string $file The filename of the plugin (__FILE__)
 * @return string the URL path of the directory that contains the plugin
 */
function plugin_dir_url( $file ) {
	return trailingslashit( plugins_url( '', $file ) );
}

/**
 * Set the activation hook for a plugin.
 *
 * When a plugin is activated, the action 'activate_PLUGINNAME' hook is
 * activated. In the name of this hook, PLUGINNAME is replaced with the name of
 * the plugin, including the optional subdirectory. For example, when the plugin
 * is located in wp-content/plugin/sampleplugin/sample.php, then the name of
 * this hook will become 'activate_sampleplugin/sample.php'. When the plugin
 * consists of only one file and is (as by default) located at
 * wp-content/plugin/sample.php the name of this hook will be
 * 'activate_sample.php'.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 2.0
 *
 * @param string $file The filename of the plugin including the path.
 * @param callback $function the function hooked to the 'activate_PLUGIN' action.
 */
function register_activation_hook($file, $function) {
	$file = plugin_basename($file);
	add_action('activate_' . $file, $function);
}

/**
 * Set the deactivation hook for a plugin.
 *
 * When a plugin is deactivated, the action 'deactivate_PLUGINNAME' hook is
 * deactivated. In the name of this hook, PLUGINNAME is replaced with the name
 * of the plugin, including the optional subdirectory. For example, when the
 * plugin is located in wp-content/plugin/sampleplugin/sample.php, then
 * the name of this hook will become 'activate_sampleplugin/sample.php'.
 *
 * When the plugin consists of only one file and is (as by default) located at
 * wp-content/plugin/sample.php the name of this hook will be
 * 'activate_sample.php'.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 2.0
 *
 * @param string $file The filename of the plugin including the path.
 * @param callback $function the function hooked to the 'activate_PLUGIN' action.
 */
function register_deactivation_hook($file, $function) {
	$file = plugin_basename($file);
	add_action('deactivate_' . $file, $function);
}

/**
 * Set the uninstallation hook for a plugin.
 *
 * Registers the uninstall hook that will be called when the user clicks on the
 * uninstall link that calls for the plugin to uninstall itself. The link won't
 * be active unless the plugin hooks into the action.
 *
 * The plugin should not run arbitrary code outside of functions, when
 * registering the uninstall hook. In order to run using the hook, the plugin
 * will have to be included, which means that any code laying outside of a
 * function will be run during the uninstall process. The plugin should not
 * hinder the uninstall process.
 *
 * If the plugin can not be written without running code within the plugin, then
 * the plugin should create a file named 'uninstall.php' in the base plugin
 * folder. This file will be called, if it exists, during the uninstall process
 * bypassing the uninstall hook. The plugin, when using the 'uninstall.php'
 * should always check for the 'WP_UNINSTALL_PLUGIN' constant, before
 * executing.
 *
 * @since 2.7
 *
 * @param string $file
 * @param callback $callback The callback to run when the hook is called.
 */
function register_uninstall_hook($file, $callback) {
	// The option should not be autoloaded, because it is not needed in most
	// cases. Emphasis should be put on using the 'uninstall.php' way of
	// uninstalling the plugin.
	$uninstallable_plugins = (array) get_option('uninstall_plugins');
	$uninstallable_plugins[plugin_basename($file)] = $callback;
	update_option('uninstall_plugins', $uninstallable_plugins);
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
 * @package WordPress
 * @subpackage Plugin
 * @since 2.5
 * @access private
 *
 * @uses $wp_filter Used to process all of the functions in the 'all' hook
 *
 * @param array $args The collected parameters from the hook that was called.
 * @param string $hook Optional. The hook name that was used to call the 'all' hook.
 */
function _wp_call_all_hook($args) {
	global $wp_filter;

	reset( $wp_filter['all'] );
	do {
		foreach( (array) current($wp_filter['all']) as $the_ )
			if ( !is_null($the_['function']) )
				call_user_func_array($the_['function'], $args);

	} while ( next($wp_filter['all']) !== false );
}

/**
 * Build Unique ID for storage and retrieval.
 *
 * The old way to serialize the callback caused issues and this function is the
 * solution. It works by checking for objects and creating an a new property in
 * the class to keep track of the object and new objects of the same class that
 * need to be added.
 *
 * It also allows for the removal of actions and filters for objects after they
 * change class properties. It is possible to include the property $wp_filter_id
 * in your class and set it to "null" or a number to bypass the workaround.
 * However this will prevent you from adding new classes and any new classes
 * will overwrite the previous hook by the same class.
 *
 * Functions and static method callbacks are just returned as strings and
 * shouldn't have any speed penalty.
 *
 * @package WordPress
 * @subpackage Plugin
 * @access private
 * @since 2.2.3
 * @link http://trac.wordpress.org/ticket/3875
 *
 * @global array $wp_filter Storage for all of the filters and actions
 * @param string $tag Used in counting how many hooks were applied
 * @param callback $function Used for creating unique id
 * @param int|bool $priority Used in counting how many hooks were applied.  If === false and $function is an object reference, we return the unique id only if it already has one, false otherwise.
 * @param string $type filter or action
 * @return string|bool Unique ID for usage as array key or false if $priority === false and $function is an object reference, and it does not already have a uniqe id.
 */
function _wp_filter_build_unique_id($tag, $function, $priority) {
	global $wp_filter;
	static $filter_id_count = 0;

	if ( is_string($function) ) {
		return $function;
	} else if (is_object($function[0]) ) {
		// Object Class Calling
		if ( function_exists('spl_object_hash') ) {
			return spl_object_hash($function[0]) . $function[1];
		} else {
			$obj_idx = get_class($function[0]).$function[1];
			if ( !isset($function[0]->wp_filter_id) ) {
				if ( false === $priority )
					return false;
				$obj_idx .= isset($wp_filter[$tag][$priority]) ? count((array)$wp_filter[$tag][$priority]) : $filter_id_count;
				$function[0]->wp_filter_id = $filter_id_count;
				++$filter_id_count;
			} else {
				$obj_idx .= $function[0]->wp_filter_id;
			}
	
			return $obj_idx;
		}
	} else if ( is_string($function[0]) ) {
		// Static Calling
		return $function[0].$function[1];
	}
}

?>
