<?php

function wp_schedule_single_event( $timestamp, $hook, $args = array()) {
	$crons = _get_cron_array();
	$key = md5(serialize($args));
	$crons[$timestamp][$hook][$key] = array( 'schedule' => false, 'args' => $args );
	uksort( $crons, "strnatcasecmp" );
	_set_cron_array( $crons );
}

function wp_schedule_event( $timestamp, $recurrence, $hook, $args = array()) {
	$crons = _get_cron_array();
	$schedules = wp_get_schedules();
	$key = md5(serialize($args));
	if ( !isset( $schedules[$recurrence] ) )
		return false;
	$crons[$timestamp][$hook][$key] = array( 'schedule' => $recurrence, 'args' => $args, 'interval' => $schedules[$recurrence]['interval'] );
	uksort( $crons, "strnatcasecmp" );
	_set_cron_array( $crons );
}

function wp_reschedule_event( $timestamp, $recurrence, $hook, $args = array()) {
	$crons = _get_cron_array();
	$schedules = wp_get_schedules();
	$key = md5(serialize($args));
	$interval = 0;

	// First we try to get it from the schedule
	if ( 0 == $interval )
		$interval = $schedules[$recurrence]['interval'];
	// Now we try to get it from the saved interval in case the schedule disappears
	if ( 0 == $interval )
		$interval = $crons[$timestamp][$hook][$key]['interval'];
	// Now we assume something is wrong and fail to schedule
	if ( 0 == $interval )
		return false;

	while ( $timestamp < time() + 1 )
		$timestamp += $interval;

	wp_schedule_event( $timestamp, $recurrence, $hook, $args );
}

function wp_unschedule_event( $timestamp, $hook, $args = array() ) {
	$crons = _get_cron_array();
	$key = md5(serialize($args));
	unset( $crons[$timestamp][$hook][$key] );
	if ( empty($crons[$timestamp][$hook]) )
		unset( $crons[$timestamp][$hook] );
	if ( empty($crons[$timestamp]) )
		unset( $crons[$timestamp] );
	_set_cron_array( $crons );
}

function wp_clear_scheduled_hook( $hook ) {
	$args = array_slice( func_get_args(), 1 );

	while ( $timestamp = wp_next_scheduled( $hook, $args ) )
		wp_unschedule_event( $timestamp, $hook, $args );
}

function wp_next_scheduled( $hook, $args = array() ) {
	$crons = _get_cron_array();
	$key = md5(serialize($args));
	if ( empty($crons) )
		return false;
	foreach ( $crons as $timestamp => $cron ) {
		if ( isset( $cron[$hook][$key] ) )
			return $timestamp;
	}
	return false;
}

function spawn_cron() {
	$crons = _get_cron_array();

	if ( !is_array($crons) )
		return;

	$keys = array_keys( $crons );
	if ( array_shift( $keys ) > time() )
		return;

	$cron_url = get_option( 'siteurl' ) . '/wp-cron.php';
	$parts = parse_url( $cron_url );
	
	if ($parts['scheme'] == 'https') {
		// support for SSL was added in 4.3.0
		if (version_compare(phpversion(), '4.3.0', '>=') && function_exists('openssl_open')) {
			$argyle = @fsockopen('ssl://' . $parts['host'], $_SERVER['SERVER_PORT'], $errno, $errstr, 0.01);
		} else {
			return false;
		}
	} else {
		$argyle = @ fsockopen( $parts['host'], $_SERVER['SERVER_PORT'], $errno, $errstr, 0.01 );
	}
	
	if ( $argyle )
		fputs( $argyle,
			  "GET {$parts['path']}?check=" . wp_hash('187425') . " HTTP/1.0\r\n"
			. "Host: {$_SERVER['HTTP_HOST']}\r\n\r\n"
		);
}

function wp_cron() {
	// Prevent infinite loops caused by lack of wp-cron.php
	if ( strpos($_SERVER['REQUEST_URI'], '/wp-cron.php') !== false )
		return;

	$crons = _get_cron_array();

	if ( !is_array($crons) )
		return;

	$keys = array_keys( $crons );
	if ( isset($keys[0]) && $keys[0] > time() )
		return;

	$schedules = wp_get_schedules();
	foreach ( $crons as $timestamp => $cronhooks ) {
		if ( $timestamp > time() ) break;
		foreach ( $cronhooks as $hook => $args ) {
			if ( isset($schedules[$hook]['callback']) && !call_user_func( $schedules[$hook]['callback'] ) )
				continue;
			spawn_cron();
			break 2;
		}
	}
}

function wp_get_schedules() {
	$schedules = array(
		'hourly' => array( 'interval' => 3600, 'display' => __('Once Hourly') ),
		'daily' => array( 'interval' => 86400, 'display' => __('Once Daily') ),
	);
	return array_merge( apply_filters( 'cron_schedules', array() ), $schedules );
}

function wp_get_schedule($hook, $args = array()) {
	$crons = _get_cron_array();
	$key = md5(serialize($args));
	if ( empty($crons) )
		return false;
	foreach ( $crons as $timestamp => $cron ) {
		if ( isset( $cron[$hook][$key] ) )
			return $cron[$hook][$key]['schedule'];
	}
	return false;
}

//
// Private functions
//

function _get_cron_array()  {
	$cron = get_option('cron');
	if ( ! is_array($cron) )
		return false;

	if ( !isset($cron['version']) )
		$cron = _upgrade_cron_array($cron);

	unset($cron['version']);

	return $cron;
}

function _set_cron_array($cron) {
	$cron['version'] = 2;
	update_option( 'cron', $cron );
}

function _upgrade_cron_array($cron) {
	if ( isset($cron['version']) && 2 == $cron['version'])
		return $cron;

	$new_cron = array();

	foreach ($cron as $timestamp => $hooks) {
		foreach ( $hooks as $hook => $args ) {
			$key = md5(serialize($args['args']));
			$new_cron[$timestamp][$hook][$key] = $args;
		}
	}

	$new_cron['version'] = 2;
	update_option( 'cron', $new_cron );
	return $new_cron;
}

?>
