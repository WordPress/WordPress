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
require('../b2config.php');
include_once('../wp-links/links.config.php');
include_once("../wp-links/links.php");

$title = 'Import Blogroll';

function mysql_doh($msg,$sql,$error) {
	echo "<p>$msg</p>";
	echo "<p>query:<br />$sql</p>";
	echo "<p>error:<br />$error</p>";
	die();
}

$connexion = mysql_connect($server, $loginsql, $passsql) or die("<h1>Check your b2config.php file!</h1>Can't connect to the database<br />".mysql_error());
$dbconnexion = mysql_select_db($base, $connexion);

if (!$dbconnexion) {
	echo mysql_error();
	die();
}
$step = $HTTP_GET_VARS['step'];
if (!$step) $step = 0;
?>
<?php
switch ($step) {
    case 0:
    {
        $standalone = 0;
        include_once('b2header.php');
        if ($user_level < $minadminlevel)
            die ("Cheatin' uh ?");
?>
<div class="wrap">
    <table width="75%" cellpadding="5" cellspacing="0" border="0">
    <tr><td>
    <h3>On this page you can import your blogroll.</h3>
    <p>You will need your unique blogrolling.com Id.</p>
    <p>First, go to <a href="http://www.blogrolling.com">Blogrolling.com</a>
    and sign in. Once you've done that, click on <b>Get Code</b>, and then
    look for the <b><abbr title="Outline Processor Markup Language">OPML</abbr>
    code</b>.</p>

    <p>Select that and copy it into the box below.</p>
    <form name="blogroll" action="links.import.php" method="GET">
       <input type="hidden" name="step" value="1" />
       Your OPML code: <input type="text" name="opml_url" size="65" />
    <p>Please select a category for these links.</p>
<?php
        $query = "SELECT cat_id, cat_name, auto_toggle FROM $tablelinkcategories ORDER BY cat_id";
        $result = mysql_query($query) or die("Couldn't execute query. ".mysql_error());
?>
    <p>Select category: <select name="cat_id">
<?php
        while($row = mysql_fetch_object($result)) {
?>
    <option value="<?php echo $row->cat_id; ?>"><?php echo $row->cat_id.': '.$row->cat_name; ?></option>
<?php
        } // end while
?>
    </select>
    <p>Finally, click on the 'Import' button and we're off!</p>
        <input type="submit" name="submit" value="Import" />
    </form>
        </td>
      </tr>
    </table>
</div>
<?php
                break;
            } // end case 0

    case 1: {
                $standalone = 0;
                include_once('b2header.php');
                if ($user_level < $minadminlevel)
                    die ("Cheatin' uh ?");
?>
<div class="wrap">
    <table width="75%" cellpadding="5" cellspacing="0" border="0">
    <tr><td>
     <h3>Importing...</h3>
<?php
                $cat_id = $HTTP_GET_VARS['cat_id'];
                if (($cat_id == '') || ($cat_id == 0)) {
                    $cat_id  = 1;
                }
                $opml_url = $HTTP_GET_VARS['opml_url'];
                if ($opml_url == '') {
                    echo "<p>You need to supply your OLPML url. Press back on your browser and try again</p>\n";
                }
                else
                {
                    $opml = implode('', file($opml_url));
                    preg_match_all('/<outline text="(.*?)" type="(.*?)" url="(.*?)" title="(.*?)" target="(.*?)"  \/>/', $opml, $items);
                    $names = $items[1];
                    $types = $items[2];
                    $urls = $items[3];
                    $titles = $items[4];
                    $targets = $items[5];
                    $link_count = count($names);
                    for ($i = 0; $i < count($names); $i++) {
                        if ('Last' == substr($titles[$i], 0, 4))
                            $titles[$i] = '';
                        //echo "INSERT INTO $tablelinks (link_url, link_name, link_target, link_category, link_description, link_owner) VALUES('{$urls[$i]}', '{$names[$i]}', '{$targets[$i]}', $cat_id, '{$titles[$i]}', \$user_ID)<br />\n";
                        $query = "INSERT INTO $tablelinks (link_url, link_name, link_target, link_category, link_description, link_owner)\n " .
                                 " VALUES('{$urls[$i]}', '".addslashes($names[$i])."', '{$targets[$i]}', $cat_id, '".addslashes($titles[$i])."', $user_ID)\n";
                        $result = mysql_query($query) or die("Couldn't insert link. Sorry".mysql_error());
                    }
?>
     <p>Inserted <?php echo $link_count ?> links into category <?php echo $cat_id; ?>.</p>
<?php
                } // end else got url
?>
        </td>
      </tr>
    </table>
</div>
<?php
                break;
            } // end case 1
} // end switch
?>
</body>
</html>