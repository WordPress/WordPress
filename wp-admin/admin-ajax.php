<?php
require_once('../wp-config.php');
require_once('includes/admin.php');

define('DOING_AJAX', true);

if ( !is_user_logged_in() )
	die('-1');

function get_out_now() { exit; }
add_action( 'shutdown', 'get_out_now', -1 );

$id = (int) $_POST['id'];
switch ( $action = $_POST['action'] ) :
case 'add-post' :
	check_ajax_referer( 'add-post' );
	add_filter( 'post_limits', $limit_filter = create_function( '$a', '$b = split(" ",$a); if ( !isset($b[2]) ) return $a; $start = intval(trim($b[1])) / 20 * 15; if ( !is_int($start) ) return $a; $start += intval(trim($b[2])) - 1; return "LIMIT $start, 1";' ) );
	wp_edit_posts_query( '_POST' );
	$posts_columns = wp_manage_posts_columns();
	ob_start();
		include( 'edit-post-rows.php' );
		$data = ob_get_contents();
	ob_end_clean();
	if ( !preg_match('|<tbody.+?>(.+)</tbody>|s', $data, $matches) )
		my_dump($data);
	$data = trim($matches[1]);
	$x = new WP_Ajax_Response( array( 'what' => 'post', 'id' => $id, 'data' => $data ) );
	$x->send();
	break;
case 'delete-comment' :
	check_ajax_referer( "delete-comment_$id" );
	if ( !$comment = get_comment( $id ) )
		die('0');
	if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) )
		die('-1');

	if ( isset($_POST['spam']) && 1 == $_POST['spam'] )
		$r = wp_set_comment_status( $comment->comment_ID, 'spam' );
	else
		$r = wp_delete_comment( $comment->comment_ID );

	die( $r ? '1' : '0' );
	break;
case 'delete-cat' :
	check_ajax_referer( "delete-category_$id" );
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');

	if ( wp_delete_category( $id ) )
		die('1');
	else	die('0');
	break;
case 'delete-link' :
	check_ajax_referer( "delete-bookmark_$id" );
	if ( !current_user_can( 'manage_links' ) )
		die('-1');

	if ( wp_delete_link( $id ) )
		die('1');
	else	die('0');
	break;
case 'delete-meta' :
	check_ajax_referer( 'change_meta' );
	if ( !$meta = get_post_meta_by_id( $id ) )
		die('0');
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

	if ( wp_delete_post( $id ) )
		die('1');
	else
		die('0');
	break;
case 'delete-page' :
	check_ajax_referer( "{$action}_$id" );
	if ( !current_user_can( 'delete_page', $id ) )
		die('-1');

	if ( wp_delete_post( $id ) )
		die('1');
	else	die('0');
	break;
case 'dim-comment' :
	if ( !$comment = get_comment( $id ) )
		die('0');
	if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) )
		die('-1');
	if ( !current_user_can( 'moderate_comments' ) )
		die('-1');

	if ( 'unapproved' == wp_get_comment_status($comment->comment_ID) ) {
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
	$x = new WP_Ajax_Response();
	foreach ( $names as $cat_name ) {
		$cat_name = trim($cat_name);
		if ( !$category_nicename = sanitize_title($cat_name) )
			die('0');
		if ( !$cat_id = category_exists( $cat_name ) )
			$cat_id = wp_create_category( $cat_name );
		$cat_name = wp_specialchars(stripslashes($cat_name));
		$x->add( array(
			'what' => 'category',
			'id' => $cat_id,
			'data' => "<li id='category-$cat_id'><label for='in-category-$cat_id' class='selectit'><input value='$cat_id' type='checkbox' checked='checked' name='post_category[]' id='in-category-$cat_id'/> $cat_name</label></li>",
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
		if ( !$slug = sanitize_title($cat_name) )
			die('0');
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
	if ( !$cat = wp_insert_category( $_POST ) )
		die('0');
	if ( !$cat = get_category( $cat ) )
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
		'data' => _cat_row( $cat, $level, $cat_full_name ),
		'supplemental' => array('name' => $cat_full_name, 'show-link' => sprintf(__( 'Category <a href="#%s">%s</a> added' ), "cat-$cat->term_id", $cat_full_name))
	) );
	$x->send();
	break;
case 'add-comment' :
	check_ajax_referer( $action );
	if ( !current_user_can( 'edit_post', $id ) )
		die('-1');
	$search = isset($_POST['s']) ? $_POST['s'] : false;
	$start = isset($_POST['page']) ? intval($_POST['page']) * 25 - 1: 24;

	list($comments, $total) = _wp_get_comment_list( $search, $start, 1 );

	if ( !$comments )
		die('1');
	$x = new WP_Ajax_Response();
	foreach ( (array) $comments as $comment ) {
		get_comment( $comment );
		ob_start();
			_wp_comment_list_item( $comment->comment_ID );
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
case 'add-meta' :
	check_ajax_referer( 'change_meta' );
	$c = 0;
	$pid = (int) $_POST['post_id'];
	if ( isset($_POST['addmeta']) ) {
		if ( !current_user_can( 'edit_post', $pid ) )
			die('-1');
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
	if ( !current_user_can('edit_users') )
		die('-1');
	require_once(ABSPATH . WPINC . '/registration.php');
	if ( !$user_id = add_user() )
		die('0');
	elseif ( is_wp_error( $user_id ) ) {
		foreach( $user_id->get_error_messages() as $message )
			echo "<p>$message<p>";
		exit;
	}
	$user_object = new WP_User( $user_id );

	$x = new WP_Ajax_Response( array(
		'what' => 'user',
		'id' => $user_id,
		'data' => user_row( $user_object ),
		'supplemental' => array(
			'show-link' => sprintf(__( 'User <a href="#%s">%s</a> added' ), "user-$user_id", $user_object->user_login),
			'role' => $user_object->roles[0]
		)
	) );
	$x->send();
	break;
case 'autosave' : // The name of this action is hardcoded in edit_post()
	check_ajax_referer( $action );
	$_POST['post_content'] = $_POST['content'];
	$_POST['post_excerpt'] = $_POST['excerpt'];
	$_POST['post_status'] = 'draft';
	$_POST['post_category'] = explode(",", $_POST['catslist']);
	if($_POST['post_type'] == 'page' || empty($_POST['post_category']))
		unset($_POST['post_category']);

	if($_POST['post_ID'] < 0) {
		$_POST['temp_ID'] = $_POST['post_ID'];
		$id = wp_write_post();
		if( is_wp_error($id) )
			die($id->get_error_message());
		else
			die("$id");
	} else {
		$post_ID = (int) $_POST['post_ID'];
		$_POST['ID'] = $post_ID;
		$post = get_post($post_ID);
		if ( 'page' == $post->post_type ) {
			if ( !current_user_can('edit_page', $post_ID) )
				die(__('You are not allowed to edit this page.'));
		} else {
			if ( !current_user_can('edit_post', $post_ID) )
				die(__('You are not allowed to edit this post.'));
		}
		wp_update_post($_POST);
	}
	die('0');
break;
case 'autosave-generate-nonces' :
	check_ajax_referer( $action );
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
default :
	do_action( 'wp_ajax_' . $_POST['action'] );
	die('0');
	break;
endswitch;
?>
