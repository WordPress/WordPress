<?php
/**
 * A class of utilities for dealing with orders.
 */

namespace Automattic\WooCommerce\Utilities;

use Automattic\WooCommerce\Caches\OrderCacheController;
use Automattic\WooCommerce\Internal\Admin\Orders\PageController;
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Internal\Features\FeaturesController;
use Automattic\WooCommerce\Internal\Utilities\COTMigrationUtil;
use WC_Order;
use WP_Post;

/**
 * A class of utilities for dealing with orders.
 */
final class OrderUtil {

	/**
	 * Helper function to get screen name of orders page in wp-admin.
	 *
	 * @return string
	 */
	public static function get_order_admin_screen() : string {
		return wc_get_container()->get( COTMigrationUtil::class )->get_order_admin_screen();
	}


	/**
	 * Helper function to get whether custom order tables are enabled or not.
	 *
	 * @return bool
	 */
	public static function custom_orders_table_usage_is_enabled() : bool {
		return wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled();
	}

	/**
	 * Helper function to get whether the orders cache should be used or not.
	 *
	 * @return bool True if the orders cache should be used, false otherwise.
	 */
	public static function orders_cache_usage_is_enabled() : bool {
		return wc_get_container()->get( OrderCacheController::class )->orders_cache_usage_is_enabled();
	}

	/**
	 * Checks if posts and order custom table sync is enabled and there are no pending orders.
	 *
	 * @return bool
	 */
	public static function is_custom_order_tables_in_sync() : bool {
		return wc_get_container()->get( COTMigrationUtil::class )->is_custom_order_tables_in_sync();
	}

	/**
	 * Gets value of a meta key from WC_Data object if passed, otherwise from the post object.
	 * This helper function support backward compatibility for meta box functions, when moving from posts based store to custom tables.
	 *
	 * @param WP_Post|null  $post Post object, meta will be fetched from this only when `$data` is not passed.
	 * @param \WC_Data|null $data WC_Data object, will be preferred over post object when passed.
	 * @param string        $key Key to fetch metadata for.
	 * @param bool          $single Whether metadata is single.
	 *
	 * @return array|mixed|string Value of the meta key.
	 */
	public static function get_post_or_object_meta( ?WP_Post $post, ?\WC_Data $data, string $key, bool $single ) {
		return wc_get_container()->get( COTMigrationUtil::class )->get_post_or_object_meta( $post, $data, $key, $single );
	}

	/**
	 * Helper function to initialize the global $theorder object, mostly used during order meta boxes rendering.
	 *
	 * @param WC_Order|WP_Post $post_or_order_object Post or order object.
	 *
	 * @return bool|WC_Order|WC_Order_Refund WC_Order object.
	 */
	public static function init_theorder_object( $post_or_order_object ) {
		return wc_get_container()->get( COTMigrationUtil::class )->init_theorder_object( $post_or_order_object );
	}

	/**
	 * Helper function to id from an post or order object.
	 *
	 * @param WP_Post/WC_Order $post_or_order_object WP_Post/WC_Order object to get ID for.
	 *
	 * @return int Order or post ID.
	 */
	public static function get_post_or_order_id( $post_or_order_object ) : int {
		return wc_get_container()->get( COTMigrationUtil::class )->get_post_or_order_id( $post_or_order_object );
	}

	/**
	 * Checks if passed id, post or order object is a WC_Order object.
	 *
	 * @param int|WP_Post|WC_Order $order_id Order ID, post object or order object.
	 * @param string[]             $types    Types to match against.
	 *
	 * @return bool Whether the passed param is an order.
	 */
	public static function is_order( $order_id, $types = array( 'shop_order' ) ) {
		return wc_get_container()->get( COTMigrationUtil::class )->is_order( $order_id, $types );
	}

	/**
	 * Returns type pf passed id, post or order object.
	 *
	 * @param int|WP_Post|WC_Order $order_id Order ID, post object or order object.
	 *
	 * @return string|null Type of the order.
	 */
	public static function get_order_type( $order_id ) {
		return wc_get_container()->get( COTMigrationUtil::class )->get_order_type( $order_id );
	}

	/**
	 * Helper method to generate admin url for an order.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return string Admin url for an order.
	 */
	public static function get_order_admin_edit_url( int $order_id ) : string {
		return wc_get_container()->get( PageController::class )->get_edit_url( $order_id );
	}

	/**
	 * Helper method to generate admin URL for new order.
	 *
	 * @return string Link for new order.
	 */
	public static function get_order_admin_new_url() : string {
		return wc_get_container()->get( PageController::class )->get_new_page_url();
	}

	/**
	 * Get the name of the database table that's currently in use for orders.
	 *
	 * @return string
	 */
	public static function get_table_for_orders() {
		return wc_get_container()->get( COTMigrationUtil::class )->get_table_for_orders();
	}

	/**
	 * Get the name of the database table that's currently in use for orders.
	 *
	 * @return string
	 */
	public static function get_table_for_order_meta() {
		return wc_get_container()->get( COTMigrationUtil::class )->get_table_for_order_meta();
	}
}
