<?php

namespace Yoast\WP\Lib\Migrations;

/**
 * Base migration class.
 */
abstract class Migration {

	/**
	 * The plugin this migration belongs to.
	 *
	 * @var string
	 */
	public static $plugin = 'unknown';

	/**
	 * The adapter.
	 *
	 * @var Adapter
	 */
	private $adapter;

	/**
	 * Performs the migration.
	 *
	 * @return void
	 */
	abstract public function up();

	/**
	 * Reverts the migration.
	 *
	 * @return void
	 */
	abstract public function down();

	/**
	 * Creates a new migration.
	 *
	 * @param Adapter $adapter The current adapter.
	 */
	public function __construct( Adapter $adapter ) {
		$this->set_adapter( $adapter );
	}

	/**
	 * Sets an adapter.
	 *
	 * @param Adapter $adapter The adapter to set.
	 *
	 * @return $this|null
	 */
	public function set_adapter( $adapter ) {
		if ( ! $adapter instanceof Adapter ) {
			return;
		}
		$this->adapter = $adapter;
		return $this;
	}

	/**
	 * Returns the current adapter.
	 *
	 * @return object
	 */
	public function get_adapter() {
		return $this->adapter;
	}

	/**
	 * Creates a database.
	 *
	 * @param string     $name    The name of the database.
	 * @param array|null $options The options.
	 *
	 * @return bool
	 */
	public function create_database( $name, $options = null ) {
		return $this->adapter->create_database( $name, $options );
	}

	/**
	 * Drops a database.
	 *
	 * @param string $name The name of the database.
	 *
	 * @return bool
	 */
	public function drop_database( $name ) {
		return $this->adapter->drop_database( $name );
	}

	/**
	 * Drops a table.
	 *
	 * @param string $table_name The name of the table.
	 *
	 * @return bool
	 */
	public function drop_table( $table_name ) {
		return $this->adapter->drop_table( $table_name );
	}

	/**
	 * Renames a table.
	 *
	 * @param string $name     The name of the table.
	 * @param string $new_name The new name of the table.
	 *
	 * @return bool
	 */
	public function rename_table( $name, $new_name ) {
		return $this->adapter->rename_table( $name, $new_name );
	}

	/**
	 * Renames a column.
	 *
	 * @param string $table_name      The name of the table.
	 * @param string $column_name     The column name.
	 * @param string $new_column_name The new column name.
	 *
	 * @return bool
	 */
	public function rename_column( $table_name, $column_name, $new_column_name ) {
		return $this->adapter->rename_column( $table_name, $column_name, $new_column_name );
	}

	/**
	 * Adds a column.
	 *
	 * @param string       $table_name  The name of the table.
	 * @param string       $column_name The column name.
	 * @param string       $type        The column type.
	 * @param array|string $options     The options.
	 *
	 * @return bool
	 */
	public function add_column( $table_name, $column_name, $type, $options = [] ) {
		return $this->adapter->add_column( $table_name, $column_name, $type, $options );
	}

	/**
	 * Removes a column.
	 *
	 * @param string $table_name  The name of the table.
	 * @param string $column_name The column name.
	 *
	 * @return bool
	 */
	public function remove_column( $table_name, $column_name ) {
		return $this->adapter->remove_column( $table_name, $column_name );
	}

	/**
	 * Changes a column.
	 *
	 * @param string       $table_name  The name of the table.
	 * @param string       $column_name The column name.
	 * @param string       $type        The column type.
	 * @param array|string $options     The options.
	 *
	 * @return bool
	 */
	public function change_column( $table_name, $column_name, $type, $options = [] ) {
		return $this->adapter->change_column( $table_name, $column_name, $type, $options );
	}

	/**
	 * Adds an index.
	 *
	 * @param string       $table_name  The name of the table.
	 * @param array|string $column_name The column name.
	 * @param array|string $options     The options.
	 *
	 * @return bool
	 */
	public function add_index( $table_name, $column_name, $options = [] ) {
		return $this->adapter->add_index( $table_name, $column_name, $options );
	}

	/**
	 * Removes an index.
	 *
	 * @param string       $table_name  The name of the table.
	 * @param array|string $column_name The column name.
	 * @param array|string $options     The options.
	 *
	 * @return bool
	 */
	public function remove_index( $table_name, $column_name, $options = [] ) {
		return $this->adapter->remove_index( $table_name, $column_name, $options );
	}

	/**
	 * Adds timestamps.
	 *
	 * @param string $table_name          The name of the table.
	 * @param string $created_column_name Created at column name.
	 * @param string $updated_column_name Updated at column name.
	 *
	 * @return bool
	 */
	public function add_timestamps( $table_name, $created_column_name = 'created_at', $updated_column_name = 'updated_at' ) {
		return $this->adapter->add_timestamps( $table_name, $created_column_name, $updated_column_name );
	}

	/**
	 * Removes timestamps.
	 *
	 * @param string $table_name          The name of the table.
	 * @param string $created_column_name Created at column name.
	 * @param string $updated_column_name Updated at column name.
	 *
	 * @return bool
	 */
	public function remove_timestamps( $table_name, $created_column_name = 'created_at', $updated_column_name = 'updated_at' ) {
		return $this->adapter->remove_timestamps( $table_name, $created_column_name, $updated_column_name );
	}

	/**
	 * Creates a table.
	 *
	 * @param string       $table_name The name of the table.
	 * @param array|string $options    The options.
	 *
	 * @return bool|Table
	 */
	public function create_table( $table_name, $options = [] ) {
		return $this->adapter->create_table( $table_name, $options );
	}

	/**
	 * Execute a query and return the first result.
	 *
	 * @param string $sql The query to run.
	 *
	 * @return array
	 */
	public function select_one( $sql ) {
		return $this->adapter->select_one( $sql );
	}

	/**
	 * Execute a query and return all results.
	 *
	 * @param string $sql The query to run.
	 *
	 * @return array
	 */
	public function select_all( $sql ) {
		return $this->adapter->select_all( $sql );
	}

	/**
	 * Execute a query.
	 *
	 * @param string $sql The query to run.
	 *
	 * @return bool
	 */
	public function query( $sql ) {
		return $this->adapter->query( $sql );
	}

	/**
	 * Returns a quoted string.
	 *
	 * @param string $str The string to quote.
	 *
	 * @return string
	 */
	public function quote_string( $str ) {
		return $this->adapter->quote_string( $str );
	}
}
