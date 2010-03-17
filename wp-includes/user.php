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
 * Number of posts user has written.
 *
 * @since 0.71
 * @uses $wpdb WordPress database object for queries.
 *
 * @param int $userid User ID.
 * @return int Amount of posts user has written.
 */
function count_user_posts($userid) {
	global $wpdb;

	$where = get_posts_by_author_sql('post', TRUE, $userid);

	$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );

	return apply_filters('get_usernumposts', $count, $userid);
}

/**
 * Number of posts written by a list of users.
 *
 * @since 3.0.0
 * @param array $userid User ID number list.
 * @return array Amount of posts each user has written.
 */
function count_many_users_posts($users) {
	global $wpdb;

	$count = array();
	if ( ! is_array($users) || empty( $users ) )
		return $count;

	$userlist = implode( ',', $users );
	$where = get_posts_by_author_sql( 'post' );

	$result = $wpdb->get_results( "SELECT post_author, COUNT(*) FROM $wpdb->posts $where AND post_author IN ($userlist) GROUP BY post_author", ARRAY_N );
	foreach ( $result as $row ) {
		$count[ $row[0] ] = $row[1];
	}

	foreach ( $users as $id ) {
		if ( ! isset( $count[ $id ] ) )
			$count[ $id ] = 0;
	}

	return $count;
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
 * Retrieve user option that can be either per Site or per Network.
 *
 * If the user ID is not given, then the current user will be used instead. If
 * the user ID is given, then the user data will be retrieved. The filter for
 * the result, will also pass the original option name and finally the user data
 * object as the third parameter.
 *
 * The option will first check for the per site name and then the per Network name.
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
 * @param bool $global Optional. Whether option name is global or blog specific. Default false (blog specific).
 * @return unknown
 */
function update_user_option( $user_id, $option_name, $newvalue, $global = false ) {
	global $wpdb;

	if ( !$global )
		$option_name = $wpdb->prefix . $option_name;
	return update_user_meta( $user_id, $option_name, $newvalue );
}

/**
 * Delete user option with global blog capability.
 *
 * User options are just like user metadata except that they have support for
 * global blog options. If the 'global' parameter is false, which it is by default
 * it will prepend the WordPress table prefix to the option name.
 *
 * @since 3.0.0
 * @uses $wpdb WordPress database object for queries
 *
 * @param int $user_id User ID
 * @param string $option_name User option name.
 * @param bool $global Optional. Whether option name is global or blog specific. Default false (blog specific).
 * @return unknown
 */
function delete_user_option( $user_id, $option_name, $global = false ) {
	global $wpdb;

	if ( !$global )
		$option_name = $wpdb->prefix . $option_name;
	return delete_user_meta( $user_id, $option_name );
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

/**
 * Add meta data field to a user.
 *
 * Post meta data is called "Custom Fields" on the Administration Panels.
 *
 * @since 3.0.0
 * @uses add_metadata()
 * @link http://codex.wordpress.org/Function_Reference/add_user_meta
 *
 * @param int $user_id Post ID.
 * @param string $key Metadata name.
 * @param mixed $value Metadata value.
 * @param bool $unique Optional, default is false. Whether the same key should not be added.
 * @return bool False for failure. True for success.
 */
function add_user_meta($user_id, $meta_key, $meta_value, $unique = false) {
	return add_metadata('user', $user_id, $meta_key, $meta_value, $unique);
}

/**
 * Remove metadata matching criteria from a user.
 *
 * You can match based on the key, or key and value. Removing based on key and
 * value, will keep from removing duplicate metadata with the same key. It also
 * allows removing all metadata matching key, if needed.
 *
 * @since 3.0.0
 * @uses delete_metadata()
 * @link http://codex.wordpress.org/Function_Reference/delete_user_meta
 *
 * @param int $user_id user ID
 * @param string $meta_key Metadata name.
 * @param mixed $meta_value Optional. Metadata value.
 * @return bool False for failure. True for success.
 */
function delete_user_meta($user_id, $meta_key, $meta_value = '') {
	return delete_metadata('user', $user_id, $meta_key, $meta_value);
}

/**
 * Retrieve user meta field for a user.
 *
 * @since 3.0.0
 * @uses get_metadata()
 * @link http://codex.wordpress.org/Function_Reference/get_user_meta
 *
 * @param int $user_id Post ID.
 * @param string $key The meta key to retrieve.
 * @param bool $single Whether to return a single value.
 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single
 *  is true.
 */
function get_user_meta($user_id, $key, $single = false) {
	return get_metadata('user', $user_id, $key, $single);
}

/**
 * Update user meta field based on user ID.
 *
 * Use the $prev_value parameter to differentiate between meta fields with the
 * same key and user ID.
 *
 * If the meta field for the user does not exist, it will be added.
 *
 * @since 3.0
 * @uses update_metadata
 * @link http://codex.wordpress.org/Function_Reference/update_user_meta
 *
 * @param int $user_id Post ID.
 * @param string $key Metadata key.
 * @param mixed $value Metadata value.
 * @param mixed $prev_value Optional. Previous value to check before removing.
 * @return bool False on failure, true if success.
 */
function update_user_meta($user_id, $meta_key, $meta_value, $prev_value = '') {
	return update_metadata('user', $user_id, $meta_key, $meta_value, $prev_value);
}

/**
 * Count number of users who have each of the user roles.
 *
 * Assumes there are neither duplicated nor orphaned capabilities meta_values.
 * Assumes role names are unique phrases.  Same assumption made by WP_User_Search::prepare_query()
 * Using $strategy = 'time' this is CPU-intensive and should handle around 10^7 users.
 * Using $strategy = 'memory' this is memory-intensive and should handle around 10^5 users, but see WP Bug #12257.
 *
 * @since 3.0.0
 * @param string $strategy 'time' or 'memory'
 * @return array Includes a grand total and an array of counts indexed by role strings.
 */
function count_users($strategy = 'time') {
	global $wpdb, $blog_id, $wp_roles;

	// Initialize
	$id = (int) $blog_id;
	$blog_prefix = $wpdb->get_blog_prefix($id);
	$result = array();

	if ('time' == $strategy) {
		$avail_roles = $wp_roles->get_names();

		// Build a CPU-intensive query that will return concise information.
		$select_count = array();
		foreach ( $avail_roles as $this_role => $name ) {
			$select_count[] = "COUNT(NULLIF(`meta_value` LIKE '%" . like_escape($this_role) . "%', FALSE))";
		}
		$select_count = implode(', ', $select_count);

		// Add the meta_value index to the selection list, then run the query.
		$row = $wpdb->get_row( "SELECT $select_count, COUNT(*) FROM $wpdb->usermeta WHERE meta_key = '{$blog_prefix}capabilities'", ARRAY_N );

		// Run the previous loop again to associate results with role names.
		$col = 0;
		$role_counts = array();
		foreach ( $avail_roles as $this_role => $name ) {
			$count = (int) $row[$col++];
			if ($count > 0) {
				$role_counts[$this_role] = $count;
			}
		}

		// Get the meta_value index from the end of the result set.
		$total_users = (int) $row[$col];

		$result['total_users'] = $total_users;
		$result['avail_roles'] =& $role_counts;
	} else {
		$avail_roles = array();

		$users_of_blog = $wpdb->get_col( "SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = '{$blog_prefix}capabilities'" );

		foreach ( $users_of_blog as $caps_meta ) {
			$b_roles = unserialize($caps_meta);
			if ( is_array($b_roles) ) {
				foreach ( $b_roles as $b_role => $val ) {
					if ( isset($avail_roles[$b_role]) ) {
						$avail_roles[$b_role]++;
					} else {
						$avail_roles[$b_role] = 1;
					}
				}
			}
		}

		$result['total_users'] = count( $users_of_blog );
		$result['avail_roles'] =& $avail_roles;
	}

	return $result;
}

//
// Private helper functions
//

/**
 * Set up global user vars.
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
 * @param int $for_user_id Optional. User ID to set up global data.
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
 * <li>multi - Default is 'false'. Whether to skip the ID attribute on the 'select' element. A 'true' value is overridden when id argument is set.</li>
 * <li>show - Default is 'display_name'. User table column to display. If the selected item is empty then the user_login will be displayed in parentesis</li>
 * <li>echo - Default is '1'. Whether to display or retrieve content.</li>
 * <li>selected - Which User ID is selected.</li>
 * <li>name - Default is 'user'. Name attribute of select element.</li>
 * <li>id - Default is the value of the 'name' parameter. ID attribute of select element.</li>
 * <li>class - Class attribute of select element.</li>
 * <li>blog_id - ID of blog (Multisite only). Defaults to ID of current blog.</li>
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
		'selected' => 0, 'name' => 'user', 'class' => '', 'blog_id' => $GLOBALS['blog_id'],
		'id' => '',
	);

	$defaults['selected'] = is_author() ? get_query_var( 'author' ) : 0;

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$blog_prefix = $wpdb->get_blog_prefix( $blog_id );
	$query = "SELECT {$wpdb->users}.* FROM $wpdb->users, $wpdb->usermeta WHERE {$wpdb->users}.ID = {$wpdb->usermeta}.user_id AND meta_key = '{$blog_prefix}capabilities'";

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
		$query .= " AND " . join(' AND', $query_where);

	$query .= " ORDER BY $orderby $order";

	$users = $wpdb->get_results( $query );

	$output = '';
	if ( !empty($users) ) {
		$name = esc_attr( $name );
		if ( $multi && ! $id )
			$id = '';
		else
			$id = $id ? " id='" . esc_attr( $id ) . "'" : "id='$name'";

		$output = "<select name='{$name}'{$id} class='$class'>\n";

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
 * should be used to retrieve user data. The intention is if the current data
 * had been cached already, there would be no need to call this function.
 *
 * @access private
 * @since 2.5.0
 * @uses $wpdb WordPress database object for queries
 *
 * @param object $user The user data object.
 */
function _fill_user( &$user ) {
	$metavalues = get_user_metavalues(array($user->ID));
	_fill_single_user($user, $metavalues[$user->ID]);
}

/**
 * Perform the query to get the $metavalues array(s) needed by _fill_user and _fill_many_users
 *
 * @since 3.0.0
 * @param array $ids User ID numbers list.
 * @return array of arrays. The array is indexed by user_id, containing $metavalues object arrays.
 */
function get_user_metavalues($ids) {
	global $wpdb;

	$clean = array_map('intval', $ids);
	if ( 0 == count($clean) )
		return $objects;

	$list = implode(',', $clean);

	$show = $wpdb->hide_errors();
	$metavalues = $wpdb->get_results("SELECT user_id, meta_key, meta_value FROM $wpdb->usermeta WHERE user_id IN ($list)");
	$wpdb->show_errors($show);

	$objects = array();
	foreach($clean as $id) {
		$objects[$id] = array();
	}
	foreach($metavalues as $meta_object) {
		$objects[$meta_object->user_id][] = $meta_object;
	}

	return $objects;
}

/**
 * Unserialize user metadata, fill $user object, then cache everything.
 *
 * @since 3.0.0
 * @param object $user The User object.
 * @param array $metavalues An array of objects provided by get_user_metavalues()
 */
function _fill_single_user( &$user, &$metavalues ) {
	global $wpdb;

	foreach ( $metavalues as $meta ) {
		$value = maybe_unserialize($meta->meta_value);
		$user->{$meta->meta_key} = $value;
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

	update_user_caches($user);
}

/**
 * Take an array of user objects, fill them with metas, and cache them.
 *
 * @since 3.0.0
 * @param array $users User objects
 * @param array $metas User metavalues objects
 */
function _fill_many_users( &$users ) {
	$ids = array();
	foreach($users as $user_object) {
		$ids[] = $user_object->ID;
	}

    $metas = get_user_metavalues($ids);

	foreach($users as $user_object) {
		if (isset($metas[$user_object->ID])) {
	        _fill_single_user($user_object, $metas[$user_object->ID]);
		}
	}
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
 * Update all user caches
 *
 * @since 3.0.0
 *
 * @param object $user User object to be cached
 */
function update_user_caches(&$user) {
	wp_cache_add($user->ID, $user, 'users');
	wp_cache_add($user->user_login, $user->ID, 'userlogins');
	wp_cache_add($user->user_email, $user->ID, 'useremail');
	wp_cache_add($user->user_nicename, $user->ID, 'userslugs');
}

/**
 * Clean all user caches
 *
 * @since 3.0.0
 *
 * @param int $id User ID
 */
function clean_user_cache($id) {
	$user = new WP_User($id);

	wp_cache_delete($id, 'users');
	wp_cache_delete($user->user_login, 'userlogins');
	wp_cache_delete($user->user_email, 'useremail');
	wp_cache_delete($user->user_nicename, 'userslugs');
}

?>
