<?php
/**
 * Class WC_Order_Item_Coupon_Data_Store file.
 *
 * @package WooCommerce\DataStores
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Order Item Coupon Data Store
 *
 * @version  3.0.0
 */
class WC_Order_Item_Coupon_Data_Store extends Abstract_WC_Order_Item_Type_Data_Store implements WC_Object_Data_Store_Interface, WC_Order_Item_Type_Data_Store_Interface {

	/**
	 * Data stored in meta keys.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $internal_meta_keys = array( 'discount_amount', 'discount_amount_tax' );

	/**
	 * Read/populate data properties specific to this order item.
	 *
	 * @since 3.0.0
	 * @param WC_Order_Item_Coupon $item Coupon order item.
	 */
	public function read( &$item ) {
		parent::read( $item );
		$id = $item->get_id();
		$item->set_props(
			array(
				'discount'     => get_metadata( 'order_item', $id, 'discount_amount', true ),
				'discount_tax' => get_metadata( 'order_item', $id, 'discount_amount_tax', true ),
			)
		);
		$item->set_object_read( true );
	}

	/**
	 * Saves an item's data to the database / item meta.
	 * Ran after both create and update, so $item->get_id() will be set.
	 *
	 * @since 3.0.0
	 * @param WC_Order_Item_Coupon $item Coupon order item.
	 */
	public function save_item_data( &$item ) {
		$id          = $item->get_id();
		$save_values = array(
			'discount_amount'     => $item->get_discount( 'edit' ),
			'discount_amount_tax' => $item->get_discount_tax( 'edit' ),
		);
		foreach ( $save_values as $key => $value ) {
			update_metadata( 'order_item', $id, $key, $value );
		}
	}
}
