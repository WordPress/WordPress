
<div class="wrap">
<h2 id="write-post"><?php _e('Write Page'); ?><?php if ( 0 != $post_ID ) : ?>
<small class="quickjump"><a href="#preview-post"><?php _e('preview &darr;'); ?></a></small><?php endif; ?></h2>
<?php
if (0 == $post_ID) {
	$form_action = 'post';
	$nonce_action = 'add-page';
	$temp_ID = -1 * time();
	$form_extra = "<input type='hidden' id='post_ID' name='temp_ID' value='$temp_ID' />";
} else {
	$form_action = 'editpost';
	$nonce_action = 'update-page_' . $post_ID;
	$form_extra = "<input type='hidden' id='post_ID' name='post_ID' value='$post_ID' />";
}

$sendto = wp_get_referer();

if ( 0 != $post_ID && $sendto == get_permalink($post_ID) )
 	$sendto = 'redo';
$sendto = wp_specialchars( $sendto );

?>

<form name="post" action="page.php" method="post" id="post">

<?php
wp_nonce_field($nonce_action);

if (isset($mode) && 'bookmarklet' == $mode) {
    echo '<input type="hidden" name="mode" value="bookmarklet" />';
}
?>
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action ?>' />
<?php echo $form_extra ?>
<input type="hidden" name="post_type" value="page" />

<script type="text/javascript">
// <![CDATA[
function focusit() { // focus on first input field
	document.post.title.focus();
}
addLoadEvent(focusit);
// ]]>
</script>
<div id="poststuff">

<div id="moremeta">
<div id="grabit" class="dbx-group">
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

<fieldset class="dbx-box">
<h3 class="dbx-handle"><?php _e('Page Status') ?></h3> 
<div class="dbx-content"><?php if ( current_user_can('publish_pages') ) : ?>
<label for="post_status_publish" class="selectit"><input id="post_status_publish" name="post_status" type="radio" value="publish" <?php checked($post->post_status, 'publish'); checked($post->post_status, 'future'); ?> /> <?php _e('Published') ?></label>
<?php endif; ?>
	  <label for="post_status_draft" class="selectit"><input id="post_status_draft" name="post_status" type="radio" value="draft" <?php checked($post->post_status, 'draft'); ?> /> <?php _e('Draft') ?></label>
	  <label for="post_status_private" class="selectit"><input id="post_status_private" name="post_status" type="radio" value="private" <?php checked($post->post_status, 'private'); ?> /> <?php _e('Private') ?></label></div>
</fieldset>

<fieldset id="passworddiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Page Password') ?></h3> 
<div class="dbx-content"><input name="post_password" type="text" size="13" id="post_password" value="<?php echo $post->post_password ?>" /></div>
</fieldset>

<fieldset id="pageparent" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Page Parent') ?></h3> 
<div class="dbx-content"><p><select name="parent_id">
<option value='0'><?php _e('Main Page (no parent)'); ?></option>
<?php parent_dropdown($post->post_parent); ?>
</select></p>
</div>
</fieldset>

<?php if ( 0 != count( get_page_templates() ) ) { ?>
<fieldset id="pagetemplate" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Page Template:') ?></h3> 
<div class="dbx-content"><p><select name="page_template">
		<option value='default'><?php _e('Default Template'); ?></option>
		<?php page_template_dropdown($post->page_template); ?>
		</select></p>
</div>
</fieldset>
<?php } ?>

<fieldset id="slugdiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Page slug') ?></h3> 
<div class="dbx-content"><input name="post_name" type="text" size="13" id="post_name" value="<?php echo $post->post_name ?>" /></div>
</fieldset>

<?php if ( $authors = get_editable_authors( $current_user->id ) ) : // TODO: ROLE SYSTEM ?>
<fieldset id="authordiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Page author'); ?>:</h3>
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

<fieldset id="pageorder" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Page Order') ?></h3> 
<div class="dbx-content"><p><input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo $post->menu_order ?>" /></p></div>
</fieldset>

<?php do_action('dbx_page_sidebar'); ?>

</div>
</div>

<fieldset id="titlediv">
  <legend><?php _e('Page Title') ?></legend> 
  <div><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $post->post_title; ?>" id="title" /></div>
</fieldset>


<fieldset id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>">
    <legend><?php _e('Page Content') ?></legend>
	<?php the_editor($post->post_content); ?>
</fieldset>

<p class="submit">
<input name="save" type="submit" id="save" tabindex="3" value="<?php _e('Save and Continue Editing'); ?>" />
<input type="submit" name="submit" value="<?php _e('Save') ?>" style="font-weight: bold;" tabindex="4" /> 
<?php 
if ('publish' != $post->post_status || 0 == $post_ID):
?>
<?php if ( current_user_can('publish_pages') ) : ?>
	<input name="publish" type="submit" id="publish" tabindex="5" accesskey="p" value="<?php _e('Publish') ?>" /> 
<?php endif; endif;?>
<input name="referredby" type="hidden" id="referredby" value="<?php echo $sendto; ?>" />
</p>

<?php do_action('edit_page_form'); ?>

<?php
if (current_user_can('upload_files')) {
	$uploading_iframe_ID = (0 == $post_ID ? $temp_ID : $post_ID);
	$uploading_iframe_src = wp_nonce_url("inline-uploading.php?action=view&amp;post=$uploading_iframe_ID", 'inlineuploading');
	$uploading_iframe_src = apply_filters('uploading_iframe_src', $uploading_iframe_src);
	if ( false != $uploading_iframe_src )
		echo '<iframe id="uploading" border="0" src="' . $uploading_iframe_src . '">' . __('This feature requires iframe support.') . '</iframe>';
}
?>

<div id="advancedstuff" class="dbx-group">

<fieldset id="postcustom" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Custom Fields') ?></h3>
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
</div>
</fieldset>

<?php do_action('dbx_page_advanced'); ?>

</div>

<?php if ('edit' == $action) :
	$delete_nonce = wp_create_nonce( 'delete-page_' . $post_ID ); 
	if ( current_user_can('delete_page', $post->ID) ) ?>
		<input name="deletepost" class="button" type="submit" id="deletepost" tabindex="10" value="<?php _e('Delete this page') ?>" <?php echo "onclick=\"if ( confirm('" . sprintf(__("You are about to delete this page \'%s\'\\n  \'Cancel\' to stop, \'OK\' to delete."), js_escape($post->post_title) ) . "') ) { document.forms.post._wpnonce.value = '$delete_nonce'; return true;}return false;\""; ?> />
<?php endif; ?>
</form>

</div>

</div>
