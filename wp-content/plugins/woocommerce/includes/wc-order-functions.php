<?php
/**
 * WooCommerce Order Functions
 *
 * Functions for order specific things.
 *
 * @package WooCommerce\Functions
 * @version 3.4.0
 */

use Automattic\WooCommerce\Internal\DataStores\Orders\DataSynchronizer;
use Automattic\WooCommerce\Utilities\StringUtil;

defined( 'ABSPATH' ) || exit;

/**
 * Standard way of retrieving orders based on certain parameters.
 *
 * This function should be used for order retrieval so that when we move to
 * custom tables, functions still work.
 *
 * Args and usage: https://github.com/woocommerce/woocommerce/wiki/wc_get_orders-and-WC_Order_Query
 *
 * @since  2.6.0
 * @param  array $args Array of args (above).
 * @return WC_Order[]|stdClass Number of pages and an array of order objects if
 *                             paginate is true, or just an array of values.
 */
function wc_get_orders( $args ) {
	$map_legacy = array(
		'numberposts'    => 'limit',
		'post_type'      => 'type',
		'post_status'    => 'status',
		'post_parent'    => 'parent',
		'author'         => 'customer',
		'email'          => 'billing_email',
		'posts_per_page' => 'limit',
		'paged'          => 'page',
	);

	foreach ( $map_legacy as $from => $to ) {
		if ( isset( $args[ $from ] ) ) {
			$args[ $to ] = $args[ $from ];
		}
	}

	// Map legacy date args to modern date args.
	$date_before = false;
	$date_after  = false;

	if ( ! empty( $args['date_before'] ) ) {
		$datetime    = wc_string_to_datetime( $args['date_before'] );
		$date_before = strpos( $args['date_before'], ':' ) ? $datetime->getOffsetTimestamp() : $datetime->date( 'Y-m-d' );
	}
	if ( ! empty( $args['date_after'] ) ) {
		$datetime   = wc_string_to_datetime( $args['date_after'] );
		$date_after = strpos( $args['date_after'], ':' ) ? $datetime->getOffsetTimestamp() : $datetime->date( 'Y-m-d' );
	}

	if ( $date_before && $date_after ) {
		$args['date_created'] = $date_after . '...' . $date_before;
	} elseif ( $date_before ) {
		$args['date_created'] = '<' . $date_before;
	} elseif ( $date_after ) {
		$args['date_created'] = '>' . $date_after;
	}

	$query = new WC_Order_Query( $args );
	return $query->get_orders();
}

/**
 * Main function for returning orders, uses the WC_Order_Factory class.
 *
 * @since  2.2
 *
 * @param mixed $the_order       Post object or post ID of the order.
 *
 * @return bool|WC_Order|WC_Order_Refund
 */
function wc_get_order( $the_order = false ) {
	if ( ! did_action( 'woocommerce_after_register_post_type' ) ) {
		wc_doing_it_wrong( __FUNCTION__, 'wc_get_order should not be called before post types are registered (woocommerce_after_register_post_type action)', '2.5' );
		return false;
	}
	return WC()->order_factory->get_order( $the_order );
}

/**
 * Get all order statuses.
 *
 * @since 2.2
 * @used-by WC_Order::set_status
 * @return array
 */
function wc_get_order_statuses() {
	$order_statuses = array(
		'wc-pending'    => _x( 'Pending payment', 'Order status', 'woocommerce' ),
		'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
		'wc-on-hold'    => _x( 'On hold', 'Order status', 'woocommerce' ),
		'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
		'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
		'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
		'wc-failed'     => _x( 'Failed', 'Order status', 'woocommerce' ),
	);
	return apply_filters( 'wc_order_statuses', $order_statuses );
}

/**
 * See if a string is an order status.
 *
 * @param  string $maybe_status Status, including any wc- prefix.
 * @return bool
 */
function wc_is_order_status( $maybe_status ) {
	$order_statuses = wc_get_order_statuses();
	return isset( $order_statuses[ $maybe_status ] );
}

/**
 * Get list of statuses which are consider 'paid'.
 *
 * @since  3.0.0
 * @return array
 */
function wc_get_is_paid_statuses() {
	return apply_filters( 'woocommerce_order_is_paid_statuses', array( 'processing', 'completed' ) );
}

/**
 * Get list of statuses which are consider 'pending payment'.
 *
 * @since  3.6.0
 * @return array
 */
function wc_get_is_pending_statuses() {
	return apply_filters( 'woocommerce_order_is_pending_statuses', array( 'pending' ) );
}

/**
 * Get the nice name for an order status.
 *
 * @since  2.2
 * @param  string $status Status.
 * @return string
 */
function wc_get_order_status_name( $status ) {
	$statuses = wc_get_order_statuses();
	$status   = 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
	$status   = isset( $statuses[ 'wc-' . $status ] ) ? $statuses[ 'wc-' . $status ] : $status;
	return $status;
}

/**
 * Generate an order key with prefix.
 *
 * @since 3.5.4
 * @param string $key Order key without a prefix. By default generates a 13 digit secret.
 * @return string The order key.
 */
function wc_generate_order_key( $key = '' ) {
	if ( '' === $key ) {
		$key = wp_generate_password( 13, false );
	}

	return 'wc_' . apply_filters( 'woocommerce_generate_order_key', 'order_' . $key );
}

/**
 * Finds an Order ID based on an order key.
 *
 * @param string $order_key An order key has generated by.
 * @return int The ID of an order, or 0 if the order could not be found.
 */
function wc_get_order_id_by_order_key( $order_key ) {
	$data_store = WC_Data_Store::load( 'order' );
	return $data_store->get_order_id_by_order_key( $order_key );
}

/**
 * Get all registered order types.
 *
 * @since  2.2
 * @param  string $for Optionally define what you are getting order types for so
 *                     only relevant types are returned.
 *                     e.g. for 'order-meta-boxes', 'order-count'.
 * @return array
 */
function wc_get_order_types( $for = '' ) {
	global $wc_order_types;

	if ( ! is_array( $wc_order_types ) ) {
		$wc_order_types = array();
	}

	$order_types = array();

	switch ( $for ) {
		case 'order-count':
			foreach ( $wc_order_types as $type => $args ) {
				if ( ! $args['exclude_from_order_count'] ) {
					$order_types[] = $type;
				}
			}
			break;
		case 'order-meta-boxes':
			foreach ( $wc_order_types as $type => $args ) {
				if ( $args['add_order_meta_boxes'] ) {
					$order_types[] = $type;
				}
			}
			break;
		case 'view-orders':
			foreach ( $wc_order_types as $type => $args ) {
				if ( ! $args['exclude_from_order_views'] ) {
					$order_types[] = $type;
				}
			}
			break;
		case 'reports':
			foreach ( $wc_order_types as $type => $args ) {
				if ( ! $args['exclude_from_order_reports'] ) {
					$order_types[] = $type;
				}
			}
			break;
		case 'sales-reports':
			foreach ( $wc_order_types as $type => $args ) {
				if ( ! $args['exclude_from_order_sales_reports'] ) {
					$order_types[] = $type;
				}
			}
			break;
		case 'order-webhooks':
			foreach ( $wc_order_types as $type => $args ) {
				if ( ! $args['exclude_from_order_webhooks'] ) {
					$order_types[] = $type;
				}
			}
			break;
		case 'cot-migration':
			foreach ( $wc_order_types as $type => $args ) {
				if ( DataSynchronizer::PLACEHOLDER_ORDER_POST_TYPE !== $type ) {
					$order_types[] = $type;
				}
			}
			break;
		case 'admin-menu':
			$order_types = array_intersect(
				array_keys( $wc_order_types ),
				get_post_types(
					array(
						'show_ui'      => true,
						'show_in_menu' => 'woocommerce',
					)
				)
			);
			break;
		default:
			$order_types = array_keys( $wc_order_types );
			break;
	}

	return apply_filters( 'wc_order_types', $order_types, $for );
}

/**
 * Get an order type by post type name.
 *
 * @param  string $type Post type name.
 * @return bool|array Details about the order type.
 */
function wc_get_order_type( $type ) {
	global $wc_order_types;

	if ( isset( $wc_order_types[ $type ] ) ) {
		return $wc_order_types[ $type ];
	}

	return false;
}

/**
 * Register order type. Do not use before init.
 *
 * Wrapper for register post type, as well as a method of telling WC which.
 * post types are types of orders, and having them treated as such.
 *
 * $args are passed to register_post_type, but there are a few specific to this function:
 *      - exclude_from_orders_screen (bool) Whether or not this order type also get shown in the main.
 *      orders screen.
 *      - add_order_meta_boxes (bool) Whether or not the order type gets shop_order meta boxes.
 *      - exclude_from_order_count (bool) Whether or not this order type is excluded from counts.
 *      - exclude_from_order_views (bool) Whether or not this order type is visible by customers when.
 *      viewing orders e.g. on the my account page.
 *      - exclude_from_order_reports (bool) Whether or not to exclude this type from core reports.
 *      - exclude_from_order_sales_reports (bool) Whether or not to exclude this type from core sales reports.
 *
 * @since  2.2
 * @see    register_post_type for $args used in that function
 * @param  string $type Post type. (max. 20 characters, can not contain capital letters or spaces).
 * @param  array  $args An array of arguments.
 * @return bool Success or failure
 */
function wc_register_order_type( $type, $args = array() ) {
	if ( post_type_exists( $type ) ) {
		return false;
	}

	global $wc_order_types;

	if ( ! is_array( $wc_order_types ) ) {
		$wc_order_types = array();
	}

	// Register as a post type.
	if ( is_wp_error( register_post_type( $type, $args ) ) ) {
		return false;
	}

	// Register for WC usage.
	$order_type_args = array(
		'exclude_from_orders_screen'       => false,
		'add_order_meta_boxes'             => true,
		'exclude_from_order_count'         => false,
		'exclude_from_order_views'         => false,
		'exclude_from_order_webhooks'      => false,
		'exclude_from_order_reports'       => false,
		'exclude_from_order_sales_reports' => false,
		'class_name'                       => 'WC_Order',
	);

	$args                    = array_intersect_key( $args, $order_type_args );
	$args                    = wp_parse_args( $args, $order_type_args );
	$wc_order_types[ $type ] = $args;

	return true;
}

/**
 * Return the count of processing orders.
 *
 * @return int
 */
function wc_processing_order_count() {
	return wc_orders_count( 'processing' );
}

/**
 * Return the orders count of a specific order status.
 *
 * @param string $status Status.
 * @param string $type   (Optional) Order type. Leave empty to include all 'for order-count' order types. @{see wc_get_order_types()}.
 * @return int
 */
function wc_orders_count( $status, string $type = '' ) {
	$count           = 0;
	$legacy_statuses = array( 'draft', 'trash' );
	$valid_statuses  = array_merge( array_keys( wc_get_order_statuses() ), $legacy_statuses );
	$status          = ( ! in_array( $status, $legacy_statuses, true ) && 0 !== strpos( $status, 'wc-' ) ) ? 'wc-' . $status : $status;
	$valid_types     = wc_get_order_types( 'order-count' );
	$type            = trim( $type );

	if ( ! in_array( $status, $valid_statuses, true ) || ( $type && ! in_array( $type, $valid_types, true ) ) ) {
		return 0;
	}

	$cache_key    = WC_Cache_Helper::get_cache_prefix( 'orders' ) . $status . $type;
	$cached_count = wp_cache_get( $cache_key, 'counts' );

	if ( false !== $cached_count ) {
		return $cached_count;
	}

	$types_for_count = $type ? array( $type ) : $valid_types;

	foreach ( $types_for_count as $type ) {
		$data_store = WC_Data_Store::load( 'shop_order' === $type ? 'order' : $type );
		if ( $data_store ) {
			$count += $data_store->get_order_count( $status );
		}
	}

	wp_cache_set( $cache_key, $count, 'counts' );

	return $count;
}

/**
 * Grant downloadable product access to the file identified by $download_id.
 *
 * @param  string         $download_id File identifier.
 * @param  int|WC_Product $product     Product instance or ID.
 * @param  WC_Order       $order       Order data.
 * @param  int            $qty         Quantity purchased.
 * @param  WC_Order_Item  $item        Item of the order.
 * @return int|bool insert id or false on failure.
 */
function wc_downloadable_file_permission( $download_id, $product, $order, $qty = 1, $item = null ) {
	if ( is_numeric( $product ) ) {
		$product = wc_get_product( $product );
	}
	$download = new WC_Customer_Download();
	$download->set_download_id( $download_id );
	$download->set_product_id( $product->get_id() );
	$download->set_user_id( $order->get_customer_id() );
	$download->set_order_id( $order->get_id() );
	$download->set_user_email( $order->get_billing_email() );
	$download->set_order_key( $order->get_order_key() );
	$download->set_downloads_remaining( 0 > $product->get_download_limit() ? '' : $product->get_download_limit() * $qty );
	$download->set_access_granted( time() );
	$download->set_download_count( 0 );

	$expiry = $product->get_download_expiry();

	if ( $expiry > 0 ) {
		$from_date = $order->get_date_completed() ? $order->get_date_completed()->format( 'Y-m-d' ) : current_time( 'mysql', true );
		$download->set_access_expires( strtotime( $from_date . ' + ' . $expiry . ' DAY' ) );
	}

	$download = apply_filters( 'woocommerce_downloadable_file_permission', $download, $product, $order, $qty, $item );

	return $download->save();
}

/**
 * Order Status completed - give downloadable product access to customer.
 *
 * @param int  $order_id Order ID.
 * @param bool $force    Force downloadable permissions.
 */
function wc_downloadable_product_permissions( $order_id, $force = false ) {
	$order = wc_get_order( $order_id );

	if ( ! $order || ( $order->get_data_store()->get_download_permissions_granted( $order ) && ! $force ) ) {
		return;
	}

	if ( $order->has_status( 'processing' ) && 'no' === get_option( 'woocommerce_downloads_grant_access_after_payment' ) ) {
		return;
	}

	if ( count( $order->get_items() ) > 0 ) {
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();

			if ( $product && $product->exists() && $product->is_downloadable() ) {
				$downloads = $product->get_downloads();

				foreach ( array_keys( $downloads ) as $download_id ) {
					wc_downloadable_file_permission( $download_id, $product, $order, $item->get_quantity(), $item );
				}
			}
		}
	}

	$order->get_data_store()->set_download_permissions_granted( $order, true );
	do_action( 'woocommerce_grant_product_download_permissions', $order_id );
}
add_action( 'woocommerce_order_status_completed', 'wc_downloadable_product_permissions' );
add_action( 'woocommerce_order_status_processing', 'wc_downloadable_product_permissions' );

/**
 * Clear all transients cache for order data.
 *
 * @param int|WC_Order $order Order instance or ID.
 */
function wc_delete_shop_order_transients( $order = 0 ) {
	if ( is_numeric( $order ) ) {
		$order = wc_get_order( $order );
	}
	$reports             = WC_Admin_Reports::get_reports();
	$transients_to_clear = array(
		'wc_admin_report',
	);

	foreach ( $reports as $report_group ) {
		foreach ( $report_group['reports'] as $report_key => $report ) {
			$transients_to_clear[] = 'wc_report_' . $report_key;
		}
	}

	foreach ( $transients_to_clear as $transient ) {
		delete_transient( $transient );
	}

	// Clear customer's order related caches.
	if ( is_a( $order, 'WC_Order' ) ) {
		$order_id = $order->get_id();
		delete_user_meta( $order->get_customer_id(), '_money_spent' );
		delete_user_meta( $order->get_customer_id(), '_order_count' );
		delete_user_meta( $order->get_customer_id(), '_last_order' );
	} else {
		$order_id = 0;
	}

	// Increments the transient version to invalidate cache.
	WC_Cache_Helper::get_transient_version( 'orders', true );

	// Do the same for regular cache.
	WC_Cache_Helper::invalidate_cache_group( 'orders' );

	do_action( 'woocommerce_delete_shop_order_transients', $order_id );
}

/**
 * See if we only ship to billing addresses.
 *
 * @return bool
 */
function wc_ship_to_billing_address_only() {
	return 'billing_only' === get_option( 'woocommerce_ship_to_destination' );
}

/**
 * Create a new order refund programmatically.
 *
 * Returns a new refund object on success which can then be used to add additional data.
 *
 * @since 2.2
 * @throws Exception Throws exceptions when fail to create, but returns WP_Error instead.
 * @param array $args New refund arguments.
 * @return WC_Order_Refund|WP_Error
 */
function wc_create_refund( $args = array() ) {
	$default_args = array(
		'amount'         => 0,
		'reason'         => null,
		'order_id'       => 0,
		'refund_id'      => 0,
		'line_items'     => array(),
		'refund_payment' => false,
		'restock_items'  => false,
	);

	try {
		$args  = wp_parse_args( $args, $default_args );
		$order = wc_get_order( $args['order_id'] );

		if ( ! $order ) {
			throw new Exception( __( 'Invalid order ID.', 'woocommerce' ) );
		}

		$remaining_refund_amount = $order->get_remaining_refund_amount();
		$remaining_refund_items  = $order->get_remaining_refund_items();
		$refund_item_count       = 0;
		$refund                  = new WC_Order_Refund( $args['refund_id'] );

		if ( 0 > $args['amount'] || $args['amount'] > $remaining_refund_amount ) {
			throw new Exception( __( 'Invalid refund amount.', 'woocommerce' ) );
		}

		$refund->set_currency( $order->get_currency() );
		$refund->set_amount( $args['amount'] );
		$refund->set_parent_id( absint( $args['order_id'] ) );
		$refund->set_refunded_by( get_current_user_id() ? get_current_user_id() : 1 );
		$refund->set_prices_include_tax( $order->get_prices_include_tax() );

		if ( ! is_null( $args['reason'] ) ) {
			$refund->set_reason( $args['reason'] );
		}

		// Negative line items.
		if ( count( $args['line_items'] ) > 0 ) {
			$items = $order->get_items( array( 'line_item', 'fee', 'shipping' ) );

			foreach ( $items as $item_id => $item ) {
				if ( ! isset( $args['line_items'][ $item_id ] ) ) {
					continue;
				}

				$qty          = isset( $args['line_items'][ $item_id ]['qty'] ) ? $args['line_items'][ $item_id ]['qty'] : 0;
				$refund_total = $args['line_items'][ $item_id ]['refund_total'];
				$refund_tax   = isset( $args['line_items'][ $item_id ]['refund_tax'] ) ? array_filter( (array) $args['line_items'][ $item_id ]['refund_tax'] ) : array();

				if ( empty( $qty ) && empty( $refund_total ) && empty( $args['line_items'][ $item_id ]['refund_tax'] ) ) {
					continue;
				}

				$class         = get_class( $item );
				$refunded_item = new $class( $item );
				$refunded_item->set_id( 0 );
				$refunded_item->add_meta_data( '_refunded_item_id', $item_id, true );
				$refunded_item->set_total( wc_format_refund_total( $refund_total ) );
				$refunded_item->set_taxes(
					array(
						'total'    => array_map( 'wc_format_refund_total', $refund_tax ),
						'subtotal' => array_map( 'wc_format_refund_total', $refund_tax ),
					)
				);

				if ( is_callable( array( $refunded_item, 'set_subtotal' ) ) ) {
					$refunded_item->set_subtotal( wc_format_refund_total( $refund_total ) );
				}

				if ( is_callable( array( $refunded_item, 'set_quantity' ) ) ) {
					$refunded_item->set_quantity( $qty * -1 );
				}

				$refund->add_item( $refunded_item );
				$refund_item_count += $qty;
			}
		}

		$refund->update_taxes();
		$refund->calculate_totals( false );
		$refund->set_total( $args['amount'] * -1 );

		// this should remain after update_taxes(), as this will save the order, and write the current date to the db
		// so we must wait until the order is persisted to set the date.
		if ( isset( $args['date_created'] ) ) {
			$refund->set_date_created( $args['date_created'] );
		}

		/**
		 * Action hook to adjust refund before save.
		 *
		 * @since 3.0.0
		 */
		do_action( 'woocommerce_create_refund', $refund, $args );

		if ( $refund->save() ) {
			if ( $args['refund_payment'] ) {
				$result = wc_refund_payment( $order, $refund->get_amount(), $refund->get_reason() );

				if ( is_wp_error( $result ) ) {
					$refund->delete();
					return $result;
				}

				$refund->set_refunded_payment( true );
				$refund->save();
			}

			if ( $args['restock_items'] ) {
				wc_restock_refunded_items( $order, $args['line_items'] );
			}

			/**
			 * Trigger notification emails.
			 *
			 * Filter hook to modify the partially-refunded status conditions.
			 *
			 * @since 6.7.0
			 *
			 * @param bool $is_partially_refunded Whether the order is partially refunded.
			 * @param int  $order_id The order id.
			 * @param int  $refund_id The refund id.
			 */
			if ( (bool) apply_filters( 'woocommerce_order_is_partially_refunded', ( $remaining_refund_amount - $args['amount'] ) > 0 || ( $order->has_free_item() && ( $remaining_refund_items - $refund_item_count ) > 0 ), $order->get_id(), $refund->get_id() ) ) {
				do_action( 'woocommerce_order_partially_refunded', $order->get_id(), $refund->get_id() );
			} else {
				do_action( 'woocommerce_order_fully_refunded', $order->get_id(), $refund->get_id() );

				$parent_status = apply_filters( 'woocommerce_order_fully_refunded_status', 'refunded', $order->get_id(), $refund->get_id() );

				if ( $parent_status ) {
					$order->update_status( $parent_status );
				}
			}
		}

		$order->set_date_modified( time() );
		$order->save();

		do_action( 'woocommerce_refund_created', $refund->get_id(), $args );
		do_action( 'woocommerce_order_refunded', $order->get_id(), $refund->get_id() );

	} catch ( Exception $e ) {
		if ( isset( $refund ) && is_a( $refund, 'WC_Order_Refund' ) ) {
			$refund->delete( true );
		}
		return new WP_Error( 'error', $e->getMessage() );
	}

	return $refund;
}

/**
 * Try to refund the payment for an order via the gateway.
 *
 * @since 3.0.0
 * @throws Exception Throws exceptions when fail to refund, but returns WP_Error instead.
 * @param WC_Order $order  Order instance.
 * @param string   $amount Amount to refund.
 * @param string   $reason Refund reason.
 * @return bool|WP_Error
 */
function wc_refund_payment( $order, $amount, $reason = '' ) {
	try {
		if ( ! is_a( $order, 'WC_Order' ) ) {
			throw new Exception( __( 'Invalid order.', 'woocommerce' ) );
		}

		$gateway_controller = WC_Payment_Gateways::instance();
		$all_gateways       = $gateway_controller->payment_gateways();
		$payment_method     = $order->get_payment_method();
		$gateway            = isset( $all_gateways[ $payment_method ] ) ? $all_gateways[ $payment_method ] : false;

		if ( ! $gateway ) {
			throw new Exception( __( 'The payment gateway for this order does not exist.', 'woocommerce' ) );
		}

		if ( ! $gateway->supports( 'refunds' ) ) {
			throw new Exception( __( 'The payment gateway for this order does not support automatic refunds.', 'woocommerce' ) );
		}

		$result = $gateway->process_refund( $order->get_id(), $amount, $reason );

		if ( ! $result ) {
			throw new Exception( __( 'An error occurred while attempting to create the refund using the payment gateway API.', 'woocommerce' ) );
		}

		if ( is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message() );
		}

		return true;

	} catch ( Exception $e ) {
		return new WP_Error( 'error', $e->getMessage() );
	}
}

/**
 * Restock items during refund.
 *
 * @since 3.0.0
 * @param WC_Order $order               Order instance.
 * @param array    $refunded_line_items Refunded items list.
 */
function wc_restock_refunded_items( $order, $refunded_line_items ) {
	if ( ! apply_filters( 'woocommerce_can_restock_refunded_items', true, $order, $refunded_line_items ) ) {
		return;
	}

	$line_items = $order->get_items();

	foreach ( $line_items as $item_id => $item ) {
		if ( ! isset( $refunded_line_items[ $item_id ], $refunded_line_items[ $item_id ]['qty'] ) ) {
			continue;
		}
		$product                = $item->get_product();
		$item_stock_reduced     = $item->get_meta( '_reduced_stock', true );
		$restock_refunded_items = (int) $item->get_meta( '_restock_refunded_items', true );
		$qty_to_refund          = $refunded_line_items[ $item_id ]['qty'];

		if ( ! $item_stock_reduced || ! $qty_to_refund || ! $product || ! $product->managing_stock() ) {
			continue;
		}

		$old_stock = $product->get_stock_quantity();
		$new_stock = wc_update_product_stock( $product, $qty_to_refund, 'increase' );

		// Update _reduced_stock meta to track changes.
		$item_stock_reduced = $item_stock_reduced - $qty_to_refund;

		// Keeps track of total running tally of reduced stock.
		$item->update_meta_data( '_reduced_stock', $item_stock_reduced );

		// Keeps track of only refunded items that needs restock.
		$item->update_meta_data( '_restock_refunded_items', $qty_to_refund + $restock_refunded_items );

		/* translators: 1: product ID 2: old stock level 3: new stock level */
		$restock_note = sprintf( __( 'Item #%1$s stock increased from %2$s to %3$s.', 'woocommerce' ), $product->get_id(), $old_stock, $new_stock );

		/**
		 * Allow the restock note to be modified.
		 *
		 * @since 6.4.0
		 *
		 * @param string $restock_note The original note.
		 * @param int $old_stock The old stock.
		 * @param bool|int|null $new_stock The new stock.
		 * @param WC_Order $order The order the refund was done for.
		 * @param bool|WC_Product $product The product the refund was done for.
		 */
		$restock_note = apply_filters( 'woocommerce_refund_restock_note', $restock_note, $old_stock, $new_stock, $order, $product );

		$order->add_order_note( $restock_note );

		$item->save();

		do_action( 'woocommerce_restock_refunded_item', $product->get_id(), $old_stock, $new_stock, $order, $product );
	}
}

/**
 * Get tax class by tax id.
 *
 * @since 2.2
 * @param int $tax_id Tax ID.
 * @return string
 */
function wc_get_tax_class_by_tax_id( $tax_id ) {
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare( "SELECT tax_rate_class FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_id = %d", $tax_id ) );
}

/**
 * Get payment gateway class by order data.
 *
 * @since 2.2
 * @param int|WC_Order $order Order instance.
 * @return WC_Payment_Gateway|bool
 */
function wc_get_payment_gateway_by_order( $order ) {
	if ( WC()->payment_gateways() ) {
		$payment_gateways = WC()->payment_gateways()->payment_gateways();
	} else {
		$payment_gateways = array();
	}

	if ( ! is_object( $order ) ) {
		$order_id = absint( $order );
		$order    = wc_get_order( $order_id );
	}

	return is_a( $order, 'WC_Order' ) && isset( $payment_gateways[ $order->get_payment_method() ] ) ? $payment_gateways[ $order->get_payment_method() ] : false;
}

/**
 * When refunding an order, create a refund line item if the partial refunds do not match order total.
 *
 * This is manual; no gateway refund will be performed.
 *
 * @since 2.4
 * @param int $order_id Order ID.
 */
function wc_order_fully_refunded( $order_id ) {
	$order      = wc_get_order( $order_id );
	$max_refund = wc_format_decimal( $order->get_total() - $order->get_total_refunded() );

	if ( ! $max_refund ) {
		return;
	}

	// Create the refund object.
	wc_switch_to_site_locale();
	wc_create_refund(
		array(
			'amount'     => $max_refund,
			'reason'     => __( 'Order fully refunded.', 'woocommerce' ),
			'order_id'   => $order_id,
			'line_items' => array(),
		)
	);
	wc_restore_locale();

	$order->add_order_note( __( 'Order status set to refunded. To return funds to the customer you will need to issue a refund through your payment gateway.', 'woocommerce' ) );
}
add_action( 'woocommerce_order_status_refunded', 'wc_order_fully_refunded' );

/**
 * Search orders.
 *
 * @since  2.6.0
 * @param  string $term Term to search.
 * @return array List of orders ID.
 */
function wc_order_search( $term ) {
	$data_store = WC_Data_Store::load( 'order' );
	return $data_store->search_orders( str_replace( 'Order #', '', wc_clean( $term ) ) );
}

/**
 * Update total sales amount for each product within a paid order.
 *
 * @since 3.0.0
 * @param int $order_id Order ID.
 */
function wc_update_total_sales_counts( $order_id ) {
	$order = wc_get_order( $order_id );

	if ( ! $order || $order->get_data_store()->get_recorded_sales( $order ) ) {
		return;
	}

	if ( count( $order->get_items() ) > 0 ) {
		foreach ( $order->get_items() as $item ) {
			$product_id = $item->get_product_id();

			if ( $product_id ) {
				$data_store = WC_Data_Store::load( 'product' );
				$data_store->update_product_sales( $product_id, absint( $item->get_quantity() ), 'increase' );
			}
		}
	}

	$order->get_data_store()->set_recorded_sales( $order, true );

	/**
	 * Called when sales for an order are recorded
	 *
	 * @param int $order_id order id
	 */
	do_action( 'woocommerce_recorded_sales', $order_id );
}
add_action( 'woocommerce_order_status_completed', 'wc_update_total_sales_counts' );
add_action( 'woocommerce_order_status_processing', 'wc_update_total_sales_counts' );
add_action( 'woocommerce_order_status_on-hold', 'wc_update_total_sales_counts' );

/**
 * Update used coupon amount for each coupon within an order.
 *
 * @since 3.0.0
 * @param int $order_id Order ID.
 */
function wc_update_coupon_usage_counts( $order_id ) {
	$order = wc_get_order( $order_id );

	if ( ! $order ) {
		return;
	}

	$has_recorded = $order->get_data_store()->get_recorded_coupon_usage_counts( $order );

	if ( $order->has_status( 'cancelled' ) && $has_recorded ) {
		$action = 'reduce';
		$order->get_data_store()->set_recorded_coupon_usage_counts( $order, false );
	} elseif ( ! $order->has_status( 'cancelled' ) && ! $has_recorded ) {
		$action = 'increase';
		$order->get_data_store()->set_recorded_coupon_usage_counts( $order, true );
	} elseif ( $order->has_status( 'cancelled' ) ) {
		$order->get_data_store()->release_held_coupons( $order, true );
		return;
	} else {
		return;
	}

	if ( count( $order->get_coupon_codes() ) > 0 ) {
		foreach ( $order->get_coupon_codes() as $code ) {
			if ( StringUtil::is_null_or_whitespace( $code ) ) {
				continue;
			}

			$coupon  = new WC_Coupon( $code );
			$used_by = $order->get_user_id();

			if ( ! $used_by ) {
				$used_by = $order->get_billing_email();
			}

			switch ( $action ) {
				case 'reduce':
					$coupon->decrease_usage_count( $used_by );
					break;
				case 'increase':
					$coupon->increase_usage_count( $used_by, $order );
					break;
			}
		}
		$order->get_data_store()->release_held_coupons( $order, true );
	}
}
add_action( 'woocommerce_order_status_pending', 'wc_update_coupon_usage_counts' );
add_action( 'woocommerce_order_status_completed', 'wc_update_coupon_usage_counts' );
add_action( 'woocommerce_order_status_processing', 'wc_update_coupon_usage_counts' );
add_action( 'woocommerce_order_status_on-hold', 'wc_update_coupon_usage_counts' );
add_action( 'woocommerce_order_status_cancelled', 'wc_update_coupon_usage_counts' );

/**
 * Cancel all unpaid orders after held duration to prevent stock lock for those products.
 */
function wc_cancel_unpaid_orders() {
	$held_duration = get_option( 'woocommerce_hold_stock_minutes' );

	// Re-schedule the event before cancelling orders
	// this way in case of a DB timeout or (plugin) crash the event is always scheduled for retry.
	wp_clear_scheduled_hook( 'woocommerce_cancel_unpaid_orders' );
	$cancel_unpaid_interval = apply_filters( 'woocommerce_cancel_unpaid_orders_interval_minutes', absint( $held_duration ) );
	wp_schedule_single_event( time() + ( absint( $cancel_unpaid_interval ) * 60 ), 'woocommerce_cancel_unpaid_orders' );

	if ( $held_duration < 1 || 'yes' !== get_option( 'woocommerce_manage_stock' ) ) {
		return;
	}

	$data_store    = WC_Data_Store::load( 'order' );
	$unpaid_orders = $data_store->get_unpaid_orders( strtotime( '-' . absint( $held_duration ) . ' MINUTES', current_time( 'timestamp' ) ) );

	if ( $unpaid_orders ) {
		foreach ( $unpaid_orders as $unpaid_order ) {
			$order = wc_get_order( $unpaid_order );

			if ( apply_filters( 'woocommerce_cancel_unpaid_order', 'checkout' === $order->get_created_via(), $order ) ) {
				$order->update_status( 'cancelled', __( 'Unpaid order cancelled - time limit reached.', 'woocommerce' ) );
			}
		}
	}
}
add_action( 'woocommerce_cancel_unpaid_orders', 'wc_cancel_unpaid_orders' );

/**
 * Sanitize order id removing unwanted characters.
 *
 * E.g Users can sometimes try to track an order id using # with no success.
 * This function will fix this.
 *
 * @since 3.1.0
 * @param int $order_id Order ID.
 */
function wc_sanitize_order_id( $order_id ) {
	return (int) filter_var( $order_id, FILTER_SANITIZE_NUMBER_INT );
}
add_filter( 'woocommerce_shortcode_order_tracking_order_id', 'wc_sanitize_order_id' );

/**
 * Get an order note.
 *
 * @since  3.2.0
 * @param  int|WP_Comment $data Note ID (or WP_Comment instance for internal use only).
 * @return stdClass|null        Object with order note details or null when does not exists.
 */
function wc_get_order_note( $data ) {
	if ( is_numeric( $data ) ) {
		$data = get_comment( $data );
	}

	if ( ! is_a( $data, 'WP_Comment' ) ) {
		return null;
	}

	return (object) apply_filters(
		'woocommerce_get_order_note',
		array(
			'id'            => (int) $data->comment_ID,
			'date_created'  => wc_string_to_datetime( $data->comment_date ),
			'content'       => $data->comment_content,
			'customer_note' => (bool) get_comment_meta( $data->comment_ID, 'is_customer_note', true ),
			'added_by'      => __( 'WooCommerce', 'woocommerce' ) === $data->comment_author ? 'system' : $data->comment_author,
		),
		$data
	);
}

/**
 * Get order notes.
 *
 * @since  3.2.0
 * @param  array $args Query arguments {
 *     Array of query parameters.
 *
 *     @type string $limit         Maximum number of notes to retrieve.
 *                                 Default empty (no limit).
 *     @type int    $order_id      Limit results to those affiliated with a given order ID.
 *                                 Default 0.
 *     @type array  $order__in     Array of order IDs to include affiliated notes for.
 *                                 Default empty.
 *     @type array  $order__not_in Array of order IDs to exclude affiliated notes for.
 *                                 Default empty.
 *     @type string $orderby       Define how should sort notes.
 *                                 Accepts 'date_created', 'date_created_gmt' or 'id'.
 *                                 Default: 'id'.
 *     @type string $order         How to order retrieved notes.
 *                                 Accepts 'ASC' or 'DESC'.
 *                                 Default: 'DESC'.
 *     @type string $type          Define what type of note should retrieve.
 *                                 Accepts 'customer', 'internal' or empty for both.
 *                                 Default empty.
 * }
 * @return stdClass[]              Array of stdClass objects with order notes details.
 */
function wc_get_order_notes( $args ) {
	$key_mapping = array(
		'limit'         => 'number',
		'order_id'      => 'post_id',
		'order__in'     => 'post__in',
		'order__not_in' => 'post__not_in',
	);

	foreach ( $key_mapping as $query_key => $db_key ) {
		if ( isset( $args[ $query_key ] ) ) {
			$args[ $db_key ] = $args[ $query_key ];
			unset( $args[ $query_key ] );
		}
	}

	// Define orderby.
	$orderby_mapping = array(
		'date_created'     => 'comment_date',
		'date_created_gmt' => 'comment_date_gmt',
		'id'               => 'comment_ID',
	);

	$args['orderby'] = ! empty( $args['orderby'] ) && in_array( $args['orderby'], array( 'date_created', 'date_created_gmt', 'id' ), true ) ? $orderby_mapping[ $args['orderby'] ] : 'comment_ID';

	// Set WooCommerce order type.
	if ( isset( $args['type'] ) && 'customer' === $args['type'] ) {
		$args['meta_query'] = array( // WPCS: slow query ok.
			array(
				'key'     => 'is_customer_note',
				'value'   => 1,
				'compare' => '=',
			),
		);
	} elseif ( isset( $args['type'] ) && 'internal' === $args['type'] ) {
		$args['meta_query'] = array( // WPCS: slow query ok.
			array(
				'key'     => 'is_customer_note',
				'compare' => 'NOT EXISTS',
			),
		);
	}

	// Set correct comment type.
	$args['type'] = 'order_note';

	// Always approved.
	$args['status'] = 'approve';

	// Does not support 'count' or 'fields'.
	unset( $args['count'], $args['fields'] );

	remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

	$notes = get_comments( $args );

	add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

	return array_filter( array_map( 'wc_get_order_note', $notes ) );
}

/**
 * Create an order note.
 *
 * @since  3.2.0
 * @param  int    $order_id         Order ID.
 * @param  string $note             Note to add.
 * @param  bool   $is_customer_note If is a costumer note.
 * @param  bool   $added_by_user    If note is create by an user.
 * @return int|WP_Error             Integer when created or WP_Error when found an error.
 */
function wc_create_order_note( $order_id, $note, $is_customer_note = false, $added_by_user = false ) {
	$order = wc_get_order( $order_id );

	if ( ! $order ) {
		return new WP_Error( 'invalid_order_id', __( 'Invalid order ID.', 'woocommerce' ), array( 'status' => 400 ) );
	}

	return $order->add_order_note( $note, (int) $is_customer_note, $added_by_user );
}

/**
 * Delete an order note.
 *
 * @since  3.2.0
 * @param  int $note_id Order note.
 * @return bool         True on success, false on failure.
 */
function wc_delete_order_note( $note_id ) {
	return wp_delete_comment( $note_id, true );
}
