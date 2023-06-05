<?php
/**
 * WooCommerce API
 *
 * Defines an interface that API request/response handlers should implement
 *
 * @author      WooThemes
 * @category    API
 * @package     WooCommerce\RestApi
 * @since       2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

interface WC_API_Handler {

	/**
	 * Get the content type for the response
	 *
	 * This should return the proper HTTP content-type for the response
	 *
	 * @since 2.1
	 * @return string
	 */
	public function get_content_type();

	/**
	 * Parse the raw request body entity into an array
	 *
	 * @since 2.1
	 * @param string $data
	 * @return array
	 */
	public function parse_body( $data );

	/**
	 * Generate a response from an array of data
	 *
	 * @since 2.1
	 * @param array $data
	 * @return string
	 */
	public function generate_response( $data );

}
