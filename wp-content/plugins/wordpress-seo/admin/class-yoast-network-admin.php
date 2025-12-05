<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals
 */

/**
 * Multisite utility class for network admin functionality.
 */
class Yoast_Network_Admin implements WPSEO_WordPress_AJAX_Integration, WPSEO_WordPress_Integration {

	/**
	 * Action identifier for updating plugin network options.
	 *
	 * @var string
	 */
	public const UPDATE_OPTIONS_ACTION = 'yoast_handle_network_options';

	/**
	 * Action identifier for restoring a site.
	 *
	 * @var string
	 */
	public const RESTORE_SITE_ACTION = 'yoast_restore_site';

	/**
	 * Gets the available sites as choices, e.g. for a dropdown.
	 *
	 * @param bool $include_empty Optional. Whether to include an initial placeholder choice.
	 *                            Default false.
	 * @param bool $show_title    Optional. Whether to show the title for each site. This requires
	 *                            switching through the sites, so has performance implications for
	 *                            sites that do not use a persistent cache.
	 *                            Default false.
	 *
	 * @return array Choices as $site_id => $site_label pairs.
	 */
	public function get_site_choices( $include_empty = false, $show_title = false ) {
		$choices = [];

		if ( $include_empty ) {
			$choices['-'] = __( 'None', 'wordpress-seo' );
		}

		$criteria = [
			'deleted'    => 0,
			'network_id' => get_current_network_id(),
		];
		$sites    = get_sites( $criteria );

		foreach ( $sites as $site ) {
			$site_name = $site->domain . $site->path;
			if ( $show_title ) {
				$site_name = $site->blogname . ' (' . $site->domain . $site->path . ')';
			}
			$choices[ $site->blog_id ] = $site->blog_id . ': ' . $site_name;

			$site_states = $this->get_site_states( $site );
			if ( ! empty( $site_states ) ) {
				$choices[ $site->blog_id ] .= ' [' . implode( ', ', $site_states ) . ']';
			}
		}

		return $choices;
	}

	/**
	 * Gets the states of a site.
	 *
	 * @param WP_Site $site Site object.
	 *
	 * @return array Array of $state_slug => $state_label pairs.
	 */
	public function get_site_states( $site ) {
		$available_states = [
			'public'   => __( 'public', 'wordpress-seo' ),
			'archived' => __( 'archived', 'wordpress-seo' ),
			'mature'   => __( 'mature', 'wordpress-seo' ),
			'spam'     => __( 'spam', 'wordpress-seo' ),
			'deleted'  => __( 'deleted', 'wordpress-seo' ),
		];

		$site_states = [];
		foreach ( $available_states as $state_slug => $state_label ) {
			if ( $site->$state_slug === '1' ) {
				$site_states[ $state_slug ] = $state_label;
			}
		}

		return $site_states;
	}

	/**
	 * Handles a request to update plugin network options.
	 *
	 * This method works similar to how option updates are handled in `wp-admin/options.php` and
	 * `wp-admin/network/settings.php`.
	 *
	 * @return void
	 */
	public function handle_update_options_request() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: Nonce verification will happen in verify_request below.
		if ( ! isset( $_POST['network_option_group'] ) || ! is_string( $_POST['network_option_group'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: Nonce verification will happen in verify_request below.
		$option_group = sanitize_text_field( wp_unslash( $_POST['network_option_group'] ) );

		if ( empty( $option_group ) ) {
			return;
		}

		$this->verify_request( "{$option_group}-network-options" );

		$whitelist_options = Yoast_Network_Settings_API::get()->get_whitelist_options( $option_group );

		if ( empty( $whitelist_options ) ) {
			add_settings_error( $option_group, 'settings_updated', __( 'You are not allowed to modify unregistered network settings.', 'wordpress-seo' ), 'error' );

			$this->terminate_request();
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification -- Nonce verified via `verify_request()` above.
		foreach ( $whitelist_options as $option_name ) {
			$value = null;
			if ( isset( $_POST[ $option_name ] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: Adding sanitize_text_field around this will break the saving of settings because it expects a string: https://github.com/Yoast/wordpress-seo/issues/12440.
				$value = wp_unslash( $_POST[ $option_name ] );
			}

			WPSEO_Options::update_site_option( $option_name, $value );
		}
		// phpcs:enable WordPress.Security.NonceVerification

		$settings_errors = get_settings_errors();
		if ( empty( $settings_errors ) ) {
			add_settings_error( $option_group, 'settings_updated', __( 'Settings Updated.', 'wordpress-seo' ), 'updated' );
		}

		$this->terminate_request();
	}

	/**
	 * Handles a request to restore a site's default settings.
	 *
	 * @return void
	 */
	public function handle_restore_site_request() {
		$this->verify_request( 'wpseo-network-restore', 'restore_site_nonce' );

		$option_group = 'wpseo_ms';

		// phpcs:ignore WordPress.Security.NonceVerification -- Nonce verified via `verify_request()` above.
		$site_id = ! empty( $_POST[ $option_group ]['site_id'] ) ? (int) $_POST[ $option_group ]['site_id'] : 0;
		if ( ! $site_id ) {
			add_settings_error( $option_group, 'settings_updated', __( 'No site has been selected to restore.', 'wordpress-seo' ), 'error' );

			$this->terminate_request();
			return;
		}

		$site = get_site( $site_id );
		if ( ! $site ) {
			/* translators: %s expands to the ID of a site within a multisite network. */
			add_settings_error( $option_group, 'settings_updated', sprintf( __( 'Site with ID %d not found.', 'wordpress-seo' ), $site_id ), 'error' );
		}
		else {
			WPSEO_Options::reset_ms_blog( $site_id );

			/* translators: %s expands to the name of a site within a multisite network. */
			add_settings_error( $option_group, 'settings_updated', sprintf( __( '%s restored to default SEO settings.', 'wordpress-seo' ), esc_html( $site->blogname ) ), 'updated' );
		}

		$this->terminate_request();
	}

	/**
	 * Outputs nonce, action and option group fields for a network settings page in the plugin.
	 *
	 * @param string $option_group Option group name for the current page.
	 *
	 * @return void
	 */
	public function settings_fields( $option_group ) {
		?>
		<input type="hidden" name="network_option_group" value="<?php echo esc_attr( $option_group ); ?>" />
		<input type="hidden" name="action" value="<?php echo esc_attr( self::UPDATE_OPTIONS_ACTION ); ?>" />
		<?php
		wp_nonce_field( "$option_group-network-options" );
	}

	/**
	 * Enqueues network admin assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$asset_manager = new WPSEO_Admin_Asset_Manager();
		$asset_manager->enqueue_script( 'network-admin' );

		$translations = [
			/* translators: %s: success message */
			'success_prefix' => __( 'Success: %s', 'wordpress-seo' ),
			/* translators: %s: error message */
			'error_prefix'   => __( 'Error: %s', 'wordpress-seo' ),
		];
		$asset_manager->localize_script(
			'network-admin',
			'wpseoNetworkAdminGlobalL10n',
			$translations
		);
	}

	/**
	 * Hooks in the necessary actions and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {

		if ( ! $this->meets_requirements() ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		add_action( 'admin_action_' . self::UPDATE_OPTIONS_ACTION, [ $this, 'handle_update_options_request' ] );
		add_action( 'admin_action_' . self::RESTORE_SITE_ACTION, [ $this, 'handle_restore_site_request' ] );
	}

	/**
	 * Hooks in the necessary AJAX actions.
	 *
	 * @return void
	 */
	public function register_ajax_hooks() {
		add_action( 'wp_ajax_' . self::UPDATE_OPTIONS_ACTION, [ $this, 'handle_update_options_request' ] );
		add_action( 'wp_ajax_' . self::RESTORE_SITE_ACTION, [ $this, 'handle_restore_site_request' ] );
	}

	/**
	 * Checks whether the requirements to use this class are met.
	 *
	 * @return bool True if requirements are met, false otherwise.
	 */
	public function meets_requirements() {
		return is_multisite() && is_network_admin();
	}

	/**
	 * Verifies that the current request is valid.
	 *
	 * @param string $action    Nonce action.
	 * @param string $query_arg Optional. Nonce query argument. Default '_wpnonce'.
	 *
	 * @return void
	 */
	public function verify_request( $action, $query_arg = '_wpnonce' ) {
		$has_access = current_user_can( 'wpseo_manage_network_options' );

		if ( wp_doing_ajax() ) {
			check_ajax_referer( $action, $query_arg );

			if ( ! $has_access ) {
				wp_die( -1, 403 );
			}
			return;
		}

		check_admin_referer( $action, $query_arg );

		if ( ! $has_access ) {
			wp_die( esc_html__( 'You are not allowed to perform this action.', 'wordpress-seo' ) );
		}
	}

	/**
	 * Terminates the current request by either redirecting back or sending an AJAX response.
	 *
	 * @return void
	 */
	public function terminate_request() {
		if ( wp_doing_ajax() ) {
			$settings_errors = get_settings_errors();

			if ( ! empty( $settings_errors ) && $settings_errors[0]['type'] === 'updated' ) {
				wp_send_json_success( $settings_errors, 200 );
			}

			wp_send_json_error( $settings_errors, 400 );
		}

		$this->persist_settings_errors();
		$this->redirect_back( [ 'settings-updated' => 'true' ] );
	}

	/**
	 * Persists settings errors.
	 *
	 * Settings errors are stored in a transient for 30 seconds so that this transient
	 * can be retrieved on the next page load.
	 *
	 * @return void
	 */
	protected function persist_settings_errors() {
		/*
		 * A regular transient is used here, since it is automatically cleared right after the redirect.
		 * A network transient would be cleaner, but would require a lot of copied code from core for
		 * just a minor adjustment when displaying settings errors.
		 */
		set_transient( 'settings_errors', get_settings_errors(), 30 );
	}

	/**
	 * Redirects back to the referer URL, with optional query arguments.
	 *
	 * @param array $query_args Optional. Query arguments to add to the redirect URL. Default none.
	 *
	 * @return void
	 */
	protected function redirect_back( $query_args = [] ) {
		$sendback = wp_get_referer();

		if ( ! empty( $query_args ) ) {
			$sendback = add_query_arg( $query_args, $sendback );
		}

		wp_safe_redirect( $sendback );
		exit;
	}
}
