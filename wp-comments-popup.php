<?php 
/* Don't remove these lines. */
$blog = 1;
require ('wp-blog-header.php');
add_filter('comment_text', 'popuplinks');
foreach ($posts as $post) { start_wp();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo get_settings('blogname'); ?> - <?php $lang->str('comments_on',the_title('','',false)); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>" />
	<style type="text/css" media="screen">
		@import url( wp-layout.css );
		body { margin: 3px; }
	</style>

</head>
<body id="commentspopup">

<h1 id="header"><a href="" title="<?php echo get_settings('blogname'); ?>"><?php echo get_settings('blogname'); ?></a></h1>

<h2 id="comments"><?php $lang->str('comments'); ?></h2>

<p><a href="<?php echo get_settings('siteurl'); ?>/wp-commentsrss2.php?p=<?php echo $post->ID; ?>"><?php $lang->str('comments_rss_post'); ?></a></p>

<?php if ('open' == $post->ping_status) { ?>
<p><?php $lang->str('trackback_uri'); ?> <em><?php trackback_url() ?></em></p>
<?php } ?>

<?php
// this line is WordPress' motor, do not delete it.
$comment_author = (isset($HTTP_COOKIE_VARS['comment_author_'.$cookiehash])) ? trim($HTTP_COOKIE_VARS['comment_author_'.$cookiehash]) : '';
$comment_author_email = (isset($HTTP_COOKIE_VARS['comment_author_email_'.$cookiehash])) ? trim($HTTP_COOKIE_VARS['comment_author_email_'.$cookiehash]) : '';
$comment_author_url = (isset($HTTP_COOKIE_VARS['comment_author_url_'.$cookiehash])) ? trim($HTTP_COOKIE_VARS['comment_author_url_'.$cookiehash]) : '';
$comments = $wpdb->get_results("SELECT * FROM $tablecomments WHERE comment_post_ID = $id AND comment_approved = '1' ORDER BY comment_date");
$commentstatus = $wpdb->get_row("SELECT comment_status, post_password FROM $tableposts WHERE ID = $id");
if (!empty($commentstatus->post_password) && $HTTP_COOKIE_VARS['wp-postpass_'.$cookiehash] != $commentstatus->post_password) {  // and it doesn't match the cookie
	echo(get_the_password_form());
} else { ?>

<?php if ($comments) { ?>
<ol id="commentlist">
<?php foreach ($comments as $comment) { ?>
	<li id="comment-<?php comment_ID() ?>">
	<?php comment_text() ?>
	<p><cite><?php comment_type(); ?> <?php $lang->str('by'); ?> <?php comment_author_link() ?> &#8212; <?php comment_date() ?> @ <a href="#comment-<?php comment_ID() ?>"><?php comment_time() ?></a></cite></p>
	</li>

<?php } // end for each comment ?>
</ol>
<?php } else { // this is displayed if there are no comments so far ?>
	<p><?php $lang->str('no_comments'); ?></p>
<?php } ?>

<?php if ('open' == $commentstatus->comment_status) { ?>
<h2><?php $lang->str('leave_a_comment'); ?></h2>
<p><?php $lang->str('comment_instructions'); ?> <code><?php echo allowed_tags(); ?></code></p>

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
<?php }
} // end password check
?>

<div><strong><a href="javascript:window.close()"><?php $lang->str('close_window'); ?></a></strong></div>

<?php // if you delete this the sky will fall on your head
}
?>

<!-- // this is just the end of the motor - don't touch that line either :) -->
<?php //} ?> 
<p class="credit"><?php timer_stop(1); ?> <?php $lang->str('powered_by_wordpress',$lang->str('powered_by_title','',1)); ?></p>
<?php // Seen at http://www.mijnkopthee.nl/log2/archive/2003/05/28/esc(18) ?>
<script type="text/javascript">
<!--
document.onkeypress = function esc(e) {	
	if(typeof(e) == "undefined") { e=event; }
	if (e.keyCode == 27) { self.close(); }
}
// -->
</script>
</body>
</html>
