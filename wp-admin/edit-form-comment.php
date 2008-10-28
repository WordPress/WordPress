<?php
/**
 * Edit comment form for inclusion in another file.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * @var string
 */
$submitbutton_text = __('Edit Comment');
$toprow_title = sprintf(__('Editing Comment # %s'), $comment->comment_ID);
$form_action = 'editedcomment';
$form_extra = "' />\n<input type='hidden' name='comment_ID' value='" . $comment->comment_ID . "' />\n<input type='hidden' name='comment_post_ID' value='" . $comment->comment_post_ID;
?>

<form name="post" action="comment.php" method="post" id="post">
<?php wp_nonce_field('update-comment_' . $comment->comment_ID) ?>
<div class="wrap">
<h2><?php _e('Edit Comment'); ?></h2>

<div id="poststuff" class="metabox-holder">
<input type="hidden" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />
<?php
// All meta boxes should be defined and added before the first do_meta_boxes() call (or potentially during the do_meta_boxes action).

/**
 * Display comment edit meta box.
 *
 * Not used, but keeping for a bit longer in case it's needed.
 *
 * @since 2.7.0
 *
 * @param object $comment Comment data.
 */
function comment_submit_meta_box($comment) {
?>
<div class="submitbox" id="submitcomment">
<div class="inside-submitbox">

<p><strong><?php _e('Approval Status') ?></strong></p>
<p id='comment-status-radio'>
<label><input type="radio"<?php checked( $comment->comment_approved, '1' ); ?> name="comment_status" value="1" /><?php _e('Approved') ?></label>
<label><input type="radio"<?php checked( $comment->comment_approved, '0' ); ?> name="comment_status" value="0" /><?php _e('Awaiting Moderation') ?></label>
<label><input type="radio"<?php checked( $comment->comment_approved, 'spam' ); ?> name="comment_status" value="spam" /><?php _e('Spam') ?></label>
</p>

<div class="insidebox" id="deletebutton">
<?php
echo "<a class='submitdelete' href='" . wp_nonce_url("comment.php?action=deletecomment&amp;c=$comment->comment_ID&amp;_wp_original_http_referer=" . wp_get_referer(), 'delete-comment_' . $comment->comment_ID) . "' onclick=\"if ( confirm('" . js_escape(__("You are about to delete this comment. \n  'Cancel' to stop, 'OK' to delete.")) . "') ) { return true;}return false;\">" . __('Delete comment') . "</a>"; ?>
</div>

<?php
$stamp = __('%1$s at %2$s');
$date = mysql2date(get_option('date_format'), $comment->comment_date);
$time = mysql2date(get_option('time_format'), $comment->comment_date);
?>
<div class="insidebox curtime"><span id="timestamp"><?php printf($stamp, $date, $time); ?></span>
&nbsp;<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" tabindex='4'><?php _e('Edit') ?></a>
<div id='timestampdiv' class='hide-if-js'><?php touch_time(('editcomment' == $action), 0, 5); ?></div></div>

</div>

<p class="submit">
<input type="submit" name="save" value="<?php _e('Save'); ?>" tabindex="4" class="button button-highlighted" />
<a class="preview button" href="<?php echo get_comment_link(); ?>" target="_blank"><?php _e('View Comment'); ?></a>
</p>
</div>
<?php
}

$email = attribute_escape( $comment->comment_author_email );
$url = attribute_escape( $comment->comment_author_url );
// add_meta_box('submitdiv', __('Save'), 'comment_submit_meta_box', 'comment', 'side', 'core');
?>

<div id="side-info-column" class="inner-sidebar">
<div id="submitdiv" class="stuffbox" >
<h3><span class='hndle'>Save</span></h3>

<div class="submitbox" id="submitcomment">
<div class="inside-submitbox">

<div class="insidebox">
<div id='comment-status-radio'>
<p><strong><?php _e('This comment is') ?></strong></p>
<label><input type="radio"<?php checked( $comment->comment_approved, '1' ); ?> name="comment_status" value="1" /><?php _e('Approved') ?></label><br />
<label><input type="radio"<?php checked( $comment->comment_approved, '0' ); ?> name="comment_status" value="0" /><?php _e('Awaiting Moderation') ?></label><br />
<label><input type="radio"<?php checked( $comment->comment_approved, 'spam' ); ?> name="comment_status" value="spam" /><?php _e('Spam') ?></label>
</div>
</div>

<div class="insidebox" id="deletebutton">
<?php
echo "<a class='submitdelete' href='" . wp_nonce_url("comment.php?action=deletecomment&amp;c=$comment->comment_ID&amp;_wp_original_http_referer=" . wp_get_referer(), 'delete-comment_' . $comment->comment_ID) . "' onclick=\"if ( confirm('" . js_escape(__("You are about to delete this comment. \n  'Cancel' to stop, 'OK' to delete.")) . "') ) { return true;}return false;\">" . __('Delete comment') . "</a>"; ?>
</div>

<?php
$stamp = __('%1$s at %2$s');
$date = mysql2date(get_option('date_format'), $comment->comment_date);
$time = mysql2date(get_option('time_format'), $comment->comment_date);
?>
<div class="insidebox curtime"><span id="timestamp"><?php printf($stamp, $date, $time); ?></span>&nbsp;<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" tabindex='4'><?php _e('Edit') ?></a>
<div id='timestampdiv' class='hide-if-js'><?php touch_time(('editcomment' == $action), 0, 5); ?></div></div>

</div>

<p class="submit">
<input type="submit" name="save" value="<?php _e('Save'); ?>" tabindex="4" class="button button-highlighted" />
<a class="preview button" href="<?php echo get_comment_link(); ?>" target="_blank"><?php _e('View Comment'); ?></a>
</p>
</div>

</div>
</div>

<div id="post-body" class="<?php echo $side_meta_boxes ? 'has-sidebar' : ''; ?>">
<div id="post-body-content" class="has-sidebar-content">

<div id="namediv" class="stuffbox">
<h3><label for="name"><?php _e('Name') ?></label></h3>
<div class="inside">
<input type="text" name="newcomment_author" size="30" value="<?php echo attribute_escape( $comment->comment_author ); ?>" tabindex="1" id="name" />
</div>
</div>

<div id="postdiv" class="postarea">
<h3><?php _e('Comment') ?></h3>
<?php the_editor($comment->comment_content, 'content', 'newcomment_author_url', false, 4); ?>
<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
</div>

<div id="emaildiv" class="stuffbox">
<h3><label for="email"><?php _e('E-mail') ?></label></h3>
<div class="inside">
<input type="text" name="newcomment_author_email" size="30" value="<?php echo $email; ?>" tabindex="2" id="email" />
<?php if ( $email )
	comment_author_email_link( __('Send Email'), '<p>', '</p>'); ?>
</div>
</div>

<div id="uridiv" class="stuffbox">
<h3><label for="newcomment_author_url"><?php _e('URL') ?></label></h3>
<div class="inside">
<input type="text" id="newcomment_author_url" name="newcomment_author_url" size="30" value="<?php echo $url; ?>" tabindex="3" />
<?php if ( ! empty( $url ) && 'http://' != $url ) {
	$url = get_comment_author_url();
	$link = "<a href='$url' rel='external nofollow' target='_blank'>" . __('Visit site') . "</a>";
	
	echo '<p>' . apply_filters('get_comment_author_link', $link) . '</p>'; 
} ?>
</div>
</div>

<?php do_meta_boxes('comment', 'normal', $comment); ?>

<input type="hidden" name="c" value="<?php echo $comment->comment_ID ?>" />
<input type="hidden" name="p" value="<?php echo $comment->comment_post_ID ?>" />
<input name="referredby" type="hidden" id="referredby" value="<?php echo wp_get_referer(); ?>" />
<?php wp_original_referer_field(true, 'previous'); ?>
<input type="hidden" name="noredir" value="1" />

</div>
</div>
</div>
</div>
</form>

<script type="text/javascript">
try{document.post.name.focus();}catch(e){}
</script>
