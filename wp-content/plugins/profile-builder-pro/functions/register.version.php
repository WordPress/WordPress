<?php
function wppb_register_profile_builder_pro(){
?>
	<form method="post" action="options.php#register-profile-builder">
	<?php $wppb_profile_builder_pro_serial = get_option('wppb_profile_builder_pro_serial'); ?>
	<?php $wppb_profile_builder_pro_serial_status = get_option('wppb_profile_builder_pro_serial_status'); ?>
	<?php settings_fields('wppb_profile_builder_pro_serial'); ?>
	
	<h2><?php _e('Register your version of Profile Builder Pro', 'profilebuilder');?></h2>
	<h3><?php _e('Register your version of Profile Builder Pro', 'profilebuilder');?></h3>
	<p><?php _e('Now that you acquired a copy of Profile Builder Pro, you should take the time and register it with the serial number you received in the e-mail.', 'profilebuilder');?><br/>
	<?php _e('If you register this version of Profile Builder, you\'ll receive information regarding eventual upgrades, patches, and - if needed - technical support.', 'profilebuilder');?></p>
	<p><strong><?php _e('Serial Number:', 'profilebuilder');?></strong>
	<input type="input" size="50" name="wppb_profile_builder_pro_serial" id="wppb_profile_builder_pro_serial" <?php if ( $wppb_profile_builder_pro_serial != ''){ echo ' value="'.$wppb_profile_builder_pro_serial.'"';} ?>/>
	<?php 
		if($wppb_profile_builder_pro_serial_status == 'found')
			echo '<span class="validateStatus"><img src="'.WPPB_PLUGIN_URL.'/assets/images/accept.png" title="'.__('The serial number was successfully validated!', 'profilebuilder').'"/></span>';
		elseif ($wppb_profile_builder_pro_serial_status == 'notFound')
			echo '<span class="validateStatus"><img src="'.WPPB_PLUGIN_URL.'/assets/images/icon_error.png" title="'.__('The serial number entered couldn\'t be validated!','profilebuilder').'"/></span>';
		elseif ($wppb_profile_builder_pro_serial_status == 'expired')
			echo '<span class="validateStatus"><img src="'.WPPB_PLUGIN_URL.'/assets/images/icon_error.png" title="'.__('The serial number couldn\'t be validated because it is no longer valid!','profilebuilder').'"/></span>';
		elseif ($wppb_profile_builder_pro_serial_status == 'serverDown')
			echo '<span class="validateStatus"><img src="'.WPPB_PLUGIN_URL.'/assets/images/icon_error.png" title="'.__('The serial number couldn\'t be validated because process timed out. This is possible due to the server being down. Please try again later!','profilebuilder').'"/></span>';
		
	?>
	
	
	</p>
	<p class="wppb-serialnumber-descr"><?php _e('(e.g. RMPB-15-SN-253a55baa4fbe7bf595b2aabb8d72985)', 'profilebuilder');?></p>
	
	<div align="right">
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<?php wp_nonce_field( 'wppb_register_version_nonce', 'wppb_register_version_nonce' ); ?>
		<input type="submit" name="wppb_serial_number_activate" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
		</p>
	</div>
	</form>
	
<?php
}

function wppb_register_profile_builder_hobbyist(){
?>
	<form method="post" action="options.php#register-profile-builder">
	<?php $wppb_profile_builder_hobbyist_serial = get_option('wppb_profile_builder_hobbyist_serial'); ?>
	<?php $wppb_profile_builder_hobbyist_serial_status = get_option('wppb_profile_builder_hobbyist_serial_status'); ?>
	<?php settings_fields('wppb_profile_builder_hobbyist_serial'); ?>
	
	<h2><?php _e('Register your version of Profile Builder Hobbyist', 'profilebuilder');?></h2>
	<h3><?php _e('Register your version of Profile Builder Hobbyist', 'profilebuilder');?></h3>
	<p><?php _e('Now that you acquired a copy of Profile Builder Hobbyist, you should take the time and register it with the serial number you received in the e-mail.', 'profilebuilder');?><br/>
	<?php _e('If you register this version of Profile Builder, you\'ll receive information regarding eventual upgrades, patches, and - if needed - technical support.', 'profilebuilder');?></p>
	<p><strong><?php _e('Serial Number:', 'profilebuilder');?></strong>
	<input type="input" size="50" name="wppb_profile_builder_hobbyist_serial" id="wppb_profile_builder_hobbyist_serial" <?php if ( $wppb_profile_builder_hobbyist_serial != ''){ echo ' value="'.$wppb_profile_builder_hobbyist_serial.'"';} ?>/>
	<?php 
		if($wppb_profile_builder_hobbyist_serial_status == 'found')
			echo '<span class="validateStatus"><img src="'.WPPB_PLUGIN_URL.'/assets/images/accept.png" title="'.__('The serial number was successfully validated!', 'profilebuilder').'"/></span>';
		elseif ($wppb_profile_builder_hobbyist_serial_status == 'notFound')
			echo '<span class="validateStatus"><img src="'.WPPB_PLUGIN_URL.'/assets/images/icon_error.png" title="'.__('The serial number entered couldn\'t be validated!','profilebuilder').'"/></span>';
		elseif ($wppb_profile_builder_hobbyist_serial_status == 'expired')
			echo '<span class="validateStatus"><img src="'.WPPB_PLUGIN_URL.'/assets/images/icon_error.png" title="'.__('The serial number couldn\'t be validated because it is no longer valid!','profilebuilder').'"/></span>';
		elseif ($wppb_profile_builder_hobbyist_serial_status == 'serverDown')
			echo '<span class="validateStatus"><img src="'.WPPB_PLUGIN_URL.'/assets/images/icon_error.png" title="'.__('The serial number couldn\'t be validated because process timed out. This is possible due to the server being down. Please try again later!','profilebuilder').'"/></span>';
		
	?>
	
	
	</p>
	<p class="wppb-serialnumber-descr"><?php _e('(e.g. RMPBH-15-SN-253a55baa4fbe7bf595b2aabb8d72985)', 'profilebuilder');?></p>
	
	<div align="right">
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<?php wp_nonce_field( 'wppb_register_version_nonce', 'wppb_register_version_nonce' ); ?>
		<input type="submit" name="wppb_serial_number_activate" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
		</p>
	</div>
	</form>
	
<?php
}

//the function to check the validity of the serial number and save a variable in the DB; purely visual
function wppb_check_serial_number($oldVal, $newVal){

	$serial_number_set = $newVal;
	
	$response = wp_remote_get( 'http://updatemetadata.cozmoslabs.com/checkserial/?serialNumberSent='.$serial_number_set );
	
	if (file_exists ( WPPB_PLUGIN_DIR . '/premium/addons/addon.php' )){
		if (is_wp_error($response)){
			update_option( 'wppb_profile_builder_pro_serial_status', 'serverDown' ); //server down
			
		}elseif((trim($response['body']) != 'notFound') && (trim($response['body']) != 'found') && (trim($response['body']) != 'expired')){
			update_option( 'wppb_profile_builder_pro_serial_status', 'serverDown' );  //unknown response parameter
			update_option( 'wppb_profile_builder_pro_serial', '' );  //reset the entered password, since the user will need to try again later
				
		}else{
			update_option( 'wppb_profile_builder_pro_serial_status', trim($response['body']) ); //either found, notFound or expired
		}
		
	}else{
		if (is_wp_error($response)){
			update_option( 'wppb_profile_builder_hobbyist_serial_status', 'serverDown' ); //server down
		
		}elseif((trim($response['body']) != 'notFound') && (trim($response['body']) != 'found') && (trim($response['body']) != 'expired')){
			update_option( 'wppb_profile_builder_hobbyist_serial_status', 'serverDown' );  //unknown response parameter
			update_option( 'wppb_profile_builder_hobbyist_serial', '' );  //reset the entered password, since the user will need to try again later
				
		}else{
			update_option( 'wppb_profile_builder_hobbyist_serial_status', trim($response['body']) ); //either found, notFound or expired
		}
	}
		
	delete_user_meta($user_id, 'wppb_dismiss_notification');
	
}
add_action( 'update_option_wppb_profile_builder_pro_serial', 'wppb_check_serial_number', 10, 2 );
add_action( 'update_option_wppb_profile_builder_hobbyist_serial', 'wppb_check_serial_number', 10, 2 );

//the update didn't work when the old value = new value, so we need to apply a filter on get_option (that is run before update_option), that resets the old value
function wppb_check_serial_number_fix($newvalue, $oldvalue){

	if ($newvalue == $oldvalue)
		wppb_check_serial_number($oldvalue, $newvalue);
		
	return $newvalue;
}
add_filter( 'pre_update_option_wppb_profile_builder_pro_serial', 'wppb_check_serial_number_fix', 10, 2);
add_filter( 'pre_update_option_wppb_profile_builder_hobbyist_serial', 'wppb_check_serial_number_fix', 10, 2);