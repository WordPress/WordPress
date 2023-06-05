<?php
/**
 * REST API Order Refunds controller
 *
 * Handles requests to the /orders/<order_id>/refunds endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Internal\RestApiUtil;

/**
 * REST API Order Refunds controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Order_Refunds_V2_Controller
 */
class WC_REST_Order_Refunds_Controller extends WC_REST_Order_Refunds_V2_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Prepares one object for create or update operation.
	 *
	 * @since  3.0.0
	 * @param  WP_REST_Request $request Request object.
	 * @param  bool            $creating If is creating a new object.
	 * @return WP_Error|WC_Data The prepared item, or WP_Error object on failure.
	 */
	protected function prepare_object_for_database( $request, $creating = false ) {
		RestApiUtil::adjust_create_refund_request_parameters( $request );

		$order = wc_get_order( (int) $request['order_id'] );

		if ( ! $order ) {
			return new WP_Error( 'woocommerce_rest_invalid_order_id', __( 'Invalid order ID.', 'woocommerce' ), 404 );
		}

		if ( 0 > $request['amount'] ) {
			return new WP_Error( 'woocommerce_rest_invalid_order_refund', __( 'Refund amount must be greater than zero.', 'woocommerce' ), 400 );
		}

		// Create the refund.
		$refund = wc_create_refund(
			array(
				'order_id'       => $order->get_id(),
				'amount'         => $request['amount'],
				'reason'         => $request['reason'],
				'line_items'     => $request['line_items'],
				'refund_payment' => $request['api_refund'],
				'restock_items'  => $request['api_restock'],
			)
		);

		if ( is_wp_error( $refund ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_create_order_refund', $refund->get_error_message(), 500 );
		}

		if ( ! $refund ) {
			return new WP_Error( 'woocommerce_rest_cannot_create_order_refund', __( 'Cannot create order refund, please try again.', 'woocommerce' ), 500 );
		}

		if ( ! empty( $request['meta_data'] ) && is_array( $request['meta_data'] ) ) {
			foreach ( $request['meta_data'] as $meta ) {
				$refund->update_meta_data( $meta['key'], $meta['value'], isset( $meta['id'] ) ? $meta['id'] : '' );
			}
			$refund->save_meta_data();
		}

		/**
		 * Filters an object before it is inserted via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->post_type`,
		 * refers to the object type slug.
		 *
		 * @param WC_Data         $coupon   Object object.
		 * @param WP_REST_Request $request  Request object.
		 * @param bool            $creating If is creating a new object.
		 */
		return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}_object", $refund, $request, $creating );
	}

	/**
	 * Get the refund schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = parent::get_item_schema();

		$schema['properties']['line_items']['items']['properties']['refund_total'] = array(
			'description' => __( 'Amount that will be refunded for this line item (excluding taxes).', 'woocommerce' ),
			'type'        => 'number',
			'context'     => array( 'edit' ),
			'readonly'    => true,
		);

		$schema['properties']['line_items']['items']['properties']['taxes']['items']['properties']['refund_total'] = array(
			'description' => __( 'Amount that will be refunded for this tax.', 'woocommerce' ),
			'type'        => 'number',
			'context'     => array( 'edit' ),
			'readonly'    => true,
		);

		$schema['properties']['api_restock'] = array(
			'description' => __( 'When true, refunded items are restocked.', 'woocommerce' ),
			'type'        => 'boolean',
			'context'     => array( 'edit' ),
			'default'     => true,
		);

		return $schema;
	}
}
