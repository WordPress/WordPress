<?php

class UM_Password {

	function __construct() {
	
		add_shortcode('ultimatemember_password', array(&$this, 'ultimatemember_password'), 1);

		add_action('template_redirect', array(&$this, 'password_reset'), 10001 );
		
		add_action('template_redirect', array(&$this, 'form_init'), 10002);
		
		add_action('init',  array(&$this, 'listen_to_password_reset_uri'), 1);

	}
	
	/***
	***	@a listener to password reset uri
	***/
	function listen_to_password_reset_uri() {
	
		global $ultimatemember;
		
		if ( isset($_REQUEST['act']) && $_REQUEST['act'] == 'reset_password' && isset($_REQUEST['hash']) && strlen($_REQUEST['hash']) == 40 &&
			isset($_REQUEST['user_id']) && is_numeric($_REQUEST['user_id']) ) {
			
				$user_id = absint( $_REQUEST['user_id'] );
				delete_option( "um_cache_userdata_{$user_id}" );
				
				um_fetch_user( $user_id );
				
				if ( $_REQUEST['hash'] != um_user('reset_pass_hash') ){
					wp_die( __('This is not a valid hash, or it has expired.','ultimate-member') );
				}

				$ultimatemember->user->profile['reset_pass_hash_token'] = current_time( 'timestamp' );
				$ultimatemember->user->update_usermeta_info('reset_pass_hash_token');
				
				$this->change_password = true;
				
				um_reset_user();

		}
		
	}
	
	/***
	***	@reset url
	***/
	function reset_url(){
		global $ultimatemember;
		
		if ( !um_user('reset_pass_hash') ) return false;
		
		$user_id = um_user('ID');
		
		delete_option( "um_cache_userdata_{$user_id}" );
				
		$url =  add_query_arg( 'act', 'reset_password', um_get_core_page('password-reset') );
		$url =  add_query_arg( 'hash', esc_attr( um_user('reset_pass_hash') ), $url );
		$url =  add_query_arg( 'user_id', esc_attr( um_user('ID') ), $url );
		
		return $url;
		
	}
	
	/***
	***	@we are on password reset page
	***/
	function password_reset(){
		global $ultimatemember;
		
		if ( um_is_core_page('password-reset') ) {
			
			$ultimatemember->fields->set_mode = 'password';
			
		}
		
	}
	
	/***
	***	@password page form
	***/
	function form_init() {
		global $ultimatemember;

		if ( um_requesting_password_reset() ) {
			
			$ultimatemember->form->post_form = $_POST;

			do_action('um_reset_password_errors_hook', $ultimatemember->form->post_form );
			
			if ( !isset($ultimatemember->form->errors) ) {

				do_action('um_reset_password_process_hook', $ultimatemember->form->post_form );

			}

		}
		
		if ( um_requesting_password_change() ) {
			
			$ultimatemember->form->post_form = $_POST;

			do_action('um_change_password_errors_hook', $ultimatemember->form->post_form );
			
			if ( !isset($ultimatemember->form->errors) ) {

				do_action('um_change_password_process_hook', $ultimatemember->form->post_form );

			}

		}

	}
	
	/***
	***	@Add class based on shortcode
	***/
	function get_class( $mode ){
	
		global $ultimatemember;
		
		$classes = 'um-'.$mode;
		
		if ( is_admin() ) {
			$classes .= ' um-in-admin';
		}
		
		if ( $ultimatemember->fields->editing == true ) {
			$classes .= ' um-editing';
		}
		
		if ( $ultimatemember->fields->viewing == true ) {
			$classes .= ' um-viewing';
		}
		
		$classes = apply_filters('um_form_official_classes__hook', $classes);
		return $classes;
	}
	
	/***
	***	@Shortcode
	***/
	function ultimatemember_password( $args = array() ) {
		return $this->load( $args );
	}
	
	/***
	***	@Load a module with global function
	***/
	function load( $args ) {
	
		global $ultimatemember;
		
		ob_start();

		$defaults = array(
			'template' => 'password-reset',
			'mode' => 'password',
			'form_id' => 'um_password_id',
			'max_width' => '450px',
			'align' => 'center',
		);
		$args = wp_parse_args( $args, $defaults );
		
		if ( isset( $args['use_globals'] ) && $args['use_globals'] == 1 ) {
			$args = array_merge( $args, $this->get_css_args( $args ) );
		} else {
			$args = array_merge( $this->get_css_args( $args ), $args );
		}
		
		$args = apply_filters('um_reset_password_shortcode_args_filter', $args);

		if ( isset( $this->change_password ) ) {
			
			$args['user_id'] =  $_REQUEST['user_id'];
			$args['template'] = 'password-change';

		}
		
		extract( $args, EXTR_SKIP );
		
		do_action("um_pre_{$mode}_shortcode", $args);
		
		do_action("um_before_form_is_loaded", $args);
		
		do_action("um_before_{$mode}_form_is_loaded", $args);

		$this->template_load( $template, $args );
		
		if ( !is_admin() && !defined( 'DOING_AJAX' ) ) {
			$this->dynamic_css( $args );
		}
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
		
	}
	
	/***
	***	@Get dynamic css args
	***/
	function get_css_args( $args ) {
		$arr = um_styling_defaults( $args['mode'] );
		$arr = array_merge( $arr, array( 'form_id' => $args['form_id'], 'mode' => $args['mode'] ) );
		return $arr;
	}
	
	/***
	***	@Load dynamic css
	***/
	function dynamic_css( $args=array() ) {
		extract($args);
		$global = um_path . 'assets/dynamic_css/dynamic_global.php';
		$file = um_path . 'assets/dynamic_css/dynamic_'.$mode.'.php';
		include $global;
		if ( file_exists( $file ) )
			include $file;
	}

	/***
	***	@Loads a template file
	***/
	function template_load( $template, $args=array() ) {
		global $ultimatemember;
		if ( is_array( $args ) ) {
			$ultimatemember->shortcodes->set_args = $args;
		}
		$ultimatemember->shortcodes->load_template( $template );
	}

}