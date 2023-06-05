<?php
namespace Automattic\WooCommerce\Internal\DataStores\Orders;

defined( 'ABSPATH' ) || exit;

/**
 * Class used to implement meta queries for the orders table datastore via {@see OrdersTableQuery}.
 * Heavily inspired by WordPress' own `WP_Meta_Query` for backwards compatibility reasons.
 *
 * Parts of the implementation have been adapted from {@link https://core.trac.wordpress.org/browser/tags/6.0.1/src/wp-includes/class-wp-meta-query.php}.
 */
class OrdersTableMetaQuery {

	/**
	 * List of non-numeric SQL operators used for comparisons in meta queries.
	 *
	 * @var array
	 */
	private const NON_NUMERIC_OPERATORS = array(
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
	);

	/**
	 * List of numeric SQL operators used for comparisons in meta queries.
	 *
	 * @var array
	 */
	private const NUMERIC_OPERATORS = array(
		'>',
		'>=',
		'<',
		'<=',
		'BETWEEN',
		'NOT BETWEEN',

	);

	/**
	 * Prefix used when generating aliases for the metadata table.
	 *
	 * @var string
	 */
	private const ALIAS_PREFIX = 'meta';

	/**
	 * Name of the main orders table.
	 *
	 * @var string
	 */
	private $meta_table = '';

	/**
	 * Name of the metadata table.
	 *
	 * @var string
	 */
	private $orders_table = '';

	/**
	 * Sanitized `meta_query`.
	 *
	 * @var array
	 */
	private $queries = array();

	/**
	 * Flat list of clauses by name.
	 *
	 * @var array
	 */
	private $flattened_clauses = array();

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
	 * Table aliases in use by the meta query. Used to optimize JOINs when possible.
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
		$meta_query = $q->get( 'meta_query' );

		if ( ! $meta_query ) {
			return;
		}

		$this->queries = $this->sanitize_meta_query( $meta_query );

		$this->meta_table   = $q->get_table_name( 'meta' );
		$this->orders_table = $q->get_table_name( 'orders' );

		$this->build_query();
	}

	/**
	 * Returns JOIN and WHERE clauses to be appended to the main SQL query.
	 *
	 * @return array {
	 *     @type string $join  JOIN clause.
	 *     @type string $where WHERE clause.
	 * }
	 */
	public function get_sql_clauses(): array {
		return array(
			'join'  => $this->sanitize_join( $this->join ),
			'where' => $this->flatten_where_clauses( $this->where ),
		);
	}

	/**
	 * Returns a list of names (corresponding to meta_query clauses) that can be used as an 'orderby' arg.
	 *
	 * @since 7.4
	 *
	 * @return array
	 */
	public function get_orderby_keys(): array {
		if ( ! $this->flattened_clauses ) {
			return array();
		}

		$keys   = array();
		$keys[] = 'meta_value';
		$keys[] = 'meta_value_num';

		$first_clause = reset( $this->flattened_clauses );
		if ( $first_clause && ! empty( $first_clause['key'] ) ) {
			$keys[] = $first_clause['key'];
		}

		$keys = array_merge(
			$keys,
			array_keys( $this->flattened_clauses )
		);

		return $keys;
	}

	/**
	 * Returns an SQL fragment for the given meta_query key that can be used in an ORDER BY clause.
	 * Call {@see 'get_orderby_keys'} to obtain a list of valid keys.
	 *
	 * @since 7.4
	 *
	 * @param string $key The key name.
	 * @return string
	 *
	 * @throws \Exception When an invalid key is passed.
	 */
	public function get_orderby_clause_for_key( string $key ): string {
		$clause = false;

		if ( isset( $this->flattened_clauses[ $key ] ) ) {
			$clause = $this->flattened_clauses[ $key ];
		} else {
			$first_clause = reset( $this->flattened_clauses );

			if ( $first_clause && ! empty( $first_clause['key'] ) ) {
				if ( 'meta_value_num' === $key ) {
					return "{$first_clause['alias']}.meta_value+0";
				}

				if ( 'meta_value' === $key || $first_clause['key'] === $key ) {
					$clause = $first_clause;
				}
			}
		}

		if ( ! $clause ) {
			// translators: %s is a meta_query key.
			throw new \Exception( sprintf( __( 'Invalid meta_query clause key: %s.', 'woocommerce' ), $key ) );
		}

		return "CAST({$clause['alias']}.meta_value AS {$clause['cast']})";
	}

	/**
	 * Checks whether a given meta_query clause is atomic or not (i.e. not nested).
	 *
	 * @param array $arg The meta_query clause.
	 * @return boolean TRUE if atomic, FALSE otherwise.
	 */
	private function is_atomic( array $arg ): bool {
		return isset( $arg['key'] ) || isset( $arg['value'] );
	}

	/**
	 * Sanitizes the meta_query argument.
	 *
	 * @param array $q A meta_query array.
	 * @return array A sanitized meta query array.
	 */
	private function sanitize_meta_query( array $q ): array {
		$sanitized = array();

		foreach ( $q as $key => $arg ) {
			if ( 'relation' === $key ) {
				$relation = $arg;
			} elseif ( ! is_array( $arg ) ) {
				continue;
			} elseif ( $this->is_atomic( $arg ) ) {
				if ( isset( $arg['value'] ) && array() === $arg['value'] ) {
					unset( $arg['value'] );
				}

				$arg['compare']     = isset( $arg['compare'] ) ? strtoupper( $arg['compare'] ) : ( isset( $arg['value'] ) && is_array( $arg['value'] ) ? 'IN' : '=' );
				$arg['compare_key'] = isset( $arg['compare_key'] ) ? strtoupper( $arg['compare_key'] ) : ( isset( $arg['key'] ) && is_array( $arg['key'] ) ? 'IN' : '=' );

				if ( ! in_array( $arg['compare'], self::NON_NUMERIC_OPERATORS, true ) && ! in_array( $arg['compare'], self::NUMERIC_OPERATORS, true ) ) {
					$arg['compare'] = '=';
				}

				if ( ! in_array( $arg['compare_key'], self::NON_NUMERIC_OPERATORS, true ) ) {
					$arg['compare_key'] = '=';
				}

				$sanitized[ $key ]          = $arg;
				$sanitized[ $key ]['index'] = $key;
			} else {
				$sanitized_arg = $this->sanitize_meta_query( $arg );

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
	 * Returns the correct type for a given meta type.
	 *
	 * @param string $type MySQL type.
	 * @return string MySQL type.
	 */
	private function sanitize_cast_type( string $type = '' ): string {
		$meta_type = strtoupper( $type );

		if ( ! $meta_type || ! preg_match( '/^(?:BINARY|CHAR|DATE|DATETIME|SIGNED|UNSIGNED|TIME|NUMERIC(?:\(\d+(?:,\s?\d+)?\))?|DECIMAL(?:\(\d+(?:,\s?\d+)?\))?)$/', $meta_type ) ) {
			return 'CHAR';
		}

		if ( 'NUMERIC' === $meta_type ) {
			$meta_type = 'SIGNED';
		}

		return $meta_type;
	}

	/**
	 * Makes sure a JOIN array does not have duplicates.
	 *
	 * @param array $join A JOIN array.
	 * @return array A sanitized JOIN array.
	 */
	private function sanitize_join( array $join ): array {
		return array_filter( array_unique( array_map( 'trim', $join ) ) );
	}

	/**
	 * Flattens a nested WHERE array.
	 *
	 * @param array $where A possibly nested WHERE array with AND/OR operators.
	 * @return string An SQL WHERE clause.
	 */
	private function flatten_where_clauses( $where ): string {
		if ( is_string( $where ) ) {
			return trim( $where );
		}

		$chunks   = array();
		$operator = $this->sanitize_relation( $where['operator'] ?? '' );

		foreach ( $where as $key => $w ) {
			if ( 'operator' === $key ) {
				continue;
			}

			$flattened = $this->flatten_where_clauses( $w );
			if ( $flattened ) {
				$chunks[] = $flattened;
			}
		}

		if ( $chunks ) {
			return '(' . implode( " {$operator} ", $chunks ) . ')';
		} else {
			return '';
		}
	}

	/**
	 * Builds all the required internal bits for this meta query.
	 *
	 * @return void
	 */
	private function build_query(): void {
		if ( ! $this->queries ) {
			return;
		}

		$queries     = $this->queries;
		$sql_where   = $this->process( $queries );
		$this->where = $sql_where;

	}

	/**
	 * Processes meta_query entries and generates the necessary table aliases, JOIN statements and WHERE conditions.
	 *
	 * @param array      $arg    A meta query.
	 * @param null|array $parent The parent of the element being processed.
	 * @return array A nested array of WHERE conditions.
	 */
	private function process( array &$arg, &$parent = null ): array {
		$where = array();

		if ( $this->is_atomic( $arg ) ) {
			$arg['alias'] = $this->find_or_create_table_alias_for_clause( $arg, $parent );
			$arg['cast']  = $this->sanitize_cast_type( $arg['type'] ?? '' );

			$where = array_filter(
				array(
					$this->generate_where_for_clause_key( $arg ),
					$this->generate_where_for_clause_value( $arg ),
				)
			);

			// Store clauses by their key for ORDER BY purposes.
			$flat_clause_key = is_int( $arg['index'] ) ? $arg['alias'] : $arg['index'];

			$unique_flat_key = $flat_clause_key;
			$i               = 1;
			while ( isset( $this->flattened_clauses[ $unique_flat_key ] ) ) {
				$unique_flat_key = $flat_clause_key . '-' . $i;
				$i++;
			}

			$this->flattened_clauses[ $unique_flat_key ] =& $arg;
		} else {
			// Nested.
			$relation = $arg['relation'];
			unset( $arg['relation'] );

			foreach ( $arg as $index => &$clause ) {
				$chunks[] = $this->process( $clause, $arg );
			}

			// Merge chunks of the form OR(m) with the surrounding clause.
			if ( 1 === count( $chunks ) ) {
				$where = $chunks[0];
			} else {
				$where = array_merge(
					array(
						'operator' => $relation,
					),
					$chunks
				);
			}
		}

		return $where;
	}

	/**
	 * Generates a JOIN clause to handle an atomic meta_query clause.
	 *
	 * @param array  $clause An atomic meta_query clause.
	 * @param string $alias  Metadata table alias to use.
	 * @return string An SQL JOIN clause.
	 */
	private function generate_join_for_clause( array $clause, string $alias ): string {
		global $wpdb;

		if ( 'NOT EXISTS' === $clause['compare'] ) {
			if ( 'LIKE' === $clause['compare_key'] ) {
				return $wpdb->prepare(
					"LEFT JOIN {$this->meta_table} AS {$alias} ON ( {$this->orders_table}.id = {$alias}.order_id AND {$alias}.meta_key LIKE %s )", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					'%' . $wpdb->esc_like( $clause['key'] ) . '%'
				);
			} else {
				return $wpdb->prepare(
					"LEFT JOIN {$this->meta_table} AS {$alias} ON ( {$this->orders_table}.id = {$alias}.order_id AND {$alias}.meta_key = %s )", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$clause['key']
				);
			}
		}

		return "INNER JOIN {$this->meta_table} AS {$alias} ON ( {$this->orders_table}.id = {$alias}.order_id )";
	}

	/**
	 * Finds a common table alias that the meta_query clause can use, or creates one.
	 *
	 * @param array $clause       An atomic meta_query clause.
	 * @param array $parent_query The parent query this clause is in.
	 * @return string A table alias for use in an SQL JOIN clause.
	 */
	private function find_or_create_table_alias_for_clause( array $clause, array $parent_query ): string {
		if ( ! empty( $clause['alias'] ) ) {
			return $clause['alias'];
		}

		$alias    = false;
		$siblings = array_filter(
			$parent_query,
			array( __CLASS__, 'is_atomic' )
		);

		foreach ( $siblings as $sibling ) {
			if ( empty( $sibling['alias'] ) ) {
				continue;
			}

			if ( $this->is_operator_compatible_with_shared_join( $clause, $sibling, $parent_query['relation'] ?? 'AND' ) ) {
				$alias = $sibling['alias'];
				break;
			}
		}

		if ( ! $alias ) {
			$alias                 = self::ALIAS_PREFIX . count( $this->table_aliases );
			$this->join[]          = $this->generate_join_for_clause( $clause, $alias );
			$this->table_aliases[] = $alias;
		}

		return $alias;
	}

	/**
	 * Checks whether two meta_query clauses can share a JOIN.
	 *
	 * @param array  $clause    An atomic meta_query clause.
	 * @param array  $sibling   An atomic meta_query clause.
	 * @param string $relation The relation involving both clauses.
	 * @return boolean TRUE if the clauses can share a table alias, FALSE otherwise.
	 */
	private function is_operator_compatible_with_shared_join( array $clause, array $sibling, string $relation = 'AND' ): bool {
		if ( ! $this->is_atomic( $clause ) || ! $this->is_atomic( $sibling ) ) {
			return false;
		}

		$valid_operators = array();

		if ( 'OR' === $relation ) {
			$valid_operators = array( '=', 'IN', 'BETWEEN', 'LIKE', 'REGEXP', 'RLIKE', '>', '>=', '<', '<=' );
		} elseif ( isset( $sibling['key'] ) && isset( $clause['key'] ) && $sibling['key'] === $clause['key'] ) {
			$valid_operators = array( '!=', 'NOT IN', 'NOT LIKE' );
		}

		return in_array( strtoupper( $clause['compare'] ), $valid_operators, true ) && in_array( strtoupper( $sibling['compare'] ), $valid_operators, true );
	}

	/**
	 * Generates an SQL WHERE clause for a given meta_query atomic clause based on its meta key.
	 * Adapted from WordPress' `WP_Meta_Query::get_sql_for_clause()` method.
	 *
	 * @param array $clause An atomic meta_query clause.
	 * @return string An SQL WHERE clause or an empty string if $clause is invalid.
	 */
	private function generate_where_for_clause_key( array $clause ): string {
		global $wpdb;

		if ( ! array_key_exists( 'key', $clause ) ) {
			return '';
		}

		if ( 'NOT EXISTS' === $clause['compare'] ) {
			return "{$clause['alias']}.order_id IS NULL";
		}

		$alias = $clause['alias'];

		if ( in_array( $clause['compare_key'], array( '!=', 'NOT IN', 'NOT LIKE', 'NOT EXISTS', 'NOT REGEXP' ), true ) ) {
			$i                     = count( $this->table_aliases );
			$subquery_alias        = self::ALIAS_PREFIX . $i;
			$this->table_aliases[] = $subquery_alias;

			$meta_compare_string_start  = 'NOT EXISTS (';
			$meta_compare_string_start .= "SELECT 1 FROM {$this->meta_table} {$subquery_alias} ";
			$meta_compare_string_start .= "WHERE {$subquery_alias}.order_id = {$alias}.order_id ";
			$meta_compare_string_end    = 'LIMIT 1';
			$meta_compare_string_end   .= ')';
		}

		switch ( $clause['compare_key'] ) {
			case '=':
			case 'EXISTS':
				$where = $wpdb->prepare( "$alias.meta_key = %s", trim( $clause['key'] ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				break;
			case 'LIKE':
				$meta_compare_value = '%' . $wpdb->esc_like( trim( $clause['key'] ) ) . '%';
				$where              = $wpdb->prepare( "$alias.meta_key LIKE %s", $meta_compare_value ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				break;
			case 'IN':
				$meta_compare_string = "$alias.meta_key IN (" . substr( str_repeat( ',%s', count( $clause['key'] ) ), 1 ) . ')';
				$where               = $wpdb->prepare( $meta_compare_string, $clause['key'] ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				break;
			case 'RLIKE':
			case 'REGEXP':
				$operator = $clause['compare_key'];
				if ( isset( $clause['type_key'] ) && 'BINARY' === strtoupper( $clause['type_key'] ) ) {
					$cast = 'BINARY';
				} else {
					$cast = '';
				}
				$where = $wpdb->prepare( "$alias.meta_key $operator $cast %s", trim( $clause['key'] ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				break;
			case '!=':
			case 'NOT EXISTS':
				$meta_compare_string = $meta_compare_string_start . "AND $subquery_alias.meta_key = %s " . $meta_compare_string_end;
				$where               = $wpdb->prepare( $meta_compare_string, $clause['key'] ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				break;
			case 'NOT LIKE':
				$meta_compare_string = $meta_compare_string_start . "AND $subquery_alias.meta_key LIKE %s " . $meta_compare_string_end;

				$meta_compare_value = '%' . $wpdb->esc_like( trim( $clause['key'] ) ) . '%';
				$where              = $wpdb->prepare( $meta_compare_string, $meta_compare_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				break;
			case 'NOT IN':
				$array_subclause     = '(' . substr( str_repeat( ',%s', count( $clause['key'] ) ), 1 ) . ') ';
				$meta_compare_string = $meta_compare_string_start . "AND $subquery_alias.meta_key IN " . $array_subclause . $meta_compare_string_end;
				$where               = $wpdb->prepare( $meta_compare_string, $clause['key'] ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				break;
			case 'NOT REGEXP':
				$operator = $clause['compare_key'];
				if ( isset( $clause['type_key'] ) && 'BINARY' === strtoupper( $clause['type_key'] ) ) {
					$cast = 'BINARY';
				} else {
					$cast = '';
				}

				$meta_compare_string = $meta_compare_string_start . "AND $subquery_alias.meta_key REGEXP $cast %s " . $meta_compare_string_end;
				$where               = $wpdb->prepare( $meta_compare_string, $clause['key'] ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				break;
			default:
				$where = '';
				break;
		}

		return $where;
	}

	/**
	 * Generates an SQL WHERE clause for a given meta_query atomic clause based on its meta value.
	 * Adapted from WordPress' `WP_Meta_Query::get_sql_for_clause()` method.
	 *
	 * @param array $clause An atomic meta_query clause.
	 * @return string An SQL WHERE clause or an empty string if $clause is invalid.
	 */
	private function generate_where_for_clause_value( $clause ): string {
		global $wpdb;

		if ( ! array_key_exists( 'value', $clause ) ) {
			return '';
		}

		$meta_value = $clause['value'];

		if ( in_array( $clause['compare'], array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ), true ) ) {
			if ( ! is_array( $meta_value ) ) {
				$meta_value = preg_split( '/[,\s]+/', $meta_value );
			}
		} elseif ( is_string( $meta_value ) ) {
			$meta_value = trim( $meta_value );
		}

		$meta_compare = $clause['compare'];

		switch ( $meta_compare ) {
			case 'IN':
			case 'NOT IN':
				$where = $wpdb->prepare( '(' . substr( str_repeat( ',%s', count( $meta_value ) ), 1 ) . ')', $meta_value ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				break;

			case 'BETWEEN':
			case 'NOT BETWEEN':
				$where = $wpdb->prepare( '%s AND %s', $meta_value[0], $meta_value[1] );
				break;

			case 'LIKE':
			case 'NOT LIKE':
				$where = $wpdb->prepare( '%s', '%' . $wpdb->esc_like( $meta_value ) . '%' );
				break;

			// EXISTS with a value is interpreted as '='.
			case 'EXISTS':
				$meta_compare = '=';
				$where        = $wpdb->prepare( '%s', $meta_value );
				break;

			// 'value' is ignored for NOT EXISTS.
			case 'NOT EXISTS':
				$where = '';
				break;

			default:
				$where = $wpdb->prepare( '%s', $meta_value );
				break;
		}

		if ( $where ) {
			if ( 'CHAR' === $clause['cast'] ) {
				return "{$clause['alias']}.meta_value {$meta_compare} {$where}";
			} else {
				return "CAST({$clause['alias']}.meta_value AS {$clause['cast']}) {$meta_compare} {$where}";
			}
		}
	}

}
