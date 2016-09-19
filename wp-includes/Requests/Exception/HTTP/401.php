<?php
/**
 * Exception for 401 Unauthorized responses
 *
 * @package Requests
 */

/**
 * Exception for 401 Unauthorized responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_401 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 401;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Unauthorized';
}