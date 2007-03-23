<?php

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

	if ( !isset($category) )         $category = -1;
	if ( !isset($before) )           $before = '';
	if ( !isset($after) )            $after = '<br />';
	if ( !isset($between) )          $between = ' ';
	if ( !isset($show_images) )      $show_images = true;
	if ( !isset($orderby) )          $orderby = 'name';
	if ( !isset($show_description) ) $show_description = true;
	if ( !isset($show_rating) )      $show_rating = false;
	if ( !isset($limit) )            $limit = -1;
	if ( !isset($show_updated) )     $show_updated = 1;
	if ( !isset($echo) )             $echo = true;

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

	$order = 'ASC';
	if ( substr($orderby, 0, 1) == '_' ) {
		$order = 'DESC';
		$orderby = substr($orderby, 1);
	}

	if ( $category == -1 ) //get_bookmarks uses '' to signify all categories
		$category = '';

	$results = get_bookmarks("category=$category&orderby=$orderby&order=$order&show_updated=$show_updated&limit=$limit");

	if ( !$results )
		return;

	$output = '';

	foreach ( (array) $results as $row ) {
		if ( !isset($row->recently_updated) )
			$row->recently_updated = false;
		$output .= $before;
		if ( $show_updated && $row->recently_updated )
			$output .= get_option('links_recently_updated_prepend');
		$the_link = '#';
		if ( !empty($row->link_url) )
			$the_link = clean_url($row->link_url);
		$rel = $row->link_rel;
		if ( '' != $rel )
			$rel = ' rel="' . $rel . '"';

		$desc = attribute_escape($row->link_description);
		$name = attribute_escape($row->link_name);
		$title = $desc;

		if ( $show_updated )
			if (substr($row->link_updated_f, 0, 2) != '00')
				$title .= ' ('.__('Last updated') . ' ' . date(get_option('links_updated_date_format'), $row->link_updated_f + (get_option('gmt_offset') * 3600)) . ')';

		if ( '' != $title )
			$title = ' title="' . $title . '"';

		$alt = ' alt="' . $name . '"';

		$target = $row->link_target;
		if ( '' != $target )
			$target = ' target="' . $target . '"';

		$output .= '<a href="' . $the_link . '"' . $rel . $title . $target. '>';

		if ( $row->link_image != null && $show_images ) {
			if ( strpos($row->link_image, 'http') !== false )
				$output .= "<img src=\"$row->link_image\" $alt $title />";
			else // If it's a relative path
				$output .= "<img src=\"" . get_option('siteurl') . "$row->link_image\" $alt $title />";
		} else {
			$output .= $name;
		}

		$output .= '</a>';

		if ( $show_updated && $row->recently_updated )
			$output .= get_option('links_recently_updated_append');

		if ( $show_description && '' != $desc )
			$output .= $between . $desc;

		$output .= "$after\n";
	} // end while

	if ( !$echo )
		return $output;
	echo $output;
}

function get_linkrating($link) {
	return apply_filters('link_rating', $link->link_rating);
}

/** function get_linkcatname()
 ** Gets the name of category n.
 ** Parameters: id (default 0)  - The category to get. If no category supplied
 **                uses 0
 */
function get_linkcatname($id = 0) {
	$id = (int) $id;

	if ( empty($id) )
		return '';

	$cats = wp_get_link_cats($id);

	if ( empty($cats) || ! is_array($cats) )
		return '';

	$cat_id = (int) $cats[0]; // Take the first cat.

	$cat = get_category($cat_id);
	return $cat->cat_name;
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
function links_popup_script($text = 'Links', $width=400, $height=400, $file='links.all.php', $count = true) {
	if ( $count )
		$counts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->links");

	$javascript = "<a href=\"#\" onclick=\"javascript:window.open('$file?popup=1', '_blank', 'width=$width,height=$height,scrollbars=yes,status=no'); return false\">";
	$javascript .= $text;

	if ( $count )
		$javascript .= " ($counts)";

	$javascript .= "</a>\n\n";
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
	if ( '_' == substr($order,0,1) ) {
		$direction = 'DESC';
		$order = substr($order,1);
	}

	if ( !isset($direction) )
		$direction = '';

	$cats = get_categories("type=link&orderby=$order&order=$direction&hierarchical=0");

	// Display each category
	if ( $cats ) {
		foreach ( (array) $cats as $cat ) {
			// Handle each category.

			// Display the category name
			echo '	<li id="linkcat-' . $cat->cat_ID . '" class="linkcat"><h2>' . $cat->cat_name . "</h2>\n\t<ul>\n";
			// Call get_links() with all the appropriate params
			get_links($cat->cat_ID, '<li>', "</li>", "\n", true, 'name', false);

			// Close the last category
			echo "\n\t</ul>\n</li>\n";
		}
	}
}

function _walk_bookmarks($bookmarks, $args = '' ) {
	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('show_updated' => 0, 'show_description' => 0, 'show_images' => 1, 'before' => '<li>',
		'after' => '</li>', 'between' => "\n");
	$r = array_merge($defaults, $r);
	extract($r);

	foreach ( (array) $bookmarks as $bookmark ) {
		if ( !isset($bookmark->recently_updated) )
			$bookmark->recently_updated = false;
		$output .= $before;
		if ( $show_updated && $bookmark->recently_updated )
			$output .= get_option('links_recently_updated_prepend');

		$the_link = '#';
		if ( !empty($bookmark->link_url) )
			$the_link = clean_url($bookmark->link_url);

		$rel = $bookmark->link_rel;
		if ( '' != $rel )
			$rel = ' rel="' . $rel . '"';

		$desc = attribute_escape($bookmark->link_description);
		$name = attribute_escape($bookmark->link_name);
		$title = $desc;

		if ( $show_updated )
			if ( '00' != substr($bookmark->link_updated_f, 0, 2) ) {
				$title .= ' ';
				$title .= sprintf(__('Last updated: %s'), date(get_option('links_updated_date_format'), $bookmark->link_updated_f + (get_option('gmt_offset') * 3600)));
				$title .= ')';
			}

		if ( '' != $title )
			$title = ' title="' . $title . '"';

		$alt = ' alt="' . $name . '"';

		$target = $bookmark->link_target;
		if ( '' != $target )
			$target = ' target="' . $target . '"';

		$output .= '<a href="' . $the_link . '"' . $rel . $title . $target. '>';

		if ( $bookmark->link_image != null && $show_images ) {
			if ( strpos($bookmark->link_image, 'http') !== false )
				$output .= "<img src=\"$bookmark->link_image\" $alt $title />";
			else // If it's a relative path
				$output .= "<img src=\"" . get_option('siteurl') . "$bookmark->link_image\" $alt $title />";
		} else {
			$output .= $name;
		}

		$output .= '</a>';

		if ( $show_updated && $bookmark->recently_updated )
			$output .= get_option('links_recently_updated_append');

		if ( $show_description && '' != $desc )
			$output .= $between . $desc;
		$output .= "$after\n";
	} // end while

	return $output;
}

function wp_list_bookmarks($args = '') {
	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('orderby' => 'name', 'order' => 'ASC', 'limit' => -1, 'category' => '',
		'category_name' => '', 'hide_invisible' => 1, 'show_updated' => 0, 'echo' => 1,
		'categorize' => 1, 'title_li' => __('Bookmarks'), 'title_before' => '<h2>', 'title_after' => '</h2>',
		'category_orderby' => 'name', 'category_order' => 'ASC', 'class' => 'linkcat',
		'category_before' => '<li id="%id" class="%class">', 'category_after' => '</li>');
	$r = array_merge($defaults, $r);
	extract($r);

	$output = '';

	if ( $categorize ) {
		//Split the bookmarks into ul's for each category
		$cats = get_categories("type=link&category_name=$category_name&include=$category&orderby=$category_orderby&order=$category_order&hierarchical=0");

		foreach ( (array) $cats as $cat ) {
			$bookmarks = get_bookmarks("limit=$limit&category={$cat->cat_ID}&show_updated=$show_updated&orderby=$orderby&order=$order&hide_invisible=$hide_invisible&show_updated=$show_updated");
			if ( empty($bookmarks) )
				continue;
			$output .= str_replace(array('%id', '%class'), array("linkcat-$cat->cat_ID", $class), $category_before);
			$output .= "$title_before$cat->cat_name$title_after\n\t<ul>\n";
			$output .= _walk_bookmarks($bookmarks, $r);
			$output .= "\n\t</ul>\n$category_after\n";
		}
	} else {
		//output one single list using title_li for the title
		$bookmarks = get_bookmarks("limit=$limit&category=$category&show_updated=$show_updated&orderby=$orderby&order=$order&hide_invisible=$hide_invisible&show_updated=$show_updated");
		
		if ( !empty($bookmarks) ) {
			if ( !empty( $title_li ) ){
				$output .= str_replace(array('%id', '%class'), array("linkcat-$category", $class), $category_before);
				$output .= "$title_before$title_li$title_after\n\t<ul>\n";
				$output .= _walk_bookmarks($bookmarks, $r);
				$output .= "\n\t</ul>\n$category_after\n";
			} else {
				$output .= _walk_bookmarks($bookmarks, $r);
			}
		}
	}

	if ( !$echo )
		return $output;
	echo $output;
}

?>
