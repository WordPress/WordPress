<?php
/**
 * WordPress User API
 *
 * @package WordPress
 */

/**
 * Authenticate user with remember capability.
 *
 * The credentials is an array that has 'user_login', 'user_password', and
 * 'remember' indices. If the credentials is not given, then the log in form
 * will be assumed and used if set.
 *
 * The various authentication cookies will be set by this function and will be
 * set for a longer period depending on if the 'remember' credential is set to
 * true.
 *
 * @since 2.5.0
 *
 * @param array $credentials Optional. User info in order to sign on.
 * @param bool $secure_cookie Optional. Whether to use secure cookie.
 * @return object Either WP_Error on failure, or WP_User on success.
 */
function wp_signon( $credentials = '', $secure_cookie = '' ) {
	if ( empty($credentials) ) {
		if ( ! empty($_POST['log']) )
			$credentials['user_login'] = $_POST['log'];
		if ( ! empty($_POST['pwd']) )
			$credentials['user_password'] = $_POST['pwd'];
		if ( ! empty($_POST['rememberme']) )
			$credentials['remember'] = $_POST['rememberme'];
	}

	if ( !empty($credentials['remember']) )
		$credentials['remember'] = true;
	else
		$credentials['remember'] = false;

	// TODO do we deprecate the wp_authentication action?
	do_action_ref_array('wp_authenticate', array(&$credentials['user_login'], &$credentials['user_password']));

	if ( '' === $secure_cookie )
		$secure_cookie = is_ssl() ? true : false;

	global $auth_secure_cookie; // XXX ugly hack to pass this to wp_authenticate_cookie
	$auth_secure_cookie = $secure_cookie;

	add_filter('authenticate', 'wp_authenticate_cookie', 30, 3);

	$user = wp_authenticate($credentials['user_login'], $credentials['user_password']);

	if ( is_wp_error($user) ) {
		if ( $user->get_error_codes() == array('empty_username', 'empty_password') ) {
			$user = new WP_Error('', '');
		}

		return $user;
	}

	wp_set_auth_cookie($user->ID, $credentials['remember'], $secure_cookie);
	do_action('wp_login', $credentials['user_login']);
	return $user;
}


/**
 * Authenticate the user using the username and password.
 */
add_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
function wp_authenticate_username_password($user, $username, $password) {
	if ( is_a($user, 'WP_User') ) { return $user; }

	if ( empty($username) || empty($password) ) {
		$error = new WP_Error();

		if ( empty($username) )
			$error->add('empty_username', __('<strong>ERROR</strong>: The username field is empty.'));

		if ( empty($password) )
			$error->add('empty_password', __('<strong>ERROR</strong>: The password field is empty.'));

		return $error;
	}

	$userdata = get_user_by('login', $username);

	if ( !$userdata )
		return new WP_Error('invalid_username', sprintf(__('<strong>ERROR</strong>: Invalid username. <a href="%s" title="Password Lost and Found">Lost your password</a>?'), site_url('wp-login.php?action=lostpassword', 'login')));

	if ( is_multisite() ) {
		// Is user marked as spam?
		if ( 1 == $userdata->spam)
			return new WP_Error('invalid_username', __('<strong>ERROR</strong>: Your account has been marked as a spammer.'));

		// Is a user's blog marked as spam?
		if ( !is_super_admin( $userdata->ID ) && isset($userdata->primary_blog) ) {
			$details = get_blog_details( $userdata->primary_blog );
			if ( is_object( $details ) && $details->spam == 1 )
				return new WP_Error('blog_suspended', __('Blog Suspended.'));
		}
	}

	$userdata = apply_filters('wp_authenticate_user', $userdata, $password);
	if ( is_wp_error($userdata) )
		return $userdata;

	if ( !wp_check_password($password, $userdata->user_pass, $userdata->ID) )
		return new WP_Error('incorrect_password', sprintf(__('<strong>ERROR</strong>: Incorrect password. <a href="%s" title="Password Lost and Found">Lost your password</a>?'), site_url('wp-login.php?action=lostpassword', 'login')));

	$user =  new WP_User($userdata->ID);
	return $user;
}

/**
 * Authenticate the user using the WordPress auth cookie.
 */
function wp_authenticate_cookie($user, $username, $password) {
	if ( is_a($user, 'WP_User') ) { return $user; }

	if ( empty($username) && empty($password) ) {
		$user_id = wp_validate_auth_cookie();
		if ( $user_id )
			return new WP_User($user_id);

		global $auth_secure_cookie;

		if ( $auth_secure_cookie )
			$auth_cookie = SECURE_AUTH_COOKIE;
		else
			$auth_cookie = AUTH_COOKIE;

		if ( !empty($_COOKIE[$auth_cookie]) )
			return new WP_Error('expired_session', __('Please log in again.'));

		// If the cookie is not set, be silent.
	}

	return $user;
}

/**
 * Retrieve user data based on field.
 *
 * Use get_profile() will make a database query to get the value of the table
 * column. The value might be cached using the query cache, but care should be
 * taken when using the function to not make a lot of queries for retrieving
 * user profile information.
 *
 * If the $user parameter is not used, then the user will be retrieved from a
 * cookie of the user. Therefore, if the cookie does not exist, then no value
 * might be returned. Sanity checking must be done to ensure that when using
 * get_profile() that empty/null/false values are handled and that something is
 * at least displayed.
 *
 * @since 1.5.0
 * @uses $wpdb WordPress database object to create queries.
 *
 * @param string $field User field to retrieve.
 * @param string $user Optional. User username.
 * @return string The value in the field.
 */
function get_profile($field, $user = false) {
	global $wpdb;
	if ( !$user )
		$user = esc_sql( $_COOKIE[USER_COOKIE] );
	return $wpdb->get_var( $wpdb->prepare("SELECT $field FROM $wpdb->users WHERE user_login = %s", $user) );
}

/**
 * Number of posts user has written.
 *
 * @since 0.71
 * @uses $wpdb WordPress database object for queries.
 *
 * @param int $userid User ID.
 * @return int Amount of posts user has written.
 */
function get_usernumposts($userid) {
	global $wpdb;
	$userid = (int) $userid;
	$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = %d AND post_type = 'post' AND ", $userid) . get_private_posts_cap_sql('post'));
	return apply_filters('get_usernumposts', $count, $userid);
}

/**
 * Check that the user login name and password is correct.
 *
 * @since 0.71
 * @todo xmlrpc only. Maybe move to xmlrpc.php.
 *
 * @param string $user_login User name.
 * @param string $user_pass User password.
 * @return bool False if does not authenticate, true if username and password authenticates.
 */
function user_pass_ok($user_login, $user_pass) {
	$user = wp_authenticate($user_login, $user_pass);
	if ( is_wp_error($user) )
		return false;

	return true;
}

//
// User option functions
//

/**
 * Retrieve user option that can be either global, user, or blog.
 *
 * If the user ID is not given, then the current user will be used instead. If
 * the user ID is given, then the user data will be retrieved. The filter for
 * the result, will also pass the original option name and finally the user data
 * object as the third parameter.
 *
 * The option will first check for the non-global name, then the global name,
 * and if it still doesn't find it, it will try the blog option. The option can
 * either be modified or set by a plugin.
 *
 * @since 2.0.0
 * @uses $wpdb WordPress database object for queries.
 * @uses apply_filters() Calls 'get_user_option_$option' hook with result,
 *		option parameter, and user data object.
 *
 * @param string $option User option name.
 * @param int $user Optional. User ID.
 * @param bool $deprecated Use get_option() to check for an option in the options table.
 * @return mixed
 */
function get_user_option( $option, $user = 0, $deprecated = '' ) {
	global $wpdb;

	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '3.0' );

	$option = preg_replace('|[^a-z0-9_]|i', '', $option);
	if ( empty($user) )
		$user = wp_get_current_user();
	else
		$user = get_userdata($user);

	if ( isset( $user->{$wpdb->prefix . $option} ) ) // Blog specific
		$result = $user->{$wpdb->prefix . $option};
	elseif ( isset( $user->{$option} ) ) // User specific and cross-blog
		$result = $user->{$option};
	else
		$result = false;

	return apply_filters("get_user_option_{$option}", $result, $option, $user);
}

/**
 * Update user option with global blog capability.
 *
 * User options are just like user metadata except that they have support for
 * global blog options. If the 'global' parameter is false, which it is by default
 * it will prepend the WordPress table prefix to the option name.
 *
 * @since 2.0.0
 * @uses $wpdb WordPress database object for queries
 *
 * @param int $user_id User ID
 * @param string $option_name User option name.
 * @param mixed $newvalue User option value.
 * @param bool $global Optional. Whether option name is blog specific or not.
 * @return unknown
 */
function update_user_option( $user_id, $option_name, $newvalue, $global = false ) {
	global $wpdb;
	if ( !$global )
		$option_name = $wpdb->prefix . $option_name;
	return update_usermeta( $user_id, $option_name, $newvalue );
}

/**
 * Get users for the blog.
 *
 * For setups that use the multi-blog feature. Can be used outside of the
 * multi-blog feature.
 *
 * @since 2.2.0
 * @uses $wpdb WordPress database object for queries
 * @uses $blog_id The Blog id of the blog for those that use more than one blog
 *
 * @param int $id Blog ID.
 * @return array List of users that are part of that Blog ID
 */
function get_users_of_blog( $id = '' ) {
	global $wpdb, $blog_id;
	if ( empty($id) )
		$id = (int) $blog_id;
	$blog_prefix = $wpdb->get_blog_prefix($id);
	$users = $wpdb->get_results( "SELECT user_id, user_id AS ID, user_login, display_name, user_email, meta_value FROM $wpdb->users, $wpdb->usermeta WHERE {$wpdb->users}.ID = {$wpdb->usermeta}.user_id AND meta_key = '{$blog_prefix}capabilities' ORDER BY {$wpdb->usermeta}.user_id" );
	return $users;
}

//
// User meta functions
//

/**
 * Remove user meta data.
 *
 * @since 2.0.0
 * @uses $wpdb WordPress database object for queries.
 *
 * @param int $user_id User ID.
 * @param string $meta_key Metadata key.
 * @param mixed $meta_value Metadata value.
 * @return bool True deletion completed and false if user_id is not a number.
 */
function delete_usermeta( $user_id, $meta_key, $meta_value = '' ) {
	global $wpdb;
	if ( !is_numeric( $user_id ) )
		return false;
	$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);

	if ( is_array($meta_value) || is_object($meta_value) )
		$meta_value = serialize($meta_value);
	$meta_value = trim( $meta_value );

	$cur = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s", $user_id, $meta_key) );

	if ( $cur && $cur->umeta_id )
		do_action( 'delete_usermeta', $cur->umeta_id, $user_id, $meta_key, $meta_value );

	if ( ! empty($meta_value) )
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s AND meta_value = %s", $user_id, $meta_key, $meta_value) );
	else
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s", $user_id, $meta_key) );

	wp_cache_delete($user_id, 'users');

	if ( $cur && $cur->umeta_id )
		do_action( 'deleted_usermeta', $cur->umeta_id, $user_id, $meta_key, $meta_value );

	return true;
}

/**
 * Retrieve user metadata.
 *
 * If $user_id is not a number, then the function will fail over with a 'false'
 * boolean return value. Other returned values depend on whether there is only
 * one item to be returned, which be that single item type. If there is more
 * than one metadata value, then it will be list of metadata values.
 *
 * @since 2.0.0
 * @uses $wpdb WordPress database object for queries.
 *
 * @param int $user_id User ID
 * @param string $meta_key Optional. Metadata key.
 * @return mixed
 */
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

/**
 * Update metadata of user.
 *
 * There is no need to serialize values, they will be serialized if it is
 * needed. The metadata key can only be a string with underscores. All else will
 * be removed.
 *
 * Will remove the metadata, if the meta value is empty.
 *
 * @since 2.0.0
 * @uses $wpdb WordPress database object for queries
 *
 * @param int $user_id User ID
 * @param string $meta_key Metadata key.
 * @param mixed $meta_value Metadata value.
 * @return bool True on successful update, false on failure.
 */
function update_usermeta( $user_id, $meta_key, $meta_value ) {
	global $wpdb;
	if ( !is_numeric( $user_id ) )
		return false;
	$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);

	/** @todo Might need fix because usermeta data is assumed to be already escaped */
	if ( is_string($meta_value) )
		$meta_value = stripslashes($meta_value);
	$meta_value = maybe_serialize($meta_value);

	if (empty($meta_value)) {
		return delete_usermeta($user_id, $meta_key);
	}

	$cur = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s", $user_id, $meta_key) );

	if ( $cur )
		do_action( 'update_usermeta', $cur->umeta_id, $user_id, $meta_key, $meta_value );

	if ( !$cur )
		$wpdb->insert($wpdb->usermeta, compact('user_id', 'meta_key', 'meta_value') );
	else if ( $cur->meta_value != $meta_value )
		$wpdb->update($wpdb->usermeta, compact('meta_value'), compact('user_id', 'meta_key') );
	else
		return false;

	wp_cache_delete($user_id, 'users');

	if ( !$cur )
		do_action( 'added_usermeta', $wpdb->insert_id, $user_id, $meta_key, $meta_value );
	else
		do_action( 'updated_usermeta', $cur->umeta_id, $user_id, $meta_key, $meta_value );

	return true;
}

//
// Private helper functions
//

/**
 * Setup global user vars.
 *
 * Used by set_current_user() for back compat. Might be deprecated in the
 * future.
 *
 * @since 2.0.4
 * @global string $userdata User description.
 * @global string $user_login The user username for logging in
 * @global int $user_level The level of the user
 * @global int $user_ID The ID of the user
 * @global string $user_email The email address of the user
 * @global string $user_url The url in the user's profile
 * @global string $user_pass_md5 MD5 of the user's password
 * @global string $user_identity The display name of the user
 *
 * @param int $for_user_id Optional. User ID to setup global data.
 */
function setup_userdata($for_user_id = '') {
	global $user_login, $userdata, $user_level, $user_ID, $user_email, $user_url, $user_pass_md5, $user_identity;

	if ( '' == $for_user_id )
		$user = wp_get_current_user();
	else
		$user = new WP_User($for_user_id);

	if ( 0 == $user->ID )
		return;

	$userdata = $user->data;
	$user_login	= $user->user_login;
	$user_level	= (int) isset($user->user_level) ? $user->user_level : 0;
	$user_ID = (int) $user->ID;
	$user_email	= $user->user_email;
	$user_url	= $user->user_url;
	$user_pass_md5	= md5($user->user_pass);
	$user_identity	= $user->display_name;
}

/**
 * Create dropdown HTML content of users.
 *
 * The content can either be displayed, which it is by default or retrieved by
 * setting the 'echo' argument. The 'include' and 'exclude' arguments do not
 * need to be used; all users will be displayed in that case. Only one can be
 * used, either 'include' or 'exclude', but not both.
 *
 * The available arguments are as follows:
 * <ol>
 * <li>show_option_all - Text to show all and whether HTML option exists.</li>
 * <li>show_option_none - Text for show none and whether HTML option exists.
 *     </li>
 * <li>orderby - SQL order by clause for what order the users appear. Default is
 * 'display_name'.</li>
 * <li>order - Default is 'ASC'. Can also be 'DESC'.</li>
 * <li>include - User IDs to include.</li>
 * <li>exclude - User IDs to exclude.</li>
 * <li>multi - Default is 'false'. Whether to skip the ID attribute on the 'select' element.</li>
 * <li>show - Default is 'display_name'. User table column to display. If the selected item is empty then the user_login will be displayed in parentesis</li>
 * <li>echo - Default is '1'. Whether to display or retrieve content.</li>
 * <li>selected - Which User ID is selected.</li>
 * <li>name - Default is 'user'. Name attribute of select element.</li>
 * <li>class - Class attribute of select element.</li>
 * </ol>
 *
 * @since 2.3.0
 * @uses $wpdb WordPress database object for queries
 *
 * @param string|array $args Optional. Override defaults.
 * @return string|null Null on display. String of HTML content on retrieve.
 */
function wp_dropdown_users( $args = '' ) {
	global $wpdb;
	$defaults = array(
		'show_option_all' => '', 'show_option_none' => '',
		'orderby' => 'display_name', 'order' => 'ASC',
		'include' => '', 'exclude' => '', 'multi' => 0,
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
		$id = $multi ? "" : "id='$name'";

		$output = "<select name='$name' $id class='$class'>\n";

		if ( $show_option_all )
			$output .= "\t<option value='0'>$show_option_all</option>\n";

		if ( $show_option_none )
			$output .= "\t<option value='-1'>$show_option_none</option>\n";

		foreach ( (array) $users as $user ) {
			$user->ID = (int) $user->ID;
			$_selected = $user->ID == $selected ? " selected='selected'" : '';
			$display = !empty($user->$show) ? $user->$show : '('. $user->user_login . ')';
			$output .= "\t<option value='$user->ID'$_selected>" . esc_html($display) . "</option>\n";
		}

		$output .= "</select>";
	}

	$output = apply_filters('wp_dropdown_users', $output);

	if ( $echo )
		echo $output;

	return $output;
}

/**
 * Add user meta data as properties to given user object.
 *
 * The finished user data is cached, but the cache is not used to fill in the
 * user data for the given object. Once the function has been used, the cache
 * should be used to retrieve user data. The purpose seems then to be to ensure
 * that the data in the object is always fresh.
 *
 * @access private
 * @since 2.5.0
 * @uses $wpdb WordPress database object for queries
 *
 * @param object $user The user data object.
 */
function _fill_user( &$user ) {
	global $wpdb;

	$show = $wpdb->hide_errors();
	$metavalues = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM $wpdb->usermeta WHERE user_id = %d", $user->ID));
	$wpdb->show_errors($show);

	if ( $metavalues ) {
		foreach ( (array) $metavalues as $meta ) {
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
	wp_cache_add($user->user_nicename, $user->ID, 'userslugs');
}

/**
 * Sanitize every user field.
 *
 * If the context is 'raw', then the user object or array will get minimal santization of the int fields.
 *
 * @since 2.3.0
 * @uses sanitize_user_field() Used to sanitize the fields.
 *
 * @param object|array $user The User Object or Array
 * @param string $context Optional, default is 'display'. How to sanitize user fields.
 * @return object|array The now sanitized User Object or Array (will be the same type as $user)
 */
function sanitize_user_object($user, $context = 'display') {
	if ( is_object($user) ) {
		if ( !isset($user->ID) )
			$user->ID = 0;
		if ( isset($user->data) )
			$vars = get_object_vars( $user->data );
		else
			$vars = get_object_vars($user);
		foreach ( array_keys($vars) as $field ) {
			if ( is_string($user->$field) || is_numeric($user->$field) )
				$user->$field = sanitize_user_field($field, $user->$field, $user->ID, $context);
		}
		$user->filter = $context;
	} else {
		if ( !isset($user['ID']) )
			$user['ID'] = 0;
		foreach ( array_keys($user) as $field )
			$user[$field] = sanitize_user_field($field, $user[$field], $user['ID'], $context);
		$user['filter'] = $context;
	}

	return $user;
}

/**
 * Sanitize user field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display', 'attribute' and 'js'. The
 * 'display' context is used by default. 'attribute' and 'js' contexts are treated like 'display'
 * when calling filters.
 *
 * @since 2.3.0
 * @uses apply_filters() Calls 'edit_$field' and '${field_no_prefix}_edit_pre' passing $value and
 *  $user_id if $context == 'edit' and field name prefix == 'user_'.
 *
 * @uses apply_filters() Calls 'edit_user_$field' passing $value and $user_id if $context == 'db'.
 * @uses apply_filters() Calls 'pre_$field' passing $value if $context == 'db' and field name prefix == 'user_'.
 * @uses apply_filters() Calls '${field}_pre' passing $value if $context == 'db' and field name prefix != 'user_'.
 *
 * @uses apply_filters() Calls '$field' passing $value, $user_id and $context if $context == anything
 *  other than 'raw', 'edit' and 'db' and field name prefix == 'user_'.
 * @uses apply_filters() Calls 'user_$field' passing $value if $context == anything other than 'raw',
 *  'edit' and 'db' and field name prefix != 'user_'.
 *
 * @param string $field The user Object field name.
 * @param mixed $value The user Object value.
 * @param int $user_id user ID.
 * @param string $context How to sanitize user fields. Looks for 'raw', 'edit', 'db', 'display',
 *               'attribute' and 'js'.
 * @return mixed Sanitized value.
 */
function sanitize_user_field($field, $value, $user_id, $context) {
	$int_fields = array('ID');
	if ( in_array($field, $int_fields) )
		$value = (int) $value;

	if ( 'raw' == $context )
		return $value;

	if ( !is_string($value) && !is_numeric($value) )
		return $value;

	$prefixed = false;
	if ( false !== strpos($field, 'user_') ) {
		$prefixed = true;
		$field_no_prefix = str_replace('user_', '', $field);
	}

	if ( 'edit' == $context ) {
		if ( $prefixed ) {
			$value = apply_filters("edit_$field", $value, $user_id);
		} else {
			$value = apply_filters("edit_user_$field", $value, $user_id);
		}

		if ( 'description' == $field )
			$value = esc_html($value);
		else
			$value = esc_attr($value);
	} else if ( 'db' == $context ) {
		if ( $prefixed ) {
			$value = apply_filters("pre_$field", $value);
		} else {
			$value = apply_filters("pre_user_$field", $value);
		}
	} else {
		// Use display filters by default.
		if ( $prefixed )
			$value = apply_filters($field, $value, $user_id, $context);
		else
			$value = apply_filters("user_$field", $value, $user_id, $context);
	}

	if ( 'user_url' == $field )
		$value = esc_url($value);

	if ( 'attribute' == $context )
		$value = esc_attr($value);
	else if ( 'js' == $context )
		$value = esc_js($value);

	return $value;
}

/**
 * Clean all user caches
 *
 * @since 3.0
 *
 * @param int $id User ID
 * @return void
 */
function clean_user_cache($id) {
	$user = new WP_User($id);

	wp_cache_delete($id, 'users');
	wp_cache_delete($user->user_login, 'userlogins');
	wp_cache_delete($user->user_email, 'useremail');
	wp_cache_delete($user->user_nicename, 'userslugs');
}

?>
