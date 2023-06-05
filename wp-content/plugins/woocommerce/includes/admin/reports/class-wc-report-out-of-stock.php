<?php
/**
 * WC_Report_Out_Of_Stock.
 *
 * @package WooCommerce\Admin\Reports
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Report_Stock' ) ) {
	require_once dirname( __FILE__ ) . '/class-wc-report-stock.php';
}

/**
 * WC_Report_Out_Of_Stock class.
 */
class WC_Report_Out_Of_Stock extends WC_Report_Stock {

	/**
	 * No items found text.
	 */
	public function no_items() {
		esc_html_e( 'No out of stock products found.', 'woocommerce' );
	}

	/**
	 * Get Products matching stock criteria.
	 *
	 * @param int $current_page Current page number.
	 * @param int $per_page How many results to show per page.
	 */
	public function get_items( $current_page, $per_page ) {
		global $wpdb;

		$this->max_items = 0;
		$this->items     = array();

		$stock = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );

		$query_from = apply_filters(
			'woocommerce_report_out_of_stock_query_from',
			$wpdb->prepare(
				"
				FROM {$wpdb->posts} as posts
				INNER JOIN {$wpdb->wc_product_meta_lookup} AS lookup ON posts.ID = lookup.product_id
				WHERE 1=1
				AND posts.post_type IN ( 'product', 'product_variation' )
				AND posts.post_status = 'publish'
				AND lookup.stock_quantity <= %d
				",
				$stock
			)
		);

		$this->items     = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS posts.ID as id, posts.post_parent as parent {$query_from} ORDER BY posts.post_title DESC LIMIT %d, %d;", ( $current_page - 1 ) * $per_page, $per_page ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$this->max_items = $wpdb->get_var( 'SELECT FOUND_ROWS();' ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}
}
