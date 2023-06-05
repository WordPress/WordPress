<?php
/**
 * Generic Migration class to move any meta data associated to an entity, to a different meta table associated with a custom entity table.
 */

namespace Automattic\WooCommerce\Database\Migrations;

/**
 * Base class for implementing migrations from the standard WordPress meta table
 * to custom meta (key-value pairs) tables.
 *
 * @package Automattic\WooCommerce\Database\Migrations\CustomOrderTable
 */
abstract class MetaToMetaTableMigrator extends TableMigrator {

	/**
	 * Schema config, see __construct for more details.
	 *
	 * @var array
	 */
	private $schema_config;

	/**
	 * Returns config for the migration.
	 *
	 * @return array Meta config, must be in following format:
	 * array(
	 *  'source'      => array(
	 *      'meta'          => array(
	 *          'table_name'        => source_meta_table_name,
	 *          'entity_id_column'  => entity_id column name in source meta table,
	 *          'meta_key_column'   => meta_key column',
	 *          'meta_value_column' => meta_value column',
	 *      ),
	 *      'entity' => array(
	 *          'table_name'       => entity table name for the meta table,
	 *          'source_id_column' => column name in entity table which maps to meta table,
	 *          'id_column'        => id column in entity table,
	 *      ),
	 *      'excluded_keys' => array of keys to exclude,
	 *  ),
	 *  'destination' => array(
	 *      'meta'   => array(
	 *          'table_name'        => destination meta table name,
	 *          'entity_id_column'  => entity_id column in meta table,
	 *          'meta_key_column'   => meta key column,
	 *          'meta_value_column' => meta_value column,
	 *          'entity_id_type'    => data type of entity id,
	 *          'meta_id_column'    => id column in meta table,
	 *      ),
	 *  ),
	 * )
	 */
	abstract protected function get_meta_config(): array;

	/**
	 * MetaToMetaTableMigrator constructor.
	 */
	public function __construct() {
		$this->schema_config = $this->get_meta_config();
	}

	/**
	 * Migrate a batch of entities from the posts table to the corresponding table.
	 *
	 * @param array $entity_ids Ids of entities ro migrate.
	 */
	protected function process_migration_batch_for_ids_core( array $entity_ids ): void {
		$to_migrate = $this->fetch_data_for_migration_for_ids( $entity_ids );
		if ( empty( $to_migrate ) ) {
			return;
		}

		$already_migrated = $this->get_already_migrated_records( array_keys( $to_migrate ) );

		$data      = $this->classify_update_insert_records( $to_migrate, $already_migrated );
		$to_insert = $data[0];
		$to_update = $data[1];

		if ( ! empty( $to_insert ) ) {
			$insert_queries       = $this->generate_insert_sql_for_batch( $to_insert );
			$processed_rows_count = $this->db_query( $insert_queries );
			$this->maybe_add_insert_or_update_error( 'insert', $processed_rows_count );
		}

		if ( ! empty( $to_update ) ) {
			$update_queries       = $this->generate_update_sql_for_batch( $to_update );
			$processed_rows_count = $this->db_query( $update_queries );
			$this->maybe_add_insert_or_update_error( 'update', $processed_rows_count );
		}
	}

	/**
	 * Generate update SQL for given batch.
	 *
	 * @param array $batch List of data to generate update SQL for. Should be in same format as output of $this->fetch_data_for_migration_for_ids.
	 *
	 * @return string Query to update batch records.
	 */
	private function generate_update_sql_for_batch( array $batch ): string {
		global $wpdb;

		$table             = $this->schema_config['destination']['meta']['table_name'];
		$meta_id_column    = $this->schema_config['destination']['meta']['meta_id_column'];
		$meta_key_column   = $this->schema_config['destination']['meta']['meta_key_column'];
		$meta_value_column = $this->schema_config['destination']['meta']['meta_value_column'];
		$entity_id_column  = $this->schema_config['destination']['meta']['entity_id_column'];
		$columns           = array( $meta_id_column, $entity_id_column, $meta_key_column, $meta_value_column );
		$columns_sql       = implode( '`, `', $columns );

		$entity_id_column_placeholder = MigrationHelper::get_wpdb_placeholder_for_type( $this->schema_config['destination']['meta']['entity_id_type'] );
		$placeholder_string           = "%d, $entity_id_column_placeholder, %s, %s";
		$values                       = array();
		foreach ( $batch as $entity_id => $rows ) {
			foreach ( $rows as $meta_key => $meta_details ) {

				// phpcs:disable WordPress.DB.PreparedSQL, WordPress.DB.PreparedSQLPlaceholders
				$values[] = $wpdb->prepare(
					"( $placeholder_string )",
					array( $meta_details['id'], $entity_id, $meta_key, $meta_details['meta_value'] )
				);
				// phpcs:enable
			}
		}
		$value_sql = implode( ',', $values );

		$on_duplicate_key_clause = MigrationHelper::generate_on_duplicate_statement_clause( $columns );

		return "INSERT INTO $table ( `$columns_sql` ) VALUES $value_sql $on_duplicate_key_clause";
	}

	/**
	 * Generate insert sql queries for batches.
	 *
	 * @param array $batch Data to generate queries for.
	 *
	 * @return string Insert SQL query.
	 */
	private function generate_insert_sql_for_batch( array $batch ): string {
		global $wpdb;

		$table             = $this->schema_config['destination']['meta']['table_name'];
		$meta_key_column   = $this->schema_config['destination']['meta']['meta_key_column'];
		$meta_value_column = $this->schema_config['destination']['meta']['meta_value_column'];
		$entity_id_column  = $this->schema_config['destination']['meta']['entity_id_column'];
		$column_sql        = "(`$entity_id_column`, `$meta_key_column`, `$meta_value_column`)";

		$entity_id_column_placeholder = MigrationHelper::get_wpdb_placeholder_for_type( $this->schema_config['destination']['meta']['entity_id_type'] );
		$placeholder_string           = "$entity_id_column_placeholder, %s, %s";
		$values                       = array();
		foreach ( $batch as $entity_id => $rows ) {
			foreach ( $rows as $meta_key => $meta_values ) {
				foreach ( $meta_values as $meta_value ) {
					$query_params = array(
						$entity_id,
						$meta_key,
						$meta_value,
					);
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders
					$value_sql = $wpdb->prepare( "$placeholder_string", $query_params );
					$values[]  = $value_sql;
				}
			}
		}

		$values_sql = implode( '), (', $values );

		return "INSERT IGNORE INTO $table $column_sql VALUES ($values_sql)";
	}

	/**
	 * Fetch data for migration.
	 *
	 * @param array $entity_ids Array of IDs to fetch data for.
	 *
	 * @return array[] Data, will of the form:
	 * array(
	 *   'id_1' => array( 'column1' => array( value1_1, value1_2...), 'column2' => array(value2_1, value2_2...), ...),
	 *   ...,
	 * )
	 */
	private function fetch_data_for_migration_for_ids( array $entity_ids ): array {
		if ( empty( $entity_ids ) ) {
			return array();
		}

		$meta_query = $this->build_meta_table_query( $entity_ids );

		$meta_data_rows = $this->db_get_results( $meta_query );
		if ( empty( $meta_data_rows ) ) {
			return array();
		}

		foreach ( $meta_data_rows as $migrate_row ) {
			if ( ! isset( $to_migrate[ $migrate_row->entity_id ] ) ) {
				$to_migrate[ $migrate_row->entity_id ] = array();
			}

			if ( ! isset( $to_migrate[ $migrate_row->entity_id ][ $migrate_row->meta_key ] ) ) {
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$to_migrate[ $migrate_row->entity_id ][ $migrate_row->meta_key ] = array();
			}

			$to_migrate[ $migrate_row->entity_id ][ $migrate_row->meta_key ][] = $migrate_row->meta_value;
		}

		return $to_migrate;
	}

	/**
	 * Helper method to get already migrated records. Will be used to find prevent migration of already migrated records.
	 *
	 * @param array $entity_ids List of entity ids to check for.
	 *
	 * @return array Already migrated records.
	 */
	private function get_already_migrated_records( array $entity_ids ): array {
		global $wpdb;

		$destination_table_name        = $this->schema_config['destination']['meta']['table_name'];
		$destination_id_column         = $this->schema_config['destination']['meta']['meta_id_column'];
		$destination_entity_id_column  = $this->schema_config['destination']['meta']['entity_id_column'];
		$destination_meta_key_column   = $this->schema_config['destination']['meta']['meta_key_column'];
		$destination_meta_value_column = $this->schema_config['destination']['meta']['meta_value_column'];

		$entity_id_type_placeholder = MigrationHelper::get_wpdb_placeholder_for_type( $this->schema_config['destination']['meta']['entity_id_type'] );
		$entity_ids_placeholder     = implode( ',', array_fill( 0, count( $entity_ids ), $entity_id_type_placeholder ) );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
		$data_already_migrated = $this->db_get_results(
			$wpdb->prepare(
				"
SELECT
	   $destination_id_column meta_id,
       $destination_entity_id_column entity_id,
       $destination_meta_key_column meta_key,
       $destination_meta_value_column meta_value
FROM $destination_table_name destination
WHERE destination.$destination_entity_id_column in ( $entity_ids_placeholder ) ORDER BY destination.$destination_entity_id_column
",
				$entity_ids
			)
		);
		// phpcs:enable

		$already_migrated = array();

		foreach ( $data_already_migrated as $migrate_row ) {
			if ( ! isset( $already_migrated[ $migrate_row->entity_id ] ) ) {
				$already_migrated[ $migrate_row->entity_id ] = array();
			}

			// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_key, WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			if ( ! isset( $already_migrated[ $migrate_row->entity_id ][ $migrate_row->meta_key ] ) ) {
				$already_migrated[ $migrate_row->entity_id ][ $migrate_row->meta_key ] = array();
			}

			$already_migrated[ $migrate_row->entity_id ][ $migrate_row->meta_key ][] = array(
				'id'         => $migrate_row->meta_id,
				'meta_value' => $migrate_row->meta_value,
			);
			// phpcs:enable
		}

		return $already_migrated;
	}

	/**
	 * Classify each record on whether to migrate or update.
	 *
	 * @param array $to_migrate Records to migrate.
	 * @param array $already_migrated Records already migrated.
	 *
	 * @return array[] Returns two arrays, first for records to migrate, and second for records to upgrade.
	 */
	private function classify_update_insert_records( array $to_migrate, array $already_migrated ): array {
		$to_update = array();
		$to_insert = array();

		foreach ( $to_migrate as $entity_id => $rows ) {
			foreach ( $rows as $meta_key => $meta_values ) {
				// If there is no corresponding record in the destination table then insert.
				// If there is single value in both already migrated and current then update.
				// If there are multiple values in either already_migrated records or in to_migrate_records, then insert instead of updating.
				if ( ! isset( $already_migrated[ $entity_id ][ $meta_key ] ) ) {
					if ( ! isset( $to_insert[ $entity_id ] ) ) {
						$to_insert[ $entity_id ] = array();
					}
					$to_insert[ $entity_id ][ $meta_key ] = $meta_values;
				} else {
					if ( 1 === count( $meta_values ) && 1 === count( $already_migrated[ $entity_id ][ $meta_key ] ) ) {
						if ( $meta_values[0] === $already_migrated[ $entity_id ][ $meta_key ][0]['meta_value'] ) {
							continue;
						}
						if ( ! isset( $to_update[ $entity_id ] ) ) {
							$to_update[ $entity_id ] = array();
						}
						$to_update[ $entity_id ][ $meta_key ] = array(
							'id'         => $already_migrated[ $entity_id ][ $meta_key ][0]['id'],
							// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
							'meta_value' => $meta_values[0],
						);
						continue;
					}

					// There are multiple meta entries, let's find the unique entries and insert.
					$unique_meta_values = array_diff( $meta_values, array_column( $already_migrated[ $entity_id ][ $meta_key ], 'meta_value' ) );
					if ( 0 === count( $unique_meta_values ) ) {
						continue;
					}
					if ( ! isset( $to_insert[ $entity_id ] ) ) {
						$to_insert[ $entity_id ] = array();
					}
					$to_insert[ $entity_id ][ $meta_key ] = $unique_meta_values;
				}
			}
		}

		return array( $to_insert, $to_update );
	}

	/**
	 * Helper method to build query used to fetch data from source meta table.
	 *
	 * @param array $entity_ids List of entity IDs to build meta query for.
	 *
	 * @return string Query that can be used to fetch data.
	 */
	private function build_meta_table_query( array $entity_ids ): string {
		global $wpdb;
		$source_meta_table        = $this->schema_config['source']['meta']['table_name'];
		$source_meta_key_column   = $this->schema_config['source']['meta']['meta_key_column'];
		$source_meta_value_column = $this->schema_config['source']['meta']['meta_value_column'];
		$source_entity_id_column  = $this->schema_config['source']['meta']['entity_id_column'];
		$order_by                 = "source.$source_entity_id_column ASC";

		$where_clause = "source.`$source_entity_id_column` IN (" . implode( ', ', array_fill( 0, count( $entity_ids ), '%d' ) ) . ')';

		$entity_table                  = $this->schema_config['source']['entity']['table_name'];
		$entity_id_column              = $this->schema_config['source']['entity']['id_column'];
		$entity_meta_id_mapping_column = $this->schema_config['source']['entity']['source_id_column'];

		if ( $this->schema_config['source']['excluded_keys'] ) {
			$key_placeholder = implode( ',', array_fill( 0, count( $this->schema_config['source']['excluded_keys'] ), '%s' ) );
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- $source_meta_key_column is escaped for backticks, $key_placeholder is hardcoded.
			$exclude_clause = $wpdb->prepare( "source.$source_meta_key_column NOT IN ( $key_placeholder )", $this->schema_config['source']['excluded_keys'] );
			$where_clause   = "$where_clause AND $exclude_clause";
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
		return $wpdb->prepare(
			"
SELECT
	source.`$source_entity_id_column` as source_entity_id,
	entity.`$entity_id_column` as entity_id,
	source.`$source_meta_key_column` as meta_key,
	source.`$source_meta_value_column` as meta_value
FROM `$source_meta_table` source
JOIN `$entity_table` entity ON entity.`$entity_meta_id_mapping_column` = source.`$source_entity_id_column`
WHERE $where_clause ORDER BY $order_by
",
			$entity_ids
		);
		// phpcs:enable
	}
}
