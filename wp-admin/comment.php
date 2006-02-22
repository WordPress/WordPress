<?php
require_once('admin.php');

$wpvarstoreset = array('action');

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

switch($action) {
case 'editcomment':
	$title = __('Edit Comment');
	$parent_file = 'edit.php';
	$submenu_file = 'edit-comments.php';
	$editing = true;
	require_once ('admin-header.php');

	$comment = (int) $_GET['comment'];

	if ( ! $comment = get_comment($comment) )
		die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'javascript:history.go(-1)'));

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )
		die( __('You are not allowed to edit comments on this post.') );

	$comment = get_comment_to_edit($comment);

	include('edit-form-comment.php');

	break;

case 'confirmdeletecomment':

	require_once('./admin-header.php');

	$comment = (int) $_GET['comment'];
	$p = (int) $_GET['p'];

	if ( ! $comment = get_comment($comment) )
		die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )
		die( __('You are not allowed to delete comments on this post.') );

	echo "<div class='wrap'>\n";
	if ( 'spam' == $_GET['delete_type'] )
		echo "<p>" . __('<strong>Caution:</strong> You are about to mark the following comment as spam:') . "</p>\n";
	else
		echo "<p>" . __('<strong>Caution:</strong> You are about to delete the following comment:') . "</p>\n";
	echo "<table border='0'>\n";
	echo "<tr><td>" . __('Author:') . "</td><td>$comment->comment_author</td></tr>\n";
	echo "<tr><td>" . __('E-mail:') . "</td><td>$comment->comment_author_email</td></tr>\n";
	echo "<tr><td>". __('URL:') . "</td><td>$comment->comment_author_url</td></tr>\n";
	echo "<tr><td>". __('Comment:') . "</td><td>$comment->comment_content</td></tr>\n";
	echo "</table>\n";
	echo "<p>" . __('Are you sure you want to do that?') . "</p>\n";

	echo "<form action='".get_settings('siteurl')."/wp-admin/comment.php' method='get'>\n";
	echo "<input type='hidden' name='action' value='deletecomment' />\n";
	if ( 'spam' == $_GET['delete_type'] )
		echo "<input type='hidden' name='delete_type' value='spam' />\n";
	echo "<input type='hidden' name='p' value='$p' />\n";
	echo "<input type='hidden' name='comment' value='{$comment->comment_ID}' />\n";
	echo "<input type='hidden' name='noredir' value='1' />\n";
	echo "<input type='submit' value='" . __('Yes') . "' />";
	echo "&nbsp;&nbsp;";
	echo "<input type='button' value='" . __('No') . "' onclick=\"self.location='". get_settings('siteurl') ."/wp-admin/edit.php?p=$p&amp;c=1#comments';\" />\n";
	echo "</form>\n";
	echo "</div>\n";

	break;

case 'deletecomment':

	check_admin_referer();

	$comment = (int) $_GET['comment'];
	$p = (int) $_GET['p'];
	if (isset($_GET['noredir'])) {
		$noredir = true;
	} else {
		$noredir = false;
	}

	$postdata = get_post($p) or die(sprintf(__('Oops, no post with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if ( ! $comment = get_comment($comment) )
			 die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit-comments.php'));

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )
		die( __('You are not allowed to edit comments on this post.') );

	if ( 'spam' == $_GET['delete_type'] )
		wp_set_comment_status($comment->comment_ID, 'spam');
	else
		wp_delete_comment($comment->comment_ID);

	if (($_SERVER['HTTP_REFERER'] != "") && (false == $noredir)) {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	} else {
		header('Location: '. get_settings('siteurl') .'/wp-admin/edit.php?p='.$p.'&c=1#comments');
	}
	exit();
	break;

case 'unapprovecomment':

	check_admin_referer();

	$comment = (int) $_GET['comment'];
	$p = (int) $_GET['p'];
	if (isset($_GET['noredir'])) {
		$noredir = true;
	} else {
		$noredir = false;
	}

	if ( ! $comment = get_comment($comment) )
		die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )
		die( __('You are not allowed to edit comments on this post, so you cannot disapprove this comment.') );

	wp_set_comment_status($comment->comment_ID, "hold");

	if (($_SERVER['HTTP_REFERER'] != "") && (false == $noredir)) {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	} else {
		header('Location: '. get_settings('siteurl') .'/wp-admin/edit.php?p='.$p.'&c=1#comments');
	}
	exit();
	break;

case 'mailapprovecomment':

	$comment = (int) $_GET['comment'];

	if ( ! $comment = get_comment($comment) )
			 die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )
		die( __('You are not allowed to edit comments on this post, so you cannot approve this comment.') );

	if ('1' != $comment->comment_approved) {
		wp_set_comment_status($comment->comment_ID, 'approve');
		if (true == get_option('comments_notify'))
			wp_notify_postauthor($comment->comment_ID);
	}

	header('Location: ' . get_option('siteurl') . '/wp-admin/moderation.php?approved=1');
	exit();
	break;

case 'approvecomment':

	$comment = (int) $_GET['comment'];
	$p = (int) $_GET['p'];
	if (isset($_GET['noredir'])) {
		$noredir = true;
	} else {
		$noredir = false;
	}

	if ( ! $comment = get_comment($comment) )
		die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )
		die( __('You are not allowed to edit comments on this post, so you cannot approve this comment.') );

	wp_set_comment_status($comment->comment_ID, "approve");
	if (get_settings("comments_notify") == true) {
		wp_notify_postauthor($comment->comment_ID);
	}


	if (($_SERVER['HTTP_REFERER'] != "") && (false == $noredir)) {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	} else {
		header('Location: '. get_settings('siteurl') .'/wp-admin/edit.php?p='.$p.'&c=1#comments');
	}
	exit();
	break;

case 'editedcomment':

	edit_comment();

	$referredby = $_POST['referredby'];
	if (!empty($referredby)) {
		header('Location: ' . $referredby);
	} else {
		header ("Location: edit.php?p=$comment_post_ID&c=1#comments");
	}

	break;
default:
	break;
} // end switch
include('admin-footer.php');
?>