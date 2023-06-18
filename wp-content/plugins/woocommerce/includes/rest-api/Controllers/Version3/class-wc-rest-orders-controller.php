<?php
/**
 * REST API Orders controller
 *
 * Handles requests to the /orders endpoint.
 *
 * @package WooCommerce\RestApi
 * @since    2.6.0
 */

use Automattic\WooCommerce\Utilities\ArrayUtil;
use Automattic\WooCommerce\Utilities\OrderUtil;
use Automattic\WooCommerce\Utilities\StringUtil;

defined( 'ABSPATH' ) || exit;

/**
 * REST API Orders controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Orders_V2_Controller
 */
class WC_REST_Orders_Controller extends WC_REST_Orders_V2_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Calculate coupons.
	 *
	 * @throws WC_REST_Exception When fails to set any item.
	 * @param WP_REST_Request $request Request object.
	 * @param WC_Order        $order   Order data.
	 * @return bool
	 */
	protected function calculate_coupons( $request, $order ) {
		if ( ! isset( $request['coupon_lines'] ) ) {
			return false;
		}

		// Validate input and at the same time store the processed coupon codes to apply.

		$coupon_codes = array();
		$discounts    = new WC_Discounts( $order );

		$current_order_coupons      = array_values( $order->get_coupons() );
		$current_order_coupon_codes = array_map(
			function( $coupon ) {
				return $coupon->get_code();
			},
			$current_order_coupons
		);

		foreach ( $request['coupon_lines'] as $item ) {
			if ( ! empty( $item['id'] ) ) {
				throw new WC_REST_Exception( 'woocommerce_rest_coupon_item_id_readonly', __( 'Coupon item ID is readonly.', 'woocommerce' ), 400 );
			}

			$coupon_code = ArrayUtil::get_value_or_default( $item, 'code' );
			if ( StringUtil::is_null_or_whitespace( $coupon_code ) ) {
				throw new WC_REST_Exception( 'woocommerce_rest_invalid_coupon', __( 'Coupon code is required.', 'woocommerce' ), 400 );
			}

			$coupon_code = wc_format_coupon_code( wc_clean( $coupon_code ) );
			$coupon      = new WC_Coupon( $coupon_code );

			// Skip check if the coupon is already applied to the order, as this could wrongly throw an error for single-use coupons.
			if ( ! in_array( $coupon_code, $current_order_coupon_codes, true ) ) {
				$check_result = $discounts->is_coupon_valid( $coupon );
				if ( is_wp_error( $check_result ) ) {
					throw new WC_REST_Exception( 'woocommerce_rest_' . $check_result->get_error_code(), $check_result->get_error_message(), 400 );
				}
			}

			$coupon_codes[] = $coupon_code;
		}

		// Remove all coupons first to ensure calculation is correct.
		foreach ( $order->get_items( 'coupon' ) as $existing_coupon ) {
			$order->remove_coupon( $existing_coupon->get_code() );
		}

		// Apply the coupons.
		foreach ( $coupon_codes as $new_coupon ) {
			$results = $order->apply_coupon( $new_coupon );

			if ( is_wp_error( $results ) ) {
				throw new WC_REST_Exception( 'woocommerce_rest_' . $results->get_error_code(), $results->get_error_message(), 400 );
			}
		}

		return true;
	}

	/**
	 * Prepare a single order for create or update.
	 *
	 * @throws WC_REST_Exception When fails to set any item.
	 * @param  WP_REST_Request $request Request object.
	 * @param  bool            $creating If is creating a new object.
	 * @return WP_Error|WC_Data
	 */
	protected function prepare_object_for_database( $request, $creating = false ) {
		$id        = isset( $request['id'] ) ? absint( $request['id'] ) : 0;
		$order     = new WC_Order( $id );
		$schema    = $this->get_item_schema();
		$data_keys = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );

		// Handle all writable props.
		foreach ( $data_keys as $key ) {
			$value = $request[ $key ];

			if ( ! is_null( $value ) ) {
				switch ( $key ) {
					case 'coupon_lines':
					case 'status':
						// Change should be done later so transitions have new data.
						break;
					case 'billing':
					case 'shipping':
						$this->update_address( $order, $value, $key );
						break;
					case 'line_items':
					case 'shipping_lines':
					case 'fee_lines':
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
					case 'meta_data':
						if ( is_array( $value ) ) {
							foreach ( $value as $meta ) {
								$order->update_meta_data( $meta['key'], $meta['value'], isset( $meta['id'] ) ? $meta['id'] : '' );
							}
						}
						break;
					default:
						if ( is_callable( array( $order, "set_{$key}" ) ) ) {
							$order->{"set_{$key}"}( $value );
						}
						break;
				}
			}
		}

		/**
		 * Filters an object before it is inserted via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->post_type`,
		 * refers to the object type slug.
		 *
		 * @since 7.4.0
		 *
		 * @param WC_Data         $order    Object object.
		 * @param WP_REST_Request $request  Request object.
		 * @param bool            $creating If is creating a new object.
		 */
		return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}_object", $order, $request, $creating );
	}

	/**
	 * Save an object data.
	 *
	 * @since  3.0.0
	 * @throws WC_REST_Exception But all errors are validated before returning any data.
	 * @param  WP_REST_Request $request  Full details about the request.
	 * @param  bool            $creating If is creating a new object.
	 * @return WC_Data|WP_Error
	 */
	protected function save_object( $request, $creating = false ) {
		try {
			$object = $this->prepare_object_for_database( $request, $creating );

			if ( is_wp_error( $object ) ) {
				return $object;
			}

			// Make sure gateways are loaded so hooks from gateways fire on save/create.
			WC()->payment_gateways();

			if ( ! is_null( $request['customer_id'] ) && 0 !== $request['customer_id'] ) {
				// Make sure customer exists.
				if ( false === get_user_by( 'id', $request['customer_id'] ) ) {
					throw new WC_REST_Exception( 'woocommerce_rest_invalid_customer_id', __( 'Customer ID is invalid.', 'woocommerce' ), 400 );
				}

				// Make sure customer is part of blog.
				if ( is_multisite() && ! is_user_member_of_blog( $request['customer_id'] ) ) {
					add_user_to_blog( get_current_blog_id(), $request['customer_id'], 'customer' );
				}
			}

			if ( $creating ) {
				$object->set_created_via( 'rest-api' );
				$object->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
				$object->save();
				$object->calculate_totals();
			} else {
				// If items have changed, recalculate order totals.
				if ( isset( $request['billing'] ) || isset( $request['shipping'] ) || isset( $request['line_items'] ) || isset( $request['shipping_lines'] ) || isset( $request['fee_lines'] ) || isset( $request['coupon_lines'] ) ) {
					$object->calculate_totals( true );
				}
			}

			// Set coupons.
			$this->calculate_coupons( $request, $object );

			// Set status.
			if ( ! empty( $request['status'] ) ) {
				$object->set_status( $request['status'] );
			}

			$object->save();

			// Actions for after the order is saved.
			if ( true === $request['set_paid'] ) {
				if ( $creating || $object->needs_payment() ) {
					$object->payment_complete( $request['transaction_id'] );
				}
			}

			return $this->get_object( $object->get_id() );
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), $e->getErrorData() );
		} catch ( WC_REST_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Prepare objects query.
	 *
	 * @since  3.0.0
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array
	 */
	protected function prepare_objects_query( $request ) {
		// This is needed to get around an array to string notice in WC_REST_Orders_V2_Controller::prepare_objects_query.
		$statuses = $request['status'];
		unset( $request['status'] );

		// Prevents WC_REST_Orders_V2_Controller::prepare_objects_query() from generating a meta_query for 'customer'.
		// which COT can handle as a native field.
		$cot_customer =
			( OrderUtil::custom_orders_table_usage_is_enabled() && isset( $request['customer'] ) )
			? $request['customer']
			: null;

		if ( ! is_null( $cot_customer ) ) {
			unset( $request['customer'] );
		}

		$args = parent::prepare_objects_query( $request );

		$args['post_status'] = array();
		foreach ( $statuses as $status ) {
			if ( in_array( $status, $this->get_order_statuses(), true ) ) {
				$args['post_status'][] = 'wc-' . $status;
			} elseif ( 'any' === $status ) {
				// Set status to "any" and short-circuit out.
				$args['post_status'] = 'any';
				break;
			} else {
				$args['post_status'][] = $status;
			}
		}

		// Put the statuses back for further processing (next/prev links, etc).
		$request['status'] = $statuses;

		// Add back 'customer' to args and request.
		if ( ! is_null( $cot_customer ) ) {
			$args['customer']    = $cot_customer;
			$request['customer'] = $cot_customer;
		}

		return $args;
	}

	/**
	 * Get the Order's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = parent::get_item_schema();

		$schema['properties']['coupon_lines']['items']['properties']['discount']['readonly'] = true;

		return $schema;
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['status'] = array(
			'default'           => 'any',
			'description'       => __( 'Limit result set to orders which have specific statuses.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'string',
				'enum' => array_merge( array( 'any', 'trash' ), $this->get_order_statuses() ),
			),
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}
}
