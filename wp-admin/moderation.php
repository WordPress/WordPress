<?php
require_once('admin.php');

$title = __('Moderate comments');
$parent_file = 'edit.php';
wp_enqueue_script( 'listman' );

$wpvarstoreset = array('action', 'item_ignored', 'item_deleted', 'item_approved', 'item_spam', 'feelinglucky');
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

$comment = array();
if (isset($_POST["comment"])) {
	foreach ($_POST["comment"] as $k => $v) {
		$comment[intval($k)] = $v;
	}
}

switch($action) {

case 'update':

	check_admin_referer('moderate-comments');

	if ( ! current_user_can('moderate_comments') )
	die('<p>'.__('Your level is not high enough to moderate comments.').'</p>');

	$item_ignored = 0;
	$item_deleted = 0;
	$item_approved = 0;
	$item_spam = 0;

	foreach($comment as $key => $value) {
	if ($feelinglucky && 'later' == $value)
		$value = 'delete';
	    switch($value) {
			case 'later':
				// do nothing with that comment
				// wp_set_comment_status($key, "hold");
				++$item_ignored;
				break;
			case 'delete':
				wp_set_comment_status($key, 'delete');
				++$item_deleted;
				break;
 			case 'spam':
 				wp_set_comment_status($key, 'spam');
 				++$item_spam;
 				break;
			case 'approve':
				wp_set_comment_status($key, 'approve');
				if ( get_settings('comments_notify') == true ) {
					wp_notify_postauthor($key);
				}
				++$item_approved;
				break;
	    }
	}

	$file = basename(__FILE__);
	header("Location: $file?ignored=$item_ignored&deleted=$item_deleted&approved=$item_approved&spam=$item_spam");
	exit();

break;

default:

require_once('admin-header.php');

if ( isset($_GET['deleted']) || isset($_GET['approved']) || isset($_GET['ignored']) ) {
	echo "<div id='moderated' class='updated fade'>\n<p>";
	$approved = (int) $_GET['approved'];
	$deleted  = (int) $_GET['deleted'];
	$ignored  = (int) $_GET['ignored'];
	$spam     = (int) $_GET['spam'];
	if ($approved) {
		if ('1' == $approved) {
			echo __("1 comment approved") . " <br/>\n";
		} else {
		 echo sprintf(__("%s comments approved <br />"), $approved) . "\n";
		}
	}
	if ($deleted) {
		if ('1' == $deleted) {
			echo __("1 comment deleted") . " <br/>\n";
		} else {
			echo sprintf(__("%s comments deleted"), $deleted) . " <br/>\n";
		}
	}
 	if ($spam) {
 		if ('1' == $spam) {
			echo __("1 comment marked as spam") . " <br/>\n";
 		} else {
 			echo sprintf(__("%s comments marked as spam"), $spam) . " <br/>\n";
 		}
 	}
	if ($ignored) {
		if ('1' == $ignored) {
			echo __("1 comment unchanged") . " <br/>\n";
		} else {
			echo sprintf(__("%s comments unchanged"), $ignored) . " <br/>\n";
		}
	}
	echo "</p></div>\n";
}

?>

<div class="wrap">

<?php
if ( current_user_can('moderate_comments') )
	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_approved = '0'");
else
	$comments = '';

if ($comments) {
    // list all comments that are waiting for approval
    $file = basename(__FILE__);
?>
    <h2><?php _e('Moderation Queue') ?></h2>
    <form name="approval" action="moderation.php" method="post">
    <?php wp_nonce_field('moderate-comments') ?>
    <input type="hidden" name="action" value="update" />
    <ol id="the-list" class="commentlist">
<?php
$i = 0;
    foreach($comments as $comment) {
	++$i;
	$comment_date = mysql2date(get_settings("date_format") . " @ " . get_settings("time_format"), $comment->comment_date);
	$post_title = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID='$comment->comment_post_ID'");
	if ($i % 2) $class = 'class="alternate"';
	else $class = '';
	echo "\n\t<li id='comment-$comment->comment_ID' $class>"; 
	?>
	<p><strong><?php comment_author() ?></strong> <?php if ($comment->comment_author_email) { ?>| <?php comment_author_email_link() ?> <?php } if ($comment->comment_author_url && 'http://' != $comment->comment_author_url) { ?> | <?php comment_author_url_link() ?> <?php } ?>| <?php _e('IP:') ?> <a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>"><?php comment_author_IP() ?></a></p>
<?php comment_text() ?>
<p><?php comment_date('M j, g:i A'); ?> &#8212; [ <?php
echo '<a href="comment.php?action=editcomment&amp;comment='.$comment->comment_ID.'">' . __('Edit') . '</a> | ';
echo " <a href=\"post.php?action=deletecomment&amp;p=".$comment->comment_post_ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return deleteSomething( 'comment', $comment->comment_ID, '" . sprintf(__("You are about to delete this comment by &quot;%s&quot;.\\n&quot;Cancel&quot; to stop, &quot;OK&quot; to delete."), js_escape($comment->comment_author)) . "' );\">" . __('Delete ') . "</a> | "; ?>
<?php
$post = get_post($comment->comment_post_ID);
$post_title = wp_specialchars( $post->post_title, 'double' );
$post_title = ('' == $post_title) ? "# $comment->comment_post_ID" : $post_title;
?>
<a href="<?php echo get_permalink($comment->comment_post_ID); ?>" title="<?php echo $post_title; ?>"><?php _e('View Post') ?></a> ] &#8212;
 <?php _e('Bulk action:') ?>
	<input type="radio" name="comment[<?php echo $comment->comment_ID; ?>]" id="comment[<?php echo $comment->comment_ID; ?>]-approve" value="approve" /> <label for="comment[<?php echo $comment->comment_ID; ?>]-approve"><?php _e('Approve') ?></label> &nbsp;
	<input type="radio" name="comment[<?php echo $comment->comment_ID; ?>]" id="comment[<?php echo $comment->comment_ID; ?>]-spam" value="spam" /> <label for="comment[<?php echo $comment->comment_ID; ?>]-spam"><?php _e('Spam') ?></label> &nbsp;
	<input type="radio" name="comment[<?php echo $comment->comment_ID; ?>]" id="comment[<?php echo $comment->comment_ID; ?>]-delete" value="delete" /> <label for="comment[<?php echo $comment->comment_ID; ?>]-delete"><?php _e('Delete') ?></label> &nbsp;
	<input type="radio" name="comment[<?php echo $comment->comment_ID; ?>]" id="comment[<?php echo $comment->comment_ID; ?>]-nothing" value="later" checked="checked" /> <label for="comment[<?php echo $comment->comment_ID; ?>]-nothing"><?php _e('Defer until later') ?></label>
	</p>

	</li>
<?php
    }
?>
    </ol>

<div id="ajax-response"></div>

    <p class="submit"><input type="submit" name="submit" value="<?php _e('Bulk Moderate Comments &raquo;') ?>" /></p>
<script type="text/javascript">
// <![CDATA[
function markAllForDelete() {
	for (var i=0; i< document.approval.length; i++) {
		if (document.approval[i].value == "delete") {
			document.approval[i].checked = true;
		}
	}
}
function markAllForApprove() {
	for (var i=0; i< document.approval.length; i++) {
		if (document.approval[i].value == "approve") {
			document.approval[i].checked = true;
		}
	}
}
function markAllForDefer() {
	for (var i=0; i< document.approval.length; i++) {
		if (document.approval[i].value == "later") {
			document.approval[i].checked = true;
		}
	}
}
function markAllAsSpam() {
	for (var i=0; i< document.approval.length; i++) {
		if (document.approval[i].value == "spam") {
			document.approval[i].checked = true;
		}
	}
}
document.write('<ul><li><a href="javascript:markAllForApprove()"><?php _e('Mark all for approval'); ?></a></li><li><a href="javascript:markAllAsSpam()"><?php _e('Mark all as spam'); ?></a></li><li><a href="javascript:markAllForDelete()"><?php _e('Mark all for deletion'); ?></a></li><li><a href="javascript:markAllForDefer()"><?php _e('Mark all for later'); ?></a></li></ul>');
// ]]>
</script>

<noscript>
	<p>
		<input name="feelinglucky" type="checkbox" id="feelinglucky" value="true" /> <label for="feelinglucky"><?php _e('Delete every comment marked "defer." <strong>Warning: This can&#8217;t be undone.</strong>'); ?></label>
	</p>
</noscript>
    </form>
<?php
} else {
    // nothing to approve
	echo '<p>'.__("Currently there are no comments for you to moderate.") . "</p>\n";
}
?>

</div>

<?php

break;
}


include('admin-footer.php');

?>
