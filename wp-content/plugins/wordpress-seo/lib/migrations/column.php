<?php

namespace Yoast\WP\Lib\Migrations;

use Exception;

/**
 * Yoast migrations column class.
 */
class Column {

	/**
	 * The adapter.
	 *
	 * @var Adapter
	 */
	private $adapter;

	/**
	 * The name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The type.
	 *
	 * @var mixed
	 */
	public $type;

	/**
	 * The properties.
	 *
	 * @var mixed
	 */
	public $properties;

	/**
	 * The options.
	 *
	 * @var array
	 */
	private $options = [];

	/**
	 * Creates an instance of a column.
	 *
	 * @param Adapter $adapter The current adapter.
	 * @param string  $name    The name of the column.
	 * @param string  $type    The type of the column.
	 * @param array   $options The column options.
	 *
	 * @throws Exception If invalid arguments provided.
	 */
	public function __construct( $adapter, $name, $type, $options = [] ) {
		if ( ! $adapter instanceof Adapter ) {
			throw new Exception( 'Invalid Adapter instance.' );
		}
		if ( empty( $name ) || ! \is_string( $name ) ) {
			throw new Exception( "Invalid 'name' parameter" );
		}
		if ( empty( $type ) || ! \is_string( $type ) ) {
			throw new Exception( "Invalid 'type' parameter" );
		}
		$this->adapter = $adapter;
		$this->name    = $name;
		$this->type    = $type;
		$this->options = $options;
	}

	/**
	 * Returns the SQL of this column.
	 *
	 * @return string
	 */
	public function to_sql() {
		$column_sql  = \sprintf( '%s %s', $this->adapter->identifier( $this->name ), $this->sql_type() );
		$column_sql .= $this->adapter->add_column_options( $this->type, $this->options );
		return $column_sql;
	}

	/**
	 * The SQL string version.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->to_sql();
	}

	/**
	 * The SQL type.
	 *
	 * @return string
	 */
	private function sql_type() {
		return $this->adapter->type_to_sql( $this->type, $this->options );
	}
}
