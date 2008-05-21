<?php

if (empty($wp)) {
	require_once('./wp-load.php');
	wp('feed=atom');
}

require (ABSPATH . WPINC . '/feed-atom.php');

?>