<?php
define('WP_INSTALLING', true);
if (!file_exists('../wp-config.php')) {
  require_once('../wp-includes/compat.php');
  require_once('../wp-includes/functions.php');
  wp_die("There doesn't seem to be a <code>wp-config.php</code> file. I need this before we can get started. Need more help? <a href='http://codex.wordpress.org/Editing_wp-config.php'>We got it</a>. You can <a href='setup-config.php'>create a <code>wp-config.php</code> file through a web interface</a>, but this doesn't work for all server setups. The safest way is to manually create the file.", "WordPress &rsaquo; Error");
}

require_once('../wp-config.php');
require_once('./includes/upgrade.php');

if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;
header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php _e('WordPress &rsaquo; Installation'); ?></title>
	<?php wp_admin_css( 'css/install' ); ?>
</head>
<body>
<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>
<?php
// Let's check to make sure WP isn't already installed.
if ( is_blog_installed() ) die('<h1>'.__('Already Installed').'</h1><p>'.__('You appear to have already installed WordPress. To reinstall please clear your old database tables first.').'</p></body></html>');

switch($step) {
	case 0:
	case 1: // in case people are directly linking to this
?>
<h1><?php _e('Welcome'); ?></h1>
<p><?php printf(__('Welcome to the famous five minute WordPress installation process! You may want to browse the <a href="%s">ReadMe documentation</a> at your leisure.  Otherwise, just fill in the information below and you\'ll be on your way to using the most extendable and powerful personal publishing platform in the world.'), '../readme.html'); ?></p>
<!--<h2 class="step"><a href="install.php?step=1"><?php _e('First Step &raquo;'); ?></a></h2>-->

<h1><?php _e('Information needed'); ?></h1>
<p><?php _e("Please provide the following information.  Don't worry, you can always change these settings later."); ?></p>

<form id="setup" method="post" action="install.php?step=2">
	<table width="100%">
		<tr>
			<th width="33%"><?php _e('Blog title:'); ?></th>
			<td><input name="weblog_title" type="text" id="weblog_title" size="25" /></td>
		</tr>
		<tr>
			<th><?php _e('Your e-mail:'); ?></th>
			<td><input name="admin_email" type="text" id="admin_email" size="25" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><label><input type="checkbox" name="blog_public" value="1" checked="checked" /> <?php _e('Allow my blog to appear in search engines like Google and Technorati.'); ?></label></td>
		</tr>
	</table>
	<p><em><?php _e('Double-check your email address before continuing.'); ?></em></p>
	<h2 class="step"><input type="submit" name="Submit" value="<?php _e('Install WordPress &raquo;'); ?>" /></h2>
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
			// TODO: poka-yoke
			die(__("<strong>ERROR</strong>: you must provide an e-mail address"));
		} else if (!is_email($admin_email)) {
			// TODO: poka-yoke
			die(__('<strong>ERROR</strong>: that isn\'t a valid e-mail address.  E-mail addresses look like: <code>username@example.com</code>'));
		}

	$result = wp_install($weblog_title, 'admin', $admin_email, $public);
	extract($result, EXTR_SKIP);
?>

<h1><?php _e('Success!'); ?></h1>

<p><?php printf(__('WordPress has been installed.  Now you can <a href="%1$s">log in</a> with the <strong>username</strong> "<code>admin</code>" and <strong>password</strong> "<code>%2$s</code>".'), '../wp-login.php', $password); ?></p>
<p><?php _e('<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you.'); ?></p>

<dl>
	<dt><?php _e('Username'); ?></dt>
		<dd><code>admin</code></dd>
	<dt><?php _e('Password'); ?></dt>
		<dd><code><?php echo $password; ?></code></dd>
	<dt><?php _e('Login address'); ?></dt>
		<dd><a href="../wp-login.php">wp-login.php</a></dd>
</dl>
<p><?php _e('Were you expecting more steps? Sorry to disappoint. :)'); ?></p>

<?php
		break;
}
?>

<p id="footer"><?php _e('<a href="http://wordpress.org/">WordPress</a>, personal publishing platform.'); ?></p>
</body>
</html>