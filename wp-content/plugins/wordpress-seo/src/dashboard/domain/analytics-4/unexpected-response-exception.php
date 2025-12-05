<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Analytics_4;

use Exception;

/**
 * Exception for when an Analytics 4 request returns with an unexpected response.
 */
class Unexpected_Response_Exception extends Exception {

	/**
	 * Constructor of the exception.
	 */
	public function __construct() {
		parent::__construct( 'The response from Google Site Kit did not have an expected format.', 400 );
	}
}
