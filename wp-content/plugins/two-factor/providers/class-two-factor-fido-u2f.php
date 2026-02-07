<?php
/**
 * Class for creating a FIDO Universal 2nd Factor provider.
 *
 * @package Two_Factor
 */

/**
 * Class for creating a FIDO Universal 2nd Factor provider.
 *
 * @since 0.1-dev
 *
 * @package Two_Factor
 */
class Two_Factor_FIDO_U2F extends Two_Factor_Provider {

	/**
	 * U2F Library
	 *
	 * @var u2flib_server\U2F
	 */
	public static $u2f;

	/**
	 * The user meta registered key.
	 *
	 * @type string
	 */
	const REGISTERED_KEY_USER_META_KEY = '_two_factor_fido_u2f_registered_key';

	/**
	 * The user meta authenticate data.
	 *
	 * @type string
	 */
	const AUTH_DATA_USER_META_KEY = '_two_factor_fido_u2f_login_request';

	/**
	 * Version number for the bundled assets.
	 *
	 * @var string
	 */
	const U2F_ASSET_VERSION = '0.2.1';

	/**
	 * Class constructor.
	 *
	 * @since 0.1-dev
	 */
	protected function __construct() {
		if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
			return;
		}

		require_once TWO_FACTOR_DIR . 'includes/Yubico/U2F.php';
		self::$u2f = new u2flib_server\U2F( self::get_u2f_app_id() );

		require_once TWO_FACTOR_DIR . 'providers/class-two-factor-fido-u2f-admin.php';
		Two_Factor_FIDO_U2F_Admin::add_hooks();

		// Ensure the script dependencies have been registered before they're enqueued at a later priority.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 5 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 5 );
		add_action( 'login_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 5 );

		add_action( 'two_factor_user_options_' . __CLASS__, array( $this, 'user_options' ) );

		parent::__construct();
	}

	/**
	 * Get the asset version number.
	 *
	 * TODO: There should be a plugin-level helper for getting the current plugin version.
	 *
	 * @return string
	 */
	public static function asset_version() {
		return self::U2F_ASSET_VERSION;
	}

	/**
	 * Return the U2F AppId. U2F requires the AppID to use HTTPS
	 * and a top-level domain.
	 *
	 * @return string AppID URI
	 */
	public static function get_u2f_app_id() {
		$url_parts = wp_parse_url( home_url() );

		if ( ! empty( $url_parts['port'] ) ) {
			return sprintf( 'https://%s:%d', $url_parts['host'], $url_parts['port'] );
		} else {
			return sprintf( 'https://%s', $url_parts['host'] );
		}
	}

	/**
	 * Returns the name of the provider.
	 *
	 * @since 0.1-dev
	 */
	public function get_label() {
		return _x( 'FIDO U2F Security Keys', 'Provider Label', 'two-factor' );
	}

	/**
	 * Returns the "continue with" text provider for the login screen.
	 *
	 * @since 0.9.0
	 */
	public function get_alternative_provider_label() {
		return __( 'Use your security key', 'two-factor' );
	}

	/**
	 * Register script dependencies used during login and when
	 * registering keys in the WP admin.
	 *
	 * @return void
	 */
	public static function enqueue_scripts() {
		wp_register_script(
			'fido-u2f-api',
			plugins_url( 'includes/Google/u2f-api.js', __DIR__ ),
			null,
			self::asset_version(),
			true
		);

		wp_register_script(
			'fido-u2f-login',
			plugins_url( 'js/fido-u2f-login.js', __FILE__ ),
			array( 'jquery', 'fido-u2f-api' ),
			self::asset_version(),
			true
		);
	}

	/**
	 * Prints the form that prompts the user to authenticate.
	 *
	 * @since 0.1-dev
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 * @return void
	 */
	public function authentication_page( $user ) {
		require_once ABSPATH . '/wp-admin/includes/template.php';

		// U2F doesn't work without HTTPS.
		if ( ! is_ssl() ) {
			?>
			<p><?php esc_html_e( 'U2F requires an HTTPS connection. Please use an alternative 2nd factor method.', 'two-factor' ); ?></p>
			<?php

			return;
		}

		try {
			$keys = self::get_security_keys( $user->ID );
			$data = self::$u2f->getAuthenticateData( $keys );
			update_user_meta( $user->ID, self::AUTH_DATA_USER_META_KEY, $data );
		} catch ( Exception $e ) {
			?>
			<p><?php esc_html_e( 'An error occurred while creating authentication data.', 'two-factor' ); ?></p>
			<?php
			return;
		}

		wp_localize_script(
			'fido-u2f-login',
			'u2fL10n',
			array(
				'request' => $data,
			)
		);

		wp_enqueue_script( 'fido-u2f-login' );

		?>
		<p><?php esc_html_e( 'Now insert (and tap) your Security Key.', 'two-factor' ); ?></p>
		<input type="hidden" name="u2f_response" id="u2f_response" />
		<?php
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
		$requests = get_user_meta( $user->ID, self::AUTH_DATA_USER_META_KEY, true );

		$response = json_decode( stripslashes( $_REQUEST['u2f_response'] ) );

		$keys = self::get_security_keys( $user->ID );

		try {
			$reg = self::$u2f->doAuthenticate( $requests, $keys, $response );

			$reg->last_used = time();

			self::update_security_key( $user->ID, $reg );

			return true;
		} catch ( Exception $e ) {
			return false;
		}
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
		return (bool) self::get_security_keys( $user->ID );
	}

	/**
	 * Inserts markup at the end of the user profile field for this provider.
	 *
	 * @since 0.1-dev
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 */
	public function user_options( $user ) {
		?>
		<p>
			<?php esc_html_e( 'Requires an HTTPS connection. Configure your security keys in the "Security Keys" section below.', 'two-factor' ); ?>
		</p>
		<?php
	}

	/**
	 * Add registered security key to a user.
	 *
	 * @since 0.1-dev
	 *
	 * @param int    $user_id  User ID.
	 * @param object $register The data of registered security key.
	 * @return int|bool Meta ID on success, false on failure.
	 */
	public static function add_security_key( $user_id, $register ) {
		if ( ! is_numeric( $user_id ) ) {
			return false;
		}

		if (
			! is_object( $register )
				|| ! property_exists( $register, 'keyHandle' ) || empty( $register->keyHandle ) // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				|| ! property_exists( $register, 'publicKey' ) || empty( $register->publicKey ) // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				|| ! property_exists( $register, 'certificate' ) || empty( $register->certificate )
				|| ! property_exists( $register, 'counter' ) || ( -1 > $register->counter )
		) {
			return false;
		}

		$register = array(
			'keyHandle'   => $register->keyHandle, // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			'publicKey'   => $register->publicKey, // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			'certificate' => $register->certificate,
			'counter'     => $register->counter,
		);

		$register['name']      = __( 'New Security Key', 'two-factor' );
		$register['added']     = time();
		$register['last_used'] = $register['added'];

		return add_user_meta( $user_id, self::REGISTERED_KEY_USER_META_KEY, $register );
	}

	/**
	 * Retrieve registered security keys for a user.
	 *
	 * @since 0.1-dev
	 *
	 * @param int $user_id User ID.
	 * @return array|bool Array of keys on success, false on failure.
	 */
	public static function get_security_keys( $user_id ) {
		if ( ! is_numeric( $user_id ) ) {
			return false;
		}

		$keys = get_user_meta( $user_id, self::REGISTERED_KEY_USER_META_KEY );
		if ( $keys ) {
			foreach ( $keys as &$key ) {
				$key = (object) $key;
			}
			unset( $key );
		}

		return $keys;
	}

	/**
	 * Update registered security key.
	 *
	 * Use the $prev_value parameter to differentiate between meta fields with the
	 * same key and user ID.
	 *
	 * If the meta field for the user does not exist, it will be added.
	 *
	 * @since 0.1-dev
	 *
	 * @param int    $user_id  User ID.
	 * @param object $data The data of registered security key.
	 * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
	 */
	public static function update_security_key( $user_id, $data ) {
		if ( ! is_numeric( $user_id ) ) {
			return false;
		}

		if (
			! is_object( $data )
				|| ! property_exists( $data, 'keyHandle' ) || empty( $data->keyHandle ) // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				|| ! property_exists( $data, 'publicKey' ) || empty( $data->publicKey ) // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				|| ! property_exists( $data, 'certificate' ) || empty( $data->certificate )
				|| ! property_exists( $data, 'counter' ) || ( -1 > $data->counter )
		) {
			return false;
		}

		$keys = self::get_security_keys( $user_id );
		if ( $keys ) {
			foreach ( $keys as $key ) {
				if ( $key->keyHandle === $data->keyHandle ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					return update_user_meta( $user_id, self::REGISTERED_KEY_USER_META_KEY, (array) $data, (array) $key );
				}
			}
		}

		return self::add_security_key( $user_id, $data );
	}

	/**
	 * Remove registered security key matching criteria from a user.
	 *
	 * @since 0.1-dev
	 *
	 * @param int    $user_id   User ID.
	 * @param string $keyHandle Optional. Key handle.
	 * @return bool True on success, false on failure.
	 */
	public static function delete_security_key( $user_id, $keyHandle = null ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		global $wpdb;

		if ( ! is_numeric( $user_id ) ) {
			return false;
		}

		$user_id = absint( $user_id );
		if ( ! $user_id ) {
			return false;
		}

		$keyHandle = wp_unslash( $keyHandle ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$keyHandle = maybe_serialize( $keyHandle ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

		$query = $wpdb->prepare( "SELECT umeta_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND user_id = %d", self::REGISTERED_KEY_USER_META_KEY, $user_id );

		if ( $keyHandle ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			$key_handle_lookup = sprintf( ':"%s";s:', $keyHandle ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			$query .= $wpdb->prepare(
				' AND meta_value LIKE %s',
				'%' . $wpdb->esc_like( $key_handle_lookup ) . '%'
			);
		}

		$meta_ids = $wpdb->get_col( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( ! count( $meta_ids ) ) {
			return false;
		}

		foreach ( $meta_ids as $meta_id ) {
			delete_metadata_by_mid( 'user', $meta_id );
		}

		return true;
	}

	/**
	 * Return user meta keys to delete during plugin uninstall.
	 *
	 * @return array
	 */
	public static function uninstall_user_meta_keys() {
		return array(
			self::REGISTERED_KEY_USER_META_KEY,
			self::AUTH_DATA_USER_META_KEY,
			'_two_factor_fido_u2f_register_request', // From Two_Factor_FIDO_U2F_Admin which is not loaded during uninstall.
		);
	}
}
