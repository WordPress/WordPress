<?php
/**
 * Exception for 505 HTTP Version Not Supported responses
 *
 * @package Requests
 */

/**
 * Exception for 505 HTTP Version Not Supported responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_505 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 505;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'HTTP Version Not Supported';
}
