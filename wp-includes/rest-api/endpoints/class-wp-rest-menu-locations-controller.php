<?php
/**
 * REST API: WP_REST_Menu_Locations_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 5.9.0
 */

/**
 * Core class used to access menu locations via the REST API.
 *
 * @since 5.9.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Menu_Locations_Controller extends WP_REST_Controller {

	/**
	 * Menu Locations Constructor.
	 *
	 * @since 5.9.0
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'menu-locations';
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
			'/' . $this->rest_base . '/(?P<location>[\w-]+)',
			array(
				'args'   => array(
					'location' => array(
						'description' => __( 'An alphanumeric identifier for the menu location.' ),
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
	 * Checks whether a given request has permission to read menu locations.
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
				__( 'Sorry, you are not allowed to view menu locations.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves all menu locations, depending on user context.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$data = array();

		foreach ( get_registered_nav_menus() as $name => $description ) {
			$location              = new stdClass();
			$location->name        = $name;
			$location->description = $description;

			$location      = $this->prepare_item_for_response( $location, $request );
			$data[ $name ] = $this->prepare_response_for_collection( $location );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Checks if a given request has access to read a menu location.
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
				__( 'Sorry, you are not allowed to view menu locations.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a specific menu location.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$registered_menus = get_registered_nav_menus();
		if ( ! array_key_exists( $request['location'], $registered_menus ) ) {
			return new WP_Error( 'rest_menu_location_invalid', __( 'Invalid menu location.' ), array( 'status' => 404 ) );
		}

		$location              = new stdClass();
		$location->name        = $request['location'];
		$location->description = $registered_menus[ $location->name ];

		$data = $this->prepare_item_for_response( $location, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Prepares a menu location object for serialization.
	 *
	 * @since 5.9.0
	 *
	 * @param stdClass        $item    Post status data.
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Menu location data.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$location  = $item;
		$locations = get_nav_menu_locations();
		$menu      = isset( $locations[ $location->name ] ) ? $locations[ $location->name ] : 0;

		$fields = $this->get_fields_for_response( $request );
		$data   = array();

		if ( rest_is_field_included( 'name', $fields ) ) {
			$data['name'] = $location->name;
		}

		if ( rest_is_field_included( 'description', $fields ) ) {
			$data['description'] = $location->description;
		}

		if ( rest_is_field_included( 'menu', $fields ) ) {
			$data['menu'] = (int) $menu;
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$response->add_links( $this->prepare_links( $location ) );
		}

		/**
		 * Filters menu location data returned from the REST API.
		 *
		 * @since 5.9.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param object           $location The original location object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_menu_location', $response, $location, $request );
	}

	/**
	 * Prepares links for the request.
	 *
	 * @since 5.9.0
	 *
	 * @param stdClass $location Menu location.
	 * @return array Links for the given menu location.
	 */
	protected function prepare_links( $location ) {
		$base = sprintf( '%s/%s', $this->namespace, $this->rest_base );

		// Entity meta.
		$links = array(
			'self'       => array(
				'href' => rest_url( trailingslashit( $base ) . $location->name ),
			),
			'collection' => array(
				'href' => rest_url( $base ),
			),
		);

		$locations = get_nav_menu_locations();
		$menu      = isset( $locations[ $location->name ] ) ? $locations[ $location->name ] : 0;
		if ( $menu ) {
			$path = rest_get_route_for_term( $menu );
			if ( $path ) {
				$url = rest_url( $path );

				$links['https://api.w.org/menu'][] = array(
					'href'       => $url,
					'embeddable' => true,
				);
			}
		}

		return $links;
	}

	/**
	 * Retrieves the menu location's schema, conforming to JSON Schema.
	 *
	 * @since 5.9.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'menu-location',
			'type'       => 'object',
			'properties' => array(
				'name'        => array(
					'description' => __( 'The name of the menu location.' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'description' => array(
					'description' => __( 'The description of the menu location.' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'menu'        => array(
					'description' => __( 'The ID of the assigned menu.' ),
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
