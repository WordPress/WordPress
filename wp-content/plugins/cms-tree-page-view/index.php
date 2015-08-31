<?php
/*
Plugin Name: CMS Tree Page View
Plugin URI: http://eskapism.se/code-playground/cms-tree-page-view/
Description: Adds a CMS-like tree view of all your pages, like the view often found in a page-focused CMS. Use the tree view to edit, view, add pages and search pages (very useful if you have many pages). And with drag and drop you can rearrange the order of your pages. Page management won't get any easier than this!
Version: 1.2.32
Author: Pär Thernström
Author URI: http://eskapism.se/
License: GPL2
*/

/*  Copyright 2010  Pär Thernström (email: par.thernstrom@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

#require("functions.php");

define( "CMS_TPV_VERSION", "1.2.32");
define( "CMS_TPV_NAME", "CMS Tree Page View");

require(dirname(__FILE__) . "/functions.php");

// Find the plugin directory URL
$aa = __FILE__;
if ( isset( $mu_plugin ) ) {
	$aa = $mu_plugin;
}
if ( isset( $network_plugin ) ) {
	$aa = $network_plugin;
}
if ( isset( $plugin ) ) {
	$aa = $plugin;
}
$plugin_dir_url = plugin_dir_url(basename($aa)) . 'cms-tree-page-view/';

// There! Now we should have it.
define( "CMS_TPV_URL", $plugin_dir_url);
// define( "CMS_TPV_PLUGIN_FOLDERNAME_AND_FILENAME", basename(dirname(__FILE__)) . "/" . basename(__FILE__) );

add_action( 'init', 'cms_tpv_load_textdomain' );

// on admin init: add styles and scripts
add_action( 'admin_init', 'cms_tpv_admin_init' );
add_action( 'admin_enqueue_scripts', 'cms_admin_enqueue_scripts' );
add_action( 'admin_init', 'cms_tpv_save_settings' );

// Hook onto dashboard and admin menu
add_action( 'admin_menu', "cms_tpv_admin_menu" );
add_action( 'admin_head', "cms_tpv_admin_head" );
add_action( 'wp_dashboard_setup', "cms_tpv_wp_dashboard_setup" );

// Ajax hooks
add_action('wp_ajax_cms_tpv_get_childs', 'cms_tpv_get_childs');
add_action('wp_ajax_cms_tpv_move_page', 'cms_tpv_move_page');
add_action('wp_ajax_cms_tpv_add_page', 'cms_tpv_add_page');
add_action('wp_ajax_cms_tpv_add_pages', 'cms_tpv_add_pages');

// activation
define( "CMS_TPV_MOVE_PERMISSION", "move_cms_tree_view_page");
register_activation_hook( WP_PLUGIN_DIR . "/cms-tree-page-view/index.php" , 'cms_tpv_install' );
register_uninstall_hook( WP_PLUGIN_DIR . "/cms-tree-page-view/index.php" , 'cms_tpv_uninstall' );

// To test activation hook, uncomment function below
// cms_tpv_install();

// catch upgrade. moved from plugins_loaded to init to be able to use wp_roles
add_action('init', 'cms_tpv_plugins_loaded' , 1);

// hook onto query
#add_action( 'parse_query', 'cms_tpv_parse_query' );
