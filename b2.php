<?php /* Don't remove this line, it calls the b2 function files ! */ $blog=1; include ("blog.header.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml/DTD/xhtml-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $blogname ?></title>

<!-- Change charset if needed(?)  But please do not remove this metatag -->
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="reply-to" content="you@somewhere.zzz" />
<meta http-equiv="imagetoolbar" content="no" />
<meta content="TRUE" name="MSSmartTagsPreventParsing" />
<link rel="alternate" type="text/xml" title="RDF" href="<?php bloginfo('rdf_url'); ?>" />
<link rel="alternate" type="text/xml" title="RSS" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php comments_popup_script() ?>
</head>

<body>

	<!-- // b2 loop start -->
	<?php while($row = mysql_fetch_object($result)) { start_b2(); ?>


<?php the_date("d.m.y","<h1>","</h1>"); ?>

<p>
<?php permalink_anchor(); ?>

<strong><?php the_title(); ?></strong> (category: <strong><?php the_category() ?></strong>)<br />
<?php the_content(); ?><?php link_pages("<br />Pages: ","<br />","number") ?>
<br />
<em>posted by <strong><?php the_author() ?></strong> @ <a href="<?php permalink_link() ?>"><?php the_time() ?></a></em>
<br />
<?php comments_popup_link("comments ?", "1 comment", "% comments") ?>

</p>

<?php include ("b2comments.php"); ?>


	<!-- // this is just the end of the motor - don't touch that line either :) -->
	<?php } ?> 

<div align="right"><cite>Powered by <a href="http://wordpress.org"><strong>Wordpress</strong></a></cite><br />
<br />
<a href="b2login.php">login</a><br />
<a href="b2register.php">register</a></div>

<?php
if ($debug=="1") {
	echo "<p>$querycount queries - ".number_format(timer_stop(),3)." seconds</p>";
}
?>

</body>
</html>