<?php
/**
 * REST API Coupons controller
 *
 * Handles requests to the /coupons endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Coupons controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Coupons_V2_Controller
 */
class WC_REST_Coupons_Controller extends WC_REST_Coupons_V2_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Prepare objects query.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array
	 */
	protected function prepare_objects_query( $request ) {
		$args = parent::prepare_objects_query( $request );

		// Set post_status.
		$args['post_status'] = $request['status'];

		if ( ! empty( $request['code'] ) ) {
			$id               = wc_get_coupon_id_by_code( $request['code'] );
			$args['post__in'] = array( $id );
		}

		// Get only ids.
		$args['fields'] = 'ids';

		return $args;
	}
}
