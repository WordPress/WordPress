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
	 * Metadata query container.
	 *
	 * @since 5.1.0
	 * @var WP_Meta_Query
	 */
	public $meta_query = false;

	/**
	 * Metadata query clauses.
	 *
	 * @since 5.1.0
	 * @var array
	 */
	protected $meta_query_clauses;

	/**
	 * Date query container.
	 *
	 * @since 4.6.0
	 * @var WP_Date_Query A date query instance.
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
	 * @since 5.1.0 Introduced the 'update_site_meta_cache', 'meta_query', 'meta_key',
	 *              'meta_value', 'meta_type' and 'meta_compare' parameters.
	 *
	 * @param string|array $query {
	 *     Optional. Array or query string of site query parameters. Default empty.
	 *
	 *     @type int[]        $site__in               Array of site IDs to include. Default empty.
	 *     @type int[]        $site__not_in           Array of site IDs to exclude. Default empty.
	 *     @type bool         $count                  Whether to return a site count (true) or array of site objects.
	 *                                                Default false.
	 *     @type array        $date_query             Date query clauses to limit sites by. See WP_Date_Query.
	 *                                                Default null.
	 *     @type string       $fields                 Site fields to return. Accepts 'ids' (returns an array of site IDs)
	 *                                                or empty (returns an array of complete site objects). Default empty.
	 *     @type int          $ID                     A site ID to only return that site. Default empty.
	 *     @type int          $number                 Maximum number of sites to retrieve. Default 100.
	 *     @type int          $offset                 Number of sites to offset the query. Used to build LIMIT clause.
	 *                                                Default 0.
	 *     @type bool         $no_found_rows          Whether to disable the `SQL_CALC_FOUND_ROWS` query. Default true.
	 *     @type string|array $orderby                Site status or array of statuses. Accepts 'id', 'domain', 'path',
	 *                                                'network_id', 'last_updated', 'registered', 'domain_length',
	 *                                                'path_length', 'site__in' and 'network__in'. Also accepts false,
	 *                                                an empty array, or 'none' to disable `ORDER BY` clause.
	 *                                                Default 'id'.
	 *     @type string       $order                  How to order retrieved sites. Accepts 'ASC', 'DESC'. Default 'ASC'.
	 *     @type int          $network_id             Limit results to those affiliated with a given network ID. If 0,
	 *                                                include all networks. Default 0.
	 *     @type int[]        $network__in            Array of network IDs to include affiliated sites for. Default empty.
	 *     @type int[]        $network__not_in        Array of network IDs to exclude affiliated sites for. Default empty.
	 *     @type string       $domain                 Limit results to those affiliated with a given domain. Default empty.
	 *     @type string[]     $domain__in             Array of domains to include affiliated sites for. Default empty.
	 *     @type string[]     $domain__not_in         Array of domains to exclude affiliated sites for. Default empty.
	 *     @type string       $path                   Limit results to those affiliated with a given path. Default empty.
	 *     @type string[]     $path__in               Array of paths to include affiliated sites for. Default empty.
	 *     @type string[]     $path__not_in           Array of paths to exclude affiliated sites for. Default empty.
	 *     @type int          $public                 Limit results to public sites. Accepts '1' or '0'. Default empty.
	 *     @type int          $archived               Limit results to archived sites. Accepts '1' or '0'. Default empty.
	 *     @type int          $mature                 Limit results to mature sites. Accepts '1' or '0'. Default empty.
	 *     @type int          $spam                   Limit results to spam sites. Accepts '1' or '0'. Default empty.
	 *     @type int          $deleted                Limit results to deleted sites. Accepts '1' or '0'. Default empty.
	 *     @type int          $lang_id                Limit results to a language ID. Default empty.
	 *     @type string[]     $lang__in               Array of language IDs to include affiliated sites for. Default empty.
	 *     @type string[]     $lang__not_in           Array of language IDs to exclude affiliated sites for. Default empty.
	 *     @type string       $search                 Search term(s) to retrieve matching sites for. Default empty.
	 *     @type array        $search_columns         Array of column names to be searched. Accepts 'domain' and 'path'.
	 *                                                Default empty array.
	 *     @type bool         $update_site_cache      Whether to prime the cache for found sites. Default true.
	 *     @type bool         $update_site_meta_cache Whether to prime the metadata cache for found sites. Default true.
	 *     @type array        $meta_query             Meta query clauses to limit retrieved sites by. See `WP_Meta_Query`.
	 *                                                Default empty.
	 *     @type string       $meta_key               Limit sites to those matching a specific metadata key.
	 *                                                Can be used in conjunction with `$meta_value`. Default empty.
	 *     @type string       $meta_value             Limit sites to those matching a specific metadata value.
	 *                                                Usually used in conjunction with `$meta_key`. Default empty.
	 *     @type string       $meta_type              Data type that the `$meta_value` column will be CAST to for
	 *                                                comparisons. Default empty.
	 *     @type string       $meta_compare           Comparison operator to test the `$meta_value`. Default empty.
	 * }
	 */
	public function __construct( $query = '' ) {
		$this->query_var_defaults = array(
			'fields'                 => '',
			'ID'                     => '',
			'site__in'               => '',
			'site__not_in'           => '',
			'number'                 => 100,
			'offset'                 => '',
			'no_found_rows'          => true,
			'orderby'                => 'id',
			'order'                  => 'ASC',
			'network_id'             => 0,
			'network__in'            => '',
			'network__not_in'        => '',
			'domain'                 => '',
			'domain__in'             => '',
			'domain__not_in'         => '',
			'path'                   => '',
			'path__in'               => '',
			'path__not_in'           => '',
			'public'                 => null,
			'archived'               => null,
			'mature'                 => null,
			'spam'                   => null,
			'deleted'                => null,
			'lang_id'                => null,
			'lang__in'               => '',
			'lang__not_in'           => '',
			'search'                 => '',
			'search_columns'         => array(),
			'count'                  => false,
			'date_query'             => null, // See WP_Date_Query.
			'update_site_cache'      => true,
			'update_site_meta_cache' => true,
			'meta_query'             => '',
			'meta_key'               => '',
			'meta_value'             => '',
			'meta_type'              => '',
			'meta_compare'           => '',
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
		 * @param WP_Site_Query $this The WP_Site_Query instance (passed by reference).
		 */
		do_action_ref_array( 'parse_site_query', array( &$this ) );
	}

	/**
	 * Sets up the WordPress query for retrieving sites.
	 *
	 * @since 4.6.0
	 *
	 * @param string|array $query Array or URL query string of parameters.
	 * @return array|int List of WP_Site objects, a list of site IDs when 'fields' is set to 'ids',
	 *                   or the number of sites when 'count' is passed as a query var.
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
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return array|int List of WP_Site objects, a list of site IDs when 'fields' is set to 'ids',
	 *                   or the number of sites when 'count' is passed as a query var.
	 */
	public function get_sites() {
		global $wpdb;

		$this->parse_query();

		// Parse meta query.
		$this->meta_query = new WP_Meta_Query();
		$this->meta_query->parse_query_vars( $this->query_vars );

		/**
		 * Fires before sites are retrieved.
		 *
		 * @since 4.6.0
		 *
		 * @param WP_Site_Query $this Current instance of WP_Site_Query (passed by reference).
		 */
		do_action_ref_array( 'pre_get_sites', array( &$this ) );

		// Reparse query vars, in case they were modified in a 'pre_get_sites' callback.
		$this->meta_query->parse_query_vars( $this->query_vars );
		if ( ! empty( $this->meta_query->queries ) ) {
			$this->meta_query_clauses = $this->meta_query->get_sql( 'blog', $wpdb->blogs, 'blog_id', $this );
		}

		$site_data = null;

		/**
		 * Filters the site data before the get_sites query takes place.
		 *
		 * Return a non-null value to bypass WordPress' default site queries.
		 *
		 * The expected return type from this filter depends on the value passed
		 * in the request query vars:
		 * - When `$this->query_vars['count']` is set, the filter should return
		 *   the site count as an integer.
		 * - When `'ids' === $this->query_vars['fields']`, the filter should return
		 *   an array of site IDs.
		 * - Otherwise the filter should return an array of WP_Site objects.
		 *
		 * Note that if the filter returns an array of site data, it will be assigned
		 * to the `sites` property of the current WP_Site_Query instance.
		 *
		 * Filtering functions that require pagination information are encouraged to set
		 * the `found_sites` and `max_num_pages` properties of the WP_Site_Query object,
		 * passed to the filter by reference. If WP_Site_Query does not perform a database
		 * query, it will not have enough information to generate these values itself.
		 *
		 * @since 5.2.0
		 * @since 5.6.0 The returned array of site data is assigned to the `sites` property
		 *              of the current WP_Site_Query instance.
		 *
		 * @param array|int|null $site_data Return an array of site data to short-circuit WP's site query,
		 *                                  the site count as an integer if `$this->query_vars['count']` is set,
		 *                                  or null to run the normal queries.
		 * @param WP_Site_Query  $query     The WP_Site_Query instance, passed by reference.
		 */
		$site_data = apply_filters_ref_array( 'sites_pre_query', array( $site_data, &$this ) );

		if ( null !== $site_data ) {
			if ( is_array( $site_data ) && ! $this->query_vars['count'] ) {
				$this->sites = $site_data;
			}

			return $site_data;
		}

		// $args can include anything. Only use the args defined in the query_var_defaults to compute the key.
		$_args = wp_array_slice_assoc( $this->query_vars, array_keys( $this->query_var_defaults ) );

		// Ignore the $fields argument as the queried result will be the same regardless.
		unset( $_args['fields'] );

		$key          = md5( serialize( $_args ) );
		$last_changed = wp_cache_get_last_changed( 'sites' );

		$cache_key   = "get_sites:$key:$last_changed";
		$cache_value = wp_cache_get( $cache_key, 'sites' );

		if ( false === $cache_value ) {
			$site_ids = $this->get_site_ids();
			if ( $site_ids ) {
				$this->set_found_sites();
			}

			$cache_value = array(
				'site_ids'    => $site_ids,
				'found_sites' => $this->found_sites,
			);
			wp_cache_add( $cache_key, $cache_value, 'sites' );
		} else {
			$site_ids          = $cache_value['site_ids'];
			$this->found_sites = $cache_value['found_sites'];
		}

		if ( $this->found_sites && $this->query_vars['number'] ) {
			$this->max_num_pages = ceil( $this->found_sites / $this->query_vars['number'] );
		}

		// If querying for a count only, there's nothing more to do.
		if ( $this->query_vars['count'] ) {
			// $site_ids is actually a count in this case.
			return (int) $site_ids;
		}

		$site_ids = array_map( 'intval', $site_ids );

		if ( 'ids' === $this->query_vars['fields'] ) {
			$this->sites = $site_ids;

			return $this->sites;
		}

		// Prime site network caches.
		if ( $this->query_vars['update_site_cache'] ) {
			_prime_site_caches( $site_ids, $this->query_vars['update_site_meta_cache'] );
		}

		// Fetch full site objects from the primed cache.
		$_sites = array();
		foreach ( $site_ids as $site_id ) {
			$_site = get_site( $site_id );
			if ( $_site ) {
				$_sites[] = $_site;
			}
		}

		/**
		 * Filters the site query results.
		 *
		 * @since 4.6.0
		 *
		 * @param WP_Site[]     $_sites An array of WP_Site objects.
		 * @param WP_Site_Query $query  Current instance of WP_Site_Query (passed by reference).
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
					$_order   = $order;
				} else {
					$_orderby = $_key;
					$_order   = $_value;
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
			$orderby = "{$wpdb->blogs}.blog_id $order";
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
			$fields = "{$wpdb->blogs}.blog_id";
		}

		// Parse site IDs for an IN clause.
		$site_id = absint( $this->query_vars['ID'] );
		if ( ! empty( $site_id ) ) {
			$this->sql_clauses['where']['ID'] = $wpdb->prepare( "{$wpdb->blogs}.blog_id = %d", $site_id );
		}

		// Parse site IDs for an IN clause.
		if ( ! empty( $this->query_vars['site__in'] ) ) {
			$this->sql_clauses['where']['site__in'] = "{$wpdb->blogs}.blog_id IN ( " . implode( ',', wp_parse_id_list( $this->query_vars['site__in'] ) ) . ' )';
		}

		// Parse site IDs for a NOT IN clause.
		if ( ! empty( $this->query_vars['site__not_in'] ) ) {
			$this->sql_clauses['where']['site__not_in'] = "{$wpdb->blogs}.blog_id NOT IN ( " . implode( ',', wp_parse_id_list( $this->query_vars['site__not_in'] ) ) . ' )';
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
			$archived                               = absint( $this->query_vars['archived'] );
			$this->sql_clauses['where']['archived'] = $wpdb->prepare( 'archived = %s ', absint( $archived ) );
		}

		if ( is_numeric( $this->query_vars['mature'] ) ) {
			$mature                               = absint( $this->query_vars['mature'] );
			$this->sql_clauses['where']['mature'] = $wpdb->prepare( 'mature = %d ', $mature );
		}

		if ( is_numeric( $this->query_vars['spam'] ) ) {
			$spam                               = absint( $this->query_vars['spam'] );
			$this->sql_clauses['where']['spam'] = $wpdb->prepare( 'spam = %d ', $spam );
		}

		if ( is_numeric( $this->query_vars['deleted'] ) ) {
			$deleted                               = absint( $this->query_vars['deleted'] );
			$this->sql_clauses['where']['deleted'] = $wpdb->prepare( 'deleted = %d ', $deleted );
		}

		if ( is_numeric( $this->query_vars['public'] ) ) {
			$public                               = absint( $this->query_vars['public'] );
			$this->sql_clauses['where']['public'] = $wpdb->prepare( 'public = %d ', $public );
		}

		if ( is_numeric( $this->query_vars['lang_id'] ) ) {
			$lang_id                               = absint( $this->query_vars['lang_id'] );
			$this->sql_clauses['where']['lang_id'] = $wpdb->prepare( 'lang_id = %d ', $lang_id );
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
			 * @param string[]      $search_columns Array of column names to be searched.
			 * @param string        $search         Text being searched.
			 * @param WP_Site_Query $query          The current WP_Site_Query instance.
			 */
			$search_columns = apply_filters( 'site_search_columns', $search_columns, $this->query_vars['search'], $this );

			$this->sql_clauses['where']['search'] = $this->get_search_sql( $this->query_vars['search'], $search_columns );
		}

		$date_query = $this->query_vars['date_query'];
		if ( ! empty( $date_query ) && is_array( $date_query ) ) {
			$this->date_query                         = new WP_Date_Query( $date_query, 'registered' );
			$this->sql_clauses['where']['date_query'] = preg_replace( '/^\s*AND\s*/', '', $this->date_query->get_sql() );
		}

		$join    = '';
		$groupby = '';

		if ( ! empty( $this->meta_query_clauses ) ) {
			$join .= $this->meta_query_clauses['join'];

			// Strip leading 'AND'.
			$this->sql_clauses['where']['meta_query'] = preg_replace( '/^\s*AND\s*/', '', $this->meta_query_clauses['where'] );

			if ( ! $this->query_vars['count'] ) {
				$groupby = "{$wpdb->blogs}.blog_id";
			}
		}

		$where = implode( ' AND ', $this->sql_clauses['where'] );

		$pieces = array( 'fields', 'join', 'where', 'orderby', 'limits', 'groupby' );

		/**
		 * Filters the site query clauses.
		 *
		 * @since 4.6.0
		 *
		 * @param string[]      $pieces An associative array of site query clauses.
		 * @param WP_Site_Query $query  Current instance of WP_Site_Query (passed by reference).
		 */
		$clauses = apply_filters_ref_array( 'sites_clauses', array( compact( $pieces ), &$this ) );

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
		$this->sql_clauses['from']    = "FROM $wpdb->blogs $join";
		$this->sql_clauses['groupby'] = $groupby;
		$this->sql_clauses['orderby'] = $orderby;
		$this->sql_clauses['limits']  = $limits;

		$this->request = "{$this->sql_clauses['select']} {$this->sql_clauses['from']} {$where} {$this->sql_clauses['groupby']} {$this->sql_clauses['orderby']} {$this->sql_clauses['limits']}";

		if ( $this->query_vars['count'] ) {
			return (int) $wpdb->get_var( $this->request );
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
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string   $string  Search string.
	 * @param string[] $columns Array of columns to search.
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
				$parsed   = "FIELD( {$wpdb->blogs}.blog_id, $site__in )";
				break;
			case 'network__in':
				$network__in = implode( ',', array_map( 'absint', $this->query_vars['network__in'] ) );
				$parsed      = "FIELD( {$wpdb->blogs}.site_id, $network__in )";
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
				$parsed = "{$wpdb->blogs}.blog_id";
				break;
		}

		if ( ! empty( $parsed ) || empty( $this->meta_query_clauses ) ) {
			return $parsed;
		}

		$meta_clauses = $this->meta_query->get_clauses();
		if ( empty( $meta_clauses ) ) {
			return $parsed;
		}

		$primary_meta_query = reset( $meta_clauses );
		if ( ! empty( $primary_meta_query['key'] ) && $primary_meta_query['key'] === $orderby ) {
			$orderby = 'meta_value';
		}

		switch ( $orderby ) {
			case 'meta_value':
				if ( ! empty( $primary_meta_query['type'] ) ) {
					$parsed = "CAST({$primary_meta_query['alias']}.meta_value AS {$primary_meta_query['cast']})";
				} else {
					$parsed = "{$primary_meta_query['alias']}.meta_value";
				}
				break;
			case 'meta_value_num':
				$parsed = "{$primary_meta_query['alias']}.meta_value+0";
				break;
			default:
				if ( isset( $meta_clauses[ $orderby ] ) ) {
					$meta_clause = $meta_clauses[ $orderby ];
					$parsed      = "CAST({$meta_clause['alias']}.meta_value AS {$meta_clause['cast']})";
				}
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
