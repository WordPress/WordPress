<?php
function wppb_emailCustomizationInitializeValues(){
	$emailCustomizer = get_option('emailCustomizer', 'not_found');

	if ($emailCustomizer == 'not_found'){
		$emailCustomizer = array(	'from_name' 					=> '%%site_name%%',
									'from' 							=> '%%reply_to%%',
									'settingsGroup1Option2' 		=> __('A new account has been created for you.', 'profilebuilder'),
									'settingsGroup1Option3' 		=> sprintf(__( 'Welcome to %1$s!<br/><br/> Your username is:%2$s and password:%3$s', 'profilebuilder'), '%%site_name%%', '%%username%%', '%%password%%'),
									'settingsGroup3Option2' 		=> __('A new account has been created for you.', 'profilebuilder'),
									'settingsGroup3Option3' 		=> sprintf(__( 'Welcome to %1$s!<br/><br/> Your username is:%2$s and password:%3$s<br/>Before you can access your account, an administrator needs to approve it. You will be notified via email.', 'profilebuilder'), '%%site_name%%', '%%username%%', '%%password%%'),
									'settingsGroup4Option2' 		=> sprintf(__('Your account on %1$s has been approved!', 'profilebuilder'), '%%site_name%%'),
									'settingsGroup4Option3' 		=> sprintf(__('An administrator has just approved your account on %1$s (%2$s).', 'profilebuilder'), '%%site_name%%', '%%username%%'),
									'settingsGroup2Option2' 		=> sprintf(__( '[%1$s] Activate %2$s', 'profilebuilder'), '%%site_name%%', '%%username%%'),
									'settingsGroup2Option3' 		=> sprintf(__( "To activate your user, please click the following link:\n\n%s\n\nAfter you activate, you will receive *another email* with your login.\n\n", "profilebuilder" ), '%%activation_key%%'),
									'settingsGroup4Option6' 		=> sprintf(__('Your account on %1$s has been unapproved!', 'profilebuilder'), '%%site_name%%'),
									'settingsGroup4Option7'			=> sprintf(__('An administrator has just unapproved your account on %1$s (%2$s).', 'profilebuilder'), '%%site_name%%', '%%username%%'),
									'admin_settingsGroup1Option2' 	=> sprintf(__( '[%1$s] A new subscriber has (been) registered!', 'profilebuilder'), '%%site_name%%'),
									'admin_settingsGroup1Option3' 	=> sprintf(__( 'New subscriber on %1$s.<br/><br/>Username:%2$s<br/>E-mail:%3$s<br/>', 'profilebuilder'), '%%site_name%%', '%%username%%', '%%user_email%%'),
									'admin_settingsGroup3Option2' 	=> sprintf(__( '[%1$s] A new subscriber has (been) registered!', 'profilebuilder'), '%%site_name%%'),
									'admin_settingsGroup3Option3' 	=> sprintf(__( 'New subscriber on %1$s.<br/><br/>Username:%2$s<br/>E-mail:%3$s<br/><br/>The "Admin Approval" feature was activated at the time of registration, so please remember that you need to approve this user before he/she can log in!', 'profilebuilder'), '%%site_name%%', '%%username%%', '%%user_email%%'),
									'admin_settingsGroup2Option2' 	=> '',
									'admin_settingsGroup2Option3' 	=> ''
								);
		update_option('emailCustomizer', $emailCustomizer);
	}
}

function wppb_emailCustomizer(){
	//first thing we will have to do is create a default settings on first-time run of the addon
	wppb_emailCustomizationInitializeValues();
	
	$show =  __('Show', 'profilebuilder');
	$hide =  __('Hide', 'profilebuilder');
	
?>
	
	<form method="post" class="emailCustomizer clearfix" action="options.php#wppb_emailCustomizer">
		<?php $emailCustomizer = get_option('emailCustomizer'); ?>
		<?php settings_fields('emailCustomizer'); ?>

		<h2><?php _e('User Email Customizer', 'profilebuilder');?></h2>
		<h3><?php _e('User Email Customizer', 'profilebuilder');?></h3>

		<p class="defaultText">
			<?php _e('Here you can customize all the emails sent the users.', 'profilebuilder');?>
		</p>
	
		<div class="label"><?php _e('Common Settings: <span style="color:gray; font-size:11px;">These settings are also replicated in the "Admin Email Customizer" settings-page upon save.</span>','profilebuilder');?></div>
		<table class="toggleElement">
			<tr>
				<td class="emailCustomizerUserCell1"><span> &rarr; <?php _e('From (name): ', 'profilebuilder');?></span></td>
				<td class="emailCustomizerUserCell2">
					<input name="emailCustomizer[from_name]" id="emailCustomizerFrom1" class="emailCustomizerFrom1" type="text" value="<?php echo $emailCustomizer['from_name'];?>" />
				</td>
			</tr>
			<tr>
				<td class="emailCustomizerUserCell1" colspan="2">
						<?php echo 
							'<span style="color:gray"><b>'. __('Available Merge Tags', 'profilebuilder') .'</b><br/>
							&rarr; <span class="selectable">%%site_name%%</span> - Display the site name</span>';
						?>
				</td>
			</tr>
		</table>
		<table class="toggleElement">
			<tr>
				<td class="emailCustomizerUserCell1"><span> &rarr; <?php _e('From (reply-to email): ', 'profilebuilder');?></span></td>
				<td class="emailCustomizerUserCell2">
					<input name="emailCustomizer[from]" id="emailCustomizerFrom2" class="emailCustomizerFrom2" type="text" value="<?php echo $emailCustomizer['from'];?>" />
				</td>
			</tr>
			<tr>
				<td class="emailCustomizerUserCell1" colspan="2">
						<?php echo 
							'<span style="color:gray"><b>'. __('Available Merge Tags', 'profilebuilder') .'</b><br/>
							&rarr; <span class="selectable">%%reply_to%%</span> - Display the default email address</span>';
						?>
				</td>
			</tr>
		</table>
		
				<div class="TableHeader">
					<span>Email Message Type</span>
					<span class="right">Show / Hide</span>
				</div>
				<!--END DIV TABLE HEADER-->
				
				<div class="wrapElement">
					
					<div class="labelAction clearfix">
						<?php _e('Default Registration','profilebuilder');?>
						<a href="javascript:void(0);" class="button button_emailCustomizer">Show</a>
						<div class="arrow">&nbsp;</div>
					</div>	
					
					<table class="toggleElement">
						<tr class="emailCustomizerUserRow">
							<td class="emailCustomizerUserCell1">
								<span> &rarr; <?php _e('Email Subject: ', 'profilebuilder');?></span>
							</td>
							<td class="emailCustomizerUserCell2">
								<input name="emailCustomizer[settingsGroup1Option2]" id="settingsGroup1Option2" class="settingsGroup1Option2" type="text" value="<?php echo $emailCustomizer['settingsGroup1Option2'];?>" />
							</td>
						</tr>
						<tr class="fieldTableRow">
							<td class="fieldTableCell1" colspan="2">
								<span> &rarr; <?php _e('Email Content: <span style="color:gray">This is the email sent to the <span style="color:red">user</span> upon registration.</span>', 'profilebuilder'); ?></span>
							</td>
						</tr>
						<tr class="fieldTableRow">
							<td class="fieldTableCell1" colspan="2">
								<?php wp_editor( html_entity_decode( stripslashes( $emailCustomizer['settingsGroup1Option3'] ) ), 'settingsGroup1Option3', $settings = array('textarea_name' => 'emailCustomizer[settingsGroup1Option3]') ); ?> 
							</td>
						</tr>
						<tr>
							<td class="fieldTableCell1" colspan="2">
									<?php echo 
										'<span style="color:gray"><b>'. __('Available Merge Tags', 'profilebuilder') .'</b><br/>
										&rarr; <span class="selectable">%%site_url%%</span> - Display the site URL<br/>
										&rarr; <span class="selectable">%%site_name%%</span> - Display the site name<br/>
										&rarr; <span class="selectable">%%user_id%%</span> - Display the userID<br/>
										&rarr; <span class="selectable">%%username%%</span> - Display the username<br/>
										&rarr; <span class="selectable">%%user_email%%</span> - Display the email-address of the user<br/>
										&rarr; <span class="selectable">%%password%%</span> - Display the users password<br/>
										&rarr; <span class="selectable">%%first_name%%</span> - Display the users first name<br/>
										&rarr; <span class="selectable">%%last_name%%</span> - Display the users last name<br/>
										&rarr; <span class="selectable">%%nickname%%</span> - Display the users nickname<br/>
										&rarr; <span class="selectable">%%website%%</span> - Display the users website<br/>
										&rarr; <span class="selectable">%%description%%</span> - Display the "about yourself" content<br/>
										&rarr; <span class="selectable">%%aim%%</span> - Display the aim value<br/>
										&rarr; <span class="selectable">%%yim%%</span> - Display the yim value<br/>
										&rarr; <span class="selectable">%%jabber%%</span> - Display the jabber value<br/>
										&rarr; <span class="selectable">%%meta_name%%</span> - Display any extra field by replacing the meta_name with the fields\' meta-name</span>';
									?>
							</td>
						</tr>
					</table>
					<!--END ELEMENT 2-->
				
				</div>
				<!--END WRAPELEMENT-->
				
				<div class="wrapElement">
					
					<div class="labelAction clearfix blue">
						<?php _e('Registration with Email Confirmation','profilebuilder');?>
						<a href="javascript:void(0);" class="button button_emailCustomizer">Show</a>
						<div class="arrow">&nbsp;</div>
					</div>	
					
					
					<table class="toggleElement">
						<tr>
							<td class="emailCustomizerUserCell1">
								<span> &rarr; <?php _e('Email Subject: ', 'profilebuilder');?></span>
							</td>
							<td class="emailCustomizerUserCell2">
								<input name="emailCustomizer[settingsGroup2Option2]" id="settingsGroup2Option2" class="settingsGroup2Option2" type="text" value="<?php echo $emailCustomizer['settingsGroup2Option2'];?>" />
							</td>
						</tr>
						<tr>
							<td class="fieldTableCell1" colspan="2">
								<span> &rarr; <?php _e('Email Content: <span style="color:gray">This is the email sent to the <span style="color:red">user</span> upon registration with email confirmation.</span>', 'profilebuilder'); ?></span>
							</td>
						</tr>
						<tr>
							<td class="fieldTableCell1" colspan="2">
								<?php wp_editor( html_entity_decode( stripslashes( $emailCustomizer['settingsGroup2Option3'] ) ), 'settingsGroup2Option3', $settings = array('textarea_name' => 'emailCustomizer[settingsGroup2Option3]') ); ?>
							</td>
						</tr>
						<tr>
							<td class="fieldTableCell1" colspan="2">
									<?php echo 
										'<span style="color:gray"><b>'. __('Available Merge Tags', 'profilebuilder') .'</b><br/>
										&rarr; <span class="selectable">%%site_url%%</span> - Display the site URL<br/>
										&rarr; <span class="selectable">%%site_name%%</span> - Display the site name<br/>
										&rarr; <span class="selectable">%%username%%</span> - Display the username<br/>
										&rarr; <span class="selectable">%%user_email%%</span> - Display the email-address of the user<br/>
										&rarr; <span class="selectable">%%password%%</span> - Display the users password<br/>
										&rarr; <span class="selectable">%%first_name%%</span> - Display the users first name<br/>
										&rarr; <span class="selectable">%%last_name%%</span> - Display the users last name<br/>
										&rarr; <span class="selectable">%%nickname%%</span> - Display the users nickname<br/>
										&rarr; <span class="selectable">%%website%%</span> - Display the users website<br/>
										&rarr; <span class="selectable">%%description%%</span> - Display the "about yourself" content<br/>
										&rarr; <span class="selectable">%%aim%%</span> - Display the aim value<br/>
										&rarr; <span class="selectable">%%yim%%</span> - Display the yim value<br/>
										&rarr; <span class="selectable">%%jabber%%</span> - Display the jabber value<br/>
										&rarr; <span class="selectable">%%meta_name%%</span> - Display any extra field by replacing the meta_name with the fields\' meta-name</span>';
									?>
							</td>
						</tr>
					</table>
					<!--END ELEMENT 3-->
				
				</div>
				<!--END WRAPELEMENT-->
		
				
				<div class="wrapElement">
					
					<div class="labelAction clearfix blue">
						<?php _e('Registration with Admin Approval','profilebuilder');?>
						<a href="javascript:void(0);" class="button button_emailCustomizer">Show</a>
						<div class="arrow">&nbsp;</div>
					</div>
					
					
					<table class="toggleElement">
						<tr>
							<td class="emailCustomizerUserCell1">
								<span> &rarr; <?php _e('Email Subject: ', 'profilebuilder');?></span>
							</td>
							<td class="emailCustomizerUserCell2">
								<input name="emailCustomizer[settingsGroup3Option2]" id="settingsGroup3Option2" class="settingsGroup3Option2" type="text" value="<?php echo $emailCustomizer['settingsGroup3Option2'];?>" />
							</td>
						</tr>
						<tr>
							<td class="fieldTableCell1" colspan="2">
								<span> &rarr; <?php _e('Email Content: <span style="color:gray">This is the email sent to the <span style="color:red">user</span> upon registration with admin approval.</span>', 'profilebuilder'); ?></span>
							</td>
						</tr>
						<tr>
							<td class="fieldTableCell1" colspan="2">
								<?php wp_editor( html_entity_decode( stripslashes( $emailCustomizer['settingsGroup3Option3'] ) ), 'settingsGroup3Option3', $settings = array('textarea_name' => 'emailCustomizer[settingsGroup3Option3]') ); ?>
							</td>
						</tr>
						<tr>
							<td class="fieldTableCell1" colspan="2">
									<?php echo 
										'<span style="color:gray"><b>'. __('Available Merge Tags', 'profilebuilder') .'</b><br/>
										&rarr; <span class="selectable">%%site_url%%</span> - Display the site URL<br/>
										&rarr; <span class="selectable">%%site_name%%</span> - Display the site name<br/>
										&rarr; <span class="selectable">%%user_id%%</span> - Display the userID<br/>
										&rarr; <span class="selectable">%%username%%</span> - Display the username<br/>
										&rarr; <span class="selectable">%%user_email%%</span> - Display the email-address of the user<br/>
										&rarr; <span class="selectable">%%password%%</span> - Display the users password<br/>
										&rarr; <span class="selectable">%%first_name%%</span> - Display the users first name<br/>
										&rarr; <span class="selectable">%%last_name%%</span> - Display the users last name<br/>
										&rarr; <span class="selectable">%%nickname%%</span> - Display the users nickname<br/>
										&rarr; <span class="selectable">%%website%%</span> - Display the users website<br/>
										&rarr; <span class="selectable">%%description%%</span> - Display the "about yourself" content<br/>
										&rarr; <span class="selectable">%%aim%%</span> - Display the aim value<br/>
										&rarr; <span class="selectable">%%yim%%</span> - Display the yim value<br/>
										&rarr; <span class="selectable">%%jabber%%</span> - Display the jabber value<br/>
										&rarr; <span class="selectable">%%meta_name%%</span> - Display any extra field by replacing the meta_name with the fields\' meta-name</span>';
									?>
							</td>
						</tr>
					</table>
					<!--END ELEMENT 4-->
					
				</div>
				<!--END WRAPELEMENT-->
				
				<div class="wrapElement">
					
					<div class="labelAction clearfix blue">
						<?php _e('Admin Approval Notifications (on status change)','profilebuilder');?>
						<a href="javascript:void(0);" class="button button_emailCustomizer">Show</a>
						<div class="arrow">&nbsp;</div>
					</div>
					
					<table class="toggleElement">
						<tr">
							<td class="emailCustomizerUserCell1" colspan="2">
								<div class="label"><strong><?php _e('Message Sent to the User Upon Approval', 'profilebuilder');?></strong></div>
							</td>
						</tr>
						<tr>
							<td class="emailCustomizerUserCell1">
								<span> &rarr; <?php _e('Email Subject: ', 'profilebuilder');?></span>
							</td>
							<td class="emailCustomizerUserCell2">
								<input name="emailCustomizer[settingsGroup4Option2]" id="settingsGroup4Option2" class="settingsGroup4Option2" type="text" value="<?php echo $emailCustomizer['settingsGroup4Option2'];?>" />
							</td>
						</tr>
						<tr>
							<td class="fieldTableCell1" colspan="2">
								<span> &rarr; <?php _e('Email Content: <span style="color:gray">This is the email sent to the <span style="color:red">user</span> upon approval.</span>', 'profilebuilder'); ?></span>
							</td>
						</tr>
						<tr>
							<td class="fieldTableCell1" colspan="2">
								<?php wp_editor( html_entity_decode( stripslashes( $emailCustomizer['settingsGroup4Option3'] ) ), 'settingsGroup4Option3', $settings = array('textarea_name' => 'emailCustomizer[settingsGroup4Option3]') ); ?>
							</td>
						</tr>
						<tr>
							<td class="fieldTableCell1" colspan="2">
									<?php echo 
										'<span style="color:gray"><b>'. __('Available Merge Tags', 'profilebuilder') .'</b><br/>
										&rarr; <span class="selectable">%%site_url%%</span> - Display the site URL<br/>
										&rarr; <span class="selectable">%%site_name%%</span> - Display the site name<br/>
										&rarr; <span class="selectable">%%user_id%%</span> - Display the userID<br/>
										&rarr; <span class="selectable">%%username%%</span> - Display the username<br/>
										&rarr; <span class="selectable">%%user_email%%</span> - Display the email-address of the user<br/>
										&rarr; <span class="selectable">%%user_status%%</span> - Display the new user-status<br/>
										&rarr; <span class="selectable">%%first_name%%</span> - Display the users first name<br/>
										&rarr; <span class="selectable">%%last_name%%</span> - Display the users last name<br/>
										&rarr; <span class="selectable">%%nickname%%</span> - Display the users nickname<br/>
										&rarr; <span class="selectable">%%website%%</span> - Display the users website<br/>
										&rarr; <span class="selectable">%%description%%</span> - Display the "about yourself" content<br/>
										&rarr; <span class="selectable">%%aim%%</span> - Display the aim value<br/>
										&rarr; <span class="selectable">%%yim%%</span> - Display the yim value<br/>
										&rarr; <span class="selectable">%%jabber%%</span> - Display the jabber value<br/>
										&rarr; <span class="selectable">%%meta_name%%</span> - Display any extra field by replacing the meta_name with the fields\' meta-name</span>';
									?>
							</td>
						</tr>			
						<tr class="sortingTableRow">
							<td class="emailCustomizerUserCell1" colspan="2">
								<div class="label"><strong><?php _e('Message Sent to the User Upon Unapproval', 'profilebuilder');?></strong></div>
							</td>
						</tr>
						<tr class="sortingTableRow">
							<td class="emailCustomizerUserCell1">
								<span> &rarr; <?php _e('Email Subject: ', 'profilebuilder');?></span>
							</td>
							<td class="emailCustomizerUserCell2">
								<input name="emailCustomizer[settingsGroup4Option6]" id="settingsGroup4Option6" class="settingsGroup4Option6" type="text" value="<?php echo $emailCustomizer['settingsGroup4Option6'];?>" />
							</td>
						</tr>
						<tr class="fieldTableRow">
							<td class="emailCustomizerUserCell1" colspan="2">
								<span> &rarr; <?php _e('Email Content: <span style="color:gray">This is the email sent to the <span style="color:red">user</span> upon unapproval.</span>', 'profilebuilder'); ?></span>
							</td>
						</tr>
						<tr class="fieldTableRow">
							<td class="emailCustomizerUserCell1" colspan="2">
								<?php wp_editor( html_entity_decode( stripslashes( $emailCustomizer['settingsGroup4Option7'] ) ), 'settingsGroup4Option7', $settings = array('textarea_name' => 'emailCustomizer[settingsGroup4Option7]') ); ?>
							</td>
						</tr>
						<tr class="fieldTableRow">
							<td class="fieldTableCell1" colspan="2">
									<?php echo 
										'<span style="color:gray"><b>'. __('Available Merge Tags', 'profilebuilder') .'</b><br/>
										&rarr; <span class="selectable">%%site_url%%</span> - Display the site URL<br/>
										&rarr; <span class="selectable">%%site_name%%</span> - Display the site name<br/>
										&rarr; <span class="selectable">%%user_id%%</span> - Display the userID<br/>
										&rarr; <span class="selectable">%%username%%</span> - Display the username<br/>
										&rarr; <span class="selectable">%%user_email%%</span> - Display the email-address of the user<br/>
										&rarr; <span class="selectable">%%user_status%%</span> - Display the new user-status<br/>
										&rarr; <span class="selectable">%%first_name%%</span> - Display the users first name<br/>
										&rarr; <span class="selectable">%%last_name%%</span> - Display the users last name<br/>
										&rarr; <span class="selectable">%%nickname%%</span> - Display the users nickname<br/>
										&rarr; <span class="selectable">%%website%%</span> - Display the users website<br/>
										&rarr; <span class="selectable">%%description%%</span> - Display the "about yourself" content<br/>
										&rarr; <span class="selectable">%%aim%%</span> - Display the aim value<br/>
										&rarr; <span class="selectable">%%yim%%</span> - Display the yim value<br/>
										&rarr; <span class="selectable">%%jabber%%</span> - Display the jabber value<br/>
										&rarr; <span class="selectable">%%meta_name%%</span> - Display any extra field by replacing the meta_name with the fields\' meta-name</span>';
									?>
							</td>
						</tr>
					</table>
					<input name="emailCustomizer[admin_settingsGroup1Option2]" id="admin_settingsGroup1Option2hidden" class="admin_settingsGroup1Option2hidden" type="hidden" value="<?php echo $emailCustomizer['admin_settingsGroup1Option2'];?>" />
					<input name="emailCustomizer[admin_settingsGroup1Option3]" id="admin_settingsGroup1Option3hidden" class="admin_settingsGroup1Option3hidden" type="hidden" value="<?php echo $emailCustomizer['admin_settingsGroup1Option3'];?>" />
					<input name="emailCustomizer[admin_settingsGroup3Option2]" id="admin_settingsGroup3Option2hidden" class="admin_settingsGroup3Option2hidden" type="hidden" value="<?php echo $emailCustomizer['admin_settingsGroup3Option2'];?>" />
					<input name="emailCustomizer[admin_settingsGroup3Option3]" id="admin_settingsGroup3Option3hidden" class="admin_settingsGroup3Option3hidden" type="hidden" value="<?php echo $emailCustomizer['admin_settingsGroup3Option3'];?>" />
					<input name="emailCustomizer[admin_settingsGroup2Option2]" id="admin_settingsGroup2Option2hidden" class="admin_settingsGroup2Option2hidden" type="hidden" value="<?php echo $emailCustomizer['admin_settingsGroup2Option2'];?>" />
					<input name="emailCustomizer[admin_settingsGroup2Option3]" id="admin_settingsGroup2Option3hidden" class="admin_settingsGroup2Option3hidden" type="hidden" value="<?php echo $emailCustomizer['admin_settingsGroup2Option3'];?>" />
					<!--END ELEMENT5-->
					
				</div>
				<!--END WRAPELEMENT-->
				
				<div class="TableHeader downFooter">
					<span>Email Message Type</span>
					<span class="right">Show / Hide</span>
				</div>
				<!--END DIV TABLE HEADER-->
				
		
	<div class="right">
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
		</p>
	</form>
	</div>
	
	<script type="text/javascript">
		
		// SLIDE/TOGGLE
		
		jQuery(function(){
			jQuery('.wrapElement .toggleElement').css('display','none');
		})
		
		jQuery(".emailCustomizer .labelAction .button_emailCustomizer").click(function() {
			jQuery(this).parent().siblings('.toggleElement').toggle(500,'linear',function(){
				if(jQuery(this).css('display') == 'none') {
					jQuery(this).siblings('.labelAction').addClass('blue');
					jQuery(this).siblings('.labelAction').removeClass('yellow');
					jQuery(this).siblings('.labelAction').children('a.button_emailCustomizer').html('Show');
				}
				else {
					jQuery(this).siblings('.labelAction').addClass('yellow');
					jQuery(this).siblings('.labelAction').removeClass('blue');
					jQuery(this).siblings('.labelAction').children('a.button_emailCustomizer').html('Hide');
				}
			});
		});
		
	</script>
		
<?php
}


function wppb_emailCustomizerAdmin(){
	//first thing we will have to do is create a default settings on first-time run of the addon
	wppb_emailCustomizationInitializeValues();

	$show =  __('Show', 'profilebuilder');
	$hide =  __('Hide', 'profilebuilder');
	
?>
	
	<form method="post" class="emailCustomizer clearfix" action="options.php#wppb_emailCustomizerAdmin">
		<?php $emailCustomizer = get_option('emailCustomizer'); ?>
		<?php settings_fields('emailCustomizer'); ?>
		
		<h2><?php _e('Admin Email Customizer', 'profilebuilder');?></h2>
		<h3><?php _e('Admin Email Customizer', 'profilebuilder');?></h3>

		<p class="defaultText">
			<?php _e('Here you can customize all the emails sent the administrator.', 'profilebuilder');?>
		</p>
	
		<div class="label"><?php _e('Common Settings: <span style="color:gray; font-size:11px;">These settings are also replicated in the "User Email Customizer" settings-page upon save.</span>','profilebuilder');?></div>		
		<table class="commonSettings2">
			<tr>
				<td class="emailCustomizerAdminCell1"><span> &rarr; <?php _e('From (name): ', 'profilebuilder');?></span></td>
				<td class="emailCustomizerAdminCell2">
					<input name="emailCustomizer[from_name]" id="emailCustomizerFrom3" class="emailCustomizerFrom3" type="text" value="<?php echo $emailCustomizer['from_name'];?>" />
				</td>
			</tr>
			<tr>
				<td class="emailCustomizerAdminCell1" colspan="2">
						<?php echo 
							'<span style="color:gray"><b>'. __('Available Merge Tags', 'profilebuilder') .'</b><br/>
							&rarr; <span class="selectable">%%site_name%%</span> - Display the site name</span>';
						?>
				</td>
			</tr>
		</table>
		<table class="commonSettings2">
			<tr>
				<td class="emailCustomizerAdminCell1"><span> &rarr; <?php _e('From (reply-to email): ', 'profilebuilder');?></span></td>
				<td class="emailCustomizerAdminCell2">
					<input name="emailCustomizer[from]" id="emailCustomizerFrom4" class="emailCustomizerFrom4" type="text" value="<?php echo $emailCustomizer['from'];?>" />
				</td>
			</tr>
			<tr>
				<td class="emailCustomizerAdminCell1" colspan="2">
						<?php echo 
							'<span style="color:gray"><b>'. __('Available Merge Tags', 'profilebuilder') .'</b><br/>
							&rarr; <span class="selectable">%%reply_to%%</span> - Display the default email address</span>';
						?>
				</td>
			</tr>
		</table>
		
	
		
		<div class="TableHeader">
			<span>Email Message Type</span>
			<span class="right">Show / Hide</span>
		</div>
		<!--END DIV TABLE HEADER-->
		
		<div class="wrapElement">		
			<div class="labelAction clearfix">
				<?php _e('Default Registration','profilebuilder');?>
				<a href="javascript:void(0);" class="button button_emailCustomizer">Show</a>
				<div class="arrow">&nbsp;</div>
			</div>	
			
			<table class="toggleElement">
				<tr class="emailCustomizerAdminUserRow">
					<td class="emailCustomizerAdminCell1">
						<span> &rarr; <?php _e('Email Subject: ', 'profilebuilder');?></span>
					</td>
					<td class="emailCustomizerAdminCell2">
						<input name="emailCustomizer[admin_settingsGroup1Option2]" id="admin_settingsGroup1Option2" class="admin_settingsGroup1Option2" type="text" value="<?php echo $emailCustomizer['admin_settingsGroup1Option2'];?>" />
					</td>
				</tr>
				<tr class="fieldTableRow">
					<td class="fieldTableCell1" colspan="2">
						<span> &rarr; <?php _e('Email Content: <span style="color:gray">This is the email sent to the <span style="color:red">administrator</span> upon user registration.</span>', 'profilebuilder'); ?></span>
					</td>
				</tr>
				<tr class="fieldTableRow">
					<td class="fieldTableCell1" colspan="2">
						<?php wp_editor( html_entity_decode( stripslashes( $emailCustomizer['admin_settingsGroup1Option3'] ) ), 'admin_settingsGroup1Option3', $settings = array('textarea_name' => 'emailCustomizer[admin_settingsGroup1Option3]') ); ?> 
					</td>
				</tr>
				<tr>
					<td class="fieldTableCell1" colspan="2">
							<?php echo 
								'<span style="color:gray"><b>'. __('Available Merge Tags', 'profilebuilder') .'</b><br/>
								&rarr; <span class="selectable">%%site_url%%</span> - Display the site URL<br/>
								&rarr; <span class="selectable">%%site_name%%</span> - Display the site name<br/>
								&rarr; <span class="selectable">%%user_id%%</span> - Display the userID<br/>
								&rarr; <span class="selectable">%%username%%</span> - Display the username<br/>
								&rarr; <span class="selectable">%%user_email%%</span> - Display the email-address of the user<br/>
								&rarr; <span class="selectable">%%password%%</span> - Display the password<br/>
								&rarr; <span class="selectable">%%first_name%%</span> - Display the users first name<br/>
								&rarr; <span class="selectable">%%last_name%%</span> - Display the users last name<br/>
								&rarr; <span class="selectable">%%nickname%%</span> - Display the users nickname<br/>
								&rarr; <span class="selectable">%%website%%</span> - Display the users website<br/>
								&rarr; <span class="selectable">%%description%%</span> - Display the "about yourself" content<br/>
								&rarr; <span class="selectable">%%aim%%</span> - Display the aim value<br/>
								&rarr; <span class="selectable">%%yim%%</span> - Display the yim value<br/>
								&rarr; <span class="selectable">%%jabber%%</span> - Display the jabber value<br/>
								&rarr; <span class="selectable">%%meta_name%%</span> - Display any extra field by replacing the meta_name with the fields\' meta-name</span>';
							?>
					</td>
				</tr>
			</table>
		</div>
		<!--END WRAPELEMENT-->
		<!--
		<div class="wrapElement">		
		
			<div class="labelAction clearfix">
				<?php _e('Registration with Email Confirmation','profilebuilder');?>
				<a href="javascript:void(0);" class="button button_emailCustomizer">Show</a>
				<div class="arrow">&nbsp;</div>
			</div>
			
			<table class="toggleElement">
				<tr class="emailCustomizerAdminRow">
					<td class="emailCustomizerAdminCell1">
						<span> &rarr; <?php _e('Email Subject: ', 'profilebuilder');?></span>
					</td>
					<td class="emailCustomizerAdminCell2">
						<input name="emailCustomizerAdmin[admin_settingsGroup2Option2]" id="admin_settingsGroup2Option2" class="admin_settingsGroup2Option2" type="text" value="<?php echo $emailCustomizer['admin_settingsGroup2Option2'];?>" />
					</td>
				</tr>
				<tr class="fieldTableRow">
					<td class="fieldTableCell1" colspan="2">
						<span> &rarr; <?php _e('Email Content: <span style="color:gray">This is the email sent to the <span style="color:red">administrator</span> upon user registration with email confirmation.</span>', 'profilebuilder'); ?></span>
					</td>
				</tr>
				<tr class="fieldTableRow">
					<td class="fieldTableCell1" colspan="2">
						<?php wp_editor( html_entity_decode( stripslashes( $emailCustomizer['admin_settingsGroup2Option3'] ) ), 'admin_settingsGroup2Option3', $settings = array('textarea_name' => 'emailCustomizer[admin_settingsGroup2Option3]') ); ?>
					</td>
				</tr>
				<tr class="fieldTableRow">
					<td class="fieldTableCell1" colspan="2">
							<?php echo 
								'<span style="color:gray"><b>'. __('Available Merge Tags', 'profilebuilder') .'</b><br/>
								&rarr; <span class="selectable">%%site_url%%</span> - Display the site URL<br/>
								&rarr; <span class="selectable">%%site_name%%</span> - Display the site name<br/>
								&rarr; <span class="selectable">%%user_id%%</span> - Display the userID<br/>
								&rarr; <span class="selectable">%%username%%</span> - Display the username<br/>
								&rarr; <span class="selectable">%%user_email%%</span> - Display the email-address of the user<br/>
								&rarr; <span class="selectable">%%password%%</span> - Display the password<br/>
								&rarr; <span class="selectable">%%last_name%%</span> - Display the users last name<br/>
								&rarr; <span class="selectable">%%nickname%%</span> - Display the users nickname<br/>
								&rarr; <span class="selectable">%%website%%</span> - Display the users website<br/>
								&rarr; <span class="selectable">%%description%%</span> - Display the "about yourself" content<br/>
								&rarr; <span class="selectable">%%aim%%</span> - Display the aim value<br/>
								&rarr; <span class="selectable">%%yim%%</span> - Display the yim value<br/>
								&rarr; <span class="selectable">%%jabber%%</span> - Display the jabber value<br/>
								&rarr; <span class="selectable">%%meta_name%%</span> - Display any extra field by replacing the meta_name with the fields\' meta-name</span>';
							?>

					</td>
				</tr>
			</table>
		
		</div> -->
		<!--END WRAP ELEMENT-->
		
		<div class="wrapElement">		
		
			<div class="labelAction clearfix">
				<?php _e('Registration with Admin Approval','profilebuilder');?>
				<a href="javascript:void(0);" class="button button_emailCustomizer">Show</a>
				<div class="arrow">&nbsp;</div>
			</div>
			
			<table class="toggleElement">
				<tr class="emailCustomizerAdminRow">
					<td class="emailCustomizerAdminCell1">
						<span> &rarr; <?php _e('Email Subject: ', 'profilebuilder');?></span>
					</td>
					<td class="emailCustomizerAdminCell2">
						<input name="emailCustomizer[admin_settingsGroup3Option2]" id="admin_settingsGroup3Option2" class="admin_settingsGroup3Option2" type="text" value="<?php echo $emailCustomizer['admin_settingsGroup3Option2'];?>" />
					</td>
				</tr>
				<tr class="fieldTableRow">
					<td class="fieldTableCell1" colspan="2">
						<span> &rarr; <?php _e('Email Content: <span style="color:gray">This is the email sent to the <span style="color:red">administrator</span> upon user registration with admin approval.</span>', 'profilebuilder'); ?></span>
					</td>
				</tr>
				<tr class="fieldTableRow">
					<td class="fieldTableCell1" colspan="2">
						<?php wp_editor( html_entity_decode( stripslashes( $emailCustomizer['admin_settingsGroup3Option3'] ) ), 'admin_settingsGroup3Option3', $settings = array('textarea_name' => 'emailCustomizer[admin_settingsGroup3Option3]') ); ?>
					</td>
				</tr>
				<tr class="fieldTableRow">
					<td class="fieldTableCell1" colspan="2">
							<?php echo 
								'<span style="color:gray"><b>'. __('Available Merge Tags', 'profilebuilder') .'</b><br/>
								&rarr; <span class="selectable">%%site_url%%</span> - Display the site URL<br/>
								&rarr; <span class="selectable">%%site_name%%</span> - Display the site name<br/>
								&rarr; <span class="selectable">%%user_id%%</span> - Display the userID<br/>
								&rarr; <span class="selectable">%%username%%</span> - Display the username<br/>
								&rarr; <span class="selectable">%%user_email%%</span> - Display the email-address of the user<br/>
								&rarr; <span class="selectable">%%password%%</span> - Display the password<br/>
								&rarr; <span class="selectable">%%first_name%%</span> - Display the first name<br/>
								&rarr; <span class="selectable">%%last_name%%</span> - Display the last name<br/>
								&rarr; <span class="selectable">%%nickname%%</span> - Display the nickname<br/>
								&rarr; <span class="selectable">%%description%%</span> - Display the "about yourself" content<br/>
								&rarr; <span class="selectable">%%aim%%</span> - Display the aim value<br/>
								&rarr; <span class="selectable">%%yim%%</span> - Display the yim value<br/>
								&rarr; <span class="selectable">%%jabber%%</span> - Display the jabber value<br/>
								&rarr; <span class="selectable">%%meta_name%%</span> - Display any extra field by replacing the meta_name with the fields\' meta-name</span>';
							?>
					</td>
				</tr>
			</table>
			<input name="emailCustomizer[settingsGroup1Option2]" id="settingsGroup1Option2hidden" class="settingsGroup1Option2hidden" type="hidden" value="<?php echo $emailCustomizer['settingsGroup1Option2'];?>" />
			<input name="emailCustomizer[settingsGroup1Option3]" id="settingsGroup1Option3hidden" class="settingsGroup1Option3hidden" type="hidden" value="<?php echo $emailCustomizer['settingsGroup1Option3'];?>" />
			<input name="emailCustomizer[settingsGroup3Option2]" id="settingsGroup3Option2hidden" class="settingsGroup3Option2hidden" type="hidden" value="<?php echo $emailCustomizer['settingsGroup3Option2'];?>" />
			<input name="emailCustomizer[settingsGroup3Option3]" id="settingsGroup3Option3hidden" class="settingsGroup3Option3hidden" type="hidden" value="<?php echo $emailCustomizer['settingsGroup3Option3'];?>" />
			<input name="emailCustomizer[settingsGroup4Option2]" id="settingsGroup4Option2hidden" class="settingsGroup4Option2hidden" type="hidden" value="<?php echo $emailCustomizer['settingsGroup4Option2'];?>" />
			<input name="emailCustomizer[settingsGroup4Option3]" id="settingsGroup4Option3hidden" class="settingsGroup4Option3hidden" type="hidden" value="<?php echo $emailCustomizer['settingsGroup4Option3'];?>" />
			<input name="emailCustomizer[settingsGroup2Option2]" id="settingsGroup2Option2hidden" class="settingsGroup2Option2hidden" type="hidden" value="<?php echo $emailCustomizer['settingsGroup2Option2'];?>" />
			<input name="emailCustomizer[settingsGroup2Option3]" id="settingsGroup2Option3hidden" class="settingsGroup2Option3hidden" type="hidden" value="<?php echo $emailCustomizer['settingsGroup2Option3'];?>" />
			<input name="emailCustomizer[settingsGroup4Option6]" id="settingsGroup4Option6hidden" class="settingsGroup4Option6hidden" type="hidden" value="<?php echo $emailCustomizer['settingsGroup4Option6'];?>" />
			<input name="emailCustomizer[settingsGroup4Option7]" id="settingsGroup4Option7hidden" class="settingsGroup4Option7hidden" type="hidden" value="<?php echo $emailCustomizer['settingsGroup4Option7'];?>" />
			<!--END TOGGLE ELEMENt-->
		</div>
		<!--END WRAP ELEMENT-->
	
		<div class="TableHeader downFooter">
			<span>Email Message Type</span>
			<span class="right">Show / Hide</span>
		</div>
	
	<div class="right">
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
		</p>
	</form>
	</div>
	
	<script type="text/javascript">
		
		// SLIDE/TOGGLE
		
		jQuery(function(){
			jQuery('#wppb_emailCustomizerAdmin .toggleElement').css('display','none');
		})
		
		jQuery("#wppb_emailCustomizerAdmin  .emailCustomizer .labelAction .button_emailCustomizer").click(function() {
			jQuery(this).parent().siblings('.toggleElement').toggle(500,'linear',function(){
				if(jQuery(this).css('display') == 'none') {
					jQuery(this).siblings('.labelAction').addClass('blue');
					jQuery(this).siblings('.labelAction').removeClass('yellow');
					jQuery(this).siblings('.labelAction').children('a.button_emailCustomizer').html('Show');
				}
				else {
					jQuery(this).siblings('.labelAction').addClass('yellow');
					jQuery(this).siblings('.labelAction').removeClass('blue');
					jQuery(this).siblings('.labelAction').children('a.button_emailCustomizer').html('Hide');
				}
			});
		});
		
	</script>
		
<?php
}

function decodeRepetitiveDefaultExtraFields ($userID, $message, $case, $extra1=''){

	if ($case === 1){
		$firstName	= get_user_meta($userID, 'first_name', true);
		$lastName	= get_user_meta($userID, 'last_name', true);
		$nickName	= get_user_meta($userID, 'nickname', true);
		$bio		= get_user_meta($userID, 'description', true);
		$aim		= get_user_meta($userID, 'aim', true);
		$yim		= get_user_meta($userID, 'yim', true);
		$jabber		= get_user_meta($userID, 'jabber', true);
		$url		= get_user_meta($userID, 'user_url', true);
		
		$user_info = get_userdata($userID);
		
		$message = str_replace( '%%first_name%%', $firstName, $message );					//Display the first name of the user
		$message = str_replace( '%%last_name%%', $lastName, $message );						//Display the last name of the user
		$message = str_replace( '%%nickname%%', $nickName, $message );						//Display the nickname of the user
		$message = str_replace( '%%description%%', $bio, $message );						//Display the description of the user
		$message = str_replace( '%%aim%%', $aim, $message );								//Display the aim of the user
		$message = str_replace( '%%yim%%', $yim, $message );								//Display the yim of the user
		$message = str_replace( '%%jabber%%', $jabber, $message );							//Display the jabber of the user
		$message = str_replace( '%%website%%', $user_info->user_url , $message );			//Display the users website
		
		$wppbFetchArray = get_option('wppb_custom_fields');
		foreach ( $wppbFetchArray as $key => $value){
			$metaValue = get_user_meta ($userID, $value['item_metaName'], true);
			$message = str_replace( '%%'.$value['item_metaName'].'%%', $metaValue, $message ); //Display any custom field
		}

	}elseif ($case === 2){																	//else for the email confirmation feature
		$extra1 = unserialize ( $extra1 );
		
		$password	= base64_decode($extra1['user_pass']);
		$firstName	= $extra1['first_name'];
		$lastName	= $extra1['last_name'];
		$nickName	= $extra1['nickname'];
		$bio		= $extra1['description'];
		$aim		= $extra1['aim'];
		$yim		= $extra1['yim'];
		$jabber		= $extra1['jabber'];
		$url		= $extra1['user_url'];
		
		$message = str_replace( '%%first_name%%', $firstName, $message );					//Display the first name of the user
		$message = str_replace( '%%last_name%%', $lastName, $message );						//Display the last name of the user
		$message = str_replace( '%%nickname%%', $nickName, $message );						//Display the nickname of the user
		$message = str_replace( '%%description%%', $bio, $message );						//Display the description of the user
		$message = str_replace( '%%aim%%', $aim, $message );								//Display the aim of the user
		$message = str_replace( '%%yim%%', $yim, $message );								//Display the yim of the user
		$message = str_replace( '%%jabber%%', $jabber, $message );							//Display the jabber of the user
		$message = str_replace( '%%website%%', $url , $message );							//Display the users website
		$message = str_replace( '%%password%%', $password, $message );						//Display the password
		
		$wppbFetchArray = get_option('wppb_custom_fields');
		foreach ( $wppbFetchArray as $key => $value){
			if (array_key_exists($wppbFetchArray[$key]['item_type'].$wppbFetchArray[$key]['id'], $extra1)) {
				$metaValue = $extra1[$value['item_type'].$value['id']];
				$message = str_replace( '%%'.$value['item_metaName'].'%%', $metaValue, $message ); //Display any custom field
			}
		}
	}	
		
	return $message;
}

function decodeECMergeTags($message, $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2){
	
	$wppbFetchArray = get_option('wppb_custom_fields');
	
	if ($function == 'approved_userFunction'){
		$userStatus = __('approved', 'profilebuilder');
		$message = str_replace( '%%user_status%%', $userStatus, $message );					//Display the userstatus (admin approval)
		$message = str_replace( '%%user_id%%', $userID, $message ); 						//Display the userID
		$message = str_replace( '%%username%%', $extraData1->user_login, $message );		//Display the username
		$message = str_replace( '%%user_email%%', $extraData1->user_email, $message );		//Display the email-address of the user
		
		$message = decodeRepetitiveDefaultExtraFields ($userID, $message, 1);

	}elseif ($function == 'unapproved_userFunction'){
		$userStatus = __('unapproved', 'profilebuilder');
		$message = str_replace( '%%user_status%%', $userStatus, $message );					//Display the userstatus (admin approval)
		$message = str_replace( '%%user_id%%', $userID, $message ); 						//Display the userID
		$message = str_replace( '%%username%%', $extraData1->user_login, $message );		//Display the username
		$message = str_replace( '%%user_email%%', $extraData1->user_email, $message );		//Display the email-address of the user
		
		$message = decodeRepetitiveDefaultExtraFields ($userID, $message, 1);
		
	}elseif ($function == 'register_w_o_admin_approval'){
		$user = get_user_by('email', $userEmail);
	
		$message = str_replace( '%%username%%', $userName, $message );						//Display the username
		$message = str_replace( '%%user_email%%', $userEmail, $message );					//Display the email-address of the user
		$message = str_replace( '%%password%%', $password, $message );						//Display the password
		$message = str_replace( '%%user_id%%', $user->ID, $message ); 						//Display the userID
				
		$message = decodeRepetitiveDefaultExtraFields ($user->ID, $message, 1);
		
	}elseif ($function == 'register_w_o_admin_approval_admin_email'){
		$user = get_user_by('email', $userEmail);
	
		$message = str_replace( '%%username%%', $userName, $message );						//Display the username
		$message = str_replace( '%%user_email%%', $userEmail, $message );					//Display the email-address of the user
		$message = str_replace( '%%password%%', $password, $message );						//Display the password
		$message = str_replace( '%%user_id%%', $user->ID, $message ); 						//Display the userID
		
		$message = decodeRepetitiveDefaultExtraFields ($user->ID, $message, 1);
		
	}elseif ($function == 'register_w_email_confirmation'){
		$user = get_user_by('email', $userEmail);
	
		$message = str_replace( '%%username%%', $userName, $message );						//Display the username
		$message = str_replace( '%%user_email%%', $userEmail, $message );					//Display the email-address of the user
		$message = str_replace( '%%activation_key%%', $extraData1, $message );				//Display the activation link
		$message = str_replace( '%%user_id%%', $user->ID, $message ); 						//Display the userID
		
		$message = decodeRepetitiveDefaultExtraFields ($user->ID, $message, 2, $extraData2);
	}

	$siteURL = get_bloginfo('url');
	$siteName = get_bloginfo('name');
	
	$message = str_replace( '%%site_url%%', $siteURL, $message ); 			//Display the site URL
	$message = str_replace( '%%site_name%%', $siteName, $message ); 		//Display the blog name
	
	
	return $message;
}


// function to send out emails (depending on the case, set by $function), and if needed overwrite it with the data storder in via email customizer
function wppb_mail($to, $subject, $message, $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2){

	//we add this filter to enable html encoding
	add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
	
	$wppb_addonOptions = get_option('wppb_addon_settings');
	
	if ($function == 'approved_userFunction'){
		if ($wppb_addonOptions['wppb_emailCustomizer'] == 'show'){
			$emailCustomizer = get_option('emailCustomizer');
			
			if (trim($emailCustomizer['settingsGroup4Option2']) != '')
				$subject = decodeECMergeTags(trim($emailCustomizer['settingsGroup4Option2']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
				
			if (trim($emailCustomizer['settingsGroup4Option3']) != '')					
				$message = decodeECMergeTags(trim($emailCustomizer['settingsGroup4Option3']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
		}
		
		$sent = wp_mail( $to , $subject, wpautop($message, true));

	}elseif ($function == 'unapproved_userFunction'){
		if ($wppb_addonOptions['wppb_emailCustomizer'] == 'show'){
			$emailCustomizer = get_option('emailCustomizer');
			
			if (trim($emailCustomizer['settingsGroup4Option6']) != '')
				$subject = decodeECMergeTags(trim($emailCustomizer['settingsGroup4Option6']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
				
			if (trim($emailCustomizer['settingsGroup4Option7']) != '')					
				$message = decodeECMergeTags(trim($emailCustomizer['settingsGroup4Option7']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
		}
			
		$sent = wp_mail( $to , $subject, wpautop($message, true));
		
	}elseif ($function == 'register_w_o_admin_approval'){
		if ($wppb_addonOptions['wppb_emailCustomizer'] == 'show'){
			$emailCustomizer = get_option('emailCustomizer');
			
			if ($extraData1 == 'yes'){
				if (trim($emailCustomizer['settingsGroup3Option2']) != '')
					$subject = decodeECMergeTags(trim($emailCustomizer['settingsGroup3Option2']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
					
				if (trim($emailCustomizer['settingsGroup3Option3']) != '')					
					$message = decodeECMergeTags(trim($emailCustomizer['settingsGroup3Option3']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
			
			}else{
				if (trim($emailCustomizer['settingsGroup1Option2']) != '')
					$subject = decodeECMergeTags(trim($emailCustomizer['settingsGroup1Option2']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
					
				if (trim($emailCustomizer['settingsGroup1Option3']) != '')					
					$message = decodeECMergeTags(trim($emailCustomizer['settingsGroup1Option3']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
			}
		}
			
		$sent = wp_mail( $to , $subject, wpautop($message, true));
		
	}elseif ($function == 'register_w_o_admin_approval_admin_email'){
		if ($wppb_addonOptions['wppb_emailCustomizerAdmin'] == 'show'){
			$emailCustomizer = get_option('emailCustomizer');
			
			if ($extraData1 == 'yes'){
				if (trim($emailCustomizer['admin_settingsGroup3Option2']) != '')
					$subject = decodeECMergeTags(trim($emailCustomizer['admin_settingsGroup3Option2']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
					
				if (trim($emailCustomizer['admin_settingsGroup3Option3']) != '')					
					$message = decodeECMergeTags(trim($emailCustomizer['admin_settingsGroup3Option3']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
			
			}else{
				if (trim($emailCustomizer['admin_settingsGroup1Option2']) != '')
				$subject = decodeECMergeTags(trim($emailCustomizer['admin_settingsGroup1Option2']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
					
				if (trim($emailCustomizer['admin_settingsGroup1Option3']) != '')					
					$message = decodeECMergeTags(trim($emailCustomizer['admin_settingsGroup1Option3']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
			}
		}
		
		$sent = wp_mail( $to , $subject, wpautop($message, true));
		
	}elseif ($function == 'register_w_email_confirmation'){
		if ($wppb_addonOptions['wppb_emailCustomizer'] == 'show'){
			$emailCustomizer = get_option('emailCustomizer');
			
			if (trim($emailCustomizer['settingsGroup2Option2']) != '')
				$subject = decodeECMergeTags(trim($emailCustomizer['settingsGroup2Option2']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
					
			if (trim($emailCustomizer['settingsGroup2Option3']) != '')					
				$message = decodeECMergeTags(trim($emailCustomizer['settingsGroup2Option3']), $blogInfo, $userID, $userName, $password, $userEmail, $function, $extraData1, $extraData2);
		}
				
		$sent = wp_mail( $to , $subject, wpautop($message, true));
		
	}else
		$sent = wp_mail( $to , $subject, wpautop($message, true));
		
	return $sent;
}