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
		$secure_cookie = is_ssl();

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
				return new WP_Error('blog_suspended', __('Site Suspended.'));
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
 * @since 3.0.0
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
 * @param array $users User ID number list.
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
 * Get the current user's ID
 *
 * @since MU
 *
 * @uses wp_get_current_user
 *
 * @return int The current user's ID
 */
function get_current_user_id() {
	$user = wp_get_current_user();
	return ( isset( $user->ID ) ? (int) $user->ID : 0 );
}

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

	if ( empty($user) ) {
		$user = wp_get_current_user();
		$user = $user->ID;
	}

	$user = get_userdata($user);

	// Keys used as object vars cannot have dashes.
	$key = str_replace('-', '', $option);

	if ( isset( $user->{$wpdb->prefix . $key} ) ) // Blog specific
		$result = $user->{$wpdb->prefix . $key};
	elseif ( isset( $user->{$key} ) ) // User specific and cross-blog
		$result = $user->{$key};
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
 * Deletes the user option if $newvalue is empty.
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

	// For backward compatibility. See differences between update_user_meta() and deprecated update_usermeta().
	// http://core.trac.wordpress.org/ticket/13088
	if ( is_null( $newvalue ) || is_scalar( $newvalue ) && empty( $newvalue ) )
		return delete_user_meta( $user_id, $option_name );

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
 * WordPress User Query class.
 *
 * @since 3.1.0
 */
class WP_User_Query {

	/**
	 * List of found user ids
	 *
	 * @since 3.1.0
	 * @access private
	 * @var array
	 */
	var $results;

	/**
	 * Total number of found users for the current query
	 *
	 * @since 3.1.0
	 * @access private
	 * @var int
	 */
	var $total_users = 0;

	// SQL pieces
	var $query_from;
	var $query_where;
	var $query_orderby;
	var $query_limit;

	/**
	 * PHP4 constructor
	 */
	function WP_User_Query( $query = null ) {
		$this->__construct( $query );
	}

	/**
	 * PHP5 constructor
	 *
	 * @since 3.1.0
	 *
	 * @param string|array $args The query variables
	 * @return WP_User_Query
	 */
	function __construct( $query = null ) {
		if ( !empty( $query ) ) {
			$this->query_vars = wp_parse_args( $query, array(
				'blog_id' => $GLOBALS['blog_id'],
				'role' => '',
				'meta_key' => '',
				'meta_value' => '',
				'meta_compare' => '',
				'include' => array(),
				'exclude' => array(),
				'search' => '',
				'orderby' => 'login',
				'order' => 'ASC',
				'offset' => '', 'number' => '',
				'count_total' => true,
				'fields' => 'all',
			) );

			$this->prepare_query();
			$this->query();
		}
	}

	/**
	 * Prepare the query variables
	 *
	 * @since 3.1.0
	 * @access private
	 */
	function prepare_query() {
		global $wpdb;

		$qv = &$this->query_vars;

		$this->query_from = " FROM $wpdb->users";
		$this->query_where = " WHERE 1=1";

		// sorting
		if ( in_array( $qv['orderby'], array('email', 'url', 'registered') ) ) {
			$orderby = 'user_' . $qv['orderby'];
		} elseif ( 'name' == $qv['orderby'] ) {
			$orderby = 'display_name';
		} elseif ( 'post_count' == $qv['orderby'] ) {
			$where = get_posts_by_author_sql('post');
			$this->query_from .= " LEFT OUTER JOIN (
				SELECT post_author, COUNT(*) as post_count
				FROM wp_posts
				$where
				GROUP BY post_author
			) p ON ({$wpdb->users}.ID = p.post_author)
			";
			$orderby = 'post_count';
		} elseif ( 'id' == $qv['orderby'] ) {
			$orderby = 'ID';
		} else {
			$orderby = 'user_login';
		}

		$qv['order'] = strtoupper( $qv['order'] );
		if ( 'ASC' == $qv['order'] )
			$order = 'ASC';
		else
			$order = 'DESC';
		$this->query_orderby = " ORDER BY $orderby $order";

		// limit
		if ( $qv['number'] ) {
			if ( $qv['offset'] )
				$this->query_limit = $wpdb->prepare(" LIMIT %d, %d", $qv['offset'], $qv['number']);
			else
				$this->query_limit = $wpdb->prepare(" LIMIT %d", $qv['number']);
		}

		$search = trim( $qv['search'] );
		if ( $search ) {
			$wild = false;
			if ( false !== strpos($search, '*') ) {
				$wild = true;
				$search = trim($search, '*');
			}
			if ( false !== strpos( $search, '@') )
				$search_columns = array('user_email');
			elseif ( is_numeric($search) )
				$search_columns = array('user_login', 'ID');
			elseif ( preg_match('|^https?://|', $search) )
				$search_columns = array('user_url');
			else
				$search_columns = array('user_login', 'user_nicename');

			$this->query_where .= $this->get_search_sql( $search, $search_columns, $wild );
		}

		_parse_meta_query( $qv );

		$role = trim( $qv['role'] );
		$blog_id = absint( $qv['blog_id'] );

		if ( $blog_id ) {
			$cap_meta_query = array();
			$cap_meta_query['key'] = $wpdb->get_blog_prefix( $blog_id ) . 'capabilities';

			if ( $role ) {
				$cap_meta_query['value'] = '"' . $role . '"';
				$cap_meta_query['compare'] = 'like';
			}

			$qv['meta_query'][] = $cap_meta_query;
		}

		if ( !empty( $qv['meta_query'] ) ) {
			$clauses = call_user_func_array( 'get_meta_sql', array( $qv['meta_query'], 'user', $wpdb->users, 'ID', &$this ) );
			$this->query_from .= $clauses['join'];
			$this->query_where .= $clauses['where'];
		}

		if ( !empty( $qv['include'] ) ) {
			$ids = implode( ',', wp_parse_id_list( $qv['include'] ) );
			$this->query_where .= " AND $wpdb->users.ID IN ($ids)";
		}
		elseif ( !empty($qv['exclude']) ) {
			$ids = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
			$this->query_where .= " AND $wpdb->users.ID NOT IN ($ids)";
		}

		do_action_ref_array( 'pre_user_query', array( &$this ) );
	}

	/**
	 * Execute the query, with the current variables
	 *
	 * @since 3.1.0
	 * @access private
	 */
	function query() {
		global $wpdb;

		$this->results = $wpdb->get_col("SELECT $wpdb->users.ID" . $this->query_from . $this->query_where . $this->query_orderby . $this->query_limit);

		if ( !$this->results )
			return;

		if ( $this->query_vars['count_total'] )
			$this->total_users = $wpdb->get_var("SELECT COUNT($wpdb->users.ID)" . $this->query_from . $this->query_where);

		if ( 'all' == $this->query_vars['fields'] ) {
			cache_users($this->results);

			$r = array();
			foreach ( $this->results as $userid )
				$r[ $userid ] = new WP_User( $userid, '', $this->query_vars['blog_id'] );

			$this->results = $r;
		}
	}

	/*
	 * Used internally to generate an SQL string for searching across multiple columns
	 *
	 * @access protected
	 * @since 3.1.0
	 *
	 * @param string $string
	 * @param array $cols
	 * @param bool $wild Whether to allow trailing wildcard searches. Default is false.
	 * @return string
	 */
	function get_search_sql( $string, $cols, $wild = false ) {
		$string = esc_sql( $string );

		$searches = array();
		$wild_char = ( $wild ) ? '%' : '';
		foreach ( $cols as $col ) {
			if ( 'ID' == $col )
				$searches[] = "$col = '$string'";
			else
				$searches[] = "$col LIKE '$string$wild_char'";
		}

		return ' AND (' . implode(' OR ', $searches) . ')';
	}

	/**
	 * Return the list of users
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return array
	 */
	function get_results() {
		return $this->results;
	}

	/**
	 * Return the total number of users for the current query
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return array
	 */
	function get_total() {
		return $this->total_users;
	}
}

/**
 * Retrieve list of users matching criteria.
 *
 * @since 3.1.0
 * @uses $wpdb
 * @uses WP_User_Query See for default arguments and information.
 *
 * @param array $args Optional.
 * @return array List of users.
 */
function get_users( $args = array() ) {

	$args = wp_parse_args( $args );
	$args['count_total'] = false;

	$user_search = new WP_User_Query($args);

	return (array) $user_search->get_results();
}

/**
 * Get users for the blog.
 *
 * For setups that use the multi-blog feature. Can be used outside of the
 * multi-blog feature.
 *
 * @since 2.2.0
 * @uses get_users() for queries
 * @uses $blog_id The Blog id of the blog for those that use more than one blog
 *
 * @param int $id Blog ID.
 * @return array List of users that are part of that Blog ID
 */
function get_users_of_blog( $id = '' ) {
	if ( empty( $id ) )
		$id = get_current_blog_id();

	return get_users( array( 'blog_id' => $id ) );
}

/**
 * Get the blogs a user belongs to.
 *
 * @since 3.0.0
 *
 * @param int $id User Id
 * @param bool $all Whether to retrieve all blogs or only blogs that are not marked as deleted, archived, or spam.
 * @return array A list of the user's blogs. False if the user was not found or an empty array if the user has no blogs.
 */
function get_blogs_of_user( $id, $all = false ) {
	global $wpdb;

	if ( !is_multisite() ) {
		$blog_id = get_current_blog_id();
		$blogs = array();
		$blogs[ $blog_id ]->userblog_id = $blog_id;
		$blogs[ $blog_id ]->blogname = get_option('blogname');
		$blogs[ $blog_id ]->domain = '';
		$blogs[ $blog_id ]->path = '';
		$blogs[ $blog_id ]->site_id = 1;
		$blogs[ $blog_id ]->siteurl = get_option('siteurl');
		return $blogs;
	}

	$blogs = wp_cache_get( 'blogs_of_user-' . $id, 'users' );

	// Try priming the new cache from the old cache
	if ( false === $blogs ) {
		$cache_suffix = $all ? '_all' : '_short';
		$blogs = wp_cache_get( 'blogs_of_user_' . $id . $cache_suffix, 'users' );
		if ( is_array( $blogs ) ) {
			$blogs = array_keys( $blogs );
			if ( $all )
				wp_cache_set( 'blogs_of_user-' . $id, $blogs, 'users' );
		}
	}

	if ( false === $blogs ) {
		$user = get_userdata( (int) $id );
		if ( !$user )
			return false;

		$blogs = $match = array();
		$prefix_length = strlen($wpdb->base_prefix);
		foreach ( (array) $user as $key => $value ) {
			if ( $prefix_length && substr($key, 0, $prefix_length) != $wpdb->base_prefix )
				continue;
			if ( substr($key, -12, 12) != 'capabilities' )
				continue;
			if ( preg_match( '/^' . $wpdb->base_prefix . '((\d+)_)?capabilities$/', $key, $match ) ) {
				if ( count( $match ) > 2 )
					$blogs[] = (int) $match[ 2 ];
				else
					$blogs[] = 1;
			}
		}
		wp_cache_set( 'blogs_of_user-' . $id, $blogs, 'users' );
	}

	$blog_deets = array();
	foreach ( (array) $blogs as $blog_id ) {
		$blog = get_blog_details( $blog_id );
		if ( $blog && isset( $blog->domain ) && ( $all == true || $all == false && ( $blog->archived == 0 && $blog->spam == 0 && $blog->deleted == 0 ) ) ) {
			$blog_deets[ $blog_id ]->userblog_id	= $blog_id;
			$blog_deets[ $blog_id ]->blogname		= $blog->blogname;
			$blog_deets[ $blog_id ]->domain		= $blog->domain;
			$blog_deets[ $blog_id ]->path			= $blog->path;
			$blog_deets[ $blog_id ]->site_id		= $blog->site_id;
			$blog_deets[ $blog_id ]->siteurl		= $blog->siteurl;
		}
	}

	return apply_filters( 'get_blogs_of_user', $blog_deets, $id, $all );
}

/**
 * Checks if the current user belong to a given blog.
 *
 * @since 3.0.0
 *
 * @param int $blog_id Blog ID
 * @return bool True if the current users belong to $blog_id, false if not.
 */
function is_blog_user( $blog_id = 0 ) {
	global $wpdb;

	$current_user = wp_get_current_user();
	if ( !$blog_id )
		$blog_id = $wpdb->blogid;

	$cap_key = $wpdb->base_prefix . $blog_id . '_capabilities';

	if ( is_array($current_user->$cap_key) && in_array(1, $current_user->$cap_key) )
		return true;

	return false;
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
 * @param string $meta_key Metadata name.
 * @param mixed $meta_value Metadata value.
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
 * @since 3.0.0
 * @uses update_metadata
 * @link http://codex.wordpress.org/Function_Reference/update_user_meta
 *
 * @param int $user_id Post ID.
 * @param string $meta_key Metadata key.
 * @param mixed $meta_value Metadata value.
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
 * Assumes role names are unique phrases.  Same assumption made by WP_User_Query::prepare_query()
 * Using $strategy = 'time' this is CPU-intensive and should handle around 10^7 users.
 * Using $strategy = 'memory' this is memory-intensive and should handle around 10^5 users, but see WP Bug #12257.
 *
 * @since 3.0.0
 * @param string $strategy 'time' or 'memory'
 * @return array Includes a grand total and an array of counts indexed by role strings.
 */
function count_users($strategy = 'time') {
	global $wpdb, $wp_roles;

	// Initialize
	$id = get_current_blog_id();
	$blog_prefix = $wpdb->get_blog_prefix($id);
	$result = array();

	if ( 'time' == $strategy ) {
		global $wp_roles;

		if ( ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

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
 * Used by wp_set_current_user() for back compat. Might be deprecated in the future.
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

	$userdata   = $user->data;
	$user_ID    = (int) $user->ID;
	$user_level = (int) isset($user->user_level) ? $user->user_level : 0;

	if ( 0 == $user->ID ) {
		$user_login = $user_email = $user_url = $user_pass_md5 = $user_identity = '';
		return;
	}

	$user_login	= $user->user_login;
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

	$users = get_users( wp_array_slice_assoc( $args, array( 'blog_id', 'include', 'exclude', 'orderby', 'order' ) ) );

	$output = '';
	if ( !empty($users) ) {
		$name = esc_attr( $name );
		if ( $multi && ! $id )
			$id = '';
		else
			$id = $id ? " id='" . esc_attr( $id ) . "'" : " id='$name'";

		$output = "<select name='{$name}'{$id} class='$class'>\n";

		if ( $show_option_all )
			$output .= "\t<option value='0'>$show_option_all</option>\n";

		if ( $show_option_none ) {
			$_selected = selected( -1, $selected, false );
			$output .= "\t<option value='-1'$_selected>$show_option_none</option>\n";
		}

		foreach ( (array) $users as $user ) {
			$user->ID = (int) $user->ID;
			$_selected = selected( $user->ID, $selected, false );
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
	$objects = array();

	$ids = array_map('intval', $ids);
	foreach ( $ids as $id )
		$objects[$id] = array();

	update_meta_cache('user', $ids);

	foreach ( $ids as $id ) {
		$meta = get_metadata('user', $id);
		foreach ( $meta as $key => $metavalues ) {
			foreach ( $metavalues as $value ) {
				$objects[$id][] = (object)array( 'user_id' => $id, 'meta_key' => $key, 'meta_value' => $value);
			}
		}
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
		// Keys used as object vars cannot have dashes.
		$key = str_replace('-', '', $meta->meta_key);
		$user->{$key} = $value;
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
 */
function _fill_many_users( &$users ) {
	$ids = array();
	foreach( $users as $user_object ) {
		$ids[] = $user_object->ID;
	}

	$metas = get_user_metavalues($ids);

	foreach ( $users as $user_object ) {
		if ( isset($metas[$user_object->ID]) ) {
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
 * @uses apply_filters() Calls 'edit_$field' and '{$field_no_prefix}_edit_pre' passing $value and
 *  $user_id if $context == 'edit' and field name prefix == 'user_'.
 *
 * @uses apply_filters() Calls 'edit_user_$field' passing $value and $user_id if $context == 'db'.
 * @uses apply_filters() Calls 'pre_$field' passing $value if $context == 'db' and field name prefix == 'user_'.
 * @uses apply_filters() Calls '{$field}_pre' passing $value if $context == 'db' and field name prefix != 'user_'.
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
			$value = apply_filters("edit_{$field}", $value, $user_id);
		} else {
			$value = apply_filters("edit_user_{$field}", $value, $user_id);
		}

		if ( 'description' == $field )
			$value = esc_html($value);
		else
			$value = esc_attr($value);
	} else if ( 'db' == $context ) {
		if ( $prefixed ) {
			$value = apply_filters("pre_{$field}", $value);
		} else {
			$value = apply_filters("pre_user_{$field}", $value);
		}
	} else {
		// Use display filters by default.
		if ( $prefixed )
			$value = apply_filters($field, $value, $user_id, $context);
		else
			$value = apply_filters("user_{$field}", $value, $user_id, $context);
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
	wp_cache_delete('blogs_of_user-' . $id, 'users');
}

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
 * 'rich_editing' - A string for whether to enable the rich editor. False
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
 * @return int|WP_Error The newly created user's ID or a WP_Error object if the user could not be created.
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

	//Remove any non-printable chars from the login string to see if we have ended up with an empty username
	$user_login = trim($user_login);

	if ( empty($user_login) )
		return new WP_Error('empty_user_login', __('Cannot create a user with an empty login name.') );

	if ( !$update && username_exists( $user_login ) )
		return new WP_Error('existing_user_login', __('This username is already registered.') );

	if ( empty($user_nicename) )
		$user_nicename = sanitize_title( $user_login );
	$user_nicename = apply_filters('pre_user_nicename', $user_nicename);

	if ( empty($user_url) )
		$user_url = '';
	$user_url = apply_filters('pre_user_url', $user_url);

	if ( empty($user_email) )
		$user_email = '';
	$user_email = apply_filters('pre_user_email', $user_email);

	if ( !$update && ! defined( 'WP_IMPORTING' ) && email_exists($user_email) )
		return new WP_Error('existing_user_email', __('This email address is already registered.') );

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

	update_user_meta( $user_id, 'first_name', $first_name);
	update_user_meta( $user_id, 'last_name', $last_name);
	update_user_meta( $user_id, 'nickname', $nickname );
	update_user_meta( $user_id, 'description', $description );
	update_user_meta( $user_id, 'rich_editing', $rich_editing);
	update_user_meta( $user_id, 'comment_shortcuts', $comment_shortcuts);
	update_user_meta( $user_id, 'admin_color', $admin_color);
	update_user_meta( $user_id, 'use_ssl', $use_ssl);

	$user = new WP_User($user_id);

	foreach ( _wp_get_user_contactmethods( $user ) as $method => $name ) {
		if ( empty($$method) )
			$$method = '';

		update_user_meta( $user_id, $method, $$method );
	}

	if ( isset($role) )
		$user->set_role($role);
	elseif ( !$update )
		$user->set_role(get_option('default_role'));

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

	wp_cache_delete($user[ 'user_email' ], 'useremail');

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
 *
 * @param string $username The user's username.
 * @param string $password The user's password.
 * @param string $email The user's email (optional).
 * @return int The new user's ID.
 */
function wp_create_user($username, $password, $email = '') {
	$user_login = esc_sql( $username );
	$user_email = esc_sql( $email    );
	$user_pass = $password;

	$userdata = compact('user_login', 'user_email', 'user_pass');
	return wp_insert_user($userdata);
}


/**
 * Set up the default contact methods
 *
 * @access private
 * @since
 *
 * @param object $user User data object (optional)
 * @return array $user_contactmethods Array of contact methods and their labels.
 */
function _wp_get_user_contactmethods( $user = null ) {
	$user_contactmethods = array(
		'aim' => __('AIM'),
		'yim' => __('Yahoo IM'),
		'jabber' => __('Jabber / Google Talk')
	);
	return apply_filters( 'user_contactmethods', $user_contactmethods, $user );
}

?>
