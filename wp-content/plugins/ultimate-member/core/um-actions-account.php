<?php

	/**
	 * Submit account page changes
	 */
	add_action('um_submit_account_details','um_submit_account_details');
	function um_submit_account_details( $args ) {
		global $ultimatemember;

		$tab = ( get_query_var('um_tab') ) ? get_query_var('um_tab') : 'general';
		
		if ( $_POST['user_password'] && $_POST['confirm_user_password'] ) {
			$changes['user_pass'] = $_POST['user_password'];

			$args['id'] = um_user('ID');
			
			do_action('send_password_change_email', $args );
			
			wp_set_password( $changes['user_pass'], um_user('ID') );
			
			wp_signon( array('user_login' => um_user('user_login'), 'user_password' =>  $changes['user_pass']) );
		}

		$arr_fields = array();
		$account_fields =  get_user_meta( um_user('ID'), 'um_account_secure_fields', true );
		$secure_fields = apply_filters('um_secure_account_fields', $account_fields , um_user('ID') );
		
		if( isset( $secure_fields  ) ){
			foreach ( $secure_fields as $tab_key => $fields ) {
				if( isset( $fields ) ){
					foreach ($fields as $key => $value) {
						$arr_fields[ ] = $key;
					}
				}
			}
		}

 
        $changes = array();
		foreach( $_POST as $k => $v ) {
			if ( !strstr( $k, 'password' ) && !strstr( $k, 'um_account' ) && in_array(  $k, $arr_fields ) ) {
				$changes[ $k ] = $v;
			}
		} 

		if ( isset( $changes['hide_in_members'] ) && $changes['hide_in_members'] == __('No','ultimate-member') ) {
			delete_user_meta( um_user('ID'), 'hide_in_members' );
			unset( $changes['hide_in_members'] );
		}
       
       // fired on account page, just before updating profile
		do_action('um_account_pre_update_profile', $changes, um_user('ID') );

		$ultimatemember->user->update_profile( $changes );
       	
       	// delete account
       	$user = get_user_by('login', um_user('user_login') );
      	$current_tab = isset( $_POST['_um_account_tab'] ) ? $_POST['_um_account_tab']: '';
			
		if (  isset( $_POST['single_user_password'] ) && wp_check_password( $_POST['single_user_password'], $user->data->user_pass, $user->data->ID )  && $current_tab == 'delete' ) {
			if ( current_user_can('delete_users') || um_user('can_delete_profile') ) {
				if ( !um_user('super_admin') ) {
					$ultimatemember->user->delete();
					if ( um_user('after_delete') && um_user('after_delete') == 'redirect_home' ) {
						um_redirect_home();
					} elseif ( um_user('delete_redirect_url') ) {
						exit( wp_redirect( um_user('delete_redirect_url') ) );
					} else {
						um_redirect_home();
					}
				}
			}
		}

		do_action('um_post_account_update');

		do_action('um_after_user_account_updated', get_current_user_id() );

		$url = $ultimatemember->account->tab_link( $tab );

		$url = add_query_arg( 'updated', 'account', $url );

		if ( function_exists('icl_get_current_language') ) {
			if ( icl_get_current_language() != icl_get_default_language() ) {
				$url = $ultimatemember->permalinks->get_current_url( true );
				$url = add_query_arg( 'updated', 'account', $url );
				exit( wp_redirect( $url ) );
			}
		}

		exit( wp_redirect( $url ) );

	}

	/**
	 * Validate for errors in account form
	 */
	add_action('um_submit_account_errors_hook','um_submit_account_errors_hook');
	function um_submit_account_errors_hook( $args ) {
		global $ultimatemember;
		
		$current_tab = isset( $_POST['_um_account_tab'] ) ? $_POST['_um_account_tab']: '';
		$user = get_user_by('login', um_user('user_login') );
					
		if( isset( $_POST['_um_account_tab'] ) && $current_tab != "delete"  ){
			// errors on general tab
			if ( isset($_POST['um_account_submit'])   ) {
				
				if( $current_tab != 'password' ){
					
					$account_name_require = um_get_option("account_name_require");

					if ( isset($_POST['first_name']) && ( strlen(trim( $_POST['first_name'] ) ) == 0 && $account_name_require ) ) {
						$ultimatemember->form->add_error('first_name', __('You must provide your first name','ultimate-member') );
					}

					if ( isset($_POST['last_name']) && ( strlen(trim( $_POST['last_name'] ) ) == 0 && $account_name_require ) ) {
						$ultimatemember->form->add_error('last_name', __('You must provide your last name','ultimate-member') );
					}

					if ( isset($_POST['user_email']) && strlen(trim( $_POST['user_email'] ) ) == 0 ) {
						$ultimatemember->form->add_error('user_email', __('You must provide your e-mail','ultimate-member') );
					}

					if ( isset($_POST['user_email']) && !is_email( $_POST['user_email'] ) ) {
						$ultimatemember->form->add_error('user_email', __('Please provide a valid e-mail','ultimate-member') );
					}

					if ( email_exists( $_POST['user_email'] ) && email_exists( $_POST['user_email'] ) != get_current_user_id() ) {
						$ultimatemember->form->add_error('user_email', __('Email already linked to another account','ultimate-member') );
					}
				}

			}
			$ultimatemember->account->current_tab = 'general';

			// change password
			if ( ( isset( $_POST['current_user_password'] ) && $_POST['current_user_password'] != '' ) ||
				 ( isset( $_POST['user_password'] ) && $_POST['user_password'] != '' ) || 
				 ( isset( $_POST['confirm_user_password'] ) && $_POST['confirm_user_password'] != '') ) {

				if ( $_POST['current_user_password'] == '' || ! wp_check_password( $_POST['current_user_password'], $user->data->user_pass, $user->data->ID ) ) {

					$ultimatemember->form->add_error('current_user_password', __('This is not your password','ultimate-member') );
					$ultimatemember->account->current_tab = 'password';
				} else { // correct password

					if ( $_POST['user_password'] != $_POST['confirm_user_password'] && $_POST['user_password'] ) {
						$ultimatemember->form->add_error('user_password', __('Your new password does not match','ultimate-member') );
						$ultimatemember->account->current_tab = 'password';
					}

					if ( um_get_option('account_require_strongpass') ) {

						if ( strlen( utf8_decode( $_POST['user_password'] ) ) < 8 ) {
							$ultimatemember->form->add_error('user_password', __('Your password must contain at least 8 characters','ultimate-member') );
						}

						if ( strlen( utf8_decode( $_POST['user_password'] ) ) > 30 ) {
							$ultimatemember->form->add_error('user_password', __('Your password must contain less than 30 characters','ultimate-member') );
						}

						if ( !$ultimatemember->validation->strong_pass( $_POST['user_password'] ) ) {
							$ultimatemember->form->add_error('user_password', __('Your password must contain at least one lowercase letter, one capital letter and one number','ultimate-member') );
							$ultimatemember->account->current_tab = 'password';
						}

					}

				}
			}

			if ( ! empty( $_POST['user_login'] ) && ! validate_username( $_POST['user_login'] ) ) {
				$ultimatemember->form->add_error('user_login', __('Your username is invalid','ultimate-member') );
				return;
			}
		}
		// delete account
		if ( isset( $_POST['um_account_submit'] ) && $_POST['_um_account_tab'] == "delete" ) {
			if ( strlen(trim( $_POST['single_user_password'] ) ) == 0 ) {
					$ultimatemember->form->add_error('single_user_password', __('You must enter your password','ultimate-member') );
			} else {
				if (  ! wp_check_password( $_POST['single_user_password'], $user->data->user_pass, $user->data->ID ) ) {
					$ultimatemember->form->add_error('single_user_password', __('This is not your password','ultimate-member') );
				}
			}
				
			$ultimatemember->account->current_tab = 'delete';
		}

	}

	/**
	 * Hidden inputs for account form
	 */
	add_action('um_account_page_hidden_fields','um_account_page_hidden_fields');
	function um_account_page_hidden_fields( $args ) {
		global $ultimatemember;
		?>

		<input type="hidden" name="_um_account" id="_um_account" value="1" />

		<?php $current_tab = $ultimatemember->account->current_tab; ?>

		<input type="hidden" name="_um_account_tab" id="_um_account_tab" value="<?php echo $current_tab;?>" />

		<?php

	}

	/**
	 * Display "Delete" tab
	 */
	add_action('um_account_tab__delete', 'um_account_tab__delete');
	function um_account_tab__delete( $info ) {
		global $ultimatemember;
		extract( $info );

		$output = $ultimatemember->account->get_tab_output('delete');

		if ( $output ) { ?>

		<div class="um-account-heading uimob340-hide uimob500-hide"><i class="<?php echo $icon; ?>"></i><?php echo $title; ?></div>

		<?php echo wpautop( um_get_option('delete_account_text') ); ?>

		<?php echo $output; ?>

		<?php do_action('um_after_account_delete'); ?>

		<div class="um-col-alt um-col-alt-b">
			<div class="um-left"><input type="submit" name="um_account_submit" id="um_account_submit" value="<?php _e('Delete Account','ultimate-member'); ?>" class="um-button" /></div>
			<?php do_action('um_after_account_delete_button'); ?>
			<div class="um-clear"></div>
		</div>

		<?php

		}

	}

	/**
	 * Display "Privacy" tab
	 */
	add_action('um_account_tab__privacy', 'um_account_tab__privacy');
	function um_account_tab__privacy( $info ) {
		global $ultimatemember;
		extract( $info );

		$output = $ultimatemember->account->get_tab_output('privacy');

		if ( $output ) { ?>

		<div class="um-account-heading uimob340-hide uimob500-hide"><i class="<?php echo $icon; ?>"></i><?php echo $title; ?></div>

		<?php echo $output; ?>

		<?php do_action('um_after_account_privacy'); ?>

		<div class="um-col-alt um-col-alt-b">
			<div class="um-left"><input type="submit" name="um_account_submit" id="um_account_submit" value="<?php _e('Update Privacy','ultimate-member'); ?>" class="um-button" /></div>
			<?php do_action('um_after_account_privacy_button'); ?>
			<div class="um-clear"></div>
		</div>

		<?php

		}

	}

	/**
	 * Display "General" tab
	 */
	add_action('um_account_tab__general', 'um_account_tab__general');
	function um_account_tab__general( $info ) {
		global $ultimatemember;
		extract( $info );

		$output = $ultimatemember->account->get_tab_output('general');

		if ( $output ) { ?>

		<div class="um-account-heading uimob340-hide uimob500-hide"><i class="<?php echo $icon; ?>"></i><?php echo $title; ?></div>

		<?php echo $output; ?>

		<?php do_action('um_after_account_general'); ?>

		<div class="um-col-alt um-col-alt-b">
			<div class="um-left"><input type="submit" name="um_account_submit" id="um_account_submit" value="<?php _e('Update Account','ultimate-member'); ?>" class="um-button" /></div>
			<?php do_action('um_after_account_general_button'); ?>
			<div class="um-clear"></div>
		</div>

		<?php

		}

	}

	/**
	 * Display "Password" tab
	 */
	add_action('um_account_tab__password', 'um_account_tab__password');
	function um_account_tab__password( $info ) {
		global $ultimatemember;
		extract( $info );

		$output = $ultimatemember->account->get_tab_output('password');

		if ( $output ) { ?>

		<div class="um-account-heading uimob340-hide uimob500-hide"><i class="<?php echo $icon; ?>"></i><?php echo $title; ?></div>

		<?php echo $output; ?>

		<?php do_action('um_after_account_password'); ?>

		<div class="um-col-alt um-col-alt-b">
			<div class="um-left"><input type="submit" name="um_account_submit" id="um_account_submit" value="<?php _e('Update Password','ultimate-member'); ?>" class="um-button" /></div>
			<?php do_action('um_after_account_password_button'); ?>
			<div class="um-clear"></div>
		</div>

		<?php

		}

	}

	/**
	 * Display "Notifications" tab
	 */
	add_action('um_account_tab__notifications', 'um_account_tab__notifications');
	function um_account_tab__notifications( $info ) {
		global $ultimatemember;
		extract( $info );

		$output = $ultimatemember->account->get_tab_output('notifications');

		if ( $output ) { ?>

		<div class="um-account-heading uimob340-hide uimob500-hide"><i class="<?php echo $icon; ?>"></i><?php echo $title; ?></div>

		<?php if ( class_exists('UM_Messaging_API') || class_exists('UM_Followers_API') ) { ?>
		<div class="um-field">
			<div class="um-field-label"><label for=""><?php _e('Email me when','ultimate-member'); ?></label><div class="um-clear"></div></div>
		</div>
		<?php } ?>

		<?php echo $output; ?>

		<?php do_action('um_after_account_notifications'); ?>

		<div class="um-col-alt um-col-alt-b">
			<div class="um-left"><input type="submit" name="um_account_submit" id="um_account_submit" value="<?php _e('Update Notifications','ultimate-member'); ?>" class="um-button" /></div>
			<?php do_action('um_after_account_notifications_button'); ?>
			<div class="um-clear"></div>
		</div>

		<?php

		}

	}

	/**
	 * Display account photo and username in mobile
	 */
	add_action('um_account_user_photo_hook__mobile', 'um_account_user_photo_hook__mobile');
	function um_account_user_photo_hook__mobile( $args ) {
		global $ultimatemember;
		extract( $args );

		?>

		<div class="um-account-meta radius-<?php echo um_get_option('profile_photocorner'); ?> uimob340-show uimob500-show">

			<div class="um-account-meta-img"><a href="<?php echo um_user_profile_url(); ?>"><?php echo get_avatar( um_user('ID'), 120); ?></a></div>

			<div class="um-account-name">
				<a href="<?php echo um_user_profile_url(); ?>"><?php echo um_user('display_name'); ?></a>
				<div class="um-account-profile-link"><a href="<?php echo um_user_profile_url(); ?>" class="um-link"><?php _e('View profile','ultimate-member'); ?></a></div>
			</div>

		</div>

		<?php

	}

	/**
	 * Display account photo and username
	 */
	add_action('um_account_user_photo_hook', 'um_account_user_photo_hook');
	function um_account_user_photo_hook( $args ) {
		global $ultimatemember;
		extract( $args );

		?>

		<div class="um-account-meta radius-<?php echo um_get_option('profile_photocorner'); ?>">

			<div class="um-account-meta-img uimob800-hide"><a href="<?php echo um_user_profile_url(); ?>"><?php echo get_avatar( um_user('ID'), 120); ?></a></div>

			<?php if ( $ultimatemember->mobile->isMobile() ) { ?>

			<div class="um-account-meta-img-b uimob800-show" title="<?php echo um_user('display_name'); ?>"><a href="<?php echo um_user_profile_url(); ?>"><?php echo get_avatar( um_user('ID'), 120); ?></a></div>

			<?php } else { ?>

			<div class="um-account-meta-img-b uimob800-show um-tip-w" title="<?php echo um_user('display_name'); ?>"><a href="<?php echo um_user_profile_url(); ?>"><?php echo get_avatar( um_user('ID'), 120); ?></a></div>

			<?php } ?>

			<div class="um-account-name uimob800-hide">
				<a href="<?php echo um_user_profile_url(); ?>"><?php echo um_user('display_name', 'html'); ?></a>
				<div class="um-account-profile-link"><a href="<?php echo um_user_profile_url(); ?>" class="um-link"><?php _e('View profile','ultimate-member'); ?></a></div>
			</div>

		</div>

		<?php

	}

	/**
	 * Display account page tabs
	 */
	add_action('um_account_display_tabs_hook', 'um_account_display_tabs_hook');
	function um_account_display_tabs_hook( $args ) {
		global $ultimatemember;
		extract( $args );

		$ultimatemember->account->tabs = apply_filters('um_account_page_default_tabs_hook', $tabs=array() );

		ksort( $ultimatemember->account->tabs  );

		?>

			<ul>

				<?php

				foreach( $ultimatemember->account->tabs as $k => $arr ) {
					foreach( $arr as $id => $info ) { extract( $info );

						$current_tab = $ultimatemember->account->current_tab;

						if ( isset($info['custom']) || um_get_option('account_tab_'.$id ) == 1 || $id == 'general' ) { ?>

				<li>
					<a data-tab="<?php echo $id; ?>" href="<?php echo $ultimatemember->account->tab_link($id); ?>" class="um-account-link <?php if ( $id == $current_tab ) echo 'current'; ?>">

						<?php if ( $ultimatemember->mobile->isMobile() ) { ?>
						<span class="um-account-icontip uimob800-show" title="<?php echo $title; ?>"><i class="<?php echo $icon; ?>"></i></span>
						<?php } else { ?>
						<span class="um-account-icontip uimob800-show um-tip-w" title="<?php echo $title; ?>"><i class="<?php echo $icon; ?>"></i></span>
						<?php } ?>

						<span class="um-account-icon uimob800-hide"><i class="<?php echo $icon; ?>"></i></span>
						<span class="um-account-title uimob800-hide"><?php echo $title; ?></span>
						<span class="um-account-arrow uimob800-hide"><?php echo ( is_rtl() ) ? '<i class="um-faicon-angle-left"></i>' : '<i class="um-faicon-angle-right"></i>'; ?></span>
					</a>
				</li>

				<?php

						}
					}
				}

				?>

			</ul>

		<?php

	}

	/**
	 *  Update account fields to secure the account submission
	 */
	add_action('wp_footer','um_account_secure_registered_fields');
	function um_account_secure_registered_fields(){
		global $ultimatemember;

		$secure_fields = $ultimatemember->account->register_fields;
		update_user_meta( um_user('ID'), 'um_account_secure_fields', $secure_fields );

	}
