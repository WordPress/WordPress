<?php
$submitbutton_text = __('Edit Comment');
$toprow_title = sprintf(__('Editing Comment # %s'), $comment->comment_ID);
$form_action = 'editedcomment';
$form_extra = "' />\n<input type='hidden' name='comment_ID' value='" . $comment->comment_ID . "' />\n<input type='hidden' name='comment_post_ID' value='" . $comment->comment_post_ID;
?>

<form name="post" action="comment.php" method="post" id="post">
<?php wp_nonce_field('update-comment_' . $comment->comment_ID) ?>
<div class="wrap">
<h2><?php echo $toprow_title; ?></h2>
<br />
<input type="hidden" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />

<script type="text/javascript">
function focusit() { // focus on first input field
	document.post.name.focus();
}
addLoadEvent(focusit);
</script>

<div id="poststuff">

<div id="namediv" class="stuffbox">
<h3><?php _e('Name') ?></h3>
<div class="inside">
<input type="text" name="newcomment_author" size="30" value="<?php echo attribute_escape( $comment->comment_author ); ?>" tabindex="1" id="name" />
</div>
</div>

<div id="emaildiv" class="stuffbox">
<h3><?php _e('E-mail') ?></h3>
<div class="inside">
<input type="text" name="newcomment_author_email" size="30" value="<?php echo attribute_escape( $comment->comment_author_email ); ?>" tabindex="2" id="email" />
</div>
</div>

<div id="uridiv" class="stuffbox">
<h3><?php _e('URL') ?></h3>
<div class="inside">
<input type="text" id="newcomment_author_url" name="newcomment_author_url" size="30" value="<?php echo attribute_escape( $comment->comment_author_url ); ?>" tabindex="2" />
</div>
</div>

<div id="postdiv" class="postarea">
<h3><?php _e('Comment') ?></h3>
<?php the_editor($comment->comment_content, 'content', 'newcomment_author_url', false); ?>
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
</div>

<div class="submitbox" id="submitcomment">

<div id="previewview">
<a href="<?php echo get_comment_link(); ?>" target="_blank"><?php _e('View this Comment'); ?></a>
</div>

<div class="inside">

<p><strong><?php _e('Approval Status') ?></strong></p>
<p>
<select name='comment_status'>
<option<?php selected( $comment->comment_approved, '1' ); ?> value='1'><?php _e('Approved') ?></option>
<option<?php selected( $comment->comment_approved, '0' ); ?> value='0'><?php _e('Moderated') ?></option>
<option<?php selected( $comment->comment_approved, 'spam' ); ?> value='spam'><?php _e('Spam') ?></option>
</select>
</p>

<?php
$stamp = __('Timestamp:<br />%1$s at %2$s');
$date = mysql2date(get_option('date_format'), $comment->comment_date);
$time = mysql2date(get_option('time_format'), $comment->comment_date);
?>
<p><?php printf($stamp, $date, $time); ?>
&nbsp;<a href="#edit_timestamp" class="edit-timestamp"><?php _e('Edit') ?></a></p>

<div id='timestamp'><?php touch_time(('editcomment' == $action), 0, 5); ?></div>

</div>

<p class="submit">
<input type="submit" name="save" value="<?php _e('Save'); ?>" style="font-weight: bold;" tabindex="4" />
<?php
echo "<a href='" . wp_nonce_url("comment.php?action=deletecomment&amp;c=$comment->comment_ID", 'delete-comment_' . $comment->comment_ID) . "' onclick=\"if ( confirm('" . js_escape(__("You are about to delete this comment. \n  'Cancel' to stop, 'OK' to delete.")) . "') ) { return true;}return false;\">" . __('Delete comment') . "</a>";
?>
</p>
<?php do_action('submitcomment_box'); ?>
</div>

<?php do_meta_boxes('comment', 'normal', $comment); ?>

<input type="hidden" name="c" value="<?php echo $comment->comment_ID ?>" />
<input type="hidden" name="p" value="<?php echo $comment->comment_post_ID ?>" />
<input name="referredby" type="hidden" id="referredby" value="<?php echo wp_get_referer(); ?>" />
<input type="hidden" name="noredir" value="1" />
</div>
</div>

</form>
