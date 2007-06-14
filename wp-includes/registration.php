<?php

/**
 * Checks whether the given username exists.
 * @param string $username Username.
 * @return mixed The user's ID on success, and null on failure.
 */
function username_exists( $username ) {
	if ( $user = get_userdatabylogin( sanitize_user( $username ) ) ) {
		return $user->ID;
	} else {
		return null;
	}
}

/**
 * Checks whether the given email exists.
 * @global object $wpdb WordPress database layer.
 * @param string $email Email.
 * @return mixed The user's ID on success, and false on failure.
 */
function email_exists( $email ) {
	global $wpdb;
	$email = $wpdb->escape( $email );
	return $wpdb->get_var( "SELECT ID FROM $wpdb->users WHERE user_email = '$email'" );
}

/**
 * Checks whether an username is valid.
 * @param string $username Username.
 * @return bool A filtered boolean.
 */
function validate_username( $username ) {
	$sanitized = sanitize_user( $username, true );
	$valid = ( $sanitized == $username );
	return apply_filters( 'validate_username', $valid, $username );
}

/**
 * Insert an user into the database.
 * @global object $wpdb WordPress database layer.
 * @param array $userdata An array of user data.
 * @return int The newly created user's ID.
 */
function wp_insert_user($userdata) {
	global $wpdb;

	extract($userdata, EXTR_SKIP);

	// Are we updating or creating?
	if ( !empty($ID) ) {
		$ID = (int) $ID;
		$update = true;
	} else {
		$update = false;
		// Password is not hashed when creating new user.
		$user_pass = md5($user_pass);
	}

	$user_login = sanitize_user($user_login, true);
	$user_login = apply_filters('pre_user_login', $user_login);

	if ( empty($user_nicename) )
		$user_nicename = sanitize_title( $user_login );
	$user_nicename = apply_filters('pre_user_nicename', $user_nicename);

	if ( empty($user_url) )
		$user_url = '';
	$user_url = apply_filters('pre_user_url', $user_url);

	if ( empty($user_email) )
		$user_email = '';
	$user_email = apply_filters('pre_user_email', $user_email);

	if ( empty($display_name) )
		$display_name = $user_login;
	$display_name = apply_filters('pre_user_display_name', $display_name);

	if ( empty($nickname) )
		$nickname = $user_login;
	$nickname = apply_filters('pre_user_nickname', $nickname);

	if ( empty($first_name) )
		$first_name = '';
	$first_name = apply_filters('pre_user_first_name', $first_name);

	if ( empty($last_name) )
		$last_name = '';
	$last_name = apply_filters('pre_user_last_name', $last_name);

	if ( empty($description) )
		$description = '';
	$description = apply_filters('pre_user_description', $description);

	if ( empty($rich_editing) )
		$rich_editing = 'true';

	if ( empty($user_registered) )
		$user_registered = gmdate('Y-m-d H:i:s');

	if ( $update ) {
		$query = "UPDATE $wpdb->users SET user_pass='$user_pass', user_email='$user_email', user_url='$user_url', user_nicename = '$user_nicename', display_name = '$display_name' WHERE ID = '$ID'";
		$query = apply_filters('update_user_query', $query);
		$wpdb->query( $query );
		$user_id = (int) $ID;
	} else {
		$query = "INSERT INTO $wpdb->users
		(user_login, user_pass, user_email, user_url, user_registered, user_nicename, display_name)
	VALUES
		('$user_login', '$user_pass', '$user_email', '$user_url', '$user_registered', '$user_nicename', '$display_name')";
		$query = apply_filters('create_user_query', $query);
		$wpdb->query( $query );
		$user_id = (int) $wpdb->insert_id;
	}

	update_usermeta( $user_id, 'first_name', $first_name);
	update_usermeta( $user_id, 'last_name', $last_name);
	update_usermeta( $user_id, 'nickname', $nickname );
	update_usermeta( $user_id, 'description', $description );
	update_usermeta( $user_id, 'jabber', $jabber );
	update_usermeta( $user_id, 'aim', $aim );
	update_usermeta( $user_id, 'yim', $yim );
	update_usermeta( $user_id, 'rich_editing', $rich_editing);

	if ( $update && isset($role) ) {
		$user = new WP_User($user_id);
		$user->set_role($role);
	}

	if ( !$update ) {
		$user = new WP_User($user_id);
		$user->set_role(get_option('default_role'));
	}

	wp_cache_delete($user_id, 'users');
	wp_cache_delete($user_login, 'userlogins');

	if ( $update )
		do_action('profile_update', $user_id);
	else
		do_action('user_register', $user_id);

	return $user_id;
}

/**
 * Update an user in the database.
 * @global object $wpdb WordPress database layer.
 * @param array $userdata An array of user data.
 * @return int The updated user's ID.
 */
function wp_update_user($userdata) {
	global $wpdb;

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
	$current_user = wp_get_current_user();
	if ( $current_user->id == $ID ) {
		if ( isset($plaintext_pass) ) {
			wp_clearcookie();
			wp_setcookie($userdata['user_login'], $plaintext_pass);
		}
	}

	return $user_id;
}

/**
 * A simpler way of inserting an user into the database.
 * See also: wp_insert_user().
 * @global object $wpdb WordPress database layer.
 * @param string $username The user's username.
 * @param string $password The user's password.
 * @param string $email The user's email (optional).
 * @return int The new user's ID.
 */
function wp_create_user($username, $password, $email = '') {
	global $wpdb;

	$user_login = $wpdb->escape($username);
	$user_email = $wpdb->escape($email);
	$user_pass = $password;

	$userdata = compact('user_login', 'user_email', 'user_pass');
	return wp_insert_user($userdata);
}

/**
 * An alias of wp_create_user().
 * @param string $username The user's username.
 * @param string $password The user's password.
 * @param string $email The user's email (optional).
 * @return int The new user's ID.
 * @deprecated
 */
function create_user($username, $password, $email) {
	return wp_create_user($username, $password, $email);
}

?>