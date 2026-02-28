<?php
/**
 * Class for creating an email provider.
 *
 * @package Two_Factor
 */

/**
 * Class for creating an email provider.
 *
 * @since 0.1-dev
 *
 * @package Two_Factor
 */
class Two_Factor_Email extends Two_Factor_Provider {

	/**
	 * The user meta token key.
	 *
	 * @var string
	 */
	const TOKEN_META_KEY = '_two_factor_email_token';

	/**
	 * Store the timestamp when the token was generated.
	 *
	 * @var string
	 */
	const TOKEN_META_KEY_TIMESTAMP = '_two_factor_email_token_timestamp';

	/**
	 * Name of the input field used for code resend.
	 *
	 * @var string
	 */
	const INPUT_NAME_RESEND_CODE = 'two-factor-email-code-resend';

	/**
	 * Class constructor.
	 *
	 * @since 0.1-dev
	 */
	protected function __construct() {
		add_action( 'two_factor_user_options_' . __CLASS__, array( $this, 'user_options' ) );
		parent::__construct();
	}

	/**
	 * Returns the name of the provider.
	 *
	 * @since 0.1-dev
	 */
	public function get_label() {
		return _x( 'Email', 'Provider Label', 'two-factor' );
	}

	/**
	 * Returns the "continue with" text provider for the login screen.
	 *
	 * @since 0.9.0
	 */
	public function get_alternative_provider_label() {
		return __( 'Send a code to your email', 'two-factor' );
	}

	/**
	 * Get the email token length.
	 *
	 * @return int Email token string length.
	 */
	private function get_token_length() {
		/**
		 * Number of characters in the email token.
		 *
		 * @param int $token_length Number of characters in the email token.
		 */
		$token_length = (int) apply_filters( 'two_factor_email_token_length', 8 );

		return $token_length;
	}

	/**
	 * Generate the user token.
	 *
	 * @since 0.1-dev
	 *
	 * @param int $user_id User ID.
	 * @return string
	 */
	public function generate_token( $user_id ) {
		$token = $this->get_code( $this->get_token_length() );

		update_user_meta( $user_id, self::TOKEN_META_KEY_TIMESTAMP, time() );
		update_user_meta( $user_id, self::TOKEN_META_KEY, wp_hash( $token ) );

		return $token;
	}

	/**
	 * Check if user has a valid token already.
	 *
	 * @param  int $user_id User ID.
	 * @return boolean      If user has a valid email token.
	 */
	public function user_has_token( $user_id ) {
		$hashed_token = $this->get_user_token( $user_id );

		if ( ! empty( $hashed_token ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Has the user token validity timestamp expired.
	 *
	 * @param integer $user_id User ID.
	 *
	 * @return boolean
	 */
	public function user_token_has_expired( $user_id ) {
		$token_lifetime = $this->user_token_lifetime( $user_id );
		$token_ttl      = $this->user_token_ttl( $user_id );

		// Invalid token lifetime is considered an expired token.
		if ( is_int( $token_lifetime ) && $token_lifetime <= $token_ttl ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the lifetime of a user token in seconds.
	 *
	 * @param integer $user_id User ID.
	 *
	 * @return integer|null Return `null` if the lifetime can't be measured.
	 */
	public function user_token_lifetime( $user_id ) {
		$timestamp = intval( get_user_meta( $user_id, self::TOKEN_META_KEY_TIMESTAMP, true ) );

		if ( ! empty( $timestamp ) ) {
			return time() - $timestamp;
		}

		return null;
	}

	/**
	 * Return the token time-to-live for a user.
	 *
	 * @param integer $user_id User ID.
	 *
	 * @return integer
	 */
	public function user_token_ttl( $user_id ) {
		$token_ttl = 15 * MINUTE_IN_SECONDS;

		/**
		 * Number of seconds the token is considered valid
		 * after the generation.
		 *
		 * @deprecated 0.11.0 Use {@see 'two_factor_email_token_ttl'} instead.
		 *
		 * @param integer $token_ttl Token time-to-live in seconds.
		 * @param integer $user_id User ID.
		 */
		$token_ttl = (int) apply_filters_deprecated( 'two_factor_token_ttl', array( $token_ttl, $user_id ), '0.11.0', 'two_factor_email_token_ttl' );

		/**
		 * Number of seconds the token is considered valid
		 * after the generation.
		 *
		 * @param integer $token_ttl Token time-to-live in seconds.
		 * @param integer $user_id User ID.
		 */
		return (int) apply_filters( 'two_factor_email_token_ttl', $token_ttl, $user_id );
	}

	/**
	 * Get the authentication token for the user.
	 *
	 * @param  int $user_id    User ID.
	 *
	 * @return string|boolean  User token or `false` if no token found.
	 */
	public function get_user_token( $user_id ) {
		$hashed_token = get_user_meta( $user_id, self::TOKEN_META_KEY, true );

		if ( ! empty( $hashed_token ) && is_string( $hashed_token ) ) {
			return $hashed_token;
		}

		return false;
	}

	/**
	 * Validate the user token.
	 *
	 * @since 0.1-dev
	 *
	 * @param int    $user_id User ID.
	 * @param string $token User token.
	 * @return boolean
	 */
	public function validate_token( $user_id, $token ) {
		$hashed_token = $this->get_user_token( $user_id );

		// Bail if token is empty or it doesn't match.
		if ( empty( $hashed_token ) || ! hash_equals( wp_hash( $token ), $hashed_token ) ) {
			return false;
		}

		if ( $this->user_token_has_expired( $user_id ) ) {
			return false;
		}

		// Ensure the token can be used only once.
		$this->delete_token( $user_id );

		return true;
	}

	/**
	 * Delete the user token.
	 *
	 * @since 0.1-dev
	 *
	 * @param int $user_id User ID.
	 */
	public function delete_token( $user_id ) {
		delete_user_meta( $user_id, self::TOKEN_META_KEY );
	}

	/**
	 * Generate and email the user token.
	 *
	 * @since 0.1-dev
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 * @return bool Whether the email contents were sent successfully.
	 */
	public function generate_and_email_token( $user ) {
		$token = $this->generate_token( $user->ID );

		/* translators: %s: site name */
		$subject = wp_strip_all_tags( sprintf( __( 'Your login confirmation code for %s', 'two-factor' ), wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) );
		/* translators: %s: token */
		$message = wp_strip_all_tags( sprintf( __( 'Enter %s to log in.', 'two-factor' ), $token ) );

		/**
		 * Filter the token email subject.
		 *
		 * @param string $subject The email subject line.
		 * @param int    $user_id The ID of the user.
		 */
		$subject = apply_filters( 'two_factor_token_email_subject', $subject, $user->ID );

		/**
		 * Filter the token email message.
		 *
		 * @param string $message The email message.
		 * @param string $token   The token.
		 * @param int    $user_id The ID of the user.
		 */
		$message = apply_filters( 'two_factor_token_email_message', $message, $token, $user->ID );

		return wp_mail( $user->user_email, $subject, $message ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_wp_mail
	}

	/**
	 * Prints the form that prompts the user to authenticate.
	 *
	 * @since 0.1-dev
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 */
	public function authentication_page( $user ) {
		if ( ! $user ) {
			return;
		}

		if ( ! $this->user_has_token( $user->ID ) || $this->user_token_has_expired( $user->ID ) ) {
			$this->generate_and_email_token( $user );
		}

		$token_length      = $this->get_token_length();
		$token_placeholder = str_repeat( 'X', $token_length );

		require_once ABSPATH . '/wp-admin/includes/template.php';
		?>
		<p class="two-factor-prompt"><?php esc_html_e( 'A verification code has been sent to the email address associated with your account.', 'two-factor' ); ?></p>
		<p>
			<label for="authcode"><?php esc_html_e( 'Verification Code:', 'two-factor' ); ?></label>
			<input type="text" inputmode="numeric" name="two-factor-email-code" id="authcode" class="input authcode" value="" size="20" pattern="[0-9 ]*" autocomplete="one-time-code" placeholder="<?php echo esc_attr( $token_placeholder ); ?>" data-digits="<?php echo esc_attr( $token_length ); ?>" />
			<?php submit_button( __( 'Verify', 'two-factor' ) ); ?>
		</p>
		<p class="two-factor-email-resend">
			<input type="submit" class="button" name="<?php echo esc_attr( self::INPUT_NAME_RESEND_CODE ); ?>" value="<?php esc_attr_e( 'Resend Code', 'two-factor' ); ?>" />
		</p>
		<script type="text/javascript">
			setTimeout( function(){
				var d;
				try{
					d = document.getElementById('authcode');
					d.value = '';
					d.focus();
				} catch(e){}
			}, 200);
		</script>
		<?php
	}

	/**
	 * Send the email code if missing or requested. Stop the authentication
	 * validation if a new token has been generated and sent.
	 *
	 * @param  WP_User $user WP_User object of the logged-in user.
	 * @return boolean
	 */
	public function pre_process_authentication( $user ) {
		if ( isset( $user->ID ) && isset( $_REQUEST[ self::INPUT_NAME_RESEND_CODE ] ) ) {
			$this->generate_and_email_token( $user );
			return true;
		}

		return false;
	}

	/**
	 * Validates the users input token.
	 *
	 * @since 0.1-dev
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 * @return boolean
	 */
	public function validate_authentication( $user ) {
		$code = $this->sanitize_code_from_request( 'two-factor-email-code' );
		if ( ! isset( $user->ID ) || ! $code ) {
			return false;
		}

		return $this->validate_token( $user->ID, $code );
	}

	/**
	 * Whether this Two Factor provider is configured and available for the user specified.
	 *
	 * @since 0.1-dev
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 * @return boolean
	 */
	public function is_available_for_user( $user ) {
		return true;
	}

	/**
	 * Inserts markup at the end of the user profile field for this provider.
	 *
	 * @since 0.1-dev
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 */
	public function user_options( $user ) {
		$email = $user->user_email;
		?>
		<p>
			<?php
			echo esc_html(
				sprintf(
				/* translators: %s: email address */
					__( 'Authentication codes will be sent to %s.', 'two-factor' ),
					$email
				)
			);
			?>
		</p>
		<?php
	}

	/**
	 * Return user meta keys to delete during plugin uninstall.
	 *
	 * @return array
	 */
	public static function uninstall_user_meta_keys() {
		return array(
			self::TOKEN_META_KEY,
			self::TOKEN_META_KEY_TIMESTAMP,
		);
	}
}
