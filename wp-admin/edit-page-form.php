
<div class="wrap">
<h2><?php _e('Write Page'); ?></h2>
<?php
if (0 == $post_ID) {
	$form_action = 'post';
	$form_extra = '';
} else {
	$form_action = 'editpost';
	$form_extra = "<input type='hidden' name='post_ID' value='$post_ID' />";
}

$sendto = $_SERVER['HTTP_REFERER'];

if ( 0 != $post_ID && $sendto == get_permalink($post_ID) )
 	$sendto = 'redo';
$sendto = wp_specialchars( $sendto );

?>

<form name="post" action="post.php" method="post" id="post">

<?php
if (isset($mode) && 'bookmarklet' == $mode) {
    echo '<input type="hidden" name="mode" value="bookmarklet" />';
}
?>
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action ?>' />
<?php echo $form_extra ?>
<input type="hidden" name="post_status" value="static" />

<script type="text/javascript">
<!--
function focusit() { // focus on first input field
	document.post.title.focus();
}
addLoadEvent(focusit);
//-->
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

<fieldset id="passworddiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Password-Protect Post') ?></h3> 
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
<fieldset id="pageparent" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Page Template:') ?></h3> 
<div class="dbx-content"><p><select name="page_template">
		<option value='default'><?php _e('Default Template'); ?></option>
		<?php page_template_dropdown($post->page_template); ?>
		</select></p>
</div>
</fieldset>
<?php } ?>

<fieldset id="slugdiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Post slug') ?></h3> 
<div class="dbx-content"><input name="post_name" type="text" size="13" id="post_name" value="<?php echo $post->post_name ?>" /></div>
</fieldset>

<?php if ( $authors = get_editable_authors( $current_user->ID ) ) : // TODO: ROLE SYSTEM ?>
<fieldset id="authordiv" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Post author'); ?>:</h3>
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

</div>
</div>

<fieldset id="titlediv">
  <legend><?php _e('Page Title') ?></legend> 
  <div><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $post->post_title; ?>" id="title" /></div>
</fieldset>


<fieldset id="postdiv">
    <legend><?php _e('Page Content') ?></legend>
<?php
 $rows = get_settings('default_post_edit_rows');
 if (($rows < 3) || ($rows > 100)) {
     $rows = 10;
 }
?>
<div><textarea title="true" rows="<?php echo $rows; ?>" cols="40" name="content" tabindex="4" id="content"><?php echo $post->post_content ?></textarea></div>
</fieldset>

<?php if ( 'true' != get_user_option('rich_editing') ) : ?>
<?php the_quicktags(); ?>
<script type="text/javascript">
<!--
edCanvas = document.getElementById('content');
//-->
</script>
<?php endif; ?>

<p class="submit">
<?php if ( $post_ID ) : ?>
<input name="save" type="submit" id="save" tabindex="5" value=" <?php _e('Save and Continue Editing'); ?> "/> 
<input name="savepage" type="submit" id="savepage" tabindex="6" value="<?php $post_ID ? _e('Edit Page') : _e('Create New Page') ?> &raquo;" /> 
<?php else : ?>
<input name="savepage" type="submit" id="savepage" tabindex="6" value="<?php _e('Create New Page') ?> &raquo;" /> 
<?php endif; ?>
<input name="referredby" type="hidden" id="referredby" value="<?php echo $sendto; ?>" />
</p>


<div id="advancedstuff" class="dbx-group">

<fieldset id="postcustom" class="dbx-box">
<h3 class="dbx-handle"><?php _e('Custom Fields') ?></h3>
<div id="postcustomstuff" class="dbx-content">
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
</div>
</fieldset>

</div>

<?php if ('edit' == $action) : ?>
		<input name="deletepost" class="delete" type="submit" id="deletepost" tabindex="10" value="<?php _e('Delete this page') ?>" <?php echo "onclick=\"return confirm('" . sprintf(__("You are about to delete this page \'%s\'\\n  \'Cancel\' to stop, \'OK\' to delete."), $wpdb->escape($post->post_title) ) . "')\""; ?> />
<?php endif; ?>

<?php do_action('edit_page_form', ''); ?>
</form>

</div>

</div>