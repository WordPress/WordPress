<?php

class UM_Admin_Functions {

	function __construct() {

		$this->slug = 'ultimatemember';

		add_action('parent_file', array(&$this, 'parent_file'), 9);

		add_filter('gettext', array(&$this, 'gettext'), 10, 4);
		
		add_filter('post_updated_messages', array(&$this, 'post_updated_messages') );
		
	}

	/***
	***	@updated post messages
	***/
	function post_updated_messages($messages) {
		global $post, $post_ID;
		
		$post_type = get_post_type( $post_ID );
		if ($post_type == 'um_form') {

			$messages['um_form'] = array(
			0 => '',
			1 => __('Form updated.'),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('Form updated.'),
			5 => isset($_GET['revision']) ? __('Form restored to revision.') : false,
			6 => __('Form created.'),
			7 => __('Form saved.'),
			8 => __('Form submitted.'),
			9 => __('Form scheduled.'),
			10=> __('Form draft updated.'),
			);
			
		}
		
		if ($post_type == 'um_role') {

			$messages['um_role'] = array(
			0 => '',
			1 => __('Role updated.'),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('Role updated.'),
			5 => isset($_GET['revision']) ? __('Role restored to revision.') : false,
			6 => __('Role created.'),
			7 => __('Role saved.'),
			8 => __('Role submitted.'),
			9 => __('Role scheduled.'),
			10=> __('Role draft updated.'),
			);
			
		}
		
		return $messages;
	}
	
	/***
	***	@check that we're on a custom post type supported by UM
	***/
	function is_plugin_post_type(){
		if (isset($_REQUEST['post_type'])){
			$post_type = $_REQUEST['post_type'];
			if ( in_array($post_type, array('um_form','um_role','um_directory'))){
				return true;
			}
		} else if ( isset($_REQUEST['action'] ) && $_REQUEST['action'] == 'edit') {
			$post_type = get_post_type();
			if ( in_array($post_type, array('um_form','um_role','um_directory'))){
				return true;
			}
		}
		return false;
	}
	
	/***
	***	@gettext filters
	***/
	function gettext($translation, $text, $domain) {
		global $post;
		$screen =  get_current_screen();
		if (isset($post->post_type) && $this->is_plugin_post_type() ) {
			$translations = get_translations_for_domain( $domain);
			if ( $text == 'Publish') {
				return $translations->translate( 'Create' );
			}
			if ( $text == 'Move to Trash') {
				return $translations->translate( 'Delete' );
			}
		}

		return $translation;
	}
	
	/***
	***	@Fix parent file for correct highlighting
	***/
	function parent_file($parent_file){
		global $current_screen;
		$screen_id = $current_screen->id;
		if ( strstr($screen_id, 'um_') ) {
			$parent_file = $this->slug;
		}
		return $parent_file;
	}

}