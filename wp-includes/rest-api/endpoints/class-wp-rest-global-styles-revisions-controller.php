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
class WP_REST_Global_Styles_Revisions_Controller extends WP_REST_Revisions_Controller {
	/**
	 * Parent controller.
	 *
	 * @since 6.6.0
	 * @var WP_REST_Controller
	 */
	private $parent_controller;

	/**
	 * The base of the parent controller's route.
	 *
	 * @since 6.3.0
	 * @var string
	 */
	protected $parent_base;

	/**
	 * Parent post type.
	 *
	 * @since 6.6.0
	 * @var string
	 */
	protected $parent_post_type;

	/**
	 * Constructor.
	 *
	 * @since 6.3.0
	 * @since 6.6.0 Extends class from WP_REST_Revisions_Controller.
	 *
	 * @param string $parent_post_type Post type of the parent.
	 */
	public function __construct( $parent_post_type = 'wp_global_styles' ) {
		parent::__construct( $parent_post_type );
		$post_type_object  = get_post_type_object( $parent_post_type );
		$parent_controller = $post_type_object->get_rest_controller();

		if ( ! $parent_controller ) {
			$parent_controller = new WP_REST_Global_Styles_Controller( $parent_post_type );
		}

		$this->parent_controller = $parent_controller;
		$this->rest_base         = 'revisions';
		$this->parent_base       = ! empty( $post_type_object->rest_base ) ? $post_type_object->rest_base : $post_type_object->name;
		$this->namespace         = ! empty( $post_type_object->rest_namespace ) ? $post_type_object->rest_namespace : 'wp/v2';
	}

	/**
	 * Registers the controller's routes.
	 *
	 * @since 6.3.0
	 * @since 6.6.0 Added route to fetch individual global styles revisions.
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
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
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
	 * Prepares the revision for the REST response.
	 *
	 * @since 6.3.0
	 * @since 6.6.0 Added resolved URI links to the response.
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

		$fields     = $this->get_fields_for_response( $request );
		$data       = array();
		$theme_json = null;

		if ( ! empty( $global_styles_config['styles'] ) || ! empty( $global_styles_config['settings'] ) ) {
			$theme_json           = new WP_Theme_JSON( $global_styles_config, 'custom' );
			$global_styles_config = $theme_json->get_raw_data();
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

		$context             = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data                = $this->add_additional_fields_to_object( $data, $request );
		$data                = $this->filter_response_by_context( $data, $context );
		$response            = rest_ensure_response( $data );
		$resolved_theme_uris = WP_Theme_JSON_Resolver::get_resolved_theme_uris( $theme_json );

		if ( ! empty( $resolved_theme_uris ) ) {
			$response->add_links(
				array(
					'https://api.w.org/theme-file' => $resolved_theme_uris,
				)
			);
		}

		return $response;
	}

	/**
	 * Retrieves the revision's schema, conforming to JSON Schema.
	 *
	 * @since 6.3.0
	 * @since 6.6.0 Merged parent and parent controller schema data.
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema               = parent::get_item_schema();
		$parent_schema        = $this->parent_controller->get_item_schema();
		$schema['properties'] = array_merge( $schema['properties'], $parent_schema['properties'] );

		unset(
			$schema['properties']['guid'],
			$schema['properties']['slug'],
			$schema['properties']['meta'],
			$schema['properties']['content'],
			$schema['properties']['title']
		);

			$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the query params for collections.
	 * Removes params that are not supported by global styles revisions.
	 *
	 * @since 6.6.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();
		unset(
			$query_params['exclude'],
			$query_params['include'],
			$query_params['search'],
			$query_params['order'],
			$query_params['orderby']
		);
		return $query_params;
	}
}
