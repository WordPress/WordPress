<?php
$messages[1] = __('Post updated');
$messages[2] = __('Custom field updated');
$messages[3] = __('Custom field deleted.');
?>
<?php if (isset($_GET['message'])) : ?>
<div class="updated"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php endif; ?>
<div class="wrap">
<?php

$allowed_users = explode(" ", trim(get_settings('fileupload_allowedusers')));

if (0 == $post_ID) {
	$form_action = 'post';
} else {
	$form_action = 'editpost';
	$form_extra = "<input type='hidden' name='post_ID' value='$post_ID' />";
}

if (get_settings('use_pingback')) {
	$form_pingback = '<input type="checkbox" class="checkbox" name="post_pingback" value="1" ';
	if ( get_settings('default_pingback_flag') ) $form_pingback .= 'checked="checked" ';
	$form_pingback .= 'tabindex="7" id="pingback" /> <label for="pingback">' . __('<strong>PingBack</strong> the <abbr title="Universal Resource Identifier">URI</abbr>s in this post') . '</label> <a href="http://wordpress.org/docs/reference/post/#pingback" title="' . __('Help on Pingbacks') . '">?</a><br />';
} else {
	$form_pingback = '';
}

$form_prevstatus = '<input type="hidden" name="prev_status" value="'.$post_status.'" />';

if (get_settings('use_trackback')) {
	$form_trackback = '<p><label for="trackback"><a href="http://wordpress.org/docs/reference/post/#trackback" title="' . __('Help on trackbacks') . '">' . __('<strong>TrackBack</strong> an <abbr title="Universal Resource Identifier">URI</abbr></a>') . '</label> ' . __('(Separate multiple <abbr title="Universal Resource Identifier">URI</abbr>s with spaces.)') . '<br />
	<input type="text" name="trackback_url" style="width: 415px" id="trackback" tabindex="7" value="'. str_replace("\n", ' ', $to_ping) .'" /></p>';
	if ('' != $pinged) {
		$pings .= '<p>'. __('Already pinged:') . '</p><ul>';
		$already_pinged = explode("\n", trim($pinged));
		foreach ($already_pinged as $pinged_url) {
			$pings .= "\n\t<li>$pinged_url</li>";
		}
		$pings .= '</ul>';
	}
} else {
	$form_trackback = '';
}
$saveasdraft = '<input name="save" type="submit" id="save" tabindex="6" value="' . __('Save and Continue Editing') . '" />';

if (empty($post_status)) $post_status = 'draft';

?>

<form name="post" action="post.php" method="post" id="post">
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action ?>' />
<?php echo $form_extra ?>
<?php if (isset($_GET['message']) && 2 > $_GET['message']) : ?>
<script type="text/javascript">
<!--
function focusit() {
	// focus on first input field
	document.post.title.focus();
}
window.onload = focusit;
//-->
</script>
<?php endif; ?>
<div id="poststuff">
    <fieldset id="titlediv">
      <legend><a href="http://wordpress.org/docs/reference/post/#title" title="<?php _e('Help on titles') ?>"><?php _e('Title') ?></a></legend> 
	  <div><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $edited_post_title; ?>" id="title" /></div>
    </fieldset>

    <fieldset id="categorydiv">
      <legend><a href="http://wordpress.org/docs/reference/post/#category" title="<?php _e('Help on categories') ?>"><?php _e('Categories') ?></a></legend> 
	  <div><?php dropdown_categories(get_settings('default_category')); ?></div>
    </fieldset>

    <fieldset id="poststatusdiv">
      <legend><a href="http://wordpress.org/docs/reference/post/#post_status" title="<?php _e('Help on post status') ?>"><?php _e('Post Status') ?></a></legend>
	  <div><label for="post_status_publish" class="selectit"><input id="post_status_publish" name="post_status" type="radio" value="publish" <?php checked($post_status, 'publish'); ?> /> <?php _e('Publish') ?></label> 
	  <label for="post_status_draft" class="selectit"><input id="post_status_draft" name="post_status" type="radio" value="draft" <?php checked($post_status, 'draft'); ?> /> <?php _e('Draft') ?></label> 
	  <label for="post_status_private" class="selectit"><input id="post_status_private" name="post_status" type="radio" value="private" <?php checked($post_status, 'private'); ?> /> <?php _e('Private') ?></label></div>
    </fieldset>
    <fieldset id="commentstatusdiv">
      <legend><a href="http://wordpress.org/docs/reference/post/#comments" title="<?php _e('Help on comment status') ?>"><?php _e('Discussion') ?></a></legend> 
	  <div><label for="comment_status" class="selectit">
	      <input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($comment_status, 'open'); ?> />
         <?php _e('Allow Comments') ?></label> 
		 <label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($ping_status, 'open'); ?> /> <?php _e('Allow Pings') ?></label>
</div>
</fieldset>
<fieldset id="slugdiv">
<legend><?php _e('Post Slug') ?></legend>
<div><input name="post_name" type="text" size="17" id="post_name" value="<?php echo $post_name ?>" /></div>
</fieldset>
    <fieldset id="postpassworddiv">
      <legend><a href="http://wordpress.org/docs/reference/post/#post_password" title="<?php _e('Help on post password') ?>"><?php _e('Post Password') ?></a></legend> 
	  <div><input name="post_password" type="text" size="13" id="post_password" value="<?php echo $post_password ?>" /></div>
    </fieldset>

<br />
<fieldset style="clear:both">
<legend><a href="http://wordpress.org/docs/reference/post/#excerpt" title="<?php _e('Help with excerpts') ?>"><?php _e('Excerpt') ?></a></legend>
<div><textarea rows="1" cols="40" name="excerpt" tabindex="4" id="excerpt"><?php echo $excerpt ?></textarea></div>
</fieldset>

<fieldset id="postdiv">
       <legend><a href="http://wordpress.org/docs/reference/post/#post" title="<?php _e('Help with post field') ?>"><?php _e('Post') ?></a></legend>
		<div id="quicktags">
<?php
	echo '<a href="http://wordpress.org/docs/reference/post/#quicktags" title="' .  __('Help with quicktags') . '">' . __('Quicktags') . '</a>: ';
	include('quicktags.php');
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
?>
<script type="text/javascript">
<!--
edCanvas = document.getElementById('content');
//-->
</script>
<?php
if ($action != 'editcomment') {
    if (get_settings('use_geo_positions')) {
        if (empty($edited_lat)) {
            if (get_settings('use_default_geourl')) {
                $edited_lat = get_settings('default_geourl_lat');
                $edited_lon = get_settings('default_geourl_lon');
            }
        }
?>
<br />
<fieldset id="post_lat_lon">
<label for="post_latf"><?php _e('Latitude:') ?></label><input size="8" type="text" value="<?php echo $edited_lat; ?>" name="post_latf" id="post_latf" />&nbsp;
<label for="post_lonf"><?php _e('Longitude:') ?></label><input size="8" type="text" value="<?php echo $edited_lon; ?>" name="post_lonf" id="post_lonf" />&nbsp; <a href="http://www.geourl.org/resources.html" rel="external"><?php _e('click for Geo Info') ?></a>
</fieldset>
<br />
<?php
    }
}
?>

<?php echo $form_pingback ?>
<?php echo $form_prevstatus ?>
<?php echo $form_trackback; ?>



<p class="submit"><?php echo $saveasdraft; ?> <input type="submit" name="submit" value="<?php _e('Save') ?>" style="font-weight: bold;" tabindex="6" /> 
<?php 
if ('publish' != $post_status || 0 == $post_ID) {
?>
	<input name="publish" type="submit" id="publish" tabindex="10" value="<?php _e('Publish') ?>" /> 
<?php
}
?>
	<input name="referredby" type="hidden" id="referredby" value="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>" />
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
?>
<fieldset id="postcustom">
<legend><?php _e('Custom Fields') ?></legend>
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
<?php do_action('edit_form_advanced', ''); ?>
</div>
</form>
<?php if ('edit' == $action) echo "
<p><a class='delete' href='post.php?action=delete&amp;post=$post_ID' onclick=\"return confirm('" . sprintf(__("You are about to delete this post \'%s\'\\n  \'Cancel\' to stop, \'OK\' to delete."), addslashes($edited_post_title)) . "')\">" .  __('Delete this post') . "</a></p>";
?>
</div>
