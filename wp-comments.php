<?php // Do not delete these lines
	if ('wp-comments.php' == basename($HTTP_SERVER_VARS['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');
	if (($withcomments) or ($single)) {

        if (!empty($post->post_password)) { // if there's a password
            if ($HTTP_COOKIE_VARS['wp-postpass_'.$cookiehash] != $post->post_password) {  // and it doesn't match the cookie
?>
<p><?php $lang->str('comments_password'); ?><p>
<?php
				return;
            }
        }

 		$comment_author = (isset($HTTP_COOKIE_VARS['comment_author_'.$cookiehash])) ? trim($HTTP_COOKIE_VARS['comment_author_'.$cookiehash]) : '';
        $comment_author_email = (isset($HTTP_COOKIE_VARS['comment_author_email_'.$cookiehash])) ? trim($HTTP_COOKIE_VARS['comment_author_email_'.$cookiehash]) : '';
 		$comment_author_url = (isset($HTTP_COOKIE_VARS['comment_author_url_'.$cookiehash])) ? trim($HTTP_COOKIE_VARS['comment_author_url_'.$cookiehash]) : '';

        $comments = $wpdb->get_results("SELECT * FROM $tablecomments WHERE comment_post_ID = '$id' AND comment_approved = '1' ORDER BY comment_date");
?>

<!-- You can start editing here. -->

<h2 id="comments"><?php comments_number($lang->str('comments','',1)); ?> 
<?php if ('open' == $post->comment_status) { ?>
<a href="#postcomment" title="<?php $lang->str('leave_a_comment'); ?>">&raquo;</a>
<?php } ?>
</h2>
<?php if ('open' == $post->ping_status) { ?>
<p><?php $lang->str('trackback_uri'); ?> <em><?php trackback_url() ?></em></p>
<?php } ?>


<?php if ($comments) { ?>
<ol id="commentlist">
<?php foreach ($comments as $comment) { ?>
	<li id="comment-<?php comment_ID() ?>">
	<?php comment_text() ?>
	<p><cite><?php comment_type(); ?> <?php $lang->str('by'); ?> <?php comment_author_link() ?> &#8212; <?php comment_date() ?> @ <a href="#comment-<?php comment_ID() ?>"><?php comment_time() ?></a></cite> <?php edit_comment_link($lang->str('edit_this', '', 1), ' |'); ?></p>
	</li>

<?php } // end for each comment ?>
</ol>
<?php } else { // this is displayed if there are no comments so far ?>
	<p><?php $lang->str('no_comments'); ?></p>
<?php } ?>
<p><?php comments_rss_link($lang->str('comments_rss_post', '', 1)); ?></p>
<h2 id="postcomment"><?php $lang->str('leave_a_comment'); ?></h2>
<?php if ('open' == $post->comment_status) { ?>
<p><?php $lang->str('comments_instructions'); ?> <code><?php echo allowed_tags(); ?></code></p>

<form action="<?php echo get_settings('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
	<p>
	  <input type="text" name="author" id="author" class="textarea" value="<?php echo $comment_author; ?>" size="28" tabindex="1" />
	   <label for="author"><?php $lang->str('comment_name'); ?></label>
	<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
	<input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" />
	</p>

	<p>
	  <input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="28" tabindex="2" />
	   <label for="email"><?php $lang->str('comment_email'); ?></label>
	</p>

	<p>
	  <input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="28" tabindex="3" />
	   <label for="url"><?php $lang->str('comment_uri'); ?></label>
	</p>

	<p>
	  <label for="comment"><?php $lang->str('comment_body'); ?></label>
	<br />
	  <textarea name="comment" id="comment" cols="70" rows="4" tabindex="4"></textarea>
	</p>

	<p>
	  <input name="submit" type="submit" tabindex="5" value="<?php $lang->str('say_it'); ?>" />
	</p>
</form>
<?php } else { // comments are closed ?>
<p><?php $lang->str('comments_closed'); ?></p>
<?php } ?>

<?php // if you delete this the sky will fall on your head
}
?>
