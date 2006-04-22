<?php
define('WP_INSTALLING', true);
if (!file_exists('../wp-config.php')) 
    die("There doesn't seem to be a <code>wp-config.php</code> file. I need this before we can get started. Need more help? <a href='http://wordpress.org/docs/faq/#wp-config'>We got it</a>. You can <a href='setup-config.php'>create a <code>wp-config.php</code> file through a web interface</a>, but this doesn't work for all server setups. The safest way is to manually create the file.");

require_once('../wp-config.php');
require_once('./upgrade-functions.php');

if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;
header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e('WordPress &rsaquo; Installation'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style media="screen" type="text/css">
	<!--
	html {
		background: #eee;
	}
	body {
		background: #fff;
		color: #000;
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 20%;
		margin-right: 20%;
		padding: .2em 2em;
	}

	h1 {
		color: #006;
		font-size: 18px;
		font-weight: lighter;
	}

	h2 {
		font-size: 16px;
	}

	p, li, dt {
		line-height: 140%;
		padding-bottom: 2px;
	}

	ul, ol {
		padding: 5px 5px 5px 20px;
	}
	#logo {
		margin-bottom: 2em;
	}
	.step a, .step input {
		font-size: 2em;
	}
	td input {
		font-size: 1.5em;
	}
	.step, th {
		text-align: right;
	}
	#footer {
		text-align: center; 
		border-top: 1px solid #ccc; 
		padding-top: 1em; 
		font-style: italic;
	}
	-->
	</style>
</head>
<body>
<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>
<?php
// Let's check to make sure WP isn't already installed.
if ( is_blog_installed() ) die('<h1>'.__('Already Installed').'</h1><p>'.__('You appear to have already installed WordPress. To reinstall please clear your old database tables first.').'</p></body></html>');

switch($step) {

	case 0:
?>
<p><?php printf(__('Welcome to WordPress installation. We&#8217;re now going to go through a few steps to get you up and running with the latest in personal publishing platforms. You may want to peruse the <a href="%s">ReadMe documentation</a> at your leisure.'), '../readme.html'); ?></p>
	<h2 class="step"><a href="install.php?step=1"><?php _e('First Step &raquo;'); ?></a></h2>
<?php
	break;

	case 1:

?>
<h1><?php _e('First Step'); ?></h1>
<p><?php _e("Before we begin we need a little bit of information. Don't worry, you can always change these later."); ?></p>

<form id="setup" method="post" action="install.php?step=2">
<table width="100%">
<tr>
<th width="33%"><?php _e('Weblog title:'); ?></th>
<td><input name="weblog_title" type="text" id="weblog_title" size="25" /></td>
</tr>
<tr>
<th><?php _e('Your e-mail:'); ?></th>
	<td><input name="admin_email" type="text" id="admin_email" size="25" /></td>
</tr>
<tr>
<th scope="row"  valign="top"> <?php __('Privacy:'); ?></th>
<td><label><input type="checkbox" name="blog_public" value="1" checked="checked" /> <?php _e('I would like my blog to appear in search engines like Google and Technorati.'); ?></label></td>
</tr> 
</table>
<p><em><?php _e('Double-check that email address before continuing.'); ?></em></p>
<h2 class="step">
<input type="submit" name="Submit" value="<?php _e('Continue to Second Step &raquo;'); ?>" />
</h2>
</form>

<?php
	break;
	case 2:

// Fill in the data we gathered
$weblog_title = stripslashes($_POST['weblog_title']);
$admin_email = stripslashes($_POST['admin_email']);
$public = (int) $_POST['blog_public'];
// check e-mail address
if (empty($admin_email)) {
	die (__("<strong>ERROR</strong>: please type your e-mail address"));
} else if (!is_email($admin_email)) {
	die (__("<strong>ERROR</strong>: the e-mail address isn't correct"));
}

?>
<h1><?php _e('Second Step'); ?></h1>
<p><?php _e('Now we&#8217;re going to create the database tables and fill them with some default data.'); ?></p>


<?php

$result = wp_install($weblog_title, __('admin'), $admin_email, $public);
extract($result);
?>

<p><em><?php _e('Finished!'); ?></em></p>

<p><?php printf(__('Now you can <a href="%1$s">log in</a> with the <strong>username</strong> "<code>admin</code>" and <strong>password</strong> "<code>%2$s</code>".'), '../wp-login.php', $password); ?></p>
<p><?php _e('<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you. If you lose it, you will have to delete the tables from the database yourself, and re-install WordPress. So to review:'); ?>
</p>
<dl>
<dt><?php _e('Username'); ?></dt>
<dd><code><?php _e('admin') ?></code></dd>
<dt><?php _e('Password'); ?></dt>
<dd><code><?php echo $password; ?></code></dd>
	<dt><?php _e('Login address'); ?></dt>
<dd><a href="../wp-login.php">wp-login.php</a></dd>
</dl>
<p><?php _e('Were you expecting more steps? Sorry to disappoint. All done! :)'); ?></p>
<?php
	break;
}
?>
<p id="footer"><?php _e('<a href="http://wordpress.org/">WordPress</a>, personal publishing platform.'); ?></p>
</body>
</html>
