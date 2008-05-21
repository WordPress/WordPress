<?php

if (empty($wp)) {
	require_once('./wp-load.php');
	wp('feed=rss2');
}

require (ABSPATH . WPINC . '/feed-rss2.php');

?>