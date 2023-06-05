<?php
/**
 * REST API Tax Classes controller
 *
 * Handles requests to the /taxes/classes endpoint.
 *
 * @package WooCommerce\RestApi
 * @since    3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Tax Classes controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Controller
 */
class WC_REST_Tax_Classes_V1_Controller extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'taxes/classes';

	/**
	 * Register the routes for tax classes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<slug>\w[\w\s\-]*)',
			array(
				'args' => array(
					'slug' => array(
						'description' => __( 'Unique slug for the resource.', 'woocommerce' ),
						'type'        => 'string',
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'default'     => false,
							'type'        => 'boolean',
							'description' => __( 'Required to be true, as resource does not support trashing.', 'woocommerce' ),
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check whether a given request has permission to read tax classes.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access create tax classes.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|WP_Error
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'create' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_create', __( 'Sorry, you are not allowed to create resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access delete a tax.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|WP_Error
	 */
	public function delete_item_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'delete' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_delete', __( 'Sorry, you are not allowed to delete this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Get all tax classes.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array
	 */
	public function get_items( $request ) {
		$tax_classes = array();

		// Add standard class.
		$tax_classes[] = array(
			'slug' => 'standard',
			'name' => __( 'Standard rate', 'woocommerce' ),
		);

		$classes = WC_Tax::get_tax_classes();

		foreach ( $classes as $class ) {
			$tax_classes[] = array(
				'slug' => sanitize_title( $class ),
				'name' => $class,
			);
		}

		$data = array();
		foreach ( $tax_classes as $tax_class ) {
			$class  = $this->prepare_item_for_response( $tax_class, $request );
			$class  = $this->prepare_response_for_collection( $class );
			$data[] = $class;
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Create a single tax class.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {
		$tax_class = WC_Tax::create_tax_class( $request['name'] );

		if ( is_wp_error( $tax_class ) ) {
			return new WP_Error( 'woocommerce_rest_' . $tax_class->get_error_code(), $tax_class->get_error_message(), array( 'status' => 400 ) );
		}

		$this->update_additional_fields_for_object( $tax_class, $request );

		/**
		 * Fires after a tax class is created or updated via the REST API.
		 *
		 * @param stdClass        $tax_class Data used to create the tax class.
		 * @param WP_REST_Request $request   Request object.
		 * @param boolean         $creating  True when creating tax class, false when updating tax class.
		 */
		do_action( 'woocommerce_rest_insert_tax_class', (object) $tax_class, $request, true );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $tax_class, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '/%s/%s/%s', $this->namespace, $this->rest_base, $tax_class['slug'] ) ) );

		return $response;
	}

	/**
	 * Delete a single tax class.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {
		global $wpdb;

		$force = isset( $request['force'] ) ? (bool) $request['force'] : false;

		// We don't support trashing for this type, error out.
		if ( ! $force ) {
			return new WP_Error( 'woocommerce_rest_trash_not_supported', __( 'Taxes do not support trashing.', 'woocommerce' ), array( 'status' => 501 ) );
		}

		$tax_class = WC_Tax::get_tax_class_by( 'slug', sanitize_title( $request['slug'] ) );
		$deleted   = WC_Tax::delete_tax_class_by( 'slug', sanitize_title( $request['slug'] ) );

		if ( ! $deleted ) {
			return new WP_Error( 'woocommerce_rest_invalid_id', __( 'Invalid resource id.', 'woocommerce' ), array( 'status' => 400 ) );
		}

		if ( is_wp_error( $deleted ) ) {
			return new WP_Error( 'woocommerce_rest_' . $deleted->get_error_code(), $deleted->get_error_message(), array( 'status' => 400 ) );
		}

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $tax_class, $request );

		/**
		 * Fires after a tax class is deleted via the REST API.
		 *
		 * @param stdClass         $tax_class The tax data.
		 * @param WP_REST_Response $response  The response returned from the API.
		 * @param WP_REST_Request  $request   The request sent to the API.
		 */
		do_action( 'woocommerce_rest_delete_tax', (object) $tax_class, $response, $request );

		return $response;
	}

	/**
	 * Prepare a single tax class output for response.
	 *
	 * @param array           $tax_class Tax class data.
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $tax_class, $request ) {
		$data = $tax_class;

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links() );

		/**
		 * Filter tax object returned from the REST API.
		 *
		 * @param WP_REST_Response $response  The response object.
		 * @param stdClass         $tax_class Tax object used to create response.
		 * @param WP_REST_Request  $request   Request object.
		 */
		return apply_filters( 'woocommerce_rest_prepare_tax', $response, (object) $tax_class, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @return array Links for the given tax class.
	 */
	protected function prepare_links() {
		$links = array(
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);

		return $links;
	}

	/**
	 * Get the Tax Classes schema, conforming to JSON Schema
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'tax_class',
			'type'       => 'object',
			'properties' => array(
				'slug' => array(
					'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'name' => array(
					'description' => __( 'Tax class name.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}
}
