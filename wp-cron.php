<?php
/**
 * WordPress Cron Implementation for hosts, which do not offer CRON or for which
 * the user has not setup a CRON job pointing to this file.
 *
 * The HTTP request to this file will not slow down the visitor who happens to
 * visit when the cron job is needed to run.
 *
 * @package WordPress
 */

ignore_user_abort(true);

/**
 * Tell WordPress we are doing the CRON task.
 *
 * @var bool
 */
define('DOING_CRON', true);
/** Setup WordPress environment */
require_once('./wp-load.php');

if ( $_GET['check'] != wp_hash('187425') )
	exit;

if ( get_option('doing_cron') > time() )
	exit;

update_option('doing_cron', time() + 30);

$crons = _get_cron_array();
$keys = array_keys($crons);
if (!is_array($crons) || $keys[0] > time())
	return;

foreach ($crons as $timestamp => $cronhooks) {
	if ($timestamp > time()) break;
	foreach ($cronhooks as $hook => $keys) {
		foreach ($keys as $key => $args) {
			$schedule = $args['schedule'];
			if ($schedule != false) {
				$new_args = array($timestamp, $schedule, $hook, $args['args']);
				call_user_func_array('wp_reschedule_event', $new_args);
			}
			wp_unschedule_event($timestamp, $hook, $args['args']);
 			do_action_ref_array($hook, $args['args']);
		}
	}
}

update_option('doing_cron', 0);

?>