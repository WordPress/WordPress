<?php
/**
 * HTTP response class
 *
 * Contains a response from Requests::request()
 * @package Requests
 */

/**
 * HTTP response class
 *
 * Contains a response from Requests::request()
 * @package Requests
 */
class Requests_Response {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->headers = new Requests_Response_Headers();
		$this->cookies = new Requests_Cookie_Jar();
	}

	/**
	 * Response body
	 *
	 * @var string
	 */
	public $body = '';

	/**
	 * Raw HTTP data from the transport
	 *
	 * @var string
	 */
	public $raw = '';

	/**
	 * Headers, as an associative array
	 *
	 * @var Requests_Response_Headers Array-like object representing headers
	 */
	public $headers = array();

	/**
	 * Status code, false if non-blocking
	 *
	 * @var integer|boolean
	 */
	public $status_code = false;

	/**
	 * Protocol version, false if non-blocking
	 *
	 * @var float|boolean
	 */
	public $protocol_version = false;

	/**
	 * Whether the request succeeded or not
	 *
	 * @var boolean
	 */
	public $success = false;

	/**
	 * Number of redirects the request used
	 *
	 * @var integer
	 */
	public $redirects = 0;

	/**
	 * URL requested
	 *
	 * @var string
	 */
	public $url = '';

	/**
	 * Previous requests (from redirects)
	 *
	 * @var array Array of Requests_Response objects
	 */
	public $history = array();

	/**
	 * Cookies from the request
	 *
	 * @var Requests_Cookie_Jar Array-like object representing a cookie jar
	 */
	public $cookies = array();

	/**
	 * Is the response a redirect?
	 *
	 * @return boolean True if redirect (3xx status), false if not.
	 */
	public function is_redirect() {
		$code = $this->status_code;
		return in_array($code, array(300, 301, 302, 303, 307), true) || $code > 307 && $code < 400;
	}

	/**
	 * Throws an exception if the request was not successful
	 *
	 * @throws Requests_Exception If `$allow_redirects` is false, and code is 3xx (`response.no_redirects`)
	 * @throws Requests_Exception_HTTP On non-successful status code. Exception class corresponds to code (e.g. {@see Requests_Exception_HTTP_404})
	 * @param boolean $allow_redirects Set to false to throw on a 3xx as well
	 */
	public function throw_for_status($allow_redirects = true) {
		if ($this->is_redirect()) {
			if (!$allow_redirects) {
				throw new Requests_Exception('Redirection not allowed', 'response.no_redirects', $this);
			}
		}
		elseif (!$this->success) {
			$exception = Requests_Exception_HTTP::get_class($this->status_code);
			throw new $exception(null, $this);
		}
	}
}
