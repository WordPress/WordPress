<?php

$submitbutton_text = __('Edit Comment &raquo;');
$toprow_title = sprintf(__('Editing Comment # %s'), $commentdata['comment_ID']);
$form_action = 'editedcomment';
$form_extra = "' />\n<input type='hidden' name='comment_ID' value='$comment' />\n<input type='hidden' name='comment_post_ID' value='".$commentdata["comment_post_ID"];
?>
<div class="wrap">

<form name="post" action="post.php" method="post" id="post">
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />

<script type="text/javascript">
function focusit() {
	// focus on first input field
	document.post.name.focus();
}
window.onload = focusit;
</script>
<fieldset id="namediv">
    <legend><?php _e('Name:') ?></legend>
	<div>
	  <input type="text" name="newcomment_author" size="22" value="<?php echo format_to_edit($commentdata['comment_author']) ?>" tabindex="1" id="name" />
    </div>
</fieldset>
<fieldset id="emaildiv">
        <legend><?php _e('E-mail:') ?></legend>
		<div>
		  <input type="text" name="newcomment_author_email" size="30" value="<?php echo format_to_edit($commentdata['comment_author_email']) ?>" tabindex="2" id="email" />
    </div>
</fieldset>
<fieldset id="uridiv">
        <legend><?php _e('URI:') ?></legend>
		<div>
		  <input type="text" name="newcomment_author_url" size="35" value="<?php echo format_to_edit($commentdata['comment_author_url']) ?>" tabindex="3" id="URL" />
    </div>
</fieldset>

<fieldset style="clear: both;">
        <legend><?php _e('Comment') ?></legend>
<?php the_quicktags(); ?>
<?php
 $rows = get_settings('default_post_edit_rows');
 if (($rows < 3) || ($rows > 100)) {
     $rows = 10;
 }
?>
<div><textarea rows="<?php echo $rows; ?>" cols="40" name="content" tabindex="4" id="content" style="width: 99%"><?php echo $content ?></textarea></div>
</fieldset>

<script type="text/javascript">
<!--
edCanvas = document.getElementById('content');
//-->
</script>

<p class="submit"><input type="submit" name="submit" value="<?php echo $submitbutton_text ?>" style="font-weight: bold;" tabindex="6" />
  <input name="referredby" type="hidden" id="referredby" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
</p>


<?php


// if the level is 5+, allow user to edit the timestamp - not on 'new post' screen though
// if (($user_level > 4) && ($action != "post"))
if ($user_level > 4) {
	touch_time(($action == 'edit'));
}
?>

</form>
<p><a class="delete" href="post.php?action=confirmdeletecomment&amp;noredir=true&amp;comment=<?php echo $commentdata['comment_ID']; ?>&amp;p=<?php echo $commentdata['comment_post_ID']; ?>"><?php _e('Delete comment') ?></a></p>
</div>
