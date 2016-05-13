<?php
/**
 * Exception for 415 Unsupported Media Type responses
 *
 * @package Requests
 */

/**
 * Exception for 415 Unsupported Media Type responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_415 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 415;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Unsupported Media Type';
}