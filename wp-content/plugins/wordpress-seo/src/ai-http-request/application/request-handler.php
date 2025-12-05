<?php

namespace Yoast\WP\SEO\AI_HTTP_Request\Application;

use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Bad_Request_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Forbidden_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Internal_Server_Error_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Not_Found_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Payment_Required_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Request_Timeout_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Service_Unavailable_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Too_Many_Requests_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Unauthorized_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\WP_Request_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Request;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Response;
use Yoast\WP\SEO\AI_HTTP_Request\Infrastructure\API_Client;

/**
 * Class Request_Handler
 * Handles the request to Yoast AI API.
 *
 * @makePublic
 */
class Request_Handler implements Request_Handler_Interface {

	private const TIMEOUT = 60;

	/**
	 * The API client.
	 *
	 * @var API_Client
	 */
	private $api_client;

	/**
	 * The response parser.
	 *
	 * @var Response_Parser
	 */
	private $response_parser;

	/**
	 * Request_Handler constructor.
	 *
	 * @param API_Client      $api_client      The API client.
	 * @param Response_Parser $response_parser The response parser.
	 */
	public function __construct( API_Client $api_client, Response_Parser $response_parser ) {
		$this->api_client      = $api_client;
		$this->response_parser = $response_parser;
	}

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber -- PHPCS doesn't take into account exceptions thrown in called methods.

	/**
	 * Executes the request to the API.
	 *
	 * @param Request $request The request to execute.
	 *
	 * @return Response The response from the API.
	 *
	 * @throws Bad_Request_Exception When the request fails for any other reason.
	 * @throws Forbidden_Exception When the response code is 403.
	 * @throws Internal_Server_Error_Exception When the response code is 500.
	 * @throws Not_Found_Exception When the response code is 404.
	 * @throws Payment_Required_Exception When the response code is 402.
	 * @throws Request_Timeout_Exception When the response code is 408.
	 * @throws Service_Unavailable_Exception When the response code is 503.
	 * @throws Too_Many_Requests_Exception When the response code is 429.
	 * @throws Unauthorized_Exception When the response code is 401.
	 * @throws WP_Request_Exception When the request fails for any other reason.
	 */
	public function handle( Request $request ): Response {
				$api_response = $this->api_client->perform_request(
					$request->get_action_path(),
					$request->get_body(),
					$request->get_headers(),
					$request->is_post()
				);

		$response = $this->response_parser->parse( $api_response );

		// phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped -- false positive.
		switch ( $response->get_response_code() ) {
			case 200:
				return $response;
			case 401:
				throw new Unauthorized_Exception( $response->get_message(), $response->get_response_code(), $response->get_error_code() );
			case 402:
				throw new Payment_Required_Exception( $response->get_message(), $response->get_response_code(), $response->get_error_code(), null, $response->get_missing_licenses() );
			case 403:
				throw new Forbidden_Exception( $response->get_message(), $response->get_response_code(), $response->get_error_code() );
			case 404:
				throw new Not_Found_Exception( $response->get_message(), $response->get_response_code(), $response->get_error_code() );
			case 408:
				throw new Request_Timeout_Exception( $response->get_message(), $response->get_response_code(), $response->get_error_code() );
			case 429:
				throw new Too_Many_Requests_Exception( $response->get_message(), $response->get_response_code(), $response->get_error_code(), null, $response->get_missing_licenses() );
			case 500:
				throw new Internal_Server_Error_Exception( $response->get_message(), $response->get_response_code(), $response->get_error_code() );
			case 503:
				throw new Service_Unavailable_Exception( $response->get_message(), $response->get_response_code(), $response->get_error_code() );
			default:
				throw new Bad_Request_Exception( $response->get_message(), $response->get_response_code(), $response->get_error_code() );
		}
		// phpcs:enable WordPress.Security.EscapeOutput.ExceptionNotEscaped
	}

	// phpcs:enable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber
}
