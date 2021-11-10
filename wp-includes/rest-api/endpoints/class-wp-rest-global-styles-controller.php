<?php
/**
 * REST API: WP_REST_Global_Styles_Controller class
 *
 * @package    WordPress
 * @subpackage REST_API
 * @since 5.9.0
 */

/**
 * Base Global Styles REST API Controller.
 */
class WP_REST_Global_Styles_Controller extends WP_REST_Controller {
	/**
	 * Constructor.
	 * @since 5.9.0
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'global-styles';
	}

	/**
	 * Registers the controllers routes.
	 *
	 * @since 5.9.0
	 *
	 * @return void
	 */
	public function register_routes() {
		// List themes global styles.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/themes/(?P<stylesheet>[^.\/]+(?:\/[^.\/]+)?)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_theme_item' ),
					'permission_callback' => array( $this, 'get_theme_item_permissions_check' ),
					'args'                => array(
						'stylesheet' => array(
							'description' => __( 'The theme identifier' ),
							'type'        => 'string',
						),
					),
				),
			)
		);

		// Lists/updates a single gloval style variation based on the given id.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\/\w-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'id' => array(
							'description' => __( 'The id of a template' ),
							'type'        => 'string',
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if the user has permissions to make the request.
	 *
	 * @since 5.9.0
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	protected function permissions_check() {
		// Verify if the current user has edit_theme_options capability.
		// This capability is required to edit/view/delete templates.
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return new WP_Error(
				'rest_cannot_manage_global_styles',
				__( 'Sorry, you are not allowed to access the global styles on this site.' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to read a single global styles config.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access for the item, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Returns the given global styles config.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request The request instance.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_item( $request ) {
		$post = get_post( $request['id'] );
		if ( ! $post || 'wp_global_styles' !== $post->post_type ) {
			return new WP_Error( 'rest_global_styles_not_found', __( 'No global styles config exist with that id.' ), array( 'status' => 404 ) );
		}

		return $this->prepare_item_for_response( $post, $request );
	}

	/**
	 * Checks if a given request has access to write a single global styles config.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has write access for the item, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Updates a single global style config.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$post = get_post( $request['id'] );
		if ( ! $post || 'wp_global_styles' !== $post->post_type ) {
			return new WP_Error( 'rest_global_styles_not_found', __( 'No global styles config exist with that id.' ), array( 'status' => 404 ) );
		}

		$changes = $this->prepare_item_for_database( $request );
		$result  = wp_update_post( wp_slash( (array) $changes ), true );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$post          = get_post( $request['id'] );
		$fields_update = $this->update_additional_fields_for_object( $post, $request );
		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		return $this->prepare_item_for_response(
			get_post( $request['id'] ),
			$request
		);
	}

	/**
	 * Prepares a single global styles config for update.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return stdClass Changes to pass to wp_update_post.
	 */
	protected function prepare_item_for_database( $request ) {
		$changes     = new stdClass();
		$changes->ID = $request['id'];

		$post            = get_post( $request['id'] );
		$existing_config = array();
		if ( $post ) {
			$existing_config     = json_decode( $post->post_content, true );
			$json_decoding_error = json_last_error();
			if ( JSON_ERROR_NONE !== $json_decoding_error || ! isset( $existing_config['isGlobalStylesUserThemeJSON'] ) ||
				! $existing_config['isGlobalStylesUserThemeJSON'] ) {
				$existing_config = array();
			}
		}

		if ( isset( $request['styles'] ) || isset( $request['settings'] ) ) {
			$config = array();
			if ( isset( $request['styles'] ) ) {
				$config['styles'] = $request['styles'];
			} elseif ( isset( $existing_config['styles'] ) ) {
				$config['styles'] = $existing_config['styles'];
			}
			if ( isset( $request['settings'] ) ) {
				$config['settings'] = $request['settings'];
			} elseif ( isset( $existing_config['settings'] ) ) {
				$config['settings'] = $existing_config['settings'];
			}
			$config['isGlobalStylesUserThemeJSON'] = true;
			$config['version']                     = WP_Theme_JSON::LATEST_SCHEMA;
			$changes->post_content                 = wp_json_encode( $config );
		}

		// Post title.
		if ( isset( $request['title'] ) ) {
			if ( is_string( $request['title'] ) ) {
				$changes->post_title = $request['title'];
			} elseif ( ! empty( $request['title']['raw'] ) ) {
				$changes->post_title = $request['title']['raw'];
			}
		}

		return $changes;
	}

	/**
	 * Prepare a global styles config output for response.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_Post         $post    Global Styles post object.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response $data
	 */
	public function prepare_item_for_response( $post, $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$config                           = json_decode( $post->post_content, true );
		$is_global_styles_user_theme_json = isset( $config['isGlobalStylesUserThemeJSON'] ) && true === $config['isGlobalStylesUserThemeJSON'];
		$fields                           = $this->get_fields_for_response( $request );

		// Base fields for every post.
		$data = array();

		if ( rest_is_field_included( 'id', $fields ) ) {
			$data['id'] = $post->ID;
		}

		if ( rest_is_field_included( 'title', $fields ) ) {
			$data['title'] = array();
		}
		if ( rest_is_field_included( 'title.raw', $fields ) ) {
			$data['title']['raw'] = $post->post_title;
		}
		if ( rest_is_field_included( 'title.rendered', $fields ) ) {
			add_filter( 'protected_title_format', array( $this, 'protected_title_format' ) );

			$data['title']['rendered'] = get_the_title( $post->ID );

			remove_filter( 'protected_title_format', array( $this, 'protected_title_format' ) );
		}

		if ( rest_is_field_included( 'settings', $fields ) ) {
			$data['settings'] = ! empty( $config['settings'] ) && $is_global_styles_user_theme_json ? $config['settings'] : new stdClass();
		}

		if ( rest_is_field_included( 'styles', $fields ) ) {
			$data['styles'] = ! empty( $config['styles'] ) && $is_global_styles_user_theme_json ? $config['styles'] : new stdClass();
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$links = $this->prepare_links( $post->ID );
		$response->add_links( $links );
		if ( ! empty( $links['self']['href'] ) ) {
			$actions = $this->get_available_actions();
			$self    = $links['self']['href'];
			foreach ( $actions as $rel ) {
				$response->add_link( $rel, $self );
			}
		}

		return $response;
	}


	/**
	 * Prepares links for the request.
	 *
	 * @since 5.9.0
	 *
	 * @param integer $id ID.
	 * @return array Links for the given post.
	 */
	protected function prepare_links( $id ) {
		$base = sprintf( '%s/%s', $this->namespace, $this->rest_base );

		$links = array(
			'self'       => array(
				'href' => rest_url( trailingslashit( $base ) . $id ),
			),
			'collection' => array(
				'href' => rest_url( $base ),
			),
		);

		return $links;
	}

	/**
	 * Get the link relations available for the post and current user.
	 *
	 * @since 5.9.0
	 *
	 * @return array List of link relations.
	 */
	protected function get_available_actions() {
		$rels = array();

		$post_type = get_post_type_object( 'wp_global_styles' );
		if ( current_user_can( $post_type->cap->publish_posts ) ) {
			$rels[] = 'https://api.w.org/action-publish';
		}

		return $rels;
	}

	/**
	 * Overwrites the default protected title format.
	 *
	 * By default, WordPress will show password protected posts with a title of
	 * "Protected: %s", as the REST API communicates the protected status of a post
	 * in a machine readable format, we remove the "Protected: " prefix.
	 *
	 * @since 5.9.0
	 *
	 * @return string Protected title format.
	 */
	public function protected_title_format() {
		return '%s';
	}

	/**
	 * Retrieves the query params for the global styles collection.
	 *
	 * @since 5.9.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array();
	}

	/**
	 * Retrieves the global styles type' schema, conforming to JSON Schema.
	 *
	 * @since 5.9.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'wp_global_styles',
			'type'       => 'object',
			'properties' => array(
				'id'       => array(
					'description' => __( 'ID of global styles config.' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'styles'   => array(
					'description' => __( 'Global styles.' ),
					'type'        => array( 'object' ),
					'context'     => array( 'view', 'edit' ),
				),
				'settings' => array(
					'description' => __( 'Global settings.' ),
					'type'        => array( 'object' ),
					'context'     => array( 'view', 'edit' ),
				),
				'title'    => array(
					'description' => __( 'Title of the global styles variation.' ),
					'type'        => array( 'object', 'string' ),
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
					'properties'  => array(
						'raw'      => array(
							'description' => __( 'Title for the global styles variation, as it exists in the database.' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
						),
						'rendered' => array(
							'description' => __( 'HTML title for the post, transformed for display.' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
						),
					),
				),
			),
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Checks if a given request has access to read a single theme global styles config.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access for the item, WP_Error object otherwise.
	 */
	public function get_theme_item_permissions_check( $request ) {
		return $this->permissions_check( $request );
	}

	/**
	 * Returns the given theme global styles config.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request The request instance.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_theme_item( $request ) {
		if ( wp_get_theme()->get_stylesheet() !== $request['stylesheet'] ) {
			// This endpoint only supports the active theme for now.
			return new WP_Error(
				'rest_theme_not_found',
				__( 'Theme not found.' ),
				array( 'status' => 404 )
			);
		}

		$theme    = WP_Theme_JSON_Resolver::get_merged_data( 'theme' );
		$styles   = $theme->get_raw_data()['styles'];
		$settings = $theme->get_settings();
		$result   = array(
			'settings' => $settings,
			'styles'   => $styles,
		);
		$response = rest_ensure_response( $result );

		return $response;
	}
}
