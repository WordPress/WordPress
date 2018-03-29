<?php

class UM_Admin_Columns {

	function __construct() {

		$this->slug = 'ultimatemember';
		
		add_filter('manage_edit-um_form_columns', array(&$this, 'manage_edit_um_form_columns') );
		add_action('manage_um_form_posts_custom_column', array(&$this, 'manage_um_form_posts_custom_column'), 10, 3);

		add_filter('manage_edit-um_directory_columns', array(&$this, 'manage_edit_um_directory_columns') );
		add_action('manage_um_directory_posts_custom_column', array(&$this, 'manage_um_directory_posts_custom_column'), 10, 3);
		
		add_filter('post_row_actions', array(&$this, 'post_row_actions'), 99, 2);
		
	}
	
	/***
	***	@custom row actions
	***/
	function post_row_actions($actions, $post){
		//check for your post type
		if ($post->post_type =="um_form"){
			$actions['um_duplicate'] = '<a href="' . $this->duplicate_uri( $post->ID ) . '">' . __('Duplicate','ultimate-member') . '</a>';
		}
		return $actions;
	}

	/***
	***	@duplicate a form
	***/
	function duplicate_uri( $id ) {
		$url = add_query_arg('um_adm_action', 'duplicate_form', admin_url('edit.php?post_type=um_form') );
		$url = add_query_arg('post_id', $id, $url);
		return $url;
	}
	
	/***
	***	@Custom columns for Form
	***/
	function manage_edit_um_form_columns($columns) {
	
		$admin = new UM_Admin_Metabox();
		
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['id'] = __('ID') . $admin->_tooltip( 'Unique ID for each form' );
		$new_columns['title'] = __('Title');
		$new_columns['mode'] = __('Type') . $admin->_tooltip( 'This is the type of the form' );
		$new_columns['shortcode'] = __('Shortcode') . $admin->_tooltip( 'Use this shortcode to display the form' );
		$new_columns['date'] = __('Date');
		
		return $new_columns;
		
	}
	
	/***
	***	@Custom columns for Directory
	***/
	function manage_edit_um_directory_columns($columns) {
		
		$admin = new UM_Admin_Metabox();
		
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['id'] = __('ID') . $admin->_tooltip( 'Unique ID for each form' );
		$new_columns['title'] = __('Title');
		$new_columns['shortcode'] = __('Shortcode') . $admin->_tooltip( 'Use this shortcode to display the member directory' );
		$new_columns['date'] = __('Date');
		
		return $new_columns;
		
	}

	/***
	***	@Display cusom columns for Form
	***/
	function manage_um_form_posts_custom_column($column_name, $id) {
		global $wpdb, $ultimatemember;
		
		switch ($column_name) {
		
			case 'id':
				echo '<span class="um-admin-number">'.$id.'</span>';
				break;
				
			case 'shortcode':
				echo $ultimatemember->shortcodes->get_shortcode( $id );
				break;
				
			case 'mode':
				$mode = $ultimatemember->query->get_attr('mode', $id);
				echo '<span class="um-admin-tag um-admin-type-'.$mode.'">'. $ultimatemember->form->display_form_type($mode, $id) . '</span>';
				break;
				
		}
		
	}

	/***
	***	@Display cusom columns for Directory
	***/
	function manage_um_directory_posts_custom_column($column_name, $id) {
		global $wpdb, $ultimatemember;
		
		switch ($column_name) {
		
			case 'id':
				echo '<span class="um-admin-number">'.$id.'</span>';
				break;
				
			case 'shortcode':
				echo $ultimatemember->shortcodes->get_shortcode( $id );
				break;
				
		}
		
	}
	
}