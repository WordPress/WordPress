<?php
/**
 * Plugin Name: WPCode Lite
 * Plugin URI: https://www.wpcode.com/
 * Version: 2.0.12
 * Requires at least: 4.6
 * Requires PHP: 5.5
 * Tested up to: 6.1
 * Author: WPCode
 * Author URI: https://www.wpcode.com/
 * Description: Easily add code snippets in WordPress. Insert scripts to the header and footer, add PHP code snippets with conditional logic, insert ads pixel, custom content, and more.
 * License: GPLv2 or later
 *
 * Text Domain:         insert-headers-and-footers
 * Domain Path:         /languages
 *
 * @package WPCode
 */

/*
	Copyright 2019 WPBeginner

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't allow multiple versions to be active.
if ( function_exists( 'WPCode' ) ) {

	if ( ! function_exists( 'wpcode_pro_just_activated' ) ) {
		/**
		 * When we activate a Pro version, we need to do additional operations:
		 * 1) deactivate a Lite version;
		 * 2) register option which help to run all activation process for Pro version (custom tables creation, etc.).
		 */
		function wpcode_pro_just_activated() {
			wpcode_deactivate();
			add_option( 'wpcode_install', 1 );
		}
	}
	add_action( 'activate_wpcode-premium/wpcode.php', 'wpcode_pro_just_activated' );

	if ( ! function_exists( 'wpcode_lite_just_activated' ) ) {
		/**
		 * Store temporarily that the Lite version of the plugin was activated.
		 * This is needed because WP does a redirect after activation and
		 * we need to preserve this state to know whether user activated Lite or not.
		 */
		function wpcode_lite_just_activated() {

			set_transient( 'wpcode_lite_just_activated', true );
		}
	}
	add_action( 'activate_insert-headers-and-footers/ihaf.php', 'wpcode_lite_just_activated' );

	if ( ! function_exists( 'wpcode_lite_just_deactivated' ) ) {
		/**
		 * Store temporarily that Lite plugin was deactivated.
		 * Convert temporary "activated" value to a global variable,
		 * so it is available through the request. Remove from the storage.
		 */
		function wpcode_lite_just_deactivated() {

			global $wpcode_lite_just_activated, $wpcode_lite_just_deactivated;

			$wpcode_lite_just_activated   = (bool) get_transient( 'wpcode_lite_just_activated' );
			$wpcode_lite_just_deactivated = true;

			delete_transient( 'wpcode_lite_just_activated' );
		}
	}
	add_action( 'deactivate_insert-headers-and-footers/ihaf.php', 'wpcode_lite_just_deactivated' );

	if ( ! function_exists( 'wpcode_deactivate' ) ) {
		/**
		 * Deactivate Lite if WPCode already activated.
		 */
		function wpcode_deactivate() {

			$plugin = 'insert-headers-and-footers/ihaf.php';

			deactivate_plugins( $plugin );

			do_action( 'wpcode_plugin_deactivated', $plugin );
		}
	}
	add_action( 'admin_init', 'wpcode_deactivate' );

	if ( ! function_exists( 'wpcode_lite_notice' ) ) {
		/**
		 * Display the notice after deactivation when Pro is still active
		 * and user wanted to activate the Lite version of the plugin.
		 */
		function wpcode_lite_notice() {

			global $wpcode_lite_just_activated, $wpcode_lite_just_deactivated;

			if (
				empty( $wpcode_lite_just_activated ) ||
				empty( $wpcode_lite_just_deactivated )
			) {
				return;
			}

			// Currently tried to activate Lite with Pro still active, so display the message.
			printf(
				'<div class="notice notice-warning">
					<p>%1$s</p>
					<p>%2$s</p>
				</div>',
				esc_html__( 'Heads up!', 'insert-headers-and-footers' ),
				esc_html__( 'Your site already has WPCode Pro activated. If you want to switch to WPCode Lite, please first go to Plugins → Installed Plugins and deactivate WPCode. Then, you can activate WPCode Lite.', 'insert-headers-and-footers' )
			);

			if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			unset( $wpcode_lite_just_activated, $wpcode_lite_just_deactivated );
		}
	}
	add_action( 'admin_notices', 'wpcode_lite_notice' );

	// Do not process the plugin code further.
	return;
}

/**
 * Main WPCode Class
 */
class WPCode {

	/**
	 * Holds the instance of the plugin.
	 *
	 * @since 2.0.0
	 *
	 * @var WPCode The one true WPCode
	 */
	private static $instance;

	/**
	 * Plugin version.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $version = '';

	/**
	 * The auto-insert instance.
	 *
	 * @var WPCode_Auto_Insert
	 */
	public $auto_insert;

	/**
	 * The snippet execution instance.
	 *
	 * @var WPCode_Snippet_Execute
	 */
	public $execute;

	/**
	 * The error handling instance.
	 *
	 * @var WPCode_Error
	 */
	public $error;

	/**
	 * The conditional logic instance.
	 *
	 * @var WPCode_Conditional_Logic
	 */
	public $conditional_logic;

	/**
	 * The conditional logic instance.
	 *
	 * @var WPCode_Snippet_Cache
	 */
	public $cache;

	/**
	 * The snippet library.
	 *
	 * @var WPCode_Library
	 */
	public $library;

	/**
	 * The Snippet Generator.
	 *
	 * @var WPCode_Generator
	 */
	public $generator;

	/**
	 * The plugin settings.
	 *
	 * @var WPCode_Settings
	 */
	public $settings;

	/**
	 * The plugin importers.
	 *
	 * @var WPCode_Importers
	 */
	public $importers;
	/**
	 * The file cache class.
	 *
	 * @var WPCode_File_Cache
	 */
	public $file_cache;

	/**
	 * The notifications instance (admin-only).
	 *
	 * @var WPCode_Notifications
	 */
	public $notifications;

	/**
	 * The admin page loader.
	 *
	 * @var WPCode_Admin_Page_Loader
	 */
	public $admin_page_loader;

	/**
	 * The library auth instance.
	 *
	 * @var WPCode_Library_Auth
	 */
	public $library_auth;

	/**
	 * The admin notices instance.
	 *
	 * @var WPCode_Notice
	 */
	public $notice;

	/**
	 * Instance for logging errors.
	 *
	 * @var WPCode_File_Logger
	 */
	public $logger;

	/**
	 * Load the smart tags.
	 *
	 * @var WPCode_Smart_Tags
	 */
	public $smart_tags;

	/**
	 * Main instance of WPCode.
	 *
	 * @return WPCode
	 * @since 2.0.0
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPCode ) ) {
			self::$instance = new WPCode();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->setup_constants();
		$this->includes();
		add_action( 'plugins_loaded', array( $this, 'load_components' ), - 1 );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ), 15 );
	}

	/**
	 * Set up global constants.
	 *
	 * @return void
	 */
	private function setup_constants() {

		define( 'WPCODE_FILE', __FILE__ );

		$plugin_headers = get_file_data( WPCODE_FILE, array( 'version' => 'Version' ) );

		define( 'WPCODE_VERSION', $plugin_headers['version'] );
		define( 'WPCODE_PLUGIN_BASENAME', plugin_basename( WPCODE_FILE ) );
		define( 'WPCODE_PLUGIN_URL', plugin_dir_url( WPCODE_FILE ) );
		define( 'WPCODE_PLUGIN_PATH', plugin_dir_path( WPCODE_FILE ) );

		$this->version = WPCODE_VERSION;
	}

	/**
	 * Require the files needed for the plugin.
	 *
	 * @return void
	 */
	private function includes() {
		// Load the safe mode logic first.
		require_once WPCODE_PLUGIN_PATH . 'includes/safe-mode.php';
		// Plugin helper functions.
		require_once WPCODE_PLUGIN_PATH . 'includes/helpers.php';
		// Functions for global headers & footers output.
		require_once WPCODE_PLUGIN_PATH . 'includes/global-output.php';
		// Use the old class name for backwards compatibility.
		require_once WPCODE_PLUGIN_PATH . 'includes/legacy.php';
		// Add backwards compatibility for older versions of PHP or WP.
		require_once WPCODE_PLUGIN_PATH . 'includes/compat.php';
		// Register code snippets post type.
		require_once WPCODE_PLUGIN_PATH . 'includes/post-type.php';
		// The snippet class.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-snippet.php';
		// Auto-insert options.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-auto-insert.php';
		// Execute snippets.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-snippet-execute.php';
		// Handle PHP errors.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-error.php';
		// [wpcode] shortcode.
		require_once WPCODE_PLUGIN_PATH . 'includes/shortcode.php';
		// Conditional logic.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-conditional-logic.php';
		// Snippet Cache.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-snippet-cache.php';
		// Settings class.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-settings.php';
		// Custom capabilities.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-capabilities.php';
		// Install routines.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-install.php';
		// Logging class.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-file-logger.php';
		// Smart tags class.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-smart-tags.php';

		if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
			require_once WPCODE_PLUGIN_PATH . 'includes/icons.php'; // This is not needed in the frontend atm.
			// Code Editor class.
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-code-editor.php';
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-admin-page-loader.php';
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/admin-scripts.php';
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/admin-ajax-handlers.php';
			// Always used just in the backend.
			require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-generator.php';
			// Snippet Library.
			require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-library.php';
			// Authentication for the library site.
			require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-library-auth.php';
			// Importers.
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-importers.php';
			// File cache.
			require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-file-cache.php';
			// The docs.
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-docs.php';
			// Notifications class.
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-notifications.php';
			// Upgrade page.
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-upgrade-welcome.php';
			// Metabox class.
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-metabox-snippets.php';
			// Metabox class.
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-admin-notice.php';
			// Ask for some love.
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-review.php';
		}

		// Load lite-specific files.
		require_once WPCODE_PLUGIN_PATH . 'includes/lite/loader.php';
	}

	/**
	 * Load components in the main plugin instance.
	 *
	 * @return void
	 */
	public function load_components() {
		$this->auto_insert       = new WPCode_Auto_Insert();
		$this->execute           = new WPCode_Snippet_Execute();
		$this->error             = new WPCode_Error();
		$this->conditional_logic = new WPCode_Conditional_Logic();
		$this->cache             = new WPCode_Snippet_Cache();
		$this->settings          = new WPCode_Settings();
		$this->logger            = new WPCode_File_Logger();

		if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
			$this->file_cache        = new WPCode_File_Cache();
			$this->library           = new WPCode_Library();
			$this->library_auth      = new WPCode_Library_Auth();
			$this->generator         = new WPCode_Generator();
			$this->importers         = new WPCode_Importers();
			$this->notifications     = new WPCode_Notifications();
			$this->admin_page_loader = new WPCode_Admin_Page_Loader_Lite();
			$this->notice            = new WPCode_Notice();
			$this->smart_tags        = new WPCode_Smart_Tags_Lite();

			// Metabox class.
			new WPCode_Metabox_Snippets_Lite();
			// Usage tracking class.
			new WPCode_Usage_Tracking_Lite();
		}

		do_action( 'wpcode_loaded' );
	}

	/**
	 * Load the plugin translations.
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		if ( is_user_logged_in() ) {
			unload_textdomain( 'insert-headers-and-footers' );
		}

		load_plugin_textdomain( 'insert-headers-and-footers', false, dirname( plugin_basename( WPCODE_FILE ) ) . '/languages/' );
	}
}

require_once dirname( __FILE__ ) . '/includes/ihaf.php';

WPCode();
