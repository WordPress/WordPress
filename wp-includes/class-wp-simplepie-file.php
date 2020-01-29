<?php
/**
 * Feed API: WP_SimplePie_File class
 *
 * @package WordPress
 * @subpackage Feed
 * @since 4.7.0
 */

/**
 * Core class for fetching remote files and reading local files with SimplePie.
 *
 * @since 2.8.0
 *
 * @see SimplePie_File
 */
class WP_SimplePie_File extends SimplePie_File {

	/**
	 * Constructor.
	 *
	 * @since 2.8.0
	 * @since 3.2.0 Updated to use a PHP5 constructor.
	 *
	 * @param string       $url             Remote file URL.
	 * @param integer      $timeout         Optional. How long the connection should stay open in seconds.
	 *                                      Default 10.
	 * @param integer      $redirects       Optional. The number of allowed redirects. Default 5.
	 * @param string|array $headers         Optional. Array or string of headers to send with the request.
	 *                                      Default null.
	 * @param string       $useragent       Optional. User-agent value sent. Default null.
	 * @param boolean      $force_fsockopen Optional. Whether to force opening internet or unix domain socket
	 *                                      connection or not. Default false.
	 */
	public function __construct( $url, $timeout = 10, $redirects = 5, $headers = null, $useragent = null, $force_fsockopen = false ) {
		$this->url       = $url;
		$this->timeout   = $timeout;
		$this->redirects = $redirects;
		$this->headers   = $headers;
		$this->useragent = $useragent;

		$this->method = SIMPLEPIE_FILE_SOURCE_REMOTE;

		if ( preg_match( '/^http(s)?:\/\//i', $url ) ) {
			$args = array(
				'timeout'     => $this->timeout,
				'redirection' => $this->redirects,
			);

			if ( ! empty( $this->headers ) ) {
				$args['headers'] = $this->headers;
			}

			if ( SIMPLEPIE_USERAGENT != $this->useragent ) { // Use default WP user agent unless custom has been specified.
				$args['user-agent'] = $this->useragent;
			}

			$res = wp_safe_remote_request( $url, $args );

			if ( is_wp_error( $res ) ) {
				$this->error   = 'WP HTTP Error: ' . $res->get_error_message();
				$this->success = false;
			} else {
				$this->headers     = wp_remote_retrieve_headers( $res );
				$this->body        = wp_remote_retrieve_body( $res );
				$this->status_code = wp_remote_retrieve_response_code( $res );
			}
		} else {
			$this->error   = '';
			$this->success = false;
		}
	}
}
