<?php
/**
 * WordPress Cron API
 *
 * @package WordPress
 */

/**
 * Schedules an event to run only once.
 *
 * Schedules a hook which will be triggered by WordPress at the specified UTC time.
 * The action will trigger when someone visits your WordPress site if the scheduled
 * time has passed.
 *
 * Note that scheduling an event to occur within 10 minutes of an existing event
 * with the same action hook will be ignored unless you pass unique `$args` values
 * for each scheduled event.
 *
 * Use wp_next_scheduled() to prevent duplicate events.
 *
 * Use wp_schedule_event() to schedule a recurring event.
 *
 * @since 2.1.0
 * @since 5.1.0 Return value modified to boolean indicating success or failure,
 *              {@see 'pre_schedule_event'} filter added to short-circuit the function.
 * @since 5.7.0 The `$wp_error` parameter was added.
 *
 * @link https://developer.wordpress.org/reference/functions/wp_schedule_single_event/
 *
 * @param int    $timestamp  Unix timestamp (UTC) for when to next run the event.
 * @param string $hook       Action hook to execute when the event is run.
 * @param array  $args       Optional. Array containing arguments to pass to the
 *                           hook's callback function. Each value in the array
 *                           is passed to the callback as an individual parameter.
 *                           The array keys are ignored. Default empty array.
 * @param bool   $wp_error   Optional. Whether to return a WP_Error on failure. Default false.
 * @return bool|WP_Error True if event successfully scheduled. False or WP_Error on failure.
 */
function wp_schedule_single_event( $timestamp, $hook, $args = array(), $wp_error = false ) {
	// Make sure timestamp is a positive integer.
	if ( ! is_numeric( $timestamp ) || $timestamp <= 0 ) {
		if ( $wp_error ) {
			return new WP_Error(
				'invalid_timestamp',
				__( 'Event timestamp must be a valid Unix timestamp.' )
			);
		}

		return false;
	}

	$event = (object) array(
		'hook'      => $hook,
		'timestamp' => $timestamp,
		'schedule'  => false,
		'args'      => $args,
	);

	/**
	 * Filter to override scheduling an event.
	 *
	 * Returning a non-null value will short-circuit adding the event to the
	 * cron array, causing the function to return the filtered value instead.
	 *
	 * Both single events and recurring events are passed through this filter;
	 * single events have `$event->schedule` as false, whereas recurring events
	 * have this set to a recurrence from wp_get_schedules(). Recurring
	 * events also have the integer recurrence interval set as `$event->interval`.
	 *
	 * For plugins replacing wp-cron, it is recommended you check for an
	 * identical event within ten minutes and apply the {@see 'schedule_event'}
	 * filter to check if another plugin has disallowed the event before scheduling.
	 *
	 * Return true if the event was scheduled, false or a WP_Error if not.
	 *
	 * @since 5.1.0
	 * @since 5.7.0 The `$wp_error` parameter was added, and a `WP_Error` object can now be returned.
	 *
	 * @param null|bool|WP_Error $result   The value to return instead. Default null to continue adding the event.
	 * @param object             $event    {
	 *     An object containing an event's data.
	 *
	 *     @type string       $hook      Action hook to execute when the event is run.
	 *     @type int          $timestamp Unix timestamp (UTC) for when to next run the event.
	 *     @type string|false $schedule  How often the event should subsequently recur.
	 *     @type array        $args      Array containing each separate argument to pass to the hook's callback function.
	 *     @type int          $interval  Optional. The interval time in seconds for the schedule. Only present for recurring events.
	 * }
	 * @param bool               $wp_error Whether to return a WP_Error on failure.
	 */
	$pre = apply_filters( 'pre_schedule_event', null, $event, $wp_error );

	if ( null !== $pre ) {
		if ( $wp_error && false === $pre ) {
			return new WP_Error(
				'pre_schedule_event_false',
				__( 'A plugin prevented the event from being scheduled.' )
			);
		}

		if ( ! $wp_error && is_wp_error( $pre ) ) {
			return false;
		}

		return $pre;
	}

	/*
	 * Check for a duplicated event.
	 *
	 * Don't schedule an event if there's already an identical event
	 * within 10 minutes.
	 *
	 * When scheduling events within ten minutes of the current time,
	 * all past identical events are considered duplicates.
	 *
	 * When scheduling an event with a past timestamp (ie, before the
	 * current time) all events scheduled within the next ten minutes
	 * are considered duplicates.
	 */
	$crons = _get_cron_array();

	$key       = md5( serialize( $event->args ) );
	$duplicate = false;

	if ( $event->timestamp < time() + 10 * MINUTE_IN_SECONDS ) {
		$min_timestamp = 0;
	} else {
		$min_timestamp = $event->timestamp - 10 * MINUTE_IN_SECONDS;
	}

	if ( $event->timestamp < time() ) {
		$max_timestamp = time() + 10 * MINUTE_IN_SECONDS;
	} else {
		$max_timestamp = $event->timestamp + 10 * MINUTE_IN_SECONDS;
	}

	foreach ( $crons as $event_timestamp => $cron ) {
		if ( $event_timestamp < $min_timestamp ) {
			continue;
		}

		if ( $event_timestamp > $max_timestamp ) {
			break;
		}

		if ( isset( $cron[ $event->hook ][ $key ] ) ) {
			$duplicate = true;
			break;
		}
	}

	if ( $duplicate ) {
		if ( $wp_error ) {
			return new WP_Error(
				'duplicate_event',
				__( 'A duplicate event already exists.' )
			);
		}

		return false;
	}

	/**
	 * Modify an event before it is scheduled.
	 *
	 * @since 3.1.0
	 *
	 * @param object|false $event {
	 *     An object containing an event's data, or boolean false to prevent the event from being scheduled.
	 *
	 *     @type string       $hook      Action hook to execute when the event is run.
	 *     @type int          $timestamp Unix timestamp (UTC) for when to next run the event.
	 *     @type string|false $schedule  How often the event should subsequently recur.
	 *     @type array        $args      Array containing each separate argument to pass to the hook's callback function.
	 *     @type int          $interval  Optional. The interval time in seconds for the schedule. Only present for recurring events.
	 * }
	 */
	$event = apply_filters( 'schedule_event', $event );

	// A plugin disallowed this event.
	if ( ! $event ) {
		if ( $wp_error ) {
			return new WP_Error(
				'schedule_event_false',
				__( 'A plugin disallowed this event.' )
			);
		}

		return false;
	}

	$crons[ $event->timestamp ][ $event->hook ][ $key ] = array(
		'schedule' => $event->schedule,
		'args'     => $event->args,
	);
	uksort( $crons, 'strnatcasecmp' );

	return _set_cron_array( $crons, $wp_error );
}

/**
 * Schedules a recurring event.
 *
 * Schedules a hook which will be triggered by WordPress at the specified interval.
 * The action will trigger when someone visits your WordPress site if the scheduled
 * time has passed.
 *
 * Valid values for the recurrence are 'hourly', 'daily', and 'twicedaily'. These can
 * be extended using the {@see 'cron_schedules'} filter in wp_get_schedules().
 *
 * Use wp_next_scheduled() to prevent duplicate events.
 *
 * Use wp_schedule_single_event() to schedule a non-recurring event.
 *
 * @since 2.1.0
 * @since 5.1.0 Return value modified to boolean indicating success or failure,
 *              {@see 'pre_schedule_event'} filter added to short-circuit the function.
 * @since 5.7.0 The `$wp_error` parameter was added.
 *
 * @link https://developer.wordpress.org/reference/functions/wp_schedule_event/
 *
 * @param int    $timestamp  Unix timestamp (UTC) for when to next run the event.
 * @param string $recurrence How often the event should subsequently recur.
 *                           See wp_get_schedules() for accepted values.
 * @param string $hook       Action hook to execute when the event is run.
 * @param array  $args       Optional. Array containing arguments to pass to the
 *                           hook's callback function. Each value in the array
 *                           is passed to the callback as an individual parameter.
 *                           The array keys are ignored. Default empty array.
 * @param bool   $wp_error   Optional. Whether to return a WP_Error on failure. Default false.
 * @return bool|WP_Error True if event successfully scheduled. False or WP_Error on failure.
 */
function wp_schedule_event( $timestamp, $recurrence, $hook, $args = array(), $wp_error = false ) {
	// Make sure timestamp is a positive integer.
	if ( ! is_numeric( $timestamp ) || $timestamp <= 0 ) {
		if ( $wp_error ) {
			return new WP_Error(
				'invalid_timestamp',
				__( 'Event timestamp must be a valid Unix timestamp.' )
			);
		}

		return false;
	}

	$schedules = wp_get_schedules();

	if ( ! isset( $schedules[ $recurrence ] ) ) {
		if ( $wp_error ) {
			return new WP_Error(
				'invalid_schedule',
				__( 'Event schedule does not exist.' )
			);
		}

		return false;
	}

	$event = (object) array(
		'hook'      => $hook,
		'timestamp' => $timestamp,
		'schedule'  => $recurrence,
		'args'      => $args,
		'interval'  => $schedules[ $recurrence ]['interval'],
	);

	/** This filter is documented in wp-includes/cron.php */
	$pre = apply_filters( 'pre_schedule_event', null, $event, $wp_error );

	if ( null !== $pre ) {
		if ( $wp_error && false === $pre ) {
			return new WP_Error(
				'pre_schedule_event_false',
				__( 'A plugin prevented the event from being scheduled.' )
			);
		}

		if ( ! $wp_error && is_wp_error( $pre ) ) {
			return false;
		}

		return $pre;
	}

	/** This filter is documented in wp-includes/cron.php */
	$event = apply_filters( 'schedule_event', $event );

	// A plugin disallowed this event.
	if ( ! $event ) {
		if ( $wp_error ) {
			return new WP_Error(
				'schedule_event_false',
				__( 'A plugin disallowed this event.' )
			);
		}

		return false;
	}

	$key = md5( serialize( $event->args ) );

	$crons = _get_cron_array();

	$crons[ $event->timestamp ][ $event->hook ][ $key ] = array(
		'schedule' => $event->schedule,
		'args'     => $event->args,
		'interval' => $event->interval,
	);
	uksort( $crons, 'strnatcasecmp' );

	return _set_cron_array( $crons, $wp_error );
}

/**
 * Reschedules a recurring event.
 *
 * Mainly for internal use, this takes the UTC timestamp of a previously run
 * recurring event and reschedules it for its next run.
 *
 * To change upcoming scheduled events, use wp_schedule_event() to
 * change the recurrence frequency.
 *
 * @since 2.1.0
 * @since 5.1.0 Return value modified to boolean indicating success or failure,
 *              {@see 'pre_reschedule_event'} filter added to short-circuit the function.
 * @since 5.7.0 The `$wp_error` parameter was added.
 *
 * @param int    $timestamp  Unix timestamp (UTC) for when the event was scheduled.
 * @param string $recurrence How often the event should subsequently recur.
 *                           See wp_get_schedules() for accepted values.
 * @param string $hook       Action hook to execute when the event is run.
 * @param array  $args       Optional. Array containing arguments to pass to the
 *                           hook's callback function. Each value in the array
 *                           is passed to the callback as an individual parameter.
 *                           The array keys are ignored. Default empty array.
 * @param bool   $wp_error   Optional. Whether to return a WP_Error on failure. Default false.
 * @return bool|WP_Error True if event successfully rescheduled. False or WP_Error on failure.
 */
function wp_reschedule_event( $timestamp, $recurrence, $hook, $args = array(), $wp_error = false ) {
	// Make sure timestamp is a positive integer.
	if ( ! is_numeric( $timestamp ) || $timestamp <= 0 ) {
		if ( $wp_error ) {
			return new WP_Error(
				'invalid_timestamp',
				__( 'Event timestamp must be a valid Unix timestamp.' )
			);
		}

		return false;
	}

	$schedules = wp_get_schedules();
	$interval  = 0;

	// First we try to get the interval from the schedule.
	if ( isset( $schedules[ $recurrence ] ) ) {
		$interval = $schedules[ $recurrence ]['interval'];
	}

	// Now we try to get it from the saved interval in case the schedule disappears.
	if ( 0 === $interval ) {
		$scheduled_event = wp_get_scheduled_event( $hook, $args, $timestamp );

		if ( $scheduled_event && isset( $scheduled_event->interval ) ) {
			$interval = $scheduled_event->interval;
		}
	}

	$event = (object) array(
		'hook'      => $hook,
		'timestamp' => $timestamp,
		'schedule'  => $recurrence,
		'args'      => $args,
		'interval'  => $interval,
	);

	/**
	 * Filter to override rescheduling of a recurring event.
	 *
	 * Returning a non-null value will short-circuit the normal rescheduling
	 * process, causing the function to return the filtered value instead.
	 *
	 * For plugins replacing wp-cron, return true if the event was successfully
	 * rescheduled, false or a WP_Error if not.
	 *
	 * @since 5.1.0
	 * @since 5.7.0 The `$wp_error` parameter was added, and a `WP_Error` object can now be returned.
	 *
	 * @param null|bool|WP_Error $pre      Value to return instead. Default null to continue adding the event.
	 * @param object             $event    {
	 *     An object containing an event's data.
	 *
	 *     @type string $hook      Action hook to execute when the event is run.
	 *     @type int    $timestamp Unix timestamp (UTC) for when to next run the event.
	 *     @type string $schedule  How often the event should subsequently recur.
	 *     @type array  $args      Array containing each separate argument to pass to the hook's callback function.
	 *     @type int    $interval  The interval time in seconds for the schedule.
	 * }
	 * @param bool               $wp_error Whether to return a WP_Error on failure.
	 */
	$pre = apply_filters( 'pre_reschedule_event', null, $event, $wp_error );

	if ( null !== $pre ) {
		if ( $wp_error && false === $pre ) {
			return new WP_Error(
				'pre_reschedule_event_false',
				__( 'A plugin prevented the event from being rescheduled.' )
			);
		}

		if ( ! $wp_error && is_wp_error( $pre ) ) {
			return false;
		}

		return $pre;
	}

	// Now we assume something is wrong and fail to schedule.
	if ( 0 === $interval ) {
		if ( $wp_error ) {
			return new WP_Error(
				'invalid_schedule',
				__( 'Event schedule does not exist.' )
			);
		}

		return false;
	}

	$now = time();

	if ( $timestamp >= $now ) {
		$timestamp = $now + $interval;
	} else {
		$timestamp = $now + ( $interval - ( ( $now - $timestamp ) % $interval ) );
	}

	return wp_schedule_event( $timestamp, $recurrence, $hook, $args, $wp_error );
}

/**
 * Unschedules a previously scheduled event.
 *
 * The `$timestamp` and `$hook` parameters are required so that the event can be
 * identified.
 *
 * @since 2.1.0
 * @since 5.1.0 Return value modified to boolean indicating success or failure,
 *              {@see 'pre_unschedule_event'} filter added to short-circuit the function.
 * @since 5.7.0 The `$wp_error` parameter was added.
 *
 * @param int    $timestamp Unix timestamp (UTC) of the event.
 * @param string $hook      Action hook of the event.
 * @param array  $args      Optional. Array containing each separate argument to pass to the hook's callback function.
 *                          Although not passed to a callback, these arguments are used to uniquely identify the
 *                          event, so they should be the same as those used when originally scheduling the event.
 *                          Default empty array.
 * @param bool   $wp_error  Optional. Whether to return a WP_Error on failure. Default false.
 * @return bool|WP_Error True if event successfully unscheduled. False or WP_Error on failure.
 */
function wp_unschedule_event( $timestamp, $hook, $args = array(), $wp_error = false ) {
	// Make sure timestamp is a positive integer.
	if ( ! is_numeric( $timestamp ) || $timestamp <= 0 ) {
		if ( $wp_error ) {
			return new WP_Error(
				'invalid_timestamp',
				__( 'Event timestamp must be a valid Unix timestamp.' )
			);
		}

		return false;
	}

	/**
	 * Filter to override unscheduling of events.
	 *
	 * Returning a non-null value will short-circuit the normal unscheduling
	 * process, causing the function to return the filtered value instead.
	 *
	 * For plugins replacing wp-cron, return true if the event was successfully
	 * unscheduled, false or a WP_Error if not.
	 *
	 * @since 5.1.0
	 * @since 5.7.0 The `$wp_error` parameter was added, and a `WP_Error` object can now be returned.
	 *
	 * @param null|bool|WP_Error $pre       Value to return instead. Default null to continue unscheduling the event.
	 * @param int                $timestamp Timestamp for when to run the event.
	 * @param string             $hook      Action hook, the execution of which will be unscheduled.
	 * @param array              $args      Arguments to pass to the hook's callback function.
	 * @param bool               $wp_error  Whether to return a WP_Error on failure.
	 */
	$pre = apply_filters( 'pre_unschedule_event', null, $timestamp, $hook, $args, $wp_error );

	if ( null !== $pre ) {
		if ( $wp_error && false === $pre ) {
			return new WP_Error(
				'pre_unschedule_event_false',
				__( 'A plugin prevented the event from being unscheduled.' )
			);
		}

		if ( ! $wp_error && is_wp_error( $pre ) ) {
			return false;
		}

		return $pre;
	}

	$crons = _get_cron_array();
	$key   = md5( serialize( $args ) );

	unset( $crons[ $timestamp ][ $hook ][ $key ] );

	if ( empty( $crons[ $timestamp ][ $hook ] ) ) {
		unset( $crons[ $timestamp ][ $hook ] );
	}

	if ( empty( $crons[ $timestamp ] ) ) {
		unset( $crons[ $timestamp ] );
	}

	return _set_cron_array( $crons, $wp_error );
}

/**
 * Unschedules all events attached to the hook with the specified arguments.
 *
 * Warning: This function may return boolean false, but may also return a non-boolean
 * value which evaluates to false. For information about casting to booleans see the
 * {@link https://www.php.net/manual/en/language.types.boolean.php PHP documentation}. Use
 * the `===` operator for testing the return value of this function.
 *
 * @since 2.1.0
 * @since 5.1.0 Return value modified to indicate success or failure,
 *              {@see 'pre_clear_scheduled_hook'} filter added to short-circuit the function.
 * @since 5.7.0 The `$wp_error` parameter was added.
 *
 * @param string $hook     Action hook, the execution of which will be unscheduled.
 * @param array  $args     Optional. Array containing each separate argument to pass to the hook's callback function.
 *                         Although not passed to a callback, these arguments are used to uniquely identify the
 *                         event, so they should be the same as those used when originally scheduling the event.
 *                         Default empty array.
 * @param bool   $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 * @return int|false|WP_Error On success an integer indicating number of events unscheduled (0 indicates no
 *                            events were registered with the hook and arguments combination), false or WP_Error
 *                            if unscheduling one or more events fail.
 */
function wp_clear_scheduled_hook( $hook, $args = array(), $wp_error = false ) {
	/*
	 * Backward compatibility.
	 * Previously, this function took the arguments as discrete vars rather than an array like the rest of the API.
	 */
	if ( ! is_array( $args ) ) {
		_deprecated_argument(
			__FUNCTION__,
			'3.0.0',
			__( 'This argument has changed to an array to match the behavior of the other cron functions.' )
		);

		$args     = array_slice( func_get_args(), 1 ); // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
		$wp_error = false;
	}

	/**
	 * Filter to override clearing a scheduled hook.
	 *
	 * Returning a non-null value will short-circuit the normal unscheduling
	 * process, causing the function to return the filtered value instead.
	 *
	 * For plugins replacing wp-cron, return the number of events successfully
	 * unscheduled (zero if no events were registered with the hook) or false
	 * or a WP_Error if unscheduling one or more events fails.
	 *
	 * @since 5.1.0
	 * @since 5.7.0 The `$wp_error` parameter was added, and a `WP_Error` object can now be returned.
	 *
	 * @param null|int|false|WP_Error $pre      Value to return instead. Default null to continue unscheduling the event.
	 * @param string                  $hook     Action hook, the execution of which will be unscheduled.
	 * @param array                   $args     Arguments to pass to the hook's callback function.
	 * @param bool                    $wp_error Whether to return a WP_Error on failure.
	 */
	$pre = apply_filters( 'pre_clear_scheduled_hook', null, $hook, $args, $wp_error );

	if ( null !== $pre ) {
		if ( $wp_error && false === $pre ) {
			return new WP_Error(
				'pre_clear_scheduled_hook_false',
				__( 'A plugin prevented the hook from being cleared.' )
			);
		}

		if ( ! $wp_error && is_wp_error( $pre ) ) {
			return false;
		}

		return $pre;
	}

	/*
	 * This logic duplicates wp_next_scheduled().
	 * It's required due to a scenario where wp_unschedule_event() fails due to update_option() failing,
	 * and, wp_next_scheduled() returns the same schedule in an infinite loop.
	 */
	$crons = _get_cron_array();
	if ( empty( $crons ) ) {
		return 0;
	}

	$results = array();
	$key     = md5( serialize( $args ) );

	foreach ( $crons as $timestamp => $cron ) {
		if ( isset( $cron[ $hook ][ $key ] ) ) {
			$results[] = wp_unschedule_event( $timestamp, $hook, $args, true );
		}
	}

	$errors = array_filter( $results, 'is_wp_error' );
	$error  = new WP_Error();

	if ( $errors ) {
		if ( $wp_error ) {
			array_walk( $errors, array( $error, 'merge_from' ) );

			return $error;
		}

		return false;
	}

	return count( $results );
}

/**
 * Unschedules all events attached to the hook.
 *
 * Can be useful for plugins when deactivating to clean up the cron queue.
 *
 * Warning: This function may return boolean false, but may also return a non-boolean
 * value which evaluates to false. For information about casting to booleans see the
 * {@link https://www.php.net/manual/en/language.types.boolean.php PHP documentation}. Use
 * the `===` operator for testing the return value of this function.
 *
 * @since 4.9.0
 * @since 5.1.0 Return value added to indicate success or failure.
 * @since 5.7.0 The `$wp_error` parameter was added.
 *
 * @param string $hook     Action hook, the execution of which will be unscheduled.
 * @param bool   $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 * @return int|false|WP_Error On success an integer indicating number of events unscheduled (0 indicates no
 *                            events were registered on the hook), false or WP_Error if unscheduling fails.
 */
function wp_unschedule_hook( $hook, $wp_error = false ) {
	/**
	 * Filter to override clearing all events attached to the hook.
	 *
	 * Returning a non-null value will short-circuit the normal unscheduling
	 * process, causing the function to return the filtered value instead.
	 *
	 * For plugins replacing wp-cron, return the number of events successfully
	 * unscheduled (zero if no events were registered with the hook) or false
	 * if unscheduling one or more events fails.
	 *
	 * @since 5.1.0
	 * @since 5.7.0 The `$wp_error` parameter was added, and a `WP_Error` object can now be returned.
	 *
	 * @param null|int|false|WP_Error $pre      Value to return instead. Default null to continue unscheduling the hook.
	 * @param string                  $hook     Action hook, the execution of which will be unscheduled.
	 * @param bool                    $wp_error Whether to return a WP_Error on failure.
	 */
	$pre = apply_filters( 'pre_unschedule_hook', null, $hook, $wp_error );

	if ( null !== $pre ) {
		if ( $wp_error && false === $pre ) {
			return new WP_Error(
				'pre_unschedule_hook_false',
				__( 'A plugin prevented the hook from being cleared.' )
			);
		}

		if ( ! $wp_error && is_wp_error( $pre ) ) {
			return false;
		}

		return $pre;
	}

	$crons = _get_cron_array();
	if ( empty( $crons ) ) {
		return 0;
	}

	$results = array();

	foreach ( $crons as $timestamp => $args ) {
		if ( ! empty( $crons[ $timestamp ][ $hook ] ) ) {
			$results[] = count( $crons[ $timestamp ][ $hook ] );
		}

		unset( $crons[ $timestamp ][ $hook ] );

		if ( empty( $crons[ $timestamp ] ) ) {
			unset( $crons[ $timestamp ] );
		}
	}

	/*
	 * If the results are empty (zero events to unschedule), no attempt
	 * to update the cron array is required.
	 */
	if ( empty( $results ) ) {
		return 0;
	}

	$set = _set_cron_array( $crons, $wp_error );

	if ( true === $set ) {
		return array_sum( $results );
	}

	return $set;
}

/**
 * Retrieves a scheduled event.
 *
 * Retrieves the full event object for a given event, if no timestamp is specified the next
 * scheduled event is returned.
 *
 * @since 5.1.0
 *
 * @param string   $hook      Action hook of the event.
 * @param array    $args      Optional. Array containing each separate argument to pass to the hook's callback function.
 *                            Although not passed to a callback, these arguments are used to uniquely identify the
 *                            event, so they should be the same as those used when originally scheduling the event.
 *                            Default empty array.
 * @param int|null $timestamp Optional. Unix timestamp (UTC) of the event. If not specified, the next scheduled event
 *                            is returned. Default null.
 * @return object|false {
 *     The event object. False if the event does not exist.
 *
 *     @type string       $hook      Action hook to execute when the event is run.
 *     @type int          $timestamp Unix timestamp (UTC) for when to next run the event.
 *     @type string|false $schedule  How often the event should subsequently recur.
 *     @type array        $args      Array containing each separate argument to pass to the hook's callback function.
 *     @type int          $interval  Optional. The interval time in seconds for the schedule. Only present for recurring events.
 * }
 */
function wp_get_scheduled_event( $hook, $args = array(), $timestamp = null ) {
	/**
	 * Filter to override retrieving a scheduled event.
	 *
	 * Returning a non-null value will short-circuit the normal process,
	 * returning the filtered value instead.
	 *
	 * Return false if the event does not exist, otherwise an event object
	 * should be returned.
	 *
	 * @since 5.1.0
	 *
	 * @param null|false|object $pre  Value to return instead. Default null to continue retrieving the event.
	 * @param string            $hook Action hook of the event.
	 * @param array             $args Array containing each separate argument to pass to the hook's callback function.
	 *                                Although not passed to a callback, these arguments are used to uniquely identify
	 *                                the event.
	 * @param int|null  $timestamp Unix timestamp (UTC) of the event. Null to retrieve next scheduled event.
	 */
	$pre = apply_filters( 'pre_get_scheduled_event', null, $hook, $args, $timestamp );

	if ( null !== $pre ) {
		return $pre;
	}

	if ( null !== $timestamp && ! is_numeric( $timestamp ) ) {
		return false;
	}

	$crons = _get_cron_array();
	if ( empty( $crons ) ) {
		return false;
	}

	$key = md5( serialize( $args ) );

	if ( ! $timestamp ) {
		// Get next event.
		$next = false;
		foreach ( $crons as $timestamp => $cron ) {
			if ( isset( $cron[ $hook ][ $key ] ) ) {
				$next = $timestamp;
				break;
			}
		}

		if ( ! $next ) {
			return false;
		}

		$timestamp = $next;
	} elseif ( ! isset( $crons[ $timestamp ][ $hook ][ $key ] ) ) {
		return false;
	}

	$event = (object) array(
		'hook'      => $hook,
		'timestamp' => $timestamp,
		'schedule'  => $crons[ $timestamp ][ $hook ][ $key ]['schedule'],
		'args'      => $args,
	);

	if ( isset( $crons[ $timestamp ][ $hook ][ $key ]['interval'] ) ) {
		$event->interval = $crons[ $timestamp ][ $hook ][ $key ]['interval'];
	}

	return $event;
}

/**
 * Retrieves the next timestamp for an event.
 *
 * @since 2.1.0
 *
 * @param string $hook Action hook of the event.
 * @param array  $args Optional. Array containing each separate argument to pass to the hook's callback function.
 *                     Although not passed to a callback, these arguments are used to uniquely identify the
 *                     event, so they should be the same as those used when originally scheduling the event.
 *                     Default empty array.
 * @return int|false The Unix timestamp of the next time the event will occur. False if the event doesn't exist.
 */
function wp_next_scheduled( $hook, $args = array() ) {
	$next_event = wp_get_scheduled_event( $hook, $args );

	if ( ! $next_event ) {
		return false;
	}

	return $next_event->timestamp;
}

/**
 * Sends a request to run cron through HTTP request that doesn't halt page loading.
 *
 * @since 2.1.0
 * @since 5.1.0 Return values added.
 *
 * @param int $gmt_time Optional. Unix timestamp (UTC). Default 0 (current time is used).
 * @return bool True if spawned, false if no events spawned.
 */
function spawn_cron( $gmt_time = 0 ) {
	if ( ! $gmt_time ) {
		$gmt_time = microtime( true );
	}

	if ( defined( 'DOING_CRON' ) || isset( $_GET['doing_wp_cron'] ) ) {
		return false;
	}

	/*
	 * Get the cron lock, which is a Unix timestamp of when the last cron was spawned
	 * and has not finished running.
	 *
	 * Multiple processes on multiple web servers can run this code concurrently,
	 * this lock attempts to make spawning as atomic as possible.
	 */
	$lock = (float) get_transient( 'doing_cron' );

	if ( $lock > $gmt_time + 10 * MINUTE_IN_SECONDS ) {
		$lock = 0;
	}

	// Don't run if another process is currently running it or more than once every 60 sec.
	if ( $lock + WP_CRON_LOCK_TIMEOUT > $gmt_time ) {
		return false;
	}

	// Sanity check.
	$crons = wp_get_ready_cron_jobs();
	if ( empty( $crons ) ) {
		return false;
	}

	$keys = array_keys( $crons );
	if ( isset( $keys[0] ) && $keys[0] > $gmt_time ) {
		return false;
	}

	if ( defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON ) {
		if ( 'GET' !== $_SERVER['REQUEST_METHOD'] || defined( 'DOING_AJAX' ) || defined( 'XMLRPC_REQUEST' ) ) {
			return false;
		}

		$doing_wp_cron = sprintf( '%.22F', $gmt_time );
		set_transient( 'doing_cron', $doing_wp_cron );

		ob_start();
		wp_redirect( add_query_arg( 'doing_wp_cron', $doing_wp_cron, wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		echo ' ';

		// Flush any buffers and send the headers.
		wp_ob_end_flush_all();
		flush();

		require_once ABSPATH . 'wp-cron.php';
		return true;
	}

	// Set the cron lock with the current unix timestamp, when the cron is being spawned.
	$doing_wp_cron = sprintf( '%.22F', $gmt_time );
	set_transient( 'doing_cron', $doing_wp_cron );

	/**
	 * Filters the cron request arguments.
	 *
	 * @since 3.5.0
	 * @since 4.5.0 The `$doing_wp_cron` parameter was added.
	 *
	 * @param array $cron_request_array {
	 *     An array of cron request URL arguments.
	 *
	 *     @type string $url  The cron request URL.
	 *     @type int    $key  The 22 digit GMT microtime.
	 *     @type array  $args {
	 *         An array of cron request arguments.
	 *
	 *         @type int  $timeout   The request timeout in seconds. Default .01 seconds.
	 *         @type bool $blocking  Whether to set blocking for the request. Default false.
	 *         @type bool $sslverify Whether SSL should be verified for the request. Default false.
	 *     }
	 * }
	 * @param string $doing_wp_cron The unix timestamp of the cron lock.
	 */
	$cron_request = apply_filters(
		'cron_request',
		array(
			'url'  => add_query_arg( 'doing_wp_cron', $doing_wp_cron, site_url( 'wp-cron.php' ) ),
			'key'  => $doing_wp_cron,
			'args' => array(
				'timeout'   => 0.01,
				'blocking'  => false,
				/** This filter is documented in wp-includes/class-wp-http-streams.php */
				'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
			),
		),
		$doing_wp_cron
	);

	$result = wp_remote_post( $cron_request['url'], $cron_request['args'] );

	return ! is_wp_error( $result );
}

/**
 * Registers _wp_cron() to run on the {@see 'wp_loaded'} action.
 *
 * If the {@see 'wp_loaded'} action has already fired, this function calls
 * _wp_cron() directly.
 *
 * Warning: This function may return Boolean FALSE, but may also return a non-Boolean
 * value which evaluates to FALSE. For information about casting to booleans see the
 * {@link https://www.php.net/manual/en/language.types.boolean.php PHP documentation}. Use
 * the `===` operator for testing the return value of this function.
 *
 * @since 2.1.0
 * @since 5.1.0 Return value added to indicate success or failure.
 * @since 5.7.0 Functionality moved to _wp_cron() to which this becomes a wrapper.
 *
 * @return false|int|void On success an integer indicating number of events spawned (0 indicates no
 *                        events needed to be spawned), false if spawning fails for one or more events or
 *                        void if the function registered _wp_cron() to run on the action.
 */
function wp_cron() {
	if ( did_action( 'wp_loaded' ) ) {
		return _wp_cron();
	}

	add_action( 'wp_loaded', '_wp_cron', 20 );
}

/**
 * Runs scheduled callbacks or spawns cron for all scheduled events.
 *
 * Warning: This function may return Boolean FALSE, but may also return a non-Boolean
 * value which evaluates to FALSE. For information about casting to booleans see the
 * {@link https://www.php.net/manual/en/language.types.boolean.php PHP documentation}. Use
 * the `===` operator for testing the return value of this function.
 *
 * @since 5.7.0
 * @access private
 *
 * @return int|false On success an integer indicating number of events spawned (0 indicates no
 *                   events needed to be spawned), false if spawning fails for one or more events.
 */
function _wp_cron() {
	// Prevent infinite loops caused by lack of wp-cron.php.
	if ( str_contains( $_SERVER['REQUEST_URI'], '/wp-cron.php' )
		|| ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON )
	) {
		return 0;
	}

	$crons = wp_get_ready_cron_jobs();
	if ( empty( $crons ) ) {
		return 0;
	}

	$gmt_time = microtime( true );
	$keys     = array_keys( $crons );
	if ( isset( $keys[0] ) && $keys[0] > $gmt_time ) {
		return 0;
	}

	$schedules = wp_get_schedules();
	$results   = array();

	foreach ( $crons as $timestamp => $cronhooks ) {
		if ( $timestamp > $gmt_time ) {
			break;
		}

		foreach ( (array) $cronhooks as $hook => $args ) {
			if ( isset( $schedules[ $hook ]['callback'] )
				&& ! call_user_func( $schedules[ $hook ]['callback'] )
			) {
				continue;
			}

			$results[] = spawn_cron( $gmt_time );
			break 2;
		}
	}

	if ( in_array( false, $results, true ) ) {
		return false;
	}

	return count( $results );
}

/**
 * Retrieves supported event recurrence schedules.
 *
 * The default supported recurrences are 'hourly', 'twicedaily', 'daily', and 'weekly'.
 * A plugin may add more by hooking into the {@see 'cron_schedules'} filter.
 * The filter accepts an array of arrays. The outer array has a key that is the name
 * of the schedule, for example 'monthly'. The value is an array with two keys,
 * one is 'interval' and the other is 'display'.
 *
 * The 'interval' is a number in seconds of when the cron job should run.
 * So for 'hourly' the time is `HOUR_IN_SECONDS` (60 * 60 or 3600). For 'monthly',
 * the value would be `MONTH_IN_SECONDS` (30 * 24 * 60 * 60 or 2592000).
 *
 * The 'display' is the description. For the 'monthly' key, the 'display'
 * would be `__( 'Once Monthly' )`.
 *
 * For your plugin, you will be passed an array. You can easily add your
 * schedule by doing the following.
 *
 *     // Filter parameter variable name is 'array'.
 *     $array['monthly'] = array(
 *         'interval' => MONTH_IN_SECONDS,
 *         'display'  => __( 'Once Monthly' )
 *     );
 *
 * @since 2.1.0
 * @since 5.4.0 The 'weekly' schedule was added.
 *
 * @return array {
 *     The array of cron schedules keyed by the schedule name.
 *
 *     @type array ...$0 {
 *         Cron schedule information.
 *
 *         @type int    $interval The schedule interval in seconds.
 *         @type string $display  The schedule display name.
 *     }
 * }
 */
function wp_get_schedules() {
	$schedules = array(
		'hourly'     => array(
			'interval' => HOUR_IN_SECONDS,
			'display'  => __( 'Once Hourly' ),
		),
		'twicedaily' => array(
			'interval' => 12 * HOUR_IN_SECONDS,
			'display'  => __( 'Twice Daily' ),
		),
		'daily'      => array(
			'interval' => DAY_IN_SECONDS,
			'display'  => __( 'Once Daily' ),
		),
		'weekly'     => array(
			'interval' => WEEK_IN_SECONDS,
			'display'  => __( 'Once Weekly' ),
		),
	);

	/**
	 * Filters the non-default cron schedules.
	 *
	 * @since 2.1.0
	 *
	 * @param array $new_schedules {
	 *     An array of non-default cron schedules keyed by the schedule name. Default empty array.
	 *
	 *     @type array ...$0 {
	 *         Cron schedule information.
	 *
	 *         @type int    $interval The schedule interval in seconds.
	 *         @type string $display  The schedule display name.
	 *     }
	 * }
	 */
	return array_merge( apply_filters( 'cron_schedules', array() ), $schedules );
}

/**
 * Retrieves the name of the recurrence schedule for an event.
 *
 * @see wp_get_schedules() for available schedules.
 *
 * @since 2.1.0
 * @since 5.1.0 {@see 'get_schedule'} filter added.
 *
 * @param string $hook Action hook to identify the event.
 * @param array  $args Optional. Arguments passed to the event's callback function.
 *                     Default empty array.
 * @return string|false Schedule name on success, false if no schedule.
 */
function wp_get_schedule( $hook, $args = array() ) {
	$schedule = false;
	$event    = wp_get_scheduled_event( $hook, $args );

	if ( $event ) {
		$schedule = $event->schedule;
	}

	/**
	 * Filters the schedule name for a hook.
	 *
	 * @since 5.1.0
	 *
	 * @param string|false $schedule Schedule for the hook. False if not found.
	 * @param string       $hook     Action hook to execute when cron is run.
	 * @param array        $args     Arguments to pass to the hook's callback function.
	 */
	return apply_filters( 'get_schedule', $schedule, $hook, $args );
}

/**
 * Retrieves cron jobs ready to be run.
 *
 * Returns the results of _get_cron_array() limited to events ready to be run,
 * ie, with a timestamp in the past.
 *
 * @since 5.1.0
 *
 * @return array[] Array of cron job arrays ready to be run.
 */
function wp_get_ready_cron_jobs() {
	/**
	 * Filter to override retrieving ready cron jobs.
	 *
	 * Returning an array will short-circuit the normal retrieval of ready
	 * cron jobs, causing the function to return the filtered value instead.
	 *
	 * @since 5.1.0
	 *
	 * @param null|array[] $pre Array of ready cron tasks to return instead. Default null
	 *                          to continue using results from _get_cron_array().
	 */
	$pre = apply_filters( 'pre_get_ready_cron_jobs', null );

	if ( null !== $pre ) {
		return $pre;
	}

	$crons    = _get_cron_array();
	$gmt_time = microtime( true );
	$results  = array();

	foreach ( $crons as $timestamp => $cronhooks ) {
		if ( $timestamp > $gmt_time ) {
			break;
		}

		$results[ $timestamp ] = $cronhooks;
	}

	return $results;
}

//
// Private functions.
//

/**
 * Retrieves cron info array option.
 *
 * @since 2.1.0
 * @since 6.1.0 Return type modified to consistently return an array.
 * @access private
 *
 * @return array[] Array of cron events.
 */
function _get_cron_array() {
	$cron = get_option( 'cron' );
	if ( ! is_array( $cron ) ) {
		return array();
	}

	if ( ! isset( $cron['version'] ) ) {
		$cron = _upgrade_cron_array( $cron );
	}

	unset( $cron['version'] );

	return $cron;
}

/**
 * Updates the cron option with the new cron array.
 *
 * @since 2.1.0
 * @since 5.1.0 Return value modified to outcome of update_option().
 * @since 5.7.0 The `$wp_error` parameter was added.
 *
 * @access private
 *
 * @param array[] $cron     Array of cron info arrays from _get_cron_array().
 * @param bool    $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 * @return bool|WP_Error True if cron array updated. False or WP_Error on failure.
 */
function _set_cron_array( $cron, $wp_error = false ) {
	if ( ! is_array( $cron ) ) {
		$cron = array();
	}

	$cron['version'] = 2;

	$result = update_option( 'cron', $cron );

	if ( $wp_error && ! $result ) {
		return new WP_Error(
			'could_not_set',
			__( 'The cron event list could not be saved.' )
		);
	}

	return $result;
}

/**
 * Upgrades a cron info array.
 *
 * This function upgrades the cron info array to version 2.
 *
 * @since 2.1.0
 * @access private
 *
 * @param array $cron Cron info array from _get_cron_array().
 * @return array An upgraded cron info array.
 */
function _upgrade_cron_array( $cron ) {
	if ( isset( $cron['version'] ) && 2 === $cron['version'] ) {
		return $cron;
	}

	$new_cron = array();

	foreach ( (array) $cron as $timestamp => $hooks ) {
		foreach ( (array) $hooks as $hook => $args ) {
			$key = md5( serialize( $args['args'] ) );

			$new_cron[ $timestamp ][ $hook ][ $key ] = $args;
		}
	}

	$new_cron['version'] = 2;

	update_option( 'cron', $new_cron );

	return $new_cron;
}
