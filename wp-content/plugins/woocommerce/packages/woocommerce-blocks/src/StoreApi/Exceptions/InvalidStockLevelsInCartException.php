<?php
namespace Automattic\WooCommerce\StoreApi\Exceptions;

use WP_Error;

/**
 * InvalidStockLevelsInCartException class.
 *
 * This exception is thrown if any items are out of stock after each product on a draft order has been stock checked.
 */
class InvalidStockLevelsInCartException extends \Exception {
	/**
	 * Sanitized error code.
	 *
	 * @var string
	 */
	public $error_code;

	/**
	 * Additional error data.
	 *
	 * @var array
	 */
	public $additional_data = [];

	/**
	 * All errors to display to the user.
	 *
	 * @var WP_Error
	 */
	public $error;

	/**
	 * Setup exception.
	 *
	 * @param string   $error_code      Machine-readable error code, e.g `woocommerce_invalid_product_id`.
	 * @param WP_Error $error           The WP_Error object containing all errors relating to stock availability.
	 * @param array    $additional_data Extra data (key value pairs) to expose in the error response.
	 */
	public function __construct( $error_code, $error, $additional_data = [] ) {
		$this->error_code      = $error_code;
		$this->error           = $error;
		$this->additional_data = array_filter( (array) $additional_data );
		parent::__construct( '', 409 );
	}

	/**
	 * Returns the error code.
	 *
	 * @return string
	 */
	public function getErrorCode() {
		return $this->error_code;
	}

	/**
	 * Returns the list of messages.
	 *
	 * @return WP_Error
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * Returns additional error data.
	 *
	 * @return array
	 */
	public function getAdditionalData() {
		return $this->additional_data;
	}
}
