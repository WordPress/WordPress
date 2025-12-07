<?php
/**
 * REST API: Bundle WP_Theme_JSON class instead of inheriting per WordPress version class
 *
 * Changes to this class should be synced to the corresponding class
 * in WordPress core: src/wp-includes/rest-api/endpoints/class-wp-rest-global-styles-controller.php.
 *
 * @package    gutenberg
 * @subpackage REST_API
 * @since 5.9.0
 */

/**
 * Base Global Styles REST API Controller.
 */
class WP_REST_Global_Styles_Controller_Gutenberg extends WP_REST_Posts_Controller {

	/**
	 * Whether the controller supports batching.
	 *
	 * @since 6.6.0
	 * @var array
	 */
	protected $allow_batch = array( 'v1' => false );

	/**
	 * Constructor.
	 *
	 * @since 5.9.0
	 */
	/**
	 * Constructor.
	 *
	 * @since 6.6.0
	 *
	 * @param string $post_type Post type.
	 */
	public function __construct( $post_type = 'wp_global_styles' ) {
		parent::__construct( $post_type );
	}

	/**
	 * Registers the controllers routes.
	 *
	 * @since 5.9.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/themes/(?P<stylesheet>[\/\s%\w\.\(\)\[\]\@_\-]+)/variations',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_theme_items' ),
					'permission_callback' => array( $this, 'get_theme_items_permissions_check' ),
					'args'                => array(
						'stylesheet' => array(
							'description' => __( 'The theme identifier', 'gutenberg' ),
							'type'        => 'string',
						),
					),
					'allow_batch'         => $this->allow_batch,
				),
			),
			/*
			 * $override is set to true to avoid conflicts with the core endpoint.
			 * Do not sync to WordPress core.
			 */
			true
		);

		// List themes global styles.
		register_rest_route(
			$this->namespace,
			// The route.
			sprintf(
				'/%s/themes/(?P<stylesheet>%s)',
				$this->rest_base,
				/*
				 * Matches theme's directory: `/themes/<subdirectory>/<theme>/` or `/themes/<theme>/`.
				 * Excludes invalid directory name characters: `/:<>*?"|`.
				 */
				'[^\/:<>\*\?"\|]+(?:\/[^\/:<>\*\?"\|]+)?'
			),
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_theme_item' ),
					'permission_callback' => array( $this, 'get_theme_item_permissions_check' ),
					'args'                => array(
						'stylesheet' => array(
							'description'       => __( 'The theme identifier', 'gutenberg' ),
							'type'              => 'string',
							'sanitize_callback' => array( $this, '_sanitize_global_styles_callback' ),
						),
					),
					'allow_batch'         => $this->allow_batch,
				),
			),
			/*
			 * $override is set to true to avoid conflicts with the core endpoint.
			 * Do not sync to WordPress core.
			 */
			true
		);

		// Lists/updates a single global style variation based on the given id.
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
							'description'       => __( 'The id of a template', 'gutenberg' ),
							'type'              => 'string',
							'sanitize_callback' => array( $this, '_sanitize_global_styles_callback' ),
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				'schema'      => array( $this, 'get_public_item_schema' ),
				'allow_batch' => $this->allow_batch,
			),
			/*
			 * $override is set to true to avoid conflicts with the core endpoint.
			 * Do not sync to WordPress core.
			 */
			true
		);
	}

	/**
	 * Sanitize the global styles ID or stylesheet to decode endpoint.
	 * For example, `wp/v2/global-styles/twentytwentytwo%200.4.0`
	 * would be decoded to `twentytwentytwo 0.4.0`.
	 *
	 * @since 5.9.0
	 *
	 * @param string $id_or_stylesheet Global styles ID or stylesheet.
	 * @return string Sanitized global styles ID or stylesheet.
	 */
	public function _sanitize_global_styles_callback( $id_or_stylesheet ) {
		return urldecode( $id_or_stylesheet );
	}

	/**
	 * Get the post, if the ID is valid.
	 *
	 * @since 5.9.0
	 *
	 * @param int $id Supplied ID.
	 * @return WP_Post|WP_Error Post object if ID is valid, WP_Error otherwise.
	 */
	protected function get_post( $id ) {
		$error = new WP_Error(
			'rest_global_styles_not_found',
			__( 'No global styles config exist with that id.', 'gutenberg' ),
			array( 'status' => 404 )
		);

		$id = (int) $id;
		if ( $id <= 0 ) {
			return $error;
		}

		$post = get_post( $id );
		if ( empty( $post ) || empty( $post->ID ) || $this->post_type !== $post->post_type ) {
			return $error;
		}

		return $post;
	}

	/**
	 * Checks if a given request has access to read a single global style.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$post = $this->get_post( $request['id'] );
		if ( is_wp_error( $post ) ) {
			return $post;
		}

		if ( 'edit' === $request['context'] && $post && ! $this->check_update_permission( $post ) ) {
			return new WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to edit this global style.', 'gutenberg' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! $this->check_read_permission( $post ) ) {
			return new WP_Error(
				'rest_cannot_view',
				__( 'Sorry, you are not allowed to view this global style.', 'gutenberg' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a global style can be read.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_Post $post Post object.
	 * @return bool Whether the post can be read.
	 */
	public function check_read_permission( $post ) {
		return current_user_can( 'read_post', $post->ID );
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
		$post = $this->get_post( $request['id'] );
		if ( is_wp_error( $post ) ) {
			return $post;
		}

		if ( $post && ! $this->check_update_permission( $post ) ) {
			return new WP_Error(
				'rest_cannot_edit',
				__( 'Sorry, you are not allowed to edit this global style.', 'gutenberg' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Prepares a single global styles config for update.
	 *
	 * @since 5.9.0
	 * @since 6.2.0 Added validation of styles.css property.
	 * @since 6.6.0 Added registration of block style variations from theme.json sources (theme.json, user theme.json, partials).
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return stdClass|WP_Error Prepared item on success. WP_Error on when the custom CSS is not valid.
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
				if ( isset( $request['styles']['css'] ) ) {
					$css_validation_result = $this->validate_custom_css( $request['styles']['css'] );
					if ( is_wp_error( $css_validation_result ) ) {
						return $css_validation_result;
					}
				}
				$config['styles'] = $request['styles'];
			} elseif ( isset( $existing_config['styles'] ) ) {
				$config['styles'] = $existing_config['styles'];
			}

			// Register theme-defined variations e.g. from block style variation partials under `/styles`.
			$variations = WP_Theme_JSON_Resolver_Gutenberg::get_style_variations( 'block' );
			gutenberg_register_block_style_variations_from_theme_json_partials( $variations );

			if ( isset( $request['settings'] ) ) {
				$config['settings'] = $request['settings'];
			} elseif ( isset( $existing_config['settings'] ) ) {
				$config['settings'] = $existing_config['settings'];
			}
			$config['isGlobalStylesUserThemeJSON'] = true;
			$config['version']                     = WP_Theme_JSON_Gutenberg::LATEST_SCHEMA;
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
	 * @since 6.2.0 Handling of style.css was added to WP_Theme_JSON.
	 * @since 6.6.0 Added custom relative theme file URIs to `_links`.
	 *
	 * @param WP_Post         $post    Global Styles post object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $post, $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$raw_config                       = json_decode( $post->post_content, true );
		$is_global_styles_user_theme_json = isset( $raw_config['isGlobalStylesUserThemeJSON'] ) && true === $raw_config['isGlobalStylesUserThemeJSON'];
		$config                           = array();
		$theme_json                       = null;
		if ( $is_global_styles_user_theme_json ) {
			$theme_json = new WP_Theme_JSON_Gutenberg( $raw_config, 'custom' );
			$config     = $theme_json->get_raw_data();
		}

		// Base fields for every post.
		$data   = array();
		$fields = $this->get_fields_for_response( $request );

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
			add_filter( 'private_title_format', array( $this, 'protected_title_format' ) );

			$data['title']['rendered'] = get_the_title( $post->ID );

			remove_filter( 'protected_title_format', array( $this, 'protected_title_format' ) );
			remove_filter( 'private_title_format', array( $this, 'protected_title_format' ) );
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

		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$links = $this->prepare_links( $post->ID );
			// Only return resolved URIs for get requests to user theme JSON.
			if ( $theme_json ) {
				$resolved_theme_uris = WP_Theme_JSON_Resolver_Gutenberg::get_resolved_theme_uris( $theme_json );
				if ( ! empty( $resolved_theme_uris ) ) {
					$links['https://api.w.org/theme-file'] = $resolved_theme_uris;
				}
			}
			$response->add_links( $links );
			if ( ! empty( $links['self']['href'] ) ) {
				$actions = $this->get_available_actions( $post, $request );
				$self    = $links['self']['href'];
				foreach ( $actions as $rel ) {
					$response->add_link( $rel, $self );
				}
			}
		}

		return $response;
	}

	/**
	 * Prepares links for the request.
	 *
	 * @since 5.9.0
	 * @since 6.3.0 Adds revisions count and rest URL href to version-history.
	 *
	 * @param integer $id ID.
	 * @return array Links for the given post.
	 */
	protected function prepare_links( $id ) {
		$base = sprintf( '%s/%s', $this->namespace, $this->rest_base );

		$links = array(
			'self'  => array(
				'href' => rest_url( trailingslashit( $base ) . $id ),
			),
			'about' => array(
				'href' => rest_url( 'wp/v2/types/' . $this->post_type ),
			),
		);

		if ( post_type_supports( $this->post_type, 'revisions' ) ) {
			$revisions                = wp_get_latest_revision_id_and_total_count( $id );
			$revisions_count          = ! is_wp_error( $revisions ) ? $revisions['count'] : 0;
			$revisions_base           = sprintf( '/%s/%d/revisions', $base, $id );
			$links['version-history'] = array(
				'href'  => rest_url( $revisions_base ),
				'count' => $revisions_count,
			);
		}

		return $links;
	}

	/**
	 * Get the link relations available for the post and current user.
	 *
	 * @since 5.9.0
	 * @since 6.2.0 Added 'edit-css' action.
	 * @since 6.6.0 Added $post and $request parameters.
	 *
	 * @param WP_Post         $post    Post object.
	 * @param WP_REST_Request $request Request object.
	 * @return array List of link relations.
	 */
	protected function get_available_actions( $post, $request ) {  // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$rels = array();

		$post_type = get_post_type_object( $post->post_type );
		if ( current_user_can( $post_type->cap->publish_posts ) ) {
			$rels[] = 'https://api.w.org/action-publish';
		}

		if ( current_user_can( 'edit_css' ) ) {
			$rels[] = 'https://api.w.org/action-edit-css';
		}

		return $rels;
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
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'id'       => array(
					'description' => __( 'ID of global styles config.', 'gutenberg' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
				),
				'styles'   => array(
					'description' => __( 'Global styles.', 'gutenberg' ),
					'type'        => array( 'object' ),
					'context'     => array( 'view', 'edit' ),
				),
				'settings' => array(
					'description' => __( 'Global settings.', 'gutenberg' ),
					'type'        => array( 'object' ),
					'context'     => array( 'view', 'edit' ),
				),
				'title'    => array(
					'description' => __( 'Title of the global styles variation.', 'gutenberg' ),
					'type'        => array( 'object', 'string' ),
					'default'     => '',
					'context'     => array( 'embed', 'view', 'edit' ),
					'properties'  => array(
						'raw'      => array(
							'description' => __( 'Title for the global styles variation, as it exists in the database.', 'gutenberg' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
						),
						'rendered' => array(
							'description' => __( 'HTML title for the post, transformed for display.', 'gutenberg' ),
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
	 * @since 6.7.0 Allow users with edit post capabilities to view theme global styles.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access for the item, WP_Error object otherwise.
	 */
	public function get_theme_item_permissions_check( $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		/*
		 * Verify if the current user has edit_posts capability.
		 */
		if ( current_user_can( 'edit_posts' ) ) {
			return true;
		}

		foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
			if ( current_user_can( $post_type->cap->edit_posts ) ) {
				return true;
			}
		}

		/*
		 * Verify if the current user has edit_theme_options capability.
		 */
		if ( current_user_can( 'edit_theme_options' ) ) {
			return true;
		}

		return new WP_Error(
			'rest_cannot_read_global_styles',
			__( 'Sorry, you are not allowed to access the global styles on this site.', 'gutenberg' ),
			array(
				'status' => rest_authorization_required_code(),
			)
		);
	}

	/**
	 * Returns the given theme global styles config.
	 *
	 * @since 5.9.0
	 *
	 * @param WP_REST_Request $request The request instance.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_theme_item( $request ) {
		if ( get_stylesheet() !== $request['stylesheet'] ) {
			// This endpoint only supports the active theme for now.
			return new WP_Error(
				'rest_theme_not_found',
				__( 'Theme not found.', 'gutenberg' ),
				array( 'status' => 404 )
			);
		}

		$theme  = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data( 'theme' );
		$fields = $this->get_fields_for_response( $request );
		$data   = array();

		if ( rest_is_field_included( 'settings', $fields ) ) {
			$data['settings'] = $theme->get_settings();
		}

		if ( rest_is_field_included( 'styles', $fields ) ) {
			$raw_data       = $theme->get_raw_data();
			$data['styles'] = isset( $raw_data['styles'] ) ? $raw_data['styles'] : array();
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );

		if ( rest_is_field_included( '_links', $fields ) || rest_is_field_included( '_embedded', $fields ) ) {
			$links               = array(
				'self' => array(
					'href' => rest_url( sprintf( '%s/%s/themes/%s', $this->namespace, $this->rest_base, $request['stylesheet'] ) ),
				),
			);
			$resolved_theme_uris = WP_Theme_JSON_Resolver_Gutenberg::get_resolved_theme_uris( $theme );
			if ( ! empty( $resolved_theme_uris ) ) {
				$links['https://api.w.org/theme-file'] = $resolved_theme_uris;
			}

			$response->add_links( $links );
		}

		return $response;
	}

	/**
	 * Checks if a given request has access to read a single theme global styles config.
	 *
	 * @since 6.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access for the item, WP_Error object otherwise.
	 */
	public function get_theme_items_permissions_check( $request ) {
		return $this->get_theme_item_permissions_check( $request );
	}

	/**
	 * Returns the given theme global styles variations.
	 *
	 * @since 6.0.0
	 * @since 6.2.0 Returns parent theme variations, if they exist.
	 * @since 6.4.0 Removed unnecessary local variable.
	 * @since 6.6.0 Added custom relative theme file URIs to `_links` for each item.
	 *
	 * @param WP_REST_Request $request The request instance.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_theme_items( $request ) {
		if ( get_stylesheet() !== $request['stylesheet'] ) {
			// This endpoint only supports the active theme for now.
			return new WP_Error(
				'rest_theme_not_found',
				__( 'Theme not found.', 'gutenberg' ),
				array( 'status' => 404 )
			);
		}

		$response = array();

		// Register theme-defined variations e.g. from block style variation partials under `/styles`.
		$partials = WP_Theme_JSON_Resolver_Gutenberg::get_style_variations( 'block' );
		gutenberg_register_block_style_variations_from_theme_json_partials( $partials );

		$variations = WP_Theme_JSON_Resolver_Gutenberg::get_style_variations();

		// Add resolved theme asset links.
		foreach ( $variations as $variation ) {
			$variation_theme_json = new WP_Theme_JSON_Gutenberg( $variation );
			$resolved_theme_uris  = WP_Theme_JSON_Resolver_Gutenberg::get_resolved_theme_uris( $variation_theme_json );
			$data                 = rest_ensure_response( $variation );
			if ( ! empty( $resolved_theme_uris ) ) {
				$data->add_links(
					array(
						'https://api.w.org/theme-file' => $resolved_theme_uris,
					)
				);
			}
			$response[] = $this->prepare_response_for_collection( $data );
		}

		return rest_ensure_response( $response );
	}

	/**
	 * Validate style.css as valid CSS.
	 *
	 * Currently just checks for invalid markup.
	 *
	 * @since 6.2.0
	 * @since 6.4.0 Changed method visibility to protected.
	 *
	 * @param string $css CSS to validate.
	 * @return true|WP_Error True if the input was validated, otherwise WP_Error.
	 */
	protected function validate_custom_css( $css ) {
		if ( preg_match( '#</?\w+#', $css ) ) {
			return new WP_Error(
				'rest_custom_css_illegal_markup',
				__( 'Markup is not allowed in CSS.', 'gutenberg' ),
				array( 'status' => 400 )
			);
		}
		return true;
	}
}
