<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handle data for the current customers session.
 * Implements the WC_Session abstract class
 *
 * Long term plan will be, if https://github.com/ericmann/wp-session-manager/ gains traction
 * in WP core, this will be switched out to use it and maintain backwards compatibility :)
 *
 * Partly based on WP SESSION by Eric Mann.
 *
 * @class 		WC_Session_Handler
 * @version		2.0.0
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_Session_Handler extends WC_Session {

	/** cookie name */
	private $_cookie;

	/** session due to expire timestamp */
	private $_session_expiring;

	/** session expiration timestamp */
	private $_session_expiration;

	/** Bool based on whether a cookie exists **/
	private $_has_cookie = false;

	/**
	 * Constructor for the session class.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->_cookie = 'wp_woocommerce_session_' . COOKIEHASH;

		if ( $cookie = $this->get_session_cookie() ) {
			$this->_customer_id        = $cookie[0];
			$this->_session_expiration = $cookie[1];
			$this->_session_expiring   = $cookie[2];
			$this->_has_cookie         = true;

			// Update session if its close to expiring
			if ( time() > $this->_session_expiring ) {
				$this->set_session_expiration();
				$session_expiry_option = '_wc_session_expires_' . $this->_customer_id;
				// Check if option exists first to avoid auloading cleaned up sessions
				if ( false === get_option( $session_expiry_option ) ) {
					add_option( $session_expiry_option, $this->_session_expiration, '', 'no' );
				} else {
					update_option( $session_expiry_option, $this->_session_expiration );
				}
			}

		} else {
			$this->set_session_expiration();
			$this->_customer_id = $this->generate_customer_id();
		}

		$this->_data = $this->get_session_data();

    	// Actions
    	add_action( 'woocommerce_set_cart_cookies', array( $this, 'set_customer_session_cookie' ), 10 );
    	add_action( 'woocommerce_cleanup_sessions', array( $this, 'cleanup_sessions' ), 10 );
    	add_action( 'shutdown', array( $this, 'save_data' ), 20 );
    }

    /**
     * Sets the session cookie on-demand (usually after adding an item to the cart).
     *
     * Since the cookie name (as of 2.1) is prepended with wp, cache systems like batcache will not cache pages when set.
     *
     * Warning: Cookies will only be set if this is called before the headers are sent.
     */
    public function set_customer_session_cookie( $set ) {
    	if ( $set ) {
	    	// Set/renew our cookie
			$to_hash           = $this->_customer_id . $this->_session_expiration;
			$cookie_hash       = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );
			$cookie_value      = $this->_customer_id . '||' . $this->_session_expiration . '||' . $this->_session_expiring . '||' . $cookie_hash;
			$this->_has_cookie = true;

	    	// Set the cookie
	    	wc_setcookie( $this->_cookie, $cookie_value, $this->_session_expiration, apply_filters( 'wc_session_use_secure_cookie', false ) );
	    }
    }

    /**
     * Return true if the current user has an active session, i.e. a cookie to retrieve values
     * @return boolean
     */
    public function has_session() {
    	return isset( $_COOKIE[ $this->_cookie ] ) || $this->_has_cookie || is_user_logged_in();
    }

    /**
     * set_session_expiration function.
     *
     * @access public
     * @return void
     */
    public function set_session_expiration() {
	    $this->_session_expiring    = time() + intval( apply_filters( 'wc_session_expiring', 60 * 60 * 47 ) ); // 47 Hours
		$this->_session_expiration  = time() + intval( apply_filters( 'wc_session_expiration', 60 * 60 * 48 ) ); // 48 Hours
    }

	/**
	 * Generate a unique customer ID for guests, or return user ID if logged in. 
	 * 
	 * Uses Portable PHP password hashing framework to generate a unique cryptographically strong ID.
	 *
	 * @access public
	 * @return int|string
	 */
	public function generate_customer_id() {
		if ( is_user_logged_in() ) {
			return get_current_user_id();
		} else {
			require_once( ABSPATH . 'wp-includes/class-phpass.php');
			$hasher = new PasswordHash( 8, false );
			return md5( $hasher->get_random_bytes( 32 ) );
		}
	}

	/**
	 * get_session_cookie function.
	 *
	 * @access public
	 * @return mixed
	 */
	public function get_session_cookie() {
		if ( empty( $_COOKIE[ $this->_cookie ] ) ) {
			return false;
		}

		list( $customer_id, $session_expiration, $session_expiring, $cookie_hash ) = explode( '||', $_COOKIE[ $this->_cookie ] );

		// Validate hash
		$to_hash = $customer_id . $session_expiration;
		$hash    = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );

		if ( $hash != $cookie_hash ) {
			return false;
		}

		return array( $customer_id, $session_expiration, $session_expiring, $cookie_hash );
	}

	/**
	 * get_session_data function.
	 *
	 * @access public
	 * @return array
	 */
	public function get_session_data() {
		return (array) get_option( '_wc_session_' . $this->_customer_id, array() );
	}

    /**
     * save_data function.
     *
     * @access public
     * @return void
     */
    public function save_data() {
    	// Dirty if something changed - prevents saving nothing new
    	if ( $this->_dirty && $this->has_session() ) {

			$session_option        = '_wc_session_' . $this->_customer_id;
			$session_expiry_option = '_wc_session_expires_' . $this->_customer_id;

	    	if ( false === get_option( $session_option ) ) {
	    		add_option( $session_option, $this->_data, '', 'no' );
		    	add_option( $session_expiry_option, $this->_session_expiration, '', 'no' );
	    	} else {
		    	update_option( $session_option, $this->_data );
	    	}
	    }
    }

    /**
	 * cleanup_sessions function.
	 *
	 * @access public
	 * @return void
	 */
	public function cleanup_sessions() {
		global $wpdb;

		if ( ! defined( 'WP_SETUP_CONFIG' ) && ! defined( 'WP_INSTALLING' ) ) {
			$now                = time();
			$expired_sessions   = array();
			$wc_session_expires = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE '_wc_session_expires_%'" );

			foreach ( $wc_session_expires as $wc_session_expire ) {
				if ( $now > intval( $wc_session_expire->option_value ) ) {
					$session_id         = substr( $wc_session_expire->option_name, 20 );
					$expired_sessions[] = $wc_session_expire->option_name;  // Expires key
					$expired_sessions[] = "_wc_session_$session_id"; // Session key
				}
			}

			if ( ! empty( $expired_sessions ) ) {
				$expired_sessions_chunked = array_chunk( $expired_sessions, 100 );
				foreach ( $expired_sessions_chunked as $chunk ) {
					$option_names = implode( "','", $chunk );
					$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name IN ('$option_names')" );
				}
			}
		}
	}
}