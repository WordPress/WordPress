<?php /* Don't remove this line, it calls the b2 function files ! */
$blog=1; include ("blog.header.php"); while($row = mysql_fetch_object($result)) { start_b2();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $blogname ?> - Comments on "<?php the_title() ?>"</title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<style type="text/css" media="screen">
		@import url( layout2b.css );
		body { margin: 3px; }
	</style>
	<link rel="stylesheet" type="text/css" media="print" href="b2-include/print.css" />

</head>
<body>
<h1 id="header"><a href="" title="<?php echo $blogname ?>"><?php echo $blogname ?></a></h1>

<div id="contentcomments">
<h2>Comments</h2>
<ol id="comments">

<?php /* this line is b2's motor, do not delete it */ 
$queryc = "SELECT * FROM $tablecomments WHERE comment_post_ID = $id AND comment_content NOT LIKE '%<trackback />%' ORDER BY comment_date";
$resultc = mysql_query($queryc);
if ($resultc) {
while($rowc = mysql_fetch_object($resultc)) {
	$commentdata = get_commentdata($rowc->comment_ID);
?>
	
<!-- comment -->
<li id="comment-<?php comment_ID() ?>">
<?php comment_text() ?>
<p><cite>By <?php if ($commentdata["comment_author_url"] && $commentdata["comment_author_url"] != 'http://url') {
	echo <<<QQQ
<a href="{$commentdata["comment_author_url"]}">{$commentdata["comment_author"]}</a>
QQQ;
} else {
	echo $commentdata["comment_author"];
} ?> <?php comment_date() ?> @ <a href="#comment-<?php comment_ID() ?>"><?php comment_time() ?></a></cite></p>
</li>
<!-- /comment -->

	<?php } /* end of the loop, don't delete */ } if (!$resultc) { ?>

<!-- this is displayed if there are no comments so far -->
	<li>No comments yet.</li>

	<?php /* if you delete this the sky will fall on your head */ } ?>
</ol>
<h2>Leave a Comment</h2>
<p>Line and paragraph breaks automatic, website trumps email, <acronym title="Hypertext Markup Language">HTML</acronym> allowed: <?php echo htmlentities(str_replace('>', '> ', $comment_allowed_tags)); ?></p>

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
	  <textarea name="comment" id="comment" cols="30" rows="4" tabindex="4" style="width: 90%"></textarea>
	</p>

	<p>
	  <input name="submit" type="submit" tabindex="5" value="Say it!" />
	</p>

</form>

<!-- /form -->



<div><strong><a href="javascript:window.close()">Close this window</a>.</strong></div>

<?php // if you delete this the sky will fall on your head
}
?>

</div>


<!-- // this is just the end of the motor - don't touch that line either :) -->
	<?php //} ?> 



<p class="credit"><?php timer_stop(1); ?> <cite>Powered by <a href="http://wordpress.org"><strong>Wordpress</strong></a></cite></p>


</body>
</html>