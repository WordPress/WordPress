<?php
/* <Register> */

require('b2config.php');
require($abspath.$b2inc.'/b2functions.php');

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

case "register":

	function filter($value)	{
		return ereg("^[a-zA-Z0-9\_-\|]+$",$value);
	}

	$user_login = $HTTP_POST_VARS["user_login"];
	$pass1 = $HTTP_POST_VARS["pass1"];
	$pass2 = $HTTP_POST_VARS["pass2"];
	$user_email = $HTTP_POST_VARS["user_email"];
	$user_login = $HTTP_POST_VARS["user_login"];

	/* declaring global fonctions */
#	global $user_login,$pass1,$pass2,$user_firstname,$user_nickname,$user_icq,$user_email,$user_url;
		
	/* checking login has been typed */
	if ($user_login=='') {
		die ("<b>ERROR</b>: please enter a Login");
	}

	/* checking the password has been typed twice */
	if ($pass1=='' ||$pass2=='') {
		die ("<b>ERROR</b>: please enter your password twice");
	}

	/* checking the password has been typed twice the same */
	if ($pass1!=$pass2)	{
		die ("<b>ERROR</b>: please type the same password in the two password fields");
	}
	$user_nickname=$user_login;

	/* checking e-mail address */
	if ($user_email=="") {
		die ("<b>ERROR</b>: please type your e-mail address");
	} else if (!is_email($user_email)) {
		die ("<b>ERROR</b>: the email address isn't correct");
	}

	$id=mysql_connect($server,$loginsql,$passsql);
	if ($id==false)	{
		die ("<b>OOPS</b>: can't connect to the server !".mysql_error());
	}

	mysql_select_db("$base") or die ("<b>OOPS</b>: can't select the database $base : ".mysql_error());

	/* checking the login isn't already used by another user */
	$request =  " SELECT user_login FROM $tableusers WHERE user_login = '$user_login'";
	$result = mysql_query($request,$id) or die ("<b>OOPS</b>: can't check the login...");
	$lines = mysql_num_rows($result);
	mysql_free_result($result);
	if ($lines>=1) {
		die ("<b>ERROR</b>: this login is already registered, please choose another one");
	}

	$user_ip = $HTTP_SERVER_VARS['REMOTE_ADDR'] ;
	$user_domain = gethostbyaddr($HTTP_SERVER_VARS['REMOTE_ADDR'] );
	$user_browser = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];

	$user_login=addslashes($user_login);
	$pass1=addslashes($pass1);
	$user_nickname=addslashes($user_nickname);

	$query = "INSERT INTO $tableusers (user_login, user_pass, user_nickname, user_email, user_ip, user_domain, user_browser, dateYMDhour, user_level, user_idmode) VALUES ('$user_login','$pass1','$user_nickname','$user_email','$user_ip','$user_domain','$user_browser',NOW(),'$new_users_can_blog','nickname')";
	$result = mysql_query($query);
	if ($result==false) {
		die ("<b>ERROR</b>: couldn't register you... please contact the <a href=\"mailto:$admin_email\">webmaster</a> !".mysql_error());
	}

	$stars="";
	for ($i = 0; $i < strlen($pass1); $i = $i + 1) {
		$stars .= "*";
	}

	$message  = "new user registration on your blog $blogname:\r\n\r\n";
	$message .= "login: $user_login\r\n\r\ne-mail: $user_email";

	@mail($admin_email,"new user registration on your blog $blogname",$message);

	?><html>
<head>
<title>b2 > Registration complete</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="<?php echo $b2inc; ?>/b2.css" type="text/css">
<style type="text/css">
<!--
<?php
if (!preg_match("/Nav/",$HTTP_USER_AGENT)) {
?>
textarea,input,select {
	background-color: #f0f0f0;
	border-width: 1px;
	border-color: #cccccc;
	border-style: solid;
	padding: 2px;
	margin: 1px;
}
<?php
}
?>
-->
</style>
</head>
<body bgcolor="#ffffff" text="#000000" link="#cccccc" vlink="#cccccc" alink="#ff0000">

<table width="100%" height="100%">
<td align="center" valign="middle">

<table width="200" height="200" style="border: 1px solid #cccccc;" cellpadding="0" cellspacing="0">

<tr height="50">
<td height="50" width="50">
<a href="http://wordpress.org" target="_blank"><img src="http://wordpress.org/images/wp-small.png" style="border:0" /></a>
</td>
<td class="b2menutop" align="center">
registration<br />complete
</td>
</tr>

<tr height="250"><td align="right" valign="bottom" height="150" colspan="2">

<table width="280">
<tr><td align="right" colspan="2">login: <b><?php echo $user_login ?>&nbsp;</b></td></tr>
<tr><td align="right" colspan="2">password: <b><?php echo $stars ?>&nbsp;</b></td></tr>
<tr><td align="right" colspan="2">e-mail: <b><?php echo $user_email ?>&nbsp;</b></td></tr>
<tr><td width="90">&nbsp;</td>
<td><form name="login" action="b2login.php" method="post">
<input type="hidden" name="log" value="<?php echo $user_login ?>" />
<input type="submit" class="search" value="Login" name="submit" /></form></td></tr>
</table>
</td>
</tr>
</table>

</td>
</tr>
</table>

</div>
</body>
</html>

	<?php
break;

case "disabled":

	?><html>
<head>
<title>b2 > Registration Currently Disabled</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="<?php echo $b2inc; ?>/b2.css" type="text/css">
<style type="text/css">
<!--
<?php
if (!preg_match("/Nav/",$HTTP_USER_AGENT)) {
?>
textarea,input,select {
	background-color: #f0f0f0;
	border-width: 1px;
	border-color: #cccccc;
	border-style: solid;
	padding: 2px;
	margin: 1px;
}
<?php
}
?>
-->
</style>
</head>
<body bgcolor="#ffffff" text="#000000" link="#cccccc" vlink="#cccccc" alink="#ff0000">

<table width="100%" height="100%">
<td align="center" valign="middle">

<table width="200" height="200" style="border: 1px solid #cccccc;" cellpadding="0" cellspacing="0">

<tr height="50">
<td height="50" width="50">
<a href="http://wordpress.org" target="_blank"><img src="http://wordpress.org/images/wp-small.png" /></a>
</td>
<td class="b2menutop" align="center">
registration disabled<br />
</td>
</tr>

<tr height="150">
<td align="center" valign="center" height="150" colspan="2">
<table width="80%" height="100%">
<tr><td class="b2menutop">
User registration is currently not allowed.<br />
<a href="<?php echo $siteurl.'/'.$blogfilename; ?>" >Home</a>
</td></tr></table>
</td>
</tr>
</table>

</td>
</tr>
</table>

</body>
</html>

	<?php
break;

default:

	?><html>
<head>
<title>b2 > Register form</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="<?php echo $b2inc; ?>/b2.css" type="text/css">
<style type="text/css">
<!--
<?php
if (!preg_match("/Nav/",$HTTP_USER_AGENT)) {
?>
textarea,input,select {
	background-color: #f0f0f0;
	border-width: 1px;
	border-color: #cccccc;
	border-style: solid;
	padding: 2px;
	margin: 1px;
}
<?php
}
?>
-->
</style>
</head>
<body bgcolor="#ffffff" text="#000000" link="#cccccc" vlink="#cccccc" alink="#ff0000">

<table width="100%" height="100%">
<td align="center" valign="middle">

<table width="250" height="250" style="border: 1px solid #cccccc;" cellpadding="0" cellspacing="0">

<tr>
<td>
<a href="http://wordpress.org"  title="visit WordPress dot org"  target="_blank"><img src="http://wordpress.org/images/wp-small.png" alt="visit WordPress dot org" style="border:0;" /></a>
</td>
<td class="b2menutop" align="center">
registration<br />
</td>
</tr>

<tr height="150"><td align="right" valign="bottom" height="150" colspan="2">

<form method="post" action="b2register.php">
<input type="hidden" name="action" value="register" />
<table border="0" width="180" class="menutop" style="background-color: #ffffff">
<tr> 
<td width="150" align="right">login</td>
<td>
<input type="text" name="user_login" size="8" maxlength="20" />
</td>
</tr>
<tr> 
<td align="right">password<br />(twice)</td>
<td> 
<input type="password" name="pass1" size="8" maxlength="100" />
<br />
<input type="password" name="pass2" size="8" maxlength="100" />
</td>
</tr>
<tr> 
<td align="right">e-mail</td>
<td>
<input type="text" name="user_email" size="8" maxlength="100" />
</td>
</tr>
<tr> 
<td>&nbsp;</td>
<td><input type="submit" value="OK" class="search" name="submit">
</td>
</tr>
</table>

</form>

</td>
</tr>
</table>

</td>
</tr>
</table>

</body>
</html>
	<?php

break;
}