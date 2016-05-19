<?php
/**
 * Exception for 511 Network Authentication Required responses
 *
 * @see http://tools.ietf.org/html/rfc6585
 * @package Requests
 */

/**
 * Exception for 511 Network Authentication Required responses
 *
 * @see http://tools.ietf.org/html/rfc6585
 * @package Requests
 */
class Requests_Exception_HTTP_511 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 511;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Network Authentication Required';
}