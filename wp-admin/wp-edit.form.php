
<div class="wrap">
<?php

$allowed_users = explode(" ", trim($fileupload_allowedusers));

function selected($selected, $current) {
	if ($selected == $current) echo ' selected="selected"';
}

function checked($checked, $current) {
	if ($checked == $current) echo ' checked="checked"';
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
            $form_pingback .= 'tabindex="7" id="pingback" /> <label for="pingback"><strong>PingBack</strong> the <acronym title="Uniform Resource Locators">URL</acronym>s in this post</label> <a href="http://wordpress.org/docs/reference/post/#pingback" title="Help on Pingbacks">?</a><br />';
		} else {
			$form_pingback = '';
		}
		if ($use_trackback) {
			$form_trackback = '<p><label for="trackback"><a href="http://wordpress.org/docs/reference/post/#trackback" title="Help on trackbacks"><strong>TrackBack</strong> an <acronym title="Uniform Resource Locator">URL</acronym></a>:</label> (Separate multiple <acronym title="Uniform Resource Locator">URL</acronym>s with commas.)<br />
			<input type="text" name="trackback_url" style="width: 415px" id="trackback" tabindex="7" /></p>';
		} else {
			$form_trackback = '';
		}
		$colspan = 3;
		$saveasdraft = '<input name="save" type="submit" id="save" tabindex="6" value="Save and Continue Editing" />';
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

<form name="post" action="wp-post.php" method="post" id="post">
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />

<?php if ($action != 'editcomment') {
  // this is for everything but comment editing
?> 
<script type="text/javascript">
function focusit() {
	// focus on first input field
	document.post.title.focus();
}
window.onload = focusit;
</script>

<div id="titlediv">
		<label for="title"><a href="http://wordpress.org/docs/reference/post/#title" title="Help on titles">Title</a>:</label>		
		<br />
		<input type="text" name="post_title" size="25" tabindex="1" style="width: 190px;" value="<?php echo $edited_post_title; ?>" id="title" /> 
	</div>
		<div id="categorydiv"> <a href="http://wordpress.org/docs/reference/post/#category" title="Help on categories">Categories</a>: <br /> 
		<?php dropdown_categories($default_post_cat); ?>
	</div>
	<div id="poststatusdiv">
		<a href="http://wordpress.org/docs/reference/post/#post_status" title="Help on post status">Post
		Status</a>:
		<label for="post_status_publish" class="selectit"><input id="post_status_publish" name="post_status" type="radio" value="publish" <?php checked($post_status, 'publish'); ?> /> Publish</label>
		<label for="post_status_draft" class="selectit"><input id="post_status_draft" name="post_status" type="radio" value="draft" <?php checked($post_status, 'draft'); ?> /> Draft</label>
		<label for="post_status_private" class="selectit"><input id="post_status_private" name="post_status" type="radio" value="private" <?php checked($post_status, 'private'); ?> /> Private</label>
	</div>
	<div id="commentstatusdiv">
		<a href="http://wordpress.org/docs/reference/post/#comments" title="Help on comment status">Comments</a>:
		<label for="comment_status_open" class="selectit"><input id="comment_status_open" name="comment_status" type="radio" value="open" <?php checked($comment_status, 'open'); ?> /> Open</label>
	  <label for="comment_status_closed" class="selectit"><input id="comment_status_closed" name="comment_status" type="radio" value="closed" <?php checked($comment_status, 'closed'); ?> /> 
	  Closed</label>
	</div>
	<div id="pingstatusdiv">
		<a href="http://wordpress.org/docs/reference/post/#pings" title="Help on ping status">Pings</a>:
		<label for="ping_status_open" class="selectit"><input id="ping_status_open" name="ping_status" type="radio" value="open"<?php checked($ping_status, 'open'); ?> /> Open</label>
		<label for="ping_status_closed" class="selectit"><input id="ping_status_closed" name="ping_status" type="radio" value="closed" <?php checked($ping_status, 'closed'); ?> /> Closed</label>

	</div>
	<div id="postpassworddiv">
		<label for="post_password"><a href="http://wordpress.org/docs/reference/post/#post_password" title="Help on post password">Post
		Password</a>:</label>		
		<br />
		<input name="post_password" type="text" id="post_password" value="<?php echo $post_password ?>" />
	</div>


<div id="poststuff">
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
?>
<p style="clear: both;" ><a href="http://wordpress.org/docs/reference/post/#excerpt" title="Help with excerpts">Excerpt</a>:
<br />
<textarea rows="3" cols="40" name="excerpt" tabindex="4" wrap="virtual" id="excerpt"><?php echo $excerpt ?></textarea></p>

<?php
} // if not a comment
?>
<table style="width: 100%; ">
	<tr>
		<td>
<?php
if ($action != 'editcomment') {
	echo '<label for="content"><a href="http://wordpress.org/docs/reference/post/#post" title="Help with post field">Post</a>:</label>';
} else {
	echo '<label for="content">Comment:</label>';
}
?>
		</td>
		<td id="quicktags">
<?php
if ($use_quicktags) {
	echo '<a href="http://wordpress.org/docs/reference/post/#quicktags" title="Help with quicktags">Quicktags</a>: ';
	include('wp-quicktags.php');
}
?>
		</td>
	</tr>
</table>
<?php
 $rows = get_settings('default_post_edit_rows');
 if (($rows < 3) || ($rows > 100)) {
     $rows = 10;
 }
?>
<textarea rows="<?php echo $rows; ?>" cols="40" name="content" tabindex="4" wrap="virtual" id="content"><?php echo $content ?></textarea><br />
<?php
if ($use_quicktags) {
?>
<script type="text/javascript" language="JavaScript">
<!--
edCanvas = document.getElementById('content');
//-->
</script>
<?php
}
if ($action != 'editcomment') {
    if (get_settings('use_geo_positions')) {
        if (empty($edited_lat)) {
            if (get_settings('use_default_geourl')) {
                $edited_lat = get_settings('default_geourl_lat');
                $edited_lon = get_settings('default_geourl_lon');
            }
        }
?>
<label for="post_latf">Latitude:</label><input size="8" type="text" value="<?php echo $edited_lat; ?>" name="post_latf">&nbsp;
<label for="post_lonf">Longitude:</label><input size="8" type="text" value="<?php echo $edited_lon; ?>" name="post_lonf">&nbsp; <a href="http://www.geourl.org/resources.html" rel="external" >click for Geo Info</a>
<br>
<?php
    }
}
?>

<?php echo $form_pingback ?>
<?php echo $form_prevstatus ?>

<p><?php echo $saveasdraft; ?> <input type="submit" name="submit" value="<?php echo $submitbutton_text ?>" style="font-weight: bold;" tabindex="6" />
  <input name="referredby" type="hidden" id="referredby" value="<?php echo $HTTP_SERVER_VARS['HTTP_REFERER']; ?>" />
</p>


<?php
if ($action != 'editcomment') {
    if ( ($use_fileupload) && ($user_level >= $fileupload_minlevel)
         && (in_array($user_login, $allowed_users) || (trim($fileupload_allowedusers)=="")) ) { ?>
<input type="button" value="upload a file/image" onclick="launchupload();" class="search"  tabindex="10" />
<?php }
}

echo $form_trackback;

// if the level is 5+, allow user to edit the timestamp - not on 'new post' screen though
// if (($user_level > 4) && ($action != "post"))
if ($user_level > 4) {
	touch_time(($action == 'edit'));
}
if ('edit' == $action) echo "
<p><a href='wp-post.php?action=delete&amp;post=$post' onclick=\"return confirm('You are about to delete this post \'".$edited_post_title."\'\\n  \'Cancel\' to stop, \'OK\' to delete.')\">Delete this post</a></p>";
?>

</form>
</div>
</div>
