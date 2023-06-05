<?php
/**
 * Handle data for the current customers session.
 * Implements the WC_Session abstract class.
 *
 * From 2.5 this uses a custom table for session storage. Based on https://github.com/kloon/woocommerce-large-sessions.
 *
 * @class    WC_Session_Handler
 * @version  2.5.0
 * @package  WooCommerce\Classes
 */

use Automattic\Jetpack\Constants;

defined( 'ABSPATH' ) || exit;

/**
 * Session handler class.
 */
class WC_Session_Handler extends WC_Session {

	/**
	 * Cookie name used for the session.
	 *
	 * @var string cookie name
	 */
	protected $_cookie;

	/**
	 * Stores session expiry.
	 *
	 * @var string session due to expire timestamp
	 */
	protected $_session_expiring;

	/**
	 * Stores session due to expire timestamp.
	 *
	 * @var string session expiration timestamp
	 */
	protected $_session_expiration;

	/**
	 * True when the cookie exists.
	 *
	 * @var bool Based on whether a cookie exists.
	 */
	protected $_has_cookie = false;

	/**
	 * Table name for session data.
	 *
	 * @var string Custom session table name
	 */
	protected $_table;

	/**
	 * Constructor for the session class.
	 */
	public function __construct() {
		$this->_cookie = apply_filters( 'woocommerce_cookie', 'wp_woocommerce_session_' . COOKIEHASH );
		$this->_table  = $GLOBALS['wpdb']->prefix . 'woocommerce_sessions';
	}

	/**
	 * Init hooks and session data.
	 *
	 * @since 3.3.0
	 */
	public function init() {
		$this->init_session_cookie();

		add_action( 'woocommerce_set_cart_cookies', array( $this, 'set_customer_session_cookie' ), 10 );
		add_action( 'shutdown', array( $this, 'save_data' ), 20 );
		add_action( 'wp_logout', array( $this, 'destroy_session' ) );

		if ( ! is_user_logged_in() ) {
			add_filter( 'nonce_user_logged_out', array( $this, 'maybe_update_nonce_user_logged_out' ), 10, 2 );
		}
	}

	/**
	 * Setup cookie and customer ID.
	 *
	 * @since 3.6.0
	 */
	public function init_session_cookie() {
		$cookie = $this->get_session_cookie();

		if ( $cookie ) {
			// Customer ID will be an MD5 hash id this is a guest session.
			$this->_customer_id        = $cookie[0];
			$this->_session_expiration = $cookie[1];
			$this->_session_expiring   = $cookie[2];
			$this->_has_cookie         = true;
			$this->_data               = $this->get_session_data();

			if ( ! $this->is_session_cookie_valid() ) {
				$this->destroy_session();
				$this->set_session_expiration();
			}

			// If the user logs in, update session.
			if ( is_user_logged_in() && strval( get_current_user_id() ) !== $this->_customer_id ) {
				$guest_session_id   = $this->_customer_id;
				$this->_customer_id = strval( get_current_user_id() );
				$this->_dirty       = true;
				$this->save_data( $guest_session_id );
				$this->set_customer_session_cookie( true );
			}

			// Update session if its close to expiring.
			if ( time() > $this->_session_expiring ) {
				$this->set_session_expiration();
				$this->update_session_timestamp( $this->_customer_id, $this->_session_expiration );
			}
		} else {
			$this->set_session_expiration();
			$this->_customer_id = $this->generate_customer_id();
			$this->_data        = $this->get_session_data();
		}
	}

	/**
	 * Checks if session cookie is expired, or belongs to a logged out user.
	 *
	 * @return bool Whether session cookie is valid.
	 */
	private function is_session_cookie_valid() {
		// If session is expired, session cookie is invalid.
		if ( time() > $this->_session_expiration ) {
			return false;
		}

		// If user has logged out, session cookie is invalid.
		if ( ! is_user_logged_in() && ! $this->is_customer_guest( $this->_customer_id ) ) {
			return false;
		}

		// Session from a different user is not valid. (Although from a guest user will be valid)
		if ( is_user_logged_in() && ! $this->is_customer_guest( $this->_customer_id ) && strval( get_current_user_id() ) !== $this->_customer_id ) {
			return false;
		}

		return true;
	}

	/**
	 * Sets the session cookie on-demand (usually after adding an item to the cart).
	 *
	 * Since the cookie name (as of 2.1) is prepended with wp, cache systems like batcache will not cache pages when set.
	 *
	 * Warning: Cookies will only be set if this is called before the headers are sent.
	 *
	 * @param bool $set Should the session cookie be set.
	 */
	public function set_customer_session_cookie( $set ) {
		if ( $set ) {
			$to_hash           = $this->_customer_id . '|' . $this->_session_expiration;
			$cookie_hash       = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );
			$cookie_value      = $this->_customer_id . '||' . $this->_session_expiration . '||' . $this->_session_expiring . '||' . $cookie_hash;
			$this->_has_cookie = true;

			if ( ! isset( $_COOKIE[ $this->_cookie ] ) || $_COOKIE[ $this->_cookie ] !== $cookie_value ) {
				wc_setcookie( $this->_cookie, $cookie_value, $this->_session_expiration, $this->use_secure_cookie(), true );
			}
		}
	}

	/**
	 * Should the session cookie be secure?
	 *
	 * @since 3.6.0
	 * @return bool
	 */
	protected function use_secure_cookie() {
		return apply_filters( 'wc_session_use_secure_cookie', wc_site_is_https() && is_ssl() );
	}

	/**
	 * Return true if the current user has an active session, i.e. a cookie to retrieve values.
	 *
	 * @return bool
	 */
	public function has_session() {
		return isset( $_COOKIE[ $this->_cookie ] ) || $this->_has_cookie || is_user_logged_in(); // @codingStandardsIgnoreLine.
	}

	/**
	 * Set session expiration.
	 */
	public function set_session_expiration() {
		$this->_session_expiring   = time() + intval( apply_filters( 'wc_session_expiring', 60 * 60 * 47 ) ); // 47 Hours.
		$this->_session_expiration = time() + intval( apply_filters( 'wc_session_expiration', 60 * 60 * 48 ) ); // 48 Hours.
	}

	/**
	 * Generate a unique customer ID for guests, or return user ID if logged in.
	 *
	 * Uses Portable PHP password hashing framework to generate a unique cryptographically strong ID.
	 *
	 * @return string
	 */
	public function generate_customer_id() {
		$customer_id = '';

		if ( is_user_logged_in() ) {
			$customer_id = strval( get_current_user_id() );
		}

		if ( empty( $customer_id ) ) {
			require_once ABSPATH . 'wp-includes/class-phpass.php';
			$hasher      = new PasswordHash( 8, false );
			$customer_id = 't_' . substr( md5( $hasher->get_random_bytes( 32 ) ), 2 );
		}

		return $customer_id;
	}

	/**
	 * Checks if this is an auto-generated customer ID.
	 *
	 * @param string|int $customer_id Customer ID to check.
	 *
	 * @return bool Whether customer ID is randomly generated.
	 */
	private function is_customer_guest( $customer_id ) {
		$customer_id = strval( $customer_id );

		if ( empty( $customer_id ) ) {
			return true;
		}

		if ( 't_' === substr( $customer_id, 0, 2 ) ) {
			return true;
		}

		/**
		 * Legacy checks. This is to handle sessions that were created from a previous release.
		 * Maybe we can get rid of them after a few releases.
		 */

		// Almost all random $customer_ids will have some letters in it, while all actual ids will be integers.
		if ( strval( (int) $customer_id ) !== $customer_id ) {
			return true;
		}

		// Performance hack to potentially save a DB query, when same user as $customer_id is logged in.
		if ( is_user_logged_in() && strval( get_current_user_id() ) === $customer_id ) {
			return false;
		} else {
			$customer = new WC_Customer( $customer_id );

			if ( 0 === $customer->get_id() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get session unique ID for requests if session is initialized or user ID if logged in.
	 * Introduced to help with unit tests.
	 *
	 * @since 5.3.0
	 * @return string
	 */
	public function get_customer_unique_id() {
		$customer_id = '';

		if ( $this->has_session() && $this->_customer_id ) {
			$customer_id = $this->_customer_id;
		} elseif ( is_user_logged_in() ) {
			$customer_id = (string) get_current_user_id();
		}

		return $customer_id;
	}

	/**
	 * Get the session cookie, if set. Otherwise return false.
	 *
	 * Session cookies without a customer ID are invalid.
	 *
	 * @return bool|array
	 */
	public function get_session_cookie() {
		$cookie_value = isset( $_COOKIE[ $this->_cookie ] ) ? wp_unslash( $_COOKIE[ $this->_cookie ] ) : false; // @codingStandardsIgnoreLine.

		if ( empty( $cookie_value ) || ! is_string( $cookie_value ) ) {
			return false;
		}

		list( $customer_id, $session_expiration, $session_expiring, $cookie_hash ) = explode( '||', $cookie_value );

		if ( empty( $customer_id ) ) {
			return false;
		}

		// Validate hash.
		$to_hash = $customer_id . '|' . $session_expiration;
		$hash    = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );

		if ( empty( $cookie_hash ) || ! hash_equals( $hash, $cookie_hash ) ) {
			return false;
		}

		return array( $customer_id, $session_expiration, $session_expiring, $cookie_hash );
	}

	/**
	 * Get session data.
	 *
	 * @return array
	 */
	public function get_session_data() {
		return $this->has_session() ? (array) $this->get_session( $this->_customer_id, array() ) : array();
	}

	/**
	 * Gets a cache prefix. This is used in session names so the entire cache can be invalidated with 1 function call.
	 *
	 * @return string
	 */
	private function get_cache_prefix() {
		return WC_Cache_Helper::get_cache_prefix( WC_SESSION_CACHE_GROUP );
	}

	/**
	 * Save data and delete guest session.
	 *
	 * @param int $old_session_key session ID before user logs in.
	 */
	public function save_data( $old_session_key = 0 ) {
		// Dirty if something changed - prevents saving nothing new.
		if ( $this->_dirty && $this->has_session() ) {
			global $wpdb;

			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}woocommerce_sessions (`session_key`, `session_value`, `session_expiry`) VALUES (%s, %s, %d)
 					ON DUPLICATE KEY UPDATE `session_value` = VALUES(`session_value`), `session_expiry` = VALUES(`session_expiry`)",
					$this->_customer_id,
					maybe_serialize( $this->_data ),
					$this->_session_expiration
				)
			);

			wp_cache_set( $this->get_cache_prefix() . $this->_customer_id, $this->_data, WC_SESSION_CACHE_GROUP, $this->_session_expiration - time() );
			$this->_dirty = false;
			if ( get_current_user_id() != $old_session_key && ! is_object( get_user_by( 'id', $old_session_key ) ) ) {
				$this->delete_session( $old_session_key );
			}
		}
	}

	/**
	 * Destroy all session data.
	 */
	public function destroy_session() {
		$this->delete_session( $this->_customer_id );
		$this->forget_session();
	}

	/**
	 * Forget all session data without destroying it.
	 */
	public function forget_session() {
		wc_setcookie( $this->_cookie, '', time() - YEAR_IN_SECONDS, $this->use_secure_cookie(), true );

		if ( ! is_admin() ) {
			include_once WC_ABSPATH . 'includes/wc-cart-functions.php';

			wc_empty_cart();
		}

		$this->_data        = array();
		$this->_dirty       = false;
		$this->_customer_id = $this->generate_customer_id();
	}

	/**
	 * When a user is logged out, ensure they have a unique nonce by using the customer/session ID.
	 *
	 * @deprecated 5.3.0
	 * @param int $uid User ID.
	 * @return int|string
	 */
	public function nonce_user_logged_out( $uid ) {
		wc_deprecated_function( 'WC_Session_Handler::nonce_user_logged_out', '5.3', 'WC_Session_Handler::maybe_update_nonce_user_logged_out' );

		return $this->has_session() && $this->_customer_id ? $this->_customer_id : $uid;
	}

	/**
	 * When a user is logged out, ensure they have a unique nonce to manage cart and more using the customer/session ID.
	 * This filter runs everything `wp_verify_nonce()` and `wp_create_nonce()` gets called.
	 *
	 * @since 5.3.0
	 * @param int    $uid    User ID.
	 * @param string $action The nonce action.
	 * @return int|string
	 */
	public function maybe_update_nonce_user_logged_out( $uid, $action ) {
		if ( Automattic\WooCommerce\Utilities\StringUtil::starts_with( $action, 'woocommerce' ) ) {
			return $this->has_session() && $this->_customer_id ? $this->_customer_id : $uid;
		}

		return $uid;
	}

	/**
	 * Cleanup session data from the database and clear caches.
	 */
	public function cleanup_sessions() {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( "DELETE FROM $this->_table WHERE session_expiry < %d", time() ) ); // @codingStandardsIgnoreLine.

		if ( class_exists( 'WC_Cache_Helper' ) ) {
			WC_Cache_Helper::invalidate_cache_group( WC_SESSION_CACHE_GROUP );
		}
	}

	/**
	 * Returns the session.
	 *
	 * @param string $customer_id Customer ID.
	 * @param mixed  $default Default session value.
	 * @return string|array
	 */
	public function get_session( $customer_id, $default = false ) {
		global $wpdb;

		if ( Constants::is_defined( 'WP_SETUP_CONFIG' ) ) {
			return false;
		}

		// Try to get it from the cache, it will return false if not present or if object cache not in use.
		$value = wp_cache_get( $this->get_cache_prefix() . $customer_id, WC_SESSION_CACHE_GROUP );

		if ( false === $value ) {
			$value = $wpdb->get_var( $wpdb->prepare( "SELECT session_value FROM $this->_table WHERE session_key = %s", $customer_id ) ); // @codingStandardsIgnoreLine.

			if ( is_null( $value ) ) {
				$value = $default;
			}

			$cache_duration = $this->_session_expiration - time();
			if ( 0 < $cache_duration ) {
				wp_cache_add( $this->get_cache_prefix() . $customer_id, $value, WC_SESSION_CACHE_GROUP, $cache_duration );
			}
		}

		return maybe_unserialize( $value );
	}

	/**
	 * Delete the session from the cache and database.
	 *
	 * @param int $customer_id Customer ID.
	 */
	public function delete_session( $customer_id ) {
		global $wpdb;

		wp_cache_delete( $this->get_cache_prefix() . $customer_id, WC_SESSION_CACHE_GROUP );

		$wpdb->delete(
			$this->_table,
			array(
				'session_key' => $customer_id,
			)
		);
	}

	/**
	 * Update the session expiry timestamp.
	 *
	 * @param string $customer_id Customer ID.
	 * @param int    $timestamp Timestamp to expire the cookie.
	 */
	public function update_session_timestamp( $customer_id, $timestamp ) {
		global $wpdb;

		$wpdb->update(
			$this->_table,
			array(
				'session_expiry' => $timestamp,
			),
			array(
				'session_key' => $customer_id,
			),
			array(
				'%d',
			)
		);
	}
}
