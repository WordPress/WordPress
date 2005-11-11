<?php
define('WP_INSTALLING', true);
if (!file_exists('../wp-config.php')) 
    die("There doesn't seem to be a <code>wp-config.php</code> file. I need this before we can get started. Need more help? <a href='http://wordpress.org/docs/faq/#wp-config'>We got it</a>. You can <a href='setup-config.php'>create a <code>wp-config.php</code> file through a web interface</a>, but this doesn't work for all server setups. The safest way is to manually create the file.");

require_once('../wp-config.php');
require_once('./upgrade-functions.php');

$schema = ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
$guessurl = str_replace('/wp-admin/install.php?step=2', '', $schema . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) );

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
<h1 id="logo"><img alt="WordPress" src="http://static.wordpress.org/logo.png" /></h1>
<?php
// Let's check to make sure WP isn't already installed.
$wpdb->hide_errors();
$installed = $wpdb->get_results("SELECT * FROM $wpdb->users");
if ($installed) die(__('<h1>Already Installed</h1><p>You appear to have already installed WordPress. To reinstall please clear your old database tables first.</p>') . '</body></html>');
$wpdb->show_errors();

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
$weblog_title = $_POST['weblog_title'];
$admin_email = $_POST['admin_email'];
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
flush();

// Set everything up
make_db_current_silent();
populate_options();
populate_roles();

$wpdb->query("UPDATE $wpdb->options SET option_value = '$weblog_title' WHERE option_name = 'blogname'");
$wpdb->query("UPDATE $wpdb->options SET option_value = '$admin_email' WHERE option_name = 'admin_email'");

// Now drop in some default links
$wpdb->query("INSERT INTO $wpdb->linkcategories (cat_id, cat_name) VALUES (1, '".$wpdb->escape(__('Blogroll'))."')");
$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss) VALUES ('http://blogs.linux.ie/xeer/', 'Donncha', 1, 'http://blogs.linux.ie/xeer/feed/');");
$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss) VALUES ('http://zengun.org/weblog/', 'Michel', 1, 'http://zengun.org/weblog/feed/');");
$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss) VALUES ('http://boren.nu/', 'Ryan', 1, 'http://boren.nu/feed/');");
$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss) VALUES ('http://photomatt.net/', 'Matt', 1, 'http://xml.photomatt.net/feed/');");
$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss) VALUES ('http://zed1.com/journalized/', 'Mike', 1, 'http://zed1.com/journalized/feed/');");
$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss) VALUES ('http://www.alexking.org/', 'Alex', 1, 'http://www.alexking.org/blog/wp-rss2.php');");
$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss) VALUES ('http://dougal.gunters.org/', 'Dougal', 1, 'http://dougal.gunters.org/feed/');");

// Default category
$wpdb->query("INSERT INTO $wpdb->categories (cat_ID, cat_name, category_nicename) VALUES ('0', '".$wpdb->escape(__('Uncategorized'))."', '".sanitize_title(__('Uncategorized'))."')");

// First post
$now = date('Y-m-d H:i:s');
$now_gmt = gmdate('Y-m-d H:i:s');
$wpdb->query("INSERT INTO $wpdb->posts (post_author, post_date, post_date_gmt, post_content, post_title, post_category, post_name, post_modified, post_modified_gmt) VALUES ('1', '$now', '$now_gmt', '".$wpdb->escape(__('Welcome to WordPress. This is your first post. Edit or delete it, then start blogging!'))."', '".$wpdb->escape(__('Hello world!'))."', '0', '".$wpdb->escape(__('hello-world'))."', '$now', '$now_gmt')");

$wpdb->query( "INSERT INTO $wpdb->post2cat (`rel_id`, `post_id`, `category_id`) VALUES (1, 1, 1)" );

// Default comment
$wpdb->query("INSERT INTO $wpdb->comments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_date, comment_date_gmt, comment_content) VALUES ('1', '".$wpdb->escape(__('Mr WordPress'))."', '', 'http://wordpress.org/', '$now', '$now_gmt', '".$wpdb->escape(__('Hi, this is a comment.<br />To delete a comment, just log in, and view the posts\' comments, there you will have the option to edit or delete them.'))."')");

// First Page

$wpdb->query("INSERT INTO $wpdb->posts (post_author, post_date, post_date_gmt, post_content, post_title, post_category, post_name, post_modified, post_modified_gmt, post_status) VALUES ('1', '$now', '$now_gmt', '".$wpdb->escape(__('This is an example of a WordPress page, you could edit this to put information about yourself or your site so readers know where you are coming from. You can create as many pages like this one or sub-pages as you like and manage all of your content inside of WordPress.'))."', '".$wpdb->escape(__('About'))."', '0', '".$wpdb->escape(__('about'))."', '$now', '$now_gmt', 'static')");

// Set up admin user
$random_password = substr(md5(uniqid(microtime())), 0, 6);
$display_name_array = explode('@', $admin_email);
$display_name = $display_name_array[0];
$wpdb->query("INSERT INTO $wpdb->users (ID, user_login, user_pass, user_email, user_registered, display_name, user_nicename) VALUES ( '1', 'admin', MD5('$random_password'), '$admin_email', NOW(), '$display_name', 'admin')");
$wpdb->query("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES ({$wpdb->insert_id}, '{$table_prefix}user_level', '10');");
$admin_caps = serialize(array('administrator' => true));
$wpdb->query("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES ({$wpdb->insert_id}, '{$table_prefix}capabilities', '{$admin_caps}');");

$message_headers = 'From: ' . stripslashes($_POST['weblog_title']) . ' <wordpress@' . $_SERVER['SERVER_NAME'] . '>';
$message = sprintf(__("Your new WordPress blog has been successfully set up at:

%1\$s

You can log in to the administrator account with the following information:

Username: admin
Password: %2\$s

We hope you enjoy your new weblog. Thanks!

--The WordPress Team
http://wordpress.org/
"), $guessurl, $random_password);

@wp_mail($admin_email, __('New WordPress Blog'), $message, $message_headers);

?>

<p><em><?php _e('Finished!'); ?></em></p>

<p><?php printf(__('Now you can <a href="%1$s">log in</a> with the <strong>username</strong> "<code>admin</code>" and <strong>password</strong> "<code>%2$s</code>".'), '../wp-login.php', $random_password); ?></p>
<p><?php _e('<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you. If you lose it, you will have to delete the tables from the database yourself, and re-install WordPress. So to review:'); ?>
</p>
<dl>
<dt><?php _e('Username'); ?></dt>
<dd><code>admin</code></dd>
<dt><?php _e('Password'); ?></dt>
<dd><code><?php echo $random_password; ?></code></dd>
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
