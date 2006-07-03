<?php
require_once('admin.php');

$parent_file = 'edit.php';
$submenu_file = 'edit.php';

wp_reset_vars(array('action', 'safe_mode', 'withcomments', 'posts', 'content', 'edited_post_title', 'comment_error', 'profile', 'trackback_url', 'excerpt', 'showcomments', 'commentstart', 'commentend', 'commentorder'));

if ( isset( $_POST['deletepost'] ) )
	$action = 'delete';

switch($action) {
case 'postajaxpost':
case 'post':
	$parent_file = 'post-new.php';
	$submenu_file = 'post-new.php';
	check_admin_referer('add-post');
	
	$post_ID = 'post' == $action ? write_post() : edit_post();

	// Redirect.
	if (!empty($_POST['mode'])) {
	switch($_POST['mode']) {
		case 'bookmarklet':
			$location = $_POST['referredby'];
			break;
		case 'sidebar':
			$location = 'sidebar.php?a=b';
			break;
		default:
			$location = 'post-new.php';
			break;
		}
	} else {
		$location = 'post-new.php?posted=true';
	}

	if ( isset($_POST['save']) )
		$location = "post.php?action=edit&post=$post_ID";

	wp_redirect($location);
	exit();
	break;

case 'edit':
	$title = __('Edit');
	$editing = true;
	require_once('admin-header.php');

	$post_ID = $p = (int) $_GET['post'];

	$post = get_post($post_ID);
	if ( !current_user_can('edit_post', $post_ID) )
		die ( __('You are not allowed to edit this post.') );

	$post = get_post_to_edit($post_ID);

	include('edit-form-advanced.php');

	?>
	<div id='preview' class='wrap'>
	<h2 id="preview-post"><?php _e('Post Preview (updated when post is saved)'); ?> <small class="quickjump"><a href="#write-post"><?php _e('edit &uarr;'); ?></a></small></h2>
		<iframe src="<?php echo add_query_arg('preview', 'true', get_permalink($post->ID)); ?>" width="100%" height="600" ></iframe>
	</div>
	<?php
	break;

case 'editattachment':
	$post_id = (int) $_POST['post_ID'];

	check_admin_referer('update-attachment_' . $post_id);

	// Don't let these be changed
	unset($_POST['guid']);
	$_POST['post_type'] = 'attachment';

	// Update the thumbnail filename
	$oldmeta = $newmeta = get_post_meta($post_id, '_wp_attachment_metadata', true);
	$newmeta['thumb'] = $_POST['thumb'];

	if ( '' !== $oldmeta )
		update_post_meta($post_id, '_wp_attachment_metadata', $newmeta, $oldmeta);
	else
		add_post_meta($post_id, '_wp_attachment_metadata', $newmeta);

case 'editpost':
	$post_ID = (int) $_POST['post_ID'];
	check_admin_referer('update-post_' . $post_ID);
	
	$post_ID = edit_post();

	if ($_POST['save']) {
		$location = wp_get_referer();
	} elseif ($_POST['updatemeta']) {
		$location = wp_get_referer() . '&message=2#postcustom';
	} elseif ($_POST['deletemeta']) {
		$location = wp_get_referer() . '&message=3#postcustom';
	} elseif (!empty($_POST['referredby']) && $_POST['referredby'] != wp_get_referer()) {
		$location = $_POST['referredby'];
		if ( $_POST['referredby'] == 'redo' )
			$location = get_permalink( $post_ID );
	} elseif ($action == 'editattachment') {
		$location = 'attachments.php';
	} else {
		$location = 'post-new.php';
	}

	wp_redirect($location); // Send user on their way while we keep working

	exit();
	break;

case 'delete':
	$post_id = (isset($_GET['post']))  ? intval($_GET['post']) : intval($_POST['post_ID']);
	check_admin_referer('delete-post_' . $post_id);

	$post = & get_post($post_id);

	if ( !current_user_can('delete_post', $post_id) )
		die( __('You are not allowed to delete this post.') );

	if ( $post->post_type == 'attachment' ) {
		if ( ! wp_delete_attachment($post_id) )
			die( __('Error in deleting...') );
	} else {
		if ( !wp_delete_post($post_id) ) 
			die( __('Error in deleting...') );
	}

	$sendback = wp_get_referer();
	if (strstr($sendback, 'post.php')) $sendback = get_settings('siteurl') .'/wp-admin/post-new.php';
	elseif (strstr($sendback, 'attachments.php')) $sendback = get_settings('siteurl') .'/wp-admin/attachments.php';
	$sendback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $sendback);
	wp_redirect($sendback);
	exit();
	break;

default:
	wp_redirect('edit.php');
	exit();
	break;
} // end switch
include('admin-footer.php');
?>
