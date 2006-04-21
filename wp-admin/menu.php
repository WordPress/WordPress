<?php
// This array constructs the admin menu bar.
//
// Menu item name
// The minimum level the user needs to access the item: between 0 and 10
// The URL of the item's file
$menu[0] = array(__('Dashboard'), 'read', 'index.php');
$menu[5] = array(__('Write'), 'edit_posts', 'post-new.php');
$menu[10] = array(__('Manage'), 'edit_posts', 'edit.php');
$menu[20] = array(__('Bookmarks'), 'manage_links', 'link-manager.php');
$menu[25] = array(__('Presentation'), 'switch_themes', 'themes.php');
$menu[30] = array(__('Plugins'), 'activate_plugins', 'plugins.php');
if ( current_user_can('edit_users') )
	$menu[35] = array(__('Users'), 'read', 'profile.php');
else
	$menu[35] = array(__('Profile'), 'read', 'profile.php');
$menu[40] = array(__('Options'), 'manage_options', 'options-general.php');
$menu[45] = array(__('Import'), 'import', 'import.php');

$submenu['post-new.php'][5] = array(__('Write Post'), 'edit_posts', 'post-new.php');
$submenu['post-new.php'][10] = array(__('Write Page'), 'edit_pages', 'page-new.php');

$submenu['edit.php'][5] = array(__('Posts'), 'edit_posts', 'edit.php');
$submenu['edit.php'][10] = array(__('Pages'), 'edit_pages', 'edit-pages.php');
$submenu['edit.php'][15] = array(__('Categories'), 'manage_categories', 'categories.php');
$submenu['edit.php'][20] = array(__('Comments'), 'edit_posts', 'edit-comments.php');
$awaiting_mod = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'");
$submenu['edit.php'][25] = array(sprintf(__("Awaiting Moderation (%s)"), "<span id='awaitmod'>$awaiting_mod</span>"), 'edit_posts', 'moderation.php');
$submenu['edit.php'][30] = array(__('Files'), 'edit_files', 'templates.php');

$submenu['link-manager.php'][5] = array(__('Manage Bookmarks'), 'manage_links', 'link-manager.php');
$submenu['link-manager.php'][10] = array(__('Add Bookmark'), 'manage_links', 'link-add.php');
$submenu['link-manager.php'][20] = array(__('Import Bookmarks'), 'manage_links', 'link-import.php');

$submenu['profile.php'][5] = array(__('Your Profile'), 'read', 'profile.php');
$submenu['profile.php'][10] = array(__('Authors &amp; Users'), 'edit_users', 'users.php');

$submenu['options-general.php'][10] = array(__('General'), 'manage_options', 'options-general.php');
$submenu['options-general.php'][15] = array(__('Writing'), 'manage_options', 'options-writing.php');
$submenu['options-general.php'][20] = array(__('Reading'), 'manage_options', 'options-reading.php');
$submenu['options-general.php'][25] = array(__('Discussion'), 'manage_options', 'options-discussion.php');
$submenu['options-general.php'][30] = array(__('Permalinks'), 'manage_options', 'options-permalink.php');
$submenu['options-general.php'][35] = array(__('Miscellaneous'), 'manage_options', 'options-misc.php');

$submenu['plugins.php'][5] = array(__('Plugins'), 'activate_plugins', 'plugins.php');
$submenu['plugins.php'][10] = array(__('Plugin Editor'), 'edit_plugins', 'plugin-editor.php');

$submenu['themes.php'][5] = array(__('Themes'), 'switch_themes', 'themes.php');
$submenu['themes.php'][10] = array(__('Theme Editor'), 'edit_themes', 'theme-editor.php');

// Loop over submenus and remove pages for which the user does not have privs.
foreach ($submenu as $parent => $sub) {
	foreach ($sub as $index => $data) {
		if ( ! current_user_can($data[1]) ) {
			$menu_nopriv[$data[2]] = true;
			unset($submenu[$parent][$index]);
		}
	}
	
	if ( empty($submenu[$parent]) )
		unset($submenu[$parent]);
}

// Loop over the top-level menu.
// Remove menus that have no accessible submenus and require privs that the user does not have.
// Menus for which the original parent is not acessible due to lack of privs will have the next
// submenu in line be assigned as the new menu parent. 
foreach ( $menu as $id => $data ) {
	// If submenu is empty...
	if ( empty($submenu[$data[2]]) ) {
		// And user doesn't have privs, remove menu.
		if ( ! current_user_can($data[1]) ) {
			$menu_nopriv[$data[2]] = true;
			unset($menu[$id]);
		}
	} else {
		$subs = $submenu[$data[2]];
		$first_sub = array_shift($subs);
		$old_parent = $data[2];
		$new_parent = $first_sub[2];
		// If the first submenu is not the same as the assigned parent,
		// make the first submenu the new parent.
		if ( $new_parent != $old_parent ) {
			$real_parent_file[$old_parent] = $new_parent;
			$menu[$id][2] = $new_parent;
			
			foreach ($submenu[$old_parent] as $index => $data) {
				$submenu[$new_parent][$index] = $submenu[$old_parent][$index];
				unset($submenu[$old_parent][$index]);
			}
			unset($submenu[$old_parent]);	
		}
	}
}

// Create list of page plugin hook names.
foreach ($menu as $menu_page) {
	$admin_page_hooks[$menu_page[2]] = sanitize_title($menu_page[0]);
}

do_action('admin_menu', '');
ksort($menu); // make it all pretty

if (! user_can_access_admin_page()) {
	die( __('You do not have sufficient permissions to access this page.') );
}

?>
