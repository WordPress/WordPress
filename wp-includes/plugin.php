<?php
/**
 * The plugin API is located in this file, which allows for creating actions
 * and filters and hooking functions, and methods. The functions or methods will
 * then be run when the action or filter is called.
 *
 * The API callback examples reference functions, but can be methods of classes.
 * To hook methods, you'll need to pass an array one of two ways.
 *
 * For static methods (you won't have access to the <tt>$this</tt> variable in the
 * method):
 * <code>array('class_name', 'method_name');</code>
 *
 * The second method will need the reference to the object to have access to the
 * method.
 * <code>array(&$this, 'method_name');</code>
 * <code>
 * $obj = new myObject();
 * array(&$obj, 'method_name');
 * </code>
 * Any of the syntaxes explained in the PHP documentation for the
 * {@link http://us2.php.net/manual/en/language.pseudo-types.php#language.types.callback 'callback' type} are valid.
 *
 * Also see the {@link http://codex.wordpress.org/Plugin_API Plugin API} for more information
 * and examples on how to use a lot of these functions.
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
 * To use the API, the following code should be used to bind a callback to the filter
 * <code>
 * function example_hook($example) { echo $example; }
 *
 * add_filter('example_filter', 'example_hook');
 * </code>
 *
 * In WordPress 1.5.1+, hooked functions can take extra arguments that are set when
 * the matching do_action() or apply_filters() call is run. The <tt>$accepted_args
 * allow for calling functions only when the number of args match. Hooked functions
 * can take extra arguments that are set when the matching <tt>do_action()</tt> or
 * <tt>apply_filters()</tt> call is run. For example, the action <tt>comment_id_not_found</tt>
 * will pass any functions that hook onto it the ID of the requested comment.
 *
 * <strong>Note:</strong> the function will return true no matter if the function was hooked
 * fails or not. There are no checks for whether the function exists beforehand and no checks
 * to whether the <tt>$function_to_add is even a string. It is up to you to take care and
 * this is done for optimization purposes, so everything is as quick as possible.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.5
 * @global array $wp_filter Stores all of the filters added in the form of
 *	wp_filter['tag']['array of priorities']['array of functions serialized']['array of ['array (functions, accepted_args)]']
 * @global array $merged_filters Tracks the tags that need to be merged for later. If the hook is added, it doesn't need to run through that process.
 *
 * @param string $tag The name of the filter to hook the <tt>$function_to_add</tt> to.
 * @param callback $function_to_add The name of the function to be called when the filter is applied.
 * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
 * @param int $accepted_args optional. The number of arguments the function accept (default 1).
 * @return boolean true
 */
function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	global $wp_filter, $merged_filters;

	$idx = _wp_filter_build_unique_id($tag, $function_to_add, $priority, 'filter');
	$wp_filter[$tag][$priority][$idx] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
	unset( $merged_filters[ $tag ] );
	return true;
}

/**
 * Call the functions added to a filter hook.
 *
 * The callback functions attached to filter hook <tt>$tag</tt> are invoked by
 * calling this function. This function can be used to create a new filter hook
 * by simply calling this function with the name of the new hook specified using
 * the <tt>$tag</a> parameter.
 *
 * The function allows for additional arguments to be added and passed to hooks.
 * <code>
 * function example_hook($string, $arg1, $arg2)
 * {
 *		//Do stuff
 * }
 * $value = apply_filters('example_filter', 'filter me', 'arg1', 'arg2');
 * </code>
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.5
 * @global array $wp_filter Stores all of the filters
 * @global array $merge_filters Merges the filter hooks using this function.
 *
 * @param string $tag The name of the filter hook.
 * @param string $value The value on which the filters hooked to <tt>$tag</tt> are applied on.
 * @param mixed $var,... Additional variables passed to the functions hooked to <tt>$tag</tt>.
 * @return string The text in <tt>$string</tt> after all hooked functions are applied to it.
 */
function apply_filters($tag, $value) {
	global $wp_filter, $merged_filters, $wp_current_filter;

	@$wp_current_filter[] = $tag;

	// Do 'all' actions first
	if ( isset($wp_filter['all']) ) {
		reset( $wp_filter['all'] );
		do {
			foreach ( (array) current($wp_filter['all']) as $the_ )
				if ( !is_null($the_['function']) )
					$value = call_user_func_array($the_['function'], $value);

		} while ( next($wp_filter['all']) !== false );
	}

	if ( !isset($wp_filter[$tag]) ) {
		array_pop($wp_current_filter);
		return $value;
	}

	// Sort
	if ( !isset( $merged_filters[ $tag ] ) ) {
		reset($wp_filter[$tag]);
		uksort($wp_filter[$tag], "strnatcasecmp");
		$merged_filters[ $tag ] = true;
	}

	reset( $wp_filter[ $tag ] );

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
 * To remove a hook, the <tt>$function_to_remove</tt> and <tt>$priority</tt> arguments
 * must match when the hook was added. This goes for both filters and actions. No warning
 * will be given on removal failure.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.5
 *
 * @param string $tag The filter hook to which the function to be removed is hooked.
 * @param callback $function_to_remove The name of the function which should be removed.
 * @param int $priority optional. The priority of the function (default: 10).
 * @param int $accepted_args optional. The number of arguments the function accpets (default: 1).
 * @return boolean Whether the function existed before it was removed.
 */
function remove_filter($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
	$function_to_remove = _wp_filter_build_unique_id($tag, $function_to_remove, $priority, 'filter');

	$r = isset($GLOBALS['wp_filter'][$tag][$priority][$function_to_remove]);

	if ( true === $r) {
		unset($GLOBALS['wp_filter'][$tag][$priority][$function_to_remove]);
		unset($GLOBALS['merged_filters'][$tag]);
	}

	return $r;
}


/**
 * Return the name of the current filter or action.
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
 * @since 1.5
 *
 * @param string $tag The name of the action to which the <tt>$function_to-add</tt> is hooked.
 * @param callback $function_to_add The name of the function you wish to be called.
 * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
 * @param int $accepted_args optional. The number of arguments the function accept (default 1).
 */
function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	global $wp_action, $merged_actions;

	$idx = _wp_filter_build_unique_id($tag, $function_to_add, $priority, 'action');
	$wp_action[$tag][$priority][$idx] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
	unset( $merged_actions[ $tag ] );
	return true;
}


/**
 * Execute functions hooked on a specific action hook.
 *
 * This function invokes all functions attached to action hook <tt>$tag</tt>.
 * It is possible to create new action hooks by simply calling this function,
 * specifying the name of the new hook using the <tt>$tag</tt> parameter.
 *
 * You can pass extra arguments to the hooks, much like you can with apply_filters().
 *
 * @see apply_filters() This function works similar with the exception that nothing is
 * returned and only the functions or methods are called.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.5
 * @global array $wp_filter Stores all of the filters
 * @global array $wp_actions Increments the amount of times action was triggered.
 *
 * @param string $tag The name of the action to be executed.
 * @param mixed $arg,... Optional additional arguments which are passed on to the functions hooked to the action.
 * @return null Will return null if $tag does not exist in $wp_filter array
 */
function do_action($tag, $arg = '') {
	global $wp_action, $wp_actions, $wp_current_filter;

	if ( is_array($wp_actions) )
		$wp_actions[] = $tag;
	else
		$wp_actions = array($tag);

	$args = array();
	if ( is_array($arg) && 1 == count($arg) && is_object($arg[0]) ) // array(&$this)
		$args[] =& $arg[0];
	else
		$args[] = $arg;
	for ( $a = 2; $a < func_num_args(); $a++ )
		$args[] = func_get_arg($a);

	@$wp_current_filter[] = $tag;

	// Do 'all' actions first
	if ( isset($wp_action['all']) ) {
		do {
			foreach( (array) current($wp_action['all']) as $the_ )
				if ( !is_null($the_['function']) )
					call_user_func_array($the_['function'], $args[0]);

		} while ( next($wp_action['all']) !== false );
	}

	if ( !isset($wp_action[$tag]) ) {
		array_pop($wp_current_filter);
		return;
	}
		
	// Sort
	if ( !isset( $merged_actions[ $tag ] ) ) {
		reset($wp_action[$tag]);
		uksort($wp_action[$tag], "strnatcasecmp");
		$merged_actions[ $tag ] = true;
	}

	reset( $wp_action[ $tag ] );

	do {
		foreach ( (array) current($wp_action[$tag]) as $the_ )
			if ( !is_null($the_['function']) )
				call_user_func_array($the_['function'], array_slice($args, 0, (int) $the_['accepted_args']));

	} while ( next($wp_action[$tag]) !== false );

		array_pop($wp_current_filter);
}

/**
 * Return the number times an action is fired.
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

	if ( empty($wp_actions) )
		return 0;

	return count(array_keys($wp_actions, $tag));
}

/**
 * Execute functions hooked on a specific action hook, specifying arguments in an array.
 *
 * @see do_action() This function is identical, but the arguments passed to
 * the functions hooked to <tt>$tag</tt> are supplied using an array.
 *
 * @uses merge_filters()
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
	global $wp_action, $wp_actions;

	if ( !is_array($wp_actions) )
		$wp_actions = array($tag);
	else
		$wp_actions[] = $tag;

	// Do 'all' actions first
	if ( isset($wp_action['all']) ) {
		do {
			foreach( (array) current($wp_action['all']) as $the_ )
				if ( !is_null($the_['function']) )
					call_user_func_array($the_['function'], $args[0]);

		} while ( next($wp_action['all']) !== false );
	}

	if ( !isset($wp_action[$tag]) )
		return;

	// Sort
	if ( !isset( $merged_actions[ $tag ] ) ) {
		reset($wp_action[$tag]);
		uksort($wp_action[$tag], "strnatcasecmp");
		$merged_actions[ $tag ] = true;
	}

	reset( $wp_action[ $tag ] );

	do {
		foreach( (array) current($wp_action[$tag]) as $the_ )
			if ( !is_null($the_['function']) )
				call_user_func_array($the_['function'], array_slice($args, 0, (int) $the_['accepted_args']));

	} while ( next($wp_action[$tag]) !== false );

}

/**
 * Removes a function from a specified action hook.
 *
 * This function removes a function attached to a specified action hook. This
 * method can be used to remove default functions attached to a specific filter
 * hook and possibly replace them with a substitute.
 *
 * @uses remove_filter() Uses remove_filter to remove actions added.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.5
 *
 * @param string $tag The action hook to which the function to be removed is hooked.
 * @param callback $function_to_remove The name of the function which should be removed.
 * @param int $priority optional The priority of the function (default: 10).
 * @param int $accepted_args optional. The number of arguments the function accpets (default: 1).
 * @return boolean Whether the function is removed.
 */
function remove_action($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
	$function_to_remove = _wp_filter_build_unique_id($tag, $function_to_remove, $priority, 'action');

	$r = isset($GLOBALS['wp_action'][$tag][$priority][$function_to_remove]);

	if ( true === $r) {
		unset($GLOBALS['wp_action'][$tag][$priority][$function_to_remove]);
		unset($GLOBALS['merged_actions'][$tag]);
	}

	return $r;
}

//
// Functions for handling plugins.
//

/**
 * Gets the basename of a plugin.
 *
 * This method extract the name of a plugin from its filename.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.5
 *
 * @access private
 *
 * @param string $file The filename of plugin.
 * @return string The name of a plugin.
 */
function plugin_basename($file) {
	$file = str_replace('\\','/',$file); // sanitize for Win32 installs
	$file = preg_replace('|/+|','/', $file); // remove any duplicate slash
	$file = preg_replace('|^.*/wp-content/plugins/|','',$file); // get relative path from plugins dir
	return $file;
}

/**
 * Hook a function on a plugin activation action hook.
 *
 * When a plugin is activated, the action 'activate_PLUGINNAME' hook is
 * activated. In the name of this hook, PLUGINNAME is replaced with the name of
 * the plugin, including the optional subdirectory. For example, when the plugin
 * is located in <tt>wp-content/plugin/sampleplugin/sample.php</tt>, then the
 * name of this hook will become 'activate_sampleplugin/sample.php'
 * When the plugin consists of only one file and is (as by default) located at
 * <tt>wp-content/plugin/sample.php</tt> the name of this hook will be
 * 'activate_sample.php'.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 1.5
 *
 * @access private
 *
 * @param string $file The filename of the plugin including the path.
 * @param string $function the function hooked to the 'activate_PLUGIN' action.
 */
function register_activation_hook($file, $function) {
	$file = plugin_basename($file);
	add_action('activate_' . $file, $function);
}

/**
 * Hook a function on a plugin deactivation action hook.
 *
 * When a plugin is deactivated, the action 'deactivate_PLUGINNAME' hook is
 * deactivated. In the name of this hook, PLUGINNAME is replaced with the name of
 * the plugin, including the optional subdirectory. For example, when the plugin
 * is located in <tt>wp-content/plugin/sampleplugin/sample.php</tt>, then the
 * name of this hook will become 'activate_sampleplugin/sample.php'.
 * When the plugin consists of only one file and is (as by default) located at
 * <tt>wp-content/plugin/sample.php</tt> the name of this hook will be
 * 'activate_sample.php'.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 2.0
 *
 * @access private
 *
 * @param string $file The filename of the plugin including the path.
 * @param string $function the function hooked to the 'activate_PLUGIN' action.
 */
function register_deactivation_hook($file, $function) {
	$file = plugin_basename($file);
	add_action('deactivate_' . $file, $function);
}

/**
 * Build Unique ID for storage and retrieval
 *
 * The old way to serialize the callback caused issues and this function is the
 * solution. It works by checking for objects and creating an a new property in
 * the class to keep track of the object and new objects of the same class that
 * need to be added.
 *
 * It also allows for the removal of actions and filters for objects after they
 * change class properties. It is possible to include the property $wp_filter_id
 * in your class and set it to "null" or a number to bypass the workaround. However
 * this will prevent you from adding new classes and any new classes will overwrite
 * the previous hook by the same class.
 *
 * Functions and static method callbacks are just returned as strings and shouldn't
 * have any speed penalty.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 2.2.3
 *
 * @link http://trac.wordpress.org/ticket/3875
 *
 * @access private
 *
 * @global array $wp_filter Storage for all of the filters and actions
 * @param string $tag Used in counting how many hooks were applied
 * @param string|array $function Used for creating unique id
 * @param int $priority Used in counting how many hooks were applied
 * @param string $type filter or action
 * @return string Unique ID for usage as array key
 */
function _wp_filter_build_unique_id($tag, $function, $priority, $type)
{
	global $wp_filter, $wp_action;

	// If function then just skip all of the tests and not overwrite the following.
	if ( is_string($function) )
		return $function;
	// Object Class Calling
	else if (is_object($function[0]) ) {
		$obj_idx = get_class($function[0]).$function[1];
		if ( !isset($function[0]->wp_filter_id) ) {
			if ( 'filter' == $type )
				$count = count((array)$wp_filter[$tag][$priority]);
			else
				$count = count((array)$wp_action[$tag][$priority]);
			$function[0]->wp_filter_id = $count;
			$obj_idx .= $count;
			unset($count);
		} else
			$obj_idx .= $function[0]->wp_filter_id;
		return $obj_idx;
	}
	// Static Calling
	else if ( is_string($function[0]) )
		return $function[0].$function[1];
}

?>
