<?php

namespace Yoast\WP\Lib;

use ArrayAccess;
use Exception;
use InvalidArgumentException;
use ReturnTypeWillChange;
use wpdb;
use Yoast\WP\SEO\Config\Migration_Status;

/**
 * Yoast ORM class.
 *
 * Based on Idiorm
 *
 * URL: http://github.com/j4mie/idiorm/
 *
 * A single-class super-simple database abstraction layer for PHP.
 * Provides (nearly) zero-configuration object-relational mapping
 * and a fluent interface for building basic, commonly-used queries.
 *
 * BSD Licensed.
 *
 * Copyright (c) 2010, Jamie Matthews
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * The methods documented below are magic methods that conform to PSR-1.
 * This documentation exposes these methods to doc generators and IDEs.
 *
 * @see http://www.php-fig.org/psr/psr-1/
 */
class ORM implements ArrayAccess {

	/*
	 * --- CLASS CONSTANTS ---
	 */

	public const CONDITION_FRAGMENT = 0;

	public const CONDITION_VALUES = 1;

	/*
	 * --- INSTANCE PROPERTIES ---
	 */

	/**
	 * Holds the class name. Wrapped find_one and find_many classes will return an instance or instances of this class.
	 *
	 * @var string
	 */
	protected $class_name;

	/**
	 * Holds the name of the table the current ORM instance is associated with.
	 *
	 * @var string
	 */
	protected $table_name;

	/**
	 * Holds the alias for the table to be used in SELECT queries.
	 *
	 * @var string|null
	 */
	protected $table_alias = null;

	/**
	 * Values to be bound to the query.
	 *
	 * @var array
	 */
	protected $values = [];

	/**
	 * Columns to select in the result.
	 *
	 * @var array
	 */
	protected $result_columns = [ '*' ];

	/**
	 * Are we using the default result column or have these been manually changed?
	 *
	 * @var bool
	 */
	protected $using_default_result_columns = true;

	/**
	 * Holds the join sources.
	 *
	 * @var array
	 */
	protected $join_sources = [];

	/**
	 * Should the query include a DISTINCT keyword?
	 *
	 * @var bool
	 */
	protected $distinct = false;

	/**
	 * Is this a raw query?
	 *
	 * @var bool
	 */
	protected $is_raw_query = false;

	/**
	 * The raw query.
	 *
	 * @var string
	 */
	protected $raw_query = '';

	/**
	 * The raw query parameters.
	 *
	 * @var array
	 */
	protected $raw_parameters = [];

	/**
	 * Array of WHERE clauses.
	 *
	 * @var array
	 */
	protected $where_conditions = [];

	/**
	 * LIMIT.
	 *
	 * @var int|null
	 */
	protected $limit = null;

	/**
	 * OFFSET.
	 *
	 * @var int|null
	 */
	protected $offset = null;

	/**
	 * ORDER BY.
	 *
	 * @var array
	 */
	protected $order_by = [];

	/**
	 * GROUP BY.
	 *
	 * @var array
	 */
	protected $group_by = [];

	/**
	 * HAVING.
	 *
	 * @var array
	 */
	protected $having_conditions = [];

	/**
	 * The data for a hydrated instance of the class.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Lifetime of the object.
	 *
	 * @var array
	 */
	protected $dirty_fields = [];

	/**
	 * Fields that are to be inserted in the DB raw.
	 *
	 * @var array
	 */
	protected $expr_fields = [];

	/**
	 * Is this a new object (has create() been called)?
	 *
	 * @var bool
	 */
	protected $is_new = false;

	/**
	 * Name of the column to use as the primary key for
	 * this instance only. Overrides the config settings.
	 *
	 * @var string|null
	 */
	protected $instance_id_column = null;

	/*
	 * --- STATIC METHODS ---
	 */

	/**
	 * Factory method, return an instance of this class bound to the supplied
	 * table name.
	 *
	 * A repeat of content in parent::for_table, so that created class is ORM.
	 *
	 * @param string $table_name The table to create instance for.
	 *
	 * @return ORM Instance of the ORM.
	 */
	public static function for_table( $table_name ) {
		return new static( $table_name, [] );
	}

	/**
	 * Executes a raw query as a wrapper for wpdb::query.
	 * Useful for queries that can't be accomplished through Idiorm,
	 * particularly those using engine-specific features.
	 *
	 * @example raw_execute('INSERT OR REPLACE INTO `widget` (`id`, `name`) SELECT `id`, `name` FROM `other_table`')
	 * @example raw_execute('SELECT `name`, AVG(`order`) FROM `customer` GROUP BY `name` HAVING AVG(`order`) > 10')
	 *
	 * @param string $query      The raw SQL query.
	 * @param array  $parameters Optional bound parameters.
	 *
	 * @return bool Success.
	 */
	public static function raw_execute( $query, $parameters = [] ) {
		return self::execute( $query, $parameters );
	}

	/**
	 * Internal helper method for executing statements.
	 *
	 * @param string $query      The query.
	 * @param array  $parameters An array of parameters to be bound in to the query.
	 *
	 * @return bool|int Response of wpdb::query
	 */
	protected static function execute( $query, $parameters = [] ) {
		/**
		 * The global WordPress database variable.
		 *
		 * @var wpdb $wpdb
		 */
		global $wpdb;

		$show_errors = $wpdb->show_errors;

		if ( \YoastSEO()->classes->get( Migration_Status::class )->get_error( 'free' ) ) {
			$wpdb->show_errors = false;
		}

		$parameters = \array_filter(
			$parameters,
			static function ( $parameter ) {
				return $parameter !== null;
			}
		);
		if ( ! empty( $parameters ) ) {
			$query = $wpdb->prepare( $query, $parameters );
		}

		$result = $wpdb->query( $query );

		$wpdb->show_errors = $show_errors;

		return $result;
	}

	/*
	 * --- INSTANCE METHODS ---
	 */

	/**
	 * "Private" constructor; shouldn't be called directly.
	 * Use the ORM::for_table factory method instead.
	 *
	 * @param string $table_name Table name.
	 * @param array  $data       Data to populate table.
	 */
	protected function __construct( $table_name, $data = [] ) {
		$this->table_name = $table_name;
		$this->data       = $data;
	}

	/**
	 * Sets the name of the class which the wrapped methods should return instances of.
	 *
	 * @param string $class_name The classname to set.
	 *
	 * @return void
	 */
	public function set_class_name( $class_name ) {
		$this->class_name = $class_name;
	}

	/**
	 * Creates a new, empty instance of the class. Used to add a new row to your database. May optionally be passed an
	 * associative array of data to populate the instance. If so, all fields will be flagged as dirty so all will be
	 * saved to the database when save() is called.
	 *
	 * @param array|null $data Data to populate table.
	 *
	 * @return bool|Model|ORM
	 */
	public function create( $data = null ) {
		$this->is_new = true;
		if ( $data !== null ) {
			$this->hydrate( $data )->force_all_dirty();
		}

		return $this->create_model_instance( $this );
	}

	/**
	 * Specifies the ID column to use for this instance or array of instances only.
	 * This overrides the id_column and id_column_overrides settings.
	 *
	 * This is mostly useful for libraries built on top of Idiorm, and will not normally be used in manually built
	 * queries. If you don't know why you would want to use this, you should probably just ignore it.
	 *
	 * @param string $id_column The ID column.
	 *
	 * @return ORM
	 */
	public function use_id_column( $id_column ) {
		$this->instance_id_column = $id_column;

		return $this;
	}

	/**
	 * Creates an ORM instance from the given row (an associative array of data fetched from the database).
	 *
	 * @param array $row A row from the database.
	 *
	 * @return bool|Model
	 */
	protected function create_instance_from_row( $row ) {
		$instance = self::for_table( $this->table_name );
		$instance->use_id_column( $this->instance_id_column );
		$instance->hydrate( $row );

		return $this->create_model_instance( $instance );
	}

	/**
	 * Tells the ORM that you are expecting a single result back from your query, and execute it. Will return a single
	 * instance of the ORM class, or false if no rows were returned. As a shortcut, you may supply an ID as a parameter
	 * to this method. This will perform a primary key lookup on the table.
	 *
	 * @param int|null $id An (optional) ID.
	 *
	 * @return bool|Model
	 */
	public function find_one( $id = null ) {
		if ( $id !== null ) {
			$this->where_id_is( $id );
		}
		$this->limit( 1 );
		$rows = $this->run();
		if ( empty( $rows ) ) {
			return false;
		}

		return $this->create_instance_from_row( $rows[0] );
	}

	/**
	 * Tells the ORM that you are expecting multiple results from your query, and execute it. Will return an array of
	 * instances of the ORM class, or an empty array if no rows were returned.
	 *
	 * @return array
	 */
	public function find_many() {
		$rows = $this->run();

		if ( $rows === false ) {
			return [];
		}

		return \array_map( [ $this, 'create_instance_from_row' ], $rows );
	}

	/**
	 * Creates an instance of the model class associated with this wrapper and populate it with the supplied Idiorm
	 * instance.
	 *
	 * @param ORM $orm The ORM used by model.
	 *
	 * @return bool|Model Instance of the model class.
	 */
	protected function create_model_instance( $orm ) {
		if ( $orm === false ) {
			return false;
		}

		/**
		 * An instance of Model is being made.
		 *
		 * @var Model $model
		 */
		$model = new $this->class_name();
		$model->set_orm( $orm );

		return $model;
	}

	/**
	 * Tells the ORM that you are expecting multiple results from your query, and execute it. Will return an array, or
	 * an empty array if no rows were returned.
	 *
	 * @return array The query results.
	 */
	public function find_array() {
		return $this->run();
	}

	/**
	 * Tells the ORM that you wish to execute a COUNT query.
	 *
	 * @param string $column The table column.
	 *
	 * @return float|int An integer representing the number of rows returned.
	 */
	public function count( $column = '*' ) {
		return $this->call_aggregate_db_function( __FUNCTION__, $column );
	}

	/**
	 * Tells the ORM that you wish to execute a MAX query.
	 *
	 * @param string $column The table column.
	 *
	 * @return float|int The max value of the chosen column.
	 */
	public function max( $column ) {
		return $this->call_aggregate_db_function( __FUNCTION__, $column );
	}

	/**
	 * Tells the ORM that you wish to execute a MIN query.
	 *
	 * @param string $column The table column.
	 *
	 * @return float|int The min value of the chosen column.
	 */
	public function min( $column ) {
		return $this->call_aggregate_db_function( __FUNCTION__, $column );
	}

	/**
	 * Tells the ORM that you wish to execute a AVG query.
	 *
	 * @param string $column The table column.
	 *
	 * @return float|int The average value of the chosen column.
	 */
	public function avg( $column ) {
		return $this->call_aggregate_db_function( __FUNCTION__, $column );
	}

	/**
	 * Tells the ORM that you wish to execute a SUM query.
	 *
	 * @param string $column The table column.
	 *
	 * @return float|int The sum of the chosen column.
	 */
	public function sum( $column ) {
		return $this->call_aggregate_db_function( __FUNCTION__, $column );
	}

	/**
	 * Returns the select query as SQL.
	 *
	 * @return string The select query in SQL.
	 */
	public function get_sql() {
		return $this->build_select();
	}

	/**
	 * Returns the update query as SQL.
	 *
	 * @return string The update query in SQL.
	 */
	public function get_update_sql() {
		return $this->build_update();
	}

	/**
	 * Executes an aggregate query on the current connection.
	 *
	 * @param string $sql_function The aggregate function to call eg. MIN, COUNT, etc.
	 * @param string $column       The column to execute the aggregate query against.
	 *
	 * @return int
	 */
	protected function call_aggregate_db_function( $sql_function, $column ) {
		$alias        = \strtolower( $sql_function );
		$sql_function = \strtoupper( $sql_function );
		if ( $column !== '*' ) {
			$column = $this->quote_identifier( $column );
		}
		$result_columns       = $this->result_columns;
		$this->result_columns = [];
		$this->select_expr( "{$sql_function}({$column})", $alias );
		$result               = $this->find_one();
		$this->result_columns = $result_columns;
		$return_value         = 0;
		if ( $result !== false && isset( $result->{$alias} ) ) {
			if ( ! \is_numeric( $result->{$alias} ) ) {
				$return_value = $result->{$alias};
			}
			// phpcs:ignore Universal.Operators.StrictComparisons -- Reason: This loose comparison seems intentional.
			elseif ( (int) $result->{$alias} == (float) $result->{$alias} ) {
				$return_value = (int) $result->{$alias};
			}
			else {
				$return_value = (float) $result->{$alias};
			}
		}

		return $return_value;
	}

	/**
	 * Hydrates (populate) this instance of the class from an associative array of data. This will usually be called
	 * only from inside the class, but it's public in case you need to call it directly.
	 *
	 * @param array $data Data to populate table.
	 *
	 * @return ORM
	 */
	public function hydrate( $data = [] ) {
		$this->data = $data;

		return $this;
	}

	/**
	 * Forces the ORM to flag all the fields in the $data array as "dirty" and therefore update them when save() is
	 * called.
	 *
	 * @return ORM
	 */
	public function force_all_dirty() {
		$this->dirty_fields = $this->data;

		return $this;
	}

	/**
	 * Performs a raw query. The query can contain placeholders in either named or question mark style. If placeholders
	 * are used, the parameters should be an array of values which will be bound to the placeholders in the query.
	 * If this method is called, all other query building methods will be ignored.
	 *
	 * @param array $query      The query.
	 * @param array $parameters The parameters. Defaults to an empty array.
	 *
	 * @return ORM
	 */
	public function raw_query( $query, $parameters = [] ) {
		$this->is_raw_query   = true;
		$this->raw_query      = $query;
		$this->raw_parameters = $parameters;

		return $this;
	}

	/**
	 * Adds an alias for the main table to be used in SELECT queries.
	 *
	 * @param string $alias The alias.
	 *
	 * @return ORM
	 */
	public function table_alias( $alias ) {
		$this->table_alias = $alias;

		return $this;
	}

	/**
	 * Adds an unquoted expression to the set of columns returned by the SELECT query. Internal method.
	 *
	 * @param string      $expr  The expression.
	 * @param string|null $alias The alias to return the expression as. Defaults to null.
	 *
	 * @return ORM
	 */
	protected function add_result_column( $expr, $alias = null ) {
		if ( $alias !== null ) {
			$expr .= ' AS ' . $this->quote_identifier( $alias );
		}
		if ( $this->using_default_result_columns ) {
			$this->result_columns               = [ $expr ];
			$this->using_default_result_columns = false;
		}
		else {
			$this->result_columns[] = $expr;
		}

		return $this;
	}

	/**
	 * Counts the number of columns that belong to the primary key and their value is null.
	 *
	 * @return int The amount of null columns.
	 *
	 * @throws Exception Primary key ID contains null value(s).
	 * @throws Exception Primary key ID missing from row or is null.
	 */
	public function count_null_id_columns() {
		if ( \is_array( $this->get_id_column_name() ) ) {
			return \count( \array_filter( $this->id(), 'is_null' ) );
		}
		else {
			return ( $this->id() === null ) ? 1 : 0;
		}
	}

	/**
	 * Adds a column to the list of columns returned by the SELECT query.
	 *
	 * @param string      $column The column. Defaults to '*'.
	 * @param string|null $alias  The alias to return the column as. Defaults to null.
	 *
	 * @return ORM
	 */
	public function select( $column, $alias = null ) {
		$column = $this->quote_identifier( $column );

		return $this->add_result_column( $column, $alias );
	}

	/**
	 * Adds an unquoted expression to the list of columns returned by the SELECT query.
	 *
	 * @param string      $expr  The expression.
	 * @param string|null $alias The alias to return the column as. Defaults to null.
	 *
	 * @return ORM
	 */
	public function select_expr( $expr, $alias = null ) {
		return $this->add_result_column( $expr, $alias );
	}

	/**
	 * Adds columns to the list of columns returned by the SELECT query.
	 *
	 * This defaults to '*'.
	 * Many columns can be supplied as either an array or as a list of parameters to the method.
	 * Note that the alias must not be numeric - if you want a numeric alias then prepend it with some alpha chars. eg.
	 * a1.
	 *
	 * @example select_many(array('column', 'column2', 'column3'), 'column4', 'column5');
	 * @example select_many(array('alias' => 'column', 'column2', 'alias2' => 'column3'), 'column4', 'column5');
	 * @example select_many('column', 'column2', 'column3');
	 *
	 * @return ORM
	 */
	public function select_many() {
		$columns = \func_get_args();
		if ( ! empty( $columns ) ) {
			$columns = $this->normalise_select_many_columns( $columns );
			foreach ( $columns as $alias => $column ) {
				if ( \is_numeric( $alias ) ) {
					$alias = null;
				}
				$this->select( $column, $alias );
			}
		}

		return $this;
	}

	/**
	 * Adds an unquoted expression to the list of columns returned by the SELECT query.
	 *
	 * Many columns can be supplied as either an array or as a list of parameters to the method.
	 * Note that the alias must not be numeric - if you want a numeric alias then prepend it with some alpha chars. eg.
	 * a1
	 *
	 * @example select_many_expr(array('alias' => 'column', 'column2', 'alias2' => 'column3'), 'column4', 'column5')
	 * @example select_many_expr('column', 'column2', 'column3')
	 * @example select_many_expr(array('column', 'column2', 'column3'), 'column4', 'column5')
	 *
	 * @return ORM
	 */
	public function select_many_expr() {
		$columns = \func_get_args();
		if ( ! empty( $columns ) ) {
			$columns = $this->normalise_select_many_columns( $columns );
			foreach ( $columns as $alias => $column ) {
				if ( \is_numeric( $alias ) ) {
					$alias = null;
				}
				$this->select_expr( $column, $alias );
			}
		}

		return $this;
	}

	/**
	 * Takes a column specification for the select many methods and convert it into a normalised array of columns and
	 * aliases.
	 *
	 * It is designed to turn the following styles into a normalised array:
	 * array(array('alias' => 'column', 'column2', 'alias2' => 'column3'), 'column4', 'column5'))
	 *
	 * @param array $columns The columns.
	 *
	 * @return array
	 */
	protected function normalise_select_many_columns( $columns ) {
		$return = [];
		foreach ( $columns as $column ) {
			if ( \is_array( $column ) ) {
				foreach ( $column as $key => $value ) {
					if ( ! \is_numeric( $key ) ) {
						$return[ $key ] = $value;
					}
					else {
						$return[] = $value;
					}
				}
			}
			else {
				$return[] = $column;
			}
		}

		return $return;
	}

	/**
	 * Adds a DISTINCT keyword before the list of columns in the SELECT query.
	 *
	 * @return ORM
	 */
	public function distinct() {
		$this->distinct = true;

		return $this;
	}

	/**
	 * Add a JOIN source to the query. Internal method.
	 *
	 * The join_operator should be one of INNER, LEFT OUTER, CROSS etc - this
	 * will be prepended to JOIN.
	 *
	 * The table should be the name of the table to join to.
	 *
	 * The constraint may be either a string or an array with three elements. If it
	 * is a string, it will be compiled into the query as-is, with no escaping. The
	 * recommended way to supply the constraint is as an array with three elements:
	 *
	 * first_column, operator, second_column
	 *
	 * Example: array('user.id', '=', 'profile.user_id')
	 *
	 * will compile to
	 *
	 * ON `user`.`id` = `profile`.`user_id`
	 *
	 * The final (optional) argument specifies an alias for the joined table.
	 *
	 * @param string      $join_operator The join_operator should be one of INNER, LEFT OUTER, CROSS etc - this will be
	 *                                   prepended to JOIN.
	 * @param string      $table         The table should be the name of the table to join to.
	 * @param string      $constraint    The constraint.
	 * @param string|null $table_alias   The alias for the joined table. Defaults to null.
	 *
	 * @return ORM
	 */
	protected function add_join_source( $join_operator, $table, $constraint, $table_alias = null ) {
		$join_operator = \trim( "{$join_operator} JOIN" );
		$table         = $this->quote_identifier( $table );
		// Add table alias if present.
		if ( $table_alias !== null ) {
			$table_alias = $this->quote_identifier( $table_alias );
			$table      .= " {$table_alias}";
		}
		// Build the constraint.
		if ( \is_array( $constraint ) ) {
			list( $first_column, $operator, $second_column ) = $constraint;

			$first_column  = $this->quote_identifier( $first_column );
			$second_column = $this->quote_identifier( $second_column );
			$constraint    = "{$first_column} {$operator} {$second_column}";
		}
		$this->join_sources[] = "{$join_operator} {$table} ON {$constraint}";

		return $this;
	}

	/**
	 * Adds a RAW JOIN source to the query.
	 *
	 * @param string $table       The table name.
	 * @param string $constraint  The constraint.
	 * @param string $table_alias The table alias.
	 * @param array  $parameters  The parameters. Defaults to an empty array.
	 *
	 * @return ORM
	 */
	public function raw_join( $table, $constraint, $table_alias, $parameters = [] ) {
		// Add table alias if present.
		if ( $table_alias !== null ) {
			$table_alias = $this->quote_identifier( $table_alias );
			$table      .= " {$table_alias}";
		}
		$this->values = \array_merge( $this->values, $parameters );
		// Build the constraint.
		if ( \is_array( $constraint ) ) {
			list( $first_column, $operator, $second_column ) = $constraint;

			$first_column  = $this->quote_identifier( $first_column );
			$second_column = $this->quote_identifier( $second_column );
			$constraint    = "{$first_column} {$operator} {$second_column}";
		}
		$this->join_sources[] = "{$table} ON {$constraint}";

		return $this;
	}

	/**
	 * Adds a simple JOIN source to the query.
	 *
	 * @param string      $table       The table name.
	 * @param string      $constraint  The constraint.
	 * @param string|null $table_alias The table alias. Defaults to null.
	 *
	 * @return ORM
	 */
	public function join( $table, $constraint, $table_alias = null ) {
		return $this->add_join_source( '', $table, $constraint, $table_alias );
	}

	/**
	 * Adds an INNER JOIN source to the query.
	 *
	 * @param string      $table       The table name.
	 * @param string      $constraint  The constraint.
	 * @param string|null $table_alias The table alias. Defaults to null.
	 *
	 * @return ORM
	 */
	public function inner_join( $table, $constraint, $table_alias = null ) {
		return $this->add_join_source( 'INNER', $table, $constraint, $table_alias );
	}

	/**
	 * Adds a LEFT OUTER JOIN source to the query.
	 *
	 * @param string      $table       The table name.
	 * @param string      $constraint  The constraint.
	 * @param string|null $table_alias The table alias. Defaults to null.
	 *
	 * @return ORM
	 */
	public function left_outer_join( $table, $constraint, $table_alias = null ) {
		return $this->add_join_source( 'LEFT OUTER', $table, $constraint, $table_alias );
	}

	/**
	 * Adds a RIGHT OUTER JOIN source to the query.
	 *
	 * @param string      $table       The table name.
	 * @param string      $constraint  The constraint.
	 * @param string|null $table_alias The table alias. Defaults to null.
	 *
	 * @return ORM
	 */
	public function right_outer_join( $table, $constraint, $table_alias = null ) {
		return $this->add_join_source( 'RIGHT OUTER', $table, $constraint, $table_alias );
	}

	/**
	 * Adds a FULL OUTER JOIN source to the query.
	 *
	 * @param string      $table       The table name.
	 * @param string      $constraint  The constraint.
	 * @param string|null $table_alias The table alias. Defaults to null.
	 *
	 * @return ORM
	 */
	public function full_outer_join( $table, $constraint, $table_alias = null ) {
		return $this->add_join_source( 'FULL OUTER', $table, $constraint, $table_alias );
	}

	/**
	 * Adds a HAVING condition to the query. Internal method.
	 *
	 * @param string $fragment The fragment.
	 * @param array  $values   The values. Defaults to an empty array.
	 *
	 * @return ORM
	 */
	protected function add_having( $fragment, $values = [] ) {
		return $this->add_condition( 'having', $fragment, $values );
	}

	/**
	 * Adds a HAVING condition to the query. Internal method.
	 *
	 * @param string $column_name The table column.
	 * @param string $separator   The separator.
	 * @param mixed  $value       The value.
	 *
	 * @return ORM
	 */
	protected function add_simple_having( $column_name, $separator, $value ) {
		return $this->add_simple_condition( 'having', $column_name, $separator, $value );
	}

	/**
	 * Adds a HAVING clause with multiple values (like IN and NOT IN). Internal method.
	 *
	 * @param string|array $column_name The table column.
	 * @param string       $separator   The separator.
	 * @param array        $values      The values.
	 *
	 * @return ORM
	 */
	public function add_having_placeholder( $column_name, $separator, $values ) {
		if ( ! \is_array( $column_name ) ) {
			$data = [ $column_name => $values ];
		}
		else {
			$data = $column_name;
		}
		$result = $this;
		foreach ( $data as $key => $val ) {
			$column       = $result->quote_identifier( $key );
			$placeholders = $result->create_placeholders( $val );
			$result       = $result->add_having( "{$column} {$separator} ({$placeholders})", $val );
		}

		return $result;
	}

	/**
	 * Adds a HAVING clause with no parameters(like IS NULL and IS NOT NULL). Internal method.
	 *
	 * @param string $column_name The column name.
	 * @param string $operator    The operator.
	 *
	 * @return ORM
	 */
	public function add_having_no_value( $column_name, $operator ) {
		$conditions = \is_array( $column_name ) ? $column_name : [ $column_name ];
		$result     = $this;
		foreach ( $conditions as $column ) {
			$column = $this->quote_identifier( $column );
			$result = $result->add_having( "{$column} {$operator}" );
		}

		return $result;
	}

	/**
	 * Adds a WHERE condition to the query. Internal method.
	 *
	 * @param string $fragment The fragment.
	 * @param array  $values   The values. Defaults to an empty array.
	 *
	 * @return ORM
	 */
	protected function add_where( $fragment, $values = [] ) {
		return $this->add_condition( 'where', $fragment, $values );
	}

	/**
	 * Adds a WHERE condition to the query. Internal method.
	 *
	 * @param string|array $column_name The table column.
	 * @param string       $separator   The separator.
	 * @param mixed        $value       The value.
	 *
	 * @return ORM
	 */
	protected function add_simple_where( $column_name, $separator, $value ) {
		return $this->add_simple_condition( 'where', $column_name, $separator, $value );
	}

	/**
	 * Adds a WHERE clause with multiple values (like IN and NOT IN).
	 *
	 * @param string|array $column_name The table column.
	 * @param string       $separator   The separator.
	 * @param array        $values      The values.
	 *
	 * @return ORM
	 */
	public function add_where_placeholder( $column_name, $separator, $values ) {
		if ( ! \is_array( $column_name ) ) {
			$data = [ $column_name => $values ];
		}
		else {
			$data = $column_name;
		}
		$result = $this;
		foreach ( $data as $key => $val ) {
			$column       = $result->quote_identifier( $key );
			$placeholders = $result->create_placeholders( $val );
			$result       = $result->add_where( "{$column} {$separator} ({$placeholders})", $val );
		}

		return $result;
	}

	/**
	 * Adds a WHERE clause with no parameters(like IS NULL and IS NOT NULL).
	 *
	 * @param string $column_name The column name.
	 * @param string $operator    The operator.
	 *
	 * @return ORM
	 */
	public function add_where_no_value( $column_name, $operator ) {
		$conditions = \is_array( $column_name ) ? $column_name : [ $column_name ];
		$result     = $this;
		foreach ( $conditions as $column ) {
			$column = $this->quote_identifier( $column );
			$result = $result->add_where( "{$column} {$operator}" );
		}

		return $result;
	}

	/**
	 * Adds a HAVING or WHERE condition to the query. Internal method.
	 *
	 * @param string $type     The type.
	 * @param string $fragment The fragment.
	 * @param array  $values   The values. Defaults to empty array.
	 *
	 * @return ORM
	 */
	protected function add_condition( $type, $fragment, $values = [] ) {
		$conditions_class_property_name = "{$type}_conditions";
		if ( ! \is_array( $values ) ) {
			$values = [ $values ];
		}
		\array_push(
			$this->{$conditions_class_property_name},
			[
				self::CONDITION_FRAGMENT => $fragment,
				self::CONDITION_VALUES   => $values,
			]
		);

		return $this;
	}

	/**
	 * Compiles a simple COLUMN SEPARATOR VALUE style HAVING or WHERE condition into a string and value ready to be
	 * passed to the add_condition method.
	 *
	 * Avoids duplication of the call to quote_identifier.
	 * If column_name is an associative array, it will add a condition for each column.
	 *
	 * @param string       $type        The type.
	 * @param string|array $column_name The table column.
	 * @param string       $separator   The separator.
	 * @param mixed        $value       The value.
	 *
	 * @return ORM
	 */
	protected function add_simple_condition( $type, $column_name, $separator, $value ) {
		$multiple = \is_array( $column_name ) ? $column_name : [ $column_name => $value ];
		$result   = $this;
		foreach ( $multiple as $key => $val ) {
			// Add the table name in case of ambiguous columns.
			if ( \count( $result->join_sources ) > 0 && \strpos( $key, '.' ) === false ) {
				$table = $result->table_name;
				if ( $result->table_alias !== null ) {
					$table = $result->table_alias;
				}
				$key = "{$table}.{$key}";
			}
			$key         = $result->quote_identifier( $key );
			$placeholder = ( $val === null ) ? 'NULL' : '%s';
			$result      = $result->add_condition( $type, "{$key} {$separator} {$placeholder}", $val );
		}

		return $result;
	}

	/**
	 * Returns a string containing the given number of question marks, separated by commas. Eg "?, ?, ?".
	 *
	 * @param array $fields Fields to create placeholder for.
	 *
	 * @return string
	 */
	protected function create_placeholders( $fields ) {
		if ( ! empty( $fields ) ) {
			$db_fields = [];
			foreach ( $fields as $key => $value ) {
				// Process expression fields directly into the query.
				if ( \array_key_exists( $key, $this->expr_fields ) ) {
					$db_fields[] = $value;
				}
				else {
					$db_fields[] = ( $value === null ) ? 'NULL' : '%s';
				}
			}

			return \implode( ', ', $db_fields );
		}

		return '';
	}

	/**
	 * Filters a column/value array returning only those columns that belong to a compound primary key.
	 *
	 * If the key contains a column that does not exist in the given array, a null value will be returned for it.
	 *
	 * @param mixed $value The value.
	 *
	 * @return array
	 */
	protected function get_compound_id_column_values( $value ) {
		$filtered = [];
		foreach ( $this->get_id_column_name() as $key ) {
			$filtered[ $key ] = ( $value[ $key ] ?? null );
		}

		return $filtered;
	}

	/**
	 * Filters an array containing compound column/value arrays.
	 *
	 * @param array $values The values.
	 *
	 * @return array
	 */
	protected function get_compound_id_column_values_array( $values ) {
		$filtered = [];
		foreach ( $values as $value ) {
			$filtered[] = $this->get_compound_id_column_values( $value );
		}

		return $filtered;
	}

	/**
	 * Add a WHERE column = value clause to your query. Each time this is called in the chain, an additional WHERE will
	 * be added, and these will be ANDed together when the final query is built.
	 *
	 * If you use an array in $column_name, a new clause will be added for each element. In this case, $value is
	 * ignored.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed|null   $value       The value. Defaults to null.
	 *
	 * @return ORM
	 */
	public function where( $column_name, $value = null ) {
		return $this->where_equal( $column_name, $value );
	}

	/**
	 * More explicitly named version of for the where() method. Can be used if preferred.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed|null   $value       The value. Defaults to null.
	 *
	 * @return ORM
	 */
	public function where_equal( $column_name, $value = null ) {
		return $this->add_simple_where( $column_name, '=', $value );
	}

	/**
	 * Add a WHERE column != value clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed|null   $value       The value. Defaults to null.
	 *
	 * @return ORM
	 */
	public function where_not_equal( $column_name, $value = null ) {
		return $this->add_simple_where( $column_name, '!=', $value );
	}

	/**
	 * Queries the table by its primary key. Special method.
	 *
	 * If primary key is compound, only the columns that belong to they key will be used for the query.
	 *
	 * @param string $id The ID.
	 *
	 * @return ORM
	 */
	public function where_id_is( $id ) {
		return \is_array( $this->get_id_column_name() ) ? $this->where( $this->get_compound_id_column_values( $id ), null ) : $this->where( $this->get_id_column_name(), $id );
	}

	/**
	 * Allows adding a WHERE clause that matches any of the conditions specified in the array. Each element in the
	 * associative array will be a different condition, where the key will be the column name.
	 *
	 * By default, an equal operator will be used against all columns, but it can be overriden for any or every column
	 * using the second parameter.
	 *
	 * Each condition will be ORed together when added to the final query.
	 *
	 * @param array  $values   The values.
	 * @param string $operator The operator.
	 *
	 * @return ORM
	 */
	public function where_any_is( $values, $operator = '=' ) {
		$data  = [];
		$query = [ '((' ];
		$first = true;
		foreach ( $values as $value ) {
			if ( $first ) {
				$first = false;
			}
			else {
				$query[] = ') OR (';
			}
			$firstsub = true;
			foreach ( $value as $key => $item ) {
				$op = \is_string( $operator ) ? $operator : ( $operator[ $key ] ?? '=' );
				if ( $op === '=' && $item === null ) {
					$op = 'IS';
				}
				if ( $firstsub ) {
					$firstsub = false;
				}
				else {
					$query[] = 'AND';
				}
				$query[] = $this->quote_identifier( $key );
				$data[]  = $item;
				$query[] = $op;
				$query[] = ( ( $item === null ) ? 'NULL' : '%s' );
			}
		}
		$query[] = '))';

		return $this->where_raw( \implode( ' ', $query ), $data );
	}

	/**
	 * Queries the table by its primary key.
	 *
	 * Similar to where_id_is() but allowing multiple primary keys.
	 * If primary key is compound, only the columns that belong to they key will be used for the query.
	 *
	 * @param string[] $ids The IDs.
	 *
	 * @return ORM
	 */
	public function where_id_in( $ids ) {
		return \is_array( $this->get_id_column_name() ) ? $this->where_any_is( $this->get_compound_id_column_values_array( $ids ) ) : $this->where_in( $this->get_id_column_name(), $ids );
	}

	/**
	 * Adds a WHERE ... LIKE clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed|null   $value       The value. Defaults to null.
	 *
	 * @return ORM
	 */
	public function where_like( $column_name, $value = null ) {
		return $this->add_simple_where( $column_name, 'LIKE', $value );
	}

	/**
	 * Adds where WHERE ... NOT LIKE clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed|null   $value       The value. Defaults to null.
	 *
	 * @return ORM
	 */
	public function where_not_like( $column_name, $value = null ) {
		return $this->add_simple_where( $column_name, 'NOT LIKE', $value );
	}

	/**
	 * Adds a WHERE ... > clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed|null   $value       The value. Defaults to null.
	 *
	 * @return ORM
	 */
	public function where_gt( $column_name, $value = null ) {
		return $this->add_simple_where( $column_name, '>', $value );
	}

	/**
	 * Adds a WHERE ... < clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed|null   $value       The value. Defaults to null.
	 *
	 * @return ORM
	 */
	public function where_lt( $column_name, $value = null ) {
		return $this->add_simple_where( $column_name, '<', $value );
	}

	/**
	 * Adds a WHERE ... >= clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed|null   $value       The value. Defaults to null.
	 *
	 * @return ORM
	 */
	public function where_gte( $column_name, $value = null ) {
		return $this->add_simple_where( $column_name, '>=', $value );
	}

	/**
	 * Adds a WHERE ... <= clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed|null   $value       The value. Defaults to null.
	 *
	 * @return ORM
	 */
	public function where_lte( $column_name, $value = null ) {
		return $this->add_simple_where( $column_name, '<=', $value );
	}

	/**
	 * Adds a WHERE ... IN clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param array        $values      The values.
	 *
	 * @return ORM
	 */
	public function where_in( $column_name, $values ) {
		return $this->add_where_placeholder( $column_name, 'IN', $values );
	}

	/**
	 * Adds a WHERE ... NOT IN clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param array        $values      The values.
	 *
	 * @return ORM
	 */
	public function where_not_in( $column_name, $values ) {
		return $this->add_where_placeholder( $column_name, 'NOT IN', $values );
	}

	/**
	 * Adds a WHERE column IS NULL clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 *
	 * @return ORM
	 */
	public function where_null( $column_name ) {
		return $this->add_where_no_value( $column_name, 'IS NULL' );
	}

	/**
	 * Adds a WHERE column IS NOT NULL clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 *
	 * @return ORM
	 */
	public function where_not_null( $column_name ) {
		return $this->add_where_no_value( $column_name, 'IS NOT NULL' );
	}

	/**
	 * Adds a raw WHERE clause to the query. The clause should contain question mark placeholders, which will be bound
	 * to the parameters supplied in the second argument.
	 *
	 * @param string $clause     The clause that should contain question mark placeholders.
	 * @param array  $parameters The parameters to include in the query.
	 *
	 * @return ORM
	 */
	public function where_raw( $clause, $parameters = [] ) {
		return $this->add_where( $clause, $parameters );
	}

	/**
	 * Adds a LIMIT to the query.
	 *
	 * @param int $limit The limit.
	 *
	 * @return ORM
	 */
	public function limit( $limit ) {
		$this->limit = $limit;

		return $this;
	}

	/**
	 * Adds an OFFSET to the query.
	 *
	 * @param int $offset The offset.
	 *
	 * @return ORM
	 */
	public function offset( $offset ) {
		$this->offset = $offset;

		return $this;
	}

	/**
	 * Adds an ORDER BY clause to the query.
	 *
	 * @param string $column_name The column name.
	 * @param string $ordering    The ordering. DESC or ASC.
	 *
	 * @return ORM
	 */
	protected function add_order_by( $column_name, $ordering ) {
		$column_name      = $this->quote_identifier( $column_name );
		$this->order_by[] = "{$column_name} {$ordering}";

		return $this;
	}

	/**
	 * Adds an ORDER BY column DESC clause.
	 *
	 * @param string|array $column_name The table column.
	 *
	 * @return ORM
	 */
	public function order_by_desc( $column_name ) {
		return $this->add_order_by( $column_name, 'DESC' );
	}

	/**
	 * Adds an ORDER BY column ASC clause.
	 *
	 * @param string|array $column_name The table column.
	 *
	 * @return ORM
	 */
	public function order_by_asc( $column_name ) {
		return $this->add_order_by( $column_name, 'ASC' );
	}

	/**
	 * Adds an unquoted expression as an ORDER BY clause.
	 *
	 * @param string $clause The clause.
	 *
	 * @return ORM
	 */
	public function order_by_expr( $clause ) {
		$this->order_by[] = $clause;

		return $this;
	}

	/**
	 * Adds a column to the list of columns to GROUP BY.
	 *
	 * @param string|array $column_name The table column.
	 *
	 * @return ORM
	 */
	public function group_by( $column_name ) {
		$column_name      = $this->quote_identifier( $column_name );
		$this->group_by[] = $column_name;

		return $this;
	}

	/**
	 * Adds an unquoted expression to the list of columns to GROUP BY.
	 *
	 * @param string $expr The expression.
	 *
	 * @return ORM
	 */
	public function group_by_expr( $expr ) {
		$this->group_by[] = $expr;

		return $this;
	}

	/**
	 * Adds a HAVING column = value clause to your query.
	 *
	 * Each time this is called in the chain, an additional HAVING will be added, and these will be ANDed together when
	 * the final query is built.
	 *
	 * If you use an array in $column_name, a new clause will be added for each element. In this case, $value is
	 * ignored.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed|null   $value       The value.
	 *
	 * @return ORM
	 */
	public function having( $column_name, $value = null ) {
		return $this->having_equal( $column_name, $value );
	}

	/**
	 * Adds a having equal to your query.
	 *
	 * More explicitly named version of for the having() method. Can be used if preferred.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed|null   $value       The value.
	 *
	 * @return ORM
	 */
	public function having_equal( $column_name, $value = null ) {
		return $this->add_simple_having( $column_name, '=', $value );
	}

	/**
	 * Adds a HAVING column != value clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed|null   $value       The value.
	 *
	 * @return ORM
	 */
	public function having_not_equal( $column_name, $value = null ) {
		return $this->add_simple_having( $column_name, '!=', $value );
	}

	/**
	 * Queries the table by its primary key. Special method.
	 *
	 * If primary key is compound, only the columns that belong to they key will be used for the query.
	 *
	 * @param string $id The ID.
	 *
	 * @return ORM
	 */
	public function having_id_is( $id ) {
		return \is_array( $this->get_id_column_name() ) ? $this->having( $this->get_compound_id_column_values( $id ), null ) : $this->having( $this->get_id_column_name(), $id );
	}

	/**
	 * Adds a HAVING ... LIKE clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param string|null  $value       The value.
	 *
	 * @return ORM
	 */
	public function having_like( $column_name, $value = null ) {
		return $this->add_simple_having( $column_name, 'LIKE', $value );
	}

	/**
	 * Adds where HAVING ... NOT LIKE clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param string|null  $value       The value.
	 *
	 * @return ORM
	 */
	public function having_not_like( $column_name, $value = null ) {
		return $this->add_simple_having( $column_name, 'NOT LIKE', $value );
	}

	/**
	 * Adds a HAVING ... > clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed        $value       The value.
	 *
	 * @return ORM
	 */
	public function having_gt( $column_name, $value = null ) {
		return $this->add_simple_having( $column_name, '>', $value );
	}

	/**
	 * Adds a HAVING ... < clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed        $value       The value.
	 *
	 * @return ORM
	 */
	public function having_lt( $column_name, $value = null ) {
		return $this->add_simple_having( $column_name, '<', $value );
	}

	/**
	 * Adds a HAVING ... >= clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed        $value       The value. Defaults to null.
	 *
	 * @return ORM
	 */
	public function having_gte( $column_name, $value = null ) {
		return $this->add_simple_having( $column_name, '>=', $value );
	}

	/**
	 * Adds a HAVING ... <= clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param mixed        $value       The value.
	 *
	 * @return ORM
	 */
	public function having_lte( $column_name, $value = null ) {
		return $this->add_simple_having( $column_name, '<=', $value );
	}

	/**
	 * Adds a HAVING ... IN clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param array|null   $values      The values. Defaults to null.
	 *
	 * @return ORM
	 */
	public function having_in( $column_name, $values = null ) {
		return $this->add_having_placeholder( $column_name, 'IN', $values );
	}

	/**
	 * Adds a HAVING ... NOT IN clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 * @param array|null   $values      The values. Defaults to null.
	 *
	 * @return ORM
	 */
	public function having_not_in( $column_name, $values = null ) {
		return $this->add_having_placeholder( $column_name, 'NOT IN', $values );
	}

	/**
	 * Adds a HAVING column IS NULL clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 *
	 * @return ORM
	 */
	public function having_null( $column_name ) {
		return $this->add_having_no_value( $column_name, 'IS NULL' );
	}

	/**
	 * Adds a HAVING column IS NOT NULL clause to your query.
	 *
	 * @param string|array $column_name The table column.
	 *
	 * @return ORM
	 */
	public function having_not_null( $column_name ) {
		return $this->add_having_no_value( $column_name, 'IS NOT NULL' );
	}

	/**
	 * Adds a raw HAVING clause to the query. The clause should contain question mark placeholders, which will be bound
	 * to the parameters supplied in the second argument.
	 *
	 * @param string $clause     The clause that should contain question mark placeholders.
	 * @param array  $parameters The parameters to include in the query.
	 *
	 * @return ORM
	 */
	public function having_raw( $clause, $parameters = [] ) {
		return $this->add_having( $clause, $parameters );
	}

	/**
	 * Builds a SELECT statement based on the clauses that have been passed to this instance by chaining method calls.
	 *
	 * @return string
	 */
	protected function build_select() {
		// If the query is raw, just set the $this->values to be the raw query parameters and return the raw query.
		if ( $this->is_raw_query ) {
			$this->values = $this->raw_parameters;

			return $this->raw_query;
		}

		// Build and return the full SELECT statement by concatenating the results of calling each separate builder method.
		return $this->join_if_not_empty(
			' ',
			[
				$this->build_select_start(),
				$this->build_join(),
				$this->build_where(),
				$this->build_group_by(),
				$this->build_having(),
				$this->build_order_by(),
				$this->build_limit(),
				$this->build_offset(),
			]
		);
	}

	/**
	 * Builds the start of the SELECT statement.
	 *
	 * @return string
	 */
	protected function build_select_start() {
		$fragment       = 'SELECT ';
		$result_columns = \implode( ', ', $this->result_columns );
		if ( $this->distinct ) {
			$result_columns = 'DISTINCT ' . $result_columns;
		}
		$fragment .= "{$result_columns} FROM " . $this->quote_identifier( $this->table_name );
		if ( $this->table_alias !== null ) {
			$fragment .= ' ' . $this->quote_identifier( $this->table_alias );
		}

		return $fragment;
	}

	/**
	 * Builds the JOIN sources.
	 *
	 * @return string
	 */
	protected function build_join() {
		if ( \count( $this->join_sources ) === 0 ) {
			return '';
		}

		return \implode( ' ', $this->join_sources );
	}

	/**
	 * Builds the WHERE clause(s).
	 *
	 * @return string
	 */
	protected function build_where() {
		return $this->build_conditions( 'where' );
	}

	/**
	 * Build the HAVING clause(s)
	 *
	 * @return string
	 */
	protected function build_having() {
		return $this->build_conditions( 'having' );
	}

	/**
	 * Builds GROUP BY.
	 *
	 * @return string
	 */
	protected function build_group_by() {
		if ( \count( $this->group_by ) === 0 ) {
			return '';
		}

		return 'GROUP BY ' . \implode( ', ', $this->group_by );
	}

	/**
	 * Builds a WHERE or HAVING clause.
	 *
	 * @param string $type Where or having.
	 *
	 * @return string
	 */
	protected function build_conditions( $type ) {
		$conditions_class_property_name = "{$type}_conditions";
		// If there are no clauses, return empty string.
		if ( \count( $this->{$conditions_class_property_name} ) === 0 ) {
			return '';
		}
		$conditions = [];
		foreach ( $this->{$conditions_class_property_name} as $condition ) {
			$conditions[] = $condition[ self::CONDITION_FRAGMENT ];
			$this->values = \array_merge( $this->values, $condition[ self::CONDITION_VALUES ] );
		}

		return \strtoupper( $type ) . ' ' . \implode( ' AND ', $conditions );
	}

	/**
	 * Builds ORDER BY.
	 *
	 * @return string
	 */
	protected function build_order_by() {
		if ( \count( $this->order_by ) === 0 ) {
			return '';
		}

		return 'ORDER BY ' . \implode( ', ', $this->order_by );
	}

	/**
	 * Builds LIMIT.
	 *
	 * @return string
	 */
	protected function build_limit() {
		if ( $this->limit !== null ) {
			return "LIMIT {$this->limit}";
		}

		return '';
	}

	/**
	 * Builds OFFSET.
	 *
	 * @return string
	 */
	protected function build_offset() {
		if ( $this->offset !== null ) {
			return 'OFFSET ' . $this->offset;
		}

		return '';
	}

	/**
	 * Joins strings if they are not empty.
	 *
	 * @param string   $glue   Glue.
	 * @param string[] $pieces Pieces to join.
	 *
	 * @return string
	 */
	protected function join_if_not_empty( $glue, $pieces ) {
		$filtered_pieces = [];
		foreach ( $pieces as $piece ) {
			if ( \is_string( $piece ) ) {
				$piece = \trim( $piece );
			}
			if ( ! empty( $piece ) ) {
				$filtered_pieces[] = $piece;
			}
		}

		return \implode( $glue, $filtered_pieces );
	}

	/**
	 * Quotes a string that is used as an identifier (table names, column names etc).
	 * This method can also deal with dot-separated identifiers eg table.column.
	 *
	 * @param string|string[] $identifier One or more identifiers.
	 *
	 * @return string
	 */
	protected function quote_one_identifier( $identifier ) {
		$parts = \explode( '.', $identifier );
		$parts = \array_map( [ $this, 'quote_identifier_part' ], $parts );

		return \implode( '.', $parts );
	}

	/**
	 * Quotes a string that is used as an identifier (table names, column names etc) or an array containing multiple
	 * identifiers. This method can also deal with dot-separated identifiers eg table.column.
	 *
	 * @param string|string[] $identifier One or more identifiers.
	 *
	 * @return string
	 */
	protected function quote_identifier( $identifier ) {
		if ( \is_array( $identifier ) ) {
			$result = \array_map( [ $this, 'quote_one_identifier' ], $identifier );

			return \implode( ', ', $result );
		}
		else {
			return $this->quote_one_identifier( $identifier );
		}
	}

	/**
	 * Quotes a single part of an identifier, using the identifier quote character specified in the config
	 * (or autodetected).
	 *
	 * @param string $part The part to quote.
	 *
	 * @return string
	 */
	protected function quote_identifier_part( $part ) {
		if ( $part === '*' ) {
			return $part;
		}
		$quote_character = '`';

		// Double up any identifier quotes to escape them.
		return $quote_character . \str_replace( $quote_character, $quote_character . $quote_character, $part ) . $quote_character;
	}

	/**
	 * Executes the SELECT query that has been built up by chaining methods on this class.
	 * Return an array of rows as associative arrays.
	 *
	 * @return array|false The result rows. False if the query failed.
	 */
	protected function run() {
		global $wpdb;

		$query   = $this->build_select();
		$success = self::execute( $query, $this->values );

		if ( $success === false ) {
			// If the query fails run the migrations and try again.
			// Action is intentionally undocumented and should not be used by third-parties.
			\do_action( '_yoast_run_migrations' );
			$success = self::execute( $query, $this->values );
		}

		$this->reset_idiorm_state();

		if ( $success === false ) {
			return false;
		}

		$rows = [];
		foreach ( $wpdb->last_result as $row ) {
			$rows[] = \get_object_vars( $row );
		}

		return $rows;
	}

	/**
	 * Resets the Idiorm instance state.
	 *
	 * @return void
	 */
	private function reset_idiorm_state() {
		$this->values                       = [];
		$this->result_columns               = [ '*' ];
		$this->using_default_result_columns = true;
	}

	/**
	 * Returns the raw data wrapped by this ORM instance as an associative array. Column names may optionally be
	 * supplied as arguments, if so, only those keys will be returned.
	 *
	 * @return array Associative array of the raw data.
	 */
	public function as_array() {
		if ( \func_num_args() === 0 ) {
			return $this->data;
		}
		$args = \func_get_args();

		return \array_intersect_key( $this->data, \array_flip( $args ) );
	}

	/**
	 * Returns the value of a property of this object (database row) or null if not present.
	 *
	 * If a column-names array is passed, it will return a associative array with the value of each column or null if
	 * it is not present.
	 *
	 * @param string|array $key Key.
	 *
	 * @return array|mixed|null
	 */
	public function get( $key ) {
		if ( \is_array( $key ) ) {
			$result = [];
			foreach ( $key as $column ) {
				$result[ $column ] = ( $this->data[ $column ] ?? null );
			}

			return $result;
		}
		else {
			return ( $this->data[ $key ] ?? null );
		}
	}

	/**
	 * Returns the name of the column in the database table which contains the primary key ID of the row.
	 *
	 * @return string The primary key ID of the row.
	 */
	protected function get_id_column_name() {
		if ( $this->instance_id_column !== null ) {
			return $this->instance_id_column;
		}

		return 'id';
	}

	/**
	 * Gets the primary key ID of this object.
	 *
	 * @param bool $disallow_null Whether to allow null IDs.
	 *
	 * @return array|mixed|null
	 *
	 * @throws Exception Primary key ID contains null value(s).
	 * @throws Exception Primary key ID missing from row or is null.
	 */
	public function id( $disallow_null = false ) {
		$id = $this->get( $this->get_id_column_name() );
		if ( $disallow_null ) {
			if ( \is_array( $id ) ) {
				foreach ( $id as $id_part ) {
					if ( $id_part === null ) {
						throw new Exception( 'Primary key ID contains null value(s)' );
					}
				}
			}
			elseif ( $id === null ) {
				throw new Exception( 'Primary key ID missing from row or is null' );
			}
		}

		return $id;
	}

	/**
	 * Sets a property to a particular value on this object.
	 *
	 * To set multiple properties at once, pass an associative array as the first parameter and leave out the second
	 * parameter. Flags the properties as 'dirty' so they will be saved to the database when save() is called.
	 *
	 * @param string|array $key   Key.
	 * @param string|null  $value Value.
	 *
	 * @return ORM
	 */
	public function set( $key, $value = null ) {
		return $this->set_orm_property( $key, $value );
	}

	/**
	 * Set a property to a particular value on this object as expression.
	 *
	 * To set multiple properties at once, pass an associative array as the first parameter and leave out the second
	 * parameter. Flags the properties as 'dirty' so they will be saved to the database when save() is called.
	 *
	 * @param string|array $key   Key.
	 * @param string|null  $value Value.
	 *
	 * @return ORM
	 */
	public function set_expr( $key, $value = null ) {
		return $this->set_orm_property( $key, $value, true );
	}

	/**
	 * Sets a property on the ORM object.
	 *
	 * @param string|array $key   Key.
	 * @param string|null  $value Value.
	 * @param bool         $expr  Expression.
	 *
	 * @return ORM
	 */
	protected function set_orm_property( $key, $value = null, $expr = false ) {
		if ( ! \is_array( $key ) ) {
			$key = [ $key => $value ];
		}
		foreach ( $key as $field => $value ) {
			$this->data[ $field ]         = $value;
			$this->dirty_fields[ $field ] = $value;
			if ( $expr === false && isset( $this->expr_fields[ $field ] ) ) {
				unset( $this->expr_fields[ $field ] );
			}
			elseif ( $expr === true ) {
				$this->expr_fields[ $field ] = true;
			}
		}

		return $this;
	}

	/**
	 * Checks whether the given field has been changed since this object was saved.
	 *
	 * @param mixed $key Key.
	 *
	 * @return bool
	 */
	public function is_dirty( $key ) {
		return \array_key_exists( $key, $this->dirty_fields );
	}

	/**
	 * Checks whether the model was the result of a call to create() or not.
	 *
	 * @return bool
	 */
	public function is_new() {
		return $this->is_new;
	}

	/**
	 * Saves any fields which have been modified on this object to the database.
	 *
	 * @return bool True on success.
	 *
	 * @throws Exception Primary key ID contains null value(s).
	 * @throws Exception Primary key ID missing from row or is null.
	 */
	public function save() {
		global $wpdb;

		// Remove any expression fields as they are already baked into the query.
		$values = \array_values( \array_diff_key( $this->dirty_fields, $this->expr_fields ) );
		if ( ! $this->is_new ) {
			// UPDATE.
			// If there are no dirty values, do nothing.
			if ( empty( $values ) && empty( $this->expr_fields ) ) {
				return true;
			}
			$query = \implode( ' ', [ $this->build_update(), $this->add_id_column_conditions() ] );

			$id = $this->id( true );
			if ( \is_array( $id ) ) {
				$values = \array_merge( $values, \array_values( $id ) );
			}
			else {
				$values[] = $id;
			}
		}
		else {
			// INSERT.
			$query = $this->build_insert();
		}
		$success = self::execute( $query, $values );
		// If we've just inserted a new record, set the ID of this object.
		if ( $this->is_new ) {
			$this->is_new = false;
			if ( $this->count_null_id_columns() !== 0 ) {
				$column = $this->get_id_column_name();
				// If the primary key is compound, assign the last inserted id to the first column.
				if ( \is_array( $column ) ) {
					$column = \reset( $column );
				}
				// Explicitly cast to int to make dealing with Id's simpler.
				$this->data[ $column ] = (int) $wpdb->insert_id;
			}
		}
		$this->dirty_fields = [];
		$this->expr_fields  = [];

		return $success;
	}

	/**
	 * Extracts and gathers all dirty column names from the given model instances.
	 *
	 * @param array $models Array of model instances to be inserted.
	 *
	 * @return array The distinct set of columns that are dirty in at least one of the models.
	 *
	 * @throws InvalidArgumentException Instance to be inserted is not a new one.
	 */
	public function get_dirty_column_names( $models ) {
		$dirty_column_names = [];

		foreach ( $models as $model ) {
			if ( ! $model->orm->is_new() ) {
				throw new InvalidArgumentException( 'Instance to be inserted is not a new one' );
			}

			// Remove any expression fields as they are already baked into the query.
			$dirty_fields       = \array_diff_key( $model->orm->dirty_fields, $model->orm->expr_fields );
			$dirty_column_names = \array_merge( $dirty_column_names, $dirty_fields );
		}

		$dirty_column_names = \array_keys( $dirty_column_names );

		return $dirty_column_names;
	}

	/**
	 * Inserts multiple rows in a single query. Expects new rows as it's a strictly insert function, not an update one.
	 *
	 * @example From the Indexable_Link_Builder class: $this->seo_links_repository->query()->insert_many( $links );
	 *
	 * @param array $models Array of model instances to be inserted.
	 *
	 * @return bool True for successful insert, false for failed.
	 *
	 * @throws InvalidArgumentException Invalid instances to be inserted.
	 * @throws InvalidArgumentException Instance to be inserted is not a new one.
	 */
	public function insert_many( $models ) {
		// Validate the input first.
		if ( ! \is_array( $models ) ) {
			throw new InvalidArgumentException( 'Invalid instances to be inserted' );
		}

		if ( empty( $models ) ) {
			return true;
		}

		$success = true;

		/**
		 * Filter: 'wpseo_chunk_bulked_insert_queries' - Allow filtering the chunk size of each bulked INSERT query.
		 *
		 * @param int $chunk_size The chunk size of the bulked INSERT queries.
		 */
		$chunk = \apply_filters( 'wpseo_chunk_bulk_insert_queries', 100 );
		$chunk = ! \is_int( $chunk ) ? 100 : $chunk;
		$chunk = ( $chunk <= 0 ) ? 100 : $chunk;

		$chunked_models = \array_chunk( $models, $chunk );
		foreach ( $chunked_models as $models_chunk ) {
			$values = [];

			// First, we'll gather all the dirty fields throughout the models to be inserted.
			$dirty_column_names = $this->get_dirty_column_names( $models_chunk );

			// Now, we're creating all dirty fields throughout the models and
			// setting them to null if they don't exist in each model.
			foreach ( $models_chunk as $model ) {
				$model_values = [];

				foreach ( $dirty_column_names as $dirty_column ) {
					// Set the value to null if it hasn't been set already.
					if ( ! isset( $model->orm->dirty_fields[ $dirty_column ] ) ) {
						$model->orm->dirty_fields[ $dirty_column ] = null;
					}

					// Only register the value if it is not null.
					if ( $model->orm->dirty_fields[ $dirty_column ] !== null ) {
						$model_values[] = $model->orm->dirty_fields[ $dirty_column ];
					}
				}
				$values = \array_merge( $values, $model_values );
			}

			// We now have the same set of dirty columns in all our models and also gathered all values.
			$query   = $this->build_insert_many( $models_chunk, $dirty_column_names );
			$success = $success && (bool) self::execute( $query, $values );
		}

		return $success;
	}

	/**
	 * Updates many records in the database.
	 *
	 * @return int|bool The number of rows changed if the query was succesful. False otherwise.
	 */
	public function update_many() {
		// Remove any expression fields as they are already baked into the query.
		$values = \array_values( \array_diff_key( $this->dirty_fields, $this->expr_fields ) );

		// UPDATE.
		// If there are no dirty values, do nothing.
		if ( empty( $values ) && empty( $this->expr_fields ) ) {
			return true;
		}

		$query = $this->join_if_not_empty( ' ', [ $this->build_update(), $this->build_where() ] );

		$success            = self::execute( $query, \array_merge( $values, $this->values ) );
		$this->dirty_fields = [];
		$this->expr_fields  = [];

		return $success;
	}

	/**
	 * Adds a WHERE clause for every column that belongs to the primary key.
	 *
	 * @return string The where part of the query.
	 */
	public function add_id_column_conditions() {
		$query   = [];
		$query[] = 'WHERE';
		$keys    = \is_array( $this->get_id_column_name() ) ? $this->get_id_column_name() : [ $this->get_id_column_name() ];
		$first   = true;
		foreach ( $keys as $key ) {
			if ( $first ) {
				$first = false;
			}
			else {
				$query[] = 'AND';
			}
			$query[] = $this->quote_identifier( $key );
			$query[] = '= %s';
		}

		return \implode( ' ', $query );
	}

	/**
	 * Builds an UPDATE query.
	 *
	 * @return string The update query.
	 */
	protected function build_update() {
		$query      = [];
		$query[]    = "UPDATE {$this->quote_identifier($this->table_name)} SET";
		$field_list = [];
		foreach ( $this->dirty_fields as $key => $value ) {
			if ( ! \array_key_exists( $key, $this->expr_fields ) ) {
				$value = ( $value === null ) ? 'NULL' : '%s';
			}
			$field_list[] = "{$this->quote_identifier($key)} = {$value}";
		}
		$query[] = \implode( ', ', $field_list );

		return \implode( ' ', $query );
	}

	/**
	 * Builds an INSERT query.
	 *
	 * @return string The insert query.
	 */
	protected function build_insert() {
		$query        = [];
		$query[]      = 'INSERT INTO';
		$query[]      = $this->quote_identifier( $this->table_name );
		$field_list   = \array_map( [ $this, 'quote_identifier' ], \array_keys( $this->dirty_fields ) );
		$query[]      = '(' . \implode( ', ', $field_list ) . ')';
		$query[]      = 'VALUES';
		$placeholders = $this->create_placeholders( $this->dirty_fields );
		$query[]      = "({$placeholders})";

		return \implode( ' ', $query );
	}

	/**
	 * Builds a bulk INSERT query.
	 *
	 * @param array $models             Array of model instances to be inserted.
	 * @param array $dirty_column_names Array of dirty fields to be used in INSERT.
	 *
	 * @return string The insert query.
	 */
	protected function build_insert_many( $models, $dirty_column_names ) {
		$example_model      = $models[0];
		$total_placeholders = '';

		$query      = [];
		$query[]    = 'INSERT INTO';
		$query[]    = $this->quote_identifier( $example_model->orm->table_name );
		$field_list = \array_map( [ $this, 'quote_identifier' ], $dirty_column_names );
		$query[]    = '(' . \implode( ', ', $field_list ) . ')';
		$query[]    = 'VALUES';

		// We assign placeholders per model for dirty fields that have values and NULL for dirty fields that don't.
		foreach ( $models as $model ) {
			$placeholder = [];
			foreach ( $dirty_column_names as $dirty_field ) {
				$placeholder[] = ( $model->orm->dirty_fields[ $dirty_field ] === null ) ? 'NULL' : '%s';
			}
			$placeholders        = \implode( ', ', $placeholder );
			$total_placeholders .= "({$placeholders}),";
		}

		$query[] = \rtrim( $total_placeholders, ',' );
		return \implode( ' ', $query );
	}

	/**
	 * Deletes this record from the database.
	 *
	 * @return string The delete query.
	 *
	 * @throws Exception Primary key ID contains null value(s).
	 * @throws Exception Primary key ID missing from row or is null.
	 */
	public function delete() {
		$query = [ 'DELETE FROM', $this->quote_identifier( $this->table_name ), $this->add_id_column_conditions() ];

		return self::execute( \implode( ' ', $query ), \is_array( $this->id( true ) ) ? \array_values( $this->id( true ) ) : [ $this->id( true ) ] );
	}

	/**
	 * Deletes many records from the database.
	 *
	 * @return bool|int Response of wpdb::query.
	 */
	public function delete_many() {
		// Build and return the full DELETE statement by concatenating
		// the results of calling each separate builder method.
		$query = $this->join_if_not_empty(
			' ',
			[
				'DELETE FROM',
				$this->quote_identifier( $this->table_name ),
				$this->build_where(),
			]
		);

		return self::execute( $query, $this->values );
	}

	/*
	 * ---  ArrayAccess  ---
	 */

	/**
	 * Checks whether the data has the key.
	 *
	 * @param mixed $offset Key.
	 *
	 * @return bool Whether the data has the key.
	 */
	#[ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return \array_key_exists( $offset, $this->data );
	}

	/**
	 * Retrieves the value of the key.
	 *
	 * @param mixed $offset Key.
	 *
	 * @return array|mixed|null The value.
	 */
	#[ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return $this->get( $offset );
	}

	/**
	 * Sets the value of the key.
	 *
	 * @param string|int $offset Key.
	 * @param mixed      $value  Value.
	 *
	 * @return void
	 */
	#[ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		if ( $offset === null ) {
			return;
		}
		$this->set( $offset, $value );
	}

	/**
	 * Removes the given key from the data.
	 *
	 * @param mixed $offset Key.
	 *
	 * @return void
	 */
	#[ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
		unset( $this->data[ $offset ] );
		unset( $this->dirty_fields[ $offset ] );
	}

	/*
	 * --- MAGIC METHODS ---
	 */

	/**
	 * Handles magic get via offset.
	 *
	 * @param mixed $key Key.
	 *
	 * @return array|mixed|null The value in the offset.
	 */
	public function __get( $key ) {
		return $this->offsetGet( $key );
	}

	/**
	 * Handles magic set via offset.
	 *
	 * @param string|int $key   Key.
	 * @param mixed      $value Value.
	 *
	 * @return void
	 */
	public function __set( $key, $value ) {
		$this->offsetSet( $key, $value );
	}

	/**
	 * Handles magic unset via offset.
	 *
	 * @param mixed $key Key.
	 *
	 * @return void
	 */
	public function __unset( $key ) {
		$this->offsetUnset( $key );
	}

	/**
	 * Handles magic isset via offset.
	 *
	 * @param mixed $key Key.
	 *
	 * @return bool Whether the offset has the key.
	 */
	public function __isset( $key ) {
		return $this->offsetExists( $key );
	}
}
