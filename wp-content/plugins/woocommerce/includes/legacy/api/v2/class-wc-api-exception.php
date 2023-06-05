<?php
/**
 * WooCommerce API Exception Class
 *
 * Extends Exception to provide additional data
 *
 * @author      WooThemes
 * @category    API
 * @package     WooCommerce\RestApi
 * @since       2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_API_Exception extends Exception {

	/** @var string sanitized error code */
	protected $error_code;

	/**
	 * Setup exception, requires 3 params:
	 *
	 * error code - machine-readable, e.g. `woocommerce_invalid_product_id`
	 * error message - friendly message, e.g. 'Product ID is invalid'
	 * http status code - proper HTTP status code to respond with, e.g. 400
	 *
	 * @since 2.2
	 * @param string $error_code
	 * @param string $error_message user-friendly translated error message
	 * @param int $http_status_code HTTP status code to respond with
	 */
	public function __construct( $error_code, $error_message, $http_status_code ) {
		$this->error_code = $error_code;
		parent::__construct( $error_message, $http_status_code );
	}

	/**
	 * Returns the error code
	 *
	 * @since 2.2
	 * @return string
	 */
	public function getErrorCode() {
		return $this->error_code;
	}
}
