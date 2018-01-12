<?php
/**
 * Regenerate Thumbnails: REST API controller class
 *
 * @package RegenerateThumbnails
 * @since 3.0.0
 */

/**
 * Registers new REST API endpoints.
 *
 * @since 3.0.0
 */
class RegenerateThumbnails_REST_Controller extends WP_REST_Controller {
	/**
	 * The namespace for the REST API routes.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $namespace = 'regenerate-thumbnails/v1';

	/**
	 * Register the new routes and endpoints.
	 *
	 * @since 3.0.0
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/regenerate/(?P<id>[\d]+)', array(
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'regenerate_item' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'                => array(
					'only_regenerate_missing_thumbnails'    => array(
						'description' => __( "Whether to only regenerate missing thumbnails. It's faster with this enabled.", 'regenerate-thumbnails' ),
						'type'        => 'boolean',
						'default'     => true,
					),
					'delete_unregistered_thumbnail_files'   => array(
						'description' => __( 'Whether to delete any old, now unregistered thumbnail files.', 'regenerate-thumbnails' ),
						'type'        => 'boolean',
						'default'     => false,
					),
					'update_usages_in_posts'                => array(
						'description' => __( 'Whether to update the image tags in any posts that make use of this attachment.', 'regenerate-thumbnails' ),
						'type'        => 'boolean',
						'default'     => true,
					),
					'update_usages_in_posts_post_type'      => array(
						'description'       => __( 'The types of posts to update. Defaults to all public post types.', 'regenerate-thumbnails' ),
						'type'              => 'array',
						'default'           => array(),
						'validate_callback' => array( $this, 'is_array' ),
					),
					'update_usages_in_posts_post_ids'       => array(
						'description'       => __( 'Specific post IDs to update rather than any posts that use this attachment.', 'regenerate-thumbnails' ),
						'type'              => 'array',
						'default'           => array(),
						'validate_callback' => array( $this, 'is_array' ),
					),
					'update_usages_in_posts_posts_per_loop' => array(
						'description'       => __( "Posts to process per loop. This is to control memory usage and you likely don't need to adjust this.", 'regenerate-thumbnails' ),
						'type'              => 'integer',
						'default'           => 10,
						'sanitize_callback' => 'absint',
					),
				),
			),
		) );

		register_rest_route( $this->namespace, '/attachmentinfo/(?P<id>[\d]+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'attachment_info' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			),
		) );

		register_rest_route( $this->namespace, '/featuredimages', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'featured_images' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'                => $this->get_paging_collection_params(),
			),
		) );
	}

	/**
	 * Register a filter to allow excluding site icons via a query parameter.
	 *
	 * @since 3.0.0
	 */
	public function register_filters() {
		add_filter( 'rest_attachment_query', array( $this, 'maybe_filter_out_site_icons' ), 10, 2 );
		add_filter( 'rest_attachment_query', array( $this, 'maybe_filter_mimes_types' ), 10, 2 );
	}

	/**
	 * If the exclude_site_icons parameter is set on a media (attachment) request,
	 * filter out any attachments that are or were being used as a site icon.
	 *
	 * @param array           $args    Key value array of query var to query value.
	 * @param WP_REST_Request $request The request used.
	 *
	 * @return array Key value array of query var to query value.
	 */
	public function maybe_filter_out_site_icons( $args, $request ) {
		if ( empty( $request['exclude_site_icons'] ) ) {
			return $args;
		}

		if ( ! isset( $args['meta_query'] ) ) {
			$args['meta_query'] = array();
		}

		$args['meta_query'][] = array(
			'key'     => '_wp_attachment_context',
			'value'   => 'site-icon',
			'compare' => 'NOT EXISTS',
		);

		return $args;
	}

	/**
	 * If the is_regeneratable parameter is set on a media (attachment) request,
	 * filter results to only include images and PDFs.
	 *
	 * @param array           $args    Key value array of query var to query value.
	 * @param WP_REST_Request $request The request used.
	 *
	 * @return array Key value array of query var to query value.
	 */
	public function maybe_filter_mimes_types( $args, $request ) {
		if ( empty( $request['is_regeneratable'] ) ) {
			return $args;
		}

		$args['post_mime_type'] = array();
		foreach ( get_allowed_mime_types() as $mime_type ) {
			if ( 'application/pdf' == $mime_type || 'image/' == substr( $mime_type, 0, 6 ) ) {
				$args['post_mime_type'][] = $mime_type;
			}
		}

		return $args;
	}

	/**
	 * Retrieves the paging query params for the collections.
	 *
	 * @since 3.0.0
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_paging_collection_params() {
		return array_intersect_key(
			parent::get_collection_params(),
			array_flip( array( 'page', 'per_page' ) )
		);
	}

	/**
	 * Regenerate the thumbnails for a specific media item.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return true|WP_Error True on success, otherwise a WP_Error object.
	 */
	public function regenerate_item( $request ) {
		$regenerator = RegenerateThumbnails_Regenerator::get_instance( $request->get_param( 'id' ) );

		if ( is_wp_error( $regenerator ) ) {
			return $regenerator;
		}

		$result = $regenerator->regenerate( array(
			'only_regenerate_missing_thumbnails'  => $request->get_param( 'only_regenerate_missing_thumbnails' ),
			'delete_unregistered_thumbnail_files' => $request->get_param( 'delete_unregistered_thumbnail_files' ),
		) );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( $request->get_param( 'update_usages_in_posts' ) ) {
			$posts_updated = $regenerator->update_usages_in_posts( array(
				'post_type'      => $request->get_param( 'update_usages_in_posts_post_type' ),
				'post_ids'       => $request->get_param( 'update_usages_in_posts_post_ids' ),
				'posts_per_loop' => $request->get_param( 'update_usages_in_posts_posts_per_loop' ),
			) );

			// If wp_update_post() failed for any posts, return that error.
			foreach ( $posts_updated as $post_updated_result ) {
				if ( is_wp_error( $post_updated_result ) ) {
					return $post_updated_result;
				}
			}
		}

		return $this->attachment_info( $request );
	}

	/**
	 * Return a bunch of information about the current attachment for use in the UI
	 * including details about the thumbnails.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return array|WP_Error The data array or a WP_Error object on error.
	 */
	public function attachment_info( $request ) {
		$regenerator = RegenerateThumbnails_Regenerator::get_instance( $request->get_param( 'id' ) );

		if ( is_wp_error( $regenerator ) ) {
			return $regenerator;
		}

		return $regenerator->get_attachment_info();
	}

	/**
	 * Return attachment IDs that are being used as featured images.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function featured_images( $request ) {
		global $wpdb;

		$page     = $request->get_param( 'page' );
		$per_page = $request->get_param( 'per_page' );

		$featured_image_ids = $wpdb->get_results( $wpdb->prepare(
			"SELECT meta_value as id FROM $wpdb->postmeta WHERE meta_key = '_thumbnail_id' ORDER BY post_id LIMIT %d OFFSET %d",
			$per_page,
			( $per_page * $page ) - $per_page
		) );

		$total     = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = '_thumbnail_id'" );
		$max_pages = ceil( $total / $per_page );

		if ( $page > $max_pages && $total > 0 ) {
			return new WP_Error( 'rest_post_invalid_page_number', __( 'The page number requested is larger than the number of pages available.' ), array( 'status' => 400 ) );
		}

		$response = rest_ensure_response( $featured_image_ids );

		$response->header( 'X-WP-Total', (int) $total );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$request_params = $request->get_query_params();
		$base           = add_query_arg( $request_params, rest_url( $this->namespace . '/featuredimages' ) );

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
	 * Check to see if the current user is allowed to use this endpoint.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return bool Whether the current user has permission to regenerate thumbnails.
	 */
	public function permissions_check( $request ) {
		return current_user_can( RegenerateThumbnails()->capability );
	}

	/**
	 * Returns whether a variable is an array or not. This is needed because 3 arguments are
	 * passed to validation callbacks but is_array() only accepts one argument.
	 *
	 * @since 3.0.0
	 *
	 * @see   https://core.trac.wordpress.org/ticket/34659
	 *
	 * @param mixed           $param   The parameter value to validate.
	 * @param WP_REST_Request $request The REST request.
	 * @param string          $key     The parameter name.
	 *
	 * @return bool Whether the parameter is an array or not.
	 */
	public function is_array( $param, $request, $key ) {
		return is_array( $param );
	}
}
