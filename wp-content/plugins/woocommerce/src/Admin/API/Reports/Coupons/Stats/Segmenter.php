<?php
/**
 * Class for adding segmenting support to coupons/stats without cluttering the data store.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Coupons\Stats;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Segmenter as ReportsSegmenter;
use Automattic\WooCommerce\Admin\API\Reports\ParameterException;

/**
 * Date & time interval and numeric range handling class for Reporting API.
 */
class Segmenter extends ReportsSegmenter {

	/**
	 * Returns column => query mapping to be used for product-related product-level segmenting query
	 * (e.g. coupon discount amount for product X when segmenting by product id or category).
	 *
	 * @param string $products_table Name of SQL table containing the product-level segmenting info.
	 *
	 * @return array Column => SELECT query mapping.
	 */
	protected function get_segment_selections_product_level( $products_table ) {
		$columns_mapping = array(
			'amount' => "SUM($products_table.coupon_amount) as amount",
		);

		return $columns_mapping;
	}

	/**
	 * Returns column => query mapping to be used for order-related product-level segmenting query
	 * (e.g. orders_count when segmented by category).
	 *
	 * @param string $coupons_lookup_table Name of SQL table containing the order-level segmenting info.
	 *
	 * @return array Column => SELECT query mapping.
	 */
	protected function get_segment_selections_order_level( $coupons_lookup_table ) {
		$columns_mapping = array(
			'coupons_count' => "COUNT(DISTINCT $coupons_lookup_table.coupon_id) as coupons_count",
			'orders_count'  => "COUNT(DISTINCT $coupons_lookup_table.order_id) as orders_count",
		);

		return $columns_mapping;
	}

	/**
	 * Returns column => query mapping to be used for order-level segmenting query
	 * (e.g. discount amount when segmented by coupons).
	 *
	 * @param string $coupons_lookup_table Name of SQL table containing the order-level info.
	 * @param array  $overrides Array of overrides for default column calculations.
	 *
	 * @return array Column => SELECT query mapping.
	 */
	protected function segment_selections_orders( $coupons_lookup_table, $overrides = array() ) {
		$columns_mapping = array(
			'amount'        => "SUM($coupons_lookup_table.discount_amount) as amount",
			'coupons_count' => "COUNT(DISTINCT $coupons_lookup_table.coupon_id) as coupons_count",
			'orders_count'  => "COUNT(DISTINCT $coupons_lookup_table.order_id) as orders_count",
		);

		if ( $overrides ) {
			$columns_mapping = array_merge( $columns_mapping, $overrides );
		}

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

		// Product-level numbers and order-level numbers can be fetched by the same query.
		$segments_products = $wpdb->get_results(
			"SELECT
						$segmenting_groupby AS $segmenting_dimension_name
						{$segmenting_selections['product_level']}
						{$segmenting_selections['order_level']}
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
		); // WPCS: cache ok, DB call ok, unprepared SQL ok.

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

		// LIMIT offset, rowcount needs to be updated to LIMIT offset, rowcount * max number of segments.
		$limit_parts      = explode( ',', $intervals_query['limit'] );
		$orig_rowcount    = intval( $limit_parts[1] );
		$segmenting_limit = $limit_parts[0] . ',' . $orig_rowcount * count( $this->get_all_segments() );

		// Product-level numbers and order-level numbers can be fetched by the same query.
		$segments_products = $wpdb->get_results(
			"SELECT
						{$intervals_query['select_clause']} AS time_interval,
						$segmenting_groupby AS $segmenting_dimension_name
						{$segmenting_selections['product_level']}
						{$segmenting_selections['order_level']}
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
			ARRAY_A
		); // WPCS: cache ok, DB call ok, unprepared SQL ok.

		$intervals_segments = $this->merge_segment_intervals_results( $segmenting_dimension_name, $segments_products, array() );
		return $intervals_segments;
	}

	/**
	 * Calculate segments for totals query where the segmenting property is bound to order (e.g. coupon or customer type).
	 *
	 * @param string $segmenting_select SELECT part of segmenting SQL query.
	 * @param string $segmenting_from FROM part of segmenting SQL query.
	 * @param string $segmenting_where WHERE part of segmenting SQL query.
	 * @param string $segmenting_groupby GROUP BY part of segmenting SQL query.
	 * @param string $table_name Name of SQL table which is the stats table for orders.
	 * @param array  $totals_query Array of SQL clauses for intervals query.
	 *
	 * @return array
	 */
	protected function get_order_related_totals_segments( $segmenting_select, $segmenting_from, $segmenting_where, $segmenting_groupby, $table_name, $totals_query ) {
		global $wpdb;

		$totals_segments = $wpdb->get_results(
			"SELECT
						$segmenting_groupby
						$segmenting_select
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
		); // WPCS: cache ok, DB call ok, unprepared SQL ok.

		// Reformat result.
		$totals_segments = $this->reformat_totals_segments( $totals_segments, $segmenting_groupby );
		return $totals_segments;
	}

	/**
	 * Calculate segments for intervals query where the segmenting property is bound to order (e.g. coupon or customer type).
	 *
	 * @param string $segmenting_select SELECT part of segmenting SQL query.
	 * @param string $segmenting_from FROM part of segmenting SQL query.
	 * @param string $segmenting_where WHERE part of segmenting SQL query.
	 * @param string $segmenting_groupby GROUP BY part of segmenting SQL query.
	 * @param string $table_name Name of SQL table which is the stats table for orders.
	 * @param array  $intervals_query Array of SQL clauses for intervals query.
	 *
	 * @return array
	 */
	protected function get_order_related_intervals_segments( $segmenting_select, $segmenting_from, $segmenting_where, $segmenting_groupby, $table_name, $intervals_query ) {
		global $wpdb;
		$limit_parts      = explode( ',', $intervals_query['limit'] );
		$orig_rowcount    = intval( $limit_parts[1] );
		$segmenting_limit = $limit_parts[0] . ',' . $orig_rowcount * count( $this->get_all_segments() );

		$intervals_segments = $wpdb->get_results(
			"SELECT
						MAX($table_name.date_created) AS datetime_anchor,
						{$intervals_query['select_clause']} AS time_interval,
						$segmenting_groupby
						$segmenting_select
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
			ARRAY_A
		); // WPCS: cache ok, DB call ok, unprepared SQL ok.

		// Reformat result.
		$intervals_segments = $this->reformat_intervals_segments( $intervals_segments, $segmenting_groupby );
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
		$unique_orders_table      = '';
		$segmenting_where         = '';

		// Product, variation, and category are bound to product, so here product segmenting table is required,
		// while coupon and customer are bound to order, so we don't need the extra JOIN for those.
		// This also means that segment selections need to be calculated differently.
		if ( 'product' === $this->query_args['segmentby'] ) {
			$product_level_columns     = $this->get_segment_selections_product_level( $product_segmenting_table );
			$order_level_columns       = $this->get_segment_selections_order_level( $table_name );
			$segmenting_selections     = array(
				'product_level' => $this->prepare_selections( $product_level_columns ),
				'order_level'   => $this->prepare_selections( $order_level_columns ),
			);
			$this->report_columns      = array_merge( $product_level_columns, $order_level_columns );
			$segmenting_from           = "INNER JOIN $product_segmenting_table ON ($table_name.order_id = $product_segmenting_table.order_id)";
			$segmenting_groupby        = $product_segmenting_table . '.product_id';
			$segmenting_dimension_name = 'product_id';

			$segments = $this->get_product_related_segments( $type, $segmenting_selections, $segmenting_from, $segmenting_where, $segmenting_groupby, $segmenting_dimension_name, $table_name, $query_params, $unique_orders_table );
		} elseif ( 'variation' === $this->query_args['segmentby'] ) {
			if ( ! isset( $this->query_args['product_includes'] ) || count( $this->query_args['product_includes'] ) !== 1 ) {
				throw new ParameterException( 'wc_admin_reports_invalid_segmenting_variation', __( 'product_includes parameter need to specify exactly one product when segmenting by variation.', 'woocommerce' ) );
			}

			$product_level_columns     = $this->get_segment_selections_product_level( $product_segmenting_table );
			$order_level_columns       = $this->get_segment_selections_order_level( $table_name );
			$segmenting_selections     = array(
				'product_level' => $this->prepare_selections( $product_level_columns ),
				'order_level'   => $this->prepare_selections( $order_level_columns ),
			);
			$this->report_columns      = array_merge( $product_level_columns, $order_level_columns );
			$segmenting_from           = "INNER JOIN $product_segmenting_table ON ($table_name.order_id = $product_segmenting_table.order_id)";
			$segmenting_where          = "AND $product_segmenting_table.product_id = {$this->query_args['product_includes'][0]}";
			$segmenting_groupby        = $product_segmenting_table . '.variation_id';
			$segmenting_dimension_name = 'variation_id';

			$segments = $this->get_product_related_segments( $type, $segmenting_selections, $segmenting_from, $segmenting_where, $segmenting_groupby, $segmenting_dimension_name, $table_name, $query_params, $unique_orders_table );
		} elseif ( 'category' === $this->query_args['segmentby'] ) {
			$product_level_columns     = $this->get_segment_selections_product_level( $product_segmenting_table );
			$order_level_columns       = $this->get_segment_selections_order_level( $table_name );
			$segmenting_selections     = array(
				'product_level' => $this->prepare_selections( $product_level_columns ),
				'order_level'   => $this->prepare_selections( $order_level_columns ),
			);
			$this->report_columns      = array_merge( $product_level_columns, $order_level_columns );
			$segmenting_from           = "
			INNER JOIN $product_segmenting_table ON ($table_name.order_id = $product_segmenting_table.order_id)
			LEFT JOIN {$wpdb->term_relationships} ON {$product_segmenting_table}.product_id = {$wpdb->term_relationships}.object_id
			JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
			LEFT JOIN {$wpdb->wc_category_lookup} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->wc_category_lookup}.category_id
			";
			$segmenting_where          = " AND {$wpdb->wc_category_lookup}.category_tree_id IS NOT NULL";
			$segmenting_groupby        = "{$wpdb->wc_category_lookup}.category_tree_id";
			$segmenting_dimension_name = 'category_id';

			$segments = $this->get_product_related_segments( $type, $segmenting_selections, $segmenting_from, $segmenting_where, $segmenting_groupby, $segmenting_dimension_name, $table_name, $query_params, $unique_orders_table );
		} elseif ( 'coupon' === $this->query_args['segmentby'] ) {
			$coupon_level_columns  = $this->segment_selections_orders( $table_name );
			$segmenting_selections = $this->prepare_selections( $coupon_level_columns );
			$this->report_columns  = $coupon_level_columns;
			$segmenting_from       = '';
			$segmenting_groupby    = "$table_name.coupon_id";

			$segments = $this->get_order_related_segments( $type, $segmenting_selections, $segmenting_from, $segmenting_where, $segmenting_groupby, $table_name, $query_params );
		}

		return $segments;
	}
}
