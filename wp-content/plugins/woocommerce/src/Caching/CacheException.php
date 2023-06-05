<?php

namespace Automattic\WooCommerce\Caching;

/**
 * Exception thrown by classes derived from ObjectCache.
 */
class CacheException extends \Exception {

	/**
	 * Error messages.
	 *
	 * @var array
	 */
	private $errors;

	/**
	 * The object that threw the exception.
	 *
	 * @var ObjectCache
	 */
	private $thrower;

	/**
	 * The id of the cached object, if available.
	 *
	 * @var int|string|null
	 */
	private $cached_id;

	/**
	 * Creates a new instance of the class.
	 *
	 * @param string          $message The exception message.
	 * @param ObjectCache     $thrower The object that is throwing the exception.
	 * @param int|string|null $cached_id The involved cached object id, if available.
	 * @param array|null      $errors An array of error messages, if available.
	 * @param mixed           $code An error code, if available.
	 * @param \Throwable|null $previous The previous exception, if available.
	 */
	public function __construct( string $message, ObjectCache $thrower, $cached_id = null, ?array $errors = null, $code = 0, \Throwable $previous = null ) {
		$this->errors    = $errors ?? array();
		$this->thrower   = $thrower;
		$this->cached_id = $cached_id;

		parent::__construct( $message, $code, $previous );
	}

	/**
	 * Get a string representation of the exception object.
	 *
	 * @return string String representation of the exception object.
	 */
	public function __toString(): string {
		$cached_id_part = $this->cached_id ? ", id: {$this->cached_id}" : '';
		return "CacheException: [{$this->thrower->get_object_type()}{$cached_id_part}]: {$this->message}";
	}

	/**
	 * Gets the array of error messages passed to the exception constructor.
	 *
	 * @return array Error messages passed to the exception constructor.
	 */
	public function get_errors(): array {
		return $this->errors;
	}

	/**
	 * Gets the object that threw the exception as passed to the exception constructor.
	 *
	 * @return object The object that threw the exception.
	 */
	public function get_thrower(): object {
		return $this->thrower;
	}

	/**
	 * Gets the id of the cached object as passed to the exception constructor.
	 *
	 * @return int|string|null The id of the cached object.
	 */
	public function get_cached_id() {
		return $this->cached_id;
	}
}
