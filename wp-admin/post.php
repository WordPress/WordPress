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

if (isset($_POST['deletepost'])) {
$action = "delete";
}

	// Fix submenu highlighting for pages.
if (false !== strpos($_SERVER['HTTP_REFERER'], 'edit-pages.php')) $submenu_file = 'page-new.php';

$editing = true;

switch($action) {
case 'post':

	write_post();

	// Redirect.
	if (!empty($_POST['mode'])) {
	switch($_POST['mode']) {
		case 'bookmarklet':
			$location = 'bookmarklet.php?a=b';
			break;
		case 'sidebar':
			$location = 'sidebar.php?a=b';
			break;
		default:
			$location = 'post.php';
			break;
		}
	} else {
		$location = 'post.php?posted=true';
	}

	if ( 'static' == $_POST['post_status'] )
		$location = "page-new.php?saved=true";

	if ( '' != $_POST['advanced'] || isset($_POST['save']) )
		$location = "post.php?action=edit&post=$post_ID";

	header("Location: $location");
	exit();
	break;

case 'edit':
	$title = __('Edit');

	require_once('admin-header.php');

	$post_ID = $p = (int) $_GET['post'];

	if ( !user_can_edit_post($user_ID, $post_ID) )
		die ( __('You are not allowed to edit this post.') );

	if ( !user_can_edit_post($user_ID, $post_ID) )
		die ( __('You are not allowed to view other users\' private posts.') );		

	$post = get_post_to_edit($post_ID);
	
	if ($post->post_status == 'static')
		include('edit-page-form.php');
	else
		include('edit-form-advanced.php');

	?>
	<div id='preview' class='wrap'>
	<h2><?php _e('Post Preview (updated when post is saved)'); ?></h2>
	<h3 class="storytitle" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__("Permanent Link: %s"), get_the_title()); ?>"><?php the_title(); ?></a></h3>
	<div class="meta"><?php _e("Filed under:"); ?> <?php the_category(','); ?> &#8212; <?php the_author() ?> @ <?php the_time() ?></div>

	<div class="storycontent">
	<?php 
	echo apply_filters('the_content', $post->post_content);
	?>
	</div>
	</div>
	<?php
	break;

case 'editpost':
	edit_post();

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
	} else {
		$location = 'post.php';
	}
	header ('Location: ' . $location); // Send user on their way while we keep working

	exit();
	break;

case 'delete':
	check_admin_referer();

	$post_id = (isset($_GET['post']))  ? intval($_GET['post']) : intval($_POST['post_ID']);
	
	if (!user_can_delete_post($user_ID, $post_id)) {
		die( __('You are not allowed to delete this post.') );
	}

	if (! wp_delete_post($post_id))
		die( __('Error in deleting...') );

	$sendback = $_SERVER['HTTP_REFERER'];
	if (strstr($sendback, 'post.php')) $sendback = get_settings('siteurl') .'/wp-admin/post.php';
	$sendback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $sendback);
	header ('Location: ' . $sendback);
	break;

case 'editcomment':
	$title = __('Edit Comment');
	$parent_file = 'edit.php';
	require_once ('admin-header.php');

	get_currentuserinfo();

	$comment = $_GET['comment'];
	$commentdata = get_commentdata($comment, 1, true) or die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'javascript:history.go(-1)'));

	if (!user_can_edit_post_comments($user_ID, $commentdata['comment_post_ID'])) {
		die( __('You are not allowed to edit comments on this post.') );
	}

	$content = $commentdata['comment_content'];
	$content = format_to_edit($content);
	$content = apply_filters('comment_edit_pre', $content);
	
	$comment_status = $commentdata['comment_approved'];

	include('edit-form-comment.php');

	break;

case 'confirmdeletecomment':

	require_once('./admin-header.php');

	$comment = $_GET['comment'];
	$p = (int) $_GET['p'];
	$commentdata = get_commentdata($comment, 1, true) or die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if (!user_can_delete_post_comments($user_ID, $commentdata['comment_post_ID'])) {
		die( __('You are not allowed to delete comments on this post.') );
	}

	echo "<div class=\"wrap\">\n";
	echo "<p>" . __('<strong>Caution:</strong> You are about to delete the following comment:') . "</p>\n";
	echo "<table border=\"0\">\n";
	echo "<tr><td>" . __('Author:') . "</td><td>" . $commentdata["comment_author"] . "</td></tr>\n";
	echo "<tr><td>" . __('E-mail:') . "</td><td>" . $commentdata["comment_author_email"] . "</td></tr>\n";
	echo "<tr><td>". __('URL:') . "</td><td>" . $commentdata["comment_author_url"] . "</td></tr>\n";
	echo "<tr><td>". __('Comment:') . "</td><td>" . stripslashes($commentdata["comment_content"]) . "</td></tr>\n";
	echo "</table>\n";
	echo "<p>" . __('Are you sure you want to do that?') . "</p>\n";

	echo "<form action='".get_settings('siteurl')."/wp-admin/post.php' method='get'>\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"deletecomment\" />\n";
	echo "<input type=\"hidden\" name=\"p\" value=\"$p\" />\n";
	echo "<input type=\"hidden\" name=\"comment\" value=\"$comment\" />\n";
	echo "<input type=\"hidden\" name=\"noredir\" value=\"1\" />\n";
	echo "<input type=\"submit\" value=\"" . __('Yes') . "\" />";
	echo "&nbsp;&nbsp;";
	echo "<input type=\"button\" value=\"" . __('No') . "\" onclick=\"self.location='". get_settings('siteurl') ."/wp-admin/edit.php?p=$p&amp;c=1#comments';\" />\n";
	echo "</form>\n";
	echo "</div>\n";

	break;

case 'deletecomment':

	check_admin_referer();

	$comment = $_GET['comment'];
	$p = $_GET['p'];
	if (isset($_GET['noredir'])) {
		$noredir = true;
	} else {
		$noredir = false;
	}

	$postdata = get_post($p) or die(sprintf(__('Oops, no post with this ID. <a href="%s">Go back</a>!'), 'edit.php'));
	$commentdata = get_commentdata($comment, 1, true) or die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'post.php'));

	if (!user_can_delete_post_comments($user_ID, $commentdata['comment_post_ID'])) {
		die( __('You are not allowed to edit comments on this post.') );
	}

	wp_set_comment_status($comment, "delete");
	do_action('delete_comment', $comment);

	if (($_SERVER['HTTP_REFERER'] != "") && (false == $noredir)) {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	} else {
		header('Location: '. get_settings('siteurl') .'/wp-admin/edit.php?p='.$p.'&c=1#comments');
	}

	break;

case 'unapprovecomment':

	require_once('./admin-header.php');

	check_admin_referer();

	$comment = $_GET['comment'];
	$p = $_GET['p'];
	if (isset($_GET['noredir'])) {
		$noredir = true;
	} else {
		$noredir = false;
	}

	$commentdata = get_commentdata($comment) or die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if (!user_can_edit_post_comments($user_ID, $commentdata['comment_post_ID'])) {
		die( __('You are not allowed to edit comments on this post, so you cannot disapprove this comment.') );
	}

	wp_set_comment_status($comment, "hold");

	if (($_SERVER['HTTP_REFERER'] != "") && (false == $noredir)) {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	} else {
		header('Location: '. get_settings('siteurl') .'/wp-admin/edit.php?p='.$p.'&c=1#comments');
	}

	break;

case 'mailapprovecomment':

	$comment = (int) $_GET['comment'];

	$commentdata = get_commentdata($comment, 1, true) or die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if (!user_can_edit_post_comments($user_ID, $commentdata['comment_post_ID'])) {
		die( __('You are not allowed to edit comments on this post, so you cannot approve this comment.') );
	}

	if ('1' != $commentdata['comment_approved']) {
		wp_set_comment_status($comment, 'approve');
		if (true == get_option('comments_notify'))
			wp_notify_postauthor($comment);
	}

	header('Location: ' . get_option('siteurl') . '/wp-admin/moderation.php?approved=1');

	break;

case 'approvecomment':

	$comment = $_GET['comment'];
	$p = $_GET['p'];
	if (isset($_GET['noredir'])) {
		$noredir = true;
	} else {
		$noredir = false;
	}
	$commentdata = get_commentdata($comment) or die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

	if (!user_can_edit_post_comments($user_ID, $commentdata['comment_post_ID'])) {
		die( __('You are not allowed to edit comments on this post, so you cannot approve this comment.') );
	}

	wp_set_comment_status($comment, "approve");
	if (get_settings("comments_notify") == true) {
		wp_notify_postauthor($comment);
	}


	if (($_SERVER['HTTP_REFERER'] != "") && (false == $noredir)) {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	} else {
		header('Location: '. get_settings('siteurl') .'/wp-admin/edit.php?p='.$p.'&c=1#comments');
	}

	break;

case 'editedcomment':

	$comment_ID = $_POST['comment_ID'];
	$comment_post_ID = $_POST['comment_post_ID'];
	$newcomment_author = $_POST['newcomment_author'];
	$newcomment_author_email = $_POST['newcomment_author_email'];
	$newcomment_author_url = $_POST['newcomment_author_url'];
	$comment_status = $_POST['comment_status'];

	if (!user_can_edit_post_comments($user_ID, $comment_post_ID)) {
		die( __('You are not allowed to edit comments on this post, so you cannot edit this comment.') );
	}

	if (user_can_edit_post_date($user_ID, $post_ID) && (!empty($_POST['edit_date']))) {
		$aa = $_POST['aa'];
		$mm = $_POST['mm'];
		$jj = $_POST['jj'];
		$hh = $_POST['hh'];
		$mn = $_POST['mn'];
		$ss = $_POST['ss'];
		$jj = ($jj > 31) ? 31 : $jj;
		$hh = ($hh > 23) ? $hh - 24 : $hh;
		$mn = ($mn > 59) ? $mn - 60 : $mn;
		$ss = ($ss > 59) ? $ss - 60 : $ss;
		$datemodif = ", comment_date = '$aa-$mm-$jj $hh:$mn:$ss'";
	} else {
		$datemodif = '';
	}
	$content = apply_filters('comment_save_pre', $_POST['content']);

	$result = $wpdb->query("
		UPDATE $wpdb->comments SET
			comment_content = '$content',
			comment_author = '$newcomment_author',
			comment_author_email = '$newcomment_author_email',
			comment_approved = '$comment_status',
			comment_author_url = '$newcomment_author_url'".$datemodif."
		WHERE comment_ID = $comment_ID"
		);

	$referredby = $_POST['referredby'];
	if (!empty($referredby)) {
		header('Location: ' . $referredby);
	} else {
		header ("Location: edit.php?p=$comment_post_ID&c=1#comments");
	}
	do_action('edit_comment', $comment_ID);
	break;

default:
	$title = __('Create New Post');
	require_once ('./admin-header.php');
?>
<?php if ( isset($_GET['posted']) ) : ?>
<div class="updated"><p><?php printf(__('Post saved. <a href="%s">View site &raquo;</a>'), get_bloginfo('home')); ?></p></div>
<?php endif; ?>
<?php
	if (user_can_create_draft($user_ID)) {
		$action = 'post';
		get_currentuserinfo();
		$drafts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'draft' AND post_author = $user_ID");
		if ($drafts) {
			?>
			<div class="wrap">
			<p><strong><?php _e('Your Drafts:') ?></strong>
			<?php
			$i = 0;
			foreach ($drafts as $draft) {
				if (0 != $i)
					echo ', ';
				$draft->post_title = stripslashes($draft->post_title);
				if ($draft->post_title == '')
					$draft->post_title = sprintf(__('Post # %s'), $draft->ID);
				echo "<a href='post.php?action=edit&amp;post=$draft->ID' title='" . __('Edit this draft') . "'>$draft->post_title</a>";
				++$i;
				}
			?>.</p>
			</div>
			<?php
		}

		$post = get_default_post_to_edit();

		include('edit-form-advanced.php');
?>
<div class="wrap">
<?php _e('<h3>WordPress bookmarklet</h3>
<p>You can drag the following link to your links bar or add it to your bookmarks and when you "Press it" it will open up a popup window with information and a link to the site you&#8217;re currently browsing so you can make a quick post about it. Try it out:</p>') ?>
<p>

<?php
$bookmarklet_height= (get_settings('use_trackback')) ? 480 : 440;

if ($is_NS4 || $is_gecko) {
?>
<a href="javascript:if(navigator.userAgent.indexOf('Safari') >= 0){Q=getSelection();}else{Q=document.selection?document.selection.createRange().text:document.getSelection();}void(window.open('<?php echo get_settings('siteurl') ?>/wp-admin/bookmarklet.php?text='+encodeURIComponent(Q)+'&amp;popupurl='+encodeURIComponent(location.href)+'&amp;popuptitle='+encodeURIComponent(document.title),'<?php _e('WordPress bookmarklet') ?>','scrollbars=yes,width=600,height=460,left=100,top=150,status=yes'));"><?php printf(__('Press It - %s'), wp_specialchars(get_settings('blogname'))); ?></a> 
<?php
} else if ($is_winIE) {
?>
<a href="javascript:Q='';if(top.frames.length==0)Q=document.selection.createRange().text;void(btw=window.open('<?php echo get_settings('siteurl') ?>/wp-admin/bookmarklet.php?text='+encodeURIComponent(Q)+'<?php echo $bookmarklet_tbpb ?>&amp;popupurl='+encodeURIComponent(location.href)+'&amp;popuptitle='+encodeURIComponent(document.title),'bookmarklet','scrollbars=yes,width=600,height=<?php echo $bookmarklet_height ?>,left=100,top=150,status=yes'));btw.focus();"><?php printf(__('Press it - %s'), get_settings('blogname')); ?></a>
<script type="text/javascript">
<!--
function oneclickbookmarklet(blah) {
window.open ("profile.php?action=IErightclick", "oneclickbookmarklet", "width=500, height=450, location=0, menubar=0, resizable=0, scrollbars=1, status=1, titlebar=0, toolbar=0, screenX=120, left=120, screenY=120, top=120");
}
// -->
</script>
<br />
<br />
<?php _e('One-click bookmarklet:') ?><br />
<a href="javascript:oneclickbookmarklet(0);"><?php _e('click here') ?></a> 
<?php
} else if ($is_opera) {
?>
<a href="javascript:void(window.open('<?php echo get_settings('siteurl'); ?>/wp-admin/bookmarklet.php?popupurl='+escape(location.href)+'&popuptitle='+escape(document.title)+'<?php echo $bookmarklet_tbpb ?>','bookmarklet','scrollbars=yes,width=600,height=<?php echo $bookmarklet_height ?>,left=100,top=150,status=yes'));"><?php printf(__('Press it - %s'), get_settings('blogname')); ?></a> 
<?php
} else if ($is_macIE) {
?>
<a href="javascript:Q='';if(top.frames.length==0);void(btw=window.open('<?php echo get_settings('siteurl'); ?>/wp-admin/bookmarklet.php?text='+escape(document.getSelection())+'&popupurl='+escape(location.href)+'&popuptitle='+escape(document.title)+'<?php echo $bookmarklet_tbpb ?>','bookmarklet','scrollbars=yes,width=600,height=<?php echo $bookmarklet_height ?>,left=100,top=150,status=yes'));btw.focus();"><?php printf(__('Press it - %s'), get_settings('blogname')); ?></a> 
<?php
}
?>
</p>
</div>
<?php
} else {
?>
<div class="wrap">
<p><?php printf(__('Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 1, in order to be authorized to post.<br />
You can also <a href="mailto:%s?subject=Promotion?">e-mail the admin</a> to ask for a promotion.<br />
When you&#8217;re promoted, just reload this page and you&#8217;ll be able to blog. :)'), get_settings('admin_email')); ?>
</p>
</div>
<?php
}

	break;
} // end switch
/* </Edit> */
include('admin-footer.php');
?>
