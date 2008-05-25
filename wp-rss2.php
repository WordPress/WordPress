<?php
/**
 * Outputs the RSS2 feed XML format using the feed-rss2.php file in wp-includes
 * folder. This file only sets the feed format and includes the feed-rss2.php.
 *
 * This file is no longer used in WordPress and while it is not deprecated now.
 * This file will most likely be deprecated or removed in a later version.
 *
 * The link for the rss2 feed is /index.php?feed=rss2 with permalinks off.
 *
 * @package WordPress
 */

if (empty($wp)) {
	require_once('./wp-load.php');
	wp('feed=rss2');
}

require (ABSPATH . WPINC . '/feed-rss2.php');

?>