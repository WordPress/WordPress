<?php

namespace Automattic\WooCommerce\DataBase\Migrations\CustomOrderTable;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Internal\DataStores\Orders\DataSynchronizer;
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use WP_CLI;

/**
 * CLI tool for migrating order data to/from custom table.
 *
 * Credits https://github.com/liquidweb/woocommerce-custom-orders-table/blob/develop/includes/class-woocommerce-custom-orders-table-cli.php.
 *
 * Class CLIRunner
 */
class CLIRunner {

	/**
	 * CustomOrdersTableController instance.
	 *
	 * @var CustomOrdersTableController
	 */
	private $controller;

	/**
	 * DataSynchronizer instance.
	 *
	 * @var DataSynchronizer;
	 */
	private $synchronizer;

	/**
	 * PostsToOrdersMigrationController instance.
	 *
	 * @var PostsToOrdersMigrationController
	 */
	private $post_to_cot_migrator;

	/**
	 * Init method, invoked by DI container.
	 *
	 * @param CustomOrdersTableController      $controller Instance.
	 * @param DataSynchronizer                 $synchronizer Instance.
	 * @param PostsToOrdersMigrationController $posts_to_orders_migration_controller Instance.
	 *
	 * @internal
	 */
	final public function init( CustomOrdersTableController $controller, DataSynchronizer $synchronizer, PostsToOrdersMigrationController $posts_to_orders_migration_controller ) {
		$this->controller           = $controller;
		$this->synchronizer         = $synchronizer;
		$this->post_to_cot_migrator = $posts_to_orders_migration_controller;
	}

	/**
	 * Registers commands for CLI.
	 */
	public function register_commands() {
		WP_CLI::add_command( 'wc cot count_unmigrated', array( $this, 'count_unmigrated' ) );
		WP_CLI::add_command( 'wc cot migrate', array( $this, 'migrate' ) );
		WP_CLI::add_command( 'wc cot sync', array( $this, 'sync' ) );
		WP_CLI::add_command( 'wc cot verify_cot_data', array( $this, 'verify_cot_data' ) );
	}

	/**
	 * Check if the COT feature is enabled.
	 *
	 * @param bool $log Optionally log a error message.
	 *
	 * @return bool Whether the COT feature is enabled.
	 */
	private function is_enabled( $log = true ) : bool {
		if ( ! $this->controller->is_feature_visible() ) {
			if ( $log ) {
				WP_CLI::log(
					sprintf(
						// translators: %s - link to testing instructions webpage.
						__( 'Custom order table usage is not enabled. If you are testing, you can enable it by following the testing instructions in %s', 'woocommerce' ),
						'https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book'
					)
				);
			}
		}

		return $this->controller->is_feature_visible();
	}

	/**
	 * Helper method to log warning that feature is not yet production ready.
	 */
	private function log_production_warning() {
		WP_CLI::log( __( 'This feature is not production ready yet. Make sure you are not running these commands in your production environment.', 'woocommerce' ) );
	}

	/**
	 * Count how many orders have yet to be migrated into the custom orders table.
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc cot count_unmigrated
	 *
	 * @param array $args Positional arguments passed to the command.
	 *
	 * @param array $assoc_args Associative arguments (options) passed to the command.
	 *
	 * @return int The number of orders to be migrated.*
	 */
	public function count_unmigrated( $args = array(), $assoc_args = array() ) : int {
		if ( ! $this->is_enabled() ) {
			return 0;
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$order_count = $this->synchronizer->get_current_orders_pending_sync_count();

		$assoc_args = wp_parse_args(
			$assoc_args,
			array(
				'log' => true,
			)
		);
		if ( isset( $assoc_args['log'] ) && $assoc_args['log'] ) {
			WP_CLI::log(
				sprintf(
					/* Translators: %1$d is the number of orders to be synced. */
					_n(
						'There is %1$d order to be synced.',
						'There are %1$d orders to be synced.',
						$order_count,
						'woocommerce'
					),
					$order_count
				)
			);
		}

		return (int) $order_count;
	}

	/**
	 * Sync order data between the custom order tables and the core WordPress post tables.
	 *
	 * ## OPTIONS
	 *
	 * [--batch-size=<batch-size>]
	 * : The number of orders to process in each batch.
	 * ---
	 * default: 500
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc cot sync --batch-size=500
	 *
	 * @param array $args Positional arguments passed to the command.
	 * @param array $assoc_args Associative arguments (options) passed to the command.
	 */
	public function sync( $args = array(), $assoc_args = array() ) {
		$this->log_production_warning();
		if ( ! $this->is_enabled() ) {
			return;
		}

		$order_count = $this->count_unmigrated();

		// Abort if there are no orders to migrate.
		if ( ! $order_count ) {
			return WP_CLI::warning( __( 'There are no orders to sync, aborting.', 'woocommerce' ) );
		}

		$assoc_args       = wp_parse_args(
			$assoc_args,
			array(
				'batch-size' => 500,
			)
		);
		$batch_size       = ( (int) $assoc_args['batch-size'] ) === 0 ? 500 : (int) $assoc_args['batch-size'];
		$progress         = WP_CLI\Utils\make_progress_bar( 'Order Data Sync', $order_count / $batch_size );
		$processed        = 0;
		$batch_count      = 1;
		$total_time       = 0;
		$orders_remaining = true;

		while ( $order_count > 0 || $orders_remaining ) {
			$remaining_count = $order_count;

			WP_CLI::debug(
				sprintf(
					/* Translators: %1$d is the batch number and %2$d is the batch size. */
					__( 'Beginning batch #%1$d (%2$d orders/batch).', 'woocommerce' ),
					$batch_count,
					$batch_size
				)
			);
			$batch_start_time = microtime( true );
			$order_ids        = $this->synchronizer->get_next_batch_to_process( $batch_size );
			if ( count( $order_ids ) ) {
				$this->synchronizer->process_batch( $order_ids );
			}
			$processed       += count( $order_ids );
			$batch_total_time = microtime( true ) - $batch_start_time;

			WP_CLI::debug(
				sprintf(
					// Translators: %1$d is the batch number, %2$d is the number of processed orders and %3$d is the execution time in seconds.
					__( 'Batch %1$d (%2$d orders) completed in %3$d seconds', 'woocommerce' ),
					$batch_count,
					count( $order_ids ),
					$batch_total_time
				)
			);

			$batch_count ++;
			$total_time += $batch_total_time;

			$progress->tick();

			$orders_remaining = count( $this->synchronizer->get_next_batch_to_process( 1 ) ) > 0;
			$order_count      = $remaining_count - $batch_size;
		}

		$progress->finish();

		// Issue a warning if no orders were migrated.
		if ( ! $processed ) {
			return WP_CLI::warning( __( 'No orders were synced.', 'woocommerce' ) );
		}

		WP_CLI::log( __( 'Sync completed.', 'woocommerce' ) );

		return WP_CLI::success(
			sprintf(
				/* Translators: %1$d is the number of migrated orders and %2$d is the execution time in seconds. */
				_n(
					'%1$d order was synced in %2$d seconds.',
					'%1$d orders were synced in %2$d seconds.',
					$processed,
					'woocommerce'
				),
				$processed,
				$total_time
			)
		);
	}

	/**
	 * Copy order data into the postmeta table.
	 *
	 * Note that this could dramatically increase the size of your postmeta table, but is recommended
	 * if you wish to stop using the custom orders table plugin.
	 *
	 * ## OPTIONS
	 *
	 * [--batch-size=<batch-size>]
	 * : The number of orders to process in each batch. Passing a value of 0 will disable batching.
	 * ---
	 * default: 500
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     # Copy all order data into the post meta table, 500 posts at a time.
	 *     wp wc cot backfill --batch-size=500
	 *
	 * @param array $args Positional arguments passed to the command.
	 * @param array $assoc_args Associative arguments (options) passed to the command.
	 */
	public function migrate( $args = array(), $assoc_args = array() ) {
		$this->log_production_warning();
		WP_CLI::log( __( 'Migrate command is deprecated. Please use `sync` instead.', 'woocommerce' ) );
	}

	/**
	 * Verify migrated order data with original posts data.
	 *
	 * ## OPTIONS
	 *
	 * [--batch-size=<batch-size>]
	 * : The number of orders to verify in each batch.
	 * ---
	 * default: 500
	 * ---
	 *
	 * [--start-from=<order_id>]
	 * : Order ID to start from.
	 * ---
	 * default: 0
	 * ---
	 *
	 * [--end-at=<order_id>]
	 * : Order ID to end at.
	 * ---
	 * default: -1
	 * ---
	 *
	 * [--verbose]
	 * : Whether to output errors as they happen in batch, or output them all together at the end.
	 * ---
	 * default: false
	 * ---
	 *
	 * [--order-types]
	 * : Comma seperated list of order types that needs to be verified. For example, --order-types=shop_order,shop_order_refund
	 * ---
	 * default: Output of function `wc_get_order_types( 'cot-migration' )`
	 *
	 * ## EXAMPLES
	 *
	 *     # Verify migrated order data, 500 orders at a time.
	 *     wp wc cot verify_cot_data --batch-size=500 --start-from=0 --end-at=10000
	 *
	 * @param array $args Positional arguments passed to the command.
	 * @param array $assoc_args Associative arguments (options) passed to the command.
	 */
	public function verify_cot_data( $args = array(), $assoc_args = array() ) {
		global $wpdb;
		$this->log_production_warning();
		if ( ! $this->is_enabled() ) {
			return;
		}

		$assoc_args = wp_parse_args(
			$assoc_args,
			array(
				'batch-size'  => 500,
				'start-from'  => 0,
				'end-at'      => - 1,
				'verbose'     => false,
				'order-types' => '',
			)
		);

		$batch_count    = 1;
		$total_time     = 0;
		$failed_ids     = array();
		$processed      = 0;
		$order_id_start = (int) $assoc_args['start-from'];
		$order_id_end   = (int) $assoc_args['end-at'];
		$order_id_end   = -1 === $order_id_end ? PHP_INT_MAX : $order_id_end;
		$batch_size     = ( (int) $assoc_args['batch-size'] ) === 0 ? 500 : (int) $assoc_args['batch-size'];
		$verbose        = (bool) $assoc_args['verbose'];
		$order_types    = wc_get_order_types( 'cot-migration' );
		if ( ! empty( $assoc_args['order-types'] ) ) {
			$passed_order_types = array_map( 'trim', explode( ',', $assoc_args['order-types'] ) );
			$order_types        = array_intersect( $order_types, $passed_order_types );
		}

		if ( 0 === count( $order_types ) ) {
			return WP_CLI::error(
				sprintf(
				/* Translators: %s is the comma seperated list of order types. */
					__( 'Passed order type does not match any registered order types. Following order types are registered: %s', 'woocommerce' ),
					implode( ',', wc_get_order_types( 'cot-migration' ) )
				)
			);
		}

		$order_types_pl = implode( ',', array_fill( 0, count( $order_types ), '%s' ) );

		$order_count = $this->get_verify_order_count( $order_id_start, $order_id_end, $order_types, false );

		$progress = WP_CLI\Utils\make_progress_bar( 'Order Data Verification', $order_count / $batch_size );

		if ( ! $order_count ) {
			return WP_CLI::warning( __( 'There are no orders to verify, aborting.', 'woocommerce' ) );
		}

		while ( $order_count > 0 ) {
			WP_CLI::debug(
				sprintf(
					/* Translators: %1$d is the batch number, %2$d is the batch size. */
					__( 'Beginning verification for batch #%1$d (%2$d orders/batch).', 'woocommerce' ),
					$batch_count,
					$batch_size
				)
			);

			// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Inputs are prepared.
			$order_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT ID FROM $wpdb->posts WHERE post_type in ( $order_types_pl ) AND ID >= %d AND ID <= %d ORDER BY ID ASC LIMIT %d",
					array_merge(
						$order_types,
						array(
							$order_id_start,
							$order_id_end,
							$batch_size,
						)
					)
				)
			);
			// phpcs:enable
			$batch_start_time            = microtime( true );
			$failed_ids_in_current_batch = $this->post_to_cot_migrator->verify_migrated_orders( $order_ids );
			$failed_ids_in_current_batch = $this->verify_meta_data( $order_ids, $failed_ids_in_current_batch );
			$failed_ids                  = $verbose ? array() : $failed_ids + $failed_ids_in_current_batch;
			$processed                  += count( $order_ids );
			$batch_total_time            = microtime( true ) - $batch_start_time;
			$batch_count ++;
			$total_time += $batch_total_time;

			if ( $verbose && count( $failed_ids_in_current_batch ) > 0 ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r -- This is a CLI command and debugging code is intended.
				$errors = print_r( $failed_ids_in_current_batch, true );
				WP_CLI::warning(
					sprintf(
					/* Translators: %1$d is number of errors and %2$s is the formatted array of order IDs. */
						_n(
							'%1$d error found: %2$s. Please review the error above.',
							'%1$d errors found: %2$s. Please review the errors above.',
							count( $failed_ids_in_current_batch ),
							'woocommerce'
						),
						count( $failed_ids_in_current_batch ),
						$errors
					)
				);
			}

			$progress->tick();

			WP_CLI::debug(
				sprintf(
					/* Translators: %1$d is the batch number, %2$d is time taken to process batch. */
					__( 'Batch %1$d (%2$d orders) completed in %3$d seconds.', 'woocommerce' ),
					$batch_count,
					count( $order_ids ),
					$batch_total_time
				)
			);

			$order_id_start  = max( $order_ids ) + 1;
			$remaining_count = $this->get_verify_order_count( $order_id_start, $order_id_end, $order_types, false );
			if ( $remaining_count === $order_count ) {
				return WP_CLI::error( __( 'Infinite loop detected, aborting. No errors found.', 'woocommerce' ) );
			}
			$order_count = $remaining_count;
		}

		$progress->finish();
		WP_CLI::log( __( 'Verification completed.', 'woocommerce' ) );

		if ( $verbose ) {
			return;
		}

		if ( 0 === count( $failed_ids ) ) {
			return WP_CLI::success(
				sprintf(
					/* Translators: %1$d is the number of migrated orders and %2$d is time taken. */
					_n(
						'%1$d order was verified in %2$d seconds.',
						'%1$d orders were verified in %2$d seconds.',
						$processed,
						'woocommerce'
					),
					$processed,
					$total_time
				)
			);
		} else {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r -- This is a CLI command and debugging code is intended.
			$errors = print_r( $failed_ids, true );

			return WP_CLI::error(
				sprintf(
					'%1$s %2$s',
					sprintf(
						/* Translators: %1$d is the number of migrated orders and %2$d is the execution time in seconds. */
						_n(
							'%1$d order was verified in %2$d seconds.',
							'%1$d orders were verified in %2$d seconds.',
							$processed,
							'woocommerce'
						),
						$processed,
						$total_time
					),
					sprintf(
						/* Translators: %1$d is number of errors and %2$s is the formatted array of order IDs. */
						_n(
							'%1$d error found: %2$s. Please review the error above.',
							'%1$d errors found: %2$s. Please review the errors above.',
							count( $failed_ids ),
							'woocommerce'
						),
						count( $failed_ids ),
						$errors
					)
				)
			);
		}
	}

	/**
	 * Helper method to get count for orders needing verification.
	 *
	 * @param int   $order_id_start Order ID to start from.
	 * @param int   $order_id_end Order ID to end at.
	 * @param array $order_types List of order types to verify.
	 * @param bool  $log Whether to also log an error message.
	 *
	 * @return int Order count.
	 */
	private function get_verify_order_count( int $order_id_start, int $order_id_end, array $order_types, bool $log = true ) : int {
		global $wpdb;

		$order_types_placeholder = implode( ',', array_fill( 0, count( $order_types ), '%s' ) );

		// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Inputs are prepared.
		$order_count = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $wpdb->posts WHERE post_type in ($order_types_placeholder) AND ID >= %d AND ID <= %d",
				array_merge(
					$order_types,
					array(
						$order_id_start,
						$order_id_end,
					)
				)
			)
		);
		// phpcs:enable

		if ( $log ) {
			WP_CLI::log(
				sprintf(
					/* Translators: %1$d is the number of orders to be verified. */
					_n(
						'There is %1$d order to be verified.',
						'There are %1$d orders to be verified.',
						$order_count,
						'woocommerce'
					),
					$order_count
				)
			);
		}

		return $order_count;
	}

	/**
	 * Verify meta data as part of verifying the order object.
	 *
	 * @param array $order_ids Order IDs.
	 * @param array $failed_ids Array for storing failed IDs.
	 *
	 * @return array Failed IDs with meta details.
	 */
	private function verify_meta_data( array $order_ids, array $failed_ids ) : array {
		global $wpdb;
		if ( ! count( $order_ids ) ) {
			return array();
		}
		$excluded_columns             = $this->post_to_cot_migrator->get_migrated_meta_keys();
		$excluded_columns_placeholder = implode( ', ', array_fill( 0, count( $excluded_columns ), '%s' ) );
		$order_ids_placeholder        = implode( ', ', array_fill( 0, count( $order_ids ), '%d' ) );
		$meta_table                   = OrdersTableDataStore::get_meta_table_name();

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- table names are hardcoded, orders_ids and excluded_columns are prepared.
		$query       = $wpdb->prepare(
			"
SELECT {$wpdb->postmeta}.post_id as entity_id, {$wpdb->postmeta}.meta_key, {$wpdb->postmeta}.meta_value
FROM $wpdb->postmeta
WHERE
      {$wpdb->postmeta}.post_id in ( $order_ids_placeholder ) AND
      {$wpdb->postmeta}.meta_key not in ( $excluded_columns_placeholder )
ORDER BY {$wpdb->postmeta}.post_id ASC, {$wpdb->postmeta}.meta_key ASC;
",
			array_merge(
				$order_ids,
				$excluded_columns
			)
		);
		$source_data = $wpdb->get_results( $query, ARRAY_A );
		// phpcs:enable

		$normalized_source_data = $this->normalize_raw_meta_data( $source_data );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- table names are hardcoded, orders_ids and excluded_columns are prepared.
		$migrated_query = $wpdb->prepare(
			"
SELECT $meta_table.order_id as entity_id, $meta_table.meta_key, $meta_table.meta_value
FROM $meta_table
WHERE
	$meta_table.order_id in ( $order_ids_placeholder )
ORDER BY $meta_table.order_id ASC, $meta_table.meta_key ASC;
",
			$order_ids
		);
		$migrated_data  = $wpdb->get_results( $migrated_query, ARRAY_A );
		// phpcs:enable

		$normalized_migrated_meta_data = $this->normalize_raw_meta_data( $migrated_data );

		foreach ( $normalized_source_data as $order_id => $meta ) {
			foreach ( $meta as $meta_key => $values ) {
				$migrated_meta_values = isset( $normalized_migrated_meta_data[ $order_id ][ $meta_key ] ) ? $normalized_migrated_meta_data[ $order_id ][ $meta_key ] : array();
				$diff                 = array_diff( $values, $migrated_meta_values );

				if ( count( $diff ) ) {
					if ( ! isset( $failed_ids[ $order_id ] ) ) {
						$failed_ids[ $order_id ] = array();
					}
					$failed_ids[ $order_id ][] = array(
						'order_id'         => $order_id,
						'meta_key'         => $meta_key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Not a meta query.
						'orig_meta_values' => $values,
						'new_meta_values'  => $migrated_meta_values,
					);
				}
			}
		}

		return $failed_ids;
	}

	/**
	 * Helper method to normalize response from meta queries into order_id > meta_key > meta_values.
	 *
	 * @param array $data Data fetched from meta queries.
	 *
	 * @return array Normalized data.
	 */
	private function normalize_raw_meta_data( array $data ) : array {
		$clubbed_data = array();
		foreach ( $data as $row ) {
			if ( ! isset( $clubbed_data[ $row['entity_id'] ] ) ) {
				$clubbed_data[ $row['entity_id'] ] = array();
			}
			if ( ! isset( $clubbed_data[ $row['entity_id'] ][ $row['meta_key'] ] ) ) {
				$clubbed_data[ $row['entity_id'] ][ $row['meta_key'] ] = array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Not a meta query.
			}
			$clubbed_data[ $row['entity_id'] ][ $row['meta_key'] ][] = $row['meta_value'];
		}
		return $clubbed_data;
	}

}
