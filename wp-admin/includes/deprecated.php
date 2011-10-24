<?php
/**
 * Deprecated admin functions from past WordPress versions. You shouldn't use these
 * functions and look for the alternatives instead. The functions will be removed
 * in a later version.
 *
 * @package WordPress
 * @subpackage Deprecated
 */

/*
 * Deprecated functions come here to die.
 */

/**
 * @since 2.1
 * @deprecated 2.1
 * @deprecated Use wp_editor().
 * @see wp_editor()
 */
function tinymce_include() {
	_deprecated_function( __FUNCTION__, '2.1', 'wp_editor()' );

	wp_editor('', 'content');
}

/**
 * Unused Admin function.
 *
 * @since 2.0
 * @deprecated 2.5
 *
 */
function documentation_link() {
	_deprecated_function( __FUNCTION__, '2.5' );
	return;
}

/**
 * Calculates the new dimensions for a downsampled image.
 *
 * @since 2.0.0
 * @deprecated 3.0.0
 * @deprecated Use wp_constrain_dimensions()
 *
 * @param int $width Current width of the image
 * @param int $height Current height of the image
 * @param int $wmax Maximum wanted width
 * @param int $hmax Maximum wanted height
 * @return mixed Array(height,width) of shrunk dimensions.
 */
function wp_shrink_dimensions( $width, $height, $wmax = 128, $hmax = 96 ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_constrain_dimensions()' );
	return wp_constrain_dimensions( $width, $height, $wmax, $hmax );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 0.71
 * @deprecated 2.6.0
 * @deprecated Use wp_category_checklist()
 * @see wp_category_checklist()
 *
 * @param unknown_type $default
 * @param unknown_type $parent
 * @param unknown_type $popular_ids
 */
function dropdown_categories( $default = 0, $parent = 0, $popular_ids = array() ) {
	_deprecated_function( __FUNCTION__, '2.6', 'wp_category_checklist()' );
	global $post_ID;
	wp_category_checklist( $post_ID );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.1.0
 * @deprecated 2.6.0
 * @deprecated Use wp_link_category_checklist()
 * @see wp_link_category_checklist()
 *
 * @param unknown_type $default
 */
function dropdown_link_categories( $default = 0 ) {
	_deprecated_function( __FUNCTION__, '2.6', 'wp_link_category_checklist()' );
	global $link_id;
	wp_link_category_checklist( $link_id );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 1.2.0
 * @deprecated 3.0.0
 * @deprecated Use wp_dropdown_categories()
 * @see wp_dropdown_categories()
 *
 * @param unknown_type $currentcat
 * @param unknown_type $currentparent
 * @param unknown_type $parent
 * @param unknown_type $level
 * @param unknown_type $categories
 * @return unknown
 */
function wp_dropdown_cats( $currentcat = 0, $currentparent = 0, $parent = 0, $level = 0, $categories = 0 ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_dropdown_categories()' );
	if (!$categories )
		$categories = get_categories( array('hide_empty' => 0) );

	if ( $categories ) {
		foreach ( $categories as $category ) {
			if ( $currentcat != $category->term_id && $parent == $category->parent) {
				$pad = str_repeat( '&#8211; ', $level );
				$category->name = esc_html( $category->name );
				echo "\n\t<option value='$category->term_id'";
				if ( $currentparent == $category->term_id )
					echo " selected='selected'";
				echo ">$pad$category->name</option>";
				wp_dropdown_cats( $currentcat, $currentparent, $category->term_id, $level +1, $categories );
			}
		}
	} else {
		return false;
	}
}

/**
 * Register a setting and its sanitization callback
 *
 * @since 2.7.0
 * @deprecated 3.0.0
 * @deprecated Use register_setting()
 * @see register_setting()
 *
 * @param string $option_group A settings group name.  Should correspond to a whitelisted option key name.
 * 	Default whitelisted option key names include "general," "discussion," and "reading," among others.
 * @param string $option_name The name of an option to sanitize and save.
 * @param unknown_type $sanitize_callback A callback function that sanitizes the option's value.
 * @return unknown
 */
function add_option_update_handler( $option_group, $option_name, $sanitize_callback = '' ) {
	_deprecated_function( __FUNCTION__, '3.0', 'register_setting()' );
	return register_setting( $option_group, $option_name, $sanitize_callback );
}

/**
 * Unregister a setting
 *
 * @since 2.7.0
 * @deprecated 3.0.0
 * @deprecated Use unregister_setting()
 * @see unregister_setting()
 *
 * @param unknown_type $option_group
 * @param unknown_type $option_name
 * @param unknown_type $sanitize_callback
 * @return unknown
 */
function remove_option_update_handler( $option_group, $option_name, $sanitize_callback = '' ) {
	_deprecated_function( __FUNCTION__, '3.0', 'unregister_setting()' );
	return unregister_setting( $option_group, $option_name, $sanitize_callback );
}

/**
 * Determines the language to use for CodePress syntax highlighting.
 *
 * @since 2.8.0
 * @deprecated 3.0.0
 *
 * @param string $filename
**/
function codepress_get_lang( $filename ) {
	_deprecated_function( __FUNCTION__, '3.0' );
	return;
}

/**
 * Adds Javascript required to make CodePress work on the theme/plugin editors.
 *
 * @since 2.8.0
 * @deprecated 3.0.0
**/
function codepress_footer_js() {
	_deprecated_function( __FUNCTION__, '3.0' );
	return;
}

/**
 * Determine whether to use CodePress.
 *
 * @since 2.8
 * @deprecated 3.0.0
**/
function use_codepress() {
	_deprecated_function( __FUNCTION__, '3.0' );
	return;
}


/**
 * @deprecated 3.1.0
 *
 * @return array List of user IDs.
 */
function get_author_user_ids() {
	_deprecated_function( __FUNCTION__, '3.1', 'get_users()' );

	global $wpdb;
	if ( !is_multisite() )
		$level_key = $wpdb->get_blog_prefix() . 'user_level';
	else
		$level_key = $wpdb->get_blog_prefix() . 'capabilities'; // wpmu site admins don't have user_levels

	return $wpdb->get_col( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value != '0'", $level_key) );
}

/**
 * @deprecated 3.1.0
 *
 * @param int $user_id User ID.
 * @return array|bool List of editable authors. False if no editable users.
 */
function get_editable_authors( $user_id ) {
	_deprecated_function( __FUNCTION__, '3.1', 'get_users()' );

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
 * @deprecated 3.1.0
 *
 * @param int $user_id User ID.
 * @param bool $exclude_zeros Optional, default is true. Whether to exclude zeros.
 * @return unknown
 */
function get_editable_user_ids( $user_id, $exclude_zeros = true, $post_type = 'post' ) {
	_deprecated_function( __FUNCTION__, '3.1', 'get_users()' );

	global $wpdb;

	$user = new WP_User( $user_id );
	$post_type_obj = get_post_type_object($post_type);

	if ( ! $user->has_cap($post_type_obj->cap->edit_others_posts) ) {
		if ( $user->has_cap($post_type_obj->cap->edit_posts) || ! $exclude_zeros )
			return array($user->ID);
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
 * @deprecated 3.1.0
 */
function get_nonauthor_user_ids() {
	_deprecated_function( __FUNCTION__, '3.1', 'get_users()' );

	global $wpdb;

	if ( !is_multisite() )
		$level_key = $wpdb->get_blog_prefix() . 'user_level';
	else
		$level_key = $wpdb->get_blog_prefix() . 'capabilities'; // wpmu site admins don't have user_levels

	return $wpdb->get_col( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value = '0'", $level_key) );
}

if ( !class_exists('WP_User_Search') ) :
/**
 * WordPress User Search class.
 *
 * @since 2.1.0
 * @deprecated 3.1.0
 */
class WP_User_Search {

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var unknown_type
	 */
	var $results;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var unknown_type
	 */
	var $search_term;

	/**
	 * Page number.
	 *
	 * @since 2.1.0
	 * @access private
	 * @var int
	 */
	var $page;

	/**
	 * Role name that users have.
	 *
	 * @since 2.5.0
	 * @access private
	 * @var string
	 */
	var $role;

	/**
	 * Raw page number.
	 *
	 * @since 2.1.0
	 * @access private
	 * @var int|bool
	 */
	var $raw_page;

	/**
	 * Amount of users to display per page.
	 *
	 * @since 2.1.0
	 * @access public
	 * @var int
	 */
	var $users_per_page = 50;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var unknown_type
	 */
	var $first_user;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var int
	 */
	var $last_user;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
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
	 * @since 2.1.0
	 * @access private
	 * @var int
	 */
	var $total_users_for_query = 0;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var bool
	 */
	var $too_many_total_users = false;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.1.0
	 * @access private
	 * @var unknown_type
	 */
	var $search_errors;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 2.7.0
	 * @access private
	 * @var unknown_type
	 */
	var $paging_text;

	/**
	 * PHP4 Constructor - Sets up the object properties.
	 *
	 * @since 2.1.0
	 *
	 * @param string $search_term Search terms string.
	 * @param int $page Optional. Page ID.
	 * @param string $role Role name.
	 * @return WP_User_Search
	 */
	function WP_User_Search ($search_term = '', $page = '', $role = '') {
		_deprecated_function( __FUNCTION__, '3.1', 'WP_User_Query' );

		$this->search_term = stripslashes( $search_term );
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
	 * @since 2.1.0
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
				$searches[] = $wpdb->prepare( $col . ' LIKE %s', '%' . like_escape($this->search_term) . '%' );
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
	 * @since 2.1.0
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
	 * @since 2.1.0
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
	 * @since 2.1.0
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
	 * @since 2.1.0
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
	 * @since 2.1.0
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
	 * @since 2.1.0
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
	 * @since 2.1.0
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

/**
 * Retrieve editable posts from other users.
 *
 * @deprecated 3.1.0
 *
 * @param int $user_id User ID to not retrieve posts from.
 * @param string $type Optional, defaults to 'any'. Post type to retrieve, can be 'draft' or 'pending'.
 * @return array List of posts from others.
 */
function get_others_unpublished_posts($user_id, $type='any') {
	_deprecated_function( __FUNCTION__, '3.1' );

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
 * @deprecated 3.1.0
 *
 * @param int $user_id User ID.
 * @return array List of drafts from other users.
 */
function get_others_drafts($user_id) {
	_deprecated_function( __FUNCTION__, '3.1' );

	return get_others_unpublished_posts($user_id, 'draft');
}

/**
 * Retrieve pending review posts from other users.
 *
 * @deprecated 3.1.0
 *
 * @param int $user_id User ID.
 * @return array List of posts with pending review post type from other users.
 */
function get_others_pending($user_id) {
	_deprecated_function( __FUNCTION__, '3.1' );

	return get_others_unpublished_posts($user_id, 'pending');
}

/**
 * Output the QuickPress dashboard widget.
 *
 * @since 3.0.0
 * @deprecated 3.2.0
 * @deprecated Use wp_dashboard_quick_press()
 * @see wp_dashboard_quick_press()
 */
function wp_dashboard_quick_press_output() {
	_deprecated_function( __FUNCTION__, '3.2', 'wp_dashboard_quick_press()' );
	wp_dashboard_quick_press();
}

/**
 * @since 2.7.0
 * @deprecated 3.3
 * @deprecated Use wp_editor()
 * @see wp_editor()
 */
function wp_tiny_mce() {
	_deprecated_function( __FUNCTION__, '3.3', 'wp_editor()' );

	wp_editor('', 'content');
}

/**
 * @deprecated 3.3.0
 * @deprecated Use wp_editor()
 * @see wp_editor()
 */
function wp_preload_dialogs() {
	_deprecated_function( __FUNCTION__, '3.3', 'wp_editor()' );
}

/**
 * @deprecated 3.3.0
 * @deprecated Use wp_editor()
 * @see wp_editor()
 */
function wp_print_editor_js() {
	_deprecated_function( __FUNCTION__, '3.3', 'wp_editor()' );
}

/**
 * @deprecated 3.3.0
 * @deprecated Use wp_editor()
 * @see wp_editor()
 */
function wp_quicktags() {
	_deprecated_function( __FUNCTION__, '3.3', 'wp_editor()' );
}

/**
 * @deprecated 3.3.0
 * @deprecated Use wp_editor()
 * @see wp_editor()
 */
function wp_fullscreen_html() {
	_deprecated_function( __FUNCTION__, '3.3', 'wp_editor()' );
}

/**
 * Returns the screen layout options.
 *
 * @since 2.8.0
 * @deprecated 3.3.0
 * @deprecated Use $current_screen->render_screen_layout()
 * @see WP_Screen::render_screen_layout()
 */
function screen_layout( $screen ) {
	_deprecated_function( __FUNCTION__, '3.3', '$current_screen->render_screen_layout()' );

	$current_screen = get_current_screen();

	if ( ! $current_screen )
		return '';

	ob_start();
	$current_screen->render_screen_layout();
	return ob_get_clean();
}

/**
 * Returns the screen's per-page options.
 *
 * @since 2.8.0
 * @deprecated 3.3.0
 * @deprecated Use $current_screen->render_per_page_options()
 * @see WP_Screen::render_per_page_options()
 */
function screen_options( $screen ) {
	_deprecated_function( __FUNCTION__, '3.3', '$current_screen->render_per_page_options()' );

	$current_screen = get_current_screen();

	if ( ! $current_screen )
		return '';

	ob_start();
	$current_screen->render_per_page_options();
	return ob_get_clean();
}

/**
 * Renders the screen's help.
 *
 * @since 2.7.0
 * @deprecated 3.3.0
 * @deprecated Use $current_screen->render_screen_meta()
 * @see WP_Screen::render_screen_meta()
 */
function screen_meta( $screen ) {
	$current_screen = get_current_screen();
	$current_screen->render_screen_meta();
}

/**
 * Favorite actions were deprecated in version 3.2. Use the admin bar instead.
 *
 * @since 2.7.0
 * @deprecated 3.2.0
 */
function favorite_actions() {
	_deprecated_function( __FUNCTION__, '3.2', 'WP_Admin_Bar' );
}

function media_upload_image() {
	__deprecated_function( __FUNCTION__, '3.3', 'wp_media_upload_handler()' );
	return wp_media_upload_handler();
}

function media_upload_audio() {
	__deprecated_function( __FUNCTION__, '3.3', 'wp_media_upload_handler()' );
	return wp_media_upload_handler();
}

function media_upload_video() {
	__deprecated_function( __FUNCTION__, '3.3', 'wp_media_upload_handler()' );
	return wp_media_upload_handler();
}

function media_upload_file() {
	__deprecated_function( __FUNCTION__, '3.3', 'wp_media_upload_handler()' );
	return wp_media_upload_handler();
}

function type_url_form_image() {
	__deprecated_function( __FUNCTION__, '3.3', "wp_media_insert_url_form('image')" );
	return wp_media_insert_url_form( 'image' );
}

function type_url_form_audio() {
	__deprecated_function( __FUNCTION__, '3.3', "wp_media_insert_url_form('audio')" );
	return wp_media_insert_url_form( 'audio' );
}

function type_url_form_video() {
	__deprecated_function( __FUNCTION__, '3.3', "wp_media_insert_url_form('video')" );
	return wp_media_insert_url_form( 'video' );
}

function type_url_form_file() {
	__deprecated_function( __FUNCTION__, '3.3', "wp_media_insert_url_form('file')" );
	return wp_media_insert_url_form( 'file' );
}