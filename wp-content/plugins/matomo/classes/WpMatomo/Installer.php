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
use Piwik\Container\StaticContainer;
use Piwik\DbHelper;
use Piwik\Exception\NotYetInstalledException;
use Piwik\Plugin\API as PluginApi;
use Piwik\Plugin\Manager;
use Piwik\Plugins\SitesManager\Model;
use Piwik\SettingsPiwik;
use Piwik\Singleton;
use WpMatomo\Site\Sync;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Installer {
	const OPTION_NAME_INSTALL_DATE    = 'matomo-install-date';
	const OPTION_NAME_INSTALL_VERSION = 'matomo-install-version';

	const DEFAULT_DB_CHARSET = 'utf8';
	const DEFAULT_DB_COLLATE = '';

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

	public static function is_file_not_exists_failure( \Exception $ex ) {
		return preg_match( '/no such file or directory/i', $ex->getMessage() );
	}

	public function register_hooks() {
		add_action( 'activate_matomo/matomo.php', [ $this, 'install' ] ); // if activate_plugin is invoked with the path to the plugin entrypoint
		add_action( 'activate_matomo', [ $this, 'install' ] ); // if activate_plugin is invoked with the plugin slug
	}

	public function looks_like_it_is_installed() {
		$paths       = new Paths();
		$config_file = $paths->get_config_ini_path();

		$config_dir = dirname( $config_file );
		if ( ! is_dir( $config_dir ) ) {
			wp_mkdir_p( $config_dir );
		}

		if ( ! file_exists( $config_file ) ) {
			return false;
		}

		if ( ! $this->is_current_instance_installed() ) {
			return false;
		}

		return true;
	}

	public static function is_intalled() {
		try {
			Bootstrap::do_bootstrap();

			return SettingsPiwik::isMatomoInstalled();
		} catch ( NotYetInstalledException $e ) {
			// not yet installed.... we will need to install it
			return false;
		} catch ( \Zend_Db_Statement_Exception $e ) {
			// not yet installed.... we will need to install it
			return false;
		}
	}

	public function can_be_installed() {
		$paths      = new Paths();
		$upload_dir = $paths->get_upload_base_dir();

		return is_writable( $upload_dir ) || is_writable( dirname( $upload_dir ) );
	}

	public function install() {
		if ( ! $this->can_be_installed() ) {
			return false;
		}

		try {
			// prevent session related errors during install making it more stable
			if ( ! defined( 'PIWIK_ENABLE_SESSION_START' ) ) {
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
				define( 'PIWIK_ENABLE_SESSION_START', false );
			}

			Bootstrap::bootstrap_environment();

			if ( ! SettingsPiwik::isMatomoInstalled() || ! $this->looks_like_it_is_installed() ) {
				throw new NotYetInstalledException( 'Not yet installed' );
			}

			return false;
		} catch ( NotYetInstalledException $e ) {
			$this->logger->log( 'Matomo is not yet installed... installing now' );

			if ( $this->is_install_in_progress() ) {
				return false;
			}

			$this->mark_install_started();

			$db_info = $this->create_db();
			$this->create_config( $db_info );

			$this->install_plugins_one_at_a_time();

			$this->update_components();

			update_option( self::OPTION_NAME_INSTALL_DATE, time() );
			$plugin_data = get_plugin_data( MATOMO_ANALYTICS_FILE, $markup = false, $translate = false );
			if ( ! empty( $plugin_data['Version'] ) ) {
				update_option( self::OPTION_NAME_INSTALL_VERSION, $plugin_data['Version'] );
			}

			$this->create_website();
			$this->create_user(); // we sync users as early as possible to make sure things are set up correctly
			$this->install_tracker();

			try {
				$this->logger->log( 'Matomo will now init the environment' );
				$environment = new \Piwik\Application\Environment( null, Bootstrap::get_extra_di_definitions() );
				$environment->init();
			} catch ( Exception $e ) {
				$this->logger->log( 'Ignoring error environment init' );
				$this->logger->log_exception( 'install_env_init', $e );
			}

			try {
				// should load and install plugins
				$this->logger->log( 'Matomo will now init the front controller and install plugins etc' );
				\Piwik\FrontController::unsetInstance(); // make sure we're loading the latest instance
				$controller = \Piwik\FrontController::getInstance();
				$controller->init();
			} catch ( Exception $e ) {
				$this->logger->log( 'Ignoring error frontcontroller init' );
				$this->logger->log_exception( 'install_front_init', $e );
			}

			try {
				// sync user now again after installing plugins...
				// before eg the users_language table would not have been available yet
				$this->create_user();
			} catch ( Exception $e ) {
				$this->logger->log_exception( 'install_create_user', $e );
			}

			try {
				// update plugins if there are any
				$this->update_components();
			} catch ( Exception $e ) {
				$this->logger->log_exception( 'install_update_comp', $e );
			}

			$this->logger->log( 'Recording version and url' );

			DbHelper::recordInstallVersion();

			$this->set_matomo_url();

			$this->logger->log( 'Emptying some caches' );

			Singleton::clearAll();
			PluginApi::unsetAllInstances();
			try {
				Cache::flushAll();
			} catch ( \Exception $ex ) {
				if ( ! self::is_file_not_exists_failure( $ex ) ) { // ignore errors that involve a directory not existing
					throw $ex;
				}
			}

			$this->logger->log( 'Matomo install finished' );

			$this->mark_matomo_installed();

			// we're scheduling another update in case there are some dimensions to be updated or anything
			// it is possible that because the plugins need to be reloaded etc that those updates are not executed right
			// away but need an actual reload and cache clearance etc
			wp_schedule_single_event( time() + 30, ScheduledTasks::EVENT_UPDATE );

			// to set up geoip in the background later... don't want this to influence the install
			$tasks                      = new ScheduledTasks( $this->settings );
			$last_geoip_update_run_time = $tasks->get_last_time_before_cron( ScheduledTasks::EVENT_GEOIP );
			if ( empty( $last_geoip_update_run_time ) ) {
				wp_schedule_single_event( time() + 35, ScheduledTasks::EVENT_GEOIP );
			}

			// in case something fails with website or user creation
			// also to set up all the other users
			wp_schedule_single_event( time() + 45, ScheduledTasks::EVENT_SYNC );
		}

		return true;
	}

	public function set_matomo_url() {
		// note that the full url might not be possible to be set if the cron is executed on cli and it maybe doesn't have
		// the host or if a plugin overwrites the constant of WP_PLUGIN_URL which is used in plugins_url() to not include domain
		// see https://www.google.com/url?q=https://wordpress.org/support/topic/no-metrics-showing/%23topic-14362043-replies&source=gmail&ust=1620409922890000&usg=AFQjCNHyzG5-9v0A8bjg8aLVVbYSWxkTxg

		$matomo_url  = SettingsPiwik::getPiwikUrl();
		$plugins_url = plugins_url( 'app', MATOMO_ANALYTICS_FILE );
		$plugins_url = rtrim( $plugins_url, '/' ) . '/';
		// need to make sure to update plugins url if it changes eg if installed somewhere else or domain changes

		if ( $matomo_url
			 && $plugins_url === $matomo_url
			 && wp_parse_url( $matomo_url, PHP_URL_SCHEME )
			 && wp_parse_url( $matomo_url, PHP_URL_HOST )
		) {
			// if currently no scheme or host is set then we'll make sure to overwrite it
			return;
		}

		if ( ! $plugins_url ) {
			return;
		}

		$has_host = wp_parse_url( $plugins_url, PHP_URL_HOST );

		if ( ! $has_host ) {
			return;
		}

		$has_scheme = wp_parse_url( $plugins_url, PHP_URL_SCHEME );

		if ( ! $has_scheme ) {
			return;
		}

		SettingsPiwik::overwritePiwikUrl( $plugins_url );
	}

	private function install_tracker() {
		$this->logger->log( 'Matomo is now installing the tracker' );
		// making sure the tracker will be created in the wp uploads directory
		$updater = StaticContainer::get( 'Piwik\Plugins\CustomJsTracker\TrackerUpdater' );
		$updater->update();
	}

	private function create_db() {
		$this->logger->log( 'Matomo will now create the database' );

		try {
			$db_infos = self::get_db_infos();
			$config   = Config::getInstance();
			if ( isset( $config ) ) {
				$db_infos = array_merge( $config->database, $db_infos );
			}
			$config->database = $db_infos;

			DbHelper::checkDatabaseVersion();
		} catch ( Exception $e ) {
			$message = sprintf( 'Database info detection failed with %s in %s:%s.', $e->getMessage(), $e->getFile(), $e->getLine() );
			throw new Exception( $message, $e->getCode(), $e );
		}

		$tables_installed = DbHelper::getTablesInstalled();
		if ( count( $tables_installed ) > 0 ) {
			// todo define behaviour... might need to ask user how to proceed... but ideally we add check to
			// see if all tables are there and if so, reuse them...
			return $db_infos;
		}
		DbHelper::createTables();
		DbHelper::createAnonymousUser();

		return $db_infos;
	}

	private function create_config( $db_info ) {
		$this->logger->log( 'Matomo is now creating the config' );
		$home_url = home_url();
		$domain   = wp_parse_url( $home_url, PHP_URL_HOST );
		if ( $domain ) {
			$port = wp_parse_url( $home_url, PHP_URL_PORT );
			if ( $port ) {
				$domain .= ':' . $port;
			}
		} else {
			$domain = $home_url;
		}
		$general = [
			'trusted_hosts' => [ $domain ],
			'salt'          => Common::generateUniqId(),
		];
		$config  = Config::getInstance();
		$path    = $config->getLocalPath();
		if ( ! is_dir( dirname( $path ) ) ) {
			wp_mkdir_p( dirname( $path ) );
		}
		$db_default      = [];
		$general_default = [];
		if ( $config->database ) {
			$db_default = $config->database;
		}
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		if ( $config->General ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$general_default = $config->General;
		}
		$config->database = array_merge( $db_default, $db_info );
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$config->General = array_merge( $general_default, $general );
		$config->forceSave();

		$mode = 0664;
		if ( ! chmod( $config->getLocalPath(), $mode ) ) {
			$this->logger->log( "Can't chmod " . $config->getLocalPath() );
		}
	}

	private function create_website() {
		$sync = new Sync( $this->settings );

		return $sync->sync_current_site();
	}

	private function create_user() {
		$sync = new User\Sync();

		$sync->sync_current_users();
	}

	/**
	 * @param array $default params
	 *
	 * @return array
	 */
	public static function get_db_infos( $default = [] ) {
		global $wpdb;

		$socket    = '';
		$host_data = null;
		$host      = null;
		$port      = 3306;
		if ( method_exists( $wpdb, 'parse_db_host' ) ) {
			// WP 4.9+
			$host_data = $wpdb->parse_db_host( DB_HOST );
			if ( $host_data ) {
				list( $host, $port, $socket, $is_ipv6 ) = $host_data;
				if ( ! $port && ! $socket ) {
					$port = 3306;
				}
			}
		}

		if ( ! $host_data || ! $host ) {
			// WP 4.8 and older
			// in case DB credentials change in WordPress, we need to apply these changes here as well on demand
			$host_parts = explode( ':', DB_HOST );
			$host       = $host_parts[0];
			if ( count( $host_parts ) === 2 && is_numeric( $host_parts[1] ) ) {
				$port = $host_parts[1];
			} else {
				$port = 3306;
			}
		}

		$charset = $wpdb->charset ? $wpdb->charset : self::DEFAULT_DB_CHARSET;
		if ( defined( 'MATOMO_DB_CHARSET' ) && MATOMO_DB_CHARSET ) {
			$charset = MATOMO_DB_CHARSET;
		}

		$collation = $wpdb->collate ? $wpdb->collate : self::DEFAULT_DB_COLLATE;
		if ( defined( 'MATOMO_DB_COLLATE' ) && MATOMO_DB_COLLATE ) {
			$collation = MATOMO_DB_COLLATE;
		}

		$database = [
			'host'          => $host,
			'port'          => $port,
			'username'      => DB_USER,
			'password'      => DB_PASSWORD,
			'dbname'        => DB_NAME,
			'charset'       => $charset,
			'collation'     => $collation,
			'tables_prefix' => $wpdb->prefix . MATOMO_DATABASE_PREFIX,
			'adapter'       => 'WordPress',
		];
		if ( ! empty( $socket ) ) {
			$database['unix_socket'] = $socket;
		}
		$database = array_merge( $default, $database );

		return $database;
	}

	private function update_components() {
		$this->logger->log( 'Matomo will now trigger an update' );
		Updater::unlock(); // make sure the update can be executed
		$updater = new Updater( $this->settings );
		$updater->update();
	}

	/**
	 * public for tests
	 *
	 * @return bool
	 */
	public function is_current_instance_installed() {
		$installed_components = $this->settings->get_option( Settings::INSTANCE_COMPONENTS_INSTALLED );
		if ( empty( $installed_components ) ) {
			$installed_components = '[]';
		}
		$installed_components = json_decode( $installed_components, true );

		if ( empty( $installed_components['core'] ) ) {
			return false;
		}

		// NOTE: this doesn't handle core plugins, but since they are always present during an install, we
		// shouldn't need to
		$plugin_files = isset( $GLOBALS['MATOMO_PLUGIN_FILES'] ) ? $GLOBALS['MATOMO_PLUGIN_FILES'] : [];
		$plugin_files = is_array( $plugin_files ) ? $plugin_files : [];

		foreach ( $plugin_files as $file ) {
			$plugin_name = basename( dirname( $file ) );
			if ( 'matomo' === $plugin_name ) {
				continue;
			}

			if ( empty( $installed_components[ $plugin_name ] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * public for tests
	 *
	 * @return void
	 */
	public function mark_matomo_installed() {
		$installed = $this->settings->get_option( Settings::INSTANCE_COMPONENTS_INSTALLED );
		if ( empty( $installed ) ) {
			$installed = '[]';
		}
		$installed = json_decode( $installed, true );

		$installed['core'] = 1;
		foreach ( Config::getInstance()->PluginsInstalled['PluginsInstalled'] as $plugin_name ) {
			$installed[ $plugin_name ] = 1;
		}

		$this->settings->set_option( Settings::INSTANCE_COMPONENTS_INSTALLED, wp_json_encode( $installed ) );
		$this->settings->save();

		$option_name = Settings::OPTION_PREFIX . 'install-start-time';
		delete_option( $option_name );
	}

	private function mark_install_started() {
		$option_name = Settings::OPTION_PREFIX . 'install-start-time';
		update_option( $option_name, time() );
	}

	private function is_install_in_progress() {
		$five_minutes = 5 * 60;

		$option_name = Settings::OPTION_PREFIX . 'install-start-time';
		$start_time  = get_option( $option_name );

		// install is in progress if there is no last start time, or the last start time is before
		// five minutes ago (we assume it failed in this case)
		return ! empty( $start_time ) && $start_time >= time() - $five_minutes;
	}

	/**
	 * Install all plugins including core and non-core plugins. Non-core plugins
	 * are installed one at a time. Uninstalled plugins will not be loaded
	 * when each non-core plugin is installed.
	 *
	 * This works around the core bug where exceptions can be thrown when an
	 * uninstalled plugin, which is loaded while another plugin is being installed,
	 * handles the "plugin installed" event.
	 *
	 * In a standalone Matomo, this likely won't be an issue, as multiple non-core
	 * plugins are not usually installed at the same time. In Matomo for WordPress,
	 * this can happen as a matter of course in Multi Site installs.
	 *
	 * If a user creates a new WordPress site with multiple non-core plugins installed,
	 * by default the Matomo install process will try to install all of them at once,
	 * causing an error.
	 *
	 * @return void
	 */
	private function install_plugins_one_at_a_time() {
		Config::getInstance()->PluginsInstalled = [ 'PluginsInstalled' => [] ];

		$plugin_names     = array_map(
			function ( $path ) {
				return basename( dirname( $path ) );
			},
			$GLOBALS['MATOMO_PLUGIN_FILES']
		);
		$non_core_plugins = array_filter(
			$plugin_names,
			function ( $name ) {
				return 'matomo' !== $name;
			}
		);

		// unload plugins since plugin instances may be holding out of date information
		$plugin_manager = Manager::getInstance();
		$plugin_manager->unloadPlugins();
		$plugin_manager->loadActivatedPlugins();

		// first, install core plugins without non-core plugins loaded
		foreach ( $non_core_plugins as $plugin ) {
			$plugin_manager->unloadPlugin( $plugin );
		}

		$plugin_manager->installLoadedPlugins();

		// then for every non-core plugin, install one at a time
		foreach ( $non_core_plugins as $plugin ) {
			$plugin_manager->loadPlugin( $plugin );
			$plugin_manager->installLoadedPlugins();
		}

		// reload activated plugins just in case something didn't go right above
		$plugin_manager->loadActivatedPlugins();
	}
}
