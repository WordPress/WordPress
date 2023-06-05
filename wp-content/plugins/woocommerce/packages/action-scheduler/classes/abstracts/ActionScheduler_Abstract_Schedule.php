<?php

/**
 * Class ActionScheduler_Abstract_Schedule
 */
abstract class ActionScheduler_Abstract_Schedule extends ActionScheduler_Schedule_Deprecated {

	/**
	 * The date & time the schedule is set to run.
	 *
	 * @var DateTime
	 */
	private $scheduled_date = NULL;

	/**
	 * Timestamp equivalent of @see $this->scheduled_date
	 *
	 * @var int
	 */
	protected $scheduled_timestamp = NULL;

	/**
	 * @param DateTime $date The date & time to run the action.
	 */
	public function __construct( DateTime $date ) {
		$this->scheduled_date = $date;
	}

	/**
	 * Check if a schedule should recur.
	 *
	 * @return bool
	 */
	abstract public function is_recurring();

	/**
	 * Calculate when the next instance of this schedule would run based on a given date & time.
	 *
	 * @param DateTime $after
	 * @return DateTime
	 */
	abstract protected function calculate_next( DateTime $after );

	/**
	 * Get the next date & time when this schedule should run after a given date & time.
	 *
	 * @param DateTime $after
	 * @return DateTime|null
	 */
	public function get_next( DateTime $after ) {
		$after = clone $after;
		if ( $after > $this->scheduled_date ) {
			$after = $this->calculate_next( $after );
			return $after;
		}
		return clone $this->scheduled_date;
	}

	/**
	 * Get the date & time the schedule is set to run.
	 *
	 * @return DateTime|null
	 */
	public function get_date() {
		return $this->scheduled_date;
	}

	/**
	 * For PHP 5.2 compat, since DateTime objects can't be serialized
	 * @return array
	 */
	public function __sleep() {
		$this->scheduled_timestamp = $this->scheduled_date->getTimestamp();
		return array(
			'scheduled_timestamp',
		);
	}

	public function __wakeup() {
		$this->scheduled_date = as_get_datetime_object( $this->scheduled_timestamp );
		unset( $this->scheduled_timestamp );
	}
}
