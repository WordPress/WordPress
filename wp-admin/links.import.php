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
require_once('../wp-config.php');
include_once("../wp-links/links.php");

$title = 'Import Blogroll';
$this_file = 'links.import.php';

$step = $HTTP_GET_VARS['step'];
if (!$step) $step = 0;
?>
<?php
switch ($step) {
    case 0:
    {
        $standalone = 0;
        include_once('b2header.php');
        if ($user_level < get_settings('links_minadminlevel'))
            die ("Cheatin&#8217; uh?");
        
        $opmltype='blogrolling'; // default.
?>
<div class="wrap">

    <h3>On this page you can import your blogroll.</h3>
	<form name="blogroll" action="links.import.php" method="get">
	<ol>
    <li>Go to <a href="http://www.blogrolling.com">Blogrolling.com</a>
    and sign in. Once you've done that, click on <strong>Get Code</strong>, and then
    look for the <strong><abbr title="Outline Processor Markup Language">OPML</abbr>
    code</strong><?php echo gethelp_link($this_file,'opml_code');?>.</li>
    <li>Or go to <a href="http://blo.gs">Blo.gs</a> and sign in. Once you've done
    that in the 'Welcome Back' box on the right, click on <strong>share</strong>, and then
    look for the <strong><abbr title="Outline Processor Markup Language">OPML</abbr>
    link</strong> (favorites.opml)<?php echo gethelp_link($this_file,'opml_code');?>.</li>

    <li>Select that text and copy it or copy the link/shortcut into the box below.<br />
       <input type="hidden" name="step" value="1" />
       Your OPML code:<?php echo gethelp_link($this_file,'opml_code');?> <input type="text" name="opml_url" size="65" />
	</li>
    <li>Did you use
        <input type="radio" name="opmltype" value="blogrolling" <?php echo(($opmltype == 'blogrolling') ? 'checked="checked"' : ''); ?>>blogrolling.com
      &nbsp;or&nbsp;<input type="radio" name="opmltype" value="blo.gs" <?php echo(($link_target == 'blo.gs') ? 'checked="checked"' : ''); ?>>blo.gs ?
    </li>

    <li>Now select a category you want to put these links in.<br />
	Category: <?php echo gethelp_link($this_file,'link_category');?><select name="cat_id">
<?php
	$categories = $wpdb->get_results("SELECT cat_id, cat_name, auto_toggle FROM $tablelinkcategories ORDER BY cat_id");
	foreach ($categories as $category) {
?>
    <option value="<?php echo $category->cat_id; ?>"><?php echo $category->cat_id.': '.$category->cat_name; ?></option>
<?php
        } // end foreach
?>
    </select>
	
	</li>

    <li><input type="submit" name="submit" value="Import!" /><?php echo gethelp_link($this_file,'import');?></li>
	</ol>
    </form>

</div>
<?php
                break;
            } // end case 0

    case 1: {
                $standalone = 0;
                include_once('b2header.php');
                if ($user_level < get_settings('links_minadminlevel'))
                    die ("Cheatin' uh ?");
?>
<div class="wrap">

     <h3>Importing...</h3>
<?php
                $cat_id = $HTTP_GET_VARS['cat_id'];
                if (($cat_id == '') || ($cat_id == 0)) {
                    $cat_id  = 1;
                }
                $opmltype = $HTTP_GET_VARS['opmltype'];
                if ($opmltype == '')
                $opmltype = 'blogrolling';
                $opml_url = $HTTP_GET_VARS['opml_url'];
                if ($opml_url == '') {
                    echo "<p>You need to supply your OPML url. Press back on your browser and try again</p>\n";
                }
                else
                {
                            
                    $opml = implode('', file($opml_url));

                    // Updated for new format thanks to Rantor http://wordpress.org/support/2/769
                    if ($opmltype == 'blogrolling') {
                        preg_match_all('/<outline text="(.*?)" type="(.*?)" url="(.*?)" (lastmod="(.*?)"|) target="(.*?)"*? \/>/', $opml, $items);
                        $names = $items[1];
                        $types = $items[2];
                        $urls = $items[3];
                        $titles = $items[5];
                        $targets = $items[6];
                    } else {
                        preg_match_all('/<outline type="(.*?)" text="(.*?)" url="(.*?)" \/>/', $opml, $items);
                        $types = $items[1];
                        $names = $items[2];
                        $urls = $items[3];
                    }
                    $link_count = count($names);
                    for ($i = 0; $i < $link_count; $i++) {
                        if ('Last' == substr($titles[$i], 0, 4))
                            $titles[$i] = '';
						if ('http' == substr($titles[$i], 0, 4))
                            $titles[$i] = '';
                        //echo "INSERT INTO $tablelinks (link_url, link_name, link_target, link_category, link_description, link_owner) VALUES('{$urls[$i]}', '{$names[$i]}', '{$targets[$i]}', $cat_id, '{$titles[$i]}', \$user_ID)<br />\n";
                        $query = "INSERT INTO $tablelinks (link_url, link_name, link_target, link_category, link_description, link_owner)
						VALUES('{$urls[$i]}', '".addslashes($names[$i])."', '{$targets[$i]}', $cat_id, '".addslashes($titles[$i])."', $user_ID)\n";
                        $result = $wpdb->query($query);
						echo "<p>Inserted <strong>{$names[$i]}</strong></p>";
                    }
?>
     <p>Inserted <?php echo $link_count ?> links into category <?php echo $cat_id; ?>. All done! Go <a href="linkmanager.php">manage those links</a>.</p>
<?php
                } // end else got url
?>

</div>
<?php
                break;
            } // end case 1
} // end switch
?>
</body>
</html>