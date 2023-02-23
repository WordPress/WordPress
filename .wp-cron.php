<?php
/**
 * A pseudo-cron daemon for scheduling WordPress tasks.
 *
 * WP-Cron is triggered when the site receives a visit. In the scenario
 * where a site may not receive enough visits to execute scheduled tasks
 * in a timely manner, this file can be called directly or via a server
 * cron daemon for X number of times.
 *
 * Defining DISABLE_WP_CRON as true and calling this file directly are
 * mutually exclusive and the latter does not rely on the former to work.
 *
 * The HTTP request to this file will not slow down the visitor who happens to
 * visit when a scheduled cron event runs.
 *
 * @package WordPress
 */

ignore_user_abort( true );

if ( ! headers_sent() ) {
	header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
	header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
}

// Don't run cron until the request finishes, if possible.
if ( PHP_VERSION_ID >= 70016 && function_exists( 'fastcgi_finish_request' ) ) {
	fastcgi_finish_request();
} elseif ( function_exists( 'litespeed_finish_request' ) ) {
	litespeed_finish_request();
}

if ( ! empty( $_POST ) || defined( 'DOING_AJAX' ) || defined( 'DOING_CRON' ) ) {
	die();
}

/**
 * Tell WordPress the cron task is running.
 *
 * @var bool
 */
define( 'DOING_CRON', true );

if ( ! defined( 'ABSPATH' ) ) {
	/** Set up WordPress environment */
	require_once __DIR__ . '/wp-load.php';
}

/**
 * Retrieves the cron lock.
 *
 * Returns the uncached `doing_cron` transient.
 *
 * @ignore
 * @since 3.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return string|int|false Value of the `doing_cron` transient, 0|false otherwise.
 */
function _get_cron_lock() {
	global $wpdb;

	$value = 0;
	if ( wp_using_ext_object_cache() ) {
		/*
		 * Skip local cache and force re-fetch of doing_cron transient
		 * in case another process updated the cache.
		 */
		$value = wp_cache_get( 'doing_cron', 'transient', true );
	} else {
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", '_transient_doing_cron' ) );
		if ( is_object( $row ) ) {
			$value = $row->option_value;
		}
	}

	return $value;
}

$crons = wp_get_ready_cron_jobs();
if ( empty( $crons ) ) {
	die();
}

$gmt_time = microtime( true );

// The cron lock: a unix timestamp from when the cron was spawned.
$doing_cron_transient = get_transient( 'doing_cron' );

// Use global $doing_wp_cron lock, otherwise use the GET lock. If no lock, try to grab a new lock.
if ( empty( $doing_wp_cron ) ) {
	if ( empty( $_GET['doing_wp_cron'] ) ) {
		// Called from external script/job. Try setting a lock.
		if ( $doing_cron_transient && ( $doing_cron_transient + WP_CRON_LOCK_TIMEOUT > $gmt_time ) ) {
			return;
		}
		$doing_wp_cron        = sprintf( '%.22F', microtime( true ) );
		$doing_cron_transient = $doing_wp_cron;
		set_transient( 'doing_cron', $doing_wp_cron );
	} else {
		$doing_wp_cron = $_GET['doing_wp_cron'];
	}
}

/*
 * The cron lock (a unix timestamp set when the cron was spawned),
 * must match $doing_wp_cron (the "key").
 */
if ( $doing_cron_transient !== $doing_wp_cron ) {
	return;
}

foreach ( $crons as $timestamp => $cronhooks ) {
	if ( $timestamp > $gmt_time ) {
		break;
	}

	foreach ( $cronhooks as $hook => $keys ) {

		foreach ( $keys as $k => $v ) {

			$schedule = $v['schedule'];

			if ( $schedule ) {
				$result = wp_reschedule_event( $timestamp, $schedule, $hook, $v['args'], true );

				if ( is_wp_error( $result ) ) {
					error_log(
						sprintf(
							/* translators: 1: Hook name, 2: Error code, 3: Error message, 4: Event data. */
							__( 'Cron reschedule event error for hook: %1$s, Error code: %2$s, Error message: %3$s, Data: %4$s' ),
							$hook,
							$result->get_error_code(),
							$result->get_error_message(),
							wp_json_encode( $v )
						)
					);

					/**
					 * Fires when an error happens rescheduling a cron event.
					 *
					 * @since 6.1.0
					 *
					 * @param WP_Error $result The WP_Error object.
					 * @param string   $hook   Action hook to execute when the event is run.
					 * @param array    $v      Event data.
					 */
					do_action( 'cron_reschedule_event_error', $result, $hook, $v );
				}
			}

			$result = wp_unschedule_event( $timestamp, $hook, $v['args'], true );

			if ( is_wp_error( $result ) ) {
				error_log(
					sprintf(
						/* translators: 1: Hook name, 2: Error code, 3: Error message, 4: Event data. */
						__( 'Cron unschedule event error for hook: %1$s, Error code: %2$s, Error message: %3$s, Data: %4$s' ),
						$hook,
						$result->get_error_code(),
						$result->get_error_message(),
						wp_json_encode( $v )
					)
				);

				/**
				 * Fires when an error happens unscheduling a cron event.
				 *
				 * @since 6.1.0
				 *
				 * @param WP_Error $result The WP_Error object.
				 * @param string   $hook   Action hook to execute when the event is run.
				 * @param array    $v      Event data.
				 */
				do_action( 'cron_unschedule_event_error', $result, $hook, $v );
			}

			/**
			 * Fires scheduled events.
			 *
			 * @ignore
			 * @since 2.1.0
			 *
			 * @param string $hook Name of the hook that was scheduled to be fired.
			 * @param array  $args The arguments to be passed to the hook.
			 */
			do_action_ref_array( $hook, $v['args'] );

			// If the hook ran too long and another cron process stole the lock, quit.
			if ( _get_cron_lock() !== $doing_wp_cron ) {
				return;
			}
		}
	}
}

if ( _get_cron_lock() === $doing_wp_cron ) {
	delete_transient( 'doing_cron' );
}

die();
