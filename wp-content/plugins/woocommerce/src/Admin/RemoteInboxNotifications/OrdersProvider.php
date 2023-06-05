<?php
/**
 * Provider for order-related queries and operations.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * Provider for order-related queries and operations.
 */
class OrdersProvider {
	/**
	 * Allowed order statuses for calculating milestones.
	 *
	 * @var array
	 */
	protected $allowed_statuses = array(
		'pending',
		'processing',
		'completed',
	);

	/**
	 * Returns the number of orders.
	 *
	 * @return integer The number of orders.
	 */
	public function get_order_count() {
		$status_counts = array_map( 'wc_orders_count', $this->allowed_statuses );
		$orders_count  = array_sum( $status_counts );

		return $orders_count;
	}
}
