<?php
/**
 * REST API Legacy Orders controller
 *
 * Handles requests to the /orders endpoint.
 *
 * @author   WooThemes
 * @category API
 * @package  WooCommerce\RestApi
 * @since    3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Legacy Orders controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_CRUD_Controller
 */
class WC_REST_Legacy_Orders_Controller extends WC_REST_CRUD_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v2';

	/**
	 * Query args.
	 *
	 * @deprecated 3.0
	 *
	 * @param array $args
	 * @param WP_REST_Request $request
	 * @return array
	 */
	public function query_args( $args, $request ) {
		global $wpdb;

		// Set post_status.
		if ( 'any' !== $request['status'] ) {
			$args['post_status'] = 'wc-' . $request['status'];
		} else {
			$args['post_status'] = 'any';
		}

		if ( ! empty( $request['customer'] ) ) {
			if ( ! empty( $args['meta_query'] ) ) {
				$args['meta_query'] = array();
			}

			$args['meta_query'][] = array(
				'key'   => '_customer_user',
				'value' => $request['customer'],
				'type'  => 'NUMERIC',
			);
		}

		// Search by product.
		if ( ! empty( $request['product'] ) ) {
			$order_ids = $wpdb->get_col( $wpdb->prepare( "
				SELECT order_id
				FROM {$wpdb->prefix}woocommerce_order_items
				WHERE order_item_id IN ( SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key = '_product_id' AND meta_value = %d )
				AND order_item_type = 'line_item'
			 ", $request['product'] ) );

			// Force WP_Query return empty if don't found any order.
			$order_ids = ! empty( $order_ids ) ? $order_ids : array( 0 );

			$args['post__in'] = $order_ids;
		}

		// Search.
		if ( ! empty( $args['s'] ) ) {
			$order_ids = wc_order_search( $args['s'] );

			if ( ! empty( $order_ids ) ) {
				unset( $args['s'] );
				$args['post__in'] = array_merge( $order_ids, array( 0 ) );
			}
		}

		return $args;
	}

	/**
	 * Prepare a single order output for response.
	 *
	 * @deprecated 3.0
	 *
	 * @param WP_Post $post Post object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $data
	 */
	public function prepare_item_for_response( $post, $request ) {
		$this->request       = $request;
		$this->request['dp'] = is_null( $this->request['dp'] ) ? wc_get_price_decimals() : absint( $this->request['dp'] );
		$statuses            = wc_get_order_statuses();
		$order               = wc_get_order( $post );
		$data                = array_merge( array( 'id' => $order->get_id() ), $order->get_data() );
		$format_decimal      = array( 'discount_total', 'discount_tax', 'shipping_total', 'shipping_tax', 'shipping_total', 'shipping_tax', 'cart_tax', 'total', 'total_tax' );
		$format_date         = array( 'date_created', 'date_modified', 'date_completed', 'date_paid' );
		$format_line_items   = array( 'line_items', 'tax_lines', 'shipping_lines', 'fee_lines', 'coupon_lines' );

		// Format decimal values.
		foreach ( $format_decimal as $key ) {
			$data[ $key ] = wc_format_decimal( $data[ $key ], $this->request['dp'] );
		}

		// Format date values.
		foreach ( $format_date as $key ) {
			$data[ $key ] = $data[ $key ] ? wc_rest_prepare_date_response( get_gmt_from_date( date( 'Y-m-d H:i:s', $data[ $key ] ) ) ) : false;
		}

		// Format the order status.
		$data['status'] = 'wc-' === substr( $data['status'], 0, 3 ) ? substr( $data['status'], 3 ) : $data['status'];

		// Format line items.
		foreach ( $format_line_items as $key ) {
			$data[ $key ] = array_values( array_map( array( $this, 'get_order_item_data' ), $data[ $key ] ) );
		}

		// Refunds.
		$data['refunds'] = array();
		foreach ( $order->get_refunds() as $refund ) {
			$data['refunds'][] = array(
				'id'     => $refund->get_id(),
				'refund' => $refund->get_reason() ? $refund->get_reason() : '',
				'total'  => '-' . wc_format_decimal( $refund->get_amount(), $this->request['dp'] ),
			);
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $order, $request ) );

		/**
		 * Filter the data for a response.
		 *
		 * The dynamic portion of the hook name, $this->post_type, refers to post_type of the post being
		 * prepared for the response.
		 *
		 * @param WP_REST_Response   $response   The response object.
		 * @param WP_Post            $post       Post object.
		 * @param WP_REST_Request    $request    Request object.
		 */
		return apply_filters( "woocommerce_rest_prepare_{$this->post_type}", $response, $post, $request );
	}

	/**
	 * Prepare a single order for create.
	 *
	 * @deprecated 3.0
	 *
	 * @param  WP_REST_Request $request Request object.
	 * @return WP_Error|WC_Order $data Object.
	 */
	protected function prepare_item_for_database( $request ) {
		$id        = isset( $request['id'] ) ? absint( $request['id'] ) : 0;
		$order     = new WC_Order( $id );
		$schema    = $this->get_item_schema();
		$data_keys = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );

		// Handle all writable props
		foreach ( $data_keys as $key ) {
			$value = $request[ $key ];

			if ( ! is_null( $value ) ) {
				switch ( $key ) {
					case 'billing' :
					case 'shipping' :
						$this->update_address( $order, $value, $key );
						break;
					case 'line_items' :
					case 'shipping_lines' :
					case 'fee_lines' :
					case 'coupon_lines' :
						if ( is_array( $value ) ) {
							foreach ( $value as $item ) {
								if ( is_array( $item ) ) {
									if ( $this->item_is_null( $item ) || ( isset( $item['quantity'] ) && 0 === $item['quantity'] ) ) {
										$order->remove_item( $item['id'] );
									} else {
										$this->set_item( $order, $key, $item );
									}
								}
							}
						}
						break;
					case 'meta_data' :
						if ( is_array( $value ) ) {
							foreach ( $value as $meta ) {
								$order->update_meta_data( $meta['key'], $meta['value'], isset( $meta['id'] ) ? $meta['id'] : '' );
							}
						}
						break;
					default :
						if ( is_callable( array( $order, "set_{$key}" ) ) ) {
							$order->{"set_{$key}"}( $value );
						}
						break;
				}
			}
		}

		/**
		 * Filter the data for the insert.
		 *
		 * The dynamic portion of the hook name, $this->post_type, refers to post_type of the post being
		 * prepared for the response.
		 *
		 * @param WC_Order           $order      The Order object.
		 * @param WP_REST_Request    $request    Request object.
		 */
		return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}", $order, $request );
	}

	/**
	 * Create base WC Order object.
	 *
	 * @deprecated 3.0.0
	 *
	 * @param array $data
	 * @return WC_Order
	 */
	protected function create_base_order( $data ) {
		return wc_create_order( $data );
	}

	/**
	 * Create order.
	 *
	 * @deprecated 3.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return int|WP_Error
	 */
	protected function create_order( $request ) {
		try {
			// Make sure customer exists.
			if ( ! is_null( $request['customer_id'] ) && 0 !== $request['customer_id'] && false === get_user_by( 'id', $request['customer_id'] ) ) {
				throw new WC_REST_Exception( 'woocommerce_rest_invalid_customer_id',__( 'Customer ID is invalid.', 'woocommerce' ), 400 );
			}

			// Make sure customer is part of blog.
			if ( is_multisite() && ! is_user_member_of_blog( $request['customer_id'] ) ) {
				add_user_to_blog( get_current_blog_id(), $request['customer_id'], 'customer' );
			}

			$order = $this->prepare_item_for_database( $request );
			$order->set_created_via( 'rest-api' );
			$order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
			$order->calculate_totals();
			$order->save();

			// Handle set paid.
			if ( true === $request['set_paid'] ) {
				$order->payment_complete( $request['transaction_id'] );
			}

			return $order->get_id();
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), $e->getErrorData() );
		} catch ( WC_REST_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Update order.
	 *
	 * @deprecated 3.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return int|WP_Error
	 */
	protected function update_order( $request ) {
		try {
			$order = $this->prepare_item_for_database( $request );
			$order->save();

			// Handle set paid.
			if ( $order->needs_payment() && true === $request['set_paid'] ) {
				$order->payment_complete( $request['transaction_id'] );
			}

			// If items have changed, recalculate order totals.
			if ( isset( $request['billing'] ) || isset( $request['shipping'] ) || isset( $request['line_items'] ) || isset( $request['shipping_lines'] ) || isset( $request['fee_lines'] ) || isset( $request['coupon_lines'] ) ) {
				$order->calculate_totals();
			}

			return $order->get_id();
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), $e->getErrorData() );
		} catch ( WC_REST_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}
}
