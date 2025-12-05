<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * This class handles a post request being send to a given endpoint.
 */
class WPSEO_Remote_Request {

	/**
	 * Holds the post method.
	 *
	 * @var string
	 */
	public const METHOD_POST = 'post';

	/**
	 * Holds the get method.
	 *
	 * @var string
	 */
	public const METHOD_GET = 'get';

	/**
	 * Holds the endpoint to send the request to.
	 *
	 * @var string
	 */
	protected $endpoint = '';

	/**
	 * Holds the arguments to use in this request.
	 *
	 * @var array
	 */
	protected $args = [
		'blocking'  => false,
		'timeout'   => 2,
	];

	/**
	 * Holds the response error.
	 *
	 * @var WP_Error|null
	 */
	protected $response_error;

	/**
	 * Holds the response body.
	 *
	 * @var mixed
	 */
	protected $response_body;

	/**
	 * Sets the endpoint and arguments.
	 *
	 * @param string $endpoint The endpoint to send the request to.
	 * @param array  $args     The arguments to use in this request.
	 */
	public function __construct( $endpoint, array $args = [] ) {
		$this->endpoint = $endpoint;
		$this->args     = wp_parse_args( $this->args, $args );
	}

	/**
	 * Sets the request body.
	 *
	 * @param mixed $body The body to set.
	 *
	 * @return void
	 */
	public function set_body( $body ) {
		$this->args['body'] = $body;
	}

	/**
	 * Sends the data to the given endpoint.
	 *
	 * @param string $method The type of request to send.
	 *
	 * @return bool True when sending data has been successful.
	 */
	public function send( $method = self::METHOD_POST ) {
		switch ( $method ) {
			case self::METHOD_POST:
				$response = $this->post();
				break;
			case self::METHOD_GET:
				$response = $this->get();
				break;
			default:
				/* translators: %1$s expands to the request method  */
				$response = new WP_Error( 1, sprintf( __( 'Request method %1$s is not valid.', 'wordpress-seo' ), $method ) );
				break;
		}

		return $this->process_response( $response );
	}

	/**
	 * Returns the value of the response error.
	 *
	 * @return WP_Error|null The response error.
	 */
	public function get_response_error() {
		return $this->response_error;
	}

	/**
	 * Returns the response body.
	 *
	 * @return mixed The response body.
	 */
	public function get_response_body() {
		return $this->response_body;
	}

	/**
	 * Processes the given response.
	 *
	 * @param mixed $response The response to process.
	 *
	 * @return bool True when response is valid.
	 */
	protected function process_response( $response ) {
		if ( $response instanceof WP_Error ) {
			$this->response_error = $response;

			return false;
		}

		$this->response_body = wp_remote_retrieve_body( $response );

		return ( wp_remote_retrieve_response_code( $response ) === 200 );
	}

	/**
	 * Performs a post request to the specified endpoint with set arguments.
	 *
	 * @return WP_Error|array The response or WP_Error on failure.
	 */
	protected function post() {
		return wp_remote_post( $this->endpoint, $this->args );
	}

	/**
	 * Performs a post request to the specified endpoint with set arguments.
	 *
	 * @return WP_Error|array The response or WP_Error on failure.
	 */
	protected function get() {
		return wp_remote_get( $this->endpoint, $this->args );
	}
}
