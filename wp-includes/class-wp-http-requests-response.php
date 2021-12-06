<?php
/**
 * HTTP API: WP_HTTP_Requests_Response class
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 4.6.0
 */

/**
 * Core wrapper object for a Requests_Response for standardisation.
 *
 * @since 4.6.0
 *
 * @see WP_HTTP_Response
 */
class WP_HTTP_Requests_Response extends WP_HTTP_Response {
	/**
	 * Requests Response object.
	 *
	 * @since 4.6.0
	 * @var Requests_Response
	 */
	protected $response;

	/**
	 * Filename the response was saved to.
	 *
	 * @since 4.6.0
	 * @var string|null
	 */
	protected $filename;

	/**
	 * Constructor.
	 *
	 * @since 4.6.0
	 *
	 * @param Requests_Response $response HTTP response.
	 * @param string            $filename Optional. File name. Default empty.
	 */
	public function __construct( Requests_Response $response, $filename = '' ) {
		$this->response = $response;
		$this->filename = $filename;
	}

	/**
	 * Retrieves the response object for the request.
	 *
	 * @since 4.6.0
	 *
	 * @return Requests_Response HTTP response.
	 */
	public function get_response_object() {
		return $this->response;
	}

	/**
	 * Retrieves headers associated with the response.
	 *
	 * @since 4.6.0
	 *
	 * @return \Requests_Utility_CaseInsensitiveDictionary Map of header name to header value.
	 */
	public function get_headers() {
		// Ensure headers remain case-insensitive.
		$converted = new Requests_Utility_CaseInsensitiveDictionary();

		foreach ( $this->response->headers->getAll() as $key => $value ) {
			if ( count( $value ) === 1 ) {
				$converted[ $key ] = $value[0];
			} else {
				$converted[ $key ] = $value;
			}
		}

		return $converted;
	}

	/**
	 * Sets all header values.
	 *
	 * @since 4.6.0
	 *
	 * @param array $headers Map of header name to header value.
	 */
	public function set_headers( $headers ) {
		$this->response->headers = new Requests_Response_Headers( $headers );
	}

	/**
	 * Sets a single HTTP header.
	 *
	 * @since 4.6.0
	 *
	 * @param string $key     Header name.
	 * @param string $value   Header value.
	 * @param bool   $replace Optional. Whether to replace an existing header of the same name.
	 *                        Default true.
	 */
	public function header( $key, $value, $replace = true ) {
		if ( $replace ) {
			unset( $this->response->headers[ $key ] );
		}

		$this->response->headers[ $key ] = $value;
	}

	/**
	 * Retrieves the HTTP return code for the response.
	 *
	 * @since 4.6.0
	 *
	 * @return int The 3-digit HTTP status code.
	 */
	public function get_status() {
		return $this->response->status_code;
	}

	/**
	 * Sets the 3-digit HTTP status code.
	 *
	 * @since 4.6.0
	 *
	 * @param int $code HTTP status.
	 */
	public function set_status( $code ) {
		$this->response->status_code = absint( $code );
	}

	/**
	 * Retrieves the response data.
	 *
	 * @since 4.6.0
	 *
	 * @return string Response data.
	 */
	public function get_data() {
		return $this->response->body;
	}

	/**
	 * Sets the response data.
	 *
	 * @since 4.6.0
	 *
	 * @param string $data Response data.
	 */
	public function set_data( $data ) {
		$this->response->body = $data;
	}

	/**
	 * Retrieves cookies from the response.
	 *
	 * @since 4.6.0
	 *
	 * @return WP_HTTP_Cookie[] List of cookie objects.
	 */
	public function get_cookies() {
		$cookies = array();
		foreach ( $this->response->cookies as $cookie ) {
			$cookies[] = new WP_Http_Cookie(
				array(
					'name'      => $cookie->name,
					'value'     => urldecode( $cookie->value ),
					'expires'   => isset( $cookie->attributes['expires'] ) ? $cookie->attributes['expires'] : null,
					'path'      => isset( $cookie->attributes['path'] ) ? $cookie->attributes['path'] : null,
					'domain'    => isset( $cookie->attributes['domain'] ) ? $cookie->attributes['domain'] : null,
					'host_only' => isset( $cookie->flags['host-only'] ) ? $cookie->flags['host-only'] : null,
				)
			);
		}

		return $cookies;
	}

	/**
	 * Converts the object to a WP_Http response array.
	 *
	 * @since 4.6.0
	 *
	 * @return array WP_Http response array, per WP_Http::request().
	 */
	public function to_array() {
		return array(
			'headers'  => $this->get_headers(),
			'body'     => $this->get_data(),
			'response' => array(
				'code'    => $this->get_status(),
				'message' => get_status_header_desc( $this->get_status() ),
			),
			'cookies'  => $this->get_cookies(),
			'filename' => $this->filename,
		);
	}
}
