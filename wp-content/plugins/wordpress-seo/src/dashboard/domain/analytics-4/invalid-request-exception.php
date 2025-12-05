<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Analytics_4;

use Exception;

/**
 * Exception for when an Analytics 4 request is invalid.
 */
class Invalid_Request_Exception extends Exception {

	/**
	 * Constructor of the exception.
	 *
	 * @param string $error_message The error message of the request.
	 */
	public function __construct( $error_message ) {
		parent::__construct( 'The Analytics 4 request is invalid: ' . $error_message, 400 );
	}
}
