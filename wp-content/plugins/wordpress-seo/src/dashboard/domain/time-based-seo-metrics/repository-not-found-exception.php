<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Time_Based_SEO_Metrics;

use Exception;

/**
 * Exception for when the repository for the given widget are not found.
 */
class Repository_Not_Found_Exception extends Exception {

	/**
	 * Constructor of the exception.
	 */
	public function __construct() {
		parent::__construct( 'Repository not found', 404 );
	}
}
