<?php

//
// Filter functions, the core of the WP plugin architecture.
//

function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	global $wp_filter;

	// check that we don't already have the same filter at the same priority
	if ( isset($wp_filter[$tag]["$priority"]) ) {
		foreach($wp_filter[$tag]["$priority"] as $filter) {
			// uncomment if we want to match function AND accepted_args
			// if ( $filter == array($function, $accepted_args) ) {
			if ( $filter['function'] == $function_to_add ) {
				return true;
			}
		}
	}

	// So the format is wp_filter['tag']['array of priorities']['array of ['array (functions, accepted_args)]']
	$wp_filter[$tag]["$priority"][] = array('function'=>$function_to_add, 'accepted_args'=>$accepted_args);
	return true;
}

function apply_filters($tag, $string) {
	global $wp_filter;

	$args = array();
	for ( $a = 2; $a < func_num_args(); $a++ )
		$args[] = func_get_arg($a);

	merge_filters($tag);

	if ( !isset($wp_filter[$tag]) ) {
		return $string;
	}
	foreach ($wp_filter[$tag] as $priority => $functions) {
		if ( !is_null($functions) ) {
			foreach($functions as $function) {

				$function_name = $function['function'];
				$accepted_args = $function['accepted_args'];

				$the_args = $args;
				array_unshift($the_args, $string);
				if ( $accepted_args > 0 )
					$the_args = array_slice($the_args, 0, $accepted_args);
				elseif ( $accepted_args == 0 )
					$the_args = NULL;

				$string = call_user_func_array($function_name, $the_args);
			}
		}
	}
	return $string;
}


function merge_filters($tag) {
	global $wp_filter;
	if ( isset($wp_filter['all']) ) {
		foreach ($wp_filter['all'] as $priority => $functions) {
			if ( isset($wp_filter[$tag][$priority]) )
				$wp_filter[$tag][$priority] = array_merge($wp_filter['all'][$priority], $wp_filter[$tag][$priority]);
			else
				$wp_filter[$tag][$priority] = array_merge($wp_filter['all'][$priority], array());
			$wp_filter[$tag][$priority] = array_unique($wp_filter[$tag][$priority]);
		}
	}

	if ( isset($wp_filter[$tag]) )
		ksort( $wp_filter[$tag] );
}



function remove_filter($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
	global $wp_filter;

	// rebuild the list of filters
	if ( isset($wp_filter[$tag]["$priority"]) ) {
		$new_function_list = array();
		foreach($wp_filter[$tag]["$priority"] as $filter) {
			if ( $filter['function'] != $function_to_remove ) {
				$new_function_list[] = $filter;
			}
		}
		$wp_filter[$tag]["$priority"] = $new_function_list;
	}
	return true;
}

//
// Action functions
//

function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	add_filter($tag, $function_to_add, $priority, $accepted_args);
}

function do_action($tag, $arg = '') {
	global $wp_filter;
	$args = array();
	if ( is_array($arg) && 1 == count($arg) && is_object($arg[0]) ) // array(&$this)
		$args[] =& $arg[0];
	else
		$args[] = $arg;
	for ( $a = 2; $a < func_num_args(); $a++ )
		$args[] = func_get_arg($a);

	merge_filters($tag);

	if ( !isset($wp_filter[$tag]) ) {
		return;
	}
	foreach ($wp_filter[$tag] as $priority => $functions) {
		if ( !is_null($functions) ) {
			foreach($functions as $function) {

				$function_name = $function['function'];
				$accepted_args = $function['accepted_args'];

				if ( $accepted_args > 0 )
					$the_args = array_slice($args, 0, $accepted_args);
				elseif ( $accepted_args == 0 )
					$the_args = NULL;
				else
					$the_args = $args;

				$string = call_user_func_array($function_name, $the_args);
			}
		}
	}
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
