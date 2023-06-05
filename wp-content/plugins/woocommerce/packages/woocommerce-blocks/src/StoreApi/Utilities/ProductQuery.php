<?php
namespace Automattic\WooCommerce\StoreApi\Utilities;

use WC_Tax;

/**
 * Product Query class.
 *
 * Helper class to handle product queries for the API.
 */
class ProductQuery {
	/**
	 * Prepare query args to pass to WP_Query for a REST API request.
	 *
	 * @param \WP_REST_Request $request Request data.
	 * @return array
	 */
	public function prepare_objects_query( $request ) {
		$args = [
			'offset'              => $request['offset'],
			'order'               => $request['order'],
			'orderby'             => $request['orderby'],
			'paged'               => $request['page'],
			'post__in'            => $request['include'],
			'post__not_in'        => $request['exclude'],
			'posts_per_page'      => $request['per_page'] ? $request['per_page'] : -1,
			'post_parent__in'     => $request['parent'],
			'post_parent__not_in' => $request['parent_exclude'],
			'search'              => $request['search'], // This uses search rather than s intentionally to handle searches internally.
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'post_status'         => 'publish',
			'date_query'          => [],
			'post_type'           => 'product',
		];

		// If searching for a specific SKU, allow any post type.
		if ( ! empty( $request['sku'] ) ) {
			$args['post_type'] = [ 'product', 'product_variation' ];
		}

		// Taxonomy query to filter products by type, category, tag, shipping class, and attribute.
		$tax_query = [];

		// Filter product type by slug.
		if ( ! empty( $request['type'] ) ) {
			if ( 'variation' === $request['type'] ) {
				$args['post_type'] = 'product_variation';
			} else {
				$args['post_type'] = 'product';
				$tax_query[]       = [
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => $request['type'],
				];
			}
		}

		if ( 'date' === $args['orderby'] ) {
			$args['orderby'] = 'date ID';
		}

		// Set before into date query. Date query must be specified as an array of an array.
		if ( isset( $request['before'] ) ) {
			$args['date_query'][0]['before'] = $request['before'];
		}

		// Set after into date query. Date query must be specified as an array of an array.
		if ( isset( $request['after'] ) ) {
			$args['date_query'][0]['after'] = $request['after'];
		}

		// Set date query column. Defaults to post_date.
		if ( isset( $request['date_column'] ) && ! empty( $args['date_query'][0] ) ) {
			$args['date_query'][0]['column'] = 'post_' . $request['date_column'];
		}

		// Set custom args to handle later during clauses.
		$custom_keys = [
			'sku',
			'min_price',
			'max_price',
			'stock_status',
		];

		foreach ( $custom_keys as $key ) {
			if ( ! empty( $request[ $key ] ) ) {
				$args[ $key ] = $request[ $key ];
			}
		}

		$operator_mapping = [
			'in'     => 'IN',
			'not_in' => 'NOT IN',
			'and'    => 'AND',
		];

		// Gets all registered product taxonomies and prefixes them with `tax_`.
		// This is neeeded to avoid situations where a users registers a new product taxonomy with the same name as default field.
		// eg an `sku` taxonomy will be mapped to `tax_sku`.
		$all_product_taxonomies = array_map(
			function ( $value ) {
				return '_unstable_tax_' . $value;
			},
			get_taxonomies( array( 'object_type' => array( 'product' ) ), 'names' )
		);

		// Map between taxonomy name and arg key.
		$default_taxonomies = [
			'product_cat' => 'category',
			'product_tag' => 'tag',
		];

		$taxonomies = array_merge( $all_product_taxonomies, $default_taxonomies );

		// Set tax_query for each passed arg.
		foreach ( $taxonomies as $taxonomy => $key ) {
			if ( ! empty( $request[ $key ] ) ) {
				$operator    = $request->get_param( $key . '_operator' ) && isset( $operator_mapping[ $request->get_param( $key . '_operator' ) ] ) ? $operator_mapping[ $request->get_param( $key . '_operator' ) ] : 'IN';
				$tax_query[] = [
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $request[ $key ],
					'operator' => $operator,
				];
			}
		}

		// Filter by attributes.
		if ( ! empty( $request['attributes'] ) ) {
			$att_queries = [];

			foreach ( $request['attributes'] as $attribute ) {
				if ( empty( $attribute['term_id'] ) && empty( $attribute['slug'] ) ) {
					continue;
				}
				if ( in_array( $attribute['attribute'], wc_get_attribute_taxonomy_names(), true ) ) {
					$operator      = isset( $attribute['operator'], $operator_mapping[ $attribute['operator'] ] ) ? $operator_mapping[ $attribute['operator'] ] : 'IN';
					$att_queries[] = [
						'taxonomy' => $attribute['attribute'],
						'field'    => ! empty( $attribute['term_id'] ) ? 'term_id' : 'slug',
						'terms'    => ! empty( $attribute['term_id'] ) ? $attribute['term_id'] : $attribute['slug'],
						'operator' => $operator,
					];
				}
			}

			if ( 1 < count( $att_queries ) ) {
				// Add relation arg when using multiple attributes.
				$relation    = $request->get_param( 'attribute_relation' ) && isset( $operator_mapping[ $request->get_param( 'attribute_relation' ) ] ) ? $operator_mapping[ $request->get_param( 'attribute_relation' ) ] : 'IN';
				$tax_query[] = [
					'relation' => $relation,
					$att_queries,
				];
			} else {
				$tax_query = array_merge( $tax_query, $att_queries );
			}
		}

		// Build tax_query if taxonomies are set.
		if ( ! empty( $tax_query ) ) {
			if ( ! empty( $args['tax_query'] ) ) {
				$args['tax_query'] = array_merge( $tax_query, $args['tax_query'] ); // phpcs:ignore
			} else {
				$args['tax_query'] = $tax_query; // phpcs:ignore
			}
		}

		// Filter featured.
		if ( is_bool( $request['featured'] ) ) {
			$args['tax_query'][] = [
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
				'operator' => true === $request['featured'] ? 'IN' : 'NOT IN',
			];
		}

		// Filter by on sale products.
		if ( is_bool( $request['on_sale'] ) ) {
			$on_sale_key = $request['on_sale'] ? 'post__in' : 'post__not_in';
			$on_sale_ids = wc_get_product_ids_on_sale();

			// Use 0 when there's no on sale products to avoid return all products.
			$on_sale_ids = empty( $on_sale_ids ) ? [ 0 ] : $on_sale_ids;

			$args[ $on_sale_key ] += $on_sale_ids;
		}

		$catalog_visibility = $request->get_param( 'catalog_visibility' );
		$rating             = $request->get_param( 'rating' );
		$visibility_options = wc_get_product_visibility_options();

		if ( in_array( $catalog_visibility, array_keys( $visibility_options ), true ) ) {
			$exclude_from_catalog = 'search' === $catalog_visibility ? '' : 'exclude-from-catalog';
			$exclude_from_search  = 'catalog' === $catalog_visibility ? '' : 'exclude-from-search';

			$args['tax_query'][] = [
				'taxonomy'      => 'product_visibility',
				'field'         => 'name',
				'terms'         => [ $exclude_from_catalog, $exclude_from_search ],
				'operator'      => 'hidden' === $catalog_visibility ? 'AND' : 'NOT IN',
				'rating_filter' => true,
			];
		}

		if ( $rating ) {
			$rating_terms = [];
			foreach ( $rating as $value ) {
				$rating_terms[] = 'rated-' . $value;
			}
			$args['tax_query'][] = [
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => $rating_terms,
			];
		}

		$orderby = $request->get_param( 'orderby' );
		$order   = $request->get_param( 'order' );

		$ordering_args   = wc()->query->get_catalog_ordering_args( $orderby, $order );
		$args['orderby'] = $ordering_args['orderby'];
		$args['order']   = $ordering_args['order'];

		if ( 'include' === $orderby ) {
			$args['orderby'] = 'post__in';
		} elseif ( 'id' === $orderby ) {
			$args['orderby'] = 'ID'; // ID must be capitalized.
		} elseif ( 'slug' === $orderby ) {
			$args['orderby'] = 'name';
		}

		if ( $ordering_args['meta_key'] ) {
			$args['meta_key'] = $ordering_args['meta_key']; // phpcs:ignore
		}

		return $args;
	}

	/**
	 * Get results of query.
	 *
	 * @param \WP_REST_Request $request Request data.
	 * @return array
	 */
	public function get_results( $request ) {
		$query_args = $this->prepare_objects_query( $request );

		add_filter( 'posts_clauses', [ $this, 'add_query_clauses' ], 10, 2 );

		$query       = new \WP_Query();
		$results     = $query->query( $query_args );
		$total_posts = $query->found_posts;

		// Out-of-bounds, run the query again without LIMIT for total count.
		if ( $total_posts < 1 && $query_args['paged'] > 1 ) {
			unset( $query_args['paged'] );
			$count_query = new \WP_Query();
			$count_query->query( $query_args );
			$total_posts = $count_query->found_posts;
		}

		remove_filter( 'posts_clauses', [ $this, 'add_query_clauses' ], 10 );

		return [
			'results' => $results,
			'total'   => (int) $total_posts,
			'pages'   => $query->query_vars['posts_per_page'] > 0 ? (int) ceil( $total_posts / (int) $query->query_vars['posts_per_page'] ) : 1,
		];
	}

	/**
	 * Get objects.
	 *
	 * @param \WP_REST_Request $request Request data.
	 * @return array
	 */
	public function get_objects( $request ) {
		$results = $this->get_results( $request );

		return [
			'objects' => array_map( 'wc_get_product', $results['results'] ),
			'total'   => $results['total'],
			'pages'   => $results['pages'],
		];
	}

	/**
	 * Get last modified date for all products.
	 *
	 * @return int timestamp.
	 */
	public function get_last_modified() {
		global $wpdb;

		return strtotime( $wpdb->get_var( "SELECT MAX( post_modified_gmt ) FROM {$wpdb->posts} WHERE post_type IN ( 'product', 'product_variation' );" ) );
	}

	/**
	 * Add in conditional search filters for products.
	 *
	 * @param array     $args Query args.
	 * @param \WC_Query $wp_query WC_Query object.
	 * @return array
	 */
	public function add_query_clauses( $args, $wp_query ) {
		global $wpdb;

		if ( $wp_query->get( 'search' ) ) {
			$search         = '%' . $wpdb->esc_like( $wp_query->get( 'search' ) ) . '%';
			$search_query   = wc_product_sku_enabled()
				? $wpdb->prepare( " AND ( $wpdb->posts.post_title LIKE %s OR wc_product_meta_lookup.sku LIKE %s ) ", $search, $search )
				: $wpdb->prepare( " AND $wpdb->posts.post_title LIKE %s ", $search );
			$args['where'] .= $search_query;
			$args['join']   = $this->append_product_sorting_table_join( $args['join'] );
		}

		if ( $wp_query->get( 'sku' ) ) {
			$skus = explode( ',', $wp_query->get( 'sku' ) );
			// Include the current string as a SKU too.
			if ( 1 < count( $skus ) ) {
				$skus[] = $wp_query->get( 'sku' );
			}
			$args['join']   = $this->append_product_sorting_table_join( $args['join'] );
			$args['where'] .= ' AND wc_product_meta_lookup.sku IN ("' . implode( '","', array_map( 'esc_sql', $skus ) ) . '")';
		}

		if ( $wp_query->get( 'stock_status' ) ) {
			$args['join']   = $this->append_product_sorting_table_join( $args['join'] );
			$args['where'] .= ' AND wc_product_meta_lookup.stock_status IN ("' . implode( '","', array_map( 'esc_sql', $wp_query->get( 'stock_status' ) ) ) . '")';
		} elseif ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$args['join']   = $this->append_product_sorting_table_join( $args['join'] );
			$args['where'] .= ' AND wc_product_meta_lookup.stock_status NOT IN ("outofstock")';
		}

		if ( $wp_query->get( 'min_price' ) || $wp_query->get( 'max_price' ) ) {
			$args = $this->add_price_filter_clauses( $args, $wp_query );
		}

		return $args;
	}

	/**
	 * Add in conditional price filters.
	 *
	 * @param array     $args Query args.
	 * @param \WC_Query $wp_query WC_Query object.
	 * @return array
	 */
	protected function add_price_filter_clauses( $args, $wp_query ) {
		global $wpdb;

		$adjust_for_taxes = $this->adjust_price_filters_for_displayed_taxes();
		$args['join']     = $this->append_product_sorting_table_join( $args['join'] );

		if ( $wp_query->get( 'min_price' ) ) {
			$min_price_filter = $this->prepare_price_filter( $wp_query->get( 'min_price' ) );

			if ( $adjust_for_taxes ) {
				$args['where'] .= $this->get_price_filter_query_for_displayed_taxes( $min_price_filter, 'min_price', '>=' );
			} else {
				$args['where'] .= $wpdb->prepare( ' AND wc_product_meta_lookup.min_price >= %f ', $min_price_filter );
			}
		}

		if ( $wp_query->get( 'max_price' ) ) {
			$max_price_filter = $this->prepare_price_filter( $wp_query->get( 'max_price' ) );

			if ( $adjust_for_taxes ) {
				$args['where'] .= $this->get_price_filter_query_for_displayed_taxes( $max_price_filter, 'max_price', '<=' );
			} else {
				$args['where'] .= $wpdb->prepare( ' AND wc_product_meta_lookup.max_price <= %f ', $max_price_filter );
			}
		}

		return $args;
	}

	/**
	 * Get query for price filters when dealing with displayed taxes.
	 *
	 * @param float  $price_filter Price filter to apply.
	 * @param string $column Price being filtered (min or max).
	 * @param string $operator Comparison operator for column.
	 * @return string Constructed query.
	 */
	protected function get_price_filter_query_for_displayed_taxes( $price_filter, $column = 'min_price', $operator = '>=' ) {
		global $wpdb;

		// Select only used tax classes to avoid unwanted calculations.
		$product_tax_classes = $wpdb->get_col( "SELECT DISTINCT tax_class FROM {$wpdb->wc_product_meta_lookup};" );

		if ( empty( $product_tax_classes ) ) {
			return '';
		}

		$or_queries = [];

		// We need to adjust the filter for each possible tax class and combine the queries into one.
		foreach ( $product_tax_classes as $tax_class ) {
			$adjusted_price_filter = $this->adjust_price_filter_for_tax_class( $price_filter, $tax_class );
			$or_queries[]          = $wpdb->prepare(
				'( wc_product_meta_lookup.tax_class = %s AND wc_product_meta_lookup.`' . esc_sql( $column ) . '` ' . esc_sql( $operator ) . ' %f )',
				$tax_class,
				$adjusted_price_filter
			);
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
		return $wpdb->prepare(
			' AND (
				wc_product_meta_lookup.tax_status = "taxable" AND ( 0=1 OR ' . implode( ' OR ', $or_queries ) . ')
				OR ( wc_product_meta_lookup.tax_status != "taxable" AND wc_product_meta_lookup.`' . esc_sql( $column ) . '` ' . esc_sql( $operator ) . ' %f )
			) ',
			$price_filter
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * If price filters need adjustment to work with displayed taxes, this returns true.
	 *
	 * This logic is used when prices are stored in the database differently to how they are being displayed, with regards
	 * to taxes.
	 *
	 * @return boolean
	 */
	protected function adjust_price_filters_for_displayed_taxes() {
		$display  = get_option( 'woocommerce_tax_display_shop' );
		$database = wc_prices_include_tax() ? 'incl' : 'excl';

		return $display !== $database;
	}

	/**
	 * Converts price filter from subunits to decimal.
	 *
	 * @param string|int $price_filter Raw price filter in subunit format.
	 * @return float Price filter in decimal format.
	 */
	protected function prepare_price_filter( $price_filter ) {
		return floatval( $price_filter / ( 10 ** wc_get_price_decimals() ) );
	}

	/**
	 * Adjusts a price filter based on a tax class and whether or not the amount includes or excludes taxes.
	 *
	 * This calculation logic is based on `wc_get_price_excluding_tax` and `wc_get_price_including_tax` in core.
	 *
	 * @param float  $price_filter Price filter amount as entered.
	 * @param string $tax_class Tax class for adjustment.
	 * @return float
	 */
	protected function adjust_price_filter_for_tax_class( $price_filter, $tax_class ) {
		$tax_display    = get_option( 'woocommerce_tax_display_shop' );
		$tax_rates      = WC_Tax::get_rates( $tax_class );
		$base_tax_rates = WC_Tax::get_base_tax_rates( $tax_class );

		// If prices are shown incl. tax, we want to remove the taxes from the filter amount to match prices stored excl. tax.
		if ( 'incl' === $tax_display ) {
			/**
			 * Filters if taxes should be removed from locations outside the store base location.
			 *
			 * The woocommerce_adjust_non_base_location_prices filter can stop base taxes being taken off when dealing
			 * with out of base locations. e.g. If a product costs 10 including tax, all users will pay 10
			 * regardless of location and taxes.
			 *
			 * @since 2.6.0
			 *
			 * @internal Matches filter name in WooCommerce core.
			 *
			 * @param boolean $adjust_non_base_location_prices True by default.
			 * @return boolean
			 */
			$taxes = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $price_filter, $base_tax_rates, true ) : WC_Tax::calc_tax( $price_filter, $tax_rates, true );
			return $price_filter - array_sum( $taxes );
		}

		// If prices are shown excl. tax, add taxes to match the prices stored in the DB.
		$taxes = WC_Tax::calc_tax( $price_filter, $tax_rates, false );

		return $price_filter + array_sum( $taxes );
	}

	/**
	 * Join wc_product_meta_lookup to posts if not already joined.
	 *
	 * @param string $sql SQL join.
	 * @return string
	 */
	protected function append_product_sorting_table_join( $sql ) {
		global $wpdb;

		if ( ! strstr( $sql, 'wc_product_meta_lookup' ) ) {
			$sql .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		}
		return $sql;
	}
}
