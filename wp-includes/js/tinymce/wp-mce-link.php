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

	$query['offset'] = $args['pagenum'] > 1 ? $query['posts_per_page'] * ( $args['pagenum'] - 1 ) : 0;

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
		);
	}

	return $results;
}

function wp_link_ajax( $request ) {
	// Searches have a title term.
	if ( isset( $request['title'] ) )
		$args['s'] = stripslashes( $request['title'] );
	$args['pagenum'] = ! empty( $request['page'] ) ? absint( $request['page'] ) : 1;

	$results = wp_link_query( $args );

	if ( ! isset( $results ) )
		die( '0' );

	echo json_encode( $results );
	echo "\n";
}

function wp_link_dialog() {
?>
<div id="wp-link">
<div id="link-selector">
	<div id="link-options">
		<p class="howto"><?php _e( 'Enter the destination URL:' ); ?></p>
		<label for="url-field">
			<span><?php _e( 'URL' ); ?></span><input id="url-field" type="text" />
		</label>
		<label for="link-title-field">
			<span><?php _e( 'Title' ); ?></span><input id="link-title-field" type="text" />
		</label>
		<label for="link-target-checkbox" id="open-in-new-tab">
			<input type="checkbox" id="link-target-checkbox" /><span><?php _e( 'Open link in a new window/tab' ); ?></span>
		</label>
	</div>
	<div id="search-panel">
		<div class="link-search-wrapper">
			<p class="howto"><?php _e( 'Or, link to existing site content:' ); ?></p>
			<label for="search-field">
				<span><?php _e( 'Search' ); ?></span>
				<input type="text" id="search-field" class="link-search-field" />
				<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
			</label>
		</div>
		<div id="search-results" class="query-results">
			<ul>
				<li class="wp-results-loading unselectable"><em><?php _e( 'Loading...' ); ?></em></li>
			</ul>
			<div class="river-waiting">
				<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
			</div>
		</div>
		<?php $most_recent = wp_link_query(); ?>
		<div id="most-recent-results" class="query-results">
			<ul>
				<li class="unselectable"><em><?php _e( 'No search term specified. Showing recent items.' ); ?></em></li>
				<?php foreach ( $most_recent as $item ) : ?>
					<li>
						<input type="hidden" class="item-permalink" value="<?php echo esc_url( $item['permalink'] ); ?>" />
						<span class="item-title"><?php echo $item['title']; ?></span>
						<span class="item-info"><?php echo esc_html( $item['info'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="river-waiting">
				<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
			</div>
		</div>
	</div>
</div>
<div class="submitbox">
	<div id="wp-link-cancel">
		<a class="submitdelete deletion"><?php _e( 'Cancel' ); ?></a>
	</div>
	<div id="wp-link-update">
		<a class="button-primary"><?php _e( 'Update' ); ?></a>
	</div>
</div>
</div>
<?php } ?>