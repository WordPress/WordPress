<?php
function wppb_display_admin_settings(){
?>		
	<form method="post" action="options.php#show-hide-admin-bar">
	<?php 
		global $wp_roles;
	
		$wppb_showAdminBar = get_option('wppb_display_admin_settings');
		settings_fields('wppb_display_admin_settings');
	?>

	
	<h2><?php _e('Show/Hide the Admin Bar on Front End', 'profilebuilder');?></h2>
	<h3><?php _e('Show/Hide the Admin Bar on Front End', 'profilebuilder');?></h3>
	<table class="wp-list-table widefat fixed pages" cellspacing="0">
		<thead>
			<tr>
				<th id="manage-column" scope="col"><?php _e('User-group', 'profilebuilder');?></th>
				<th id="manage-column" scope="col"><?php _e('Visibility', 'profilebuilder');?></th>
			</tr>
		</thead>
			<tbody>
				<?php
				foreach ($wp_roles->roles as $role) {
					$key = $role['name'];
					$setting_exists = !empty($wppb_showAdminBar[$key]);
					echo'<tr>
							<td id="manage-columnCell">'.$key.'</td>
							<td id="manage-columnCell">
								<input type="radio" name="wppb_display_admin_settings['.$key.']" value="default" ';if (!$setting_exists || $wppb_showAdminBar[$key] == 'default') echo ' checked';echo'/><font size="1">'; _e('Default', 'profilebuilder'); echo'</font><span style="padding-left:20px"></span>
								<input type="radio" name="wppb_display_admin_settings['.$key.']" value="show"';if ($setting_exists && $wppb_showAdminBar[$key] == 'show') echo ' checked';echo'/><font size="1">'; _e('Show', 'profilebuilder'); echo'</font><span style="padding-left:20px"></span>
								<input type="radio" name="wppb_display_admin_settings['.$key.']" value="hide"';if ($setting_exists && $wppb_showAdminBar[$key] == 'hide') echo ' checked';echo'/><font size="1">'; _e('Hide', 'profilebuilder'); echo'</font>
							</td>
						</tr>';
				}
				?>
			
	</table>

	<div align="right">
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
		</p>
	</div>
	</form>
	
	
<?php
}