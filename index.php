<?php /* Don't remove this line, it calls the b2 function files ! */ $blog=1;
require('blog.header.php');
require($abspath.'wp-links/links.php');
require($abspath.'wp-links/links.weblogs.com.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?php bloginfo('name') ?><?php single_post_title(' :: ') ?><?php single_cat_title(' :: ') ?><?php single_month_title(' :: ') ?></title>
	
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="generator" content="WordPress .7" /> <!-- leave this for stats -->

	<style type="text/css" media="screen">
		@import url( <?php echo $siteurl; ?>/layout2b.css );
	</style>
	
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo $siteurl; ?>/print.css" />
	<link rel="alternate" type="text/xml" title="RDF" href="<?php bloginfo('rdf_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php  comments_popup_script(); // off by default ?>
</head>

<body>
<h1 id="header"><a href="<?php echo $siteurl; ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>

<div id="content">

<!-- // b2 loop start -->
<?php while($row = mysql_fetch_object($result)) { start_b2(); ?>


<?php the_date('','<h2>','</h2>'); ?>

 <h3 class="storytitle">
  <a href="<?php permalink_link() ?>"><?php the_title(); ?></a> 
  <span class="meta"><a href="?cat=<?php the_category_ID() ?>" title="Category: <?php the_category() ?>">[<?php the_category() ?>]</a> &#8212; <?php the_author() ?> @ <?php the_time() ?>
  </span>
 </h3>

<div class="storycontent">
	<?php the_content(); ?>
</div>

<div class="feedback">
	<?php link_pages('<br />Pages: ', '<br />', 'number'); ?> 
	<?php comments_popup_link('Comments (0)', 'Comments (1)', 'Comments (%)'); ?> 
	<?php trackback_popup_link('TrackBack (0)', 'TrackBack (1)', 'TrackBack (%)'); ?> 
	<?php pingback_popup_link('PingBack (0)', 'PingBack (1)', 'PingBack (%)'); ?>
</div>

<?php trackback_rdf(); ?>

<!-- this includes the comments and a form to add a new comment -->
<?php include('b2comments.php'); ?>

<!-- this includes the trackbacks -->
<?php include('b2trackback.php'); ?>

<!-- this includes the pingbacks -->
<?php include('b2pingbacks.php'); ?>

<!-- // this is just the end of the motor - don't touch that line either :) -->
	<?php } ?> 


</div>

<p class="credit"><?php timer_stop(1); ?> <cite>Powered by <a href="http://wordpress.org"><strong>Wordpress</strong></a></cite></p>


<div id="menu">

<ul>
<li>Links:
	<ul>
		<?php get_links(-1, '<li>', '</li>', '', 0, '_updated', 0, 0, -1, 1 )?>
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
 <li>Other:
	<ul>
		<li><a href="b2login.php">login</a></li>
		<li><a href="b2register.php">register</a></li>
	</ul>
 </li>
 <li>Meta:
 	<ul>
		<li><a href="b2rss.php">RSS .92</a></li>
		<li><a href="b2rdf.php">RDF 1.0</a></li>
		<li><a href="b2rss2.php">RSS 2.0</a></li>
		<li><a href="http://validator.w3.org/check/referer" title="this page validates as XHTML 1.0 Transitional">Valid XHTML</a></li>
		<li><a href="http://wordpress.org" title="Powered by WordPress, personal publishing platform">WP</a></li>
	</ul>
 </li>
</ul>

</div>


</body>
</html>