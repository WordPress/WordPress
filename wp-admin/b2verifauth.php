<?php

require_once('../wp-config.php');

/* checking login & pass in the database */
function veriflog() {
	global $HTTP_COOKIE_VARS,$cookiehash;
	global $tableusers, $wpdb;

	if (!empty($HTTP_COOKIE_VARS["wordpressuser_".$cookiehash])) {
		$user_login = $HTTP_COOKIE_VARS["wordpressuser_".$cookiehash];
		$user_pass_md5 = $HTTP_COOKIE_VARS["wordpresspass_".$cookiehash];
	} else {
		return false;
	}

	if (!($user_login != ''))
		return false;
	if (!$user_pass_md5)
		return false;

	$login = $wpdb->get_row("SELECT user_login, user_pass FROM $tableusers WHERE user_login = '$user_login'");

	if (!$login) {
		return false;
	} else {
		if ($login->user_login == $user_login && md5($login->user_pass) == $user_pass_md5) {
			return true;
		} else {
			return false;
		}
	}
}
//if ( $user_login!="" && $user_pass!="" && $id_session!="" && $adresse_ip==$REMOTE_ADDR) {
//	if ( !(veriflog()) AND !(verifcookielog()) ) {
	if (!(veriflog())) {
		header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		if (!empty($HTTP_COOKIE_VARS["wordpressuser_".$cookiehash])) {
			$error="<strong>Error</strong>: wrong login or password";
		}
		$redir = "Location: $siteurl/b2login.php?redirect_to=" . urlencode($HTTP_SERVER_VARS["REQUEST_URI"]);
		header($redir);
		exit();
	}
//}
?>
