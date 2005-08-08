<?php
$submitbutton_text = __('Edit Comment &raquo;');
$toprow_title = sprintf(__('Editing Comment # %s'), $comment->comment_ID);
$form_action = 'editedcomment';
$form_extra = "' />\n<input type='hidden' name='comment_ID' value='" . $comment->comment_ID . "' />\n<input type='hidden' name='comment_post_ID' value='".$comment->comment_post_ID;
?>

<form name="post" action="post.php" method="post" id="post">
<div class="wrap">
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />

<script type="text/javascript">
function focusit() { // focus on first input field
	document.post.name.focus();
}
addLoadEvent(focusit);
</script>
<fieldset id="namediv">
    <legend><?php _e('Name:') ?></legend>
	<div>
	  <input type="text" name="newcomment_author" size="22" value="<?php echo $comment->comment_author ?>" tabindex="1" id="name" />
    </div>
</fieldset>
<fieldset id="emaildiv">
        <legend><?php _e('E-mail:') ?></legend>
		<div>
		  <input type="text" name="newcomment_author_email" size="30" value="<?php echo $comment->comment_author_email ?>" tabindex="2" id="email" />
    </div>
</fieldset>
<fieldset id="uridiv">
        <legend><?php _e('URI:') ?></legend>
		<div>
		  <input type="text" name="newcomment_author_url" size="35" value="<?php echo $comment->comment_author_url ?>" tabindex="3" id="URL" />
    </div>
</fieldset>

<fieldset style="clear: both;">
        <legend><?php _e('Comment') ?></legend>
<?php if ( !get_option('rich_editing') ) : ?>
<?php the_quicktags(); ?>
<script type="text/javascript">
<!--
edCanvas = document.getElementById('content');
//-->
</script>
<?php endif; ?>
<?php
 $rows = get_settings('default_post_edit_rows');
 if (($rows < 3) || ($rows > 100)) {
     $rows = 10;
 }
?>
<div><textarea title="true" rows="<?php echo $rows; ?>" cols="40" name="content" tabindex="4" id="content" style="width: 99%"><?php echo $comment->comment_content ?></textarea></div>
</fieldset>


<p class="submit"><input type="submit" name="editcomment" id="editcomment" value="<?php echo $submitbutton_text ?>" style="font-weight: bold;" tabindex="6" />
  <input name="referredby" type="hidden" id="referredby" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
</p>

</div>

<div class="wrap">
<h2><?php _e('Advanced'); ?></h2>

<table width="100%" cellspacing="2" cellpadding="5" class="editform">
	<tr>
		<th scope="row" valign="top"><?php _e('Comment Status') ?>:</th>
		<td><label for="comment_status_approved" class="selectit"><input id="comment_status_approved" name="comment_status" type="radio" value="1" <?php checked($comment->comment_approved, '1'); ?> /> <?php _e('Approved') ?></label><br />
	  <label for="comment_status_moderated" class="selectit"><input id="comment_status_moderated" name="comment_status" type="radio" value="0" <?php checked($comment->comment_approved, '0'); ?> /> <?php _e('Moderated') ?></label><br />
	  <label for="comment_status_spam" class="selectit"><input id="comment_status_spam" name="comment_status" type="radio" value="spam" <?php checked($comment->comment_approved, 'spam'); ?> /> <?php _e('Spam') ?></label></td>
	</tr>

<?php if ( current_user_can('edit_posts') ) : ?>
	<tr>
		<th scope="row"><?php _e('Edit time'); ?>:</th>
		<td><?php touch_time(('editcomment' == $action), 0); ?></td>
	</tr>
<?php endif; ?>

	<tr>
		<th scope="row"><?php _e('Delete'); ?>:</th>
		<td><p><a class="delete" href="post.php?action=confirmdeletecomment&amp;noredir=true&amp;comment=<?php echo $comment->comment_ID; ?>&amp;p=<?php echo $comment->comment_post_ID; ?>"><?php _e('Delete comment') ?></a></p></td>
	</tr>
</table>

</div>

</form>
