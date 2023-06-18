<?php
/**
 * Handle product stock reservation during checkout.
 */

namespace Automattic\WooCommerce\Checkout\Helpers;

use Automattic\WooCommerce\Utilities\OrderUtil;

defined( 'ABSPATH' ) || exit;

/**
 * Stock Reservation class.
 */
final class ReserveStock {

	/**
	 * Is stock reservation enabled?
	 *
	 * @var boolean
	 */
	private $enabled = true;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Table needed for this feature are added in 4.3.
		$this->enabled = get_option( 'woocommerce_schema_version', 0 ) >= 430;
	}

	/**
	 * Is stock reservation enabled?
	 *
	 * @return boolean
	 */
	protected function is_enabled() {
		return $this->enabled;
	}

	/**
	 * Query for any existing holds on stock for this item.
	 *
	 * @param \WC_Product $product Product to get reserved stock for.
	 * @param integer     $exclude_order_id Optional order to exclude from the results.
	 *
	 * @return integer Amount of stock already reserved.
	 */
	public function get_reserved_stock( $product, $exclude_order_id = 0 ) {
		global $wpdb;

		if ( ! $this->is_enabled() ) {
			return 0;
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
		return (int) $wpdb->get_var( $this->get_query_for_reserved_stock( $product->get_stock_managed_by_id(), $exclude_order_id ) );
	}

	/**
	 * Put a temporary hold on stock for an order if enough is available.
	 *
	 * @throws ReserveStockException If stock cannot be reserved.
	 *
	 * @param \WC_Order $order Order object.
	 * @param int       $minutes How long to reserve stock in minutes. Defaults to woocommerce_hold_stock_minutes.
	 */
	public function reserve_stock_for_order( $order, $minutes = 0 ) {
		$minutes = $minutes ? $minutes : (int) get_option( 'woocommerce_hold_stock_minutes', 60 );

		if ( ! $minutes || ! $this->is_enabled() ) {
			return;
		}

		$held_stock_notes = array();

		try {
			$items = array_filter(
				$order->get_items(),
				function( $item ) {
					return $item->is_type( 'line_item' ) && $item->get_product() instanceof \WC_Product && $item->get_quantity() > 0;
				}
			);
			$rows  = array();

			foreach ( $items as $item ) {
				$product = $item->get_product();

				if ( ! $product->is_in_stock() ) {
					throw new ReserveStockException(
						'woocommerce_product_out_of_stock',
						sprintf(
							/* translators: %s: product name */
							__( '&quot;%s&quot; is out of stock and cannot be purchased.', 'woocommerce' ),
							$product->get_name()
						),
						403
					);
				}

				// If stock management is off, no need to reserve any stock here.
				if ( ! $product->managing_stock() || $product->backorders_allowed() ) {
					continue;
				}

				$managed_by_id = $product->get_stock_managed_by_id();

				/**
				 * Filter order item quantity.
				 *
				 * @param int|float             $quantity Quantity.
				 * @param WC_Order              $order    Order data.
				 * @param WC_Order_Item_Product $item Order item data.
				 */
				$item_quantity = apply_filters( 'woocommerce_order_item_quantity', $item->get_quantity(), $order, $item );

				$rows[ $managed_by_id ] = isset( $rows[ $managed_by_id ] ) ? $rows[ $managed_by_id ] + $item_quantity : $item_quantity;

				if ( count( $held_stock_notes ) < 5 ) {
					// translators: %1$s is a product's formatted name, %2$d: is the quantity of said product to which the stock hold applied.
					$held_stock_notes[] = sprintf( _x( '- %1$s &times; %2$d', 'held stock note', 'woocommerce' ), $product->get_formatted_name(), $rows[ $managed_by_id ] );
				}
			}

			if ( ! empty( $rows ) ) {
				foreach ( $rows as $product_id => $quantity ) {
					$this->reserve_stock_for_product( $product_id, $quantity, $order, $minutes );
				}
			}
		} catch ( ReserveStockException $e ) {
			$this->release_stock_for_order( $order );
			throw $e;
		}

		// Add order note after successfully holding the stock.
		if ( ! empty( $held_stock_notes ) ) {
			$remaining_count = count( $rows ) - count( $held_stock_notes );
			if ( $remaining_count > 0 ) {
				$held_stock_notes[] = sprintf(
					// translators: %d is the remaining order items count.
					_nx( '- ...and %d more item.', '- ... and %d more items.', $remaining_count, 'held stock note', 'woocommerce' ),
					$remaining_count
				);
			}

			$order->add_order_note(
				sprintf(
					// translators: %1$s is a time in minutes, %2$s is a list of products and quantities.
					_x( 'Stock hold of %1$s minutes applied to: %2$s', 'held stock note', 'woocommerce' ),
					$minutes,
					'<br>' . implode( '<br>', $held_stock_notes )
				)
			);
		}
	}

	/**
	 * Release a temporary hold on stock for an order.
	 *
	 * @param \WC_Order $order Order object.
	 */
	public function release_stock_for_order( $order ) {
		global $wpdb;

		if ( ! $this->is_enabled() ) {
			return;
		}

		$wpdb->delete(
			$wpdb->wc_reserved_stock,
			array(
				'order_id' => $order->get_id(),
			)
		);
	}

	/**
	 * Reserve stock for a product by inserting rows into the DB.
	 *
	 * @throws ReserveStockException If a row cannot be inserted.
	 *
	 * @param int       $product_id Product ID which is having stock reserved.
	 * @param int       $stock_quantity Stock amount to reserve.
	 * @param \WC_Order $order Order object which contains the product.
	 * @param int       $minutes How long to reserve stock in minutes.
	 */
	private function reserve_stock_for_product( $product_id, $stock_quantity, $order, $minutes ) {
		global $wpdb;

		$product_data_store       = \WC_Data_Store::load( 'product' );
		$query_for_stock          = $product_data_store->get_query_for_stock( $product_id );
		$query_for_reserved_stock = $this->get_query_for_reserved_stock( $product_id, $order->get_id() );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
		$result = $wpdb->query(
			$wpdb->prepare(
				"
				INSERT INTO {$wpdb->wc_reserved_stock} ( `order_id`, `product_id`, `stock_quantity`, `timestamp`, `expires` )
				SELECT %d, %d, %d, NOW(), ( NOW() + INTERVAL %d MINUTE ) FROM DUAL
				WHERE ( $query_for_stock FOR UPDATE ) - ( $query_for_reserved_stock FOR UPDATE ) >= %d
				ON DUPLICATE KEY UPDATE `expires` = VALUES( `expires` ), `stock_quantity` = VALUES( `stock_quantity` )
				",
				$order->get_id(),
				$product_id,
				$stock_quantity,
				$minutes,
				$stock_quantity
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared

		if ( ! $result ) {
			$product = wc_get_product( $product_id );
			throw new ReserveStockException(
				'woocommerce_product_not_enough_stock',
				sprintf(
					/* translators: %s: product name */
					__( 'Not enough units of %s are available in stock to fulfil this order.', 'woocommerce' ),
					$product ? $product->get_name() : '#' . $product_id
				),
				403
			);
		}
	}

	/**
	 * Returns query statement for getting reserved stock of a product.
	 *
	 * @param int     $product_id Product ID.
	 * @param integer $exclude_order_id Optional order to exclude from the results.
	 * @return string|void Query statement.
	 */
	private function get_query_for_reserved_stock( $product_id, $exclude_order_id = 0 ) {
		global $wpdb;

		$join         = "$wpdb->posts posts ON stock_table.`order_id` = posts.ID";
		$where_status = "posts.post_status IN ( 'wc-checkout-draft', 'wc-pending' )";
		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$join         = "{$wpdb->prefix}wc_orders orders ON stock_table.`order_id` = orders.id";
			$where_status = "orders.status IN ( 'wc-checkout-draft', 'wc-pending' )";
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$query = $wpdb->prepare(
			"
			SELECT COALESCE( SUM( stock_table.`stock_quantity` ), 0 ) FROM $wpdb->wc_reserved_stock stock_table
			LEFT JOIN $join
			WHERE $where_status
			AND stock_table.`expires` > NOW()
			AND stock_table.`product_id` = %d
			AND stock_table.`order_id` != %d
			",
			$product_id,
			$exclude_order_id
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		/**
		 * Filter: woocommerce_query_for_reserved_stock
		 * Allows to filter the query for getting reserved stock of a product.
		 *
		 * @since 4.5.0
		 * @param string $query            The query for getting reserved stock of a product.
		 * @param int    $product_id       Product ID.
		 * @param int    $exclude_order_id Order to exclude from the results.
		 */
		return apply_filters( 'woocommerce_query_for_reserved_stock', $query, $product_id, $exclude_order_id );
	}
}
