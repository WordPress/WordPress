<?php

/**
 * Deprecated API functions for scheduling actions
 *
 * Functions with the wc prefix were deprecated to avoid confusion with
 * Action Scheduler being included in WooCommerce core, and it providing
 * a different set of APIs for working with the action queue.
 */

/**
 * Schedule an action to run one time
 *
 * @param int $timestamp When the job will run
 * @param string $hook The hook to trigger
 * @param array $args Arguments to pass when the hook triggers
 * @param string $group The group to assign this job to
 *
 * @return string The job ID
 */
function wc_schedule_single_action( $timestamp, $hook, $args = array(), $group = '' ) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'as_schedule_single_action()' );
	return as_schedule_single_action( $timestamp, $hook, $args, $group );
}

/**
 * Schedule a recurring action
 *
 * @param int $timestamp When the first instance of the job will run
 * @param int $interval_in_seconds How long to wait between runs
 * @param string $hook The hook to trigger
 * @param array $args Arguments to pass when the hook triggers
 * @param string $group The group to assign this job to
 *
 * @deprecated 2.1.0
 *
 * @return string The job ID
 */
function wc_schedule_recurring_action( $timestamp, $interval_in_seconds, $hook, $args = array(), $group = '' ) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'as_schedule_recurring_action()' );
	return as_schedule_recurring_action( $timestamp, $interval_in_seconds, $hook, $args, $group );
}

/**
 * Schedule an action that recurs on a cron-like schedule.
 *
 * @param int $timestamp The schedule will start on or after this time
 * @param string $schedule A cron-link schedule string
 * @see http://en.wikipedia.org/wiki/Cron
 *   *    *    *    *    *    *
 *   ┬    ┬    ┬    ┬    ┬    ┬
 *   |    |    |    |    |    |
 *   |    |    |    |    |    + year [optional]
 *   |    |    |    |    +----- day of week (0 - 7) (Sunday=0 or 7)
 *   |    |    |    +---------- month (1 - 12)
 *   |    |    +--------------- day of month (1 - 31)
 *   |    +-------------------- hour (0 - 23)
 *   +------------------------- min (0 - 59)
 * @param string $hook The hook to trigger
 * @param array $args Arguments to pass when the hook triggers
 * @param string $group The group to assign this job to
 *
 * @deprecated 2.1.0
 *
 * @return string The job ID
 */
function wc_schedule_cron_action( $timestamp, $schedule, $hook, $args = array(), $group = '' ) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'as_schedule_cron_action()' );
	return as_schedule_cron_action( $timestamp, $schedule, $hook, $args, $group );
}

/**
 * Cancel the next occurrence of a job.
 *
 * @param string $hook The hook that the job will trigger
 * @param array $args Args that would have been passed to the job
 * @param string $group
 *
 * @deprecated 2.1.0
 */
function wc_unschedule_action( $hook, $args = array(), $group = '' ) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'as_unschedule_action()' );
	as_unschedule_action( $hook, $args, $group );
}

/**
 * @param string $hook
 * @param array $args
 * @param string $group
 *
 * @deprecated 2.1.0
 *
 * @return int|bool The timestamp for the next occurrence, or false if nothing was found
 */
function wc_next_scheduled_action( $hook, $args = NULL, $group = '' ) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'as_next_scheduled_action()' );
	return as_next_scheduled_action( $hook, $args, $group );
}

/**
 * Find scheduled actions
 *
 * @param array $args Possible arguments, with their default values:
 *        'hook' => '' - the name of the action that will be triggered
 *        'args' => NULL - the args array that will be passed with the action
 *        'date' => NULL - the scheduled date of the action. Expects a DateTime object, a unix timestamp, or a string that can parsed with strtotime(). Used in UTC timezone.
 *        'date_compare' => '<=' - operator for testing "date". accepted values are '!=', '>', '>=', '<', '<=', '='
 *        'modified' => NULL - the date the action was last updated. Expects a DateTime object, a unix timestamp, or a string that can parsed with strtotime(). Used in UTC timezone.
 *        'modified_compare' => '<=' - operator for testing "modified". accepted values are '!=', '>', '>=', '<', '<=', '='
 *        'group' => '' - the group the action belongs to
 *        'status' => '' - ActionScheduler_Store::STATUS_COMPLETE or ActionScheduler_Store::STATUS_PENDING
 *        'claimed' => NULL - TRUE to find claimed actions, FALSE to find unclaimed actions, a string to find a specific claim ID
 *        'per_page' => 5 - Number of results to return
 *        'offset' => 0
 *        'orderby' => 'date' - accepted values are 'hook', 'group', 'modified', or 'date'
 *        'order' => 'ASC'
 * @param string $return_format OBJECT, ARRAY_A, or ids
 *
 * @deprecated 2.1.0
 *
 * @return array
 */
function wc_get_scheduled_actions( $args = array(), $return_format = OBJECT ) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'as_get_scheduled_actions()' );
	return as_get_scheduled_actions( $args, $return_format );
}
