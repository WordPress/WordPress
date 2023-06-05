<?php
/**
 * Order Item Type Data Store Interface
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
interface WC_Order_Item_Type_Data_Store_Interface {
	/**
	 * Saves an item's data to the database / item meta.
	 * Ran after both create and update, so $item->get_id() will be set.
	 *
	 * @param WC_Order_Item $item Item object.
	 */
	public function save_item_data( &$item );
}
