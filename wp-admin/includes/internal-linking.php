<?php
/**
 * Internal linking functions.
 *
 * @package WordPres
 * @subpackage Administration
 * @since 3.1.0
 */

/**
 * Performs post queries for internal linking.
 *
 * @since 3.1.0
 *
 * @param array $args Optional. Accepts 'pagenum' and 's' (search) arguments.
 * @return array Results.
 */
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

/**
 * Dialog for internal linking.
 *
 * @since 3.1.0
 */
function wp_link_dialog() {
?>
<div id="wp-link">
<div id="link-selector">
	<div id="link-options">
		<p class="howto"><?php _e( 'Enter the destination URL:' ); ?></p>
		<div>
			<label><span><?php _e( 'URL' ); ?></span><input id="url-field" type="text" /></label>
		</div>
		<div>
			<label><span><?php _e( 'Title' ); ?></span><input id="link-title-field" type="text" /></label>
		</div>
		<div class="link-target">
			<label><input type="checkbox" id="link-target-checkbox" /> <?php _e( 'Open link in a new window/tab' ); ?></label>
		</div>
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
				<li class="loading-results unselectable"><em><?php _e( 'Loading...' ); ?></em></li>
			</ul>
			<div class="river-waiting">
				<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
			</div>
		</div>
		<?php $most_recent = wp_link_query(); ?>
		<div id="most-recent-results" class="query-results">
			<ul>
				<li class="unselectable"><em><?php _e( 'No search term specified. Showing recent items.' ); ?></em></li>
				<?php
				$alt = true;
				foreach ( $most_recent as $item ) : ?>
					<li<?php if ( $alt ) echo ' class="alternate"'; ?>>
						<input type="hidden" class="item-permalink" value="<?php echo esc_url( $item['permalink'] ); ?>" />
						<span class="item-title"><?php echo $item['title']; ?></span>
						<span class="item-info"><?php echo esc_html( $item['info'] ); ?></span>
					</li>
				<?php
				$alt = ! $alt;
				endforeach; ?>
			</ul>
			<div class="river-waiting">
				<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
			</div>
		</div>
	</div>
</div>
<div class="submitbox">
	<div id="wp-link-cancel">
		<a class="submitdelete deletion" href="#"><?php _e( 'Cancel' ); ?></a>
	</div>
	<div id="wp-link-update">
		<a class="button-primary" href="#"><?php _e( 'Update' ); ?></a>
	</div>
</div>
</div>
<?php 
}
?>