<?php
require('./wp-config.php');

$wpvarstoreset = array('action');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
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

if (!get_settings('users_can_register')) {
	$action = 'disabled';
}

switch($action) {

case 'register':

	$user_login = $_POST['user_login'];
	$user_email = $_POST['user_email'];
		
	/* checking that username has been typed */
	if ($user_login == '') {
		die (__('<strong>ERROR</strong>: Please enter a username.'));
	}

	/* checking e-mail address */
	if ($user_email == '') {
		die (__('<strong>ERROR</strong>: Please type your e-mail address.'));
	} else if (!is_email($user_email)) {
		die (__('<strong>ERROR</strong>: The email address isn&#8217;t correct.'));
	}

	/* checking the username isn't already used by another user */
	$result = $wpdb->get_results("SELECT user_login FROM $wpdb->users WHERE user_login = '$user_login'");
    if (count($result) >= 1) {
		die (__('<strong>ERROR</strong>: This username is already registered, please choose another one.'));
	}

	$user_ip = $_SERVER['REMOTE_ADDR'] ;

	$user_browser = $wpdb->escape($_SERVER['HTTP_USER_AGENT']);

	$user_login = $wpdb->escape( preg_replace('|a-z0-9 _.-|i', '', $user_login) );
	$user_nickname = $user_login;
   $user_nicename = sanitize_title($user_nickname);
	$now = gmdate('Y-m-d H:i:s');
	$user_level = get_settings('new_users_can_blog');
	$password = substr( md5( uniqid( microtime() ) ), 0, 7);

	$result = $wpdb->query("INSERT INTO $wpdb->users 
		(user_login, user_pass, user_nickname, user_email, user_ip, user_browser, user_registered, user_level, user_idmode, user_nicename)
	VALUES 
		('$user_login', MD5('$password'), '$user_nickname', '$user_email', '$user_ip', '$user_browser', '$now', '$user_level', 'nickname', '$user_nicename')");

	do_action('user_register', $wpdb->insert_id);

	if ($result == false) {
		die (sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_settings('admin_email')));
	}

	$stars = '';
	for ($i = 0; $i < strlen($pass1); $i = $i + 1) {
		$stars .= '*';
	}
	
	$message  = sprintf(__('Username: %s'), $user_login) . "\r\n";
	$message .= sprintf(__('Password: %s'), $password) . "\r\n";
	$message .= get_settings('siteurl') . "/wp-login.php\r\n";
	
	wp_mail($user_email, sprintf(__('[%s] Your username and password'), get_settings('blogname')), $message);

	$message  = sprintf(__('New user registration on your blog %s:'), get_settings('blogname')) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

	@wp_mail(get_settings('admin_email'), sprintf(__('[%s] New User Registration'), get_settings('blogname')), $message);

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &raquo; <?php _e('Registration Complete') ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_settings('blog_charset'); ?>" />	
	<link rel="stylesheet" href="wp-admin/wp-admin.css" type="text/css" />
	<style type="text/css">
	.submit {
		font-size: 1.7em;
	}
	</style>
</head>
<body>

<div id="login"> 
	<h2><?php _e('Registration Complete') ?></h2>
	<p><?php printf(__('Username: %s'), "<strong>$user_login</strong>") ?><br />
	<?php printf(__('Password: %s'), '<strong>' . __('emailed to you') . '</strong>') ?> <br />
	<?php printf(__('E-mail: %s'), "<strong>$user_email</strong>") ?></p>
	<p class="submit"><a href="wp-login.php"><?php _e('Login'); ?> &raquo;</a></p>
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
	<title>WordPress &raquo; <?php _e('Registration Currently Disabled') ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_settings('blog_charset'); ?>">
	<link rel="stylesheet" href="wp-admin/wp-admin.css" type="text/css">
</head>

<body>

<div id="login">
	<h2><?php _e('Registration Disabled') ?></h2>
	<p><?php _e('User registration is currently not allowed.') ?><br />
	<a href="<?php echo get_settings('home') . '/'; ?>" title="<?php _e('Go back to the blog') ?>"><?php _e('Home') ?></a>
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
	<title>WordPress &raquo; <?php _e('Registration Form') ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_settings('blog_charset'); ?>" />
	<link rel="stylesheet" href="wp-admin/wp-admin.css" type="text/css" />
	<style type="text/css">
	#user_email, #user_login, #submit {
		font-size: 1.7em;
	}
	</style>
</head>

<body>
<div id="login">
<h1><a href="http://wordpress.org/">WordPress</a></h1>
<h2><?php _e('Register for this blog') ?></h2>

<form method="post" action="wp-register.php" id="registerform">
	<p><input type="hidden" name="action" value="register" />
	<label for="user_login"><?php _e('Username:') ?></label><br /> <input type="text" name="user_login" id="user_login" size="20" maxlength="20" /><br /></p>
	<p><label for="user_email"><?php _e('E-mail:') ?></label><br /> <input type="text" name="user_email" id="user_email" size="25" maxlength="100" /></p>
	<p>A password will be emailed to you.</p>
	<p class="submit"><input type="submit" value="<?php _e('Register') ?> &raquo;" id="submit" name="submit" /></p>
</form>
<ul>
	<li><a href="<?php bloginfo('home'); ?>" title="<?php _e('Are you lost?') ?>">&laquo; <?php _e('Back to blog') ?></a></li>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php"><?php _e('Login') ?></a></li>
	<li><a href="<?php bloginfo('wpurl'); ?>/wp-login.php?action=lostpassword" title="<?php _e('Password Lost and Found') ?>"><?php _e('Lost your password?') ?></a></li>
</ul>
</div>

</body>
</html>
<?php

break;
}
?>
