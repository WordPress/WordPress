<?php

namespace Elementor\Core\Base;

use Elementor\Core\Admin\Admin_Notices;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class DB_Upgrades_Manager extends Background_Task_Manager {
	protected $current_version = null;
	protected $query_limit = 100;

	abstract public function get_new_version();
	abstract public function get_version_option_name();
	abstract public function get_upgrades_class();
	abstract public function get_updater_label();

	public function get_task_runner_class() {
		return 'Elementor\Core\Upgrade\Updater';
	}

	public function get_query_limit() {
		return $this->query_limit;
	}

	public function set_query_limit( $limit ) {
		$this->query_limit = $limit;
	}

	public function get_current_version() {
		if ( null === $this->current_version ) {
			$this->current_version = get_option( $this->get_version_option_name() );
		}

		return $this->current_version;
	}

	public function should_upgrade() {
		$current_version = $this->get_current_version();

		// It's a new install.
		if ( ! $current_version ) {
			$this->update_db_version();
			return false;
		}

		return version_compare( $this->get_new_version(), $current_version, '>' );
	}

	public function on_runner_start() {
		parent::on_runner_start();

		if ( ! defined( 'IS_ELEMENTOR_UPGRADE' ) ) {
			define( 'IS_ELEMENTOR_UPGRADE', true );
		}
	}

	public function on_runner_complete( $did_tasks = false ) {
		$logger = Plugin::$instance->logger->get_logger();

		$logger->info( 'Elementor data updater process has been completed.', [
			'meta' => [
				'plugin' => $this->get_plugin_label(),
				'from' => $this->current_version,
				'to' => $this->get_new_version(),
			],
		] );

		$this->clear_cache();

		$this->update_db_version();

		if ( $did_tasks ) {
			$this->add_flag( 'completed' );
		}
	}

	protected function clear_cache() {
		Plugin::$instance->files_manager->clear_cache();
	}

	public function admin_notice_start_upgrade() {
		/**
		 * @var Admin_Notices $admin_notices
		 */
		$admin_notices = Plugin::$instance->admin->get_component( 'admin-notices' );

		$options = [
			'title' => $this->get_updater_label(),
			'description' => esc_html__( 'Your site database needs to be updated to the latest version.', 'elementor' ),
			'type' => 'error',
			'icon' => false,
			'button' => [
				'text' => esc_html__( 'Update Now', 'elementor' ),
				'url' => $this->get_start_action_url(),
				'class' => 'e-button e-button--cta',
			],
		];

		$admin_notices->print_admin_notice( $options );
	}

	public function admin_notice_upgrade_is_running() {
		/**
		 * @var Admin_Notices $admin_notices
		 */
		$admin_notices = Plugin::$instance->admin->get_component( 'admin-notices' );

		$options = [
			'title' => $this->get_updater_label(),
			'description' => esc_html__( 'Database update process is running in the background. Taking a while?', 'elementor' ),
			'type' => 'warning',
			'icon' => false,
			'button' => [
				'text' => esc_html__( 'Click here to run it now', 'elementor' ),
				'url' => $this->get_continue_action_url(),
				'class' => 'e-button e-button--primary',
			],
		];

		$admin_notices->print_admin_notice( $options );
	}

	public function admin_notice_upgrade_is_completed() {
		$this->delete_flag( 'completed' );

		$message = esc_html__( 'The database update process is now complete. Thank you for updating to the latest version!', 'elementor' );

		/**
		 * @var Admin_Notices $admin_notices
		 */
		$admin_notices = Plugin::$instance->admin->get_component( 'admin-notices' );

		$options = [
			'description' => '<b>' . $this->get_updater_label() . '</b> - ' . $message,
			'type' => 'success',
			'icon' => false,
		];

		$admin_notices->print_admin_notice( $options );
	}

	/**
	 * @access protected
	 */
	protected function start_run() {
		$updater = $this->get_task_runner();

		if ( $updater->is_running() ) {
			return;
		}

		$upgrade_callbacks = $this->get_upgrade_callbacks();

		if ( empty( $upgrade_callbacks ) ) {
			$this->on_runner_complete();
			return;
		}

		$this->clear_cache();

		foreach ( $upgrade_callbacks as $callback ) {
			$updater->push_to_queue( [
				'callback' => $callback,
			] );
		}

		$updater->save()->dispatch();

		Plugin::$instance->logger->get_logger()->info( 'Elementor data updater process has been queued.', [
			'meta' => [
				'plugin' => $this->get_plugin_label(),
				'from' => $this->current_version,
				'to' => $this->get_new_version(),
			],
		] );
	}

	protected function update_db_version() {
		update_option( $this->get_version_option_name(), $this->get_new_version() );
	}

	public function get_upgrade_callbacks() {
		$prefix = '_v_';
		$upgrades_class = $this->get_upgrades_class();
		$upgrades_reflection = new \ReflectionClass( $upgrades_class );

		$callbacks = [];

		foreach ( $upgrades_reflection->getMethods() as $method ) {
			$method_name = $method->getName();

			if ( '_on_each_version' === $method_name ) {
				$callbacks[] = [ $upgrades_class, $method_name ];
				continue;
			}

			if ( false === strpos( $method_name, $prefix ) ) {
				continue;
			}

			if ( ! preg_match_all( "/$prefix(\d+_\d+_\d+)/", $method_name, $matches ) ) {
				continue;
			}

			$method_version = str_replace( '_', '.', $matches[1][0] );

			if ( ! version_compare( $method_version, $this->current_version, '>' ) ) {
				continue;
			}

			$callbacks[] = [ $upgrades_class, $method_name ];
		}

		return $callbacks;
	}

	public function __construct() {
		// If upgrade is completed - show the notice only for admins.
		// Note: in this case `should_upgrade` returns false, because it's already upgraded.
		if ( is_admin() && current_user_can( 'update_plugins' ) && $this->get_flag( 'completed' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_upgrade_is_completed' ] );
		}

		if ( ! $this->should_upgrade() ) {
			return;
		}

		$updater = $this->get_task_runner();

		$this->start_run();

		if ( $updater->is_running() && current_user_can( 'update_plugins' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_upgrade_is_running' ] );
		}

		parent::__construct();
	}
}
