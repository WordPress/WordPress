<?php
$_wp_installing = 1;
if (!file_exists('../wp-config.php')) 
    die("There doesn't seem to be a <code>wp-config.php</code> file. I need this before we can get started. Need more help? <a href='http://wordpress.org/docs/faq/#wp-config'>We got it</a>. You can <a href='wp-admin/setup-config.php'>create a <code>wp-config.php</code> file through a web interface</a>, but this doesn't work for all server setups. The safest way is to manually create the file.");
require_once('../wp-config.php');
require('upgrade-functions.php');

if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &rsaquo; Installation</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<style media="screen" type="text/css">

	body {
		background-color: white;
		color: black;
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 15%;
		margin-right: 15%;
	}
	#logo {
		margin: 0;
		padding: 0;
		background-image: url(http://wordpress.org/images/wordpress.gif);
		background-repeat: no-repeat;
		height: 60px;
		border-bottom: 4px solid #333;
	}
	#logo a {
		display: block;
		height: 60px;
	}
	#logo a span {
		display: none;
	}
	p, li {
		line-height: 140%;
	}

	</style>
</head>
<body>
<h1 id="logo"><a href="http://wordpress.org"><span>WordPress</span></a></h1>
<?php
// Let's check to make sure WP isn't already installed.

$wpdb->hide_errors();
$installed = $wpdb->get_results("SELECT * FROM $wpdb->users");
if ($installed) die(__('<p>You appear to already have WordPress installed. If you would like to reinstall please clear your old database files first.</p></body></html>'));
$wpdb->show_errors();
switch($step) {

	case 0:
?>
<p>Welcome to WordPress. We&#8217;re now going to go through a few steps to get
  you up and running with the latest in personal publishing platforms. Before
  we get started, remember that we require a PHP version of at least 4.1.0, you
  have <?php echo phpversion(); ?>. Look good? You also need to set up the database
  connection information in <code>wp-config.php</code>. Have you looked at the
  <a href="../readme.html">readme</a>? If you&#8217;re all ready, <a href="install.php?step=1">let's
  go</a>! </p>
<?php
	break;

	case 1:
?>
<h1>Step 1</h1>
<p>Okay first we&#8217;re going to set up the links database. This will allow you to host your own blogroll, complete with Weblogs.com updates.</p>
<?php

$got_links = false;
$got_cats = false;
$got_row = false;
?>
<p>Installing WP-Links.</p>
<p>Checking for tables...</p>
<?php
$result = mysql_list_tables(DB_NAME);
if (!$result) {
    print "DB Error, could not list tables\n";
    print 'MySQL Error: ' . mysql_error();
    exit;
}

while ($row = mysql_fetch_row($result)) {
    if ($row[0] == $wpdb->links)
        $got_links = true;
    if ($row[0] == $wpdb->linkcategories)
        $got_cats = true;
    //print "Table: $row[0]<br />\n";
}
if (!$got_cats) {
    echo "<p>Can't find table '$wpdb->linkcategories', gonna create it...</p>\n";
    $sql = "CREATE TABLE $wpdb->linkcategories ( " .
           " cat_id int(11) NOT NULL auto_increment, " .
           " cat_name tinytext NOT NULL, ".
           " auto_toggle enum ('Y','N') NOT NULL default 'N', ".
           " show_images enum ('Y','N') NOT NULL default 'Y',        " .
           " show_description enum ('Y','N') NOT NULL default 'Y',   " .
           " show_rating enum ('Y','N') NOT NULL default 'Y',        " .
           " show_updated enum ('Y','N') NOT NULL default 'Y',       " .
           " sort_order varchar(64) NOT NULL default 'name',         " .
           " sort_desc enum('Y','N') NOT NULL default 'N',           " .
           " text_before_link varchar(128) not null default '<li>',  " .
           " text_after_link  varchar(128) not null default '<br />'," .
           " text_after_all  varchar(128) not null default '</li>',  " .
           " list_limit int not null default '-1',                     " .
           " PRIMARY KEY (cat_id) ".
           ") ";
    $result = mysql_query($sql) or print ("Can't create the table '$wpdb->linkcategories' in the database.<br />" . $sql . "<br />" . mysql_error());
    if ($result != false) {
        echo "<p>Table '$wpdb->linkcategories' created OK</p>\n";
        $got_cats = true;
    }
} else {
    echo "<p>Found table '$wpdb->linkcategories', don't need to create it...</p>\n";
        $got_cats = true;
}
if (!$got_links) {
    echo "<p>Can't find '$wpdb->links', gonna create it...</p>\n";
    $sql = "CREATE TABLE $wpdb->links ( " .
           " link_id int(11) NOT NULL auto_increment,           " .
           " link_url varchar(255) NOT NULL default '',         " .
           " link_name varchar(255) NOT NULL default '',        " .
           " link_image varchar(255) NOT NULL default '',       " .
           " link_target varchar(25) NOT NULL default '',       " .
           " link_category int(11) NOT NULL default 0,          " .
           " link_description varchar(255) NOT NULL default '', " .
           " link_visible enum ('Y','N') NOT NULL default 'Y',  " .
           " link_owner int NOT NULL DEFAULT '1',               " .
           " link_rating int NOT NULL DEFAULT '0',              " .
           " link_updated DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00', " .
           " link_rel varchar(255) NOT NULL default '',         " .
           " link_notes MEDIUMTEXT NOT NULL default '',         " .
           " PRIMARY KEY (link_id)                              " .
           ") ";
    $result = mysql_query($sql) or print ("Can't create the table '$wpdb->links' in the database.<br />" . $sql . "<br />" . mysql_error());
	$links = mysql_query("INSERT INTO $wpdb->links VALUES ('', 'http://wordpress.org/', 'WordPress', '', '', 1, '', 'Y', 1, 0, '0000-00-00 00:00:00', '', '');");
	$links = mysql_query("INSERT INTO $wpdb->links VALUES ('', 'http://photomatt.net/', 'Matt', '', '', 1, '', 'Y', 1, 0, '0000-00-00 00:00:00', '', '');");
	$links = mysql_query("INSERT INTO $wpdb->links VALUES ('', 'http://zed1.com/journalized/', 'Mike', '', '', 1, '', 'Y', 1, 0, '0000-00-00 00:00:00', '', '');");
	$links = mysql_query("INSERT INTO $wpdb->links VALUES ('', 'http://www.alexking.org/', 'Alex', '', '', 1, '', 'Y', 1, 0, '0000-00-00 00:00:00', '', '');");
	$links = mysql_query("INSERT INTO $wpdb->links VALUES ('', 'http://dougal.gunters.org/', 'Dougal', '', '', 1, '', 'Y', 1, 0, '0000-00-00 00:00:00', '', '');");


    if ($result != false) {
        echo "<p>Table '$wpdb->links' created OK</p>\n";
        $got_links = true;
    }
} else {
    echo "<p>Found table '$wpdb->links', don't need to create it...</p>\n";
        $got_links = true;
}

if ($got_links && $got_cats) {
    echo "<p>Looking for category 1...</p>\n";
    $sql = "SELECT * FROM $wpdb->linkcategories WHERE cat_id=1 ";
    $result = mysql_query($sql) or print ("Can't query '$wpdb->linkcategories'.<br />" . $sql . "<br />" . mysql_error());
    if ($result != false) {
        if ($row = mysql_fetch_object($result)) {
            echo "<p>You have at least 1 category. Good!</p>\n";
            $got_row = true;
        } else {
            echo "<p>Gonna insert category 1...</p>\n";
            $sql = "INSERT INTO $wpdb->linkcategories (cat_id, cat_name) VALUES (1, 'Links')";
            $result = mysql_query($sql) or print ("Can't query insert category.<br />" . $sql . "<br />" . mysql_error());
            if ($result != false) {
                echo "<p>Inserted category Ok</p>\n";
                $got_row = true;
            }
        }
    }
}

if ($got_row) {
    echo "<p>All done!</p>\n";
}
?>
<p>Did you defeat the boss monster at the end? Great! You&#8217;re ready for <a href="install.php?step=2">Step
  2</a>.</p>
<?php
	break;
	case 2:
?>
<h1>Step 2</h1>
<p>First we&#8217;re going to create the necessary blog tables in the database...</p>

<?php
# Note: if you want to start again with a clean b2 database,
#       just remove the // in this file

// $query = "DROP TABLE IF EXISTS $wpdb->posts";
// $q = mysql_query($query) or die ("doh, can't drop the table \"$wpdb->posts\" in the database.");

$query = "CREATE TABLE $wpdb->posts (
  ID int(10) unsigned NOT NULL auto_increment,
  post_author int(4) NOT NULL default '0',
  post_date datetime NOT NULL default '0000-00-00 00:00:00',
  post_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  post_content text NOT NULL,
  post_title text NOT NULL,
  post_category int(4) NOT NULL default '0',
  post_excerpt text NOT NULL,
  post_lat float,
  post_lon float,
  post_status enum('publish','draft','private') NOT NULL default 'publish',
  comment_status enum('open','closed') NOT NULL default 'open',
  ping_status enum('open','closed') NOT NULL default 'open',
  post_password varchar(20) NOT NULL default '',
  post_name varchar(200) NOT NULL default '',
  to_ping text NOT NULL,
  pinged text NOT NULL,
  post_modified datetime NOT NULL default '0000-00-00 00:00:00',
  post_modified_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  post_content_filtered text NOT NULL,
  PRIMARY KEY  (ID),
  KEY post_date (post_date),
  KEY post_date_gmt (post_date_gmt),
  KEY post_name (post_name),
  KEY post_status (post_status)
)
";
$q = $wpdb->query($query);
?>

<p>The first table has been created! ...</p>

<?php
$now = date('Y-m-d H:i:s');
$now_gmt = gmdate('Y-m-d H:i:s');
$query = "INSERT INTO $wpdb->posts (post_author, post_date, post_date_gmt, post_content, post_title, post_modified, post_modified_gmt) VALUES ('1', '$now', '$now_gmt', 'Welcome to WordPress. This is the first post. Edit or delete it, then start blogging!', 'Hello world!', '$now', '$now_gmt')";

$q = $wpdb->query($query);
?>

<p>The test post has been inserted correctly...</p>

<?php
// $query = "DROP TABLE IF EXISTS $wpdb->categories";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$wpdb->categories\" in the database.");

$query = "
CREATE TABLE $wpdb->categories (
  cat_ID int(4) NOT NULL auto_increment,
  cat_name varchar(55) NOT NULL default '',
  PRIMARY KEY  (cat_ID),
  UNIQUE (cat_name)
)
";
$q = $wpdb->query($query);

$query = "INSERT INTO $wpdb->categories (cat_ID, cat_name) VALUES ('0', 'General')";
$q = $wpdb->query($query);

$query = "UPDATE $wpdb->posts SET post_category = 1";
$result = $wpdb->query($query);
?>

<p>Categories are up and running...</p>

<?php
// $query = "DROP TABLE IF EXISTS $wpdb->comments";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$wpdb->comments\" in the database.");

$query = "
CREATE TABLE $wpdb->comments (
  comment_ID int(11) unsigned NOT NULL auto_increment,
  comment_post_ID int(11) NOT NULL default '0',
  comment_author tinytext NOT NULL,
  comment_author_email varchar(100) NOT NULL default '',
  comment_author_url varchar(100) NOT NULL default '',
  comment_author_IP varchar(100) NOT NULL default '',
  comment_date datetime NOT NULL default '0000-00-00 00:00:00',
  comment_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
  comment_content text NOT NULL,
  comment_karma int(11) NOT NULL default '0',
  PRIMARY KEY  (comment_ID)
)
";
$q = $wpdb->query($query);

$now = date('Y-m-d H:i:s');
$now_gmt = gmdate('Y-m-d H:i:s');
$query = "INSERT INTO $wpdb->comments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_date_gmt, comment_content) VALUES ('1', 'Mr WordPress', 'mr@wordpress.org', 'http://wordpress.org', '127.0.0.1', '$now', '$now_gmt', 'Hi, this is a comment.<br />To delete a comment, just log in, and view the posts\' comments, there you will have the option to edit or delete them.')";
$q = $wpdb->query($query);
?>

<p>Comments are groovy...</p>

<?php
$query = "
	CREATE TABLE $wpdb->postmeta (
	  meta_id int(11) NOT NULL auto_increment,
	  post_id int(11) NOT NULL default 0,
	  meta_key varchar(255),
	  meta_value text,
	  PRIMARY KEY (meta_id),
	  INDEX (post_id),
	  INDEX (meta_key)
	)
	";

$q = $wpdb->query($query);


?>

<p>Post metadata table ready to go...</p>

<?php
// $query = "DROP TABLE IF EXISTS $wpdb->options";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$wpdb->options\" in the database.");

$query = "
CREATE TABLE $wpdb->options (
  option_id int(11) NOT NULL auto_increment,
  blog_id int(11) NOT NULL default 0,
  option_name varchar(64) NOT NULL default '',
  option_can_override enum ('Y','N') NOT NULL default 'Y',
  option_type int(11) NOT NULL default 1,
  option_value varchar(255) NOT NULL default '',
  option_width int NOT NULL default 20,
  option_height int NOT NULL default 8,
  option_description tinytext NOT NULL default '',
  option_admin_level int NOT NULL DEFAULT '1',
  PRIMARY KEY (option_id, blog_id, option_name)
)
";
$q = $wpdb->query($query);

// $query = "DROP TABLE IF EXISTS $tableoptiontypes";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$tableoptiontypes\" in the database.");

$query = "
CREATE TABLE $wpdb->optiontypes (
  optiontype_id int(11) NOT NULL auto_increment,
  optiontype_name varchar(64) NOT NULL,
  PRIMARY KEY (optiontype_id)
)
";
$q = $wpdb->query($query);


// $query = "DROP TABLE IF EXISTS $tableoptiongroups";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$tableoptiongroups\" in the database.");

$query = "
CREATE TABLE $wpdb->optiongroups (
  group_id int(11) NOT NULL auto_increment,
  group_name varchar(64) not null,
  group_desc varchar(255),
  group_longdesc tinytext,
  PRIMARY KEY (group_id)
)
";
$q = $wpdb->query($query);


// $query = "DROP TABLE IF EXISTS $wpdb->optiongroup_options";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$wpdb->optiongroup_options\" in the database.");

$query = "
CREATE TABLE $wpdb->optiongroup_options (
  group_id int(11) NOT NULL,
  option_id int(11) NOT NULL,
  seq int(11) NOT NULL,
  PRIMARY KEY (group_id, option_id)
)
";
$q = $wpdb->query($query);


// $query = "DROP TABLE IF EXISTS $wpdb->optionvalues";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$wpdb->optionvalues\" in the database.");

$query = "
CREATE TABLE $wpdb->optionvalues (
  option_id int(11) NOT NULL,
  optionvalue tinytext,
  optionvalue_desc varchar(255),
  optionvalue_max int(11),
  optionvalue_min int(11),
  optionvalue_seq int(11),
  UNIQUE (option_id, optionvalue(255)),
  INDEX (option_id, optionvalue_seq)
)
";
$q = $wpdb->query($query);

?>

<p>Option Tables created okay.</p>

<?php

$option_data = array(
"INSERT INTO $wpdb->optiontypes (optiontype_id, optiontype_name) VALUES (1, 'integer')",
"INSERT INTO $wpdb->optiontypes (optiontype_id, optiontype_name) VALUES (2, 'boolean')",
"INSERT INTO $wpdb->optiontypes (optiontype_id, optiontype_name) VALUES (3, 'string')",
"INSERT INTO $wpdb->optiontypes (optiontype_id, optiontype_name) VALUES (4, 'date')",
"INSERT INTO $wpdb->optiontypes (optiontype_id, optiontype_name) VALUES (5, 'select')",
"INSERT INTO $wpdb->optiontypes (optiontype_id, optiontype_name) VALUES (6, 'range')",
"INSERT INTO $wpdb->optiontypes (optiontype_id, optiontype_name) VALUES (7, 'sqlselect')",
"INSERT INTO $wpdb->optiontypes (optiontype_id, optiontype_name) VALUES (8, 'float')",

//base options from b2cofig
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(1,'siteurl', 3, 'http://example.com', 'siteurl is your blog\'s URL: for example, \'http://example.com/wordpress\' (no trailing slash !)', 8, 30)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(2,'blogfilename', 3, 'index.php', 'blogfilename is the name of the default file for your blog', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(3,'blogname', 3, 'my weblog', 'blogname is the name of your blog', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(4,'blogdescription', 3, 'babblings!', 'blogdescription is the description of your blog', 8, 40)",
//"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(6,'search_engine_friendly_urls', 2, '0', 'Querystring Configuration ** (don\'t change if you don\'t know what you\'re doing)', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(7,'new_users_can_blog', 2, '0', 'whether you want new users to be able to post entries once they have registered', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(8,'users_can_register', 2, '1', 'whether you want to allow users to register on your blog', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(54,'admin_email', 3, 'you@example.com', 'Your email (obvious eh?)', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level) VALUES (93, 'blog_charset', 3, 'utf-8', 'Your blog&#8217;s charset (here&#8217;s a <a href=\'http://developer.apple.com/documentation/macos8/TextIntlSvcs/TextEncodingConversionManager/TEC1.5/TEC.b0.html\'>list of possible charsets</a>)', 8)",
// general blog setup
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(9 ,'start_of_week', 5, '1', 'day at the start of the week', 8, 20)",
//"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(10,'use_preview', 2, '1', 'Do you want to use the \'preview\' function', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(14,'use_htmltrans', 2, '1', 'IMPORTANT! set this to false if you are using Chinese, Japanese, Korean, or other double-bytes languages', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(15,'use_balanceTags', 2, '1', 'this could help balance your HTML code. if it gives bad results, set it to false', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(16,'use_smilies', 2, '1', 'set this to 1 to enable smiley conversion in posts (note: this makes smiley conversion in ALL posts)', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(18,'require_name_email', 2, '0', 'set this to true to require e-mail and name, or false to allow comments without e-mail/name', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(20,'comments_notify', 2, '1', 'set this to true to let every author be notified about comments on their posts', 8, 20)",
//rss/rdf feeds
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(21,'posts_per_rss', 1, '10', 'number of last posts to syndicate', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(22,'rss_language', 3, 'en', 'the language of your blog ( see this: http://backend.userland.com/stories/storyReader$16 )', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(23,'rss_encoded_html', 2, '0', 'for b2rss.php: allow encoded HTML in &lt;description> tag?', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(24,'rss_excerpt_length', 1, '50', 'length (in words) of excerpts in the RSS feed? 0=unlimited note: in b2rss.php, this will be set to 0 if you use encoded HTML', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(25,'rss_use_excerpt', 2, '1', 'use the excerpt field for rss feed.', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(29,'use_trackback', 2, '1', 'set this to 0 or 1, whether you want to allow your posts to be trackback\'able or not note: setting it to zero would also disable sending trackbacks', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(30,'use_pingback', 2, '1', 'set this to 0 or 1, whether you want to allow your posts to be pingback\'able or not note: setting it to zero would also disable sending pingbacks', 8, 20)",
//file upload
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(31,'use_fileupload', 2, '0', 'set this to false to disable file upload, or true to enable it', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(32,'fileupload_realpath', 3, '/home/your/site/wordpress/images', 'enter the real path of the directory where you\'ll upload the pictures \nif you\'re unsure about what your real path is, please ask your host\'s support staff \nnote that the  directory must be writable by the webserver (chmod 766) \nnote for windows-servers users: use forwardslashes instead of backslashes', 8, 40)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(33,'fileupload_url', 3, 'http://example.com/images', 'enter the URL of that directory (it\'s used to generate the links to the uploded files)', 8, 40)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(34,'fileupload_allowedtypes', 3, 'jpg jpeg gif png', 'accepted file types, separated by spaces. example: \'jpg gif png\'', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(35,'fileupload_maxk', 1, '96', 'by default, most servers limit the size of uploads to 2048 KB, if you want to set it to a lower value, here it is (you cannot set a higher value than your server limit)', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(36,'fileupload_minlevel', 1, '4', 'you may not want all users to upload pictures/files, so you can set a minimum level for this', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(37,'fileupload_allowedusers', 3, '', '...or you may authorize only some users. enter their logins here, separated by spaces. if you leave this variable blank, all users who have the minimum level are authorized to upload. example: \'barbara anne george\'', 8, 30)",
// email settings
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(38,'mailserver_url', 3, 'mail.example.com', 'mailserver settings', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(39,'mailserver_login', 3, 'login@example.com', 'mailserver settings', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(40,'mailserver_pass', 3, 'password', 'mailserver settings', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(41,'mailserver_port', 1, '110', 'mailserver settings', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(42,'default_category', 1, '1', 'by default posts will have this category', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(46,'use_phoneemail', 2, '0', 'some mobile phone email services will send identical subject & content on the same line if you use such a service, set use_phoneemail to true, and indicate a separator string', 8, 20)",
// original options from options page
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(48,'posts_per_page', 1, '20','How many posts/days to show on the index page.', 4, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(49,'what_to_show', 5, 'posts','Posts, days, or posts paged', 4, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(50,'archive_mode', 5, 'monthly','Which \'unit\' to use for archives.', 4, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(51,'time_difference', 6, '0', 'if you\'re not on the timezone of your server', 4, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(52,'date_format', 3, 'n/j/Y', 'see note for format characters', 4, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(53,'time_format', 3, 'g:i a', 'see note for format characters', 4, 20)",

//'pages' of options
"INSERT INTO $wpdb->optiongroups (group_id,  group_name, group_desc) VALUES(1, 'Other Options', 'Posts per page etc. Original options page')",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(1,48,1 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(1,49,2 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(1,50,3 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(1,51,4 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(1,52,5 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(1,53,6 )",

"INSERT INTO $wpdb->optiongroups (group_id,  group_name, group_desc) VALUES(2, 'General blog settings', 'Things you\'ll probably want to tweak')",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(2,9 ,1 )",
//"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(2,10,2 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(2,11,3 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(2,12,4 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(2,13,5 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(2,14,6 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(2,15,7 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(2,16,8 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(2,17,9 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(2,18,10)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(2,19,11)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(2,20,12)",

"INSERT INTO $wpdb->optiongroups (group_id,  group_name, group_desc) VALUES(3, 'RSS/RDF Feeds, Track/Ping-backs', 'Settings for RSS/RDF Feeds, Track/ping-backs')",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(3,21,1 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(3,22,2 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(3,23,3 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(3,24,4 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(3,25,5 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(3,26,6 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(3,27,7 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(3,28,8 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(3,29,9 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(3,30,10)",

"INSERT INTO $wpdb->optiongroups (group_id,  group_name, group_desc) VALUES(4, 'File uploads', 'Settings for file uploads')",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(4,31,1 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(4,32,2 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(4,33,3 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(4,34,4 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(4,35,5 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(4,36,6 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(4,37,7 )",

"INSERT INTO $wpdb->optiongroups (group_id,  group_name, group_desc) VALUES(5, 'Blog-by-Email settings', 'Settings for blogging via email')",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(5,38,1 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(5,39,2 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(5,40,3 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(5,41,4 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(5,42,5 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(5,43,6 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(5,44,7 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(5,45,8 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(5,46,9 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(5,47,10)",

"INSERT INTO $wpdb->optiongroups (group_id,  group_name, group_desc) VALUES(6, 'Base settings', 'Basic settings required to get your blog working')",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(6,1,1)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(6,2,2)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(6,3,3)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(6,4,4)",
//"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(6,6,5)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(6,7,6)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(6,8,7)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(6,54,8)",

// select data for what to show
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (49, 'days',  'days',        null,null,1)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (49, 'posts', 'posts',       null,null,2)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (49, 'paged', 'posts paged', null,null,3)",
// select data for archive mode
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'daily',     'daily',       null,null,1)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'weekly',    'weekly',      null,null,2)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'monthly',   'monthly',     null,null,3)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (50, 'postbypost','post by post',null,null,4)",
// select data for time diff
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (51, 'hours', 'hours', 23, -23, null)",
// select data for start of week
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (9, '0', 'Sunday',   null,null,1)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (9, '1', 'Monday',   null,null,2)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (9, '6', 'Saturday', null,null,3)",


// Add in a new page for POST DEFAULTS

// default_post_status  select one of publish draft private
// default_comment_status select one of open closed
// default_ping_status select one of open closed
// default_pingback_flag select one of checked unchecked
// default_post_category sql_select "SELECT cat_id AS value, cat_name AS label FROM $wpdb->categories order by cat_name"

"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(55,'default_post_status',    5, 'publish', 'The default state of each new post', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(56,'default_comment_status', 5, 'open', 'The default state of comments for each new post', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(57,'default_ping_status',    5, 'open', 'The default ping state for each new post', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(58,'default_pingback_flag',  5, '1', 'Whether the \'PingBack the URLs in this post\' checkbox should be checked by default', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(59,'default_post_category',  7, '1', 'The default category for each new post', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(83,'default_post_edit_rows', 1, '9', 'The number of rows in the edit post form (min 3, max 100)', 8, 5)",

"INSERT INTO $wpdb->optiongroups (group_id,  group_name, group_desc) VALUES(7, 'Default post options', 'Default settings for new posts.')",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(7,55,1 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(7,56,2 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(7,57,3 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(7,58,4 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(7,59,5 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(7,83,5 )",

// select data for post_status
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (55, 'publish', 'Publish', null,null,1)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (55, 'draft',   'Draft',   null,null,2)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (55, 'private', 'Private', null,null,3)",

// select data for comment_status
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (56, 'open', 'Open',   null,null,1)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (56, 'closed', 'Closed', null,null,2)",

// select data for ping_status (aargh duplication!)
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (57, 'open', 'Open',   null,null,1)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (57, 'closed', 'Closed', null,null,2)",

// select data for pingback flag
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (58, '1', 'Checked',   null,null,1)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (58, '0', 'Unchecked', null,null,2)",

// sql select data for default
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (59, 'SELECT cat_id AS value, cat_name AS label FROM $wpdb->categories order by cat_name', '', null,null,1)",
);

foreach ($option_data as $query) {
    $q = $wpdb->query($query);
}
?>

<p>Option Data inserted okay.</p>


<?php
$links_option_data = array(
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(60,'links_minadminlevel',    1, '5', 'The minimum admin level to edit links', 8, 10)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(61,'links_use_adminlevels',  2, '1', 'set this to false to have all links visible and editable to everyone in the link manager', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(62,'links_rating_type',      5, 'image', 'Set this to the type of rating indication you wish to use', 8, 10)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(63,'links_rating_char',      3, '*', 'If we are set to \'char\' which char to use.', 8, 5)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(64,'links_rating_ignore_zero', 2, '1', 'What do we do with a value of zero? set this to true to output nothing, 0 to output as normal (number/image)', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(65,'links_rating_single_image',  2, '1', 'Use the same image for each rating point? (Uses links_rating_image[0])', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(66,'links_rating_image0',  3, 'wp-links/links-images/tick.png', 'Image for rating 0 (and for single image)', 8, 40)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(67,'links_rating_image1',  3, 'wp-links/links-images/rating-1.gif', 'Image for rating 1', 8, 40)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(68,'links_rating_image2',  3, 'wp-links/links-images/rating-2.gif', 'Image for rating 2', 8, 40)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(69,'links_rating_image3',  3, 'wp-links/links-images/rating-3.gif', 'Image for rating 3', 8, 40)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(70,'links_rating_image4',  3, 'wp-links/links-images/rating-4.gif', 'Image for rating 4', 8, 40)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(71,'links_rating_image5',  3, 'wp-links/links-images/rating-5.gif', 'Image for rating 5', 8, 40)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(72,'links_rating_image6',  3, 'wp-links/links-images/rating-6.gif', 'Image for rating 6', 8, 40)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(73,'links_rating_image7',  3, 'wp-links/links-images/rating-7.gif', 'Image for rating 7', 8, 40)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(74,'links_rating_image8',  3, 'wp-links/links-images/rating-8.gif', 'Image for rating 8', 8, 40)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(75,'links_rating_image9',  3, 'wp-links/links-images/rating-9.gif', 'Image for rating 9', 8, 40)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(77,'weblogs_xml_url',      3, 'http://www.weblogs.com/changes.xml', 'Which file to grab from weblogs.com', 8, 40)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(78,'weblogs_cacheminutes', 1, '60', 'cache time in minutes (if it is older than this get a new copy)', 8, 10)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(79,'links_updated_date_format',  3, 'd/m/Y h:i', 'The date format for the updated tooltip', 8, 25)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(80,'links_recently_updated_prepend',  3, '&gt;&gt;', 'The text to prepend to a recently updated link', 8, 10)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(81,'links_recently_updated_append',  3, '&lt;&lt;', 'The text to append to a recently updated link', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(82,'links_recently_updated_time',  1, '120', 'The time in minutes to consider a link recently updated', 8, 20)",

//group them together
"INSERT INTO $wpdb->optiongroups (group_id,  group_name, group_desc) VALUES(8, 'Link Manager Settings', 'Various settings for the link manager.')",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,60,1 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,61,2 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,62,3 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,63,4 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,64,5 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,65,6 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,66,7 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,67,8 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,68,9 )",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,69,10)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,70,11)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,71,12)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,72,13)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,73,14)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,74,15)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,75,16)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,76,17)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,77,18)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,78,19)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,79,20)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,80,21)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,81,22)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(8,82,23)",

// select data for rating_type
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (62, 'number', 'Number',    null,null,1)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (62, 'char',   'Character', null,null,2)",
"INSERT INTO $wpdb->optionvalues (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) VALUES (62, 'image',  'Image',     null,null,3)",
);

foreach ($links_option_data as $query) {
    $q = $wpdb->query($query);
}
?>

<p>Links option data inserted okay.</p>

<?php
$geo_option_data = array(
// data for geo settings
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(84,'use_geo_positions', 2, '0', 'Turns on the geo url features of WordPress', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(85,'use_default_geourl', 2, '1','enables placement of default GeoURL ICBM location even when no other specified', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(86,'default_geourl_lat ', 8, 0.0, 'The default Latitude ICBM value - <a href=\"http://www.geourl.org/resources.html\" target=\"_blank\">see here</a>', 8, 20)",
"INSERT INTO $wpdb->options (option_id, option_name, option_type, option_value, option_description, option_admin_level, option_width) VALUES(87,'default_geourl_lon', 8, 0.0, 'The default Longitude ICBM value', 8, 20)",

"INSERT INTO $wpdb->optiongroups (group_id, group_name, group_desc) VALUES(9,'Geo Options', 'Settings which control the posting and display of Geo Options')",

"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(9,84,1)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(9,85,1)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(9,86,1)",
"INSERT INTO $wpdb->optiongroup_options (group_id, option_id, seq) VALUES(9,87,1)",

);

foreach ($geo_option_data as $query) {
    $q = $wpdb->query($query);
}
?>

<p>Geo option data inserted okay.</p>


<p>OK. We're nearly done now. We just need to ask you a couple of things:</p>
<form action="install.php?step=3" method="post">
<input type="hidden" name="step" value="3" />
<p>What is the web address of your WordPress installation? (We probably guessed it for you correctly.) <br />
<?php
$guessurl = str_replace('/wp-admin/install.php?step=2', '', 'http://' . $HTTP_HOST . $REQUEST_URI);
?>
  <input name="url" type="text" size="60" value="<?php echo $guessurl; ?>" />
</p>
<p>On to 
    <input type="submit" value="Step 3..." />
</p>
</form>

<?php
	break;
	case 3:
?>
<h1>Step 3</h1>


<?php
$url = $_REQUEST['url'];
if (isset($url)) {
    $query= "UPDATE $wpdb->options set option_value='$url' where option_id=1"; //siteurl
    $q = $wpdb->query($query);
}

// $query = "DROP TABLE IF EXISTS $wpdb->users";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$wpdb->users\" in the database.");

$query = "
CREATE TABLE $wpdb->users (
  ID int(10) unsigned NOT NULL auto_increment,
  user_login varchar(20) NOT NULL default '',
  user_pass varchar(64) NOT NULL default '',
  user_firstname varchar(50) NOT NULL default '',
  user_lastname varchar(50) NOT NULL default '',
  user_nickname varchar(50) NOT NULL default '',
  user_icq int(10) unsigned NOT NULL default '0',
  user_email varchar(100) NOT NULL default '',
  user_url varchar(100) NOT NULL default '',
  user_ip varchar(15) NOT NULL default '',
  user_domain varchar(200) NOT NULL default '',
  user_browser varchar(200) NOT NULL default '',
  dateYMDhour datetime NOT NULL default '0000-00-00 00:00:00',
  user_level int(2) unsigned NOT NULL default '0',
  user_aim varchar(50) NOT NULL default '',
  user_msn varchar(100) NOT NULL default '',
  user_yim varchar(50) NOT NULL default '',
  user_idmode varchar(20) NOT NULL default '',
  PRIMARY KEY  (ID),
  UNIQUE KEY (user_login)
)
";
$q = $wpdb->query($query);

$random_password = substr(md5(uniqid(microtime())),0,6);

$query = "INSERT INTO $wpdb->users (ID, user_login, user_pass, user_firstname, user_lastname, user_nickname, user_icq, user_email, user_url, user_ip, user_domain, user_browser, dateYMDhour, user_level, user_aim, user_msn, user_yim, user_idmode) VALUES ( '1', 'admin', MD5('$random_password'), '', '', 'site admin', '0', '$admin_email', '', '127.0.0.1', '127.0.0.1', '', '00-00-0000 00:00:01', '10', '', '', '', 'nickname')";
$q = $wpdb->query($query);

// Do final updates
upgrade_all();
?>

<p>User setup successful!</p>

<p>Now you can <a href="../wp-login.php">log in</a> with the <strong>login</strong>
  "<code>admin</code>" and <strong>password</strong> "<code><?php echo $random_password; ?></code>".</p>
<p><strong><em>Note that password</em></strong> carefully! It is a <em>random</em>
  password that was generated just for you. If you lose it, you
  will have to delete the tables from the database yourself, and re-install WordPress.
</p>
<p>Were you expecting more steps? Sorry to disappoint. All done!</p>
<?php
	break;
}
?>
</body>
</html>