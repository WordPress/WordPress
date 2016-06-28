<?php
/**
 * Exception for 306 Switch Proxy responses
 *
 * @package Requests
 */

/**
 * Exception for 306 Switch Proxy responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_306 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 306;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Switch Proxy';
}
