<?php
/**
 * fsockopen HTTP transport
 *
 * @package Requests\Transport
 */

namespace WpOrg\Requests\Transport;

use WpOrg\Requests\Capability;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Port;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Ssl;
use WpOrg\Requests\Transport;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;
use WpOrg\Requests\Utility\InputValidator;

/**
 * fsockopen HTTP transport
 *
 * @package Requests\Transport
 */
final class Fsockopen implements Transport {
	/**
	 * Second to microsecond conversion
	 *
	 * @var integer
	 */
	const SECOND_IN_MICROSECONDS = 1000000;

	/**
	 * Raw HTTP data
	 *
	 * @var string
	 */
	public $headers = '';

	/**
	 * Stream metadata
	 *
	 * @var array Associative array of properties, see {@link https://www.php.net/stream_get_meta_data}
	 */
	public $info;

	/**
	 * What's the maximum number of bytes we should keep?
	 *
	 * @var int|bool Byte count, or false if no limit.
	 */
	private $max_bytes = false;

	/**
	 * Cache for received connection errors.
	 *
	 * @var string
	 */
	private $connect_error = '';

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
	 * @throws \WpOrg\Requests\Exception       On failure to connect to socket (`fsockopenerror`)
	 * @throws \WpOrg\Requests\Exception       On socket timeout (`timeout`)
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

		$options['hooks']->dispatch('fsockopen.before_request');

		$url_parts = parse_url($url);
		if (empty($url_parts)) {
			throw new Exception('Invalid URL.', 'invalidurl', $url);
		}

		$host                     = $url_parts['host'];
		$context                  = stream_context_create();
		$verifyname               = false;
		$case_insensitive_headers = new CaseInsensitiveDictionary($headers);

		// HTTPS support
		if (isset($url_parts['scheme']) && strtolower($url_parts['scheme']) === 'https') {
			$remote_socket = 'ssl://' . $host;
			if (!isset($url_parts['port'])) {
				$url_parts['port'] = Port::HTTPS;
			}

			$context_options = [
				'verify_peer'       => true,
				'capture_peer_cert' => true,
			];
			$verifyname      = true;

			// SNI, if enabled (OpenSSL >=0.9.8j)
			// phpcs:ignore PHPCompatibility.Constants.NewConstants.openssl_tlsext_server_nameFound
			if (defined('OPENSSL_TLSEXT_SERVER_NAME') && OPENSSL_TLSEXT_SERVER_NAME) {
				$context_options['SNI_enabled'] = true;
				if (isset($options['verifyname']) && $options['verifyname'] === false) {
					$context_options['SNI_enabled'] = false;
				}
			}

			if (isset($options['verify'])) {
				if ($options['verify'] === false) {
					$context_options['verify_peer']      = false;
					$context_options['verify_peer_name'] = false;
					$verifyname                          = false;
				} elseif (is_string($options['verify'])) {
					$context_options['cafile'] = $options['verify'];
				}
			}

			if (isset($options['verifyname']) && $options['verifyname'] === false) {
				$context_options['verify_peer_name'] = false;
				$verifyname                          = false;
			}

			// Handle the PHP 8.4 deprecation (PHP 9.0 removal) of the function signature we use for stream_context_set_option().
			// Ref: https://wiki.php.net/rfc/deprecate_functions_with_overloaded_signatures#stream_context_set_option
			if (function_exists('stream_context_set_options')) {
				// PHP 8.3+.
				stream_context_set_options($context, ['ssl' => $context_options]);
			} else {
				// PHP < 8.3.
				stream_context_set_option($context, ['ssl' => $context_options]);
			}
		} else {
			$remote_socket = 'tcp://' . $host;
		}

		$this->max_bytes = $options['max_bytes'];

		if (!isset($url_parts['port'])) {
			$url_parts['port'] = Port::HTTP;
		}

		$remote_socket .= ':' . $url_parts['port'];

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_set_error_handler
		set_error_handler([$this, 'connect_error_handler'], E_WARNING | E_NOTICE);

		$options['hooks']->dispatch('fsockopen.remote_socket', [&$remote_socket]);

		$socket = stream_socket_client($remote_socket, $errno, $errstr, ceil($options['connect_timeout']), STREAM_CLIENT_CONNECT, $context);

		restore_error_handler();

		if ($verifyname && !$this->verify_certificate_from_context($host, $context)) {
			throw new Exception('SSL certificate did not match the requested domain name', 'ssl.no_match');
		}

		if (!$socket) {
			if ($errno === 0) {
				// Connection issue
				throw new Exception(rtrim($this->connect_error), 'fsockopen.connect_error');
			}

			throw new Exception($errstr, 'fsockopenerror', null, $errno);
		}

		$data_format = $options['data_format'];

		if ($data_format === 'query') {
			$path = self::format_get($url_parts, $data);
			$data = '';
		} else {
			$path = self::format_get($url_parts, []);
		}

		$options['hooks']->dispatch('fsockopen.remote_host_path', [&$path, $url]);

		$request_body = '';
		$out          = sprintf("%s %s HTTP/%.1F\r\n", $options['type'], $path, $options['protocol_version']);

		if ($options['type'] !== Requests::TRACE) {
			if (is_array($data)) {
				$request_body = http_build_query($data, '', '&');
			} else {
				$request_body = $data;
			}

			// Always include Content-length on POST requests to prevent
			// 411 errors from some servers when the body is empty.
			if (!empty($data) || $options['type'] === Requests::POST) {
				if (!isset($case_insensitive_headers['Content-Length'])) {
					$headers['Content-Length'] = strlen($request_body);
				}

				if (!isset($case_insensitive_headers['Content-Type'])) {
					$headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
				}
			}
		}

		if (!isset($case_insensitive_headers['Host'])) {
			$out         .= sprintf('Host: %s', $url_parts['host']);
			$scheme_lower = strtolower($url_parts['scheme']);

			if (($scheme_lower === 'http' && $url_parts['port'] !== Port::HTTP) || ($scheme_lower === 'https' && $url_parts['port'] !== Port::HTTPS)) {
				$out .= ':' . $url_parts['port'];
			}

			$out .= "\r\n";
		}

		if (!isset($case_insensitive_headers['User-Agent'])) {
			$out .= sprintf("User-Agent: %s\r\n", $options['useragent']);
		}

		$accept_encoding = $this->accept_encoding();
		if (!isset($case_insensitive_headers['Accept-Encoding']) && !empty($accept_encoding)) {
			$out .= sprintf("Accept-Encoding: %s\r\n", $accept_encoding);
		}

		$headers = Requests::flatten($headers);

		if (!empty($headers)) {
			$out .= implode("\r\n", $headers) . "\r\n";
		}

		$options['hooks']->dispatch('fsockopen.after_headers', [&$out]);

		if (substr($out, -2) !== "\r\n") {
			$out .= "\r\n";
		}

		if (!isset($case_insensitive_headers['Connection'])) {
			$out .= "Connection: Close\r\n";
		}

		$out .= "\r\n" . $request_body;

		$options['hooks']->dispatch('fsockopen.before_send', [&$out]);

		fwrite($socket, $out);
		$options['hooks']->dispatch('fsockopen.after_send', [$out]);

		if (!$options['blocking']) {
			fclose($socket);
			$fake_headers = '';
			$options['hooks']->dispatch('fsockopen.after_request', [&$fake_headers]);
			return '';
		}

		$timeout_sec = (int) floor($options['timeout']);
		if ($timeout_sec === $options['timeout']) {
			$timeout_msec = 0;
		} else {
			$timeout_msec = self::SECOND_IN_MICROSECONDS * $options['timeout'] % self::SECOND_IN_MICROSECONDS;
		}

		stream_set_timeout($socket, $timeout_sec, $timeout_msec);

		$response   = '';
		$body       = '';
		$headers    = '';
		$this->info = stream_get_meta_data($socket);
		$size       = 0;
		$doingbody  = false;
		$download   = false;
		if ($options['filename']) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors -- Silenced the PHP native warning in favour of throwing an exception.
			$download = @fopen($options['filename'], 'wb');
			if ($download === false) {
				$error = error_get_last();
				throw new Exception($error['message'], 'fopen');
			}
		}

		while (!feof($socket)) {
			$this->info = stream_get_meta_data($socket);
			if ($this->info['timed_out']) {
				throw new Exception('fsocket timed out', 'timeout');
			}

			$block = fread($socket, Requests::BUFFER_SIZE);
			if (!$doingbody) {
				$response .= $block;
				if (strpos($response, "\r\n\r\n")) {
					list($headers, $block) = explode("\r\n\r\n", $response, 2);
					$doingbody             = true;
				}
			}

			// Are we in body mode now?
			if ($doingbody) {
				$options['hooks']->dispatch('request.progress', [$block, $size, $this->max_bytes]);
				$data_length = strlen($block);
				if ($this->max_bytes) {
					// Have we already hit a limit?
					if ($size === $this->max_bytes) {
						continue;
					}

					if (($size + $data_length) > $this->max_bytes) {
						// Limit the length
						$limited_length = ($this->max_bytes - $size);
						$block          = substr($block, 0, $limited_length);
					}
				}

				$size += strlen($block);
				if ($download) {
					fwrite($download, $block);
				} else {
					$body .= $block;
				}
			}
		}

		$this->headers = $headers;

		if ($download) {
			fclose($download);
		} else {
			$this->headers .= "\r\n\r\n" . $body;
		}

		fclose($socket);

		$options['hooks']->dispatch('fsockopen.after_request', [&$this->headers, &$this->info]);
		return $this->headers;
	}

	/**
	 * Send multiple requests simultaneously
	 *
	 * @param array $requests Request data (array of 'url', 'headers', 'data', 'options') as per {@see \WpOrg\Requests\Transport::request()}
	 * @param array $options Global options, see {@see \WpOrg\Requests\Requests::response()} for documentation
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

		$responses = [];
		$class     = get_class($this);
		foreach ($requests as $id => $request) {
			try {
				$handler        = new $class();
				$responses[$id] = $handler->request($request['url'], $request['headers'], $request['data'], $request['options']);

				$request['options']['hooks']->dispatch('transport.internal.parse_response', [&$responses[$id], $request]);
			} catch (Exception $e) {
				$responses[$id] = $e;
			}

			if (!is_string($responses[$id])) {
				$request['options']['hooks']->dispatch('multiple.request.complete', [&$responses[$id], $id]);
			}
		}

		return $responses;
	}

	/**
	 * Retrieve the encodings we can accept
	 *
	 * @return string Accept-Encoding header value
	 */
	private static function accept_encoding() {
		$type = [];
		if (function_exists('gzinflate')) {
			$type[] = 'deflate;q=1.0';
		}

		if (function_exists('gzuncompress')) {
			$type[] = 'compress;q=0.5';
		}

		$type[] = 'gzip;q=0.5';

		return implode(', ', $type);
	}

	/**
	 * Format a URL given GET data
	 *
	 * @param array        $url_parts Array of URL parts as received from {@link https://www.php.net/parse_url}
	 * @param array|object $data Data to build query using, see {@link https://www.php.net/http_build_query}
	 * @return string URL with data
	 */
	private static function format_get($url_parts, $data) {
		if (!empty($data)) {
			if (empty($url_parts['query'])) {
				$url_parts['query'] = '';
			}

			$url_parts['query'] .= '&' . http_build_query($data, '', '&');
			$url_parts['query']  = trim($url_parts['query'], '&');
		}

		if (isset($url_parts['path'])) {
			if (isset($url_parts['query'])) {
				$get = $url_parts['path'] . '?' . $url_parts['query'];
			} else {
				$get = $url_parts['path'];
			}
		} else {
			$get = '/';
		}

		return $get;
	}

	/**
	 * Error handler for stream_socket_client()
	 *
	 * @param int $errno Error number (e.g. E_WARNING)
	 * @param string $errstr Error message
	 */
	public function connect_error_handler($errno, $errstr) {
		// Double-check we can handle it
		if (($errno & E_WARNING) === 0 && ($errno & E_NOTICE) === 0) {
			// Return false to indicate the default error handler should engage
			return false;
		}

		$this->connect_error .= $errstr . "\n";
		return true;
	}

	/**
	 * Verify the certificate against common name and subject alternative names
	 *
	 * Unfortunately, PHP doesn't check the certificate against the alternative
	 * names, leading things like 'https://www.github.com/' to be invalid.
	 * Instead
	 *
	 * @link https://tools.ietf.org/html/rfc2818#section-3.1 RFC2818, Section 3.1
	 *
	 * @param string $host Host name to verify against
	 * @param resource $context Stream context
	 * @return bool
	 *
	 * @throws \WpOrg\Requests\Exception On failure to connect via TLS (`fsockopen.ssl.connect_error`)
	 * @throws \WpOrg\Requests\Exception On not obtaining a match for the host (`fsockopen.ssl.no_match`)
	 */
	public function verify_certificate_from_context($host, $context) {
		$meta = stream_context_get_options($context);

		// If we don't have SSL options, then we couldn't make the connection at
		// all
		if (empty($meta) || empty($meta['ssl']) || empty($meta['ssl']['peer_certificate'])) {
			throw new Exception(rtrim($this->connect_error), 'ssl.connect_error');
		}

		$cert = openssl_x509_parse($meta['ssl']['peer_certificate']);

		return Ssl::verify_certificate($host, $cert);
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
		if (!function_exists('fsockopen')) {
			return false;
		}

		// If needed, check that streams support SSL
		if (isset($capabilities[Capability::SSL]) && $capabilities[Capability::SSL]) {
			if (!extension_loaded('openssl') || !function_exists('openssl_x509_parse')) {
				return false;
			}
		}

		return true;
	}
}
