<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo;

use Piwik\Config;
use WpMatomo\Admin\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}
/**
 * We need to access db not cache
 * phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
 * phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
 *
 * Table names management
 * phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
 * phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
 */
class Uninstaller {

	/**
	 * @var Logger
	 */
	private $logger;

	public function __construct() {
		$this->logger = self::make_logger();
	}

	private static function make_logger() {
		return new Logger();
	}

	public function uninstall( $should_remove_all_data ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$this->uninstall_multisite( $should_remove_all_data );
		} else {
			$this->uninstall_blog( $should_remove_all_data );
		}

		do_action( 'matomo_uninstall', $should_remove_all_data );
	}

	public function uninstall_blog( $should_remove_all_data ) {
		$this->logger->log( 'Matomo is now uninstalling blogId ' . get_current_blog_id() );

		$settings = new Settings();

		$tasks = new ScheduledTasks( $settings );
		$tasks->uninstall();

		$roles = new Roles( $settings );
		$roles->uninstall();

		$dashboard = new Dashboard();
		$dashboard->uninstall();

		$paths = new Paths();

		if ( $should_remove_all_data ) {
			$this->logger->log( 'Matomo is forced to remove all data' );

			$settings->uninstall();

			$this->drop_tables();

			$site = new Site();
			$site->uninstall();

			$site = new User();
			$site->uninstall();

			$paths->uninstall();
		} else {
			$paths->clear_cache_dir();
		}

		do_action( 'matomo_uninstall_blog', $should_remove_all_data );

		$this->logger->log( 'Matomo has finished uninstalling ' . get_current_blog_id() );
	}

	public static function uninstall_options( $prefix ) {
		global $wpdb;

		self::make_logger()->log( 'Removing options with prefix ' . $prefix );
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '" . $prefix . "%';" );

		wp_cache_flush();
	}

	public static function uninstall_site_meta( $prefix ) {
		global $wpdb;

		if ( ! empty( $wpdb->sitemeta ) ) {
			// multisite
			self::make_logger()->log( 'Removing sitemeta with prefix ' . $prefix );
			$wpdb->query( "DELETE FROM $wpdb->sitemeta WHERE meta_key LIKE '" . $prefix . "%';" );

			wp_cache_flush();
		}

		// not multisite
		self::uninstall_options( $prefix );
	}

	public static function uninstall_user_meta( $prefix ) {
		global $wpdb;

		if ( ! empty( $wpdb->usermeta ) ) {
			self::make_logger()->log( 'Removing usermeta with prefix ' . $prefix );
			$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key LIKE '" . $prefix . "%';" );

			wp_cache_flush();
		}
	}

	public function uninstall_multisite( $should_remove_all_data ) {
		global $wpdb;

		$this->logger->log( 'Matomo is now uninstalling all blogs: ' . (int) $should_remove_all_data );

		$blogs = $wpdb->get_results( 'SELECT blog_id, deleted FROM ' . $wpdb->blogs . ' ORDER BY blog_id', ARRAY_A );

		if ( is_array( $blogs ) ) {
			foreach ( $blogs as $blog ) {
				if ( 1 === (int) $blog['deleted'] ) {
					continue;
				}

				switch_to_blog( $blog['blog_id'] );

				$this->uninstall_blog( $should_remove_all_data );

				restore_current_blog();
			}
		}
	}

	private function drop_tables() {
		global $wpdb;

		$db_settings      = new \WpMatomo\Db\Settings();
		$installed_tables = $db_settings->get_installed_matomo_tables();
		$this->logger->log( sprintf( 'Matomo will now drop %s matomo tables', count( $installed_tables ) ) );

		foreach ( $installed_tables as $table_name ) {
			// temporary table are used in tests and just making sure they are being removed
			// $wpdb->query( "DROP TEMPORARY TABLE IF EXISTS `$tableName`" );
			// two spaces between drop and table so it won't be replaced in WP tests
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
			$wpdb->query( "DROP TABLE IF EXISTS `$table_name`" );
		}
	}
}
