<?php
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )			
	exit ();												// If uninstall not called from WordPress exit

/* get all the custom fields' names in one single array */
$customFields = get_option('wppb_custom_fields','not_found');

$customFieldNames = array();
foreach($customFields as $key => $value)
	array_push($customFieldNames, $value['item_metaName']);
/* END get all custom fields */


/* delete all the custom fields */
global $wpdb;
$allUserMeta = $wpdb->get_results("SELECT * FROM $wpdb->usermeta");

foreach ($allUserMeta as $userMeta) {
	if (in_array($userMeta->meta_key, $customFieldNames)){
		$metaFieldName = $userMeta->meta_key;
		$metaFieldValue = $userMeta->meta_value;
		delete_user_meta( $userMeta->user_id, $metaFieldName, $metaFieldValue );
		//$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key = '".$metaFieldName."' AND meta_value = '".$metaFieldValue."'");
	}

	$foundAvatar = strpos ( (string)$userMeta->meta_key , 'resized_avatar_' );
	if ( $foundAvatar !== FALSE ){
		$metaFieldName = $userMeta->meta_key;
		$metaFieldValue = $userMeta->meta_value;
		delete_user_meta( $userMeta->user_id, $metaFieldName, $metaFieldValue );
		//$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key = '".$metaFieldName."' AND meta_value = '".$metaFieldValue."'");
	}
}

delete_option( 'wppb_default_settings' );	   	 			// Delete default settings from options table
delete_option( 'wppb_default_style' );						// Delete "use default css or not" settings (old one; preserving backwards compatibility)
delete_option( 'wppb_general_settings' );					// Delete the plugin general settings
delete_option( 'wppb_display_admin_settings' ); 			// Delete display admin bar option
delete_option( 'wppb_custom_fields' ); 						// Delete the default fields
delete_option( 'wppb_profile_builder_pro_serial' ); 		// Delete the serial number associated with this instalation
delete_option( 'wppb_profile_builder_hobbyist_serial' );  
delete_option( 'serial_number_availability' ); 				// Delete the serial number status
delete_option( 'wppb_premium_addon_settings' ); 			// Delete addon settings (old one; preserving backwards compatibility)
delete_option( 'wppb_addon_settings' ); 					// Delete addon settings (since 1.1.21)
delete_option( 'wppb_premium_addon_settings_description' ); // Delete addon settings description (old one; preserving backwards compatibility)
delete_option( 'wppb_addon_settings_description' );			// Delete addon settings description (since 1.1.21)
delete_option( 'customRedirectSettings' ); 					// Delete the custom redirect settings
delete_option( 'userListingSettings' ); 					// Delete the user-listing settings (old one; preserving backwards compatibility)
delete_option( 'customUserListingSettings' ); 				// Delete the user-listing settings (since 1.1.21)
delete_option( 'reCaptchaSettings' ); 						// Delete the public and private keys used by the reCAPTCHA addon (since 1.1.42)
delete_option( 'emailCustomizer' ); 						// Delete the email-customizer settings