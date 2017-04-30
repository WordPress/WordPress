<?php
/* wp_signon can only be executed before anything is outputed in the page because of that we're adding it to the init hook */
global $wppb_login; 
$wppb_login = false;

function wppb_signon(){	
	global $error;
	global $wppb_login;
	global $wpdb;
	
	$wppb_generalSettings = get_option('wppb_general_settings');

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' && wp_verify_nonce($_POST['login_nonce_field'],'verify_true_login') && ($_POST['formName'] == 'login') ){
		if (isset($_POST['remember-me']))
			$remember = $_POST['remember-me'];
		else $remember = false;
		
		$username = trim($_POST['user-name']);
		if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email')){
			// if this setting is active, the posted username is, in fact the user's email
			$result = mysql_query("SELECT user_login FROM $wpdb->users WHERE user_email='".$username."' LIMIT 1");
			$result=mysql_fetch_row($result);
			$username = $result[0];
		}
		
		$wppb_login = wp_signon( array( 'user_login' => $username, 'user_password' => trim($_POST['password']), 'remember' => trim($_POST['remember-me']) ), false );
		
	}elseif (isset($_GET['userName']) && isset($_GET['passWord'])){
		$remember = true;
		$username = trim($_GET['userName']);
		$password = base64_decode(trim($_GET['passWord']));
		
		if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email')){
			// if this setting is active, the posted username is, in fact the user's email
			$result = mysql_query("SELECT user_login FROM $wpdb->users WHERE user_email='".$username."' LIMIT 1");
			$result=mysql_fetch_row($result);
			$username = $result[0];
		}
		
		$wppb_login = wp_signon( array( 'user_login' => $username, 'user_password' => $password, 'remember' => $remember ), false );
	}
}
add_action('init', 'wppb_signon');

function wppb_front_end_login( $atts ){
	$loginFilterArray = array();
	ob_start();
	global $wppb_login;
	
	$wppb_generalSettings = get_option('wppb_general_settings');

	extract(shortcode_atts(array('display' => true, 'redirect' => '', 'submit' => 'page'), $atts));
	
	echo '<div class="wppb_holder" id="wppb_login"><div class="login-form">';
	
	if ( is_user_logged_in() ){  // Already logged in 
		global $user_ID; 
		$wppb_user = get_userdata( $user_ID );
		
		if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email'))
			$display_name = $wppb_user->user_email;
		elseif($wppb_user->display_name !== '')
			$display_name = $wppb_user->user_login;
		else
			$display_name = $wppb_user->display_name;
		
		$loginFilterArray['loginMessage1'] = '<p class="alert">'. sprintf(__('You are currently logged in as %1$s. %2$s', 'profilebuilder'), '<a href="'.$authorPostsUrl = get_author_posts_url( $wppb_user->ID ).'" title="'.$display_name.'">'.$display_name.'</a>', '<a href="'.wp_logout_url( $redirectTo = wppb_curpageurl() ).'" title="'. __('Log out of this account', 'profilebuilder').'">'. __('Log out', 'profilebuilder').' &raquo;</a>') . '</p><!-- .alert-->';
		echo $loginFilterArray['loginMessage1'] = apply_filters('wppb_login_login_message1', $loginFilterArray['loginMessage1'], $wppb_user->ID, $display_name);	
	
	}elseif ( isset($wppb_login->ID) ){ // Successful login
		if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email'))	
			$display_name = $wppb_login->user_email;
		elseif($wppb_login->display_name !== '')
			$display_name = $wppb_login->user_login;
		else
			$display_name = $wppb_login->display_name;

		$loginFilterArray['loginMessage2'] = '<p class="success">'. sprintf(__('You have successfully logged in as %1$s', 'profilebuilder'), '<a href="'.$authorPostsUrl = get_author_posts_url( $wppb_login->ID ).'" title="'.$display_name.'">'.$display_name.'</a>') . '</p><!-- .success-->';
		echo $loginFilterArray['loginMessage2'] = apply_filters('wppb_login_login_message2', $loginFilterArray['loginMessage2'], $wppb_login->ID, $display_name);
/*		echo "<pre>";
		print_r($wppb_login);
		echo $wppb_login->roles[0];
		die();*/

		

		
		if (isset($_POST['button']) && isset($_POST['formName']) ){
			if ($_POST['formName'] == 'login'){
				if ($_POST['button'] == 'page'){
					//$permaLnk2 = wppb_curpageurl();
					if($wppb_login->roles[0]=="promoter") {
					$permaLnk2 = home_url().'/promoter-profile';
					} else if($wppb_login->roles[0]=="artistband") {
					$permaLnk2 = home_url().'/artist-profile';
					}
					else if($wppb_login->roles[0]=="venue") {
					$permaLnk2 = home_url().'/venue-profile';
					} else {
					$permaLnk2 = home_url().'/user-profile';
					}
					//$permaLnk2 = home_url().'/user-profile';
				
					$wppb_addon_settings = get_option('wppb_addon_settings'); //fetch the descriptions array
					if ($wppb_addon_settings['wppb_customRedirect'] == 'show'){
						//check to see if the redirect location is not an empty string and is activated
						$customRedirectSettings = get_option('customRedirectSettings');
						if ((trim($customRedirectSettings['afterLoginTarget']) != '') && ($customRedirectSettings['afterLogin'] == 'yes')){
							$permaLnk2 = trim($customRedirectSettings['afterLoginTarget']);
							if (wppb_check_missing_http($permaLnk2))
								$permaLnk2 = 'http://'. $permaLnk2;
						}
					}
					
					$loginFilterArray['redirectMessage'] = '<font id="messageTextColor">' . sprintf(__('You will soon be redirected automatically. If you see this page for more than 1 second, please click %1$s', 'profilebuilder'), '<a href="'.$permaLnk2.'">'. __('here', 'profilebuilder').'</a>.<meta http-equiv="Refresh" content="1;url='.$permaLnk2.'" />') . '</font><br/><br/>';
					echo $loginFilterArray['redirectMessage'] = apply_filters('wppb_login_redirect_message', $loginFilterArray['redirectMessage'], $permaLnk2);

				}elseif($_POST['button'] == 'widget'){
					$permaLnk2 = wppb_curpageurl();
					if ($redirect != '')
						$permaLnk2 = trim($redirect);

						
					$loginFilterArray['widgetRedirectMessage'] = '<font id="messageTextColor">' . sprintf(__('You will soon be redirected automatically. If you see this page for more than 1 second, please click %1$s', 'profilebuilder'), '<a href="'.$permaLnk2.'">'. __('here', 'profilebuilder').'</a>.<meta http-equiv="Refresh" content="1;url='.$permaLnk2.'" />') . '</font><br/><br/>';
					wp_redirect($permaLnk2);
					exit;
					echo $loginFilterArray['widgetRedirectMessage'] = apply_filters('wppb_login_widget_redirect_message', $loginFilterArray['widgetRedirectMessage'], $permaLnk2);
					
				}
			}
		}
					
	}else{ // Not logged in

		if (!empty( $_POST['action'] ) && isset($_POST['formName']) ){
			if ($_POST['formName'] == 'login'){
		?>
				<p class="error" style="padding-bottom: 5px; font-size: 17px;">
					<?php 
					if (trim($_POST['user-name']) == ''){
						if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email')){
							$loginFilterArray['emptyUsernameError'] = '<strong>'. __('ERROR:','profilebuilder').'</strong> '. __('The email field is empty', 'profilebuilder').'.'; 
							$loginFilterArray['emptyUsernameError'] = apply_filters('wppb_login_empty_email_as_username_error_message', $loginFilterArray['emptyUsernameError']);
							
						}else{
							$loginFilterArray['emptyUsernameError'] = '<strong>'. __('ERROR:','profilebuilder').'</strong> '. __('The username field is empty', 'profilebuilder').'.'; 
							$loginFilterArray['emptyUsernameError'] = apply_filters('wppb_login_empty_username_error_message', $loginFilterArray['emptyUsernameError']);
						}
						
						echo $loginFilterArray['emptyUsernameError'];
						
					}elseif (trim($_POST['password']) == ''){
						$loginFilterArray['emptyPasswordError'] = '<strong>'. __('ERROR:','profilebuilder').'</strong> '. __('The password field is empty', 'profilebuilder').'.'; 
						$loginFilterArray['emptyPasswordError'] = apply_filters('wppb_login_empty_password_error_message', $loginFilterArray['emptyPasswordError']);
						
						echo $loginFilterArray['emptyPasswordError'];
					}
					
					if ( is_wp_error($wppb_login) ){
						$loginFilterArray['wpError'] = $wppb_login->get_error_message();
						$loginFilterArray['wpError'] = apply_filters('wppb_login_wp_error_message', $loginFilterArray['wpError'],$wppb_login);
						echo $loginFilterArray['wpError'];
					}
					?>
				</p><!-- .error -->
		<?php
			}
		} 
		
		/* use this action hook to add extra content before the login form. */
		do_action( 'wppb_before_login' );?>
		
		<form action="<?php wppb_curpageurl(); ?>" method="post" class="sign-in" name="loginForm">
		<?php
			if (isset($_POST['user-name']))
				$userName = esc_html( $_POST['user-name'] );
			else $userName = '';
			
			if (isset($wppb_generalSettings['loginWith']) && ($wppb_generalSettings['loginWith'] == 'email'))
				$loginWith = __('Email', 'profilebuilder');
			else
				$loginWith = __('Username', 'profilebuilder');
			
			$loginFilterArray['loginUsername'] = '
				<p class="login-form-username" style="padding-bottom: 5px; font-size: 17px;">
					'. $loginWith .'
				</p>	<input type="text" name="user-name" id="user-name" class="textboxt" value="'.$userName.'" />
				<!-- .form-username -->';
			$loginFilterArray['loginUsername'] = apply_filters('wppb_login_username', $loginFilterArray['loginUsername'], $userName);
			echo $loginFilterArray['loginUsername'];

			$loginFilterArray['loginPassword'] = '
				<p class="login-form-password" style="padding-bottom: 5px; font-size: 17px;">
					'. __('Password', 'profilebuilder') .'
					</p><input type="password" name="password" id="password" class="textboxt" />
				<!-- .form-password -->';
			$loginFilterArray['loginPassword'] = apply_filters('wppb_login_password', $loginFilterArray['loginPassword']);
			echo $loginFilterArray['loginPassword'];
		
			if ($display === true){
					$siteURL=get_option('siteurl').'/wp-login.php?action=lostpassword';
					$siteURL = apply_filters('wppb_pre_login_url_filter', $siteURL);
					$loginFilterArray['loginURL'] = '
						<p style="float:right; margin-right:36px;">
							<a href="'.$siteURL.'">'. __('Lost password?', 'profilebuilder').'</a>
						</p>';
					$loginFilterArray['loginURL'] = apply_filters('wppb_login_url', $loginFilterArray['loginURL'], $siteURL);
					echo $loginFilterArray['loginURL'];
				}
				
		?>
			<p class="login-form-submit">
				<input type="submit" name="submit" class="loginbtn" value="<?php _e('Log in', 'profilebuilder'); ?>" />
				<?php
					$loginFilterArray['rememberMe'] = '
						<input class="remember-me checkbox" name="remember-me" id="remember-me" type="checkbox" checked="checked" value="forever" />
						<label for="remember-me">'. __('Remember me', 'profilebuilder').'</label>';
					$loginFilterArray['rememberMe'] = apply_filters('wppb_login_remember_me', $loginFilterArray['rememberMe']);
				///	echo $loginFilterArray['rememberMe'];
				?>

				<input type="hidden" name="action" value="log-in" />
				<input type="hidden" name="button" value="<?php echo $submit;?>" />
				<input type="hidden" name="formName" value="login" />
			</p><!-- .form-submit -->
			<?php
			
			wp_nonce_field('verify_true_login','login_nonce_field'); ?>
		</form><!-- .sign-in -->

	<?php 
	}
	
	/* use this action hook to add extra content after the login form. */
	do_action( 'wppb_after_login' );?>
	
	</div>
	<?php
	$output = ob_get_contents();
    ob_end_clean();
		
	$loginFilterArray = apply_filters('wppb_login', $loginFilterArray);

    return $output;
}