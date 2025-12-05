<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\XML_Sitemaps
 */

/**
 * Handles sitemaps caching and invalidation.
 *
 * @since 3.2
 */
class WPSEO_Sitemaps_Cache {

	/**
	 * Holds the options that, when updated, should cause the cache to clear.
	 *
	 * @var array
	 */
	protected static $cache_clear = [];

	/**
	 * Mirror of enabled status for static calls.
	 *
	 * @var bool
	 */
	protected static $is_enabled = false;

	/**
	 * Holds the flag to clear all cache.
	 *
	 * @var bool
	 */
	protected static $clear_all = false;

	/**
	 * Holds the array of types to clear.
	 *
	 * @var array
	 */
	protected static $clear_types = [];

	/**
	 * Hook methods for invalidation on necessary events.
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'init' ] );

		add_action( 'deleted_term_relationships', [ self::class, 'invalidate' ] );

		add_action( 'update_option', [ self::class, 'clear_on_option_update' ] );

		add_action( 'edited_terms', [ self::class, 'invalidate_helper' ], 10, 2 );
		add_action( 'clean_term_cache', [ self::class, 'invalidate_helper' ], 10, 2 );
		add_action( 'clean_object_term_cache', [ self::class, 'invalidate_helper' ], 10, 2 );

		add_action( 'user_register', [ self::class, 'invalidate_author' ] );
		add_action( 'delete_user', [ self::class, 'invalidate_author' ] );

		add_action( 'shutdown', [ self::class, 'clear_queued' ] );
	}

	/**
	 * Setup context for static calls.
	 *
	 * @return void
	 */
	public function init() {

		self::$is_enabled = $this->is_enabled();
	}

	/**
	 * If cache is enabled.
	 *
	 * @since 3.2
	 *
	 * @return bool
	 */
	public function is_enabled() {

		/**
		 * Filter if XML sitemap transient cache is enabled.
		 *
		 * @param bool $unsigned Enable cache or not, defaults to true.
		 */
		return apply_filters( 'wpseo_enable_xml_sitemap_transient_caching', false );
	}

	/**
	 * Retrieve the sitemap page from cache.
	 *
	 * @since 3.2
	 *
	 * @param string $type Sitemap type.
	 * @param int    $page Page number to retrieve.
	 *
	 * @return string|bool
	 */
	public function get_sitemap( $type, $page ) {

		$transient_key = WPSEO_Sitemaps_Cache_Validator::get_storage_key( $type, $page );
		if ( $transient_key === false ) {
			return false;
		}

		return get_transient( $transient_key );
	}

	/**
	 * Get the sitemap that is cached.
	 *
	 * @param string $type Sitemap type.
	 * @param int    $page Page number to retrieve.
	 *
	 * @return WPSEO_Sitemap_Cache_Data|null Null on no cache found otherwise object containing sitemap and meta data.
	 */
	public function get_sitemap_data( $type, $page ) {

		$sitemap = $this->get_sitemap( $type, $page );

		if ( empty( $sitemap ) ) {
			return null;
		}

		/*
		 * Unserialize Cache Data object as is_serialized() doesn't recognize classes in C format.
		 * This work-around should no longer be needed once the minimum PHP version has gone up to PHP 7.4,
		 * as the `WPSEO_Sitemap_Cache_Data` class uses O format serialization in PHP 7.4 and higher.
		 *
		 * @link https://wiki.php.net/rfc/custom_object_serialization
		 */
		if ( is_string( $sitemap ) && strpos( $sitemap, 'C:24:"WPSEO_Sitemap_Cache_Data"' ) === 0 ) {
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- Can't be avoided due to how WP stores options.
			$sitemap = unserialize( $sitemap );
		}

		// What we expect it to be if it is set.
		if ( $sitemap instanceof WPSEO_Sitemap_Cache_Data_Interface ) {
			return $sitemap;
		}

		return null;
	}

	/**
	 * Store the sitemap page from cache.
	 *
	 * @since 3.2
	 *
	 * @param string $type    Sitemap type.
	 * @param int    $page    Page number to store.
	 * @param string $sitemap Sitemap body to store.
	 * @param bool   $usable  Is this a valid sitemap or a cache of an invalid sitemap.
	 *
	 * @return bool
	 */
	public function store_sitemap( $type, $page, $sitemap, $usable = true ) {

		$transient_key = WPSEO_Sitemaps_Cache_Validator::get_storage_key( $type, $page );

		if ( $transient_key === false ) {
			return false;
		}

		$status = ( $usable ) ? WPSEO_Sitemap_Cache_Data::OK : WPSEO_Sitemap_Cache_Data::ERROR;

		$sitemap_data = new WPSEO_Sitemap_Cache_Data();
		$sitemap_data->set_sitemap( $sitemap );
		$sitemap_data->set_status( $status );

		return set_transient( $transient_key, $sitemap_data, DAY_IN_SECONDS );
	}

	/**
	 * Delete cache transients for index and specific type.
	 *
	 * Always deletes the main index sitemaps cache, as that's always invalidated by any other change.
	 *
	 * @since 1.5.4
	 * @since 3.2   Changed from function wpseo_invalidate_sitemap_cache() to method in this class.
	 *
	 * @param string $type Sitemap type to invalidate.
	 *
	 * @return void
	 */
	public static function invalidate( $type ) {

		self::clear( [ $type ] );
	}

	/**
	 * Helper to invalidate in hooks where type is passed as second argument.
	 *
	 * @since 3.2
	 *
	 * @param int    $unused Unused term ID value.
	 * @param string $type   Taxonomy to invalidate.
	 *
	 * @return void
	 */
	public static function invalidate_helper( $unused, $type ) {

		if (
			WPSEO_Options::get( 'noindex-' . $type ) === false
			|| WPSEO_Options::get( 'noindex-tax-' . $type ) === false
		) {
			self::invalidate( $type );
		}
	}

	/**
	 * Invalidate sitemap cache for authors.
	 *
	 * @param int $user_id User ID.
	 *
	 * @return bool True if the sitemap was properly invalidated. False otherwise.
	 */
	public static function invalidate_author( $user_id ) {

		$user = get_user_by( 'id', $user_id );

		if ( $user === false ) {
			return false;
		}

		if ( current_action() === 'user_register' ) {
			update_user_meta( $user_id, '_yoast_wpseo_profile_updated', time() );
		}

		if ( empty( $user->roles ) || in_array( 'subscriber', $user->roles, true ) ) {
			return false;
		}

		self::invalidate( 'author' );

		return true;
	}

	/**
	 * Invalidate sitemap cache for the post type of a post.
	 *
	 * Don't invalidate for revisions.
	 *
	 * @since 1.5.4
	 * @since 3.2   Changed from function wpseo_invalidate_sitemap_cache_on_save_post() to method in this class.
	 *
	 * @param int $post_id Post ID to invalidate type for.
	 *
	 * @return void
	 */
	public static function invalidate_post( $post_id ) {

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		self::invalidate( get_post_type( $post_id ) );
	}

	/**
	 * Delete cache transients for given sitemaps types or all by default.
	 *
	 * @since 1.8.0
	 * @since 3.2   Moved from WPSEO_Utils to this class.
	 *
	 * @param array $types Set of sitemap types to delete cache transients for.
	 *
	 * @return void
	 */
	public static function clear( $types = [] ) {

		if ( ! self::$is_enabled ) {
			return;
		}

		// No types provided, clear all.
		if ( empty( $types ) ) {
			self::$clear_all = true;

			return;
		}

		// Always invalidate the index sitemap as well.
		if ( ! in_array( WPSEO_Sitemaps::SITEMAP_INDEX_TYPE, $types, true ) ) {
			array_unshift( $types, WPSEO_Sitemaps::SITEMAP_INDEX_TYPE );
		}

		foreach ( $types as $type ) {
			if ( ! in_array( $type, self::$clear_types, true ) ) {
				self::$clear_types[] = $type;
			}
		}
	}

	/**
	 * Invalidate storage for cache types queued to clear.
	 *
	 * @return void
	 */
	public static function clear_queued() {

		if ( self::$clear_all ) {

			WPSEO_Sitemaps_Cache_Validator::invalidate_storage();
			self::$clear_all   = false;
			self::$clear_types = [];

			return;
		}

		foreach ( self::$clear_types as $type ) {
			WPSEO_Sitemaps_Cache_Validator::invalidate_storage( $type );
		}

		self::$clear_types = [];
	}

	/**
	 * Adds a hook that when given option is updated, the cache is cleared.
	 *
	 * @since 3.2
	 *
	 * @param string $option Option name.
	 * @param string $type   Sitemap type.
	 *
	 * @return void
	 */
	public static function register_clear_on_option_update( $option, $type = '' ) {

		self::$cache_clear[ $option ] = $type;
	}

	/**
	 * Clears the transient cache when a given option is updated, if that option has been registered before.
	 *
	 * @since 3.2
	 *
	 * @param string $option The option name that's being updated.
	 *
	 * @return void
	 */
	public static function clear_on_option_update( $option ) {

		if ( array_key_exists( $option, self::$cache_clear ) ) {

			if ( empty( self::$cache_clear[ $option ] ) ) {
				// Clear all caches.
				self::clear();
			}
			else {
				// Clear specific provided type(s).
				$types = (array) self::$cache_clear[ $option ];
				self::clear( $types );
			}
		}
	}
}
