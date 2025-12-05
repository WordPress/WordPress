<?php

namespace Yoast\WP\SEO\Exceptions\Importing;

use Exception;

/**
 * Class Aioseo_Validation_Exception
 */
class Aioseo_Validation_Exception extends Exception {

	/**
	 * Exception that is thrown whenever validation of the
	 * AIOSEO data structure has failed.
	 */
	public function __construct() {
		parent::__construct( \esc_html__( 'The validation of the AIOSEO data structure has failed.', 'wordpress-seo' ) );
	}
}
