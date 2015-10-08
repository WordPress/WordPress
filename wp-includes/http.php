<?php
/**
 * Core HTTP Request API
 *
 * Standardizes the HTTP requests for WordPress. Handles cookies, gzip encoding and decoding, chunk
 * decoding, if HTTP 1.1 and various other difficult HTTP protocol implementations.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7.0
 */

/** Core HTTP API functionality */
require_once( ABSPATH . WPINC . '/http-functions.php' );

/** WP_Http class */
require_once( ABSPATH . WPINC . '/class-http.php' );

/** WP_Http_Streams class */
require_once( ABSPATH . WPINC . '/class-wp-http-streams.php' );

/** WP_Http_Curl transport class */
require_once( ABSPATH . WPINC . '/class-wp-http-curl.php' );

/** WP_HTTP_Proxy transport class */
require_once( ABSPATH . WPINC . '/class-wp-http-proxy.php' );

/** WP_Http_Cookie class */
require_once( ABSPATH . WPINC . '/class-wp-http-cookie.php' );

/** WP_Http_Encoding class */
require_once( ABSPATH . WPINC . '/class-wp-http-encoding.php' );

/** WP_HTTP_Response class */
require_once( ABSPATH . WPINC . '/class-wp-http-response.php' );
