<?php
/**
 * WordPress Core Ajax Handlers.
 *
 * @package WordPress
 * @subpackage Administration
 */

//
// No-privilege Ajax handlers.
//

/**
 * Ajax handler for the Heartbeat API in
 * the no-privilege context.
 *
 * Runs when the user is not logged in.
 *
 * @since 3.6.0
 */
function wp_ajax_nopriv_heartbeat() {
	$response = array();

	// screen_id is the same as $current_screen->id and the JS global 'pagenow'.
	if ( ! empty($_POST['screen_id']) )
		$screen_id = sanitize_key($_POST['screen_id']);
	else
		$screen_id = 'front';

	if ( ! empty($_POST['data']) ) {
		$data = wp_unslash( (array) $_POST['data'] );

		/**
		 * Filter Heartbeat AJAX response in no-privilege environments.
		 *
		 * @since 3.6.0
		 *
		 * @param array|object $response  The no-priv Heartbeat response object or array.
		 * @param array        $data      An array of data passed via $_POST.
		 * @param string       $screen_id The screen id.
		 */
		$response = apply_filters( 'heartbeat_nopriv_received', $response, $data, $screen_id );
	}

	/**
	 * Filter Heartbeat AJAX response when no data is passed.
	 *
	 * @since 3.6.0
	 *
	 * @param array|object $response  The Heartbeat response object or array.
	 * @param string       $screen_id The screen id.
	 */
	$response = apply_filters( 'heartbeat_nopriv_send', $response, $screen_id );

	/**
	 * Fires when Heartbeat ticks in no-privilege environments.
	 *
	 * Allows the transport to be easily replaced with long-polling.
	 *
	 * @since 3.6.0
	 *
	 * @param array|object $response  The no-priv Heartbeat response.
	 * @param string       $screen_id The screen id.
	 */
	do_action( 'heartbeat_nopriv_tick', $response, $screen_id );

	// Send the current time according to the server.
	$response['server_time'] = time();

	wp_send_json($response);
}

//
// GET-based Ajax handlers.
//

/**
 * Ajax handler for fetching a list table.
 *
 * @since 3.1.0
 */
function wp_ajax_fetch_list() {
	global $wp_list_table;

	$list_class = $_GET['list_args']['class'];
	check_ajax_referer( "fetch-list-$list_class", '_ajax_fetch_list_nonce' );

	$wp_list_table = _get_list_table( $list_class, array( 'screen' => $_GET['list_args']['screen']['id'] ) );
	if ( ! $wp_list_table )
		wp_die( 0 );

	if ( ! $wp_list_table->ajax_user_can() )
		wp_die( -1 );

	$wp_list_table->ajax_response();

	wp_die( 0 );
}

/**
 * Ajax handler for tag search.
 *
 * @since 3.1.0
 */
function wp_ajax_ajax_tag_search() {
	if ( ! isset( $_GET['tax'] ) ) {
		wp_die( 0 );
	}

	$taxonomy = sanitize_key( $_GET['tax'] );
	$tax = get_taxonomy( $taxonomy );
	if ( ! $tax ) {
		wp_die( 0 );
	}

	if ( ! current_user_can( $tax->cap->assign_terms ) ) {
		wp_die( -1 );
	}

	$s = wp_unslash( $_GET['q'] );

	$comma = _x( ',', 'tag delimiter' );
	if ( ',' !== $comma )
		$s = str_replace( $comma, ',', $s );
	if ( false !== strpos( $s, ',' ) ) {
		$s = explode( ',', $s );
		$s = $s[count( $s ) - 1];
	}
	$s = trim( $s );

	/**
	 * Filter the minimum number of characters required to fire a tag search via AJAX.
	 *
	 * @since 4.0.0
	 *
	 * @param int    $characters The minimum number of characters required. Default 2.
	 * @param object $tax        The taxonomy object.
	 * @param string $s          The search term.
	 */
	$term_search_min_chars = (int) apply_filters( 'term_search_min_chars', 2, $tax, $s );

	/*
	 * Require $term_search_min_chars chars for matching (default: 2)
	 * ensure it's a non-negative, non-zero integer.
	 */
	if ( ( $term_search_min_chars == 0 ) || ( strlen( $s ) < $term_search_min_chars ) ){
		wp_die();
	}

	$results = get_terms( $taxonomy, array( 'name__like' => $s, 'fields' => 'names', 'hide_empty' => false ) );

	echo join( $results, "\n" );
	wp_die();
}

/**
 * Ajax handler for compression testing.
 *
 * @since 3.1.0
 */
function wp_ajax_wp_compression_test() {
	if ( !current_user_can( 'manage_options' ) )
		wp_die( -1 );

	if ( ini_get('zlib.output_compression') || 'ob_gzhandler' == ini_get('output_handler') ) {
		update_site_option('can_compress_scripts', 0);
		wp_die( 0 );
	}

	if ( isset($_GET['test']) ) {
		header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
		header( 'Pragma: no-cache' );
		header('Content-Type: application/x-javascript; charset=UTF-8');
		$force_gzip = ( defined('ENFORCE_GZIP') && ENFORCE_GZIP );
		$test_str = '"wpCompressionTest Lorem ipsum dolor sit amet consectetuer mollis sapien urna ut a. Eu nonummy condimentum fringilla tempor pretium platea vel nibh netus Maecenas. Hac molestie amet justo quis pellentesque est ultrices interdum nibh Morbi. Cras mattis pretium Phasellus ante ipsum ipsum ut sociis Suspendisse Lorem. Ante et non molestie. Porta urna Vestibulum egestas id congue nibh eu risus gravida sit. Ac augue auctor Ut et non a elit massa id sodales. Elit eu Nulla at nibh adipiscing mattis lacus mauris at tempus. Netus nibh quis suscipit nec feugiat eget sed lorem et urna. Pellentesque lacus at ut massa consectetuer ligula ut auctor semper Pellentesque. Ut metus massa nibh quam Curabitur molestie nec mauris congue. Volutpat molestie elit justo facilisis neque ac risus Ut nascetur tristique. Vitae sit lorem tellus et quis Phasellus lacus tincidunt nunc Fusce. Pharetra wisi Suspendisse mus sagittis libero lacinia Integer consequat ac Phasellus. Et urna ac cursus tortor aliquam Aliquam amet tellus volutpat Vestibulum. Justo interdum condimentum In augue congue tellus sollicitudin Quisque quis nibh."';

		 if ( 1 == $_GET['test'] ) {
		 	echo $test_str;
		 	wp_die();
		 } elseif ( 2 == $_GET['test'] ) {
			if ( !isset($_SERVER['HTTP_ACCEPT_ENCODING']) )
				wp_die( -1 );
			if ( false !== stripos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') && function_exists('gzdeflate') && ! $force_gzip ) {
				header('Content-Encoding: deflate');
				$out = gzdeflate( $test_str, 1 );
			} elseif ( false !== stripos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzencode') ) {
				header('Content-Encoding: gzip');
				$out = gzencode( $test_str, 1 );
			} else {
				wp_die( -1 );
			}
			echo $out;
			wp_die();
		} elseif ( 'no' == $_GET['test'] ) {
			update_site_option('can_compress_scripts', 0);
		} elseif ( 'yes' == $_GET['test'] ) {
			update_site_option('can_compress_scripts', 1);
		}
	}

	wp_die( 0 );
}

/**
 * Ajax handler for image editor previews.
 *
 * @since 3.1.0
 */
function wp_ajax_imgedit_preview() {
	$post_id = intval($_GET['postid']);
	if ( empty($post_id) || !current_user_can('edit_post', $post_id) )
		wp_die( -1 );

	check_ajax_referer( "image_editor-$post_id" );

	include_once( ABSPATH . 'wp-admin/includes/image-edit.php' );
	if ( ! stream_preview_image($post_id) )
		wp_die( -1 );

	wp_die();
}

/**
 * Ajax handler for oEmbed caching.
 *
 * @since 3.1.0
 */
function wp_ajax_oembed_cache() {
	$GLOBALS['wp_embed']->cache_oembed( $_GET['post'] );
	wp_die( 0 );
}

/**
 * Ajax handler for user autocomplete.
 *
 * @since 3.4.0
 */
function wp_ajax_autocomplete_user() {
	if ( ! is_multisite() || ! current_user_can( 'promote_users' ) || wp_is_large_network( 'users' ) )
		wp_die( -1 );

	/** This filter is documented in wp-admin/user-new.php */
	if ( ! is_super_admin() && ! apply_filters( 'autocomplete_users_for_site_admins', false ) )
		wp_die( -1 );

	$return = array();

	// Check the type of request
	// Current allowed values are `add` and `search`
	if ( isset( $_REQUEST['autocomplete_type'] ) && 'search' === $_REQUEST['autocomplete_type'] ) {
		$type = $_REQUEST['autocomplete_type'];
	} else {
		$type = 'add';
	}

	// Check the desired field for value
	// Current allowed values are `user_email` and `user_login`
	if ( isset( $_REQUEST['autocomplete_field'] ) && 'user_email' === $_REQUEST['autocomplete_field'] ) {
		$field = $_REQUEST['autocomplete_field'];
	} else {
		$field = 'user_login';
	}

	// Exclude current users of this blog
	if ( isset( $_REQUEST['site_id'] ) ) {
		$id = absint( $_REQUEST['site_id'] );
	} else {
		$id = get_current_blog_id();
	}

	$include_blog_users = ( $type == 'search' ? get_users( array( 'blog_id' => $id, 'fields' => 'ID' ) ) : array() );
	$exclude_blog_users = ( $type == 'add' ? get_users( array( 'blog_id' => $id, 'fields' => 'ID' ) ) : array() );

	$users = get_users( array(
		'blog_id' => false,
		'search'  => '*' . $_REQUEST['term'] . '*',
		'include' => $include_blog_users,
		'exclude' => $exclude_blog_users,
		'search_columns' => array( 'user_login', 'user_nicename', 'user_email' ),
	) );

	foreach ( $users as $user ) {
		$return[] = array(
			/* translators: 1: user_login, 2: user_email */
			'label' => sprintf( __( '%1$s (%2$s)' ), $user->user_login, $user->user_email ),
			'value' => $user->$field,
		);
	}

	wp_die( wp_json_encode( $return ) );
}

/**
 * Ajax handler for dashboard widgets.
 *
 * @since 3.4.0
 */
function wp_ajax_dashboard_widgets() {
	require_once ABSPATH . 'wp-admin/includes/dashboard.php';

	$pagenow = $_GET['pagenow'];
	if ( $pagenow === 'dashboard-user' || $pagenow === 'dashboard-network' || $pagenow === 'dashboard' ) {
		set_current_screen( $pagenow );
	}

	switch ( $_GET['widget'] ) {
		case 'dashboard_primary' :
			wp_dashboard_primary();
			break;
	}
	wp_die();
}

/**
 * Ajax handler for Customizer preview logged-in status.
 *
 * @since 3.4.0
 */
function wp_ajax_logged_in() {
	wp_die( 1 );
}

//
// Ajax helpers.
//

/**
 * Sends back current comment total and new page links if they need to be updated.
 *
 * Contrary to normal success AJAX response ("1"), die with time() on success.
 *
 * @since 2.7.0
 *
 * @param int $comment_id
 * @return die
 */
function _wp_ajax_delete_comment_response( $comment_id, $delta = -1 ) {
	$total    = isset( $_POST['_total'] )    ? (int) $_POST['_total']    : 0;
	$per_page = isset( $_POST['_per_page'] ) ? (int) $_POST['_per_page'] : 0;
	$page     = isset( $_POST['_page'] )     ? (int) $_POST['_page']     : 0;
	$url      = isset( $_POST['_url'] )      ? esc_url_raw( $_POST['_url'] ) : '';

	// JS didn't send us everything we need to know. Just die with success message
	if ( !$total || !$per_page || !$page || !$url )
		wp_die( time() );

	$total += $delta;
	if ( $total < 0 )
		$total = 0;

	// Only do the expensive stuff on a page-break, and about 1 other time per page
	if ( 0 == $total % $per_page || 1 == mt_rand( 1, $per_page ) ) {
		$post_id = 0;
		$status = 'total_comments'; // What type of comment count are we looking for?
		$parsed = parse_url( $url );
		if ( isset( $parsed['query'] ) ) {
			parse_str( $parsed['query'], $query_vars );
			if ( !empty( $query_vars['comment_status'] ) )
				$status = $query_vars['comment_status'];
			if ( !empty( $query_vars['p'] ) )
				$post_id = (int) $query_vars['p'];
		}

		$comment_count = wp_count_comments($post_id);

		// We're looking for a known type of comment count.
		if ( isset( $comment_count->$status ) )
			$total = $comment_count->$status;
			// Else use the decremented value from above.
	}

	// The time since the last comment count.
	$time = time();

	$x = new WP_Ajax_Response( array(
		'what' => 'comment',
		// Here for completeness - not used.
		'id' => $comment_id,
		'supplemental' => array(
			'total_items_i18n' => sprintf( _n( '1 item', '%s items', $total ), number_format_i18n( $total ) ),
			'total_pages' => ceil( $total / $per_page ),
			'total_pages_i18n' => number_format_i18n( ceil( $total / $per_page ) ),
			'total' => $total,
			'time' => $time
		)
	) );
	$x->send();
}

//
// POST-based Ajax handlers.
//

/**
 * Ajax handler for adding a hierarchical term.
 *
 * @since 3.1.0
 */
function _wp_ajax_add_hierarchical_term() {
	$action = $_POST['action'];
	$taxonomy = get_taxonomy(substr($action, 4));
	check_ajax_referer( $action, '_ajax_nonce-add-' . $taxonomy->name );
	if ( !current_user_can( $taxonomy->cap->edit_terms ) )
		wp_die( -1 );
	$names = explode(',', $_POST['new'.$taxonomy->name]);
	$parent = isset($_POST['new'.$taxonomy->name.'_parent']) ? (int) $_POST['new'.$taxonomy->name.'_parent'] : 0;
	if ( 0 > $parent )
		$parent = 0;
	if ( $taxonomy->name == 'category' )
		$post_category = isset($_POST['post_category']) ? (array) $_POST['post_category'] : array();
	else
		$post_category = ( isset($_POST['tax_input']) && isset($_POST['tax_input'][$taxonomy->name]) ) ? (array) $_POST['tax_input'][$taxonomy->name] : array();
	$checked_categories = array_map( 'absint', (array) $post_category );
	$popular_ids = wp_popular_terms_checklist($taxonomy->name, 0, 10, false);

	foreach ( $names as $cat_name ) {
		$cat_name = trim($cat_name);
		$category_nicename = sanitize_title($cat_name);
		if ( '' === $category_nicename )
			continue;
		if ( !$cat_id = term_exists( $cat_name, $taxonomy->name, $parent ) )
			$cat_id = wp_insert_term( $cat_name, $taxonomy->name, array( 'parent' => $parent ) );
		if ( is_wp_error( $cat_id ) )
			continue;
		else if ( is_array( $cat_id ) )
			$cat_id = $cat_id['term_id'];
		$checked_categories[] = $cat_id;
		if ( $parent ) // Do these all at once in a second
			continue;
		ob_start();
			wp_terms_checklist( 0, array( 'taxonomy' => $taxonomy->name, 'descendants_and_self' => $cat_id, 'selected_cats' => $checked_categories, 'popular_cats' => $popular_ids ));
		$data = ob_get_contents();
		ob_end_clean();
		$add = array(
			'what' => $taxonomy->name,
			'id' => $cat_id,
			'data' => str_replace( array("\n", "\t"), '', $data),
			'position' => -1
		);
	}

	if ( $parent ) { // Foncy - replace the parent and all its children
		$parent = get_term( $parent, $taxonomy->name );
		$term_id = $parent->term_id;

		while ( $parent->parent ) { // get the top parent
			$parent = get_term( $parent->parent, $taxonomy->name );
			if ( is_wp_error( $parent ) )
				break;
			$term_id = $parent->term_id;
		}

		ob_start();
			wp_terms_checklist( 0, array('taxonomy' => $taxonomy->name, 'descendants_and_self' => $term_id, 'selected_cats' => $checked_categories, 'popular_cats' => $popular_ids));
		$data = ob_get_contents();
		ob_end_clean();
		$add = array(
			'what' => $taxonomy->name,
			'id' => $term_id,
			'data' => str_replace( array("\n", "\t"), '', $data),
			'position' => -1
		);
	}

	ob_start();
		wp_dropdown_categories( array(
			'taxonomy' => $taxonomy->name, 'hide_empty' => 0, 'name' => 'new'.$taxonomy->name.'_parent', 'orderby' => 'name',
			'hierarchical' => 1, 'show_option_none' => '&mdash; '.$taxonomy->labels->parent_item.' &mdash;'
		) );
	$sup = ob_get_contents();
	ob_end_clean();
	$add['supplemental'] = array( 'newcat_parent' => $sup );

	$x = new WP_Ajax_Response( $add );
	$x->send();
}

/**
 * Ajax handler for deleting a comment.
 *
 * @since 3.1.0
 */
function wp_ajax_delete_comment() {
	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;

	if ( !$comment = get_comment( $id ) )
		wp_die( time() );
	if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) )
		wp_die( -1 );

	check_ajax_referer( "delete-comment_$id" );
	$status = wp_get_comment_status( $comment->comment_ID );

	$delta = -1;
	if ( isset($_POST['trash']) && 1 == $_POST['trash'] ) {
		if ( 'trash' == $status )
			wp_die( time() );
		$r = wp_trash_comment( $comment->comment_ID );
	} elseif ( isset($_POST['untrash']) && 1 == $_POST['untrash'] ) {
		if ( 'trash' != $status )
			wp_die( time() );
		$r = wp_untrash_comment( $comment->comment_ID );
		if ( ! isset( $_POST['comment_status'] ) || $_POST['comment_status'] != 'trash' ) // undo trash, not in trash
			$delta = 1;
	} elseif ( isset($_POST['spam']) && 1 == $_POST['spam'] ) {
		if ( 'spam' == $status )
			wp_die( time() );
		$r = wp_spam_comment( $comment->comment_ID );
	} elseif ( isset($_POST['unspam']) && 1 == $_POST['unspam'] ) {
		if ( 'spam' != $status )
			wp_die( time() );
		$r = wp_unspam_comment( $comment->comment_ID );
		if ( ! isset( $_POST['comment_status'] ) || $_POST['comment_status'] != 'spam' ) // undo spam, not in spam
			$delta = 1;
	} elseif ( isset($_POST['delete']) && 1 == $_POST['delete'] ) {
		$r = wp_delete_comment( $comment->comment_ID );
	} else {
		wp_die( -1 );
	}

	if ( $r ) // Decide if we need to send back '1' or a more complicated response including page links and comment counts
		_wp_ajax_delete_comment_response( $comment->comment_ID, $delta );
	wp_die( 0 );
}

/**
 * Ajax handler for deleting a tag.
 *
 * @since 3.1.0
 */
function wp_ajax_delete_tag() {
	$tag_id = (int) $_POST['tag_ID'];
	check_ajax_referer( "delete-tag_$tag_id" );

	$taxonomy = !empty($_POST['taxonomy']) ? $_POST['taxonomy'] : 'post_tag';
	$tax = get_taxonomy($taxonomy);

	if ( !current_user_can( $tax->cap->delete_terms ) )
		wp_die( -1 );

	$tag = get_term( $tag_id, $taxonomy );
	if ( !$tag || is_wp_error( $tag ) )
		wp_die( 1 );

	if ( wp_delete_term($tag_id, $taxonomy))
		wp_die( 1 );
	else
		wp_die( 0 );
}

/**
 * Ajax handler for deleting a link.
 *
 * @since 3.1.0
 */
function wp_ajax_delete_link() {
	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;

	check_ajax_referer( "delete-bookmark_$id" );
	if ( !current_user_can( 'manage_links' ) )
		wp_die( -1 );

	$link = get_bookmark( $id );
	if ( !$link || is_wp_error( $link ) )
		wp_die( 1 );

	if ( wp_delete_link( $id ) )
		wp_die( 1 );
	else
		wp_die( 0 );
}

/**
 * Ajax handler for deleting meta.
 *
 * @since 3.1.0
 */
function wp_ajax_delete_meta() {
	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;

	check_ajax_referer( "delete-meta_$id" );
	if ( !$meta = get_metadata_by_mid( 'post', $id ) )
		wp_die( 1 );

	if ( is_protected_meta( $meta->meta_key, 'post' ) || ! current_user_can( 'delete_post_meta',  $meta->post_id, $meta->meta_key ) )
		wp_die( -1 );
	if ( delete_meta( $meta->meta_id ) )
		wp_die( 1 );
	wp_die( 0 );
}

/**
 * Ajax handler for deleting a post.
 *
 * @since 3.1.0
 *
 * @param string $action Action to perform.
 */
function wp_ajax_delete_post( $action ) {
	if ( empty( $action ) )
		$action = 'delete-post';
	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;

	check_ajax_referer( "{$action}_$id" );
	if ( !current_user_can( 'delete_post', $id ) )
		wp_die( -1 );

	if ( !get_post( $id ) )
		wp_die( 1 );

	if ( wp_delete_post( $id ) )
		wp_die( 1 );
	else
		wp_die( 0 );
}

/**
 * Ajax handler for sending a post to the trash.
 *
 * @since 3.1.0
 *
 * @param string $action Action to perform.
 */
function wp_ajax_trash_post( $action ) {
	if ( empty( $action ) )
		$action = 'trash-post';
	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;

	check_ajax_referer( "{$action}_$id" );
	if ( !current_user_can( 'delete_post', $id ) )
		wp_die( -1 );

	if ( !get_post( $id ) )
		wp_die( 1 );

	if ( 'trash-post' == $action )
		$done = wp_trash_post( $id );
	else
		$done = wp_untrash_post( $id );

	if ( $done )
		wp_die( 1 );

	wp_die( 0 );
}

/**
 * Ajax handler to restore a post from the trash.
 *
 * @since 3.1.0
 *
 * @param string $action Action to perform.
 */
function wp_ajax_untrash_post( $action ) {
	if ( empty( $action ) )
		$action = 'untrash-post';
	wp_ajax_trash_post( $action );
}

function wp_ajax_delete_page( $action ) {
	if ( empty( $action ) )
		$action = 'delete-page';
	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;

	check_ajax_referer( "{$action}_$id" );
	if ( !current_user_can( 'delete_page', $id ) )
		wp_die( -1 );

	if ( ! get_post( $id ) )
		wp_die( 1 );

	if ( wp_delete_post( $id ) )
		wp_die( 1 );
	else
		wp_die( 0 );
}

/**
 * Ajax handler to dim a comment.
 *
 * @since 3.1.0
 */
function wp_ajax_dim_comment() {
	$id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;

	if ( !$comment = get_comment( $id ) ) {
		$x = new WP_Ajax_Response( array(
			'what' => 'comment',
			'id' => new WP_Error('invalid_comment', sprintf(__('Comment %d does not exist'), $id))
		) );
		$x->send();
	}

	if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) && ! current_user_can( 'moderate_comments' ) )
		wp_die( -1 );

	$current = wp_get_comment_status( $comment->comment_ID );
	if ( isset( $_POST['new'] ) && $_POST['new'] == $current )
		wp_die( time() );

	check_ajax_referer( "approve-comment_$id" );
	if ( in_array( $current, array( 'unapproved', 'spam' ) ) )
		$result = wp_set_comment_status( $comment->comment_ID, 'approve', true );
	else
		$result = wp_set_comment_status( $comment->comment_ID, 'hold', true );

	if ( is_wp_error($result) ) {
		$x = new WP_Ajax_Response( array(
			'what' => 'comment',
			'id' => $result
		) );
		$x->send();
	}

	// Decide if we need to send back '1' or a more complicated response including page links and comment counts
	_wp_ajax_delete_comment_response( $comment->comment_ID );
	wp_die( 0 );
}

/**
 * Ajax handler for deleting a link category.
 *
 * @since 3.1.0
 *
 * @param string $action Action to perform.
 */
function wp_ajax_add_link_category( $action ) {
	if ( empty( $action ) )
		$action = 'add-link-category';
	check_ajax_referer( $action );
	if ( !current_user_can( 'manage_categories' ) )
		wp_die( -1 );
	$names = explode(',', wp_unslash( $_POST['newcat'] ) );
	$x = new WP_Ajax_Response();
	foreach ( $names as $cat_name ) {
		$cat_name = trim($cat_name);
		$slug = sanitize_title($cat_name);
		if ( '' === $slug )
			continue;
		if ( !$cat_id = term_exists( $cat_name, 'link_category' ) )
			$cat_id = wp_insert_term( $cat_name, 'link_category' );
		if ( is_wp_error( $cat_id ) )
			continue;
		else if ( is_array( $cat_id ) )
			$cat_id = $cat_id['term_id'];
		$cat_name = esc_html( $cat_name );
		$x->add( array(
			'what' => 'link-category',
			'id' => $cat_id,
			'data' => "<li id='link-category-$cat_id'><label for='in-link-category-$cat_id' class='selectit'><input value='" . esc_attr($cat_id) . "' type='checkbox' checked='checked' name='link_category[]' id='in-link-category-$cat_id'/> $cat_name</label></li>",
			'position' => -1
		) );
	}
	$x->send();
}

/**
 * Ajax handler to add a tag.
 *
 * @since 3.1.0
 */
function wp_ajax_add_tag() {
	global $wp_list_table;

	check_ajax_referer( 'add-tag', '_wpnonce_add-tag' );
	$taxonomy = !empty($_POST['taxonomy']) ? $_POST['taxonomy'] : 'post_tag';
	$tax = get_taxonomy($taxonomy);

	if ( !current_user_can( $tax->cap->edit_terms ) )
		wp_die( -1 );

	$x = new WP_Ajax_Response();

	$tag = wp_insert_term($_POST['tag-name'], $taxonomy, $_POST );

	if ( !$tag || is_wp_error($tag) || (!$tag = get_term( $tag['term_id'], $taxonomy )) ) {
		$message = __('An error has occurred. Please reload the page and try again.');
		if ( is_wp_error($tag) && $tag->get_error_message() )
			$message = $tag->get_error_message();

		$x->add( array(
			'what' => 'taxonomy',
			'data' => new WP_Error('error', $message )
		) );
		$x->send();
	}

	$wp_list_table = _get_list_table( 'WP_Terms_List_Table', array( 'screen' => $_POST['screen'] ) );

	$level = 0;
	if ( is_taxonomy_hierarchical($taxonomy) ) {
		$level = count( get_ancestors( $tag->term_id, $taxonomy, 'taxonomy' ) );
		ob_start();
		$wp_list_table->single_row( $tag, $level );
		$noparents = ob_get_clean();
	}

	ob_start();
	$wp_list_table->single_row( $tag );
	$parents = ob_get_clean();

	$x->add( array(
		'what' => 'taxonomy',
		'supplemental' => compact('parents', 'noparents')
		) );
	$x->add( array(
		'what' => 'term',
		'position' => $level,
		'supplemental' => (array) $tag
		) );
	$x->send();
}

/**
 * Ajax handler for getting a tagcloud.
 *
 * @since 3.1.0
 */
function wp_ajax_get_tagcloud() {
	if ( ! isset( $_POST['tax'] ) ) {
		wp_die( 0 );
	}

	$taxonomy = sanitize_key( $_POST['tax'] );
	$tax = get_taxonomy( $taxonomy );
	if ( ! $tax ) {
		wp_die( 0 );
	}
	
	if ( ! current_user_can( $tax->cap->assign_terms ) ) {
		wp_die( -1 );
	}

	$tags = get_terms( $taxonomy, array( 'number' => 45, 'orderby' => 'count', 'order' => 'DESC' ) );

	if ( empty( $tags ) )
		wp_die( $tax->labels->not_found );

	if ( is_wp_error( $tags ) )
		wp_die( $tags->get_error_message() );

	foreach ( $tags as $key => $tag ) {
		$tags[ $key ]->link = '#';
		$tags[ $key ]->id = $tag->term_id;
	}

	// We need raw tag names here, so don't filter the output
	$return = wp_generate_tag_cloud( $tags, array('filter' => 0) );

	if ( empty($return) )
		wp_die( 0 );

	echo $return;

	wp_die();
}

/**
 * Ajax handler for getting comments.
 *
 * @since 3.1.0
 *
 * @param string $action Action to perform.
 */
function wp_ajax_get_comments( $action ) {
	global $wp_list_table, $post_id;
	if ( empty( $action ) )
		$action = 'get-comments';

	check_ajax_referer( $action );

	if ( empty( $post_id ) && ! empty( $_REQUEST['p'] ) ) {
		$id = absint( $_REQUEST['p'] );
		if ( ! empty( $id ) )
			$post_id = $id;
	}

	if ( empty( $post_id ) )
		wp_die( -1 );

	$wp_list_table = _get_list_table( 'WP_Post_Comments_List_Table', array( 'screen' => 'edit-comments' ) );

	if ( ! current_user_can( 'edit_post', $post_id ) )
		wp_die( -1 );

	$wp_list_table->prepare_items();

	if ( !$wp_list_table->has_items() )
		wp_die( 1 );

	$x = new WP_Ajax_Response();
	ob_start();
	foreach ( $wp_list_table->items as $comment ) {
		if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) )
			continue;
		get_comment( $comment );
		$wp_list_table->single_row( $comment );
	}
	$comment_list_item = ob_get_contents();
	ob_end_clean();

	$x->add( array(
		'what' => 'comments',
		'data' => $comment_list_item
	) );
	$x->send();
}

/**
 * Ajax handler for replying to a comment.
 *
 * @since 3.1.0
 *
 * @param string $action Action to perform.
 */
function wp_ajax_replyto_comment( $action ) {
	global $wp_list_table;
	if ( empty( $action ) )
		$action = 'replyto-comment';

	check_ajax_referer( $action, '_ajax_nonce-replyto-comment' );

	$comment_post_ID = (int) $_POST['comment_post_ID'];
	$post = get_post( $comment_post_ID );
	if ( ! $post )
		wp_die( -1 );

	if ( !current_user_can( 'edit_post', $comment_post_ID ) )
		wp_die( -1 );

	if ( empty( $post->post_status ) )
		wp_die( 1 );
	elseif ( in_array($post->post_status, array('draft', 'pending', 'trash') ) )
		wp_die( __('ERROR: you are replying to a comment on a draft post.') );

	$user = wp_get_current_user();
	if ( $user->exists() ) {
		$user_ID = $user->ID;
		$comment_author       = wp_slash( $user->display_name );
		$comment_author_email = wp_slash( $user->user_email );
		$comment_author_url   = wp_slash( $user->user_url );
		$comment_content      = trim( $_POST['content'] );
		$comment_type         = isset( $_POST['comment_type'] ) ? trim( $_POST['comment_type'] ) : '';
		if ( current_user_can( 'unfiltered_html' ) ) {
			if ( ! isset( $_POST['_wp_unfiltered_html_comment'] ) )
				$_POST['_wp_unfiltered_html_comment'] = '';

			if ( wp_create_nonce( 'unfiltered-html-comment' ) != $_POST['_wp_unfiltered_html_comment'] ) {
				kses_remove_filters(); // start with a clean slate
				kses_init_filters(); // set up the filters
			}
		}
	} else {
		wp_die( __( 'Sorry, you must be logged in to reply to a comment.' ) );
	}

	if ( '' == $comment_content )
		wp_die( __( 'ERROR: please type a comment.' ) );

	$comment_parent = 0;
	if ( isset( $_POST['comment_ID'] ) )
		$comment_parent = absint( $_POST['comment_ID'] );
	$comment_auto_approved = false;
	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

	// Automatically approve parent comment.
	if ( !empty($_POST['approve_parent']) ) {
		$parent = get_comment( $comment_parent );

		if ( $parent && $parent->comment_approved === '0' && $parent->comment_post_ID == $comment_post_ID ) {
			if ( wp_set_comment_status( $parent->comment_ID, 'approve' ) )
				$comment_auto_approved = true;
		}
	}

	$comment_id = wp_new_comment( $commentdata );
	$comment = get_comment($comment_id);
	if ( ! $comment ) wp_die( 1 );

	$position = ( isset($_POST['position']) && (int) $_POST['position'] ) ? (int) $_POST['position'] : '-1';

	ob_start();
	if ( isset( $_REQUEST['mode'] ) && 'dashboard' == $_REQUEST['mode'] ) {
		require_once( ABSPATH . 'wp-admin/includes/dashboard.php' );
		_wp_dashboard_recent_comments_row( $comment );
	} else {
		if ( isset( $_REQUEST['mode'] ) && 'single' == $_REQUEST['mode'] ) {
			$wp_list_table = _get_list_table('WP_Post_Comments_List_Table', array( 'screen' => 'edit-comments' ) );
		} else {
			$wp_list_table = _get_list_table('WP_Comments_List_Table', array( 'screen' => 'edit-comments' ) );
		}
		$wp_list_table->single_row( $comment );
	}
	$comment_list_item = ob_get_clean();

	$response =  array(
		'what' => 'comment',
		'id' => $comment->comment_ID,
		'data' => $comment_list_item,
		'position' => $position
	);

	if ( $comment_auto_approved )
		$response['supplemental'] = array( 'parent_approved' => $parent->comment_ID );

	$x = new WP_Ajax_Response();
	$x->add( $response );
	$x->send();
}

/**
 * Ajax handler for editing a comment.
 *
 * @since 3.1.0
 */
function wp_ajax_edit_comment() {
	global $wp_list_table;

	check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment' );

	$comment_id = (int) $_POST['comment_ID'];
	if ( ! current_user_can( 'edit_comment', $comment_id ) )
		wp_die( -1 );

	if ( '' == $_POST['content'] )
		wp_die( __( 'ERROR: please type a comment.' ) );

	if ( isset( $_POST['status'] ) )
		$_POST['comment_status'] = $_POST['status'];
	edit_comment();

	$position = ( isset($_POST['position']) && (int) $_POST['position']) ? (int) $_POST['position'] : '-1';
	$checkbox = ( isset($_POST['checkbox']) && true == $_POST['checkbox'] ) ? 1 : 0;
	$wp_list_table = _get_list_table( $checkbox ? 'WP_Comments_List_Table' : 'WP_Post_Comments_List_Table', array( 'screen' => 'edit-comments' ) );

	$comment = get_comment( $comment_id );
	if ( empty( $comment->comment_ID ) )
		wp_die( -1 );

	ob_start();
	$wp_list_table->single_row( $comment );
	$comment_list_item = ob_get_clean();

	$x = new WP_Ajax_Response();

	$x->add( array(
		'what' => 'edit_comment',
		'id' => $comment->comment_ID,
		'data' => $comment_list_item,
		'position' => $position
	));

	$x->send();
}

/**
 * Ajax handler for adding a menu item.
 *
 * @since 3.1.0
 */
function wp_ajax_add_menu_item() {
	check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );

	if ( ! current_user_can( 'edit_theme_options' ) )
		wp_die( -1 );

	require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

	// For performance reasons, we omit some object properties from the checklist.
	// The following is a hacky way to restore them when adding non-custom items.

	$menu_items_data = array();
	foreach ( (array) $_POST['menu-item'] as $menu_item_data ) {
		if (
			! empty( $menu_item_data['menu-item-type'] ) &&
			'custom' != $menu_item_data['menu-item-type'] &&
			! empty( $menu_item_data['menu-item-object-id'] )
		) {
			switch( $menu_item_data['menu-item-type'] ) {
				case 'post_type' :
					$_object = get_post( $menu_item_data['menu-item-object-id'] );
				break;

				case 'taxonomy' :
					$_object = get_term( $menu_item_data['menu-item-object-id'], $menu_item_data['menu-item-object'] );
				break;
			}

			$_menu_items = array_map( 'wp_setup_nav_menu_item', array( $_object ) );
			$_menu_item = array_shift( $_menu_items );

			// Restore the missing menu item properties
			$menu_item_data['menu-item-description'] = $_menu_item->description;
		}

		$menu_items_data[] = $menu_item_data;
	}

	$item_ids = wp_save_nav_menu_items( 0, $menu_items_data );
	if ( is_wp_error( $item_ids ) )
		wp_die( 0 );

	$menu_items = array();

	foreach ( (array) $item_ids as $menu_item_id ) {
		$menu_obj = get_post( $menu_item_id );
		if ( ! empty( $menu_obj->ID ) ) {
			$menu_obj = wp_setup_nav_menu_item( $menu_obj );
			$menu_obj->label = $menu_obj->title; // don't show "(pending)" in ajax-added items
			$menu_items[] = $menu_obj;
		}
	}

	/** This filter is documented in wp-admin/includes/nav-menu.php */
	$walker_class_name = apply_filters( 'wp_edit_nav_menu_walker', 'Walker_Nav_Menu_Edit', $_POST['menu'] );

	if ( ! class_exists( $walker_class_name ) )
		wp_die( 0 );

	if ( ! empty( $menu_items ) ) {
		$args = array(
			'after' => '',
			'before' => '',
			'link_after' => '',
			'link_before' => '',
			'walker' => new $walker_class_name,
		);
		echo walk_nav_menu_tree( $menu_items, 0, (object) $args );
	}
	wp_die();
}

/**
 * Ajax handler for adding meta.
 *
 * @since 3.1.0
 */
function wp_ajax_add_meta() {
	check_ajax_referer( 'add-meta', '_ajax_nonce-add-meta' );
	$c = 0;
	$pid = (int) $_POST['post_id'];
	$post = get_post( $pid );

	if ( isset($_POST['metakeyselect']) || isset($_POST['metakeyinput']) ) {
		if ( !current_user_can( 'edit_post', $pid ) )
			wp_die( -1 );
		if ( isset($_POST['metakeyselect']) && '#NONE#' == $_POST['metakeyselect'] && empty($_POST['metakeyinput']) )
			wp_die( 1 );

		// If the post is an autodraft, save the post as a draft and then attempt to save the meta.
		if ( $post->post_status == 'auto-draft' ) {
			$save_POST = $_POST; // Backup $_POST
			$_POST = array(); // Make it empty for edit_post()
			$_POST['action'] = 'draft'; // Warning fix
			$_POST['post_ID'] = $pid;
			$_POST['post_type'] = $post->post_type;
			$_POST['post_status'] = 'draft';
			$now = current_time('timestamp', 1);
			$_POST['post_title'] = sprintf( __( 'Draft created on %1$s at %2$s' ), date( get_option( 'date_format' ), $now ), date( get_option( 'time_format' ), $now ) );

			if ( $pid = edit_post() ) {
				if ( is_wp_error( $pid ) ) {
					$x = new WP_Ajax_Response( array(
						'what' => 'meta',
						'data' => $pid
					) );
					$x->send();
				}
				$_POST = $save_POST; // Now we can restore original $_POST again
				if ( !$mid = add_meta( $pid ) )
					wp_die( __( 'Please provide a custom field value.' ) );
			} else {
				wp_die( 0 );
			}
		} else if ( !$mid = add_meta( $pid ) ) {
			wp_die( __( 'Please provide a custom field value.' ) );
		}

		$meta = get_metadata_by_mid( 'post', $mid );
		$pid = (int) $meta->post_id;
		$meta = get_object_vars( $meta );
		$x = new WP_Ajax_Response( array(
			'what' => 'meta',
			'id' => $mid,
			'data' => _list_meta_row( $meta, $c ),
			'position' => 1,
			'supplemental' => array('postid' => $pid)
		) );
	} else { // Update?
		$mid = (int) key( $_POST['meta'] );
		$key = wp_unslash( $_POST['meta'][$mid]['key'] );
		$value = wp_unslash( $_POST['meta'][$mid]['value'] );
		if ( '' == trim($key) )
			wp_die( __( 'Please provide a custom field name.' ) );
		if ( '' == trim($value) )
			wp_die( __( 'Please provide a custom field value.' ) );
		if ( ! $meta = get_metadata_by_mid( 'post', $mid ) )
			wp_die( 0 ); // if meta doesn't exist
		if ( is_protected_meta( $meta->meta_key, 'post' ) || is_protected_meta( $key, 'post' ) ||
			! current_user_can( 'edit_post_meta', $meta->post_id, $meta->meta_key ) ||
			! current_user_can( 'edit_post_meta', $meta->post_id, $key ) )
			wp_die( -1 );
		if ( $meta->meta_value != $value || $meta->meta_key != $key ) {
			if ( !$u = update_metadata_by_mid( 'post', $mid, $value, $key ) )
				wp_die( 0 ); // We know meta exists; we also know it's unchanged (or DB error, in which case there are bigger problems).
		}

		$x = new WP_Ajax_Response( array(
			'what' => 'meta',
			'id' => $mid, 'old_id' => $mid,
			'data' => _list_meta_row( array(
				'meta_key' => $key,
				'meta_value' => $value,
				'meta_id' => $mid
			), $c ),
			'position' => 0,
			'supplemental' => array('postid' => $meta->post_id)
		) );
	}
	$x->send();
}

/**
 * Ajax handler for adding a user.
 *
 * @since 3.1.0
 *
 * @param string $action Action to perform.
 */
function wp_ajax_add_user( $action ) {
	global $wp_list_table;
	if ( empty( $action ) )
		$action = 'add-user';

	check_ajax_referer( $action );
	if ( ! current_user_can('create_users') )
		wp_die( -1 );
	if ( ! $user_id = edit_user() ) {
		wp_die( 0 );
	} elseif ( is_wp_error( $user_id ) ) {
		$x = new WP_Ajax_Response( array(
			'what' => 'user',
			'id' => $user_id
		) );
		$x->send();
	}
	$user_object = get_userdata( $user_id );

	$wp_list_table = _get_list_table('WP_Users_List_Table');

	$role = current( $user_object->roles );

	$x = new WP_Ajax_Response( array(
		'what' => 'user',
		'id' => $user_id,
		'data' => $wp_list_table->single_row( $user_object, '', $role ),
		'supplemental' => array(
			'show-link' => sprintf(__( 'User <a href="#%s">%s</a> added' ), "user-$user_id", $user_object->user_login),
			'role' => $role,
		)
	) );
	$x->send();
}

/**
 * Ajax handler for closed post boxes.
 *
 * @since 3.1.0
 */
function wp_ajax_closed_postboxes() {
	check_ajax_referer( 'closedpostboxes', 'closedpostboxesnonce' );
	$closed = isset( $_POST['closed'] ) ? explode( ',', $_POST['closed']) : array();
	$closed = array_filter($closed);

	$hidden = isset( $_POST['hidden'] ) ? explode( ',', $_POST['hidden']) : array();
	$hidden = array_filter($hidden);

	$page = isset( $_POST['page'] ) ? $_POST['page'] : '';

	if ( $page != sanitize_key( $page ) )
		wp_die( 0 );

	if ( ! $user = wp_get_current_user() )
		wp_die( -1 );

	if ( is_array($closed) )
		update_user_option($user->ID, "closedpostboxes_$page", $closed, true);

	if ( is_array($hidden) ) {
		$hidden = array_diff( $hidden, array('submitdiv', 'linksubmitdiv', 'manage-menu', 'create-menu') ); // postboxes that are always shown
		update_user_option($user->ID, "metaboxhidden_$page", $hidden, true);
	}

	wp_die( 1 );
}

/**
 * Ajax handler for hidden columns.
 *
 * @since 3.1.0
 */
function wp_ajax_hidden_columns() {
	check_ajax_referer( 'screen-options-nonce', 'screenoptionnonce' );
	$hidden = explode( ',', isset( $_POST['hidden'] ) ? $_POST['hidden'] : '' );
	$page = isset( $_POST['page'] ) ? $_POST['page'] : '';

	if ( $page != sanitize_key( $page ) )
		wp_die( 0 );

	if ( ! $user = wp_get_current_user() )
		wp_die( -1 );

	if ( is_array($hidden) )
		update_user_option($user->ID, "manage{$page}columnshidden", $hidden, true);

	wp_die( 1 );
}

/**
 * Ajax handler for updating whether to display the welcome panel.
 *
 * @since 3.1.0
 */
function wp_ajax_update_welcome_panel() {
	check_ajax_referer( 'welcome-panel-nonce', 'welcomepanelnonce' );

	if ( ! current_user_can( 'edit_theme_options' ) )
		wp_die( -1 );

	update_user_meta( get_current_user_id(), 'show_welcome_panel', empty( $_POST['visible'] ) ? 0 : 1 );

	wp_die( 1 );
}

/**
 * Ajax handler for retrieving menu meta boxes.
 *
 * @since 3.1.0
 */
function wp_ajax_menu_get_metabox() {
	if ( ! current_user_can( 'edit_theme_options' ) )
		wp_die( -1 );

	require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

	if ( isset( $_POST['item-type'] ) && 'post_type' == $_POST['item-type'] ) {
		$type = 'posttype';
		$callback = 'wp_nav_menu_item_post_type_meta_box';
		$items = (array) get_post_types( array( 'show_in_nav_menus' => true ), 'object' );
	} elseif ( isset( $_POST['item-type'] ) && 'taxonomy' == $_POST['item-type'] ) {
		$type = 'taxonomy';
		$callback = 'wp_nav_menu_item_taxonomy_meta_box';
		$items = (array) get_taxonomies( array( 'show_ui' => true ), 'object' );
	}

	if ( ! empty( $_POST['item-object'] ) && isset( $items[$_POST['item-object']] ) ) {
		$menus_meta_box_object = $items[ $_POST['item-object'] ];

		/** This filter is documented in wp-admin/includes/nav-menu.php */
		$item = apply_filters( 'nav_menu_meta_box_object', $menus_meta_box_object );
		ob_start();
		call_user_func_array($callback, array(
			null,
			array(
				'id' => 'add-' . $item->name,
				'title' => $item->labels->name,
				'callback' => $callback,
				'args' => $item,
			)
		));

		$markup = ob_get_clean();

		echo wp_json_encode(array(
			'replace-id' => $type . '-' . $item->name,
			'markup' => $markup,
		));
	}

	wp_die();
}

/**
 * Ajax handler for internal linking.
 *
 * @since 3.1.0
 */
function wp_ajax_wp_link_ajax() {
	check_ajax_referer( 'internal-linking', '_ajax_linking_nonce' );

	$args = array();

	if ( isset( $_POST['search'] ) )
		$args['s'] = wp_unslash( $_POST['search'] );
	$args['pagenum'] = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

	require(ABSPATH . WPINC . '/class-wp-editor.php');
	$results = _WP_Editors::wp_link_query( $args );

	if ( ! isset( $results ) )
		wp_die( 0 );

	echo wp_json_encode( $results );
	echo "\n";

	wp_die();
}

/**
 * Ajax handler for menu locations save.
 *
 * @since 3.1.0
 */
function wp_ajax_menu_locations_save() {
	if ( ! current_user_can( 'edit_theme_options' ) )
		wp_die( -1 );
	check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );
	if ( ! isset( $_POST['menu-locations'] ) )
		wp_die( 0 );
	set_theme_mod( 'nav_menu_locations', array_map( 'absint', $_POST['menu-locations'] ) );
	wp_die( 1 );
}

/**
 * Ajax handler for saving the meta box order.
 *
 * @since 3.1.0
 */
function wp_ajax_meta_box_order() {
	check_ajax_referer( 'meta-box-order' );
	$order = isset( $_POST['order'] ) ? (array) $_POST['order'] : false;
	$page_columns = isset( $_POST['page_columns'] ) ? $_POST['page_columns'] : 'auto';

	if ( $page_columns != 'auto' )
		$page_columns = (int) $page_columns;

	$page = isset( $_POST['page'] ) ? $_POST['page'] : '';

	if ( $page != sanitize_key( $page ) )
		wp_die( 0 );

	if ( ! $user = wp_get_current_user() )
		wp_die( -1 );

	if ( $order )
		update_user_option($user->ID, "meta-box-order_$page", $order, true);

	if ( $page_columns )
		update_user_option($user->ID, "screen_layout_$page", $page_columns, true);

	wp_die( 1 );
}

/**
 * Ajax handler for menu quick searching.
 *
 * @since 3.1.0
 */
function wp_ajax_menu_quick_search() {
	if ( ! current_user_can( 'edit_theme_options' ) )
		wp_die( -1 );

	require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

	_wp_ajax_menu_quick_search( $_POST );

	wp_die();
}

/**
 * Ajax handler to retrieve a permalink.
 *
 * @since 3.1.0
 */
function wp_ajax_get_permalink() {
	check_ajax_referer( 'getpermalink', 'getpermalinknonce' );
	$post_id = isset($_POST['post_id'])? intval($_POST['post_id']) : 0;
	wp_die( add_query_arg( array( 'preview' => 'true' ), get_permalink( $post_id ) ) );
}

/**
 * Ajax handler to retrieve a sample permalink.
 *
 * @since 3.1.0
 */
function wp_ajax_sample_permalink() {
	check_ajax_referer( 'samplepermalink', 'samplepermalinknonce' );
	$post_id = isset($_POST['post_id'])? intval($_POST['post_id']) : 0;
	$title = isset($_POST['new_title'])? $_POST['new_title'] : '';
	$slug = isset($_POST['new_slug'])? $_POST['new_slug'] : null;
	wp_die( get_sample_permalink_html( $post_id, $title, $slug ) );
}

/**
 * Ajax handler for Quick Edit saving a post from a list table.
 *
 * @since 3.1.0
 */
function wp_ajax_inline_save() {
	global $wp_list_table;

	check_ajax_referer( 'inlineeditnonce', '_inline_edit' );

	if ( ! isset($_POST['post_ID']) || ! ( $post_ID = (int) $_POST['post_ID'] ) )
		wp_die();

	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_ID ) )
			wp_die( __( 'You are not allowed to edit this page.' ) );
	} else {
		if ( ! current_user_can( 'edit_post', $post_ID ) )
			wp_die( __( 'You are not allowed to edit this post.' ) );
	}

	if ( $last = wp_check_post_lock( $post_ID ) ) {
		$last_user = get_userdata( $last );
		$last_user_name = $last_user ? $last_user->display_name : __( 'Someone' );
		printf( $_POST['post_type'] == 'page' ? __( 'Saving is disabled: %s is currently editing this page.' ) : __( 'Saving is disabled: %s is currently editing this post.' ),	esc_html( $last_user_name ) );
		wp_die();
	}

	$data = &$_POST;

	$post = get_post( $post_ID, ARRAY_A );

	// Since it's coming from the database.
	$post = wp_slash($post);

	$data['content'] = $post['post_content'];
	$data['excerpt'] = $post['post_excerpt'];

	// Rename.
	$data['user_ID'] = get_current_user_id();

	if ( isset($data['post_parent']) )
		$data['parent_id'] = $data['post_parent'];

	// Status.
	if ( isset( $data['keep_private'] ) && 'private' == $data['keep_private'] ) {
		$data['visibility']  = 'private';
		$data['post_status'] = 'private';
	} else {
		$data['post_status'] = $data['_status'];
	}

	if ( empty($data['comment_status']) )
		$data['comment_status'] = 'closed';
	if ( empty($data['ping_status']) )
		$data['ping_status'] = 'closed';

	// Hack: wp_unique_post_slug() doesn't work for drafts, so we will fake that our post is published.
	if ( ! empty( $data['post_name'] ) && in_array( $post['post_status'], array( 'draft', 'pending' ) ) ) {
		$post['post_status'] = 'publish';
		$data['post_name'] = wp_unique_post_slug( $data['post_name'], $post['ID'], $post['post_status'], $post['post_type'], $post['post_parent'] );
	}

	// Update the post.
	edit_post();

	$wp_list_table = _get_list_table( 'WP_Posts_List_Table', array( 'screen' => $_POST['screen'] ) );

	$level = 0;
	$request_post = array( get_post( $_POST['post_ID'] ) );
	$parent = $request_post[0]->post_parent;

	while ( $parent > 0 ) {
		$parent_post = get_post( $parent );
		$parent = $parent_post->post_parent;
		$level++;
	}

	$wp_list_table->display_rows( array( get_post( $_POST['post_ID'] ) ), $level );

	wp_die();
}

/**
 * Ajax handler for quick edit saving for a term.
 *
 * @since 3.1.0
 */
function wp_ajax_inline_save_tax() {
	global $wp_list_table;

	check_ajax_referer( 'taxinlineeditnonce', '_inline_edit' );

	$taxonomy = sanitize_key( $_POST['taxonomy'] );
	$tax = get_taxonomy( $taxonomy );
	if ( ! $tax )
		wp_die( 0 );

	if ( ! current_user_can( $tax->cap->edit_terms ) )
		wp_die( -1 );

	$wp_list_table = _get_list_table( 'WP_Terms_List_Table', array( 'screen' => 'edit-' . $taxonomy ) );

	if ( ! isset($_POST['tax_ID']) || ! ( $id = (int) $_POST['tax_ID'] ) )
		wp_die( -1 );

	$tag = get_term( $id, $taxonomy );
	$_POST['description'] = $tag->description;

	$updated = wp_update_term($id, $taxonomy, $_POST);
	if ( $updated && !is_wp_error($updated) ) {
		$tag = get_term( $updated['term_id'], $taxonomy );
		if ( !$tag || is_wp_error( $tag ) ) {
			if ( is_wp_error($tag) && $tag->get_error_message() )
				wp_die( $tag->get_error_message() );
			wp_die( __( 'Item not updated.' ) );
		}
	} else {
		if ( is_wp_error($updated) && $updated->get_error_message() )
			wp_die( $updated->get_error_message() );
		wp_die( __( 'Item not updated.' ) );
	}
	$level = 0;
	$parent = $tag->parent;
	while ( $parent > 0 ) {
		$parent_tag = get_term( $parent, $taxonomy );
		$parent = $parent_tag->parent;
		$level++;
	}
	$wp_list_table->single_row( $tag, $level );
	wp_die();
}

/**
 * Ajax handler for querying posts for the Find Posts modal.
 *
 * @see window.findPosts
 *
 * @since 3.1.0
 */
function wp_ajax_find_posts() {
	check_ajax_referer( 'find-posts' );

	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	unset( $post_types['attachment'] );

	$s = wp_unslash( $_POST['ps'] );
	$args = array(
		'post_type' => array_keys( $post_types ),
		'post_status' => 'any',
		'posts_per_page' => 50,
	);
	if ( '' !== $s )
		$args['s'] = $s;

	$posts = get_posts( $args );

	if ( ! $posts ) {
		wp_send_json_error( __( 'No items found.' ) );
	}

	$html = '<table class="widefat"><thead><tr><th class="found-radio"><br /></th><th>'.__('Title').'</th><th class="no-break">'.__('Type').'</th><th class="no-break">'.__('Date').'</th><th class="no-break">'.__('Status').'</th></tr></thead><tbody>';
	$alt = '';
	foreach ( $posts as $post ) {
		$title = trim( $post->post_title ) ? $post->post_title : __( '(no title)' );
		$alt = ( 'alternate' == $alt ) ? '' : 'alternate';

		switch ( $post->post_status ) {
			case 'publish' :
			case 'private' :
				$stat = __('Published');
				break;
			case 'future' :
				$stat = __('Scheduled');
				break;
			case 'pending' :
				$stat = __('Pending Review');
				break;
			case 'draft' :
				$stat = __('Draft');
				break;
		}

		if ( '0000-00-00 00:00:00' == $post->post_date ) {
			$time = '';
		} else {
			/* translators: date format in table columns, see http://php.net/date */
			$time = mysql2date(__('Y/m/d'), $post->post_date);
		}

		$html .= '<tr class="' . trim( 'found-posts ' . $alt ) . '"><td class="found-radio"><input type="radio" id="found-'.$post->ID.'" name="found_post_id" value="' . esc_attr($post->ID) . '"></td>';
		$html .= '<td><label for="found-'.$post->ID.'">' . esc_html( $title ) . '</label></td><td class="no-break">' . esc_html( $post_types[$post->post_type]->labels->singular_name ) . '</td><td class="no-break">'.esc_html( $time ) . '</td><td class="no-break">' . esc_html( $stat ). ' </td></tr>' . "\n\n";
	}

	$html .= '</tbody></table>';

	wp_send_json_success( $html );
}

/**
 * Ajax handler for saving the widgets order.
 *
 * @since 3.1.0
 */
function wp_ajax_widgets_order() {
	check_ajax_referer( 'save-sidebar-widgets', 'savewidgets' );

	if ( !current_user_can('edit_theme_options') )
		wp_die( -1 );

	unset( $_POST['savewidgets'], $_POST['action'] );

	// Save widgets order for all sidebars.
	if ( is_array($_POST['sidebars']) ) {
		$sidebars = array();
		foreach ( $_POST['sidebars'] as $key => $val ) {
			$sb = array();
			if ( !empty($val) ) {
				$val = explode(',', $val);
				foreach ( $val as $k => $v ) {
					if ( strpos($v, 'widget-') === false )
						continue;

					$sb[$k] = substr($v, strpos($v, '_') + 1);
				}
			}
			$sidebars[$key] = $sb;
		}
		wp_set_sidebars_widgets($sidebars);
		wp_die( 1 );
	}

	wp_die( -1 );
}

/**
 * Ajax handler for saving a widget.
 *
 * @since 3.1.0
 */
function wp_ajax_save_widget() {
	global $wp_registered_widgets, $wp_registered_widget_controls, $wp_registered_widget_updates;

	check_ajax_referer( 'save-sidebar-widgets', 'savewidgets' );

	if ( !current_user_can('edit_theme_options') || !isset($_POST['id_base']) )
		wp_die( -1 );

	unset( $_POST['savewidgets'], $_POST['action'] );

	/**
	 * Fires early when editing the widgets displayed in sidebars.
	 *
	 * @since 2.8.0
	 */
	do_action( 'load-widgets.php' );

	/**
	 * Fires early when editing the widgets displayed in sidebars.
	 *
	 * @since 2.8.0
	 */
	do_action( 'widgets.php' );

	/** This action is documented in wp-admin/widgets.php */
	do_action( 'sidebar_admin_setup' );

	$id_base = $_POST['id_base'];
	$widget_id = $_POST['widget-id'];
	$sidebar_id = $_POST['sidebar'];
	$multi_number = !empty($_POST['multi_number']) ? (int) $_POST['multi_number'] : 0;
	$settings = isset($_POST['widget-' . $id_base]) && is_array($_POST['widget-' . $id_base]) ? $_POST['widget-' . $id_base] : false;
	$error = '<p>' . __('An error has occurred. Please reload the page and try again.') . '</p>';

	$sidebars = wp_get_sidebars_widgets();
	$sidebar = isset($sidebars[$sidebar_id]) ? $sidebars[$sidebar_id] : array();

	// Delete.
	if ( isset($_POST['delete_widget']) && $_POST['delete_widget'] ) {

		if ( !isset($wp_registered_widgets[$widget_id]) )
			wp_die( $error );

		$sidebar = array_diff( $sidebar, array($widget_id) );
		$_POST = array('sidebar' => $sidebar_id, 'widget-' . $id_base => array(), 'the-widget-id' => $widget_id, 'delete_widget' => '1');
	} elseif ( $settings && preg_match( '/__i__|%i%/', key($settings) ) ) {
		if ( !$multi_number )
			wp_die( $error );

		$_POST['widget-' . $id_base] = array( $multi_number => array_shift($settings) );
		$widget_id = $id_base . '-' . $multi_number;
		$sidebar[] = $widget_id;
	}
	$_POST['widget-id'] = $sidebar;

	foreach ( (array) $wp_registered_widget_updates as $name => $control ) {

		if ( $name == $id_base ) {
			if ( !is_callable( $control['callback'] ) )
				continue;

			ob_start();
				call_user_func_array( $control['callback'], $control['params'] );
			ob_end_clean();
			break;
		}
	}

	if ( isset($_POST['delete_widget']) && $_POST['delete_widget'] ) {
		$sidebars[$sidebar_id] = $sidebar;
		wp_set_sidebars_widgets($sidebars);
		echo "deleted:$widget_id";
		wp_die();
	}

	if ( !empty($_POST['add_new']) )
		wp_die();

	if ( $form = $wp_registered_widget_controls[$widget_id] )
		call_user_func_array( $form['callback'], $form['params'] );

	wp_die();
}

/**
 * Ajax handler for saving a widget.
 *
 * @since 3.9.0
 */
function wp_ajax_update_widget() {
	global $wp_customize;
	$wp_customize->widgets->wp_ajax_update_widget();
}

/**
 * Ajax handler for uploading attachments
 *
 * @since 3.3.0
 */
function wp_ajax_upload_attachment() {
	check_ajax_referer( 'media-form' );
	/*
	 * This function does not use wp_send_json_success() / wp_send_json_error()
	 * as the html4 Plupload handler requires a text/html content-type for older IE.
	 * See https://core.trac.wordpress.org/ticket/31037
	 */

	if ( ! current_user_can( 'upload_files' ) ) {
		echo wp_json_encode( array(
			'success' => false,
			'data'    => array(
				'message'  => __( "You don't have permission to upload files." ),
				'filename' => $_FILES['async-upload']['name'],
			)
		) );

		wp_die();
	}

	if ( isset( $_REQUEST['post_id'] ) ) {
		$post_id = $_REQUEST['post_id'];
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			echo wp_json_encode( array(
				'success' => false,
				'data'    => array(
					'message'  => __( "You don't have permission to attach files to this post." ),
					'filename' => $_FILES['async-upload']['name'],
				)
			) );

			wp_die();
		}
	} else {
		$post_id = null;
	}

	$post_data = ! empty( $_REQUEST['post_data'] ) ? _wp_get_allowed_postdata( _wp_translate_postdata( false, (array) $_REQUEST['post_data'] ) ) : array();

	if ( is_wp_error( $post_data ) ) {
		wp_die( $post_data->get_error_message() );
	}

	// If the context is custom header or background, make sure the uploaded file is an image.
	if ( isset( $post_data['context'] ) && in_array( $post_data['context'], array( 'custom-header', 'custom-background' ) ) ) {
		$wp_filetype = wp_check_filetype_and_ext( $_FILES['async-upload']['tmp_name'], $_FILES['async-upload']['name'], false );
		if ( ! wp_match_mime_types( 'image', $wp_filetype['type'] ) ) {
			echo wp_json_encode( array(
				'success' => false,
				'data'    => array(
					'message'  => __( 'The uploaded file is not a valid image. Please try again.' ),
					'filename' => $_FILES['async-upload']['name'],
				)
			) );

			wp_die();
		}
	}

	$attachment_id = media_handle_upload( 'async-upload', $post_id, $post_data );

	if ( is_wp_error( $attachment_id ) ) {
		echo wp_json_encode( array(
			'success' => false,
			'data'    => array(
				'message'  => $attachment_id->get_error_message(),
				'filename' => $_FILES['async-upload']['name'],
			)
		) );

		wp_die();
	}

	if ( isset( $post_data['context'] ) && isset( $post_data['theme'] ) ) {
		if ( 'custom-background' === $post_data['context'] )
			update_post_meta( $attachment_id, '_wp_attachment_is_custom_background', $post_data['theme'] );

		if ( 'custom-header' === $post_data['context'] )
			update_post_meta( $attachment_id, '_wp_attachment_is_custom_header', $post_data['theme'] );
	}

	if ( ! $attachment = wp_prepare_attachment_for_js( $attachment_id ) )
		wp_die();

	echo wp_json_encode( array(
		'success' => true,
		'data'    => $attachment,
	) );

	wp_die();
}

/**
 * Ajax handler for image editing.
 *
 * @since 3.1.0
 */
function wp_ajax_image_editor() {
	$attachment_id = intval($_POST['postid']);
	if ( empty($attachment_id) || !current_user_can('edit_post', $attachment_id) )
		wp_die( -1 );

	check_ajax_referer( "image_editor-$attachment_id" );
	include_once( ABSPATH . 'wp-admin/includes/image-edit.php' );

	$msg = false;
	switch ( $_POST['do'] ) {
		case 'save' :
			$msg = wp_save_image($attachment_id);
			$msg = wp_json_encode($msg);
			wp_die( $msg );
			break;
		case 'scale' :
			$msg = wp_save_image($attachment_id);
			break;
		case 'restore' :
			$msg = wp_restore_image($attachment_id);
			break;
	}

	wp_image_editor($attachment_id, $msg);
	wp_die();
}

/**
 * Ajax handler for setting the featured image.
 *
 * @since 3.1.0
 */
function wp_ajax_set_post_thumbnail() {
	$json = ! empty( $_REQUEST['json'] ); // New-style request

	$post_ID = intval( $_POST['post_id'] );
	if ( ! current_user_can( 'edit_post', $post_ID ) )
		wp_die( -1 );

	$thumbnail_id = intval( $_POST['thumbnail_id'] );

	if ( $json )
		check_ajax_referer( "update-post_$post_ID" );
	else
		check_ajax_referer( "set_post_thumbnail-$post_ID" );

	if ( $thumbnail_id == '-1' ) {
		if ( delete_post_thumbnail( $post_ID ) ) {
			$return = _wp_post_thumbnail_html( null, $post_ID );
			$json ? wp_send_json_success( $return ) : wp_die( $return );
		} else {
			wp_die( 0 );
		}
	}

	if ( set_post_thumbnail( $post_ID, $thumbnail_id ) ) {
		$return = _wp_post_thumbnail_html( $thumbnail_id, $post_ID );
		$json ? wp_send_json_success( $return ) : wp_die( $return );
	}

	wp_die( 0 );
}

/**
 * AJAX handler for setting the featured image for an attachment.
 *
 * @since 4.0.0
 *
 * @see set_post_thumbnail()
 */
function wp_ajax_set_attachment_thumbnail() {
	if ( empty( $_POST['urls'] ) || ! is_array( $_POST['urls'] ) ) {
		wp_send_json_error();
	}

	$thumbnail_id = (int) $_POST['thumbnail_id'];
	if ( empty( $thumbnail_id ) ) {
		wp_send_json_error();
	}

	$post_ids = array();
	// For each URL, try to find its corresponding post ID.
	foreach ( $_POST['urls'] as $url ) {
		$post_id = attachment_url_to_postid( $url );
		if ( ! empty( $post_id ) ) {
			$post_ids[] = $post_id;
		}
	}

	if ( empty( $post_ids ) ) {
		wp_send_json_error();
	}

	$success = 0;
	// For each found attachment, set its thumbnail.
	foreach ( $post_ids as $post_id ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			continue;
		}

		if ( set_post_thumbnail( $post_id, $thumbnail_id ) ) {
			$success++;
		}
	}

	if ( 0 === $success ) {
		wp_send_json_error();
	} else {
		wp_send_json_success();
	}

	wp_send_json_error();
}

/**
 * Ajax handler for date formatting.
 *
 * @since 3.1.0
 */
function wp_ajax_date_format() {
	wp_die( date_i18n( sanitize_option( 'date_format', wp_unslash( $_POST['date'] ) ) ) );
}

/**
 * Ajax handler for time formatting.
 *
 * @since 3.1.0
 */
function wp_ajax_time_format() {
	wp_die( date_i18n( sanitize_option( 'time_format', wp_unslash( $_POST['date'] ) ) ) );
}

/**
 * Ajax handler for saving posts from the fullscreen editor.
 *
 * @since 3.1.0
 */
function wp_ajax_wp_fullscreen_save_post() {
	$post_id = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : 0;

	$post = null;

	if ( $post_id )
		$post = get_post( $post_id );

	check_ajax_referer('update-post_' . $post_id, '_wpnonce');

	$post_id = edit_post();

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error();
	}

	if ( $post ) {
		$last_date = mysql2date( get_option('date_format'), $post->post_modified );
		$last_time = mysql2date( get_option('time_format'), $post->post_modified );
	} else {
		$last_date = date_i18n( get_option('date_format') );
		$last_time = date_i18n( get_option('time_format') );
	}

	if ( $last_id = get_post_meta( $post_id, '_edit_last', true ) ) {
		$last_user = get_userdata( $last_id );
		$last_edited = sprintf( __('Last edited by %1$s on %2$s at %3$s'), esc_html( $last_user->display_name ), $last_date, $last_time );
	} else {
		$last_edited = sprintf( __('Last edited on %1$s at %2$s'), $last_date, $last_time );
	}

	wp_send_json_success( array( 'last_edited' => $last_edited ) );
}

/**
 * Ajax handler for removing a post lock.
 *
 * @since 3.1.0
 */
function wp_ajax_wp_remove_post_lock() {
	if ( empty( $_POST['post_ID'] ) || empty( $_POST['active_post_lock'] ) )
		wp_die( 0 );
	$post_id = (int) $_POST['post_ID'];
	if ( ! $post = get_post( $post_id ) )
		wp_die( 0 );

	check_ajax_referer( 'update-post_' . $post_id );

	if ( ! current_user_can( 'edit_post', $post_id ) )
		wp_die( -1 );

	$active_lock = array_map( 'absint', explode( ':', $_POST['active_post_lock'] ) );
	if ( $active_lock[1] != get_current_user_id() )
		wp_die( 0 );

	/**
	 * Filter the post lock window duration.
	 *
	 * @since 3.3.0
	 *
	 * @param int $interval The interval in seconds the post lock duration
	 *                      should last, plus 5 seconds. Default 150.
	 */
	$new_lock = ( time() - apply_filters( 'wp_check_post_lock_window', 150 ) + 5 ) . ':' . $active_lock[1];
	update_post_meta( $post_id, '_edit_lock', $new_lock, implode( ':', $active_lock ) );
	wp_die( 1 );
}

/**
 * Ajax handler for dismissing a WordPress pointer.
 *
 * @since 3.1.0
 */
function wp_ajax_dismiss_wp_pointer() {
	$pointer = $_POST['pointer'];
	if ( $pointer != sanitize_key( $pointer ) )
		wp_die( 0 );

//	check_ajax_referer( 'dismiss-pointer_' . $pointer );

	$dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) ) );

	if ( in_array( $pointer, $dismissed ) )
		wp_die( 0 );

	$dismissed[] = $pointer;
	$dismissed = implode( ',', $dismissed );

	update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', $dismissed );
	wp_die( 1 );
}

/**
 * Ajax handler for getting an attachment.
 *
 * @since 3.5.0
 */
function wp_ajax_get_attachment() {
	if ( ! isset( $_REQUEST['id'] ) )
		wp_send_json_error();

	if ( ! $id = absint( $_REQUEST['id'] ) )
		wp_send_json_error();

	if ( ! $post = get_post( $id ) )
		wp_send_json_error();

	if ( 'attachment' != $post->post_type )
		wp_send_json_error();

	if ( ! current_user_can( 'upload_files' ) )
		wp_send_json_error();

	if ( ! $attachment = wp_prepare_attachment_for_js( $id ) )
		wp_send_json_error();

	wp_send_json_success( $attachment );
}

/**
 * Ajax handler for querying attachments.
 *
 * @since 3.5.0
 */
function wp_ajax_query_attachments() {
	if ( ! current_user_can( 'upload_files' ) )
		wp_send_json_error();

	$query = isset( $_REQUEST['query'] ) ? (array) $_REQUEST['query'] : array();
	$query = array_intersect_key( $query, array_flip( array(
		's', 'order', 'orderby', 'posts_per_page', 'paged', 'post_mime_type',
		'post_parent', 'post__in', 'post__not_in', 'year', 'monthnum'
	) ) );

	$query['post_type'] = 'attachment';
	if ( MEDIA_TRASH
		&& ! empty( $_REQUEST['query']['post_status'] )
		&& 'trash' === $_REQUEST['query']['post_status'] ) {
		$query['post_status'] = 'trash';
	} else {
		$query['post_status'] = 'inherit';
	}

	if ( current_user_can( get_post_type_object( 'attachment' )->cap->read_private_posts ) )
		$query['post_status'] .= ',private';

	/**
	 * Filter the arguments passed to WP_Query during an AJAX
	 * call for querying attachments.
	 *
	 * @since 3.7.0
	 *
	 * @see WP_Query::parse_query()
	 *
	 * @param array $query An array of query variables.
	 */
	$query = apply_filters( 'ajax_query_attachments_args', $query );
	$query = new WP_Query( $query );

	$posts = array_map( 'wp_prepare_attachment_for_js', $query->posts );
	$posts = array_filter( $posts );

	wp_send_json_success( $posts );
}

/**
 * Ajax handler for updating attachment attributes.
 *
 * @since 3.5.0
 */
function wp_ajax_save_attachment() {
	if ( ! isset( $_REQUEST['id'] ) || ! isset( $_REQUEST['changes'] ) )
		wp_send_json_error();

	if ( ! $id = absint( $_REQUEST['id'] ) )
		wp_send_json_error();

	check_ajax_referer( 'update-post_' . $id, 'nonce' );

	if ( ! current_user_can( 'edit_post', $id ) )
		wp_send_json_error();

	$changes = $_REQUEST['changes'];
	$post    = get_post( $id, ARRAY_A );

	if ( 'attachment' != $post['post_type'] )
		wp_send_json_error();

	if ( isset( $changes['title'] ) )
		$post['post_title'] = $changes['title'];

	if ( isset( $changes['caption'] ) )
		$post['post_excerpt'] = $changes['caption'];

	if ( isset( $changes['description'] ) )
		$post['post_content'] = $changes['description'];

	if ( MEDIA_TRASH && isset( $changes['status'] ) )
		$post['post_status'] = $changes['status'];

	if ( isset( $changes['alt'] ) ) {
		$alt = wp_unslash( $changes['alt'] );
		if ( $alt != get_post_meta( $id, '_wp_attachment_image_alt', true ) ) {
			$alt = wp_strip_all_tags( $alt, true );
			update_post_meta( $id, '_wp_attachment_image_alt', wp_slash( $alt ) );
		}
	}

	if ( 0 === strpos( $post['post_mime_type'], 'audio/' ) ) {
		$changed = false;
		$id3data = wp_get_attachment_metadata( $post['ID'] );
		if ( ! is_array( $id3data ) ) {
			$changed = true;
			$id3data = array();
		}
		foreach ( wp_get_attachment_id3_keys( (object) $post, 'edit' ) as $key => $label ) {
			if ( isset( $changes[ $key ] ) ) {
				$changed = true;
				$id3data[ $key ] = sanitize_text_field( wp_unslash( $changes[ $key ] ) );
			}
		}

		if ( $changed ) {
			wp_update_attachment_metadata( $id, $id3data );
		}
	}

	if ( MEDIA_TRASH && isset( $changes['status'] ) && 'trash' === $changes['status'] ) {
		wp_delete_post( $id );
	} else {
		wp_update_post( $post );
	}

	wp_send_json_success();
}

/**
 * Ajax handler for saving backwards compatible attachment attributes.
 *
 * @since 3.5.0
 */
function wp_ajax_save_attachment_compat() {
	if ( ! isset( $_REQUEST['id'] ) )
		wp_send_json_error();

	if ( ! $id = absint( $_REQUEST['id'] ) )
		wp_send_json_error();

	if ( empty( $_REQUEST['attachments'] ) || empty( $_REQUEST['attachments'][ $id ] ) )
		wp_send_json_error();
	$attachment_data = $_REQUEST['attachments'][ $id ];

	check_ajax_referer( 'update-post_' . $id, 'nonce' );

	if ( ! current_user_can( 'edit_post', $id ) )
		wp_send_json_error();

	$post = get_post( $id, ARRAY_A );

	if ( 'attachment' != $post['post_type'] )
		wp_send_json_error();

	/** This filter is documented in wp-admin/includes/media.php */
	$post = apply_filters( 'attachment_fields_to_save', $post, $attachment_data );

	if ( isset( $post['errors'] ) ) {
		$errors = $post['errors']; // @todo return me and display me!
		unset( $post['errors'] );
	}

	wp_update_post( $post );

	foreach ( get_attachment_taxonomies( $post ) as $taxonomy ) {
		if ( isset( $attachment_data[ $taxonomy ] ) )
			wp_set_object_terms( $id, array_map( 'trim', preg_split( '/,+/', $attachment_data[ $taxonomy ] ) ), $taxonomy, false );
	}

	if ( ! $attachment = wp_prepare_attachment_for_js( $id ) )
		wp_send_json_error();

	wp_send_json_success( $attachment );
}

/**
 * Ajax handler for saving the attachment order.
 *
 * @since 3.5.0
 */
function wp_ajax_save_attachment_order() {
	if ( ! isset( $_REQUEST['post_id'] ) )
		wp_send_json_error();

	if ( ! $post_id = absint( $_REQUEST['post_id'] ) )
		wp_send_json_error();

	if ( empty( $_REQUEST['attachments'] ) )
		wp_send_json_error();

	check_ajax_referer( 'update-post_' . $post_id, 'nonce' );

	$attachments = $_REQUEST['attachments'];

	if ( ! current_user_can( 'edit_post', $post_id ) )
		wp_send_json_error();

	foreach ( $attachments as $attachment_id => $menu_order ) {
		if ( ! current_user_can( 'edit_post', $attachment_id ) )
			continue;
		if ( ! $attachment = get_post( $attachment_id ) )
			continue;
		if ( 'attachment' != $attachment->post_type )
			continue;

		wp_update_post( array( 'ID' => $attachment_id, 'menu_order' => $menu_order ) );
	}

	wp_send_json_success();
}

/**
 * Ajax handler for sending an attachment to the editor.
 *
 * Generates the HTML to send an attachment to the editor.
 * Backwards compatible with the media_send_to_editor filter
 * and the chain of filters that follow.
 *
 * @since 3.5.0
 */
function wp_ajax_send_attachment_to_editor() {
	check_ajax_referer( 'media-send-to-editor', 'nonce' );

	$attachment = wp_unslash( $_POST['attachment'] );

	$id = intval( $attachment['id'] );

	if ( ! $post = get_post( $id ) )
		wp_send_json_error();

	if ( 'attachment' != $post->post_type )
		wp_send_json_error();

	if ( current_user_can( 'edit_post', $id ) ) {
		// If this attachment is unattached, attach it. Primarily a back compat thing.
		if ( 0 == $post->post_parent && $insert_into_post_id = intval( $_POST['post_id'] ) ) {
			wp_update_post( array( 'ID' => $id, 'post_parent' => $insert_into_post_id ) );
		}
	}

	$rel = $url = '';
	$html = isset( $attachment['post_title'] ) ? $attachment['post_title'] : '';
	if ( ! empty( $attachment['url'] ) ) {
		$url = $attachment['url'];
		if ( strpos( $url, 'attachment_id') || get_attachment_link( $id ) == $url )
			$rel = ' rel="attachment wp-att-' . $id . '"';
		$html = '<a href="' . esc_url( $url ) . '"' . $rel . '>' . $html . '</a>';
	}

	remove_filter( 'media_send_to_editor', 'image_media_send_to_editor' );

	if ( 'image' === substr( $post->post_mime_type, 0, 5 ) ) {
		$align = isset( $attachment['align'] ) ? $attachment['align'] : 'none';
		$size = isset( $attachment['image-size'] ) ? $attachment['image-size'] : 'medium';
		$alt = isset( $attachment['image_alt'] ) ? $attachment['image_alt'] : '';
		$caption = isset( $attachment['post_excerpt'] ) ? $attachment['post_excerpt'] : '';
		$title = ''; // We no longer insert title tags into <img> tags, as they are redundant.
		$html = get_image_send_to_editor( $id, $caption, $title, $align, $url, (bool) $rel, $size, $alt );
	} elseif ( 'video' === substr( $post->post_mime_type, 0, 5 ) || 'audio' === substr( $post->post_mime_type, 0, 5 )  ) {
		$html = stripslashes_deep( $_POST['html'] );
	}

	/** This filter is documented in wp-admin/includes/media.php */
	$html = apply_filters( 'media_send_to_editor', $html, $id, $attachment );

	wp_send_json_success( $html );
}

/**
 * Ajax handler for sending a link to the editor.
 *
 * Generates the HTML to send a non-image embed link to the editor.
 *
 * Backwards compatible with the following filters:
 * - file_send_to_editor_url
 * - audio_send_to_editor_url
 * - video_send_to_editor_url
 *
 * @since 3.5.0
 */
function wp_ajax_send_link_to_editor() {
	global $post, $wp_embed;

	check_ajax_referer( 'media-send-to-editor', 'nonce' );

	if ( ! $src = wp_unslash( $_POST['src'] ) )
		wp_send_json_error();

	if ( ! strpos( $src, '://' ) )
		$src = 'http://' . $src;

	if ( ! $src = esc_url_raw( $src ) )
		wp_send_json_error();

	if ( ! $title = trim( wp_unslash( $_POST['title'] ) ) )
		$title = wp_basename( $src );

	$post = get_post( isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0 );

	// Ping WordPress for an embed.
	$check_embed = $wp_embed->run_shortcode( '[embed]'. $src .'[/embed]' );

	// Fallback that WordPress creates when no oEmbed was found.
	$fallback = $wp_embed->maybe_make_link( $src );

	if ( $check_embed !== $fallback ) {
		// TinyMCE view for [embed] will parse this
		$html = '[embed]' . $src . '[/embed]';
	} elseif ( $title ) {
		$html = '<a href="' . esc_url( $src ) . '">' . $title . '</a>';
	} else {
		$html = '';
	}

	// Figure out what filter to run:
	$type = 'file';
	if ( ( $ext = preg_replace( '/^.+?\.([^.]+)$/', '$1', $src ) ) && ( $ext_type = wp_ext2type( $ext ) )
		&& ( 'audio' == $ext_type || 'video' == $ext_type ) )
			$type = $ext_type;

	/** This filter is documented in wp-admin/includes/media.php */
	$html = apply_filters( $type . '_send_to_editor_url', $html, $src, $title );

	wp_send_json_success( $html );
}

/**
 * Ajax handler for the Heartbeat API.
 *
 * Runs when the user is logged in.
 *
 * @since 3.6.0
 */
function wp_ajax_heartbeat() {
	if ( empty( $_POST['_nonce'] ) )
		wp_send_json_error();

	$response = array();

	if ( false === wp_verify_nonce( $_POST['_nonce'], 'heartbeat-nonce' ) ) {
		// User is logged in but nonces have expired.
		$response['nonces_expired'] = true;
		wp_send_json($response);
	}

	// screen_id is the same as $current_screen->id and the JS global 'pagenow'.
	if ( ! empty($_POST['screen_id']) )
		$screen_id = sanitize_key($_POST['screen_id']);
	else
		$screen_id = 'front';

	if ( ! empty($_POST['data']) ) {
		$data = wp_unslash( (array) $_POST['data'] );

		/**
		 * Filter the Heartbeat response received.
		 *
		 * @since 3.6.0
		 *
		 * @param array|object $response  The Heartbeat response object or array.
		 * @param array        $data      The $_POST data sent.
		 * @param string       $screen_id The screen id.
		 */
		$response = apply_filters( 'heartbeat_received', $response, $data, $screen_id );
	}

	/**
	 * Filter the Heartbeat response sent.
	 *
	 * @since 3.6.0
	 *
	 * @param array|object $response  The Heartbeat response object or array.
	 * @param string       $screen_id The screen id.
	 */
	$response = apply_filters( 'heartbeat_send', $response, $screen_id );

	/**
	 * Fires when Heartbeat ticks in logged-in environments.
	 *
	 * Allows the transport to be easily replaced with long-polling.
	 *
	 * @since 3.6.0
	 *
	 * @param array|object $response  The Heartbeat response object or array.
	 * @param string       $screen_id The screen id.
	 */
	do_action( 'heartbeat_tick', $response, $screen_id );

	// Send the current time according to the server
	$response['server_time'] = time();

	wp_send_json($response);
}

/**
 * Ajax handler for getting revision diffs.
 *
 * @since 3.6.0
 */
function wp_ajax_get_revision_diffs() {
	require ABSPATH . 'wp-admin/includes/revision.php';

	if ( ! $post = get_post( (int) $_REQUEST['post_id'] ) )
		wp_send_json_error();

	if ( ! current_user_can( 'edit_post', $post->ID ) )
		wp_send_json_error();

	// Really just pre-loading the cache here.
	if ( ! $revisions = wp_get_post_revisions( $post->ID, array( 'check_enabled' => false ) ) )
		wp_send_json_error();

	$return = array();
	@set_time_limit( 0 );

	foreach ( $_REQUEST['compare'] as $compare_key ) {
		list( $compare_from, $compare_to ) = explode( ':', $compare_key ); // from:to

		$return[] = array(
			'id' => $compare_key,
			'fields' => wp_get_revision_ui_diff( $post, $compare_from, $compare_to ),
		);
	}
	wp_send_json_success( $return );
}

/**
 * Ajax handler for auto-saving the selected color scheme for
 * a user's own profile.
 *
 * @since 3.8.0
 */
function wp_ajax_save_user_color_scheme() {
	global $_wp_admin_css_colors;

	check_ajax_referer( 'save-color-scheme', 'nonce' );

	$color_scheme = sanitize_key( $_POST['color_scheme'] );

	if ( ! isset( $_wp_admin_css_colors[ $color_scheme ] ) ) {
		wp_send_json_error();
	}

	update_user_meta( get_current_user_id(), 'admin_color', $color_scheme );
	wp_send_json_success();
}

/**
 * Ajax handler for getting themes from themes_api().
 *
 * @since 3.9.0
 */
function wp_ajax_query_themes() {
	global $themes_allowedtags, $theme_field_defaults;

	if ( ! current_user_can( 'install_themes' ) ) {
		wp_send_json_error();
	}

	$args = wp_parse_args( wp_unslash( $_REQUEST['request'] ), array(
		'per_page' => 20,
		'fields'   => $theme_field_defaults
	) );

	$old_filter = isset( $args['browse'] ) ? $args['browse'] : 'search';

	/** This filter is documented in wp-admin/includes/class-wp-theme-install-list-table.php */
	$args = apply_filters( 'install_themes_table_api_args_' . $old_filter, $args );

	$api = themes_api( 'query_themes', $args );

	if ( is_wp_error( $api ) ) {
		wp_send_json_error();
	}

	$update_php = network_admin_url( 'update.php?action=install-theme' );
	foreach ( $api->themes as &$theme ) {
		$theme->install_url = add_query_arg( array(
			'theme'    => $theme->slug,
			'_wpnonce' => wp_create_nonce( 'install-theme_' . $theme->slug )
		), $update_php );

		$theme->name        = wp_kses( $theme->name, $themes_allowedtags );
		$theme->author      = wp_kses( $theme->author, $themes_allowedtags );
		$theme->version     = wp_kses( $theme->version, $themes_allowedtags );
		$theme->description = wp_kses( $theme->description, $themes_allowedtags );
		$theme->num_ratings = sprintf( _n( '(based on %s rating)', '(based on %s ratings)', $theme->num_ratings ), number_format_i18n( $theme->num_ratings ) );
		$theme->preview_url = set_url_scheme( $theme->preview_url );
	}

	wp_send_json_success( $api );
}

/**
 * Apply [embed] AJAX handlers to a string.
 *
 * @since 4.0.0
 *
 * @global WP_Post  $post     Global $post.
 * @global WP_Embed $wp_embed Embed API instance.
 */
function wp_ajax_parse_embed() {
	global $post, $wp_embed;

	if ( ! $post = get_post( (int) $_POST['post_ID'] ) ) {
		wp_send_json_error();
	}

	if ( empty( $_POST['shortcode'] ) || ! current_user_can( 'edit_post', $post->ID ) ) {
		wp_send_json_error();
	}

	$shortcode = wp_unslash( $_POST['shortcode'] );
	$url = str_replace( '[embed]', '', str_replace( '[/embed]', '', $shortcode ) );
	$parsed = false;
	setup_postdata( $post );

	$wp_embed->return_false_on_fail = true;

	if ( is_ssl() && preg_match( '%^\\[embed[^\\]]*\\]http://%i', $shortcode ) ) {
		// Admin is ssl and the user pasted non-ssl URL.
		// Check if the provider supports ssl embeds and use that for the preview.
		$ssl_shortcode = preg_replace( '%^(\\[embed[^\\]]*\\])http://%i', '$1https://', $shortcode );
		$parsed = $wp_embed->run_shortcode( $ssl_shortcode );

		if ( ! $parsed ) {
			$no_ssl_support = true;
		}
	}

	if ( ! $parsed ) {
		$parsed = $wp_embed->run_shortcode( $shortcode );
	}

	if ( ! $parsed ) {
		wp_send_json_error( array(
			'type' => 'not-embeddable',
			'message' => sprintf( __( '%s failed to embed.' ), '<code>' . esc_html( $url ) . '</code>' ),
		) );
	}

	if ( has_shortcode( $parsed, 'audio' ) || has_shortcode( $parsed, 'video' ) ) {
		$styles = '';
		$mce_styles = wpview_media_sandbox_styles();
		foreach ( $mce_styles as $style ) {
			$styles .= sprintf( '<link rel="stylesheet" href="%s"/>', $style );
		}

		$html = do_shortcode( $parsed );

		global $wp_scripts;
		if ( ! empty( $wp_scripts ) ) {
			$wp_scripts->done = array();
		}
		ob_start();
		wp_print_scripts( 'wp-mediaelement' );
		$scripts = ob_get_clean();

		$parsed = $styles . $html . $scripts;
	}


	if ( ! empty( $no_ssl_support ) || ( is_ssl() && ( preg_match( '%<(iframe|script|embed) [^>]*src="http://%', $parsed ) ||
		preg_match( '%<link [^>]*href="http://%', $parsed ) ) ) ) {
		// Admin is ssl and the embed is not. Iframes, scripts, and other "active content" will be blocked.
		wp_send_json_error( array(
			'type' => 'not-ssl',
			'message' => __( 'This preview is unavailable in the editor.' ),
		) );
	}

	wp_send_json_success( array(
		'body' => $parsed
	) );
}

function wp_ajax_parse_media_shortcode() {
	global $post, $wp_scripts;

	if ( ! $post = get_post( (int) $_POST['post_ID'] ) ) {
		wp_send_json_error();
	}

	if ( empty( $_POST['shortcode'] ) || ! current_user_can( 'edit_post', $post->ID ) ) {
		wp_send_json_error();
	}

	setup_postdata( $post );
	$shortcode = do_shortcode( wp_unslash( $_POST['shortcode'] ) );

	if ( empty( $shortcode ) ) {
		wp_send_json_error( array(
			'type' => 'no-items',
			'message' => __( 'No items found.' ),
		) );
	}

	$head = '';
	$styles = wpview_media_sandbox_styles();

	foreach ( $styles as $style ) {
		$head .= '<link type="text/css" rel="stylesheet" href="' . $style . '">';
	}

	if ( ! empty( $wp_scripts ) ) {
		$wp_scripts->done = array();
	}

	ob_start();

	echo $shortcode;

	if ( 'playlist' === $_REQUEST['type'] ) {
		wp_underscore_playlist_templates();

		wp_print_scripts( 'wp-playlist' );
	} else {
		wp_print_scripts( 'wp-mediaelement' );
	}

	wp_send_json_success( array(
		'head' => $head,
		'body' => ob_get_clean()
	) );
}

/**
 * AJAX handler for destroying multiple open sessions for a user.
 *
 * @since 4.1.0
 */
function wp_ajax_destroy_sessions() {

	$user = get_userdata( (int) $_POST['user_id'] );
	if ( $user ) {
		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			$user = false;
		} elseif ( ! wp_verify_nonce( $_POST['nonce'], 'update-user_' . $user->ID ) ) {
			$user = false;
		}
	}

	if ( ! $user ) {
		wp_send_json_error( array(
			'message' => __( 'Could not log out user sessions. Please try again.' ),
		) );
	}

	$sessions = WP_Session_Tokens::get_instance( $user->ID );

	if ( $user->ID === get_current_user_id() ) {
		$sessions->destroy_others( wp_get_session_token() );
		$message = __( 'You are now logged out everywhere else.' );
	} else {
		$sessions->destroy_all();
		/* translators: 1: User's display name. */ 
		$message = sprintf( __( '%s has been logged out.' ), $user->display_name );
	}

	wp_send_json_success( array( 'message' => $message ) );
}
