<?php
require_once('../wp-includes/wp-l10n.php');

$title = __('Moderate comments');
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
	$_GET    = add_magic_quotes($_GET);
	$_POST   = add_magic_quotes($_POST);
	$_COOKIE = add_magic_quotes($_COOKIE);
}

$wpvarstoreset = array('action','item_ignored','item_deleted','item_approved');
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

	$standalone = 1;
	require_once('admin-header.php');

	if ($user_level < 3) {
		die(__('<p>Your level is not high enough to moderate comments.</p>'));
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
		die(__('<p>Your level is not high enough to moderate comments.</p>'));
	}
?>
<ul id="adminmenu2">
       <li><a href="edit.php"> <?php _e('Posts') ?></a></li>
        <li><a href="edit-comments.php"> <?php _e('Comments') ?></a></li>
	<li class="last"><a href="moderation.php" class="current"><?php _e('Awaiting Moderation') ?></a></li>
</ul>
<?php

if (isset($deleted) || isset($approved) || isset($ignored)) {
	echo "<div class='updated'>\n<p>";
	if ($approved) {
		if ('1' == $approved) {
		 echo __("1 comment approved <br />") . "\n";
		} else {
		 echo sprintf(__("%s comments approved <br />"), $approved) . "\n";
		}
	}
	if ($deleted) {
		if ('1' == $deleted) {
		echo __("1 comment deleted <br />") . "\n";
		} else {
		echo sprintf(__("%s comments deleted <br />"), $deleted) . "\n";
		}
	}
	if ($ignored) {
		if ('1' == $ignored) {
		echo __("1 comment unchanged <br />") . "\n";
		} else {
		echo sprintf(__("%s comments unchanged <br />"), $ignored) . "\n";
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
    <?php _e('<p>The following comments are in the moderation queue:</p>') ?>
    <form name="approval" action="moderation.php" method="post">
    <input type="hidden" name="action" value="update" />
    <ol id="comments">
<?php
    foreach($comments as $comment) {
	$comment_date = mysql2date(get_settings("date_format") . " @ " . get_settings("time_format"), $comment->comment_date);
	$post_title = $wpdb->get_var("SELECT post_title FROM $tableposts WHERE ID='$comment->comment_post_ID'");
	
	echo "\n\t<li id='comment-$comment->comment_ID'>"; 
	?>
			<p><strong><?php _e('Name:') ?></strong> <?php comment_author() ?> <?php if ($comment->comment_author_email) { ?>| <strong><?php _e('Email:') ?></strong> <?php comment_author_email_link() ?> <?php } if ($comment->comment_author_email) { ?> | <strong><?php _e('URI:') ?></strong> <?php comment_author_url_link() ?> <?php } ?>| <strong><?php _e('IP:') ?></strong> <a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>"><?php comment_author_IP() ?></a></p>
<?php comment_text() ?>
<p><?php
echo "<a href=\"post.php?action=editcomment&amp;comment=".$comment->comment_ID."\">" . __('Edit') . "</a>";
echo " | <a href=\"post.php?action=deletecomment&amp;p=".$comment->comment_post_ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return confirm('" . sprintf(__("You are about to delete this comment by \'%s\'\\n  \'Cancel\' to stop, \'OK\' to delete."), $comment->comment_author) . "')\">" . __('Delete just this comment') . "</a> | "; ?><?php _e('Bulk action:') ?>
	<input type="radio" name="comment[<?php echo $comment->comment_ID; ?>]" id="comment[<?php echo $comment->comment_ID; ?>]-approve" value="approve" /> <label for="comment[<?php echo $comment->comment_ID; ?>]-approve"><?php _e('Approve') ?></label>
	<input type="radio" name="comment[<?php echo $comment->comment_ID; ?>]" id="comment[<?php echo $comment->comment_ID; ?>]-delete" value="delete" /> <label for="comment[<?php echo $comment->comment_ID; ?>]-delete"><?php _e('Delete') ?></label>
	<input type="radio" name="comment[<?php echo $comment->comment_ID; ?>]" id="comment[<?php echo $comment->comment_ID; ?>]-nothing" value="later" checked="checked" /> <label for="comment[<?php echo $comment->comment_ID; ?>]-nothing"><?php _e('Do nothing') ?></label>

	</li>
<?php
    }
?>
    </ol>
    <p class="submit"><input type="submit" name="submit" value="<?php _e('Moderate Comments &raquo;') ?>" /></p>
    </form>
<?php
} else {
    // nothing to approve
    echo __("<p>Currently there are no comments to be approved.</p>") . "\n";
}
?>

</div>

<?php

break;
}

/* </Template> */
include("admin-footer.php") ?>
