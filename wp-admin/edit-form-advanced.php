
<div class="wrap">
<?php

$allowed_users = explode(" ", trim(get_settings('fileupload_allowedusers')));

$post_ID = intval($postdata['ID']);

$submitbutton_text = 'Save';
$toprow_title = 'Editing Post #' . $post_ID;
if (0 == $post_ID) {
	$form_action = 'post';
} else {
	$form_action = 'editpost';
	$form_extra = "<input type='hidden' name='post_ID' value='$post_ID' />";
}

$colspan = 2;
$form_pingback = '<input type="hidden" name="post_pingback" value="0" />';
$form_prevstatus = '<input type="hidden" name="prev_status" value="'.$post_status.'" />';
if (get_settings('use_trackback')) {
	$form_trackback = '<p><label for="trackback"><a href="http://wordpress.org/docs/reference/post/#trackback" title="Help on trackbacks"><strong>TrackBack</strong> an <acronym title="Uniform Resource Locator">URL</acronym></a></label>
	 (Separate multiple <acronym title="Uniform Resource Locator">URL</acronym>s with spaces.)<br />
	<input type="text" name="trackback_url" style="width: 415px" id="trackback" tabindex="7" value="'. str_replace("\n", ' ', $to_ping) .'" /></p>';
	if ('' != $pinged) {
		$pings .= '<p>Already pinged:</p><ul>';
		$already_pinged = explode("\n", trim($pinged));
		foreach ($already_pinged as $pinged_url) {
			$pings .= "\n\t<li>$pinged_url</li>";
		}
		$pings .= '</ul>';
	}
} else {
	$form_trackback = '';
}
$saveasdraft = '<input name="save" type="submit" id="save" tabindex="6" value="Save and Continue Editing" />';


?>

<form name="post" action="post.php" method="post" id="post">
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action ?>' />
<?php echo $form_extra ?>

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
      <legend><a href="http://wordpress.org/docs/reference/post/#comments" title="Help on comment status">Discussion</a></legend> 
	  <div><label for="comment_status" class="selectit">
	      <input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($comment_status, 'open'); ?> />
         Allow Comments</label> 
		 <label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($ping_status, 'open'); ?> /> Allow Pings</label>
</div>
</fieldset>
<fieldset id="slugdiv">
<legend>Post Slug</legend>
<div><input name="post_name" type="text" size="17" id="post_name" value="<?php echo $post_name ?>" /></div>
</fieldset>
    <fieldset id="postpassworddiv">
      <legend><a href="http://wordpress.org/docs/reference/post/#post_password" title="Help on post password">Post Password</a></legend> 
	  <div><input name="post_password" type="text" size="13" id="post_password" value="<?php echo $post_password ?>" /></div>
    </fieldset>

<br />
<fieldset style="clear:both">
<legend><a href="http://wordpress.org/docs/reference/post/#excerpt" title="Help with excerpts">Excerpt</a></legend>
<div><textarea rows="1" cols="40" name="excerpt" tabindex="4" id="excerpt"><?php echo $excerpt ?></textarea></div>
</fieldset>

<fieldset id="postdiv">
<legend><a href="http://wordpress.org/docs/reference/post/#post" title="Help with post field">Post</a></legend>
		<div id="quicktags">
<?php
if ( get_settings('use_quicktags') ) {
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
if ( get_settings('use_quicktags') ) {
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
<fieldset id="postcustom">
<legend>Post Custom</legend>
<?php 
if($metadata = has_meta($post_ID)) {
?>
<?php
	list_meta($metadata); 
?>
<?php
}
	meta_form();
?>
</fieldset>

<?php echo $form_pingback ?>
<?php echo $form_prevstatus ?>
<?php echo $form_trackback; ?>



<p><?php echo $saveasdraft; ?> <input type="submit" name="submit" value="Save" style="font-weight: bold;" tabindex="6" /> 
<?php 
if ('publish' != $post_status || 0 == $post_ID) {
?>
	<input name="publish" type="submit" id="publish" tabindex="10" value="Publish" /> 
<?php
}
?>
	<input name="referredby" type="hidden" id="referredby" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
</p>
<?php
if ('' != $pinged) {
	echo $pings;
}

// if the level is 5+, allow user to edit the timestamp - not on 'new post' screen though
// if (($user_level > 4) && ($action != "post"))
if ($user_level > 4) {
	touch_time(($action == 'edit'));
}
if ('edit' == $action) echo "
<p><a href='post.php?action=delete&amp;post=$post_ID' onclick=\"return confirm('You are about to delete this post \'".addslashes($edited_post_title)."\'\\n  \'Cancel\' to stop, \'OK\' to delete.')\">Delete this post</a></p>";
?>

</div>
</form>

</div>
