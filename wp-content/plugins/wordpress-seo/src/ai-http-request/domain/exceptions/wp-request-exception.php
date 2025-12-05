<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions;

use Throwable;
/**
 * Class to manage an error response in wp_remote_*() requests.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class WP_Request_Exception extends Remote_Request_Exception {

	/**
	 * WP_Request_Exception constructor.
	 *
	 * @param string          $message  The error message.
	 * @param Throwable| null $previous The previously thrown exception.
	 */
	public function __construct( $message = '', $previous = null ) {
		parent::__construct( $message, 400, 'WP_HTTP_REQUEST_ERROR', $previous );
	}
}
