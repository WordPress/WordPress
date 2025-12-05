<?php

namespace Yoast\WP\SEO\AI_Generator\Domain;

/**
 * Class Suggestion_Bucket
 * Represents a collection of Suggestion objects.
 */
class Suggestions_Bucket {

	/**
	 * The suggestions.
	 *
	 * @var array<Suggestion>
	 */
	private $suggestions;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->suggestions = [];
	}

	/**
	 * Adds a suggestion to the bucket.
	 *
	 * @param Suggestion $suggestion The suggestion to add.
	 *
	 * @return void
	 */
	public function add_suggestion( Suggestion $suggestion ) {
		$this->suggestions[] = $suggestion;
	}

	/**
	 * Returns the suggestions as an array.
	 *
	 * @return array<string>
	 */
	public function to_array() {
		return \array_map(
			static function ( $item ) {
				return $item->get_value();
			},
			$this->suggestions
		);
	}
}
