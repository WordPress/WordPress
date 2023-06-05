<?php
/**
 * Order Item Product Data Store Interface
 *
 * @version 3.0.0
 * @package WooCommerce\Interface
 */

/**
 * WC Order Item Data Store Interface
 *
 * Functions that must be defined by order item store classes.
 *
 * @version  3.0.0
 */
interface WC_Order_Item_Product_Data_Store_Interface {
	/**
	 * Get a list of download IDs for a specific item from an order.
	 *
	 * @param WC_Order_Item $item Item object.
	 * @param WC_Order      $order Order object.
	 * @return array
	 */
	public function get_download_ids( $item, $order );
}
