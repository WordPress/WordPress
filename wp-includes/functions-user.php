<?php

function verify_login($user, $password) {
	global $wpdb;
	$user = $wpdb->escape($user);
	$password = $password;

	if ( $user = $wpdb->get_row("SELECT user_login, user_pass FROM $wpdb->users WHERE user_login = '$user'") ) {
		if ( $user->user_pass = md5($password) )
			return true;
		else
			return false;
	} else {
		return false;
	}
}

function verify_current() {
	if (!empty($_COOKIE['wordpressuser_' . COOKIEHASH])) {
		$user_login = $_COOKIE['wordpressuser_' . COOKIEHASH];
		$user_pass = $_COOKIE['wordpresspass_' . COOKIEHASH];
	} else {
		return false;
	}

	if ('' == $user_login)
		return false;
	if ('' == $user_pass)
		return false;

	if ( verify_login($user_login, $user_pass) {
		return true;
	} else {
		header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Location: ' . get_settings('siteurl') . '/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']) );
		exit();
	}
}

?>