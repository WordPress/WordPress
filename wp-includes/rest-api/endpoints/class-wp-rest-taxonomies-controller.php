<?php
/**
 * REST API: WP_REST_Taxonomies_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 4.7.0
 */

/**
 * Core class used to manage taxonomies via the REST API.
 *
 * @since 4.7.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Taxonomies_Controller extends WP_REST_Controller {

	/**
	 * Constructor.
	 *
	 * @since 4.7.0
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'taxonomies';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 4.7.0
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'            => $this->get_collection_params(),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<taxonomy>[\w-]+)', array(
			'args' => array(
				'taxonomy' => array(
					'description'  => __( 'An alphanumeric identifier for the taxonomy.' ),
					'type'         => 'string',
				),
			),
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'            => array(
					'context'     => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	/**
	 * Checks whether a given request has permission to read taxonomies.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( 'edit' === $request['context'] ) {
			if ( ! empty( $request['type'] ) ) {
				$taxonomies = get_object_taxonomies( $request['type'], 'objects' );
			} else {
				$taxonomies = get_taxonomies( '', 'objects' );
			}
			foreach ( $taxonomies as $taxonomy ) {
				if ( ! empty( $taxonomy->show_in_rest ) && current_user_can( $taxonomy->cap->assign_terms ) ) {
					return true;
				}
			}
			return new WP_Error( 'rest_cannot_view', __( 'Sorry, you are not allowed to manage terms in this taxonomy.' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Retrieves all public taxonomies.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {

		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();

		if ( isset( $registered['type'] ) && ! empty( $request['type'] ) ) {
			$taxonomies = get_object_taxonomies( $request['type'], 'objects' );
		} else {
			$taxonomies = get_taxonomies( '', 'objects' );
		}
		$data = array();
		foreach ( $taxonomies as $tax_type => $value ) {
			if ( empty( $value->show_in_rest ) || ( 'edit' === $request['context'] && ! current_user_can( $value->cap->assign_terms ) ) ) {
				continue;
			}
			$tax = $this->prepare_item_for_response( $value, $request );
			$tax = $this->prepare_response_for_collection( $tax );
			$data[ $tax_type ] = $tax;
		}

		if ( empty( $data ) ) {
			// Response should still be returned as a JSON object when it is empty.
			$data = (object) $data;
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Checks if a given request has access to a taxonomy.
	 *
	 * @since 4.7.0
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access for the item, otherwise false or WP_Error object.
	 */
	public function get_item_permissions_check( $request ) {

		$tax_obj = get_taxonomy( $request['taxonomy'] );

		if ( $tax_obj ) {
			if ( empty( $tax_obj->show_in_rest ) ) {
				return false;
			}
			if ( 'edit' === $request['context'] && ! current_user_can( $tax_obj->cap->assign_terms ) ) {
				return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to manage terms in this taxonomy.' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		return true;
	}

	/**
	 * Retrieves a specific taxonomy.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$tax_obj = get_taxonomy( $request['taxonomy'] );
		if ( empty( $tax_obj ) ) {
			return new WP_Error( 'rest_taxonomy_invalid', __( 'Invalid taxonomy.' ), array( 'status' => 404 ) );
		}
		$data = $this->prepare_item_for_response( $tax_obj, $request );
		return rest_ensure_response( $data );
	}

	/**
	 * Prepares a taxonomy object for serialization.
	 *
	 * @since 4.7.0
	 *
	 * @param stdClass        $taxonomy Taxonomy data.
	 * @param WP_REST_Request $request  Full details about the request.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $taxonomy, $request ) {
		$base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;

		$fields = $this->get_fields_for_response( $request );
		$data   = array();

		if ( in_array( 'name', $fields, true ) ) {
			$data['name'] = $taxonomy->label;
		}

		if ( in_array( 'slug', $fields, true ) ) {
			$data['slug'] = $taxonomy->name;
		}

		if ( in_array( 'capabilities', $fields, true ) ) {
			$data['capabilities'] = $taxonomy->cap;
		}

		if ( in_array( 'description', $fields, true ) ) {
			$data['description'] = $taxonomy->description;
		}

		if ( in_array( 'labels', $fields, true ) ) {
			$data['labels'] = $taxonomy->labels;
		}

		if ( in_array( 'types', $fields, true ) ) {
			$data['types'] = $taxonomy->object_type;
		}

		if ( in_array( 'show_cloud', $fields, true ) ) {
			$data['show_cloud'] = $taxonomy->show_tagcloud;
		}

		if ( in_array( 'hierarchical', $fields, true ) ) {
			$data['hierarchical'] = $taxonomy->hierarchical;
		}

		if ( in_array( 'rest_base', $fields, true ) ) {
			$data['rest_base'] = $base;
		}

		if ( in_array( 'visibility', $fields, true ) ) {
			$data['visibility'] = array(
				'public'             => (bool) $taxonomy->public,
				'publicly_queryable' => (bool) $taxonomy->publicly_queryable,
				'show_admin_column'  => (bool) $taxonomy->show_admin_column,
				'show_in_nav_menus'  => (bool) $taxonomy->show_in_nav_menus,
				'show_in_quick_edit' => (bool) $taxonomy->show_in_quick_edit,
				'show_ui'            => (bool) $taxonomy->show_ui,
			);
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data = $this->add_additional_fields_to_object( $data, $request );
		$data = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links( array(
			'collection'                => array(
				'href'                  => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
			'https://api.w.org/items'   => array(
				'href'                  => rest_url( sprintf( 'wp/v2/%s', $base ) ),
			),
		) );

		/**
		 * Filters a taxonomy returned from the REST API.
		 *
		 * Allows modification of the taxonomy data right before it is returned.
		 *
		 * @since 4.7.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param object           $item     The original taxonomy object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_taxonomy', $response, $taxonomy, $request );
	}

	/**
	 * Retrieves the taxonomy's schema, conforming to JSON Schema.
	 *
	 * @since 4.7.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'              => 'http://json-schema.org/draft-04/schema#',
			'title'                => 'taxonomy',
			'type'                 => 'object',
			'properties'           => array(
				'capabilities'     => array(
					'description'  => __( 'All capabilities used by the taxonomy.' ),
					'type'         => 'object',
					'context'      => array( 'edit' ),
					'readonly'     => true,
				),
				'description'      => array(
					'description'  => __( 'A human-readable description of the taxonomy.' ),
					'type'         => 'string',
					'context'      => array( 'view', 'edit' ),
					'readonly'     => true,
				),
				'hierarchical'     => array(
					'description'  => __( 'Whether or not the taxonomy should have children.' ),
					'type'         => 'boolean',
					'context'      => array( 'view', 'edit' ),
					'readonly'     => true,
				),
				'labels'           => array(
					'description'  => __( 'Human-readable labels for the taxonomy for various contexts.' ),
					'type'         => 'object',
					'context'      => array( 'edit' ),
					'readonly'     => true,
				),
				'name'             => array(
					'description'  => __( 'The title for the taxonomy.' ),
					'type'         => 'string',
					'context'      => array( 'view', 'edit', 'embed' ),
					'readonly'     => true,
				),
				'slug'             => array(
					'description'  => __( 'An alphanumeric identifier for the taxonomy.' ),
					'type'         => 'string',
					'context'      => array( 'view', 'edit', 'embed' ),
					'readonly'     => true,
				),
				'show_cloud'       => array(
					'description'  => __( 'Whether or not the term cloud should be displayed.' ),
					'type'         => 'boolean',
					'context'      => array( 'edit' ),
					'readonly'     => true,
				),
				'types'            => array(
					'description'  => __( 'Types associated with the taxonomy.' ),
					'type'         => 'array',
					'items'        => array(
						'type' => 'string',
					),
					'context'      => array( 'view', 'edit' ),
					'readonly'     => true,
				),
				'rest_base'            => array(
					'description'  => __( 'REST base route for the taxonomy.' ),
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
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$new_params = array();
		$new_params['context'] = $this->get_context_param( array( 'default' => 'view' ) );
		$new_params['type'] = array(
			'description'  => __( 'Limit results to taxonomies associated with a specific post type.' ),
			'type'         => 'string',
		);
		return $new_params;
	}

}
