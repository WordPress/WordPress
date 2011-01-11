<?php
/**
 * Internal linking functions.
 *
 * @package WordPress
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
			'title' => trim( esc_html( strip_tags( get_the_title( $post ) ) ) ),
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
<form id="wp-link" tabindex="-1">
<?php wp_nonce_field( 'internal-linking', '_ajax_linking_nonce', false ); ?>
<div id="link-selector">
	<div id="link-options">
		<p class="howto"><?php _e( 'Enter the destination URL' ); ?></p>
		<div>
			<label for="url-field"><span><?php _e( 'URL' ); ?></span><input id="url-field" type="text" tabindex="10" autocomplete="off" /></label>
		</div>
		<div>
			<label for="link-title-field"><span><?php _e( 'Title' ); ?></span><input id="link-title-field" type="text" tabindex="20" autocomplete="off" /></label>
		</div>
		<div class="link-target">
			<label for="link-target-checkbox"><input type="checkbox" id="link-target-checkbox" tabindex="30" /> <?php _e( 'Open link in a new window/tab' ); ?></label>
		</div>
	</div>
	<?php $show_internal = '1' == get_user_setting( 'wplink', '0' ); ?>
	<p class="howto toggle-arrow <?php if ( $show_internal ) echo 'toggle-arrow-active'; ?>" id="internal-toggle"><?php _e( 'Or link to existing content' ); ?></p>
	<div id="search-panel"<?php if ( ! $show_internal ) echo ' style="display:none"'; ?>>
		<div class="link-search-wrapper">
			<label for="search-field">
				<span><?php _e( 'Search' ); ?></span>
				<input type="text" id="search-field" class="link-search-field" tabindex="60" autocomplete="off" />
				<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
			</label>
		</div>
		<div id="search-results" class="query-results">
			<ul></ul>
			<div class="river-waiting">
				<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
			</div>
		</div>
		<div id="most-recent-results" class="query-results">
			<div class="query-notice"><em><?php _e( 'No search term specified. Showing recent items.' ); ?></em></div>
			<ul></ul>
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
		<?php submit_button( __('Update'), 'primary', 'wp-link-submit', false, array('tabindex' => 100)); ?>
	</div>
</div>
</form>
<?php
}
?>