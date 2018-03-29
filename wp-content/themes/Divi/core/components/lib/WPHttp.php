<?php

require_once ABSPATH . WPINC . '/class-http.php';

/**
 * Some 3rd-party APIs require data to be sent in the request body for
 * GET requests (eg. SendinBlue). This is not currently possible using the WP
 * HTTP API. I've submitted a patch to WP Core for this. Until its merged, we
 * have to extend the WP_HTTP class and override the method in question.
 *
 * @see https://core.trac.wordpress.org/ticket/39043
 *
 * @private
 */
class ET_Core_LIB_WPHttp extends WP_Http {
	/**
	 * Send an HTTP request to a URI.
	 *
	 * Please note: The only URI that are supported in the HTTP Transport implementation
	 * are the HTTP and HTTPS protocols.
	 *
	 * @access public
	 * @since 2.7.0
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
	 *                                             Default WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ).
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
	 *                                             given, the stream will be output to a new file in the WP temp dir
	 *                                             using a name generated from the basename of the URL. Default false.
	 *     @type string       $filename            Filename of the file to write to when streaming. $stream must be
	 *                                             set to true. Default null.
	 *     @type int          $limit_response_size Size in bytes to limit the response to. Default null.
	 *     @type bool|null    $data_format         How the `$data` should be sent ('query' or 'body'). Default null.
	 *                                             If null, data will be sent as 'query' for HEAD/GET and as
	 *                                             'body' for POST/PUT/OPTIONS/PATCH/DELETE.
	 *
	 * }
	 * @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'.
	 *                        A WP_Error instance upon error.
	 */
	public function request( $url, $args = array() ) {
		$defaults = array(
			'method'              => 'GET',
			/**
			 * Filters the timeout value for an HTTP request.
			 *
			 * @since 2.7.0
			 *
			 * @param int $timeout_value Time in seconds until a request times out.
			 *                           Default 5.
			 */
			'timeout'             => apply_filters( 'http_request_timeout', 5 ),
			/**
			 * Filters the number of redirects allowed during an HTTP request.
			 *
			 * @since 2.7.0
			 *
			 * @param int $redirect_count Number of redirects allowed. Default 5.
			 */
			'redirection'         => apply_filters( 'http_request_redirection_count', 5 ),
			/**
			 * Filters the version of the HTTP protocol used in a request.
			 *
			 * @since 2.7.0
			 *
			 * @param string $version Version of HTTP used. Accepts '1.0' and '1.1'.
			 *                        Default '1.0'.
			 */
			'httpversion'         => apply_filters( 'http_request_version', '1.0' ),
			/**
			 * Filters the user agent value sent with an HTTP request.
			 *
			 * @since 2.7.0
			 *
			 * @param string $user_agent WordPress user agent string.
			 */
			'user-agent'          => apply_filters( 'http_headers_useragent', 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ) ),
			/**
			 * Filters whether to pass URLs through wp_http_validate_url() in an HTTP request.
			 *
			 * @since 3.6.0
			 *
			 * @param bool $pass_url Whether to pass URLs through wp_http_validate_url().
			 *                       Default false.
			 */
			'reject_unsafe_urls'  => apply_filters( 'http_request_reject_unsafe_urls', false ),
			'blocking'            => true,
			'headers'             => array(),
			'cookies'             => array(),
			'body'                => null,
			'compress'            => false,
			'decompress'          => true,
			'sslverify'           => true,
			'sslcertificates'     => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
			'stream'              => false,
			'filename'            => null,
			'limit_response_size' => null,
			'data_format'         => null,
		);

		// Pre-parse for the HEAD checks.
		$args = wp_parse_args( $args );

		// By default, Head requests do not cause redirections.
		if ( isset( $args['method'] ) && 'HEAD' == $args['method'] ) {
			$defaults['redirection'] = 0;
		}

		$request_args = wp_parse_args( $args, $defaults );
		/**
		 * Filters the arguments used in an HTTP request.
		 *
		 * @since 2.7.0
		 *
		 * @param array  $request_args An array of HTTP request arguments.
		 * @param string $url          The request URL.
		 */
		$request_args = apply_filters( 'http_request_args', $request_args, $url );

		// The transports decrement this, store a copy of the original value for loop purposes.
		if ( ! isset( $request_args['_redirection'] ) ) {
			$request_args['_redirection'] = $request_args['redirection'];
		}

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
		 * @param array                $request_args       HTTP request arguments.
		 * @param string               $url     The request URL.
		 */
		$pre = apply_filters( 'pre_http_request', false, $request_args, $url );

		if ( false !== $pre ) {
			return $pre;
		}

		if ( function_exists( 'wp_kses_bad_protocol' ) ) {
			if ( $request_args['reject_unsafe_urls'] ) {
				$url = wp_http_validate_url( $url );
			}
			if ( $url ) {
				$url = wp_kses_bad_protocol( $url, array( 'http', 'https', 'ssl' ) );
			}
		}

		$arrURL = @parse_url( $url );

		if ( empty( $url ) || empty( $arrURL['scheme'] ) ) {
			return new WP_Error( 'http_request_failed', esc_html__( 'A valid URL was not provided.' ) );
		}

		if ( $this->block_request( $url ) ) {
			return new WP_Error( 'http_request_failed', esc_html__( 'User has blocked requests through HTTP.' ) );
		}

		// If we are streaming to a file but no filename was given drop it in the WP temp dir
		// and pick its name using the basename of the $url
		if ( $request_args['stream'] ) {
			if ( empty( $request_args['filename'] ) ) {
				$request_args['filename'] = get_temp_dir() . basename( $url );
			}

			// Force some settings if we are streaming to a file and check for existence and perms of destination directory
			$request_args['blocking'] = true;
			if ( ! wp_is_writable( dirname( $request_args['filename'] ) ) ) {
				return new WP_Error( 'http_request_failed', esc_html__( 'Destination directory for file streaming does not exist or is not writable.' ) );
			}
		}

		if ( is_null( $request_args['headers'] ) ) {
			$request_args['headers'] = array();
		}

		// WP allows passing in headers as a string, weirdly.
		if ( ! is_array( $request_args['headers'] ) ) {
			$processedHeaders = WP_Http::processHeaders( $request_args['headers'] );
			$request_args['headers']     = $processedHeaders['headers'];
		}

		// Setup arguments
		$headers = $request_args['headers'];
		$data    = $request_args['body'];
		$type    = $request_args['method'];
		$options = array(
			'timeout'   => $request_args['timeout'],
			'useragent' => $request_args['user-agent'],
			'blocking'  => $request_args['blocking'],
			'hooks'     => new WP_HTTP_Requests_Hooks( $url, $request_args ),
		);

		// Ensure redirects follow browser behaviour.
		$options['hooks']->register( 'requests.before_redirect', array(
			get_class(),
			'browser_redirect_compatibility'
		) );

		if ( $request_args['stream'] ) {
			$options['filename'] = $request_args['filename'];
		}
		if ( empty( $request_args['redirection'] ) ) {
			$options['follow_redirects'] = false;
		} else {
			$options['redirects'] = $request_args['redirection'];
		}

		// Use byte limit, if we can
		if ( isset( $request_args['limit_response_size'] ) ) {
			$options['max_bytes'] = $request_args['limit_response_size'];
		}

		// If we've got cookies, use and convert them to Requests_Cookie.
		if ( ! empty( $request_args['cookies'] ) ) {
			$options['cookies'] = WP_Http::normalize_cookies( $request_args['cookies'] );
		}

		// SSL certificate handling
		if ( ! $request_args['sslverify'] ) {
			$options['verify']     = false;
			$options['verifyname'] = false;
		} else {
			$options['verify'] = $request_args['sslcertificates'];
		}

		if ( null !== $request_args['data_format'] ) {
			$options['data_format'] = $request_args['data_format'];

		} elseif ( 'HEAD' !== $type && 'GET' !== $type ) {
			// All non-GET/HEAD requests should put the arguments in the form body.
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
				$options['proxy']->user               = $proxy->username();
				$options['proxy']->pass               = $proxy->password();
			}
		}

		// Avoid issues where mbstring.func_overload is enabled
		mbstring_binary_safe_encoding();

		try {
			$requests_response = Requests::request( $url, $headers, $data, $type, $options );

			// Convert the response into an array
			$http_response = new WP_HTTP_Requests_Response( $requests_response, $request_args['filename'] );
			$response      = $http_response->to_array();

			// Add the original object to the array.
			$response['http_response'] = $http_response;
		} catch ( Requests_Exception $e ) {
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
		do_action( 'http_api_debug', $response, 'response', 'Requests', $request_args, $url );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! $request_args['blocking'] ) {
			return array(
				'headers'       => array(),
				'body'          => '',
				'response'      => array(
					'code'    => false,
					'message' => false,
				),
				'cookies'       => array(),
				'http_response' => null,
			);
		}

		/**
		 * Filters the HTTP API response immediately before the response is returned.
		 *
		 * @since 2.9.0
		 *
		 * @param array  $response HTTP response.
		 * @param array  $request_args        HTTP request arguments.
		 * @param string $url      The request URL.
		 */
		return apply_filters( 'http_response', $response, $request_args, $url );
	}
}
