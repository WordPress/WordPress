<?php
require_once('../wp-config.php');

if ( !empty($_COOKIE['wordpressuser_' . COOKIEHASH]) && !wp_login($_COOKIE['wordpressuser_' . COOKIEHASH], $_COOKIE['wordpresspass_' . COOKIEHASH) ) {
	header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');

	header('Location: ' . get_settings('siteurl') . '/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']);
	exit();
}

?>