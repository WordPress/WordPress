<?php
/* Hook to change auto generated password */
add_filter('random_password', 'signup_password_random_password_filter');
/**
 * Function that changes the auto generated password with the one selected by the user.
 */
function signup_password_random_password_filter($password) {
	global $wpdb;

	if ( ! empty($_GET['key']) ) {
		$key = $_GET['key'];
	} else {
		$key = $_POST['key'];
	}
	if ( !empty($_POST['user_pass']) ) {
		$password = $_POST['user_pass'];
	} else if ( !empty( $key ) ) {
	
		if ( is_multisite() )
			$signup = $wpdb->get_row("SELECT * FROM " . $wpdb->signups . " WHERE activation_key = '" . $key . "'");
		else
			$signup = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "signups WHERE activation_key = '" . $key . "'");
		
		if ( empty($signup) || $signup->active ) {
			//bad key or already active
		} else {
			//check for password in signup meta
			$meta = unserialize($signup->meta);
			
			$password = $meta['user_pass'];
		}		
	}
	return $password;
}

function wppb_generate_random_username($sentEmail){
	$email = '';
	
	
	for($i=0; $i<strlen($sentEmail); $i++){
		if (($sentEmail[$i] === '@') || ($sentEmail[$i] === '_') || ($sentEmail[$i] === '-') || ($sentEmail[$i] === '.'))
			break;
		else
			$email .= $sentEmail[$i];
	}
	
	
    $username = 'pbUser'.$email.mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));

	while (username_exists($username)){
		 $username = 'pbUser'.$email.mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
    }
	
    return $username;
}

function wppb_add_custom_field_values($POST, $meta){
	/* add the extra profile information */
	$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
	if (file_exists ( $wppb_premium.'extra.fields.php' )){
		$wppbFetchArray = get_option('wppb_custom_fields');
		$randomUserNumber = rand(); //we need a random number to identify file uploads since we don't have a user ID yet
		foreach ( $wppbFetchArray as $key => $value){
			switch ($value['item_type']) {
				case "input":{
					$meta[$value['item_type'].$value['id']] = apply_filters('wppb_register_wpmu_input', esc_attr($POST[$value['item_type'].$value['id']]));
					break;
				}						
				case "hiddenInput":{
					$meta[$value['item_type'].$value['id']] = apply_filters('wppb_register_wpmu_hidden_input', esc_attr($POST[$value['item_type'].$value['id']]));
					break;
				}
				case "checkbox":{
					$checkboxOption = '';
					$value['item_options'] = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_options_translation', $value['item_options']);
					$checkboxValue = explode(',', $value['item_options']);
					foreach($checkboxValue as $thisValue){
						$thisValue = str_replace(' ', '#@space@#', $thisValue); //we need to escape the space-codification we sent earlier in the post
						if (isset($POST[$thisValue.$value['id']])){
							$localValue = str_replace('#@space@#', ' ', $POST[$thisValue.$value['id']]);
							$checkboxOption = $checkboxOption.$localValue.',';
						}
					}							
					
					$meta[$value['item_type'].$value['id']] = apply_filters('wppb_register_wpmu_checkbox', $checkboxOption);
					break;
				}
				case "radio":{
					$meta[$value['item_type'].$value['id']] = apply_filters('wppb_register_wpmu_radio', esc_attr($POST[$value['item_type'].$value['id']]));
					break;
				}
				case "select":{
					$meta[$value['item_type'].$value['id']] = apply_filters('wppb_register_wpmu_select', esc_attr($POST[$value['item_type'].$value['id']]));
					break;
				}
				case "countrySelect":{
					$meta[$value['item_type'].$value['id']] = apply_filters('wppb_register_wpmu_country_select', esc_attr($POST[$value['item_type'].$value['id']]));
					break;
				}
				case "timeZone":{
					$meta[$value['item_type'].$value['id']] = apply_filters('wppb_register_wpmu_timezone_select', esc_attr($POST[$value['item_type'].$value['id']]));
					break;
				}
				case "datepicker":{
					$meta[$value['item_type'].$value['id']] = apply_filters('wppb_register_wpmu_datepicker', esc_attr($POST[$value['item_type'].$value['id']]));
					break;
				}
				case "textarea":{
					$meta[$value['item_type'].$value['id']] = apply_filters('wppb_register_wpmu_textarea', esc_attr($POST[$value['item_type'].$value['id']]));
					break;
				}
				case "upload":{
					$uploadedfile = $value['item_type'].$value['id'];
						
					//first we need to verify if we don't try to upload a 0b or 0 length file
					if ( (basename( $_FILES[$uploadedfile]['name']) != '')){
						
						//second we need to verify if the uploaded file size is less then the set file size in php.ini
						if (($_FILES[$uploadedfile]['size'] < WPPB_SERVER_MAX_UPLOAD_SIZE_BYTE) && ($_FILES[$uploadedfile]['size'] !=0)){
							//we need to prepare the basename of the file, so that ' becomes ` as ' gives an error
							$fileName = basename( $_FILES[$uploadedfile]['name']);
							$finalFileName = '';
							
							for ($i=0; $i < strlen($fileName); $i++){
								if ($fileName[$i] == "'")
									$finalFileName .= '`';
								else $finalFileName .= $fileName[$i];
							}
								
							//create the target path for uploading	
							$wpUploadPath = wp_upload_dir(); // Array of key => value pairs
							$target_path = $wpUploadPath['basedir']."/profile_builder/attachments/";
							$target_path = $target_path . 'wpmuRandomID_'.$randomUserNumber.'_attachment_'. $finalFileName;

							if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
								$upFile = 'wpmuRandomID_'.$randomUserNumber.'_attachment_'. $finalFileName;
								$meta[$value['item_type'].$value['id']] = apply_filters('wppb_register_wpmu_upload', $upFile);
								$meta[$value['item_type'].$value['id'].'_radomUserNumber'] = apply_filters('wppb_register_wpmu_upload_random_user_number', $randomUserNumber);
								$pictureUpload = 'yes';
							}
						}
					}
					break;
				}
				case "avatar":{
					$uploadedfile = $value['item_type'].$value['id'];
					$wpUploadPath = wp_upload_dir(); // Array of key => value pairs
					$target_path_original = $wpUploadPath['basedir']."/profile_builder/avatars/";
					$fileName = $_FILES[$uploadedfile]['name'];
					$finalFileName = '';
							
					for ($i=0; $i < strlen($fileName); $i++){
						if ($fileName[$i] == "'")
							$finalFileName .= '`';
						elseif ($fileName[$i] == ' ')
							$finalFileName .= '_';
						else $finalFileName .= $fileName[$i];
					}
					
					$fileName = $finalFileName;

					$target_path = $target_path_original . 'wpmuRandomID_'.$randomUserNumber.'_originalAvatar_'. $fileName; 	
					
					/* when trying to upload file, be sure it's one of the accepted image file-types */
					if ( (($_FILES[$uploadedfile]['type'] == 'image/jpeg') || ($_FILES[$uploadedfile]['type'] == 'image/jpg') || ($_FILES[$uploadedfile]['type'] == 'image/png') || ($_FILES[$uploadedfile]['type'] == 'image/bmp') || ($_FILES[$uploadedfile]['type'] == 'image/pjpeg') || ($_FILES[$uploadedfile]['type'] == 'image/x-png')) && (($_FILES[$uploadedfile]['size'] < WPPB_SERVER_MAX_UPLOAD_SIZE_BYTE) && ($_FILES[$uploadedfile]['size'] !=0)) ){
						$wp_filetype = wp_check_filetype(basename( $_FILES[$uploadedfile]['name']), null );
						$attachment = array('post_mime_type' => $wp_filetype['type'],
											'post_title' => $fileName, //preg_replace('/\.[^.]+$/', '', basename($_FILES[$uploadedfile]['name'])),
											'post_content' => '',
											'post_status' => 'inherit'
											);


						$attach_id = wp_insert_attachment( $attachment, $target_path);
				
						$upFile = image_downsize( $attach_id, 'thumbnail' );
						$upFile = $upFile[0];
						
						//if file upload succeded			
						if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
							$meta[$value['item_type'].$value['id']] = apply_filters('wppb_register_wpmu_avatar', $upFile);
							$meta[$value['item_type'].$value['id'].'_radomUserNumber'] = apply_filters('wppb_register_wpmu_avatar_random_user_number', $randomUserNumber);
							//will need to call the wppb_resize_avatar once email has been verified
							$avatarUpload = 'yes';
						}
						else $avatarUpload = 'no'; 
					}
					if (($_FILES[$uploadedfile]['type'] == ''))
						$avatarUpload = 'yes';
				
					break;
				}
			}
		}
	}
	
	return $meta;
	
}

/**
 * Activate a signup.
 *
 *
 * @param string $key The activation key provided to the user.
 * @return array An array containing information about the activated user and/or blog
 */
function wppb_activate_signup($key) {
	global $wpdb;
	$bloginfo = get_bloginfo( 'name' );
	$wppb_generalSettings = get_option('wppb_general_settings');

	if ( is_multisite() )
		$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE activation_key = %s", $key) );
	else
		$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."signups WHERE activation_key = %s", $key) );

	if ( empty( $signup ) )
		return $activateUserErrorMessage1 = apply_filters('wppb_register_activate_user_error_message1', '<p class="error">'. __('Invalid activation key!', 'profilebuilder') .'</p>');

	if ( $signup->active )
		if ( empty( $signup->domain ) )
			return $activateUserErrorMessage2 = apply_filters('wppb_register_activate_user_error_message2', '<p class="error">'. __('The user is already active!', 'profilebuilder') .'</p>');

	$meta = unserialize($signup->meta);
	if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email'))
		$user_login = $wpdb->escape($signup->user_email);
	else
		$user_login = $wpdb->escape($signup->user_login);
	$user_email = $wpdb->escape($signup->user_email);
	$password = base64_decode($meta['user_pass']);

	$user_id = username_exists($user_login);

	if ( ! $user_id )
		$user_id = wppb_create_user($user_login, $password, $user_email);
	else
		$user_already_exists = true;

	if ( ! $user_id )
		return $activateUserErrorMessage4 = apply_filters('wppb_register_activate_user_error_message4', '<p class="error">'. __('Could not create user!', 'profilebuilder') .'</p>');
		
	elseif ( isset( $user_already_exists ) )
		return $activateUserErrorMessage5 = apply_filters('wppb_register_activate_user_error_message5', '<p class="error">'. __('That username is already activated!', 'profilebuilder') .'</p>');
	
	else{
		$now = current_time('mysql', true);
		
		if ( is_multisite() )
			$retVal = $wpdb->update( $wpdb->signups, array('active' => 1, 'activated' => $now), array('activation_key' => $key) );
		else
			$retVal = $wpdb->update( $wpdb->prefix.'signups', array('active' => 1, 'activated' => $now), array('activation_key' => $key) );

		

		wppb_add_meta_to_user_on_activation($user_id, '', $meta);
		
		// if admin approval is activated, then block the user untill he gets approved
		$wppb_generalSettings = get_option('wppb_general_settings');
		if($wppb_generalSettings['adminApproval'] == 'yes'){
			wp_set_object_terms( $user_id, array( 'unapproved' ), 'user_status', false);
			clean_object_term_cache( $user_id, 'user_status' );
		}
		
		wppb_notify_user_registration_email($bloginfo, $user_login, $user_email, 'sending', $password, $wppb_generalSettings['adminApproval']);
		
		do_action('wppb_activate_user', $user_id, $password, $meta);
		
		if ($retVal)
			return $registerFilterArray['successfullUserActivation'] = apply_filters('wppb_register_successfull_user_activation', '<p class="success">'. __('The user was successfully activated.', 'profilebuilder') .'</p><!-- .success -->');
		else
			return $registerFilterArray['successfullUserActivationFail'] = apply_filters('wppb_register_failed_user_activation', '<p class="error">'. __('There was an error while trying to activate the user.', 'profilebuilder') .'</p><!-- .error -->');
	}		
}


//function to display the registration page
function wppb_front_end_register($atts){
	ob_start();
	global $current_user;
	global $wp_roles;
	global $wpdb;
	global $error;	
	global $wppb_shortcode_on_front;
	
	//get required and shown fields
	$wppb_defaultOptions = get_option('wppb_default_settings');
	
	//get "login with" setting
	$wppb_generalSettings = get_option('wppb_general_settings');
	
	$wppb_shortcode_on_front = true;
	$agreed = true;
	$new_user = 'no';
	$multisite_message = false;
	$registerFilterArray = array();
	$registerFilterArray2 = array();
	$uploadExt = array();
	$extraFieldsErrorHolder = array();  //we will use this array to store the ID's of the extra-fields left uncompleted
	get_currentuserinfo();

	/* variables used to verify if all required fields were submitted*/
	$firstnameComplete = 'yes';
	$lastnameComplete = 'yes';
	$nicknameComplete = 'yes';
	$websiteComplete = 'yes';
	$aimComplete = 'yes';
	$yahooComplete = 'yes';
	$jabberComplete = 'yes';
	$bioComplete = 'yes';
	$roleComplete = 'yes';
	$cityComplete = 'yes';
	/* END variables used to verify if all required fields were submitted*/
	
	

	/* Check if users can register. */
	$registration = get_option( 'users_can_register' );
	$registration = apply_filters ( 'wppb_register_setting_override', $registration);
	
	
	//fallback if the file was largen then post_max_size, case in which no errors can be saved in $_FILES[fileName]['error']	
	if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
		$registerFilterArray['noPostError'] = '<p class="error">' . sprintf(__( 'The information size you were trying to submit was larger than %1$sb!<br/>This is usually caused by a large file(s) trying to be uploaded.<br/>Since it was also larger than %2$sb no additional information is available.<br/>The user was NOT created!', 'profilebuilder'), WPPB_SERVER_MAX_UPLOAD_SIZE_MEGA, WPPB_SERVER_MAX_POST_SIZE_MEGA) . '</p>';
		echo  $registerFilterArray['noPostError'] = apply_filters('wppb_register_no_post_error_message', $registerFilterArray['noPostError'], WPPB_SERVER_MAX_UPLOAD_SIZE_MEGA, WPPB_SERVER_MAX_POST_SIZE_MEGA);
	}
	
	/* If user registered, input info. */
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'adduser' && wp_verify_nonce($_POST['register_nonce_field'],'verify_true_registration') && ($_POST['formName'] == 'register') ) {
		//global $wp_roles;
		
		//get value sent in the shortcode as parameter, use default if not set
	/*	$default_role = get_option( 'default_role' );
		extract(shortcode_atts(array('role' => $default_role), $atts));

		//check if the specified role exists in the database, else fall back to the "safe-zone"
		$aprovedRole = ( ($role == $default_role) || get_role($role) ) ? $role : $default_role;*/
	
		/* preset the values in case some are not submitted */
		$user_pass = '';
		if (isset($_POST['passw1']))
			$user_pass = esc_attr( $_POST['passw1'] );
		$email = '';
		if (isset($_POST['email']))
			$email = trim ($_POST['email']);
		if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email')){
			$user_name = wppb_generate_random_username($email);
		}else{
			$user_name = '';
			if (isset($_POST['user_name']))
				$user_name = trim ($_POST['user_name']);
		}
		$first_name = '';
		if (isset($_POST['first_name']))
			$first_name = trim ($_POST['first_name']);
		$last_name = '';
		if (isset($_POST['last_name']))
			$last_name = trim ($_POST['last_name']);
		$nickname = '';
		if (isset($_POST['nickname'])){
			//the field is filled by the user upon registration
			$nickname = trim ($_POST['nickname']);
		}elseif (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email')){
			//in case the nickname field is hidden, and the login with email is active
			$nickname = $email;
		}else{
			//in case the nickname field is hidden, but login is done with username
			$nickname = $user_name;
		}
		$website = '';
		if (isset($_POST['website']))
			$website = trim ($_POST['website']);
		$role = '';
		if (isset($_POST['role']))
			$role = trim ($_POST['role']);
		$city = '';
		if (isset($_POST['city']))
			$city = trim ($_POST['city']);		
		$aim = '';
		if (isset($_POST['aim']))
			$aim = trim ($_POST['aim']);
		$yim = '';
		if (isset($_POST['yim']))
			$yim = trim ($_POST['yim']);
		$jabber = '';
		if (isset($_POST['jabber']))
			$jabber = trim ($_POST['jabber']);
		$description = '';
		if (isset($_POST['description']))
			$description = trim ($_POST['description']);
		
		/* use filters to modify (if needed) the posted data before creating the user-data */
		$user_pass = apply_filters('wppb_register_posted_password', $user_pass);
		$user_name = apply_filters('wppb_register_posted_username', $user_name);
		$first_name = apply_filters('wppb_register_posted_first_name', $first_name);
		$last_name = apply_filters('wppb_register_posted_last_name', $last_name);
		$nickname = apply_filters('wppb_register_posted_nickname', $nickname);
		$email = apply_filters('wppb_register_posted_email', $email);
		$role = apply_filters('wppb_register_posted_role', $role);
		$city = apply_filters('wppb_register_posted_city', $city);
		$website = apply_filters('wppb_register_posted_website', $website);
		$aim = apply_filters('wppb_register_posted_aim', $aim);
		$yim = apply_filters('wppb_register_posted_yahoo', $yim);
		$jabber = apply_filters('wppb_register_posted_jabber', $jabber);
		$description = apply_filters('wppb_register_posted_bio', $description);
		/* END use filters to modify (if needed) the posted data before creating the user-data */

	if (isset($_POST['role'])) {	
$aprovedRole =$_POST['role'];
		} else {
				//get value sent in the shortcode as parameter, use default if not set
		$default_role = get_option( 'default_role' );
		extract(shortcode_atts(array('role' => $default_role), $atts));

		//check if the specified role exists in the database, else fall back to the "safe-zone"
		$aprovedRole = ( ($role == $default_role) || get_role($role) ) ? $role : $default_role;
		}

		
		$userdata = array(
			'user_pass' => $user_pass,
			'user_login' => esc_attr( $user_name ),
			'first_name' => esc_attr( $first_name ),
			'last_name' => esc_attr( $last_name ),
			'nickname' => esc_attr( $nickname ),
			'user_email' => esc_attr( $email ),
			'city' => esc_attr( $city ),
			'user_url' => esc_attr( $website ),
			'aim' => esc_attr( $aim ),
			'yim' => esc_attr( $yim ),
			'jabber' => esc_attr( $jabber ),
			'description' => esc_attr( $description ),
			'role' => $aprovedRole);
		$userdata = apply_filters('wppb_register_userdata', $userdata);
		
		//check if the user agreed to the terms and conditions (if it was set)
		$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
			if (file_exists ( $wppb_premium.'extra.fields.php' )){
				$wppbFetchArray = get_option('wppb_custom_fields');
				foreach ( $wppbFetchArray as $key => $value){
					switch ($value['item_type']) {
						case "agreeToTerms":{
							$agreed = false;
							if ( (isset($_POST[$value['item_type'].$value['id']] )) && ($_POST[$value['item_type'].$value['id']] == 'agree'))
								$agreed = true;
							break;
						}
					}
					
					// add filters for all the custom fields
					$_POST[$value['item_type'].$value['id']] = apply_filters('wppb_register_'.$value['item_type'].$value['id'].'_general_filter', $_POST[$value['item_type'].$value['id']]);
				}
			}
		
		$registerFilterArray['extraError'] = ''; //this is for creating extra error message and bypassing registration
		$registerFilterArray['extraError'] = apply_filters('wppb_register_extra_error', $registerFilterArray['extraError']);
		
		/* check if all the required fields were completed */
		if($wppb_defaultOptions['firstname'] == 'show'){
			if (($wppb_defaultOptions['firstnameRequired'] == 'yes') && (trim($_POST['first_name']) == ''))
				$firstnameComplete = 'no';
		}
		
		if($wppb_defaultOptions['lastname'] == 'show'){
			if (($wppb_defaultOptions['lastnameRequired'] == 'yes') && (trim($_POST['last_name']) == ''))
				$lastnameComplete = 'no';
		}
		
		if($wppb_defaultOptions['nickname'] == 'show'){
			if (($wppb_defaultOptions['nicknameRequired'] == 'yes') && (trim($_POST['nickname']) == ''))
				$nicknameComplete = 'no';
		}
		if($wppb_defaultOptions['role'] == 'show'){
			if (($wppb_defaultOptions['roleRequired'] == 'yes') && (trim($_POST['role']) == ''))
				$roleComplete = 'no';
		}
		if($wppb_defaultOptions['city'] == 'show'){
			if (($wppb_defaultOptions['cityRequired'] == 'yes') && (trim($_POST['city']) == ''))
				$cityComplete = 'no';
		}
		
		if($wppb_defaultOptions['website'] == 'show'){
			if (($wppb_defaultOptions['websiteRequired'] == 'yes') && (trim($_POST['website']) == ''))
				$websiteComplete = 'no';
		}
		
		if($wppb_defaultOptions['aim'] == 'show'){
			if (($wppb_defaultOptions['aimRequired'] == 'yes') && (trim($_POST['aim']) == ''))
				$aimComplete = 'no';
		}
		
		if($wppb_defaultOptions['yahoo'] == 'show'){
			if (($wppb_defaultOptions['yahooRequired'] == 'yes') && (trim($_POST['yahoo']) == ''))
				$yahooComplete = 'no';
		}
		
		if($wppb_defaultOptions['jabber'] == 'show'){
			if (($wppb_defaultOptions['jabberRequired'] == 'yes') && (trim($_POST['jabber']) == ''))
				$jabberComplete = 'no';
		}
		
		if($wppb_defaultOptions['bio'] == 'show'){
			if (($wppb_defaultOptions['bioRequired'] == 'yes') && (trim($_POST['description']) == ''))
				$bioComplete = 'no';
		}
		
		// check the extra fields also
		$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
		if (file_exists ( $wppb_premium.'extra.fields.php' )){
			$wppbFetchArray = get_option('wppb_custom_fields');
			foreach ( $wppbFetchArray as $key => $value){
				switch ($value['item_type']) {
					case "input":{
						$_POST[$value['item_type'].$value['id']] = apply_filters('wppb_register_input_custom_field_'.$value['id'], $_POST[$value['item_type'].$value['id']]);
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;					
					}
					case "checkbox":{
						$checkboxOption = '';
						$value['item_options'] = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_options_translation', $value['item_options']);
						$checkboxValue = explode(',', $value['item_options']);
						foreach($checkboxValue as $thisValue){
							$thisValue = str_replace(' ', '#@space@#', $thisValue); //we need to escape the space-codification we sent earlier in the post
							if (isset($_POST[$thisValue.$value['id']])){
								$localValue = str_replace('#@space@#', ' ', $_POST[$thisValue.$value['id']]);
								$checkboxOption = $checkboxOption.$localValue.',';
							}
						}
						
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($checkboxOption) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "radio":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "select":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "countrySelect":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "timeZone":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "datepicker":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "textarea":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "upload":{
						$uploadedfile = $value['item_type'].$value['id'];

						if ( (basename( $_FILES[$uploadedfile]['name']) == '')){
							if (isset($value['item_required'])){
								if ($value['item_required'] == 'yes')
										array_push($extraFieldsErrorHolder, $value['id']);
							}
						}elseif ( (basename( $_FILES[$uploadedfile]['name']) != '')){
							//get allowed file types
							if (($value['item_options'] != NULL) || ($value['item_options'] != '')){
								$allFiles = false;
								$extensions = explode(',', $value['item_options']);
								foreach($extensions as $key2 => $value2)
									$extensions[$key2] = trim($value2);
							}else 
								$allFiles = true;
							
							$thisFileExtStart = strrpos($_FILES[$uploadedfile]['name'], '.');
							$thisFileExt = substr($_FILES[$uploadedfile]['name'], $thisFileExtStart);
								
							if (($allFiles == false) && (!in_array($thisFileExt, $extensions))){
								array_push($uploadExt, basename( $_FILES[$uploadedfile]['name']));
								$allowedExtensions = '';
								(int)$nrOfExt = count($extensions)-2;
								foreach($extensions as $key2 => $value2){
									$allowedExtensions .= $value2;
									if ($key2 <= $nrOfExt)
										$allowedExtensions .= ', ';
										
								}
							}
						}
						break;
					}
					case "avatar":{
						$uploadedfile = $value['item_type'].$value['id'];

						if ( (basename( $_FILES[$uploadedfile]['name']) == '')){
							if (($_FILES[$uploadedfile]['type'] != 'image/jpeg') || ($_FILES[$uploadedfile]['type'] != 'image/jpg') || ($_FILES[$uploadedfile]['type'] != 'image/png') || ($_FILES[$uploadedfile]['type'] != 'image/bmp') || ($_FILES[$uploadedfile]['type'] != 'image/pjpeg') || ($_FILES[$uploadedfile]['type'] != 'image/x-png'))
								if (isset($value['item_required'])){
									if ($value['item_required'] == 'yes')
											array_push($extraFieldsErrorHolder, $value['id']);
								}
						}
						break;
					}
					case "agreeToTerms":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if ($_POST[$value['item_type'].$value['id']] == NULL)
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
				}
			}
		}
		
		/* END check if all the required fields were completed */
		if ($registerFilterArray['extraError'] != '')
			$error = $registerFilterArray['extraError'];
		elseif ( !$userdata['user_login'] ){
			if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email')){
			}else
				$error = apply_filters('wppb_register_userlogin_error1', __('A username is required for registration.', 'profilebuilder'));
		}elseif ( username_exists($userdata['user_login']) ){
			if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email')){
			}else
				$error = apply_filters('wppb_register_userlogin_error2', __('Sorry, that username already exists!', 'profilebuilder'));
		}elseif ( !is_email($userdata['user_email'], true) )
			$error = apply_filters('wppb_register_useremail_error1', __('You must enter a valid email address.', 'profilebuilder'));
		elseif ( email_exists($userdata['user_email']) )
			$error = apply_filters('wppb_register_useremail_error2', __('Sorry, that email address is already used!', 'profilebuilder'));
		elseif (( empty($_POST['passw1'] ) || empty( $_POST['passw2'] )) || ( $_POST['passw1'] != $_POST['passw2'] )){
			if ( empty($_POST['passw1'] ) || empty( $_POST['passw2'] ))                                                    //verify if the user has completed both password fields
				$error = apply_filters('wppb_register_userpass_error1', __('You didn\'t complete one of the password-fields!', 'profilebuilder'));
			elseif ( $_POST['passw1'] != $_POST['passw2'] )																   //verify if the the password and the retyped password are a match
				$error = apply_filters('wppb_register_userpass_error2', __('The entered passwords don\'t match!', 'profilebuilder'));
		}elseif(count($uploadExt) > 0){
			$error ='<p class="semi-saved">'.
						__('There was an error while trying to upload the following attachment(s)', 'profilebuilder') .': <span class="error">';
						foreach ($uploadExt as $key5 => $name5){
							$lastOne++;
							$error .= $name5;
							if (count($uploadExt)-$lastOne > 0) 
								$error .= ';<span style="padding-left:10px"></span>';
						}
						$error .= '</span><br/>'. __('Only files with the following extension(s) can be uploaded:', 'profilebuilder') .' <span class="error">'.$allowedExtensions.'</span><br/><span class="error">'. __('The account was NOT created!', 'profilebuilder') .'</span>
					</p>';
		}
		elseif ( $agreed == false )
			$error = __('You must agree to the terms and conditions before registering!', 'profilebuilder');
		elseif(($firstnameComplete == 'no' || $lastnameComplete == 'no' ||	$nicknameComplete == 'no' || $websiteComplete == 'no' || $aimComplete == 'no' || $yahooComplete == 'no' ||	$jabberComplete == 'no' ||	$bioComplete == 'no'  ||	$roleComplete == 'no'  ||	$cityComplete == 'no' ) || !empty($extraFieldsErrorHolder))
			$error = __('The account was NOT created!', 'profilebuilder') .'<br/>'. __('(Several required fields were left uncompleted)', 'profilebuilder');
		else{
			$registered_name = $_POST['user_name'];
			
			//register the user normally if it is not a multi-site installation
			if ( !is_multisite() ){
				$wppb_generalSettings = get_option('wppb_general_settings');
				
				if ($wppb_generalSettings['emailConfirmation'] == 'yes'){
					$foundError = false;
				
					if ( is_multisite() )
						$userSignup = $wpdb->get_results("SELECT * FROM $wpdb->signups WHERE user_login='".$userdata['user_login']."' OR user_email='".$userdata['user_email']."'");
					else
						$userSignup = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."signups WHERE user_login='".$userdata['user_login']."' OR user_email='".$userdata['user_email']."'");
						
					if (trim($userSignup[0]->user_login) == $userdata['user_login']){
						$foundError = true;
						$error = __('This username is already reserved to be used soon.', 'profilebuilder') .'<br/>'. __('Please try a different one!', 'profilebuilder');
						
					}elseif (trim($userSignup[0]->user_email) == $userdata['user_email']){
						$foundError = true;
						$error = __('This email address is already reserved to be used soon.', 'profilebuilder') .'<br/>'. __('Please try a different one!', 'profilebuilder');
					}
					
				
					if ( $foundError ){
					}else{
						$new_user = 'yes';
						$multisite_message = true;

						$meta = array(
							'user_pass' => base64_encode($userdata['user_pass']),
							'first_name' => $userdata['first_name'],
							'last_name' => $userdata['last_name'],
							'nickname' => $userdata['nickname'],
							'user_url' => $userdata['user_url'],
							'aim' => $userdata['aim'],
							'yim' => $userdata['yim'],
							'jabber' => $userdata['jabber'],
							'description' => $userdata['description'],
							'role' => $userdata['role'],
							'city' => $userdata['city']
						);
						
						$meta = wppb_add_custom_field_values($_POST, $meta);
						
						wppb_signup_user( $userdata['user_login'], $userdata['user_email'], $meta );

					}
				
				
				
				}else{
				
					$new_user = wp_insert_user( $userdata );
					
					/* add the extra profile information */
					$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
					if (file_exists ( $wppb_premium.'extra.fields.php' )){
						$wppbFetchArray = get_option('wppb_custom_fields');
						foreach ( $wppbFetchArray as $key => $value){
							switch ($value['item_type']) {
								case "input":{
									add_user_meta( $new_user, $value['item_metaName'], esc_attr($_POST[$value['item_type'].$value['id']]) );
									break;
								}						
								case "hiddenInput":{
									add_user_meta( $new_user, $value['item_metaName'], esc_attr($_POST[$value['item_type'].$value['id']]) );
									break;
								}
								case "checkbox":{
									$checkboxOption = '';
									$value['item_options'] = wppb_icl_t('plugin profile-builder-pro', 'custom_field_'.$id.'_options_translation', $value['item_options']);
									$checkboxValue = explode(',', $value['item_options']);
									foreach($checkboxValue as $thisValue){
										$thisValue = str_replace(' ', '#@space@#', $thisValue); //we need to escape the space-codification we sent earlier in the post
										if (isset($_POST[$thisValue.$value['id']])){
											$localValue = str_replace('#@space@#', ' ', $_POST[$thisValue.$value['id']]);
											$checkboxOption = $checkboxOption.$localValue.',';
										}
									}							
									
									add_user_meta( $new_user, $value['item_metaName'], $checkboxOption );
									break;
								}
								case "radio":{
									add_user_meta( $new_user, $value['item_metaName'], $_POST[$value['item_type'].$value['id']] );
									break;
								}
								case "select":{
									add_user_meta( $new_user, $value['item_metaName'], $_POST[$value['item_type'].$value['id']] );
									break;
								}
								case "countrySelect":{
									update_user_meta( $new_user, $value['item_metaName'], $_POST[$value['item_type'].$value['id']] );
									break;
								}
								case "timeZone":{
									update_user_meta( $new_user, $value['item_metaName'], $_POST[$value['item_type'].$value['id']] );
									break;
								}
								case "datepicker":{
									update_user_meta( $new_user, $value['item_metaName'], $_POST[$value['item_type'].$value['id']] );
									break;
								}
								case "textarea":{
									add_user_meta( $new_user, $value['item_metaName'], esc_attr($_POST[$value['item_type'].$value['id']]) );
									break;
								}
								case "upload":{
									$uploadedfile = $value['item_type'].$value['id'];
										
									//first we need to verify if we don't try to upload a 0b or 0 length file
									if ( (basename( $_FILES[$uploadedfile]['name']) != '')){
										
										//second we need to verify if the uploaded file size is less then the set file size in php.ini
										if (($_FILES[$uploadedfile]['size'] < WPPB_SERVER_MAX_UPLOAD_SIZE_BYTE) && ($_FILES[$uploadedfile]['size'] !=0)){
											//we need to prepare the basename of the file, so that ' becomes ` as ' gives an error
											$fileName = basename( $_FILES[$uploadedfile]['name']);
											$finalFileName = '';
											
											for ($i=0; $i < strlen($fileName); $i++){
												if ($fileName[$i] == "'")
													$finalFileName .= '`';
												else $finalFileName .= $fileName[$i];
											}
												
											//create the target path for uploading	
											$wpUploadPath = wp_upload_dir(); // Array of key => value pairs
											$target_path = $wpUploadPath['basedir']."/profile_builder/attachments/";
											$target_path = $target_path . 'userID_'.$new_user.'_attachment_'. $finalFileName;

											if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
												//$upFile = get_bloginfo('home').'/'.$target_path;
												$upFile = $wpUploadPath['baseurl'].'/profile_builder/attachments/userID_'.$new_user.'_attachment_'. $finalFileName;
												add_user_meta( $new_user, $value['item_metaName'], $upFile );
												$pictureUpload = 'yes';
											}
										}
									}
									break;
								}
								case "avatar":{

									$uploadedfile = $value['item_type'].$value['id'];
									$wpUploadPath = wp_upload_dir(); // Array of key => value pairs
									$target_path_original = $wpUploadPath['basedir']."/profile_builder/avatars/";
									$fileName = $_FILES[$uploadedfile]['name'];
									$finalFileName = '';
											
									for ($i=0; $i < strlen($fileName); $i++){
										if ($fileName[$i] == "'")
											$finalFileName .= '`';
										elseif ($fileName[$i] == ' ')
											$finalFileName .= '_';
										else $finalFileName .= $fileName[$i];
									}
									
									$fileName = $finalFileName;

									$target_path = $target_path_original . 'userID_'.$new_user.'_originalAvatar_'. $fileName; 	
									
									/* when trying to upload file, be sure it's one of the accepted image file-types */
									if ( (($_FILES[$uploadedfile]['type'] == 'image/jpeg') || ($_FILES[$uploadedfile]['type'] == 'image/jpg') || ($_FILES[$uploadedfile]['type'] == 'image/png') || ($_FILES[$uploadedfile]['type'] == 'image/bmp') || ($_FILES[$uploadedfile]['type'] == 'image/pjpeg') || ($_FILES[$uploadedfile]['type'] == 'image/x-png')) && (($_FILES[$uploadedfile]['size'] < WPPB_SERVER_MAX_UPLOAD_SIZE_BYTE) && ($_FILES[$uploadedfile]['size'] !=0)) ){
										$wp_filetype = wp_check_filetype(basename( $_FILES[$uploadedfile]['name']), null );
										$attachment = array('post_mime_type' => $wp_filetype['type'],
															'post_title' => $fileName, //preg_replace('/\.[^.]+$/', '', basename($_FILES[$uploadedfile]['name'])),
															'post_content' => '',
															'post_status' => 'inherit'
															);


										$attach_id = wp_insert_attachment( $attachment, $target_path);
								
										$upFile = image_downsize( $attach_id, 'thumbnail' );
										$upFile = $upFile[0];
										
										//if file upload succeded			
										if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
											add_user_meta( $new_user, $value['item_metaName'], $upFile );
											wppb_resize_avatar($new_user);
											$avatarUpload = 'yes';
										}
										else $avatarUpload = 'no'; 
									}
									if (($_FILES[$uploadedfile]['type'] == ''))
										$avatarUpload = 'yes';
								
									break;
								}
							}
						}
					}

					// if admin approval is activated, then block the user untill he gets approved
					$wppb_generalSettings = get_option('wppb_general_settings', 'not_found');
					if ($wppb_generalSettings != 'not_found')
						if($wppb_generalSettings['adminApproval'] == 'yes'){
							wp_set_object_terms( $new_user, array( 'unapproved' ), 'user_status', false);
							clean_object_term_cache( $new_user, 'user_status' );
						}
					
					// send an email to the admin, and - if selected - to the user also.
					$bloginfo = get_bloginfo( 'name' );
					$sentEmailStatus = wppb_notify_user_registration_email($bloginfo, esc_attr($_POST['user_name']), esc_attr($_POST['email']), $_POST['send_credentials_via_email'], $_POST['passw1'], $wppb_generalSettings['adminApproval']);
					
				}
			}elseif ( is_multisite() ){
			
				//validate username and email
				$validationRes = wpmu_validate_user_signup($userdata['user_login'], $userdata['user_email']);
				$error = apply_filters('wppb_register_wpmu_registration_error', $validationRes['errors']->get_error_message());
				if ( trim($error) != '' ){
				}else{
					$new_user = 'yes';
					$multisite_message = true;

					$meta = array(
						'user_pass' => base64_encode($userdata['user_pass']),
						'first_name' => $userdata['first_name'],
						'last_name' => $userdata['last_name'],
						'nickname' => $userdata['nickname'],
						'user_url' => $userdata['user_url'],
						'aim' => $userdata['aim'],
						'yim' => $userdata['yim'],
						'jabber' => $userdata['jabber'],
						'description' => $userdata['description'],
						'role' => $userdata['role']
					);
					
					$meta = wppb_add_custom_field_values($_POST, $meta);
					
					wppb_signup_user( $userdata['user_login'], $userdata['user_email'], $meta );

				}
			}
			
		}
	}

?>
 <div class="login-container">   
<h2>Sign Up</h2>
<div class="login-form">
	<div class="wppb_holder" id="wppb_register">
<?php 	

		if ( is_user_logged_in() && !current_user_can( 'create_users' ) ) :

		global $user_ID; 
		$login = get_userdata( $user_ID );
		if($login->display_name == ''){ 
			$login->display_name = $login->user_login;
		}
			$registerFilterArray['loginLogoutError'] = '
				<p class="log-in-out alert">'. __('You are logged in as', 'profilebuilder') .' <a href="'.get_author_posts_url( $login->ID ).'" title="'.$login->display_name.'">'.$login->display_name.'</a>. '. __('You don\'t need another account.', 'profilebuilder') .' <a href="'.wp_logout_url(get_permalink()).'" title="'. __('Log out of this account.', 'profilebuilder') .'">'. __('Logout', 'profilebuilder') .'  &raquo;</a></p><!-- .log-in-out .alert -->';
			$registerFilterArray['loginLogoutError'] = apply_filters('wppb_register_have_account_alert', $registerFilterArray['loginLogoutError'], $login->ID);
			echo $registerFilterArray['loginLogoutError'];
			
		elseif ( $new_user != 'no' ) :
					if ( current_user_can( 'create_users' ) ){
					
						if ($multisite_message){
							$registerFilterArray['wpmuRegistrationMessage1'] = '<p class="success">' . sprintf(__( 'An email has been sent to %1$s with information on how to activate his/her account.', 'profilebuilder'), $userdata['user_email']) . '</p><!-- .success -->';
							echo $registerFilterArray['registrationMessage1'] = apply_filters('wppb_wpmu_register_account_created1', $registerFilterArray['wpmuRegistrationMessage1'], $registered_name, $userdata['user_email']);
						
						}else{
							$registerFilterArray['registrationMessage1'] = '<p class="success">' . sprintf(__( 'A user account has been created for %1$s.', 'profilebuilder'), $registered_name) . '</p><!-- .success -->';
							echo $registerFilterArray['registrationMessage1'] = apply_filters('wppb_register_account_created1', $registerFilterArray['registrationMessage1'], $registered_name);
						}
						
						$redirectLink = wppb_curpageurl();
						
						if (file_exists ( WPPB_PLUGIN_DIR . '/premium/addons/addon.php' )){
							//check to see if the redirecting addon is present and activated
							$wppb_addon_settings = get_option('wppb_addon_settings');
							if ($wppb_addon_settings['wppb_customRedirect'] == 'show'){
								//check to see if the redirect location is not an empty string and is activated
								$customRedirectSettings = get_option('customRedirectSettings');
								if ((trim($customRedirectSettings['afterRegisterTarget']) != '') && ($customRedirectSettings['afterRegister'] == 'yes')){
									$redirectLink = trim($customRedirectSettings['afterRegisterTarget']);
									if (wppb_check_missing_http($redirectLink))
										$redirectLink = 'http://'. $redirectLink;
								}
							}
						}
						
						$registerFilterArray['redirectMessage1'] = '<font id="messageTextColor">' . sprintf(__( 'You will soon be redirected automatically. If you see this page for more than 3 seconds, please click %1$s.%2$s', 'profilebuilder'), '<a href="'.$redirectLink.'">'.__('here', 'profilebuilder').'</a>', '<meta http-equiv="Refresh" content="3;url='.$redirectLink.'" />') . '</font><br/><br/>';
						echo $registerFilterArray['redirectMessage1'] = apply_filters('wppb_register_redirect_after_creation1', $registerFilterArray['redirectMessage1'], $redirectLink);		
						
					}else{
					
						if ($multisite_message){
							$registerFilterArray['wpmuRegistrationMessage2'] = '<p class="success">'. __('An email has been sent to you with information on how to activate your account.', 'profilebuilder') .'</p><!-- .success -->';
							echo $registerFilterArray['wpmuRegistrationMessage2'] = apply_filters('wppb_register_account_created2', $registerFilterArray['wpmuRegistrationMessage2'], $registered_name);
						
						}else{
							$registerFilterArray['registrationMessage2'] = '<p class="success">' . sprintf(__( 'Thank you for registering %1$s.', 'profilebuilder'), $registered_name) .'</p><!-- .success -->';
							echo $registerFilterArray['registrationMessage2'] = apply_filters('wppb_register_account_created2', $registerFilterArray['registrationMessage2'], $registered_name);
						}
						
						$redirectLink = wppb_curpageurl();
						
						if (file_exists ( WPPB_PLUGIN_DIR . '/premium/addons/addon.php' )){
							//check to see if the redirecting addon is present and activated
							$wppb_addon_settings = get_option('wppb_addon_settings');
							if ($wppb_addon_settings['wppb_customRedirect'] == 'show'){
								//check to see if the redirect location is not an empty string and is activated
								$customRedirectSettings = get_option('customRedirectSettings');
								if ((trim($customRedirectSettings['afterRegisterTarget']) != '') && ($customRedirectSettings['afterRegister'] == 'yes')){
									$redirectLink = trim($customRedirectSettings['afterRegisterTarget']);
									if (wppb_check_missing_http($redirectLink))
										$redirectLink = 'http://'. $redirectLink;
								}
							}
						}
						
						$registerFilterArray['redirectMessage2'] = '<font id="messageTextColor">' . sprintf(__( 'You will soon be redirected automatically. If you see this page for more than 3 seconds, please click %1$s.%2$s', 'profilebuilder'), '<a href="'.$redirectLink.'">'.__('here', 'profilebuilder').'</a>', '<meta http-equiv="Refresh" content="3;url='.$redirectLink.'" />') . '</font><br/><br/>';
						echo $registerFilterArray['redirectMessage2'] = apply_filters('wppb_register_redirect_after_creation2', $registerFilterArray['redirectMessage2'], $redirectLink);
					}

			
				if(isset($_POST['send_credentials_via_email'])){
					if ($sentEmailStatus == 1){
						$registerFilterArray['emailMessage1'] = '<p class="error">'. __('An error occured while trying to send the notification email.', 'profilebuilder') .'</p><!-- .error -->';
						$registerFilterArray['emailMessage1'] = apply_filters('wppb_register_send_notification_email_fail', $registerFilterArray['emailMessage1']);
						echo $registerFilterArray['emailMessage1'];
					}elseif ($sentEmailStatus == 2){
						if ($multisite_message){
							$registerFilterArray['wpmuEmailMessage2'] = '<p class="success">'. __('An email containing activation instructions was successfully sent.', 'profilebuilder') .'</p><!-- .success -->';
							$registerFilterArray['wpmuEmailMessage2'] = apply_filters('wppb_register_send_notification_email_success', $registerFilterArray['wpmuEmailMessage2']);
							echo $registerFilterArray['wpmuEmailMessage2'];

						}else{
							$registerFilterArray['emailMessage2'] = '<p class="success">'. __('An email containing the username and password was successfully sent.', 'profilebuilder') .'</p><!-- .success -->';
							$registerFilterArray['emailMessage2'] = apply_filters('wppb_register_send_notification_email_success', $registerFilterArray['emailMessage2']);
							echo $registerFilterArray['emailMessage2'];
						}
					}
				}
?>
<?php			
			else :
				if ( $error ) : 
					$registerFilterArray['errorMessage'] = '<p class="error">'. $error .'</p><!-- .error -->';
					$registerFilterArray['errorMessage'] = apply_filters('wppb_register_error_messaging', $registerFilterArray['errorMessage'], $error);
					echo $registerFilterArray['errorMessage'];
				endif;
			
				if ( current_user_can( 'create_users' ) && $registration ) :
					$registerFilterArray['alertMessage1'] = '<p class="alert">'. __('Users can register themselves or you can manually create users here.', 'profilebuilder') .'</p><!-- .alert -->';
					$registerFilterArray['alertMessage1'] = apply_filters('wppb_register_alert_messaging1', $registerFilterArray['alertMessage1']);
					echo $registerFilterArray['alertMessage1'];					
					
				elseif ( current_user_can( 'create_users' ) ) :
					$registerFilterArray['alertMessage2'] = '<p class="alert">'. __('Users cannot currently register themselves, but you can manually create users here.', 'profilebuilder') .'</p><!-- .alert -->';
					$registerFilterArray['alertMessage2'] = apply_filters('wppb_register_alert_messaging2', $registerFilterArray['alertMessage2']);
					echo $registerFilterArray['alertMessage2'];
					
				elseif ( !current_user_can( 'create_users' ) && !$registration) :
					$registerFilterArray['alertMessage3'] = '<p class="alert">'. __('Only an administrator can add new users.', 'profilebuilder') .'</p><!-- .alert -->';
					$registerFilterArray['alertMessage3'] = apply_filters('wppb_register_alert_messaging3', $registerFilterArray['alertMessage3']);
					echo $registerFilterArray['alertMessage3'];				
				endif;

				if ( $registration || current_user_can( 'create_users' ) ) :
					/* use this action hook to add extra content before the register form. */
					do_action( 'wppb_before_register_fields' );
?>
					<form enctype="multipart/form-data" method="post" id="adduser" class="user-forms" action="<?php wppb_curpageurl(); ?>">

<?php 					
						echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.WPPB_SERVER_MAX_UPLOAD_SIZE_BYTE.'" /><!-- set the MAX_FILE_SIZE to the server\'s current max upload size in bytes -->'; 
	
						//$registerFilterArray2['name1'] = '<p class="registerNameHeading"><strong>'. __('Name', 'profilebuilder') .'</strong></p>';
						$registerFilterArray2['name1'] = apply_filters('wppb_register_content_name1', $registerFilterArray2['name1']);
						
						if ($wppb_defaultOptions['username'] == 'show'){
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['usernameRequired'] == 'yes'){
								$errorMark = '<font color="red" title="This field is required for registration.">*</font>';
								if (isset($_POST['user_name'])){
									if (trim($_POST['user_name']) == ''){
										$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'.__('This field must be filled out before registering (It was marked as required by the administrator)', 'profilebuilder').'"/>';
										$errorVar = ' errorHolder';
									}
								}
							}
							
							if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email')){
								if ($wppb_defaultOptions['email'] == 'show'){
									$errorVar = '';
									$errorMark = '';
									if ($wppb_defaultOptions['emailRequired'] == 'yes'){
										$errorMark = '<font color="red" title="'.__('This field is marked as required by the administrator', 'profilebuilder').'">*</font>';
										if (isset($_POST['email'])){
											if ( (trim($_POST['email']) == '') || (!is_email(trim($_POST['email']))) ){
												$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="This field is required for registration."/>';
												$errorVar = ' errorHolder';
											}
										}
									}
									
									$localVar = '';
									if (isset($_POST['email']))
										$localVar = $_POST['email'];
									$registerFilterArray2['name2'] = '
										<p class="form-email'.$errorVar.'">
											<label for="email">'. __('E-mail', 'profilebuilder') .$errorMark.'</label>
											<input class="text-input" name="email" type="text" id="email" value="'.trim($localVar).'" />
										</p><!-- .form-email -->';
									$registerFilterArray2['name2'] = apply_filters('wppb_register_content_name2_with_email', $registerFilterArray2['name2'], trim($localVar), $errorVar, $errorMark);
								}
								
							}else{
								$localVar = '';
								if (isset($_POST['user_name']))
									$localVar = $_POST['user_name'];
								$registerFilterArray2['name2'] = '
									<p class="form-username'.$errorVar.'">
										<label for="user_name">'. __('Username', 'profilebuilder') .$errorMark.'</label>
										<input class="text-input" name="user_name" type="text" id="user_name" value="'.trim($localVar).'" />
									</p><!-- .form-username -->';
								$registerFilterArray2['name2'] = apply_filters('wppb_register_content_name2', $registerFilterArray2['name2'], trim($localVar), $errorVar, $errorMark);
							}
						}
							if ($wppb_defaultOptions['password'] == 'show'){
							$errorMark = '';
							$errorMark2 = '';
							$errorVar = '';
							$errorVar2 = '';
							if ($wppb_defaultOptions['passwordRequired'] == 'yes'){
								$errorMark = '<font color="red" title="This field is required for registration.">*</font>';
								$errorMark2 = '<font color="red" title="This field is required for registration.">*</font>';
								if (isset ($_POST['passw1']))
									if (trim($_POST['passw1']) == ''){
										$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="This field is required for registration."/>';
										$errorVar = ' errorHolder';
									}
								if (isset ($_POST['passw2']))
									if (trim($_POST['passw2']) == ''){
										$errorMark2 = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="This field is required for registration."/>';
										$errorVar2 = ' errorHolder';
									}
							}
							
							$localVar1 = '';
							if (isset($_POST['passw1']))
								$localVar1 = $_POST['passw1'];
							$localVar2 = '';
							if (isset($_POST['passw2']))
								$localVar2 = $_POST['passw2'];
							$registerFilterArray2['ay3'] = '
								<p class="form-password'.$errorVar.'">
									<label for="pass1">'. __('Password', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="passw1" type="password" id="pass1" value="'.trim($localVar1).'" />
								</p><!-- .form-password -->
				 
								<p class="form-password'.$errorVar2.'">
									<label for="pass2">'. __('Confirm Password', 'profilebuilder') .$errorMark2.'</label>
									<input class="text-input" name="passw2" type="password" id="pass2" value="'.trim($localVar2).'" />
								</p><!-- .form-password -->';
							$registerFilterArray2['ay3'] = apply_filters('wppb_register_content_about_yourself3', $registerFilterArray2['ay3'], trim($localVar1), trim($localVar2), $errorVar, $errorMark, $errorVar2, $errorMark2);
						}
						if ($wppb_defaultOptions['firstname'] == 'show'){
								$errorVar = '';
								$errorMark = '';
								if ($wppb_defaultOptions['firstnameRequired'] == 'yes'){
									$errorMark = '<font color="red" title="'.__('This field is marked as required by the administrator', 'profilebuilder').'">*</font>';
									if (isset($_POST['first_name'])){
										if (trim($_POST['first_name']) == ''){
											$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'.__('This field must be filled out before registering (It was marked as required by the administrator)', 'profilebuilder').'"/>';
											$errorVar = ' errorHolder';
										}
									}
								}
								
							$localVar = '';
							if (isset($_POST['first_name']))
								$localVar = $_POST['first_name'];
							$registerFilterArray2['name3'] = '
								<p class="first_name'.$errorVar.'">
									<label for="first_name">'. __('First Name', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="first_name" type="text" id="first_name" value="'.trim($localVar).'" />
								</p><!-- .first_name -->';
							$registerFilterArray2['name3'] = apply_filters('wppb_register_content_name3', $registerFilterArray2['name3'], trim($localVar), $errorVar, $errorMark);
						}

						if ($wppb_defaultOptions['lastname'] == 'show'){ 
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['lastnameRequired'] == 'yes'){
								$errorMark = '<font color="red" title="'.__('This field is marked as required by the administrator', 'profilebuilder').'">*</font>';
								if (isset($_POST['last_name'])){
									if (trim($_POST['last_name']) == ''){
										$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'.__('This field must be filled out before registering (It was marked as required by the administrator)', 'profilebuilder').'"/>';
										$errorVar = ' errorHolder';
									}
								}
							}
							
							$localVar = '';
							if (isset($_POST['last_name']))
								$localVar = $_POST['last_name'];
							$registerFilterArray2['name4'] = '
								<p class="last_name'.$errorVar.'">
									<label for="last_name">'. __('Last Name', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="last_name" type="text" id="last_name" value="'.trim($localVar).'" />
								</p><!-- .last_name -->';
							$registerFilterArray2['name4'] = apply_filters('wppb_register_content_name4', $registerFilterArray2['name4'], trim($localVar), $errorVar, $errorMark);
						}

						if ($wppb_defaultOptions['nickname'] == 'show'){
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['nicknameRequired'] == 'yes'){
								$errorMark = '<font color="red" title="'.__('This field is marked as required by the administrator', 'profilebuilder').'">*</font>';
								if (isset($_POST['nickname'])){
									if (trim($_POST['nickname']) == ''){
										$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'.__('This field must be filled out before registering (It was marked as required by the administrator)', 'profilebuilder').'"/>';
										$errorVar = ' errorHolder';
									}
								}
							}
							
							$localVar = '';
							if (isset($_POST['nickname']))
								$localVar = $_POST['nickname'];
							$registerFilterArray2['name5'] = '
								<p class="nickname'.$errorVar.'">
									<label for="nickname">'. __('Nickname', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="nickname" type="text" id="nickname" value="'.trim($localVar).'" />
								</p><!-- .nickname -->';
							$registerFilterArray2['name5'] = apply_filters('wppb_register_content_name5', $registerFilterArray2['name5'], trim($localVar), $errorVar, $errorMark);
						}

						//$registerFilterArray2['info1'] = '<p class="registerContactInfoHeading"><strong>'. __('Contact Info', 'profilebuilder') .'</strong></p>';
						$registerFilterArray2['info1'] = apply_filters('wppb_register_content_info1', $registerFilterArray2['info1']);		

						if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email')){
						}else{
							if ($wppb_defaultOptions['email'] == 'show'){
								$errorVar = '';
								$errorMark = '';
								if ($wppb_defaultOptions['emailRequired'] == 'yes'){
									$errorMark = '<font color="red" title="'.__('This field is marked as required by the administrator', 'profilebuilder').'">*</font>';
									if (isset($_POST['email'])){
										if ( (trim($_POST['email']) == '') || (!is_email(trim($_POST['email']))) ){
											$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="This field is required for registration."/>';
											$errorVar = ' errorHolder';
										}
									}
								}
								
								$localVar = '';
								if (isset($_POST['email']))
									$localVar = $_POST['email'];
								$registerFilterArray2['info2'] = '
									<p class="form-email'.$errorVar.'">
										<label for="email">'. __('E-mail', 'profilebuilder') .$errorMark.'</label>
										<input class="text-input" name="email" type="text" id="email" value="'.trim($localVar).'" style="padding: 10px 12px !important;" />
									</p><!-- .form-email -->';
								$registerFilterArray2['info2'] = apply_filters('wppb_register_content_info2', $registerFilterArray2['info2'], trim($localVar), $errorVar, $errorMark);
							}
						}

if ($wppb_defaultOptions['role'] == 'show'){
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['roleRequired'] == 'yes'){
								$errorMark = '<font color="red" title="'.__('This field is marked as required by the administrator', 'profilebuilder').'">*</font>';
								if (isset($_POST['role'])){
									if (trim($_POST['role']) == ''){
										$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'.__('This field must be filled out before registering (It was marked as required by the administrator)', 'profilebuilder').'"/>';
										$errorVar = ' errorHolder';
									}
								}
							}	
						$localVar = '';
							if (isset($_POST['role']))
								$localVar = $_POST['role'];
								//trim($localVar)
							$registerFilterArray2['info7'] = '
								<p class="form-role'.$errorVar.'">
									<label for="role">'. __('Request Role', 'profilebuilder') .$errorMark.'</label>
									<select class="text-input" name="role" type="text" id="role">
									<option value="">--Select Role--</option>
									<option value="artistband">Artist / Band</option>
									<option value="venue">Venue</option>
									<option value="promoter">Promoter</option>
									</select>
									
								</p><!-- .form-jabber -->';
							$registerFilterArray2['info7'] = apply_filters('wppb_register_content_info6', $registerFilterArray2['info7'], trim($localVar), 				$errorVar, $errorMark);
						}

if ($wppb_defaultOptions['city'] == 'show'){
$errorVar = '';
								$errorMark = '';
								if ($wppb_defaultOptions['cityRequired'] == 'yes'){
									$errorMark = '<font color="red" title="'.__('This field is marked as required by the administrator', 'profilebuilder').'">*</font>';
									if (isset($_POST['city'])){
										if (trim($_POST['city']) == ''){
											$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'.__('This field must be filled out before registering (It was marked as required by the administrator)', 'profilebuilder').'"/>';
											$errorVar = ' errorHolder';
										}
									}
								}
$localVar = '';
							if (isset($_POST['city']))
								$localVar = $_POST['city'];
							$registerFilterArray2['info71'] = '
							<p class="form-city'.$errorVar.'">
							<label for="city">'. __('Region', 'profilebuilder') .$errorMark.'</label>
<select class="selectop" name="city" id="city">
<option selected="selected" value="">Region</option>
<option value="Augusta">Augusta</option>
<option value="Las Vegas">Las Vegas</option>
<option value="Virginia Beach">Virginia Beach</option>
<option value="St. Louis">St. Louis</option>
<option value="Tulsa">Tulsa</option>
<option value="Houston">Houston</option>
<option value="Phoenix">Phoenix</option>
<option value="Ventura">Ventura</option>
<option value="San Diego">San Diego</option>
						</select>

								</p><!-- .first_name -->';
							$registerFilterArray2['info71'] = apply_filters('wppb_register_content_name71', $registerFilterArray2['info71'], trim($localVar), $errorVar, $errorMark);
}


						if ($wppb_defaultOptions['website'] == 'show'){
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['websiteRequired'] == 'yes'){
								$errorMark = '<font color="red" title="'.__('This field is marked as required by the administrator', 'profilebuilder').'">*</font>';
								if (isset($_POST['website'])){
									if (trim($_POST['website']) == ''){
										$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'.__('This field must be filled out before registering (It was marked as required by the administrator)', 'profilebuilder').'"/>';
										$errorVar = ' errorHolder';
									}
								}
							}
						
							$localVar = '';
							if (isset($_POST['website']))
								$localVar = $_POST['website'];
							$registerFilterArray2['info3'] = '
								<p class="form-website'.$errorVar.'">
									<label for="website">'. __('Website', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="website" type="text" id="website" value="'.trim($localVar).'" />
								</p><!-- .form-website -->';
							$registerFilterArray2['info3'] = apply_filters('wppb_register_content_info3', $registerFilterArray2['info3'], trim($localVar), $errorVar, $errorMark);
						}

						if ($wppb_defaultOptions['aim'] == 'show'){
							$errorVar = '';
								$errorMark = '';
								if ($wppb_defaultOptions['aimRequired'] == 'yes'){
									$errorMark = '<font color="red" title="'.__('This field is marked as required by the administrator', 'profilebuilder').'">*</font>';
									if (isset($_POST['aim'])){
										if (trim($_POST['aim']) == ''){
											$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'.__('This field must be filled out before registering (It was marked as required by the administrator)', 'profilebuilder').'"/>';
											$errorVar = ' errorHolder';
										}
									}
								}					

							$localVar = '';
							if (isset($_POST['aim']))
								$localVar = $_POST['aim'];
							$registerFilterArray2['info4'] = '
								<p class="form-aim'.$errorVar.'">
									<label for="aim">'. __('AIM', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="aim" type="text" id="aim" value="'.trim($localVar).'" />
								</p><!-- .form-aim -->';
							$registerFilterArray2['info4'] = apply_filters('wppb_register_content_info4', $registerFilterArray2['info4'], trim($localVar), $errorVar, $errorMark);
						}

						if ($wppb_defaultOptions['yahoo'] == 'show'){
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['yahooRequired'] == 'yes'){
								$errorMark = '<font color="red" title="'.__('This field is marked as required by the administrator', 'profilebuilder').'">*</font>';
								if (isset($_POST['yim'])){
									if (trim($_POST['yim']) == ''){
										$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'.__('This field must be filled out before registering (It was marked as required by the administrator)', 'profilebuilder').'"/>';
										$errorVar = ' errorHolder';
									}
								}
							}
							
							$localVar = '';
							if (isset($_POST['yim']))
								$localVar = $_POST['yim'];
							$registerFilterArray2['info5'] = '
								<p class="form-yim'.$errorVar.'">
									<label for="yim">'. __('Yahoo IM', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="yim" type="text" id="yim" value="'.trim($localVar).'" />
								</p><!-- .form-yim -->';
							$registerFilterArray2['info5'] = apply_filters('wppb_register_content_info5', $registerFilterArray2['info5'], trim($localVar), $errorVar, $errorMark);
						}

						if ($wppb_defaultOptions['jabber'] == 'show'){
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['jabberRequired'] == 'yes'){
								$errorMark = '<font color="red" title="'.__('This field is marked as required by the administrator', 'profilebuilder').'">*</font>';
								if (isset($_POST['jabber'])){
									if (trim($_POST['jabber']) == ''){
										$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'.__('This field must be filled out before registering (It was marked as required by the administrator)', 'profilebuilder').'"/>';
										$errorVar = ' errorHolder';
									}
								}
							}						
						
							$localVar = '';
							if (isset($_POST['jabber']))
								$localVar = $_POST['jabber'];
							$registerFilterArray2['info6'] = '
								<p class="form-jabber'.$errorVar.'">
									<label for="jabber">'. __('Jabber / Google Talk', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="jabber" type="text" id="jabber" value="'.trim($localVar).'" />
								</p><!-- .form-jabber -->';
							$registerFilterArray2['info6'] = apply_filters('wppb_register_content_info6', $registerFilterArray2['info6'], trim($localVar), $errorVar, $errorMark);
						}
						
					//	$registerFilterArray2['ay1'] = '<p class="registerAboutYourselfHeader"><strong>'. __('About Yourself', 'profilebuilder') .'</strong></p>';
						$registerFilterArray2['ay1'] = apply_filters('wppb_register_content_about_yourself1', $registerFilterArray2['ay1']);
						
						if ($wppb_defaultOptions['bio'] == 'show'){
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['bioRequired'] == 'yes'){
								$errorMark = '<font color="red" title="'.__('This field is marked as required by the administrator', 'profilebuilder').'">*</font>';
								if (isset($_POST['description'])){
									if (trim($_POST['description']) == ''){
										$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'.__('This field must be filled out before registering (It was marked as required by the administrator)', 'profilebuilder').'"/>';
										$errorVar = ' errorHolder';
									}
								}
							}
							
							$localVar = '';
							if (isset($_POST['description']))
								$localVar = $_POST['description'];
							$registerFilterArray2['ay2'] = '
								<p class="form-description'.$errorVar.'">
									<label for="description">'. __('Biographical Info', 'profilebuilder') .$errorMark.'</label>
									<textarea class="text-input" name="description" id="description" rows="5" cols="30">'.trim($localVar).'</textarea>
								</p><!-- .form-description -->';
							$registerFilterArray2['ay2'] = apply_filters('wppb_register_content_about_yourself2', $registerFilterArray2['ay2'], trim($localVar), $errorVar, $errorMark);
						}

					

						/*	$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
							if (file_exists ( $wppb_premium.'extra.fields.php' )){
								require_once($wppb_premium.'extra.fields.php');
								
								//register_user_extra_fields($error, $_POST, $extraFieldsErrorHolder);
								$page = 'register';
								$returnedValue = wppb_extra_fields($current_user->id, $extraFieldsErrorHolder, $registerFilterArray, $page, $error, $_POST);
								
								//copy over extra fields to the rest of the fieldso on the edit profile
								foreach($returnedValue as $key => $value)
									$registerFilterArray2[$key] = apply_filters('wppb_register_content_'.$key, $value);
							}*/

							if(function_exists('wppb_add_recaptcha_to_registration_form')){
								$wppb_addon_settings = get_option('wppb_addon_settings');
								if ($wppb_addon_settings['wppb_reCaptcha'] == 'show'){
									$reCAPTCHAForm = wppb_add_recaptcha_to_registration_form();
									$labelName = apply_filters('wppb_register_anti_spam_title', __('Anti-Spam', 'profilebuilder'));
									$registerFilterArray2['reCAPTCHAForm'] = '<div class="form-reCAPTCHA"><label class="form-reCAPTCHA-label" for="'.$labelName.'">'.$labelName.'</label>'.$reCAPTCHAForm.'</div><!-- .form-reCAPTCHA -->';
								}
							}
							
							// additional filter, just in case it is needed
							$registerFilterArray2['extraRegistrationFilter'] = '';
							$registerFilterArray2['extraRegistrationFilter'] = apply_filters('extraRegistrationField', $registerFilterArray2['extraRegistrationFilter']);
							// END additional filter, just in case it is needed

							$wppb_generalSettings = get_option('wppb_general_settings');
		
							if ($wppb_generalSettings['emailConfirmation'] != 'yes'){
								if (!is_multisite()){
									if (isset($_POST['send_credentials_via_email'])) 
										$checkedVar = ' checked';
									else $checkedVar = '';
									$registerFilterArray2['confirmationEmailForm'] = '
										<p class="send-confirmation-email">
											<label for="send-confirmation-email"> 
												<input id="send_credentials_via_email" type="checkbox" name="send_credentials_via_email" value="sending"'. $checkedVar .'/>
												'. __('Send these credentials via email.', 'profilebuilder') . '
											</label>
										</p><!-- .send-confirmation-email -->';
									$registerFilterArray2['confirmationEmailForm'] = apply_filters('wppb_register_confirmation_email_form', $registerFilterArray2['confirmationEmailForm'], $checkedVar);
								}
							}
							
							
							$registerFilterArray2 = apply_filters('wppb_register', $registerFilterArray2);
							foreach ($registerFilterArray2 as $key => $value)
								echo $value;
?>
							
						<p class="form-submit">
							<input name="adduser" type="submit" id="addusersub" class="creataccount" value="<?php if ( current_user_can( 'create_users' ) ) _e('Add User', 'profilebuilder'); else _e('Register', 'profilebuilder'); ?>" />
							<input name="action" type="hidden" id="action" value="adduser" />
							<input type="hidden" name="formName" value="register" />
						</p><!-- .form-submit -->
<?php 
						wp_nonce_field('verify_true_registration','register_nonce_field'); 
?></div></div>
					</form><!-- #adduser -->

<?php	
				endif;
			endif;
		
		/* use this action hook to add extra content after the register form. */
		do_action( 'wppb_after_register_fields' );
?>
	
	</div>
<?php
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}



// function to choose whether to display the registration page or the key-validating function
function wppb_front_end_register_handler($atts){

	if (isset($_GET['key'])){
		$res = wppb_activate_signup ($_GET['key']);
	}else
		$res = wppb_front_end_register($atts);
		
	return $res;	
}

?>