<?php

namespace Yoast\WP\SEO\Introductions\Domain;

/**
 * Domain object that holds introduction information.
 */
class Introduction_Item {

	/**
	 * The ID.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The priority.
	 *
	 * @var int
	 */
	private $priority;

	/**
	 * Constructs the instance.
	 *
	 * @param string $id       The ID.
	 * @param int    $priority The priority.
	 */
	public function __construct( $id, $priority ) {
		$this->id       = $id;
		$this->priority = $priority;
	}

	/**
	 * Returns an array representation of the data.
	 *
	 * @return array Returns in an array format.
	 */
	public function to_array() {
		return [
			'id'       => $this->get_id(),
			'priority' => $this->get_priority(),
		];
	}

	/**
	 * Returns the ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Returns the requested pagination priority. Higher means earlier.
	 *
	 * @return int
	 */
	public function get_priority() {
		return $this->priority;
	}
}
