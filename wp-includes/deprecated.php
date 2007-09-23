<?php

/*
 * Deprecated global variables.
 */

$tableposts = $wpdb->posts;
$tableusers = $wpdb->users;
$tablecategories = $wpdb->categories;
$tablepost2cat = $wpdb->post2cat;
$tablecomments = $wpdb->comments;
$tablelinks = $wpdb->links;
$tablelinkcategories = 'linkcategories_is_gone';
$tableoptions = $wpdb->options;
$tablepostmeta = $wpdb->postmeta;

/*
 * Deprecated functions come here to die.
 */

// Use get_post().
function get_postdata($postid) {
	$post = &get_post($postid);

	$postdata = array (
		'ID' => $post->ID,
		'Author_ID' => $post->post_author,
		'Date' => $post->post_date,
		'Content' => $post->post_content,
		'Excerpt' => $post->post_excerpt,
		'Title' => $post->post_title,
		'Category' => $post->post_category,
		'post_status' => $post->post_status,
		'comment_status' => $post->comment_status,
		'ping_status' => $post->ping_status,
		'post_password' => $post->post_password,
		'to_ping' => $post->to_ping,
		'pinged' => $post->pinged,
		'post_type' => $post->post_type,
		'post_name' => $post->post_name
	);

	return $postdata;
}

// Use the new post loop.
function start_wp() {
	global $wp_query, $post;

	// Since the old style loop is being used, advance the query iterator here.
	$wp_query->next_post();

	setup_postdata($post);
}

function the_category_ID($echo = true) {
	// Grab the first cat in the list.
	$categories = get_the_category();
	$cat = $categories[0]->term_id;

	if ( $echo )
		echo $cat;

	return $cat;
}

function the_category_head($before='', $after='') {
	global $currentcat, $previouscat;
	// Grab the first cat in the list.
	$categories = get_the_category();
	$currentcat = $categories[0]->category_id;
	if ( $currentcat != $previouscat ) {
		echo $before;
		echo get_the_category_by_ID($currentcat);
		echo $after;
		$previouscat = $currentcat;
	}
}

// Use previous_post_link().
function previous_post($format='%', $previous='previous post: ', $title='yes', $in_same_cat='no', $limitprev=1, $excluded_categories='') {

	if ( empty($in_same_cat) || 'no' == $in_same_cat )
		$in_same_cat = false;
	else
		$in_same_cat = true;

	$post = get_previous_post($in_same_cat, $excluded_categories);

	if ( !$post )
		return;

	$string = '<a href="'.get_permalink($post->ID).'">'.$previous;
	if ( 'yes' == $title )
		$string .= apply_filters('the_title', $post->post_title, $post);
	$string .= '</a>';
	$format = str_replace('%', $string, $format);
	echo $format;
}

// Use next_post_link().
function next_post($format='%', $next='next post: ', $title='yes', $in_same_cat='no', $limitnext=1, $excluded_categories='') {

	if ( empty($in_same_cat) || 'no' == $in_same_cat )
		$in_same_cat = false;
	else
		$in_same_cat = true;

	$post = get_next_post($in_same_cat, $excluded_categories);

	if ( !$post	)
		return;

	$string = '<a href="'.get_permalink($post->ID).'">'.$next;
	if ( 'yes' == $title )
		$string .= apply_filters('the_title', $post->post_title, $nextpost);
	$string .= '</a>';
	$format = str_replace('%', $string, $format);
	echo $format;
}

//
// Use current_user_can() for these.
//

/* returns true if $user_id can create a new post */
function user_can_create_post($user_id, $blog_id = 1, $category_id = 'None') {
	$author_data = get_userdata($user_id);
	return ($author_data->user_level > 1);
}

/* returns true if $user_id can create a new post */
function user_can_create_draft($user_id, $blog_id = 1, $category_id = 'None') {
	$author_data = get_userdata($user_id);
	return ($author_data->user_level >= 1);
}

/* returns true if $user_id can edit $post_id */
function user_can_edit_post($user_id, $post_id, $blog_id = 1) {
	$author_data = get_userdata($user_id);
	$post = get_post($post_id);
	$post_author_data = get_userdata($post->post_author);

	if ( (($user_id == $post_author_data->ID) && !($post->post_status == 'publish' &&  $author_data->user_level < 2))
			 || ($author_data->user_level > $post_author_data->user_level)
			 || ($author_data->user_level >= 10) ) {
		return true;
	} else {
		return false;
	}
}

/* returns true if $user_id can delete $post_id */
function user_can_delete_post($user_id, $post_id, $blog_id = 1) {
	// right now if one can edit, one can delete
	return user_can_edit_post($user_id, $post_id, $blog_id);
}

/* returns true if $user_id can set new posts' dates on $blog_id */
function user_can_set_post_date($user_id, $blog_id = 1, $category_id = 'None') {
	$author_data = get_userdata($user_id);
	return (($author_data->user_level > 4) && user_can_create_post($user_id, $blog_id, $category_id));
}

/* returns true if $user_id can edit $post_id's date */
function user_can_edit_post_date($user_id, $post_id, $blog_id = 1) {
	$author_data = get_userdata($user_id);
	return (($author_data->user_level > 4) && user_can_edit_post($user_id, $post_id, $blog_id));
}

/* returns true if $user_id can edit $post_id's comments */
function user_can_edit_post_comments($user_id, $post_id, $blog_id = 1) {
	// right now if one can edit a post, one can edit comments made on it
	return user_can_edit_post($user_id, $post_id, $blog_id);
}

/* returns true if $user_id can delete $post_id's comments */
function user_can_delete_post_comments($user_id, $post_id, $blog_id = 1) {
	// right now if one can edit comments, one can delete comments
	return user_can_edit_post_comments($user_id, $post_id, $blog_id);
}

function user_can_edit_user($user_id, $other_user) {
	$user  = get_userdata($user_id);
	$other = get_userdata($other_user);
	if ( $user->user_level > $other->user_level || $user->user_level > 8 || $user->ID == $other->ID )
		return true;
	else
		return false;
}

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
		$cat = get_term_by('name', $cat_name, 'link_category');
		if ( $cat )
			$cat_id = $cat->term_id;

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

	$cat = get_term_by('name', $cat_name, 'link_category');
	if ( !$cat )
		return false;
	$cat_id = $cat->term_id;

	$args = add_query_arg('category', $cat_id, $args);
	wp_get_links($args);
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
		$cat = get_term_by('name', $cat_name, 'link_category');
		if ( $cat )
			$cat_id = $cat->term_id;

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
function get_linkobjects($category = 0, $orderby = 'name', $limit = 0) {
		global $wpdb;

		$links = get_bookmarks("category=$category&orderby=$orderby&limit=$limit");

		$links_array = array();
		foreach ($links as $link) {
			$links_array[] = $link;
		}

		return $links_array;
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

/** function get_get_autotoggle()
 ** Gets the auto_toggle setting of category n.
 ** Parameters: id (default 0)  - The category to get. If no category supplied
 **                uses 0
 */
function get_autotoggle($id = 0) {
	return 0;
}

// Use wp_list_cats().
function list_cats($optionall = 1, $all = 'All', $sort_column = 'ID', $sort_order = 'asc', $file = '', $list = true, $optiondates = 0, $optioncount = 0, $hide_empty = 1, $use_desc_for_title = 1, $children=FALSE, $child_of=0, $categories=0, $recurse=0, $feed = '', $feed_image = '', $exclude = '', $hierarchical=FALSE) {
	$query = compact('optionall', 'all', 'sort_column', 'sort_order', 'file', 'list', 'optiondates', 'optioncount', 'hide_empty', 'use_desc_for_title', 'children',
		'child_of', 'categories', 'recurse', 'feed', 'feed_image', 'exclude', 'hierarchical');
	return wp_list_cats($query);
}

function wp_list_cats($args = '') {
	$r = wp_parse_args( $args );

	// Map to new names.
	if ( isset($r['optionall']) && isset($r['all']))
		$r['show_option_all'] = $r['all'];
	if ( isset($r['sort_column']) )
		$r['orderby'] = $r['sort_column'];
	if ( isset($r['sort_order']) )
		$r['order'] = $r['sort_order'];
	if ( isset($r['optiondates']) )
		$r['show_last_update'] = $r['optiondates'];
	if ( isset($r['optioncount']) )
		$r['show_count'] = $r['optioncount'];
	if ( isset($r['list']) )
		$r['style'] = $r['list'] ? 'list' : 'break';
	$r['title_li'] = '';

	return wp_list_categories($r);
}

function dropdown_cats($optionall = 1, $all = 'All', $orderby = 'ID', $order = 'asc',
		$show_last_update = 0, $show_count = 0, $hide_empty = 1, $optionnone = FALSE,
		$selected = 0, $exclude = 0) {

	$show_option_all = '';
	if ( $optionall )
		$show_option_all = $all;

	$show_option_none = '';
	if ( $optionnone )
		$show_option_none = __('None');

	$vars = compact('show_option_all', 'show_option_none', 'orderby', 'order',
					'show_last_update', 'show_count', 'hide_empty', 'selected', 'exclude');
	$query = add_query_arg($vars, '');
	return wp_dropdown_categories($query);
}

// Use wp_print_scripts() or WP_Scripts.
function tinymce_include() {
	wp_print_script('wp_tiny_mce');
}

function list_authors($optioncount = false, $exclude_admin = true, $show_fullname = false, $hide_empty = true, $feed = '', $feed_image = '') {
	$args = compact('optioncount', 'exclude_admin', 'show_fullname', 'hide_empty', 'feed', 'feed_image');
	return wp_list_authors($args);
}

function wp_get_post_cats($blogid = '1', $post_ID = 0) {
	return wp_get_post_categories($post_ID);
}

function wp_set_post_cats($blogid = '1', $post_ID = 0, $post_categories = array()) {
	return wp_set_post_categories($post_ID, $post_categories);
}

// Use wp_get_archives().
function get_archives($type='', $limit='', $format='html', $before = '', $after = '', $show_post_count = false) {
	$args = compact('type', 'limit', 'format', 'before', 'after', 'show_post_count');
	return wp_get_archives($args);
}

// Use get_author_posts_url().
function get_author_link($echo = false, $author_id, $author_nicename = '') {
	$link = get_author_posts_url($author_id, $author_nicename);

	if ( $echo )
		echo $link;
	return $link;
}

// Use wp_link_pages().
function link_pages($before='<br />', $after='<br />', $next_or_number='number', $nextpagelink='next page', $previouspagelink='previous page', $pagelink='%', $more_file='') {
	$args = compact('before', 'after', 'next_or_number', 'nextpagelink', 'previouspagelink', 'pagelink', 'more_file');
	return wp_link_pages($args);
}

// Use get_option().
function get_settings($option) {
	return get_option($option);
}

// Use the_permalink().
function permalink_link() {
	the_permalink();
}

// Use the_permalink_rss()
function permalink_single_rss($file = '') {
	the_permalink_rss();
}

/** function wp_get_links()
 ** Gets the links associated with category n.
 ** Parameters:
 **   category (no default)  - The category to use.
 ** or:
 **   a query string
 **/
function wp_get_links($args = '') {
	global $wpdb;

	if ( strpos( $args, '=' ) === false ) {
		$cat_id = $args;
		$args = add_query_arg( 'category', $cat_id, $args );
	}

	$defaults = array(
		'category' => -1, 'before' => '',
		'after' => '<br />', 'between' => ' ',
		'show_images' => true, 'orderby' => 'name',
		'show_description' => true, 'show_rating' => false,
		'limit' => -1, 'show_updated' => true,
		'echo' => true
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

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

		$desc = attribute_escape(sanitize_bookmark_field('link_description', $row->link_description, $row->link_id, 'display'));
		$name = attribute_escape(sanitize_bookmark_field('link_name', $row->link_name, $row->link_id, 'display'));
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

		if ($show_rating) {
			$output .= $between . get_linkrating($row);
		}

		$output .= "$after\n";
	} // end while

	if ( !$echo )
		return $output;
	echo $output;
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
			echo '  <li id="linkcat-' . $cat->term_id . '" class="linkcat"><h2>' . apply_filters('link_category', $cat->name ) . "</h2>\n\t<ul>\n";
			// Call get_links() with all the appropriate params
			get_links($cat->term_id, '<li>', "</li>", "\n", true, 'name', false);

			// Close the last category
			echo "\n\t</ul>\n</li>\n";
		}
	}
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


function get_linkrating($link) {
	return sanitize_bookmark_field('link_rating', $link->link_rating, $link->link_id, 'display');
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
	return $cat->name;
}

?>