<?php
/**
 * Utility functions meant for helping in migration from posts tables to custom order tables.
 */

namespace Automattic\WooCommerce\Internal\Utilities;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Internal\DataStores\Orders\{ DataSynchronizer, OrdersTableDataStore };
use WC_Order;
use WP_Post;

/**
 * Utility functions meant for helping in migration from posts tables to custom order tables.
 */
class COTMigrationUtil {

	/**
	 * Custom order table controller.
	 *
	 * @var CustomOrdersTableController
	 */
	private $table_controller;

	/**
	 * Data synchronizer.
	 *
	 * @var DataSynchronizer
	 */
	private $data_synchronizer;

	/**
	 * Initialize method, invoked by the DI container.
	 *
	 * @internal Automatically called by the container.
	 * @param CustomOrdersTableController $table_controller Custom order table controller.
	 * @param DataSynchronizer            $data_synchronizer Data synchronizer.
	 *
	 * @return void
	 */
	final public function init( CustomOrdersTableController $table_controller, DataSynchronizer $data_synchronizer ) {
		$this->table_controller  = $table_controller;
		$this->data_synchronizer = $data_synchronizer;
	}

	/**
	 * Helper function to get screen name of orders page in wp-admin.
	 *
	 * @throws \Exception If called from outside of wp-admin.
	 *
	 * @return string
	 */
	public function get_order_admin_screen() : string {
		if ( ! is_admin() ) {
			throw new \Exception( 'This function should only be called in admin.' );
		}
		return $this->custom_orders_table_usage_is_enabled() && function_exists( 'wc_get_page_screen_id' )
			? wc_get_page_screen_id( 'shop-order' )
			: 'shop_order';
	}

	/**
	 * Helper function to get whether custom order tables are enabled or not.
	 *
	 * @return bool
	 */
	private function custom_orders_table_usage_is_enabled() : bool {
		return $this->table_controller->custom_orders_table_usage_is_enabled();
	}

	/**
	 * Checks if posts and order custom table sync is enabled and there are no pending orders.
	 *
	 * @return bool
	 */
	public function is_custom_order_tables_in_sync() : bool {
		$sync_status = $this->data_synchronizer->get_sync_status();
		return 0 === $sync_status['current_pending_count'] && $this->data_synchronizer->data_sync_is_enabled();
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
	public function get_post_or_object_meta( ?WP_Post $post, ?\WC_Data $data, string $key, bool $single ) {
		if ( isset( $data ) ) {
			if ( method_exists( $data, "get$key" ) ) {
				return $data->{"get$key"}();
			}
			return $data->get_meta( $key, $single );
		} else {
			return isset( $post->ID ) ? get_post_meta( $post->ID, $key, $single ) : false;
		}
	}

	/**
	 * Helper function to initialize the global $theorder object, mostly used during order meta boxes rendering.
	 *
	 * @param WC_Order|WP_Post $post_or_order_object Post or order object.
	 *
	 * @return bool|WC_Order|WC_Order_Refund WC_Order object.
	 */
	public function init_theorder_object( $post_or_order_object ) {
		global $theorder;
		if ( $theorder instanceof WC_Order ) {
			return $theorder;
		}

		if ( $post_or_order_object instanceof WC_Order ) {
			$theorder = $post_or_order_object;
		} else {
			$theorder = wc_get_order( $post_or_order_object->ID );
		}
		return $theorder;
	}

	/**
	 * Helper function to get ID from a post or order object.
	 *
	 * @param WP_Post/WC_Order $post_or_order_object WP_Post/WC_Order object to get ID for.
	 *
	 * @return int Order or post ID.
	 */
	public function get_post_or_order_id( $post_or_order_object ) : int {
		if ( is_numeric( $post_or_order_object ) ) {
			return (int) $post_or_order_object;
		} elseif ( $post_or_order_object instanceof WC_Order ) {
			return $post_or_order_object->get_id();
		} elseif ( $post_or_order_object instanceof WP_Post ) {
			return $post_or_order_object->ID;
		}
		return 0;
	}

	/**
	 * Checks if passed id, post or order object is a WC_Order object.
	 *
	 * @param int|WP_Post|WC_Order $order_id Order ID, post object or order object.
	 * @param string[]             $types    Types to match against.
	 *
	 * @return bool Whether the passed param is an order.
	 */
	public function is_order( $order_id, array $types = array( 'shop_order' ) ) : bool {
		$order_id         = $this->get_post_or_order_id( $order_id );
		$order_data_store = \WC_Data_Store::load( 'order' );
		return in_array( $order_data_store->get_order_type( $order_id ), $types, true );
	}

	/**
	 * Returns type pf passed id, post or order object.
	 *
	 * @param int|WP_Post|WC_Order $order_id Order ID, post object or order object.
	 *
	 * @return string|null Type of the order.
	 */
	public function get_order_type( $order_id ) {
		$order_id         = $this->get_post_or_order_id( $order_id );
		$order_data_store = \WC_Data_Store::load( 'order' );
		return $order_data_store->get_order_type( $order_id );
	}

	/**
	 * Get the name of the database table that's currently in use for orders.
	 *
	 * @return string
	 */
	public function get_table_for_orders() {
		if ( $this->custom_orders_table_usage_is_enabled() ) {
			$table_name = OrdersTableDataStore::get_orders_table_name();
		} else {
			global $wpdb;
			$table_name = $wpdb->posts;
		}

		return $table_name;
	}

	/**
	 * Get the name of the database table that's currently in use for orders.
	 *
	 * @return string
	 */
	public function get_table_for_order_meta() {
		if ( $this->custom_orders_table_usage_is_enabled() ) {
			$table_name = OrdersTableDataStore::get_meta_table_name();
		} else {
			global $wpdb;
			$table_name = $wpdb->postmeta;
		}

		return $table_name;
	}
}
