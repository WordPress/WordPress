<?php
/**
 * Bookmark Template Functions for usage in Themes
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * The formatted output of a list of bookmarks.
 *
 * The $bookmarks array must contain bookmark objects and will be iterated over
 * to retrieve the bookmark to be used in the output.
 *
 * The output is formatted as HTML with no way to change that format. However,
 * what is between, before, and after can be changed. The link itself will be
 * HTML.
 *
 * This function is used internally by wp_list_bookmarks() and should not be
 * used by themes.
 *
 * The defaults for overwriting are:
 * 'show_updated' - Default is 0 (integer). Will show the time of when the
 *		bookmark was last updated.
 * 'show_description' - Default is 0 (integer). Whether to show the description
 *		of the bookmark.
 * 'show_images' - Default is 1 (integer). Whether to show link image if
 *		available.
 * 'show_name' - Default is 0 (integer). Whether to show link name if
 *		available.
 * 'before' - Default is '<li>' (string). The html or text to prepend to each
 *		bookmarks.
 * 'after' - Default is '</li>' (string). The html or text to append to each
 *		bookmarks.
 * 'link_before' - Default is '' (string). The html or text to prepend to each
 *		bookmarks inside the <a> tag.
 * 'link_after' - Default is '' (string). The html or text to append to each
 *		bookmarks inside the <a> tag.
 * 'between' - Default is '\n' (string). The string for use in between the link,
 *		description, and image.
 * 'show_rating' - Default is 0 (integer). Whether to show the link rating.
 *
 * @since 2.1.0
 * @access private
 * @usedby wp_list_bookmarks()
 *
 * @param array $bookmarks List of bookmarks to traverse
 * @param string|array $args Optional. Overwrite the defaults.
 * @return string Formatted output in HTML
 */
function _walk_bookmarks($bookmarks, $args = '' ) {
	$defaults = array(
		'show_updated' => 0, 'show_description' => 0,
		'show_images' => 1, 'show_name' => 0,
		'before' => '<li>', 'after' => '</li>', 'between' => "\n",
		'show_rating' => 0, 'link_before' => '', 'link_after' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$output = ''; // Blank string to start with.

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

		$desc = attribute_escape(sanitize_bookmark_field('link_description', $bookmark->link_description, $bookmark->link_id, 'display'));
		$name = attribute_escape(sanitize_bookmark_field('link_name', $bookmark->link_name, $bookmark->link_id, 'display'));
 		$title = $desc;

		if ( $show_updated )
			if ( '00' != substr($bookmark->link_updated_f, 0, 2) ) {
				$title .= ' (';
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

		$output .= $link_before;

		if ( $bookmark->link_image != null && $show_images ) {
			if ( strpos($bookmark->link_image, 'http') !== false )
				$output .= "<img src=\"$bookmark->link_image\" $alt $title />";
			else // If it's a relative path
				$output .= "<img src=\"" . get_option('siteurl') . "$bookmark->link_image\" $alt $title />";

			if ($show_name) $output .= $name;
		} else {
			$output .= $name;
		}

		$output .= $link_after;

		$output .= '</a>';

		if ( $show_updated && $bookmark->recently_updated )
			$output .= get_option('links_recently_updated_append');

		if ( $show_description && '' != $desc )
			$output .= $between . $desc;

		if ($show_rating) {
			$output .= $between . sanitize_bookmark_field('link_rating', $bookmark->link_rating, $bookmark->link_id, 'display');
		}

		$output .= "$after\n";
	} // end while

	return $output;
}

/**
 * Retrieve or echo all of the bookmarks.
 *
 * List of default arguments are as follows:
 * 'orderby' - Default is 'name' (string). How to order the links by. String is
 *		based off of the bookmark scheme.
 * 'order' - Default is 'ASC' (string). Either 'ASC' or 'DESC'. Orders in either
 *		ascending or descending order.
 * 'limit' - Default is -1 (integer) or show all. The amount of bookmarks to
 *		display.
 * 'category' - Default is empty string (string). Include the links in what
 *		category ID(s).
 * 'category_name' - Default is empty string (string). Get links by category
 *		name.
 * 'hide_invisible' - Default is 1 (integer). Whether to show (default) or hide
 *		links marked as 'invisible'.
 * 'show_updated' - Default is 0 (integer). Will show the time of when the
 *		bookmark was last updated.
 * 'echo' - Default is 1 (integer). Whether to echo (default) or return the
 *		formatted bookmarks.
 * 'categorize' - Default is 1 (integer). Whether to show links listed by
 *		category (default) or show links in one column.
 *
 * These options define how the Category name will appear before the category
 * links are displayed, if 'categorize' is 1. If 'categorize' is 0, then it will
 * display for only the 'title_li' string and only if 'title_li' is not empty.
 * 'title_li' - Default is 'Bookmarks' (translatable string). What to show
 *		before the links appear.
 * 'title_before' - Default is '<h2>' (string). The HTML or text to show before
 *		the 'title_li' string.
 * 'title_after' - Default is '</h2>' (string). The HTML or text to show after
 *		the 'title_li' string.
 * 'class' - Default is 'linkcat' (string). The CSS class to use for the
 *		'title_li'.
 *
 * 'category_before' - Default is '<li id="%id" class="%class">'. String must
 *		contain '%id' and '%class' to get
 * the id of the category and the 'class' argument. These are used for
 *		formatting in themes.
 * Argument will be displayed before the 'title_before' argument.
 * 'category_after' - Default is '</li>' (string). The HTML or text that will
 *		appear after the list of links.
 *
 * These are only used if 'categorize' is set to 1 or true.
 * 'category_orderby' - Default is 'name'. How to order the bookmark category
 *		based on term scheme.
 * 'category_order' - Default is 'ASC'. Set the order by either ASC (ascending)
 *		or DESC (descending).
 *
 * @see _walk_bookmarks() For other arguments that can be set in this function
 *		and passed to _walk_bookmarks().
 * @see get_bookmarks() For other arguments that can be set in this function and
 *		passed to get_bookmarks().
 * @link http://codex.wordpress.org/Template_Tags/wp_list_bookmarks
 *
 * @since 2.1.0
 * @uses _list_bookmarks() Used to iterate over all of the bookmarks and return
 *		the html
 * @uses get_terms() Gets all of the categories that are for links.
 *
 * @param string|array $args Optional. Overwrite the defaults of the function
 * @return string|null Will only return if echo option is set to not echo.
 *		Default is not return anything.
 */
function wp_list_bookmarks($args = '') {
	$defaults = array(
		'orderby' => 'name', 'order' => 'ASC',
		'limit' => -1, 'category' => '', 'exclude_category' => '',
		'category_name' => '', 'hide_invisible' => 1,
		'show_updated' => 0, 'echo' => 1,
		'categorize' => 1, 'title_li' => __('Bookmarks'),
		'title_before' => '<h2>', 'title_after' => '</h2>',
		'category_orderby' => 'name', 'category_order' => 'ASC',
		'class' => 'linkcat', 'category_before' => '<li id="%id" class="%class">',
		'category_after' => '</li>'
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$output = '';

	if ( $categorize ) {
		//Split the bookmarks into ul's for each category
		$cats = get_terms('link_category', array('name__like' => $category_name, 'include' => $category, 'exclude' => $exclude_category, 'orderby' => $category_orderby, 'order' => $category_order, 'hierarchical' => 0));

		foreach ( (array) $cats as $cat ) {
			$params = array_merge($r, array('category'=>$cat->term_id));
			$bookmarks = get_bookmarks($params);
			if ( empty($bookmarks) )
				continue;
			$output .= str_replace(array('%id', '%class'), array("linkcat-$cat->term_id", $class), $category_before);
			$catname = apply_filters( "link_category", $cat->name );
			$output .= "$title_before$catname$title_after\n\t<ul class='xoxo blogroll'>\n";
			$output .= _walk_bookmarks($bookmarks, $r);
			$output .= "\n\t</ul>\n$category_after\n";
		}
	} else {
		//output one single list using title_li for the title
		$bookmarks = get_bookmarks($r);

		if ( !empty($bookmarks) ) {
			if ( !empty( $title_li ) ){
				$output .= str_replace(array('%id', '%class'), array("linkcat-$category", $class), $category_before);
				$output .= "$title_before$title_li$title_after\n\t<ul class='xoxo blogroll'>\n";
				$output .= _walk_bookmarks($bookmarks, $r);
				$output .= "\n\t</ul>\n$category_after\n";
			} else {
				$output .= _walk_bookmarks($bookmarks, $r);
			}
		}
	}

	$output = apply_filters( 'wp_list_bookmarks', $output );

	if ( !$echo )
		return $output;
	echo $output;
}

?>
