<?php

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
    global $wpdb;
    $cat_id = -1;
    $results = $wpdb->get_results("SELECT cat_id FROM $wpdb->linkcategories WHERE cat_name='$cat_name'");
    if ($results) {
        foreach ($results as $result) {
            $cat_id = $result->cat_id;
        }
    }
    get_links($cat_id, $before, $after, $between, $show_images, $orderby,
              $show_description, $show_rating, $limit, $show_updated);
}

function bool_from_yn($yn) {
    if ($yn == 'Y') return 1;
    return 0;
}

/** function wp_get_linksbyname()
 ** Gets the links associated with the named category.
 ** Parameters:
 **   category (no default)  - The category to use.
 **/
function wp_get_linksbyname($category) {
    global $wpdb;

    $cat = $wpdb->get_row("SELECT cat_id, cat_name, auto_toggle, show_images, show_description, "
         . " show_rating, show_updated, sort_order, sort_desc, text_before_link, text_after_link, "
         . " text_after_all, list_limit FROM $wpdb->linkcategories WHERE cat_name='$category'");
    if ($cat) {
        if ($cat->sort_desc == 'Y') {
            $cat->sort_order = '_'.$cat->sort_order;
        }
        get_links($cat->cat_id, $cat->text_before_link, $cat->text_after_all,
                  $cat->text_after_link, bool_from_yn($cat->show_images), $cat->sort_order,
                   bool_from_yn($cat->show_description), bool_from_yn($cat->show_rating),
                   $cat->list_limit, bool_from_yn($cat->show_updated));
    }
} // end wp_get_linksbyname

/** function wp_get_links()
 ** Gets the links associated with category n.
 ** Parameters:
 **   category (no default)  - The category to use.
 **/
function wp_get_links($category) {
    global $wpdb;

    $cat = $wpdb->get_row("SELECT cat_id, cat_name, auto_toggle, show_images, show_description, "
         . " show_rating, show_updated, sort_order, sort_desc, text_before_link, text_after_link, "
         . " text_after_all, list_limit FROM $wpdb->linkcategories WHERE cat_id=$category");
    if ($cat) {
        if ($cat->sort_desc == 'Y') {
            $cat->sort_order = '_'.$cat->sort_order;
        }
        get_links($cat->cat_id, $cat->text_before_link, $cat->text_after_all,
                  $cat->text_after_link, bool_from_yn($cat->show_images), $cat->sort_order,
                   bool_from_yn($cat->show_description), bool_from_yn($cat->show_rating),
                   $cat->list_limit, bool_from_yn($cat->show_updated));
    }
} // end wp_get_links

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
                   $between = ' ', $show_images = true, $orderby = 'name',
                   $show_description = true, $show_rating = false,
                   $limit = -1, $show_updated = 1, $echo = true) {

    global $wpdb;

    $direction = ' ASC';
    $category_query = "";
    if ($category != -1) {
        $category_query = " AND link_category = $category ";
    }
    if (get_settings('links_recently_updated_time')) {
        $recently_updated_test = ", IF (DATE_ADD(link_updated, INTERVAL ".get_settings('links_recently_updated_time')." MINUTE) >= NOW(), 1,0) as recently_updated ";
    } else {
		$recently_updated_test = '';
	}
    if ($show_updated) {
        $get_updated = ", UNIX_TIMESTAMP(link_updated) AS link_updated_f ";
    }

    $orderby=strtolower($orderby);
    if ($orderby == '')
        $orderby = 'id';
    if (substr($orderby,0,1) == '_') {
        $direction = ' DESC';
        $orderby = substr($orderby,1);
    }

    switch($orderby) {
        case 'length':
        $length = ",CHAR_LENGTH(link_name) AS length";
        break;
        case 'rand':
            $orderby = 'rand()';
            break;
        default:
            $orderby = " link_" . $orderby;
    }

    if (!isset($length)) {
		$length = "";
	}

    $sql = "SELECT link_url, link_name, link_image, link_target,
            link_description, link_rating, link_rel $length $recently_updated_test $get_updated
            FROM $wpdb->links
            WHERE link_visible = 'Y' " .
           $category_query;
    $sql .= ' ORDER BY ' . $orderby;
    $sql .= $direction;
    /* The next 2 lines implement LIMIT TO processing */
    if ($limit != -1)
        $sql .= " LIMIT $limit";
    //echo $sql;
    $results = $wpdb->get_results($sql);
    if (!$results) {
        return;
    }
    foreach ($results as $row) {
		if (!isset($row->recently_updated)) $row->recently_updated = false;
        echo($before);
        if ($show_updated && $row->recently_updated) {
            echo get_settings('links_recently_updated_prepend');
        }
        $the_link = '#';
        if (($row->link_url != null) && ($row->link_url != '')) {
            $the_link = htmlspecialchars($row->link_url);
        }
        $rel = $row->link_rel;
        if ($rel != '') {
            $rel = " rel='$rel'";
        }
        $desc = htmlspecialchars($row->link_description, ENT_QUOTES);
        $name = htmlspecialchars($row->link_name, ENT_QUOTES);

        $title = $desc;

        if ($show_updated) {
           if (substr($row->link_updated_f,0,2) != '00') {
                $title .= ' (Last updated ' . date(get_settings('links_updated_date_format'), $row->link_updated_f + (get_settings('gmt_offset') * 3600)) .')';
            }
        }

        if ('' != $title) {
            $title = " title='$title'";
        }

        $alt = " alt='$name'";
            
        $target = $row->link_target;
        if ('' != $target) {
            $target = " target='$target'";
        }
        echo("<a href='$the_link'");
        echo($rel . $title . $target);
        echo('>');
        if (($row->link_image != null) && $show_images) {
			if (strstr($row->link_image, 'http'))
				echo "<img src='$row->link_image' $alt $title />";
			else // If it's a relative path
            	echo "<img src='" . get_settings('siteurl') . "$row->link_image' $alt $title />";
        } else {
            echo($name);
        }
        echo('</a>');
        if ($show_updated && $row->recently_updated) {
            echo get_settings('links_recently_updated_append');
        }

        if ($show_description && ($desc != '')) {
            echo($between.$desc);
        }

        // now do the rating
        if ($show_rating) {
            
            if (get_settings('links_rating_type') == 'number') {
                if (($row->link_rating != 0) || (get_settings('links_rating_ignore_zero') != 1)) {
                    echo($between." $row->link_rating\n");
                }
            } else if (get_settings('links_rating_type') == 'char') {
                echo($between);
                for ($r = $row->link_rating; $r > 0; $r--) {
                    echo(get_settings('links_rating_char'));
                }
            } else if (get_settings('links_rating_type') == 'image') {
                echo($between);
                if (get_settings('links_rating_single_image')) {
                    for ($r = $row->link_rating; $r > 0; $r--) {
                        echo(' <img src="'.get_settings('links_rating_image0').'" alt="' .
                             $row->link_rating.'" />'."\n");
                    }
                } else {
                    if (($row->link_rating != 0) || (get_settings('links_rating_ignore_zero') != 1)) {
                        $b = 'links_rating_image'.$row->link_rating;
                        echo(' <img src="' .
                             get_settings($b).'" alt="' .
                             $row->link_rating.'" />'."\n");
                    }
                }
            } // end if image
        } // end if show_rating
        echo("$after\n");
    } // end while
}


/** function get_linkobjectsbyname()
 ** Gets an array of link objects associated with category 'cat_name'.
 ** Parameters:
 **   cat_name (default 'noname')  - The category name to use. If no
 **     match is found uses all
 **   orderby (default 'id') - the order to output the links. E.g. 'id', 'name',
 **     'url', 'description', or 'rating'. Or maybe owner. If you start the
 **     name with an underscore the order will be reversed.
 **     You can also specify 'rand' as the order which will return links in a
 **     random order.
 **   limit (default -1) - Limit to X entries. If not specified, all entries
 **     are shown.
 **
 ** Use this like:
 ** $links = get_linkobjectsbyname('fred');
 ** foreach ($links as $link) {
 **   echo '<li>'.$link->link_name.'</li>';
 ** }
 **/
function get_linkobjectsbyname($cat_name = "noname" , $orderby = 'name', $limit = -1) {
    global $wpdb;
    $cat_id = -1;
    $results = $wpdb->get_results("SELECT cat_id FROM $wpdb->linkcategories WHERE cat_name='$cat_name'");
    if ($results) {
        foreach ($results as $result) {
            $cat_id = $result->cat_id;
        }
    }
    return get_linkobjects($cat_id, $orderby, $limit);
}

/** function get_linkobjects()
 ** Gets an array of link objects associated with category n.
 ** Parameters:
 **   category (default -1)  - The category to use. If no category supplied
 **      uses all
 **   orderby (default 'id') - the order to output the links. E.g. 'id', 'name',
 **     'url', 'description', or 'rating'. Or maybe owner. If you start the
 **     name with an underscore the order will be reversed.
 **     You can also specify 'rand' as the order which will return links in a
 **     random order.
 **   limit (default -1) - Limit to X entries. If not specified, all entries
 **     are shown.
 **
 ** Use this like:
 ** $links = get_linkobjects(1);
 ** if ($links) {
 **   foreach ($links as $link) {
 **     echo '<li>'.$link->link_name.'<br />'.$link->link_description.'</li>';
 **   }
 ** }
 ** Fields are:
 ** link_id
 ** link_url
 ** link_name
 ** link_image
 ** link_target
 ** link_category
 ** link_description
 ** link_visible
 ** link_owner
 ** link_rating
 ** link_updated
 ** link_rel
 ** link_notes
 **/
function get_linkobjects($category = -1, $orderby = 'name', $limit = -1) {
    global $wpdb;

    $sql = "SELECT * FROM $wpdb->links WHERE link_visible = 'Y'";
    if ($category != -1) {
        $sql .= " AND link_category = $category ";
    }
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

    $results = $wpdb->get_results($sql);
    if ($results) {
        foreach ($results as $result) {
            $result->link_url         = $result->link_url;
            $result->link_name        = $result->link_name;
            $result->link_description = $result->link_description;
            $result->link_notes       = $result->link_notes;
            $newresults[] = $result;
        }
    }
    return $newresults;
}

/** function get_linkrating()
 ** Returns the appropriate html for the link rating based on the configuration.
 ** Parameters:
 **   link  - The link object returned from get_linkobjects
 **/
function get_linkrating($link) {
    if (get_settings('links_rating_type') == 'number') {
        if (($link->link_rating != 0) || (get_settings('links_rating_ignore_zero') != 1)) {
            $s = "$link->link_rating";
        }
    } else if (get_settings('links_rating_type') == 'char') {
        for ($r = $link->link_rating; $r > 0; $r--) {
            $s .= get_settings('links_rating_char');
        }
    } else if (get_settings('links_rating_type') == 'image') {
        if (get_settings('links_rating_single_image')) {
            for ($r = $link->link_rating; $r > 0; $r--) {
                $s .= '<img src="'.get_settings('links_rating_image0').'" alt="' .
                      $link->link_rating.'" />'."\n";
            }
        } else {
            if (($link->link_rating != 0) || (get_settings('links_rating_ignore_zero') != 1)) {
                $b = 'links_rating_image'.$row->link_rating;
                $s = ' <img src="' .
                     get_settings($b).'" alt="' .
                     $link->link_rating.'" />'."\n";
            }
        }
    } // end if image
    return $s;
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
    global $wpdb;
    $cat_name = '';
    if ('' != $id) {
        $cat_name = $wpdb->get_var("SELECT cat_name FROM $wpdb->linkcategories WHERE cat_id=$id");
    }
    return $cat_name;
}

/** function get_get_autotoggle()
 ** Gets the auto_toggle setting of category n.
 ** Parameters: id (default 0)  - The category to get. If no category supplied
 **                uses 0
 */
function get_autotoggle($id = 0) {
    global $wpdb;
    $auto_toggle = $wpdb->get_var("SELECT auto_toggle FROM $wpdb->linkcategories WHERE cat_id=$id");
    if ('' == $auto_toggle)
        $auto_toggle = 'N';
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
   if ($count == true) {
      $counts = $wpdb->get_var("SELECT count(*) FROM $wpdb->links");
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


/*
 * function get_links_list()
 *
 * added by Dougal
 *
 * Output a list of all links, listed by category, using the
 * settings in $wpdb->linkcategories and output it as a nested
 * HTML unordered list.
 *
 * Parameters:
 *   order (default 'name')  - Sort link categories by 'name' or 'id'
 *   hide_if_empty (default true)  - Supress listing empty link categories
 */
function get_links_list($order = 'name', $hide_if_empty = 'obsolete') {
	global $wpdb;

	$order = strtolower($order);

	// Handle link category sorting
	if (substr($order,0,1) == '_') {
		$direction = ' DESC';
		$order = substr($order,1);
	}

	// if 'name' wasn't specified, assume 'id':
	$cat_order = ('name' == $order) ? 'cat_name' : 'cat_id';

	if (!isset($direction)) $direction = '';
	// Fetch the link category data as an array of hashesa
	$cats = $wpdb->get_results("
		SELECT DISTINCT link_category, cat_name, show_images, 
			show_description, show_rating, show_updated, sort_order, 
			sort_desc, list_limit
		FROM `$wpdb->links` 
		LEFT JOIN `$wpdb->linkcategories` ON (link_category = cat_id)
		WHERE link_visible =  'Y'
			AND list_limit <> 0
		ORDER BY $cat_order $direction ", ARRAY_A);

	// Display each category
	if ($cats) {
		foreach ($cats as $cat) {
			// Handle each category.
			// First, fix the sort_order info
			$orderby = $cat['sort_order'];
			$orderby = (bool_from_yn($cat['sort_desc'])?'_':'') . $orderby;

			// Display the category name
			echo '	<li id="'.sanitize_title($cat['cat_name']).'">' . $cat['cat_name'] . "\n\t<ul>\n";
			// Call get_links() with all the appropriate params
			get_links($cat['link_category'],
				'<li>',"</li>","\n",
				bool_from_yn($cat['show_images']),
				$orderby,
				bool_from_yn($cat['show_description']),
				bool_from_yn($cat['show_rating']),
				$cat['list_limit'],
				bool_from_yn($cat['show_updated']));

			// Close the last category
			echo "\n\t</ul>\n</li>\n";
		}
	}
}

?>