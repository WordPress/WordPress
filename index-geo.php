<?php /* Don't remove these lines, they call the b2 function files ! */
$blog = 1;
require_once('blog.header.php');
require_once($abspath.'wp-links/links.php');
// not on by default: require_once($abspath.'wp-links/links.weblogs.com.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?php bloginfo('name') ?><?php single_post_title(' :: ') ?><?php single_cat_title(' :: ') ?><?php single_month_title(' :: ') ?></title>
	
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="generator" content="WordPress .72" /> <!-- leave this for stats -->

	<?php if(get_settings('use_geo_positions')) {
		doGeoUrlHeader($posts);
	} ?>
	<style type="text/css" media="screen">
		@import url( <?php echo $siteurl; ?>/wp-layout.css );
	</style>
	
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo $siteurl; ?>/print.css" />
	<link rel="alternate" type="text/xml" title="RDF" href="<?php bloginfo('rdf_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <?php get_archives('monthly', '', 'link'); ?>
	<?php //comments_popup_script(); // off by default ?>
	<?php if(get_settings('use_geo_positions')) {
		print_PopUpScript();
	} ?>

</head>

<body>
<div id="rap">
<h1 id="header"><a href="<?php echo $siteurl; ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>

<div id="content">
<?php if ($posts) { foreach ($posts as $post) { start_b2(); ?>
<?php the_date('','<h2>','</h2>'); ?>

 <h3 class="storytitle" id="post-<?php the_ID(); ?>">
  <a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a>
  <span class="meta"><a href="<?php get_category_link(1) ?>" title="Category: <?php the_category() ?>">[<?php the_category() ?>]</a> &#8212; <?php the_author() ?> @ <?php the_time() ?> 
  <?php if(get_settings('use_geo_positions')) { if((get_Lon() != null) && (get_Lon() < 360) &&(get_Lon() > -360)) {  ?>
		&#8212; Posted from: <?php print_Lat(); ?> &#215; <?php print_Lon(); ?>
<?php  } } ?>
		</span>
 </h3>
<?php if(get_settings('use_geo_positions')) { if((get_Lon() != null) && (get_Lon() < 360) &&(get_Lon() > -360)) {  ?>
    	 <?php print_UrlPopNav(); ?>
<?php } } ?>

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

<?php include($abspath . 'b2comments.php'); ?>

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
		<li><a href="<?php echo $siteurl; ?>/b2login.php">login</a></li>
		<li><a href="<?php echo $siteurl; ?>/b2register.php">register</a></li>
	</ul>
 </li>
 <li>Meta:
 	<ul>
		<li><a href="<?php bloginfo('rss_url'); ?>">RSS .92</a></li>
		<li><a href="<?php bloginfo('rdf_url'); ?>">RDF 1.0</a></li>
		<li><a href="<?php bloginfo('rss2_url'); ?>">RSS 2.0</a></li>
		<li><a href="<?php bloginfo('comments_rss2_url'); ?>">Comments RSS 2.0</a></li>
		<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
		<li><a href="http://wordpress.org" title="Powered by WordPress, personal publishing platform">WP</a></li>
	</ul>
 </li>

</ul>

</div>

</div>

<p class="credit"><?php timer_stop(1); ?> <cite>Powered by <a href="http://wordpress.org"><strong>WordPress</strong></a></cite></p>
</body>
</html>
