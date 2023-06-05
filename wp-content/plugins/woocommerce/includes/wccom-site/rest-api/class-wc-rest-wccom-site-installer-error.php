<?php
/**
 * WCCOM Site Installer Error Class
 *
 * @package WooCommerce\WCCom\API
 * @since   7.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WCCOM Site Installer Error Class
 */
class WC_REST_WCCOM_Site_Installer_Error extends Exception {

	/**
	 * Constructor for the Installer Error class.
	 *
	 * @param string $error_code Error code.
	 * @param string $error_message Error message.
	 * @param int    $http_code HTTP status code.
	 */
	public function __construct( $error_code, $error_message = null, $http_code = null ) {
		$this->error_code    = $error_code;
		$this->error_message = $error_message ?? WC_REST_WCCOM_Site_Installer_Error_Codes::ERROR_MESSAGES[ $error_code ] ?? '';
		$this->http_code     = $http_code ?? WC_REST_WCCOM_Site_Installer_Error_Codes::HTTP_CODES[ $error_code ] ?? 400;

		parent::__construct( $error_code );
	}

	/**
	 * Get the error code.
	 */
	public function get_error_code() {
		return $this->error_code;
	}

	/**
	 * Get the error message.
	 */
	public function get_error_message() {
		return $this->error_message;
	}

	/**
	 * Get the HTTP status code.
	 */
	public function get_http_code() {
		return $this->http_code;
	}
}
