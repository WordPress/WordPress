<?php

class Requests_Exception_Transport_cURL extends Requests_Exception_Transport {

	const EASY  = 'cURLEasy';
	const MULTI = 'cURLMulti';
	const SHARE = 'cURLShare';

	/**
	 * cURL error code
	 *
	 * @var integer
	 */
	protected $code = -1;

	/**
	 * Which type of cURL error
	 *
	 * EASY|MULTI|SHARE
	 *
	 * @var string
	 */
	protected $type = 'Unknown';

	/**
	 * Clear text error message
	 *
	 * @var string
	 */
	protected $reason = 'Unknown';

	public function __construct($message, $type, $data = null, $code = 0) {
		if ($type !== null) {
			$this->type = $type;
		}

		if ($code !== null) {
			$this->code = $code;
		}

		if ($message !== null) {
			$this->reason = $message;
		}

		$message = sprintf('%d %s', $this->code, $this->reason);
		parent::__construct($message, $this->type, $data, $this->code);
	}

	/**
	 * Get the error message
	 */
	public function getReason() {
		return $this->reason;
	}

}
