<?php
/**
 * A custom REST server for Gutenberg.
 *
 * @package gutenberg
 * @since   6.9.0
 */

// Create a new class that extends WP_REST_Comments_Controller
class Gutenberg_REST_Comment_Controller_6_9 extends WP_REST_Comments_Controller {

	public function get_items_permissions_check( $request ) {
		$is_note         = 'note' === $request['type'];
		$is_edit_context = 'edit' === $request['context'];

		if ( ! empty( $request['post'] ) ) {
			foreach ( (array) $request['post'] as $post_id ) {
				$post = get_post( $post_id );

				// Note: This is only relevant change for the backport.
				if ( $post && $is_note && ! $this->check_post_type_supports_notes( $post->post_type ) ) {
					return new WP_Error(
						'rest_comment_not_supported_post_type',
						__( 'Sorry, this post type does not support notes.', 'gutenberg' ),
						array( 'status' => 403 )
					);
				}

				if ( ! empty( $post_id ) && $post && ! $this->check_read_post_permission( $post, $request ) ) {
					return new WP_Error(
						'rest_cannot_read_post',
						__( 'Sorry, you are not allowed to read the post for this comment.', 'gutenberg' ),
						array( 'status' => rest_authorization_required_code() )
					);
				} elseif ( 0 === $post_id && ! current_user_can( 'moderate_comments' ) ) {
					return new WP_Error(
						'rest_cannot_read',
						__( 'Sorry, you are not allowed to read comments without a post.', 'gutenberg' ),
						array( 'status' => rest_authorization_required_code() )
					);
				}
			}
		}

		// Re-map edit context capabilities when requesting `note` for a post.
		// Note: This is only relevant change for the backport.
		if ( $is_edit_context && $is_note && ! empty( $request['post'] ) ) {
			foreach ( (array) $request['post'] as $post_id ) {
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return new WP_Error(
						'rest_forbidden_context',
						__( 'Sorry, you are not allowed to edit comments.', 'gutenberg' ),
						array( 'status' => rest_authorization_required_code() )
					);
				}
			}
		} elseif ( $is_edit_context && ! current_user_can( 'moderate_comments' ) ) {
			return new WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to edit comments.', 'gutenberg' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			$protected_params = array( 'author', 'author_exclude', 'author_email', 'type', 'status' );
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
				return new WP_Error(
					'rest_forbidden_param',
					/* translators: %s: List of forbidden parameters. */
					sprintf( __( 'Query parameter not permitted: %s', 'gutenberg' ), implode( ', ', $forbidden_params ) ),
					array( 'status' => rest_authorization_required_code() )
				);
			}
		}

		return true;
	}

	public function get_item_permissions_check( $request ) {
		$comment = $this->get_comment( $request['id'] );
		if ( is_wp_error( $comment ) ) {
			return $comment;
		}

		// Re-map edit context capabilities when requesting `note` type.
		// Note: This is only relevant change for the backport.
		$edit_cap = 'note' === $comment->comment_type ? array( 'edit_comment', $comment->comment_ID ) : array( 'moderate_comments' );
		if ( ! empty( $request['context'] ) && 'edit' === $request['context'] && ! current_user_can( ...$edit_cap ) ) {
			return new WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to edit comments.', 'gutenberg' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$post = get_post( $comment->comment_post_ID );

		if ( ! $this->check_read_permission( $comment, $request ) ) {
			return new WP_Error(
				'rest_cannot_read',
				__( 'Sorry, you are not allowed to read this comment.', 'gutenberg' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( $post && ! $this->check_read_post_permission( $post, $request ) ) {
			return new WP_Error(
				'rest_cannot_read_post',
				__( 'Sorry, you are not allowed to read the post for this comment.', 'gutenberg' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	public function create_item_permissions_check( $request ) {
		$is_note = ! empty( $request['type'] ) && 'note' === $request['type'];

		// Note: This is only relevant change for the backport.
		if ( ! is_user_logged_in() && $is_note ) {
			return new WP_Error(
				'rest_comment_login_required',
				__( 'Sorry, you must be logged in to comment.', 'gutenberg' ),
				array( 'status' => 401 )
			);
		}

		if ( ! is_user_logged_in() ) {
			if ( get_option( 'comment_registration' ) ) {
				return new WP_Error(
					'rest_comment_login_required',
					__( 'Sorry, you must be logged in to comment.', 'gutenberg' ),
					array( 'status' => 401 )
				);
			}

			/**
			 * Filters whether comments can be created via the REST API without authentication.
			 *
			 * Enables creating comments for anonymous users.
			 *
			 * @since 4.7.0
			 *
			 * @param bool $allow_anonymous Whether to allow anonymous comments to
			 *                              be created. Default `false`.
			 * @param WP_REST_Request $request Request used to generate the
			 *                                 response.
			 */
			$allow_anonymous = apply_filters( 'rest_allow_anonymous_comments', false, $request );

			if ( ! $allow_anonymous ) {
				return new WP_Error(
					'rest_comment_login_required',
					__( 'Sorry, you must be logged in to comment.', 'gutenberg' ),
					array( 'status' => 401 )
				);
			}
		}

		// Limit who can set comment `author`, `author_ip` or `status` to anything other than the default.
		if ( isset( $request['author'] ) && get_current_user_id() !== $request['author'] && ! current_user_can( 'moderate_comments' ) ) {
			return new WP_Error(
				'rest_comment_invalid_author',
				/* translators: %s: Request parameter. */
				sprintf( __( "Sorry, you are not allowed to edit '%s' for comments.", 'gutenberg' ), 'author' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( isset( $request['author_ip'] ) && ! current_user_can( 'moderate_comments' ) ) {
			if ( empty( $_SERVER['REMOTE_ADDR'] ) || $request['author_ip'] !== $_SERVER['REMOTE_ADDR'] ) {
				return new WP_Error(
					'rest_comment_invalid_author_ip',
					/* translators: %s: Request parameter. */
					sprintf( __( "Sorry, you are not allowed to edit '%s' for comments.", 'gutenberg' ), 'author_ip' ),
					array( 'status' => rest_authorization_required_code() )
				);
			}
		}

		// Note: This is only relevant change for the backport.
		$edit_cap = $is_note ? array( 'edit_post', (int) $request['post'] ) : array( 'moderate_comments' );
		if ( isset( $request['status'] ) && ! current_user_can( ...$edit_cap ) ) {
			return new WP_Error(
				'rest_comment_invalid_status',
				/* translators: %s: Request parameter. */
				sprintf( __( "Sorry, you are not allowed to edit '%s' for comments.", 'gutenberg' ), 'status' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( empty( $request['post'] ) ) {
			return new WP_Error(
				'rest_comment_invalid_post_id',
				__( 'Sorry, you are not allowed to create this comment without a post.', 'gutenberg' ),
				array( 'status' => 403 )
			);
		}

		$post = get_post( (int) $request['post'] );

		if ( ! $post ) {
			return new WP_Error(
				'rest_comment_invalid_post_id',
				__( 'Sorry, you are not allowed to create this comment without a post.', 'gutenberg' ),
				array( 'status' => 403 )
			);
		}

		// Note: This is only relevant change for the backport.
		if ( $is_note && ! $this->check_post_type_supports_notes( $post->post_type ) ) {
			return new WP_Error(
				'rest_comment_not_supported_post_type',
				__( 'Sorry, this post type does not support notes.', 'gutenberg' ),
				array( 'status' => 403 )
			);
		}

		// Note: This is only relevant change for the backport.
		if ( 'draft' === $post->post_status && ! $is_note ) {
			return new WP_Error(
				'rest_comment_draft_post',
				__( 'Sorry, you are not allowed to create a comment on this post.', 'gutenberg' ),
				array( 'status' => 403 )
			);
		}

		if ( 'trash' === $post->post_status ) {
			return new WP_Error(
				'rest_comment_trash_post',
				__( 'Sorry, you are not allowed to create a comment on this post.', 'gutenberg' ),
				array( 'status' => 403 )
			);
		}

		if ( ! $this->check_read_post_permission( $post, $request ) ) {
			return new WP_Error(
				'rest_cannot_read_post',
				__( 'Sorry, you are not allowed to read the post for this comment.', 'gutenberg' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		// Note: This is only relevant change for the backport.
		if ( ! comments_open( $post->ID ) && ! $is_note ) {
			return new WP_Error(
				'rest_comment_closed',
				__( 'Sorry, comments are closed for this item.', 'gutenberg' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Creates a comment.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or error object on failure.
	 */
	public function create_item( $request ) {
		// This code is copied exactly from (core file name) except for sectioned marked with the comment.
		// '// Note: This is only relevant change for the backport.'
		if ( ! empty( $request['id'] ) ) {
			return new WP_Error(
				'rest_comment_exists',
				__( 'Cannot create existing comment.', 'gutenberg' ),
				array( 'status' => 400 )
			);
		}

		// Note: Removes non-default comment type check for the backport.
		// Do not allow comments to be created with a non-core type.
		if ( ! empty( $request['type'] ) && ! in_array( $request['type'], array( 'comment', 'note' ), true ) ) {
			return new WP_Error(
				'rest_invalid_comment_type',
				__( 'Cannot create a comment with that type.', 'gutenberg' ),
				array( 'status' => 400 )
			);
		}

		$prepared_comment = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $prepared_comment ) ) {
			return $prepared_comment;
		}

		$prepared_comment['comment_type'] = $request['type'];

		if ( ! isset( $prepared_comment['comment_content'] ) ) {
			$prepared_comment['comment_content'] = '';
		}

		// Include note metadata into check_is_comment_content_allowed [backport].
		if ( isset( $request['meta']['_wp_note_status'] ) ) {
			$prepared_comment['meta']['_wp_note_status'] = $request['meta']['_wp_note_status'];
		}

		if ( ! $this->check_is_comment_content_allowed( $prepared_comment ) ) {
			return new WP_Error(
				'rest_comment_content_invalid',
				__( 'Invalid comment content.', 'gutenberg' ),
				array( 'status' => 400 )
			);
		}

		// Setting remaining values before wp_insert_comment so we can use wp_allow_comment().
		if ( ! isset( $prepared_comment['comment_date_gmt'] ) ) {
			$prepared_comment['comment_date_gmt'] = current_time( 'mysql', true );
		}

		// Set author data if the user's logged in.
		$missing_author = empty( $prepared_comment['user_id'] )
			&& empty( $prepared_comment['comment_author'] )
			&& empty( $prepared_comment['comment_author_email'] )
			&& empty( $prepared_comment['comment_author_url'] );

		if ( is_user_logged_in() && $missing_author ) {
			$user = wp_get_current_user();

			$prepared_comment['user_id']              = $user->ID;
			$prepared_comment['comment_author']       = $user->display_name;
			$prepared_comment['comment_author_email'] = $user->user_email;
			$prepared_comment['comment_author_url']   = $user->user_url;
		}

		// Honor the discussion setting that requires a name and email address of the comment author.
		if ( get_option( 'require_name_email' ) ) {
			if ( empty( $prepared_comment['comment_author'] ) || empty( $prepared_comment['comment_author_email'] ) ) {
				return new WP_Error(
					'rest_comment_author_data_required',
					__( 'Creating a comment requires valid author name and email values.', 'gutenberg' ),
					array( 'status' => 400 )
				);
			}
		}

		if ( ! isset( $prepared_comment['comment_author_email'] ) ) {
			$prepared_comment['comment_author_email'] = '';
		}

		if ( ! isset( $prepared_comment['comment_author_url'] ) ) {
			$prepared_comment['comment_author_url'] = '';
		}

		if ( ! isset( $prepared_comment['comment_agent'] ) ) {
			$prepared_comment['comment_agent'] = '';
		}

		$check_comment_lengths = wp_check_comment_data_max_lengths( $prepared_comment );

		if ( is_wp_error( $check_comment_lengths ) ) {
			$error_code = $check_comment_lengths->get_error_code();
			return new WP_Error(
				$error_code,
				__( 'Comment field exceeds maximum length allowed.', 'gutenberg' ),
				array( 'status' => 400 )
			);
		}

		$prepared_comment['comment_approved'] = wp_allow_comment( $prepared_comment, true );

		if ( is_wp_error( $prepared_comment['comment_approved'] ) ) {
			$error_code    = $prepared_comment['comment_approved']->get_error_code();
			$error_message = $prepared_comment['comment_approved']->get_error_message();

			if ( 'comment_duplicate' === $error_code ) {
				return new WP_Error(
					$error_code,
					$error_message,
					array( 'status' => 409 )
				);
			}

			if ( 'comment_flood' === $error_code ) {
				return new WP_Error(
					$error_code,
					$error_message,
					array( 'status' => 400 )
				);
			}

			return $prepared_comment['comment_approved'];
		}

		/**
		 * Filters a comment before it is inserted via the REST API.
		 *
		 * Allows modification of the comment right before it is inserted via wp_insert_comment().
		 * Returning a WP_Error value from the filter will short-circuit insertion and allow
		 * skipping further processing.
		 *
		 * @since 4.7.0
		 * @since 4.8.0 `$prepared_comment` can now be a WP_Error to short-circuit insertion.
		 *
		 * @param array|WP_Error  $prepared_comment The prepared comment data for wp_insert_comment().
		 * @param WP_REST_Request $request          Request used to insert the comment.
		 */
		$prepared_comment = apply_filters( 'rest_pre_insert_comment', $prepared_comment, $request );
		if ( is_wp_error( $prepared_comment ) ) {
			return $prepared_comment;
		}

		$comment_id = wp_insert_comment( wp_filter_comment( wp_slash( (array) $prepared_comment ) ) );

		if ( ! $comment_id ) {
			return new WP_Error(
				'rest_comment_failed_create',
				__( 'Creating comment failed.', 'gutenberg' ),
				array( 'status' => 500 )
			);
		}

		if ( isset( $request['status'] ) ) {
			$this->handle_status_param( $request['status'], $comment_id );
		}

		$comment = get_comment( $comment_id );

		/**
		 * Fires after a comment is created or updated via the REST API.
		 *
		 * @since 4.7.0
		 *
		 * @param WP_Comment      $comment  Inserted or updated comment object.
		 * @param WP_REST_Request $request  Request object.
		 * @param bool            $creating True when creating a comment, false
		 *                                  when updating.
		 */
		do_action( 'rest_insert_comment', $comment, $request, true );

		$schema = $this->get_item_schema();

		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $comment_id );

			if ( is_wp_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$fields_update = $this->update_additional_fields_for_object( $comment, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$context = current_user_can( 'moderate_comments' ) ? 'edit' : 'view';
		$request->set_param( 'context', $context );

		/**
		 * Fires completely after a comment is created or updated via the REST API.
		 *
		 * @since 5.0.0
		 *
		 * @param WP_Comment      $comment  Inserted or updated comment object.
		 * @param WP_REST_Request $request  Request object.
		 * @param bool            $creating True when creating a comment, false
		 *                                  when updating.
		 */
		do_action( 'rest_after_insert_comment', $comment, $request, true );

		$response = $this->prepare_item_for_response( $comment, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $comment_id ) ) );

		return $response;
	}

	/**
	 * Check if post type supports block comments.
	 *
	 * @param string $post_type Post type name.
	 * @return bool True if post type supports block comments, false otherwise.
	 */
	private function check_post_type_supports_notes( $post_type ) {
		$supports = get_all_post_type_supports( $post_type );
		if ( ! isset( $supports['editor'] ) ) {
			return false;
		}
		if ( ! is_array( $supports['editor'] ) ) {
			return false;
		}
		foreach ( $supports['editor'] as $item ) {
			if ( ! empty( $item['notes'] ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Prepares links for the request.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_Comment $comment Comment object.
	 * @return array Links for the given comment.
	 */
	protected function prepare_links( $comment ) {
		$links = parent::prepare_links( $comment );

		// Embedding children for notes requires `type` and `status` inheritance.
		// Note: This is only relevant change for the backport.
		if ( isset( $links['children'] ) && 'note' === $comment->comment_type ) {
			$args = array(
				'parent' => $comment->comment_ID,
				'type'   => $comment->comment_type,
				'status' => 'all',
			);

			$rest_url = add_query_arg( $args, rest_url( $this->namespace . '/' . $this->rest_base ) );

			$links['children'] = array(
				'href'       => $rest_url,
				'embeddable' => true,
			);
		}

		return $links;
	}

	/**
	 * Override the schema to change `type` property.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema                       = parent::get_item_schema();
		$schema['properties']['type'] = array(
			'description' => __( 'Type of the comment.', 'gutenberg' ),
			'type'        => 'string',
			'context'     => array( 'view', 'edit', 'embed' ),
			'readonly'    => true,
		);

		return $schema;
	}

	/**
	 * If empty comments are not allowed, checks if the provided comment content is not empty.
	 *
	 * @since 6.9.0
	 *
	 * @param array $prepared_comment The prepared comment data.
	 * @return bool True if the content is allowed, false otherwise.
	 */
	protected function check_is_comment_content_allowed( $prepared_comment ) {
		$check = wp_parse_args(
			$prepared_comment,
			array(
				'comment_post_ID'      => 0,
				'comment_author'       => null,
				'comment_author_email' => null,
				'comment_author_url'   => null,
				'comment_parent'       => 0,
				'user_id'              => 0,
			)
		);

		/** This filter is documented in wp-includes/comment.php */
		$allow_empty = apply_filters( 'allow_empty_comment', false, $check );

		if ( $allow_empty ) {
			return true;
		}

		// Allow empty block comments only when resolution metadata is valid [backport].
		if (
			isset( $check['comment_type'] ) &&
			'note' === $check['comment_type'] &&
			isset( $check['meta']['_wp_note_status'] ) &&
			in_array( $check['meta']['_wp_note_status'], array( 'resolved', 'reopen' ), true )
		) {
			return true;
		}

		/*
		 * Do not allow a comment to be created with missing or empty
		 * comment_content. See wp_handle_comment_submission().
		 */
		return '' !== $check['comment_content'];
	}
}

add_action(
	'rest_api_init',
	function () {
		$controller = new Gutenberg_REST_Comment_Controller_6_9();
		$controller->register_routes();
	}
);
