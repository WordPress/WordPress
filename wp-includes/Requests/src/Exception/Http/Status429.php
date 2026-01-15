<?php
/**
 * Exception for 429 Too Many Requests responses
 *
 * @link https://tools.ietf.org/html/draft-nottingham-http-new-status-04
 *
 * @package Requests\Exceptions
 */

namespace WpOrg\Requests\Exception\Http;

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 429 Too Many Requests responses
 *
 * @link https://tools.ietf.org/html/draft-nottingham-http-new-status-04
 *
 * @package Requests\Exceptions
 */
final class Status429 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 429;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Too Many Requests';
}
