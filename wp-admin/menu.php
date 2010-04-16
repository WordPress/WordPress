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
 *     3: Class
 *     4: ID
 *     5: Icon for top level menu
 *
 * @global array $menu
 * @name $menu
 * @var array
 */

$awaiting_mod = wp_count_comments();
$awaiting_mod = $awaiting_mod->moderated;

if ( is_multisite() && is_super_admin() ) {
	/* translators: Network menu item */
	$menu[0] = array(__('Super Admin'), 'manage_network', 'ms-admin.php', '', 'menu-top menu-top-first menu-icon-site', 'menu-site', 'div');
	$submenu[ 'ms-admin.php' ][1] = array( __('Admin'), 'manage_network', 'ms-admin.php' );
	/* translators: Sites menu item */
	$submenu[ 'ms-admin.php' ][5] = array( __('Sites'), 'manage_sites', 'ms-sites.php' );
	$submenu[ 'ms-admin.php' ][10] = array( __('Users'), 'manage_network_users', 'ms-users.php' );
	$submenu[ 'ms-admin.php' ][20] = array( __('Themes'), 'manage_network_themes', 'ms-themes.php' );
	$submenu[ 'ms-admin.php' ][25] = array( __('Options'), 'manage_network_options', 'ms-options.php' );
	$submenu[ 'ms-admin.php' ][30] = array( __('Update'), 'manage_network', 'ms-upgrade-network.php' );

	$menu[1] = array( '', 'read', 'separator1', '', 'wp-menu-separator' );

	$menu[2] = array( __('Dashboard'), 'read', 'index.php', '', 'menu-top menu-icon-dashboard', 'menu-dashboard', 'div' );
} else {
	$menu[2] = array( __('Dashboard'), 'read', 'index.php', '', 'menu-top menu-top-first menu-icon-dashboard', 'menu-dashboard', 'div' );
}

if ( is_multisite() || is_super_admin() ) {
	$submenu[ 'index.php' ][0] = array( __('Dashboard'), 'read', 'index.php' );

	if ( is_multisite() )
		$submenu[ 'index.php' ][5] = array( __('My Sites'), 'read', 'my-sites.php' );

	if ( is_super_admin() ) {
		$plugin_update_count = $theme_update_count = $wordpress_update_count = 0;
		$update_plugins = get_site_transient( 'update_plugins' );
		if ( !empty($update_plugins->response) )
			$plugin_update_count = count( $update_plugins->response );
		$update_themes = get_site_transient( 'update_themes' );
		if ( !empty($update_themes->response) )
			$theme_update_count = count( $update_themes->response );
		$update_wordpress = get_core_updates( array('dismissed' => false) );
		if ( !empty($update_wordpress) && !in_array( $update_wordpress[0]->response, array('development', 'latest') ) )
			$wordpress_update_count = 1;

		$update_count = $plugin_update_count + $theme_update_count + $wordpress_update_count;
		$update_title = array();
		if ( $wordpress_update_count )
			$update_title[] = sprintf(__('%d WordPress Update'), $wordpress_update_count);
		if ( $plugin_update_count )
			$update_title[] = sprintf(_n('%d Plugin Update', '%d Plugin Updates', $plugin_update_count), $plugin_update_count);
		if ( $theme_update_count )
			$update_title[] = sprintf(_n('%d Theme Update', '%d Themes Updates', $theme_update_count), $theme_update_count);

		$update_title = !empty($update_title) ? esc_attr(implode(', ', $update_title)) : '';

		$submenu[ 'index.php' ][10] = array( sprintf( __('Updates %s'), "<span class='update-plugins count-$update_count' title='$update_title'><span class='update-count'>" . number_format_i18n($update_count) . "</span></span>" ), 'install_plugins',  'update-core.php');
		unset($plugin_update_count, $theme_update_count, $wordpress_update_count, $update_count, $update_title);
	}
}

$menu[4] = array( '', 'read', 'separator1', '', 'wp-menu-separator' );

$menu[5] = array( __('Posts'), 'edit_posts', 'edit.php', '', 'open-if-no-js menu-top menu-icon-post', 'menu-posts', 'div' );
	$submenu['edit.php'][5]  = array( __('Edit'), 'edit_posts', 'edit.php' );
	/* translators: add new post */
	$submenu['edit.php'][10]  = array( _x('Add New', 'post'), 'edit_posts', 'post-new.php' );

	$i = 15;
	foreach ( $wp_taxonomies as $tax ) {
		if ( ! $tax->show_ui || ! in_array('post', (array) $tax->object_type, true) )
			continue;

		$submenu['edit.php'][$i++] = array( esc_attr($tax->label), $tax->manage_cap, 'edit-tags.php?taxonomy=' . $tax->name );
	}
	unset($tax);

$menu[10] = array( __('Media'), 'upload_files', 'upload.php', '', 'menu-top menu-icon-media', 'menu-media', 'div' );
	$submenu['upload.php'][5] = array( __('Library'), 'upload_files', 'upload.php');
	/* translators: add new file */
	$submenu['upload.php'][10] = array( _x('Add New', 'file'), 'upload_files', 'media-new.php');

$menu[15] = array( __('Links'), 'manage_links', 'link-manager.php', '', 'menu-top menu-icon-links', 'menu-links', 'div' );
	$submenu['link-manager.php'][5] = array( __('Edit'), 'manage_links', 'link-manager.php' );
	/* translators: add new links */
	$submenu['link-manager.php'][10] = array( _x('Add New', 'link'), 'manage_links', 'link-add.php' );
	$submenu['link-manager.php'][15] = array( __('Link Categories'), 'manage_categories', 'edit-link-categories.php' );

$menu[20] = array( __('Pages'), 'edit_pages', 'edit.php?post_type=page', '', 'menu-top menu-icon-page', 'menu-pages', 'div' );
	$submenu['edit.php?post_type=page'][5] = array( __('Edit'), 'edit_pages', 'edit.php?post_type=page' );
	/* translators: add new page */
	$submenu['edit.php?post_type=page'][10] = array( _x('Add New', 'page'), 'edit_pages', 'post-new.php?post_type=page' );

$menu[25] = array( sprintf( __('Comments %s'), "<span id='awaiting-mod' class='count-$awaiting_mod'><span class='pending-count'>" . number_format_i18n($awaiting_mod) . "</span></span>" ), 'edit_posts', 'edit-comments.php', '', 'menu-top menu-icon-comments', 'menu-comments', 'div' );

$_wp_last_object_menu = 25; // The index of the last top-level menu in the object menu group

foreach ( (array) get_post_types( array('show_ui' => true) ) as $ptype ) {
	$ptype_obj = get_post_type_object( $ptype );
	$ptype_menu_position = is_int( $ptype_obj->menu_position ) ? $ptype_obj->menu_position : $_wp_last_object_menu++; // If we're to use $_wp_last_object_menu, increment it first.
	if ( is_string( $ptype_obj->menu_icon ) ) {
		$menu_icon   = esc_url( $ptype_obj->menu_icon );
		$ptype_class = sanitize_html_class( $ptype );
	} else {
		$menu_icon   = 'div';
		$ptype_class = 'post';
	}

	// if $ptype_menu_position is already populated or will be populated by a hard-coded value below, increment the position.
	$core_menu_positions = array(59, 60, 65, 70, 75, 80, 85, 99);
	while ( isset($menu[$ptype_menu_position]) || in_array($ptype_menu_position, $core_menu_positions) )
		$ptype_menu_position++;

	$menu[$ptype_menu_position] = array( esc_attr( $ptype_obj->label ), $ptype_obj->edit_type_cap, "edit.php?post_type=$ptype", '', 'menu-top menu-icon-' . $ptype_class, 'menu-' . $ptype_class, $menu_icon );
	$submenu["edit.php?post_type=$ptype"][5]  = array( __('Edit'), $ptype_obj->edit_type_cap,  "edit.php?post_type=$ptype");
	/* translators: add new custom post type */
	$submenu["edit.php?post_type=$ptype"][10]  = array( _x('Add New', 'post'), $ptype_obj->edit_type_cap, "post-new.php?post_type=$ptype" );

	$i = 15;
	foreach ( $wp_taxonomies as $tax ) {
		if ( ! $tax->show_ui || ! in_array($ptype, (array) $tax->object_type, true) )
			continue;

		$submenu["edit.php?post_type=$ptype"][$i++] = array( esc_attr($tax->label), $tax->manage_cap, "edit-tags.php?taxonomy=$tax->name&amp;post_type=$ptype" );
	}
}
unset($ptype, $ptype_obj);

$menu[59] = array( '', 'read', 'separator2', '', 'wp-menu-separator' );

$menu[60] = array( __('Appearance'), 'switch_themes', 'themes.php', '', 'menu-top menu-icon-appearance', 'menu-appearance', 'div' );
	$submenu['themes.php'][5]  = array(__('Themes'), 'switch_themes', 'themes.php');
	$submenu['themes.php'][10] = array(__('Menus'), 'switch_themes', 'nav-menus.php');

// Add 'Editor' to the bottom of the Appearence menu.
add_action('admin_menu', '_add_themes_utility_last', 101);
function _add_themes_utility_last() {
	// Must use API on the admin_menu hook, direct modification is only possible on/before the _admin_menu hook
	add_submenu_page('themes.php', _x('Editor', 'theme editor'), _x('Editor', 'theme editor'), 'edit_themes', 'theme-editor.php');
}

$update_plugins = get_site_transient( 'update_plugins' );
$update_count = 0;
if ( !empty($update_plugins->response) )
	$update_count = count( $update_plugins->response );

$menu_perms = get_site_option('menu_items', array());
if ( is_super_admin() || ( is_multisite() && isset($menu_perms['plugins']) && $menu_perms['plugins'] ) ) {
	$menu[65] = array( sprintf( __('Plugins %s'), "<span class='update-plugins count-$update_count'><span class='plugin-count'>" . number_format_i18n($update_count) . "</span></span>" ), 'activate_plugins', 'plugins.php', '', 'menu-top menu-icon-plugins', 'menu-plugins', 'div' );
		$submenu['plugins.php'][5]  = array( __('Installed'), 'activate_plugins', 'plugins.php' );
		/* translators: add new plugin */
		$submenu['plugins.php'][10] = array(_x('Add New', 'plugin'), 'install_plugins', 'plugin-install.php');
		$submenu['plugins.php'][15] = array( _x('Editor', 'plugin editor'), 'edit_plugins', 'plugin-editor.php' );
}
unset($menu_perms, $update_plugins, $update_count);

if ( current_user_can('edit_users') )
	$menu[70] = array( __('Users'), 'edit_users', 'users.php', '', 'menu-top menu-icon-users', 'menu-users', 'div' );
else
	$menu[70] = array( __('Profile'), 'read', 'profile.php', '', 'menu-top menu-icon-users', 'menu-users', 'div' );

if ( current_user_can('edit_users') ) {
	$_wp_real_parent_file['profile.php'] = 'users.php'; // Back-compat for plugins adding submenus to profile.php.
	$submenu['users.php'][5] = array(__('Authors &amp; Users'), 'edit_users', 'users.php');
	$submenu['users.php'][10] = array(_x('Add New', 'user'), 'create_users', 'user-new.php');

	$submenu['users.php'][15] = array(__('Your Profile'), 'read', 'profile.php');
} else {
	$_wp_real_parent_file['users.php'] = 'profile.php';
	$submenu['profile.php'][5] = array(__('Your Profile'), 'read', 'profile.php');
}

$menu[75] = array( __('Tools'), 'edit_posts', 'tools.php', '', 'menu-top menu-icon-tools', 'menu-tools', 'div' );
	$submenu['tools.php'][5] = array( __('Tools'), 'edit_posts', 'tools.php' );
	$submenu['tools.php'][10] = array( __('Import'), 'import', 'import.php' );
	$submenu['tools.php'][15] = array( __('Export'), 'import', 'export.php' );
	if ( is_multisite() && !is_main_site() )
		$submenu['tools.php'][25] = array( __('Delete Site'), 'manage_options', 'ms-delete-site.php' );
	if ( ( ! is_multisite() || defined( 'MULTISITE' ) ) && defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE && is_super_admin() )
		$submenu['tools.php'][50] = array(__('Network'), 'manage_options', 'network.php');

$menu[80] = array( __('Settings'), 'manage_options', 'options-general.php', '', 'menu-top menu-icon-settings', 'menu-settings', 'div' );
	$submenu['options-general.php'][10] = array(_x('General', 'settings screen'), 'manage_options', 'options-general.php');
	$submenu['options-general.php'][15] = array(__('Writing'), 'manage_options', 'options-writing.php');
	$submenu['options-general.php'][20] = array(__('Reading'), 'manage_options', 'options-reading.php');
	$submenu['options-general.php'][25] = array(__('Discussion'), 'manage_options', 'options-discussion.php');
	$submenu['options-general.php'][30] = array(__('Media'), 'manage_options', 'options-media.php');
	$submenu['options-general.php'][35] = array(__('Privacy'), 'manage_options', 'options-privacy.php');
	$submenu['options-general.php'][40] = array(__('Permalinks'), 'manage_options', 'options-permalink.php');

$_wp_last_utility_menu = 80; // The index of the last top-level menu in the utility menu group

$menu[99] = array( '', 'read', 'separator-last', '', 'wp-menu-separator-last' );

// Back-compat for old top-levels
$_wp_real_parent_file['post.php'] = 'edit.php';
$_wp_real_parent_file['post-new.php'] = 'edit.php';
$_wp_real_parent_file['edit-pages.php'] = 'edit.php?post_type=page';
$_wp_real_parent_file['page-new.php'] = 'edit.php?post_type=page';
$_wp_real_parent_file['wpmu-admin.php'] = 'ms-admin.php';

do_action('_admin_menu');

// Create list of page plugin hook names.
foreach ($menu as $menu_page) {
	if ( false !== $pos = strpos($menu_page[2], '?') ) {
		// Handle post_type=post|page|foo pages.
		$hook_name = substr($menu_page[2], 0, $pos);
		$hook_args = substr($menu_page[2], $pos + 1);
		wp_parse_str($hook_args, $hook_args);
		// Set the hook name to be the post type.
		if ( isset($hook_args['post_type']) )
			$hook_name = $hook_args['post_type'];
		else
			$hook_name = basename($hook_name, '.php');
		unset($hook_args);
	} else {
		$hook_name = basename($menu_page[2], '.php');
	}
	$hook_name = sanitize_title($hook_name);

	// ensure we're backwards compatible
	$compat = array(
		'index' => 'dashboard',
		'edit' => 'posts',
		'post' => 'posts',
		'upload' => 'media',
		'link-manager' => 'links',
		'edit-pages' => 'pages',
		'page' => 'pages',
		'edit-comments' => 'comments',
		'options-general' => 'settings',
		'themes' => 'appearance',
		);

	if ( isset($compat[$hook_name]) )
		$hook_name = $compat[$hook_name];
	elseif ( !$hook_name )
		continue;

	$admin_page_hooks[$menu_page[2]] = $hook_name;
}
unset($menu_page);

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
		unset($index, $data);

		if ( empty(${$sub_loop}[$parent]) )
			unset(${$sub_loop}[$parent]);
	}
	unset($sub, $parent);
}
unset($sub_loop);

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
		unset($submenu[$old_parent], $index);

		if ( isset($_wp_submenu_nopriv[$old_parent]) )
			$_wp_submenu_nopriv[$new_parent] = $_wp_submenu_nopriv[$old_parent];
	}
}
unset($id, $data, $subs, $first_sub, $old_parent, $new_parent);

do_action('admin_menu', '');

// Remove menus that have no accessible submenus and require privs that the user does not have.
// Run re-parent loop again.
foreach ( $menu as $id => $data ) {
	if ( ! current_user_can($data[1]) )
		$_wp_menu_nopriv[$data[2]] = true;

	// If submenu is empty...
	if ( empty($submenu[$data[2]]) ) {
		// And user doesn't have privs, remove menu.
		if ( isset( $_wp_menu_nopriv[$data[2]] ) ) {
			unset($menu[$id]);
		}
	}
}
unset($id, $data);

// Remove any duplicated seperators
$seperator_found = false;
foreach ( $menu as $id => $data ) {
	if ( 0 == strcmp('wp-menu-separator', $data[4] ) ) {
		if (false == $seperator_found) {
			$seperator_found = true;
		} else {
			unset($menu[$id]);
			$seperator_found = false;
		}
	} else {
		$seperator_found = false;
	}
}
unset($id, $data);

function add_cssclass($add, $class) {
	$class = empty($class) ? $add : $class .= ' ' . $add;
	return $class;
}

function add_menu_classes($menu) {

	$first = $lastorder = false;
	$i = 0;
	$mc = count($menu);
	foreach ( $menu as $order => $top ) {
		$i++;

		if ( 0 == $order ) { // dashboard is always shown/single
			$menu[0][4] = add_cssclass('menu-top-first', $top[4]);
			$lastorder = 0;
			continue;
		}

		if ( 0 === strpos($top[2], 'separator') ) { // if separator
			$first = true;
			$c = $menu[$lastorder][4];
			$menu[$lastorder][4] = add_cssclass('menu-top-last', $c);
			continue;
		}

		if ( $first ) {
			$c = $menu[$order][4];
			$menu[$order][4] = add_cssclass('menu-top-first', $c);
			$first = false;
		}

		if ( $mc == $i ) { // last item
			$c = $menu[$order][4];
			$menu[$order][4] = add_cssclass('menu-top-last', $c);
		}

		$lastorder = $order;
	}

	return apply_filters( 'add_menu_classes', $menu );
}

uksort($menu, "strnatcasecmp"); // make it all pretty

if ( apply_filters('custom_menu_order', false) ) {
	$menu_order = array();
	foreach ( $menu as $menu_item ) {
		$menu_order[] = $menu_item[2];
	}
	unset($menu_item);
	$default_menu_order = $menu_order;
	$menu_order = apply_filters('menu_order', $menu_order);
	$menu_order = array_flip($menu_order);
	$default_menu_order = array_flip($default_menu_order);

	function sort_menu($a, $b) {
		global $menu_order, $default_menu_order;
		$a = $a[2];
		$b = $b[2];
		if ( isset($menu_order[$a]) && !isset($menu_order[$b]) ) {
			return -1;
		} elseif ( !isset($menu_order[$a]) && isset($menu_order[$b]) ) {
			return 1;
		} elseif ( isset($menu_order[$a]) && isset($menu_order[$b]) ) {
			if ( $menu_order[$a] == $menu_order[$b] )
				return 0;
			return ($menu_order[$a] < $menu_order[$b]) ? -1 : 1;
		} else {
			return ($default_menu_order[$a] <= $default_menu_order[$b]) ? -1 : 1;
		}
	}

	usort($menu, 'sort_menu');
	unset($menu_order, $default_menu_order);
}

$menu = add_menu_classes($menu);

if ( !user_can_access_admin_page() ) {
	do_action('admin_page_access_denied');
	wp_die( __('You do not have sufficient permissions to access this page.') );
}

?>
