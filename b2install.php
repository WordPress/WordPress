<html>
<head>
<title>b2 > Installation</title>
</head>
<body>

<?php
include ("b2config.php");

function mysql_doh($msg,$sql,$error) {
	echo "<p>$msg</p>";
	echo "<p>query:<br />$sql</p>";
	echo "<p>error:<br />$error</p>";
	die();
}

$connexion = mysql_connect($server,$loginsql,$passsql) or die("Can't connect to the database<br>".mysql_error());
$dbconnexion = mysql_select_db($base, $connexion);

if (!$dbconnexion) {
	echo mysql_error();
	die();
}

echo "Now creating the necessary tables in the database...<br /><br />";


# Note: if you want to start again with a clean b2 database,
#       just remove the // in this file

// $query = "DROP TABLE IF EXISTS $tableposts";
// $q = mysql_query($query) or die ("doh, can't drop the table \"$tableposts\" in the database.");

$query = "CREATE TABLE $tableposts ( ID int(10) unsigned NOT NULL auto_increment, post_author int(4) DEFAULT '0' NOT NULL, post_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL, post_content text NOT NULL, post_title text NOT NULL, post_category int(4) DEFAULT '0' NOT NULL, post_karma int(11) DEFAULT '0' NOT NULL, PRIMARY KEY (ID), UNIQUE ID (ID) )";
$q = mysql_query($query) or mysql_doh("doh, can't create the table \"$tableposts\" in the database.", $query, mysql_error());

$now = date('Y-m-d H:i:s');
$query = "INSERT INTO $tableposts (post_author, post_date, post_content, post_title, post_category) VALUES ('1', '$now', 'This is the first post. Edit or delete it, then start blogging !', 'Hello world !', '1')";
$q = mysql_query($query) or mysql_doh("doh, can't insert a first post in the table \"$tableposts\" in the database.", $query, mysql_error());

echo "posts: OK<br />";

// $query = "DROP TABLE IF EXISTS $tablecategories";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$tablecategories\" in the database.");

$query="CREATE TABLE $tablecategories (cat_ID int(4) NOT NULL auto_increment, cat_name TINYTEXT not null , KEY (cat_ID))";
$q = mysql_query($query) or mysql_doh("doh, can't create the table \"$tablecategories\" in the database.", $query, mysql_error());

$query = "INSERT INTO $tablecategories (cat_ID, cat_name) VALUES ('0', 'General')";
$q = mysql_query($query) or mysql_doh("doh, can't set the default category in the table \"$tablecategories\" in the database.", $query, mysql_error());

$query = "UPDATE $tableposts SET post_category=\"1\"";
$result = mysql_query($query) or mysql_doh("Oops, can't set the default category on $tableposts.", $query, mysql_error());

echo "b2categories: OK<br />";

// $query = "DROP TABLE IF EXISTS $tablecomments";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$tablecomments\" in the database.");

$query = "CREATE TABLE $tablecomments ( comment_ID int(11) unsigned NOT NULL auto_increment, comment_post_ID int(11) DEFAULT '0' NOT NULL, comment_author tinytext NOT NULL, comment_author_email varchar(100) NOT NULL, comment_author_url varchar(100) NOT NULL, comment_author_IP varchar(100) NOT NULL, comment_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL, comment_content text NOT NULL, comment_karma int(11) DEFAULT '0' NOT NULL, PRIMARY KEY (comment_ID) )";
$q = mysql_query($query) or mysql_doh("doh, can't create the table \"$tablecomments\" in the database.", $query, mysql_error());

$now = date('Y-m-d H:i:s');
$query = "INSERT INTO $tablecomments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_content) VALUES ('1', 'miss b2', 'missb2@example.com', 'http://example.com', '127.0.0.1', '$now', 'Hi, this is a comment.<br />To delete a comment, just log in, and view the posts\' comments, there you will have the option to edit or delete them.')";
$q = mysql_query($query) or mysql_doh("doh, can't insert a first comment in the table \"$tablecomments\" in the database.", $query, mysql_error());

echo "comments: OK<br />";

// $query = "DROP TABLE IF EXISTS $tablesettings";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$tablesettings\" in the database.");

$query = "CREATE TABLE $tablesettings ( ID tinyint(3) DEFAULT '1' NOT NULL, posts_per_page int(4) unsigned DEFAULT '7' NOT NULL, what_to_show varchar(5) DEFAULT 'days' NOT NULL, archive_mode varchar(10) DEFAULT 'weekly' NOT NULL, time_difference tinyint(4) DEFAULT '0' NOT NULL, AutoBR tinyint(1) DEFAULT '1' NOT NULL, time_format varchar(20) DEFAULT 'H:i:s' NOT NULL, date_format varchar(20) DEFAULT 'Y/m/d' NOT NULL, PRIMARY KEY (ID), KEY ID (ID) )";
$q = mysql_query($query) or mysql_doh("doh, can't create the table \"$tablesettings\" in the database.", $query, mysql_error());

$query = "INSERT INTO $tablesettings ( ID, posts_per_page, what_to_show, archive_mode, time_difference, AutoBR, time_format, date_format) VALUES ( '1', '20', 'posts', 'monthly', '0', '1', 'H:i:s', 'd.m.y')";
$q = mysql_query($query) or mysql_doh("doh, can't set the default settings in the table \"$tablesettings\" in the database.", $query, mysql_error());

echo "settings: OK<br />";

// $query = "DROP TABLE IF EXISTS $tableusers";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$tableusers\" in the database.");

$query = "CREATE TABLE $tableusers ( ID int(10) unsigned NOT NULL auto_increment, user_login varchar(20) NOT NULL, user_pass varchar(20) NOT NULL, user_firstname varchar(50) NOT NULL, user_lastname varchar(50) NOT NULL, user_nickname varchar(50) NOT NULL, user_icq int(10) unsigned DEFAULT '0' NOT NULL, user_email varchar(100) NOT NULL, user_url varchar(100) NOT NULL, user_ip varchar(15) NOT NULL, user_domain varchar(200) NOT NULL, user_browser varchar(200) NOT NULL, dateYMDhour datetime DEFAULT '0000-00-00 00:00:00' NOT NULL, user_level int(2) unsigned DEFAULT '0' NOT NULL, user_aim varchar(50) NOT NULL, user_msn varchar(100) NOT NULL, user_yim varchar(50) NOT NULL, user_idmode varchar(20) NOT NULL, PRIMARY KEY (ID), UNIQUE ID (ID), UNIQUE (user_login) )";
$q = mysql_query($query) or mysql_doh("doh, can't create the table \"$tableusers\" in the database.", $query, mysql_error());

$random_password = substr(md5(uniqid(microtime())),0,6);

$query = "INSERT INTO $tableusers (ID, user_login, user_pass, user_firstname, user_lastname, user_nickname, user_icq, user_email, user_url, user_ip, user_domain, user_browser, dateYMDhour, user_level, user_aim, user_msn, user_yim, user_idmode) VALUES ( '1', 'admin', '$random_password', '', '', 'admin', '0', '$admin_email', '', '127.0.0.1', '127.0.0.1', '', '00-00-0000 00:00:01', '10', '', '', '', 'nickname')";
$q = mysql_query($query) or mysql_doh("doh, can't set the default user in the table \"$tableusers\" in the database.", $query, mysql_error());

echo "users: OK<br />";

require_once("wp-links/links.install.php");
?>

<br />
Installation successful !<br />
<br/ >
Now you can <a href="b2login.php">log in</a> with the login "admin" and password "<?php echo $random_password; ?>".<br /><br />
<br />
Note that password carefully ! It is a <em>random</em> password that is given to you when you install b2. If you lose it, you will have to delete the tables from the database yourself, and re-install b2.

</body>
</html>