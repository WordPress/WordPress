<?php
/**
 * Cookie holder object
 *
 * @package Requests\Cookies
 */

namespace WpOrg\Requests\Cookie;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use ReturnTypeWillChange;
use WpOrg\Requests\Cookie;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\HookManager;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Response;

/**
 * Cookie holder object
 *
 * @package Requests\Cookies
 */
class Jar implements ArrayAccess, IteratorAggregate {
	/**
	 * Actual item data
	 *
	 * @var array
	 */
	protected $cookies = [];

	/**
	 * Create a new jar
	 *
	 * @param array $cookies Existing cookie values
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed argument is not an array.
	 */
	public function __construct($cookies = []) {
		if (is_array($cookies) === false) {
			throw InvalidArgument::create(1, '$cookies', 'array', gettype($cookies));
		}

		$this->cookies = $cookies;
	}

	/**
	 * Normalise cookie data into a \WpOrg\Requests\Cookie
	 *
	 * @param string|\WpOrg\Requests\Cookie $cookie Cookie header value, possibly pre-parsed (object).
	 * @param string                        $key    Optional. The name for this cookie.
	 * @return \WpOrg\Requests\Cookie
	 */
	public function normalize_cookie($cookie, $key = '') {
		if ($cookie instanceof Cookie) {
			return $cookie;
		}

		return Cookie::parse($cookie, $key);
	}

	/**
	 * Check if the given item exists
	 *
	 * @param string $offset Item key
	 * @return boolean Does the item exist?
	 */
	#[ReturnTypeWillChange]
	public function offsetExists($offset) {
		return isset($this->cookies[$offset]);
	}

	/**
	 * Get the value for the item
	 *
	 * @param string $offset Item key
	 * @return string|null Item value (null if offsetExists is false)
	 */
	#[ReturnTypeWillChange]
	public function offsetGet($offset) {
		if (!isset($this->cookies[$offset])) {
			return null;
		}

		return $this->cookies[$offset];
	}

	/**
	 * Set the given item
	 *
	 * @param string $offset Item name
	 * @param string $value Item value
	 *
	 * @throws \WpOrg\Requests\Exception On attempting to use dictionary as list (`invalidset`)
	 */
	#[ReturnTypeWillChange]
	public function offsetSet($offset, $value) {
		if ($offset === null) {
			throw new Exception('Object is a dictionary, not a list', 'invalidset');
		}

		$this->cookies[$offset] = $value;
	}

	/**
	 * Unset the given header
	 *
	 * @param string $offset The key for the item to unset.
	 */
	#[ReturnTypeWillChange]
	public function offsetUnset($offset) {
		unset($this->cookies[$offset]);
	}

	/**
	 * Get an iterator for the data
	 *
	 * @return \ArrayIterator
	 */
	#[ReturnTypeWillChange]
	public function getIterator() {
		return new ArrayIterator($this->cookies);
	}

	/**
	 * Register the cookie handler with the request's hooking system
	 *
	 * @param \WpOrg\Requests\HookManager $hooks Hooking system
	 */
	public function register(HookManager $hooks) {
		$hooks->register('requests.before_request', [$this, 'before_request']);
		$hooks->register('requests.before_redirect_check', [$this, 'before_redirect_check']);
	}

	/**
	 * Add Cookie header to a request if we have any
	 *
	 * As per RFC 6265, cookies are separated by '; '
	 *
	 * @param string $url
	 * @param array $headers
	 * @param array $data
	 * @param string $type
	 * @param array $options
	 */
	public function before_request($url, &$headers, &$data, &$type, &$options) {
		if (!$url instanceof Iri) {
			$url = new Iri($url);
		}

		if (!empty($this->cookies)) {
			$cookies = [];
			foreach ($this->cookies as $key => $cookie) {
				$cookie = $this->normalize_cookie($cookie, $key);

				// Skip expired cookies
				if ($cookie->is_expired()) {
					continue;
				}

				if ($cookie->domain_matches($url->host)) {
					$cookies[] = $cookie->format_for_header();
				}
			}

			$headers['Cookie'] = implode('; ', $cookies);
		}
	}

	/**
	 * Parse all cookies from a response and attach them to the response
	 *
	 * @param \WpOrg\Requests\Response $response Response as received.
	 */
	public function before_redirect_check(Response $response) {
		$url = $response->url;
		if (!$url instanceof Iri) {
			$url = new Iri($url);
		}

		$cookies           = Cookie::parse_from_headers($response->headers, $url);
		$this->cookies     = array_merge($this->cookies, $cookies);
		$response->cookies = $this;
	}
}
