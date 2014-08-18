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
								'usernameRequired' => 'no',
								'firstname' => 'show',
								'firstnameRequired' => 'no',
								'lastname' => 'show',
								'lastnameRequired' => 'no',
								'nickname' => 'show',
								'nicknameRequired' => 'no',
								'dispname' => 'show',
								'dispnameRequired' => 'no',
								'email'	=> 'show',
								'emailRequired' => 'no',
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
								'passwordRequired' => 'no' 
							);
		add_option( 'wppb_default_settings', $wppb_default_settings );    //set all fields visible on first activation of the plugin
		$wppb_default_settings = array(
						'extraFieldsLayout' =>	'yes',
						'emailConfirmation' =>	'no',
						'loginWith'			=>	'username'
						);
		add_option( 'wppb_general_settings', $wppb_default_settings);
		$all_roles = $wp_roles->roles;
		$editable_roles = apply_filters('editable_roles', $all_roles);

		$admintSettingsPresent = get_option('wppb_display_admin_settings','not_found');

		if ($admintSettingsPresent == 'not_found'){                    			 // if the field doesn't exists, then create it
			$rolesArray = array();
			foreach ( $editable_roles as $key => $data )
				$rolesArray = array( $data['name'] => 'show' ) + $rolesArray;
			$rolesArray = array_reverse($rolesArray,true);
			update_option( 'wppb_display_admin_settings', $rolesArray);
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
		wp_enqueue_style( 'profile-builder-style', WPPB_PLUGIN_URL.'/assets/css/style.css', false, $this->version, 'screen');

		// enqueue scripts
		add_thickbox();	
		wp_enqueue_script( 'jquery-extra-profile-fields', WPPB_PLUGIN_URL.'/assets/js/jquery.extra.fields.js', array('jquery','media-upload','thickbox','jquery-ui-core','jquery-ui-tabs', 'jquery-ui-sortable'), $this->version );
		
		// remove GD star rating conflicts
		wp_deregister_style( 'gdsr-jquery-ui-core' );
		wp_deregister_style( 'gdsr-jquery-ui-theme' );
	}
  
		function profile_builder_options_page() {
			// Grab Options Page
			include( WPPB_PLUGIN_DIR.'/functions/options.php' );
		}
		 
}