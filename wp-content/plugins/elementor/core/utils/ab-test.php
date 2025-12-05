<?php

namespace Elementor\Core\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Core A/B Testing utility class
 *
 * Provides A/B testing functionality for Core features.
 * Uses WordPress transients for caching and user-specific variation assignment.
 */
class Ab_Test {

	const PREFIX_CACHE_KEY = '_elementor_ab_test_';
	const CACHE_TTL = 90 * DAY_IN_SECONDS;

	/**
	 * Get variation for a specific test
	 *
	 * @param string $test_name The name of the A/B test
	 * @param int    $user_id Optional user ID, defaults to current user
	 * @return int Returns 1 or 2 for variation assignment
	 */
	public static function get_variation( $test_name, $user_id = null ): int {
		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}

		$variation_id = self::get_variation_id_from_cache( $test_name, $user_id );

		if ( false === $variation_id ) {
			$variation_id = self::get_random_variation();
			self::set_variation_id_from_cache( $test_name, $user_id, $variation_id );
		}

		return absint( $variation_id );
	}

	/**
	 * Check if user should see the feature (50% probability)
	 *
	 * @param string $test_name The name of the A/B test
	 * @param int    $user_id Optional user ID, defaults to current user
	 * @return bool True if user should see the feature
	 */
	public static function should_show_feature( $test_name, $user_id = null ): bool {
		$variation = self::get_variation( $test_name, $user_id );
		return 1 === $variation; // Only variation 1 sees the feature
	}

	/**
	 * Get variation ID from cache
	 *
	 * @param string $test_name The name of the A/B test
	 * @param int    $user_id User ID
	 * @return int|false Variation ID or false if not cached
	 */
	private static function get_variation_id_from_cache( $test_name, $user_id ) {
		$cache_key = self::PREFIX_CACHE_KEY . $test_name . '_' . $user_id;
		return get_transient( $cache_key );
	}

	/**
	 * Set variation ID in cache
	 *
	 * @param string $test_name The name of the A/B test
	 * @param int    $user_id User ID
	 * @param int    $variation_id Variation ID to cache
	 */
	private static function set_variation_id_from_cache( $test_name, $user_id, $variation_id ): void {
		$cache_key = self::PREFIX_CACHE_KEY . $test_name . '_' . $user_id;
		set_transient( $cache_key, $variation_id, self::CACHE_TTL );
	}

	/**
	 * Generate random variation (1 or 2)
	 *
	 * @return int Random variation ID
	 */
	private static function get_random_variation(): int {
		return mt_rand( 1, 2 );
	}
}
