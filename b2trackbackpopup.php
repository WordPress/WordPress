<?php /* Don't remove this line, it calls the b2 function files ! */
$blog=1; include ("blog.header.php"); while($row = mysql_fetch_object($result)) { start_b2();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $blogname ?> - Trackbacks on "<?php the_title() ?>"</title>
	
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	
	<style type="text/css" media="screen">
		@import url( layout2b.css );
		body { margin: 3px; }
	</style>
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo $siteurl; ?>/print.css" />
</head>

<body>
<h1 id="header"><a title="<?php echo $blogname ?>"><?php echo $blogname ?></a></h1>

<div id="contentcomments">

<div class="storyContent">

<p>The URL to TrackBack this entry is:</p>
<p><em><?php trackback_url() ?></em></p>


	<?php /* do not delete this line */ $queryc = "SELECT * FROM $tablecomments WHERE comment_post_ID = $id AND comment_content LIKE '%<trackback />%' ORDER BY comment_date"; $resultc = mysql_query($queryc); if ($resultc) { ?>

<h2>Trackbacks</h2>
<ol id="trackbacks">
	<?php /* this line is b2's motor, do not delete it */ $wxcvbn_tb=0; while($rowc = mysql_fetch_object($resultc)) { $commentdata = get_commentdata($rowc->comment_ID); $wxcvbn_tb++; ?>
	
<a name="tb<?php comment_ID() ?>"></a>
	
<!-- trackback -->
	<li id="trackback-<?php comment_ID() ?>">
	<?php comment_text() ?>
	
	<p><cite>Tracked on <a href="<?php comment_author_url(); ?>" title="<?php comment_author() ?>"><?php comment_author() ?></a> on <?php comment_date() ?> @ <?php comment_time() ?></cite></p>
	</li>
<!-- /trackback -->


	<?php /* end of the loop, don't delete */ }
	if (!$wxcvbn_tb) { ?>

<!-- this is displayed if there are no trackbacks so far -->
	<li>No trackbacks yet.</li>


	<?php /* if you delete this the sky will fall on your head */ } ?>
</ol>

<div><strong><a href="javascript:window.close()">close this window</a></strong></div>

	<?php /* if you delete this the sky will fall on your head */ } ?>

</div>

	<?php /* this is just the end of the motor - don't touch that line either :) */ } ?> 

</div>

<p class="credit"><?php timer_stop(1); ?> <cite>Powered by <a href="http://wordpress.org"><strong>Wordpress</strong></a></cite></p>


</body>
</html>