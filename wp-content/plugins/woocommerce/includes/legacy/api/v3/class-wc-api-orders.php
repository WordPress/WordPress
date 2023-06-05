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
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_API_Orders extends WC_API_Resource {

	/** @var string $base the route base */
	protected $base = '/orders';

	/** @var string $post_type the custom post type */
	protected $post_type = 'shop_order';

	/**
	 * Register the routes for this class
	 *
	 * GET|POST /orders
	 * GET /orders/count
	 * GET|PUT|DELETE /orders/<id>
	 * GET /orders/<id>/notes
	 *
	 * @since 2.1
	 * @param array $routes
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET|POST /orders
		$routes[ $this->base ] = array(
			array( array( $this, 'get_orders' ),     WC_API_Server::READABLE ),
			array( array( $this, 'create_order' ),   WC_API_Server::CREATABLE | WC_API_Server::ACCEPT_DATA ),
		);

		# GET /orders/count
		$routes[ $this->base . '/count' ] = array(
			array( array( $this, 'get_orders_count' ), WC_API_Server::READABLE ),
		);

		# GET /orders/statuses
		$routes[ $this->base . '/statuses' ] = array(
			array( array( $this, 'get_order_statuses' ), WC_API_Server::READABLE ),
		);

		# GET|PUT|DELETE /orders/<id>
		$routes[ $this->base . '/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_order' ),  WC_API_Server::READABLE ),
			array( array( $this, 'edit_order' ), WC_API_Server::EDITABLE | WC_API_Server::ACCEPT_DATA ),
			array( array( $this, 'delete_order' ), WC_API_Server::DELETABLE ),
		);

		# GET|POST /orders/<id>/notes
		$routes[ $this->base . '/(?P<order_id>\d+)/notes' ] = array(
			array( array( $this, 'get_order_notes' ), WC_API_Server::READABLE ),
			array( array( $this, 'create_order_note' ), WC_API_SERVER::CREATABLE | WC_API_Server::ACCEPT_DATA ),
		);

		# GET|PUT|DELETE /orders/<order_id>/notes/<id>
		$routes[ $this->base . '/(?P<order_id>\d+)/notes/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_order_note' ), WC_API_Server::READABLE ),
			array( array( $this, 'edit_order_note' ), WC_API_SERVER::EDITABLE | WC_API_Server::ACCEPT_DATA ),
			array( array( $this, 'delete_order_note' ), WC_API_SERVER::DELETABLE ),
		);

		# GET|POST /orders/<order_id>/refunds
		$routes[ $this->base . '/(?P<order_id>\d+)/refunds' ] = array(
			array( array( $this, 'get_order_refunds' ), WC_API_Server::READABLE ),
			array( array( $this, 'create_order_refund' ), WC_API_SERVER::CREATABLE | WC_API_Server::ACCEPT_DATA ),
		);

		# GET|PUT|DELETE /orders/<order_id>/refunds/<id>
		$routes[ $this->base . '/(?P<order_id>\d+)/refunds/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_order_refund' ), WC_API_Server::READABLE ),
			array( array( $this, 'edit_order_refund' ), WC_API_SERVER::EDITABLE | WC_API_Server::ACCEPT_DATA ),
			array( array( $this, 'delete_order_refund' ), WC_API_SERVER::DELETABLE ),
		);

		# POST|PUT /orders/bulk
		$routes[ $this->base . '/bulk' ] = array(
			array( array( $this, 'bulk' ), WC_API_Server::EDITABLE | WC_API_Server::ACCEPT_DATA ),
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

			$orders[] = current( $this->get_order( $order_id, $fields, $filter ) );
		}

		$this->server->add_pagination_headers( $query );

		return array( 'orders' => $orders );
	}


	/**
	 * Get the order for the given ID.
	 *
	 * @since 2.1
	 * @param int $id The order ID.
	 * @param array $fields Request fields.
	 * @param array $filter Request filters.
	 * @return array|WP_Error
	 */
	public function get_order( $id, $fields = null, $filter = array() ) {

		// Ensure order ID is valid & user has permission to read.
		$id = $this->validate_request( $id, $this->post_type, 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		// Get the decimal precession.
		$dp     = ( isset( $filter['dp'] ) ? intval( $filter['dp'] ) : 2 );
		$order  = wc_get_order( $id );
		$expand = array();

		if ( ! empty( $filter['expand'] ) ) {
			$expand = explode( ',', $filter['expand'] );
		}

		$order_data = array(
			'id'                        => $order->get_id(),
			'order_number'              => $order->get_order_number(),
			'order_key'                 => $order->get_order_key(),
			'created_at'                => $this->server->format_datetime( $order->get_date_created() ? $order->get_date_created()->getTimestamp() : 0, false, false ), // API gives UTC times.
			'updated_at'                => $this->server->format_datetime( $order->get_date_modified() ? $order->get_date_modified()->getTimestamp() : 0, false, false ), // API gives UTC times.
			'completed_at'              => $this->server->format_datetime( $order->get_date_completed() ? $order->get_date_completed()->getTimestamp() : 0, false, false ), // API gives UTC times.
			'status'                    => $order->get_status(),
			'currency'                  => $order->get_currency(),
			'total'                     => wc_format_decimal( $order->get_total(), $dp ),
			'subtotal'                  => wc_format_decimal( $order->get_subtotal(), $dp ),
			'total_line_items_quantity' => $order->get_item_count(),
			'total_tax'                 => wc_format_decimal( $order->get_total_tax(), $dp ),
			'total_shipping'            => wc_format_decimal( $order->get_shipping_total(), $dp ),
			'cart_tax'                  => wc_format_decimal( $order->get_cart_tax(), $dp ),
			'shipping_tax'              => wc_format_decimal( $order->get_shipping_tax(), $dp ),
			'total_discount'            => wc_format_decimal( $order->get_total_discount(), $dp ),
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

		// Add line items.
		foreach ( $order->get_items() as $item_id => $item ) {
			$product    = $item->get_product();
			$hideprefix = ( isset( $filter['all_item_meta'] ) && 'true' === $filter['all_item_meta'] ) ? null : '_';
			$item_meta  = $item->get_all_formatted_meta_data( $hideprefix );

			foreach ( $item_meta as $key => $values ) {
				$item_meta[ $key ]->label = $values->display_key;
				unset( $item_meta[ $key ]->display_key );
				unset( $item_meta[ $key ]->display_value );
			}

			$line_item = array(
				'id'           => $item_id,
				'subtotal'     => wc_format_decimal( $order->get_line_subtotal( $item, false, false ), $dp ),
				'subtotal_tax' => wc_format_decimal( $item->get_subtotal_tax(), $dp ),
				'total'        => wc_format_decimal( $order->get_line_total( $item, false, false ), $dp ),
				'total_tax'    => wc_format_decimal( $item->get_total_tax(), $dp ),
				'price'        => wc_format_decimal( $order->get_item_total( $item, false, false ), $dp ),
				'quantity'     => $item->get_quantity(),
				'tax_class'    => $item->get_tax_class(),
				'name'         => $item->get_name(),
				'product_id'   => $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id(),
				'sku'          => is_object( $product ) ? $product->get_sku() : null,
				'meta'         => array_values( $item_meta ),
			);

			if ( in_array( 'products', $expand ) && is_object( $product ) ) {
				$_product_data = WC()->api->WC_API_Products->get_product( $product->get_id() );

				if ( isset( $_product_data['product'] ) ) {
					$line_item['product_data'] = $_product_data['product'];
				}
			}

			$order_data['line_items'][] = $line_item;
		}

		// Add shipping.
		foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
			$order_data['shipping_lines'][] = array(
				'id'           => $shipping_item_id,
				'method_id'    => $shipping_item->get_method_id(),
				'method_title' => $shipping_item->get_name(),
				'total'        => wc_format_decimal( $shipping_item->get_total(), $dp ),
			);
		}

		// Add taxes.
		foreach ( $order->get_tax_totals() as $tax_code => $tax ) {
			$tax_line = array(
				'id'       => $tax->id,
				'rate_id'  => $tax->rate_id,
				'code'     => $tax_code,
				'title'    => $tax->label,
				'total'    => wc_format_decimal( $tax->amount, $dp ),
				'compound' => (bool) $tax->is_compound,
			);

			if ( in_array( 'taxes', $expand ) ) {
				$_rate_data = WC()->api->WC_API_Taxes->get_tax( $tax->rate_id );

				if ( isset( $_rate_data['tax'] ) ) {
					$tax_line['rate_data'] = $_rate_data['tax'];
				}
			}

			$order_data['tax_lines'][] = $tax_line;
		}

		// Add fees.
		foreach ( $order->get_fees() as $fee_item_id => $fee_item ) {
			$order_data['fee_lines'][] = array(
				'id'        => $fee_item_id,
				'title'     => $fee_item->get_name(),
				'tax_class' => $fee_item->get_tax_class(),
				'total'     => wc_format_decimal( $order->get_line_total( $fee_item ), $dp ),
				'total_tax' => wc_format_decimal( $order->get_line_tax( $fee_item ), $dp ),
			);
		}

		// Add coupons.
		foreach ( $order->get_items( 'coupon' ) as $coupon_item_id => $coupon_item ) {
			$coupon_line = array(
				'id'     => $coupon_item_id,
				'code'   => $coupon_item->get_code(),
				'amount' => wc_format_decimal( $coupon_item->get_discount(), $dp ),
			);

			if ( in_array( 'coupons', $expand ) ) {
				$_coupon_data = WC()->api->WC_API_Coupons->get_coupon_by_code( $coupon_item->get_code() );

				if ( ! is_wp_error( $_coupon_data ) && isset( $_coupon_data['coupon'] ) ) {
					$coupon_line['coupon_data'] = $_coupon_data['coupon'];
				}
			}

			$order_data['coupon_lines'][] = $coupon_line;
		}

		return array( 'order' => apply_filters( 'woocommerce_api_order_response', $order_data, $order, $fields, $this->server ) );
	}

	/**
	 * Get the total number of orders
	 *
	 * @since 2.4
	 *
	 * @param string $status
	 * @param array $filter
	 *
	 * @return array|WP_Error
	 */
	public function get_orders_count( $status = null, $filter = array() ) {

		try {
			if ( ! current_user_can( 'read_private_shop_orders' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_read_orders_count', __( 'You do not have permission to read the orders count', 'woocommerce' ), 401 );
			}

			if ( ! empty( $status ) ) {

				if ( 'any' === $status ) {

					$order_statuses = array();

					foreach ( wc_get_order_statuses() as $slug => $name ) {
						$filter['status'] = str_replace( 'wc-', '', $slug );
						$query = $this->query_orders( $filter );
						$order_statuses[ str_replace( 'wc-', '', $slug ) ] = (int) $query->found_posts;
					}

					return array( 'count' => $order_statuses );

				} else {
					$filter['status'] = $status;
				}
			}

			$query = $this->query_orders( $filter );

			return array( 'count' => (int) $query->found_posts );

		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Get a list of valid order statuses
	 *
	 * Note this requires no specific permissions other than being an authenticated
	 * API user. Order statuses (particularly custom statuses) could be considered
	 * private information which is why it's not in the API index.
	 *
	 * @since 2.1
	 * @return array
	 */
	public function get_order_statuses() {

		$order_statuses = array();

		foreach ( wc_get_order_statuses() as $slug => $name ) {
			$order_statuses[ str_replace( 'wc-', '', $slug ) ] = $name;
		}

		return array( 'order_statuses' => apply_filters( 'woocommerce_api_order_statuses_response', $order_statuses, $this ) );
	}

	/**
	 * Create an order
	 *
	 * @since 2.2
	 * @param array $data raw order data
	 * @return array|WP_Error
	 */
	public function create_order( $data ) {
		global $wpdb;

		try {
			if ( ! isset( $data['order'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_order_data', sprintf( __( 'No %1$s data specified to create %1$s', 'woocommerce' ), 'order' ), 400 );
			}

			$data = $data['order'];

			// permission check
			if ( ! current_user_can( 'publish_shop_orders' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_create_order', __( 'You do not have permission to create orders', 'woocommerce' ), 401 );
			}

			$data = apply_filters( 'woocommerce_api_create_order_data', $data, $this );

			// default order args, note that status is checked for validity in wc_create_order()
			$default_order_args = array(
				'status'        => isset( $data['status'] ) ? $data['status'] : '',
				'customer_note' => isset( $data['note'] ) ? $data['note'] : null,
			);

			// if creating order for existing customer
			if ( ! empty( $data['customer_id'] ) ) {

				// make sure customer exists
				if ( false === get_user_by( 'id', $data['customer_id'] ) ) {
					throw new WC_API_Exception( 'woocommerce_api_invalid_customer_id', __( 'Customer ID is invalid.', 'woocommerce' ), 400 );
				}

				$default_order_args['customer_id'] = $data['customer_id'];
			}

			// create the pending order
			$order = $this->create_base_order( $default_order_args, $data );

			if ( is_wp_error( $order ) ) {
				throw new WC_API_Exception( 'woocommerce_api_cannot_create_order', sprintf( __( 'Cannot create order: %s', 'woocommerce' ), implode( ', ', $order->get_error_messages() ) ), 400 );
			}

			// billing/shipping addresses
			$this->set_order_addresses( $order, $data );

			$lines = array(
				'line_item' => 'line_items',
				'shipping'  => 'shipping_lines',
				'fee'       => 'fee_lines',
				'coupon'    => 'coupon_lines',
			);

			foreach ( $lines as $line_type => $line ) {

				if ( isset( $data[ $line ] ) && is_array( $data[ $line ] ) ) {

					$set_item = "set_{$line_type}";

					foreach ( $data[ $line ] as $item ) {

						$this->$set_item( $order, $item, 'create' );
					}
				}
			}

			// set is vat exempt
			if ( isset( $data['is_vat_exempt'] ) ) {
				update_post_meta( $order->get_id(), '_is_vat_exempt', $data['is_vat_exempt'] ? 'yes' : 'no' );
			}

			// calculate totals and set them
			$order->calculate_totals();

			// payment method (and payment_complete() if `paid` == true)
			if ( isset( $data['payment_details'] ) && is_array( $data['payment_details'] ) ) {

				// method ID & title are required
				if ( empty( $data['payment_details']['method_id'] ) || empty( $data['payment_details']['method_title'] ) ) {
					throw new WC_API_Exception( 'woocommerce_invalid_payment_details', __( 'Payment method ID and title are required', 'woocommerce' ), 400 );
				}

				update_post_meta( $order->get_id(), '_payment_method', $data['payment_details']['method_id'] );
				update_post_meta( $order->get_id(), '_payment_method_title', sanitize_text_field( $data['payment_details']['method_title'] ) );

				// mark as paid if set
				if ( isset( $data['payment_details']['paid'] ) && true === $data['payment_details']['paid'] ) {
					$order->payment_complete( isset( $data['payment_details']['transaction_id'] ) ? $data['payment_details']['transaction_id'] : '' );
				}
			}

			// set order currency
			if ( isset( $data['currency'] ) ) {

				if ( ! array_key_exists( $data['currency'], get_woocommerce_currencies() ) ) {
					throw new WC_API_Exception( 'woocommerce_invalid_order_currency', __( 'Provided order currency is invalid.', 'woocommerce' ), 400 );
				}

				update_post_meta( $order->get_id(), '_order_currency', $data['currency'] );
			}

			// set order meta
			if ( isset( $data['order_meta'] ) && is_array( $data['order_meta'] ) ) {
				$this->set_order_meta( $order->get_id(), $data['order_meta'] );
			}

			// HTTP 201 Created
			$this->server->send_status( 201 );

			wc_delete_shop_order_transients( $order );

			do_action( 'woocommerce_api_create_order', $order->get_id(), $data, $this );
			do_action( 'woocommerce_new_order', $order->get_id() );

			return $this->get_order( $order->get_id() );
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => 400 ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Creates new WC_Order.
	 *
	 * Requires a separate function for classes that extend WC_API_Orders.
	 *
	 * @since 2.3
	 *
	 * @param $args array
	 * @param $data
	 *
	 * @return WC_Order
	 */
	protected function create_base_order( $args, $data ) {
		return wc_create_order( $args );
	}

	/**
	 * Edit an order
	 *
	 * @since 2.2
	 * @param int $id the order ID
	 * @param array $data
	 * @return array|WP_Error
	 */
	public function edit_order( $id, $data ) {
		try {
			if ( ! isset( $data['order'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_order_data', sprintf( __( 'No %1$s data specified to edit %1$s', 'woocommerce' ), 'order' ), 400 );
			}

			$data = $data['order'];

			$update_totals = false;

			$id = $this->validate_request( $id, $this->post_type, 'edit' );

			if ( is_wp_error( $id ) ) {
				return $id;
			}

			$data  = apply_filters( 'woocommerce_api_edit_order_data', $data, $id, $this );
			$order = wc_get_order( $id );

			if ( empty( $order ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_id', __( 'Order ID is invalid', 'woocommerce' ), 400 );
			}

			$order_args = array( 'order_id' => $order->get_id() );

			// Customer note.
			if ( isset( $data['note'] ) ) {
				$order_args['customer_note'] = $data['note'];
			}

			// Customer ID.
			if ( isset( $data['customer_id'] ) && $data['customer_id'] != $order->get_user_id() ) {
				// Make sure customer exists.
				if ( false === get_user_by( 'id', $data['customer_id'] ) ) {
					throw new WC_API_Exception( 'woocommerce_api_invalid_customer_id', __( 'Customer ID is invalid.', 'woocommerce' ), 400 );
				}

				update_post_meta( $order->get_id(), '_customer_user', $data['customer_id'] );
			}

			// Billing/shipping address.
			$this->set_order_addresses( $order, $data );

			$lines = array(
				'line_item' => 'line_items',
				'shipping'  => 'shipping_lines',
				'fee'       => 'fee_lines',
				'coupon'    => 'coupon_lines',
			);

			foreach ( $lines as $line_type => $line ) {

				if ( isset( $data[ $line ] ) && is_array( $data[ $line ] ) ) {

					$update_totals = true;

					foreach ( $data[ $line ] as $item ) {
						// Item ID is always required.
						if ( ! array_key_exists( 'id', $item ) ) {
							$item['id'] = null;
						}

						// Create item.
						if ( is_null( $item['id'] ) ) {
							$this->set_item( $order, $line_type, $item, 'create' );
						} elseif ( $this->item_is_null( $item ) ) {
							// Delete item.
							wc_delete_order_item( $item['id'] );
						} else {
							// Update item.
							$this->set_item( $order, $line_type, $item, 'update' );
						}
					}
				}
			}

			// Payment method (and payment_complete() if `paid` == true and order needs payment).
			if ( isset( $data['payment_details'] ) && is_array( $data['payment_details'] ) ) {

				// Method ID.
				if ( isset( $data['payment_details']['method_id'] ) ) {
					update_post_meta( $order->get_id(), '_payment_method', $data['payment_details']['method_id'] );
				}

				// Method title.
				if ( isset( $data['payment_details']['method_title'] ) ) {
					update_post_meta( $order->get_id(), '_payment_method_title', sanitize_text_field( $data['payment_details']['method_title'] ) );
				}

				// Mark as paid if set.
				if ( $order->needs_payment() && isset( $data['payment_details']['paid'] ) && true === $data['payment_details']['paid'] ) {
					$order->payment_complete( isset( $data['payment_details']['transaction_id'] ) ? $data['payment_details']['transaction_id'] : '' );
				}
			}

			// Set order currency.
			if ( isset( $data['currency'] ) ) {
				if ( ! array_key_exists( $data['currency'], get_woocommerce_currencies() ) ) {
					throw new WC_API_Exception( 'woocommerce_invalid_order_currency', __( 'Provided order currency is invalid.', 'woocommerce' ), 400 );
				}

				update_post_meta( $order->get_id(), '_order_currency', $data['currency'] );
			}

			// If items have changed, recalculate order totals.
			if ( $update_totals ) {
				$order->calculate_totals();
			}

			// Update order meta.
			if ( isset( $data['order_meta'] ) && is_array( $data['order_meta'] ) ) {
				$this->set_order_meta( $order->get_id(), $data['order_meta'] );
			}

			// Update the order post to set customer note/modified date.
			wc_update_order( $order_args );

			// Order status.
			if ( ! empty( $data['status'] ) ) {
				// Refresh the order instance.
				$order = wc_get_order( $order->get_id() );
				$order->update_status( $data['status'], isset( $data['status_note'] ) ? $data['status_note'] : '', true );
			}

			wc_delete_shop_order_transients( $order );

			do_action( 'woocommerce_api_edit_order', $order->get_id(), $data, $this );
			do_action( 'woocommerce_update_order', $order->get_id() );

			return $this->get_order( $id );
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => 400 ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Delete an order
	 *
	 * @param int $id the order ID
	 * @param bool $force true to permanently delete order, false to move to trash
	 * @return array|WP_Error
	 */
	public function delete_order( $id, $force = false ) {

		$id = $this->validate_request( $id, $this->post_type, 'delete' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		wc_delete_shop_order_transients( $id );

		do_action( 'woocommerce_api_delete_order', $id, $this );

		return $this->delete( $id, 'order',  ( 'true' === $force ) );
	}

	/**
	 * Helper method to get order post objects
	 *
	 * @since 2.1
	 * @param array $args request arguments for filtering query
	 * @return WP_Query
	 */
	protected function query_orders( $args ) {

		// set base query arguments
		$query_args = array(
			'fields'      => 'ids',
			'post_type'   => $this->post_type,
			'post_status' => array_keys( wc_get_order_statuses() ),
		);

		// add status argument
		if ( ! empty( $args['status'] ) ) {
			$statuses                  = 'wc-' . str_replace( ',', ',wc-', $args['status'] );
			$statuses                  = explode( ',', $statuses );
			$query_args['post_status'] = $statuses;

			unset( $args['status'] );
		}

		if ( ! empty( $args['customer_id'] ) ) {
			$query_args['meta_query'] = array(
				array(
					'key'     => '_customer_user',
					'value'   => absint( $args['customer_id'] ),
					'compare' => '=',
				),
			);
		}

		$query_args = $this->merge_query_args( $query_args, $args );

		return new WP_Query( $query_args );
	}

	/**
	 * Helper method to set/update the billing & shipping addresses for
	 * an order
	 *
	 * @since 2.1
	 * @param \WC_Order $order
	 * @param array $data
	 */
	protected function set_order_addresses( $order, $data ) {

		$address_fields = array(
			'first_name',
			'last_name',
			'company',
			'email',
			'phone',
			'address_1',
			'address_2',
			'city',
			'state',
			'postcode',
			'country',
		);

		$billing_address = $shipping_address = array();

		// billing address
		if ( isset( $data['billing_address'] ) && is_array( $data['billing_address'] ) ) {

			foreach ( $address_fields as $field ) {

				if ( isset( $data['billing_address'][ $field ] ) ) {
					$billing_address[ $field ] = wc_clean( $data['billing_address'][ $field ] );
				}
			}

			unset( $address_fields['email'] );
			unset( $address_fields['phone'] );
		}

		// shipping address
		if ( isset( $data['shipping_address'] ) && is_array( $data['shipping_address'] ) ) {

			foreach ( $address_fields as $field ) {

				if ( isset( $data['shipping_address'][ $field ] ) ) {
					$shipping_address[ $field ] = wc_clean( $data['shipping_address'][ $field ] );
				}
			}
		}

		$this->update_address( $order, $billing_address, 'billing' );
		$this->update_address( $order, $shipping_address, 'shipping' );

		// update user meta
		if ( $order->get_user_id() ) {
			foreach ( $billing_address as $key => $value ) {
				update_user_meta( $order->get_user_id(), 'billing_' . $key, $value );
			}
			foreach ( $shipping_address as $key => $value ) {
				update_user_meta( $order->get_user_id(), 'shipping_' . $key, $value );
			}
		}
	}

	/**
	 * Update address.
	 *
	 * @param WC_Order $order
	 * @param array $posted
	 * @param string $type
	 */
	protected function update_address( $order, $posted, $type = 'billing' ) {
		foreach ( $posted as $key => $value ) {
			if ( is_callable( array( $order, "set_{$type}_{$key}" ) ) ) {
				$order->{"set_{$type}_{$key}"}( $value );
			}
		}
	}

	/**
	 * Helper method to add/update order meta, with two restrictions:
	 *
	 * 1) Only non-protected meta (no leading underscore) can be set
	 * 2) Meta values must be scalar (int, string, bool)
	 *
	 * @since 2.2
	 * @param int $order_id valid order ID
	 * @param array $order_meta order meta in array( 'meta_key' => 'meta_value' ) format
	 */
	protected function set_order_meta( $order_id, $order_meta ) {

		foreach ( $order_meta as $meta_key => $meta_value ) {

			if ( is_string( $meta_key ) && ! is_protected_meta( $meta_key ) && is_scalar( $meta_value ) ) {
				update_post_meta( $order_id, $meta_key, $meta_value );
			}
		}
	}

	/**
	 * Helper method to check if the resource ID associated with the provided item is null
	 *
	 * Items can be deleted by setting the resource ID to null
	 *
	 * @since 2.2
	 * @param array $item item provided in the request body
	 * @return bool true if the item resource ID is null, false otherwise
	 */
	protected function item_is_null( $item ) {

		$keys = array( 'product_id', 'method_id', 'title', 'code' );

		foreach ( $keys as $key ) {
			if ( array_key_exists( $key, $item ) && is_null( $item[ $key ] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Wrapper method to create/update order items
	 *
	 * When updating, the item ID provided is checked to ensure it is associated
	 * with the order.
	 *
	 * @since 2.2
	 * @param \WC_Order $order order
	 * @param string $item_type
	 * @param array $item item provided in the request body
	 * @param string $action either 'create' or 'update'
	 * @throws WC_API_Exception if item ID is not associated with order
	 */
	protected function set_item( $order, $item_type, $item, $action ) {
		global $wpdb;

		$set_method = "set_{$item_type}";

		// verify provided line item ID is associated with order
		if ( 'update' === $action ) {

			$result = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d AND order_id = %d",
				absint( $item['id'] ),
				absint( $order->get_id() )
			) );

			if ( is_null( $result ) ) {
				throw new WC_API_Exception( 'woocommerce_invalid_item_id', __( 'Order item ID provided is not associated with order.', 'woocommerce' ), 400 );
			}
		}

		$this->$set_method( $order, $item, $action );
	}

	/**
	 * Create or update a line item
	 *
	 * @since 2.2
	 * @param \WC_Order $order
	 * @param array $item line item data
	 * @param string $action 'create' to add line item or 'update' to update it
	 * @throws WC_API_Exception invalid data, server error
	 */
	protected function set_line_item( $order, $item, $action ) {
		$creating  = ( 'create' === $action );

		// product is always required
		if ( ! isset( $item['product_id'] ) && ! isset( $item['sku'] ) ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_product_id', __( 'Product ID or SKU is required', 'woocommerce' ), 400 );
		}

		// when updating, ensure product ID provided matches
		if ( 'update' === $action ) {

			$item_product_id   = wc_get_order_item_meta( $item['id'], '_product_id' );
			$item_variation_id = wc_get_order_item_meta( $item['id'], '_variation_id' );

			if ( $item['product_id'] != $item_product_id && $item['product_id'] != $item_variation_id ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_product_id', __( 'Product ID provided does not match this line item', 'woocommerce' ), 400 );
			}
		}

		if ( isset( $item['product_id'] ) ) {
			$product_id = $item['product_id'];
		} elseif ( isset( $item['sku'] ) ) {
			$product_id = wc_get_product_id_by_sku( $item['sku'] );
		}

		// variations must each have a key & value
		$variation_id = 0;
		if ( isset( $item['variations'] ) && is_array( $item['variations'] ) ) {
			foreach ( $item['variations'] as $key => $value ) {
				if ( ! $key || ! $value ) {
					throw new WC_API_Exception( 'woocommerce_api_invalid_product_variation', __( 'The product variation is invalid', 'woocommerce' ), 400 );
				}
			}
			$variation_id = $this->get_variation_id( wc_get_product( $product_id ), $item['variations'] );
		}

		$product = wc_get_product( $variation_id ? $variation_id : $product_id );

		// must be a valid WC_Product
		if ( ! is_object( $product ) ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_product', __( 'Product is invalid.', 'woocommerce' ), 400 );
		}

		// quantity must be positive float
		if ( isset( $item['quantity'] ) && floatval( $item['quantity'] ) <= 0 ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_product_quantity', __( 'Product quantity must be a positive float.', 'woocommerce' ), 400 );
		}

		// quantity is required when creating
		if ( $creating && ! isset( $item['quantity'] ) ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_product_quantity', __( 'Product quantity is required.', 'woocommerce' ), 400 );
		}

		// quantity
		if ( $creating ) {
			$line_item = new WC_Order_Item_Product();
		} else {
			$line_item = new WC_Order_Item_Product( $item['id'] );
		}

		$line_item->set_product( $product );
		$line_item->set_order_id( $order->get_id() );

		if ( isset( $item['quantity'] ) ) {
			$line_item->set_quantity( $item['quantity'] );
		}
		if ( isset( $item['total'] ) ) {
			$line_item->set_total( floatval( $item['total'] ) );
		} elseif ( $creating ) {
			$total = wc_get_price_excluding_tax( $product, array( 'qty' => $line_item->get_quantity() ) );
			$line_item->set_total( $total );
			$line_item->set_subtotal( $total );
		}
		if ( isset( $item['total_tax'] ) ) {
			$line_item->set_total_tax( floatval( $item['total_tax'] ) );
		}
		if ( isset( $item['subtotal'] ) ) {
			$line_item->set_subtotal( floatval( $item['subtotal'] ) );
		}
		if ( isset( $item['subtotal_tax'] ) ) {
			$line_item->set_subtotal_tax( floatval( $item['subtotal_tax'] ) );
		}
		if ( $variation_id ) {
			$line_item->set_variation_id( $variation_id );
			$line_item->set_variation( $item['variations'] );
		}

		// Save or add to order.
		if ( $creating ) {
			$order->add_item( $line_item );
		} else {
			$item_id = $line_item->save();

			if ( ! $item_id ) {
				throw new WC_API_Exception( 'woocommerce_cannot_create_line_item', __( 'Cannot create line item, try again.', 'woocommerce' ), 500 );
			}
		}
	}

	/**
	 * Given a product ID & API provided variations, find the correct variation ID to use for calculation
	 * We can't just trust input from the API to pass a variation_id manually, otherwise you could pass
	 * the cheapest variation ID but provide other information so we have to look up the variation ID.
	 *
	 * @param  WC_Product $product Product instance
	 * @param array $variations
	 *
	 * @return int Returns an ID if a valid variation was found for this product
	 */
	public function get_variation_id( $product, $variations = array() ) {
		$variation_id = null;
		$variations_normalized = array();

		if ( $product->is_type( 'variable' ) && $product->has_child() ) {
			if ( isset( $variations ) && is_array( $variations ) ) {
				// start by normalizing the passed variations
				foreach ( $variations as $key => $value ) {
					$key = str_replace( 'attribute_', '', wc_attribute_taxonomy_slug( $key ) ); // from get_attributes in class-wc-api-products.php
					$variations_normalized[ $key ] = strtolower( $value );
				}
				// now search through each product child and see if our passed variations match anything
				foreach ( $product->get_children() as $variation ) {
					$meta = array();
					foreach ( get_post_meta( $variation ) as $key => $value ) {
						$value = $value[0];
						$key = str_replace( 'attribute_', '', wc_attribute_taxonomy_slug( $key ) );
						$meta[ $key ] = strtolower( $value );
					}
					// if the variation array is a part of the $meta array, we found our match
					if ( $this->array_contains( $variations_normalized, $meta ) ) {
						$variation_id = $variation;
						break;
					}
				}
			}
		}

		return $variation_id;
	}

	/**
	 * Utility function to see if the meta array contains data from variations
	 *
	 * @param array $needles
	 * @param array $haystack
	 *
	 * @return bool
	 */
	protected function array_contains( $needles, $haystack ) {
		foreach ( $needles as $key => $value ) {
			if ( $haystack[ $key ] !== $value ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Create or update an order shipping method
	 *
	 * @since 2.2
	 * @param \WC_Order $order
	 * @param array $shipping item data
	 * @param string $action 'create' to add shipping or 'update' to update it
	 * @throws WC_API_Exception invalid data, server error
	 */
	protected function set_shipping( $order, $shipping, $action ) {

		// total must be a positive float
		if ( isset( $shipping['total'] ) && floatval( $shipping['total'] ) < 0 ) {
			throw new WC_API_Exception( 'woocommerce_invalid_shipping_total', __( 'Shipping total must be a positive amount.', 'woocommerce' ), 400 );
		}

		if ( 'create' === $action ) {

			// method ID is required
			if ( ! isset( $shipping['method_id'] ) ) {
				throw new WC_API_Exception( 'woocommerce_invalid_shipping_item', __( 'Shipping method ID is required.', 'woocommerce' ), 400 );
			}

			$rate = new WC_Shipping_Rate( $shipping['method_id'], isset( $shipping['method_title'] ) ? $shipping['method_title'] : '', isset( $shipping['total'] ) ? floatval( $shipping['total'] ) : 0, array(), $shipping['method_id'] );
			$item = new WC_Order_Item_Shipping();
			$item->set_order_id( $order->get_id() );
			$item->set_shipping_rate( $rate );
			$order->add_item( $item );
		} else {

			$item = new WC_Order_Item_Shipping( $shipping['id'] );

			if ( isset( $shipping['method_id'] ) ) {
				$item->set_method_id( $shipping['method_id'] );
			}

			if ( isset( $shipping['method_title'] ) ) {
				$item->set_method_title( $shipping['method_title'] );
			}

			if ( isset( $shipping['total'] ) ) {
				$item->set_total( floatval( $shipping['total'] ) );
			}

			$shipping_id = $item->save();

			if ( ! $shipping_id ) {
				throw new WC_API_Exception( 'woocommerce_cannot_update_shipping', __( 'Cannot update shipping method, try again.', 'woocommerce' ), 500 );
			}
		}
	}

	/**
	 * Create or update an order fee
	 *
	 * @since 2.2
	 * @param \WC_Order $order
	 * @param array $fee item data
	 * @param string $action 'create' to add fee or 'update' to update it
	 * @throws WC_API_Exception invalid data, server error
	 */
	protected function set_fee( $order, $fee, $action ) {

		if ( 'create' === $action ) {

			// fee title is required
			if ( ! isset( $fee['title'] ) ) {
				throw new WC_API_Exception( 'woocommerce_invalid_fee_item', __( 'Fee title is required', 'woocommerce' ), 400 );
			}

			$item = new WC_Order_Item_Fee();
			$item->set_order_id( $order->get_id() );
			$item->set_name( wc_clean( $fee['title'] ) );
			$item->set_total( isset( $fee['total'] ) ? floatval( $fee['total'] ) : 0 );

			// if taxable, tax class and total are required
			if ( ! empty( $fee['taxable'] ) ) {
				if ( ! isset( $fee['tax_class'] ) ) {
					throw new WC_API_Exception( 'woocommerce_invalid_fee_item', __( 'Fee tax class is required when fee is taxable.', 'woocommerce' ), 400 );
				}

				$item->set_tax_status( 'taxable' );
				$item->set_tax_class( $fee['tax_class'] );

				if ( isset( $fee['total_tax'] ) ) {
					$item->set_total_tax( isset( $fee['total_tax'] ) ? wc_format_refund_total( $fee['total_tax'] ) : 0 );
				}

				if ( isset( $fee['tax_data'] ) ) {
					$item->set_total_tax( wc_format_refund_total( array_sum( $fee['tax_data'] ) ) );
					$item->set_taxes( array_map( 'wc_format_refund_total', $fee['tax_data'] ) );
				}
			}

			$order->add_item( $item );
		} else {

			$item = new WC_Order_Item_Fee( $fee['id'] );

			if ( isset( $fee['title'] ) ) {
				$item->set_name( wc_clean( $fee['title'] ) );
			}

			if ( isset( $fee['tax_class'] ) ) {
				$item->set_tax_class( $fee['tax_class'] );
			}

			if ( isset( $fee['total'] ) ) {
				$item->set_total( floatval( $fee['total'] ) );
			}

			if ( isset( $fee['total_tax'] ) ) {
				$item->set_total_tax( floatval( $fee['total_tax'] ) );
			}

			$fee_id = $item->save();

			if ( ! $fee_id ) {
				throw new WC_API_Exception( 'woocommerce_cannot_update_fee', __( 'Cannot update fee, try again.', 'woocommerce' ), 500 );
			}
		}
	}

	/**
	 * Create or update an order coupon
	 *
	 * @since 2.2
	 * @param \WC_Order $order
	 * @param array $coupon item data
	 * @param string $action 'create' to add coupon or 'update' to update it
	 * @throws WC_API_Exception invalid data, server error
	 */
	protected function set_coupon( $order, $coupon, $action ) {

		// coupon amount must be positive float
		if ( isset( $coupon['amount'] ) && floatval( $coupon['amount'] ) < 0 ) {
			throw new WC_API_Exception( 'woocommerce_invalid_coupon_total', __( 'Coupon discount total must be a positive amount.', 'woocommerce' ), 400 );
		}

		if ( 'create' === $action ) {

			// coupon code is required
			if ( empty( $coupon['code'] ) ) {
				throw new WC_API_Exception( 'woocommerce_invalid_coupon_coupon', __( 'Coupon code is required.', 'woocommerce' ), 400 );
			}

			$item = new WC_Order_Item_Coupon();
			$item->set_props( array(
				'code'         => $coupon['code'],
				'discount'     => isset( $coupon['amount'] ) ? floatval( $coupon['amount'] ) : 0,
				'discount_tax' => 0,
				'order_id'     => $order->get_id(),
			) );
			$order->add_item( $item );
		} else {

			$item = new WC_Order_Item_Coupon( $coupon['id'] );

			if ( isset( $coupon['code'] ) ) {
				$item->set_code( $coupon['code'] );
			}

			if ( isset( $coupon['amount'] ) ) {
				$item->set_discount( floatval( $coupon['amount'] ) );
			}

			$coupon_id = $item->save();

			if ( ! $coupon_id ) {
				throw new WC_API_Exception( 'woocommerce_cannot_update_order_coupon', __( 'Cannot update coupon, try again.', 'woocommerce' ), 500 );
			}
		}
	}

	/**
	 * Get the admin order notes for an order
	 *
	 * @since 2.1
	 * @param string $order_id order ID
	 * @param string|null $fields fields to include in response
	 * @return array|WP_Error
	 */
	public function get_order_notes( $order_id, $fields = null ) {

		// ensure ID is valid order ID
		$order_id = $this->validate_request( $order_id, $this->post_type, 'read' );

		if ( is_wp_error( $order_id ) ) {
			return $order_id;
		}

		$args = array(
			'post_id' => $order_id,
			'approve' => 'approve',
			'type'    => 'order_note',
		);

		remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

		$notes = get_comments( $args );

		add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

		$order_notes = array();

		foreach ( $notes as $note ) {

			$order_notes[] = current( $this->get_order_note( $order_id, $note->comment_ID, $fields ) );
		}

		return array( 'order_notes' => apply_filters( 'woocommerce_api_order_notes_response', $order_notes, $order_id, $fields, $notes, $this->server ) );
	}

	/**
	 * Get an order note for the given order ID and ID
	 *
	 * @since 2.2
	 *
	 * @param string $order_id order ID
	 * @param string $id order note ID
	 * @param string|null $fields fields to limit response to
	 *
	 * @return array|WP_Error
	 */
	public function get_order_note( $order_id, $id, $fields = null ) {
		try {
			// Validate order ID
			$order_id = $this->validate_request( $order_id, $this->post_type, 'read' );

			if ( is_wp_error( $order_id ) ) {
				return $order_id;
			}

			$id = absint( $id );

			if ( empty( $id ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_note_id', __( 'Invalid order note ID', 'woocommerce' ), 400 );
			}

			$note = get_comment( $id );

			if ( is_null( $note ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_note_id', __( 'An order note with the provided ID could not be found', 'woocommerce' ), 404 );
			}

			$order_note = array(
				'id'            => $note->comment_ID,
				'created_at'    => $this->server->format_datetime( $note->comment_date_gmt ),
				'note'          => $note->comment_content,
				'customer_note' => (bool) get_comment_meta( $note->comment_ID, 'is_customer_note', true ),
			);

			return array( 'order_note' => apply_filters( 'woocommerce_api_order_note_response', $order_note, $id, $fields, $note, $order_id, $this ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Create a new order note for the given order
	 *
	 * @since 2.2
	 * @param string $order_id order ID
	 * @param array $data raw request data
	 * @return WP_Error|array error or created note response data
	 */
	public function create_order_note( $order_id, $data ) {
		try {
			if ( ! isset( $data['order_note'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_order_note_data', sprintf( __( 'No %1$s data specified to create %1$s', 'woocommerce' ), 'order_note' ), 400 );
			}

			$data = $data['order_note'];

			// permission check
			if ( ! current_user_can( 'publish_shop_orders' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_create_order_note', __( 'You do not have permission to create order notes', 'woocommerce' ), 401 );
			}

			$order_id = $this->validate_request( $order_id, $this->post_type, 'edit' );

			if ( is_wp_error( $order_id ) ) {
				return $order_id;
			}

			$order = wc_get_order( $order_id );

			$data = apply_filters( 'woocommerce_api_create_order_note_data', $data, $order_id, $this );

			// note content is required
			if ( ! isset( $data['note'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_note', __( 'Order note is required', 'woocommerce' ), 400 );
			}

			$is_customer_note = ( isset( $data['customer_note'] ) && true === $data['customer_note'] );

			// create the note
			$note_id = $order->add_order_note( $data['note'], $is_customer_note );

			if ( ! $note_id ) {
				throw new WC_API_Exception( 'woocommerce_api_cannot_create_order_note', __( 'Cannot create order note, please try again.', 'woocommerce' ), 500 );
			}

			// HTTP 201 Created
			$this->server->send_status( 201 );

			do_action( 'woocommerce_api_create_order_note', $note_id, $order_id, $this );

			return $this->get_order_note( $order->get_id(), $note_id );
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => 400 ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Edit the order note
	 *
	 * @since 2.2
	 * @param string $order_id order ID
	 * @param string $id note ID
	 * @param array $data parsed request data
	 * @return WP_Error|array error or edited note response data
	 */
	public function edit_order_note( $order_id, $id, $data ) {
		try {
			if ( ! isset( $data['order_note'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_order_note_data', sprintf( __( 'No %1$s data specified to edit %1$s', 'woocommerce' ), 'order_note' ), 400 );
			}

			$data = $data['order_note'];

			// Validate order ID
			$order_id = $this->validate_request( $order_id, $this->post_type, 'edit' );

			if ( is_wp_error( $order_id ) ) {
				return $order_id;
			}

			$order = wc_get_order( $order_id );

			// Validate note ID
			$id = absint( $id );

			if ( empty( $id ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_note_id', __( 'Invalid order note ID', 'woocommerce' ), 400 );
			}

			// Ensure note ID is valid
			$note = get_comment( $id );

			if ( is_null( $note ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_note_id', __( 'An order note with the provided ID could not be found', 'woocommerce' ), 404 );
			}

			// Ensure note ID is associated with given order
			if ( $note->comment_post_ID != $order->get_id() ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_note_id', __( 'The order note ID provided is not associated with the order', 'woocommerce' ), 400 );
			}

			$data = apply_filters( 'woocommerce_api_edit_order_note_data', $data, $note->comment_ID, $order->get_id(), $this );

			// Note content
			if ( isset( $data['note'] ) ) {

				wp_update_comment(
					array(
						'comment_ID'      => $note->comment_ID,
						'comment_content' => $data['note'],
					)
				);
			}

			// Customer note
			if ( isset( $data['customer_note'] ) ) {

				update_comment_meta( $note->comment_ID, 'is_customer_note', true === $data['customer_note'] ? 1 : 0 );
			}

			do_action( 'woocommerce_api_edit_order_note', $note->comment_ID, $order->get_id(), $this );

			return $this->get_order_note( $order->get_id(), $note->comment_ID );
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => 400 ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Delete order note
	 *
	 * @since 2.2
	 * @param string $order_id order ID
	 * @param string $id note ID
	 * @return WP_Error|array error or deleted message
	 */
	public function delete_order_note( $order_id, $id ) {
		try {
			$order_id = $this->validate_request( $order_id, $this->post_type, 'delete' );

			if ( is_wp_error( $order_id ) ) {
				return $order_id;
			}

			// Validate note ID
			$id = absint( $id );

			if ( empty( $id ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_note_id', __( 'Invalid order note ID', 'woocommerce' ), 400 );
			}

			// Ensure note ID is valid
			$note = get_comment( $id );

			if ( is_null( $note ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_note_id', __( 'An order note with the provided ID could not be found', 'woocommerce' ), 404 );
			}

			// Ensure note ID is associated with given order
			if ( $note->comment_post_ID != $order_id ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_note_id', __( 'The order note ID provided is not associated with the order', 'woocommerce' ), 400 );
			}

			// Force delete since trashed order notes could not be managed through comments list table
			$result = wc_delete_order_note( $note->comment_ID );

			if ( ! $result ) {
				throw new WC_API_Exception( 'woocommerce_api_cannot_delete_order_note', __( 'This order note cannot be deleted', 'woocommerce' ), 500 );
			}

			do_action( 'woocommerce_api_delete_order_note', $note->comment_ID, $order_id, $this );

			return array( 'message' => __( 'Permanently deleted order note', 'woocommerce' ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Get the order refunds for an order
	 *
	 * @since 2.2
	 * @param string $order_id order ID
	 * @param string|null $fields fields to include in response
	 * @return array|WP_Error
	 */
	public function get_order_refunds( $order_id, $fields = null ) {

		// Ensure ID is valid order ID
		$order_id = $this->validate_request( $order_id, $this->post_type, 'read' );

		if ( is_wp_error( $order_id ) ) {
			return $order_id;
		}

		$refund_items = wc_get_orders( array(
			'type'   => 'shop_order_refund',
			'parent' => $order_id,
			'limit'  => -1,
			'return' => 'ids',
		) );
		$order_refunds = array();

		foreach ( $refund_items as $refund_id ) {
			$order_refunds[] = current( $this->get_order_refund( $order_id, $refund_id, $fields ) );
		}

		return array( 'order_refunds' => apply_filters( 'woocommerce_api_order_refunds_response', $order_refunds, $order_id, $fields, $refund_items, $this ) );
	}

	/**
	 * Get an order refund for the given order ID and ID
	 *
	 * @since 2.2
	 *
	 * @param string $order_id order ID
	 * @param int $id
	 * @param string|null $fields fields to limit response to
	 * @param array $filter
	 *
	 * @return array|WP_Error
	 */
	public function get_order_refund( $order_id, $id, $fields = null, $filter = array() ) {
		try {
			// Validate order ID
			$order_id = $this->validate_request( $order_id, $this->post_type, 'read' );

			if ( is_wp_error( $order_id ) ) {
				return $order_id;
			}

			$id = absint( $id );

			if ( empty( $id ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_refund_id', __( 'Invalid order refund ID.', 'woocommerce' ), 400 );
			}

			$order  = wc_get_order( $order_id );
			$refund = wc_get_order( $id );

			if ( ! $refund ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_refund_id', __( 'An order refund with the provided ID could not be found.', 'woocommerce' ), 404 );
			}

			$line_items = array();

			// Add line items
			foreach ( $refund->get_items( 'line_item' ) as $item_id => $item ) {
				$product    = $item->get_product();
				$hideprefix = ( isset( $filter['all_item_meta'] ) && 'true' === $filter['all_item_meta'] ) ? null : '_';
				$item_meta  = $item->get_all_formatted_meta_data( $hideprefix );

				foreach ( $item_meta as $key => $values ) {
					$item_meta[ $key ]->label = $values->display_key;
					unset( $item_meta[ $key ]->display_key );
					unset( $item_meta[ $key ]->display_value );
				}

				$line_items[] = array(
					'id'               => $item_id,
					'subtotal'         => wc_format_decimal( $order->get_line_subtotal( $item ), 2 ),
					'subtotal_tax'     => wc_format_decimal( $item->get_subtotal_tax(), 2 ),
					'total'            => wc_format_decimal( $order->get_line_total( $item ), 2 ),
					'total_tax'        => wc_format_decimal( $order->get_line_tax( $item ), 2 ),
					'price'            => wc_format_decimal( $order->get_item_total( $item ), 2 ),
					'quantity'         => $item->get_quantity(),
					'tax_class'        => $item->get_tax_class(),
					'name'             => $item->get_name(),
					'product_id'       => $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id(),
					'sku'              => is_object( $product ) ? $product->get_sku() : null,
					'meta'             => array_values( $item_meta ),
					'refunded_item_id' => (int) $item->get_meta( 'refunded_item_id' ),
				);
			}

			$order_refund = array(
				'id'         => $refund->get_id(),
				'created_at' => $this->server->format_datetime( $refund->get_date_created() ? $refund->get_date_created()->getTimestamp() : 0, false, false ),
				'amount'     => wc_format_decimal( $refund->get_amount(), 2 ),
				'reason'     => $refund->get_reason(),
				'line_items' => $line_items,
			);

			return array( 'order_refund' => apply_filters( 'woocommerce_api_order_refund_response', $order_refund, $id, $fields, $refund, $order_id, $this ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Create a new order refund for the given order
	 *
	 * @since 2.2
	 * @param string $order_id order ID
	 * @param array $data raw request data
	 * @param bool $api_refund do refund using a payment gateway API
	 * @return WP_Error|array error or created refund response data
	 */
	public function create_order_refund( $order_id, $data, $api_refund = true ) {
		try {
			if ( ! isset( $data['order_refund'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_order_refund_data', sprintf( __( 'No %1$s data specified to create %1$s', 'woocommerce' ), 'order_refund' ), 400 );
			}

			$data = $data['order_refund'];

			// Permission check
			if ( ! current_user_can( 'publish_shop_orders' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_create_order_refund', __( 'You do not have permission to create order refunds', 'woocommerce' ), 401 );
			}

			$order_id = absint( $order_id );

			if ( empty( $order_id ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_id', __( 'Order ID is invalid', 'woocommerce' ), 400 );
			}

			$data = apply_filters( 'woocommerce_api_create_order_refund_data', $data, $order_id, $this );

			// Refund amount is required
			if ( ! isset( $data['amount'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_refund', __( 'Refund amount is required.', 'woocommerce' ), 400 );
			} elseif ( 0 > $data['amount'] ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_refund', __( 'Refund amount must be positive.', 'woocommerce' ), 400 );
			}

			$data['order_id']  = $order_id;
			$data['refund_id'] = 0;

			// Create the refund
			$refund = wc_create_refund( $data );

			if ( ! $refund ) {
				throw new WC_API_Exception( 'woocommerce_api_cannot_create_order_refund', __( 'Cannot create order refund, please try again.', 'woocommerce' ), 500 );
			}

			// Refund via API
			if ( $api_refund ) {
				if ( WC()->payment_gateways() ) {
					$payment_gateways = WC()->payment_gateways->payment_gateways();
				}

				$order = wc_get_order( $order_id );

				if ( isset( $payment_gateways[ $order->get_payment_method() ] ) && $payment_gateways[ $order->get_payment_method() ]->supports( 'refunds' ) ) {
					$result = $payment_gateways[ $order->get_payment_method() ]->process_refund( $order_id, $refund->get_amount(), $refund->get_reason() );

					if ( is_wp_error( $result ) ) {
						return $result;
					} elseif ( ! $result ) {
						throw new WC_API_Exception( 'woocommerce_api_create_order_refund_api_failed', __( 'An error occurred while attempting to create the refund using the payment gateway API.', 'woocommerce' ), 500 );
					}
				}
			}

			// HTTP 201 Created
			$this->server->send_status( 201 );

			do_action( 'woocommerce_api_create_order_refund', $refund->get_id(), $order_id, $this );

			return $this->get_order_refund( $order_id, $refund->get_id() );
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => 400 ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Edit an order refund
	 *
	 * @since 2.2
	 * @param string $order_id order ID
	 * @param string $id refund ID
	 * @param array $data parsed request data
	 * @return WP_Error|array error or edited refund response data
	 */
	public function edit_order_refund( $order_id, $id, $data ) {
		try {
			if ( ! isset( $data['order_refund'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_order_refund_data', sprintf( __( 'No %1$s data specified to edit %1$s', 'woocommerce' ), 'order_refund' ), 400 );
			}

			$data = $data['order_refund'];

			// Validate order ID
			$order_id = $this->validate_request( $order_id, $this->post_type, 'edit' );

			if ( is_wp_error( $order_id ) ) {
				return $order_id;
			}

			// Validate refund ID
			$id = absint( $id );

			if ( empty( $id ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_refund_id', __( 'Invalid order refund ID.', 'woocommerce' ), 400 );
			}

			// Ensure order ID is valid
			$refund = get_post( $id );

			if ( ! $refund ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_refund_id', __( 'An order refund with the provided ID could not be found.', 'woocommerce' ), 404 );
			}

			// Ensure refund ID is associated with given order
			if ( $refund->post_parent != $order_id ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_refund_id', __( 'The order refund ID provided is not associated with the order.', 'woocommerce' ), 400 );
			}

			$data = apply_filters( 'woocommerce_api_edit_order_refund_data', $data, $refund->ID, $order_id, $this );

			// Update reason
			if ( isset( $data['reason'] ) ) {
				$updated_refund = wp_update_post( array( 'ID' => $refund->ID, 'post_excerpt' => $data['reason'] ) );

				if ( is_wp_error( $updated_refund ) ) {
					return $updated_refund;
				}
			}

			// Update refund amount
			if ( isset( $data['amount'] ) && 0 < $data['amount'] ) {
				update_post_meta( $refund->ID, '_refund_amount', wc_format_decimal( $data['amount'] ) );
			}

			do_action( 'woocommerce_api_edit_order_refund', $refund->ID, $order_id, $this );

			return $this->get_order_refund( $order_id, $refund->ID );
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => 400 ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Delete order refund
	 *
	 * @since 2.2
	 * @param string $order_id order ID
	 * @param string $id refund ID
	 * @return WP_Error|array error or deleted message
	 */
	public function delete_order_refund( $order_id, $id ) {
		try {
			$order_id = $this->validate_request( $order_id, $this->post_type, 'delete' );

			if ( is_wp_error( $order_id ) ) {
				return $order_id;
			}

			// Validate refund ID
			$id = absint( $id );

			if ( empty( $id ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_refund_id', __( 'Invalid order refund ID.', 'woocommerce' ), 400 );
			}

			// Ensure refund ID is valid
			$refund = get_post( $id );

			if ( ! $refund ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_refund_id', __( 'An order refund with the provided ID could not be found.', 'woocommerce' ), 404 );
			}

			// Ensure refund ID is associated with given order
			if ( $refund->post_parent != $order_id ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_order_refund_id', __( 'The order refund ID provided is not associated with the order.', 'woocommerce' ), 400 );
			}

			wc_delete_shop_order_transients( $order_id );

			do_action( 'woocommerce_api_delete_order_refund', $refund->ID, $order_id, $this );

			return $this->delete( $refund->ID, 'refund', true );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Bulk update or insert orders
	 * Accepts an array with orders in the formats supported by
	 * WC_API_Orders->create_order() and WC_API_Orders->edit_order()
	 *
	 * @since 2.4.0
	 *
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function bulk( $data ) {

		try {
			if ( ! isset( $data['orders'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_orders_data', sprintf( __( 'No %1$s data specified to create/edit %1$s', 'woocommerce' ), 'orders' ), 400 );
			}

			$data  = $data['orders'];
			$limit = apply_filters( 'woocommerce_api_bulk_limit', 100, 'orders' );

			// Limit bulk operation
			if ( count( $data ) > $limit ) {
				throw new WC_API_Exception( 'woocommerce_api_orders_request_entity_too_large', sprintf( __( 'Unable to accept more than %s items for this request.', 'woocommerce' ), $limit ), 413 );
			}

			$orders = array();

			foreach ( $data as $_order ) {
				$order_id = 0;

				// Try to get the order ID
				if ( isset( $_order['id'] ) ) {
					$order_id = intval( $_order['id'] );
				}

				if ( $order_id ) {

					// Order exists / edit order
					$edit = $this->edit_order( $order_id, array( 'order' => $_order ) );

					if ( is_wp_error( $edit ) ) {
						$orders[] = array(
							'id'    => $order_id,
							'error' => array( 'code' => $edit->get_error_code(), 'message' => $edit->get_error_message() ),
						);
					} else {
						$orders[] = $edit['order'];
					}
				} else {

					// Order don't exists / create order
					$new = $this->create_order( array( 'order' => $_order ) );

					if ( is_wp_error( $new ) ) {
						$orders[] = array(
							'id'    => $order_id,
							'error' => array( 'code' => $new->get_error_code(), 'message' => $new->get_error_message() ),
						);
					} else {
						$orders[] = $new['order'];
					}
				}
			}

			return array( 'orders' => apply_filters( 'woocommerce_api_orders_bulk_response', $orders, $this ) );
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => 400 ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}
}
