<?php

/**
 * Class ActionScheduler_ActionFactory
 */
class ActionScheduler_ActionFactory {

	/**
	 * Return stored actions for given params.
	 *
	 * @param string                   $status The action's status in the data store.
	 * @param string                   $hook The hook to trigger when this action runs.
	 * @param array                    $args Args to pass to callbacks when the hook is triggered.
	 * @param ActionScheduler_Schedule $schedule The action's schedule.
	 * @param string                   $group A group to put the action in.
	 *
	 * @return ActionScheduler_Action An instance of the stored action.
	 */
	public function get_stored_action( $status, $hook, array $args = array(), ActionScheduler_Schedule $schedule = null, $group = '' ) {

		switch ( $status ) {
			case ActionScheduler_Store::STATUS_PENDING:
				$action_class = 'ActionScheduler_Action';
				break;
			case ActionScheduler_Store::STATUS_CANCELED:
				$action_class = 'ActionScheduler_CanceledAction';
				if ( ! is_null( $schedule ) && ! is_a( $schedule, 'ActionScheduler_CanceledSchedule' ) && ! is_a( $schedule, 'ActionScheduler_NullSchedule' ) ) {
					$schedule = new ActionScheduler_CanceledSchedule( $schedule->get_date() );
				}
				break;
			default:
				$action_class = 'ActionScheduler_FinishedAction';
				break;
		}

		$action_class = apply_filters( 'action_scheduler_stored_action_class', $action_class, $status, $hook, $args, $schedule, $group );

		$action = new $action_class( $hook, $args, $schedule, $group );

		/**
		 * Allow 3rd party code to change the instantiated action for a given hook, args, schedule and group.
		 *
		 * @param ActionScheduler_Action $action The instantiated action.
		 * @param string $hook The instantiated action's hook.
		 * @param array $args The instantiated action's args.
		 * @param ActionScheduler_Schedule $schedule The instantiated action's schedule.
		 * @param string $group The instantiated action's group.
		 */
		return apply_filters( 'action_scheduler_stored_action_instance', $action, $hook, $args, $schedule, $group );
	}

	/**
	 * Enqueue an action to run one time, as soon as possible (rather a specific scheduled time).
	 *
	 * This method creates a new action using the NullSchedule. In practice, this results in an action scheduled to
	 * execute "now". Therefore, it will generally run as soon as possible but is not prioritized ahead of other actions
	 * that are already past-due.
	 *
	 * @param string $hook The hook to trigger when this action runs.
	 * @param array  $args Args to pass when the hook is triggered.
	 * @param string $group A group to put the action in.
	 *
	 * @return int The ID of the stored action.
	 */
	public function async( $hook, $args = array(), $group = '' ) {
		return $this->async_unique( $hook, $args, $group, false );
	}

	/**
	 * Same as async, but also supports $unique param.
	 *
	 * @param string $hook The hook to trigger when this action runs.
	 * @param array  $args Args to pass when the hook is triggered.
	 * @param string $group A group to put the action in.
	 * @param bool   $unique Whether to ensure the action is unique.
	 *
	 * @return int The ID of the stored action.
	 */
	public function async_unique( $hook, $args = array(), $group = '', $unique = true ) {
		$schedule = new ActionScheduler_NullSchedule();
		$action   = new ActionScheduler_Action( $hook, $args, $schedule, $group );
		return $unique ? $this->store_unique_action( $action, $unique ) : $this->store( $action );
	}

	/**
	 * Create single action.
	 *
	 * @param string $hook  The hook to trigger when this action runs.
	 * @param array  $args  Args to pass when the hook is triggered.
	 * @param int    $when  Unix timestamp when the action will run.
	 * @param string $group A group to put the action in.
	 *
	 * @return int The ID of the stored action.
	 */
	public function single( $hook, $args = array(), $when = null, $group = '' ) {
		return $this->single_unique( $hook, $args, $when, $group, false );
	}

	/**
	 * Create single action only if there is no pending or running action with same name and params.
	 *
	 * @param string $hook The hook to trigger when this action runs.
	 * @param array  $args Args to pass when the hook is triggered.
	 * @param int    $when Unix timestamp when the action will run.
	 * @param string $group A group to put the action in.
	 * @param bool   $unique Whether action scheduled should be unique.
	 *
	 * @return int The ID of the stored action.
	 */
	public function single_unique( $hook, $args = array(), $when = null, $group = '', $unique = true ) {
		$date     = as_get_datetime_object( $when );
		$schedule = new ActionScheduler_SimpleSchedule( $date );
		$action   = new ActionScheduler_Action( $hook, $args, $schedule, $group );
		return $unique ? $this->store_unique_action( $action ) : $this->store( $action );
	}

	/**
	 * Create the first instance of an action recurring on a given interval.
	 *
	 * @param string $hook The hook to trigger when this action runs.
	 * @param array  $args Args to pass when the hook is triggered.
	 * @param int    $first Unix timestamp for the first run.
	 * @param int    $interval Seconds between runs.
	 * @param string $group A group to put the action in.
	 *
	 * @return int The ID of the stored action.
	 */
	public function recurring( $hook, $args = array(), $first = null, $interval = null, $group = '' ) {
		return $this->recurring_unique( $hook, $args, $first, $interval, $group, false );
	}

	/**
	 * Create the first instance of an action recurring on a given interval only if there is no pending or running action with same name and params.
	 *
	 * @param string $hook The hook to trigger when this action runs.
	 * @param array  $args Args to pass when the hook is triggered.
	 * @param int    $first Unix timestamp for the first run.
	 * @param int    $interval Seconds between runs.
	 * @param string $group A group to put the action in.
	 * @param bool   $unique Whether action scheduled should be unique.
	 *
	 * @return int The ID of the stored action.
	 */
	public function recurring_unique( $hook, $args = array(), $first = null, $interval = null, $group = '', $unique = true ) {
		if ( empty( $interval ) ) {
			return $this->single_unique( $hook, $args, $first, $group, $unique );
		}
		$date     = as_get_datetime_object( $first );
		$schedule = new ActionScheduler_IntervalSchedule( $date, $interval );
		$action   = new ActionScheduler_Action( $hook, $args, $schedule, $group );
		return $unique ? $this->store_unique_action( $action ) : $this->store( $action );
	}

	/**
	 * Create the first instance of an action recurring on a Cron schedule.
	 *
	 * @param string $hook The hook to trigger when this action runs.
	 * @param array  $args Args to pass when the hook is triggered.
	 * @param int    $base_timestamp The first instance of the action will be scheduled
	 *        to run at a time calculated after this timestamp matching the cron
	 *        expression. This can be used to delay the first instance of the action.
	 * @param int    $schedule A cron definition string.
	 * @param string $group A group to put the action in.
	 *
	 * @return int The ID of the stored action.
	 */
	public function cron( $hook, $args = array(), $base_timestamp = null, $schedule = null, $group = '' ) {
		return $this->cron_unique( $hook, $args, $base_timestamp, $schedule, $group, false );
	}


	/**
	 * Create the first instance of an action recurring on a Cron schedule only if there is no pending or running action with same name and params.
	 *
	 * @param string $hook The hook to trigger when this action runs.
	 * @param array  $args Args to pass when the hook is triggered.
	 * @param int    $base_timestamp The first instance of the action will be scheduled
	 *        to run at a time calculated after this timestamp matching the cron
	 *        expression. This can be used to delay the first instance of the action.
	 * @param int    $schedule A cron definition string.
	 * @param string $group A group to put the action in.
	 * @param bool   $unique Whether action scheduled should be unique.
	 *
	 * @return int The ID of the stored action.
	 **/
	public function cron_unique( $hook, $args = array(), $base_timestamp = null, $schedule = null, $group = '', $unique = true ) {
		if ( empty( $schedule ) ) {
			return $this->single_unique( $hook, $args, $base_timestamp, $group, $unique );
		}
		$date     = as_get_datetime_object( $base_timestamp );
		$cron     = CronExpression::factory( $schedule );
		$schedule = new ActionScheduler_CronSchedule( $date, $cron );
		$action   = new ActionScheduler_Action( $hook, $args, $schedule, $group );
		return $unique ? $this->store_unique_action( $action ) : $this->store( $action );
	}

	/**
	 * Create a successive instance of a recurring or cron action.
	 *
	 * Importantly, the action will be rescheduled to run based on the current date/time.
	 * That means when the action is scheduled to run in the past, the next scheduled date
	 * will be pushed forward. For example, if a recurring action set to run every hour
	 * was scheduled to run 5 seconds ago, it will be next scheduled for 1 hour in the
	 * future, which is 1 hour and 5 seconds from when it was last scheduled to run.
	 *
	 * Alternatively, if the action is scheduled to run in the future, and is run early,
	 * likely via manual intervention, then its schedule will change based on the time now.
	 * For example, if a recurring action set to run every day, and is run 12 hours early,
	 * it will run again in 24 hours, not 36 hours.
	 *
	 * This slippage is less of an issue with Cron actions, as the specific run time can
	 * be set for them to run, e.g. 1am each day. In those cases, and entire period would
	 * need to be missed before there was any change is scheduled, e.g. in the case of an
	 * action scheduled for 1am each day, the action would need to run an entire day late.
	 *
	 * @param ActionScheduler_Action $action The existing action.
	 *
	 * @return string The ID of the stored action
	 * @throws InvalidArgumentException If $action is not a recurring action.
	 */
	public function repeat( $action ) {
		$schedule = $action->get_schedule();
		$next     = $schedule->get_next( as_get_datetime_object() );

		if ( is_null( $next ) || ! $schedule->is_recurring() ) {
			throw new InvalidArgumentException( __( 'Invalid action - must be a recurring action.', 'woocommerce' ) );
		}

		$schedule_class = get_class( $schedule );
		$new_schedule   = new $schedule( $next, $schedule->get_recurrence(), $schedule->get_first_date() );
		$new_action     = new ActionScheduler_Action( $action->get_hook(), $action->get_args(), $new_schedule, $action->get_group() );
		return $this->store( $new_action );
	}

	/**
	 * Save action to database.
	 *
	 * @param ActionScheduler_Action $action Action object to save.
	 *
	 * @return int The ID of the stored action
	 */
	protected function store( ActionScheduler_Action $action ) {
		$store = ActionScheduler_Store::instance();
		return $store->save_action( $action );
	}

	/**
	 * Store action if it's unique.
	 *
	 * @param ActionScheduler_Action $action Action object to store.
	 *
	 * @return int ID of the created action. Will be 0 if action was not created.
	 */
	protected function store_unique_action( ActionScheduler_Action $action ) {
		$store = ActionScheduler_Store::instance();
		return method_exists( $store, 'save_unique_action' ) ?
			$store->save_unique_action( $action ) : $store->save_action( $action );
	}
}
