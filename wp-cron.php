<?php
ignore_user_abort(true);
define('DOING_CRON', TRUE);
require_once('wp-config.php');

if ( $_GET['check'] != md5(DB_PASS . '187425') )
	exit;

$crons = _get_cron_array();
$keys = array_keys($crons);
if (!is_array($crons) || $keys[0] > time())
	return;
foreach ($crons as $timestamp => $cronhooks) {
	if ($timestamp > time()) break;
	foreach ($cronhooks as $hook => $keys) {
		foreach ($keys as $key => $args) {
 			do_action_ref_array($hook, $args['args']);
			$schedule = $args['schedule'];
			if ($schedule != false) {
				$new_args = array($timestamp, $schedule, $hook, $args['args']);
				call_user_func_array('wp_reschedule_event', $new_args);
			}
			wp_unschedule_event($timestamp, $hook, $args['args']);
		}
	}
}
?>
