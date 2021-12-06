<?php
/**
 * Exception based on HTTP response
 *
 * @package Requests
 */

/**
 * Exception based on HTTP response
 *
 * @package Requests
 */
class Requests_Exception_HTTP extends Requests_Exception {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 0;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Unknown';

	/**
	 * Create a new exception
	 *
	 * There is no mechanism to pass in the status code, as this is set by the
	 * subclass used. Reason phrases can vary, however.
	 *
	 * @param string|null $reason Reason phrase
	 * @param mixed $data Associated data
	 */
	public function __construct($reason = null, $data = null) {
		if ($reason !== null) {
			$this->reason = $reason;
		}

		$message = sprintf('%d %s', $this->code, $this->reason);
		parent::__construct($message, 'httpresponse', $data, $this->code);
	}

	/**
	 * Get the status message
	 */
	public function getReason() {
		return $this->reason;
	}

	/**
	 * Get the correct exception class for a given error code
	 *
	 * @param int|bool $code HTTP status code, or false if unavailable
	 * @return string Exception class name to use
	 */
	public static function get_class($code) {
		if (!$code) {
			return 'Requests_Exception_HTTP_Unknown';
		}

		$class = sprintf('Requests_Exception_HTTP_%d', $code);
		if (class_exists($class)) {
			return $class;
		}

		return 'Requests_Exception_HTTP_Unknown';
	}
}
