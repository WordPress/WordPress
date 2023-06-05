<?php

/**
 * Class ActionScheduler_Compatibility
 */
class ActionScheduler_Compatibility {

	/**
	 * Converts a shorthand byte value to an integer byte value.
	 *
	 * Wrapper for wp_convert_hr_to_bytes(), moved to load.php in WordPress 4.6 from media.php
	 *
	 * @link https://secure.php.net/manual/en/function.ini-get.php
	 * @link https://secure.php.net/manual/en/faq.using.php#faq.using.shorthandbytes
	 *
	 * @param string $value A (PHP ini) byte value, either shorthand or ordinary.
	 * @return int An integer byte value.
	 */
	public static function convert_hr_to_bytes( $value ) {
		if ( function_exists( 'wp_convert_hr_to_bytes' ) ) {
			return wp_convert_hr_to_bytes( $value );
		}

		$value = strtolower( trim( $value ) );
		$bytes = (int) $value;

		if ( false !== strpos( $value, 'g' ) ) {
			$bytes *= GB_IN_BYTES;
		} elseif ( false !== strpos( $value, 'm' ) ) {
			$bytes *= MB_IN_BYTES;
		} elseif ( false !== strpos( $value, 'k' ) ) {
			$bytes *= KB_IN_BYTES;
		}

		// Deal with large (float) values which run into the maximum integer size.
		return min( $bytes, PHP_INT_MAX );
	}

	/**
	 * Attempts to raise the PHP memory limit for memory intensive processes.
	 *
	 * Only allows raising the existing limit and prevents lowering it.
	 *
	 * Wrapper for wp_raise_memory_limit(), added in WordPress v4.6.0
	 *
	 * @return bool|int|string The limit that was set or false on failure.
	 */
	public static function raise_memory_limit() {
		if ( function_exists( 'wp_raise_memory_limit' ) ) {
			return wp_raise_memory_limit( 'admin' );
		}

		$current_limit     = @ini_get( 'memory_limit' );
		$current_limit_int = self::convert_hr_to_bytes( $current_limit );

		if ( -1 === $current_limit_int ) {
			return false;
		}

		$wp_max_limit       = WP_MAX_MEMORY_LIMIT;
		$wp_max_limit_int   = self::convert_hr_to_bytes( $wp_max_limit );
		$filtered_limit     = apply_filters( 'admin_memory_limit', $wp_max_limit );
		$filtered_limit_int = self::convert_hr_to_bytes( $filtered_limit );

		if ( -1 === $filtered_limit_int || ( $filtered_limit_int > $wp_max_limit_int && $filtered_limit_int > $current_limit_int ) ) {
			if ( false !== @ini_set( 'memory_limit', $filtered_limit ) ) {
				return $filtered_limit;
			} else {
				return false;
			}
		} elseif ( -1 === $wp_max_limit_int || $wp_max_limit_int > $current_limit_int ) {
			if ( false !== @ini_set( 'memory_limit', $wp_max_limit ) ) {
				return $wp_max_limit;
			} else {
				return false;
			}
		}
		return false;
	}

	/**
	 * Attempts to raise the PHP timeout for time intensive processes.
	 *
	 * Only allows raising the existing limit and prevents lowering it. Wrapper for wc_set_time_limit(), when available.
	 *
	 * @param int $limit The time limit in seconds.
	 */
	public static function raise_time_limit( $limit = 0 ) {
		$limit = (int) $limit;
		$max_execution_time = (int) ini_get( 'max_execution_time' );

		/*
		 * If the max execution time is already unlimited (zero), or if it exceeds or is equal to the proposed
		 * limit, there is no reason for us to make further changes (we never want to lower it).
		 */
		if (
			0 === $max_execution_time
			|| ( $max_execution_time >= $limit && $limit !== 0 )
		) {
			return;
		}

		if ( function_exists( 'wc_set_time_limit' ) ) {
			wc_set_time_limit( $limit );
		} elseif ( function_exists( 'set_time_limit' ) && false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) { // phpcs:ignore PHPCompatibility.IniDirectives.RemovedIniDirectives.safe_modeDeprecatedRemoved
			@set_time_limit( $limit ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}
	}
}
