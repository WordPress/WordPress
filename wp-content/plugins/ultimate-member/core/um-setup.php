<?php

class UM_Setup {

	function __construct() {

		add_action('init',  array(&$this, 'install_basics'), 9);

		add_action('init',  array(&$this, 'install_default_forms'), 9);

		add_action('init',  array(&$this, 'install_default_roles'), 9);

		add_action('init',  array(&$this, 'install_posts_roles'), 9);

		$this->core_forms = array(
			'register',
			'login',
			'profile',
		);

		$this->core_directories = array(
			'members',
		);

		$this->core_pages = array(
			'user' => array( 'title' => 'User' ),
			'login' => array( 'title' => 'Login' ),
			'register' => array( 'title' => 'Register' ),
			'members' => array( 'title' => 'Members' ),
			'logout' => array( 'title' => 'Logout' ),
			'account' => array( 'title' => 'Account' ),
			'password-reset' => array( 'title' => 'Password Reset'),
		);

		$this->core_directory_meta['members'] = array(
			'_um_template' => 'members',
			'_um_mode' => 'directory',
			'_um_has_profile_photo' => 0,
			'_um_has_cover_photo' => 0,
			'_um_show_social' => 0,
			'_um_show_userinfo' => 0,
			'_um_show_tagline' => 0,
			'_um_search' => 0,
			'_um_userinfo_animate' => '1',
			'_um_sortby' => 'user_registered_desc',
			'_um_profile_photo' => '1',
			'_um_cover_photos' => '1',
			'_um_show_name' => '1',
			'_um_directory_header' => __('{total_users} Members','ultimate-member'),
			'_um_directory_header_single' => __('{total_users} Member','ultimate-member'),
			'_um_directory_no_users' => __('We are sorry. We cannot find any users who match your search criteria.','ultimate-member'),
			'_um_profiles_per_page' => 12,
			'_um_profiles_per_page_mobile' => 6,
			'_um_core' => 'members',
		);

		$this->core_global_meta_all = array(
			'_um_primary_btn_color',
			'_um_primary_btn_hover',
			'_um_primary_btn_text',
			'_um_secondary_btn_color',
			'_um_secondary_btn_hover',
			'_um_secondary_btn_text',
			'_um_form_border',
			'_um_form_border_hover',
			'_um_form_bg_color',
			'_um_form_bg_color_focus',
			'_um_form_placeholder',
			'_um_form_icon_color',
			'_um_form_asterisk_color',
			'_um_form_field_label',
			'_um_form_text_color',
			'_um_active_color',
			'_um_help_tip_color',
			'_um_secondary_color',
		);

		$this->core_form_meta_all = array(
			'_um_help_tip_color' => '#ccc',
			'_um_active_color' => '#3ba1da',
			'_um_secondary_color' => '#44b0ec',
			'_um_form_text_color' => '#666',
			'_um_form_field_label' => '#555',
			'_um_form_icon_color' => '#aaa',
			'_um_form_asterisk_color' => '#aaa',
			'_um_form_bg_color' => '#fff',
			'_um_form_bg_color_focus' => '#fff',
			'_um_form_placeholder' => '#aaa',
			'_um_form_border' => '2px solid #ddd',
			'_um_form_border_hover' => '2px solid #bbb',
			'_um_primary_btn_color' => '#3ba1da',
			'_um_primary_btn_hover' => '#44b0ec',
			'_um_primary_btn_text' => '#fff',
			'_um_secondary_btn_color' => '#eee',
			'_um_secondary_btn_hover' => '#e5e5e5',
			'_um_secondary_btn_text' => '#666',
			'_um_profile_show_name' => 1,
			'_um_profile_show_social_links' => 0,
			'_um_profile_show_bio' => 1,
			'profile_show_html_bio' => 0,
			'_um_profile_bio_maxchars' => 180,
			'_um_profile_header_menu' => 'bc',
			'_um_profile_empty_text' => 1,
			'_um_profile_empty_text_emo' => 1,
			'_um_profile_role' => '0',
			'_um_profile_template' => 'profile',
			'_um_profile_max_width' => '1000px',
			'_um_profile_area_max_width' => '600px',
			'_um_profile_align' => 'center',
			'_um_profile_icons' => 'label',
			'_um_profile_cover_enabled' => 1,
			'_um_profile_cover_ratio' => '2.7:1',
			'_um_profile_photosize' => '190px',
			'_um_profile_photocorner' => '1',
			'_um_profile_header_bg' => '',
			'_um_profile_header_text' => '#999',
			'_um_profile_header_link_color' => '#555',
			'_um_profile_header_link_hcolor' => '#444',
			'_um_profile_header_icon_color' => '#aaa',
			'_um_profile_header_icon_hcolor' => '#3ba1da',
			'_um_profile_primary_btn_word' => 'Update Profile',
			'_um_profile_primary_btn_color' => '#3ba1da',
			'_um_profile_primary_btn_hover' => '#44b0ec',
			'_um_profile_primary_btn_text' => '#fff',
			'_um_profile_secondary_btn' => '1',
			'_um_profile_secondary_btn_word' => 'Cancel',
			'_um_profile_secondary_btn_color' => '#eee',
			'_um_profile_secondary_btn_hover' => '#e5e5e5',
			'_um_profile_secondary_btn_text' => '#666',
			'_um_profile_main_bg' => '',
			'_um_profile_main_text_color' => '#555555',
			'_um_register_role' => '0',
			'_um_register_template' => 'register',
			'_um_register_max_width' => '450px',
			'_um_register_align' => 'center',
			'_um_register_icons' => 'label',
			'_um_register_primary_btn_word' => __('Register','ultimate-member'),
			'_um_register_primary_btn_color' => '#3ba1da',
			'_um_register_primary_btn_hover' => '#44b0ec',
			'_um_register_primary_btn_text' => '#fff',
			'_um_register_secondary_btn' => 1,
			'_um_register_secondary_btn_word' => __('Login','ultimate-member'),
			'_um_register_secondary_btn_color' => '#eee',
			'_um_register_secondary_btn_hover' => '#e5e5e5',
			'_um_register_secondary_btn_text' => '#666',
			'_um_register_secondary_btn_url' => '',
			'_um_login_template' => 'login',
			'_um_login_max_width' => '450px',
			'_um_login_align' => 'center',
			'_um_login_icons' => 'label',
			'_um_login_primary_btn_word' => __('Login','ultimate-member'),
			'_um_login_primary_btn_color' => '#3ba1da',
			'_um_login_primary_btn_hover' => '#44b0ec',
			'_um_login_primary_btn_text' => '#fff',
			'_um_login_forgot_pass_link' => 1,
			'_um_login_show_rememberme' => 1,
			'_um_login_secondary_btn' => 1,
			'_um_login_secondary_btn_word' => __('Register','ultimate-member'),
			'_um_login_secondary_btn_color' => '#eee',
			'_um_login_secondary_btn_hover' => '#e5e5e5',
			'_um_login_secondary_btn_text' => '#666',
			'_um_login_secondary_btn_url' => '',
			'_um_directory_template' => 'members',
			'_um_directory_header' => __('{total_users} Members','ultimate-member'),
			'_um_directory_header_single' => __('{total_users} Member','ultimate-member'),
		);

		$this->core_form_meta_all = apply_filters('um_core_form_meta_all', $this->core_form_meta_all );

		$this->core_form_meta['register'] = array(
			'_um_custom_fields' => 'a:6:{s:10:"user_login";a:15:{s:5:"title";s:8:"Username";s:7:"metakey";s:10:"user_login";s:4:"type";s:4:"text";s:5:"label";s:8:"Username";s:8:"required";i:1;s:6:"public";i:1;s:8:"editable";i:0;s:8:"validate";s:15:"unique_username";s:9:"min_chars";i:3;s:9:"max_chars";i:24;s:8:"position";s:1:"1";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:10:"user_email";a:13:{s:5:"title";s:14:"E-mail Address";s:7:"metakey";s:10:"user_email";s:4:"type";s:4:"text";s:5:"label";s:14:"E-mail Address";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:8:"validate";s:12:"unique_email";s:8:"position";s:1:"4";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:13:"user_password";a:16:{s:5:"title";s:8:"Password";s:7:"metakey";s:13:"user_password";s:4:"type";s:8:"password";s:5:"label";s:8:"Password";s:8:"required";i:1;s:6:"public";i:1;s:8:"editable";i:1;s:9:"min_chars";i:8;s:9:"max_chars";i:30;s:15:"force_good_pass";i:1;s:18:"force_confirm_pass";i:1;s:8:"position";s:1:"5";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:10:"first_name";a:12:{s:5:"title";s:10:"First Name";s:7:"metakey";s:10:"first_name";s:4:"type";s:4:"text";s:5:"label";s:10:"First Name";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:8:"position";s:1:"2";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:9:"last_name";a:12:{s:5:"title";s:9:"Last Name";s:7:"metakey";s:9:"last_name";s:4:"type";s:4:"text";s:5:"label";s:9:"Last Name";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:8:"position";s:1:"3";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:9:"_um_row_1";a:4:{s:4:"type";s:3:"row";s:2:"id";s:9:"_um_row_1";s:8:"sub_rows";s:1:"1";s:4:"cols";s:1:"1";}}',
			'_um_mode' => 'register',
			'_um_core' => 'register',
			'_um_register_use_globals' => 1,
		);

		$this->core_form_meta['login'] = array(
			'_um_custom_fields' => 'a:3:{s:8:"username";a:13:{s:5:"title";s:18:"Username or E-mail";s:7:"metakey";s:8:"username";s:4:"type";s:4:"text";s:5:"label";s:18:"Username or E-mail";s:8:"required";i:1;s:6:"public";i:1;s:8:"editable";i:0;s:8:"validate";s:24:"unique_username_or_email";s:8:"position";s:1:"1";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:13:"user_password";a:16:{s:5:"title";s:8:"Password";s:7:"metakey";s:13:"user_password";s:4:"type";s:8:"password";s:5:"label";s:8:"Password";s:8:"required";i:1;s:6:"public";i:1;s:8:"editable";i:1;s:9:"min_chars";i:8;s:9:"max_chars";i:30;s:15:"force_good_pass";i:1;s:18:"force_confirm_pass";i:1;s:8:"position";s:1:"2";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:9:"_um_row_1";a:4:{s:4:"type";s:3:"row";s:2:"id";s:9:"_um_row_1";s:8:"sub_rows";s:1:"1";s:4:"cols";s:1:"1";}}',
			'_um_mode' => 'login',
			'_um_core' => 'login',
			'_um_login_use_globals' => 1,
		);

		$this->core_form_meta['profile'] = array(
			'_um_custom_fields' => 'a:1:{s:9:"_um_row_1";a:4:{s:4:"type";s:3:"row";s:2:"id";s:9:"_um_row_1";s:8:"sub_rows";s:1:"1";s:4:"cols";s:1:"1";}}',
			'_um_mode' => 'profile',
			'_um_core' => 'profile',
			'_um_profile_use_globals' => 1,
		);

		// admin permissions
		$this->perms = array(
			'core' => 'admin',
			'can_access_wpadmin' => 1,
			'can_not_see_adminbar' => 0,
			'can_edit_everyone' => 1,
			'can_delete_everyone' => 1,
			'can_edit_profile' => 1,
			'can_delete_profile' => 1,
			'can_view_all' => 1,
			'can_make_private_profile' => 1,
			'can_access_private_profile' => 1,
			'default_homepage' => 1,
			'status' => 'approved',
			'auto_approve_act' => 'redirect_profile',
			'after_login' => 'redirect_admin',
			'after_logout' => 'redirect_home',
		);

		// non-admin permissions
		$this->nonadmin_perms = array(
			'core' => 'member',
			'can_access_wpadmin' => 0,
			'can_not_see_adminbar' => 1,
			'can_edit_everyone' => 0,
			'can_delete_everyone' => 0,
			'can_make_private_profile' => 0,
			'can_access_private_profile' => 0,
			'after_login' => 'redirect_profile',
		);

	}

	/***
	***	@Get default permissions
	***/
	function get_initial_permissions( $role ) {
		if ( $role == 'admin' ) {
			$perms = $this->perms;
			return $perms;
		} else {
			$perms = $this->perms;
			$perms = array_merge($this->perms, $this->nonadmin_perms);
			return $perms;
		}
	}

	/***
	***	@Basics
	***/
	function install_basics() {
		if ( !get_option('__ultimatemember_sitekey') )
			update_option('__ultimatemember_sitekey', str_replace( array('http://','https://'), '', sanitize_user( get_bloginfo('url') ) ) . '-' . wp_generate_password( 20, false ) );
	}

	/***
	***	@Default Forms
	***/
	function install_default_forms(){
		global $wpdb, $ultimatemember;

		if ( current_user_can('manage_options') && um_user('ID') && !get_option('um_is_installed') ) {

			update_option('um_is_installed', 1);

			// Install Core Forms
			foreach($this->core_forms as $id ) {

				/**
					If page does not exist
					Create it
				**/
				$page_exists = $ultimatemember->query->find_post_id('um_form','_um_core', $id);
				if ( !$page_exists ) {

					if ( $id == 'register' ) {
						$title = 'Default Registration';
					} else if ( $id == 'login' ) {
						$title = 'Default Login';
					} else {
						$title = 'Default Profile';
					}

					$form = array(
						'post_type' 	  	=> 'um_form',
						'post_title'		=> $title,
						'post_status'		=> 'publish',
						'post_author'   	=> um_user('ID'),
					);

					$form_id = wp_insert_post( $form );

					foreach( $this->core_form_meta[$id] as $key => $value ) {
						if ( $key == '_um_custom_fields' ) {
							$array = unserialize( $value );
							update_post_meta( $form_id, $key, $array );
						} else {
							update_post_meta($form_id, $key, $value);
						}
					}

					$this->setup_shortcode[$id] = '[ultimatemember form_id='.$form_id.']';

					$core_forms[ $form_id ] = $form_id;

				}
				/** DONE **/

			}
			if ( isset( $core_forms ) ) update_option('um_core_forms', $core_forms);

			// Install Core Directories
			foreach($this->core_directories as $id ) {

				/**
					If page does not exist
					Create it
				**/
				$page_exists = $ultimatemember->query->find_post_id('um_directory','_um_core', $id);
				if ( !$page_exists ) {

					$title = 'Members';

					$form = array(
						'post_type' 	  	=> 'um_directory',
						'post_title'		=> $title,
						'post_status'		=> 'publish',
						'post_author'   	=> um_user('ID'),
					);

					$form_id = wp_insert_post( $form );

					foreach( $this->core_directory_meta[$id] as $key => $value ) {
						if ( $key == '_um_custom_fields' ) {
							$array = unserialize( $value );
							update_post_meta( $form_id, $key, $array );
						} else {
							update_post_meta($form_id, $key, $value);
						}
					}

					$this->setup_shortcode[$id] = '[ultimatemember form_id='.$form_id.']';

					$core_directories[ $form_id ] = $form_id;

				}
				/** DONE **/

			}
			if ( isset( $core_directories ) ) update_option('um_core_directories', $core_directories);

			// Install Core Pages
			foreach($this->core_pages as $slug => $array ) {

				/**
					If page does not exist
					Create it
				**/
				$page_exists = $ultimatemember->query->find_post_id('page','_um_core', $slug);
				if ( !$page_exists ) {

					if ( $slug == 'logout' ) {
						$content = '';
					} else if ( $slug == 'account' ) {
						$content = '[ultimatemember_account]';
					} else if ( $slug == 'password-reset' ) {
						$content = '[ultimatemember_password]';
					} else if ( $slug == 'user' ){
						$content = $this->setup_shortcode['profile'];
					} else {
						$content = $this->setup_shortcode[$slug];
					}

					$user_page = array(
						'post_title'		=> $array['title'],
						'post_content'		=> $content,
						'post_name'			=> $slug,
						'post_type' 	  	=> 'post',
						'post_status'		=> 'publish',
						'post_author'   	=> um_user('ID'),
						'comment_status'    => 'closed'
					);

					$post_id = wp_insert_post( $user_page );
					wp_update_post( array('ID' => $post_id, 'post_type' => 'page' ) );

					update_post_meta($post_id, '_um_core', $slug);

					$core_pages[ $slug ] = $post_id;

				}
				/** DONE **/

			}
			if ( isset( $core_pages ) ) {
				update_option('um_core_pages', $core_pages);
				$options = get_option('um_options');
				foreach( $core_pages as $o_slug => $page_id ) {
					$options['core_' . $o_slug] = $page_id;
				}
				if ( isset( $options ) ) {
					update_option('um_options', $options );
				}
			}

		}

	}

	/***
	***	@First setup of core roles
	***/
	function install_default_roles(){

		if ( !get_option('um_first_setup_roles') ) {

			update_option('um_first_setup_roles', 1);

			$users = get_users( array('fields' => 'ID') );
			foreach( $users as $id ) {

				delete_user_meta( $id, 'account_status' );
				delete_user_meta( $id, 'role' );

				update_user_meta( $id, 'account_status', 'approved' );

				if ( !is_super_admin( $id ) ) {
					if ( is_numeric( $id ) ) {
						update_user_meta( $id, 'role', 'member' );
					}
				} else {
					if ( is_numeric( $id ) ) {
						update_user_meta( $id, 'role', 'admin' );
					}
				}

			}

		}

		if ( !get_option('um_hashed_passwords_fix') ) {
			update_option('um_hashed_passwords_fix', 1);
			$users = get_users( array('fields' => 'ID') );
			foreach( $users as $id ) {
				delete_user_meta( $id, '_um_cool_but_hard_to_guess_plain_pw' );
			}
		}

	}

	/***
	***	@Build default roles
	***/
	function install_posts_roles(){

		global $wpdb, $ultimatemember;

		if ( !isset( $ultimatemember->query ) || ! method_exists( $ultimatemember->query, 'get_roles' ) ) {
			return;
		} else {
			//die('Method loaded!');
		}

		$admin = $ultimatemember->query->find_post_id('um_role','_um_core','admin');

		if ( !$admin && current_user_can('manage_options') && um_user('ID') ){

			$admin_role = array(
				'post_title'		=> 'Admin',
				'post_name'			=> 'admin',
				'post_type' 	  	=> 'um_role',
				'post_status'		=> 'publish',
				'post_author'   	=> um_user('ID'),
			);

			$post_id = wp_insert_post( $admin_role );

			foreach( $this->get_initial_permissions('admin') as $key => $value ) update_post_meta($post_id, "_um_" . $key, $value);

		}

		$member = $ultimatemember->query->find_post_id('um_role','_um_core','member');

		if ( !$member && current_user_can('manage_options') && um_user('ID') ){

			$member_role = array(
				'post_title'		=> 'Member',
				'post_name'			=> 'member',
				'post_type' 	  	=> 'um_role',
				'post_status'		=> 'publish',
				'post_author'   	=> um_user('ID'),
			);

			$post_id = wp_insert_post( $member_role );

			foreach( $this->get_initial_permissions('member') as $key => $value ) update_post_meta($post_id, "_um_" . $key, $value);

		}

	}

}
