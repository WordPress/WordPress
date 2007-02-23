<?php

if (empty($wp)) {
	require_once('wp-config.php');
	wp('feed=atom');
}

require (ABSPATH . WPINC . '/feed-atom.php');

?>