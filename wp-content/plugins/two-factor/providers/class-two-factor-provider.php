<?php
/**
 * Abstract class for creating two factor authentication providers.
 *
 * @package Two_Factor
 */

/**
 * Abstract class for creating two factor authentication providers.
 *
 * @since 0.1-dev
 *
 * @package Two_Factor
 */
abstract class Two_Factor_Provider {

	/**
	 * Ensures only one instance of the provider class exists in memory at any one time.
	 *
	 * @since 0.1-dev
	 */
	public static function get_instance() {
		static $instances = array();

		$class_name = static::class;

		if ( ! isset( $instances[ $class_name ] ) ) {
			$instances[ $class_name ] = new $class_name();
		}

		return $instances[ $class_name ];
	}

	/**
	 * Class constructor.
	 *
	 * @since 0.1-dev
	 */
	protected function __construct() {
		return $this;
	}

	/**
	 * Returns the name of the provider.
	 *
	 * @since 0.1-dev
	 *
	 * @return string
	 */
	abstract public function get_label();

	/**
	 * Returns the "continue with" text provider for the login screen.
	 *
	 * @since 0.9.0
	 *
	 * @return string
	 */
	public function get_alternative_provider_label() {
		return sprintf(
			/* translators: the two factor provider name */
			__( 'Use %s', 'two-factor' ),
			$this->get_label()
		);
	}

	/**
	 * Prints the name of the provider.
	 *
	 * @since 0.1-dev
	 */
	public function print_label() {
		echo esc_html( $this->get_label() );
	}

	/**
	 * Retrieves the provider key / slug.
	 *
	 * @since 0.9.0
	 *
	 * @return string
	 */
	public function get_key() {
		return get_class( $this );
	}

	/**
	 * Prints the form that prompts the user to authenticate.
	 *
	 * @since 0.1-dev
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 */
	abstract public function authentication_page( $user );

	/**
	 * Allow providers to do extra processing before the authentication.
	 * Return `true` to prevent the authentication and render the
	 * authentication page.
	 *
	 * @param  WP_User $user WP_User object of the logged-in user.
	 * @return boolean
	 */
	public function pre_process_authentication( $user ) {
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
	abstract public function validate_authentication( $user );

	/**
	 * Whether this Two Factor provider is configured and available for the user specified.
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 * @return boolean
	 */
	abstract public function is_available_for_user( $user );

	/**
	 * If this provider should be available for the user.
	 *
	 * @param WP_User|int $user WP_User object, user ID or null to resolve the current user.
	 *
	 * @return bool
	 */
	public static function is_supported_for_user( $user = null ) {
		$providers = Two_Factor_Core::get_supported_providers_for_user( $user );

		return isset( $providers[ static::class ] );
	}

	/**
	 * Generate a random eight-digit string to send out as an auth code.
	 *
	 * @since 0.1-dev
	 *
	 * @param int          $length The code length.
	 * @param string|array $chars Valid auth code characters.
	 * @return string
	 */
	public static function get_code( $length = 8, $chars = '1234567890' ) {
		$code = '';
		if ( is_array( $chars ) ) {
			$chars = implode( '', $chars );
		}
		for ( $i = 0; $i < $length; $i++ ) {
			$code .= substr( $chars, wp_rand( 0, strlen( $chars ) - 1 ), 1 );
		}
		return $code;
	}

	/**
	 * Sanitizes a numeric code to be used as an auth code.
	 *
	 * @param string $field  The _REQUEST field to check for the code.
	 * @param int    $length The valid expected length of the field.
	 * @return false|string Auth code on success, false if the field is not set or not expected length.
	 */
	public static function sanitize_code_from_request( $field, $length = 0 ) {
		if ( empty( $_REQUEST[ $field ] ) ) {
			return false;
		}

		$code = wp_unslash( $_REQUEST[ $field ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, handled by the core method already.
		$code = preg_replace( '/\s+/', '', $code );

		// Maybe validate the length.
		if ( $length && strlen( $code ) !== $length ) {
			return false;
		}

		return (string) $code;
	}

	/**
	 * Return the user meta keys that need to be deletated on plugin uninstall.
	 *
	 * @return array
	 */
	public static function uninstall_user_meta_keys() {
		return array();
	}

	/**
	 * Return the option keys that need to be deleted on plugin uninstall.
	 *
	 * Note: this method doesn't have access to the instantiated provider object.
	 *
	 * @return array
	 */
	public static function uninstall_options() {
		return array();
	}
}
