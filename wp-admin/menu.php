<?php
/**
 * Build Administration Menu.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Constructs the admin menu bar.
 *
 * The elements in the array are :
 *     0: Menu item name
 *     1: Minimum level or capability required.
 *     2: The URL of the item's file
 *
 * @global array $menu
 * @name $menu
 * @var array
 */

$awaiting_mod = wp_count_comments();
$awaiting_mod = $awaiting_mod->moderated;

$inbox_num = count( wp_get_inbox_items() );
$awaiting_mod = wp_count_comments();
$awaiting_mod = $awaiting_mod->moderated;

$top_menu = $top_submenu = $menu = $submenu = array();

$top_menu[5]  = array( __('My Account'), 'read', 'profile.php' );
$top_menu[10] = array( __('My Dashboard'), 'read', 'index.php' );
$top_menu[15] = array( __('New Post'), 'edit_posts', 'post-new.php', 'highlighted' );
//$top_menu[20] = array( sprintf( __('Inbox (%s)'), "<span id='inbox-num' class='count-$inbox_num'><span class='inbox-count'>" . number_format_i18n($inbox_num) . "</span></span>" ), 'edit_posts', 'inbox.php' );
$top_menu[20] = array( sprintf( __('Comments (%s)'), "<span id='awaiting-mod' class='count-$awaiting_mod'><span class='comment-count'>" . number_format_i18n($awaiting_mod) . "</span></span>" ), 'edit_posts', 'edit-comments.php');
$top_menu[25] = array( __('Help'), 'read', 'index.php?help' ); // place holder

$top_submenu['profile.php'][5]  = array( __('Profile'), 'read', 'profile.php' );

$menu[0] = array( __('Dashboard'), 'read', 'index.php' );
	$submenu['index.php'][5]  = array( __('Overview'), 'read', 'index.php' );
	$submenu['index.php'][10]  = array( __('Inbox'), 'read', 'inbox.php' );

$menu[5] = array( __('Posts'), 'edit_posts', 'edit.php', 'wp-menu-open' );
	$submenu['edit.php'][5]  = array( __('Write'), 'edit_posts', 'post-new.php' );
	//$submenu['edit.php'][10]  = array( __('Drafts'), 'edit_posts', 'edit.php?post_status=draft' );
	$submenu['edit.php'][15]  = array( __('View All'), 'edit_posts', 'edit.php' );
	$submenu['edit.php'][20] = array( __('Tags'), 'manage_categories', 'edit-tags.php' );
	$submenu['edit.php'][25] = array( __('Categories'), 'manage_categories', 'categories.php' );

$menu[10] = array( __('Media'), 'upload_files', 'upload.php' );
	$submenu['upload.php'][5] = array( __('Upload New'), 'upload_files', 'media-upload.php');
	$submenu['upload.php'][10] = array( __('View All'), 'upload_files', 'upload.php');

$menu[15] = array( __('Links'), 'manage_links', 'link-manager.php' );
	$submenu['link-manager.php'][5] = array( __('Add New'), 'manage_links', 'link-add.php' );
	$submenu['link-manager.php'][10] = array( __('View All'), 'manage_links', 'link-manager.php' );
	$submenu['link-manager.php'][15] = array( __('Link Categories'), 'manage_categories', 'edit-link-categories.php' );

$menu[20] = array( __('Pages'), 'edit_pages', 'edit-pages.php' );
	$submenu['edit-pages.php'][5] = array( __('Write'), 'edit_pages', 'page-new.php' );
	//$submenu['edit-pages.php'][10] = array( __('Drafts'), 'edit_pages', 'edit-pages.php?post_status=draft' );
	$submenu['edit-pages.php'][15] = array( __('View All'), 'edit_pages', 'edit-pages.php' );

$menu[25] = array( __('Comments'), 'edit_posts', 'edit-comments.php' );

$menu[30] = array( __('Appearance'), 'switch_themes', 'themes.php' );
	$submenu['themes.php'][5]  = array(__('Themes'), 'switch_themes', 'themes.php');
	$submenu['themes.php'][10] = array(__('Editor'), 'edit_themes', 'theme-editor.php');

$menu[35] = array(__('Settings'), 'manage_options', 'options-general.php');
	$submenu['options-general.php'][10] = array(__('General'), 'manage_options', 'options-general.php');
	$submenu['options-general.php'][15] = array(__('Writing'), 'manage_options', 'options-writing.php');
	$submenu['options-general.php'][20] = array(__('Reading'), 'manage_options', 'options-reading.php');
	$submenu['options-general.php'][25] = array(__('Discussion'), 'manage_options', 'options-discussion.php');
	$submenu['options-general.php'][30] = array(__('Media'), 'manage_options', 'options-media.php');
	$submenu['options-general.php'][35] = array(__('Privacy'), 'manage_options', 'options-privacy.php');
	$submenu['options-general.php'][40] = array(__('Permalinks'), 'manage_options', 'options-permalink.php');
	$submenu['options-general.php'][45] = array(__('Miscellaneous'), 'manage_options', 'options-misc.php');
	$submenu['options-general.php'][50] = array( __('Users'), 'edit_users', 'users.php' );
	$submenu['options-general.php'][55] = array( __('Import'), 'import', 'import.php' );
	$submenu['options-general.php'][60] = array( __('Export'), 'import', 'export.php' );

$menu[40] = array( __('Plugins'), 'activate_plugins', 'plugins.php' );
	$submenu['plugins.php'][5]  = array( __('Manage'), 'activate_plugins', 'plugins.php' );
	$submenu['plugins.php'][10] = array( __('Editor'), 'edit_plugins', 'plugin-editor.php' );
	$submenu['plugins.php'][15] = array(__('Browse'), 'install_plugins', 'plugin-install.php');

// Back-compat for old top-levels
$_wp_real_parent_file['post.php'] = 'edit.php'; 
$_wp_real_parent_file['post-new.php'] = 'edit.php';

do_action('_admin_menu');

// Create list of page plugin hook names.
foreach ($menu as $menu_page) {
	$admin_page_hooks[$menu_page[2]] = sanitize_title($menu_page[0]);
}

$_wp_submenu_nopriv = array();
$_wp_menu_nopriv = array();
// Loop over submenus and remove pages for which the user does not have privs.
foreach ( array( 'top_submenu', 'submenu' ) as $sub_loop ) {
	foreach ($$sub_loop as $parent => $sub) {
		foreach ($sub as $index => $data) {
			if ( ! current_user_can($data[1]) ) {
				unset(${$sub_loop}[$parent][$index]);
				$_wp_submenu_nopriv[$parent][$data[2]] = true;
			}
		}

		if ( empty(${$sub_loop}[$parent]) )
			unset(${$sub_loop}[$parent]);
	}
}

// Loop over the top-level menu.
// Menus for which the original parent is not acessible due to lack of privs will have the next
// submenu in line be assigned as the new menu parent.
foreach ( $menu as $id => $data ) {
	if ( empty($submenu[$data[2]]) )
		continue;
	$subs = $submenu[$data[2]];
	$first_sub = array_shift($subs);
	$old_parent = $data[2];
	$new_parent = $first_sub[2];
	// If the first submenu is not the same as the assigned parent,
	// make the first submenu the new parent.
	if ( $new_parent != $old_parent ) {
		$_wp_real_parent_file[$old_parent] = $new_parent;
		$menu[$id][2] = $new_parent;

		foreach ($submenu[$old_parent] as $index => $data) {
			$submenu[$new_parent][$index] = $submenu[$old_parent][$index];
			unset($submenu[$old_parent][$index]);
		}
		unset($submenu[$old_parent]);

		if ( isset($_wp_submenu_nopriv[$old_parent]) )
			$_wp_submenu_nopriv[$new_parent] = $_wp_submenu_nopriv[$old_parent];
	}
}

do_action('admin_menu', '');

// Remove menus that have no accessible submenus and require privs that the user does not have.
// Run re-parent loop again.
foreach ( $menu as $id => $data ) {
	// If submenu is empty...
	if ( empty($submenu[$data[2]]) ) {
		// And user doesn't have privs, remove menu.
		if ( ! current_user_can($data[1]) ) {
			$_wp_menu_nopriv[$data[2]] = true;
			unset($menu[$id]);
		}
	}
}

unset($id);

uksort($menu, "strnatcasecmp"); // make it all pretty

if (! user_can_access_admin_page()) {
	do_action('admin_page_access_denied');
	wp_die( __('You do not have sufficient permissions to access this page.') );
}

?>
