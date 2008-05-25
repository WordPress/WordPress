<?php
/**
 * Outputs the RSS2 feed XML format. This file is a shortcut or compatibility
 * layer for easily finding the RSS feed for the site. It loads WordPress using
 * the wp-blog-header.php file and running do_feed() function.
 *
 * @see do_feed() Used to display the RSS2 feed
 *
 * This file is no longer used in WordPress and while it is not deprecated now.
 * This file will most likely be deprecated or removed in a later version.
 *
 * The link for the rss2 feed is /index.php?feed=rss2 with permalinks off.
 *
 * @package WordPress
 */

if (empty($doing_rss)) {
	$doing_rss = 1;
	require(dirname(__FILE__) . '/wp-blog-header.php');
}

do_feed();

?>
