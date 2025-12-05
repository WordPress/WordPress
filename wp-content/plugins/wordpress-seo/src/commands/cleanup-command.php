<?php

namespace Yoast\WP\SEO\Commands;

use WP_CLI;
use WP_CLI\ExitException;
use WP_CLI\Utils;
use Yoast\WP\SEO\Integrations\Cleanup_Integration;
use Yoast\WP\SEO\Main;

/**
 * A WP CLI command that helps with cleaning up unwanted records from our custom tables.
 */
final class Cleanup_Command implements Command_Interface {

	/**
	 * The integration that cleans up on cron.
	 *
	 * @var Cleanup_Integration
	 */
	private $cleanup_integration;

	/**
	 * The constructor.
	 *
	 * @param Cleanup_Integration $cleanup_integration The integration that cleans up on cron.
	 */
	public function __construct( Cleanup_Integration $cleanup_integration ) {
		$this->cleanup_integration = $cleanup_integration;
	}

	/**
	 * Returns the namespace of this command.
	 *
	 * @return string
	 */
	public static function get_namespace() {
		return Main::WP_CLI_NAMESPACE;
	}

	/**
	 * Performs a cleanup of custom Yoast tables.
	 *
	 * This removes unused, unwanted or orphaned database records, which ensures the best performance. Including:
	 * - Indexables
	 * - Indexable hierarchy
	 * - SEO links
	 *
	 * ## OPTIONS
	 *
	 * [--batch-size=<batch-size>]
	 * : The number of database records to clean up in a single sql query.
	 * ---
	 * default: 1000
	 * ---
	 *
	 * [--interval=<interval>]
	 * : The number of microseconds (millionths of a second) to wait between cleanup batches.
	 * ---
	 * default: 500000
	 * ---
	 *
	 * [--network]
	 * : Performs the cleanup on all sites within the network.
	 *
	 * ## EXAMPLES
	 *
	 *     wp yoast cleanup
	 *
	 * @when after_wp_load
	 *
	 * @param array|null $args       The arguments.
	 * @param array|null $assoc_args The associative arguments.
	 *
	 * @return void
	 *
	 * @throws ExitException When the input args are invalid.
	 */
	public function cleanup( $args = null, $assoc_args = null ) {
		if ( isset( $assoc_args['interval'] ) && (int) $assoc_args['interval'] < 0 ) {
			WP_CLI::error( \__( 'The value for \'interval\' must be a positive integer.', 'wordpress-seo' ) );
		}
		if ( isset( $assoc_args['batch-size'] ) && (int) $assoc_args['batch-size'] < 1 ) {
			WP_CLI::error( \__( 'The value for \'batch-size\' must be a positive integer higher than equal to 1.', 'wordpress-seo' ) );
		}

		if ( isset( $assoc_args['network'] ) && \is_multisite() ) {
			$total_removed = $this->cleanup_network( $assoc_args );
		}
		else {
			$total_removed = $this->cleanup_current_site( $assoc_args );
		}

		WP_CLI::success(
			\sprintf(
			/* translators: %1$d is the number of records that are removed. */
				\_n(
					'Cleaned up %1$d record.',
					'Cleaned up %1$d records.',
					$total_removed,
					'wordpress-seo'
				),
				$total_removed
			)
		);
	}

	/**
	 * Performs the cleanup for the entire network.
	 *
	 * @param array|null $assoc_args The associative arguments.
	 *
	 * @return int The number of cleaned up records.
	 */
	private function cleanup_network( $assoc_args ) {
		$criteria      = [
			'fields'   => 'ids',
			'spam'     => 0,
			'deleted'  => 0,
			'archived' => 0,
		];
		$blog_ids      = \get_sites( $criteria );
		$total_removed = 0;
		foreach ( $blog_ids as $blog_id ) {
			\switch_to_blog( $blog_id );
			$total_removed += $this->cleanup_current_site( $assoc_args );
			\restore_current_blog();
		}

		return $total_removed;
	}

	/**
	 * Performs the cleanup for a single site.
	 *
	 * @param array|null $assoc_args The associative arguments.
	 *
	 * @return int The number of cleaned up records.
	 */
	private function cleanup_current_site( $assoc_args ) {
		$site_url      = \site_url();
		$total_removed = 0;

		if ( ! \is_plugin_active( \WPSEO_BASENAME ) ) {
			/* translators: %1$s is the site url of the site that is skipped. %2$s is Yoast SEO. */
			WP_CLI::warning( \sprintf( \__( 'Skipping %1$s. %2$s is not active on this site.', 'wordpress-seo' ), $site_url, 'Yoast SEO' ) );

			return $total_removed;
		}

		// Make sure the DB is up to date first.
		\do_action( '_yoast_run_migrations' );

		$tasks    = $this->cleanup_integration->get_cleanup_tasks();
		$limit    = (int) $assoc_args['batch-size'];
		$interval = (int) $assoc_args['interval'];

		/* translators: %1$s is the site url of the site that is cleaned up. %2$s is the name of the cleanup task that is currently running. */
		$progress_bar_title_format = \__( 'Cleaning up %1$s [%2$s]', 'wordpress-seo' );
		$progress                  = Utils\make_progress_bar( \sprintf( $progress_bar_title_format, $site_url, \key( $tasks ) ), \count( $tasks ) );

		foreach ( $tasks as $task_name => $task ) {
			// Update the progressbar title with the current task name.
			$progress->tick( 0, \sprintf( $progress_bar_title_format, $site_url, $task_name ) );
			do {
				$items_cleaned = $task( $limit );
				if ( \is_int( $items_cleaned ) ) {
					$total_removed += $items_cleaned;
				}
				\usleep( $interval );

				// Update the timer.
				$progress->tick( 0 );
			} while ( $items_cleaned !== false && $items_cleaned > 0 );
			$progress->tick();
		}
		$progress->finish();

		$this->cleanup_integration->reset_cleanup();
		WP_CLI::log(
			\sprintf(
			/* translators: %1$d is the number of records that were removed. %2$s is the site url. */
				\_n(
					'Cleaned up %1$d record from %2$s.',
					'Cleaned up %1$d records from %2$s.',
					$total_removed,
					'wordpress-seo'
				),
				$total_removed,
				$site_url
			)
		);

		return $total_removed;
	}
}
