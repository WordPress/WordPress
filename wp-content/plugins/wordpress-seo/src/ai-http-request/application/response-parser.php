<?php
namespace Yoast\WP\SEO\AI_HTTP_Request\Application;

use Yoast\WP\SEO\AI_HTTP_Request\Domain\Response;

/**
 * Class Response_Parser
 * Parses the response from the AI API and creates a Response object.
 */
class Response_Parser implements Response_Parser_Interface {

	/**
	 * Parses the response from the API.
	 *
	 * @param array<int|string|array<string>> $response The response from the API.
	 *
	 * @return Response The parsed response.
	 */
	public function parse( $response ): Response {
		$response_code    = ( \wp_remote_retrieve_response_code( $response ) !== '' ) ? \wp_remote_retrieve_response_code( $response ) : 0;
		$response_message = \esc_html( \wp_remote_retrieve_response_message( $response ) );
		$error_code       = '';
		$missing_licenses = [];

		if ( $response_code !== 200 && $response_code !== 0 ) {
			$json_body = \json_decode( \wp_remote_retrieve_body( $response ) );
			if ( $json_body !== null ) {
				$response_message = ( $json_body->message ?? $response_message );
				$error_code       = ( $json_body->error_code ?? $this->map_message_to_code( $response_message ) );
				if ( $response_code === 402 || $response_code === 429 ) {
					$missing_licenses = isset( $json_body->missing_licenses ) ? (array) $json_body->missing_licenses : [];
				}
			}
		}

		return new Response( $response['body'], $response_code, $response_message, $error_code, $missing_licenses );
	}

	/**
	 * Maps the error message to a code.
	 *
	 * @param string $message The error message.
	 *
	 * @return string The mapped code.
	 */
	private function map_message_to_code( string $message ): string {
		if ( \strpos( $message, 'must NOT have fewer than 1 characters' ) !== false ) {
			return 'NOT_ENOUGH_CONTENT';
		}
		if ( \strpos( $message, 'Client timeout' ) !== false ) {
			return 'CLIENT_TIMEOUT';
		}
		if ( \strpos( $message, 'Server timeout' ) !== false ) {
			return 'SERVER_TIMEOUT';
		}

		return 'UNKNOWN';
	}
}
