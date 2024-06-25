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
		$this->namespace = 'wp/v2';
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
	 * Checks whether a given request has permission to view the local block pattern directory.
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
	 * @since 6.0.0 Added 'slug' to request.
	 * @since 6.2.0 Added 'per_page', 'page', 'offset', 'order', and 'orderby' to request.
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

		$valid_query_args = array(
			'offset'   => true,
			'order'    => true,
			'orderby'  => true,
			'page'     => true,
			'per_page' => true,
			'search'   => true,
			'slug'     => true,
		);
		$query_args       = array_intersect_key( $request->get_params(), $valid_query_args );

		$query_args['locale']             = get_user_locale();
		$query_args['wp-version']         = $wp_version;
		$query_args['pattern-categories'] = isset( $request['category'] ) ? $request['category'] : false;
		$query_args['pattern-keywords']   = isset( $request['keyword'] ) ? $request['keyword'] : false;

		$query_args = array_filter( $query_args );

		$transient_key = $this->get_transient_key( $query_args );

		/*
		 * Use network-wide transient to improve performance. The locale is the only site
		 * configuration that affects the response, and it's included in the transient key.
		 */
		$raw_patterns = get_site_transient( $transient_key );

		if ( ! $raw_patterns ) {
			$api_url = 'http://api.wordpress.org/patterns/1.0/?' . build_query( $query_args );
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
	 * Prepare a raw block pattern before it gets output in a REST API response.
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
		$raw_pattern = $item;

		$prepared_pattern = array(
			'id'             => absint( $raw_pattern->id ),
			'title'          => sanitize_text_field( $raw_pattern->title->rendered ),
			'content'        => wp_kses_post( $raw_pattern->pattern_content ),
			'categories'     => array_map( 'sanitize_title', $raw_pattern->category_slugs ),
			'keywords'       => array_map( 'sanitize_text_field', explode( ',', $raw_pattern->meta->wpop_keywords ) ),
			'description'    => sanitize_text_field( $raw_pattern->meta->wpop_description ),
			'viewport_width' => absint( $raw_pattern->meta->wpop_viewport_width ),
			'block_types'    => array_map( 'sanitize_text_field', $raw_pattern->meta->wpop_block_types ),
		);

		$prepared_pattern = $this->add_additional_fields_to_object( $prepared_pattern, $request );

		$response = new WP_REST_Response( $prepared_pattern );

		/**
		 * Filters the REST API response for a block pattern.
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Response $response    The response object.
		 * @param object           $raw_pattern The unprepared block pattern.
		 * @param WP_REST_Request  $request     The request object.
		 */
		return apply_filters( 'rest_prepare_block_pattern', $response, $raw_pattern, $request );
	}

	/**
	 * Retrieves the block pattern's schema, conforming to JSON Schema.
	 *
	 * @since 5.8.0
	 * @since 6.2.0 Added `'block_types'` to schema.
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
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'title'          => array(
					'description' => __( 'The pattern title, in human readable format.' ),
					'type'        => 'string',
					'minLength'   => 1,
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'content'        => array(
					'description' => __( 'The pattern content.' ),
					'type'        => 'string',
					'minLength'   => 1,
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'categories'     => array(
					'description' => __( "The pattern's category slugs." ),
					'type'        => 'array',
					'uniqueItems' => true,
					'items'       => array( 'type' => 'string' ),
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'keywords'       => array(
					'description' => __( "The pattern's keywords." ),
					'type'        => 'array',
					'uniqueItems' => true,
					'items'       => array( 'type' => 'string' ),
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'description'    => array(
					'description' => __( 'A description of the pattern.' ),
					'type'        => 'string',
					'minLength'   => 1,
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'viewport_width' => array(
					'description' => __( 'The preferred width of the viewport when previewing a pattern, in pixels.' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),

				'block_types'    => array(
					'description' => __( 'The block types which can use this pattern.' ),
					'type'        => 'array',
					'uniqueItems' => true,
					'items'       => array( 'type' => 'string' ),
					'context'     => array( 'view', 'embed' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the search parameters for the block pattern's collection.
	 *
	 * @since 5.8.0
	 * @since 6.2.0 Added 'per_page', 'page', 'offset', 'order', and 'orderby' to request.
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$query_params = parent::get_collection_params();

		$query_params['per_page']['default'] = 100;
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

		$query_params['slug'] = array(
			'description' => __( 'Limit results to those matching a pattern (slug).' ),
			'type'        => 'array',
		);

		$query_params['offset'] = array(
			'description' => __( 'Offset the result set by a specific number of items.' ),
			'type'        => 'integer',
		);

		$query_params['order'] = array(
			'description' => __( 'Order sort attribute ascending or descending.' ),
			'type'        => 'string',
			'default'     => 'desc',
			'enum'        => array( 'asc', 'desc' ),
		);

		$query_params['orderby'] = array(
			'description' => __( 'Sort collection by post attribute.' ),
			'type'        => 'string',
			'default'     => 'date',
			'enum'        => array(
				'author',
				'date',
				'id',
				'include',
				'modified',
				'parent',
				'relevance',
				'slug',
				'include_slugs',
				'title',
				'favorite_count',
			),
		);

		/**
		 * Filter collection parameters for the block pattern directory controller.
		 *
		 * @since 5.8.0
		 *
		 * @param array $query_params JSON Schema-formatted collection parameters.
		 */
		return apply_filters( 'rest_pattern_directory_collection_params', $query_params );
	}

	/*
	 * Include a hash of the query args, so that different requests are stored in
	 * separate caches.
	 *
	 * MD5 is chosen for its speed, low-collision rate, universal availability, and to stay
	 * under the character limit for `_site_transient_timeout_{...}` keys.
	 *
	 * @link https://stackoverflow.com/questions/3665247/fastest-hash-for-non-cryptographic-uses
	 *
	 * @since 6.0.0
	 *
	 * @param array $query_args Query arguments to generate a transient key from.
	 * @return string Transient key.
	 */
	protected function get_transient_key( $query_args ) {

		if ( isset( $query_args['slug'] ) ) {
			// This is an additional precaution because the "sort" function expects an array.
			$query_args['slug'] = wp_parse_list( $query_args['slug'] );

			// Empty arrays should not affect the transient key.
			if ( empty( $query_args['slug'] ) ) {
				unset( $query_args['slug'] );
			} else {
				// Sort the array so that the transient key doesn't depend on the order of slugs.
				sort( $query_args['slug'] );
			}
		}

		return 'wp_remote_block_patterns_' . md5( serialize( $query_args ) );
	}
}
