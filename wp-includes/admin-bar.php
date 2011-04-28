<?php
/**
 * Admin Bar
 *
 * This code handles the building and rendering of the press bar.
 */

/**
 * Instantiate the admin bar object and set it up as a global for access elsewhere.
 *
 * To hide the admin bar, you're looking in the wrong place. Unhooking this function will not
 * properly remove the admin bar. For that, use show_admin_bar(false) or the show_admin_bar filter.
 *
 * @since 3.1.0
 * @access private
 * @return bool Whether the admin bar was successfully initialized.
 */
function _wp_admin_bar_init() {
	global $wp_admin_bar;

	if ( ! is_admin_bar_showing() )
		return false;

	/* Load the admin bar class code ready for instantiation */
	require( ABSPATH . WPINC . '/class-wp-admin-bar.php' );

	/* Instantiate the admin bar */
	$admin_bar_class = apply_filters( 'wp_admin_bar_class', 'WP_Admin_Bar' );
	if ( class_exists( $admin_bar_class ) )
		$wp_admin_bar = new $admin_bar_class;
	else
		return false;

	$wp_admin_bar->initialize();
	$wp_admin_bar->add_menus();

	return true;
}
add_action( 'init', '_wp_admin_bar_init' ); // Don't remove. Wrong way to disable.

/**
 * Render the admin bar to the page based on the $wp_admin_bar->menu member var.
 * This is called very late on the footer actions so that it will render after anything else being
 * added to the footer.
 *
 * It includes the action "admin_bar_menu" which should be used to hook in and
 * add new menus to the admin bar. That way you can be sure that you are adding at most optimal point,
 * right before the admin bar is rendered. This also gives you access to the $post global, among others.
 *
 * @since 3.1.0
 */
function wp_admin_bar_render() {
	global $wp_admin_bar;

	if ( ! is_admin_bar_showing() || ! is_object( $wp_admin_bar ) )
		return false;

	$wp_admin_bar->load_user_locale_translations();

	do_action_ref_array( 'admin_bar_menu', array( &$wp_admin_bar ) );

	do_action( 'wp_before_admin_bar_render' );

	$wp_admin_bar->render();

	do_action( 'wp_after_admin_bar_render' );

	$wp_admin_bar->unload_user_locale_translations();
}
add_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
add_action( 'admin_footer', 'wp_admin_bar_render', 1000 );

/**
 * Add the "My Account" menu and all submenus.
 *
 * @since 3.1.0
 */
function wp_admin_bar_my_account_menu( $wp_admin_bar ) {
	global $user_identity;

	$user_id = get_current_user_id();

	if ( 0 != $user_id ) {
		/* Add the 'My Account' menu */
		$avatar = get_avatar( get_current_user_id(), 16 );
		$id = ( ! empty( $avatar ) ) ? 'my-account-with-avatar' : 'my-account';

		$wp_admin_bar->add_menu( array( 'id' => $id, 'title' => $avatar . $user_identity,  'href' => get_edit_profile_url( $user_id ) ) );

		/* Add the "My Account" sub menus */
		$wp_admin_bar->add_menu( array( 'parent' => $id, 'title' => __( 'Edit My Profile' ), 'href' => get_edit_profile_url( $user_id ) ) );
		if ( is_multisite() )
			$wp_admin_bar->add_menu( array( 'parent' => $id, 'title' => __( 'Dashboard' ), 'href' => get_dashboard_url( $user_id ) ) );
		else
			$wp_admin_bar->add_menu( array( 'parent' => $id, 'title' => __( 'Dashboard' ), 'href' => admin_url() ) );
		$wp_admin_bar->add_menu( array( 'parent' => $id, 'title' => __( 'Log Out' ), 'href' => wp_logout_url() ) );
	}
}

/**
 * Add the "My Sites/[Site Name]" menu and all submenus.
 *
 * @since 3.1.0
 */
function wp_admin_bar_my_sites_menu( $wp_admin_bar ) {
	global $wpdb;

	/* Add the 'My Sites' menu if the user has more than one site. */
	if ( count( $wp_admin_bar->user->blogs ) <= 1 )
		return;

	$wp_admin_bar->add_menu( array(  'id' => 'my-blogs', 'title' => __( 'My Sites' ),  'href' => admin_url( 'my-sites.php' ) ) );

	$default = includes_url('images/wpmini-blue.png');

	foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {
		// @todo Replace with some favicon lookup.
		//$blavatar = '<img src="' . esc_url( blavatar_url( blavatar_domain( $blog->siteurl ), 'img', 16, $default ) ) . '" alt="Blavatar" width="16" height="16" />';
		$blavatar = '<img src="' . esc_url($default) . '" alt="' . esc_attr__( 'Blavatar' ) . '" width="16" height="16" class="blavatar"/>';

		$blogname = empty( $blog->blogname ) ? $blog->domain : $blog->blogname;

		$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-' . $blog->userblog_id, 'title' => $blavatar . $blogname,  'href' => get_admin_url($blog->userblog_id) ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-d', 'title' => __( 'Dashboard' ), 'href' => get_admin_url($blog->userblog_id) ) );

		if ( current_user_can_for_blog( $blog->userblog_id, 'edit_posts' ) ) {
			$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-n', 'title' => __( 'New Post' ), 'href' => get_admin_url($blog->userblog_id, 'post-new.php') ) );
			$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-c', 'title' => __( 'Manage Comments' ), 'href' => get_admin_url($blog->userblog_id, 'edit-comments.php') ) );
		}

		$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-v', 'title' => __( 'Visit Site' ), 'href' => get_home_url($blog->userblog_id) ) );
	}
}

/**
 * Provide a shortlink.
 *
 * @since 3.1.0
 */
function wp_admin_bar_shortlink_menu( $wp_admin_bar ) {
	$short = wp_get_shortlink( 0, 'query' );
	$id = 'get-shortlink';

	if ( empty( $short ) )
		return;

	$html = '<input class="shortlink-input" type="text" readonly="readonly" value="' . esc_attr( $short ) . '" />';

	$wp_admin_bar->add_menu( array(
		'id' => $id,
		'title' => __( 'Shortlink' ),
		'href' => $short,
		'meta' => array( 'html' => $html ),
	) );
}

/**
 * Provide an edit link for posts and terms.
 *
 * @since 3.1.0
 */
function wp_admin_bar_edit_menu( $wp_admin_bar ) {
	$current_object = get_queried_object();

	if ( empty($current_object) )
		return;

	if ( ! empty( $current_object->post_type ) && ( $post_type_object = get_post_type_object( $current_object->post_type ) ) && current_user_can( $post_type_object->cap->edit_post, $current_object->ID ) && $post_type_object->show_ui ) {
		$wp_admin_bar->add_menu( array( 'id' => 'edit', 'title' => $post_type_object->labels->edit_item,  'href' => get_edit_post_link( $current_object->ID ) ) );
	} elseif ( ! empty( $current_object->taxonomy ) &&  ( $tax = get_taxonomy( $current_object->taxonomy ) ) && current_user_can( $tax->cap->edit_terms ) && $tax->show_ui ) {
		$wp_admin_bar->add_menu( array( 'id' => 'edit', 'title' => $tax->labels->edit_item, 'href' => get_edit_term_link( $current_object->term_id, $current_object->taxonomy ) ) );
	}
}

/**
 * Add "Add New" menu.
 *
 * @since 3.1.0
 */
function wp_admin_bar_new_content_menu( $wp_admin_bar ) {
	$actions = array();
	foreach ( (array) get_post_types( array( 'show_ui' => true ), 'objects' ) as $ptype_obj ) {
		if ( true !== $ptype_obj->show_in_menu || ! current_user_can( $ptype_obj->cap->edit_posts ) )
			continue;

		$actions[ 'post-new.php?post_type=' . $ptype_obj->name ] = array( $ptype_obj->labels->singular_name, $ptype_obj->cap->edit_posts, 'new-' . $ptype_obj->name );
	}

	if ( empty( $actions ) )
		return;

	$wp_admin_bar->add_menu( array( 'id' => 'new-content', 'title' => _x( 'Add New', 'admin bar menu group label' ), 'href' => admin_url( array_shift( array_keys( $actions ) ) ) ) );

	foreach ( $actions as $link => $action ) {
		$wp_admin_bar->add_menu( array( 'parent' => 'new-content', 'id' => $action[2], 'title' => $action[0], 'href' => admin_url($link) ) );
	}
}

/**
 * Add edit comments link with awaiting moderation count bubble.
 *
 * @since 3.1.0
 */
function wp_admin_bar_comments_menu( $wp_admin_bar ) {
	if ( !current_user_can('edit_posts') )
		return;

	$awaiting_mod = wp_count_comments();
	$awaiting_mod = $awaiting_mod->moderated;

	$awaiting_mod = $awaiting_mod ? "<span id='ab-awaiting-mod' class='pending-count'>" . number_format_i18n( $awaiting_mod ) . "</span>" : '';
	$wp_admin_bar->add_menu( array( 'id' => 'comments', 'title' => sprintf( __('Comments %s'), $awaiting_mod ), 'href' => admin_url('edit-comments.php') ) );
}

/**
 * Add "Appearance" menu with widget and nav menu submenu.
 *
 * @since 3.1.0
 */
function wp_admin_bar_appearance_menu( $wp_admin_bar ) {
	// You can have edit_theme_options but not switch_themes.
	if ( ! current_user_can('switch_themes') && ! current_user_can( 'edit_theme_options' ) )
		return;

	$wp_admin_bar->add_menu( array( 'id' => 'appearance', 'title' => __('Appearance'), 'href' => admin_url('themes.php') ) );

	if ( ! current_user_can( 'edit_theme_options' ) )
		return;

	if ( current_user_can( 'switch_themes' ) )
		$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'id' => 'themes', 'title' => __('Themes'), 'href' => admin_url('themes.php') ) );

	if ( current_theme_supports( 'widgets' )  )
		$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'id' => 'widgets', 'title' => __('Widgets'), 'href' => admin_url('widgets.php') ) );

	 if ( current_theme_supports( 'menus' ) || current_theme_supports( 'widgets' ) )
		$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'id' => 'menus', 'title' => __('Menus'), 'href' => admin_url('nav-menus.php') ) );

	if ( current_theme_supports( 'custom-background' ) )
		$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'id' => 'background', 'title' => __('Background'), 'href' => admin_url('themes.php?page=custom-background') ) );

	if ( current_theme_supports( 'custom-header' ) )
		$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'id' => 'header', 'title' => __('Header'), 'href' => admin_url('themes.php?page=custom-header') ) );
}

/**
 * Provide an update link if theme/plugin/core updates are available.
 *
 * @since 3.1.0
 */
function wp_admin_bar_updates_menu( $wp_admin_bar ) {
	if ( !current_user_can('install_plugins') )
		return;

	$plugin_update_count = $theme_update_count = $wordpress_update_count = 0;
	$update_plugins = get_site_transient( 'update_plugins' );
	if ( !empty($update_plugins->response) )
		$plugin_update_count = count( $update_plugins->response );
	$update_themes = get_site_transient( 'update_themes' );
	if ( !empty($update_themes->response) )
		$theme_update_count = count( $update_themes->response );
	/* @todo get_core_updates() is only available on admin page loads
	$update_wordpress = get_core_updates( array('dismissed' => false) );
	if ( !empty($update_wordpress) && !in_array( $update_wordpress[0]->response, array('development', 'latest') ) )
		$wordpress_update_count = 1;
	*/

	$update_count = $plugin_update_count + $theme_update_count + $wordpress_update_count;

	if ( !$update_count )
		return;

	$update_title = array();
	if ( $wordpress_update_count )
		$update_title[] = sprintf(__('%d WordPress Update'), $wordpress_update_count);
	if ( $plugin_update_count )
		$update_title[] = sprintf(_n('%d Plugin Update', '%d Plugin Updates', $plugin_update_count), $plugin_update_count);
	if ( $theme_update_count )
		$update_title[] = sprintf(_n('%d Theme Update', '%d Themes Updates', $theme_update_count), $theme_update_count);

	$update_title = !empty($update_title) ? esc_attr(implode(', ', $update_title)) : '';

	$update_title = "<span title='$update_title'>";
	$update_title .= sprintf( __('Updates %s'), "<span id='ab-updates' class='update-count'>" . number_format_i18n($update_count) . '</span>' );
	$update_title .= '</span>';

	$wp_admin_bar->add_menu( array( 'id' => 'updates', 'title' => $update_title, 'href' => network_admin_url( 'update-core.php' ) ) );
}

/**
 * Style and scripts for the admin bar.
 *
 * @since 3.1.0
 *
 */
function wp_admin_bar_header() { ?>
<style type="text/css" media="print">#wpadminbar { display:none; }</style>
<?php
}

/**
 * Default admin bar callback.
 *
 * @since 3.1.0
 *
 */
function _admin_bar_bump_cb() { ?>
<style type="text/css">
	html { margin-top: 28px !important; }
	* html body { margin-top: 28px !important; }
</style>
<?php
}

/**
 * Set the display status of the admin bar
 *
 * This can be called immediately upon plugin load.  It does not need to be called from a function hooked to the init action.
 *
 * @since 3.1.0
 *
 * @param bool $show Whether to allow the admin bar to show.
 * @return void
 */
function show_admin_bar( $show ) {
	global $show_admin_bar;
	$show_admin_bar = (bool) $show;
}

/**
 * Determine whether the admin bar should be showing.
 *
 * @since 3.1.0
 *
 * @return bool Whether the admin bar should be showing.
 */
function is_admin_bar_showing() {
	global $show_admin_bar, $pagenow;

	/* For all these types of request we never want an admin bar period */
	if ( defined('XMLRPC_REQUEST') || defined('APP_REQUEST') || defined('DOING_AJAX') || defined('IFRAME_REQUEST') )
		return false;

	if ( ! isset( $show_admin_bar ) ) {
		if ( ! is_user_logged_in() || 'wp-login.php' == $pagenow ) {
			$show_admin_bar = false;
		} else {
			$context = is_admin() ? 'admin' : 'front';
			$show_admin_bar = _get_admin_bar_pref( $context );
		}
	}

	$show_admin_bar = apply_filters( 'show_admin_bar', $show_admin_bar );

	return $show_admin_bar;
}

/**
 * Retrieve the admin bar display preference of a user based on context.
 *
 * @since 3.1.0
 * @access private
 *
 * @param string $context Context of this preference check, either 'admin' or 'front'
 * @param int $user Optional. ID of the user to check, defaults to 0 for current user
 * @return bool Whether the admin bar should be showing for this user
 */
function _get_admin_bar_pref( $context, $user = 0 ) {
	$pref = get_user_option( "show_admin_bar_{$context}", $user );
	if ( false === $pref )
		return 'admin' != $context || is_multisite();

	return 'true' === $pref;
}

?>