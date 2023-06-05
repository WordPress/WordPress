<?php

/**
 * Class ActionScheduler_SimpleSchedule
 */
class ActionScheduler_CanceledSchedule extends ActionScheduler_SimpleSchedule {

	/**
	 * Deprecated property @see $this->__wakeup() for details.
	 **/
	private $timestamp = NULL;

	/**
	 * @param DateTime $after
	 *
	 * @return DateTime|null
	 */
	public function calculate_next( DateTime $after ) {
		return null;
	}

	/**
	 * Cancelled actions should never have a next schedule, even if get_next()
	 * is called with $after < $this->scheduled_date.
	 *
	 * @param DateTime $after
	 * @return DateTime|null
	 */
	public function get_next( DateTime $after ) {
		return null;
	}

	/**
	 * @return bool
	 */
	public function is_recurring() {
		return false;
	}

	/**
	 * Unserialize recurring schedules serialized/stored prior to AS 3.0.0
	 *
	 * Prior to Action Scheduler 3.0.0, schedules used different property names to refer
	 * to equivalent data. For example, ActionScheduler_IntervalSchedule::start_timestamp
	 * was the same as ActionScheduler_SimpleSchedule::timestamp. Action Scheduler 3.0.0
	 * aligned properties and property names for better inheritance. To maintain backward
	 * compatibility with schedules serialized and stored prior to 3.0, we need to correctly
	 * map the old property names with matching visibility.
	 */
	public function __wakeup() {
		if ( ! is_null( $this->timestamp ) ) {
			$this->scheduled_timestamp = $this->timestamp;
			unset( $this->timestamp );
		}
		parent::__wakeup();
	}
}
