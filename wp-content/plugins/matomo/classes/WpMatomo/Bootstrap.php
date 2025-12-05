<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo;

use Piwik\Application\Environment;
use Piwik\Cache;
use Piwik\Container\StaticContainer;
use Piwik\FrontController;
use Piwik\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}
/**
 * piwik constants
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
 */
class Bootstrap {
	/**
	 * Tests only
	 *
	 * @var bool|null
	 */
	private static $assume_not_bootstrapped;

	private static $bootstrapped_by_wordpress = false;

	private static $are_incompatible_plugins_filtered = false;

	private static $extra_di_definitions = [];

	private static $environment_bootstrapped = false;

	public static function set_extra_di_definitions( array $definitions ) {
		if ( ! defined( 'PIWIK_TEST_MODE' ) ) {
			throw new \Exception( 'set_extra_di_definitions is only for tests' );
		}

		self::$extra_di_definitions = $definitions;
	}

	public static function get_extra_di_definitions() {
		return self::$extra_di_definitions;
	}

	/**
	 * Tests only
	 *
	 * @internal
	 * @ignore
	 */
	public static function set_not_bootstrapped() {
		self::$assume_not_bootstrapped   = true;
		self::$bootstrapped_by_wordpress = false;
	}

	public static function was_bootstrapped_by_wordpress() {
		return self::$bootstrapped_by_wordpress;
	}

	public static function is_environment_bootstrapped() {
		return self::$environment_bootstrapped;
	}

	public static function bootstrap_environment() {
		if ( self::is_environment_bootstrapped() ) {
			return;
		}

		if ( ! self::$are_incompatible_plugins_filtered ) {
			matomo_filter_incompatible_plugins( $GLOBALS['MATOMO_PLUGINS_ENABLED'] );

			self::$are_incompatible_plugins_filtered = true;
		}

		if ( ! defined( 'PIWIK_ENABLE_DISPATCH' ) ) {
			define( 'PIWIK_ENABLE_DISPATCH', false );
		}

		// prevent session related errors during install making it more stable
		if ( ! defined( 'PIWIK_ENABLE_SESSION_START' ) ) {
			define( 'PIWIK_ENABLE_SESSION_START', false );
		}

		if ( ! defined( 'PIWIK_DOCUMENT_ROOT' ) ) {
			define( 'PIWIK_DOCUMENT_ROOT', plugin_dir_path( MATOMO_ANALYTICS_FILE ) . 'app' );
		}

		require_once PIWIK_DOCUMENT_ROOT . '/bootstrap.php';

		if ( ! defined( 'PIWIK_INCLUDE_PATH' ) ) {
			define( 'PIWIK_INCLUDE_PATH', PIWIK_DOCUMENT_ROOT );
		}

		require_once PIWIK_INCLUDE_PATH . '/core/bootstrap.php';
		// we need to install now

		include_once 'Db/WordPress.php';

		$environment = new Environment( null, self::$extra_di_definitions );
		$environment->init();

		self::$environment_bootstrapped = true;
	}

	public function bootstrap() {
		if ( self::is_bootstrapped() ) {
			return;
		}

		self::$bootstrapped_by_wordpress = true;
		self::$assume_not_bootstrapped   = false; // we need to unset it again to prevent recursion

		self::bootstrap_environment();

		FrontController::unsetInstance();
		$controller = FrontController::getInstance();
		$controller->init();

		add_action(
			'set_current_user',
			function () {
				$access = \Piwik\Access::getInstance();
				if ( $access ) {
					$access->reloadAccess();
				}
			}
		);
	}

	public static function is_bootstrapped() {
		if ( true === self::$assume_not_bootstrapped ) {
			return false;
		}

		return defined( 'PIWIK_DOCUMENT_ROOT' );
	}

	/**
	 * @api
	 */
	public static function do_bootstrap() {
		$bootstrap = new Bootstrap();
		$bootstrap->bootstrap();
	}

	public static function destroy_bootstrapped_environment() {
		if ( ! self::$environment_bootstrapped ) {
			return;
		}

		Option::clearCache();
		\Piwik\Site::clearCache();
		Cache::getTransientCache()->flushAll();
		\Piwik\Plugin\API::unsetAllInstances();
		\Piwik\Tracker\Cache::$cache = null;
		\Piwik\DataTable\Manager::getInstance()->deleteAll();
		\Piwik\DataAccess\ArchiveTableCreator::clear();
		\Piwik\Plugins\ScheduledReports\API::$cache = [];
		\Piwik\Singleton::clearAll();
		\Piwik\ArchiveProcessor\PluginsArchiver::$archivers = [];
		\Piwik\Notification\Manager::cancelAllNotifications();
		StaticContainer::clearContainer();

		self::$environment_bootstrapped  = false;
		self::$bootstrapped_by_wordpress = false;
		self::$assume_not_bootstrapped   = true;
	}
}
