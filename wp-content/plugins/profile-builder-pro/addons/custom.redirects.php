<?php
function wppb_customRedirect(){
	//first thing we will have to do is create a default settings on first-time run of the addon
	$customRedirectSettings = get_option('customRedirectSettings','not_found');
		if ($customRedirectSettings == 'not_found'){
			$customRedirectSettingsArg = array( 'afterRegister' => 'no', 
												'afterLogin'=> 'no',
												'afterRegisterTarget' => '', 
												'afterLoginTarget'=> '',
												'loginRedirect' => 'no',
												'loginRedirectLogout' => 'no',
												'registerRedirect' => 'no',
												'recoverRedirect' => 'no',
												'dashboardRedirect' => 'no',
												'loginRedirectTarget' => '', 
												'loginRedirectTargetLogout' => '', 
												'registerRedirectTarget'=> '',
												'recoverRedirectTarget' => '', 
												'dashboardRedirectTarget' => '');
			add_option('customRedirectSettings', $customRedirectSettingsArg);
		}
?>
	
	<form method="post" action="options.php#wppb_customRedirect">
		<?php $customRedirectSettings = get_option('customRedirectSettings'); ?>
		<?php settings_fields('customRedirectSettings'); ?>

		
		
		<h2><?php _e('Custom Redirects', 'profilebuilder');?></h2>
		<h3><?php _e('Custom Redirects', 'profilebuilder');?></h3>


		<p>
			<?php _e('Redirects on custom page requests:', 'profilebuilder');?>
		</p>
		
		<table class="redirectTable">
			<thead class="disableLoginAndRegistrationTableHead">
				<tr>
					<th class="manage-column" scope="col"><?php _e('Action', 'profilebuilder');?></th>
					<th class="manage-column" scope="col"><?php _e('Redirect', 'profilebuilder');?></th>
					<th class="manage-column" scope="col"><?php _e('URL', 'profilebuilder');?></th>
				</tr>
			</thead>
			<tr class="redirectTableRow">
				<td class="redirectTableCell1"><?php _e('After Registration:', 'profilebuilder');?></td>
				<td>
					<input type="radio" name="customRedirectSettings[afterRegister]" value="yes" <?php if ($customRedirectSettings['afterRegister'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[afterRegister]" value="no" <?php if ($customRedirectSettings['afterRegister'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="redirectTableCell2"><input name="customRedirectSettings[afterRegisterTarget]" class="redirectFirstInput" type="text" value="<?php echo $customRedirectSettings['afterRegisterTarget'];?>" /></td>
			</tr>
			<tr class="redirectTableRow">
				<td class="redirectTableCell1"><?php _e('After Login:', 'profilebuilder');?></td>
				<td>
					<input type="radio" name="customRedirectSettings[afterLogin]" value="yes" <?php if ($customRedirectSettings['afterLogin'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[afterLogin]" value="no" <?php if ($customRedirectSettings['afterLogin'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="redirectTableCell2"><input name="customRedirectSettings[afterLoginTarget]" class="redirectSecondInput" type="text" value="<?php echo $customRedirectSettings['afterLoginTarget'];?>" /></td>
			</tr>
			<tr class="redirectTableRow">
				<td class="redirectTableCell1">
					<?php _e('Recover Password (*)', 'profilebuilder');?>
				</td>
				<td>
					<input type="radio" name="customRedirectSettings[recoverRedirect]" value="yes" <?php if ($customRedirectSettings['recoverRedirect'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[recoverRedirect]" value="no" <?php if ($customRedirectSettings['recoverRedirect'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="redirectTableCell2">
					<input name="customRedirectSettings[recoverRedirectTarget]" class="redirectThirdInput" type="text" value="<?php echo $customRedirectSettings['recoverRedirectTarget'];?>" />
				</td>
			</tr>
		</table>
		<?php echo '<font size="1" color="grey">(*) '.__('When activated this feature will redirect the user on both the default Wordpress password recovery page and the "Lost password?" link used by Profile Builder on the front-end login page.', 'profilebuilder').' </font>'; ?>
		
		<br/><br/><br/>
		
		<p>
			<?php _e('Redirects on default WordPress page requests:', 'profilebuilder');?>
		</p>
		
		<table class="disableLoginAndRegistrationTable">
			<thead class="disableLoginAndRegistrationTableHead">
				<tr>
					<th class="manage-column" scope="col"><?php _e('Requested WP Page', 'profilebuilder');?></th>
					<th class="manage-column" scope="col"><?php _e('Redirect', 'profilebuilder');?></th>
					<th class="manage-column" scope="col"><?php _e('URL', 'profilebuilder');?></th>
				</tr>
			</thead>
			<tr class="disableLoginAndRegistrationTableRow">
				<td class="disableLoginAndRegistrationTableCell1">
					<?php _e('Default WP Login Page (*)', 'profilebuilder');?>
				</td>
				<td class="disableLoginAndRegistrationTableCell2">
					<input type="radio" name="customRedirectSettings[loginRedirect]" value="yes" <?php if ($customRedirectSettings['loginRedirect'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[loginRedirect]" value="no" <?php if ($customRedirectSettings['loginRedirect'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="disableLoginAndRegistrationTableCell3">
					<input name="customRedirectSettings[loginRedirectTarget]" class="loginRedirectTarget" type="text" value="<?php echo $customRedirectSettings['loginRedirectTarget'];?>" />
				</td>
			</tr>
			<tr class="disableLoginAndRegistrationTableRow">
				<td class="disableLoginAndRegistrationTableCell1">
					<?php _e('Default WP Logout Page (**)', 'profilebuilder');?>
				</td>
				<td class="disableLoginAndRegistrationTableCell2">
					<input type="radio" name="customRedirectSettings[loginRedirectLogout]" value="yes" <?php if ($customRedirectSettings['loginRedirectLogout'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[loginRedirectLogout]" value="no" <?php if ($customRedirectSettings['loginRedirectLogout'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="disableLoginAndRegistrationTableCell3">
					<input name="customRedirectSettings[loginRedirectTargetLogout]" class="loginRedirectTarget" type="text" value="<?php echo $customRedirectSettings['loginRedirectTargetLogout'];?>" />
				</td>
			</tr>
			<tr class="disableLoginAndRegistrationTableRow">
				<td class="disableLoginAndRegistrationTableCell1">
					<?php _e('Default WP Register Page', 'profilebuilder');?>
				</td>
				<td class="disableLoginAndRegistrationTableCell2">
					<input type="radio" name="customRedirectSettings[registerRedirect]" value="yes" <?php if ($customRedirectSettings['registerRedirect'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[registerRedirect]" value="no" <?php if ($customRedirectSettings['registerRedirect'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="disableLoginAndRegistrationTableCell3">
					<input name="customRedirectSettings[registerRedirectTarget]" class="registerRedirectTarget" type="text" value="<?php echo $customRedirectSettings['registerRedirectTarget'];?>" />
				</td>
			</tr>
			<tr class="disableLoginAndRegistrationTableRow">
				<td class="disableLoginAndRegistrationTableCell1">
					<?php _e('Default WP Dashboard (***)', 'profilebuilder');?>
				</td>
				<td class="disableLoginAndRegistrationTableCell2">
					<input type="radio" name="customRedirectSettings[dashboardRedirect]" value="yes" <?php if ($customRedirectSettings['dashboardRedirect'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[dashboardRedirect]" value="no" <?php if ($customRedirectSettings['dashboardRedirect'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="disableLoginAndRegistrationTableCell3">
					<input name="customRedirectSettings[dashboardRedirectTarget]" class="dashboardRedirectTarget" type="text" value="<?php echo $customRedirectSettings['dashboardRedirectTarget'];?>" />
				</td>
			</tr>
		</table>
		<?php echo '<font size="1" color="grey">(*) '.__('Before login. Works best if used in conjuction with "After logout".', 'profilebuilder').' </font><br/>'; ?>
		<?php echo '<font size="1" color="grey">(**) '.__('After logout. Works best if used in conjuction with "Before login".', 'profilebuilder').' </font><br/>'; ?>
		<?php echo '<font size="1" color="grey">(***) '.__('Redirects every user-role EXCEPT the ones with administrator privilages (can manage options).', 'profilebuilder').' </font>'; ?>
	
	<div align="right">
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
		</p>
	</form>
	</div>
	
<?php	
}

// function to check if there is a need to add the http:// prefix
function wppb_check_missing_http($redirectLink) {

	return preg_match('#^(?:[a-z\d]+(?:-+[a-z\d]+)*\.)+[a-z]+(?::\d+)?(?:/|$)#i', $redirectLink);
}

//the function needed to block access to the admin-panel (if requested)
function wppb_restrict_dashboard_access(){
	$capabilities = apply_filters('wppb_redirect_capability', 'manage_options');
	
	if (!is_admin())
        return '';

	elseif ((is_admin()) && (!current_user_can( $capabilities ))){
			//check to see if the redirecting addon is present and activated
			$wppb_addon_settings = get_option('wppb_addon_settings');
			if ($wppb_addon_settings['wppb_customRedirect'] == 'show'){
		
			$customRedirectSettings = get_option('customRedirectSettings','not_found');
			if ($customRedirectSettings != 'not_found'){
				if (($customRedirectSettings['dashboardRedirect'] == 'yes') && (trim($customRedirectSettings['dashboardRedirectTarget']) != '')){
				
					$redirectLink = trim($customRedirectSettings['dashboardRedirectTarget']);
					
					if (wppb_check_missing_http($redirectLink))
						$redirectLink = 'http://'. $redirectLink;
					
					wp_redirect( $redirectLink );
					exit;

				}
			}
		}
	}
}
add_action('admin_menu','wppb_restrict_dashboard_access');


if (! is_admin()){
	$addonPresent = WPPB_PLUGIN_DIR . '/premium/addons/custom.redirects.php';
	if (file_exists($addonPresent)){
		//check to see if the redirecting addon is present and activated
		$wppb_addon_settings = get_option('wppb_addon_settings'); //fetch the descriptions array
		if ($wppb_addon_settings['wppb_customRedirect'] == 'show'){
			
			//get the currently loaded page
			global $pagenow;

			//the part for the WP register page
			if (($pagenow == 'wp-login.php') && (isset($_GET['action'])) && ($_GET['action'] == 'register')){
				$customRedirectSettings = get_option('customRedirectSettings','not_found');
				
				if ($customRedirectSettings != 'not_found'){
					if (($customRedirectSettings['registerRedirect'] == 'yes') && (trim($customRedirectSettings['registerRedirectTarget']) != '')){
						include ('wp-includes/pluggable.php');
						
						$redirectLink = trim($customRedirectSettings['registerRedirectTarget']);
						
						if (wppb_check_missing_http($redirectLink))
							$redirectLink = 'http://'. $redirectLink;
					
						wp_redirect( $redirectLink );
						
						exit;
					}
				}
			//the part for the WP password recovery
			}elseif (($pagenow == 'wp-login.php') && (isset($_GET['action'])) && ($_GET['action'] == 'lostpassword')){
				$customRedirectSettings = get_option('customRedirectSettings','not_found');
				
				if ($customRedirectSettings != 'not_found'){
					if (($customRedirectSettings['recoverRedirect'] == 'yes') && (trim($customRedirectSettings['recoverRedirectTarget']) != '')){
						include ('wp-includes/pluggable.php');
						
						$redirectLink = trim($customRedirectSettings['recoverRedirectTarget']);
						
						if (wppb_check_missing_http($redirectLink))
							$redirectLink = 'http://'. $redirectLink;
					
						wp_redirect( $redirectLink );
						
						exit;
					}
				}
			//the part for WP login; BEFORE login; this part only covers when the user isn't logged in and NOT when he just logged out
			}elseif ((($pagenow == 'wp-login.php') && (!isset($_GET['action'])) && (!isset($_GET['loggedout']))) || (isset($_GET['redirect_to']) && ($_GET['action'] != 'logout'))){
				$customRedirectSettings = get_option('customRedirectSettings','not_found');
				
				if ($customRedirectSettings != 'not_found'){
					if (($customRedirectSettings['loginRedirect'] == 'yes') && (trim($customRedirectSettings['loginRedirectTarget']) != '')){
						include ('wp-includes/pluggable.php');
						
						$redirectLink = trim($customRedirectSettings['loginRedirectTarget']);
						
						if (wppb_check_missing_http($redirectLink))
							$redirectLink = 'http://'. $redirectLink;
					
						wp_redirect( $redirectLink );
						
						exit;
					}
				}
			//the part for WP login; AFTER logout; this part only covers when the user was logged in and has logged out
			}elseif (($pagenow == 'wp-login.php') && (isset($_GET['loggedout'])) && ($_GET['loggedout'] == 'true')){
				$customRedirectSettings = get_option('customRedirectSettings','not_found');
				
				if ($customRedirectSettings != 'not_found'){
					if (($customRedirectSettings['loginRedirectLogout'] == 'yes') && (trim($customRedirectSettings['loginRedirectTargetLogout']) != '')){
						include ('wp-includes/pluggable.php');
						
						$redirectLink = trim($customRedirectSettings['loginRedirectTargetLogout']);					
						
						if (wppb_check_missing_http($redirectLink))
							$redirectLink = 'http://'. $redirectLink;
					
						wp_redirect( $redirectLink );
						
						exit;
					}
				}
				
			}
		}
	}
}