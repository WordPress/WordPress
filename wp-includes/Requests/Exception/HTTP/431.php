<?php
/**
 * Exception for 431 Request Header Fields Too Large responses
 *
 * @see http://tools.ietf.org/html/rfc6585
 * @package Requests
 */

/**
 * Exception for 431 Request Header Fields Too Large responses
 *
 * @see http://tools.ietf.org/html/rfc6585
 * @package Requests
 */
class Requests_Exception_HTTP_431 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 431;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Request Header Fields Too Large';
}