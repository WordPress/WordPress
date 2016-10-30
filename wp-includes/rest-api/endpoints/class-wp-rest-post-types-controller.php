<?php
/**
 * REST API: WP_REST_Post_Types_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 4.7.0
 */

/**
 * Core class to access post types via the REST API.
 *
 * @since 4.7.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Post_Types_Controller extends WP_REST_Controller {

	/**
	 * Constructor.
	 *
	 * @since 4.7.0
	 * @access public
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'types';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<type>[\w-]+)', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_item' ),
				'args'     => array(
					'context' => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	/**
	 * Checks whether a given request has permission to read types.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|true True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( 'edit' === $request['context'] ) {
			foreach ( get_post_types( array(), 'object' ) as $post_type ) {
				if ( ! empty( $post_type->show_in_rest ) && current_user_can( $post_type->cap->edit_posts ) ) {
					return true;
				}
			}

			return new WP_Error( 'rest_cannot_view', __( 'Sorry, you cannot view this resource with edit context.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Retrieves all public post types.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$data = array();

		foreach ( get_post_types( array(), 'object' ) as $obj ) {
			if ( empty( $obj->show_in_rest ) || ( 'edit' === $request['context'] && ! current_user_can( $obj->cap->edit_posts ) ) ) {
				continue;
			}

			$post_type = $this->prepare_item_for_response( $obj, $request );
			$data[ $obj->name ] = $this->prepare_response_for_collection( $post_type );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves a specific post type.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$obj = get_post_type_object( $request['type'] );

		if ( empty( $obj ) ) {
			return new WP_Error( 'rest_type_invalid', __( 'Invalid resource.' ), array( 'status' => 404 ) );
		}

		if ( empty( $obj->show_in_rest ) ) {
			return new WP_Error( 'rest_cannot_read_type', __( 'Cannot view resource.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		if ( 'edit' === $request['context'] && ! current_user_can( $obj->cap->edit_posts ) ) {
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to manage this resource.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		$data = $this->prepare_item_for_response( $obj, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Prepares a post type object for serialization.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param stdClass        $post_type Post type data.
	 * @param WP_REST_Request $request   Full details about the request.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $post_type, $request ) {
		$data = array(
			'capabilities' => $post_type->cap,
			'description'  => $post_type->description,
			'hierarchical' => $post_type->hierarchical,
			'labels'       => $post_type->labels,
			'name'         => $post_type->label,
			'slug'         => $post_type->name,
		);
		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$base = ! empty( $post_type->rest_base ) ? $post_type->rest_base : $post_type->name;

		$response->add_links( array(
			'collection' => array(
				'href'   => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
			'https://api.w.org/items' => array(
				'href' => rest_url( sprintf( 'wp/v2/%s', $base ) ),
			),
		) );

		/**
		 * Filters a post type returned from the API.
		 *
		 * Allows modification of the post type data right before it is returned.
		 *
		 * @since 4.7.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param object           $item     The original post type object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_post_type', $response, $post_type, $request );
	}

	/**
	 * Retrieves the post type's schema, conforming to JSON Schema.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'              => 'http://json-schema.org/draft-04/schema#',
			'title'                => 'type',
			'type'                 => 'object',
			'properties'           => array(
				'capabilities'     => array(
					'description'  => __( 'All capabilities used by the resource.' ),
					'type'         => 'array',
					'context'      => array( 'edit' ),
					'readonly'     => true,
				),
				'description'      => array(
					'description'  => __( 'A human-readable description of the resource.' ),
					'type'         => 'string',
					'context'      => array( 'view', 'edit' ),
					'readonly'     => true,
				),
				'hierarchical'     => array(
					'description'  => __( 'Whether or not the resource should have children.' ),
					'type'         => 'boolean',
					'context'      => array( 'view', 'edit' ),
					'readonly'     => true,
				),
				'labels'           => array(
					'description'  => __( 'Human-readable labels for the resource for various contexts.' ),
					'type'         => 'object',
					'context'      => array( 'edit' ),
					'readonly'     => true,
				),
				'name'             => array(
					'description'  => __( 'The title for the resource.' ),
					'type'         => 'string',
					'context'      => array( 'view', 'edit', 'embed' ),
					'readonly'     => true,
				),
				'slug'             => array(
					'description'  => __( 'An alphanumeric identifier for the resource.' ),
					'type'         => 'string',
					'context'      => array( 'view', 'edit', 'embed' ),
					'readonly'     => true,
				),
			),
		);
		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}

}
