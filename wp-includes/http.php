<?php
/**
 * Simple and uniform HTTP request API.
 *
 * Will eventually replace and standardize the WordPress HTTP requests made.
 *
 * @link http://trac.wordpress.org/ticket/4779 HTTP API Proposal
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7
 * @author Jacob Santos <wordpress@santosj.name>
 */

/**
 * WordPress HTTP Class for managing HTTP Transports and making HTTP requests.
 *
 * This class is called for the functionality of making HTTP requests and should
 * replace Snoopy functionality, eventually. There is no available functionality
 * to add HTTP transport implementations, since most of the HTTP transports are
 * added and available for use.
 *
 * The exception is that cURL is not available as a transport and lacking an
 * implementation. It will be added later and should be a patch on the WordPress
 * Trac.
 *
 * There are no properties, because none are needed and for performance reasons.
 * Some of the functions are static and while they do have some overhead over
 * functions in PHP4, the purpose is maintainability. When PHP5 is finally the
 * requirement, it will be easy to add the static keyword to the code. It is not
 * as easy to convert a function to a method after enough code uses the old way.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7
 */
class WP_Http {

	/**
	 * PHP4 style Constructor - Calls PHP5 Style Constructor
	 *
	 * @since 2.7
	 * @return WP_Http
	 */
	function WP_Http() {
		$this->__construct();
	}

	/**
	 * PHP5 style Constructor - Setup available transport if not available.
	 *
	 * PHP4 does not have the 'self' keyword and since WordPress supports PHP4,
	 * the class needs to be used for the static call.
	 *
	 * The transport are setup to save time. This should only be called once, so
	 * the overhead should be fine.
	 *
	 * @since 2.7
	 * @return WP_Http
	 */
	function __construct() {
		WP_Http::_getTransport();
		WP_Http::_postTransport();
	}

	/**
	 * Tests the WordPress HTTP objects for an object to use and returns it.
	 *
	 * Tests all of the objects and returns the object that passes. Also caches
	 * that object to be used later.
	 *
	 * The order for the GET/HEAD requests are Streams, HTTP Extension, Fopen,
	 * and finally Fsockopen. fsockopen() is used last, because it has the most
	 * overhead in its implementation. There isn't any real way around it, since
	 * redirects have to be supported, much the same way the other transports
	 * also handle redirects.
	 *
	 * There are currently issues with "localhost" not resolving correctly with
	 * DNS. This may cause an error "failed to open stream: A connection attempt
	 * failed because the connected party did not properly respond after a
	 * period of time, or established connection failed because connected host
	 * has failed to respond."
	 *
	 * @since 2.7
	 * @access private
	 *
	 * @param array $args Request args, default us an empty array
	 * @return object|null Null if no transports are available, HTTP transport object.
	 */
	function &_getTransport( $args = array() ) {
		static $working_transport, $blocking_transport, $nonblocking_transport;

		if ( is_null($working_transport) ) {
			if ( true === WP_Http_ExtHttp::test() && apply_filters('use_http_extension_transport', true) ) {
				$working_transport['exthttp'] = new WP_Http_ExtHttp();
				$blocking_transport[] = &$working_transport['exthttp'];
			} else if ( true === WP_Http_Curl::test() && apply_filters('use_curl_transport', true) ) {
				$working_transport['curl'] = new WP_Http_Curl();
				$blocking_transport[] = &$working_transport['curl'];
			} else if ( true === WP_Http_Streams::test() && apply_filters('use_streams_transport', true) ) {
				$working_transport['streams'] = new WP_Http_Streams();
				$blocking_transport[] = &$working_transport['streams'];
			} else if ( true === WP_Http_Fopen::test() && apply_filters('use_fopen_transport', true) ) {
				$working_transport['fopen'] = new WP_Http_Fopen();
				$blocking_transport[] = &$working_transport['fopen'];
			} else if ( true === WP_Http_Fsockopen::test() && apply_filters('use_fsockopen_transport', true) ) {
				$working_transport['fsockopen'] = new WP_Http_Fsockopen();
				$blocking_transport[] = &$working_transport['fsockopen'];
			}

			foreach ( array('curl', 'streams', 'fopen', 'fsockopen', 'exthttp') as $transport ) {
				if ( isset($working_transport[$transport]) )
					$nonblocking_transport[] = &$working_transport[$transport];
			}
		}

		if ( isset($args['blocking']) && !$args['blocking'] )
			return $nonblocking_transport;
		else
			return $blocking_transport;
	}

	/**
	 * Tests the WordPress HTTP objects for an object to use and returns it.
	 *
	 * Tests all of the objects and returns the object that passes. Also caches
	 * that object to be used later. This is for posting content to a URL and
	 * is used when there is a body. The plain Fopen Transport can not be used
	 * to send content, but the streams transport can. This is a limitation that
	 * is addressed here, by just not including that transport.
	 *
	 * @since 2.7
	 * @access private
	 *
	 * @param array $args Request args, default us an empty array
	 * @return object|null Null if no transports are available, HTTP transport object.
	 */
	function &_postTransport( $args = array() ) {
		static $working_transport, $blocking_transport, $nonblocking_transport;

		if ( is_null($working_transport) ) {
			if ( true === WP_Http_ExtHttp::test() && apply_filters('use_http_extension_transport', true) ) {
				$working_transport['exthttp'] = new WP_Http_ExtHttp();
				$blocking_transport[] = &$working_transport['exthttp'];
			} else if ( true === WP_Http_Streams::test() && apply_filters('use_streams_transport', true) ) {
				$working_transport['streams'] = new WP_Http_Streams();
				$blocking_transport[] = &$working_transport['streams'];
			} else if ( true === WP_Http_Fsockopen::test() && apply_filters('use_fsockopen_transport', true) ) {
				$working_transport['fsockopen'] = new WP_Http_Fsockopen();
				$blocking_transport[] = &$working_transport['fsockopen'];
			}

			foreach ( array('streams', 'fsockopen', 'exthttp') as $transport ) {
				if ( isset($working_transport[$transport]) )
					$nonblocking_transport[] = &$working_transport[$transport];
			}
		}

		if ( isset($args['blocking']) && !$args['blocking'] )
			return $nonblocking_transport;
		else
			return $blocking_transport;
	}

	/**
	 * Send a HTTP request to a URI.
	 *
	 * The body and headers are part of the arguments. The 'body' argument is
	 * for the body and will accept either a string or an array. The 'headers'
	 * argument should be an array, but a string is acceptable. If the 'body'
	 * argument is an array, then it will automatically be escaped using
	 * http_build_query().
	 *
	 * The only URI that are supported in the HTTP Transport implementation are
	 * the HTTP and HTTPS protocols. HTTP and HTTPS are assumed so the server
	 * might not know how to handle the send headers. Other protocols are
	 * unsupported and most likely will fail.
	 *
	 * The defaults are 'method', 'timeout', 'redirection', 'httpversion',
	 * 'blocking' and 'user-agent'.
	 *
	 * Accepted 'method' values are 'GET', 'POST', and 'HEAD', some transports
	 * technically allow others, but should not be assumed. The 'timeout' is
	 * used to sent how long the connection should stay open before failing when
	 * no response. 'redirection' is used to track how many redirects were taken
	 * and used to sent the amount for other transports, but not all transports
	 * accept setting that value.
	 *
	 * The 'httpversion' option is used to sent the HTTP version and accepted
	 * values are '1.0', and '1.1' and should be a string. Version 1.1 is not
	 * supported, because of chunk response. The 'user-agent' option is the
	 * user-agent and is used to replace the default user-agent, which is
	 * 'WordPress/WP_Version', where WP_Version is the value from $wp_version.
	 *
	 * 'blocking' is the default, which is used to tell the transport, whether
	 * it should halt PHP while it performs the request or continue regardless.
	 * Actually, that isn't entirely correct. Blocking mode really just means
	 * whether the fread should just pull what it can whenever it gets bytes or
	 * if it should wait until it has enough in the buffer to read or finishes
	 * reading the entire content. It doesn't actually always mean that PHP will
	 * continue going after making the request.
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param string $url URI resource.
	 * @param str|array $args Optional. Override the defaults.
	 * @return boolean
	 */
	function request( $url, $args = array() ) {
		global $wp_version;

		$defaults = array(
			'method' => 'GET',
			'timeout' => apply_filters( 'http_request_timeout', 5),
			'redirection' => apply_filters( 'http_request_redirection_count', 5),
			'httpversion' => apply_filters( 'http_request_version', '1.0'),
			'user-agent' => apply_filters( 'http_headers_useragent', 'WordPress/' . $wp_version ),
			'blocking' => true,
			'headers' => array(), 'body' => null
		);

		$r = wp_parse_args( $args, $defaults );
		$r = apply_filters( 'http_request_args', $r );

		if ( is_null( $r['headers'] ) )
			$r['headers'] = array();

		if ( ! is_array($r['headers']) ) {
			$processedHeaders = WP_Http::processHeaders($r['headers']);
			$r['headers'] = $processedHeaders['headers'];
		}

		if ( isset($r['headers']['User-Agent']) ) {
			$r['user-agent'] = $r['headers']['User-Agent'];
			unset($r['headers']['User-Agent']);
		}

		if ( isset($r['headers']['user-agent']) ) {
			$r['user-agent'] = $r['headers']['user-agent'];
			unset($r['headers']['user-agent']);
		}

		if ( is_null($r['body']) ) {
			$transports = WP_Http::_getTransport($r);
		} else {
			if ( is_array( $r['body'] ) || is_object( $r['body'] ) ) {
				$r['body'] = http_build_query($r['body'], null, '&');
				$r['headers']['Content-Type'] = 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset');
				$r['headers']['Content-Length'] = strlen($r['body']);
			}

			if ( ! isset( $r['headers']['Content-Length'] ) && ! isset( $r['headers']['content-length'] ) )
				$r['headers']['Content-Length'] = strlen($r['body']);

			$transports = WP_Http::_postTransport($r);
		}

		$response = array( 'headers' => array(), 'body' => '', 'response' => array('code', 'message') );
		foreach( (array) $transports as $transport ) {
			$response = $transport->request($url, $r);

			if( !is_wp_error($response) )
				return $response;
		}

		return $response;
	}

	/**
	 * Uses the POST HTTP method.
	 *
	 * Used for sending data that is expected to be in the body.
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param string $url URI resource.
	 * @param str|array $args Optional. Override the defaults.
	 * @return boolean
	 */
	function post($url, $args = array()) {
		$defaults = array('method' => 'POST');
		$r = wp_parse_args( $args, $defaults );
		return $this->request($url, $r);
	}

	/**
	 * Uses the GET HTTP method.
	 *
	 * Used for sending data that is expected to be in the body.
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param string $url URI resource.
	 * @param str|array $args Optional. Override the defaults.
	 * @return boolean
	 */
	function get($url, $args = array()) {
		$defaults = array('method' => 'GET');
		$r = wp_parse_args( $args, $defaults );
		return $this->request($url, $r);
	}

	/**
	 * Uses the HEAD HTTP method.
	 *
	 * Used for sending data that is expected to be in the body.
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param string $url URI resource.
	 * @param str|array $args Optional. Override the defaults.
	 * @return boolean
	 */
	function head($url, $args = array()) {
		$defaults = array('method' => 'HEAD');
		$r = wp_parse_args( $args, $defaults );
		return $this->request($url, $r);
	}

	/**
	 * Parses the responses and splits the parts into headers and body.
	 *
	 * @access public
	 * @static
	 * @since 2.7
	 *
	 * @param string $strResponse The full response string
	 * @return array Array with 'headers' and 'body' keys.
	 */
	function processResponse($strResponse) {
		list($theHeaders, $theBody) = explode("\r\n\r\n", $strResponse, 2);
		return array('headers' => $theHeaders, 'body' => $theBody);
	}

	/**
	 * Transform header string into an array.
	 *
	 * If an array is given then it is assumed to be raw header data with
	 * numeric keys with the headers as the values. No headers must be passed
	 * that were already processed.
	 *
	 * @access public
	 * @static
	 * @since 2.7
	 *
	 * @param string|array $headers
	 * @return array Processed string headers
	 */
	function processHeaders($headers) {
		if ( is_string($headers) )
			$headers = explode("\n", str_replace(array("\r\n", "\r"), "\n", $headers) );

		$response = array('code' => 0, 'message' => '');

		$newheaders = array();
		foreach ( $headers as $tempheader ) {
			if ( empty($tempheader) )
				continue;

			if ( false === strpos($tempheader, ':') ) {
				list( , $iResponseCode, $strResponseMsg) = explode(' ', $tempheader, 3);
				$response['code'] = $iResponseCode;
				$response['message'] = $strResponseMsg;
				continue;
			}

			list($key, $value) = explode(':', $tempheader, 2);

			if ( ! empty($value) )
				$newheaders[strtolower($key)] = trim($value);
		}

		return array('response' => $response, 'headers' => $newheaders);
	}

	/**
	 * Decodes chunk transfer-encoding, based off the HTTP 1.1 specification.
	 *
	 * Based off the HTTP http_encoding_dechunk function. Does not support
	 * UTF-8. Does not support returning footer headers. Shouldn't be too
	 * difficult to support it though.
	 *
	 * @todo Add support for footer chunked headers.
	 * @access public
	 * @since 2.7
	 * @static
	 *
	 * @param string $body Body content
	 * @return bool|string|WP_Error False if not chunked encoded. WP_Error on failure. Chunked decoded body on success.
	 */
	function chunkTransferDecode($body) {
		$body = str_replace(array("\r\n", "\r"), "\n", $body);
		// The body is not chunked encoding or is malformed.
		if ( ! preg_match( '/^[0-9a-f]+(\s|\n)+/mi', trim($body) ) )
			return $body;

		$parsedBody = '';
		//$parsedHeaders = array(); Unsupported

		$done = false;

		do {
			$hasChunk = (bool) preg_match( '/^([0-9a-f]+)(\s|\n)+/mi', $body, $match );

			if ( $hasChunk ) {
				if ( empty($match[1]) ) {
					return new WP_Error('http_chunked_decode', __('Does not appear to be chunked encoded or body is malformed.') );
				}

				$length = hexdec( $match[1] );
				$chunkLength = strlen( $match[0] );

				$strBody = substr($body, $chunkLength, $length);
				$parsedBody .= $strBody;

				$body = ltrim(str_replace(array($match[0], $strBody), '', $body), "\n");

				if( "0" == trim($body) ) {
					$done = true;
					return $parsedBody; // Ignore footer headers.
					break;
				}
			} else {
				return new WP_Error('http_chunked_decode', __('Does not appear to be chunked encoded or body is malformed.') );
			}
		} while ( false === $done );
	}
}

/**
 * HTTP request method uses fsockopen function to retrieve the url.
 *
 * This would be the preferred method, but the fsockopen implementation has the
 * most overhead of all the HTTP transport implementations.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7
 */
class WP_Http_Fsockopen {
	/**
	 * Send a HTTP request to a URI using fsockopen().
	 *
	 * Does not support non-blocking mode.
	 *
	 * @see WP_Http::request For default options descriptions.
	 *
	 * @since 2.7
	 * @access public
	 * @param string $url URI resource.
	 * @param str|array $args Optional. Override the defaults.
	 * @return array 'headers', 'body', and 'response' keys.
	 */
	function request($url, $args = array()) {
		$defaults = array(
			'method' => 'GET', 'timeout' => 5,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(), 'body' => null
		);

		$r = wp_parse_args( $args, $defaults );

		if ( isset($r['headers']['User-Agent']) ) {
			$r['user-agent'] = $r['headers']['User-Agent'];
			unset($r['headers']['User-Agent']);
		} else if( isset($r['headers']['user-agent']) ) {
			$r['user-agent'] = $r['headers']['user-agent'];
			unset($r['headers']['user-agent']);
		}

		$iError = null; // Store error number
		$strError = null; // Store error string

		$arrURL = parse_url($url);

		$secure_transport = false;

		if ( ! isset($arrURL['port']) ) {
			if ( ($arrURL['scheme'] == 'ssl' || $arrURL['scheme'] == 'https') && extension_loaded('openssl') ) {
				$arrURL['host'] = 'ssl://' . $arrURL['host'];
				$arrURL['port'] = apply_filters('http_request_port', 443);
				$secure_transport = true;
			} else {
				$arrURL['port'] = apply_filters('http_request_default_port', 80);
			}
		} else {
			$arrURL['port'] = apply_filters('http_request_port', $arrURL['port']);
		}

		// There are issues with the HTTPS and SSL protocols that cause errors
		// that can be safely ignored and should be ignored.
		if ( true === $secure_transport )
			$error_reporting = error_reporting(0);

		$startDelay = time();

		if ( !defined('WP_DEBUG') || ( defined('WP_DEBUG') && false === WP_DEBUG ) )
			$handle = @fsockopen($arrURL['host'], $arrURL['port'], $iError, $strError, $r['timeout'] );
		else
			$handle = fsockopen($arrURL['host'], $arrURL['port'], $iError, $strError, $r['timeout'] );

		$endDelay = time();

		// If the delay is greater than the timeout then fsockopen should't be
		// used, because it will cause a long delay.
		$elapseDelay = ($endDelay-$startDelay) > $r['timeout'];
		if ( true === $elapseDelay )
			add_option( 'disable_fsockopen', $endDelay, null, true );

		if ( false === $handle )
			return new WP_Error('http_request_failed', $iError . ': ' . $strError);

		// WordPress supports PHP 4.3, which has this function. Removed sanity
		// checking for performance reasons.
		stream_set_timeout($handle, $r['timeout'] );

		$requestPath = $arrURL['path'] . ( isset($arrURL['query']) ? '?' . $arrURL['query'] : '' );
		$requestPath = empty($requestPath) ? '/' : $requestPath;

		$strHeaders = '';
		$strHeaders .= strtoupper($r['method']) . ' ' . $requestPath . ' HTTP/' . $r['httpversion'] . "\r\n";
		$strHeaders .= 'Host: ' . $arrURL['host'] . "\r\n";

		if( isset($r['user-agent']) )
			$strHeaders .= 'User-agent: ' . $r['user-agent'] . "\r\n";

		if ( is_array($r['headers']) ) {
			foreach ( (array) $r['headers'] as $header => $headerValue )
				$strHeaders .= $header . ': ' . $headerValue . "\r\n";
		} else {
			$strHeaders .= $r['headers'];
		}

		$strHeaders .= "\r\n";

		if ( ! is_null($r['body']) )
			$strHeaders .= $r['body'];

		fwrite($handle, $strHeaders);

		if ( ! $r['blocking'] ) {
			fclose($handle);
			return array( 'headers' => array(), 'body' => '', 'response' => array('code', 'message') );
		}

		$strResponse = '';
		while ( ! feof($handle) )
			$strResponse .= fread($handle, 4096);

		fclose($handle);

		if ( true === $secure_transport )
			error_reporting($error_reporting);

		$process = WP_Http::processResponse($strResponse);
		$arrHeaders = WP_Http::processHeaders($process['headers']);

		// Is the response code within the 400 range?
		if ( (int) $arrHeaders['response']['code'] >= 400 && (int) $arrHeaders['response']['code'] < 500 )
			return new WP_Error('http_request_failed', $arrHeaders['response']['code'] . ': ' . $arrHeaders['response']['message']);

		// If location is found, then assume redirect and redirect to location.
		if ( isset($arrHeaders['headers']['location']) ) {
			if ( $r['redirection']-- > 0 ) {
				return $this->request($arrHeaders['headers']['location'], $r);
			} else {
				return new WP_Error('http_request_failed', __('Too many redirects.'));
			}
		}

		// If the body was chunk encoded, then decode it.
		if ( ! empty( $process['body'] ) && isset( $arrHeaders['headers']['transfer-encoding'] ) && 'chunked' == $arrHeaders['headers']['transfer-encoding'] )
			$process['body'] = WP_Http::chunkTransferDecode($process['body']);

		return array('headers' => $arrHeaders['headers'], 'body' => $process['body'], 'response' => $arrHeaders['response']);
	}

	/**
	 * Whether this class can be used for retrieving an URL.
	 *
	 * @since 2.7
	 * @static
	 * @return boolean False means this class can not be used, true means it can.
	 */
	function test() {
		if ( false !== ($option = get_option( 'disable_fsockopen' )) && time()-$option < 43200 ) // 12 hours
			return false;

		if ( function_exists( 'fsockopen' ) )
			return true;

		return false;
	}
}

/**
 * HTTP request method uses fopen function to retrieve the url.
 *
 * Requires PHP version greater than 4.3.0 for stream support. Does not allow
 * for $context support, but should still be okay, to write the headers, before
 * getting the response. Also requires that 'allow_url_fopen' to be enabled.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7
 */
class WP_Http_Fopen {
	/**
	 * Send a HTTP request to a URI using fopen().
	 *
	 * This transport does not support sending of headers and body, therefore
	 * should not be used in the instances, where there is a body and headers.
	 *
	 * Notes: Does not support non-blocking mode. Ignores 'redirection' option.
	 *
	 * @see WP_Http::retrieve For default options descriptions.
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param string $url URI resource.
	 * @param str|array $args Optional. Override the defaults.
	 * @return array 'headers', 'body', and 'response' keys.
	 */
	function request($url, $args = array()) {
		global $http_response_header;

		$defaults = array(
			'method' => 'GET', 'timeout' => 5,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(), 'body' => null
		);

		$r = wp_parse_args( $args, $defaults );

		$arrURL = parse_url($url);

		if ( false === $arrURL )
			return new WP_Error('http_request_failed', sprintf(__('Malformed URL: %s'), $url));

		if ( 'http' != $arrURL['scheme'] || 'https' != $arrURL['scheme'] )
			$url = str_replace($arrURL['scheme'], 'http', $url);

		if ( !defined('WP_DEBUG') || ( defined('WP_DEBUG') && false === WP_DEBUG ) )
			$handle = @fopen($url, 'r');
		else
			$handle = fopen($url, 'r');

		if (! $handle)
			return new WP_Error('http_request_failed', sprintf(__('Could not open handle for fopen() to %s'), $url));

		// WordPress supports PHP 4.3, which has this function. Removed sanity
		// checking for performance reasons.
		stream_set_timeout($handle, $r['timeout'] );

		if ( ! $r['blocking'] ) {
			fclose($handle);
			return array( 'headers' => array(), 'body' => '', 'response' => array('code', 'message') );
		}

		$strResponse = '';
		while ( ! feof($handle) )
			$strResponse .= fread($handle, 4096);

		$theHeaders = '';
		if ( function_exists('stream_get_meta_data') ) {
			$meta = stream_get_meta_data($handle);
			$theHeaders = $meta['wrapper_data'];
			if( isset( $meta['wrapper_data']['headers'] ) )
				$theHeaders = $meta['wrapper_data']['headers'];
		} else {
			if( ! isset( $http_response_header ) )
				global $http_response_header;
			$theHeaders = $http_response_header;
		}

		fclose($handle);

		$processedHeaders = WP_Http::processHeaders($theHeaders);

		if ( ! empty( $strResponse ) && isset( $processedHeaders['headers']['transfer-encoding'] ) && 'chunked' == $processedHeaders['headers']['transfer-encoding'] )
			$strResponse = WP_Http::chunkTransferDecode($strResponse);

		return array('headers' => $processedHeaders['headers'], 'body' => $strResponse, 'response' => $processedHeaders['response']);
	}

	/**
	 * Whether this class can be used for retrieving an URL.
	 *
	 * @static
	 * @return boolean False means this class can not be used, true means it can.
	 */
	function test() {
		if ( ! function_exists('fopen') || (function_exists('ini_get') && true != ini_get('allow_url_fopen')) )
			return false;

		return true;
	}
}

/**
 * HTTP request method uses Streams to retrieve the url.
 *
 * Requires PHP 5.0+ and uses fopen with stream context. Requires that
 * 'allow_url_fopen' PHP setting to be enabled.
 *
 * Second preferred method for getting the URL, for PHP 5.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7
 */
class WP_Http_Streams {
	/**
	 * Send a HTTP request to a URI using streams with fopen().
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param string $url
	 * @param str|array $args Optional. Override the defaults.
	 * @return array 'headers', 'body', and 'response' keys.
	 */
	function request($url, $args = array()) {
		$defaults = array(
			'method' => 'GET', 'timeout' => 5,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(), 'body' => null
		);

		$r = wp_parse_args( $args, $defaults );

		if ( isset($r['headers']['User-Agent']) ) {
			$r['user-agent'] = $r['headers']['User-Agent'];
			unset($r['headers']['User-Agent']);
		} else if( isset($r['headers']['user-agent']) ) {
			$r['user-agent'] = $r['headers']['user-agent'];
			unset($r['headers']['user-agent']);
		}

		$arrURL = parse_url($url);

		if ( false === $arrURL )
			return new WP_Error('http_request_failed', sprintf(__('Malformed URL: %s'), $url));

		if ( 'http' != $arrURL['scheme'] || 'https' != $arrURL['scheme'] )
			$url = str_replace($arrURL['scheme'], 'http', $url);

		// Convert Header array to string.
		$strHeaders = '';
		if ( is_array( $r['headers'] ) )
			foreach( $r['headers'] as $name => $value )
				$strHeaders .= "{$name}: $value\r\n";
		else if ( is_string( $r['headers'] ) )
			$strHeaders = $r['headers'];

		$arrContext = array('http' =>
			array(
				'method' => strtoupper($r['method']),
				'user_agent' => $r['user-agent'],
				'max_redirects' => $r['redirection'],
				'protocol_version' => (float) $r['httpversion'],
				'header' => $strHeaders,
				'timeout' => $r['timeout']
			)
		);

		if ( ! is_null($r['body']) && ! empty($r['body'] ) )
			$arrContext['http']['content'] = $r['body'];

		$context = stream_context_create($arrContext);

		if ( !defined('WP_DEBUG') || ( defined('WP_DEBUG') && false === WP_DEBUG ) )
			$handle = @fopen($url, 'r', false, $context);
		else
			$handle = fopen($url, 'r', false, $context);

		if ( ! $handle)
			return new WP_Error('http_request_failed', sprintf(__('Could not open handle for fopen() to %s'), $url));

		// WordPress supports PHP 4.3, which has this function. Removed sanity
		// checking for performance reasons.
		stream_set_timeout($handle, $r['timeout'] );

		if ( ! $r['blocking'] ) {
			stream_set_blocking($handle, 0);
			fclose($handle);
			return array( 'headers' => array(), 'body' => '', 'response' => array('code', 'message') );
		}

		$strResponse = stream_get_contents($handle);
		$meta = stream_get_meta_data($handle);

		$processedHeaders = array();
		if( isset( $meta['wrapper_data']['headers'] ) )
			$processedHeaders = WP_Http::processHeaders($meta['wrapper_data']['headers']);
		else
			$processedHeaders = WP_Http::processHeaders($meta['wrapper_data']);

		if ( ! empty( $strResponse ) && isset( $processedHeaders['headers']['transfer-encoding'] ) && 'chunked' == $processedHeaders['headers']['transfer-encoding'] )
			$strResponse = WP_Http::chunkTransferDecode($strResponse);

		fclose($handle);

		return array('headers' => $processedHeaders['headers'], 'body' => $strResponse, 'response' => $processedHeaders['response']);
	}

	/**
	 * Whether this class can be used for retrieving an URL.
	 *
	 * @static
	 * @access public
	 * @since 2.7
	 *
	 * @return boolean False means this class can not be used, true means it can.
	 */
	function test() {
		if ( ! function_exists('fopen') || (function_exists('ini_get') && true != ini_get('allow_url_fopen')) )
			return false;

		if ( version_compare(PHP_VERSION, '5.0', '<') )
			return false;

		return true;
	}
}

/**
 * HTTP request method uses HTTP extension to retrieve the url.
 *
 * Requires the HTTP extension to be installed. This would be the preferred
 * transport since it can handle a lot of the problems that forces the others to
 * use the HTTP version 1.0. Even if PHP 5.2+ is being used, it doesn't mean
 * that the HTTP extension will be enabled.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7
 */
class WP_Http_ExtHTTP {
	/**
	 * Send a HTTP request to a URI using HTTP extension.
	 *
	 * Does not support non-blocking.
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param string $url
	 * @param str|array $args Optional. Override the defaults.
	 * @return array 'headers', 'body', and 'response' keys.
	 */
	function request($url, $args = array()) {
		$defaults = array(
			'method' => 'GET', 'timeout' => 5,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(), 'body' => null
		);

		$r = wp_parse_args( $args, $defaults );

		if ( isset($r['headers']['User-Agent']) ) {
			$r['user-agent'] = $r['headers']['User-Agent'];
			unset($r['headers']['User-Agent']);
		} else if( isset($r['headers']['user-agent']) ) {
			$r['user-agent'] = $r['headers']['user-agent'];
			unset($r['headers']['user-agent']);
		}

		switch ( $r['method'] ) {
			case 'POST':
				$r['method'] = HTTP_METH_POST;
				break;
			case 'HEAD':
				$r['method'] = HTTP_METH_HEAD;
				break;
			case 'GET':
			default:
				$r['method'] = HTTP_METH_GET;
		}

		$arrURL = parse_url($url);

		if ( 'http' != $arrURL['scheme'] || 'https' != $arrURL['scheme'] )
			$url = str_replace($arrURL['scheme'], 'http', $url);

		$options = array(
			'timeout' => $r['timeout'],
			'connecttimeout' => $r['timeout'],
			'redirect' => $r['redirection'],
			'useragent' => $r['user-agent'],
			'headers' => $r['headers'],
		);

		if ( !defined('WP_DEBUG') || ( defined('WP_DEBUG') && false === WP_DEBUG ) ) //Emits warning level notices for max redirects and timeouts
			$strResponse = @http_request($r['method'], $url, $r['body'], $options, $info);
		else
			$strResponse = http_request($r['method'], $url, $r['body'], $options, $info); //Emits warning level notices for max redirects and timeouts

		if ( false === $strResponse || ! empty($info['error']) ) //Error may still be set, Response may return headers or partial document, and error contains a reason the request was aborted, eg, timeout expired or max-redirects reached
			return new WP_Error('http_request_failed', $info['response_code'] . ': ' . $info['error']);

		if ( ! $r['blocking'] )
			return array( 'headers' => array(), 'body' => '', 'response' => array('code', 'message') );

		list($theHeaders, $theBody) = explode("\r\n\r\n", $strResponse, 2);
		$theHeaders = WP_Http::processHeaders($theHeaders);

		if ( ! empty( $theBody ) && isset( $theHeaders['headers']['transfer-encoding'] ) && 'chunked' == $theHeaders['headers']['transfer-encoding'] ) {
			if ( !defined('WP_DEBUG') || ( defined('WP_DEBUG') && false === WP_DEBUG ) )
				$theBody = @http_chunked_decode($theBody);
			else
				$theBody = http_chunked_decode($theBody);
		}

		$theResponse = array();
		$theResponse['code'] = $info['response_code'];
		$theResponse['message'] = get_status_header_desc($info['response_code']);

		return array('headers' => $theHeaders['headers'], 'body' => $theBody, 'response' => $theResponse);
	}

	/**
	 * Whether this class can be used for retrieving an URL.
	 *
	 * @static
	 * @since 2.7
	 *
	 * @return boolean False means this class can not be used, true means it can.
	 */
	function test() {
		if ( function_exists('http_request') )
			return true;

		return false;
	}
}

/**
 * HTTP request method uses Curl extension to retrieve the url.
 *
 * Requires the Curl extension to be installed.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7
 */
class WP_Http_Curl {
	/**
	 * Send a HTTP request to a URI using cURL extension.
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param string $url
	 * @param str|array $args Optional. Override the defaults.
	 * @return array 'headers', 'body', and 'response' keys.
	 */
	function request($url, $args = array()) {
		$defaults = array(
			'method' => 'GET', 'timeout' => 5,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(), 'body' => null
		);

		$r = wp_parse_args( $args, $defaults );

		if ( isset($r['headers']['User-Agent']) ) {
			$r['user-agent'] = $r['headers']['User-Agent'];
			unset($r['headers']['User-Agent']);
		} else if( isset($r['headers']['user-agent']) ) {
			$r['user-agent'] = $r['headers']['user-agent'];
			unset($r['headers']['user-agent']);
		}

		// If timeout is a float less than 1, round it up to 1.
		if ( $r['timeout'] > 0 && $r['timeout'] < 1 )
			$r['timeout'] = 1;

		$handle = curl_init();
		curl_setopt( $handle, CURLOPT_URL, $url);

		if ( 'HEAD' === $r['method'] ) {
			curl_setopt( $handle, CURLOPT_NOBODY, true );
		}

		if ( true === $r['blocking'] ) {
			curl_setopt( $handle, CURLOPT_HEADER, true );
			curl_setopt( $handle, CURLOPT_RETURNTRANSFER, 1 );
		} else {
			curl_setopt( $handle, CURLOPT_HEADER, false );
			curl_setopt( $handle, CURLOPT_NOBODY, true );
			curl_setopt( $handle, CURLOPT_RETURNTRANSFER, 0 );
		}

		curl_setopt( $handle, CURLOPT_USERAGENT, $r['user-agent'] );
		curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 1 );
		curl_setopt( $handle, CURLOPT_TIMEOUT, $r['timeout'] );
		curl_setopt( $handle, CURLOPT_MAXREDIRS, $r['redirection'] );

		if ( !ini_get('safe_mode') && !ini_get('open_basedir') )
			curl_setopt( $handle, CURLOPT_FOLLOWLOCATION, true );

		if( ! is_null($r['headers']) )
			curl_setopt( $handle, CURLOPT_HTTPHEADER, $r['headers'] );

		if ( $r['httpversion'] == '1.0' )
			curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
		else
			curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );

		if ( ! $r['blocking'] ) {
			curl_exec( $handle );
			curl_close( $handle );
			return array( 'headers' => array(), 'body' => '', 'response' => array('code', 'message') );
		}

		$theResponse = curl_exec( $handle );

		if ( !empty($theResponse) ) {
			$headerLength = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
			$theHeaders = trim( substr($theResponse, 0, $headerLength) );
			$theBody = substr( $theResponse, $headerLength );
			if ( false !== strrpos($theHeaders, "\r\n\r\n") ) {
				$headerParts = explode("\r\n\r\n", $theHeaders);
				$theHeaders = $headerParts[ count($headerParts) -1 ];
			}
			$theHeaders = WP_Http::processHeaders($theHeaders);
		} else {
			if ( $curl_error = curl_error($handle) )
				return new WP_Error('http_request_failed', $curl_error);
			if ( in_array( curl_getinfo( $handle, CURLINFO_HTTP_CODE ), array(301, 302) ) )
				return new WP_Error('http_request_failed', __('Too many redirects.'));

			$theHeaders = array( 'headers' => array() );
			$theBody = '';
		}
		$response = array();
		$response['code'] = curl_getinfo( $handle, CURLINFO_HTTP_CODE );
		$response['message'] = get_status_header_desc($response['code']);

		curl_close( $handle );

		return array('headers' => $theHeaders['headers'], 'body' => $theBody, 'response' => $response);
	}

	/**
	 * Whether this class can be used for retrieving an URL.
	 *
	 * @static
	 * @since 2.7
	 *
	 * @return boolean False means this class can not be used, true means it can.
	 */
	function test() {
		if ( function_exists('curl_init') )
			return true;

		return false;
	}
}

/**
 * Returns the initialized WP_Http Object
 *
 * @since 2.7
 * @access private
 *
 * @return WP_Http HTTP Transport object.
 */
function &_wp_http_get_object() {
	static $http;

	if ( is_null($http) )
		$http = new WP_Http();

	return $http;
}

/**
 * Retrieve the raw response from the HTTP request.
 *
 * The array structure is a little complex.
 *
 * <code>
 * $res = array( 'headers' => array(), 'response' => array('code', 'message') );
 * </code>
 *
 * All of the headers in $res['headers'] are with the name as the key and the
 * value as the value. So to get the User-Agent, you would do the following.
 *
 * <code>
 * $user_agent = $res['headers']['user-agent'];
 * </code>
 *
 * The body is the raw response content and can be retrieved from $res['body'].
 *
 * This function is called first to make the request and there are other API
 * functions to abstract out the above convoluted setup.
 *
 * @since 2.7.0
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @return string The body of the response
 */
function wp_remote_request($url, $args = array()) {
	$objFetchSite = _wp_http_get_object();
	return $objFetchSite->request($url, $args);
}

/**
 * Retrieve the raw response from the HTTP request using the GET method.
 *
 * @see wp_remote_request() For more information on the response array format.
 *
 * @since 2.7
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @return string The body of the response
 */
function wp_remote_get($url, $args = array()) {
	$objFetchSite = _wp_http_get_object();

	return $objFetchSite->get($url, $args);
}

/**
 * Retrieve the raw response from the HTTP request using the POST method.
 *
 * @see wp_remote_request() For more information on the response array format.
 *
 * @since 2.7
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @return string The body of the response
 */
function wp_remote_post($url, $args = array()) {
	$objFetchSite = _wp_http_get_object();
	return $objFetchSite->post($url, $args);
}

/**
 * Retrieve the raw response from the HTTP request using the HEAD method.
 *
 * @see wp_remote_request() For more information on the response array format.
 *
 * @since 2.7
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @return string The body of the response
 */
function wp_remote_head($url, $args = array()) {
	$objFetchSite = _wp_http_get_object();
	return $objFetchSite->head($url, $args);
}

/**
 * Retrieve only the headers from the raw response.
 *
 * @since 2.7
 *
 * @param array $response HTTP response.
 * @return array The headers of the response. Empty array if incorrect parameter given.
 */
function wp_remote_retrieve_headers(&$response) {
	if ( ! isset($response['headers']) || ! is_array($response['headers']))
		return array();

	return $response['headers'];
}

/**
 * Retrieve a single header by name from the raw response.
 *
 * @since 2.7
 *
 * @param array $response
 * @param string $header Header name to retrieve value from.
 * @return array The header value. Empty string on if incorrect parameter given.
 */
function wp_remote_retrieve_header(&$response, $header) {
	if ( ! isset($response['headers']) || ! is_array($response['headers']))
		return '';

	if ( array_key_exists($header, $response['headers']) )
		return $response['headers'][$header];

	return '';
}

/**
 * Retrieve only the response code from the raw response.
 *
 * Will return an empty array if incorrect parameter value is given.
 *
 * @since 2.7
 *
 * @param array $response HTTP response.
 * @return array The keys 'code' and 'message' give information on the response.
 */
function wp_remote_retrieve_response_code(&$response) {
	if ( ! isset($response['response']) || ! is_array($response['response']))
		return '';

	return $response['response']['code'];
}

/**
 * Retrieve only the response message from the raw response.
 *
 * Will return an empty array if incorrect parameter value is given.
 *
 * @since 2.7
 *
 * @param array $response HTTP response.
 * @return array The keys 'code' and 'message' give information on the response.
 */
function wp_remote_retrieve_response_message(&$response) {
	if ( ! isset($response['response']) || ! is_array($response['response']))
		return '';

	return $response['response']['message'];
}

/**
 * Retrieve only the body from the raw response.
 *
 * @since 2.7
 *
 * @param array $response HTTP response.
 * @return string The body of the response. Empty string if no body or incorrect parameter given.
 */
function wp_remote_retrieve_body(&$response) {
	if ( ! isset($response['body']) )
		return '';

	return $response['body'];
}

?>
