<?php
/**
 * WooCommerce Admin Helper Compat
 *
 * @package WooCommerce\Admin\Helper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Helper_Compat Class
 *
 * Some level of compatibility with the legacy WooCommerce Helper plugin.
 */
class WC_Helper_Compat {

	/**
	 * Loads the class, runs on init.
	 */
	public static function load() {
		add_action( 'woocommerce_helper_loaded', array( __CLASS__, 'helper_loaded' ) );
	}

	/**
	 * Runs during woocommerce_helper_loaded
	 */
	public static function helper_loaded() {
		// Stop the nagging about WooThemes Updater
		remove_action( 'admin_notices', 'woothemes_updater_notice' );

		// A placeholder dashboard menu for legacy helper users.
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );

		if ( empty( $GLOBALS['woothemes_updater'] ) ) {
			return;
		}

		self::remove_actions();
		self::migrate_connection();
		self::deactivate_plugin();
	}

	/**
	 * Remove legacy helper actions (notices, menus, etc.)
	 */
	public static function remove_actions() {
		// Remove WooThemes Updater notices
		remove_action( 'network_admin_notices', array( $GLOBALS['woothemes_updater']->admin, 'maybe_display_activation_notice' ) );
		remove_action( 'admin_notices', array( $GLOBALS['woothemes_updater']->admin, 'maybe_display_activation_notice' ) );
		remove_action( 'network_admin_menu', array( $GLOBALS['woothemes_updater']->admin, 'register_settings_screen' ) );
		remove_action( 'admin_menu', array( $GLOBALS['woothemes_updater']->admin, 'register_settings_screen' ) );
	}

	/**
	 * Attempt to migrate a legacy connection to a new one.
	 */
	public static function migrate_connection() {
		// Don't attempt to migrate if attempted before.
		if ( WC_Helper_Options::get( 'did-migrate' ) ) {
			return;
		}

		$auth = WC_Helper_Options::get( 'auth' );
		if ( ! empty( $auth ) ) {
			return;
		}

		WC_Helper::log( 'Attempting oauth/migrate' );
		WC_Helper_Options::update( 'did-migrate', true );

		$master_key = get_option( 'woothemes_helper_master_key' );
		if ( empty( $master_key ) ) {
			WC_Helper::log( 'Master key not found, aborting' );
			return;
		}

		$request = WC_Helper_API::post(
			'oauth/migrate',
			array(
				'body' => array(
					'home_url'   => home_url(),
					'master_key' => $master_key,
				),
			)
		);

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200 ) {
			WC_Helper::log( 'Call to oauth/migrate returned a non-200 response code' );
			return;
		}

		$request_token = json_decode( wp_remote_retrieve_body( $request ) );
		if ( empty( $request_token ) ) {
			WC_Helper::log( 'Call to oauth/migrate returned an empty token' );
			return;
		}

		// Obtain an access token.
		$request = WC_Helper_API::post(
			'oauth/access_token',
			array(
				'body' => array(
					'request_token' => $request_token,
					'home_url'      => home_url(),
					'migrate'       => true,
				),
			)
		);

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200 ) {
			WC_Helper::log( 'Call to oauth/access_token returned a non-200 response code' );
			return;
		}

		$access_token = json_decode( wp_remote_retrieve_body( $request ), true );
		if ( empty( $access_token ) ) {
			WC_Helper::log( 'Call to oauth/access_token returned an invalid token' );
			return;
		}

		WC_Helper_Options::update(
			'auth',
			array(
				'access_token'        => $access_token['access_token'],
				'access_token_secret' => $access_token['access_token_secret'],
				'site_id'             => $access_token['site_id'],
				'user_id'             => null, // Set this later
				'updated'             => time(),
			)
		);

		// Obtain the connected user info.
		if ( ! WC_Helper::_flush_authentication_cache() ) {
			WC_Helper::log( 'Could not obtain connected user info in migrate_connection' );
			WC_Helper_Options::update( 'auth', array() );
			return;
		}
	}

	/**
	 * Attempt to deactivate the legacy helper plugin.
	 */
	public static function deactivate_plugin() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			return;
		}

		if ( is_plugin_active( 'woothemes-updater/woothemes-updater.php' ) ) {
			deactivate_plugins( 'woothemes-updater/woothemes-updater.php' );

			// Notify the user when the plugin is deactivated.
			add_action( 'pre_current_active_plugins', array( __CLASS__, 'plugin_deactivation_notice' ) );
		}
	}

	/**
	 * Display admin notice directing the user where to go.
	 */
	public static function plugin_deactivation_notice() {
		?>
		<div id="message" class="error is-dismissible">
			<p><?php printf( __( 'The WooCommerce Helper plugin is no longer needed. <a href="%s">Manage subscriptions</a> from the extensions tab instead.', 'woocommerce' ), esc_url( admin_url( 'admin.php?page=wc-addons&section=helper' ) ) ); ?></p>
		</div>
		<?php
	}

	/**
	 * Register menu item.
	 */
	public static function admin_menu() {
		// No additional menu items for users who did not have a connected helper before.
		$master_key = get_option( 'woothemes_helper_master_key' );
		if ( empty( $master_key ) ) {
			return;
		}

		// Do not show the menu item if user has already seen the new screen.
		$auth = WC_Helper_Options::get( 'auth' );
		if ( ! empty( $auth['user_id'] ) ) {
			return;
		}

		add_dashboard_page( __( 'WooCommerce Helper', 'woocommerce' ), __( 'WooCommerce Helper', 'woocommerce' ), 'manage_options', 'woothemes-helper', array( __CLASS__, 'render_compat_menu' ) );
	}

	/**
	 * Render the legacy helper compat view.
	 */
	public static function render_compat_menu() {
		$helper_url = add_query_arg(
			array(
				'page'    => 'wc-addons',
				'section' => 'helper',
			),
			admin_url( 'admin.php' )
		);
		include WC_Helper::get_view_filename( 'html-helper-compat.php' );
	}
}

WC_Helper_Compat::load();
