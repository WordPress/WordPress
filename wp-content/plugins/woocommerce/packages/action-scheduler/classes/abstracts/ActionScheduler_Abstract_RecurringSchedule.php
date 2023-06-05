<?php

/**
 * Class ActionScheduler_Abstract_RecurringSchedule
 */
abstract class ActionScheduler_Abstract_RecurringSchedule extends ActionScheduler_Abstract_Schedule {

	/**
	 * The date & time the first instance of this schedule was setup to run (which may not be this instance).
	 *
	 * Schedule objects are attached to an action object. Each schedule stores the run date for that
	 * object as the start date - @see $this->start - and logic to calculate the next run date after
	 * that - @see $this->calculate_next(). The $first_date property also keeps a record of when the very
	 * first instance of this chain of schedules ran.
	 *
	 * @var DateTime
	 */
	private $first_date = NULL;

	/**
	 * Timestamp equivalent of @see $this->first_date
	 *
	 * @var int
	 */
	protected $first_timestamp = NULL;

	/**
	 * The recurrance between each time an action is run using this schedule.
	 * Used to calculate the start date & time. Can be a number of seconds, in the
	 * case of ActionScheduler_IntervalSchedule, or a cron expression, as in the
	 * case of ActionScheduler_CronSchedule. Or something else.
	 *
	 * @var mixed
	 */
	protected $recurrence;

	/**
	 * @param DateTime $date The date & time to run the action.
	 * @param mixed $recurrence The data used to determine the schedule's recurrance.
	 * @param DateTime|null $first (Optional) The date & time the first instance of this interval schedule ran. Default null, meaning this is the first instance.
	 */
	public function __construct( DateTime $date, $recurrence, DateTime $first = null ) {
		parent::__construct( $date );
		$this->first_date = empty( $first ) ? $date : $first;
		$this->recurrence = $recurrence;
	}

	/**
	 * @return bool
	 */
	public function is_recurring() {
		return true;
	}

	/**
	 * Get the date & time of the first schedule in this recurring series.
	 *
	 * @return DateTime|null
	 */
	public function get_first_date() {
		return clone $this->first_date;
	}

	/**
	 * @return string
	 */
	public function get_recurrence() {
		return $this->recurrence;
	}

	/**
	 * For PHP 5.2 compat, since DateTime objects can't be serialized
	 * @return array
	 */
	public function __sleep() {
		$sleep_params = parent::__sleep();
		$this->first_timestamp = $this->first_date->getTimestamp();
		return array_merge( $sleep_params, array(
			'first_timestamp',
			'recurrence'
		) );
	}

	/**
	 * Unserialize recurring schedules serialized/stored prior to AS 3.0.0
	 *
	 * Prior to Action Scheduler 3.0.0, schedules used different property names to refer
	 * to equivalent data. For example, ActionScheduler_IntervalSchedule::start_timestamp
	 * was the same as ActionScheduler_SimpleSchedule::timestamp. This was addressed in
	 * Action Scheduler 3.0.0, where properties and property names were aligned for better
	 * inheritance. To maintain backward compatibility with scheduled serialized and stored
	 * prior to 3.0, we need to correctly map the old property names.
	 */
	public function __wakeup() {
		parent::__wakeup();
		if ( $this->first_timestamp > 0 ) {
			$this->first_date = as_get_datetime_object( $this->first_timestamp );
		} else {
			$this->first_date = $this->get_date();
		}
	}
}
