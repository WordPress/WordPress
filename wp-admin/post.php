<?php
require_once('admin.php');

$wpvarstoreset = array('action', 'safe_mode', 'withcomments', 'posts', 'content', 'edited_post_title', 'comment_error', 'profile', 'trackback_url', 'excerpt', 'showcomments', 'commentstart', 'commentend', 'commentorder' );

for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($_POST["$wpvar"])) {
			if (empty($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
			$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}
if (isset($_POST['deletepost']))
$action = "delete";

switch($action) {
case 'postajaxpost':
case 'post':
	check_admin_referer();
	
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

	header("Location: $location");
	exit();
	break;

case 'edit':
	$title = __('Edit');
	$parent_file = 'edit.php';
	$submenu_file = 'edit.php';
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
	check_admin_referer();

	$post_id = (int) $_POST['post_ID'];

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
	check_admin_referer();
	
	$post_ID = edit_post();

	if ($_POST['save']) {
		$location = $_SERVER['HTTP_REFERER'];
	} elseif ($_POST['updatemeta']) {
		$location = $_SERVER['HTTP_REFERER'] . '&message=2#postcustom';
	} elseif ($_POST['deletemeta']) {
		$location = $_SERVER['HTTP_REFERER'] . '&message=3#postcustom';
	} elseif (isset($_POST['referredby']) && $_POST['referredby'] != $_SERVER['HTTP_REFERER']) {
		$location = $_POST['referredby'];
		if ( $_POST['referredby'] == 'redo' )
			$location = get_permalink( $post_ID );
	} elseif ($action == 'editattachment') {
		$location = 'attachments.php';
	} else {
		$location = 'post-new.php';
	}
	header ('Location: ' . $location); // Send user on their way while we keep working

	exit();
	break;

case 'delete':
	check_admin_referer();

	$post_id = (isset($_GET['post']))  ? intval($_GET['post']) : intval($_POST['post_ID']);

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

	$sendback = $_SERVER['HTTP_REFERER'];
	if (strstr($sendback, 'post.php')) $sendback = get_settings('siteurl') .'/wp-admin/post-new.php';
	elseif (strstr($sendback, 'attachments.php')) $sendback = get_settings('siteurl') .'/wp-admin/attachments.php';
	$sendback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $sendback);
	header ('Location: ' . $sendback);
	exit();
	break;

default:
	break;
} // end switch
include('admin-footer.php');
?>
