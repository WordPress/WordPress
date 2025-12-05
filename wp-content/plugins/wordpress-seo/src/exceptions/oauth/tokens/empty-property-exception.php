<?php

namespace Yoast\WP\SEO\Exceptions\OAuth\Tokens;

use Exception;

/**
 * Class Empty_Property_Exception
 */
class Empty_Property_Exception extends Exception {

	/**
	 * Empty_Property_Exception constructor.
	 *
	 * @param string $property The property that is empty.
	 */
	public function __construct( $property ) {
		parent::__construct( \sprintf( 'Token creation failed. Property `%s` cannot be empty.', $property ), 400 );
	}
}
