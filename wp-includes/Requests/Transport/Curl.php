<?php
/**
 * cURL HTTP transport
 *
 * @package Requests\Transport
 */

namespace WpOrg\Requests\Transport;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use WpOrg\Requests\Capability;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Exception\Transport\Curl as CurlException;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Transport;
use WpOrg\Requests\Utility\InputValidator;

/**
 * cURL HTTP transport
 *
 * @package Requests\Transport
 */
final class Curl implements Transport {
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
	 * @var array cURL information array, see {@link https://www.php.net/curl_getinfo}
	 */
	public $info;

	/**
	 * cURL version number
	 *
	 * @var int
	 */
	public $version;

	/**
	 * cURL handle
	 *
	 * @var resource|\CurlHandle Resource in PHP < 8.0, Instance of CurlHandle in PHP >= 8.0.
	 */
	private $handle;

	/**
	 * Hook dispatcher instance
	 *
	 * @var \WpOrg\Requests\Hooks
	 */
	private $hooks;

	/**
	 * Have we finished the headers yet?
	 *
	 * @var boolean
	 */
	private $done_headers = false;

	/**
	 * If streaming to a file, keep the file pointer
	 *
	 * @var resource
	 */
	private $stream_handle;

	/**
	 * How many bytes are in the response body?
	 *
	 * @var int
	 */
	private $response_bytes;

	/**
	 * What's the maximum number of bytes we should keep?
	 *
	 * @var int|bool Byte count, or false if no limit.
	 */
	private $response_byte_limit;

	/**
	 * Constructor
	 */
	public function __construct() {
		$curl          = curl_version();
		$this->version = $curl['version_number'];
		$this->handle  = curl_init();

		curl_setopt($this->handle, CURLOPT_HEADER, false);
		curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
		if ($this->version >= self::CURL_7_10_5) {
			curl_setopt($this->handle, CURLOPT_ENCODING, '');
		}
		if (defined('CURLOPT_PROTOCOLS')) {
			// phpcs:ignore PHPCompatibility.Constants.NewConstants.curlopt_protocolsFound
			curl_setopt($this->handle, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
		}
		if (defined('CURLOPT_REDIR_PROTOCOLS')) {
			// phpcs:ignore PHPCompatibility.Constants.NewConstants.curlopt_redir_protocolsFound
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
	 * @param string|Stringable $url URL to request
	 * @param array $headers Associative array of request headers
	 * @param string|array $data Data to send either as the POST body, or as parameters in the URL for a GET/HEAD
	 * @param array $options Request options, see {@see \WpOrg\Requests\Requests::response()} for documentation
	 * @return string Raw HTTP result
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $url argument is not a string or Stringable.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $headers argument is not an array.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $data parameter is not an array or string.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $options argument is not an array.
	 * @throws \WpOrg\Requests\Exception       On a cURL error (`curlerror`)
	 */
	public function request($url, $headers = [], $data = [], $options = []) {
		if (InputValidator::is_string_or_stringable($url) === false) {
			throw InvalidArgument::create(1, '$url', 'string|Stringable', gettype($url));
		}

		if (is_array($headers) === false) {
			throw InvalidArgument::create(2, '$headers', 'array', gettype($headers));
		}

		if (!is_array($data) && !is_string($data)) {
			if ($data === null) {
				$data = '';
			} else {
				throw InvalidArgument::create(3, '$data', 'array|string', gettype($data));
			}
		}

		if (is_array($options) === false) {
			throw InvalidArgument::create(4, '$options', 'array', gettype($options));
		}

		$this->hooks = $options['hooks'];

		$this->setup_handle($url, $headers, $data, $options);

		$options['hooks']->dispatch('curl.before_send', [&$this->handle]);

		if ($options['filename'] !== false) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors -- Silenced the PHP native warning in favour of throwing an exception.
			$this->stream_handle = @fopen($options['filename'], 'wb');
			if ($this->stream_handle === false) {
				$error = error_get_last();
				throw new Exception($error['message'], 'fopen');
			}
		}

		$this->response_data       = '';
		$this->response_bytes      = 0;
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

		$options['hooks']->dispatch('curl.after_send', []);

		if (curl_errno($this->handle) === CURLE_WRITE_ERROR || curl_errno($this->handle) === CURLE_BAD_CONTENT_ENCODING) {
			// Reset encoding and try again
			curl_setopt($this->handle, CURLOPT_ENCODING, 'none');

			$this->response_data  = '';
			$this->response_bytes = 0;
			curl_exec($this->handle);
			$response = $this->response_data;
		}

		$this->process_response($response, $options);

		// Need to remove the $this reference from the curl handle.
		// Otherwise \WpOrg\Requests\Transport\Curl won't be garbage collected and the curl_close() will never be called.
		curl_setopt($this->handle, CURLOPT_HEADERFUNCTION, null);
		curl_setopt($this->handle, CURLOPT_WRITEFUNCTION, null);

		return $this->headers;
	}

	/**
	 * Send multiple requests simultaneously
	 *
	 * @param array $requests Request data
	 * @param array $options Global options
	 * @return array Array of \WpOrg\Requests\Response objects (may contain \WpOrg\Requests\Exception or string responses as well)
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $requests argument is not an array or iterable object with array access.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $options argument is not an array.
	 */
	public function request_multiple($requests, $options) {
		// If you're not requesting, we can't get any responses ¯\_(ツ)_/¯
		if (empty($requests)) {
			return [];
		}

		if (InputValidator::has_array_access($requests) === false || InputValidator::is_iterable($requests) === false) {
			throw InvalidArgument::create(1, '$requests', 'array|ArrayAccess&Traversable', gettype($requests));
		}

		if (is_array($options) === false) {
			throw InvalidArgument::create(2, '$options', 'array', gettype($options));
		}

		$multihandle = curl_multi_init();
		$subrequests = [];
		$subhandles  = [];

		$class = get_class($this);
		foreach ($requests as $id => $request) {
			$subrequests[$id] = new $class();
			$subhandles[$id]  = $subrequests[$id]->get_subrequest_handle($request['url'], $request['headers'], $request['data'], $request['options']);
			$request['options']['hooks']->dispatch('curl.before_multi_add', [&$subhandles[$id]]);
			curl_multi_add_handle($multihandle, $subhandles[$id]);
		}

		$completed       = 0;
		$responses       = [];
		$subrequestcount = count($subrequests);

		$request['options']['hooks']->dispatch('curl.before_multi_exec', [&$multihandle]);

		do {
			$active = 0;

			do {
				$status = curl_multi_exec($multihandle, $active);
			}
			while ($status === CURLM_CALL_MULTI_PERFORM);

			$to_process = [];

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
				if ($done['result'] !== CURLE_OK) {
					//get error string for handle.
					$reason          = curl_error($done['handle']);
					$exception       = new CurlException(
						$reason,
						CurlException::EASY,
						$done['handle'],
						$done['result']
					);
					$responses[$key] = $exception;
					$options['hooks']->dispatch('transport.internal.parse_error', [&$responses[$key], $requests[$key]]);
				}
				else {
					$responses[$key] = $subrequests[$key]->process_response($subrequests[$key]->response_data, $options);

					$options['hooks']->dispatch('transport.internal.parse_response', [&$responses[$key], $requests[$key]]);
				}

				curl_multi_remove_handle($multihandle, $done['handle']);
				curl_close($done['handle']);

				if (!is_string($responses[$key])) {
					$options['hooks']->dispatch('multiple.request.complete', [&$responses[$key], $key]);
				}
				$completed++;
			}
		}
		while ($active || $completed < $subrequestcount);

		$request['options']['hooks']->dispatch('curl.after_multi_exec', [&$multihandle]);

		curl_multi_close($multihandle);

		return $responses;
	}

	/**
	 * Get the cURL handle for use in a multi-request
	 *
	 * @param string $url URL to request
	 * @param array $headers Associative array of request headers
	 * @param string|array $data Data to send either as the POST body, or as parameters in the URL for a GET/HEAD
	 * @param array $options Request options, see {@see \WpOrg\Requests\Requests::response()} for documentation
	 * @return resource|\CurlHandle Subrequest's cURL handle
	 */
	public function &get_subrequest_handle($url, $headers, $data, $options) {
		$this->setup_handle($url, $headers, $data, $options);

		if ($options['filename'] !== false) {
			$this->stream_handle = fopen($options['filename'], 'wb');
		}

		$this->response_data       = '';
		$this->response_bytes      = 0;
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
	 * @param array $options Request options, see {@see \WpOrg\Requests\Requests::response()} for documentation
	 */
	private function setup_handle($url, $headers, $data, $options) {
		$options['hooks']->dispatch('curl.before_request', [&$this->handle]);

		// Force closing the connection for old versions of cURL (<7.22).
		if (!isset($headers['Connection'])) {
			$headers['Connection'] = 'close';
		}

		/**
		 * Add "Expect" header.
		 *
		 * By default, cURL adds a "Expect: 100-Continue" to most requests. This header can
		 * add as much as a second to the time it takes for cURL to perform a request. To
		 * prevent this, we need to set an empty "Expect" header. To match the behaviour of
		 * Guzzle, we'll add the empty header to requests that are smaller than 1 MB and use
		 * HTTP/1.1.
		 *
		 * https://curl.se/mail/lib-2017-07/0013.html
		 */
		if (!isset($headers['Expect']) && $options['protocol_version'] === 1.1) {
			$headers['Expect'] = $this->get_expect_header($data);
		}

		$headers = Requests::flatten($headers);

		if (!empty($data)) {
			$data_format = $options['data_format'];

			if ($data_format === 'query') {
				$url  = self::format_get($url, $data);
				$data = '';
			}
			elseif (!is_string($data)) {
				$data = http_build_query($data, '', '&');
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
					curl_setopt($this->handle, CURLOPT_POSTFIELDS, $data);
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
			// phpcs:ignore PHPCompatibility.Constants.NewConstants.curlopt_timeout_msFound
			curl_setopt($this->handle, CURLOPT_TIMEOUT_MS, round($timeout * 1000));
		}

		if (is_int($options['connect_timeout']) || $this->version < self::CURL_7_16_2) {
			curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT, ceil($options['connect_timeout']));
		}
		else {
			// phpcs:ignore PHPCompatibility.Constants.NewConstants.curlopt_connecttimeout_msFound
			curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT_MS, round($options['connect_timeout'] * 1000));
		}
		curl_setopt($this->handle, CURLOPT_URL, $url);
		curl_setopt($this->handle, CURLOPT_USERAGENT, $options['useragent']);
		if (!empty($headers)) {
			curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
		}
		if ($options['protocol_version'] === 1.1) {
			curl_setopt($this->handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		}
		else {
			curl_setopt($this->handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		}

		if ($options['blocking'] === true) {
			curl_setopt($this->handle, CURLOPT_HEADERFUNCTION, [$this, 'stream_headers']);
			curl_setopt($this->handle, CURLOPT_WRITEFUNCTION, [$this, 'stream_body']);
			curl_setopt($this->handle, CURLOPT_BUFFERSIZE, Requests::BUFFER_SIZE);
		}
	}

	/**
	 * Process a response
	 *
	 * @param string $response Response data from the body
	 * @param array $options Request options
	 * @return string|false HTTP response data including headers. False if non-blocking.
	 * @throws \WpOrg\Requests\Exception
	 */
	public function process_response($response, $options) {
		if ($options['blocking'] === false) {
			$fake_headers = '';
			$options['hooks']->dispatch('curl.after_request', [&$fake_headers]);
			return false;
		}
		if ($options['filename'] !== false && $this->stream_handle) {
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
			throw new Exception($error, 'curlerror', $this->handle);
		}
		$this->info = curl_getinfo($this->handle);

		$options['hooks']->dispatch('curl.after_request', [&$this->headers, &$this->info]);
		return $this->headers;
	}

	/**
	 * Collect the headers as they are received
	 *
	 * @param resource|\CurlHandle $handle cURL handle
	 * @param string $headers Header string
	 * @return integer Length of provided header
	 */
	public function stream_headers($handle, $headers) {
		// Why do we do this? cURL will send both the final response and any
		// interim responses, such as a 100 Continue. We don't need that.
		// (We may want to keep this somewhere just in case)
		if ($this->done_headers) {
			$this->headers      = '';
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
	 * @param resource|\CurlHandle $handle cURL handle
	 * @param string $data Body data
	 * @return integer Length of provided data
	 */
	public function stream_body($handle, $data) {
		$this->hooks->dispatch('request.progress', [$data, $this->response_bytes, $this->response_byte_limit]);
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
				$data           = substr($data, 0, $limited_length);
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
	 * @param array|object $data Data to build query using, see {@link https://www.php.net/http_build_query}
	 * @return string URL with data
	 */
	private static function format_get($url, $data) {
		if (!empty($data)) {
			$query     = '';
			$url_parts = parse_url($url);
			if (empty($url_parts['query'])) {
				$url_parts['query'] = '';
			}
			else {
				$query = $url_parts['query'];
			}

			$query .= '&' . http_build_query($data, '', '&');
			$query  = trim($query, '&');

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
	 * Self-test whether the transport can be used.
	 *
	 * The available capabilities to test for can be found in {@see \WpOrg\Requests\Capability}.
	 *
	 * @codeCoverageIgnore
	 * @param array<string, bool> $capabilities Optional. Associative array of capabilities to test against, i.e. `['<capability>' => true]`.
	 * @return bool Whether the transport can be used.
	 */
	public static function test($capabilities = []) {
		if (!function_exists('curl_init') || !function_exists('curl_exec')) {
			return false;
		}

		// If needed, check that our installed curl version supports SSL
		if (isset($capabilities[Capability::SSL]) && $capabilities[Capability::SSL]) {
			$curl_version = curl_version();
			if (!(CURL_VERSION_SSL & $curl_version['features'])) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the correct "Expect" header for the given request data.
	 *
	 * @param string|array $data Data to send either as the POST body, or as parameters in the URL for a GET/HEAD.
	 * @return string The "Expect" header.
	 */
	private function get_expect_header($data) {
		if (!is_array($data)) {
			return strlen((string) $data) >= 1048576 ? '100-Continue' : '';
		}

		$bytesize = 0;
		$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));

		foreach ($iterator as $datum) {
			$bytesize += strlen((string) $datum);

			if ($bytesize >= 1048576) {
				return '100-Continue';
			}
		}

		return '';
	}
}
