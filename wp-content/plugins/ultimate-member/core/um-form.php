<?php

class UM_Form {

	public $form_suffix;

	function __construct() {

		$this->post_form = null;

		$this->form_suffix = null;

		$this->errors = null;

		$this->processing = null;

		add_action('template_redirect', array(&$this, 'form_init'), 2);

		add_action('init', array(&$this, 'field_declare'), 10);

	}

	/**
	 * Count the form errors.
	 * @return integer
	 */
	function count_errors() {
		$errors = $this->errors;

		if( $errors && is_array( $errors ) ) {
			return count( $errors );
		}

		return 0;
	}


	/**
	 * Appends field errors
	 * @param string $key 
	 * @param string $error 
	 */
	function add_error( $key, $error ) {
		if ( ! isset( $this->errors[ $key ] ) ){

			$error = apply_filters('um_submit_form_error', $error , $key );

			$this->errors[ $key ] = $error;
		}
	}

	
	/**
	 * If a form has errors
	 * @param  string  $key 
	 * @return boolean     
	 */
	function has_error( $key ) {
		if ( isset($this->errors[$key]) )
			return true;
		return false;
	}

	
	/**
	 * Declare all fields
	 */
	function field_declare(){
		global $ultimatemember;
		if ( isset( $ultimatemember->builtin->custom_fields ) ) {
			$this->all_fields = $ultimatemember->builtin->custom_fields;
		} else {
			$this->all_fields = null;
		}
	}


	/**
	 * Validate form
	 */
	function form_init(){
		global $ultimatemember;

		if ( isset( $_SERVER['REQUEST_METHOD'] ) ) {
			$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
		} else {
			$http_post = 'POST';
		}

		
		if ( $http_post && !is_admin() && isset( $_POST['form_id'] ) && is_numeric($_POST['form_id']) ) {
			
			do_action("um_before_submit_form_post", $_POST );

			$this->form_id = $_POST['form_id'];
			$this->form_status = get_post_status( $this->form_id );


			if ( $this->form_status == 'publish' ) {

				/* save entire form as global */
				$this->post_form = apply_filters('um_submit_post_form' ,$_POST );

				$this->post_form = $this->beautify( $this->post_form );

				$this->form_data = $ultimatemember->query->post_data( $this->form_id );

				$this->post_form['submitted'] = $this->post_form;

				$this->post_form = array_merge( $this->form_data, $this->post_form );

				
				if( isset( $this->form_data['custom_fields'] )  && strstr( $this->form_data['custom_fields'], 'role_' )  ){  // Secure selected role
					
					$custom_field_roles = $this->custom_field_roles( $this->form_data['custom_fields'] );
                    
                    if( isset( $_POST['role'] ) ){
	                    $role = $_POST['role'];

	                    if( is_array( $_POST['role'] ) ){
	                    	$role = current( $_POST['role'] );
	                    }

						if ( isset( $custom_field_roles ) && is_array(  $custom_field_roles ) && ! empty( $role ) && ! in_array( $role , $custom_field_roles ) ) {
							wp_die( __( 'This is not possible for security reasons.','ultimate-member') );
						} 

						$this->post_form['role'] = $role;
						$this->post_form['submitted']['role'] = $role;
					}

					

				}else if( isset( $this->post_form['mode'] ) && $this->post_form['mode'] == 'register' ) {
					$role = $this->assigned_role( $this->form_id );
					$this->post_form['role'] = $role;
					$this->post_form['submitted']['role'] = $role;
				}
				
				if ( isset( $_POST[ $ultimatemember->honeypot ] ) && $_POST[ $ultimatemember->honeypot ] != '' ){
					wp_die('Hello, spam bot!','ultimate-member');
				}

				if ( !in_array( $this->form_data['mode'], array('login') ) ) {

					$form_timestamp  = trim($_POST['timestamp']);
					$live_timestamp  = current_time( 'timestamp' );

					if ( $form_timestamp == '' && um_get_option('enable_timebot') == 1 )
						wp_die( __('Hello, spam bot!','ultimate-member') );

					if ( !current_user_can('manage_options') && $live_timestamp - $form_timestamp < 6 && um_get_option('enable_timebot') == 1  )
						wp_die( __('Whoa, slow down! You\'re seeing this message because you tried to submit a form too fast and we think you might be a spam bot. If you are a real human being please wait a few seconds before submitting the form. Thanks!','ultimate-member') );

				}

				$this->post_form = apply_filters('um_submit_form_data', $this->post_form, $this->post_form['mode'] );

				/* Continue based on form mode - pre-validation */

				do_action('um_submit_form_errors_hook', $this->post_form );

				do_action("um_submit_form_{$this->post_form['mode']}", $this->post_form );

			}

		}

	}


	/**
	 * Beautify form data
	 * @param  array $form 
	 * @return array $form
	 */
	function beautify( $form ){

		if (isset($form['form_id'])){

			$this->form_suffix = '-' . $form['form_id'];

			$this->processing = $form['form_id'];

			foreach( $form as $key => $value ){
				if ( strstr( $key, $this->form_suffix ) ) {
					$a_key = str_replace( $this->form_suffix, '', $key );
					$form[ $a_key ] = $value;
					unset( $form[ $key ] );
				}
			}

		}

		return $form;
	}


	/**
	 * Display form type as Title
	 * @param  string $mode  
	 * @param  integer $post_id
	 * @return string $output
	 */
	function display_form_type( $mode, $post_id ){
		$output = null;
		switch( $mode ){
			case 'login':
				$output = 'Login';
				break;
			case 'profile':
				$output = 'Profile';
				break;
			case 'register':
				$output = 'Register';
				break;
		}
		return $output;
	}

	/**
	 * Assigned roles to a form
	 * @param  integer $post_id
	 * @return string $role
	 */
	function assigned_role( $post_id ){

		$mode = $this->form_type( $post_id );
		$use_globals = get_post_meta( $post_id, "_um_{$mode}_use_globals", true);
       
        $global_role = um_get_option('default_role'); // Form Global settings

		if( $use_globals == 0 ){ // Non-Global settings
			$role = get_post_meta( $post_id, "_um_{$mode}_role", true );
		}

		if( empty( $role ) ){ // custom role is default, return default role's slug
			$role = $global_role;
		}
        
    	return $role;
	
	}

	/**
	 * Get form type
	 * @param  integer $post_id 
	 * @return string
	 */
	function form_type( $post_id ){
		
		$mode = get_post_meta( $post_id, '_um_mode', true );
		
		return $mode;
	} 
    
    /**
     * Get custom field roles
     * @param  string $custom_fields serialized 
     * @return array  roles
     */
    function custom_field_roles( $custom_fields ){
        
        if( is_serialized( $custom_fields ) ){
        	$fields = unserialize( $custom_fields );

        	if( ! is_array( $fields )  ) return false;

        	foreach ( $fields  as $field_key => $field_settings ) {

        		 if( strstr( $field_key , 'role_') ){
        		 		if( is_array( $field_settings['options'] ) ){
                        	return array_keys( $field_settings['options'] );
        		 		}
        		 }

        	}

        }

    	return false;
    }
}
