<?php

namespace Yoast\WP\SEO\Promotions\Domain;

/**
 * Class Time_Interval
 *
 * Value object for a time interval.
 */
class Time_Interval {

	/**
	 * The starting time of the interval as a Unix timestamp.
	 *
	 * @var int
	 */
	public $time_start;

	/**
	 * The ending time of the interval as a Unix timestamp.
	 *
	 * @var int
	 */
	public $time_end;

	/**
	 * Time_Interval constructor.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param int $time_start Interval start time.
	 * @param int $time_end   Interval end time.
	 */
	public function __construct( int $time_start, int $time_end ) {
		$this->time_start = $time_start;
		$this->time_end   = $time_end;
	}

	/**
	 * Checks if the given time is within the interval.
	 *
	 * @param int $time The time to check.
	 *
	 * @return bool Whether the given time is within the interval.
	 */
	public function contains( int $time ): bool {
		return ( ( $time > $this->time_start ) && ( $time < $this->time_end ) );
	}

	/**
	 * Sets the interval astarting date.
	 *
	 * @param int $time_start The interval start time.
	 *
	 * @return void
	 */
	public function set_start_date( int $time_start ) {
		$this->time_start = $time_start;
	}

	/**
	 * Sets the interval ending date.
	 *
	 * @param int $time_end The interval end time.
	 *
	 * @return void
	 */
	public function set_end_date( int $time_end ) {
		$this->time_end = $time_end;
	}
}
