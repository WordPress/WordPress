<?php // Do not delete these lines
	if ('b2comments.php' == basename($HTTP_SERVER_VARS['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');
	if (($withcomments) or ($c)) {

		$comment_author = (empty($HTTP_COOKIE_VARS["comment_author"])) ? "name" : $HTTP_COOKIE_VARS["comment_author"];
		$comment_author_email = (empty($HTTP_COOKIE_VARS["comment_author"])) ? "email" : trim($HTTP_COOKIE_VARS["comment_author_email"]);
		$comment_author_url = (empty($HTTP_COOKIE_VARS["comment_author"])) ? "url" : trim($HTTP_COOKIE_VARS["comment_author_url"]);

	$comments = $wpdb->get_results("SELECT * FROM $tablecomments WHERE comment_post_ID = $id ORDER BY comment_date");
	$commentstatus = $wpdb->get_var("SELECT comment_status FROM $tableposts WHERE ID = $id");
	$pingstatus = $wpdb->get_var("SELECT ping_status FROM $tableposts WHERE ID = $id");
?>

<!-- you can start editing here -->

<h2>Comments</h2>

<?php if ('open' == $pingstatus) { ?>
<p>The URL to TrackBack this entry is:</p>
<p><em><?php trackback_url() ?></em></p>
<?php } ?>

<ol id="comments">
<?php 
// this line is WordPress' motor, do not delete it.
if ($comments) {
	foreach ($comments as $comment) {
?>
	
<li id="comment-<?php comment_ID() ?>">
<?php comment_text() ?>
<p><cite><?php comment_type(); ?> by <?php comment_author_link() ?> <?php comment_date() ?> @ <a href="#comment-<?php comment_ID() ?>"><?php comment_time() ?></a></cite></p>
</li>

<?php } // end for each comment
} else { // this is displayed if there are no comments so far 
?>
	<li>No comments yet.</li>
<?php } ?>
</ol>
<h2>Leave a Comment</h2>
<?php if ('open' == $commentstatus) { ?>
<p>Line and paragraph breaks automatic, website trumps email, <acronym title="Hypertext Markup Language">HTML</acronym> allowed: <?php echo htmlentities($comment_allowed_tags); ?></p>


<form action="<?php echo $siteurl; ?>/b2comments.post.php" method="post" id="commentform">
	<p>
	  <input type="text" name="author" id="author" class="textarea" value="<?php echo $comment_author; ?>" size="28" tabindex="1" />
	   <label for="author">name</label>
	<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
	<input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" />
	</p>

	<p>
	  <input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="28" tabindex="2" />
	   <label for="email">Email</label>
	</p>

	<p>
	  <input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="28" tabindex="3" />
	   <label for="url"><acronym title="Uniform Resource Locator">URL</acronym></label>
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
<?php } ?>

<div><a href="javascript:history.go(-1)">Go back</a>.</div>

<?php // if you delete this the sky will fall on your head
}
?>