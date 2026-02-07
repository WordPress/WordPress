<?php

/**
 * Add search_columns parameter to users endpoint parameters
 *
 * @param array $query_params JSON Schema-formatted collection parameters.
 * @return array Updated collection parameters
 */
function gutenberg_add_search_columns_param( $query_params ) {
	$query_params['search_columns'] = array(
		'default'     => array(),
		'description' => __( 'Array of column names to be searched.' ),
		'type'        => 'array',
		'items'       => array(
			'enum' => array( 'email', 'name', 'id', 'username', 'slug' ),
			'type' => 'string',
		),
	);

	return $query_params;
}

add_filter( 'rest_user_collection_params', 'gutenberg_add_search_columns_param', 10, 1 );

/**
 * Modify user query based on search_columns parameter
 *
 * @param array           $prepared_args Array of arguments for WP_User_Query.
 * @param WP_REST_Request $request       The REST API request.
 * @return array Modified arguments
 */
function gutenberg_modify_user_query_args( $prepared_args, $request ) {
	if ( $request->get_param( 'search' ) && $request->get_param( 'search_columns' ) ) {
		$search_columns = $request->get_param( 'search_columns' );

		// Validate search columns
		$valid_columns          = isset( $prepared_args['search_columns'] )
			? $prepared_args['search_columns']
			: array( 'ID', 'user_login', 'user_nicename', 'user_email', 'user_url', 'display_name' );
		$search_columns_mapping = array(
			'id'       => 'ID',
			'username' => 'user_login',
			'slug'     => 'user_nicename',
			'email'    => 'user_email',
			'name'     => 'display_name',
		);
		$search_columns         = array_map(
			static function ( $column ) use ( $search_columns_mapping ) {
				return $search_columns_mapping[ $column ];
			},
			$search_columns
		);
		$search_columns         = array_intersect( $search_columns, $valid_columns );

		if ( ! empty( $search_columns ) ) {
			$prepared_args['search_columns'] = $search_columns;
		}
	}

	return $prepared_args;
}
add_filter( 'rest_user_query', 'gutenberg_modify_user_query_args', 10, 2 );
