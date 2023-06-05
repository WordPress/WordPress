<?php
/**
 * Returns information about the package and handles init.
 */

/**
 * This namespace isn't compatible with the PSR-4
 * which ensures that the copy in the standalone plugin will not be autoloaded.
 */
namespace Automattic\WooCommerce\Admin\Composer;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Notes;
use Automattic\WooCommerce\Admin\Notes\NotesUnavailableException;
use Automattic\WooCommerce\Internal\Admin\FeaturePlugin;

/**
 * Main package class.
 */
class Package {

	/**
	 * Version.
	 *
	 * @var string
	 */
	const VERSION = '3.3.0';

	/**
	 * Package active.
	 *
	 * @var bool
	 */
	private static $package_active = false;

	/**
	 * Active version
	 *
	 * @var bool
	 */
	private static $active_version = null;

	/**
	 * Init the package.
	 *
	 * Only initialize for WP 5.3 or greater.
	 */
	public static function init() {
		// Avoid double initialization when the feature plugin is in use.
		if ( defined( 'WC_ADMIN_VERSION_NUMBER' ) ) {
			self::$active_version = WC_ADMIN_VERSION_NUMBER;
			return;
		}

		$feature_plugin_instance = FeaturePlugin::instance();

		// Indicate to the feature plugin that the core package exists.
		if ( ! defined( 'WC_ADMIN_PACKAGE_EXISTS' ) ) {
			define( 'WC_ADMIN_PACKAGE_EXISTS', true );
		}

		self::$package_active = true;
		self::$active_version = self::VERSION;
		$feature_plugin_instance->init();

		// Unhook the custom Action Scheduler data store class in active older versions of WC Admin.
		remove_filter( 'action_scheduler_store_class', array( $feature_plugin_instance, 'replace_actionscheduler_store_class' ) );
	}

	/**
	 * Return the version of the package.
	 *
	 * @return string
	 */
	public static function get_version() {
		return self::VERSION;
	}

	/**
	 * Return the active version of WC Admin.
	 *
	 * @return string
	 */
	public static function get_active_version() {
		return self::$active_version;
	}

	/**
	 * Return whether the package is active.
	 *
	 * @return bool
	 */
	public static function is_package_active() {
		return self::$package_active;
	}

	/**
	 * Return the path to the package.
	 *
	 * @return string
	 */
	public static function get_path() {
		return dirname( __DIR__ );
	}

	/**
	 * Checks if notes have been initialized.
	 */
	private static function is_notes_initialized() {
		try {
			Notes::load_data_store();
		} catch ( NotesUnavailableException $e ) {
			return false;
		}
		return true;
	}
}
