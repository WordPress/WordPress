<?php

namespace Yoast\WP\SEO\Analytics\Domain;

/**
 * The to be cleaned indexable domain object.
 */
class To_Be_Cleaned_Indexable_Count {

	/**
	 * The cleanup task that is represented by this.
	 *
	 * @var string
	 */
	private $cleanup_name;

	/**
	 * The amount of missing indexables.
	 *
	 * @var int
	 */
	private $count;

	/**
	 * The constructor.
	 *
	 * @param string $cleanup_name The indexable type that is represented by this.
	 * @param int    $count        The amount of missing indexables.
	 */
	public function __construct( $cleanup_name, $count ) {
		$this->cleanup_name = $cleanup_name;
		$this->count        = $count;
	}

	/**
	 * Returns an array representation of the data.
	 *
	 * @return array Returns both values in an array format.
	 */
	public function to_array() {
		return [
			'cleanup_name' => $this->get_cleanup_name(),
			'count'        => $this->get_count(),
		];
	}

	/**
	 * Gets the name.
	 *
	 * @return string
	 */
	public function get_cleanup_name() {
		return $this->cleanup_name;
	}

	/**
	 * Gets the count.
	 *
	 * @return int Returns the amount of missing indexables.
	 */
	public function get_count() {
		return $this->count;
	}
}
