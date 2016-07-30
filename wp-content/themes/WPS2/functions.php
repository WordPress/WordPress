<?php

// Remove any pre-existing shortcodes
remove_all_shortcodes();


// Required in order to inherit parent theme style.css
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

function my_theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

	wp_register_script('browse-media', get_stylesheet_directory() . '/js/libs/browse-media.js');
	wp_enqueue_media();
	wp_enqueue_script( 'browse-media' );
	
	
}

// Create CA Options Page
include(get_stylesheet_directory(). '/options.php');
add_action( 'admin_menu', 'menu_setup' );
function menu_setup(){
	
	add_theme_page( 'CA Option', 'CA Options', 'manage_options', 'ca_options', 'menu_option_setup');
	add_action( 'admin_init', 'ca_register_settings' );

	}

function ca_setup_theme(){
	
}
add_action( 'after_setup_theme', 'ca_setup_theme' );


/*
   This function gets called during Prep_CA_Custom_Modules
   and is attached to the appropriate action hook. 
   Includes the custom modules once Divi Parent Theme ET_Builder_Module exists
*/
function CA_Custom_Modules(){
	if(class_exists("ET_Builder_Module")){
		include(get_stylesheet_directory() . '/includes/builder/main-modules.php');
 	}

}

function Prep_CA_Custom_Modules(){
 	global $pagenow;
	$is_admin = is_admin();
 	$action_hook = $is_admin ? 'wp_loaded' : 'wp';

 	$required_admin_pages = array( 'edit.php', 'post.php', 'post-new.php', 'admin.php', 'customize.php', 'edit-tags.php', 'admin-ajax.php', 'export.php' ); // list of admin pages where we need to load builder files

 $specific_filter_pages = array( 'edit.php', 'admin.php', 'edit-tags.php' );

 $is_edit_library_page = 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'et_pb_layout' === $_GET['post_type'];

 $is_role_editor_page = 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'et_divi_role_editor' === $_GET['page'];

 $is_import_page = 'admin.php' === $pagenow && isset( $_GET['import'] ) && 'wordpress' === $_GET['import']; 

 $is_edit_layout_category_page = 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && 'layout_category' === $_GET['taxonomy'];

if ( ! $is_admin || ( $is_admin && in_array( $pagenow, $required_admin_pages ) && ( ! in_array( $pagenow, $specific_filter_pages ) || $is_edit_library_page || $is_role_editor_page || $is_edit_layout_category_page || $is_import_page ) ) ) {

 add_action($action_hook, 'CA_Custom_Modules', 9789);

 }

}

Prep_CA_Custom_Modules();

/**
* Returns all child nav_menu_items under a specific parent


* Source http://wpsmith.net/2011/how-to-get-all-the-children-of-a-specific-nav-menu-item/

* @param int the parent nav_menu_item ID
* @param array nav_menu_items
* @param bool gives all children or direct children only
* @return array returns filtered array of nav_menu_items
*/

function get_nav_menu_item_children( $parent_id, $nav_menu_items, $depth = true ) {

	$nav_menu_item_list = array();

	foreach ( (array) $nav_menu_items as $nav_menu_item ) {

		if ( $nav_menu_item->menu_item_parent == $parent_id ) {

			$nav_menu_item_list[] = $nav_menu_item;

			if ( $depth ) {

			if ( $children = get_nav_menu_item_children( $nav_menu_item->ID, $nav_menu_items ) )


				$nav_menu_item_list = array_merge( $nav_menu_item_list, $children );

			}

		}

	}


	return $nav_menu_item_list;

}































?>