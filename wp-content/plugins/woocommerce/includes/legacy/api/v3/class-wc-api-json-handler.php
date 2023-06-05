<?php
/**
 * WooCommerce API
 *
 * Handles parsing JSON request bodies and generating JSON responses
 *
 * @author      WooThemes
 * @category    API
 * @package     WooCommerce\RestApi
 * @since       2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_API_JSON_Handler implements WC_API_Handler {

	/**
	 * Get the content type for the response
	 *
	 * @since 2.1
	 * @return string
	 */
	public function get_content_type() {

		return sprintf( '%s; charset=%s', isset( $_GET['_jsonp'] ) ? 'application/javascript' : 'application/json', get_option( 'blog_charset' ) );
	}

	/**
	 * Parse the raw request body entity
	 *
	 * @since 2.1
	 * @param string $body the raw request body
	 * @return array|mixed
	 */
	public function parse_body( $body ) {

		return json_decode( $body, true );
	}

	/**
	 * Generate a JSON response given an array of data
	 *
	 * @since 2.1
	 * @param array $data the response data
	 * @return string
	 */
	public function generate_response( $data ) {
		if ( isset( $_GET['_jsonp'] ) ) {

			if ( ! apply_filters( 'woocommerce_api_jsonp_enabled', true ) ) {
				WC()->api->server->send_status( 400 );
				return wp_json_encode( array( array( 'code' => 'woocommerce_api_jsonp_disabled', 'message' => __( 'JSONP support is disabled on this site', 'woocommerce' ) ) ) );
			}

			$jsonp_callback = $_GET['_jsonp'];

			if ( ! wp_check_jsonp_callback( $jsonp_callback ) ) {
				WC()->api->server->send_status( 400 );
				return wp_json_encode( array( array( 'code' => 'woocommerce_api_jsonp_callback_invalid', __( 'The JSONP callback function is invalid', 'woocommerce' ) ) ) );
			}

			WC()->api->server->header( 'X-Content-Type-Options', 'nosniff' );

			// Prepend '/**/' to mitigate possible JSONP Flash attacks.
			// https://miki.it/blog/2014/7/8/abusing-jsonp-with-rosetta-flash/
			return '/**/' . $jsonp_callback . '(' . wp_json_encode( $data ) . ')';
		}

		return wp_json_encode( $data );
	}
}
