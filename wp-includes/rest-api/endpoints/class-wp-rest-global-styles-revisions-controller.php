<?php
/**
 * REST API: WP_REST_Global_Styles_Revisions_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 6.3.0
 */

/**
 * Core class used to access global styles revisions via the REST API.
 *
 * @since 6.3.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Global_Styles_Revisions_Controller extends WP_REST_Controller {
	/**
	 * Parent post type.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $parent_post_type;

	/**
	 * The base of the parent controller's route.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $parent_base;

	/**
	 * Constructor.
	 *
	 * @since 6.3.0
	 */
	public function __construct() {
		$this->parent_post_type = 'wp_global_styles';
		$this->rest_base        = 'revisions';
		$this->parent_base      = 'global-styles';
		$this->namespace        = 'wp/v2';
	}

	/**
	 * Registers the controller's routes.
	 *
	 * @since 6.3.0
	 * @since 6.5.0 Added route to fetch individual global styles revisions.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->parent_base . '/(?P<parent>[\d]+)/' . $this->rest_base,
			array(
				'args'   => array(
					'parent' => array(
						'description' => __( 'The ID for the parent of the revision.' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $this->get_collection_params(),
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
						'description' => __( 'The ID for the parent of the global styles revision.' ),
						'type'        => 'integer',
					),
					'id'     => array(
						'description' => __( 'Unique identifier for the global styles revision.' ),
						'type'        => 'integer',
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
	 * Retrieves the query params for collections.
	 *
	 * Inherits from WP_REST_Controller::get_collection_params(),
	 * also reflects changes to return value WP_REST_Revisions_Controller::get_collection_params().
	 *
	 * @since 6.3.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$collection_params                       = parent::get_collection_params();
		$collection_params['context']['default'] = 'view';
		$collection_params['offset']             = array(
			'description' => __( 'Offset the result set by a specific number of items.' ),
			'type'        => 'integer',
		);
		unset( $collection_params['search'] );
		unset( $collection_params['per_page']['default'] );

		return $collection_params;
	}

	/**
	 * Returns decoded JSON from post content string,
	 * or a 404 if not found.
	 *
	 * @since 6.3.0
	 *
	 * @param string $raw_json Encoded JSON from global styles custom post content.
	 * @return Array|WP_Error
	 */
	protected function get_decoded_global_styles_json( $raw_json ) {
		$decoded_json = json_decode( $raw_json, true );

		if ( is_array( $decoded_json ) && isset( $decoded_json['isGlobalStylesUserThemeJSON'] ) && true === $decoded_json['isGlobalStylesUserThemeJSON'] ) {
			return $decoded_json;
		}

		return new WP_Error(
			'rest_global_styles_not_found',
			__( 'Cannot find user global styles revisions.' ),
			array( 'status' => 404 )
		);
	}

	/**
	 * Returns paginated revisions of the given global styles config custom post type.
	 *
	 * The bulk of the body is taken from WP_REST_Revisions_Controller->get_items,
	 * but global styles does not require as many parameters.
	 *
	 * @since 6.3.0
	 *
	 * @param WP_REST_Request $request The request instance.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_items( $request ) {
		$parent = $this->get_parent( $request['parent'] );

		if ( is_wp_error( $parent ) ) {
			return $parent;
		}

		$global_styles_config = $this->get_decoded_global_styles_json( $parent->post_content );

		if ( is_wp_error( $global_styles_config ) ) {
			return $global_styles_config;
		}

		if ( wp_revisions_enabled( $parent ) ) {
			$registered = $this->get_collection_params();
			$query_args = array(
				'post_parent'    => $parent->ID,
				'post_type'      => 'revision',
				'post_status'    => 'inherit',
				'posts_per_page' => -1,
				'orderby'        => 'date ID',
				'order'          => 'DESC',
			);

			$parameter_mappings = array(
				'offset'   => 'offset',
				'page'     => 'paged',
				'per_page' => 'posts_per_page',
			);

			foreach ( $parameter_mappings as $api_param => $wp_param ) {
				if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
					$query_args[ $wp_param ] = $request[ $api_param ];
				}
			}

			$revisions_query = new WP_Query();
			$revisions       = $revisions_query->query( $query_args );
			$offset          = isset( $query_args['offset'] ) ? (int) $query_args['offset'] : 0;
			$page            = (int) $query_args['paged'];
			$total_revisions = $revisions_query->found_posts;

			if ( $total_revisions < 1 ) {
				// Out-of-bounds, run the query again without LIMIT for total count.
				unset( $query_args['paged'], $query_args['offset'] );
				$count_query = new WP_Query();
				$count_query->query( $query_args );

				$total_revisions = $count_query->found_posts;
			}

			if ( $revisions_query->query_vars['posts_per_page'] > 0 ) {
				$max_pages = (int) ceil( $total_revisions / (int) $revisions_query->query_vars['posts_per_page'] );
			} else {
				$max_pages = $total_revisions > 0 ? 1 : 0;
			}
			if ( $total_revisions > 0 ) {
				if ( $offset >= $total_revisions ) {
					return new WP_Error(
						'rest_revision_invalid_offset_number',
						__( 'The offset number requested is larger than or equal to the number of available revisions.' ),
						array( 'status' => 400 )
					);
				} elseif ( ! $offset && $page > $max_pages ) {
					return new WP_Error(
						'rest_revision_invalid_page_number',
						__( 'The page number requested is larger than the number of pages available.' ),
						array( 'status' => 400 )
					);
				}
			}
		} else {
			$revisions       = array();
			$total_revisions = 0;
			$max_pages       = 0;
			$page            = (int) $request['page'];
		}

		$response = array();

		foreach ( $revisions as $revision ) {
			$data       = $this->prepare_item_for_response( $revision, $request );
			$response[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $response );

		$response->header( 'X-WP-Total', (int) $total_revisions );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$request_params = $request->get_query_params();
		$base_path      = rest_url( sprintf( '%s/%s/%d/%s', $this->namespace, $this->parent_base, $request['parent'], $this->rest_base ) );
		$base           = add_query_arg( urlencode_deep( $request_params ), $base_path );

		if ( $page > 1 ) {
			$prev_page = $page - 1;

			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}

			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );

			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Retrieves one global styles revision from the collection.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$parent = $this->get_parent( $request['parent'] );
		if ( is_wp_error( $parent ) ) {
			return $parent;
		}

		$revision = $this->get_revision( $request['id'] );
		if ( is_wp_error( $revision ) ) {
			return $revision;
		}

		$response = $this->prepare_item_for_response( $revision, $request );
		return rest_ensure_response( $response );
	}

	/**
	 * Gets the global styles revision, if the ID is valid.
	 *
	 * @since 6.5.0
	 *
	 * @param int $id Supplied ID.
	 * @return WP_Post|WP_Error Revision post object if ID is valid, WP_Error otherwise.
	 */
	protected function get_revision( $id ) {
		$error = new WP_Error(
			'rest_post_invalid_id',
			__( 'Invalid global styles revision ID.' ),
			array( 'status' => 404 )
		);

		if ( (int) $id <= 0 ) {
			return $error;
		}

		$revision = get_post( (int) $id );
		if ( empty( $revision ) || empty( $revision->ID ) || 'revision' !== $revision->post_type ) {
			return $error;
		}

		return $revision;
	}

	/**
	 * Checks the post_date_gmt or modified_gmt and prepare any post or
	 * modified date for single post output.
	 *
	 * Duplicate of WP_REST_Revisions_Controller::prepare_date_response.
	 *
	 * @since 6.3.0
	 *
	 * @param string      $date_gmt GMT publication time.
	 * @param string|null $date     Optional. Local publication time. Default null.
	 * @return string|null ISO8601/RFC3339 formatted datetime, otherwise null.
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
	 * Prepares the revision for the REST response.
	 *
	 * @since 6.3.0
	 *
	 * @param WP_Post         $post    Post revision object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error Response object.
	 */
	public function prepare_item_for_response( $post, $request ) {
		$parent               = $this->get_parent( $request['parent'] );
		$global_styles_config = $this->get_decoded_global_styles_json( $post->post_content );

		if ( is_wp_error( $global_styles_config ) ) {
			return $global_styles_config;
		}

		$fields = $this->get_fields_for_response( $request );
		$data   = array();

		if ( ! empty( $global_styles_config['styles'] ) || ! empty( $global_styles_config['settings'] ) ) {
			$global_styles_config = ( new WP_Theme_JSON( $global_styles_config, 'custom' ) )->get_raw_data();
			if ( rest_is_field_included( 'settings', $fields ) ) {
				$data['settings'] = ! empty( $global_styles_config['settings'] ) ? $global_styles_config['settings'] : new stdClass();
			}
			if ( rest_is_field_included( 'styles', $fields ) ) {
				$data['styles'] = ! empty( $global_styles_config['styles'] ) ? $global_styles_config['styles'] : new stdClass();
			}
		}

		if ( rest_is_field_included( 'author', $fields ) ) {
			$data['author'] = (int) $post->post_author;
		}

		if ( rest_is_field_included( 'date', $fields ) ) {
			$data['date'] = $this->prepare_date_response( $post->post_date_gmt, $post->post_date );
		}

		if ( rest_is_field_included( 'date_gmt', $fields ) ) {
			$data['date_gmt'] = $this->prepare_date_response( $post->post_date_gmt );
		}

		if ( rest_is_field_included( 'id', $fields ) ) {
			$data['id'] = (int) $post->ID;
		}

		if ( rest_is_field_included( 'modified', $fields ) ) {
			$data['modified'] = $this->prepare_date_response( $post->post_modified_gmt, $post->post_modified );
		}

		if ( rest_is_field_included( 'modified_gmt', $fields ) ) {
			$data['modified_gmt'] = $this->prepare_date_response( $post->post_modified_gmt );
		}

		if ( rest_is_field_included( 'parent', $fields ) ) {
			$data['parent'] = (int) $parent->ID;
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves the revision's schema, conforming to JSON Schema.
	 *
	 * @since 6.3.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => "{$this->parent_post_type}-revision",
			'type'       => 'object',
			// Base properties for every revision.
			'properties' => array(

				/*
				 * Adds settings and styles from the WP_REST_Revisions_Controller item fields.
				 * Leaves out GUID as global styles shouldn't be accessible via URL.
				 */
				'author'       => array(
					'description' => __( 'The ID for the author of the revision.' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'date'         => array(
					'description' => __( "The date the revision was published, in the site's timezone." ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'date_gmt'     => array(
					'description' => __( 'The date the revision was published, as GMT.' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'id'           => array(
					'description' => __( 'Unique identifier for the revision.' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'modified'     => array(
					'description' => __( "The date the revision was last modified, in the site's timezone." ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'modified_gmt' => array(
					'description' => __( 'The date the revision was last modified, as GMT.' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'parent'       => array(
					'description' => __( 'The ID for the parent of the revision.' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				// Adds settings and styles from the WP_REST_Global_Styles_Controller parent schema.
				'styles'       => array(
					'description' => __( 'Global styles.' ),
					'type'        => array( 'object' ),
					'context'     => array( 'view', 'edit' ),
				),
				'settings'     => array(
					'description' => __( 'Global settings.' ),
					'type'        => array( 'object' ),
					'context'     => array( 'view', 'edit' ),
				),
			),
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Checks if a given request has access to read a single global style.
	 *
	 * @since 6.3.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$post = $this->get_parent( $request['parent'] );
		if ( is_wp_error( $post ) ) {
			return $post;
		}

		/*
		 * The same check as WP_REST_Global_Styles_Controller::get_item_permissions_check.
		 */
		if ( ! current_user_can( 'read_post', $post->ID ) ) {
			return new WP_Error(
				'rest_cannot_view',
				__( 'Sorry, you are not allowed to view revisions for this global style.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Gets the parent post, if the ID is valid.
	 *
	 * Duplicate of WP_REST_Revisions_Controller::get_parent.
	 *
	 * @since 6.3.0
	 *
	 * @param int $parent_post_id Supplied ID.
	 * @return WP_Post|WP_Error Post object if ID is valid, WP_Error otherwise.
	 */
	protected function get_parent( $parent_post_id ) {
		$error = new WP_Error(
			'rest_post_invalid_parent',
			__( 'Invalid post parent ID.' ),
			array( 'status' => 404 )
		);

		if ( (int) $parent_post_id <= 0 ) {
			return $error;
		}

		$parent_post = get_post( (int) $parent_post_id );

		if ( empty( $parent_post ) || empty( $parent_post->ID )
			|| $this->parent_post_type !== $parent_post->post_type
		) {
			return $error;
		}

		return $parent_post;
	}
}
