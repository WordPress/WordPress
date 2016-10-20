<?php

/**
 * Access comments
 */
class WP_REST_Comments_Controller extends WP_REST_Controller {

	/**
	 * Instance of a comment meta fields object.
	 *
	 * @access protected
	 * @var WP_REST_Comment_Meta_Fields
	 */
	protected $meta;

	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'comments';

		$this->meta = new WP_REST_Comment_Meta_Fields();
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'   => WP_REST_Server::READABLE,
				'callback'  => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'      => $this->get_collection_params(),
			),
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args'     => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'     => array(
					'context'          => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'     => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
			),
			array(
				'methods'  => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'     => array(
					'force'    => array(
						'default'     => false,
						'description' => __( 'Whether to bypass trash and force deletion.' ),
					),
				),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	/**
	 * Check if a given request has access to read comments
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {

		if ( ! empty( $request['post'] ) ) {
			foreach ( (array) $request['post'] as $post_id ) {
				$post = $this->get_post( $post_id );
				if ( ! empty( $post_id ) && $post && ! $this->check_read_post_permission( $post ) ) {
					return new WP_Error( 'rest_cannot_read_post', __( 'Sorry, you cannot read the post for this comment.' ), array( 'status' => rest_authorization_required_code() ) );
				} elseif ( 0 === $post_id && ! current_user_can( 'moderate_comments' ) ) {
					return new WP_Error( 'rest_cannot_read', __( 'Sorry, you cannot read comments without a post.' ), array( 'status' => rest_authorization_required_code() ) );
				}
			}
		}

		if ( ! empty( $request['context'] ) && 'edit' === $request['context'] && ! current_user_can( 'moderate_comments' ) ) {
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you cannot view comments with edit context.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			$protected_params = array( 'author', 'author_exclude', 'karma', 'author_email', 'type', 'status' );
			$forbidden_params = array();
			foreach ( $protected_params as $param ) {
				if ( 'status' === $param ) {
					if ( 'approve' !== $request[ $param ] ) {
						$forbidden_params[] = $param;
					}
				} elseif ( 'type' === $param ) {
					if ( 'comment' !== $request[ $param ] ) {
						$forbidden_params[] = $param;
					}
				} elseif ( ! empty( $request[ $param ] ) ) {
					$forbidden_params[] = $param;
				}
			}
			if ( ! empty( $forbidden_params ) ) {
				return new WP_Error( 'rest_forbidden_param', sprintf( __( 'Query parameter not permitted: %s' ), implode( ', ', $forbidden_params ) ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		return true;
	}

	/**
	 * Get a list of comments.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {

		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();

		// This array defines mappings between public API query parameters whose
		// values are accepted as-passed, and their internal WP_Query parameter
		// name equivalents (some are the same). Only values which are also
		// present in $registered will be set.
		$parameter_mappings = array(
			'author'         => 'author__in',
			'author_email'   => 'author_email',
			'author_exclude' => 'author__not_in',
			'exclude'        => 'comment__not_in',
			'include'        => 'comment__in',
			'karma'          => 'karma',
			'offset'         => 'offset',
			'order'          => 'order',
			'parent'         => 'parent__in',
			'parent_exclude' => 'parent__not_in',
			'per_page'       => 'number',
			'post'           => 'post__in',
			'search'         => 'search',
			'status'         => 'status',
			'type'           => 'type',
		);

		$prepared_args = array();

		// For each known parameter which is both registered and present in the request,
		// set the parameter's value on the query $prepared_args.
		foreach ( $parameter_mappings as $api_param => $wp_param ) {
			if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
				$prepared_args[ $wp_param ] = $request[ $api_param ];
			}
		}

		// Ensure certain parameter values default to empty strings.
		foreach ( array( 'author_email', 'karma', 'search' ) as $param ) {
			if ( ! isset( $prepared_args[ $param ] ) ) {
				$prepared_args[ $param ] = '';
			}
		}

		if ( isset( $registered['orderby'] ) ) {
			$prepared_args['orderby'] = $this->normalize_query_param( $request['orderby'] );
		}

		$prepared_args['no_found_rows'] = false;

		$prepared_args['date_query'] = array();
		// Set before into date query. Date query must be specified as an array of an array.
		if ( isset( $registered['before'], $request['before'] ) ) {
			$prepared_args['date_query'][0]['before'] = $request['before'];
		}

		// Set after into date query. Date query must be specified as an array of an array.
		if ( isset( $registered['after'], $request['after'] ) ) {
			$prepared_args['date_query'][0]['after'] = $request['after'];
		}

		if ( isset( $registered['page'] ) && empty( $request['offset'] ) ) {
			$prepared_args['offset'] = $prepared_args['number'] * ( absint( $request['page'] ) - 1 );
		}

		/**
		 * Filter arguments, before passing to WP_Comment_Query, when querying comments via the REST API.
		 *
		 * @see https://developer.wordpress.org/reference/classes/wp_comment_query/
		 *
		 * @param array           $prepared_args Array of arguments for WP_Comment_Query.
		 * @param WP_REST_Request $request       The current request.
		 */
		$prepared_args = apply_filters( 'rest_comment_query', $prepared_args, $request );

		$query = new WP_Comment_Query;
		$query_result = $query->query( $prepared_args );

		$comments = array();
		foreach ( $query_result as $comment ) {
			if ( ! $this->check_read_permission( $comment ) ) {
				continue;
			}

			$data = $this->prepare_item_for_response( $comment, $request );
			$comments[] = $this->prepare_response_for_collection( $data );
		}

		$total_comments = (int) $query->found_comments;
		$max_pages = (int) $query->max_num_pages;
		if ( $total_comments < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count
			unset( $prepared_args['number'], $prepared_args['offset'] );
			$query = new WP_Comment_Query;
			$prepared_args['count'] = true;

			$total_comments = $query->query( $prepared_args );
			$max_pages = ceil( $total_comments / $request['per_page'] );
		}

		$response = rest_ensure_response( $comments );
		$response->header( 'X-WP-Total', $total_comments );
		$response->header( 'X-WP-TotalPages', $max_pages );

		$base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );
		if ( $request['page'] > 1 ) {
			$prev_page = $request['page'] - 1;
			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}
			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $request['page'] ) {
			$next_page = $request['page'] + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Check if a given request has access to read the comment
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		$id = (int) $request['id'];

		$comment = get_comment( $id );

		if ( ! $comment ) {
			return true;
		}

		if ( ! $this->check_read_permission( $comment ) ) {
			return new WP_Error( 'rest_cannot_read', __( 'Sorry, you cannot read this comment.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		$post = $this->get_post( $comment->comment_post_ID );

		if ( $post && ! $this->check_read_post_permission( $post ) ) {
			return new WP_Error( 'rest_cannot_read_post', __( 'Sorry, you cannot read the post for this comment.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		if ( ! empty( $request['context'] ) && 'edit' === $request['context'] && ! current_user_can( 'moderate_comments' ) ) {
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you cannot view this comment with edit context.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Get a comment.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$id = (int) $request['id'];

		$comment = get_comment( $id );
		if ( empty( $comment ) ) {
			return new WP_Error( 'rest_comment_invalid_id', __( 'Invalid comment id.' ), array( 'status' => 404 ) );
		}

		if ( ! empty( $comment->comment_post_ID ) ) {
			$post = $this->get_post( $comment->comment_post_ID );
			if ( empty( $post ) ) {
				return new WP_Error( 'rest_post_invalid_id', __( 'Invalid post id.' ), array( 'status' => 404 ) );
			}
		}

		$data = $this->prepare_item_for_response( $comment, $request );
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Check if a given request has access to create a comment
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function create_item_permissions_check( $request ) {

		if ( ! is_user_logged_in() && get_option( 'comment_registration' ) ) {
			return new WP_Error( 'rest_comment_login_required', __( 'Sorry, you must be logged in to comment.' ), array( 'status' => 401 ) );
		}

		// Limit who can set comment `author`, `karma` or `status` to anything other than the default.
		if ( isset( $request['author'] ) && get_current_user_id() !== $request['author'] && ! current_user_can( 'moderate_comments' ) ) {
			return new WP_Error( 'rest_comment_invalid_author', __( 'Comment author invalid.' ), array( 'status' => rest_authorization_required_code() ) );
		}
		if ( isset( $request['karma'] ) && $request['karma'] > 0 && ! current_user_can( 'moderate_comments' ) ) {
			return new WP_Error( 'rest_comment_invalid_karma', __( 'Sorry, you cannot set karma for comments.' ), array( 'status' => rest_authorization_required_code() ) );
		}
		if ( isset( $request['status'] ) && ! current_user_can( 'moderate_comments' ) ) {
			return new WP_Error( 'rest_comment_invalid_status', __( 'Sorry, you cannot set status for comments.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		if ( empty( $request['post'] ) && ! current_user_can( 'moderate_comments' ) ) {
			return new WP_Error( 'rest_comment_invalid_post_id', __( 'Sorry, you cannot create this comment without a post.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		if ( ! empty( $request['post'] ) && $post = $this->get_post( (int) $request['post'] ) ) {
			if ( 'draft' === $post->post_status ) {
				return new WP_Error( 'rest_comment_draft_post', __( 'Sorry, you cannot create a comment on this post.' ), array( 'status' => 403 ) );
			}

			if ( 'trash' === $post->post_status ) {
				return new WP_Error( 'rest_comment_trash_post', __( 'Sorry, you cannot create a comment on this post.' ), array( 'status' => 403 ) );
			}

			if ( ! $this->check_read_post_permission( $post ) ) {
				return new WP_Error( 'rest_cannot_read_post', __( 'Sorry, you cannot read the post for this comment.' ), array( 'status' => rest_authorization_required_code() ) );
			}

			if ( ! comments_open( $post->ID ) ) {
				return new WP_Error( 'rest_comment_closed', __( 'Sorry, comments are closed on this post.' ), array( 'status' => 403 ) );
			}
		}

		return true;
	}

	/**
	 * Create a comment.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new WP_Error( 'rest_comment_exists', __( 'Cannot create existing comment.' ), array( 'status' => 400 ) );
		}

		$prepared_comment = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $prepared_comment ) ) {
			return $prepared_comment;
		}

		/**
		 * Do not allow a comment to be created with an empty string for
		 * comment_content.
		 * See `wp_handle_comment_submission()`.
		 */
		if ( '' === $prepared_comment['comment_content'] ) {
			return new WP_Error( 'rest_comment_content_invalid', __( 'Comment content is invalid.' ), array( 'status' => 400 ) );
		}

		// Setting remaining values before wp_insert_comment so we can
		// use wp_allow_comment().
		if ( ! isset( $prepared_comment['comment_date_gmt'] ) ) {
			$prepared_comment['comment_date_gmt'] = current_time( 'mysql', true );
		}

		// Set author data if the user's logged in
		$missing_author = empty( $prepared_comment['user_id'] )
			&& empty( $prepared_comment['comment_author'] )
			&& empty( $prepared_comment['comment_author_email'] )
			&& empty( $prepared_comment['comment_author_url'] );

		if ( is_user_logged_in() && $missing_author ) {
			$user = wp_get_current_user();
			$prepared_comment['user_id'] = $user->ID;
			$prepared_comment['comment_author'] = $user->display_name;
			$prepared_comment['comment_author_email'] = $user->user_email;
			$prepared_comment['comment_author_url'] = $user->user_url;
		}

		// Honor the discussion setting that requires a name and email address
		// of the comment author.
		if ( get_option( 'require_name_email' ) ) {
			if ( ! isset( $prepared_comment['comment_author'] ) && ! isset( $prepared_comment['comment_author_email'] ) ) {
				return new WP_Error( 'rest_comment_author_data_required', __( 'Creating a comment requires valid author name and email values.' ), array( 'status' => 400 ) );
			}
			if ( ! isset( $prepared_comment['comment_author'] ) ) {
				return new WP_Error( 'rest_comment_author_required', __( 'Creating a comment requires a valid author name.' ), array( 'status' => 400 ) );
			}
			if ( ! isset( $prepared_comment['comment_author_email'] ) ) {
				return new WP_Error( 'rest_comment_author_email_required', __( 'Creating a comment requires a valid author email.' ), array( 'status' => 400 ) );
			}
		}

		if ( ! isset( $prepared_comment['comment_author_email'] ) ) {
			$prepared_comment['comment_author_email'] = '';
		}
		if ( ! isset( $prepared_comment['comment_author_url'] ) ) {
			$prepared_comment['comment_author_url'] = '';
		}

		$prepared_comment['comment_agent'] = '';
		$prepared_comment['comment_approved'] = wp_allow_comment( $prepared_comment, true );

		if ( is_wp_error( $prepared_comment['comment_approved'] ) ) {
			$error_code = $prepared_comment['comment_approved']->get_error_code();
			$error_message = $prepared_comment['comment_approved']->get_error_message();

			if ( 'comment_duplicate' === $error_code ) {
				return new WP_Error( $error_code, $error_message, array( 'status' => 409 ) );
			}

			if ( 'comment_flood' === $error_code ) {
				return new WP_Error( $error_code, $error_message, array( 'status' => 400 ) );
			}

			return $prepared_comment['comment_approved'];
		}

		/**
		 * Filter a comment before it is inserted via the REST API.
		 *
		 * Allows modification of the comment right before it is inserted via `wp_insert_comment`.
		 *
		 * @param array           $prepared_comment The prepared comment data for `wp_insert_comment`.
		 * @param WP_REST_Request $request          Request used to insert the comment.
		 */
		$prepared_comment = apply_filters( 'rest_pre_insert_comment', $prepared_comment, $request );

		$comment_id = wp_insert_comment( $prepared_comment );
		if ( ! $comment_id ) {
			return new WP_Error( 'rest_comment_failed_create', __( 'Creating comment failed.' ), array( 'status' => 500 ) );
		}

		if ( isset( $request['status'] ) ) {
			$comment = get_comment( $comment_id );
			$this->handle_status_param( $request['status'], $comment );
		}

		$schema = $this->get_item_schema();
		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $comment_id );
			if ( is_wp_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$comment = get_comment( $comment_id );
		$fields_update = $this->update_additional_fields_for_object( $comment, $request );
		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$context = current_user_can( 'moderate_comments' ) ? 'edit' : 'view';
		$request->set_param( 'context', $context );
		$response = $this->prepare_item_for_response( $comment, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $comment_id ) ) );

		/**
		 * Fires after a comment is created or updated via the REST API.
		 *
		 * @param array           $comment  Comment as it exists in the database.
		 * @param WP_REST_Request $request  The request sent to the API.
		 * @param boolean         $creating True when creating a comment, false when updating.
		 */
		do_action( 'rest_insert_comment', $comment, $request, true );

		return $response;
	}

	/**
	 * Check if a given request has access to update a comment
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function update_item_permissions_check( $request ) {

		$id = (int) $request['id'];

		$comment = get_comment( $id );

		if ( $comment && ! $this->check_edit_permission( $comment ) ) {
			return new WP_Error( 'rest_cannot_edit', __( 'Sorry, you can not edit this comment.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Edit a comment
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_item( $request ) {
		$id = (int) $request['id'];

		$comment = get_comment( $id );
		if ( empty( $comment ) ) {
			return new WP_Error( 'rest_comment_invalid_id', __( 'Invalid comment id.' ), array( 'status' => 404 ) );
		}

		if ( isset( $request['type'] ) && get_comment_type( $id ) !== $request['type'] ) {
			return new WP_Error( 'rest_comment_invalid_type', __( 'Sorry, you cannot change the comment type.' ), array( 'status' => 404 ) );
		}

		$prepared_args = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $prepared_args ) ) {
			return $prepared_args;
		}

		if ( empty( $prepared_args ) && isset( $request['status'] ) ) {
			// Only the comment status is being changed.
			$change = $this->handle_status_param( $request['status'], $comment );
			if ( ! $change ) {
				return new WP_Error( 'rest_comment_failed_edit', __( 'Updating comment status failed.' ), array( 'status' => 500 ) );
			}
		} else {
			if ( is_wp_error( $prepared_args ) ) {
				return $prepared_args;
			}

			$prepared_args['comment_ID'] = $id;

			$updated = wp_update_comment( $prepared_args );
			if ( 0 === $updated ) {
				return new WP_Error( 'rest_comment_failed_edit', __( 'Updating comment failed.' ), array( 'status' => 500 ) );
			}

			if ( isset( $request['status'] ) ) {
				$this->handle_status_param( $request['status'], $comment );
			}
		}

		$schema = $this->get_item_schema();
		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $id );
			if ( is_wp_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$comment = get_comment( $id );
		$fields_update = $this->update_additional_fields_for_object( $comment, $request );
		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $comment, $request );

		/* This action is documented in lib/endpoints/class-wp-rest-comments-controller.php */
		do_action( 'rest_insert_comment', $comment, $request, false );

		return rest_ensure_response( $response );
	}

	/**
	 * Check if a given request has access to delete a comment
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function delete_item_permissions_check( $request ) {
		$id = (int) $request['id'];
		$comment = get_comment( $id );
		if ( ! $comment ) {
			return new WP_Error( 'rest_comment_invalid_id', __( 'Invalid comment id.' ), array( 'status' => 404 ) );
		}
		if ( ! $this->check_edit_permission( $comment ) ) {
			return new WP_Error( 'rest_cannot_delete', __( 'Sorry, you can not delete this comment.' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Delete a comment.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {
		$id = (int) $request['id'];
		$force = isset( $request['force'] ) ? (bool) $request['force'] : false;

		$comment = get_comment( $id );
		if ( empty( $comment ) ) {
			return new WP_Error( 'rest_comment_invalid_id', __( 'Invalid comment id.' ), array( 'status' => 404 ) );
		}

		/**
		 * Filter whether a comment is trashable.
		 *
		 * Return false to disable trash support for the post.
		 *
		 * @param boolean $supports_trash Whether the post type support trashing.
		 * @param WP_Post $comment        The comment object being considered for trashing support.
		 */
		$supports_trash = apply_filters( 'rest_comment_trashable', ( EMPTY_TRASH_DAYS > 0 ), $comment );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $comment, $request );

		if ( $force ) {
			$result = wp_delete_comment( $comment->comment_ID, true );
		} else {
			// If we don't support trashing for this type, error out
			if ( ! $supports_trash ) {
				return new WP_Error( 'rest_trash_not_supported', __( 'The comment does not support trashing.' ), array( 'status' => 501 ) );
			}

			if ( 'trash' === $comment->comment_approved ) {
				return new WP_Error( 'rest_already_trashed', __( 'The comment has already been trashed.' ), array( 'status' => 410 ) );
			}

			$result = wp_trash_comment( $comment->comment_ID );
		}

		if ( ! $result ) {
			return new WP_Error( 'rest_cannot_delete', __( 'The comment cannot be deleted.' ), array( 'status' => 500 ) );
		}

		/**
		 * Fires after a comment is deleted via the REST API.
		 *
		 * @param object           $comment  The deleted comment data.
		 * @param WP_REST_Response $response The response returned from the API.
		 * @param WP_REST_Request  $request  The request sent to the API.
		 */
		do_action( 'rest_delete_comment', $comment, $response, $request );

		return $response;
	}

	/**
	 * Prepare a single comment output for response.
	 *
	 * @param  object          $comment Comment object.
	 * @param  WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response
	 */
	public function prepare_item_for_response( $comment, $request ) {
		$data = array(
			'id'                 => (int) $comment->comment_ID,
			'post'               => (int) $comment->comment_post_ID,
			'parent'             => (int) $comment->comment_parent,
			'author'             => (int) $comment->user_id,
			'author_name'        => $comment->comment_author,
			'author_email'       => $comment->comment_author_email,
			'author_url'         => $comment->comment_author_url,
			'author_ip'          => $comment->comment_author_IP,
			'author_user_agent'  => $comment->comment_agent,
			'date'               => mysql_to_rfc3339( $comment->comment_date ),
			'date_gmt'           => mysql_to_rfc3339( $comment->comment_date_gmt ),
			'content'            => array(
				'rendered' => apply_filters( 'comment_text', $comment->comment_content, $comment ),
				'raw'      => $comment->comment_content,
			),
			'karma'              => (int) $comment->comment_karma,
			'link'               => get_comment_link( $comment ),
			'status'             => $this->prepare_status_response( $comment->comment_approved ),
			'type'               => get_comment_type( $comment->comment_ID ),
		);

		$schema = $this->get_item_schema();

		if ( ! empty( $schema['properties']['author_avatar_urls'] ) ) {
			$data['author_avatar_urls'] = rest_get_avatar_urls( $comment->comment_author_email );
		}

		if ( ! empty( $schema['properties']['meta'] ) ) {
			$data['meta'] = $this->meta->get_value( $comment->comment_ID, $request );
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data = $this->add_additional_fields_to_object( $data, $request );
		$data = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $comment ) );

		/**
		 * Filter a comment returned from the API.
		 *
		 * Allows modification of the comment right before it is returned.
		 *
		 * @param WP_REST_Response  $response   The response object.
		 * @param object            $comment    The original comment object.
		 * @param WP_REST_Request   $request    Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_comment', $response, $comment, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param object $comment Comment object.
	 * @return array Links for the given comment.
	 */
	protected function prepare_links( $comment ) {
		$links = array(
			'self' => array(
				'href' => rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $comment->comment_ID ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);

		if ( 0 !== (int) $comment->user_id ) {
			$links['author'] = array(
				'href'       => rest_url( 'wp/v2/users/' . $comment->user_id ),
				'embeddable' => true,
			);
		}

		if ( 0 !== (int) $comment->comment_post_ID ) {
			$post = $this->get_post( $comment->comment_post_ID );
			if ( ! empty( $post->ID ) ) {
				$obj = get_post_type_object( $post->post_type );
				$base = ! empty( $obj->rest_base ) ? $obj->rest_base : $obj->name;

				$links['up'] = array(
					'href'       => rest_url( 'wp/v2/' . $base . '/' . $comment->comment_post_ID ),
					'embeddable' => true,
					'post_type'  => $post->post_type,
				);
			}
		}

		if ( 0 !== (int) $comment->comment_parent ) {
			$links['in-reply-to'] = array(
				'href'       => rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $comment->comment_parent ) ),
				'embeddable' => true,
			);
		}

		// Only grab one comment to verify the comment has children.
		$comment_children = $comment->get_children( array( 'number' => 1, 'count' => true ) );
		if ( ! empty( $comment_children ) ) {
			$args = array( 'parent' => $comment->comment_ID );
			$rest_url = add_query_arg( $args, rest_url( $this->namespace . '/' . $this->rest_base ) );

			$links['children'] = array(
				'href' => $rest_url,
			);
		}

		return $links;
	}

	/**
	 * Prepend internal property prefix to query parameters to match our response fields.
	 *
	 * @param  string $query_param
	 * @return string $normalized
	 */
	protected function normalize_query_param( $query_param ) {
		$prefix = 'comment_';

		switch ( $query_param ) {
			case 'id':
				$normalized = $prefix . 'ID';
				break;
			case 'post':
				$normalized = $prefix . 'post_ID';
				break;
			case 'parent':
				$normalized = $prefix . 'parent';
				break;
			case 'include':
				$normalized = 'comment__in';
				break;
			default:
				$normalized = $prefix . $query_param;
				break;
		}

		return $normalized;
	}

	/**
	 * Check comment_approved to set comment status for single comment output.
	 *
	 * @param  string|int $comment_approved
	 * @return string     $status
	 */
	protected function prepare_status_response( $comment_approved ) {

		switch ( $comment_approved ) {
			case 'hold':
			case '0':
				$status = 'hold';
				break;

			case 'approve':
			case '1':
				$status = 'approved';
				break;

			case 'spam':
			case 'trash':
			default:
				$status = $comment_approved;
				break;
		}

		return $status;
	}

	/**
	 * Prepare a single comment to be inserted into the database.
	 *
	 * @param  WP_REST_Request $request Request object.
	 * @return array|WP_Error  $prepared_comment
	 */
	protected function prepare_item_for_database( $request ) {
		$prepared_comment = array();

		/**
		 * Allow the comment_content to be set via the 'content' or
		 * the 'content.raw' properties of the Request object.
		 */
		if ( isset( $request['content'] ) && is_string( $request['content'] ) ) {
			$prepared_comment['comment_content'] = wp_filter_kses( $request['content'] );
		} elseif ( isset( $request['content']['raw'] ) && is_string( $request['content']['raw'] ) ) {
			$prepared_comment['comment_content'] = wp_filter_kses( $request['content']['raw'] );
		}

		if ( isset( $request['post'] ) ) {
			$prepared_comment['comment_post_ID'] = (int) $request['post'];
		}

		if ( isset( $request['parent'] ) ) {
			$prepared_comment['comment_parent'] = $request['parent'];
		}

		if ( isset( $request['author'] ) ) {
			$user = new WP_User( $request['author'] );
			if ( $user->exists() ) {
				$prepared_comment['user_id'] = $user->ID;
				$prepared_comment['comment_author'] = $user->display_name;
				$prepared_comment['comment_author_email'] = $user->user_email;
				$prepared_comment['comment_author_url'] = $user->user_url;
			} else {
				return new WP_Error( 'rest_comment_author_invalid', __( 'Invalid comment author id.' ), array( 'status' => 400 ) );
			}
		}

		if ( isset( $request['author_name'] ) ) {
			$prepared_comment['comment_author'] = $request['author_name'];
		}

		if ( isset( $request['author_email'] ) ) {
			$prepared_comment['comment_author_email'] = $request['author_email'];
		}

		if ( isset( $request['author_url'] ) ) {
			$prepared_comment['comment_author_url'] = $request['author_url'];
		}

		if ( isset( $request['author_ip'] ) ) {
			$prepared_comment['comment_author_IP'] = $request['author_ip'];
		}

		if ( isset( $request['type'] ) ) {
			// Comment type "comment" needs to be created as an empty string.
			$prepared_comment['comment_type'] = 'comment' === $request['type'] ? '' : $request['type'];
		}

		if ( isset( $request['karma'] ) ) {
			$prepared_comment['comment_karma'] = $request['karma'] ;
		}

		if ( ! empty( $request['date'] ) ) {
			$date_data = rest_get_date_with_gmt( $request['date'] );

			if ( ! empty( $date_data ) ) {
				list( $prepared_comment['comment_date'], $prepared_comment['comment_date_gmt'] ) = $date_data;
			}
		} elseif ( ! empty( $request['date_gmt'] ) ) {
			$date_data = rest_get_date_with_gmt( $request['date_gmt'], true );

			if ( ! empty( $date_data ) ) {
				list( $prepared_comment['comment_date'], $prepared_comment['comment_date_gmt'] ) = $date_data;
			}
		}

		// Require 'comment_content' unless only the 'comment_status' is being
		// updated.
		if ( ! empty( $prepared_comment ) && ! isset( $prepared_comment['comment_content'] ) ) {
			return new WP_Error( 'rest_comment_content_required', __( 'Missing comment content.' ), array( 'status' => 400 ) );
		}

		return apply_filters( 'rest_preprocess_comment', $prepared_comment, $request );
	}

	/**
	 * Get the Comment's schema, conforming to JSON Schema
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'              => 'http://json-schema.org/draft-04/schema#',
			'title'                => 'comment',
			'type'                 => 'object',
			'properties'           => array(
				'id'               => array(
					'description'  => __( 'Unique identifier for the object.' ),
					'type'         => 'integer',
					'context'      => array( 'view', 'edit', 'embed' ),
					'readonly'     => true,
				),
				'author'           => array(
					'description'  => __( 'The id of the user object, if author was a user.' ),
					'type'         => 'integer',
					'context'      => array( 'view', 'edit', 'embed' ),
				),
				'author_email'     => array(
					'description'  => __( 'Email address for the object author.' ),
					'type'         => 'string',
					'format'       => 'email',
					'context'      => array( 'edit' ),
				),
				'author_ip'     => array(
					'description'  => __( 'IP address for the object author.' ),
					'type'         => 'string',
					'format'       => 'ipv4',
					'context'      => array( 'edit' ),
					'arg_options'  => array(
						'default'           => '127.0.0.1',
					),
				),
				'author_name'     => array(
					'description'  => __( 'Display name for the object author.' ),
					'type'         => 'string',
					'context'      => array( 'view', 'edit', 'embed' ),
					'arg_options'  => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'author_url'       => array(
					'description'  => __( 'URL for the object author.' ),
					'type'         => 'string',
					'format'       => 'uri',
					'context'      => array( 'view', 'edit', 'embed' ),
				),
				'author_user_agent'     => array(
					'description'  => __( 'User agent for the object author.' ),
					'type'         => 'string',
					'context'      => array( 'edit' ),
					'readonly'     => true,
				),
				'content'          => array(
					'description'     => __( 'The content for the object.' ),
					'type'            => 'object',
					'context'         => array( 'view', 'edit', 'embed' ),
					'properties'      => array(
						'raw'         => array(
							'description'     => __( 'Content for the object, as it exists in the database.' ),
							'type'            => 'string',
							'context'         => array( 'edit' ),
						),
						'rendered'    => array(
							'description'     => __( 'HTML content for the object, transformed for display.' ),
							'type'            => 'string',
							'context'         => array( 'view', 'edit', 'embed' ),
						),
					),
				),
				'date'             => array(
					'description'  => __( 'The date the object was published.' ),
					'type'         => 'string',
					'format'       => 'date-time',
					'context'      => array( 'view', 'edit', 'embed' ),
				),
				'date_gmt'         => array(
					'description'  => __( 'The date the object was published as GMT.' ),
					'type'         => 'string',
					'format'       => 'date-time',
					'context'      => array( 'view', 'edit' ),
				),
				'karma'             => array(
					'description'  => __( 'Karma for the object.' ),
					'type'         => 'integer',
					'context'      => array( 'edit' ),
				),
				'link'             => array(
					'description'  => __( 'URL to the object.' ),
					'type'         => 'string',
					'format'       => 'uri',
					'context'      => array( 'view', 'edit', 'embed' ),
					'readonly'     => true,
				),
				'parent'           => array(
					'description'  => __( 'The id for the parent of the object.' ),
					'type'         => 'integer',
					'context'      => array( 'view', 'edit', 'embed' ),
					'arg_options'  => array(
						'default'           => 0,
					),
				),
				'post'             => array(
					'description'  => __( 'The id of the associated post object.' ),
					'type'         => 'integer',
					'context'      => array( 'view', 'edit' ),
					'arg_options'  => array(
						'default'           => 0,
					),
				),
				'status'           => array(
					'description'  => __( 'State of the object.' ),
					'type'         => 'string',
					'context'      => array( 'view', 'edit' ),
					'arg_options'  => array(
						'sanitize_callback' => 'sanitize_key',
					),
				),
				'type'             => array(
					'description'  => __( 'Type of Comment for the object.' ),
					'type'         => 'string',
					'context'      => array( 'view', 'edit', 'embed' ),
					'default'      => 'comment',
					'arg_options'  => array(
						'sanitize_callback' => 'sanitize_key',
					),
				),
			),
		);

		if ( get_option( 'show_avatars' ) ) {
			$avatar_properties = array();

			$avatar_sizes = rest_get_avatar_sizes();
			foreach ( $avatar_sizes as $size ) {
				$avatar_properties[ $size ] = array(
					'description' => sprintf( __( 'Avatar URL with image size of %d pixels.' ), $size ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'embed', 'view', 'edit' ),
				);
			}

			$schema['properties']['author_avatar_urls'] = array(
				'description'   => __( 'Avatar URLs for the object author.' ),
				'type'          => 'object',
				'context'       => array( 'view', 'edit', 'embed' ),
				'readonly'      => true,
				'properties'    => $avatar_properties,
			);
		}

		$schema['properties']['meta'] = $this->meta->get_field_schema();

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();

		$query_params['context']['default'] = 'view';

		$query_params['after'] = array(
			'description'       => __( 'Limit response to resources published after a given ISO8601 compliant date.' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$query_params['author'] = array(
			'description'       => __( 'Limit result set to comments assigned to specific user ids. Requires authorization.' ),
			'sanitize_callback' => 'wp_parse_id_list',
			'type'              => 'array',
		);
		$query_params['author_exclude'] = array(
			'description'       => __( 'Ensure result set excludes comments assigned to specific user ids. Requires authorization.' ),
			'sanitize_callback' => 'wp_parse_id_list',
			'type'              => 'array',
		);
		$query_params['author_email'] = array(
			'default'           => null,
			'description'       => __( 'Limit result set to that from a specific author email. Requires authorization.' ),
			'format'            => 'email',
			'sanitize_callback' => 'sanitize_email',
			'type'              => 'string',
		);
		$query_params['before'] = array(
			'description'       => __( 'Limit response to resources published before a given ISO8601 compliant date.' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$query_params['exclude'] = array(
			'description'        => __( 'Ensure result set excludes specific ids.' ),
			'type'               => 'array',
			'default'            => array(),
			'sanitize_callback'  => 'wp_parse_id_list',
		);
		$query_params['include'] = array(
			'description'        => __( 'Limit result set to specific ids.' ),
			'type'               => 'array',
			'default'            => array(),
			'sanitize_callback'  => 'wp_parse_id_list',
		);
		$query_params['karma'] = array(
			'default'           => null,
			'description'       => __( 'Limit result set to that of a particular comment karma. Requires authorization.' ),
			'sanitize_callback' => 'absint',
			'type'              => 'integer',
			'validate_callback'  => 'rest_validate_request_arg',
		);
		$query_params['offset'] = array(
			'description'        => __( 'Offset the result set by a specific number of comments.' ),
			'type'               => 'integer',
			'sanitize_callback'  => 'absint',
			'validate_callback'  => 'rest_validate_request_arg',
		);
		$query_params['order']      = array(
			'description'           => __( 'Order sort attribute ascending or descending.' ),
			'type'                  => 'string',
			'sanitize_callback'     => 'sanitize_key',
			'validate_callback'     => 'rest_validate_request_arg',
			'default'               => 'desc',
			'enum'                  => array(
				'asc',
				'desc',
			),
		);
		$query_params['orderby']    = array(
			'description'           => __( 'Sort collection by object attribute.' ),
			'type'                  => 'string',
			'sanitize_callback'     => 'sanitize_key',
			'validate_callback'     => 'rest_validate_request_arg',
			'default'               => 'date_gmt',
			'enum'                  => array(
				'date',
				'date_gmt',
				'id',
				'include',
				'post',
				'parent',
				'type',
			),
		);
		$query_params['parent'] = array(
			'default'           => array(),
			'description'       => __( 'Limit result set to resources of specific parent ids.' ),
			'sanitize_callback' => 'wp_parse_id_list',
			'type'              => 'array',
		);
		$query_params['parent_exclude'] = array(
			'default'           => array(),
			'description'       => __( 'Ensure result set excludes specific parent ids.' ),
			'sanitize_callback' => 'wp_parse_id_list',
			'type'              => 'array',
		);
		$query_params['post']   = array(
			'default'           => array(),
			'description'       => __( 'Limit result set to resources assigned to specific post ids.' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$query_params['status'] = array(
			'default'           => 'approve',
			'description'       => __( 'Limit result set to comments assigned a specific status. Requires authorization.' ),
			'sanitize_callback' => 'sanitize_key',
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$query_params['type'] = array(
			'default'           => 'comment',
			'description'       => __( 'Limit result set to comments assigned a specific type. Requires authorization.' ),
			'sanitize_callback' => 'sanitize_key',
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		return $query_params;
	}

	/**
	 * Set the comment_status of a given comment object when creating or updating a comment.
	 *
	 * @param string|int $new_status
	 * @param object     $comment
	 * @return boolean   $changed
	 */
	protected function handle_status_param( $new_status, $comment ) {
		$old_status = wp_get_comment_status( $comment->comment_ID );

		if ( $new_status === $old_status ) {
			return false;
		}

		switch ( $new_status ) {
			case 'approved' :
			case 'approve':
			case '1':
				$changed = wp_set_comment_status( $comment->comment_ID, 'approve' );
				break;
			case 'hold':
			case '0':
				$changed = wp_set_comment_status( $comment->comment_ID, 'hold' );
				break;
			case 'spam' :
				$changed = wp_spam_comment( $comment->comment_ID );
				break;
			case 'unspam' :
				$changed = wp_unspam_comment( $comment->comment_ID );
				break;
			case 'trash' :
				$changed = wp_trash_comment( $comment->comment_ID );
				break;
			case 'untrash' :
				$changed = wp_untrash_comment( $comment->comment_ID );
				break;
			default :
				$changed = false;
				break;
		}

		return $changed;
	}

	/**
	 * Check if we can read a post.
	 *
	 * Correctly handles posts with the inherit status.
	 *
	 * @param  WP_Post $post Post Object.
	 * @return boolean Can we read it?
	 */
	protected function check_read_post_permission( $post ) {
		$posts_controller = new WP_REST_Posts_Controller( $post->post_type );

		return $posts_controller->check_read_permission( $post );
	}

	/**
	 * Check if we can read a comment.
	 *
	 * @param  object  $comment Comment object.
	 * @return boolean Can we read it?
	 */
	protected function check_read_permission( $comment ) {
		if ( ! empty( $comment->comment_post_ID ) ) {
			$post = get_post( $comment->comment_post_ID );
			if ( $post ) {
				if ( $this->check_read_post_permission( $post ) && 1 === (int) $comment->comment_approved ) {
					return true;
				}
			}
		}

		if ( 0 === get_current_user_id() ) {
			return false;
		}

		if ( empty( $comment->comment_post_ID ) && ! current_user_can( 'moderate_comments' ) ) {
			return false;
		}

		if ( ! empty( $comment->user_id ) && get_current_user_id() === (int) $comment->user_id ) {
			return true;
		}

		return current_user_can( 'edit_comment', $comment->comment_ID );
	}

	/**
	 * Check if we can edit or delete a comment.
	 *
	 * @param  object  $comment Comment object.
	 * @return boolean Can we edit or delete it?
	 */
	protected function check_edit_permission( $comment ) {
		if ( 0 === (int) get_current_user_id() ) {
			return false;
		}

		if ( ! current_user_can( 'moderate_comments' ) ) {
			return false;
		}

		return current_user_can( 'edit_comment', $comment->comment_ID );
	}
}
