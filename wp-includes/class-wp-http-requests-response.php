<?php

/**
 * Wrapper object for a Requests_Response for compatibility.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 4.6.0
 */
class WP_HTTP_Requests_Response extends WP_HTTP_Response implements ArrayAccess {
	/**
	 * Requests Response object.
	 *
	 * @var Requests_Response
	 */
	protected $response;

	/**
	 * Filename the response was saved to.
	 *
	 * @var string|null
	 */
	protected $filename;

	/**
	 * Constructor.
	 */
	public function __construct( Requests_Response $response, $filename = '' ) {
		$this->response = $response;
		$this->filename = $filename;
	}

	/**
	 * Get the response object for the request.
	 *
	 * @return Requests_Response
	 */
	public function get_response_object() {
		return $this->response;
	}

	/**
	 * Retrieves headers associated with the response.
	 *
	 * @return array Map of header name to header value.
	 */
	public function get_headers() {
		// Ensure headers remain case-insensitive
		$converted = new Requests_Utility_CaseInsensitiveDictionary();

		foreach ( $this->response->headers->getAll() as $key => $value ) {
			if ( count( $value ) === 1 ) {
				$converted[ $key ] = $value[0];
			}
			else {
				$converted[ $key ] = $value;
			}
		}

		return $converted;
	}

	/**
	 * Sets all header values.
	 *
	 * @param array $headers Map of header name to header value.
	 */
	public function set_headers( $headers ) {
		$this->response->headers = new Requests_Response_Headers( $headers );
	}

	/**
	 * Sets a single HTTP header.
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
	 * @return int The 3-digit HTTP status code.
	 */
	public function get_status() {
		return $this->response->status_code;
	}

	/**
	 * Sets the 3-digit HTTP status code.
	 *
	 * @param int $code HTTP status.
	 */
	public function set_status( $code ) {
		$this->response->status_code = absint( $code );
	}

	/**
	 * Retrieves the response data.
	 *
	 * @return mixed Response data.
	 */
	public function get_data() {
		return $this->response->body;
	}

	/**
	 * Sets the response data.
	 *
	 * @param mixed $data Response data.
	 */
	public function set_data( $data ) {
		$this->response->body = $data;
	}

	/**
	 * Get cookies from the response.
	 *
	 * @return WP_HTTP_Cookie[] List of cookie objects.
	 */
	public function get_cookies() {
		$cookies = array();
		foreach ( $this->response->cookies as $cookie ) {
			$cookies[] = new WP_Http_Cookie( array(
				'name'    => $cookie->name,
				'value'   => urldecode( $cookie->value ),
				'expires' => $cookie->attributes['expires'],
				'path'    => $cookie->attributes['path'],
				'domain'  => $cookie->attributes['domain'],
			));
		}

		return $cookies;
	}

	/**
	 * Check if an ArrayAccess offset exists.
	 *
	 * This is for array access back-compat.
	 *
	 * @param string|int $key Array offset.
	 * @return bool True if the offset exists, false otherwise.
	 */
	public function offsetExists( $key ) {
		$allowed = array( 'headers', 'body', 'response', 'cookies', 'filename' );
		return in_array( $key, $allowed );
	}

	/**
	 * Get an ArrayAccess value.
	 *
	 * This is for array access back-compat.
	 *
	 * @param string|int $key Array offset to get.
	 * @return mixed Value if the key is a valid offset, null if invalid.
	 */
	public function offsetGet( $key ) {
		switch ( $key ) {
			case 'headers':
				return $this->get_headers();

			case 'body':
				return $this->get_data();

			case 'response':
				return array(
					'code'    => $this->get_status(),
					'message' => get_status_header_desc( $this->get_status() ),
				);

			case 'cookies':
				return $this->get_cookies();

			case 'filename':
				return $this->filename;
		}

		return null;
	}

	/**
	 * Set an ArrayAccess value.
	 *
	 * This is for array access back-compat.
	 *
	 * @param string|int $key Array offset to set.
	 * @param mixed $value Value to set.
	 */
	public function offsetSet( $key, $value ) {
		switch ( $key ) {
			case 'headers':
				$this->set_headers( $value );
				break;

			case 'body':
				$this->set_data( $value );
				break;

			case 'response':
				if ( isset( $value['code'] ) ) {
					$this->set_status( $value['code'] );
				}
				break;

			case 'filename':
				$this->filename = $value;
				break;
		}
	}

	/**
	 * Unset an ArrayAccess value.
	 *
	 * This is for array access back-compat.
	 *
	 * @param string|int $key Array offset to remove.
	 */
	public function offsetUnset( $key ) {
		$this->offsetSet( $key, null );
	}
}
