<?php

/**
 * Class ActionScheduler_IntervalSchedule
 */
class ActionScheduler_IntervalSchedule extends ActionScheduler_Abstract_RecurringSchedule implements ActionScheduler_Schedule {

	/**
	 * Deprecated property @see $this->__wakeup() for details.
	 **/
	private $start_timestamp = NULL;

	/**
	 * Deprecated property @see $this->__wakeup() for details.
	 **/
	private $interval_in_seconds = NULL;

	/**
	 * Calculate when this schedule should start after a given date & time using
	 * the number of seconds between recurrences.
	 *
	 * @param DateTime $after
	 * @return DateTime
	 */
	protected function calculate_next( DateTime $after ) {
		$after->modify( '+' . (int) $this->get_recurrence() . ' seconds' );
		return $after;
	}

	/**
	 * @return int
	 */
	public function interval_in_seconds() {
		_deprecated_function( __METHOD__, '3.0.0', '(int)ActionScheduler_Abstract_RecurringSchedule::get_recurrence()' );
		return (int) $this->get_recurrence();
	}

	/**
	 * Serialize interval schedules with data required prior to AS 3.0.0
	 *
	 * Prior to Action Scheduler 3.0.0, reccuring schedules used different property names to
	 * refer to equivalent data. For example, ActionScheduler_IntervalSchedule::start_timestamp
	 * was the same as ActionScheduler_SimpleSchedule::timestamp. Action Scheduler 3.0.0
	 * aligned properties and property names for better inheritance. To guard against the
	 * possibility of infinite loops if downgrading to Action Scheduler < 3.0.0, we need to
	 * also store the data with the old property names so if it's unserialized in AS < 3.0,
	 * the schedule doesn't end up with a null/false/0 recurrence.
	 *
	 * @return array
	 */
	public function __sleep() {

		$sleep_params = parent::__sleep();

		$this->start_timestamp     = $this->scheduled_timestamp;
		$this->interval_in_seconds = $this->recurrence;

		return array_merge( $sleep_params, array(
			'start_timestamp',
			'interval_in_seconds'
		) );
	}

	/**
	 * Unserialize interval schedules serialized/stored prior to AS 3.0.0
	 *
	 * For more background, @see ActionScheduler_Abstract_RecurringSchedule::__wakeup().
	 */
	public function __wakeup() {
		if ( is_null( $this->scheduled_timestamp ) && ! is_null( $this->start_timestamp ) ) {
			$this->scheduled_timestamp = $this->start_timestamp;
			unset( $this->start_timestamp );
		}

		if ( is_null( $this->recurrence ) && ! is_null( $this->interval_in_seconds ) ) {
			$this->recurrence = $this->interval_in_seconds;
			unset( $this->interval_in_seconds );
		}
		parent::__wakeup();
	}
}
