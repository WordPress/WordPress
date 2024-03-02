<?php
/**
 * Network API: WP_Network_Query class
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 4.6.0
 */

/**
 * Core class used for querying networks.
 *
 * @since 4.6.0
 *
 * @see WP_Network_Query::__construct() for accepted arguments.
 */
#[AllowDynamicProperties]
class WP_Network_Query {

	/**
	 * SQL for database query.
	 *
	 * @since 4.6.0
	 * @var string
	 */
	public $request;

	/**
	 * SQL query clauses.
	 *
	 * @since 4.6.0
	 * @var array
	 */
	protected $sql_clauses = array(
		'select'  => '',
		'from'    => '',
		'where'   => array(),
		'groupby' => '',
		'orderby' => '',
		'limits'  => '',
	);

	/**
	 * Query vars set by the user.
	 *
	 * @since 4.6.0
	 * @var array
	 */
	public $query_vars;

	/**
	 * Default values for query vars.
	 *
	 * @since 4.6.0
	 * @var array
	 */
	public $query_var_defaults;

	/**
	 * List of networks located by the query.
	 *
	 * @since 4.6.0
	 * @var array
	 */
	public $networks;

	/**
	 * The amount of found networks for the current query.
	 *
	 * @since 4.6.0
	 * @var int
	 */
	public $found_networks = 0;

	/**
	 * The number of pages.
	 *
	 * @since 4.6.0
	 * @var int
	 */
	public $max_num_pages = 0;

	/**
	 * Constructor.
	 *
	 * Sets up the network query, based on the query vars passed.
	 *
	 * @since 4.6.0
	 *
	 * @param string|array $query {
	 *     Optional. Array or query string of network query parameters. Default empty.
	 *
	 *     @type int[]        $network__in          Array of network IDs to include. Default empty.
	 *     @type int[]        $network__not_in      Array of network IDs to exclude. Default empty.
	 *     @type bool         $count                Whether to return a network count (true) or array of network objects.
	 *                                              Default false.
	 *     @type string       $fields               Network fields to return. Accepts 'ids' (returns an array of network IDs)
	 *                                              or empty (returns an array of complete network objects). Default empty.
	 *     @type int          $number               Maximum number of networks to retrieve. Default empty (no limit).
	 *     @type int          $offset               Number of networks to offset the query. Used to build LIMIT clause.
	 *                                              Default 0.
	 *     @type bool         $no_found_rows        Whether to disable the `SQL_CALC_FOUND_ROWS` query. Default true.
	 *     @type string|array $orderby              Network status or array of statuses. Accepts 'id', 'domain', 'path',
	 *                                              'domain_length', 'path_length' and 'network__in'. Also accepts false,
	 *                                              an empty array, or 'none' to disable `ORDER BY` clause. Default 'id'.
	 *     @type string       $order                How to order retrieved networks. Accepts 'ASC', 'DESC'. Default 'ASC'.
	 *     @type string       $domain               Limit results to those affiliated with a given domain. Default empty.
	 *     @type string[]     $domain__in           Array of domains to include affiliated networks for. Default empty.
	 *     @type string[]     $domain__not_in       Array of domains to exclude affiliated networks for. Default empty.
	 *     @type string       $path                 Limit results to those affiliated with a given path. Default empty.
	 *     @type string[]     $path__in             Array of paths to include affiliated networks for. Default empty.
	 *     @type string[]     $path__not_in         Array of paths to exclude affiliated networks for. Default empty.
	 *     @type string       $search               Search term(s) to retrieve matching networks for. Default empty.
	 *     @type bool         $update_network_cache Whether to prime the cache for found networks. Default true.
	 * }
	 */
	public function __construct( $query = '' ) {
		$this->query_var_defaults = array(
			'network__in'          => '',
			'network__not_in'      => '',
			'count'                => false,
			'fields'               => '',
			'number'               => '',
			'offset'               => '',
			'no_found_rows'        => true,
			'orderby'              => 'id',
			'order'                => 'ASC',
			'domain'               => '',
			'domain__in'           => '',
			'domain__not_in'       => '',
			'path'                 => '',
			'path__in'             => '',
			'path__not_in'         => '',
			'search'               => '',
			'update_network_cache' => true,
		);

		if ( ! empty( $query ) ) {
			$this->query( $query );
		}
	}

	/**
	 * Parses arguments passed to the network query with default query parameters.
	 *
	 * @since 4.6.0
	 *
	 * @param string|array $query WP_Network_Query arguments. See WP_Network_Query::__construct() for accepted arguments.
	 */
	public function parse_query( $query = '' ) {
		if ( empty( $query ) ) {
			$query = $this->query_vars;
		}

		$this->query_vars = wp_parse_args( $query, $this->query_var_defaults );

		/**
		 * Fires after the network query vars have been parsed.
		 *
		 * @since 4.6.0
		 *
		 * @param WP_Network_Query $query The WP_Network_Query instance (passed by reference).
		 */
		do_action_ref_array( 'parse_network_query', array( &$this ) );
	}

	/**
	 * Sets up the WordPress query for retrieving networks.
	 *
	 * @since 4.6.0
	 *
	 * @param string|array $query Array or URL query string of parameters.
	 * @return array|int List of WP_Network objects, a list of network IDs when 'fields' is set to 'ids',
	 *                   or the number of networks when 'count' is passed as a query var.
	 */
	public function query( $query ) {
		$this->query_vars = wp_parse_args( $query );
		return $this->get_networks();
	}

	/**
	 * Gets a list of networks matching the query vars.
	 *
	 * @since 4.6.0
	 *
	 * @return array|int List of WP_Network objects, a list of network IDs when 'fields' is set to 'ids',
	 *                   or the number of networks when 'count' is passed as a query var.
	 */
	public function get_networks() {
		$this->parse_query();

		/**
		 * Fires before networks are retrieved.
		 *
		 * @since 4.6.0
		 *
		 * @param WP_Network_Query $query Current instance of WP_Network_Query (passed by reference).
		 */
		do_action_ref_array( 'pre_get_networks', array( &$this ) );

		$network_data = null;

		/**
		 * Filters the network data before the query takes place.
		 *
		 * Return a non-null value to bypass WordPress' default network queries.
		 *
		 * The expected return type from this filter depends on the value passed
		 * in the request query vars:
		 * - When `$this->query_vars['count']` is set, the filter should return
		 *   the network count as an integer.
		 * - When `'ids' === $this->query_vars['fields']`, the filter should return
		 *   an array of network IDs.
		 * - Otherwise the filter should return an array of WP_Network objects.
		 *
		 * Note that if the filter returns an array of network data, it will be assigned
		 * to the `networks` property of the current WP_Network_Query instance.
		 *
		 * Filtering functions that require pagination information are encouraged to set
		 * the `found_networks` and `max_num_pages` properties of the WP_Network_Query object,
		 * passed to the filter by reference. If WP_Network_Query does not perform a database
		 * query, it will not have enough information to generate these values itself.
		 *
		 * @since 5.2.0
		 * @since 5.6.0 The returned array of network data is assigned to the `networks` property
		 *              of the current WP_Network_Query instance.
		 *
		 * @param array|int|null   $network_data Return an array of network data to short-circuit WP's network query,
		 *                                       the network count as an integer if `$this->query_vars['count']` is set,
		 *                                       or null to allow WP to run its normal queries.
		 * @param WP_Network_Query $query        The WP_Network_Query instance, passed by reference.
		 */
		$network_data = apply_filters_ref_array( 'networks_pre_query', array( $network_data, &$this ) );

		if ( null !== $network_data ) {
			if ( is_array( $network_data ) && ! $this->query_vars['count'] ) {
				$this->networks = $network_data;
			}

			return $network_data;
		}

		// $args can include anything. Only use the args defined in the query_var_defaults to compute the key.
		$_args = wp_array_slice_assoc( $this->query_vars, array_keys( $this->query_var_defaults ) );

		// Ignore the $fields, $update_network_cache arguments as the queried result will be the same regardless.
		unset( $_args['fields'], $_args['update_network_cache'] );

		$key          = md5( serialize( $_args ) );
		$last_changed = wp_cache_get_last_changed( 'networks' );

		$cache_key   = "get_network_ids:$key:$last_changed";
		$cache_value = wp_cache_get( $cache_key, 'network-queries' );

		if ( false === $cache_value ) {
			$network_ids = $this->get_network_ids();
			if ( $network_ids ) {
				$this->set_found_networks();
			}

			$cache_value = array(
				'network_ids'    => $network_ids,
				'found_networks' => $this->found_networks,
			);
			wp_cache_add( $cache_key, $cache_value, 'network-queries' );
		} else {
			$network_ids          = $cache_value['network_ids'];
			$this->found_networks = $cache_value['found_networks'];
		}

		if ( $this->found_networks && $this->query_vars['number'] ) {
			$this->max_num_pages = (int) ceil( $this->found_networks / $this->query_vars['number'] );
		}

		// If querying for a count only, there's nothing more to do.
		if ( $this->query_vars['count'] ) {
			// $network_ids is actually a count in this case.
			return (int) $network_ids;
		}

		$network_ids = array_map( 'intval', $network_ids );

		if ( 'ids' === $this->query_vars['fields'] ) {
			$this->networks = $network_ids;
			return $this->networks;
		}

		if ( $this->query_vars['update_network_cache'] ) {
			_prime_network_caches( $network_ids );
		}

		// Fetch full network objects from the primed cache.
		$_networks = array();
		foreach ( $network_ids as $network_id ) {
			$_network = get_network( $network_id );
			if ( $_network ) {
				$_networks[] = $_network;
			}
		}

		/**
		 * Filters the network query results.
		 *
		 * @since 4.6.0
		 *
		 * @param WP_Network[]     $_networks An array of WP_Network objects.
		 * @param WP_Network_Query $query     Current instance of WP_Network_Query (passed by reference).
		 */
		$_networks = apply_filters_ref_array( 'the_networks', array( $_networks, &$this ) );

		// Convert to WP_Network instances.
		$this->networks = array_map( 'get_network', $_networks );

		return $this->networks;
	}

	/**
	 * Used internally to get a list of network IDs matching the query vars.
	 *
	 * @since 4.6.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return int|array A single count of network IDs if a count query. An array of network IDs if a full query.
	 */
	protected function get_network_ids() {
		global $wpdb;

		$order = $this->parse_order( $this->query_vars['order'] );

		// Disable ORDER BY with 'none', an empty array, or boolean false.
		if ( in_array( $this->query_vars['orderby'], array( 'none', array(), false ), true ) ) {
			$orderby = '';
		} elseif ( ! empty( $this->query_vars['orderby'] ) ) {
			$ordersby = is_array( $this->query_vars['orderby'] ) ?
				$this->query_vars['orderby'] :
				preg_split( '/[,\s]/', $this->query_vars['orderby'] );

			$orderby_array = array();
			foreach ( $ordersby as $_key => $_value ) {
				if ( ! $_value ) {
					continue;
				}

				if ( is_int( $_key ) ) {
					$_orderby = $_value;
					$_order   = $order;
				} else {
					$_orderby = $_key;
					$_order   = $_value;
				}

				$parsed = $this->parse_orderby( $_orderby );

				if ( ! $parsed ) {
					continue;
				}

				if ( 'network__in' === $_orderby ) {
					$orderby_array[] = $parsed;
					continue;
				}

				$orderby_array[] = $parsed . ' ' . $this->parse_order( $_order );
			}

			$orderby = implode( ', ', $orderby_array );
		} else {
			$orderby = "$wpdb->site.id $order";
		}

		$number = absint( $this->query_vars['number'] );
		$offset = absint( $this->query_vars['offset'] );
		$limits = '';

		if ( ! empty( $number ) ) {
			if ( $offset ) {
				$limits = 'LIMIT ' . $offset . ',' . $number;
			} else {
				$limits = 'LIMIT ' . $number;
			}
		}

		if ( $this->query_vars['count'] ) {
			$fields = 'COUNT(*)';
		} else {
			$fields = "$wpdb->site.id";
		}

		// Parse network IDs for an IN clause.
		if ( ! empty( $this->query_vars['network__in'] ) ) {
			$this->sql_clauses['where']['network__in'] = "$wpdb->site.id IN ( " . implode( ',', wp_parse_id_list( $this->query_vars['network__in'] ) ) . ' )';
		}

		// Parse network IDs for a NOT IN clause.
		if ( ! empty( $this->query_vars['network__not_in'] ) ) {
			$this->sql_clauses['where']['network__not_in'] = "$wpdb->site.id NOT IN ( " . implode( ',', wp_parse_id_list( $this->query_vars['network__not_in'] ) ) . ' )';
		}

		if ( ! empty( $this->query_vars['domain'] ) ) {
			$this->sql_clauses['where']['domain'] = $wpdb->prepare( "$wpdb->site.domain = %s", $this->query_vars['domain'] );
		}

		// Parse network domain for an IN clause.
		if ( is_array( $this->query_vars['domain__in'] ) ) {
			$this->sql_clauses['where']['domain__in'] = "$wpdb->site.domain IN ( '" . implode( "', '", $wpdb->_escape( $this->query_vars['domain__in'] ) ) . "' )";
		}

		// Parse network domain for a NOT IN clause.
		if ( is_array( $this->query_vars['domain__not_in'] ) ) {
			$this->sql_clauses['where']['domain__not_in'] = "$wpdb->site.domain NOT IN ( '" . implode( "', '", $wpdb->_escape( $this->query_vars['domain__not_in'] ) ) . "' )";
		}

		if ( ! empty( $this->query_vars['path'] ) ) {
			$this->sql_clauses['where']['path'] = $wpdb->prepare( "$wpdb->site.path = %s", $this->query_vars['path'] );
		}

		// Parse network path for an IN clause.
		if ( is_array( $this->query_vars['path__in'] ) ) {
			$this->sql_clauses['where']['path__in'] = "$wpdb->site.path IN ( '" . implode( "', '", $wpdb->_escape( $this->query_vars['path__in'] ) ) . "' )";
		}

		// Parse network path for a NOT IN clause.
		if ( is_array( $this->query_vars['path__not_in'] ) ) {
			$this->sql_clauses['where']['path__not_in'] = "$wpdb->site.path NOT IN ( '" . implode( "', '", $wpdb->_escape( $this->query_vars['path__not_in'] ) ) . "' )";
		}

		// Falsey search strings are ignored.
		if ( strlen( $this->query_vars['search'] ) ) {
			$this->sql_clauses['where']['search'] = $this->get_search_sql(
				$this->query_vars['search'],
				array( "$wpdb->site.domain", "$wpdb->site.path" )
			);
		}

		$join = '';

		$where = implode( ' AND ', $this->sql_clauses['where'] );

		$groupby = '';

		$pieces = array( 'fields', 'join', 'where', 'orderby', 'limits', 'groupby' );

		/**
		 * Filters the network query clauses.
		 *
		 * @since 4.6.0
		 *
		 * @param string[]         $clauses An associative array of network query clauses.
		 * @param WP_Network_Query $query   Current instance of WP_Network_Query (passed by reference).
		 */
		$clauses = apply_filters_ref_array( 'networks_clauses', array( compact( $pieces ), &$this ) );

		$fields  = isset( $clauses['fields'] ) ? $clauses['fields'] : '';
		$join    = isset( $clauses['join'] ) ? $clauses['join'] : '';
		$where   = isset( $clauses['where'] ) ? $clauses['where'] : '';
		$orderby = isset( $clauses['orderby'] ) ? $clauses['orderby'] : '';
		$limits  = isset( $clauses['limits'] ) ? $clauses['limits'] : '';
		$groupby = isset( $clauses['groupby'] ) ? $clauses['groupby'] : '';

		if ( $where ) {
			$where = 'WHERE ' . $where;
		}

		if ( $groupby ) {
			$groupby = 'GROUP BY ' . $groupby;
		}

		if ( $orderby ) {
			$orderby = "ORDER BY $orderby";
		}

		$found_rows = '';
		if ( ! $this->query_vars['no_found_rows'] ) {
			$found_rows = 'SQL_CALC_FOUND_ROWS';
		}

		$this->sql_clauses['select']  = "SELECT $found_rows $fields";
		$this->sql_clauses['from']    = "FROM $wpdb->site $join";
		$this->sql_clauses['groupby'] = $groupby;
		$this->sql_clauses['orderby'] = $orderby;
		$this->sql_clauses['limits']  = $limits;

		// Beginning of the string is on a new line to prevent leading whitespace. See https://core.trac.wordpress.org/ticket/56841.
		$this->request =
			"{$this->sql_clauses['select']}
			 {$this->sql_clauses['from']}
			 {$where}
			 {$this->sql_clauses['groupby']}
			 {$this->sql_clauses['orderby']}
			 {$this->sql_clauses['limits']}";

		if ( $this->query_vars['count'] ) {
			return (int) $wpdb->get_var( $this->request );
		}

		$network_ids = $wpdb->get_col( $this->request );

		return array_map( 'intval', $network_ids );
	}

	/**
	 * Populates found_networks and max_num_pages properties for the current query
	 * if the limit clause was used.
	 *
	 * @since 4.6.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 */
	private function set_found_networks() {
		global $wpdb;

		if ( $this->query_vars['number'] && ! $this->query_vars['no_found_rows'] ) {
			/**
			 * Filters the query used to retrieve found network count.
			 *
			 * @since 4.6.0
			 *
			 * @param string           $found_networks_query SQL query. Default 'SELECT FOUND_ROWS()'.
			 * @param WP_Network_Query $network_query        The `WP_Network_Query` instance.
			 */
			$found_networks_query = apply_filters( 'found_networks_query', 'SELECT FOUND_ROWS()', $this );

			$this->found_networks = (int) $wpdb->get_var( $found_networks_query );
		}
	}

	/**
	 * Used internally to generate an SQL string for searching across multiple columns.
	 *
	 * @since 4.6.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string   $search  Search string.
	 * @param string[] $columns Array of columns to search.
	 * @return string Search SQL.
	 */
	protected function get_search_sql( $search, $columns ) {
		global $wpdb;

		$like = '%' . $wpdb->esc_like( $search ) . '%';

		$searches = array();
		foreach ( $columns as $column ) {
			$searches[] = $wpdb->prepare( "$column LIKE %s", $like );
		}

		return '(' . implode( ' OR ', $searches ) . ')';
	}

	/**
	 * Parses and sanitizes 'orderby' keys passed to the network query.
	 *
	 * @since 4.6.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $orderby Alias for the field to order by.
	 * @return string|false Value to used in the ORDER clause. False otherwise.
	 */
	protected function parse_orderby( $orderby ) {
		global $wpdb;

		$allowed_keys = array(
			'id',
			'domain',
			'path',
		);

		$parsed = false;
		if ( 'network__in' === $orderby ) {
			$network__in = implode( ',', array_map( 'absint', $this->query_vars['network__in'] ) );
			$parsed      = "FIELD( {$wpdb->site}.id, $network__in )";
		} elseif ( 'domain_length' === $orderby || 'path_length' === $orderby ) {
			$field  = substr( $orderby, 0, -7 );
			$parsed = "CHAR_LENGTH($wpdb->site.$field)";
		} elseif ( in_array( $orderby, $allowed_keys, true ) ) {
			$parsed = "$wpdb->site.$orderby";
		}

		return $parsed;
	}

	/**
	 * Parses an 'order' query variable and cast it to 'ASC' or 'DESC' as necessary.
	 *
	 * @since 4.6.0
	 *
	 * @param string $order The 'order' query variable.
	 * @return string The sanitized 'order' query variable.
	 */
	protected function parse_order( $order ) {
		if ( ! is_string( $order ) || empty( $order ) ) {
			return 'ASC';
		}

		if ( 'ASC' === strtoupper( $order ) ) {
			return 'ASC';
		} else {
			return 'DESC';
		}
	}
}
