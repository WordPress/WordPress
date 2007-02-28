<?php

//
// Filter functions, the core of the WP plugin architecture.
//

function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	global $wp_filter;

	// So the format is wp_filter['tag']['array of priorities']['array of functions serialized']['array of ['array (functions, accepted_args)]']
	$wp_filter[$tag][$priority][serialize($function_to_add)] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
	return true;
}

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

function merge_filters($tag) {
	global $wp_filter;

	if ( isset($wp_filter['all']) )
		$wp_filter[$tag] = array_merge($wp_filter['all'], (array) $wp_filter[$tag]);

	if ( isset($wp_filter[$tag]) ){
		reset($wp_filter[$tag]);
		uksort($wp_filter[$tag], "strnatcasecmp");
	}
}

function remove_filter($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
	global $wp_filter;

	unset($GLOBALS['wp_filter'][$tag][$priority][serialize($function_to_remove)]);

	return true;
}

//
// Action functions
//

function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	add_filter($tag, $function_to_add, $priority, $accepted_args);
}

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

// Returns the number of times an action has been done
function did_action($tag) {
	global $wp_actions;

	return count(array_keys($wp_actions, $tag));
}

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

function remove_action($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
	remove_filter($tag, $function_to_remove, $priority, $accepted_args);
}

//
// Functions for handling plugins.
//

function plugin_basename($file) {
	$file = preg_replace('|\\\\+|', '\\\\', $file);
	$file = preg_replace('/^.*wp-content[\\\\\/]plugins[\\\\\/]/', '', $file);
	return $file;
}

function register_activation_hook($file, $function) {
	$file = plugin_basename($file);
	add_action('activate_' . $file, $function);
}

function register_deactivation_hook($file, $function) {
	$file = plugin_basename($file);
	add_action('deactivate_' . $file, $function);
}

?>