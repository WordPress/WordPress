<?php
/* <Register> */

require_once('wp-config.php');
require_once($abspath.$b2inc.'/b2functions.php');

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

if (!get_magic_quotes_gpc()) {
	$HTTP_GET_VARS    = add_magic_quotes($HTTP_GET_VARS);
	$HTTP_POST_VARS   = add_magic_quotes($HTTP_POST_VARS);
	$HTTP_COOKIE_VARS = add_magic_quotes($HTTP_COOKIE_VARS);
}

$b2varstoreset = array('action');
for ($i=0; $i<count($b2varstoreset); $i += 1) {
	$b2var = $b2varstoreset[$i];
	if (!isset($$b2var)) {
		if (empty($HTTP_POST_VARS["$b2var"])) {
			if (empty($HTTP_GET_VARS["$b2var"])) {
				$$b2var = '';
			} else {
				$$b2var = $HTTP_GET_VARS["$b2var"];
			}
		} else {
			$$b2var = $HTTP_POST_VARS["$b2var"];
		}
	}
}

if (!$users_can_register) {
	$action = 'disabled';
}

switch($action) {

case 'register':

	function filter($value)	{
		return ereg('^[a-zA-Z0-9\_-\|]+$',$value);
	}

	$user_login = $HTTP_POST_VARS['user_login'];
	$pass1 = $HTTP_POST_VARS['pass1'];
	$pass2 = $HTTP_POST_VARS['pass2'];
	$user_email = $HTTP_POST_VARS['user_email'];
	$user_login = $HTTP_POST_VARS['user_login'];
		
	/* checking login has been typed */
	if ($user_login == '') {
		die ('<strong>ERROR</strong>: Please enter a login.');
	}

	/* checking the password has been typed twice */
	if ($pass1 == '' || $pass2 == '') {
		die ('<strong>ERROR</strong>: Please enter your password twice.');
	}

	/* checking the password has been typed twice the same */
	if ($pass1 != $pass2)	{
		die ('<strong>ERROR</strong>: Please type the same password in the two password fields.');
	}
	$user_nickname = $user_login;

	/* checking e-mail address */
	if ($user_email == '') {
		die ('<strong>ERROR</strong>: Please type your e-mail address.');
	} else if (!is_email($user_email)) {
		die ('<strong>ERROR</strong>: The email address isn&#8217;t correct.');
	}

	/* checking the login isn't already used by another user */
	$result = $wpdb->get_results("SELECT user_login FROM $tableusers WHERE user_login = '$user_login'");
    ++$querycount;
    if (count($result) >= 1) {
		die ('<strong>ERROR</strong>: This login is already registered, please choose another one.');
	}

	$user_ip = $HTTP_SERVER_VARS['REMOTE_ADDR'] ;
	$user_domain = gethostbyaddr($HTTP_SERVER_VARS['REMOTE_ADDR'] );
	$user_browser = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];

	$user_login = addslashes($user_login);
	$pass1 = addslashes($pass1);
	$user_nickname = addslashes($user_nickname);

	$result = $wpdb->query("INSERT INTO $tableusers 
		(user_login, user_pass, user_nickname, user_email, user_ip, user_domain, user_browser, dateYMDhour, user_level, user_idmode)
	VALUES 
		('$user_login', '$pass1', '$user_nickname', '$user_email', '$user_ip', '$user_domain', '$user_browser', NOW(), '$new_users_can_blog', 'nickname')");
	
	if ($result == false) {
		die ('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:'.$admin_email.'">webmaster</a> !');
	}

	$stars = '';
	for ($i = 0; $i < strlen($pass1); $i = $i + 1) {
		$stars .= '*';
	}

	$message  = "New user registration on your blog $blogname:\r\n\r\n";
	$message .= "Login: $user_login\r\n\r\nE-mail: $user_email";

	@mail($admin_email, "[$blogname] New User Registration", $message);

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &raquo; Registration Complete</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />	
	<link rel="stylesheet" href="wp-admin/wp-admin.css" type="text/css" />
</head>
<body>

<div id="login"> 
	<h2>Registration Complete</h2>
	<p>Login: <strong><?php echo $user_login; ?></strong><br />
	Password: <strong><?php echo $stars; ?></strong><br />
	E-mail: <strong><?php echo $user_email; ?></strong></p>
	<form action="b2login.php" method="post" name="login">
		<input type="hidden" name="log" value="<?php echo $user_login; ?>" />
		<input type="submit" value="Login" name="submit" />
	</form>
</div>
</body>
</html>

	<?php
break;

case 'disabled':

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &raquo; Registration Currently Disabled</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="wp-admin/wp-admin.css" type="text/css">
</head>

<body>

<div id="login">
	<h2>Registration Disabled</h2>
	<p>User registration is currently not allowed.<br />
	<a href="<?php echo $siteurl.'/'.$blogfilename; ?>" title="Go back to the blog">Home</a>
	</p>
</div>

</body>
</html>

	<?php
break;

default:

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &raquo; Registration Form</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="wp-admin/wp-admin.css" type="text/css" />
</head>

<body>
<div id="login">
<h2>Registration</h2>

<form method="post" action="b2register.php">
	<input type="hidden" name="action" value="register" />
	<label for="user_login">Login:</label> <input type="text" name="user_login" id="user_login" size="10" maxlength="20" /><br />
	<label for="pass1">Password:</label> <input type="password" name="pass1" id="pass1" size="10" maxlength="100" /><br />
 
	<input type="password" name="pass2" size="10" maxlength="100" /><br />
	<label for="user_email">E-mail</label>: <input type="text" name="user_email" id="user_email" size="15" maxlength="100" /><br />
	<input type="submit" value="OK" class="search" name="submit" />
</form>
</div>

</body>
</html>
<?php

break;
}