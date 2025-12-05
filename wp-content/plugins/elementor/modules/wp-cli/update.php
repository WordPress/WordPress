<?php
namespace Elementor\Modules\WpCli;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Page Builder cli tools.
 */
class Update extends \WP_CLI_Command {

	/**
	 * Update the DB after plugin upgrade.
	 *
	 * [--network]
	 *      Update DB in all the sites in the network.
	 *
	 * [--force]
	 *      Force update even if it's looks like that update is in progress.
	 *
	 *
	 * ## EXAMPLES
	 *
	 *  1. wp elementor update db
	 *      - This will Upgrade the DB if needed.
	 *
	 *  2. wp elementor update db --force
	 *      - This will Upgrade the DB even if another process is running.
	 *
	 *  3. wp elementor update db --network
	 *      - This will Upgrade the DB for each site in the network if needed.
	 *
	 * @since  2.4.0
	 * @access public
	 *
	 * @param $args
	 * @param $assoc_args
	 */
	public function db( $args, $assoc_args ) {
		$network = ! empty( $assoc_args['network'] ) && is_multisite();

		if ( $network ) {
			$blog_ids = get_sites( [
				'fields' => 'ids',
				'number' => 0,
			] );

			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );

				\WP_CLI::line( 'Site #' . $blog_id . ' - ' . get_option( 'blogname' ) );

				$this->do_db_upgrade( $assoc_args );

				\WP_CLI::success( 'Done! - ' . get_option( 'home' ) );

				restore_current_blog();
			}
		} else {
			$this->do_db_upgrade( $assoc_args );
		}
	}

	protected function get_update_db_manager_class() {
		return '\Elementor\Core\Upgrade\Manager';
	}

	protected function do_db_upgrade( $assoc_args ) {
		$manager_class = $this->get_update_db_manager_class();

		/** @var \Elementor\Core\Upgrade\Manager $manager */
		$manager = new $manager_class();

		$updater = $manager->get_task_runner();

		if ( $updater->is_process_locked() && empty( $assoc_args['force'] ) ) {
			\WP_CLI::warning( 'Oops! Process is already running. Use --force to force run.' );
			return;
		}

		if ( ! $manager->should_upgrade() ) {
			\WP_CLI::success( 'The DB is already updated!' );
			return;
		}

		$callbacks = $manager->get_upgrade_callbacks();
		$did_tasks = false;

		if ( ! empty( $callbacks ) ) {
			Plugin::$instance->logger->get_logger()->info( 'Update DB has been started', [
				'meta' => [
					'plugin' => $manager->get_plugin_label(),
					'from' => $manager->get_current_version(),
					'to' => $manager->get_new_version(),
				],
			] );

			$updater->handle_immediately( $callbacks );
			$did_tasks = true;
		}

		$manager->on_runner_complete( $did_tasks );

		\WP_CLI::success( count( $callbacks ) . ' updates(s) has been applied.' );
	}
}
