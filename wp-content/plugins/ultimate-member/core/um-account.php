<?php

class UM_Account {


	function __construct() {

		$this->register_fields = array(); 

		add_shortcode('ultimatemember_account', array(&$this, 'ultimatemember_account'), 1);

		add_filter('um_account_page_default_tabs_hook', array(&$this, 'core_tabs'), 1);

		add_action('template_redirect', array(&$this, 'account'), 10001 );

		add_action('template_redirect', array(&$this, 'form_init'), 10002);

		add_filter('um_predefined_fields_hook', array(&$this,'predefined_fields_hook'),1 );

		$this->current_tab = 'general';

	}

	/**
	 * Get Core account tabs
	 * @return array
	 */
	function core_tabs() {

		$tabs[100]['general']['icon'] = 'um-faicon-user';
		$tabs[100]['general']['title'] = __('Account','ultimate-member');

		$tabs[200]['password']['icon'] = 'um-faicon-asterisk';
		$tabs[200]['password']['title'] = __('Change Password','ultimate-member');

		$tabs[300]['privacy']['icon'] = 'um-faicon-lock';
		$tabs[300]['privacy']['title'] = __('Privacy','ultimate-member');

		$tabs[400]['notifications']['icon'] = 'um-faicon-envelope';
		$tabs[400]['notifications']['title'] = __('Notifications','ultimate-member');

		$tabs[9999]['delete']['icon'] = 'um-faicon-trash-o';
		$tabs[9999]['delete']['title'] = __('Delete Account','ultimate-member');

		return $tabs;
	}

	/**
	 * Account page form
	 */
	function form_init() {
		global $ultimatemember;

		if ( um_submitting_account_page() ) {

			$ultimatemember->form->post_form = $_POST;

			do_action('um_submit_account_errors_hook', $ultimatemember->form->post_form );

			if ( !isset($ultimatemember->form->errors) ) {

				if ( get_query_var('um_tab') ) {
					$this->current_tab = get_query_var('um_tab');
				}

				do_action('um_submit_account_details', $ultimatemember->form->post_form );

			}

		}

	}

	/**
	 * Can access account page
	 */
	function account(){
		global $ultimatemember;

		if ( um_is_core_page('account') && !is_user_logged_in() ) {
			
			$redirect_to = add_query_arg(	
				'redirect_to', 
				urlencode_deep( um_get_core_page('account') ) , 
				um_get_core_page('login') 
			);
			
			exit( wp_redirect( $redirect_to ) );
		
		}

		if ( um_is_core_page('account') ) {

			$ultimatemember->fields->set_mode = 'account';

			$ultimatemember->fields->editing = true;

			if ( get_query_var('um_tab') ) {
				$this->current_tab = get_query_var('um_tab');
			}

		}

	}

	/**
	 * Get Tab Link
	 * @param  integer $id 
	 * @return string
	 */
	function tab_link( $id ) {

		if ( get_option('permalink_structure') ) {

			$url = trailingslashit( untrailingslashit( um_get_core_page('account') ) );
			$url = $url . $id . '/';

		} else {

			$url = add_query_arg( 'um_tab', $id, um_get_core_page('account') );

		}

		return $url;
	}

	/**
	 * Add class based on shortcode
	 * @param  string $mode
	 * @return string     
	 */
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

	/**
	 * Get Tab Output
	 * @param  integer $id 
	 * @return string 
	 */
	function get_tab_output( $id ) {
		global $ultimatemember;

		$output = null;
        
		switch( $id ) {

			case 'notifications':

				$output = apply_filters("um_account_content_hook_{$id}", $output);
				return $output;

				break;

			case 'privacy':

				$args = 'profile_privacy,hide_in_members';
				$args = apply_filters('um_account_tab_privacy_fields', $args );

				$fields = $ultimatemember->builtin->get_specific_fields( $args );

				$fields = apply_filters('um_account_secure_fields', $fields, $id );

				foreach( $fields as $key => $data ){
					$output .= $ultimatemember->fields->edit_field( $key, $data );
				}

				return $output;

				break;

			case 'delete':

				$args = 'single_user_password';

				$fields = $ultimatemember->builtin->get_specific_fields( $args );

				$fields = apply_filters('um_account_secure_fields', $fields, $id );

				foreach( $fields as $key => $data ){
					$output .= $ultimatemember->fields->edit_field( $key, $data );
				}

				return $output;

				break;

			case 'general':

				$args = 'user_login,first_name,last_name,user_email';

				if ( !um_get_option('account_name') ) {
					$args = 'user_login,user_email';
				}

				if ( !um_get_option('account_email') && !um_user('can_edit_everyone') ) {
					$args = str_replace(',user_email','', $args );
				}

				$fields = $ultimatemember->builtin->get_specific_fields( $args );

				$fields = apply_filters('um_account_secure_fields', $fields, $id );

				foreach( $fields as $key => $data ){
					$output .= $ultimatemember->fields->edit_field( $key, $data );
				}

				return $output;

				break;

			case 'password':

				$args = 'user_password';

				$fields = $ultimatemember->builtin->get_specific_fields( $args );

				$fields = apply_filters('um_account_secure_fields', $fields, $id );

				foreach( $fields as $key => $data ){
					$output .= $ultimatemember->fields->edit_field( $key, $data );
				}

				return $output;

				break;

			default :

				$output = apply_filters("um_account_content_hook_{$id}", $output);
				return $output;

				break;

		}
	}

	/**
	 * Shortcode
	 * @param  array  $args 
	 * @return string      
	 */
	function ultimatemember_account( $args = array() ) {
		return $this->load( $args );
	}

	/**
	 * Load module with global function
	 * @param  array $args 
	 * @return string       
	 */
	function load( $args ) {

		global $ultimatemember;

		$ultimatemember->user->set( get_current_user_id() );

		ob_start();

		$defaults = array(
			'template' => 'account',
			'mode' => 'account',
			'form_id' => 'um_account_id',
		);
		$args = wp_parse_args( $args, $defaults );

		if ( isset( $args['use_globals'] ) && $args['use_globals'] == 1 ) {
			$args = array_merge( $args, $this->get_css_args( $args ) );
		} else {
			$args = array_merge( $this->get_css_args( $args ), $args );
		}

		$args = apply_filters('um_account_shortcode_args_filter', $args);

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

	/**
	 * Get dynamic css args
	 * @param  array $args 
	 * @return array      
	 */
	function get_css_args( $args ) {
		$arr = um_styling_defaults( $args['mode'] );
		$arr = array_merge( $arr, array( 'form_id' => $args['form_id'], 'mode' => $args['mode'] ) );
		return $arr;
	}

	/**
	 * Load dynamic css
	 * @param  array  $args 
	 */
	function dynamic_css( $args=array() ) {
		extract($args);
		$global = um_path . 'assets/dynamic_css/dynamic_global.php';
		$file = um_path . 'assets/dynamic_css/dynamic_'.$mode.'.php';
		include $global;
		if ( file_exists( $file ) )
			include $file;
	}

	/**
	 * Loads a template file
	 * @param  string $template 
	 * @param  array  $args     
	 */
	function template_load( $template, $args=array() ) {
		global $ultimatemember;
		if ( is_array( $args ) ) {
			$ultimatemember->shortcodes->set_args = $args;
		}
		$ultimatemember->shortcodes->load_template( $template );
	}

	/**
	 * Filter account fields
	 * @param  array $predefined_fields 
	 * @return array               
	 */
	function predefined_fields_hook( $predefined_fields ){

		$account_hide_in_directory =  um_get_option('account_hide_in_directory');
		if( !  $account_hide_in_directory  ){
			unset( $predefined_fields['hide_in_members'] );
		}

		return $predefined_fields;
	}
}
