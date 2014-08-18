<?php
function wppb_default_settings(){
?>
	<form method="post" action="options.php#default-fields">
	<?php $wppb_defaultOptions = get_option('wppb_default_settings'); ?>
	<?php settings_fields('wppb_option_group'); ?>
	
	
	<h2><?php _e('Default Profile Fields', 'profilebuilder');?></h2>
	<h3><?php _e('Default Profile Fields', 'profilebuilder');?></h3>
	<table class="wp-list-table widefat fixed pages" cellspacing="0">
		<thead>
			<tr>
				<th id="manage-column" scope="col"><?php _e('Input Field Name', 'profilebuilder');?></th>
				<th id="manage-column" scope="col"><?php _e('Visibility', 'profilebuilder');?></th>
				<th id="manage-column" scope="col"><?php _e('Required', 'profilebuilder');?></th>
			</tr>
		</thead>
			<tbody class="plugins" > 
				<tr>
					<td colspan="3"><font size="2"><?php _e('Name:', 'profilebuilder');?></font></td> 
				</tr>
			</tbody>
			<tbody>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('Username', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[username]" value="show" checked /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[username]" value="hide" disabled /><font size="1" color="grey"><?php _e('Hide', 'profilebuilder');?></font>
					</td> 						
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[usernameRequired]" value="yes" checked /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[usernameRequired]" value="no" disabled /><font size="1" color="grey"><?php _e('No', 'profilebuilder');?></font>
					</td> 
				</tr>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('First Name', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[firstname]" value="show" <?php if ($wppb_defaultOptions['firstname'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[firstname]" value="hide" <?php if ($wppb_defaultOptions['firstname'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
					</td> 						
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[firstnameRequired]" value="yes" <?php if ($wppb_defaultOptions['firstnameRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[firstnameRequired]" value="no" <?php if ($wppb_defaultOptions['firstnameRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
					</td> 
				</tr>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('Last Name', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[lastname]" value="show" <?php if ($wppb_defaultOptions['lastname'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[lastname]" value="hide" <?php if ($wppb_defaultOptions['lastname'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
					</td>
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[lastnameRequired]" value="yes" <?php if ($wppb_defaultOptions['lastnameRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[lastnameRequired]" value="no" <?php if ($wppb_defaultOptions['lastnameRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
					</td> 
				</tr>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('Nickname', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[nickname]" value="show" <?php if ($wppb_defaultOptions['nickname'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[nickname]" value="hide" <?php if ($wppb_defaultOptions['nickname'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
					</td>
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[nicknameRequired]" value="yes" <?php if ($wppb_defaultOptions['nicknameRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[nicknameRequired]" value="no" <?php if ($wppb_defaultOptions['nicknameRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
					</td> 						
				</tr>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('Display name publicly as...', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[dispname]" value="show" <?php if ($wppb_defaultOptions['dispname'] == 'show') echo 'checked';?> /><?php _e('Show', 'profilebuilder');?><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[dispname]" value="hide" <?php if ($wppb_defaultOptions['dispname'] == 'hide') echo 'checked';?> /><?php _e('Hide', 'profilebuilder');?>
					</td>
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[dispnameRequired]" value="yes" <?php if ($wppb_defaultOptions['dispnameRequired'] == 'yes') echo 'checked';?> /><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[dispnameRequired]" value="no" <?php if ($wppb_defaultOptions['dispnameRequired'] == 'no') echo 'checked';?> /><?php _e('No', 'profilebuilder');?>
					</td> 						
				</tr>
			<tbody class="plugins">
				<tr> 
					<td colspan="3"><font size="2"><?php _e('Contact Info:', 'profilebuilder');?></font></td> 
				</tr>
			</tbody>
			<tbody>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('E-mail', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[email]" value="show" checked><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[email]" value="hide" disabled><font size="1" color="grey"><?php _e('Hide', 'profilebuilder');?></font>
					</td>
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[emailRequired]" value="yes" checked /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[emailRequired]" value="no" disabled /><font size="1" color="grey"><?php _e('No', 'profilebuilder');?></font>
					</td> 		
				</tr>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('Website', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[website]" value="show" <?php if ($wppb_defaultOptions['website'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[website]" value="hide" <?php if ($wppb_defaultOptions['website'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
					</td>
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[websiteRequired]" value="yes" <?php if ($wppb_defaultOptions['websiteRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[websiteRequired]" value="no" <?php if ($wppb_defaultOptions['websiteRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
					</td> 						
				</tr>
			<tbody class="plugins">
			</tbody>
			<tbody>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('AIM', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[aim]" value="show" <?php if ($wppb_defaultOptions['aim'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[aim]" value="hide" <?php if ($wppb_defaultOptions['aim'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
					</td>
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[aimRequired]" value="yes" <?php if ($wppb_defaultOptions['aimRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[aimRequired]" value="no" <?php if ($wppb_defaultOptions['aimRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
					</td> 						
				</tr>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('Yahoo IM', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[yahoo]" value="show" <?php if ($wppb_defaultOptions['yahoo'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[yahoo]" value="hide" <?php if ($wppb_defaultOptions['yahoo'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
					</td>
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[yahooRequired]" value="yes" <?php if ($wppb_defaultOptions['yahooRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[yahooRequired]" value="no" <?php if ($wppb_defaultOptions['yahooRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
					</td> 						
				</tr>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('Jabber / Google Talk', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[jabber]" value="show" <?php if ($wppb_defaultOptions['jabber'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[jabber]" value="hide" <?php if ($wppb_defaultOptions['jabber'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
					</td>
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[jabberRequired]" value="yes" <?php if ($wppb_defaultOptions['jabberRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[jabberRequired]" value="no" <?php if ($wppb_defaultOptions['jabberRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
					</td> 						
				</tr>
			<tbody class="plugins">
				<tr> 
					<td  colspan="3"><font size="2"><?php _e('About Yourself:', 'profilebuilder');?></font></td> 
				</tr>
			</tbody>
			<tbody>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('Biographical Info', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[bio]" value="show" <?php if ($wppb_defaultOptions['bio'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[bio]" value="hide" <?php if ($wppb_defaultOptions['bio'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
					</td>
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[bioRequired]" value="yes" <?php if ($wppb_defaultOptions['bioRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[bioRequired]" value="no" <?php if ($wppb_defaultOptions['bioRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
					</td> 
				</tr>
			<tbody>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('(New) Password', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[password]" value="show" checked><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[password]" value="hide" disabled><font size="1" color="grey"><?php _e('Hide', 'profilebuilder');?></font>
					</td>
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[passwordRequired]" value="yes" checked /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[passwordRequired]" value="no" disabled /><font size="1" color="grey"><?php _e('No', 'profilebuilder');?></font>
					</td> 						
				</tr>
                <tbody>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('City', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[city]" value="show" <?php if ($wppb_defaultOptions['city'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[city]" value="hide" <?php if ($wppb_defaultOptions['city'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
					</td>
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[cityRequired]" value="yes" <?php if ($wppb_defaultOptions['cityRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[cityRequired]" value="no" <?php if ($wppb_defaultOptions['cityRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
					</td> 
				</tr>
			</tbody>
            <tbody>
				<tr>  
					<td id="manage-columnCell"> 
						<span style="padding-left:50px"></span><?php _e('Request Role', 'profilebuilder');?>
					</td> 
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[role]" value="show" checked><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[role]" value="hide" ><font size="1" color="grey"><?php _e('Hide', 'profilebuilder');?></font>
					</td>
					<td id="manage-columnCell"> 
						<input type="radio" name="wppb_default_settings[roleRequired]" value="yes" checked /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
						<input type="radio" name="wppb_default_settings[roleRequired]" value="no" disabled /><font size="1" color="grey"><?php _e('No', 'profilebuilder');?></font>
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
?>