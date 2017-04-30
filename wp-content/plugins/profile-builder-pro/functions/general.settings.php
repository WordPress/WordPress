<?php
function wppb_general_settings(){

	$wppb_premiumStyle = WPPB_PLUGIN_DIR . '/premium/functions/';	
?>		
	<form method="post" action="options.php#general-settings">
	<?php $wppb_generalSettings = get_option('wppb_general_settings'); ?>
	<?php settings_fields('wppb_general_settings'); ?>

	<h2><?php _e('General Settings', 'profilebuilder');?></h2>
	<h3><?php _e('General Settings', 'profilebuilder');?></h3>
	<font id="generalSettingFont"><?php _e('Stylesheet Used on the Front-End:', 'profilebuilder');?></font>
	<select name="wppb_general_settings[extraFieldsLayout]" class="wppb_general_settings">
		<option value="yes" <?php if ($wppb_generalSettings['extraFieldsLayout'] == 'yes') echo 'selected';?>><?php _e('Default', 'profilebuilder');?></option>
		<?php 
			if (file_exists ( $wppb_premiumStyle.'custom.fields.php' )){
		?>
				<option value="white" <?php if ($wppb_generalSettings['extraFieldsLayout'] == 'white') echo 'selected';?>><?php _e('White', 'profilebuilder');?></option>
				<option value="black" <?php if ($wppb_generalSettings['extraFieldsLayout'] == 'black') echo 'selected';?>><?php _e('Black', 'profilebuilder');?></option>
		<?php
			}
		?>
		<option value="no" <?php if ($wppb_generalSettings['extraFieldsLayout'] == 'no') echo 'selected';?>><?php _e('None', 'profilebuilder');?></option>
	</select>
	<?php
	if (!is_multisite()){
	?>
		<br/><br/>
		<font id="generalSettingFont"><?php _e('"Email Confirmation" Feature Activated:', 'profilebuilder');?></font>
		<select name="wppb_general_settings[emailConfirmation]" class="wppb_general_settings2">
			<option value="yes" <?php if ($wppb_generalSettings['emailConfirmation'] == 'yes') echo 'selected';?>><?php _e('Yes', 'profilebuilder');?></option>
			<option value="no" <?php if ($wppb_generalSettings['emailConfirmation'] == 'no') echo 'selected';?>><?php _e('No', 'profilebuilder');?></option>
		</select>
	<?php
	}
	if (file_exists ( $wppb_premiumStyle.'custom.fields.php' )){
	?>
		<br/><br/>
		<font id="generalSettingFont"><?php _e('"Admin Approval" Feature Activated:', 'profilebuilder');?></font>
		<select id="adminApprovalSelect" name="wppb_general_settings[adminApproval]" class="wppb_general_settings3">
			<option value="yes" <?php if ($wppb_generalSettings['adminApproval'] == 'yes') echo 'selected';?>><?php _e('Yes', 'profilebuilder');?></option>
			<option value="no" <?php if ($wppb_generalSettings['adminApproval'] == 'no') echo 'selected';?>><?php _e('No', 'profilebuilder');?></option>
		</select>
		
	<?php
		$generalInfoNotes = 
		'<div id="layoutNoticeDiv">
			<font size="1" id="layoutNotice">
				<b>'. __('NOTE:', 'profilebuilder') .'</b><br/>
				&rarr; '. __('The black stylesheet is intended for sites/blogs with a dark background.', 'profilebuilder') .'<br/>
				&rarr; '. __('The white stylesheet is intended for a sites/blogs with a light background color.', 'profilebuilder') .'<br/>
				&rarr; '. __('On single-site installations the "Email Confirmation" feature only works in the front-end, so make sure you also use the "Custom Redirects" feature.', 'profilebuilder') .'<br/>
				&rarr; '. __('The "Email Confirmation" feature is active (by default) on WPMU installations.', 'profilebuilder');
				if (is_multisite() || ($wppb_generalSettings['emailConfirmation'] == 'yes'))
					$generalInfoNotes .= '<br/>&rarr; '.sprintf(__( 'You can find a list of unconfirmed email addresses %1$shere%2$s.', 'profilebuilder'), '<a href="'.get_bloginfo('url').'/wp-admin/users.php?page=unconfirmed_emails">', '</a>');
		$generalInfoNotes .= 
			'</font>
		</div>';
			
			
		}else{
			$generalInfoNotes = 
			'<div id="layoutNoticeDiv">
				<font size="1" id="layoutNotice">
					<b>'. __('NOTE:', 'profilebuilder') .'</b><br/>
					&rarr; '. __('On single-site installations the "Email Confirmation" feature only works in the front-end.', 'profilebuilder') .'<br/>
					&rarr; '. __('The "Email Confirmation" feature is active (by default) on WPMU installations.', 'profilebuilder');
					if (is_multisite() || ($wppb_generalSettings['emailConfirmation'] == 'yes'))
						$generalInfoNotes .= '<br/>&rarr; '.sprintf(__( 'You can find a list of unconfirmed email addresses %1$shere%2$s.', 'profilebuilder'), '<a href="'.get_bloginfo('url').'/wp-admin/users.php?page=unconfirmed_emails">', '</a>');
			$generalInfoNotes .= 
				'</font>
			</div>';
		}
		
		?>
		<br/><br/>
			<font id="generalSettingFont"><?php _e('Allow Users to Log in With:', 'profilebuilder');?></font>
			<select name="wppb_general_settings[loginWith]" class="wppb_general_settings4">
				<option value="username" <?php if ($wppb_generalSettings['loginWith'] == 'username') echo 'selected';?>><?php _e('Username', 'profilebuilder');?></option>
				<option value="email" <?php if ($wppb_generalSettings['loginWith'] == 'email') echo 'selected';?>><?php _e('Email', 'profilebuilder');?></option>
			</select>
		<?php
		
		echo $generalInfoNotes;
	?>
	<div align="right">
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
		</p>
	</div>
	</form>
	
<?php
}