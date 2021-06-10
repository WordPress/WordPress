<?php
/**
 * Exception for 412 Precondition Failed responses
 *
 * @package Requests
 */

/**
 * Exception for 412 Precondition Failed responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_412 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 412;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Precondition Failed';
}
