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
	$HTTP_GET_VARS    = add_magic_quotes($HTTP_GET_VARS);
	$HTTP_POST_VARS   = add_magic_quotes($HTTP_POST_VARS);
	$HTTP_COOKIE_VARS = add_magic_quotes($HTTP_COOKIE_VARS);
}

$wpvarstoreset = array('action','mode','error','text','popupurl','popuptitle');

for ($i = 0; $i < count($wpvarstoreset); $i = $i + 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($HTTP_POST_VARS["$wpvar"])) {
			if (empty($HTTP_GET_VARS["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $HTTP_GET_VARS["$wpvar"];
			}
		} else {
			$$wpvar = $HTTP_POST_VARS["$wpvar"];
		}
	}
}

switch($action) {

case 'logout':

	setcookie('wordpressuser_'.$cookiehash);
	setcookie('wordpresspass_'.$cookiehash);
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

	if(!empty($HTTP_POST_VARS)) {
		$log = $HTTP_POST_VARS['log'];
		$pwd = $HTTP_POST_VARS['pwd'];
		$redirect_to = $HTTP_POST_VARS['redirect_to'];
	}
	
	$user = get_userdatabylogin($log);
	
	if (0 == $user->user_level) {
		$redirect_to = $site . '/wp-admin/profile.php';
	}

	function login() {
		global $wpdb, $log, $pwd, $error, $user_ID;
		global $tableusers, $pass_is_md5;
		$user_login = &$log;
		$pwd = md5($pwd);
		$password = &$pwd;
		if (!$user_login) {
			$error = '<strong>Error</strong>: the login field is empty.';
			return false;
		}

		if (!$password) {
			$error = '<strong>Error</strong>: the password field is empty.';
			return false;
		}

		$query = "SELECT ID, user_login, user_pass FROM $tableusers WHERE user_login = '$user_login' AND user_pass = '$password'";
	
		$login = $wpdb->get_row($query);

		if (!$login) {
			$error = '<strong>Error</strong>: wrong login or password.';
			$pwd = '';
			return false;
		} else {
		$user_ID = $login->ID;
			if (($pass_is_md5 == 0 && $login->user_login == $user_login && $login->user_pass == $password) || ($pass_is_md5 == 1 && $login->user_login == $user_login && $login->user_pass == md5($password))) {
				return true;
			} else {
				$error = '<strong>Error</strong>: wrong login or password.';
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
		setcookie('wordpressuser_'.$cookiehash, $user_login, time()+31536000);
		setcookie('wordpresspass_'.$cookiehash, md5($user_pass), time()+31536000);
		if (empty($HTTP_COOKIE_VARS['wordpressblogid_'.$cookiehash])) {
			setcookie('wordpressblogid_'.$cookiehash, 1,time()+31536000);
		}
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
	<title>WordPress &rsaquo; Lost password ?</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $blog_charset; ?>" />
	<link rel="stylesheet" href="<?php echo $siteurl; ?>/wp-admin/wp-admin.css" type="text/css" />
	<script type="text/javascript">
	function focusit() {
		// focus on first input field
		document.lostpass.user_login.focus();
	}
	window.onload = focusit;
	</script>
</head>
<body>


<div id="login">
<p>Please enter your information here. We will send you a new password. </p>
<?php
if ($error) echo "<div align=\"right\" style=\"padding:4px;\"><font color=\"#FF0000\">$error</font><br />&nbsp;</div>";
?>

<form name="" action="wp-login.php" method="post" id="lostpass">
<input type="hidden" name="action" value="retrievepassword" />
<label>Login: <input type="text" name="user_login" id="user_login" value="" size="12" /></label><br />
<label>Email: <input type="text" name="email" id="email" value="" size="12" /></label><br />
<input type="submit" name="Submit2" value="OK" class="search">

</form>
</div>



</body>
</html>
	<?php

break;

case 'retrievepassword':

	$user_login = $HTTP_POST_VARS["user_login"];
	$user_data = get_userdatabylogin($user_login);
	$user_email = $user_data->user_email;

	if (!$user_email || $user_email != $_POST['email']) die('Sorry, that user does not seem to exist in our database. Perhaps you have the wrong username or email address? <a href="wp-login.php?action=lostpassword">Try again</a>.');
 	// Generate something random for a password... md5'ing current time with a rand salt
    $user_pass = substr((MD5("time" . rand(1,16000))), 0, 6);
 	// now insert the new pass md5'd into the db
 	$wpdb->query("UPDATE $tableusers SET user_pass = MD5('$user_pass') WHERE user_login = '$user_login'");
	$message  = "Login: $user_login\r\n";
	$message .= "Password: $user_pass\r\n";
	$message .= "Login at: $siteurl/wp-login.php";

	$m = mail($user_email, "[$blogname] Your weblog's login/password", $message);

	if ($m == false) {
		echo "<p>The email could not be sent.<br />\n";
		echo "Possible reason: your host may have disabled the mail() function...</p>";
		die();
	} else {
		echo "<p>The email was sent successfully to $user_login's email address.<br />
		<a href='wp-login.php' title='Check your email first, of course'>Click here to login!</a></p>";
		// send a copy of password change notification to the admin
		mail($admin_email, "[$blogname] Password Lost/Change", "Password Lost and Changed for user: $user_login");
		die();
	}

break;


default:

	if((!empty($HTTP_COOKIE_VARS['wordpressuser_'.$cookiehash])) && (!empty($HTTP_COOKIE_VARS['wordpresspass_'.$cookiehash]))) {
		$user_login = $HTTP_COOKIE_VARS['wordpressuser_'.$cookiehash];
		$user_pass_md5 = $HTTP_COOKIE_VARS['wordpresspass_'.$cookiehash];
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
		if (!empty($HTTP_COOKIE_VARS['wordpressuser_'.$cookiehash])) {
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
	<title>WordPress > <?php bloginfo('name') ?> > Login form</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $blog_charset; ?>" />
	<link rel="stylesheet" href="<?php echo $siteurl; ?>/wp-admin/wp-admin.css" type="text/css" />
	<script type="text/javascript">
	function focusit() {
		// focus on first input field
		document.loginform.log.focus();
	}
	window.onload = focusit;
	</script>
</head>
<body>

<div id="login">
<p>
	<a href="<?php echo $siteurl?>" title="Are you lost?">Back to blog?</a><br />
<?php if ($users_can_register) { ?>
	<a href="<?php echo $siteurl; ?>/wp-register.php" title="Register to be an author">Register?</a><br />
<?php } ?>
	<a href="<?php echo $siteurl; ?>/wp-login.php?action=lostpassword" title="Password Lost and Found">Lost your password?</a>
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
<?php if (isset($HTTP_GET_VARS["redirect_to"])) { ?>
	<input type="hidden" name="redirect_to" value="<?php echo $HTTP_GET_VARS["redirect_to"] ?>" />
<?php } else { ?>
	<input type="hidden" name="redirect_to" value="wp-admin/" />
<?php } ?>
	<input type="hidden" name="action" value="login" />
	<label>Login: <input type="text" name="log" id="log" value="" size="20" tabindex="1" /></label><br />
	<label>Password: <input type="password" name="pwd" value="" size="20" tabindex="2" /></label><br />
	<input type="submit" name="Submit2" value="OK" class="search" tabindex="3" />
</form>

</div>

</body>
</html>
<?php

break;
} // end action switch
?>
