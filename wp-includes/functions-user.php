<?php

function login($username, $password, $already_md5 = false) {
	global $wpdb, $error;
	if ( !$already_md5 )
		$pwd = md5($password);

	if ( !$username )
		return false;

	if ( !$password ) {
		$error = __('<strong>Error</strong>: The password field is empty.');
		return false;
	}

	$login = $wpdb->get_row("SELECT ID, user_login, user_pass FROM $wpdb->users WHERE user_login = '$username'");

	if (!$login) {
		$error = __('<strong>Error</strong>: Wrong login.');
		$pwd = '';
		return false;
	} else {

		if ( $login->user_login == $username && $login->user_pass == $pwd ) {
			return true;
		} else {
			$error = __('<strong>Error</strong>: Incorrect password.');
			$pwd = '';
			return false;
		}
	}
}

?>