<?php
/**
 * Error Protection API: WP_Recovery_Mode class
 *
 * @package WordPress
 * @since   5.2.0
 */

/**
 * Core class used to implement Recovery Mode.
 *
 * @since 5.2.0
 */
class WP_Recovery_Mode {

	const EXIT_ACTION = 'exit_recovery_mode';

	/**
	 * Service to handle cookies.
	 *
	 * @since 5.2.0
	 * @var WP_Recovery_Mode_Cookie_Service
	 */
	private $cookie_service;

	/**
	 * Service to generate a recovery mode key.
	 *
	 * @since 5.2.0
	 * @var WP_Recovery_Mode_Key_Service
	 */
	private $key_service;

	/**
	 * Service to generate and validate recovery mode links.
	 *
	 * @since 5.2.0
	 * @var WP_Recovery_Mode_Link_Service
	 */
	private $link_service;

	/**
	 * Service to handle sending an email with a recovery mode link.
	 *
	 * @since 5.2.0
	 * @var WP_Recovery_Mode_Email_Service
	 */
	private $email_service;

	/**
	 * Is recovery mode initialized.
	 *
	 * @since 5.2.0
	 * @var bool
	 */
	private $is_initialized = false;

	/**
	 * Is recovery mode active in this session.
	 *
	 * @since 5.2.0
	 * @var bool
	 */
	private $is_active = false;

	/**
	 * Get an ID representing the current recovery mode session.
	 *
	 * @since 5.2.0
	 * @var string
	 */
	private $session_id = '';

	/**
	 * WP_Recovery_Mode constructor.
	 *
	 * @since 5.2.0
	 */
	public function __construct() {
		$this->cookie_service = new WP_Recovery_Mode_Cookie_Service();
		$this->key_service    = new WP_Recovery_Mode_Key_Service();
		$this->link_service   = new WP_Recovery_Mode_Link_Service( $this->cookie_service, $this->key_service );
		$this->email_service  = new WP_Recovery_Mode_Email_Service( $this->link_service );
	}

	/**
	 * Initialize recovery mode for the current request.
	 *
	 * @since 5.2.0
	 */
	public function initialize() {
		$this->is_initialized = true;

		add_action( 'wp_logout', array( $this, 'exit_recovery_mode' ) );
		add_action( 'login_form_' . self::EXIT_ACTION, array( $this, 'handle_exit_recovery_mode' ) );
		add_action( 'recovery_mode_clean_expired_keys', array( $this, 'clean_expired_keys' ) );

		if ( ! wp_next_scheduled( 'recovery_mode_clean_expired_keys' ) && ! wp_installing() ) {
			wp_schedule_event( time(), 'daily', 'recovery_mode_clean_expired_keys' );
		}

		if ( defined( 'WP_RECOVERY_MODE_SESSION_ID' ) ) {
			$this->is_active  = true;
			$this->session_id = WP_RECOVERY_MODE_SESSION_ID;

			return;
		}

		if ( $this->cookie_service->is_cookie_set() ) {
			$this->handle_cookie();

			return;
		}

		$this->link_service->handle_begin_link( $this->get_link_ttl() );
	}

	/**
	 * Checks whether recovery mode is active.
	 *
	 * This will not change after recovery mode has been initialized. {@see WP_Recovery_Mode::run()}.
	 *
	 * @since 5.2.0
	 *
	 * @return bool True if recovery mode is active, false otherwise.
	 */
	public function is_active() {
		return $this->is_active;
	}

	/**
	 * Gets the recovery mode session ID.
	 *
	 * @since 5.2.0
	 *
	 * @return string The session ID if recovery mode is active, empty string otherwise.
	 */
	public function get_session_id() {
		return $this->session_id;
	}

	/**
	 * Checks whether recovery mode has been initialized.
	 *
	 * Recovery mode should not be used until this point. Initialization happens immediately before loading plugins.
	 *
	 * @since 5.2.0
	 *
	 * @return bool
	 */
	public function is_initialized() {
		return $this->is_initialized;
	}

	/**
	 * Handles a fatal error occurring.
	 *
	 * The calling API should immediately die() after calling this function.
	 *
	 * @since 5.2.0
	 *
	 * @param array $error Error details from {@see error_get_last()}
	 * @return true|WP_Error True if the error was handled and headers have already been sent.
	 *                       Or the request will exit to try and catch multiple errors at once.
	 *                       WP_Error if an error occurred preventing it from being handled.
	 */
	public function handle_error( array $error ) {

		$extension = $this->get_extension_for_error( $error );

		if ( ! $extension || $this->is_network_plugin( $extension ) ) {
			return new WP_Error( 'invalid_source', __( 'Error not caused by a plugin or theme.' ) );
		}

		if ( ! $this->is_active() ) {
			if ( ! is_protected_endpoint() ) {
				return new WP_Error( 'non_protected_endpoint', __( 'Error occurred on a non-protected endpoint.' ) );
			}

			if ( ! function_exists( 'wp_generate_password' ) ) {
				require_once ABSPATH . WPINC . '/pluggable.php';
			}

			return $this->email_service->maybe_send_recovery_mode_email( $this->get_email_rate_limit(), $error, $extension );
		}

		if ( ! $this->store_error( $error ) ) {
			return new WP_Error( 'storage_error', __( 'Failed to store the error.' ) );
		}

		if ( headers_sent() ) {
			return true;
		}

		$this->redirect_protected();
	}

	/**
	 * Ends the current recovery mode session.
	 *
	 * @since 5.2.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function exit_recovery_mode() {
		if ( ! $this->is_active() ) {
			return false;
		}

		$this->email_service->clear_rate_limit();
		$this->cookie_service->clear_cookie();

		wp_paused_plugins()->delete_all();
		wp_paused_themes()->delete_all();

		return true;
	}

	/**
	 * Handles a request to exit Recovery Mode.
	 *
	 * @since 5.2.0
	 */
	public function handle_exit_recovery_mode() {
		$redirect_to = wp_get_referer();

		// Safety check in case referrer returns false.
		if ( ! $redirect_to ) {
			$redirect_to = is_user_logged_in() ? admin_url() : home_url();
		}

		if ( ! $this->is_active() ) {
			wp_safe_redirect( $redirect_to );
			die;
		}

		if ( ! isset( $_GET['action'] ) || self::EXIT_ACTION !== $_GET['action'] ) {
			return;
		}

		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], self::EXIT_ACTION ) ) {
			wp_die( __( 'Exit recovery mode link expired.' ) );
		}

		if ( ! $this->exit_recovery_mode() ) {
			wp_die( __( 'Failed to exit recovery mode. Please try again later.' ) );
		}

		wp_safe_redirect( $redirect_to );
		die;
	}

	/**
	 * Cleans any recovery mode keys that have expired according to the link TTL.
	 *
	 * Executes on a daily cron schedule.
	 *
	 * @since 5.2.0
	 */
	public function clean_expired_keys() {
		$this->key_service->clean_expired_keys( $this->get_link_ttl() );
	}

	/**
	 * Handles checking for the recovery mode cookie and validating it.
	 *
	 * @since 5.2.0
	 */
	protected function handle_cookie() {
		$validated = $this->cookie_service->validate_cookie();

		if ( is_wp_error( $validated ) ) {
			$this->cookie_service->clear_cookie();

			wp_die( $validated, '' );
		}

		$session_id = $this->cookie_service->get_session_id_from_cookie();
		if ( is_wp_error( $session_id ) ) {
			$this->cookie_service->clear_cookie();

			wp_die( $session_id, '' );
		}

		$this->is_active  = true;
		$this->session_id = $session_id;
	}

	/**
	 * Gets the rate limit between sending new recovery mode email links.
	 *
	 * @since 5.2.0
	 *
	 * @return int Rate limit in seconds.
	 */
	protected function get_email_rate_limit() {
		/**
		 * Filter the rate limit between sending new recovery mode email links.
		 *
		 * @since 5.2.0
		 *
		 * @param int $rate_limit Time to wait in seconds. Defaults to 1 day.
		 */
		return apply_filters( 'recovery_mode_email_rate_limit', DAY_IN_SECONDS );
	}

	/**
	 * Gets the number of seconds the recovery mode link is valid for.
	 *
	 * @since 5.2.0
	 *
	 * @return int Interval in seconds.
	 */
	protected function get_link_ttl() {

		$rate_limit = $this->get_email_rate_limit();
		$valid_for  = $rate_limit;

		/**
		 * Filter the amount of time the recovery mode email link is valid for.
		 *
		 * The ttl must be at least as long as the email rate limit.
		 *
		 * @since 5.2.0
		 *
		 * @param int $valid_for The number of seconds the link is valid for.
		 */
		$valid_for = apply_filters( 'recovery_mode_email_link_ttl', $valid_for );

		return max( $valid_for, $rate_limit );
	}

	/**
	 * Gets the extension that the error occurred in.
	 *
	 * @since 5.2.0
	 *
	 * @global array $wp_theme_directories
	 *
	 * @param array  $error Error that was triggered.
	 *
	 * @return array|false {
	 *      @type string  $slug  The extension slug. This is the plugin or theme's directory.
	 *      @type string  $type  The extension type. Either 'plugin' or 'theme'.
	 * }
	 */
	protected function get_extension_for_error( $error ) {
		global $wp_theme_directories;

		if ( ! isset( $error['file'] ) ) {
			return false;
		}

		if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
			return false;
		}

		$error_file    = wp_normalize_path( $error['file'] );
		$wp_plugin_dir = wp_normalize_path( WP_PLUGIN_DIR );

		if ( 0 === strpos( $error_file, $wp_plugin_dir ) ) {
			$path  = str_replace( $wp_plugin_dir . '/', '', $error_file );
			$parts = explode( '/', $path );

			return array(
				'type' => 'plugin',
				'slug' => $parts[0],
			);
		}

		if ( empty( $wp_theme_directories ) ) {
			return false;
		}

		foreach ( $wp_theme_directories as $theme_directory ) {
			$theme_directory = wp_normalize_path( $theme_directory );

			if ( 0 === strpos( $error_file, $theme_directory ) ) {
				$path  = str_replace( $theme_directory . '/', '', $error_file );
				$parts = explode( '/', $path );

				return array(
					'type' => 'theme',
					'slug' => $parts[0],
				);
			}
		}

		return false;
	}

	/**
	 * Checks whether the given extension a network activated plugin.
	 *
	 * @since 5.2.0
	 *
	 * @param array $extension Extension data.
	 * @return bool True if network plugin, false otherwise.
	 */
	protected function is_network_plugin( $extension ) {
		if ( 'plugin' !== $extension['type'] ) {
			return false;
		}

		if ( ! is_multisite() ) {
			return false;
		}

		$network_plugins = wp_get_active_network_plugins();

		foreach ( $network_plugins as $plugin ) {
			if ( 0 === strpos( $plugin, $extension['slug'] . '/' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Stores the given error so that the extension causing it is paused.
	 *
	 * @since 5.2.0
	 *
	 * @param array $error Error that was triggered.
	 * @return bool True if the error was stored successfully, false otherwise.
	 */
	protected function store_error( $error ) {
		$extension = $this->get_extension_for_error( $error );

		if ( ! $extension ) {
			return false;
		}

		switch ( $extension['type'] ) {
			case 'plugin':
				return wp_paused_plugins()->set( $extension['slug'], $error );
			case 'theme':
				return wp_paused_themes()->set( $extension['slug'], $error );
			default:
				return false;
		}
	}

	/**
	 * Redirects the current request to allow recovering multiple errors in one go.
	 *
	 * The redirection will only happen when on a protected endpoint.
	 *
	 * It must be ensured that this method is only called when an error actually occurred and will not occur on the
	 * next request again. Otherwise it will create a redirect loop.
	 *
	 * @since 5.2.0
	 */
	protected function redirect_protected() {
		// Pluggable is usually loaded after plugins, so we manually include it here for redirection functionality.
		if ( ! function_exists( 'wp_safe_redirect' ) ) {
			require_once ABSPATH . WPINC . '/pluggable.php';
		}

		$scheme = is_ssl() ? 'https://' : 'http://';

		$url = "{$scheme}{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
		wp_safe_redirect( $url );
		exit;
	}
}
