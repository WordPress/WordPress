<?php
/**
 * REST API Shipping Zones controller
 *
 * Handles requests to the /shipping/zones endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Shipping Zones class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Shipping_Zones_Controller_Base
 */
class WC_REST_Shipping_Zones_V2_Controller extends WC_REST_Shipping_Zones_Controller_Base {

	/**
	 * Register the routes for Shipping Zones.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace, '/' . $this->rest_base, array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => array_merge(
						$this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ), array(
							'name' => array(
								'required'    => true,
								'type'        => 'string',
								'description' => __( 'Shipping zone name.', 'woocommerce' ),
							),
						)
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique ID for the resource.', 'woocommerce' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_items_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_items_permissions_check' ),
					'args'                => array(
						'force' => array(
							'default'     => false,
							'type'        => 'boolean',
							'description' => __( 'Whether to bypass trash and force deletion.', 'woocommerce' ),
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Get a single Shipping Zone.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_item( $request ) {
		$zone = $this->get_zone( $request->get_param( 'id' ) );

		if ( is_wp_error( $zone ) ) {
			return $zone;
		}

		$data = $zone->get_data();
		$data = $this->prepare_item_for_response( $data, $request );
		$data = $this->prepare_response_for_collection( $data );

		return rest_ensure_response( $data );
	}

	/**
	 * Get all Shipping Zones.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return WP_REST_Response
	 */
	public function get_items( $request ) {
		$rest_of_the_world = WC_Shipping_Zones::get_zone_by( 'zone_id', 0 );

		$zones = WC_Shipping_Zones::get_zones();
		array_unshift( $zones, $rest_of_the_world->get_data() );
		$data = array();

		foreach ( $zones as $zone_obj ) {
			$zone   = $this->prepare_item_for_response( $zone_obj, $request );
			$zone   = $this->prepare_response_for_collection( $zone );
			$data[] = $zone;
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Create a single Shipping Zone.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function create_item( $request ) {
		$zone = new WC_Shipping_Zone( null );

		if ( ! is_null( $request->get_param( 'name' ) ) ) {
			$zone->set_zone_name( $request->get_param( 'name' ) );
		}

		if ( ! is_null( $request->get_param( 'order' ) ) ) {
			$zone->set_zone_order( $request->get_param( 'order' ) );
		}

		$zone->save();

		if ( $zone->get_id() !== 0 ) {
			$request->set_param( 'id', $zone->get_id() );
			$response = $this->get_item( $request );
			$response->set_status( 201 );
			$response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $zone->get_id() ) ) );
			return $response;
		} else {
			return new WP_Error( 'woocommerce_rest_shipping_zone_not_created', __( "Resource cannot be created. Check to make sure 'order' and 'name' are present.", 'woocommerce' ), array( 'status' => 500 ) );
		}
	}

	/**
	 * Update a single Shipping Zone.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function update_item( $request ) {
		$zone = $this->get_zone( $request->get_param( 'id' ) );

		if ( is_wp_error( $zone ) ) {
			return $zone;
		}

		if ( 0 === $zone->get_id() ) {
			return new WP_Error( 'woocommerce_rest_shipping_zone_invalid_zone', __( 'The "locations not covered by your other zones" zone cannot be updated.', 'woocommerce' ), array( 'status' => 403 ) );
		}

		$zone_changed = false;

		if ( ! is_null( $request->get_param( 'name' ) ) ) {
			$zone->set_zone_name( $request->get_param( 'name' ) );
			$zone_changed = true;
		}

		if ( ! is_null( $request->get_param( 'order' ) ) ) {
			$zone->set_zone_order( $request->get_param( 'order' ) );
			$zone_changed = true;
		}

		if ( $zone_changed ) {
			$zone->save();
		}

		return $this->get_item( $request );
	}

	/**
	 * Delete a single Shipping Zone.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function delete_item( $request ) {
		$zone = $this->get_zone( $request->get_param( 'id' ) );

		if ( is_wp_error( $zone ) ) {
			return $zone;
		}

		$force = $request['force'];

		$response = $this->get_item( $request );

		if ( $force ) {
			$zone->delete();
		} else {
			return new WP_Error( 'rest_trash_not_supported', __( 'Shipping zones do not support trashing.', 'woocommerce' ), array( 'status' => 501 ) );
		}

		return $response;
	}

	/**
	 * Prepare the Shipping Zone for the REST response.
	 *
	 * @param array           $item Shipping Zone.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = array(
			'id'    => (int) $item['id'],
			'name'  => $item['zone_name'],
			'order' => (int) $item['zone_order'],
		);

		$context = empty( $request['context'] ) ? 'view' : $request['context'];
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $data['id'] ) );

		return $response;
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param int $zone_id Given Shipping Zone ID.
	 * @return array Links for the given Shipping Zone.
	 */
	protected function prepare_links( $zone_id ) {
		$base  = '/' . $this->namespace . '/' . $this->rest_base;
		$links = array(
			'self'        => array(
				'href' => rest_url( trailingslashit( $base ) . $zone_id ),
			),
			'collection'  => array(
				'href' => rest_url( $base ),
			),
			'describedby' => array(
				'href' => rest_url( trailingslashit( $base ) . $zone_id . '/locations' ),
			),
		);

		return $links;
	}

	/**
	 * Get the Shipping Zones schema, conforming to JSON Schema
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'shipping_zone',
			'type'       => 'object',
			'properties' => array(
				'id'    => array(
					'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'name'  => array(
					'description' => __( 'Shipping zone name.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'order' => array(
					'description' => __( 'Shipping zone order.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}
