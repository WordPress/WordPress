<?php
/**
 * Abstract class for managing user session tokens.
 *
 * @since 4.0.0
 */
abstract class WP_Session_Tokens {

	/**
	 * User ID.
	 *
	 * @since 4.0.0
	 * @access protected
	 * @var int User ID.
	 */
	protected $user_id;

	/**
	 * Protected constructor.
	 *
	 * @param int $user_id User whose session to manage.
	 */
	protected function __construct( $user_id ) {
		$this->user_id = $user_id;
	}

	/**
	 * Get a session token manager instance for a user.
	 *
	 * This method contains a filter that allows a plugin to swap out
	 * the session manager for a subclass of WP_Session_Tokens.
	 *
	 * @since 4.0.0
	 * @access public
	 * @static
	 *
	 * @param int $user_id User whose session to manage.
	 */
	final public static function get_instance( $user_id ) {
		/**
		 * Filter the session token manager used.
		 *
		 * @since 4.0.0
		 *
		 * @param string $session Name of class to use as the manager.
		 *                        Default 'WP_User_Meta_Session_Tokens'.
		 */
		$manager = apply_filters( 'session_token_manager', 'WP_User_Meta_Session_Tokens' );
		return new $manager( $user_id );
	}

	/**
	 * Hashes a token for storage.
	 *
	 * @since 4.0.0
	 * @access private
	 *
	 * @param string $token Token to hash.
	 * @return string A hash of the token (a verifier).
	 */
	final private function hash_token( $token ) {
		return hash( 'sha256', $token );
	}

	/**
	 * Validate a user's session token as authentic.
	 *
	 * Checks that the given token is present and hasn't expired.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $token Token to verify.
	 * @return bool Whether the token is valid for the user.
	 */
	final public function verify_token( $token ) {
		$verifier = $this->hash_token( $token );
		return (bool) $this->get_session( $verifier );
	}

	/**
	 * Generate a cookie session identification token.
	 *
	 * A session identification token is a long, random string. It is used to
	 * link a cookie to an expiration time and to ensure that cookies become
	 * invalidated upon logout. This function generates a token and stores it
	 * with the associated expiration time.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param int $expiration Session expiration timestamp.
	 * @return string Session identification token.
	 */
	final public function create_token( $expiration ) {
		/**
		 * Filter the information attached to the newly created session.
		 *
		 * Could be used in the future to attach information such as
		 * IP address or user agent to a session.
		 *
		 * @since 4.0.0
		 *
		 * @param array $session Array of extra data.
		 * @param int   $user_id User ID.
		 */
		$session = apply_filters( 'attach_session_information', array(), $this->user_id );
		$session['expiration'] = $expiration;

		$token = wp_generate_password( 43, false, false );

		$this->update_token( $token, $session );

		return $token;
	}

	/**
	 * Updates a session based on its token.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $token Token to update.
	 * @param array  $session Session information.
	 */
	final public function update_token( $token, $session ) {
		$verifier = $this->hash_token( $token );
		$this->update_session( $verifier, $session );
	}

	/**
	 * Destroy a session token.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $token Token to destroy.
	 */
	final public function destroy_token( $token ) {
		$verifier = $this->hash_token( $token );
		$this->update_session( $verifier, null );
	}

	/**
	 * Destroy all session tokens for this user,
	 * except a single token, presumably the one in use.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $token_to_keep Token to keep.
	 */
	final public function destroy_other_tokens( $token_to_keep ) {
		$verifier = $this->hash_token( $token_to_keep );
		$session = $this->get_session( $verifier );
		if ( $session ) {
			$this->destroy_other_sessions( $verifier );
		} else {
			$this->destroy_all_tokens();
		}
	}

	/**
	 * Determine whether a session token is still valid,
	 * based on expiration.
	 *
	 * @since 4.0.0
	 * @access protected
	 *
	 * @param array $session Session to check.
	 * @return bool Whether session is valid.
	 */
	final protected function is_still_valid( $session ) {
		return $session['expiration'] >= time();
	}

	/**
	 * Destroy all tokens for a user.
	 *
	 * @since 4.0.0
	 * @access public
	 */
	final public function destroy_all_tokens() {
		$this->destroy_all_sessions();
	}

	/**
	 * Destroy all tokens for all users.
	 *
	 * @since 4.0.0
	 * @access public
	 * @static
	 */
	final public static function destroy_all_tokens_for_all_users() {
		$manager = apply_filters( 'session_token_manager', 'WP_User_Meta_Session_Tokens' );
		call_user_func( array( $manager, 'drop_sessions' ) );
	}

	/**
	 * Retrieve all sessions of a user.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @return array Sessions of a user.
	 */
	final public function get_all_sessions() {
		return array_values( $this->get_sessions() );
	}

	/**
	 * This method should retrieve all sessions of a user, keyed by verifier.
	 *
	 * @since 4.0.0
	 * @access protected
	 *
	 * @return array Sessions of a user, keyed by verifier.
	 */
	abstract protected function get_sessions();

	/**
	 * This method should look up a session by its verifier (token hash).
	 *
	 * @since 4.0.0
	 * @access protected
	 *
	 * @param $verifier Verifier of the session to retrieve.
	 * @return array|null The session, or null if it does not exist.
	 */
	abstract protected function get_session( $verifier );

	/**
	 * This method should update a session by its verifier.
	 *
	 * Omitting the second argument should destroy the session.
	 *
	 * @since 4.0.0
	 * @access protected
	 *
	 * @param $verifier Verifier of the session to update.
	 */
	abstract protected function update_session( $verifier, $session = null );

	/**
	 * This method should destroy all session tokens for this user,
	 * except a single session passed.
	 *
	 * @since 4.0.0
	 * @access protected
	 *
	 * @param $verifier Verifier of the session to keep.
	 */
	abstract protected function destroy_other_sessions( $verifier );

	/**
	 * This method should destroy all sessions for a user.
	 *
	 * @since 4.0.0
	 * @access protected
	 */
	abstract protected function destroy_all_sessions();

	/**
	 * This static method should destroy all session tokens for all users.
	 *
	 * @since 4.0.0
	 * @access public
	 * @static
	 */
	public static function drop_sessions() {}
}

/**
 * Meta-based user sessions token manager.
 *
 * @since 4.0.0
 */
class WP_User_Meta_Session_Tokens extends WP_Session_Tokens {

	/**
	 * Get all sessions of a user.
	 *
	 * @since 4.0.0
	 * @access protected
	 *
	 * @return array Sessions of a user.
	 */
	protected function get_sessions() {
		$sessions = get_user_meta( $this->user_id, 'session_tokens', true );

		if ( ! is_array( $sessions ) ) {
			return array();
		}

		$sessions = array_map( array( $this, 'prepare_session' ), $sessions );
		return array_filter( $sessions, array( $this, 'is_still_valid' ) );
	}

	/**
	 * Converts an expiration to an array of session information.
	 *
	 * @param mixed $session Session or expiration.
	 * @return array Session.
	 */
	protected function prepare_session( $session ) {
		if ( is_int( $session ) ) {
			return array( 'expiration' => $session );
		}

		return $session;
	}

	/**
	 * Retrieve a session by its verifier (token hash).
	 *
	 * @since 4.0.0
	 * @access protected
	 *
	 * @param $verifier Verifier of the session to retrieve.
	 * @return array|null The session, or null if it does not exist
	 */
	protected function get_session( $verifier ) {
		$sessions = $this->get_sessions();

		if ( isset( $sessions[ $verifier ] ) ) {
			return $sessions[ $verifier ];
		}

		return null;
	}

	/**
	 * Update a session by its verifier.
	 *
	 * @since 4.0.0
	 * @access protected
	 *
	 * @param string $verifier Verifier of the session to update.
	 * @param array  $session  Optional. Session. Omitting this argument destroys the session.
	 */
	protected function update_session( $verifier, $session = null ) {
		$sessions = $this->get_sessions();

		if ( $session ) {
			$sessions[ $verifier ] = $session;
		} else {
			unset( $sessions[ $verifier ] );
		}

		$this->update_sessions( $sessions );
	}

	/**
	 * Update a user's sessions in the usermeta table.
	 *
	 * @since 4.0.0
	 * @access protected
	 *
	 * @param array $sessions Sessions.
	 */
	protected function update_sessions( $sessions ) {
		if ( ! has_filter( 'attach_session_information' ) ) {
			$sessions = wp_list_pluck( $sessions, 'expiration' );
		}

		if ( $sessions ) {
			update_user_meta( $this->user_id, 'session_tokens', $sessions );
		} else {
			delete_user_meta( $this->user_id, 'session_tokens' );
		}
	}

	/**
	 * Destroy all session tokens for a user, except a single session passed.
	 *
	 * @since 4.0.0
	 * @access protected
	 *
	 * @param $verifier Verifier of the session to keep.
	 */
	protected function destroy_other_sessions( $verifier ) {
		$session = $this->get_session( $verifier );
		$this->update_sessions( array( $verifier => $session ) );
	}

	/**
	 * Destroy all session tokens for a user.
	 *
	 * @since 4.0.0
	 * @access protected
	 */
	protected function destroy_all_sessions() {
		$this->update_sessions( array() );
	}

	/**
	 * Destroy all session tokens for all users.
	 *
	 * @since 4.0.0
	 * @access public
	 * @static
	 */
	public static function drop_sessions() {
		delete_metadata( 'user', false, 'session_tokens', false, true );
	}
}
