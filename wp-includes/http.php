<?php
/**
 * Simple and uniform HTTP request API.
 *
 * Will eventually replace and standardize the WordPress HTTP requests made.
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
	 * @return object|null Null if no transports are available, HTTP transport object.
	 */
	function &_getTransport() {
		static $working_transport;

		if ( is_null($working_transport) ) {
			if ( true === WP_Http_ExtHttp::test() && apply_filters('use_http_extension_transport', true) )
				$working_transport[] = new WP_Http_ExtHttp();
			else if ( true === WP_Http_Curl::test() && apply_filters('use_curl_transport', true) )
				$working_transport[] = new WP_Http_Curl();
			else if ( true === WP_Http_Streams::test() && apply_filters('use_streams_transport', true) )
				$working_transport[] = new WP_Http_Streams();
			else if ( true === WP_Http_Fopen::test() && apply_filters('use_fopen_transport', true) )
				$working_transport[] = new WP_Http_Fopen();
			else if ( true === WP_Http_Fsockopen::test() && apply_filters('use_fsockopen_transport', true) )
				$working_transport[] = new WP_Http_Fsockopen();
		}

		return $working_transport;
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
	 * @return object|null Null if no transports are available, HTTP transport object.
	 */
	function &_postTransport() {
		static $working_transport;

		if ( is_null($working_transport) ) {
			if ( true === WP_Http_ExtHttp::test() && apply_filters('use_http_extension_transport', true) )
				$working_transport[] = new WP_Http_ExtHttp();
			else if ( true === WP_Http_Streams::test() && apply_filters('use_streams_transport', true) )
				$working_transport[] = new WP_Http_Streams();
			else if ( true === WP_Http_Fsockopen::test() && apply_filters('use_fsockopen_transport', true) )
				$working_transport[] = new WP_Http_Fsockopen();
		}

		return $working_transport;
	}

	/**
	 * Send a HTTP request to a URI.
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
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs. Expects sanitized.
	 * @param string $body Optional. The body that should be sent. Will be automatically escaped and processed.
	 * @return boolean
	 */
	function request($url, $args = array(), $headers = null, $body = null) {
		global $wp_version;

		$defaults = array(
			'method' => 'GET', 'timeout' => apply_filters('http_request_timeout', 3),
			'redirection' => 5, 'httpversion' => '1.0',
			'user-agent' => apply_filters('http_headers_useragent', 'WordPress/' . $wp_version ),
			'blocking' => true
		);

		$r = wp_parse_args( $args, $defaults );

		if ( ! is_null($headers) && ! is_array($headers) ) {
			$processedHeaders = WP_Http::processHeaders($headers);
			$headers = $processedHeaders['headers'];
		} else {
			$headers = array();
		}

		if ( ! isset($headers['user-agent']) || ! isset($headers['User-Agent']) )
			$headers['user-agent'] = $r['user-agent'];

		if ( is_null($body) ) {
			if ( ! is_string($body) )
				$body = http_build_query($body);

			$transports = WP_Http::_getTransport();
		} else
			$transports = WP_Http::_postTransport();

		$response = array( 'headers' => array(), 'body' => '', 'response' => array('code', 'message') );
		foreach( (array) $transports as $transport ) {
			$response = $transport->request($url, $r, $headers, $body);

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
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @return boolean
	 */
	function post($url, $args = array(), $headers = null, $body = null) {
		$defaults = array('method' => 'POST');
		$r = wp_parse_args( $args, $defaults );
		return $this->request($url, $r, $headers, $body);
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
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @return boolean
	 */
	function get($url, $args = array(), $headers = null, $body = null) {
		$defaults = array('method' => 'GET');
		$r = wp_parse_args( $args, $defaults );
		return $this->request($url, $r, $headers, $body);
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
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @return boolean
	 */
	function head($url, $args = array(), $headers = null, $body = null) {
		$defaults = array('method' => 'HEAD');
		$r = wp_parse_args( $args, $defaults );
		return $this->request($url, $r, $headers, $body);
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
	 * Whether response code is in the 400 range.
	 *
	 * @access public
	 * @static
	 * @since 2.7
	 *
	 * @param array $response Array with code and message keys
	 * @return bool True if 40x Response, false if something else.
	 */
	function is400Response($response) {
		if ( (int) substr($response, 0, 1) == 4 )
			return true;
		return false;
	}

	/**
	 * Whether the headers returned a redirect location.
	 *
	 * Actually just checks whether the location header exists.
	 *
	 * @access public
	 * @static
	 * @since 2.7
	 *
	 * @param array $headers Array with headers
	 * @return bool True if Location header is found.
	 */
	function isRedirect($headers) {
		if ( isset($headers['location']) )
			return true;
		return false;
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
	 * @see WP_Http::retrieve For default options descriptions.
	 *
	 * @since 2.7
	 * @access public
	 * @param string $url URI resource.
	 * @param str|array $args Optional. Override the defaults.
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs. Expects sanitized.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @return array 'headers', 'body', and 'response' keys.
	 */
	function request($url, $args = array(), $headers = null, $body = null) {
		$defaults = array(
			'method' => 'GET', 'timeout' => 3,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true
		);

		$r = wp_parse_args( $args, $defaults );

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

		$handle = fsockopen($arrURL['host'], $arrURL['port'], $iError, $strError, $r['timeout'] );

		if ( false === $handle )
			return new WP_Error('http_request_failed', $iError . ': ' . $strError);

		$requestPath = $arrURL['path'] . ( isset($arrURL['query']) ? '?' . $arrURL['query'] : '' );
		$requestPath = empty($requestPath) ? '/' : $requestPath;

		$strHeaders = '';
		$strHeaders .= strtoupper($r['method']) . ' ' . $requestPath . ' HTTP/' . $r['httpversion'] . "\r\n";
		$strHeaders .= 'Host: ' . $arrURL['host'] . "\r\n";

		if ( is_array($header) ) {
			foreach ( (array) $this->getHeaders() as $header => $headerValue )
				$strHeaders .= $header . ': ' . $headerValue . "\r\n";
		} else {
			$strHeaders .= $header;
		}

		$strHeaders .= "\r\n";

		if ( ! is_null($body) )
			$strHeaders .= $body;

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

		if ( WP_Http::is400Response($arrHeaders['response']) )
			return new WP_Error('http_request_failed', $arrHeaders['response']['code'] . ': ' . $arrHeaders['response']['message']);

		if ( isset($arrHeaders['headers']['location']) ) {
			if ( $r['redirection']-- > 0 )
				return $this->request($arrHeaders['headers']['location'], $r, $headers, $body);
			else
				return new WP_Error('http_request_failed', __('Too many redirects.'));
		}

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
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs. Expects sanitized.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @return array 'headers', 'body', and 'response' keys.
	 */
	function request($url, $args = array(), $headers = null, $body = null) {
		global $http_response_header;

		$defaults = array(
			'method' => 'GET', 'timeout' => 3,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true
		);

		$r = wp_parse_args( $args, $defaults );

		$arrURL = parse_url($url);

		if ( 'http' != $arrURL['scheme'] || 'https' != $arrURL['scheme'] )
			$url = str_replace($arrURL['scheme'], 'http', $url);

		$handle = fopen($url, 'r');

		if (! $handle)
			return new WP_Error('http_request_failed', sprintf(__('Could not open handle for fopen() to %s'), $url));

		if ( function_exists('stream_set_timeout') )
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
		} else {
			$theHeaders = $http_response_header;
		}

		fclose($handle);

		$processedHeaders = WP_Http::processHeaders($theHeaders);

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
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs. Expects sanitized.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @return array 'headers', 'body', and 'response' keys.
	 */
	function request($url, $args = array(), $headers = null, $body = null) {
		$defaults = array(
			'method' => 'GET', 'timeout' => 3,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true
		);

		$r = wp_parse_args( $args, $defaults );

		if ( isset($headers['User-Agent']) ) {
			$r['user-agent'] = $headers['User-Agent'];
			unset($headers['User-Agent']);
		} else if( isset($headers['user-agent']) ) {
			$r['user-agent'] = $headers['user-agent'];
			unset($headers['user-agent']);
		} else {
			$r['user-agent'] = apply_filters('http_headers_useragent', 'WordPress/' . $wp_version );
		}

		$arrURL = parse_url($url);

		if ( 'http' != $arrURL['scheme'] || 'https' != $arrURL['scheme'] )
			$url = str_replace($arrURL['scheme'], 'http', $url);

		$arrContext = array('http' => 
			array(
				'method' => strtoupper($r['method']),
				'user-agent' => $r['user-agent'],
				'max_redirects' => $r['redirection'],
				'protocol_version' => (float) $r['httpversion'],
				'header' => $headers,
				'timeout' => $r['timeout']
			)
		);

		if ( ! is_null($body) )
			$arrContext['http']['content'] = $body;

		$context = stream_context_create($arrContext);

		$handle = fopen($url, 'r', false, $context);

		if ( ! $handle)
			return new WP_Error('http_request_failed', sprintf(__('Could not open handle for fopen() to %s'), $url));

		stream_set_timeout($handle, $r['timeout'] );

		if ( ! $r['blocking'] ) {
			fclose($handle);
			return array( 'headers' => array(), 'body' => '', 'response' => array('code', 'message') );
		}

		$strResponse = stream_get_contents($handle);
		$meta = stream_get_meta_data($handle);
		$processedHeaders = WP_Http::processHeaders($meta['wrapper_data']);

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
	 * @param array $headers Optional. Either the header string or array of Header name and value pairs. Expects sanitized.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @return array 'headers', 'body', and 'response' keys.
	 */
	function request($url, $args = array(), $headers = null, $body = null) {
		global $wp_version;

		$defaults = array(
			'method' => 'GET', 'timeout' => 3,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true
		);

		$r = wp_parse_args( $args, $defaults );

		if ( isset($headers['User-Agent']) ) {
			$r['user-agent'] = $headers['User-Agent'];
			unset($headers['User-Agent']);
		} else if( isset($headers['user-agent']) ) {
			$r['user-agent'] = $headers['user-agent'];
			unset($headers['user-agent']);
		} else {
			$r['user-agent'] = apply_filters('http_headers_useragent', 'WordPress/' . $wp_version );
		}

		switch ( $r['method'] ) {
			case 'GET':
				$r['method'] = HTTP_METH_GET;
				break;
			case 'POST':
				$r['method'] = HTTP_METH_POST;
				break;
			case 'HEAD':
				$r['method'] = HTTP_METH_HEAD;
				break;
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
			'headers' => $headers,
		);

		$strResponse = http_request($r['method'], $url, $body, $options, $info);

		if ( false === $strResponse )
			return new WP_Error('http_request_failed', $info['response_code'] . ': ' . $info['error']);

		if ( ! $r['blocking'] )
			return array( 'headers' => array(), 'body' => '', 'response' => array('code', 'message') );

		list($theHeaders, $theBody) = explode("\r\n\r\n", $strResponse, 2);
		$theHeaders = WP_Http::processHeaders($theHeaders);

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
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs. Expects sanitized.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @return array 'headers', 'body', and 'response' keys.
	 */
	function request($url, $args = array(), $headers = null, $body = null) {
		global $wp_version;

		$defaults = array(
			'method' => 'GET', 'timeout' => 3,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true
		);

		$r = wp_parse_args( $args, $defaults );

		if ( isset($headers['User-Agent']) ) {
			$r['user-agent'] = $headers['User-Agent'];
			unset($headers['User-Agent']);
		} else if( isset($headers['user-agent']) ) {
			$r['user-agent'] = $headers['user-agent'];
			unset($headers['user-agent']);
		} else {
			$r['user-agent'] = apply_filters('http_headers_useragent', 'WordPress/' . $wp_version );
		}

		$handle = curl_init();
		curl_setopt( $handle, CURLOPT_URL, $url);

		if ( true === $r['blocking'] ) {
			curl_setopt( $handle, CURLOPT_HEADER, true );
		} else {
			curl_setopt( $handle, CURLOPT_HEADER, false );
			curl_setopt( $handle, CURLOPT_NOBODY, true );
		}

		curl_setopt( $handle, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $handle, CURLOPT_USERAGENT, $r['user-agent'] );
		curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 1 );
		curl_setopt( $handle, CURLOPT_TIMEOUT, $r['timeout'] );
		curl_setopt( $handle, CURLOPT_MAXREDIRS, $r['redirection'] );

		if ( !ini_get('safe_mode') && !ini_get('open_basedir') )
			curl_setopt( $handle, CURLOPT_FOLLOWLOCATION, true );
		
		if( ! is_null($headers) )
			curl_setopt( $handle, CURLOPT_HTTPHEADER, $headers );

		if ( $r['httpversion'] == '1.0' )
			curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
		else
			curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );

		if ( ! $r['blocking'] ) {
			curl_close( $handle );
			return array( 'headers' => array(), 'body' => '', 'response' => array('code', 'message') );
		}

		$theResponse = curl_exec( $handle );

		list($theHeaders, $theBody) = explode("\r\n\r\n", $strResponse, 2);
		$theHeaders = WP_Http::processHeaders($theHeaders);

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
 * $res = array( 'headers' =>
 *		'response' => array('code', 'message'),
 *		'headers' => array()
 * );
 * </code>
 *
 * All of the headers in $res['headers']['headers'] are with the name as the key
 * and the value as the value. So to get the User-Agent, you would do the
 * following.
 *
 * <code>
 * $user_agent = $res['headers']['headers']['user-agent'];
 * </code>
 *
 * The body is the raw response content and can be retrieved from $res['body'].
 *
 * This function is called first to make the request and there are other API
 * functions to abstract out the above convoluted setup.
 *
 * @since 2.7
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
 * @param string $body Optional. The body that should be sent. Expected to be already processed.
 * @return string The body of the response
 */
function wp_remote_request($url, $args = array(), $headers = null, $body = null) {
	$objFetchSite = _wp_http_get_object();

	return $objFetchSite->request($url, $args, $headers, $body);
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
 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
 * @param string $body Optional. The body that should be sent. Expected to be already processed.
 * @return string The body of the response
 */
function wp_remote_get($url, $args = array(), $headers = null, $body = null) {
	$objFetchSite = _wp_http_get_object();

	return $objFetchSite->get($url, $args, $headers, $body);
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
 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
 * @param string $body Optional. The body that should be sent. Expected to be already processed.
 * @return string The body of the response
 */
function wp_remote_post($url, $args = array(), $headers = null, $body = null) {
	$objFetchSite = _wp_http_get_object();

	return $objFetchSite->post($url, $args, $headers, $body);
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
 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
 * @param string $body Optional. The body that should be sent. Expected to be already processed.
 * @return string The body of the response
 */
function wp_remote_head($url, $args = array(), $headers = null, $body = null) {
	$objFetchSite = _wp_http_get_object();

	return $objFetchSite->head($url, $args, $headers, $body);
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