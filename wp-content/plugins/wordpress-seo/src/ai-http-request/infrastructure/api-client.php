<?php

namespace Yoast\WP\SEO\AI_HTTP_Request\Infrastructure;

use WPSEO_Utils;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\WP_Request_Exception;

/**
 * Class API_Client
 * Handles the API requests to the AI Generator API.
 *
 * @makePublic
 */
class API_Client implements API_Client_Interface {

	/**
	 * The base URL for the API.
	 *
	 * @var string
	 */
	private $base_url = 'https://ai.yoa.st/api/v1';

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
	public function perform_request( string $action_path, $body, $headers, bool $is_post ): array {
		// Our API expects JSON.
		// The request times out after 30 seconds.
		$headers   = \array_merge( $headers, [ 'Content-Type' => 'application/json' ] );
		$arguments = [
			'timeout' => $this->get_request_timeout(),
			'headers' => $headers,
		];

		if ( $is_post ) {
			// phpcs:ignore Yoast.Yoast.JsonEncodeAlternative.Found -- Reason: We don't want the debug/pretty possibility.
			$arguments['body'] = WPSEO_Utils::format_json_encode( $body );
		}

		/**
		 * Filter: 'Yoast\WP\SEO\ai_api_url' - Replaces the default URL for the AI API with a custom one.
		 *
		 * @internal
		 *
		 * @param string $url The default URL for the AI API.
		 */
		$url      = \apply_filters( 'Yoast\WP\SEO\ai_api_url', $this->base_url );
		$response = ( $is_post ) ? \wp_remote_post( $url . $action_path, $arguments ) : \wp_remote_get( $url . $action_path, $arguments );

		if ( \is_wp_error( $response ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- false positive.
			throw new WP_Request_Exception( $response->get_error_message() );
		}

		return $response;
	}

	/**
	 * Gets the timeout of the requests in seconds.
	 *
	 * @return int The timeout of the suggestion requests in seconds.
	 */
	public function get_request_timeout(): int {
		/**
		 * Filter: 'Yoast\WP\SEO\ai_suggestions_timeout' - Replaces the default timeout with a custom one, for testing purposes.
		 *
		 * @since 22.7
		 * @internal
		 *
		 * @param int $timeout The default timeout in seconds.
		 */
		return (int) \apply_filters( 'Yoast\WP\SEO\ai_suggestions_timeout', 60 );
	}
}
