<?php
/**
 * Site API: WP_Site_Query class
 *
 * @package WordPress
 * @subpackage Sites
 * @since 4.6.0
 */

/**
 * Core class used for querying sites.
 *
 * @since 4.6.0
 *
 * @see WP_Site_Query::__construct() for accepted arguments.
 */
class WP_Site_Query {

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
	 * Date query container.
	 *
	 * @since 4.6.0
	 * @var object WP_Date_Query
	 */
	public $date_query = false;

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
	 * List of sites located by the query.
	 *
	 * @since 4.6.0
	 * @var array
	 */
	public $sites;

	/**
	 * The amount of found sites for the current query.
	 *
	 * @since 4.6.0
	 * @var int
	 */
	public $found_sites = 0;

	/**
	 * The number of pages.
	 *
	 * @since 4.6.0
	 * @var int
	 */
	public $max_num_pages = 0;

	/**
	 * Sets up the site query, based on the query vars passed.
	 *
	 * @since 4.6.0
	 * @since 4.8.0 Introduced the 'lang_id', 'lang__in', and 'lang__not_in' parameters.
	 *
	 * @param string|array $query {
	 *     Optional. Array or query string of site query parameters. Default empty.
	 *
	 *     @type array        $site__in          Array of site IDs to include. Default empty.
	 *     @type array        $site__not_in      Array of site IDs to exclude. Default empty.
	 *     @type bool         $count             Whether to return a site count (true) or array of site objects.
	 *                                           Default false.
	 *     @type array        $date_query        Date query clauses to limit sites by. See WP_Date_Query.
	 *                                           Default null.
	 *     @type string       $fields            Site fields to return. Accepts 'ids' (returns an array of site IDs)
	 *                                           or empty (returns an array of complete site objects). Default empty.
	 *     @type int          $ID                A site ID to only return that site. Default empty.
	 *     @type int          $number            Maximum number of sites to retrieve. Default 100.
	 *     @type int          $offset            Number of sites to offset the query. Used to build LIMIT clause.
	 *                                           Default 0.
	 *     @type bool         $no_found_rows     Whether to disable the `SQL_CALC_FOUND_ROWS` query. Default true.
	 *     @type string|array $orderby           Site status or array of statuses. Accepts 'id', 'domain', 'path',
	 *                                           'network_id', 'last_updated', 'registered', 'domain_length',
	 *                                           'path_length', 'site__in' and 'network__in'. Also accepts false,
	 *                                           an empty array, or 'none' to disable `ORDER BY` clause.
	 *                                           Default 'id'.
	 *     @type string       $order             How to order retrieved sites. Accepts 'ASC', 'DESC'. Default 'ASC'.
	 *     @type int          $network_id        Limit results to those affiliated with a given network ID. If 0,
	 *                                           include all networks. Default 0.
	 *     @type array        $network__in       Array of network IDs to include affiliated sites for. Default empty.
	 *     @type array        $network__not_in   Array of network IDs to exclude affiliated sites for. Default empty.
	 *     @type string       $domain            Limit results to those affiliated with a given domain. Default empty.
	 *     @type array        $domain__in        Array of domains to include affiliated sites for. Default empty.
	 *     @type array        $domain__not_in    Array of domains to exclude affiliated sites for. Default empty.
	 *     @type string       $path              Limit results to those affiliated with a given path. Default empty.
	 *     @type array        $path__in          Array of paths to include affiliated sites for. Default empty.
	 *     @type array        $path__not_in      Array of paths to exclude affiliated sites for. Default empty.
	 *     @type int          $public            Limit results to public sites. Accepts '1' or '0'. Default empty.
	 *     @type int          $archived          Limit results to archived sites. Accepts '1' or '0'. Default empty.
	 *     @type int          $mature            Limit results to mature sites. Accepts '1' or '0'. Default empty.
	 *     @type int          $spam              Limit results to spam sites. Accepts '1' or '0'. Default empty.
	 *     @type int          $deleted           Limit results to deleted sites. Accepts '1' or '0'. Default empty.
	 *     @type int          $lang_id           Limit results to a language ID. Default empty.
	 *     @type array        $lang__in          Array of language IDs to include affiliated sites for. Default empty.
	 *     @type array        $lang__not_in      Array of language IDs to exclude affiliated sites for. Default empty.
	 *     @type string       $search            Search term(s) to retrieve matching sites for. Default empty.
	 *     @type array        $search_columns    Array of column names to be searched. Accepts 'domain' and 'path'.
	 *                                           Default empty array.
	 *     @type bool         $update_site_cache Whether to prime the cache for found sites. Default false.
	 * }
	 */
	public function __construct( $query = '' ) {
		$this->query_var_defaults = array(
			'fields'            => '',
			'ID'                => '',
			'site__in'          => '',
			'site__not_in'      => '',
			'number'            => 100,
			'offset'            => '',
			'no_found_rows'     => true,
			'orderby'           => 'id',
			'order'             => 'ASC',
			'network_id'        => 0,
			'network__in'       => '',
			'network__not_in'   => '',
			'domain'            => '',
			'domain__in'        => '',
			'domain__not_in'    => '',
			'path'              => '',
			'path__in'          => '',
			'path__not_in'      => '',
			'public'            => null,
			'archived'          => null,
			'mature'            => null,
			'spam'              => null,
			'deleted'           => null,
			'lang_id'           => null,
			'lang__in'          => '',
			'lang__not_in'      => '',
			'search'            => '',
			'search_columns'    => array(),
			'count'             => false,
			'date_query'        => null, // See WP_Date_Query
			'update_site_cache' => true,
		);

		if ( ! empty( $query ) ) {
			$this->query( $query );
		}
	}

	/**
	 * Parses arguments passed to the site query with default query parameters.
	 *
	 * @since 4.6.0
	 *
	 * @see WP_Site_Query::__construct()
	 *
	 * @param string|array $query Array or string of WP_Site_Query arguments. See WP_Site_Query::__construct().
	 */
	public function parse_query( $query = '' ) {
		if ( empty( $query ) ) {
			$query = $this->query_vars;
		}

		$this->query_vars = wp_parse_args( $query, $this->query_var_defaults );

		/**
		 * Fires after the site query vars have been parsed.
		 *
		 * @since 4.6.0
		 *
		 * @param WP_Site_Query &$this The WP_Site_Query instance (passed by reference).
		 */
		do_action_ref_array( 'parse_site_query', array( &$this ) );
	}

	/**
	 * Sets up the WordPress query for retrieving sites.
	 *
	 * @since 4.6.0
	 *
	 * @param string|array $query Array or URL query string of parameters.
	 * @return array|int List of sites, or number of sites when 'count' is passed as a query var.
	 */
	public function query( $query ) {
		$this->query_vars = wp_parse_args( $query );

		return $this->get_sites();
	}

	/**
	 * Retrieves a list of sites matching the query vars.
	 *
	 * @since 4.6.0
	 *
	 * @return array|int List of sites, or number of sites when 'count' is passed as a query var.
	 */
	public function get_sites() {
		$this->parse_query();

		/**
		 * Fires before sites are retrieved.
		 *
		 * @since 4.6.0
		 *
		 * @param WP_Site_Query &$this Current instance of WP_Site_Query, passed by reference.
		 */
		do_action_ref_array( 'pre_get_sites', array( &$this ) );

		// $args can include anything. Only use the args defined in the query_var_defaults to compute the key.
		$_args = wp_array_slice_assoc( $this->query_vars, array_keys( $this->query_var_defaults ) );

		// Ignore the $fields argument as the queried result will be the same regardless.
		unset( $_args['fields'] );

		$key = md5( serialize( $_args ) );
		$last_changed = wp_cache_get_last_changed( 'sites' );

		$cache_key = "get_sites:$key:$last_changed";
		$cache_value = wp_cache_get( $cache_key, 'sites' );

		if ( false === $cache_value ) {
			$site_ids = $this->get_site_ids();
			if ( $site_ids ) {
				$this->set_found_sites();
			}

			$cache_value = array(
				'site_ids' => $site_ids,
				'found_sites' => $this->found_sites,
			);
			wp_cache_add( $cache_key, $cache_value, 'sites' );
		} else {
			$site_ids = $cache_value['site_ids'];
			$this->found_sites = $cache_value['found_sites'];
		}

		if ( $this->found_sites && $this->query_vars['number'] ) {
			$this->max_num_pages = ceil( $this->found_sites / $this->query_vars['number'] );
		}

		// If querying for a count only, there's nothing more to do.
		if ( $this->query_vars['count'] ) {
			// $site_ids is actually a count in this case.
			return intval( $site_ids );
		}

		$site_ids = array_map( 'intval', $site_ids );

		if ( 'ids' == $this->query_vars['fields'] ) {
			$this->sites = $site_ids;

			return $this->sites;
		}

		// Prime site network caches.
		if ( $this->query_vars['update_site_cache'] ) {
			_prime_site_caches( $site_ids );
		}

		// Fetch full site objects from the primed cache.
		$_sites = array();
		foreach ( $site_ids as $site_id ) {
			if ( $_site = get_site( $site_id ) ) {
				$_sites[] = $_site;
			}
		}

		/**
		 * Filters the site query results.
		 *
		 * @since 4.6.0
		 *
		 * @param array         $results An array of sites.
		 * @param WP_Site_Query &$this   Current instance of WP_Site_Query, passed by reference.
		 */
		$_sites = apply_filters_ref_array( 'the_sites', array( $_sites, &$this ) );

		// Convert to WP_Site instances.
		$this->sites = array_map( 'get_site', $_sites );

		return $this->sites;
	}

	/**
	 * Used internally to get a list of site IDs matching the query vars.
	 *
	 * @since 4.6.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return int|array A single count of site IDs if a count query. An array of site IDs if a full query.
	 */
	protected function get_site_ids() {
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
					$_order = $order;
				} else {
					$_orderby = $_key;
					$_order = $_value;
				}

				$parsed = $this->parse_orderby( $_orderby );

				if ( ! $parsed ) {
					continue;
				}

				if ( 'site__in' === $_orderby || 'network__in' === $_orderby ) {
					$orderby_array[] = $parsed;
					continue;
				}

				$orderby_array[] = $parsed . ' ' . $this->parse_order( $_order );
			}

			$orderby = implode( ', ', $orderby_array );
		} else {
			$orderby = "blog_id $order";
		}

		$number = absint( $this->query_vars['number'] );
		$offset = absint( $this->query_vars['offset'] );

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
			$fields = 'blog_id';
		}

		// Parse site IDs for an IN clause.
		$site_id = absint( $this->query_vars['ID'] );
		if ( ! empty( $site_id ) ) {
			$this->sql_clauses['where']['ID'] = $wpdb->prepare( 'blog_id = %d', $site_id );
		}

		// Parse site IDs for an IN clause.
		if ( ! empty( $this->query_vars['site__in'] ) ) {
			$this->sql_clauses['where']['site__in'] = "blog_id IN ( " . implode( ',', wp_parse_id_list( $this->query_vars['site__in'] ) ) . ' )';
		}

		// Parse site IDs for a NOT IN clause.
		if ( ! empty( $this->query_vars['site__not_in'] ) ) {
			$this->sql_clauses['where']['site__not_in'] = "blog_id NOT IN ( " . implode( ',', wp_parse_id_list( $this->query_vars['site__not_in'] ) ) . ' )';
		}

		$network_id = absint( $this->query_vars['network_id'] );

		if ( ! empty( $network_id ) ) {
			$this->sql_clauses['where']['network_id'] = $wpdb->prepare( 'site_id = %d', $network_id );
		}

		// Parse site network IDs for an IN clause.
		if ( ! empty( $this->query_vars['network__in'] ) ) {
			$this->sql_clauses['where']['network__in'] = 'site_id IN ( ' . implode( ',', wp_parse_id_list( $this->query_vars['network__in'] ) ) . ' )';
		}

		// Parse site network IDs for a NOT IN clause.
		if ( ! empty( $this->query_vars['network__not_in'] ) ) {
			$this->sql_clauses['where']['network__not_in'] = 'site_id NOT IN ( ' . implode( ',', wp_parse_id_list( $this->query_vars['network__not_in'] ) ) . ' )';
		}

		if ( ! empty( $this->query_vars['domain'] ) ) {
			$this->sql_clauses['where']['domain'] = $wpdb->prepare( 'domain = %s', $this->query_vars['domain'] );
		}

		// Parse site domain for an IN clause.
		if ( is_array( $this->query_vars['domain__in'] ) ) {
			$this->sql_clauses['where']['domain__in'] = "domain IN ( '" . implode( "', '", $wpdb->_escape( $this->query_vars['domain__in'] ) ) . "' )";
		}

		// Parse site domain for a NOT IN clause.
		if ( is_array( $this->query_vars['domain__not_in'] ) ) {
			$this->sql_clauses['where']['domain__not_in'] = "domain NOT IN ( '" . implode( "', '", $wpdb->_escape( $this->query_vars['domain__not_in'] ) ) . "' )";
		}

		if ( ! empty( $this->query_vars['path'] ) ) {
			$this->sql_clauses['where']['path'] = $wpdb->prepare( 'path = %s', $this->query_vars['path'] );
		}

		// Parse site path for an IN clause.
		if ( is_array( $this->query_vars['path__in'] ) ) {
			$this->sql_clauses['where']['path__in'] = "path IN ( '" . implode( "', '", $wpdb->_escape( $this->query_vars['path__in'] ) ) . "' )";
		}

		// Parse site path for a NOT IN clause.
		if ( is_array( $this->query_vars['path__not_in'] ) ) {
			$this->sql_clauses['where']['path__not_in'] = "path NOT IN ( '" . implode( "', '", $wpdb->_escape( $this->query_vars['path__not_in'] ) ) . "' )";
		}

		if ( is_numeric( $this->query_vars['archived'] ) ) {
			$archived = absint( $this->query_vars['archived'] );
			$this->sql_clauses['where']['archived'] = $wpdb->prepare( "archived = %d ", $archived );
		}

		if ( is_numeric( $this->query_vars['mature'] ) ) {
			$mature = absint( $this->query_vars['mature'] );
			$this->sql_clauses['where']['mature'] = $wpdb->prepare( "mature = %d ", $mature );
		}

		if ( is_numeric( $this->query_vars['spam'] ) ) {
			$spam = absint( $this->query_vars['spam'] );
			$this->sql_clauses['where']['spam'] = $wpdb->prepare( "spam = %d ", $spam );
		}

		if ( is_numeric( $this->query_vars['deleted'] ) ) {
			$deleted = absint( $this->query_vars['deleted'] );
			$this->sql_clauses['where']['deleted'] = $wpdb->prepare( "deleted = %d ", $deleted );
		}

		if ( is_numeric( $this->query_vars['public'] ) ) {
			$public = absint( $this->query_vars['public'] );
			$this->sql_clauses['where']['public'] = $wpdb->prepare( "public = %d ", $public );
		}

		if ( is_numeric( $this->query_vars['lang_id'] ) ) {
			$lang_id = absint( $this->query_vars['lang_id'] );
			$this->sql_clauses['where']['lang_id'] = $wpdb->prepare( "lang_id = %d ", $lang_id );
		}

		// Parse site language IDs for an IN clause.
		if ( ! empty( $this->query_vars['lang__in'] ) ) {
			$this->sql_clauses['where']['lang__in'] = 'lang_id IN ( ' . implode( ',', wp_parse_id_list( $this->query_vars['lang__in'] ) ) . ' )';
		}

		// Parse site language IDs for a NOT IN clause.
		if ( ! empty( $this->query_vars['lang__not_in'] ) ) {
			$this->sql_clauses['where']['lang__not_in'] = 'lang_id NOT IN ( ' . implode( ',', wp_parse_id_list( $this->query_vars['lang__not_in'] ) ) . ' )';
		}

		// Falsey search strings are ignored.
		if ( strlen( $this->query_vars['search'] ) ) {
			$search_columns = array();

			if ( $this->query_vars['search_columns'] ) {
				$search_columns = array_intersect( $this->query_vars['search_columns'], array( 'domain', 'path' ) );
			}

			if ( ! $search_columns ) {
				$search_columns = array( 'domain', 'path' );
			}

			/**
			 * Filters the columns to search in a WP_Site_Query search.
			 *
			 * The default columns include 'domain' and 'path.
			 *
			 * @since 4.6.0
			 *
			 * @param array         $search_columns Array of column names to be searched.
			 * @param string        $search         Text being searched.
			 * @param WP_Site_Query $this           The current WP_Site_Query instance.
			 */
			$search_columns = apply_filters( 'site_search_columns', $search_columns, $this->query_vars['search'], $this );

			$this->sql_clauses['where']['search'] = $this->get_search_sql( $this->query_vars['search'], $search_columns );
		}

		$date_query = $this->query_vars['date_query'];
		if ( ! empty( $date_query ) && is_array( $date_query ) ) {
			$this->date_query = new WP_Date_Query( $date_query, 'registered' );
			$this->sql_clauses['where']['date_query'] = preg_replace( '/^\s*AND\s*/', '', $this->date_query->get_sql() );
		}

		$join = '';

		$where = implode( ' AND ', $this->sql_clauses['where'] );

		$pieces = array( 'fields', 'join', 'where', 'orderby', 'limits', 'groupby' );

		/**
		 * Filters the site query clauses.
		 *
		 * @since 4.6.0
		 *
		 * @param array $pieces A compacted array of site query clauses.
		 * @param WP_Site_Query &$this Current instance of WP_Site_Query, passed by reference.
		 */
		$clauses = apply_filters_ref_array( 'sites_clauses', array( compact( $pieces ), &$this ) );

		$fields = isset( $clauses['fields'] ) ? $clauses['fields'] : '';
		$join = isset( $clauses['join'] ) ? $clauses['join'] : '';
		$where = isset( $clauses['where'] ) ? $clauses['where'] : '';
		$orderby = isset( $clauses['orderby'] ) ? $clauses['orderby'] : '';
		$limits = isset( $clauses['limits'] ) ? $clauses['limits'] : '';
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
		$this->sql_clauses['from']    = "FROM $wpdb->blogs $join";
		$this->sql_clauses['groupby'] = $groupby;
		$this->sql_clauses['orderby'] = $orderby;
		$this->sql_clauses['limits']  = $limits;

		$this->request = "{$this->sql_clauses['select']} {$this->sql_clauses['from']} {$where} {$this->sql_clauses['groupby']} {$this->sql_clauses['orderby']} {$this->sql_clauses['limits']}";

		if ( $this->query_vars['count'] ) {
			return intval( $wpdb->get_var( $this->request ) );
		}

		$site_ids = $wpdb->get_col( $this->request );

		return array_map( 'intval', $site_ids );
	}

	/**
	 * Populates found_sites and max_num_pages properties for the current query
	 * if the limit clause was used.
	 *
	 * @since 4.6.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 */
	private function set_found_sites() {
		global $wpdb;

		if ( $this->query_vars['number'] && ! $this->query_vars['no_found_rows'] ) {
			/**
			 * Filters the query used to retrieve found site count.
			 *
			 * @since 4.6.0
			 *
			 * @param string        $found_sites_query SQL query. Default 'SELECT FOUND_ROWS()'.
			 * @param WP_Site_Query $site_query        The `WP_Site_Query` instance.
			 */
			$found_sites_query = apply_filters( 'found_sites_query', 'SELECT FOUND_ROWS()', $this );

			$this->found_sites = (int) $wpdb->get_var( $found_sites_query );
		}
	}

	/**
	 * Used internally to generate an SQL string for searching across multiple columns.
	 *
	 * @since 4.6.0
	 *
	 * @global wpdb  $wpdb WordPress database abstraction object.
	 *
	 * @param string $string  Search string.
	 * @param array  $columns Columns to search.
	 * @return string Search SQL.
	 */
	protected function get_search_sql( $string, $columns ) {
		global $wpdb;

		if ( false !== strpos( $string, '*' ) ) {
			$like = '%' . implode( '%', array_map( array( $wpdb, 'esc_like' ), explode( '*', $string ) ) ) . '%';
		} else {
			$like = '%' . $wpdb->esc_like( $string ) . '%';
		}

		$searches = array();
		foreach ( $columns as $column ) {
			$searches[] = $wpdb->prepare( "$column LIKE %s", $like );
		}

		return '(' . implode( ' OR ', $searches ) . ')';
	}

	/**
	 * Parses and sanitizes 'orderby' keys passed to the site query.
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

		$parsed = false;

		switch ( $orderby ) {
			case 'site__in':
				$site__in = implode( ',', array_map( 'absint', $this->query_vars['site__in'] ) );
				$parsed = "FIELD( {$wpdb->blogs}.blog_id, $site__in )";
				break;
			case 'network__in':
				$network__in = implode( ',', array_map( 'absint', $this->query_vars['network__in'] ) );
				$parsed = "FIELD( {$wpdb->blogs}.site_id, $network__in )";
				break;
			case 'domain':
			case 'last_updated':
			case 'path':
			case 'registered':
				$parsed = $orderby;
				break;
			case 'network_id':
				$parsed = 'site_id';
				break;
			case 'domain_length':
				$parsed = 'CHAR_LENGTH(domain)';
				break;
			case 'path_length':
				$parsed = 'CHAR_LENGTH(path)';
				break;
			case 'id':
				$parsed = 'blog_id';
				break;
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
