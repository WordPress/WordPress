<?php
/**
 * User Registration API
 *
 * @package WordPress
 */

/**
 * Checks whether the given username exists.
 *
 * @since 2.0.0
 *
 * @param string $username Username.
 * @return null|int The user's ID on success, and null on failure.
 */
function username_exists( $username ) {
	if ( $user = get_userdatabylogin( $username ) ) {
		return $user->ID;
	} else {
		return null;
	}
}

/**
 * Checks whether the given email exists.
 *
 * @since 2.1.0
 * @uses $wpdb
 *
 * @param string $email Email.
 * @return bool|int The user's ID on success, and false on failure.
 */
function email_exists( $email ) {
	if ( $user = get_user_by_email($email) )
		return $user->ID;

	return false;
}

/**
 * Checks whether an username is valid.
 *
 * @since 2.0.1
 * @uses apply_filters() Calls 'validate_username' hook on $valid check and $username as parameters
 *
 * @param string $username Username.
 * @return bool Whether username given is valid
 */
function validate_username( $username ) {
	$sanitized = sanitize_user( $username, true );
	$valid = ( $sanitized == $username );
	return apply_filters( 'validate_username', $valid, $username );
}

/**
 * Insert an user into the database.
 *
 * Can update a current user or insert a new user based on whether the user's ID
 * is present.
 *
 * Can be used to update the user's info (see below), set the user's role, and
 * set the user's preference on whether they want the rich editor on.
 *
 * Most of the $userdata array fields have filters associated with the values.
 * The exceptions are 'rich_editing', 'role', 'jabber', 'aim', 'yim',
 * 'user_registered', and 'ID'. The filters have the prefix 'pre_user_' followed
 * by the field name. An example using 'description' would have the filter
 * called, 'pre_user_description' that can be hooked into.
 *
 * The $userdata array can contain the following fields:
 * 'ID' - An integer that will be used for updating an existing user.
 * 'user_pass' - A string that contains the plain text password for the user.
 * 'user_login' - A string that contains the user's username for logging in.
 * 'user_nicename' - A string that contains a nicer looking name for the user.
 *		The default is the user's username.
 * 'user_url' - A string containing the user's URL for the user's web site.
 * 'user_email' - A string containing the user's email address.
 * 'display_name' - A string that will be shown on the site. Defaults to user's
 *		username. It is likely that you will want to change this, for both
 *		appearance and security through obscurity (that is if you don't use and
 *		delete the default 'admin' user).
 * 'nickname' - The user's nickname, defaults to the user's username.
 * 'first_name' - The user's first name.
 * 'last_name' - The user's last name.
 * 'description' - A string containing content about the user.
 * 'rich_editing' - A string for whether to enable the rich editor or not. False
 *		if not empty.
 * 'user_registered' - The date the user registered. Format is 'Y-m-d H:i:s'.
 * 'role' - A string used to set the user's role.
 * 'jabber' - User's Jabber account.
 * 'aim' - User's AOL IM account.
 * 'yim' - User's Yahoo IM account.
 *
 * @since 2.0.0
 * @uses $wpdb WordPress database layer.
 * @uses apply_filters() Calls filters for most of the $userdata fields with the prefix 'pre_user'. See note above.
 * @uses do_action() Calls 'profile_update' hook when updating giving the user's ID
 * @uses do_action() Calls 'user_register' hook when creating a new user giving the user's ID
 *
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
		$old_user_data = get_userdata($ID);
	} else {
		$update = false;
		// Hash the password
		$user_pass = wp_hash_password($user_pass);
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

	if ( empty($comment_shortcuts) )
		$comment_shortcuts = 'false';

	if ( empty($admin_color) )
		$admin_color = 'fresh';
	$admin_color = preg_replace('|[^a-z0-9 _.\-@]|i', '', $admin_color);

	if ( empty($use_ssl) )
		$use_ssl = 0;

	if ( empty($user_registered) )
		$user_registered = gmdate('Y-m-d H:i:s');

	$user_nicename_check = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND user_login != %s LIMIT 1" , $user_nicename, $user_login));

	if ( $user_nicename_check ) {
		$suffix = 2;
		while ($user_nicename_check) {
			$alt_user_nicename = $user_nicename . "-$suffix";
			$user_nicename_check = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND user_login != %s LIMIT 1" , $alt_user_nicename, $user_login));
			$suffix++;
		}
		$user_nicename = $alt_user_nicename;
	}

	$data = compact( 'user_pass', 'user_email', 'user_url', 'user_nicename', 'display_name', 'user_registered' );
	$data = stripslashes_deep( $data );

	if ( $update ) {
		$wpdb->update( $wpdb->users, $data, compact( 'ID' ) );
		$user_id = (int) $ID;
	} else {
		$wpdb->insert( $wpdb->users, $data + compact( 'user_login' ) );
		$user_id = (int) $wpdb->insert_id;
	}

	update_usermeta( $user_id, 'first_name', $first_name);
	update_usermeta( $user_id, 'last_name', $last_name);
	update_usermeta( $user_id, 'nickname', $nickname );
	update_usermeta( $user_id, 'description', $description );
	update_usermeta( $user_id, 'rich_editing', $rich_editing);
	update_usermeta( $user_id, 'comment_shortcuts', $comment_shortcuts);
	update_usermeta( $user_id, 'admin_color', $admin_color);
	update_usermeta( $user_id, 'use_ssl', $use_ssl);

	foreach ( _wp_get_user_contactmethods() as $method => $name ) {
		if ( empty($$method) )
			$$method = '';

		update_usermeta( $user_id, $method, $$method );
	}

	if ( isset($role) ) {
		$user = new WP_User($user_id);
		$user->set_role($role);
	} elseif ( !$update ) {
		$user = new WP_User($user_id);
		$user->set_role(get_option('default_role'));
	}

	wp_cache_delete($user_id, 'users');
	wp_cache_delete($user_login, 'userlogins');

	if ( $update )
		do_action('profile_update', $user_id, $old_user_data);
	else
		do_action('user_register', $user_id);

	return $user_id;
}

/**
 * Update an user in the database.
 *
 * It is possible to update a user's password by specifying the 'user_pass'
 * value in the $userdata parameter array.
 *
 * If $userdata does not contain an 'ID' key, then a new user will be created
 * and the new user's ID will be returned.
 *
 * If current user's password is being updated, then the cookies will be
 * cleared.
 *
 * @since 2.0.0
 * @see wp_insert_user() For what fields can be set in $userdata
 * @uses wp_insert_user() Used to update existing user or add new one if user doesn't exist already
 *
 * @param array $userdata An array of user data.
 * @return int The updated user's ID.
 */
function wp_update_user($userdata) {
	$ID = (int) $userdata['ID'];

	// First, get all of the original fields
	$user = get_userdata($ID);

	// Escape data pulled from DB.
	$user = add_magic_quotes(get_object_vars($user));

	// If password is changing, hash it now.
	if ( ! empty($userdata['user_pass']) ) {
		$plaintext_pass = $userdata['user_pass'];
		$userdata['user_pass'] = wp_hash_password($userdata['user_pass']);
	}

	// Merge old and new fields with new fields overwriting old ones.
	$userdata = array_merge($user, $userdata);
	$user_id = wp_insert_user($userdata);

	// Update the cookies if the password changed.
	$current_user = wp_get_current_user();
	if ( $current_user->id == $ID ) {
		if ( isset($plaintext_pass) ) {
			wp_clear_auth_cookie();
			wp_set_auth_cookie($ID);
		}
	}

	return $user_id;
}

/**
 * A simpler way of inserting an user into the database.
 *
 * Creates a new user with just the username, password, and email. For a more
 * detail creation of a user, use wp_insert_user() to specify more infomation.
 *
 * @since 2.0.0
 * @see wp_insert_user() More complete way to create a new user
 * @uses $wpdb Escapes $username and $email parameters
 *
 * @param string $username The user's username.
 * @param string $password The user's password.
 * @param string $email The user's email (optional).
 * @return int The new user's ID.
 */
function wp_create_user($username, $password, $email = '') {
	global $wpdb;

	$user_login = esc_sql( $username );
	$user_email = esc_sql( $email    );
	$user_pass = $password;

	$userdata = compact('user_login', 'user_email', 'user_pass');
	return wp_insert_user($userdata);
}


/**
 * Setup the default contact methods
 *
 * @access private
 * @since
 *
 * @return array $user_contactmethods Array of contact methods and their labels.
 */
function _wp_get_user_contactmethods() {
	$user_contactmethods = array(
		'aim' => __('AIM'),
		'yim' => __('Yahoo IM'),
		'jabber' => __('Jabber / Google Talk')
	);
	return apply_filters('user_contactmethods',$user_contactmethods);
}

?>
