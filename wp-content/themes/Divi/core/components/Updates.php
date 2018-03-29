<?php

if ( ! class_exists( 'ET_Core_Updates' ) ):
/**
 * Handles the updates workflow.
 *
 * @private
 *
 * @package ET\Core\Updates
 */
final class ET_Core_Updates {
	protected $core_url;
	protected $options;
	protected $account_status;
	protected $product_version;

	// class version
	protected $version;

	private static $_this;

	function __construct( $core_url, $product_version ) {
		// Don't allow more than one instance of the class
		if ( isset( self::$_this ) ) {
			wp_die( sprintf( esc_html__( '%s: You cannot create a second instance of this class.', 'et-core' ),
				get_class( $this ) )
			);
		}

		self::$_this = $this;

		$this->core_url = $core_url;
		$this->version  = '1.0';

		$this->product_version = $product_version;

		$this->get_options();

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_plugins_updates' ) );
		add_filter( 'site_transient_update_plugins', array( $this, 'add_plugins_to_update_notification' ) );
		add_filter( 'plugins_api', array( $this, 'maybe_modify_plugins_changelog' ), 20, 3 );

		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_themes_updates' ) );
		add_filter( 'site_transient_update_themes', array( $this, 'add_themes_to_update_notification' ) );

		add_filter( 'gettext', array( $this, 'update_notifications' ), 20, 3 );

		add_action( 'et_core_updates_before_request', array( $this, 'maybe_update_account_status' ) );

		add_action( 'admin_notices', array( $this, 'maybe_show_expired_account_notice' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts_styles' ) );

		add_action( 'plugins_loaded', array( $this, 'remove_updater_plugin_actions' ), 30 );

		add_action( 'after_setup_theme', array( $this, 'remove_theme_update_actions' ), 11 );

		add_action( 'admin_init', array( $this, 'remove_plugin_update_actions' ) );
	}

	function remove_theme_update_actions() {
		remove_filter( 'pre_set_site_transient_update_themes', 'et_check_themes_updates' );
		remove_filter( 'site_transient_update_themes', 'et_add_themes_to_update_notification' );
	}

	function remove_plugin_update_actions() {
		remove_filter( 'pre_set_site_transient_update_plugins', 'et_shortcodes_plugin_check_updates' );
		remove_filter( 'site_transient_update_plugins', 'et_shortcodes_plugin_add_to_update_notification' );
	}

	/**
	 * Removes Updater plugin actions and filters,
	 * so it doesn't make additional requests to API
	 *
	 * @return void
	 */
	function remove_updater_plugin_actions() {
		if ( ! class_exists( 'ET_Automatic_Updates' ) ) {
			return;
		}

		$updates_class = ET_Automatic_Updates::get_this();

		remove_filter( 'after_setup_theme', array( $updates_class, 'remove_default_updates' ), 11 );

		remove_filter( 'init', array( $updates_class, 'remove_default_plugins_updates' ), 20 );

		remove_action( 'admin_notices', array( $updates_class, 'maybe_display_expired_message' ) );
	}

	/**
	 * Returns an instance of the object
	 *
	 * @return object
	 */
	static function get_this() {
		return self::$_this;
	}

	/**
	 * Adds automatic updates data only if Username and API key options are set
	 *
	 * @param array $send_to_api Data sent to server
	 * @return array Modified data set if Username and API key are set, original data if not
	 */
	function maybe_add_automatic_updates_data( $send_to_api ) {
		if ( $this->options && isset( $this->options['username'] ) && isset( $this->options['api_key'] ) ) {
			$send_to_api['automatic_updates'] = 'on';

			$send_to_api['username'] = urlencode( sanitize_text_field( $this->options['username'] ) );
			$send_to_api['api_key']  = sanitize_text_field( $this->options['api_key'] );

			$send_to_api = apply_filters( 'et_add_automatic_updates_data', $send_to_api );
		}

		return $send_to_api;
	}

	/**
	 * Gets plugin options
	 *
	 * @return void
	 */
	function get_options() {
		$this->options = get_option( 'et_automatic_updates_options' );

		$this->account_status = get_option( 'et_account_status' );
	}

	function load_scripts_styles( $hook ) {
		if ( 'plugin-install.php' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'et_core_updates', $this->core_url . 'admin/css/updates.css', array(), $this->product_version );
	}

	/**
	 * Check if the account status needs to be updated
	 *
	 * @return void
	 */
	function maybe_update_account_status() {
		$last_checked = get_option( 'et_account_status_last_checked' );

		$timeout = 12 * HOUR_IN_SECONDS;

		$time_changed = $last_checked && ( $timeout <= ( time() - $last_checked ) );

		if ( ! $last_checked || $time_changed ) {
			$this->check_is_active_account();
		}
	}

	/**
	 * Checks if the user's account is active, updates account status.
	 * Doesn't attempt to check the status if the Username isn't set
	 *
	 * @return void
	 */
	function check_is_active_account() {
		global $wp_version;

		if ( ! isset( $this->options['username'] ) || '' == trim( $this->options['username'] ) ) {
			return;
		}

		$send_to_api = array(
			'et_check_account_action' => 'check_active_account',
			'username'                => sanitize_text_field( $this->options['username'] ),
			'class_version'           => $this->version,
		);

		$options = array(
			'timeout'    => 30,
			'body'		 => $send_to_api,
			'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
		);

		$request = wp_remote_post( 'https://www.elegantthemes.com/api/api_downloads.php', $options );

		if ( is_wp_error( $request ) ) {
			$request = wp_remote_post( 'https://cdn.elegantthemes.com/api/api_downloads.php', $options );
		}

		if ( ! is_wp_error( $request ) && wp_remote_retrieve_response_code( $request ) == 200 ){
			$response = wp_remote_retrieve_body( $request );

			if ( ! empty( $response ) ) {
				if ( in_array( $response, array( 'expired', 'active', 'not_found' ) ) ) {
					$this->account_status = $response;

					update_option( 'et_account_status', $this->account_status );
					update_option( 'et_account_status_last_checked', time() );
				}
			}
		}
	}

	function check_plugins_updates( $update_transient ) {
		global $wp_version;

		if ( ! isset( $update_transient->checked ) ) {
			return $update_transient;
		}

		$plugins = $update_transient->checked;

		do_action( 'et_core_updates_before_request' );

		$send_to_api = array(
			'action'            => 'check_all_plugins_updates',
			'installed_plugins' => $plugins,
			'class_version'     => $this->version,
		);

		// Add automatic updates data if Username and API key are set correctly
		$send_to_api = $this->maybe_add_automatic_updates_data( $send_to_api );

		$options = array(
			'timeout'    => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3),
			'body'       => $send_to_api,
			'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
		);

		$last_update = new stdClass();

		$plugins_request = wp_remote_post( 'https://www.elegantthemes.com/api/api.php', $options );

		if ( is_wp_error( $plugins_request ) ) {
			$options['body']['failed_request'] = 'true';
			$plugins_request = wp_remote_post( 'https://cdn.elegantthemes.com/api/api.php', $options );
		}

		if ( ! is_wp_error( $plugins_request ) && wp_remote_retrieve_response_code( $plugins_request ) == 200 ){
			$plugins_response = unserialize( wp_remote_retrieve_body( $plugins_request ) );

			if ( ! empty( $plugins_response ) ) {
				$update_transient->response = array_merge( ! empty( $update_transient->response ) ? $update_transient->response : array(), $plugins_response );

				$last_update->checked  = $plugins;
				$last_update->response = $plugins_response;
			}
		}

		$last_update->last_checked = time();

		set_site_transient( 'et_update_all_plugins', $last_update );

		return $update_transient;
	}

	function add_plugins_to_update_notification( $update_transient ){
		$et_update_lb_plugin = get_site_transient( 'et_update_all_plugins' );

		if ( ! is_object( $et_update_lb_plugin ) || ! isset( $et_update_lb_plugin->response ) ) {
			return $update_transient;
		}

		if ( ! is_object( $update_transient ) ) {
			$update_transient = new stdClass();
		}

		$update_transient->response = array_merge( ! empty( $update_transient->response ) ? $update_transient->response : array(), $et_update_lb_plugin->response );

		return $update_transient;
	}

	public function maybe_modify_plugins_changelog( $false, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $false;
		}

		if ( isset( $args->slug ) ) {
			$et_update_lb_plugin = get_site_transient( 'et_update_all_plugins' );

			$plugin_basename = sprintf( '%1$s/%1$s.php', sanitize_text_field( $args->slug ) );

			if ( isset( $et_update_lb_plugin->response[ $plugin_basename ] ) ) {
				$plugin_info = $et_update_lb_plugin->response[ $plugin_basename ];

				if ( isset( $plugin_info->et_sections_used ) && 'on' === $plugin_info->et_sections_used ) {
					return $plugin_info;
				}
			}
		}

		return $false;
	}

	/**
	 * Sends a request to server, gets current themes versions
	 *
	 * @param object $update_transient Update transient option
	 * @return object Update transient option
	 */
	function check_themes_updates( $update_transient ){
		global $wp_version;

		$et_update_themes = get_site_transient( 'et_update_themes' );

		if ( ! isset( $update_transient->checked ) ) {
			return $update_transient;
		}

		$themes = $update_transient->checked;

		do_action( 'et_core_updates_before_request' );

		$send_to_api = array(
			'action'           => 'check_theme_updates',
			'installed_themes' => $themes,
			'class_version'    => $this->version,
		);

		// Add automatic updates data if Username and API key are set correctly
		$send_to_api = $this->maybe_add_automatic_updates_data( $send_to_api );

		$options = array(
			'timeout'    => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3 ),
			'body'       => $send_to_api,
			'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url()
		);

		$last_update = new stdClass();

		$theme_request = wp_remote_post( 'https://www.elegantthemes.com/api/api.php', $options );

		if ( is_wp_error( $theme_request ) ) {
			$options['body']['failed_request'] = 'true';
			$theme_request = wp_remote_post( 'https://cdn.elegantthemes.com/api/api.php', $options );
		}

		if ( ! is_wp_error( $theme_request ) && wp_remote_retrieve_response_code( $theme_request ) == 200 ){
			$theme_response = unserialize( wp_remote_retrieve_body( $theme_request ) );

			if ( ! empty( $theme_response ) ) {
				foreach ( $theme_response as $et_theme ) {
					if ( array_key_exists( 'et_expired_subscription', $et_theme ) ) {
						// Set the account status to expired if the response array has 'et_expired_subscription' key
						$this->account_status = 'expired';
					} else {
						$this->account_status = 'active';
					}

					update_option( 'et_account_status', $this->account_status );

					break;
				}

				$update_transient->response = array_merge( ! empty( $update_transient->response ) ? $update_transient->response : array(), $theme_response );

				$last_update->checked  = $themes;
				$last_update->response = $theme_response;
			}
		}

		$last_update->last_checked = time();
		set_site_transient( 'et_update_themes', $last_update );

		return $update_transient;
	}

	/**
	 * Adds updated ET themes to default update transient
	 *
	 * @param object $update_transient Update transient option
	 * @return object Update transient option
	 */
	function add_themes_to_update_notification( $update_transient ){
		$et_update_themes = get_site_transient( 'et_update_themes' );

		if ( ! is_object( $et_update_themes ) || ! isset( $et_update_themes->response ) ) {
			return $update_transient;
		}

		// Fix for warning messages on Dashboard / Updates page
		if ( ! is_object( $update_transient ) ) {
			$update_transient = new stdClass();
		}

		$update_transient->response = array_merge( ! empty( $update_transient->response ) ? $update_transient->response : array(), $et_update_themes->response );

		return $update_transient;
	}

	/**
	 * Provides customized messages if an update failed
	 *
	 * @param string $default_translated_text Translated text
	 * @param string $original_text Original text
	 * @param string $domain Localization domain
	 * @return string Error message or Default translated text
	 */
	function update_notifications( $default_translated_text, $original_text, $domain ) {
		$message = '';

		$messages = apply_filters( 'et_core_updates_notifications_messages', array(
			'update_package_unavailable' => array(
				'Update package not available.',
			),
			'theme_updates_unavailable' => array(
				'There is a new version of %1$s available. <a href="%2$s" class="thickbox" aria-label="%3$s">View version %4$s details</a>. <em>Automatic update is unavailable for this theme.</em>',
				'There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s details</a>. <em>Automatic update is unavailable for this theme.</em>',
			),
			'plugin_updates_unavailable' => array(
				'There is a new version of %1$s available. <a href="%2$s" class="thickbox open-plugin-details-modal" aria-label="%3$s">View version %4$s details</a>. <em>Automatic update is unavailable for this plugin.</em>',
				'There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s details</a>. <em>Automatic update is unavailable for this plugin.</em>',
			),
		) );

		$theme_plugin_updates_unavailable = array_merge( $messages['theme_updates_unavailable'], $messages['plugin_updates_unavailable'] );

		if ( is_admin() ) {
			// Use in_array() with $strict=true to avoid adding our messages to wrong places. It may happen if $original_text = 0 for example.
			if ( in_array( $original_text, $messages['update_package_unavailable'], true ) ) {
				$message = et_get_safe_localization( __( '<em>Before you can receive product updates, you must first authenticate your Elegant Themes subscription. To do this, you need to enter both your Elegant Themes Username and your Elegant Themes API Key into the Updates Tab in your theme and plugin settings. To locate your API Key, <a href="https://www.elegantthemes.com/members-area/api/" target="_blank">log in</a> to your Elegant Themes account and navigate to the <strong>Account > API Key</strong> page. <a href="http://www.elegantthemes.com/gallery/divi/documentation/update/" target="_blank">Learn more here</a></em>. If you still get this message, please make sure that your Username and API Key have been entered correctly', 'et-core' ) );
			} else if ( in_array( $original_text, $theme_plugin_updates_unavailable, true ) ) {
				$message = et_get_safe_localization( __( 'Automatic updates currently unavailable. For all Elegant Themes products, please <a href="http://www.elegantthemes.com/gallery/divi/documentation/update/" target="_blank">authenticate your subscription</a> via the Updates tab in your theme & plugin settings to enable product updates. Make sure that your Username and API Key have been entered correctly.', 'et-core' ) );
			}

			if ( '' !== $message ) {
				return $message;
			}
		}

		return $default_translated_text;
	}

	function maybe_show_expired_account_notice() {
		if ( empty( $this->options['username'] ) || empty( $this->options['api_key'] ) ) {
			return;
		}

		if ( 'expired' !== $this->account_status ) {
			return;
		}

		printf(
			'<div class="notice notice-warning">
				<p>%1$s</p>
			</div>',
			et_get_safe_localization( __( 'Your Elegant Themes subscription has expired. You must <a href="https://www.elegantthemes.com/members-area/" target="_blank">renew your account</a> to regain access to product updates and support. To ensure compatibility and security, it is important to always keep your themes and plugins updated.', 'et-core' ) )
		);
	}
}
endif;

if ( ! function_exists( 'et_core_enable_automatic_updates' ) ) :
function et_core_enable_automatic_updates( $url, $version ) {
	if ( ! is_admin() ) {
		return;
	}

	if ( isset( $GLOBALS['et_core_updates'] ) ) {
		return;
	}

	$url = trailingslashit( $url ) . 'core/';

	$GLOBALS['et_core_updates'] = new ET_Core_Updates( $url, $version );

}
endif;
