<?php
require( dirname(__FILE__) . '/wp-config.php' );

$action = $_REQUEST['action'];
$error = '';

header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// If someone has moved WordPress let's try to detect it
if ( isset( $_SERVER['PATH_INFO'] ) && ($_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF']) )
	$_SERVER['PHP_SELF'] = str_replace( $_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF'] );

if ( dirname('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) != get_settings('siteurl') )
	update_option('siteurl', dirname('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) );

switch($action) {

case 'logout':

	wp_clearcookie();
	header('Expires: Mon, 11 Jan 1984 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	header('Pragma: no-cache');
	header('Location: wp-login.php');
	exit();

break;

case 'lostpassword':

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &raquo; <?php _e('Lost Password') ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
	<link rel="stylesheet" href="<?php echo get_settings('siteurl'); ?>/wp-admin/wp-admin.css" type="text/css" />
	<script type="text/javascript">
	function focusit() {
		// focus on first input field
		document.getElementById('user_login').focus();
	}
	window.onload = focusit;
	</script>
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
<label><?php _e('Login') ?>: <input type="text" name="user_login" id="user_login" value="" size="12" tabindex="1" /></label><br />
<label><?php _e('E-mail') ?>: <input type="text" name="email" id="email" value="" size="12" tabindex="2" /></label><br />
</p>
<p class="submit"><input type="submit" name="submit" value="<?php _e('Retrieve Password'); ?> &raquo;" tabindex="3" /></p>
</form>
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

	// Generate something random for a password... md5'ing current time with a rand salt
	$user_pass = substr(md5(uniqid(microtime())), 0, 6);
	// now insert the new pass md5'd into the db
 	$wpdb->query("UPDATE $wpdb->users SET user_pass = MD5('$user_pass') WHERE user_login = '$user_login'");
	$message  = __('Login') . ": $user_login\r\n";
	$message .= __('Password') . ": $user_pass\r\n";
	$message .= get_settings('siteurl') . '/wp-login.php';

	$m = wp_mail($user_email, sprintf(__("[%s] Your login and password"), get_settings('blogname')), $message);

	if ($m == false) {
		 echo '<p>' . __('The e-mail could not be sent.') . "<br />\n";
         echo  __('Possible reason: your host may have disabled the mail() function...') . "</p>";
		die();
	} else {
		echo '<p>' .  sprintf(__("The e-mail was sent successfully to %s's e-mail address."), $user_login) . '<br />';
        echo  "<a href='wp-login.php' title='" . __('Check your e-mail first, of course') . "'>" . __('Click here to login!') . '</a></p>';
		// send a copy of password change notification to the admin
		wp_mail(get_settings('admin_email'), sprintf(__('[%s] Password Lost/Change'), get_settings('blogname')), sprintf(__('Password Lost and Changed for user: %s'), $user_login));
		die();
	}

break;

case 'login' : 
default:

	$user_login = '';
	$user_pass = '';
	$redirect_to = 'wp-admin/';
	$using_cookie = false;

	if( !empty($_POST) ) {
		$user_login = $_POST['log'];
		$user_pass  = $_POST['pwd'];
		$redirect_to = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $_POST['redirect_to']);
	} elseif ( !empty($_COOKIE) ) {
		if (! empty($_COOKIE['wordpressuser_' . COOKIEHASH]) )
			$user_login = $_COOKIE['wordpressuser_' . COOKIEHASH];
		if (! empty($_COOKIE['wordpresspass_' . COOKIEHASH]) ) {
			$user_pass = $_COOKIE['wordpresspass_' . COOKIEHASH];
			$using_cookie = true;
		}
	}

	if ($user_login && $user_pass) {
		$user = get_userdatabylogin($user_login);
		if ( 0 == $user->user_level )
			$redirect_to = get_settings('siteurl') . '/wp-admin/profile.php';

		if ( wp_login($user_login, $user_pass, $using_cookie) ) {
			if (! $using_cookie) {
				wp_setcookie($user_login, $user_pass);
			}

			header("Location: $redirect_to");
			exit();
		} else {
			if ($using_cookie)			
				$error = __('Your session has expired.');
		}
	}
	if ( isset($_REQUEST['redirect_to']) )
		$redirect_to = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $_REQUEST['redirect_to']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &rsaquo; <?php _e('Login') ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
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
<p><label><?php _e('Login') ?>: <input type="text" name="log" id="log" value="" size="20" tabindex="1" /></label></p>
<p><label><?php _e('Password') ?>: <input type="password" name="pwd" value="" size="20" tabindex="2" /></label></p>
<p class="submit">
	<input type="submit" name="submit" value="<?php _e('Login'); ?> &raquo;" tabindex="3" />
	<input type="hidden" name="redirect_to" value="<?php echo $redirect_to; ?>" />
</p>
</form>
<ul>
	<li><a href="<?php bloginfo('home'); ?>" title="<?php _e('Are you lost?') ?>">&laquo; <?php _e('Back to blog') ?></a></li>
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