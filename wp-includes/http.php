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

require_once( ABSPATH . WPINC . '/http-functions.php' );
require_once( ABSPATH . WPINC . '/class-http.php' );
require_once( ABSPATH . WPINC . '/class-wp-http-streams.php' );
require_once( ABSPATH . WPINC . '/class-wp-http-curl.php' );
require_once( ABSPATH . WPINC . '/class-wp-http-proxy.php' );
require_once( ABSPATH . WPINC . '/class-wp-http-cookie.php' );
require_once( ABSPATH . WPINC . '/class-wp-http-encoding.php' );