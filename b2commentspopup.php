<?php /* Don't remove this line, it calls the b2 function files ! */
$blog=1; include ("blog.header.php"); while($row = mysql_fetch_object($result)) { start_b2();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $blogname ?> - comments on '<?php the_title() ?>'</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="reply-to" content="you@yourdomain.com" />
<meta http-equiv="imagetoolbar" content="no" />
<meta content="TRUE" name="MSSmartTagsPreventParsing" />

<style type="text/css" media="screen">
@import url( layout2b.css );
</style>
<link rel="stylesheet" type="text/css" media="print" href="b2-include/print.css" />
<link rel="alternate" type="text/xml" title="XML" href="<?php echo $siteurl ?>/b2rss.php" />

</head>
<body>
<div id="header"><a href="" title="<?php echo $blogname ?>"><?php echo $blogname ?></a></div>

<div id="contentcomments">

<div class="storyContent">

<?php 
$comment_author = (empty($HTTP_COOKIE_VARS["comment_author"])) ? "name" : $HTTP_COOKIE_VARS["comment_author"];
$comment_author_email = (empty($HTTP_COOKIE_VARS["comment_author"])) ? "email" : trim($HTTP_COOKIE_VARS["comment_author_email"]);
$comment_author_url = (empty($HTTP_COOKIE_VARS["comment_author"])) ? "url" : trim($HTTP_COOKIE_VARS["comment_author_url"]);

$queryc = "SELECT * FROM $tablecomments WHERE comment_post_ID = $id AND comment_content NOT LIKE '%<trackback />%' ORDER BY comment_date";
$resultc = mysql_query($queryc);
if ($resultc) {
?>



<!-- you can start editing here -->

<a name="comments"></a>
<p>&nbsp;</p>
<div><strong><span style="color: #0099CC">::</span> comments</strong></div>
<p>&nbsp;</p>

<?php // these lines are b2's motor, do not delete
while($rowc = mysql_fetch_object($resultc)) {
	$commentdata = get_commentdata($rowc->comment_ID);
?><a name="c<?php comment_ID() ?>"></a>
	
<!-- comment -->
<p>
<b><?php comment_author() ?> <?php comment_author_email_link("email", " - ", "") ?><?php comment_author_url_link("url", " - ", "") ?></b>
<br />
<?php comment_text() ?>
<br />
<?php comment_date() ?> @ <?php comment_time() ?>
</p>
<p>&nbsp;</p>
<!-- /comment -->


<?php //end of the loop, don't delete
}

?>

<div><strong><span style="color: #0099CC">::</span> leave a comment</strong></div>
<p>&nbsp;</p>


<!-- form to add a comment -->

<form action="b2comments.post.php" method="post">
	<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
	<input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($HTTP_SERVER_VARS["REQUEST_URI"]); ?>" />
	
	<p class="commentfield">
	name<br />
	<input type="text" name="author" class="textarea" value="<?php echo $comment_author ?>" size="20" tabindex="1" />
	</p>

	<p class="commentfield">
	email<br />
	<input type="text" name="email" class="textarea" value="<?php echo $comment_author_email ?>" size="20" tabindex="2" />
	</p>

	<p class="commentfield">
	url<br />
	<input type="text" name="url" class="textarea" value="<?php echo $comment_author_url ?>" size="20" tabindex="3" />
	</p>

	<p class="commentfield">
	your comment<br />
	<textarea cols="40" rows="4" name="comment" tabindex="4" class="textarea">comment</textarea>
	</p>

	<p class="commentfield">
	<input type="checkbox" name="comment_autobr" value="1" <?php
	if ($autobr)
	echo " checked=\"checked\"" ?> tabindex="6" /> Auto-BR (line-breaks become &lt;br> tags)<br />
	<input type="submit" name="submit" class="buttonarea" value="ok" tabindex="5" />
	</p>

</form>

<!-- /form -->


<p>&nbsp;</p>
<div><b><span style="color: #0099CC">::</span> <a href="javascript:window.close()">close this window</a></b></div>

<?php // if you delete this the sky will fall on your head
}
?>

</div>


<!-- // this is just the end of the motor - don't touch that line either :) -->
	<?php } ?> 


</div>

<p class="centerP">
[powered by <a href="http://cafelog.com" target="_blank"><b>b2</b></a>.]
</p>


</body>
</html>