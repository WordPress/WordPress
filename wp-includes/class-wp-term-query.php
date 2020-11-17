<?php

/**
 * Taxonomy API: WP_Term_Query class.
 *
 * @package WordPress
 * @subpackage Taxonomy
 * @since 4.6.0
 */

/**
 * Class used for querying terms.
 *
 * @since 4.6.0
 *
 * @see WP_Term_Query::__construct() for accepted arguments.
 */
class WP_Term_Query {

	/**
	 * SQL string used to perform database query.
	 *
	 * @since 4.6.0
	 * @var string
	 */
	public $request;

	/**
	 * Metadata query container.
	 *
	 * @since 4.6.0
	 * @var WP_Meta_Query A meta query instance.
	 */
	public $meta_query = false;

	/**
	 * Metadata query clauses.
	 *
	 * @since 4.6.0
	 * @var array
	 */
	protected $meta_query_clauses;

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
	 * List of terms located by the query.
	 *
	 * @since 4.6.0
	 * @var array
	 */
	public $terms;

	/**
	 * Constructor.
	 *
	 * Sets up the term query, based on the query vars passed.
	 *
	 * @since 4.6.0
	 * @since 4.6.0 Introduced 'term_taxonomy_id' parameter.
	 * @since 4.7.0 Introduced 'object_ids' parameter.
	 * @since 4.9.0 Added 'slug__in' support for 'orderby'.
	 *
	 * @param string|array $query {
	 *     Optional. Array or query string of term query parameters. Default empty.
	 *
	 *     @type string|array $taxonomy               Taxonomy name, or array of taxonomies, to which results should
	 *                                                be limited.
	 *     @type int|array    $object_ids             Optional. Object ID, or array of object IDs. Results will be
	 *                                                limited to terms associated with these objects.
	 *     @type string       $orderby                Field(s) to order terms by. Accepts:
	 *                                                - term fields ('name', 'slug', 'term_group', 'term_id', 'id',
	 *                                                  'description', 'parent', 'term_order'). Unless `$object_ids`
	 *                                                  is not empty, 'term_order' is treated the same as 'term_id'.
	 *                                                - 'count' to use the number of objects associated with the term.
	 *                                                - 'include' to match the 'order' of the $include param.
	 *                                                - 'slug__in' to match the 'order' of the $slug param.
	 *                                                - 'meta_value', 'meta_value_num'.
	 *                                                - the value of `$meta_key`.
	 *                                                - the array keys of `$meta_query`.
	 *                                                - 'none' to omit the ORDER BY clause.
	 *                                                Defaults to 'name'.
	 *     @type string       $order                  Whether to order terms in ascending or descending order.
	 *                                                Accepts 'ASC' (ascending) or 'DESC' (descending).
	 *                                                Default 'ASC'.
	 *     @type bool|int     $hide_empty             Whether to hide terms not assigned to any posts. Accepts
	 *                                                1|true or 0|false. Default 1|true.
	 *     @type array|string $include                Array or comma/space-separated string of term IDs to include.
	 *                                                Default empty array.
	 *     @type array|string $exclude                Array or comma/space-separated string of term IDs to exclude.
	 *                                                If $include is non-empty, $exclude is ignored.
	 *                                                Default empty array.
	 *     @type array|string $exclude_tree           Array or comma/space-separated string of term IDs to exclude
	 *                                                along with all of their descendant terms. If $include is
	 *                                                non-empty, $exclude_tree is ignored. Default empty array.
	 *     @type int|string   $number                 Maximum number of terms to return. Accepts ''|0 (all) or any
	 *                                                positive number. Default ''|0 (all). Note that $number may
	 *                                                not return accurate results when coupled with $object_ids.
	 *                                                See #41796 for details.
	 *     @type int          $offset                 The number by which to offset the terms query. Default empty.
	 *     @type string       $fields                 Term fields to query for. Accepts:
	 *                                                - 'all' Returns an array of complete term objects (`WP_Term[]`).
	 *                                                - 'all_with_object_id' Returns an array of term objects
	 *                                                  with the 'object_id' param (`WP_Term[]`). Works only
	 *                                                  when the `$object_ids` parameter is populated.
	 *                                                - 'ids' Returns an array of term IDs (`int[]`).
	 *                                                - 'tt_ids' Returns an array of term taxonomy IDs (`int[]`).
	 *                                                - 'names' Returns an array of term names (`string[]`).
	 *                                                - 'slugs' Returns an array of term slugs (`string[]`).
	 *                                                - 'count' Returns the number of matching terms (`int`).
	 *                                                - 'id=>parent' Returns an associative array of parent term IDs,
	 *                                                   keyed by term ID (`int[]`).
	 *                                                - 'id=>name' Returns an associative array of term names,
	 *                                                   keyed by term ID (`string[]`).
	 *                                                - 'id=>slug' Returns an associative array of term slugs,
	 *                                                   keyed by term ID (`string[]`).
	 *                                                Default 'all'.
	 *     @type bool         $count                  Whether to return a term count. If true, will take precedence
	 *                                                over `$fields`. Default false.
	 *     @type string|array $name                   Optional. Name or array of names to return term(s) for.
	 *                                                Default empty.
	 *     @type string|array $slug                   Optional. Slug or array of slugs to return term(s) for.
	 *                                                Default empty.
	 *     @type int|array    $term_taxonomy_id       Optional. Term taxonomy ID, or array of term taxonomy IDs,
	 *                                                to match when querying terms.
	 *     @type bool         $hierarchical           Whether to include terms that have non-empty descendants
	 *                                                (even if $hide_empty is set to true). Default true.
	 *     @type string       $search                 Search criteria to match terms. Will be SQL-formatted with
	 *                                                wildcards before and after. Default empty.
	 *     @type string       $name__like             Retrieve terms with criteria by which a term is LIKE
	 *                                                `$name__like`. Default empty.
	 *     @type string       $description__like      Retrieve terms where the description is LIKE
	 *                                                `$description__like`. Default empty.
	 *     @type bool         $pad_counts             Whether to pad the quantity of a term's children in the
	 *                                                quantity of each term's "count" object variable.
	 *                                                Default false.
	 *     @type string       $get                    Whether to return terms regardless of ancestry or whether the
	 *                                                terms are empty. Accepts 'all' or empty (disabled).
	 *                                                Default empty.
	 *     @type int          $child_of               Term ID to retrieve child terms of. If multiple taxonomies
	 *                                                are passed, $child_of is ignored. Default 0.
	 *     @type int|string   $parent                 Parent term ID to retrieve direct-child terms of.
	 *                                                Default empty.
	 *     @type bool         $childless              True to limit results to terms that have no children.
	 *                                                This parameter has no effect on non-hierarchical taxonomies.
	 *                                                Default false.
	 *     @type string       $cache_domain           Unique cache key to be produced when this query is stored in
	 *                                                an object cache. Default is 'core'.
	 *     @type bool         $update_term_meta_cache Whether to prime meta caches for matched terms. Default true.
	 *     @type array        $meta_query             Optional. Meta query clauses to limit retrieved terms by.
	 *                                                See `WP_Meta_Query`. Default empty.
	 *     @type string       $meta_key               Limit terms to those matching a specific metadata key.
	 *                                                Can be used in conjunction with `$meta_value`. Default empty.
	 *     @type string       $meta_value             Limit terms to those matching a specific metadata value.
	 *                                                Usually used in conjunction with `$meta_key`. Default empty.
	 *     @type string       $meta_type              MySQL data type that the `$meta_value` will be CAST to for
	 *                                                comparisons. Default empty.
	 *     @type string       $meta_compare           Comparison operator to test the 'meta_value'. Default empty.
	 * }
	 */
	public function __construct( $query = '' ) {
		$this->query_var_defaults = array(
			'taxonomy'               => null,
			'object_ids'             => null,
			'orderby'                => 'name',
			'order'                  => 'ASC',
			'hide_empty'             => true,
			'include'                => array(),
			'exclude'                => array(),
			'exclude_tree'           => array(),
			'number'                 => '',
			'offset'                 => '',
			'fields'                 => 'all',
			'count'                  => false,
			'name'                   => '',
			'slug'                   => '',
			'term_taxonomy_id'       => '',
			'hierarchical'           => true,
			'search'                 => '',
			'name__like'             => '',
			'description__like'      => '',
			'pad_counts'             => false,
			'get'                    => '',
			'child_of'               => 0,
			'parent'                 => '',
			'childless'              => false,
			'cache_domain'           => 'core',
			'update_term_meta_cache' => true,
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
	 * Parse arguments passed to the term query with default query parameters.
	 *
	 * @since 4.6.0
	 *
	 * @param string|array $query WP_Term_Query arguments. See WP_Term_Query::__construct()
	 */
	public function parse_query( $query = '' ) {
		if ( empty( $query ) ) {
			$query = $this->query_vars;
		}

		$taxonomies = isset( $query['taxonomy'] ) ? (array) $query['taxonomy'] : null;

		/**
		 * Filters the terms query default arguments.
		 *
		 * Use {@see 'get_terms_args'} to filter the passed arguments.
		 *
		 * @since 4.4.0
		 *
		 * @param array    $defaults   An array of default get_terms() arguments.
		 * @param string[] $taxonomies An array of taxonomy names.
		 */
		$this->query_var_defaults = apply_filters( 'get_terms_defaults', $this->query_var_defaults, $taxonomies );

		$query = wp_parse_args( $query, $this->query_var_defaults );

		$query['number'] = absint( $query['number'] );
		$query['offset'] = absint( $query['offset'] );

		// 'parent' overrides 'child_of'.
		if ( 0 < (int) $query['parent'] ) {
			$query['child_of'] = false;
		}

		if ( 'all' === $query['get'] ) {
			$query['childless']    = false;
			$query['child_of']     = 0;
			$query['hide_empty']   = 0;
			$query['hierarchical'] = false;
			$query['pad_counts']   = false;
		}

		$query['taxonomy'] = $taxonomies;

		$this->query_vars = $query;

		/**
		 * Fires after term query vars have been parsed.
		 *
		 * @since 4.6.0
		 *
		 * @param WP_Term_Query $this Current instance of WP_Term_Query.
		 */
		do_action( 'parse_term_query', $this );
	}

	/**
	 * Sets up the query for retrieving terms.
	 *
	 * @since 4.6.0
	 *
	 * @param string|array $query Array or URL query string of parameters.
	 * @return array|int List of terms, or number of terms when 'count' is passed as a query var.
	 */
	public function query( $query ) {
		$this->query_vars = wp_parse_args( $query );
		return $this->get_terms();
	}

	/**
	 * Get terms, based on query_vars.
	 *
	 * @since 4.6.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return array List of terms.
	 */
	public function get_terms() {
		global $wpdb;

		$this->parse_query( $this->query_vars );
		$args = &$this->query_vars;

		// Set up meta_query so it's available to 'pre_get_terms'.
		$this->meta_query = new WP_Meta_Query();
		$this->meta_query->parse_query_vars( $args );

		/**
		 * Fires before terms are retrieved.
		 *
		 * @since 4.6.0
		 *
		 * @param WP_Term_Query $this Current instance of WP_Term_Query (passed by reference).
		 */
		do_action_ref_array( 'pre_get_terms', array( &$this ) );

		$taxonomies = (array) $args['taxonomy'];

		// Save queries by not crawling the tree in the case of multiple taxes or a flat tax.
		$has_hierarchical_tax = false;
		if ( $taxonomies ) {
			foreach ( $taxonomies as $_tax ) {
				if ( is_taxonomy_hierarchical( $_tax ) ) {
					$has_hierarchical_tax = true;
				}
			}
		} else {
			// When no taxonomies are provided, assume we have to descend the tree.
			$has_hierarchical_tax = true;
		}

		if ( ! $has_hierarchical_tax ) {
			$args['hierarchical'] = false;
			$args['pad_counts']   = false;
		}

		// 'parent' overrides 'child_of'.
		if ( 0 < (int) $args['parent'] ) {
			$args['child_of'] = false;
		}

		if ( 'all' === $args['get'] ) {
			$args['childless']    = false;
			$args['child_of']     = 0;
			$args['hide_empty']   = 0;
			$args['hierarchical'] = false;
			$args['pad_counts']   = false;
		}

		/**
		 * Filters the terms query arguments.
		 *
		 * @since 3.1.0
		 *
		 * @param array    $args       An array of get_terms() arguments.
		 * @param string[] $taxonomies An array of taxonomy names.
		 */
		$args = apply_filters( 'get_terms_args', $args, $taxonomies );

		// Avoid the query if the queried parent/child_of term has no descendants.
		$child_of = $args['child_of'];
		$parent   = $args['parent'];

		if ( $child_of ) {
			$_parent = $child_of;
		} elseif ( $parent ) {
			$_parent = $parent;
		} else {
			$_parent = false;
		}

		if ( $_parent ) {
			$in_hierarchy = false;
			foreach ( $taxonomies as $_tax ) {
				$hierarchy = _get_term_hierarchy( $_tax );

				if ( isset( $hierarchy[ $_parent ] ) ) {
					$in_hierarchy = true;
				}
			}

			if ( ! $in_hierarchy ) {
				if ( 'count' === $args['fields'] ) {
					return 0;
				} else {
					$this->terms = array();
					return $this->terms;
				}
			}
		}

		// 'term_order' is a legal sort order only when joining the relationship table.
		$_orderby = $this->query_vars['orderby'];
		if ( 'term_order' === $_orderby && empty( $this->query_vars['object_ids'] ) ) {
			$_orderby = 'term_id';
		}

		$orderby = $this->parse_orderby( $_orderby );

		if ( $orderby ) {
			$orderby = "ORDER BY $orderby";
		}

		$order = $this->parse_order( $this->query_vars['order'] );

		if ( $taxonomies ) {
			$this->sql_clauses['where']['taxonomy'] = "tt.taxonomy IN ('" . implode( "', '", array_map( 'esc_sql', $taxonomies ) ) . "')";
		}

		$exclude      = $args['exclude'];
		$exclude_tree = $args['exclude_tree'];
		$include      = $args['include'];

		$inclusions = '';
		if ( ! empty( $include ) ) {
			$exclude      = '';
			$exclude_tree = '';
			$inclusions   = implode( ',', wp_parse_id_list( $include ) );
		}

		if ( ! empty( $inclusions ) ) {
			$this->sql_clauses['where']['inclusions'] = 't.term_id IN ( ' . $inclusions . ' )';
		}

		$exclusions = array();
		if ( ! empty( $exclude_tree ) ) {
			$exclude_tree      = wp_parse_id_list( $exclude_tree );
			$excluded_children = $exclude_tree;
			foreach ( $exclude_tree as $extrunk ) {
				$excluded_children = array_merge(
					$excluded_children,
					(array) get_terms(
						array(
							'taxonomy'   => reset( $taxonomies ),
							'child_of'   => (int) $extrunk,
							'fields'     => 'ids',
							'hide_empty' => 0,
						)
					)
				);
			}
			$exclusions = array_merge( $excluded_children, $exclusions );
		}

		if ( ! empty( $exclude ) ) {
			$exclusions = array_merge( wp_parse_id_list( $exclude ), $exclusions );
		}

		// 'childless' terms are those without an entry in the flattened term hierarchy.
		$childless = (bool) $args['childless'];
		if ( $childless ) {
			foreach ( $taxonomies as $_tax ) {
				$term_hierarchy = _get_term_hierarchy( $_tax );
				$exclusions     = array_merge( array_keys( $term_hierarchy ), $exclusions );
			}
		}

		if ( ! empty( $exclusions ) ) {
			$exclusions = 't.term_id NOT IN (' . implode( ',', array_map( 'intval', $exclusions ) ) . ')';
		} else {
			$exclusions = '';
		}

		/**
		 * Filters the terms to exclude from the terms query.
		 *
		 * @since 2.3.0
		 *
		 * @param string   $exclusions `NOT IN` clause of the terms query.
		 * @param array    $args       An array of terms query arguments.
		 * @param string[] $taxonomies An array of taxonomy names.
		 */
		$exclusions = apply_filters( 'list_terms_exclusions', $exclusions, $args, $taxonomies );

		if ( ! empty( $exclusions ) ) {
			// Must do string manipulation here for backward compatibility with filter.
			$this->sql_clauses['where']['exclusions'] = preg_replace( '/^\s*AND\s*/', '', $exclusions );
		}

		if (
			( ! empty( $args['name'] ) ) ||
			( is_string( $args['name'] ) && 0 !== strlen( $args['name'] ) )
		) {
			$names = (array) $args['name'];
			foreach ( $names as &$_name ) {
				// `sanitize_term_field()` returns slashed data.
				$_name = stripslashes( sanitize_term_field( 'name', $_name, 0, reset( $taxonomies ), 'db' ) );
			}

			$this->sql_clauses['where']['name'] = "t.name IN ('" . implode( "', '", array_map( 'esc_sql', $names ) ) . "')";
		}

		if (
			( ! empty( $args['slug'] ) ) ||
			( is_string( $args['slug'] ) && 0 !== strlen( $args['slug'] ) )
		) {
			if ( is_array( $args['slug'] ) ) {
				$slug                               = array_map( 'sanitize_title', $args['slug'] );
				$this->sql_clauses['where']['slug'] = "t.slug IN ('" . implode( "', '", $slug ) . "')";
			} else {
				$slug                               = sanitize_title( $args['slug'] );
				$this->sql_clauses['where']['slug'] = "t.slug = '$slug'";
			}
		}

		if ( ! empty( $args['term_taxonomy_id'] ) ) {
			if ( is_array( $args['term_taxonomy_id'] ) ) {
				$tt_ids = implode( ',', array_map( 'intval', $args['term_taxonomy_id'] ) );
				$this->sql_clauses['where']['term_taxonomy_id'] = "tt.term_taxonomy_id IN ({$tt_ids})";
			} else {
				$this->sql_clauses['where']['term_taxonomy_id'] = $wpdb->prepare( 'tt.term_taxonomy_id = %d', $args['term_taxonomy_id'] );
			}
		}

		if ( ! empty( $args['name__like'] ) ) {
			$this->sql_clauses['where']['name__like'] = $wpdb->prepare( 't.name LIKE %s', '%' . $wpdb->esc_like( $args['name__like'] ) . '%' );
		}

		if ( ! empty( $args['description__like'] ) ) {
			$this->sql_clauses['where']['description__like'] = $wpdb->prepare( 'tt.description LIKE %s', '%' . $wpdb->esc_like( $args['description__like'] ) . '%' );
		}

		if ( ! empty( $args['object_ids'] ) ) {
			$object_ids = $args['object_ids'];
			if ( ! is_array( $object_ids ) ) {
				$object_ids = array( $object_ids );
			}

			$object_ids                               = implode( ', ', array_map( 'intval', $object_ids ) );
			$this->sql_clauses['where']['object_ids'] = "tr.object_id IN ($object_ids)";
		}

		/*
		 * When querying for object relationships, the 'count > 0' check
		 * added by 'hide_empty' is superfluous.
		 */
		if ( ! empty( $args['object_ids'] ) ) {
			$args['hide_empty'] = false;
		}

		if ( '' !== $parent ) {
			$parent                               = (int) $parent;
			$this->sql_clauses['where']['parent'] = "tt.parent = '$parent'";
		}

		$hierarchical = $args['hierarchical'];
		if ( 'count' === $args['fields'] ) {
			$hierarchical = false;
		}
		if ( $args['hide_empty'] && ! $hierarchical ) {
			$this->sql_clauses['where']['count'] = 'tt.count > 0';
		}

		$number = $args['number'];
		$offset = $args['offset'];

		// Don't limit the query results when we have to descend the family tree.
		if ( $number && ! $hierarchical && ! $child_of && '' === $parent ) {
			if ( $offset ) {
				$limits = 'LIMIT ' . $offset . ',' . $number;
			} else {
				$limits = 'LIMIT ' . $number;
			}
		} else {
			$limits = '';
		}

		if ( ! empty( $args['search'] ) ) {
			$this->sql_clauses['where']['search'] = $this->get_search_sql( $args['search'] );
		}

		// Meta query support.
		$join     = '';
		$distinct = '';

		// Reparse meta_query query_vars, in case they were modified in a 'pre_get_terms' callback.
		$this->meta_query->parse_query_vars( $this->query_vars );
		$mq_sql       = $this->meta_query->get_sql( 'term', 't', 'term_id' );
		$meta_clauses = $this->meta_query->get_clauses();

		if ( ! empty( $meta_clauses ) ) {
			$join                                    .= $mq_sql['join'];
			$this->sql_clauses['where']['meta_query'] = preg_replace( '/^\s*AND\s*/', '', $mq_sql['where'] );
			$distinct                                .= 'DISTINCT';

		}

		$selects = array();
		switch ( $args['fields'] ) {
			case 'all':
			case 'all_with_object_id':
			case 'tt_ids':
			case 'slugs':
				$selects = array( 't.*', 'tt.*' );
				if ( 'all_with_object_id' === $args['fields'] && ! empty( $args['object_ids'] ) ) {
					$selects[] = 'tr.object_id';
				}
				break;
			case 'ids':
			case 'id=>parent':
				$selects = array( 't.term_id', 'tt.parent', 'tt.count', 'tt.taxonomy' );
				break;
			case 'names':
				$selects = array( 't.term_id', 'tt.parent', 'tt.count', 't.name', 'tt.taxonomy' );
				break;
			case 'count':
				$orderby = '';
				$order   = '';
				$selects = array( 'COUNT(*)' );
				break;
			case 'id=>name':
				$selects = array( 't.term_id', 't.name', 'tt.parent', 'tt.count', 'tt.taxonomy' );
				break;
			case 'id=>slug':
				$selects = array( 't.term_id', 't.slug', 'tt.parent', 'tt.count', 'tt.taxonomy' );
				break;
		}

		$_fields = $args['fields'];

		/**
		 * Filters the fields to select in the terms query.
		 *
		 * Field lists modified using this filter will only modify the term fields returned
		 * by the function when the `$fields` parameter set to 'count' or 'all'. In all other
		 * cases, the term fields in the results array will be determined by the `$fields`
		 * parameter alone.
		 *
		 * Use of this filter can result in unpredictable behavior, and is not recommended.
		 *
		 * @since 2.8.0
		 *
		 * @param string[] $selects    An array of fields to select for the terms query.
		 * @param array    $args       An array of term query arguments.
		 * @param string[] $taxonomies An array of taxonomy names.
		 */
		$fields = implode( ', ', apply_filters( 'get_terms_fields', $selects, $args, $taxonomies ) );

		$join .= " INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id";

		if ( ! empty( $this->query_vars['object_ids'] ) ) {
			$join .= " INNER JOIN {$wpdb->term_relationships} AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id";
		}

		$where = implode( ' AND ', $this->sql_clauses['where'] );

		/**
		 * Filters the terms query SQL clauses.
		 *
		 * @since 3.1.0
		 *
		 * @param string[] $pieces     Array of query SQL clauses.
		 * @param string[] $taxonomies An array of taxonomy names.
		 * @param array    $args       An array of term query arguments.
		 */
		$clauses = apply_filters( 'terms_clauses', compact( 'fields', 'join', 'where', 'distinct', 'orderby', 'order', 'limits' ), $taxonomies, $args );

		$fields   = isset( $clauses['fields'] ) ? $clauses['fields'] : '';
		$join     = isset( $clauses['join'] ) ? $clauses['join'] : '';
		$where    = isset( $clauses['where'] ) ? $clauses['where'] : '';
		$distinct = isset( $clauses['distinct'] ) ? $clauses['distinct'] : '';
		$orderby  = isset( $clauses['orderby'] ) ? $clauses['orderby'] : '';
		$order    = isset( $clauses['order'] ) ? $clauses['order'] : '';
		$limits   = isset( $clauses['limits'] ) ? $clauses['limits'] : '';

		if ( $where ) {
			$where = "WHERE $where";
		}

		$this->sql_clauses['select']  = "SELECT $distinct $fields";
		$this->sql_clauses['from']    = "FROM $wpdb->terms AS t $join";
		$this->sql_clauses['orderby'] = $orderby ? "$orderby $order" : '';
		$this->sql_clauses['limits']  = $limits;

		$this->request = "{$this->sql_clauses['select']} {$this->sql_clauses['from']} {$where} {$this->sql_clauses['orderby']} {$this->sql_clauses['limits']}";

		$this->terms = null;

		/**
		 * Filters the terms array before the query takes place.
		 *
		 * Return a non-null value to bypass WordPress' default term queries.
		 *
		 * @since 5.3.0
		 *
		 * @param array|null    $terms Return an array of term data to short-circuit WP's term query,
		 *                             or null to allow WP queries to run normally.
		 * @param WP_Term_Query $this  The WP_Term_Query instance, passed by reference.
		 */
		$this->terms = apply_filters_ref_array( 'terms_pre_query', array( $this->terms, &$this ) );

		if ( null !== $this->terms ) {
			return $this->terms;
		}

		// $args can be anything. Only use the args defined in defaults to compute the key.
		$key          = md5( serialize( wp_array_slice_assoc( $args, array_keys( $this->query_var_defaults ) ) ) . serialize( $taxonomies ) . $this->request );
		$last_changed = wp_cache_get_last_changed( 'terms' );
		$cache_key    = "get_terms:$key:$last_changed";
		$cache        = wp_cache_get( $cache_key, 'terms' );
		if ( false !== $cache ) {
			if ( 'all' === $_fields || 'all_with_object_id' === $_fields ) {
				$cache = $this->populate_terms( $cache );
			}

			$this->terms = $cache;
			return $this->terms;
		}

		if ( 'count' === $_fields ) {
			$count = $wpdb->get_var( $this->request );
			wp_cache_set( $cache_key, $count, 'terms' );
			return $count;
		}

		$terms = $wpdb->get_results( $this->request );

		if ( 'all' === $_fields || 'all_with_object_id' === $_fields ) {
			update_term_cache( $terms );
		}

		// Prime termmeta cache.
		if ( $args['update_term_meta_cache'] ) {
			$term_ids = wp_list_pluck( $terms, 'term_id' );
			update_termmeta_cache( $term_ids );
		}

		if ( empty( $terms ) ) {
			wp_cache_add( $cache_key, array(), 'terms', DAY_IN_SECONDS );
			return array();
		}

		if ( $child_of ) {
			foreach ( $taxonomies as $_tax ) {
				$children = _get_term_hierarchy( $_tax );
				if ( ! empty( $children ) ) {
					$terms = _get_term_children( $child_of, $terms, $_tax );
				}
			}
		}

		// Update term counts to include children.
		if ( $args['pad_counts'] && 'all' === $_fields ) {
			foreach ( $taxonomies as $_tax ) {
				_pad_term_counts( $terms, $_tax );
			}
		}

		// Make sure we show empty categories that have children.
		if ( $hierarchical && $args['hide_empty'] && is_array( $terms ) ) {
			foreach ( $terms as $k => $term ) {
				if ( ! $term->count ) {
					$children = get_term_children( $term->term_id, $term->taxonomy );
					if ( is_array( $children ) ) {
						foreach ( $children as $child_id ) {
							$child = get_term( $child_id, $term->taxonomy );
							if ( $child->count ) {
								continue 2;
							}
						}
					}

					// It really is empty.
					unset( $terms[ $k ] );
				}
			}
		}

		/*
		 * When querying for terms connected to objects, we may get
		 * duplicate results. The duplicates should be preserved if
		 * `$fields` is 'all_with_object_id', but should otherwise be
		 * removed.
		 */
		if ( ! empty( $args['object_ids'] ) && 'all_with_object_id' !== $_fields ) {
			$_tt_ids = array();
			$_terms  = array();
			foreach ( $terms as $term ) {
				if ( isset( $_tt_ids[ $term->term_id ] ) ) {
					continue;
				}

				$_tt_ids[ $term->term_id ] = 1;
				$_terms[]                  = $term;
			}

			$terms = $_terms;
		}

		$_terms = array();
		if ( 'id=>parent' === $_fields ) {
			foreach ( $terms as $term ) {
				$_terms[ $term->term_id ] = $term->parent;
			}
		} elseif ( 'ids' === $_fields ) {
			foreach ( $terms as $term ) {
				$_terms[] = (int) $term->term_id;
			}
		} elseif ( 'tt_ids' === $_fields ) {
			foreach ( $terms as $term ) {
				$_terms[] = (int) $term->term_taxonomy_id;
			}
		} elseif ( 'names' === $_fields ) {
			foreach ( $terms as $term ) {
				$_terms[] = $term->name;
			}
		} elseif ( 'slugs' === $_fields ) {
			foreach ( $terms as $term ) {
				$_terms[] = $term->slug;
			}
		} elseif ( 'id=>name' === $_fields ) {
			foreach ( $terms as $term ) {
				$_terms[ $term->term_id ] = $term->name;
			}
		} elseif ( 'id=>slug' === $_fields ) {
			foreach ( $terms as $term ) {
				$_terms[ $term->term_id ] = $term->slug;
			}
		}

		if ( ! empty( $_terms ) ) {
			$terms = $_terms;
		}

		// Hierarchical queries are not limited, so 'offset' and 'number' must be handled now.
		if ( $hierarchical && $number && is_array( $terms ) ) {
			if ( $offset >= count( $terms ) ) {
				$terms = array();
			} else {
				$terms = array_slice( $terms, $offset, $number, true );
			}
		}

		wp_cache_add( $cache_key, $terms, 'terms', DAY_IN_SECONDS );

		if ( 'all' === $_fields || 'all_with_object_id' === $_fields ) {
			$terms = $this->populate_terms( $terms );
		}

		$this->terms = $terms;
		return $this->terms;
	}

	/**
	 * Parse and sanitize 'orderby' keys passed to the term query.
	 *
	 * @since 4.6.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $orderby_raw Alias for the field to order by.
	 * @return string|false Value to used in the ORDER clause. False otherwise.
	 */
	protected function parse_orderby( $orderby_raw ) {
		$_orderby           = strtolower( $orderby_raw );
		$maybe_orderby_meta = false;

		if ( in_array( $_orderby, array( 'term_id', 'name', 'slug', 'term_group' ), true ) ) {
			$orderby = "t.$_orderby";
		} elseif ( in_array( $_orderby, array( 'count', 'parent', 'taxonomy', 'term_taxonomy_id', 'description' ), true ) ) {
			$orderby = "tt.$_orderby";
		} elseif ( 'term_order' === $_orderby ) {
			$orderby = 'tr.term_order';
		} elseif ( 'include' === $_orderby && ! empty( $this->query_vars['include'] ) ) {
			$include = implode( ',', wp_parse_id_list( $this->query_vars['include'] ) );
			$orderby = "FIELD( t.term_id, $include )";
		} elseif ( 'slug__in' === $_orderby && ! empty( $this->query_vars['slug'] ) && is_array( $this->query_vars['slug'] ) ) {
			$slugs   = implode( "', '", array_map( 'sanitize_title_for_query', $this->query_vars['slug'] ) );
			$orderby = "FIELD( t.slug, '" . $slugs . "')";
		} elseif ( 'none' === $_orderby ) {
			$orderby = '';
		} elseif ( empty( $_orderby ) || 'id' === $_orderby || 'term_id' === $_orderby ) {
			$orderby = 't.term_id';
		} else {
			$orderby = 't.name';

			// This may be a value of orderby related to meta.
			$maybe_orderby_meta = true;
		}

		/**
		 * Filters the ORDERBY clause of the terms query.
		 *
		 * @since 2.8.0
		 *
		 * @param string   $orderby    `ORDERBY` clause of the terms query.
		 * @param array    $args       An array of term query arguments.
		 * @param string[] $taxonomies An array of taxonomy names.
		 */
		$orderby = apply_filters( 'get_terms_orderby', $orderby, $this->query_vars, $this->query_vars['taxonomy'] );

		// Run after the 'get_terms_orderby' filter for backward compatibility.
		if ( $maybe_orderby_meta ) {
			$maybe_orderby_meta = $this->parse_orderby_meta( $_orderby );
			if ( $maybe_orderby_meta ) {
				$orderby = $maybe_orderby_meta;
			}
		}

		return $orderby;
	}

	/**
	 * Generate the ORDER BY clause for an 'orderby' param that is potentially related to a meta query.
	 *
	 * @since 4.6.0
	 *
	 * @param string $orderby_raw Raw 'orderby' value passed to WP_Term_Query.
	 * @return string ORDER BY clause.
	 */
	protected function parse_orderby_meta( $orderby_raw ) {
		$orderby = '';

		// Tell the meta query to generate its SQL, so we have access to table aliases.
		$this->meta_query->get_sql( 'term', 't', 'term_id' );
		$meta_clauses = $this->meta_query->get_clauses();
		if ( ! $meta_clauses || ! $orderby_raw ) {
			return $orderby;
		}

		$allowed_keys       = array();
		$primary_meta_key   = null;
		$primary_meta_query = reset( $meta_clauses );
		if ( ! empty( $primary_meta_query['key'] ) ) {
			$primary_meta_key = $primary_meta_query['key'];
			$allowed_keys[]   = $primary_meta_key;
		}
		$allowed_keys[] = 'meta_value';
		$allowed_keys[] = 'meta_value_num';
		$allowed_keys   = array_merge( $allowed_keys, array_keys( $meta_clauses ) );

		if ( ! in_array( $orderby_raw, $allowed_keys, true ) ) {
			return $orderby;
		}

		switch ( $orderby_raw ) {
			case $primary_meta_key:
			case 'meta_value':
				if ( ! empty( $primary_meta_query['type'] ) ) {
					$orderby = "CAST({$primary_meta_query['alias']}.meta_value AS {$primary_meta_query['cast']})";
				} else {
					$orderby = "{$primary_meta_query['alias']}.meta_value";
				}
				break;

			case 'meta_value_num':
				$orderby = "{$primary_meta_query['alias']}.meta_value+0";
				break;

			default:
				if ( array_key_exists( $orderby_raw, $meta_clauses ) ) {
					// $orderby corresponds to a meta_query clause.
					$meta_clause = $meta_clauses[ $orderby_raw ];
					$orderby     = "CAST({$meta_clause['alias']}.meta_value AS {$meta_clause['cast']})";
				}
				break;
		}

		return $orderby;
	}

	/**
	 * Parse an 'order' query variable and cast it to ASC or DESC as necessary.
	 *
	 * @since 4.6.0
	 *
	 * @param string $order The 'order' query variable.
	 * @return string The sanitized 'order' query variable.
	 */
	protected function parse_order( $order ) {
		if ( ! is_string( $order ) || empty( $order ) ) {
			return 'DESC';
		}

		if ( 'ASC' === strtoupper( $order ) ) {
			return 'ASC';
		} else {
			return 'DESC';
		}
	}

	/**
	 * Used internally to generate a SQL string related to the 'search' parameter.
	 *
	 * @since 4.6.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $string
	 * @return string
	 */
	protected function get_search_sql( $string ) {
		global $wpdb;

		$like = '%' . $wpdb->esc_like( $string ) . '%';

		return $wpdb->prepare( '((t.name LIKE %s) OR (t.slug LIKE %s))', $like, $like );
	}

	/**
	 * Creates an array of term objects from an array of term IDs.
	 *
	 * Also discards invalid term objects.
	 *
	 * @since 4.9.8
	 *
	 * @param array $term_ids Term IDs.
	 * @return array
	 */
	protected function populate_terms( $term_ids ) {
		$terms = array();

		if ( ! is_array( $term_ids ) ) {
			return $terms;
		}

		foreach ( $term_ids as $key => $term_id ) {
			$term = get_term( $term_id );
			if ( $term instanceof WP_Term ) {
				$terms[ $key ] = $term;
			}
		}

		return $terms;
	}
}
