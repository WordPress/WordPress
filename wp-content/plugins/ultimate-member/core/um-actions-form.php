<?php

	/***
	***	@Error handling: Registering
	***/
	add_action('um_submit_form_errors_hook__registration', 'um_submit_form_errors_hook__registration', 10);
	function um_submit_form_errors_hook__registration($args){
		global $ultimatemember;

		$can_register = get_option('users_can_register');
		if ( !$can_register ) {
			exit( wp_redirect( esc_url( add_query_arg('err', 'registration_disabled') ) ) );
		}

	}

	/***
	***	@Error handling: blocked emails
	***/
	add_action('um_submit_form_errors_hook__blockedemails', 'um_submit_form_errors_hook__blockedemails', 10);
	function um_submit_form_errors_hook__blockedemails($args){
		global $ultimatemember;

		$emails = um_get_option('blocked_emails');
		if ( !$emails )
			return;

		$emails = array_map("rtrim", explode("\n", $emails));

		if ( isset( $args['user_email'] ) && is_email( $args['user_email'] ) ) {

			$domain = explode('@', $args['user_email'] );
			$check_domain = str_replace($domain[0], '*', $args['user_email']);

			if ( in_array( $args['user_email'], $emails ) )
				exit( wp_redirect( esc_url(  add_query_arg('err', 'blocked_email') ) ) );

			if ( in_array( $check_domain, $emails ) )
				exit( wp_redirect( esc_url(  add_query_arg('err', 'blocked_domain') ) ) );

		}

		if ( isset( $args['username'] ) && is_email( $args['username'] ) ) {

			$domain = explode('@', $args['username'] );
			$check_domain = str_replace($domain[0], '*', $args['username']);

			if ( in_array( $args['username'], $emails ) )
				exit( wp_redirect(  esc_url( add_query_arg('err', 'blocked_email') ) ) );

			if ( in_array( $check_domain, $emails ) )
				exit( wp_redirect(  esc_url(  add_query_arg('err', 'blocked_domain') ) ) );

		}

	}

	/***
	***	@Error handling: blocked IPs
	***/
	add_action('um_submit_form_errors_hook__blockedips', 'um_submit_form_errors_hook__blockedips', 10);
	function um_submit_form_errors_hook__blockedips($args){
		global $ultimatemember;

		$ips = um_get_option('blocked_ips');
		if ( !$ips )
			return;

		$ips = array_map("rtrim", explode("\n", $ips));
		$user_ip = um_user_ip();

		foreach($ips as $ip) {
			$ip = str_replace('*','',$ip); 
			if ( !empty( $ip ) && strpos($user_ip, $ip) === 0) {
				exit( wp_redirect(  esc_url(  add_query_arg('err', 'blocked_ip') ) ) );
			}
		}
	}

	/***
	***	@Error handling: blocked words during sign up
	***/
	add_action('um_submit_form_errors_hook__blockedwords', 'um_submit_form_errors_hook__blockedwords', 10);
	function um_submit_form_errors_hook__blockedwords($args){
		global $ultimatemember;

		$form_id = $args['form_id'];
		$mode = $args['mode'];
		$fields = unserialize( $args['custom_fields'] );

		$words = um_get_option('blocked_words');
		if ( $words != '' ) {

			$words = array_map("rtrim", explode("\n", $words));
			if( isset( $fields ) && ! empty( $fields ) && is_array( $fields ) ){
				foreach( $fields as $key => $array ) {
					if ( isset($array['validate']) && in_array( $array['validate'], array('unique_username','unique_email','unique_username_or_email') ) ) {
						if ( !$ultimatemember->form->has_error( $key ) && isset( $args[$key] ) && in_array( $args[$key], $words ) ) {
							$ultimatemember->form->add_error( $key,  __('You are not allowed to use this word as your username.','ultimate-member') );
						}
					}
				}
			}

		}

	}

	/***
	***	@Error handling
	***/
	add_action('um_submit_form_errors_hook', 'um_submit_form_errors_hook', 10);
	function um_submit_form_errors_hook( $args ){
		global $ultimatemember;

		$form_id = $args['form_id'];

		$mode = $args['mode'];

		$fields = unserialize( $args['custom_fields'] );

		if ( $mode == 'register' ){

			do_action("um_submit_form_errors_hook__registration", $args );

		}

		do_action("um_submit_form_errors_hook__blockedips", $args );
		do_action("um_submit_form_errors_hook__blockedemails", $args );

		if ( $mode == 'login' ) {

			do_action('um_submit_form_errors_hook_login', $args );
			do_action('um_submit_form_errors_hook_logincheck', $args );

		} else {

			do_action('um_submit_form_errors_hook_', $args );
			do_action("um_submit_form_errors_hook__blockedwords", $args );

		}

	}

	/***
	***	@Error processing hook : standard
	***/
	add_action('um_submit_form_errors_hook_', 'um_submit_form_errors_hook_', 10);
	function um_submit_form_errors_hook_( $args ){
		global $ultimatemember;

		$form_id = $args['form_id'];
		$mode = $args['mode'];
		$fields = unserialize( $args['custom_fields'] );
   		$um_profile_photo = um_profile('profile_photo');

	    if ( get_post_meta( $form_id, '_um_profile_photo_required', true ) && ( empty( $args['profile_photo'] ) && empty( $um_profile_photo ) ) ) {
	        $ultimatemember->form->add_error('profile_photo', sprintf(__('%s is required.','ultimate-member'), 'Profile Photo' ) );
	    }

	   if( isset(  $fields ) && ! empty(  $fields ) ){
			foreach( $fields as $key => $array ) {

				if( isset( $array['public']  ) &&  -2 == $array['public'] && isset(  $array['roles'] ) && ! empty( $array['roles'] ) && is_user_logged_in() ){
				
					 if( ! in_array( um_user('role') ,  $array['roles'] ) ){
					 	continue;
					 }
				}

				$array = apply_filters('um_get_custom_field_array', $array, $fields );
				
				if( isset( $array ['conditions'] ) && ! empty(  $array ['conditions'] )  ){ 
					
					foreach( $array ['conditions'] as $condition ){
						
						$visibility = $condition[0];
						$parent_key = $condition[1];
						$op = $condition[2];
						$parent_value = $condition[3];
						
						if( $visibility == 'hide' ){
							if( $op == 'equals to' ){

								if( $args[ $parent_key ] == $parent_value ){
										continue 2; 
								}
							
							}
						}

					}
					
				}

				if ( isset( $array['type'] ) && $array['type'] == 'checkbox' && isset( $array['required'] ) && $array['required'] == 1 && !isset( $args[$key] ) ) {
					$ultimatemember->form->add_error($key, sprintf(__('%s is required.','ultimate-member'), $array['title'] ) );
				}

                if ( defined('um_user_tags_path') && isset( $array['type'] ) && $array['type'] == 'user_tags' && isset( $array['required'] ) && $array['required'] == 1 && !isset( $args[$key] ) ) {
                    $ultimatemember->form->add_error($key, sprintf(__('%s is required.','ultimate-member'), $array['title'] ) );
                }

				if ( isset( $array['type'] ) && $array['type'] == 'radio' && isset( $array['required'] ) && $array['required'] == 1 && !isset( $args[$key] ) && !in_array($key, array('role_radio','role_select') ) ) {
					$ultimatemember->form->add_error($key, sprintf(__('%s is required.','ultimate-member'), $array['title'] ) );
				}

				if ( isset( $array['type'] ) && $array['type'] == 'multiselect' && isset( $array['required'] ) && $array['required'] == 1 && !isset( $args[$key] ) && !in_array($key, array('role_radio','role_select') ) ) {
					$ultimatemember->form->add_error($key, sprintf(__('%s is required.','ultimate-member'), $array['title'] ) );
				}

				if ( $key == 'role_select' || $key == 'role_radio' ) {
					if ( isset( $array['required'] ) && $array['required'] == 1 && ( !isset( $args['role'] ) || empty( $args['role'] ) ) ) {
						$ultimatemember->form->add_error('role', __('Please specify account type.','ultimate-member') );
					}
				}

				if ( isset( $args[$key] ) ) {

					if ( isset( $array['required'] ) && $array['required'] == 1 ) {
						if ( !isset($args[$key]) || $args[$key] == '' ) {
							$ultimatemember->form->add_error($key, sprintf( __('%s is required','ultimate-member'), $array['label'] ) );
						}
					}

					if ( isset( $array['max_words'] ) && $array['max_words'] > 0 ) {
						if ( str_word_count( $args[$key] ) > $array['max_words'] ) {
						$ultimatemember->form->add_error($key, sprintf(__('You are only allowed to enter a maximum of %s words','ultimate-member'), $array['max_words']) );
						}
					}

					if ( isset( $array['min_chars'] ) && $array['min_chars'] > 0 ) {
						if ( $args[$key] && strlen( utf8_decode( $args[$key] ) ) < $array['min_chars'] ) {
						$ultimatemember->form->add_error($key, sprintf(__('Your %s must contain at least %s characters','ultimate-member'), $array['label'], $array['min_chars']) );
						}
					}

					if ( isset( $array['max_chars'] ) && $array['max_chars'] > 0 ) {
						if ( $args[$key] && strlen( utf8_decode( $args[$key] ) ) > $array['max_chars'] ) {
						$ultimatemember->form->add_error($key, sprintf(__('Your %s must contain less than %s characters','ultimate-member'), $array['label'], $array['max_chars']) );
						}
					}
                     
                    $profile_show_html_bio = um_get_option('profile_show_html_bio');
					
					if(  $profile_show_html_bio == 1 && $key !== "description" ){
						if ( isset( $array['html'] ) && $array['html'] == 0 ) {
							if ( wp_strip_all_tags( $args[$key] ) != trim( $args[$key] ) ) {
								$ultimatemember->form->add_error($key, __('You can not use HTML tags here','ultimate-member') );
							}
						}
					}

					if ( isset( $array['force_good_pass'] ) && $array['force_good_pass'] == 1 ) {
						if ( !$ultimatemember->validation->strong_pass( $args[$key] ) ) {
						$ultimatemember->form->add_error($key, __('Your password must contain at least one lowercase letter, one capital letter and one number','ultimate-member') );
						}
					}

					if ( isset( $array['force_confirm_pass'] ) && $array['force_confirm_pass'] == 1 ) {
						if ( $args[ 'confirm_' . $key] == '' && !$ultimatemember->form->has_error($key) ) {
						$ultimatemember->form->add_error( 'confirm_' . $key , __('Please confirm your password','ultimate-member') );
						}
						if ( $args[ 'confirm_' . $key] != $args[$key] && !$ultimatemember->form->has_error($key) ) {
						$ultimatemember->form->add_error( 'confirm_' . $key , __('Your passwords do not match','ultimate-member') );
						}
					}

					if ( isset( $array['min_selections'] ) && $array['min_selections'] > 0 ) {
						if ( ( !isset($args[$key]) ) || ( isset( $args[$key] ) && is_array($args[$key]) && count( $args[$key] ) < $array['min_selections'] ) ) {
						$ultimatemember->form->add_error($key, sprintf(__('Please select at least %s choices','ultimate-member'), $array['min_selections'] ) );
						}
					}

					if ( isset( $array['max_selections'] ) && $array['max_selections'] > 0 ) {
						if ( isset( $args[$key] ) && is_array($args[$key]) && count( $args[$key] ) > $array['max_selections'] ) {
						$ultimatemember->form->add_error($key, sprintf(__('You can only select up to %s choices','ultimate-member'), $array['max_selections'] ) );
						}
					}

					if ( isset( $array['min'] ) && is_numeric( $args[ $key ] ) ) {
								if ( isset( $args[ $key ] )  && $args[ $key ] < $array['min'] ) {
									$ultimatemember->form->add_error( $key, sprintf(__('Minimum number limit is %s','ultimate-member'), $array['min'] ) );
									}
								}

					if ( isset( $array['max'] ) && is_numeric( $args[ $key ] )  ) {
								if ( isset( $args[ $key ] ) && $args[ $key ] > $array['max'] ) {
									$ultimatemember->form->add_error( $key, sprintf(__('Maximum number limit is %s','ultimate-member'), $array['max'] ) );
								}
					}

					if ( isset( $array['validate'] ) && !empty( $array['validate'] ) ) {

						switch( $array['validate'] ) {

							case 'custom':
								$custom = $array['custom_validate'];
								do_action("um_custom_field_validation_{$custom}", $key, $array, $args );
								break;

							case 'numeric':
								
								if ( $args[$key] && !is_numeric( $args[$key] ) ) {
									$ultimatemember->form->add_error($key, __('Please enter numbers only in this field','ultimate-member') );
								}

								break;

							case 'phone_number':
								if ( !$ultimatemember->validation->is_phone_number( $args[$key] ) ) {
									$ultimatemember->form->add_error($key, __('Please enter a valid phone number','ultimate-member') );
								}
								break;

							case 'youtube_url':
								if ( !$ultimatemember->validation->is_url( $args[$key], 'youtube.com' ) ) {
									$ultimatemember->form->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimate-member'), $array['label'] ) );
								}
								break;

							case 'soundcloud_url':
								if ( !$ultimatemember->validation->is_url( $args[$key], 'soundcloud.com' ) ) {
									$ultimatemember->form->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimate-member'), $array['label'] ) );
								}
								break;

							case 'facebook_url':
								if ( !$ultimatemember->validation->is_url( $args[$key], 'facebook.com' ) ) {
									$ultimatemember->form->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimate-member'), $array['label'] ) );
								}
								break;

							case 'twitter_url':
								if ( !$ultimatemember->validation->is_url( $args[$key], 'twitter.com' ) ) {
									$ultimatemember->form->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimate-member'), $array['label'] ) );
								}
								break;

							case 'instagram_url':
								if ( !$ultimatemember->validation->is_url( $args[$key], 'instagram.com' ) ) {
									$ultimatemember->form->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimate-member'), $array['label'] ) );
								}
								break;

							case 'google_url':
								if ( !$ultimatemember->validation->is_url( $args[$key], 'plus.google.com' ) ) {
									$ultimatemember->form->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimate-member'), $array['label'] ) );
								}
								break;

							case 'linkedin_url':
								if ( !$ultimatemember->validation->is_url( $args[$key], 'linkedin.com' ) ) {
									$ultimatemember->form->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimate-member'), $array['label'] ) );
								}
								break;

							case 'vk_url':
								if ( !$ultimatemember->validation->is_url( $args[$key], 'vk.com' ) ) {
									$ultimatemember->form->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimate-member'), $array['label'] ) );
								}
								break;

							case 'url':
								if ( !$ultimatemember->validation->is_url( $args[$key] ) ) {
									$ultimatemember->form->add_error($key, __('Please enter a valid URL','ultimate-member') );
								}
								break;

							case 'skype':
								if ( !$ultimatemember->validation->is_url( $args[$key], 'skype.com' ) ) {
									$ultimatemember->form->add_error($key, sprintf(__('Please enter a valid %s username or profile URL','ultimate-member'), $array['label'] ) );
								}
								break;

							case 'unique_username':

								if ( $args[$key] == '' ) {
									$ultimatemember->form->add_error($key, __('You must provide a username','ultimate-member') );
								} else if ( $mode == 'register' && username_exists( sanitize_user( $args[$key] ) ) ) {
									$ultimatemember->form->add_error($key, __('Your username is already taken','ultimate-member') );
								} else if ( is_email( $args[$key] ) ) {
									$ultimatemember->form->add_error($key, __('Username cannot be an email','ultimate-member') );
								} else if ( !$ultimatemember->validation->safe_username( $args[$key] ) ) {
									$ultimatemember->form->add_error($key, __('Your username contains invalid characters','ultimate-member') );
								}

								break;

							case 'unique_username_or_email':

								if ( $args[$key] == '' ) {
									$ultimatemember->form->add_error($key,  __('You must provide a username','ultimate-member') );
								} else if ( $mode == 'register' && username_exists( sanitize_user( $args[$key] ) ) ) {
									$ultimatemember->form->add_error($key, __('Your username is already taken','ultimate-member') );
								} else if ( $mode == 'register' && email_exists( $args[$key] ) ) {
									$ultimatemember->form->add_error($key,  __('This email is already linked to an existing account','ultimate-member') );
								} else if ( !$ultimatemember->validation->safe_username( $args[$key] ) ) {
									$ultimatemember->form->add_error($key,  __('Your username contains invalid characters','ultimate-member') );
								}

								break;

							case 'unique_email':

								$args[ $key ] = trim( $args[ $key ] );

								if ( in_array( $key, array('user_email') ) ) {

									if( ! isset( $args['user_id'] ) ){
										$args['user_id'] = um_get_requested_user();
									}

									$email_exists =  email_exists( $args[ $key ] );

									if ( $args[ $key ] == '' && in_array( $key, array('user_email') ) ) {
										$ultimatemember->form->add_error( $key, __('You must provide your email','ultimate-member') );
									} else if ( in_array( $mode, array('register') )  && $email_exists  ) {
										$ultimatemember->form->add_error($key, __('This email is already linked to an existing account','ultimate-member') );
									} else if ( in_array( $mode, array('profile') )  && $email_exists && $email_exists != $args['user_id']  ) {
										$ultimatemember->form->add_error( $key, __('This email is already linked to an existing account','ultimate-member') );
									} else if ( !is_email( $args[ $key ] ) ) {
										$ultimatemember->form->add_error( $key, __('This is not a valid email','ultimate-member') );
									} else if ( !$ultimatemember->validation->safe_username( $args[ $key ] ) ) {
										$ultimatemember->form->add_error( $key,  __('Your email contains invalid characters','ultimate-member') );
									}

								} else {

									if ( $args[ $key ] != '' && !is_email( $args[ $key ] ) ) {
										$ultimatemember->form->add_error( $key, __('This is not a valid email','ultimate-member') );
									} else if ( $args[ $key ] != '' && email_exists( $args[ $key ] ) ) {
										$ultimatemember->form->add_error($key, __('This email is already linked to an existing account','ultimate-member') );
									} else if ( $args[ $key ] != '' ) {
										
										$users = get_users('meta_value='.$args[ $key ]);

										foreach ( $users as $user ) {
											if( $user->ID != $args['user_id'] ){
												$ultimatemember->form->add_error( $key, __('This email is already linked to an existing account','ultimate-member') );
											}		
										}

										
									}

								}

								break;

							case 'unique_value':

								if ( $args[$key] != '' ) {

									$args_unique_meta = array(
										'meta_key' => $key,
										'meta_value' => $args[ $key ],
										'compare' => '=',
										'exclude' => array( $args['user_id'] ),
									);

									$meta_key_exists = get_users( $args_unique_meta );

									if( $meta_key_exists ){
									   $ultimatemember->form->add_error( $key , __('You must provide a unique value','ultimate-member') );
									}
								}
							break;
							
							case 'alphabetic':

								if ( $args[$key] != '' ) {

									if( ! ctype_alpha( str_replace(' ', '', $args[$key] ) ) ){
									   $ultimatemember->form->add_error( $key , __('You must provide alphabetic letters','ultimate-member') );
									}
								}
							break;

							case 'lowercase':

								if ( $args[$key] != '' ) {

									if( ! ctype_lower( str_replace(' ', '',$args[$key] ) ) ){
									   $ultimatemember->form->add_error( $key , __('You must provide lowercase letters.','ultimate-member') );
									}
								}

							break;

						}

					}

				}

				if ( isset( $args['description'] ) ) {
					
					$max_chars = um_get_option('profile_bio_maxchars');
					$profile_show_bio = um_get_option('profile_show_bio');

					if( $profile_show_bio ){
						if ( strlen( utf8_decode( $args['description'] ) ) > $max_chars && $max_chars  ) {
								$ultimatemember->form->add_error('description', sprintf(__('Your user description must contain less than %s characters','ultimate-member'), $max_chars ) );
						}
					}

				}

			} // end if ( isset in args array )
		}

	}
