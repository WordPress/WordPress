<?php
/**
 * REST API: WP_REST_Block_Navigation_Areas_Controller class
 *
 * @subpackage REST_API
 * @package WordPress
 */

/**
 * Core class used to access block navigation areas via the REST API.
 *
 * @since 5.9.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Block_Navigation_Areas_Controller extends WP_REST_Controller {

	/**
	 * Constructor.
	 *
	 * @since 5.9.0
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'block-navigation-areas';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 5.9.0
	 *
	 * @see register_rest_route()
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
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<area>[\w-]+)',
			array(
				'args'        => array(
					'area' => array(
						'description' => __( 'An alphanumeric identifier for the navigation area.' ),
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
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				'allow_batch' => array( 'v1' => true ),
				'schema'      => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks whether a given request has permission to read navigation areas.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|bool True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return new WP_Error(
				'rest_cannot_view',
				__( 'Sorry, you are not allowed to view navigation areas.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves all navigation areas, depending on user context.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$data = array();
		foreach ( get_navigation_areas() as $name => $description ) {
			$area   = $this->get_navigation_area_object( $name );
			$area   = $this->prepare_item_for_response( $area, $request );
			$data[] = $this->prepare_response_for_collection( $area );
		}
		return rest_ensure_response( $data );
	}

	/**
	 * Checks if a given request has access to read a navigation area.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|bool True if the request has read access for the item, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return new WP_Error(
				'rest_cannot_view',
				__( 'Sorry, you are not allowed to view navigation areas.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		if ( ! array_key_exists( $request['area'], get_navigation_areas() ) ) {
			return new WP_Error( 'rest_navigation_area_invalid', __( 'Invalid navigation area.' ), array( 'status' => 404 ) );
		}

		return true;
	}

	/**
	 * Checks if a request has access to update the specified term.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error True if the request has access to update the item, false or WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		return $this->get_item_permissions_check( $request );
	}

	/**
	 * Retrieves a specific navigation area.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$name = $request['area'];
		$area = $this->get_navigation_area_object( $name );
		$data = $this->prepare_item_for_response( $area, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Updates a specific navigation area.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$name = $request['area'];

		$mapping          = get_option( 'fse_navigation_areas', array() );
		$mapping[ $name ] = $request['navigation'];
		update_option( 'fse_navigation_areas', $mapping );

		$area = $this->get_navigation_area_object( $name );
		$data = $this->prepare_item_for_response( $area, $request );
		return rest_ensure_response( $data );
	}

	/**
	 * Converts navigation area name to a convenient object that this endpoint can reason about.
	 *
	 * @since 5.9.0
	 *
	 * @param string $name Navigation area name.
	 * @return stdClass An object representation of the navigation area.
	 */
	protected function get_navigation_area_object( $name ) {
		$available_areas   = get_navigation_areas();
		$mapping           = get_option( 'fse_navigation_areas', array() );
		$area              = new stdClass();
		$area->name        = $name;
		$area->navigation  = ! empty( $mapping[ $name ] ) ? $mapping[ $name ] : null;
		$area->description = $available_areas[ $name ];
		return $area;
	}

	/**
	 * Prepares a navigation area object for serialization.
	 *
	 * @since 5.9.0
	 *
	 * @param stdClass        $area    Post status data.
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Post status data.
	 */
	public function prepare_item_for_response( $area, $request ) {
		$areas      = get_navigation_areas();
		$navigation = ( isset( $areas[ $area->name ] ) ) ? $area->navigation : 0;

		$fields = $this->get_fields_for_response( $request );
		$data   = array();

		if ( rest_is_field_included( 'name', $fields ) ) {
			$data['name'] = $area->name;
		}

		if ( rest_is_field_included( 'description', $fields ) ) {
			$data['description'] = $area->description;
		}

		if ( rest_is_field_included( 'navigation', $fields ) ) {
			$data['navigation'] = (int) $navigation;
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		/**
		 * Filters a navigation area returned from the REST API.
		 *
		 * @since 5.9.0
		 *
		 * Allows modification of the navigation area data right before it is
		 * returned.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param object           $area     The original status object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_navigation_area', $response, $area, $request );
	}

	/**
	 * Retrieves the navigation area's schema, conforming to JSON Schema.
	 *
	 * @since 5.9.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->schema;
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'navigation-area',
			'type'       => 'object',
			'properties' => array(
				'name'        => array(
					'description' => __( 'The name of the navigation area.' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'description' => array(
					'description' => __( 'The description of the navigation area.' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'navigation'  => array(
					'description' => __( 'The ID of the assigned navigation.' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 * @since 5.9.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}

}
