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

$menu[2] = array( __('Dashboard'), 'read', 'index.php', '', 'menu-top menu-top-first menu-icon-dashboard', 'menu-dashboard', 'div' );

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
	$submenu['edit.php'][5]  = array( __('Posts'), 'edit_posts', 'edit.php' );
	/* translators: add new post */
	$submenu['edit.php'][10]  = array( _x('Add New', 'post'), 'edit_posts', 'post-new.php' );

	$i = 15;
	foreach ( $wp_taxonomies as $tax ) {
		if ( ! $tax->show_ui || ! in_array('post', (array) $tax->object_type, true) )
			continue;

		$submenu['edit.php'][$i++] = array( esc_attr( $tax->labels->name ), $tax->cap->manage_terms, 'edit-tags.php?taxonomy=' . $tax->name );
	}
	unset($tax);

$menu[10] = array( __('Media'), 'upload_files', 'upload.php', '', 'menu-top menu-icon-media', 'menu-media', 'div' );
	$submenu['upload.php'][5] = array( __('Library'), 'upload_files', 'upload.php');
	/* translators: add new file */
	$submenu['upload.php'][10] = array( _x('Add New', 'file'), 'upload_files', 'media-new.php');

$menu[15] = array( __('Links'), 'manage_links', 'link-manager.php', '', 'menu-top menu-icon-links', 'menu-links', 'div' );
	$submenu['link-manager.php'][5] = array( __('Links'), 'manage_links', 'link-manager.php' );
	/* translators: add new links */
	$submenu['link-manager.php'][10] = array( _x('Add New', 'link'), 'manage_links', 'link-add.php' );
	$submenu['link-manager.php'][15] = array( __('Link Categories'), 'manage_categories', 'edit-tags.php?taxonomy=link_category' );

$menu[20] = array( __('Pages'), 'edit_pages', 'edit.php?post_type=page', '', 'menu-top menu-icon-page', 'menu-pages', 'div' );
	$submenu['edit.php?post_type=page'][5] = array( __('Pages'), 'edit_pages', 'edit.php?post_type=page' );
	/* translators: add new page */
	$submenu['edit.php?post_type=page'][10] = array( _x('Add New', 'page'), 'edit_pages', 'post-new.php?post_type=page' );
	$i = 15;
	foreach ( $wp_taxonomies as $tax ) {
		if ( ! $tax->show_ui || ! in_array('page', (array) $tax->object_type, true) )
			continue;

		$submenu['edit.php?post_type=page'][$i++] = array( esc_attr( $tax->labels->name ), $tax->cap->manage_terms, 'edit-tags.php?taxonomy=' . $tax->name . '&post_type=page' );
	}
	unset($tax);

$menu[25] = array( sprintf( __('Comments %s'), "<span id='awaiting-mod' class='count-$awaiting_mod'><span class='pending-count'>" . number_format_i18n($awaiting_mod) . "</span></span>" ), 'edit_posts', 'edit-comments.php', '', 'menu-top menu-icon-comments', 'menu-comments', 'div' );

$_wp_last_object_menu = 25; // The index of the last top-level menu in the object menu group

foreach ( (array) get_post_types( array('show_ui' => true, '_builtin' => false) ) as $ptype ) {
	$ptype_obj = get_post_type_object( $ptype );
	$ptype_menu_position = is_int( $ptype_obj->menu_position ) ? $ptype_obj->menu_position : ++$_wp_last_object_menu; // If we're to use $_wp_last_object_menu, increment it first.
	$ptype_for_id = sanitize_html_class( $ptype );
	if ( is_string( $ptype_obj->menu_icon ) ) {
		$menu_icon   = esc_url( $ptype_obj->menu_icon );
		$ptype_class = $ptype_for_id;
	} else {
		$menu_icon   = 'div';
		$ptype_class = 'post';
	}

	// if $ptype_menu_position is already populated or will be populated by a hard-coded value below, increment the position.
	$core_menu_positions = array(59, 60, 65, 70, 75, 80, 85, 99);
	while ( isset($menu[$ptype_menu_position]) || in_array($ptype_menu_position, $core_menu_positions) )
		$ptype_menu_position++;

	$menu[$ptype_menu_position] = array( esc_attr( $ptype_obj->labels->name ), $ptype_obj->cap->edit_posts, "edit.php?post_type=$ptype", '', 'menu-top menu-icon-' . $ptype_class, 'menu-posts-' . $ptype_for_id, $menu_icon );
	$submenu["edit.php?post_type=$ptype"][5]  = array( $ptype_obj->labels->name, $ptype_obj->cap->edit_posts,  "edit.php?post_type=$ptype");
	$submenu["edit.php?post_type=$ptype"][10]  = array( $ptype_obj->labels->add_new, $ptype_obj->cap->edit_posts, "post-new.php?post_type=$ptype" );

	$i = 15;
	foreach ( $wp_taxonomies as $tax ) {
		if ( ! $tax->show_ui || ! in_array($ptype, (array) $tax->object_type, true) )
			continue;

		$submenu["edit.php?post_type=$ptype"][$i++] = array( esc_attr( $tax->labels->name ), $tax->cap->manage_terms, "edit-tags.php?taxonomy=$tax->name&amp;post_type=$ptype" );
	}
}
unset($ptype, $ptype_obj);

$menu[59] = array( '', 'read', 'separator2', '', 'wp-menu-separator' );

if ( current_user_can( 'switch_themes') ) {
	$menu[60] = array( __('Appearance'), 'switch_themes', 'themes.php', '', 'menu-top menu-icon-appearance', 'menu-appearance', 'div' );
		$submenu['themes.php'][5]  = array(__('Themes'), 'switch_themes', 'themes.php');
		if ( current_theme_supports( 'menus' ) || current_theme_supports( 'widgets' ) )
			$submenu['themes.php'][10] = array(__('Menus'), 'edit_theme_options', 'nav-menus.php');
} else {
	$menu[60] = array( __('Appearance'), 'edit_theme_options', 'themes.php', '', 'menu-top menu-icon-appearance', 'menu-appearance', 'div' );
		$submenu['themes.php'][5]  = array(__('Themes'), 'edit_theme_options', 'themes.php');
		if ( current_theme_supports( 'menus' ) || current_theme_supports( 'widgets' ) )
			$submenu['themes.php'][10] = array(__('Menus'), 'edit_theme_options', 'nav-menus.php' );
}

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
		$submenu['plugins.php'][5]  = array( __('Plugins'), 'activate_plugins', 'plugins.php' );
		/* translators: add new plugin */
		$submenu['plugins.php'][10] = array(_x('Add New', 'plugin'), 'install_plugins', 'plugin-install.php');
		$submenu['plugins.php'][15] = array( _x('Editor', 'plugin editor'), 'edit_plugins', 'plugin-editor.php' );
}
unset($menu_perms, $update_plugins, $update_count);

if ( current_user_can('list_users') )
	$menu[70] = array( __('Users'), 'list_users', 'users.php', '', 'menu-top menu-icon-users', 'menu-users', 'div' );
else
	$menu[70] = array( __('Profile'), 'read', 'profile.php', '', 'menu-top menu-icon-users', 'menu-users', 'div' );

if ( current_user_can('list_users') ) {
	$_wp_real_parent_file['profile.php'] = 'users.php'; // Back-compat for plugins adding submenus to profile.php.
	$submenu['users.php'][5] = array(__('Users'), 'list_users', 'users.php');
	$submenu['users.php'][10] = array(_x('Add New', 'user'), 'create_users', 'user-new.php');

	$submenu['users.php'][15] = array(__('Your Profile'), 'read', 'profile.php');
} else {
	$_wp_real_parent_file['users.php'] = 'profile.php';
	$submenu['profile.php'][5] = array(__('Your Profile'), 'read', 'profile.php');
	$submenu['profile.php'][10] = array(__('Add New User'), 'create_users', 'user-new.php');
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

require(ABSPATH . 'wp-admin/includes/menu.php');

?>
