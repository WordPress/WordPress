<?php
//functions needed for the email-confirmation on single-sites (to create the "_signups" table)
function wppb_signup_schema($oldVal, $newVal){

	// Declare these as global in case schema.php is included from a function.
	global $wpdb, $wp_queries, $charset_collate;

	if ($newVal['emailConfirmation'] == 'yes'){
		
		//The database character collate.
		$charset_collate = '';
		
		if ( ! empty( $wpdb->charset ) )
			$charset_collate = "DEFAULT CHARACTER SET ".$wpdb->charset;
		if ( ! empty( $wpdb->collate ) )
			$charset_collate .= " COLLATE ".$wpdb->collate;
		$tableName = $wpdb->prefix.'signups';

		$sql = "
			CREATE TABLE $tableName (
				domain varchar(200) NOT NULL default '',
				path varchar(100) NOT NULL default '',
				title longtext NOT NULL,
				user_login varchar(60) NOT NULL default '',
				user_email varchar(100) NOT NULL default '',
				registered datetime NOT NULL default '0000-00-00 00:00:00',
				activated datetime NOT NULL default '0000-00-00 00:00:00',
				active tinyint(1) NOT NULL default '0',
				activation_key varchar(50) NOT NULL default '',
				meta longtext,
				KEY activation_key (activation_key),
				KEY domain (domain)
			) $charset_collate;";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$res = dbDelta($sql);
	}
}
add_action( 'update_option_wppb_general_settings', 'wppb_signup_schema', 10, 2 );


//function to add new tab in the default WP userlisting with all the users who didn't confirm their account yet
function wppb_add_pending_users_header_script(){
?>
	<script type="text/javascript">	
		jQuery(document).ready(function() {
			jQuery.post( ajaxurl ,  { action:"wppb_get_unconfirmed_email_number"}, function(response) {
				jQuery('.wrap ul.subsubsub').append('<span id="separatorID"> |</span> <li class="listUsersWithUncofirmedEmail"><a class="unconfirmedEmailUsers" href="?page=unconfirmed_emails"><?php _e('Users with Unconfirmed Email Address', 'profilebuilder');?></a> <font id="unconfirmedEmailNo" color="grey">('+response.number+')</font></li>');	
			});			
		});
		
		function confirmECActionBulk(URL, message) {
			if (confirm(message)) {
				window.location=URL;
			}
		}
	
		// script to create a confirmation box for the user upon approving/unapproving a user
		function confirmECAction(URL, todo, userID, actionText) {
			actionText = '<?php _e('Do you want to', 'profilebuilder');?>'+' '+actionText;
		
			if (confirm(actionText)) {
				jQuery.post( ajaxurl ,  { action:"wppb_handle_email_confirmation_cases", URL:URL, todo:todo, userID:userID}, function(response) {
					if (response == 'ok'){
						window.location=URL;
					}else{
						alert(response);
					}
				});			
			}
		}
	</script>	
<?php
}

function wppb_get_unconfirmed_email_number(){
	global $wpdb;
	
	$result = mysql_query("SELECT * FROM ".$wpdb->prefix."signups AS t1 WHERE t1.active = 0");
	if ($result === false)
		$retVal = 0;
	else
		$retVal = mysql_num_rows($result);
	
	header( 'Content-type: application/json' );
	die( json_encode( array( 'number' => $retVal ) ) );
}
	

function wppb_handle_email_confirmation_cases() {
	global $current_user;
	global $wpdb;
	
	//die($current_user);
	$url = trim($_POST['URL']);
	$todo = trim($_POST['todo']);
	$userID = trim($_POST['userID']);
	
	if (current_user_can('delete_users'))
		if (($todo != '') && ($userID != '')){
			
			$iterator = 0;
			$result = mysql_query("SELECT * FROM ".$wpdb->prefix."signups WHERE active=0");
		
			if ($todo == 'delete'){
				while ($row=mysql_fetch_row($result)){
					if ((string)$iterator === $userID){
						$result2 = mysql_query("DELETE FROM ".$wpdb->prefix."signups WHERE user_login='".$row[3]."' AND user_email='".$row[4]."'");
						if ($result2)
							die('ok');
						else{
							$failed = __("The selected user couldn't be deleted.", "profilebuilder");
							die($failed);
						}
					}
					$iterator++;
				}
			}elseif ($todo == 'confirm'){
				while ($row=mysql_fetch_row($result)){
					if ((string)$iterator === $userID){
						$ret = wppb_manual_activate_signup($row[8]);
						die($ret);	
					}
					$iterator++;
				}
			}
		}
	
	$failed = __("You either don't have permission for that action or there was an error!", "profilebuilder");
	die($failed);
}



// FUNCTIONS USED BOTH ON THE REGISTRATION PAGE AND THE EMAIL CONFIRMATION TABLE

//function to add new variables in the address. Checks whether the new variable has to start with a ? or an &
function wppb_passed_arguments_check(){

	$verifyLink = get_permalink();
	$questionMarkPosition = strpos ( (string)$verifyLink , '?' );
	if ($questionMarkPosition !== FALSE ) //we already have 1 "?"
		$passedArgument = '&';
	else $passedArgument = '?';
	
	return $passedArgument;
}

// Hook to add AP user meta after signup autentification 
add_action('wpmu_activate_user','wppb_add_meta_to_user_on_activation',10,3);

//function that adds AP user meta after signup autentification and it is also used when editing the user uprofile
function wppb_add_meta_to_user_on_activation($user_id, $password, $meta){

	//copy the data from the meta field (default fields)
	if( !empty($meta['first_name'] ) )
		update_user_meta( $user_id, 'first_name', $meta['first_name'] );
	if( !empty($meta['last_name'] ) )
		update_user_meta( $user_id, 'last_name', $meta['last_name'] );
	if( !empty($meta['nickname'] ) )
		update_user_meta( $user_id, 'nickname', $meta['nickname'] );
	if( !empty($meta['user_url'] ) )
		update_user_meta( $user_id, 'user_url', $meta['user_url'] );
	if( !empty($meta['aim'] ) )
		update_user_meta( $user_id, 'aim', $meta['aim'] );
	if( !empty($meta['yim'] ) )
		update_user_meta( $user_id, 'yim', $meta['yim'] );
	if( !empty($meta['jabber'] ) )
		update_user_meta( $user_id, 'jabber', $meta['jabber'] );
	if( !empty($meta['description'] ) )
		update_user_meta( $user_id, 'description', $meta['description'] );
	
	//update the users role (s)he registered for
	if( !empty($meta['role'] ) ){
		$user = new WP_User($user_id);
		$user->set_role($meta['role']);
	}
	
	//copy the data from the meta fields (custom fields)
	$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
	if (file_exists ( $wppb_premium.'extra.fields.php' )){
		$wppbFetchArray = get_option('wppb_custom_fields');
		foreach ( $wppbFetchArray as $key => $value){
			switch ($value['item_type']) {
				case "input":{
					if( !empty($meta[$value['item_type'].$value['id']] ) )
						update_user_meta( $user_id, $value['item_metaName'], $meta[$value['item_type'].$value['id']] );
					break;
				}						
				case "hiddenInput":{
					if( !empty($meta[$value['item_type'].$value['id']] ) )
						update_user_meta( $user_id, $value['item_metaName'], $meta[$value['item_type'].$value['id']] );
					break;
				}
				case "checkbox":{
					if( !empty($meta[$value['item_type'].$value['id']] ) )
						update_user_meta( $user_id, $value['item_metaName'], $meta[$value['item_type'].$value['id']] );
					break;
				}
				case "radio":{
					if( !empty($meta[$value['item_type'].$value['id']] ) )
						update_user_meta( $user_id, $value['item_metaName'], $meta[$value['item_type'].$value['id']] );
					break;
				}
				case "select":{
					if( !empty($meta[$value['item_type'].$value['id']] ) )
						update_user_meta( $user_id, $value['item_metaName'], $meta[$value['item_type'].$value['id']] );
					break;
				}
				case "countrySelect":{
					if( !empty($meta[$value['item_type'].$value['id']] ) )
						update_user_meta( $user_id, $value['item_metaName'], $meta[$value['item_type'].$value['id']] );
					break;
				}
				case "timeZone":{
					if( !empty($meta[$value['item_type'].$value['id']] ) )
						update_user_meta( $user_id, $value['item_metaName'], $meta[$value['item_type'].$value['id']] );
					break;
				}
				case "datepicker":{
					if( !empty($meta[$value['item_type'].$value['id']] ) )
						update_user_meta( $user_id, $value['item_metaName'], $meta[$value['item_type'].$value['id']] );
					break;
				}
				case "textarea":{
					if( !empty($meta[$value['item_type'].$value['id']] ) )
						update_user_meta( $user_id, $value['item_metaName'], $meta[$value['item_type'].$value['id']] );
					break;
				}
				case "upload":{
					if( !empty($meta[$value['item_type'].$value['id']] ) ){
						$filename = $meta[$value['item_type'].$value['id']]; 
						
						$fileNameStartUpload = strpos ( (string)$filename , '_attachment_' );
						$originalUploadFilename = substr($filename, $fileNameStartUpload+12);
						$newFileName = 'userID_'.$user_id.'_attachment_'.$originalUploadFilename;

						$wpUploadPath = wp_upload_dir(); // Array of key => value pairs
						$target_path_original = $wpUploadPath['baseurl']."/profile_builder/attachments/";
						$fileDir = $wpUploadPath['basedir'].'/profile_builder/attachments/';
						$target_path = $target_path_original . 'userID_'.$user_id.'_attachment_'. $originalUploadFilename; 	
						
						$renamedVar = rename ($fileDir.$meta[$value['item_type'].$value['id']], $fileDir.$newFileName);
						
						if ($renamedVar)
							add_user_meta( $user_id, $value['item_metaName'], $target_path );
					}
					break;
				}
				case "avatar":{
					if( !empty($meta[$value['item_type'].$value['id']] ) ){
						$filename = $meta[$value['item_type'].$value['id']];
						
						$fileNameStartAvatar = strpos ( (string)$filename , 'originalAvatar_' );
						$originalAvatarFilename = substr($filename, $fileNameStartAvatar+15);
						$newFileName = 'userID_'.$user_id.'_originalAvatar_'.$originalAvatarFilename;
						
						$wpUploadPath = wp_upload_dir(); // Array of key => value pairs
						$target_path_original = $wpUploadPath['baseurl']."/profile_builder/avatars/";
						$fileDir = $wpUploadPath['basedir'].'/profile_builder/avatars/';
						$target_path = $target_path_original . 'userID_'.$user_id.'_originalAvatar_'. $originalAvatarFilename;
						
						$renamedVar = rename ($fileDir.'wpmuRandomID_'.$meta[$value['item_type'].$value['id'].'_radomUserNumber'].'_originalAvatar_'.$originalAvatarFilename, $fileDir.$newFileName);
						
						if ($renamedVar){
							$wp_filetype = wp_check_filetype(basename( $filename ), null );
							$attachment = array('post_mime_type' => $wp_filetype['type'],
												'post_title' => $filename, //preg_replace('/\.[^.]+$/', '', basename($_FILES[$uploadedfile]['name'])),
												'post_content' => '',
												'post_status' => 'inherit'
												);


							$attach_id = wp_insert_attachment( $attachment, $target_path);
						
							$upFile = image_downsize( $attach_id, 'thumbnail' );
							$upFile = $upFile[0];


							
							add_user_meta( $user_id, $value['item_metaName'], $upFile );
							wppb_resize_avatar($user_id);
						}
					}				
					break;
				}
			}
		}
	}
}


// function to add the new user to the signup table if email confirmation is selected as active or it is a wpmu installation
function wppb_signup_user($user, $user_email, $meta = '') {
	global $wpdb;

	// Format data
	$user = preg_replace( '/\s+/', '', sanitize_user( $user, true ) );
	$user_email = sanitize_email( $user_email );
	$key = substr( md5( time() . rand() . $user_email ), 0, 16 );
	$meta = serialize($meta);

	if ( is_multisite() ) 
		$wpdb->insert( $wpdb->signups, array('domain' => '', 'path' => '', 'title' => '', 'user_login' => $user, 'user_email' => $user_email, 'registered' => current_time('mysql', true), 'activation_key' => $key, 'meta' => $meta) );
	else
		$wpdb->insert( $wpdb->prefix.'signups', array('domain' => '', 'path' => '', 'title' => '', 'user_login' => $user, 'user_email' => $user_email, 'registered' => current_time('mysql', true), 'activation_key' => $key, 'meta' => $meta) );
	
	wppb_signup_user_notification($user, $user_email, $key, $meta);
}

/**
 * Notify user of signup success.
 *
 * Filter 'wppb_signup_user_notification_filter' to bypass this function or
 * replace it with your own notification behavior.
 *
 * Filter 'wppb_signup_user_notification_email' and
 * 'wppb_signup_user_notification_subject' to change the content
 * and subject line of the email sent to newly registered users.
 *
 * @param string $user The user's login name.
 * @param string $user_email The user's email address.
 * @param array $meta By default, an empty array.
 * @param string $key The activation key created in wppb_signup_user()
 * @return bool
 */
function wppb_signup_user_notification($user, $user_email, $key, $meta = '') {
	if ( !apply_filters('wppb_signup_user_notification_filter', $user, $user_email, $key, $meta) )
		return false;

	// Send email with activation link.
	$admin_email = get_site_option( 'admin_email' );
	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];
		
	$from_name = get_site_option( 'site_name' ) == '' ? 'WordPress' : esc_html( get_site_option( 'site_name' ) );
	$from_name = apply_filters ('wppb_signup_user_notification_email_from_field', $from_name);
	$message_headers = apply_filters ("wppb_signup_user_notification_from", "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n");

	$siteURL = wppb_curpageurl().wppb_passed_arguments_check().'key='.$key;
	
	$subject = sprintf(apply_filters( 'wppb_signup_user_notification_subject', __( '[%1$s] Activate %2$s', 'profilebuilder'), $user, $user_email, $key, $meta ), $from_name, $user);
	$message = sprintf(apply_filters( 'wppb_signup_user_notification_email', __( "To activate your user, please click the following link:\n\n%s%s%s\n\nAfter you activate, you will receive *another email* with your login.\n\n", "profilebuilder" ),$user, $user_email, $key, $meta), '<a href="'.$siteURL.'">', $siteURL, '</a>.');
	
	wppb_mail( $user_email, $subject, $message, $from_name, '', $user, '', $user_email, 'register_w_email_confirmation', $siteURL, $meta );
	
	return true;
}


/**
 * Activate a signup.
 *
 *
 * @param string $key The activation key provided to the user.
 * @return array An array containing information about the activated user and/or blog
 */
function wppb_manual_activate_signup($key) {
	global $wpdb;
	$bloginfo = get_bloginfo( 'name' );	

	if ( is_multisite() )
		$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE activation_key = %s", $key) );
	else
		$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."signups WHERE activation_key = %s", $key) );

	if ( !empty( $signup ) && !$signup->active ){
		$meta = unserialize($signup->meta);
		$user_login = $wpdb->escape($signup->user_login);
		$user_email = $wpdb->escape($signup->user_email);
		$password = base64_decode($meta['user_pass']);

		$user_id = username_exists($user_login);

		if ( ! $user_id )
			$user_id = wppb_create_user($user_login, $password, $user_email);
		else
			$user_already_exists = true;

		if ( ! $user_id )
			return __('Could not create user!', 'profilebuilder');
			
		elseif ( isset( $user_already_exists ) )
			return __('That username is already activated!', 'profilebuilder');
		
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
					return 'ok';
				else
					return __('There was an error while trying to activate the user.', 'profilebuilder');
					
		}
	}
}

/**
 * Create a user.
 *
 * @param string $user_name The new user's login name.
 * @param string $password The new user's password.
 * @param string $email The new user's email address.
 * @return mixed Returns false on failure, or int $user_id on success
 */
function wppb_create_user( $user_name, $password, $email) {
	$user_name = preg_replace( '/\s+/', '', sanitize_user( $user_name, true ) );

	$user_id = wp_create_user( $user_name, $password, $email );
	if ( is_wp_error($user_id) )
		return false;

	// Newly created users have no roles or caps until they are added to a blog.
	delete_user_option( $user_id, 'capabilities' );
	delete_user_option( $user_id, 'user_level' );

	do_action( 'wppb_new_user', $user_id );

	return $user_id;
}

//send an email to the admin regarding each and every new subscriber, and - if selected - to the user himself
function wppb_notify_user_registration_email($bloginfo, $user_name, $email, $send_credentials_via_email, $passw1, $adminApproval){

	$registerFilterArray['adminMessageOnRegistration']  = sprintf(__( 'New subscriber on %1$s.<br/><br/>Username:%2$s<br/>E-mail:%3$s<br/>', 'profilebuilder'), $bloginfo, $user_name, $email);
	if ($adminApproval == 'yes')
		$registerFilterArray['adminMessageOnRegistration'] .= '<br/>'. __('The "Admin Approval" feature was activated at the time of registration, so please remember that you need to approve this user before he/she can log in!', 'profilebuilder') ."\r\n";
	$registerFilterArray['adminMessageOnRegistration'] = apply_filters('wppb_register_admin_message_content', $registerFilterArray['adminMessageOnRegistration'], $bloginfo, $user_name, $email, $adminApproval);
	
	$registerFilterArray['adminMessageOnRegistrationSubject'] = '['. $bloginfo .'] '. __('A new subscriber has (been) registered!', 'profilebuilder');
	$registerFilterArray['adminMessageOnRegistrationSubject'] = apply_filters ('wppb_register_admin_message_title', $registerFilterArray['adminMessageOnRegistrationSubject']);

	if (trim($registerFilterArray['adminMessageOnRegistration']) != '')
		wppb_mail(get_option('admin_email'), $registerFilterArray['adminMessageOnRegistrationSubject'], $registerFilterArray['adminMessageOnRegistration'], $blogInfo, '', $user_name, $passw1, $email, 'register_w_o_admin_approval_admin_email', $adminApproval, '' );

	
	//send an email to the newly registered user, if this option was selected
	if (isset($send_credentials_via_email) && ($send_credentials_via_email == 'sending')){
		//change these variables to modify sent email message, destination and source.	
		
		$registerFilterArray['userMessageFrom'] = $bloginfo;
		$registerFilterArray['userMessageFrom'] = apply_filters('wppb_register_from_email_content', $registerFilterArray['userMessageFrom']);

		$registerFilterArray['userMessageSubject'] = __('A new account has been created for you.', 'profilebuilder');
		$registerFilterArray['userMessageSubject'] = apply_filters('wppb_register_subject_email_content', $registerFilterArray['userMessageSubject']);
		
		$registerFilterArray['userMessageContent'] = sprintf(__( 'Welcome to %1$s!<br/><br/> Your username is:%2$s and password:%3$s', 'profilebuilder'), $registerFilterArray['userMessageFrom'], $user_name, $passw1);
		if ($adminApproval == 'yes')
			$registerFilterArray['userMessageContent'] .= '<br/>'. __('Before you can access your account, an administrator needs to approve it. You will be notified via email.', 'profilebuilder');
		$registerFilterArray['userMessageContent'] = apply_filters('wppb_register_email_content', $registerFilterArray['userMessageContent'], $registerFilterArray['userMessageFrom'], $user_name, $passw1);
		
		$messageSent = wppb_mail( $email, $registerFilterArray['userMessageSubject'], $registerFilterArray['userMessageContent'], $registerFilterArray['userMessageFrom'], '', $user_name, $passw1, $email, 'register_w_o_admin_approval', $adminApproval, '' );
		
		if( $messageSent == TRUE)
			return 2; 
		else
			return 1;
	}
}

// END FUNCTIONS USED BOTH ON THE REGISTRATION PAGE AND THE EMAIL CONFIRMATION TABLE





// Set up the AJAX hooks
add_action( 'wp_ajax_wppb_get_unconfirmed_email_number', 'wppb_get_unconfirmed_email_number' );	
add_action( 'wp_ajax_wppb_handle_email_confirmation_cases', 'wppb_handle_email_confirmation_cases' );

if (is_multisite()){
			
	if (strpos($_SERVER['SCRIPT_NAME'], 'users.php')){  //global $pagenow doesn't seem to work
		add_action( 'admin_head', 'wppb_add_pending_users_header_script' );

	}
	if (file_exists ( WPPB_PLUGIN_DIR . '/premium/functions/admin.approval.php' ))
		add_action( 'user_register', 'wppb_update_user_status_on_admin_registration' );
	
}else{
	$wppb_generalSettings = get_option('wppb_general_settings', 'not_found');
	if($wppb_generalSettings != 'not_found')
		if(!empty($wppb_generalSettings['emailConfirmation']) && ($wppb_generalSettings['emailConfirmation'] == 'yes')){
			global $pagenow;
			
			if ($pagenow == 'users.php'){
				add_action( 'admin_head', 'wppb_add_pending_users_header_script' );

			}
			if (file_exists ( WPPB_PLUGIN_DIR . '/premium/functions/admin.approval.php' ))
				add_action( 'user_register', 'wppb_update_user_status_on_admin_registration' );
		}
}
	
	