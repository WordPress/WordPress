<?php
require('../b2config.php');

function mysql_doh($msg,$sql,$error) {
	echo "<p>$msg</p>";
	echo "<p>query:<br />$sql</p>";
	echo "<p>error:<br />$error</p>";
	die();
}

$connexion = mysql_connect($server, $loginsql, $passsql) or die("Can't connect to the database<br />".mysql_error());
$dbconnexion = mysql_select_db($base, $connexion);

if (!$dbconnexion) {
	echo mysql_error();
	die();
}
$step = $HTTP_GET_VARS['step'];
if (!$step) $step = 0;
if (!step) $step = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<title>WordPress > b2 Conversion</title>
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
<p>Welcome to WordPress. Since you're upgrading from b2 everything should be relatively 
  familiar to you. Here are some notes on upgrading:</p>
<ul>
  <li>If you're using an older version of b2, it's probably a good idea to upgrade 
    to at least .6.1 before making the leap to WordPress.</li>
  <li>The templates are so much better, and there is so much more going on than 
    before it's probably worth it to start from scratch and work back to your 
    design.</li>
  <li>You can keeep your <code>b2config.php</code> file if you want to, but it 
    is <strong>very important</strong> that you take the last few lines from the 
    WordPress one and add those in, otherwise, nothing will work.</li>
  <li>WordPress issues should be discussed in our <a href="http://wordpress.org/support/">support 
    forums</a>.</li>
  <li><strong>Back up</strong> your database before you do anything. Yes, you. 
    Right now.</li>
</ul>
<p><code></code>Have you looked at the <a href="../readme.html">readme</a>? If 
  you&#8217;re all ready, <a href="b2-2-wp.php?step=1">let's go</a>! </p>
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
$result = mysql_list_tables($dbname);
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
    echo "<p>... may need to update it though. Looking for column link_updated...</p>\n";
    $query = "SELECT link_updated FROM $tablelinks LIMIT 1";
    $q = @mysql_query($query);
    if ($q != false) {
        if ($row = mysql_fetch_object($q)) {
            echo "<p>You have  column link_updated. Good!</p>\n";
        }
    } else {
        $query = "ALTER TABLE $tablelinks ADD COLUMN link_updated DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'";
        $q = mysql_query($query) or mysql_doh("Doh, couldn't add column.", $query, mysql_error());
        echo "<p>Added column link_updated...</p>\n";
    }
    echo "<p>Looking for column link_rel...</p>\n";
    $query = "SELECT link_rel FROM $tablelinks LIMIT 1";
    $q = @mysql_query($query);
    if ($q != false) {
        if ($row = mysql_fetch_object($q)) {
            echo "<p>You have column link_rel. Good!</p>\n";
        }
    } else {
        $query = "ALTER TABLE $tablelinks ADD COLUMN link_rel varchar(255) NOT NULL DEFAULT '' ";
        $q = mysql_query($query) or mysql_doh("Doh, couldn't add column.", $query, mysql_error());
        echo "<p>Added column link_rel...</p>\n";
    }
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
<p>Did you defeat the boss monster at the end? Good, then you&#8217;re ready for 
  <a href="b2-2-wp.php?step=2">Step 2</a>.</p>
<?php
	break;
	case 2:
?>
<h1>Step 2</h1>
<p>First we&#8217;re going to add excerpt functionality...</p>

<?php

$query = "ALTER TABLE $tableposts ADD COLUMN post_excerpt text NOT NULL;";
$q = mysql_query($query) or mysql_doh("Doh, add excerpts.", $query, mysql_error());
?>

<p>That went well! Now let's clean up the b2 database structure a bit...</p>

<?php
$query = "ALTER TABLE $tableposts DROP INDEX `ID`";

$q = mysql_query($query) or mysql_doh("Can't drop the ID index. Did you already fix this?", $query, mysql_error());
?>

<p>One down, two to go...</p>

<?php

$query="ALTER TABLE $tablesettings DROP INDEX `ID`";
$q = mysql_query($query) or mysql_doh("Can't drop ID from the settings table. Do it already?", $query, mysql_error());

?>

<p>Almost there...</p>

<?php

$query = "ALTER TABLE $tableusers DROP INDEX `ID`";

$q = mysql_query($query) or mysql_doh("Couldn't drop index from users table.", $query, mysql_error());

?>

<p>All done!</p>

<p>Don't forget to CHMOD the <code>weblogs.com.changes.cache</code> file and you'll 
  be A-okay. Welcome to the family.</p>
<?php
	break;
}
?>

</body>
</html>