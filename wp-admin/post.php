<?php
/**
 * Edit post administration panel.
 *
 * Manage Post actions: post, edit, delete, etc.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

$parent_file = 'edit.php';
$submenu_file = 'edit.php';

wp_reset_vars(array('action', 'safe_mode', 'withcomments', 'posts', 'content', 'edited_post_title', 'comment_error', 'profile', 'trackback_url', 'excerpt', 'showcomments', 'commentstart', 'commentend', 'commentorder'));

/**
 * Redirect to previous page.
 *
 * @param int $post_ID Optional. Post ID.
 */
function redirect_post($post_ID = '') {
	global $action;

	$referredby = '';
	if ( !empty($_POST['referredby']) ) {
		$referredby = preg_replace('|https?://[^/]+|i', '', $_POST['referredby']);
		$referredby = remove_query_arg('_wp_original_http_referer', $referredby);
	}
	$referer = preg_replace('|https?://[^/]+|i', '', wp_get_referer());

	if ( !empty($_POST['mode']) && 'bookmarklet' == $_POST['mode'] ) {
		$location = $_POST['referredby'];
	} elseif ( !empty($_POST['mode']) && 'sidebar' == $_POST['mode'] ) {
		if ( isset($_POST['saveasdraft']) )
			$location = 'sidebar.php?a=c';
		elseif ( isset($_POST['publish']) )
			$location = 'sidebar.php?a=b';
	} elseif ( ( isset($_POST['save']) || isset($_POST['publish']) ) ) {
		if ( isset( $_POST['publish'] ) ) {
			switch ( get_post_status( $post_ID ) ) {
				case 'pending':
					$location = add_query_arg( 'message', 8, get_edit_post_link( $post_ID, 'url' ) );
					break;
				case 'future':
					$location = add_query_arg( 'message', 9, get_edit_post_link( $post_ID, 'url' ) );
					break;
				default:
					$location = add_query_arg( 'message', 6, get_edit_post_link( $post_ID, 'url' ) );
			}
		} else {
			$location = add_query_arg( 'message', 1, get_edit_post_link( $post_ID, 'url' ) );
		}
	} elseif (isset($_POST['addmeta']) && $_POST['addmeta']) {
		$location = add_query_arg( 'message', 2, wp_get_referer() );
		$location = explode('#', $location);
		$location = $location[0] . '#postcustom';
	} elseif (isset($_POST['deletemeta']) && $_POST['deletemeta']) {
		$location = add_query_arg( 'message', 3, wp_get_referer() );
		$location = explode('#', $location);
		$location = $location[0] . '#postcustom';
	} elseif ( 'post-quickpress-save-cont' == $_POST['action'] ) {
		$location = "post.php?action=edit&post=$post_ID&message=7";
	} else {
		$location = add_query_arg( 'message', 4, get_edit_post_link( $post_ID, 'url' ) );
	}

	wp_redirect( $location );
}

if ( isset( $_POST['deletepost'] ) )
	$action = 'delete';
elseif ( isset($_POST['wp-preview']) && 'dopreview' == $_POST['wp-preview'] )
	$action = 'preview';

switch($action) {
case 'postajaxpost':
case 'post':
case 'post-quickpress-publish':
case 'post-quickpress-save':
	check_admin_referer('add-post');

	if ( 'post-quickpress-publish' == $action )
		$_POST['publish'] = 'publish'; // tell write_post() to publish

	if ( 'post-quickpress-publish' == $action || 'post-quickpress-save' == $action ) {
		$_POST['comment_status'] = get_option('default_comment_status');
		$_POST['ping_status'] = get_option('default_ping_status');
	}

	if ( !empty( $_POST['quickpress_post_ID'] ) ) {
		$_POST['post_ID'] = (int) $_POST['quickpress_post_ID'];
		$post_ID = edit_post();
	} else {
		$post_ID = 'postajaxpost' == $action ? edit_post() : write_post();
	}

	if ( 0 === strpos( $action, 'post-quickpress' ) ) {
		$_POST['post_ID'] = $post_ID;
		// output the quickpress dashboard widget
		require_once(ABSPATH . 'wp-admin/includes/dashboard.php');
		wp_dashboard_quick_press();
		exit;
	}

	redirect_post($post_ID);
	exit();
	break;

case 'edit':
	$editing = true;

	if ( empty( $_GET['post'] ) ) {
		wp_redirect("post.php");
		exit();
	}
	$post_ID = $p = (int) $_GET['post'];
	$post = get_post($post_ID);

	if ( empty($post->ID) )
		wp_die( __('You attempted to edit a post that doesn&#8217;t exist. Perhaps it was deleted?') );

	if ( !current_user_can('edit_post', $post_ID) )
		wp_die( __('You are not allowed to edit this post.') );

	if ( 'trash' == $post->post_status )
		wp_die( __('You can&#8217;t edit this post because it is in the Trash. Please restore it and try again.') );

	if ( 'post' != $post->post_type ) {
		wp_redirect( get_edit_post_link( $post->ID, 'url' ) );
		exit();
	}

	wp_enqueue_script('post');
	if ( user_can_richedit() )
		wp_enqueue_script('editor');
	add_thickbox();
	wp_enqueue_script('media-upload');
	wp_enqueue_script('word-count');
	wp_enqueue_script( 'admin-comments' );
	enqueue_comment_hotkeys_js();

	if ( $last = wp_check_post_lock( $post->ID ) ) {
		$last_user = get_userdata( $last );
		$last_user_name = $last_user ? $last_user->display_name : __('Somebody');
		$message = sprintf( __( 'Warning: %s is currently editing this post' ), esc_html( $last_user_name ) );
		$message = str_replace( "'", "\'", "<div class='error'><p>$message</p></div>" );
		add_action('admin_notices', create_function( '', "echo '$message';" ) );
	} else {
		wp_set_post_lock( $post->ID );
		wp_enqueue_script('autosave');
	}

	$title = __('Edit Post');
	$post = get_post_to_edit($post_ID);

	include('edit-form-advanced.php');

	break;

case 'editattachment':
	$post_id = (int) $_POST['post_ID'];

	check_admin_referer('update-attachment_' . $post_id);

	// Don't let these be changed
	unset($_POST['guid']);
	$_POST['post_type'] = 'attachment';

	// Update the thumbnail filename
	$newmeta = wp_get_attachment_metadata( $post_id, true );
	$newmeta['thumb'] = $_POST['thumb'];

	wp_update_attachment_metadata( $post_id, $newmeta );

case 'editpost':
	$post_ID = (int) $_POST['post_ID'];
	check_admin_referer('update-post_' . $post_ID);

	$post_ID = edit_post();

	redirect_post($post_ID); // Send user on their way while we keep working

	exit();
	break;

case 'trash':
	$post_id = isset($_GET['post']) ? intval($_GET['post']) : intval($_POST['post_ID']);
	check_admin_referer('trash-post_' . $post_id);

	$post = & get_post($post_id);

	if ( !current_user_can('delete_post', $post_id) )
		wp_die( __('You are not allowed to move this post to the trash.') );

	if ( ! wp_trash_post($post_id) )
		wp_die( __('Error in moving to trash...') );

	$sendback = wp_get_referer();
	if ( strpos($sendback, 'post.php') !== false )
		$sendback = admin_url('edit.php?trashed=1');
	else
		$sendback = add_query_arg('trashed', 1, $sendback);

	wp_redirect($sendback);
	exit();
	break;

case 'untrash':
	$post_id = isset($_GET['post']) ? intval($_GET['post']) : intval($_POST['post_ID']);
	check_admin_referer('untrash-post_' . $post_id);

	$post = & get_post($post_id);

	if ( !current_user_can('delete_post', $post_id) )
		wp_die( __('You are not allowed to move this post out of the trash.') );

	if ( ! wp_untrash_post($post_id) )
		wp_die( __('Error in restoring from trash...') );

	$sendback = wp_get_referer();
	if ( strpos($sendback, 'post.php') !== false )
		$sendback = admin_url('edit.php?untrashed=1');
	else
		$sendback = add_query_arg('untrashed', 1, $sendback);

	wp_redirect($sendback);
	exit();
	break;

case 'delete':
	$post_id = (isset($_GET['post']))  ? intval($_GET['post']) : intval($_POST['post_ID']);
	check_admin_referer('delete-post_' . $post_id);

	$post = & get_post($post_id);

	if ( !current_user_can('delete_post', $post_id) )
		wp_die( __('You are not allowed to delete this post.') );

	if ( $post->post_type == 'attachment' ) {
		if ( ! wp_delete_attachment($post_id) )
			wp_die( __('Error in deleting...') );
	} else {
		if ( !wp_delete_post($post_id) )
			wp_die( __('Error in deleting...') );
	}

	$sendback = wp_get_referer();
	if ( strpos($sendback, 'post.php') !== false )
		$sendback = admin_url('edit.php?deleted=1');
	else
		$sendback = add_query_arg('deleted', 1, $sendback);

	wp_redirect($sendback);
	exit();
	break;

case 'preview':
	check_admin_referer( 'autosave', 'autosavenonce' );

	$url = post_preview();

	wp_redirect($url);
	exit();
	break;

default:
	wp_redirect('edit.php');
	exit();
	break;
} // end switch
include('admin-footer.php');
?>
