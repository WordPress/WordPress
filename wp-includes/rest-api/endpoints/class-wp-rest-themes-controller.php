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
		if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error( 'rest_user_cannot_view', __( 'Sorry, you are not allowed to view themes.' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
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
			$formats                           = get_theme_support( 'post-formats' );
			$formats                           = is_array( $formats ) ? array_values( $formats[0] ) : array();
			$formats                           = array_merge( array( 'standard' ), $formats );
			$data['theme_supports']['formats'] = $formats;

			$data['theme_supports']['post-thumbnails']   = false;
			$data['theme_supports']['responsive-embeds'] = (bool) get_theme_support( 'responsive-embeds' );
			$post_thumbnails                             = get_theme_support( 'post-thumbnails' );

			if ( $post_thumbnails ) {
				// $post_thumbnails can contain a nested array of post types.
				// e.g. array( array( 'post', 'page' ) ).
				$data['theme_supports']['post-thumbnails'] = is_array( $post_thumbnails ) ? $post_thumbnails[0] : true;
			}
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
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'theme',
			'type'       => 'object',
			'properties' => array(
				'theme_supports' => array(
					'description' => __( 'Features supported by this theme.' ),
					'type'        => 'array',
					'readonly'    => true,
					'properties'  => array(
						'formats'           => array(
							'description' => __( 'Post formats supported.' ),
							'type'        => 'array',
							'readonly'    => true,
						),
						'post-thumbnails'   => array(
							'description' => __( 'Whether the theme supports post thumbnails.' ),
							'type'        => array( 'array', 'bool' ),
							'readonly'    => true,
						),
						'responsive-embeds' => array(
							'description' => __( 'Whether the theme supports responsive embedded content.' ),
							'type'        => 'bool',
							'readonly'    => true,
						),
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
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
		 * @param array        $query_params JSON Schema-formatted collection parameters.
		 */
		return apply_filters( 'rest_themes_collection_params', $query_params );
	}

	/**
	 * Sanitizes and validates the list of theme status.
	 *
	 * @since 5.0.0
	 *
	 * @param  string|array    $statuses  One or more theme statuses.
	 * @param  WP_REST_Request $request   Full details about the request.
	 * @param  string          $parameter Additional parameter to pass to validation.
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
