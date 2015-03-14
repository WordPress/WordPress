<?php
/**
 * Simple and uniform HTTP request API.
 *
 * Standardizes the HTTP requests for WordPress. Handles cookies, gzip encoding and decoding, chunk
 * decoding, if HTTP 1.1 and various other difficult HTTP protocol implementations.
 *
 * @link https://core.trac.wordpress.org/ticket/4779 HTTP API Proposal
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7.0
 */

/**
 * WordPress HTTP Class for managing HTTP Transports and making HTTP requests.
 *
 * This class is used to consistently make outgoing HTTP requests easy for developers
 * while still being compatible with the many PHP configurations under which
 * WordPress runs.
 *
 * Debugging includes several actions, which pass different variables for debugging the HTTP API.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7.0
 */
class WP_Http {

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
	 *                                             Default WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ).
	 *     @type bool         $reject_unsafe_urls  Whether to pass URLs through {@see wp_http_validate_url()}.
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
			 * Filter the timeout value for an HTTP request.
			 *
			 * @since 2.7.0
			 *
			 * @param int $timeout_value Time in seconds until a request times out.
			 *                           Default 5.
			 */
			'timeout' => apply_filters( 'http_request_timeout', 5 ),
			/**
			 * Filter the number of redirects allowed during an HTTP request.
			 *
			 * @since 2.7.0
			 *
			 * @param int $redirect_count Number of redirects allowed. Default 5.
			 */
			'redirection' => apply_filters( 'http_request_redirection_count', 5 ),
			/**
			 * Filter the version of the HTTP protocol used in a request.
			 *
			 * @since 2.7.0
			 *
			 * @param string $version Version of HTTP used. Accepts '1.0' and '1.1'.
			 *                        Default '1.0'.
			 */
			'httpversion' => apply_filters( 'http_request_version', '1.0' ),
			/**
			 * Filter the user agent value sent with an HTTP request.
			 *
			 * @since 2.7.0
			 *
			 * @param string $user_agent WordPress user agent string.
			 */
			'user-agent' => apply_filters( 'http_headers_useragent', 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) ),
			/**
			 * Filter whether to pass URLs through wp_http_validate_url() in an HTTP request.
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
		 * Filter the arguments used in an HTTP request.
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
		 * Filter whether to preempt an HTTP request's return.
		 *
		 * Returning a truthy value to the filter will short-circuit
		 * the HTTP request and return early with that value.
		 *
		 * @since 2.9.0
		 *
		 * @param bool   $preempt Whether to preempt an HTTP request return. Default false.
		 * @param array  $r       HTTP request arguments.
		 * @param string $url     The request URL.
		 */
		$pre = apply_filters( 'pre_http_request', false, $r, $url );
		if ( false !== $pre )
			return $pre;

		if ( function_exists( 'wp_kses_bad_protocol' ) ) {
			if ( $r['reject_unsafe_urls'] )
				$url = wp_http_validate_url( $url );
			if ( $url ) {
				$url = wp_kses_bad_protocol( $url, array( 'http', 'https', 'ssl' ) );
			}
		}

		$arrURL = @parse_url( $url );

		if ( empty( $url ) || empty( $arrURL['scheme'] ) )
			return new WP_Error('http_request_failed', __('A valid URL was not provided.'));

		if ( $this->block_request( $url ) )
			return new WP_Error( 'http_request_failed', __( 'User has blocked requests through HTTP.' ) );

		/*
		 * Determine if this is a https call and pass that on to the transport functions
		 * so that we can blacklist the transports that do not support ssl verification
		 */
		$r['ssl'] = $arrURL['scheme'] == 'https' || $arrURL['scheme'] == 'ssl';

		// Determine if this request is to OUR install of WordPress.
		$homeURL = parse_url( get_bloginfo( 'url' ) );
		$r['local'] = 'localhost' == $arrURL['host'] || ( isset( $homeURL['host'] ) && $homeURL['host'] == $arrURL['host'] );
		unset( $homeURL );

		/*
		 * If we are streaming to a file but no filename was given drop it in the WP temp dir
		 * and pick its name using the basename of the $url.
		 */
		if ( $r['stream']  && empty( $r['filename'] ) ) {
			$r['filename'] = wp_unique_filename( get_temp_dir(), basename( $url ) );
		}

		/*
		 * Force some settings if we are streaming to a file and check for existence and perms
		 * of destination directory.
		 */
		if ( $r['stream'] ) {
			$r['blocking'] = true;
			if ( ! wp_is_writable( dirname( $r['filename'] ) ) )
				return new WP_Error( 'http_request_failed', __( 'Destination directory for file streaming does not exist or is not writable.' ) );
		}

		if ( is_null( $r['headers'] ) )
			$r['headers'] = array();

		if ( ! is_array( $r['headers'] ) ) {
			$processedHeaders = self::processHeaders( $r['headers'], $url );
			$r['headers'] = $processedHeaders['headers'];
		}

		if ( isset( $r['headers']['User-Agent'] ) ) {
			$r['user-agent'] = $r['headers']['User-Agent'];
			unset( $r['headers']['User-Agent'] );
		}

		if ( isset( $r['headers']['user-agent'] ) ) {
			$r['user-agent'] = $r['headers']['user-agent'];
			unset( $r['headers']['user-agent'] );
		}

		if ( '1.1' == $r['httpversion'] && !isset( $r['headers']['connection'] ) ) {
			$r['headers']['connection'] = 'close';
		}

		// Construct Cookie: header if any cookies are set.
		self::buildCookieHeader( $r );

		// Avoid issues where mbstring.func_overload is enabled.
		mbstring_binary_safe_encoding();

		if ( ! isset( $r['headers']['Accept-Encoding'] ) ) {
			if ( $encoding = WP_Http_Encoding::accept_encoding( $url, $r ) )
				$r['headers']['Accept-Encoding'] = $encoding;
		}

		if ( ( ! is_null( $r['body'] ) && '' != $r['body'] ) || 'POST' == $r['method'] || 'PUT' == $r['method'] ) {
			if ( is_array( $r['body'] ) || is_object( $r['body'] ) ) {
				$r['body'] = http_build_query( $r['body'], null, '&' );

				if ( ! isset( $r['headers']['Content-Type'] ) )
					$r['headers']['Content-Type'] = 'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' );
			}

			if ( '' === $r['body'] )
				$r['body'] = null;

			if ( ! isset( $r['headers']['Content-Length'] ) && ! isset( $r['headers']['content-length'] ) )
				$r['headers']['Content-Length'] = strlen( $r['body'] );
		}

		$response = $this->_dispatch_request( $url, $r );

		reset_mbstring_encoding();

		if ( is_wp_error( $response ) )
			return $response;

		// Append cookies that were used in this request to the response
		if ( ! empty( $r['cookies'] ) ) {
			$cookies_set = wp_list_pluck( $response['cookies'], 'name' );
			foreach ( $r['cookies'] as $cookie ) {
				if ( ! in_array( $cookie->name, $cookies_set ) && $cookie->test( $url ) ) {
					$response['cookies'][] = $cookie;
				}
			}
		}

		return $response;
	}

	/**
	 * Tests which transports are capable of supporting the request.
	 *
	 * @since 3.2.0
	 * @access private
	 *
	 * @param array $args Request arguments
	 * @param string $url URL to Request
	 *
	 * @return string|false Class name for the first transport that claims to support the request. False if no transport claims to support the request.
	 */
	public function _get_first_available_transport( $args, $url = null ) {
		/**
		 * Filter which HTTP transports are available and in what order.
		 *
		 * @since 3.7.0
		 *
		 * @param array  $value Array of HTTP transports to check. Default array contains
		 *                      'curl', and 'streams', in that order.
		 * @param array  $args  HTTP request arguments.
		 * @param string $url   The URL to request.
		 */
		$request_order = apply_filters( 'http_api_transports', array( 'curl', 'streams' ), $args, $url );

		// Loop over each transport on each HTTP request looking for one which will serve this request's needs.
		foreach ( $request_order as $transport ) {
			$class = 'WP_HTTP_' . $transport;

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
		do_action( 'http_api_debug', $response, 'response', $class, $args, $url );

		if ( is_wp_error( $response ) )
			return $response;

		/**
		 * Filter the HTTP API response immediately before the response is returned.
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
					$r['cookies'][ $name ] = new WP_HTTP_Cookie( array( 'name' => $name, 'value' => $value ) );
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
	 * @link http://tools.ietf.org/html/rfc2616#section-19.4.6 Process for chunked decoding.
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
	 * file and this will only allow localhost and your blog to make requests. The constant
	 * WP_ACCESSIBLE_HOSTS will allow additional hosts to go through for requests. The format of the
	 * WP_ACCESSIBLE_HOSTS constant is a comma separated list of hostnames to allow, wildcard domains
	 * are supported, eg *.wordpress.org will allow for all subdomains of wordpress.org to be contacted.
	 *
	 * @since 2.8.0
	 * @link https://core.trac.wordpress.org/ticket/8927 Allow preventing external requests.
	 * @link https://core.trac.wordpress.org/ticket/14636 Allow wildcard domains in WP_ACCESSIBLE_HOSTS
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
			 * Filter whether to block local requests through the proxy.
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

		static $accessible_hosts;
		static $wildcard_regex = false;
		if ( null == $accessible_hosts ) {
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
	 * A wrapper for PHP's parse_url() function that handles edgecases in < PHP 5.4.7
	 *
	 * PHP 5.4.7 expanded parse_url()'s ability to handle non-absolute url's, including
	 * schemeless and relative url's with :// in the path, this works around those
	 * limitations providing a standard output on PHP 5.2~5.4+.
	 *
	 * Error suppression is used as prior to PHP 5.3.3, an E_WARNING would be generated
	 * when URL parsing failed.
	 *
	 * @since 4.1.0
	 * @access protected
	 *
	 * @param string $url The URL to parse.
	 * @return bool|array False on failure; Array of URL components on success;
	 *                    See parse_url()'s return values.
	 */
	protected static function parse_url( $url ) {
		$parts = @parse_url( $url );
		if ( ! $parts ) {
			// < PHP 5.4.7 compat, trouble with relative paths including a scheme break in the path
			if ( '/' == $url[0] && false !== strpos( $url, '://' ) ) {
				// Since we know it's a relative path, prefix with a scheme/host placeholder and try again
				if ( ! $parts = @parse_url( 'placeholder://placeholder' . $url ) ) {
					return $parts;
				}
				// Remove the placeholder values
				unset( $parts['scheme'], $parts['host'] );
			} else {
				return $parts;
			}
		}

		// < PHP 5.4.7 compat, doesn't detect schemeless URL's host field
		if ( '//' == substr( $url, 0, 2 ) && ! isset( $parts['host'] ) ) {
			list( $parts['host'], $slashless_path ) = explode( '/', substr( $parts['path'], 2 ), 2 );
			$parts['path'] = "/{$slashless_path}";
		}

		return $parts;
	}

	/**
	 * Converts a relative URL to an absolute URL relative to a given URL.
	 *
	 * If an Absolute URL is provided, no processing of that URL is done.
	 *
	 * @since 3.4.0
	 *
	 * @access public
	 * @param string $maybe_relative_path The URL which might be relative
	 * @param string $url                 The URL which $maybe_relative_path is relative to
	 * @return string An Absolute URL, in a failure condition where the URL cannot be parsed, the relative URL will be returned.
	 */
	public static function make_absolute_url( $maybe_relative_path, $url ) {
		if ( empty( $url ) )
			return $maybe_relative_path;

		if ( ! $url_parts = WP_HTTP::parse_url( $url ) ) {
			return $maybe_relative_path;
		}

		if ( ! $relative_url_parts = WP_HTTP::parse_url( $maybe_relative_path ) ) {
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

		$redirect_location = WP_HTTP::make_absolute_url( $redirect_location, $url );

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
	 * @see http://home.deds.nl/~aeron/regex/ for IPv6 regex
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

/**
 * HTTP request method uses PHP Streams to retrieve the url.
 *
 * @since 2.7.0
 * @since 3.7.0 Combined with the fsockopen transport and switched to stream_socket_client().
 */
class WP_Http_Streams {
	/**
	 * Send a HTTP request to a URI using PHP Streams.
	 *
	 * @see WP_Http::request For default options descriptions.
	 *
	 * @since 2.7.0
	 * @since 3.7.0 Combined with the fsockopen transport and switched to stream_socket_client().
	 *
	 * @access public
	 * @param string $url The request URL.
	 * @param string|array $args Optional. Override the defaults.
	 * @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'. A WP_Error instance upon error
	 */
	public function request($url, $args = array()) {
		$defaults = array(
			'method' => 'GET', 'timeout' => 5,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(), 'body' => null, 'cookies' => array()
		);

		$r = wp_parse_args( $args, $defaults );

		if ( isset( $r['headers']['User-Agent'] ) ) {
			$r['user-agent'] = $r['headers']['User-Agent'];
			unset( $r['headers']['User-Agent'] );
		} elseif ( isset( $r['headers']['user-agent'] ) ) {
			$r['user-agent'] = $r['headers']['user-agent'];
			unset( $r['headers']['user-agent'] );
		}

		// Construct Cookie: header if any cookies are set.
		WP_Http::buildCookieHeader( $r );

		$arrURL = parse_url($url);

		$connect_host = $arrURL['host'];

		$secure_transport = ( $arrURL['scheme'] == 'ssl' || $arrURL['scheme'] == 'https' );
		if ( ! isset( $arrURL['port'] ) ) {
			if ( $arrURL['scheme'] == 'ssl' || $arrURL['scheme'] == 'https' ) {
				$arrURL['port'] = 443;
				$secure_transport = true;
			} else {
				$arrURL['port'] = 80;
			}
		}

		// Always pass a Path, defaulting to the root in cases such as http://example.com
		if ( ! isset( $arrURL['path'] ) ) {
			$arrURL['path'] = '/';
		}

		if ( isset( $r['headers']['Host'] ) || isset( $r['headers']['host'] ) ) {
			if ( isset( $r['headers']['Host'] ) )
				$arrURL['host'] = $r['headers']['Host'];
			else
				$arrURL['host'] = $r['headers']['host'];
			unset( $r['headers']['Host'], $r['headers']['host'] );
		}

		/*
		 * Certain versions of PHP have issues with 'localhost' and IPv6, It attempts to connect
		 * to ::1, which fails when the server is not set up for it. For compatibility, always
		 * connect to the IPv4 address.
		 */
		if ( 'localhost' == strtolower( $connect_host ) )
			$connect_host = '127.0.0.1';

		$connect_host = $secure_transport ? 'ssl://' . $connect_host : 'tcp://' . $connect_host;

		$is_local = isset( $r['local'] ) && $r['local'];
		$ssl_verify = isset( $r['sslverify'] ) && $r['sslverify'];
		if ( $is_local ) {
			/**
			 * Filter whether SSL should be verified for local requests.
			 *
			 * @since 2.8.0
			 *
			 * @param bool $ssl_verify Whether to verify the SSL connection. Default true.
			 */
			$ssl_verify = apply_filters( 'https_local_ssl_verify', $ssl_verify );
		} elseif ( ! $is_local ) {
			/**
			 * Filter whether SSL should be verified for non-local requests.
			 *
			 * @since 2.8.0
			 *
			 * @param bool $ssl_verify Whether to verify the SSL connection. Default true.
			 */
			$ssl_verify = apply_filters( 'https_ssl_verify', $ssl_verify );
		}

		$proxy = new WP_HTTP_Proxy();

		$context = stream_context_create( array(
			'ssl' => array(
				'verify_peer' => $ssl_verify,
				//'CN_match' => $arrURL['host'], // This is handled by self::verify_ssl_certificate()
				'capture_peer_cert' => $ssl_verify,
				'SNI_enabled' => true,
				'cafile' => $r['sslcertificates'],
				'allow_self_signed' => ! $ssl_verify,
			)
		) );

		$timeout = (int) floor( $r['timeout'] );
		$utimeout = $timeout == $r['timeout'] ? 0 : 1000000 * $r['timeout'] % 1000000;
		$connect_timeout = max( $timeout, 1 );

		// Store error number.
		$connection_error = null;

		// Store error string.
		$connection_error_str = null;

		if ( !WP_DEBUG ) {
			// In the event that the SSL connection fails, silence the many PHP Warnings.
			if ( $secure_transport )
				$error_reporting = error_reporting(0);

			if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) )
				$handle = @stream_socket_client( 'tcp://' . $proxy->host() . ':' . $proxy->port(), $connection_error, $connection_error_str, $connect_timeout, STREAM_CLIENT_CONNECT, $context );
			else
				$handle = @stream_socket_client( $connect_host . ':' . $arrURL['port'], $connection_error, $connection_error_str, $connect_timeout, STREAM_CLIENT_CONNECT, $context );

			if ( $secure_transport )
				error_reporting( $error_reporting );

		} else {
			if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) )
				$handle = stream_socket_client( 'tcp://' . $proxy->host() . ':' . $proxy->port(), $connection_error, $connection_error_str, $connect_timeout, STREAM_CLIENT_CONNECT, $context );
			else
				$handle = stream_socket_client( $connect_host . ':' . $arrURL['port'], $connection_error, $connection_error_str, $connect_timeout, STREAM_CLIENT_CONNECT, $context );
		}

		if ( false === $handle ) {
			// SSL connection failed due to expired/invalid cert, or, OpenSSL configuration is broken.
			if ( $secure_transport && 0 === $connection_error && '' === $connection_error_str )
				return new WP_Error( 'http_request_failed', __( 'The SSL certificate for the host could not be verified.' ) );

			return new WP_Error('http_request_failed', $connection_error . ': ' . $connection_error_str );
		}

		// Verify that the SSL certificate is valid for this request.
		if ( $secure_transport && $ssl_verify && ! $proxy->is_enabled() ) {
			if ( ! self::verify_ssl_certificate( $handle, $arrURL['host'] ) )
				return new WP_Error( 'http_request_failed', __( 'The SSL certificate for the host could not be verified.' ) );
		}

		stream_set_timeout( $handle, $timeout, $utimeout );

		if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) //Some proxies require full URL in this field.
			$requestPath = $url;
		else
			$requestPath = $arrURL['path'] . ( isset($arrURL['query']) ? '?' . $arrURL['query'] : '' );

		$strHeaders = strtoupper($r['method']) . ' ' . $requestPath . ' HTTP/' . $r['httpversion'] . "\r\n";

		$include_port_in_host_header = (
			( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) ||
			( 'http'  == $arrURL['scheme'] && 80  != $arrURL['port'] ) ||
			( 'https' == $arrURL['scheme'] && 443 != $arrURL['port'] )
		);

		if ( $include_port_in_host_header ) {
			$strHeaders .= 'Host: ' . $arrURL['host'] . ':' . $arrURL['port'] . "\r\n";
		} else {
			$strHeaders .= 'Host: ' . $arrURL['host'] . "\r\n";
		}

		if ( isset($r['user-agent']) )
			$strHeaders .= 'User-agent: ' . $r['user-agent'] . "\r\n";

		if ( is_array($r['headers']) ) {
			foreach ( (array) $r['headers'] as $header => $headerValue )
				$strHeaders .= $header . ': ' . $headerValue . "\r\n";
		} else {
			$strHeaders .= $r['headers'];
		}

		if ( $proxy->use_authentication() )
			$strHeaders .= $proxy->authentication_header() . "\r\n";

		$strHeaders .= "\r\n";

		if ( ! is_null($r['body']) )
			$strHeaders .= $r['body'];

		fwrite($handle, $strHeaders);

		if ( ! $r['blocking'] ) {
			stream_set_blocking( $handle, 0 );
			fclose( $handle );
			return array( 'headers' => array(), 'body' => '', 'response' => array('code' => false, 'message' => false), 'cookies' => array() );
		}

		$strResponse = '';
		$bodyStarted = false;
		$keep_reading = true;
		$block_size = 4096;
		if ( isset( $r['limit_response_size'] ) )
			$block_size = min( $block_size, $r['limit_response_size'] );

		// If streaming to a file setup the file handle.
		if ( $r['stream'] ) {
			if ( ! WP_DEBUG )
				$stream_handle = @fopen( $r['filename'], 'w+' );
			else
				$stream_handle = fopen( $r['filename'], 'w+' );
			if ( ! $stream_handle )
				return new WP_Error( 'http_request_failed', sprintf( __( 'Could not open handle for fopen() to %s' ), $r['filename'] ) );

			$bytes_written = 0;
			while ( ! feof($handle) && $keep_reading ) {
				$block = fread( $handle, $block_size );
				if ( ! $bodyStarted ) {
					$strResponse .= $block;
					if ( strpos( $strResponse, "\r\n\r\n" ) ) {
						$process = WP_Http::processResponse( $strResponse );
						$bodyStarted = true;
						$block = $process['body'];
						unset( $strResponse );
						$process['body'] = '';
					}
				}

				$this_block_size = strlen( $block );

				if ( isset( $r['limit_response_size'] ) && ( $bytes_written + $this_block_size ) > $r['limit_response_size'] ) {
					$this_block_size = ( $r['limit_response_size'] - $bytes_written );
					$block = substr( $block, 0, $this_block_size );
				}

				$bytes_written_to_file = fwrite( $stream_handle, $block );

				if ( $bytes_written_to_file != $this_block_size ) {
					fclose( $handle );
					fclose( $stream_handle );
					return new WP_Error( 'http_request_failed', __( 'Failed to write request to temporary file.' ) );
				}

				$bytes_written += $bytes_written_to_file;

				$keep_reading = !isset( $r['limit_response_size'] ) || $bytes_written < $r['limit_response_size'];
			}

			fclose( $stream_handle );

		} else {
			$header_length = 0;
			while ( ! feof( $handle ) && $keep_reading ) {
				$block = fread( $handle, $block_size );
				$strResponse .= $block;
				if ( ! $bodyStarted && strpos( $strResponse, "\r\n\r\n" ) ) {
					$header_length = strpos( $strResponse, "\r\n\r\n" ) + 4;
					$bodyStarted = true;
				}
				$keep_reading = ( ! $bodyStarted || !isset( $r['limit_response_size'] ) || strlen( $strResponse ) < ( $header_length + $r['limit_response_size'] ) );
			}

			$process = WP_Http::processResponse( $strResponse );
			unset( $strResponse );

		}

		fclose( $handle );

		$arrHeaders = WP_Http::processHeaders( $process['headers'], $url );

		$response = array(
			'headers' => $arrHeaders['headers'],
			// Not yet processed.
			'body' => null,
			'response' => $arrHeaders['response'],
			'cookies' => $arrHeaders['cookies'],
			'filename' => $r['filename']
		);

		// Handle redirects.
		if ( false !== ( $redirect_response = WP_HTTP::handle_redirects( $url, $r, $response ) ) )
			return $redirect_response;

		// If the body was chunk encoded, then decode it.
		if ( ! empty( $process['body'] ) && isset( $arrHeaders['headers']['transfer-encoding'] ) && 'chunked' == $arrHeaders['headers']['transfer-encoding'] )
			$process['body'] = WP_Http::chunkTransferDecode($process['body']);

		if ( true === $r['decompress'] && true === WP_Http_Encoding::should_decode($arrHeaders['headers']) )
			$process['body'] = WP_Http_Encoding::decompress( $process['body'] );

		if ( isset( $r['limit_response_size'] ) && strlen( $process['body'] ) > $r['limit_response_size'] )
			$process['body'] = substr( $process['body'], 0, $r['limit_response_size'] );

		$response['body'] = $process['body'];

		return $response;
	}

	/**
	 * Verifies the received SSL certificate against it's Common Names and subjectAltName fields
	 *
	 * PHP's SSL verifications only verify that it's a valid Certificate, it doesn't verify if
	 * the certificate is valid for the hostname which was requested.
	 * This function verifies the requested hostname against certificate's subjectAltName field,
	 * if that is empty, or contains no DNS entries, a fallback to the Common Name field is used.
	 *
	 * IP Address support is included if the request is being made to an IP address.
	 *
	 * @since 3.7.0
	 * @static
	 *
	 * @param stream $stream The PHP Stream which the SSL request is being made over
	 * @param string $host The hostname being requested
	 * @return bool If the cerficiate presented in $stream is valid for $host
	 */
	public static function verify_ssl_certificate( $stream, $host ) {
		$context_options = stream_context_get_options( $stream );

		if ( empty( $context_options['ssl']['peer_certificate'] ) )
			return false;

		$cert = openssl_x509_parse( $context_options['ssl']['peer_certificate'] );
		if ( ! $cert )
			return false;

		/*
		 * If the request is being made to an IP address, we'll validate against IP fields
		 * in the cert (if they exist)
		 */
		$host_type = ( WP_HTTP::is_ip_address( $host ) ? 'ip' : 'dns' );

		$certificate_hostnames = array();
		if ( ! empty( $cert['extensions']['subjectAltName'] ) ) {
			$match_against = preg_split( '/,\s*/', $cert['extensions']['subjectAltName'] );
			foreach ( $match_against as $match ) {
				list( $match_type, $match_host ) = explode( ':', $match );
				if ( $host_type == strtolower( trim( $match_type ) ) ) // IP: or DNS:
					$certificate_hostnames[] = strtolower( trim( $match_host ) );
			}
		} elseif ( !empty( $cert['subject']['CN'] ) ) {
			// Only use the CN when the certificate includes no subjectAltName extension.
			$certificate_hostnames[] = strtolower( $cert['subject']['CN'] );
		}

		// Exact hostname/IP matches.
		if ( in_array( strtolower( $host ), $certificate_hostnames ) )
			return true;

		// IP's can't be wildcards, Stop processing.
		if ( 'ip' == $host_type )
			return false;

		// Test to see if the domain is at least 2 deep for wildcard support.
		if ( substr_count( $host, '.' ) < 2 )
			return false;

		// Wildcard subdomains certs (*.example.com) are valid for a.example.com but not a.b.example.com.
		$wildcard_host = preg_replace( '/^[^.]+\./', '*.', $host );

		return in_array( strtolower( $wildcard_host ), $certificate_hostnames );
	}

	/**
	 * Whether this class can be used for retrieving a URL.
	 *
	 * @static
	 * @access public
	 * @since 2.7.0
	 * @since 3.7.0 Combined with the fsockopen transport and switched to stream_socket_client().
	 *
	 * @return boolean False means this class can not be used, true means it can.
	 */
	public static function test( $args = array() ) {
		if ( ! function_exists( 'stream_socket_client' ) )
			return false;

		$is_ssl = isset( $args['ssl'] ) && $args['ssl'];

		if ( $is_ssl ) {
			if ( ! extension_loaded( 'openssl' ) )
				return false;
			if ( ! function_exists( 'openssl_x509_parse' ) )
				return false;
		}

		/**
		 * Filter whether streams can be used as a transport for retrieving a URL.
		 *
		 * @since 2.7.0
		 *
		 * @param bool  $use_class Whether the class can be used. Default true.
		 * @param array $args      Request arguments.
		 */
		return apply_filters( 'use_streams_transport', true, $args );
	}
}

/**
 * Deprecated HTTP Transport method which used fsockopen.
 *
 * This class is not used, and is included for backwards compatibility only.
 * All code should make use of WP_HTTP directly through it's API.
 *
 * @see WP_HTTP::request
 *
 * @since 2.7.0
 * @deprecated 3.7.0 Please use WP_HTTP::request() directly
 */
class WP_HTTP_Fsockopen extends WP_HTTP_Streams {
	// For backwards compatibility for users who are using the class directly.
}

/**
 * HTTP request method uses Curl extension to retrieve the url.
 *
 * Requires the Curl extension to be installed.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7.0
 */
class WP_Http_Curl {

	/**
	 * Temporary header storage for during requests.
	 *
	 * @since 3.2.0
	 * @access private
	 * @var string
	 */
	private $headers = '';

	/**
	 * Temporary body storage for during requests.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var string
	 */
	private $body = '';

	/**
	 * The maximum amount of data to receive from the remote server.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var int
	 */
	private $max_body_length = false;

	/**
	 * The file resource used for streaming to file.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var resource
	 */
	private $stream_handle = false;

	/**
	 * The total bytes written in the current request.
	 *
	 * @since 4.1.0
	 * @access private
	 * @var int
	 */
	private $bytes_written_total = 0;

	/**
	 * Send a HTTP request to a URI using cURL extension.
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @param string $url The request URL.
	 * @param string|array $args Optional. Override the defaults.
	 * @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'. A WP_Error instance upon error
	 */
	public function request($url, $args = array()) {
		$defaults = array(
			'method' => 'GET', 'timeout' => 5,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(), 'body' => null, 'cookies' => array()
		);

		$r = wp_parse_args( $args, $defaults );

		if ( isset( $r['headers']['User-Agent'] ) ) {
			$r['user-agent'] = $r['headers']['User-Agent'];
			unset( $r['headers']['User-Agent'] );
		} elseif ( isset( $r['headers']['user-agent'] ) ) {
			$r['user-agent'] = $r['headers']['user-agent'];
			unset( $r['headers']['user-agent'] );
		}

		// Construct Cookie: header if any cookies are set.
		WP_Http::buildCookieHeader( $r );

		$handle = curl_init();

		// cURL offers really easy proxy support.
		$proxy = new WP_HTTP_Proxy();

		if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) {

			curl_setopt( $handle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP );
			curl_setopt( $handle, CURLOPT_PROXY, $proxy->host() );
			curl_setopt( $handle, CURLOPT_PROXYPORT, $proxy->port() );

			if ( $proxy->use_authentication() ) {
				curl_setopt( $handle, CURLOPT_PROXYAUTH, CURLAUTH_ANY );
				curl_setopt( $handle, CURLOPT_PROXYUSERPWD, $proxy->authentication() );
			}
		}

		$is_local = isset($r['local']) && $r['local'];
		$ssl_verify = isset($r['sslverify']) && $r['sslverify'];
		if ( $is_local ) {
			/** This filter is documented in wp-includes/class-http.php */
			$ssl_verify = apply_filters( 'https_local_ssl_verify', $ssl_verify );
		} elseif ( ! $is_local ) {
			/** This filter is documented in wp-includes/class-http.php */
			$ssl_verify = apply_filters( 'https_ssl_verify', $ssl_verify );
		}

		/*
		 * CURLOPT_TIMEOUT and CURLOPT_CONNECTTIMEOUT expect integers. Have to use ceil since.
		 * a value of 0 will allow an unlimited timeout.
		 */
		$timeout = (int) ceil( $r['timeout'] );
		curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt( $handle, CURLOPT_TIMEOUT, $timeout );

		curl_setopt( $handle, CURLOPT_URL, $url);
		curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $handle, CURLOPT_SSL_VERIFYHOST, ( $ssl_verify === true ) ? 2 : false );
		curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, $ssl_verify );
		curl_setopt( $handle, CURLOPT_CAINFO, $r['sslcertificates'] );
		curl_setopt( $handle, CURLOPT_USERAGENT, $r['user-agent'] );

		/*
		 * The option doesn't work with safe mode or when open_basedir is set, and there's
		 * a bug #17490 with redirected POST requests, so handle redirections outside Curl.
		 */
		curl_setopt( $handle, CURLOPT_FOLLOWLOCATION, false );
		if ( defined( 'CURLOPT_PROTOCOLS' ) ) // PHP 5.2.10 / cURL 7.19.4
			curl_setopt( $handle, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS );

		switch ( $r['method'] ) {
			case 'HEAD':
				curl_setopt( $handle, CURLOPT_NOBODY, true );
				break;
			case 'POST':
				curl_setopt( $handle, CURLOPT_POST, true );
				curl_setopt( $handle, CURLOPT_POSTFIELDS, $r['body'] );
				break;
			case 'PUT':
				curl_setopt( $handle, CURLOPT_CUSTOMREQUEST, 'PUT' );
				curl_setopt( $handle, CURLOPT_POSTFIELDS, $r['body'] );
				break;
			default:
				curl_setopt( $handle, CURLOPT_CUSTOMREQUEST, $r['method'] );
				if ( ! is_null( $r['body'] ) )
					curl_setopt( $handle, CURLOPT_POSTFIELDS, $r['body'] );
				break;
		}

		if ( true === $r['blocking'] ) {
			curl_setopt( $handle, CURLOPT_HEADERFUNCTION, array( $this, 'stream_headers' ) );
			curl_setopt( $handle, CURLOPT_WRITEFUNCTION, array( $this, 'stream_body' ) );
		}

		curl_setopt( $handle, CURLOPT_HEADER, false );

		if ( isset( $r['limit_response_size'] ) )
			$this->max_body_length = intval( $r['limit_response_size'] );
		else
			$this->max_body_length = false;

		// If streaming to a file open a file handle, and setup our curl streaming handler.
		if ( $r['stream'] ) {
			if ( ! WP_DEBUG )
				$this->stream_handle = @fopen( $r['filename'], 'w+' );
			else
				$this->stream_handle = fopen( $r['filename'], 'w+' );
			if ( ! $this->stream_handle )
				return new WP_Error( 'http_request_failed', sprintf( __( 'Could not open handle for fopen() to %s' ), $r['filename'] ) );
		} else {
			$this->stream_handle = false;
		}

		if ( !empty( $r['headers'] ) ) {
			// cURL expects full header strings in each element.
			$headers = array();
			foreach ( $r['headers'] as $name => $value ) {
				$headers[] = "{$name}: $value";
			}
			curl_setopt( $handle, CURLOPT_HTTPHEADER, $headers );
		}

		if ( $r['httpversion'] == '1.0' )
			curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
		else
			curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );

		/**
		 * Fires before the cURL request is executed.
		 *
		 * Cookies are not currently handled by the HTTP API. This action allows
		 * plugins to handle cookies themselves.
		 *
		 * @since 2.8.0
		 *
		 * @param resource &$handle The cURL handle returned by curl_init().
		 * @param array    $r       The HTTP request arguments.
		 * @param string   $url     The request URL.
		 */
		do_action_ref_array( 'http_api_curl', array( &$handle, $r, $url ) );

		// We don't need to return the body, so don't. Just execute request and return.
		if ( ! $r['blocking'] ) {
			curl_exec( $handle );

			if ( $curl_error = curl_error( $handle ) ) {
				curl_close( $handle );
				return new WP_Error( 'http_request_failed', $curl_error );
			}
			if ( in_array( curl_getinfo( $handle, CURLINFO_HTTP_CODE ), array( 301, 302 ) ) ) {
				curl_close( $handle );
				return new WP_Error( 'http_request_failed', __( 'Too many redirects.' ) );
			}

			curl_close( $handle );
			return array( 'headers' => array(), 'body' => '', 'response' => array('code' => false, 'message' => false), 'cookies' => array() );
		}

		curl_exec( $handle );
		$theHeaders = WP_Http::processHeaders( $this->headers, $url );
		$theBody = $this->body;
		$bytes_written_total = $this->bytes_written_total;

		$this->headers = '';
		$this->body = '';
		$this->bytes_written_total = 0;

		$curl_error = curl_errno( $handle );

		// If an error occurred, or, no response.
		if ( $curl_error || ( 0 == strlen( $theBody ) && empty( $theHeaders['headers'] ) ) ) {
			if ( CURLE_WRITE_ERROR /* 23 */ == $curl_error ) {
				if ( ! $this->max_body_length || $this->max_body_length != $bytes_written_total ) {
					if ( $r['stream'] ) {
						curl_close( $handle );
						fclose( $this->stream_handle );
						return new WP_Error( 'http_request_failed', __( 'Failed to write request to temporary file.' ) );
					} else {
						curl_close( $handle );
						return new WP_Error( 'http_request_failed', curl_error( $handle ) );
					}
				}
			} else {
				if ( $curl_error = curl_error( $handle ) ) {
					curl_close( $handle );
					return new WP_Error( 'http_request_failed', $curl_error );
				}
			}
			if ( in_array( curl_getinfo( $handle, CURLINFO_HTTP_CODE ), array( 301, 302 ) ) ) {
				curl_close( $handle );
				return new WP_Error( 'http_request_failed', __( 'Too many redirects.' ) );
			}
		}

		curl_close( $handle );

		if ( $r['stream'] )
			fclose( $this->stream_handle );

		$response = array(
			'headers' => $theHeaders['headers'],
			'body' => null,
			'response' => $theHeaders['response'],
			'cookies' => $theHeaders['cookies'],
			'filename' => $r['filename']
		);

		// Handle redirects.
		if ( false !== ( $redirect_response = WP_HTTP::handle_redirects( $url, $r, $response ) ) )
			return $redirect_response;

		if ( true === $r['decompress'] && true === WP_Http_Encoding::should_decode($theHeaders['headers']) )
			$theBody = WP_Http_Encoding::decompress( $theBody );

		$response['body'] = $theBody;

		return $response;
	}

	/**
	 * Grab the headers of the cURL request
	 *
	 * Each header is sent individually to this callback, so we append to the $header property for temporary storage
	 *
	 * @since 3.2.0
	 * @access private
	 * @return int
	 */
	private function stream_headers( $handle, $headers ) {
		$this->headers .= $headers;
		return strlen( $headers );
	}

	/**
	 * Grab the body of the cURL request
	 *
	 * The contents of the document are passed in chunks, so we append to the $body property for temporary storage.
	 * Returning a length shorter than the length of $data passed in will cause cURL to abort the request with CURLE_WRITE_ERROR
	 *
	 * @since 3.6.0
	 * @access private
	 * @return int
	 */
	private function stream_body( $handle, $data ) {
		$data_length = strlen( $data );

		if ( $this->max_body_length && ( $this->bytes_written_total + $data_length ) > $this->max_body_length ) {
			$data_length = ( $this->max_body_length - $this->bytes_written_total );
			$data = substr( $data, 0, $data_length );
		}

		if ( $this->stream_handle ) {
			$bytes_written = fwrite( $this->stream_handle, $data );
		} else {
			$this->body .= $data;
			$bytes_written = $data_length;
		}

		$this->bytes_written_total += $bytes_written;

		// Upon event of this function returning less than strlen( $data ) curl will error with CURLE_WRITE_ERROR.
		return $bytes_written;
	}

	/**
	 * Whether this class can be used for retrieving an URL.
	 *
	 * @static
	 * @since 2.7.0
	 *
	 * @return boolean False means this class can not be used, true means it can.
	 */
	public static function test( $args = array() ) {
		if ( ! function_exists( 'curl_init' ) || ! function_exists( 'curl_exec' ) )
			return false;

		$is_ssl = isset( $args['ssl'] ) && $args['ssl'];

		if ( $is_ssl ) {
			$curl_version = curl_version();
			// Check whether this cURL version support SSL requests.
			if ( ! (CURL_VERSION_SSL & $curl_version['features']) )
				return false;
		}

		/**
		 * Filter whether cURL can be used as a transport for retrieving a URL.
		 *
		 * @since 2.7.0
		 *
		 * @param bool  $use_class Whether the class can be used. Default true.
		 * @param array $args      An array of request arguments.
		 */
		return apply_filters( 'use_curl_transport', true, $args );
	}
}

/**
 * Adds Proxy support to the WordPress HTTP API.
 *
 * There are caveats to proxy support. It requires that defines be made in the wp-config.php file to
 * enable proxy support. There are also a few filters that plugins can hook into for some of the
 * constants.
 *
 * Please note that only BASIC authentication is supported by most transports.
 * cURL MAY support more methods (such as NTLM authentication) depending on your environment.
 *
 * The constants are as follows:
 * <ol>
 * <li>WP_PROXY_HOST - Enable proxy support and host for connecting.</li>
 * <li>WP_PROXY_PORT - Proxy port for connection. No default, must be defined.</li>
 * <li>WP_PROXY_USERNAME - Proxy username, if it requires authentication.</li>
 * <li>WP_PROXY_PASSWORD - Proxy password, if it requires authentication.</li>
 * <li>WP_PROXY_BYPASS_HOSTS - Will prevent the hosts in this list from going through the proxy.
 * You do not need to have localhost and the blog host in this list, because they will not be passed
 * through the proxy. The list should be presented in a comma separated list, wildcards using * are supported, eg. *.wordpress.org</li>
 * </ol>
 *
 * An example can be as seen below.
 *
 *     define('WP_PROXY_HOST', '192.168.84.101');
 *     define('WP_PROXY_PORT', '8080');
 *     define('WP_PROXY_BYPASS_HOSTS', 'localhost, www.example.com, *.wordpress.org');
 *
 * @link https://core.trac.wordpress.org/ticket/4011 Proxy support ticket in WordPress.
 * @link https://core.trac.wordpress.org/ticket/14636 Allow wildcard domains in WP_PROXY_BYPASS_HOSTS
 * @since 2.8.0
 */
class WP_HTTP_Proxy {

	/**
	 * Whether proxy connection should be used.
	 *
	 * @since 2.8.0
	 *
	 * @use WP_PROXY_HOST
	 * @use WP_PROXY_PORT
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return defined('WP_PROXY_HOST') && defined('WP_PROXY_PORT');
	}

	/**
	 * Whether authentication should be used.
	 *
	 * @since 2.8.0
	 *
	 * @use WP_PROXY_USERNAME
	 * @use WP_PROXY_PASSWORD
	 *
	 * @return bool
	 */
	public function use_authentication() {
		return defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD');
	}

	/**
	 * Retrieve the host for the proxy server.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function host() {
		if ( defined('WP_PROXY_HOST') )
			return WP_PROXY_HOST;

		return '';
	}

	/**
	 * Retrieve the port for the proxy server.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function port() {
		if ( defined('WP_PROXY_PORT') )
			return WP_PROXY_PORT;

		return '';
	}

	/**
	 * Retrieve the username for proxy authentication.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function username() {
		if ( defined('WP_PROXY_USERNAME') )
			return WP_PROXY_USERNAME;

		return '';
	}

	/**
	 * Retrieve the password for proxy authentication.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function password() {
		if ( defined('WP_PROXY_PASSWORD') )
			return WP_PROXY_PASSWORD;

		return '';
	}

	/**
	 * Retrieve authentication string for proxy authentication.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function authentication() {
		return $this->username() . ':' . $this->password();
	}

	/**
	 * Retrieve header string for proxy authentication.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function authentication_header() {
		return 'Proxy-Authorization: Basic ' . base64_encode( $this->authentication() );
	}

	/**
	 * Whether URL should be sent through the proxy server.
	 *
	 * We want to keep localhost and the blog URL from being sent through the proxy server, because
	 * some proxies can not handle this. We also have the constant available for defining other
	 * hosts that won't be sent through the proxy.
	 *
	 * @since 2.8.0
	 *
	 * @param string $uri URI to check.
	 * @return bool True, to send through the proxy and false if, the proxy should not be used.
	 */
	public function send_through_proxy( $uri ) {
		/*
		 * parse_url() only handles http, https type URLs, and will emit E_WARNING on failure.
		 * This will be displayed on blogs, which is not reasonable.
		 */
		$check = @parse_url($uri);

		// Malformed URL, can not process, but this could mean ssl, so let through anyway.
		if ( $check === false )
			return true;

		$home = parse_url( get_option('siteurl') );

		/**
		 * Filter whether to preempt sending the request through the proxy server.
		 *
		 * Returning false will bypass the proxy; returning true will send
		 * the request through the proxy. Returning null bypasses the filter.
		 *
		 * @since 3.5.0
		 *
		 * @param null   $override Whether to override the request result. Default null.
		 * @param string $uri      URL to check.
		 * @param array  $check    Associative array result of parsing the URI.
		 * @param array  $home     Associative array result of parsing the site URL.
		 */
		$result = apply_filters( 'pre_http_send_through_proxy', null, $uri, $check, $home );
		if ( ! is_null( $result ) )
			return $result;

		if ( 'localhost' == $check['host'] || ( isset( $home['host'] ) && $home['host'] == $check['host'] ) )
			return false;

		if ( !defined('WP_PROXY_BYPASS_HOSTS') )
			return true;

		static $bypass_hosts;
		static $wildcard_regex = false;
		if ( null == $bypass_hosts ) {
			$bypass_hosts = preg_split('|,\s*|', WP_PROXY_BYPASS_HOSTS);

			if ( false !== strpos(WP_PROXY_BYPASS_HOSTS, '*') ) {
				$wildcard_regex = array();
				foreach ( $bypass_hosts as $host )
					$wildcard_regex[] = str_replace( '\*', '.+', preg_quote( $host, '/' ) );
				$wildcard_regex = '/^(' . implode('|', $wildcard_regex) . ')$/i';
			}
		}

		if ( !empty($wildcard_regex) )
			return !preg_match($wildcard_regex, $check['host']);
		else
			return !in_array( $check['host'], $bypass_hosts );
	}
}
/**
 * Internal representation of a single cookie.
 *
 * Returned cookies are represented using this class, and when cookies are set, if they are not
 * already a WP_Http_Cookie() object, then they are turned into one.
 *
 * @todo The WordPress convention is to use underscores instead of camelCase for function and method
 * names. Need to switch to use underscores instead for the methods.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.8.0
 */
class WP_Http_Cookie {

	/**
	 * Cookie name.
	 *
	 * @since 2.8.0
	 * @var string
	 */
	public $name;

	/**
	 * Cookie value.
	 *
	 * @since 2.8.0
	 * @var string
	 */
	public $value;

	/**
	 * When the cookie expires.
	 *
	 * @since 2.8.0
	 * @var string
	 */
	public $expires;

	/**
	 * Cookie URL path.
	 *
	 * @since 2.8.0
	 * @var string
	 */
	public $path;

	/**
	 * Cookie Domain.
	 *
	 * @since 2.8.0
	 * @var string
	 */
	public $domain;

	/**
	 * Sets up this cookie object.
	 *
	 * The parameter $data should be either an associative array containing the indices names below
	 * or a header string detailing it.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param string|array $data {
	 *     Raw cookie data as header string or data array.
	 *
	 *     @type string     $name    Cookie name.
	 *     @type mixed      $value   Value. Should NOT already be urlencoded.
	 *     @type string|int $expires Optional. Unix timestamp or formatted date. Default null.
	 *     @type string     $path    Optional. Path. Default '/'.
	 *     @type string     $domain  Optional. Domain. Default host of parsed $requested_url.
	 *     @type int        $port    Optional. Port. Default null.
	 * }
	 * @param string       $requested_url The URL which the cookie was set on, used for default $domain
	 *                                    and $port values.
	 */
	public function __construct( $data, $requested_url = '' ) {
		if ( $requested_url )
			$arrURL = @parse_url( $requested_url );
		if ( isset( $arrURL['host'] ) )
			$this->domain = $arrURL['host'];
		$this->path = isset( $arrURL['path'] ) ? $arrURL['path'] : '/';
		if (  '/' != substr( $this->path, -1 ) )
			$this->path = dirname( $this->path ) . '/';

		if ( is_string( $data ) ) {
			// Assume it's a header string direct from a previous request.
			$pairs = explode( ';', $data );

			// Special handling for first pair; name=value. Also be careful of "=" in value.
			$name  = trim( substr( $pairs[0], 0, strpos( $pairs[0], '=' ) ) );
			$value = substr( $pairs[0], strpos( $pairs[0], '=' ) + 1 );
			$this->name  = $name;
			$this->value = urldecode( $value );

			// Removes name=value from items.
			array_shift( $pairs );

			// Set everything else as a property.
			foreach ( $pairs as $pair ) {
				$pair = rtrim($pair);

				// Handle the cookie ending in ; which results in a empty final pair.
				if ( empty($pair) )
					continue;

				list( $key, $val ) = strpos( $pair, '=' ) ? explode( '=', $pair ) : array( $pair, '' );
				$key = strtolower( trim( $key ) );
				if ( 'expires' == $key )
					$val = strtotime( $val );
				$this->$key = $val;
			}
		} else {
			if ( !isset( $data['name'] ) )
				return;

			// Set properties based directly on parameters.
			foreach ( array( 'name', 'value', 'path', 'domain', 'port' ) as $field ) {
				if ( isset( $data[ $field ] ) )
					$this->$field = $data[ $field ];
			}

			if ( isset( $data['expires'] ) )
				$this->expires = is_int( $data['expires'] ) ? $data['expires'] : strtotime( $data['expires'] );
			else
				$this->expires = null;
		}
	}

	/**
	 * Confirms that it's OK to send this cookie to the URL checked against.
	 *
	 * Decision is based on RFC 2109/2965, so look there for details on validity.
	 *
	 * @access public
	 * @since 2.8.0
	 *
	 * @param string $url URL you intend to send this cookie to
	 * @return boolean true if allowed, false otherwise.
	 */
	public function test( $url ) {
		if ( is_null( $this->name ) )
			return false;

		// Expires - if expired then nothing else matters.
		if ( isset( $this->expires ) && time() > $this->expires )
			return false;

		// Get details on the URL we're thinking about sending to.
		$url = parse_url( $url );
		$url['port'] = isset( $url['port'] ) ? $url['port'] : ( 'https' == $url['scheme'] ? 443 : 80 );
		$url['path'] = isset( $url['path'] ) ? $url['path'] : '/';

		// Values to use for comparison against the URL.
		$path   = isset( $this->path )   ? $this->path   : '/';
		$port   = isset( $this->port )   ? $this->port   : null;
		$domain = isset( $this->domain ) ? strtolower( $this->domain ) : strtolower( $url['host'] );
		if ( false === stripos( $domain, '.' ) )
			$domain .= '.local';

		// Host - very basic check that the request URL ends with the domain restriction (minus leading dot).
		$domain = substr( $domain, 0, 1 ) == '.' ? substr( $domain, 1 ) : $domain;
		if ( substr( $url['host'], -strlen( $domain ) ) != $domain )
			return false;

		// Port - supports "port-lists" in the format: "80,8000,8080".
		if ( !empty( $port ) && !in_array( $url['port'], explode( ',', $port) ) )
			return false;

		// Path - request path must start with path restriction.
		if ( substr( $url['path'], 0, strlen( $path ) ) != $path )
			return false;

		return true;
	}

	/**
	 * Convert cookie name and value back to header string.
	 *
	 * @access public
	 * @since 2.8.0
	 *
	 * @return string Header encoded cookie name and value.
	 */
	public function getHeaderValue() {
		if ( ! isset( $this->name ) || ! isset( $this->value ) )
			return '';

		/**
		 * Filter the header-encoded cookie value.
		 *
		 * @since 3.4.0
		 *
		 * @param string $value The cookie value.
		 * @param string $name  The cookie name.
		 */
		return $this->name . '=' . apply_filters( 'wp_http_cookie_value', $this->value, $this->name );
	}

	/**
	 * Retrieve cookie header for usage in the rest of the WordPress HTTP API.
	 *
	 * @access public
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function getFullHeader() {
		return 'Cookie: ' . $this->getHeaderValue();
	}
}

/**
 * Implementation for deflate and gzip transfer encodings.
 *
 * Includes RFC 1950, RFC 1951, and RFC 1952.
 *
 * @since 2.8.0
 * @package WordPress
 * @subpackage HTTP
 */
class WP_Http_Encoding {

	/**
	 * Compress raw string using the deflate format.
	 *
	 * Supports the RFC 1951 standard.
	 *
	 * @since 2.8.0
	 *
	 * @param string $raw String to compress.
	 * @param int $level Optional, default is 9. Compression level, 9 is highest.
	 * @param string $supports Optional, not used. When implemented it will choose the right compression based on what the server supports.
	 * @return string|false False on failure.
	 */
	public static function compress( $raw, $level = 9, $supports = null ) {
		return gzdeflate( $raw, $level );
	}

	/**
	 * Decompression of deflated string.
	 *
	 * Will attempt to decompress using the RFC 1950 standard, and if that fails
	 * then the RFC 1951 standard deflate will be attempted. Finally, the RFC
	 * 1952 standard gzip decode will be attempted. If all fail, then the
	 * original compressed string will be returned.
	 *
	 * @since 2.8.0
	 *
	 * @param string $compressed String to decompress.
	 * @param int $length The optional length of the compressed data.
	 * @return string|bool False on failure.
	 */
	public static function decompress( $compressed, $length = null ) {

		if ( empty($compressed) )
			return $compressed;

		if ( false !== ( $decompressed = @gzinflate( $compressed ) ) )
			return $decompressed;

		if ( false !== ( $decompressed = self::compatible_gzinflate( $compressed ) ) )
			return $decompressed;

		if ( false !== ( $decompressed = @gzuncompress( $compressed ) ) )
			return $decompressed;

		if ( function_exists('gzdecode') ) {
			$decompressed = @gzdecode( $compressed );

			if ( false !== $decompressed )
				return $decompressed;
		}

		return $compressed;
	}

	/**
	 * Decompression of deflated string while staying compatible with the majority of servers.
	 *
	 * Certain Servers will return deflated data with headers which PHP's gzinflate()
	 * function cannot handle out of the box. The following function has been created from
	 * various snippets on the gzinflate() PHP documentation.
	 *
	 * Warning: Magic numbers within. Due to the potential different formats that the compressed
	 * data may be returned in, some "magic offsets" are needed to ensure proper decompression
	 * takes place. For a simple progmatic way to determine the magic offset in use, see:
	 * https://core.trac.wordpress.org/ticket/18273
	 *
	 * @since 2.8.1
	 * @link https://core.trac.wordpress.org/ticket/18273
	 * @link http://au2.php.net/manual/en/function.gzinflate.php#70875
	 * @link http://au2.php.net/manual/en/function.gzinflate.php#77336
	 *
	 * @param string $gzData String to decompress.
	 * @return string|bool False on failure.
	 */
	public static function compatible_gzinflate($gzData) {

		// Compressed data might contain a full header, if so strip it for gzinflate().
		if ( substr($gzData, 0, 3) == "\x1f\x8b\x08" ) {
			$i = 10;
			$flg = ord( substr($gzData, 3, 1) );
			if ( $flg > 0 ) {
				if ( $flg & 4 ) {
					list($xlen) = unpack('v', substr($gzData, $i, 2) );
					$i = $i + 2 + $xlen;
				}
				if ( $flg & 8 )
					$i = strpos($gzData, "\0", $i) + 1;
				if ( $flg & 16 )
					$i = strpos($gzData, "\0", $i) + 1;
				if ( $flg & 2 )
					$i = $i + 2;
			}
			$decompressed = @gzinflate( substr($gzData, $i, -8) );
			if ( false !== $decompressed )
				return $decompressed;
		}

		// Compressed data from java.util.zip.Deflater amongst others.
		$decompressed = @gzinflate( substr($gzData, 2) );
		if ( false !== $decompressed )
			return $decompressed;

		return false;
	}

	/**
	 * What encoding types to accept and their priority values.
	 *
	 * @since 2.8.0
	 *
	 * @param string $url
	 * @param array  $args
	 * @return string Types of encoding to accept.
	 */
	public static function accept_encoding( $url, $args ) {
		$type = array();
		$compression_enabled = self::is_available();

		if ( ! $args['decompress'] ) // Decompression specifically disabled.
			$compression_enabled = false;
		elseif ( $args['stream'] ) // Disable when streaming to file.
			$compression_enabled = false;
		elseif ( isset( $args['limit_response_size'] ) ) // If only partial content is being requested, we won't be able to decompress it.
			$compression_enabled = false;

		if ( $compression_enabled ) {
			if ( function_exists( 'gzinflate' ) )
				$type[] = 'deflate;q=1.0';

			if ( function_exists( 'gzuncompress' ) )
				$type[] = 'compress;q=0.5';

			if ( function_exists( 'gzdecode' ) )
				$type[] = 'gzip;q=0.5';
		}

		/**
		 * Filter the allowed encoding types.
		 *
		 * @since 3.6.0
		 *
		 * @param array  $type Encoding types allowed. Accepts 'gzinflate',
		 *                     'gzuncompress', 'gzdecode'.
		 * @param string $url  URL of the HTTP request.
		 * @param array  $args HTTP request arguments.
		 */
		$type = apply_filters( 'wp_http_accept_encoding', $type, $url, $args );

		return implode(', ', $type);
	}

	/**
	 * What encoding the content used when it was compressed to send in the headers.
	 *
	 * @since 2.8.0
	 *
	 * @return string Content-Encoding string to send in the header.
	 */
	public static function content_encoding() {
		return 'deflate';
	}

	/**
	 * Whether the content be decoded based on the headers.
	 *
	 * @since 2.8.0
	 *
	 * @param array|string $headers All of the available headers.
	 * @return bool
	 */
	public static function should_decode($headers) {
		if ( is_array( $headers ) ) {
			if ( array_key_exists('content-encoding', $headers) && ! empty( $headers['content-encoding'] ) )
				return true;
		} elseif ( is_string( $headers ) ) {
			return ( stripos($headers, 'content-encoding:') !== false );
		}

		return false;
	}

	/**
	 * Whether decompression and compression are supported by the PHP version.
	 *
	 * Each function is tested instead of checking for the zlib extension, to
	 * ensure that the functions all exist in the PHP version and aren't
	 * disabled.
	 *
	 * @since 2.8.0
	 *
	 * @return bool
	 */
	public static function is_available() {
		return ( function_exists('gzuncompress') || function_exists('gzdeflate') || function_exists('gzinflate') );
	}
}
