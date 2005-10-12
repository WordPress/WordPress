<?php
// This is an example of a very simple template
require_once('./wp-blog-header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml/DTD/xhtml-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_settings('blog_charset'); ?>" />
	<meta name="generator" content="WordPress <?php $wp_version ?>" /> <!-- leave this for stats -->
	<link rel="alternate" type="text/xml" title="RSS" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
</head>
<body>
<h1 id="header"><a href="<?php echo get_settings('home'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>

<!-- // loop start -->
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php the_date('d.m.y', '<h2>','</h2>'); ?>
<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h3>

<?php the_content(); ?>

<?php link_pages('<br />Pages: ', '<br />', 'number') ?>

<p><em>Posted by <strong><?php the_author() ?></strong> @ <a href="<?php the_permalink() ?>"><?php the_time() ?></a></em></p>
<p>Filed under: <?php the_category(',') ?></p>

<?php comments_popup_link('comments ?', '1 comment', '% comments') ?>

<?php comments_template(); ?>


<!-- // this is just the end of the motor - don't touch that line either :) -->
<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>

<div align="right"><cite>Powered by <a href="http://wordpress.org/"><strong>Wordpress</strong></a></cite><br />
<br />
<a href="wp-login.php">login</a><br />
<a href="wp-register.php">register</a>
</div>


</body>
</html>