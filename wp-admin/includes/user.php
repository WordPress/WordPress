<?php
/**
 * WordPress user administration API.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Creates a new user from the "Users" form using $_POST information.
 *
 * It seems that the first half is for backwards compatibility, but only
 * has the ability to alter the user's role. WordPress core seems to
 * use this function only in the second way, running edit_user() with
 * no id so as to create a new user.
 *
 * @since 2.0
 *
 * @param int $user_id Optional. User ID.
 * @return null|WP_Error|int Null when adding user, WP_Error or User ID integer when no parameters.
 */
function add_user() {
	if ( func_num_args() ) { // The hackiest hack that ever did hack
		global $current_user, $wp_roles;
		$user_id = (int) func_get_arg( 0 );

		if ( isset( $_POST['role'] ) ) {
			$new_role = sanitize_text_field( $_POST['role'] );
			// Don't let anyone with 'edit_users' (admins) edit their own role to something without it.
			if ( $user_id != $current_user->id || $wp_roles->role_objects[$new_role]->has_cap( 'edit_users' ) ) {
				// If the new role isn't editable by the logged-in user die with error
				$editable_roles = get_editable_roles();
				if ( empty( $editable_roles[$new_role] ) )
					wp_die(__('You can&#8217;t give users that role.'));

				$user = new WP_User( $user_id );
				$user->set_role( $new_role );
			}
		}
	} else {
		add_action( 'user_register', 'add_user' ); // See above
		return edit_user();
	}
}

/**
 * Edit user settings based on contents of $_POST
 *
 * Used on user-edit.php and profile.php to manage and process user options, passwords etc.
 *
 * @since 2.0
 *
 * @param int $user_id Optional. User ID.
 * @return int user id of the updated user
 */
function edit_user( $user_id = 0 ) {
	global $current_user, $wp_roles, $wpdb;
	if ( $user_id != 0 ) {
		$update = true;
		$user->ID = (int) $user_id;
		$userdata = get_userdata( $user_id );
		$user->user_login = $wpdb->escape( $userdata->user_login );
	} else {
		$update = false;
		$user = '';
	}

	if ( !$update && isset( $_POST['user_login'] ) )
		$user->user_login = sanitize_user($_POST['user_login'], true);

	$pass1 = $pass2 = '';
	if ( isset( $_POST['pass1'] ))
		$pass1 = $_POST['pass1'];
	if ( isset( $_POST['pass2'] ))
		$pass2 = $_POST['pass2'];

	if ( isset( $_POST['role'] ) && current_user_can( 'edit_users' ) ) {
		$new_role = sanitize_text_field( $_POST['role'] );
		$potential_role = isset($wp_roles->role_objects[$new_role]) ? $wp_roles->role_objects[$new_role] : false;
		// Don't let anyone with 'edit_users' (admins) edit their own role to something without it.
		// Multisite super admins can freely edit their blog roles -- they possess all caps.
		if ( ( is_multisite() && current_user_can( 'manage_sites' ) ) || $user_id != $current_user->id || ($potential_role && $potential_role->has_cap( 'edit_users' ) ) )
			$user->role = $new_role;

		// If the new role isn't editable by the logged-in user die with error
		$editable_roles = get_editable_roles();
		if ( ! empty( $new_role ) && empty( $editable_roles[$new_role] ) )
			wp_die(__('You can&#8217;t give users that role.'));
	}

	if ( isset( $_POST['email'] ))
		$user->user_email = sanitize_text_field( $_POST['email'] );
	if ( isset( $_POST['url'] ) ) {
		if ( empty ( $_POST['url'] ) || $_POST['url'] == 'http://' ) {
			$user->user_url = '';
		} else {
			$user->user_url = esc_url_raw( $_POST['url'] );
			$user->user_url = preg_match('/^(https?|ftps?|mailto|news|irc|gopher|nntp|feed|telnet):/is', $user->user_url) ? $user->user_url : 'http://'.$user->user_url;
		}
	}
	if ( isset( $_POST['first_name'] ) )
		$user->first_name = sanitize_text_field( $_POST['first_name'] );
	if ( isset( $_POST['last_name'] ) )
		$user->last_name = sanitize_text_field( $_POST['last_name'] );
	if ( isset( $_POST['nickname'] ) )
		$user->nickname = sanitize_text_field( $_POST['nickname'] );
	if ( isset( $_POST['display_name'] ) )
		$user->display_name = sanitize_text_field( $_POST['display_name'] );

	if ( isset( $_POST['description'] ) )
		$user->description = trim( $_POST['description'] );

	foreach ( _wp_get_user_contactmethods() as $method => $name ) {
		if ( isset( $_POST[$method] ))
			$user->$method = sanitize_text_field( $_POST[$method] );
	}

	if ( $update ) {
		$user->rich_editing = isset( $_POST['rich_editing'] ) && 'false' == $_POST['rich_editing'] ? 'false' : 'true';
		$user->admin_color = isset( $_POST['admin_color'] ) ? sanitize_text_field( $_POST['admin_color'] ) : 'fresh';
	}

	$user->comment_shortcuts = isset( $_POST['comment_shortcuts'] ) && 'true' == $_POST['comment_shortcuts'] ? 'true' : '';

	$user->use_ssl = 0;
	if ( !empty($_POST['use_ssl']) )
		$user->use_ssl = 1;

	$errors = new WP_Error();

	/* checking that username has been typed */
	if ( $user->user_login == '' )
		$errors->add( 'user_login', __( '<strong>ERROR</strong>: Please enter a username.' ));

	/* checking the password has been typed twice */
	do_action_ref_array( 'check_passwords', array ( $user->user_login, & $pass1, & $pass2 ));

	if ( $update ) {
		if ( empty($pass1) && !empty($pass2) )
			$errors->add( 'pass', __( '<strong>ERROR</strong>: You entered your new password only once.' ), array( 'form-field' => 'pass1' ) );
		elseif ( !empty($pass1) && empty($pass2) )
			$errors->add( 'pass', __( '<strong>ERROR</strong>: You entered your new password only once.' ), array( 'form-field' => 'pass2' ) );
	} else {
		if ( empty($pass1) )
			$errors->add( 'pass', __( '<strong>ERROR</strong>: Please enter your password.' ), array( 'form-field' => 'pass1' ) );
		elseif ( empty($pass2) )
			$errors->add( 'pass', __( '<strong>ERROR</strong>: Please enter your password twice.' ), array( 'form-field' => 'pass2' ) );
	}

	/* Check for "\" in password */
	if ( false !== strpos( stripslashes($pass1), "\\" ) )
		$errors->add( 'pass', __( '<strong>ERROR</strong>: Passwords may not contain the character "\\".' ), array( 'form-field' => 'pass1' ) );

	/* checking the password has been typed twice the same */
	if ( $pass1 != $pass2 )
		$errors->add( 'pass', __( '<strong>ERROR</strong>: Please enter the same password in the two password fields.' ), array( 'form-field' => 'pass1' ) );

	if ( !empty( $pass1 ) )
		$user->user_pass = $pass1;

	if ( !$update && !validate_username( $user->user_login ) )
		$errors->add( 'user_login', __( '<strong>ERROR</strong>: This username is invalid. Please enter a valid username.' ));

	if ( !$update && username_exists( $user->user_login ) )
		$errors->add( 'user_login', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.' ));

	/* checking e-mail address */
	if ( empty( $user->user_email ) ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please enter an e-mail address.' ), array( 'form-field' => 'email' ) );
	} elseif ( !is_email( $user->user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The e-mail address isn&#8217;t correct.' ), array( 'form-field' => 'email' ) );
	} elseif ( ( $owner_id = email_exists($user->user_email) ) && $owner_id != $user->ID ) {
		$errors->add( 'email_exists', __('<strong>ERROR</strong>: This email is already registered, please choose another one.'), array( 'form-field' => 'email' ) );
	}

	// Allow plugins to return their own errors.
	do_action_ref_array('user_profile_update_errors', array ( &$errors, $update, &$user ) );

	if ( $errors->get_error_codes() )
		return $errors;

	if ( $update ) {
		$user_id = wp_update_user( get_object_vars( $user ) );
	} else {
		$user_id = wp_insert_user( get_object_vars( $user ) );
		wp_new_user_notification( $user_id, isset($_POST['send_password']) ? $pass1 : '' );
	}
	return $user_id;
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @return array List of user IDs.
 */
function get_author_user_ids() {
	global $wpdb;
	if ( !is_multisite() )
		$level_key = $wpdb->get_blog_prefix() . 'user_level';
	else
		$level_key = $wpdb->get_blog_prefix() . 'capabilities'; // wpmu site admins don't have user_levels

	return $wpdb->get_col( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value != '0'", $level_key) );
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param int $user_id User ID.
 * @return array|bool List of editable authors. False if no editable users.
 */
function get_editable_authors( $user_id ) {
	global $wpdb;

	$editable = get_editable_user_ids( $user_id );

	if ( !$editable ) {
		return false;
	} else {
		$editable = join(',', $editable);
		$authors = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE ID IN ($editable) ORDER BY display_name" );
	}

	return apply_filters('get_editable_authors', $authors);
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param int $user_id User ID.
 * @param bool $exclude_zeros Optional, default is true. Whether to exclude zeros.
 * @return unknown
 */
function get_editable_user_ids( $user_id, $exclude_zeros = true, $post_type = 'post' ) {
	global $wpdb;

	$user = new WP_User( $user_id );
	$post_type_obj = get_post_type_object($post_type);

	if ( ! $user->has_cap($post_type_obj->edit_others_cap) ) {
		if ( $user->has_cap($post_type_obj->edit_type_cap) || ! $exclude_zeros )
			return array($user->id);
		else
			return array();
	}

	if ( !is_multisite() )
		$level_key = $wpdb->get_blog_prefix() . 'user_level';
	else
		$level_key = $wpdb->get_blog_prefix() . 'capabilities'; // wpmu site admins don't have user_levels

	$query = $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s", $level_key);
	if ( $exclude_zeros )
		$query .= " AND meta_value != '0'";

	return $wpdb->get_col( $query );
}

/**
 * Fetch a filtered list of user roles that the current user is
 * allowed to edit.
 *
 * Simple function who's main purpose is to allow filtering of the
 * list of roles in the $wp_roles object so that plugins can remove
 * innappropriate ones depending on the situation or user making edits.
 * Specifically because without filtering anyone with the edit_users
 * capability can edit others to be administrators, even if they are
 * only editors or authors. This filter allows admins to delegate
 * user management.
 *
 * @since 2.8
 *
 * @return unknown
 */
function get_editable_roles() {
	global $wp_roles;

	$all_roles = $wp_roles->roles;
	$editable_roles = apply_filters('editable_roles', $all_roles);

	return $editable_roles;
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function get_nonauthor_user_ids() {
	global $wpdb;

	if ( !is_multisite() )
		$level_key = $wpdb->get_blog_prefix() . 'user_level';
	else
		$level_key = $wpdb->get_blog_prefix() . 'capabilities'; // wpmu site admins don't have user_levels

	return $wpdb->get_col( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value = '0'", $level_key) );
}

/**
 * Retrieve editable posts from other users.
 *
 * @since unknown
 *
 * @param int $user_id User ID to not retrieve posts from.
 * @param string $type Optional, defaults to 'any'. Post type to retrieve, can be 'draft' or 'pending'.
 * @return array List of posts from others.
 */
function get_others_unpublished_posts($user_id, $type='any') {
	global $wpdb;

	$editable = get_editable_user_ids( $user_id );

	if ( in_array($type, array('draft', 'pending')) )
		$type_sql = " post_status = '$type' ";
	else
		$type_sql = " ( post_status = 'draft' OR post_status = 'pending' ) ";

	$dir = ( 'pending' == $type ) ? 'ASC' : 'DESC';

	if ( !$editable ) {
		$other_unpubs = '';
	} else {
		$editable = join(',', $editable);
		$other_unpubs = $wpdb->get_results( $wpdb->prepare("SELECT ID, post_title, post_author FROM $wpdb->posts WHERE post_type = 'post' AND $type_sql AND post_author IN ($editable) AND post_author != %d ORDER BY post_modified $dir", $user_id) );
	}

	return apply_filters('get_others_drafts', $other_unpubs);
}

/**
 * Retrieve drafts from other users.
 *
 * @since unknown
 *
 * @param int $user_id User ID.
 * @return array List of drafts from other users.
 */
function get_others_drafts($user_id) {
	return get_others_unpublished_posts($user_id, 'draft');
}

/**
 * Retrieve pending review posts from other users.
 *
 * @since unknown
 *
 * @param int $user_id User ID.
 * @return array List of posts with pending review post type from other users.
 */
function get_others_pending($user_id) {
	return get_others_unpublished_posts($user_id, 'pending');
}

/**
 * Retrieve user data and filter it.
 *
 * @since unknown
 *
 * @param int $user_id User ID.
 * @return object WP_User object with user data.
 */
function get_user_to_edit( $user_id ) {
	$user = new WP_User( $user_id );

	$user_contactmethods = _wp_get_user_contactmethods();
	foreach ($user_contactmethods as $method => $name) {
		if ( empty( $user->{$method} ) )
			$user->{$method} = '';
	}

	if ( empty($user->description) )
		$user->description = '';

	$user = sanitize_user_object($user, 'edit');

	return $user;
}

/**
 * Retrieve the user's drafts.
 *
 * @since unknown
 *
 * @param int $user_id User ID.
 * @return array
 */
function get_users_drafts( $user_id ) {
	global $wpdb;
	$query = $wpdb->prepare("SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'draft' AND post_author = %d ORDER BY post_modified DESC", $user_id);
	$query = apply_filters('get_users_drafts', $query);
	return $wpdb->get_results( $query );
}

/**
 * Remove user and optionally reassign posts and links to another user.
 *
 * If the $reassign parameter is not assigned to an User ID, then all posts will
 * be deleted of that user. The action 'delete_user' that is passed the User ID
 * being deleted will be run after the posts are either reassigned or deleted.
 * The user meta will also be deleted that are for that User ID.
 *
 * @since unknown
 *
 * @param int $id User ID.
 * @param int $reassign Optional. Reassign posts and links to new User ID.
 * @return bool True when finished.
 */
function wp_delete_user( $id, $reassign = 'novalue' ) {
	global $wpdb;

	$id = (int) $id;

	// allow for transaction statement
	do_action('delete_user', $id);

	if ( 'novalue' === $reassign || null === $reassign ) {
		$post_ids = $wpdb->get_col( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_author = %d", $id) );

		if ( $post_ids ) {
			foreach ( $post_ids as $post_id )
				wp_delete_post($post_id);
		}

		// Clean links
		$link_ids = $wpdb->get_col( $wpdb->prepare("SELECT link_id FROM $wpdb->links WHERE link_owner = %d", $id) );

		if ( $link_ids ) {
			foreach ( $link_ids as $link_id )
				wp_delete_link($link_id);
		}
	} else {
		$reassign = (int) $reassign;
		$wpdb->update( $wpdb->posts, array('post_author' => $reassign), array('post_author' => $id) );
		$wpdb->update( $wpdb->links, array('link_owner' => $reassign), array('link_owner' => $id) );
	}

	// FINALLY, delete user
	if ( !is_multisite() ) {
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id = %d", $id) );
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->users WHERE ID = %d", $id) );
	} else {
		$level_key = $wpdb->get_blog_prefix() . 'capabilities'; // wpmu site admins don't have user_levels
		$wpdb->query("DELETE FROM $wpdb->usermeta WHERE user_id = $id AND meta_key = '{$level_key}'");
	}

	clean_user_cache($id);

	// allow for commit transaction
	do_action('deleted_user', $id);

	return true;
}

/**
 * Remove all capabilities from user.
 *
 * @since unknown
 *
 * @param int $id User ID.
 */
function wp_revoke_user($id) {
	$id = (int) $id;

	$user = new WP_User($id);
	$user->remove_all_caps();
}

if ( !class_exists('WP_User_Search') ) :
/**
 * WordPress User Search class.
 *
 * @since unknown
 */
class WP_User_Search {

	/**
	 * {@internal Missing Description}}
	 *
	 * @since unknown
	 * @access private
	 * @var unknown_type
	 */
	var $results;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since unknown
	 * @access private
	 * @var unknown_type
	 */
	var $search_term;

	/**
	 * Page number.
	 *
	 * @since unknown
	 * @access private
	 * @var int
	 */
	var $page;

	/**
	 * Role name that users have.
	 *
	 * @since unknown
	 * @access private
	 * @var string
	 */
	var $role;

	/**
	 * Raw page number.
	 *
	 * @since unknown
	 * @access private
	 * @var int|bool
	 */
	var $raw_page;

	/**
	 * Amount of users to display per page.
	 *
	 * @since unknown
	 * @access public
	 * @var int
	 */
	var $users_per_page = 50;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since unknown
	 * @access private
	 * @var unknown_type
	 */
	var $first_user;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since unknown
	 * @access private
	 * @var int
	 */
	var $last_user;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since unknown
	 * @access private
	 * @var string
	 */
	var $query_limit;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 3.0.0
	 * @access private
	 * @var string
	 */
	var $query_orderby;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 3.0.0
	 * @access private
	 * @var string
	 */
	var $query_from;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 3.0.0
	 * @access private
	 * @var string
	 */
	var $query_where;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since unknown
	 * @access private
	 * @var int
	 */
	var $total_users_for_query = 0;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since unknown
	 * @access private
	 * @var bool
	 */
	var $too_many_total_users = false;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since unknown
	 * @access private
	 * @var unknown_type
	 */
	var $search_errors;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since unknown
	 * @access private
	 * @var unknown_type
	 */
	var $paging_text;

	/**
	 * PHP4 Constructor - Sets up the object properties.
	 *
	 * @since unknown
	 *
	 * @param string $search_term Search terms string.
	 * @param int $page Optional. Page ID.
	 * @param string $role Role name.
	 * @return WP_User_Search
	 */
	function WP_User_Search ($search_term = '', $page = '', $role = '') {
		$this->search_term = $search_term;
		$this->raw_page = ( '' == $page ) ? false : (int) $page;
		$this->page = (int) ( '' == $page ) ? 1 : $page;
		$this->role = $role;

		$this->prepare_query();
		$this->query();
		$this->prepare_vars_for_template_usage();
		$this->do_paging();
	}

	/**
	 * {@internal Missing Short Description}}
	 *
	 * {@internal Missing Long Description}}
	 *
	 * @since unknown
	 * @access public
	 */
	function prepare_query() {
		global $wpdb;
		$this->first_user = ($this->page - 1) * $this->users_per_page;

		$this->query_limit = $wpdb->prepare(" LIMIT %d, %d", $this->first_user, $this->users_per_page);
		$this->query_orderby = ' ORDER BY user_login';

		$search_sql = '';
		if ( $this->search_term ) {
			$searches = array();
			$search_sql = 'AND (';
			foreach ( array('user_login', 'user_nicename', 'user_email', 'user_url', 'display_name') as $col )
				$searches[] = $col . " LIKE '%$this->search_term%'";
			$search_sql .= implode(' OR ', $searches);
			$search_sql .= ')';
		}

		$this->query_from = " FROM $wpdb->users";
		$this->query_where = " WHERE 1=1 $search_sql";

		if ( $this->role ) {
			$this->query_from .= " INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id";
			$this->query_where .= $wpdb->prepare(" AND $wpdb->usermeta.meta_key = '{$wpdb->prefix}capabilities' AND $wpdb->usermeta.meta_value LIKE %s", '%' . $this->role . '%');
		} elseif ( is_multisite() ) {
			$level_key = $wpdb->prefix . 'capabilities'; // wpmu site admins don't have user_levels
			$this->query_from .= ", $wpdb->usermeta";
			$this->query_where .= " AND $wpdb->users.ID = $wpdb->usermeta.user_id AND meta_key = '{$level_key}'";
		}

		do_action_ref_array( 'pre_user_search', array( &$this ) );
	}

	/**
	 * {@internal Missing Short Description}}
	 *
	 * {@internal Missing Long Description}}
	 *
	 * @since unknown
	 * @access public
	 */
	function query() {
		global $wpdb;

		$this->results = $wpdb->get_col("SELECT DISTINCT($wpdb->users.ID)" . $this->query_from . $this->query_where . $this->query_orderby . $this->query_limit);

		if ( $this->results )
			$this->total_users_for_query = $wpdb->get_var("SELECT COUNT(DISTINCT($wpdb->users.ID))" . $this->query_from . $this->query_where); // no limit
		else
			$this->search_errors = new WP_Error('no_matching_users_found', __('No matching users were found!'));
	}

	/**
	 * {@internal Missing Short Description}}
	 *
	 * {@internal Missing Long Description}}
	 *
	 * @since unknown
	 * @access public
	 */
	function prepare_vars_for_template_usage() {
		$this->search_term = stripslashes($this->search_term); // done with DB, from now on we want slashes gone
	}

	/**
	 * {@internal Missing Short Description}}
	 *
	 * {@internal Missing Long Description}}
	 *
	 * @since unknown
	 * @access public
	 */
	function do_paging() {
		if ( $this->total_users_for_query > $this->users_per_page ) { // have to page the results
			$args = array();
			if( ! empty($this->search_term) )
				$args['usersearch'] = urlencode($this->search_term);
			if( ! empty($this->role) )
				$args['role'] = urlencode($this->role);

			$this->paging_text = paginate_links( array(
				'total' => ceil($this->total_users_for_query / $this->users_per_page),
				'current' => $this->page,
				'base' => 'users.php?%_%',
				'format' => 'userspage=%#%',
				'add_args' => $args
			) );
			if ( $this->paging_text ) {
				$this->paging_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
					number_format_i18n( ( $this->page - 1 ) * $this->users_per_page + 1 ),
					number_format_i18n( min( $this->page * $this->users_per_page, $this->total_users_for_query ) ),
					number_format_i18n( $this->total_users_for_query ),
					$this->paging_text
				);
			}
		}
	}

	/**
	 * {@internal Missing Short Description}}
	 *
	 * {@internal Missing Long Description}}
	 *
	 * @since unknown
	 * @access public
	 *
	 * @return unknown
	 */
	function get_results() {
		return (array) $this->results;
	}

	/**
	 * Displaying paging text.
	 *
	 * @see do_paging() Builds paging text.
	 *
	 * @since unknown
	 * @access public
	 */
	function page_links() {
		echo $this->paging_text;
	}

	/**
	 * Whether paging is enabled.
	 *
	 * @see do_paging() Builds paging text.
	 *
	 * @since unknown
	 * @access public
	 *
	 * @return bool
	 */
	function results_are_paged() {
		if ( $this->paging_text )
			return true;
		return false;
	}

	/**
	 * Whether there are search terms.
	 *
	 * @since unknown
	 * @access public
	 *
	 * @return bool
	 */
	function is_search() {
		if ( $this->search_term )
			return true;
		return false;
	}
}
endif;

add_action('admin_init', 'default_password_nag_handler');
function default_password_nag_handler($errors = false) {
	global $user_ID;
	if ( ! get_user_option('default_password_nag') ) //Short circuit it.
		return;

	//get_user_setting = JS saved UI setting. else no-js-falback code.
	if ( 'hide' == get_user_setting('default_password_nag') || isset($_GET['default_password_nag']) && '0' == $_GET['default_password_nag'] ) {
		delete_user_setting('default_password_nag');
		update_user_option($user_ID, 'default_password_nag', false, true);
	}
}

add_action('profile_update', 'default_password_nag_edit_user', 10, 2);
function default_password_nag_edit_user($user_ID, $old_data) {
	global $user_ID;
	if ( ! get_user_option('default_password_nag') ) //Short circuit it.
		return;

	$new_data = get_userdata($user_ID);

	if ( $new_data->user_pass != $old_data->user_pass ) { //Remove the nag if the password has been changed.
		delete_user_setting('default_password_nag');
		update_user_option($user_ID, 'default_password_nag', false, true);
	}
}

add_action('admin_notices', 'default_password_nag');
function default_password_nag() {
	if ( ! get_user_option('default_password_nag') ) //Short circuit it.
		return;

	echo '<div class="error default-password-nag"><p><strong>' . __('Notice:') . '</strong> ';
	printf(__("You're using the auto-generated password for your account. Would you like to change it to something you'll remember easier?<br/>
		<a href='%s'>Yes, take me to my profile page</a> | <a href='%s' id='default-password-nag-no'>No thanks, do not remind me again</a>"), admin_url('profile.php') . '#password', '?default_password_nag=0');
	echo '</p></div>';
}

?>
