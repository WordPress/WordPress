<?php
/**
 * Class WC_Order_Item_Data_Store file.
 *
 * @package WooCommerce\DataStores
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Order Item Data Store: Misc Order Item Data functions.
 *
 * @version  3.0.0
 */
class WC_Order_Item_Data_Store implements WC_Order_Item_Data_Store_Interface {

	/**
	 * Add an order item to an order.
	 *
	 * @since  3.0.0
	 * @param  int   $order_id Order ID.
	 * @param  array $item order_item_name and order_item_type.
	 * @return int Order Item ID
	 */
	public function add_order_item( $order_id, $item ) {
		global $wpdb;
		$wpdb->insert(
			$wpdb->prefix . 'woocommerce_order_items',
			array(
				'order_item_name' => $item['order_item_name'],
				'order_item_type' => $item['order_item_type'],
				'order_id'        => $order_id,
			),
			array(
				'%s',
				'%s',
				'%d',
			)
		);

		$item_id = absint( $wpdb->insert_id );

		$this->clear_caches( $item_id, $order_id );

		return $item_id;
	}

	/**
	 * Update an order item.
	 *
	 * @since  3.0.0
	 * @param  int   $item_id Item ID.
	 * @param  array $item order_item_name or order_item_type.
	 * @return boolean
	 */
	public function update_order_item( $item_id, $item ) {
		global $wpdb;
		$updated = $wpdb->update( $wpdb->prefix . 'woocommerce_order_items', $item, array( 'order_item_id' => $item_id ) );
		$this->clear_caches( $item_id, null );
		return $updated;
	}

	/**
	 * Delete an order item.
	 *
	 * @since  3.0.0
	 * @param  int $item_id Item ID.
	 */
	public function delete_order_item( $item_id ) {
		// Load the order ID before the deletion, since after, it won't exist in the database.
		$order_id = $this->get_order_id_by_order_item_id( $item_id );

		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d", $item_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = %d", $item_id ) );

		$this->clear_caches( $item_id, $order_id );
	}

	/**
	 * Update term meta.
	 *
	 * @since  3.0.0
	 * @param  int    $item_id Item ID.
	 * @param  string $meta_key Meta key.
	 * @param  mixed  $meta_value Meta value.
	 * @param  string $prev_value (default: '').
	 * @return bool
	 */
	public function update_metadata( $item_id, $meta_key, $meta_value, $prev_value = '' ) {
		return update_metadata( 'order_item', $item_id, $meta_key, is_string( $meta_value ) ? wp_slash( $meta_value ) : $meta_value, $prev_value );
	}

	/**
	 * Add term meta.
	 *
	 * @since  3.0.0
	 * @param  int    $item_id Item ID.
	 * @param  string $meta_key Meta key.
	 * @param  mixed  $meta_value Meta value.
	 * @param  bool   $unique (default: false).
	 * @return int    New row ID or 0
	 */
	public function add_metadata( $item_id, $meta_key, $meta_value, $unique = false ) {
		return add_metadata( 'order_item', $item_id, wp_slash( $meta_key ), is_string( $meta_value ) ? wp_slash( $meta_value ) : $meta_value, $unique );
	}

	/**
	 * Delete term meta.
	 *
	 * @since  3.0.0
	 * @param  int    $item_id Item ID.
	 * @param  string $meta_key Meta key.
	 * @param  mixed  $meta_value (default: '').
	 * @param  bool   $delete_all (default: false).
	 * @return bool
	 */
	public function delete_metadata( $item_id, $meta_key, $meta_value = '', $delete_all = false ) {
		return delete_metadata( 'order_item', $item_id, $meta_key, is_string( $meta_value ) ? wp_slash( $meta_value ) : $meta_value, $delete_all );
	}

	/**
	 * Get term meta.
	 *
	 * @since  3.0.0
	 * @param  int    $item_id Item ID.
	 * @param  string $key Meta key.
	 * @param  bool   $single (default: true).
	 * @return mixed
	 */
	public function get_metadata( $item_id, $key, $single = true ) {
		return get_metadata( 'order_item', $item_id, $key, $single );
	}

	/**
	 * Get order ID by order item ID.
	 *
	 * @since 3.0.0
	 * @param  int $item_id Item ID.
	 * @return int
	 */
	public function get_order_id_by_order_item_id( $item_id ) {
		global $wpdb;
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d",
				$item_id
			)
		);
	}

	/**
	 * Get the order item type based on Item ID.
	 *
	 * @since 3.0.0
	 * @param int $item_id Item ID.
	 * @return string|null Order item type or null if no order item entry found.
	 */
	public function get_order_item_type( $item_id ) {
		global $wpdb;
		$order_item_type = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT order_item_type FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d LIMIT 1;",
				$item_id
			)
		);

		return $order_item_type;
	}

	/**
	 * Clear meta cache.
	 *
	 * @param int      $item_id Item ID.
	 * @param int|null $order_id Order ID. If not set, it will be loaded using the item ID.
	 */
	protected function clear_caches( $item_id, $order_id ) {
		wp_cache_delete( 'item-' . $item_id, 'order-items' );

		if ( ! $order_id ) {
			$order_id = $this->get_order_id_by_order_item_id( $item_id );
		}
		if ( $order_id ) {
			wp_cache_delete( 'order-items-' . $order_id, 'orders' );
		}
	}
}
