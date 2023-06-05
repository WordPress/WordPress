<?php
/**
 * REST API WC Shipping Methods controller
 *
 * Handles requests to the /shipping_methods endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shipping methods controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Controller
 */
class WC_REST_Shipping_Methods_V2_Controller extends WC_REST_Controller {

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
	protected $rest_base = 'shipping_methods';

	/**
	 * Register the route for /shipping_methods and /shipping_methods/<method>
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace, '/' . $this->rest_base, array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace, '/' . $this->rest_base . '/(?P<id>[\w-]+)', array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
						'type'        => 'string',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check whether a given request has permission to view shipping methods.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'shipping_methods', 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Check if a given request has access to read a shipping method.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'shipping_methods', 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Get shipping methods.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$wc_shipping = WC_Shipping::instance();
		$response    = array();
		foreach ( $wc_shipping->get_shipping_methods() as $id => $shipping_method ) {
			$method     = $this->prepare_item_for_response( $shipping_method, $request );
			$method     = $this->prepare_response_for_collection( $method );
			$response[] = $method;
		}
		return rest_ensure_response( $response );
	}

	/**
	 * Get a single Shipping Method.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_item( $request ) {
		$wc_shipping = WC_Shipping::instance();
		$methods     = $wc_shipping->get_shipping_methods();
		if ( empty( $methods[ $request['id'] ] ) ) {
			return new WP_Error( 'woocommerce_rest_shipping_method_invalid', __( 'Resource does not exist.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$method   = $methods[ $request['id'] ];
		$response = $this->prepare_item_for_response( $method, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Prepare a shipping method for response.
	 *
	 * @param  WC_Shipping_Method $method   Shipping method object.
	 * @param  WP_REST_Request    $request  Request object.
	 * @return WP_REST_Response   $response Response data.
	 */
	public function prepare_item_for_response( $method, $request ) {
		$data = array(
			'id'          => $method->id,
			'title'       => $method->method_title,
			'description' => $method->method_description,
		);

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $method, $request ) );

		/**
		 * Filter shipping methods object returned from the REST API.
		 *
		 * @param WP_REST_Response   $response The response object.
		 * @param WC_Shipping_Method $method   Shipping method object used to create response.
		 * @param WP_REST_Request    $request  Request object.
		 */
		return apply_filters( 'woocommerce_rest_prepare_shipping_method', $response, $method, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param WC_Shipping_Method $method Shipping method object.
	 * @param WP_REST_Request    $request Request object.
	 * @return array
	 */
	protected function prepare_links( $method, $request ) {
		$links = array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%s', $this->namespace, $this->rest_base, $method->id ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);

		return $links;
	}

	/**
	 * Get the shipping method schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'shipping_method',
			'type'       => 'object',
			'properties' => array(
				'id'          => array(
					'description' => __( 'Method ID.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'title'       => array(
					'description' => __( 'Shipping method title.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'description' => array(
					'description' => __( 'Shipping method description.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get any query params needed.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}
}
