	<?php if (!empty($pb)) { ?>

	<?php // Do not delete these lines
	if (basename($HTTP_SERVER_VARS["SCRIPT_FILENAME"]) == "b2pingbacks.php")
		die ("please, do not load this page directly");
	$queryc = "SELECT * FROM $tablecomments WHERE comment_post_ID = $id AND comment_content LIKE '%<pingback />%' ORDER BY comment_date";
	$resultc = mysql_query($queryc); if ($resultc) {
	?>

<!-- you can START editing here -->

<h2>Pingbacks</h2>

<ol id="pingbacks">
	<?php /* this line is b2's motor, do not delete it */ $wxcvbn_pb=0; while($rowc = mysql_fetch_object($resultc)) { $wxcvbn_pb++; $commentdata = get_commentdata($rowc->comment_ID); ?>
	

<a name="pb<?php comment_ID() ?>"></a>
	

<!-- pingback -->
<li>
<?php comment_text() ?>

<div><cite>Pingback from <a href="<?php comment_author_url(); ?>" title="<?php comment_author() ?>"><?php comment_author() ?></a> on <?php comment_date() ?> @ <a href="#pb<?php comment_ID(); ?>"></a><?php comment_time() ?></a></cite></div>
</li>
<!-- /pingback -->


	<?php /* end of the loop, don't delete */ }
	if (!$wxcvbn_pb) { ?>

<!-- this is displayed if there are no pingbacks so far -->
<li>No pingbacks on this post so far.</li>

	<?php /* if you delete this the sky will fall on your head */ } ?>
</ol>


<h3><a href="javascript:history.go(-1)">Go back.</a></h3>


	<?php /* if you delete this the sky will fall on your head */ } ?>

</div>

	<?php /* if you delete this the sky will fall on your head */ } ?>
