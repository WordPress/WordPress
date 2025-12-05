<?php

namespace Yoast\WP\Lib\Migrations;

use Exception;
use Yoast\WP\Lib\Model;

/**
 * Yoast migrations adapter class.
 */
class Adapter {

	/**
	 * The version of this adapter.
	 *
	 * @var string
	 */
	private $version = '1.0';

	/**
	 * Whether or not a transaction has been started.
	 *
	 * @var bool
	 */
	private $in_transaction = false;

	/**
	 * Returns the current database name.
	 *
	 * @return string
	 */
	public function get_database_name() {
		global $wpdb;

		return $wpdb->dbname;
	}

	/**
	 * Checks support for migrations.
	 *
	 * @return bool
	 */
	public function supports_migrations() {
		return true;
	}

	/**
	 * Returns all column native types.
	 *
	 * @return array
	 */
	public function native_database_types() {
		$types = [
			'primary_key'   => [
				'name'  => 'integer',
				'limit' => 11,
				'null'  => false,
			],
			'string'        => [
				'name'  => 'varchar',
				'limit' => 255,
			],
			'text'          => [ 'name' => 'text' ],
			'tinytext'      => [ 'name' => 'tinytext' ],
			'mediumtext'    => [ 'name' => 'mediumtext' ],
			'integer'       => [
				'name'  => 'int',
				'limit' => 11,
			],
			'tinyinteger'   => [ 'name' => 'tinyint' ],
			'smallinteger'  => [ 'name' => 'smallint' ],
			'mediuminteger' => [ 'name' => 'mediumint' ],
			'biginteger'    => [ 'name' => 'bigint' ],
			'float'         => [ 'name' => 'float' ],
			'decimal'       => [
				'name'      => 'decimal',
				'scale'     => 0,
				'precision' => 10,
			],
			'datetime'      => [ 'name' => 'datetime' ],
			'timestamp'     => [ 'name' => 'timestamp' ],
			'time'          => [ 'name' => 'time' ],
			'date'          => [ 'name' => 'date' ],
			'binary'        => [ 'name' => 'blob' ],
			'tinybinary'    => [ 'name' => 'tinyblob' ],
			'mediumbinary'  => [ 'name' => 'mediumblob' ],
			'longbinary'    => [ 'name' => 'longblob' ],
			'boolean'       => [
				'name'  => 'tinyint',
				'limit' => 1,
			],
			'enum'          => [
				'name'   => 'enum',
				'values' => [],
			],
			'uuid'          => [
				'name'  => 'char',
				'limit' => 36,
			],
			'char'          => [ 'name' => 'char' ],
		];

		return $types;
	}

	/**
	 * Checks if a table exists.
	 *
	 * @param string $table The table name.
	 *
	 * @return bool
	 */
	public function has_table( $table ) {
		return $this->table_exists( $table );
	}

	/**
	 * Allows overriding the hardcoded schema table name constant in case of parallel migrations.
	 *
	 * @return string
	 */
	public function get_schema_version_table_name() {
		return Model::get_table_name( 'migrations' );
	}

	/**
	 * Create the schema table, if necessary.
	 *
	 * @return void
	 */
	public function create_schema_version_table() {
		if ( ! $this->has_table( $this->get_schema_version_table_name() ) ) {
			$t = $this->create_table( $this->get_schema_version_table_name() );
			$t->column( 'version', 'string', [ 'limit' => 191 ] );
			$t->finish();
			$this->add_index( $this->get_schema_version_table_name(), 'version', [ 'unique' => true ] );
		}
	}

	/**
	 * Starts a transaction.
	 *
	 * @return void
	 */
	public function start_transaction() {
		if ( $this->in_transaction() === false ) {
			$this->begin_transaction();
		}
	}

	/**
	 * Commits a transaction.
	 *
	 * @return void
	 */
	public function commit_transaction() {
		if ( $this->in_transaction() ) {
			$this->commit();
		}
	}

	/**
	 * Rollbacks a transaction.
	 *
	 * @return void
	 */
	public function rollback_transaction() {
		if ( $this->in_transaction() ) {
			$this->rollback();
		}
	}

	/**
	 * Quotes a table name string.
	 *
	 * @param string $text Table name.
	 *
	 * @return string
	 */
	public function quote_table( $text ) {
		return '`' . $text . '`';
	}

	/**
	 * Return the SQL definition of a column.
	 *
	 * @param string     $column_name The column name.
	 * @param string     $type        The type of the column.
	 * @param array|null $options     Column options.
	 *
	 * @return string
	 */
	public function column_definition( $column_name, $type, $options = null ) {
		$col = new Column( $this, $column_name, $type, $options );

		return $col->__toString();
	}

	/**
	 * Checks if a database exists.
	 *
	 * @param string $database The database name.
	 *
	 * @return bool
	 */
	public function database_exists( $database ) {
		$ddl    = 'SHOW DATABASES';
		$result = $this->select_all( $ddl );
		if ( \count( $result ) === 0 ) {
			return false;
		}
		foreach ( $result as $dbrow ) {
			if ( $dbrow['Database'] === $database ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Creates a database.
	 *
	 * @param string $db The database name.
	 *
	 * @return bool
	 */
	public function create_database( $db ) {
		if ( $this->database_exists( $db ) ) {
			return false;
		}
		$ddl    = \sprintf( 'CREATE DATABASE %s', $this->identifier( $db ) );
		$result = $this->query( $ddl );

		return $result === true;
	}

	/**
	 * Drops a database.
	 *
	 * @param string $db The database name.
	 *
	 * @return bool
	 */
	public function drop_database( $db ) {
		if ( ! $this->database_exists( $db ) ) {
			return false;
		}
		$ddl    = \sprintf( 'DROP DATABASE IF EXISTS %s', $this->identifier( $db ) );
		$result = $this->query( $ddl );

		return $result === true;
	}

	/**
	 * Checks if a table exists.
	 *
	 * @param string $table The table name.
	 *
	 * @return bool
	 */
	public function table_exists( $table ) {
		global $wpdb;

		// We need last error to be clear so we can check against it easily.
		$previous_last_error      = $wpdb->last_error;
		$previous_suppress_errors = $wpdb->suppress_errors;
		$wpdb->last_error         = '';
		$wpdb->suppress_errors    = true;

		$result = $wpdb->query( "SELECT * FROM $table LIMIT 1" );

		// Restore the last error, as this is not truly an error and we don't want to alarm people.
		$wpdb->last_error      = $previous_last_error;
		$wpdb->suppress_errors = $previous_suppress_errors;

		return $result !== false;
	}

	/**
	 * Wrapper to execute a query.
	 *
	 * @param string $query The query to run.
	 *
	 * @return bool
	 */
	public function execute( $query ) {
		return $this->query( $query );
	}

	/**
	 * Executes a query.
	 *
	 * @param string $query The query to run.
	 *
	 * @return bool Whether or not the query was performed succesfully.
	 */
	public function query( $query ) {
		global $wpdb;

		$query_type = $this->determine_query_type( $query );
		$data       = [];
		if ( $query_type === Constants::SQL_SELECT || $query_type === Constants::SQL_SHOW ) {
			$data = $wpdb->get_results( $query, \ARRAY_A );
			if ( $data === false ) {
				return false;
			}

			return $data;
		}
		else {
			// INSERT, DELETE, etc...
			$result = $wpdb->query( $query );
			if ( $result === false ) {
				return false;
			}
			if ( $query_type === Constants::SQL_INSERT ) {
				return $wpdb->insert_id;
			}

			return true;
		}
	}

	/**
	 * Returns a single result for a query.
	 *
	 * @param string $query The query to run.
	 *
	 * @return array|false An associative array of the result.
	 */
	public function select_one( $query ) {
		global $wpdb;

		$query_type = $this->determine_query_type( $query );
		if ( $query_type === Constants::SQL_SELECT || $query_type === Constants::SQL_SHOW ) {
			$result = $wpdb->query( $query );
			if ( $result === false ) {
				return false;
			}

			return $wpdb->last_result[0];
		}

		return false;
	}

	/**
	 * Returns all results for a query.
	 *
	 * @param string $query The query to run.
	 *
	 * @return array An array of associative arrays.
	 */
	public function select_all( $query ) {
		return $this->query( $query );
	}

	/**
	 * Use this method for non-SELECT queries.
	 * Or anything where you dont necessarily expect a result string, e.g. DROPs, CREATEs, etc.
	 *
	 * @param string $ddl The query to run.
	 *
	 * @return bool
	 */
	public function execute_ddl( $ddl ) {
		return $this->query( $ddl );
	}

	/**
	 * Drops a table
	 *
	 * @param string $table The table name.
	 *
	 * @return bool Whether or not the table was succesfully dropped.
	 */
	public function drop_table( $table ) {
		$ddl = \sprintf( 'DROP TABLE IF EXISTS %s', $this->identifier( $table ) );
		return $this->query( $ddl );
	}

	/**
	 * Creates a table.
	 *
	 * @param string $table_name The table name.
	 * @param array  $options    The options.
	 *
	 * @return Table
	 */
	public function create_table( $table_name, $options = [] ) {
		return new Table( $this, $table_name, $options );
	}

	/**
	 * Escapes a string for usage in queries.
	 *
	 * @param string $text The string.
	 *
	 * @return string
	 */
	public function quote_string( $text ) {
		global $wpdb;

		return $wpdb->_escape( $text );
	}

	/**
	 * Returns a quoted string.
	 *
	 * @param string $text The string.
	 *
	 * @return string
	 */
	public function identifier( $text ) {
		return '`' . $text . '`';
	}

	/**
	 * Renames a table.
	 *
	 * @param string $name     The current table name.
	 * @param string $new_name The new table name.
	 *
	 * @return bool
	 */
	public function rename_table( $name, $new_name ) {
		if ( empty( $name ) || empty( $new_name ) ) {
			return false;
		}
		$sql = \sprintf( 'RENAME TABLE %s TO %s', $this->identifier( $name ), $this->identifier( $new_name ) );

		return $this->execute_ddl( $sql );
	}

	/**
	 * Adds a column.
	 *
	 * @param string $table_name  The table name.
	 * @param string $column_name The column name.
	 * @param string $type        The column type.
	 * @param array  $options     Column options.
	 *
	 * @return bool
	 */
	public function add_column( $table_name, $column_name, $type, $options = [] ) {
		if ( empty( $table_name ) || empty( $column_name ) || empty( $type ) ) {
			return false;
		}
		// Default types.
		if ( ! \array_key_exists( 'limit', $options ) ) {
			$options['limit'] = null;
		}
		if ( ! \array_key_exists( 'precision', $options ) ) {
			$options['precision'] = null;
		}
		if ( ! \array_key_exists( 'scale', $options ) ) {
			$options['scale'] = null;
		}
		$sql  = \sprintf( 'ALTER TABLE %s ADD `%s` %s', $this->identifier( $table_name ), $column_name, $this->type_to_sql( $type, $options ) );
		$sql .= $this->add_column_options( $type, $options );

		return $this->execute_ddl( $sql );
	}

	/**
	 * Drops a column.
	 *
	 * @param string $table_name  The table name.
	 * @param string $column_name The column name.
	 *
	 * @return bool
	 */
	public function remove_column( $table_name, $column_name ) {
		$sql = \sprintf( 'ALTER TABLE %s DROP COLUMN %s', $this->identifier( $table_name ), $this->identifier( $column_name ) );

		return $this->execute_ddl( $sql );
	}

	/**
	 * Renames a column.
	 *
	 * @param string $table_name      The table name.
	 * @param string $column_name     The column name.
	 * @param string $new_column_name The new column name.
	 *
	 * @return bool
	 */
	public function rename_column( $table_name, $column_name, $new_column_name ) {
		if ( empty( $table_name ) || empty( $column_name ) || empty( $new_column_name ) ) {
			return false;
		}
		$column_info  = $this->column_info( $table_name, $column_name );
		$current_type = $column_info['type'];
		$sql          = \sprintf( 'ALTER TABLE %s CHANGE %s %s %s', $this->identifier( $table_name ), $this->identifier( $column_name ), $this->identifier( $new_column_name ), $current_type );
		$sql         .= $this->add_column_options( $current_type, $column_info );

		return $this->execute_ddl( $sql );
	}

	/**
	 * Changes a column.
	 *
	 * @param string $table_name  The table name.
	 * @param string $column_name The column name.
	 * @param string $type        The column type.
	 * @param array  $options     Column options.
	 *
	 * @return bool
	 */
	public function change_column( $table_name, $column_name, $type, $options = [] ) {
		if ( empty( $table_name ) || empty( $column_name ) || empty( $type ) ) {
			return false;
		}
		$column_info = $this->column_info( $table_name, $column_name );
		// Default types.
		if ( ! \array_key_exists( 'limit', $options ) ) {
			$options['limit'] = null;
		}
		if ( ! \array_key_exists( 'precision', $options ) ) {
			$options['precision'] = null;
		}
		if ( ! \array_key_exists( 'scale', $options ) ) {
			$options['scale'] = null;
		}
		$sql  = \sprintf( 'ALTER TABLE `%s` CHANGE `%s` `%s` %s', $table_name, $column_name, $column_name, $this->type_to_sql( $type, $options ) );
		$sql .= $this->add_column_options( $type, $options );

		return $this->execute_ddl( $sql );
	}

	/**
	 * Returns the database information for a column.
	 *
	 * @param string $table  The table name.
	 * @param string $column The column name.
	 *
	 * @return array|null
	 */
	public function column_info( $table, $column ) {
		if ( empty( $table ) || empty( $column ) ) {
			return null;
		}

		try {
			$sql    = \sprintf( "SHOW FULL COLUMNS FROM %s LIKE '%s'", $this->identifier( $table ), $column );
			$result = $this->select_one( $sql );
			if ( \is_array( $result ) ) {
				$result = \array_change_key_case( $result, \CASE_LOWER );
			}

			return $result;
		} catch ( Exception $e ) {
			return null;
		}
	}

	/**
	 * Adds an index.
	 *
	 * @param string       $table_name  The table name.
	 * @param array|string $column_name The column name(s).
	 * @param array        $options     Index options.
	 *
	 * @return bool
	 */
	public function add_index( $table_name, $column_name, $options = [] ) {
		if ( empty( $table_name ) || empty( $column_name ) ) {
			return false;
		}
		// Unique index?
		if ( \is_array( $options ) && \array_key_exists( 'unique', $options ) && $options['unique'] === true ) {
			$unique = true;
		}
		else {
			$unique = false;
		}

		// Did the user specify an index name?
		if ( \is_array( $options ) && \array_key_exists( 'name', $options ) ) {
			$index_name = $options['name'];
		}
		else {
			$index_name = $this->get_index_name( $table_name, $column_name );
		}

		if ( \strlen( $index_name ) > Constants::MYSQL_MAX_IDENTIFIER_LENGTH ) {
			return false;
		}

		if ( ! \is_array( $column_name ) ) {
			$column_names = [ $column_name ];
		}
		else {
			$column_names = $column_name;
		}

		$cols = [];
		foreach ( $column_names as $name ) {
			$cols[] = $this->identifier( $name );
		}
		$sql = \sprintf(
			'CREATE %sINDEX %s ON %s(%s)',
			( $unique === true ) ? 'UNIQUE ' : '',
			$this->identifier( $index_name ),
			$this->identifier( $table_name ),
			\implode( ', ', $cols )
		);

		return $this->execute_ddl( $sql );
	}

	/**
	 * Drops an index.
	 *
	 * @param string       $table_name  The table name.
	 * @param array|string $column_name The column name(s).
	 * @param array        $options     Index options.
	 *
	 * @return bool
	 */
	public function remove_index( $table_name, $column_name, $options = [] ) {
		if ( empty( $table_name ) || empty( $column_name ) ) {
			return false;
		}
		// Did the user specify an index name?
		if ( \is_array( $options ) && \array_key_exists( 'name', $options ) ) {
			$index_name = $options['name'];
		}
		else {
			$index_name = $this->get_index_name( $table_name, $column_name );
		}

		$sql = \sprintf( 'DROP INDEX %s ON %s', $this->identifier( $index_name ), $this->identifier( $table_name ) );

		return $this->execute_ddl( $sql );
	}

	/**
	 * Adds timestamps.
	 *
	 * @param string $table_name          The table name.
	 * @param string $created_column_name Created at column name.
	 * @param string $updated_column_name Updated at column name.
	 *
	 * @return bool
	 */
	public function add_timestamps( $table_name, $created_column_name, $updated_column_name ) {
		if ( empty( $table_name ) || empty( $created_column_name ) || empty( $updated_column_name ) ) {
			return false;
		}
		$created_at = $this->add_column( $table_name, $created_column_name, 'datetime' );
		$updated_at = $this->add_column(
			$table_name,
			$updated_column_name,
			'timestamp',
			[
				'null'    => false,
				'default' => 'CURRENT_TIMESTAMP',
				'extra'   => 'ON UPDATE CURRENT_TIMESTAMP',
			]
		);

		return $created_at && $updated_at;
	}

	/**
	 * Removes timestamps.
	 *
	 * @param string $table_name          The table name.
	 * @param string $created_column_name Created at column name.
	 * @param string $updated_column_name Updated at column name.
	 *
	 * @return bool Whether or not the timestamps were removed.
	 */
	public function remove_timestamps( $table_name, $created_column_name, $updated_column_name ) {
		if ( empty( $table_name ) || empty( $created_column_name ) || empty( $updated_column_name ) ) {
			return false;
		}
		$updated_at = $this->remove_column( $table_name, $created_column_name );
		$created_at = $this->remove_column( $table_name, $updated_column_name );

		return $created_at && $updated_at;
	}

	/**
	 * Checks an index.
	 *
	 * @param string       $table_name  The table name.
	 * @param array|string $column_name The column name(s).
	 * @param array        $options     Index options.
	 *
	 * @return bool Whether or not the index exists.
	 */
	public function has_index( $table_name, $column_name, $options = [] ) {
		if ( empty( $table_name ) || empty( $column_name ) ) {
			return false;
		}
		// Did the user specify an index name?
		if ( \is_array( $options ) && \array_key_exists( 'name', $options ) ) {
			$index_name = $options['name'];
		}
		else {
			$index_name = $this->get_index_name( $table_name, $column_name );
		}
		$indexes = $this->indexes( $table_name );
		foreach ( $indexes as $idx ) {
			if ( $idx['name'] === $index_name ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns all indexes of a table.
	 *
	 * @param string $table_name The table name.
	 *
	 * @return array
	 */
	public function indexes( $table_name ) {
		$sql     = \sprintf( 'SHOW KEYS FROM %s', $this->identifier( $table_name ) );
		$result  = $this->select_all( $sql );
		$indexes = [];
		foreach ( $result as $row ) {
			// Skip primary.
			if ( $row['Key_name'] === 'PRIMARY' ) {
				continue;
			}
			$indexes[] = [
				'name'   => $row['Key_name'],
				'unique' => (int) $row['Non_unique'] === 0,
			];
		}

		return $indexes;
	}

	/**
	 * Converts a type to sql. Default options:
	 * $limit = null, $precision = null, $scale = null
	 *
	 * @param string $type    The native type.
	 * @param array  $options The options.
	 *
	 * @return string The SQL type.
	 *
	 * @throws Exception If invalid arguments are supplied.
	 */
	public function type_to_sql( $type, $options = [] ) {
		$natives = $this->native_database_types();
		if ( ! \array_key_exists( $type, $natives ) ) {
			$error  = \sprintf( "Error:I dont know what column type of '%s' maps to for MySQL.", $type );
			$error .= "\nYou provided: {$type}\n";
			$error .= "Valid types are: \n";
			$types  = \array_keys( $natives );
			foreach ( $types as $t ) {
				if ( $t === 'primary_key' ) {
					continue;
				}
				$error .= "\t{$t}\n";
			}
			throw new Exception( $error );
		}
		$scale     = null;
		$precision = null;
		$limit     = null;
		if ( isset( $options['precision'] ) ) {
			$precision = $options['precision'];
		}
		if ( isset( $options['scale'] ) ) {
			$scale = $options['scale'];
		}
		if ( isset( $options['limit'] ) ) {
			$limit = $options['limit'];
		}
		if ( isset( $options['values'] ) ) {
			$values = $options['values'];
		}
		$native_type = $natives[ $type ];
		if ( \is_array( $native_type ) && \array_key_exists( 'name', $native_type ) ) {
			$column_type_sql = $native_type['name'];
		}
		else {
			return $native_type;
		}
		if ( $type === 'decimal' || $type === 'float' ) {
			// Ignore limit, use precison and scale.
			if ( $precision === null && \array_key_exists( 'precision', $native_type ) ) {
				$precision = $native_type['precision'];
			}
			if ( $scale === null && \array_key_exists( 'scale', $native_type ) ) {
				$scale = $native_type['scale'];
			}
			if ( $precision !== null ) {
				if ( \is_int( $scale ) ) {
					$column_type_sql .= \sprintf( '(%d, %d)', $precision, $scale );
				}
				else {
					$column_type_sql .= \sprintf( '(%d)', $precision );
				}
			}
			elseif ( $scale ) {
				throw new Exception( "Error adding $type column: precision cannot be empty if scale is specified" );
			}
		}
		elseif ( $type === 'enum' ) {
			if ( empty( $values ) ) {
				throw new Exception( 'Error adding enum column: there must be at least one value defined' );
			}
			else {
				$column_type_sql .= \sprintf(
					"('%s')",
					\implode( "','", \array_map( [ $this, 'quote_string' ], $values ) )
				);
			}
		}
		// Not a decimal column.
		if ( $limit === null && \array_key_exists( 'limit', $native_type ) ) {
			$limit = $native_type['limit'];
		}
		if ( $limit ) {
			$column_type_sql .= \sprintf( '(%d)', $limit );
		}

		return $column_type_sql;
	}

	/**
	 * Adds column options.
	 *
	 * @param string $type    The native type.
	 * @param array  $options The options.
	 *
	 * @return string The SQL statement for the column options.
	 *
	 * @throws Exception If invalid arguments are supplied.
	 */
	public function add_column_options( $type, $options ) {
		$sql = '';
		if ( ! \is_array( $options ) ) {
			return $sql;
		}
		if ( \array_key_exists( 'unsigned', $options ) && $options['unsigned'] === true ) {
			$sql .= ' UNSIGNED';
		}
		if ( \array_key_exists( 'character', $options ) ) {
			$sql .= \sprintf( ' CHARACTER SET %s', $this->identifier( $options['character'] ) );
		}
		if ( \array_key_exists( 'collate', $options ) ) {
			$sql .= \sprintf( ' COLLATE %s', $this->identifier( $options['collate'] ) );
		}
		if ( \array_key_exists( 'auto_increment', $options ) && $options['auto_increment'] === true ) {
			$sql .= ' auto_increment';
		}
		if ( \array_key_exists( 'default', $options ) && $options['default'] !== null ) {
			if ( $this->is_sql_method_call( $options['default'] ) ) {
				throw new Exception( 'MySQL does not support function calls as default values, constants only.' );
			}
			if ( \is_int( $options['default'] ) ) {
				$default_format = '%d';
			}
			elseif ( \is_bool( $options['default'] ) ) {
				$default_format = "'%d'";
			}
			elseif ( $options['default'] === 'CURRENT_TIMESTAMP' ) {
				$default_format = '%s';
			}
			else {
				$default_format = "'%s'";
			}
			$default_value = \sprintf( $default_format, $options['default'] );
			$sql          .= \sprintf( ' DEFAULT %s', $default_value );
		}
		if ( \array_key_exists( 'null', $options ) ) {
			if ( $options['null'] === false || $options['null'] === 'NO' ) {
				$sql .= ' NOT NULL';
			}
			elseif ( $type === 'timestamp' ) {
				$sql .= ' NULL';
			}
		}
		if ( \array_key_exists( 'comment', $options ) ) {
			$sql .= \sprintf( " COMMENT '%s'", $this->quote_string( $options['comment'] ) );
		}
		if ( \array_key_exists( 'extra', $options ) ) {
			$sql .= \sprintf( ' %s', $this->quote_string( $options['extra'] ) );
		}
		if ( \array_key_exists( 'after', $options ) ) {
			$sql .= \sprintf( ' AFTER %s', $this->identifier( $options['after'] ) );
		}

		return $sql;
	}

	/**
	 * Returns a list of all versions that have been migrated.
	 *
	 * @return string[] The version numbers that have been migrated.
	 */
	public function get_migrated_versions() {
		$result = $this->select_all( \sprintf( 'SELECT version FROM %s', $this->get_schema_version_table_name() ) );
		return \array_column( $result, 'version' );
	}

	/**
	 * Adds a migrated version.
	 *
	 * @param string $version The version.
	 *
	 * @return bool Whether or not the version was succesfully set.
	 */
	public function add_version( $version ) {
		$sql = \sprintf( "INSERT INTO %s (version) VALUES ('%s')", $this->get_schema_version_table_name(), $version );

		return $this->execute_ddl( $sql );
	}

	/**
	 * Removes a migrated version.
	 *
	 * @param string $version The version.
	 *
	 * @return bool Whether or not the version was succesfully removed.
	 */
	public function remove_version( $version ) {
		$sql = \sprintf( "DELETE FROM %s WHERE version = '%s'", $this->get_schema_version_table_name(), $version );

		return $this->execute_ddl( $sql );
	}

	/**
	 * Returns a message displaying the current version
	 *
	 * @return string
	 */
	public function __toString() {
		return self::class . ', version ' . $this->version;
	}

	/**
	 * Returns an index name.
	 *
	 * @param string $table_name  The table name.
	 * @param string $column_name The column name.
	 *
	 * @return string The index name.
	 */
	private function get_index_name( $table_name, $column_name ) {
		$name = \preg_replace( '/\\W/', '_', $table_name );
		$name = \preg_replace( '/\\_{2,}/', '_', $name );
		// If the column parameter is an array then the user wants to create a multi-column index.
		if ( \is_array( $column_name ) ) {
			$column_str = \implode( '_and_', $column_name );
		}
		else {
			$column_str = $column_name;
		}
		$name .= \sprintf( '_%s', $column_str );
		return $name;
	}

	/**
	 * Returns the type of a query.
	 *
	 * @param string $query The query to run.
	 *
	 * @return int The query type.
	 */
	private function determine_query_type( $query ) {
		$query = \strtolower( \trim( $query ) );
		$match = [];
		\preg_match( '/^(\\w)*/i', $query, $match );
		$type = $match[0];
		switch ( $type ) {
			case 'select':
				return Constants::SQL_SELECT;
			case 'update':
				return Constants::SQL_UPDATE;
			case 'delete':
				return Constants::SQL_DELETE;
			case 'insert':
				return Constants::SQL_INSERT;
			case 'alter':
				return Constants::SQL_ALTER;
			case 'drop':
				return Constants::SQL_DROP;
			case 'create':
				return Constants::SQL_CREATE;
			case 'show':
				return Constants::SQL_SHOW;
			case 'rename':
				return Constants::SQL_RENAME;
			case 'set':
				return Constants::SQL_SET;
			default:
				return Constants::SQL_UNKNOWN_QUERY_TYPE;
		}
	}

	/**
	 * Detect whether or not the string represents a function call and if so
	 * do not wrap it in single-quotes, otherwise do wrap in single quotes.
	 *
	 * @param string $text The string.
	 *
	 * @return bool Whether or not it's a SQL function call.
	 */
	private function is_sql_method_call( $text ) {
		$text = \trim( $text );
		if ( \substr( $text, -2, 2 ) === '()' ) {
			return true;
		}
		return false;
	}

	/**
	 * Checks if a transaction is active.
	 *
	 * @return bool
	 */
	private function in_transaction() {
		return $this->in_transaction;
	}

	/**
	 * Starts a transaction.
	 *
	 * @return void
	 *
	 * @throws Exception If a transaction was already started.
	 */
	private function begin_transaction() {
		global $wpdb;

		if ( $this->in_transaction === true ) {
			throw new Exception( 'Transaction already started' );
		}
		$wpdb->query( 'START TRANSACTION' );
		$this->in_transaction = true;
	}

	/**
	 * Commits a transaction.
	 *
	 * @return void
	 *
	 * @throws Exception If no transaction was strated.
	 */
	private function commit() {
		global $wpdb;

		if ( $this->in_transaction === false ) {
			throw new Exception( 'Transaction not started' );
		}
		$wpdb->query( 'COMMIT' );
		$this->in_transaction = false;
	}

	/**
	 * Rollbacks a transaction.
	 *
	 * @return void
	 *
	 * @throws Exception If no transaction was started.
	 */
	private function rollback() {
		global $wpdb;

		if ( $this->in_transaction === false ) {
			throw new Exception( 'Transaction not started' );
		}
		$wpdb->query( 'ROLLBACK' );
		$this->in_transaction = false;
	}
}
