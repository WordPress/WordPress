<?php
function wp_schedule_single_event($timestamp, $hook) {
	$args = array_slice(func_get_args(), 2);
	$crons = get_option('cron');
	$crons[$timestamp][$hook] = array('schedule' => false, 'args' => $args);
	ksort($crons);
	update_option('cron', $crons);
}
function wp_schedule_event($timestamp, $recurrence, $hook) {
	$args = array_slice(func_get_args(), 3);
	$crons = get_option('cron');
	$schedules = wp_get_schedules();
	if(!isset($schedules[$recurrence]))
		return false;
	$crons[$timestamp][$hook] = array('schedule' => $recurrence, 'args' => $args, 'interval' => $schedules[$recurrence]['interval']);
	ksort($crons);
	update_option('cron', $crons);
}


function wp_reschedule_event($timestamp, $recurrence, $hook) {
	$args = array_slice(func_get_args(), 3);
	$crons = get_option('cron');
	$schedules = wp_get_schedules();
	$interval = 0;
	
	// First we try to get it from the schedule
	if( 0 == $interval )
		$interval = $schedules[$recurrence]['interval'];
	// Now we try to get it from the saved interval in case the schedule disappears
	if( 0 == $interval )
		$interval = $crons[$timestamp][$hook]['interval'];
	// Now we assume something is wrong and fail to schedule
	if( 0 == $interval )
		return false;

	while($timestamp < time() + 1) {
		$timestamp += $interval;
	}
	wp_schedule_event($timestamp, $recurrence, $hook);
}

function wp_unschedule_event($timestamp, $hook) {
	$crons = get_option('cron');
	unset($crons[$timestamp][$hook]);
	if ( empty($crons[$timestamp]) )
		unset($crons[$timestamp]);
	update_option('cron', $crons);
}

function wp_clear_scheduled_hook($hook) {
	while($timestamp = next_scheduled($hook))
		wp_unschedule_event($timestamp, $hook);
}

function wp_next_scheduled($hook) {
	$crons = get_option('cron');
	if ( empty($crons) )
		return false;
	foreach($crons as $timestamp => $cron) {
		//if($timestamp <= time()) continue;
		if(isset($cron[$hook])) return $timestamp;
	}
	return false;
}

function spawn_cron() {
	if (array_shift(array_keys(get_option('cron'))) > time()) return;
	
	$cron_url = get_settings('siteurl') . '/wp-cron.php';
	$parts = parse_url($cron_url);
	
	$argyle = @ fsockopen($parts['host'], $_SERVER['SERVER_PORT'], $errno, $errstr, 0.01);
	if ( $argyle )
		fputs($argyle, "GET {$parts['path']}?time=" . time() . '&check='
		. md5(DB_PASS . '187425') . " HTTP/1.0\r\nHost: {$_SERVER['HTTP_HOST']}\r\n\r\n");
}

function wp_cron() {
	$crons = get_option('cron');
	if (!is_array($crons) || array_shift(array_keys($crons)) > time())
		return;

	$schedules = wp_get_schedules();
	foreach ($crons as $timestamp => $cronhooks) {
		if ($timestamp > time()) break;
		foreach($cronhooks as $hook => $args) {
			if(isset($schedules[$hook]['callback']) && !call_user_func($schedules[$hook]['callback']))
				continue;
			spawn_cron();
			break 2;
		}
	}
}

function wp_get_schedules() {
	$schedules = array(
		'hourly' => array('interval' => 3600, 'display' => __('Once Hourly')),
		'daily' => array('interval' => 86400, 'display' => __('Once Daily')),
	);
	return array_merge(apply_filters('cron_schedules', array()), $schedules);
}
?>