<?php
/**
 * Class WC_Order_Item_Tax_Data_Store file.
 *
 * @package WooCommerce\DataStores
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Order Item Tax Data Store
 *
 * @version  3.0.0
 */
class WC_Order_Item_Tax_Data_Store extends Abstract_WC_Order_Item_Type_Data_Store implements WC_Object_Data_Store_Interface, WC_Order_Item_Type_Data_Store_Interface {

	/**
	 * Data stored in meta keys.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $internal_meta_keys = array( 'rate_id', 'label', 'compound', 'tax_amount', 'shipping_tax_amount', 'rate_percent' );

	/**
	 * Read/populate data properties specific to this order item.
	 *
	 * @since 3.0.0
	 * @param WC_Order_Item_Tax $item Tax order item object.
	 * @throws Exception If invalid order item.
	 */
	public function read( &$item ) {
		parent::read( $item );
		$id = $item->get_id();
		$item->set_props(
			array(
				'rate_id'            => get_metadata( 'order_item', $id, 'rate_id', true ),
				'label'              => get_metadata( 'order_item', $id, 'label', true ),
				'compound'           => get_metadata( 'order_item', $id, 'compound', true ),
				'tax_total'          => get_metadata( 'order_item', $id, 'tax_amount', true ),
				'shipping_tax_total' => get_metadata( 'order_item', $id, 'shipping_tax_amount', true ),
				'rate_percent'       => get_metadata( 'order_item', $id, 'rate_percent', true ),
			)
		);
		$item->set_object_read( true );
	}

	/**
	 * Saves an item's data to the database / item meta.
	 * Ran after both create and update, so $id will be set.
	 *
	 * @since 3.0.0
	 * @param WC_Order_Item_Tax $item Tax order item object.
	 */
	public function save_item_data( &$item ) {
		$id                = $item->get_id();
		$changes           = $item->get_changes();
		$meta_key_to_props = array(
			'rate_id'             => 'rate_id',
			'label'               => 'label',
			'compound'            => 'compound',
			'tax_amount'          => 'tax_total',
			'shipping_tax_amount' => 'shipping_tax_total',
			'rate_percent'        => 'rate_percent',
		);
		$props_to_update   = $this->get_props_to_update( $item, $meta_key_to_props, 'order_item' );

		foreach ( $props_to_update as $meta_key => $prop ) {
			update_metadata( 'order_item', $id, $meta_key, $item->{"get_$prop"}( 'edit' ) );
		}
	}
}
