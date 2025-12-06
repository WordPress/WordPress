<?php
/**
 * PHP and WordPress configuration compatibility functions for the Gutenberg
 * editor plugin changes related to REST API.
 *
 * @package gutenberg
 */

// When querying terms for a given taxonomy in the REST API, respect the default
// query arguments set for that taxonomy upon registration.
function gutenberg_respect_taxonomy_default_args_in_rest_api( $args ) {
	// If a `post` argument is provided, the Terms controller will use
	// `wp_get_object_terms`, which respects the default query arguments,
	// so we don't need to do anything.
	if ( ! empty( $args['post'] ) ) {
		return $args;
	}

	$t = get_taxonomy( $args['taxonomy'] );
	if ( isset( $t->args ) && is_array( $t->args ) ) {
		$args = array_merge( $args, $t->args );
	}
	return $args;
}
add_action(
	'registered_taxonomy',
	function ( $taxonomy ) {
		add_filter( "rest_{$taxonomy}_query", 'gutenberg_respect_taxonomy_default_args_in_rest_api' );
	}
);
add_action(
	'unregistered_taxonomy',
	function ( $taxonomy ) {
		remove_filter( "rest_{$taxonomy}_query", 'gutenberg_respect_taxonomy_default_args_in_rest_api' );
	}
);

/**
 * Adds the site reading options to the REST API index.
 *
 * @param WP_REST_Response $response REST API response.
 * @return WP_REST_Response Modified REST API response.
 */
function gutenberg_add_rest_index_reading_options( WP_REST_Response $response ) {
	$response->data['page_for_posts'] = (int) get_option( 'page_for_posts' );
	$response->data['page_on_front']  = (int) get_option( 'page_on_front' );
	$response->data['show_on_front']  = get_option( 'show_on_front' );

	return $response;
}
add_filter( 'rest_index', 'gutenberg_add_rest_index_reading_options' );

/**
 * Adds `ignore_sticky` parameter to the post collection endpoint.
 *
 * Note: Backports into the wp-includes/rest-api/endpoints/class-wp-rest-posts-controller.php file.
 *
 * @param array        $query_params JSON Schema-formatted collection parameters.
 * @param WP_Post_Type $post_type    Post type object.
 * @return array
 */
function gutenberg_modify_post_collection_param( $query_params, WP_Post_Type $post_type ) {
	if ( 'post' === $post_type->name && ! isset( $query_params['ignore_sticky'] ) ) {
		$query_params['ignore_sticky'] = array(
			'description' => __( 'Whether to ignore sticky posts or not.' ),
			'type'        => 'boolean',
			'default'     => false,
		);
	}

	return $query_params;
}
add_filter( 'rest_post_collection_params', 'gutenberg_modify_post_collection_param', 10, 2 );

/**
 * Modify posts query based on `ignore_sticky` parameter.
 *
 * Note: Backports into the wp-includes/rest-api/endpoints/class-wp-rest-posts-controller.php file.
 *
 * @param array           $prepared_args Array of arguments for WP_User_Query.
 * @param WP_REST_Request $request       The REST API request.
 * @return array Modified arguments
 */
function gutenberg_modify_post_collection_query( $args, WP_REST_Request $request ) {
	/*
	 * Honor the original REST API `post__in` behavior. Don't prepend sticky posts
	 * when `post__in` has been specified.
	 */
	if ( isset( $request['ignore_sticky'] ) && empty( $args['post__in'] ) ) {
		$args['ignore_sticky_posts'] = $request['ignore_sticky'];
	}

	return $args;
}
add_filter( 'rest_post_query', 'gutenberg_modify_post_collection_query', 10, 2 );

/**
 * Registers `default_template_types` and `default_template_part_areas` fields for the active theme.
 *
 * Note: Backports into the wp-includes/rest-api/endpoints/class-wp-rest-themes-controller.php file.
 *
 * @return void
 */
function gutenberg_register_rest_theme_fields() {
	register_rest_field(
		'theme',
		'default_template_types',
		array(
			'get_callback' => static function ( $response_data ) {
				if ( ! isset( $response_data['status'] ) || 'active' !== $response_data['status'] ) {
					return null;
				}

				$default_template_types = array();
				foreach ( get_default_block_template_types() as $slug => $template_type ) {
					$template_type['slug']    = (string) $slug;
					$default_template_types[] = $template_type;
				}

				return $default_template_types;
			},
			'schema'       => array(
				'description' => __( 'A list of default template types.' ),
				'type'        => 'array',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'slug'        => array(
							'type' => 'string',
						),
						'title'       => array(
							'type' => 'string',
						),
						'description' => array(
							'type' => 'string',
						),
					),
				),
			),
		)
	);
	register_rest_field(
		'theme',
		'default_template_part_areas',
		array(
			'get_callback' => static function ( $response_data ) {
				if ( ! isset( $response_data['status'] ) || 'active' !== $response_data['status'] ) {
					return null;
				}

				return get_allowed_block_template_part_areas();
			},
			'schema'       => array(
				'description' => __( 'A list of allowed area values for template parts.' ),
				'type'        => 'array',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'area'        => array(
							'type' => 'string',
						),
						'label'       => array(
							'type' => 'string',
						),
						'description' => array(
							'type' => 'string',
						),
						'icon'        => array(
							'type' => 'string',
						),
						'area_tag'    => array(
							'type' => 'string',
						),
					),
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'gutenberg_register_rest_theme_fields' );
