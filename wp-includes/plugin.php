<?php

/**
 * Hooks a function to a specific filter action.
 * 
 * Filters are the hooks that WordPress launches to modify text of various types
 * before adding it to the database or sending it to the browser screen. Plugins 
 * can specify that one or more of its PHP functions is executed to 
 * modify specific types of text at these times, using the Filter API.
 * See the [Plugin API] for a list of filter hooks. 
 *
 * @param string $tag The name of the filter to hook the <tt>$function_to_add</tt> to.
 * @param callback $function_to_add The name of the function to be called when the filter is applied.
 * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
 * @param int $accepted_args optional. The number of arguments the function accept (default 1). In WordPress 1.5.1+, hooked functions can take extra arguments that are set when the matching do_action() or apply_filters() call is run.
 * @return boolean true if the <tt>$function_to_add</tt> is added succesfully to filter <tt>$tag</tt>. How many arguments your function takes. In WordPress 1.5.1+, hooked functions can take extra arguments that are set when the matching <tt>do_action()</tt> or <tt>apply_filters()</tt> call is run. For example, the action <tt>comment_id_not_found</tt> will pass any functions that hook onto it the ID of the requested comment.
 */
function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	global $wp_filter;

	// So the format is wp_filter['tag']['array of priorities']['array of functions serialized']['array of ['array (functions, accepted_args)]']
	$wp_filter[$tag][$priority][serialize($function_to_add)] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
	return true;
}

/**
 * Call the functions added to a filter hook.
 * 
 * The callback functions attached to filter hook <tt>$tag</tt> are invoked by
 * calling this function. This function can be used to create a new filter hook
 * by simply calling this function with the name of the new hook specified using
 * the <tt>$tag</a> parameter.
 * @uses merge_filters Merges the filter hooks using this function.
 * @param string $tag The name of the filter hook.
 * @param string $string The text on which the filters hooked to <tt>$tag</tt> are applied on.
 * @param mixed $var,... Additional variables passed to the functions hooked to <tt>$tag</tt>.
 * @return string The text in <tt>$string</tt> after all hooked functions are applied to it.
 */
function apply_filters($tag, $string) {
	global $wp_filter;

	merge_filters($tag);

	if ( !isset($wp_filter[$tag]) )
		return $string;

	$args = func_get_args();

	do{
		foreach( (array) current($wp_filter[$tag]) as $the_ )
			if ( !is_null($the_['function']) ){
				$args[1] = $string;
				$string = call_user_func_array($the_['function'], array_slice($args, 1, (int) $the_['accepted_args']));
			}

	} while ( next($wp_filter[$tag]) );

	return $string;
}

/**
 * Merge the filter functions of a specific filter hook with generic filter functions.
 * 
 * It is possible to defined generic filter functions using the filter hook 
 * <em>all</e>. These functions are called for every filter tag. This function 
 * merges the functions attached to the <em>all</em> hook with the functions
 * of a specific hoook defined by <tt>$tag</tt>.
 * @param string $tag The filter hook of which the functions should be merged.
 */
function merge_filters($tag) {
	global $wp_filter;

	if ( isset($wp_filter['all']) )
		$wp_filter[$tag] = array_merge($wp_filter['all'], (array) $wp_filter[$tag]);

	if ( isset($wp_filter[$tag]) ){
		reset($wp_filter[$tag]);
		uksort($wp_filter[$tag], "strnatcasecmp");
	}
}

/**
 * Removes a function from a specified filter hook. 
 * 
 * This function removes a function attached to a specified filter hook. This 
 * method can be used to remove default functions attached to a specific filter 
 * hook and possibly replace them with a substitute.
 * @param string $tag The filter hook to which the function to be removed is hooked.
 * @param callback $function_to_remove The name of the function which should be removed.
 * @param int $priority optional. The priority of the function (default: 10).
 * @param int $accepted_args optional. The number of arguments the function accpets (default: 1).
 * @return boolean Whether the function is removed.
 */
function remove_filter($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
	global $wp_filter;

	unset($GLOBALS['wp_filter'][$tag][$priority][serialize($function_to_remove)]);

	return true;
}

/**
 * Hooks a function on to a specific action.
 * 
 * Actions are the hooks that the WordPress core launches at specific points 
 * during execution, or when specific events occur. Plugins can specify that
 * one or more of its PHP functions are executed at these points, using the 
 * Action API.
 * 
 * @param string $tag The name of the action to which the <tt>$function_to-add</tt> is hooked.
 * @param callback $function_to_add The name of the function you wish to be called. Note: any of the syntaxes explained in the PHP documentation for the 'callback' type (http://us2.php.net/manual/en/language.pseudo-types.php#language.types.callback) are valid.
 * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
 * @param int $accepted_args optional. The number of arguments the function accept (default 1). In WordPress 1.5.1+, hooked functions can take extra arguments that are set when the matching do_action() or apply_filters() call is run. 
 * @return boolean Always true.
 */
function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	add_filter($tag, $function_to_add, $priority, $accepted_args);
}

/**
 * Execute functions hooked on a specific action hook.
 * 
 * This function invokes all functions attached to action hook <tt>$tag</tt>.
 * It is possible to create new action hooks by simply calling this function,
 * specifying the name of the new hook using the <tt>$tag</tt> parameter.
 * @uses merge_filters
 * @param string $tag The name of the action to be executed.
 * @param mixed $arg,... Optional additional arguments which are passed on to the functions hooked to the action. 
 */
function do_action($tag, $arg = '') {
	global $wp_filter, $wp_actions;

	$args = array();
	if ( is_array($arg) && 1 == count($arg) && is_object($arg[0]) ) // array(&$this)
		$args[] =& $arg[0];
	else
		$args[] = $arg;
	for ( $a = 2; $a < func_num_args(); $a++ )
		$args[] = func_get_arg($a);

	merge_filters($tag);

	if ( !isset($wp_filter[$tag]) )
		return;

	do{
		foreach( (array) current($wp_filter[$tag]) as $the_ )
			if ( !is_null($the_['function']) )
				call_user_func_array($the_['function'], array_slice($args, 0, (int) $the_['accepted_args']));

	} while ( next($wp_filter[$tag]) );

	if ( is_array($wp_actions) )
		$wp_actions[] = $tag;
	else
		$wp_actions = array($tag);
}

/**
 * Return the number of functions hooked to a specific action hook.
 * @param string $tag The name of the action hook.
 * @return int The number of functions hooked to action hook <tt>$tag</tt>
 */
function did_action($tag) {
	global $wp_actions;

	return count(array_keys($wp_actions, $tag));
}

/**
 * Execute functions hooked on a specific action hook, specifying arguments in a array.
 * 
 * This function is identical to {@link do_action}, but the argumetns passe to 
 * the functions hooked to <tt>$tag</tt> are supplied using an array.
 * @param string $tag The name of the action to be executed.
 * @param array $args The arguments supplied to the functions hooked to <tt>$tag</tt>
 */
function do_action_ref_array($tag, $args) {
	global $wp_filter, $wp_actions;

	if ( !is_array($wp_actions) )
		$wp_actions = array($tag);
	else
		$wp_actions[] = $tag;

	merge_filters($tag);

	if ( !isset($wp_filter[$tag]) )
		return;

	do{
		foreach( (array) current($wp_filter[$tag]) as $the_ )
			if ( !is_null($the_['function']) )
				call_user_func_array($the_['function'], array_slice($args, 0, (int) $the_['accepted_args']));

	} while ( next($wp_filter[$tag]) );

}

/**
 * Removes a function from a specified action hook. 
 * 
 * This function removes a function attached to a specified action hook. This 
 * method can be used to remove default functions attached to a specific filter 
 * hook and possibly replace them with a substitute.
 * @param string $tag The action hook to which the function to be removed is hooked.
 * @param callback $function_to_remove The name of the function which should be removed.
 * @param int $priority optional The priority of the function (default: 10).
 * @param int $accepted_args optional. The number of arguments the function accpets (default: 1).
 * @return boolean Whether the function is removed.
 */
function remove_action($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
	remove_filter($tag, $function_to_remove, $priority, $accepted_args);
}

//
// Functions for handling plugins.
//

/**
 * Gets the basename of a plugin.
 * 
 * This method extract the name of a plugin from its filename.
 * @param string $file The filename of plugin.
 * @return string The name of a plugin.
 */
function plugin_basename($file) {
	$file = preg_replace('|\\\\+|', '\\\\', $file);
	$file = preg_replace('/^.*wp-content[\\\\\/]plugins[\\\\\/]/', '', $file);
	return $file;
}

/**
 * Hook a function on a plugin activation action hook.
 * 
 * When a plugin is activated, the action 'activate_PLUGINNAME' hook is
 * activated. In the name of this hook, PLUGINNAME is replaced with the name of
 * the plugin, including the optional subdirectory. For example, when the plugin
 * is located in <tt>wp-content/plugin/sampleplugin/sample.php</tt>, then the 
 * name of this hook will become 'activate_sampleplugin/sample.php'. 
 * When the plugin consists of only one file and is (as by default) located at 
 * <tt>wp-content/plugin/sample.php</tt> the name of this hook will be 
 * 'activate_sample.php'.
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
 * @param string $file The filename of the plugin including the path.
 * @param string $function the function hooked to the 'activate_PLUGIN' action.
 */
function register_deactivation_hook($file, $function) {
	$file = plugin_basename($file);
	add_action('deactivate_' . $file, $function);
}

?>