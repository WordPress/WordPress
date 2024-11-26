<?php
/**
 * REST API: WP_REST_Templates_Controller class
 *
 * @package    WordPress
 * @subpackage REST_API
 * @since 5.8.0
 */

/**
 * Base Templates REST API Controller.
 *
 * @since 5.8.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Templates_Controller extends WP_REST_Controller {

	/**
	 * Post type.
	 *
	 * @since 5.8.0
	 * @var string
	 */
	protected $post_type;

	/**
	 * Constructor.
	 *
	 * @since 5.8.0
	 *
	 * @param string $post_type Post type.
	 */
	public function __construct( $post_type ) {
		$this->post_type = $post_type;
		$obj             = get_post_type_object( $post_type );
		$this->rest_base = ! empty( $obj->rest_base ) ? $obj->rest_base : $obj->name;
		$this->namespace = ! empty( $obj->rest_namespace ) ? $obj->rest_namespace : 'wp/v2';
	}

	/**
	 * Registers the controllers routes.
	 *
	 * @since 5.8.0
	 * @since 6.1.0 Endpoint for fallback template content.
	 */
	public function register_routes() {
		// Lists all templates.
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

		// Get fallback template content.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/lookup',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_template_fallback' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'slug'            => array(
							'description' => __( 'The slug of the template to get the fallback for' ),
							'type'        => 'string',
							'required'    => true,
						),
						'is_custom'       => array(
							'description' => __( 'Indicates if a template is custom or part of the template hierarchy' ),
							'type'        => 'boolean',
						),
						'template_prefix' => array(
							'description' => __( 'The template prefix for the created template. This is used to extract the main template type, e.g. in `taxonomy-books` extracts the `taxonomy`' ),
							'type'        => 'string',
						),
					),
				),
			)
		);

		// Lists/updates a single template based on the given id.
		register_rest_route(
			$this->namespace,
			// The route.
			sprintf(
				'/%s/(?P<id>%s%s)',
				$this->rest_base,
				/*
				 * Matches theme's directory: `/themes/<subdirectory>/<theme>/` or `/themes/<theme>/`.
				 * Excludes invalid directory name characters: `/:<>*?"|`.
				 */
				'([^\/:<>\*\?"\|]+(?:\/[^\/:<>\*\?"\|]+)?)',
				// Matches the template name.
				'[\/\w%-]+'
			),
			array(
				'args'   => array(
					'id' => array(
						'description'       => __( 'The id of a template' ),
						'type'              => 'string',
						'sanitize_callback' => array( $this, '_sanitize_template_id' ),
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
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'type'        => 'boolean',
							'default'     => false,
							'description' => __( 'Whether to bypass Trash and force deletion.' ),
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Returns the fallback template for the given slug.
	 *
	 * @since 6.1.0
	 * @since 6.3.0 Ignore empty templates.
	 *
	 * @param WP_REST_Request $request The request instance.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_template_fallback( $request ) {
		$hierarchy = get_template_hierarchy( $request['slug'], $request['is_custom'], $request['template_prefix'] );

		do {
			$fallback_template = resolve_block_template( $request['slug'], $hierarchy, '' );
			array_shift( $hierarchy );
		} while ( ! empty( $hierarchy ) && empty( $fallback_template->content ) );

		// To maintain original behavior, return an empty object rather than a 404 error when no template is found.
		$response = $fallback_template ? $this->prepare_item_for_response( $fallback_template, $request ) : new stdClass();

		return rest_ensure_response( $response );
	}

	/**
	 * Checks if the user has permissions to make the request.
	 *
	 * @since 5.8.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	protected function permissions_check( $request ) {
		/*
		 * Verify if the current user has edit_theme_options capability.
		 * This capability is required to edit/view/delete templates.
		 */
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return new WP_Error(
				'rest_cannot_manage_templates',
				__( 'Sorry, you are not allowed to access the templates on this site.' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		return true;
	}

	/**
	 * Requesting this endpoint for a template like 'twentytwentytwo//home'
	 * requires using a path like /wp/v2/templates/twentytwentytwo//home. There
	 * are special cases when WordPress routing corrects the name to contain
	 * only a single slash like 'twentytwentytwo/home'.
	 *
	 * This method doubles the last slash if it's not already doubled. It relies
	 * on the template ID format {theme_name}//{template_slug} and the fact that
	 * slugs cannot contain slashes.
	 *
	 * @since 5.9.0
	 * @see https://core.trac.wordpress.org/ticket/54507
	 *
	 * @param string $id Template ID.
	 * @return string Sanitized template ID.
	 */
	public function _sanitize_template_id( $id ) {
		$id = urldecode( $id );

		$last_slash_pos = strrpos( $id, '/' );
		if ( false === $last_slash_pos ) {
			return $id;
		}

		$is_double_slashed = substr( $id, $last_slash_pos - 1, 1 ) === '/';
		if ( $is_double_slashed ) {
			return $id;
		}
		return (
			substr( $id, 0, $last_slash_pos )
			. '/'
			. substr( $id, $last_slash_pos )
		);
	}

	/**
	 * Checks if a given request has access to read templates.
	 *
	 * @since 5.8.0
	 * @since 6.6.0 Allow users with edit_posts capability to read templates.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( current_user_can( 'edit_posts' ) ) {
			return true;
		}
		foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
			if ( current_user_can( $post_type->cap->edit_posts ) ) {
				return true;
			}
		}

		return new WP_Error(
			'rest_cannot_manage_templates',
			__( 'Sorry, you are not allowed to access the templates on this site.' ),
			array(
				'status' => rest_authorization_required_code(),
			)
		);
	}

	/**
	 * Returns a list of templates.
	 *
	 * @since 5.8.0
	 *
	 * @param WP_REST_Request $request The request instance.
	 * @return WP_REST_Response
	 */
	public function get_items( $request ) {
		$query = array();
		if ( isset( $request['wp_id'] ) ) {
			$query['wp_id'] = $request['wp_id'];
		}
		if ( isset( $request['area'] ) ) {
			$query['area'] = $request['area'];
		}
		if ( isset( $request['post_type'] ) ) {
			$query['post_type'] = $request['post_type'];
		}

		$templates = array();
		foreach ( get_block_templates( $query, $this->post_type ) as $template ) {
			$data        = $this->prepare_item_for_response( $template, $request );
			$templates[] = $this->prepare_response_for_collection( $data );
		}

		return rest_ensure_response( $templates );
	}

	/**
	 * Checks if a given request has access to read a single template.
	 *
	 * @since 5.8.0
	 * @since 6.6.0 Allow users with edit_posts capability to read individual templates.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access for the item, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		if ( current_user_can( 'edit_posts' ) ) {
			return true;
		}
		foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
			if ( current_user_can( $post_type->cap->edit_posts ) ) {
				return true;
			}
		}

		return new WP_Error(
			'rest_cannot_manage_templates',
			__( 'Sorry, you are not allowed to access the templates on this site.' ),
			array(
				'status' => rest_authorization_required_code(),
			)
		);
	}

	/**
	 * Returns the given template
	 *
	 * @since 5.8.0
	 *
	 * @param WP_REST_Request $request The request instance.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_item( $request ) {
		if ( isset( $request['source'] ) && ( 'theme' === $request['source'] || 'plugin' === $request['source'] ) ) {
			$template = get_block_file_template( $request['id'], $this->post_type );
		} else {
			$template = get_block_template( $request['id'], $this->post_type );
		}

		if ( ! $template ) {
			return new WP_Error( 'rest_template_not_found', __( 'No templates exist with that id.' ), array( 'status' => 404 ) );
		}

		return $this->prepare_item_for_response( $template, $request );
	}

	/**
	 * Checks if a given request has access to write a single template.
	 *
	 * @since 5.8.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has write access for the item, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Updates a single template.
	 *
	 * @since 5.8.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$template = get_block_template( $request['id'], $this->post_type );
		if ( ! $template ) {
			return new WP_Error( 'rest_template_not_found', __( 'No templates exist with that id.' ), array( 'status' => 404 ) );
		}

		$post_before = get_post( $template->wp_id );

		if ( isset( $request['source'] ) && 'theme' === $request['source'] ) {
			wp_delete_post( $template->wp_id, true );
			$request->set_param( 'context', 'edit' );

			$template = get_block_template( $request['id'], $this->post_type );
			$response = $this->prepare_item_for_response( $template, $request );

			return rest_ensure_response( $response );
		}

		$changes = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $changes ) ) {
			return $changes;
		}

		if ( 'custom' === $template->source ) {
			$update = true;
			$result = wp_update_post( wp_slash( (array) $changes ), false );
		} else {
			$update      = false;
			$post_before = null;
			$result      = wp_insert_post( wp_slash( (array) $changes ), false );
		}

		if ( is_wp_error( $result ) ) {
			if ( 'db_update_error' === $result->get_error_code() ) {
				$result->add_data( array( 'status' => 500 ) );
			} else {
				$result->add_data( array( 'status' => 400 ) );
			}
			return $result;
		}

		$template      = get_block_template( $request['id'], $this->post_type );
		$fields_update = $this->update_additional_fields_for_object( $template, $request );
		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$request->set_param( 'context', 'edit' );

		$post = get_post( $template->wp_id );
		/** This action is documented in wp-includes/rest-api/endpoints/class-wp-rest-posts-controller.php */
		do_action( "rest_after_insert_{$this->post_type}", $post, $request, false );

		wp_after_insert_post( $post, $update, $post_before );

		$response = $this->prepare_item_for_response( $template, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Checks if a given request has access to create a template.
	 *
	 * @since 5.8.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Creates a single template.
	 *
	 * @since 5.8.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {
		$prepared_post = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $prepared_post ) ) {
			return $prepared_post;
		}

		$prepared_post->post_name = $request['slug'];
		$post_id                  = wp_insert_post( wp_slash( (array) $prepared_post ), true );
		if ( is_wp_error( $post_id ) ) {
			if ( 'db_insert_error' === $post_id->get_error_code() ) {
				$post_id->add_data( array( 'status' => 500 ) );
			} else {
				$post_id->add_data( array( 'status' => 400 ) );
			}

			return $post_id;
		}
		$posts = get_block_templates( array( 'wp_id' => $post_id ), $this->post_type );
		if ( ! count( $posts ) ) {
			return new WP_Error( 'rest_template_insert_error', __( 'No templates exist with that id.' ), array( 'status' => 400 ) );
		}
		$id            = $posts[0]->id;
		$post          = get_post( $post_id );
		$template      = get_block_template( $id, $this->post_type );
		$fields_update = $this->update_additional_fields_for_object( $template, $request );
		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		/** This action is documented in wp-includes/rest-api/endpoints/class-wp-rest-posts-controller.php */
		do_action( "rest_after_insert_{$this->post_type}", $post, $request, true );

		wp_after_insert_post( $post, false, null );

		$response = $this->prepare_item_for_response( $template, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $template->id ) ) );

		return $response;
	}

	/**
	 * Checks if a given request has access to delete a single template.
	 *
	 * @since 5.8.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has delete access for the item, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Deletes a single template.
	 *
	 * @since 5.8.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
		$template = get_block_template( $request['id'], $this->post_type );
		if ( ! $template ) {
			return new WP_Error( 'rest_template_not_found', __( 'No templates exist with that id.' ), array( 'status' => 404 ) );
		}
		if ( 'custom' !== $template->source ) {
			return new WP_Error( 'rest_invalid_template', __( 'Templates based on theme files can\'t be removed.' ), array( 'status' => 400 ) );
		}

		$id    = $template->wp_id;
		$force = (bool) $request['force'];

		$request->set_param( 'context', 'edit' );

		// If we're forcing, then delete permanently.
		if ( $force ) {
			$previous = $this->prepare_item_for_response( $template, $request );
			$result   = wp_delete_post( $id, true );
			$response = new WP_REST_Response();
			$response->set_data(
				array(
					'deleted'  => true,
					'previous' => $previous->get_data(),
				)
			);
		} else {
			// Otherwise, only trash if we haven't already.
			if ( 'trash' === $template->status ) {
				return new WP_Error(
					'rest_template_already_trashed',
					__( 'The template has already been deleted.' ),
					array( 'status' => 410 )
				);
			}

			/*
			 * (Note that internally this falls through to `wp_delete_post()`
			 * if the Trash is disabled.)
			 */
			$result           = wp_trash_post( $id );
			$template->status = 'trash';
			$response         = $this->prepare_item_for_response( $template, $request );
		}

		if ( ! $result ) {
			return new WP_Error(
				'rest_cannot_delete',
				__( 'The template cannot be deleted.' ),
				array( 'status' => 500 )
			);
		}

		return $response;
	}

	/**
	 * Prepares a single template for create or update.
	 *
	 * @since 5.8.0
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return stdClass|WP_Error Changes to pass to wp_update_post.
	 */
	protected function prepare_item_for_database( $request ) {
		$template = $request['id'] ? get_block_template( $request['id'], $this->post_type ) : null;
		$changes  = new stdClass();
		if ( null === $template ) {
			$changes->post_type   = $this->post_type;
			$changes->post_status = 'publish';
			$changes->tax_input   = array(
				'wp_theme' => isset( $request['theme'] ) ? $request['theme'] : get_stylesheet(),
			);
		} elseif ( 'custom' !== $template->source ) {
			$changes->post_name   = $template->slug;
			$changes->post_type   = $this->post_type;
			$changes->post_status = 'publish';
			$changes->tax_input   = array(
				'wp_theme' => $template->theme,
			);
			$changes->meta_input  = array(
				'origin' => $template->source,
			);
		} else {
			$changes->post_name   = $template->slug;
			$changes->ID          = $template->wp_id;
			$changes->post_status = 'publish';
		}
		if ( isset( $request['content'] ) ) {
			if ( is_string( $request['content'] ) ) {
				$changes->post_content = $request['content'];
			} elseif ( isset( $request['content']['raw'] ) ) {
				$changes->post_content = $request['content']['raw'];
			}
		} elseif ( null !== $template && 'custom' !== $template->source ) {
			$changes->post_content = $template->content;
		}
		if ( isset( $request['title'] ) ) {
			if ( is_string( $request['title'] ) ) {
				$changes->post_title = $request['title'];
			} elseif ( ! empty( $request['title']['raw'] ) ) {
				$changes->post_title = $request['title']['raw'];
			}
		} elseif ( null !== $template && 'custom' !== $template->source ) {
			$changes->post_title = $template->title;
		}
		if ( isset( $request['description'] ) ) {
			$changes->post_excerpt = $request['description'];
		} elseif ( null !== $template && 'custom' !== $template->source ) {
			$changes->post_excerpt = $template->description;
		}

		if ( 'wp_template' === $this->post_type && isset( $request['is_wp_suggestion'] ) ) {
			$changes->meta_input     = wp_parse_args(
				array(
					'is_wp_suggestion' => $request['is_wp_suggestion'],
				),
				$changes->meta_input = array()
			);
		}

		if ( 'wp_template_part' === $this->post_type ) {
			if ( isset( $request['area'] ) ) {
				$changes->tax_input['wp_template_part_area'] = _filter_block_template_part_area( $request['area'] );
			} elseif ( null !== $template && 'custom' !== $template->source && $template->area ) {
				$changes->tax_input['wp_template_part_area'] = _filter_block_template_part_area( $template->area );
			} elseif ( empty( $template->area ) ) {
				$changes->tax_input['wp_template_part_area'] = WP_TEMPLATE_PART_AREA_UNCATEGORIZED;
			}
		}

		if ( ! empty( $request['author'] ) ) {
			$post_author = (int) $request['author'];

			if ( get_current_user_id() !== $post_author ) {
				$user_obj = get_userdata( $post_author );

				if ( ! $user_obj ) {
					return new WP_Error(
						'rest_invalid_author',
						__( 'Invalid author ID.' ),
						array( 'status' => 400 )
					);
				}
			}

			$changes->post_author = $post_author;
		}

		/** This filter is documented in wp-includes/rest-api/endpoints/class-wp-rest-posts-controller.php */
		return apply_filters( "rest_pre_insert_{$this->post_type}", $changes, $request );
	}

	/**
	 * Prepare a single template output for response
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Renamed `$template` to `$item` to match parent class for PHP 8 named parameter support.
	 * @since 6.3.0 Added `modified` property to the response.
	 *
	 * @param WP_Block_Template $item    Template instance.
	 * @param WP_REST_Request   $request Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {
		/*
		 * Resolve pattern blocks so they don't need to be resolved client-side
		 * in the editor, improving performance.
		 */
		$blocks        = parse_blocks( $item->content );
		$blocks        = resolve_pattern_blocks( $blocks );
		$item->content = serialize_blocks( $blocks );

		// Restores the more descriptive, specific name for use within this method.
		$template = $item;

		$fields = $this->get_fields_for_response( $request );

		// Base fields for every template.
		$data = array();

		if ( rest_is_field_included( 'id', $fields ) ) {
			$data['id'] = $template->id;
		}

		if ( rest_is_field_included( 'theme', $fields ) ) {
			$data['theme'] = $template->theme;
		}

		if ( rest_is_field_included( 'content', $fields ) ) {
			$data['content'] = array();
		}
		if ( rest_is_field_included( 'content.raw', $fields ) ) {
			$data['content']['raw'] = $template->content;
		}

		if ( rest_is_field_included( 'content.block_version', $fields ) ) {
			$data['content']['block_version'] = block_version( $template->content );
		}

		if ( rest_is_field_included( 'slug', $fields ) ) {
			$data['slug'] = $template->slug;
		}

		if ( rest_is_field_included( 'source', $fields ) ) {
			$data['source'] = $template->source;
		}

		if ( rest_is_field_included( 'origin', $fields ) ) {
			$data['origin'] = $template->origin;
		}

		if ( rest_is_field_included( 'type', $fields ) ) {
			$data['type'] = $template->type;
		}

		if ( rest_is_field_included( 'description', $fields ) ) {
			$data['description'] = $template->description;
		}

		if ( rest_is_field_included( 'title', $fields ) ) {
			$data['title'] = array();
		}

		if ( rest_is_field_included( 'title.raw', $fields ) ) {
			$data['title']['raw'] = $template->title;
		}

		if ( rest_is_field_included( 'title.rendered', $fields ) ) {
			if ( $template->wp_id ) {
				/** This filter is documented in wp-includes/post-template.php */
				$data['title']['rendered'] = apply_filters( 'the_title', $template->title, $template->wp_id );
			} else {
				$data['title']['rendered'] = $template->title;
			}
		}

		if ( rest_is_field_included( 'status', $fields ) ) {
			$data['status'] = $template->status;
		}

		if ( rest_is_field_included( 'wp_id', $fields ) ) {
			$data['wp_id'] = (int) $template->wp_id;
		}

		if ( rest_is_field_included( 'has_theme_file', $fields ) ) {
			$data['has_theme_file'] = (bool) $template->has_theme_file;
		}

		if ( rest_is_field_included( 'is_custom', $fields ) && 'wp_template' === $template->type ) {
			$data['is_custom'] = $template->is_custom;
		}

		if ( rest_is_field_included( 'author', $fields ) ) {
			$data['author'] = (int) $template->author;
		}

		if ( rest_is_field_included( 'area', $fields ) && 'wp_template_part' === $template->type ) {
			$data['area'] = $template->area;
		}

		if ( rest_is_field_included( 'modified', $fields ) ) {
			$data['modified'] = mysql_to_rfc3339( $template->modified );
		}

		if ( rest_is_field_included( 'author_text', $fields ) ) {
			$data['author_text'] = self::get_wp_templates_author_text_field( $template );
		}

		if ( rest_is_field_included( 'original_source', $fields ) ) {
			$data['original_source'] = self::get_wp_templates_original_source_field( $template );
		}

		if ( rest_is_field_included( 'plugin', $fields ) ) {
			$registered_template = WP_Block_Templates_Registry::get_instance()->get_by_slug( $template->slug );
			if ( $registered_template ) {
				$data['plugin'] = $registered_template->plugin;
			}
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$links = $this->prepare_links( $template->id );
			$response->add_links( $links );
			if ( ! empty( $links['self']['href'] ) ) {
				$actions = $this->get_available_actions();
				$self    = $links['self']['href'];
				foreach ( $actions as $rel ) {
					$response->add_link( $rel, $self );
				}
			}
		}

		return $response;
	}

	/**
	 * Returns the source from where the template originally comes from.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Block_Template $template_object Template instance.
	 * @return string                            Original source of the template one of theme, plugin, site, or user.
	 */
	private static function get_wp_templates_original_source_field( $template_object ) {
		if ( 'wp_template' === $template_object->type || 'wp_template_part' === $template_object->type ) {
			/*
			 * Added by theme.
			 * Template originally provided by a theme, but customized by a user.
			 * Templates originally didn't have the 'origin' field so identify
			 * older customized templates by checking for no origin and a 'theme'
			 * or 'custom' source.
			 */
			if ( $template_object->has_theme_file &&
			( 'theme' === $template_object->origin || (
				empty( $template_object->origin ) && in_array(
					$template_object->source,
					array(
						'theme',
						'custom',
					),
					true
				) )
			)
			) {
				return 'theme';
			}

			// Added by plugin.
			if ( 'plugin' === $template_object->origin ) {
				return 'plugin';
			}

			/*
			 * Added by site.
			 * Template was created from scratch, but has no author. Author support
			 * was only added to templates in WordPress 5.9. Fallback to showing the
			 * site logo and title.
			 */
			if ( empty( $template_object->has_theme_file ) && 'custom' === $template_object->source && empty( $template_object->author ) ) {
				return 'site';
			}
		}

		// Added by user.
		return 'user';
	}

	/**
	 * Returns a human readable text for the author of the template.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Block_Template $template_object Template instance.
	 * @return string                            Human readable text for the author.
	 */
	private static function get_wp_templates_author_text_field( $template_object ) {
		$original_source = self::get_wp_templates_original_source_field( $template_object );
		switch ( $original_source ) {
			case 'theme':
				$theme_name = wp_get_theme( $template_object->theme )->get( 'Name' );
				return empty( $theme_name ) ? $template_object->theme : $theme_name;
			case 'plugin':
				if ( ! function_exists( 'get_plugins' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
				if ( isset( $template_object->plugin ) ) {
					$plugins = wp_get_active_and_valid_plugins();

					foreach ( $plugins as $plugin_file ) {
						$plugin_basename = plugin_basename( $plugin_file );
						// Split basename by '/' to get the plugin slug.
						list( $plugin_slug, ) = explode( '/', $plugin_basename );

						if ( $plugin_slug === $template_object->plugin ) {
							$plugin_data = get_plugin_data( $plugin_file );

							if ( ! empty( $plugin_data['Name'] ) ) {
								return $plugin_data['Name'];
							}

							break;
						}
					}
				}

				/*
				 * Fall back to the theme name if the plugin is not defined. That's needed to keep backwards
				 * compatibility with templates that were registered before the plugin attribute was added.
				 */
				$plugins         = get_plugins();
				$plugin_basename = plugin_basename( sanitize_text_field( $template_object->theme . '.php' ) );
				if ( isset( $plugins[ $plugin_basename ] ) && isset( $plugins[ $plugin_basename ]['Name'] ) ) {
					return $plugins[ $plugin_basename ]['Name'];
				}
				return isset( $template_object->plugin ) ?
					$template_object->plugin :
					$template_object->theme;
			case 'site':
				return get_bloginfo( 'name' );
			case 'user':
				$author = get_user_by( 'id', $template_object->author );
				if ( ! $author ) {
					return __( 'Unknown author' );
				}
				return $author->get( 'display_name' );
		}

		// Fail-safe to return a string should the original source ever fall through.
		return '';
	}


	/**
	 * Prepares links for the request.
	 *
	 * @since 5.8.0
	 *
	 * @param integer $id ID.
	 * @return array Links for the given post.
	 */
	protected function prepare_links( $id ) {
		$links = array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%s', $this->namespace, $this->rest_base, $id ) ),
			),
			'collection' => array(
				'href' => rest_url( rest_get_route_for_post_type_items( $this->post_type ) ),
			),
			'about'      => array(
				'href' => rest_url( 'wp/v2/types/' . $this->post_type ),
			),
		);

		if ( post_type_supports( $this->post_type, 'revisions' ) ) {
			$template = get_block_template( $id, $this->post_type );
			if ( $template instanceof WP_Block_Template && ! empty( $template->wp_id ) ) {
				$revisions       = wp_get_latest_revision_id_and_total_count( $template->wp_id );
				$revisions_count = ! is_wp_error( $revisions ) ? $revisions['count'] : 0;
				$revisions_base  = sprintf( '/%s/%s/%s/revisions', $this->namespace, $this->rest_base, $id );

				$links['version-history'] = array(
					'href'  => rest_url( $revisions_base ),
					'count' => $revisions_count,
				);

				if ( $revisions_count > 0 ) {
					$links['predecessor-version'] = array(
						'href' => rest_url( $revisions_base . '/' . $revisions['latest_id'] ),
						'id'   => $revisions['latest_id'],
					);
				}
			}
		}

		return $links;
	}

	/**
	 * Get the link relations available for the post and current user.
	 *
	 * @since 5.8.0
	 *
	 * @return string[] List of link relations.
	 */
	protected function get_available_actions() {
		$rels = array();

		$post_type = get_post_type_object( $this->post_type );

		if ( current_user_can( $post_type->cap->publish_posts ) ) {
			$rels[] = 'https://api.w.org/action-publish';
		}

		if ( current_user_can( 'unfiltered_html' ) ) {
			$rels[] = 'https://api.w.org/action-unfiltered-html';
		}

		return $rels;
	}

	/**
	 * Retrieves the query params for the posts collection.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added `'area'` and `'post_type'`.
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array(
			'context'   => $this->get_context_param( array( 'default' => 'view' ) ),
			'wp_id'     => array(
				'description' => __( 'Limit to the specified post id.' ),
				'type'        => 'integer',
			),
			'area'      => array(
				'description' => __( 'Limit to the specified template part area.' ),
				'type'        => 'string',
			),
			'post_type' => array(
				'description' => __( 'Post type to get the templates for.' ),
				'type'        => 'string',
			),
		);
	}

	/**
	 * Retrieves the block type' schema, conforming to JSON Schema.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Added `'area'`.
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'id'              => array(
					'description' => __( 'ID of template.' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'slug'            => array(
					'description' => __( 'Unique slug identifying the template.' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'required'    => true,
					'minLength'   => 1,
					'pattern'     => '[a-zA-Z0-9_\%-]+',
				),
				'theme'           => array(
					'description' => __( 'Theme identifier for the template.' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'type'            => array(
					'description' => __( 'Type of template.' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'source'          => array(
					'description' => __( 'Source of template' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'origin'          => array(
					'description' => __( 'Source of a customized template' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'content'         => array(
					'description' => __( 'Content of template.' ),
					'type'        => array( 'object', 'string' ),
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
					'properties'  => array(
						'raw'           => array(
							'description' => __( 'Content for the template, as it exists in the database.' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'block_version' => array(
							'description' => __( 'Version of the content block format used by the template.' ),
							'type'        => 'integer',
							'context'     => array( 'edit' ),
							'readonly'    => true,
						),
					),
				),
				'title'           => array(
					'description' => __( 'Title of template.' ),
					'type'        => array( 'object', 'string' ),
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
					'properties'  => array(
						'raw'      => array(
							'description' => __( 'Title for the template, as it exists in the database.' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
						),
						'rendered' => array(
							'description' => __( 'HTML title for the template, transformed for display.' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
						),
					),
				),
				'description'     => array(
					'description' => __( 'Description of template.' ),
					'type'        => 'string',
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'status'          => array(
					'description' => __( 'Status of template.' ),
					'type'        => 'string',
					'enum'        => array_keys( get_post_stati( array( 'internal' => false ) ) ),
					'default'     => 'publish',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'wp_id'           => array(
					'description' => __( 'Post ID.' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'has_theme_file'  => array(
					'description' => __( 'Theme file exists.' ),
					'type'        => 'bool',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'author'          => array(
					'description' => __( 'The ID for the author of the template.' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'modified'        => array(
					'description' => __( "The date the template was last modified, in the site's timezone." ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'author_text'     => array(
					'type'        => 'string',
					'description' => __( 'Human readable text for the author.' ),
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'original_source' => array(
					'description' => __( 'Where the template originally comes from e.g. \'theme\'' ),
					'type'        => 'string',
					'readonly'    => true,
					'context'     => array( 'view', 'edit', 'embed' ),
					'enum'        => array(
						'theme',
						'plugin',
						'site',
						'user',
					),
				),
			),
		);

		if ( 'wp_template' === $this->post_type ) {
			$schema['properties']['is_custom'] = array(
				'description' => __( 'Whether a template is a custom template.' ),
				'type'        => 'bool',
				'context'     => array( 'embed', 'view', 'edit' ),
				'readonly'    => true,
			);
			$schema['properties']['plugin']    = array(
				'type'        => 'string',
				'description' => __( 'Plugin that registered the template.' ),
				'readonly'    => true,
				'context'     => array( 'view', 'edit', 'embed' ),
			);
		}

		if ( 'wp_template_part' === $this->post_type ) {
			$schema['properties']['area'] = array(
				'description' => __( 'Where the template part is intended for use (header, footer, etc.)' ),
				'type'        => 'string',
				'context'     => array( 'embed', 'view', 'edit' ),
			);
		}

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}
}
