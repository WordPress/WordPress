<?php

if (empty($wp)) {
	require_once('./wp-config.php');
	wp('feed=rss2&withcomments=1');
}

require (ABSPATH . WPINC . '/feed-rss2-comments.php');

?>