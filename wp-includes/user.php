<?php
/**
 * WordPress User API
 *
 * @package WordPress
 * @subpackage Users
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
 * @global string $auth_secure_cookie
 *
 * @param array       $credentials   Optional. User info in order to sign on.
 * @param string|bool $secure_cookie Optional. Whether to use secure cookie.
 * @return WP_User|WP_Error WP_User on success, WP_Error on failure.
 */
function wp_signon( $credentials = array(), $secure_cookie = '' ) {
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

	/**
	 * Fires before the user is authenticated.
	 *
	 * The variables passed to the callbacks are passed by reference,
	 * and can be modified by callback functions.
	 *
	 * @since 1.5.1
	 *
	 * @todo Decide whether to deprecate the wp_authenticate action.
	 *
	 * @param string $user_login    Username, passed by reference.
	 * @param string $user_password User password, passed by reference.
	 */
	do_action_ref_array( 'wp_authenticate', array( &$credentials['user_login'], &$credentials['user_password'] ) );

	if ( '' === $secure_cookie )
		$secure_cookie = is_ssl();

	/**
	 * Filter whether to use a secure sign-on cookie.
	 *
	 * @since 3.1.0
	 *
	 * @param bool  $secure_cookie Whether to use a secure sign-on cookie.
	 * @param array $credentials {
 	 *     Array of entered sign-on data.
 	 *
 	 *     @type string $user_login    Username.
 	 *     @type string $user_password Password entered.
	 *     @type bool   $remember      Whether to 'remember' the user. Increases the time
	 *                                 that the cookie will be kept. Default false.
 	 * }
	 */
	$secure_cookie = apply_filters( 'secure_signon_cookie', $secure_cookie, $credentials );

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
	/**
	 * Fires after the user has successfully logged in.
	 *
	 * @since 1.5.0
	 *
	 * @param string  $user_login Username.
	 * @param WP_User $user       WP_User object of the logged-in user.
	 */
	do_action( 'wp_login', $user->user_login, $user );
	return $user;
}

/**
 * Authenticate the user using the username and password.
 *
 * @since 2.8.0
 *
 * @param WP_User|WP_Error|null $user     WP_User or WP_Error object from a previous callback. Default null.
 * @param string                $username Username for authentication.
 * @param string                $password Password for authentication.
 * @return WP_User|WP_Error WP_User on success, WP_Error on failure.
 */
function wp_authenticate_username_password($user, $username, $password) {
	if ( $user instanceof WP_User ) {
		return $user;
	}

	if ( empty($username) || empty($password) ) {
		if ( is_wp_error( $user ) )
			return $user;

		$error = new WP_Error();

		if ( empty($username) )
			$error->add('empty_username', __('<strong>ERROR</strong>: The username field is empty.'));

		if ( empty($password) )
			$error->add('empty_password', __('<strong>ERROR</strong>: The password field is empty.'));

		return $error;
	}

	$user = get_user_by('login', $username);

	if ( !$user )
		return new WP_Error( 'invalid_username', sprintf( __( '<strong>ERROR</strong>: Invalid username. <a href="%s">Lost your password?</a>' ), wp_lostpassword_url() ) );

	/**
	 * Filter whether the given user can be authenticated with the provided $password.
	 *
	 * @since 2.5.0
	 *
	 * @param WP_User|WP_Error $user     WP_User or WP_Error object if a previous
	 *                                   callback failed authentication.
	 * @param string           $password Password to check against the user.
	 */
	$user = apply_filters( 'wp_authenticate_user', $user, $password );
	if ( is_wp_error($user) )
		return $user;

	if ( !wp_check_password($password, $user->user_pass, $user->ID) )
		return new WP_Error( 'incorrect_password', sprintf( __( '<strong>ERROR</strong>: The password you entered for the username <strong>%1$s</strong> is incorrect. <a href="%2$s">Lost your password?</a>' ),
		$username, wp_lostpassword_url() ) );

	return $user;
}

/**
 * Authenticate the user using the WordPress auth cookie.
 *
 * @since 2.8.0
 *
 * @global string $auth_secure_cookie
 *
 * @param WP_User|WP_Error|null $user     WP_User or WP_Error object from a previous callback. Default null.
 * @param string                $username Username. If not empty, cancels the cookie authentication.
 * @param string                $password Password. If not empty, cancels the cookie authentication.
 * @return WP_User|WP_Error WP_User on success, WP_Error on failure.
 */
function wp_authenticate_cookie($user, $username, $password) {
	if ( $user instanceof WP_User ) {
		return $user;
	}

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
 * For Multisite blogs, check if the authenticated user has been marked as a
 * spammer, or if the user's primary blog has been marked as spam.
 *
 * @since 3.7.0
 *
 * @param WP_User|WP_Error|null $user WP_User or WP_Error object from a previous callback. Default null.
 * @return WP_User|WP_Error WP_User on success, WP_Error if the user is considered a spammer.
 */
function wp_authenticate_spam_check( $user ) {
	if ( $user instanceof WP_User && is_multisite() ) {
		/**
		 * Filter whether the user has been marked as a spammer.
		 *
		 * @since 3.7.0
		 *
		 * @param bool    $spammed Whether the user is considered a spammer.
		 * @param WP_User $user    User to check against.
		 */
		$spammed = apply_filters( 'check_is_user_spammed', is_user_spammy(), $user );

		if ( $spammed )
			return new WP_Error( 'spammer_account', __( '<strong>ERROR</strong>: Your account has been marked as a spammer.' ) );
	}
	return $user;
}

/**
 * Validate the logged-in cookie.
 *
 * Checks the logged-in cookie if the previous auth cookie could not be
 * validated and parsed.
 *
 * This is a callback for the determine_current_user filter, rather than API.
 *
 * @since 3.9.0
 *
 * @param int|bool $user_id The user ID (or false) as received from the
 *                       determine_current_user filter.
 * @return int|false User ID if validated, false otherwise. If a user ID from
 *                   an earlier filter callback is received, that value is returned.
 */
function wp_validate_logged_in_cookie( $user_id ) {
	if ( $user_id ) {
		return $user_id;
	}

	if ( is_blog_admin() || is_network_admin() || empty( $_COOKIE[LOGGED_IN_COOKIE] ) ) {
		return false;
	}

	return wp_validate_auth_cookie( $_COOKIE[LOGGED_IN_COOKIE], 'logged_in' );
}

/**
 * Number of posts user has written.
 *
 * @since 3.0.0
 * @since 4.1.0 Added `$post_type` argument.
 * @since 4.3.0 Added `$public_only` argument. Added the ability to pass an array of post types to `$post_type`.
 *
 * @global wpdb $wpdb WordPress database object for queries.
 *
 * @param int          $userid      User ID.
 * @param array|string $post_type   Optional. Post type(s) to count the number of posts for. Default 'post'.
 * @param bool         $public_only Optional. Only return counts for public posts. Defaults to false.
 * @return int Number of posts the user has written in this post type.
 */
function count_user_posts( $userid, $post_type = 'post', $public_only = false ) {
	global $wpdb;

	$where = get_posts_by_author_sql( $post_type, true, $userid, $public_only );

	$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );

	/**
	 * Filter the number of posts a user has written.
	 *
	 * @since 2.7.0
	 * @since 4.1.0 Added `$post_type` argument.
	 * @since 4.3.0 Added `$public_only` argument.
	 *
	 * @param int          $count       The user's post count.
	 * @param int          $userid      User ID.
	 * @param string|array $post_types  Post types to count the number of posts for.
	 * @param bool         $public_only Whether to limit counted posts to public posts.
	 */
	return apply_filters( 'get_usernumposts', $count, $userid, $post_type );
}

/**
 * Number of posts written by a list of users.
 *
 * @since 3.0.0
 *
 * @global wpdb $wpdb
 *
 * @param array        $users       Array of user IDs.
 * @param string|array $post_type   Optional. Single post type or array of post types to check. Defaults to 'post'.
 * @param bool         $public_only Optional. Only return counts for public posts.  Defaults to false.
 * @return array Amount of posts each user has written.
 */
function count_many_users_posts( $users, $post_type = 'post', $public_only = false ) {
	global $wpdb;

	$count = array();
	if ( empty( $users ) || ! is_array( $users ) )
		return $count;

	$userlist = implode( ',', array_map( 'absint', $users ) );
	$where = get_posts_by_author_sql( $post_type, true, null, $public_only );

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

//
// User option functions
//

/**
 * Get the current user's ID
 *
 * @since MU
 *
 * @return int The current user's ID
 */
function get_current_user_id() {
	if ( ! function_exists( 'wp_get_current_user' ) )
		return 0;
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
 *
 * @global wpdb $wpdb WordPress database object for queries.
 *
 * @param string $option     User option name.
 * @param int    $user       Optional. User ID.
 * @param string $deprecated Use get_option() to check for an option in the options table.
 * @return mixed User option value on success, false on failure.
 */
function get_user_option( $option, $user = 0, $deprecated = '' ) {
	global $wpdb;

	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '3.0' );

	if ( empty( $user ) )
		$user = get_current_user_id();

	if ( ! $user = get_userdata( $user ) )
		return false;

	$prefix = $wpdb->get_blog_prefix();
	if ( $user->has_prop( $prefix . $option ) ) // Blog specific
		$result = $user->get( $prefix . $option );
	elseif ( $user->has_prop( $option ) ) // User specific and cross-blog
		$result = $user->get( $option );
	else
		$result = false;

	/**
	 * Filter a specific user option value.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the user option name.
	 *
	 * @since 2.5.0
	 *
	 * @param mixed   $result Value for the user's option.
	 * @param string  $option Name of the option being retrieved.
	 * @param WP_User $user   WP_User object of the user whose option is being retrieved.
	 */
	return apply_filters( "get_user_option_{$option}", $result, $option, $user );
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
 *
 * @global wpdb $wpdb WordPress database object for queries.
 *
 * @param int    $user_id     User ID.
 * @param string $option_name User option name.
 * @param mixed  $newvalue    User option value.
 * @param bool   $global      Optional. Whether option name is global or blog specific.
 *                            Default false (blog specific).
 * @return int|bool User meta ID if the option didn't exist, true on successful update,
 *                  false on failure.
 */
function update_user_option( $user_id, $option_name, $newvalue, $global = false ) {
	global $wpdb;

	if ( !$global )
		$option_name = $wpdb->get_blog_prefix() . $option_name;

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
 *
 * @global wpdb $wpdb WordPress database object for queries.
 *
 * @param int    $user_id     User ID
 * @param string $option_name User option name.
 * @param bool   $global      Optional. Whether option name is global or blog specific.
 *                            Default false (blog specific).
 * @return bool True on success, false on failure.
 */
function delete_user_option( $user_id, $option_name, $global = false ) {
	global $wpdb;

	if ( !$global )
		$option_name = $wpdb->get_blog_prefix() . $option_name;
	return delete_user_meta( $user_id, $option_name );
}

/**
 * WordPress User Query class.
 *
 * @since 3.1.0
 *
 * @see WP_User_Query::prepare_query() for information on accepted arguments.
 */
class WP_User_Query {

	/**
	 * Query vars, after parsing
	 *
	 * @since 3.5.0
	 * @access public
	 * @var array
	 */
	public $query_vars = array();

	/**
	 * List of found user ids
	 *
	 * @since 3.1.0
	 * @access private
	 * @var array
	 */
	private $results;

	/**
	 * Total number of found users for the current query
	 *
	 * @since 3.1.0
	 * @access private
	 * @var int
	 */
	private $total_users = 0;

	/**
	 * Metadata query container.
	 *
	 * @since 4.2.0
	 * @access public
	 * @var object WP_Meta_Query
	 */
	public $meta_query = false;

	private $compat_fields = array( 'results', 'total_users' );

	// SQL clauses
	public $query_fields;
	public $query_from;
	public $query_where;
	public $query_orderby;
	public $query_limit;

	/**
	 * PHP5 constructor.
	 *
	 * @since 3.1.0
	 *
	 * @param null|string|array $args Optional. The query variables.
	 */
	public function __construct( $query = null ) {
		if ( ! empty( $query ) ) {
			$this->prepare_query( $query );
			$this->query();
		}
	}

	/**
	 * Prepare the query variables.
	 *
	 * @since 3.1.0
	 * @since 4.2.0 Added 'meta_value_num' support for `$orderby` parameter. Added multi-dimensional array syntax
	 *              for `$orderby` parameter.
	 * @since 4.3.0 Added 'has_published_posts' parameter.
	 * @access public
	 *
	 * @global wpdb $wpdb
	 * @global int  $blog_id
	 *
	 * @param string|array $query {
	 *     Optional. Array or string of Query parameters.
	 *
	 *     @type int          $blog_id             The site ID. Default is the global blog id.
	 *     @type string       $role                Role name. Default empty.
	 *     @type string       $meta_key            User meta key. Default empty.
	 *     @type string       $meta_value          User meta value. Default empty.
	 *     @type string       $meta_compare        Comparison operator to test the `$meta_value`. Accepts '=', '!=',
	 *                                             '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN',
	 *                                             'BETWEEN', 'NOT BETWEEN', 'EXISTS', 'NOT EXISTS', 'REGEXP',
	 *                                             'NOT REGEXP', or 'RLIKE'. Default '='.
	 *     @type array        $include             An array of user IDs to include. Default empty array.
	 *     @type array        $exclude             An array of user IDs to exclude. Default empty array.
	 *     @type string       $search              Search keyword. Searches for possible string matches on columns.
	 *                                             When `$search_columns` is left empty, it tries to determine which
	 *                                             column to search in based on search string. Default empty.
	 *     @type array        $search_columns      Array of column names to be searched. Accepts 'ID', 'login',
	 *                                             'nicename', 'email', 'url'. Default empty array.
	 *     @type string|array $orderby             Field(s) to sort the retrieved users by. May be a single value,
	 *                                             an array of values, or a multi-dimensional array with fields as
	 *                                             keys and orders ('ASC' or 'DESC') as values. Accepted values are
	 *                                             'ID', 'display_name' (or 'name'), 'user_login' (or 'login'),
	 *                                             'user_nicename' (or 'nicename'), 'user_email' (or 'email'),
	 *                                             'user_url' (or 'url'), 'user_registered' (or 'registered'),
	 *                                             'post_count', 'meta_value', 'meta_value_num', the value of
	 *                                             `$meta_key`, or an array key of `$meta_query`. To use
	 *                                             'meta_value' or 'meta_value_num', `$meta_key` must be also be
	 *                                             defined. Default 'user_login'.
	 *     @type string       $order               Designates ascending or descending order of users. Order values
	 *                                             passed as part of an `$orderby` array take precedence over this
	 *                                             parameter. Accepts 'ASC', 'DESC'. Default 'ASC'.
	 *     @type int          $offset              Number of users to offset in retrieved results. Can be used in
	 *                                             conjunction with pagination. Default 0.
	 *     @type int          $number              Number of users to limit the query for. Can be used in
	 *                                             conjunction with pagination. Value -1 (all) is not supported.
	 *                                             Default empty (all users).
	 *     @type bool         $count_total         Whether to count the total number of users found. If pagination
	 *                                             is not needed, setting this to false can improve performance.
	 *                                             Default true.
	 *     @type string|array $fields              Which fields to return. Single or all fields (string), or array
	 *                                             of fields. Accepts 'ID', 'display_name', 'login', 'nicename',
	 *                                             'email', 'url', 'registered'. Use 'all' for all fields and
	 *                                             'all_with_meta' to include meta fields. Default 'all'.
	 *     @type string       $who                 Type of users to query. Accepts 'authors'.
	 *                                             Default empty (all users).
	 *     @type bool|array   $has_published_posts Pass an array of post types to filter results to users who have
	 *                                             published posts in those post types. `true` is an alias for all
	 *                                             public post types.
	 * }
	 */
	public function prepare_query( $query = array() ) {
		global $wpdb;

		if ( empty( $this->query_vars ) || ! empty( $query ) ) {
			$this->query_limit = null;
			$this->query_vars = wp_parse_args( $query, array(
				'blog_id' => $GLOBALS['blog_id'],
				'role' => '',
				'meta_key' => '',
				'meta_value' => '',
				'meta_compare' => '',
				'include' => array(),
				'exclude' => array(),
				'search' => '',
				'search_columns' => array(),
				'orderby' => 'login',
				'order' => 'ASC',
				'offset' => '',
				'number' => '',
				'count_total' => true,
				'fields' => 'all',
				'who' => '',
				'has_published_posts' => null,
			) );
		}

		/**
		 * Fires before the WP_User_Query has been parsed.
		 *
		 * The passed WP_User_Query object contains the query variables, not
		 * yet passed into SQL.
		 *
		 * @since 4.0.0
		 *
		 * @param WP_User_Query $this The current WP_User_Query instance,
		 *                            passed by reference.
		 */
		do_action( 'pre_get_users', $this );

		$qv =& $this->query_vars;

		if ( is_array( $qv['fields'] ) ) {
			$qv['fields'] = array_unique( $qv['fields'] );

			$this->query_fields = array();
			foreach ( $qv['fields'] as $field ) {
				$field = 'ID' === $field ? 'ID' : sanitize_key( $field );
				$this->query_fields[] = "$wpdb->users.$field";
			}
			$this->query_fields = implode( ',', $this->query_fields );
		} elseif ( 'all' == $qv['fields'] ) {
			$this->query_fields = "$wpdb->users.*";
		} else {
			$this->query_fields = "$wpdb->users.ID";
		}

		if ( isset( $qv['count_total'] ) && $qv['count_total'] )
			$this->query_fields = 'SQL_CALC_FOUND_ROWS ' . $this->query_fields;

		$this->query_from = "FROM $wpdb->users";
		$this->query_where = "WHERE 1=1";

		// Parse and sanitize 'include', for use by 'orderby' as well as 'include' below.
		if ( ! empty( $qv['include'] ) ) {
			$include = wp_parse_id_list( $qv['include'] );
		} else {
			$include = false;
		}

		$blog_id = 0;
		if ( isset( $qv['blog_id'] ) ) {
			$blog_id = absint( $qv['blog_id'] );
		}

		if ( isset( $qv['who'] ) && 'authors' == $qv['who'] && $blog_id ) {
			$qv['meta_key'] = $wpdb->get_blog_prefix( $blog_id ) . 'user_level';
			$qv['meta_value'] = 0;
			$qv['meta_compare'] = '!=';
			$qv['blog_id'] = $blog_id = 0; // Prevent extra meta query
		}

		if ( $qv['has_published_posts'] && $blog_id ) {
			if ( true === $qv['has_published_posts'] ) {
				$post_types = get_post_types( array( 'public' => true ) );
			} else {
				$post_types = (array) $qv['has_published_posts'];
			}

			foreach ( $post_types as &$post_type ) {
				$post_type = $wpdb->prepare( '%s', $post_type );
			}

			$posts_table = $wpdb->get_blog_prefix( $blog_id ) . 'posts';
			$this->query_where .= " AND $wpdb->users.ID IN ( SELECT DISTINCT $posts_table.post_author FROM $posts_table WHERE $posts_table.post_status = 'publish' AND $posts_table.post_type IN ( " . join( ", ", $post_types ) . " ) )";
		}

		// Meta query.
		$this->meta_query = new WP_Meta_Query();
		$this->meta_query->parse_query_vars( $qv );

		$role = '';
		if ( isset( $qv['role'] ) ) {
			$role = trim( $qv['role'] );
		}

		if ( $blog_id && ( $role || is_multisite() ) ) {
			$cap_meta_query = array();
			$cap_meta_query['key'] = $wpdb->get_blog_prefix( $blog_id ) . 'capabilities';

			if ( $role ) {
				$cap_meta_query['value'] = '"' . $role . '"';
				$cap_meta_query['compare'] = 'like';
			}

			if ( empty( $this->meta_query->queries ) ) {
				$this->meta_query->queries = array( $cap_meta_query );
			} elseif ( ! in_array( $cap_meta_query, $this->meta_query->queries, true ) ) {
				// Append the cap query to the original queries and reparse the query.
				$this->meta_query->queries = array(
					'relation' => 'AND',
					array( $this->meta_query->queries, $cap_meta_query ),
				);
			}

			$this->meta_query->parse_query_vars( $this->meta_query->queries );
		}

		if ( ! empty( $this->meta_query->queries ) ) {
			$clauses = $this->meta_query->get_sql( 'user', $wpdb->users, 'ID', $this );
			$this->query_from .= $clauses['join'];
			$this->query_where .= $clauses['where'];

			if ( $this->meta_query->has_or_relation() ) {
				$this->query_fields = 'DISTINCT ' . $this->query_fields;
			}
		}

		// sorting
		$qv['order'] = isset( $qv['order'] ) ? strtoupper( $qv['order'] ) : '';
		$order = $this->parse_order( $qv['order'] );

		if ( empty( $qv['orderby'] ) ) {
			// Default order is by 'user_login'.
			$ordersby = array( 'user_login' => $order );
		} elseif ( is_array( $qv['orderby'] ) ) {
			$ordersby = $qv['orderby'];
		} else {
			// 'orderby' values may be a comma- or space-separated list.
			$ordersby = preg_split( '/[,\s]+/', $qv['orderby'] );
		}

		$orderby_array = array();
		foreach ( $ordersby as $_key => $_value ) {
			if ( ! $_value ) {
				continue;
			}

			if ( is_int( $_key ) ) {
				// Integer key means this is a flat array of 'orderby' fields.
				$_orderby = $_value;
				$_order = $order;
			} else {
				// Non-integer key means this the key is the field and the value is ASC/DESC.
				$_orderby = $_key;
				$_order = $_value;
			}

			$parsed = $this->parse_orderby( $_orderby );

			if ( ! $parsed ) {
				continue;
			}

			$orderby_array[] = $parsed . ' ' . $this->parse_order( $_order );
		}

		// If no valid clauses were found, order by user_login.
		if ( empty( $orderby_array ) ) {
			$orderby_array[] = "user_login $order";
		}

		$this->query_orderby = 'ORDER BY ' . implode( ', ', $orderby_array );

		// limit
		if ( isset( $qv['number'] ) && $qv['number'] ) {
			if ( $qv['offset'] )
				$this->query_limit = $wpdb->prepare("LIMIT %d, %d", $qv['offset'], $qv['number']);
			else
				$this->query_limit = $wpdb->prepare("LIMIT %d", $qv['number']);
		}

		$search = '';
		if ( isset( $qv['search'] ) )
			$search = trim( $qv['search'] );

		if ( $search ) {
			$leading_wild = ( ltrim($search, '*') != $search );
			$trailing_wild = ( rtrim($search, '*') != $search );
			if ( $leading_wild && $trailing_wild )
				$wild = 'both';
			elseif ( $leading_wild )
				$wild = 'leading';
			elseif ( $trailing_wild )
				$wild = 'trailing';
			else
				$wild = false;
			if ( $wild )
				$search = trim($search, '*');

			$search_columns = array();
			if ( $qv['search_columns'] )
				$search_columns = array_intersect( $qv['search_columns'], array( 'ID', 'user_login', 'user_email', 'user_url', 'user_nicename' ) );
			if ( ! $search_columns ) {
				if ( false !== strpos( $search, '@') )
					$search_columns = array('user_email');
				elseif ( is_numeric($search) )
					$search_columns = array('user_login', 'ID');
				elseif ( preg_match('|^https?://|', $search) && ! ( is_multisite() && wp_is_large_network( 'users' ) ) )
					$search_columns = array('user_url');
				else
					$search_columns = array('user_login', 'user_url', 'user_email', 'user_nicename', 'display_name');
			}

			/**
			 * Filter the columns to search in a WP_User_Query search.
			 *
			 * The default columns depend on the search term, and include 'user_email',
			 * 'user_login', 'ID', 'user_url', and 'user_nicename'.
			 *
			 * @since 3.6.0
			 *
			 * @param array         $search_columns Array of column names to be searched.
			 * @param string        $search         Text being searched.
			 * @param WP_User_Query $this           The current WP_User_Query instance.
			 */
			$search_columns = apply_filters( 'user_search_columns', $search_columns, $search, $this );

			$this->query_where .= $this->get_search_sql( $search, $search_columns, $wild );
		}

		if ( ! empty( $include ) ) {
			// Sanitized earlier.
			$ids = implode( ',', $include );
			$this->query_where .= " AND $wpdb->users.ID IN ($ids)";
		} elseif ( ! empty( $qv['exclude'] ) ) {
			$ids = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
			$this->query_where .= " AND $wpdb->users.ID NOT IN ($ids)";
		}

		// Date queries are allowed for the user_registered field.
		if ( ! empty( $qv['date_query'] ) && is_array( $qv['date_query'] ) ) {
			$date_query = new WP_Date_Query( $qv['date_query'], 'user_registered' );
			$this->query_where .= $date_query->get_sql();
		}

		/**
		 * Fires after the WP_User_Query has been parsed, and before
		 * the query is executed.
		 *
		 * The passed WP_User_Query object contains SQL parts formed
		 * from parsing the given query.
		 *
		 * @since 3.1.0
		 *
		 * @param WP_User_Query $this The current WP_User_Query instance,
		 *                            passed by reference.
		 */
		do_action_ref_array( 'pre_user_query', array( &$this ) );
	}

	/**
	 * Execute the query, with the current variables.
	 *
	 * @since 3.1.0
	 *
	 * @global wpdb $wpdb WordPress database object for queries.
	 */
	public function query() {
		global $wpdb;

		$qv =& $this->query_vars;

		$query = "SELECT $this->query_fields $this->query_from $this->query_where $this->query_orderby $this->query_limit";

		if ( is_array( $qv['fields'] ) || 'all' == $qv['fields'] ) {
			$this->results = $wpdb->get_results( $query );
		} else {
			$this->results = $wpdb->get_col( $query );
		}

		/**
		 * Filter SELECT FOUND_ROWS() query for the current WP_User_Query instance.
		 *
		 * @since 3.2.0
		 *
		 * @global wpdb $wpdb WordPress database abstraction object.
		 *
		 * @param string $sql The SELECT FOUND_ROWS() query for the current WP_User_Query.
		 */
		if ( isset( $qv['count_total'] ) && $qv['count_total'] )
			$this->total_users = $wpdb->get_var( apply_filters( 'found_users_query', 'SELECT FOUND_ROWS()' ) );

		if ( !$this->results )
			return;

		if ( 'all_with_meta' == $qv['fields'] ) {
			cache_users( $this->results );

			$r = array();
			foreach ( $this->results as $userid )
				$r[ $userid ] = new WP_User( $userid, '', $qv['blog_id'] );

			$this->results = $r;
		} elseif ( 'all' == $qv['fields'] ) {
			foreach ( $this->results as $key => $user ) {
				$this->results[ $key ] = new WP_User( $user, '', $qv['blog_id'] );
			}
		}
	}

	/**
	 * Retrieve query variable.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param string $query_var Query variable key.
	 * @return mixed
	 */
	public function get( $query_var ) {
		if ( isset( $this->query_vars[$query_var] ) )
			return $this->query_vars[$query_var];

		return null;
	}

	/**
	 * Set query variable.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param string $query_var Query variable key.
	 * @param mixed $value Query variable value.
	 */
	public function set( $query_var, $value ) {
		$this->query_vars[$query_var] = $value;
	}

	/**
	 * Used internally to generate an SQL string for searching across multiple columns
	 *
	 * @access protected
	 * @since 3.1.0
	 *
	 * @global wpdb $wpdb
	 *
	 * @param string $string
	 * @param array  $cols
	 * @param bool   $wild   Whether to allow wildcard searches. Default is false for Network Admin, true for single site.
	 *                       Single site allows leading and trailing wildcards, Network Admin only trailing.
	 * @return string
	 */
	protected function get_search_sql( $string, $cols, $wild = false ) {
		global $wpdb;

		$searches = array();
		$leading_wild = ( 'leading' == $wild || 'both' == $wild ) ? '%' : '';
		$trailing_wild = ( 'trailing' == $wild || 'both' == $wild ) ? '%' : '';
		$like = $leading_wild . $wpdb->esc_like( $string ) . $trailing_wild;

		foreach ( $cols as $col ) {
			if ( 'ID' == $col ) {
				$searches[] = $wpdb->prepare( "$col = %s", $string );
			} else {
				$searches[] = $wpdb->prepare( "$col LIKE %s", $like );
			}
		}

		return ' AND (' . implode(' OR ', $searches) . ')';
	}

	/**
	 * Return the list of users.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return array Array of results.
	 */
	public function get_results() {
		return $this->results;
	}

	/**
	 * Return the total number of users for the current query.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return int Number of total users.
	 */
	public function get_total() {
		return $this->total_users;
	}

	/**
	 * Parse and sanitize 'orderby' keys passed to the user query.
	 *
	 * @since 4.2.0
	 * @access protected
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $orderby Alias for the field to order by.
	 * @return string Value to used in the ORDER clause, if `$orderby` is valid.
	 */
	protected function parse_orderby( $orderby ) {
		global $wpdb;

		$meta_query_clauses = $this->meta_query->get_clauses();

		$_orderby = '';
		if ( in_array( $orderby, array( 'login', 'nicename', 'email', 'url', 'registered' ) ) ) {
			$_orderby = 'user_' . $orderby;
		} elseif ( in_array( $orderby, array( 'user_login', 'user_nicename', 'user_email', 'user_url', 'user_registered' ) ) ) {
			$_orderby = $orderby;
		} elseif ( 'name' == $orderby || 'display_name' == $orderby ) {
			$_orderby = 'display_name';
		} elseif ( 'post_count' == $orderby ) {
			// todo: avoid the JOIN
			$where = get_posts_by_author_sql( 'post' );
			$this->query_from .= " LEFT OUTER JOIN (
				SELECT post_author, COUNT(*) as post_count
				FROM $wpdb->posts
				$where
				GROUP BY post_author
			) p ON ({$wpdb->users}.ID = p.post_author)
			";
			$_orderby = 'post_count';
		} elseif ( 'ID' == $orderby || 'id' == $orderby ) {
			$_orderby = 'ID';
		} elseif ( 'meta_value' == $orderby || $this->get( 'meta_key' ) == $orderby ) {
			$_orderby = "$wpdb->usermeta.meta_value";
		} elseif ( 'meta_value_num' == $orderby ) {
			$_orderby = "$wpdb->usermeta.meta_value+0";
		} elseif ( 'include' === $orderby && ! empty( $this->query_vars['include'] ) ) {
			$include = wp_parse_id_list( $this->query_vars['include'] );
			$include_sql = implode( ',', $include );
			$_orderby = "FIELD( $wpdb->users.ID, $include_sql )";
		} elseif ( isset( $meta_query_clauses[ $orderby ] ) ) {
			$meta_clause = $meta_query_clauses[ $orderby ];
			$_orderby = sprintf( "CAST(%s.meta_value AS %s)", esc_sql( $meta_clause['alias'] ), esc_sql( $meta_clause['cast'] ) );
		}

		return $_orderby;
	}

	/**
	 * Parse an 'order' query variable and cast it to ASC or DESC as necessary.
	 *
	 * @since 4.2.0
	 * @access protected
	 *
	 * @param string $order The 'order' query variable.
	 * @return string The sanitized 'order' query variable.
	 */
	protected function parse_order( $order ) {
		if ( ! is_string( $order ) || empty( $order ) ) {
			return 'DESC';
		}

		if ( 'ASC' === strtoupper( $order ) ) {
			return 'ASC';
		} else {
			return 'DESC';
		}
	}

	/**
	 * Make private properties readable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to get.
	 * @return mixed Property.
	 */
	public function __get( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return $this->$name;
		}
	}

	/**
	 * Make private properties settable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name  Property to check if set.
	 * @param mixed  $value Property value.
	 * @return mixed Newly-set property.
	 */
	public function __set( $name, $value ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return $this->$name = $value;
		}
	}

	/**
	 * Make private properties checkable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to check if set.
	 * @return bool Whether the property is set.
	 */
	public function __isset( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return isset( $this->$name );
		}
	}

	/**
	 * Make private properties un-settable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to unset.
	 */
	public function __unset( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			unset( $this->$name );
		}
	}

	/**
	 * Make private/protected methods readable for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param callable $name      Method to call.
	 * @param array    $arguments Arguments to pass when calling.
	 * @return mixed Return value of the callback, false otherwise.
	 */
	public function __call( $name, $arguments ) {
		if ( 'get_search_sql' === $name ) {
			return call_user_func_array( array( $this, $name ), $arguments );
		}
		return false;
	}
}

/**
 * Retrieve list of users matching criteria.
 *
 * @since 3.1.0
 *
 * @see WP_User_Query
 *
 * @param array $args Optional. Arguments to retrieve users. See {@see WP_User_Query::prepare_query()}
 *                    for more information on accepted arguments.
 * @return array List of users.
 */
function get_users( $args = array() ) {

	$args = wp_parse_args( $args );
	$args['count_total'] = false;

	$user_search = new WP_User_Query($args);

	return (array) $user_search->get_results();
}

/**
 * Get the blogs a user belongs to.
 *
 * @since 3.0.0
 *
 * @global wpdb $wpdb WordPress database object for queries.
 *
 * @param int  $user_id User ID
 * @param bool $all     Whether to retrieve all blogs, or only blogs that are not
 *                      marked as deleted, archived, or spam.
 * @return array A list of the user's blogs. An empty array if the user doesn't exist
 *               or belongs to no blogs.
 */
function get_blogs_of_user( $user_id, $all = false ) {
	global $wpdb;

	$user_id = (int) $user_id;

	// Logged out users can't have blogs
	if ( empty( $user_id ) )
		return array();

	$keys = get_user_meta( $user_id );
	if ( empty( $keys ) )
		return array();

	if ( ! is_multisite() ) {
		$blog_id = get_current_blog_id();
		$blogs = array( $blog_id => new stdClass );
		$blogs[ $blog_id ]->userblog_id = $blog_id;
		$blogs[ $blog_id ]->blogname = get_option('blogname');
		$blogs[ $blog_id ]->domain = '';
		$blogs[ $blog_id ]->path = '';
		$blogs[ $blog_id ]->site_id = 1;
		$blogs[ $blog_id ]->siteurl = get_option('siteurl');
		$blogs[ $blog_id ]->archived = 0;
		$blogs[ $blog_id ]->spam = 0;
		$blogs[ $blog_id ]->deleted = 0;
		return $blogs;
	}

	$blogs = array();

	if ( isset( $keys[ $wpdb->base_prefix . 'capabilities' ] ) && defined( 'MULTISITE' ) ) {
		$blog = get_blog_details( 1 );
		if ( $blog && isset( $blog->domain ) && ( $all || ( ! $blog->archived && ! $blog->spam && ! $blog->deleted ) ) ) {
			$blogs[ 1 ] = (object) array(
				'userblog_id' => 1,
				'blogname'    => $blog->blogname,
				'domain'      => $blog->domain,
				'path'        => $blog->path,
				'site_id'     => $blog->site_id,
				'siteurl'     => $blog->siteurl,
				'archived'    => $blog->archived,
				'mature'      => $blog->mature,
				'spam'        => $blog->spam,
				'deleted'     => $blog->deleted,
			);
		}
		unset( $keys[ $wpdb->base_prefix . 'capabilities' ] );
	}

	$keys = array_keys( $keys );

	foreach ( $keys as $key ) {
		if ( 'capabilities' !== substr( $key, -12 ) )
			continue;
		if ( $wpdb->base_prefix && 0 !== strpos( $key, $wpdb->base_prefix ) )
			continue;
		$blog_id = str_replace( array( $wpdb->base_prefix, '_capabilities' ), '', $key );
		if ( ! is_numeric( $blog_id ) )
			continue;

		$blog_id = (int) $blog_id;
		$blog = get_blog_details( $blog_id );
		if ( $blog && isset( $blog->domain ) && ( $all || ( ! $blog->archived && ! $blog->spam && ! $blog->deleted ) ) ) {
			$blogs[ $blog_id ] = (object) array(
				'userblog_id' => $blog_id,
				'blogname'    => $blog->blogname,
				'domain'      => $blog->domain,
				'path'        => $blog->path,
				'site_id'     => $blog->site_id,
				'siteurl'     => $blog->siteurl,
				'archived'    => $blog->archived,
				'mature'      => $blog->mature,
				'spam'        => $blog->spam,
				'deleted'     => $blog->deleted,
			);
		}
	}

	/**
	 * Filter the list of blogs a user belongs to.
	 *
	 * @since MU
	 *
	 * @param array $blogs   An array of blog objects belonging to the user.
	 * @param int   $user_id User ID.
	 * @param bool  $all     Whether the returned blogs array should contain all blogs, including
	 *                       those marked 'deleted', 'archived', or 'spam'. Default false.
	 */
	return apply_filters( 'get_blogs_of_user', $blogs, $user_id, $all );
}

/**
 * Find out whether a user is a member of a given blog.
 *
 * @since MU 1.1
 *
 * @param int $user_id Optional. The unique ID of the user. Defaults to the current user.
 * @param int $blog_id Optional. ID of the blog to check. Defaults to the current site.
 * @return bool
 */
function is_user_member_of_blog( $user_id = 0, $blog_id = 0 ) {
	$user_id = (int) $user_id;
	$blog_id = (int) $blog_id;

	if ( empty( $user_id ) )
		$user_id = get_current_user_id();

	if ( empty( $blog_id ) )
		$blog_id = get_current_blog_id();

	$blogs = get_blogs_of_user( $user_id );
	return array_key_exists( $blog_id, $blogs );
}

/**
 * Add meta data field to a user.
 *
 * Post meta data is called "Custom Fields" on the Administration Screens.
 *
 * @since 3.0.0
 * @link https://codex.wordpress.org/Function_Reference/add_user_meta
 *
 * @param int    $user_id    User ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Metadata value.
 * @param bool   $unique     Optional, default is false. Whether the same key should not be added.
 * @return int|false Meta ID on success, false on failure.
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
 * @link https://codex.wordpress.org/Function_Reference/delete_user_meta
 *
 * @param int    $user_id    User ID
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Optional. Metadata value.
 * @return bool True on success, false on failure.
 */
function delete_user_meta($user_id, $meta_key, $meta_value = '') {
	return delete_metadata('user', $user_id, $meta_key, $meta_value);
}

/**
 * Retrieve user meta field for a user.
 *
 * @since 3.0.0
 * @link https://codex.wordpress.org/Function_Reference/get_user_meta
 *
 * @param int    $user_id User ID.
 * @param string $key     Optional. The meta key to retrieve. By default, returns data for all keys.
 * @param bool   $single  Whether to return a single value.
 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function get_user_meta($user_id, $key = '', $single = false) {
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
 * @link https://codex.wordpress.org/Function_Reference/update_user_meta
 *
 * @param int    $user_id    User ID.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value.
 * @param mixed  $prev_value Optional. Previous value to check before removing.
 * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function update_user_meta($user_id, $meta_key, $meta_value, $prev_value = '') {
	return update_metadata('user', $user_id, $meta_key, $meta_value, $prev_value);
}

/**
 * Count number of users who have each of the user roles.
 *
 * Assumes there are neither duplicated nor orphaned capabilities meta_values.
 * Assumes role names are unique phrases. Same assumption made by WP_User_Query::prepare_query()
 * Using $strategy = 'time' this is CPU-intensive and should handle around 10^7 users.
 * Using $strategy = 'memory' this is memory-intensive and should handle around 10^5 users, but see WP Bug #12257.
 *
 * @since 3.0.0
 *
 * @global wpdb $wpdb
 *
 * @param string $strategy 'time' or 'memory'
 * @return array Includes a grand total and an array of counts indexed by role strings.
 */
function count_users($strategy = 'time') {
	global $wpdb;

	// Initialize
	$id = get_current_blog_id();
	$blog_prefix = $wpdb->get_blog_prefix($id);
	$result = array();

	if ( 'time' == $strategy ) {
		$avail_roles = wp_roles()->get_names();

		// Build a CPU-intensive query that will return concise information.
		$select_count = array();
		foreach ( $avail_roles as $this_role => $name ) {
			$select_count[] = $wpdb->prepare( "COUNT(NULLIF(`meta_value` LIKE %s, false))", '%' . $wpdb->esc_like( '"' . $this_role . '"' ) . '%');
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
			$b_roles = maybe_unserialize($caps_meta);
			if ( ! is_array( $b_roles ) )
				continue;
			foreach ( $b_roles as $b_role => $val ) {
				if ( isset($avail_roles[$b_role]) ) {
					$avail_roles[$b_role]++;
				} else {
					$avail_roles[$b_role] = 1;
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
 *
 * @global string $user_login    The user username for logging in
 * @global object $userdata      User data.
 * @global int    $user_level    The level of the user
 * @global int    $user_ID       The ID of the user
 * @global string $user_email    The email address of the user
 * @global string $user_url      The url in the user's profile
 * @global string $user_identity The display name of the user
 *
 * @param int $for_user_id Optional. User ID to set up global data.
 */
function setup_userdata($for_user_id = '') {
	global $user_login, $userdata, $user_level, $user_ID, $user_email, $user_url, $user_identity;

	if ( '' == $for_user_id )
		$for_user_id = get_current_user_id();
	$user = get_userdata( $for_user_id );

	if ( ! $user ) {
		$user_ID = 0;
		$user_level = 0;
		$userdata = null;
		$user_login = $user_email = $user_url = $user_identity = '';
		return;
	}

	$user_ID    = (int) $user->ID;
	$user_level = (int) $user->user_level;
	$userdata   = $user;
	$user_login = $user->user_login;
	$user_email = $user->user_email;
	$user_url   = $user->user_url;
	$user_identity = $user->display_name;
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
 *
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database object for queries.
 * @global int  $blog_id
 *
 * @param array|string $args {
 *     Optional. Array or string of arguments to generate a drop-down of users.
 *     {@see WP_User_Query::prepare_query() for additional available arguments.
 *
 *     @type string       $show_option_all         Text to show as the drop-down default (all).
 *                                                 Default empty.
 *     @type string       $show_option_none        Text to show as the drop-down default when no
 *                                                 users were found. Default empty.
 *     @type int|string   $option_none_value       Value to use for $show_option_non when no users
 *                                                 were found. Default -1.
 *     @type string       $hide_if_only_one_author Whether to skip generating the drop-down
 *                                                 if only one user was found. Default empty.
 *     @type string       $orderby                 Field to order found users by. Accepts user fields.
 *                                                 Default 'display_name'.
 *     @type string       $order                   Whether to order users in ascending or descending
 *                                                 order. Accepts 'ASC' (ascending) or 'DESC' (descending).
 *                                                 Default 'ASC'.
 *     @type array|string $include                 Array or comma-separated list of user IDs to include.
 *                                                 Default empty.
 *     @type array|string $exclude                 Array or comma-separated list of user IDs to exclude.
 *                                                 Default empty.
 *     @type bool|int     $multi                   Whether to skip the ID attribute on the 'select' element.
 *                                                 Accepts 1|true or 0|false. Default 0|false.
 *     @type string       $show                    User table column to display. If the selected item is empty
 *                                                 then the 'user_login' will be displayed in parentheses.
 *                                                 Accepts user fields. Default 'display_name'.
 *     @type int|bool     $echo                    Whether to echo or return the drop-down. Accepts 1|true (echo)
 *                                                 or 0|false (return). Default 1|true.
 *     @type int          $selected                Which user ID should be selected. Default 0.
 *     @type bool         $include_selected        Whether to always include the selected user ID in the drop-
 *                                                 down. Default false.
 *     @type string       $name                    Name attribute of select element. Default 'user'.
 *     @type string       $id                      ID attribute of the select element. Default is the value of $name.
 *     @type string       $class                   Class attribute of the select element. Default empty.
 *     @type int          $blog_id                 ID of blog (Multisite only). Default is ID of the current blog.
 *     @type string       $who                     Which type of users to query. Accepts only an empty string or
 *                                                 'authors'. Default empty.
 * }
 * @return string String of HTML content.
 */
function wp_dropdown_users( $args = '' ) {
	$defaults = array(
		'show_option_all' => '', 'show_option_none' => '', 'hide_if_only_one_author' => '',
		'orderby' => 'display_name', 'order' => 'ASC',
		'include' => '', 'exclude' => '', 'multi' => 0,
		'show' => 'display_name', 'echo' => 1,
		'selected' => 0, 'name' => 'user', 'class' => '', 'id' => '',
		'blog_id' => $GLOBALS['blog_id'], 'who' => '', 'include_selected' => false,
		'option_none_value' => -1
	);

	$defaults['selected'] = is_author() ? get_query_var( 'author' ) : 0;

	$r = wp_parse_args( $args, $defaults );
	$show = $r['show'];
	$show_option_all = $r['show_option_all'];
	$show_option_none = $r['show_option_none'];
	$option_none_value = $r['option_none_value'];

	$query_args = wp_array_slice_assoc( $r, array( 'blog_id', 'include', 'exclude', 'orderby', 'order', 'who' ) );
	$query_args['fields'] = array( 'ID', 'user_login', $show );
	$users = get_users( $query_args );

	$output = '';
	if ( ! empty( $users ) && ( empty( $r['hide_if_only_one_author'] ) || count( $users ) > 1 ) ) {
		$name = esc_attr( $r['name'] );
		if ( $r['multi'] && ! $r['id'] ) {
			$id = '';
		} else {
			$id = $r['id'] ? " id='" . esc_attr( $r['id'] ) . "'" : " id='$name'";
		}
		$output = "<select name='{$name}'{$id} class='" . $r['class'] . "'>\n";

		if ( $show_option_all ) {
			$output .= "\t<option value='0'>$show_option_all</option>\n";
		}

		if ( $show_option_none ) {
			$_selected = selected( $option_none_value, $r['selected'], false );
			$output .= "\t<option value='" . esc_attr( $option_none_value ) . "'$_selected>$show_option_none</option>\n";
		}

		$found_selected = false;
		foreach ( (array) $users as $user ) {
			$user->ID = (int) $user->ID;
			$_selected = selected( $user->ID, $r['selected'], false );
			if ( $_selected ) {
				$found_selected = true;
			}
			$display = ! empty( $user->$show ) ? $user->$show : '('. $user->user_login . ')';
			$output .= "\t<option value='$user->ID'$_selected>" . esc_html( $display ) . "</option>\n";
		}

		if ( $r['include_selected'] && ! $found_selected && ( $r['selected'] > 0 ) ) {
			$user = get_userdata( $r['selected'] );
			$_selected = selected( $user->ID, $r['selected'], false );
			$display = ! empty( $user->$show ) ? $user->$show : '('. $user->user_login . ')';
			$output .= "\t<option value='$user->ID'$_selected>" . esc_html( $display ) . "</option>\n";
		}

		$output .= "</select>";
	}

	/**
	 * Filter the wp_dropdown_users() HTML output.
	 *
	 * @since 2.3.0
	 *
	 * @param string $output HTML output generated by wp_dropdown_users().
	 */
	$html = apply_filters( 'wp_dropdown_users', $output );

	if ( $r['echo'] ) {
		echo $html;
	}
	return $html;
}

/**
 * Sanitize user field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display', 'attribute' and 'js'. The
 * 'display' context is used by default. 'attribute' and 'js' contexts are treated like 'display'
 * when calling filters.
 *
 * @since 2.3.0
 *
 * @param string $field   The user Object field name.
 * @param mixed  $value   The user Object value.
 * @param int    $user_id User ID.
 * @param string $context How to sanitize user fields. Looks for 'raw', 'edit', 'db', 'display',
 *                        'attribute' and 'js'.
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

	$prefixed = false !== strpos( $field, 'user_' );

	if ( 'edit' == $context ) {
		if ( $prefixed ) {

			/** This filter is documented in wp-includes/post.php */
			$value = apply_filters( "edit_{$field}", $value, $user_id );
		} else {

			/**
			 * Filter a user field value in the 'edit' context.
			 *
			 * The dynamic portion of the hook name, `$field`, refers to the prefixed user
			 * field being filtered, such as 'user_login', 'user_email', 'first_name', etc.
			 *
			 * @since 2.9.0
			 *
			 * @param mixed $value   Value of the prefixed user field.
			 * @param int   $user_id User ID.
			 */
			$value = apply_filters( "edit_user_{$field}", $value, $user_id );
		}

		if ( 'description' == $field )
			$value = esc_html( $value ); // textarea_escaped?
		else
			$value = esc_attr($value);
	} elseif ( 'db' == $context ) {
		if ( $prefixed ) {
			/** This filter is documented in wp-includes/post.php */
			$value = apply_filters( "pre_{$field}", $value );
		} else {

			/**
			 * Filter the value of a user field in the 'db' context.
			 *
			 * The dynamic portion of the hook name, `$field`, refers to the prefixed user
			 * field being filtered, such as 'user_login', 'user_email', 'first_name', etc.
 			 *
			 * @since 2.9.0
			 *
			 * @param mixed $value Value of the prefixed user field.
			 */
			$value = apply_filters( "pre_user_{$field}", $value );
		}
	} else {
		// Use display filters by default.
		if ( $prefixed ) {

			/** This filter is documented in wp-includes/post.php */
			$value = apply_filters( $field, $value, $user_id, $context );
		} else {

			/**
			 * Filter the value of a user field in a standard context.
			 *
			 * The dynamic portion of the hook name, `$field`, refers to the prefixed user
			 * field being filtered, such as 'user_login', 'user_email', 'first_name', etc.
			 *
			 * @since 2.9.0
			 *
			 * @param mixed  $value   The user object value to sanitize.
			 * @param int    $user_id User ID.
			 * @param string $context The context to filter within.
			 */
			$value = apply_filters( "user_{$field}", $value, $user_id, $context );
		}
	}

	if ( 'user_url' == $field )
		$value = esc_url($value);

	if ( 'attribute' == $context ) {
		$value = esc_attr( $value );
	} elseif ( 'js' == $context ) {
		$value = esc_js( $value );
	}
	return $value;
}

/**
 * Update all user caches
 *
 * @since 3.0.0
 *
 * @param object $user User object to be cached
 */
function update_user_caches($user) {
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
 * @param WP_User|int $user User object or ID to be cleaned from the cache
 */
function clean_user_cache( $user ) {
	if ( is_numeric( $user ) )
		$user = new WP_User( $user );

	if ( ! $user->exists() )
		return;

	wp_cache_delete( $user->ID, 'users' );
	wp_cache_delete( $user->user_login, 'userlogins' );
	wp_cache_delete( $user->user_email, 'useremail' );
	wp_cache_delete( $user->user_nicename, 'userslugs' );
}

/**
 * Checks whether the given username exists.
 *
 * @since 2.0.0
 *
 * @param string $username Username.
 * @return int|false The user's ID on success, and false on failure.
 */
function username_exists( $username ) {
	if ( $user = get_user_by( 'login', $username ) ) {
		return $user->ID;
	}
	return false;
}

/**
 * Checks whether the given email exists.
 *
 * @since 2.1.0
 *
 * @param string $email Email.
 * @return int|false The user's ID on success, and false on failure.
 */
function email_exists( $email ) {
	if ( $user = get_user_by( 'email', $email) ) {
		return $user->ID;
	}
	return false;
}

/**
 * Checks whether a username is valid.
 *
 * @since 2.0.1
 *
 * @param string $username Username.
 * @return bool Whether username given is valid
 */
function validate_username( $username ) {
	$sanitized = sanitize_user( $username, true );
	$valid = ( $sanitized == $username );
	/**
	 * Filter whether the provided username is valid or not.
	 *
	 * @since 2.0.1
	 *
	 * @param bool   $valid    Whether given username is valid.
	 * @param string $username Username to check.
	 */
	return apply_filters( 'validate_username', $valid, $username );
}

/**
 * Insert a user into the database.
 *
 * Most of the `$userdata` array fields have filters associated with the values. Exceptions are
 * 'ID', 'rich_editing', 'comment_shortcuts', 'admin_color', 'use_ssl',
 * 'user_registered', and 'role'. The filters have the prefix 'pre_user_' followed by the field
 * name. An example using 'description' would have the filter called, 'pre_user_description' that
 * can be hooked into.
 *
 * @since 2.0.0
 * @since 3.6.0 The `aim`, `jabber`, and `yim` fields were removed as default user contact
 *              methods for new installs. See wp_get_user_contact_methods().
 *
 * @global wpdb $wpdb WordPress database object for queries.
 *
 * @param array|object|WP_User $userdata {
 *     An array, object, or WP_User object of user data arguments.
 *
 *     @type int         $ID                   User ID. If supplied, the user will be updated.
 *     @type string      $user_pass            The plain-text user password.
 *     @type string      $user_login           The user's login username.
 *     @type string      $user_nicename        The URL-friendly user name.
 *     @type string      $user_url             The user URL.
 *     @type string      $user_email           The user email address.
 *     @type string      $display_name         The user's display name.
 *                                             Default is the the user's username.
 *     @type string      $nickname             The user's nickname.
 *                                             Default is the the user's username.
 *     @type string      $first_name           The user's first name. For new users, will be used
 *                                             to build the first part of the user's display name
 *                                             if `$display_name` is not specified.
 *     @type string      $last_name            The user's last name. For new users, will be used
 *                                             to build the second part of the user's display name
 *                                             if `$display_name` is not specified.
 *     @type string      $description          The user's biographical description.
 *     @type string|bool $rich_editing         Whether to enable the rich-editor for the user.
 *                                             False if not empty.
 *     @type string|bool $comment_shortcuts    Whether to enable comment moderation keyboard
 *                                             shortcuts for the user. Default false.
 *     @type string      $admin_color          Admin color scheme for the user. Default 'fresh'.
 *     @type bool        $use_ssl              Whether the user should always access the admin over
 *                                             https. Default false.
 *     @type string      $user_registered      Date the user registered. Format is 'Y-m-d H:i:s'.
 *     @type string|bool $show_admin_bar_front Whether to display the Admin Bar for the user on the
 *                                             site's frontend. Default true.
 *     @type string      $role                 User's role.
 * }
 * @return int|WP_Error The newly created user's ID or a WP_Error object if the user could not
 *                      be created.
 */
function wp_insert_user( $userdata ) {
	global $wpdb;

	if ( $userdata instanceof stdClass ) {
		$userdata = get_object_vars( $userdata );
	} elseif ( $userdata instanceof WP_User ) {
		$userdata = $userdata->to_array();
	}
	// Are we updating or creating?
	if ( ! empty( $userdata['ID'] ) ) {
		$ID = (int) $userdata['ID'];
		$update = true;
		$old_user_data = WP_User::get_data_by( 'id', $ID );
		// hashed in wp_update_user(), plaintext if called directly
		$user_pass = $userdata['user_pass'];
	} else {
		$update = false;
		// Hash the password
		$user_pass = wp_hash_password( $userdata['user_pass'] );
	}

	$sanitized_user_login = sanitize_user( $userdata['user_login'], true );

	/**
	 * Filter a username after it has been sanitized.
	 *
	 * This filter is called before the user is created or updated.
	 *
	 * @since 2.0.3
	 *
	 * @param string $sanitized_user_login Username after it has been sanitized.
	 */
	$pre_user_login = apply_filters( 'pre_user_login', $sanitized_user_login );

	//Remove any non-printable chars from the login string to see if we have ended up with an empty username
	$user_login = trim( $pre_user_login );

	if ( empty( $user_login ) ) {
		return new WP_Error('empty_user_login', __('Cannot create a user with an empty login name.') );
	}
	if ( ! $update && username_exists( $user_login ) ) {
		return new WP_Error( 'existing_user_login', __( 'Sorry, that username already exists!' ) );
	}

	// If a nicename is provided, remove unsafe user characters before
	// using it. Otherwise build a nicename from the user_login.
	if ( ! empty( $userdata['user_nicename'] ) ) {
		$user_nicename = sanitize_user( $userdata['user_nicename'], true );
	} else {
		$user_nicename = $user_login;
	}

	$user_nicename = sanitize_title( $user_nicename );

	// Store values to save in user meta.
	$meta = array();

	/**
	 * Filter a user's nicename before the user is created or updated.
	 *
	 * @since 2.0.3
	 *
	 * @param string $user_nicename The user's nicename.
	 */
	$user_nicename = apply_filters( 'pre_user_nicename', $user_nicename );

	$raw_user_url = empty( $userdata['user_url'] ) ? '' : $userdata['user_url'];

	/**
	 * Filter a user's URL before the user is created or updated.
	 *
	 * @since 2.0.3
	 *
	 * @param string $raw_user_url The user's URL.
	 */
	$user_url = apply_filters( 'pre_user_url', $raw_user_url );

	$raw_user_email = empty( $userdata['user_email'] ) ? '' : $userdata['user_email'];

	/**
	 * Filter a user's email before the user is created or updated.
	 *
	 * @since 2.0.3
	 *
	 * @param string $raw_user_email The user's email.
	 */
	$user_email = apply_filters( 'pre_user_email', $raw_user_email );

	/*
	 * If there is no update, just check for `email_exists`. If there is an update,
	 * check if current email and new email are the same, or not, and check `email_exists`
	 * accordingly.
	 */
	if ( ( ! $update || ( ! empty( $old_user_data ) && $user_email !== $old_user_data->user_email ) )
		&& ! defined( 'WP_IMPORTING' )
		&& email_exists( $user_email )
	) {
		return new WP_Error( 'existing_user_email', __( 'Sorry, that email address is already used!' ) );
	}
	$nickname = empty( $userdata['nickname'] ) ? $user_login : $userdata['nickname'];

	/**
	 * Filter a user's nickname before the user is created or updated.
	 *
	 * @since 2.0.3
	 *
	 * @param string $nickname The user's nickname.
	 */
	$meta['nickname'] = apply_filters( 'pre_user_nickname', $nickname );

	$first_name = empty( $userdata['first_name'] ) ? '' : $userdata['first_name'];

	/**
	 * Filter a user's first name before the user is created or updated.
	 *
	 * @since 2.0.3
	 *
	 * @param string $first_name The user's first name.
	 */
	$meta['first_name'] = apply_filters( 'pre_user_first_name', $first_name );

	$last_name = empty( $userdata['last_name'] ) ? '' : $userdata['last_name'];

	/**
	 * Filter a user's last name before the user is created or updated.
	 *
	 * @since 2.0.3
	 *
	 * @param string $last_name The user's last name.
	 */
	$meta['last_name'] = apply_filters( 'pre_user_last_name', $last_name );

	if ( empty( $userdata['display_name'] ) ) {
		if ( $update ) {
			$display_name = $user_login;
		} elseif ( $meta['first_name'] && $meta['last_name'] ) {
			/* translators: 1: first name, 2: last name */
			$display_name = sprintf( _x( '%1$s %2$s', 'Display name based on first name and last name' ), $meta['first_name'], $meta['last_name'] );
		} elseif ( $meta['first_name'] ) {
			$display_name = $meta['first_name'];
		} elseif ( $meta['last_name'] ) {
			$display_name = $meta['last_name'];
		} else {
			$display_name = $user_login;
		}
	} else {
		$display_name = $userdata['display_name'];
	}

	/**
	 * Filter a user's display name before the user is created or updated.
	 *
	 * @since 2.0.3
	 *
	 * @param string $display_name The user's display name.
	 */
	$display_name = apply_filters( 'pre_user_display_name', $display_name );

	$description = empty( $userdata['description'] ) ? '' : $userdata['description'];

	/**
	 * Filter a user's description before the user is created or updated.
	 *
	 * @since 2.0.3
	 *
	 * @param string $description The user's description.
	 */
	$meta['description'] = apply_filters( 'pre_user_description', $description );

	$meta['rich_editing'] = empty( $userdata['rich_editing'] ) ? 'true' : $userdata['rich_editing'];

	$meta['comment_shortcuts'] = empty( $userdata['comment_shortcuts'] ) || 'false' === $userdata['comment_shortcuts'] ? 'false' : 'true';

	$admin_color = empty( $userdata['admin_color'] ) ? 'fresh' : $userdata['admin_color'];
	$meta['admin_color'] = preg_replace( '|[^a-z0-9 _.\-@]|i', '', $admin_color );

	$meta['use_ssl'] = empty( $userdata['use_ssl'] ) ? 0 : $userdata['use_ssl'];

	$user_registered = empty( $userdata['user_registered'] ) ? gmdate( 'Y-m-d H:i:s' ) : $userdata['user_registered'];

	$meta['show_admin_bar_front'] = empty( $userdata['show_admin_bar_front'] ) ? 'true' : $userdata['show_admin_bar_front'];

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

	$compacted = compact( 'user_pass', 'user_email', 'user_url', 'user_nicename', 'display_name', 'user_registered' );
	$data = wp_unslash( $compacted );

	if ( $update ) {
		if ( $user_email !== $old_user_data->user_email ) {
			$data['user_activation_key'] = '';
		}
		$wpdb->update( $wpdb->users, $data, compact( 'ID' ) );
		$user_id = (int) $ID;
	} else {
		$wpdb->insert( $wpdb->users, $data + compact( 'user_login' ) );
		$user_id = (int) $wpdb->insert_id;
	}

	$user = new WP_User( $user_id );

	// Update user meta.
	foreach ( $meta as $key => $value ) {
		update_user_meta( $user_id, $key, $value );
	}

	foreach ( wp_get_user_contact_methods( $user ) as $key => $value ) {
		if ( isset( $userdata[ $key ] ) ) {
			update_user_meta( $user_id, $key, $userdata[ $key ] );
		}
	}

	if ( isset( $userdata['role'] ) ) {
		$user->set_role( $userdata['role'] );
	} elseif ( ! $update ) {
		$user->set_role(get_option('default_role'));
	}
	wp_cache_delete( $user_id, 'users' );
	wp_cache_delete( $user_login, 'userlogins' );

	if ( $update ) {
		/**
		 * Fires immediately after an existing user is updated.
		 *
		 * @since 2.0.0
		 *
		 * @param int    $user_id       User ID.
		 * @param object $old_user_data Object containing user's data prior to update.
		 */
		do_action( 'profile_update', $user_id, $old_user_data );
	} else {
		/**
		 * Fires immediately after a new user is registered.
		 *
		 * @since 1.5.0
		 *
		 * @param int $user_id User ID.
		 */
		do_action( 'user_register', $user_id );
	}

	return $user_id;
}

/**
 * Update a user in the database.
 *
 * It is possible to update a user's password by specifying the 'user_pass'
 * value in the $userdata parameter array.
 *
 * If current user's password is being updated, then the cookies will be
 * cleared.
 *
 * @since 2.0.0
 *
 * @see wp_insert_user() For what fields can be set in $userdata.
 *
 * @param mixed $userdata An array of user data or a user object of type stdClass or WP_User.
 * @return int|WP_Error The updated user's ID or a WP_Error object if the user could not be updated.
 */
function wp_update_user($userdata) {
	if ( $userdata instanceof stdClass ) {
		$userdata = get_object_vars( $userdata );
	} elseif ( $userdata instanceof WP_User ) {
		$userdata = $userdata->to_array();
	}

	$ID = isset( $userdata['ID'] ) ? (int) $userdata['ID'] : 0;
	if ( ! $ID ) {
		return new WP_Error( 'invalid_user_id', __( 'Invalid user ID.' ) );
	}

	// First, get all of the original fields
	$user_obj = get_userdata( $ID );
	if ( ! $user_obj ) {
		return new WP_Error( 'invalid_user_id', __( 'Invalid user ID.' ) );
	}

	$user = $user_obj->to_array();

	// Add additional custom fields
	foreach ( _get_additional_user_keys( $user_obj ) as $key ) {
		$user[ $key ] = get_user_meta( $ID, $key, true );
	}

	// Escape data pulled from DB.
	$user = add_magic_quotes( $user );

	if ( ! empty($userdata['user_pass']) ) {
		// If password is changing, hash it now
		$plaintext_pass = $userdata['user_pass'];
		$userdata['user_pass'] = wp_hash_password( $userdata['user_pass'] );
		/**
		 * Filter to stop the sending of the password change email.
		 *
		 * @since 4.3.0
		 * @see  wp_insert_user() For $user and $userdata fields.
		 *
		 * @param bool Return false to not send the email.
		 * @param array $user The original user array.
		 * @param array $userdata The updated user array.
		 *
		 */
		$send_pass_change_email = apply_filters( 'send_pass_change_email', true, $user, $userdata );
	}

	if ( isset( $userdata['user_email'] ) && $user['user_email'] !== $userdata['user_email'] ) {
		/**
		 * Filter to stop the sending of the email change email.
		 *
		 * @since 4.3.0
		 * @see  wp_insert_user() For $user and $userdata fields.
		 *
		 * @param bool Return false to not send the email.
		 * @param array $user The original user array.
		 * @param array $userdata The updated user array.
		 *
		 */
		$send_email_change_email = apply_filters( 'send_email_change_email', true, $user, $userdata );
	}

	wp_cache_delete( $user['user_email'], 'useremail' );

	// Merge old and new fields with new fields overwriting old ones.
	$userdata = array_merge( $user, $userdata );
	$user_id = wp_insert_user( $userdata );

	if ( ! is_wp_error( $user_id ) ) {

		$blog_name = wp_specialchars_decode( get_option( 'blogname' ) );

		if ( ! empty( $send_pass_change_email ) ) {

			/* translators: Do not translate USERNAME, ADMIN_EMAIL, EMAIL, SITENAME, SITEURL: those are placeholders. */
			$pass_change_text = __( 'Hi ###USERNAME###,

This notice confirms that your password was changed on ###SITENAME###.

If you did not change your password, please contact the Site Administrator at
###ADMIN_EMAIL###

This email has been sent to ###EMAIL###

Regards,
All at ###SITENAME###
###SITEURL###' );

			$pass_change_email = array(
				'to'      => $user['user_email'],
				'subject' => __( '[%s] Notice of Password Change' ),
				'message' => $pass_change_text,
				'headers' => '',
			);

			/**
			 * Filter the email sent when the user's password is changed.
			 *
			 * @since 4.3.0
			 *
			 * @param array $pass_change_email {
			 *            Used to build wp_mail(). https://developer.wordpress.org/reference/functions/wp_mail/
			 *            @type string $to      The intended recipients. Add emails in a comma separated string.
			 *            @type string $subject The subject of the email.
			 *            @type string $message The content of the email.
			 *                The following strings have a special meaning and will get replaced dynamically:
			 *                ###USERNAME###    The current user's username.
			 *                ###ADMIN_EMAIL### The admin email in case this was unexpected.
			 *                ###EMAIL###       The old email.
			 *                ###SITENAME###    The name of the site.
			 *                ###SITEURL###     The URL to the site.
			 *            @type  string $headers Headers. Add headers in a newline (\r\n) separated string.
			 *        }
			 * @param array $user The original user array.
			 * @param array $userdata The updated user array.
			 *
			 */
			$pass_change_email = apply_filters( 'password_change_email', $pass_change_email, $user, $userdata );

			$pass_change_email['message'] = str_replace( '###USERNAME###', $user['user_login'], $pass_change_email['message'] );
			$pass_change_email['message'] = str_replace( '###ADMIN_EMAIL###', get_option( 'admin_email' ), $pass_change_email['message'] );
			$pass_change_email['message'] = str_replace( '###EMAIL###', $user['user_email'], $pass_change_email['message'] );
			$pass_change_email['message'] = str_replace( '###SITENAME###', get_option( 'blogname' ), $pass_change_email['message'] );
			$pass_change_email['message'] = str_replace( '###SITEURL###', get_option( 'siteurl' ), $pass_change_email['message'] );

			wp_mail( $pass_change_email['to'], sprintf( $pass_change_email['subject'], $blog_name ), $pass_change_email['message'], $pass_change_email['headers'] );
		}

		if ( ! empty( $send_email_change_email ) ) {
			/* translators: Do not translate USERNAME, ADMIN_EMAIL, EMAIL, SITENAME, SITEURL: those are placeholders. */
			$email_change_text = __( 'Hi ###USERNAME###,

This notice confirms that your email was changed on ###SITENAME###.

If you did not change your email, please contact the Site Administrator at
###ADMIN_EMAIL###

This email has been sent to ###EMAIL###

Regards,
All at ###SITENAME###
###SITEURL###' );

			$email_change_email = array(
				'to'      => $user['user_email'],
				'subject' => __( '[%s] Notice of Email Change' ),
				'message' => $email_change_text,
				'headers' => '',
			);

			/**
			 * Filter the email sent when the user's password is changed.
			 *
			 * @since 4.3.0
			 *
			 * @param array $email_change_email {
			 *            Used to build wp_mail(). https://developer.wordpress.org/reference/functions/wp_mail/
			 *            @type string $to      The intended recipients.
			 *            @type string $subject The subject of the email.
			 *            @type string $message The content of the email.
			 *                The following strings have a special meaning and will get replaced dynamically:
			 *                ###USERNAME###    The current user's username.
			 *                ###ADMIN_EMAIL### The admin email in case this was unexpected.
			 *                ###EMAIL###       The old email.
			 *                ###SITENAME###    The name of the site.
			 *                ###SITEURL###     The URL to the site.
			 *            @type string $headers Headers.
			 *        }
			 * @param array $user The original user array.
			 * @param array $userdata The updated user array.
			 */
			$email_change_email = apply_filters( 'email_change_email', $email_change_email, $user, $userdata );

			$email_change_email['message'] = str_replace( '###USERNAME###', $user['user_login'], $email_change_email['message'] );
			$email_change_email['message'] = str_replace( '###ADMIN_EMAIL###', get_option( 'admin_email' ), $email_change_email['message'] );
			$email_change_email['message'] = str_replace( '###EMAIL###', $user['user_email'], $email_change_email['message'] );
			$email_change_email['message'] = str_replace( '###SITENAME###', get_option( 'blogname' ), $email_change_email['message'] );
			$email_change_email['message'] = str_replace( '###SITEURL###', get_option( 'siteurl' ), $email_change_email['message'] );

			wp_mail( $email_change_email['to'], sprintf( $email_change_email['subject'], $blog_name ), $email_change_email['message'], $email_change_email['headers'] );
		}
	}

	// Update the cookies if the password changed.
	$current_user = wp_get_current_user();
	if ( $current_user->ID == $ID ) {
		if ( isset($plaintext_pass) ) {
			wp_clear_auth_cookie();

			// Here we calculate the expiration length of the current auth cookie and compare it to the default expiration.
			// If it's greater than this, then we know the user checked 'Remember Me' when they logged in.
			$logged_in_cookie    = wp_parse_auth_cookie( '', 'logged_in' );
			/** This filter is documented in wp-includes/pluggable.php */
			$default_cookie_life = apply_filters( 'auth_cookie_expiration', ( 2 * DAY_IN_SECONDS ), $ID, false );
			$remember            = ( ( $logged_in_cookie['expiration'] - time() ) > $default_cookie_life );

			wp_set_auth_cookie( $ID, $remember );
		}
	}

	return $user_id;
}

/**
 * A simpler way of inserting a user into the database.
 *
 * Creates a new user with just the username, password, and email. For more
 * complex user creation use {@see wp_insert_user()} to specify more information.
 *
 * @since 2.0.0
 * @see wp_insert_user() More complete way to create a new user
 *
 * @param string $username The user's username.
 * @param string $password The user's password.
 * @param string $email    Optional. The user's email. Default empty.
 * @return int|WP_Error The new user's ID.
 */
function wp_create_user($username, $password, $email = '') {
	$user_login = wp_slash( $username );
	$user_email = wp_slash( $email    );
	$user_pass = $password;

	$userdata = compact('user_login', 'user_email', 'user_pass');
	return wp_insert_user($userdata);
}

/**
 * Return a list of meta keys that wp_insert_user() is supposed to set.
 *
 * @since 3.3.0
 * @access private
 *
 * @param WP_User $user WP_User instance.
 * @return array
 */
function _get_additional_user_keys( $user ) {
	$keys = array( 'first_name', 'last_name', 'nickname', 'description', 'rich_editing', 'comment_shortcuts', 'admin_color', 'use_ssl', 'show_admin_bar_front' );
	return array_merge( $keys, array_keys( wp_get_user_contact_methods( $user ) ) );
}

/**
 * Set up the user contact methods.
 *
 * Default contact methods were removed in 3.6. A filter dictates contact methods.
 *
 * @since 3.7.0
 *
 * @param WP_User $user Optional. WP_User object.
 * @return array Array of contact methods and their labels.
 */
function wp_get_user_contact_methods( $user = null ) {
	$methods = array();
	if ( get_site_option( 'initial_db_version' ) < 23588 ) {
		$methods = array(
			'aim'    => __( 'AIM' ),
			'yim'    => __( 'Yahoo IM' ),
			'jabber' => __( 'Jabber / Google Talk' )
		);
	}

	/**
	 * Filter the user contact methods.
	 *
	 * @since 2.9.0
	 *
	 * @param array   $methods Array of contact methods and their labels.
 	 * @param WP_User $user    WP_User object.
	 */
	return apply_filters( 'user_contactmethods', $methods, $user );
}

/**
 * The old private function for setting up user contact methods.
 *
 * @since 2.9.0
 * @access private
 */
function _wp_get_user_contactmethods( $user = null ) {
	return wp_get_user_contact_methods( $user );
}

/**
 * Gets the text suggesting how to create strong passwords.
 *
 * @since 4.1.0
 *
 * @return string The password hint text.
 */
function wp_get_password_hint() {
	$hint = __( 'Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ &amp; ).' );

	/**
	 * Filter the text describing the site's password complexity policy.
	 *
	 * @since 4.1.0
	 *
	 * @param string $hint The password hint text.
	 */
	return apply_filters( 'password_hint', $hint );
}

/**
 * Retrieves a user row based on password reset key and login
 *
 * A key is considered 'expired' if it exactly matches the value of the
 * user_activation_key field, rather than being matched after going through the
 * hashing process. This field is now hashed; old values are no longer accepted
 * but have a different WP_Error code so good user feedback can be provided.
 *
 * @since 3.1.0
 *
 * @global wpdb         $wpdb      WordPress database object for queries.
 * @global PasswordHash $wp_hasher Portable PHP password hashing framework instance.
 *
 * @param string $key       Hash to validate sending user's password.
 * @param string $login     The user login.
 * @return WP_User|WP_Error WP_User object on success, WP_Error object for invalid or expired keys.
 */
function check_password_reset_key($key, $login) {
	global $wpdb, $wp_hasher;

	$key = preg_replace('/[^a-z0-9]/i', '', $key);

	if ( empty( $key ) || !is_string( $key ) )
		return new WP_Error('invalid_key', __('Invalid key'));

	if ( empty($login) || !is_string($login) )
		return new WP_Error('invalid_key', __('Invalid key'));

	$row = $wpdb->get_row( $wpdb->prepare( "SELECT ID, user_activation_key FROM $wpdb->users WHERE user_login = %s", $login ) );
	if ( ! $row )
		return new WP_Error('invalid_key', __('Invalid key'));

	if ( empty( $wp_hasher ) ) {
		require_once ABSPATH . WPINC . '/class-phpass.php';
		$wp_hasher = new PasswordHash( 8, true );
	}

	if ( $wp_hasher->CheckPassword( $key, $row->user_activation_key ) )
		return get_userdata( $row->ID );

	if ( $key === $row->user_activation_key ) {
		$return = new WP_Error( 'expired_key', __( 'Invalid key' ) );
		$user_id = $row->ID;

		/**
		 * Filter the return value of check_password_reset_key() when an
		 * old-style key is used (plain-text key was stored in the database).
		 *
		 * @since 3.7.0
		 *
		 * @param WP_Error $return  A WP_Error object denoting an expired key.
		 *                          Return a WP_User object to validate the key.
		 * @param int      $user_id The matched user ID.
		 */
		return apply_filters( 'password_reset_key_expired', $return, $user_id );
	}

	return new WP_Error( 'invalid_key', __( 'Invalid key' ) );
}

/**
 * Handles resetting the user's password.
 *
 * @since 2.5.0
 *
 * @param object $user     The user
 * @param string $new_pass New password for the user in plaintext
 */
function reset_password( $user, $new_pass ) {
	/**
	 * Fires before the user's password is reset.
	 *
	 * @since 1.5.0
	 *
	 * @param object $user     The user.
	 * @param string $new_pass New user password.
	 */
	do_action( 'password_reset', $user, $new_pass );

	wp_set_password( $new_pass, $user->ID );
	update_user_option( $user->ID, 'default_password_nag', false, true );

	wp_password_change_notification( $user );
}

/**
 * Handles registering a new user.
 *
 * @since 2.5.0
 *
 * @param string $user_login User's username for logging in
 * @param string $user_email User's email address to send password and add
 * @return int|WP_Error Either user's ID or error on failure.
 */
function register_new_user( $user_login, $user_email ) {
	$errors = new WP_Error();

	$sanitized_user_login = sanitize_user( $user_login );
	/**
	 * Filter the email address of a user being registered.
	 *
	 * @since 2.1.0
	 *
	 * @param string $user_email The email address of the new user.
	 */
	$user_email = apply_filters( 'user_registration_email', $user_email );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.' ) );
	} elseif ( ! validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.' ) );
	}

	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.' ) );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ) );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
	}

	/**
	 * Fires when submitting registration form data, before the user is created.
	 *
	 * @since 2.1.0
	 *
	 * @param string   $sanitized_user_login The submitted username after being sanitized.
	 * @param string   $user_email           The submitted email.
	 * @param WP_Error $errors               Contains any errors with submitted username and email,
	 *                                       e.g., an empty field, an invalid username or email,
	 *                                       or an existing username or email.
	 */
	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	/**
	 * Filter the errors encountered when a new user is being registered.
	 *
	 * The filtered WP_Error object may, for example, contain errors for an invalid
	 * or existing username or email address. A WP_Error object should always returned,
	 * but may or may not contain errors.
	 *
	 * If any errors are present in $errors, this will abort the user's registration.
	 *
	 * @since 2.1.0
	 *
	 * @param WP_Error $errors               A WP_Error object containing any errors encountered
	 *                                       during registration.
	 * @param string   $sanitized_user_login User's username after it has been sanitized.
	 * @param string   $user_email           User's email.
	 */
	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

	if ( $errors->get_error_code() )
		return $errors;

	$user_pass = wp_generate_password( 12, false );
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	if ( ! $user_id || is_wp_error( $user_id ) ) {
		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you&hellip; please contact the <a href="mailto:%s">webmaster</a> !' ), get_option( 'admin_email' ) ) );
		return $errors;
	}

	update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

	wp_new_user_notification( $user_id, $user_pass );

	return $user_id;
}

/**
 * Retrieve the current session token from the logged_in cookie.
 *
 * @since 4.0.0
 *
 * @return string Token.
 */
function wp_get_session_token() {
	$cookie = wp_parse_auth_cookie( '', 'logged_in' );
	return ! empty( $cookie['token'] ) ? $cookie['token'] : '';
}

/**
 * Retrieve a list of sessions for the current user.
 *
 * @since 4.0.0
 * @return array Array of sessions.
 */
function wp_get_all_sessions() {
	$manager = WP_Session_Tokens::get_instance( get_current_user_id() );
	return $manager->get_all();
}

/**
 * Remove the current session token from the database.
 *
 * @since 4.0.0
 */
function wp_destroy_current_session() {
	$token = wp_get_session_token();
	if ( $token ) {
		$manager = WP_Session_Tokens::get_instance( get_current_user_id() );
		$manager->destroy( $token );
	}
}

/**
 * Remove all but the current session token for the current user for the database.
 *
 * @since 4.0.0
 */
function wp_destroy_other_sessions() {
	$token = wp_get_session_token();
	if ( $token ) {
		$manager = WP_Session_Tokens::get_instance( get_current_user_id() );
		$manager->destroy_others( $token );
	}
}

/**
 * Remove all session tokens for the current user from the database.
 *
 * @since 4.0.0
 */
function wp_destroy_all_sessions() {
	$manager = WP_Session_Tokens::get_instance( get_current_user_id() );
	$manager->destroy_all();
}
