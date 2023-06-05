<?php
/**
 * REST API Shipping Zones Controller base
 *
 * Houses common functionality between Shipping Zones and Locations.
 *
 * @package WooCommerce\RestApi
 * @since    3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Shipping Zones base class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Controller
 */
abstract class WC_REST_Shipping_Zones_Controller_Base extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v2';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'shipping/zones';

	/**
	 * Retrieve a Shipping Zone by it's ID.
	 *
	 * @param int $zone_id Shipping Zone ID.
	 * @return WC_Shipping_Zone|WP_Error
	 */
	protected function get_zone( $zone_id ) {
		$zone = WC_Shipping_Zones::get_zone_by( 'zone_id', $zone_id );

		if ( false === $zone ) {
			return new WP_Error( 'woocommerce_rest_shipping_zone_invalid', __( 'Resource does not exist.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		return $zone;
	}

	/**
	 * Check whether a given request has permission to read Shipping Zones.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_shipping_enabled() ) {
			return new WP_Error( 'rest_no_route', __( 'Shipping is disabled.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		if ( ! wc_rest_check_manager_permissions( 'settings', 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to create Shipping Zones.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! wc_shipping_enabled() ) {
			return new WP_Error( 'rest_no_route', __( 'Shipping is disabled.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		if ( ! wc_rest_check_manager_permissions( 'settings', 'edit' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_create', __( 'Sorry, you are not allowed to create resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check whether a given request has permission to edit Shipping Zones.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function update_items_permissions_check( $request ) {
		if ( ! wc_shipping_enabled() ) {
			return new WP_Error( 'rest_no_route', __( 'Shipping is disabled.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		if ( ! wc_rest_check_manager_permissions( 'settings', 'edit' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_edit', __( 'Sorry, you are not allowed to edit this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check whether a given request has permission to delete Shipping Zones.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function delete_items_permissions_check( $request ) {
		if ( ! wc_shipping_enabled() ) {
			return new WP_Error( 'rest_no_route', __( 'Shipping is disabled.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		if ( ! wc_rest_check_manager_permissions( 'settings', 'delete' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_edit', __( 'Sorry, you are not allowed to delete this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

}
