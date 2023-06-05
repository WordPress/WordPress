<?php
/**
 * API\Reports\Stock\Stats\DataStore class file.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Stock\Stats;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\DataStore as ReportsDataStore;
use Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;

/**
 * API\Reports\Stock\Stats\DataStore.
 */
class DataStore extends ReportsDataStore implements DataStoreInterface {

	/**
	 * Get stock counts for the whole store.
	 *
	 * @param array $query Not used for the stock stats data store, but needed for the interface.
	 * @return array Array of counts.
	 */
	public function get_data( $query ) {
		$report_data              = array();
		$cache_expire             = DAY_IN_SECONDS * 30;
		$low_stock_transient_name = 'wc_admin_stock_count_lowstock';
		$low_stock_count          = get_transient( $low_stock_transient_name );
		if ( false === $low_stock_count ) {
			$low_stock_count = $this->get_low_stock_count();
			set_transient( $low_stock_transient_name, $low_stock_count, $cache_expire );
		} else {
			$low_stock_count = intval( $low_stock_count );
		}
		$report_data['lowstock'] = $low_stock_count;

		$status_options = wc_get_product_stock_status_options();
		foreach ( $status_options as $status => $label ) {
			$transient_name = 'wc_admin_stock_count_' . $status;
			$count          = get_transient( $transient_name );
			if ( false === $count ) {
				$count = $this->get_count( $status );
				set_transient( $transient_name, $count, $cache_expire );
			} else {
				$count = intval( $count );
			}
			$report_data[ $status ] = $count;
		}

		$product_count_transient_name = 'wc_admin_product_count';
		$product_count                = get_transient( $product_count_transient_name );
		if ( false === $product_count ) {
			$product_count = $this->get_product_count();
			set_transient( $product_count_transient_name, $product_count, $cache_expire );
		} else {
			$product_count = intval( $product_count );
		}
		$report_data['products'] = $product_count;
		return $report_data;
	}

	/**
	 * Get low stock count (products with stock < low stock amount, but greater than no stock amount).
	 *
	 * @return int Low stock count.
	 */
	private function get_low_stock_count() {
		global $wpdb;

		$no_stock_amount  = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );
		$low_stock_amount = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT count( DISTINCT posts.ID ) FROM {$wpdb->posts} posts
				LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON posts.ID = wc_product_meta_lookup.product_id
				LEFT JOIN {$wpdb->postmeta} low_stock_amount_meta ON posts.ID = low_stock_amount_meta.post_id AND low_stock_amount_meta.meta_key = '_low_stock_amount'
				WHERE posts.post_type IN ( 'product', 'product_variation' )
				AND wc_product_meta_lookup.stock_quantity IS NOT NULL
				AND wc_product_meta_lookup.stock_status = 'instock'
				AND (
					(
						low_stock_amount_meta.meta_value > ''
						AND wc_product_meta_lookup.stock_quantity <= CAST(low_stock_amount_meta.meta_value AS SIGNED)
						AND wc_product_meta_lookup.stock_quantity > %d
					)
					OR (
						(
							low_stock_amount_meta.meta_value IS NULL OR low_stock_amount_meta.meta_value <= ''
						)
						AND wc_product_meta_lookup.stock_quantity <= %d
						AND wc_product_meta_lookup.stock_quantity > %d
					)
				)
				",
				$no_stock_amount,
				$low_stock_amount,
				$no_stock_amount
			)
		);
	}

	/**
	 * Get count for the passed in stock status.
	 *
	 * @param  string $status Status slug.
	 * @return int Count.
	 */
	private function get_count( $status ) {
		global $wpdb;

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT count( DISTINCT posts.ID ) FROM {$wpdb->posts} posts
				LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON posts.ID = wc_product_meta_lookup.product_id
				WHERE posts.post_type IN ( 'product', 'product_variation' )
				AND wc_product_meta_lookup.stock_status = %s
				",
				$status
			)
		);
	}

	/**
	 * Get product count for the store.
	 *
	 * @return int Product count.
	 */
	private function get_product_count() {
		$query_args              = array();
		$query_args['post_type'] = array( 'product', 'product_variation' );
		$query                   = new \WP_Query();
		$query->query( $query_args );
		return intval( $query->found_posts );
	}
}
