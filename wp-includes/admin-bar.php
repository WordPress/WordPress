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
		$avatar = get_avatar( get_current_user_id(), 28 );
		$id     = ( ! empty( $avatar ) ) ? 'my-account-with-avatar' : 'my-account';
		$howdy  = sprintf( __('Howdy, %1$s'), $user_identity );

		$wp_admin_bar->add_menu( array( 'id' => $id, 'title' => $howdy . $avatar,  'href' => get_edit_profile_url( $user_id ) ) );

		/* Add the "My Account" sub menus */
		$wp_admin_bar->add_menu( array( 'id' => 'edit-profile', 'parent' => $id, 'title' => __( 'Edit My Profile' ), 'href' => get_edit_profile_url( $user_id ) ) );
		$wp_admin_bar->add_menu( array( 'id' => 'logout', 'parent' => $id, 'title' => __( 'Log Out' ), 'href' => wp_logout_url() ) );
	}
}

/**
 * Add the "Blog Name" menu in the front end.
 *
 * @since 3.3.0
 */
function wp_admin_bar_blog_front_menu( $wp_admin_bar ) {
	$blogname = get_bloginfo('name');

	if ( empty( $blogname ) )
		$blogname = preg_replace( '#^(https?://)?(www.)?#', '', get_home_url() );


	$wp_admin_bar->add_menu( array(
		'id'    => 'blog-name',
		'title' => $blogname,
		'href'  => admin_url(),
	) );

	// Add Dashboard item.
	$wp_admin_bar->add_menu( array(
		'id'     => 'dashboard',
		'title'  => __( 'Dashboard' ),
		'href'   => admin_url(),
		'parent' => 'blog-name',
	) );

	wp_admin_bar_appearance_menu( $wp_admin_bar );
}

/**
 * Add the "Blog Name" menu in the admin.
 *
 * @since 3.3.0
 */
function wp_admin_bar_blog_admin_menu( $wp_admin_bar ) {
	global $current_site;

	if ( is_network_admin() ) {
		$title = sprintf( __('Network Admin: %s'), esc_html($current_site->site_name) );
		$url   = '#';
	} elseif ( is_user_admin() ) {
		$title = sprintf( __('Global Dashboard: %s'), esc_html($current_site->site_name) );
		$url   = '#';
	} else {
		$title = get_bloginfo('name');
		$url   = get_home_url();

		if ( empty( $title ) )
			$title = preg_replace( '#^(https?://)?(www.)?#', '', $url );
	}

	$wp_admin_bar->add_menu( array(
		'id'    => 'blog-name',
		'title' => $title,
		'href'  => $url,
	) );
}

/**
 * Add the "My Sites/[Site Name]" menu and all submenus.
 *
 * @since 3.1.0
 */
function wp_admin_bar_my_sites_menu( $wp_admin_bar ) {
	global $wpdb;

	/* Add the 'My Sites' menu if the user has more than one site. */
	// if ( count( $wp_admin_bar->user->blogs ) <= 1 )
	// 	return;

	$grey_wp_logo_url = admin_url( 'images/wp-logo.png' );

	$grey_wp_logo = '<img src="' . esc_url( $grey_wp_logo_url ) . '" alt="' . esc_attr__( 'Blavatar' ) . '" width="16" height="16" class="blavatar"/>';

	if ( is_multisite() )
		$url = admin_url( 'my-sites.php' );
	else
		$url = admin_url();

	$wp_admin_bar->add_menu( array(
		'id'    => 'my-blogs',
		'title' => $grey_wp_logo,
		'href'  => $url,
	) );

	// Add network admin link
	if ( is_multisite() && is_super_admin() && ! is_network_admin() ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'my-blogs',
			'id'     => 'network-admin',
			'title'  => __('Network Admin'),
			'href'   => network_admin_url(),
		) );
	}

	// Add blog links
	$blue_wp_logo_url = includes_url('images/wpmini-blue.png');

	foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {
		// Skip the current blog (unless we're in the network/user admin).
		if ( $blog->userblog_id == get_current_blog_id() && ! is_network_admin() && ! is_user_admin() ) {
			continue;
		}

		// @todo Replace with some favicon lookup.
		//$blavatar = '<img src="' . esc_url( blavatar_url( blavatar_domain( $blog->siteurl ), 'img', 16, $blue_wp_logo_url ) ) . '" alt="Blavatar" width="16" height="16" />';
		$blavatar = '<img src="' . esc_url($blue_wp_logo_url) . '" alt="' . esc_attr__( 'Blavatar' ) . '" width="16" height="16" class="blavatar"/>';

		$blogname = empty( $blog->blogname ) ? $blog->domain : $blog->blogname;

		$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-' . $blog->userblog_id, 'title' => $blavatar . $blogname,  'href' => get_admin_url($blog->userblog_id) ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-d', 'title' => __( 'Dashboard' ), 'href' => get_admin_url($blog->userblog_id) ) );

		if ( current_user_can_for_blog( $blog->userblog_id, 'edit_posts' ) ) {
			$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-n', 'title' => __( 'New Post' ), 'href' => get_admin_url($blog->userblog_id, 'post-new.php') ) );
			$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-c', 'title' => __( 'Manage Comments' ), 'href' => get_admin_url($blog->userblog_id, 'edit-comments.php') ) );
		}

		$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-v', 'title' => __( 'Visit Site' ), 'href' => get_home_url($blog->userblog_id) ) );
	}

	// Add WordPress.org link
	$wp_admin_bar->add_menu( array(
		'parent' => 'my-blogs',
		'id'     => 'wporg',
		'title'  => __('WordPress.org'),
		'href'   => 'http://wordpress.org',
	) );
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
	global $post, $tag;

	if ( is_admin() ) {
		$current_screen = get_current_screen();

		if ( 'post' == $current_screen->base
			&& 'add' != $current_screen->action
			&& ( $post_type_object = get_post_type_object( $post->post_type ) )
			&& current_user_can( $post_type_object->cap->read_post, $post->ID )
			&& ( $post_type_object->public ) )
		{
			$wp_admin_bar->add_menu( array(
				'id' => 'view',
				'title' => $post_type_object->labels->view_item,
				'href' => get_permalink( $post->ID )
			) );
		} elseif ( 'edit-tags' == $current_screen->base
			&& isset( $tag ) && is_object( $tag )
			&& ( $tax = get_taxonomy( $tag->taxonomy ) )
			&& $tax->public )
		{
			$wp_admin_bar->add_menu( array(
				'id' => 'view',
				'title' => $tax->labels->view_item,
				'href' => get_term_link( $tag )
			) );
		}
	} else {
		$current_object = get_queried_object();

		if ( empty($current_object) )
			return;

		if ( ! empty( $current_object->post_type )
			&& ( $post_type_object = get_post_type_object( $current_object->post_type ) )
			&& current_user_can( $post_type_object->cap->edit_post, $current_object->ID )
			&& ( $post_type_object->show_ui || 'attachment' == $current_object->post_type ) )
		{
			$wp_admin_bar->add_menu( array(
				'id' => 'edit',
				'title' => $post_type_object->labels->edit_item,
				'href' => get_edit_post_link( $current_object->ID )
			) );
		} elseif ( ! empty( $current_object->taxonomy )
			&& ( $tax = get_taxonomy( $current_object->taxonomy ) )
			&& current_user_can( $tax->cap->edit_terms )
			&& $tax->show_ui )
		{
			$wp_admin_bar->add_menu( array(
				'id' => 'edit',
				'title' => $tax->labels->edit_item,
				'href' => get_edit_term_link( $current_object->term_id, $current_object->taxonomy )
			) );
		}
	}
}

/**
 * Add "Add New" menu.
 *
 * @since 3.1.0
 */
function wp_admin_bar_new_content_menu( $wp_admin_bar ) {
	$actions = array();
	foreach ( (array) get_post_types( array( 'show_in_admin_bar' => true ), 'objects' ) as $ptype_obj ) {
		if ( ! current_user_can( $ptype_obj->cap->edit_posts ) )
			continue;

		$actions[ 'post-new.php?post_type=' . $ptype_obj->name ] = array( $ptype_obj->labels->name_admin_bar, $ptype_obj->cap->edit_posts, 'new-' . $ptype_obj->name );
	}

	if ( current_user_can( 'upload_files' ) )
		$actions[ 'media-new.php' ] = array( _x( 'Media', 'add new from admin bar' ), 'upload_files', 'new-media' );

	if ( current_user_can( 'manage_links' ) )
		$actions[ 'link-add.php' ] = array( _x( 'Link', 'add new from admin bar' ), 'manage_links', 'new-link' );

	if ( current_user_can( 'create_users' ) || current_user_can( 'promote_users' ) )
		$actions[ 'user-new.php' ] = array( _x( 'User', 'add new from admin bar' ), 'create_users', 'new-user' );

	if ( ! is_multisite() && current_user_can( 'install_themes' ) )
		$actions[ 'theme-install.php' ] = array( _x( 'Theme', 'add new from admin bar' ), 'install_themes', 'new-theme' );

	if ( ! is_multisite() && current_user_can( 'install_plugins' ) )
		$actions[ 'plugin-install.php' ] = array( _x( 'Plugin', 'add new from admin bar' ), 'install_plugins', 'new-plugin' );

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
	$awaiting_mod = number_format_i18n( $awaiting_mod->moderated );

	$bubble  = "<div class='ab-comments-bubble'>";
	$bubble .= "<div class='ab-comments-count'>$awaiting_mod</div>";
	$bubble .= "<div class='ab-comments-arrow'></div>";
	$bubble .= "</div>";

	$wp_admin_bar->add_menu( array(
		'id'    => 'comments',
		'title' => $bubble,
		'href'  => admin_url('edit-comments.php'),
	) );
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

	$wp_admin_bar->add_menu( array(
		'id'     => 'appearance',
		'title'  => __('Appearance'),
		'href'   => admin_url('themes.php'),
		'parent' => 'blog-name',
	) );

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

	$update_data = wp_get_update_data();

	if ( !$update_data['counts']['total'] )
		return;

	$update_title = "<span title='{$update_data['title']}'>";
	$update_title .= sprintf( __('Updates %s'), "<span id='ab-updates' class='update-count'>" . number_format_i18n($update_data['counts']['total']) . '</span>' );
	$update_title .= '</span>';

	$wp_admin_bar->add_menu( array( 'id' => 'updates', 'title' => $update_title, 'href' => network_admin_url( 'update-core.php' ) ) );
}

/**
 * Add screen options link.
 *
 * @since 3.3.0
 */
function wp_admin_bar_screen_options_menu( $wp_admin_bar ) {
	$wp_admin_bar->add_menu( array(
		'id'    => 'screen-options',
		'title' => __('Screen Options'),
		'href'  => '#',
		'meta'  => array(
			'class' => 'screen-meta-toggle hide-if-no-js',
		),
	) );
}

/**
 * Add help link.
 *
 * @since 3.3.0
 */
function wp_admin_bar_help_menu( $wp_admin_bar ) {
	$wp_admin_bar->add_menu( array(
		'id'    => 'help',
		'title' => __('Help'),
		'href'  => '#',
		'meta'  => array(
			'class' => 'screen-meta-toggle hide-if-no-js',
		),
	) );
}

/**
 * Add search form.
 *
 * @since 3.3.0
 */
function wp_admin_bar_search_menu( $wp_admin_bar ) {
	$form  = '<div id="adminbarsearch-wrap">';
	$form .= '<form action="' . home_url() . '" method="get" id="adminbarsearch">';
	$form .= '<input class="adminbar-input" name="s" id="adminbar-search"';
	$form .= 'type="text" value="" maxlength="150" placeholder="' . esc_attr__( 'Search' ) . '" />';
	$form .= '<input type="submit" class="adminbar-button" value="' . __('Search') . '"/>';
	$form .= '</form>';
	$form .= '</div>';

	$wp_admin_bar->add_menu( array(
		'id'    => 'search',
		'title' => $form,
		'href'  => '#',
		'meta'  => array(
			'class'   => 'admin-bar-search',
			// @TODO: Replace me with something far less hacky
			'onclick' => 'if ( event.target.value != "Search" ) { return false; }',
		),
	) );
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
<style type="text/css" media="screen">
	html { margin-top: 28px !important; }
	* html body { margin-top: 28px !important; }
</style>
<?php
}

/**
 * Set the display status of the admin bar.
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
 * @param string $context Context of this preference check, either 'admin' or 'front'.
 * @param int $user Optional. ID of the user to check, defaults to 0 for current user.
 * @return bool Whether the admin bar should be showing for this user.
 */
function _get_admin_bar_pref( $context, $user = 0 ) {
	$pref = get_user_option( "show_admin_bar_{$context}", $user );
	if ( false === $pref )
		return true;

	return 'true' === $pref;
}

?>