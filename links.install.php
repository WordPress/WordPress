<?php
// $Id$
//
// Links
// Copyright (C) 2002 Mike Little -- mike@zed1.com
//
// This is an add-on to b2 weblog / news publishing tool
// b2 is copyright (c)2001, 2002 by Michel Valdrighi - m@tidakada.com
//
// **********************************************************************
// Copyright (C) 2002 Mike Little
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
// General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
//
// Mike Little (mike@zed1.com)
// *****************************************************************

// Start Connect To MYSQL
require_once("b2config.php");
include_once("$b2inc/b2template.functions.php");
include_once("$b2inc/b2vars.php");
include_once("$b2inc/b2functions.php");

include_once('links.config.php');
$connexion = mysql_connect($server,$loginsql,$passsql) or die("Can't connect to the database<br>".mysql_error());
$dbconnexion = mysql_select_db($base, $connexion);

$got_links = false;
$got_cats = false;
$got_row = false;
?><html>
<head>
<title>links > Installation</title>
</head>
<body>
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
           " PRIMARY KEY (link_id)                              " .
           ") ";
    $result = mysql_query($sql) or print ("Can't create the table '$tablelinks' in the database.<br />" . $sql . "<br />" . mysql_error());
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
            echo "<p>You have at least got category 1. Good!</p>\n";
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
    echo "<p>All done.</p>\n";
}
?>
</body>
</html>
