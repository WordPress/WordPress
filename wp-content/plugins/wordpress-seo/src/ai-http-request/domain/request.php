<?php

namespace Yoast\WP\SEO\AI_HTTP_Request\Domain;

/**
 * Class Request
 * Represents a request to the AI Generator API.
 */
class Request {

	/**
	 * The action path for the request.
	 *
	 * @var string
	 */
	private $action_path;

	/**
	 * The body of the request.
	 *
	 * @var array<string>
	 */
	private $body;

	/**
	 * The headers for the request.
	 *
	 * @var array<string>
	 */
	private $headers;

	/**
	 * Whether the request is a POST request.
	 *
	 * @var bool
	 */
	private $is_post;

	/**
	 * Constructor for the Request class.
	 *
	 * @param string        $action_path The action path for the request.
	 * @param array<string> $body        The body of the request.
	 * @param array<string> $headers     The headers for the request.
	 * @param bool          $is_post     Whether the request is a POST request. Default is true.
	 */
	public function __construct( string $action_path, array $body = [], array $headers = [], bool $is_post = true ) {
		$this->action_path = $action_path;
		$this->body        = $body;
		$this->headers     = $headers;
		$this->is_post     = $is_post;
	}

	/**
	 * Get the action path for the request.
	 *
	 * @return string The action path for the request.
	 */
	public function get_action_path(): string {
		return $this->action_path;
	}

	/**
	 * Get the body of the request.
	 *
	 * @return array<string> The body of the request.
	 */
	public function get_body(): array {
		return $this->body;
	}

	/**
	 * Get the headers for the request.
	 *
	 * @return array<string> The headers for the request.
	 */
	public function get_headers(): array {
		return $this->headers;
	}

	/**
	 * Whether the request is a POST request.
	 *
	 * @return bool True if the request is a POST request, false otherwise.
	 */
	public function is_post(): bool {
		return $this->is_post;
	}
}
