<?php

	/**
	 * Account automatically approved
	 */
	add_action('um_post_registration_approved_hook', 'um_post_registration_approved_hook', 10, 2);
	function um_post_registration_approved_hook($user_id, $args){
		global $ultimatemember;

		um_fetch_user( $user_id );

		$ultimatemember->user->approve();
	}

	/**
	 * Account needs email validation
	 */
	add_action('um_post_registration_checkmail_hook', 'um_post_registration_checkmail_hook', 10, 2);
	function um_post_registration_checkmail_hook($user_id, $args){
		global $ultimatemember;

		um_fetch_user( $user_id );

		$ultimatemember->user->email_pending();
	}

	/**
	 * Account needs admin review
	 */
	add_action('um_post_registration_pending_hook', 'um_post_registration_pending_hook', 10, 2);
	function um_post_registration_pending_hook($user_id, $args){
		global $ultimatemember;

		um_fetch_user( $user_id );

		$ultimatemember->user->pending();
		
	}

	/**
	 * Add user to wordpress
	 */
	add_action('um_add_user_frontend', 'um_add_user_frontend', 10);
	function um_add_user_frontend($args){
		global $ultimatemember;

		unset( $args['user_id'] );
		
		extract($args);


		if ( isset( $username ) && !isset($args['user_login']) ) {
			$user_login = $username;
		}

		if ( ! empty( $first_name ) &&  ! empty( $last_name ) && ! isset( $user_login ) ) {

			if ( um_get_option('permalink_base') == 'name' ) {
				$user_login = rawurlencode( strtolower( str_replace(" ",".",$first_name." ".$last_name ) ) );
			}else if ( um_get_option('permalink_base') == 'name_dash' ) {
				$user_login = rawurlencode( strtolower( str_replace(" ","-",$first_name." ".$last_name ) ) );
			}else if ( um_get_option('permalink_base') == 'name_plus' ) {
				$user_login = strtolower( str_replace(" ","+",$first_name." ".$last_name ) );
			}else{
				$user_login = strtolower( str_replace(" ","",$first_name." ".$last_name ) );
			}

			// if full name exists
			$count = 1;
			while( username_exists( $user_login ) ) {
				$user_login .= $count;
				$count++;
			}
		}

		if( !isset( $user_login ) && isset( $user_email ) && $user_email )
		{
			$user_login = $user_email;
		}

		$unique_userID = $ultimatemember->query->count_users() + 1;

		if ( ! isset( $user_login ) ||  strlen( $user_login ) > 30 && ! is_email( $user_login ) ) {
			$user_login = 'user' . $unique_userID;
		}

		if ( isset( $username ) && is_email( $username ) ) {
			$user_email = $username;
		}

		if ( ! isset( $user_password ) ){
			$user_password = $ultimatemember->validation->generate( 8 );
		}


		if( ! isset( $user_email ) ) {
			$site_url = @$_SERVER['SERVER_NAME'];
			$user_email = 'nobody' . $unique_userID . '@' . $site_url;
			$user_email = apply_filters("um_user_register_submitted__email", $user_email );
		}


		$creds['user_login'] = $user_login;
		$creds['user_password'] = $user_password;
		$creds['user_email'] = trim( $user_email );

		$args = apply_filters('um_add_user_frontend_submitted', $args );
		
		$args['submitted'] = array_merge( $args['submitted'], $creds);
		$args = array_merge($args, $creds);
		
		unset( $args['user_id'] );
		

		do_action('um_before_new_user_register', $args);

		$user_id = wp_create_user( $user_login, $user_password, $user_email );

		do_action('um_after_new_user_register', $user_id, $args);

		return $user_id;
	}

	/**
	 * After adding a new user
	 */
	add_action('um_after_new_user_register', 'um_after_new_user_register', 10, 2);
	function um_after_new_user_register( $user_id, $args ){
		global $ultimatemember, $pagenow;
		extract( $args );

		um_fetch_user( $user_id );

		if ( !isset( $args['role'] ) ) {
			$role = um_get_option('default_role');
		}

		if ( $pagenow != 'user-new.php' && !array_key_exists( $role, $ultimatemember->query->get_roles( false, array('admin') ) ) ) {
			$role = um_get_option('default_role');
		}

		$ultimatemember->user->set_role( $role );

		$ultimatemember->user->set_registration_details( $args['submitted'] );

		$ultimatemember->user->set_last_login();

		do_action('um_new_user_registration_plain');

		do_action('um_post_registration_save', $user_id, $args);

		do_action('um_post_registration_listener', $user_id, $args);
		
		do_action('um_update_profile_full_name', $args );

		do_action('um_post_registration', $user_id, $args);

		if( ! is_admin() ){
			do_action('user_register', $user_id );
		}

	}

	/**
	 * Update user's profile after registration
	 */
	add_action('um_post_registration_save', 'um_post_registration_save', 10, 2);
	function um_post_registration_save( $user_id, $args ){
		global $ultimatemember;

		unset( $args['user_id'] );
		$args['_user_id'] = $user_id;
		$args['is_signup'] = 1;

		do_action('um_user_edit_profile', $args);

	}

	/**
	 * Post-registration admin listener
	 */
	add_action('um_post_registration_listener', 'um_post_registration_listener', 10, 2);
	function um_post_registration_listener( $user_id, $args ){
		global $ultimatemember;
        
        if ( um_user('status') != 'pending' ) {
			$ultimatemember->mail->send( um_admin_email(), 'notification_new_user', array('admin' => true ) );
		} else {
			$ultimatemember->mail->send( um_admin_email(), 'notification_review', array('admin' => true ) );
		}

	}

	/**
	 * Post-registration procedure
	 */
	add_action('um_post_registration', 'um_post_registration', 10, 2);
	function um_post_registration( $user_id, $args ){
		global $ultimatemember;
		unset(  $args['user_id'] );
		extract($args);
        
        $status = um_user('status');
         
		do_action("um_post_registration_global_hook", $user_id, $args);

		do_action("um_post_registration_{$status}_hook", $user_id, $args);

		if ( !is_admin() ) {

			do_action("track_{$status}_user_registration");

			// Priority redirect
			if ( isset( $args['redirect_to'] ) ) {
				exit( wp_redirect(  urldecode( $args['redirect_to'] ) ) );
			}
            
            if ( $status == 'approved' ) {

				$ultimatemember->user->auto_login( $user_id );
				$ultimatemember->permalinks->profile_url( true );

				do_action('um_registration_after_auto_login', $user_id );

				if ( um_user('auto_approve_act') == 'redirect_url' && um_user('auto_approve_url') !== '' ){
					exit( wp_redirect( um_user('auto_approve_url') ) );
				}
				if ( um_user('auto_approve_act') == 'redirect_profile' ){
				    exit( wp_redirect( um_user_profile_url() ) );
				}

			}

			if ( $status != 'approved' ) {

				if ( um_user( $status . '_action' ) == 'redirect_url' && um_user( $status . '_url' ) != '' ) {
					exit( wp_redirect( um_user( $status . '_url' ) ) );
				}

				if ( um_user( $status . '_action' ) == 'show_message' && um_user( $status . '_message' ) != '' ) {

					$role_id = $ultimatemember->user->get_role_name( um_user('role'), true );
					$url  = $ultimatemember->permalinks->get_current_url();
					$url  = add_query_arg( 'message', esc_attr( $status ), $url );
					$url  = add_query_arg( 'um_role', esc_attr( $role_id ), $url );
					$url  = add_query_arg( 'um_form_id', esc_attr( $form_id ), $url );

					exit( wp_redirect( $url ) );
				}

			}

		}

	}

	/**
	 * New user registration
	 */
	add_action('um_user_registration', 'um_user_registration', 10);
	function um_user_registration($args){
		global $ultimatemember;
         
        unset( $args['user_id'] );
		do_action('um_add_user_frontend', $args);

	}

	/**
	 * Form Processing
	 */
	add_action('um_submit_form_register', 'um_submit_form_register', 10);
	function um_submit_form_register($args){
		global $ultimatemember;

		if ( !isset($ultimatemember->form->errors) ) do_action('um_user_registration', $args);

		do_action('um_user_registration_extra_hook', $args );

	}

	/**
	 * Register user with predefined role in options
	 */
	add_action('um_after_register_fields', 'um_add_user_role');
	function um_add_user_role( $args ){

		global $ultimatemember;

		if ( isset( $args['custom_fields']['role_select'] ) || isset( $args['custom_fields']['role_radio'] ) ) return;

		$use_global_settings = get_post_meta( $args['form_id'], '_um_register_use_globals', true);
		
		if (isset($args['role']) && !empty($args['role']) && $use_global_settings == 0 ) {
			$role = $args['role'];
		} else if( $use_global_settings == 1 ) {
			$role = um_get_option('default_role');
		}

		if( empty( $role ) ) return;

		$role = apply_filters('um_register_hidden_role_field', $role );
		if( $role ){
			echo '<input type="hidden" name="role" id="role" value="' . $role . '" />';
		}

	}

	/**
	 * Show the submit button 
	 */
	add_action('um_after_register_fields', 'um_add_submit_button_to_register', 1000);
	function um_add_submit_button_to_register($args){
		global $ultimatemember;

		// DO NOT add when reviewing user's details
		if ( isset( $ultimatemember->user->preview ) && $ultimatemember->user->preview == true && is_admin() ) return;

		$primary_btn_word = $args['primary_btn_word'];
		$primary_btn_word = apply_filters('um_register_form_button_one', $primary_btn_word, $args );

		$secondary_btn_word = $args['secondary_btn_word'];
		$secondary_btn_word = apply_filters('um_register_form_button_two', $secondary_btn_word, $args );

		$secondary_btn_url = ( isset( $args['secondary_btn_url'] ) && $args['secondary_btn_url'] ) ? $args['secondary_btn_url'] : um_get_core_page('login');
		$secondary_btn_url = apply_filters('um_register_form_button_two_url', $secondary_btn_url, $args );

		?>

		<div class="um-col-alt">

			<?php if ( isset($args['secondary_btn']) && $args['secondary_btn'] != 0 ) { ?>

			<div class="um-left um-half"><input type="submit"  value="<?php echo __( $primary_btn_word,'ultimate-member'); ?>" class="um-button" id="um-submit-btn" /></div>
			<div class="um-right um-half"><a href="<?php echo $secondary_btn_url; ?>" class="um-button um-alt"><?php echo __( $secondary_btn_word,'ultimate-member'); ?></a></div>

			<?php } else { ?>

			<div class="um-center"><input type="submit" value="<?php echo __( $primary_btn_word,'ultimate-member'); ?>" class="um-button" id="um-submit-btn" /></div>

			<?php } ?>

			<div class="um-clear"></div>

		</div>

		<?php
	}

	/**
	 * Show Fields
	 */
	add_action('um_main_register_fields', 'um_add_register_fields', 100);
	function um_add_register_fields($args){
		global $ultimatemember;

		echo $ultimatemember->fields->display( 'register', $args );

	}

	/**
	 * Set user gravatar with user_email
	 */
	add_action('user_register','um_user_register_generate_gravatar');
	function um_user_register_generate_gravatar( $user_id ){
		global $ultimatemember;

		$ultimatemember->user->set_gravatar( $user_id );

	}
