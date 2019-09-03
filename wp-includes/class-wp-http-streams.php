<?php
/**
 * HTTP API: WP_Http_Streams class
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 4.4.0
 */

/**
 * Core class used to integrate PHP Streams as an HTTP transport.
 *
 * @since 2.7.0
 * @since 3.7.0 Combined with the fsockopen transport and switched to `stream_socket_client()`.
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
	 * @param string $url The request URL.
	 * @param string|array $args Optional. Override the defaults.
	 * @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'. A WP_Error instance upon error
	 */
	public function request( $url, $args = array() ) {
		$defaults = array(
			'method'      => 'GET',
			'timeout'     => 5,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => null,
			'cookies'     => array(),
		);

		$parsed_args = wp_parse_args( $args, $defaults );

		if ( isset( $parsed_args['headers']['User-Agent'] ) ) {
			$parsed_args['user-agent'] = $parsed_args['headers']['User-Agent'];
			unset( $parsed_args['headers']['User-Agent'] );
		} elseif ( isset( $parsed_args['headers']['user-agent'] ) ) {
			$parsed_args['user-agent'] = $parsed_args['headers']['user-agent'];
			unset( $parsed_args['headers']['user-agent'] );
		}

		// Construct Cookie: header if any cookies are set.
		WP_Http::buildCookieHeader( $parsed_args );

		$arrURL = parse_url( $url );

		$connect_host = $arrURL['host'];

		$secure_transport = ( $arrURL['scheme'] == 'ssl' || $arrURL['scheme'] == 'https' );
		if ( ! isset( $arrURL['port'] ) ) {
			if ( $arrURL['scheme'] == 'ssl' || $arrURL['scheme'] == 'https' ) {
				$arrURL['port']   = 443;
				$secure_transport = true;
			} else {
				$arrURL['port'] = 80;
			}
		}

		// Always pass a Path, defaulting to the root in cases such as http://example.com
		if ( ! isset( $arrURL['path'] ) ) {
			$arrURL['path'] = '/';
		}

		if ( isset( $parsed_args['headers']['Host'] ) || isset( $parsed_args['headers']['host'] ) ) {
			if ( isset( $parsed_args['headers']['Host'] ) ) {
				$arrURL['host'] = $parsed_args['headers']['Host'];
			} else {
				$arrURL['host'] = $parsed_args['headers']['host'];
			}
			unset( $parsed_args['headers']['Host'], $parsed_args['headers']['host'] );
		}

		/*
		 * Certain versions of PHP have issues with 'localhost' and IPv6, It attempts to connect
		 * to ::1, which fails when the server is not set up for it. For compatibility, always
		 * connect to the IPv4 address.
		 */
		if ( 'localhost' == strtolower( $connect_host ) ) {
			$connect_host = '127.0.0.1';
		}

		$connect_host = $secure_transport ? 'ssl://' . $connect_host : 'tcp://' . $connect_host;

		$is_local   = isset( $parsed_args['local'] ) && $parsed_args['local'];
		$ssl_verify = isset( $parsed_args['sslverify'] ) && $parsed_args['sslverify'];
		if ( $is_local ) {
			/**
			 * Filters whether SSL should be verified for local requests.
			 *
			 * @since 2.8.0
			 * @since 5.1.0 The `$url` parameter was added.
			 *
			 * @param bool   $ssl_verify Whether to verify the SSL connection. Default true.
			 * @param string $url        The request URL.
			 */
			$ssl_verify = apply_filters( 'https_local_ssl_verify', $ssl_verify, $url );
		} elseif ( ! $is_local ) {
			/** This filter is documented in wp-includes/class-http.php */
			$ssl_verify = apply_filters( 'https_ssl_verify', $ssl_verify, $url );
		}

		$proxy = new WP_HTTP_Proxy();

		$context = stream_context_create(
			array(
				'ssl' => array(
					'verify_peer'       => $ssl_verify,
					//'CN_match' => $arrURL['host'], // This is handled by self::verify_ssl_certificate()
					'capture_peer_cert' => $ssl_verify,
					'SNI_enabled'       => true,
					'cafile'            => $parsed_args['sslcertificates'],
					'allow_self_signed' => ! $ssl_verify,
				),
			)
		);

		$timeout         = (int) floor( $parsed_args['timeout'] );
		$utimeout        = $timeout == $parsed_args['timeout'] ? 0 : 1000000 * $parsed_args['timeout'] % 1000000;
		$connect_timeout = max( $timeout, 1 );

		// Store error number.
		$connection_error = null;

		// Store error string.
		$connection_error_str = null;

		if ( ! WP_DEBUG ) {
			// In the event that the SSL connection fails, silence the many PHP Warnings.
			if ( $secure_transport ) {
				$error_reporting = error_reporting( 0 );
			}

			if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) {
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$handle = @stream_socket_client( 'tcp://' . $proxy->host() . ':' . $proxy->port(), $connection_error, $connection_error_str, $connect_timeout, STREAM_CLIENT_CONNECT, $context );
			} else {
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$handle = @stream_socket_client( $connect_host . ':' . $arrURL['port'], $connection_error, $connection_error_str, $connect_timeout, STREAM_CLIENT_CONNECT, $context );
			}

			if ( $secure_transport ) {
				error_reporting( $error_reporting );
			}
		} else {
			if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) {
				$handle = stream_socket_client( 'tcp://' . $proxy->host() . ':' . $proxy->port(), $connection_error, $connection_error_str, $connect_timeout, STREAM_CLIENT_CONNECT, $context );
			} else {
				$handle = stream_socket_client( $connect_host . ':' . $arrURL['port'], $connection_error, $connection_error_str, $connect_timeout, STREAM_CLIENT_CONNECT, $context );
			}
		}

		if ( false === $handle ) {
			// SSL connection failed due to expired/invalid cert, or, OpenSSL configuration is broken.
			if ( $secure_transport && 0 === $connection_error && '' === $connection_error_str ) {
				return new WP_Error( 'http_request_failed', __( 'The SSL certificate for the host could not be verified.' ) );
			}

			return new WP_Error( 'http_request_failed', $connection_error . ': ' . $connection_error_str );
		}

		// Verify that the SSL certificate is valid for this request.
		if ( $secure_transport && $ssl_verify && ! $proxy->is_enabled() ) {
			if ( ! self::verify_ssl_certificate( $handle, $arrURL['host'] ) ) {
				return new WP_Error( 'http_request_failed', __( 'The SSL certificate for the host could not be verified.' ) );
			}
		}

		stream_set_timeout( $handle, $timeout, $utimeout );

		if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) { //Some proxies require full URL in this field.
			$requestPath = $url;
		} else {
			$requestPath = $arrURL['path'] . ( isset( $arrURL['query'] ) ? '?' . $arrURL['query'] : '' );
		}

		$strHeaders = strtoupper( $parsed_args['method'] ) . ' ' . $requestPath . ' HTTP/' . $parsed_args['httpversion'] . "\r\n";

		$include_port_in_host_header = (
			( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) ||
			( 'http' == $arrURL['scheme'] && 80 != $arrURL['port'] ) ||
			( 'https' == $arrURL['scheme'] && 443 != $arrURL['port'] )
		);

		if ( $include_port_in_host_header ) {
			$strHeaders .= 'Host: ' . $arrURL['host'] . ':' . $arrURL['port'] . "\r\n";
		} else {
			$strHeaders .= 'Host: ' . $arrURL['host'] . "\r\n";
		}

		if ( isset( $parsed_args['user-agent'] ) ) {
			$strHeaders .= 'User-agent: ' . $parsed_args['user-agent'] . "\r\n";
		}

		if ( is_array( $parsed_args['headers'] ) ) {
			foreach ( (array) $parsed_args['headers'] as $header => $headerValue ) {
				$strHeaders .= $header . ': ' . $headerValue . "\r\n";
			}
		} else {
			$strHeaders .= $parsed_args['headers'];
		}

		if ( $proxy->use_authentication() ) {
			$strHeaders .= $proxy->authentication_header() . "\r\n";
		}

		$strHeaders .= "\r\n";

		if ( ! is_null( $parsed_args['body'] ) ) {
			$strHeaders .= $parsed_args['body'];
		}

		fwrite( $handle, $strHeaders );

		if ( ! $parsed_args['blocking'] ) {
			stream_set_blocking( $handle, 0 );
			fclose( $handle );
			return array(
				'headers'  => array(),
				'body'     => '',
				'response' => array(
					'code'    => false,
					'message' => false,
				),
				'cookies'  => array(),
			);
		}

		$strResponse  = '';
		$bodyStarted  = false;
		$keep_reading = true;
		$block_size   = 4096;
		if ( isset( $parsed_args['limit_response_size'] ) ) {
			$block_size = min( $block_size, $parsed_args['limit_response_size'] );
		}

		// If streaming to a file setup the file handle.
		if ( $parsed_args['stream'] ) {
			if ( ! WP_DEBUG ) {
				$stream_handle = @fopen( $parsed_args['filename'], 'w+' );
			} else {
				$stream_handle = fopen( $parsed_args['filename'], 'w+' );
			}
			if ( ! $stream_handle ) {
				return new WP_Error(
					'http_request_failed',
					sprintf(
						/* translators: 1: fopen(), 2: File name. */
						__( 'Could not open handle for %1$s to %2$s.' ),
						'fopen()',
						$parsed_args['filename']
					)
				);
			}

			$bytes_written = 0;
			while ( ! feof( $handle ) && $keep_reading ) {
				$block = fread( $handle, $block_size );
				if ( ! $bodyStarted ) {
					$strResponse .= $block;
					if ( strpos( $strResponse, "\r\n\r\n" ) ) {
						$process     = WP_Http::processResponse( $strResponse );
						$bodyStarted = true;
						$block       = $process['body'];
						unset( $strResponse );
						$process['body'] = '';
					}
				}

				$this_block_size = strlen( $block );

				if ( isset( $parsed_args['limit_response_size'] ) && ( $bytes_written + $this_block_size ) > $parsed_args['limit_response_size'] ) {
					$this_block_size = ( $parsed_args['limit_response_size'] - $bytes_written );
					$block           = substr( $block, 0, $this_block_size );
				}

				$bytes_written_to_file = fwrite( $stream_handle, $block );

				if ( $bytes_written_to_file != $this_block_size ) {
					fclose( $handle );
					fclose( $stream_handle );
					return new WP_Error( 'http_request_failed', __( 'Failed to write request to temporary file.' ) );
				}

				$bytes_written += $bytes_written_to_file;

				$keep_reading = ! isset( $parsed_args['limit_response_size'] ) || $bytes_written < $parsed_args['limit_response_size'];
			}

			fclose( $stream_handle );

		} else {
			$header_length = 0;
			while ( ! feof( $handle ) && $keep_reading ) {
				$block        = fread( $handle, $block_size );
				$strResponse .= $block;
				if ( ! $bodyStarted && strpos( $strResponse, "\r\n\r\n" ) ) {
					$header_length = strpos( $strResponse, "\r\n\r\n" ) + 4;
					$bodyStarted   = true;
				}
				$keep_reading = ( ! $bodyStarted || ! isset( $parsed_args['limit_response_size'] ) || strlen( $strResponse ) < ( $header_length + $parsed_args['limit_response_size'] ) );
			}

			$process = WP_Http::processResponse( $strResponse );
			unset( $strResponse );

		}

		fclose( $handle );

		$arrHeaders = WP_Http::processHeaders( $process['headers'], $url );

		$response = array(
			'headers'  => $arrHeaders['headers'],
			// Not yet processed.
			'body'     => null,
			'response' => $arrHeaders['response'],
			'cookies'  => $arrHeaders['cookies'],
			'filename' => $parsed_args['filename'],
		);

		// Handle redirects.
		$redirect_response = WP_Http::handle_redirects( $url, $parsed_args, $response );
		if ( false !== $redirect_response ) {
			return $redirect_response;
		}

		// If the body was chunk encoded, then decode it.
		if ( ! empty( $process['body'] ) && isset( $arrHeaders['headers']['transfer-encoding'] ) && 'chunked' == $arrHeaders['headers']['transfer-encoding'] ) {
			$process['body'] = WP_Http::chunkTransferDecode( $process['body'] );
		}

		if ( true === $parsed_args['decompress'] && true === WP_Http_Encoding::should_decode( $arrHeaders['headers'] ) ) {
			$process['body'] = WP_Http_Encoding::decompress( $process['body'] );
		}

		if ( isset( $parsed_args['limit_response_size'] ) && strlen( $process['body'] ) > $parsed_args['limit_response_size'] ) {
			$process['body'] = substr( $process['body'], 0, $parsed_args['limit_response_size'] );
		}

		$response['body'] = $process['body'];

		return $response;
	}

	/**
	 * Verifies the received SSL certificate against its Common Names and subjectAltName fields.
	 *
	 * PHP's SSL verifications only verify that it's a valid Certificate, it doesn't verify if
	 * the certificate is valid for the hostname which was requested.
	 * This function verifies the requested hostname against certificate's subjectAltName field,
	 * if that is empty, or contains no DNS entries, a fallback to the Common Name field is used.
	 *
	 * IP Address support is included if the request is being made to an IP address.
	 *
	 * @since 3.7.0
	 *
	 * @param stream $stream The PHP Stream which the SSL request is being made over
	 * @param string $host The hostname being requested
	 * @return bool If the cerficiate presented in $stream is valid for $host
	 */
	public static function verify_ssl_certificate( $stream, $host ) {
		$context_options = stream_context_get_options( $stream );

		if ( empty( $context_options['ssl']['peer_certificate'] ) ) {
			return false;
		}

		$cert = openssl_x509_parse( $context_options['ssl']['peer_certificate'] );
		if ( ! $cert ) {
			return false;
		}

		/*
		 * If the request is being made to an IP address, we'll validate against IP fields
		 * in the cert (if they exist)
		 */
		$host_type = ( WP_Http::is_ip_address( $host ) ? 'ip' : 'dns' );

		$certificate_hostnames = array();
		if ( ! empty( $cert['extensions']['subjectAltName'] ) ) {
			$match_against = preg_split( '/,\s*/', $cert['extensions']['subjectAltName'] );
			foreach ( $match_against as $match ) {
				list( $match_type, $match_host ) = explode( ':', $match );
				if ( $host_type == strtolower( trim( $match_type ) ) ) { // IP: or DNS:
					$certificate_hostnames[] = strtolower( trim( $match_host ) );
				}
			}
		} elseif ( ! empty( $cert['subject']['CN'] ) ) {
			// Only use the CN when the certificate includes no subjectAltName extension.
			$certificate_hostnames[] = strtolower( $cert['subject']['CN'] );
		}

		// Exact hostname/IP matches.
		if ( in_array( strtolower( $host ), $certificate_hostnames ) ) {
			return true;
		}

		// IP's can't be wildcards, Stop processing.
		if ( 'ip' == $host_type ) {
			return false;
		}

		// Test to see if the domain is at least 2 deep for wildcard support.
		if ( substr_count( $host, '.' ) < 2 ) {
			return false;
		}

		// Wildcard subdomains certs (*.example.com) are valid for a.example.com but not a.b.example.com.
		$wildcard_host = preg_replace( '/^[^.]+\./', '*.', $host );

		return in_array( strtolower( $wildcard_host ), $certificate_hostnames );
	}

	/**
	 * Determines whether this class can be used for retrieving a URL.
	 *
	 * @since 2.7.0
	 * @since 3.7.0 Combined with the fsockopen transport and switched to stream_socket_client().
	 *
	 * @param array $args Optional. Array of request arguments. Default empty array.
	 * @return bool False means this class can not be used, true means it can.
	 */
	public static function test( $args = array() ) {
		if ( ! function_exists( 'stream_socket_client' ) ) {
			return false;
		}

		$is_ssl = isset( $args['ssl'] ) && $args['ssl'];

		if ( $is_ssl ) {
			if ( ! extension_loaded( 'openssl' ) ) {
				return false;
			}
			if ( ! function_exists( 'openssl_x509_parse' ) ) {
				return false;
			}
		}

		/**
		 * Filters whether streams can be used as a transport for retrieving a URL.
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
 * This class is not used, and is included for backward compatibility only.
 * All code should make use of WP_Http directly through its API.
 *
 * @see WP_HTTP::request
 *
 * @since 2.7.0
 * @deprecated 3.7.0 Please use WP_HTTP::request() directly
 */
class WP_HTTP_Fsockopen extends WP_HTTP_Streams {
	// For backward compatibility for users who are using the class directly.
}
