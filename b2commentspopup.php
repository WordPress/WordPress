<?php /* Don't remove this line, it calls the b2 function files ! */
$blog=1;
include('blog.header.php');
add_filter('comment_text', 'popuplinks');
foreach ($posts as $post) { start_b2();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $blogname ?> - Comments on "<?php the_title() ?>"</title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<style type="text/css" media="screen">
		@import url( wp-layout.css );
		body { margin: 3px; }
	</style>

</head>
<body id="commentspopup">

<h1 id="header"><a href="" title="<?php echo $blogname ?>"><?php echo $blogname ?></a></h1>

<h2>Comments</h2>

<p><a href="<?php echo $siteurl; ?>/wp-commentsrss2.php?p=<?php echo $post->ID; ?>">RSS feed for comments on this post.</a></p>

<?php if ('open' == $post->ping_status) { ?>
<p>The <acronym title="Uniform Resource Identifier">URI</acronym> to TrackBack this entry is: <em><?php trackback_url() ?></em></p>
<?php } ?>

<ol id="comments">
<?php
// this line is WordPress' motor, do not delete it.
$comments = $wpdb->get_results("SELECT * FROM $tablecomments WHERE comment_post_ID = $id ORDER BY comment_date");
$commentstatus = $wpdb->get_row("SELECT comment_status, post_password FROM $tableposts WHERE ID = $id");
if (!empty($commentstatus->post_password) && $HTTP_COOKIE_VARS['wp-postpass_'.$cookiehash] != $commentstatus->post_password) {  // and it doesn't match the cookie
	echo("<li>".get_the_password_form()."</li></ol>");
}
else {
	if ($comments) {
// this line is WordPress' motor, do not delete it.
		foreach ($comments as $comment) {
?>
<!-- comment -->
<li id="comment-<?php comment_ID() ?>">
<?php comment_text() ?>
<p><cite><?php comment_type(); ?> by <?php comment_author_link() ?> &#8212; <?php comment_date() ?> @ <a href="#comment-<?php comment_ID() ?>"><?php comment_time() ?></a></cite></p>
</li>

<?php	 	} // end for each comment
	} else { // this is displayed if there are no comments so far 
?>
	<li>No comments yet.</li>
<?php } ?>
</ol>
<?php 
	if ('open' == $commentstatus->comment_status) { ?>
<h2>Leave a Comment</h2>
<p>Line and paragraph breaks automatic, website trumps email, <acronym title="Hypertext Markup Language">HTML</acronym> allowed: <code><?php echo htmlentities(str_replace('<', ' <', $comment_allowed_tags)); ?></code></p>

<form action="<?php echo $siteurl; ?>/b2comments.post.php" method="post" id="commentform">
	<p>
	  <input type="text" name="author" id="author" class="textarea" value="<?php echo $comment_author; ?>" size="28" tabindex="1" />
	   <label for="author">Name</label>
	<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
	<input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" />
	</p>

	<p>
	  <input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="28" tabindex="2" />
	   <label for="email">Email</label>
	</p>

	<p>
	  <input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="28" tabindex="3" />
	   <label for="url"><acronym title="Uniform Resource Identifier">URI</acronym></label>
	</p>

	<p>
	  <label for="comment">Your Comment</label>
	<br />
	  <textarea name="comment" id="comment" cols="70" rows="4" tabindex="4"></textarea>
	</p>

	<p>
	  <input name="submit" type="submit" tabindex="5" value="Say it!" />
	</p>
</form>
<?php } else { // comments are closed ?>
<p>Sorry, comments are closed at this time.</p>
<?php }
} // end password check
?>

<div><strong><a href="javascript:window.close()">Close this window</a>.</strong></div>

<?php // if you delete this the sky will fall on your head
}
?>

<!-- // this is just the end of the motor - don't touch that line either :) -->
<?php //} ?> 
<p class="credit"><?php timer_stop(1); ?> <cite>Powered by <a href="http://wordpress.org"><strong>Wordpress</strong></a></cite></p>
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