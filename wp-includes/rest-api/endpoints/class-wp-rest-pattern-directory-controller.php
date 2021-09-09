<?php
/**
 * Block Pattern Directory REST API: WP_REST_Pattern_Directory_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 5.8.0
 */

/**
 * Controller which provides REST endpoint for block patterns.
 *
 * This simply proxies the endpoint at http://api.wordpress.org/patterns/1.0/. That isn't necessary for
 * functionality, but is desired for privacy. It prevents api.wordpress.org from knowing the user's IP address.
 *
 * @since 5.8.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Pattern_Directory_Controller extends WP_REST_Controller {

	/**
	 * Constructs the controller.
	 *
	 * @since 5.8.0
	 */
	public function __construct() {
		$this->namespace     = 'wp/v2';
			$this->rest_base = 'pattern-directory';
	}

	/**
	 * Registers the necessary REST API routes.
	 *
	 * @since 5.8.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/patterns',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks whether a given request has permission to view the local pattern directory.
	 *
	 * @since 5.8.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has permission, WP_Error object otherwise.
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
			'rest_pattern_directory_cannot_view',
			__( 'Sorry, you are not allowed to browse the local block pattern directory.' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Search and retrieve block patterns metadata
	 *
	 * @since 5.8.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		/*
		 * Include an unmodified `$wp_version`, so the API can craft a response that's tailored to
		 * it. Some plugins modify the version in a misguided attempt to improve security by
		 * obscuring the version, which can cause invalid requests.
		 */
		require ABSPATH . WPINC . '/version.php';

		$query_args = array(
			'locale'     => get_user_locale(),
			'wp-version' => $wp_version,
		);

		$category_id = $request['category'];
		$keyword_id  = $request['keyword'];
		$search_term = $request['search'];

		if ( $category_id ) {
			$query_args['pattern-categories'] = $category_id;
		}

		if ( $keyword_id ) {
			$query_args['pattern-keywords'] = $keyword_id;
		}

		if ( $search_term ) {
			$query_args['search'] = $search_term;
		}

		/*
		 * Include a hash of the query args, so that different requests are stored in
		 * separate caches.
		 *
		 * MD5 is chosen for its speed, low-collision rate, universal availability, and to stay
		 * under the character limit for `_site_transient_timeout_{...}` keys.
		 *
		 * @link https://stackoverflow.com/questions/3665247/fastest-hash-for-non-cryptographic-uses
		 */
		$transient_key = 'wp_remote_block_patterns_' . md5( implode( '-', $query_args ) );

		/*
		 * Use network-wide transient to improve performance. The locale is the only site
		 * configuration that affects the response, and it's included in the transient key.
		 */
		$raw_patterns = get_site_transient( $transient_key );

		if ( ! $raw_patterns ) {
			$api_url = add_query_arg(
				array_map( 'rawurlencode', $query_args ),
				'http://api.wordpress.org/patterns/1.0/'
			);

			if ( wp_http_supports( array( 'ssl' ) ) ) {
				$api_url = set_url_scheme( $api_url, 'https' );
			}

			/*
			 * Default to a short TTL, to mitigate cache stampedes on high-traffic sites.
			 * This assumes that most errors will be short-lived, e.g., packet loss that causes the
			 * first request to fail, but a follow-up one will succeed. The value should be high
			 * enough to avoid stampedes, but low enough to not interfere with users manually
			 * re-trying a failed request.
			 */
			$cache_ttl      = 5;
			$wporg_response = wp_remote_get( $api_url );
			$raw_patterns   = json_decode( wp_remote_retrieve_body( $wporg_response ) );

			if ( is_wp_error( $wporg_response ) ) {
				$raw_patterns = $wporg_response;

			} elseif ( ! is_array( $raw_patterns ) ) {
				// HTTP request succeeded, but response data is invalid.
				$raw_patterns = new WP_Error(
					'pattern_api_failed',
					sprintf(
					/* translators: %s: Support forums URL. */
						__( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.' ),
						__( 'https://wordpress.org/support/forums/' )
					),
					array(
						'response' => wp_remote_retrieve_body( $wporg_response ),
					)
				);

			} else {
				// Response has valid data.
				$cache_ttl = HOUR_IN_SECONDS;
			}

			set_site_transient( $transient_key, $raw_patterns, $cache_ttl );
		}

		if ( is_wp_error( $raw_patterns ) ) {
			$raw_patterns->add_data( array( 'status' => 500 ) );

			return $raw_patterns;
		}

		$response = array();

		if ( $raw_patterns ) {
			foreach ( $raw_patterns as $pattern ) {
				$response[] = $this->prepare_response_for_collection(
					$this->prepare_item_for_response( $pattern, $request )
				);
			}
		}

		return new WP_REST_Response( $response );
	}

	/**
	 * Prepare a raw pattern before it's output in an API response.
	 *
	 * @since 5.8.0
	 * @since 5.9.0 Renamed `$raw_pattern` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param object          $item    Raw pattern from api.wordpress.org, before any changes.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $item, $request ) {
		// Restores the more descriptive, specific name for use within this method.
		$raw_pattern      = $item;
		$prepared_pattern = array(
			'id'             => absint( $raw_pattern->id ),
			'title'          => sanitize_text_field( $raw_pattern->title->rendered ),
			'content'        => wp_kses_post( $raw_pattern->pattern_content ),
			'categories'     => array_map( 'sanitize_title', $raw_pattern->category_slugs ),
			'keywords'       => array_map( 'sanitize_title', $raw_pattern->keyword_slugs ),
			'description'    => sanitize_text_field( $raw_pattern->meta->wpop_description ),
			'viewport_width' => absint( $raw_pattern->meta->wpop_viewport_width ),
		);

		$prepared_pattern = $this->add_additional_fields_to_object( $prepared_pattern, $request );

		$response = new WP_REST_Response( $prepared_pattern );

		/**
		 * Filters the REST API response for a pattern.
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Response $response    The response object.
		 * @param object           $raw_pattern The unprepared pattern.
		 * @param WP_REST_Request  $request     The request object.
		 */
		return apply_filters( 'rest_prepare_block_pattern', $response, $raw_pattern, $request );
	}

	/**
	 * Retrieves the pattern's schema, conforming to JSON Schema.
	 *
	 * @since 5.8.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'pattern-directory-item',
			'type'       => 'object',
			'properties' => array(
				'id'             => array(
					'description' => __( 'The pattern ID.' ),
					'type'        => 'integer',
					'minimum'     => 1,
					'context'     => array( 'view', 'embed' ),
				),

				'title'          => array(
					'description' => __( 'The pattern title, in human readable format.' ),
					'type'        => 'string',
					'minLength'   => 1,
					'context'     => array( 'view', 'embed' ),
				),

				'content'        => array(
					'description' => __( 'The pattern content.' ),
					'type'        => 'string',
					'minLength'   => 1,
					'context'     => array( 'view', 'embed' ),
				),

				'categories'     => array(
					'description' => __( "The pattern's category slugs." ),
					'type'        => 'array',
					'uniqueItems' => true,
					'items'       => array( 'type' => 'string' ),
					'context'     => array( 'view', 'embed' ),
				),

				'keywords'       => array(
					'description' => __( "The pattern's keyword slugs." ),
					'type'        => 'array',
					'uniqueItems' => true,
					'items'       => array( 'type' => 'string' ),
					'context'     => array( 'view', 'embed' ),
				),

				'description'    => array(
					'description' => __( 'A description of the pattern.' ),
					'type'        => 'string',
					'minLength'   => 1,
					'context'     => array( 'view', 'embed' ),
				),

				'viewport_width' => array(
					'description' => __( 'The preferred width of the viewport when previewing a pattern, in pixels.' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the search params for the patterns collection.
	 *
	 * @since 5.8.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();

		// Pagination is not supported.
		unset( $query_params['page'] );
		unset( $query_params['per_page'] );

		$query_params['search']['minLength'] = 1;
		$query_params['context']['default']  = 'view';

		$query_params['category'] = array(
			'description' => __( 'Limit results to those matching a category ID.' ),
			'type'        => 'integer',
			'minimum'     => 1,
		);

		$query_params['keyword'] = array(
			'description' => __( 'Limit results to those matching a keyword ID.' ),
			'type'        => 'integer',
			'minimum'     => 1,
		);

		/**
		 * Filter collection parameters for the pattern directory controller.
		 *
		 * @since 5.8.0
		 *
		 * @param array $query_params JSON Schema-formatted collection parameters.
		 */
		return apply_filters( 'rest_pattern_directory_collection_params', $query_params );
	}
}
