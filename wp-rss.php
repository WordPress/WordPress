<?php

if (empty($wp)) {
	require_once('./wp-load.php');
	wp('feed=rss');
}

require (ABSPATH . WPINC . '/feed-rss.php');

?>