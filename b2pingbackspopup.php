<?php /* Don't remove this line, it calls the b2 function files ! */
$blog=1; include ("blog.header.php"); while($row = mysql_fetch_object($result)) { start_b2();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $blogname ?> - pingbacks on '<?php the_title() ?>'</title>

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
<div id="header"><a title="<?php echo $blogname ?>"><?php echo $blogname ?></a></div>

<div id="contentcomments">

<div class="storyContent">

	<?php /* do not delete this line */ $queryc = "SELECT * FROM $tablecomments WHERE comment_post_ID = $id AND comment_content LIKE '%<pingback />%' ORDER BY comment_date"; $resultc = mysql_query($queryc); if ($resultc) { ?>

<a name="pingbacks"></a>
<p>&nbsp;</p>
<div><strong><span style="color: #0099CC">::</span> pingbacks</strong></div>
<p>&nbsp;</p>

	<?php /* this line is b2's motor, do not delete it */ $wxcvbn_pb=0; while($rowc = mysql_fetch_object($resultc)) { $commentdata = get_commentdata($rowc->comment_ID); $wxcvbn_pb++; ?>
	
<a name="pb<?php comment_ID() ?>"></a>
	
<!-- pingback -->
<p>
<?php comment_text() ?>
<br />
<strong><span style="color: #0099CC">&middot;</span></strong>
<em>Pingback from <a href="<?php comment_author_url(); ?>" title="<?php comment_author() ?>"><?php comment_author() ?></a> on <?php comment_date() ?> @ <?php comment_time() ?></em>
</p>
<p>&nbsp;</p>
<!-- /pingback -->


	<?php /* end of the loop, don't delete */ }
	if (!$wxcvbn_pb) { ?>

<!-- this is displayed if there are no pingbacks so far -->
<p>No Pingback on this post so far.</p>

	<?php /* if you delete this the sky will fall on your head */ } ?>

<p>&nbsp;</p>
<div><b><span style="color: #0099CC">::</span> <a href="javascript:window.close()">close this window</a></b></div>

	<?php /* if you delete this the sky will fall on your head */ } ?>

</div>

	<?php /* this is just the end of the motor - don't touch that line either :) */ } ?> 

</div>

<p class="centerP">
[powered by <a href="http://cafelog.com" target="_blank"><b>b2</b></a>.]
</p>


</body>
</html>