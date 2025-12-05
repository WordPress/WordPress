<?php

namespace Yoast\WP\SEO\Promotions\Domain;

/**
 * Abstract class for a promotion.
 */
abstract class Abstract_Promotion implements Promotion_Interface {

	/**
	 * The promotion name.
	 *
	 * @var string
	 */
	private $promotion_name;

	/**
	 * The time interval in which the promotion is active.
	 *
	 * @var Time_Interval
	 */
	private $time_interval;

	/**
	 * Class constructor.
	 *
	 * @param string        $promotion_name The promotion name.
	 * @param Time_Interval $time_interval  The time interval in which the promotion is active.
	 */
	public function __construct( string $promotion_name, Time_Interval $time_interval ) {
		$this->promotion_name = $promotion_name;
		$this->time_interval  = $time_interval;
	}

	/**
	 * Returns the promotion name.
	 *
	 * @return string
	 */
	public function get_promotion_name() {
		return $this->promotion_name;
	}

	/**
	 * Returns the time interval in which the promotion is active.
	 *
	 * @return Time_Interval
	 */
	public function get_time_interval() {
		return $this->time_interval;
	}
}
