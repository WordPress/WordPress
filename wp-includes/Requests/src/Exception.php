<?php
/**
 * Exception for HTTP requests
 *
 * @package Requests\Exceptions
 */

namespace WpOrg\Requests;

use Exception as PHPException;

/**
 * Exception for HTTP requests
 *
 * @package Requests\Exceptions
 */
class Exception extends PHPException {
	/**
	 * Type of exception
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Data associated with the exception
	 *
	 * @var mixed
	 */
	protected $data;

	/**
	 * Create a new exception
	 *
	 * @param string $message Exception message
	 * @param string $type Exception type
	 * @param mixed $data Associated data
	 * @param integer $code Exception numerical code, if applicable
	 */
	public function __construct($message, $type, $data = null, $code = 0) {
		parent::__construct($message, $code);

		$this->type = $type;
		$this->data = $data;
	}

	/**
	 * Like {@see \Exception::getCode()}, but a string code.
	 *
	 * @codeCoverageIgnore
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Gives any relevant data
	 *
	 * @codeCoverageIgnore
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}
}
