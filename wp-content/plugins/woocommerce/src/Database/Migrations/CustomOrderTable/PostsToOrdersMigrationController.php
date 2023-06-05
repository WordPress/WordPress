<?php
/**
 * Class for implementing migration from wp_posts and wp_postmeta to custom order tables.
 */

namespace Automattic\WooCommerce\Database\Migrations\CustomOrderTable;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\ArrayUtil;

/**
 * This is the main class used to perform the complete migration of orders
 * from the posts table to the custom orders table.
 *
 * @package Automattic\WooCommerce\Database\Migrations\CustomOrderTable
 */
class PostsToOrdersMigrationController {

	/**
	 * Error logger for migration errors.
	 *
	 * @var \WC_Logger
	 */
	private $error_logger;

	/**
	 * Array of objects used to perform the migration.
	 *
	 * @var array
	 */
	private $all_migrators;

	/**
	 * The source name to use for logs.
	 */
	public const LOGS_SOURCE_NAME = 'posts-to-orders-migration';

	/**
	 * PostsToOrdersMigrationController constructor.
	 */
	public function __construct() {

		$this->all_migrators   = array();
		$this->all_migrators[] = new PostToOrderTableMigrator();
		$this->all_migrators[] = new PostToOrderAddressTableMigrator( 'billing' );
		$this->all_migrators[] = new PostToOrderAddressTableMigrator( 'shipping' );
		$this->all_migrators[] = new PostToOrderOpTableMigrator();
		$this->all_migrators[] = new PostMetaToOrderMetaMigrator( $this->get_migrated_meta_keys() );
		$this->error_logger    = wc_get_logger();
	}

	/**
	 * Helper method to get migrated keys for all the tables in this controller.
	 *
	 * @return string[] Array of meta keys.
	 */
	public function get_migrated_meta_keys() {
		$migrated_meta_keys = array();
		foreach ( $this->all_migrators as $migrator ) {
			if ( method_exists( $migrator, 'get_meta_column_config' ) ) {
				$migrated_meta_keys = array_merge( $migrated_meta_keys, $migrator->get_meta_column_config() );
			}
		}
		return array_keys( $migrated_meta_keys );
	}

	/**
	 * Migrates a set of orders from the posts table to the custom orders tables.
	 *
	 * @param array $order_post_ids List of post IDs of the orders to migrate.
	 */
	public function migrate_orders( array $order_post_ids ): void {
		$this->error_logger = WC()->call_function( 'wc_get_logger' );

		$using_transactions = $this->maybe_start_transaction();
		if ( null === $using_transactions ) {
			return;
		}

		$errors_were_logged = false;

		foreach ( $this->all_migrators as $migrator ) {
			$errors_were_logged = $this->do_orders_migration_step( $migrator, $order_post_ids );
			if ( $errors_were_logged && $using_transactions ) {
				$this->rollback_transaction();
				break;
			}
		}

		if ( ! $errors_were_logged && $using_transactions ) {
			$this->commit_transaction();
		}
	}

	/**
	 * Start a database transaction if the configuration mandates so.
	 *
	 * @return bool|null True if transaction started, false if transactions won't be used, null if transaction failed to start.
	 */
	private function maybe_start_transaction(): ?bool {
		if ( 'yes' !== get_option( CustomOrdersTableController::USE_DB_TRANSACTIONS_OPTION ) ) {
			return false;
		}

		$transaction_isolation_level = get_option( CustomOrdersTableController::DB_TRANSACTIONS_ISOLATION_LEVEL_OPTION, CustomOrdersTableController::DEFAULT_DB_TRANSACTIONS_ISOLATION_LEVEL );
		$this->verify_transaction_isolation_level( $transaction_isolation_level );
		$set_transaction_isolation_level_command = "SET TRANSACTION ISOLATION LEVEL $transaction_isolation_level";

		if ( ! $this->db_query( $set_transaction_isolation_level_command ) ) {
			return null;
		}

		return $this->db_query( 'START TRANSACTION' ) ? true : null;
	}

	/**
	 * Verify that a given database transaction isolation level name is valid, and throw an exception if not.
	 *
	 * @param string $transaction_isolation_level Transaction isolation level name to check.
	 * @return void
	 * @throws \Exception Invalid transaction isolation level name.
	 */
	private function verify_transaction_isolation_level( string $transaction_isolation_level ): void {
		if ( ! in_array( $transaction_isolation_level, CustomOrdersTableController::get_valid_transaction_isolation_levels(), true ) ) {
			throw new \Exception( 'Invalid database transaction isolation level name ' . $transaction_isolation_level );
		}
	}

	/**
	 * Commit the current database transaction.
	 *
	 * @return bool True on success, false on error.
	 */
	private function commit_transaction(): bool {
		return $this->db_query( 'COMMIT' );
	}

	/**
	 * Rollback the current database transaction.
	 *
	 * @return bool True on success, false on error.
	 */
	private function rollback_transaction(): bool {
		return $this->db_query( 'ROLLBACK' );
	}

	/**
	 * Execute a database query and log any errors.
	 *
	 * @param string $query The SQL query to execute.
	 * @return bool True if the query succeeded, false if there were errors.
	 */
	private function db_query( string $query ): bool {
		$wpdb = WC()->get_global( 'wpdb' );

		try {
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$wpdb->query( $query );
		} catch ( \Exception $exception ) {
			$exception_class = get_class( $exception );
			$this->error_logger->error(
				"PostsToOrdersMigrationController: when executing $query: ($exception_class) {$exception->getMessage()}, {$exception->getTraceAsString()}",
				array(
					'source'    => self::LOGS_SOURCE_NAME,
					'exception' => $exception,
				)
			);
			return false;
		}

		$error = $wpdb->last_error;
		if ( '' !== $error ) {
			$this->error_logger->error(
				"PostsToOrdersMigrationController: when executing $query: $error",
				array(
					'source' => self::LOGS_SOURCE_NAME,
					'error'  => $error,
				)
			);
			return false;
		}

		return true;
	}

	/**
	 * Performs one step of the migration for a set of order posts using one given migration class.
	 * All database errors and exceptions are logged.
	 *
	 * @param object $migration_class The migration class to use, must have a `process_migration_batch_for_ids(array of ids)` method.
	 * @param array  $order_post_ids List of post IDs of the orders to migrate.
	 * @return bool True if errors were logged, false otherwise.
	 */
	private function do_orders_migration_step( object $migration_class, array $order_post_ids ): bool {
		$result = $migration_class->process_migration_batch_for_ids( $order_post_ids );

		$errors    = array_unique( $result['errors'] );
		$exception = $result['exception'];
		if ( null === $exception && empty( $errors ) ) {
			return false;
		}

		$migration_class_name = ( new \ReflectionClass( $migration_class ) )->getShortName();
		$batch                = ArrayUtil::to_ranges_string( $order_post_ids );

		if ( null !== $exception ) {
			$exception_class = get_class( $exception );
			$this->error_logger->error(
				"$migration_class_name: when processing ids $batch: ($exception_class) {$exception->getMessage()}, {$exception->getTraceAsString()}",
				array(
					'source'    => self::LOGS_SOURCE_NAME,
					'ids'       => $order_post_ids,
					'exception' => $exception,
				)
			);
		}

		foreach ( $errors as $error ) {
			$this->error_logger->error(
				"$migration_class_name: when processing ids $batch: $error",
				array(
					'source' => self::LOGS_SOURCE_NAME,
					'ids'    => $order_post_ids,
					'error'  => $error,
				)
			);
		}

		return true;
	}

	/**
	 * Verify whether the given order IDs were migrated properly or not.
	 *
	 * @param array $order_post_ids Order IDs.
	 *
	 * @return array Array of failed IDs along with columns.
	 */
	public function verify_migrated_orders( array $order_post_ids ): array {
		$errors = array();
		foreach ( $this->all_migrators as $migrator ) {
			if ( method_exists( $migrator, 'verify_migrated_data' ) ) {
				$errors = $errors + $migrator->verify_migrated_data( $order_post_ids );
			}
		}
		return $errors;
	}

	/**
	 * Migrates an order from the posts table to the custom orders tables.
	 *
	 * @param int $order_post_id Post ID of the order to migrate.
	 */
	public function migrate_order( int $order_post_id ): void {
		$this->migrate_orders( array( $order_post_id ) );
	}
}
