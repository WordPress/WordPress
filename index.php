<?php /* Don't remove this line, it calls the b2 function files ! */ $blog=1; include ('blog.header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?php bloginfo('name') ?><?php single_post_title(' :: ') ?><?php single_cat_title(' :: ') ?><?php single_month_title(' :: ') ?></title>
	
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	
	<style type="text/css" media="screen">
		@import url( layout2b.css );
	</style>
	
	<link rel="stylesheet" type="text/css" media="print" href="print.css" />
	<link rel="alternate" type="text/xml" title="RDF" href="<?php bloginfo('rdf_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php comments_popup_script() ?>
</head>

<body>
<h1 id="header"><a href="<?php echo $siteurl; ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>


<div id="content">

<!-- // b2 loop start -->
<?php while($row = mysql_fetch_object($result)) { start_b2(); ?>


<?php the_date('','<h2>','</h2>'); ?>

<h3 class="storyTitle"><a href="<?php permalink_link() ?>"><?php the_title(); ?></a> <a href="?cat=<?php the_category_ID() ?>" title="Category: <?php the_category() ?>" class="storyCategory">[<?php the_category() ?>]</a> - <span class="storyAuthor"><?php the_author() ?></span> @ <?php the_time() ?>
</h3>

<div class="storyContent">
<div class="storyExcerpt">
<?php the_excerpt(); ?>
</div>
<?php the_content(); ?>

<div class="feedback">
<?php link_pages('<br />Pages: ', '<br />', 'number') ?> 
<?php comments_popup_link('Comments (0)', 'Comments (1)', 'Comments (%)') ?> 
<?php trackback_popup_link('TrackBack (0)', 'TrackBack (1)', 'TrackBack (%)') ?> 
<?php pingback_popup_link('PingBack (0)', 'PingBack (1)', 'PingBack (%)') ?>

<?php trackback_rdf() ?>

<!-- this includes the comments and a form to add a new comment -->
<?php include ('b2comments.php'); ?>

<!-- this includes the trackbacks -->
<?php include ('b2trackback.php'); ?>

<!-- this includes the pingbacks -->
<?php include ('b2pingbacks.php'); ?>

</div>

</div>


<!-- // this is just the end of the motor - don't touch that line either :) -->
	<?php } ?> 


</div>

<p class="centerP"><?php timer_stop(1); ?> <cite>Powered by <a href="http://wordpress.org"><strong>Wordpress</strong></a></cite>
</p>


<div id="menu">

<ul>
 <li>Quick Links:
	<ul>
		<li><a href="http://wordpress.org" title="The Wordpress Organization">Wordpress</a></li>
		<li><a href="http://www.cafelog.com" title="b2's homepage">cafelog.com</a></li>
		<li><a href="http://some other site" title="another link">another link</a></li>
		<li><a href="http://photomatt.net" title="another link">another link</a></li>
	</ul>
 </li>
 <li>Categories:
	<ul>
	<?php list_cats(0, 'All', 'name'); ?>
	</ul>
 </li>
 <li>Search:
	<form name="searchform" method="get" action="<?php echo $PHP_SELF; /*$siteurl."/".$blogfilename*/ ?>">
	<div>
		<input type="text" name="s" size="15" /><br />
		<input type="submit" name="submit" value="search" />
	</div>
	</form>
 </li>
 <li>Archives:
 	<ul>
	 <?php include("b2archives.php"); // fix this too ?>
 	</ul>
 </li>
 <li>Other:
	<ul>
		<li><a href="b2login.php">login</a></li>
		<li><a href="b2register.php">register</a></li>
	</ul>
 </li>
 <li>Misc:
 	<ul>
		<li><a href="b2rss.php"><img src="b2-img/xml.gif" alt="view this weblog as RSS !" width="36" height="14" border="0"  /></a></li>
		<li><a href="http://validator.w3.org/check/referer" title="this page validates as XHTML 1.0 Transitional"><img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0!" height="31" width="88" border="0" /></a></li>
	</ul>
 </li>
</ul>

</div>


</body>
</html>