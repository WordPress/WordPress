<?php
/**
 * Simple HTTP request fallback system
 *
 * @package WordPress
 * @subpackage HTTP
 * @since {@internal Version Unknown}}
 * @author Jacob Santos <wordpress@santosj.name>
 */

/**
 * Abstract class for all of the fallback implementation
 * classes. The implementation classes will extend this class
 * to keep common API methods universal between different
 * functionality.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since {@internal Version Unknown}}
 */
class WP_Http
{

	/**
	 * PHP4 style Constructor - Calls PHP5 Style Constructor
	 *
	 * @since {@internal Version Unknown}}
	 * @return WP_Http
	 */
	function WP_Http()
	{
		$this->__construct();
	}

	/**
	 * PHP5 style Constructor - Setup available transport if not available.
	 *
	 * @since {@internal Version Unknown}}
	 * @return WP_Http
	 */
	function __construct()
	{
		WP_Http::_getTransport();
	}

	/**
	 * Tests the WordPress HTTP objects for an object to use and returns it.
	 *
	 * Tests all of the objects and returns the object that passes. Also caches
	 * that object to be used later.
	 *
	 * @since {@internal Version Unknown}}
	 * @access private
	 *
	 * @return object|null Null if no transports are available, HTTP transport object.
	 */
	function &_getTransport()
	{
		static $working_transport;

		if( is_null($working_transport) ) {
			if( true === WP_Http_Streams::test() )
				$working_transport = new WP_Http_Streams();
			else if( true ===  WP_Http_ExtHttp::test() )
				$working_transport = new WP_Http_ExtHttp();
			else if( true === WP_Http_Fopen::test() )
				$working_transport = new WP_Http_Fopen();
			else if( true === WP_Http_Fsockopen::test() )
				$working_transport = new WP_Http_Fsockopen();
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
	 * is addressed here.
	 *
	 * @since {@internal Version Unknown}}
	 * @access private
	 *
	 * @return object|null Null if no transports are available, HTTP transport object.
	 */
	function &_postTransport()
	{
		static $working_transport;

		if( is_null($working_transport) ) {
			if( true === WP_Http_Streams::test() )
				$working_transport = new WP_Http_Streams();
			else if( true ===  WP_Http_ExtHttp::test() )
				$working_transport = new WP_Http_ExtHttp();
			else if( true === WP_Http_Fsockopen::test() )
				$working_transport = new WP_Http_Fsockopen();
		}

		return $working_transport;
	}

	/**
	 * Retrieve the location and set the class properties after the site has been retrieved.
	 *
	 * @access public
	 * @since {@internal Version Unknown}}
	 *
	 * @param string $url
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs. Expects sanitized.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @param str|array $type Optional. Should be an already processed array with HTTP arguments.
	 * @return boolean
	 */
	function request($url, $args=array(), $headers=null, $body=null)
	{
		global $wp_version;

		$defaults = array(
			'method' => 'GET', 'timeout' => 3,
			'redirection' => 5, 'redirected' => false,
			'httpversion' => '1.0'
		);

		$r = wp_parse_args( $args, $defaults );

		if( !is_null($headers) && !is_array($headers) ) {
			$processedHeaders = WP_Http::processHeaders($headers);
			$headers = $processedHeaders['headers'];
		} else {
			$headers = array();
		}

		if( !isset($headers['user-agent']) || !isset($headers['User-Agent']) )
			$headers['user-agent'] = apply_filters('http_headers_useragent', 'WordPress/'.$wp_version );

		if( is_null($body) )
			$transport = WP_Http::_getTransport();
		else
			$transport = WP_Http::_postTransport();

		return $transport->request($url, $headers, $body, $r);
	}

	/**
	 * Uses the POST HTTP method.
	 * 
	 * Used for sending data that is expected to be in the body.
	 *
	 * @access public
	 * @since {@internal Version Unknown}}
	 *
	 * @param string $url The location of the site and page to retrieve.
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @return boolean
	 */
	function post($url, $args=array(), $headers=null, $body=null)
	{
		$defaults = array('method' => 'POST');
		$r = wp_parse_args( $args, $defaults );
		return $this->request($url, $headers, $body, $r);
	}

	/**
	 * Uses the GET HTTP method. 
	 *
	 * Used for sending data that is expected to be in the body.
	 *
	 * @access public
	 * @since {@internal Version Unknown}}
	 *
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @return boolean
	 */
	function get($url, $args=array(), $headers=null, $body=null)
	{
		$defaults = array('method' => 'GET');
		$r = wp_parse_args( $args, $defaults );
		return $this->request($url, $headers, $body, $r);
	}

	/**
	 * Uses the HEAD HTTP method. 
	 *
	 * Used for sending data that is expected to be in the body.
	 *
	 * @access public
	 * @since {@internal Version Unknown}}
	 *
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @return boolean
	 */
	function head($url, $args=array(), $headers=null, $body=null)
	{
		$defaults = array('method' => 'HEAD');
		$r = wp_parse_args( $args, $defaults );
		return $this->request($url, $r, $headers, $body);
	}

	/**
	 * Parses the responses and splits the parts into headers and body.
	 *
	 * @access public
	 * @static
	 * @since {@internal Version Unknown}}
	 *
	 * @param string $strResponse The full response string
	 * @return array Array with 'headers' and 'body' keys.
	 */
	function processResponse($strResponse)
	{
		list($theHeaders, $theBody) = explode("\r\n\r\n", $strResponse, 2);
		return array('headers' => $theHeaders, 'body' => $theBody);
	}

	/**
	 * Whether response code is in the 400 range.
	 *
	 * @access public
	 * @static
	 * @since {@internal Version Unknown}}
	 *
	 * @param array $response Array with code and message keys
	 * @return bool True if 40x Response, false if something else.
	 */
	function is400Response($response)
	{
		if( (int) substr($response, 0, 1) == 4 )
			return true;
		return false;
	}

	/**
	 * Whether the headers returned a redirect location.
	 *
	 * @access public
	 * @static
	 * @since {@internal Version Unknown}}
	 *
	 * @param array $headers Array with headers
	 * @return bool True if Location header is found.
	 */
	function isRedirect($headers)
	{
		if( isset($headers['location']) )
			return true;
		return false;
	}

	/**
	 * Transform header string into an array.
	 *
	 * Will overwrite the last header value, if it is not empty.
	 *
	 * @access public
	 * @static
	 * @since {@internal Version Unknown}}
	 *
	 * @param string|array $headers
	 * @return array
	 */
	function processHeaders($headers)
	{
		if( is_array($headers) )
			return $headers;

		$headers = explode("\n", str_replace(array("\r"), '', $headers) );

		$response = array('code' => 0, 'message' => '');

		$newheaders = array();
		foreach($headers as $tempheader) {
			if( empty($tempheader) )
				continue;

			if( false === strpos($tempheader, ':') ) {
				list( , $iResponseCode, $strResponseMsg) = explode(" ", $tempheader, 3);
				$response['code'] = $iResponseCode;
				$response['message'] = $strResponseMsg;
				continue;
			}

			list($key, $value) = explode(":", $tempheader, 2);

			if( !empty($value) )
				$newheaders[strtolower($key)] = trim($value);
		}

		return array('response' => $response, 'headers' => $newheaders);
	}
}

/**
 * HTTP request method uses fsockopen function to retrieve the url.
 *
 * Preferred method since it works with all WordPress supported PHP versions.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since {@internal Version Unknown}}
 */
class WP_Http_Fsockopen
{
	/**
	 * Retrieve the location and set the class properties after the site has been retrieved.
	 *
	 * @since {@internal Version Unknown}}
	 * @access public
	 * @param string $url
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs. Expects sanitized.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @param str|array $type Optional. Should be an already processed array with HTTP arguments.
	 * @return boolean
	 */
	function request($url, $args=array(), $headers=null, $body=null)
	{
		$defaults = array(
			'method' => 'GET', 'timeout' => 3,
			'redirection' => 5, 'httpversion' => '1.0'
		);

		$r = wp_parse_args( $args, $defaults );

		$iError = null; // Store error number
		$strError = null; // Store error string

		$arrURL = parse_url($url);

		$secure_transport = false;

		if( !isset($arrURL['port']) ) {
			if( (($arrURL['scheme'] == 'ssl' || $arrURL['scheme'] == 'https')) && extension_loaded('openssl') ) {
				$arrURL['host'] = 'ssl://'.$arrURL['host'];
				$arrURL['port'] = apply_filters('http_request_default_port', 443);
				$secure_transport = true;
			}
			else
				$arrURL['port'] = apply_filters('http_request_default_port', 80);
		}
		else
			$arrURL['port'] = apply_filters('http_request_port', $arrURL['port']);

		if( true === $secure_transport )
			$error_reporting = error_reporting(0);

		$handle = fsockopen($arrURL['host'], $arrURL['port'], $iError, $strError, apply_filters('http_request_timeout', absint($r['timeout']) ) );

		if( false === $handle ) {
			return new WP_Error('http_request_failed', $iError.': '.$strError);
		}

		$requestPath = $arrURL['path'] . ( isset($arrURL['query']) ? '?' . $arrURL['query'] : '' );
		$requestPath = (empty($requestPath)) ? '/' : $requestPath;

		$strHeaders = '';
		$strHeaders .= strtoupper($r['method']).' '.$requestPath.' HTTP/'.$r['httpversion']."\r\n";
		$strHeaders .= 'Host: '.$arrURL['host']."\r\n";

		if( is_array($header) ) {
			foreach( (array) $this->getHeaders() as $header => $headerValue)
				$strHeaders .= $header.': '.$headerValue."\r\n";
		} else
			$strHeaders .= $header;

		$strHeaders .= "\r\n";

		if( !is_null($body) )
			$strHeaders .= $body;

		fwrite($handle, $strHeaders);

		$strResponse = '';
		while( !feof($handle) ) {
			$strResponse .= fread($handle, 4096);
		}
		fclose($handle);

		if( true === $secure_transport )
			error_reporting($error_reporting);

		$process = WP_Http::processResponse($strResponse);
		$arrHeaders = WP_Http::processHeaders($process['headers']);

		if( WP_Http::is400Response($arrHeaders['response']) )
			return new WP_Error('http_request_failed', $arrHeaders['response']['code'] .': '. $arrHeaders['response']['message']);

		if( isset($arrHeaders['headers']['location']) ) {
			if( $r['redirection']-- > 0 ) {
				return $this->request($arrHeaders['headers']['location'], $r, $headers, $body);
			} else
				return new WP_Error('http_request_failed', __('Too many redirects.'));
		}

		return array('headers' => $arrHeaders['headers'], 'body' => $process['body'], 'response' => $arrHeaders['response']);
	}

	/**
	 * Whether this class can be used for retrieving an URL.
	 *
	 * @since {@internal Version Unknown}}
	 * @static
	 * @return boolean False means this class can not be used, true means it can.
	 */
	function test()
	{
		if( function_exists( 'fsockopen' ) )
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
 * Second preferred method for handling the retrieving the url for PHP 4.3+.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since {@internal Version Unknown}}
 */
class WP_Http_Fopen
{
	/**
	 * Retrieve the location and set the class properties after the site has been retrieved.
	 *
	 * @access public
	 * @since {@internal Version Unknown}}
	 *
	 * @param string $url
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs. Expects sanitized.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @param str|array $type Optional. Should be an already processed array with HTTP arguments.
	 * @return boolean
	 */
	function request($url, $args=array(), $headers=null, $body=null)
	{
		global $http_response_header;

		$defaults = array(
			'method' => 'GET', 'timeout' => 3,
			'redirection' => 5, 'httpversion' => '1.0'
		);

		$r = wp_parse_args( $args, $defaults );

		$arrURL = parse_url($url);

		if( 'http' != $arrURL['scheme'] || 'https' != $arrURL['scheme'] )
			$url = str_replace($arrURL['scheme'], 'http', $url);

		$handle = fopen($url, 'rb');

		if(!$handle)
			return new WP_Error('http_request_failed', sprintf(__("Could not open handle for fopen() to %s"), $url));

		if( function_exists('stream_set_timeout') )
			stream_set_timeout($handle, apply_filters('http_request_timeout', $r['timeout']) );

		$strResponse = '';
		while(!feof($handle)) {
			$strResponse .= fread($handle, 4096);
		}

		$theHeaders = '';
		if( function_exists('stream_get_meta_data') ) {
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
	function test()
	{
		if( !function_exists('fopen') || (function_exists('ini_get') && true != ini_get('allow_url_fopen')) )
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
 * @since {@internal Version Unknown}}
 */
class WP_Http_Streams
{
	/**
	 * Retrieve the location and set the class properties after the site has been retrieved.
	 *
	 * @access public
	 * @since {@internal Version Unknown}}
	 *
	 * @param string $url
	 * @param str|array $args Optional. Override the defaults.
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs. Expects sanitized.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @return boolean
	 */
	function request($url, $args=array(), $headers=null, $body=null)
	{
		$defaults = array(
			'method' => 'GET', 'timeout' => 3,
			'redirection' => 5, 'httpversion' => '1.0'
		);

		$r = wp_parse_args( $args, $defaults );

		$arrURL = parse_url($url);

		if( 'http' != $arrURL['scheme'] || 'https' != $arrURL['scheme'] )
			$url = str_replace($arrURL['scheme'], 'http', $url);

		$arrContext = array('http' => 
			array(
				'method' => strtoupper($r['method']),
				'user-agent' => $headers['User-Agent'],
				'max_redirects' => $r['redirection'],
				'protocol_version' => (float) $r['httpversion'],
				'header' => $headers
			)
		);

		if( !is_null($body) )
			$arrContext['http']['content'] = $body;

		$context = stream_context_create($arrContext);

		$handle = fopen($url, 'rb', false, $context);

		stream_set_timeout($handle, apply_filters('http_request_stream_timeout', $this->timeout) );

		if(!$handle)
			return new WP_Error('http_request_failed', sprintf(__("Could not open handle for fopen() to %s"), $url));

		$strResponse = stream_get_contents($handle);
		$meta = stream_get_meta_data($handle);

		fclose($handle);

		$processedHeaders = WP_Http::processHeaders($meta['wrapper_data']);
		return array('headers' => $processedHeaders['headers'], 'body' => $strResponse, 'response' => $processedHeaders['response']);
	}

	/**
	 * Whether this class can be used for retrieving an URL.
	 *
	 * @static
	 * @access public
	 * @since {@internal Version Unknown}}
	 *
	 * @return boolean False means this class can not be used, true means it can.
	 */
	function test()
	{
		if( !function_exists('fopen') || (function_exists('ini_get') && true != ini_get('allow_url_fopen')) )
			return false;

		if( version_compare(PHP_VERSION, '5.0', '<') )
			return false;

		return true;
	}
}

/**
 * HTTP request method uses HTTP extension to retrieve the url.
 *
 * Requires the HTTP extension to be installed.
 *
 * Last ditch effort to retrieve the URL before complete failure.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since {@internal Version Unknown}}
 */
class WP_Http_ExtHTTP
{
	/**
	 * Retrieve the location and set the class properties after the site has been retrieved.
	 *
	 * @access public
	 * @since {@internal Version Unknown}}
	 *
	 * @param string $url
	 * @param str|array $args Optional. Override the defaults.
	 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs. Expects sanitized.
	 * @param string $body Optional. The body that should be sent. Expected to be already processed.
	 * @return boolean
	 */
	function request($url, $args=array(), $headers=null, $body=null)
	{
		global $wp_version;

		$defaults = array(
			'method' => 'GET', 'timeout' => 3,
			'redirection' => 5, 'httpversion' => '1.0',
			'user_agent' => apply_filters('http_headers_useragent', 'WordPress/'.$wp_version)
		);

		$r = wp_parse_args( $args, $defaults );

		if( isset($headers['User-Agent']) )
			unset($headers['User-Agent']);

		switch($r['method'])
		{
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

		if( 'http' != $arrURL['scheme'] || 'https' != $arrURL['scheme'] )
			$url = str_replace($arrURL['scheme'], 'http', $url);

		$options = array(
			'timeout' => $this->timeout,
			'connecttimeout' => apply_filters('http_request_stream_timeout', $this->timeout),
			'redirect' => apply_filters('http_request_redirect', 3),
			'useragent' => $r['user_agent'],
			'headers' => $headers,
		);

		$strResponse = http_request($r['method'], $url, $body, $options, $info);

		if( false === $strResponse )
			return new WP_Error('http_request_failed', $info['response_code'] .': '. $info['error']);

		list($theHeaders, $theBody) = explode("\r\n\r\n", $strResponse, 2);
		$theHeaders = WP_Http::processHeaders($theHeaders);

		$theResponse = array();
		$theResponse['response']['code'] = $info['response_code'];
		$theResponse['response']['message'] = get_status_header_desc($info['response_code']);

		return array('headers' => $theHeaders['headers'], 'body' => $theBody, 'response' => $theResponse);
	}

	/**
	 * Whether this class can be used for retrieving an URL.
	 *
	 * @static
	 * @since {@internal Version Unknown}}
	 *
	 * @return boolean False means this class can not be used, true means it can.
	 */
	function test()
	{
		if( function_exists('http_request') )
			return true;

		return false;
	}
}

/**
 * Returns the initialized WP_Http Object
 *
 * @since {@internal Version Unknown}}
 * @access private
 *
 * @return WP_Http HTTP Transport object.
 */
function &_wp_http_get_object() {
	static $http;

	if( is_null($http) )
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
 * @since {@internal Version Unknown}}
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
 * @param string $body Optional. The body that should be sent. Expected to be already processed.
 * @return string The body of the response
 */
function wp_remote_request($url, $args=array(), $headers=null, $body=null) {
	$objFetchSite = _wp_http_get_object();

	return $objFetchSite->request($url, $headers, $body, $args);
}

/**
 * Retrieve the raw response from the HTTP request using the GET method.
 *
 * @see wp_remote_request() For more information on the response array format.
 *
 * @since {@internal Version Unknown}}
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
 * @param string $body Optional. The body that should be sent. Expected to be already processed.
 * @return string The body of the response
 */
function wp_remote_get($url, $args=array(), $headers=null, $body=null) {
	$objFetchSite = _wp_http_get_object();

	return $objFetchSite->get($url, $headers, $body, $args);
}

/**
 * Retrieve the raw response from the HTTP request using the POST method.
 *
 * @see wp_remote_request() For more information on the response array format.
 *
 * @since {@internal Version Unknown}}
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
 * @param string $body Optional. The body that should be sent. Expected to be already processed.
 * @return string The body of the response
 */
function wp_remote_post($url, $args=array(), $headers=null, $body=null) {
	$objFetchSite = _wp_http_get_object();

	return $objFetchSite->post($url, $headers, $body, $args);
}

/**
 * Retrieve the raw response from the HTTP request using the HEAD method.
 *
 * @see wp_remote_request() For more information on the response array format.
 *
 * @since {@internal Version Unknown}}
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @param string|array $headers Optional. Either the header string or array of Header name and value pairs.
 * @param string $body Optional. The body that should be sent. Expected to be already processed.
 * @return string The body of the response
 */
function wp_remote_head($url, $args=array(), $headers=null, $body=null) {
	$objFetchSite = _wp_http_get_object();

	return $objFetchSite->head($url, $headers, $body, $args);
}

/**
 * Retrieve only the headers from the raw response.
 *
 * @since {@internal Version Unknown}}
 *
 * @param array $response HTTP response.
 * @return array The headers of the response. Empty array if incorrect parameter given.
 */
function wp_remote_retrieve_headers(&$response) {
	if( !isset($response['headers']) || !is_array($response['headers']))
		return array();

	return $response['headers'];
}

/**
 * Retrieve a single header by name from the raw response.
 *
 * @since {@internal Version Unknown}}
 *
 * @param array $response
 * @param string $header Header name to retrieve value from.
 * @return array The header value. Empty string on if incorrect parameter given.
 */
function wp_remote_retrieve_header(&$response, $header) {
	if( !isset($response['headers']) || !is_array($response['headers']))
		return '';

	if( array_key_exists($header, $response['headers']) )
		return $response['headers'][$header];

	return '';
}

/**
 * Retrieve only the response code from the raw response.
 *
 * Will return an empty array if incorrect parameter value is given.
 *
 * @since {@internal Version Unknown}}
 *
 * @param array $response HTTP response.
 * @return array The keys 'code' and 'message' give information on the response.
 */
function wp_remote_retrieve_response_code(&$response) {
	if( !isset($response['response']) || !is_array($response['response']))
		return '';

	return $response['response']['code'];
}

/**
 * Retrieve only the response message from the raw response.
 *
 * Will return an empty array if incorrect parameter value is given.
 *
 * @since {@internal Version Unknown}}
 *
 * @param array $response HTTP response.
 * @return array The keys 'code' and 'message' give information on the response.
 */
function wp_remote_retrieve_response_message(&$response) {
	if( !isset($response['response']) || !is_array($response['response']))
		return '';

	return $response['response']['message'];
}

/**
 * Retrieve only the body from the raw response.
 *
 * @since {@internal Version Unknown}}
 *
 * @param array $response HTTP response.
 * @return string The body of the response. Empty string if no body or incorrect parameter given.
 */
function wp_remote_retrieve_body(&$response) {
	if( !isset($response['body']) )
		return '';

	return $response['body'];
}

?>