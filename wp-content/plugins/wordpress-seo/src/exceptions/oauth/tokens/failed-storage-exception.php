<?php

namespace Yoast\WP\SEO\Exceptions\OAuth\Tokens;

use Exception;

/**
 * Class Failed_Storage_Exception
 */
class Failed_Storage_Exception extends Exception {

	public const DEFAULT_MESSAGE = 'Token storing failed. Please try again.';

	/**
	 * Failed_Storage_Exception constructor.
	 *
	 * @param string $reason The reason why token storage failed. Optional.
	 */
	public function __construct( $reason = '' ) {
		$message = ( $reason ) ? \sprintf( 'Token storing failed. Reason: %s. Please try again', $reason ) : self::DEFAULT_MESSAGE;

		parent::__construct( $message, 500 );
	}
}
