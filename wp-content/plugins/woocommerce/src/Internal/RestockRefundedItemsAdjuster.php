<?php
/**
 * RestockRefundedItemsAdjuster class file.
 */

namespace Automattic\WooCommerce\Internal;

use Automattic\WooCommerce\Proxies\LegacyProxy;

defined( 'ABSPATH' ) || exit;

/**
 * Class to adjust or initialize the restock refunded items.
 */
class RestockRefundedItemsAdjuster {
	/**
	 * The order factory to use.
	 *
	 * @var WC_Order_Factory
	 */
	private $order_factory;

	/**
	 * Class initialization, to be executed when the class is resolved by the container.
	 *
	 * @internal
	 */
	final public function init() {
		$this->order_factory = wc_get_container()->get( LegacyProxy::class )->get_instance_of( \WC_Order_Factory::class );
		add_action( 'woocommerce_before_save_order_items', array( $this, 'initialize_restock_refunded_items' ), 10, 2 );
	}

	/**
	 * Initializes the restock refunded items meta for order version less than 5.5.
	 *
	 * @see https://github.com/woocommerce/woocommerce/issues/29502
	 *
	 * @param int   $order_id Order ID.
	 * @param array $items Order items to save.
	 */
	public function initialize_restock_refunded_items( $order_id, $items ) {
		$order         = wc_get_order( $order_id );
		$order_version = $order->get_version();

		if ( version_compare( $order_version, '5.5', '>=' ) ) {
			return;
		}

		// If there are no refund lines, then this migration isn't necessary because restock related meta's wouldn't be set.
		if ( 0 === count( $order->get_refunds() ) ) {
			return;
		}

		if ( isset( $items['order_item_id'] ) ) {
			foreach ( $items['order_item_id'] as $item_id ) {
				$item = $this->order_factory::get_order_item( absint( $item_id ) );

				if ( ! $item ) {
					continue;
				}

				if ( 'line_item' !== $item->get_type() ) {
					continue;
				}

				// There could be code paths in custom code which don't update version number but still update the items.
				if ( '' !== $item->get_meta( '_restock_refunded_items', true ) ) {
					continue;
				}

				$refunded_item_quantity = abs( $order->get_qty_refunded_for_item( $item->get_id() ) );
				$item->add_meta_data( '_restock_refunded_items', $refunded_item_quantity, false );
				$item->save();
			}
		}
	}
}
