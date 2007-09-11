<?php
$submitbutton_text = __('Edit Comment &raquo;');
$toprow_title = sprintf(__('Editing Comment # %s'), $comment->comment_ID);
$form_action = 'editedcomment';
$form_extra = "' />\n<input type='hidden' name='comment_ID' value='" . $comment->comment_ID . "' />\n<input type='hidden' name='comment_post_ID' value='" . $comment->comment_post_ID;
?>

<form name="post" action="comment.php" method="post" id="post">
<h2><?php echo $toprow_title; ?></h2>
<?php wp_nonce_field('update-comment_' . $comment->comment_ID) ?>
<div class="wrap">
<input type="hidden" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />

<script type="text/javascript">
function focusit() { // focus on first input field
	document.post.name.focus();
}
addLoadEvent(focusit);
</script>
<fieldset id="namediv">
    <legend><label for="name"><?php _e('Name:') ?></label></legend>
	<div>
	  <input type="text" name="newcomment_author" size="25" value="<?php echo attribute_escape( $comment->comment_author ); ?>" tabindex="1" id="name" />
    </div>
</fieldset>
<fieldset id="emaildiv">
        <legend><label for="email"><?php _e('E-mail:') ?></label></legend>
		<div>
		  <input type="text" name="newcomment_author_email" size="20" value="<?php echo attribute_escape( $comment->comment_author_email ); ?>" tabindex="2" id="email" />
    </div>
</fieldset>
<fieldset id="uridiv">
        <legend><label for="newcomment_author_url"><?php _e('URL:') ?></label></legend>
		<div>
		  <input type="text" id="newcomment_author_url" name="newcomment_author_url" size="35" value="<?php echo attribute_escape( $comment->comment_author_url ); ?>" tabindex="2" />
    </div>
</fieldset>

<fieldset style="clear: both;">
        <legend><?php _e('Comment') ?></legend>
	<?php the_editor($comment->comment_content, 'content', 'newcomment_author_url'); ?>
</fieldset>

<p class="submit"><input type="submit" name="editcomment" id="editcomment" value="<?php echo $submitbutton_text ?>" style="font-weight: bold;" tabindex="6" />
  <input name="referredby" type="hidden" id="referredby" value="<?php echo wp_get_referer(); ?>" />
</p>

</div>

<div class="wrap">
<h2><?php _e('Advanced'); ?></h2>

<table width="100%" cellspacing="2" cellpadding="5" class="editform">
	<tr>
		<th scope="row" valign="top"><?php _e('Comment Status') ?>:</th>
		<td><label for="comment_status_approved" class="selectit"><input id="comment_status_approved" name="comment_status" type="radio" value="1" <?php checked($comment->comment_approved, '1'); ?> tabindex="4" /> <?php _e('Approved') ?></label> &nbsp;
		<label for="comment_status_moderated" class="selectit"><input id="comment_status_moderated" name="comment_status" type="radio" value="0" <?php checked($comment->comment_approved, '0'); ?> tabindex="4" /> <?php _e('Moderated') ?></label> &nbsp;
		<label for="comment_status_spam" class="selectit"><input id="comment_status_spam" name="comment_status" type="radio" value="spam" <?php checked($comment->comment_approved, 'spam'); ?> tabindex="4" /> <?php _e('Spam') ?></label></td>
	</tr>

<?php if ( current_user_can('edit_posts') ) : ?>
	<tr>
		<th scope="row" valign="top"><?php _e('Edit time'); ?>:</th>
		<td><?php touch_time(('editcomment' == $action), 0, 5); ?> </td>
	</tr>
<?php endif; ?>

	<tr>
		<th scope="row" valign="top">&nbsp;</th>
		<td><input name="deletecomment" class="button delete" type="submit" id="deletecomment" tabindex="10" value="<?php _e('Delete this comment') ?>" <?php echo "onclick=\"if ( confirm('" . js_escape(__("You are about to delete this comment. \n  'Cancel' to stop, 'OK' to delete.")) . "') ) { document.forms.post._wpnonce.value = '" . wp_create_nonce( 'delete-comment_' . $comment->comment_ID ) . "'; return true; } return false;\""; ?> />
		<input type="hidden" name="c" value="<?php echo $comment->comment_ID ?>" />
		<input type="hidden" name="p" value="<?php echo $comment->comment_post_ID ?>" />
		<input type="hidden" name="noredir" value="1" />
	</td>
	</tr>
</table>

</div>

</form>
