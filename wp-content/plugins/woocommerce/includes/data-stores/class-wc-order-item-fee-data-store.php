<?php
/**
 * Class WC_Order_Item_Fee_Data_Store file.
 *
 * @package WooCommerce\DataStores
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Order Item Fee Data Store
 *
 * @version  3.0.0
 */
class WC_Order_Item_Fee_Data_Store extends Abstract_WC_Order_Item_Type_Data_Store implements WC_Object_Data_Store_Interface, WC_Order_Item_Type_Data_Store_Interface {

	/**
	 * Data stored in meta keys.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $internal_meta_keys = array( '_fee_amount', '_tax_class', '_tax_status', '_line_subtotal', '_line_subtotal_tax', '_line_total', '_line_tax', '_line_tax_data' );

	/**
	 * Read/populate data properties specific to this order item.
	 *
	 * @since 3.0.0
	 * @param WC_Order_Item_Fee $item Fee order item object.
	 */
	public function read( &$item ) {
		parent::read( $item );
		$id = $item->get_id();
		$item->set_props(
			array(
				'amount'     => get_metadata( 'order_item', $id, '_fee_amount', true ),
				'tax_class'  => get_metadata( 'order_item', $id, '_tax_class', true ),
				'tax_status' => get_metadata( 'order_item', $id, '_tax_status', true ),
				'total'      => get_metadata( 'order_item', $id, '_line_total', true ),
				'taxes'      => get_metadata( 'order_item', $id, '_line_tax_data', true ),
			)
		);
		$item->set_object_read( true );
	}

	/**
	 * Saves an item's data to the database / item meta.
	 * Ran after both create and update, so $id will be set.
	 *
	 * @since 3.0.0
	 * @param WC_Order_Item_Fee $item Fee order item object.
	 */
	public function save_item_data( &$item ) {
		$id          = $item->get_id();
		$save_values = array(
			'_fee_amount'    => $item->get_amount( 'edit' ),
			'_tax_class'     => $item->get_tax_class( 'edit' ),
			'_tax_status'    => $item->get_tax_status( 'edit' ),
			'_line_total'    => $item->get_total( 'edit' ),
			'_line_tax'      => $item->get_total_tax( 'edit' ),
			'_line_tax_data' => $item->get_taxes( 'edit' ),
		);
		foreach ( $save_values as $key => $value ) {
			update_metadata( 'order_item', $id, $key, $value );
		}
	}
}
