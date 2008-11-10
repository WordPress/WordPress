<?php
/**
 * WordPress Installer
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * We are installing WordPress.
 *
 * @since unknown
 * @var bool
 */
define('WP_INSTALLING', true);

/** Load WordPress Bootstrap */
require_once('../wp-load.php');

/** Load WordPress Administration Upgrade API */
require_once('./includes/upgrade.php');

if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;

/**
 * Display install header.
 *
 * @since unknown
 * @package WordPress
 * @subpackage Installer
 */
function display_header() {
header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php _e('WordPress &rsaquo; Installation'); ?></title>
	<?php wp_admin_css( 'install', true ); ?>
</head>
<body>
<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>

<?php
}//end function display_header();

// Let's check to make sure WP isn't already installed.
if ( is_blog_installed() ) {display_header(); die('<h1>'.__('Already Installed').'</h1><p>'.__('You appear to have already installed WordPress. To reinstall please clear your old database tables first.').'</p></body></html>');}

switch($step) {
	case 0:
	case 1: // in case people are directly linking to this
	  display_header();
?>
<h1><?php _e('Welcome'); ?></h1>
<p><?php printf(__('Welcome to the famous five minute WordPress installation process! You may want to browse the <a href="%s">ReadMe documentation</a> at your leisure.  Otherwise, just fill in the information below and you\'ll be on your way to using the most extendable and powerful personal publishing platform in the world.'), '../readme.html'); ?></p>
<!--<h2 class="step"><a href="install.php?step=1"><?php _e('First Step'); ?></a></h2>-->

<h1><?php _e('Information needed'); ?></h1>
<p><?php _e("Please provide the following information.  Don't worry, you can always change these settings later."); ?></p>

<form id="setup" method="post" action="install.php?step=2">
	<table class="form-table">
		<tr>
			<th scope="row"><label for="weblog_title"><?php _e('Blog Title'); ?></label></th>
			<td><input name="weblog_title" type="text" id="weblog_title" size="25" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="admin_email"><?php _e('Your E-mail'); ?></label></th>
			<td><input name="admin_email" type="text" id="admin_email" size="25" /><br />
			<?php _e('Double-check your email address before continuing.'); ?>
		</tr>
		<tr>
			<td colspan="2"><label><input type="checkbox" name="blog_public" value="1" checked="checked" /> <?php _e('Allow my blog to appear in search engines like Google and Technorati.'); ?></label></td>
		</tr>
	</table>
	<p class="step"><input type="submit" name="Submit" value="<?php _e('Install WordPress'); ?>" class="button" /></p>
</form>

<?php
		break;
	case 2:
		if ( !empty($wpdb->error) )
			wp_die($wpdb->error->get_error_message());

		display_header();
		// Fill in the data we gathered
		$weblog_title = isset($_POST['weblog_title']) ? stripslashes($_POST['weblog_title']) : '';
		$admin_email = isset($_POST['admin_email']) ? stripslashes($_POST['admin_email']) : '';
		$public = isset($_POST['blog_public']) ? (int) $_POST['blog_public'] : 0;
		// check e-mail address
		if (empty($admin_email)) {
			// TODO: poka-yoke
			die('<p>'.__("<strong>ERROR</strong>: you must provide an e-mail address.").'</p>');
		} else if (!is_email($admin_email)) {
			// TODO: poka-yoke
			die('<p>'.__('<strong>ERROR</strong>: that isn&#8217;t a valid e-mail address.  E-mail addresses look like: <code>username@example.com</code>').'</p>');
		}

		$wpdb->show_errors();
		$result = wp_install($weblog_title, 'admin', $admin_email, $public);
		extract($result, EXTR_SKIP);
?>

<h1><?php _e('Success!'); ?></h1>

<p><?php printf(__('WordPress has been installed. Were you expecting more steps? Sorry to disappoint.'), ''); ?></p>

<table class="form-table">
	<tr>
		<th><?php _e('Username'); ?></th>
		<td><code>admin</code></td>
	</tr>
	<tr>
		<th><?php _e('Password'); ?></th>
		<td><code><?php echo $password; ?></code><br />
			<?php echo '<p>'.__('<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you.').'</p>'; ?></td>
	</tr>
</table>

<p class="step"><a href="../wp-login.php" class="button"><?php _e('Log In'); ?></a></p>

<?php
		break;
}
?>
<script type="text/javascript">var t = document.getElementById('weblog_title'); if (t){ t.focus(); }</script>
</body>
</html>
