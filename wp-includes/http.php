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
 * @since 2.7.0
 */

/**
 * Returns the initialized WP_Http Object
 *
 * @since 2.7.0
 * @access private
 *
 * @return WP_Http HTTP Transport object.
 */
function _wp_http_get_object() {
	static $http;

	if ( is_null($http) )
		$http = new WP_Http();

	return $http;
}

/**
 * Retrieve the raw response from a safe HTTP request.
 *
 * This function is ideal when the HTTP request is being made to an arbitrary
 * URL. The URL is validated to avoid redirection and request forgery attacks.
 *
 * @see wp_remote_request() For more information on the response array format
 * 	and default arguments.
 *
 * @since 3.6.0
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @return WP_Error|array The response or WP_Error on failure.
 */
function wp_safe_remote_request( $url, $args = array() ) {
	$args['reject_unsafe_urls'] = true;
	$http = _wp_http_get_object();
	return $http->request( $url, $args );
}

/**
 * Retrieve the raw response from a safe HTTP request using the GET method.
 *
 * This function is ideal when the HTTP request is being made to an arbitrary
 * URL. The URL is validated to avoid redirection and request forgery attacks.
 *
 * @see wp_remote_request() For more information on the response array format
 * 	and default arguments.
 *
 * @since 3.6.0
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @return WP_Error|array The response or WP_Error on failure.
 */
function wp_safe_remote_get( $url, $args = array() ) {
	$args['reject_unsafe_urls'] = true;
	$http = _wp_http_get_object();
	return $http->get( $url, $args );
}

/**
 * Retrieve the raw response from a safe HTTP request using the POST method.
 *
 * This function is ideal when the HTTP request is being made to an arbitrary
 * URL. The URL is validated to avoid redirection and request forgery attacks.
 *
 * @see wp_remote_request() For more information on the response array format
 * 	and default arguments.
 *
 * @since 3.6.0
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @return WP_Error|array The response or WP_Error on failure.
 */
function wp_safe_remote_post( $url, $args = array() ) {
	$args['reject_unsafe_urls'] = true;
	$http = _wp_http_get_object();
	return $http->post( $url, $args );
}

/**
 * Retrieve the raw response from a safe HTTP request using the HEAD method.
 *
 * This function is ideal when the HTTP request is being made to an arbitrary
 * URL. The URL is validated to avoid redirection and request forgery attacks.
 *
 * @see wp_remote_request() For more information on the response array format
 * 	and default arguments.
 *
 * @since 3.6.0
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @return WP_Error|array The response or WP_Error on failure.
 */
function wp_safe_remote_head( $url, $args = array() ) {
	$args['reject_unsafe_urls'] = true;
	$http = _wp_http_get_object();
	return $http->head( $url, $args );
}

/**
 * Retrieve the raw response from the HTTP request.
 *
 * The array structure is a little complex.
 *
 * <code>
 * $res = array( 'headers' => array(), 'response' => array('code' => int, 'message' => string) );
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
 * List of default arguments:
 * 'method'      => 'GET'
 *  - Default 'GET'  for wp_remote_get()
 *  - Default 'POST' for wp_remote_post()
 *  - Default 'HEAD' for wp_remote_head()
 * 'timeout'     => 5
 * 'redirection' => 5
 * 'httpversion' => '1.0'
 * 'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
 * 'blocking'    => true
 * 'headers'     => array()
 * 'cookies'     => array()
 * 'body'        => null
 * 'compress'    => false,
 * 'decompress'  => true,
 * 'sslverify'   => true,
 * 'stream'      => false,
 * 'filename'    => null
 *
 * @since 2.7.0
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @return WP_Error|array The response or WP_Error on failure.
 */
function wp_remote_request($url, $args = array()) {
	$objFetchSite = _wp_http_get_object();
	return $objFetchSite->request($url, $args);
}

/**
 * Retrieve the raw response from the HTTP request using the GET method.
 *
 * @see wp_remote_request() For more information on the response array format and default arguments.
 *
 * @since 2.7.0
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @return WP_Error|array The response or WP_Error on failure.
 */
function wp_remote_get($url, $args = array()) {
	$objFetchSite = _wp_http_get_object();
	return $objFetchSite->get($url, $args);
}

/**
 * Retrieve the raw response from the HTTP request using the POST method.
 *
 * @see wp_remote_request() For more information on the response array format and default arguments.
 *
 * @since 2.7.0
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @return WP_Error|array The response or WP_Error on failure.
 */
function wp_remote_post($url, $args = array()) {
	$objFetchSite = _wp_http_get_object();
	return $objFetchSite->post($url, $args);
}

/**
 * Retrieve the raw response from the HTTP request using the HEAD method.
 *
 * @see wp_remote_request() For more information on the response array format and default arguments.
 *
 * @since 2.7.0
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Override the defaults.
 * @return WP_Error|array The response or WP_Error on failure.
 */
function wp_remote_head($url, $args = array()) {
	$objFetchSite = _wp_http_get_object();
	return $objFetchSite->head($url, $args);
}

/**
 * Retrieve only the headers from the raw response.
 *
 * @since 2.7.0
 *
 * @param array $response HTTP response.
 * @return array The headers of the response. Empty array if incorrect parameter given.
 */
function wp_remote_retrieve_headers(&$response) {
	if ( is_wp_error($response) || ! isset($response['headers']) || ! is_array($response['headers']))
		return array();

	return $response['headers'];
}

/**
 * Retrieve a single header by name from the raw response.
 *
 * @since 2.7.0
 *
 * @param array $response
 * @param string $header Header name to retrieve value from.
 * @return string The header value. Empty string on if incorrect parameter given, or if the header doesn't exist.
 */
function wp_remote_retrieve_header(&$response, $header) {
	if ( is_wp_error($response) || ! isset($response['headers']) || ! is_array($response['headers']))
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
 * @since 2.7.0
 *
 * @param array $response HTTP response.
 * @return string the response code. Empty string on incorrect parameter given.
 */
function wp_remote_retrieve_response_code(&$response) {
	if ( is_wp_error($response) || ! isset($response['response']) || ! is_array($response['response']))
		return '';

	return $response['response']['code'];
}

/**
 * Retrieve only the response message from the raw response.
 *
 * Will return an empty array if incorrect parameter value is given.
 *
 * @since 2.7.0
 *
 * @param array $response HTTP response.
 * @return string The response message. Empty string on incorrect parameter given.
 */
function wp_remote_retrieve_response_message(&$response) {
	if ( is_wp_error($response) || ! isset($response['response']) || ! is_array($response['response']))
		return '';

	return $response['response']['message'];
}

/**
 * Retrieve only the body from the raw response.
 *
 * @since 2.7.0
 *
 * @param array $response HTTP response.
 * @return string The body of the response. Empty string if no body or incorrect parameter given.
 */
function wp_remote_retrieve_body(&$response) {
	if ( is_wp_error($response) || ! isset($response['body']) )
		return '';

	return $response['body'];
}

/**
 * Determines if there is an HTTP Transport that can process this request.
 *
 * @since 3.2.0
 *
 * @param array  $capabilities Array of capabilities to test or a wp_remote_request() $args array.
 * @param string $url Optional. If given, will check if the URL requires SSL and adds that requirement to the capabilities array.
 *
 * @return bool
 */
function wp_http_supports( $capabilities = array(), $url = null ) {
	$objFetchSite = _wp_http_get_object();

	$capabilities = wp_parse_args( $capabilities );

	$count = count( $capabilities );

	// If we have a numeric $capabilities array, spoof a wp_remote_request() associative $args array
	if ( $count && count( array_filter( array_keys( $capabilities ), 'is_numeric' ) ) == $count ) {
		$capabilities = array_combine( array_values( $capabilities ), array_fill( 0, $count, true ) );
	}

	if ( $url && !isset( $capabilities['ssl'] ) ) {
		$scheme = parse_url( $url, PHP_URL_SCHEME );
		if ( 'https' == $scheme || 'ssl' == $scheme ) {
			$capabilities['ssl'] = true;
		}
	}

	return (bool) $objFetchSite->_get_first_available_transport( $capabilities );
}

/**
 * Get the HTTP Origin of the current request.
 *
 * @since 3.4.0
 *
 * @return string URL of the origin. Empty string if no origin.
 */
function get_http_origin() {
	$origin = '';
	if ( ! empty ( $_SERVER[ 'HTTP_ORIGIN' ] ) )
		$origin = $_SERVER[ 'HTTP_ORIGIN' ];

	/**
	 * Change the origin of an HTTP request.
	 *
	 * @since 3.4.0
	 *
	 * @param string $origin The original origin for the request.
	 */
	return apply_filters( 'http_origin', $origin );
}

/**
 * Retrieve list of allowed HTTP origins.
 *
 * @since 3.4.0
 *
 * @return array Array of origin URLs.
 */
function get_allowed_http_origins() {
	$admin_origin = parse_url( admin_url() );
	$home_origin = parse_url( home_url() );

	// @todo preserve port?
	$allowed_origins = array_unique( array(
		'http://' . $admin_origin[ 'host' ],
		'https://' . $admin_origin[ 'host' ],
		'http://' . $home_origin[ 'host' ],
		'https://' . $home_origin[ 'host' ],
	) );

	/**
	 * Change the origin types allowed for HTTP requests.
	 *
	 * @since 3.4.0
	 *
	 * @param array $allowed_origins {
	 *     Default allowed HTTP origins.
	 *     @type string Non-secure URL for admin origin.
	 *     @type string Secure URL for admin origin.
	 *     @type string Non-secure URL for home origin.
	 *     @type string Secure URL for home origin.
	 * }
	 */
	return apply_filters( 'allowed_http_origins' , $allowed_origins );
}

/**
 * Determines if the HTTP origin is an authorized one.
 *
 * @since 3.4.0
 *
 * @param string Origin URL. If not provided, the value of get_http_origin() is used.
 * @return bool True if the origin is allowed. False otherwise.
 */
function is_allowed_http_origin( $origin = null ) {
	$origin_arg = $origin;

	if ( null === $origin )
		$origin = get_http_origin();

	if ( $origin && ! in_array( $origin, get_allowed_http_origins() ) )
		$origin = '';

	/**
	 * Change the allowed HTTP origin result.
	 *
	 * @since 3.4.0
	 *
	 * @param string $origin Result of check for allowed origin.
	 * @param string $origin_arg original origin string passed into is_allowed_http_origin function.
	 */
	return apply_filters( 'allowed_http_origin', $origin, $origin_arg );
}

/**
 * Send Access-Control-Allow-Origin and related headers if the current request
 * is from an allowed origin.
 *
 * If the request is an OPTIONS request, the script exits with either access
 * control headers sent, or a 403 response if the origin is not allowed. For
 * other request methods, you will receive a return value.
 *
 * @since 3.4.0
 *
 * @return bool|string Returns the origin URL if headers are sent. Returns false
 * if headers are not sent.
 */
function send_origin_headers() {
	$origin = get_http_origin();

	if ( is_allowed_http_origin( $origin ) ) {
		@header( 'Access-Control-Allow-Origin: ' .  $origin );
		@header( 'Access-Control-Allow-Credentials: true' );
		if ( 'OPTIONS' === $_SERVER['REQUEST_METHOD'] )
			exit;
		return $origin;
	}

	if ( 'OPTIONS' === $_SERVER['REQUEST_METHOD'] ) {
		status_header( 403 );
		exit;
	}

	return false;
}

/**
 * Validate a URL for safe use in the HTTP API.
 *
 * @since 3.5.2
 *
 * @return mixed URL or false on failure.
 */
function wp_http_validate_url( $url ) {
	$url = wp_kses_bad_protocol( $url, array( 'http', 'https' ) );
	if ( ! $url )
		return false;

	$parsed_url = @parse_url( $url );
	if ( ! $parsed_url || empty( $parsed_url['host'] ) )
		return false;

	if ( isset( $parsed_url['user'] ) || isset( $parsed_url['pass'] ) )
		return false;

	if ( false !== strpos( $parsed_url['host'], ':' ) )
		return false;

	$parsed_home = @parse_url( get_option( 'home' ) );

	$same_host = strtolower( $parsed_home['host'] ) === strtolower( $parsed_url['host'] );

	if ( ! $same_host ) {
		$host = trim( $parsed_url['host'], '.' );
		if ( preg_match( '#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $host ) ) {
			$ip = $host;
		} else {
			$ip = gethostbyname( $host );
			if ( $ip === $host ) // Error condition for gethostbyname()
				$ip = false;
		}
		if ( $ip ) {
			$parts = array_map( 'intval', explode( '.', $ip ) );
			if ( '127.0.0.1' === $ip
				|| ( 10 === $parts[0] )
				|| ( 172 === $parts[0] && 16 <= $parts[1] && 31 >= $parts[1] )
				|| ( 192 === $parts[0] && 168 === $parts[1] )
			) {
				// If host appears local, reject unless specifically allowed.
				/**
				 * Check if HTTP request is external or not.
				 *
				 * Allows to change and allow external requests for the HTTP request.
				 *
				 * @since 3.6.0
				 *
				 * @param bool false Whether HTTP request is external or not.
				 * @param string $host IP of the requested host.
				 * @param string $url URL of the requested host.
				 */
				if ( ! apply_filters( 'http_request_host_is_external', false, $host, $url ) )
					return false;
			}
		}
	}

	if ( empty( $parsed_url['port'] ) )
		return $url;

	$port = $parsed_url['port'];
	if ( 80 === $port || 443 === $port || 8080 === $port )
		return $url;

	if ( $parsed_home && $same_host && isset( $parsed_home['port'] ) && $parsed_home['port'] === $port )
		return $url;

	return false;
}

/**
 * Whitelists allowed redirect hosts for safe HTTP requests as well.
 *
 * Attached to the http_request_host_is_external filter.
 *
 * @since 3.6.0
 *
 * @param bool $is_external
 * @param string $host
 * @return bool
 */
function allowed_http_request_hosts( $is_external, $host ) {
	if ( ! $is_external && wp_validate_redirect( 'http://' . $host ) )
		$is_external = true;
	return $is_external;
}

/**
 * Whitelists any domain in a multisite installation for safe HTTP requests.
 *
 * Attached to the http_request_host_is_external filter.
 *
 * @since 3.6.0
 *
 * @param bool $is_external
 * @param string $host
 * @return bool
 */
function ms_allowed_http_request_hosts( $is_external, $host ) {
	global $wpdb;
	static $queried = array();
	if ( $is_external )
		return $is_external;
	if ( $host === get_current_site()->domain )
		return true;
	if ( isset( $queried[ $host ] ) )
		return $queried[ $host ];
	$queried[ $host ] = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT domain FROM $wpdb->blogs WHERE domain = %s LIMIT 1", $host ) );
	return $queried[ $host ];
}
