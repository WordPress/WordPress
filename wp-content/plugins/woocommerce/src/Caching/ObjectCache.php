<?php

namespace Automattic\WooCommerce\Caching;

/**
 * Base class for caching objects (or associative arrays) that have a unique identifier.
 * At the very least, derived classes need to implement the 'get_object_type' method,
 * but usually it will be convenient to override some of the other protected members.
 *
 * The actual caching is delegated to an instance of CacheEngine. By default WpCacheEngine is used,
 * but a different engine can be used by either overriding the get_cache_engine_instance method
 * or capturing the wc_object_cache_get_engine filter.
 *
 * Objects are identified by ids that are either integers or strings. The actual cache keys passed
 * to the cache engine will be prefixed with the object type and a random string. The 'flush' operation
 * just forces the generation a new prefix and lets the old cached objects expire.
 */
abstract class ObjectCache {

	/**
	 * Expiration value to be passed to 'set' to use the value of $default_expiration.
	 */
	public const DEFAULT_EXPIRATION = -1;

	/**
	 * Maximum expiration time value, in seconds, that can be passed to 'set'.
	 */
	public const MAX_EXPIRATION = MONTH_IN_SECONDS;

	/**
	 * This needs to be set in each derived class.
	 *
	 * @var string
	 */
	private $object_type;

	/**
	 * Default value for the duration of the objects in the cache, in seconds
	 * (may not be used depending on the cache engine used WordPress cache implementation).
	 *
	 * @var int
	 */
	protected $default_expiration = HOUR_IN_SECONDS;

	/**
	 * Temporarily used when retrieving data in 'get'.
	 *
	 * @var array
	 */
	private $last_cached_data;

	/**
	 * The cache engine to use.
	 *
	 * @var ?CacheEngine
	 */
	private $cache_engine = null;

	/**
	 * Gets an identifier for the types of objects cached by this class.
	 * This identifier will be used to compose the keys passed to the cache engine,
	 * to the name of the option that stores the cache prefix, and the names of the hooks used.
	 * It must be unique for each class inheriting from ObjectCache.
	 *
	 * @return string
	 */
	abstract public function get_object_type(): string;

	/**
	 * Creates a new instance of the class.
	 *
	 * @throws CacheException If get_object_type returns null or an empty string.
	 */
	public function __construct() {
		$this->object_type = $this->get_object_type();
		if ( empty( $this->object_type ) ) {
			throw new CacheException( 'Class ' . get_class( $this ) . ' returns an empty value for get_object_type', $this );
		}
	}

	/**
	 * Get the default expiration time for cached objects, in seconds.
	 *
	 * @return int
	 */
	public function get_default_expiration_value(): int {
		return $this->default_expiration;
	}

	/**
	 * Get the cache engine to use and cache it internally.
	 *
	 * @return CacheEngine
	 */
	private function get_cache_engine(): CacheEngine {
		if ( null === $this->cache_engine ) {
			$engine = $this->get_cache_engine_instance();

			/**
			 * Filters the underlying cache engine to be used by an instance of ObjectCache.
			 *
			 * @since 7.4.0
			 *
			 * @param CacheEngine $engine The cache engine to be used by default.
			 * @param ObjectCache $cache_instance The instance of ObjectCache that will use the cache engine.
			 * @returns CacheEngine The actual cache engine that will be used.
			 */
			$this->cache_engine = apply_filters( 'wc_object_cache_get_engine', $engine, $this );
		}
		return $this->cache_engine;
	}

	/**
	 * Add an object to the cache, or update an already cached object.
	 *
	 * @param object|array    $object The object to be cached.
	 * @param int|string|null $id Id of the object to be cached, if null, get_object_id will be used to get it.
	 * @param int             $expiration Expiration of the cached data in seconds from the current time, or DEFAULT_EXPIRATION to use the default value.
	 * @return bool True on success, false on error.
	 * @throws CacheException Invalid parameter, or null id was passed and get_object_id returns null too.
	 */
	public function set( $object, $id = null, int $expiration = self::DEFAULT_EXPIRATION ): bool {
		if ( null === $object ) {
			throw new CacheException( "Can't cache a null value", $this, $id );
		}

		if ( ! is_array( $object ) && ! is_object( $object ) ) {
			throw new CacheException( "Can't cache a non-object, non-array value", $this, $id );
		}

		if ( ! is_string( $id ) && ! is_int( $id ) && ! is_null( $id ) ) {
			throw new CacheException( "Object id must be an int, a string, or null for 'set'", $this, $id );
		}

		$this->verify_expiration_value( $expiration );

		$errors = $this->validate( $object );
		if ( ! is_null( $errors ) ) {
			try {
				$id = $this->get_id_from_object_if_null( $object, $id );
			} catch ( \Throwable $ex ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
				// Nothing else to do, we won't be able to add any significant object id to the CacheException and that's it.
			}

			if ( count( $errors ) === 1 ) {
				throw new CacheException( 'Object validation/serialization failed: ' . $errors[0], $this, $id, $errors );
			} elseif ( ! empty( $errors ) ) {
				throw new CacheException( 'Object validation/serialization failed', $this, $id, $errors );
			}
		}

		$id = $this->get_id_from_object_if_null( $object, $id );

		$this->last_cached_data = $object;
		return $this->get_cache_engine()->cache_object(
			$id,
			$object,
			self::DEFAULT_EXPIRATION === $expiration ? $this->default_expiration : $expiration,
			$this->get_object_type()
		);
	}

	/**
	 * Update an object in the cache, but only if an object is already cached with the same id.
	 *
	 * @param object|array    $object The new object that will replace the already cached one.
	 * @param int|string|null $id Id of the object to be cached, if null, get_object_id will be used to get it.
	 * @param int             $expiration Expiration of the cached data in seconds from the current time, or DEFAULT_EXPIRATION to use the default value.
	 * @return bool True on success, false on error or if no object wiith the supplied id was cached.
	 * @throws CacheException Invalid parameter, or null id was passed and get_object_id returns null too.
	 */
	public function update_if_cached( $object, $id = null, int $expiration = self::DEFAULT_EXPIRATION ): bool {
		$id = $this->get_id_from_object_if_null( $object, $id );

		if ( ! $this->is_cached( $id ) ) {
			return false;
		}

		return $this->set( $object, $id, $expiration );
	}

	/**
	 * Get the id from an object if the id itself is null.
	 *
	 * @param object|array    $object The object to get the id from.
	 * @param int|string|null $id An object id or null.
	 *
	 * @return int|string|null Passed $id if it wasn't null, otherwise id obtained from $object using get_object_id.
	 *
	 * @throws CacheException Passed $id is null and get_object_id returned null too.
	 */
	private function get_id_from_object_if_null( $object, $id ) {
		if ( null === $id ) {
			$id = $this->get_object_id( $object );
			if ( null === $id ) {
				throw new CacheException( "Null id supplied and the cache class doesn't implement get_object_id", $this );
			}
		}

		return $id;
	}

	/**
	 * Check if the given expiration time value is valid, throw an exception if not.
	 *
	 * @param int $expiration Expiration time to check.
	 * @return void
	 * @throws CacheException Expiration time is negative or higher than MAX_EXPIRATION.
	 */
	private function verify_expiration_value( int $expiration ): void {
		if ( self::DEFAULT_EXPIRATION !== $expiration && ( ( $expiration < 1 ) || ( $expiration > self::MAX_EXPIRATION ) ) ) {
			throw new CacheException( 'Invalid expiration value, must be ObjectCache::DEFAULT_EXPIRATION or a value between 1 and ObjectCache::MAX_EXPIRATION', $this );
		}
	}

	/**
	 * Retrieve a cached object, and if no object is cached with the given id,
	 * try to get one via get_from_datastore method or by supplying a callback and then cache it.
	 *
	 * If you want to provide a callable but still use the default expiration value,
	 * pass "ObjectCache::DEFAULT_EXPIRATION" as the second parameter.
	 *
	 * @param int|string    $id The id of the object to retrieve.
	 * @param int           $expiration Expiration of the cached data in seconds from the current time, used if an object is retrieved from datastore and cached.
	 * @param callable|null $get_from_datastore_callback Optional callback to get the object if it's not cached, it must return an object/array or null.
	 * @return object|array|null Cached object, or null if it's not cached and can't be retrieved from datastore or via callback.
	 * @throws CacheException Invalid id parameter.
	 */
	public function get( $id, int $expiration = self::DEFAULT_EXPIRATION, callable $get_from_datastore_callback = null ) {
		if ( ! is_string( $id ) && ! is_int( $id ) ) {
			throw new CacheException( "Object id must be an int or a string for 'get'", $this );
		}

		$this->verify_expiration_value( $expiration );

		$data = $this->get_cache_engine()->get_cached_object( $id, $this->get_object_type() );
		if ( null === $data ) {
			$object = null;
			if ( $get_from_datastore_callback ) {
				$object = $get_from_datastore_callback( $id );
			}

			if ( null === $object ) {
				return null;
			}
			$this->set( $object, $id, $expiration );
			$data = $this->last_cached_data;
		}

		return $data;
	}

	/**
	 * Remove an object from the cache.
	 *
	 * @param int|string $id The id of the object to remove.
	 * @return bool True if the object is removed from the cache successfully, false otherwise (because the object wasn't cached or for other reason).
	 */
	public function remove( $id ): bool {
		return $this->get_cache_engine()->delete_cached_object( $id, $this->get_object_type() );
	}

	/**
	 * Remove all the objects from the cache.
	 *
	 * @return bool True on success, false on error.
	 */
	public function flush(): bool {
		return $this->get_cache_engine()->delete_cache_group( $this->get_object_type() );
	}

	/**
	 * Is a given object cached?
	 *
	 * @param int|string $id The id of the object to check.
	 * @return bool True if there's a cached object with the specified id.
	 */
	public function is_cached( $id ): bool {
		return $this->get_cache_engine()->is_cached( $id, $this->get_object_type() );
	}

	/**
	 * Get the id of an object. This is used by 'set' when a null id is passed.
	 * If the object id can't be determined the method must return null.
	 *
	 * @param array|object $object The object to get the id for.
	 * @return int|string|null
	 */
	abstract protected function get_object_id( $object );

	/**
	 * Validate an object before it's cached.
	 *
	 * @param array|object $object Object to validate.
	 * @return array|null An array with validation error messages, null or an empty array if there are no errors.
	 */
	abstract protected function validate( $object ): ?array;

	/**
	 * Get the instance of the cache engine to use.
	 *
	 * @return CacheEngine
	 */
	protected function get_cache_engine_instance(): CacheEngine {
		return wc_get_container()->get( WPCacheEngine::class );
	}

	/**
	 * Get a random string to be used to compose the cache key prefix.
	 * It should return a different string each time.
	 *
	 * @return string
	 */
	protected function get_random_string(): string {
		return dechex( microtime( true ) * 1000 ) . bin2hex( random_bytes( 8 ) );
	}
}
