<?php

namespace Yoast\WP\SEO\AI_Authorization\Domain;

/**
 * Class Token
 * Represents a token used for authentication with the AI Generator API.
 */
class Token {

	/**
	 * The token value.
	 *
	 * @var string
	 */
	private $value;

	/**
	 * The expiration time.
	 *
	 * @var int
	 */
	private $expiration;

	/**
	 * Token constructor.
	 *
	 * @param string $value      The token value.
	 * @param int    $expiration The expiration time.
	 */
	public function __construct( string $value, int $expiration ) {
		$this->value      = $value;
		$this->expiration = $expiration;
	}

	/**
	 * Get the token value.
	 *
	 * @return string The token value.
	 */
	public function get_value(): string {
		return $this->value;
	}

	/**
	 * Whether the token is expired.
	 *
	 * @return bool True if the token is expired, false otherwise.
	 */
	public function is_expired(): bool {
		return $this->expiration < \time();
	}
}
