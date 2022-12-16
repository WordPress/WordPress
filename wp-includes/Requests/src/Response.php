<?php
/**
 * HTTP response class
 *
 * Contains a response from \WpOrg\Requests\Requests::request()
 *
 * @package Requests
 */

namespace WpOrg\Requests;

use WpOrg\Requests\Cookie\Jar;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\Http;
use WpOrg\Requests\Response\Headers;

/**
 * HTTP response class
 *
 * Contains a response from \WpOrg\Requests\Requests::request()
 *
 * @package Requests
 */
class Response {

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
	 * @var \WpOrg\Requests\Response\Headers Array-like object representing headers
	 */
	public $headers = [];

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
	 * @var array Array of \WpOrg\Requests\Response objects
	 */
	public $history = [];

	/**
	 * Cookies from the request
	 *
	 * @var \WpOrg\Requests\Cookie\Jar Array-like object representing a cookie jar
	 */
	public $cookies = [];

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->headers = new Headers();
		$this->cookies = new Jar();
	}

	/**
	 * Is the response a redirect?
	 *
	 * @return boolean True if redirect (3xx status), false if not.
	 */
	public function is_redirect() {
		$code = $this->status_code;
		return in_array($code, [300, 301, 302, 303, 307], true) || $code > 307 && $code < 400;
	}

	/**
	 * Throws an exception if the request was not successful
	 *
	 * @param boolean $allow_redirects Set to false to throw on a 3xx as well
	 *
	 * @throws \WpOrg\Requests\Exception If `$allow_redirects` is false, and code is 3xx (`response.no_redirects`)
	 * @throws \WpOrg\Requests\Exception\Http On non-successful status code. Exception class corresponds to "Status" + code (e.g. {@see \WpOrg\Requests\Exception\Http\Status404})
	 */
	public function throw_for_status($allow_redirects = true) {
		if ($this->is_redirect()) {
			if ($allow_redirects !== true) {
				throw new Exception('Redirection not allowed', 'response.no_redirects', $this);
			}
		} elseif (!$this->success) {
			$exception = Http::get_class($this->status_code);
			throw new $exception(null, $this);
		}
	}

	/**
	 * JSON decode the response body.
	 *
	 * The method parameters are the same as those for the PHP native `json_decode()` function.
	 *
	 * @link https://php.net/json-decode
	 *
	 * @param ?bool $associative Optional. When `true`, JSON objects will be returned as associative arrays;
	 *                           When `false`, JSON objects will be returned as objects.
	 *                           When `null`, JSON objects will be returned as associative arrays
	 *                           or objects depending on whether `JSON_OBJECT_AS_ARRAY` is set in the flags.
	 *                           Defaults to `true` (in contrast to the PHP native default of `null`).
	 * @param int   $depth       Optional. Maximum nesting depth of the structure being decoded.
	 *                           Defaults to `512`.
	 * @param int   $options     Optional. Bitmask of JSON_BIGINT_AS_STRING, JSON_INVALID_UTF8_IGNORE,
	 *                           JSON_INVALID_UTF8_SUBSTITUTE, JSON_OBJECT_AS_ARRAY, JSON_THROW_ON_ERROR.
	 *                           Defaults to `0` (no options set).
	 *
	 * @return array
	 *
	 * @throws \WpOrg\Requests\Exception If `$this->body` is not valid json.
	 */
	public function decode_body($associative = true, $depth = 512, $options = 0) {
		$data = json_decode($this->body, $associative, $depth, $options);

		if (json_last_error() !== JSON_ERROR_NONE) {
			$last_error = json_last_error_msg();
			throw new Exception('Unable to parse JSON data: ' . $last_error, 'response.invalid', $this);
		}

		return $data;
	}
}
