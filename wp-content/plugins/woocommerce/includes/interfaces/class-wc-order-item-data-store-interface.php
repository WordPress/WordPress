<?php
/**
 * Order Item Data Store Interface
 *
 * @version 3.0.0
 * @package WooCommerce\Interface
 */

/**
 * WC Order Item Data Store Interface
 *
 * Functions that must be defined by the order item data store (for functions).
 *
 * @version  3.0.0
 */
interface WC_Order_Item_Data_Store_Interface {

	/**
	 * Add an order item to an order.
	 *
	 * @param  int   $order_id Order ID.
	 * @param  array $item order_item_name and order_item_type.
	 * @return int   Order Item ID
	 */
	public function add_order_item( $order_id, $item );

	/**
	 * Update an order item.
	 *
	 * @param  int   $item_id Item ID.
	 * @param  array $item order_item_name or order_item_type.
	 * @return boolean
	 */
	public function update_order_item( $item_id, $item );

	/**
	 * Delete an order item.
	 *
	 * @param int $item_id Item ID.
	 */
	public function delete_order_item( $item_id );

	/**
	 * Update term meta.
	 *
	 * @param  int    $item_id Item ID.
	 * @param  string $meta_key Meta key.
	 * @param  mixed  $meta_value Meta value.
	 * @param  string $prev_value Previous value (default: '').
	 * @return bool
	 */
	public function update_metadata( $item_id, $meta_key, $meta_value, $prev_value = '' );

	/**
	 * Add term meta.
	 *
	 * @param  int    $item_id Item ID.
	 * @param  string $meta_key Meta key.
	 * @param  mixed  $meta_value Meta value.
	 * @param  bool   $unique Unique? (default: false).
	 * @return int    New row ID or 0
	 */
	public function add_metadata( $item_id, $meta_key, $meta_value, $unique = false );


	/**
	 * Delete term meta.
	 *
	 * @param  int    $item_id Item ID.
	 * @param  string $meta_key Meta key.
	 * @param  mixed  $meta_value Meta value (default: '').
	 * @param  bool   $delete_all Delete all matching entries? (default: false).
	 * @return bool
	 */
	public function delete_metadata( $item_id, $meta_key, $meta_value = '', $delete_all = false );

	/**
	 * Get term meta.
	 *
	 * @param  int    $item_id Item ID.
	 * @param  string $key Meta key.
	 * @param  bool   $single Store as single value and not serialised (default: true).
	 * @return mixed
	 */
	public function get_metadata( $item_id, $key, $single = true );

	/**
	 * Get order ID by order item ID.
	 *
	 * @param  int $item_id Item ID.
	 * @return int
	 */
	public function get_order_id_by_order_item_id( $item_id );

	/**
	 * Get the order item type based on Item ID.
	 *
	 * @param  int $item_id Item ID.
	 * @return string
	 */
	public function get_order_item_type( $item_id );
}
