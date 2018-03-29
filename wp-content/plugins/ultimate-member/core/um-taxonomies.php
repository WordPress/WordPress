<?php

class UM_Taxonomies {

	function __construct() {
	
		add_action('init',  array(&$this, 'create_taxonomies'), 1);
	
	}
	
	/***
	***	@Create taxonomies for use for UM
	***/
	function create_taxonomies() {
	
		register_post_type( 'um_form', array(
				'labels' => array(
					'name' => __( 'Forms' ),
					'singular_name' => __( 'Form' ),
					'add_new' => __( 'Add New' ),
					'add_new_item' => __('Add New Form' ),
					'edit_item' => __('Edit Form'),
					'not_found' => __('You did not create any forms yet'),
					'not_found_in_trash' => __('Nothing found in Trash'),
					'search_items' => __('Search Forms')
				),
				'show_ui' => true,
				'show_in_menu' => false,
				'public' => false,
				'supports' => array('title')
			)
		);
		
		register_post_type( 'um_role', array(
				'labels' => array(
					'name' => __( 'User Roles' ),
					'singular_name' => __( 'User Role' ),
					'add_new' => __( 'Add New' ),
					'add_new_item' => __('Add New User Role' ),
					'edit_item' => __('Edit User Role'),
					'not_found' => __('You did not create any user roles yet'),
					'not_found_in_trash' => __('Nothing found in Trash'),
					'search_items' => __('Search User Roles')
				),
				'show_ui' => true,
				'show_in_menu' => false,
				'public' => false,
				'supports' => array('title')
			)
		);

		if ( um_get_option('members_page') || !get_option('um_options') ){
		
		register_post_type( 'um_directory', array(
				'labels' => array(
					'name' => __( 'Member Directories' ),
					'singular_name' => __( 'Member Directory' ),
					'add_new' => __( 'Add New' ),
					'add_new_item' => __('Add New Member Directory' ),
					'edit_item' => __('Edit Member Directory'),
					'not_found' => __('You did not create any member directories yet'),
					'not_found_in_trash' => __('Nothing found in Trash'),
					'search_items' => __('Search Member Directories')
				),
				'show_ui' => true,
				'show_in_menu' => false,
				'public' => false,
				'supports' => array('title')
			)
		);
		
		}
		
	}

}