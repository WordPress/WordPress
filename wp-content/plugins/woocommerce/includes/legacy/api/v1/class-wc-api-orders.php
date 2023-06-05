<?php
/**
 * WooCommerce API Orders Class
 *
 * Handles requests to the /orders endpoint
 *
 * @author      WooThemes
 * @category    API
 * @package     WooCommerce\RestApi
 * @since       2.1
 * @version     2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_API_Orders extends WC_API_Resource {

	/** @var string $base the route base */
	protected $base = '/orders';

	/**
	 * Register the routes for this class
	 *
	 * GET /orders
	 * GET /orders/count
	 * GET|PUT /orders/<id>
	 * GET /orders/<id>/notes
	 *
	 * @since 2.1
	 * @param array $routes
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET /orders
		$routes[ $this->base ] = array(
			array( array( $this, 'get_orders' ),     WC_API_Server::READABLE ),
		);

		# GET /orders/count
		$routes[ $this->base . '/count' ] = array(
			array( array( $this, 'get_orders_count' ), WC_API_Server::READABLE ),
		);

		# GET|PUT /orders/<id>
		$routes[ $this->base . '/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_order' ),  WC_API_Server::READABLE ),
			array( array( $this, 'edit_order' ), WC_API_Server::EDITABLE | WC_API_Server::ACCEPT_DATA ),
		);

		# GET /orders/<id>/notes
		$routes[ $this->base . '/(?P<id>\d+)/notes' ] = array(
			array( array( $this, 'get_order_notes' ), WC_API_Server::READABLE ),
		);

		return $routes;
	}

	/**
	 * Get all orders
	 *
	 * @since 2.1
	 * @param string $fields
	 * @param array $filter
	 * @param string $status
	 * @param int $page
	 * @return array
	 */
	public function get_orders( $fields = null, $filter = array(), $status = null, $page = 1 ) {

		if ( ! empty( $status ) ) {
			$filter['status'] = $status;
		}

		$filter['page'] = $page;

		$query = $this->query_orders( $filter );

		$orders = array();

		foreach ( $query->posts as $order_id ) {

			if ( ! $this->is_readable( $order_id ) ) {
				continue;
			}

			$orders[] = current( $this->get_order( $order_id, $fields ) );
		}

		$this->server->add_pagination_headers( $query );

		return array( 'orders' => $orders );
	}


	/**
	 * Get the order for the given ID
	 *
	 * @since 2.1
	 * @param int $id the order ID
	 * @param array $fields
	 * @return array|WP_Error
	 */
	public function get_order( $id, $fields = null ) {

		// ensure order ID is valid & user has permission to read
		$id = $this->validate_request( $id, 'shop_order', 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$order      = wc_get_order( $id );
		$order_data = array(
			'id'                        => $order->get_id(),
			'order_number'              => $order->get_order_number(),
			'created_at'                => $this->server->format_datetime( $order->get_date_created() ? $order->get_date_created()->getTimestamp() : 0, false, false ), // API gives UTC times.
			'updated_at'                => $this->server->format_datetime( $order->get_date_modified() ? $order->get_date_modified()->getTimestamp() : 0, false, false ), // API gives UTC times.
			'completed_at'              => $this->server->format_datetime( $order->get_date_completed() ? $order->get_date_completed()->getTimestamp() : 0, false, false ), // API gives UTC times.
			'status'                    => $order->get_status(),
			'currency'                  => $order->get_currency(),
			'total'                     => wc_format_decimal( $order->get_total(), 2 ),
			'subtotal'                  => wc_format_decimal( $this->get_order_subtotal( $order ), 2 ),
			'total_line_items_quantity' => $order->get_item_count(),
			'total_tax'                 => wc_format_decimal( $order->get_total_tax(), 2 ),
			'total_shipping'            => wc_format_decimal( $order->get_shipping_total(), 2 ),
			'cart_tax'                  => wc_format_decimal( $order->get_cart_tax(), 2 ),
			'shipping_tax'              => wc_format_decimal( $order->get_shipping_tax(), 2 ),
			'total_discount'            => wc_format_decimal( $order->get_total_discount(), 2 ),
			'cart_discount'             => wc_format_decimal( 0, 2 ),
			'order_discount'            => wc_format_decimal( 0, 2 ),
			'shipping_methods'          => $order->get_shipping_method(),
			'payment_details' => array(
				'method_id'    => $order->get_payment_method(),
				'method_title' => $order->get_payment_method_title(),
				'paid'         => ! is_null( $order->get_date_paid() ),
			),
			'billing_address' => array(
				'first_name' => $order->get_billing_first_name(),
				'last_name'  => $order->get_billing_last_name(),
				'company'    => $order->get_billing_company(),
				'address_1'  => $order->get_billing_address_1(),
				'address_2'  => $order->get_billing_address_2(),
				'city'       => $order->get_billing_city(),
				'state'      => $order->get_billing_state(),
				'postcode'   => $order->get_billing_postcode(),
				'country'    => $order->get_billing_country(),
				'email'      => $order->get_billing_email(),
				'phone'      => $order->get_billing_phone(),
			),
			'shipping_address' => array(
				'first_name' => $order->get_shipping_first_name(),
				'last_name'  => $order->get_shipping_last_name(),
				'company'    => $order->get_shipping_company(),
				'address_1'  => $order->get_shipping_address_1(),
				'address_2'  => $order->get_shipping_address_2(),
				'city'       => $order->get_shipping_city(),
				'state'      => $order->get_shipping_state(),
				'postcode'   => $order->get_shipping_postcode(),
				'country'    => $order->get_shipping_country(),
			),
			'note'                      => $order->get_customer_note(),
			'customer_ip'               => $order->get_customer_ip_address(),
			'customer_user_agent'       => $order->get_customer_user_agent(),
			'customer_id'               => $order->get_user_id(),
			'view_order_url'            => $order->get_view_order_url(),
			'line_items'                => array(),
			'shipping_lines'            => array(),
			'tax_lines'                 => array(),
			'fee_lines'                 => array(),
			'coupon_lines'              => array(),
		);

		// add line items
		foreach ( $order->get_items() as $item_id => $item ) {
			$product                    = $item->get_product();
			$order_data['line_items'][] = array(
				'id'         => $item_id,
				'subtotal'   => wc_format_decimal( $order->get_line_subtotal( $item ), 2 ),
				'total'      => wc_format_decimal( $order->get_line_total( $item ), 2 ),
				'total_tax'  => wc_format_decimal( $order->get_line_tax( $item ), 2 ),
				'price'      => wc_format_decimal( $order->get_item_total( $item ), 2 ),
				'quantity'   => $item->get_quantity(),
				'tax_class'  => $item->get_tax_class(),
				'name'       => $item->get_name(),
				'product_id' => $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id(),
				'sku'        => is_object( $product ) ? $product->get_sku() : null,
			);
		}

		// add shipping
		foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
			$order_data['shipping_lines'][] = array(
				'id'           => $shipping_item_id,
				'method_id'    => $shipping_item->get_method_id(),
				'method_title' => $shipping_item->get_name(),
				'total'        => wc_format_decimal( $shipping_item->get_total(), 2 ),
			);
		}

		// add taxes
		foreach ( $order->get_tax_totals() as $tax_code => $tax ) {
			$order_data['tax_lines'][] = array(
				'code'     => $tax_code,
				'title'    => $tax->label,
				'total'    => wc_format_decimal( $tax->amount, 2 ),
				'compound' => (bool) $tax->is_compound,
			);
		}

		// add fees
		foreach ( $order->get_fees() as $fee_item_id => $fee_item ) {
			$order_data['fee_lines'][] = array(
				'id'        => $fee_item_id,
				'title'     => $fee_item->get_name(),
				'tax_class' => $fee_item->get_tax_class(),
				'total'     => wc_format_decimal( $order->get_line_total( $fee_item ), 2 ),
				'total_tax' => wc_format_decimal( $order->get_line_tax( $fee_item ), 2 ),
			);
		}

		// add coupons
		foreach ( $order->get_items( 'coupon' ) as $coupon_item_id => $coupon_item ) {
			$order_data['coupon_lines'][] = array(
				'id'     => $coupon_item_id,
				'code'   => $coupon_item->get_code(),
				'amount' => wc_format_decimal( $coupon_item->get_discount(), 2 ),
			);
		}

		return array( 'order' => apply_filters( 'woocommerce_api_order_response', $order_data, $order, $fields, $this->server ) );
	}

	/**
	 * Get the total number of orders
	 *
	 * @since 2.1
	 *
	 * @param string $status
	 * @param array $filter
	 *
	 * @return array|WP_Error
	 */
	public function get_orders_count( $status = null, $filter = array() ) {

		if ( ! empty( $status ) ) {
			$filter['status'] = $status;
		}

		$query = $this->query_orders( $filter );

		if ( ! current_user_can( 'read_private_shop_orders' ) ) {
			return new WP_Error( 'woocommerce_api_user_cannot_read_orders_count', __( 'You do not have permission to read the orders count', 'woocommerce' ), array( 'status' => 401 ) );
		}

		return array( 'count' => (int) $query->found_posts );
	}

	/**
	 * Edit an order
	 *
	 * API v1 only allows updating the status of an order
	 *
	 * @since 2.1
	 * @param int $id the order ID
	 * @param array $data
	 * @return array|WP_Error
	 */
	public function edit_order( $id, $data ) {

		$id = $this->validate_request( $id, 'shop_order', 'edit' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$order = wc_get_order( $id );

		if ( ! empty( $data['status'] ) ) {

			$order->update_status( $data['status'], isset( $data['note'] ) ? $data['note'] : '' );
		}

		return $this->get_order( $id );
	}

	/**
	 * Delete an order
	 *
	 * @param int $id the order ID
	 * @param bool $force true to permanently delete order, false to move to trash
	 * @return array
	 */
	public function delete_order( $id, $force = false ) {

		$id = $this->validate_request( $id, 'shop_order', 'delete' );

		return $this->delete( $id, 'order',  ( 'true' === $force ) );
	}

	/**
	 * Get the admin order notes for an order
	 *
	 * @since 2.1
	 * @param int $id the order ID
	 * @param string $fields fields to include in response
	 * @return array|WP_Error
	 */
	public function get_order_notes( $id, $fields = null ) {

		// ensure ID is valid order ID
		$id = $this->validate_request( $id, 'shop_order', 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$args = array(
			'post_id' => $id,
			'approve' => 'approve',
			'type'    => 'order_note',
		);

		remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

		$notes = get_comments( $args );

		add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

		$order_notes = array();

		foreach ( $notes as $note ) {

			$order_notes[] = array(
				'id'            => $note->comment_ID,
				'created_at'    => $this->server->format_datetime( $note->comment_date_gmt ),
				'note'          => $note->comment_content,
				'customer_note' => (bool) get_comment_meta( $note->comment_ID, 'is_customer_note', true ),
			);
		}

		return array( 'order_notes' => apply_filters( 'woocommerce_api_order_notes_response', $order_notes, $id, $fields, $notes, $this->server ) );
	}

	/**
	 * Helper method to get order post objects
	 *
	 * @since 2.1
	 * @param array $args request arguments for filtering query
	 * @return WP_Query
	 */
	private function query_orders( $args ) {

		// set base query arguments
		$query_args = array(
			'fields'      => 'ids',
			'post_type'   => 'shop_order',
			'post_status' => array_keys( wc_get_order_statuses() ),
		);

		// add status argument
		if ( ! empty( $args['status'] ) ) {

			$statuses                  = 'wc-' . str_replace( ',', ',wc-', $args['status'] );
			$statuses                  = explode( ',', $statuses );
			$query_args['post_status'] = $statuses;

			unset( $args['status'] );

		}

		$query_args = $this->merge_query_args( $query_args, $args );

		return new WP_Query( $query_args );
	}

	/**
	 * Helper method to get the order subtotal
	 *
	 * @since 2.1
	 * @param WC_Order $order
	 * @return float
	 */
	private function get_order_subtotal( $order ) {
		$subtotal = 0;

		// subtotal
		foreach ( $order->get_items() as $item ) {
			$subtotal += $item->get_subtotal();
		}

		return $subtotal;
	}
}
