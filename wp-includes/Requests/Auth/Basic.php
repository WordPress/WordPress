<?php
/**
 * Basic Authentication provider
 *
 * @package Requests\Authentication
 */

namespace WpOrg\Requests\Auth;

use WpOrg\Requests\Auth;
use WpOrg\Requests\Exception\ArgumentCount;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Hooks;

/**
 * Basic Authentication provider
 *
 * Provides a handler for Basic HTTP authentication via the Authorization
 * header.
 *
 * @package Requests\Authentication
 */
class Basic implements Auth {
	/**
	 * Username
	 *
	 * @var string
	 */
	public $user;

	/**
	 * Password
	 *
	 * @var string
	 */
	public $pass;

	/**
	 * Constructor
	 *
	 * @since 2.0 Throws an `InvalidArgument` exception.
	 * @since 2.0 Throws an `ArgumentCount` exception instead of the Requests base `Exception.
	 *
	 * @param array|null $args Array of user and password. Must have exactly two elements
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed argument is not an array or null.
	 * @throws \WpOrg\Requests\Exception\ArgumentCount   On incorrect number of array elements (`authbasicbadargs`).
	 */
	public function __construct($args = null) {
		if (is_array($args)) {
			if (count($args) !== 2) {
				throw ArgumentCount::create('an array with exactly two elements', count($args), 'authbasicbadargs');
			}

			list($this->user, $this->pass) = $args;
			return;
		}

		if ($args !== null) {
			throw InvalidArgument::create(1, '$args', 'array|null', gettype($args));
		}
	}

	/**
	 * Register the necessary callbacks
	 *
	 * @see \WpOrg\Requests\Auth\Basic::curl_before_send()
	 * @see \WpOrg\Requests\Auth\Basic::fsockopen_header()
	 * @param \WpOrg\Requests\Hooks $hooks Hook system
	 */
	public function register(Hooks $hooks) {
		$hooks->register('curl.before_send', [$this, 'curl_before_send']);
		$hooks->register('fsockopen.after_headers', [$this, 'fsockopen_header']);
	}

	/**
	 * Set cURL parameters before the data is sent
	 *
	 * @param resource|\CurlHandle $handle cURL handle
	 */
	public function curl_before_send(&$handle) {
		curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($handle, CURLOPT_USERPWD, $this->getAuthString());
	}

	/**
	 * Add extra headers to the request before sending
	 *
	 * @param string $out HTTP header string
	 */
	public function fsockopen_header(&$out) {
		$out .= sprintf("Authorization: Basic %s\r\n", base64_encode($this->getAuthString()));
	}

	/**
	 * Get the authentication string (user:pass)
	 *
	 * @return string
	 */
	public function getAuthString() {
		return $this->user . ':' . $this->pass;
	}
}
