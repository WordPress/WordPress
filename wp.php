<?php /* Don't remove this line, it calls the WP function files ! */
$blog=1;
require_once("wp-blog-header.php");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml/DTD/xhtml-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php bloginfo('name') ?><?php single_post_title(' :: ') ?><?php single_cat_title(' :: ') ?><?php single_month_title(' :: ') ?></title>
  <!-- Change charset if needed(?)  But please do not remove this metatag -->
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <meta name="generator" content="WordPress <?php $b2_version ?>" /> <!-- leave this for stats -->
  <meta http-equiv="reply-to" content="you@somewhere.zzz" />
  <link rel="alternate" type="text/xml" title="RDF" href="<?php bloginfo('rdf_url'); ?>" />
  <link rel="alternate" type="text/xml" title="RSS" href="<?php bloginfo('rss2_url'); ?>" />
  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
</head>
<body>
<h1 id="header"><a href="<?php echo $siteurl; ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>

<!-- // loop start -->
<?php foreach ($posts as $post) { start_b2(); ?>
<?php the_date("d.m.y","<h2>","</h2>"); ?>
<h3 class="storytitle" id="post-<?php the_ID(); ?>"><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h3>

<?php the_content(); ?><?php link_pages("<br />Pages: ","<br />","number") ?>
<p><em>posted by <strong><?php the_author() ?></strong> @ <a href="<?php permalink_link() ?>"><?php the_time() ?></a></em></p>
<p>Filed under: <?php the_category() ?></p>
<?php comments_popup_link("comments ?", "1 comment", "% comments") ?>

<?php include ("wp-comments.php"); ?>


<!-- // this is just the end of the motor - don't touch that line either :) -->
<?php } ?> 

<div align="right"><cite>Powered by <a href="http://wordpress.org"><strong>Wordpress</strong></a></cite><br />
<br />
<a href="wp-login.php">login</a><br />
<a href="wp-register.php">register</a></div>

<?php
$debug = "1";
if ($debug == "1") {
	echo "<p>$wpdb->querycount queries - ".number_format(timer_stop(),3)." seconds</p>";
}
?>

</body>
</html>