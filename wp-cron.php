<?php
ignore_user_abort(true);
define('DOING_CRON', TRUE);
require_once('wp-config.php');

if ( $_GET['check'] != md5(DB_PASS . '187425') )
	exit;

$crons = get_option('cron');
if (!is_array($crons) || array_shift(array_keys($crons)) > time())
	return;
foreach ($crons as $timestamp => $cronhooks) {
	if ($timestamp > time()) break;
	foreach($cronhooks as $hook => $args) {
		do_action($hook, $args['args']);
		$schedule = $args['schedule'];
		if($schedule != false) {
			$args = array_merge( array($timestamp, $schedule, $hook), $args['args']);
			call_user_func_array('wp_reschedule_event', $args);
		}
		wp_unschedule_event($timestamp, $hook);
	}
}
?>
