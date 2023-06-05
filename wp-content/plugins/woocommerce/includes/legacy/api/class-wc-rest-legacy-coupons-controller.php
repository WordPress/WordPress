<?php
/**
 * REST API Legacy Coupons controller
 *
 * Handles requests to the /coupons endpoint.
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
 * REST API Legacy Coupons controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_CRUD_Controller
 */
class WC_REST_Legacy_Coupons_Controller extends WC_REST_CRUD_Controller {

	/**
	 * Query args.
	 *
	 * @deprecated 3.0.0
	 *
	 * @param array $args Query args
	 * @param WP_REST_Request $request Request data.
	 * @return array
	 */
	public function query_args( $args, $request ) {
		if ( ! empty( $request['code'] ) ) {
			$id = wc_get_coupon_id_by_code( $request['code'] );
			$args['post__in'] = array( $id );
		}

		return $args;
	}

	/**
	 * Prepare a single coupon output for response.
	 *
	 * @deprecated 3.0.0
	 *
	 * @param WP_Post $post Post object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $data
	 */
	public function prepare_item_for_response( $post, $request ) {
		$coupon = new WC_Coupon( (int) $post->ID );
		$data   = $coupon->get_data();

		$format_decimal = array( 'amount', 'minimum_amount', 'maximum_amount' );
		$format_date    = array( 'date_created', 'date_modified', 'date_expires' );
		$format_null    = array( 'usage_limit', 'usage_limit_per_user', 'limit_usage_to_x_items' );

		// Format decimal values.
		foreach ( $format_decimal as $key ) {
			$data[ $key ] = wc_format_decimal( $data[ $key ], 2 );
		}

		// Format date values.
		foreach ( $format_date as $key ) {
			$data[ $key ] = $data[ $key ] ? wc_rest_prepare_date_response( get_gmt_from_date( date( 'Y-m-d H:i:s', $data[ $key ] ) ) ) : null;
		}

		// Format null values.
		foreach ( $format_null as $key ) {
			$data[ $key ] = $data[ $key ] ? $data[ $key ] : null;
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $post, $request ) );

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
	 * Prepare a single coupon for create or update.
	 *
	 * @deprecated 3.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_Error|stdClass $data Post object.
	 */
	protected function prepare_item_for_database( $request ) {
		global $wpdb;

		$id        = isset( $request['id'] ) ? absint( $request['id'] ) : 0;
		$coupon    = new WC_Coupon( $id );
		$schema    = $this->get_item_schema();
		$data_keys = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );

		// Validate required POST fields.
		if ( 'POST' === $request->get_method() && 0 === $coupon->get_id() ) {
			if ( empty( $request['code'] ) ) {
				return new WP_Error( 'woocommerce_rest_empty_coupon_code', sprintf( __( 'The coupon code cannot be empty.', 'woocommerce' ), 'code' ), array( 'status' => 400 ) );
			}
		}

		// Handle all writable props.
		foreach ( $data_keys as $key ) {
			$value = $request[ $key ];

			if ( ! is_null( $value ) ) {
				switch ( $key ) {
					case 'code' :
						$coupon_code = wc_format_coupon_code( $value );
						$id          = $coupon->get_id() ? $coupon->get_id() : 0;
						$id_from_code = wc_get_coupon_id_by_code( $coupon_code, $id );

						if ( $id_from_code ) {
							return new WP_Error( 'woocommerce_rest_coupon_code_already_exists', __( 'The coupon code already exists', 'woocommerce' ), array( 'status' => 400 ) );
						}

						$coupon->set_code( $coupon_code );
						break;
					case 'meta_data' :
						if ( is_array( $value ) ) {
							foreach ( $value as $meta ) {
								$coupon->update_meta_data( $meta['key'], $meta['value'], isset( $meta['id'] ) ? $meta['id'] : '' );
							}
						}
						break;
					case 'description' :
						$coupon->set_description( wp_filter_post_kses( $value ) );
						break;
					default :
						if ( is_callable( array( $coupon, "set_{$key}" ) ) ) {
							$coupon->{"set_{$key}"}( $value );
						}
						break;
				}
			}
		}

		/**
		 * Filter the query_vars used in `get_items` for the constructed query.
		 *
		 * The dynamic portion of the hook name, $this->post_type, refers to post_type of the post being
		 * prepared for insertion.
		 *
		 * @param WC_Coupon       $coupon        The coupon object.
		 * @param WP_REST_Request $request       Request object.
		 */
		return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}", $coupon, $request );
	}
}
