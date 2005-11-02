<?php

function username_exists( $username ) {
	global $wpdb;
	$username = sanitize_user( $username );
	$query = "SELECT user_login FROM $wpdb->users WHERE user_login = '$username'";
	$query = apply_filters('username_exists', $query);
	return $wpdb->get_var( $query );
}

function wp_insert_user($userdata) {
	global $wpdb;

	extract($userdata);

	// Are we updating or creating?
	if ( !empty($ID) ) {
		$update = true;
	} else {
		$update = false;
		// Password is not hashed when creating new user.
		$user_pass = md5($user_pass);
	}
	
	if ( empty($user_nicename) )
		$user_nicename = sanitize_title( $user_login );

	if ( empty($display_name) )
		$display_name = $user_login;
		
	if ( empty($nickname) )
		$nickname = $user_login;
			
	if ( empty($user_registered) )
		$user_registered = gmdate('Y-m-d H:i:s');

	if ( $update ) {
		$query = "UPDATE $wpdb->users SET user_pass='$user_pass', user_email='$user_email', user_url='$user_url', user_nicename = '$user_nicename', display_name = '$display_name' WHERE ID = '$ID'";
		$query = apply_filters('update_user_query', $query);
		$wpdb->query( $query );
		$user_id = $ID;
	} else {
		$query = "INSERT INTO $wpdb->users 
		(user_login, user_pass, user_email, user_url, user_registered, user_nicename, display_name)
	VALUES 
		('$user_login', '$user_pass', '$user_email', '$user_url', '$user_registered', '$user_nicename', '$display_name')";
		$query = apply_filters('create_user_query', $query);
		$wpdb->query( $query );
		$user_id = $wpdb->insert_id;
	}
	
	clean_user_cache($user_id);
	clean_user_cache($user_login);

	update_usermeta( $user_id, 'first_name', $first_name);
	update_usermeta( $user_id, 'middle_name', $middle_name);
	update_usermeta( $user_id, 'last_name', $last_name);
	update_usermeta( $user_id, 'nickname', $nickname );
	update_usermeta( $user_id, 'description', $description );
	update_usermeta( $user_id, 'jabber', $jabber );
	update_usermeta( $user_id, 'aim', $aim );
	update_usermeta( $user_id, 'yim', $yim );
	update_usermeta( $user_id, 'flickr_username', $flickr_username );
	
	
	if ( !$update ) {
		$user = new WP_User($user_id);
		$user->set_role(get_settings('default_role'));
	}
	
	if ( $update )
		do_action('profile_update', $user_id);
	else
		do_action('user_register', $user_id);
		
	return $user_id;	
}

function wp_update_user($userdata) {
	global $wpdb, $current_user;

	$ID = (int) $userdata['ID'];
	
	// First, get all of the original fields
	$user = get_userdata($ID);	

	// Escape data pulled from DB.
	$user = add_magic_quotes(get_object_vars($user));

	// If password is changing, hash it now.
	if ( ! empty($userdata['user_pass']) ) {
		$plaintext_pass = $userdata['user_pass'];
		$userdata['user_pass'] = md5($userdata['user_pass']);
	}
	
	// Merge old and new fields with new fields overwriting old ones.
	$userdata = array_merge($user, $userdata);
	$user_id = wp_insert_user($userdata);

	// Update the cookies if the password changed.	
	if( $current_user->id == $ID ) {
		if ( isset($plaintext_pass) ) {
			wp_clearcookie();
			wp_setcookie($userdata['user_login'], $plaintext_pass);
		}
	}
	
	return $user_id;
}

function wp_create_user( $username, $password, $email = '') {
	global $wpdb;
	
	$user_login = $wpdb->escape( $username );
	$user_email = $wpdb->escape( $email );
	$user_pass = $password;

	$userdata = compact('user_login', 'user_email', 'user_pass');
	return wp_insert_user($userdata);
}


function create_user( $username, $password, $email ) {
	return wp_create_user( $username, $password, $email );	
}


?>