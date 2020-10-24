<?php
/**
 * REST API: WP_REST_Autosaves_Controller class.
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 5.0.0
 */

/**
 * Core class used to access autosaves via the REST API.
 *
 * @since 5.0.0
 *
 * @see WP_REST_Revisions_Controller
 * @see WP_REST_Controller
 */
class WP_REST_Autosaves_Controller extends WP_REST_Revisions_Controller {

	/**
	 * Parent post type.
	 *
	 * @since 5.0.0
	 * @var string
	 */
	private $parent_post_type;

	/**
	 * Parent post controller.
	 *
	 * @since 5.0.0
	 * @var WP_REST_Controller
	 */
	private $parent_controller;

	/**
	 * Revision controller.
	 *
	 * @since 5.0.0
	 * @var WP_REST_Controller
	 */
	private $revisions_controller;

	/**
	 * The base of the parent controller's route.
	 *
	 * @since 5.0.0
	 * @var string
	 */
	private $parent_base;

	/**
	 * Constructor.
	 *
	 * @since 5.0.0
	 *
	 * @param string $parent_post_type Post type of the parent.
	 */
	public function __construct( $parent_post_type ) {
		$this->parent_post_type = $parent_post_type;
		$post_type_object       = get_post_type_object( $parent_post_type );
		$parent_controller      = $post_type_object->get_rest_controller();

		if ( ! $parent_controller ) {
			$parent_controller = new WP_REST_Posts_Controller( $parent_post_type );
		}

		$this->parent_controller    = $parent_controller;
		$this->revisions_controller = new WP_REST_Revisions_Controller( $parent_post_type );
		$this->namespace            = 'wp/v2';
		$this->rest_base            = 'autosaves';
		$this->parent_base          = ! empty( $post_type_object->rest_base ) ? $post_type_object->rest_base : $post_type_object->name;
	}

	/**
	 * Registers the routes for autosaves.
	 *
	 * @since 5.0.0
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->parent_base . '/(?P<id>[\d]+)/' . $this->rest_base,
			array(
				'args'   => array(
					'parent' => array(
						'description' => __( 'The ID for the parent of the object.' ),
						'type'        => 'integer',
					),
				),
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
					'args'                => $this->parent_controller->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->parent_base . '/(?P<parent>[\d]+)/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'parent' => array(
						'description' => __( 'The ID for the parent of the object.' ),
						'type'        => 'integer',
					),
					'id'     => array(
						'description' => __( 'The ID for the object.' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this->revisions_controller, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

	}

	/**
	 * Get the parent post.
	 *
	 * @since 5.0.0
	 *
	 * @param int $parent_id Supplied ID.
	 * @return WP_Post|WP_Error Post object if ID is valid, WP_Error otherwise.
	 */
	protected function get_parent( $parent_id ) {
		return $this->revisions_controller->get_parent( $parent_id );
	}

	/**
	 * Checks if a given request has access to get autosaves.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		$parent = $this->get_parent( $request['id'] );
		if ( is_wp_error( $parent ) ) {
			return $parent;
		}

		if ( ! current_user_can( 'edit_post', $parent->ID ) ) {
			return new WP_Error(
				'rest_cannot_read',
				__( 'Sorry, you are not allowed to view autosaves of this post.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to create an autosave revision.
	 *
	 * Autosave revisions inherit permissions from the parent post,
	 * check if the current user has permission to edit the post.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has access to create the item, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		$id = $request->get_param( 'id' );

		if ( empty( $id ) ) {
			return new WP_Error(
				'rest_post_invalid_id',
				__( 'Invalid item ID.' ),
				array( 'status' => 404 )
			);
		}

		return $this->parent_controller->update_item_permissions_check( $request );
	}

	/**
	 * Creates, updates or deletes an autosave revision.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {

		if ( ! defined( 'DOING_AUTOSAVE' ) ) {
			define( 'DOING_AUTOSAVE', true );
		}

		$post = get_post( $request['id'] );

		if ( is_wp_error( $post ) ) {
			return $post;
		}

		$prepared_post     = $this->parent_controller->prepare_item_for_database( $request );
		$prepared_post->ID = $post->ID;
		$user_id           = get_current_user_id();

		if ( ( 'draft' === $post->post_status || 'auto-draft' === $post->post_status ) && $post->post_author == $user_id ) {
			// Draft posts for the same author: autosaving updates the post and does not create a revision.
			// Convert the post object to an array and add slashes, wp_update_post() expects escaped array.
			$autosave_id = wp_update_post( wp_slash( (array) $prepared_post ), true );
		} else {
			// Non-draft posts: create or update the post autosave.
			$autosave_id = $this->create_post_autosave( (array) $prepared_post );
		}

		if ( is_wp_error( $autosave_id ) ) {
			return $autosave_id;
		}

		$autosave = get_post( $autosave_id );
		$request->set_param( 'context', 'edit' );

		$response = $this->prepare_item_for_response( $autosave, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Get the autosave, if the ID is valid.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Post|WP_Error Revision post object if ID is valid, WP_Error otherwise.
	 */
	public function get_item( $request ) {
		$parent_id = (int) $request->get_param( 'parent' );

		if ( $parent_id <= 0 ) {
			return new WP_Error(
				'rest_post_invalid_id',
				__( 'Invalid post parent ID.' ),
				array( 'status' => 404 )
			);
		}

		$autosave = wp_get_post_autosave( $parent_id );

		if ( ! $autosave ) {
			return new WP_Error(
				'rest_post_no_autosave',
				__( 'There is no autosave revision for this post.' ),
				array( 'status' => 404 )
			);
		}

		$response = $this->prepare_item_for_response( $autosave, $request );
		return $response;
	}

	/**
	 * Gets a collection of autosaves using wp_get_post_autosave.
	 *
	 * Contains the user's autosave, for empty if it doesn't exist.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$parent = $this->get_parent( $request['id'] );
		if ( is_wp_error( $parent ) ) {
			return $parent;
		}

		$response  = array();
		$parent_id = $parent->ID;
		$revisions = wp_get_post_revisions( $parent_id, array( 'check_enabled' => false ) );

		foreach ( $revisions as $revision ) {
			if ( false !== strpos( $revision->post_name, "{$parent_id}-autosave" ) ) {
				$data       = $this->prepare_item_for_response( $revision, $request );
				$response[] = $this->prepare_response_for_collection( $data );
			}
		}

		return rest_ensure_response( $response );
	}


	/**
	 * Retrieves the autosave's schema, conforming to JSON Schema.
	 *
	 * @since 5.0.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = $this->revisions_controller->get_item_schema();

		$schema['properties']['preview_link'] = array(
			'description' => __( 'Preview link for the post.' ),
			'type'        => 'string',
			'format'      => 'uri',
			'context'     => array( 'edit' ),
			'readonly'    => true,
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Creates autosave for the specified post.
	 *
	 * From wp-admin/post.php.
	 *
	 * @since 5.0.0
	 *
	 * @param array $post_data Associative array containing the post data.
	 * @return mixed The autosave revision ID or WP_Error.
	 */
	public function create_post_autosave( $post_data ) {

		$post_id = (int) $post_data['ID'];
		$post    = get_post( $post_id );

		if ( is_wp_error( $post ) ) {
			return $post;
		}

		$user_id = get_current_user_id();

		// Store one autosave per author. If there is already an autosave, overwrite it.
		$old_autosave = wp_get_post_autosave( $post_id, $user_id );

		if ( $old_autosave ) {
			$new_autosave                = _wp_post_revision_data( $post_data, true );
			$new_autosave['ID']          = $old_autosave->ID;
			$new_autosave['post_author'] = $user_id;

			// If the new autosave has the same content as the post, delete the autosave.
			$autosave_is_different = false;

			foreach ( array_intersect( array_keys( $new_autosave ), array_keys( _wp_post_revision_fields( $post ) ) ) as $field ) {
				if ( normalize_whitespace( $new_autosave[ $field ] ) !== normalize_whitespace( $post->$field ) ) {
					$autosave_is_different = true;
					break;
				}
			}

			if ( ! $autosave_is_different ) {
				wp_delete_post_revision( $old_autosave->ID );
				return new WP_Error(
					'rest_autosave_no_changes',
					__( 'There is nothing to save. The autosave and the post content are the same.' ),
					array( 'status' => 400 )
				);
			}

			/** This filter is documented in wp-admin/post.php */
			do_action( 'wp_creating_autosave', $new_autosave );

			// wp_update_post() expects escaped array.
			return wp_update_post( wp_slash( $new_autosave ) );
		}

		// Create the new autosave as a special post revision.
		return _wp_put_post_revision( $post_data, true );
	}

	/**
	 * Prepares the revision for the REST response.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_Post         $post    Post revision object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $post, $request ) {

		$response = $this->revisions_controller->prepare_item_for_response( $post, $request );

		$fields = $this->get_fields_for_response( $request );

		if ( in_array( 'preview_link', $fields, true ) ) {
			$parent_id          = wp_is_post_autosave( $post );
			$preview_post_id    = false === $parent_id ? $post->ID : $parent_id;
			$preview_query_args = array();

			if ( false !== $parent_id ) {
				$preview_query_args['preview_id']    = $parent_id;
				$preview_query_args['preview_nonce'] = wp_create_nonce( 'post_preview_' . $parent_id );
			}

			$response->data['preview_link'] = get_preview_post_link( $preview_post_id, $preview_query_args );
		}

		$context        = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$response->data = $this->add_additional_fields_to_object( $response->data, $request );
		$response->data = $this->filter_response_by_context( $response->data, $context );

		/**
		 * Filters a revision returned from the API.
		 *
		 * Allows modification of the revision right before it is returned.
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WP_Post          $post     The original revision object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_autosave', $response, $post, $request );
	}

	/**
	 * Retrieves the query params for the autosaves collection.
	 *
	 * @since 5.0.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}
}
