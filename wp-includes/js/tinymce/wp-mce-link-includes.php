<?php
// args expects optionally 'pagenum' and 's'
function wp_link_query( $args = array() ) {
	$pts = get_post_types( array( 'publicly_queryable' => true ), 'objects' );
	$pt_names = array_keys( $pts );
	
	$query = array(
		'post_type' => $pt_names,
		'suppress_filters' => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
		'post_status' => 'publish',
		'order' => 'DESC',
		'orderby' => 'post_date',
		'posts_per_page' => 20,
	);

	$args['pagenum'] = isset( $args['pagenum'] ) ? absint( $args['pagenum'] ) : 1;

	if ( isset( $args['s'] ) )
		$query['s'] = $args['s'];

	$pagination = array(
		'current'  => $args['pagenum'],
		'per_page' => $query['posts_per_page'],
	);

	$query['offset'] = $pagination['offset'] = $args['pagenum'] > 1 ? $query['posts_per_page'] * ( $args['pagenum'] - 1 ) : 0;

	// Do main query.
	$get_posts = new WP_Query;
	$posts = $get_posts->query( $query );
	// Check if any posts were found.
	if ( ! $get_posts->post_count )
		return false;
	
	// Build results.
	$results = array();
	foreach ( $posts as $post ) {
		if ( 'post' == $post->post_type )
			$info = mysql2date( __( 'Y/m/d' ), $post->post_date );
		else
			$info = $pts[ $post->post_type ]->labels->singular_name;
		
		$results[] = array(
			'ID' => $post->ID,
			'title' => esc_html( strip_tags($post->post_title) ),
			'permalink' => get_permalink( $post->ID ),
			'info' => $info,
		);;
	}

	// Remaining pagination values.
	$pagination['max'] = $get_posts->max_num_pages;
	$pagination['page_links'] = paginate_links( array(
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $pagination['max'],
		'current' => $pagination['current']
	) );

	// Build response.
	$resp = array(
		'query' => $get_posts,
		'objects' => $posts,
		'results' => $results,
		'pages' => $pagination,
	);

	return $resp;
}

function wp_link_ajax( $request ) {
	// Searches have a title term.
	if ( isset( $request['title'] ) )
		$args['s'] = stripslashes( $request['title'] );
	$args['pagenum'] = ! empty( $request['page'] ) ? absint( $request['page'] ) : 1;

	$resp = wp_link_query( $args );

	if ( ! isset( $resp ) )
		die( '0' );

	$json = array( 'results' => $resp['results'] );
	if ( isset( $resp['pages'] ) && !empty( $resp['pages']['page_links'] ) )
		$json['page_links'] = $resp['pages']['page_links'];
	
	echo json_encode( $json );
	echo "\n";
}

?>