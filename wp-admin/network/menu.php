<?php

/* translators: Network menu item */
$menu[0] = array(__('Dashboard'), 'manage_network', 'index.php', '', 'menu-top menu-top-first menu-icon-site', 'menu-site', 'div');

$menu[4] = array( '', 'read', 'separator1', '', 'wp-menu-separator' );

/* translators: Sites menu item */
$menu[5] = array(__('Sites'), 'manage_sites', 'sites.php', '', 'menu-top menu-icon-site', 'menu-site', 'div');
$menu[10] = array(__('Users'), 'manage_network_users', 'users.php', '', 'menu-top menu-icon-users', 'menu-users', 'div');
$menu[15] = array(__('Themes'), 'manage_network_themes', 'themes.php', '', 'menu-top menu-icon-appearance', 'menu-appearance', 'div');
$menu[20] = array(__('Plugins'), 'manage_network_plugins', 'plugins.php', '', 'menu-top menu-icon-plugins', 'menu-plugins', 'div');
$menu[25] = array(__('Settings'), 'manage_network_options', 'settings.php', '', 'menu-top menu-icon-settings', 'menu-settings', 'div');
$menu[30] = array(__('Update'), 'manage_network', 'upgrade.php', '', 'menu-top menu-icon-tools', 'menu-update', 'div');

$menu[99] = array( '', 'read', 'separator-last', '', 'wp-menu-separator-last' );

$compat = array();
$submenu = array();

$submenu['plugins.php'][5]  = array( __('Plugins'), 'manage_network_plugins', 'plugins.php' ); 
$submenu['plugins.php'][15] = array( _x('Editor', 'plugin editor'), 'edit_plugins', 'plugin-editor.php' );

$submenu['themes.php'][5]  = array( __('Themes'), 'manage_network_themes', 'themes.php' ); 
$submenu['themes.php'][15] = array( _x('Editor', 'plugin editor'), 'edit_themes', 'theme-editor.php' );

require_once(ABSPATH . 'wp-admin/includes/menu.php');

?>