<?php
// If a header.php file exists in the WP root directory we
// use that, otherwise use this default wp-header.php file.
if ( file_exists(ABSPATH . '/header.php') ) :
	include_once(ABSPATH . '/header.php');
else :
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
	
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

	<style type="text/css" media="screen">
		@import url( <?php echo bloginfo('stylesheet_url'); ?> );
	</style>
	
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo get_settings('siteurl'); ?>/print.css" />
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <?php wp_get_archives('type=monthly&format=link'); ?>
	<?php //comments_popup_script(); // off by default ?>
	<?php wp_head(); ?>
</head>

<body>
<div id="rap">
<h1 id="header"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>

<div id="content">
<?php endif; ?>