<?php
require_once('../wp-config.php');
require_once('admin-functions.php');
require_once('admin-db.php');

if ( !is_user_logged_in() )
	die('-1');

function grab_results() {
	global $ajax_results;
	$ajax_results = func_get_arg(0);
}

function get_out_now() { exit; }
add_action('shutdown', 'get_out_now', -1);

//	check_admin_referer();

switch ( $_POST['action'] ) :
case 'delete-link' :
	$id = (int) $_POST['id'];
	if ( !current_user_can('manage_links') )
		die ('-1');

	if ( $wpdb->query("DELETE FROM $wpdb->links WHERE link_id = '$id'") )
		die('1');
	else	die('0');
	break;
case 'delete-post' :
case 'delete-page' :
	$id = (int) $_POST['id'];
	if ( !current_user_can('edit_post', $id) )	{
		die('-1');
	}

	if ( wp_delete_post($id) ) {
		die('1');
	} else	die('0');
	break;
case 'delete-cat' :
	if ( !current_user_can('manage_categories') )
		die ('-1');

	$id = (int) $_POST['id'];
	$cat_name = get_catname($cat_ID);

	if ( wp_delete_category($id) )
		die('1');
	else	die('0');
	break;
case 'delete-comment' :
	$id = (int) $_POST['id'];

	if ( !$comment = get_comment($id) )
		die('0');
	if ( !current_user_can('edit_post', $comment->comment_post_ID) )
		die('-1');

	if ( wp_delete_comment($comment->comment_ID) ) {
		die('1');
	} else {
		die('0');
	}
	break;
case 'delete-comment-as-spam' :
	$id = (int) $_POST['id'];

	if ( !$comment = get_comment($id) )
		die('0');
	if ( !current_user_can('edit_post', $comment->comment_post_ID) )
		die('-1');

	if ( wp_set_comment_status($comment->comment_ID, 'spam') ) {
		die('1');
	} else {
		die('0');
	}
	break;
case 'delete-link-category' :
	$id = (int) $_POST['id'];
	if ( 1 == $id )
		die('0');
	if ( !current_user_can('manage_links') )
		die('-1');

	if ( $wpdb->query("DELETE FROM $wpdb->linkcategories WHERE cat_id='$id'") ) {
		$wpdb->query("UPDATE $wpdb->links SET link_category=1 WHERE link_category='$id'");
		die('1');
	} else {
		die('0');
	}
	break;
endswitch;
?>
