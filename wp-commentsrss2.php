<?php
/**
 * Outputs the RSS2 XML format comment feed using the feed-rss2.php file in
 * wp-includes folder. This file only sets the feed format and includes the
 * feed-rss2-comments.php.
 *
 * This file is no longer used in WordPress and while it is not deprecated now.
 * This file will most likely be deprecated or removed in a later version.
 *
 * The link for the rss2 comment feed is /index.php?feed=rss2&withcomments=1
 * with permalinks off.
 *
 * @package WordPress
 */

if (empty($wp)) {
	require_once('./wp-load.php');
	wp('feed=rss2&withcomments=1');
}

require (ABSPATH . WPINC . '/feed-rss2-comments.php');

?>