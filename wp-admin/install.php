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
require_once(dirname(dirname(__FILE__)) . '/wp-load.php');

/** Load WordPress Administration Upgrade API */
require_once(dirname(__FILE__) . '/includes/upgrade.php');

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

function display_setup_form( $error = null ) {
	// Ensure that Blogs appear in search engines by default
	$blog_public = 1;
	if ( isset($_POST) && !empty($_POST) ) {
		$blog_public = isset($_POST['blog_public']);
	}
	
	if ( ! is_null( $error ) ) {
?>
<p><?php printf( __('<strong>ERROR</strong>: %s'), $error); ?></p>
<?php } ?>
<form id="setup" method="post" action="install.php?step=2">
	<table class="form-table">
		<tr>
			<th scope="row"><label for="weblog_title"><?php _e('Blog Title'); ?></label></th>
			<td><input name="weblog_title" type="text" id="weblog_title" size="25" value="<?php echo ( isset($_POST['weblog_title']) ? esc_attr($_POST['weblog_title']) : '' ); ?>" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="admin_email"><?php _e('Your E-mail'); ?></label></th>
			<td><input name="admin_email" type="text" id="admin_email" size="25" value="<?php echo ( isset($_POST['admin_email']) ? esc_attr($_POST['admin_email']) : '' ); ?>" /><br />
			<?php _e('Double-check your email address before continuing.'); ?></td>
		</tr>
		<tr>
			<td colspan="2"><label><input type="checkbox" name="blog_public" value="1" <?php checked($blog_public); ?> /> <?php _e('Allow my blog to appear in search engines like Google and Technorati.'); ?></label></td>
		</tr>
	</table>
	<p class="step"><input type="submit" name="Submit" value="<?php esc_attr_e('Install WordPress'); ?>" class="button" /></p>
</form>
<?php
}

// Let's check to make sure WP isn't already installed.
if ( is_blog_installed() ) {display_header(); die('<h1>'.__('Already Installed').'</h1><p>'.__('You appear to have already installed WordPress. To reinstall please clear your old database tables first.').'</p></body></html>');}

switch($step) {
	case 0:
	case 1: // in case people are directly linking to this
	  display_header();
?>
<h1><?php _e('Welcome'); ?></h1>
<p><?php printf(__('Welcome to the famous five minute WordPress installation process! You may want to browse the <a href="%s">ReadMe documentation</a> at your leisure.  Otherwise, just fill in the information below and you&#8217;ll be on your way to using the most extendable and powerful personal publishing platform in the world.'), '../readme.html'); ?></p>
<!--<h2 class="step"><a href="install.php?step=1"><?php _e('First Step'); ?></a></h2>-->

<h1><?php _e('Information needed'); ?></h1>
<p><?php _e('Please provide the following information.  Don&#8217;t worry, you can always change these settings later.'); ?></p>



<?php
		display_setup_form();
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
		$error = false;
		if (empty($admin_email)) {
			// TODO: poka-yoke
			display_setup_form( __('you must provide an e-mail address.') );
			$error = true;
		} else if (!is_email($admin_email)) {
			// TODO: poka-yoke
			display_setup_form( __('that isn&#8217;t a valid e-mail address.  E-mail addresses look like: <code>username@example.com</code>') );
			$error = true;
		}

		if ( $error === false ) {
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
		<td><?php if ( !empty( $password ) ) {
						echo '<code>'. $password .'</code><br />';
					}
					echo '<p>'. $password_message .'</p>'; ?></td>
	</tr>
</table>

<p class="step"><a href="../wp-login.php" class="button"><?php _e('Log In'); ?></a></p>

<?php
		}
		break;
}
?>
<script type="text/javascript">var t = document.getElementById('weblog_title'); if (t){ t.focus(); }</script>
</body>
</html>
