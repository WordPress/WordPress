<?php
/**
 * HTTP API: WP_Http class
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7.0
 */

if ( ! class_exists( 'Requests' ) ) {
	require( ABSPATH . WPINC . '/class-requests.php' );

	Requests::register_autoloader();
	Requests::set_certificate_path( ABSPATH . WPINC . '/certificates/ca-bundle.crt' );
}

/**
 * Core class used for managing HTTP transports and making HTTP requests.
 *
 * This class is used to consistently make outgoing HTTP requests easy for developers
 * while still being compatible with the many PHP configurations under which
 * WordPress runs.
 *
 * Debugging includes several actions, which pass different variables for debugging the HTTP API.
 *
 * @since 2.7.0
 */
class WP_Http {

	// Aliases for HTTP response codes.
	const HTTP_CONTINUE                   = 100;
	const SWITCHING_PROTOCOLS             = 101;
	const PROCESSING                      = 102;

	const OK                              = 200;
	const CREATED                         = 201;
	const ACCEPTED                        = 202;
	const NON_AUTHORITATIVE_INFORMATION   = 203;
	const NO_CONTENT                      = 204;
	const RESET_CONTENT                   = 205;
	const PARTIAL_CONTENT                 = 206;
	const MULTI_STATUS                    = 207;
	const IM_USED                         = 226;

	const MULTIPLE_CHOICES                = 300;
	const MOVED_PERMANENTLY               = 301;
	const FOUND                           = 302;
	const SEE_OTHER                       = 303;
	const NOT_MODIFIED                    = 304;
	const USE_PROXY                       = 305;
	const RESERVED                        = 306;
	const TEMPORARY_REDIRECT              = 307;
	const PERMANENT_REDIRECT              = 308;

	const BAD_REQUEST                     = 400;
	const UNAUTHORIZED                    = 401;
	const PAYMENT_REQUIRED                = 402;
	const FORBIDDEN                       = 403;
	const NOT_FOUND                       = 404;
	const METHOD_NOT_ALLOWED              = 405;
	const NOT_ACCEPTABLE                  = 406;
	const PROXY_AUTHENTICATION_REQUIRED   = 407;
	const REQUEST_TIMEOUT                 = 408;
	const CONFLICT                        = 409;
	const GONE                            = 410;
	const LENGTH_REQUIRED                 = 411;
	const PRECONDITION_FAILED             = 412;
	const REQUEST_ENTITY_TOO_LARGE        = 413;
	const REQUEST_URI_TOO_LONG            = 414;
	const UNSUPPORTED_MEDIA_TYPE          = 415;
	const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	const EXPECTATION_FAILED              = 417;
	const IM_A_TEAPOT                     = 418;
	const MISDIRECTED_REQUEST             = 421;
	const UNPROCESSABLE_ENTITY            = 422;
	const LOCKED                          = 423;
	const FAILED_DEPENDENCY               = 424;
	const UPGRADE_REQUIRED                = 426;
	const PRECONDITION_REQUIRED           = 428;
	const TOO_MANY_REQUESTS               = 429;
	const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
	const UNAVAILABLE_FOR_LEGAL_REASONS   = 451;

	const INTERNAL_SERVER_ERROR           = 500;
	const NOT_IMPLEMENTED                 = 501;
	const BAD_GATEWAY                     = 502;
	const SERVICE_UNAVAILABLE             = 503;
	const GATEWAY_TIMEOUT                 = 504;
	const HTTP_VERSION_NOT_SUPPORTED      = 505;
	const VARIANT_ALSO_NEGOTIATES         = 506;
	const INSUFFICIENT_STORAGE            = 507;
	const NOT_EXTENDED                    = 510;
	const NETWORK_AUTHENTICATION_REQUIRED = 511;

	/**
	 * Send an HTTP request to a URI.
	 *
	 * Please note: The only URI that are supported in the HTTP Transport implementation
	 * are the HTTP and HTTPS protocols.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @global string $wp_version
	 *
	 * @param string       $url  The request URL.
	 * @param string|array $args {
	 *     Optional. Array or string of HTTP request arguments.
	 *
	 *     @type string       $method              Request method. Accepts 'GET', 'POST', 'HEAD', or 'PUT'.
	 *                                             Some transports technically allow others, but should not be
	 *                                             assumed. Default 'GET'.
	 *     @type int          $timeout             How long the connection should stay open in seconds. Default 5.
	 *     @type int          $redirection         Number of allowed redirects. Not supported by all transports
	 *                                             Default 5.
	 *     @type string       $httpversion         Version of the HTTP protocol to use. Accepts '1.0' and '1.1'.
	 *                                             Default '1.0'.
	 *     @type string       $user-agent          User-agent value sent.
	 *                                             Default WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ).
	 *     @type bool         $reject_unsafe_urls  Whether to pass URLs through wp_http_validate_url().
	 *                                             Default false.
	 *     @type bool         $blocking            Whether the calling code requires the result of the request.
	 *                                             If set to false, the request will be sent to the remote server,
	 *                                             and processing returned to the calling code immediately, the caller
	 *                                             will know if the request succeeded or failed, but will not receive
	 *                                             any response from the remote server. Default true.
	 *     @type string|array $headers             Array or string of headers to send with the request.
	 *                                             Default empty array.
	 *     @type array        $cookies             List of cookies to send with the request. Default empty array.
	 *     @type string|array $body                Body to send with the request. Default null.
	 *     @type bool         $compress            Whether to compress the $body when sending the request.
	 *                                             Default false.
	 *     @type bool         $decompress          Whether to decompress a compressed response. If set to false and
	 *                                             compressed content is returned in the response anyway, it will
	 *                                             need to be separately decompressed. Default true.
	 *     @type bool         $sslverify           Whether to verify SSL for the request. Default true.
	 *     @type string       sslcertificates      Absolute path to an SSL certificate .crt file.
	 *                                             Default ABSPATH . WPINC . '/certificates/ca-bundle.crt'.
	 *     @type bool         $stream              Whether to stream to a file. If set to true and no filename was
	 *                                             given, it will be droped it in the WP temp dir and its name will
	 *                                             be set using the basename of the URL. Default false.
	 *     @type string       $filename            Filename of the file to write to when streaming. $stream must be
	 *                                             set to true. Default null.
	 *     @type int          $limit_response_size Size in bytes to limit the response to. Default null.
	 *
	 * }
	 * @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'.
	 *                        A WP_Error instance upon error.
	 */
	public function request( $url, $args = array() ) {
		global $wp_version;

		$defaults = array(
			'method' => 'GET',
			/**
			 * Filters the timeout value for an HTTP request.
			 *
			 * @since 2.7.0
			 *
			 * @param int $timeout_value Time in seconds until a request times out.
			 *                           Default 5.
			 */
			'timeout' => apply_filters( 'http_request_timeout', 5 ),
			/**
			 * Filters the number of redirects allowed during an HTTP request.
			 *
			 * @since 2.7.0
			 *
			 * @param int $redirect_count Number of redirects allowed. Default 5.
			 */
			'redirection' => apply_filters( 'http_request_redirection_count', 5 ),
			/**
			 * Filters the version of the HTTP protocol used in a request.
			 *
			 * @since 2.7.0
			 *
			 * @param string $version Version of HTTP used. Accepts '1.0' and '1.1'.
			 *                        Default '1.0'.
			 */
			'httpversion' => apply_filters( 'http_request_version', '1.0' ),
			/**
			 * Filters the user agent value sent with an HTTP request.
			 *
			 * @since 2.7.0
			 *
			 * @param string $user_agent WordPress user agent string.
			 */
			'user-agent' => apply_filters( 'http_headers_useragent', 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) ),
			/**
			 * Filters whether to pass URLs through wp_http_validate_url() in an HTTP request.
			 *
			 * @since 3.6.0
			 *
			 * @param bool $pass_url Whether to pass URLs through wp_http_validate_url().
			 *                       Default false.
			 */
			'reject_unsafe_urls' => apply_filters( 'http_request_reject_unsafe_urls', false ),
			'blocking' => true,
			'headers' => array(),
			'cookies' => array(),
			'body' => null,
			'compress' => false,
			'decompress' => true,
			'sslverify' => true,
			'sslcertificates' => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
			'stream' => false,
			'filename' => null,
			'limit_response_size' => null,
		);

		// Pre-parse for the HEAD checks.
		$args = wp_parse_args( $args );

		// By default, Head requests do not cause redirections.
		if ( isset($args['method']) && 'HEAD' == $args['method'] )
			$defaults['redirection'] = 0;

		$r = wp_parse_args( $args, $defaults );
		/**
		 * Filters the arguments used in an HTTP request.
		 *
		 * @since 2.7.0
		 *
		 * @param array  $r   An array of HTTP request arguments.
		 * @param string $url The request URL.
		 */
		$r = apply_filters( 'http_request_args', $r, $url );

		// The transports decrement this, store a copy of the original value for loop purposes.
		if ( ! isset( $r['_redirection'] ) )
			$r['_redirection'] = $r['redirection'];

		/**
		 * Filters whether to preempt an HTTP request's return value.
		 *
		 * Returning a non-false value from the filter will short-circuit the HTTP request and return
		 * early with that value. A filter should return either:
		 *
		 *  - An array containing 'headers', 'body', 'response', 'cookies', and 'filename' elements
		 *  - A WP_Error instance
		 *  - boolean false (to avoid short-circuiting the response)
		 *
		 * Returning any other value may result in unexpected behaviour.
		 *
		 * @since 2.9.0
		 *
		 * @param false|array|WP_Error $preempt Whether to preempt an HTTP request's return value. Default false.
		 * @param array               $r        HTTP request arguments.
		 * @param string              $url      The request URL.
		 */
		$pre = apply_filters( 'pre_http_request', false, $r, $url );

		if ( false !== $pre )
			return $pre;

		if ( function_exists( 'wp_kses_bad_protocol' ) ) {
			if ( $r['reject_unsafe_urls'] ) {
				$url = wp_http_validate_url( $url );
			}
			if ( $url ) {
				$url = wp_kses_bad_protocol( $url, array( 'http', 'https', 'ssl' ) );
			}
		}

		$arrURL = @parse_url( $url );

		if ( empty( $url ) || empty( $arrURL['scheme'] ) ) {
			return new WP_Error('http_request_failed', __('A valid URL was not provided.'));
		}

		if ( $this->block_request( $url ) ) {
			return new WP_Error( 'http_request_failed', __( 'User has blocked requests through HTTP.' ) );
		}

		// If we are streaming to a file but no filename was given drop it in the WP temp dir
		// and pick its name using the basename of the $url
		if ( $r['stream'] ) {
			if ( empty( $r['filename'] ) ) {
				$r['filename'] = get_temp_dir() . basename( $url );
			}

			// Force some settings if we are streaming to a file and check for existence and perms of destination directory
			$r['blocking'] = true;
			if ( ! wp_is_writable( dirname( $r['filename'] ) ) ) {
				return new WP_Error( 'http_request_failed', __( 'Destination directory for file streaming does not exist or is not writable.' ) );
			}
		}

		if ( is_null( $r['headers'] ) ) {
			$r['headers'] = array();
		}

		// WP allows passing in headers as a string, weirdly.
		if ( ! is_array( $r['headers'] ) ) {
			$processedHeaders = WP_Http::processHeaders( $r['headers'] );
			$r['headers'] = $processedHeaders['headers'];
		}

		// Setup arguments
		$headers = $r['headers'];
		$data = $r['body'];
		$type = $r['method'];
		$options = array(
			'timeout' => $r['timeout'],
			'useragent' => $r['user-agent'],
			'blocking' => $r['blocking'],
			'hooks' => new Requests_Hooks(),
		);

		// Ensure redirects follow browser behaviour.
		$options['hooks']->register( 'requests.before_redirect', array( get_class(), 'browser_redirect_compatibility' ) );

		if ( $r['stream'] ) {
			$options['filename'] = $r['filename'];
		}
		if ( empty( $r['redirection'] ) ) {
			$options['follow_redirects'] = false;
		} else {
			$options['redirects'] = $r['redirection'];
		}

		// Use byte limit, if we can
		if ( isset( $r['limit_response_size'] ) ) {
			$options['max_bytes'] = $r['limit_response_size'];
		}

		// If we've got cookies, use and convert them to Requests_Cookie.
		if ( ! empty( $r['cookies'] ) ) {
			$options['cookies'] = WP_Http::normalize_cookies( $r['cookies'] );
		}

		// SSL certificate handling
		if ( ! $r['sslverify'] ) {
			$options['verify'] = false;
			$options['verifyname'] = false;
		} else {
			$options['verify'] = $r['sslcertificates'];
		}

		// All non-GET/HEAD requests should put the arguments in the form body.
		if ( 'HEAD' !== $type && 'GET' !== $type ) {
			$options['data_format'] = 'body';
		}

		/**
		 * Filters whether SSL should be verified for non-local requests.
		 *
		 * @since 2.8.0
		 *
		 * @param bool $ssl_verify Whether to verify the SSL connection. Default true.
		 */
		$options['verify'] = apply_filters( 'https_ssl_verify', $options['verify'] );

		// Check for proxies.
		$proxy = new WP_HTTP_Proxy();
		if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) {
			$options['proxy'] = new Requests_Proxy_HTTP( $proxy->host() . ':' . $proxy->port() );

			if ( $proxy->use_authentication() ) {
				$options['proxy']->use_authentication = true;
				$options['proxy']->user = $proxy->username();
				$options['proxy']->pass = $proxy->password();
			}
		}

		// Avoid issues where mbstring.func_overload is enabled
		mbstring_binary_safe_encoding();

		try {
			$requests_response = Requests::request( $url, $headers, $data, $type, $options );

			// Convert the response into an array
			$http_response = new WP_HTTP_Requests_Response( $requests_response, $r['filename'] );
			$response = $http_response->to_array();

			// Add the original object to the array.
			$response['http_response'] = $http_response;
		}
		catch ( Requests_Exception $e ) {
			$response = new WP_Error( 'http_request_failed', $e->getMessage() );
		}

		reset_mbstring_encoding();

		/**
		 * Fires after an HTTP API response is received and before the response is returned.
		 *
		 * @since 2.8.0
		 *
		 * @param array|WP_Error $response HTTP response or WP_Error object.
		 * @param string         $context  Context under which the hook is fired.
		 * @param string         $class    HTTP transport used.
		 * @param array          $args     HTTP request arguments.
		 * @param string         $url      The request URL.
		 */
		do_action( 'http_api_debug', $response, 'response', 'Requests', $r, $url );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! $r['blocking'] ) {
			return array(
				'headers' => array(),
				'body' => '',
				'response' => array(
					'code' => false,
					'message' => false,
				),
				'cookies' => array(),
				'http_response' => null,
			);
		}

		/**
		 * Filters the HTTP API response immediately before the response is returned.
		 *
		 * @since 2.9.0
		 *
		 * @param array  $response HTTP response.
		 * @param array  $r        HTTP request arguments.
		 * @param string $url      The request URL.
		 */
		return apply_filters( 'http_response', $response, $r, $url );
	}

	/**
	 * Normalizes cookies for using in Requests.
	 *
	 * @since 4.6.0
	 * @access public
	 * @static
	 *
	 * @param array $cookies List of cookies to send with the request.
	 * @return Requests_Cookie_Jar Cookie holder object.
	 */
	public static function normalize_cookies( $cookies ) {
		$cookie_jar = new Requests_Cookie_Jar();

		foreach ( $cookies as $name => $value ) {
			if ( $value instanceof WP_Http_Cookie ) {
				$cookie_jar[ $value->name ] = new Requests_Cookie( $value->name, $value->value, $value->get_attributes() );
			} elseif ( is_scalar( $value ) ) {
				$cookie_jar[ $name ] = new Requests_Cookie( $name, $value );
			}
		}

		return $cookie_jar;
	}

	/**
	 * Match redirect behaviour to browser handling.
	 *
	 * Changes 302 redirects from POST to GET to match browser handling. Per
	 * RFC 7231, user agents can deviate from the strict reading of the
	 * specification for compatibility purposes.
	 *
	 * @since 4.6.0
	 * @access public
	 * @static
	 *
	 * @param string            $location URL to redirect to.
	 * @param array             $headers  Headers for the redirect.
	 * @param array             $options  Redirect request options.
	 * @param Requests_Response $original Response object.
	 */
	public static function browser_redirect_compatibility( $location, $headers, $data, &$options, $original ) {
		// Browser compat
		if ( $original->status_code === 302 ) {
			$options['type'] = Requests::GET;
		}
	}

	/**
	 * Tests which transports are capable of supporting the request.
	 *
	 * @since 3.2.0
	 * @access public
	 *
	 * @param array $args Request arguments
	 * @param string $url URL to Request
	 *
	 * @return string|false Class name for the first transport that claims to support the request. False if no transport claims to support the request.
	 */
	public function _get_first_available_transport( $args, $url = null ) {
		$transports = array( 'curl', 'streams' );

		/**
		 * Filters which HTTP transports are available and in what order.
		 *
		 * @since 3.7.0
		 *
		 * @param array  $transports Array of HTTP transports to check. Default array contains
		 *                           'curl', and 'streams', in that order.
		 * @param array  $args       HTTP request arguments.
		 * @param string $url        The URL to request.
		 */
		$request_order = apply_filters( 'http_api_transports', $transports, $args, $url );

		// Loop over each transport on each HTTP request looking for one which will serve this request's needs.
		foreach ( $request_order as $transport ) {
			if ( in_array( $transport, $transports ) ) {
				$transport = ucfirst( $transport );
			}
			$class = 'WP_Http_' . $transport;

			// Check to see if this transport is a possibility, calls the transport statically.
			if ( !call_user_func( array( $class, 'test' ), $args, $url ) )
				continue;

			return $class;
		}

		return false;
	}

	/**
	 * Dispatches a HTTP request to a supporting transport.
	 *
	 * Tests each transport in order to find a transport which matches the request arguments.
	 * Also caches the transport instance to be used later.
	 *
	 * The order for requests is cURL, and then PHP Streams.
	 *
	 * @since 3.2.0
	 *
	 * @static
	 * @access private
	 *
	 * @param string $url URL to Request
	 * @param array $args Request arguments
	 * @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'. A WP_Error instance upon error
	 */
	private function _dispatch_request( $url, $args ) {
		static $transports = array();

		$class = $this->_get_first_available_transport( $args, $url );
		if ( !$class )
			return new WP_Error( 'http_failure', __( 'There are no HTTP transports available which can complete the requested request.' ) );

		// Transport claims to support request, instantiate it and give it a whirl.
		if ( empty( $transports[$class] ) )
			$transports[$class] = new $class;

		$response = $transports[$class]->request( $url, $args );

		/** This action is documented in wp-includes/class-http.php */
		do_action( 'http_api_debug', $response, 'response', $class, $args, $url );

		if ( is_wp_error( $response ) )
			return $response;

		/**
		 * Filters the HTTP API response immediately before the response is returned.
		 *
		 * @since 2.9.0
		 *
		 * @param array  $response HTTP response.
		 * @param array  $args     HTTP request arguments.
		 * @param string $url      The request URL.
		 */
		return apply_filters( 'http_response', $response, $args, $url );
	}

	/**
	 * Uses the POST HTTP method.
	 *
	 * Used for sending data that is expected to be in the body.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @param string       $url  The request URL.
	 * @param string|array $args Optional. Override the defaults.
	 * @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'. A WP_Error instance upon error
	 */
	public function post($url, $args = array()) {
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
	 * @since 2.7.0
	 *
	 * @param string $url The request URL.
	 * @param string|array $args Optional. Override the defaults.
	 * @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'. A WP_Error instance upon error
	 */
	public function get($url, $args = array()) {
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
	 * @since 2.7.0
	 *
	 * @param string $url The request URL.
	 * @param string|array $args Optional. Override the defaults.
	 * @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'. A WP_Error instance upon error
	 */
	public function head($url, $args = array()) {
		$defaults = array('method' => 'HEAD');
		$r = wp_parse_args( $args, $defaults );
		return $this->request($url, $r);
	}

	/**
	 * Parses the responses and splits the parts into headers and body.
	 *
	 * @access public
	 * @static
	 * @since 2.7.0
	 *
	 * @param string $strResponse The full response string
	 * @return array Array with 'headers' and 'body' keys.
	 */
	public static function processResponse($strResponse) {
		$res = explode("\r\n\r\n", $strResponse, 2);

		return array('headers' => $res[0], 'body' => isset($res[1]) ? $res[1] : '');
	}

	/**
	 * Transform header string into an array.
	 *
	 * If an array is given then it is assumed to be raw header data with numeric keys with the
	 * headers as the values. No headers must be passed that were already processed.
	 *
	 * @access public
	 * @static
	 * @since 2.7.0
	 *
	 * @param string|array $headers
	 * @param string $url The URL that was requested
	 * @return array Processed string headers. If duplicate headers are encountered,
	 * 					Then a numbered array is returned as the value of that header-key.
	 */
	public static function processHeaders( $headers, $url = '' ) {
		// Split headers, one per array element.
		if ( is_string($headers) ) {
			// Tolerate line terminator: CRLF = LF (RFC 2616 19.3).
			$headers = str_replace("\r\n", "\n", $headers);
			/*
			 * Unfold folded header fields. LWS = [CRLF] 1*( SP | HT ) <US-ASCII SP, space (32)>,
			 * <US-ASCII HT, horizontal-tab (9)> (RFC 2616 2.2).
			 */
			$headers = preg_replace('/\n[ \t]/', ' ', $headers);
			// Create the headers array.
			$headers = explode("\n", $headers);
		}

		$response = array('code' => 0, 'message' => '');

		/*
		 * If a redirection has taken place, The headers for each page request may have been passed.
		 * In this case, determine the final HTTP header and parse from there.
		 */
		for ( $i = count($headers)-1; $i >= 0; $i-- ) {
			if ( !empty($headers[$i]) && false === strpos($headers[$i], ':') ) {
				$headers = array_splice($headers, $i);
				break;
			}
		}

		$cookies = array();
		$newheaders = array();
		foreach ( (array) $headers as $tempheader ) {
			if ( empty($tempheader) )
				continue;

			if ( false === strpos($tempheader, ':') ) {
				$stack = explode(' ', $tempheader, 3);
				$stack[] = '';
				list( , $response['code'], $response['message']) = $stack;
				continue;
			}

			list($key, $value) = explode(':', $tempheader, 2);

			$key = strtolower( $key );
			$value = trim( $value );

			if ( isset( $newheaders[ $key ] ) ) {
				if ( ! is_array( $newheaders[ $key ] ) )
					$newheaders[$key] = array( $newheaders[ $key ] );
				$newheaders[ $key ][] = $value;
			} else {
				$newheaders[ $key ] = $value;
			}
			if ( 'set-cookie' == $key )
				$cookies[] = new WP_Http_Cookie( $value, $url );
		}

		// Cast the Response Code to an int
		$response['code'] = intval( $response['code'] );

		return array('response' => $response, 'headers' => $newheaders, 'cookies' => $cookies);
	}

	/**
	 * Takes the arguments for a ::request() and checks for the cookie array.
	 *
	 * If it's found, then it upgrades any basic name => value pairs to WP_Http_Cookie instances,
	 * which are each parsed into strings and added to the Cookie: header (within the arguments array).
	 * Edits the array by reference.
	 *
	 * @access public
	 * @version 2.8.0
	 * @static
	 *
	 * @param array $r Full array of args passed into ::request()
	 */
	public static function buildCookieHeader( &$r ) {
		if ( ! empty($r['cookies']) ) {
			// Upgrade any name => value cookie pairs to WP_HTTP_Cookie instances.
			foreach ( $r['cookies'] as $name => $value ) {
				if ( ! is_object( $value ) )
					$r['cookies'][ $name ] = new WP_Http_Cookie( array( 'name' => $name, 'value' => $value ) );
			}

			$cookies_header = '';
			foreach ( (array) $r['cookies'] as $cookie ) {
				$cookies_header .= $cookie->getHeaderValue() . '; ';
			}

			$cookies_header = substr( $cookies_header, 0, -2 );
			$r['headers']['cookie'] = $cookies_header;
		}
	}

	/**
	 * Decodes chunk transfer-encoding, based off the HTTP 1.1 specification.
	 *
	 * Based off the HTTP http_encoding_dechunk function.
	 *
	 * @link https://tools.ietf.org/html/rfc2616#section-19.4.6 Process for chunked decoding.
	 *
	 * @access public
	 * @since 2.7.0
	 * @static
	 *
	 * @param string $body Body content
	 * @return string Chunked decoded body on success or raw body on failure.
	 */
	public static function chunkTransferDecode( $body ) {
		// The body is not chunked encoded or is malformed.
		if ( ! preg_match( '/^([0-9a-f]+)[^\r\n]*\r\n/i', trim( $body ) ) )
			return $body;

		$parsed_body = '';

		// We'll be altering $body, so need a backup in case of error.
		$body_original = $body;

		while ( true ) {
			$has_chunk = (bool) preg_match( '/^([0-9a-f]+)[^\r\n]*\r\n/i', $body, $match );
			if ( ! $has_chunk || empty( $match[1] ) )
				return $body_original;

			$length = hexdec( $match[1] );
			$chunk_length = strlen( $match[0] );

			// Parse out the chunk of data.
			$parsed_body .= substr( $body, $chunk_length, $length );

			// Remove the chunk from the raw data.
			$body = substr( $body, $length + $chunk_length );

			// End of the document.
			if ( '0' === trim( $body ) )
				return $parsed_body;
		}
	}

	/**
	 * Block requests through the proxy.
	 *
	 * Those who are behind a proxy and want to prevent access to certain hosts may do so. This will
	 * prevent plugins from working and core functionality, if you don't include api.wordpress.org.
	 *
	 * You block external URL requests by defining WP_HTTP_BLOCK_EXTERNAL as true in your wp-config.php
	 * file and this will only allow localhost and your site to make requests. The constant
	 * WP_ACCESSIBLE_HOSTS will allow additional hosts to go through for requests. The format of the
	 * WP_ACCESSIBLE_HOSTS constant is a comma separated list of hostnames to allow, wildcard domains
	 * are supported, eg *.wordpress.org will allow for all subdomains of wordpress.org to be contacted.
	 *
	 * @since 2.8.0
	 * @link https://core.trac.wordpress.org/ticket/8927 Allow preventing external requests.
	 * @link https://core.trac.wordpress.org/ticket/14636 Allow wildcard domains in WP_ACCESSIBLE_HOSTS
	 *
	 * @staticvar array|null $accessible_hosts
	 * @staticvar array      $wildcard_regex
	 *
	 * @param string $uri URI of url.
	 * @return bool True to block, false to allow.
	 */
	public function block_request($uri) {
		// We don't need to block requests, because nothing is blocked.
		if ( ! defined( 'WP_HTTP_BLOCK_EXTERNAL' ) || ! WP_HTTP_BLOCK_EXTERNAL )
			return false;

		$check = parse_url($uri);
		if ( ! $check )
			return true;

		$home = parse_url( get_option('siteurl') );

		// Don't block requests back to ourselves by default.
		if ( 'localhost' == $check['host'] || ( isset( $home['host'] ) && $home['host'] == $check['host'] ) ) {
			/**
			 * Filters whether to block local requests through the proxy.
			 *
			 * @since 2.8.0
			 *
			 * @param bool $block Whether to block local requests through proxy.
			 *                    Default false.
			 */
			return apply_filters( 'block_local_requests', false );
		}

		if ( !defined('WP_ACCESSIBLE_HOSTS') )
			return true;

		static $accessible_hosts = null;
		static $wildcard_regex = array();
		if ( null === $accessible_hosts ) {
			$accessible_hosts = preg_split('|,\s*|', WP_ACCESSIBLE_HOSTS);

			if ( false !== strpos(WP_ACCESSIBLE_HOSTS, '*') ) {
				$wildcard_regex = array();
				foreach ( $accessible_hosts as $host )
					$wildcard_regex[] = str_replace( '\*', '.+', preg_quote( $host, '/' ) );
				$wildcard_regex = '/^(' . implode('|', $wildcard_regex) . ')$/i';
			}
		}

		if ( !empty($wildcard_regex) )
			return !preg_match($wildcard_regex, $check['host']);
		else
			return !in_array( $check['host'], $accessible_hosts ); //Inverse logic, If it's in the array, then we can't access it.

	}

	/**
	 * Used as a wrapper for PHP's parse_url() function that handles edgecases in < PHP 5.4.7.
	 *
	 * @access protected
	 * @deprecated 4.4.0 Use wp_parse_url()
	 * @see wp_parse_url()
	 *
	 * @param string $url The URL to parse.
	 * @return bool|array False on failure; Array of URL components on success;
	 *                    See parse_url()'s return values.
	 */
	protected static function parse_url( $url ) {
		_deprecated_function( __METHOD__, '4.4.0', 'wp_parse_url()' );
		return wp_parse_url( $url );
	}

	/**
	 * Converts a relative URL to an absolute URL relative to a given URL.
	 *
	 * If an Absolute URL is provided, no processing of that URL is done.
	 *
	 * @since 3.4.0
	 *
	 * @static
	 * @access public
	 *
	 * @param string $maybe_relative_path The URL which might be relative
	 * @param string $url                 The URL which $maybe_relative_path is relative to
	 * @return string An Absolute URL, in a failure condition where the URL cannot be parsed, the relative URL will be returned.
	 */
	public static function make_absolute_url( $maybe_relative_path, $url ) {
		if ( empty( $url ) )
			return $maybe_relative_path;

		if ( ! $url_parts = wp_parse_url( $url ) ) {
			return $maybe_relative_path;
		}

		if ( ! $relative_url_parts = wp_parse_url( $maybe_relative_path ) ) {
			return $maybe_relative_path;
		}

		// Check for a scheme on the 'relative' url
		if ( ! empty( $relative_url_parts['scheme'] ) ) {
			return $maybe_relative_path;
		}

		$absolute_path = $url_parts['scheme'] . '://';

		// Schemeless URL's will make it this far, so we check for a host in the relative url and convert it to a protocol-url
		if ( isset( $relative_url_parts['host'] ) ) {
			$absolute_path .= $relative_url_parts['host'];
			if ( isset( $relative_url_parts['port'] ) )
				$absolute_path .= ':' . $relative_url_parts['port'];
		} else {
			$absolute_path .= $url_parts['host'];
			if ( isset( $url_parts['port'] ) )
				$absolute_path .= ':' . $url_parts['port'];
		}

		// Start off with the Absolute URL path.
		$path = ! empty( $url_parts['path'] ) ? $url_parts['path'] : '/';

		// If it's a root-relative path, then great.
		if ( ! empty( $relative_url_parts['path'] ) && '/' == $relative_url_parts['path'][0] ) {
			$path = $relative_url_parts['path'];

		// Else it's a relative path.
		} elseif ( ! empty( $relative_url_parts['path'] ) ) {
			// Strip off any file components from the absolute path.
			$path = substr( $path, 0, strrpos( $path, '/' ) + 1 );

			// Build the new path.
			$path .= $relative_url_parts['path'];

			// Strip all /path/../ out of the path.
			while ( strpos( $path, '../' ) > 1 ) {
				$path = preg_replace( '![^/]+/\.\./!', '', $path );
			}

			// Strip any final leading ../ from the path.
			$path = preg_replace( '!^/(\.\./)+!', '', $path );
		}

		// Add the Query string.
		if ( ! empty( $relative_url_parts['query'] ) )
			$path .= '?' . $relative_url_parts['query'];

		return $absolute_path . '/' . ltrim( $path, '/' );
	}

	/**
	 * Handles HTTP Redirects and follows them if appropriate.
	 *
	 * @since 3.7.0
	 *
	 * @static
	 *
	 * @param string $url The URL which was requested.
	 * @param array $args The Arguments which were used to make the request.
	 * @param array $response The Response of the HTTP request.
	 * @return false|object False if no redirect is present, a WP_HTTP or WP_Error result otherwise.
	 */
	public static function handle_redirects( $url, $args, $response ) {
		// If no redirects are present, or, redirects were not requested, perform no action.
		if ( ! isset( $response['headers']['location'] ) || 0 === $args['_redirection'] )
			return false;

		// Only perform redirections on redirection http codes.
		if ( $response['response']['code'] > 399 || $response['response']['code'] < 300 )
			return false;

		// Don't redirect if we've run out of redirects.
		if ( $args['redirection']-- <= 0 )
			return new WP_Error( 'http_request_failed', __('Too many redirects.') );

		$redirect_location = $response['headers']['location'];

		// If there were multiple Location headers, use the last header specified.
		if ( is_array( $redirect_location ) )
			$redirect_location = array_pop( $redirect_location );

		$redirect_location = WP_Http::make_absolute_url( $redirect_location, $url );

		// POST requests should not POST to a redirected location.
		if ( 'POST' == $args['method'] ) {
			if ( in_array( $response['response']['code'], array( 302, 303 ) ) )
				$args['method'] = 'GET';
		}

		// Include valid cookies in the redirect process.
		if ( ! empty( $response['cookies'] ) ) {
			foreach ( $response['cookies'] as $cookie ) {
				if ( $cookie->test( $redirect_location ) )
					$args['cookies'][] = $cookie;
			}
		}

		return wp_remote_request( $redirect_location, $args );
	}

	/**
	 * Determines if a specified string represents an IP address or not.
	 *
	 * This function also detects the type of the IP address, returning either
	 * '4' or '6' to represent a IPv4 and IPv6 address respectively.
	 * This does not verify if the IP is a valid IP, only that it appears to be
	 * an IP address.
	 *
	 * @link http://home.deds.nl/~aeron/regex/ for IPv6 regex
	 *
	 * @since 3.7.0
	 * @static
	 *
	 * @param string $maybe_ip A suspected IP address
	 * @return integer|bool Upon success, '4' or '6' to represent a IPv4 or IPv6 address, false upon failure
	 */
	public static function is_ip_address( $maybe_ip ) {
		if ( preg_match( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $maybe_ip ) )
			return 4;

		if ( false !== strpos( $maybe_ip, ':' ) && preg_match( '/^(((?=.*(::))(?!.*\3.+\3))\3?|([\dA-F]{1,4}(\3|:\b|$)|\2))(?4){5}((?4){2}|(((2[0-4]|1\d|[1-9])?\d|25[0-5])\.?\b){4})$/i', trim( $maybe_ip, ' []' ) ) )
			return 6;

		return false;
	}

}
