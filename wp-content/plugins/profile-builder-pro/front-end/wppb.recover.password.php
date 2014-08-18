<?php
//function needed to check if the current page already has a ? sign in the address bar
if(!function_exists('wppb_curpageurl_password_recovery')){
    function wppb_curpageurl_password_recovery() {
		$pageURL = 'http';
		if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")) {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	
		$questionPos = strpos( (string)$pageURL, '?' );
		$submittedPos = strpos( (string)$pageURL, 'submitted=yes' );
		
		if ($submittedPos !== false)
			return $pageURL;
		elseif($questionPos !== false)
			return $pageURL.'&submitted=yes';
		else
			return $pageURL.'?submitted=yes';
    }
}

if(!function_exists('wppb_curpageurl_password_recovery2')){
    function wppb_curpageurl_password_recovery2($user_login, $id) {
	
		global $wpdb;
		$pageURL = 'http';
		
		if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")) {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	
		$questionPos = strpos( (string)$pageURL, '?' );
		
		
		$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
		if ( empty($key) ) {
			// Generate something random for a key...
			$key = wp_generate_password(20, false);
			do_action('wppb_retrieve_password_key', $user_login, $key);
			// Now insert the new md5 key into the db
			$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
		}
			
		//$key = md5($user_login.'RMPBP'.$id.'PWRCVR');
		
		if($questionPos !== false){
			//$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
			return $pageURL.'&loginName='.$user_login.'&key='.$key;
		}else{
			//$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
			return $pageURL.'?loginName='.$user_login.'&key='.$key;
		}
    }
}

if(!function_exists('wppb_curpageurl_password_recovery3')){
    function wppb_curpageurl_password_recovery3() {
		$pageURL = 'http';
		if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")) {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	
		$questionPos = strpos( (string)$pageURL, '?' );
		$finalActionPos = strpos( (string)$pageURL, 'finalAction=yes' );
		
		if ($finalActionPos !== false)
			return $pageURL;
		elseif($questionPos !== false)
			return $pageURL.'&finalAction=yes';
		else
			return $pageURL.'?finalAction=yes';
    }
}

function wppb_check_for_unapproved_user($data, $what){
	$retArray = array( 0 => '', 1 => '');
	$retMessage = '';
	$messageNo = '';
	
	$wppb_generalSettings = get_option('wppb_general_settings');
	
	if($wppb_generalSettings['adminApproval'] == 'yes'){
	
		if ($what == 'user_email'){
			require_once(ABSPATH . WPINC . '/ms-functions.php');
			$userID = get_user_id_from_string( $data );
		
		}else{
			$user = get_user_by('login', $data);
			$userID = $user->ID;
		}
		

		if (wp_get_object_terms( $userID, 'user_status' )){
			$retMessage = '<strong>'. __('ERROR', 'profilebuilder') . '</strong>: ' . __('Your account has to be confirmed by an administrator before you can use the "Password Reset" feature.', 'profilebuilder');
			$retMessage = apply_filters('wppb_recover_password_unapporved_user', $retMessage);
			
			$messageNo = '6';
		
		}
	}
	
	return $retArray = array(0 => $retMessage, 1 => $messageNo);
}


function wppb_front_end_password_recovery(){
	$recoverPasswordFilterArray = array();
	$message = '';
	$messageNo = '';	
	$message2 = '';
	$messageNo2 = '';
	
	global $wpdb;
	
	$linkLoginName = '';
	$linkKey = '';
	
	ob_start();

	
	/* If the user entered an email/username, process the request */
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'recover_password' && wp_verify_nonce($_POST['password_recovery_nonce_field'],'verify_true_password_recovery') ) {
		
		$postedData = $_POST['username_email'];	//we get the raw data
		//check to see if it's an e-mail (and if this is valid/present in the database) or is a username
		if (is_email($postedData)){
			if (email_exists($postedData)){
				$retVal = wppb_check_for_unapproved_user($postedData, 'user_email');
				if ($retVal[0] != ''){
					$message = $retVal[0];
					$messageNo = $retVal [1];
					
				}else{
					$recoverPasswordFilterArray['sentMessage1'] = sprintf(__('A password reset email has been sent to %1$s.<br/>Following the link sent in the email address will reset the password.', 'profilebuilder'), $postedData);
					$recoverPasswordFilterArray['sentMessage1'] = apply_filters('wppb_recover_password_sent_message1', $recoverPasswordFilterArray['sentMessage1'], $postedData);
					$message = $recoverPasswordFilterArray['sentMessage1'];
					$messageNo = '1';
					
					//verify e-mail validity
					$query = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE user_email='".$postedData."'");
					$requestedUserID = $query[0]->ID;
					$requestedUserLogin = $query[0]->user_login; 
					$requestedUserEmail = $query[0]->user_email; 
					
					//send primary email message
					$recoverPasswordFilterArray['userMailMessage1']  = sprintf(__('Someone requested that the password be reset for the following account: <b>%1$s</b><br/>If this was a mistake, just ignore this email and nothing will happen.<br/>To reset your password, visit the following link:%2$s', 'profilebuilder'), $requestedUserLogin, '<a href="'.wppb_curpageurl_password_recovery2($requestedUserLogin, $requestedUserID).'">'.wppb_curpageurl_password_recovery2($requestedUserLogin, $requestedUserID).'</a>');
					$recoverPasswordFilterArray['userMailMessage1']  = apply_filters('wppb_recover_password_message_content_sent_to_user1', $recoverPasswordFilterArray['userMailMessage1'], $requestedUserID, $requestedUserLogin);
					
					$recoverPasswordFilterArray['userMailMessageTitle1'] = sprintf(__('Password Reset Feature from "%1$s"', 'profilebuilder'), $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES));
					$recoverPasswordFilterArray['userMailMessageTitle1'] = apply_filters('wppb_recover_password_message_title_sent_to_user1', $recoverPasswordFilterArray['userMailMessageTitle1']);
					
					//we add this filter to enable html encoding
					add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
					//send mail to the user notifying him of the reset request
					if (trim($recoverPasswordFilterArray['userMailMessageTitle1']) != ''){
						$sent = wp_mail($requestedUserEmail, $recoverPasswordFilterArray['userMailMessageTitle1'], $recoverPasswordFilterArray['userMailMessage1']);
						if ($sent === false){
							$recoverPasswordFilterArray['sentMessageCouldntSendMessage'] = '<b>'. __('ERROR', 'profilebuilder') .': </b>' . sprintf(__( 'There was an error while trying to send the activation link to %1$s!', 'profilebuilder'), $postedData);
							$recoverPasswordFilterArray['sentMessageCouldntSendMessage'] = apply_filters('wppb_recover_password_sent_message_error_sending', $recoverPasswordFilterArray['sentMessageCouldntSendMessage']);
							$messageNo = '5';
							$message = $recoverPasswordFilterArray['sentMessageCouldntSendMessage'];
						}
					}
				
				}
				
			}elseif (!email_exists($postedData)){
				$recoverPasswordFilterArray['sentMessage2'] = __('The email address entered wasn\'t found in the database!', 'profilebuilder').'<br/>'.__('Please check that you entered the correct email address.', 'profilebuilder');
				$recoverPasswordFilterArray['sentMessage2'] = apply_filters('wppb_recover_password_sent_message2', $recoverPasswordFilterArray['sentMessage2']);
				$messageNo = '2';
				$message = $recoverPasswordFilterArray['sentMessage2'];
			}
		}elseif (!is_email($postedData)){
			if (username_exists($postedData)){
				$retVal = wppb_check_for_unapproved_user($postedData, 'user_login');
				if ($retVal[0] != ''){
					$message = $retVal[0];
					$messageNo = $retVal [1];
					
				}else{
					$recoverPasswordFilterArray['sentMessage3'] = sprintf(__('A password reset email has been sent to %1$s.<br/>Following the link sent in the email address will reset the password.', 'profilebuilder'), $postedData);
					$recoverPasswordFilterArray['sentMessage3'] = apply_filters('wppb_recover_password_sent_message3', $recoverPasswordFilterArray['sentMessage3']);
					$messageNo = '3';
					$message = $recoverPasswordFilterArray['sentMessage3'];
					
					//verify username validity
					$query = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE user_login='".$postedData."'");
					$requestedUserID = $query[0]->ID;
					$requestedUserLogin = $query[0]->user_login; 
					$requestedUserEmail = $query[0]->user_email; 

					//send primary email message
					$recoverPasswordFilterArray['userMailMessage1']  = sprintf(__('Someone requested that the password be reset for the following account: <b>%1$s</b><br/>If this was a mistake, just ignore this email and nothing will happen.<br/>To reset your password, visit the following link:%2$s', 'profilebuilder'), $requestedUserLogin, '<a href="'.wppb_curpageurl_password_recovery2($requestedUserLogin, $requestedUserID).'">'.wppb_curpageurl_password_recovery2($requestedUserLogin, $requestedUserID).'</a>');
					$recoverPasswordFilterArray['userMailMessage1']  = apply_filters('wppb_recover_password_message_content_sent_to_user1', $recoverPasswordFilterArray['userMailMessage1'], $requestedUserID, $requestedUserLogin);
					
					$recoverPasswordFilterArray['userMailMessageTitle1'] = sprintf(__('Password Reset Feature from "%1$s"', 'profilebuilder'), $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES));
					$recoverPasswordFilterArray['userMailMessageTitle1'] = apply_filters('wppb_recover_password_message_title_sent_to_user1', $recoverPasswordFilterArray['userMailMessageTitle1']);
					
					//we add this filter to enable html encoding
					add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
					//send mail to the user notifying him of the reset request
					if (trim($recoverPasswordFilterArray['userMailMessageTitle1']) != ''){
						$sent = wp_mail($requestedUserEmail, $recoverPasswordFilterArray['userMailMessageTitle1'], $recoverPasswordFilterArray['userMailMessage1']);
						if ($sent === false){
								$recoverPasswordFilterArray['sentMessageCouldntSendMessage'] = '<b>'. __('ERROR', 'profilebuilder') .': </b>'.__('There was an error while trying to send the activation link to ', 'profilebuilder').$postedData.'!';
								$recoverPasswordFilterArray['sentMessageCouldntSendMessage'] = apply_filters('wppb_recover_password_sent_message_error_sending', $recoverPasswordFilterArray['sentMessageCouldntSendMessage']);
								$messageNo = '5';
								$message = $recoverPasswordFilterArray['sentMessageCouldntSendMessage'];
							}				
					}
				}
			}elseif (!username_exists($postedData)){
				$recoverPasswordFilterArray['sentMessage4'] = __('The username entered wasn\'t found in the database!', 'profilebuilder').'<br/>'.__('Please check that you entered the correct username.', 'profilebuilder');
				$recoverPasswordFilterArray['sentMessage4'] = apply_filters('wppb_recover_password_sent_message4', $recoverPasswordFilterArray['sentMessage4']);
				$messageNo = '4';
				$message = $recoverPasswordFilterArray['sentMessage4'];
			}
		}	
		
	}
	/* If the user used the correct key-code, update his/her password */
	elseif ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action2'] ) && $_POST['action2'] == 'recover_password2' && wp_verify_nonce($_POST['password_recovery_nonce_field2'],'verify_true_password_recovery2') ) {
		if (($_POST['passw1'] == $_POST['passw2']) && (!empty($_POST['passw1']) && !empty($_POST['passw2']))){
			$message2 = __('Your password has been successfully changed!', 'profilebuilder');
			$messageNo2 = '1';
			
			$userID = $_POST['userData'];
			$new_pass = $_POST['passw1'];
			
			//update the new password and delete the key
			do_action('wppb_password_reset', $userID, $new_pass);

			wp_set_password($new_pass, $userID);
			
			$user_info = get_userdata($userID);

			//send secondary mail to the user containing the username and the new password
			$recoverPasswordFilterArray['userMailMessage2']  = sprintf(__('You have successfully reset your password to: %1$s', 'profilebuilder'), $new_pass);
			$recoverPasswordFilterArray['userMailMessage2']  = apply_filters('wppb_recover_password_message_content_sent_to_user2', $recoverPasswordFilterArray['userMailMessage2'], $loginName);
			
			$recoverPasswordFilterArray['userMailMessageTitle2'] = sprintf(__('Password Successfully Reset for %1$s on "%2$s"', 'profilebuilder'), $user_info->user_login, $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES));
			$recoverPasswordFilterArray['userMailMessageTitle2'] = apply_filters('wppb_recover_password_message_title_sent_to_user2', $recoverPasswordFilterArray['userMailMessageTitle2']);
			
			//we add this filter to enable html encoding
			add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
			//send mail to the user notifying him of the reset request
			if (trim($recoverPasswordFilterArray['userMailMessageTitle2']) != '')
				wp_mail($user_info->user_email, $recoverPasswordFilterArray['userMailMessageTitle2'], $recoverPasswordFilterArray['userMailMessage2']);
			
			//send email to admin
			$recoverPasswordFilterArray['adminMailMessage']  = sprintf(__('%1$s has requested a password change via the password reset feature.<br/>His/her new password is:%2$s', 'profilebuilder'), $user_info->user_login, $_POST['passw1']);
			$recoverPasswordFilterArray['adminMailMessage'] = apply_filters('wppb_recover_password_message_content_sent_to_admin', $recoverPasswordFilterArray['adminMailMessage']);

			$recoverPasswordFilterArray['adminMailMessageTitle'] = sprintf(__('Password Successfully Reset for %1$s on "%2$s"', 'profilebuilder'), $user_info->user_login, $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES));
			$recoverPasswordFilterArray['adminMailMessageTitle'] = apply_filters('wppb_recover_password_message_title_sent_to_admin', $recoverPasswordFilterArray['adminMailMessageTitle']);
			
			
			//we disable the feature to send the admin a notification mail but can be still used using filters
			$recoverPasswordFilterArray['adminMailMessageTitle'] = '';
			$recoverPasswordFilterArray['adminMailMessageTitle'] = apply_filters('wppb_recover_password_message_title_sent_to_admin', $recoverPasswordFilterArray['adminMailMessageTitle']);
			
			//we add this filter to enable html encoding
			add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
			//send mail to the admin notifying him of of a user with a password reset request
			if (trim($recoverPasswordFilterArray['adminMailMessageTitle']) != '') 
				wp_mail(get_option('admin_email'), $recoverPasswordFilterArray['adminMailMessageTitle'], $recoverPasswordFilterArray['adminMailMessage']);
			
		}else{
			$message2 = __('The entered passwords don\'t match!', 'profilebuilder');
			$messageNo2 = '2';
		}
			
	}
	
?>

	<div class="wppb_holder" id="wppb_recover_password">

<?php
			/* use this action hook to add extra content before the password recovery form. */
			do_action( 'wppb_before_recover_password_fields' );

			//this is the part that handles the actual recovery
			if (isset($_GET['submitted']) && isset($_GET['loginName']) && isset($_GET['key'])){
				//get the login name and key and verify if they match the ones in the database

				$key = preg_replace('/[^a-z0-9]/i', '', $_GET['key']);
				$login = $_GET['loginName'];

				$user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $key, $login));
				
				if ( !empty( $user ) ){
					//check if the "finalAction" variable is not in the address bar, if it is, don't display the form anymore
					if (isset($_GET['finalAction']) && ($_GET['finalAction'] == 'yes')){
						if ($messageNo2 == '2'){
							$recoverPasswordFilterArray['passwordChangedMessage2'] = '<p class="error">'. $message2 .'</p><!-- .error -->';
							$recoverPasswordFilterArray['passwordChangedMessage2'] = apply_filters ('wppb_recover_password_password_changed_message2', $recoverPasswordFilterArray['passwordChangedMessage2'], $message2);
							echo $recoverPasswordFilterArray['passwordChangedMessage2'];
?>
							<form enctype="multipart/form-data" method="post" id="recover_password2" class="user-forms" action="<?php echo $url=wppb_curpageurl_password_recovery3();?>">
<?php
								$recoverPasswordFilterArray['inputPassword'] = '
									<p class="passw1">
										<label for="passw1">'. __('Password', 'profilebuilder').'</label>
										<input class="password" name="passw1" type="password" id="passw1" value="'.$_POST['passw1'].'" />
									</p><!-- .passw1 -->
									<input type="hidden" name="userData" value="'.$user->ID.'"/>
									<p class="passw2">
										<label for="passw2">'. __('Repeat Password', 'profilebuilder').'</label>
										<input class="password" name="passw2" type="password" id="passw2" value="'.$_POST['passw2'].'" />
									</p><!-- .passw2 -->';
								$recoverPasswordFilterArray['inputPassword'] = apply_filters('wppb_recover_password_input', $recoverPasswordFilterArray['inputPassword'], $_POST['passw1'], $_POST['passw2'], $user->ID);
								echo $recoverPasswordFilterArray['inputPassword'];
?>
								<p class="form-submit">
									<input name="recover_password2" type="submit" id="recover_password2" class="submit button" value="<?php _e('Reset Password', 'profilebuilder'); ?>" />
									<input name="action2" type="hidden" id="action2" value="recover_password2" />
								</p><!-- .form-submit -->
								<?php wp_nonce_field('verify_true_password_recovery2', 'password_recovery_nonce_field2'); ?>
							</form><!-- #recover_password -->
<?php
						}elseif ($messageNo2 == '1'){
							$recoverPasswordFilterArray['passwordChangedMessage1'] = '<p class="success">'. $message2 .'</p><!-- .success -->';
							$recoverPasswordFilterArray['passwordChangedMessage1'] = apply_filters ('wppb_recover_password_password_changed_message1', $recoverPasswordFilterArray['passwordChangedMessage1'], $message2);
							echo $recoverPasswordFilterArray['passwordChangedMessage1'];
						}
							
					}else{
?>
						<form enctype="multipart/form-data" method="post" id="recover_password2" class="user-forms" action="<?php echo $url=wppb_curpageurl_password_recovery3();?>">
<?php
							$recoverPasswordFilterArray['inputPassword'] = '
								<p class="passw1">
									<label for="passw1">'. __('Password', 'profilebuilder').'</label>
									<input class="password" name="passw1" type="password" id="passw1" value="'.$_POST['passw1'].'" />
								</p><!-- .passw1 -->
								<input type="hidden" name="userData" value="'.$user->ID.'"/>
								<p class="passw2">
									<label for="passw2">'. __('Repeat Password', 'profilebuilder').'</label>
									<input class="password" name="passw2" type="password" id="passw2" value="'.$_POST['passw2'].'" />
								</p><!-- .passw2 -->';
							$recoverPasswordFilterArray['inputPassword'] = apply_filters('wppb_recover_password_input', $recoverPasswordFilterArray['inputPassword'], $_POST['passw1'], $_POST['passw2'], $user->ID);
							echo $recoverPasswordFilterArray['inputPassword'];
?>
							<p class="form-submit">
								<input name="recover_password2" type="submit" id="recover_password2" class="submit button" value="<?php _e('Reset Password', 'profilebuilder'); ?>" />
								<input name="action2" type="hidden" id="action2" value="recover_password2" />
							</p><!-- .form-submit -->
							<?php wp_nonce_field('verify_true_password_recovery2', 'password_recovery_nonce_field2'); ?>
						</form><!-- #recover_password -->
<?php
					}
				}else{
					if ($messageNo2 == '1'){
						$recoverPasswordFilterArray['passwordChangedMessage1'] = '<p class="success">'. $message2 .'</p><!-- .success -->';
						$recoverPasswordFilterArray['passwordChangedMessage1'] = apply_filters ('wppb_recover_password_password_changed_message1', $recoverPasswordFilterArray['passwordChangedMessage1'], $message2);
						echo $recoverPasswordFilterArray['passwordChangedMessage1'];
					}elseif ($messageNo2 == '2'){
						$recoverPasswordFilterArray['passwordChangedMessage2'] = '<p class="error">'. $message2 .'</p><!-- .error -->';
						$recoverPasswordFilterArray['passwordChangedMessage2'] = apply_filters ('wppb_recover_password_password_changed_message2', $recoverPasswordFilterArray['passwordChangedMessage2'], $message2);
						echo $recoverPasswordFilterArray['passwordChangedMessage2'];
					}else{
						$recoverPasswordFilterArray['invalidKeyMessage'] = '<p class="warning"><b>'. __('ERROR:', 'profilebuilder') .'</b> '. __('Invalid key!', 'profilebuilder') .'</p><!-- .warning -->';
						echo $recoverPasswordFilterArray['invalidKeyMessage'] = apply_filters('wppb_recover_password_invalid_key_message', $recoverPasswordFilterArray['invalidKeyMessage']);
					}
				}
				
			}else{
				//display error message and the form
				if (($messageNo == '') || ($messageNo == '2') || ($messageNo == '4')){
					$recoverPasswordFilterArray['messageDisplay1'] = '
						<p class="warning">'.$message.'</p><!-- .warning -->';
					$recoverPasswordFilterArray['messageDisplay1'] = apply_filters('wppb_recover_password_displayed_message1', $recoverPasswordFilterArray['messageDisplay1']);
					echo $recoverPasswordFilterArray['messageDisplay1'];
					
					echo '<form enctype="multipart/form-data" method="post" id="recover_password" class="user-forms" action="'.$address = wppb_curpageurl_password_recovery().'">';
				
						$recoverPasswordFilterArray['notification'] = __('Please enter your username or email address.', 'profilebuilder').'<br/>'.__('You will receive a link to create a new password via email.', 'profilebuilder').'<br/><br/>';
						echo $recoverPasswordFilterArray['notification'] = apply_filters('wppb_recover_password_message1', $recoverPasswordFilterArray['notification']);
						
						$username_email = '';
						if (isset($_POST['username_email']))
							$username_email = $_POST['username_email'];
						$recoverPasswordFilterArray['input'] = '
							<p class="username_email">
								<label for="username_email">'. __('Username or E-mail', 'profilebuilder').'</label>
								<input class="text-input" name="username_email" type="text" id="username_email" value="'.trim($username_email).'" />
							</p><!-- .username_email -->';
						$recoverPasswordFilterArray['input'] = apply_filters('wppb_recover_password_input', $recoverPasswordFilterArray['input'], trim($username_email));
						echo $recoverPasswordFilterArray['input'];
					
				
	?>	
						<p class="form-submit">
							<input name="recover_password" type="submit" id="recover_password" class="submit button" value="<?php _e('Get New Password', 'profilebuilder'); ?>" />
							<input name="action" type="hidden" id="action" value="recover_password" />
						</p><!-- .form-submit -->
						<?php wp_nonce_field('verify_true_password_recovery', 'password_recovery_nonce_field'); ?>
					</form><!-- #recover_password -->
	<?php
				}elseif (($messageNo == '5')  || ($messageNo == '6')){
					$recoverPasswordFilterArray['messageDisplay1'] = '
						<p class="warning">'.$message.'</p><!-- .warning -->';
					$recoverPasswordFilterArray['messageDisplay1'] = apply_filters('wppb_recover_password_displayed_message1', $recoverPasswordFilterArray['messageDisplay1']);
					echo $recoverPasswordFilterArray['messageDisplay1'];
				}else{
					//display success message
					$recoverPasswordFilterArray['messageDisplay2'] = '
						<p class="success">'.$message.'</p><!-- .success -->';
					$recoverPasswordFilterArray['messageDisplay2'] = apply_filters('wppb_recover_password_displayed_message2', $recoverPasswordFilterArray['messageDisplay2']);
					echo $recoverPasswordFilterArray['messageDisplay2'];
				}
			}
			/* use this action hook to add extra content after the password recovery form. */
			do_action( 'wppb_after_recover_password_fields' );
?>
	</div>
	
<?php
	$output = ob_get_contents();
    ob_end_clean();
		
	$recoverPasswordFilterArray = apply_filters('wppb_recover_password', $recoverPasswordFilterArray);
	
    return $output;
}
?>