<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo;

use Exception;
use Piwik\Cache;
use Piwik\Common;
use Piwik\Config;
use Piwik\Db;
use Piwik\Filesystem;
use Piwik\Option;
use Piwik\Plugins\Installation\ServerFilesGenerator;
use Piwik\SettingsServer;
use Piwik\Version;
use WP_Upgrader;
use WpMatomo\TrackingCode\GeneratorOptions;
use WpMatomo\TrackingCode\TrackingCodeGenerator;
use WpMatomo\Updater\UpdateInProgressException;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Updater {
	const LOCK_NAME = 'matomo_updater';

	/**
	 * @var Settings
	 */
	private $settings;

	/**
	 * @var Logger
	 */
	private $logger;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
		$this->logger   = new Logger();
	}

	public function load_plugin_functions() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		return function_exists( 'get_plugin_data' );
	}

	public function get_plugins_requiring_update() {
		if ( ! $this->load_plugin_functions() ) {
			return [];
		}

		$keys         = [];
		$plugin_files = $GLOBALS['MATOMO_PLUGIN_FILES'];
		if ( ! in_array( MATOMO_ANALYTICS_FILE, $plugin_files, true ) ) {
			array_unshift( $plugin_files, MATOMO_ANALYTICS_FILE );
			// making sure this plugin is in the list so when itself gets updated
			// it will execute the core updates
		}

		foreach ( $plugin_files as $plugin_file ) {
			$plugin_data = get_plugin_data( $plugin_file, $markup = false, $translate = false );

			$key           = Settings::OPTION_PREFIX . 'plugin-version-' . basename( str_ireplace( '.php', '', $plugin_file ) );
			$installed_ver = get_option( $key );
			if ( ! $installed_ver || $installed_ver !== $plugin_data['Version'] ) {
				if ( ! Installer::is_intalled() ) {
					return [];
				}
				$keys[ $key ] = $plugin_data['Version'];
			}
		}

		return $keys;
	}

	public function update_if_needed() {
		$plugins_requiring_update = $this->get_plugins_requiring_update();
		if ( ! empty( $plugins_requiring_update ) ) {
			try {
				$this->update();
			} catch ( UpdateInProgressException $e ) {
				$this->logger->log( 'Matomo update is already in progress' );

				return; // we also don't execute any further update as they should be executed in another process
			} catch ( Exception $e ) {
				$this->logger->log_exception( 'plugin_update', $e );
				return;
			}

			// we're scheduling another update in case there are some dimensions to be updated or anything
			// we do not do this in the "update" method as otherwise we might be calling this recursively...
			// it is possible that because the plugins need to be reloaded etc that those updates are not executed right
			// away but need an actual reload and cache clearance etc
			wp_schedule_single_event( time() + 15, ScheduledTasks::EVENT_UPDATE );

			// we make sure to delete cache even if no component was updated eg there may be translation updates etc
			// and caches need to be invalidated
			Filesystem::deleteAllCacheOnUpdate();

			$tracking_code_generator = new TrackingCodeGenerator( $this->settings, new GeneratorOptions( $this->settings ) );
			$tracking_code_generator->update_tracking_code( true );
		}

		foreach ( $plugins_requiring_update as $key => $plugin_version ) {
			update_option( $key, $plugin_version );
		}

		return array_keys( $plugins_requiring_update );
	}

	public function update( $update_from_version = null ) {
		Bootstrap::do_bootstrap();

		if ( $this->load_plugin_functions() ) {
			$plugin_data = get_plugin_data( MATOMO_ANALYTICS_FILE, $markup = false, $translate = false );

			$history = $this->settings->get_global_option( 'version_history' );
			if ( empty( $history ) || ! is_array( $history ) ) {
				$history = [];
			}

			if ( ! empty( $plugin_data['Version'] )
				 && ! in_array( $plugin_data['Version'], $history, true ) ) {
				// this allows us to see which versions of matomo the user was using before this update so we better understand
				// which version maybe regressed something
				array_unshift( $history, $plugin_data['Version'] );
				$history = array_slice( $history, 0, 5 ); // lets keep only the last 5 versions
				$this->settings->set_global_option( 'version_history', $history );
			}
		}

		$this->settings->set_global_option( 'core_version', Version::VERSION );
		$this->settings->save();

		$paths = new Paths();
		$paths->clear_cache_dir();

		Option::clearCache();
		try {
			Cache::flushAll();
		} catch ( \Exception $ex ) {
			if ( ! Installer::is_file_not_exists_failure( $ex ) ) { // ignore errors that involve a directory not existing
				throw $ex;
			}
		}

		$current_version = Option::get( 'version_core' );

		try {
			if ( ! empty( $update_from_version ) ) {
				$update_from_version = trim( $update_from_version );

				if ( ! preg_match( '/^\d+\.\d+.\d+/', $update_from_version ) ) {
					throw new \Exception( __( 'Invalid version. Please specify a full version identifier like "5.0.0".', 'matomo' ) );
				}

				if ( version_compare( $update_from_version, Version::VERSION, '>' ) ) {
					throw new \Exception( __( 'Invalid version. The given version is greater than the current Matomo version.', 'matomo' ) );
				}

				Option::set( 'version_core', $update_from_version );

				$installed_plugins = Config::getInstance()->PluginsInstalled['PluginsInstalled'];
				foreach ( $installed_plugins as $plugin ) {
					Option::set( 'version_' . $plugin, $update_from_version );
				}
			}

			\Piwik\Access::doAsSuperUser(
				function () {
					self::update_components();
					self::update_components();
				}
			);
		} catch ( \Exception $ex ) {
			Option::set( 'version_core', $current_version );
			throw $ex;
		}

		$upload_dir = $paths->get_upload_base_dir();

		$wp_filesystem = $paths->get_file_system();
		if ( $paths->is_upload_dir_writable() ) {
			$wp_filesystem->put_contents( $upload_dir . '/index.php', '//hello' );
			$wp_filesystem->put_contents( $upload_dir . '/index.html', '//hello' );
			$wp_filesystem->put_contents( $upload_dir . '/index.htm', '//hello' );
			$wp_filesystem->put_contents(
				$upload_dir . '/.htaccess',
				'<Files ~ "(\.mmdb)$">
' . ServerFilesGenerator::getDenyHtaccessContent() . '
</Files>
<Files ~ "(\.js)$">
' . ServerFilesGenerator::getAllowHtaccessContent() . '
</Files>'
			);
		}
		$config_dir = $paths->get_config_ini_path();
		if ( $paths->is_upload_dir_writable() ) {
			$wp_filesystem->put_contents( $config_dir . '/index.php', '//hello' );
			$wp_filesystem->put_contents( $config_dir . '/index.html', '//hello' );
			$wp_filesystem->put_contents( $config_dir . '/index.htm', '//hello' );
		}

		if ( $this->settings->should_disable_addhandler() ) {
			wp_schedule_single_event( time() + 10, ScheduledTasks::EVENT_DISABLE_ADDHANDLER );
		}

		do_action( 'matomo_update' );
	}

	public function is_upgrade_in_progress() {
		if ( ! self::load_upgrader() ) {
			return 'no upgrader';
		}

		if ( self::lock() ) {
			// we can get the lock meaning no update is in progress
			self::unlock();

			return false;
		}

		return true;
	}

	private static function load_upgrader() {
		if ( ! class_exists( '\WP_Upgrader', false ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		return class_exists( '\WP_Upgrader', false );
	}

	public static function lock() {
		// prevent the upgrade from being started several times at once
		// we lock for 4 minutes. In case of major Matomo upgrades the upgrade may take much longer but it should be
		// safe in this case to run the upgrade several times
		// important: we always need to use the same timeout otherwise if something did use `create_lock(2)` then
		// even though another job locked it for 4 minutes, the other job that locks it only for 2 seconds would release
		// the lock basically since WP does not remember the initialy set release timeout
		return self::load_upgrader() && WP_Upgrader::create_lock( self::LOCK_NAME, 60 * 4 );
	}

	public static function unlock() {
		return self::load_upgrader() && WP_Upgrader::release_lock( self::LOCK_NAME );
	}

	private static function update_components() {
		$updater                     = new \Piwik\Updater();
		$components_with_update_file = $updater->getComponentUpdates();

		if ( empty( $components_with_update_file ) ) {
			return false;
		}

		if ( ! self::lock() ) {
			throw new UpdateInProgressException();
		}

		try {
			SettingsServer::setMaxExecutionTime( 0 );

			if ( function_exists( 'ignore_user_abort' ) ) {
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				@ignore_user_abort( true );
			}

			self::ensure_log_tables_have_correct_row_format();
			$result = $updater->updateComponents( $components_with_update_file );
		} catch ( Exception $e ) {
			self::unlock();
			throw $e;
		}
		self::unlock();

		if ( ! empty( $result['errors'] ) ) {
			throw new Exception( 'Error while updating components: ' . implode( ', ', $result['errors'] ) );
		}

		\Piwik\Updater::recordComponentSuccessfullyUpdated( 'core', Version::VERSION );
		Filesystem::deleteAllCacheOnUpdate();
	}

	private static function ensure_log_tables_have_correct_row_format() {
		try {
			// we only care about log tables which can have dimension columns added/removed.
			// other tables won't have this problem.
			$core_log_tables = [
				'log_visit',
				'log_link_visit_action',
				'log_conversion',
				'log_conversion_item',
			];

			// get table status of all log_ tables
			$prefix     = Config::getInstance()->database['tables_prefix'];
			$table_info = \Piwik\Db::fetchAll( 'SHOW TABLE STATUS LIKE ?', [ "{$prefix}log_%" ] );
			$table_info = array_column( $table_info, null, 'Name' );

			// for every CORE log table that has Compact, switch to Dynamic
			foreach ( $core_log_tables as $table_name ) {
				$prefixed_name = Common::prefixTable( $table_name );
				if ( isset( $table_info[ $prefixed_name ] )
					&& 'Compact' === $table_info[ $prefixed_name ]['Row_format']
				) {
					Db::exec( "ALTER TABLE `$prefixed_name` ROW_FORMAT=Dynamic" );
				}
			}
		} catch ( \Exception $ex ) {
			// log and ignore
			$logger = new Logger();
			$logger->log( 'Failure when trying to ensure log table ROW_FORMATs are Dynamic: ' . $ex->getMessage() . "\n" . $ex->getTraceAsString(), Logger::LEVEL_DEBUG );
		}
	}
}
