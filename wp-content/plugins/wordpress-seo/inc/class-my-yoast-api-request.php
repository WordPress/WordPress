<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Inc
 */

/**
 * Handles requests to MyYoast.
 */
class WPSEO_MyYoast_Api_Request {

	/**
	 * The Request URL.
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * The request parameters.
	 *
	 * @var array
	 */
	protected $args = [
		'method'    => 'GET',
		'timeout'   => 5,
		'headers'   => [
			'Accept-Encoding' => '*',
			'Expect'          => '',
		],
	];

	/**
	 * Contains the fetched response.
	 *
	 * @var stdClass
	 */
	protected $response;

	/**
	 * Contains the error message when request went wrong.
	 *
	 * @var string
	 */
	protected $error_message = '';

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $url  The request url.
	 * @param array  $args The request arguments.
	 */
	public function __construct( $url, array $args = [] ) {
		$this->url  = 'https://my.yoast.com/api/' . $url;
		$this->args = wp_parse_args( $args, $this->args );
	}

	/**
	 * Fires the request.
	 *
	 * @return bool True when request is successful.
	 */
	public function fire() {
		try {
			$response       = $this->do_request( $this->url, $this->args );
			$response       = $this->decode_response( $response );
			$this->response = $this->validate_response( $response );
			return true;
		} catch ( WPSEO_MyYoast_Bad_Request_Exception $bad_request_exception ) {
			$this->error_message = $bad_request_exception->getMessage();

			return false;
		}
	}

	/**
	 * Retrieves the error message.
	 *
	 * @return string The set error message.
	 */
	public function get_error_message() {
		return $this->error_message;
	}

	/**
	 * Retrieves the response.
	 *
	 * @return stdClass The response object.
	 */
	public function get_response() {
		return $this->response;
	}

	/**
	 * Performs the request using WordPress internals.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $url               The request URL.
	 * @param array  $request_arguments The request arguments.
	 *
	 * @return string                                 The retrieved body.
	 * @throws WPSEO_MyYoast_Bad_Request_Exception    When request is invalid.
	 */
	protected function do_request( $url, $request_arguments ) {
		$request_arguments = $this->enrich_request_arguments( $request_arguments );
		$response          = wp_remote_request( $url, $request_arguments );

		if ( is_wp_error( $response ) ) {
			throw new WPSEO_MyYoast_Bad_Request_Exception( $response->get_error_message() );
		}

		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		// Do nothing, response code is okay.
		if ( $response_code === 200 ) {
			return wp_remote_retrieve_body( $response );
		}

		throw new WPSEO_MyYoast_Bad_Request_Exception( esc_html( $response_message ), (int) $response_code );
	}

	/**
	 * Decodes the JSON encoded response.
	 *
	 * @param string $response The response to decode.
	 *
	 * @return stdClass                             The json decoded response.
	 * @throws WPSEO_MyYoast_Invalid_JSON_Exception When decoded string is not a JSON object.
	 */
	protected function decode_response( $response ) {
		$response = json_decode( $response );

		if ( ! is_object( $response ) ) {
			throw new WPSEO_MyYoast_Invalid_JSON_Exception(
				esc_html__( 'No JSON object was returned.', 'wordpress-seo' )
			);
		}

		return $response;
	}

	/**
	 * Validates that all the needed fields are in de decoded response.
	 *
	 * @param stdClass $response The response to validate.
	 *
	 * @return stdClass                             The json decoded response.
	 * @throws WPSEO_MyYoast_Invalid_JSON_Exception When not all needed fields are found.
	 */
	private function validate_response( $response ) {
		if ( isset( $response->url, $response->subscriptions ) && is_array( $response->subscriptions ) ) {
			return $response;
		}

		throw new WPSEO_MyYoast_Invalid_JSON_Exception(
			esc_html__( 'Not all needed fields are present.', 'wordpress-seo' )
		);
	}

	/**
	 * Checks if MyYoast tokens are allowed and adds the token to the request body.
	 *
	 * When tokens are disallowed it will add the url to the request body.
	 *
	 * @param array $request_arguments The arguments to enrich.
	 *
	 * @return array The enriched arguments.
	 */
	protected function enrich_request_arguments( array $request_arguments ) {
		$request_arguments     = wp_parse_args( $request_arguments, [ 'headers' => [] ] );
		$addon_version_headers = $this->get_installed_addon_versions();

		foreach ( $addon_version_headers as $addon => $version ) {
			$request_arguments['headers'][ $addon . '-version' ] = $version;
		}

		$request_body = $this->get_request_body();
		if ( $request_body !== [] ) {
			$request_arguments['body'] = $request_body;
		}

		return $request_arguments;
	}

	/**
	 * Retrieves the request body based on URL or access token support.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array The request body.
	 */
	public function get_request_body() {
		return [ 'url' => WPSEO_Utils::get_home_url() ];
	}

	/**
	 * Wraps the get current user id function.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return int The user id.
	 */
	protected function get_current_user_id() {
		return get_current_user_id();
	}

	/**
	 * Retrieves the installed addons as http headers.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array The installed addon versions.
	 */
	protected function get_installed_addon_versions() {
		$addon_manager = new WPSEO_Addon_Manager();

		return $addon_manager->get_installed_addons_versions();
	}
}
