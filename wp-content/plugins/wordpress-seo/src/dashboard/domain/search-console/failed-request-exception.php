<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Search_Console;

use Exception;

/**
 * Exception for when a search console request fails.
 */
class Failed_Request_Exception extends Exception {

	/**
	 * Constructor of the exception.
	 *
	 * @param string $error_message     The error message of the request.
	 * @param int    $error_status_code The error status code of the request.
	 */
	public function __construct( $error_message, $error_status_code ) {
		parent::__construct( 'The Search Console request failed: ' . $error_message, $error_status_code );
	}
}
