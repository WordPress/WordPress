<?php
/**
 * REST API Orders Controller
 *
 * Handles requests to /orders/*
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Controller as ReportsController;
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use Automattic\WooCommerce\Utilities\OrderUtil;

/**
 * Orders controller.
 *
 * @internal
 * @extends WC_REST_Orders_Controller
 */
class Orders extends \WC_REST_Orders_Controller {
	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-analytics';

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();
		// This needs to remain a string to support extensions that filter Order Number.
		$params['number'] = array(
			'description'       => __( 'Limit result set to orders matching part of an order number.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		// Fix the default 'status' value until it can be patched in core.
		$params['status']['default'] = array( 'any' );

		// Analytics settings may affect the allowed status list.
		$params['status']['items']['enum'] = ReportsController::get_order_statuses();

		return $params;
	}

	/**
	 * Prepare objects query.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array
	 */
	protected function prepare_objects_query( $request ) {
		$args = parent::prepare_objects_query( $request );

		if ( ! empty( $request['number'] ) ) {
			$args = $this->search_partial_order_number( $request['number'], $args );
		}

		return $args;
	}

	/**
	 * Helper method to allow searching by partial order number.
	 *
	 * @param int   $number Partial order number match.
	 * @param array $args List of arguments for the request.
	 *
	 * @return array Modified args with partial order search included.
	 */
	private function search_partial_order_number( $number, $args ) {
		global $wpdb;

		$partial_number = trim( $number );
		$limit          = intval( $args['posts_per_page'] );
		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$order_table_name = OrdersTableDataStore::get_orders_table_name();
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $orders_table_name is hardcoded.
			$order_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT id
					FROM $order_table_name
					    WHERE type = 'shop_order'
					    AND id LIKE %s
					LIMIT %d",
					$wpdb->esc_like( absint( $partial_number ) ) . '%',
					$limit
				)
			);
			// phpcs:enable
		} else {
			$order_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT ID
				FROM {$wpdb->prefix}posts
				WHERE post_type = 'shop_order'
				AND ID LIKE %s
				LIMIT %d",
					$wpdb->esc_like( absint( $partial_number ) ) . '%',
					$limit
				)
			);
		}

		// Force WP_Query return empty if don't found any order.
		$order_ids        = empty( $order_ids ) ? array( 0 ) : $order_ids;
		$args['post__in'] = $order_ids;

		return $args;
	}

	/**
	 * Get product IDs, names, and quantity from order ID.
	 *
	 * @param array $order_id ID of order.
	 * @return array
	 */
	protected function get_products_by_order_id( $order_id ) {
		global $wpdb;
		$order_items_table    = $wpdb->prefix . 'woocommerce_order_items';
		$order_itemmeta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
		$products             = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT
				order_id,
				order_itemmeta.meta_value as product_id,
				order_itemmeta_2.meta_value as product_quantity,
				order_itemmeta_3.meta_value as variation_id,
				{$wpdb->posts}.post_title as product_name
			FROM {$order_items_table} order_items
			    LEFT JOIN {$order_itemmeta_table} order_itemmeta on order_items.order_item_id = order_itemmeta.order_item_id
			    LEFT JOIN {$order_itemmeta_table} order_itemmeta_2 on order_items.order_item_id = order_itemmeta_2.order_item_id
			    LEFT JOIN {$order_itemmeta_table} order_itemmeta_3 on order_items.order_item_id = order_itemmeta_3.order_item_id
			    LEFT JOIN {$wpdb->posts} on {$wpdb->posts}.ID = order_itemmeta.meta_value
			WHERE
				order_id = ( %d )
			    AND order_itemmeta.meta_key = '_product_id'
				AND order_itemmeta_2.meta_key = '_qty'
			  	AND order_itemmeta_3.meta_key = '_variation_id'
			GROUP BY product_id
			", // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$order_id
			),
			ARRAY_A
		);

		return $products;
	}

	/**
	 * Get customer data from customer_id.
	 *
	 * @param array $customer_id ID of customer.
	 * @return array
	 */
	protected function get_customer_by_id( $customer_id ) {
		global $wpdb;

		$customer_lookup_table = $wpdb->prefix . 'wc_customer_lookup';

		$customer = $wpdb->get_row(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT * FROM {$customer_lookup_table} WHERE customer_id = ( %d )",
				$customer_id
			),
			ARRAY_A
		);

		return $customer;
	}

	/**
	 * Get formatted item data.
	 *
	 * @param  WC_Data $object WC_Data instance.
	 * @return array
	 */
	protected function get_formatted_item_data( $object ) {
		$extra_fields = array( 'customer', 'products' );
		$fields       = false;
		// Determine if the response fields were specified.
		if ( ! empty( $this->request['_fields'] ) ) {
			$fields = wp_parse_list( $this->request['_fields'] );

			if ( 0 === count( $fields ) ) {
				$fields = false;
			} else {
				$fields = array_map( 'trim', $fields );
			}
		}

		// Initially skip line items if we can.
		$using_order_class_override = is_a( $object, '\Automattic\WooCommerce\Admin\Overrides\Order' );
		if ( $using_order_class_override ) {
			$data = $object->get_data_without_line_items();
		} else {
			$data = $object->get_data();
		}

		$extra_fields      = false === $fields ? array() : array_intersect( $extra_fields, $fields );
		$format_decimal    = array( 'discount_total', 'discount_tax', 'shipping_total', 'shipping_tax', 'shipping_total', 'shipping_tax', 'cart_tax', 'total', 'total_tax' );
		$format_date       = array( 'date_created', 'date_modified', 'date_completed', 'date_paid' );
		$format_line_items = array( 'line_items', 'tax_lines', 'shipping_lines', 'fee_lines', 'coupon_lines' );

		// Add extra data as necessary.
		$extra_data = array();
		foreach ( $extra_fields as $field ) {
			switch ( $field ) {
				case 'customer':
					$extra_data['customer'] = $this->get_customer_by_id( $data['customer_id'] );
					break;
				case 'products':
					$extra_data['products'] = $this->get_products_by_order_id( $object->get_id() );
					break;
			}
		}
		// Format decimal values.
		foreach ( $format_decimal as $key ) {
			$data[ $key ] = wc_format_decimal( $data[ $key ], $this->request['dp'] );
		}

		// format total with order currency.
		if ( $object instanceof \WC_Order ) {
			$data['total_formatted'] = wp_strip_all_tags( html_entity_decode( $object->get_formatted_order_total() ), true );
		}

		// Format date values.
		foreach ( $format_date as $key ) {
			$datetime              = $data[ $key ];
			$data[ $key ]          = wc_rest_prepare_date_response( $datetime, false );
			$data[ $key . '_gmt' ] = wc_rest_prepare_date_response( $datetime );
		}

		// Format the order status.
		$data['status'] = 'wc-' === substr( $data['status'], 0, 3 ) ? substr( $data['status'], 3 ) : $data['status'];

		// Format requested line items.
		$formatted_line_items = array();

		foreach ( $format_line_items as $key ) {
			if ( false === $fields || in_array( $key, $fields, true ) ) {
				if ( $using_order_class_override ) {
					$line_item_data = $object->get_line_item_data( $key );
				} else {
					$line_item_data = $data[ $key ];
				}
				$formatted_line_items[ $key ] = array_values( array_map( array( $this, 'get_order_item_data' ), $line_item_data ) );
			}
		}

		// Refunds.
		$data['refunds'] = array();
		foreach ( $object->get_refunds() as $refund ) {
			$data['refunds'][] = array(
				'id'     => $refund->get_id(),
				'reason' => $refund->get_reason() ? $refund->get_reason() : '',
				'total'  => '-' . wc_format_decimal( $refund->get_amount(), $this->request['dp'] ),
			);
		}

		return array_merge(
			array(
				'id'                   => $object->get_id(),
				'parent_id'            => $data['parent_id'],
				'number'               => $data['number'],
				'order_key'            => $data['order_key'],
				'created_via'          => $data['created_via'],
				'version'              => $data['version'],
				'status'               => $data['status'],
				'currency'             => $data['currency'],
				'date_created'         => $data['date_created'],
				'date_created_gmt'     => $data['date_created_gmt'],
				'date_modified'        => $data['date_modified'],
				'date_modified_gmt'    => $data['date_modified_gmt'],
				'discount_total'       => $data['discount_total'],
				'discount_tax'         => $data['discount_tax'],
				'shipping_total'       => $data['shipping_total'],
				'shipping_tax'         => $data['shipping_tax'],
				'cart_tax'             => $data['cart_tax'],
				'total'                => $data['total'],
				'total_formatted'      => isset( $data['total_formatted'] ) ? $data['total_formatted'] : $data['total'],
				'total_tax'            => $data['total_tax'],
				'prices_include_tax'   => $data['prices_include_tax'],
				'customer_id'          => $data['customer_id'],
				'customer_ip_address'  => $data['customer_ip_address'],
				'customer_user_agent'  => $data['customer_user_agent'],
				'customer_note'        => $data['customer_note'],
				'billing'              => $data['billing'],
				'shipping'             => $data['shipping'],
				'payment_method'       => $data['payment_method'],
				'payment_method_title' => $data['payment_method_title'],
				'transaction_id'       => $data['transaction_id'],
				'date_paid'            => $data['date_paid'],
				'date_paid_gmt'        => $data['date_paid_gmt'],
				'date_completed'       => $data['date_completed'],
				'date_completed_gmt'   => $data['date_completed_gmt'],
				'cart_hash'            => $data['cart_hash'],
				'meta_data'            => $data['meta_data'],
				'refunds'              => $data['refunds'],
			),
			$formatted_line_items,
			$extra_data
		);
	}
}
