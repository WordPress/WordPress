<?php

	/***
	***	@Error processing hook : login
	***/
	add_action('um_submit_form_errors_hook_login', 'um_submit_form_errors_hook_login', 10);
	function um_submit_form_errors_hook_login( $args ){
		global $ultimatemember;

		$is_email = false;

			  $form_id = $args['form_id'];
				 $mode = $args['mode'];
		$user_password = $args['user_password'];


		if ( isset( $args['username'] ) && $args['username'] == '' ) {
			$ultimatemember->form->add_error( 'username',  __('Please enter your username or email','ultimate-member') );
		}

		if ( isset( $args['user_login'] ) && $args['user_login'] == '' ) {
			$ultimatemember->form->add_error( 'user_login',  __('Please enter your username','ultimate-member') );
		}

		if ( isset( $args['user_email'] ) && $args['user_email'] == '' ) {
			$ultimatemember->form->add_error( 'user_email',  __('Please enter your email','ultimate-member') );
		}

		if ( isset( $args['username'] ) ) {
			$field = 'username';
			if ( is_email( $args['username'] ) ) {
				$is_email = true;
				$data = get_user_by('email', $args['username'] );
				$user_name = (isset ( $data->user_login ) ) ? $data->user_login : null;
			} else {
				$user_name  = $args['username'];
			}
		} else if ( isset( $args['user_email'] ) ) {
				$field = 'user_email';
				$is_email = true;
				$data = get_user_by('email', $args['user_email'] );
				$user_name = (isset ( $data->user_login ) ) ? $data->user_login : null;
		} else {
				$field = 'user_login';
				$user_name = $args['user_login'];
		}

		if ( !username_exists( $user_name ) ) {
			if ( $is_email ) {
				$ultimatemember->form->add_error( $field,  __(' Sorry, we can\'t find an account with that email address','ultimate-member') );
			} else {
				$ultimatemember->form->add_error( $field,  __(' Sorry, we can\'t find an account with that username','ultimate-member') );
			}
		} else {
			if ( $args['user_password'] == '' ) {
				$ultimatemember->form->add_error( 'user_password',  __('Please enter your password','ultimate-member') );
			}
		}

		$user = get_user_by( 'login', $user_name );
		if ( $user && wp_check_password( $args['user_password'], $user->data->user_pass, $user->ID) ) {
			$ultimatemember->login->auth_id = username_exists( $user_name );
		} else {
			$ultimatemember->form->add_error( 'user_password',  __('Password is incorrect. Please try again.','ultimate-member') );
		}

		// add a way for other plugins like wp limit login
		// to limit the login attempts
		$user = apply_filters( 'authenticate', null, $user_name, $args['user_password'] );
		
		$authenticate_user = apply_filters( 'wp_authenticate_user', $user_name, $args['user_password'] );
		
		// @since 4.18 replacement for 'wp_login_failed' action hook
		// see WP function wp_authenticate()
		$ignore_codes = array('empty_username', 'empty_password');

		if ( is_wp_error( $user ) && ! in_array( $user->get_error_code(), $ignore_codes ) ) {
			
				$ultimatemember->form->add_error( $user->get_error_code(),  __( $user->get_error_message() ,'ultimate-member') );
		}

		if( is_wp_error( $authenticate_user ) && ! in_array( $authenticate_user->get_error_code(), $ignore_codes ) ){

				$ultimatemember->form->add_error( $authenticate_user->get_error_code(),  __( $authenticate_user->get_error_message() ,'ultimate-member') );
		
		}

		// if there is an error notify wp
		if( $ultimatemember->form->has_error( $field ) || $ultimatemember->form->has_error( $user_password ) || $ultimatemember->form->count_errors() > 0 ) {
			do_action( 'wp_login_failed', $user_name );
		}
	}

	/**
	 * Display the login errors from other plugins
	 */
	add_action( 'um_before_login_fields', 'um_display_login_errors' );
	function um_display_login_errors( $args ) {
		global $ultimatemember;
	
		$error = '';
	
		if( $ultimatemember->form->count_errors() > 0 ) {
			$errors = $ultimatemember->form->errors;
			// hook for other plugins to display error
			$error_keys = array_keys( $errors );
		}

		if( isset( $args['custom_fields'] ) ){
			$custom_fields = $args['custom_fields'];
		}

		if( ! empty( $error_keys ) && ! empty( $custom_fields ) ){
			foreach( $error_keys as $error ){
				if( trim( $error ) && ! isset( $custom_fields[ $error ] )  && ! empty(  $errors[ $error ] ) ){
					$error_message = apply_filters( 'login_errors', $errors[ $error ]  );
					echo '<p class="um-notice err um-error-code-'.$error.'"><i class="um-icon-ios-close-empty" onclick="jQuery(this).parent().fadeOut();"></i>' . $error_message  . '</p>';
				}
			}
		}
	}

	/***
	***	@login checks thru the frontend login
	***/
	add_action('um_submit_form_errors_hook_logincheck', 'um_submit_form_errors_hook_logincheck', 9999 );
	function um_submit_form_errors_hook_logincheck($args){
		global $ultimatemember;

		// Logout if logged in
		if ( is_user_logged_in() ) {
			wp_logout();
		}

		$user_id = ( isset( $ultimatemember->login->auth_id ) ) ? $ultimatemember->login->auth_id : '';
		um_fetch_user( $user_id );

		$status = um_user('account_status'); // account status
		switch( $status ) {

			// If user can't login to site...
			case 'inactive':
			case 'awaiting_admin_review':
			case 'awaiting_email_confirmation':
			case 'rejected':
				um_reset_user();
				exit( wp_redirect(  add_query_arg( 'err', esc_attr( $status ), $ultimatemember->permalinks->get_current_url() ) ) );
				break;

		}

		if ( isset( $args['form_id'] ) && $args['form_id'] == $ultimatemember->shortcodes->core_login_form() &&  $ultimatemember->form->errors && !isset( $_POST[ $ultimatemember->honeypot ] ) ) {
			exit( wp_redirect( um_get_core_page('login') ) );
		}

	}

	/***
	***	@store last login timestamp
	***/
	add_action('um_on_login_before_redirect', 'um_store_lastlogin_timestamp', 10);
	function um_store_lastlogin_timestamp( $user_id ) {
		delete_user_meta( $user_id, '_um_last_login' );
		update_user_meta( $user_id, '_um_last_login', current_time( 'timestamp' ) );
	}

	add_action( 'wp_login', 'um_store_lastlogin_timestamp_' );
	function um_store_lastlogin_timestamp_( $login ) {
		$user = get_user_by('login',$login);
		$user_id = $user->ID;
		delete_user_meta( $user_id, '_um_last_login' );
		update_user_meta( $user_id, '_um_last_login', current_time( 'timestamp' ) );
	}

	/***
	***	@login user
	***/
	add_action('um_user_login', 'um_user_login', 10);
	function um_user_login($args){
		global $ultimatemember;
		extract( $args );

		$rememberme = ( isset($args['rememberme']) ) ? 1 : 0;
		
		if ( ( um_get_option('deny_admin_frontend_login')   && ! isset( $_GET['provider'] ) ) && strrpos( um_user('wp_roles' ), 'administrator' ) !== FALSE ){
			wp_die( __('This action has been prevented for security measures.','ultimate-member') );
		}

		$ultimatemember->user->auto_login( um_user('ID'), $rememberme );

		// Hook that runs after successful login and before user is redirected
		do_action('um_on_login_before_redirect', um_user('ID') );

		// Priority redirect
		if ( isset( $args['redirect_to'] ) && ! empty( $args['redirect_to']  ) ) {
			exit( wp_redirect(  $args['redirect_to']  ) );
		}

		$role = um_user('role');
		$role_data = $ultimatemember->query->role_data( $role );
		
		// Role redirect
		$after = $role_data['after_login'];
		switch( $after ) {

			case 'redirect_admin':
				exit( wp_redirect( admin_url() ) );
				break;

			case 'redirect_profile':
				exit( wp_redirect( um_user_profile_url() ) );
				break;

			case 'redirect_url':
				exit( wp_redirect( $role_data['login_redirect_url'] ) );
				break;

			case 'refresh':
				exit( wp_redirect( $ultimatemember->permalinks->get_current_url() ) );
				break;

		}
	}

	/***
	***	@form processing
	***/
	add_action('um_submit_form_login', 'um_submit_form_login', 10);
	function um_submit_form_login($args){
		global $ultimatemember;
		
		if ( !isset($ultimatemember->form->errors) ) {
			do_action( 'um_user_login', $args );
		}

		do_action('um_user_login_extra_hook', $args );
	}

	/***
	***	@Show the submit button
	***/
	add_action('um_after_login_fields', 'um_add_submit_button_to_login', 1000);
	function um_add_submit_button_to_login($args){
		global $ultimatemember;

		// DO NOT add when reviewing user's details
		if ( $ultimatemember->user->preview == true && is_admin() ) return;

		$primary_btn_word = $args['primary_btn_word'];
		$primary_btn_word = apply_filters('um_login_form_button_one', $primary_btn_word, $args );

		$secondary_btn_word = $args['secondary_btn_word'];
		$secondary_btn_word = apply_filters('um_login_form_button_two', $secondary_btn_word, $args );

		$secondary_btn_url = ( isset( $args['secondary_btn_url'] ) && $args['secondary_btn_url'] ) ? $args['secondary_btn_url'] : um_get_core_page('register');
		$secondary_btn_url = apply_filters('um_login_form_button_two_url', $secondary_btn_url, $args );

		?>

		<div class="um-col-alt">

			<?php if ( isset( $args['show_rememberme'] ) && $args['show_rememberme'] ) {
					echo $ultimatemember->fields->checkbox('rememberme', __('Keep me signed in','ultimate-member') );
					echo '<div class="um-clear"></div>';
			} ?>

			<?php if ( isset($args['secondary_btn']) && $args['secondary_btn'] != 0 ) { ?>

			<div class="um-left um-half"><input type="submit" value="<?php echo __( $primary_btn_word,'ultimate-member'); ?>" class="um-button" id="um-submit-btn" /></div>
			<div class="um-right um-half"><a href="<?php echo $secondary_btn_url; ?>" class="um-button um-alt"><?php echo __( $secondary_btn_word,'ultimate-member'); ?></a></div>

			<?php } else { ?>

			<div class="um-center"><input type="submit" value="<?php echo __( $args['primary_btn_word'],'ultimate-member'); ?>" class="um-button" id="um-submit-btn" /></div>

			<?php } ?>

			<div class="um-clear"></div>

		</div>

		<?php
	}

	/***
	***	@Display a forgot password link
	***/
	add_action('um_after_login_fields', 'um_after_login_submit', 1001);
	function um_after_login_submit( $args ){
		global $ultimatemember;

		if ( $args['forgot_pass_link'] == 0 ) return;

		?>

		<div class="um-col-alt-b">
			<a href="<?php echo um_get_core_page('password-reset'); ?>" class="um-link-alt"><?php _e('Forgot your password?','ultimate-member'); ?></a>
		</div>

		<?php
	}

	/***
	***	@Show Fields
	***/
	add_action('um_main_login_fields', 'um_add_login_fields', 100);
	function um_add_login_fields($args){
		global $ultimatemember;
		
		echo $ultimatemember->fields->display( 'login', $args );
	}

	/**
	 * Remove authenticate filter
	 * @uses 'wp_authenticate_username_password_before'
	 */
	add_action('wp_authenticate_username_password_before','um_auth_username_password_before',10,3);
	function um_auth_username_password_before( $user, $username, $password ){
		remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
	}

