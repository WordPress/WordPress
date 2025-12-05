<?php

namespace Yoast\WP\Lib\Migrations;

use Exception;

/**
 * Yoast migrations table class.
 */
class Table {

	/**
	 * The adapter.
	 *
	 * @var Adapter
	 */
	private $adapter;

	/**
	 * The name
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The options
	 *
	 * @var array
	 */
	private $options;

	/**
	 * The SQL representation of this table.
	 *
	 * @var string
	 */
	private $sql = '';

	/**
	 * Whether or not the table has been initialized.
	 *
	 * @var bool
	 */
	private $initialized = false;

	/**
	 * The columns
	 *
	 * @var Column[]
	 */
	private $columns = [];

	/**
	 * The primary keys.
	 *
	 * @var string[]
	 */
	private $primary_keys = [];

	/**
	 * Whether or not to auto generate the id.
	 *
	 * @var bool
	 */
	private $auto_generate_id = true;

	/**
	 * Creates an instance of the Adapter.
	 *
	 * @param Adapter $adapter The current adapter.
	 * @param string  $name    The table name.
	 * @param array   $options The options.
	 *
	 * @throws Exception If invalid arguments are passed.
	 */
	public function __construct( $adapter, $name, $options = [] ) {
		// Sanity checks.
		if ( ! $adapter instanceof Adapter ) {
			throw new Exception( 'Invalid MySQL Adapter instance.' );
		}
		if ( ! $name ) {
			throw new Exception( "Invalid 'name' parameter" );
		}
		$this->adapter = $adapter;
		$this->name    = $name;
		$this->options = $options;
		$this->init_sql( $name, $options );
		if ( \array_key_exists( 'id', $options ) ) {
			if ( \is_bool( $options['id'] ) && $options['id'] === false ) {
				$this->auto_generate_id = false;
			}

			// If its a string then we want to auto-generate an integer-based
			// primary key with this name.
			if ( \is_string( $options['id'] ) ) {
				$this->auto_generate_id = true;
				$this->primary_keys[]   = $options['id'];
			}
		}
	}

	/**
	 * Create a column
	 *
	 * @param string $column_name The column name.
	 * @param string $type        The column type.
	 * @param array  $options     The options.
	 *
	 * @return void
	 */
	public function column( $column_name, $type, $options = [] ) {
		// If there is already a column by the same name then silently fail and continue.
		foreach ( $this->columns as $column ) {
			if ( $column->name === $column_name ) {
				return;
			}
		}

		$column_options = [];
		if ( \array_key_exists( 'primary_key', $options ) ) {
			if ( $options['primary_key'] ) {
				$this->primary_keys[] = $column_name;
			}
		}
		if ( \array_key_exists( 'auto_increment', $options ) ) {
			if ( $options['auto_increment'] ) {
				$column_options['auto_increment'] = true;
			}
		}
		$column_options  = \array_merge( $column_options, $options );
		$column          = new Column( $this->adapter, $column_name, $type, $column_options );
		$this->columns[] = $column;
	}

	/**
	 * Shortcut to create timestamps columns (default created_at, updated_at)
	 *
	 * @param string $created_column_name Created at column name.
	 * @param string $updated_column_name Updated at column name.
	 *
	 * @return void
	 */
	public function timestamps( $created_column_name = 'created_at', $updated_column_name = 'updated_at' ) {
		$this->column( $created_column_name, 'datetime' );
		$this->column(
			$updated_column_name,
			'timestamp',
			[
				'null'    => false,
				'default' => 'CURRENT_TIMESTAMP',
				'extra'   => 'ON UPDATE CURRENT_TIMESTAMP',
			]
		);
	}

	/**
	 * Get all primary keys
	 *
	 * @return string
	 */
	private function keys() {
		if ( \count( $this->primary_keys ) > 0 ) {
			$lead   = ' PRIMARY KEY (';
			$quoted = [];
			foreach ( $this->primary_keys as $key ) {
				$quoted[] = \sprintf( '%s', $this->adapter->identifier( $key ) );
			}
			$primary_key_sql = ",\n" . $lead . \implode( ',', $quoted ) . ')';
			return $primary_key_sql;
		}

		return '';
	}

	/**
	 * Table definition
	 *
	 * @param bool $wants_sql Whether or not to return SQL or execute the query. Defaults to false.
	 *
	 * @return bool|string
	 *
	 * @throws Exception If the table definition has not been intialized.
	 */
	public function finish( $wants_sql = false ) {
		if ( ! $this->initialized ) {
			throw new Exception( \sprintf( "Table Definition: '%s' has not been initialized", $this->name ) );
		}
		$opt_str = '';
		if ( \is_array( $this->options ) && \array_key_exists( 'options', $this->options ) ) {
			$opt_str = $this->options['options'];
		}
		elseif ( isset( $this->adapter->db_info['charset'] ) ) {
			$opt_str = ' DEFAULT CHARSET=' . $this->adapter->db_info['charset'];
		}
		else {
			$opt_str = ' DEFAULT CHARSET=utf8';
		}
		$close_sql        = \sprintf( ') %s;', $opt_str );
		$create_table_sql = $this->sql;
		if ( $this->auto_generate_id === true ) {
			$this->primary_keys[] = 'id';
			$primary_id           = new Column(
				$this->adapter,
				'id',
				'integer',
				[
					'unsigned'       => true,
					'null'           => false,
					'auto_increment' => true,
				]
			);
			$create_table_sql    .= $primary_id->to_sql() . ",\n";
		}
		$create_table_sql .= $this->columns_to_str();
		$create_table_sql .= $this->keys() . $close_sql;
		if ( $wants_sql ) {
			return $create_table_sql;
		}
		return $this->adapter->execute_ddl( $create_table_sql );
	}

	/**
	 * Get SQL for all columns.
	 *
	 * @return string The SQL.
	 */
	private function columns_to_str() {
		$fields = [];
		$len    = \count( $this->columns );
		for ( $i = 0; $i < $len; $i++ ) {
			$c        = $this->columns[ $i ];
			$fields[] = $c->__toString();
		}
		return \implode( ",\n", $fields );
	}

	/**
	 * Init create sql statement.
	 *
	 * @param string $name    The name.
	 * @param array  $options The options.
	 *
	 * @return void
	 */
	private function init_sql( $name, $options ) {
		// Are we forcing table creation? If so, drop it first.
		if ( \array_key_exists( 'force', $options ) && $options['force'] === true ) {
			$this->adapter->drop_table( $name );
		}
		$temp = '';
		if ( \array_key_exists( 'temporary', $options ) ) {
			$temp = ' TEMPORARY';
		}
		$create_sql        = \sprintf( 'CREATE%s TABLE ', $temp );
		$create_sql       .= \sprintf( "%s (\n", $this->adapter->identifier( $name ) );
		$this->sql        .= $create_sql;
		$this->initialized = true;
	}
}
