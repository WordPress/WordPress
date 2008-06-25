<?php

// Creates a new user from the "Users" form using $_POST information.
function add_user() {
	if ( func_num_args() ) { // The hackiest hack that ever did hack
		global $current_user, $wp_roles;
		$user_id = (int) func_get_arg( 0 );

		if ( isset( $_POST['role'] ) ) {
			if( $user_id != $current_user->id || $wp_roles->role_objects[$_POST['role']]->has_cap( 'edit_users' ) ) {
				$user = new WP_User( $user_id );
				$user->set_role( $_POST['role'] );
			}
		}
	} else {
		add_action( 'user_register', 'add_user' ); // See above
		return edit_user();
	}
}

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

	if ( isset( $_POST['user_login'] ))
		$user->user_login = wp_specialchars( trim( $_POST['user_login'] ));

	$pass1 = $pass2 = '';
	if ( isset( $_POST['pass1'] ))
		$pass1 = $_POST['pass1'];
	if ( isset( $_POST['pass2'] ))
		$pass2 = $_POST['pass2'];

	if ( isset( $_POST['role'] ) && current_user_can( 'edit_users' ) ) {
		if( $user_id != $current_user->id || $wp_roles->role_objects[$_POST['role']]->has_cap( 'edit_users' ))
			$user->role = $_POST['role'];
	}

	if ( isset( $_POST['email'] ))
		$user->user_email = wp_specialchars( trim( $_POST['email'] ));
	if ( isset( $_POST['url'] ) ) {
		$user->user_url = clean_url( trim( $_POST['url'] ));
		$user->user_url = preg_match('/^(https?|ftps?|mailto|news|irc|gopher|nntp|feed|telnet):/is', $user->user_url) ? $user->user_url : 'http://'.$user->user_url;
	}
	if ( isset( $_POST['first_name'] ))
		$user->first_name = wp_specialchars( trim( $_POST['first_name'] ));
	if ( isset( $_POST['last_name'] ))
		$user->last_name = wp_specialchars( trim( $_POST['last_name'] ));
	if ( isset( $_POST['nickname'] ))
		$user->nickname = wp_specialchars( trim( $_POST['nickname'] ));
	if ( isset( $_POST['display_name'] ))
		$user->display_name = wp_specialchars( trim( $_POST['display_name'] ));
	if ( isset( $_POST['description'] ))
		$user->description = trim( $_POST['description'] );
	if ( isset( $_POST['jabber'] ))
		$user->jabber = wp_specialchars( trim( $_POST['jabber'] ));
	if ( isset( $_POST['aim'] ))
		$user->aim = wp_specialchars( trim( $_POST['aim'] ));
	if ( isset( $_POST['yim'] ))
		$user->yim = wp_specialchars( trim( $_POST['yim'] ));
	if ( !$update )
		$user->rich_editing = 'true';  // Default to true for new users.
	else if ( isset( $_POST['rich_editing'] ) )
		$user->rich_editing = $_POST['rich_editing'];
	else
		$user->rich_editing = 'false';

	if ( !$update )
		$user->admin_color = 'fresh';  // Default to fresh for new users.
	else if ( isset( $_POST['admin_color'] ) )
		$user->admin_color = $_POST['admin_color'];
	else
		$user->admin_color = 'fresh';

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
	if( strpos( " ".$pass1, "\\" ) )
		$errors->add( 'pass', __( '<strong>ERROR</strong>: Passwords may not contain the character "\\".' ), array( 'form-field' => 'pass1' ) );

	/* checking the password has been typed twice the same */
	if ( $pass1 != $pass2 )
		$errors->add( 'pass', __( '<strong>ERROR</strong>: Please enter the same password in the two password fields.' ), array( 'form-field' => 'pass1' ) );

	if (!empty ( $pass1 ))
		$user->user_pass = $pass1;

	if ( !$update && !validate_username( $user->user_login ) )
		$errors->add( 'user_login', __( '<strong>ERROR</strong>: This username is invalid. Please enter a valid username.' ));

	if (!$update && username_exists( $user->user_login ))
		$errors->add( 'user_login', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.' ));

	/* checking e-mail address */
	if ( empty ( $user->user_email ) ) {
		$errors->add( 'user_email', __( '<strong>ERROR</strong>: Please enter an e-mail address.' ), array( 'form-field' => 'email' ) );
	} else
		if (!is_email( $user->user_email ) ) {
			$errors->add( 'user_email', __( "<strong>ERROR</strong>: The e-mail address isn't correct." ), array( 'form-field' => 'email' ) );
		}

	if ( $errors->get_error_codes() )
		return $errors;

	if ( $update ) {
		$user_id = wp_update_user( get_object_vars( $user ));
	} else {
		$user_id = wp_insert_user( get_object_vars( $user ));
		wp_new_user_notification( $user_id );
	}
	return $user_id;
}

function get_author_user_ids() {
	global $wpdb;
	$level_key = $wpdb->prefix . 'user_level';
	return $wpdb->get_col( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value != '0'", $level_key) );
}

function get_editable_authors( $user_id ) {
	global $wpdb;

	$editable = get_editable_user_ids( $user_id );

	if( !$editable ) {
		return false;
	} else {
		$editable = join(',', $editable);
		$authors = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE ID IN ($editable) ORDER BY display_name" );
	}

	return apply_filters('get_editable_authors', $authors);
}

function get_editable_user_ids( $user_id, $exclude_zeros = true ) {
	global $wpdb;

	$user = new WP_User( $user_id );

	if ( ! $user->has_cap('edit_others_posts') ) {
		if ( $user->has_cap('edit_posts') || $exclude_zeros == false )
			return array($user->id);
		else
			return false;
	}

	$level_key = $wpdb->prefix . 'user_level';

	$query = $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s", $level_key);
	if ( $exclude_zeros )
		$query .= " AND meta_value != '0'";

	return $wpdb->get_col( $query );
}

function get_nonauthor_user_ids() {
	global $wpdb;
	$level_key = $wpdb->prefix . 'user_level';

	return $wpdb->get_col( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value = '0'", $level_key) );
}

function get_others_unpublished_posts($user_id, $type='any') {
	global $wpdb;

	$editable = get_editable_user_ids( $user_id );

	if ( in_array($type, array('draft', 'pending')) )
		$type_sql = " post_status = '$type' ";
	else
		$type_sql = " ( post_status = 'draft' OR post_status = 'pending' ) ";

	$dir = ( 'pending' == $type ) ? 'ASC' : 'DESC';

	if( !$editable ) {
		$other_unpubs = '';
	} else {
		$editable = join(',', $editable);
		$other_unpubs = $wpdb->get_results( $wpdb->prepare("SELECT ID, post_title, post_author FROM $wpdb->posts WHERE post_type = 'post' AND $type_sql AND post_author IN ($editable) AND post_author != %d ORDER BY post_modified $dir", $user_id) );
	}

	return apply_filters('get_others_drafts', $other_unpubs);
}

function get_others_drafts($user_id) {
	return get_others_unpublished_posts($user_id, 'draft');
}

function get_others_pending($user_id) {
	return get_others_unpublished_posts($user_id, 'pending');
}

function get_user_to_edit( $user_id ) {
	$user = new WP_User( $user_id );
	$user->user_login   = attribute_escape($user->user_login);
	$user->user_email   = attribute_escape($user->user_email);
	$user->user_url     = clean_url($user->user_url);
	$user->first_name   = attribute_escape($user->first_name);
	$user->last_name    = attribute_escape($user->last_name);
	$user->display_name = attribute_escape($user->display_name);
	$user->nickname     = attribute_escape($user->nickname);
	$user->aim          = attribute_escape($user->aim);
	$user->yim          = attribute_escape($user->yim);
	$user->jabber       = attribute_escape($user->jabber);
	$user->description  =  wp_specialchars($user->description);

	return $user;
}

function get_users_drafts( $user_id ) {
	global $wpdb;
	$query = $wpdb->prepare("SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'draft' AND post_author = %d ORDER BY post_modified DESC", $user_id);
	$query = apply_filters('get_users_drafts', $query);
	return $wpdb->get_results( $query );
}

function wp_delete_user($id, $reassign = 'novalue') {
	global $wpdb;

	$id = (int) $id;

	if ($reassign == 'novalue') {
		$post_ids = $wpdb->get_col( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_author = %d", $id) );

		if ($post_ids) {
			foreach ($post_ids as $post_id)
				wp_delete_post($post_id);
		}

		// Clean links
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->links WHERE link_owner = %d", $id) );
	} else {
		$reassign = (int) $reassign;
		$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET post_author = %d WHERE post_author = %d", $reassign, $id) );
		$wpdb->query( $wpdb->prepare("UPDATE $wpdb->links SET link_owner = %d WHERE link_owner = %d}", $reassign, $id) );
	}

	// FINALLY, delete user
	do_action('delete_user', $id);

	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->users WHERE ID = %d", $id) );
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id = %d", $id) );

	wp_cache_delete($id, 'users');
	wp_cache_delete($user->user_login, 'userlogins');
	wp_cache_delete($user->user_email, 'useremail');

	return true;
}

function wp_revoke_user($id) {
	$id = (int) $id;

	$user = new WP_User($id);
	$user->remove_all_caps();
}

// WP_User_Search class
// by Mark Jaquith

if ( !class_exists('WP_User_Search') ) :
class WP_User_Search {
	var $results;
	var $search_term;
	var $page;
	var $role;
	var $raw_page;
	var $users_per_page = 50;
	var $first_user;
	var $last_user;
	var $query_limit;
	var $query_sort;
	var $query_from_where;
	var $total_users_for_query = 0;
	var $too_many_total_users = false;
	var $search_errors;

	function WP_User_Search ($search_term = '', $page = '', $role = '') { // constructor
		$this->search_term = $search_term;
		$this->raw_page = ( '' == $page ) ? false : (int) $page;
		$this->page = (int) ( '' == $page ) ? 1 : $page;
		$this->role = $role;

		$this->prepare_query();
		$this->query();
		$this->prepare_vars_for_template_usage();
		$this->do_paging();
	}

	function prepare_query() {
		global $wpdb;
		$this->first_user = ($this->page - 1) * $this->users_per_page;
		$this->query_limit = $wpdb->prepare(" LIMIT %d, %d", $this->first_user, $this->users_per_page);
		$this->query_sort = ' ORDER BY user_login';
		$search_sql = '';
		if ( $this->search_term ) {
			$searches = array();
			$search_sql = 'AND (';
			foreach ( array('user_login', 'user_nicename', 'user_email', 'user_url', 'display_name') as $col )
				$searches[] = $col . " LIKE '%$this->search_term%'";
			$search_sql .= implode(' OR ', $searches);
			$search_sql .= ')';
		}

		$this->query_from_where = "FROM $wpdb->users";
		if ( $this->role )
			$this->query_from_where .= $wpdb->prepare(" INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = '{$wpdb->prefix}capabilities' AND $wpdb->usermeta.meta_value LIKE %s", '%' . $this->role . '%');
		else
			$this->query_from_where .= " WHERE 1=1";
		$this->query_from_where .= " $search_sql";

	}

	function query() {
		global $wpdb;
		$this->results = $wpdb->get_col('SELECT ID ' . $this->query_from_where . $this->query_sort . $this->query_limit);

		if ( $this->results )
			$this->total_users_for_query = $wpdb->get_var('SELECT COUNT(ID) ' . $this->query_from_where); // no limit
		else
			$this->search_errors = new WP_Error('no_matching_users_found', __('No matching users were found!'));
	}

	function prepare_vars_for_template_usage() {
		$this->search_term = stripslashes($this->search_term); // done with DB, from now on we want slashes gone
	}

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
		}
	}

	function get_results() {
		return (array) $this->results;
	}

	function page_links() {
		echo $this->paging_text;
	}

	function results_are_paged() {
		if ( $this->paging_text )
			return true;
		return false;
	}

	function is_search() {
		if ( $this->search_term )
			return true;
		return false;
	}
}
endif;

?>