<?php

namespace Yoast\WP\SEO\Introductions\Domain;

/**
 * A collection domain object.
 */
class Introductions_Bucket {

	/**
	 * Holds the introductions.
	 *
	 * @var Introduction_Item[]
	 */
	private $introductions;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->introductions = [];
	}

	/**
	 * Adds an introduction to this bucket.
	 *
	 * @param Introduction_Item $introduction The introduction.
	 *
	 * @return void
	 */
	public function add_introduction( Introduction_Item $introduction ) {
		$this->introductions[] = $introduction;
	}

	/**
	 * Returns the array representation of the introductions.
	 *
	 * @return array
	 */
	public function to_array() {
		// No sorting here because that is done in JS.
		return \array_map(
			static function ( $item ) {
				return $item->to_array();
			},
			$this->introductions
		);
	}
}
