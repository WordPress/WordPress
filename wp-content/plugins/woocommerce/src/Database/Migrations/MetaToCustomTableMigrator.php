<?php
/**
 * Generic migration class to move any entity, entity_meta table combination to custom table.
 */

namespace Automattic\WooCommerce\Database\Migrations;

/**
 * Base class for implementing migrations from the standard WordPress meta table
 * to custom structured tables.
 *
 * @package Automattic\WooCommerce\Database\Migrations\CustomOrderTable
 */
abstract class MetaToCustomTableMigrator extends TableMigrator {

	/**
	 * Config for tables being migrated and migrated from. See __construct() for detailed config.
	 *
	 * @var array
	 */
	protected $schema_config;

	/**
	 * Meta config, see __construct for detailed config.
	 *
	 * @var array
	 */
	protected $meta_column_mapping;

	/**
	 * Column mapping from source table to destination custom table. See __construct for detailed config.
	 *
	 * @var array
	 */
	protected $core_column_mapping;

	/**
	 * MetaToCustomTableMigrator constructor.
	 */
	public function __construct() {
		$this->schema_config       = MigrationHelper::escape_schema_for_backtick( $this->get_schema_config() );
		$this->meta_column_mapping = $this->get_meta_column_config();
		$this->core_column_mapping = $this->get_core_column_mapping();
	}

	/**
	 * Specify schema config the source and destination table.
	 *
	 * @return array Schema, must of the form:
	 * array(
		'source' => array(
			'entity' => array(
				'table_name' => $source_table_name,
				'meta_rel_column' => $column_meta, Name of column in source table which is referenced by meta table.
				'destination_rel_column' => $column_dest, Name of column in source table which is refenced by destination table,
				'primary_key' => $primary_key, Primary key of the source table
			),
			'meta' => array(
				'table' => $meta_table_name,
				'meta_key_column' => $meta_key_column_name,
				'meta_value_column' => $meta_value_column_name,
				'entity_id_column' => $entity_id_column, Name of the column having entity IDs.
			),
		),
		'destination' => array(
			'table_name' => $table_name, Name of destination table,
			'source_rel_column' => $column_source_id, Name of the column in destination table which is referenced by source table.
			'primary_key' => $table_primary_key,
			'primary_key_type' => $type bool|int|string|decimal
		)
	 */
	abstract protected function get_schema_config(): array;

	/**
	 * Specify column config from the source table.
	 *
	 * @return array Config, must be of the form:
	 * array(
	 *  '$source_column_name_1' => array( // $source_column_name_1 is column name in source table, or a select statement.
	 *      'type' => 'type of value, could be string/int/date/float.',
	 *      'destination' => 'name of the column in column name where this data should be inserted in.',
	 *  ),
	 *  '$source_column_name_2' => array(
	 *          ......
	 *  ),
	 *  ....
	 * ).
	 */
	abstract protected function get_core_column_mapping(): array;

	/**
	 * Specify meta keys config from source meta table.
	 *
	 * @return array Config, must be of the form.
	 * array(
	 *  '$meta_key_1' => array(  // $meta_key_1 is the name of meta_key in source meta table.
	 *          'type' => 'type of value, could be string/int/date/float',
	 *          'destination' => 'name of the column in column name where this data should be inserted in.',
	 *  ),
	 *  '$meta_key_2' => array(
	 *          ......
	 *  ),
	 *  ....
	 * ).
	 */
	abstract protected function get_meta_column_config(): array;

	/**
	 * Generate SQL for data insertion.
	 *
	 * @param array $batch Data to generate queries for. Will be 'data' array returned by `$this->fetch_data_for_migration_for_ids()` method.
	 *
	 * @return string Generated queries for insertion for this batch, would be of the form:
	 * INSERT IGNORE INTO $table_name ($columns) values
	 *  ($value for row 1)
	 *  ($value for row 2)
	 * ...
	 */
	private function generate_insert_sql_for_batch( array $batch ): string {
		$table = $this->schema_config['destination']['table_name'];

		list( $value_sql, $column_sql ) = $this->generate_column_clauses( array_merge( $this->core_column_mapping, $this->meta_column_mapping ), $batch );

		return "INSERT INTO $table (`$column_sql`) VALUES $value_sql;"; // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, -- $insert_query is hardcoded, $value_sql is already escaped.
	}

	/**
	 * Generate SQL for data updating.
	 *
	 * @param array $batch Data to generate queries for. Will be `data` array returned by fetch_data_for_migration_for_ids() method.
	 *
	 * @param array $entity_row_mapping Maps rows to update data with their original IDs. Will be returned by `generate_update_sql_for_batch`.
	 *
	 * @return string Generated queries for batch update. Would be of the form:
	 * INSERT INTO $table ( $columns ) VALUES
	 *  ($value for row 1)
	 *  ($valye for row 2)
	 * ...
	 * ON DUPLICATE KEY UPDATE
	 * $column1 = VALUES($column1)
	 * $column2 = VALUES($column2)
	 * ...
	 */
	private function generate_update_sql_for_batch( array $batch, array $entity_row_mapping ): string {
		$table = $this->schema_config['destination']['table_name'];

		$destination_primary_id_schema = $this->get_destination_table_primary_id_schema();
		foreach ( $batch as $entity_id => $row ) {
			$batch[ $entity_id ][ $destination_primary_id_schema['destination_primary_key']['destination'] ] = $entity_row_mapping[ $entity_id ]->destination_id;
		}

		list( $value_sql, $column_sql, $columns ) = $this->generate_column_clauses(
			array_merge( $destination_primary_id_schema, $this->core_column_mapping, $this->meta_column_mapping ),
			$batch
		);

		$duplicate_update_key_statement = MigrationHelper::generate_on_duplicate_statement_clause( $columns );

		return "INSERT INTO $table (`$column_sql`) VALUES $value_sql $duplicate_update_key_statement;";
	}

	/**
	 * Generate schema for primary ID column of destination table.
	 *
	 * @return array[] Schema for primary ID column.
	 */
	private function get_destination_table_primary_id_schema(): array {
		return array(
			'destination_primary_key' => array(
				'destination' => $this->schema_config['destination']['primary_key'],
				'type'        => $this->schema_config['destination']['primary_key_type'],
			),
		);
	}

	/**
	 * Generate values and columns clauses to be used in INSERT and INSERT..ON DUPLICATE KEY UPDATE statements.
	 *
	 * @param array $columns_schema Columns config for destination table.
	 * @param array $batch Actual data to migrate as returned by `data` in `fetch_data_for_migration_for_ids` method.
	 *
	 * @return array SQL clause for values, columns placeholders, and columns.
	 */
	private function generate_column_clauses( array $columns_schema, array $batch ): array {
		global $wpdb;

		$columns      = array();
		$placeholders = array();
		foreach ( $columns_schema as $prev_column => $schema ) {
			if ( in_array( $schema['destination'], $columns, true ) ) {
				continue;
			}
			$columns[]      = $schema['destination'];
			$placeholders[] = MigrationHelper::get_wpdb_placeholder_for_type( $schema['type'] );
		}
		$placeholders = "'" . implode( "', '", $placeholders ) . "'";

		$values = array();
		foreach ( array_values( $batch ) as $row ) {
			$query_params = array();
			foreach ( $columns as $column ) {
				$query_params[] = $row[ $column ] ?? null;
			}
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $placeholders can only contain combination of placeholders described in MigrationHelper::get_wpdb_placeholder_for_type
			$value_string = '(' . $wpdb->prepare( $placeholders, $query_params ) . ')';
			$values[]     = $value_string;
		}

		$value_sql = implode( ',', $values );

		$column_sql = implode( '`, `', $columns );

		return array( $value_sql, $column_sql, $columns );
	}

	/**
	 * Migrate a batch of entities from the posts table to the corresponding table.
	 *
	 * @param array $entity_ids Ids of entities to migrate.
	 *
	 * @return void
	 */
	protected function process_migration_batch_for_ids_core( array $entity_ids ): void {
		$data = $this->fetch_data_for_migration_for_ids( $entity_ids );

		foreach ( $data['errors'] as $entity_id => $errors ) {
			foreach ( $errors as $column_name => $error_message ) {
				$this->add_error( "Error importing data for post with id $entity_id: column $column_name: $error_message" );
			}
		}

		if ( count( $data['data'] ) === 0 ) {
			return;
		}

		$entity_ids       = array_keys( $data['data'] );
		$existing_records = $this->get_already_existing_records( $entity_ids );

		$to_insert = array_diff_key( $data['data'], $existing_records );
		$this->process_insert_batch( $to_insert );

		$existing_records = array_filter(
			$existing_records,
			function( $record_data ) {
				return '1' === $record_data->modified;
			}
		);
		$to_update        = array_intersect_key( $data['data'], $existing_records );
		$this->process_update_batch( $to_update, $existing_records );
	}

	/**
	 * Process batch for insertion into destination table.
	 *
	 * @param array $batch Data to insert, will be of the form as returned by `data` in `fetch_data_for_migration_for_ids`.
	 */
	private function process_insert_batch( array $batch ): void {
		if ( 0 === count( $batch ) ) {
			return;
		}

		$queries = $this->generate_insert_sql_for_batch( $batch );
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Queries should already be prepared.
		$processed_rows_count = $this->db_query( $queries );
		$this->maybe_add_insert_or_update_error( 'insert', $processed_rows_count );
	}

	/**
	 * Process batch for update into destination table.
	 *
	 * @param array $batch Data to insert, will be of the form as returned by `data` in `fetch_data_for_migration_for_ids`.
	 * @param array $ids_mapping Maps rows to update data with their original IDs.
	 */
	private function process_update_batch( array $batch, array $ids_mapping ): void {
		if ( 0 === count( $batch ) ) {
			return;
		}

		$queries = $this->generate_update_sql_for_batch( $batch, $ids_mapping );
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Queries should already be prepared.
		$processed_rows_count = $this->db_query( $queries ) / 2;
		$this->maybe_add_insert_or_update_error( 'update', $processed_rows_count );
	}


	/**
	 * Fetch data for migration.
	 *
	 * @param array $entity_ids Entity IDs to fetch data for.
	 *
	 * @return array[] Data along with errors (if any), will of the form:
	 * array(
	 *  'data' => array(
	 *      'id_1' => array( 'column1' => value1, 'column2' => value2, ...),
	 *      ...,
	 *   ),
	 *  'errors' => array(
	 *      'id_1' => array( 'column1' => error1, 'column2' => value2, ...),
	 *      ...,
	 * )
	 */
	private function fetch_data_for_migration_for_ids( array $entity_ids ): array {
		if ( empty( $entity_ids ) ) {
			return array(
				'data'   => array(),
				'errors' => array(),
			);
		}

		$entity_table_query = $this->build_entity_table_query( $entity_ids );
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Output of $this->build_entity_table_query is already prepared.
		$entity_data = $this->db_get_results( $entity_table_query );
		if ( empty( $entity_data ) ) {
			return array(
				'data'   => array(),
				'errors' => array(),
			);
		}
		$entity_meta_rel_ids = array_column( $entity_data, 'entity_meta_rel_id' );

		$meta_table_query = $this->build_meta_data_query( $entity_meta_rel_ids );
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Output of $this->build_meta_data_query is already prepared.
		$meta_data = $this->db_get_results( $meta_table_query );

		return $this->process_and_sanitize_data( $entity_data, $meta_data );
	}

	/**
	 * Fetch id mappings for records that are already inserted in the destination table.
	 *
	 * @param array $entity_ids List of entity IDs to verify.
	 *
	 * @return array Already migrated entities, would be of the form
	 * array(
	 *      '$source_id1' => array(
	 *          'source_id' => $source_id1,
	 *          'destination_id' => $destination_id1
	 *          'modified' => 0 if it can be determined that the row doesn't need update, 1 otherwise
	 *      ),
	 *      ...
	 * )
	 */
	protected function get_already_existing_records( array $entity_ids ): array {
		global $wpdb;

		$source_table                   = $this->schema_config['source']['entity']['table_name'];
		$source_destination_join_column = $this->schema_config['source']['entity']['destination_rel_column'];
		$source_primary_key_column      = $this->schema_config['source']['entity']['primary_key'];

		$destination_table              = $this->schema_config['destination']['table_name'];
		$destination_source_join_column = $this->schema_config['destination']['source_rel_column'];
		$destination_primary_key_column = $this->schema_config['destination']['primary_key'];

		$entity_id_placeholder = implode( ',', array_fill( 0, count( $entity_ids ), '%d' ) );

		// Additional SQL to check if the row needs update according to the column mapping.
		// The IFNULL and CHAR(0) "hack" is needed because NULLs can't be directly compared in SQL.
		$modified_selector   = array();
		$core_column_mapping = array_filter(
			$this->core_column_mapping,
			function( $mapping ) {
				return ! isset( $mapping['select_clause'] );
			}
		);
		foreach ( $core_column_mapping as $column_name => $mapping ) {
			if ( $column_name === $source_primary_key_column ) {
				continue;
			}
			$modified_selector[] =
				"IFNULL(source.$column_name,CHAR(0)) != IFNULL(destination.{$mapping['destination']},CHAR(0))"
				. ( 'string' === $mapping['type'] ? ' COLLATE ' . $wpdb->collate : '' );
		}

		if ( empty( $modified_selector ) ) {
			$modified_selector = ', 1 AS modified';
		} else {
			$modified_selector = trim( implode( ' OR ', $modified_selector ) );
			$modified_selector = ", if( $modified_selector, 1, 0 ) AS modified";
		}

		$additional_where = $this->get_additional_where_clause_for_get_data_to_insert_or_update( $entity_ids );

		$already_migrated_entity_ids = $this->db_get_results(
			$wpdb->prepare(
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- All columns and table names are hardcoded.
				"
SELECT source.`$source_primary_key_column` as source_id, destination.`$destination_primary_key_column` as destination_id $modified_selector
FROM `$destination_table` destination
JOIN `$source_table` source ON source.`$source_destination_join_column` = destination.`$destination_source_join_column`
WHERE source.`$source_primary_key_column` IN ( $entity_id_placeholder ) $additional_where
",
				$entity_ids
			)
		// phpcs:enable
		);

		return array_column( $already_migrated_entity_ids, null, 'source_id' );
	}

	/**
	 * Get additional string to be appended to the WHERE clause of the SQL query used by get_data_to_insert_or_update.
	 *
	 * @param array $entity_ids The ids of the entities being inserted or updated.
	 * @return string Additional string for the WHERE clause, must either be empty or start with "AND" or "OR".
	 */
	protected function get_additional_where_clause_for_get_data_to_insert_or_update( array $entity_ids ): string {
		return '';
	}

	/**
	 * Helper method to build query used to fetch data from core source table.
	 *
	 * @param array $entity_ids List of entity IDs to fetch.
	 *
	 * @return string Query that can be used to fetch data.
	 */
	private function build_entity_table_query( array $entity_ids ): string {
		global $wpdb;

		$source_entity_table       = $this->schema_config['source']['entity']['table_name'];
		$source_meta_rel_id_column = "`$source_entity_table`.`{$this->schema_config['source']['entity']['meta_rel_column']}`";
		$source_primary_key_column = "`$source_entity_table`.`{$this->schema_config['source']['entity']['primary_key']}`";

		$where_clause = "$source_primary_key_column IN (" . implode( ',', array_fill( 0, count( $entity_ids ), '%d' ) ) . ')';
		$entity_keys  = array();
		foreach ( $this->core_column_mapping as $column_name => $column_schema ) {
			if ( isset( $column_schema['select_clause'] ) ) {
				$select_clause = $column_schema['select_clause'];
				$entity_keys[] = "$select_clause AS $column_name";
			} else {
				$entity_keys[] = "$source_entity_table.$column_name";
			}
		}
		$entity_column_string = implode( ', ', $entity_keys );
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- $source_meta_rel_id_column, $source_destination_rel_id_column etc is escaped for backticks. $where clause and $order_by should already be escaped.
		$query = $wpdb->prepare(
			"
SELECT
	$source_meta_rel_id_column as entity_meta_rel_id,
    $source_primary_key_column as primary_key_id,
	$entity_column_string
FROM `$source_entity_table`
WHERE $where_clause;
",
			$entity_ids
		);

		// phpcs:enable

		return $query;
	}

	/**
	 * Helper method to build query that will be used to fetch data from source meta table.
	 *
	 * @param array $entity_ids List of IDs to fetch metadata for.
	 *
	 * @return string Query for fetching meta data.
	 */
	private function build_meta_data_query( array $entity_ids ): string {
		global $wpdb;

		$meta_table                = $this->schema_config['source']['meta']['table_name'];
		$meta_keys                 = array_keys( $this->meta_column_mapping );
		$meta_key_column           = $this->schema_config['source']['meta']['meta_key_column'];
		$meta_value_column         = $this->schema_config['source']['meta']['meta_value_column'];
		$meta_table_relational_key = $this->schema_config['source']['meta']['entity_id_column'];

		$meta_column_string = implode( ', ', array_fill( 0, count( $meta_keys ), '%s' ) );
		$entity_id_string   = implode( ', ', array_fill( 0, count( $entity_ids ), '%d' ) );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- $meta_table_relational_key, $meta_key_column, $meta_value_column and $meta_table is escaped for backticks. $entity_id_string and $meta_column_string are placeholders.
		$query = $wpdb->prepare(
			"
SELECT `$meta_table_relational_key` as entity_id, `$meta_key_column` as meta_key, `$meta_value_column` as meta_value
FROM `$meta_table`
WHERE
	`$meta_table_relational_key` IN ( $entity_id_string )
	AND `$meta_key_column` IN ( $meta_column_string );
",
			array_merge(
				$entity_ids,
				$meta_keys
			)
		);

		// phpcs:enable

		return $query;
	}

	/**
	 * Helper function to validate and combine data before we try to insert.
	 *
	 * @param array $entity_data Data from source table.
	 * @param array $meta_data Data from meta table.
	 *
	 * @return array[] Validated and combined data with errors.
	 */
	private function process_and_sanitize_data( array $entity_data, array $meta_data ): array {
		$sanitized_entity_data = array();
		$error_records         = array();
		$this->process_and_sanitize_entity_data( $sanitized_entity_data, $error_records, $entity_data );
		$this->processs_and_sanitize_meta_data( $sanitized_entity_data, $error_records, $meta_data );

		return array(
			'data'   => $sanitized_entity_data,
			'errors' => $error_records,
		);
	}

	/**
	 * Helper method to sanitize core source table.
	 *
	 * @param array $sanitized_entity_data Array containing sanitized data for insertion.
	 * @param array $error_records Error records.
	 * @param array $entity_data Original source data.
	 */
	private function process_and_sanitize_entity_data( array &$sanitized_entity_data, array &$error_records, array $entity_data ): void {
		foreach ( $entity_data as $entity ) {
			$row_data = array();
			foreach ( $this->core_column_mapping as $column_name => $schema ) {
				$custom_table_column_name = $schema['destination'] ?? $column_name;
				$value                    = $entity->$column_name;
				$value                    = $this->validate_data( $value, $schema['type'] );
				if ( is_wp_error( $value ) ) {
					$error_records[ $entity->primary_key_id ][ $custom_table_column_name ] = $value->get_error_code();
				} else {
					$row_data[ $custom_table_column_name ] = $value;
				}
			}
			$sanitized_entity_data[ $entity->entity_meta_rel_id ] = $row_data;
		}
	}

	/**
	 * Helper method to sanitize soure meta data.
	 *
	 * @param array $sanitized_entity_data Array containing sanitized data for insertion.
	 * @param array $error_records Error records.
	 * @param array $meta_data Original source data.
	 */
	private function processs_and_sanitize_meta_data( array &$sanitized_entity_data, array &$error_records, array $meta_data ): void {
		foreach ( $meta_data as $datum ) {
			$column_schema = $this->meta_column_mapping[ $datum->meta_key ];
			if ( isset( $sanitized_entity_data[ $datum->entity_id ][ $column_schema['destination'] ] ) ) {
				// We pick only the first meta if there are duplicates for a flat column, to be consistent with WP core behavior in handing duplicate meta which are marked as unique.
				continue;
			}
			$value = $this->validate_data( $datum->meta_value, $column_schema['type'] );
			if ( is_wp_error( $value ) ) {
				$error_records[ $datum->entity_id ][ $column_schema['destination'] ] = "{$value->get_error_code()}: {$value->get_error_message()}";
			} else {
				$sanitized_entity_data[ $datum->entity_id ][ $column_schema['destination'] ] = $value;
			}
		}
	}

	/**
	 * Validate and transform data so that we catch as many errors as possible before inserting.
	 *
	 * @param mixed  $value Actual data value.
	 * @param string $type Type of data, could be decimal, int, date, string.
	 *
	 * @return float|int|mixed|string|\WP_Error
	 */
	private function validate_data( $value, string $type ) {
		switch ( $type ) {
			case 'decimal':
				$value = wc_format_decimal( $value, false, true );
				break;
			case 'int':
				$value = (int) $value;
				break;
			case 'bool':
				$value = wc_string_to_bool( $value );
				break;
			case 'date':
				try {
					if ( '' === $value ) {
						$value = null;
					} else {
						$value = ( new \DateTime( $value ) )->format( 'Y-m-d H:i:s' );
					}
				} catch ( \Exception $e ) {
					return new \WP_Error( $e->getMessage() );
				}
				break;
			case 'date_epoch':
				try {
					if ( '' === $value ) {
						$value = null;
					} else {
						$value = ( new \DateTime( "@$value" ) )->format( 'Y-m-d H:i:s' );
					}
				} catch ( \Exception $e ) {
					return new \WP_Error( $e->getMessage() );
				}
				break;
		}

		return $value;
	}

	/**
	 * Verify whether data was migrated properly for given IDs.
	 *
	 * @param array $source_ids List of source IDs.
	 *
	 * @return array List of IDs along with columns that failed to migrate.
	 */
	public function verify_migrated_data( array $source_ids ) : array {
		global $wpdb;
		$query = $this->build_verification_query( $source_ids );
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $query should already be prepared.
		$results = $wpdb->get_results( $query, ARRAY_A );
		$results = $this->fill_source_metadata( $results, $source_ids );
		return $this->verify_data( $results );
	}

	/**
	 * Generate query to fetch data from both source and destination tables. Use the results in `verify_data` to verify if data was migrated properly.
	 *
	 * @param array $source_ids Array of IDs in source table.
	 *
	 * @return string SELECT statement.
	 */
	protected function build_verification_query( $source_ids ) {
		$source_table                  = $this->schema_config['source']['entity']['table_name'];
		$destination_table             = $this->schema_config['destination']['table_name'];
		$destination_source_rel_column = $this->schema_config['destination']['source_rel_column'];
		$source_destination_rel_column = $this->schema_config['source']['entity']['destination_rel_column'];

		$source_destination_join_clause = "$destination_table ON $destination_table.$destination_source_rel_column = $source_table.$source_destination_rel_column";

		$meta_select_clauses        = array();
		$source_select_clauses      = array();
		$destination_select_clauses = array();

		foreach ( $this->core_column_mapping as $column_name => $schema ) {
			$source_select_column         = isset( $schema['select_clause'] ) ? $schema['select_clause'] : "$source_table.$column_name";
			$source_select_clauses[]      = "$source_select_column as {$source_table}_{$column_name}";
			$destination_select_clauses[] = "$destination_table.{$schema['destination']} as {$destination_table}_{$schema['destination']}";
		}

		foreach ( $this->meta_column_mapping as $meta_key => $schema ) {
			$destination_select_clauses[] = "$destination_table.{$schema['destination']} as {$destination_table}_{$schema['destination']}";
		}

		$select_clause = implode( ', ', array_merge( $source_select_clauses, $meta_select_clauses, $destination_select_clauses ) );

		$where_clause = $this->get_where_clause_for_verification( $source_ids );

		return "
SELECT $select_clause
FROM $source_table
    LEFT JOIN $source_destination_join_clause
WHERE $where_clause
";
	}

	/**
	 * Fill source metadata for given IDs for verification. This will return filled data in following format:
	 * [
	 *    {
	 *      $source_table_$source_column: $value,
	 *      ...,
	 *      $destination_table_$destination_column: $value,
	 *      ...
	 *      meta_source_{$destination_column_name1}: $meta_value,
	 *      ...
	 *    },
	 *   ...
	 * ]
	 *
	 * @param array $results    Entity data from both source and destination table.
	 * @param array $source_ids List of source IDs.
	 *
	 * @return array Filled $results param with source metadata.
	 */
	private function fill_source_metadata( $results, $source_ids ) {
		global $wpdb;
		$meta_table            = $this->schema_config['source']['meta']['table_name'];
		$meta_entity_id_column = $this->schema_config['source']['meta']['entity_id_column'];
		$meta_key_column       = $this->schema_config['source']['meta']['meta_key_column'];
		$meta_value_column     = $this->schema_config['source']['meta']['meta_value_column'];
		$meta_id_column        = $this->schema_config['source']['meta']['meta_id_column'];
		$meta_columns          = array_keys( $this->meta_column_mapping );

		$meta_columns_placeholder = implode( ', ', array_fill( 0, count( $meta_columns ), '%s' ) );
		$source_ids_placeholder   = implode( ', ', array_fill( 0, count( $source_ids ), '%d' ) );

		$query = $wpdb->prepare(
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
			"SELECT $meta_entity_id_column as entity_id, $meta_key_column as meta_key, $meta_value_column as meta_value
			FROM $meta_table
			WHERE $meta_entity_id_column IN ($source_ids_placeholder)
			AND $meta_key_column IN ($meta_columns_placeholder)
			ORDER BY $meta_id_column ASC",
			array_merge( $source_ids, $meta_columns )
		);
		//phpcs:enable

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$meta_data            = $wpdb->get_results( $query, ARRAY_A );
		$source_metadata_rows = array();
		foreach ( $meta_data as $meta_datum ) {
			if ( ! isset( $source_metadata_rows[ $meta_datum['entity_id'] ] ) ) {
				$source_metadata_rows[ $meta_datum['entity_id'] ] = array();
			}
			$destination_column = $this->meta_column_mapping[ $meta_datum['meta_key'] ]['destination'];
			$alias              = "meta_source_{$destination_column}";
			if ( isset( $source_metadata_rows[ $meta_datum['entity_id'] ][ $alias ] ) ) {
				// Only process first value, duplicate values mapping to flat columns are ignored to be consistent with WP core.
				continue;
			}
			$source_metadata_rows[ $meta_datum['entity_id'] ][ $alias ] = $meta_datum['meta_value'];
		}
		foreach ( $results as $index => $result_row ) {
			$source_id         = $result_row[ $this->schema_config['source']['entity']['table_name'] . '_' . $this->schema_config['source']['entity']['primary_key'] ];
			$results[ $index ] = array_merge( $result_row, ( $source_metadata_rows[ $source_id ] ?? array() ) );
		}
		return $results;
	}

	/**
	 * Helper function to generate where clause for fetching data for verification.
	 *
	 * @param array $source_ids Array of IDs from source table.
	 *
	 * @return string WHERE clause.
	 */
	protected function get_where_clause_for_verification( $source_ids ) {
		global $wpdb;
		$source_primary_id_column = $this->schema_config['source']['entity']['primary_key'];
		$source_table             = $this->schema_config['source']['entity']['table_name'];
		$source_ids_placeholder   = implode( ', ', array_fill( 0, count( $source_ids ), '%d' ) );

		return $wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
			"$source_table.$source_primary_id_column IN ($source_ids_placeholder)",
			$source_ids
		);
	}

	/**
	 * Verify data from both source and destination tables and check if they were migrated properly.
	 *
	 * @param array $collected_data Collected data in array format, should be in same structure as returned from query in `$this->build_verification_query`.
	 *
	 * @return array Array of failed IDs if any, along with columns/meta_key names.
	 */
	protected function verify_data( $collected_data ) {
		$failed_ids = array();
		foreach ( $collected_data as $row ) {
			$failed_ids = $this->verify_entity_columns( $row, $failed_ids );
			$failed_ids = $this->verify_meta_columns( $row, $failed_ids );
		}

		return $failed_ids;
	}

	/**
	 * Helper method to verify and compare core columns.
	 *
	 * @param array $row        Both migrated and source data for a single row.
	 * @param array $failed_ids Array of failed IDs.
	 *
	 * @return array Array of failed IDs if any, along with columns/meta_key names.
	 */
	private function verify_entity_columns( $row, $failed_ids ) {
		$primary_key_column = "{$this->schema_config['source']['entity']['table_name']}_{$this->schema_config['source']['entity']['primary_key']}";
		foreach ( $this->core_column_mapping as $column_name => $schema ) {
			$source_alias      = "{$this->schema_config['source']['entity']['table_name']}_$column_name";
			$destination_alias = "{$this->schema_config['destination']['table_name']}_{$schema['destination']}";
			$row               = $this->pre_process_row( $row, $schema, $source_alias, $destination_alias );
			if ( $row[ $source_alias ] !== $row[ $destination_alias ] ) {
				if ( ! isset( $failed_ids[ $row[ $primary_key_column ] ] ) ) {
					$failed_ids[ $row[ $primary_key_column ] ] = array();
				}
				$failed_ids[ $row[ $primary_key_column ] ][] = array(
					'column'         => $column_name,
					'original_value' => $row[ $source_alias ],
					'new_value'      => $row[ $destination_alias ],
				);
			}
		}

		return $failed_ids;
	}

	/**
	 * Helper method to verify meta columns.
	 *
	 * @param array $row        Both migrated and source data for a single row.
	 * @param array $failed_ids Array of failed IDs.
	 *
	 * @return array Array of failed IDs if any, along with columns/meta_key names.
	 */
	private function verify_meta_columns( $row, $failed_ids ) {
		$primary_key_column = "{$this->schema_config['source']['entity']['table_name']}_{$this->schema_config['source']['entity']['primary_key']}";
		foreach ( $this->meta_column_mapping as $meta_key => $schema ) {
			$meta_alias        = "meta_source_{$schema['destination']}";
			$destination_alias = "{$this->schema_config['destination']['table_name']}_{$schema['destination']}";
			$row               = $this->pre_process_row( $row, $schema, $meta_alias, $destination_alias );
			if ( $row[ $meta_alias ] !== $row[ $destination_alias ] ) {
				if ( ! isset( $failed_ids[ $row[ $primary_key_column ] ] ) ) {
					$failed_ids[ $row[ $primary_key_column ] ] = array();
				}
				$failed_ids[ $row[ $primary_key_column ] ][] = array(
					'column'         => $meta_key,
					'original_value' => $row[ $meta_alias ],
					'new_value'      => $row[ $destination_alias ],
				);
			}
		}

		return $failed_ids;
	}

	/**
	 * Helper method to pre-process rows to make sure we parse the correct type.
	 *
	 * @param array  $row Both migrated and source data for a single row.
	 * @param array  $schema Column schema.
	 * @param string $alias Name of source column.
	 * @param string $destination_alias Name of destination column.
	 *
	 * @return array Processed row.
	 */
	private function pre_process_row( $row, $schema, $alias, $destination_alias ) {
		if ( ! isset( $row[ $alias ] ) ) {
			$row[ $alias ] = $this->get_type_defaults( $schema['type'] );
		}
		if ( in_array( $schema['type'], array( 'int', 'decimal' ), true ) ) {
			if ( '' === $row[ $alias ] || null === $row[ $alias ] ) {
				$row[ $alias ] = 0; // $wpdb->prepare forces empty values to 0.
			}
			$row[ $alias ]             = wc_format_decimal( $row[ $alias ], false, true );
			$row[ $destination_alias ] = wc_format_decimal( $row[ $destination_alias ], false, true );
		}
		if ( 'bool' === $schema['type'] ) {
			$row[ $alias ]             = wc_string_to_bool( $row[ $alias ] );
			$row[ $destination_alias ] = wc_string_to_bool( $row[ $destination_alias ] );
		}
		if ( 'date_epoch' === $schema['type'] ) {
			if ( '' === $row[ $alias ] || null === $row[ $alias ] ) {
				$row[ $alias ] = null;
			} else {
				$row[ $alias ] = ( new \DateTime( "@{$row[ $alias ]}" ) )->format( 'Y-m-d H:i:s' );
			}
			if ( '0000-00-00 00:00:00' === $row[ $destination_alias ] ) {
				$row[ $destination_alias ] = null;
			}
		}
		return $row;
	}

	/**
	 * Helper method to get default value of a type.
	 *
	 * @param string $type Type.
	 *
	 * @return mixed Default value.
	 */
	private function get_type_defaults( $type ) {
		switch ( $type ) {
			case 'float':
			case 'int':
				return 0;
			case 'string':
				return '';
		}
	}
}
