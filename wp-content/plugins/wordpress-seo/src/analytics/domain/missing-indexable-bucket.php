<?php

namespace Yoast\WP\SEO\Analytics\Domain;

/**
 * A collection domain object.
 */
class Missing_Indexable_Bucket {

	/**
	 * All the missing indexable count objects.
	 *
	 * @var array<Missing_Indexable_Count>
	 */
	private $missing_indexable_counts;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->missing_indexable_counts = [];
	}

	/**
	 * Adds a missing indexable count object to this bucket.
	 *
	 * @param Missing_Indexable_Count $missing_indexable_count The missing indexable count object.
	 *
	 * @return void
	 */
	public function add_missing_indexable_count( Missing_Indexable_Count $missing_indexable_count ): void {
		$this->missing_indexable_counts[] = $missing_indexable_count;
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
			$this->missing_indexable_counts
		);
	}
}
