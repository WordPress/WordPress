<?php
$title = "Moderate comments";
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

$b2varstoreset = array('action','item_ignored','item_deleted','item_approved');
for ($i=0; $i<count($b2varstoreset); $i += 1) {
	$b2var = $b2varstoreset[$i];
	if (!isset($$b2var)) {
		if (empty($HTTP_POST_VARS["$b2var"])) {
			if (empty($HTTP_GET_VARS["$b2var"])) {
				$$b2var = '';
			} else {
				$$b2var = $HTTP_GET_VARS["$b2var"];
			}
		} else {
			$$b2var = $HTTP_POST_VARS["$b2var"];
		}
	}
}



switch($action) {

case 'update':

	$standalone = 1;
	require_once("b2header.php");

	if ($user_level < 3) {
		die('<p>You have no right to moderate comments.<br />Ask for a promotion to your <a href="mailto:$admin_email">blog admin</a>. :)</p>');
	}
	
	// check if comment moderation is turned on in the settings
	// if not, just give a short note and stop
	if (get_settings("comment_moderation") == "none") {
	    echo "<div class=\"wrap\">\n";
	    echo "Comment moderation has been turned off.<br /><br />\n";
	    echo "</div>\n";
	    include("b2footer.php");
	    exit;
	}	

	$item_ignored = 0;
	$item_deleted = 0;
	$item_approved = 0;
	
	foreach($comment as $key => $value) {
	    switch($value) {
	    case "later":
		// do nothing with that comment
		// wp_set_comment_status($key, "hold");
		++$item_ignored;
		break;
		
	    case "delete":
		wp_set_comment_status($key, "delete");
		++$item_deleted;
		break;
		
	    case "approve":
		wp_set_comment_status($key, "approve");
		if (get_settings("comments_notify") == true) {
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

	require_once('b2header.php');

	if ($user_level <= 3) {
		die('<p>You have no right to moderate comments.<br>Ask for a promotion to your <a href="mailto:$admin_email">blog admin</a>. :)</p>');
	}

	// check if comment moderation is turned on in the settings
	// if not, just give a short note and stop
	if (get_settings("comment_moderation") == "none") {
	    echo "<div class=\"wrap\">\n";
	    echo "Comment moderation has been turned off.<br /><br />\n";
	    echo "</div>\n";
	    include("b2footer.php");
	    exit;
	}	

	// if we come here after deleting/approving comments we give
	// a short overview what has been done
	if (($deleted) || ($approved) || ($ignored)) {
	    echo "<div class=\"wrap\">\n";
	    if ($approved) {
		if ($approved == "1") {
		    echo "1 comment approved <br />\n";
		} else {
		    echo "$approved comments approved <br />\n";
		}
	    }
	    if ($deleted) {
		if ($deleted == "1") {
		    echo "1 comment deleted <br />\n";
		} else {
		    echo "$approved comments deleted <br />\n";
		}
	    }
	    if ($ignored) {
		if ($deleted == "1") {
		    echo "1 comment left unchanged <br />\n";
		} else {
		    echo "$approved comments left unchanged <br />\n";
		}
	    
	    }
	    echo "</div>\n";
	}

	?>
	
<div class="wrap">

	<?php
	
$comments = $wpdb->get_results("SELECT * FROM $tablecomments WHERE comment_approved='0'");
if ($comments) {
    // list all comments that are waiting for approval
    $file = basename(__FILE__);
    echo "The following comments wait for approval:<br /><br />";
    echo "<form name=\"approval\" action=\"$file\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"action\" value=\"update\" />\n";
    echo "<ol id=\"comments\">\n";

    foreach($comments as $comment) {
	$comment_date = mysql2date(get_settings("date_format") . " @ " . get_settings("time_format"), $comment->comment_date);
	$post_title = $wpdb->get_var("SELECT post_title FROM $tableposts WHERE ID='$comment->comment_post_ID'");
        $comment_text = stripslashes($comment->comment_content);
        $comment_text = str_replace('<trackback />', '', $comment_text);
        $comment_text = str_replace('<pingback />', '', $comment_text);
        $comment_text = convert_chars($comment_text);
        $comment_text = convert_bbcode($comment_text);
        $comment_text = convert_gmcode($comment_text);
        $comment_text = convert_smilies($comment_text);
        $comment_text = make_clickable($comment_text);
        $comment_text = balanceTags($comment_text,1);
        $comment_text = apply_filters('comment_text', $comment_text);
	
	echo "<li id=\"comment-$comment->comment_ID\">";
	echo "$comment_date -&gt; $post_title<br />";
	echo "<strong>$comment->comment_author ";
	echo "(<a href=\"mailto:$comment->comment_author_email\">$comment->comment_author_email</a> /";
	echo "<a href=\"$comment->comment_author_url\">$comment->comment_author_url</a>)</strong> ";
	echo "(IP: <a href=\"http://ws.arin.net/cgi-bin/whois.pl?queryinput=$comment->comment_author_IP\">$comment->comment_author_IP</a>)<br />";
	echo $comment_text;
	echo "<strong>Your action:</strong>";
	echo "&nbsp;&nbsp;<input type=\"radio\" name=\"comment[$comment->comment_ID]\" value=\"approve\" />&nbsp;approve";
	echo "&nbsp;&nbsp;<input type=\"radio\" name=\"comment[$comment->comment_ID]\" value=\"delete\" />&nbsp;delete";
	echo "&nbsp;&nbsp;<input type=\"radio\" name=\"comment[$comment->comment_ID]\" value=\"later\" checked=\"checked\" />&nbsp;later";
	echo "<br /><br />";
	echo "</li>\n";
    }
    
    echo "</ol>\n";
    echo "<input type=\"submit\" name=\"submit\" value=\"Continue!\" class=\"search\" style=\"font-weight: bold;\" />\n";
    echo "</form>\n";
} else {
    // nothing to approve
    echo "Currently there are no comments to be approved.<br />\n";
}

	?>

	<br />
</div>

<?php
if ($comments) { 
    // show this help text only if there are comments waiting
?>

<div class="wrap"> 
	<p>For each comment you have to choose either <em>approve</em>, <em>delete</em> or <em>later</em>:</p>
	<p><em>approve</em>: approves comment, so that it will be publically visible
	<?php 
	    if ("1" == get_settings("comments_notify")) {
		echo "; the author of the post will be notified about the new comment on his post.</p>\n";
	    } else {
		echo ".</p>\n";
	    }
	?>	    
	<p><em>delete</em>: remove the content from your blog (note: you won't be asked again, so you should double-check
	that you really want to delete the comment - once deleted you can&#8242;t bring them back!)</p>
	<p><em>later</em>: don&#8242;t change the comment&#8242;s status at all now.</p>
</div>

<?php
} // if comments

break;
}

/* </Template> */
include("b2footer.php") ?>