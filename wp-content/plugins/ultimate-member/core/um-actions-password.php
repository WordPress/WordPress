<?php

	/***
	***	@process a new request
	***/
	add_action('um_reset_password_process_hook','um_reset_password_process_hook');
	function um_reset_password_process_hook( $args ) {
		global $ultimatemember;

		$user = null;
		
		foreach ( $_POST as $key => $val ) {
        	if( strstr( $key, "username_b") ){
        		$user = trim( $val );
        	}
        }

		if ( !is_email( $user ) ) {
			$data = get_user_by( 'login', $user );
			$user_email = $data->user_email;
		} else {
			$data = get_user_by( 'email', $user );
			$user_email = $user;
		}

		$ultimatemember->password->reset_request['user_id'] = $data->ID;
		$ultimatemember->password->reset_request['user_email'] = $user_email;

		um_fetch_user( $data->ID );

		$ultimatemember->user->password_reset();

		um_reset_user();

	}

	/***
	***	@process a change request
	***/
	add_action('um_change_password_process_hook','um_change_password_process_hook');
	function um_change_password_process_hook( $args ) {
		global $ultimatemember;
		extract(  $args );

		wp_set_password( $args['user_password'], $args['user_id'] );

		delete_user_meta( $args['user_id'], 'reset_pass_hash');
		delete_user_meta( $args['user_id'], 'reset_pass_hash_token');
		delete_user_meta( $args['user_id'], 'password_rst_attempts');

		do_action('um_after_changing_user_password', $args['user_id'] );


		if ( is_user_logged_in() ) {
			wp_logout();
		}

		exit( wp_redirect( um_get_core_page('login', 'password_changed') ) );

	}

	/**
	 * Overrides password changed notification
	 *
	 */
	add_action('send_password_change_email','um_send_password_change_email');
	function um_send_password_change_email( $args ){

		global $ultimatemember;
		extract(  $args );

		um_fetch_user( $user_id );

		$ultimatemember->user->password_changed();

		um_reset_user();


		return false;
	}

	/***
	***	@This is executed after changing password
	***/
	add_action('um_after_changing_user_password','um_after_changing_user_password');
	function um_after_changing_user_password( $user_id ) {
		global $ultimatemember;

	}

	/***
	***	@Error handler: reset password
	***/
	add_action('um_reset_password_errors_hook','um_reset_password_errors_hook');
	function um_reset_password_errors_hook( $args ) {
		global $ultimatemember;

		if ( $_POST[ $ultimatemember->honeypot ] != '' )
			wp_die('Hello, spam bot!','ultimate-member');

		$form_timestamp  = trim($_POST['timestamp']);
		$live_timestamp  = current_time( 'timestamp' );

		if ( $form_timestamp == '' && um_get_option('enable_timebot') == 1 )
			wp_die( __('Hello, spam bot!','ultimate-member') );

		if ( $live_timestamp - $form_timestamp < 3 && um_get_option('enable_timebot') == 1 )
			wp_die( __('Whoa, slow down! You\'re seeing this message because you tried to submit a form too fast and we think you might be a spam bot. If you are a real human being please wait a few seconds before submitting the form. Thanks!','ultimate-member') );
        
        $user = "";

        foreach ( $_POST as $key => $val ) {
        	if( strstr( $key, "username_b") ){
        		$user = trim( $val );
        	}
        }

		if ( empty( $user ) ) {
			$ultimatemember->form->add_error('username_b', __('Please provide your username or email','ultimate-member') );
		}

		if ( ( !is_email( $user ) && !username_exists( $user ) ) || ( is_email( $user ) && !email_exists( $user ) ) ) {
			$ultimatemember->form->add_error('username_b', __('We can\'t find an account registered with that address or username','ultimate-member') );
		} else {

			if ( is_email( $user ) ) {
				$user_id = email_exists( $user );
			} else {
				$user_id = username_exists( $user );
			}

			$attempts = (int)get_user_meta( $user_id, 'password_rst_attempts', true );
			$is_admin = user_can( intval( $user_id ),'manage_options' );

			if( um_get_option('enable_reset_password_limit') ){ // if reset password limit is set

				if(  um_get_option('disable_admin_reset_password_limit') &&  $is_admin ){
					// Triggers this when a user has admin capabilities and when reset password limit is disabled for admins
				}else{
					$limit = um_get_option('reset_password_limit_number');
					if ( $attempts >= $limit ) {
						$ultimatemember->form->add_error('username_b', __('You have reached the limit for requesting password change for this user already. Contact support if you cannot open the email','ultimate-member') );
					} else {
						update_user_meta( $user_id, 'password_rst_attempts', $attempts + 1 );
					}
				}

			}
		}

	}

	/***
	***	@Error handler: changing password
	***/
	add_action('um_change_password_errors_hook','um_change_password_errors_hook');
	function um_change_password_errors_hook( $args ) {
		global $ultimatemember;

		if ( isset(  $_POST[ $ultimatemember->honeypot ]  ) && $_POST[ $ultimatemember->honeypot ] != '' ){
			wp_die('Hello, spam bot!','ultimate-member');
		}

		$form_timestamp  = trim($_POST['timestamp']);
		$live_timestamp  = current_time( 'timestamp' );

		if ( $form_timestamp == '' && um_get_option('enable_timebot') == 1 )
			wp_die( __('Hello, spam bot!','ultimate-member') );

		if ( $live_timestamp - $form_timestamp < 3 && um_get_option('enable_timebot') == 1 ){
			wp_die( __('Whoa, slow down! You\'re seeing this message because you tried to submit a form too fast and we think you might be a spam bot. If you are a real human being please wait a few seconds before submitting the form. Thanks!','ultimate-member') );
		}

		$reset_pass_hash = '';

		if( isset( $_REQUEST['act'] ) && $_REQUEST['act']  == 'reset_password' && um_is_core_page('password-reset')  ){
			$reset_pass_hash = get_user_meta( $args['user_id'], 'reset_pass_hash', true );

		}

		if( !is_user_logged_in() && isset( $args ) && ! um_is_core_page('password-reset') ||  
			 is_user_logged_in() && isset( $args['user_id'] ) && $args['user_id'] != get_current_user_id() ||
			!is_user_logged_in() && isset( $_REQUEST['hash'] ) && $reset_pass_hash != $_REQUEST['hash'] && um_is_core_page('password-reset')   
		){
			wp_die( __( 'This is not possible for security reasons.','ultimate-member') );
		}

		if ( isset( $args['user_password'] ) && empty( $args['user_password'] ) ) {
			$ultimatemember->form->add_error('user_password', __('You must enter a new password','ultimate-member') );
		}

		if ( um_get_option('reset_require_strongpass') ) {

			if ( strlen( utf8_decode( $args['user_password'] ) ) < 8 ) {
				$ultimatemember->form->add_error('user_password', __('Your password must contain at least 8 characters','ultimate-member') );
			}

			if ( strlen( utf8_decode( $args['user_password'] ) ) > 30 ) {
				$ultimatemember->form->add_error('user_password', __('Your password must contain less than 30 characters','ultimate-member') );
			}

			if ( !$ultimatemember->validation->strong_pass( $args['user_password'] ) ) {
				$ultimatemember->form->add_error('user_password', __('Your password must contain at least one lowercase letter, one capital letter and one number','ultimate-member') );
			}

		}

		if ( isset( $args['confirm_user_password'] ) && empty( $args['confirm_user_password'] ) ) {
			$ultimatemember->form->add_error('confirm_user_password', __('You must confirm your new password','ultimate-member') );
		}

		if ( isset( $args['user_password'] ) && isset( $args['confirm_user_password'] ) && $args['user_password'] != $args['confirm_user_password'] ) {
			$ultimatemember->form->add_error('confirm_user_password', __('Your passwords do not match','ultimate-member') );
		}

	}

	/***
	***	@hidden fields
	***/
	add_action('um_change_password_page_hidden_fields','um_change_password_page_hidden_fields');
	function um_change_password_page_hidden_fields( $args ) {

		?>

		<input type="hidden" name="_um_password_change" id="_um_password_change" value="1" />

		<input type="hidden" name="user_id" id="user_id" value="<?php echo $args['user_id']; ?>" />

		<?php

	}

	/***
	***	@hidden fields
	***/
	add_action('um_reset_password_page_hidden_fields','um_reset_password_page_hidden_fields');
	function um_reset_password_page_hidden_fields( $args ) {

		?>

		<input type="hidden" name="_um_password_reset" id="_um_password_reset" value="1" />

		<?php

	}

	/***
	***	@form content
	***/
	add_action('um_reset_password_form', 'um_reset_password_form');
	function um_reset_password_form($args) {

		global $ultimatemember;

		$fields = $ultimatemember->builtin->get_specific_fields('password_reset_text,username_b'); ?>

		<?php $output = null;
		foreach( $fields as $key => $data ) {
			$output .= $ultimatemember->fields->edit_field( $key, $data );
		}echo $output; ?>

		<?php do_action( 'um_after_password_reset_fields', $args ); ?>

		<div class="um-col-alt um-col-alt-b">

			<div class="um-center"><input type="submit" value="<?php _e('Reset my password','ultimate-member'); ?>" class="um-button" id="um-submit-btn" /></div>

			<div class="um-clear"></div>

		</div>

		<?php

	}

	/***
	***	@change password form
	***/
	add_action('um_change_password_form', 'um_change_password_form');
	function um_change_password_form() {

		global $ultimatemember;

		$fields = $ultimatemember->builtin->get_specific_fields('user_password'); ?>

		<?php $output = null;
		foreach( $fields as $key => $data ) {
			$output .= $ultimatemember->fields->edit_field( $key, $data );
		}echo $output; ?>

		<div class="um-col-alt um-col-alt-b">

			<div class="um-center"><input type="submit" value="<?php _e('Change my password','ultimate-member'); ?>" class="um-button" id="um-submit-btn"  /></div>

			<div class="um-clear"></div>

		</div>

		<?php

	}
