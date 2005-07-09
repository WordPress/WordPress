<?php

function username_exists( $username ) {
	global $wpdb;
	$username = sanitize_user( $username );
	$query = "SELECT user_login FROM $wpdb->users WHERE user_login = '$username'";
	$query = apply_filters('username_exists', $query);
	return $wpdb->get_var( $query );
}

function create_user( $username, $password, $email, $user_level ) {
	global $wpdb;
	$username = $wpdb->escape( $username );
	$email    = $wpdb->escape( $email );
	$password = md5( $password );
	$user_nicename = sanitize_title( $username );
	$now = gmdate('Y-m-d H:i:s');

	$query = "INSERT INTO $wpdb->users 
		(user_login, user_pass, user_email, user_registered, user_nicename, display_name)
	VALUES 
		('$username', '$password', '$email', '$now', '$user_nicename', '$username')";
	$query = apply_filters('create_user_query', $query);
	$wpdb->query( $query );
	$user_id = $wpdb->insert_id;

	$user_level = (int) $user_level;
	update_usermeta( $user_id, $wpdb->prefix . 'user_level', $user_level);
	return $user_id;
}

?>