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

include_once('links.config.php');

/** function get_linksbyname()
 ** Gets the links associated with category 'cat_name'.
 ** Parameters:
 **   cat_name (default 'noname')  - The category name to use. If no
 **     match is found uses all
 **   before (default '')  - the html to output before the link
 **   after (default '<br />')  - the html to output after the link
 **   between (default ' ')  - the html to output between the link/image
 **     and it's description. Not used if no image or show_images == true
 **   show_images (default true) - whether to show images (if defined).
 **   orderby (default 'id') - the order to output the links. E.g. 'id', 'name',
 **     'url', 'description' or 'rating'. Or maybe owner. If you start the
 **     name with an underscore the order will be reversed.
 **     You can also specify 'rand' as the order which will return links in a
 **     random order.
 **   show_description (default true) - whether to show the description if
 **     show_images=false/not defined
 **   show_rating (default false) - show rating stars/chars
 **   limit (default -1) - Limit to X entries. If not specified, all entries
 **     are shown.
 **   show_updated (default 0) - whether to show last updated timestamp
 */
function get_linksbyname($cat_name = "noname", $before = '', $after = '<br />',
                         $between = " ", $show_images = true, $orderby = 'id',
                         $show_description = true, $show_rating = false,
                         $limit = -1, $show_updated = 0) {
    global $tablelinkcategories;
    $cat_id = -1;
    $sql = "SELECT cat_id FROM $tablelinkcategories WHERE cat_name='$cat_name'";
    $result = mysql_query($sql)
              or die("Oops, couldn't query the db for categories."
                     . $sql . mysql_error());
    if ($row = mysql_fetch_object($result)) {
        $cat_id = $row->cat_id;
    }
    get_links($cat_id, $before, $after, $between, $show_images, $orderby,
              $show_description, $show_rating, $limit, $show_updated);
}


/** function get_links()
 ** Gets the links associated with category n.
 ** Parameters:
 **   category (default -1)  - The category to use. If no category supplied
 **      uses all
 **   before (default '')  - the html to output before the link
 **   after (default '<br />')  - the html to output after the link
 **   between (default ' ')  - the html to output between the link/image
 **     and it's description. Not used if no image or show_images == true
 **   show_images (default true) - whether to show images (if defined).
 **   orderby (default 'id') - the order to output the links. E.g. 'id', 'name',
 **     'url', 'description', or 'rating'. Or maybe owner. If you start the
 **     name with an underscore the order will be reversed.
 **     You can also specify 'rand' as the order which will return links in a
 **     random order.
 **   show_description (default true) - whether to show the description if
 **    show_images=false/not defined .
 **   show_rating (default false) - show rating stars/chars
 **   limit (default -1) - Limit to X entries. If not specified, all entries
 **     are shown.
 **   show_updated (default 0) - whether to show last updated timestamp
 */
function get_links($category = -1, $before = '', $after = '<br />',
                   $between = " ", $show_images = true, $orderby = 'id',
                   $show_description = true, $show_rating = false,
                   $limit = -1, $show_updated = 0) {

    global $tablelinks, $links_rating_type, $links_rating_char,
        $links_rating_image, $links_rating_ignore_zero,
        $links_rating_single_image;

    $direction = ' ASC';
    $category_query = "";
    if ($category != -1) {
        $category_query = " AND link_category = $category ";
    }
    if ($show_updated) {
        $get_updated = ", DATE_FORMAT(link_updated, '%d/%m/%Y %h:%i') AS link_updated ";
    }
    dbconnect();
    $sql = "SELECT link_url, link_name, link_image, link_target, " .
           " link_description, link_rating, link_rel $get_updated " .
           " FROM $tablelinks " .
           " WHERE link_visible = 'Y' " .
           $category_query;
    if ($orderby == '')
        $orderby = 'id';
    if (substr($orderby,0,1) == '_') {
        $direction = ' DESC';
        $orderby = substr($orderby,1);
    }
    if (strcasecmp('rand',$orderby) == 0) {
        $orderby = 'rand()';
    } else {
        $orderby = " link_" . $orderby;
    }
    $sql .= ' ORDER BY ' . $orderby;
    $sql .= $direction;
    /* The next 2 lines implement LIMIT TO processing */
    if ($limit != -1)
        $sql .= " LIMIT $limit";

    $result = mysql_query($sql)
              or die("Couldn't execute query. " . $sql . mysql_error());
    while ($row = mysql_fetch_object($result)) {
        echo($before);
        $the_link = '#';
        if (($row->link_url != null) || ($row->link_url != '')) {
            $the_link = $row->link_url;
        }
        $rel = stripslashes($row->link_rel);
        if ($rel != '') {
            $rel = 'rel="'.$rel.'"';
        }
        echo("<a href=\"".$the_link."\" $rel title=\"" .
             stripslashes($row->link_description) .
             "\" target=\"$row->link_target\">");
        if (($row->link_image != null) && $show_images) {
            echo("<img src=\"$row->link_image\" border=\"0\" alt=\"" .
                 stripslashes($row->link_name) . "\" title=\"" .
                 stripslashes($row->link_description) . "\" />");
        } else {
            echo(stripslashes($row->link_name));
        }
        if ((($row->link_image != null) && $show_images) || !$show_description) {
            echo("</a>");
        } else {
            echo("</a>$between" . stripslashes($row->link_description));
        }

        if ($show_updated) {
            if (substr($row->link_updated,0,2) != '00') {
                echo("</a>$between " . $row->link_updated);
            }
        }
        // now do the rating
        if ($show_rating) {
            echo($between);
            if ($links_rating_type == 'number') {
                if (($row->link_rating != 0) || ($links_rating_ignore_zero != 1)) {
                    echo(" $row->link_rating\n");
                }
            } else if ($links_rating_type == 'char') {
                for ($r = $row->link_rating; $r > 0; $r--) {
                    echo($links_rating_char);
                }
            } else if ($links_rating_type == 'image') {
                if ($links_rating_single_image) {
                    for ($r = $row->link_rating; $r > 0; $r--) {
                        echo(' <img src="'.$links_rating_image[0].'" alt="' .
                             $row->link_rating.'" />'."\n");
                    }
                } else {
                    if (($row->link_rating != 0) || ($links_rating_ignore_zero != 1)) {
                        echo(' <img src="' .
                             $links_rating_image[$row->link_rating].'" alt="' .
                             $row->link_rating.'" />'."\n");
                    }
                }
            } // end if image
        } // end if show_rating
        echo("$after\n");
    } // end while
}

/** function get_linksbyname_withrating()
 ** Gets the links associated with category 'cat_name' and display rating stars/chars.
 ** Parameters:
 **   cat_name (default 'noname')  - The category name to use. If no
 **     match is found uses all
 **   before (default '')  - the html to output before the link
 **   after (default '<br />')  - the html to output after the link
 **   between (default ' ')  - the html to output between the link/image
 **     and it's description. Not used if no image or show_images == true
 **   show_images (default true) - whether to show images (if defined).
 **   orderby (default 'id') - the order to output the links. E.g. 'id', 'name',
 **     'url' or 'description'. Or maybe owner. If you start the
 **     name with an underscore the order will be reversed.
 **     You can also specify 'rand' as the order which will return links in a
 **     random order.
 **   show_description (default true) - whether to show the description if
 **     show_images=false/not defined
 **   limit (default -1) - Limit to X entries. If not specified, all entries
 **     are shown.
 **   show_updated (default 0) - whether to show last updated timestamp
 */
function get_linksbyname_withrating($cat_name = "noname", $before = '',
                                    $after = '<br />', $between = " ",
                                    $show_images = true, $orderby = 'id',
                                    $show_description = true, $limit = -1, $show_updated = 0) {

    get_linksbyname($cat_name, $before, $after, $between, $show_images,
                    $orderby, $show_description, true, $limit, $show_updated);
}

/** function get_links_withrating()
 ** Gets the links associated with category n and display rating stars/chars.
 ** Parameters:
 **   category (default -1)  - The category to use. If no category supplied
 **      uses all
 **   before (default '')  - the html to output before the link
 **   after (default '<br />')  - the html to output after the link
 **   between (default ' ')  - the html to output between the link/image
 **     and it's description. Not used if no image or show_images == true
 **   show_images (default true) - whether to show images (if defined).
 **   orderby (default 'id') - the order to output the links. E.g. 'id', 'name',
 **     'url' or 'description'. Or maybe owner. If you start the
 **     name with an underscore the order will be reversed.
 **     You can also specify 'rand' as the order which will return links in a
 **     random order.
 **   show_description (default true) - whether to show the description if
 **    show_images=false/not defined .
 **   limit (default -1) - Limit to X entries. If not specified, all entries
 **     are shown.
 **   show_updated (default 0) - whether to show last updated timestamp
 */
function get_links_withrating($category = -1, $before = '', $after = '<br />',
                              $between = " ", $show_images = true,
                              $orderby = 'id', $show_description = true,
                              $limit = -1, $show_updated = 0) {

    get_links($category, $before, $after, $between, $show_images, $orderby,
              $show_description, true, $limit, $show_updated);
}

/** function get_linkcatname()
 ** Gets the name of category n.
 ** Parameters: id (default 0)  - The category to get. If no category supplied
 **                uses 0
 */
function get_linkcatname($id = 0) {
    global $tablelinkcategories;
    $cat_name = "noname";
    $sql = "SELECT cat_name FROM $tablelinkcategories WHERE cat_id=$id";
    $result = mysql_query($sql)
              or die("Couldn't query the db for categories:". $sql. mysql_error());
    if ($row = mysql_fetch_object($result)) {
        $cat_name = stripslashes($row->cat_name);
    }
    return $cat_name;
}

/** function get_get_autotoggle()
 ** Gets the auto_toggle setting of category n.
 ** Parameters: id (default 0)  - The category to get. If no category supplied
 **                uses 0
 */
function get_autotoggle($id = 0) {
    global $tablelinkcategories;
    $auto_toggle = "N";
		$sql = "SELECT auto_toggle FROM $tablelinkcategories WHERE cat_id=$id";
		$result = mysql_query($sql)
                  or die("Couldn't query the db for categories:". $sql.
                         mysql_error());
		if ($row = mysql_fetch_object($result)) {
			$auto_toggle = $row->auto_toggle;
		}
	return $auto_toggle;
}

/** function links_popup_script()
 ** This function contributed by Fullo -- http://sprite.csr.unibo.it/fullo/
 ** Show the link to the links popup and the number of links
 ** Parameters:
 **   text (default Links)  - the text of the link
 **   width (default 400)  - the width of the popup window
 **   height (default 400)  - the height of the popup window
 **   file (default linkspopup.php) - the page to open in the popup window
 **   count (default true) - the number of links in the db
 */
function links_popup_script($text = 'Links', $width=400, $height=400,
                            $file='links.all.php', $count = true) {
   global $tablelinks;
   if ($count == true) {
      $sql = "SELECT count(*) FROM $tablelinks";
      $result = mysql_query($sql)
                or die("Couldn't query the db for categories:". $sql .
                       mysql_error());

      if ($row = mysql_fetch_row($result)) {
         $counts = $row[0];
      }
   }

   $javascript = "<a href=\"#\" " .
                 " onclick=\"javascript:window.open('$file?popup=1', '_blank', " .
                 "'width=$width,height=$height,scrollbars=yes,status=no'); " .
                 " return false\">";
   $javascript .= $text;

   if ($count == true) {
      $javascript .= " ($counts)";
   }

   $javascript .="</a>\n\n";
   echo $javascript;
}

?>
