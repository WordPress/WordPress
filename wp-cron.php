<?php
/**
 * WordPress Cron Implementation for hosts, which do not offer CRON or for which
 * the user has not set up a CRON job pointing to this file.
 *
 * The HTTP request to this file will not slow down the visitor who happens to
 * visit when the cron job is needed to run.
 *
 * @package WordPress
 */

ignore_user_abort(true);

if ( !empty($_POST) || defined('DOING_AJAX') || defined('DOING_CRON') )
	die();

/**
 * Tell WordPress we are doing the CRON task.
 *
 * @var bool
 */
define('DOING_CRON', true);

if ( !defined('ABSPATH') ) {
	/** Set up WordPress environment */
	require_once('./wp-load.php');
}

if ( false === $crons = _get_cron_array() )
	die();

$keys = array_keys( $crons );
$local_time = time();

if ( isset($keys[0]) && $keys[0] > $local_time )
	die();

foreach ($crons as $timestamp => $cronhooks) {
	if ( $timestamp > $local_time )
		break;

	foreach ($cronhooks as $hook => $keys) {

		foreach ($keys as $k => $v) {

			$schedule = $v['schedule'];

			if ($schedule != false) {
				$new_args = array($timestamp, $schedule, $hook, $v['args']);
				call_user_func_array('wp_reschedule_event', $new_args);
			}

			wp_unschedule_event($timestamp, $hook, $v['args']);

 			do_action_ref_array($hook, $v['args']);
		}
	}
}

die();
