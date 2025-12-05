<?php

namespace Yoast\WP\SEO\AI_Generator\Domain;

/**
 * Class Suggestion
 * Represents a suggestion from the AI Generator API.
 */
class Suggestion {

	/**
	 * The suggestion text.
	 *
	 * @var string
	 */
	private $value;

	/**
	 * The constructor.
	 *
	 * @param string $value The suggestion text.
	 */
	public function __construct( string $value ) {
		$this->value = $value;
	}

	/**
	 * Returns the suggestion text.
	 *
	 * @return string
	 */
	public function get_value(): string {
		return $this->value;
	}
}
