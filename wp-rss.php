<?php

if (empty($wp)) {
	require_once('./wp-config.php');
	wp('feed=rss');
}

require (ABSPATH . WPINC . '/feed-rss.php');

?>