<?php
/**
 * Session handler for persistent requests and default parameters
 *
 * @package Requests\SessionHandler
 */

namespace WpOrg\Requests;

use WpOrg\Requests\Cookie\Jar;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Utility\InputValidator;

/**
 * Session handler for persistent requests and default parameters
 *
 * Allows various options to be set as default values, and merges both the
 * options and URL properties together. A base URL can be set for all requests,
 * with all subrequests resolved from this. Base options can be set (including
 * a shared cookie jar), then overridden for individual requests.
 *
 * @package Requests\SessionHandler
 */
class Session {
	/**
	 * Base URL for requests
	 *
	 * URLs will be made absolute using this as the base
	 *
	 * @var string|null
	 */
	public $url = null;

	/**
	 * Base headers for requests
	 *
	 * @var array
	 */
	public $headers = [];

	/**
	 * Base data for requests
	 *
	 * If both the base data and the per-request data are arrays, the data will
	 * be merged before sending the request.
	 *
	 * @var array
	 */
	public $data = [];

	/**
	 * Base options for requests
	 *
	 * The base options are merged with the per-request data for each request.
	 * The only default option is a shared cookie jar between requests.
	 *
	 * Values here can also be set directly via properties on the Session
	 * object, e.g. `$session->useragent = 'X';`
	 *
	 * @var array
	 */
	public $options = [];

	/**
	 * Create a new session
	 *
	 * @param string|Stringable|null $url Base URL for requests
	 * @param array $headers Default headers for requests
	 * @param array $data Default data for requests
	 * @param array $options Default options for requests
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $url argument is not a string, Stringable or null.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $headers argument is not an array.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $data argument is not an array.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $options argument is not an array.
	 */
	public function __construct($url = null, $headers = [], $data = [], $options = []) {
		if ($url !== null && InputValidator::is_string_or_stringable($url) === false) {
			throw InvalidArgument::create(1, '$url', 'string|Stringable|null', gettype($url));
		}

		if (is_array($headers) === false) {
			throw InvalidArgument::create(2, '$headers', 'array', gettype($headers));
		}

		if (is_array($data) === false) {
			throw InvalidArgument::create(3, '$data', 'array', gettype($data));
		}

		if (is_array($options) === false) {
			throw InvalidArgument::create(4, '$options', 'array', gettype($options));
		}

		$this->url     = $url;
		$this->headers = $headers;
		$this->data    = $data;
		$this->options = $options;

		if (empty($this->options['cookies'])) {
			$this->options['cookies'] = new Jar();
		}
	}

	/**
	 * Get a property's value
	 *
	 * @param string $name Property name.
	 * @return mixed|null Property value, null if none found
	 */
	public function __get($name) {
		if (isset($this->options[$name])) {
			return $this->options[$name];
		}

		return null;
	}

	/**
	 * Set a property's value
	 *
	 * @param string $name Property name.
	 * @param mixed $value Property value
	 */
	public function __set($name, $value) {
		$this->options[$name] = $value;
	}

	/**
	 * Remove a property's value
	 *
	 * @param string $name Property name.
	 */
	public function __isset($name) {
		return isset($this->options[$name]);
	}

	/**
	 * Remove a property's value
	 *
	 * @param string $name Property name.
	 */
	public function __unset($name) {
		unset($this->options[$name]);
	}

	/**#@+
	 * @see \WpOrg\Requests\Session::request()
	 * @param string $url
	 * @param array $headers
	 * @param array $options
	 * @return \WpOrg\Requests\Response
	 */
	/**
	 * Send a GET request
	 */
	public function get($url, $headers = [], $options = []) {
		return $this->request($url, $headers, null, Requests::GET, $options);
	}

	/**
	 * Send a HEAD request
	 */
	public function head($url, $headers = [], $options = []) {
		return $this->request($url, $headers, null, Requests::HEAD, $options);
	}

	/**
	 * Send a DELETE request
	 */
	public function delete($url, $headers = [], $options = []) {
		return $this->request($url, $headers, null, Requests::DELETE, $options);
	}
	/**#@-*/

	/**#@+
	 * @see \WpOrg\Requests\Session::request()
	 * @param string $url
	 * @param array $headers
	 * @param array $data
	 * @param array $options
	 * @return \WpOrg\Requests\Response
	 */
	/**
	 * Send a POST request
	 */
	public function post($url, $headers = [], $data = [], $options = []) {
		return $this->request($url, $headers, $data, Requests::POST, $options);
	}

	/**
	 * Send a PUT request
	 */
	public function put($url, $headers = [], $data = [], $options = []) {
		return $this->request($url, $headers, $data, Requests::PUT, $options);
	}

	/**
	 * Send a PATCH request
	 *
	 * Note: Unlike {@see \WpOrg\Requests\Session::post()} and {@see \WpOrg\Requests\Session::put()},
	 * `$headers` is required, as the specification recommends that should send an ETag
	 *
	 * @link https://tools.ietf.org/html/rfc5789
	 */
	public function patch($url, $headers, $data = [], $options = []) {
		return $this->request($url, $headers, $data, Requests::PATCH, $options);
	}
	/**#@-*/

	/**
	 * Main interface for HTTP requests
	 *
	 * This method initiates a request and sends it via a transport before
	 * parsing.
	 *
	 * @see \WpOrg\Requests\Requests::request()
	 *
	 * @param string $url URL to request
	 * @param array $headers Extra headers to send with the request
	 * @param array|null $data Data to send either as a query string for GET/HEAD requests, or in the body for POST requests
	 * @param string $type HTTP request type (use \WpOrg\Requests\Requests constants)
	 * @param array $options Options for the request (see {@see \WpOrg\Requests\Requests::request()})
	 * @return \WpOrg\Requests\Response
	 *
	 * @throws \WpOrg\Requests\Exception On invalid URLs (`nonhttp`)
	 */
	public function request($url, $headers = [], $data = [], $type = Requests::GET, $options = []) {
		$request = $this->merge_request(compact('url', 'headers', 'data', 'options'));

		return Requests::request($request['url'], $request['headers'], $request['data'], $type, $request['options']);
	}

	/**
	 * Send multiple HTTP requests simultaneously
	 *
	 * @see \WpOrg\Requests\Requests::request_multiple()
	 *
	 * @param array $requests Requests data (see {@see \WpOrg\Requests\Requests::request_multiple()})
	 * @param array $options Global and default options (see {@see \WpOrg\Requests\Requests::request()})
	 * @return array Responses (either \WpOrg\Requests\Response or a \WpOrg\Requests\Exception object)
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $requests argument is not an array or iterable object with array access.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $options argument is not an array.
	 */
	public function request_multiple($requests, $options = []) {
		if (InputValidator::has_array_access($requests) === false || InputValidator::is_iterable($requests) === false) {
			throw InvalidArgument::create(1, '$requests', 'array|ArrayAccess&Traversable', gettype($requests));
		}

		if (is_array($options) === false) {
			throw InvalidArgument::create(2, '$options', 'array', gettype($options));
		}

		foreach ($requests as $key => $request) {
			$requests[$key] = $this->merge_request($request, false);
		}

		$options = array_merge($this->options, $options);

		// Disallow forcing the type, as that's a per request setting
		unset($options['type']);

		return Requests::request_multiple($requests, $options);
	}

	public function __wakeup() {
		throw new \LogicException( __CLASS__ . ' should never be unserialized' );
	}

	/**
	 * Merge a request's data with the default data
	 *
	 * @param array $request Request data (same form as {@see \WpOrg\Requests\Session::request_multiple()})
	 * @param boolean $merge_options Should we merge options as well?
	 * @return array Request data
	 */
	protected function merge_request($request, $merge_options = true) {
		if ($this->url !== null) {
			$request['url'] = Iri::absolutize($this->url, $request['url']);
			$request['url'] = $request['url']->uri;
		}

		if (empty($request['headers'])) {
			$request['headers'] = [];
		}

		$request['headers'] = array_merge($this->headers, $request['headers']);

		if (empty($request['data'])) {
			if (is_array($this->data)) {
				$request['data'] = $this->data;
			}
		} elseif (is_array($request['data']) && is_array($this->data)) {
			$request['data'] = array_merge($this->data, $request['data']);
		}

		if ($merge_options === true) {
			$request['options'] = array_merge($this->options, $request['options']);

			// Disallow forcing the type, as that's a per request setting
			unset($request['options']['type']);
		}

		return $request;
	}
}
