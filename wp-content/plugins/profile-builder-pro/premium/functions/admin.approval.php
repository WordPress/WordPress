<?php
function wppb_add_header_script(){
?>
	<script type="text/javascript">	
		// script to add an extra link to the users page listing the unapproved users
			jQuery(document).ready(function() {
				jQuery.post( ajaxurl ,  { action:"wppb_get_user_numbers"}, function(response) {
					jQuery('.wrap ul.subsubsub').append('<span id="separatorID2"> |</span> <li class="listAllUserForBulk"><a class="bulkActionUsers" href="?page=admin_approval&orderby=registered&order=desc"><?php _e('Admin Approval', 'profilebuilder');?></a> <font id="bulkUsersNo" color="grey">('+response.number+')</font></li>');	
				});					
			});		

		
		function confirmAUActionBulk(URL, message, nonce, users, todo) {
			if (confirm(message)) {
				jQuery.post( ajaxurl ,  { action:"wppb_handle_bulk_approve_unapprove_cases", URL:URL, todo:todo, users:users, _ajax_nonce:nonce}, function(response) {
					alert(response);
					window.location=URL;
				})
			}
		}
	
		// script to create a confirmation box for the user upon approving/unapproving a user
		function confirmAUAction(URL, todo, userID, nonce, actionText) {
			actionText = '<?php _e('Do you want to', 'profilebuilder');?>'+' '+actionText;
		
			if (confirm(actionText)) {
				jQuery.post( ajaxurl ,  { action:"wppb_handle_approve_unapprove_cases", URL:URL, todo:todo, userID:userID, _ajax_nonce:nonce}, function(response) {
					alert(response);
					window.location=URL;
				});			
			}
		}
		
	</script>	
<?php
}

function wppb_get_user_numbers(){
	global $wpdb;
	
	$result = mysql_query("SELECT ID FROM $wpdb->users");
	if ($result === false)
		$retVal = 0;
	else
		$retVal = mysql_num_rows($result);
	
	header( 'Content-type: application/json' );
	die( json_encode( array( 'number' => $retVal ) ) );
}

function wppb_handle_approve_unapprove_cases(){
	global $current_user;
	global $wpdb;
	
	$todo = trim($_POST['todo']);
	$userID = trim($_POST['userID']);
	$nonce = trim($_POST['_ajax_nonce']);
	
	if (! wp_verify_nonce($nonce, '_nonce_'.$current_user->ID.$userID) )
		die(__("Your session has expired! Please refresh the page and try again.", "profilebuilder"));
	
	if (current_user_can('delete_users')){
		if (($todo != '') && ($userID != '')){
		
			if ($todo == 'approve'){					
				wp_set_object_terms( $userID, NULL, 'user_status' );
				clean_object_term_cache( $userID, 'user_status' );
				
				wppb_send_new_user_status_email($userID, 'approved');
				
				die(__("User successfully approved!", "profilebuilder"));
				
			}elseif ($todo == 'unapprove'){
				wp_set_object_terms( $userID, array( 'unapproved' ), 'user_status', false);
				clean_object_term_cache( $userID, 'user_status' );
				
				wppb_send_new_user_status_email($userID, 'unapproved');
				
				die(__("User successfully unapproved!", "profilebuilder"));

			}elseif ($todo == 'delete'){
				require_once(ABSPATH.'wp-admin/includes/user.php');
				wp_delete_user( $userID );
				
				die(__("User successfully deleted!", "profilebuilder"));
			}
		}
		
	}else
		die(__("You either don't have permission for that action or there was an error!", "profilebuilder"));
}

function wppb_handle_bulk_approve_unapprove_cases(){
	global $current_user;
	global $wpdb;
	
	$todo = trim($_POST['todo']);
	$users = explode(',', trim($_POST['users']));
	$nonce = trim($_POST['_ajax_nonce']);
	
	if (! wp_verify_nonce($nonce, '_nonce_'.$current_user->ID.'_bulk') )
		die(__("Your session has expired! Please refresh the page and try again.", "profilebuilder"));
	
	if (current_user_can('delete_users')){
		if (($todo != '') && (is_array($users))){
			$iterator = 0;
			$bulkResult = mysql_query("SELECT * FROM $wpdb->users");		
		
			if ($todo == 'bulkApporve'){			
				while ($bulkRow=mysql_fetch_row($bulkResult)){
					if (in_array((string)$iterator, $users)){
						if ($current_user->ID != $bulkRow[0]){
							wp_set_object_terms( $bulkRow[0], NULL, 'user_status' );
							clean_object_term_cache( $bulkRow[0], 'user_status' );
							
							wppb_send_new_user_status_email($bulkRow[0], 'approved');
						}
					}
					$iterator++;
				}
				
				die(__("Users successfully approved!", "profilebuilder"));
				
			}elseif ($todo == 'bulkUnapporve'){
				while ($bulkRow=mysql_fetch_row($bulkResult)){
					if (in_array((string)$iterator, $users)){
						if ($current_user->ID != $bulkRow[0]){
							wp_set_object_terms( $bulkRow[0], array( 'unapproved' ), 'user_status', false);
							clean_object_term_cache( $bulkRow[0], 'user_status' );
							
							wppb_send_new_user_status_email($bulkRow[0], 'unapproved');
						}
					}
					$iterator++;
				}
				
				die(__("Users successfully unapproved!", "profilebuilder"));
				
			}elseif ($todo == 'bulkDelete'){
				require_once(ABSPATH.'wp-admin/includes/user.php');
				while ($bulkRow=mysql_fetch_row($bulkResult)){
					if (in_array((string)$iterator, $users)){
						if ($current_user->ID != $bulkRow[0]){
							wp_delete_user( $bulkRow[0] );
						}
					}
					$iterator++;
				}
				
				die(__("Users successfully deleted!", "profilebuilder"));
			}
		}
		
	}else
		die(__("You either don't have permission for that action or there was an error!", "profilebuilder"));
}

function wppb_send_new_user_status_email($userID, $newStatus){

	$user_info = get_userdata($userID);

	$bloginfo = get_bloginfo( 'name' );	
	
	$userMessageFrom = $bloginfo;
	$userMessageFrom = apply_filters('wppb_new_user_status_from_email_content', $userMessageFrom, $bloginfo, $userID, $newStatus);
	
	if ($newStatus == 'approved'){
		$userMessageSubject = sprintf(__('Your account on %1$s has been approved!', 'profilebuilder'), $bloginfo);
		$userMessageContent = sprintf(__('An administrator has just approved your account on %1$s (%2$s).', 'profilebuilder'), $bloginfo, $user_info->user_login);
		
		$userMessageSubject = apply_filters('wppb_new_user_status_subject_approved', $userMessageSubject, $userMessageFrom, $newStatus, $userID, $user_info, $bloginfo);
		$userMessageContent = apply_filters('wppb_new_user_status_content_approved', $userMessageContent, $newStatus, $userMessageFrom, $user_info);
		
	}elseif ($newStatus == 'unapproved'){
		$userMessageSubject = sprintf(__('Your account on %1$s has been unapproved!', 'profilebuilder'), $bloginfo);
		$userMessageContent = sprintf(__('An administrator has just unapproved your account on %1$s (%2$s).', 'profilebuilder'), $bloginfo, $user_info->user_login);	
		
		$userMessageSubject = apply_filters('wppb_new_user_status_subject_unapproved', $userMessageSubject, $userMessageFrom, $newStatus, $userID, $user_info, $bloginfo);
		$userMessageContent = apply_filters('wppb_new_user_status_content_unapproved', $userMessageContent, $newStatus, $userMessageFrom, $user_info);
		
	}
	
	wppb_mail( $user_info->user_email, $userMessageSubject, $userMessageContent, $userMessageFrom, $userID, '', '', '', $newStatus.'_userFunction', $user_info, $newStatus );

}

// function to register the new user_status taxonomy for the admin approval
function wppb_register_user_status_taxonomy() {

	register_taxonomy('user_status','user',array('public' => false));
}

// function to create a new wp error in case the admin approval feature is active and the given user is still unapproved
function wppb_unapproved_user_admin_error_message_handler($userdata, $password){

	if (wp_get_object_terms( $userdata->ID, 'user_status' )){
		$errorMessage = __('<strong>ERROR</strong>: Your account has to be confirmed by an administrator before you can log in.', 'profilebuilder');
	
		return new WP_Error('wppb_unapproved_user_admin_error_message', $errorMessage);
	}else
	
		return $userdata;
}

// function to prohibit user from using the default wp password recovery feature
function wppb_unapproved_user_password_reovery($allow, $userID){

	if (wp_get_object_terms( $userID, 'user_status' ))
		return new WP_Error('wppb_no_password_reset', __('Your account has to be confirmed by an administrator before you can use the "Password Recovery" feature.', 'profilebuilder'));
	else
		return true;
}

// function to add the "unapproved" status for the user who just registered using the WP registration form (only if the admin approval feature is active)
function wppb_update_user_status_on_admin_registration($user_id){

	wp_set_object_terms( $user_id, array( 'unapproved' ), 'user_status', false);
	clean_object_term_cache( $user_id, 'user_status' );	
}

	
// Set up the AJAX hooks
add_action( 'wp_ajax_wppb_get_user_numbers', 'wppb_get_user_numbers' );	
add_action( 'wp_ajax_wppb_handle_approve_unapprove_cases', 'wppb_handle_approve_unapprove_cases' );
add_action( 'wp_ajax_wppb_handle_bulk_approve_unapprove_cases', 'wppb_handle_bulk_approve_unapprove_cases' );

	
$wppb_generalSettings = get_option('wppb_general_settings', 'not_found');
if($wppb_generalSettings != 'not_found')
	if(!empty($wppb_generalSettings['adminApproval']) && ($wppb_generalSettings['adminApproval'] == 'yes')){
		/* if (is_multisite()){
			
			if (strpos($_SERVER['SCRIPT_NAME'], 'users.php')){  //global $pagenow doesn't seem to work
				add_action( 'admin_head', 'wpp_display_bulk_action_for_approved_unapproved' );

			} */
		global $pagenow;
		
		add_action( 'init', 'wppb_register_user_status_taxonomy', 1 );
		add_filter( 'wp_authenticate_user', 'wppb_unapproved_user_admin_error_message_handler', 10, 2 );
		add_filter( 'allow_password_reset', 'wppb_unapproved_user_password_reovery', 10, 2 );
		
		if ($pagenow == 'users.php'){
			add_action( 'admin_head', 'wppb_add_header_script' );
		}
		
		add_action( 'user_register', 'wppb_update_user_status_on_admin_registration' );
	}