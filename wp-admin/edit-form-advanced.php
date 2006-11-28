<?php
$messages[1] = __('Post updated');
$messages[2] = __('Custom field updated');
$messages[3] = __('Custom field deleted.');
?>
<?php if (isset($_GET['message'])) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php endif; ?>

<form name="post" action="post.php" method="post" id="post">
<?php if ( (isset($mode) && 'bookmarklet' == $mode) || isset($_GET['popupurl']) ): ?>
<input type="hidden" name="mode" value="bookmarklet" />
<?php endif; ?>

<div class="wrap">
<?php

if (0 == $post_ID) {
	$form_action = 'post';
	$temp_ID = -1 * time();
	$form_extra = "<input type='hidden' id='post_ID' name='temp_ID' value='$temp_ID' />";
	wp_nonce_field('add-post');
} else {
	$form_action = 'editpost';
	$form_extra = "<input type='hidden' id='post_ID' name='post_ID' value='$post_ID' />";
	wp_nonce_field('update-post_' .  $post_ID);
}

$form_pingback = '<input type="hidden" name="post_pingback" value="' . get_option('default_pingback_flag') . '" id="post_pingback" />';

$form_prevstatus = '<input type="hidden" name="prev_status" value="' . $post->post_status . '" />';

$form_trackback = '<input type="text" name="trackback_url" style="width: 415px" id="trackback" tabindex="7" value="'. str_replace("\n", ' ', $post->to_ping) .'" />';

if ('' != $post->pinged) {
	$pings = '<p>'. __('Already pinged:') . '</p><ul>';
	$already_pinged = explode("\n", trim($post->pinged));
	foreach ($already_pinged as $pinged_url) {
		$pings .= "\n\t<li>" . wp_specialchars($pinged_url) . "</li>";
	}
	$pings .= '</ul>';
}

$saveasdraft = '<input name="save" type="submit" id="save" tabindex="3" value="' . __('Save and Continue Editing') . '" />';

if (empty($post->post_status)) $post->post_status = 'draft';

?>

<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" id="hiddenaction" name="action" value="<?php echo $form_action ?>" />
<input type="hidden" id="originalaction" name="originalaction" value="<?php echo $form_action ?>" />
<input type="hidden" name="post_author" value="<?php echo $post->post_author ?>" />
<input type="hidden" id="post_type" name="post_type" value="post" />

<?php echo $form_extra ?>
<?php if (isset($_GET['message']) && 2 > $_GET['message']) : ?>
<script type="text/javascript">
function focusit() {
	// focus on first input field
	document.post.title.focus();
}
addLoadEvent(focusit);
</script>
<?php endif; ?>
<div id="poststuff">

<div id="moremeta">
<div id="grabit" class="dbx-group">

<fieldset id="categorydiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Categories') ?></h3>
<div class="dbx-content">
<p id="jaxcat"></p>
<ul id="categorychecklist"><?php dropdown_categories(); ?></ul></div>
</fieldset>

<fieldset id="commentstatusdiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Discussion') ?></h3>
<div class="dbx-content">
<input name="advanced_view" type="hidden" value="1" />
<label for="comment_status" class="selectit">
<input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($post->comment_status, 'open'); ?> />
<?php _e('Allow Comments') ?></label> 
<label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($post->ping_status, 'open'); ?> /> <?php _e('Allow Pings') ?></label>
</div>
</fieldset>

<fieldset id="passworddiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Post Password') ?></h3> 
<div class="dbx-content"><input name="post_password" type="text" size="13" id="post_password" value="<?php echo $post->post_password ?>" /></div>
</fieldset>

<fieldset id="slugdiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Post Slug') ?></h3> 
<div class="dbx-content"><input name="post_name" type="text" size="13" id="post_name" value="<?php echo $post->post_name ?>" /></div>
</fieldset>

<fieldset id="poststatusdiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Post Status') ?></h3> 
<div class="dbx-content"><?php if ( current_user_can('publish_posts') ) : ?>
<label for="post_status_publish" class="selectit"><input id="post_status_publish" name="post_status" type="radio" value="publish" <?php checked($post->post_status, 'publish'); checked($post->post_status, 'future'); ?> /> <?php _e('Published') ?></label>
<?php endif; ?>
	  <label for="post_status_draft" class="selectit"><input id="post_status_draft" name="post_status" type="radio" value="draft" <?php checked($post->post_status, 'draft'); ?> /> <?php _e('Draft') ?></label>
	  <label for="post_status_private" class="selectit"><input id="post_status_private" name="post_status" type="radio" value="private" <?php checked($post->post_status, 'private'); ?> /> <?php _e('Private') ?></label></div>
</fieldset>

<?php if ( current_user_can('edit_posts') ) : ?>
<fieldset id="posttimestampdiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Post Timestamp'); ?>:</h3>
<div class="dbx-content"><?php touch_time(($action == 'edit')); ?></div>
</fieldset>
<?php endif; ?>

<?php 
$authors = get_editable_authors( $current_user->id ); // TODO: ROLE SYSTEM
if ( $authors && count( $authors ) > 1 ) :
?>
<fieldset id="authordiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Post Author'); ?>:</h3>
<div class="dbx-content">
<select name="post_author_override" id="post_author_override">
<?php
foreach ($authors as $o) :
$o = get_userdata( $o->ID );
if ( $post->post_author == $o->ID || ( empty($post_ID) && $user_ID == $o->ID ) ) $selected = 'selected="selected"';
else $selected = '';
echo "<option value='$o->ID' $selected>$o->display_name</option>";
endforeach;
?>
</select>
</div>
</fieldset>
<?php endif; ?>

<?php do_action('dbx_post_sidebar'); ?>

</div>
</div>

<fieldset id="titlediv">
	<legend><?php _e('Title') ?></legend>
	<div><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $post->post_title; ?>" id="title" /></div>
</fieldset>

<fieldset id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>">
<legend><?php _e('Post') ?></legend>

	<?php the_editor($post->post_content); ?>
</fieldset>

<?php echo $form_pingback ?>
<?php echo $form_prevstatus ?>


<p class="submit">
<span id="autosave"></span>
<?php echo $saveasdraft; ?>
<input type="submit" name="submit" value="<?php _e('Save') ?>" style="font-weight: bold;" tabindex="4" /> 
<?php 
if ('publish' != $post->post_status || 0 == $post_ID) {
?>
<?php if ( current_user_can('publish_posts') ) : ?>
	<input name="publish" type="submit" id="publish" tabindex="5" accesskey="p" value="<?php _e('Publish') ?>" /> 
<?php endif; ?>
<?php
}
?>
<input name="referredby" type="hidden" id="referredby" value="<?php 
if ( !empty($_REQUEST['popupurl']) )
	echo wp_specialchars($_REQUEST['popupurl']);
else if ( url_to_postid(wp_get_referer()) == $post_ID )
	echo 'redo';
else
	echo wp_specialchars(wp_get_referer());
?>" /></p>

<?php do_action('edit_form_advanced'); ?>

<?php
if (current_user_can('upload_files')) {
	$uploading_iframe_ID = (0 == $post_ID ? $temp_ID : $post_ID);
	$uploading_iframe_src = wp_nonce_url("upload.php?style=inline&amp;tab=upload&amp;post_id=$uploading_iframe_ID", 'inlineuploading');
	$uploading_iframe_src = apply_filters('uploading_iframe_src', $uploading_iframe_src);
	if ( false != $uploading_iframe_src )
		echo '<iframe id="uploading" frameborder="0" src="' . $uploading_iframe_src . '">' . __('This feature requires iframe support.') . '</iframe>';
}
?>

<div id="advancedstuff" class="dbx-group" >

<div class="dbx-box-wrapper">
<fieldset id="postexcerpt" class="dbx-box">
<div class="dbx-handle-wrapper">
<h3 class="dbx-handle"><?php _e('Optional Excerpt') ?></h3>
</div>
<div class="dbx-content-wrapper">
<div class="dbx-content"><textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt"><?php echo $post->post_excerpt ?></textarea></div>
</div>
</fieldset>
</div>

<div class="dbx-box-wrapper">
<fieldset id="trackbacksdiv" class="dbx-box">
<div class="dbx-handle-wrapper">
<h3 class="dbx-handle"><?php _e('Trackbacks') ?></h3>
</div>
<div class="dbx-content-wrapper">
<div class="dbx-content"><?php _e('Send trackbacks to'); ?>: <?php echo $form_trackback; ?> (<?php _e('Separate multiple URIs with spaces'); ?>)
<?php
if ( ! empty($pings) )
	echo $pings;
?>
</div>
</div>
</fieldset>
</div>

<div class="dbx-box-wrapper">
<fieldset id="postcustom" class="dbx-box">
<div class="dbx-handle-wrapper">
<h3 class="dbx-handle"><?php _e('Custom Fields') ?></h3>
</div>
<div class="dbx-content-wrapper">
<div id="postcustomstuff" class="dbx-content">
<table cellpadding="3">
<?php
$metadata = has_meta($post_ID);
list_meta($metadata);
?>

</table>
<?php
	meta_form();
?>
<div id="ajax-response"></div>
</div>
</div>
</fieldset>
</div>

<?php do_action('dbx_post_advanced'); ?>

</div>

<?php if ('edit' == $action) : $delete_nonce = wp_create_nonce( 'delete-post_' . $post_ID ); ?>
<input name="deletepost" class="button delete" type="submit" id="deletepost" tabindex="10" value="<?php _e('Delete this post') ?>" <?php echo "onclick=\"if ( confirm('" . js_escape(sprintf(__("You are about to delete this post \'%s\'\\n  \'Cancel\' to stop, \'OK\' to delete."), $post->post_title )) . "') ) { document.forms.post._wpnonce.value = '$delete_nonce'; return true;}return false;\""; ?> />
<?php endif; ?>

</div>

</div>

</form>
