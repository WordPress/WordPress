<?php
require(dirname(__FILE__) . '/wp-config.php');

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

$wpvarstoreset = array('action','mode','error','text','popupurl','popuptitle');

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

switch($action) {

case 'logout':

    setcookie('wordpressuser_'.$cookiehash, " ", time() - 31536000, COOKIEPATH);
    setcookie('wordpresspass_'.$cookiehash, " ", time() - 31536000, COOKIEPATH);
	header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	if ($is_IIS) {
		header('Refresh: 0;url=wp-login.php');
	} else {
		header('Location: wp-login.php');
	}
	exit();

break;

case 'login':

	if(!empty($_POST)) {
		$log = $_POST['log'];
		$pwd = $_POST['pwd'];
		$redirect_to = $_POST['redirect_to'];
	}
	
	$user = get_userdatabylogin($log);
	
	if (0 == $user->user_level) {
		$redirect_to = get_settings('siteurl') . '/wp-admin/profile.php';
	}

	function login() {
		global $wpdb, $log, $pwd, $error, $user_ID;
		global $pass_is_md5;
		$user_login = &$log;
		$pwd = md5($pwd);
		$password = &$pwd;
		if (!$user_login) {
			$error = __('<strong>Error</strong>: the login field is empty.');
			return false;
		}

		if (!$password) {
			$error = __('<strong>Error</strong>: the password field is empty.');
			return false;
		}

		$query = "SELECT ID, user_login, user_pass FROM $wpdb->users WHERE user_login = '$user_login' AND user_pass = '$password'";
	
		$login = $wpdb->get_row($query);

		if (!$login) {
			$error = __('<strong>Error</strong>: wrong login or password.');
			$pwd = '';
			return false;
		} else {
		$user_ID = $login->ID;
			if (($pass_is_md5 == 0 && $login->user_login == $user_login && $login->user_pass == $password) || ($pass_is_md5 == 1 && $login->user_login == $user_login && $login->user_pass == md5($password))) {
				return true;
			} else {
				$error = __('<strong>Error</strong>: wrong login or password.');
				$pwd = '';
			return false;
			}
		}
	}

	if (!login()) {
		header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
	if ($is_IIS) {
		header('Refresh: 0;url=wp-login.php');
	} else {
		header('Location: wp-login.php');
	}
		exit();
	} else {
		$user_login = $log;
		$user_pass = $pwd;
		setcookie('wordpressuser_'.$cookiehash, $user_login, time() + 31536000, COOKIEPATH);
		setcookie('wordpresspass_'.$cookiehash, md5($user_pass), time() + 31536000, COOKIEPATH);

		header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');

		switch($mode) {
			case 'bookmarklet':
				$location = "wp-admin/bookmarklet.php?text=$text&popupurl=$popupurl&popuptitle=$popuptitle";
				break;
			case 'sidebar':
				$location = "wp-admin/sidebar.php?text=$text&popupurl=$popupurl&popuptitle=$popuptitle";
				break;
			case 'profile':
				$location = "wp-admin/profile.php?text=$text&popupurl=$popupurl&popuptitle=$popuptitle";
				break;
			default:
				$location = "$redirect_to";
				break;
		}

		if ($is_IIS) {
			header("Refresh: 0;url=$location");
		} else {
			header("Location: $location");
		}
	}

break;


case 'lostpassword':

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e('WordPress &raquo; Lost password ?') ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>" />
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
<p><?php _e('Please enter your information here. We will send you a new password.') ?> </p>
<?php
if ($error) echo "<div align=\"right\" style=\"padding:4px;\"><font color=\"#FF0000\">$error</font><br />&nbsp;</div>";
?>

<form name="" action="wp-login.php" method="post" id="lostpass">
<input type="hidden" name="action" value="retrievepassword" />
<label><?php _e('Login:') ?> <input type="text" name="user_login" id="user_login" value="" size="12" /></label><br />
<label><?php _e('E-mail:') ?> <input type="text" name="email" id="email" value="" size="12" /></label><br />
<input type="submit" name="Submit2" value="OK" class="search" />

</form>
</div>



</body>
</html>
	<?php

break;

case 'retrievepassword':

	$user_data = get_userdatabylogin($_POST["user_login"]);
	// redefining user_login ensures we return the right case in the email
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;

	if (!$user_email || $user_email != $_POST['email']) die(sprintf(__('Sorry, that user does not seem to exist in our database. Perhaps you have the wrong username or e-mail address? <a href="%s">Try again</a>.'), 'wp-login.php?action=lostpassword'));
 	// Generate something random for a password... md5'ing current time with a rand salt
    $user_pass = substr((MD5("time" . rand(1,16000))), 0, 6);
 	// now insert the new pass md5'd into the db
 	$wpdb->query("UPDATE $wpdb->users SET user_pass = MD5('$user_pass') WHERE user_login = '$user_login'");
	$message  = "Login: $user_login\r\n";
	$message .= "Password: $user_pass\r\n";
	$message .= 'Login at: ' . get_settings('siteurl') . '/wp-login.php';

	$m = mail($user_email, '[' . get_settings('blogname') . "] Your weblog's login/password", $message);

	if ($m == false) {
		 echo '<p>' . __('The e-mail could not be sent.') . "<br />\n";
         echo  __('Possible reason: your host may have disabled the mail() function...') . "</p>";
		die();
	} else {
		echo '<p>' .  sprintf(__("The e-mail was sent successfully to %s's e-mail address."), $user_login) . '<br />';
        echo  "<a href='wp-login.php' title='" . __('Check your e-mail first, of course') . "'>" . __('Click here to login!') . '</a></p>';
		// send a copy of password change notification to the admin
		mail(get_settings('admin_email'), sprintf(__('[%s] Password Lost/Change'), get_settings('blogname')), sprintf(__('Password Lost and Changed for user: %s'), $user_login));
		die();
	}

break;


default:

	if((!empty($_COOKIE['wordpressuser_'.$cookiehash])) && (!empty($_COOKIE['wordpresspass_'.$cookiehash]))) {
		$user_login = $_COOKIE['wordpressuser_'.$cookiehash];
		$user_pass_md5 = $_COOKIE['wordpresspass_'.$cookiehash];
	}

	function checklogin() {
		global $user_login, $user_pass_md5, $user_ID;

		$userdata = get_userdatabylogin($user_login);

		if ($user_pass_md5 != md5($userdata->user_pass)) {
			return false;
		} else {
			return true;
		}
	} 

	if ( !(checklogin()) ) {
		if (!empty($_COOKIE['wordpressuser_'.$cookiehash])) {
			$error="Error: wrong login/password"; //, or your session has expired.";
		}
	} else {
		header("Expires: Wed, 5 Jun 1979 23:41:00 GMT"); /* private joke: this is Michel's birthdate - though officially it's on the 6th, since he's GMT+1 :) */
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); /* different all the time */
		header("Cache-Control: no-cache, must-revalidate"); /* to cope with HTTP/1.1 */
		header("Pragma: no-cache");
		header("Location: wp-admin/");
		exit();
	}
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php printf(__('WordPress > %s > Login form'), htmlspecialchars(get_settings('blogname'))) ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>" />
	<link rel="stylesheet" href="<?php echo get_settings('siteurl'); ?>/wp-admin/wp-admin.css" type="text/css" />
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
<p>
	<a href="<?php echo get_settings('home'); ?>" title="<?php _e('Are you lost?') ?>"><?php _e('Back to blog?') ?></a><br />
<?php if (get_settings('users_can_register')) { ?>
	<a href="<?php echo get_settings('siteurl'); ?>/wp-register.php" title="<?php _e('Register to be an author') ?>"><?php _e('Register?') ?></a><br />
<?php } ?>
	<a href="<?php echo get_settings('siteurl'); ?>/wp-login.php?action=lostpassword" title="<?php _e('Password Lost and Found') ?>"><?php _e('Lost your password?') ?></a>
</p>

<?php
if ($error) echo "<div align=\"right\" style=\"padding:4px;\"><font color=\"#FF0000\">$error</font><br />&nbsp;</div>";
?>

<form name="login" id="loginform" action="wp-login.php" method="post">
<?php if ($mode=="bookmarklet") { ?>
	<input type="hidden" name="mode" value="<?php echo $mode ?>" />
	<input type="hidden" name="text" value="<?php echo $text ?>" />
	<input type="hidden" name="popupurl" value="<?php echo $popupurl ?>" />
	<input type="hidden" name="popuptitle" value="<?php echo $popuptitle ?>" />
<?php } ?>
<?php if (isset($_GET["redirect_to"])) { ?>
	<input type="hidden" name="redirect_to" value="<?php echo $_GET["redirect_to"] ?>" />
<?php } else { ?>
	<input type="hidden" name="redirect_to" value="wp-admin/" />
<?php } ?>
	<input type="hidden" name="action" value="login" />
	<label><?php _e('Login:') ?> <input type="text" name="log" id="log" value="" size="20" tabindex="1" /></label><br />
	<label><?php _e('Password:') ?> <input type="password" name="pwd" value="" size="20" tabindex="2" /></label><br />
	<input type="submit" name="Submit2" value="OK" class="search" tabindex="3" />
</form>

</div>

</body>
</html>
<?php

break;
} // end action switch
?>
