<?php
/**
 * Queue Interface
 *
 * @version 3.5.0
 * @package WooCommerce\Interface
 */

/**
 * WC Queue Interface
 *
 * Functions that must be defined to implement an action/job/event queue.
 *
 * @version 3.5.0
 */
interface WC_Queue_Interface {

	/**
	 * Enqueue an action to run one time, as soon as possible
	 *
	 * @param string $hook The hook to trigger.
	 * @param array  $args Arguments to pass when the hook triggers.
	 * @param string $group The group to assign this job to.
	 * @return string The action ID
	 */
	public function add( $hook, $args = array(), $group = '' );

	/**
	 * Schedule an action to run once at some time in the future
	 *
	 * @param int    $timestamp When the job will run.
	 * @param string $hook The hook to trigger.
	 * @param array  $args Arguments to pass when the hook triggers.
	 * @param string $group The group to assign this job to.
	 * @return string The action ID
	 */
	public function schedule_single( $timestamp, $hook, $args = array(), $group = '' );

	/**
	 * Schedule a recurring action
	 *
	 * @param int    $timestamp When the first instance of the job will run.
	 * @param int    $interval_in_seconds How long to wait between runs.
	 * @param string $hook The hook to trigger.
	 * @param array  $args Arguments to pass when the hook triggers.
	 * @param string $group The group to assign this job to.
	 * @return string The action ID
	 */
	public function schedule_recurring( $timestamp, $interval_in_seconds, $hook, $args = array(), $group = '' );

	/**
	 * Schedule an action that recurs on a cron-like schedule.
	 *
	 * @param int    $timestamp The schedule will start on or after this time.
	 * @param string $cron_schedule A cron-link schedule string.
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
	 * @param string $hook The hook to trigger.
	 * @param array  $args Arguments to pass when the hook triggers.
	 * @param string $group The group to assign this job to.
	 * @return string The action ID
	 */
	public function schedule_cron( $timestamp, $cron_schedule, $hook, $args = array(), $group = '' );

	/**
	 * Dequeue the next scheduled instance of an action with a matching hook (and optionally matching args and group).
	 *
	 * Any recurring actions with a matching hook should also be cancelled, not just the next scheduled action.
	 *
	 * @param string $hook The hook that the job will trigger.
	 * @param array  $args Args that would have been passed to the job.
	 * @param string $group The group the job is assigned to (if any).
	 */
	public function cancel( $hook, $args = array(), $group = '' );

	/**
	 * Dequeue all actions with a matching hook (and optionally matching args and group) so no matching actions are ever run.
	 *
	 * @param string $hook The hook that the job will trigger.
	 * @param array  $args Args that would have been passed to the job.
	 * @param string $group The group the job is assigned to (if any).
	 */
	public function cancel_all( $hook, $args = array(), $group = '' );

	/**
	 * Get the date and time for the next scheduled occurence of an action with a given hook
	 * (an optionally that matches certain args and group), if any.
	 *
	 * @param string $hook The hook that the job will trigger.
	 * @param array  $args Filter to a hook with matching args that will be passed to the job when it runs.
	 * @param string $group Filter to only actions assigned to a specific group.
	 * @return WC_DateTime|null The date and time for the next occurrence, or null if there is no pending, scheduled action for the given hook
	 */
	public function get_next( $hook, $args = null, $group = '' );

	/**
	 * Find scheduled actions.
	 *
	 * @param array  $args Possible arguments, with their default values.
	 *        'hook' => '' - the name of the action that will be triggered.
	 *        'args' => null - the args array that will be passed with the action.
	 *        'date' => null - the scheduled date of the action. Expects a DateTime object, a unix timestamp, or a string that can parsed with strtotime(). Used in UTC timezone.
	 *        'date_compare' => '<=' - operator for testing "date". accepted values are '!=', '>', '>=', '<', '<=', '='.
	 *        'modified' => null - the date the action was last updated. Expects a DateTime object, a unix timestamp, or a string that can parsed with strtotime(). Used in UTC timezone.
	 *        'modified_compare' => '<=' - operator for testing "modified". accepted values are '!=', '>', '>=', '<', '<=', '='.
	 *        'group' => '' - the group the action belongs to.
	 *        'status' => '' - ActionScheduler_Store::STATUS_COMPLETE or ActionScheduler_Store::STATUS_PENDING.
	 *        'claimed' => null - TRUE to find claimed actions, FALSE to find unclaimed actions, a string to find a specific claim ID.
	 *        'per_page' => 5 - Number of results to return.
	 *        'offset' => 0.
	 *        'orderby' => 'date' - accepted values are 'hook', 'group', 'modified', or 'date'.
	 *        'order' => 'ASC'.
	 * @param string $return_format OBJECT, ARRAY_A, or ids.
	 * @return array
	 */
	public function search( $args = array(), $return_format = OBJECT );
}
