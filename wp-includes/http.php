<?php
/**
 * Core HTTP Request API
 *
 * Standardizes the HTTP requests for WordPress. Handles cookies, gzip encoding and decoding, chunk
 * decoding, if HTTP 1.1 and various other difficult HTTP protocol implementations.
 *
 * @package WordPress
 * @subpackage HTTP
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
	static $http = null;

	if ( is_null( $http ) ) {
		$http = new WP_Http();
	}
	return $http;
}

/**
 * Retrieves the raw response from a safe HTTP request.
 *
 * This function is ideal when the HTTP request is being made to an arbitrary
 * URL. The URL, and every URL it redirects to, are validated with wp_http_validate_url()
 * to avoid Server Side Request Forgery attacks (SSRF).
 *
 * The only supported protocols are `http` and `https`.
 *
 * @since 3.6.0
 *
 * @see wp_remote_request() For more information on the response array format.
 * @see WP_Http::request() For default arguments information.
 * @see wp_http_validate_url() For more information about how the URL is validated.
 *
 * @link https://owasp.org/www-community/attacks/Server_Side_Request_Forgery
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 *                     See WP_Http::request() for information on accepted arguments.
 * @return array|WP_Error The response or WP_Error on failure.
 *                        See WP_Http::request() for information on return value.
 */
function wp_safe_remote_request( $url, $args = array() ) {
	$args['reject_unsafe_urls'] = true;
	$http                       = _wp_http_get_object();
	return $http->request( $url, $args );
}

/**
 * Retrieves the raw response from a safe HTTP request using the GET method.
 *
 * This function is ideal when the HTTP request is being made to an arbitrary
 * URL. The URL, and every URL it redirects to, are validated with wp_http_validate_url()
 * to avoid Server Side Request Forgery attacks (SSRF).
 *
 * The only supported protocols are `http` and `https`.
 *
 * @since 3.6.0
 *
 * @see wp_remote_request() For more information on the response array format.
 * @see WP_Http::request() For default arguments information.
 * @see wp_http_validate_url() For more information about how the URL is validated.
 *
 * @link https://owasp.org/www-community/attacks/Server_Side_Request_Forgery
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 *                     See WP_Http::request() for information on accepted arguments.
 * @return array|WP_Error The response or WP_Error on failure.
 *                        See WP_Http::request() for information on return value.
 */
function wp_safe_remote_get( $url, $args = array() ) {
	$args['reject_unsafe_urls'] = true;
	$http                       = _wp_http_get_object();
	return $http->get( $url, $args );
}

/**
 * Retrieves the raw response from a safe HTTP request using the POST method.
 *
 * This function is ideal when the HTTP request is being made to an arbitrary
 * URL. The URL, and every URL it redirects to, are validated with wp_http_validate_url()
 * to avoid Server Side Request Forgery attacks (SSRF).
 *
 * The only supported protocols are `http` and `https`.
 *
 * @since 3.6.0
 *
 * @see wp_remote_request() For more information on the response array format.
 * @see WP_Http::request() For default arguments information.
 * @see wp_http_validate_url() For more information about how the URL is validated.
 *
 * @link https://owasp.org/www-community/attacks/Server_Side_Request_Forgery
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 *                     See WP_Http::request() for information on accepted arguments.
 * @return array|WP_Error The response or WP_Error on failure.
 *                        See WP_Http::request() for information on return value.
 */
function wp_safe_remote_post( $url, $args = array() ) {
	$args['reject_unsafe_urls'] = true;
	$http                       = _wp_http_get_object();
	return $http->post( $url, $args );
}

/**
 * Retrieves the raw response from a safe HTTP request using the HEAD method.
 *
 * This function is ideal when the HTTP request is being made to an arbitrary
 * URL. The URL, and every URL it redirects to, are validated with wp_http_validate_url()
 * to avoid Server Side Request Forgery attacks (SSRF).
 *
 * The only supported protocols are `http` and `https`.
 *
 * @since 3.6.0
 *
 * @see wp_remote_request() For more information on the response array format.
 * @see WP_Http::request() For default arguments information.
 * @see wp_http_validate_url() For more information about how the URL is validated.
 *
 * @link https://owasp.org/www-community/attacks/Server_Side_Request_Forgery
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 *                     See WP_Http::request() for information on accepted arguments.
 * @return array|WP_Error The response or WP_Error on failure.
 *                        See WP_Http::request() for information on return value.
 */
function wp_safe_remote_head( $url, $args = array() ) {
	$args['reject_unsafe_urls'] = true;
	$http                       = _wp_http_get_object();
	return $http->head( $url, $args );
}

/**
 * Performs an HTTP request and returns its response.
 *
 * There are other API functions available which abstract away the HTTP method:
 *
 *  - Default 'GET'  for wp_remote_get()
 *  - Default 'POST' for wp_remote_post()
 *  - Default 'HEAD' for wp_remote_head()
 *
 * Important: If the URL is user-controlled, use `wp_safe_remote_request()` instead.
 *
 * @since 2.7.0
 *
 * @see WP_Http::request() For information on default arguments.
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 *                     See WP_Http::request() for information on accepted arguments.
 * @return array|WP_Error The response array or a WP_Error on failure.
 *                        See WP_Http::request() for information on return value.
 */
function wp_remote_request( $url, $args = array() ) {
	$http = _wp_http_get_object();
	return $http->request( $url, $args );
}

/**
 * Performs an HTTP request using the GET method and returns its response.
 *
 * Important: If the URL is user-controlled, use `wp_safe_remote_get()` instead.
 *
 * @since 2.7.0
 *
 * @see wp_remote_request() For more information on the response array format.
 * @see WP_Http::request() For default arguments information.
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 *                     See WP_Http::request() for information on accepted arguments.
 * @return array|WP_Error The response or WP_Error on failure.
 *                        See WP_Http::request() for information on return value.
 */
function wp_remote_get( $url, $args = array() ) {
	$http = _wp_http_get_object();
	return $http->get( $url, $args );
}

/**
 * Performs an HTTP request using the POST method and returns its response.
 *
 * Important: If the URL is user-controlled, use `wp_safe_remote_post()` instead.
 *
 * @since 2.7.0
 *
 * @see wp_remote_request() For more information on the response array format.
 * @see WP_Http::request() For default arguments information.
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 *                     See WP_Http::request() for information on accepted arguments.
 * @return array|WP_Error The response or WP_Error on failure.
 *                        See WP_Http::request() for information on return value.
 */
function wp_remote_post( $url, $args = array() ) {
	$http = _wp_http_get_object();
	return $http->post( $url, $args );
}

/**
 * Performs an HTTP request using the HEAD method and returns its response.
 *
 * Important: If the URL is user-controlled, use `wp_safe_remote_head()` instead.
 *
 * @since 2.7.0
 *
 * @see wp_remote_request() For more information on the response array format.
 * @see WP_Http::request() For default arguments information.
 *
 * @param string $url  URL to retrieve.
 * @param array  $args Optional. Request arguments. Default empty array.
 *                     See WP_Http::request() for information on accepted arguments.
 * @return array|WP_Error The response or WP_Error on failure.
 *                        See WP_Http::request() for information on return value.
 */
function wp_remote_head( $url, $args = array() ) {
	$http = _wp_http_get_object();
	return $http->head( $url, $args );
}

/**
 * Retrieves only the headers from the raw response.
 *
 * @since 2.7.0
 * @since 4.6.0 Return value changed from an array to an WpOrg\Requests\Utility\CaseInsensitiveDictionary instance.
 *
 * @see \WpOrg\Requests\Utility\CaseInsensitiveDictionary
 *
 * @param array|WP_Error $response HTTP response.
 * @return \WpOrg\Requests\Utility\CaseInsensitiveDictionary|array The headers of the response, or empty array
 *                                                                 if incorrect parameter given.
 */
function wp_remote_retrieve_headers( $response ) {
	if ( is_wp_error( $response ) || ! isset( $response['headers'] ) ) {
		return array();
	}

	return $response['headers'];
}

/**
 * Retrieves a single header by name from the raw response.
 *
 * @since 2.7.0
 *
 * @param array|WP_Error $response HTTP response.
 * @param string         $header   Header name to retrieve value from.
 * @return array|string The header(s) value(s). Array if multiple headers with the same name are retrieved.
 *                      Empty string if incorrect parameter given, or if the header doesn't exist.
 */
function wp_remote_retrieve_header( $response, $header ) {
	if ( is_wp_error( $response ) || ! isset( $response['headers'] ) ) {
		return '';
	}

	if ( isset( $response['headers'][ $header ] ) ) {
		return $response['headers'][ $header ];
	}

	return '';
}

/**
 * Retrieves only the response code from the raw response.
 *
 * Will return an empty string if incorrect parameter value is given.
 *
 * @since 2.7.0
 *
 * @param array|WP_Error $response HTTP response.
 * @return int|string The response code as an integer. Empty string if incorrect parameter given.
 */
function wp_remote_retrieve_response_code( $response ) {
	if ( is_wp_error( $response ) || ! isset( $response['response'] ) || ! is_array( $response['response'] ) ) {
		return '';
	}

	return $response['response']['code'];
}

/**
 * Retrieves only the response message from the raw response.
 *
 * Will return an empty string if incorrect parameter value is given.
 *
 * @since 2.7.0
 *
 * @param array|WP_Error $response HTTP response.
 * @return string The response message. Empty string if incorrect parameter given.
 */
function wp_remote_retrieve_response_message( $response ) {
	if ( is_wp_error( $response ) || ! isset( $response['response'] ) || ! is_array( $response['response'] ) ) {
		return '';
	}

	return $response['response']['message'];
}

/**
 * Retrieves only the body from the raw response.
 *
 * @since 2.7.0
 *
 * @param array|WP_Error $response HTTP response.
 * @return string The body of the response. Empty string if no body or incorrect parameter given.
 */
function wp_remote_retrieve_body( $response ) {
	if ( is_wp_error( $response ) || ! isset( $response['body'] ) ) {
		return '';
	}

	return $response['body'];
}

/**
 * Retrieves only the cookies from the raw response.
 *
 * @since 4.4.0
 *
 * @param array|WP_Error $response HTTP response.
 * @return WP_Http_Cookie[] An array of `WP_Http_Cookie` objects from the response.
 *                          Empty array if there are none, or the response is a WP_Error.
 */
function wp_remote_retrieve_cookies( $response ) {
	if ( is_wp_error( $response ) || empty( $response['cookies'] ) ) {
		return array();
	}

	return $response['cookies'];
}

/**
 * Retrieves a single cookie by name from the raw response.
 *
 * @since 4.4.0
 *
 * @param array|WP_Error $response HTTP response.
 * @param string         $name     The name of the cookie to retrieve.
 * @return WP_Http_Cookie|string The `WP_Http_Cookie` object, or empty string
 *                               if the cookie is not present in the response.
 */
function wp_remote_retrieve_cookie( $response, $name ) {
	$cookies = wp_remote_retrieve_cookies( $response );

	if ( empty( $cookies ) ) {
		return '';
	}

	foreach ( $cookies as $cookie ) {
		if ( $cookie->name === $name ) {
			return $cookie;
		}
	}

	return '';
}

/**
 * Retrieves a single cookie's value by name from the raw response.
 *
 * @since 4.4.0
 *
 * @param array|WP_Error $response HTTP response.
 * @param string         $name     The name of the cookie to retrieve.
 * @return string The value of the cookie, or empty string
 *                if the cookie is not present in the response.
 */
function wp_remote_retrieve_cookie_value( $response, $name ) {
	$cookie = wp_remote_retrieve_cookie( $response, $name );

	if ( ! ( $cookie instanceof WP_Http_Cookie ) ) {
		return '';
	}

	return $cookie->value;
}

/**
 * Determines if there is an HTTP Transport that can process this request.
 *
 * @since 3.2.0
 *
 * @param array  $capabilities Array of capabilities to test or a wp_remote_request() $args array.
 * @param string $url          Optional. If given, will check if the URL requires SSL and adds
 *                             that requirement to the capabilities array.
 *
 * @return bool
 */
function wp_http_supports( $capabilities = array(), $url = null ) {
	$capabilities = wp_parse_args( $capabilities );

	$count = count( $capabilities );

	// If we have a numeric $capabilities array, spoof a wp_remote_request() associative $args array.
	if ( $count && count( array_filter( array_keys( $capabilities ), 'is_numeric' ) ) === $count ) {
		$capabilities = array_combine( array_values( $capabilities ), array_fill( 0, $count, true ) );
	}

	if ( $url && ! isset( $capabilities['ssl'] ) ) {
		$scheme = parse_url( $url, PHP_URL_SCHEME );
		if ( 'https' === $scheme || 'ssl' === $scheme ) {
			$capabilities['ssl'] = true;
		}
	}

	return WpOrg\Requests\Requests::has_capabilities( $capabilities );
}

/**
 * Gets the HTTP Origin of the current request.
 *
 * @since 3.4.0
 *
 * @return string URL of the origin. Empty string if no origin.
 */
function get_http_origin() {
	$origin = '';
	if ( ! empty( $_SERVER['HTTP_ORIGIN'] ) ) {
		$origin = $_SERVER['HTTP_ORIGIN'];
	}

	/**
	 * Changes the origin of an HTTP request.
	 *
	 * @since 3.4.0
	 *
	 * @param string $origin The HTTP origin for the request.
	 */
	return apply_filters( 'http_origin', $origin );
}

/**
 * Retrieves list of allowed HTTP origins.
 *
 * @since 3.4.0
 *
 * @return string[] Array of origin URLs.
 */
function get_allowed_http_origins() {
	$admin_origin = parse_url( admin_url() );
	$home_origin  = parse_url( home_url() );

	// @todo Preserve port?
	$allowed_origins = array_unique(
		array(
			'http://' . $admin_origin['host'],
			'https://' . $admin_origin['host'],
			'http://' . $home_origin['host'],
			'https://' . $home_origin['host'],
		)
	);

	/**
	 * Changes the origin types allowed for HTTP requests.
	 *
	 * @since 3.4.0
	 *
	 * @param string[] $allowed_origins Array of allowed HTTP origins.
	 */
	return apply_filters( 'allowed_http_origins', $allowed_origins );
}

/**
 * Determines if the HTTP origin is an authorized one.
 *
 * @since 3.4.0
 *
 * @param string|null $origin Origin URL. If not provided, the value of get_http_origin() is used.
 * @return string Origin URL if allowed, empty string if not.
 */
function is_allowed_http_origin( $origin = null ) {
	$origin_arg = $origin;

	if ( null === $origin ) {
		$origin = get_http_origin();
	}

	if ( $origin && ! in_array( $origin, get_allowed_http_origins(), true ) ) {
		$origin = '';
	}

	/**
	 * Changes the allowed HTTP origin result.
	 *
	 * @since 3.4.0
	 *
	 * @param string $origin     Origin URL if allowed, empty string if not.
	 * @param string $origin_arg Original origin string passed into is_allowed_http_origin function.
	 */
	return apply_filters( 'allowed_http_origin', $origin, $origin_arg );
}

/**
 * Sends Access-Control-Allow-Origin and related headers if the current request
 * is from an allowed origin.
 *
 * If the request is an OPTIONS request, the script exits with either access
 * control headers sent, or a 403 response if the origin is not allowed. For
 * other request methods, you will receive a return value.
 *
 * @since 3.4.0
 *
 * @return string|false Returns the origin URL if headers are sent. Returns false
 *                      if headers are not sent.
 */
function send_origin_headers() {
	$origin = get_http_origin();

	if ( is_allowed_http_origin( $origin ) ) {
		header( 'Access-Control-Allow-Origin: ' . $origin );
		header( 'Access-Control-Allow-Credentials: true' );
		if ( 'OPTIONS' === $_SERVER['REQUEST_METHOD'] ) {
			exit;
		}
		return $origin;
	}

	if ( 'OPTIONS' === $_SERVER['REQUEST_METHOD'] ) {
		status_header( 403 );
		exit;
	}

	return false;
}

/**
 * Validates a URL as safe for use in the HTTP API.
 *
 * The only supported protocols are `http` and `https`.
 *
 * Examples of URLs that are considered unsafe:
 *
 * - `ftp://example.com/caniload.php` - Invalid protocol - only http and https are allowed.
 * - `http:///example.com/caniload.php` - Malformed URL.
 * - `http://user:pass@example.com/caniload.php` - Login information.
 * - `http://example.invalid/caniload.php` - Invalid hostname, as the IP cannot be looked up in DNS.
 *
 * Examples of URLs that are considered unsafe by default but can be allowed with filters:
 *
 * - `http://192.168.0.1/caniload.php` - IP address from LAN network.
 *   This can be changed with the {@see 'http_request_host_is_external'} filter.
 * - `http://198.143.164.252:81/caniload.php` - By default, only ports 80, 443, and 8080 are allowed.
 *   This can be changed with the {@see 'http_allowed_safe_ports'} filter.
 *
 * @since 3.5.2
 *
 * @param string $url Request URL.
 * @return string|false Returns false if the URL is not safe, or the original URL if it is safe.
 */
function wp_http_validate_url( $url ) {
	if ( ! is_string( $url ) || '' === $url || is_numeric( $url ) ) {
		return false;
	}

	$original_url = $url;
	$url          = wp_kses_bad_protocol( $url, array( 'http', 'https' ) );
	if ( ! $url || strtolower( $url ) !== strtolower( $original_url ) ) {
		return false;
	}

	$parsed_url = parse_url( $url );
	if ( ! $parsed_url || empty( $parsed_url['host'] ) ) {
		return false;
	}

	if ( isset( $parsed_url['user'] ) || isset( $parsed_url['pass'] ) ) {
		return false;
	}

	if ( false !== strpbrk( $parsed_url['host'], ':#?[]' ) ) {
		return false;
	}

	$parsed_home = parse_url( get_option( 'home' ) );
	$same_host   = isset( $parsed_home['host'] ) && strtolower( $parsed_home['host'] ) === strtolower( $parsed_url['host'] );
	$host        = trim( $parsed_url['host'], '.' );

	if ( ! $same_host ) {
		if ( preg_match( '#^(([1-9]?\d|1\d\d|25[0-5]|2[0-4]\d)\.){3}([1-9]?\d|1\d\d|25[0-5]|2[0-4]\d)$#', $host ) ) {
			$ip = $host;
		} else {
			$ip = gethostbyname( $host );
			if ( $ip === $host ) { // Error condition for gethostbyname().
				return false;
			}
		}
		if ( $ip ) {
			$parts = array_map( 'intval', explode( '.', $ip ) );
			if ( 127 === $parts[0] || 10 === $parts[0] || 0 === $parts[0]
				|| ( 172 === $parts[0] && 16 <= $parts[1] && 31 >= $parts[1] )
				|| ( 192 === $parts[0] && 168 === $parts[1] )
			) {
				// If host appears local, reject unless specifically allowed.
				/**
				 * Checks if HTTP request is external or not.
				 *
				 * Allows to change and allow external requests for the HTTP request.
				 *
				 * @since 3.6.0
				 *
				 * @param bool   $external Whether HTTP request is external or not.
				 * @param string $host     Host name of the requested URL.
				 * @param string $url      Requested URL.
				 */
				if ( ! apply_filters( 'http_request_host_is_external', false, $host, $url ) ) {
					return false;
				}
			}
		}
	}

	if ( empty( $parsed_url['port'] ) ) {
		return $url;
	}

	$port = $parsed_url['port'];

	/**
	 * Controls the list of ports considered safe in HTTP API.
	 *
	 * Allows to change and allow external requests for the HTTP request.
	 *
	 * @since 5.9.0
	 *
	 * @param int[]  $allowed_ports Array of integers for valid ports. Default allowed ports
	 *                              are 80, 443, and 8080.
	 * @param string $host          Host name of the requested URL.
	 * @param string $url           Requested URL.
	 */
	$allowed_ports = apply_filters( 'http_allowed_safe_ports', array( 80, 443, 8080 ), $host, $url );
	if ( is_array( $allowed_ports ) && in_array( $port, $allowed_ports, true ) ) {
		return $url;
	}

	if ( $parsed_home && $same_host && isset( $parsed_home['port'] ) && $parsed_home['port'] === $port ) {
		return $url;
	}

	return false;
}

/**
 * Marks allowed redirect hosts safe for HTTP requests as well.
 *
 * Attached to the {@see 'http_request_host_is_external'} filter.
 *
 * @since 3.6.0
 *
 * @param bool   $is_external
 * @param string $host
 * @return bool
 */
function allowed_http_request_hosts( $is_external, $host ) {
	if ( ! $is_external && wp_validate_redirect( 'http://' . $host ) ) {
		$is_external = true;
	}
	return $is_external;
}

/**
 * Adds any domain in a multisite installation for safe HTTP requests to the
 * allowed list.
 *
 * Attached to the {@see 'http_request_host_is_external'} filter.
 *
 * @since 3.6.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param bool   $is_external
 * @param string $host
 * @return bool
 */
function ms_allowed_http_request_hosts( $is_external, $host ) {
	global $wpdb;
	static $queried = array();
	if ( $is_external ) {
		return $is_external;
	}
	if ( get_network()->domain === $host ) {
		return true;
	}
	if ( isset( $queried[ $host ] ) ) {
		return $queried[ $host ];
	}
	$queried[ $host ] = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT domain FROM $wpdb->blogs WHERE domain = %s LIMIT 1", $host ) );
	return $queried[ $host ];
}

/**
 * A wrapper for PHP's parse_url() function that handles consistency in the return values
 * across PHP versions.
 *
 * Across various PHP versions, schemeless URLs containing a ":" in the query
 * are being handled inconsistently. This function works around those differences.
 *
 * @since 4.4.0
 * @since 4.7.0 The `$component` parameter was added for parity with PHP's `parse_url()`.
 *
 * @link https://www.php.net/manual/en/function.parse-url.php
 *
 * @param string $url       The URL to parse.
 * @param int    $component The specific component to retrieve. Use one of the PHP
 *                          predefined constants to specify which one.
 *                          Defaults to -1 (= return all parts as an array).
 * @return mixed False on parse failure; Array of URL components on success;
 *               When a specific component has been requested: null if the component
 *               doesn't exist in the given URL; a string or - in the case of
 *               PHP_URL_PORT - integer when it does. See parse_url()'s return values.
 */
function wp_parse_url( $url, $component = -1 ) {
	$to_unset = array();
	$url      = (string) $url;

	if ( str_starts_with( $url, '//' ) ) {
		$to_unset[] = 'scheme';
		$url        = 'placeholder:' . $url;
	} elseif ( str_starts_with( $url, '/' ) ) {
		$to_unset[] = 'scheme';
		$to_unset[] = 'host';
		$url        = 'placeholder://placeholder' . $url;
	}

	$parts = parse_url( $url );

	if ( false === $parts ) {
		// Parsing failure.
		return $parts;
	}

	// Remove the placeholder values.
	foreach ( $to_unset as $key ) {
		unset( $parts[ $key ] );
	}

	return _get_component_from_parsed_url_array( $parts, $component );
}

/**
 * Retrieves a specific component from a parsed URL array.
 *
 * @internal
 *
 * @since 4.7.0
 * @access private
 *
 * @link https://www.php.net/manual/en/function.parse-url.php
 *
 * @param array|false $url_parts The parsed URL. Can be false if the URL failed to parse.
 * @param int         $component The specific component to retrieve. Use one of the PHP
 *                               predefined constants to specify which one.
 *                               Defaults to -1 (= return all parts as an array).
 * @return mixed False on parse failure; Array of URL components on success;
 *               When a specific component has been requested: null if the component
 *               doesn't exist in the given URL; a string or - in the case of
 *               PHP_URL_PORT - integer when it does. See parse_url()'s return values.
 */
function _get_component_from_parsed_url_array( $url_parts, $component = -1 ) {
	if ( -1 === $component ) {
		return $url_parts;
	}

	$key = _wp_translate_php_url_constant_to_key( $component );
	if ( false !== $key && is_array( $url_parts ) && isset( $url_parts[ $key ] ) ) {
		return $url_parts[ $key ];
	} else {
		return null;
	}
}

/**
 * Translates a PHP_URL_* constant to the named array keys PHP uses.
 *
 * @internal
 *
 * @since 4.7.0
 * @access private
 *
 * @link https://www.php.net/manual/en/url.constants.php
 *
 * @param int $constant PHP_URL_* constant.
 * @return string|false The named key or false.
 */
function _wp_translate_php_url_constant_to_key( $constant ) {
	$translation = array(
		PHP_URL_SCHEME   => 'scheme',
		PHP_URL_HOST     => 'host',
		PHP_URL_PORT     => 'port',
		PHP_URL_USER     => 'user',
		PHP_URL_PASS     => 'pass',
		PHP_URL_PATH     => 'path',
		PHP_URL_QUERY    => 'query',
		PHP_URL_FRAGMENT => 'fragment',
	);

	if ( isset( $translation[ $constant ] ) ) {
		return $translation[ $constant ];
	} else {
		return false;
	}
}
