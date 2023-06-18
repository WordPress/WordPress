<?php
/**
 * WooCommerce setup
 *
 * @package WooCommerce
 * @since   3.2.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Internal\AssignDefaultCategory;
use Automattic\WooCommerce\Internal\BatchProcessing\BatchProcessingController;
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Internal\DownloadPermissionsAdjuster;
use Automattic\WooCommerce\Internal\Features\FeaturesController;
use Automattic\WooCommerce\Internal\ProductAttributesLookup\DataRegenerator;
use Automattic\WooCommerce\Internal\ProductAttributesLookup\LookupDataStore;
use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Register as ProductDownloadDirectories;
use Automattic\WooCommerce\Internal\RestockRefundedItemsAdjuster;
use Automattic\WooCommerce\Internal\Settings\OptionSanitizer;
use Automattic\WooCommerce\Internal\Utilities\WebhookUtil;
use Automattic\WooCommerce\Proxies\LegacyProxy;

/**
 * Main WooCommerce Class.
 *
 * @class WooCommerce
 */
final class WooCommerce {

	/**
	 * WooCommerce version.
	 *
	 * @var string
	 */
	public $version = '7.8.0';

	/**
	 * WooCommerce Schema version.
	 *
	 * @since 4.3 started with version string 430.
	 *
	 * @var string
	 */
	public $db_version = '430';

	/**
	 * The single instance of the class.
	 *
	 * @var WooCommerce
	 * @since 2.1
	 */
	protected static $_instance = null;

	/**
	 * Session instance.
	 *
	 * @var WC_Session|WC_Session_Handler
	 */
	public $session = null;

	/**
	 * Query instance.
	 *
	 * @var WC_Query
	 */
	public $query = null;

	/**
	 * API instance
	 *
	 * @var WC_API
	 */
	public $api;

	/**
	 * Product factory instance.
	 *
	 * @var WC_Product_Factory
	 */
	public $product_factory = null;

	/**
	 * Countries instance.
	 *
	 * @var WC_Countries
	 */
	public $countries = null;

	/**
	 * Integrations instance.
	 *
	 * @var WC_Integrations
	 */
	public $integrations = null;

	/**
	 * Cart instance.
	 *
	 * @var WC_Cart
	 */
	public $cart = null;

	/**
	 * Customer instance.
	 *
	 * @var WC_Customer
	 */
	public $customer = null;

	/**
	 * Order factory instance.
	 *
	 * @var WC_Order_Factory
	 */
	public $order_factory = null;

	/**
	 * Structured data instance.
	 *
	 * @var WC_Structured_Data
	 */
	public $structured_data = null;

	/**
	 * Array of deprecated hook handlers.
	 *
	 * @var array of WC_Deprecated_Hooks
	 */
	public $deprecated_hook_handlers = array();

	/**
	 * Main WooCommerce Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @see WC()
	 * @return WooCommerce - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'woocommerce' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce' ), '2.1' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @param mixed $key Key name.
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( in_array( $key, array( 'payment_gateways', 'shipping', 'mailer', 'checkout' ), true ) ) {
			return $this->$key();
		}
	}

	/**
	 * WooCommerce Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->define_tables();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * When WP has loaded all plugins, trigger the `woocommerce_loaded` hook.
	 *
	 * This ensures `woocommerce_loaded` is called only after all other plugins
	 * are loaded, to avoid issues caused by plugin directory naming changing
	 * the load order. See #21524 for details.
	 *
	 * @since 3.6.0
	 */
	public function on_plugins_loaded() {
		/**
		 * Action to signal that WooCommerce has finished loading.
		 *
		 * @since 3.6.0
		 */
		do_action( 'woocommerce_loaded' );
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 2.3
	 */
	private function init_hooks() {
		register_activation_hook( WC_PLUGIN_FILE, array( 'WC_Install', 'install' ) );
		register_shutdown_function( array( $this, 'log_errors' ) );

		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), -1 );
		add_action( 'admin_notices', array( $this, 'build_dependencies_notice' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( 'WC_Shortcodes', 'init' ) );
		add_action( 'init', array( 'WC_Emails', 'init_transactional_emails' ) );
		add_action( 'init', array( $this, 'add_image_sizes' ) );
		add_action( 'init', array( $this, 'load_rest_api' ) );
		add_action( 'init', array( 'WC_Site_Tracking', 'init' ) );
		add_action( 'switch_blog', array( $this, 'wpdb_table_fix' ), 0 );
		add_action( 'activated_plugin', array( $this, 'activated_plugin' ) );
		add_action( 'deactivated_plugin', array( $this, 'deactivated_plugin' ) );
		add_action( 'woocommerce_installed', array( $this, 'add_woocommerce_inbox_variant' ) );
		add_action( 'woocommerce_updated', array( $this, 'add_woocommerce_inbox_variant' ) );

		// These classes set up hooks on instantiation.
		$container = wc_get_container();
		$container->get( ProductDownloadDirectories::class );
		$container->get( DownloadPermissionsAdjuster::class );
		$container->get( AssignDefaultCategory::class );
		$container->get( DataRegenerator::class );
		$container->get( LookupDataStore::class );
		$container->get( RestockRefundedItemsAdjuster::class );
		$container->get( CustomOrdersTableController::class );
		$container->get( OptionSanitizer::class );
		$container->get( BatchProcessingController::class );
		$container->get( FeaturesController::class );
		$container->get( WebhookUtil::class );
	}

	/**
	 * Add woocommerce_inbox_variant for the Remote Inbox Notification.
	 *
	 * P2 post can be found at https://wp.me/paJDYF-1uJ.
	 */
	public function add_woocommerce_inbox_variant() {
		$config_name = 'woocommerce_inbox_variant_assignment';
		if ( false === get_option( $config_name, false ) ) {
			update_option( $config_name, wp_rand( 1, 12 ) );
		}
	}
	/**
	 * Ensures fatal errors are logged so they can be picked up in the status report.
	 *
	 * @since 3.2.0
	 */
	public function log_errors() {
		$error = error_get_last();
		if ( $error && in_array( $error['type'], array( E_ERROR, E_PARSE, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR ), true ) ) {
			$logger = wc_get_logger();
			$logger->critical(
				/* translators: 1: error message 2: file name and path 3: line number */
				sprintf( __( '%1$s in %2$s on line %3$s', 'woocommerce' ), $error['message'], $error['file'], $error['line'] ) . PHP_EOL,
				array(
					'source' => 'fatal-errors',
				)
			);

			/**
			 * Action triggered when there are errors during shutdown.
			 *
			 * @since 3.2.0
			 */
			do_action( 'woocommerce_shutdown_error', $error );
		}
	}

	/**
	 * Define WC Constants.
	 */
	private function define_constants() {
		$upload_dir = wp_upload_dir( null, false );

		$this->define( 'WC_ABSPATH', dirname( WC_PLUGIN_FILE ) . '/' );
		$this->define( 'WC_PLUGIN_BASENAME', plugin_basename( WC_PLUGIN_FILE ) );
		$this->define( 'WC_VERSION', $this->version );
		$this->define( 'WOOCOMMERCE_VERSION', $this->version );
		$this->define( 'WC_ROUNDING_PRECISION', 6 );
		$this->define( 'WC_DISCOUNT_ROUNDING_MODE', 2 );
		$this->define( 'WC_TAX_ROUNDING_MODE', 'yes' === get_option( 'woocommerce_prices_include_tax', 'no' ) ? 2 : 1 );
		$this->define( 'WC_DELIMITER', '|' );
		$this->define( 'WC_LOG_DIR', $upload_dir['basedir'] . '/wc-logs/' );
		$this->define( 'WC_SESSION_CACHE_GROUP', 'wc_session_id' );
		$this->define( 'WC_TEMPLATE_DEBUG_MODE', false );
		$this->define( 'WC_NOTICE_MIN_PHP_VERSION', '7.2' );
		$this->define( 'WC_NOTICE_MIN_WP_VERSION', '5.2' );
		$this->define( 'WC_PHP_MIN_REQUIREMENTS_NOTICE', 'wp_php_min_requirements_' . WC_NOTICE_MIN_PHP_VERSION . '_' . WC_NOTICE_MIN_WP_VERSION );
		/** Define if we're checking against major, minor or no versions in the following places:
		 *   - plugin screen in WP Admin (displaying extra warning when updating to new major versions)
		 *   - System Status Report ('Installed version not tested with active version of WooCommerce' warning)
		 *   - core update screen in WP Admin (displaying extra warning when updating to new major versions)
		 *   - enable/disable automated updates in the plugin screen in WP Admin (if there are any plugins
		 *      that don't declare compatibility, the auto-update is disabled)
		 *
		 * We dropped SemVer before WC 5.0, so all versions are backwards compatible now, thus no more check needed.
		 * The SSR in the name is preserved for bw compatibility, as this was initially used in System Status Report.
		 */
		$this->define( 'WC_SSR_PLUGIN_UPDATE_RELEASE_VERSION_TYPE', 'none' );

	}

	/**
	 * Register custom tables within $wpdb object.
	 */
	private function define_tables() {
		global $wpdb;

		// List of tables without prefixes.
		$tables = array(
			'payment_tokenmeta'      => 'woocommerce_payment_tokenmeta',
			'order_itemmeta'         => 'woocommerce_order_itemmeta',
			'wc_product_meta_lookup' => 'wc_product_meta_lookup',
			'wc_tax_rate_classes'    => 'wc_tax_rate_classes',
			'wc_reserved_stock'      => 'wc_reserved_stock',
		);

		foreach ( $tables as $name => $table ) {
			$wpdb->$name    = $wpdb->prefix . $table;
			$wpdb->tables[] = $table;
		}
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Returns true if the request is a non-legacy REST API request.
	 *
	 * Legacy REST requests should still run some extra code for backwards compatibility.
	 *
	 * @todo: replace this function once core WP function is available: https://core.trac.wordpress.org/ticket/42061.
	 *
	 * @return bool
	 */
	public function is_rest_api_request() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix         = trailingslashit( rest_get_url_prefix() );
		$is_rest_api_request = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		/**
		 * Whether this is a REST API request.
		 *
		 * @since 3.6.0
		 */
		return apply_filters( 'woocommerce_is_rest_api_request', $is_rest_api_request );
	}

	/**
	 * Load REST API.
	 */
	public function load_rest_api() {
		\Automattic\WooCommerce\RestApi\Server::instance()->init();
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_rest_api_request();
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		/**
		 * Class autoloader.
		 */
		include_once WC_ABSPATH . 'includes/class-wc-autoloader.php';

		/**
		 * Interfaces.
		 */
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-abstract-order-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-coupon-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-customer-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-customer-download-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-customer-download-log-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-object-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-order-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-order-item-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-order-item-product-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-order-item-type-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-order-refund-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-payment-token-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-product-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-product-variable-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-shipping-zone-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-logger-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-log-handler-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-webhooks-data-store-interface.php';
		include_once WC_ABSPATH . 'includes/interfaces/class-wc-queue-interface.php';

		/**
		 * Core traits.
		 */
		include_once WC_ABSPATH . 'includes/traits/trait-wc-item-totals.php';

		/**
		 * Abstract classes.
		 */
		include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-data.php';
		include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-object-query.php';
		include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-payment-token.php';
		include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-product.php';
		include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-order.php';
		include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-settings-api.php';
		include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-shipping-method.php';
		include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-payment-gateway.php';
		include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-integration.php';
		include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-log-handler.php';
		include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-deprecated-hooks.php';
		include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-session.php';
		include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-privacy.php';

		/**
		 * Core classes.
		 */
		include_once WC_ABSPATH . 'includes/wc-core-functions.php';
		include_once WC_ABSPATH . 'includes/class-wc-datetime.php';
		include_once WC_ABSPATH . 'includes/class-wc-post-types.php';
		include_once WC_ABSPATH . 'includes/class-wc-install.php';
		include_once WC_ABSPATH . 'includes/class-wc-geolocation.php';
		include_once WC_ABSPATH . 'includes/class-wc-download-handler.php';
		include_once WC_ABSPATH . 'includes/class-wc-comments.php';
		include_once WC_ABSPATH . 'includes/class-wc-post-data.php';
		include_once WC_ABSPATH . 'includes/class-wc-ajax.php';
		include_once WC_ABSPATH . 'includes/class-wc-emails.php';
		include_once WC_ABSPATH . 'includes/class-wc-data-exception.php';
		include_once WC_ABSPATH . 'includes/class-wc-query.php';
		include_once WC_ABSPATH . 'includes/class-wc-meta-data.php';
		include_once WC_ABSPATH . 'includes/class-wc-order-factory.php';
		include_once WC_ABSPATH . 'includes/class-wc-order-query.php';
		include_once WC_ABSPATH . 'includes/class-wc-product-factory.php';
		include_once WC_ABSPATH . 'includes/class-wc-product-query.php';
		include_once WC_ABSPATH . 'includes/class-wc-payment-tokens.php';
		include_once WC_ABSPATH . 'includes/class-wc-shipping-zone.php';
		include_once WC_ABSPATH . 'includes/gateways/class-wc-payment-gateway-cc.php';
		include_once WC_ABSPATH . 'includes/gateways/class-wc-payment-gateway-echeck.php';
		include_once WC_ABSPATH . 'includes/class-wc-countries.php';
		include_once WC_ABSPATH . 'includes/class-wc-integrations.php';
		include_once WC_ABSPATH . 'includes/class-wc-cache-helper.php';
		include_once WC_ABSPATH . 'includes/class-wc-https.php';
		include_once WC_ABSPATH . 'includes/class-wc-deprecated-action-hooks.php';
		include_once WC_ABSPATH . 'includes/class-wc-deprecated-filter-hooks.php';
		include_once WC_ABSPATH . 'includes/class-wc-background-emailer.php';
		include_once WC_ABSPATH . 'includes/class-wc-discounts.php';
		include_once WC_ABSPATH . 'includes/class-wc-cart-totals.php';
		include_once WC_ABSPATH . 'includes/customizer/class-wc-shop-customizer.php';
		include_once WC_ABSPATH . 'includes/class-wc-regenerate-images.php';
		include_once WC_ABSPATH . 'includes/class-wc-privacy.php';
		include_once WC_ABSPATH . 'includes/class-wc-structured-data.php';
		include_once WC_ABSPATH . 'includes/class-wc-shortcodes.php';
		include_once WC_ABSPATH . 'includes/class-wc-logger.php';
		include_once WC_ABSPATH . 'includes/queue/class-wc-action-queue.php';
		include_once WC_ABSPATH . 'includes/queue/class-wc-queue.php';
		include_once WC_ABSPATH . 'includes/admin/marketplace-suggestions/class-wc-marketplace-updater.php';
		include_once WC_ABSPATH . 'includes/blocks/class-wc-blocks-utils.php';

		/**
		 * Data stores - used to store and retrieve CRUD object data from the database.
		 */
		include_once WC_ABSPATH . 'includes/class-wc-data-store.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-data-store-wp.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-coupon-data-store-cpt.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-product-data-store-cpt.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-product-grouped-data-store-cpt.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-product-variable-data-store-cpt.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-product-variation-data-store-cpt.php';
		include_once WC_ABSPATH . 'includes/data-stores/abstract-wc-order-item-type-data-store.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-order-item-data-store.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-order-item-coupon-data-store.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-order-item-fee-data-store.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-order-item-product-data-store.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-order-item-shipping-data-store.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-order-item-tax-data-store.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-payment-token-data-store.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-customer-data-store.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-customer-data-store-session.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-customer-download-data-store.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-customer-download-log-data-store.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-shipping-zone-data-store.php';
		include_once WC_ABSPATH . 'includes/data-stores/abstract-wc-order-data-store-cpt.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-order-data-store-cpt.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-order-refund-data-store-cpt.php';
		include_once WC_ABSPATH . 'includes/data-stores/class-wc-webhook-data-store.php';

		/**
		 * REST API.
		 */
		include_once WC_ABSPATH . 'includes/legacy/class-wc-legacy-api.php';
		include_once WC_ABSPATH . 'includes/class-wc-api.php';
		include_once WC_ABSPATH . 'includes/class-wc-rest-authentication.php';
		include_once WC_ABSPATH . 'includes/class-wc-rest-exception.php';
		include_once WC_ABSPATH . 'includes/class-wc-auth.php';
		include_once WC_ABSPATH . 'includes/class-wc-register-wp-admin-settings.php';

		/**
		 * Tracks.
		 */
		include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks.php';
		include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks-event.php';
		include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks-client.php';
		include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks-footer-pixel.php';
		include_once WC_ABSPATH . 'includes/tracks/class-wc-site-tracking.php';

		/**
		 * WCCOM Site.
		 */
		include_once WC_ABSPATH . 'includes/wccom-site/class-wc-wccom-site.php';

		/**
		 * Libraries and packages.
		 */
		include_once WC_ABSPATH . 'packages/action-scheduler/action-scheduler.php';

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			include_once WC_ABSPATH . 'includes/class-wc-cli.php';
		}

		if ( $this->is_request( 'admin' ) ) {
			include_once WC_ABSPATH . 'includes/admin/class-wc-admin.php';
		}

		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}

		if ( $this->is_request( 'cron' ) && 'yes' === get_option( 'woocommerce_allow_tracking', 'no' ) ) {
			include_once WC_ABSPATH . 'includes/class-wc-tracker.php';
		}

		$this->theme_support_includes();
		$this->query = new WC_Query();
		$this->api   = new WC_API();
		$this->api->init();
	}

	/**
	 * Include classes for theme support.
	 *
	 * @since 3.3.0
	 */
	private function theme_support_includes() {
		if ( wc_is_wp_default_theme_active() ) {
			switch ( get_template() ) {
				case 'twentyten':
					include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-ten.php';
					break;
				case 'twentyeleven':
					include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-eleven.php';
					break;
				case 'twentytwelve':
					include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-twelve.php';
					break;
				case 'twentythirteen':
					include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-thirteen.php';
					break;
				case 'twentyfourteen':
					include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-fourteen.php';
					break;
				case 'twentyfifteen':
					include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-fifteen.php';
					break;
				case 'twentysixteen':
					include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-sixteen.php';
					break;
				case 'twentyseventeen':
					include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-seventeen.php';
					break;
				case 'twentynineteen':
					include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-nineteen.php';
					break;
				case 'twentytwenty':
					include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-twenty.php';
					break;
				case 'twentytwentyone':
					include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-twenty-one.php';
					break;
				case 'twentytwentytwo':
					include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-twenty-two.php';
					break;
				case 'twentytwentythree':
					include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-twenty-three.php';
					break;
			}
		}
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
		include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
		include_once WC_ABSPATH . 'includes/wc-template-hooks.php';
		include_once WC_ABSPATH . 'includes/class-wc-template-loader.php';
		include_once WC_ABSPATH . 'includes/class-wc-frontend-scripts.php';
		include_once WC_ABSPATH . 'includes/class-wc-form-handler.php';
		include_once WC_ABSPATH . 'includes/class-wc-cart.php';
		include_once WC_ABSPATH . 'includes/class-wc-tax.php';
		include_once WC_ABSPATH . 'includes/class-wc-shipping-zones.php';
		include_once WC_ABSPATH . 'includes/class-wc-customer.php';
		include_once WC_ABSPATH . 'includes/class-wc-embed.php';
		include_once WC_ABSPATH . 'includes/class-wc-session-handler.php';
	}

	/**
	 * Function used to Init WooCommerce Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
		include_once WC_ABSPATH . 'includes/wc-template-functions.php';
	}

	/**
	 * Init WooCommerce when WordPress Initialises.
	 */
	public function init() {
		/**
		 * Action triggered before WooCommerce initialization begins.
		 */
		do_action( 'before_woocommerce_init' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment

		// Set up localisation.
		$this->load_plugin_textdomain();

		// Load class instances.
		$this->product_factory                     = new WC_Product_Factory();
		$this->order_factory                       = new WC_Order_Factory();
		$this->countries                           = new WC_Countries();
		$this->integrations                        = new WC_Integrations();
		$this->structured_data                     = new WC_Structured_Data();
		$this->deprecated_hook_handlers['actions'] = new WC_Deprecated_Action_Hooks();
		$this->deprecated_hook_handlers['filters'] = new WC_Deprecated_Filter_Hooks();

		// Classes/actions loaded for the frontend and for ajax requests.
		if ( $this->is_request( 'frontend' ) ) {
			wc_load_cart();
		}

		$this->load_webhooks();

		/**
		 * Action triggered after WooCommerce initialization finishes.
		 */
		do_action( 'woocommerce_init' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/woocommerce/woocommerce-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/woocommerce-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = determine_locale();

		/**
		 * Filter to adjust the WooCommerce locale to use for translations.
		 */
		$locale = apply_filters( 'plugin_locale', $locale, 'woocommerce' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment

		unload_textdomain( 'woocommerce' );
		load_textdomain( 'woocommerce', WP_LANG_DIR . '/woocommerce/woocommerce-' . $locale . '.mo' );
		load_plugin_textdomain( 'woocommerce', false, plugin_basename( dirname( WC_PLUGIN_FILE ) ) . '/i18n/languages' );
	}

	/**
	 * Ensure theme and server variable compatibility and setup image sizes.
	 */
	public function setup_environment() {
		/**
		 * WC_TEMPLATE_PATH constant.
		 *
		 * @deprecated 2.2 Use WC()->template_path() instead.
		 */
		$this->define( 'WC_TEMPLATE_PATH', $this->template_path() );

		$this->add_thumbnail_support();
	}

	/**
	 * Ensure post thumbnail support is turned on.
	 */
	private function add_thumbnail_support() {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
		}
		add_post_type_support( 'product', 'thumbnail' );
	}

	/**
	 * Add WC Image sizes to WP.
	 *
	 * As of 3.3, image sizes can be registered via themes using add_theme_support for woocommerce
	 * and defining an array of args. If these are not defined, we will use defaults. This is
	 * handled in wc_get_image_size function.
	 *
	 * 3.3 sizes:
	 *
	 * woocommerce_thumbnail - Used in product listings. We assume these work for a 3 column grid layout.
	 * woocommerce_single - Used on single product pages for the main image.
	 *
	 * @since 2.3
	 */
	public function add_image_sizes() {
		$thumbnail         = wc_get_image_size( 'thumbnail' );
		$single            = wc_get_image_size( 'single' );
		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );

		add_image_size( 'woocommerce_thumbnail', $thumbnail['width'], $thumbnail['height'], $thumbnail['crop'] );
		add_image_size( 'woocommerce_single', $single['width'], $single['height'], $single['crop'] );
		add_image_size( 'woocommerce_gallery_thumbnail', $gallery_thumbnail['width'], $gallery_thumbnail['height'], $gallery_thumbnail['crop'] );
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', WC_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( WC_PLUGIN_FILE ) );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		/**
		 * Filter to adjust the base templates path.
		 */
		return apply_filters( 'woocommerce_template_path', 'woocommerce/' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment
	}

	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * Return the WC API URL for a given request.
	 *
	 * @param string    $request Requested endpoint.
	 * @param bool|null $ssl     If should use SSL, null if should auto detect. Default: null.
	 * @return string
	 */
	public function api_request_url( $request, $ssl = null ) {
		if ( is_null( $ssl ) ) {
			$scheme = wp_parse_url( home_url(), PHP_URL_SCHEME );
		} elseif ( $ssl ) {
			$scheme = 'https';
		} else {
			$scheme = 'http';
		}

		if ( strstr( get_option( 'permalink_structure' ), '/index.php/' ) ) {
			$api_request_url = trailingslashit( home_url( '/index.php/wc-api/' . $request, $scheme ) );
		} elseif ( get_option( 'permalink_structure' ) ) {
			$api_request_url = trailingslashit( home_url( '/wc-api/' . $request, $scheme ) );
		} else {
			$api_request_url = add_query_arg( 'wc-api', $request, trailingslashit( home_url( '', $scheme ) ) );
		}

		/**
		 * Filter to adjust the url of an incoming API request.
		 */
		return esc_url_raw( apply_filters( 'woocommerce_api_request_url', $api_request_url, $request, $ssl ) );  // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment
	}

	/**
	 * Load & enqueue active webhooks.
	 *
	 * @since 2.2
	 */
	private function load_webhooks() {

		if ( ! is_blog_installed() ) {
			return;
		}

		/**
		 * Hook: woocommerce_load_webhooks_limit.
		 *
		 * @since 3.6.0
		 * @param int $limit Used to limit how many webhooks are loaded. Default: no limit.
		 */
		$limit = apply_filters( 'woocommerce_load_webhooks_limit', null );

		wc_load_webhooks( 'active', $limit );
	}

	/**
	 * Initialize the customer and cart objects and setup customer saving on shutdown.
	 *
	 * @since 3.6.4
	 * @return void
	 */
	public function initialize_cart() {
		// Cart needs customer info.
		if ( is_null( $this->customer ) || ! $this->customer instanceof WC_Customer ) {
			$this->customer = new WC_Customer( get_current_user_id(), true );
			// Customer should be saved during shutdown.
			add_action( 'shutdown', array( $this->customer, 'save' ), 10 );
		}
		if ( is_null( $this->cart ) || ! $this->cart instanceof WC_Cart ) {
			$this->cart = new WC_Cart();
		}
	}

	/**
	 * Initialize the session class.
	 *
	 * @since 3.6.4
	 * @return void
	 */
	public function initialize_session() {
		/**
		 * Filter to overwrite the session class that handles session data for users.
		 */
		$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment
		if ( is_null( $this->session ) || ! $this->session instanceof $session_class ) {
			$this->session = new $session_class();
			$this->session->init();
		}
	}

	/**
	 * Set tablenames inside WPDB object.
	 */
	public function wpdb_table_fix() {
		$this->define_tables();
	}

	/**
	 * Ran when any plugin is activated.
	 *
	 * @since 3.6.0
	 * @param string $filename The filename of the activated plugin.
	 */
	public function activated_plugin( $filename ) {
		include_once dirname( __FILE__ ) . '/admin/helper/class-wc-helper.php';

		if ( '/woocommerce.php' === substr( $filename, -16 ) ) {
			set_transient( 'woocommerce_activated_plugin', $filename );
		}

		WC_Helper::activated_plugin( $filename );
	}

	/**
	 * Ran when any plugin is deactivated.
	 *
	 * @since 3.6.0
	 * @param string $filename The filename of the deactivated plugin.
	 */
	public function deactivated_plugin( $filename ) {
		include_once dirname( __FILE__ ) . '/admin/helper/class-wc-helper.php';

		WC_Helper::deactivated_plugin( $filename );
	}

	/**
	 * Get queue instance.
	 *
	 * @return WC_Queue_Interface
	 */
	public function queue() {
		return WC_Queue::instance();
	}

	/**
	 * Get Checkout Class.
	 *
	 * @return WC_Checkout
	 */
	public function checkout() {
		return WC_Checkout::instance();
	}

	/**
	 * Get gateways class.
	 *
	 * @return WC_Payment_Gateways
	 */
	public function payment_gateways() {
		return WC_Payment_Gateways::instance();
	}

	/**
	 * Get shipping class.
	 *
	 * @return WC_Shipping
	 */
	public function shipping() {
		return WC_Shipping::instance();
	}

	/**
	 * Email Class.
	 *
	 * @return WC_Emails
	 */
	public function mailer() {
		return WC_Emails::instance();
	}

	/**
	 * Check if plugin assets are built and minified
	 *
	 * @return bool
	 */
	public function build_dependencies_satisfied() {
		// Check if we have compiled CSS.
		if ( ! file_exists( WC()->plugin_path() . '/assets/css/admin.css' ) ) {
			return false;
		}

		// Check if we have minified JS.
		if ( ! file_exists( WC()->plugin_path() . '/assets/js/admin/woocommerce_admin.min.js' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Output a admin notice when build dependencies not met.
	 *
	 * @return void
	 */
	public function build_dependencies_notice() {
		if ( $this->build_dependencies_satisfied() ) {
			return;
		}

		$message_one = __( 'You have installed a development version of WooCommerce which requires files to be built and minified. From the plugin directory, run <code>pnpm install</code> and then <code>pnpm run build --filter=woocommerce</code> to build and minify assets.', 'woocommerce' );
		$message_two = sprintf(
			/* translators: 1: URL of WordPress.org Repository 2: URL of the GitHub Repository release page */
			__( 'Or you can download a pre-built version of the plugin from the <a href="%1$s">WordPress.org repository</a> or by visiting <a href="%2$s">the releases page in the GitHub repository</a>.', 'woocommerce' ),
			'https://wordpress.org/plugins/woocommerce/',
			'https://github.com/woocommerce/woocommerce/releases'
		);
		printf( '<div class="error"><p>%s %s</p></div>', $message_one, $message_two ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Is the WooCommerce Admin actively included in the WooCommerce core?
	 * Based on presence of a basic WC Admin function.
	 *
	 * @return boolean
	 */
	public function is_wc_admin_active() {
		return function_exists( 'wc_admin_url' );
	}

	/**
	 * Call a user function. This should be used to execute any non-idempotent function, especially
	 * those in the `includes` directory or provided by WordPress.
	 *
	 * This method can be useful for unit tests, since functions called using this method
	 * can be easily mocked by using WC_Unit_Test_Case::register_legacy_proxy_function_mocks.
	 *
	 * @param string $function_name The function to execute.
	 * @param mixed  ...$parameters The parameters to pass to the function.
	 *
	 * @return mixed The result from the function.
	 *
	 * @since 4.4
	 */
	public function call_function( $function_name, ...$parameters ) {
		return wc_get_container()->get( LegacyProxy::class )->call_function( $function_name, ...$parameters );
	}

	/**
	 * Call a static method in a class. This should be used to execute any non-idempotent method in classes
	 * from the `includes` directory.
	 *
	 * This method can be useful for unit tests, since methods called using this method
	 * can be easily mocked by using WC_Unit_Test_Case::register_legacy_proxy_static_mocks.
	 *
	 * @param string $class_name The name of the class containing the method.
	 * @param string $method_name The name of the method.
	 * @param mixed  ...$parameters The parameters to pass to the method.
	 *
	 * @return mixed The result from the method.
	 *
	 * @since 4.4
	 */
	public function call_static( $class_name, $method_name, ...$parameters ) {
		return wc_get_container()->get( LegacyProxy::class )->call_static( $class_name, $method_name, ...$parameters );
	}

	/**
	 * Gets an instance of a given legacy class.
	 * This must not be used to get instances of classes in the `src` directory.
	 *
	 * This method can be useful for unit tests, since objects obtained using this method
	 * can be easily mocked by using WC_Unit_Test_Case::register_legacy_proxy_class_mocks.
	 *
	 * @param string $class_name The name of the class to get an instance for.
	 * @param mixed  ...$args Parameters to be passed to the class constructor or to the appropriate internal 'get_instance_of_' method.
	 *
	 * @return object The instance of the class.
	 * @throws \Exception The requested class belongs to the `src` directory, or there was an error creating an instance of the class.
	 *
	 * @since 4.4
	 */
	public function get_instance_of( string $class_name, ...$args ) {
		return wc_get_container()->get( LegacyProxy::class )->get_instance_of( $class_name, ...$args );
	}

	/**
	 * Gets the value of a global.
	 *
	 * @param string $global_name The name of the global to get the value for.
	 * @return mixed The value of the global.
	 */
	public function get_global( string $global_name ) {
		return wc_get_container()->get( LegacyProxy::class )->get_global( $global_name );
	}
}
