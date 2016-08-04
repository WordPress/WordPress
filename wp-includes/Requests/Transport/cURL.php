<?php
/**
 * cURL HTTP transport
 *
 * @package Requests
 * @subpackage Transport
 */

/**
 * cURL HTTP transport
 *
 * @package Requests
 * @subpackage Transport
 */
class Requests_Transport_cURL implements Requests_Transport {
	const CURL_7_10_5 = 0x070A05;
	const CURL_7_16_2 = 0x071002;

	/**
	 * Raw HTTP data
	 *
	 * @var string
	 */
	public $headers = '';

	/**
	 * Raw body data
	 *
	 * @var string
	 */
	public $response_data = '';

	/**
	 * Information on the current request
	 *
	 * @var array cURL information array, see {@see https://secure.php.net/curl_getinfo}
	 */
	public $info;

	/**
	 * Version string
	 *
	 * @var long
	 */
	public $version;

	/**
	 * cURL handle
	 *
	 * @var resource
	 */
	protected $handle;

	/**
	 * Hook dispatcher instance
	 *
	 * @var Requests_Hooks
	 */
	protected $hooks;

	/**
	 * Have we finished the headers yet?
	 *
	 * @var boolean
	 */
	protected $done_headers = false;

	/**
	 * If streaming to a file, keep the file pointer
	 *
	 * @var resource
	 */
	protected $stream_handle;

	/**
	 * How many bytes are in the response body?
	 *
	 * @var int
	 */
	protected $response_bytes;

	/**
	 * What's the maximum number of bytes we should keep?
	 *
	 * @var int|bool Byte count, or false if no limit.
	 */
	protected $response_byte_limit;

	/**
	 * Constructor
	 */
	public function __construct() {
		$curl = curl_version();
		$this->version = $curl['version_number'];
		$this->handle = curl_init();

		curl_setopt($this->handle, CURLOPT_HEADER, false);
		curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
		if ($this->version >= self::CURL_7_10_5) {
			curl_setopt($this->handle, CURLOPT_ENCODING, '');
		}
		if (defined('CURLOPT_PROTOCOLS')) {
			curl_setopt($this->handle, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
		}
		if (defined('CURLOPT_REDIR_PROTOCOLS')) {
			curl_setopt($this->handle, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
		}
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		if (is_resource($this->handle)) {
			curl_close($this->handle);
		}
	}

	/**
	 * Perform a request
	 *
	 * @throws Requests_Exception On a cURL error (`curlerror`)
	 *
	 * @param string $url URL to request
	 * @param array $headers Associative array of request headers
	 * @param string|array $data Data to send either as the POST body, or as parameters in the URL for a GET/HEAD
	 * @param array $options Request options, see {@see Requests::response()} for documentation
	 * @return string Raw HTTP result
	 */
	public function request($url, $headers = array(), $data = array(), $options = array()) {
		$this->hooks = $options['hooks'];

		$this->setup_handle($url, $headers, $data, $options);

		$options['hooks']->dispatch('curl.before_send', array(&$this->handle));

		if ($options['filename'] !== false) {
			$this->stream_handle = fopen($options['filename'], 'wb');
		}

		$this->response_data = '';
		$this->response_bytes = 0;
		$this->response_byte_limit = false;
		if ($options['max_bytes'] !== false) {
			$this->response_byte_limit = $options['max_bytes'];
		}

		if (isset($options['verify'])) {
			if ($options['verify'] === false) {
				curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, 0);
			}
			elseif (is_string($options['verify'])) {
				curl_setopt($this->handle, CURLOPT_CAINFO, $options['verify']);
			}
		}

		if (isset($options['verifyname']) && $options['verifyname'] === false) {
			curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, 0);
		}

		curl_exec($this->handle);
		$response = $this->response_data;

		$options['hooks']->dispatch('curl.after_send', array());

		if (curl_errno($this->handle) === 23 || curl_errno($this->handle) === 61) {
			// Reset encoding and try again
			curl_setopt($this->handle, CURLOPT_ENCODING, 'none');

			$this->response_data = '';
			$this->response_bytes = 0;
			curl_exec($this->handle);
			$response = $this->response_data;
		}

		$this->process_response($response, $options);

		// Need to remove the $this reference from the curl handle.
		// Otherwise Requests_Transport_cURL wont be garbage collected and the curl_close() will never be called.
		curl_setopt($this->handle, CURLOPT_HEADERFUNCTION, null);
		curl_setopt($this->handle, CURLOPT_WRITEFUNCTION, null);

		return $this->headers;
	}

	/**
	 * Send multiple requests simultaneously
	 *
	 * @param array $requests Request data
	 * @param array $options Global options
	 * @return array Array of Requests_Response objects (may contain Requests_Exception or string responses as well)
	 */
	public function request_multiple($requests, $options) {
		// If you're not requesting, we can't get any responses ¯\_(ツ)_/¯
		if (empty($requests)) {
			return array();
		}

		$multihandle = curl_multi_init();
		$subrequests = array();
		$subhandles = array();

		$class = get_class($this);
		foreach ($requests as $id => $request) {
			$subrequests[$id] = new $class();
			$subhandles[$id] = $subrequests[$id]->get_subrequest_handle($request['url'], $request['headers'], $request['data'], $request['options']);
			$request['options']['hooks']->dispatch('curl.before_multi_add', array(&$subhandles[$id]));
			curl_multi_add_handle($multihandle, $subhandles[$id]);
		}

		$completed = 0;
		$responses = array();

		$request['options']['hooks']->dispatch('curl.before_multi_exec', array(&$multihandle));

		do {
			$active = false;

			do {
				$status = curl_multi_exec($multihandle, $active);
			}
			while ($status === CURLM_CALL_MULTI_PERFORM);

			$to_process = array();

			// Read the information as needed
			while ($done = curl_multi_info_read($multihandle)) {
				$key = array_search($done['handle'], $subhandles, true);
				if (!isset($to_process[$key])) {
					$to_process[$key] = $done;
				}
			}

			// Parse the finished requests before we start getting the new ones
			foreach ($to_process as $key => $done) {
				$options = $requests[$key]['options'];
				if (CURLE_OK !== $done['result']) {
					//get error string for handle.
					$reason = curl_error($done['handle']);
					$exception = new Requests_Exception_Transport_cURL(
									$reason,
									Requests_Exception_Transport_cURL::EASY,
									$done['handle'],
									$done['result']
								);
					$responses[$key] = $exception;
					$options['hooks']->dispatch('transport.internal.parse_error', array(&$responses[$key], $requests[$key]));
				}
				else {
					$responses[$key] = $subrequests[$key]->process_response($subrequests[$key]->response_data, $options);

					$options['hooks']->dispatch('transport.internal.parse_response', array(&$responses[$key], $requests[$key]));
				}

				curl_multi_remove_handle($multihandle, $done['handle']);
				curl_close($done['handle']);

				if (!is_string($responses[$key])) {
					$options['hooks']->dispatch('multiple.request.complete', array(&$responses[$key], $key));
				}
				$completed++;
			}
		}
		while ($active || $completed < count($subrequests));

		$request['options']['hooks']->dispatch('curl.after_multi_exec', array(&$multihandle));

		curl_multi_close($multihandle);

		return $responses;
	}

	/**
	 * Get the cURL handle for use in a multi-request
	 *
	 * @param string $url URL to request
	 * @param array $headers Associative array of request headers
	 * @param string|array $data Data to send either as the POST body, or as parameters in the URL for a GET/HEAD
	 * @param array $options Request options, see {@see Requests::response()} for documentation
	 * @return resource Subrequest's cURL handle
	 */
	public function &get_subrequest_handle($url, $headers, $data, $options) {
		$this->setup_handle($url, $headers, $data, $options);

		if ($options['filename'] !== false) {
			$this->stream_handle = fopen($options['filename'], 'wb');
		}

		$this->response_data = '';
		$this->response_bytes = 0;
		$this->response_byte_limit = false;
		if ($options['max_bytes'] !== false) {
			$this->response_byte_limit = $options['max_bytes'];
		}
		$this->hooks = $options['hooks'];

		return $this->handle;
	}

	/**
	 * Setup the cURL handle for the given data
	 *
	 * @param string $url URL to request
	 * @param array $headers Associative array of request headers
	 * @param string|array $data Data to send either as the POST body, or as parameters in the URL for a GET/HEAD
	 * @param array $options Request options, see {@see Requests::response()} for documentation
	 */
	protected function setup_handle($url, $headers, $data, $options) {
		$options['hooks']->dispatch('curl.before_request', array(&$this->handle));

		// Force closing the connection for old versions of cURL (<7.22).
		if ( ! isset( $headers['Connection'] ) ) {
			$headers['Connection'] = 'close';
		}

		$headers = Requests::flatten($headers);

		if (!empty($data)) {
			$data_format = $options['data_format'];

			if ($data_format === 'query') {
				$url = self::format_get($url, $data);
				$data = '';
			}
			elseif (!is_string($data)) {
				$data = http_build_query($data, null, '&');
			}
		}

		switch ($options['type']) {
			case Requests::POST:
				curl_setopt($this->handle, CURLOPT_POST, true);
				curl_setopt($this->handle, CURLOPT_POSTFIELDS, $data);
				break;
			case Requests::HEAD:
				curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $options['type']);
				curl_setopt($this->handle, CURLOPT_NOBODY, true);
				break;
			case Requests::TRACE:
				curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $options['type']);
				break;
			case Requests::PATCH:
			case Requests::PUT:
			case Requests::DELETE:
			case Requests::OPTIONS:
			default:
				curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $options['type']);
				if (!empty($data)) {
					curl_setopt( $this->handle, CURLOPT_POSTFIELDS, $data );
				}
		}

		// cURL requires a minimum timeout of 1 second when using the system
		// DNS resolver, as it uses `alarm()`, which is second resolution only.
		// There's no way to detect which DNS resolver is being used from our
		// end, so we need to round up regardless of the supplied timeout.
		//
		// https://github.com/curl/curl/blob/4f45240bc84a9aa648c8f7243be7b79e9f9323a5/lib/hostip.c#L606-L609
		$timeout = max($options['timeout'], 1);

		if (is_int($timeout) || $this->version < self::CURL_7_16_2) {
			curl_setopt($this->handle, CURLOPT_TIMEOUT, ceil($timeout));
		}
		else {
			curl_setopt($this->handle, CURLOPT_TIMEOUT_MS, round($timeout * 1000));
		}

		if (is_int($options['connect_timeout']) || $this->version < self::CURL_7_16_2) {
			curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT, ceil($options['connect_timeout']));
		}
		else {
			curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT_MS, round($options['connect_timeout'] * 1000));
		}
		curl_setopt($this->handle, CURLOPT_URL, $url);
		curl_setopt($this->handle, CURLOPT_REFERER, $url);
		curl_setopt($this->handle, CURLOPT_USERAGENT, $options['useragent']);
		curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);

		if ($options['protocol_version'] === 1.1) {
			curl_setopt($this->handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		}
		else {
			curl_setopt($this->handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		}

		if (true === $options['blocking']) {
			curl_setopt($this->handle, CURLOPT_HEADERFUNCTION, array(&$this, 'stream_headers'));
			curl_setopt($this->handle, CURLOPT_WRITEFUNCTION, array(&$this, 'stream_body'));
			curl_setopt($this->handle, CURLOPT_BUFFERSIZE, Requests::BUFFER_SIZE);
		}
	}

	/**
	 * Process a response
	 *
	 * @param string $response Response data from the body
	 * @param array $options Request options
	 * @return string HTTP response data including headers
	 */
	public function process_response($response, $options) {
		if ($options['blocking'] === false) {
			$fake_headers = '';
			$options['hooks']->dispatch('curl.after_request', array(&$fake_headers));
			return false;
		}
		if ($options['filename'] !== false) {
			fclose($this->stream_handle);
			$this->headers = trim($this->headers);
		}
		else {
			$this->headers .= $response;
		}

		if (curl_errno($this->handle)) {
			$error = sprintf(
				'cURL error %s: %s',
				curl_errno($this->handle),
				curl_error($this->handle)
			);
			throw new Requests_Exception($error, 'curlerror', $this->handle);
		}
		$this->info = curl_getinfo($this->handle);

		$options['hooks']->dispatch('curl.after_request', array(&$this->headers, &$this->info));
		return $this->headers;
	}

	/**
	 * Collect the headers as they are received
	 *
	 * @param resource $handle cURL resource
	 * @param string $headers Header string
	 * @return integer Length of provided header
	 */
	public function stream_headers($handle, $headers) {
		// Why do we do this? cURL will send both the final response and any
		// interim responses, such as a 100 Continue. We don't need that.
		// (We may want to keep this somewhere just in case)
		if ($this->done_headers) {
			$this->headers = '';
			$this->done_headers = false;
		}
		$this->headers .= $headers;

		if ($headers === "\r\n") {
			$this->done_headers = true;
		}
		return strlen($headers);
	}

	/**
	 * Collect data as it's received
	 *
	 * @since 1.6.1
	 *
	 * @param resource $handle cURL resource
	 * @param string $data Body data
	 * @return integer Length of provided data
	 */
	protected function stream_body($handle, $data) {
		$this->hooks->dispatch('request.progress', array($data, $this->response_bytes, $this->response_byte_limit));
		$data_length = strlen($data);

		// Are we limiting the response size?
		if ($this->response_byte_limit) {
			if ($this->response_bytes === $this->response_byte_limit) {
				// Already at maximum, move on
				return $data_length;
			}

			if (($this->response_bytes + $data_length) > $this->response_byte_limit) {
				// Limit the length
				$limited_length = ($this->response_byte_limit - $this->response_bytes);
				$data = substr($data, 0, $limited_length);
			}
		}

		if ($this->stream_handle) {
			fwrite($this->stream_handle, $data);
		}
		else {
			$this->response_data .= $data;
		}

		$this->response_bytes += strlen($data);
		return $data_length;
	}

	/**
	 * Format a URL given GET data
	 *
	 * @param string $url
	 * @param array|object $data Data to build query using, see {@see https://secure.php.net/http_build_query}
	 * @return string URL with data
	 */
	protected static function format_get($url, $data) {
		if (!empty($data)) {
			$url_parts = parse_url($url);
			if (empty($url_parts['query'])) {
				$query = $url_parts['query'] = '';
			}
			else {
				$query = $url_parts['query'];
			}

			$query .= '&' . http_build_query($data, null, '&');
			$query = trim($query, '&');

			if (empty($url_parts['query'])) {
				$url .= '?' . $query;
			}
			else {
				$url = str_replace($url_parts['query'], $query, $url);
			}
		}
		return $url;
	}

	/**
	 * Whether this transport is valid
	 *
	 * @codeCoverageIgnore
	 * @return boolean True if the transport is valid, false otherwise.
	 */
	public static function test($capabilities = array()) {
		if (!function_exists('curl_init') && !function_exists('curl_exec')) {
			return false;
		}

		// If needed, check that our installed curl version supports SSL
		if (isset($capabilities['ssl']) && $capabilities['ssl']) {
			$curl_version = curl_version();
			if (!(CURL_VERSION_SSL & $curl_version['features'])) {
				return false;
			}
		}

		return true;
	}
}
