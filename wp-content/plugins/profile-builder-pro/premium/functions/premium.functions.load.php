<?php if (!defined('PROFILE_BUILDER_VERSION')) exit('No direct script access allowed');
/**
 * Functions Load
 *
 */
 
// Set up the AJAX hooks
function wppb_delete(){

	if (isset($_POST['_ajax_nonce'])){
		
		if ((isset($_POST['what'])) && ($_POST['what'] == 'avatar')){
			if (! wp_verify_nonce($_POST['_ajax_nonce'], 'user'.$_POST['currentUser'].'_nonce_avatar') ){
				echo $retVal = __('The user-validation has failed - the avatar was not deleted!', 'profilebuilder');
				die();
				
			}else{
				update_user_meta( $_POST['currentUser'], $_POST['customFieldName'], '');
				update_user_meta( $_POST['currentUser'], 'resized_avatar_'.$_POST['customFieldID'], '');
				echo 'done';
				die();
			}
		}elseif ((isset($_POST['what'])) && ($_POST['what'] == 'attachment')){
			if (! wp_verify_nonce($_POST['_ajax_nonce'], 'user'.$_POST['currentUser'].'_nonce_upload') ){
				echo $retVal = __('The user-validation has failed - the attachment was not deleted!', 'profilebuilder');
				die();
				
			}else{
				update_user_meta( $_POST['currentUser'], $_POST['customFieldName'], '');
				echo 'done';
				die();
			}
		}
	}
}
add_action("wp_ajax_hook_wppb_delete", 'wppb_delete' );


//the function used to overwrite the avatar across the wp installation
function wppb_changeDefaultAvatar($avatar, $id_or_email, $size, $default, $alt) {

  global $wpdb;
  
  /* Get user info. */ 
  if(is_object($id_or_email)){
	$my_user_id = $id_or_email->user_id;
  }
  elseif(is_numeric($id_or_email)){
	$my_user_id = $id_or_email; 
  }elseif(!is_integer($id_or_email)){
	$user_info = get_user_by_email($id_or_email);
	$my_user_id = $user_info->ID;
  }else  
	$my_user_id = $id_or_email; 

  $arraySettingsPresent = get_option('wppb_custom_fields','not_found');
  if ($arraySettingsPresent != 'not_found'){
	$wppbFetchArray = get_option('wppb_custom_fields');
	foreach( $wppbFetchArray as $value ){
	  if ( $value['item_type'] == 'avatar'){
		$customUserAvatar = get_user_meta($my_user_id, 'resized_avatar_'.$value['id'], true);
		if (($customUserAvatar != '') || ($customUserAvatar != null)){				
			$avatar = "<img alt='{$alt}' src='{$customUserAvatar}' class='avatar avatar-{$value['item_options']} photo avatar-default' height='{$size}' width='{$size}' />";
		}
	  }
	}
  }

  return $avatar;
}


//the function used to resize the avatar image; the new function uses a user ID as parameter to make pages load faster
function wppb_resize_avatar($userID){

	// include the admin image API
	require_once(ABSPATH . '/wp-admin/includes/image.php');
	
	
	// retrieve first a list of all the current custom fields
	$wppbFetchArray = get_option('wppb_custom_fields');
	
	foreach ( $wppbFetchArray as $key => $value){
		if ($value['item_type'] == 'avatar'){
		
			// retrieve the original image (in original size)
			$originalAvatar = get_user_meta($userID, $value['item_metaName'], true);
			
			// we need to check if this field has an image uploaded, or else we would get an error
			if ($originalAvatar != ''){
			
				// retrieve width and height of the image
				$width = $height = '';
				
				//this checks if it only has 1 component
				if (is_numeric($value['item_options'])){
					$width = $height = $value['item_options'];
				//this checks if the entered value has 2 components
				}else{
					$sentValue = explode(',',$value['item_options']);
					$width = $sentValue[0];
					$height = $sentValue[1];
				}
					
			
				// retrieve the path where exactly in the upload dir the image is : /profile_builder/avatars/userID_ID_originalAvatar_NAME.EXTENSION
				if (is_array($originalAvatar)){
					$searchOld = strpos ( (string)$originalAvatar[0], '/profile_builder/avatars/' );
					$imagePartialPath = substr($originalAvatar[0], $searchOld);
					
				}else{
					$searchOld = strpos ( (string)$originalAvatar, '/profile_builder/avatars/' );
					$imagePartialPath = substr($originalAvatar, $searchOld);
				}
					
				// get path to image to be resized
				$wpUploadPath = wp_upload_dir(); // Array of key => value pairs
				$imagePath = $wpUploadPath['basedir'].$imagePartialPath;
				
				//add a filter for the user to select crop or resizing
				$crop = true;
				$crop = apply_filters('wppb_image_crop_resize', $crop);
				
				//we need to check if the image is not, in fact, smaller then the preset values, or it will give a fatal error
				$imageSize = getimagesize($imagePath);

				
				if (($imageSize[0] > $width) && ($imageSize[1] > $heaight)){
					$thumb = image_resize($imagePath, $width, $height, $crop);
					// value to add in the usermeta as saved image
					$copyFrom = strpos( (string)$thumb, '/profile_builder/' );
					$newImagePartial = substr($thumb, $copyFrom);

				}else{
					// value to add in the usermeta as saved image
					$copyFrom = strpos( (string)$imagePath, '/profile_builder/' );
					$newImagePartial = substr($imagePath, $copyFrom);
				}
				
				$newImage1 = $wpUploadPath['baseurl'].$newImagePartial;
				$newImage2 = $wpUploadPath['basedir'].$newImagePartial;
				
				// this if can be done using the built-in filter of wp_upload_dir if needed
				if (PHP_OS == "WIN32" || PHP_OS == "WINNT")
					$newImage2 = str_replace('\\', '/', $newImage2);

				update_user_meta( $userID, 'resized_avatar_'.$value['id'], $newImage1);
				update_user_meta( $userID, 'resized_avatar_'.$value['id'].'_relative_path', $newImage2);
			}
		}
	}
}



if ( is_admin() ){
	// add a hook to delete the user from the _signups table if either the email confirmation is activated, or it is a wpmu installation
	function wppb_delete_user_from_signups_table($user_id) {
		global $wpdb;

		$userLogin = $wpdb->get_var("SELECT user_login, user_email FROM " . $wpdb->users . " WHERE ID = '" . $user_id . "' LIMIT 1");
		if ( is_multisite() )
			$delete = $wpdb->query("DELETE FROM ".$wpdb->signups ." WHERE user_login = '" .$userLogin ."'");
		else
			$delete = $wpdb->query("DELETE FROM " . $wpdb->prefix . "signups WHERE user_login = '" .$userLogin ."'");
	}
	
	if (is_multisite())
		add_action( 'wpmu_delete_user', 'wppb_delete_user_from_signups_table');
	else{
		$wppb_generalSettings = get_option('wppb_general_settings');
				
		if ($wppb_generalSettings['emailConfirmation'] == 'yes')
			add_action( 'delete_user', 'wppb_delete_user_from_signups_table');
	}

}


/**
 * Registers the css to the datepicker on the front-end
 *
 */
function wppb_register_datepicker_styles() {

	$myStyleUrl = WPPB_PLUGIN_URL.'/premium/assets/css/ui-lightness/jquery-ui-1.8.14.custom.css';
	wp_register_style('wppb_jqueryStyleSheet', $myStyleUrl);
}
add_action('init', 'wppb_register_datepicker_styles');

/**
 * Add the css to the datepicker on the front-end
 *
 * @uses $wppb_shortcode_on_front global. Used to check if the shortcode is present on the page.
 * $wppb_shortcode_on_front global is set to true in wppb_front_end_profile_info() and wppb_front_end_register()
 */
function wppb_add_datepicker_styles() {
	global  $wppb_shortcode_on_front;
	
	if( $wppb_shortcode_on_front == true ){
		wp_print_styles( 'wppb_jqueryStyleSheet' );
	}
}
add_action('wp_footer', 'wppb_add_datepicker_styles');

/**
 * Registers the datepicker js to the fontend and wppb_init js
 *
 */
function wppb_register_datepicker_script() {

	wp_register_script( 'wppb_jqueryDatepicker2', WPPB_PLUGIN_URL.'/premium/assets/js/jquery-ui-datepicker.min.js', array( 'jquery', 'jquery-ui-core' ) );
	wp_register_script( 'wppb_init', WPPB_PLUGIN_URL.'/premium/assets/js/wppb_init.js', array( 'wppb_jqueryDatepicker2' ) );
}    
add_action('init', 'wppb_register_datepicker_script');

/**
 * Add the datepicker to the fontend and wppb_init 
 *
 * @uses $wppb_shortcode_on_front global. Used to check if the shortcode is present on the page..
 * $wppb_shortcode_on_front global is set to true in wppb_front_end_profile_info() and wppb_front_end_register()
 */
function wppb_add_datepicker_script() {

	global  $wppb_shortcode_on_front;
	
	if( $wppb_shortcode_on_front == true ){
		wp_print_scripts( 'wppb_jqueryDatepicker2' );
		wp_print_scripts( 'wppb_init' );
	}
}    
add_action('wp_footer', 'wppb_add_datepicker_script');


add_action( 'admin_print_styles-profile.php', 'wppb_add_datepicker_styles_admin_panel');
add_action( 'admin_print_styles-user-edit.php', 'wppb_add_datepicker_styles_admin_panel');

/* function to add the css to the datepicker on the admin side */
function wppb_add_datepicker_styles_admin_panel(  ) {
	
		$myStyleUrl = WPPB_PLUGIN_URL.'/premium/assets/css/ui-lightness/jquery-ui-1.8.14.custom.css';
		wp_register_style('wppb_admin_jqueryStyleSheet', $myStyleUrl);
		wp_enqueue_style( 'wppb_admin_jqueryStyleSheet');
	
}


/* add the dateformat as a variable for more personalization possibilities */
function wppb_add_datepicker_dateformat(){
?>
	<script type="text/javascript">
		var dateFormatVar = "<?php echo $dateFormat = apply_filters('wppb_datepicker_format', 'mm/dd/yy'); ?>";
	</script>
<?php
}
add_action('wp_head','wppb_add_datepicker_dateformat');

/* function to add the jquery for the datepicker on the admin side */
function wppb_add_datepicker_script_admin_panel( $hook ) {

	if(( $hook == 'profile.php' ) || ($hook == 'user-edit.php')){
	
?>
			<script type="text/javascript">
				var dateFormatVar = "<?php echo $dateFormat = apply_filters('wppb_datepicker_format', 'mm/dd/yy'); ?>";
			</script>
<?php
	
		wp_enqueue_script('jquery-ui-core');
		
		wp_register_script( 'wppb_admin_jqueryDatepicker2', WPPB_PLUGIN_URL.'/premium/assets/js/jquery-ui-datepicker.min.js');
		wp_enqueue_script( 'wppb_admin_jqueryDatepicker2' );
	}   
}    
add_action( 'admin_enqueue_scripts', 'wppb_add_datepicker_script_admin_panel');

// This function offers compatibility with the all in one event calendar plugin
function wppb_aioec_compatibility(){

	wp_deregister_script( 'jquery.tools-form');
}
add_action('admin_print_styles-users_page_ProfileBuilderOptionsAndSettings', 'wppb_aioec_compatibility');


class wppb_add_notices{
	public $pluginPrefix = '';
	public $pluginName = '';
	public $notificaitonMessage = '';
	public $pluginSerialStatus = '';
	
	function __construct($pluginPrefix, $pluginName, $notificaitonMessage, $pluginSerialStatus){
		$this->pluginPrefix = $pluginPrefix;
		$this->pluginName = $pluginName;
		$this->notificaitonMessage = $notificaitonMessage;
		$this->pluginSerialStatus = $pluginSerialStatus;
		
		add_action('admin_notices', array( $this, 'add_admin_notice' ) );
		add_action('admin_init', array( $this, 'dismiss_notification' ) );
	}
	

	// Display a notice that can be dismissed in case the serial number is inactive
	function add_admin_notice() {
		global $current_user ;
		global $pagenow;
		
		$user_id = $current_user->ID;
		
		do_action( $this->pluginPrefix.'_before_notification_displayed', $current_user, $pagenow );
		
		if ( current_user_can( 'manage_options' ) ){
			if ( $pagenow == 'index.php' ){
				$plugin_serial_status = get_option($this->pluginSerialStatus);
				if ($plugin_serial_status != 'found'){
					// Check that the user hasn't already clicked to ignore the message
					if ( ! get_user_meta($user_id, $this->pluginPrefix.'_dismiss_notification') ) {
						echo $finalMessage = apply_filters($this->pluginPrefix.'_notification_message','<div class="updated" style="padding: 15px;">'.$this->notificaitonMessage.'</div>', $this->notificaitonMessage);
						
						
					}
				}
				
				do_action( $this->pluginPrefix.'_notification_displayed', $current_user, $pagenow, $plugin_serial_status );
			}
		}
		
		do_action( $this->pluginPrefix.'_after_notification_displayed', $current_user, $pagenow );
		
	}

	function dismiss_notification() {
		global $current_user;
		
		$user_id = $current_user->ID;
		
		do_action( $this->pluginPrefix.'_before_notification_dismissed', $current_user );
		
		// If user clicks to ignore the notice, add that to their user meta 
		if ( isset($_GET[$this->pluginPrefix.'_dismiss_notification']) && '0' == $_GET[$this->pluginPrefix.'_dismiss_notification'] ) {
			add_user_meta($user_id, $this->pluginPrefix.'_dismiss_notification', 'true', true); 
		}
		
		do_action( $this->pluginPrefix.'_after_notification_dismissed', $current_user );
	}
}

//get current serial status
if (file_exists ( WPPB_PLUGIN_DIR . '/premium/addons/addon.php' ))
	$wppb_profile_builder_pro_hobbyist_serial_status = get_option('wppb_profile_builder_pro_serial_status');
else
	$wppb_profile_builder_pro_hobbyist_serial_status = get_option('wppb_profile_builder_hobbyist_serial_status');
	
if ($wppb_profile_builder_pro_hobbyist_serial_status == 'notFound'){
	$message = sprintf(__('Your <strong>Profile Builder</strong> serial number is invalid or missing. Please %1$sregister your copy%2$s of Profile Builder to receive access to automatic updates and support. Need a license key? %3$sPurchase one now%4$s %5$sDismiss%6$s', 'profilebuilder'), "<a href='admin.php?page=ProfileBuilderOptionsAndSettings#register-profile-builder'>", "</a>", "<a href='http://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=PB&utm_medium=dashboard&utm_campaign=PB-Purchase' target='_blank' class='button-primary'>", "</a>", "<a href='?wppb_dismiss_notification=0' style='float:right;'>", "</a>");
	$wppb_pluginNotification = new wppb_add_notices('wppb', 'profile_builder_pro', $message, 'wppb_profile_builder_pro_hobbyist_serial_status');
	
}elseif ($wppb_profile_builder_pro_hobbyist_serial_status == 'expired'){
	$message = sprintf(__('Your <strong>Profile Builder</strong> 1 year licence has expired. Please %1$sRenew Your Licence%2$s to receive access to automatic updates and priority support. %3$sPurchase one now%4$s %5$sDismiss%6$s', 'profilebuilder'), "<a href='http://www.cozmoslabs.com/downloads/profile-builder-pro-1-year/?utm_source=PB&utm_medium=dashboard&utm_campaign=PB-Renewal' target='_blank'>", "</a>", "<a href='http://www.cozmoslabs.com/downloads/profile-builder-pro-1-year/?utm_source=PB&utm_medium=dashboard&utm_campaign=PB-Renewal' target='_blank' class='button-primary'>", "</a>", "<a href='?wppb_dismiss_notification=0' style='float:right;'>", "</a>");
	$wppb_pluginNotification = new wppb_add_notices('wppb', 'profile_builder_pro', $message, 'wppb_profile_builder_pro_hobbyist_serial_status');
}