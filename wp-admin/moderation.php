<?php
$title = 'Moderate comments';
$parent_file = 'edit.php';
/* <Moderation> */

function add_magic_quotes($array) {
	foreach ($array as $k => $v) {
		if (is_array($v)) {
			$array[$k] = add_magic_quotes($v);
		} else {
			$array[$k] = addslashes($v);
		}
	}
	return $array;
} 

if (!get_magic_quotes_gpc()) {
	$HTTP_GET_VARS    = add_magic_quotes($HTTP_GET_VARS);
	$HTTP_POST_VARS   = add_magic_quotes($HTTP_POST_VARS);
	$HTTP_COOKIE_VARS = add_magic_quotes($HTTP_COOKIE_VARS);
}

$wpvarstoreset = array('action','item_ignored','item_deleted','item_approved');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($HTTP_POST_VARS["$wpvar"])) {
			if (empty($HTTP_GET_VARS["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $HTTP_GET_VARS["$wpvar"];
			}
		} else {
			$$wpvar = $HTTP_POST_VARS["$wpvar"];
		}
	}
}

$comment = array();
if (isset($HTTP_POST_VARS["comment"])) {
	foreach ($HTTP_POST_VARS["comment"] as $k => $v) {
		$comment[intval($k)] = $v;
	}
}

switch($action) {

case 'update':

	$standalone = 1;
	require_once('admin-header.php');

	if ($user_level < 3) {
		die('<p>Your level is not high enough to moderate comments.</p>');
	}

	$item_ignored = 0;
	$item_deleted = 0;
	$item_approved = 0;
	
	foreach($comment as $key => $value) {
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
			
			case 'approve':
				wp_set_comment_status($key, 'approve');
				if (get_settings('comments_notify') == true) {
					wp_notify_postauthor($key);
				}
				++$item_approved;
				break;
	    }
	}

	$file = basename(__FILE__);
	header("Location: $file?ignored=$item_ignored&deleted=$item_deleted&approved=$item_approved");
	exit();

break;

default:

	require_once('admin-header.php');

	if ($user_level <= 3) {
		die('<p>Your level is not high enough to moderate comments.</p>');
	}
?>
<ul id="adminmenu2">
	<li><a href="edit.php"> Posts</a></li>
	<li><a href="edit-comments.php"> Comments</a></li>
	<li class="last"><a href="moderation.php" class="current">Awaiting Moderation</a></li>
</ul>
<?php
$ignored = $_GET['ignored'];
$deleted = $_GET['deleted'];
$approved = $_GET['approved'];

if (($deleted) || ($approved) || ($ignored)) {
	echo "<div class='updated'>\n<p>";
	if ($approved) {
		if ('1' == $approved) {
		echo "1 comment approved <br />\n";
		} else {
		echo "$approved comments approved <br />\n";
		}
	}
	if ($deleted) {
		if ('1' == $deleted) {
		echo "1 comment deleted <br />\n";
		} else {
		echo "$deleted comments deleted <br />\n";
		}
	}
	if ($ignored) {
		if ('1' == $ignored) {
		echo "1 comment unchanged <br />\n";
		} else {
		echo "$ignored comments unchanged <br />\n";
		}
	}
	echo "</p></div>\n";
}

?>
	
<div class="wrap">
<?php
$comments = $wpdb->get_results("SELECT * FROM $tablecomments WHERE comment_approved = '0'");

if ($comments) {
    // list all comments that are waiting for approval
    $file = basename(__FILE__);
?>
    <p>The following comments are in the moderation queue:</p>
    <form name="approval" action="moderation.php" method="post">
    <input type="hidden" name="action" value="update" />
    <ol id="comments">
<?php
    foreach($comments as $comment) {
	$comment_date = mysql2date(get_settings("date_format") . " @ " . get_settings("time_format"), $comment->comment_date);
	$post_title = $wpdb->get_var("SELECT post_title FROM $tableposts WHERE ID='$comment->comment_post_ID'");
	
	echo "\n\t<li id='comment-$comment->comment_ID'>"; 
	?>
			<p><strong>Name:</strong> <?php comment_author() ?> <?php if ($comment->comment_author_email) { ?>| <strong>Email:</strong> <?php comment_author_email_link() ?> <?php } if ($comment->comment_author_email) { ?> | <strong>URI:</strong> <?php comment_author_url_link() ?> <?php } ?>| <strong>IP:</strong> <a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>"><?php comment_author_IP() ?></a></p>
<?php comment_text() ?>
<p><?php
echo "<a href=\"post.php?action=editcomment&amp;comment=".$comment->comment_ID."\">Edit</a>";
				echo " | <a href=\"post.php?action=deletecomment&amp;p=".$comment->comment_post_ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return confirm('You are about to delete this comment by \'".$comment->comment_author."\'\\n  \'Cancel\' to stop, \'OK\' to delete.')\">Delete just this comment</a> | "; ?>Bulk action:
	<input type="radio" name="comment[<?php echo $comment->comment_ID; ?>]" id="comment[<?php echo $comment->comment_ID; ?>]-approve" value="approve" /> <label for="comment[<?php echo $comment->comment_ID; ?>]-approve">Approve</label>
	<input type="radio" name="comment[<?php echo $comment->comment_ID; ?>]" id="comment[<?php echo $comment->comment_ID; ?>]-delete" value="delete" /> <label for="comment[<?php echo $comment->comment_ID; ?>]-delete">Delete</label>
	<input type="radio" name="comment[<?php echo $comment->comment_ID; ?>]" id="comment[<?php echo $comment->comment_ID; ?>]-nothing" value="later" checked="checked" /> <label for="comment[<?php echo $comment->comment_ID; ?>]-nothing">Do nothing</label>

	</li>
<?php
    }
?>
    </ol>
    <p class="submit"><input type="submit" name="submit" value="Moderate Comments &raquo;" /></p>
    </form>
<?php
} else {
    // nothing to approve
    echo "<p>Currently there are no comments to be approved.</p>\n";
}
?>

</div>

<?php

break;
}

/* </Template> */
include("admin-footer.php") ?>
