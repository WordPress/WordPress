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
	$wp_admin_bar->add_menu( array( 'id' => 'me', 'title' => get_avatar( get_current_user_id(), 16 ), 'href' => admin_url('profile.php'), ) );
}

/**
 * Add the "My Account" menu and all submenus.
 *
 * @since 3.1.0
 */
function wp_admin_bar_my_account_menu() {
	global $wp_admin_bar;

	/* Add the 'My Account' menu */
	$wp_admin_bar->add_menu( array( 'id' => 'my-account', 'title' => __( 'My Account' ),  'href' => admin_url('profile.php'), ) );

	/* Add the "My Account" sub menus */
	$wp_admin_bar->add_menu( array( 'parent' => 'my-account', 'title' => __( 'Edit My Profile' ), 'href' => admin_url('profile.php'), ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'my-account', 'title' => __( 'Global Dashboard' ), 'href' => admin_url(), ) );
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
	if ( count( $wp_admin_bar->user->blogs ) > 1 ) {
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

	/* Add the 'My Dashboard' menu if the user only has one site. */
	} else {
		$wp_admin_bar->add_menu( array( 'id' => 'my-blogs', 'title' => __( 'My Site' ), 'href' => $wp_admin_bar->user->account_domain, ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-1-d', 'title' => __( 'Dashboard' ), 'href' => admin_url(),) );
		$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-1-n', 'title' => __( 'New Post' ), 'href' => admin_url('post-new.php'),) );

		// @todo Stats plugins should add this.
		//$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-1-s', 'title' => __( 'Site Stats' ), 'href' => admin_url('index.php?page=stats') ) );

		$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-1-c','title' => __( 'Manage Comments' ), 'href' => admin_url('edit-comments.php'), ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-1-v', 'title' => __( 'Visit Site' ), 'href' => home_url(),) );
	}
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
 * Site info menu
 * 
 * @since 3.1.0
 */
function wp_admin_bar_bloginfo_menu() {
	global $wp_admin_bar;
	
	$short = wp_get_shortlink( 0, 'query' );
	if ( ! empty( $short) )
		$wp_admin_bar->add_menu( array( 'id' => 'get-shortlink', 'title' => __( 'Get Shortlink' ), 'href' => '', ) );
}

/**
 * Provide an edit link for posts and terms.
 * 
 * @since 3.1.0
 */
function wp_admin_bar_edit_menu() {
	global $wp_admin_bar, $wp_query;

	$current_object = $wp_query->get_queried_object();

	if ( empty( $current_object ) ) 
		return false;

	if ( ! empty( $current_object->post_type ) && ( $post_type_object = get_post_type_object( $current_object->post_type ) ) && current_user_can( $post_type_object->cap->edit_post, $current_object->ID ) ) {
		$wp_admin_bar->add_menu( array( 'id' => 'edit', 'title' => __( 'Edit' ),  'href' => get_edit_post_link( $current_object->ID ), ) );
	} elseif ( ! empty( $current_object->taxonomy ) &&  ( $tax = get_taxonomy( $current_object->taxonomy ) ) && current_user_can( $tax->cap->edit_terms ) ) {
		$wp_admin_bar->add_menu( array( 'id' => 'edit', 'title' => __( 'Edit' ), 'href' => get_edit_term_link( $current_object->term_id, $current_object->taxonomy ), ) );
	}
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
