<?php
/**
 * Class for adding segmenting support without cluttering the data stores.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Taxes\Stats;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Segmenter as ReportsSegmenter;

/**
 * Date & time interval and numeric range handling class for Reporting API.
 */
class Segmenter extends ReportsSegmenter {

	/**
	 * Returns column => query mapping to be used for order-related order-level segmenting query (e.g. tax_rate_id).
	 *
	 * @param string $lookup_table Name of SQL table containing the order-level segmenting info.
	 *
	 * @return array Column => SELECT query mapping.
	 */
	protected function get_segment_selections_order_level( $lookup_table ) {
		$columns_mapping = array(
			'tax_codes'    => "COUNT(DISTINCT $lookup_table.tax_rate_id) as tax_codes",
			'total_tax'    => "SUM($lookup_table.total_tax) AS total_tax",
			'order_tax'    => "SUM($lookup_table.order_tax) as order_tax",
			'shipping_tax' => "SUM($lookup_table.shipping_tax) as shipping_tax",
			'orders_count' => "COUNT(DISTINCT $lookup_table.order_id) as orders_count",
		);

		return $columns_mapping;
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
		$segmenting_limit = '';
		$limit_parts      = explode( ',', $intervals_query['limit'] );
		if ( 2 === count( $limit_parts ) ) {
			$orig_rowcount    = intval( $limit_parts[1] );
			$segmenting_limit = $limit_parts[0] . ',' . $orig_rowcount * count( $this->get_all_segments() );
		}

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
		if ( ! isset( $this->query_args['segmentby'] ) || '' === $this->query_args['segmentby'] ) {
			return array();
		}

		$segmenting_where = '';
		$segmenting_from  = '';
		$segments         = array();

		if ( 'tax_rate_id' === $this->query_args['segmentby'] ) {
			$tax_rate_level_columns = $this->get_segment_selections_order_level( $table_name );
			$segmenting_select      = $this->prepare_selections( $tax_rate_level_columns );
			$this->report_columns   = $tax_rate_level_columns;
			$segmenting_groupby     = $table_name . '.tax_rate_id';

			$segments = $this->get_order_related_segments( $type, $segmenting_select, $segmenting_from, $segmenting_where, $segmenting_groupby, $table_name, $query_params );
		}

		return $segments;
	}
}
