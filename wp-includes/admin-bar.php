<?php
/**
 * Admin Bar
 *
 * This code handles the building and rendering of the press bar.
 */
 
/**
 * Instantiate the admin bar object and set it up as a global for access elsewhere.
 *
 * @since 3.1.0
 * @return bool Whether the admin bar was successfully initialized.
 */
function wp_admin_bar_init() {
	global $wp_admin_bar;

	if ( ! is_admin_bar_showing() )
		return false;

	/* Load the admin bar class code ready for instantiation */
	require( ABSPATH . WPINC . '/admin-bar/admin-bar-class.php' );

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
add_action( 'init', 'wp_admin_bar_init' );

/**
 * Render the admin bar to the page based on the $wp_admin_bar->menu member var.
 * This is called very late on the footer actions so that it will render after anything else being
 * added to the footer.
 *
 * It includes the action "wp_before_admin_bar_render" which should be used to hook in and
 * add new menus to the admin bar. That way you can be sure that you are adding at most optimal point,
 * right before the admin bar is rendered. This also gives you access to the $post global, among others.
 *
 * @since 3.1.0
 */
function wp_admin_bar_render() {
	global $wp_admin_bar;

	if ( ! is_object( $wp_admin_bar ) )
		return false;

	$wp_admin_bar->load_user_locale_translations();

	do_action( 'wp_before_admin_bar_render' );

	$wp_admin_bar->render();

	do_action( 'wp_after_admin_bar_render' );
	
	$wp_admin_bar->unload_user_locale_translations();
}
add_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
add_action( 'admin_footer', 'wp_admin_bar_render', 1000 );

/**
 * Show the logged in user's gravatar as a separator.
 *
 * @since 3.1.0
 */
function wp_admin_bar_me_separator() {
	global $wp_admin_bar;
	$wp_admin_bar->add_menu( array( 'id' => 'me', 'title' => get_avatar( get_current_user_id(), 16 ), 'href' => get_edit_profile_url( get_current_user_id() ), ) );
}

/**
 * Add the "My Account" menu and all submenus.
 *
 * @since 3.1.0
 */
function wp_admin_bar_my_account_menu() {
	global $wp_admin_bar, $user_identity;

	$user_id = get_current_user_id();

	/* Add the 'My Account' menu */
	$wp_admin_bar->add_menu( array( 'id' => 'my-account', 'title' => $user_identity,  'href' => get_edit_profile_url( $user_id ) ) );

	/* Add the "My Account" sub menus */
	$wp_admin_bar->add_menu( array( 'parent' => 'my-account', 'title' => __( 'Edit My Profile' ), 'href' => get_edit_profile_url( $user_id ) ) );
	if ( is_multisite() )
		$wp_admin_bar->add_menu( array( 'parent' => 'my-account', 'title' => __( 'Dashboard' ), 'href' => get_dashboard_url( $user_id ), ) );
	else
		$wp_admin_bar->add_menu( array( 'parent' => 'my-account', 'title' => __( 'Dashboard' ), 'href' => admin_url(), ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'my-account', 'title' => __( 'Log Out' ), 'href' => wp_logout_url(), ) );
}

/**
 * Add the "My Sites/[Site Name]" menu and all submenus.
 *
 * @since 3.1.0
 */
function wp_admin_bar_my_blogs_menu() {
	global $wpdb, $wp_admin_bar;

	/* Add the 'My Dashboards' menu if the user has more than one site. */
	if ( count( $wp_admin_bar->user->blogs ) <= 1 )
		return;

	$wp_admin_bar->add_menu( array(  'id' => 'my-blogs', 'title' => __( 'My Sites' ),  'href' => $wp_admin_bar->user->account_domain, ) );

	$default = includes_url('images/wpmini-blue.png');

	foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {
		$blogdomain = preg_replace( '!^https?://!', '', $blog->siteurl );
		// @todo Replace with some favicon lookup.
		//$blavatar = '<img src="' . esc_url( blavatar_url( blavatar_domain( $blog->siteurl ), 'img', 16, $default ) ) . '" alt="Blavatar" width="16" height="16" />';
		$blavatar = '<img src="' . esc_url($default) . '" alt="' . esc_attr__( 'Blavatar' ) . '" width="16" height="16" />';

		$marker = '';
		if ( strlen($blog->blogname) > 35 )
			$marker = '...';

		if ( empty( $blog->blogname ) )
			$blogname = $blog->domain;
		else
			$blogname = substr( $blog->blogname, 0, 35 ) . $marker;

		if ( ! isset( $blog->visible ) || $blog->visible === true ) {
			$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-' . $blog->userblog_id, 'title' => $blavatar . $blogname,  'href' => $wp_admin_bar->proto . $blogdomain . '/wp-admin/', ) );
			$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-d', 'title' => __( 'Dashboard' ), 'href' => $wp_admin_bar->proto . $blogdomain . '/wp-admin/', ) );
			$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-n', 'title' => __( 'New Post' ), 'href' => $wp_admin_bar->proto . $blogdomain . '/wp-admin/post-new.php', ) );

			// @todo, stats plugins should add this:
			//$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-s', 'title' => __( 'Site Stats' ), 'href' => $wp_admin_bar->proto . $blogdomain . '/wp-admin/index.php?page=stats' ) );
			
			$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-c', 'title' => __( 'Manage Comments' ), 'href' => $wp_admin_bar->proto . $blogdomain . '/wp-admin/edit-comments.php', ) );
			$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-v', 'title' => __( 'Visit Site' ), 'href' => $wp_admin_bar->proto . $blogdomain, ) );
		}
	}

	/* Add the "Manage Sites" menu item */
	// @todo, use dashboard site.
	$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'manage-blogs', 'title' => __( 'Manage Sites' ), admin_url('my-sites.php'), ) );
}

/**
 * Show the blavatar of the current site as a separator.
 *
 * @since 3.1.0
 */
function wp_admin_bar_blog_separator() {
	global $wp_admin_bar, $current_blog;
	$default = includes_url('images/wpmini-blue.png');
	$wp_admin_bar->add_menu( array( 'id' => 'blog', 'title' => '<img class="avatar" src="' . $default . '" alt="' . esc_attr__( 'Current site avatar' ) . '" width="16" height="16" />',  'href' => home_url(), ) );
}


/**
 * Provide a shortlink.
 * 
 * @since 3.1.0
 */
function wp_admin_bar_shortlink_menu() {
	global $wp_admin_bar;

	$short = wp_get_shortlink( 0, 'query' );

	if ( ! empty( $short) )
		$wp_admin_bar->add_menu( array( 'id' => 'get-shortlink', 'title' => __( 'Shortlink' ), 'href' => '', ) );
}

/**
 * Provide an edit link for posts and terms.
 * 
 * @since 3.1.0
 */
function wp_admin_bar_edit_menu () {
	global $wp_admin_bar, $wp_query;

	$current_object = $wp_query->get_queried_object();

	if ( empty($current_object) )
		return;

	// @todo Use proper type name in edit strings
	if ( ! empty( $current_object->post_type ) && ( $post_type_object = get_post_type_object( $current_object->post_type ) ) && current_user_can( $post_type_object->cap->edit_post, $current_object->ID ) ) {
		$wp_admin_bar->add_menu( array( 'id' => 'edit', 'title' => __( 'Edit Post' ),  'href' => get_edit_post_link( $current_object->ID ), ) );
	} elseif ( ! empty( $current_object->taxonomy ) &&  ( $tax = get_taxonomy( $current_object->taxonomy ) ) && current_user_can( $tax->cap->edit_terms ) ) {
		$wp_admin_bar->add_menu( array( 'id' => 'edit', 'title' => __( 'Edit Post' ), 'href' => get_edit_term_link( $current_object->term_id, $current_object->taxonomy ), ) );
	}
}

function wp_admin_bar_new_content_menu() {
	global $wp_admin_bar;

	// @todo Handle CPTs, use post type object for strings
	$actions = array(
		'post-new.php' => array(__('Post'), 'edit_posts', 'new-post'),
		'post-new.php?post_type=page' => array(__('Page'), 'edit_pages', 'new-page'),
	);

	$user_can = false;
	foreach ( $actions as $action ) {
		if ( current_user_can($action[1]) ) {
			$user_can = true;
			break;
		}
	}

	if ( !$user_can )
		return;

	$wp_admin_bar->add_menu( array( 'id' => 'new-content', 'title' => __( 'New Content' ), 'href' => '', ) );

	foreach ( $actions as $link => $action ) {
		$wp_admin_bar->add_menu( array( 'parent' => 'new-content', 'id' => $action[2], 'title' => $action[0], 'href' => admin_url($link) ) );
	}
}

function wp_admin_bar_comments_menu() {
	global $wp_admin_bar;

	if ( !current_user_can('edit_posts') )
		return;

	$awaiting_mod = wp_count_comments();
	$awaiting_mod = $awaiting_mod->moderated;

	// @todo styling for awaiting mod count. Don't show count if zero?
	$wp_admin_bar->add_menu( array( 'id' => 'comments', 'title' => sprintf( __('Comments %s'), "<span id='awaiting-mod' class='count-$awaiting_mod'><span class='pending-count'>" . number_format_i18n($awaiting_mod) . "</span></span>" ), 'href' => admin_url('edit-comments.php') ) );
}

function wp_admin_bar_appearance_menu() {
	global $wp_admin_bar;

	if ( !current_user_can('switch themes') )
		return;

	$wp_admin_bar->add_menu( array( 'id' => 'appearance', 'title' => __('Appearance'), 'href' => admin_url('themes.php') ) );

	if ( !current_user_can('edit_theme_options') )
		return;

	if ( current_theme_supports( 'widgets' )  )
		$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'id' => 'widgets', 'title' => __('Widgets'), 'href' => admin_url('widgets.php') ) );

	 if ( current_theme_supports( 'menus' ) || current_theme_supports( 'widgets' ) )
		$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'id' => 'menus', 'title' => __('Menus'), 'href' => admin_url('nav-menus.php') ) );
}

function wp_admin_bar_updates_menu() {
	global $wp_admin_bar;

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

	$update_title = sprintf( __('Updates %s'), "<span class='update-plugins count-$update_count' title='$update_title'><span class='update-count'>" . number_format_i18n($update_count) . "</span></span>" );

	// @todo styling for awaiting mod count. Don't show count if zero?
	$wp_admin_bar->add_menu( array( 'id' => 'updates', 'title' => $update_title, 'href' => admin_url('update-core.php') ) );
}

/**
 * Style and scripts for the admin bar.
 *
 * @since 3.1.0
 * @todo move js into a admin-bar js file
 *
 */
function wp_admin_bar_header() {
	?>
	<style type="text/css" media="print">#wpadminbar { display:none; }</style>
	<?php
}

// @TODO do we still need this in core?
function wp_admin_body_style() {
	?>
	<style type="text/css">
		<?php 
		
		if ( 
			( empty( $_GET['nobump'] ) || is_admin() ) && 
			! strpos( $_SERVER['REQUEST_URI'], 'media-upload.php' ) 
		) : 
			?>
			body { padding-top: 28px !important; }
			<?php 
		endif; 

		if ( in_array( get_current_theme(), array('H3', 'H4', 'The Journalist v1.9') ) ) :
			?>
			body { padding-top: 28px; background-position: 0px 28px; }
			<?php
		endif;

		?>
	</style>
	<?php
}

/**
 * Determine whether the admin bar should be showing.
 *
 * @since 3.1.0
 *
 * @return bool Whether the admin bar should be showing.
 */
function is_admin_bar_showing() {
	global $show_admin_bar;
	if ( ! isset( $show_admin_bar ) || null === $show_admin_bar ) {
		$show_admin_bar = true;

		if ( defined('WP_SHOW_ADMIN_BAR') )
			$show_admin_bar = (bool) WP_SHOW_ADMIN_BAR;
		else if ( ! is_user_logged_in() || ( is_admin() && ! is_multisite() ) )
			$show_admin_bar = false;
	}

	$show_admin_bar = apply_filters( 'show_admin_bar', $show_admin_bar );

	return $show_admin_bar;
}
?>
