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
<h2><?php echo $toprow_title; ?></h2>

<div id="previewview">
<a class="button" href="<?php echo get_comment_link(); ?>" target="_blank"><?php _e('View this Comment'); ?></a>
</div>
<div id="poststuff">
<input type="hidden" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />
<?php
// All meta boxes should be defined and added before the first do_meta_boxes() call (or potentially during the do_meta_boxes action).

function comment_submit_meta_box($comment) {
?>
<div class="submitbox" id="submitcomment">
<div class="inside-submitbox">

<p><strong><label for='comment_status'><?php _e('Approval Status') ?></label></strong></p>
<p>
<select name='comment_status' id='comment_status'>
<option<?php selected( $comment->comment_approved, '1' ); ?> value='1'><?php _e('Approved') ?></option>
<option<?php selected( $comment->comment_approved, '0' ); ?> value='0'><?php _e('Moderated') ?></option>
<option<?php selected( $comment->comment_approved, 'spam' ); ?> value='spam'><?php _e('Spam') ?></option>
</select>
</p>

<?php
$stamp = __('%1$s at %2$s');
$date = mysql2date(get_option('date_format'), $comment->comment_date);
$time = mysql2date(get_option('time_format'), $comment->comment_date);
?>
<p class="curtime"><?php printf($stamp, $date, $time); ?>
&nbsp;<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js"><?php _e('Edit') ?></a></p>

<div id='timestampdiv' class='hide-if-js'><?php touch_time(('editcomment' == $action), 0, 5); ?></div>

</div>

<p class="submit">
<input type="submit" name="save" value="<?php _e('Save'); ?>" tabindex="4" class="button button-highlighted" />
<?php
echo "<a class='submitdelete' href='" . wp_nonce_url("comment.php?action=deletecomment&amp;c=$comment->comment_ID&amp;_wp_original_http_referer=" . wp_get_referer(), 'delete-comment_' . $comment->comment_ID) . "' onclick=\"if ( confirm('" . js_escape(__("You are about to delete this comment. \n  'Cancel' to stop, 'OK' to delete.")) . "') ) { return true;}return false;\">" . __('Delete comment') . "</a>";
?>
</p>
</div>
<?php
}
add_meta_box('submitdiv', __('Save'), 'comment_submit_meta_box', 'comment', 'side', 'core');
?>

<div id="side-info-column" class="inner-sidebar">

<?php $side_meta_boxes = do_meta_boxes('comment', 'side', $comment); ?>

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
<input type="text" name="newcomment_author_email" size="30" value="<?php echo attribute_escape( $comment->comment_author_email ); ?>" tabindex="2" id="email" />
</div>
</div>

<div id="uridiv" class="stuffbox">
<h3><label for="newcomment_author_url"><?php _e('URL') ?></label></h3>
<div class="inside">
<input type="text" id="newcomment_author_url" name="newcomment_author_url" size="30" value="<?php echo attribute_escape( $comment->comment_author_url ); ?>" tabindex="3" />
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
