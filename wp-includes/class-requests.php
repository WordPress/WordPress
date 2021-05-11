<?php
/**
 * Requests for PHP
 *
 * Inspired by Requests for Python.
 *
 * Based on concepts from SimplePie_File, RequestCore and WP_Http.
 *
 * @package Requests
 */

/**
 * Requests for PHP
 *
 * Inspired by Requests for Python.
 *
 * Based on concepts from SimplePie_File, RequestCore and WP_Http.
 *
 * @package Requests
 */
class Requests {
	/**
	 * POST method
	 *
	 * @var string
	 */
	const POST = 'POST';

	/**
	 * PUT method
	 *
	 * @var string
	 */
	const PUT = 'PUT';

	/**
	 * GET method
	 *
	 * @var string
	 */
	const GET = 'GET';

	/**
	 * HEAD method
	 *
	 * @var string
	 */
	const HEAD = 'HEAD';

	/**
	 * DELETE method
	 *
	 * @var string
	 */
	const DELETE = 'DELETE';

	/**
	 * OPTIONS method
	 *
	 * @var string
	 */
	const OPTIONS = 'OPTIONS';

	/**
	 * TRACE method
	 *
	 * @var string
	 */
	const TRACE = 'TRACE';

	/**
	 * PATCH method
	 *
	 * @link https://tools.ietf.org/html/rfc5789
	 * @var string
	 */
	const PATCH = 'PATCH';

	/**
	 * Default size of buffer size to read streams
	 *
	 * @var integer
	 */
	const BUFFER_SIZE = 1160;

	/**
	 * Current version of Requests
	 *
	 * @var string
	 */
	const VERSION = '1.7';

	/**
	 * Registered transport classes
	 *
	 * @var array
	 */
	protected static $transports = array();

	/**
	 * Selected transport name
	 *
	 * Use {@see get_transport()} instead
	 *
	 * @var array
	 */
	public static $transport = array();

	/**
	 * Default certificate path.
	 *
	 * @see Requests::get_certificate_path()
	 * @see Requests::set_certificate_path()
	 *
	 * @var string
	 */
	protected static $certificate_path;

	/**
	 * This is a static class, do not instantiate it
	 *
	 * @codeCoverageIgnore
	 */
	private function __construct() {}

	/**
	 * Autoloader for Requests
	 *
	 * Register this with {@see register_autoloader()} if you'd like to avoid
	 * having to create your own.
	 *
	 * (You can also use `spl_autoload_register` directly if you'd prefer.)
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $class Class name to load
	 */
	public static function autoloader($class) {
		// Check that the class starts with "Requests"
		if (strpos($class, 'Requests') !== 0) {
			return;
		}

		$file = str_replace('_', '/', $class);
		if (file_exists(dirname(__FILE__) . '/' . $file . '.php')) {
			require_once dirname(__FILE__) . '/' . $file . '.php';
		}
	}

	/**
	 * Register the built-in autoloader
	 *
	 * @codeCoverageIgnore
	 */
	public static function register_autoloader() {
		spl_autoload_register(array('Requests', 'autoloader'));
	}

	/**
	 * Register a transport
	 *
	 * @param string $transport Transport class to add, must support the Requests_Transport interface
	 */
	public static function add_transport($transport) {
		if (empty(self::$transports)) {
			self::$transports = array(
				'Requests_Transport_cURL',
				'Requests_Transport_fsockopen',
			);
		}

		self::$transports = array_merge(self::$transports, array($transport));
	}

	/**
	 * Get a working transport
	 *
	 * @throws Requests_Exception If no valid transport is found (`notransport`)
	 * @return Requests_Transport
	 */
	protected static function get_transport($capabilities = array()) {
		// Caching code, don't bother testing coverage
		// @codeCoverageIgnoreStart
		// array of capabilities as a string to be used as an array key
		ksort($capabilities);
		$cap_string = serialize($capabilities);

		// Don't search for a transport if it's already been done for these $capabilities
		if (isset(self::$transport[$cap_string]) && self::$transport[$cap_string] !== null) {
			$class = self::$transport[$cap_string];
			return new $class();
		}
		// @codeCoverageIgnoreEnd

		if (empty(self::$transports)) {
			self::$transports = array(
				'Requests_Transport_cURL',
				'Requests_Transport_fsockopen',
			);
		}

		// Find us a working transport
		foreach (self::$transports as $class) {
			if (!class_exists($class)) {
				continue;
			}

			$result = call_user_func(array($class, 'test'), $capabilities);
			if ($result) {
				self::$transport[$cap_string] = $class;
				break;
			}
		}
		if (self::$transport[$cap_string] === null) {
			throw new Requests_Exception('No working transports found', 'notransport', self::$transports);
		}

		$class = self::$transport[$cap_string];
		return new $class();
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
	public static function get($url, $headers = array(), $options = array()) {
		return self::request($url, $headers, null, self::GET, $options);
	}

	/**
	 * Send a HEAD request
	 */
	public static function head($url, $headers = array(), $options = array()) {
		return self::request($url, $headers, null, self::HEAD, $options);
	}

	/**
	 * Send a DELETE request
	 */
	public static function delete($url, $headers = array(), $options = array()) {
		return self::request($url, $headers, null, self::DELETE, $options);
	}

	/**
	 * Send a TRACE request
	 */
	public static function trace($url, $headers = array(), $options = array()) {
		return self::request($url, $headers, null, self::TRACE, $options);
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
	public static function post($url, $headers = array(), $data = array(), $options = array()) {
		return self::request($url, $headers, $data, self::POST, $options);
	}
	/**
	 * Send a PUT request
	 */
	public static function put($url, $headers = array(), $data = array(), $options = array()) {
		return self::request($url, $headers, $data, self::PUT, $options);
	}

	/**
	 * Send an OPTIONS request
	 */
	public static function options($url, $headers = array(), $data = array(), $options = array()) {
		return self::request($url, $headers, $data, self::OPTIONS, $options);
	}

	/**
	 * Send a PATCH request
	 *
	 * Note: Unlike {@see post} and {@see put}, `$headers` is required, as the
	 * specification recommends that should send an ETag
	 *
	 * @link https://tools.ietf.org/html/rfc5789
	 */
	public static function patch($url, $headers, $data = array(), $options = array()) {
		return self::request($url, $headers, $data, self::PATCH, $options);
	}
	/**#@-*/

	/**
	 * Main interface for HTTP requests
	 *
	 * This method initiates a request and sends it via a transport before
	 * parsing.
	 *
	 * The `$options` parameter takes an associative array with the following
	 * options:
	 *
	 * - `timeout`: How long should we wait for a response?
	 *    Note: for cURL, a minimum of 1 second applies, as DNS resolution
	 *    operates at second-resolution only.
	 *    (float, seconds with a millisecond precision, default: 10, example: 0.01)
	 * - `connect_timeout`: How long should we wait while trying to connect?
	 *    (float, seconds with a millisecond precision, default: 10, example: 0.01)
	 * - `useragent`: Useragent to send to the server
	 *    (string, default: php-requests/$version)
	 * - `follow_redirects`: Should we follow 3xx redirects?
	 *    (boolean, default: true)
	 * - `redirects`: How many times should we redirect before erroring?
	 *    (integer, default: 10)
	 * - `blocking`: Should we block processing on this request?
	 *    (boolean, default: true)
	 * - `filename`: File to stream the body to instead.
	 *    (string|boolean, default: false)
	 * - `auth`: Authentication handler or array of user/password details to use
	 *    for Basic authentication
	 *    (Requests_Auth|array|boolean, default: false)
	 * - `proxy`: Proxy details to use for proxy by-passing and authentication
	 *    (Requests_Proxy|array|string|boolean, default: false)
	 * - `max_bytes`: Limit for the response body size.
	 *    (integer|boolean, default: false)
	 * - `idn`: Enable IDN parsing
	 *    (boolean, default: true)
	 * - `transport`: Custom transport. Either a class name, or a
	 *    transport object. Defaults to the first working transport from
	 *    {@see getTransport()}
	 *    (string|Requests_Transport, default: {@see getTransport()})
	 * - `hooks`: Hooks handler.
	 *    (Requests_Hooker, default: new Requests_Hooks())
	 * - `verify`: Should we verify SSL certificates? Allows passing in a custom
	 *    certificate file as a string. (Using true uses the system-wide root
	 *    certificate store instead, but this may have different behaviour
	 *    across transports.)
	 *    (string|boolean, default: library/Requests/Transport/cacert.pem)
	 * - `verifyname`: Should we verify the common name in the SSL certificate?
	 *    (boolean, default: true)
	 * - `data_format`: How should we send the `$data` parameter?
	 *    (string, one of 'query' or 'body', default: 'query' for
	 *    HEAD/GET/DELETE, 'body' for POST/PUT/OPTIONS/PATCH)
	 *
	 * @throws Requests_Exception On invalid URLs (`nonhttp`)
	 *
	 * @param string $url URL to request
	 * @param array $headers Extra headers to send with the request
	 * @param array|null $data Data to send either as a query string for GET/HEAD requests, or in the body for POST requests
	 * @param string $type HTTP request type (use Requests constants)
	 * @param array $options Options for the request (see description for more information)
	 * @return Requests_Response
	 */
	public static function request($url, $headers = array(), $data = array(), $type = self::GET, $options = array()) {
		if (empty($options['type'])) {
			$options['type'] = $type;
		}
		$options = array_merge(self::get_default_options(), $options);

		self::set_defaults($url, $headers, $data, $type, $options);

		$options['hooks']->dispatch('requests.before_request', array(&$url, &$headers, &$data, &$type, &$options));

		if (!empty($options['transport'])) {
			$transport = $options['transport'];

			if (is_string($options['transport'])) {
				$transport = new $transport();
			}
		}
		else {
			$need_ssl     = (stripos($url, 'https://') === 0);
			$capabilities = array('ssl' => $need_ssl);
			$transport    = self::get_transport($capabilities);
		}
		$response = $transport->request($url, $headers, $data, $options);

		$options['hooks']->dispatch('requests.before_parse', array(&$response, $url, $headers, $data, $type, $options));

		return self::parse_response($response, $url, $headers, $data, $options);
	}

	/**
	 * Send multiple HTTP requests simultaneously
	 *
	 * The `$requests` parameter takes an associative or indexed array of
	 * request fields. The key of each request can be used to match up the
	 * request with the returned data, or with the request passed into your
	 * `multiple.request.complete` callback.
	 *
	 * The request fields value is an associative array with the following keys:
	 *
	 * - `url`: Request URL Same as the `$url` parameter to
	 *    {@see Requests::request}
	 *    (string, required)
	 * - `headers`: Associative array of header fields. Same as the `$headers`
	 *    parameter to {@see Requests::request}
	 *    (array, default: `array()`)
	 * - `data`: Associative array of data fields or a string. Same as the
	 *    `$data` parameter to {@see Requests::request}
	 *    (array|string, default: `array()`)
	 * - `type`: HTTP request type (use Requests constants). Same as the `$type`
	 *    parameter to {@see Requests::request}
	 *    (string, default: `Requests::GET`)
	 * - `cookies`: Associative array of cookie name to value, or cookie jar.
	 *    (array|Requests_Cookie_Jar)
	 *
	 * If the `$options` parameter is specified, individual requests will
	 * inherit options from it. This can be used to use a single hooking system,
	 * or set all the types to `Requests::POST`, for example.
	 *
	 * In addition, the `$options` parameter takes the following global options:
	 *
	 * - `complete`: A callback for when a request is complete. Takes two
	 *    parameters, a Requests_Response/Requests_Exception reference, and the
	 *    ID from the request array (Note: this can also be overridden on a
	 *    per-request basis, although that's a little silly)
	 *    (callback)
	 *
	 * @param array $requests Requests data (see description for more information)
	 * @param array $options Global and default options (see {@see Requests::request})
	 * @return array Responses (either Requests_Response or a Requests_Exception object)
	 */
	public static function request_multiple($requests, $options = array()) {
		$options = array_merge(self::get_default_options(true), $options);

		if (!empty($options['hooks'])) {
			$options['hooks']->register('transport.internal.parse_response', array('Requests', 'parse_multiple'));
			if (!empty($options['complete'])) {
				$options['hooks']->register('multiple.request.complete', $options['complete']);
			}
		}

		foreach ($requests as $id => &$request) {
			if (!isset($request['headers'])) {
				$request['headers'] = array();
			}
			if (!isset($request['data'])) {
				$request['data'] = array();
			}
			if (!isset($request['type'])) {
				$request['type'] = self::GET;
			}
			if (!isset($request['options'])) {
				$request['options']         = $options;
				$request['options']['type'] = $request['type'];
			}
			else {
				if (empty($request['options']['type'])) {
					$request['options']['type'] = $request['type'];
				}
				$request['options'] = array_merge($options, $request['options']);
			}

			self::set_defaults($request['url'], $request['headers'], $request['data'], $request['type'], $request['options']);

			// Ensure we only hook in once
			if ($request['options']['hooks'] !== $options['hooks']) {
				$request['options']['hooks']->register('transport.internal.parse_response', array('Requests', 'parse_multiple'));
				if (!empty($request['options']['complete'])) {
					$request['options']['hooks']->register('multiple.request.complete', $request['options']['complete']);
				}
			}
		}
		unset($request);

		if (!empty($options['transport'])) {
			$transport = $options['transport'];

			if (is_string($options['transport'])) {
				$transport = new $transport();
			}
		}
		else {
			$transport = self::get_transport();
		}
		$responses = $transport->request_multiple($requests, $options);

		foreach ($responses as $id => &$response) {
			// If our hook got messed with somehow, ensure we end up with the
			// correct response
			if (is_string($response)) {
				$request = $requests[$id];
				self::parse_multiple($response, $request);
				$request['options']['hooks']->dispatch('multiple.request.complete', array(&$response, $id));
			}
		}

		return $responses;
	}

	/**
	 * Get the default options
	 *
	 * @see Requests::request() for values returned by this method
	 * @param boolean $multirequest Is this a multirequest?
	 * @return array Default option values
	 */
	protected static function get_default_options($multirequest = false) {
		$defaults = array(
			'timeout'          => 10,
			'connect_timeout'  => 10,
			'useragent'        => 'php-requests/' . self::VERSION,
			'protocol_version' => 1.1,
			'redirected'       => 0,
			'redirects'        => 10,
			'follow_redirects' => true,
			'blocking'         => true,
			'type'             => self::GET,
			'filename'         => false,
			'auth'             => false,
			'proxy'            => false,
			'cookies'          => false,
			'max_bytes'        => false,
			'idn'              => true,
			'hooks'            => null,
			'transport'        => null,
			'verify'           => self::get_certificate_path(),
			'verifyname'       => true,
		);
		if ($multirequest !== false) {
			$defaults['complete'] = null;
		}
		return $defaults;
	}

	/**
	 * Get default certificate path.
	 *
	 * @return string Default certificate path.
	 */
	public static function get_certificate_path() {
		if (!empty(self::$certificate_path)) {
			return self::$certificate_path;
		}

		return dirname(__FILE__) . '/Requests/Transport/cacert.pem';
	}

	/**
	 * Set default certificate path.
	 *
	 * @param string $path Certificate path, pointing to a PEM file.
	 */
	public static function set_certificate_path($path) {
		self::$certificate_path = $path;
	}

	/**
	 * Set the default values
	 *
	 * @param string $url URL to request
	 * @param array $headers Extra headers to send with the request
	 * @param array|null $data Data to send either as a query string for GET/HEAD requests, or in the body for POST requests
	 * @param string $type HTTP request type
	 * @param array $options Options for the request
	 * @return array $options
	 */
	protected static function set_defaults(&$url, &$headers, &$data, &$type, &$options) {
		if (!preg_match('/^http(s)?:\/\//i', $url, $matches)) {
			throw new Requests_Exception('Only HTTP(S) requests are handled.', 'nonhttp', $url);
		}

		if (empty($options['hooks'])) {
			$options['hooks'] = new Requests_Hooks();
		}

		if (is_array($options['auth'])) {
			$options['auth'] = new Requests_Auth_Basic($options['auth']);
		}
		if ($options['auth'] !== false) {
			$options['auth']->register($options['hooks']);
		}

		if (is_string($options['proxy']) || is_array($options['proxy'])) {
			$options['proxy'] = new Requests_Proxy_HTTP($options['proxy']);
		}
		if ($options['proxy'] !== false) {
			$options['proxy']->register($options['hooks']);
		}

		if (is_array($options['cookies'])) {
			$options['cookies'] = new Requests_Cookie_Jar($options['cookies']);
		}
		elseif (empty($options['cookies'])) {
			$options['cookies'] = new Requests_Cookie_Jar();
		}
		if ($options['cookies'] !== false) {
			$options['cookies']->register($options['hooks']);
		}

		if ($options['idn'] !== false) {
			$iri       = new Requests_IRI($url);
			$iri->host = Requests_IDNAEncoder::encode($iri->ihost);
			$url       = $iri->uri;
		}

		// Massage the type to ensure we support it.
		$type = strtoupper($type);

		if (!isset($options['data_format'])) {
			if (in_array($type, array(self::HEAD, self::GET, self::DELETE), true)) {
				$options['data_format'] = 'query';
			}
			else {
				$options['data_format'] = 'body';
			}
		}
	}

	/**
	 * HTTP response parser
	 *
	 * @throws Requests_Exception On missing head/body separator (`requests.no_crlf_separator`)
	 * @throws Requests_Exception On missing head/body separator (`noversion`)
	 * @throws Requests_Exception On missing head/body separator (`toomanyredirects`)
	 *
	 * @param string $headers Full response text including headers and body
	 * @param string $url Original request URL
	 * @param array $req_headers Original $headers array passed to {@link request()}, in case we need to follow redirects
	 * @param array $req_data Original $data array passed to {@link request()}, in case we need to follow redirects
	 * @param array $options Original $options array passed to {@link request()}, in case we need to follow redirects
	 * @return Requests_Response
	 */
	protected static function parse_response($headers, $url, $req_headers, $req_data, $options) {
		$return = new Requests_Response();
		if (!$options['blocking']) {
			return $return;
		}

		$return->raw  = $headers;
		$return->url  = (string) $url;
		$return->body = '';

		if (!$options['filename']) {
			$pos = strpos($headers, "\r\n\r\n");
			if ($pos === false) {
				// Crap!
				throw new Requests_Exception('Missing header/body separator', 'requests.no_crlf_separator');
			}

			$headers = substr($return->raw, 0, $pos);
			// Headers will always be separated from the body by two new lines - `\n\r\n\r`.
			$body = substr($return->raw, $pos + 4);
			if (!empty($body)) {
				$return->body = $body;
			}
		}
		// Pretend CRLF = LF for compatibility (RFC 2616, section 19.3)
		$headers = str_replace("\r\n", "\n", $headers);
		// Unfold headers (replace [CRLF] 1*( SP | HT ) with SP) as per RFC 2616 (section 2.2)
		$headers = preg_replace('/\n[ \t]/', ' ', $headers);
		$headers = explode("\n", $headers);
		preg_match('#^HTTP/(1\.\d)[ \t]+(\d+)#i', array_shift($headers), $matches);
		if (empty($matches)) {
			throw new Requests_Exception('Response could not be parsed', 'noversion', $headers);
		}
		$return->protocol_version = (float) $matches[1];
		$return->status_code      = (int) $matches[2];
		if ($return->status_code >= 200 && $return->status_code < 300) {
			$return->success = true;
		}

		foreach ($headers as $header) {
			list($key, $value) = explode(':', $header, 2);
			$value             = trim($value);
			preg_replace('#(\s+)#i', ' ', $value);
			$return->headers[$key] = $value;
		}
		if (isset($return->headers['transfer-encoding'])) {
			$return->body = self::decode_chunked($return->body);
			unset($return->headers['transfer-encoding']);
		}
		if (isset($return->headers['content-encoding'])) {
			$return->body = self::decompress($return->body);
		}

		//fsockopen and cURL compatibility
		if (isset($return->headers['connection'])) {
			unset($return->headers['connection']);
		}

		$options['hooks']->dispatch('requests.before_redirect_check', array(&$return, $req_headers, $req_data, $options));

		if ($return->is_redirect() && $options['follow_redirects'] === true) {
			if (isset($return->headers['location']) && $options['redirected'] < $options['redirects']) {
				if ($return->status_code === 303) {
					$options['type'] = self::GET;
				}
				$options['redirected']++;
				$location = $return->headers['location'];
				if (strpos($location, 'http://') !== 0 && strpos($location, 'https://') !== 0) {
					// relative redirect, for compatibility make it absolute
					$location = Requests_IRI::absolutize($url, $location);
					$location = $location->uri;
				}

				$hook_args = array(
					&$location,
					&$req_headers,
					&$req_data,
					&$options,
					$return,
				);
				$options['hooks']->dispatch('requests.before_redirect', $hook_args);
				$redirected            = self::request($location, $req_headers, $req_data, $options['type'], $options);
				$redirected->history[] = $return;
				return $redirected;
			}
			elseif ($options['redirected'] >= $options['redirects']) {
				throw new Requests_Exception('Too many redirects', 'toomanyredirects', $return);
			}
		}

		$return->redirects = $options['redirected'];

		$options['hooks']->dispatch('requests.after_request', array(&$return, $req_headers, $req_data, $options));
		return $return;
	}

	/**
	 * Callback for `transport.internal.parse_response`
	 *
	 * Internal use only. Converts a raw HTTP response to a Requests_Response
	 * while still executing a multiple request.
	 *
	 * @param string $response Full response text including headers and body (will be overwritten with Response instance)
	 * @param array $request Request data as passed into {@see Requests::request_multiple()}
	 * @return null `$response` is either set to a Requests_Response instance, or a Requests_Exception object
	 */
	public static function parse_multiple(&$response, $request) {
		try {
			$url      = $request['url'];
			$headers  = $request['headers'];
			$data     = $request['data'];
			$options  = $request['options'];
			$response = self::parse_response($response, $url, $headers, $data, $options);
		}
		catch (Requests_Exception $e) {
			$response = $e;
		}
	}

	/**
	 * Decoded a chunked body as per RFC 2616
	 *
	 * @see https://tools.ietf.org/html/rfc2616#section-3.6.1
	 * @param string $data Chunked body
	 * @return string Decoded body
	 */
	protected static function decode_chunked($data) {
		if (!preg_match('/^([0-9a-f]+)(?:;(?:[\w-]*)(?:=(?:(?:[\w-]*)*|"(?:[^\r\n])*"))?)*\r\n/i', trim($data))) {
			return $data;
		}

		$decoded = '';
		$encoded = $data;

		while (true) {
			$is_chunked = (bool) preg_match('/^([0-9a-f]+)(?:;(?:[\w-]*)(?:=(?:(?:[\w-]*)*|"(?:[^\r\n])*"))?)*\r\n/i', $encoded, $matches);
			if (!$is_chunked) {
				// Looks like it's not chunked after all
				return $data;
			}

			$length = hexdec(trim($matches[1]));
			if ($length === 0) {
				// Ignore trailer headers
				return $decoded;
			}

			$chunk_length = strlen($matches[0]);
			$decoded     .= substr($encoded, $chunk_length, $length);
			$encoded      = substr($encoded, $chunk_length + $length + 2);

			if (trim($encoded) === '0' || empty($encoded)) {
				return $decoded;
			}
		}

		// We'll never actually get down here
		// @codeCoverageIgnoreStart
	}
	// @codeCoverageIgnoreEnd

	/**
	 * Convert a key => value array to a 'key: value' array for headers
	 *
	 * @param array $array Dictionary of header values
	 * @return array List of headers
	 */
	public static function flatten($array) {
		$return = array();
		foreach ($array as $key => $value) {
			$return[] = sprintf('%s: %s', $key, $value);
		}
		return $return;
	}

	/**
	 * Convert a key => value array to a 'key: value' array for headers
	 *
	 * @codeCoverageIgnore
	 * @deprecated Misspelling of {@see Requests::flatten}
	 * @param array $array Dictionary of header values
	 * @return array List of headers
	 */
	public static function flattern($array) {
		return self::flatten($array);
	}

	/**
	 * Decompress an encoded body
	 *
	 * Implements gzip, compress and deflate. Guesses which it is by attempting
	 * to decode.
	 *
	 * @param string $data Compressed data in one of the above formats
	 * @return string Decompressed string
	 */
	public static function decompress($data) {
		if (substr($data, 0, 2) !== "\x1f\x8b" && substr($data, 0, 2) !== "\x78\x9c") {
			// Not actually compressed. Probably cURL ruining this for us.
			return $data;
		}

		if (function_exists('gzdecode')) {
			// phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.gzdecodeFound -- Wrapped in function_exists() for PHP 5.2.
			$decoded = @gzdecode($data);
			if ($decoded !== false) {
				return $decoded;
			}
		}

		if (function_exists('gzinflate')) {
			$decoded = @gzinflate($data);
			if ($decoded !== false) {
				return $decoded;
			}
		}

		$decoded = self::compatible_gzinflate($data);
		if ($decoded !== false) {
			return $decoded;
		}

		if (function_exists('gzuncompress')) {
			$decoded = @gzuncompress($data);
			if ($decoded !== false) {
				return $decoded;
			}
		}

		return $data;
	}

	/**
	 * Decompression of deflated string while staying compatible with the majority of servers.
	 *
	 * Certain Servers will return deflated data with headers which PHP's gzinflate()
	 * function cannot handle out of the box. The following function has been created from
	 * various snippets on the gzinflate() PHP documentation.
	 *
	 * Warning: Magic numbers within. Due to the potential different formats that the compressed
	 * data may be returned in, some "magic offsets" are needed to ensure proper decompression
	 * takes place. For a simple progmatic way to determine the magic offset in use, see:
	 * https://core.trac.wordpress.org/ticket/18273
	 *
	 * @since 2.8.1
	 * @link https://core.trac.wordpress.org/ticket/18273
	 * @link https://secure.php.net/manual/en/function.gzinflate.php#70875
	 * @link https://secure.php.net/manual/en/function.gzinflate.php#77336
	 *
	 * @param string $gz_data String to decompress.
	 * @return string|bool False on failure.
	 */
	public static function compatible_gzinflate($gz_data) {
		// Compressed data might contain a full zlib header, if so strip it for
		// gzinflate()
		if (substr($gz_data, 0, 3) === "\x1f\x8b\x08") {
			$i   = 10;
			$flg = ord(substr($gz_data, 3, 1));
			if ($flg > 0) {
				if ($flg & 4) {
					list($xlen) = unpack('v', substr($gz_data, $i, 2));
					$i         += 2 + $xlen;
				}
				if ($flg & 8) {
					$i = strpos($gz_data, "\0", $i) + 1;
				}
				if ($flg & 16) {
					$i = strpos($gz_data, "\0", $i) + 1;
				}
				if ($flg & 2) {
					$i += 2;
				}
			}
			$decompressed = self::compatible_gzinflate(substr($gz_data, $i));
			if ($decompressed !== false) {
				return $decompressed;
			}
		}

		// If the data is Huffman Encoded, we must first strip the leading 2
		// byte Huffman marker for gzinflate()
		// The response is Huffman coded by many compressors such as
		// java.util.zip.Deflater, Ruby’s Zlib::Deflate, and .NET's
		// System.IO.Compression.DeflateStream.
		//
		// See https://decompres.blogspot.com/ for a quick explanation of this
		// data type
		$huffman_encoded = false;

		// low nibble of first byte should be 0x08
		list(, $first_nibble) = unpack('h', $gz_data);

		// First 2 bytes should be divisible by 0x1F
		list(, $first_two_bytes) = unpack('n', $gz_data);

		if ($first_nibble === 0x08 && ($first_two_bytes % 0x1F) === 0) {
			$huffman_encoded = true;
		}

		if ($huffman_encoded) {
			$decompressed = @gzinflate(substr($gz_data, 2));
			if ($decompressed !== false) {
				return $decompressed;
			}
		}

		if (substr($gz_data, 0, 4) === "\x50\x4b\x03\x04") {
			// ZIP file format header
			// Offset 6: 2 bytes, General-purpose field
			// Offset 26: 2 bytes, filename length
			// Offset 28: 2 bytes, optional field length
			// Offset 30: Filename field, followed by optional field, followed
			// immediately by data
			list(, $general_purpose_flag) = unpack('v', substr($gz_data, 6, 2));

			// If the file has been compressed on the fly, 0x08 bit is set of
			// the general purpose field. We can use this to differentiate
			// between a compressed document, and a ZIP file
			$zip_compressed_on_the_fly = ((0x08 & $general_purpose_flag) === 0x08);

			if (!$zip_compressed_on_the_fly) {
				// Don't attempt to decode a compressed zip file
				return $gz_data;
			}

			// Determine the first byte of data, based on the above ZIP header
			// offsets:
			$first_file_start = array_sum(unpack('v2', substr($gz_data, 26, 4)));
			$decompressed     = @gzinflate(substr($gz_data, 30 + $first_file_start));
			if ($decompressed !== false) {
				return $decompressed;
			}
			return false;
		}

		// Finally fall back to straight gzinflate
		$decompressed = @gzinflate($gz_data);
		if ($decompressed !== false) {
			return $decompressed;
		}

		// Fallback for all above failing, not expected, but included for
		// debugging and preventing regressions and to track stats
		$decompressed = @gzinflate(substr($gz_data, 2));
		if ($decompressed !== false) {
			return $decompressed;
		}

		return false;
	}

	public static function match_domain($host, $reference) {
		// Check for a direct match
		if ($host === $reference) {
			return true;
		}

		// Calculate the valid wildcard match if the host is not an IP address
		// Also validates that the host has 3 parts or more, as per Firefox's
		// ruleset.
		$parts = explode('.', $host);
		if (ip2long($host) === false && count($parts) >= 3) {
			$parts[0] = '*';
			$wildcard = implode('.', $parts);
			if ($wildcard === $reference) {
				return true;
			}
		}

		return false;
	}
}
