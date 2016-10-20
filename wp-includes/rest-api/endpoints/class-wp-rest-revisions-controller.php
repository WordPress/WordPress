<?php

class WP_REST_Revisions_Controller extends WP_REST_Controller {

	private $parent_post_type;
	private $parent_controller;
	private $parent_base;

	public function __construct( $parent_post_type ) {
		$this->parent_post_type = $parent_post_type;
		$this->parent_controller = new WP_REST_Posts_Controller( $parent_post_type );
		$this->namespace = 'wp/v2';
		$this->rest_base = 'revisions';
		$post_type_object = get_post_type_object( $parent_post_type );
		$this->parent_base = ! empty( $post_type_object->rest_base ) ? $post_type_object->rest_base : $post_type_object->name;
	}

	/**
	 * Register routes for revisions based on post types supporting revisions
	 *
	 * @access public
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->parent_base . '/(?P<parent>[\d]+)/' . $this->rest_base, array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'            => $this->get_collection_params(),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->parent_base . '/(?P<parent>[\d]+)/' . $this->rest_base . '/(?P<id>[\d]+)', array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'            => array(
					'context'          => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			array(
				'methods'         => WP_REST_Server::DELETABLE,
				'callback'        => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		));

	}

	/**
	 * Check if a given request has access to get revisions
	 *
	 * @access public
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {

		$parent = $this->get_post( $request['parent'] );
		if ( ! $parent ) {
			return true;
		}
		$parent_post_type_obj = get_post_type_object( $parent->post_type );
		if ( ! current_user_can( $parent_post_type_obj->cap->edit_post, $parent->ID ) ) {
			return new WP_Error( 'rest_cannot_read', __( 'Sorry, you cannot view revisions of this post.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Get a collection of revisions
	 *
	 * @access public
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {

		$parent = $this->get_post( $request['parent'] );
		if ( ! $request['parent'] || ! $parent || $this->parent_post_type !== $parent->post_type ) {
			return new WP_Error( 'rest_post_invalid_parent', __( 'Invalid post parent id.' ), array( 'status' => 404 ) );
		}

		$revisions = wp_get_post_revisions( $request['parent'] );

		$response = array();
		foreach ( $revisions as $revision ) {
			$data = $this->prepare_item_for_response( $revision, $request );
			$response[] = $this->prepare_response_for_collection( $data );
		}
		return rest_ensure_response( $response );
	}

	/**
	 * Check if a given request has access to get a specific revision
	 *
	 * @access public
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		return $this->get_items_permissions_check( $request );
	}

	/**
	 * Get one revision from the collection
	 *
	 * @access public
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|array
	 */
	public function get_item( $request ) {

		$parent = $this->get_post( $request['parent'] );
		if ( ! $request['parent'] || ! $parent || $this->parent_post_type !== $parent->post_type ) {
			return new WP_Error( 'rest_post_invalid_parent', __( 'Invalid post parent id.' ), array( 'status' => 404 ) );
		}

		$revision = $this->get_post( $request['id'] );
		if ( ! $revision || 'revision' !== $revision->post_type ) {
			return new WP_Error( 'rest_post_invalid_id', __( 'Invalid revision id.' ), array( 'status' => 404 ) );
		}

		$response = $this->prepare_item_for_response( $revision, $request );
		return rest_ensure_response( $response );
	}

	/**
	 * Check if a given request has access to delete a revision
	 *
	 * @access public
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function delete_item_permissions_check( $request ) {

		$response = $this->get_items_permissions_check( $request );
		if ( ! $response || is_wp_error( $response ) ) {
			return $response;
		}

		$post = $this->get_post( $request['id'] );
		if ( ! $post ) {
			return new WP_Error( 'rest_post_invalid_id', __( 'Invalid revision id.' ), array( 'status' => 404 ) );
		}
		$post_type = get_post_type_object( 'revision' );
		return current_user_can( $post_type->cap->delete_post, $post->ID );
	}

	/**
	 * Delete a single revision
	 *
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function delete_item( $request ) {
		$result = wp_delete_post( $request['id'], true );

		/**
		 * Fires after a revision is deleted via the REST API.
		 *
		 * @param (mixed) $result The revision object (if it was deleted or moved to the trash successfully)
		 *                        or false (failure). If the revision was moved to to the trash, $result represents
		 *                        its new state; if it was deleted, $result represents its state before deletion.
		 * @param WP_REST_Request $request The request sent to the API.
		 */
		do_action( 'rest_delete_revision', $result, $request );

		if ( $result ) {
			return true;
		} else {
			return new WP_Error( 'rest_cannot_delete', __( 'The post cannot be deleted.' ), array( 'status' => 500 ) );
		}
	}

	/**
	 * Prepare the revision for the REST response
	 *
	 * @access public
	 *
	 * @param WP_Post         $post    Post revision object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response
	 */
	public function prepare_item_for_response( $post, $request ) {

		$schema = $this->get_item_schema();

		$data = array();

		if ( ! empty( $schema['properties']['author'] ) ) {
			$data['author'] = $post->post_author;
		}

		if ( ! empty( $schema['properties']['date'] ) ) {
			$data['date'] = $this->prepare_date_response( $post->post_date_gmt, $post->post_date );
		}

		if ( ! empty( $schema['properties']['date_gmt'] ) ) {
			$data['date_gmt'] = $this->prepare_date_response( $post->post_date_gmt );
		}

		if ( ! empty( $schema['properties']['id'] ) ) {
			$data['id'] = $post->ID;
		}

		if ( ! empty( $schema['properties']['modified'] ) ) {
			$data['modified'] = $this->prepare_date_response( $post->post_modified_gmt, $post->post_modified );
		}

		if ( ! empty( $schema['properties']['modified_gmt'] ) ) {
			$data['modified_gmt'] = $this->prepare_date_response( $post->post_modified_gmt );
		}

		if ( ! empty( $schema['properties']['parent'] ) ) {
			$data['parent'] = (int) $post->post_parent;
		}

		if ( ! empty( $schema['properties']['slug'] ) ) {
			$data['slug'] = $post->post_name;
		}

		if ( ! empty( $schema['properties']['guid'] ) ) {
			$data['guid'] = array(
				/** This filter is documented in wp-includes/post-template.php */
				'rendered' => apply_filters( 'get_the_guid', $post->guid ),
				'raw'      => $post->guid,
			);
		}

		if ( ! empty( $schema['properties']['title'] ) ) {
			$data['title'] = array(
				'raw'      => $post->post_title,
				'rendered' => get_the_title( $post->ID ),
			);
		}

		if ( ! empty( $schema['properties']['content'] ) ) {

			$data['content'] = array(
				'raw'      => $post->post_content,
				/** This filter is documented in wp-includes/post-template.php */
				'rendered' => apply_filters( 'the_content', $post->post_content ),
			);
		}

		if ( ! empty( $schema['properties']['excerpt'] ) ) {
			$data['excerpt'] = array(
				'raw'      => $post->post_excerpt,
				'rendered' => $this->prepare_excerpt_response( $post->post_excerpt, $post ),
			);
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data = $this->add_additional_fields_to_object( $data, $request );
		$data = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );

		if ( ! empty( $data['parent'] ) ) {
			$response->add_link( 'parent', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->parent_base, $data['parent'] ) ) );
		}

		/**
		 * Filter a revision returned from the API.
		 *
		 * Allows modification of the revision right before it is returned.
		 *
		 * @param WP_REST_Response  $response   The response object.
		 * @param WP_Post           $post       The original revision object.
		 * @param WP_REST_Request   $request    Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_revision', $response, $post, $request );
	}

	/**
	 * Check the post_date_gmt or modified_gmt and prepare any post or
	 * modified date for single post output.
	 *
	 * @access protected
	 *
	 * @param string      $date_gmt GMT publication time.
	 * @param string|null $date     Optional, default is null. Local publication time.
	 * @return string|null ISO8601/RFC3339 formatted datetime.
	 */
	protected function prepare_date_response( $date_gmt, $date = null ) {
		if ( '0000-00-00 00:00:00' === $date_gmt ) {
			return null;
		}

		if ( isset( $date ) ) {
			return mysql_to_rfc3339( $date );
		}

		return mysql_to_rfc3339( $date_gmt );
	}

	/**
	 * Get the revision's schema, conforming to JSON Schema
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => "{$this->parent_post_type}-revision",
			'type'       => 'object',
			/*
			 * Base properties for every Revision
			 */
			'properties' => array(
				'author'          => array(
					'description' => __( 'The id for the author of the object.' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'date'            => array(
					'description' => __( 'The date the object was published.' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'date_gmt'        => array(
					'description' => __( 'The date the object was published, as GMT.' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'guid'            => array(
					'description' => __( 'GUID for the object, as it exists in the database.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'id'              => array(
					'description' => __( 'Unique identifier for the object.' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'modified'        => array(
					'description' => __( 'The date the object was last modified.' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'modified_gmt'    => array(
					'description' => __( 'The date the object was last modified, as GMT.' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'parent'          => array(
					'description' => __( 'The id for the parent of the object.' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
					),
				'slug'            => array(
					'description' => __( 'An alphanumeric identifier for the object unique to its type.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
			),
		);

		$parent_schema = $this->parent_controller->get_item_schema();

		if ( ! empty( $parent_schema['properties']['title'] ) ) {
			$schema['properties']['title'] = $parent_schema['properties']['title'];
		}
		if ( ! empty( $parent_schema['properties']['content'] ) ) {
			$schema['properties']['content'] = $parent_schema['properties']['content'];
		}
		if ( ! empty( $parent_schema['properties']['excerpt'] ) ) {
			$schema['properties']['excerpt'] = $parent_schema['properties']['excerpt'];
		}
		if ( ! empty( $parent_schema['properties']['guid'] ) ) {
			$schema['properties']['guid'] = $parent_schema['properties']['guid'];
		}

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for collections
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}

	/**
	 * Check the post excerpt and prepare it for single post output.
	 *
	 * @access protected
	 *
	 * @param string  $excerpt The post excerpt.
	 * @param WP_Post $post    Post revision object.
	 * @return string|null $excerpt
	 */
	protected function prepare_excerpt_response( $excerpt, $post ) {

		/** This filter is documented in wp-includes/post-template.php */
		$excerpt = apply_filters( 'the_excerpt', $excerpt, $post );

		if ( empty( $excerpt ) ) {
			return '';
		}

		return $excerpt;
	}
}
