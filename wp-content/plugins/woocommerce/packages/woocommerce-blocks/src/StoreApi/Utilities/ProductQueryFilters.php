<?php
namespace Automattic\WooCommerce\StoreApi\Utilities;

use Automattic\WooCommerce\StoreApi\Utilities\ProductQuery;

/**
 * Product Query filters class.
 */
class ProductQueryFilters {
	/**
	 * Get filtered min price for current products.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return object
	 */
	public function get_filtered_price( $request ) {
		global $wpdb;

		// Regenerate the products query without min/max price request params.
		unset( $request['min_price'], $request['max_price'] );

		// Grab the request from the WP Query object, and remove SQL_CALC_FOUND_ROWS and Limits so we get a list of all products.
		$product_query = new ProductQuery();

		add_filter( 'posts_clauses', array( $product_query, 'add_query_clauses' ), 10, 2 );
		add_filter( 'posts_pre_query', '__return_empty_array' );

		$query_args                   = $product_query->prepare_objects_query( $request );
		$query_args['no_found_rows']  = true;
		$query_args['posts_per_page'] = -1;
		$query                        = new \WP_Query();
		$result                       = $query->query( $query_args );
		$product_query_sql            = $query->request;

		remove_filter( 'posts_clauses', array( $product_query, 'add_query_clauses' ), 10 );
		remove_filter( 'posts_pre_query', '__return_empty_array' );

		$price_filter_sql = "
			SELECT min( min_price ) as min_price, MAX( max_price ) as max_price
			FROM {$wpdb->wc_product_meta_lookup}
			WHERE product_id IN ( {$product_query_sql} )
		";

		return $wpdb->get_row( $price_filter_sql ); // phpcs:ignore
	}

	/**
	 * Get stock status counts for the current products.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return array status=>count pairs.
	 */
	public function get_stock_status_counts( $request ) {
		global $wpdb;
		$product_query         = new ProductQuery();
		$stock_status_options  = array_map( 'esc_sql', array_keys( wc_get_product_stock_status_options() ) );
		$hide_outofstock_items = get_option( 'woocommerce_hide_out_of_stock_items' );
		if ( 'yes' === $hide_outofstock_items ) {
			unset( $stock_status_options['outofstock'] );
		}

		add_filter( 'posts_clauses', array( $product_query, 'add_query_clauses' ), 10, 2 );
		add_filter( 'posts_pre_query', '__return_empty_array' );

		$query_args = $product_query->prepare_objects_query( $request );
		unset( $query_args['stock_status'] );
		$query_args['no_found_rows']  = true;
		$query_args['posts_per_page'] = -1;
		$query                        = new \WP_Query();
		$result                       = $query->query( $query_args );
		$product_query_sql            = $query->request;

		remove_filter( 'posts_clauses', array( $product_query, 'add_query_clauses' ), 10 );
		remove_filter( 'posts_pre_query', '__return_empty_array' );

		$stock_status_counts = array();

		foreach ( $stock_status_options as $status ) {
			$stock_status_count_sql = $this->generate_stock_status_count_query( $status, $product_query_sql, $stock_status_options );

			$result = $wpdb->get_row( $stock_status_count_sql ); // phpcs:ignore
			$stock_status_counts[ $status ] = $result->status_count;
		}

		return $stock_status_counts;
	}

	/**
	 * Generate calculate query by stock status.
	 *
	 * @param string $status status to calculate.
	 * @param string $product_query_sql product query for current filter state.
	 * @param array  $stock_status_options available stock status options.
	 *
	 * @return false|string
	 */
	private function generate_stock_status_count_query( $status, $product_query_sql, $stock_status_options ) {
		if ( ! in_array( $status, $stock_status_options, true ) ) {
			return false;
		}
		global $wpdb;
		$status = esc_sql( $status );
		return "
			SELECT COUNT( DISTINCT posts.ID ) as status_count
			FROM {$wpdb->posts} as posts
			INNER JOIN {$wpdb->postmeta} as postmeta ON posts.ID = postmeta.post_id
            AND postmeta.meta_key = '_stock_status'
            AND postmeta.meta_value = '{$status}'
			WHERE posts.ID IN ( {$product_query_sql} )
		";
	}

	/**
	 * Get attribute counts for the current products.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @param array            $attributes Attributes to count, either names or ids.
	 * @return array termId=>count pairs.
	 */
	public function get_attribute_counts( $request, $attributes = [] ) {
		global $wpdb;

		// Remove paging and sorting params from the request.
		$request->set_param( 'page', null );
		$request->set_param( 'per_page', null );
		$request->set_param( 'order', null );
		$request->set_param( 'orderby', null );

		// Grab the request from the WP Query object, and remove SQL_CALC_FOUND_ROWS and Limits so we get a list of all products.
		$product_query = new ProductQuery();

		add_filter( 'posts_clauses', array( $product_query, 'add_query_clauses' ), 10, 2 );
		add_filter( 'posts_pre_query', '__return_empty_array' );

		$query_args                   = $product_query->prepare_objects_query( $request );
		$query_args['no_found_rows']  = true;
		$query_args['posts_per_page'] = -1;
		$query                        = new \WP_Query();
		$result                       = $query->query( $query_args );
		$product_query_sql            = $query->request;

		remove_filter( 'posts_clauses', array( $product_query, 'add_query_clauses' ), 10 );
		remove_filter( 'posts_pre_query', '__return_empty_array' );

		if ( count( $attributes ) === count( array_filter( $attributes, 'is_numeric' ) ) ) {
			$attributes = array_map( 'wc_attribute_taxonomy_name_by_id', wp_parse_id_list( $attributes ) );
		}

		$attributes_to_count     = array_map(
			function( $attribute ) {
				$attribute = wc_sanitize_taxonomy_name( $attribute );
				return esc_sql( $attribute );
			},
			$attributes
		);
		$attributes_to_count_sql = 'AND term_taxonomy.taxonomy IN ("' . implode( '","', $attributes_to_count ) . '")';
		$attribute_count_sql     = "
			SELECT COUNT( DISTINCT posts.ID ) as term_count, terms.term_id as term_count_id
			FROM {$wpdb->posts} AS posts
			INNER JOIN {$wpdb->term_relationships} AS term_relationships ON posts.ID = term_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
			INNER JOIN {$wpdb->terms} AS terms USING( term_id )
			WHERE posts.ID IN ( {$product_query_sql} )
			{$attributes_to_count_sql}
			GROUP BY terms.term_id
		";

		$results = $wpdb->get_results( $attribute_count_sql ); // phpcs:ignore

		return array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
	}

	/**
	 * Get rating counts for the current products.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return array rating=>count pairs.
	 */
	public function get_rating_counts( $request ) {
		global $wpdb;

		// Regenerate the products query without rating request params.
		unset( $request['rating'] );

		// Grab the request from the WP Query object, and remove SQL_CALC_FOUND_ROWS and Limits so we get a list of all products.
		$product_query = new ProductQuery();

		add_filter( 'posts_clauses', array( $product_query, 'add_query_clauses' ), 10, 2 );
		add_filter( 'posts_pre_query', '__return_empty_array' );

		$query_args                   = $product_query->prepare_objects_query( $request );
		$query_args['no_found_rows']  = true;
		$query_args['posts_per_page'] = -1;
		$query                        = new \WP_Query();
		$result                       = $query->query( $query_args );
		$product_query_sql            = $query->request;

		remove_filter( 'posts_clauses', array( $product_query, 'add_query_clauses' ), 10 );
		remove_filter( 'posts_pre_query', '__return_empty_array' );

		$rating_count_sql = "
			SELECT COUNT( DISTINCT product_id ) as product_count, ROUND( average_rating, 0 ) as rounded_average_rating
			FROM {$wpdb->wc_product_meta_lookup}
			WHERE product_id IN ( {$product_query_sql} )
			AND average_rating > 0
			GROUP BY rounded_average_rating
			ORDER BY rounded_average_rating ASC
		";

		$results = $wpdb->get_results( $rating_count_sql ); // phpcs:ignore

		return array_map( 'absint', wp_list_pluck( $results, 'product_count', 'rounded_average_rating' ) );
	}

	/**
	 * Gets product by metas.
	 *
	 * @since TBD
	 * @param array $metas Array of metas to query.
	 * @return array $results
	 */
	public function get_product_by_metas( $metas = array() ) {
		global $wpdb;

		if ( empty( $metas ) ) {
			return array();
		}

		$where   = array();
		$results = array();
		$params  = array();

		foreach ( $metas as $column => $value ) {
			if ( 'min_price' === $column ) {
				$where[]  = "{$column} >= %f";
				$params[] = (float) $value;
				continue;
			}

			if ( 'max_price' === $column ) {
				$where[]  = "{$column} <= %f";
				$params[] = (float) $value;
				continue;
			}

			$where[]  = "{$column} = %s";
			$params[] = $value;
		}

		if ( ! empty( $where ) ) {
			$where_clause = implode( ' AND ', $where );
			// Use a parameterized query.
			$results = $wpdb->get_col(
				$wpdb->prepare( "SELECT DISTINCT product_id FROM {$wpdb->prefix}wc_product_meta_lookup WHERE {$where_clause}", // phpcs:ignore
					$params
				)
			);
		}

		return $results;
	}

	/**
	 * Gets product by filtered terms.
	 *
	 * @since TBD
	 * @param string $taxonomy Taxonomy name.
	 * @param array  $term_ids Term IDs.
	 * @param string $query_type or | and.
	 * @return array Product IDs.
	 */
	public function get_product_by_filtered_terms( $taxonomy = '', $term_ids = array(), $query_type = 'or' ) {
		global $wpdb;

		$term_count = count( $term_ids );
		$results    = array();
		$term_ids   = implode( ',', array_map( 'intval', $term_ids ) );

		if ( 'or' === $query_type ) {
			// phpcs:disable
			$results = $wpdb->get_col(
				$wpdb->prepare(
					"
					SELECT DISTINCT `product_or_parent_id`
					FROM {$wpdb->prefix}wc_product_attributes_lookup
					WHERE `taxonomy` = %s
					AND `term_id` IN ({$term_ids})
					",
					$taxonomy
				)
			);
			// phpcs:enable
		}

		if ( 'and' === $query_type ) {
			// phpcs:disable
			$results = $wpdb->get_col(
				$wpdb->prepare(
					"
					SELECT DISTINCT `product_or_parent_id`
					FROM {$wpdb->prefix}wc_product_attributes_lookup
					WHERE `taxonomy` = %s
					AND `term_id` IN ({$term_ids})
					GROUP BY `product_or_parent_id`
					HAVING COUNT( DISTINCT `term_id` ) >= %d
					",
					$taxonomy,
					$term_count
				)
			);
			// phpcs:enable
		}

		return $results;
	}
}
