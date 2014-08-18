<?php if (!defined('PROFILE_BUILDER_VERSION')) exit('No direct script access allowed');
 /*
Original Plugin Name: OptionTree
Original Plugin URI: http://wp.envato.com
Original Author: Derek Herman
Original Author URI: http://valendesigns.com
*/

/**
 * Profile Builder Admin
 *
 */
class PB_Admin{
	private $version = NULL;

	function __construct(){
		$this->version = PROFILE_BUILDER_VERSION;
	}
  
	/**
	* Initiate Plugin & setup main options
	*
	* @uses get_option()
	* @uses add_option()
	* @uses profile_builder_activate()
	* @uses wp_redirect()
	* @uses admin_url()
	*
	* @access public
	*
	*
	* @return bool
	*/
	function profile_builder_initialize(){
		// check for activation
		$check = get_option( 'profile_builder_activation' );

		// redirect on activation
		if ($check != "set") {   
			// add theme options
			add_option( 'profile_builder_activation', 'set');

			// load DB activation function if updating plugin
			$this->profile_builder_activate();

			// Redirect
			wp_redirect( admin_url().'users.php?page=ProfileBuilderOptionsAndSettings' );
		}
		return false;
	}
  
  
	/**
	* Plugin Activation
	*
	*
	*
	* @return void
	*/
  function profile_builder_activate(){
	global $wp_roles;
    
    // check for installed version
  	$installed_ver = get_option( 'profile_builder_version' );
    
    // New Version Update
    if ( $installed_ver != $this->version ){
      update_option( 'profile_builder_version', $this->version );
    } 
    else if ( !$installed_ver ) {
      add_option( 'profile_builder_version', $this->version );
    }
	
	
	$wppb_default_settings = array(
								'username' => 'show',
								'usernameRequired' => 'yes',
								'firstname' => 'show',
								'firstnameRequired' => 'no',
								'lastname' => 'show',
								'lastnameRequired' => 'no',
								'nickname' => 'show',
								'nicknameRequired' => 'yes',
								'dispname' => 'show',
								'dispnameRequired' => 'no',
								'email'	=> 'show',
								'emailRequired' => 'yes',
								'website' => 'show',
								'websiteRequired' => 'no',
								'aim' => 'show',
								'aimRequired' => 'no',
								'yahoo' => 'show',
								'yahooRequired' => 'no',
								'jabber' => 'show',
								'jabberRequired' => 'no',
								'bio' => 'show',
								'bioRequired' => 'no',
								'password' => 'show',
								'passwordRequired' => 'yes' 
							);
		add_option( 'wppb_default_settings', $wppb_default_settings );    //set all fields visible on first activation of the plugin
		$wppb_default_settings = array(
									'extraFieldsLayout' => 	'yes',
									'emailConfirmation' => 	'no',
									'adminApproval' 	=> 	'no',
									'loginWith'			=>	'username'
								);
		add_option( 'wppb_general_settings', $wppb_default_settings);
		add_option( 'wppb_profile_builder_pro_serial', '');
		$all_roles = $wp_roles->roles;
		$editable_roles = apply_filters('editable_roles', $all_roles);

		$admintSettingsPresent = get_option('wppb_display_admin_settings','not_found');

		if ($admintSettingsPresent == 'not_found'){                    			 // if the field doesn't exists, then create it
			$rolesArray = array();
			foreach ( $editable_roles as $key => $data )
				$rolesArray = array( $key => 'show' ) + $rolesArray;
			$rolesArray = array_reverse($rolesArray,true);
			update_option( 'wppb_display_admin_settings', $rolesArray);
		}
		
		$arraySettingsPresent = get_option('wppb_custom_fields','not_found');
		if ($arraySettingsPresent == 'not_found'){
			$wppbMultyArray = array(); 
			add_option( 'wppb_custom_fields', $wppbMultyArray);
		}else{
			//this is for creating an item_metaName if it doesn't exist; for the new version 1.1.13
			foreach ($arraySettingsPresent as $key => $value){
				if ($value['item_metaName'] == null){
					$arraySettingsPresent[$key]['item_metaName'] = 'custom_field_'.$value['id'];
				}
				if ($value['item_LastMetaName'] == null){
					$arraySettingsPresent[$key]['item_LastMetaName'] = 'custom_field_'.$value['id'];
				}
			}
			update_option( 'wppb_custom_fields', $arraySettingsPresent);
		}
		

		add_option('wppb_profile_builder_pro_serial_status', 'notFound');
		
		$wppb_addons_file = WPPB_PLUGIN_DIR . '/premium/addons/addon.php';
		if (file_exists($wppb_addons_file)){
			$wppb_addon_settings_description = array( 
												'wppb_userListing' => 'User-Listing', 
												'wppb_customRedirect' => 'Custom Redirects',
												'wppb_reCaptcha' => 'reCAPTCHA',
												'wppb_emailCustomizer' => 'User Email Customizer',
												'wppb_emailCustomizerAdmin' => 'Admin Email Customizer');
			update_option('wppb_addon_settings_description', $wppb_addon_settings_description);
												  
			$wppb_addon_settings = array( 
										'wppb_userListing' => 'hide', 
										'wppb_customRedirect'=> 'hide',
										'wppb_reCaptcha'=> 'hide',
										'wppb_emailCustomizer' => 'hide',
										'wppb_emailCustomizerAdmin' => 'hide');
			add_option('wppb_addon_settings', $wppb_addon_settings);
		}
		
		
		/* now that we created the meta fields, let's create the folder structure in the wp upload dir for the attachments and the avatars */
		$upload_dir = wp_upload_dir(); 
		$upload_dir['basedir']; 
		
		$structure0 = $upload_dir['basedir'].'/profile_builder';
		$structure1 = $upload_dir['basedir'].'/profile_builder/attachments/';
		$structure2 = $upload_dir['basedir'].'/profile_builder/avatars/';
		
		wp_mkdir_p( $structure0 );
		wp_mkdir_p( $structure1 );
		wp_mkdir_p( $structure2 );
		
		// Add the rewrite rule and flush on plugin activation
		if (file_exists ( WPPB_PLUGIN_DIR . '/premium/addons/addon.php' )){
			wppb_rrr_add_rules();
			flush_rewrite_rules();
		}

	}
  
	/**
	* Plugin Deactivation delete options
	*
	* @uses delete_option()
	*
	* @access public
	*
	*
	* @return void
	*/
	function profile_builder_deactivate() {
		// remove activation check & version
		delete_option( 'profile_builder_activation' );
		delete_option( 'profile_builder_version' );
		
		// Flush rewrite rules when deactivated
		flush_rewrite_rules();
	}
    
	//add WPML support
	function profile_builder_wpml_register_string($context, $name, $value){  
		if(function_exists('icl_register_string'))
			icl_register_string($context, $name, $value);
	}
	  
	function profile_builder_wpml_unregister_string($context, $name){  
		if(function_exists('icl_unregister_string'))
			icl_unregister_string($context, $name, $value);
	}
	
	/**
	* Add Admin Menu Items & Test Actions
	*
	*
	* @return void
	*/
	function profile_builder_admin(){  
		// create menu item
		$profile_builder_options = add_submenu_page( 'users.php', 'Profile Builder', 'Profile Builder', 'delete_users', 'ProfileBuilderOptionsAndSettings', array( $this, 'profile_builder_options_page' ) );

		// add menu item
		add_action( "admin_print_styles-$profile_builder_options", array( $this, 'profile_builder_load' ) );
	}
  
	/**
	* Load Scripts & Styles
	*
	* @uses wp_enqueue_style()
	*
	*
	* @return void
	*/
	function profile_builder_load(){
		// enqueue styles
		if (file_exists ( WPPB_PLUGIN_DIR . '/premium/addons/addon.php' ))
			wp_enqueue_style( 'profile-builder-style', WPPB_PLUGIN_URL.'/assets/css/premium.style.css', false, $this->version, 'screen');
		else
			wp_enqueue_style( 'profile-builder-style', WPPB_PLUGIN_URL.'/assets/css/hobbyist.style.css', false, $this->version, 'screen');

		// enqueue scripts
		add_thickbox();
		$wppb_premiumJS = WPPB_PLUGIN_DIR . '/premium/assets/js/';

			if (file_exists ( $wppb_premiumJS.'jquery.table.drag.n.drop.js' )){
				wp_enqueue_script( 'jquery-table-drag-and-drop', WPPB_PLUGIN_URL.'/premium/assets/js/jquery.table.drag.n.drop.js', array('jquery'), $this->version );
				wp_enqueue_script( 'jquery-extra-profile-fields', WPPB_PLUGIN_URL.'/premium/assets/js/jquery.premium.extra.fields.js', array('jquery','media-upload','thickbox','jquery-ui-core','jquery-ui-tabs','jquery-table-drag-and-drop', 'jquery-ui-sortable'), $this->version );
			}
			else{		
				wp_enqueue_script( 'jquery-extra-profile-fields', WPPB_PLUGIN_URL.'/assets/js/jquery.extra.fields.js', array('jquery','media-upload','thickbox','jquery-ui-core','jquery-ui-tabs', 'jquery-ui-sortable'), $this->version );
			}
			
		// remove GD star rating conflicts
		wp_deregister_style( 'gdsr-jquery-ui-core' );
		wp_deregister_style( 'gdsr-jquery-ui-theme' );
	}
  
		
		function profile_builder_options_page() {
			// Grab Options Page
			include( WPPB_PLUGIN_DIR.'/functions/options.php' );
		}
		
  /**
   * Insert new custom input via AJAX
   *
   * @uses check_ajax_referer()
   *
   *
   * @return void
   */
  function profile_builder_add() {
	global $wpdb;
    
    // check AJAX referer
    check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
    
    // grab fresh options array
	$wppbFetchArray = get_option('wppb_custom_fields');
    
    // get form data
    $id            = $_POST['id'];
  	$item_metaName = htmlspecialchars(stripslashes(trim($_POST['item_metaName'])), ENT_QUOTES,'UTF-8',true);
  	$item_title    = htmlspecialchars(stripslashes(trim($_POST['item_title'])), ENT_QUOTES,'UTF-8',true);
  	$item_desc     = htmlspecialchars(stripslashes(trim($_POST['item_desc'])), ENT_QUOTES,'UTF-8',true);
  	$item_type     = htmlspecialchars(stripslashes(trim($_POST['item_type'])), ENT_QUOTES,'UTF-8',true);
	if ($item_type != 'agreeToTerms')
		$item_options = trim($_POST['item_options']);
	else
		$item_options  = htmlspecialchars(stripslashes(trim($_POST['item_options'])), ENT_QUOTES,'UTF-8',true);
	$item_LastMetaName = $item_metaName;
	
	if ($_POST['item_required'] == NULL)
		$item_required = 'no';
	else 
		$item_required = $_POST['item_required'];
  	

  	// verify title
    if (strlen($item_title) < 1 ) 
      die(__('You must give your option a title.', 'profilebuilder'));
	
	// check if meta-key doesn't have space
	if(preg_match("/^[a-zA-Z0-9_-]+$/", $item_metaName) === 0)
		die(__('You have entered an invalid meta-key format!', 'profilebuilder'));	
	
	// verify key is alphanumeric
    if ( eregi( '[^a-z0-9_]', $item_metaName ) ) 
      die(__('You must enter a valid meta-key.', 'profilebuilder'));
	
	// validate item key
  	foreach($wppbFetchArray as $value){
      if ( $value['id'] != $id ){
        if ($item_metaName == $value['item_metaName']){
			die(__('That meta-key is already in use.', 'profilebuilder'));
        }
      } 
  	}
	
	//if the filter is set to true (default), then also check if this meta_key name exists in the usermeta table
	$checkDuplicateMetaFields = apply_filters('wppb_check_duplicate_meta_fields_on_add', true);
	if ($checkDuplicateMetaFields){
		$testExisting = $wpdb->get_row("SELECT * FROM $wpdb->usermeta WHERE meta_key = '".$item_metaName."'");
		if ($testExisting != null)
			die(__('That meta-key is already in use.', 'profilebuilder'));
	}
	  
	//check if the item we are about to add is of avatar type
	if ($item_type == 'avatar'){ 
		foreach( $wppbFetchArray as $value ){
			if($value['item_type'] == 'avatar')
				die(__('There is already an avatar input-type. You can only have one avatar present.', 'profilebuilder'));
		}
	}	
	//check if the item we are about to add is of agreeToTerms type
	if ($item_type == 'agreeToTerms'){ 
		foreach( $wppbFetchArray as $value ){
			if($value['item_type'] == 'agreeToTerms')
				die(__('There is already an "Agree to Terms and Conditions" checkbox. You can only have one present.', 'profilebuilder'));
		}
	}
    
    if ( $item_type == 'textarea' && !is_numeric( $item_options ) )
		die(__('The textarea row value must be numeric.', 'profilebuilder'));
	
	if ( $item_type == 'input' && (trim($item_options) != '') ){
		if ( !is_numeric( $item_options ) )
			die(__('The maxlength attribute must be numeric.', 'profilebuilder'));
	}
	  
	if ( $item_type == 'avatar' ){
		//use this to verify if the entered value has only 1 component
		if (is_numeric($item_options)){
			if (($item_options < 20) || ($item_options > 200))
				die(__('The value must be between 20 and 200!', 'profilebuilder'));
		//this checks if the entered value has 2 components
		}elseif (strpos( (string)$item_options, ',' ) != false){
			$sentValue = explode(',',$item_options);
			if (!is_numeric($sentValue[0]))
				die(__('The width component of the entered value must be numeric!', 'profilebuilder'));
			elseif (!is_numeric($sentValue[1]))
				die(__('The height component of the entered value must be numeric!', 'profilebuilder'));				
			elseif (($sentValue[0] < 20) || ($sentValue[0] > 200))
				die(__('The width component of the entered value must be between 20 and 200!', 'profilebuilder'));
			elseif (($sentValue[1] < 20) || ($sentValue[1] > 200))
				die(__('The height component of the entered value must be between 20 and 200!', 'profilebuilder'));
			elseif ((!is_numeric($sentValue[0])) && (!is_numeric($sentValue[1])))
				die(__('The pair of values entered didn\'t have the right format (width,height)!', 'profilebuilder'));
		//fallback
		}else
			die(__('The entered avatar size must be numeric!', 'profilebuilder'));
		
	}
    
	/* update the multy-array in the options table */
	$max_id = 0;
	$max_sort = 0;
	
	foreach( $wppbFetchArray as $value ){
		if ( $value['item_sort'] > $max_sort)
			$max_sort = $value['item_sort'];
		if ( $value['id'] > $max_id)
			$max_id = $value['id'];
	}
	
	$max_sort++;
	$max_id++;
	$tempArray = array('id' => $max_id, 'item_metaName' => $item_metaName, 'item_title' => $item_title, 'item_desc' => $item_desc, 'item_type' => $item_type, 'item_options' => $item_options, 'item_sort' => $max_sort, 'item_required' => $item_required, 'item_LastMetaName' => $item_LastMetaName);
	array_push($wppbFetchArray, $tempArray);
	update_option( 'wppb_custom_fields', $wppbFetchArray );
	
	/* verify if the multiarray contains our new array */
	$wppbFetchArray = get_option('wppb_custom_fields');
	foreach( $wppbFetchArray as $value ){
		if ( $item_metaName == $value['item_metaName'] && $item_title == $value['item_title'] && $item_desc == $value['item_desc'] && $item_type == $value['item_type'] && $item_options == $value['item_options'] && $item_required == $value['item_required']){
			
			// add WPML support
			if (trim($item_title) != '')
				$this->profile_builder_wpml_register_string('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
			if (trim($item_desc) != '')
				$this->profile_builder_wpml_register_string('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);
			if ( (trim($item_options) != '') && ( ($item_type == 'radio') || ($item_type == 'select') || ($item_type == 'checkbox') ) )
				$this->profile_builder_wpml_register_string('plugin profile-builder-pro', 'custom_field_'.$id.'_options_translation', $item_options);
			
			// finalize add
			$updated = TRUE;
		}
  	}
	 
    /* if the update was successfull */
    if ( $updated == TRUE){
		die('updated');
    } 
    else{
		die(__('There was an error, please try again.', 'profilebuilder'));
    }
  }
  
  /**
   * Update Option Setting Table via AJAX
   *
   * @uses check_ajax_referer()
   *
   *
   * @return void
   */
  function profile_builder_edit() {
	global $wpdb;
    
    // Check AJAX Referer
    check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
    
    // grab fresh options array
	$wppbFetchArray = get_option('wppb_custom_fields');
    
    // get form data
  	$id 		   = $_POST['id'];
  	$item_metaName = htmlspecialchars(stripslashes(trim($_POST['item_metaName'])), ENT_QUOTES,'UTF-8',true);
  	$item_title    = htmlspecialchars(stripslashes(trim($_POST['item_title'])), ENT_QUOTES,'UTF-8',true);
  	$item_desc     = htmlspecialchars(stripslashes(trim($_POST['item_desc'])), ENT_QUOTES,'UTF-8',true);
  	$item_type     = htmlspecialchars(stripslashes(trim($_POST['item_type'])), ENT_QUOTES,'UTF-8',true);
	if ($item_type != 'agreeToTerms')
		$item_options = trim($_POST['item_options']);
	else
		$item_options  = htmlspecialchars(stripslashes(trim($_POST['item_options'])), ENT_QUOTES,'UTF-8',true);
		
	if ($_POST['item_required'] == NULL)
		$item_required = 'no';
	else 
		$item_required = $_POST['item_required'];

  	// verify title
  	if ( strlen( $item_title ) < 1 ) 
      die(__('You must give your option a title.', 'profilebuilder'));
	  
	// check if meta-key doesn't have space
	if(preg_match("/^[a-zA-Z0-9_-]+$/", $item_metaName) === 0)
		die(__('You have entered an invalid meta-key format!', 'profilebuilder'));	 
	 
	// verify key is alphanumeric
    if ( eregi( '[^a-z0-9_]', $item_metaName ) ) 
      die(__('You must enter a valid meta-key.', 'profilebuilder'));	
	 
	$customFieldNames = array();
	$safe = false;
	
	// validate item key
  	foreach($wppbFetchArray as $value){
		array_push($customFieldNames, $value['item_metaName']); // we will need this later on
		if ( $value['id'] != $id ){
			if ($item_metaName == $value['item_metaName']){
				die(__('That meta-key is already in use.', 'profilebuilder'));
			}
		}elseif (($value['id'] == $id) && ($item_metaName == $value['item_metaName']))
			$safe = true;
  	}
	
	//if the filter is set to true (default), then also check if this meta_key name exists in the usermeta table
	$checkDuplicateMetaFields = apply_filters('wppb_check_duplicate_meta_fields_on_edit', true);
	if ($checkDuplicateMetaFields){
		if (!$safe){
			$testExisting = $wpdb->get_row("SELECT * FROM $wpdb->usermeta WHERE meta_key = '".$item_metaName."'");
			if ($testExisting != null)
				die(__('That meta-key is already in use.', 'profilebuilder'));
		}
	}
	
	//check if the item we are about to add is of avatar type
	if ($item_type == 'avatar'){ 
		foreach( $wppbFetchArray as $value ){
			if(($value['item_type'] == 'avatar') && ($id != $value['id']))
				die(__('There is already an avatar input-type. You can only have one avatar present.', 'profilebuilder'));
		}
	}
	//check if the item we are about to add is of agreeToTerms type
	if ($item_type == 'agreeToTerms'){ 
		foreach( $wppbFetchArray as $value ){
			if(($value['item_type'] == 'agreeToTerms') && ($id != $value['id']))
				die(__('There is already an "Agree to Terms and Conditions" checkbox. You can only have one present.', 'profilebuilder'));
		}
	}
    
    if ( $item_type == 'textarea' && !is_numeric( $item_options ) )
      die(__('The textarea row value must be numeric.', 'profilebuilder'));
	
	if ( $item_type == 'input' && (trim($item_options) != '') ){
		if ( !is_numeric( $item_options ) )
			die(__('The maxlength attribute must be numeric.', 'profilebuilder'));
	}
	
	if ( $item_type == 'avatar' ){
		//use this to verify if the entered value has only 1 component
		if (is_numeric($item_options)){
			if (($item_options < 20) || ($item_options > 200))
				die(__('The value must be between 20 and 200!', 'profilebuilder'));
		//this checks if the entered value has 2 components
		}elseif (strpos( (string)$item_options, ',' ) != false){
			$sentValue = explode(',',$item_options);
			if (!is_numeric($sentValue[0]))
				die(__('The width component of the entered value must be numeric!', 'profilebuilder'));
			elseif (!is_numeric($sentValue[1]))
				die(__('The height component of the entered value must be numeric!', 'profilebuilder'));				
			elseif (($sentValue[0] < 20) || ($sentValue[0] > 200))
				die(__('The width component of the entered value must be between 20 and 200!', 'profilebuilder'));
			elseif (($sentValue[1] < 20) || ($sentValue[1] > 200))
				die(__('The height component of the entered value must be between 20 and 200!', 'profilebuilder'));
			elseif ((!is_numeric($sentValue[0])) && (!is_numeric($sentValue[1])))
				die(__('The pair of values entered didn\'t have the right format (width,height)!', 'profilebuilder'));
		//fallback
		}else
			die(__('The entered avatar size must be numeric!', 'profilebuilder'));
		
	}
    
    // update the array
	foreach( $wppbFetchArray as $key => $value ){
		if ( $id == $value['id'] ){
			//echo 'found';
			$wppbFetchArray[$key]['item_metaName'] = $item_metaName;
			$wppbFetchArray[$key]['item_title'] = $item_title;
			$wppbFetchArray[$key]['item_desc'] = $item_desc;
			$wppbFetchArray[$key]['item_type'] = $item_type;
			$wppbFetchArray[$key]['item_options'] = $item_options;
			$wppbFetchArray[$key]['item_required'] = $item_required;
			$key = $key;
		}
  	}
	
	/* update */
	update_option( 'wppb_custom_fields', $wppbFetchArray );
	
	/* verify if the multiarray contains our new array */
	$wppbFetchArray = get_option('wppb_custom_fields');
	
	foreach( $wppbFetchArray as $value ){
		if ( $item_metaName == $value['item_metaName'] && $item_title == $value['item_title'] && $item_desc == $value['item_desc'] && $item_type == $value['item_type'] && $item_options == $value['item_options'] && $item_required == $value['item_required']){
			$item_LastMetaName = $value['item_LastMetaName'];
			
			// add WPML support
			if (trim($item_title) != '')
				$this->profile_builder_wpml_register_string('plugin profile-builder-pro', 'custom_field_'.$id.'_title_translation', $item_title);
			if (trim($item_desc) != '')
				$this->profile_builder_wpml_register_string('plugin profile-builder-pro', 'custom_field_'.$id.'_description_translation', $item_desc);
			if ( (trim($item_options) != '') && ( ($item_type == 'radio') || ($item_type == 'select') || ($item_type == 'checkbox') ) )
				$this->profile_builder_wpml_register_string('plugin profile-builder-pro', 'custom_field_'.$id.'_options_translation', $item_options);
			
			// finalize add
			$updated = TRUE;
		}
  	}
	 
    /* if the update was successfull */
    if ( $updated == TRUE){
		//check if it has a different name compared to the last one
		if ($item_metaName != $item_LastMetaName){
		
			//query the usermeta table
			global $wpdb;
			$allUserMeta = $wpdb->get_results("SELECT * FROM $wpdb->usermeta WHERE meta_key='".$item_LastMetaName."'");

			//get old meta field and value for all the users and update them
			foreach ($allUserMeta as $userMeta) {
				$thisUserValue = get_user_meta($userMeta->user_id, $item_LastMetaName, true);
				$done = add_user_meta( $userMeta->user_id, $item_metaName, $thisUserValue);
				if ($done == true){
					delete_user_meta( $userMeta->user_id, $item_LastMetaName);
					
					//update the new meta_key name
					$wppbFetchArray[$key]['item_LastMetaName'] = $item_metaName;
				}
			}
			
			//update the extra fields array
			update_option( 'wppb_custom_fields', $wppbFetchArray );
		}
		
		//send success message
		die('updated');
    } 
    else{
      die(__('There was an error, please try again.', 'profilebuilder'));
    }
  }

  /**
   * Remove Option via AJAX
   *
   * @uses check_ajax_referer()
   *
   *
   * @return void
   */
  function profile_builder_delete(){
    global $wpdb;
    
    // check AJAX referer
    check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
  
    // grab ID
  	$id = $_REQUEST['id'];
    
	/* Fetch the existing array */
	$wppbFetchArray = get_option('wppb_custom_fields');
	
	/* Get user info. */
	global $current_user;
	get_currentuserinfo();
	
    /* delete the array */
	foreach( $wppbFetchArray as $key => $value ){
      if ( $id == $value['id'] ){
		// add WPML support
		if (trim($value['item_title']) != '')
			$this->profile_builder_wpml_unregister_string('plugin profile-builder-pro', 'custom_field_'.$value['id'].'_title_translation');
		if (trim($value['item_desc']) != '')
			$this->profile_builder_wpml_unregister_string('plugin profile-builder-pro', 'custom_field_'.$value['id'].'_description_translation');
		if ( (trim($value['item_options']) != '') && ( ($value['item_type'] == 'radio') || ($value['item_type'] == 'select') || ($value['item_type'] == 'checkbox') ) )
			$this->profile_builder_wpml_unregister_string('plugin profile-builder-pro', 'custom_field_'.$value['id'].'_options_translation');
	  
	  
        unset( $wppbFetchArray[$key] );
		$wp_user_search = $wpdb->get_results("SELECT ID, display_name FROM $wpdb->users ORDER BY ID");
		foreach ($wp_user_search as $users){
			delete_user_meta($users->ID, $value['item_metaName']);
			delete_user_meta($users->ID, 'resized_avatar_'.$value['id']);
		}
      }
  	}
	
	update_option( 'wppb_custom_fields', $wppbFetchArray );		
	
  	die('removed');
  }
  
  /**
   * Get Option ID via AJAX
   *
   * @uses check_ajax_referer()
   *
   *
   * @return void
   */
  function profile_builder_next_id(){    
    // check AJAX referer
    check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
    
	// grab fresh options array
	$wppbFetchArray = get_option('wppb_custom_fields');
	
    // get ID
	$id = '0';

	foreach( $wppbFetchArray as $value ){
		if ( (int)$value['id'] >= (int)$id)
			$id = $value['id'];
	}
	
    // return ID and exit	
  	die((string)$id);
  }
  
  /**
   * Update Sort Order via AJAX
   *
   * @uses check_ajax_referer()
   *
   *
   * @return void
   */
  function profile_builder_sort() {
    
    // check AJAX referer
    check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
  
	/* Fetch the existing array */
	$wppbFetchArray = get_option('wppb_custom_fields');
	
    // create an array of IDs
  	$fields = explode('&', $_REQUEST['id']);
	
  	// set order, and a counter
  	$order = 0;
	$wppbCounter = 0;
	$newArray = array();
    
    // update the sort order
  	foreach( $fields as $field ) {
  		$key = explode('=', $field);
  		$id = urldecode($key[1]);
		
		foreach( $wppbFetchArray as $key => $value){
			if($value['id'] == $id){
				array_push($newArray, $wppbFetchArray[$key]);
			}
		}
  	}
	
	/* Update the new array */
	update_option( 'wppb_custom_fields', $newArray );
	
  	die();
	
   }
  
}