<?php
require( dirname(__FILE__) . '/wp-config.php' );

$action = $_REQUEST['action'];
$errors = array();

if ( isset($_GET['key']) )
	$action = 'resetpass';

nocache_headers();

header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));

if ( defined('RELOCATE') ) { // Move flag is set
	if ( isset( $_SERVER['PATH_INFO'] ) && ($_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF']) )
		$_SERVER['PHP_SELF'] = str_replace( $_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF'] );

	$schema = ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
	if ( dirname($schema . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) != get_option('siteurl') )
		update_option('siteurl', dirname($schema . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) );
}


// Rather than duplicating this HTML all over the place, we'll stick it in function
function login_header($title = 'Login', $message = '') {
	global $errors, $error, $wp_locale;

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<title><?php bloginfo('name'); ?> &rsaquo; <?php echo $title; ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<link rel="stylesheet" href="<?php bloginfo('wpurl'); ?>/wp-admin/wp-admin.css?version=<?php bloginfo('version'); ?>" type="text/css" />
<?php if ( ('rtl' == $wp_locale->text_direction) ) : ?>
	<link rel="stylesheet" href="<?php bloginfo('wpurl'); ?>/wp-admin/rtl.css?version=<?php bloginfo('version'); ?>" type="text/css" />
<?php endif; ?>
	<!--[if IE]><style type="text/css">#login h1 a { margin-top: 35px; } #login ul { padding-bottom: 65px; }</style><![endif]--><!-- Curse you, IE! -->
	<script type="text/javascript">
		function focusit() {
			document.getElementById('user_login').focus();
		}
		window.onload = focusit;
	</script>
<?php do_action('login_head'); ?>
</head>
<body>

<div id="login"><h1><a href="<?php echo apply_filters('login_headerurl', 'http://wordpress.org/'); ?>" title="<?php echo apply_filters('login_headertitle', __('Powered by WordPress')); ?>"><span class="hide"><?php bloginfo('name'); ?></span></a></h1>
<?php
	if ( !empty( $message ) ) echo apply_filters('login_message', $message) . "\n";

	// Incase a plugin uses $error rather than the $errors array
	if ( !empty( $error ) ) {
		$errors['error'] = $error;
		unset($error);
	}

	if ( !empty( $errors ) ) {
		if ( is_array( $errors ) ) {
			$newerrors = "\n";
			foreach ( $errors as $error ) $newerrors .= '	' . $error . "<br />\n";
			$errors = $newerrors;
		}

		echo '<div id="login_error">' . apply_filters('login_errors', $errors) . "</div>\n";
	}
} // End of login_header()


switch ($action) {

case 'logout' :

	wp_clearcookie();
	do_action('wp_logout');

	$redirect_to = 'wp-login.php?loggedout=true';
	if ( isset( $_REQUEST['redirect_to'] ) )
		$redirect_to = $_REQUEST['redirect_to'];

	wp_redirect($redirect_to);
	exit();

break;

case 'lostpassword' :
case 'retrievepassword' :
	$user_login = '';
	$user_pass = '';

	if ( $_POST ) {
		if ( empty( $_POST['user_login'] ) )
			$errors['user_login'] = __('<strong>ERROR</strong>: The username field is empty.');
		if ( empty( $_POST['user_email'] ) )
			$errors['user_email'] = __('<strong>ERROR</strong>: The e-mail field is empty.');

		do_action('lostpassword_post');
		
		if ( empty( $errors ) ) {
			$user_data = get_userdatabylogin(trim($_POST['user_login']));
			// redefining user_login ensures we return the right case in the email
			$user_login = $user_data->user_login;
			$user_email = $user_data->user_email;

			if (!$user_email || $user_email != $_POST['user_email']) {
				$errors['invalidcombo'] = __('<strong>ERROR</strong>: Invalid username / e-mail combination.');
			} else {
				do_action('retreive_password', $user_login);  // Misspelled and deprecated
				do_action('retrieve_password', $user_login);

				// Generate something random for a password... md5'ing current time with a rand salt
				$key = substr( md5( uniqid( microtime() ) ), 0, 8);
				// Now insert the new pass md5'd into the db
				$wpdb->query("UPDATE $wpdb->users SET user_activation_key = '$key' WHERE user_login = '$user_login'");
				$message = __('Someone has asked to reset the password for the following site and username.') . "\r\n\r\n";
				$message .= get_option('siteurl') . "\r\n\r\n";
				$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
				$message .= __('To reset your password visit the following address, otherwise just ignore this email and nothing will happen.') . "\r\n\r\n";
				$message .= get_option('siteurl') . "/wp-login.php?action=rp&key=$key\r\n";

				if (FALSE == wp_mail($user_email, sprintf(__('[%s] Password Reset'), get_option('blogname')), $message)) {
					die('<p>' . __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') . '</p>');
				} else {
					wp_redirect('wp-login.php?checkemail=confirm');
					exit();
				}
			}
		}
	}

	if ( 'invalidkey' == $_GET['error'] ) $errors['invalidkey'] = __('Sorry, that key does not appear to be valid.');

	do_action('lost_password');
	login_header(__('Lost Password'), '<p class="message">' . __('Please enter your username and e-mail address. You will receive a new password via e-mail.') . '</p>');
?>

<form name="lostpasswordform" id="lostpasswordform" action="wp-login.php?action=lostpassword" method="post">
	<p>
		<label><?php _e('Username:') ?><br />
		<input type="text" name="user_login" id="user_login" class="input" value="<?php echo wp_specialchars(stripslashes($_POST['user_login']), 1); ?>" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label><?php _e('E-mail:') ?><br />
		<input type="text" name="user_email" id="user_email" class="input" value="<?php echo wp_specialchars(stripslashes($_POST['user_email']), 1); ?>" size="25" tabindex="20" /></label>
	</p>
<?php do_action('lostpassword_form'); ?>
	<p class="submit"><input type="submit" name="submit" id="submit" value="<?php _e('Get New Password &raquo;'); ?>" tabindex="100" /></p>
</form>
<ul>
<?php if (get_option('users_can_register')) : ?>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php"><?php _e('Login') ?></a></li>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php?action=register"><?php _e('Register') ?></a></li>
	<li><a href="<?php bloginfo('home'); ?>/" title="<?php _e('Are you lost?') ?>" class="fullwidth">&laquo; <?php _e('Back to blog') ?></a></li>
<?php else : ?>
	<li><a href="<?php bloginfo('home'); ?>/" title="<?php _e('Are you lost?') ?>">&laquo; <?php _e('Back to blog') ?></a></li>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php"><?php _e('Login') ?></a></li>
<?php endif; ?>
</ul>
</div>
</body>
</html>
<?php
break;

case 'resetpass' :
case 'rp' :
	$key = preg_replace('/[^a-z0-9]/i', '', $_GET['key']);
	if ( empty( $key ) ) {
		wp_redirect('wp-login.php?action=lostpassword&error=invalidkey');
		exit();
	}

	$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_activation_key = '$key'");
	if ( empty( $user ) ) {
		wp_redirect('wp-login.php?action=lostpassword&error=invalidkey');
		exit();
	}

	do_action('password_reset');

	// Generate something random for a password... md5'ing current time with a rand salt
	$new_pass = substr( md5( uniqid( microtime() ) ), 0, 7);
	$wpdb->query("UPDATE $wpdb->users SET user_pass = MD5('$new_pass'), user_activation_key = '' WHERE user_login = '$user->user_login'");
	wp_cache_delete($user->ID, 'users');
	wp_cache_delete($user->user_login, 'userlogins');
	$message  = sprintf(__('Username: %s'), $user->user_login) . "\r\n";
	$message .= sprintf(__('Password: %s'), $new_pass) . "\r\n";
	$message .= get_option('siteurl') . "/wp-login.php\r\n";

	if (FALSE == wp_mail($user->user_email, sprintf(__('[%s] Your new password'), get_option('blogname')), $message)) {
		die('<p>' . __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') . '</p>');
	} else {
		// send a copy of password change notification to the admin
		$message = sprintf(__('Password Lost and Changed for user: %s'), $user->user_login) . "\r\n";
		wp_mail(get_option('admin_email'), sprintf(__('[%s] Password Lost/Changed'), get_option('blogname')), $message);

		wp_redirect('wp-login.php?checkemail=newpass');
		exit();
	}
break;

case 'register' :
	if ( FALSE == get_option('users_can_register') ) {
		wp_redirect('wp-login.php?registration=disabled');
		exit();
	}

	if ( $_POST ) {
		require_once( ABSPATH . WPINC . '/registration.php');

		$user_login = sanitize_user( $_POST['user_login'] );
		$user_email = $_POST['user_email'];

		// Check the username
		if ( $user_login == '' )
			$errors['user_login'] = __('<strong>ERROR</strong>: Please enter a username.');
		elseif ( !validate_username( $user_login ) ) {
			$errors['user_login'] = __('<strong>ERROR</strong>: This username is invalid.  Please enter a valid username.');
			$user_login = '';
		} elseif ( username_exists( $user_login ) )
			$errors['user_login'] = __('<strong>ERROR</strong>: This username is already registered, please choose another one.');

		// Check the e-mail address
		if ($user_email == '') {
			$errors['user_email'] = __('<strong>ERROR</strong>: Please type your e-mail address.');
		} elseif ( !is_email( $user_email ) ) {
			$errors['user_email'] = __('<strong>ERROR</strong>: The email address isn&#8217;t correct.');
			$user_email = '';
		} elseif ( email_exists( $user_email ) )
			$errors['user_email'] = __('<strong>ERROR</strong>: This email is already registered, please choose another one.');

		do_action('register_post');

		if ( empty( $errors ) ) {
			$user_pass = substr( md5( uniqid( microtime() ) ), 0, 7);

			$user_id = wp_create_user( $user_login, $user_pass, $user_email );
			if ( !$user_id )
				$errors['registerfail'] = sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_option('admin_email'));
			else {
				wp_new_user_notification($user_id, $user_pass);

				wp_redirect('wp-login.php?checkemail=registered');
				exit();
			}
		}
	}

	login_header(__('Registration Form'), '<p class="message register">' . __('Register For This Site') . '</p>');
?>

<form name="registerform" id="registerform" action="wp-login.php?action=register" method="post">
	<p>
		<label><?php _e('Username:') ?><br />
		<input type="text" name="user_login" id="user_login" class="input" value="<?php echo wp_specialchars(stripslashes($user_login), 1); ?>" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label><?php _e('E-mail:') ?><br />
		<input type="text" name="user_email" id="user_email" class="input" value="<?php echo wp_specialchars(stripslashes($user_email), 1); ?>" size="25" tabindex="20" /></label>
	</p>
<?php do_action('register_form'); ?>
	<p id="reg_passmail"><?php _e('A password will be e-mailed to you.') ?></p>
	<p class="submit"><input type="submit" name="submit" id="submit" value="<?php _e('Register &raquo;'); ?>" tabindex="100" /></p>
</form>
<ul>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php"><?php _e('Login') ?></a></li>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php?action=lostpassword" title="<?php _e('Password Lost and Found') ?>"><?php _e('Lost your password?') ?></a></li>
	<li><a href="<?php bloginfo('home'); ?>/" title="<?php _e('Are you lost?') ?>" class="fullwidth">&laquo; <?php _e('Back to blog') ?></a></li>
</ul>
</div>
</body>
</html>
<?php
break;

case 'login' :
default:
	$user_login = '';
	$user_pass = '';
	$using_cookie = FALSE;

	if ( !isset( $_REQUEST['redirect_to'] ) )
		$redirect_to = 'wp-admin/';
	else
		$redirect_to = $_REQUEST['redirect_to'];

	if ( $_POST ) {
		$user_login = $_POST['log'];
		$user_login = sanitize_user( $user_login );
		$user_pass  = $_POST['pwd'];
		$rememberme = $_POST['rememberme'];
	} else {
		$cookie_login = wp_get_cookie_login();
		if ( ! empty($cookie_login) ) {
			$using_cookie = true;
			$user_login = $cookie_login['login'];
			$user_pass = $cookie_login['password'];
		}
	}

	do_action_ref_array('wp_authenticate', array(&$user_login, &$user_pass));

	if ( $user_login && $user_pass && empty( $errors ) ) {
		$user = new WP_User(0, $user_login);

		// If the user can't edit posts, send them to their profile.
		if ( !$user->has_cap('edit_posts') && ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' ) )
			$redirect_to = get_option('siteurl') . '/wp-admin/profile.php';

		if ( wp_login($user_login, $user_pass, $using_cookie) ) {
			if ( !$using_cookie )
				wp_setcookie($user_login, $user_pass, false, '', '', $rememberme);
			do_action('wp_login', $user_login);
			wp_redirect($redirect_to);
			exit();
		} else {
			if ( $using_cookie )
				$errors['expiredsession'] = __('Your session has expired.');
		}
	}
	
	if ( $_POST && empty( $user_login ) )
		$errors['user_login'] = __('<strong>ERROR</strong>: The username field is empty.');
	if ( $_POST && empty( $user_pass ) )
		$errors['user_pass'] = __('<strong>ERROR</strong>: The password field is empty.');

	// Some parts of this script use the main login form to display a message
	if		( TRUE == $_GET['loggedout'] )			$errors['loggedout']		= __('Successfully logged you out.');
	elseif	( 'disabled' == $_GET['registration'] )	$errors['registerdiabled']	= __('User registration is currently not allowed.');
	elseif	( 'confirm' == $_GET['checkemail'] )	$errors['confirm']			= __('Check your e-mail for the confirmation link.');
	elseif	( 'newpass' == $_GET['checkemail'] )	$errors['newpass']			= __('Check your e-mail for your new password.');
	elseif	( 'registered' == $_GET['checkemail'] )	$errors['registered']		= __('Registration complete. Please check your e-mail.');

	login_header(__('Login'));
?>

<form name="loginform" id="loginform" action="wp-login.php" method="post">
	<p>
		<label><?php _e('Username:') ?><br />
		<input type="text" name="log" id="user_login" class="input" value="<?php echo wp_specialchars(stripslashes($user_login), 1); ?>" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label><?php _e('Password:') ?><br />
		<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>
	</p>
<?php do_action('login_form'); ?>
	<p><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" /> <?php _e('Remember me'); ?></label></p>
	<p class="submit">
		<input type="submit" name="submit" id="submit" value="<?php _e('Login'); ?> &raquo;" tabindex="100" />
		<input type="hidden" name="redirect_to" value="<?php echo wp_specialchars($redirect_to); ?>" />
	</p>
</form>
<ul>
<?php if (get_option('users_can_register')) : ?>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php?action=register"><?php _e('Register') ?></a></li>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php?action=lostpassword" title="<?php _e('Password Lost and Found') ?>"><?php _e('Lost your password?') ?></a></li>
	<li><a href="<?php bloginfo('home'); ?>/" title="<?php _e('Are you lost?') ?>" class="fullwidth">&laquo; <?php _e('Back to blog') ?></a></li>
<?php else : ?>
	<li><a href="<?php bloginfo('home'); ?>/" title="<?php _e('Are you lost?') ?>">&laquo; <?php _e('Back to blog') ?></a></li>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php?action=lostpassword" title="<?php _e('Password Lost and Found') ?>"><?php _e('Lost your password?') ?></a></li>
<?php endif; ?>
</ul>
</div>

</body>
</html>
<?php

break;
} // end action switch
?>
