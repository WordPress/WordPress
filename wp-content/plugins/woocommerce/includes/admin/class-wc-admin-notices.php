<?php
/**
 * Display notices in admin
 *
 * @package WooCommerce\Admin
 * @version 3.4.0
 */

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Internal\Utilities\Users;
use Automattic\WooCommerce\Internal\Traits\AccessiblePrivateMethods;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Admin_Notices Class.
 */
class WC_Admin_Notices {

	use AccessiblePrivateMethods;

	/**
	 * Stores notices.
	 *
	 * @var array
	 */
	private static $notices = array();

	/**
	 * Array of notices - name => callback.
	 *
	 * @var array
	 */
	private static $core_notices = array(
		'update'                             => 'update_notice',
		'template_files'                     => 'template_file_check_notice',
		'legacy_shipping'                    => 'legacy_shipping_notice',
		'no_shipping_methods'                => 'no_shipping_methods_notice',
		'regenerating_thumbnails'            => 'regenerating_thumbnails_notice',
		'regenerating_lookup_table'          => 'regenerating_lookup_table_notice',
		'no_secure_connection'               => 'secure_connection_notice',
		WC_PHP_MIN_REQUIREMENTS_NOTICE       => 'wp_php_min_requirements_notice',
		'maxmind_license_key'                => 'maxmind_missing_license_key_notice',
		'redirect_download_method'           => 'redirect_download_method_notice',
		'uploads_directory_is_unprotected'   => 'uploads_directory_is_unprotected_notice',
		'base_tables_missing'                => 'base_tables_missing_notice',
		'download_directories_sync_complete' => 'download_directories_sync_complete',
	);

	/**
	 * Constructor.
	 */
	public static function init() {
		self::$notices = get_option( 'woocommerce_admin_notices', array() );

		add_action( 'switch_theme', array( __CLASS__, 'reset_admin_notices' ) );
		add_action( 'woocommerce_installed', array( __CLASS__, 'reset_admin_notices' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'add_redirect_download_method_notice' ) );
		add_action( 'admin_init', array( __CLASS__, 'hide_notices' ), 20 );

		// @TODO: This prevents Action Scheduler async jobs from storing empty list of notices during WC installation.
		// That could lead to OBW not starting and 'Run setup wizard' notice not appearing in WP admin, which we want
		// to avoid.
		if ( ! WC_Install::is_new_install() || ! wc_is_running_from_async_action_scheduler() ) {
			add_action( 'shutdown', array( __CLASS__, 'store_notices' ) );
		}

		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_action( 'admin_print_styles', array( __CLASS__, 'add_notices' ) );
		}
	}

	/**
	 * Parses query to create nonces when available.
	 *
	 * @deprecated 5.4.0
	 * @param object $response The WP_REST_Response we're working with.
	 * @return object $response The prepared WP_REST_Response object.
	 */
	public static function prepare_note_with_nonce( $response ) {
		wc_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '5.4.0' );

		return $response;
	}

	/**
	 * Store notices to DB
	 */
	public static function store_notices() {
		update_option( 'woocommerce_admin_notices', self::get_notices() );
	}

	/**
	 * Get notices
	 *
	 * @return array
	 */
	public static function get_notices() {
		return self::$notices;
	}

	/**
	 * Remove all notices.
	 */
	public static function remove_all_notices() {
		self::$notices = array();
	}

	/**
	 * Reset notices for themes when switched or a new version of WC is installed.
	 */
	public static function reset_admin_notices() {
		if ( ! self::is_ssl() ) {
			self::add_notice( 'no_secure_connection' );
		}
		if ( ! self::is_uploads_directory_protected() ) {
			self::add_notice( 'uploads_directory_is_unprotected' );
		}
		self::add_notice( 'template_files' );
		self::add_min_version_notice();
		self::add_maxmind_missing_license_key_notice();
	}

	/**
	 * Show a notice.
	 *
	 * @param string $name Notice name.
	 * @param bool   $force_save Force saving inside this method instead of at the 'shutdown'.
	 */
	public static function add_notice( $name, $force_save = false ) {
		self::$notices = array_unique( array_merge( self::get_notices(), array( $name ) ) );

		if ( $force_save ) {
			// Adding early save to prevent more race conditions with notices.
			self::store_notices();
		}
	}

	/**
	 * Remove a notice from being displayed.
	 *
	 * @param string $name Notice name.
	 * @param bool   $force_save Force saving inside this method instead of at the 'shutdown'.
	 */
	public static function remove_notice( $name, $force_save = false ) {
		self::$notices = array_diff( self::get_notices(), array( $name ) );
		delete_option( 'woocommerce_admin_notice_' . $name );

		if ( $force_save ) {
			// Adding early save to prevent more race conditions with notices.
			self::store_notices();
		}
	}

	/**
	 * See if a notice is being shown.
	 *
	 * @param string $name Notice name.
	 *
	 * @return boolean
	 */
	public static function has_notice( $name ) {
		return in_array( $name, self::get_notices(), true );
	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public static function hide_notices() {
		if ( isset( $_GET['wc-hide-notice'] ) && isset( $_GET['_wc_notice_nonce'] ) ) { // WPCS: input var ok, CSRF ok.
			if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wc_notice_nonce'] ) ), 'woocommerce_hide_notices_nonce' ) ) { // WPCS: input var ok, CSRF ok.
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'woocommerce' ) );
			}

			$notice_name = sanitize_text_field( wp_unslash( $_GET['wc-hide-notice'] ) ); // WPCS: input var ok, CSRF ok.

			/**
			 * Filter the capability required to dismiss a given notice.
			 *
			 * @since 6.7.0
			 *
			 * @param string $default_capability The default required capability.
			 * @param string $notice_name The notice name.
			 */
			$required_capability = apply_filters( 'woocommerce_dismiss_admin_notice_capability', 'manage_woocommerce', $notice_name );

			if ( ! current_user_can( $required_capability ) ) {
				wp_die( esc_html__( 'You don&#8217;t have permission to do this.', 'woocommerce' ) );
			}

			self::hide_notice( $notice_name );
		}
	}

	/**
	 * Hide a single notice.
	 *
	 * @param string $name Notice name.
	 */
	private static function hide_notice( $name ) {
		self::remove_notice( $name );

		update_user_meta( get_current_user_id(), 'dismissed_' . $name . '_notice', true );

		do_action( 'woocommerce_hide_' . $name . '_notice' );
	}

	/**
	 * Add notices + styles if needed.
	 */
	public static function add_notices() {
		$notices = self::get_notices();

		if ( empty( $notices ) ) {
			return;
		}

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$show_on_screens = array(
			'dashboard',
			'plugins',
		);

		// Notices should only show on WooCommerce screens, the main dashboard, and on the plugins screen.
		if ( ! in_array( $screen_id, wc_get_screen_ids(), true ) && ! in_array( $screen_id, $show_on_screens, true ) ) {
			return;
		}

		wp_enqueue_style( 'woocommerce-activation', plugins_url( '/assets/css/activation.css', WC_PLUGIN_FILE ), array(), Constants::get_constant( 'WC_VERSION' ) );

		// Add RTL support.
		wp_style_add_data( 'woocommerce-activation', 'rtl', 'replace' );

		foreach ( $notices as $notice ) {
			if ( ! empty( self::$core_notices[ $notice ] ) && apply_filters( 'woocommerce_show_admin_notice', true, $notice ) ) {
				add_action( 'admin_notices', array( __CLASS__, self::$core_notices[ $notice ] ) );
			} else {
				add_action( 'admin_notices', array( __CLASS__, 'output_custom_notices' ) );
			}
		}
	}

	/**
	 * Add a custom notice.
	 *
	 * @param string $name        Notice name.
	 * @param string $notice_html Notice HTML.
	 */
	public static function add_custom_notice( $name, $notice_html ) {
		self::add_notice( $name );
		update_option( 'woocommerce_admin_notice_' . $name, wp_kses_post( $notice_html ) );
	}

	/**
	 * Output any stored custom notices.
	 */
	public static function output_custom_notices() {
		$notices = self::get_notices();

		if ( ! empty( $notices ) ) {
			foreach ( $notices as $notice ) {
				if ( empty( self::$core_notices[ $notice ] ) ) {
					$notice_html = get_option( 'woocommerce_admin_notice_' . $notice );

					if ( $notice_html ) {
						include dirname( __FILE__ ) . '/views/html-notice-custom.php';
					}
				}
			}
		}
	}

	/**
	 * If we need to update the database, include a message with the DB update button.
	 */
	public static function update_notice() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		if ( WC()->is_wc_admin_active() && in_array( $screen_id, wc_get_screen_ids(), true ) ) {
			return;
		}

		if ( WC_Install::needs_db_update() ) {
			$next_scheduled_date = WC()->queue()->get_next( 'woocommerce_run_update_callback', null, 'woocommerce-db-updates' );

			if ( $next_scheduled_date || ! empty( $_GET['do_update_woocommerce'] ) ) { // WPCS: input var ok, CSRF ok.
				include dirname( __FILE__ ) . '/views/html-notice-updating.php';
			} else {
				include dirname( __FILE__ ) . '/views/html-notice-update.php';
			}
		} else {
			include dirname( __FILE__ ) . '/views/html-notice-updated.php';
		}
	}

	/**
	 * If we have just installed, show a message with the install pages button.
	 *
	 * @deprecated 4.6.0
	 */
	public static function install_notice() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '4.6.0', esc_html__( 'Onboarding is maintained in WooCommerce Admin.', 'woocommerce' ) );
	}

	/**
	 * Show a notice highlighting bad template files.
	 */
	public static function template_file_check_notice() {
		$core_templates = WC_Admin_Status::scan_template_files( WC()->plugin_path() . '/templates' );
		$outdated       = false;

		foreach ( $core_templates as $file ) {

			$theme_file = false;
			if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . $file;
			} elseif ( file_exists( get_stylesheet_directory() . '/' . WC()->template_path() . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . WC()->template_path() . $file;
			} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
				$theme_file = get_template_directory() . '/' . $file;
			} elseif ( file_exists( get_template_directory() . '/' . WC()->template_path() . $file ) ) {
				$theme_file = get_template_directory() . '/' . WC()->template_path() . $file;
			}

			if ( false !== $theme_file ) {
				$core_version  = WC_Admin_Status::get_file_version( WC()->plugin_path() . '/templates/' . $file );
				$theme_version = WC_Admin_Status::get_file_version( $theme_file );

				if ( $core_version && $theme_version && version_compare( $theme_version, $core_version, '<' ) ) {
					$outdated = true;
					break;
				}
			}
		}

		if ( $outdated ) {
			include dirname( __FILE__ ) . '/views/html-notice-template-check.php';
		} else {
			self::remove_notice( 'template_files' );
		}
	}

	/**
	 * Show a notice asking users to convert to shipping zones.
	 *
	 * @todo remove in 4.0.0
	 */
	public static function legacy_shipping_notice() {
		$maybe_load_legacy_methods = array( 'flat_rate', 'free_shipping', 'international_delivery', 'local_delivery', 'local_pickup' );
		$enabled                   = false;

		foreach ( $maybe_load_legacy_methods as $method ) {
			$options = get_option( 'woocommerce_' . $method . '_settings' );
			if ( $options && isset( $options['enabled'] ) && 'yes' === $options['enabled'] ) {
				$enabled = true;
			}
		}

		if ( $enabled ) {
			include dirname( __FILE__ ) . '/views/html-notice-legacy-shipping.php';
		} else {
			self::remove_notice( 'template_files' );
		}
	}

	/**
	 * No shipping methods.
	 */
	public static function no_shipping_methods_notice() {
		if ( wc_shipping_enabled() && ( empty( $_GET['page'] ) || empty( $_GET['tab'] ) || 'wc-settings' !== $_GET['page'] || 'shipping' !== $_GET['tab'] ) ) { // WPCS: input var ok, CSRF ok.
			$product_count = wp_count_posts( 'product' );
			$method_count  = wc_get_shipping_method_count();

			if ( $product_count->publish > 0 && 0 === $method_count ) {
				include dirname( __FILE__ ) . '/views/html-notice-no-shipping-methods.php';
			}

			if ( $method_count > 0 ) {
				self::remove_notice( 'no_shipping_methods' );
			}
		}
	}

	/**
	 * Notice shown when regenerating thumbnails background process is running.
	 */
	public static function regenerating_thumbnails_notice() {
		include dirname( __FILE__ ) . '/views/html-notice-regenerating-thumbnails.php';
	}

	/**
	 * Notice about secure connection.
	 */
	public static function secure_connection_notice() {
		if ( self::is_ssl() || get_user_meta( get_current_user_id(), 'dismissed_no_secure_connection_notice', true ) ) {
			return;
		}

		include dirname( __FILE__ ) . '/views/html-notice-secure-connection.php';
	}

	/**
	 * Notice shown when regenerating thumbnails background process is running.
	 *
	 * @since 3.6.0
	 */
	public static function regenerating_lookup_table_notice() {
		// See if this is still relevant.
		if ( ! wc_update_product_lookup_tables_is_running() ) {
			self::remove_notice( 'regenerating_lookup_table' );
			return;
		}

		include dirname( __FILE__ ) . '/views/html-notice-regenerating-lookup-table.php';
	}

	/**
	 * Add notice about minimum PHP and WordPress requirement.
	 *
	 * @since 3.6.5
	 */
	public static function add_min_version_notice() {
		if ( version_compare( phpversion(), WC_NOTICE_MIN_PHP_VERSION, '<' ) || version_compare( get_bloginfo( 'version' ), WC_NOTICE_MIN_WP_VERSION, '<' ) ) {
			self::add_notice( WC_PHP_MIN_REQUIREMENTS_NOTICE );
		}
	}

	/**
	 * Notice about WordPress and PHP minimum requirements.
	 *
	 * @since 3.6.5
	 * @return void
	 */
	public static function wp_php_min_requirements_notice() {
		if ( apply_filters( 'woocommerce_hide_php_wp_nag', get_user_meta( get_current_user_id(), 'dismissed_' . WC_PHP_MIN_REQUIREMENTS_NOTICE . '_notice', true ) ) ) {
			self::remove_notice( WC_PHP_MIN_REQUIREMENTS_NOTICE );
			return;
		}

		$old_php = version_compare( phpversion(), WC_NOTICE_MIN_PHP_VERSION, '<' );
		$old_wp  = version_compare( get_bloginfo( 'version' ), WC_NOTICE_MIN_WP_VERSION, '<' );

		// Both PHP and WordPress up to date version => no notice.
		if ( ! $old_php && ! $old_wp ) {
			return;
		}

		if ( $old_php && $old_wp ) {
			$msg = sprintf(
				/* translators: 1: Minimum PHP version 2: Minimum WordPress version */
				__( 'Update required: WooCommerce will soon require PHP version %1$s and WordPress version %2$s or newer.', 'woocommerce' ),
				WC_NOTICE_MIN_PHP_VERSION,
				WC_NOTICE_MIN_WP_VERSION
			);
		} elseif ( $old_php ) {
			$msg = sprintf(
				/* translators: %s: Minimum PHP version */
				__( 'Update required: WooCommerce will soon require PHP version %s or newer.', 'woocommerce' ),
				WC_NOTICE_MIN_PHP_VERSION
			);
		} elseif ( $old_wp ) {
			$msg = sprintf(
				/* translators: %s: Minimum WordPress version */
				__( 'Update required: WooCommerce will soon require WordPress version %s or newer.', 'woocommerce' ),
				WC_NOTICE_MIN_WP_VERSION
			);
		}

		include dirname( __FILE__ ) . '/views/html-notice-wp-php-minimum-requirements.php';
	}

	/**
	 * Add MaxMind missing license key notice.
	 *
	 * @since 3.9.0
	 */
	public static function add_maxmind_missing_license_key_notice() {
		$default_address = get_option( 'woocommerce_default_customer_address' );

		if ( ! in_array( $default_address, array( 'geolocation', 'geolocation_ajax' ), true ) ) {
			return;
		}

		$integration_options = get_option( 'woocommerce_maxmind_geolocation_settings' );
		if ( empty( $integration_options['license_key'] ) ) {
			self::add_notice( 'maxmind_license_key' );

		}
	}

	/**
	 *  Add notice about Redirect-only download method, nudging user to switch to a different method instead.
	 */
	public static function add_redirect_download_method_notice() {
		if ( 'redirect' === get_option( 'woocommerce_file_download_method' ) ) {
			self::add_notice( 'redirect_download_method' );
		} else {
			self::remove_notice( 'redirect_download_method' );
		}
	}

	/**
	 * Notice about the completion of the product downloads sync, with further advice for the site operator.
	 */
	public static function download_directories_sync_complete() {
		$notice_dismissed = apply_filters(
			'woocommerce_hide_download_directories_sync_complete',
			get_user_meta( get_current_user_id(), 'download_directories_sync_complete', true )
		);

		if ( $notice_dismissed ) {
			self::remove_notice( 'download_directories_sync_complete' );
		}

		if ( Users::is_site_administrator() ) {
			include __DIR__ . '/views/html-notice-download-dir-sync-complete.php';
		}
	}

	/**
	 * Display MaxMind missing license key notice.
	 *
	 * @since 3.9.0
	 */
	public static function maxmind_missing_license_key_notice() {
		$user_dismissed_notice   = get_user_meta( get_current_user_id(), 'dismissed_maxmind_license_key_notice', true );
		$filter_dismissed_notice = ! apply_filters( 'woocommerce_maxmind_geolocation_display_notices', true );

		if ( $user_dismissed_notice || $filter_dismissed_notice ) {
			self::remove_notice( 'maxmind_license_key' );
			return;
		}

		include dirname( __FILE__ ) . '/views/html-notice-maxmind-license-key.php';
	}

	/**
	 * Notice about Redirect-Only download method.
	 *
	 * @since 4.0
	 */
	public static function redirect_download_method_notice() {
		if ( apply_filters( 'woocommerce_hide_redirect_method_nag', get_user_meta( get_current_user_id(), 'dismissed_redirect_download_method_notice', true ) ) ) {
			self::remove_notice( 'redirect_download_method' );
			return;
		}

		include dirname( __FILE__ ) . '/views/html-notice-redirect-only-download.php';
	}

	/**
	 * Notice about uploads directory begin unprotected.
	 *
	 * @since 4.2.0
	 */
	public static function uploads_directory_is_unprotected_notice() {
		if ( get_user_meta( get_current_user_id(), 'dismissed_uploads_directory_is_unprotected_notice', true ) || self::is_uploads_directory_protected() ) {
			self::remove_notice( 'uploads_directory_is_unprotected' );
			return;
		}

		include dirname( __FILE__ ) . '/views/html-notice-uploads-directory-is-unprotected.php';
	}

	/**
	 * Notice about base tables missing.
	 */
	public static function base_tables_missing_notice() {
		$notice_dismissed = apply_filters(
			'woocommerce_hide_base_tables_missing_nag',
			get_user_meta( get_current_user_id(), 'dismissed_base_tables_missing_notice', true )
		);
		if ( $notice_dismissed ) {
			self::remove_notice( 'base_tables_missing' );
		}

		include dirname( __FILE__ ) . '/views/html-notice-base-table-missing.php';
	}

	/**
	 * Determine if the store is running SSL.
	 *
	 * @return bool Flag SSL enabled.
	 * @since  3.5.1
	 */
	protected static function is_ssl() {
		$shop_page = wc_get_page_permalink( 'shop' );

		return ( is_ssl() && 'https' === substr( $shop_page, 0, 5 ) );
	}

	/**
	 * Wrapper for is_plugin_active.
	 *
	 * @param string $plugin Plugin to check.
	 * @return boolean
	 */
	protected static function is_plugin_active( $plugin ) {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return is_plugin_active( $plugin );
	}

	/**
	 * Simplify Commerce is no longer in core.
	 *
	 * @deprecated 3.6.0 No longer shown.
	 */
	public static function simplify_commerce_notice() {
		wc_deprecated_function( 'WC_Admin_Notices::simplify_commerce_notice', '3.6.0' );
	}

	/**
	 * Show the Theme Check notice.
	 *
	 * @deprecated 3.3.0 No longer shown.
	 */
	public static function theme_check_notice() {
		wc_deprecated_function( 'WC_Admin_Notices::theme_check_notice', '3.3.0' );
	}

	/**
	 * Check if uploads directory is protected.
	 *
	 * @since 4.2.0
	 * @return bool
	 */
	protected static function is_uploads_directory_protected() {
		$cache_key = '_woocommerce_upload_directory_status';
		$status    = get_transient( $cache_key );

		// Check for cache.
		if ( false !== $status ) {
			return 'protected' === $status;
		}

		// Get only data from the uploads directory.
		$uploads = wp_get_upload_dir();

		// Check for the "uploads/woocommerce_uploads" directory.
		$response         = wp_safe_remote_get(
			esc_url_raw( $uploads['baseurl'] . '/woocommerce_uploads/' ),
			array(
				'redirection' => 0,
			)
		);
		$response_code    = intval( wp_remote_retrieve_response_code( $response ) );
		$response_content = wp_remote_retrieve_body( $response );

		// Check if returns 200 with empty content in case can open an index.html file,
		// and check for non-200 codes in case the directory is protected.
		$is_protected = ( 200 === $response_code && empty( $response_content ) ) || ( 200 !== $response_code );
		set_transient( $cache_key, $is_protected ? 'protected' : 'unprotected', 1 * DAY_IN_SECONDS );

		return $is_protected;
	}
}

WC_Admin_Notices::init();
