<?php
require('./wp-config.php');

if (!function_exists('add_magic_quotes')) {
	function add_magic_quotes($array) {
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$array[$k] = add_magic_quotes($v);
			} else {
				$array[$k] = addslashes($v);
			}
		}
		return $array;
	} 
}

if (!get_magic_quotes_gpc()) {
	$_GET    = add_magic_quotes($_GET);
	$_POST   = add_magic_quotes($_POST);
	$_COOKIE = add_magic_quotes($_COOKIE);
}

$wpvarstoreset = array('action');

for ($i = 0; $i < count($wpvarstoreset); $i = $i + 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($_POST["$wpvar"])) {
			if (empty($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}
$error = '';
// If someone has moved WordPress let's try to detect it
if ( dirname('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) != get_settings('siteurl') )
	update_option('siteurl', dirname('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) );

switch($action) {

case 'logout':

    setcookie('wordpressuser_' . COOKIEHASH, ' ', time() - 31536000, COOKIEPATH);
    setcookie('wordpresspass_' . COOKIEHASH, ' ', time() - 31536000, COOKIEPATH);
	header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	if ($is_IIS)
		header('Refresh: 0;url=wp-login.php');
	else
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
	echo "<div id='loginerror'>$error</div>";
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
	$user_pass = substr( MD5('time' . rand(1, 16000) ), 0, 6);
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

	if( !empty($_POST) ) {
		$log = $_POST['log'];
		$pwd = $_POST['pwd'];
		$redirect_to = preg_replace('|[^a-z/~.:_-]|i', '', $_POST['redirect_to']);
	}
	
	$user = get_userdatabylogin($log);
	
	if (0 == $user->user_level) {
		$redirect_to = get_settings('siteurl') . '/wp-admin/profile.php';
	}

	if ( !login($log, $pwd) ) {
		header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
	} else {
		$user_login = $log;
		$user_pass = md5($pwd);
		setcookie('wordpressuser_'.$cookiehash, $user_login, time() + 31536000, COOKIEPATH);
		setcookie('wordpresspass_'.$cookiehash, md5($user_pass), time() + 31536000, COOKIEPATH);

		header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');

		if ($is_IIS)
			header("Refresh: 0;url=$redirect_to");
		else
			header("Location: $redirect_to");
	}

	if( !empty($_COOKIE['wordpressuser_' . COOKIEHASH]) && !empty($_COOKIE['wordpresspass_' . COOKIEHASH]) ) {
		$user_login = $_COOKIE['wordpressuser_' . COOKIEHASH];
		$user_pass_md5 = $_COOKIE['wordpresspass_' . COOKIEHASH];
	}

	if ( !login($user_login, $user_pass_md5, true) ) {
		if ( !empty($_COOKIE['wordpressuser_' . COOKIEHASH]) )
			$error = 'Your session has expired.';
	} else {
		header('Expires: Wed, 5 Jun 1979 23:41:00 GMT'); // Michel's birthday
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Location: wp-admin/');
		exit();
	}
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &rsaquo; <?php _e('Login') ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
	<link rel="stylesheet" href="<?php bloginfo('wpurl'); ?>/wp-admin/wp-admin.css" type="text/css" />
	<script type="text/javascript">
	function focusit() {
		// focus on first input field
		document.getElementById('log').focus();
	}
	window.onload = focusit;
	</script>
</head>
<body>

<div id="login">
<h1><a href="http://wordpress.org/">WordPress</a></h1>
<?php
if ($error)
	echo "<div id='login_error'>$error</div>";
?>

<form name="loginform" id="loginform" action="wp-login.php?action=login" method="post">
<p><label><?php _e('Login') ?>: <input type="text" name="log" id="log" value="" size="20" tabindex="1" /></label></p>
<p><label><?php _e('Password') ?>: <input type="password" name="pwd" value="" size="20" tabindex="2" /></label></p>
<p class="submit"><input type="submit" name="submit" value="<?php _e('Login'); ?> &raquo;" tabindex="3" />
<?php if (isset($_GET["redirect_to"])) { ?>
	<input type="hidden" name="redirect_to" value="<?php echo $_GET["redirect_to"] ?>" />
<?php } else { ?>
	<input type="hidden" name="redirect_to" value="wp-admin/" />
<?php } ?>
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