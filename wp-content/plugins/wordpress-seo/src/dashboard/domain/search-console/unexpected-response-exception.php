<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Search_Console;

use Exception;

/**
 * Exception for when a Search Console request returns with an unexpected response.
 */
class Unexpected_Response_Exception extends Exception {

	/**
	 * Constructor of the exception.
	 */
	public function __construct() {
		parent::__construct( 'The response from Google Site Kit did not have an expected format.', 400 );
	}
}
