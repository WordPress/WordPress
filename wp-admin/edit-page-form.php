
<div class="wrap">
<h2><?php _e('New Page'); ?></h2>
<?php
if (0 == $post_ID) {
	$form_action = 'post';
	$form_extra = '';
} else {
	$form_action = 'editpost';
	$form_extra = "<input type='hidden' name='post_ID' value='$post_ID' />";
}
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
function focusit() {
	// focus on first input field
	document.post.title.focus();
}
window.onload = focusit;
//-->
</script>
    <fieldset id="titlediv">
      <legend><?php _e('Page Title') ?></legend> 
	  <div><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $edited_post_title; ?>" id="title" /></div>
    </fieldset>
<fieldset id="commentstatusdiv">
      <legend><?php _e('Discussion') ?></legend> 
	  <div><label for="comment_status" class="selectit">
	      <input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($comment_status, 'open'); ?> />
         <?php _e('Allow Comments') ?></label> 
		 <label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($ping_status, 'open'); ?> /> <?php _e('Allow Pings') ?></label>
	</div>
</fieldset>
    <fieldset id="postpassworddiv">
      <legend><?php _e('Page Password') ?></legend> 
	  <div><input name="post_password" type="text" size="13" id="post_password" value="<?php echo $post_password ?>" /></div>
    </fieldset>
    <fieldset id="pageparent">
      <legend><?php _e('Page Parent') ?></legend> 
	  <div><select name="parent_id">
	  <option value='0'><?php _e('Main Page (no parent)'); ?></option>
			<?php parent_dropdown($post_parent); ?>
        </select>
	  </div>
    </fieldset>
<fieldset id="postdiv">
    <legend><?php _e('Page Content') ?></legend>
<?php the_quicktags(); ?>
<?php
 $rows = get_settings('default_post_edit_rows');
 if (($rows < 3) || ($rows > 100)) {
     $rows = 10;
 }
?>
<div><textarea rows="<?php echo $rows; ?>" cols="40" name="content" tabindex="4" id="content"><?php echo $content ?></textarea></div>
</fieldset>


<script type="text/javascript">
<!--
edCanvas = document.getElementById('content');
//-->
</script>

<p class="submit">
  <input name="savepage" type="submit" id="savepage" tabindex="6" value="<?php $post_ID ? _e('Edit Page') :_e('Create New Page') ?> &raquo;" /> 
  <input name="referredby" type="hidden" id="referredby" value="<?php if (isset($_SERVER['HTTP_REFERER'])) echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>" />
</p>

<fieldset id="pageoptions">
	 <legend><?php _e('Page Options') ?></legend> 
   <table width="100%" cellspacing="2" cellpadding="5" class="editform">
	 <tr valign="top">
	 <th scope="row"><?php _e('Page Template:') ?></th>
   <td><div><select name="page_template">
	 <option value='default'><?php _e('Default Template'); ?></option>
	 <?php page_template_dropdown($page_template); ?>
	 </select>
	 </div>
	 </td>
	 </tr>
	 </table>
</fieldset>

<?php do_action('edit_page_form', ''); ?>
</form>

</div>
