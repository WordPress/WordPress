
<div class="wrap">
<?php

$allowed_users = explode(" ", trim($fileupload_allowedusers));

function selected($selected, $current) {
	if ($selected == $current) echo ' selected="selected"';
}

function checked($checked, $current) {
	if ($checked == $current) echo ' checked="checked"';
}


$submitbutton_text = 'Save';
$toprow_title = 'Editing Post #' . $postdata['ID'];
$form_action = 'editpost';
$form_extra = "' />\n<input type='hidden' name='post_ID' value='$post->ID";
$colspan = 2;
$form_pingback = '<input type="hidden" name="post_pingback" value="0" />';
$form_prevstatus = '<input type="hidden" name="prev_status" value="'.$post_status.'" />';
if ($use_trackback) {
	$form_trackback = '<p><label for="trackback"><a href="http://wordpress.org/docs/reference/post/#trackback" title="Help on trackbacks"><strong>TrackBack</strong> an <acronym title="Uniform Resource Locator">URL</acronym></a></label>
	 (Separate multiple <acronym title="Uniform Resource Locator">URL</acronym>s with spaces.)<br />
	<input type="text" name="trackback_url" style="width: 415px" id="trackback" tabindex="7" value="'. str_replace("\n", ' ', $to_ping) .'" /></p>';
	if ('' != $pinged) {
		$form_trackback .= '<p>Already pinged:</p><ul>';
		$already_pinged = explode("\n", trim($pinged));
		foreach ($already_pinged as $pinged_url) {
			$form_trackback .= "\n\t<li>$pinged_url</li>";
		}
		$form_trackback .= '</ul>';
	}
} else {
	$form_trackback = '';
}
$saveasdraft = '<input name="save" type="submit" id="save" tabindex="6" value="Save and Continue Editing" />';


?>

<form name="post" action="post.php" method="post" id="post">
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />

<script type="text/javascript">
<!--
function focusit() {
	// focus on first input field
	document.post.title.focus();
}
window.onload = focusit;
//-->
</script>

<div id="poststuff">
    <fieldset id="titlediv">
      <legend><a href="http://wordpress.org/docs/reference/post/#title" title="Help on titles">Title</a></legend> 
	  <div><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $edited_post_title; ?>" id="title" /></div>
    </fieldset>

    <fieldset id="categorydiv">
      <legend><a href="http://wordpress.org/docs/reference/post/#category" title="Help on categories">Categories</a></legend> 
	  <div><?php dropdown_categories($default_post_cat); ?></div>
    </fieldset>

    <fieldset id="poststatusdiv">
      <legend><a href="http://wordpress.org/docs/reference/post/#post_status" title="Help on post status">Post Status</a></legend>
	  <div><label for="post_status_publish" class="selectit"><input id="post_status_publish" name="post_status" type="radio" value="publish" <?php checked($post_status, 'publish'); ?> /> Publish</label> 
	  <label for="post_status_draft" class="selectit"><input id="post_status_draft" name="post_status" type="radio" value="draft" <?php checked($post_status, 'draft'); ?> /> Draft</label> 
	  <label for="post_status_private" class="selectit"><input id="post_status_private" name="post_status" type="radio" value="private" <?php checked($post_status, 'private'); ?> /> Private</label></div>
    </fieldset>
    <fieldset id="commentstatusdiv">
      <legend><a href="http://wordpress.org/docs/reference/post/#comments" title="Help on comment status">Comments</a></legend> 
	  <div><label for="comment_status_open" class="selectit"><input id="comment_status_open" name="comment_status" type="radio" value="open" <?php checked($comment_status, 'open'); ?> /> Open</label> 
	  <label for="comment_status_closed" class="selectit"><input id="comment_status_closed" name="comment_status" type="radio" value="closed" <?php checked($comment_status, 'closed'); ?> /> Closed</label></div>
    </fieldset>
    <fieldset id="pingstatusdiv">
      <legend><a href="http://wordpress.org/docs/reference/post/#pings" title="Help on ping status">Pings</a></legend> 
	  <div><label for="ping_status_open" class="selectit"><input id="ping_status_open" name="ping_status" type="radio" value="open" <?php checked($ping_status, 'open'); ?> /> Open</label> 
	  <label for="ping_status_closed" class="selectit"><input id="ping_status_closed" name="ping_status" type="radio" value="closed" <?php checked($ping_status, 'closed'); ?> /> Closed</label></div>
    </fieldset>
    <fieldset id="postpassworddiv">
      <legend><a href="http://wordpress.org/docs/reference/post/#post_password" title="Help on post password">Post Password</a></legend> 
	  <div><input name="post_password" type="text" size="18" id="post_password" value="<?php echo $post_password ?>" /></div>
    </fieldset>

<br />
<fieldset style="clear:both">
<legend><a href="http://wordpress.org/docs/reference/post/#excerpt" title="Help with excerpts">Excerpt</a></legend>
<div><textarea rows="2" cols="40" name="excerpt" tabindex="4" id="excerpt"><?php echo $excerpt ?></textarea></div>
</fieldset>

<fieldset>
<legend><a href="http://wordpress.org/docs/reference/post/#post" title="Help with post field">Post</a></legend>
		<div id="quicktags">
<?php
if ($use_quicktags) {
	echo '<a href="http://wordpress.org/docs/reference/post/#quicktags" title="Help with quicktags">Quicktags</a>: ';
	include('quicktags.php');
}
?>
</div>
<?php
 $rows = get_settings('default_post_edit_rows');
 if (($rows < 3) || ($rows > 100)) {
     $rows = 10;
 }
?>
<div><textarea rows="<?php echo $rows; ?>" cols="40" name="content" tabindex="5" id="content"><?php echo $content ?></textarea></div>
</fieldset>

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

<p><?php echo $saveasdraft; ?> <input type="submit" name="submit" value="Save" style="font-weight: bold;" tabindex="6" /> 
  <input name="publish" type="submit" id="publish" tabindex="10" value="Publish" /> 
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
<p><a href='post.php?action=delete&amp;post=$post->ID' onclick=\"return confirm('You are about to delete this post \'".addslashes($edited_post_title)."\'\\n  \'Cancel\' to stop, \'OK\' to delete.')\">Delete this post</a></p>";
?>

</div>
</form>

</div>
