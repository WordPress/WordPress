<?php
/*
	wp_update_user only attempts to clear and reset cookies if it's updating the password.
	The php function setcookie(), used in both the cookie-clearing and cookie-resetting functions,
	adds to the page headers and therefore must be called within the first php tag on the page, and 
	before the WordPress get_header() function. Since wp_update_user needs this, it must be at the 
	beginning of the page as well.
*/
$changesSaved = 'no';
$changesSavedNoMatchingPass = 'no';
$changesSavedNoPass = 'no';

function wppb_save_the_password(){
	global $changesSaved;
	global $changesSavedNoMatchingPass;
	global $changesSavedNoPass;
	
	/* Get user info. */
	global $current_user;

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' && wp_verify_nonce($_POST['edit_nonce_field'],'verify_edit_user') ) { 
		/* Update user password. */
		if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ){
			if ( $_POST['pass1'] == $_POST['pass2'] ){
				wp_update_user( array( 'ID' => $current_user->id, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
				$changesSaved = 'yes';
			} else {
				$changesSavedNoMatchingPass = 'yes'; 
			}
		}elseif (( empty($_POST['pass1'] ) && !empty( $_POST['pass2'] )) || ( !empty($_POST['pass1'] ) && empty( $_POST['pass2'] )) ) {
			$changesSavedNoPass = 'yes';
		}	
	}
}
add_action('init', 'wppb_save_the_password');
		
function wppb_front_end_profile_info() {

	global $changesSaved;
	global $changesSavedNoMatchingPass;
	global $changesSavedNoPass;
	$editProfileFilterArray = array();
	$editProfileFilterArray2 = array();
	$extraFieldsErrorHolder = array();  //we will use this array to store the ID's of the extra-fields left uncompleted
	
	global $wppb_shortcode_on_front;
	
	//get "login with" setting
	$wppb_generalSettings = get_option('wppb_general_settings');
	
	$wppb_shortcode_on_front = true;
	ob_start();
	get_currentuserinfo();
	$wppb_defaultOptions = get_option('wppb_default_settings');
	$changesSavedNoEmail = 'no';
	$changesSavedNoEmailExist = 'no';
	$previousError = 'no';
	$pictureUpload = 'no';
	$avatarUpload = 'yes';
	$allRequiredCompleted = 'yes';
	$uploadName = array();
	$uploadExt = array();
	$uploadSize = array();
	$editFilterArray = array();
	$error = null;
	
	
	/* Get user info. */
		global $current_user;
		
	/* delete the attachment if set */
	if (isset($_GET['userID']) && isset($_GET['field'])){
		update_user_meta( $_GET['userID'], $_GET['field'], '');
	}
	/* delete the avatar */
	if (isset($_GET['userID']) && isset($_GET['fieldOriginal']) && isset($_GET['fieldResized'])){
		update_user_meta( $_GET['userID'], $_GET['fieldOriginal'], '');
		update_user_meta( $_GET['userID'], $_GET['fieldResized'], '');
	}
	
	//fallback if the file was largen then post_max_size, case in which no errors can be saved in $_FILES[fileName]['error']	
	if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
		$editProfileFilterArray['noPost'] = '<p class="error">' . sprintf(__( 'The information size you were trying to submit was larger than %1$sb!<br/>This is usually caused by a large file(s) trying to be uploaded.<br/>Since it was also larger than %2$sb no additional information is available.<br/>The user was NOT created!', 'profilebuilder'), WPPB_SERVER_MAX_UPLOAD_SIZE_MEGA, WPPB_SERVER_MAX_POST_SIZE_MEGA) . '</p>';
		echo $editProfileFilterArray['noPost'] = apply_filters('wppb_edit_profile_no_post_error', $editProfileFilterArray['noPost']);
	}
	
	//a way to catch the user before updating his/her profile without completing a required field
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' && wp_verify_nonce($_POST['edit_nonce_field'],'verify_edit_user') ) {	
		//variable to control whether the user submitted data or not
		
		$allRequiredCompleted = apply_filters('wppb_edit_profile_all_required_completed', $allRequiredCompleted);
		
		if ($wppb_defaultOptions['firstname'] == 'show'){
			$_POST['first_name'] =  apply_filters('wppb_edit_profile_posted_first_name_check', $_POST['first_name']);
			if ($wppb_defaultOptions['firstnameRequired'] == 'yes'){
				if (isset($_POST['first_name']) && (trim($_POST['first_name']) == '')){
					$allRequiredCompleted = 'no'; 
				}
			}
		}
		
		if ($wppb_defaultOptions['lastname'] == 'show'){
			$_POST['last_name'] =  apply_filters('wppb_edit_profile_posted_last_name_check', $_POST['last_name']);
			if ($wppb_defaultOptions['lastnameRequired'] == 'yes'){
				if (isset($_POST['last_name']) && (trim($_POST['last_name']) == '')){
					$allRequiredCompleted = 'no';
				}
			}
		}
		
		if ($wppb_defaultOptions['nickname'] == 'show'){
			$_POST['nickname'] =  apply_filters('wppb_edit_profile_posted_nickname_check', $_POST['nickname']);
			if ($wppb_defaultOptions['nicknameRequired'] == 'yes'){
				if (isset($_POST['nickname']) && (trim($_POST['nickname']) == '')){
					$allRequiredCompleted = 'no';
				}
			}
		}
			
		if ($wppb_defaultOptions['dispname'] == 'show'){
			$_POST['display_name'] =  apply_filters('wppb_edit_profile_posted_display_name_check', $_POST['display_name']);
			if ($wppb_defaultOptions['dispnameRequired'] == 'yes'){
				if (isset($_POST['display_name']) && (trim($_POST['display_name']) == '')){
					$allRequiredCompleted = 'no'; 
				}
			}
		}
			
		if ($wppb_defaultOptions['website'] == 'show'){
			$_POST['website'] =  apply_filters('wppb_edit_profile_posted_website_check', $_POST['website']);
			if ($wppb_defaultOptions['websiteRequired'] == 'yes'){
				if (isset($_POST['website']) && (trim($_POST['website']) == '')){
					$allRequiredCompleted = 'no'; 
				}
			}
		}
		
		if ($wppb_defaultOptions['aim'] == 'show'){
			$_POST['aim'] =  apply_filters('wppb_edit_profile_posted_aim_check', $_POST['aim']);
			if ($wppb_defaultOptions['aimRequired'] == 'yes'){
				if (isset($_POST['aim']) && (trim($_POST['aim']) == '')){
					$allRequiredCompleted = 'no'; 
				}
			}
		}
			
		if ($wppb_defaultOptions['yahoo'] == 'show'){
			$_POST['yim'] =  apply_filters('wppb_edit_profile_posted_yahoo_check', $_POST['yim']);
			if ($wppb_defaultOptions['yahooRequired'] == 'yes'){
				if (isset($_POST['yim']) && (trim($_POST['yim']) == '')){
					$allRequiredCompleted = 'no'; 
				}
			}
		}
			
		if ($wppb_defaultOptions['jabber'] == 'show'){
			$_POST['jabber'] =  apply_filters('wppb_edit_profile_posted_jabber_check', $_POST['jabber']);
			if ($wppb_defaultOptions['jabberRequired'] == 'yes'){
				if (isset($_POST['jabber']) && (trim($_POST['jabber']) == '')){
					$allRequiredCompleted = 'no'; 
				}
			}
		}
			
		if ($wppb_defaultOptions['bio'] == 'show'){
			$_POST['description'] =  apply_filters('wppb_edit_profile_posted_bio_check', $_POST['description']);
			if ($wppb_defaultOptions['bioRequired'] == 'yes'){
				if (isset($_POST['description']) && (trim($_POST['description']) == '')){
					$allRequiredCompleted = 'no'; 
				}
			}
		}
	
		/* also check the extra profile information */
		$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
		if (file_exists ( $wppb_premium.'extra.fields.php' )){
			$wppbFetchArray = get_option('wppb_custom_fields');
			foreach ( $wppbFetchArray as $key => $value){
				switch ($value['item_type']) {
					case "input":{
						$_POST[$value['item_type'].$value['id']] = apply_filters('wppb_edit_profile_input_custom_field_'.$value['id'].'_check', $_POST[$value['item_type'].$value['id']]);
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == ''){
									array_push($extraFieldsErrorHolder, $value['id']);
									$allRequiredCompleted = 'no';
								}
							}
						}
						break;
					}
					case "checkbox":{
						$checkboxOption = '';
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
								if (trim($checkboxOption) == ''){
									array_push($extraFieldsErrorHolder, $value['id']);
									$allRequiredCompleted = 'no';
								}
							}
						}
							
						break;
					}
					case "radio":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == ''){
									array_push($extraFieldsErrorHolder, $value['id']);
									$allRequiredCompleted = 'no';
								}
							}
						}
						break;
					}
					case "select":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == ''){
									array_push($extraFieldsErrorHolder, $value['id']);
									$allRequiredCompleted = 'no';
								}
							}
						}
						break;
					}
					case "countrySelect":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == ''){
									array_push($extraFieldsErrorHolder, $value['id']);
									$allRequiredCompleted = 'no';
								}
							}
						}
						
						break;
					}
					case "timeZone":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == ''){
									array_push($extraFieldsErrorHolder, $value['id']);
									$allRequiredCompleted = 'no';
								}
							}
						}
						
						break;
					}
					case "datepicker":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == ''){
									array_push($extraFieldsErrorHolder, $value['id']);
									$allRequiredCompleted = 'no'; 
								}
							}
						}
						
						break;
					}
					case "textarea":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) == ''){
									array_push($extraFieldsErrorHolder, $value['id']);
									$allRequiredCompleted = 'no';
								}
							}
						}
						
						break;
					}
					case "upload":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								$uploadedfile = $value['item_type'].$value['id'];
								if ( (basename( $_FILES[$uploadedfile]['name']) == '')){
									array_push($extraFieldsErrorHolder, $value['id']);
									$allRequiredCompleted = 'no'; 
								}
							}
						}
						break;
					}
					case "avatar":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								$uploadedfile = $value['item_type'].$value['id'];
								if ( (basename( $_FILES[$uploadedfile]['name']) == '')){
									array_push($extraFieldsErrorHolder, $value['id']);
									$allRequiredCompleted = 'no'; 
								}
							}
						}
						break;
					}
				}
			}
		}		
		
		
		$allRequiredCompleted = apply_filters('wppb_edit_profile_all_required_completed_after_check', $allRequiredCompleted);
	}
		
	/* If profile was saved, update profile. */
	if ( ('POST' == $_SERVER['REQUEST_METHOD']) && (!empty( $_POST['action'] )) && ($_POST['action'] == 'update-user') && (wp_verify_nonce($_POST['edit_nonce_field'],'verify_edit_user')) && ($allRequiredCompleted == 'yes') ) { 
		
		if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email')){
		}else{
			$_POST['email'] =  apply_filters('wppb_edit_profile_posted_email', $_POST['email']);
			if ($wppb_defaultOptions['emailRequired'] == 'yes'){
				if ((trim($_POST['email']) != '') && isset($_POST['email'])){
					if (email_exists( $_POST['email'] ) !=  FALSE)
						$thisEmail = email_exists( $_POST['email'] );
					else $thisEmail = $current_user->id;
					
					if ( !empty( $_POST['email'] ) &&  is_email( $_POST['email'] )){                  				// if the user entered a valid email address
						if (($thisEmail ==  $current_user->id)){            										// if the entered email address is not already registered to some other user
							wp_update_user( array( 'ID' => $current_user->id, 'user_email' => esc_attr( $_POST['email'] )));	
							$changesSaved = 'yes';
						}else{
							$changesSavedNoEmailExist = 'yes';
						}
					}else{
						$changesSavedNoEmail = 'yes';
					}
				}
			}else{	
				if (email_exists( $_POST['email'] ) !=  FALSE)
					$thisEmail = email_exists( $_POST['email'] );
				else $thisEmail = $current_user->id;
				
				if ( !empty( $_POST['email'] ) &&  is_email( $_POST['email'] )){                  				// if the user entered a valid email address
					if (($thisEmail ==  $current_user->id)){            										// if the entered email address is not already registered to some other user
						wp_update_user( array( 'ID' => $current_user->id, 'user_email' => esc_attr( $_POST['email'] )));	
						$changesSaved = 'yes';
					}else{
						$changesSavedNoEmailExist = 'yes';
					}
				}else{
					$changesSavedNoEmail = 'yes';
				}
			}
		}

		/* Update user information. */
		if ($wppb_defaultOptions['firstname'] == 'show'){
			$_POST['first_name'] =  apply_filters('wppb_edit_profile_posted_first_name', $_POST['first_name']);
			if ($wppb_defaultOptions['firstnameRequired'] == 'yes'){
				if (isset($_POST['first_name']) && (trim($_POST['first_name']) != '')){
					wp_update_user( array( 'ID' => $current_user->id, 'first_name' => esc_attr( $_POST['first_name'] )));
					$changesSaved = 'yes';
				}
			}else{
				wp_update_user( array( 'ID' => $current_user->id, 'first_name' => esc_attr( $_POST['first_name'] )));
				$changesSaved = 'yes';
			}
		}	
		
		if ($wppb_defaultOptions['lastname'] == 'show'){
			$_POST['last_name'] =  apply_filters('wppb_edit_profile_posted_last_name', $_POST['last_name']);
			if ($wppb_defaultOptions['lastnameRequired'] == 'yes'){
				if (isset($_POST['last_name']) && (trim($_POST['last_name']) != '')){
					wp_update_user( array( 'ID' => $current_user->id, 'last_name' => esc_attr( $_POST['last_name'] )));
					$changesSaved = 'yes';
				}
			}else{
				wp_update_user( array( 'ID' => $current_user->id, 'last_name' => esc_attr( $_POST['last_name'] )));
				$changesSaved = 'yes';
			}
		}
		
		if ($wppb_defaultOptions['nickname'] == 'show'){
			$_POST['nickname'] =  apply_filters('wppb_edit_profile_posted_nickname', $_POST['nickname']);
			if ($wppb_defaultOptions['nicknameRequired'] == 'yes'){
				if (isset($_POST['nickname']) && (trim($_POST['nickname']) != '')){
					wp_update_user( array( 'ID' => $current_user->id, 'nickname' => esc_attr( $_POST['nickname'] )));
					$changesSaved = 'yes';
				}
			}else{
				wp_update_user( array( 'ID' => $current_user->id, 'nickname' => esc_attr( $_POST['nickname'] )));
				$changesSaved = 'yes';
			}

		}
			
		if ($wppb_defaultOptions['dispname'] == 'show'){
			$_POST['display_name'] =  apply_filters('wppb_edit_profile_posted_display_name', $_POST['display_name']);
			if ($wppb_defaultOptions['dispnameRequired'] == 'yes'){
				if (isset($_POST['display_name']) && (trim($_POST['display_name']) != '')){
					wp_update_user( array( 'ID' => $current_user->id, 'display_name' => esc_attr( $_POST['display_name'] )));
					$changesSaved = 'yes';
				}
			}else{
				wp_update_user( array( 'ID' => $current_user->id, 'display_name' => esc_attr( $_POST['display_name'] )));
				$changesSaved = 'yes';
			}
		}
			
		if ($wppb_defaultOptions['website'] == 'show'){
			$_POST['website'] =  apply_filters('wppb_edit_profile_posted_website', $_POST['website']);
			if ($wppb_defaultOptions['websiteRequired'] == 'yes'){
				if (isset($_POST['website']) && (trim($_POST['website']) != '')){
					$wppbPos = strpos( (string)$_POST['website'], 'http://' );
					if($wppbPos !== FALSE){
						wp_update_user( array( 'ID' => $current_user->id, 'user_url' => esc_attr( $_POST['website'] )));
						$changesSaved = 'yes';
					}else{
						wp_update_user( array( 'ID' => $current_user->id, 'user_url' => 'http://'.esc_attr( $_POST['website'] )));
						$changesSaved = 'yes';
					}
				}
			}else{
				$wppbPos = strpos( (string)$_POST['website'], 'http://' );
				$website = esc_attr( $_POST['website'] );
				if($wppbPos !== FALSE){
					if ($website == 'http://')
						$website = '';
					wp_update_user( array( 'ID' => $current_user->id, 'user_url' => $website));
					$changesSaved = 'yes';
				}else{
					if ($website != '')
						$website = 'http://'.$website;
					wp_update_user( array( 'ID' => $current_user->id, 'user_url' => $website));
					$changesSaved = 'yes';
				}
			}
		}
		
		if ($wppb_defaultOptions['aim'] == 'show'){
			$_POST['aim'] =  apply_filters('wppb_edit_profile_posted_aim', $_POST['aim']);
			if ($wppb_defaultOptions['aimRequired'] == 'yes'){
				if (isset($_POST['aim']) && (trim($_POST['aim']) != '')){
					update_user_meta( $current_user->id, 'aim', esc_attr( $_POST['aim'] ) );
					$changesSaved = 'yes';
				}
			}else{
				update_user_meta( $current_user->id, 'aim', esc_attr( $_POST['aim'] ) );
				$changesSaved = 'yes';
			}
		}
			
		if ($wppb_defaultOptions['yahoo'] == 'show'){
			$_POST['yim'] =  apply_filters('wppb_edit_profile_posted_yahoo', $_POST['yim']);
			if ($wppb_defaultOptions['yahooRequired'] == 'yes'){
				if (isset($_POST['yim']) && (trim($_POST['yim']) != '')){
					update_user_meta( $current_user->id, 'yim', esc_attr( $_POST['yim'] ) );
					$changesSaved = 'yes';
				}
			}else{
				update_user_meta( $current_user->id, 'yim', esc_attr( $_POST['yim'] ) );
				$changesSaved = 'yes';
			}
		}
			
		if ($wppb_defaultOptions['jabber'] == 'show'){
			$_POST['jabber'] =  apply_filters('wppb_edit_profile_posted_jabber', $_POST['jabber']);
			if ($wppb_defaultOptions['jabberRequired'] == 'yes'){
				if (isset($_POST['jabber']) && (trim($_POST['jabber']) != '')){
					update_user_meta( $current_user->id, 'jabber', esc_attr( $_POST['jabber'] ) );
					$changesSaved = 'yes';
				}
			}else{
				update_user_meta( $current_user->id, 'jabber', esc_attr( $_POST['jabber'] ) );
				$changesSaved = 'yes';
			}
		}
			
		if ($wppb_defaultOptions['bio'] == 'show'){
			$_POST['description'] =  apply_filters('wppb_edit_profile_posted_bio', $_POST['description']);
			if ($wppb_defaultOptions['bioRequired'] == 'yes'){
				if (isset($_POST['description']) && (trim($_POST['description']) != '')){
					update_user_meta( $current_user->id, 'description', esc_attr( $_POST['description'] ) );
					$changesSaved = 'yes';
				}
			}else{
				update_user_meta( $current_user->id, 'description', esc_attr( $_POST['description'] ) );
				$changesSaved = 'yes';
			}
		}
		
		/* update the extra profile information */
		$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
		if (file_exists ( $wppb_premium.'extra.fields.php' )){
			$wppbFetchArray = get_option('wppb_custom_fields');
			foreach ( $wppbFetchArray as $key => $value){
				switch ($value['item_type']) {
					case "input":{
						$_POST[$value['item_type'].$value['id']] = apply_filters('wppb_edit_profile_input_custom_field_'.$value['id'], $_POST[$value['item_type'].$value['id']]);
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) != '')
									update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
								else 
									array_push($extraFieldsErrorHolder, $value['id']);
							}else
								update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						}else
							update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
							
						break;
					}						
					case "hiddenInput":{
						update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						
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
								if (trim($checkboxOption) != '')
									update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $checkboxOption ) );
								else 
									array_push($extraFieldsErrorHolder, $value['id']);
							}else
								update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $checkboxOption ) );
						}else
							update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $checkboxOption ) );
							
						break;
					}
					case "radio":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) != '')
									update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
								else 
									array_push($extraFieldsErrorHolder, $value['id']);
							}else
								update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						}else
							update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						break;
					}
					case "select":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) != '')
									update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
								else 
									array_push($extraFieldsErrorHolder, $value['id']);
							}else
								update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						}else
							update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						break;
					}
					case "countrySelect":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) != '')
									update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
								else 
									array_push($extraFieldsErrorHolder, $value['id']);
							}else
								update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						}else
							update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						
						break;
					}
					case "timeZone":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) != '')
									update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
								else 
									array_push($extraFieldsErrorHolder, $value['id']);
							}else
								update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						}else
							update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						
						break;
					}
					case "datepicker":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) != '')
									update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
								else 
									array_push($extraFieldsErrorHolder, $value['id']);
							}else
								update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						}else
							update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						
						break;
					}
					case "textarea":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_type'].$value['id']]) != '')
									update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
								else 
									array_push($extraFieldsErrorHolder, $value['id']);
							}else
								update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						}else
							update_user_meta( $current_user->id, $value['item_metaName'], esc_attr( $_POST[$value['item_type'].$value['id']] ) );
						
						break;
					}
					case "upload":{
						$uploadedfile = $value['item_type'].$value['id'];
						
						//get allowed file types
						if (($value['item_options'] != NULL) || ($value['item_options'] != '')){
							$allFiles = false;
							$extensions = explode(',', $value['item_options']);
							foreach($extensions as $key3 => $value3)
								$extensions[$key3] = trim($value3);
						}else 
							$allFiles = true;
						//first we need to verify if we don't try to upload a 0b or 0 length file
						if ( (basename( $_FILES[$uploadedfile]['name']) != '')){
							//get this attachments extension
							$thisFileExtStart = strrpos($_FILES[$uploadedfile]['name'], '.');
							$thisFileExt = substr($_FILES[$uploadedfile]['name'], $thisFileExtStart);
							
							if ($allFiles === false){
								if (in_array($thisFileExt, $extensions)){
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
										$target_path = $target_path . 'userID_'.$current_user->id.'_attachment_'. $finalFileName;

										if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
											$upFile = $wpUploadPath['baseurl'].'/profile_builder/attachments/userID_'.$current_user->id.'_attachment_'. $finalFileName;
											update_user_meta( $current_user->id, $value['item_metaName'], $upFile);
											$pictureUpload = 'yes';
										}else{
											//insert the name of the file in an array so that in case an error comes up, we know which files we just uploaded
											array_push($uploadName, basename( $_FILES[$uploadedfile]['name']));
										}
									}else{
										//insert the name of the file in an array so that in case an error comes up, we know which files we just uploaded
										array_push($uploadName, basename( $_FILES[$uploadedfile]['name']));
									}
								}else{
									array_push($uploadExt, basename( $_FILES[$uploadedfile]['name']));
									$allowedExtensions = '';
									(int)$nrOfExt = count($extensions)-2;
									foreach($extensions as $key4 => $value4){
										$allowedExtensions .= $value4;
										if ($key4 <= $nrOfExt)
											$allowedExtensions .= ', ';
											
									}
								}
							}else{
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
									$target_path = $target_path . 'userID_'.$current_user->id.'_attachment_'. $finalFileName;

									if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
										$upFile = $wpUploadPath['baseurl'].'/profile_builder/attachments/userID_'.$current_user->id.'_attachment_'. $finalFileName;
										update_user_meta( $current_user->id, $value['item_metaName'], $upFile);
										$pictureUpload = 'yes';
									}else{
										//insert the name of the file in an array so that in case an error comes up, we know which files we just uploaded
										array_push($uploadName, basename( $_FILES[$uploadedfile]['name']));
									}
								}else{
									//insert the name of the file in an array so that in case an error comes up, we know which files we just uploaded
									array_push($uploadName, basename( $_FILES[$uploadedfile]['name']));
								}
							}
						}
						break;
					}
					case "avatar":{
						$avatarUpload = 'no';
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

						$target_path = $target_path_original . 'userID_'.$current_user->id.'_originalAvatar_'. $fileName; 	

						/* when trying to upload file, be sure it's one of the accepted image file-types */
						if ( (($_FILES[$uploadedfile]['type'] == 'image/jpeg') || ($_FILES[$uploadedfile]['type'] == 'image/jpg') || ($_FILES[$uploadedfile]['type'] == 'image/png') || ($_FILES[$uploadedfile]['type'] == 'image/bmp') || ($_FILES[$uploadedfile]['type'] == 'image/pjpeg') || ($_FILES[$uploadedfile]['type'] == 'image/x-png')) && (($_FILES[$uploadedfile]['size'] < WPPB_SERVER_MAX_UPLOAD_SIZE_BYTE) && ($_FILES[$uploadedfile]['size'] !=0)) ){
							$avatarUpload = 'yes';
							$wp_filetype = wp_check_filetype(basename( $_FILES[$uploadedfile]['name']), null );
							$attachment = array(
								 'post_mime_type' => $wp_filetype['type'],
								 'post_title' => $fileName,
								 'post_content' => '',
								 'post_status' => 'inherit'
								);


							$attach_id = wp_insert_attachment( $attachment, $target_path);
					
							$upFile = image_downsize( $attach_id, 'thumbnail' );
							$upFile = $upFile[0];
							
							//if file upload succeded			
							if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
								update_user_meta( $current_user->id, $value['item_metaName'], $upFile);
								update_user_meta( $current_user->id, 'resized_avatar_'.$value['id'], '');				

							}else $avatarUpload = 'no'; 
						}elseif ( (($_FILES[$uploadedfile]['size'] > WPPB_SERVER_MAX_UPLOAD_SIZE_BYTE) || ($_FILES[$uploadedfile]['size'] == 0)) && ($fileName != '') )
							$avatarUpload = 'no';
						elseif ($fileName == '')
							$avatarUpload = 'yes';
						break;
					}
				}
			}
		}
		
	}
	
?>
	<div class="wppb_holder" id="wppb_modify">
<?php 
		if ( !is_user_logged_in() ){
			$editProfileFilterArray['notLoggedIn'] = '
				<p class="warning">'. __('You must be logged in to edit your profile.', 'profilebuilder') .'</p><!-- .warning -->';
			echo $editProfileFilterArray['notLoggedIn'] = apply_filters('wppb_edit_profile_user_not_logged_in', $editProfileFilterArray['notLoggedIn']);
 
		}else{
			/* messages for the the delete avatar/attachment */
			if (isset($_GET['fileType'])){
				if ($_GET['fileType'] == 'avatar'){
					$editProfileFilterArray['avatarChangesSaved'] = '
						<p class="changes-saved">'. __('The avatar was successfully deleted.', 'profilebuilder') .'</p><!-- .changes-saved -->';
					echo $editProfileFilterArray['avatarChangesSaved'] = apply_filters('wppb_edit_profile_avatar_changes_saved', $editProfileFilterArray['avatarChangesSaved']);
					unset($_GET['fileType']);
					
				}elseif ($_GET['fileType'] == 'attachment'){
					$editProfileFilterArray['attachmentChangesSaved'] = '<p class="changes-saved">' . sprintf(__( 'The attachment "%1$s" was successfully deleted.', 'profilebuilder'), $_GET['fileName']) . '</p><!-- .changes-saved -->';
					echo $editProfileFilterArray['attachmentChangesSaved'] = apply_filters('wppb_edit_profile_attachment_changes_saved', $editProfileFilterArray['attachmentChangesSaved']);
					unset($_GET['fileType']);
					unset($_GET['fileName']);
				}
			}
			
			/* all the other messages/errors */
			$nrOfBadUploads = 0;
			$nrOfBadUploads = count($uploadName);
			$nrOfBadExtUploads = count($uploadExt);
			if (($changesSaved == 'yes') && ($changesSavedNoMatchingPass == 'no')  && ($changesSavedNoPass == 'no') && ($changesSavedNoEmail == 'no') && ($changesSavedNoEmailExist == 'no') && ($avatarUpload == 'yes') && ($nrOfBadUploads == 0) && ($nrOfBadExtUploads == 0)){
				$editProfileFilterArray['allChangesSaved'] = '
					<p class="changes-saved">'. __('The changes have been successfully saved.', 'profilebuilder') .'</p><!-- .changes-saved -->';
				echo $editProfileFilterArray['allChangesSaved'] = apply_filters('wppb_edit_profile_all_changes_saved', $editProfileFilterArray['allChangesSaved']);
				
			}elseif (($changesSaved == 'yes') && ($changesSavedNoEmailExist == 'yes') && ($previousError == 'no')){
				$editProfileFilterArray['allChangesSavedExceptExistingEmail'] = '<p class="semi-saved">'. sprintf(__( 'The email address you entered is already registered to a different user.%1$sThe email address was %2$sNOT%3$s updated along with the rest of the information.', 'profilebuilder'), '<br/>', '<span class="error">', '</span>') .'</p>';
				echo $editProfileFilterArray['allChangesSavedExceptExistingEmail'] = apply_filters('wppb_edit_profile_all_changes_saved_except_existing_email', $editProfileFilterArray['allChangesSavedExceptExistingEmail']);
				$previousError = 'yes';
				
			}elseif (($changesSaved == 'yes') && ($changesSavedNoEmail == 'yes') && ($previousError == 'no')){
				$editProfileFilterArray['allChangesSavedExceptInvalidEmail'] = '<p class="semi-saved">'. sprintf(__( 'The email address you entered is invalid.%1$sThe email address was %2$sNOT%3$s updated along with the rest of the information.', 'profilebuilder'), '<br/>', '<span class="error">', '</span>') .'</p>';
				echo $editProfileFilterArray['allChangesSavedExceptInvalidEmail'] = apply_filters('wppb_edit_profile_all_changes_saved_except_invalid_email', $editProfileFilterArray['allChangesSavedExceptInvalidEmail']);
				$previousError = 'yes';
				
			}elseif (($changesSaved == 'yes') && ($changesSavedNoMatchingPass == 'yes') && ($previousError == 'no')){
				$editProfileFilterArray['allChangesSavedMismatchedPass'] = '<p class="semi-saved">'. sprintf(__( 'The passwords you entered do not match.%1$sThe password was %2$sNOT%3$s updated along with the rest of the information.', 'profilebuilder'), '<br/>', '<span class="error">', '</span>') .'</p>';
				echo $editProfileFilterArray['allChangesSavedMismatchedPass'] = apply_filters('wppb_edit_profile_all_changes_saved_except_mismatch_password', $editProfileFilterArray['allChangesSavedMismatchedPass']);
				$previousError = 'yes';
				
			}elseif (($changesSaved == 'yes') && ($changesSavedNoPass == 'yes') && ($previousError == 'no')){
				$editProfileFilterArray['allChangesSavedUncompletedPass'] = '<p class="semi-saved">'. sprintf(__( 'You didn\'t complete both password fields.%1$sThe password was %2$sNOT%3$s updated along with the rest of the information.', 'profilebuilder'), '<br/>', '<span class="error">', '</span>') .'</p>';
				echo $editProfileFilterArray['allChangesSavedUncompletedPass'] = apply_filters('wppb_edit_profile_all_changes_saved_except_uncompleted_password', $editProfileFilterArray['allChangesSavedUncompletedPass']);
				$previousError = 'yes';
				
			}elseif ($allRequiredCompleted == 'no'){
				$editProfileFilterArray['errorSavingChanges'] = '<p class="error">'.$errorMessage.'<br/>'. __('Your profile was NOT updated, since not all required fields were completed!', 'profilebuilder').'</p><!-- .error -->';
				echo $editProfileFilterArray['errorSavingChanges'] = apply_filters('wppb_edit_profile_error_saving_changes', $editProfileFilterArray['errorSavingChanges']);
			}
			
			$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
			if (file_exists ( $wppb_premium.'extra.fields.php' )){
				if (($changesSaved == 'yes') && ($nrOfBadUploads > 0) && ($previousError == 'no')){
					$lastOne = 0;
					$editProfileFilterArray['errorUploadingAttachments'] = '
						<p class="semi-saved">'. __('There was an error while trying to upload the following attachments:', 'profilebuilder') .'<br/>
							<span class="error">';
								foreach ($uploadName as $key => $name){
									$lastOne++;
									$editProfileFilterArray['errorUploadingAttachments'] .= $name;
									if ($nrOfBadUploads-$lastOne > 0) 
										$editProfileFilterArray['errorUploadingAttachments'] .= ';<span style="padding-left:10px"></span>';
								}
					$editProfileFilterArray['errorUploadingAttachments'] .= '</span><br/>' . sprintf(__( 'Possible cause: the size was bigger than %1$sb. The listed attachements were %2$sNOT%3$s updated along with the rest of the information.', 'profilebuilder'), WPPB_SERVER_MAX_UPLOAD_SIZE_MEGA, '<span class="error">', '</span>') . '</p>';
					echo $editProfileFilterArray['errorUploadingAttachments'] = apply_filters('wppb_edit_profile_error_uploading_attachments', $editProfileFilterArray['errorUploadingAttachments']);
					$previousError = 'yes';
					
				}if (($changesSaved == 'yes') && ($avatarUpload == 'no') && ($previousError == 'no')){
					$editProfileFilterArray['errorUploadingAvatar'] = '
						<p class="semi-saved">'.
							__('There was an error while trying to upload your avatar picture.', 'profilebuilder') .'<br/>'. __('Possible cause: size/incorrect file-type.', 'profilebuilder') .'<br/>'. __('The avatar was', 'profilebuilder') .' <span class="error">'. __('NOT', 'profilebuilder') .'</span> '. __('updated along with the rest of the information.', 'profilebuilder') .'
						</p>';
					echo $editProfileFilterArray['errorUploadingAvatar'] = apply_filters('wppb_edit_profile_error_uploading_avatar', $editProfileFilterArray['errorUploadingAvatar']);
					$previousError = 'yes';
					
				}elseif (($changesSaved == 'yes') && ($nrOfBadExtUploads != 0) && ($previousError == 'no')){
					$editProfileFilterArray['errorUploadingAttachmentExts'] = '
						<p class="semi-saved">'.
							__('There was an error while trying to upload the following attachment(s)', 'profilebuilder') .': <span class="error">';
							foreach ($uploadExt as $key5 => $name5){
								$lastOne++;
								$editProfileFilterArray['errorUploadingAttachmentExts'] .= $name5;
								if ($nrOfBadExtUploads-$lastOne > 0) 
									$editProfileFilterArray['errorUploadingAttachmentExts'] .= ';<span style="padding-left:10px"></span>';
							}
							$editProfileFilterArray['errorUploadingAttachmentExts'] .= '</span><br/>' . sprintf(__( 'Only files with the following extension(s) can be uploaded: %1$s<br/>This file was %2$sNOT%3$s updated along with the rest of the information.', 'profilebuilder'), '<span class="error">'.$allowedExtensions.'</span>', '<span class="error">', '</span>') . '</p>';
					echo $editProfileFilterArray['errorUploadingAttachmentExts'] = apply_filters('wppb_edit_profile_error_uploading_attachment', $editProfileFilterArray['errorUploadingAttachmentExts']);
					$previousError = 'yes';
				}
			}
	 
			/* use this action hook to add extra content before the edit profile form. */
			do_action( 'wppb_before_edit_profile_fields' );
		
?>
	
			<form enctype="multipart/form-data" method="post" id="edituser" class="user-forms" action="<?php wppb_curpageurl(); ?>">
<?php 
				echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.WPPB_SERVER_MAX_UPLOAD_SIZE_BYTE.'" /><!-- set the MAX_FILE_SIZE to the server\'s current max upload size in bytes -->';			
				
				$editProfileFilterArray2['contentName1'] = '<p class="nameHeader"><strong>'. __('Name', 'profilebuilder') .'</strong></p>';
				$editProfileFilterArray2['contentName1'] = apply_filters('wppb_edit_profile_content_name1', $editProfileFilterArray2['contentName1']);
			
				if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email')){
					if ($wppb_defaultOptions['email'] == 'show'){
						$editProfileFilterArray2['contentName2'] = '
							<p class="email">
								<label for="email">'. __('Email', 'profilebuilder') .'</label>
								<input class="text-input" name="email" type="text" id="email" value="'. get_the_author_meta( 'user_email', $current_user->id ) .'" disabled="disabled"/> <span class="wppb-description-delimiter"> '. __('The email cannot be changed.', 'profilebuilder') .'</span>
							</p><!-- .first_name -->';
						$editProfileFilterArray2['contentName2'] = apply_filters('wppb_edit_profile_content_name2_with_email', $editProfileFilterArray2['contentName2'], $current_user->id);
					}
				}else{
					if ($wppb_defaultOptions['username'] == 'show'){
						$editProfileFilterArray2['contentName2'] = '
							<p class="username">
								<label for="user_login">'. __('Username', 'profilebuilder') .'</label>
								<input class="text-input" name="user_login" type="text" id="user_login" value="'. get_the_author_meta( 'user_login', $current_user->id ) .'" disabled="disabled"/> <span class="wppb-description-delimiter"> '. __('The usernames cannot be changed.', 'profilebuilder') .'</span>
							</p><!-- .first_name -->';
						$editProfileFilterArray2['contentName2'] = apply_filters('wppb_edit_profile_content_name2', $editProfileFilterArray2['contentName2'], $current_user->id);
					}
				}
					
				if ($wppb_defaultOptions['firstname'] == 'show'){
					$errorVar = '';
					$errorMark = '';
					if ($wppb_defaultOptions['firstnameRequired'] == 'yes'){
						$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
						if (isset($_POST['first_name'])){
							if (trim($_POST['first_name']) == ''){
								$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator.', 'profilebuilder') .'"/>';
								$errorVar = ' errorHolder';
							}
						}
					}
					$editProfileFilterArray2['contentName3'] = '
						<p class="first_name'.$errorVar.'">
							<label for="first_name">'. __('First Name', 'profilebuilder') .$errorMark.'</label>
							<input class="text-input" name="first_name" type="text" id="first_name" value="'. get_the_author_meta( 'first_name', $current_user->id ) .'" />
						</p><!-- .first_name -->';
					$editProfileFilterArray2['contentName3'] = apply_filters('wppb_edit_profile_content_name3', $editProfileFilterArray2['contentName3'], $current_user->id, $errorVar, $errorMark);	
				}
					
				if ($wppb_defaultOptions['lastname'] == 'show'){
					$errorVar = '';
					$errorMark = '';
					if ($wppb_defaultOptions['lastnameRequired'] == 'yes'){
						$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
						if (isset($_POST['last_name'])){
							if (trim($_POST['last_name']) == ''){
								$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator.', 'profilebuilder') .'"/>';
								$errorVar = ' errorHolder';
							}
						}
					}					
					$editProfileFilterArray2['contentName4'] = '
						<p class="last_name'.$errorVar.'">
							<label for="last_name">'. __('Last Name', 'profilebuilder') .$errorMark.'</label>
							<input class="text-input" name="last_name" type="text" id="last_name" value="'. get_the_author_meta( 'last_name', $current_user->id ) .'" />
						</p><!-- .last_name -->';
					$editProfileFilterArray2['contentName4'] = apply_filters('wppb_edit_profile_content_name4', $editProfileFilterArray2['contentName4'], $current_user->id);
				}
					
				if ($wppb_defaultOptions['nickname'] == 'show'){
					$errorVar = '';
					$errorMark = '';
					if ($wppb_defaultOptions['nicknameRequired'] == 'yes'){
						$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
						if (isset($_POST['nickname'])){
							if (trim($_POST['nickname']) == ''){
								$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator.', 'profilebuilder') .'"/>';
								$errorVar = ' errorHolder';
							}
						}
					}					
					$editProfileFilterArray2['contentName5'] = '
						<p class="nickname'.$errorVar.'">
							<label for="nickname">'. __('Nickname', 'profilebuilder') .$errorMark.'</label>
							<input class="text-input" name="nickname" type="text" id="nickname" value="'. get_the_author_meta( 'nickname', $current_user->id ) .'" />
						</p><!-- .nickname -->';
					$editProfileFilterArray2['contentName5'] = apply_filters('wppb_edit_profile_content_name5', $editProfileFilterArray2['contentName5'], $current_user->id, $errorVar, $errorMark);	
				}
					
				if ($wppb_defaultOptions['dispname'] == 'show'){
					$errorVar = '';
					$errorMark = '';
					if ($wppb_defaultOptions['dispnameRequired'] == 'yes'){
						$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
						if (isset($_POST['display_name'])){
							if (trim($_POST['display_name']) == ''){
								$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator.', 'profilebuilder') .'"/>';
								$errorVar = ' errorHolder';
							}
						}
					}		
					
					$editProfileFilterArray2['displayName'] = '
						<p class="display_name'.$errorVar.'">
							<label for="display_name">'. __('Display name publicly as', 'profilebuilder') .$errorMark.'</label>
							<select name="display_name" id="display_name">';
								$public_display = array();
								$public_display['display_username']  = get_the_author_meta('user_login', $current_user->id);
								$thisFirstName = get_the_author_meta('first_name', $current_user->id);
								if ( !empty($thisFirstName))
									$public_display['display_firstname'] = get_the_author_meta('first_name', $current_user->id);
								$thisLastName = get_the_author_meta('last_name', $current_user->id);
								if ( !empty($thisLastName))
									$public_display['display_lastname'] = get_the_author_meta('last_name', $current_user->id);
								$public_display['display_nickname']  = get_the_author_meta('nickname', $current_user->id);
								if ( !empty($thisFirstName) && !empty($thisLastName) ) {
									$public_display['display_firstlast'] = $thisFirstName . ' ' . $thisLastName;
									$public_display['display_lastfirst'] = $thisLastName . ' ' . $thisFirstName;
								}
								$thisDisplayName = get_the_author_meta('display_name', $current_user->id);
								if ( !in_array( $thisDisplayName, $public_display ) )               // Only add this if it isn't duplicated elsewhere
									$public_display = array( 'display_displayname' => $thisDisplayName ) + $public_display;
								$public_display = array_map( 'trim', $public_display );
								foreach ( $public_display as $id => $item ) {
									$editProfileFilterArray2['displayName'] .= '<option id="'.$id.'" value="'.$item.'"';
									if ($thisDisplayName == $item)
										$editProfileFilterArray2['displayName'] .= ' selected';
									$editProfileFilterArray2['displayName'] .= '>'.$item.'</option>';
								}
						$editProfileFilterArray2['displayName'] .= '
							</select>
						</p><!-- .display_name -->';
						
					$editProfileFilterArray2['displayName'] = apply_filters('wppb_edit_profile_display_name', $editProfileFilterArray2['displayName'], $current_user->id, $errorVar, $errorMark);	
				}

				$editProfileFilterArray2['contentInfo1'] = '<p class="contactInfoHeader"><strong>'. __('Contact Info', 'profilebuilder') .'</strong></p>';
				$editProfileFilterArray2['contentInfo1'] = apply_filters('wppb_edit_profile_content_info1', $editProfileFilterArray2['contentInfo1']);			
				
				if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email')){
				}else{
					if ($wppb_defaultOptions['email'] == 'show'){
						$errorVar = '';
						$errorMark = '';
						if ($wppb_defaultOptions['emailRequired'] == 'yes'){
							$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
							if (isset($_POST['email'])){
								if (trim($_POST['email']) == ''){
									$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator.', 'profilebuilder') .'"/>';
									$errorVar = ' errorHolder';
								}
							}
						}					
						$editProfileFilterArray2['contentInfo2'] = '
							<p class="form-email'.$errorVar.'">
								<label for="email">'. __('E-mail', 'profilebuilder') .$errorMark.'</label>
								<input class="text-input" name="email" type="text" id="email" value="'. get_the_author_meta( 'user_email', $current_user->id ) .'" />
								<span class="wppb-description-delimiter">'. __('(required)', 'profilebuilder') .'</span>
							</p><!-- .form-email -->';
						$editProfileFilterArray2['contentInfo2'] = apply_filters('wppb_edit_profile_content_info2', $editProfileFilterArray2['contentInfo2'], $current_user->id, $errorVar, $errorMark);
					}
				}	
					
				if ($wppb_defaultOptions['website'] == 'show'){
					$errorVar = '';
					$errorMark = '';
					if ($wppb_defaultOptions['websiteRequired'] == 'yes'){
						$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
						if (isset($_POST['website']) && (trim($_POST['website']) == '')){
							$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator.', 'profilebuilder') .'"/>';
							$errorVar = ' errorHolder';
						}
					}					
					$editProfileFilterArray2['contentInfo3'] = '
						<p class="form-website'.$errorVar.'">
							<label for="website">'. __('Website', 'profilebuilder') .$errorMark.'</label>
							<input class="text-input" name="website" type="text" id="website" value="'. get_the_author_meta( 'user_url', $current_user->id ) .'" />
						</p><!-- .form-website -->';
					$editProfileFilterArray2['contentInfo3'] = apply_filters('wppb_edit_profile_content_info3', $editProfileFilterArray2['contentInfo3'], $current_user->id, $errorVar, $errorMark);
				}

				if ($wppb_defaultOptions['aim'] == 'show'){
					$errorVar = '';
					$errorMark = '';
					if ($wppb_defaultOptions['aimRequired'] == 'yes'){
						$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
						if (isset($_POST['aim']) && (trim($_POST['aim']) == '')){
							$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator.', 'profilebuilder') .'"/>';
							$errorVar = ' errorHolder';
						}
					}					
					$editProfileFilterArray2['contentInfo4'] = '
						<p class="form-aim'.$errorVar.'">
							<label for="aim">'. __('AIM', 'profilebuilder') .'</label>
							<input class="text-input" name="aim" type="text" id="aim" value="'. get_the_author_meta( 'aim', $current_user->id ) .'" />
						</p><!-- .form-aim -->';
					$editProfileFilterArray2['contentInfo4'] = apply_filters('wppb_edit_profile_content_info4', $editProfileFilterArray2['contentInfo4'], $current_user->id, $errorVar, $errorMark);
				}
					
				if ($wppb_defaultOptions['yahoo'] == 'show'){
					$errorVar = '';
					$errorMark = '';
					if ($wppb_defaultOptions['yahooRequired'] == 'yes'){
						$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
						if (isset($_POST['yim']) && (trim($_POST['yim']) == '')){
							$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator.', 'profilebuilder') .'"/>';
							$errorVar = ' errorHolder';
						}
					}					
					$editProfileFilterArray2['contentInfo5'] = '
						<p class="form-yim'.$errorVar.'">
							<label for="yim">'. __('Yahoo IM', 'profilebuilder') .$errorMark.'</label>
							<input class="text-input" name="yim" type="text" id="yim" value="'. get_the_author_meta( 'yim', $current_user->id ) .'" />
						</p><!-- .form-yim -->';
					$editProfileFilterArray2['contentInfo5'] = apply_filters('wppb_edit_profile_content_info5', $editProfileFilterArray2['contentInfo5'], $current_user->id, $errorVar, $errorMark);
				}
	 
				if ($wppb_defaultOptions['jabber'] == 'show'){
					$errorVar = '';
					$errorMark = '';
					if ($wppb_defaultOptions['jabberRequired'] == 'yes'){
						$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
						if (isset($_POST['jabber']) && (trim($_POST['jabber']) == '')){
							$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator.', 'profilebuilder') .'"/>';
							$errorVar = ' errorHolder';
						}
					}					
					$editProfileFilterArray2['contentInfo6'] = '
						<p class="form-jabber'.$errorVar.'">
							<label for="jabber">'. __('Jabber / Google Talk', 'profilebuilder') .$errorMark.'</label>
							<input class="text-input" name="jabber" type="text" id="jabber" value="'. get_the_author_meta( 'jabber', $current_user->id ) .'" />
						</p><!-- .form-jabber -->';
					$editProfileFilterArray2['contentInfo6'] = apply_filters('wppb_edit_profile_content_info6', $editProfileFilterArray2['contentInfo6'], $current_user->id, $errorVar, $errorMark);
				}
					
				$editProfileFilterArray2['aboutYourself1'] = '<p class="aboutYourselfHeader"><strong>'. __('About Yourself', 'profilebuilder') .'</strong></p>';
				$editProfileFilterArray2['aboutYourself1'] = apply_filters('wppb_edit_profile_content_about_yourself1', $editProfileFilterArray2['aboutYourself1']);
					
				if ($wppb_defaultOptions['bio'] == 'show'){
					$errorVar = '';
					$errorMark = '';
					if ($wppb_defaultOptions['bioRequired'] == 'yes'){
						$errorMark = '<font color="red" title="'. __('This field is marked as required by the administrator.', 'profilebuilder') .'">*</font>';
						if (isset($_POST['description']) && (trim($_POST['description']) == '')){
							$errorMark = '<img src="'.WPPB_PLUGIN_URL . '/assets/images/pencil_delete.png" title="'. __('This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator.', 'profilebuilder') .'"/>';
							$errorVar = ' errorHolder';
						}
					}					
					$editProfileFilterArray2['aboutYourself2'] = '
						<p class="form-description'.$errorVar.'">
							<label for="description">'. __('Biographical Info', 'profilebuilder') .$errorMark.'</label>
							<textarea class="text-input" name="description" id="description" rows="5" cols="30">'. get_the_author_meta( 'description', $current_user->id ) .'</textarea>
						</p><!-- .form-description -->';
					$editProfileFilterArray2['aboutYourself2'] = apply_filters('wppb_edit_profile_content_about_yourself2', $editProfileFilterArray2['aboutYourself2'], $current_user->id, $errorVar, $errorMark);
				}
					
				if ($wppb_defaultOptions['password'] == 'show'){
				
					$editProfileFilterArray2['aboutYourself3'] = '
						<p class="form-password">
							<label for="pass1">'. __('New Password', 'profilebuilder') .'</label>
							<input class="text-input" name="pass1" type="password" id="pass1" />
						</p><!-- .form-password -->

						<p class="form-password'.$errorVar.'">
							<label for="pass2">'. __('Repeat Password', 'profilebuilder') .$errorMark.'</label>
							<input class="text-input" name="pass2" type="password" id="pass2" />
						</p><!-- .form-password -->';
					$editProfileFilterArray2['aboutYourself3'] = apply_filters('wppb_edit_profile_content_about_yourself3', $editProfileFilterArray2['aboutYourself3'], $errorVar, $errorMark);
				}
					

				$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
				if (file_exists ( $wppb_premium.'extra.fields.php' )){
					require_once($wppb_premium.'extra.fields.php');
					
					$page = 'edit_profile';
					$returnedValue = wppb_extra_fields($current_user->id, $extraFieldsErrorHolder, $editProfileFilterArray2, $page, $error, $_POST);
					
					//copy over extra fields to the rest of the fieldso on the edit profile
					foreach($returnedValue as $key => $value)
						$editProfileFilterArray2[$key] = apply_filters('wppb_edit_profile_content_'.$key, $value, $returnedValue, $key );
				}
				
				/* additional filter, just in case it is needed  */
				$editProfileFilterArray2['extraEditProfileFilter'] = '';
				$editProfileFilterArray2['extraEditProfileFilter'] = apply_filters('extraEditProfileFilter', $editProfileFilterArray2['extraEditProfileFilter']);
				/* END additional filter, just in case it is needed */
				
				
				$editProfileFilterArray2 = apply_filters('wppb_edit_profile', $editProfileFilterArray2);
				foreach ($editProfileFilterArray2 as $key => $value)
					echo $value;
?>
				
				<p class="form-submit">
					<input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'profilebuilder'); ?>" />
					<?php // wp_nonce_field( 'update-user' ) ?>
					<input name="action" type="hidden" id="action" value="update-user" />
				</p><!-- .form-submit -->
				<?php wp_nonce_field('verify_edit_user','edit_nonce_field'); ?>
				</form><!-- #edituser -->
			
<?php 		
			//use this action hook to add extra content after the edit profile form.
			do_action( 'wppb_after_edit_profile_fields' ); 
		
		}
?>
			
	</div>	
	
<?php

	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}
?>