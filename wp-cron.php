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

// Uncached doing_cron transient fetch
function _get_cron_lock() {
	global $_wp_using_ext_object_cache, $wpdb;

	$value = 0;
	if ( $_wp_using_ext_object_cache ) {
		// Skip local cache and force refetch of doing_cron transient in case
		// another processs updated the cache
		$value = wp_cache_get( 'doing_cron', 'transient', true );
	} else {
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", '_transient_doing_cron' ) );
		if ( is_object( $row ) )
			$value = $row->option_value;
	}

	return $value;
}

if ( false === $crons = _get_cron_array() )
	die();

$keys = array_keys( $crons );
$local_time = microtime( true );

if ( isset($keys[0]) && $keys[0] > $local_time )
	die();

$doing_cron_transient = get_transient( 'doing_cron');

// Use global $doing_wp_cron lock otherwise use the GET lock. If no lock, trying grabbing a new lock.
if ( empty( $doing_wp_cron ) ) {
	if ( empty( $_GET[ 'doing_wp_cron' ] ) ) {
		// Called from external script/job. Try setting a lock.
		if ( $doing_cron_transient && ( $doing_cron_transient + WP_CRON_LOCK_TIMEOUT > $local_time ) )
			return;
		$doing_cron_transient = $doing_wp_cron = sprintf( '%.22F', microtime( true ) );
		set_transient( 'doing_cron', $doing_wp_cron );
	} else {
		$doing_wp_cron = $_GET[ 'doing_wp_cron' ];
	}
}

// Check lock
if ( $doing_cron_transient != $doing_wp_cron )
	return;

foreach ( $crons as $timestamp => $cronhooks ) {
	if ( $timestamp > $local_time )
		break;

	foreach ( $cronhooks as $hook => $keys ) {

		foreach ( $keys as $k => $v ) {

			$schedule = $v['schedule'];

			if ( $schedule != false ) {
				$new_args = array($timestamp, $schedule, $hook, $v['args']);
				call_user_func_array('wp_reschedule_event', $new_args);
			}

			wp_unschedule_event( $timestamp, $hook, $v['args'] );

 			do_action_ref_array( $hook, $v['args'] );

			// If the hook ran too long and another cron process stole the lock, quit.
			if ( _get_cron_lock() != $doing_wp_cron )
				return;
		}
	}
}

if ( _get_cron_lock() == $doing_wp_cron )
	delete_transient( 'doing_cron' );

die();
