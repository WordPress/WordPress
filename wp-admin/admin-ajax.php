<?php
/**
 * WordPress AJAX Process Execution.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Executing AJAX process.
 *
 * @since unknown
 */
define('DOING_AJAX', true);
define('WP_ADMIN', true);

require_once('../wp-load.php');
require_once('includes/admin.php');

if ( ! is_user_logged_in() ) {

	if ( $_POST['action'] == 'autosave' ) {
		$id = isset($_POST['post_ID'])? (int) $_POST['post_ID'] : 0;

		if ( ! $id )
			die('-1');

		$message = sprintf( __('<strong>ALERT: You are logged out!</strong> Could not save draft. <a href="%s" target="blank">Please log in again.</a>'), wp_login_url() );
			$x = new WP_Ajax_Response( array(
				'what' => 'autosave',
				'id' => $id,
				'data' => $message
			) );
			$x->send();
	}

	die('-1');
}

if ( isset( $_GET['action'] ) ) :
switch ( $action = $_GET['action'] ) :
case 'ajax-tag-search' :
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');

	$s = $_GET['q']; // is this slashed already?

	if ( false !== strpos( $s, ',' ) ) {
		$s = explode( ',', $s );
		$s = $s[count( $s ) - 1];
	}
	$s = trim( $s );
	if ( strlen( $s ) < 2 )
		die; // require 2 chars for matching
	$results = $wpdb->get_col( "SELECT t.name FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = 'post_tag' AND t.name LIKE ('%". $s . "%')" );
	echo join( $results, "\n" );
	die;
	break;
default :
	do_action( 'wp_ajax_' . $_GET['action'] );
	die('0');
	break;
endswitch;
endif;

$id = isset($_POST['id'])? (int) $_POST['id'] : 0;
switch ( $action = $_POST['action'] ) :
case 'delete-comment' :
	check_ajax_referer( "delete-comment_$id" );
	if ( !$comment = get_comment( $id ) )
		die('1');
	if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) )
		die('-1');

	if ( isset($_POST['spam']) && 1 == $_POST['spam'] ) {
		if ( 'spam' == wp_get_comment_status( $comment->comment_ID ) )
			die('1');
		$r = wp_set_comment_status( $comment->comment_ID, 'spam' );
	} else {
		$r = wp_delete_comment( $comment->comment_ID );
	}

	die( $r ? '1' : '0' );
	break;
case 'delete-cat' :
	check_ajax_referer( "delete-category_$id" );
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');

	$cat = get_category( $id );
	if ( !$cat || is_wp_error( $cat ) )
		die('1');

	if ( wp_delete_category( $id ) )
		die('1');
	else
		die('0');
	break;
case 'delete-tag' :
	check_ajax_referer( "delete-tag_$id" );
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');

	$tag = get_term( $id, 'post_tag' );
	if ( !$tag || is_wp_error( $tag ) )
		die('1');

	if ( wp_delete_term($id, 'post_tag'))
		die('1');
	else
		die('0');
	break;
case 'delete-link-cat' :
	check_ajax_referer( "delete-link-category_$id" );
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');

	$cat = get_term( $id, 'link_category' );
	if ( !$cat || is_wp_error( $cat ) )
		die('1');

	$cat_name = get_term_field('name', $id, 'link_category');

	// Don't delete the default cats.
	if ( $id == get_option('default_link_category') ) {
		$x = new WP_AJAX_Response( array(
			'what' => 'link-cat',
			'id' => $id,
			'data' => new WP_Error( 'default-link-cat', sprintf(__("Can&#8217;t delete the <strong>%s</strong> category: this is the default one"), $cat_name) )
		) );
		$x->send();
	}

	$r = wp_delete_term($id, 'link_category');
	if ( !$r )
		die('0');
	if ( is_wp_error($r) ) {
		$x = new WP_AJAX_Response( array(
			'what' => 'link-cat',
			'id' => $id,
			'data' => $r
		) );
		$x->send();
	}
	die('1');
	break;
case 'delete-link' :
	check_ajax_referer( "delete-bookmark_$id" );
	if ( !current_user_can( 'manage_links' ) )
		die('-1');

	$link = get_bookmark( $id );
	if ( !$link || is_wp_error( $link ) )
		die('1');

	if ( wp_delete_link( $id ) )
		die('1');
	else
		die('0');
	break;
case 'delete-meta' :
	check_ajax_referer( "delete-meta_$id" );
	if ( !$meta = get_post_meta_by_id( $id ) )
		die('1');

	if ( !current_user_can( 'edit_post', $meta->post_id ) )
		die('-1');
	if ( delete_meta( $meta->meta_id ) )
		die('1');
	die('0');
	break;
case 'delete-post' :
	check_ajax_referer( "{$action}_$id" );
	if ( !current_user_can( 'delete_post', $id ) )
		die('-1');

	if ( !get_post( $id ) )
		die('1');

	if ( wp_delete_post( $id ) )
		die('1');
	else
		die('0');
	break;
case 'delete-page' :
	check_ajax_referer( "{$action}_$id" );
	if ( !current_user_can( 'delete_page', $id ) )
		die('-1');

	if ( !get_page( $id ) )
		die('1');

	if ( wp_delete_post( $id ) )
		die('1');
	else
		die('0');
	break;
case 'dim-comment' :
	if ( !$comment = get_comment( $id ) )
		die('0');

	if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) )
		die('-1');
	if ( !current_user_can( 'moderate_comments' ) )
		die('-1');

	$current = wp_get_comment_status( $comment->comment_ID );
	if ( $_POST['new'] == $current )
		die('1');

	if ( in_array( $current, array( 'unapproved', 'spam' ) ) ) {
		check_ajax_referer( "approve-comment_$id" );
		if ( wp_set_comment_status( $comment->comment_ID, 'approve' ) )
			die('1');
	} else {
		check_ajax_referer( "unapprove-comment_$id" );
		if ( wp_set_comment_status( $comment->comment_ID, 'hold' ) )
			die('1');
	}
	die('0');
	break;
case 'add-category' : // On the Fly
	check_ajax_referer( $action );
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');
	$names = explode(',', $_POST['newcat']);
	if ( 0 > $parent = (int) $_POST['newcat_parent'] )
		$parent = 0;
	$post_category = isset($_POST['post_category'])? (array) $_POST['post_category'] : array();
	$checked_categories = array_map( 'absint', (array) $post_category );
	$popular_ids = isset( $_POST['popular_ids'] ) ?
			array_map( 'absint', explode( ',', $_POST['popular_ids'] ) ) :
			false;

	$x = new WP_Ajax_Response();
	foreach ( $names as $cat_name ) {
		$cat_name = trim($cat_name);
		$category_nicename = sanitize_title($cat_name);
		if ( '' === $category_nicename )
			continue;
		$cat_id = wp_create_category( $cat_name, $parent );
		$checked_categories[] = $cat_id;
		if ( $parent ) // Do these all at once in a second
			continue;
		$category = get_category( $cat_id );
		ob_start();
			wp_category_checklist( 0, $cat_id, $checked_categories, $popular_ids );
		$data = ob_get_contents();
		ob_end_clean();
		$x->add( array(
			'what' => 'category',
			'id' => $cat_id,
			'data' => $data,
			'position' => -1
		) );
	}
	if ( $parent ) { // Foncy - replace the parent and all its children
		$parent = get_category( $parent );
		ob_start();
			dropdown_categories( 0, $parent );
		$data = ob_get_contents();
		ob_end_clean();
		$x->add( array(
			'what' => 'category',
			'id' => $parent->term_id,
			'old_id' => $parent->term_id,
			'data' => $data,
			'position' => -1
		) );

	}
	$x->send();
	break;
case 'add-link-category' : // On the Fly
	check_ajax_referer( $action );
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');
	$names = explode(',', $_POST['newcat']);
	$x = new WP_Ajax_Response();
	foreach ( $names as $cat_name ) {
		$cat_name = trim($cat_name);
		$slug = sanitize_title($cat_name);
		if ( '' === $slug )
			continue;
		if ( !$cat_id = is_term( $cat_name, 'link_category' ) ) {
			$cat_id = wp_insert_term( $cat_name, 'link_category' );
		}
		$cat_id = $cat_id['term_id'];
		$cat_name = wp_specialchars(stripslashes($cat_name));
		$x->add( array(
			'what' => 'link-category',
			'id' => $cat_id,
			'data' => "<li id='link-category-$cat_id'><label for='in-link-category-$cat_id' class='selectit'><input value='$cat_id' type='checkbox' checked='checked' name='link_category[]' id='in-link-category-$cat_id'/> $cat_name</label></li>",
			'position' => -1
		) );
	}
	$x->send();
	break;
case 'add-cat' : // From Manage->Categories
	check_ajax_referer( 'add-category' );
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');

	if ( '' === trim($_POST['cat_name']) ) {
		$x = new WP_Ajax_Response( array(
			'what' => 'cat',
			'id' => new WP_Error( 'cat_name', __('You did not enter a category name.') )
		) );
		$x->send();
	}

	if ( category_exists( trim( $_POST['cat_name'] ) ) ) {
		$x = new WP_Ajax_Response( array(
			'what' => 'cat',
			'id' => new WP_Error( 'cat_exists', __('The category you are trying to create already exists.'), array( 'form-field' => 'cat_name' ) ),
		) );
		$x->send();
	}

	$cat = wp_insert_category( $_POST, true );

	if ( is_wp_error($cat) ) {
		$x = new WP_Ajax_Response( array(
			'what' => 'cat',
			'id' => $cat
		) );
		$x->send();
	}

	if ( !$cat || (!$cat = get_category( $cat )) )
		die('0');

	$level = 0;
	$cat_full_name = $cat->name;
	$_cat = $cat;
	while ( $_cat->parent ) {
		$_cat = get_category( $_cat->parent );
		$cat_full_name = $_cat->name . ' &#8212; ' . $cat_full_name;
		$level++;
	}
	$cat_full_name = attribute_escape($cat_full_name);

	$x = new WP_Ajax_Response( array(
		'what' => 'cat',
		'id' => $cat->term_id,
		'position' => -1,
		'data' => _cat_row( $cat, $level, $cat_full_name ),
		'supplemental' => array('name' => $cat_full_name, 'show-link' => sprintf(__( 'Category <a href="#%s">%s</a> added' ), "cat-$cat->term_id", $cat_full_name))
	) );
	$x->send();
	break;
case 'add-link-cat' : // From Blogroll -> Categories
	check_ajax_referer( 'add-link-category' );
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');

	if ( '' === trim($_POST['name']) ) {
		$x = new WP_Ajax_Response( array(
			'what' => 'link-cat',
			'id' => new WP_Error( 'name', __('You did not enter a category name.') )
		) );
		$x->send();
	}

	$r = wp_insert_term($_POST['name'], 'link_category', $_POST );
	if ( is_wp_error( $r ) ) {
		$x = new WP_AJAX_Response( array(
			'what' => 'link-cat',
			'id' => $r
		) );
		$x->send();
	}

	extract($r, EXTR_SKIP);

	if ( !$link_cat = link_cat_row( $term_id ) )
		die('0');

	$x = new WP_Ajax_Response( array(
		'what' => 'link-cat',
		'id' => $term_id,
		'position' => -1,
		'data' => $link_cat
	) );
	$x->send();
	break;
case 'add-tag' : // From Manage->Tags
	check_ajax_referer( 'add-tag' );
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');

	if ( '' === trim($_POST['name']) ) {
		$x = new WP_Ajax_Response( array(
			'what' => 'tag',
			'id' => new WP_Error( 'name', __('You did not enter a tag name.') )
		) );
		$x->send();
	}

	$tag = wp_insert_term($_POST['name'], 'post_tag', $_POST );

	if ( is_wp_error($tag) ) {
		$x = new WP_Ajax_Response( array(
			'what' => 'tag',
			'id' => $tag
		) );
		$x->send();
	}

	if ( !$tag || (!$tag = get_term( $tag['term_id'], 'post_tag' )) )
		die('0');

	$tag_full_name = $tag->name;
	$tag_full_name = attribute_escape($tag_full_name);

	$x = new WP_Ajax_Response( array(
		'what' => 'tag',
		'id' => $tag->term_id,
		'position' => '-1',
		'data' => _tag_row( $tag ),
		'supplemental' => array('name' => $tag_full_name, 'show-link' => sprintf(__( 'Tag <a href="#%s">%s</a> added' ), "tag-$tag->term_id", $tag_full_name))
	) );
	$x->send();
	break;
case 'get-tagcloud' :
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');

	$tags = get_tags( array( 'number' => 45, 'orderby' => 'count', 'order' => 'DESC' ) );

	if ( empty( $tags ) )
		die( __('No tags found!') );

	if ( is_wp_error($tags) )
		die($tags->get_error_message());

	foreach ( $tags as $key => $tag ) {
		$tags[ $key ]->link = '#';
		$tags[ $key ]->id = $tag->term_id;
	}

	$return = wp_generate_tag_cloud( $tags );

	if ( empty($return) )
		die('0');

	echo $return;

	exit;
	break;
case 'add-comment' :
	check_ajax_referer( $action );
	if ( !current_user_can( 'edit_post', $id ) )
		die('-1');
	$search = isset($_POST['s']) ? $_POST['s'] : false;
	$start = isset($_POST['page']) ? intval($_POST['page']) * 25 - 1: 24;
	$status = isset($_POST['comment_status']) ? $_POST['comment_status'] : false;
	$mode = isset($_POST['mode']) ? $_POST['mode'] : 'detail';
	$p = isset($_POST['p']) ? $_POST['p'] : 0;
	$comment_type = isset($_POST['comment_type']) ? $_POST['comment_type'] : '';
	list($comments, $total) = _wp_get_comment_list( $status, $search, $start, 1, $p, $comment_type );

	if ( get_option('show_avatars') )
		add_filter( 'comment_author', 'floated_admin_avatar' );

	if ( !$comments )
		die('1');
	$x = new WP_Ajax_Response();
	foreach ( (array) $comments as $comment ) {
		get_comment( $comment );
		ob_start();
			_wp_comment_row( $comment->comment_ID, $mode, $status, true, true );
			$comment_list_item = ob_get_contents();
		ob_end_clean();
		$x->add( array(
			'what' => 'comment',
			'id' => $comment->comment_ID,
			'data' => $comment_list_item
		) );
	}
	$x->send();
	break;
case 'get-comments' :
	check_ajax_referer( $action );

	$post_ID = (int) $_POST['post_ID'];
	if ( !current_user_can( 'edit_post', $post_ID ) )
		die('-1');

	$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
	$num = isset($_POST['num']) ? intval($_POST['num']) : 10;

	list($comments, $total) = _wp_get_comment_list( false, false, $start, $num, $post_ID );

	if ( !$comments )
		die('1');

	$comment_list_item = '';
	$x = new WP_Ajax_Response();
	foreach ( (array) $comments as $comment ) {
		get_comment( $comment );
		ob_start();
			_wp_comment_row( $comment->comment_ID, 'single', false, false );
			$comment_list_item .= ob_get_contents();
		ob_end_clean();
	}
	$x->add( array(
		'what' => 'comments',
		'data' => $comment_list_item
	) );
	$x->send();
	break;
case 'replyto-comment' :
	check_ajax_referer( $action );

	$comment_post_ID = (int) $_POST['comment_post_ID'];
	if ( !current_user_can( 'edit_post', $comment_post_ID ) )
		die('-1');

	$status = $wpdb->get_var( $wpdb->prepare("SELECT post_status FROM $wpdb->posts WHERE ID = %d", $comment_post_ID) );

	if ( empty($status) )
		die('1');
	elseif ( in_array($status, array('draft', 'pending') ) )
		die( __('Error: you are replying to a comment on a draft post.') );

	$user = wp_get_current_user();
	if ( $user->ID ) {
		$comment_author       = $wpdb->escape($user->display_name);
		$comment_author_email = $wpdb->escape($user->user_email);
		$comment_author_url   = $wpdb->escape($user->user_url);
		$comment_content      = trim($_POST['content']);
		if ( current_user_can('unfiltered_html') ) {
			if ( wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment'] ) {
				kses_remove_filters(); // start with a clean slate
				kses_init_filters(); // set up the filters
			}
		}
	} else {
		die( __('Sorry, you must be logged in to reply to a comment.') );
	}

	if ( '' == $comment_content )
		die( __('Error: please type a comment.') );

	$comment_parent = absint($_POST['comment_ID']);
	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

	$comment_id = wp_new_comment( $commentdata );
	$comment = get_comment($comment_id);
	if ( ! $comment ) die('1');

	$modes = array( 'single', 'detail', 'dashboard' );
	$mode = isset($_POST['mode']) && in_array( $_POST['mode'], $modes ) ? $_POST['mode'] : 'detail';
	$position = ( isset($_POST['position']) && (int) $_POST['position']) ? (int) $_POST['position'] : '-1';
	$checkbox = ( isset($_POST['checkbox']) && true == $_POST['checkbox'] ) ? 1 : 0;

	if ( get_option('show_avatars') && 'single' != $mode )
		add_filter( 'comment_author', 'floated_admin_avatar' );

	$x = new WP_Ajax_Response();

	ob_start();
		if ( 'dashboard' == $mode ) {
			require_once( ABSPATH . 'wp-admin/includes/dashboard.php' );
			_wp_dashboard_recent_comments_row( $comment, false );
		} else {
			_wp_comment_row( $comment->comment_ID, $mode, false, $checkbox );
		}
		$comment_list_item = ob_get_contents();
	ob_end_clean();

	$x->add( array(
		'what' => 'comment',
		'id' => $comment->comment_ID,
		'data' => $comment_list_item,
		'position' => $position
	));

	$x->send();
	break;
case 'edit-comment' :
	check_ajax_referer( 'replyto-comment' );

	$comment_post_ID = (int) $_POST['comment_post_ID'];
	if ( ! current_user_can( 'edit_post', $comment_post_ID ) )
		die('-1');

	if ( '' == $_POST['content'] )
		die( __('Error: please type a comment.') );

	$comment_id = (int) $_POST['comment_ID'];
	$_POST['comment_status'] = $_POST['status'];
	edit_comment();

	$mode = ( isset($_POST['mode']) && 'single' == $_POST['mode'] ) ? 'single' : 'detail';
	$position = ( isset($_POST['position']) && (int) $_POST['position']) ? (int) $_POST['position'] : '-1';
	$checkbox = ( isset($_POST['checkbox']) && true == $_POST['checkbox'] ) ? 1 : 0;

	if ( get_option('show_avatars') && 'single' != $mode )
		add_filter( 'comment_author', 'floated_admin_avatar' );

	$x = new WP_Ajax_Response();

	ob_start();
		_wp_comment_row( $comment_id, $mode, true, $checkbox );
		$comment_list_item = ob_get_contents();
	ob_end_clean();

	$x->add( array(
		'what' => 'edit_comment',
		'id' => $comment->comment_ID,
		'data' => $comment_list_item,
		'position' => $position
	));

	$x->send();
	break;
case 'add-meta' :
	check_ajax_referer( 'add-meta' );
	$c = 0;
	$pid = (int) $_POST['post_id'];
	if ( isset($_POST['metakeyselect']) || isset($_POST['metakeyinput']) ) {
		if ( !current_user_can( 'edit_post', $pid ) )
			die('-1');
		if ( '#NONE#' == $_POST['metakeyselect'] && empty($_POST['metakeyinput']) )
			die('1');
		if ( $pid < 0 ) {
			$now = current_time('timestamp', 1);
			if ( $pid = wp_insert_post( array(
				'post_title' => sprintf('Draft created on %s at %s', date(get_option('date_format'), $now), date(get_option('time_format'), $now))
			) ) ) {
				if ( is_wp_error( $pid ) ) {
					$x = new WP_Ajax_Response( array(
						'what' => 'meta',
						'data' => $pid
					) );
					$x->send();
				}
				$mid = add_meta( $pid );
			} else {
				die('0');
			}
		} else if ( !$mid = add_meta( $pid ) ) {
			die('0');
		}

		$meta = get_post_meta_by_id( $mid );
		$pid = (int) $meta->post_id;
		$meta = get_object_vars( $meta );
		$x = new WP_Ajax_Response( array(
			'what' => 'meta',
			'id' => $mid,
			'data' => _list_meta_row( $meta, $c ),
			'position' => 1,
			'supplemental' => array('postid' => $pid)
		) );
	} else {
		$mid = (int) array_pop(array_keys($_POST['meta']));
		$key = $_POST['meta'][$mid]['key'];
		$value = $_POST['meta'][$mid]['value'];
		if ( !$meta = get_post_meta_by_id( $mid ) )
			die('0'); // if meta doesn't exist
		if ( !current_user_can( 'edit_post', $meta->post_id ) )
			die('-1');
		if ( !$u = update_meta( $mid, $key, $value ) )
			die('1'); // We know meta exists; we also know it's unchanged (or DB error, in which case there are bigger problems).
		$key = stripslashes($key);
		$value = stripslashes($value);
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
	break;
case 'add-user' :
	check_ajax_referer( $action );
	if ( !current_user_can('create_users') )
		die('-1');
	require_once(ABSPATH . WPINC . '/registration.php');
	if ( !$user_id = add_user() )
		die('0');
	elseif ( is_wp_error( $user_id ) ) {
		$x = new WP_Ajax_Response( array(
			'what' => 'user',
			'id' => $user_id
		) );
		$x->send();
	}
	$user_object = new WP_User( $user_id );

	$x = new WP_Ajax_Response( array(
		'what' => 'user',
		'id' => $user_id,
		'data' => user_row( $user_object, '', $user_object->roles[0] ),
		'supplemental' => array(
			'show-link' => sprintf(__( 'User <a href="#%s">%s</a> added' ), "user-$user_id", $user_object->user_login),
			'role' => $user_object->roles[0]
		)
	) );
	$x->send();
	break;
case 'autosave' : // The name of this action is hardcoded in edit_post()
	define( 'DOING_AUTOSAVE', true );

	$nonce_age = check_ajax_referer( 'autosave', 'autosavenonce' );
	global $current_user;

	$_POST['post_category'] = explode(",", $_POST['catslist']);
	$_POST['tags_input'] = explode(",", $_POST['tags_input']);
	if($_POST['post_type'] == 'page' || empty($_POST['post_category']))
		unset($_POST['post_category']);

	$do_autosave = (bool) $_POST['autosave'];
	$do_lock = true;

	$data = '';
	$message = sprintf( __('Draft Saved at %s.'), date_i18n( __('g:i:s a') ) );

	$supplemental = array();

	$id = $revision_id = 0;
	if($_POST['post_ID'] < 0) {
		$_POST['post_status'] = 'draft';
		$_POST['temp_ID'] = $_POST['post_ID'];
		if ( $do_autosave ) {
			$id = wp_write_post();
			$data = $message;
		}
	} else {
		$post_ID = (int) $_POST['post_ID'];
		$_POST['ID'] = $post_ID;
		$post = get_post($post_ID);

		if ( $last = wp_check_post_lock( $post->ID ) ) {
			$do_autosave = $do_lock = false;

			$last_user = get_userdata( $last );
			$last_user_name = $last_user ? $last_user->display_name : __( 'Someone' );
			$data = new WP_Error( 'locked', sprintf(
				$_POST['post_type'] == 'page' ? __( 'Autosave disabled: %s is currently editing this page.' ) : __( 'Autosave disabled: %s is currently editing this post.' ),
				wp_specialchars( $last_user_name )
			) );

			$supplemental['disable_autosave'] = 'disable';
		}

		if ( 'page' == $post->post_type ) {
			if ( !current_user_can('edit_page', $post_ID) )
				die(__('You are not allowed to edit this page.'));
		} else {
			if ( !current_user_can('edit_post', $post_ID) )
				die(__('You are not allowed to edit this post.'));
		}

		if ( $do_autosave ) {
			// Drafts are just overwritten by autosave
			if ( 'draft' == $post->post_status ) {
				$id = edit_post();
			} else { // Non drafts are not overwritten.  The autosave is stored in a special post revision.
				$revision_id = wp_create_post_autosave( $post->ID );
				if ( is_wp_error($revision_id) )
					$id = $revision_id;
				else
					$id = $post->ID;
			}
			$data = $message;
		} else {
			$id = $post->ID;
		}
	}

	if ( $do_lock && $id && is_numeric($id) )
		wp_set_post_lock( $id );

	if ( $nonce_age == 2 ) {
		$supplemental['replace-autosavenonce'] = wp_create_nonce('autosave');
		$supplemental['replace-getpermalinknonce'] = wp_create_nonce('getpermalink');
		$supplemental['replace-samplepermalinknonce'] = wp_create_nonce('samplepermalink');
		$supplemental['replace-closedpostboxesnonce'] = wp_create_nonce('closedpostboxes');
		if ( $id ) {
			if ( $_POST['post_type'] == 'post' )
				$supplemental['replace-_wpnonce'] = wp_create_nonce('update-post_' . $id);
			elseif ( $_POST['post_type'] == 'page' )
				$supplemental['replace-_wpnonce'] = wp_create_nonce('update-page_' . $id);
		}
	}

	$x = new WP_Ajax_Response( array(
		'what' => 'autosave',
		'id' => $id,
		'data' => $id ? $data : '',
		'supplemental' => $supplemental
	) );
	$x->send();
	break;
case 'autosave-generate-nonces' :
	check_ajax_referer( 'autosave', 'autosavenonce' );
	$ID = (int) $_POST['post_ID'];
	if($_POST['post_type'] == 'post') {
		if(current_user_can('edit_post', $ID))
			die(wp_create_nonce('update-post_' . $ID));
	}
	if($_POST['post_type'] == 'page') {
		if(current_user_can('edit_page', $ID)) {
			die(wp_create_nonce('update-page_' . $ID));
		}
	}
	die('0');
break;
case 'closed-postboxes' :
	check_ajax_referer( 'closedpostboxes', 'closedpostboxesnonce' );
	$closed = isset( $_POST['closed'] )? $_POST['closed'] : '';
	$closed = explode( ',', $_POST['closed'] );
	$hidden = isset( $_POST['hidden'] )? $_POST['hidden'] : '';
	$hidden = explode( ',', $_POST['hidden'] );
	$page = isset( $_POST['page'] )? $_POST['page'] : '';
	if ( !preg_match( '/^[a-z-_]+$/', $page ) ) {
		die(-1);
	}
	$current_user = wp_get_current_user();
	if ( is_array($closed) )
		update_usermeta($current_user->ID, 'closedpostboxes_'.$page, $closed);
	if ( is_array($hidden) )
		update_usermeta($current_user->ID, 'meta-box-hidden_'.$page, $hidden);
break;
case 'hidden-columns' :
	check_ajax_referer( 'hiddencolumns', 'hiddencolumnsnonce' );
	$hidden = isset( $_POST['hidden'] )? $_POST['hidden'] : '';
	$hidden = explode( ',', $_POST['hidden'] );
	$page = isset( $_POST['page'] )? $_POST['page'] : '';
	if ( !preg_match( '/^[a-z-_]+$/', $page ) ) {
		die(-1);
	}
	$current_user = wp_get_current_user();
	if ( is_array($hidden) )
		update_usermeta($current_user->ID, "manage-$page-columns-hidden", $hidden);
break;
case 'get-permalink':
	check_ajax_referer( 'getpermalink', 'getpermalinknonce' );
	$post_id = isset($_POST['post_id'])? intval($_POST['post_id']) : 0;
	die(add_query_arg(array('preview' => 'true'), get_permalink($post_id)));
break;
case 'sample-permalink':
	check_ajax_referer( 'samplepermalink', 'samplepermalinknonce' );
	$post_id = isset($_POST['post_id'])? intval($_POST['post_id']) : 0;
	$title = isset($_POST['new_title'])? $_POST['new_title'] : '';
	$slug = isset($_POST['new_slug'])? $_POST['new_slug'] : '';
	die(get_sample_permalink_html($post_id, $title, $slug));
break;
case 'inline-save':
	check_ajax_referer( 'inlineeditnonce', '_inline_edit' );

	if ( ! isset($_POST['post_ID']) || ! ( $post_ID = (int) $_POST['post_ID'] ) )
		exit;

	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_ID ) )
			die( __('You are not allowed to edit this page.') );
	} else {
		if ( ! current_user_can( 'edit_post', $post_ID ) )
			die( __('You are not allowed to edit this post.') );
	}

	if ( $last = wp_check_post_lock( $post_ID ) ) {
		$last_user = get_userdata( $last );
		$last_user_name = $last_user ? $last_user->display_name : __( 'Someone' );
		printf( $_POST['post_type'] == 'page' ? __( 'Saving is disabled: %s is currently editing this page.' ) : __( 'Saving is disabled: %s is currently editing this post.' ),	wp_specialchars( $last_user_name ) );
		exit;
	}

	$data = &$_POST;
	$post = get_post( $post_ID, ARRAY_A );
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
	$_POST = $data;
	edit_post();

	$post = array();
	if ( 'page' == $_POST['post_type'] ) {
		$post[] = get_post($_POST['post_ID']);
		page_rows($post);
	} elseif ( 'post' == $_POST['post_type'] ) {
		$mode = $_POST['post_view'];
		$post[] = get_post($_POST['post_ID']);
		post_rows($post);
	}

	exit;
	break;
case 'inline-save-tax':
	check_ajax_referer( 'taxinlineeditnonce', '_inline_edit' );

	if ( ! current_user_can('manage_categories') )
		die( __('Cheatin&#8217; uh?') );

	if ( ! isset($_POST['tax_ID']) || ! ( $id = (int) $_POST['tax_ID'] ) )
		die(-1);

	switch ($_POST['tax_type']) {
		case 'cat' :
			$data = array();
			$data['cat_ID'] = $id;
			$data['cat_name'] = $_POST['name'];
			$data['category_nicename'] = $_POST['slug'];
			if ( isset($_POST['parent']) && (int) $_POST['parent'] > 0 )
				$data['category_parent'] = $_POST['parent'];

			$cat = get_category($id, ARRAY_A);
			$data['category_description'] = $cat['category_description'];

			$updated = wp_update_category($data);

			if ( $updated && !is_wp_error($updated) )
				echo _cat_row( $updated, 0 );
			else
				die( __('Category not updated.') );

			break;
		case 'link-cat' :
			$updated = wp_update_term($id, 'link_category', $_POST);

			if ( $updated && !is_wp_error($updated) )
				echo link_cat_row($updated['term_id']);
			else
				die( __('Category not updated.') );

			break;
		case 'tag' :
			$updated = wp_update_term($id, 'post_tag', $_POST);
			if ( $updated && !is_wp_error($updated) ) {
				$tag = get_term( $updated['term_id'], 'post_tag' );
				if ( !$tag || is_wp_error( $tag ) )
					die( __('Tag not updated.') );

				echo _tag_row($tag);
			} else {
				die( __('Tag not updated.') );
			}

			break;
	}

	exit;
	break;
case 'meta-box-order':
	check_ajax_referer( 'meta-box-order' );
	update_user_option( $GLOBALS['current_user']->ID, "meta-box-order_$_POST[page]", $_POST['order'] );
	die('1');
	break;
case 'find_posts':
	check_ajax_referer( 'find-posts' );

	if ( empty($_POST['ps']) )
		exit;

	$what = isset($_POST['pages']) ? 'page' : 'post';
	$s = stripslashes($_POST['ps']);
	preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $s, $matches);
	$search_terms = array_map(create_function('$a', 'return trim($a, "\\"\'\\n\\r ");'), $matches[0]);

	$searchand = $search = '';
	foreach( (array) $search_terms as $term) {
		$term = addslashes_gpc($term);
		$search .= "{$searchand}(($wpdb->posts.post_title LIKE '%{$term}%') OR ($wpdb->posts.post_content LIKE '%{$term}%'))";
		$searchand = ' AND ';
	}
	$term = $wpdb->escape($s);
	if ( count($search_terms) > 1 && $search_terms[0] != $s )
		$search .= " OR ($wpdb->posts.post_title LIKE '%{$term}%') OR ($wpdb->posts.post_content LIKE '%{$term}%')";

	$posts = $wpdb->get_results( "SELECT ID, post_title, post_status, post_date FROM $wpdb->posts WHERE post_type = '$what' AND $search ORDER BY post_date_gmt DESC LIMIT 50" );

	if ( ! $posts )
		exit( __('No posts found.') );

	$html = '<table class="widefat"><thead><tr><th class="found-radio"><br /></th><th>'.__('Title').'</th><th>'.__('Time').'</th><th>'.__('Status').'</th></tr></thead><tbody>';
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
				$stat = __('Unpublished');
				break;
		}

		if ( '0000-00-00 00:00:00' == $post->post_date ) {
			$time = '';
		} else {
			$time = mysql2date(__('Y/m/d'), $post->post_date);
		}

		$html .= '<tr class="found-posts"><td class="found-radio"><input type="radio" id="found-'.$post->ID.'" name="found_post_id" value="'.$post->ID.'"></td>';
		$html .= '<td><label for="found-'.$post->ID.'">'.wp_specialchars($post->post_title, true).'</label></td><td>'.wp_specialchars($time, true).'</td><td>'.wp_specialchars($stat, true).'</td></tr>'."\n\n";
	}
	$html .= '</tbody></table>';

	$x = new WP_Ajax_Response();
	$x->add( array(
		'what' => $what,
		'data' => $html
	));
	$x->send();

	break;
default :
	do_action( 'wp_ajax_' . $_POST['action'] );
	die('0');
	break;
endswitch;
?>
