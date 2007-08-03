<?php
ignore_user_abort(true);
define('DOING_CRON', TRUE);
require_once('./wp-config.php');

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
