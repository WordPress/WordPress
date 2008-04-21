<?php

function wp_signon( $credentials = '' ) {
	if ( empty($credentials) ) {
		if ( ! empty($_POST['log']) )
			$credentials['user_login'] = $_POST['log'];
		if ( ! empty($_POST['pwd']) )
			$credentials['user_password'] = $_POST['pwd'];
		if ( ! empty($_POST['rememberme']) )
			$credentials['remember'] = $_POST['rememberme'];
	}

	if ( !empty($credentials['user_login']) )
		$credentials['user_login'] = sanitize_user($credentials['user_login']);
	if ( !empty($credentials['user_password']) )
		$credentials['user_password'] = trim($credentials['user_password']);
	if ( !empty($credentials['remember']) )
		$credentials['remember'] = true;
	else
		$credentials['remember'] = false;

	do_action_ref_array('wp_authenticate', array(&$credentials['user_login'], &$credentials['user_password']));

	// If no credential info provided, check cookie.
	if ( empty($credentials['user_login']) && empty($credentials['user_password']) ) {
			$user = wp_validate_auth_cookie();
			if ( $user )
				return new WP_User($user);

			if ( !empty($_COOKIE[AUTH_COOKIE]) )
				return new WP_Error('expired_session', __('Please log in again.'));

			// If the cookie is not set, be silent.
			return new WP_Error();
	}

	if ( empty($credentials['user_login']) || empty($credentials['user_password']) ) {
		$error = new WP_Error();

		if ( empty($credentials['user_login']) )
			$error->add('empty_username', __('<strong>ERROR</strong>: The username field is empty.'));
		if ( empty($credentials['user_password']) )
			$error->add('empty_password', __('<strong>ERROR</strong>: The password field is empty.'));
		return $error;
	}

	$user = wp_authenticate($credentials['user_login'], $credentials['user_password']);
	if ( is_wp_error($user) )
		return $user;

	wp_set_auth_cookie($user->ID, $credentials['remember']);
	do_action('wp_login', $credentials['user_login']);
	return $user;
}

function get_profile($field, $user = false) {
	global $wpdb;
	if ( !$user )
		$user = $wpdb->escape($_COOKIE[USER_COOKIE]);
	return $wpdb->get_var("SELECT $field FROM $wpdb->users WHERE user_login = '$user'");
}

function get_usernumposts($userid) {
	global $wpdb;
	$userid = (int) $userid;
	return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = '$userid' AND post_type = 'post' AND " . get_private_posts_cap_sql('post'));
}

// TODO: xmlrpc only.  Maybe move to xmlrpc.php.
function user_pass_ok($user_login,$user_pass) {
	$user = wp_authenticate($user_login, $user_pass);
	if ( is_wp_error($user) )
		return false;

	return true;
}

//
// User option functions
//

function get_user_option( $option, $user = 0 ) {
	global $wpdb;

	$option = preg_replace('|[^a-z0-9_]|i', '', $option);
	if ( empty($user) )
		$user = wp_get_current_user();
	else
		$user = get_userdata($user);

	if ( isset( $user->{$wpdb->prefix . $option} ) ) // Blog specific
		$result = $user->{$wpdb->prefix . $option};
	elseif ( isset( $user->{$option} ) ) // User specific and cross-blog
		$result = $user->{$option};
	else // Blog global
		$result = get_option( $option );
	
	return apply_filters("get_user_option_{$option}", $result, $option, $user);
}

function update_user_option( $user_id, $option_name, $newvalue, $global = false ) {
	global $wpdb;
	if ( !$global )
		$option_name = $wpdb->prefix . $option_name;
	return update_usermeta( $user_id, $option_name, $newvalue );
}

// Get users with capabilities for the current blog.
// For setups that use the multi-blog feature.
function get_users_of_blog( $id = '' ) {
	global $wpdb, $blog_id;
	if ( empty($id) )
		$id = (int) $blog_id;
	$users = $wpdb->get_results( "SELECT user_id, user_login, display_name, user_email, meta_value FROM $wpdb->users, $wpdb->usermeta WHERE " . $wpdb->users . ".ID = " . $wpdb->usermeta . ".user_id AND meta_key = '" . $wpdb->prefix . "capabilities' ORDER BY {$wpdb->usermeta}.user_id" );
	return $users;
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

	wp_cache_delete($user_id, 'users');

	return true;
}

function get_usermeta( $user_id, $meta_key = '') {
	global $wpdb;
	$user_id = (int) $user_id;

	if ( !$user_id )
		return false;

	if ( !empty($meta_key) ) {
		$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);
		$user = wp_cache_get($user_id, 'users');
		// Check the cached user object
		if ( false !== $user && isset($user->$meta_key) )
			$metas = array($user->$meta_key);
		else
			$metas = $wpdb->get_col( $wpdb->prepare("SELECT meta_value FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s", $user_id, $meta_key) );
	} else {
		$metas = $wpdb->get_col( $wpdb->prepare("SELECT meta_value FROM $wpdb->usermeta WHERE user_id = %d", $user_id) );
	}

	if ( empty($metas) ) {
		if ( empty($meta_key) )
			return array();
		else
			return '';
	}

	$metas = array_map('maybe_unserialize', $metas);

	if ( count($metas) == 1 )
		return $metas[0];
	else
		return $metas;
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

	wp_cache_delete($user_id, 'users');

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
	$user_level	= (int) isset($user->user_level) ? $user->user_level : 0;
	$user_ID	= (int) $user->ID;
	$user_email	= $user->user_email;
	$user_url	= $user->user_url;
	$user_pass_md5	= md5($user->user_pass);
	$user_identity	= $user->display_name;
}

function wp_dropdown_users( $args = '' ) {
	global $wpdb;
	$defaults = array(
		'show_option_all' => '', 'show_option_none' => '',
		'orderby' => 'display_name', 'order' => 'ASC',
		'include' => '', 'exclude' => '',
		'show' => 'display_name', 'echo' => 1,
		'selected' => 0, 'name' => 'user', 'class' => ''
	);

	$defaults['selected'] = is_author() ? get_query_var( 'author' ) : 0;

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$query = "SELECT * FROM $wpdb->users";

	$query_where = array();

	if ( is_array($include) )
		$include = join(',', $include);
	$include = preg_replace('/[^0-9,]/', '', $include); // (int)
	if ( $include )
		$query_where[] = "ID IN ($include)";

	if ( is_array($exclude) )
		$exclude = join(',', $exclude);
	$exclude = preg_replace('/[^0-9,]/', '', $exclude); // (int)
	if ( $exclude )
		$query_where[] = "ID NOT IN ($exclude)";

	if ( $query_where )
		$query .= " WHERE " . join(' AND', $query_where);

	$query .= " ORDER BY $orderby $order";

	$users = $wpdb->get_results( $query );

	$output = '';
	if ( !empty($users) ) {
		$output = "<select name='$name' id='$name' class='$class'>\n";

		if ( $show_option_all )
			$output .= "\t<option value='0'>$show_option_all</option>\n";

		if ( $show_option_none )
			$output .= "\t<option value='-1'>$show_option_none</option>\n";

		foreach ( $users as $user ) {
			$user->ID = (int) $user->ID;
			$_selected = $user->ID == $selected ? " selected='selected'" : '';
			$output .= "\t<option value='$user->ID'$_selected>" . wp_specialchars($user->$show) . "</option>\n";
		}

		$output .= "</select>";
	}

	$output = apply_filters('wp_dropdown_users', $output);

	if ( $echo )
		echo $output;

	return $output;
}

function _fill_user( &$user ) {
	global $wpdb;

	$show = $wpdb->hide_errors();
	$metavalues = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM $wpdb->usermeta WHERE user_id = %d", $user->ID));
	$wpdb->show_errors($show);

	if ( $metavalues ) {
		foreach ( $metavalues as $meta ) {
			$value = maybe_unserialize($meta->meta_value);
			$user->{$meta->meta_key} = $value;
		}
	}

	$level = $wpdb->prefix . 'user_level';
	if ( isset( $user->{$level} ) )
		$user->user_level = $user->{$level};

	// For backwards compat.
	if ( isset($user->first_name) )
		$user->user_firstname = $user->first_name;
	if ( isset($user->last_name) )
		$user->user_lastname = $user->last_name;
	if ( isset($user->description) )
		$user->user_description = $user->description;

	wp_cache_add($user->ID, $user, 'users');
	wp_cache_add($user->user_login, $user->ID, 'userlogins');
	wp_cache_add($user->user_email, $user->ID, 'useremail');
}

?>
