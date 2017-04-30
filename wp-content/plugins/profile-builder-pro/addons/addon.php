<?php
function wppb_displayAddons(){
?>
	<form method="post" action="options.php#add-ons">
		<?php $wppb_addonOptions = get_option('wppb_addon_settings'); ?>
		<?php settings_fields('wppb_addon_settings'); ?>
		
		
		<h2><?php _e('Activate/Deactivate Addons', 'profilebuilder');?></h2>
		<h3><?php _e('Activate/Deactivate Addons', 'profilebuilder');?></h3>
		<table id="wp-list-table widefat fixed pages" cellspacing="0">
			<thead>
				<tr>
					<th id="manage-column" id="addonHeader" scope="col"><?php _e('Name/Description', 'profilebuilder');?></th>
					<th id="manage-column" scope="col"><?php _e('Status', 'profilebuilder');?></th>
				</tr>
			</thead>
				<tbody>
					<tr>  
						<td id="manage-columnCell"><?php _e('User-Listing', 'profilebuilder');?></td> 
						<td> 
							<input type="radio" name="wppb_addon_settings[wppb_userListing]" value="show" <?php if ($wppb_addonOptions['wppb_userListing'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Active', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_addon_settings[wppb_userListing]" value="hide" <?php if ($wppb_addonOptions['wppb_userListing'] == 'hide') echo 'checked';?>/><font size="1"><?php _e('Inactive', 'profilebuilder');?></font>
						</td> 
					</tr>
					<tr>  
						<td id="manage-columnCell"><?php _e('Custom Redirects', 'profilebuilder');?></td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_addon_settings[wppb_customRedirect]" value="show" <?php if ($wppb_addonOptions['wppb_customRedirect'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Active', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_addon_settings[wppb_customRedirect]" value="hide" <?php if ($wppb_addonOptions['wppb_customRedirect'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Inactive', 'profilebuilder');?></font>
						</td> 
					</tr>
					<tr>  
						<td id="manage-columnCell"><?php _e('reCAPTCHA', 'profilebuilder');?></td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_addon_settings[wppb_reCaptcha]" value="show" <?php if ($wppb_addonOptions['wppb_reCaptcha'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Active', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_addon_settings[wppb_reCaptcha]" value="hide" <?php if ($wppb_addonOptions['wppb_reCaptcha'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Inactive', 'profilebuilder');?></font>
						</td> 
										</tr>
					<tr>  
						<td id="manage-columnCell"><?php _e('User Email Customizer', 'profilebuilder');?></td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_addon_settings[wppb_emailCustomizer]" value="show" <?php if ($wppb_addonOptions['wppb_emailCustomizer'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Active', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_addon_settings[wppb_emailCustomizer]" value="hide" <?php if ($wppb_addonOptions['wppb_emailCustomizer'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Inactive', 'profilebuilder');?></font>
						</td> 
					</tr>
					<tr>  
						<td id="manage-columnCell"><?php _e('Admin Email Customizer', 'profilebuilder');?></td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_addon_settings[wppb_emailCustomizerAdmin]" value="show" <?php if ($wppb_addonOptions['wppb_emailCustomizerAdmin'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Active', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_addon_settings[wppb_emailCustomizerAdmin]" value="hide" <?php if ($wppb_addonOptions['wppb_emailCustomizerAdmin'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Inactive', 'profilebuilder');?></font>
						</td> 
					</tr>
				</tbody>
		</table>
		<div align="right">
			<input type="hidden" name="action" value="update" />
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
			</p>
			</form>
		</div>
<?php
}