<?php /* Don't remove this line, it calls the b2 function files ! */
$blog=1; include ("blog.header.php"); while($row = mysql_fetch_object($result)) { start_b2();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $blogname ?> - pingbacks on '<?php the_title() ?>'</title>
	
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	
	<style type="text/css" media="screen">
		@import url( layout2b.css );
		body {
			margin: 3px;
		}
	</style>

</head>
<body>
<h1 id="header"><a title="<?php echo $blogname ?>"><?php echo $blogname ?></a></h1>
<div id="contentcomments">

<div class="storyContent">

	<?php /* do not delete this line */ $queryc = "SELECT * FROM $tablecomments WHERE comment_post_ID = $id AND comment_content LIKE '%<pingback />%' ORDER BY comment_date"; $resultc = mysql_query($queryc); if ($resultc) { ?>

<h2>Pingbacks</h2>

<ol id="pingbacks">
	<?php /* this line is b2's motor, do not delete it */ $wxcvbn_pb=0; while($rowc = mysql_fetch_object($resultc)) { $commentdata = get_commentdata($rowc->comment_ID); $wxcvbn_pb++; ?>
	
<li id="pb<?php comment_ID() ?>">
	
<!-- pingback -->
<?php comment_text() ?>

<div><cite>Pingback from <a href="<?php comment_author_url(); ?>" title="<?php comment_author() ?>"><?php comment_author() ?></a> on <?php comment_date() ?> @ <a href="pb<?php comment_ID() ?>"><?php comment_time() ?></a></cite>
</div>

<!-- /pingback -->


	<?php /* end of the loop, don't delete */ }
	if (!$wxcvbn_pb) { ?>

<!-- this is displayed if there are no pingbacks so far -->
<li>No Pingbacks on this post so far.</li>

	<?php /* if you delete this the sky will fall on your head */ } ?>
</ol>

<h3> <a href="javascript:window.close()">Close this window</a>.</h3>

	<?php /* if you delete this the sky will fall on your head */ } ?>

</div>

	<?php /* this is just the end of the motor - don't touch that line either :) */ } ?> 

</div>


<p class="credit"><?php timer_stop(1); ?> <cite>Powered by <a href="http://wordpress.org"><strong>Wordpress</strong></a></cite></p>


</body>
</html>