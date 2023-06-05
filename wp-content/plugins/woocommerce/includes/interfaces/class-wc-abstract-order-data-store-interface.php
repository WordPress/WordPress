<?php
/**
 * Order Data Store Interface
 *
 * @version 3.0.0
 * @package WooCommerce\Interfaces
 */

/**
 * WC Order Data Store Interface
 *
 * Functions that must be defined by order store classes.
 *
 * @version  3.0.0
 */
interface WC_Abstract_Order_Data_Store_Interface {

	/**
	 * Read order items of a specific type from the database for this order.
	 *
	 * @param WC_Order $order Order object.
	 * @param string   $type Order item type.
	 * @return array
	 */
	public function read_items( $order, $type );

	/**
	 * Remove all line items (products, coupons, shipping, taxes) from the order.
	 *
	 * @param WC_Order $order Order object.
	 * @param string   $type Order item type. Default null.
	 */
	public function delete_items( $order, $type = null );

	/**
	 * Get token ids for an order.
	 *
	 * @param WC_Order $order Order object.
	 * @return array
	 */
	public function get_payment_token_ids( $order );

	/**
	 * Update token ids for an order.
	 *
	 * @param WC_Order $order Order object.
	 * @param array    $token_ids Token IDs.
	 */
	public function update_payment_token_ids( $order, $token_ids );
}
