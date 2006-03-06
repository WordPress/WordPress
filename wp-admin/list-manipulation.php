<?php
require_once('../wp-config.php');
require_once('admin-functions.php');
require_once('admin-db.php');

if ( !is_user_logged_in() )
	die('-1');

function get_out_now() { exit; }
add_action( 'shutdown', 'get_out_now', -1 );

//	check_admin_referer();

$id = (int) $_POST['id'];
switch ( $_POST['action'] ) :
case 'delete-link' :
	if ( !current_user_can( 'manage_links' ) )
		die('-1');

	if ( wp_delete_link( $id ) )
		die('1');
	else	die('0');
	break;
case 'delete-post' :
	if ( !current_user_can( 'delete_post', $id ) )
		die('-1');

	if ( wp_delete_post( $id ) )
		die('1');
	else	die('0');
	break;
case 'delete-page' :
	if ( !current_user_can( 'delete_page', $id ) )
		die('-1');

	if ( wp_delete_post( $id ) )
		die('1');
	else	die('0');
	break;
case 'delete-cat' :
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');

	if ( wp_delete_category( $id ) )
		die('1');
	else	die('0');
	break;
case 'delete-comment' :
	if ( !$comment = get_comment( $id ) )
		die('0');
	if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) )
		die('-1');

	if ( wp_delete_comment( $comment->comment_ID ) )
		die('1');
	else	die('0');
	break;
case 'delete-comment-as-spam' :
	if ( !$comment = get_comment( $id ) )
		die('0');
	if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) )
		die('-1');

	if ( wp_set_comment_status( $comment->comment_ID, 'spam' ) )
		die('1');
	else	die('0');
	break;
endswitch;
?>
