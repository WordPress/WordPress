<?php

namespace Yoast\WP\SEO\AI_HTTP_Request\Infrastructure;

use Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\WP_Request_Exception;

/**
 * Interface for the API client.
 */

interface API_Client_Interface {

	/**
	 * Performs a request to the API.
	 *
	 * @param string        $action_path The action path for the request.
	 * @param array<string> $body        The body of the request.
	 * @param array<string> $headers     The headers for the request.
	 * @param bool          $is_post     Whether the request is a POST request.
	 *
	 * @return array<int|string|array<string>> The response from the API.
	 *
	 * @throws WP_Request_Exception When the wp_remote_post() returns an error.
	 */
	public function perform_request( string $action_path, $body, $headers, bool $is_post ): array;

	/**
	 * Gets the timeout of the requests in seconds.
	 *
	 * @return int The timeout of the suggestion requests in seconds.
	 */
	public function get_request_timeout(): int;
}
