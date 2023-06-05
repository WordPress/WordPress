<?php
/**
 * Provide basic rate limiting functionality via WP Options API.
 *
 * Currently only provides a simple limit by delaying action by X seconds.
 *
 * Example usage:
 *
 * When an action runs, call set_rate_limit, e.g.:
 *
 * WC_Rate_Limiter::set_rate_limit( "{$my_action_name}_{$user_id}", $delay );
 *
 * This sets a timestamp for future timestamp after which action can run again.
 *
 *
 * Then before running the action again, check if the action is allowed to run, e.g.:
 *
 * if ( WC_Rate_Limiter::retried_too_soon( "{$my_action_name}_{$user_id}" ) ) {
 *     add_notice( 'Sorry, too soon!' );
 * }
 *
 * @package WooCommerce\Classes
 * @version 3.9.0
 * @since   3.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Rate limit class.
 */
class WC_Rate_Limiter {

	/**
	 * Cache group.
	 */
	const CACHE_GROUP = 'wc_rate_limit';

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'woocommerce_cleanup_rate_limits', array( __CLASS__, 'cleanup' ) );
	}

	/**
	 * Constructs key name from action identifier.
	 * Left in for backwards compatibility.
	 *
	 * @param string $action_id Identifier of the action.
	 * @return string
	 */
	public static function storage_id( $action_id ) {
		return $action_id;
	}

	/**
	 * Gets a cache prefix.
	 *
	 * @param string $action_id Identifier of the action.
	 * @return string
	 */
	protected static function get_cache_key( $action_id ) {
		return WC_Cache_Helper::get_cache_prefix( 'rate_limit' . $action_id );
	}

	/**
	 * Retrieve a cached rate limit.
	 *
	 * @param string $action_id Identifier of the action.
	 * @return bool|int
	 */
	protected static function get_cached( $action_id ) {
		return wp_cache_get( self::get_cache_key( $action_id ), self::CACHE_GROUP );
	}

	/**
	 * Cache a rate limit.
	 *
	 * @param string $action_id Identifier of the action.
	 * @param int    $expiry Timestamp when the limit expires.
	 * @return bool
	 */
	protected static function set_cache( $action_id, $expiry ) {
		return wp_cache_set( self::get_cache_key( $action_id ), $expiry, self::CACHE_GROUP );
	}

	/**
	 * Returns true if the action is not allowed to be run by the rate limiter yet, false otherwise.
	 *
	 * @param string $action_id Identifier of the action.
	 * @return bool
	 */
	public static function retried_too_soon( $action_id ) {
		global $wpdb;

		$next_try_allowed_at = self::get_cached( $action_id );

		if ( false === $next_try_allowed_at ) {
			$next_try_allowed_at = $wpdb->get_var(
				$wpdb->prepare(
					"
						SELECT rate_limit_expiry
						FROM {$wpdb->prefix}wc_rate_limits
						WHERE rate_limit_key = %s
					",
					$action_id
				)
			);

			self::set_cache( $action_id, $next_try_allowed_at );
		}

		// No record of action running, so action is allowed to run.
		if ( null === $next_try_allowed_at ) {
			return false;
		}

		// Before the next run is allowed, retry forbidden.
		if ( time() <= $next_try_allowed_at ) {
			return true;
		}

		// After the next run is allowed, retry allowed.
		return false;
	}

	/**
	 * Sets the rate limit delay in seconds for action with identifier $id.
	 *
	 * @param string $action_id Identifier of the action.
	 * @param int    $delay Delay in seconds.
	 * @return bool True if the option setting was successful, false otherwise.
	 */
	public static function set_rate_limit( $action_id, $delay ) {
		global $wpdb;

		$next_try_allowed_at = time() + $delay;

		$result = $wpdb->replace(
			$wpdb->prefix . 'wc_rate_limits',
			array(
				'rate_limit_key'    => $action_id,
				'rate_limit_expiry' => $next_try_allowed_at,
			),
			array( '%s', '%d' )
		);

		self::set_cache( $action_id, $next_try_allowed_at );

		return false !== $result;
	}

	/**
	 * Cleanup expired rate limits from the database and clear caches.
	 */
	public static function cleanup() {
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}wc_rate_limits WHERE rate_limit_expiry < %d",
				time()
			)
		);

		if ( class_exists( 'WC_Cache_Helper' ) ) {
			WC_Cache_Helper::invalidate_cache_group( self::CACHE_GROUP );
		}
	}
}

WC_Rate_Limiter::init();
