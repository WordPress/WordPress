<div class="wrap">
<?php

function selected($selected, $current) {
	if ($selected == $current) echo ' selected="selected"';
}

switch($action) {
	case 'post':
		$submitbutton_text = 'Blog this!';
		$toprow_title = 'New Post';
		$form_action = 'post';
		$form_extra = '';
		if ($use_pingback) {
			$form_pingback = '<input type="checkbox" class="checkbox" name="post_pingback" value="1" ';
            if ($post_pingback) $form_pingback .= 'checked="checked" ';
            $form_pingback .= 'tabindex="7" id="pingback" /> <label for="pingback">PingBack the URLs in this post</label><br />';
		} else {
			$form_pingback = '';
		}
		if ($use_trackback) {
			$form_trackback = '<p><label for="trackback"><strong>TrackBack</strong> an <acronym title="Uniform Resource Locator">URL</acronym>:</label> (Separate multiple <acronym title="Uniform Resource Locator">URL</acronym>s with commas.)<br />
			<input type="text" name="trackback_url" style="width: 415px" id="trackback" tabindex="7" /></p>';
		} else {
			$form_trackback = '';
		}
		$colspan = 3;
		break;
	case "edit":
		$submitbutton_text = 'Edit this!';
		$toprow_title = 'Editing Post #' . $postdata['ID'];
		$form_action = 'editpost';
		$form_extra = "' />\n<input type='hidden' name='post_ID' value='$post";
		$colspan = 2;
		$form_pingback = '<input type="hidden" name="post_pingback" value="0" />';
		$form_prevstatus = '<input type="hidden" name="prev_status" value="'.$post_status.'" />';
		$form_trackback = '';
		break;
	case "editcomment":
		$submitbutton_text = 'Edit this!';
		$toprow_title = 'Editing Comment # '.$commentdata['comment_ID'];
		$form_action = 'editedcomment';
		$form_extra = "' />\n<input type='hidden' name='comment_ID' value='$comment' />\n<input type='hidden' name='comment_post_ID' value='".$commentdata["comment_post_ID"];
		$colspan = 3;
		$form_pingback = '<input type="hidden" name="post_pingback" value="0" />';
		$form_trackback = '';
		break;
}

?>

<form name="post" action="b2edit.php" method="post" id="post">
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />

<?php if ($action != "editcomment") {
  // this is for everything but comment editing
?> 
<script type="text/javascript">
function focusit() {
	// focus on first input field
	document.post.title.focus();
}
window.onload = focusit;
</script>
<table>
	<tr> 
	<td width="210">
		<label for="title">Title:</label><br />
		<input type="text" name="post_title" size="25" tabindex="1" style="width: 190px;" value="<?php echo $edited_post_title; ?>" id="title" /> 
	</td>
	<td>
		<label for="category">Category:</label><br /> 
		<?php dropdown_categories($blog_ID, $default_post_cat); ?>
	</td>
	<td>
		<label for="post_status">Post Status:</label><br />          
		<select name="post_status" id="post_status">
			<option value="publish"<?php selected($post_status, 'publish'); ?>>Publish</option>
			<option value="draft"<?php selected($post_status, 'draft'); ?>>Draft</option>
			<option value="private"<?php selected($post_status, 'private'); ?>>Private</option>
		</select>
	</td>
	<td>
		<label for="comment_status">Comments:</label><br />
		<select name="comment_status" id="comment_status">
			<option value="open"<?php selected($comment_status, 'open'); ?>>Open</option>
			<option value="closed"<?php selected($comment_status, 'closed'); ?>>Closed</option>
		</select>
	</td>
	<td>
		<label for="ping_status">Pings:</label><br />	
		<select name="ping_status" id="ping_status">
			<option value="open"<?php selected($ping_status, 'open'); ?>>Open</option>
			<option value="closed"<?php selected($ping_status, 'closed'); ?>>Closed</option>
		</select>
	</td>
	<td>
		<label for="post_password">Post Password:</label><br />
		<input name="post_password" type="text" id="post_password" value="<?php echo $post_password ?>" />
	</td>
	</tr>
</table>
<?php

} else {
  
// this is for comment editing
?>
<script type="text/javascript">
function focusit() {
	// focus on first input field
	document.post.name.focus();
}
window.onload = focusit;
</script>
<table>
	<tr>
	<td>
		<label for="name">Name:</label><br />
		<input type="text" name="newcomment_author" size="22" value="<?php echo format_to_edit($commentdata['comment_author']) ?>" tabindex="1" id="name" /></td>
	<td>
		<label for="email">E-mail:</label><br />
		<input type="text" name="newcomment_author_email" size="30" value="<?php echo format_to_edit($commentdata['comment_author_email']) ?>" tabindex="2" id="email" />
	</td>
	<td>
		<label for="URL">URL:</label><br />
		<input type="text" name="newcomment_author_url" size="35" value="<?php echo format_to_edit($commentdata['comment_author_url']) ?>" tabindex="3" id="URL" />
	</td>
	</tr>
</table>
<?php
  
} // end else comment editing

?>

<?php
if ($action != 'editcomment') {
  echo '<p><label for="excerpt">Excerpt:</label><br />';
?>

<textarea rows="3" cols="40" style="width:100%" name="excerpt" tabindex="4" wrap="virtual" id="excerpt"><?php echo $excerpt ?></textarea></p>

<?php
} // if not a comment
?>
<table width="100%">
	<tr>
		<td>
<?php
if ($action != 'editcomment') {
	echo '<label for="content">Post:</label>';
} else {
	echo '<label for="content">Comment:</label>';
}
?>
		</td>
		<td align="right">
<?php if ($use_quicktags) {
	include('b2quicktags.php');
	}
?>
		</td>
	</tr>
</table>
<?php
 $rows = get_settings('default_post_edit_rows');
 if (($rows < 3) || ($rows > 100)) {
     $rows = 9;
 }
?>
<textarea rows="<?php echo $rows; ?>" cols="40" style="width:100%" name="content" tabindex="4" wrap="virtual" id="content"><?php echo $content ?></textarea><br />

<?php echo $form_pingback ?>
<?php echo $form_prevstatus ?>

<p><input type="submit" name="submit" value="<?php echo $submitbutton_text ?>" class="search" style="font-weight: bold;" tabindex="6" /></p>


<?php if ( ($use_fileupload) && ($user_level >= $fileupload_minlevel) && ((ereg(" ".$user_login." ", $fileupload_allowedusers)) || (trim($fileupload_allowedusers)=="")) ) { ?>
<input type="button" value="upload a file/image" onclick="launchupload();" class="search"  tabindex="10" />
<?php }

echo $form_trackback;

// if the level is 5+, allow user to edit the timestamp - not on 'new post' screen though
// if (($user_level > 4) && ($action != "post"))
if ($user_level > 4) {
	touch_time(($action == 'edit'));
}
if ('edit' == $action) echo "
<p><a href='b2edit.php?action=delete&amp;post=$post' onclick=\"return confirm('You are about to delete this post \'".$edited_post_title."\'\\n  \'Cancel\' to stop, \'OK\' to delete.')\">Delete this post</a></p>";
?>
</form>
</div>