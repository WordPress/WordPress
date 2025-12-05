<?php

namespace Yoast\WP\SEO\Analytics\Domain;

/**
 * A collection domain object.
 */
class To_Be_Cleaned_Indexable_Bucket {

	/**
	 * All the to be cleaned indexable count objects.
	 *
	 * @var array<To_Be_Cleaned_Indexable_Count>
	 */
	private $to_be_cleaned_indexable_counts;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->to_be_cleaned_indexable_counts = [];
	}

	/**
	 * Adds a 'to be cleaned' indexable count object to this bucket.
	 *
	 * @param To_Be_Cleaned_Indexable_Count $to_be_cleaned_indexable_counts The to be cleaned indexable count object.
	 *
	 * @return void
	 */
	public function add_to_be_cleaned_indexable_count( To_Be_Cleaned_Indexable_Count $to_be_cleaned_indexable_counts ) {
		$this->to_be_cleaned_indexable_counts[] = $to_be_cleaned_indexable_counts;
	}

	/**
	 * Returns the array representation of all indexable counts.
	 *
	 * @return array
	 */
	public function to_array() {
		return \array_map(
			static function ( $item ) {
				return $item->to_array();
			},
			$this->to_be_cleaned_indexable_counts
		);
	}
}
