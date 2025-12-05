<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Admin;

use Exception;
use ITSEC_Modules;
use Piwik\CliMulti;
use Piwik\CliMulti\CliPhp;
use Piwik\Common;
use Piwik\Config;
use Piwik\Container\StaticContainer;
use Piwik\DeviceDetector\DeviceDetectorFactory;
use Piwik\Filesystem;
use Piwik\Piwik;
use Piwik\Plugin;
use Piwik\Plugins\CoreAdminHome\API;
use Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult;
use Piwik\Plugins\Diagnostics\DiagnosticService;
use Piwik\Plugins\SitesManager\Model;
use Piwik\Plugins\UserCountry\LocationProvider;
use Piwik\Plugins\WordPress\WordPress;
use Piwik\Scheduler\Scheduler;
use Piwik\Scheduler\Task;
use Piwik\Scheduler\TaskLoader;
use Piwik\SettingsPiwik;
use Piwik\Tracker\Failures;
use Piwik\Version;
use WpMatomo;
use WpMatomo\Bootstrap;
use WpMatomo\Capabilities;
use WpMatomo\Installer;
use WpMatomo\Logger;
use WpMatomo\Paths;
use WpMatomo\ScheduledTasks;
use WpMatomo\Settings;
use WpMatomo\Site;
use WpMatomo\Site\Sync as SiteSync;
use WpMatomo\Updater;
use WpMatomo\User\Sync as UserSync;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

/**
 * error_reporting is required for this page
 * phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting
 *
 * We want a real data, not something coming from cache
 * phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
 *
 * This is a report error, so silent the possible errors
 * phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged
 *
 * We cannot use parameters of statements as this is the table names we build
 * phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
 * phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
 */
class SystemReport {
	const NONCE_NAME                      = 'matomo_troubleshooting';
	const TROUBLESHOOT_SYNC_USERS         = 'matomo_troubleshooting_action_site_users';
	const TROUBLESHOOT_SYNC_ALL_USERS     = 'matomo_troubleshooting_action_all_users';
	const TROUBLESHOOT_SYNC_SITE          = 'matomo_troubleshooting_action_site';
	const TROUBLESHOOT_SYNC_ALL_SITES     = 'matomo_troubleshooting_action_all_sites';
	const TROUBLESHOOT_CLEAR_MATOMO_CACHE = 'matomo_troubleshooting_action_clear_matomo_cache';
	const TROUBLESHOOT_ARCHIVE_NOW        = 'matomo_troubleshooting_action_archive_now';
	const TROUBLESHOOT_UPDATE_GEOIP_DB    = 'matomo_troubleshooting_action_update_geoipdb';
	const TROUBLESHOOT_CLEAR_LOGS         = 'matomo_troubleshooting_action_clear_logs';
	const TROUBLESHOOT_RUN_UPDATER        = 'matomo_troubleshooting_action_run_updater';
	const REGENERATE_TRACKING_CODE        = 'matomo_troubleshooting_action_regen_tracking_code';
	const RUN_SCHEDULED_TASK              = 'matomo_troubleshooting_action_run_task';

	private $not_compatible_plugins = [
		'minify-html-markup',
	];

	private $valid_tabs = [ 'troubleshooting' ];

	/**
	 * @var Settings
	 */
	private $settings;

	/**
	 * @var Logger
	 */
	private $logger;

	private $initial_error_reporting = null;

	private $shell_exec_available;
	/**
	 * @var \WpMatomo\Db\Settings
	 */
	public $db_settings;
	/**
	 * @var string the php binary used by Matomo (use get_php_binary() to access)
	 */
	private $binary;

	/**
	 * @var string
	 */
	private $path_to_plugin;

	private static $matomo_tables;

	public function __construct( Settings $settings ) {
		$this->settings             = $settings;
		$this->logger               = new Logger();
		$this->db_settings          = new \WpMatomo\Db\Settings();
		$this->shell_exec_available = function_exists( 'shell_exec' );
		$this->path_to_plugin       = $this->get_abs_path_to_plugin();
	}

	public function get_not_compatible_plugins() {
		return $this->not_compatible_plugins;
	}

	private function execute_troubleshoot_if_needed() {
		if ( ! empty( $_POST )
			&& is_admin()
			&& check_admin_referer( self::NONCE_NAME )
			&& current_user_can( Capabilities::KEY_SUPERUSER )
		) {
			if ( ! empty( $_POST[ self::TROUBLESHOOT_ARCHIVE_NOW ] ) ) {
				Bootstrap::do_bootstrap();
				$scheduled_tasks = new ScheduledTasks( $this->settings );

				if ( ! defined( 'PIWIK_ARCHIVE_NO_TRUNCATE' ) ) {
					// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
					define( 'PIWIK_ARCHIVE_NO_TRUNCATE', 1 ); // when triggering it manually, we prefer the full error message
				}

				try {
					// force invalidation of archive to ensure it actually will rearchive the data
					$site   = new Site();
					$idsite = $site->get_current_matomo_site_id();
					if ( $idsite ) {
						$timezone   = \Piwik\Site::getTimezoneFor( $idsite );
						$now_string = \Piwik\Date::factory( 'now', $timezone )->toString();
						foreach ( [ 'day' ] as $period ) {
							API::getInstance()->invalidateArchivedReports( $idsite, $now_string, $period, false, false );
						}
					}
				} catch ( Exception $e ) {
					$this->logger->log_exception( 'archive_invalidate', $e );
				}

				try {
					$errors = $scheduled_tasks->archive( true, false );
				} catch ( Exception $e ) {
					echo '<div class="error"><p>' . esc_html__( 'Matomo Archive Error', 'matomo' ) . ': ' . esc_html( matomo_anonymize_value( $e->getMessage() . ' =>' . $this->logger->get_readable_trace( $e ) ) ) . '</p></div>';
					throw $e;
				}

				if ( ! empty( $errors ) ) {
					echo '<div class="notice notice-warning"><p>Matomo Archive Warnings: ';
					foreach ( $errors as $error ) {
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
						echo nl2br( esc_html( matomo_anonymize_value( var_export( $error, 1 ) ) ) );
						echo '<br/>';
					}
					echo '</p></div>';
				} else {
					echo '<div class="notice notice-success"><p>' . esc_html__( 'Matomo Archiving completed successfully!', 'matomo' ) . '</p></div>';
				}
			}

			if ( ! empty( $_POST[ self::TROUBLESHOOT_CLEAR_MATOMO_CACHE ] ) ) {
				$paths = new Paths();
				$paths->clear_cache_dir();
				// we first delete the cache dir manually just in case there's something
				// going wrong with matomo and bootstrapping would not even be possible.
				Bootstrap::do_bootstrap();
				Filesystem::deleteAllCacheOnUpdate();
				Updater::unlock();
			}

			if ( ! empty( $_POST[ self::TROUBLESHOOT_UPDATE_GEOIP_DB ] ) ) {
				$scheduled_tasks = new ScheduledTasks( $this->settings );
				$scheduled_tasks->update_geo_ip2_db();
			}

			if ( ! empty( $_POST[ self::TROUBLESHOOT_CLEAR_LOGS ] ) ) {
				$this->logger->clear_logged_exceptions();
			}

			if ( ! $this->settings->is_network_enabled() || ! is_network_admin() ) {
				if ( ! empty( $_POST[ self::TROUBLESHOOT_SYNC_USERS ] ) ) {
					$sync = new UserSync();
					$sync->sync_current_users();
				}
				if ( ! empty( $_POST[ self::TROUBLESHOOT_SYNC_SITE ] ) ) {
					$sync = new SiteSync( $this->settings );
					$sync->sync_current_site();
				}
				if ( ! empty( $_POST[ self::TROUBLESHOOT_RUN_UPDATER ] ) ) {
					$update_from_version = ! empty( $_POST['matomo_troubleshooting_update_from'] )
						? sanitize_text_field( wp_unslash( $_POST['matomo_troubleshooting_update_from'] ) )
						: null;
					$update_from_version = trim( $update_from_version );

					if ( ! empty( $update_from_version )
						&& ! preg_match( '/^\d+(?:.\d+)*$/', $update_from_version )
					) {
						echo '<div class="error"><p>' . esc_html__( 'Matomo Update Error', 'matomo' )
							. ': unrecognized version string "' . esc_html( $update_from_version )
							. '", ignoring.</p></div>';

						$update_from_version = '';
					}

					try {
						Updater::unlock();
						$sync = new Updater( $this->settings );
						$sync->update( $update_from_version );
					} catch ( \Exception $e ) {
						echo '<div class="error"><p>' . esc_html__( 'Matomo Update Error', 'matomo' ) . ': ' . esc_html( matomo_anonymize_value( $e->getMessage() . ' =>' . $this->logger->get_readable_trace( $e ) ) ) . '</p></div>';
					}
				}
			}
			if ( $this->settings->is_network_enabled() ) {
				if ( ! empty( $_POST[ self::TROUBLESHOOT_SYNC_ALL_SITES ] ) ) {
					$sync = new SiteSync( $this->settings );
					$sync->sync_all();
				}
				if ( ! empty( $_POST[ self::TROUBLESHOOT_SYNC_ALL_USERS ] ) ) {
					$sync = new UserSync();
					$sync->sync_all();
				}
			}

			if ( ! empty( $_POST[ self::REGENERATE_TRACKING_CODE ] ) ) {
				try {
					Bootstrap::do_bootstrap();

					// regenerate tracker.js file in Matomo
					Piwik::postEvent( 'CustomJsTracker.updateTracker' );

					// regenerate embed tracking code in MWP
					$options                 = new WpMatomo\TrackingCode\GeneratorOptions( $this->settings );
					$tracking_code_generator = new WpMatomo\TrackingCode\TrackingCodeGenerator( $this->settings, $options );

					$tracking_code_generator->update_tracking_code( true );

					echo '<div class="notice notice-success"><p>' . esc_html__( 'JavaScript tracking code regenerated successfully.', 'matomo' ) . '</p></div>';
				} catch ( \Exception $ex ) {
					echo '<div class="error"><p>' . esc_html__( 'Matomo Error', 'matomo' ) . ': ' . esc_html( matomo_anonymize_value( $e->getMessage() . ' =>' . $this->logger->get_readable_trace( $e ) ) ) . '</p></div>';
				}
			}

			if ( ! empty( $_POST[ self::RUN_SCHEDULED_TASK ] ) ) {
				try {
					Bootstrap::do_bootstrap();

					if ( empty( $_POST['matomo_troubleshooting_run_task'] ) ) {
						throw new \Exception( __( 'No task specified.', 'matomo' ) );
					}

					$task_to_run = sanitize_text_field( wp_unslash( $_POST['matomo_troubleshooting_run_task'] ) );

					$scheduler = StaticContainer::get( Scheduler::class );
					$message   = $scheduler->runTaskNow( $task_to_run );

					echo '<div class="notice notice-success"><p>' . esc_html__( 'Task ran successfully', 'matomo' ) . ': ' . esc_html( $message ) . '</p></div>';
				} catch ( \Exception $e ) {
					echo '<div class="error"><p>' . esc_html__( 'Matomo Error', 'matomo' ) . ': ' . esc_html( matomo_anonymize_value( $e->getMessage() . ' =>' . $this->logger->get_readable_trace( $e ) ) ) . '</p></div>';
				}
			}
		}
	}

	private function get_error_tables() {
		$matomo_tables = self::$matomo_tables;

		if ( ! $matomo_tables ) {
			$matomo_tables       = [
				[
					'title'        => 'Matomo',
					'rows'         => $this->get_matomo_info(),
					'has_comments' => true,
				],
				[
					'title'        => 'WordPress',
					'rows'         => $this->get_wordpress_info(),
					'has_comments' => true,
				],
				[
					'title'        => 'WordPress Plugins',
					'rows'         => $this->get_plugins_info(),
					'has_comments' => true,
				],
				[
					'title'        => 'Server',
					'rows'         => $this->get_server_info(),
					'has_comments' => true,
				],
				[
					'title'        => 'PHP cli',
					'rows'         => $this->get_phpcli_info(),
					'has_comments' => true,
				],
				[
					'title'        => 'Database',
					'rows'         => $this->get_db_info(),
					'has_comments' => true,
				],
				[
					'title'        => 'Browser',
					'rows'         => $this->get_browser_info(),
					'has_comments' => true,
				],
			];
			self::$matomo_tables = $matomo_tables;
		}

		return $matomo_tables;
	}

	public function errors_present() {
		$cache_key   = $this->get_errors_present_cache_key();
		$cache_value = get_site_transient( $cache_key );

		if ( false === $cache_value ) {
			// pre-record that there were no errors found. in case the system report fails to execute, this will
			// allow the rest of Matomo for WordPress to continue to still be usable.
			set_site_transient( $cache_key, 0, WEEK_IN_SECONDS );

			$matomo_tables = $this->get_error_tables();

			$matomo_tables = apply_filters( 'matomo_systemreport_tables', $matomo_tables );
			$matomo_tables = $this->add_errors_first( $matomo_tables );

			$this->set_errors_present_transient( $matomo_tables );
		}

		return 1 === (int) $cache_value;
	}

	public function show() {
		$this->execute_troubleshoot_if_needed();

		$settings = $this->settings;

		$matomo_active_tab = '';

		if ( isset( $_GET['tab'] ) ) {
			$tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
			if ( in_array( $tab, $this->valid_tabs, true ) ) {
				$matomo_active_tab = $tab;
			}
		}

		$matomo_tables                    = [];
		$matomo_has_exception_logs        = [];
		$matomo_has_warning_and_no_errors = false;
		$matomo_scheduled_tasks           = [];

		if ( empty( $matomo_active_tab ) ) { // system report
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting
			$this->initial_error_reporting = @error_reporting();
			$matomo_tables                 = $this->get_error_tables();
			$matomo_tables                 = apply_filters( 'matomo_systemreport_tables', $matomo_tables );

			$matomo_has_errors = $this->set_errors_present_transient( $matomo_tables );

			$matomo_tables                    = $this->add_errors_first( $matomo_tables );
			$matomo_has_warning_and_no_errors = $this->has_only_warnings_no_error( $matomo_tables );
			$matomo_has_exception_logs        = $this->logger->get_last_logged_entries();
		} else { // troubleshooting
			try {
				Bootstrap::do_bootstrap();
				$scheduler              = StaticContainer::get( Scheduler::class );
				$matomo_scheduled_tasks = $scheduler->getTaskList();
			} catch ( \Exception $e ) {
				$this->logger->log_exception( 'troubleshooting', $e );
			}
		}

		include dirname( __FILE__ ) . '/views/systemreport.php';
	}

	private function has_only_warnings_no_error( $report_tables ) {
		$has_warning = false;
		$has_error   = false;
		foreach ( $report_tables as $report_table ) {
			foreach ( $report_table['rows'] as $row ) {
				if ( ! empty( $row['is_error'] ) ) {
					$has_error = true;
				}
				if ( ! empty( $row['is_warning'] ) ) {
					$has_warning = true;
				}
			}
		}

		return $has_warning && ! $has_error;
	}

	private function add_errors_first( $report_tables ) {
		$errors = [
			'title'        => 'Errors',
			'rows'         => [],
			'has_comments' => true,
		];
		foreach ( $report_tables as $report_table ) {
			foreach ( $report_table['rows'] as $row ) {
				if ( ! empty( $row['is_error'] ) ) {
					$errors['rows'][] = $row;
				}
			}
		}

		if ( ! empty( $errors['rows'] ) ) {
			array_unshift( $report_tables, $errors );
		}

		return $report_tables;
	}

	private function check_file_exists_and_writable( $rows, $path_to_check, $title, $required ) {
		$file_exists   = file_exists( $path_to_check );
		$file_readable = is_readable( $path_to_check );
		$file_writable = is_writable( $path_to_check );
		$comment       = '"' . $path_to_check . '" ';
		if ( ! $file_exists ) {
			$comment .= sprintf( esc_html__( '%s does not exist. ', 'matomo' ), $title );
		}
		if ( ! $file_readable ) {
			$comment .= sprintf( esc_html__( '%s is not readable. ', 'matomo' ), $title );
		}
		if ( ! $file_writable ) {
			$comment .= sprintf( esc_html__( '%s is not writable. ', 'matomo' ), $title );
		}

		$rows[] = [
			'name'       => sprintf( esc_html__( '%s exists and is writable.', 'matomo' ), $title ),
			'value'      => $file_exists && $file_readable && $file_writable ? esc_html__( 'Yes', 'matomo' ) : esc_html__( 'No', 'matomo' ),
			'comment'    => $comment,
			'is_error'   => $required && ( ! $file_exists || ! $file_readable ),
			'is_warning' => ! $required && ( ! $file_exists || ! $file_readable ),
		];

		return $rows;
	}

	private function get_phpcli_info() {
		$rows = [];

		if ( $this->shell_exec_available ) {
			try {
				$cli_multi = new CliMulti();

				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$supports_async = $cli_multi->supportsAsync;
			} catch ( \Exception $ex ) {
				$rows[] = [
					'name'       => esc_html__( 'PHP CLI configuration', 'matomo' ),
					'value'      => esc_html__( 'Unexpected error', 'matomo' ),
					'is_warning' => true,
					'comment'    => sprintf( esc_html__( 'Could not detect whether async archiving is enabled: %s', 'matomo' ), $ex->getMessage() ),
				];
			}

			$phpcli_version = $this->get_phpcli_output( '-r "echo phpversion();"' );

			$is_warning = false;
			$comment    = '';

			$advanced_settings_url = home_url( '/wp-admin/admin.php?page=matomo-settings&tab=advanced#matomo[disable_async_archiving]' );

			$is_using_cli_archiving = ! \WpMatomo::is_async_archiving_manually_disabled() && $supports_async;

			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			if ( version_compare( $phpcli_version, PHP_VERSION ) < 0 ) {
				if ( $is_using_cli_archiving ) {
					$is_warning = true;
				}

				$comment = sprintf(
					esc_html__( 'The detected PHP CLI version does not match the PHP web version. To avoid archiving errors, %1$senable archiving via HTTP requests%2$s, or %3$smanually set the path to your PHP CLI executable%4$s to the one for PHP version %5$s.', 'matomo' ),
					'<a href="' . $advanced_settings_url . '">',
					'</a>',
					'<a href="https://matomo.org/faq/how-to-solve-the-error-message-your-php-cli-version-is-not-compatible-with-the-matomo-requirements/" target="_blank" rel="noreferrer noopener">',
					'</a>',
					PHP_VERSION
				);
			}
			$rows[] = [
				'name'       => esc_html__( 'PHP CLI Version', 'matomo' ),
				'value'      => $phpcli_version,
				'comment'    => $comment,
				'is_warning' => $is_warning,
			];

			$is_error = false;
			$value    = __( 'ok', 'matomo' );
			$comment  = '';
			if ( ! intval( $this->get_phpcli_output( '-r "echo extension_loaded(\'mysqli\');"' ) ) ) {
				$value    = __( 'missing', 'matomo' );
				$is_error = true;
				$comment  = esc_html__( 'Your PHP cli does not load the MySQLi extension. You might have archiving problems in Matomo but also others problems in your WordPress cron tasks. You should enable this extension', 'matomo' );
			}

			$rows[] = [
				'name'     => esc_html__( 'MySQLi support', 'matomo' ),
				'value'    => $value,
				'comment'  => $comment,
				'is_error' => $is_using_cli_archiving ? $is_error : false,
			];

			if ( $supports_async ) {
				$this->check_wp_can_be_loaded_in_php_cli( $rows );
			}
		}

		return $rows;
	}

	private function check_wp_can_be_loaded_in_php_cli( &$rows ) {
		if ( ! $this->binary ) {
			return;
		}

		$wp_load_path = $this->find_wp_load_path();
		if ( ! $wp_load_path ) {
			return;
		}

		$command = "-r 'require_once( \"$wp_load_path\" );'";
		$output  = $this->get_phpcli_output( $command );
		if ( empty( $output ) ) {
			$rows[] = [
				'name'  => esc_html__( 'PHP CLI configuration', 'matomo' ),
				'value' => esc_html__( 'Configured correctly', 'matomo' ),
			];
		} else {
			$error_message = esc_html__(
				'WordPress cannot be loaded via PHP CLI. Please ensure both WordPress and PHP CLI are correctly configured. Note: If you are using get_cfg_var() in your wp-config.php, you will need to make sure the php.ini file for PHP CLI has the correct values. You may need to contact your hosting provider for these changes to be made.',
				'matomo'
			);

			$rows[] = [
				'name'    => esc_html__( 'PHP CLI configuration', 'matomo' ),
				'value'   => esc_html__( 'warning', 'matomo' ),
				'comment' => $error_message,
			];
		}
	}

	private function get_phpcli_output( $phpcli_params ) {
		$output = '';
		if ( $this->shell_exec_available && $this->get_php_cli_binary() ) {
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_shell_exec
			$output = trim( '' . @shell_exec( $this->get_php_cli_binary() . ' ' . $phpcli_params ) );
		}

		return $output;
	}

	private function get_matomo_info() {
		$rows = [];

		$plugin_data  = get_plugin_data( MATOMO_ANALYTICS_FILE, $markup = false, $translate = false );
		$install_time = get_option( Installer::OPTION_NAME_INSTALL_DATE );

		$rows[] = [
			'name'    => esc_html__( 'Matomo Plugin Version', 'matomo' ),
			'value'   => $plugin_data['Version'],
			'comment' => '',
		];

		$paths            = new Paths();
		$path_config_file = $paths->get_config_ini_path();
		$rows             = $this->check_file_exists_and_writable( $rows, $path_config_file, 'Config', true );

		$path_tracker_file = $paths->get_matomo_js_upload_path();
		$rows              = $this->check_file_exists_and_writable( $rows, $path_tracker_file, 'JS Tracker', false );

		$rows[] = [
			'name'    => esc_html__( 'Plugin directories', 'matomo' ),
			'value'   => ! empty( $GLOBALS['MATOMO_PLUGIN_DIRS'] ) ? 'Yes' : 'No',
			'comment' => ! empty( $GLOBALS['MATOMO_PLUGIN_DIRS'] ) ? wp_json_encode( $GLOBALS['MATOMO_PLUGIN_DIRS'] ) : '',
		];

		$tmp_dir = $paths->get_tmp_dir();

		$rows[] = [
			'name'    => esc_html__( 'Tmp directory writable', 'matomo' ),
			'value'   => is_writable( $tmp_dir ),
			'comment' => $tmp_dir,
		];

		if ( ! empty( $_SERVER['MATOMO_WP_ROOT_PATH'] ) ) {
			// we can have / in this value
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$custom_path = rtrim( $_SERVER['MATOMO_WP_ROOT_PATH'], '/' ) . '/wp-load.php';
			$path_exists = file_exists( $custom_path );
			$comment     = '';
			if ( ! $path_exists ) {
				$comment = 'It seems the path does not point to the WP root directory.';
			}

			$rows[] = [
				'name'     => 'Custom MATOMO_WP_ROOT_PATH',
				'value'    => $path_exists,
				'is_error' => ! $path_exists,
				'comment'  => $comment,
			];
		}

		$report = null;

		if ( ! WpMatomo::is_safe_mode() ) {
			try {
				Bootstrap::do_bootstrap();
				/** @var DiagnosticService $service */
				$service = StaticContainer::get( DiagnosticService::class );
				$report  = $service->runDiagnostics();

				$rows[] = [
					'name'    => esc_html__( 'Matomo Version', 'matomo' ),
					'value'   => \Piwik\Version::VERSION,
					'comment' => '',
				];
			} catch ( Exception $e ) {
				$rows[] = [
					'name'     => esc_html__( 'Matomo System Check', 'matomo' ),
					'value'    => 'Failed to run Matomo system check.',
					'comment'  => $e->getMessage(),
					'is_error' => true,
				];
			}
		}

		$site   = new Site();
		$idsite = $site->get_current_matomo_site_id();

		$rows[] = [
			'name'    => esc_html__( 'Matomo Blog idSite', 'matomo' ),
			'value'   => $idsite,
			'comment' => '',
		];

		$install_date = '';
		if ( ! empty( $install_time ) ) {
			$install_date = 'Install date: ' . $this->convert_time_to_date( $install_time, true, false );
		}

		$rows[] = [
			'name'    => esc_html__( 'Matomo Install Version', 'matomo' ),
			'value'   => get_option( Installer::OPTION_NAME_INSTALL_VERSION ),
			'comment' => $install_date,
		];

		$wpmatomo_updater = new \WpMatomo\Updater( $this->settings );
		if ( ! WpMatomo::is_safe_mode() ) {
			$outstanding_updates = $wpmatomo_updater->get_plugins_requiring_update();
			$upgrade_in_progress = $wpmatomo_updater->is_upgrade_in_progress();
			$rows[]              = [
				'name'    => 'Upgrades outstanding',
				'value'   => ! empty( $outstanding_updates ),
				'comment' => ! empty( $outstanding_updates ) ? wp_json_encode( $outstanding_updates ) : '',
			];
			$rows[]              = [
				'name'    => 'Upgrade in progress',
				'value'   => $upgrade_in_progress,
				'comment' => '',
			];
		}

		if ( ! $wpmatomo_updater->load_plugin_functions() ) {
			// this should actually never happen...
			$rows[] = [
				'name'       => 'Matomo Upgrade Plugin Functions',
				'is_warning' => true,
				'value'      => false,
				'comment'    => 'Function "get_plugin_data" not available. There may be an issue with upgrades not being executed. Please reach out to us.',
			];
		}

		$rows[] = [
			'section' => 'Endpoints',
		];

		$rows[] = [
			'name'    => 'Matomo JavaScript Tracker URL',
			'value'   => '',
			'comment' => $paths->get_js_tracker_url_in_matomo_dir(),
		];

		$rows[] = [
			'name'    => 'Matomo JavaScript Tracker - WP Rest API',
			'value'   => '',
			'comment' => $paths->get_js_tracker_rest_api_endpoint(),
		];

		$rows[] = [
			'name'    => 'Matomo HTTP Tracking API',
			'value'   => '',
			'comment' => $paths->get_tracker_api_url_in_matomo_dir(),
		];

		$rows[] = [
			'name'    => 'Matomo HTTP Tracking API - WP Rest API',
			'value'   => '',
			'comment' => $paths->get_tracker_api_rest_api_endpoint(),
		];

		$matomo_plugin_dir_name = basename( dirname( MATOMO_ANALYTICS_FILE ) );
		if ( 'matomo' !== $matomo_plugin_dir_name ) {
			$rows[] = [
				'name'     => 'Matomo Plugin Name is correct',
				'value'    => false,
				'is_error' => true,
				'comment'  => 'The plugin name should be "matomo" but seems to be "' . $matomo_plugin_dir_name . '". As a result, admin pages and other features might not work. You might need to rename the directory name of this plugin and reactive the plugin.',
			];
		} elseif ( ! is_plugin_active( 'matomo/matomo.php' ) ) {
			$rows[] = [
				'name'     => 'Matomo Plugin not active',
				'value'    => false,
				'is_error' => true,
				'comment'  => 'It seems WordPress thinks that `matomo/matomo.php` is not active. As a result Matomo reporting and admin pages may not work. You may be able to fix this by deactivating and activating the Matomo Analytics plugin. One of the reasons this could happen is that you used to have Matomo installed in the wrong folder.',
			];
		}

		$rows[] = [
			'section' => 'Crons',
		];

		$scheduled_tasks = new ScheduledTasks( $this->settings );
		$all_events      = $scheduled_tasks->get_all_events();

		$rows[] = [
			'name'    => esc_html__( 'Server time', 'matomo' ),
			'value'   => $this->convert_time_to_date( time(), false ),
			'comment' => '',
		];

		$rows[] = [
			'name'    => esc_html__( 'Blog time', 'matomo' ),
			'value'   => $this->convert_time_to_date( time(), true ),
			'comment' => esc_html__( 'Below dates are shown in blog timezone', 'matomo' ),
		];

		foreach ( $all_events as $event_name => $event_config ) {
			$last_run_before = $scheduled_tasks->get_last_time_before_cron( $event_name );
			$last_run_after  = $scheduled_tasks->get_last_time_after_cron( $event_name );

			$next_scheduled = wp_next_scheduled( $event_name );

			$comment  = ' Last started: ' . $this->convert_time_to_date( $last_run_before, true, true ) . '.';
			$comment .= ' Last ended: ' . $this->convert_time_to_date( $last_run_after, true, true ) . '.';
			$comment .= ' Interval: ' . $event_config['interval'];

			$rows[] = [
				'name'    => $event_config['name'],
				'value'   => 'Next run: ' . $this->convert_time_to_date( $next_scheduled, true, true ),
				'comment' => $comment,
			];
		}

		$suports_async = false;
		if ( ! WpMatomo::is_safe_mode() && $report ) {
			$rows[] = [
				'section' => esc_html__( 'Mandatory checks', 'matomo' ),
			];

			$rows = $this->add_diagnostic_results( $rows, $report->getMandatoryDiagnosticResults() );

			$rows[] = [
				'section' => esc_html__( 'Optional checks', 'matomo' ),
			];
			$rows   = $this->add_diagnostic_results( $rows, $report->getOptionalDiagnosticResults() );

			try {
				$cli_multi     = new CliMulti();
				$suports_async = $cli_multi->supportsAsync();

				$rows[] = [
					'name'    => esc_html__( 'Supports Async Archiving', 'matomo' ),
					'value'   => $suports_async,
					'comment' => '',
				];
			} catch ( \Exception $e ) {
				$rows[] = [
					'name'     => esc_html__( 'Supports Async Archiving', 'matomo' ),
					'value'    => 'Could not check if async archiving is supported.',
					'comment'  => $e->getMessage(),
					'is_error' => true,
				];
			}

			$rows[] = [
				'name'    => 'Async Archiving Disabled in Setting',
				'value'   => $this->settings->is_async_archiving_disabled_by_option(),
				'comment' => '',
			];

			$location_provider = LocationProvider::getCurrentProvider();
			if ( $location_provider ) {
				try {
					$location_provider = LocationProvider::getCurrentProvider();
					if ( $location_provider ) {
						$rows[] = [
							'name'    => 'Location provider ID',
							'value'   => $location_provider->getId(),
							'comment' => '',
						];
						$rows[] = [
							'name'    => 'Location provider available',
							'value'   => $location_provider->isAvailable(),
							'comment' => '',
						];
						$rows[] = [
							'name'    => 'Location provider working',
							'value'   => $location_provider->isWorking(),
							'comment' => '',
						];
					}
				} catch ( \Exception $e ) {
					$rows[] = [
						'name'     => 'Location provider ID',
						'value'    => 'Failed to get the configured Location Provider.',
						'comment'  => $e->getMessage(),
						'is_error' => true,
					];
				}
			}

			if ( ! WpMatomo::is_safe_mode() ) {
				Bootstrap::do_bootstrap();
				$general = Config::getInstance()->General;

				if ( empty( $general['proxy_client_headers'] ) ) {
					foreach ( AdvancedSettings::$valid_host_headers as $header ) {
						if ( ! empty( $_SERVER[ $header ] ) ) {
							$rows[] = [
								'name'       => 'Proxy header',
								'value'      => $header,
								'is_warning' => true,
								'comment'    => sprintf( esc_html__( 'A proxy header is set which means you maybe need to configure a proxy header in the Advanced settings to make location reporting work. If the location in your reports is detected correctly, you can ignore this warning. %s', 'matomo' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://matomo.org/faq/wordpress/how-do-i-fix-the-proxy-header-warning-in-the-matomo-for-wordpress-system-report/', esc_html__( 'Learn more', 'matomo' ) ) ),
							];
						}
					}
				}

				try {
					$incompatible_plugins = Plugin\Manager::getInstance()->getIncompatiblePlugins( Version::VERSION );
					if ( ! empty( $incompatible_plugins ) ) {
						$rows[] = [
							'section' => esc_html__( 'Incompatible Matomo plugins', 'matomo' ),
						];
						foreach ( $incompatible_plugins as $plugin ) {
							$rows[] = [
								'name'     => 'Plugin has missing dependencies',
								'value'    => $plugin->getPluginName(),
								'is_error' => true,
								'comment'  => sprintf( esc_html__( '%s If the plugin requires a different Matomo version you may need to update it. If you no longer use it consider uninstalling it.', 'matomo' ), $plugin->getMissingDependenciesAsString( Version::VERSION ) ),
							];
						}
					}
				} catch ( \Exception $e ) {
					$rows[] = [
						'section' => esc_html__( 'Incompatible Matomo plugins', 'matomo' ),
					];
					$rows[] = [
						'name'     => 'Incompatible Matomo plugin check',
						'value'    => 'Failed to check for incompatible Matomo plugins.',
						'is_error' => true,
						'comment'  => $e->getMessage(),
					];
				}
			}

			$num_days_check_visits = 5;
			$had_visits            = $this->had_visits_in_last_days( $num_days_check_visits );

			if ( false === $had_visits || true === $had_visits ) {
				// do not show info if we could not detect it (had_visits === null)
				$comment = '';
				if ( ! $had_visits ) {
					$comment = sprintf( esc_html__( 'It looks like there were no visits in the last %s days. This may be expected if tracking is disabled, you have not added the tracking code, or your website does not have many visitors in general and you exclude your own visits.', 'matomo' ), $num_days_check_visits );
				}

				$rows[] = [
					'name'       => 'Had visit in last ' . $num_days_check_visits . ' days',
					'value'      => $had_visits,
					'is_warning' => ! $had_visits && $this->settings->is_tracking_enabled(),
					'comment'    => $comment,
				];
			}

			if ( ! WpMatomo::is_safe_mode() ) {
				Bootstrap::do_bootstrap();

				try {
					$matomo_url = SettingsPiwik::getPiwikUrl();
					$rows[]     = [
						'name'    => 'Matomo URL',
						'comment' => $matomo_url,
						'value'   => ! empty( $matomo_url ),
					];
				} catch ( \Exception $e ) {
					$rows[] = [
						'name'     => 'Matomo URL',
						'comment'  => $e->getMessage(),
						'value'    => 'Failed to fetch the Matomo URL.',
						'is_error' => true,
					];
				}
			}

			// check that yml files are not accessible
			$url    = plugins_url( 'app', MATOMO_ANALYTICS_FILE ) . '/vendor/matomo/device-detector/regexes/bots.yml';
			$result = wp_remote_post(
				$url,
				array(
					'method'    => 'GET',
					'sslverify' => false,
					'timeout'   => 2,
				)
			);
			if ( is_array( $result ) ) {
				$response_code = (int) $result['response']['code'];
				if ( $response_code >= 200 && $response_code < 300 ) {
					$rows[] = [
						'name'       => __( 'YML files should not be accessible', 'matomo' ),
						'value'      => 'warning',
						'comment'    => __( 'The .yml files in the wp-content/plugins/matomo/app/vendor directory are accessible from the internet. This can cause some web security tools to flag your website as suspicious. If you are using Apache, it is probably due to your server configuration disabling the use of .htaccess files. If you are instead using nginx, it is due to your nginx configuration allowing .yml files. You may need to contact your hosting provider to fix this.', 'matomo' ),
						'is_warning' => true,
					];
				}
			}
		}

		$rows[] = [
			'section' => 'Matomo Settings',
		];

		// always show these settings
		$global_settings_always_show = [
			'track_mode',
			'track_ecommerce',
			'track_codeposition',
			'track_api_endpoint',
			'track_js_endpoint',
		];
		foreach ( $global_settings_always_show as $key ) {
			$rows[] = [
				'name'    => ucfirst( str_replace( '_', ' ', $key ) ),
				'value'   => $this->settings->get_global_option( $key ),
				'comment' => '',
			];
		}

		// otherwise show only few customised settings
		// mostly only numeric values and booleans to not eg accidentally show anything that would store a token etc
		// like we don't want to show license key etc
		foreach ( $this->settings->get_customised_global_settings() as $key => $val ) {
			if ( is_numeric( $val ) || is_bool( $val ) || 'track_content' === $key || 'track_user_id' === $key || 'core_version' === $key || 'version_history' === $key || 'mail_history' === $key ) {
				if ( is_array( $val ) ) {
					$val = implode( ', ', $val );
				}

				$rows[] = [
					'name'    => ucfirst( str_replace( '_', ' ', $key ) ),
					'value'   => $val,
					'comment' => '',
				];
			}
		}

		$rows[] = [
			'section' => 'Logs',
		];

		$error_log_entries = $this->logger->get_last_logged_entries();

		if ( ! empty( $error_log_entries ) ) {
			foreach ( $error_log_entries as $error ) {
				if ( ! empty( $install_time )
					&& is_numeric( $install_time )
					&& ! empty( $error['name'] )
					&& ! empty( $error['value'] )
					&& is_numeric( $error['value'] )
					&& 'cron_sync' === $error['name']
					&& $error['value'] < ( $install_time + 300 )
				) {
					// the first sync might right after the installation
					continue;
				}

				// we only consider plugin_updates as errors only if there are still outstanding updates
				$is_plugin_update_error = ! empty( $error['name'] ) && 'plugin_update' === $error['name']
					&& ! empty( $outstanding_updates );

				$skip_plugin_update = ! empty( $error['name'] ) && 'plugin_update' === $error['name']
					&& empty( $outstanding_updates );

				if ( empty( $error['comment'] ) && '0' !== $error['comment'] ) {
					$error['comment'] = '';
				}

				if ( strpos( $error['comment'], '<head>' ) ) {
					$error['comment'] = esc_html( $error['comment'] );
					$error['comment'] = $this->replace_hexadecimal_colors( $error['comment'] );
				}

				$error['value']      = $this->convert_time_to_date( $error['value'], true, false );
				$error['is_warning'] = ! empty( $error['name'] ) && stripos( $error['name'], 'archiv' ) !== false && 'archive_boot' !== $error['name'];
				$error['is_error']   = $is_plugin_update_error;
				if ( $is_plugin_update_error ) {
					$error['comment'] = sprintf( esc_html__( 'Please reach out to us and include the copied system report (%s)<br><br>You can also retry the update manually by clicking in the top on the "Troubleshooting" tab and then clicking on the "Run updater" button.', 'matomo' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://matomo.org/faq/wordpress/how-do-i-troubleshoot-a-failed-database-upgrade-in-matomo-for-wordpress/', esc_html__( 'more info', 'matomo' ) ) ) . $error['comment'];
				} elseif ( $skip_plugin_update ) {
					$error['comment'] = esc_html__( 'As there are no outstanding plugin updates it looks like this log can be ignored.', 'matomo' ) . '<br><br>' . $error['comment'];
				}
				$error['comment'] = matomo_anonymize_value( $error['comment'] );
				$rows[]           = $error;
			}

			foreach ( $error_log_entries as $error ) {
				if ( $suports_async
					&& ! empty( $error['value'] )
					&& is_string( $error['value'] )
					&& strpos( $error['value'], __( 'Your PHP installation appears to be missing the MySQL extension which is required by WordPress.', 'matomo' ) ) > 0
				) {
					$rows[] = [
						'name'     => 'Cli has no MySQL',
						'value'    => true,
						'comment'  => sprintf( esc_html__( 'It looks like MySQL is not available on CLI. Please read our FAQ on how to %s', 'matomo' ), sprintf( ' <a href="%s" target="_blank">%s</a>', 'https://matomo.org/faq/wordpress/how-do-i-fix-the-error-your-php-installation-appears-to-be-missing-the-mysql-extension-which-is-required-by-wordpress-in-matomo-system-report/', esc_html__( 'fix this issue', 'matomo' ) ) ),
						'is_error' => true,
					];
				}
			}
		} else {
			$rows[] = [
				'name'    => __( 'None', 'matomo' ),
				'value'   => '',
				'comment' => '',
			];
		}

		if ( ! WpMatomo::is_safe_mode() ) {
			Bootstrap::do_bootstrap();
			$trackfailures = [];
			try {
				$tracking_failures = new Failures();
				$trackfailures     = $tracking_failures->getAllFailures();
				// phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			} catch ( Exception $e ) {
				// ignored in case not set up yet etc.
			}
			if ( ! empty( $trackfailures ) ) {
				$rows[] = [
					'section' => 'Tracking failures',
				];
				foreach ( $trackfailures as $failure ) {
					$comment = sprintf(
						'Solution: %s<br>More info: %s<br>Date: %s<br>Request URL: %s',
						$failure['solution'],
						$failure['solution_url'],
						$failure['pretty_date_first_occurred'],
						$failure['request_url']
					);
					// do not esc_html the comment: we want the br
					$rows[] = [
						'name'       => $failure['problem'],
						'is_warning' => true,
						'value'      => '',
						'comment'    => $comment,
					];
				}
			}
		}

		return $rows;
	}

	private function had_visits_in_last_days( $num_days ) {
		global $wpdb;

		if ( WpMatomo::is_safe_mode() ) {
			return null;
		}

		$days_in_seconds = $num_days * 86400;

		$prefix_table = $this->db_settings->prefix_table_name( 'log_visit' );

		$suppress_errors = $wpdb->suppress_errors;
		$wpdb->suppress_errors( true );// prevent any of this showing in logs just in case

		try {
			$time = gmdate( 'Y-m-d H:i:s', time() - $days_in_seconds );
			$sql  = $wpdb->prepare( 'SELECT idsite from ' . $prefix_table . ' where visit_last_action_time > %s LIMIT 1', $time );
			$row  = $wpdb->get_var( $sql );
		} catch ( Exception $e ) {
			$row = null;
		}

		$wpdb->suppress_errors( $suppress_errors );
		// we need to differentiate between
		// 0 === had no visit
		// 1 === had visit
		// null === sum error... eg table was not correctly installed
		if ( null !== $row ) {
			$row = ! empty( $row );
		}

		return $row;
	}

	private function convert_time_to_date( $time, $in_blog_timezone, $print_diff = false ) {
		if ( empty( $time ) ) {
			return esc_html__( 'Unknown', 'matomo' );
		}

		$date = gmdate( 'Y-m-d H:i:s', (int) $time );

		if ( $in_blog_timezone ) {
			$date = get_date_from_gmt( $date, 'Y-m-d H:i:s' );
		}

		if ( $print_diff && class_exists( '\Piwik\Metrics\Formatter' ) ) {
			try {
				$formatter = new \Piwik\Metrics\Formatter();
				$date     .= ' (' . $formatter->getPrettyTimeFromSeconds( $time - time(), true, false ) . ')';
			} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
				// empty
			}
		}

		return $date;
	}

	private function add_diagnostic_results( $rows, $results ) {
		foreach ( $results as $result ) {
			$comment = '';
			/** @var DiagnosticResult $result */
			if ( $result->getStatus() !== DiagnosticResult::STATUS_OK ) {
				foreach ( $result->getItems() as $item ) {
					$item_comment = $item->getComment();
					if ( ! empty( $item_comment ) && is_string( $item_comment ) ) {
						if ( stripos( $item_comment, 'core:archive' ) > 0 ) {
							// we only want to keep the first sentence like "	Archiving last ran successfully on Wednesday, January 2, 2019 00:00:00 which is 335 days 20:08:11 ago"
							// but not anything that asks user to set up a cronjob
							$item_comment = substr( $item_comment, 0, stripos( $item_comment, 'core:archive' ) );
							if ( strpos( $item_comment, '.' ) > 0 ) {
								$item_comment = substr( $item_comment, 0, strripos( $item_comment, '.' ) );
							} else {
								$item_comment = 'Archiving hasn\'t run in a while.';
							}
						}
						$comment .= $item_comment . '<br/>';
					}
				}
			}

			$rows[] = [
				'name'       => $result->getLabel(),
				'value'      => $result->getStatus() . ' ' . $result->getLongErrorMessage(),
				'comment'    => $comment,
				'is_warning' => $result->getStatus() === DiagnosticResult::STATUS_WARNING,
				'is_error'   => $result->getStatus() === DiagnosticResult::STATUS_ERROR,
			];
		}

		return $rows;
	}

	private function get_wordpress_info() {
		$is_multi_site      = is_multisite();
		$num_blogs          = 1;
		$is_network_enabled = false;

		if ( $is_multi_site ) {
			if ( function_exists( 'get_blog_count' ) ) {
				$num_blogs = get_blog_count();
			}
			$settings           = new Settings();
			$is_network_enabled = $settings->is_network_enabled();
		}

		$rows   = [];
		$rows[] = [
			'name'  => 'Home URL',
			'value' => home_url(),
		];
		$rows[] = [
			'name'  => 'Site URL',
			'value' => site_url(),
		];
		$rows[] = [
			'name'  => 'WordPress Version',
			'value' => get_bloginfo( 'version' ),
		];
		$rows[] = [
			'name'  => 'Number of blogs',
			'value' => $num_blogs,
		];
		$rows[] = [
			'name'  => 'Multisite Enabled',
			'value' => $is_multi_site,
		];
		$rows[] = [
			'name'  => 'Network Enabled',
			'value' => $is_network_enabled,
		];
		$consts = [
			'WP_DEBUG',
			'WP_DEBUG_DISPLAY',
			'WP_DEBUG_LOG',
			'DISABLE_WP_CRON',
			'FORCE_SSL_ADMIN',
			'WP_CACHE',
			'CONCATENATE_SCRIPTS',
			'COMPRESS_SCRIPTS',
			'COMPRESS_CSS',
			'ENFORCE_GZIP',
			'WP_LOCAL_DEV',
			'WP_CONTENT_URL',
			'WP_CONTENT_DIR',
			'UPLOADS',
			'BLOGUPLOADDIR',
			'DIEONDBERROR',
			'WPLANG',
			'ALTERNATE_WP_CRON',
			'WP_CRON_LOCK_TIMEOUT',
			'WP_DISABLE_FATAL_ERROR_HANDLER',
			'MATOMO_SUPPORT_ASYNC_ARCHIVING',
			'MATOMO_ENABLE_TAG_MANAGER',
			'MATOMO_SUPPRESS_DB_ERRORS',
			'MATOMO_ENABLE_AUTO_UPGRADE',
			'MATOMO_DEBUG',
			'MATOMO_SAFE_MODE',
			'MATOMO_GLOBAL_UPLOAD_DIR',
			'MATOMO_LOGIN_REDIRECT',
		];
		foreach ( $consts as $const ) {
			$rows[] = [
				'name'  => $const,
				'value' => defined( $const ) ? constant( $const ) : '-',
			];
		}

		$rows[] = [
			'name'  => 'Permalink Structure',
			'value' => get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default',
		];

		$rows[] = [
			'name'  => 'Possibly uses symlink',
			'value' => strpos( __DIR__, ABSPATH ) === false && strpos( __DIR__, WP_CONTENT_DIR ) === false,
		];

		$upload_dir = wp_upload_dir();
		$rows[]     = [
			'name'  => 'Upload base url',
			'value' => $upload_dir['baseurl'],
		];

		$rows[] = [
			'name'  => 'Upload base dir',
			'value' => $upload_dir['basedir'],
		];

		$rows[] = [
			'name'  => 'Upload url',
			'value' => $upload_dir['url'],
		];

		foreach ( [ 'upload_path', 'upload_url_path' ] as $option_read ) {
			$rows[] = [
				'name'  => 'Custom ' . $option_read,
				'value' => get_option( $option_read ),
			];
		}

		if ( is_plugin_active( 'wp-piwik/wp-piwik.php' ) ) {
			$rows[] = [
				'name'       => 'Connect Matomo (WP-Matomo) activated',
				'value'      => true,
				'is_warning' => true,
				'comment'    => sprintf( esc_html__( 'It is usually not recommended or needed to run Matomo for WordPress and Connect Matomo at the same time. To learn more about the differences between the two plugins view this %s', 'matomo' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://matomo.org/faq/wordpress/why-are-there-two-different-matomo-for-wordpress-plugins-what-is-the-difference-to-wp-matomo-integration-plugin/', esc_html__( 'URL', 'matomo' ) ) ),
			];

			$mode = get_option( 'wp-piwik_global-piwik_mode' );
			if ( function_exists( 'get_site_option' ) && is_plugin_active_for_network( 'wp-piwik/wp-piwik.php' ) ) {
				$mode = get_site_option( 'wp-piwik_global-piwik_mode' );
			}
			if ( ! empty( $mode ) ) {
				$rows[] = [
					'name'       => 'Connect Matomo mode',
					'value'      => $mode,
					'is_warning' => 'php' === $mode || 'PHP' === $mode,
					'comment'    => esc_html__( 'Connect Matomo is configured in "PHP mode". This is known to cause issues with Matomo for WordPress. We recommend you either deactivate Connect Matomo or you go "Settings => Connect Matomo" and change the "Matomo Mode" in the "Connect to Matomo" section to "Self-hosted HTTP API".', 'matomo' ),
				];
			}
		}

		$compatible_content_dir = matomo_has_compatible_content_dir();
		if ( true === $compatible_content_dir ) {
			$rows[] = [
				'name'  => 'Compatible content directory',
				'value' => true,
			];
		} else {
			$rows[] = [
				'name'       => 'Compatible content directory',
				'value'      => $compatible_content_dir,
				'is_warning' => true,
				'comment'    => esc_html__( 'It looks like you are maybe using a custom WordPress content directory. The Matomo reporting/admin pages might not work. You may be able to workaround this.', 'matomo' ) . ' ' . sprintf( '<a href="%s" target="_blank">%s</a>', 'https://matomo.org/faq/wordpress/how-do-i-make-matomo-for-wordpress-work-when-i-have-a-custom-content-directory/', esc_html__( 'Learn more', 'matomo' ) ),
			];
		}

		$paths = new Paths();
		$paths->get_file_system();

		$filesystem_init_succeeded = $paths->get_host_init_filesystem_succeeded();
		$rows[]                    = [
			'name'       => 'WP_Filesystem Initialized',
			'value'      => $filesystem_init_succeeded,
			'is_warning' => ! $filesystem_init_succeeded,
			'comment'    => $filesystem_init_succeeded
				? null
				: esc_html__( 'The WordPress Filesystem abstraction was not initialized correctly (WP_Filesystem() returned false). This indicates a WordPress or server configuration issue which may cause problems in Matomo and other plugins. To resolve it, contact your hosting provider.', 'matomo' ),
		];

		$system_cron_warning = esc_html__( 'Warning: Your WordPress site does not appear to have a system cron job set up to run the WordPress cron. Matomo uses the WordPress cron to generate reports and to delete temporary data to provide compliance with various privacy regulations (e.g., GDPR).', 'matomo' )
			. '<br/><br/>'
			. esc_html__( 'Without a system cron job set up, these tasks may be run irregularly or infrequently, which could lead to personal data being retained longer than allowed.', 'matomo' )
			. '<br/><br/>'
			. esc_html__( 'To avoid this, we recommend setting up a system cron job to run the WordPress cron regularly. Contact your hosting provider if you require help with this.', 'matomo' );

		$is_system_cron_set_up = defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON;
		$rows[]                = [
			'name'       => 'System Cron Set Up',
			'value'      => $is_system_cron_set_up,
			'is_warning' => ! $is_system_cron_set_up,
			'comment'    => $is_system_cron_set_up ? null : $system_cron_warning,
		];

		return $rows;
	}

	private function add_maxminddb_row( &$rows, $maxmind_db_loaded ) {
		$rows[] = [
			'name'       => esc_html__( 'PHP Maxmind DB extension', 'matomo' ),
			'value'      => $maxmind_db_loaded ? __( 'Loaded', 'matomo' ) : __( 'Not loaded', 'matomo' ),
			'comment'    => $maxmind_db_loaded ? sprintf( esc_html__( 'You may encounter %s', 'matomo' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://matomo.org/faq/troubleshooting/how-do-i-fix-the-error-call-to-undefined-method-maxminddbreadergetwithprefixlen/', esc_html__( 'the following problem', 'matomo' ) ) ) : '',
			'is_warning' => $maxmind_db_loaded,
		];
	}

	private function get_server_info() {
		$rows = [];

		if ( ! empty( $_SERVER['SERVER_SOFTWARE'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$server_software = sanitize_text_field( $_SERVER['SERVER_SOFTWARE'] );
			$rows[]          = [
				'name'  => 'Server Info',
				'value' => $server_software,
			];
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( strpos( $server_software, 'Apache' ) !== false ) {
				$url    = plugins_url( 'app', MATOMO_ANALYTICS_FILE ) . '/index.php';
				$result = wp_remote_post(
					$url,
					array(
						'method'    => 'GET',
						'sslverify' => false,
						'timeout'   => defined( 'MATOMO_LOCAL_ENVIRONMENT' ) && MATOMO_LOCAL_ENVIRONMENT ? 10 : 2,
					)
				);
				if ( is_array( $result ) ) {
					// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
					$file_content = file_get_contents( dirname( MATOMO_ANALYTICS_FILE ) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . '.htaccess' );
					if ( strpos( $file_content, 'AddHandler' ) && ! strpos( $file_content, '# AddHandler' ) ) {
						switch ( (int) $result['response']['code'] ) {
							case 500:
								$value    = __( 'To be confirmed', 'matomo' );
								$comment  = sprintf( esc_html__( 'The AddHandler Apache directive maybe disabled. If you get a 500 error code when accessing Matomo, please read this %s', 'matomo' ), sprintf( '<a href="%s" target="_blank">%s<a/>', 'https://matomo.org/faq/wordpress/how-do-i-fix-the-error-addhandler-not-allowed-here/', esc_html__( 'FAQ', 'matomo' ) ) );
								$is_error = true;
								break;
							default:
								$value    = __( 'Supported', 'matomo' );
								$comment  = '';
								$is_error = false;
						}
						$rows[] = [
							'name'     => 'Apache AddHandler support',
							'value'    => $value,
							'comment'  => $comment,
							'is_error' => $is_error,
						];
					}
				} else {
					$this->logger->log( 'diagnostic check: wp_remove_post to index.php failed' );
				}
			}
		}

		if ( PHP_OS ) {
			$rows[] = [
				'name'  => 'PHP OS',
				'value' => PHP_OS,
			];
		}
		$rows[] = [
			'name'  => 'PHP Version',
			'value' => phpversion(),
		];
		$rows[] = [
			'name'  => 'PHP SAPI',
			'value' => php_sapi_name(),
		];
		if ( defined( 'PHP_BINARY' ) && PHP_BINARY ) {
			$rows[] = [
				'name'  => 'PHP Binary Name',
				'value' => PHP_BINARY,
			];
		}

		$this->add_maxminddb_row( $rows, extension_loaded( 'maxminddb' ) );

		// we report error reporting before matomo bootstraped and after to see if Matomo changed it successfully etc
		$rows[] = [
			'name'  => 'PHP Error Reporting',
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting
			'value' => $this->initial_error_reporting . ' After bootstrap: ' . @error_reporting(),
		];

		if ( ! empty( $this->get_php_cli_binary() ) ) {
			$rows[] = [
				'name'  => 'PHP Found Binary',
				'value' => $this->get_php_cli_binary(),
			];
		}
		$rows[] = [
			'name'  => 'Timezone',
			'value' => date_default_timezone_get(),
		];
		if ( function_exists( 'wp_timezone_string' ) ) {
			$rows[] = [
				'name'  => 'WP timezone',
				'value' => wp_timezone_string(),
			];
		}
		$rows[] = [
			'name'  => 'Locale',
			'value' => get_locale(),
		];
		if ( function_exists( 'get_user_locale' ) ) {
			$rows[] = [
				'name'  => 'User Locale',
				'value' => get_user_locale(),
			];
		}

		$rows[] = [
			'name'    => 'Memory Limit',
			'value'   => @ini_get( 'memory_limit' ),
			'comment' => sprintf( esc_html__( 'At least %1$dMB recommended. Depending on your traffic %2$dMB or more may be needed.', 'matomo' ), 128, 256 ),
		];

		$rows[] = [
			'name'    => 'WP Memory Limit',
			'value'   => defined( 'WP_MEMORY_LIMIT' ) ? WP_MEMORY_LIMIT : '',
			'comment' => '',
		];

		$rows[] = [
			'name'    => 'WP Max Memory Limit',
			'value'   => defined( 'WP_MAX_MEMORY_LIMIT' ) ? WP_MAX_MEMORY_LIMIT : '',
			'comment' => '',
		];

		if ( function_exists( 'timezone_version_get' ) ) {
			$rows[] = [
				'name'  => 'Timezone version',
				'value' => timezone_version_get(),
			];
		}

		$rows[] = [
			'name'  => 'Time',
			'value' => time(),
		];

		$rows[] = [
			'name'  => 'Max Execution Time',
			'value' => ini_get( 'max_execution_time' ),
		];
		$rows[] = [
			'name'  => 'Max Post Size',
			'value' => ini_get( 'post_max_size' ),
		];
		$rows[] = [
			'name'  => 'Max Upload Size',
			'value' => wp_max_upload_size(),
		];
		$rows[] = [
			'name'  => 'Max Input Vars',
			'value' => ini_get( 'max_input_vars' ),
		];

		$disabled_functions = ini_get( 'disable_functions' );
		$rows[]             = [
			'name'    => 'Disabled PHP functions',
			'value'   => ! empty( $disabled_functions ),
			'comment' => ! empty( $disabled_functions ) ? $disabled_functions : '',
		];

		// try to set it before checking, since we try to set it in /app/bootstrap.php
		@ini_set( 'zlib.output_compression', false );

		$zlib_compression = ini_get( 'zlib.output_compression' );
		$row              = [
			'name'  => 'zlib.output_compression is off',
			'value' => '1' !== $zlib_compression,
		];

		if ( '1' === $zlib_compression ) {
			$row['is_error'] = true;
			$row['comment']  = esc_html__( 'You need to set "zlib.output_compression" in your php.ini to "Off".', 'matomo' );
		}
		$rows[] = $row;

		if ( function_exists( 'curl_version' ) ) {
			$curl_version = curl_version();
			$curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
			$rows[]       = [
				'name'  => 'Curl Version',
				'value' => $curl_version,
			];
		}

		$suhosin_installed = ( extension_loaded( 'suhosin' ) || ( defined( 'SUHOSIN_PATCH' ) && constant( 'SUHOSIN_PATCH' ) ) );
		$rows[]            = [
			'name'    => 'Suhosin installed',
			'value'   => ! empty( $suhosin_installed ),
			'comment' => '',
		];

		return $rows;
	}

	private function get_browser_info() {
		$rows = [];

		if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$rows[] = [
				'name'    => 'Browser',
				'value'   => '',
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				'comment' => sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ),
			];
		}
		if ( ! WpMatomo::is_safe_mode() ) {
			Bootstrap::do_bootstrap();
			try {
				if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
					$detector = StaticContainer::get( DeviceDetectorFactory::class )->makeInstance( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) );
					$client   = $detector->getClient();
					if ( ! empty( $client['name'] ) && 'Microsoft Edge' === $client['name'] && (int) $client['version'] >= 85 ) {
						$rows[] = [
							'name'       => 'Browser Compatibility',
							'is_warning' => true,
							'value'      => 'Yes',
							'comment'    => esc_html__( 'Because you are using MS Edge browser, you may see a warning like "This site has been reported as unsafe" from "Microsoft Defender SmartScreen" when you view the Matomo Reporting, Admin or Tag Manager page. This is a false alert and you can safely ignore this warning by clicking on the icon next to the URL (in the address bar) and choosing either "Report as safe" (preferred) or "Show unsafe content". We are hoping to get this false warning removed in the future.', 'matomo' ),
						];
					}
				}
			} catch ( Exception $e ) {
				$rows[] = [
					'name'     => 'Browser Compatibility',
					'is_error' => true,
					'value'    => 'Failed to use Device Detector.',
					'comment'  => $e->getMessage(),
				];
			}

			$rows[] = [
				'name'    => 'Language',
				'value'   => Common::getBrowserLanguage(),
				'comment' => '',
			];
		}

		return $rows;
	}

	private function get_db_info() {
		global $wpdb;
		$rows = [];

		$rows[] = [
			'name'    => 'MySQL Version',
			'value'   => ! empty( $wpdb->is_mysql ) ? $wpdb->db_version() : '',
			'comment' => '',
		];

		$rows[] = [
			'name'    => 'Mysqli Connect',
			'value'   => function_exists( 'mysqli_connect' ),
			'comment' => '',
		];
		$rows[] = [
			'name'    => 'Force MySQL over Mysqli',
			'value'   => defined( 'WP_USE_EXT_MYSQL' ) && WP_USE_EXT_MYSQL,
			'comment' => '',
		];

		$rows[] = [
			'name'  => 'DB Prefix',
			'value' => $wpdb->prefix,
		];

		$rows[] = [
			'name'  => 'DB CHARSET',
			'value' => defined( 'DB_CHARSET' ) ? DB_CHARSET : '',
		];

		$rows[] = [
			'name'  => 'DB COLLATE',
			'value' => defined( 'DB_COLLATE' ) ? DB_COLLATE : '',
		];

		$rows[] = [
			'name'  => 'SHOW ERRORS',
			'value' => ! empty( $wpdb->show_errors ),
		];

		$rows[] = [
			'name'  => 'SUPPRESS ERRORS',
			'value' => ! empty( $wpdb->suppress_errors ),
		];

		if ( method_exists( $wpdb, 'parse_db_host' ) ) {
			$host_data = $wpdb->parse_db_host( DB_HOST );
			if ( $host_data ) {
				list( $host, $port, $socket, $is_ipv6 ) = $host_data;
			}

			$rows[] = [
				'name'  => 'Uses Socket',
				'value' => ! empty( $socket ),
			];
			$rows[] = [
				'name'  => 'Uses IPv6',
				'value' => ! empty( $is_ipv6 ),
			];
		}

		$rows[] = [
			'name'  => 'Matomo tables found',
			'value' => $this->get_num_matomo_tables(),
		];

		$missing_tables     = $this->get_missing_tables();
		$has_missing_tables = ( count( $missing_tables ) > 0 );
		$rows[]             = [
			'name'     => 'DB tables exist',
			'value'    => ( ! $has_missing_tables ),
			'comment'  => $has_missing_tables ? sprintf( esc_html__( 'Some tables may be missing: %s', 'matomo' ), implode( ', ', $missing_tables ) ) : '',
			'is_error' => $has_missing_tables,
		];

		foreach ( [ 'user', 'site' ] as $table ) {
			$row = [
				'name'  => 'Matomo ' . $table . 's found',
				'value' => $this->get_num_entries_in_table( $table ),
			];
			if ( 'site' === $table ) {
				if ( ( ! is_multisite() ) && ( $row['value'] > 1 ) ) {
					$row['is_warning'] = true;
					$row['comment']    = esc_html__( 'There is an error in your Matomo records. Please contact wordpress@matomo.org', 'matomo' );
				}
			}
			$rows[] = $row;
		}

		$grants = $this->get_db_grants();

		// we only show these grants for security reasons as only they are needed and we don't need to know any other ones
		$needed_grants = [
			'SELECT',
			'INSERT',
			'UPDATE',
			'INDEX',
			'DELETE',
			'CREATE',
			'DROP',
			'ALTER',
			'CREATE TEMPORARY TABLES',
			'LOCK TABLES',
		];
		if ( in_array( 'ALL PRIVILEGES', $grants, true ) ) {
			// ALL PRIVILEGES may be used pre MySQL 8.0
			$grants = $needed_grants;
		}

		$grants_missing = array_diff( $needed_grants, $grants );

		if ( empty( $grants )
			|| ! is_array( $grants )
			|| count( $grants_missing ) === count( $needed_grants )
		) {
			$rows[] = [
				'name'       => esc_html__( 'Required permissions', 'matomo' ),
				'value'      => esc_html__( 'Failed to detect granted permissions', 'matomo' ),
				'comment'    => esc_html__( 'Please check your MySQL user has these permissions (grants):', 'matomo' ) . '<br />' . implode( ', ', $needed_grants ),
				'is_warning' => false,
			];
		} else {
			if ( ! empty( $grants_missing ) ) {
				$rows[] = [
					'name'       => esc_html__( 'Required permissions', 'matomo' ),
					'value'      => esc_html__( 'Error', 'matomo' ),
					'comment'    => esc_html__( 'Missing permissions', 'matomo' ) . ': ' . implode( ', ', $grants_missing ) . '. ' . esc_html__( 'Please check if any of these MySQL permission (grants) are missing and add them if needed.', 'matomo' ) . ' ' . sprintf( '<a href="https://matomo.org/faq/troubleshooting/how-do-i-check-if-my-mysql-user-has-all-required-grants/" target="_blank">%s</a>', __( 'Learn more', 'matomo' ) ),
					'is_warning' => true,
				];
			} else {
				$rows[] = [
					'name'       => esc_html__( 'Required permissions', 'matomo' ),
					'value'      => esc_html__( 'OK', 'matomo' ),
					'comment'    => '',
					'is_warning' => false,
				];
			}
		}

		return $rows;
	}

	/**
	 * @return string[]
	 */
	public function get_missing_tables() {
		global $wpdb;

		$required_matomo_tables = $this->db_settings->get_matomo_tables();
		$required_matomo_tables = array_map( [ $this->db_settings, 'prefix_table_name' ], $required_matomo_tables );

		$existing_tables = [];
		try {
			$prefix          = $this->db_settings->prefix_table_name( '' );
			$existing_tables = $wpdb->get_col( 'SHOW TABLES LIKE "' . $prefix . '%"' );
		} catch ( Exception $e ) {
			$this->logger->log( 'no show tables: ' . $e->getMessage() );
		}

		return array_diff( $required_matomo_tables, $existing_tables );
	}

	private function get_num_entries_in_table( $table ) {
		global $wpdb;

		$prefix = $this->db_settings->prefix_table_name( $table );

		$results = null;
		try {
			$results = $wpdb->get_var( 'select count(*) from ' . $prefix );
		} catch ( Exception $e ) {
			$this->logger->log( 'no count(*): ' . $e->getMessage() );
		}

		if ( isset( $results ) && is_numeric( $results ) ) {
			return $results;
		}

		return 'table not exists';
	}

	private function get_num_matomo_tables() {
		global $wpdb;

		$prefix = $this->db_settings->prefix_table_name( '' );

		$results = null;
		try {
			$results = $wpdb->get_results( 'show tables like "' . $prefix . '%"' );
		} catch ( Exception $e ) {
			$this->logger->log( 'no show tables: ' . $e->getMessage() );
		}

		if ( is_array( $results ) ) {
			return count( $results );
		}

		return 'show tables not working';
	}

	private function get_db_grants() {
		global $wpdb;

		$suppress_errors = $wpdb->suppress_errors;
		$wpdb->suppress_errors( true );// prevent any of this showing in logs just in case

		try {
			$values = $wpdb->get_results( 'SHOW GRANTS', ARRAY_N );
		} catch ( Exception $e ) {
			// We ignore any possible error in case of permission or not supported etc.
			$values = [];
		}

		$wpdb->suppress_errors( $suppress_errors );

		$grants = [];
		foreach ( $values as $index => $value ) {
			if ( empty( $value[0] ) || ! is_string( $value[0] ) ) {
				continue;
			}

			if ( stripos( $value[0], 'ALL PRIVILEGES' ) !== false ) {
				return [ 'ALL PRIVILEGES' ]; // the split on empty string wouldn't work otherwise
			}

			foreach ( [ ' ON ', ' TO ', ' IDENTIFIED ', ' BY ' ] as $keyword ) {
				if ( stripos( $values[ $index ][0], $keyword ) !== false ) {
					// make sure to never show by any accident a db user or password by cutting anything after on/to
					$values[ $index ][0] = substr( $values[ $index ][0], 0, stripos( $values[ $index ][0], $keyword ) );
				}
				if ( stripos( $values[ $index ][0], 'GRANT' ) !== false ) {
					// otherwise we end up having "grant select"... instead of just "select"
					$values[ $index ][0] = substr( $values[ $index ][0], stripos( $values[ $index ][0], 'GRANT' ) + 5 );
				}
			}
			// make sure to never show by any accident a db user or password
			$values[ $index ][0] = str_replace(
				[ DB_USER, DB_PASSWORD ],
				[
					'DB_USER',
					'DB_PASS',
				],
				$values[ $index ][0]
			);

			$grants = array_merge( $grants, explode( ',', $values[ $index ][0] ) );
		}
		$grants = array_map( 'trim', $grants );
		$grants = array_map( 'strtoupper', $grants );
		$grants = array_unique( $grants );

		return $grants;
	}

	/**
	 * @return string[]
	 */
	private function get_actives_plugins() {
		$active_plugins = get_option( 'active_plugins', [] );
		if ( ! empty( $active_plugins ) && is_array( $active_plugins ) ) {
			$plugins        = get_plugins();
			$active_plugins = array_map(
				function ( $active_plugin ) use ( $plugins ) {
					$plugin_version = isset( $plugins[ $active_plugin ]['Version'] ) ? $plugins[ $active_plugin ]['Version'] : null;
					$result_suffix  = $plugin_version ? ( ':' . $plugin_version ) : '';
					$parts          = explode( '/', trim( $active_plugin ) );
					return trim( $parts[0] ) . $result_suffix;
				},
				$active_plugins
			);
		}

		return $active_plugins;
	}

	private function get_plugins_info() {
		$rows       = [];
		$mu_plugins = get_mu_plugins();

		if ( ! empty( $mu_plugins ) ) {
			$rows[] = [
				'section' => 'MU Plugins',
			];

			foreach ( $mu_plugins as $mu_pin ) {
				$comment = '';
				if ( ! empty( $plugin['Network'] ) ) {
					$comment = esc_html__( 'Network enabled', 'matomo' );
				}
				$rows[] = [
					'name'    => $mu_pin['Name'],
					'value'   => $mu_pin['Version'],
					'comment' => $comment,
				];
			}

			$rows[] = [
				'section' => 'Plugins',
			];
		}

		$plugins = get_plugins();

		foreach ( $plugins as $plugin_file => $plugin ) {
			$comment = '';
			if ( ! empty( $plugin['Network'] ) ) {
				$comment = esc_html__( 'Network enabled', 'matomo' );
			}

			if ( strpos( $plugin_file, 'ninjafirewall/' ) === 0 ) {
				$comment .= '<br/><br/>' . esc_html__( 'We noticed you are using Matomo with Ninja Firewall. This can result in Matomo cache file changes showing up in Ninja Firewall which likely undesired.', 'matomo' ) . '
	<a href="https://matomo.org/faq/wordpress/how-do-i-prevent-matomo-cache-file-changes-to-show-up-in-ninja-firewall/" rel="noreferrer noopener" target="_blank">' . esc_html__( 'Read our FAQ to learn how to prevent these entries.', 'matomo' ) . '</a>';
			}

			$rows[] = [
				'name'    => $plugin['Name'],
				'value'   => $plugin['Version'],
				'comment' => $comment,
			];
		}

		$active_plugins_with_version = $this->get_actives_plugins();

		if ( ! empty( $active_plugins_with_version ) && is_array( $active_plugins_with_version ) ) {
			$rows[] = [
				'name'    => 'Active Plugins',
				'value'   => count( $active_plugins_with_version ),
				'comment' => implode( ' ', $active_plugins_with_version ),
			];

			$active_plugins = array_map(
				function ( $plugin_with_version ) {
					$parts = explode( ':', $plugin_with_version );
					return $parts[0];
				},
				$active_plugins_with_version
			);

			$used_not_compatible = array_intersect( $active_plugins, $this->not_compatible_plugins );
			if ( ! empty( $used_not_compatible ) ) {
				$additional_comment = '';
				if ( count( $used_not_compatible ) ) {
					$rows[] = [
						'name'       => __( 'Not compatible plugins', 'matomo' ),
						'value'      => count( $used_not_compatible ),
						'comment'    => implode( ', ', $used_not_compatible ) . '<br><br>' . sprintf( esc_html__( 'Matomo may work fine when using these plugins but there may be some issues. For more information %1$sSee %2$s', 'matomo' ), '<br/>', sprintf( '<a href="%s" target="_blank">%s</a>', 'https://matomo.org/faq/wordpress/which-plugins-is-matomo-for-wordpress-known-to-be-not-compatible-with/', esc_html__( 'this FAQ', 'matomo' ) ) ) . $additional_comment,
						'is_warning' => true,
						'is_error'   => false,
					];
				}
			}
		}

		$rows[] = [
			'name'    => 'Theme',
			'value'   => function_exists( 'get_template' ) ? get_template() : '',
			'comment' => get_option( 'stylesheet' ),
		];

		if ( is_plugin_active( 'better-wp-security/better-wp-security.php' ) ) {
			if ( class_exists( 'ITSEC_Modules' ) ) {
				if ( method_exists( '\ITSEC_Modules', 'get_setting' ) ) {
					$input = ITSEC_Modules::get_settings( 'system-tweaks' );
					// old plugin versions
					$long_url_strings_options = [ 'long_url_strings', 'st_longurl' ];
					$long_url_strings_enabled = false;
					foreach ( $long_url_strings_options as $option ) {
						if ( isset( $input[ $option ] ) && $input[ $option ] ) {
							$long_url_strings_enabled = true;
						}
					}
					if ( $long_url_strings_enabled ) {
						$rows[] = [
							'name'     => "iThemes Security 'Long URLs' Enabled",
							'value'    => true,
							'comment'  => esc_html__( 'Tracking might not work because it looks like you have Long URLs disabled in iThemes Security. To fix this please contact ithemes security support.', 'matomo' ),
							'is_error' => true,
						];
					}
					if ( $input['plugins_php'] ) {
						$rows[] = [
							'name'     => "iThemes Security 'Disable PHP in plugins' Enabled",
							'value'    => true,
							'comment'  => esc_html__( 'You have disabled the PHP usage in the plugins folder from your ithemes security plugin. Matomo won\'t work in this configuration. You must uncheck the checkbox "Security > Settings > Advanced > System tweaks > Disable PHP in plugins."', 'matomo' ),
							'is_error' => true,
						];
					}
				}
			}
		}

		if ( is_plugin_active( 'secupress/secupress.php' ) ) {
			if ( function_exists( 'secupress_is_submodule_active' ) ) {
				$blocked_methods = (int) secupress_is_submodule_active( 'firewall', 'request-methods-header' );
				if ( $blocked_methods ) {
					if ( ! defined( 'MATOMO_SUPPORT_ASYNC_ARCHIVING' ) || MATOMO_SUPPORT_ASYNC_ARCHIVING ) {
						$rows[] = [
							'name'     => "Secupress 'Block Bad Request Methods' Enabled",
							'value'    => true,
							'comment'  => esc_html__( "If reports aren't being generated then you may need to disable the feature \"Firewall -> Block Bad Request Methods\" in SecuPress (if it is enabled) or add the following line to your \"wp-config.php\"", 'matomo' ) . ": <br><code>define( 'MATOMO_SUPPORT_ASYNC_ARCHIVING', false );</code>.",
							'is_error' => true,
						];
					}
				}
			}
		}

		return $rows;
	}

	/**
	 * Convert the hexadecimal colors in the content into their rgb values
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	private function replace_hexadecimal_colors( $content ) {
		$matches = array();
		if ( preg_match_all( '/ (#(([a-f0-9]{8})|([a-f0-9]{4}[ ;])))/i', $content, $matches ) ) {
			foreach ( $matches[1] as $hexadecimal_color ) {
				switch ( strlen( $hexadecimal_color ) ) {
					case 9:
						list( $r, $g, $b, $a ) = sscanf( $hexadecimal_color, '#%02x%02x%02x%02x' );
						break;
					case 6:
						$hexadecimal_color     = substr( $hexadecimal_color, 0, 5 );
						list( $r, $g, $b, $a ) = sscanf( $hexadecimal_color, '#%01x%01x%01x%01x' );
						break;
				}
				$content = str_replace( $hexadecimal_color, 'rgb(' . $r . ',' . $g . ',' . $b . ',' . $a . ')', $content );
			}
		}
		if ( preg_match_all( '/ (#(([a-f0-9]{6})|([a-f0-9]{3})))/i', $content, $matches ) ) {
			foreach ( $matches[1] as $hexadecimal_color ) {
				switch ( strlen( $hexadecimal_color ) ) {
					case 7:
						list( $r, $g, $b ) = sscanf( $hexadecimal_color, '#%02x%02x%02x' );
						break;
					case 4:
						list( $r, $g, $b ) = sscanf( $hexadecimal_color, '#%01x%01x%01x' );
						break;
				}
				$content = str_replace( $hexadecimal_color, 'rgb(' . $r . ',' . $g . ',' . $b . ')', $content );
			}
		}

		return $content;
	}

	private function get_abs_path_to_plugin() {
		if ( is_dir( ABSPATH . '/wp-content/plugins/matomo' ) ) {
			return ABSPATH . '/wp-content/plugins/matomo';
		} elseif ( is_dir( ABSPATH . '/wp-content/mu-plugins/matomo' ) ) {
			return ABSPATH . '/wp-content/mu-plugins/matomo';
		} else {
			return __DIR__ . '/../../..';
		}
	}

	private function find_wp_load_path() {
		$abs_path   = rtrim( ABSPATH, '/' );
		$search_dir = $this->get_abs_path_to_plugin();

		while ( ! is_file( $search_dir . '/wp-load.php' ) && strpos( $search_dir, $abs_path ) === 0 ) {
			$search_dir = dirname( $search_dir );
		}

		if ( strpos( $search_dir, $abs_path ) !== 0 ) {
			return null;
		}

		return $search_dir . '/wp-load.php';
	}

	private function get_php_cli_binary() {
		if ( ! $this->binary && ! WpMatomo::is_safe_mode() ) {
			Bootstrap::do_bootstrap();
			$cli_php      = new CliPhp();
			$this->binary = $cli_php->findPhpBinary();
		}

		return $this->binary;
	}

	private function set_errors_present_transient( $matomo_tables ) {
		$cache_key = $this->get_errors_present_cache_key();

		$cache_value = false;
		foreach ( $matomo_tables as $report_table ) {
			foreach ( $report_table['rows'] as $row ) {
				if ( ! empty( $row['is_error'] ) || ! empty( $row['is_warning'] ) ) {
					$cache_value = true;
				}
			}
		}

		set_site_transient( $cache_key, (int) $cache_value, WEEK_IN_SECONDS );

		return $cache_value;
	}

	private function get_errors_present_cache_key() {
		return 'matomo_system_report_has_errors';
	}
}
