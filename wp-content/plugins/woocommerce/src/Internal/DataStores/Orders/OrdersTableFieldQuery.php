<?php
namespace Automattic\WooCommerce\Internal\DataStores\Orders;

defined( 'ABSPATH' ) || exit;

/**
 * Provides the implementation for `field_query` in {@see OrdersTableQuery} used to build
 * complex queries against order fields in the database.
 *
 * @internal
 */
class OrdersTableFieldQuery {

	/**
	 * List of valid SQL operators to use as field_query 'compare' values.
	 *
	 * @var array
	 */
	private const VALID_COMPARISON_OPERATORS = array(
		'=',
		'!=',
		'LIKE',
		'NOT LIKE',
		'IN',
		'NOT IN',
		'EXISTS',
		'NOT EXISTS',
		'RLIKE',
		'REGEXP',
		'NOT REGEXP',
		'>',
		'>=',
		'<',
		'<=',
		'BETWEEN',
		'NOT BETWEEN',
	);

	/**
	 * The original query object.
	 *
	 * @var OrdersTableQuery
	 */
	private $query = null;

	/**
	 * Determines whether the field query should produce no results due to an invalid argument.
	 *
	 * @var boolean
	 */
	private $force_no_results = false;

	/**
	 * Holds a sanitized version of the `field_query`.
	 *
	 * @var array
	 */
	private $queries = array();

	/**
	 * JOIN clauses to add to the main SQL query.
	 *
	 * @var array
	 */
	private $join = array();

	/**
	 * WHERE clauses to add to the main SQL query.
	 *
	 * @var array
	 */
	private $where = array();

	/**
	 * Table aliases in use by the field query. Used to keep track of JOINs and optimize when possible.
	 *
	 * @var array
	 */
	private $table_aliases = array();


	/**
	 * Constructor.
	 *
	 * @param OrdersTableQuery $q The main query being performed.
	 */
	public function __construct( OrdersTableQuery $q ) {
		$field_query = $q->get( 'field_query' );

		if ( ! $field_query || ! is_array( $field_query ) ) {
			return;
		}

		$this->query   = $q;
		$this->queries = $this->sanitize_query( $field_query );
		$this->where   = ( ! $this->force_no_results ) ? $this->process( $this->queries ) : '1=0';
	}

	/**
	 * Sanitizes the field_query argument.
	 *
	 * @param array $q A field_query array.
	 * @return array A sanitized field query array.
	 * @throws \Exception When field table info is missing.
	 */
	private function sanitize_query( array $q ) {
		$sanitized = array();

		foreach ( $q as $key => $arg ) {
			if ( 'relation' === $key ) {
				$relation = $arg;
			} elseif ( ! is_array( $arg ) ) {
				continue;
			} elseif ( $this->is_atomic( $arg ) ) {
				if ( isset( $arg['value'] ) && array() === $arg['value'] ) {
					continue;
				}

				// Sanitize 'compare'.
				$arg['compare'] = strtoupper( $arg['compare'] ?? '=' );
				$arg['compare'] = in_array( $arg['compare'], self::VALID_COMPARISON_OPERATORS, true ) ? $arg['compare'] : '=';

				if ( '=' === $arg['compare'] && isset( $arg['value'] ) && is_array( $arg['value'] ) ) {
					$arg['compare'] = 'IN';
				}

				// Sanitize 'cast'.
				$arg['cast'] = $this->sanitize_cast_type( $arg['type'] ?? '' );

				$field_info = $this->query->get_field_mapping_info( $arg['field'] );
				if ( ! $field_info ) {
					$this->force_no_results = true;
					continue;
				}

				$arg = array_merge( $arg, $field_info );

				$sanitized[ $key ] = $arg;
			} else {
				$sanitized_arg = $this->sanitize_query( $arg );

				if ( $sanitized_arg ) {
					$sanitized[ $key ] = $sanitized_arg;
				}
			}
		}

		if ( $sanitized ) {
			$sanitized['relation'] = 1 === count( $sanitized ) ? 'OR' : $this->sanitize_relation( $relation ?? 'AND' );
		}

		return $sanitized;
	}

	/**
	 * Makes sure we use an AND or OR relation. Defaults to AND.
	 *
	 * @param string $relation An unsanitized relation prop.
	 * @return string
	 */
	private function sanitize_relation( string $relation ): string {
		if ( ! empty( $relation ) && 'OR' === strtoupper( $relation ) ) {
			return 'OR';
		}

		return 'AND';
	}

	/**
	 * Processes field_query entries and generates the necessary table aliases, JOIN statements and WHERE conditions.
	 *
	 * @param array $q A field query.
	 * @return string An SQL WHERE statement.
	 */
	private function process( array $q ) {
		$where = '';

		if ( empty( $q ) ) {
			return $where;
		}

		if ( $this->is_atomic( $q ) ) {
			$q['alias'] = $this->find_or_create_table_alias_for_clause( $q );
			$where      = $this->generate_where_for_clause( $q );
		} else {
			$relation = $q['relation'];
			unset( $q['relation'] );

			foreach ( $q as $query ) {
				$chunks[] = $this->process( $query );
			}

			if ( 1 === count( $chunks ) ) {
				$where = $chunks[0];
			} else {
				$where = '(' . implode( " {$relation} ", $chunks ) . ')';
			}
		}

		return $where;
	}

	/**
	 * Checks whether a given field_query clause is atomic or not (i.e. not nested).
	 *
	 * @param array $q The field_query clause.
	 * @return boolean TRUE if atomic, FALSE otherwise.
	 */
	private function is_atomic( $q ) {
		return isset( $q['field'] );
	}

	/**
	 * Finds a common table alias that the field_query clause can use, or creates one.
	 *
	 * @param array $q       An atomic field_query clause.
	 * @return string A table alias for use in an SQL JOIN clause.
	 * @throws \Exception When table info for clause is missing.
	 */
	private function find_or_create_table_alias_for_clause( $q ) {
		global $wpdb;

		if ( ! empty( $q['alias'] ) ) {
			return $q['alias'];
		}

		if ( empty( $q['table'] ) || empty( $q['column'] ) ) {
			throw new \Exception( __( 'Missing table info for query arg.', 'woocommerce' ) );
		}

		$join = '';

		if ( isset( $q['mapping_id'] ) ) {
			// Re-use JOINs and aliases from OrdersTableQuery for core tables.
			$alias = $this->query->get_core_mapping_alias( $q['mapping_id'] );
			$join  = $this->query->get_core_mapping_join( $q['mapping_id'] );
		} else {
			$alias = $q['table'];
			$join  = '';
		}

		if ( in_array( $alias, $this->table_aliases, true ) ) {
			return $alias;
		}

		$this->table_aliases[] = $alias;

		if ( $join ) {
			$this->join[ $alias ] = $join;
		}

		return $alias;
	}

	/**
	 * Returns the correct type for a given clause 'type'.
	 *
	 * @param string $type MySQL type.
	 * @return string MySQL type.
	 */
	private function sanitize_cast_type( $type ) {
		$clause_type = strtoupper( $type );

		if ( ! $clause_type || ! preg_match( '/^(?:BINARY|CHAR|DATE|DATETIME|SIGNED|UNSIGNED|TIME|NUMERIC(?:\(\d+(?:,\s?\d+)?\))?|DECIMAL(?:\(\d+(?:,\s?\d+)?\))?)$/', $clause_type ) ) {
			return 'CHAR';
		}

		if ( 'NUMERIC' === $clause_type ) {
			$clause_type = 'SIGNED';
		}

		return $clause_type;
	}

	/**
	 * Generates an SQL WHERE clause for a given field_query atomic clause.
	 *
	 * @param array $clause An atomic field_query clause.
	 * @return string An SQL WHERE clause or an empty string if $clause is invalid.
	 */
	private function generate_where_for_clause( $clause ): string {
		global $wpdb;

		$clause_value = $clause['value'] ?? '';

		if ( in_array( $clause['compare'], array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ), true ) ) {
			if ( ! is_array( $clause_value ) ) {
				$clause_value = preg_split( '/[,\s]+/', $clause_value );
			}
		} elseif ( is_string( $clause_value ) ) {
			$clause_value = trim( $clause_value );
		}

		$clause_compare = $clause['compare'];

		switch ( $clause_compare ) {
			case 'IN':
			case 'NOT IN':
				$where = $wpdb->prepare( '(' . substr( str_repeat( ',%s', count( $clause_value ) ), 1 ) . ')', $clause_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				break;
			case 'BETWEEN':
			case 'NOT BETWEEN':
				$where = $wpdb->prepare( '%s AND %s', $clause_value[0], $clause_value[1] ?? $clause_value[0] );
				break;
			case 'LIKE':
			case 'NOT LIKE':
				$where = $wpdb->prepare( '%s', '%' . $wpdb->esc_like( $clause_value ) . '%' );
				break;
			case 'EXISTS':
				// EXISTS with a value is interpreted as '='.
				if ( $clause_value ) {
					$clause_compare = '=';
					$where          = $wpdb->prepare( '%s', $clause_value );
				} else {
					$clause_compare = 'IS NOT';
					$where          = 'NULL';
				}

				break;
			case 'NOT EXISTS':
				// 'value' is ignored for NOT EXISTS.
				$clause_compare = 'IS';
				$where          = 'NULL';
				break;
			default:
				$where = $wpdb->prepare( '%s', $clause_value );
				break;
		}

		if ( $where ) {
			if ( 'CHAR' === $clause['cast'] ) {
				return "`{$clause['alias']}`.`{$clause['column']}` {$clause_compare} {$where}";
			} else {
				return "CAST(`{$clause['alias']}`.`{$clause['column']}` AS {$clause['cast']}) {$clause_compare} {$where}";
			}
		}

		return '';
	}

	/**
	 * Returns JOIN and WHERE clauses to be appended to the main SQL query.
	 *
	 * @return array {
	 *     @type string $join  JOIN clause.
	 *     @type string $where WHERE clause.
	 * }
	 */
	public function get_sql_clauses() {
		return array(
			'join'  => $this->join,
			'where' => $this->where ? array( $this->where ) : array(),
		);
	}

}
