<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Llms_Txt\Domain\Available_Posts;

use Exception;

/**
 * Exception for when the post type asked is invalid.
 */
class Invalid_Post_Type_Exception extends Exception {

	/**
	 * Constructor of the exception.
	 */
	public function __construct() {
		parent::__construct( 'The post type asked is not valid', 400 );
	}
}
