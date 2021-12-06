<?php
/**
 * Exception for 403 Forbidden responses
 *
 * @package Requests
 */

/**
 * Exception for 403 Forbidden responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_403 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 403;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Forbidden';
}
