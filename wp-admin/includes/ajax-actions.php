<?php
/**
 * WordPress Core Ajax Handlers.
 *
 * @package WordPress
 * @subpackage Administration
 */

/*
 * No-privilege Ajax handlers.
 */

function wp_ajax_nopriv_autosave() {
	$id = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : 0;

	if ( ! $id )
		wp_die( -1 );

	$message = sprintf( __('<strong>ALERT: You are logged out!</strong> Could not save draft. <a href="%s" target="_blank">Please log in again.</a>'), wp_login_url() );
	$x = new WP_Ajax_Response( array(
		'what' => 'autosave',
		'id' => $id,
		'data' => $message
	) );
	$x->send();
}

/*
 * GET-based Ajax handlers.
 */
function wp_ajax_fetch_list() {
	global $current_screen, $wp_list_table;

	$list_class = $_GET['list_args']['class'];
	check_ajax_referer( "fetch-list-$list_class", '_ajax_fetch_list_nonce' );

	$current_screen = convert_to_screen( $_GET['list_args']['screen']['id'] );

	define( 'WP_NETWORK_ADMIN', $current_screen->is_network );
	define( 'WP_USER_ADMIN', $current_screen->is_user );

	$wp_list_table = _get_list_table( $list_class );
	if ( ! $wp_list_table )
		wp_die( 0 );

	if ( ! $wp_list_table->ajax_user_can() )
		wp_die( -1 );

	$wp_list_table->ajax_response();

	wp_die( 0 );
}
function wp_ajax_ajax_tag_search() {
	global $wpdb;

	if ( isset( $_GET['tax'] ) ) {
		$taxonomy = sanitize_key( $_GET['tax'] );
		$tax = get_taxonomy( $taxonomy );
		if ( ! $tax )
			wp_die( 0 );
		if ( ! current_user_can( $tax->cap->assign_terms ) )
			wp_die( -1 );
	} else {
		wp_die( 0 );
	}

	$s = stripslashes( $_GET['q'] );

	$comma = _x( ',', 'tag delimiter' );
	if ( ',' !== $comma )
		$s = str_replace( $comma, ',', $s );
	if ( false !== strpos( $s, ',' ) ) {
		$s = explode( ',', $s );
		$s = $s[count( $s ) - 1];
	}
	$s = trim( $s );
	if ( strlen( $s ) < 2 )
		wp_die(); // require 2 chars for matching

	$results = $wpdb->get_col( $wpdb->prepare( "SELECT t.name FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = %s AND t.name LIKE (%s)", $taxonomy, '%' . like_escape( $s ) . '%' ) );

	echo join( $results, "\n" );
	wp_die();
}

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

function wp_ajax_oembed_cache() {
	global $wp_embed;

	$return = ( $wp_embed->cache_oembed( $_GET['post'] ) ) ? '1' : '0';
	wp_die( $return );
}

function wp_ajax_autocomplete_user() {
	if ( ! is_multisite() || ! current_user_can( 'promote_users' ) || wp_is_large_network( 'users' ) )
		wp_die( -1 );

	if ( ! is_super_admin() && ! apply_filters( 'autocomplete_users_for_site_admins', false ) )
		wp_die( -1 );

	$return = array();

	// Check the type of request
	if ( isset( $_REQUEST['autocomplete_type'] ) )
		$type = $_REQUEST['autocomplete_type'];
	else
		$type = 'add';

	// Exclude current users of this blog
	if ( isset( $_REQUEST['site_id'] ) )
		$id = absint( $_REQUEST['site_id'] );
	else
		$id = get_current_blog_id();

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
			'value' => $user->user_login,
		);
	}

	wp_die( json_encode( $return ) );
}

function wp_ajax_dashboard_widgets() {
	require ABSPATH . 'wp-admin/includes/dashboard.php';

	switch ( $_GET['widget'] ) {
		case 'dashboard_incoming_links' :
			wp_dashboard_incoming_links();
			break;
		case 'dashboard_primary' :
			wp_dashboard_primary();
			break;
		case 'dashboard_secondary' :
			wp_dashboard_secondary();
			break;
		case 'dashboard_plugins' :
			wp_dashboard_plugins();
			break;
	}
	wp_die();
}

function wp_ajax_logged_in() {
	wp_die( 1 );
}

/*
 * Ajax helper.
 */

/**
 * Sends back current comment total and new page links if they need to be updated.
 *
 * Contrary to normal success AJAX response ("1"), die with time() on success.
 *
 * @since 2.7
 *
 * @param int $comment_id
 * @return die
 */
function _wp_ajax_delete_comment_response( $comment_id, $delta = -1 ) {
	$total = (int) @$_POST['_total'];
	$per_page = (int) @$_POST['_per_page'];
	$page = (int) @$_POST['_page'];
	$url = esc_url_raw( @$_POST['_url'] );
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

		if ( isset( $comment_count->$status ) ) // We're looking for a known type of comment count
			$total = $comment_count->$status;
			// else use the decremented value from above
	}

	$time = time(); // The time since the last comment count

	$x = new WP_Ajax_Response( array(
		'what' => 'comment',
		'id' => $comment_id, // here for completeness - not used
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

/*
 * POST-based Ajax handlers.
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
			$parent = &get_term( $parent->parent, $taxonomy->name );
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

	if ( !get_page( $id ) )
		wp_die( 1 );

	if ( wp_delete_post( $id ) )
		wp_die( 1 );
	else
		wp_die( 0 );
}

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
	if ( $_POST['new'] == $current )
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

function wp_ajax_add_link_category( $action ) {
	if ( empty( $action ) )
		$action = 'add-link-category';
	check_ajax_referer( $action );
	if ( !current_user_can( 'manage_categories' ) )
		wp_die( -1 );
	$names = explode(',', $_POST['newcat']);
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
		$cat_name = esc_html(stripslashes($cat_name));
		$x->add( array(
			'what' => 'link-category',
			'id' => $cat_id,
			'data' => "<li id='link-category-$cat_id'><label for='in-link-category-$cat_id' class='selectit'><input value='" . esc_attr($cat_id) . "' type='checkbox' checked='checked' name='link_category[]' id='in-link-category-$cat_id'/> $cat_name</label></li>",
			'position' => -1
		) );
	}
	$x->send();
}

function wp_ajax_add_tag() {
	global $wp_list_table;

	check_ajax_referer( 'add-tag', '_wpnonce_add-tag' );
	$post_type = !empty($_POST['post_type']) ? $_POST['post_type'] : 'post';
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

	set_current_screen( $_POST['screen'] );

	$wp_list_table = _get_list_table('WP_Terms_List_Table');

	$level = 0;
	if ( is_taxonomy_hierarchical($taxonomy) ) {
		$level = count( get_ancestors( $tag->term_id, $taxonomy ) );
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

function wp_ajax_get_tagcloud() {
	if ( isset( $_POST['tax'] ) ) {
		$taxonomy = sanitize_key( $_POST['tax'] );
		$tax = get_taxonomy( $taxonomy );
		if ( ! $tax )
			wp_die( 0 );
		if ( ! current_user_can( $tax->cap->assign_terms ) )
			wp_die( -1 );
	} else {
		wp_die( 0 );
	}

	$tags = get_terms( $taxonomy, array( 'number' => 45, 'orderby' => 'count', 'order' => 'DESC' ) );

	if ( empty( $tags ) )
		wp_die( isset( $tax->no_tagcloud ) ? $tax->no_tagcloud : __('No tags found!') );

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

function wp_ajax_get_comments( $action ) {
	global $wp_list_table, $post_id;
	if ( empty( $action ) )
		$action = 'get-comments';

	check_ajax_referer( $action );

	set_current_screen( 'edit-comments' );

	$wp_list_table = _get_list_table('WP_Post_Comments_List_Table');

	if ( !current_user_can( 'edit_post', $post_id ) )
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

function wp_ajax_replyto_comment( $action ) {
	global $wp_list_table, $wpdb;
	if ( empty( $action ) )
		$action = 'replyto-comment';

	check_ajax_referer( $action, '_ajax_nonce-replyto-comment' );

	set_current_screen( 'edit-comments' );

	$comment_post_ID = (int) $_POST['comment_post_ID'];
	if ( !current_user_can( 'edit_post', $comment_post_ID ) )
		wp_die( -1 );

	$status = $wpdb->get_var( $wpdb->prepare("SELECT post_status FROM $wpdb->posts WHERE ID = %d", $comment_post_ID) );

	if ( empty($status) )
		wp_die( 1 );
	elseif ( in_array($status, array('draft', 'pending', 'trash') ) )
		wp_die( __('ERROR: you are replying to a comment on a draft post.') );

	$user = wp_get_current_user();
	if ( $user->exists() ) {
		$user_ID = $user->ID;
		$comment_author       = $wpdb->escape($user->display_name);
		$comment_author_email = $wpdb->escape($user->user_email);
		$comment_author_url   = $wpdb->escape($user->user_url);
		$comment_content      = trim($_POST['content']);
		if ( current_user_can( 'unfiltered_html' ) ) {
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

	$comment_parent = absint($_POST['comment_ID']);
	$comment_auto_approved = false;
	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

	$comment_id = wp_new_comment( $commentdata );
	$comment = get_comment($comment_id);
	if ( ! $comment ) wp_die( 1 );

	$position = ( isset($_POST['position']) && (int) $_POST['position'] ) ? (int) $_POST['position'] : '-1';

	// automatically approve parent comment
	if ( !empty($_POST['approve_parent']) ) {
		$parent = get_comment( $comment_parent );

		if ( $parent && $parent->comment_approved === '0' && $parent->comment_post_ID == $comment_post_ID ) {
			if ( wp_set_comment_status( $parent->comment_ID, 'approve' ) )
				$comment_auto_approved = true;
		}
	}

	ob_start();
		if ( 'dashboard' == $_REQUEST['mode'] ) {
			require_once( ABSPATH . 'wp-admin/includes/dashboard.php' );
			_wp_dashboard_recent_comments_row( $comment );
		} else {
			if ( 'single' == $_REQUEST['mode'] ) {
				$wp_list_table = _get_list_table('WP_Post_Comments_List_Table');
			} else {
				$wp_list_table = _get_list_table('WP_Comments_List_Table');
			}
			$wp_list_table->single_row( $comment );
		}
		$comment_list_item = ob_get_contents();
	ob_end_clean();

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

function wp_ajax_edit_comment() {
	global $wp_list_table;

	check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment' );

	set_current_screen( 'edit-comments' );

	$comment_id = (int) $_POST['comment_ID'];
	if ( ! current_user_can( 'edit_comment', $comment_id ) )
		wp_die( -1 );

	if ( '' == $_POST['content'] )
		wp_die( __( 'ERROR: please type a comment.' ) );

	$_POST['comment_status'] = $_POST['status'];
	edit_comment();

	$position = ( isset($_POST['position']) && (int) $_POST['position']) ? (int) $_POST['position'] : '-1';
	$comments_status = isset($_POST['comments_listing']) ? $_POST['comments_listing'] : '';

	$checkbox = ( isset($_POST['checkbox']) && true == $_POST['checkbox'] ) ? 1 : 0;
	$wp_list_table = _get_list_table( $checkbox ? 'WP_Comments_List_Table' : 'WP_Post_Comments_List_Table' );

	$comment = get_comment( $comment_id );

	ob_start();
		$wp_list_table->single_row( $comment );
		$comment_list_item = ob_get_contents();
	ob_end_clean();

	$x = new WP_Ajax_Response();

	$x->add( array(
		'what' => 'edit_comment',
		'id' => $comment->comment_ID,
		'data' => $comment_list_item,
		'position' => $position
	));

	$x->send();
}

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
}

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
		if ( $post->post_status == 'auto-draft' ) {
			$save_POST = $_POST; // Backup $_POST
			$_POST = array(); // Make it empty for edit_post()
			$_POST['action'] = 'draft'; // Warning fix
			$_POST['post_ID'] = $pid;
			$_POST['post_type'] = $post->post_type;
			$_POST['post_status'] = 'draft';
			$now = current_time('timestamp', 1);
			$_POST['post_title'] = sprintf('Draft created on %s at %s', date(get_option('date_format'), $now), date(get_option('time_format'), $now));

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
		$key = stripslashes( $_POST['meta'][$mid]['key'] );
		$value = stripslashes( $_POST['meta'][$mid]['value'] );
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
	$user_object = new WP_User( $user_id );

	$wp_list_table = _get_list_table('WP_Users_List_Table');

	$x = new WP_Ajax_Response( array(
		'what' => 'user',
		'id' => $user_id,
		'data' => $wp_list_table->single_row( $user_object, '', $user_object->roles[0] ),
		'supplemental' => array(
			'show-link' => sprintf(__( 'User <a href="#%s">%s</a> added' ), "user-$user_id", $user_object->user_login),
			'role' => $user_object->roles[0]
		)
	) );
	$x->send();
}

function wp_ajax_autosave() {
	global $login_grace_period;

	define( 'DOING_AUTOSAVE', true );

	$nonce_age = check_ajax_referer( 'autosave', 'autosavenonce' );

	$_POST['post_category'] = explode(",", $_POST['catslist']);
	if ( $_POST['post_type'] == 'page' || empty($_POST['post_category']) )
		unset($_POST['post_category']);

	$do_autosave = (bool) $_POST['autosave'];
	$do_lock = true;

	$data = $alert = '';
	/* translators: draft saved date format, see http://php.net/date */
	$draft_saved_date_format = __('g:i:s a');
	/* translators: %s: date and time */
	$message = sprintf( __('Draft saved at %s.'), date_i18n( $draft_saved_date_format ) );

	$supplemental = array();
	if ( isset($login_grace_period) )
		$alert .= sprintf( __('Your login has expired. Please open a new browser window and <a href="%s" target="_blank">log in again</a>. '), add_query_arg( 'interim-login', 1, wp_login_url() ) );

	$id = $revision_id = 0;

	$post_ID = (int) $_POST['post_ID'];
	$_POST['ID'] = $post_ID;
	$post = get_post($post_ID);
	if ( 'auto-draft' == $post->post_status )
		$_POST['post_status'] = 'draft';

	if ( $last = wp_check_post_lock( $post->ID ) ) {
		$do_autosave = $do_lock = false;

		$last_user = get_userdata( $last );
		$last_user_name = $last_user ? $last_user->display_name : __( 'Someone' );
		$data = __( 'Autosave disabled.' );

		$supplemental['disable_autosave'] = 'disable';
		$alert .= sprintf( __( '%s is currently editing this article. If you update it, you will overwrite the changes.' ), esc_html( $last_user_name ) );
	}

	if ( 'page' == $post->post_type ) {
		if ( !current_user_can('edit_page', $post_ID) )
			wp_die( __( 'You are not allowed to edit this page.' ) );
	} else {
		if ( !current_user_can('edit_post', $post_ID) )
			wp_die( __( 'You are not allowed to edit this post.' ) );
	}

	if ( $do_autosave ) {
		// Drafts and auto-drafts are just overwritten by autosave
		if ( 'auto-draft' == $post->post_status || 'draft' == $post->post_status ) {
			$id = edit_post();
		} else { // Non drafts are not overwritten. The autosave is stored in a special post revision.
			$revision_id = wp_create_post_autosave( $post->ID );
			if ( is_wp_error($revision_id) )
				$id = $revision_id;
			else
				$id = $post->ID;
		}
		$data = $message;
	} else {
		if ( ! empty( $_POST['auto_draft'] ) )
			$id = 0; // This tells us it didn't actually save
		else
			$id = $post->ID;
	}

	if ( $do_lock && empty( $_POST['auto_draft'] ) && $id && is_numeric( $id ) ) {
		$lock_result = wp_set_post_lock( $id );
		$supplemental['active-post-lock'] = implode( ':', $lock_result );
	}

	if ( $nonce_age == 2 ) {
		$supplemental['replace-autosavenonce'] = wp_create_nonce('autosave');
		$supplemental['replace-getpermalinknonce'] = wp_create_nonce('getpermalink');
		$supplemental['replace-samplepermalinknonce'] = wp_create_nonce('samplepermalink');
		$supplemental['replace-closedpostboxesnonce'] = wp_create_nonce('closedpostboxes');
		$supplemental['replace-_ajax_linking_nonce'] = wp_create_nonce( 'internal-linking' );
		if ( $id ) {
			if ( $_POST['post_type'] == 'post' )
				$supplemental['replace-_wpnonce'] = wp_create_nonce('update-post_' . $id);
			elseif ( $_POST['post_type'] == 'page' )
				$supplemental['replace-_wpnonce'] = wp_create_nonce('update-page_' . $id);
		}
	}

	if ( ! empty($alert) )
		$supplemental['alert'] = $alert;

	$x = new WP_Ajax_Response( array(
		'what' => 'autosave',
		'id' => $id,
		'data' => $id ? $data : '',
		'supplemental' => $supplemental
	) );
	$x->send();
}

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

function wp_ajax_hidden_columns() {
	check_ajax_referer( 'screen-options-nonce', 'screenoptionnonce' );
	$hidden = isset( $_POST['hidden'] ) ? $_POST['hidden'] : '';
	$hidden = explode( ',', $_POST['hidden'] );
	$page = isset( $_POST['page'] ) ? $_POST['page'] : '';

	if ( $page != sanitize_key( $page ) )
		wp_die( 0 );

	if ( ! $user = wp_get_current_user() )
		wp_die( -1 );

	if ( is_array($hidden) )
		update_user_option($user->ID, "manage{$page}columnshidden", $hidden, true);

	wp_die( 1 );
}

function wp_ajax_update_welcome_panel() {
	check_ajax_referer( 'welcome-panel-nonce', 'welcomepanelnonce' );

	if ( ! current_user_can( 'edit_theme_options' ) )
		wp_die( -1 );

	update_user_meta( get_current_user_id(), 'show_welcome_panel', empty( $_POST['visible'] ) ? 0 : 1 );

	wp_die( 1 );
}

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
		$item = apply_filters( 'nav_menu_meta_box_object', $items[ $_POST['item-object'] ] );
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

		echo json_encode(array(
			'replace-id' => $type . '-' . $item->name,
			'markup' => $markup,
		));
	}

	wp_die();
}

function wp_ajax_wp_link_ajax() {
	check_ajax_referer( 'internal-linking', '_ajax_linking_nonce' );

	$args = array();

	if ( isset( $_POST['search'] ) )
		$args['s'] = stripslashes( $_POST['search'] );
	$args['pagenum'] = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

	require(ABSPATH . WPINC . '/class-wp-editor.php');
	$results = _WP_Editors::wp_link_query( $args );

	if ( ! isset( $results ) )
		wp_die( 0 );

	echo json_encode( $results );
	echo "\n";

	wp_die();
}

function wp_ajax_menu_locations_save() {
	if ( ! current_user_can( 'edit_theme_options' ) )
		wp_die( -1 );
	check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );
	if ( ! isset( $_POST['menu-locations'] ) )
		wp_die( 0 );
	set_theme_mod( 'nav_menu_locations', array_map( 'absint', $_POST['menu-locations'] ) );
	wp_die( 1 );
}

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

function wp_ajax_menu_quick_search() {
	if ( ! current_user_can( 'edit_theme_options' ) )
		wp_die( -1 );

	require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

	_wp_ajax_menu_quick_search( $_POST );

	wp_die();
}

function wp_ajax_get_permalink() {
	check_ajax_referer( 'getpermalink', 'getpermalinknonce' );
	$post_id = isset($_POST['post_id'])? intval($_POST['post_id']) : 0;
	wp_die( add_query_arg( array( 'preview' => 'true' ), get_permalink( $post_id ) ) );
}

function wp_ajax_sample_permalink() {
	check_ajax_referer( 'samplepermalink', 'samplepermalinknonce' );
	$post_id = isset($_POST['post_id'])? intval($_POST['post_id']) : 0;
	$title = isset($_POST['new_title'])? $_POST['new_title'] : '';
	$slug = isset($_POST['new_slug'])? $_POST['new_slug'] : null;
	wp_die( get_sample_permalink_html( $post_id, $title, $slug ) );
}

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

	set_current_screen( $_POST['screen'] );

	if ( $last = wp_check_post_lock( $post_ID ) ) {
		$last_user = get_userdata( $last );
		$last_user_name = $last_user ? $last_user->display_name : __( 'Someone' );
		printf( $_POST['post_type'] == 'page' ? __( 'Saving is disabled: %s is currently editing this page.' ) : __( 'Saving is disabled: %s is currently editing this post.' ),	esc_html( $last_user_name ) );
		wp_die();
	}

	$data = &$_POST;

	$post = get_post( $post_ID, ARRAY_A );
	$post = add_magic_quotes($post); //since it is from db

	$data['content'] = $post['post_content'];
	$data['excerpt'] = $post['post_excerpt'];

	// rename
	$data['user_ID'] = $GLOBALS['user_ID'];

	if ( isset($data['post_parent']) )
		$data['parent_id'] = $data['post_parent'];

	// status
	if ( isset($data['keep_private']) && 'private' == $data['keep_private'] )
		$data['post_status'] = 'private';
	else
		$data['post_status'] = $data['_status'];

	if ( empty($data['comment_status']) )
		$data['comment_status'] = 'closed';
	if ( empty($data['ping_status']) )
		$data['ping_status'] = 'closed';

	// update the post
	edit_post();

	$wp_list_table = _get_list_table('WP_Posts_List_Table');

	$mode = $_POST['post_view'];
	$wp_list_table->display_rows( array( get_post( $_POST['post_ID'] ) ) );

	wp_die();
}

function wp_ajax_inline_save_tax() {
	global $wp_list_table;

	check_ajax_referer( 'taxinlineeditnonce', '_inline_edit' );

	$taxonomy = sanitize_key( $_POST['taxonomy'] );
	$tax = get_taxonomy( $taxonomy );
	if ( ! $tax )
		wp_die( 0 );

	if ( ! current_user_can( $tax->cap->edit_terms ) )
		wp_die( -1 );

	set_current_screen( 'edit-' . $taxonomy );

	$wp_list_table = _get_list_table('WP_Terms_List_Table');

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

		echo $wp_list_table->single_row( $tag );
	} else {
		if ( is_wp_error($updated) && $updated->get_error_message() )
			wp_die( $updated->get_error_message() );
		wp_die( __( 'Item not updated.' ) );
	}

	wp_die();
}

function wp_ajax_find_posts() {
	global $wpdb;

	check_ajax_referer( 'find-posts' );

	if ( empty($_POST['ps']) )
		wp_die();

	if ( !empty($_POST['post_type']) && in_array( $_POST['post_type'], get_post_types() ) )
		$what = $_POST['post_type'];
	else
		$what = 'post';

	$s = stripslashes($_POST['ps']);
	preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $s, $matches);
	$search_terms = array_map('_search_terms_tidy', $matches[0]);

	$searchand = $search = '';
	foreach ( (array) $search_terms as $term ) {
		$term = esc_sql( like_escape( $term ) );
		$search .= "{$searchand}(($wpdb->posts.post_title LIKE '%{$term}%') OR ($wpdb->posts.post_content LIKE '%{$term}%'))";
		$searchand = ' AND ';
	}
	$term = esc_sql( like_escape( $s ) );
	if ( count($search_terms) > 1 && $search_terms[0] != $s )
		$search .= " OR ($wpdb->posts.post_title LIKE '%{$term}%') OR ($wpdb->posts.post_content LIKE '%{$term}%')";

	$posts = $wpdb->get_results( "SELECT ID, post_title, post_status, post_date FROM $wpdb->posts WHERE post_type = '$what' AND post_status IN ('draft', 'publish') AND ($search) ORDER BY post_date_gmt DESC LIMIT 50" );

	if ( ! $posts ) {
		$posttype = get_post_type_object($what);
		wp_die( $posttype->labels->not_found );
	}

	$html = '<table class="widefat" cellspacing="0"><thead><tr><th class="found-radio"><br /></th><th>'.__('Title').'</th><th>'.__('Date').'</th><th>'.__('Status').'</th></tr></thead><tbody>';
	foreach ( $posts as $post ) {

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

		$html .= '<tr class="found-posts"><td class="found-radio"><input type="radio" id="found-'.$post->ID.'" name="found_post_id" value="' . esc_attr($post->ID) . '"></td>';
		$html .= '<td><label for="found-'.$post->ID.'">'.esc_html( $post->post_title ).'</label></td><td>'.esc_html( $time ).'</td><td>'.esc_html( $stat ).'</td></tr>'."\n\n";
	}
	$html .= '</tbody></table>';

	$x = new WP_Ajax_Response();
	$x->add( array(
		'what' => $what,
		'data' => $html
	));
	$x->send();

}

function wp_ajax_widgets_order() {
	check_ajax_referer( 'save-sidebar-widgets', 'savewidgets' );

	if ( !current_user_can('edit_theme_options') )
		wp_die( -1 );

	unset( $_POST['savewidgets'], $_POST['action'] );

	// save widgets order for all sidebars
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

function wp_ajax_save_widget() {
	global $wp_registered_widgets, $wp_registered_widget_controls, $wp_registered_widget_updates;

	check_ajax_referer( 'save-sidebar-widgets', 'savewidgets' );

	if ( !current_user_can('edit_theme_options') || !isset($_POST['id_base']) )
		wp_die( -1 );

	unset( $_POST['savewidgets'], $_POST['action'] );

	do_action('load-widgets.php');
	do_action('widgets.php');
	do_action('sidebar_admin_setup');

	$id_base = $_POST['id_base'];
	$widget_id = $_POST['widget-id'];
	$sidebar_id = $_POST['sidebar'];
	$multi_number = !empty($_POST['multi_number']) ? (int) $_POST['multi_number'] : 0;
	$settings = isset($_POST['widget-' . $id_base]) && is_array($_POST['widget-' . $id_base]) ? $_POST['widget-' . $id_base] : false;
	$error = '<p>' . __('An error has occurred. Please reload the page and try again.') . '</p>';

	$sidebars = wp_get_sidebars_widgets();
	$sidebar = isset($sidebars[$sidebar_id]) ? $sidebars[$sidebar_id] : array();

	// delete
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

function wp_ajax_upload_attachment() {
	check_ajax_referer( 'media-form' );

	if ( ! current_user_can( 'upload_files' ) )
		wp_die( -1 );

	if ( isset( $_REQUEST['post_id'] ) ) {
		$post_id = $_REQUEST['post_id'];
		if ( ! current_user_can( 'edit_post', $post_id ) )
			wp_die( -1 );
	} else {
		$post_id = null;
	}

	$post_data = isset( $_REQUEST['post_data'] ) ? $_REQUEST['post_data'] : array();

	$attachment_id = media_handle_upload( 'async-upload', $post_id, $post_data );

	if ( is_wp_error( $attachment_id ) ) {
		echo json_encode( array(
			'type' => 'error',
			'data' => array(
				'message'  => $attachment_id->get_error_message(),
				'filename' => $_FILES['async-upload']['name'],
			),
		) );
		wp_die();
	}

	if ( isset( $post_data['context'] ) && isset( $post_data['theme'] ) ) {
		if ( 'custom-background' === $post_data['context'] )
			update_post_meta( $attachment_id, '_wp_attachment_is_custom_background', $post_data['theme'] );

		if ( 'custom-header' === $post_data['context'] )
			update_post_meta( $attachment_id, '_wp_attachment_is_custom_header', $post_data['theme'] );
	}

	$post = get_post( $attachment_id );

	echo json_encode( array(
		'type' => 'success',
		'data' => array(
			'id'       => $attachment_id,
			'title'    => esc_attr( $post->post_title ),
			'filename' => esc_html( basename( $post->guid ) ),
			'url'      => wp_get_attachment_url( $attachment_id ),
			'meta'     => wp_get_attachment_metadata( $attachment_id ),
		),
	) );
	wp_die();
}

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
			$msg = json_encode($msg);
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

function wp_ajax_set_post_thumbnail() {
	$post_ID = intval( $_POST['post_id'] );
	if ( !current_user_can( 'edit_post', $post_ID ) )
		wp_die( -1 );
	$thumbnail_id = intval( $_POST['thumbnail_id'] );

	check_ajax_referer( "set_post_thumbnail-$post_ID" );

	if ( $thumbnail_id == '-1' ) {
		if ( delete_post_thumbnail( $post_ID ) )
			wp_die( _wp_post_thumbnail_html( null, $post_ID ) );
		else
			wp_die( 0 );
	}

	if ( set_post_thumbnail( $post_ID, $thumbnail_id ) )
		wp_die( _wp_post_thumbnail_html( $thumbnail_id, $post_ID ) );
	wp_die( 0 );
}

function wp_ajax_date_format() {
	wp_die( date_i18n( sanitize_option( 'date_format', $_POST['date'] ) ) );
}

function wp_ajax_time_format() {
	wp_die( date_i18n( sanitize_option( 'time_format', $_POST['date'] ) ) );
}

function wp_ajax_wp_fullscreen_save_post() {
	$post_id = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : 0;

	$post = $post_type = null;

	if ( $post_id )
		$post = get_post( $post_id );

	if ( $post )
		$post_type = $post->post_type;
	elseif ( isset( $_POST['post_type'] ) && post_type_exists( $_POST['post_type'] ) )
		$post_type = $_POST['post_type'];

	check_ajax_referer('update-' . $post_type . '_' . $post_id, '_wpnonce');

	$post_id = edit_post();

	if ( is_wp_error($post_id) ) {
		if ( $post_id->get_error_message() )
			$message = $post_id->get_error_message();
		else
			$message = __('Save failed');

		echo json_encode( array( 'message' => $message, 'last_edited' => '' ) );
		wp_die();
	} else {
		$message = __('Saved.');
	}

	if ( $post ) {
		$last_date = mysql2date( get_option('date_format'), $post->post_modified );
		$last_time = mysql2date( get_option('time_format'), $post->post_modified );
	} else {
		$last_date = date_i18n( get_option('date_format') );
		$last_time = date_i18n( get_option('time_format') );
	}

	if ( $last_id = get_post_meta($post_id, '_edit_last', true) ) {
		$last_user = get_userdata($last_id);
		$last_edited = sprintf( __('Last edited by %1$s on %2$s at %3$s'), esc_html( $last_user->display_name ), $last_date, $last_time );
	} else {
		$last_edited = sprintf( __('Last edited on %1$s at %2$s'), $last_date, $last_time );
	}

	echo json_encode( array( 'message' => $message, 'last_edited' => $last_edited ) );
	wp_die();
}

function wp_ajax_wp_remove_post_lock() {
	if ( empty( $_POST['post_ID'] ) || empty( $_POST['active_post_lock'] ) )
		wp_die( 0 );
	$post_id = (int) $_POST['post_ID'];
	if ( ! $post = get_post( $post_id ) )
		wp_die( 0 );

	check_ajax_referer( 'update-' . $post->post_type . '_' . $post_id );

	if ( ! current_user_can( 'edit_post', $post_id ) )
		wp_die( -1 );

	$active_lock = array_map( 'absint', explode( ':', $_POST['active_post_lock'] ) );
	if ( $active_lock[1] != get_current_user_id() )
		wp_die( 0 );

	$new_lock = ( time() - apply_filters( 'wp_check_post_lock_window', AUTOSAVE_INTERVAL * 2 ) + 5 ) . ':' . $active_lock[1];
	update_post_meta( $post_id, '_edit_lock', $new_lock, implode( ':', $active_lock ) );
	wp_die( 1 );
}

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
