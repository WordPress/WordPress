<?php
/**
 * REST API: WP_REST_Themes_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 5.0.0
 */

/**
 * Core class used to manage themes via the REST API.
 *
 * @since 5.0.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Themes_Controller extends WP_REST_Controller {

	/**
	 * Constructor.
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'themes';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 5.0.0
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
				'schema' => array( $this, 'get_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to read the theme.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access for the item, otherwise WP_Error object.
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
			'rest_user_cannot_view',
			__( 'Sorry, you are not allowed to view themes.' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Retrieves a collection of themes.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();
		$themes     = array();

		if ( isset( $registered['status'], $request['status'] ) && in_array( 'active', $request['status'], true ) ) {
			$active_theme = wp_get_theme();
			$active_theme = $this->prepare_item_for_response( $active_theme, $request );
			$themes[]     = $this->prepare_response_for_collection( $active_theme );
		}

		$response = rest_ensure_response( $themes );

		$response->header( 'X-WP-Total', count( $themes ) );
		$response->header( 'X-WP-TotalPages', count( $themes ) );

		return $response;
	}

	/**
	 * Prepares a single theme output for response.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_Theme        $theme   Theme object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $theme, $request ) {
		$data   = array();
		$fields = $this->get_fields_for_response( $request );

		if ( in_array( 'theme_supports', $fields, true ) ) {
			$item_schemas   = $this->get_item_schema();
			$theme_supports = $item_schemas['properties']['theme_supports']['properties'];
			foreach ( $theme_supports as $name => $schema ) {
				if ( 'formats' === $name ) {
					continue;
				}

				if ( ! current_theme_supports( $name ) ) {
					$data['theme_supports'][ $name ] = false;
					continue;
				}

				if ( 'boolean' === $schema['type'] ) {
					$data['theme_supports'][ $name ] = true;
					continue;
				}

				$support = get_theme_support( $name );

				if ( is_array( $support ) ) {
					// None of the Core theme supports have variadic args.
					$support = $support[0];

					// Core multi-type theme-support schema definitions always list boolean first.
					if ( is_array( $schema['type'] ) && 'boolean' === $schema['type'][0] ) {
						// Pass the non-boolean type through to the sanitizer, which cannot itself
						// determine the intended type if the value is invalid (for example if an
						// object includes non-safelisted properties).
						$schema['type'] = $schema['type'][1];
					}
				}

				$data['theme_supports'][ $name ] = rest_sanitize_value_from_schema( $support, $schema );
			}

			$formats = get_theme_support( 'post-formats' );
			$formats = is_array( $formats ) ? array_values( $formats[0] ) : array();
			$formats = array_merge( array( 'standard' ), $formats );

			$data['theme_supports']['formats'] = $formats;
		}

		$data = $this->add_additional_fields_to_object( $data, $request );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		/**
		 * Filters theme data returned from the REST API.
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WP_Theme         $theme    Theme object used to create response.
		 * @param WP_REST_Request  $request  Request object.
		 */
		return apply_filters( 'rest_prepare_theme', $response, $theme, $request );
	}

	/**
	 * Retrieves the theme's schema, conforming to JSON Schema.
	 *
	 * @since 5.0.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'theme',
			'type'       => 'object',
			'properties' => array(
				'theme_supports' => array(
					'description' => __( 'Features supported by this theme.' ),
					'type'        => 'object',
					'readonly'    => true,
					'properties'  => array(
						'align-wide'                => array(
							'description' => __( 'Whether theme opts in to wide alignment CSS class.' ),
							'type'        => 'boolean',
						),
						'automatic-feed-links'      => array(
							'description' => __( 'Whether posts and comments RSS feed links are added to head.' ),
							'type'        => 'boolean',
						),
						'custom-header'             => array(
							'description'          => __( 'Custom header if defined by the theme.' ),
							'type'                 => array( 'boolean', 'object' ),
							'properties'           => array(
								'default-image'      => array(
									'type'   => 'string',
									'format' => 'uri',
								),
								'random-default'     => array(
									'type' => 'boolean',
								),
								'width'              => array(
									'type' => 'integer',
								),
								'height'             => array(
									'type' => 'integer',
								),
								'flex-height'        => array(
									'type' => 'boolean',
								),
								'flex-width'         => array(
									'type' => 'boolean',
								),
								'default-text-color' => array(
									'type' => 'string',
								),
								'header-text'        => array(
									'type' => 'boolean',
								),
								'uploads'            => array(
									'type' => 'boolean',
								),
								'video'              => array(
									'type' => 'boolean',
								),
							),
							'additionalProperties' => false,
						),
						'custom-background'         => array(
							'description'          => __( 'Custom background if defined by the theme.' ),
							'type'                 => array( 'boolean', 'object' ),
							'properties'           => array(
								'default-image'      => array(
									'type'   => 'string',
									'format' => 'uri',
								),
								'default-preset'     => array(
									'type' => 'string',
									'enum' => array(
										'default',
										'fill',
										'fit',
										'repeat',
										'custom',
									),
								),
								'default-position-x' => array(
									'type' => 'string',
									'enum' => array(
										'left',
										'center',
										'right',
									),
								),
								'default-position-y' => array(
									'type' => 'string',
									'enum' => array(
										'left',
										'center',
										'right',
									),
								),
								'default-size'       => array(
									'type' => 'string',
									'enum' => array(
										'auto',
										'contain',
										'cover',
									),
								),
								'default-repeat'     => array(
									'type' => 'string',
									'enum' => array(
										'repeat-x',
										'repeat-y',
										'repeat',
										'no-repeat',
									),
								),
								'default-attachment' => array(
									'type' => 'string',
									'enum' => array(
										'scroll',
										'fixed',
									),
								),
								'default-color'      => array(
									'type' => 'string',
								),
							),
							'additionalProperties' => false,
						),
						'custom-logo'               => array(
							'description'          => __( 'Custom logo if defined by the theme.' ),
							'type'                 => array( 'boolean', 'object' ),
							'properties'           => array(
								'width'       => array(
									'type' => 'integer',
								),
								'height'      => array(
									'type' => 'integer',
								),
								'flex-width'  => array(
									'type' => 'boolean',
								),
								'flex-height' => array(
									'type' => 'boolean',
								),
								'header-text' => array(
									'type'  => 'array',
									'items' => array(
										'type' => 'string',
									),
								),
							),
							'additionalProperties' => false,
						),
						'customize-selective-refresh-widgets' => array(
							'description' => __( 'Whether the theme enables Selective Refresh for Widgets being managed with the Customizer.' ),
							'type'        => 'boolean',
						),
						'dark-editor-style'         => array(
							'description' => __( 'Whether theme opts in to the dark editor style UI.' ),
							'type'        => 'boolean',
						),
						'disable-custom-colors'     => array(
							'description' => __( 'Whether the theme disables custom colors.' ),
							'type'        => 'boolean',
						),
						'disable-custom-font-sizes' => array(
							'description' => __( 'Whether the theme disables custom font sizes.' ),
							'type'        => 'boolean',
						),
						'disable-custom-gradients'  => array(
							'description' => __( 'Whether the theme disables custom gradients.' ),
							'type'        => 'boolean',
						),
						'editor-color-palette'      => array(
							'description' => __( 'Custom color palette if defined by the theme.' ),
							'type'        => array( 'boolean', 'array' ),
							'items'       => array(
								'type'                 => 'object',
								'properties'           => array(
									'name'  => array(
										'type' => 'string',
									),
									'slug'  => array(
										'type' => 'string',
									),
									'color' => array(
										'type' => 'string',
									),
								),
								'additionalProperties' => false,
							),
						),
						'editor-font-sizes'         => array(
							'description' => __( 'Custom font sizes if defined by the theme.' ),
							'type'        => array( 'boolean', 'array' ),
							'items'       => array(
								'type'                 => 'object',
								'properties'           => array(
									'name' => array(
										'type' => 'string',
									),
									'size' => array(
										'type' => 'number',
									),
									'slug' => array(
										'type' => 'string',
									),
								),
								'additionalProperties' => false,
							),
						),
						'editor-gradient-presets'   => array(
							'description' => __( 'Custom gradient presets if defined by the theme.' ),
							'type'        => array( 'boolean', 'array' ),
							'items'       => array(
								'type'                 => 'object',
								'properties'           => array(
									'name'     => array(
										'type' => 'string',
									),
									'gradient' => array(
										'type' => 'string',
									),
									'slug'     => array(
										'type' => 'string',
									),
								),
								'additionalProperties' => false,
							),
						),
						'editor-styles'             => array(
							'description' => __( 'Whether theme opts in to the editor styles CSS wrapper.' ),
							'type'        => 'boolean',
						),
						'formats'                   => array(
							'description' => __( 'Post formats supported.' ),
							'type'        => 'array',
							'items'       => array(
								'type' => 'string',
								'enum' => get_post_format_slugs(),
							),
						),
						'html5'                     => array(
							'description' => __( 'Allows use of html5 markup for search forms, comment forms, comment lists, gallery, and caption.' ),
							'type'        => array( 'boolean', 'array' ),
							'items'       => array(
								'type' => 'string',
								'enum' => array(
									'search-form',
									'comment-form',
									'comment-list',
									'gallery',
									'caption',
									'script',
									'style',
								),
							),
						),
						'post-thumbnails'           => array(
							'description' => __( 'Whether the theme supports post thumbnails.' ),
							'type'        => array( 'boolean', 'array' ),
							'items'       => array(
								'type' => 'string',
							),
						),
						'responsive-embeds'         => array(
							'description' => __( 'Whether the theme supports responsive embedded content.' ),
							'type'        => 'boolean',
						),
						'title-tag'                 => array(
							'description' => __( 'Whether the theme can manage the document title tag.' ),
							'type'        => 'boolean',
						),
						'wp-block-styles'           => array(
							'description' => __( 'Whether theme opts in to default WordPress block styles for viewing.' ),
							'type'        => 'boolean',
						),
					),
				),
			),
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the search params for the themes collection.
	 *
	 * @since 5.0.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();

		$query_params['status'] = array(
			'description'       => __( 'Limit result set to themes assigned one or more statuses.' ),
			'type'              => 'array',
			'items'             => array(
				'enum' => array( 'active' ),
				'type' => 'string',
			),
			'required'          => true,
			'sanitize_callback' => array( $this, 'sanitize_theme_status' ),
		);

		/**
		 * Filter collection parameters for the themes controller.
		 *
		 * @since 5.0.0
		 *
		 * @param array $query_params JSON Schema-formatted collection parameters.
		 */
		return apply_filters( 'rest_themes_collection_params', $query_params );
	}

	/**
	 * Sanitizes and validates the list of theme status.
	 *
	 * @since 5.0.0
	 *
	 * @param string|array    $statuses  One or more theme statuses.
	 * @param WP_REST_Request $request   Full details about the request.
	 * @param string          $parameter Additional parameter to pass to validation.
	 * @return array|WP_Error A list of valid statuses, otherwise WP_Error object.
	 */
	public function sanitize_theme_status( $statuses, $request, $parameter ) {
		$statuses = wp_parse_slug_list( $statuses );

		foreach ( $statuses as $status ) {
			$result = rest_validate_request_arg( $status, $request, $parameter );

			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		return $statuses;
	}
}
