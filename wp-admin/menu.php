<?php
// This array constructs the admin menu bar.
//
// Menu item name
// The minimum level the user needs to access the item: between 0 and 10
// The URL of the item's file
$menu[0] = array(__('Dashboard'), 'read', 'index.php');

if (strpos($_SERVER['REQUEST_URI'], 'edit-pages.php') !== false)
	$menu[5] = array(__('Write'), 'edit_pages', 'page-new.php');
elseif (strpos($_SERVER['REQUEST_URI'], 'link-manager.php') !== false)
	$menu[5] = array(__('Write'), 'manage_links', 'link-add.php');
else
	$menu[5] = array(__('Write'), 'edit_posts', 'post-new.php');

if (strpos($_SERVER['REQUEST_URI'], 'page-new.php') !== false)
	$menu[10] = array(__('Manage'), 'edit_pages', 'edit-pages.php');
elseif (strpos($_SERVER['REQUEST_URI'], 'link-add.php') !== false)
	$menu[10] = array(__('Manage'), 'manage_links', 'link-manager.php');
else
	$menu[10] = array(__('Manage'), 'edit_posts', 'edit.php');

$awaiting_mod = wp_count_comments();
$awaiting_mod = $awaiting_mod->moderated;
$menu[15] = array(__('Design'), 'switch_themes', 'themes.php');
$menu[20] = array( sprintf( __('Comments %s'), "<span id='awaiting-mod' class='count-$awaiting_mod'><span class='comment-count'>$awaiting_mod</span></span>" ), 'edit_posts', 'edit-comments.php');
$menu[30] = array(__('Settings'), 'manage_options', 'options-general.php');
$menu[35] = array(__('Plugins'), 'activate_plugins', 'plugins.php');
if ( current_user_can('edit_users') )
	$menu[40] = array(__('Users'), 'edit_users', 'users.php');
else
	$menu[40] = array(__('Profile'), 'read', 'profile.php');

$_wp_real_parent_file['post.php'] = 'post-new.php'; // Back-compat
$submenu['post-new.php'][5] = array(__('Post'), 'edit_posts', 'post-new.php');
$submenu['post-new.php'][10] = array(__('Page'), 'edit_pages', 'page-new.php');
$submenu['post-new.php'][15] = array(__('Link'), 'manage_links', 'link-add.php');

$submenu['edit-comments.php'][5] = array(__('Comments'), 'edit_posts', 'edit-comments.php');

$submenu['edit.php'][5] = array(__('Posts'), 'edit_posts', 'edit.php');
$submenu['edit.php'][10] = array(__('Pages'), 'edit_pages', 'edit-pages.php');
$submenu['edit.php'][15] = array(__('Links'), 'manage_links', 'link-manager.php');
$submenu['edit.php'][20] = array(__('Categories'), 'manage_categories', 'categories.php');
$submenu['edit.php'][25] = array(__('Tags'), 'manage_categories', 'edit-tags.php');
$submenu['edit.php'][30] = array(__('Link Categories'), 'manage_categories', 'edit-link-categories.php');
$submenu['edit.php'][35] = array(__('Media Library'), 'upload_files', 'upload.php');
$submenu['edit.php'][40] = array(__('Import'), 'import', 'import.php');
$submenu['edit.php'][45] = array(__('Export'), 'import', 'export.php');

if ( current_user_can('edit_users') ) {
	$_wp_real_parent_file['profile.php'] = 'users.php'; // Back-compat for plugins adding submenus to profile.php.
	$submenu['users.php'][5] = array(__('Authors &amp; Users'), 'edit_users', 'users.php');
	$submenu['users.php'][10] = array(__('Your Profile'), 'read', 'profile.php');
} else {
	$_wp_real_parent_file['users.php'] = 'profile.php';
	$submenu['profile.php'][5] = array(__('Your Profile'), 'read', 'profile.php');
}

$submenu['options-general.php'][10] = array(__('General'), 'manage_options', 'options-general.php');
$submenu['options-general.php'][15] = array(__('Writing'), 'manage_options', 'options-writing.php');
$submenu['options-general.php'][20] = array(__('Reading'), 'manage_options', 'options-reading.php');
$submenu['options-general.php'][25] = array(__('Discussion'), 'manage_options', 'options-discussion.php');
$submenu['options-general.php'][30] = array(__('Privacy'), 'manage_options', 'options-privacy.php');
$submenu['options-general.php'][35] = array(__('Permalinks'), 'manage_options', 'options-permalink.php');
$submenu['options-general.php'][40] = array(__('Miscellaneous'), 'manage_options', 'options-misc.php');

$submenu['plugins.php'][5] = array(__('Plugins'), 'activate_plugins', 'plugins.php');
$submenu['plugins.php'][10] = array(__('Plugin Editor'), 'edit_plugins', 'plugin-editor.php');

$submenu['themes.php'][5] = array(__('Themes'), 'switch_themes', 'themes.php');
$submenu['themes.php'][10] = array(__('Theme Editor'), 'edit_themes', 'theme-editor.php');

do_action('_admin_menu');

// Create list of page plugin hook names.
foreach ($menu as $menu_page) {
	$admin_page_hooks[$menu_page[2]] = sanitize_title($menu_page[0]);
}

$_wp_submenu_nopriv = array();
$_wp_menu_nopriv = array();
// Loop over submenus and remove pages for which the user does not have privs.
foreach ($submenu as $parent => $sub) {
	foreach ($sub as $index => $data) {
		if ( ! current_user_can($data[1]) ) {
			unset($submenu[$parent][$index]);
			$_wp_submenu_nopriv[$parent][$data[2]] = true;
		}
	}

	if ( empty($submenu[$parent]) )
		unset($submenu[$parent]);
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
