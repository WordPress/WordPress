	<?php // Do not delete these lines
	if (basename($HTTP_SERVER_VARS["SCRIPT_FILENAME"]) == "b2comments.php")
		die ("please, do not load this page directly");
	if (($withcomments) or ($c)) {

		$comment_author = (empty($HTTP_COOKIE_VARS["comment_author"])) ? "name" : $HTTP_COOKIE_VARS["comment_author"];
		$comment_author_email = (empty($HTTP_COOKIE_VARS["comment_author"])) ? "email" : trim($HTTP_COOKIE_VARS["comment_author_email"]);
		$comment_author_url = (empty($HTTP_COOKIE_VARS["comment_author"])) ? "url" : trim($HTTP_COOKIE_VARS["comment_author_url"]);

	$queryc = "SELECT * FROM $tablecomments WHERE comment_post_ID = $id AND comment_content NOT LIKE '%<trackback />%' AND comment_content NOT LIKE '%<pingback />%' ORDER BY comment_date";
	$resultc = mysql_query($queryc);
	if ($resultc) {
	?>

<!-- you can start editing here -->

<h2 id="comment">Comments</h2>
<ol id="comments">
	<?php /* this line is b2's motor, do not delete it */ $wxcvbn_c=0; while($rowc = mysql_fetch_object($resultc)) { $wxcvbn_c++; $commentdata = get_commentdata($rowc->comment_ID); ?>
	
<!-- comment -->
<li id="comment-<?php comment_ID() ?>">
<?php comment_text() ?>
<p><cite>By <?php if ($commentdata["comment_author_url"] && $commentdata["comment_author_url"] != 'http://url') {
	echo <<<QQQ
<a href="{$commentdata["comment_author_url"]}">{$commentdata["comment_author"]}</a>
QQQ;
} else {
	echo $commentdata["comment_author"];
} ?> <?php comment_date() ?> @ <?php comment_time() ?></cite></p>
</li>
<!-- /comment -->
	<?php /* end of the loop, don't delete */ } if (!$wxcvbn_c) { ?>

<!-- this is displayed if there are no comments so far -->
	<li>No comments yet.</li>

	<?php /* if you delete this the sky will fall on your head */ } ?>
</ol>
<h2>Leave a Comment</h2>
<p>Line and paragraph breaks automatic, website trumps email, <acronym title="Hypertext Markup Language">HTML</acronym> allowed: <?php echo htmlentities($comment_allowed_tags); ?></p>

<!-- form to add a comment -->

<form action="<?php echo $siteurl; ?>/b2comments.post.php" method="post" id="commentform">
	<p>
	  <input type="text" name="author" id="author" class="textarea" value="<?php echo $comment_author; ?>" size="28" tabindex="1" />
	   <label for="author">name</label>
	<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
	<input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" />
	</p>

	<p>
	  <input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="28" tabindex="2" />
	   <label for="email">email</label>
	</p>

	<p>
	  <input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="28" tabindex="3" />
	   <label for="url"><acronym title="Uniform Resource Locator">url</acronym></label>
	</p>

	<p>
	  <label for="comment">your comment</label>
	<br />
	  <textarea name="comment" id="comment" cols="70" rows="4" tabindex="4"></textarea>
	</p>

	<p>
	  <input name="submit" type="submit" tabindex="5" value="Say it!" />
	</p>

</form>

<!-- /form -->

<div><a href="javascript:history.go(-1)">Go back</a>.</div>

<?php // if you delete this the sky will fall on your head
}
} else {
	return false;
}
?>