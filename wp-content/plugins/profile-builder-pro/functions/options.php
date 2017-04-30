<!--
Original Plugin Name: OptionTree
Original Plugin URI: http://wp.envato.com
Original Author: Derek Herman
Original Author URI: http://valendesigns.com
-->
<?php 
if (!defined('PROFILE_BUILDER_VERSION'))
	exit('No direct script access allowed');

/* this is for the backwards compatibility from v.1.1.12 to v.1.1.13 */
$update = false;
$arraySettingsPresent = get_option('wppb_custom_fields','not_found');
	if ($arraySettingsPresent != 'not_found'){
		foreach ($arraySettingsPresent as $key => $value){
			if ($value['item_metaName'] == null){
				$arraySettingsPresent[$key]['item_metaName'] = 'custom_field_'.$value['id'];
				$update = true;
			}
			if ($value['item_LastMetaName'] == null){
				$arraySettingsPresent[$key]['item_LsastMetaName'] = 'custom_field_'.$value['id'];
				$update = true;
			}
		}
		// only update if it is needed
		if ($update == true)
			update_option( 'wppb_custom_fields', $arraySettingsPresent);
	}
/* END backwards compatibility */
?>


<div id="framework_wrap" class="wrap">
	
	<div id="header">
    <h1>Profile Builder</h1>
    <span class="icon">&nbsp;</span>
    <div class="version">
      <?php echo 'Version ' . PROFILE_BUILDER_VERSION; ?>
    </div>
	</div>
  
  <div id="content_wrap">
      
	<?php 
	global $current_user ;

	$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
	if (!file_exists ( $wppb_premium.'custom.fields.php' )){
	?>
		<div class="info basic-version-info">
			<img src="<?php echo WPPB_PLUGIN_URL ?>/assets/images/ad_image.png" alt="Profile Builder Pro" />
			<a href="http://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=wpbackend&utm_medium=clientsite&utm_content=link&utm_campaign=ProfileBuilderFree" alt="Profile Builder Pro" title="Buy Profile Builder Pro"><img id="wppb_buyNowButton" src="<?php echo WPPB_PLUGIN_URL ?>/assets/images/buy_now_button.png"/></a>
		</div>
	<?php
	}elseif ( ! get_user_meta($current_user->ID, 'wppb_dismiss_notification') ) {
		
		if (file_exists ( WPPB_PLUGIN_DIR . '/premium/addons/addon.php' ))
			$wppb_profile_builder_pro_hobbyist_serial_status = get_option('wppb_profile_builder_pro_serial_status');
		else
			$wppb_profile_builder_pro_hobbyist_serial_status = get_option('wppb_profile_builder_hobbyist_serial_status');
			
		if ($wppb_profile_builder_pro_hobbyist_serial_status == 'notFound')
			echo '<div class="info pro-noserial-info" style="line-height:22px;">' . sprintf(__('Your <strong>Profile Builder</strong> serial number is invalid or missing. Please %1$sregister your copy%2$s of <b>Profile Builder</b> to receive access to automatic updates and support. Need a license key? %3$sPurchase one now%4$s', 'profilebuilder'), "<a href='admin.php?page=ProfileBuilderOptionsAndSettings#register-profile-builder'>", "</a>", "<a href='http://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=PB&utm_medium=plugin&utm_campaign=PB-Purchase' target='_blank' class='button-primary'>", "</a>") . '</div>';
		elseif ($wppb_profile_builder_pro_hobbyist_serial_status == 'expired')
			echo '<div class="info pro-noserial-info" style="line-height:22px;">' . sprintf(__('Your <strong>Profile Builder</strong> 1 year licence has expired. Please %1$sRenew Your Licence%2$s to receive access to automatic updates and priority support. %3$sPurchase one now%4$s', 'profilebuilder'), "<a href='http://www.cozmoslabs.com/downloads/profile-builder-pro-1-year/?utm_source=PB&utm_medium=plugin&utm_campaign=PB-Renewal'>", "</a>", "<a href='http://www.cozmoslabs.com/downloads/profile-builder-pro-1-year/?utm_source=PB&utm_medium=plugin&utm_campaign=PB-Renewal' target='_blank' class='button-primary'>", "</a>") . '</div>';
	}
	?>
      <div class="info top-info"></div>
      
	  <?php $wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
		if (file_exists ( $wppb_premium.'custom.fields.php' )){
			echo '<div class="ajax-message'; 
			if ( isset( $message ) ) { echo ' show'; } 
			echo '">';
			if ( isset( $message ) ) { echo $message; } 
			echo '</div>';
		}
		?>
      
      <div id="content">
      
        <div id="options_tabs">
        
          <ul class="options_tabs">
			<li><a href="#profile-builder"><?php _e('Basic Information','profilebuilder');?></a><span></span></li>
			<li><a href="#general-settings"><?php _e('General Settings','profilebuilder');?></a><span></span></li>
			<li><a href="#show-hide-admin-bar"><?php _e('Show/Hide the Admin Bar on Front-end','profilebuilder');?></a><span></span></li>
			<li><a href="#default-fields"><?php _e('Default Profile Fields','profilebuilder');?></a><span></span></li>
			<?php 
				$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
				$wppb_addons = WPPB_PLUGIN_DIR . '/premium/addons/';
				
				if (file_exists ( $wppb_premium.'custom.fields.php' )){
					echo '<li><a href="#create-extra-fields">'; _e('Extra Profile Fields','profilebuilder'); echo'</a><span></span></li>'; 
				}
				if (file_exists ( $wppb_addons.'addon.php' )){
					echo '<li><a href="#add-ons">'; _e('Addons','profilebuilder'); echo'</a><span></span></li>'; 
				}
				if (file_exists ( $wppb_premium.'custom.fields.php' )){
					echo '<li><a href="#register-profile-builder">'; _e('Register Your Version','profilebuilder'); echo'</a><span></span></li>'; 
				}
			?>
			<?php 
			$addons_options_set = get_option('wppb_addon_settings','not_found');
			if ($addons_options_set != 'not_found'){ 
				$addons_options_description = get_option('wppb_addon_settings_description'); //fetch the descriptions array
				foreach ($addons_options_set as $key => $value)
					if ($value == 'show'){
						echo '<li><a href="#'.$key.'">'; _e($addons_options_description[$key],'profilebuilder'); echo '</a><span></span></li>';
					}
			}
			?>
			
          </ul>
			<div id="profile-builder" class="block">
			<?php wppb_basic_info(); ?>
			</div>

			<div id="general-settings" class="block">
			<?php wppb_general_settings(); ?>
			</div>
            
			
			<div id="show-hide-admin-bar" class="block has-table">
			<?php wppb_display_admin_settings(); ?>
			</div>
			
			<div id="default-fields" class="block has-table">
			<?php wppb_default_settings(); ?>
			</div>
			
			<?php 
				$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
				if (file_exists ( $wppb_premium.'custom.fields.php' )){
					require_once($wppb_premium.'custom.fields.php');
					echo '<div id="create-extra-fields" class="block has-table">';
					wppb_custom_settings();
					echo '</div>';
					echo '<div id="register-profile-builder" class="block">';
				//	if (file_exists ( WPPB_PLUGIN_DIR . '/premium/addons/addon.php' ))
						wppb_register_profile_builder_pro();
					//else
					//	wppb_register_profile_builder_hobbyist();
					echo '</div>';
				}

				$wppb_addons = WPPB_PLUGIN_DIR . '/premium/addons/';
				if (file_exists ( $wppb_addons.'addon.php' )){
					require_once($wppb_addons.'addon.php');
					echo '<div id="add-ons" class="block has-table">';
					wppb_displayAddons();
					echo '</div>';
					
					$addons_options_set = get_option('wppb_addon_settings','not_found');
					if ($addons_options_set != 'not_found'){ 
						foreach ($addons_options_set as $key => $value)
							if ($value == 'show'){
								echo '<div id="'.$key.'" class="block has-table">';
								$key();
								echo '</div>';
							}
					}
				}
			?>
			
			<br class="clear" />
   
        </div>
        
      </div>
     
      <div class="info bottom"></div> 

  </div>

</div>
<!-- [END] framework_wrap -->