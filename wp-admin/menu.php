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

$menu[0] = array( __('Dashboard'), 'read', 'index.php' );

$menu[5] = array( __('Posts'), 'edit_posts', 'post-new.php', 'wp-menu-open' );
	$submenu['post-new.php'][5]  = array( __('Write'), 'edit_posts', 'post-new.php' );
	$submenu['post-new.php'][10]  = array( __('Drafts'), 'edit_posts', 'edit-post-drafts.php' );
	$submenu['post-new.php'][15]  = array( __('View All'), 'edit_posts', 'edit.php' );
	$submenu['post-new.php'][20] = array( __('Tags'), 'manage_categories', 'edit-tags.php' );
	$submenu['post-new.php'][25] = array( __('Categories'), 'manage_categories', 'categories.php' );

$menu[10] = array( __('Media'), 'upload_files', 'media-upload.php' );
	$submenu['media-upload.php'][5] = array( __('Upload New'), 'upload_files', 'media-upload.php?inline');
	$submenu['media-upload.php'][10] = array( __('View All'), 'upload_files', 'upload.php');

$menu[15] = array( __('Links'), 'manage_links', 'link-add.php' );
	$submenu['link-add.php'][5] = array( __('Add New'), 'manage_links', 'link-add.php' );
	$submenu['link-add.php'][10] = array( __('View All'), 'manage_links', 'link-manager.php' );
	$submenu['link-add.php'][15] = array( __('Link Categories'), 'manage_categories', 'edit-link-categories.php' );

$menu[20] = array( __('Pages'), 'edit_pages', 'page-new.php' );
	$submenu['page-new.php'][5] = array( __('Write'), 'edit_pages', 'page-new.php' );
	//$submenu['page-new.php'][10] = array( __('Drafts'), 'edit_pages', 'edit-pages.php?post_status=draft' );
	$submenu['page-new.php'][15] = array( __('View All'), 'edit_pages', 'edit-pages.php' );

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

$menu[45] = array( __('Help'), 'read', 'dummy.php' );
	$submenu['dummy.php'][5]  = array( __('Documentation'), 'read', __('http://codex.wordpress.org/') );
	$submenu['dummy.php'][10]  = array( __('Forums'), 'read', __('http://wordpress.org/support/') );
	$submenu['dummy.php'][15]  = array( __('Feedback'), 'read', __('http://wordpress.org/support/forum/4') );

// Back-compat for old top-levels
$_wp_real_parent_file['post.php'] = 'post-new.php'; 
$_wp_real_parent_file['edit.php'] = 'post-new.php';
$_wp_real_parent_file['edit-pages.php'] = 'page-new.php';

do_action('_admin_menu');

// Create list of page plugin hook names.
foreach ($menu as $menu_page) {
	$admin_page_hooks[$menu_page[2]] = sanitize_title($menu_page[0]);
}

$_wp_submenu_nopriv = array();
$_wp_menu_nopriv = array();
// Loop over submenus and remove pages for which the user does not have privs.
foreach ( array( 'submenu' ) as $sub_loop ) {
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
