<?php
/**
 * WooCommerce Admin Helper API
 *
 * @package WooCommerce\Admin\Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Helper_API Class
 *
 * Provides a communication interface with the WooCommerce.com Helper API.
 */
class WC_Helper_API {
	/**
	 * Base path for API routes.
	 *
	 * @var $api_base
	 */
	public static $api_base;

	/**
	 * Load
	 *
	 * Allow devs to point the API base to a local API development or staging server.
	 * Note that sslverify will be turned off for the woocommerce.dev + WP_DEBUG combination.
	 * The URL can be changed on plugins_loaded before priority 10.
	 */
	public static function load() {
		self::$api_base = apply_filters( 'woocommerce_helper_api_base', 'https://woocommerce.com/wp-json/helper/1.0' );
	}

	/**
	 * Perform an HTTP request to the Helper API.
	 *
	 * @param string $endpoint The endpoint to request.
	 * @param array  $args Additional data for the request. Set authenticated to a truthy value to enable auth.
	 *
	 * @return array|WP_Error The response from wp_safe_remote_request()
	 */
	public static function request( $endpoint, $args = array() ) {
		if ( ! isset( $args['query_string'] ) ) {
			$args['query_string'] = '';
		}
		$url = self::url( $endpoint, $args['query_string'] );

		if ( ! empty( $args['authenticated'] ) ) {
			if ( ! self::_authenticate( $url, $args ) ) {
				return new WP_Error( 'authentication', 'Authentication failed.' );
			}
		}

		if ( ! isset( $args['user-agent'] ) ) {
			$args['user-agent'] = 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' );
		}

		/**
		 * Allow developers to filter the request args passed to wp_safe_remote_request().
		 * Useful to remove sslverify when working on a local api dev environment.
		 */
		$args = apply_filters( 'woocommerce_helper_api_request_args', $args, $endpoint );

		// TODO: Check response signatures on certain endpoints.
		return wp_safe_remote_request( $url, $args );
	}

	/**
	 * Adds authentication headers to an HTTP request.
	 *
	 * @param string $url The request URI.
	 * @param array  $args By-ref, the args that will be passed to wp_remote_request().
	 * @return bool Were the headers added?
	 */
	private static function _authenticate( &$url, &$args ) {
		$auth = WC_Helper_Options::get( 'auth' );

		if ( empty( $auth['access_token'] ) || empty( $auth['access_token_secret'] ) ) {
			return false;
		}

		$request_uri  = parse_url( $url, PHP_URL_PATH );
		$query_string = parse_url( $url, PHP_URL_QUERY );

		if ( is_string( $query_string ) ) {
			$request_uri .= '?' . $query_string;
		}

		$data = array(
			'host'        => parse_url( $url, PHP_URL_HOST ),
			'request_uri' => $request_uri,
			'method'      => ! empty( $args['method'] ) ? $args['method'] : 'GET',
		);

		if ( ! empty( $args['body'] ) ) {
			$data['body'] = $args['body'];
		}

		$signature = hash_hmac( 'sha256', json_encode( $data ), $auth['access_token_secret'] );
		if ( empty( $args['headers'] ) ) {
			$args['headers'] = array();
		}

		$headers         = array(
			'Authorization'   => 'Bearer ' . $auth['access_token'],
			'X-Woo-Signature' => $signature,
		);
		$args['headers'] = wp_parse_args( $headers, $args['headers'] );

		$url = add_query_arg(
			array(
				'token'     => $auth['access_token'],
				'signature' => $signature,
			),
			$url
		);

		return true;
	}

	/**
	 * Wrapper for self::request().
	 *
	 * @param string $endpoint The helper API endpoint to request.
	 * @param array  $args Arguments passed to wp_remote_request().
	 *
	 * @return array The response object from wp_safe_remote_request().
	 */
	public static function get( $endpoint, $args = array() ) {
		$args['method'] = 'GET';
		return self::request( $endpoint, $args );
	}

	/**
	 * Wrapper for self::request().
	 *
	 * @param string $endpoint The helper API endpoint to request.
	 * @param array  $args Arguments passed to wp_remote_request().
	 *
	 * @return array The response object from wp_safe_remote_request().
	 */
	public static function post( $endpoint, $args = array() ) {
		$args['method'] = 'POST';
		return self::request( $endpoint, $args );
	}

	/**
	 * Wrapper for self::request().
	 *
	 * @param string $endpoint The helper API endpoint to request.
	 * @param array  $args Arguments passed to wp_remote_request().
	 *
	 * @return array The response object from wp_safe_remote_request().
	 */
	public static function put( $endpoint, $args = array() ) {
		$args['method'] = 'PUT';
		return self::request( $endpoint, $args );
	}

	/**
	 * Using the API base, form a request URL from a given endpoint.
	 *
	 * @param string $endpoint The endpoint to request.
	 * @param string $query_string Optional query string to append to the URL.
	 *
	 * @return string The absolute endpoint URL.
	 */
	public static function url( $endpoint, $query_string = '' ) {
		$endpoint = ltrim( $endpoint, '/' );
		$endpoint = sprintf( '%s/%s/%s', self::$api_base, $endpoint, $query_string );
		$endpoint = esc_url_raw( $endpoint );
		$endpoint = rtrim( $endpoint, '/' );
		return $endpoint;
	}
}

WC_Helper_API::load();
