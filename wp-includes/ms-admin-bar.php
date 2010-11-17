<?php

/**
 *
 * Use the $wp_admin_bar global to add the super admin menu, providing admin options only visible to super admins.
 */
function wp_admin_bar_superadmin_settings_menu() {
	global $wp_admin_bar, $current_blog, $current_user;

	/* Add the main superadmin menu item */
	$wp_admin_bar->add_menu( array(  'id' => 'superadmin', 'title' => '&mu;', 'href' => '', 'meta' => array( 'class' => 'ab-sadmin' ), ) );

	/* Get the settings we need for the current site */
	$matureaction = $current_blog->mature ? 'unmatureblog' : 'matureblog';
	$maturetext_confirm = $current_blog->mature ?
		sprintf(
			esc_attr__( 'Are you sure you want to unmark %s as mature?' ),
			$current_blog->domain
		) :
		sprintf(
			esc_attr__( 'Are you sure you want to mark %s as mature?' ),
			$current_blog->domain
		);

	$suspendaction = $current_blog->spam ? 'unspamblog' : 'spamblog';
	$suspendtext_confirm = $current_blog->spam ?
		sprintf(
			esc_attr__( 'Are you sure you want to unsuspend site %s?' ),
			$current_blog->domain
		) :
		sprintf(
			esc_attr__( 'Are you sure you want to suspend site %s?' ),
			$current_blog->domain
		);

	$mature_url = network_admin_url( "edit.php?action=confirm&amp;action2={$matureaction}&amp;id={$current_blog->blog_id}&amp;msg=" . urlencode( $maturetext_confirm ) );
	$suspend_url = network_admin_url( "edit.php?action=confirm&amp;action2={$suspendaction}&amp;id={$current_blog->blog_id}&amp;msg=" . urlencode( $suspendtext_confirm ) );

	/* Add the submenu items to the Super Admin menu */
	$wp_admin_bar->add_menu( array( 'parent' => 'superadmin', 'title' => __( 'Network Admin' ), 'href' => network_admin_url(), 'position' => 5, ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'superadmin', 'title' => __( 'Site Admin' ), 'href' => admin_url(), 'position' => 10, ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'superadmin', 'title' => __( 'Site Options' ), 'href' => network_admin_url( "site-info.php?id={$current_blog->blog_id}" ), 'position' => 30, ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'superadmin', 'title' => ( $current_blog->mature ? __('Unmark as mature') : __('Mark as mature') ), 'href' => $mature_url, 'position' => 50, ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'superadmin', 'title' => ( $current_blog->spam ? __('Unsuspend site') : __('Suspend site') ), 'href' => $suspend_url, 'position' => 80, ) );
}

?>
