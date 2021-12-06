<?php
/**
 * Session handler for persistent requests and default parameters
 *
 * @package Requests
 * @subpackage Session Handler
 */

/**
 * Session handler for persistent requests and default parameters
 *
 * Allows various options to be set as default values, and merges both the
 * options and URL properties together. A base URL can be set for all requests,
 * with all subrequests resolved from this. Base options can be set (including
 * a shared cookie jar), then overridden for individual requests.
 *
 * @package Requests
 * @subpackage Session Handler
 */
class Requests_Session {
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
	public $headers = array();

	/**
	 * Base data for requests
	 *
	 * If both the base data and the per-request data are arrays, the data will
	 * be merged before sending the request.
	 *
	 * @var array
	 */
	public $data = array();

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
	public $options = array();

	/**
	 * Create a new session
	 *
	 * @param string|null $url Base URL for requests
	 * @param array $headers Default headers for requests
	 * @param array $data Default data for requests
	 * @param array $options Default options for requests
	 */
	public function __construct($url = null, $headers = array(), $data = array(), $options = array()) {
		$this->url     = $url;
		$this->headers = $headers;
		$this->data    = $data;
		$this->options = $options;

		if (empty($this->options['cookies'])) {
			$this->options['cookies'] = new Requests_Cookie_Jar();
		}
	}

	/**
	 * Get a property's value
	 *
	 * @param string $key Property key
	 * @return mixed|null Property value, null if none found
	 */
	public function __get($key) {
		if (isset($this->options[$key])) {
			return $this->options[$key];
		}

		return null;
	}

	/**
	 * Set a property's value
	 *
	 * @param string $key Property key
	 * @param mixed $value Property value
	 */
	public function __set($key, $value) {
		$this->options[$key] = $value;
	}

	/**
	 * Remove a property's value
	 *
	 * @param string $key Property key
	 */
	public function __isset($key) {
		return isset($this->options[$key]);
	}

	/**
	 * Remove a property's value
	 *
	 * @param string $key Property key
	 */
	public function __unset($key) {
		if (isset($this->options[$key])) {
			unset($this->options[$key]);
		}
	}

	/**#@+
	 * @see request()
	 * @param string $url
	 * @param array $headers
	 * @param array $options
	 * @return Requests_Response
	 */
	/**
	 * Send a GET request
	 */
	public function get($url, $headers = array(), $options = array()) {
		return $this->request($url, $headers, null, Requests::GET, $options);
	}

	/**
	 * Send a HEAD request
	 */
	public function head($url, $headers = array(), $options = array()) {
		return $this->request($url, $headers, null, Requests::HEAD, $options);
	}

	/**
	 * Send a DELETE request
	 */
	public function delete($url, $headers = array(), $options = array()) {
		return $this->request($url, $headers, null, Requests::DELETE, $options);
	}
	/**#@-*/

	/**#@+
	 * @see request()
	 * @param string $url
	 * @param array $headers
	 * @param array $data
	 * @param array $options
	 * @return Requests_Response
	 */
	/**
	 * Send a POST request
	 */
	public function post($url, $headers = array(), $data = array(), $options = array()) {
		return $this->request($url, $headers, $data, Requests::POST, $options);
	}

	/**
	 * Send a PUT request
	 */
	public function put($url, $headers = array(), $data = array(), $options = array()) {
		return $this->request($url, $headers, $data, Requests::PUT, $options);
	}

	/**
	 * Send a PATCH request
	 *
	 * Note: Unlike {@see post} and {@see put}, `$headers` is required, as the
	 * specification recommends that should send an ETag
	 *
	 * @link https://tools.ietf.org/html/rfc5789
	 */
	public function patch($url, $headers, $data = array(), $options = array()) {
		return $this->request($url, $headers, $data, Requests::PATCH, $options);
	}
	/**#@-*/

	/**
	 * Main interface for HTTP requests
	 *
	 * This method initiates a request and sends it via a transport before
	 * parsing.
	 *
	 * @see Requests::request()
	 *
	 * @throws Requests_Exception On invalid URLs (`nonhttp`)
	 *
	 * @param string $url URL to request
	 * @param array $headers Extra headers to send with the request
	 * @param array|null $data Data to send either as a query string for GET/HEAD requests, or in the body for POST requests
	 * @param string $type HTTP request type (use Requests constants)
	 * @param array $options Options for the request (see {@see Requests::request})
	 * @return Requests_Response
	 */
	public function request($url, $headers = array(), $data = array(), $type = Requests::GET, $options = array()) {
		$request = $this->merge_request(compact('url', 'headers', 'data', 'options'));

		return Requests::request($request['url'], $request['headers'], $request['data'], $type, $request['options']);
	}

	/**
	 * Send multiple HTTP requests simultaneously
	 *
	 * @see Requests::request_multiple()
	 *
	 * @param array $requests Requests data (see {@see Requests::request_multiple})
	 * @param array $options Global and default options (see {@see Requests::request})
	 * @return array Responses (either Requests_Response or a Requests_Exception object)
	 */
	public function request_multiple($requests, $options = array()) {
		foreach ($requests as $key => $request) {
			$requests[$key] = $this->merge_request($request, false);
		}

		$options = array_merge($this->options, $options);

		// Disallow forcing the type, as that's a per request setting
		unset($options['type']);

		return Requests::request_multiple($requests, $options);
	}

	/**
	 * Merge a request's data with the default data
	 *
	 * @param array $request Request data (same form as {@see request_multiple})
	 * @param boolean $merge_options Should we merge options as well?
	 * @return array Request data
	 */
	protected function merge_request($request, $merge_options = true) {
		if ($this->url !== null) {
			$request['url'] = Requests_IRI::absolutize($this->url, $request['url']);
			$request['url'] = $request['url']->uri;
		}

		if (empty($request['headers'])) {
			$request['headers'] = array();
		}
		$request['headers'] = array_merge($this->headers, $request['headers']);

		if (empty($request['data'])) {
			if (is_array($this->data)) {
				$request['data'] = $this->data;
			}
		}
		elseif (is_array($request['data']) && is_array($this->data)) {
			$request['data'] = array_merge($this->data, $request['data']);
		}

		if ($merge_options !== false) {
			$request['options'] = array_merge($this->options, $request['options']);

			// Disallow forcing the type, as that's a per request setting
			unset($request['options']['type']);
		}

		return $request;
	}
}
