<?php
/**
 * Base class for all the WP posts to order table migrator.
 */

namespace Automattic\WooCommerce\Database\Migrations;

/**
 * Base class for implementing WP posts to order tables migrations handlers.
 * It mainly contains methods to deal with error handling.
 *
 * @package Automattic\WooCommerce\Database\Migrations\CustomOrderTable
 */
abstract class TableMigrator {

	/**
	 * An array of cummulated error messages.
	 *
	 * @var array
	 */
	private $errors;

	/**
	 * Clear the error messages list.
	 *
	 * @return void
	 */
	protected function clear_errors(): void {
		$this->errors = array();
	}

	/**
	 * Add an error message to the errors list unless it's there already.
	 *
	 * @param string $error The error message to add.
	 * @return void
	 */
	protected function add_error( string $error ): void {
		if ( ! in_array( $error, $this->errors, true ) ) {
			$this->errors[] = $error;
		}
	}

	/**
	 * Get the list of error messages added.
	 *
	 * @return array
	 */
	protected function get_errors(): array {
		return $this->errors;
	}

	/**
	 * Run $wpdb->query and add the error, if any, to the errors list.
	 *
	 * @param string $query The SQL query to run.
	 * @return mixed Whatever $wpdb->query returns.
	 */
	protected function db_query( string $query ) {
		$wpdb = WC()->get_global( 'wpdb' );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$result = $wpdb->query( $query );

		if ( '' !== $wpdb->last_error ) {
			$this->add_error( $wpdb->last_error );
		}

		return $result;
	}

	/**
	 * Run $wpdb->get_results and add the error, if any, to the errors list.
	 *
	 * @param string|null $query The SQL query to run.
	 * @param string      $output Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 * @return mixed Whatever $wpdb->get_results returns.
	 */
	protected function db_get_results( string $query = null, string $output = OBJECT ) {
		$wpdb = WC()->get_global( 'wpdb' );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$result = $wpdb->get_results( $query, $output );

		if ( '' !== $wpdb->last_error ) {
			$this->add_error( $wpdb->last_error );
		}

		return $result;
	}

	/**
	 * Migrate a batch of orders, logging any database error that could arise and the exception thrown if any.
	 *
	 * @param array $entity_ids Order ids to migrate.
	 * @return array An array containing the keys 'errors' (array of strings) and 'exception' (exception object or null).
	 */
	public function process_migration_batch_for_ids( array $entity_ids ): array {
		$this->clear_errors();
		$exception = null;

		try {
			$this->process_migration_batch_for_ids_core( $entity_ids );
		} catch ( \Exception $ex ) {
			$exception = $ex;
		}

		return array(
			'errors'    => $this->get_errors(),
			'exception' => $exception,
		);
	}

	/**
	 * The core method that actually performs the migration for the supplied batch of order ids.
	 * It doesn't need to deal with database errors nor with exceptions.
	 *
	 * @param array $entity_ids Order ids to migrate.
	 * @return void
	 */
	abstract protected function process_migration_batch_for_ids_core( array $entity_ids ): void;

	/**
	 * Check if the amount of processed database rows matches the amount of orders to process, and log an error if not.
	 *
	 * @param string     $operation Operation performed, 'insert' or 'update'.
	 * @param array|bool $received_rows_count Value returned by @wpdb after executing the query.
	 * @return void
	 */
	protected function maybe_add_insert_or_update_error( string $operation, $received_rows_count ) {
		if ( false === $received_rows_count ) {
			$this->add_error( "$operation operation didn't complete, the database query failed" );
		}
	}
}
