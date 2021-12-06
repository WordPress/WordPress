<?php
/**
 * Exception for 411 Length Required responses
 *
 * @package Requests
 */

/**
 * Exception for 411 Length Required responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_411 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 411;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Length Required';
}
