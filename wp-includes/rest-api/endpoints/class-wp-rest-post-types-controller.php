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
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'types';
	}

	/**
	 * Registers the routes for post types.
	 *
	 * @since 4.7.0
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
			'/' . $this->rest_base . '/(?P<type>[\w-]+)',
			array(
				'args'   => array(
					'type' => array(
						'description' => __( 'An alphanumeric identifier for the post type.' ),
						'type'        => 'string',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks whether a given request has permission to read types.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( 'edit' === $request['context'] ) {
			$types = get_post_types( array( 'show_in_rest' => true ), 'objects' );

			foreach ( $types as $type ) {
				if ( current_user_can( $type->cap->edit_posts ) ) {
					return true;
				}
			}

			return new WP_Error(
				'rest_cannot_view',
				__( 'Sorry, you are not allowed to edit posts in this post type.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves all public post types.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$data  = array();
		$types = get_post_types( array( 'show_in_rest' => true ), 'objects' );

		foreach ( $types as $type ) {
			if ( 'edit' === $request['context'] && ! current_user_can( $type->cap->edit_posts ) ) {
				continue;
			}

			$post_type           = $this->prepare_item_for_response( $type, $request );
			$data[ $type->name ] = $this->prepare_response_for_collection( $post_type );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves a specific post type.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$obj = get_post_type_object( $request['type'] );

		if ( empty( $obj ) ) {
			return new WP_Error(
				'rest_type_invalid',
				__( 'Invalid post type.' ),
				array( 'status' => 404 )
			);
		}

		if ( empty( $obj->show_in_rest ) ) {
			return new WP_Error(
				'rest_cannot_read_type',
				__( 'Cannot view post type.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( 'edit' === $request['context'] && ! current_user_can( $obj->cap->edit_posts ) ) {
			return new WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to edit posts in this post type.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$data = $this->prepare_item_for_response( $obj, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Prepares a post type object for serialization.
	 *
	 * @since 4.7.0
	 * @since 5.9.0 Renamed `$post_type` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param WP_Post_Type    $item    Post type object.
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$post_type = $item;

		$taxonomies = wp_list_filter( get_object_taxonomies( $post_type->name, 'objects' ), array( 'show_in_rest' => true ) );
		$taxonomies = wp_list_pluck( $taxonomies, 'name' );
		$base       = ! empty( $post_type->rest_base ) ? $post_type->rest_base : $post_type->name;
		$namespace  = ! empty( $post_type->rest_namespace ) ? $post_type->rest_namespace : 'wp/v2';
		$supports   = get_all_post_type_supports( $post_type->name );

		$fields = $this->get_fields_for_response( $request );
		$data   = array();

		if ( rest_is_field_included( 'capabilities', $fields ) ) {
			$data['capabilities'] = $post_type->cap;
		}

		if ( rest_is_field_included( 'description', $fields ) ) {
			$data['description'] = $post_type->description;
		}

		if ( rest_is_field_included( 'hierarchical', $fields ) ) {
			$data['hierarchical'] = $post_type->hierarchical;
		}

		if ( rest_is_field_included( 'has_archive', $fields ) ) {
			$data['has_archive'] = $post_type->has_archive;
		}

		if ( rest_is_field_included( 'visibility', $fields ) ) {
			$data['visibility'] = array(
				'show_in_nav_menus' => (bool) $post_type->show_in_nav_menus,
				'show_ui'           => (bool) $post_type->show_ui,
			);
		}

		if ( rest_is_field_included( 'viewable', $fields ) ) {
			$data['viewable'] = is_post_type_viewable( $post_type );
		}

		if ( rest_is_field_included( 'labels', $fields ) ) {
			$data['labels'] = $post_type->labels;
		}

		if ( rest_is_field_included( 'name', $fields ) ) {
			$data['name'] = $post_type->label;
		}

		if ( rest_is_field_included( 'slug', $fields ) ) {
			$data['slug'] = $post_type->name;
		}

		if ( rest_is_field_included( 'icon', $fields ) ) {
			$data['icon'] = $post_type->menu_icon;
		}

		if ( rest_is_field_included( 'supports', $fields ) ) {
			$data['supports'] = $supports;
		}

		if ( rest_is_field_included( 'taxonomies', $fields ) ) {
			$data['taxonomies'] = array_values( $taxonomies );
		}

		if ( rest_is_field_included( 'rest_base', $fields ) ) {
			$data['rest_base'] = $base;
		}

		if ( rest_is_field_included( 'rest_namespace', $fields ) ) {
			$data['rest_namespace'] = $namespace;
		}

		if ( rest_is_field_included( 'template', $fields ) ) {
			$data['template'] = $post_type->template ?? array();
		}

		if ( rest_is_field_included( 'template_lock', $fields ) ) {
			$data['template_lock'] = ! empty( $post_type->template_lock ) ? $post_type->template_lock : false;
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$response->add_links( $this->prepare_links( $post_type ) );
		}

		/**
		 * Filters a post type returned from the REST API.
		 *
		 * Allows modification of the post type data right before it is returned.
		 *
		 * @since 4.7.0
		 *
		 * @param WP_REST_Response $response  The response object.
		 * @param WP_Post_Type     $post_type The original post type object.
		 * @param WP_REST_Request  $request   Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_post_type', $response, $post_type, $request );
	}

	/**
	 * Prepares links for the request.
	 *
	 * @since 6.1.0
	 *
	 * @param WP_Post_Type $post_type The post type.
	 * @return array Links for the given post type.
	 */
	protected function prepare_links( $post_type ) {
		return array(
			'collection'              => array(
				'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
			'https://api.w.org/items' => array(
				'href' => rest_url( rest_get_route_for_post_type_items( $post_type->name ) ),
			),
		);
	}

	/**
	 * Retrieves the post type's schema, conforming to JSON Schema.
	 *
	 * @since 4.7.0
	 * @since 4.8.0 The `supports` property was added.
	 * @since 5.9.0 The `visibility` and `rest_namespace` properties were added.
	 * @since 6.1.0 The `icon` property was added.
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'type',
			'type'       => 'object',
			'properties' => array(
				'capabilities'   => array(
					'description' => __( 'All capabilities used by the post type.' ),
					'type'        => 'object',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'description'    => array(
					'description' => __( 'A human-readable description of the post type.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'hierarchical'   => array(
					'description' => __( 'Whether or not the post type should have children.' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'viewable'       => array(
					'description' => __( 'Whether or not the post type can be viewed.' ),
					'type'        => 'boolean',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'labels'         => array(
					'description' => __( 'Human-readable labels for the post type for various contexts.' ),
					'type'        => 'object',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'name'           => array(
					'description' => __( 'The title for the post type.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'slug'           => array(
					'description' => __( 'An alphanumeric identifier for the post type.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'supports'       => array(
					'description' => __( 'All features, supported by the post type.' ),
					'type'        => 'object',
					'context'     => array( 'edit' ),
					'readonly'    => true,
				),
				'has_archive'    => array(
					'description' => __( 'If the value is a string, the value will be used as the archive slug. If the value is false the post type has no archive.' ),
					'type'        => array( 'string', 'boolean' ),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'taxonomies'     => array(
					'description' => __( 'Taxonomies associated with post type.' ),
					'type'        => 'array',
					'items'       => array(
						'type' => 'string',
					),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'rest_base'      => array(
					'description' => __( 'REST base route for the post type.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'rest_namespace' => array(
					'description' => __( 'REST route\'s namespace for the post type.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'visibility'     => array(
					'description' => __( 'The visibility settings for the post type.' ),
					'type'        => 'object',
					'context'     => array( 'edit' ),
					'readonly'    => true,
					'properties'  => array(
						'show_ui'           => array(
							'description' => __( 'Whether to generate a default UI for managing this post type.' ),
							'type'        => 'boolean',
						),
						'show_in_nav_menus' => array(
							'description' => __( 'Whether to make the post type available for selection in navigation menus.' ),
							'type'        => 'boolean',
						),
					),
				),
				'icon'           => array(
					'description' => __( 'The icon for the post type.' ),
					'type'        => array( 'string', 'null' ),
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'template'       => array(
					'type'        => array( 'array' ),
					'description' => __( 'The block template associated with the post type.' ),
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'template_lock'  => array(
					'type'        => array( 'string', 'boolean' ),
					'enum'        => array( 'all', 'insert', 'contentOnly', false ),
					'description' => __( 'The template_lock associated with the post type, or false if none.' ),
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
			),
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 * @since 4.7.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}
}
