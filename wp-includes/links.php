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
    $results = $wpdb->get_results("SELECT cat_ID FROM $wpdb->categories WHERE cat_name='$cat_name'");
    if ($results) {
        foreach ($results as $result) {
            $cat_id = $result->cat_ID;
        }
    }
    get_links($cat_id, $before, $after, $between, $show_images, $orderby,
              $show_description, $show_rating, $limit, $show_updated);
}

/** function wp_get_linksbyname()
 ** Gets the links associated with the named category.
 ** Parameters:
 **   category (no default)  - The category to use.
 **/
function wp_get_linksbyname($category, $args = '') {
	global $wpdb;

	$cat_id = $wpdb->get_var("SELECT cat_ID FROM $wpdb->categories WHERE cat_name='$category' LIMIT 1");

	if (! $cat_id)
		return;

	$args = add_query_arg('category', $cat_id, $args);
	wp_get_links($args);
} // end wp_get_linksbyname

/** function wp_get_links()
 ** Gets the links associated with category n.
 ** Parameters:
 **   category (no default)  - The category to use.
 ** or:
 **   a query string
 **/
function wp_get_links($args = '') {
	global $wpdb;

	if ( empty($args) )
		return;

	if ( false === strpos($args, '=') ) {
		$cat_id = $args;
		$args = add_query_arg('category', $cat_id, $args);
	}

	parse_str($args);

	if (! isset($category))	$category = -1;
	if (! isset($before)) $before = '';
	if (! isset($after)) $after = '<br />';
	if (! isset($between))	$between = ' ';
	if (! isset($show_images)) $show_images = true;
	if (! isset($orderby)) $orderby = 'name';
	if (! isset($show_description)) $show_description = true;
	if (! isset($show_rating)) $show_rating = false;
	if (! isset($limit)) $limit = -1;
	if (! isset($show_updated)) $show_updated = 1;
	if (! isset($echo)) $echo = true;

	return get_links($category, $before, $after, $between, $show_images, $orderby, $show_description, $show_rating, $limit, $show_updated, $echo);
} // end wp_get_links

/** function get_links()
 ** Gets the links associated with category n.
 ** Parameters:
 **   category (default -1)  - The category to use. If no category supplied
 **      uses all
 **   before (default '')  - the html to output before the link
 **   after (default '<br />')  - the html to output after the link
 **   between (default ' ')  - the html to output between the link/image
 **     and its description. Not used if no image or show_images == true
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
 **   echo (default true) - whether to echo the results, or return them instead
 */
function get_links($category = -1,
			$before = '',
			$after = '<br />',
			$between = ' ',
			$show_images = true,
			$orderby = 'name',
			$show_description = true,
			$show_rating = false,
			$limit = -1,
			$show_updated = 1,
			$echo = true) {

	global $wpdb;

	$results = get_linkz("category=$category&orderby=$orderby&show_updated=$show_updated&limit=$limit");

	if (!$results) {
		return;
	}


	$output = '';

	foreach ($results as $row) {
		if (!isset($row->recently_updated)) $row->recently_updated = false;
			$output .= $before;
		if ($show_updated && $row->recently_updated) {
			$output .= get_settings('links_recently_updated_prepend');
		}

		$the_link = '#';
		if (!empty($row->link_url))
			$the_link = wp_specialchars($row->link_url);

		$rel = $row->link_rel;
		if ($rel != '') {
			$rel = ' rel="' . $rel . '"';
		}

		$desc = wp_specialchars($row->link_description, ENT_QUOTES);
		$name = wp_specialchars($row->link_name, ENT_QUOTES);
		$title = $desc;

		if ($show_updated) {
			if (substr($row->link_updated_f, 0, 2) != '00') {
				$title .= ' (Last updated ' . date(get_settings('links_updated_date_format'), $row->link_updated_f + (get_settings('gmt_offset') * 3600)) . ')';
			}
		}

		if ('' != $title) {
			$title = ' title="' . $title . '"';
		}

		$alt = ' alt="' . $name . '"';

		$target = $row->link_target;
		if ('' != $target) {
			$target = ' target="' . $target . '"';
		}

		$output .= '<a href="' . $the_link . '"' . $rel . $title . $target. '>';

		if (($row->link_image != null) && $show_images) {
			if (strstr($row->link_image, 'http'))
				$output .= "<img src=\"$row->link_image\" $alt $title />";
			else // If it's a relative path
				$output .= "<img src=\"" . get_settings('siteurl') . "$row->link_image\" $alt $title />";
		} else {
			$output .= $name;
		}

		$output .= '</a>';

		if ($show_updated && $row->recently_updated) {
			$output .= get_settings('links_recently_updated_append');
		}

		if ($show_description && ($desc != '')) {
			$output .= $between . $desc;
		}
		$output .= "$after\n";
	} // end while

	if ($echo) {
		echo $output;
	} else {
		return $output;
	}
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
// Deprecate in favor of get_linkz().
function get_linkobjectsbyname($cat_name = "noname" , $orderby = 'name', $limit = -1) {
    global $wpdb;
    $cat_id = -1;
    //$results = $wpdb->get_results("SELECT cat_id FROM $wpdb->linkcategories WHERE cat_name='$cat_name'");
    // TODO: Fix me.
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
// Deprecate in favor of get_linkz().
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

function get_linkrating($link) {
    return apply_filters('link_rating', $link->link_rating);
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
    if ( empty($id) )
    	return '';
  
	$cats = wp_get_link_cats($id);

	if ( empty($cats) || ! is_array($cats) )
		return '';

	$cat_id = $cats[0]; // Take the first cat.

	$cat = get_category($cat_id);
	return $cat->cat_name;
}

/** function get_get_autotoggle()
 ** Gets the auto_toggle setting of category n.
 ** Parameters: id (default 0)  - The category to get. If no category supplied
 **                uses 0
 */
function get_autotoggle($id = 0) {
	return 0;  
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
	$order = strtolower($order);

	// Handle link category sorting
	$direction = 'ASC';
	if (substr($order,0,1) == '_') {
		$direction = 'DESC';
		$order = substr($order,1);
	}

	if (!isset($direction)) $direction = '';

	$cats = get_categories("type=link&orderby=$order&order=$direction");

	// Display each category
	if ($cats) {
		foreach ($cats as $cat) {
			// Handle each category.

			// Display the category name
			echo '	<li id="linkcat-' . $cat->cat_ID . '"><h2>' . $cat->cat_name . "</h2>\n\t<ul>\n";
			// Call get_links() with all the appropriate params
			get_links($cat->cat_ID,
				'<li>',"</li>","\n");

			// Close the last category
			echo "\n\t</ul>\n</li>\n";
		}
	}
}

function get_linkz($args = '') {
	global $wpdb;

	parse_str($args, $r);

	if ( !isset($r['orderby']) )
		$r['orderby'] = 'name';
	if ( !isset($r['order']) )
		$r['order'] = 'ASC';
	if ( !isset($r['limit']) )
		$r['limit'] = -1;
	if ( !isset($r['category']) )
		$r['category'] = -1;
	if ( !isset($r['category_name']) )
		$r['category_name'] = '';
	if ( !isset($r['hide_invisible']) )
		$r['hide_invisible'] = 1;
	if ( !isset($r['show_updated']) )
		$r['show_updated'] = 0;

	$exclusions = '';
	if ( !empty($r['exclude']) ) {
		$exlinks = preg_split('/[\s,]+/',$r['exclude']);
		if ( count($exlinks) ) {
			foreach ( $exlinks as $exlink ) {
				$exclusions .= ' AND link_id <> ' . intval($exlink) . ' ';
			}
		}
	}

	extract($r);

	if ( ! empty($category_name) ) {
		if ( $cat_id = $wpdb->get_var("SELECT cat_ID FROM $wpdb->categories WHERE cat_name='$category_name' LIMIT 1") )
			$category = $cat_id;
	}

	$category_query = '';
	$join = '';
	if ( $category != -1 && !empty($category) ) {
		$join = " LEFT JOIN $wpdb->link2cat ON ($wpdb->links.link_id = $wpdb->link2cat.link_id) ";

      	$category_query = " AND category_id = $category ";
	}

	if (get_settings('links_recently_updated_time')) {
		$recently_updated_test = ", IF (DATE_ADD(link_updated, INTERVAL " . get_settings('links_recently_updated_time') . " MINUTE) >= NOW(), 1,0) as recently_updated ";
	} else {
		$recently_updated_test = '';
	}

	if ($show_updated) {
		$get_updated = ", UNIX_TIMESTAMP(link_updated) AS link_updated_f ";
	}

	$orderby = strtolower($r['orderby']);
	$length = '';
	switch ($orderby) {
		case 'length':
			$length = ", CHAR_LENGTH(link_name) AS length";
			break;
		case 'rand':
			$orderby = 'rand()';
			break;
		default:
			$orderby = "link_" . $orderby;
	}

	if ( 'link_id' == $orderby )
		$orderby = "$wpdb->links.link_id";

	$visible = '';
	if ( $hide_invisible )
		$visible = "AND link_visible = 'Y'";

	$query = "SELECT * $length $recently_updated_test $get_updated FROM $wpdb->links $join WHERE 1=1 $visible $category_query";
	$query .= " ORDER BY $orderby $order";
	if ($limit != -1)
		$query .= " LIMIT $limit";

	return $wpdb->get_results($query);
}
?>