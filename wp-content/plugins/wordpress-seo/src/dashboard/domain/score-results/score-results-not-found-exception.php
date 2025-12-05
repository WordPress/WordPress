<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Score_Results;

use Exception;

/**
 * Exception for when score results are not found.
 */
class Score_Results_Not_Found_Exception extends Exception {

	/**
	 * Constructor of the exception.
	 */
	public function __construct() {
		parent::__construct( 'Score results not found', 500 );
	}
}
