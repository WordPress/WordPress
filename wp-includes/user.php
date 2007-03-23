<?php

function get_profile($field, $user = false) {
	global $wpdb;
	if ( !$user )
		$user = $wpdb->escape($_COOKIE[USER_COOKIE]);
	return $wpdb->get_var("SELECT $field FROM $wpdb->users WHERE user_login = '$user'");
}

function get_usernumposts($userid) {
	global $wpdb;
	$userid = (int) $userid;
	return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = '$userid' AND post_type = 'post' AND post_status = 'publish'");
}

// TODO: xmlrpc only.  Maybe move to xmlrpc.php.
function user_pass_ok($user_login,$user_pass) {
	global $cache_userdata;
	if ( empty($cache_userdata[$user_login]) ) {
		$userdata = get_userdatabylogin($user_login);
	} else {
		$userdata = $cache_userdata[$user_login];
	}
	return (md5($user_pass) == $userdata->user_pass);
}

//
// User option functions
//

function get_user_option( $option, $user = 0 ) {
	global $wpdb;

	if ( empty($user) )
		$user = wp_get_current_user();
	else
		$user = get_userdata($user);

	if ( isset( $user->{$wpdb->prefix . $option} ) ) // Blog specific
		return $user->{$wpdb->prefix . $option};
	elseif ( isset( $user->{$option} ) ) // User specific and cross-blog
		return $user->{$option};
	else // Blog global
		return get_option( $option );
}

function update_user_option( $user_id, $option_name, $newvalue, $global = false ) {
	global $wpdb;
	if ( !$global )
		$option_name = $wpdb->prefix . $option_name;
	return update_usermeta( $user_id, $option_name, $newvalue );
}

//
// User meta functions
//

function delete_usermeta( $user_id, $meta_key, $meta_value = '' ) {
	global $wpdb;
	if ( !is_numeric( $user_id ) )
		return false;
	$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);

	if ( is_array($meta_value) || is_object($meta_value) )
		$meta_value = serialize($meta_value);
	$meta_value = trim( $meta_value );

	if ( ! empty($meta_value) )
		$wpdb->query("DELETE FROM $wpdb->usermeta WHERE user_id = '$user_id' AND meta_key = '$meta_key' AND meta_value = '$meta_value'");
	else
		$wpdb->query("DELETE FROM $wpdb->usermeta WHERE user_id = '$user_id' AND meta_key = '$meta_key'");

	$user = get_userdata($user_id);
	wp_cache_delete($user_id, 'users');
	wp_cache_delete($user->user_login, 'userlogins');

	return true;
}

function get_usermeta( $user_id, $meta_key = '') {
	global $wpdb;
	$user_id = (int) $user_id;

	if ( !empty($meta_key) ) {
		$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);
		$metas = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->usermeta WHERE user_id = '$user_id' AND meta_key = '$meta_key'");
	} else {
		$metas = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->usermeta WHERE user_id = '$user_id'");
	}

	if ( empty($metas) ) {
		if ( empty($meta_key) )
			return array();
		else
			return '';
	}

	foreach ($metas as $index => $meta) {
		@ $value = unserialize($meta->meta_value);
		if ( $value === FALSE )
			$value = $meta->meta_value;

		$values[] = $value;
	}

	if ( count($values) == 1 )
		return $values[0];
	else
		return $values;
}

function update_usermeta( $user_id, $meta_key, $meta_value ) {
	global $wpdb;
	if ( !is_numeric( $user_id ) )
		return false;
	$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);

	// FIXME: usermeta data is assumed to be already escaped
	if ( is_string($meta_value) )
		$meta_value = stripslashes($meta_value);
	$meta_value = maybe_serialize($meta_value);
	$meta_value = $wpdb->escape($meta_value);

	if (empty($meta_value)) {
		return delete_usermeta($user_id, $meta_key);
	}

	$cur = $wpdb->get_row("SELECT * FROM $wpdb->usermeta WHERE user_id = '$user_id' AND meta_key = '$meta_key'");
	if ( !$cur ) {
		$wpdb->query("INSERT INTO $wpdb->usermeta ( user_id, meta_key, meta_value )
		VALUES
		( '$user_id', '$meta_key', '$meta_value' )");
	} else if ( $cur->meta_value != $meta_value ) {
		$wpdb->query("UPDATE $wpdb->usermeta SET meta_value = '$meta_value' WHERE user_id = '$user_id' AND meta_key = '$meta_key'");
	} else {
		return false;
	}

	$user = get_userdata($user_id);
	wp_cache_delete($user_id, 'users');
	wp_cache_delete($user->user_login, 'userlogins');

	return true;
}

//
// Private helper functions
//

// Setup global user vars.  Used by set_current_user() for back compat.
function setup_userdata($user_id = '') {
	global $user_login, $userdata, $user_level, $user_ID, $user_email, $user_url, $user_pass_md5, $user_identity;

	if ( '' == $user_id )
		$user = wp_get_current_user();
	else
		$user = new WP_User($user_id);

	if ( 0 == $user->ID )
		return;

	$userdata = $user->data;
	$user_login	= $user->user_login;
	$user_level	= (int) $user->user_level;
	$user_ID	= (int) $user->ID;
	$user_email	= $user->user_email;
	$user_url	= $user->user_url;
	$user_pass_md5	= md5($user->user_pass);
	$user_identity	= $user->display_name;
}

?>