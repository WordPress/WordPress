<?php
require_once('../wp-config.php');


$step = $HTTP_GET_VARS['step'];
if (!$step) $step = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<title>WordPress > Installation</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<style media="screen" type="text/css">
	body {
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 15%;
		margin-right: 15%;
	}
	#logo {
		margin: 0;
		padding: 0;
		background-image: url(http://wordpress.org/images/wordpress.gif);
		background-repeat: no-repeat;
		height: 72px;
		border-bottom: 4px solid #333;
	}
	#logo a {
		display: block;
		height: 72px;
	}
	#logo a span {
		display: none;
	}
	p {
		line-height: 140%;
	}
	</style>
</head>
<body>
<h1 id="logo"><a href="http://wordpress.org"><span>WordPress</span></a></h1>
<?php
switch($step) {

	case 0:
?>
<p>Welcome to WordPress. We&#8217;re now going to go through a few steps to get 
  you up and running with the latest in personal publishing platforms. Before 
  we get started, remember that we require a PHP version of at least 4.0.6, you 
  have <?php echo phpversion(); ?>. Look good? You also need to set up the database 
  connection information in <code>wp-config.php</code>. Have you looked at the 
  <a href="../readme.html">readme</a>? If you&#8217;re all ready, <a href="wp-install.php?step=1">let's 
  go</a>! </p>
<?php
	break;
	
	case 1:
?>
<h1>Step 1</h1>
<p>Okay first we&#8217;re going to set up the links database. This will allow you to host your own blogroll, complete with Weblogs.com updates.</p>
<?php
require_once('../wp-links/links.config.php');

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
    if ($row[0] == $tablelinks)
        $got_links = true;
    if ($row[0] == $tablelinkcategories)
        $got_cats = true;
    //print "Table: $row[0]<br />\n";
}
if (!$got_cats) {
    echo "<p>Can't find table '$tablelinkcategories', gonna create it...</p>\n";
    $sql = "CREATE TABLE $tablelinkcategories ( " .
           " cat_id int(11) NOT NULL auto_increment, " .
           " cat_name tinytext NOT NULL, ".
           " auto_toggle enum ('Y','N') NOT NULL default 'N', ".
           " PRIMARY KEY (cat_id) ".
           ") ";
    $result = mysql_query($sql) or print ("Can't create the table '$tablelinkcategories' in the database.<br />" . $sql . "<br />" . mysql_error());
    if ($result != false) {
        echo "<p>Table '$tablelinkcategories' created OK</p>\n";
        $got_cats = true;
    }
} else {
    echo "<p>Found table '$tablelinkcategories', don't need to create it...</p>\n";
        $got_cats = true;
}
if (!$got_links) {
    echo "<p>Can't find '$tablelinks', gonna create it...</p>\n";
    $sql = "CREATE TABLE $tablelinks ( " .
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
           " PRIMARY KEY (link_id)                              " .
           ") ";
    $result = mysql_query($sql) or print ("Can't create the table '$tablelinks' in the database.<br />" . $sql . "<br />" . mysql_error());
	$links = mysql_query("INSERT INTO $tablelinks VALUES ('', 'http://wordpress.org', 'WordPress', '', '', 1, '', 'Y', 1, 0, '0000-00-00 00:00:00', '');");
	$links = mysql_query("INSERT INTO $tablelinks VALUES ('', 'http://cafelog.com', 'b2', '', '', 1, '', 'Y', 1, 0, '0000-00-00 00:00:00', '');");
	$links = mysql_query("INSERT INTO $tablelinks VALUES ('', 'http://photomatt.net', 'Matt', '', '', 1, '', 'Y', 1, 0, '0000-00-00 00:00:00', '');");
	$links = mysql_query("INSERT INTO $tablelinks VALUES ('', 'http://zed1.com/b2/', 'Mike', '', '', 1, '', 'Y', 1, 0, '0000-00-00 00:00:00', '');");



    if ($result != false) {
        echo "<p>Table '$tablelinks' created OK</p>\n";
        $got_links = true;
    }
} else {
    echo "<p>Found table '$tablelinks', don't need to create it...</p>\n";
        $got_links = true;
}

if ($got_links && $got_cats) {
    echo "<p>Looking for category 1...</p>\n";
    $sql = "SELECT * FROM $tablelinkcategories WHERE cat_id=1 ";
    $result = mysql_query($sql) or print ("Can't query '$tablelinkcategories'.<br />" . $sql . "<br />" . mysql_error());
    if ($result != false) {
        if ($row = mysql_fetch_object($result)) {
            echo "<p>You have at least 1 category. Good!</p>\n";
            $got_row = true;
        } else {
            echo "<p>Gonna insert category 1...</p>\n";
            $sql = "INSERT INTO $tablelinkcategories (cat_id, cat_name) VALUES (1, 'General')";
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
<p>Did you defeat the boss monster at the end? Great! You&#8217;re ready for <a href="wp-install.php?step=2">Step 
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

// $query = "DROP TABLE IF EXISTS $tableposts";
// $q = mysql_query($query) or die ("doh, can't drop the table \"$tableposts\" in the database.");

$query = "CREATE TABLE $tableposts (
  ID int(10) unsigned NOT NULL auto_increment,
  post_author int(4) NOT NULL default '0',
  post_date datetime NOT NULL default '0000-00-00 00:00:00',
  post_content text NOT NULL,
  post_title text NOT NULL,
  post_category int(4) NOT NULL default '0',
  post_excerpt text NOT NULL,
  post_status enum('publish','draft','private') NOT NULL default 'publish',
  comment_status enum('open','closed') NOT NULL default 'open',
  ping_status enum('open','closed') NOT NULL default 'open',
  post_password varchar(20) NOT NULL default '',
  PRIMARY KEY  (ID),
  KEY post_status (post_status)
)
";
$q = $wpdb->query($query);
?>

<p>The first table has been created! ...</p>

<?php
$now = date('Y-m-d H:i:s');
$query = "INSERT INTO $tableposts (post_author, post_date, post_content, post_title, post_category) VALUES ('1', '$now', 'Welcome to WordPress. This is the first post. Edit or delete it, then start blogging!', 'Hello world!', '1')";

$q = $wpdb->query($query);
?>

<p>The test post has been inserted correctly...</p>

<?php
// $query = "DROP TABLE IF EXISTS $tablecategories";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$tablecategories\" in the database.");

$query = "
CREATE TABLE $tablecategories (
  cat_ID int(4) NOT NULL auto_increment,
  cat_name varchar(55) NOT NULL default '',
  PRIMARY KEY  (cat_ID)
)
";
$q = $wpdb->query($query);

$query = "INSERT INTO $tablecategories (cat_ID, cat_name) VALUES ('0', 'General')";
$q = $wpdb->query($query);

$query = "UPDATE $tableposts SET post_category = 1";
$result = $wpdb->query($query);
?>

<p>Categories are up and running...</p>

<?php
// $query = "DROP TABLE IF EXISTS $tablecomments";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$tablecomments\" in the database.");

$query = "
CREATE TABLE $tablecomments (
  comment_ID int(11) unsigned NOT NULL auto_increment,
  comment_post_ID int(11) NOT NULL default '0',
  comment_author tinytext NOT NULL,
  comment_author_email varchar(100) NOT NULL default '',
  comment_author_url varchar(100) NOT NULL default '',
  comment_author_IP varchar(100) NOT NULL default '',
  comment_date datetime NOT NULL default '0000-00-00 00:00:00',
  comment_content text NOT NULL,
  comment_karma int(11) NOT NULL default '0',
  PRIMARY KEY  (comment_ID)
)
";
$q = $wpdb->query($query);

$now = date('Y-m-d H:i:s');
$query = "INSERT INTO $tablecomments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_content) VALUES ('1', 'Mr WordPress', 'mr@wordpress.org', 'http://wordpress.org', '127.0.0.1', '$now', 'Hi, this is a comment.<br />To delete a comment, just log in, and view the posts\' comments, there you will have the option to edit or delete them.')";
$q = $wpdb->query($query);
?>

<p>Comments are groovy...</p>

<?php
// $query = "DROP TABLE IF EXISTS $tablesettings";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$tablesettings\" in the database.");

$query = "
CREATE TABLE $tablesettings (
  ID tinyint(3) NOT NULL default '1',
  posts_per_page int(4) unsigned NOT NULL default '7',
  what_to_show varchar(5) NOT NULL default 'days',
  archive_mode varchar(10) NOT NULL default 'weekly',
  time_difference tinyint(4) NOT NULL default '0',
  AutoBR tinyint(1) NOT NULL default '1',
  time_format varchar(20) NOT NULL default 'H:i:s',
  date_format varchar(20) NOT NULL default 'd.m.y',
  PRIMARY KEY  (ID)
)
";
$q = $wpdb->query($query);

$query = "INSERT INTO $tablesettings ( ID, posts_per_page, what_to_show, archive_mode, time_difference, AutoBR, time_format, date_format) VALUES ( '1', '20', 'posts', 'monthly', '0', '1', 'g:i a', 'n/j/Y')";
$q = $wpdb->query($query);
?>

<p>Settings are okay.</p>

<?php
// $query = "DROP TABLE IF EXISTS $tableusers";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$tableusers\" in the database.");

$query = "
CREATE TABLE $tableusers (
  ID int(10) unsigned NOT NULL auto_increment,
  user_login varchar(20) NOT NULL default '',
  user_pass varchar(20) NOT NULL default '',
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

$query = "INSERT INTO $tableusers (ID, user_login, user_pass, user_firstname, user_lastname, user_nickname, user_icq, user_email, user_url, user_ip, user_domain, user_browser, dateYMDhour, user_level, user_aim, user_msn, user_yim, user_idmode) VALUES ( '1', 'admin', '$random_password', '', '', 'admin', '0', '$admin_email', '', '127.0.0.1', '127.0.0.1', '', '00-00-0000 00:00:01', '10', '', '', '', 'nickname')";
$q = $wpdb->query($query);

?>

<p>User setup successful!</p>

<p>Now you can <a href="../b2login.php">log in</a> with the <strong>login</strong> 
  "admin" and <strong>password</strong> "<?php echo $random_password; ?>".</p>
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