<?php
/**
 * Class for adding segmenting support without cluttering the data stores.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Variations\Stats;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Segmenter as ReportsSegmenter;
use Automattic\WooCommerce\Admin\API\Reports\ParameterException;

/**
 * Date & time interval and numeric range handling class for Reporting API.
 */
class Segmenter extends ReportsSegmenter {

	/**
	 * Returns column => query mapping to be used for product-related product-level segmenting query
	 * (e.g. products sold, revenue from product X when segmenting by category).
	 *
	 * @param string $products_table Name of SQL table containing the product-level segmenting info.
	 *
	 * @return array Column => SELECT query mapping.
	 */
	protected function get_segment_selections_product_level( $products_table ) {
		$columns_mapping = array(
			'items_sold'       => "SUM($products_table.product_qty) as items_sold",
			'net_revenue'      => "SUM($products_table.product_net_revenue ) AS net_revenue",
			'orders_count'     => "COUNT( DISTINCT $products_table.order_id ) AS orders_count",
			'variations_count' => "COUNT( DISTINCT $products_table.variation_id ) AS variations_count",
		);

		return $columns_mapping;
	}

	/**
	 * Calculate segments for totals where the segmenting property is bound to product (e.g. category, product_id, variation_id).
	 *
	 * @param array  $segmenting_selections SELECT part of segmenting SQL query--one for 'product_level' and one for 'order_level'.
	 * @param string $segmenting_from FROM part of segmenting SQL query.
	 * @param string $segmenting_where WHERE part of segmenting SQL query.
	 * @param string $segmenting_groupby GROUP BY part of segmenting SQL query.
	 * @param string $segmenting_dimension_name Name of the segmenting dimension.
	 * @param string $table_name Name of SQL table which is the stats table for orders.
	 * @param array  $totals_query Array of SQL clauses for totals query.
	 * @param string $unique_orders_table Name of temporary SQL table that holds unique orders.
	 *
	 * @return array
	 */
	protected function get_product_related_totals_segments( $segmenting_selections, $segmenting_from, $segmenting_where, $segmenting_groupby, $segmenting_dimension_name, $table_name, $totals_query, $unique_orders_table ) {
		global $wpdb;

		$product_segmenting_table = $wpdb->prefix . 'wc_order_product_lookup';

		// Can't get all the numbers from one query, so split it into one query for product-level numbers and one for order-level numbers (which first need to have orders uniqued).
		// Product-level numbers.
		/* phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared */
		$segments_products = $wpdb->get_results(
			"SELECT
						$segmenting_groupby AS $segmenting_dimension_name
						{$segmenting_selections['product_level']}
					FROM
						$table_name
						$segmenting_from
						{$totals_query['from_clause']}
					WHERE
						1=1
						{$totals_query['where_time_clause']}
						{$totals_query['where_clause']}
						$segmenting_where
					GROUP BY
						$segmenting_groupby",
			ARRAY_A
		);
		/* phpcs:enable */

		$totals_segments = $this->merge_segment_totals_results( $segmenting_dimension_name, $segments_products, array() );
		return $totals_segments;
	}

	/**
	 * Calculate segments for intervals where the segmenting property is bound to product (e.g. category, product_id, variation_id).
	 *
	 * @param array  $segmenting_selections SELECT part of segmenting SQL query--one for 'product_level' and one for 'order_level'.
	 * @param string $segmenting_from FROM part of segmenting SQL query.
	 * @param string $segmenting_where WHERE part of segmenting SQL query.
	 * @param string $segmenting_groupby GROUP BY part of segmenting SQL query.
	 * @param string $segmenting_dimension_name Name of the segmenting dimension.
	 * @param string $table_name Name of SQL table which is the stats table for orders.
	 * @param array  $intervals_query Array of SQL clauses for intervals query.
	 * @param string $unique_orders_table Name of temporary SQL table that holds unique orders.
	 *
	 * @return array
	 */
	protected function get_product_related_intervals_segments( $segmenting_selections, $segmenting_from, $segmenting_where, $segmenting_groupby, $segmenting_dimension_name, $table_name, $intervals_query, $unique_orders_table ) {
		global $wpdb;

		$product_segmenting_table = $wpdb->prefix . 'wc_order_product_lookup';

		// LIMIT offset, rowcount needs to be updated to a multiple of the number of segments.
		preg_match( '/LIMIT (\d+)\s?,\s?(\d+)/', $intervals_query['limit'], $limit_parts );
		$segment_count    = count( $this->get_all_segments() );
		$orig_offset      = intval( $limit_parts[1] );
		$orig_rowcount    = intval( $limit_parts[2] );
		$segmenting_limit = $wpdb->prepare( 'LIMIT %d, %d', $orig_offset * $segment_count, $orig_rowcount * $segment_count );

		// Can't get all the numbers from one query, so split it into one query for product-level numbers and one for order-level numbers (which first need to have orders uniqued).
		// Product-level numbers.
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$segments_products = $wpdb->get_results(
			"SELECT
						{$intervals_query['select_clause']} AS time_interval,
						$segmenting_groupby AS $segmenting_dimension_name
						{$segmenting_selections['product_level']}
					FROM
						$table_name
						$segmenting_from
						{$intervals_query['from_clause']}
					WHERE
						1=1
						{$intervals_query['where_time_clause']}
						{$intervals_query['where_clause']}
						$segmenting_where
					GROUP BY
						time_interval, $segmenting_groupby
					$segmenting_limit",
				// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			ARRAY_A
		);

		$intervals_segments = $this->merge_segment_intervals_results( $segmenting_dimension_name, $segments_products, array() );
		return $intervals_segments;
	}

	/**
	 * Return array of segments formatted for REST response.
	 *
	 * @param string $type Type of segments to return--'totals' or 'intervals'.
	 * @param array  $query_params SQL query parameter array.
	 * @param string $table_name Name of main SQL table for the data store (used as basis for JOINS).
	 *
	 * @return array
	 * @throws \Automattic\WooCommerce\Admin\API\Reports\ParameterException In case of segmenting by variations, when no parent product is specified.
	 */
	protected function get_segments( $type, $query_params, $table_name ) {
		global $wpdb;
		if ( ! isset( $this->query_args['segmentby'] ) || '' === $this->query_args['segmentby'] ) {
			return array();
		}

		$product_segmenting_table = $wpdb->prefix . 'wc_order_product_lookup';
		$unique_orders_table      = 'uniq_orders';
		$segmenting_where         = '';

		// Product, variation, and category are bound to product, so here product segmenting table is required,
		// while coupon and customer are bound to order, so we don't need the extra JOIN for those.
		// This also means that segment selections need to be calculated differently.
		if ( 'variation' === $this->query_args['segmentby'] ) {
			$product_level_columns     = $this->get_segment_selections_product_level( $product_segmenting_table );
			$segmenting_selections     = array(
				'product_level' => $this->prepare_selections( $product_level_columns ),
			);
			$this->report_columns      = $product_level_columns;
			$segmenting_from           = '';
			$segmenting_groupby        = $product_segmenting_table . '.variation_id';
			$segmenting_dimension_name = 'variation_id';

			// Restrict our search space for variation comparisons.
			if ( isset( $this->query_args['variation_includes'] ) ) {
				$variation_ids    = implode( ',', $this->get_all_segments() );
				$segmenting_where = " AND $product_segmenting_table.variation_id IN ( $variation_ids )";
			}

			$segments = $this->get_product_related_segments( $type, $segmenting_selections, $segmenting_from, $segmenting_where, $segmenting_groupby, $segmenting_dimension_name, $table_name, $query_params, $unique_orders_table );
		}

		return $segments;
	}
}
