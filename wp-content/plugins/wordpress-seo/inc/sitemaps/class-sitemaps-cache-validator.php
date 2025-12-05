<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\XML_Sitemaps
 */

/**
 * Handles storage keys for sitemaps caching and invalidation.
 *
 * @since 3.2
 */
class WPSEO_Sitemaps_Cache_Validator {

	/**
	 * Prefix of the transient key for sitemap caches.
	 *
	 * @var string
	 */
	public const STORAGE_KEY_PREFIX = 'yst_sm_';

	/**
	 * Name of the option that holds the global validation value.
	 *
	 * @var string
	 */
	public const VALIDATION_GLOBAL_KEY = 'wpseo_sitemap_cache_validator_global';

	/**
	 * The format which creates the key of the option that holds the type validation value.
	 *
	 * @var string
	 */
	public const VALIDATION_TYPE_KEY_FORMAT = 'wpseo_sitemap_%s_cache_validator';

	/**
	 * Get the cache key for a certain type and page.
	 *
	 * A type of cache would be something like 'page', 'post' or 'video'.
	 *
	 * Example key format for sitemap type "post", page 1: wpseo_sitemap_post_1:akfw3e_23azBa .
	 *
	 * @since 3.2
	 *
	 * @param string|null $type The type to get the key for. Null or self::SITEMAP_INDEX_TYPE for index cache.
	 * @param int         $page The page of cache to get the key for.
	 *
	 * @return bool|string The key where the cache is stored on. False if the key could not be generated.
	 */
	public static function get_storage_key( $type = null, $page = 1 ) {

		// Using SITEMAP_INDEX_TYPE for sitemap index cache.
		$type = ( $type === null ) ? WPSEO_Sitemaps::SITEMAP_INDEX_TYPE : $type;

		$global_cache_validator = self::get_validator();
		$type_cache_validator   = self::get_validator( $type );

		$prefix  = self::STORAGE_KEY_PREFIX;
		$postfix = sprintf( '_%d:%s_%s', $page, $global_cache_validator, $type_cache_validator );

		try {
			$type = self::truncate_type( $type, $prefix, $postfix );
		} catch ( OutOfBoundsException $exception ) {
			// Maybe do something with the exception, for now just mark as invalid.
			return false;
		}

		// Build key.
		$full_key = $prefix . $type . $postfix;

		return $full_key;
	}

	/**
	 * If the type is over length make sure we compact it so we don't have any database problems.
	 *
	 * When there are more 'extremely long' post types, changes are they have variations in either the start or ending.
	 * Because of this, we cut out the excess in the middle which should result in less chance of collision.
	 *
	 * @since 3.2
	 *
	 * @param string $type    The type of sitemap to be used.
	 * @param string $prefix  The part before the type in the cache key. Only the length is used.
	 * @param string $postfix The part after the type in the cache key. Only the length is used.
	 *
	 * @return string The type with a safe length to use
	 *
	 * @throws OutOfRangeException When there is less than 15 characters of space for a key that is originally longer.
	 */
	public static function truncate_type( $type, $prefix = '', $postfix = '' ) {
		/*
		 * This length has been restricted by the database column length of 64 in the past.
		 * The prefix added by WordPress is '_transient_' because we are saving to a transient.
		 * We need to use a timeout on the transient, otherwise the values get autoloaded, this adds
		 * another restriction to the length.
		 */
		$max_length  = 45; // 64 - 19 ('_transient_timeout_')
		$max_length -= strlen( $prefix );
		$max_length -= strlen( $postfix );

		if ( strlen( $type ) > $max_length ) {

			if ( $max_length < 15 ) {
				/*
				 * If this happens the most likely cause is a page number that is too high.
				 *
				 * So this would not happen unintentionally.
				 * Either by trying to cause a high server load, finding backdoors or misconfiguration.
				 */
				throw new OutOfRangeException(
					__(
						'Trying to build the sitemap cache key, but the postfix and prefix combination leaves too little room to do this. You are probably requesting a page that is way out of the expected range.',
						'wordpress-seo'
					)
				);
			}

			$half = ( $max_length / 2 );

			$first_part = substr( $type, 0, ( ceil( $half ) - 1 ) );
			$last_part  = substr( $type, ( 1 - floor( $half ) ) );

			$type = $first_part . '..' . $last_part;
		}

		return $type;
	}

	/**
	 * Invalidate sitemap cache.
	 *
	 * @since 3.2
	 *
	 * @param string|null $type The type to get the key for. Null for all caches.
	 *
	 * @return void
	 */
	public static function invalidate_storage( $type = null ) {

		// Global validator gets cleared when no type is provided.
		$old_validator = null;

		// Get the current type validator.
		if ( $type !== null ) {
			$old_validator = self::get_validator( $type );
		}

		// Refresh validator.
		self::create_validator( $type );

		if ( ! wp_using_ext_object_cache() ) {
			// Clean up current cache from the database.
			self::cleanup_database( $type, $old_validator );
		}

		// External object cache pushes old and unretrieved items out by itself so we don't have to do anything for that.
	}

	/**
	 * Cleanup invalidated database cache.
	 *
	 * @since 3.2
	 *
	 * @param string|null $type      The type of sitemap to clear cache for.
	 * @param string|null $validator The validator to clear cache of.
	 *
	 * @return void
	 */
	public static function cleanup_database( $type = null, $validator = null ) {

		global $wpdb;

		if ( $type === null ) {
			// Clear all cache if no type is provided.
			$like = sprintf( '%s%%', self::STORAGE_KEY_PREFIX );
		}
		else {
			// Clear type cache for all type keys.
			$like = sprintf( '%1$s%2$s_%%', self::STORAGE_KEY_PREFIX, $type );
		}

		/*
		 * Add slashes to the LIKE "_" single character wildcard.
		 *
		 * We can't use `esc_like` here because we need the % in the query.
		 */
		$where   = [];
		$where[] = sprintf( "option_name LIKE '%s'", addcslashes( '_transient_' . $like, '_' ) );
		$where[] = sprintf( "option_name LIKE '%s'", addcslashes( '_transient_timeout_' . $like, '_' ) );

		// Delete transients.
		//phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- We need to use a direct query here.
		//phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
		$wpdb->query(
			$wpdb->prepare(
			//phpcs:disable WordPress.DB.PreparedSQLPlaceholders -- %i placeholder is still not recognized.
				'DELETE FROM %i WHERE ' . implode( ' OR ', array_fill( 0, count( $where ), '%s' ) ),
				array_merge( [ $wpdb->options ], $where )
			)
		);

		wp_cache_delete( 'alloptions', 'options' );
	}

	/**
	 * Get the current cache validator.
	 *
	 * Without the type the global validator is returned.
	 * This can invalidate -all- keys in cache at once.
	 *
	 * With the type parameter the validator for that specific type can be invalidated.
	 *
	 * @since 3.2
	 *
	 * @param string $type Provide a type for a specific type validator, empty for global validator.
	 *
	 * @return string|null The validator for the supplied type.
	 */
	public static function get_validator( $type = '' ) {

		$key = self::get_validator_key( $type );

		$current = get_option( $key, null );
		if ( $current !== null ) {
			return $current;
		}

		if ( self::create_validator( $type ) ) {
			return self::get_validator( $type );
		}

		return null;
	}

	/**
	 * Get the cache validator option key for the specified type.
	 *
	 * @since 3.2
	 *
	 * @param string $type Provide a type for a specific type validator, empty for global validator.
	 *
	 * @return string Validator to be used to generate the cache key.
	 */
	public static function get_validator_key( $type = '' ) {

		if ( empty( $type ) ) {
			return self::VALIDATION_GLOBAL_KEY;
		}

		return sprintf( self::VALIDATION_TYPE_KEY_FORMAT, $type );
	}

	/**
	 * Refresh the cache validator value.
	 *
	 * @since 3.2
	 *
	 * @param string $type Provide a type for a specific type validator, empty for global validator.
	 *
	 * @return bool True if validator key has been saved as option.
	 */
	public static function create_validator( $type = '' ) {

		$key = self::get_validator_key( $type );

		// Generate new validator.
		$microtime = microtime();

		// Remove space.
		list( $milliseconds, $seconds ) = explode( ' ', $microtime );

		// Transients are purged every 24h.
		$seconds      = ( $seconds % DAY_IN_SECONDS );
		$milliseconds = intval( substr( $milliseconds, 2, 3 ), 10 );

		// Combine seconds and milliseconds and convert to integer.
		$validator = intval( $seconds . '' . $milliseconds, 10 );

		// Apply base 61 encoding.
		$compressed = self::convert_base10_to_base61( $validator );

		return update_option( $key, $compressed, false );
	}

	/**
	 * Encode to base61 format.
	 *
	 * This is base64 (numeric + alpha + alpha upper case) without the 0.
	 *
	 * @since 3.2
	 *
	 * @param int $base10 The number that has to be converted to base 61.
	 *
	 * @return string Base 61 converted string.
	 *
	 * @throws InvalidArgumentException When the input is not an integer.
	 */
	public static function convert_base10_to_base61( $base10 ) {

		if ( ! is_int( $base10 ) ) {
			throw new InvalidArgumentException( __( 'Expected an integer as input.', 'wordpress-seo' ) );
		}

		// Characters that will be used in the conversion.
		$characters = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$length     = strlen( $characters );

		$remainder = $base10;
		$output    = '';

		do {
			// Building from right to left in the result.
			$index = ( $remainder % $length );

			// Prepend the character to the output.
			$output = $characters[ $index ] . $output;

			// Determine the remainder after removing the applied number.
			$remainder = floor( $remainder / $length );

			// Keep doing it until we have no remainder left.
		} while ( $remainder );

		return $output;
	}
}
