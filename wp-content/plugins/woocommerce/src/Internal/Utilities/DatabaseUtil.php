<?php
/**
 * DatabaseUtil class file.
 */

namespace Automattic\WooCommerce\Internal\Utilities;

use DateTime;
use DateTimeZone;

/**
 * A class of utilities for dealing with the database.
 */
class DatabaseUtil {

	/**
	 * Wrapper for the WordPress dbDelta function, allows to execute a series of SQL queries.
	 *
	 * @param string $queries The SQL queries to execute.
	 * @param bool   $execute Ture to actually execute the queries, false to only simulate the execution.
	 * @return array The result of the execution (or simulation) from dbDelta.
	 */
	public function dbdelta( string $queries = '', bool $execute = true ): array {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		return dbDelta( $queries, $execute );
	}

	/**
	 * Given a set of table creation SQL statements, check which of the tables are currently missing in the database.
	 *
	 * @param string $creation_queries The SQL queries to execute ("CREATE TABLE" statements, same format as for dbDelta).
	 * @return array An array containing the names of the tables that currently don't exist in the database.
	 */
	public function get_missing_tables( string $creation_queries ): array {
		$dbdelta_output = $this->dbdelta( $creation_queries, false );
		$parsed_output  = $this->parse_dbdelta_output( $dbdelta_output );
		return $parsed_output['created_tables'];
	}

	/**
	 * Parses the output given by dbdelta and returns information about it.
	 *
	 * @param array $dbdelta_output The output from the execution of dbdelta.
	 * @return array[] An array containing a 'created_tables' key whose value is an array with the names of the tables that have been (or would have been) created.
	 */
	public function parse_dbdelta_output( array $dbdelta_output ): array {
		$created_tables = array();

		foreach ( $dbdelta_output as $table_name => $result ) {
			if ( "Created table $table_name" === $result ) {
				$created_tables[] = $table_name;
			}
		}

		return array( 'created_tables' => $created_tables );
	}

	/**
	 * Drops a database table.
	 *
	 * @param string $table_name The name of the table to drop.
	 * @param bool   $add_prefix True if the table name passed needs to be prefixed with $wpdb->prefix before processing.
	 * @return bool True on success, false on error.
	 */
	public function drop_database_table( string $table_name, bool $add_prefix = false ) {
		global $wpdb;

		if ( $add_prefix ) {
			$table_name = $wpdb->prefix . $table_name;
		}

		//phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->query( "DROP TABLE IF EXISTS `{$table_name}`" );
	}

	/**
	 * Drops a table index, if both the table and the index exist.
	 *
	 * @param string $table_name The name of the table that contains the index.
	 * @param string $index_name The name of the index to be dropped.
	 * @return bool True if the index has been dropped, false if either the table or the index don't exist.
	 */
	public function drop_table_index( string $table_name, string $index_name ): bool {
		global $wpdb;

		if ( empty( $this->get_index_columns( $table_name, $index_name ) ) ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.PreparedSQL
		$wpdb->query( "ALTER TABLE $table_name DROP INDEX $index_name" );
		return true;
	}

	/**
	 * Create a primary key for a table, only if the table doesn't have a primary key already.
	 *
	 * @param string $table_name Table name.
	 * @param array  $columns An array with the index column names.
	 * @return bool True if the key has been created, false if the table already had a primary key.
	 */
	public function create_primary_key( string $table_name, array $columns ) {
		global $wpdb;

		if ( ! empty( $this->get_index_columns( $table_name ) ) ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.PreparedSQL
		$wpdb->query( "ALTER TABLE $table_name ADD PRIMARY KEY(`" . join( '`,`', $columns ) . '`)' );
		return true;
	}

	/**
	 * Get the columns of a given table index, or of the primary key.
	 *
	 * @param string $table_name Table name.
	 * @param string $index_name Index name, empty string for the primary key.
	 * @return array The index columns. Empty array if the table or the index don't exist.
	 */
	public function get_index_columns( string $table_name, string $index_name = '' ): array {
		global $wpdb;

		if ( empty( $index_name ) ) {
			$index_name = 'PRIMARY';
		}

		// phpcs:disable WordPress.DB.PreparedSQL
		return $wpdb->get_col(
			"
SELECT column_name FROM INFORMATION_SCHEMA.STATISTICS
WHERE table_name='$table_name'
AND table_schema='" . DB_NAME . "'
AND index_name='$index_name'"
		);
		// phpcs:enable WordPress.DB.PreparedSQL
	}

	/**
	 * Formats an object value of type `$type` for inclusion in the database.
	 *
	 * @param mixed  $value Raw value.
	 * @param string $type  Data type.
	 * @return mixed
	 * @throws \Exception When an invalid type is passed.
	 */
	public function format_object_value_for_db( $value, string $type ) {
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
			case 'string':
				$value = strval( $value );
				break;
			case 'date':
				// Date properties are converted to the WP timezone (see WC_Data::set_date_prop() method), however
				// for our own tables we persist dates in GMT.
				$value = $value ? ( new DateTime( $value ) )->setTimezone( new DateTimeZone( '+00:00' ) )->format( 'Y-m-d H:i:s' ) : null;
				break;
			case 'date_epoch':
				$value = $value ? ( new DateTime( "@{$value}" ) )->format( 'Y-m-d H:i:s' ) : null;
				break;
			default:
				throw new \Exception( 'Invalid type received: ' . $type );
		}

		return $value;
	}

	/**
	 * Returns the `$wpdb` placeholder to use for data type `$type`.
	 *
	 * @param string $type Data type.
	 * @return string
	 * @throws \Exception When an invalid type is passed.
	 */
	public function get_wpdb_format_for_type( string $type ) {
		static $wpdb_placeholder_for_type = array(
			'int'        => '%d',
			'decimal'    => '%f',
			'string'     => '%s',
			'date'       => '%s',
			'date_epoch' => '%s',
			'bool'       => '%d',
		);

		if ( ! isset( $wpdb_placeholder_for_type[ $type ] ) ) {
			throw new \Exception( 'Invalid column type: ' . $type );
		}

		return $wpdb_placeholder_for_type[ $type ];
	}

	/**
	 * Generates ON DUPLICATE KEY UPDATE clause to be used in migration.
	 *
	 * @param array $columns List of column names.
	 *
	 * @return string SQL clause for INSERT...ON DUPLICATE KEY UPDATE
	 */
	public function generate_on_duplicate_statement_clause( array $columns ): string {
		$update_value_statements = array();
		foreach ( $columns as $column ) {
			$update_value_statements[] = "`$column` = VALUES( `$column` )";
		}
		$update_value_clause = implode( ', ', $update_value_statements );

		return "ON DUPLICATE KEY UPDATE $update_value_clause";
	}

	/**
	 * Hybrid of $wpdb->update and $wpdb->insert. It will try to update a row, and if it doesn't exist, it will insert it. This needs unique constraints to be set on the table on all ID columns.
	 *
	 * You can use this function only when:
	 * 1. There is only one unique constraint on the table. The constraint can contain multiple columns, but it must be the only one unique constraint.
	 * 2. The complete unique constraint must be part of the $data array.
	 * 3. You do not need the LAST_INSERT_ID() value.
	 *
	 * @param string $table_name Table name.
	 * @param array  $data Unescaped data to update (in column => value pairs).
	 * @param array  $format An array of formats to be mapped to each of the values in $data.
	 *
	 * @return int Returns the value of DB's  ON DUPLICATE KEY UPDATE clause.
	 */
	public function insert_on_duplicate_key_update( $table_name, $data, $format ) : int {
		global $wpdb;

		$columns             = array_keys( $data );
		$column_clause       = '`' . implode( '`, `', $columns ) . '`';
		$value_placeholders  = implode( ', ', array_values( $format ) );
		$on_duplicate_clause = $this->generate_on_duplicate_statement_clause( $columns );
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- Values are escaped in $wpdb->prepare.
		$sql = $wpdb->prepare(
			"
INSERT INTO $table_name ( $column_clause )
VALUES ( $value_placeholders )
$on_duplicate_clause
",
			array_values( $data )
		);
		// phpcs:enable
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql is prepared.
		return $wpdb->query( $sql );
	}

}
