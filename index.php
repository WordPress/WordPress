<?php 
/* Don't remove these lines. */
$blog = 1;
require_once('wp-blog-header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
	
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="generator" content="WordPress <?php echo $wp_version; ?>" /> <!-- leave this for stats -->

	<style type="text/css" media="screen">
		@import url( <?php echo $siteurl; ?>/wp-layout.css );
	</style>
	
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo $siteurl; ?>/print.css" />
	<link rel="alternate" type="text/xml" title="RDF" href="<?php bloginfo('rdf_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <?php get_archives('monthly', '', 'link'); ?>
	<?php //comments_popup_script(); // off by default ?>

</head>

<body>
<div id="rap">
<h1 id="header"><a href="<?php echo $siteurl; ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>

<div id="content">
<?php if ($posts) { foreach ($posts as $post) { start_wp(); ?>

<?php the_date('','<h2>','</h2>'); ?>
	
<div class="post">
	 <h3 class="storytitle" id="post-<?php the_ID(); ?>"><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h3>
	<div class="meta">Filed under: <?php the_category() ?> &#8212; <?php the_author() ?> @ <?php the_time() ?> <?php edit_post_link(); ?></div>
	
	<div class="storycontent">
		<?php the_content(); ?>
	</div>
	
	<div class="feedback">
		<?php link_pages('<br />Pages: ', '<br />', 'number'); ?> 
		<?php comments_popup_link('Comments (0)', 'Comments (1)', 'Comments (%)'); ?> 
	</div>
	
	<!--
	<?php trackback_rdf(); ?>
	-->

<?php include(ABSPATH . 'wp-comments.php'); ?>
</div>

<?php } } else { // end foreach, end if any posts ?>
<p>Sorry, no posts matched your criteria.</p>
<?php } ?>
</div>



<div id="menu">

<ul>
 <li>Links:
	<ul>
		<?php get_links(-1, '<li>', '</li>', '', 0, '_updated', 0, 0, -1, -1)?>
	</ul>
 </li>
 <li>Categories:
	<ul>
	<?php list_cats(0, 'All', 'name'); ?>
	</ul>
 </li>
 <li>Search:
	<form id="searchform" method="get" action="<?php echo $PHP_SELF; /*$siteurl."/".$blogfilename*/ ?>">
	<div>
		<input type="text" name="s" size="15" /><br />
		<input type="submit" name="submit" value="search" />
	</div>
	</form>
 </li>
 <li>Archives:
 	<ul>
	 <?php get_archives('monthly'); ?>
 	</ul>
 </li>
 <li>
	<?php get_calendar(); ?>
 </li>
 <li>Other:
	<ul>
		<li><a href="<?php echo $siteurl; ?>/wp-login.php">login</a></li>
		<li><a href="<?php echo $siteurl; ?>/wp-register.php">register</a></li>
	</ul>
 </li>
 <li>Meta:
 	<ul>
		<li><a href="<?php bloginfo('rss2_url'); ?>" title="Syndicate this site using RSS"><abbr title="Really Simple Syndication">RSS</abbr> 2.0</a></li>
		<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="The latest comments to all posts in RSS">Comments <abbr title="Really Simple Syndication">RSS</abbr> 2.0</a></li>
		<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
		<li><a href="http://wordpress.org" title="Powered by WordPress, state-of-the-art semantic personal publishing platform">WP</a></li>
	</ul>
 </li>

</ul>

</div>

</div>

<p class="credit"><?php echo $wpdb->querycount; ?> queries. <?php timer_stop(1); ?> <cite>Powered by <a href="http://wordpress.org" title="Powered by WordPress, state-of-the-art semantic personal publishing platform"><strong>WordPress</strong></a></cite></p>
</body>
</html>