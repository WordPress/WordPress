	<?php if (!empty($pb)) { ?>

	<?php // Do not delete these lines
	if (basename($HTTP_SERVER_VARS["SCRIPT_FILENAME"]) == "b2pingbacks.php")
		die ("please, do not load this page directly");
	$queryc = "SELECT * FROM $tablecomments WHERE comment_post_ID = $id AND comment_content LIKE '%<pingback />%' ORDER BY comment_date";
	$resultc = mysql_query($queryc); if ($resultc) {
	?>

<!-- you can START editing here -->

<a name="pingbacks"></a>
<div><strong><span style="color: #0099CC">::</span> pingbacks</strong></div>


	<?php /* this line is b2's motor, do not delete it */ $wxcvbn_pb=0; while($rowc = mysql_fetch_object($resultc)) { $wxcvbn_pb++; $commentdata = get_commentdata($rowc->comment_ID); ?>
	

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
<div><b><span style="color: #0099CC">::</span> <a href="javascript:history.go(-1)">return to the blog</a></b></div>


	<?php /* if you delete this the sky will fall on your head */ } ?>

</div>

	<?php /* if you delete this the sky will fall on your head */ } ?>
