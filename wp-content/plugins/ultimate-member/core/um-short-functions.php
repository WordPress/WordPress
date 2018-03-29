<?php

	function um_mail_content_type( $content_type ) {
		
		return 'text/html';
	}

	function UM_Mail( $user_id_or_email = 1, $subject_line = 'Email Subject', $template, $path = null, $args = array() ) {

		if ( absint( $user_id_or_email ) ) {
			$user = get_userdata( $user_id_or_email );
			$email = $user->user_email;
		} else {
			$email = $user_id_or_email;
		}

		$headers = 'From: '. um_get_option('mail_from') .' <'. um_get_option('mail_from_addr') .'>' . "\r\n";
		$attachments = null;

		if ( file_exists( get_stylesheet_directory() . '/ultimate-member/templates/email/' . get_locale() . '/' . $template . '.html' ) ) {
			$path_to_email = get_stylesheet_directory() . '/ultimate-member/templates/email/' . get_locale() . '/' . $template . '.html';
		} else if ( file_exists( get_stylesheet_directory() . '/ultimate-member/templates/email/' . $template . '.html' ) ) {
			$path_to_email = get_stylesheet_directory() . '/ultimate-member/templates/email/' . $template . '.html';
		} else {
			$path_to_email = $path . $template . '.html';
		}

		if ( um_get_option('email_html') ) {
			$message = file_get_contents( $path_to_email );
			add_filter( 'wp_mail_content_type', 'um_mail_content_type' );
		} else {
			$message = ( um_get_option('email-' . $template ) ) ? um_get_option('email-' . $template ) : 'Untitled';
		}

		$message = um_convert_tags( $message, $args );
		wp_mail( $email, $subject_line, $message, $headers, $attachments );
	}

	/***
	***	@Trim string by char length
	***/
	function um_trim_string( $s, $length = 20 ) {
		$s = strlen($s) > $length ? substr($s,0,$length)."..." : $s;
		return $s;
	}

	/***
	***	@Convert urls to clickable links
	***/
	function um_clickable_links($s) {
	
		return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" class="um-link" target="_blank">$1</a>', $s);
	}

	/***
	***	@Get where user should be headed after logging
	***/
	function um_dynamic_login_page_redirect( $redirect_to = '' ) {

		global $ultimatemember;

		$uri = um_get_core_page( 'login' );

		if ( ! $redirect_to ) {
			$redirect_to = $ultimatemember->permalinks->get_current_url();
		}

		$redirect_key = urlencode_deep( $redirect_to );

		$uri = add_query_arg( 'redirect_to', $redirect_key, $uri );

		return $uri;
	}

	/**
	 * Set redirect key
	 * @param  string $url
	 * @return string $redirect_key
	 */
	function um_set_redirect_url( $url ){

		if( um_is_session_started() === FALSE ){
				session_start();
		}

		$redirect_key = wp_generate_password(12,false);

		$_SESSION['um_redirect_key'] = array( $redirect_key => $url );

		return $redirect_key;
	}

	/**
	 * Set redirect key
	 * @param  string $url
	 * @return string $redirect_key
	 */
	function um_get_redirect_url( $key ){

		if( um_is_session_started() === FALSE ){
				session_start();
		}

		if( isset( $_SESSION['um_redirect_key'][ $key ] ) ){

			$url = $_SESSION['um_redirect_key'][ $key ];

			return $url;

		}else{

			if( isset( $_SESSION['um_redirect_key'] ) ){
				foreach ( $_SESSION['um_redirect_key'] as $key => $url ) {

					return $url;

					break;
				}
			}
		}

		return;
	}


	/**
	 * Checks if session has been started
	 * @return bool
	*/
	function um_is_session_started(){

		if ( php_sapi_name() !== 'cli' ) {
		        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
		            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
		        } else {
		            return session_id() === '' ? FALSE : TRUE;
		        }
		}

		return FALSE;
	}


	/***
	*** @user clean basename
	***/
	function um_clean_user_basename( $value ) {

		$raw_value = $value;
		$value = str_replace('.', ' ', $value);
		$value = str_replace('-', ' ', $value);
		$value = str_replace('+', ' ', $value);

		$value = apply_filters('um_clean_user_basename_filter', $value, $raw_value );

		return $value;
	}
	/***
	***	@convert template tags
	***/
	function um_convert_tags( $content, $args = array() ) {

		$search = array(
			'{display_name}',
			'{first_name}',
			'{last_name}',
			'{gender}',
			'{username}',
			'{email}',
			'{password}',
			'{login_url}',
			'{login_referrer}',
			'{site_name}',
			'{site_url}',
			'{account_activation_link}',
			'{password_reset_link}',
			'{admin_email}',
			'{user_profile_link}',
			'{user_account_link}',
			'{submitted_registration}',
			'{user_avatar_url}',
		);

		$search = apply_filters('um_template_tags_patterns_hook', $search);

		$replace = array(
			um_user('display_name'),
			um_user('first_name'),
			um_user('last_name'),
			um_user('gender'),
			um_user('user_login'),
			um_user('user_email'),
			um_user('_um_cool_but_hard_to_guess_plain_pw'),
			um_get_core_page('login'),
			um_dynamic_login_page_redirect(),
			um_get_option('site_name'),
			get_bloginfo('url'),
			um_user('account_activation_link'),
			um_user('password_reset_link'),
			um_admin_email(),
			um_user_profile_url(),
			um_get_core_page('account'),
			um_user_submitted_registration(),
			um_get_user_avatar_url(),
		);

		$replace = apply_filters('um_template_tags_replaces_hook', $replace);
        
        $content = wp_kses_decode_entities( str_replace($search, $replace, $content) );

		if ( isset( $args['tags'] ) && isset( $args['tags_replace'] ) ) {
			$content = str_replace($args['tags'], $args['tags_replace'], $content);
		}

		$regex = '~\{([^}]*)\}~';
		preg_match_all($regex, $content, $matches);

		// Support for all usermeta keys
		if ( isset( $matches[1] ) && is_array( $matches[1] ) && !empty( $matches[1] ) ) {
			foreach( $matches[1] as $match ) {
				$strip_key = str_replace('usermeta:','', $match );
				$content = str_replace( '{' . $match . '}', um_user( $strip_key ), $content);
			}
		}

		return $content;

	}

	/**
	 * @function um_user_ip()
	 *
	 * @description This function returns the IP address of user.
	 *
	 * @usage <?php $user_ip = um_user_ip(); ?>
	 *
	 * @returns Returns the user's IP address.
	 *
	 * @example The example below can retrieve the user's IP address

		<?php

			$user_ip = um_user_ip();
			echo 'User IP address is: ' . $user_ip; // prints the user IP address e.g. 127.0.0.1

		?>

	 *
	 *
	 */
	function um_user_ip() {
		$ip = '127.0.0.1';

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return apply_filters( 'um_user_ip', $ip );
	}

	/***
	***	@If conditions are met return true;
	***/
	function um_field_conditions_are_met( $data ) {
		if ( !isset( $data['conditions'] ) ) return true;

		$state = 1;
	   
		foreach( $data['conditions'] as $k => $arr ) {
			if ( $arr[0] == 'show' ) {

				$val = $arr[3];
				$op = $arr[2];

				if( strstr( $arr[1] , 'role_') ){
					$arr[1] = 'role';
				}

				$field = um_profile( $arr[1] );
				
				switch( $op ) {
					case 'equals to': 

						if( is_serialized( $field ) ){
							
							if ( in_array( $val , unserialize( $field ) ) ) {
								$state = 1; 
							}else{
								$state = 0;
							}

						}else{

							if ( $field == $val ) {
								$state = 1; 
							}else{
								$state = 0;
							}

						}
						
					break;
					case 'not equals': 

						if( is_serialized( $field ) ){
							
							if ( ! in_array( $val , unserialize( $field ) ) ) {
								$state = 1; 
							}else{
								$state = 0;
							}

						}else{
							
							if ( $field != $val ) {
								$state = 1; 
							}else{
								$state = 0;
							}

						}
					break;
					case 'empty': 
						if ( $field ){
							$state = 1;
						}else{
							$state = 0;
						} 
					break;
					case 'not empty': 
						if ( ! $field ){
							$state = 1;
						}else{
							$state = 0;
						}  
					break;
					case 'greater than':
						if ( $field > $val ){
							$state = 1;
						}else{
							$state = 0;
						} 
					break; 
					case 'less than': 
						if ( $field < $val ){
							$state = 1;
						}else{
							$state = 0;
						} 
					break;
					case 'contains': 
						if ( strstr( $field, $val ) ){
							$state = 1; 
						}else{
							$state = 0;
						} 
					break;
				}
			}

			if ( $arr[0] == 'hide' ) {

				$state = 1;
				$val = $arr[3];
				$op = $arr[2];

				if( strstr( $arr[1] , 'role_') ){
					$arr[1] = 'role';
				}

				$field = um_profile( $arr[1] );
				
				switch( $op ) {
					case 'equals to': 
						if( is_serialized( $field ) ){
							if ( in_array( $val , unserialize( $field ) ) ) {
								$state = 0; 
							}else{
								$state = 1;
							}
						}else{
							if ( $field == $val ) {
								$state = 0; 
							}else{
								$state = 1;
							}
						}
					break;
					case 'not equals': 
						if( is_serialized( $field ) ){
							if ( ! in_array( $val , unserialize( $field ) ) ) {
								$state = 0; 
							}else{
								$state = 1;
							}
						}else{
							if ( $field != $val ) {
								$state = 0; 
							}else{
								$state = 1;
							}
						}
					break;
					case 'empty': 
						if ( $field ){
						 	$state = 0; 
						}else{
								$state = 1;
						}
					break;
					case 'not empty': 
						if ( !$field ){
						 	$state = 0; 
						}else{
								$state = 1;
						}
					break;
					case 'greater than': 
						if ( $field <= $val ){
							$state = 0; 
						}else{
							$state = 1;
						}
					break;
					case 'less than': 
						if ( $field >= $val ){
							$state = 0; 
						}else{
							$state = 1;
						}
					break;
					case 'contains': 
						if ( strstr( $field, $val ) ){
							$state = 0; 
						}else{
							$state = 1;
						}
					break;
				}
			}

		}

		if ( $state )
			return true;
		return false;
	}

	/***
	***	@Exit and redirect to home
	***/
	function um_redirect_home() {
		
		exit( wp_redirect( home_url() ) );
	}

	/***
	***	@Get limit of words from sentence
	***/
	function um_get_snippet( $str, $wordCount = 10 ) {
		if ( str_word_count( $str ) > $wordCount ) {
		  $str = implode(
			'',
			array_slice(
			  preg_split(
				'/([\s,\.;\?\!]+)/',
				$str,
				$wordCount*2+1,
				PREG_SPLIT_DELIM_CAPTURE
			  ),
			  0,
			  $wordCount*2-1
			)
		  );
		}
	  return $str;
	}

	/***
	***	@Get submitted user information
	***/
	function um_user_submitted_registration( $style = false ) {
		$output = null;

		$data = um_user('submitted');
		$udata = get_userdata( um_user('ID') );

		if ( $style ) $output .= '<div class="um-admin-infobox">';

		if ( isset( $data ) && is_array( $data ) ) {

			$data = apply_filters('um_email_registration_data', $data );

			foreach( $data as $k => $v ) {

				if ( !is_array( $v ) && strstr( $v, 'ultimatemember/temp' ) ) {
					$file = basename( $v );
					$v = um_user_uploads_uri() . $file;
				}

				if ( !strstr( $k, 'user_pass' ) && ! in_array( $k, array('g-recaptcha-response','request','_wpnonce','_wp_http_referer') ) ) {

					if ( is_array($v) ) {
						$v = implode(',', $v );
					}

					if ( $k == 'timestamp' ) {
						$k = __('date submitted','ultimate-member');
						$v = date("d M Y H:i", $v);
					}

					if ( $style ) {
						if ( !$v ) $v = __('(empty)','ultimate-member');
						$output .= "<p><label>$k</label><span>$v</span></p>";
					} else {
						$output .= "$k: $v" . "\r\n";
					}

				}

			}
		}

		if ( $style ) $output .= '</div>';

		return $output;
	}

	/***
	***	@Show filtered social link
	***/
	function um_filtered_social_link( $key, $match ) {
		$value = um_profile( $key );
		$submatch = str_replace( 'https://', '', $match );
		$submatch = str_replace( 'http://', '', $submatch );
		if ( strstr( $value, $submatch ) ) {
			$value = 'https://' . $value;
		} else if ( strpos($value, 'http') !== 0 ) {
			$value = $match . $value;
		}
		$value = str_replace('https://https://','https://',$value);
		$value = str_replace('http://https://','https://',$value);
		$value = str_replace('https://http://','https://',$value);
		return $value;
	}

	/***
	***	@Get filtered meta value after applying hooks
	***/
	function um_filtered_value( $key, $data = false ) {
		global $ultimatemember;

		$value = um_user( $key );

		if ( !$data ) {
			$data = $ultimatemember->builtin->get_specific_field( $key );
		}

		$type = ( isset($data['type']) ) ? $data['type'] : '';

		$value = apply_filters("um_profile_field_filter_hook__", $value, $data, $type );
		$value = apply_filters("um_profile_field_filter_hook__{$key}", $value, $data );
		$value = apply_filters("um_profile_field_filter_hook__{$type}", $value, $data );

		return $value;
	}


	function um_profile_id() {

		if ( um_get_requested_user() ) {
			return um_get_requested_user();
		} else if ( is_user_logged_in() && get_current_user_id() ) {
			return get_current_user_id();
		}

		return 0;
	}

	/***
	***	@Check that temp upload is valid
	***/
	function um_is_temp_upload( $url ) {
		global $ultimatemember;

		$url = explode('/ultimatemember/temp/', $url);
		if ( isset( $url[1] ) ) {

			if ( strstr( $url[1], '../' ) || strstr( $url[1], '%' ) ) {
				return false;
			}

			$src = $ultimatemember->files->upload_temp . $url[1];
			if ( !file_exists( $src ) ) {
				return false;
			}
			return $src;
		}
		return false;
	}

	/***
	***	@Check that temp image is valid
	***/
	function um_is_temp_image( $url ) {
		global $ultimatemember;
		$url = explode('/ultimatemember/temp/', $url);
		if ( isset( $url[1] ) ) {
			$src = $ultimatemember->files->upload_temp . $url[1];
			if ( !file_exists( $src ) )
				return false;
			list($width, $height, $type, $attr) = @getimagesize($src);
			if ( isset( $width ) && isset( $height ) )
				return $src;
		}
		return false;
	}

	/***
	***	@Get a translated core page URL
	***/
	function um_get_url_for_language( $post_id, $language ){
		$lang_post_id = icl_object_id( $post_id , 'page', true, $language );

		$url = "";
		if($lang_post_id != 0) {
			$url = get_permalink( $lang_post_id );
		}else {
			// No page found, it's most likely the homepage
			global $sitepress;
			$url = $sitepress->language_url( $language );
		}

		return $url;
	}

	/***
	***	@Get core page url
	***/
	function um_time_diff( $time1, $time2 ) {
		global $ultimatemember;
		return $ultimatemember->datetime->time_diff( $time1, $time2 );
	}

	/***
	***	@Get user's last login timestamp
	***/
	function um_user_last_login_timestamp( $user_id ) {
		$value = get_user_meta( $user_id, '_um_last_login', true );
		if ( $value )
			return $value;
		return '';
	}

	/***
	***	@Get user's last login time
	***/
	function um_user_last_login_date( $user_id ) {
		$value = get_user_meta( $user_id, '_um_last_login', true );
		if ( $value )
			return date_i18n('F d, Y', $value );
		return '';
	}

	/***
	***	@Get user's last login (time diff)
	***/
	function um_user_last_login( $user_id ) {
		$value = get_user_meta( $user_id, '_um_last_login', true );
		if ( $value ) {
			$value = um_time_diff( $value, current_time('timestamp') );
		} else {
			$value = '';
		}
		return $value;
	}

	/***
	***	@Get core page url
	***/
	function um_get_core_page( $slug, $updated = false) {
		global $ultimatemember;
		$url = '';

		if ( isset( $ultimatemember->permalinks->core[ $slug ] ) ) {
			$url = get_permalink( $ultimatemember->permalinks->core[ $slug ] );
			if ( $updated )
				$url =  add_query_arg( 'updated', esc_attr( $updated ), $url );
		}

		if ( function_exists('icl_get_current_language') && icl_get_current_language() != icl_get_default_language()  ) {

			$url = um_get_url_for_language( $ultimatemember->permalinks->core[ $slug ], icl_get_current_language() );

			if ( get_post_meta( get_the_ID() , '_um_wpml_account', true ) == 1 ) {
				$url = get_permalink( get_the_ID() );
			}
			if ( get_post_meta( get_the_ID() , '_um_wpml_user', true ) == 1 ) {
				$url = um_get_url_for_language( $ultimatemember->permalinks->core[ $slug ], icl_get_current_language() );
			}
		}

		if ( $url ) {
			$url = apply_filters('um_get_core_page_filter', $url, $slug, $updated);
			return $url;
		}

		return '';
	}

	/***
	***	@boolean check if we are on UM page
	***/
	function is_ultimatemember() {
		global $post, $ultimatemember;
		if ( isset($post->ID) && in_array( $post->ID, $ultimatemember->permalinks->core ) )
			return true;
		return false;
	}

	/***
	***	@boolean check if we are on a core page or not
	***/
	function um_is_core_page( $page ) {
		global $post, $ultimatemember;
		if ( isset($post->ID) && isset( $ultimatemember->permalinks->core[ $page ] ) && $post->ID == $ultimatemember->permalinks->core[ $page ] )
			return true;
		if ( isset($post->ID) && get_post_meta( $post->ID, '_um_wpml_' . $page, true ) == 1 )
			return true;

		if( isset($post->ID) ){
			$_icl_lang_duplicate_of = get_post_meta( $post->ID, '_icl_lang_duplicate_of', true );

			if (  isset( $ultimatemember->permalinks->core[ $page ] ) && (  (  $_icl_lang_duplicate_of == $ultimatemember->permalinks->core[ $page ] && ! empty( $_icl_lang_duplicate_of ) ) || $ultimatemember->permalinks->core[ $page ] == $post->ID ) )
				return true;
		}

		return false;
	}

	/***
	***	@Is core URL
	***/
	function um_is_core_uri() {
		global $ultimatemember;
		$array = $ultimatemember->permalinks->core;
		$current_url = $ultimatemember->permalinks->get_current_url( get_option('permalink_structure') );

		if ( !isset( $array ) || !is_array( $array ) ) return false;

		foreach( $array as $k => $id ) {
			$page_url = get_permalink( $id );
			if ( strstr( $current_url, $page_url ) )
				return true;
		}
		return false;
	}

	/***
	***	@Check value of queried search in text input
	***/
	function um_queried_search_value( $filter, $echo = true ) {
		global $ultimatemember;
		$value = '';
		if ( isset($_REQUEST['um_search']) ) {
			$query = $ultimatemember->permalinks->get_query_array();
			if ( isset( $query[ $filter ] ) && $query[ $filter ] != '' ) {
				$value = stripslashes_deep( $query[ $filter ] );
			}
		}
		
		if( $echo ){
			echo $value;
		}else{
			return $value;
		}

	}

	/***
	***	@Check whether item in dropdown is selected in query-url
	***/
	function um_select_if_in_query_params( $filter, $val ) {
		global $ultimatemember;
		if ( isset($_REQUEST['um_search']) ) {
			$query = $ultimatemember->permalinks->get_query_array();
			if ( isset( $query[$filter] ) && $val == $query[$filter] )
				echo 'selected="selected"';
		}
		echo '';
	}

	/***
	***	@get styling defaults
	***/
	function um_styling_defaults( $mode ) {
		global $ultimatemember;
		$arr = $ultimatemember->setup->core_form_meta_all;
		foreach( $arr as $k => $v ) {
			$s = str_replace($mode . '_', '', $k );
			if ( strstr($k, '_um_'.$mode.'_') && !in_array($s, $ultimatemember->setup->core_global_meta_all ) ) {
				$a = str_replace('_um_'.$mode.'_','',$k);
				$b = str_replace('_um_','',$k);
				$new_arr[$a] = um_get_option( $b );
			} else if ( in_array( $k, $ultimatemember->setup->core_global_meta_all ) ) {
				$a = str_replace('_um_','',$k);
				$new_arr[$a] = um_get_option( $a );
			}
		}

		return $new_arr;
	}

	/***
	***	@get meta option default
	***/
	function um_get_metadefault( $id ) {
		global $ultimatemember;
		if ( isset( $ultimatemember->setup->core_form_meta_all[ '_um_' . $id ] ) )
			return $ultimatemember->setup->core_form_meta_all[ '_um_' . $id ];
		return '';
	}

	/***
	***	@check if a legitimate password reset request is in action
	***/
	function um_requesting_password_reset() {
		global $post, $ultimatemember;
		if (  um_is_core_page('password-reset') && isset( $_POST['_um_password_reset'] ) == 1 )
			return true;
		return false;
	}

	/***
	***	@check if a legitimate password change request is in action
	***/
	function um_requesting_password_change() {
		global $post, $ultimatemember;

		if (  um_is_core_page('account') && isset( $_POST['_um_account'] ) == 1 )
			return true;
		elseif ( isset( $_POST['_um_password_change'] ) && $_POST['_um_password_change'] == 1)
			return true;
		return false;
	}

	/***
	***	@boolean for account page editing
	***/
	function um_submitting_account_page() {
		if ( um_is_core_page('account') && isset($_POST['_um_account']) == 1 && is_user_logged_in() )
			return true;
		return false;
	}

	/***
	***	@get a user's display name
	***/
	function um_get_display_name( $user_id ) {
		um_fetch_user( $user_id );
		$name = um_user('display_name');
		um_reset_user();
		return $name;
	}

	/***
	***	@get members to show in directory
	***/
	function um_members( $argument ) {
		global $ultimatemember;
		return $ultimatemember->members->results[ $argument ];
	}

	/**
	 * @function um_reset_user_clean()
	 *
	 * @description This function is similar to um_reset_user() with a difference that it will not use the logged-in user
		data after resetting. It is a hard-reset function for all user data.
	 *
	 * @usage <?php um_reset_user_clean(); ?>
	 *
	 * @returns Clears the user data. You need to fetch a user manually after using this function.
	 *
	 * @example You can reset user data by using the following line in your code

		<?php um_reset_user_clean(); ?>

	 *
	 *
	 */
	function um_reset_user_clean() {
		global $ultimatemember;
		$ultimatemember->user->reset( true );
	}

	/**
	 * @function um_reset_user()
	 *
	 * @description This function resets the current user. You can use it to reset user data after
		retrieving the details of a specific user.
	 *
	 * @usage <?php um_reset_user(); ?>
	 *
	 * @returns Clears the user data. If a user is logged in, the user data will be reset to that user's data
	 *
	 * @example You can reset user data by using the following line in your code

		<?php um_reset_user(); ?>

	 *
	 *
	 */
	function um_reset_user() {
		global $ultimatemember;
		$ultimatemember->user->reset();
	}

	/***
	***	@gets the queried user
	***/
	function um_queried_user() {
		
		return get_query_var('um_user');
	}

	/***
	***	@Sets the requested user
	***/
	function um_set_requested_user( $user_id ) {
		global $ultimatemember;
		$ultimatemember->user->target_id = $user_id;
	}

	/***
	***	@Gets the requested user
	***/
	function um_get_requested_user() {
		global $ultimatemember;
		if ( isset( $ultimatemember->user->target_id ) && !empty( $ultimatemember->user->target_id ) )
			return $ultimatemember->user->target_id;
		return false;
	}

	/***
	***	@remove edit profile args from url
	***/
	function um_edit_my_profile_cancel_uri( $url = '' ) {
		global $ultimatemember;
		
		if(  empty(  $url ) ){
			$url = remove_query_arg( 'um_action' );
			$url = remove_query_arg( 'profiletab', $url );
			$url = add_query_arg('profiletab', 'main', $url );
		}

		$url = apply_filters('um_edit_profile_cancel_uri', $url );

		return $url;
	}

	/***
	***	@boolean for profile edit page
	***/
	function um_is_on_edit_profile() {
		if ( isset( $_REQUEST['profiletab'] ) && isset( $_REQUEST['um_action'] ) ) {
			if ( $_REQUEST['profiletab'] == 'main' && $_REQUEST['um_action'] == 'edit' ) {
				return true;
			}
		}
		return false;
	}

	/***
	***	@can view field
	***/
	function um_can_view_field( $data ) {
		global $ultimatemember;

		if ( !isset( $ultimatemember->fields->set_mode ) )
			$ultimatemember->fields->set_mode = '';

		if ( isset( $data['public'] ) && $ultimatemember->fields->set_mode != 'register' ) {

			if ( !is_user_logged_in() && $data['public'] != '1' ) return false;

			if ( is_user_logged_in() ) {

				if ( $data['public'] == '-3' && !um_is_user_himself() && !in_array( $ultimatemember->query->get_role_by_userid( get_current_user_id() ), $data['roles'] ) )
					return false;

				if ( !um_is_user_himself() && $data['public'] == '-1' && !um_user_can('can_edit_everyone') )
					return false;

				if ( $data['public'] == '-2' && $data['roles'] )
					if ( !in_array( $ultimatemember->query->get_role_by_userid( get_current_user_id() ), $data['roles'] ) )
						return false;
			}

		}

		return true;
	}

	/***
	***	@checks if user can view profile
	***/
	function um_can_view_profile( $user_id ){
		global $ultimatemember;

		if ( !um_user('can_view_all') && $user_id != get_current_user_id() && is_user_logged_in() ) return false;

		if ( um_current_user_can('edit', $user_id ) ) {
			return true;
		}

		if ( !is_user_logged_in() ) {
			if ( $ultimatemember->user->is_private_profile( $user_id ) ) {
				return false;
			} else {
				return true;
			}
		}

		if ( !um_user('can_access_private_profile') && $ultimatemember->user->is_private_profile( $user_id ) ) return false;

		if ( um_user_can('can_view_roles') && $user_id != get_current_user_id() ) {
			if ( !in_array( $ultimatemember->query->get_role_by_userid( $user_id ), um_user_can('can_view_roles') ) ) {
				return false;
			}
		}

		return true;

	}

	/***
	***	@boolean check for not same user
	***/
	function um_is_user_himself() {
		if ( um_get_requested_user() && um_get_requested_user() != get_current_user_id() )
			return false;
		return true;
	}

	/***
	***	@can edit field
	***/
	function um_can_edit_field( $data ) {
		global $ultimatemember;

		if ( isset( $ultimatemember->fields->editing ) && $ultimatemember->fields->editing == true &&
				isset( $ultimatemember->fields->set_mode ) && $ultimatemember->fields->set_mode == 'profile' ) {

			if ( is_user_logged_in() && isset( $data['editable'] ) && $data['editable'] == 0 ) {

				if( isset( $data['public'] ) && $data['public'] == "-2"){
					return true;
				}

				if ( um_is_user_himself() && !um_user('can_edit_everyone') ){
					return true;
				}


				if ( !um_is_user_himself() && !um_user_can('can_edit_everyone') ){
					return false;
				}

			}

		}

		return true;

	}

	/***
	***	@User can (role settings )
	***/
	function um_user_can( $permission ) {
		global $ultimatemember;
		if ( !is_user_logged_in() )
			return false;
		$user_id = get_current_user_id();
		$role = get_user_meta( $user_id, 'role', true );
		$permissions = $ultimatemember->query->role_data( $role );
		$permissions = apply_filters('um_user_permissions_filter', $permissions, $user_id);
		if ( isset( $permissions[ $permission ] ) && is_serialized( $permissions[ $permission ] ) )
			return unserialize( $permissions[ $permission ] );
		if ( isset( $permissions[ $permission ] ) && $permissions[ $permission ] == 1 )
			return true;
		return false;
	}

	/***
	***	@Check if user is in his profile
	***/
	function um_is_myprofile(){
		global $ultimatemember;
		if ( get_current_user_id() && get_current_user_id() == um_get_requested_user() )return true;
		if ( !um_get_requested_user() && um_is_core_page('user') && get_current_user_id() ) return true;
		return false;
	}

	/***
	***	@Current user can
	***/
	function um_current_user_can( $cap, $user_id ){
		global $ultimatemember;

		if ( !is_user_logged_in() ) return false;

		$return = 1;

		um_fetch_user( get_current_user_id() );

		switch($cap) {

			case 'edit':
				if ( get_current_user_id() == $user_id && um_user('can_edit_profile') ) $return = 1;
					elseif ( !um_user('can_edit_everyone') ) $return = 0;
					elseif ( get_current_user_id() == $user_id && !um_user('can_edit_profile') ) $return = 0;
					elseif ( um_user('can_edit_roles') && !in_array( $ultimatemember->query->get_role_by_userid( $user_id ), um_user('can_edit_roles') ) ) $return = 0;
				break;

			case 'delete':
				if ( !um_user('can_delete_everyone') ) $return = 0;
				elseif ( um_user('can_delete_roles') && !in_array( $ultimatemember->query->get_role_by_userid( $user_id ), um_user('can_delete_roles') ) ) $return = 0;
				break;

		}

		um_fetch_user( $user_id );

		return $return;
	}

	/***
	***	@Returns the edit profile link
	***/
	function um_edit_profile_url(){
		global $ultimatemember;

		if( um_is_core_page('user') ){
			$url = $ultimatemember->permalinks->get_current_url();
		}else{ 
			$url = um_user_profile_url();
		}
		
		$url = remove_query_arg('profiletab', $url);
		$url = remove_query_arg('subnav', $url);
		$url = add_query_arg( 'profiletab', 'main', $url );
		$url = add_query_arg( 'um_action',  'edit', $url );
		return $url;
	}

	/***
	***	@checks if user can edit his profile
	***/
	function um_can_edit_my_profile(){
		global $ultimatemember;
		if ( !is_user_logged_in() ) return false;
		if ( !um_user('can_edit_profile') ) return false;
		return true;
	}

	/***
	***	@short for admin e-mail
	***/
	function um_admin_email(){
		return um_get_option('admin_email');
	}

	/**
	 * @function um_get_option()
	 *
	 * @description This function returns the value of an option or setting.
	 *
	 * @usage <?php $value = um_get_option( $setting ); ?>
	 *
	 * @param $option_id (string) (required) The option or setting that you want to retrieve
	 *
	 * @returns Returns the value of the setting you requested, or a blank value if the setting
		does not exist.
	 *
	 * @example Get default user role set in global options

		<?php $default_role = um_get_option('default_role'); ?>

	 *
	 * @example Get blocked IP addresses set in backend

		<?php $blocked_ips = um_get_option('blocked_ips'); ?>

	 *
	 */
	function um_get_option($option_id) {
		global $ultimatemember;
		if ( !isset( $ultimatemember->options ) ) return '';
		$um_options = $ultimatemember->options;
		if ( isset( $um_options[ $option_id ] ) && !empty( $um_options[ $option_id ] ) )	{
			return apply_filters("um_get_option_filter__{$option_id}", $um_options[ $option_id ] );
		}

		switch($option_id){

			case 'site_name':
				return get_bloginfo('name');
				break;

			case 'admin_email':
				return get_bloginfo('admin_email');
				break;

		}

	}

	/***
	***	@Display a link to profile page
	***/
	function um_user_profile_url() {
		global $ultimatemember;
		return $ultimatemember->permalinks->profile_url();
	}

	/***
	***	@Get all UM roles in array
	***/
	function um_get_roles() {
		global $ultimatemember;
		return $ultimatemember->query->get_roles();
	}

	/**
	 * @function um_fetch_user()
	 *
	 * @description This function sets a user and allow you to retrieve any information for the retrieved user
	 *
	 * @usage <?php um_fetch_user( $user_id ); ?>
	 *
	 * @param $user_id (numeric) (required) A user ID is required. This is the user's ID that you wish to set/retrieve
	 *
	 * @returns Sets a specific user and prepares profile data and user permissions and makes them accessible.
	 *
	 * @example The example below will set user ID 5 prior to retrieving his profile information.

		<?php

			um_fetch_user(5);
			echo um_user('display_name'); // returns the display name of user ID 5

		?>

	 *
	 * @example In the following example you can fetch the profile of a logged-in user dynamically.

		<?php

			um_fetch_user( get_current_user_id() );
			echo um_user('display_name'); // returns the display name of logged-in user

		?>

	 *
	 */
	function um_fetch_user( $user_id ) {
		global $ultimatemember;
		$ultimatemember->user->set( $user_id );
	}

	/***
	***	@Load profile key
	***/
	function um_profile( $key ){
		global $ultimatemember;
		
		if (isset( $ultimatemember->user->profile[ $key ] ) && !empty( $ultimatemember->user->profile[ $key ] ) ){
			$value = apply_filters("um_profile_{$key}__filter", $ultimatemember->user->profile[ $key ] );
		} else {
			$value = apply_filters("um_profile_{$key}_empty__filter", false );
		}

		return $value;
			
	}

	/***
	***	@Get youtube video ID from url
	***/
	function um_youtube_id_from_url($url) {
		$pattern =
			'%^# Match any youtube URL
			(?:https?://)?  # Optional scheme. Either http or https
			(?:www\.)?      # Optional www subdomain
			(?:             # Group host alternatives
			  youtu\.be/    # Either youtu.be,
			| youtube\.com  # or youtube.com
			  (?:           # Group path alternatives
				/embed/     # Either /embed/
			  | /v/         # or /v/
			  | /watch\?v=  # or /watch\?v=
			  )             # End path alternatives.
			)               # End host alternatives.
			([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
			$%x'
			;
		$result = preg_match($pattern, $url, $matches);
		if (false !== $result) {
			return $matches[1];
		}
		return false;
	}

	/***
	***	@user uploads uri
	***/
	function um_user_uploads_uri() {
		global $ultimatemember;

		if( is_ssl() ){
			 $ultimatemember->files->upload_baseurl = str_replace("http://", "https://",  $ultimatemember->files->upload_baseurl );
		}

		$uri = $ultimatemember->files->upload_baseurl . um_user('ID') . '/';
		return $uri;
	}

	/***
	***	@user uploads directory
	***/
	function um_user_uploads_dir() {
		global $ultimatemember;
		$uri = $ultimatemember->files->upload_basedir . um_user('ID') . '/';
		return $uri;
	}

	/***
	***	@find closest number in an array
	***/
	function um_closest_num($array, $number) {
		sort($array);
		foreach ($array as $a) {
			if ($a >= $number) return $a;
		}
		return end($array);
	}

	/***
	***	@get cover uri
	***/
	function um_get_cover_uri( $image, $attrs ) {
		global $ultimatemember;
		$uri = false;
		$ext = '.' . pathinfo($image, PATHINFO_EXTENSION);
		if ( file_exists( $ultimatemember->files->upload_basedir . um_user('ID') . '/cover_photo'.$ext ) ) {
			$uri = um_user_uploads_uri() . 'cover_photo'.$ext.'?' . current_time( 'timestamp' );
		}
		if ( file_exists( $ultimatemember->files->upload_basedir . um_user('ID') . '/cover_photo-' .$attrs.$ext ) ){
			$uri = um_user_uploads_uri() . 'cover_photo-'.$attrs.$ext.'?' . current_time( 'timestamp' );
		}
		return $uri;
	}

	/***
	***	@get avatar URL instead of image
	***/
	function um_get_avatar_url($get_avatar){
		preg_match('/src="(.*?)"/i', $get_avatar, $matches);
		return $matches[1];
	}

	/***
	***	@get avatar uri
	***/
	function um_get_avatar_uri( $image, $attrs ) {
		global $ultimatemember;
		$uri = false;
		$find = false;
		$ext = '.' . pathinfo($image, PATHINFO_EXTENSION);

		$cache_time = apply_filters('um_filter_avatar_cache_time', current_time( 'timestamp' ), um_user('ID') );
		
		if( ! empty( $cache_time ) ){
				$cache_time = "?{$cache_time}";
		}

		if ( file_exists( $ultimatemember->files->upload_basedir . um_user('ID') . "/profile_photo-{$attrs}{$ext}" ) ) {
			
			$uri = um_user_uploads_uri() . "profile_photo-{$attrs}{$ext}{$cache_time}";

		} else {

			$sizes = um_get_option('photo_thumb_sizes');
			if ( is_array( $sizes ) ) $find = um_closest_num( $sizes, $attrs );

			if ( file_exists( $ultimatemember->files->upload_basedir . um_user('ID') . "/profile_photo-{$find}{$ext}" ) ) {

				$uri = um_user_uploads_uri() . "profile_photo-{$find}{$ext}{$cache_time}";

			} else if ( file_exists( $ultimatemember->files->upload_basedir . um_user('ID') . "/profile_photo{$ext}" ) ) {

				$uri = um_user_uploads_uri() . "profile_photo{$ext}{$cache_time}";

			}

			if ( $attrs == 'original' ) {
				$uri = um_user_uploads_uri() . "profile_photo{$ext}{$cache_time}";
			}

		}
		return $uri;
	}

	/***
	***	@default avatar
	***/
	function um_get_default_avatar_uri( $user_id = '' ) {
		$uri = um_get_option('default_avatar');
		$uri = $uri['url'];
		if ( !$uri )
			$uri = um_url . 'assets/img/default_avatar.jpg';

		return $uri;
	}

	/***
	***	@get user avatar url
	***/
	function um_get_user_avatar_url() {
		if ( um_profile('profile_photo') ) {
			$avatar_uri = um_get_avatar_uri( um_profile('profile_photo'), 32 );
		} else {
			$avatar_uri = um_get_default_avatar_uri();
		}
		return $avatar_uri;
	}

	/***
	***	@default cover
	***/
	function um_get_default_cover_uri() {
		$uri = um_get_option('default_cover');
		$uri = $uri['url'];
		if ( $uri ){
			$uri = apply_filters('um_get_default_cover_uri_filter', $uri );
			return $uri;
		}
		return '';
	}

	function um_user( $data, $attrs = null ) {

		global $ultimatemember;
       
       switch($data){

			default:

				$value = um_profile($data);
				
				if ( $ultimatemember->validation->is_serialized( $value ) ) {
					$value = unserialize( $value );
				}

				if( in_array( $data, array('role','gender') ) ){
					if( is_array( $value ) ){
						$value = implode(",", $value );
					}
					return $value;
				}

				return $value;
				break;

			case 'user_email':

				$user_email_in_meta = get_user_meta( um_user('ID'), 'user_email', true );
				if( $user_email_in_meta ){
					delete_user_meta( um_user('ID'), 'user_email' );
				}

				$value = um_profile( $data );
				
				return $value;
				break;

			case 'first_name':
			case 'last_name':

				$name = um_profile( $data );

				if( um_get_option('force_display_name_capitlized') ){
					$name = implode('-', array_map('ucfirst', explode('-', $name ) ) );
				}

				$name = apply_filters("um_user_{$data}_case", $name );

				return $name;

				break;

			case 'full_name':

				if ( um_user('first_name') && um_user('last_name') ) {
					$full_name = um_user('first_name') . ' ' . um_user('last_name');
				} else {
					$full_name = um_user('display_name');
				}

				$full_name = $ultimatemember->validation->safe_name_in_url( $full_name );

				// update full_name changed
				if( um_profile( $data ) !== $full_name )
				{
					update_user_meta( um_user('ID'), 'full_name', $full_name );
				}

				return $full_name;

			break;

			case 'first_and_last_name_initial':
				
				$f_and_l_initial = '';

				if ( um_user('first_name') && um_user('last_name') ) {
					$initial = um_user('last_name');
					$f_and_l_initial =  um_user('first_name').' '. $initial[0];
				}else{
					$f_and_l_initial = um_profile( $data );
				}

				$f_and_l_initial = $ultimatemember->validation->safe_name_in_url( $f_and_l_initial );

				if( um_get_option('force_display_name_capitlized') ){
					$name = implode('-', array_map('ucfirst', explode('-', $f_and_l_initial ) ) ); 
				}else{
					$name = $f_and_l_initial;
				}
				
				return $name;

			break;

			case 'display_name':

				$op = um_get_option('display_name');

				$name = '';


				if ( $op == 'default' ) {
					$name = um_profile('display_name');
				}

				if ( $op == 'nickname' ) {
					$name = um_profile('nickname');
				}

				if ( $op == 'full_name' ) {
					if ( um_user('first_name') && um_user('last_name') ) {
						$name = um_user('first_name') . ' ' . um_user('last_name');
					} else {
						$name = um_profile( $data );
					}
					if ( ! $name ) {
						$name = um_user('user_login');
					}
				}

				if ( $op == 'sur_name' ) {
					if ( um_user('first_name') && um_user('last_name') ) {
						$name = um_user('last_name') . ' ' . um_user('first_name');
					} else {
						$name = um_profile( $data );
					}
				}

				if ( $op == 'first_name' ) {
					if ( um_user('first_name') ) {
						$name = um_user('first_name');
					} else {
						$name = um_profile( $data );
					}
				}

				if ( $op == 'username' ) {
					$name = um_user('user_login');
				}

				if ( $op == 'initial_name' ) {
					if ( um_user('first_name') && um_user('last_name') ) {
						$initial = um_user('last_name');
						$name = um_user('first_name') . ' ' . $initial[0];
					} else {
						$name = um_profile( $data );
					}
				}

				if ( $op == 'initial_name_f' ) {
					if ( um_user('first_name') && um_user('last_name') ) {
						$initial = um_user('first_name');
						$name = $initial[0] . ' ' . um_user('last_name');
					} else {
						$name = um_profile( $data );
					}
				}


				if ( $op == 'field' && um_get_option('display_name_field') != '' ) {
					$fields = array_filter(preg_split('/[,\s]+/', um_get_option('display_name_field') ));
					$name = '';

					foreach( $fields as $field ) {
						if( um_profile( $field ) ){
							$name .= um_profile( $field ) . ' ';
						}else if(  um_user( $field ) ){
							$name .= um_user( $field ) . ' ';
						}
						
					}
				}

				if( um_get_option('force_display_name_capitlized') ){
					$name = implode('-', array_map('ucfirst', explode('-', $name ) ) );
				}

				return apply_filters('um_user_display_name_filter', $name, um_user('ID'), ( $attrs == 'html' ) ? 1 : 0 );

				break;

			case 'role_select':
			case 'role_radio':
				return $ultimatemember->user->get_role_name( um_user('role') );
				break;

			case 'submitted':
				$array = um_profile($data);
				if ( empty( $array ) ) return '';
				$array = unserialize( $array );
				return $array;
				break;

			case 'password_reset_link':
				return $ultimatemember->password->reset_url();
				break;

			case 'account_activation_link':
				return $ultimatemember->permalinks->activate_url();
				break;

			case 'profile_photo':

				$has_profile_photo = false;
				$photo_type = 'um-avatar-default';
				$image_alt = apply_filters("um_avatar_image_alternate_text",  um_user("display_name") );

				if ( um_profile('profile_photo') ) {
						$avatar_uri = um_get_avatar_uri( um_profile('profile_photo'), $attrs );
						$has_profile_photo = true;
						$photo_type = 'um-avatar-uploaded';
				} elseif( um_user('synced_profile_photo') ){
						$avatar_uri = um_user('synced_profile_photo');
				} else {
						$avatar_uri = um_get_default_avatar_uri( um_user('ID') );
				}

				$avatar_uri = apply_filters('um_user_avatar_url_filter', $avatar_uri, um_user('ID') );

				if ( $avatar_uri )

					if( um_get_option('use_gravatars') && ! um_user('synced_profile_photo') && ! $has_profile_photo ){
						$avatar_hash_id = get_user_meta( um_user('ID'),'synced_gravatar_hashed_id', true);
						$avatar_uri  = um_get_domain_protocol().'gravatar.com/avatar/'.$avatar_hash_id;
						$avatar_uri = add_query_arg('s',400, $avatar_uri);
						$gravatar_type = um_get_option('use_um_gravatar_default_builtin_image');
						$photo_type = 'um-avatar-gravatar';
						if( $gravatar_type == 'default' ){
							if( um_get_option('use_um_gravatar_default_image') ){
								$avatar_uri = add_query_arg('d', um_get_default_avatar_uri(), $avatar_uri  );
							}
						}else{
								$avatar_uri = add_query_arg('d', $gravatar_type, $avatar_uri  );
						}
						
					}

					return '<img src="' . $avatar_uri . '" class="func-um_user gravatar avatar avatar-'.$attrs.' um-avatar '.$photo_type.'" width="'.$attrs.'" height="'.$attrs.'" alt="'.$image_alt.'" />';

				if ( !$avatar_uri )
					return '';

				break;

			case 'cover_photo':
				
				$is_default = false;

				if ( um_profile('cover_photo') ) {
					$cover_uri = um_get_cover_uri( um_profile('cover_photo'), $attrs );
				} else if( um_profile('synced_cover_photo') ) {
					$cover_uri = um_profile('synced_cover_photo');
				}else{
					$cover_uri = um_get_default_cover_uri();
					$is_default = true;
				}

				$cover_uri = apply_filters('um_user_cover_photo_uri__filter', $cover_uri, $is_default, $attrs );
				
				if ( $cover_uri )
					return '<img src="'. $cover_uri .'" alt="" />';

				if ( !$cover_uri )
					return '';

				break;


		}

	}

	/**
	 * Get server protocol
	 * @return  string
	 */
	function um_get_domain_protocol(){

		if ( is_ssl() ) {
				$protocol = 'https://';
		} else {
				$protocol = 'http://';
		}

		return $protocol;
	}

	/**
	 * Set SSL to media URI
	 * @param  string $url
	 * @return string
	 */
	function um_secure_media_uri( $url ){
		
		if( is_ssl() ){
			$url = str_replace('http:', 'https:', $url );
		}

		return $url;
	}

	/**
	 * Check if meta_value exists
	 * @param  string $key
	 * @param  mixed $value
	 * @return integer
	 */
	function um_is_meta_value_exists( $key, $value, $return_user_id = false ){
		global $wpdb, $ultimatemember;

		if( isset( $ultimatemember->profile->arr_user_slugs[ 'is_'.$return_user_id ][ $key ] ) ){
			return $ultimatemember->profile->arr_user_slugs[ 'is_'.$return_user_id ][ $key ];
		}

		if( ! $return_user_id ){
			$count = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*) as count FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s ",
					$key,
					$value
			) );

			$ultimatemember->profile->arr_user_slugs[ 'is_'.$return_user_id ][ $key ] = $count;

			return $count;
		}
			
			$user_id = $wpdb->get_var( $wpdb->prepare(
					"SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s ",
					$key,
					$value
			) );
			
			$ultimatemember->profile->arr_user_slugs[ 'is_'.$return_user_id ][ $key ] = $user_id;

			return $user_id;

	}

	/**
	 * Force strings to UTF-8 encoded
	 * @param  mixed $value
	 * @return mixed
	 */
	function um_force_utf8_string( $value ){

		if( is_array( $value ) ){
			$arr_value = array();
			foreach ($value as $key => $value) {
				$utf8_decoded_value = utf8_decode( $value );

				if( mb_check_encoding( $utf8_decoded_value, 'UTF-8') ){
				 	array_push( $arr_value, $utf8_decoded_value );
				}else{
					array_push( $arr_value, $value );
				}

			}
			return $arr_value;
		}else{

			$utf8_decoded_value = utf8_decode($value);

			if( mb_check_encoding( $utf8_decoded_value, 'UTF-8') ){
			 	return $utf8_decoded_value;
			}
		}

		return $value;
	}

	/**
	 * Filters the search query.
	 *
	 * @param  string $search
	 * @return string
	 */
	function um_filter_search($search) {
		$search = trim( strip_tags( $search ) );
		$search = preg_replace('/[^a-z \.\@\_\-]+/i', '', $search);

		return $search;
	}

	/**
	 * Returns the user search query
	 * @return string
	 */
	function um_get_search_query() {
		global $ultimatemember;

		$query  = $ultimatemember->permalinks->get_query_array();
		$search = isset( $query['search'] ) ? $query['search'] : '';

		return um_filter_search($search);
	}

	/**
	 * Returns the ultimate member search form
	 * @return string
	 */
	function um_get_search_form() {
		
		return do_shortcode( '[ultimatemember_searchform]' );
	}

	/**
	 * Display the search form.
	 *
	 * @return string
	 */
	function um_search_form() {
	
		echo um_get_search_form();
	}

	/**
	 * Get localization
	 * @return string
	 */
	function um_get_locale(){

		$lang_code = get_locale();

		if( strpos( $lang_code , 'en_' ) > -1 || empty( $lang_code ) ||  $lang_code == 0 ){
			return 'en';
		}
		
		return $lang_code;
	}

	/**
	 * Get current page type
	 * @return string
	 */
	function um_get_current_page_type() {
	    global $wp_query;
	    $loop = 'notfound';

	    if ( $wp_query->is_page ) {
	        $loop = is_front_page() ? 'front' : 'page';
	    } elseif ( $wp_query->is_home ) {
	        $loop = 'home';
	    } elseif ( $wp_query->is_single ) {
	        $loop = ( $wp_query->is_attachment ) ? 'attachment' : 'single';
	    } elseif ( $wp_query->is_category ) {
	        $loop = 'category';
	    } elseif ( $wp_query->is_tag ) {
	        $loop = 'tag';
	    } elseif ( $wp_query->is_tax ) {
	        $loop = 'tax';
	    } elseif ( $wp_query->is_archive ) {
	        if ( $wp_query->is_day ) {
	            $loop = 'day';
	        } elseif ( $wp_query->is_month ) {
	            $loop = 'month';
	        } elseif ( $wp_query->is_year ) {
	            $loop = 'year';
	        } elseif ( $wp_query->is_author ) {
	            $loop = 'author';
	        } else {
	            $loop = 'archive';
	        }
	    } elseif ( $wp_query->is_search ) {
	        $loop = 'search';
	    } elseif ( $wp_query->is_404 ) {
	        $loop = 'notfound';
	    }

	    return $loop;
	}

	/**
	 * Check if running local
	 * @return boolean
	 */
	function um_core_is_local() {
	    if( $_SERVER['HTTP_HOST'] == 'localhost'
	        || substr($_SERVER['HTTP_HOST'],0,3) == '10.'
	        || substr($_SERVER['HTTP_HOST'],0,7) == '192.168') return true;
	    return false;
	}

	/**
	 * Get user host
	 *
	 * Returns the webhost this site is using if possible
	 *
	 * @since 1.3.68
	 * @return mixed string $host if detected, false otherwise
	 */
	function um_get_host() {
		$host = false;

		if( defined( 'WPE_APIKEY' ) ) {
			$host = 'WP Engine';
		} elseif( defined( 'PAGELYBIN' ) ) {
			$host = 'Pagely';
		} elseif( DB_HOST == 'localhost:/tmp/mysql5.sock' ) {
			$host = 'ICDSoft';
		} elseif( DB_HOST == 'mysqlv5' ) {
			$host = 'NetworkSolutions';
		} elseif( strpos( DB_HOST, 'ipagemysql.com' ) !== false ) {
			$host = 'iPage';
		} elseif( strpos( DB_HOST, 'ipowermysql.com' ) !== false ) {
			$host = 'IPower';
		} elseif( strpos( DB_HOST, '.gridserver.com' ) !== false ) {
			$host = 'MediaTemple Grid';
		} elseif( strpos( DB_HOST, '.pair.com' ) !== false ) {
			$host = 'pair Networks';
		} elseif( strpos( DB_HOST, '.stabletransit.com' ) !== false ) {
			$host = 'Rackspace Cloud';
		} elseif( strpos( DB_HOST, '.sysfix.eu' ) !== false ) {
			$host = 'SysFix.eu Power Hosting';
		} elseif( strpos( $_SERVER['SERVER_NAME'], 'Flywheel' ) !== false ) {
			$host = 'Flywheel';
		} else {
			// Adding a general fallback for data gathering
			$host = 'DBH: ' . DB_HOST . ', SRV: ' . $_SERVER['SERVER_NAME'];
		}

		return $host;
	}

	/**
	 * Let To Num
	 *
	 * Does Size Conversions
	 *
	 * @since 1.3.68
	 * @author Chris Christoff
	 *
	 * @param unknown $v
	 * @return int|string
	 */
	function um_let_to_num( $v ) {
		$l   = substr( $v, -1 );
		$ret = substr( $v, 0, -1 );

		switch ( strtoupper( $l ) ) {
			case 'P': // fall-through
			case 'T': // fall-through
			case 'G': // fall-through
			case 'M': // fall-through
			case 'K': // fall-through
				$ret *= 1024;
				break;
			default:
				break;
		}

		return $ret;
	}

