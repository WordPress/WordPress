<?php
/**
 * WooCommerce Admin: Feature plugin main class.
 */

namespace Automattic\WooCommerce\Internal\Admin;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API;
use Automattic\WooCommerce\Admin\Notes\Notes;
use Automattic\WooCommerce\Internal\Admin\Notes\OrderMilestones;
use Automattic\WooCommerce\Internal\Admin\Notes\WooSubscriptionsNotes;
use Automattic\WooCommerce\Internal\Admin\Notes\TrackingOptIn;
use Automattic\WooCommerce\Internal\Admin\Notes\WooCommercePayments;
use Automattic\WooCommerce\Internal\Admin\Notes\InstallJPAndWCSPlugins;
use Automattic\WooCommerce\Internal\Admin\Notes\TestCheckout;
use Automattic\WooCommerce\Internal\Admin\Notes\SellingOnlineCourses;
use Automattic\WooCommerce\Internal\Admin\Notes\MerchantEmailNotifications;
use Automattic\WooCommerce\Internal\Admin\Notes\MagentoMigration;
use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\PluginsHelper;
use Automattic\WooCommerce\Admin\PluginsInstaller;
use Automattic\WooCommerce\Admin\ReportExporter;
use Automattic\WooCommerce\Admin\ReportsSync;
use Automattic\WooCommerce\Internal\Admin\CategoryLookup;
use Automattic\WooCommerce\Internal\Admin\Events;
use Automattic\WooCommerce\Internal\Admin\Onboarding\Onboarding;

/**
 * Feature plugin main class.
 *
 * @internal This file will not be bundled with woo core, only the feature plugin.
 * @internal Note this is not called WC_Admin due to a class already existing in core with that name.
 */
class FeaturePlugin {
	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function __construct() {}

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Init the feature plugin, only if we can detect both Gutenberg and WooCommerce.
	 */
	public function init() {
		// Bail if WC isn't initialized (This can be called from WCAdmin's entrypoint).
		if ( ! defined( 'WC_ABSPATH' ) ) {
			return;
		}

		// Load the page controller functions file first to prevent fatal errors when disabling WooCommerce Admin.
		$this->define_constants();
		require_once WC_ADMIN_ABSPATH . '/includes/react-admin/page-controller-functions.php';
		require_once WC_ADMIN_ABSPATH . '/src/Admin/Notes/DeprecatedNotes.php';
		require_once WC_ADMIN_ABSPATH . '/includes/react-admin/core-functions.php';
		require_once WC_ADMIN_ABSPATH . '/includes/react-admin/feature-config.php';
		require_once WC_ADMIN_ABSPATH . '/includes/react-admin/wc-admin-update-functions.php';
		require_once WC_ADMIN_ABSPATH . '/includes/react-admin/class-experimental-abtest.php';

		if ( did_action( 'plugins_loaded' ) ) {
			self::on_plugins_loaded();
		} else {
			// Make sure we hook into `plugins_loaded` before core's Automattic\WooCommerce\Package::init().
			// If core is network activated but we aren't, the packaged version of WooCommerce Admin will
			// attempt to use a data store that hasn't been loaded yet - because we've defined our constants here.
			// See: https://github.com/woocommerce/woocommerce-admin/issues/3869.
			add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), 9 );
		}
	}

	/**
	 * Setup plugin once all other plugins are loaded.
	 *
	 * @return void
	 */
	public function on_plugins_loaded() {
		$this->hooks();
		$this->includes();
	}

	/**
	 * Define Constants.
	 */
	protected function define_constants() {
		$this->define( 'WC_ADMIN_APP', 'wc-admin-app' );
		$this->define( 'WC_ADMIN_ABSPATH', WC_ABSPATH );
		$this->define( 'WC_ADMIN_DIST_JS_FOLDER', 'assets/client/admin/' );
		$this->define( 'WC_ADMIN_DIST_CSS_FOLDER', 'assets/client/admin/' );
		$this->define( 'WC_ADMIN_PLUGIN_FILE', WC_PLUGIN_FILE );

		/**
		 * Define the WC Admin Images Folder URL.
		 *
		 * @deprecated 6.7.0
		 * @var string
		 */
		if ( ! defined( 'WC_ADMIN_IMAGES_FOLDER_URL' ) ) {
			/**
			 * Define the WC Admin Images Folder URL.
			 *
			 * @deprecated 6.7.0
			 * @var string
			 */
			define( 'WC_ADMIN_IMAGES_FOLDER_URL', plugins_url( 'assets/images', WC_PLUGIN_FILE ) );
		}

		/**
		 * Define the current WC Admin version.
		 *
		 * @deprecated 6.4.0
		 * @var string
		 */
		if ( ! defined( 'WC_ADMIN_VERSION_NUMBER' ) ) {
			/**
			  * Define the current WC Admin version.
			  *
			  * @deprecated 6.4.0
			  * @var string
			  */
			define( 'WC_ADMIN_VERSION_NUMBER', '3.3.0' );
		}
	}

	/**
	 * Include WC Admin classes.
	 */
	public function includes() {
		// Initialize Database updates, option migrations, and Notes.
		Events::instance()->init();
		Notes::init();

		// Initialize Plugins Installer.
		PluginsInstaller::init();
		PluginsHelper::init();

		// Initialize API.
		API\Init::instance();

		if ( Features::is_enabled( 'onboarding' ) ) {
			Onboarding::init();
		}

		if ( Features::is_enabled( 'analytics' ) ) {
			// Initialize Reports syncing.
			ReportsSync::init();
			CategoryLookup::instance()->init();

			// Initialize Reports exporter.
			ReportExporter::init();
		}

		// Admin note providers.
		// @todo These should be bundled in the features/ folder, but loading them from there currently has a load order issue.
		new WooSubscriptionsNotes();
		new OrderMilestones();
		new TrackingOptIn();
		new WooCommercePayments();
		new InstallJPAndWCSPlugins();
		new TestCheckout();
		new SellingOnlineCourses();
		new MagentoMigration();

		// Initialize MerchantEmailNotifications.
		MerchantEmailNotifications::init();
	}

	/**
	 * Set up our admin hooks and plugin loader.
	 */
	protected function hooks() {
		add_filter( 'woocommerce_admin_features', array( $this, 'replace_supported_features' ), 0 );

		Loader::get_instance();
		WCAdminAssets::get_instance();
	}


	/**
	 * Overwrites the allowed features array using a local `feature-config.php` file.
	 *
	 * @param array $features Array of feature slugs.
	 */
	public function replace_supported_features( $features ) {
		/**
		 * Get additional feature config
		 *
		 * @since 6.5.0
		 */
		$feature_config = apply_filters( 'woocommerce_admin_get_feature_config', wc_admin_get_feature_config() );
		$features       = array_keys( array_filter( $feature_config ) );
		return $features;
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	protected function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		die();
	}
}
