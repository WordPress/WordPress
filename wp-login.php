<?php
require( dirname(__FILE__) . '/wp-config.php' );

$action = $_REQUEST['action'];
$error = '';

nocache_headers();

header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));

if ( defined('RELOCATE') ) { // Move flag is set
	if ( isset( $_SERVER['PATH_INFO'] ) && ($_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF']) )
		$_SERVER['PHP_SELF'] = str_replace( $_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF'] );

	$schema = ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
	if ( dirname($schema . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) != get_settings('siteurl') )
		update_option('siteurl', dirname($schema . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) );
}

switch($action) {

case 'logout':

	wp_clearcookie();
	do_action('wp_logout');
	nocache_headers();

	$redirect_to = 'wp-login.php';
	if ( isset($_REQUEST['redirect_to']) )
		$redirect_to = $_REQUEST['redirect_to'];
			
	wp_redirect($redirect_to);
	exit();

break;

case 'lostpassword':
do_action('lost_password');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &raquo; <?php _e('Lost Password') ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<link rel="stylesheet" href="<?php echo get_settings('siteurl'); ?>/wp-admin/wp-admin.css" type="text/css" />
	<script type="text/javascript">
	function focusit() {
		// focus on first input field
		document.getElementById('user_login').focus();
	}
	window.onload = focusit;
	</script>
	<style type="text/css">
	#user_login, #email, #submit {
		font-size: 1.7em;
	}
	</style>
</head>
<body>
<div id="login">
<h1><a href="http://wordpress.org/">WordPress</a></h1>
<p><?php _e('Please enter your information here. We will send you a new password.') ?></p>
<?php
if ($error)
	echo "<div id='login_error'>$error</div>";
?>

<form name="lostpass" action="wp-login.php" method="post" id="lostpass">
<p>
<input type="hidden" name="action" value="retrievepassword" />
<label><?php _e('Username:') ?><br />
<input type="text" name="user_login" id="user_login" value="" size="20" tabindex="1" /></label></p>
<p><label><?php _e('E-mail:') ?><br />
<input type="text" name="email" id="email" value="" size="25" tabindex="2" /></label><br />
</p>
<p class="submit"><input type="submit" name="submit" id="submit" value="<?php _e('Retrieve Password'); ?> &raquo;" tabindex="3" /></p>
</form>
<ul>
	<li><a href="<?php bloginfo('home'); ?>/" title="<?php _e('Are you lost?') ?>">&laquo; <?php _e('Back to blog') ?></a></li>
<?php if (get_settings('users_can_register')) : ?>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-register.php"><?php _e('Register') ?></a></li>
<?php endif; ?>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php"><?php _e('Login') ?></a></li>
</ul>
</div>
</body>
</html>
<?php
break;

case 'retrievepassword':
	$user_data = get_userdatabylogin($_POST['user_login']);
	// redefining user_login ensures we return the right case in the email
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;

	if (!$user_email || $user_email != $_POST['email'])
		die(sprintf(__('Sorry, that user does not seem to exist in our database. Perhaps you have the wrong username or e-mail address? <a href="%s">Try again</a>.'), 'wp-login.php?action=lostpassword'));

do_action('retreive_password', $user_login);  // Misspelled and deprecated.
do_action('retrieve_password', $user_login);

	// Generate something random for a password... md5'ing current time with a rand salt
	$key = substr( md5( uniqid( microtime() ) ), 0, 50);
	// now insert the new pass md5'd into the db
 	$wpdb->query("UPDATE $wpdb->users SET user_activation_key = '$key' WHERE user_login = '$user_login'");
	$message = __('Someone has asked to reset the password for the following site and username.') . "\r\n\r\n";
	$message .= get_option('siteurl') . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= __('To reset your password visit the following address, otherwise just ignore this email and nothing will happen.') . "\r\n\r\n";
	$message .= get_settings('siteurl') . "/wp-login.php?action=resetpass&key=$key\r\n";

	$m = wp_mail($user_email, sprintf(__('[%s] Password Reset'), get_settings('blogname')), $message);

	if ($m == false) {
		 echo '<p>' . __('The e-mail could not be sent.') . "<br />\n";
         echo  __('Possible reason: your host may have disabled the mail() function...') . "</p>";
		die();
	} else {
		echo '<p>' .  sprintf(__("The e-mail was sent successfully to %s's e-mail address."), $user_login) . '<br />';
		echo  "<a href='wp-login.php' title='" . __('Check your e-mail first, of course') . "'>" . __('Click here to login!') . '</a></p>';
		die();
	}

break;

case 'resetpass' :

	// Generate something random for a password... md5'ing current time with a rand salt
	$key = preg_replace('/a-z0-9/i', '', $_GET['key']);
	if ( empty($key) )
		die( __('Sorry, that key does not appear to be valid.') );
	$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_activation_key = '$key'");
	if ( !$user )
		die( __('Sorry, that key does not appear to be valid.') );

	do_action('password_reset');

	$new_pass = substr( md5( uniqid( microtime() ) ), 0, 7);
 	$wpdb->query("UPDATE $wpdb->users SET user_pass = MD5('$new_pass'), user_activation_key = '' WHERE user_login = '$user->user_login'");
	wp_cache_delete($user->ID, 'users');
	wp_cache_delete($user->user_login, 'userlogins');	
	$message  = sprintf(__('Username: %s'), $user->user_login) . "\r\n";
	$message .= sprintf(__('Password: %s'), $new_pass) . "\r\n";
	$message .= get_settings('siteurl') . "/wp-login.php\r\n";

	$m = wp_mail($user->user_email, sprintf(__('[%s] Your new password'), get_settings('blogname')), $message);

	if ($m == false) {
		echo '<p>' . __('The e-mail could not be sent.') . "<br />\n";
		echo  __('Possible reason: your host may have disabled the mail() function...') . '</p>';
		die();
	} else {
		echo '<p>' .  sprintf(__('Your new password is in the mail.'), $user_login) . '<br />';
        echo  "<a href='wp-login.php' title='" . __('Check your e-mail first, of course') . "'>" . __('Click here to login!') . '</a></p>';
		// send a copy of password change notification to the admin
		$message = sprintf(__('Password Lost and Changed for user: %s'), $user->user_login) . "\r\n";
		wp_mail(get_settings('admin_email'), sprintf(__('[%s] Password Lost/Change'), get_settings('blogname')), $message);
		die();
	}
break;

case 'login' : 
default:

	$user_login = '';
	$user_pass = '';
	$using_cookie = false;
	if ( !isset( $_REQUEST['redirect_to'] ) )
		$redirect_to = 'wp-admin/';
	else
		$redirect_to = $_REQUEST['redirect_to'];

	if( $_POST ) {
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

	do_action('wp_authenticate', array(&$user_login, &$user_pass));

	if ( $user_login && $user_pass ) {
		$user = new WP_User(0, $user_login);
	
		// If the user can't edit posts, send them to their profile.
		if ( !$user->has_cap('edit_posts') && ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' ) )
			$redirect_to = get_settings('siteurl') . '/wp-admin/profile.php';
	
		if ( wp_login($user_login, $user_pass, $using_cookie) ) {
			if ( !$using_cookie )
				wp_setcookie($user_login, $user_pass, false, '', '', $rememberme);
			do_action('wp_login', $user_login);
			wp_redirect($redirect_to);
			exit;
		} else {
			if ( $using_cookie )			
				$error = __('Your session has expired.');
		}
	} else if ( $user_login || $user_pass ) {
		$error = __('<strong>Error</strong>: The password field is empty.');
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &rsaquo; <?php _e('Login') ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<link rel="stylesheet" href="<?php bloginfo('wpurl'); ?>/wp-admin/wp-admin.css" type="text/css" />
	<script type="text/javascript">
	function focusit() {
		document.getElementById('log').focus();
	}
	window.onload = focusit;
	</script>
</head>
<body>

<div id="login">
<h1><a href="http://wordpress.org/">WordPress</a></h1>
<?php
if ( $error )
	echo "<div id='login_error'>$error</div>";
?>

<form name="loginform" id="loginform" action="wp-login.php" method="post">
<p><label><?php _e('Username:') ?><br /><input type="text" name="log" id="log" value="<?php echo wp_specialchars(stripslashes($user_login), 1); ?>" size="20" tabindex="1" /></label></p>
<p><label><?php _e('Password:') ?><br /> <input type="password" name="pwd" id="pwd" value="" size="20" tabindex="2" /></label></p>
<p>
  <label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="3" /> 
  <?php _e('Remember me'); ?></label></p>
<p class="submit">
	<input type="submit" name="submit" id="submit" value="<?php _e('Login'); ?> &raquo;" tabindex="4" />
	<input type="hidden" name="redirect_to" value="<?php echo wp_specialchars($redirect_to); ?>" />
</p>
</form>
<ul>
	<li><a href="<?php bloginfo('home'); ?>/" title="<?php _e('Are you lost?') ?>">&laquo; <?php _e('Back to blog') ?></a></li>
<?php if (get_settings('users_can_register')) : ?>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-register.php"><?php _e('Register') ?></a></li>
<?php endif; ?>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php?action=lostpassword" title="<?php _e('Password Lost and Found') ?>"><?php _e('Lost your password?') ?></a></li>
</ul>
</div>

</body>
</html>
<?php

break;
} // end action switch
?>
