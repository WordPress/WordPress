<?php
/**
 * Use the $wp_admin_bar global to add a menu for site admins and administrator controls.
 */
function wp_admin_bar_superadmin_menus() {
	global $wp_admin_bar, $wpdb;

	if ( !is_object( $wp_admin_bar ) || !is_super_admin() )
		return false;

	/* Add the "Super Admin" settings sub menu */
	if ( is_multisite() )
		wp_admin_bar_superadmin_settings_menu();
}
add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_superadmin_menus', 1000 );

/**
 *
 * Use the $wp_admin_bar global to add the super admin menu, providing admin options only visible to super admins.
 */
function wp_admin_bar_superadmin_settings_menu() {
	global $wp_admin_bar, $current_blog, $current_user;

	if ( !is_object( $wp_admin_bar ) || !is_super_admin() )
		return false;

	/* Add the main superadmin menu item */
	$wp_admin_bar->add_menu( array( 'id' => 'superadmin', 'title' => '&mu;', 'href' => '', 'meta' => array( 'class' => 'ab-sadmin' ) ) );

	wp_admin_bar_build_snackmenu();

	/* Get the settings we need for the current site */
	$matureaction = $current_blog->mature ? 'unmatureblog' : 'matureblog';
	$maturetext = $current_blog->mature ? esc_attr__('Unmark as mature') : esc_attr__('Mark as mature');
	$suspendtext = $current_blog->spam ? esc_attr('Unsuspend site') : esc_attr('Suspend site');
	$suspendaction = $current_blog->spam ? 'unspamblog' : 'spamblog';
	$mature_url = network_admin_url( "edit.php?action=confirm&amp;action2={$matureaction}&amp;id={$current_blog->blog_id}&amp;msg=" . urlencode( 'Are you sure you want to ' . strtolower( $maturetext ) . " {$current_blog->domain} as mature?" ) );
	$suspend_url = network_admin_url( "edit.php?action=confirm&amp;action2={$suspendaction}&amp;id={$current_blog->blog_id}&amp;msg=" . urlencode( 'Are you sure you want to ' . strtolower( $suspendtext ) . " {$current_blog->domain} ?" ) );

	/* Add the submenu items to the Super Admin menu */
	$wp_admin_bar->add_menu( array( 'parent' => 'superadmin', 'title' => __( 'Site Dashboard' ), 'href' => admin_url(), 'position' => 10 ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'superadmin', 'title' => __( 'Site Options' ),  'href' => network_admin_url( "sites.php?action=blogs&amp;searchaction=id&amp;s={$current_blog->blog_id}" ), 'position' => 30 ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'superadmin', 'title' => "$maturetext", 'href' => $mature_url, 'position' => 50 ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'superadmin', 'title' => "$suspendtext", 'href' => $suspend_url, 'position' => 80 ) );
}

function wp_admin_bar_build_snackmenu() {
	global $wp_admin_bar, $menu, $submenu, $pagenow;

	if ( !is_object( $wp_admin_bar ) || !is_super_admin() )
		return false;

	// Hide moderation count, filter removed at the bottom of this function
	add_filter( 'wp_count_comments', 'wp_admin_bar_removemodcount' );

	require_once( ABSPATH . 'wp-admin/includes/admin.php' );

	// menu.php assumes it is in the global scope and relies on the $wp_taxonomies global array
	$wp_taxonomies = array();
	require_once( ABSPATH . 'wp-admin/menu.php' );

	/* Add the snack menu submenu to the superadmin menu */
	$wp_admin_bar->add_menu( array( 'parent' => 'superadmin', 'title' => 'Snack Menu', 'href' => '/wp-admin/' ) );

	/* Loop through the submenus and add them */
	foreach ( (array) $menu as $key => $item ) {
		$admin_is_parent = false;
		$submenu_as_parent = false;

		if ( $submenu_as_parent && !empty($submenu[$item[2]]) ) {
			$submenu[$item[2]] = array_values($submenu[$item[2]]);  // Re-index.
			$menu_hook = get_plugin_page_hook($submenu[$item[2]][0][2], $item[2]);
			$menu_file = $submenu[$item[2]][0][2];
	
			if ( false !== $pos = strpos($menu_file, '?') )
				$menu_file = substr($menu_file, 0, $pos);
			if ( ( ('index.php' != $submenu[$item[2]][0][2]) && file_exists(WP_PLUGIN_DIR . "/$menu_file") ) || !empty($menu_hook)) {
				$admin_is_parent = true;
				$wp_admin_bar->add_menu( array( 'parent' => 'snack-menu', 'title' => $item[0], 'href' => admin_url("admin.php?page={$submenu[$item[2]][0][2]}") ) );
			} else {
				$wp_admin_bar->add_menu( array( 'parent' => 'snack-menu', 'title' => $item[0], 'href' => admin_url("{$submenu[$item[2]][0][2]}") ) );
			}
		} else if ( current_user_can($item[1]) ) {
			$menu_hook = get_plugin_page_hook($item[2], 'admin.php');
			$menu_file = $item[2];

			if ( false !== $pos = strpos($menu_file, '?') )
				$menu_file = substr($menu_file, 0, $pos);
			if ( ('index.php' != $item[2]) && file_exists(WP_PLUGIN_DIR . "/$menu_file") || !empty($menu_hook) ) {
				$admin_is_parent = true;
				$wp_admin_bar->add_menu( array( 'parent' => 'snack-menu', 'title' => $item[0], 'href' => admin_url("admin.php?page={$item[2]}") ) );
			} else {
				$wp_admin_bar->add_menu( array( 'parent' => 'snack-menu', 'title' => $item[0], 'href' => admin_url("{$item[2]}") ) );
			}
		}

		if ( !empty($submenu[$item[2]]) ) {
			$first = true;
			$unique_submenu = array();
			
			foreach ( $submenu[$item[2]] as $sub_key => $sub_item ) {
				if ( !current_user_can($sub_item[1]) || in_array( $sub_item[0], $unique_submenu ) )
					continue;
			
				$unique_submenu[] = $sub_item[0];
				
				if ( $first )
					$first = false;
				
				$menu_file = $item[2];
				if ( false !== $pos = strpos($menu_file, '?') )
					$menu_file = substr($menu_file, 0, $pos);
				
				$menu_hook = get_plugin_page_hook($sub_item[2], $item[2]);
				$sub_file = $sub_item[2];
				if ( false !== $pos = strpos($sub_file, '?') )
					$sub_file = substr($sub_file, 0, $pos);
				
				if ( ( ('index.php' != $sub_item[2]) && file_exists(WP_PLUGIN_DIR . "/$sub_file") ) || ! empty($menu_hook) ) {
					// If admin.php is the current page or if the parent exists as a file in the plugins or admin dir
				
					$parent_exists = (!$admin_is_parent && file_exists(WP_PLUGIN_DIR . "/$menu_file") && !is_dir(WP_PLUGIN_DIR . "/{$item[2]}") ) || file_exists($menu_file);
					if ( $parent_exists )
						$wp_admin_bar->add_menu( array( 'parent' => sanitize_title( $item[0] ), 'title' => $sub_item[0], 'href' => admin_url("{$item[2]}?page={$sub_item[2]}") ) );
					elseif ( 'admin.php' == $pagenow || !$parent_exists )
						$wp_admin_bar->add_menu( array( 'parent' => sanitize_title( $item[0] ), 'title' => $sub_item[0], 'href' => admin_url("admin.php?page={$sub_item[2]}") ) );
					else
						$wp_admin_bar->add_menu( array( 'parent' => sanitize_title( $item[0] ), 'title' => $sub_item[0], 'href' => admin_url("{$item[2]}?page={$sub_item[2]}") ) );
				} else {
					$wp_admin_bar->add_menu( array( 'parent' => sanitize_title( $item[0] ), 'title' => $sub_item[0], 'href' => admin_url("{$sub_item[2]}") ) );
				}
			}
		}
	}

	remove_filter( 'wp_count_comments', 'wp_admin_bar_removemodcount' );
}

// Short circuits wp_count_comments() for the front end
function wp_admin_bar_removemodcount( $stats ) {
	if ( is_admin() )
		return $stats;

	$stats = array(
		'moderated'      => 0,
		'approved'       => 0,
		'spam'           => 0,
		'trash'          => 0,
		'post-trashed'   => 0,
		'total_comments' => 0,
	);

	return (object) $stats;
}

?>