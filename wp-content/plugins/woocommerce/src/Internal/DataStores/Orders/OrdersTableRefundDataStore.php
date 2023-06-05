<?php
/**
 * Order refund data store. Refunds are based on orders (essentially negative orders) but there is slight difference in how we save them.
 * For example, order save hooks etc can't be fired when saving refund, so we need to do it a separate datastore.
 */

namespace Automattic\WooCommerce\Internal\DataStores\Orders;

/**
 * Class OrdersTableRefundDataStore.
 */
class OrdersTableRefundDataStore extends OrdersTableDataStore {

	/**
	 * We do not have and use all the getters and setters from OrderTableDataStore, so we only select the props we actually need.
	 *
	 * @var \string[][]
	 */
	protected $operational_data_column_mapping = array(
		'id'                        => array( 'type' => 'int' ),
		'order_id'                  => array( 'type' => 'int' ),
		'woocommerce_version'       => array(
			'type' => 'string',
			'name' => 'version',
		),
		'prices_include_tax'        => array(
			'type' => 'bool',
			'name' => 'prices_include_tax',
		),
		'coupon_usages_are_counted' => array(
			'type' => 'bool',
			'name' => 'recorded_coupon_usage_counts',
		),
		'shipping_tax_amount'       => array(
			'type' => 'decimal',
			'name' => 'shipping_tax',
		),
		'shipping_total_amount'     => array(
			'type' => 'decimal',
			'name' => 'shipping_total',
		),
		'discount_tax_amount'       => array(
			'type' => 'decimal',
			'name' => 'discount_tax',
		),
		'discount_total_amount'     => array(
			'type' => 'decimal',
			'name' => 'discount_total',
		),
	);

	/**
	 * Delete a refund order from database.
	 *
	 * @param \WC_Order $refund Refund object to delete.
	 * @param array     $args Array of args to pass to the delete method.
	 *
	 * @return void
	 */
	public function delete( &$refund, $args = array() ) {
		$refund_id = $refund->get_id();
		if ( ! $refund_id ) {
			return;
		}

		$this->delete_order_data_from_custom_order_tables( $refund_id );
		$refund->set_id( 0 );

		// If this datastore method is called while the posts table is authoritative, refrain from deleting post data.
		if ( ! is_a( $refund->get_data_store(), self::class ) ) {
			return;
		}

		// Delete the associated post, which in turn deletes order items, etc. through {@see WC_Post_Data}.
		// Once we stop creating posts for orders, we should do the cleanup here instead.
		wp_delete_post( $refund_id );
	}

	/**
	 * Read a refund object from custom tables.
	 *
	 * @param \WC_Abstract_Order $refund Refund object.
	 *
	 * @return void
	 */
	public function read( &$refund ) {
		parent::read( $refund );
		$this->set_refund_props( $refund );
	}

	/**
	 * Read multiple refund objects from custom tables.
	 *
	 * @param \WC_Order $refunds Refund objects.
	 */
	public function read_multiple( &$refunds ) {
		parent::read_multiple( $refunds );
		foreach ( $refunds as $refund ) {
			$this->set_refund_props( $refund );
		}
	}

	/**
	 * Helper method to set refund props.
	 *
	 * @param \WC_Order $refund Refund object.
	 */
	private function set_refund_props( $refund ) {
		$refund->set_props(
			array(
				'amount'           => $refund->get_meta( '_refund_amount', true ),
				'refunded_by'      => $refund->get_meta( '_refunded_by', true ),
				'refunded_payment' => wc_string_to_bool( $refund->get_meta( '_refunded_payment', true ) ),
				'reason'           => $refund->get_meta( '_refund_reason', true ),
			)
		);
	}

	/**
	 * Method to create a refund in the database.
	 *
	 * @param \WC_Abstract_Order $refund Refund object.
	 */
	public function create( &$refund ) {
		$refund->set_status( 'completed' ); // Refund are always marked completed.
		$this->persist_save( $refund );
	}

	/**
	 * Update refund in database.
	 *
	 * @param \WC_Order $refund Refund object.
	 */
	public function update( &$refund ) {
		$this->persist_updates( $refund );
	}

	/**
	 * Helper method that updates post meta based on an refund object.
	 * Mostly used for backwards compatibility purposes in this datastore.
	 *
	 * @param \WC_Order $refund Refund object.
	 */
	public function update_order_meta( &$refund ) {
		parent::update_order_meta( $refund );

		// Update additional props.
		$updated_props     = array();
		$meta_key_to_props = array(
			'_refund_amount'    => 'amount',
			'_refunded_by'      => 'refunded_by',
			'_refunded_payment' => 'refunded_payment',
			'_refund_reason'    => 'reason',
		);

		$props_to_update = $this->get_props_to_update( $refund, $meta_key_to_props );
		foreach ( $props_to_update as $meta_key => $prop ) {
			$value = $refund->{"get_$prop"}( 'edit' );
			$refund->update_meta_data( $meta_key, $value );
			$updated_props[] = $prop;
		}

		/**
		 * Fires after updating meta for a order refund.
		 *
		 * @since 2.7.0
		 */
		do_action( 'woocommerce_order_refund_object_updated_props', $refund, $updated_props );
	}

	/**
	 * Get a title for the new post type.
	 *
	 * @return string
	 */
	protected function get_post_title() {
		return sprintf(
		/* translators: %s: Order date */
			__( 'Refund &ndash; %s', 'woocommerce' ),
			( new \DateTime( 'now' ) )->format( _x( 'M d, Y @ h:i A', 'Order date parsed by DateTime::format', 'woocommerce' ) ) // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment, WordPress.WP.I18n.UnorderedPlaceholdersText
		);
	}


	/**
	 * Returns data store object to use backfilling.
	 *
	 * @return \WC_Order_Refund_Data_Store_CPT
	 */
	protected function get_post_data_store_for_backfill() {
		return new \WC_Order_Refund_Data_Store_CPT();
	}

}
