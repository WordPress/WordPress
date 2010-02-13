<?php
/**
 * Deprecated functions from past WordPress versions. You shouldn't use these
 * globals and functions and look for the alternatives instead. The functions
 * and globals will be removed in a later version.
 *
 * @package WordPress
 * @subpackage Deprecated
 */

/*
 * Deprecated global variables.
 */

/**
 * The name of the Posts table
 * @global string $tableposts
 * @deprecated Use $wpdb->posts
 */
$tableposts = $wpdb->posts;

/**
 * The name of the Users table
 * @global string $tableusers
 * @deprecated Use $wpdb->users
 */
$tableusers = $wpdb->users;

/**
 * The name of the Categories table
 * @global string $tablecategories
 * @deprecated Use $wpdb->categories
 */
$tablecategories = $wpdb->categories;

/**
 * The name of the post to category table
 * @global string $tablepost2cat
 * @deprecated Use $wpdb->post2cat;
 */
$tablepost2cat = $wpdb->post2cat;

/**
 * The name of the comments table
 * @global string $tablecomments
 * @deprecated Use $wpdb->comments;
 */
$tablecomments = $wpdb->comments;

/**
 * The name of the links table
 * @global string $tablelinks
 * @deprecated Use $wpdb->links;
 */
$tablelinks = $wpdb->links;

/**
 * @global string $tablelinkcategories
 * @deprecated Not used anymore;
 */
$tablelinkcategories = 'linkcategories_is_gone';

/**
 * The name of the options table
 * @global string $tableoptions
 * @deprecated Use $wpdb->options;
 */
$tableoptions = $wpdb->options;

/**
 * The name of the postmeta table
 * @global string $tablepostmeta
 * @deprecated Use $wpdb->postmeta;
 */
$tablepostmeta = $wpdb->postmeta;

/*
 * Deprecated functions come here to die.
 */

/**
 * Entire Post data.
 *
 * @since 0.71
 * @deprecated 1.5.1
 * @deprecated Use get_post()
 * @see get_post()
 *
 * @param int $postid
 * @return array
 */
function get_postdata($postid) {
	_deprecated_function( __FUNCTION__, '1.5.1', 'get_post()' );

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

/**
 * Sets up the WordPress Loop.
 *
 * @since 1.0.1
 * @deprecated 1.5
 * @deprecated Use The Loop - {@link http://codex.wordpress.org/The_Loop Use new WordPress Loop}
 */
function start_wp() {
	global $wp_query, $post;

	_deprecated_function( __FUNCTION__, '1.5', __('new WordPress Loop') );

	// Since the old style loop is being used, advance the query iterator here.
	$wp_query->next_post();

	setup_postdata($post);
}

/**
 * Return or Print Category ID.
 *
 * @since 0.71
 * @deprecated 0.71
 * @deprecated use get_the_category()
 * @see get_the_category()
 *
 * @param bool $echo
 * @return null|int
 */
function the_category_ID($echo = true) {
	_deprecated_function( __FUNCTION__, '0.71', 'get_the_category()' );

	// Grab the first cat in the list.
	$categories = get_the_category();
	$cat = $categories[0]->term_id;

	if ( $echo )
		echo $cat;

	return $cat;
}

/**
 * Print category with optional text before and after.
 *
 * @since 0.71
 * @deprecated 0.71
 * @deprecated use get_the_category_by_ID()
 * @see get_the_category_by_ID()
 *
 * @param string $before
 * @param string $after
 */
function the_category_head($before='', $after='') {
	global $currentcat, $previouscat;

	_deprecated_function( __FUNCTION__, '0.71', 'get_the_category_by_ID()' );

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

/**
 * Prints link to the previous post.
 *
 * @since 1.5
 * @deprecated 2.0
 * @deprecated Use previous_post_link()
 * @see previous_post_link()
 *
 * @param string $format
 * @param string $previous
 * @param string $title
 * @param string $in_same_cat
 * @param int $limitprev
 * @param string $excluded_categories
 */
function previous_post($format='%', $previous='previous post: ', $title='yes', $in_same_cat='no', $limitprev=1, $excluded_categories='') {

	_deprecated_function( __FUNCTION__, '2.0', 'previous_post_link()' );

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

/**
 * Prints link to the next post.
 *
 * @since 0.71
 * @deprecated 2.0
 * @deprecated Use next_post_link()
 * @see next_post_link()
 *
 * @param string $format
 * @param string $previous
 * @param string $title
 * @param string $in_same_cat
 * @param int $limitprev
 * @param string $excluded_categories
 */
function next_post($format='%', $next='next post: ', $title='yes', $in_same_cat='no', $limitnext=1, $excluded_categories='') {
	_deprecated_function( __FUNCTION__, '2.0', 'next_post_link()' );

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

/**
 * Whether user can create a post.
 *
 * @since 1.5
 * @deprecated 2.0
 * @deprecated Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $blog_id Not Used
 * @param int $category_id Not Used
 * @return bool
 */
function user_can_create_post($user_id, $blog_id = 1, $category_id = 'None') {
	_deprecated_function( __FUNCTION__, '2.0', 'current_user_can()' );

	$author_data = get_userdata($user_id);
	return ($author_data->user_level > 1);
}

/**
 * Whether user can create a post.
 *
 * @since 1.5
 * @deprecated 2.0
 * @deprecated Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $blog_id Not Used
 * @param int $category_id Not Used
 * @return bool
 */
function user_can_create_draft($user_id, $blog_id = 1, $category_id = 'None') {
	_deprecated_function( __FUNCTION__, '2.0', 'current_user_can()' );

	$author_data = get_userdata($user_id);
	return ($author_data->user_level >= 1);
}

/**
 * Whether user can edit a post.
 *
 * @since 1.5
 * @deprecated 2.0
 * @deprecated Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $post_id
 * @param int $blog_id Not Used
 * @return bool
 */
function user_can_edit_post($user_id, $post_id, $blog_id = 1) {
	_deprecated_function( __FUNCTION__, '2.0', 'current_user_can()' );

	$author_data = get_userdata($user_id);
	$post = get_post($post_id);
	$post_author_data = get_userdata($post->post_author);

	if ( (($user_id == $post_author_data->ID) && !($post->post_status == 'publish' && $author_data->user_level < 2))
			 || ($author_data->user_level > $post_author_data->user_level)
			 || ($author_data->user_level >= 10) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Whether user can delete a post.
 *
 * @since 1.5
 * @deprecated 2.0
 * @deprecated Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $post_id
 * @param int $blog_id Not Used
 * @return bool
 */
function user_can_delete_post($user_id, $post_id, $blog_id = 1) {
	_deprecated_function( __FUNCTION__, '2.0', 'current_user_can()' );

	// right now if one can edit, one can delete
	return user_can_edit_post($user_id, $post_id, $blog_id);
}

/**
 * Whether user can set new posts' dates.
 *
 * @since 1.5
 * @deprecated 2.0
 * @deprecated Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $blog_id Not Used
 * @param int $category_id Not Used
 * @return bool
 */
function user_can_set_post_date($user_id, $blog_id = 1, $category_id = 'None') {
	_deprecated_function( __FUNCTION__, '2.0', 'current_user_can()' );

	$author_data = get_userdata($user_id);
	return (($author_data->user_level > 4) && user_can_create_post($user_id, $blog_id, $category_id));
}

/**
 * Whether user can delete a post.
 *
 * @since 1.5
 * @deprecated 2.0
 * @deprecated Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $post_id
 * @param int $blog_id Not Used
 * @return bool returns true if $user_id can edit $post_id's date
 */
function user_can_edit_post_date($user_id, $post_id, $blog_id = 1) {
	_deprecated_function( __FUNCTION__, '2.0', 'current_user_can()' );

	$author_data = get_userdata($user_id);
	return (($author_data->user_level > 4) && user_can_edit_post($user_id, $post_id, $blog_id));
}

/**
 * Whether user can delete a post.
 *
 * @since 1.5
 * @deprecated 2.0
 * @deprecated Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $post_id
 * @param int $blog_id Not Used
 * @return bool returns true if $user_id can edit $post_id's comments
 */
function user_can_edit_post_comments($user_id, $post_id, $blog_id = 1) {
	_deprecated_function( __FUNCTION__, '2.0', 'current_user_can()' );

	// right now if one can edit a post, one can edit comments made on it
	return user_can_edit_post($user_id, $post_id, $blog_id);
}

/**
 * Whether user can delete a post.
 *
 * @since 1.5
 * @deprecated 2.0
 * @deprecated Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $post_id
 * @param int $blog_id Not Used
 * @return bool returns true if $user_id can delete $post_id's comments
 */
function user_can_delete_post_comments($user_id, $post_id, $blog_id = 1) {
	_deprecated_function( __FUNCTION__, '2.0', 'current_user_can()' );

	// right now if one can edit comments, one can delete comments
	return user_can_edit_post_comments($user_id, $post_id, $blog_id);
}

/**
 * Can user can edit other user.
 *
 * @since 1.5
 * @deprecated 2.0
 * @deprecated Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $other_user
 * @return bool
 */
function user_can_edit_user($user_id, $other_user) {
	_deprecated_function( __FUNCTION__, '2.0', 'current_user_can()' );

	$user  = get_userdata($user_id);
	$other = get_userdata($other_user);
	if ( $user->user_level > $other->user_level || $user->user_level > 8 || $user->ID == $other->ID )
		return true;
	else
		return false;
}

/**
 * Gets the links associated with category $cat_name.
 *
 * @since 0.71
 * @deprecated 2.1
 * @deprecated Use get_bookmarks()
 * @see get_bookmarks()
 *
 * @param string $cat_name Optional. The category name to use. If no match is found uses all.
 * @param string $before Optional. The html to output before the link.
 * @param string $after Optional. The html to output after the link.
 * @param string $between Optional. The html to output between the link/image and it's description. Not used if no image or $show_images is true.
 * @param bool $show_images Optional. Whether to show images (if defined).
 * @param string $orderby Optional. The order to output the links. E.g. 'id', 'name', 'url', 'description' or 'rating'. Or maybe owner.
 *		If you start the name with an underscore the order will be reversed. You can also specify 'rand' as the order which will return links in a
 *		random order.
 * @param bool $show_description Optional. Whether to show the description if show_images=false/not defined.
 * @param bool $show_rating Optional. Show rating stars/chars.
 * @param int $limit		Optional. Limit to X entries. If not specified, all entries are shown.
 * @param int $show_updated Optional. Whether to show last updated timestamp
 */
function get_linksbyname($cat_name = "noname", $before = '', $after = '<br />', $between = " ", $show_images = true, $orderby = 'id',
						 $show_description = true, $show_rating = false,
						 $limit = -1, $show_updated = 0) {
	_deprecated_function( __FUNCTION__, '2.1', 'get_bookmarks()' );

	$cat_id = -1;
	$cat = get_term_by('name', $cat_name, 'link_category');
	if ( $cat )
		$cat_id = $cat->term_id;

	get_links($cat_id, $before, $after, $between, $show_images, $orderby, $show_description, $show_rating, $limit, $show_updated);
}

/**
 * Gets the links associated with the named category.
 *
 * @since 1.0.1
 * @deprecated 2.1
 * @deprecated Use wp_list_bookmarks()
 * @see wp_list_bookmarks()
 *
 * @param string $category The category to use.
 * @param string $args
 * @return bool|null
 */
function wp_get_linksbyname($category, $args = '') {
	_deprecated_function(__FUNCTION__, '0.0', 'wp_list_bookmarks()');

	$defaults = array(
		'after' => '<br />',
		'before' => '',
		'categorize' => 0,
		'category_after' => '',
		'category_before' => '',
		'category_name' => $category,
		'show_description' => 1,
		'title_li' => '',
	);

	$r = wp_parse_args( $args, $defaults );

	return wp_list_bookmarks($r);
}

/**
 * Gets an array of link objects associated with category $cat_name.
 *
 * <code>
 *	$links = get_linkobjectsbyname('fred');
 *	foreach ($links as $link) {
 * 		echo '<li>'.$link->link_name.'</li>';
 *	}
 * </code>
 *
 * @since 1.0.1
 * @deprecated 2.1
 * @deprecated Use get_bookmarks()
 * @see get_bookmarks()
 *
 * @param string $cat_name The category name to use. If no match is found uses all.
 * @param string $orderby The order to output the links. E.g. 'id', 'name', 'url', 'description', or 'rating'.
 *		Or maybe owner. If you start the name with an underscore the order will be reversed. You can also
 *		specify 'rand' as the order which will return links in a random order.
 * @param int $limit Limit to X entries. If not specified, all entries are shown.
 * @return unknown
 */
function get_linkobjectsbyname($cat_name = "noname" , $orderby = 'name', $limit = -1) {
	_deprecated_function( __FUNCTION__, '2.1', 'get_bookmarks()' );

	$cat_id = -1;
	$cat = get_term_by('name', $cat_name, 'link_category');
	if ( $cat )
		$cat_id = $cat->term_id;

	return get_linkobjects($cat_id, $orderby, $limit);
}

/**
 * Gets an array of link objects associated with category n.
 *
 * Usage:
 * <code>
 *	$links = get_linkobjects(1);
 *	if ($links) {
 *		foreach ($links as $link) {
 *			echo '<li>'.$link->link_name.'<br />'.$link->link_description.'</li>';
 *		}
 *	}
 * </code>
 *
 * Fields are:
 * <ol>
 *	<li>link_id</li>
 *	<li>link_url</li>
 *	<li>link_name</li>
 *	<li>link_image</li>
 *	<li>link_target</li>
 *	<li>link_category</li>
 *	<li>link_description</li>
 *	<li>link_visible</li>
 *	<li>link_owner</li>
 *	<li>link_rating</li>
 *	<li>link_updated</li>
 *	<li>link_rel</li>
 *	<li>link_notes</li>
 * </ol>
 *
 * @since 1.0.1
 * @deprecated 2.1
 * @deprecated Use get_bookmarks()
 * @see get_bookmarks()
 *
 * @param int $category The category to use. If no category supplied uses all
 * @param string $orderby the order to output the links. E.g. 'id', 'name', 'url',
 *		'description', or 'rating'. Or maybe owner. If you start the name with an
 *		underscore the order will be reversed. You can also specify 'rand' as the
 *		order which will return links in a random order.
 * @param int $limit Limit to X entries. If not specified, all entries are shown.
 * @return unknown
 */
function get_linkobjects($category = 0, $orderby = 'name', $limit = 0) {
	_deprecated_function( __FUNCTION__, '2.1', 'get_bookmarks()' );

	$links = get_bookmarks( array( 'category' => $category, 'orderby' => $orderby, 'limit' => $limit ) ) ;

	$links_array = array();
	foreach ($links as $link)
		$links_array[] = $link;

	return $links_array;
}

/**
 * Gets the links associated with category 'cat_name' and display rating stars/chars.
 *
 * @since 0.71
 * @deprecated 2.1
 * @deprecated Use get_bookmarks()
 * @see get_bookmarks()
 *
 * @param string $cat_name The category name to use. If no match is found uses all
 * @param string $before The html to output before the link
 * @param string $after The html to output after the link
 * @param string $between The html to output between the link/image and it's description. Not used if no image or show_images is true
 * @param bool $show_images Whether to show images (if defined).
 * @param string $orderby the order to output the links. E.g. 'id', 'name', 'url',
 *		'description', or 'rating'. Or maybe owner. If you start the name with an
 *		underscore the order will be reversed. You can also specify 'rand' as the
 *		order which will return links in a random order.
 * @param bool $show_description Whether to show the description if show_images=false/not defined
 * @param int $limit Limit to X entries. If not specified, all entries are shown.
 * @param int $show_updated Whether to show last updated timestamp
 */
function get_linksbyname_withrating($cat_name = "noname", $before = '', $after = '<br />', $between = " ",
									$show_images = true, $orderby = 'id', $show_description = true, $limit = -1, $show_updated = 0) {
	_deprecated_function( __FUNCTION__, '2.1', 'get_bookmarks()' );

	get_linksbyname($cat_name, $before, $after, $between, $show_images, $orderby, $show_description, true, $limit, $show_updated);
}

/**
 * Gets the links associated with category n and display rating stars/chars.
 *
 * @since 0.71
 * @deprecated 2.1
 * @deprecated Use get_bookmarks()
 * @see get_bookmarks()
 *
 * @param int $category The category to use. If no category supplied uses all
 * @param string $before The html to output before the link
 * @param string $after The html to output after the link
 * @param string $between The html to output between the link/image and it's description. Not used if no image or show_images == true
 * @param bool $show_images Whether to show images (if defined).
 * @param string $orderby The order to output the links. E.g. 'id', 'name', 'url',
 *		'description', or 'rating'. Or maybe owner. If you start the name with an
 *		underscore the order will be reversed. You can also specify 'rand' as the
 *		order which will return links in a random order.
 * @param bool $show_description Whether to show the description if show_images=false/not defined.
 * @param string $limit Limit to X entries. If not specified, all entries are shown.
 * @param int $show_updated Whether to show last updated timestamp
 */
function get_links_withrating($category = -1, $before = '', $after = '<br />', $between = " ", $show_images = true,
							  $orderby = 'id', $show_description = true, $limit = -1, $show_updated = 0) {
	_deprecated_function( __FUNCTION__, '2.1', 'get_bookmarks()' );

	get_links($category, $before, $after, $between, $show_images, $orderby, $show_description, true, $limit, $show_updated);
}

/**
 * Gets the auto_toggle setting.
 *
 * @since 0.71
 * @deprecated 2.1
 * @deprecated No alternative function available
 *
 * @param int $id The category to get. If no category supplied uses 0
 * @return int Only returns 0.
 */
function get_autotoggle($id = 0) {
	_deprecated_function( __FUNCTION__, '2.1' );
	return 0;
}

/**
 * @since 0.71
 * @deprecated 2.1
 * @deprecated Use wp_list_categories()
 * @see wp_list_categories()
 *
 * @param int $optionall
 * @param string $all
 * @param string $sort_column
 * @param string $sort_order
 * @param string $file
 * @param bool $list
 * @param int $optiondates
 * @param int $optioncount
 * @param int $hide_empty
 * @param int $use_desc_for_title
 * @param bool $children
 * @param int $child_of
 * @param int $categories
 * @param int $recurse
 * @param string $feed
 * @param string $feed_image
 * @param string $exclude
 * @param bool $hierarchical
 * @return unknown
 */
function list_cats($optionall = 1, $all = 'All', $sort_column = 'ID', $sort_order = 'asc', $file = '', $list = true, $optiondates = 0,
				   $optioncount = 0, $hide_empty = 1, $use_desc_for_title = 1, $children=false, $child_of=0, $categories=0,
				   $recurse=0, $feed = '', $feed_image = '', $exclude = '', $hierarchical=false) {
	_deprecated_function( __FUNCTION__, '2.1', 'wp_list_categories()' );

	$query = compact('optionall', 'all', 'sort_column', 'sort_order', 'file', 'list', 'optiondates', 'optioncount', 'hide_empty', 'use_desc_for_title', 'children',
		'child_of', 'categories', 'recurse', 'feed', 'feed_image', 'exclude', 'hierarchical');
	return wp_list_cats($query);
}

/**
 * @since 1.2
 * @deprecated 2.1
 * @deprecated Use wp_list_categories()
 * @see wp_list_categories()
 *
 * @param string|array $args
 * @return unknown
 */
function wp_list_cats($args = '') {
	_deprecated_function( __FUNCTION__, '2.1', 'wp_list_categories()' );

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

/**
 * @since 0.71
 * @deprecated 2.1
 * @deprecated Use wp_dropdown_categories()
 * @see wp_dropdown_categories()
 *
 * @param int $optionall
 * @param string $all
 * @param string $orderby
 * @param string $order
 * @param int $show_last_update
 * @param int $show_count
 * @param int $hide_empty
 * @param bool $optionnone
 * @param int $selected
 * @param int $exclude
 * @return unknown
 */
function dropdown_cats($optionall = 1, $all = 'All', $orderby = 'ID', $order = 'asc',
		$show_last_update = 0, $show_count = 0, $hide_empty = 1, $optionnone = false,
		$selected = 0, $exclude = 0) {
	_deprecated_function( __FUNCTION__, '2.1', 'wp_dropdown_categories()' );

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

/**
 * @since 1.2
 * @deprecated 2.1
 * @deprecated Use wp_list_authors()
 * @see wp_list_authors()
 *
 * @param bool $optioncount
 * @param bool $exclude_admin
 * @param bool $show_fullname
 * @param bool $hide_empty
 * @param string $feed
 * @param string $feed_image
 * @return unknown
 */
function list_authors($optioncount = false, $exclude_admin = true, $show_fullname = false, $hide_empty = true, $feed = '', $feed_image = '') {
	_deprecated_function( __FUNCTION__, '2.1', 'wp_list_authors()' );

	$args = compact('optioncount', 'exclude_admin', 'show_fullname', 'hide_empty', 'feed', 'feed_image');
	return wp_list_authors($args);
}

/**
 * @since 1.0.1
 * @deprecated 2.1
 * @deprecated Use wp_get_post_categories()
 * @see wp_get_post_categories()
 *
 * @param int $blogid Not Used
 * @param int $post_ID
 * @return unknown
 */
function wp_get_post_cats($blogid = '1', $post_ID = 0) {
	_deprecated_function( __FUNCTION__, '2.1', 'wp_get_post_categories()' );
	return wp_get_post_categories($post_ID);
}

/**
 * Sets the categories that the post id belongs to.
 *
 * @since 1.0.1
 * @deprecated 2.1
 * @deprecated Use wp_set_post_categories()
 * @see wp_set_post_categories()
 *
 * @param int $blogid Not used
 * @param int $post_ID
 * @param array $post_categories
 * @return unknown
 */
function wp_set_post_cats($blogid = '1', $post_ID = 0, $post_categories = array()) {
	_deprecated_function( __FUNCTION__, '2.1', 'wp_set_post_categories()' );
	return wp_set_post_categories($post_ID, $post_categories);
}

/**
 * @since 0.71
 * @deprecated 2.1
 * @deprecated Use wp_get_archives()
 * @see wp_get_archives()
 *
 * @param string $type
 * @param string $limit
 * @param string $format
 * @param string $before
 * @param string $after
 * @param bool $show_post_count
 * @return unknown
 */
function get_archives($type='', $limit='', $format='html', $before = '', $after = '', $show_post_count = false) {
	_deprecated_function( __FUNCTION__, '2.1', 'wp_get_archives()' );
	$args = compact('type', 'limit', 'format', 'before', 'after', 'show_post_count');
	return wp_get_archives($args);
}

/**
 * Returns or Prints link to the author's posts.
 *
 * @since 1.2
 * @deprecated 2.1
 * @deprecated Use get_author_posts_url()
 * @see get_author_posts_url()
 *
 * @param bool $echo Optional.
 * @param int $author_id Required.
 * @param string $author_nicename Optional.
 * @return string|null
 */
function get_author_link($echo = false, $author_id, $author_nicename = '') {
	_deprecated_function( __FUNCTION__, '2.1', 'get_author_posts_url()' );

	$link = get_author_posts_url($author_id, $author_nicename);

	if ( $echo )
		echo $link;
	return $link;
}

/**
 * Print list of pages based on arguments.
 *
 * @since 0.71
 * @deprecated 2.1
 * @deprecated Use wp_link_pages()
 * @see wp_link_pages()
 *
 * @param string $before
 * @param string $after
 * @param string $next_or_number
 * @param string $nextpagelink
 * @param string $previouspagelink
 * @param string $pagelink
 * @param string $more_file
 * @return string
 */
function link_pages($before='<br />', $after='<br />', $next_or_number='number', $nextpagelink='next page', $previouspagelink='previous page',
					$pagelink='%', $more_file='') {
	_deprecated_function( __FUNCTION__, '2.1', 'wp_link_pages()' );

	$args = compact('before', 'after', 'next_or_number', 'nextpagelink', 'previouspagelink', 'pagelink', 'more_file');
	return wp_link_pages($args);
}

/**
 * Get value based on option.
 *
 * @since 0.71
 * @deprecated 2.1
 * @deprecated Use get_option()
 * @see get_option()
 *
 * @param string $option
 * @return string
 */
function get_settings($option) {
	_deprecated_function( __FUNCTION__, '2.1', 'get_option()' );

	return get_option($option);
}

/**
 * Print the permalink of the current post in the loop.
 *
 * @since 0.71
 * @deprecated 1.2
 * @deprecated Use the_permalink()
 * @see the_permalink()
 */
function permalink_link() {
	_deprecated_function( __FUNCTION__, '1.2', 'the_permalink()' );
	the_permalink();
}

/**
 * Print the permalink to the RSS feed.
 *
 * @since 0.71
 * @deprecated 2.3
 * @deprecated Use the_permalink_rss()
 * @see the_permalink_rss()
 *
 * @param string $file
 */
function permalink_single_rss($deprecated = '') {
	_deprecated_function( __FUNCTION__, '0.0', 'the_permalink_rss()' );
	the_permalink_rss();
}

/**
 * Gets the links associated with category.
 *
 * @see get_links() for argument information that can be used in $args
 * @since 1.0.1
 * @deprecated 2.1
 * @deprecated Use wp_list_bookmarks()
 * @see wp_list_bookmarks()
 *
 * @param string $args a query string
 * @return null|string
 */
function wp_get_links($args = '') {
	_deprecated_function( __FUNCTION__, '0.0', 'wp_list_bookmarks()' );

	if ( strpos( $args, '=' ) === false ) {
		$cat_id = $args;
		$args = add_query_arg( 'category', $cat_id, $args );
	}

	$defaults = array(
		'after' => '<br />',
		'before' => '',
		'between' => ' ',
		'categorize' => 0,
		'category' => '',
		'echo' => true,
		'limit' => -1,
		'orderby' => 'name',
		'show_description' => true,
		'show_images' => true,
		'show_rating' => false,
		'show_updated' => true,
		'title_li' => '',
	);

	$r = wp_parse_args( $args, $defaults );

	return wp_list_bookmarks($r);
}

/**
 * Gets the links associated with category by id.
 *
 * @since 0.71
 * @deprecated 2.1
 * @deprecated Use get_bookmarks()
 * @see get_bookmarks()
 *
 * @param int $category The category to use. If no category supplied uses all
 * @param string $before the html to output before the link
 * @param string $after the html to output after the link
 * @param string $between the html to output between the link/image and its description.
 *		Not used if no image or show_images == true
 * @param bool $show_images whether to show images (if defined).
 * @param string $orderby the order to output the links. E.g. 'id', 'name', 'url',
 *		'description', or 'rating'. Or maybe owner. If you start the name with an
 *		underscore the order will be reversed. You can also specify 'rand' as the order
 *		which will return links in a random order.
 * @param bool $show_description whether to show the description if show_images=false/not defined.
 * @param bool $show_rating show rating stars/chars
 * @param int $limit Limit to X entries. If not specified, all entries are shown.
 * @param int $show_updated whether to show last updated timestamp
 * @param bool $echo whether to echo the results, or return them instead
 * @return null|string
 */
function get_links($category = -1, $before = '', $after = '<br />', $between = ' ', $show_images = true, $orderby = 'name',
			$show_description = true, $show_rating = false, $limit = -1, $show_updated = 1, $echo = true) {
	_deprecated_function( __FUNCTION__, '2.1', 'get_bookmarks()' );

	$order = 'ASC';
	if ( substr($orderby, 0, 1) == '_' ) {
		$order = 'DESC';
		$orderby = substr($orderby, 1);
	}

	if ( $category == -1 ) //get_bookmarks uses '' to signify all categories
		$category = '';

	$results = get_bookmarks(array('category' => $category, 'orderby' => $orderby, 'order' => $order, 'show_updated' => $show_updated, 'limit' => $limit));

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
			$the_link = esc_url($row->link_url);
		$rel = $row->link_rel;
		if ( '' != $rel )
			$rel = ' rel="' . $rel . '"';

		$desc = esc_attr(sanitize_bookmark_field('link_description', $row->link_description, $row->link_id, 'display'));
		$name = esc_attr(sanitize_bookmark_field('link_name', $row->link_name, $row->link_id, 'display'));
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

/**
 * Output entire list of links by category.
 *
 * Output a list of all links, listed by category, using the settings in
 * $wpdb->linkcategories and output it as a nested HTML unordered list.
 *
 * @author Dougal
 * @since 1.0.1
 * @deprecated 2.1
 * @deprecated Use wp_list_bookmarks()
 * @see wp_list_bookmarks()
 *
 * @param string $order Sort link categories by 'name' or 'id'
 */
function get_links_list($order = 'name') {
	_deprecated_function( __FUNCTION__, '2.1', 'wp_list_bookmarks()' );

	$order = strtolower($order);

	// Handle link category sorting
	$direction = 'ASC';
	if ( '_' == substr($order,0,1) ) {
		$direction = 'DESC';
		$order = substr($order,1);
	}

	if ( !isset($direction) )
		$direction = '';

	$cats = get_categories(array('type' => 'link', 'orderby' => $order, 'order' => $direction, 'hierarchical' => 0));

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

/**
 * Show the link to the links popup and the number of links.
 *
 * @author Fullo
 * @link http://sprite.csr.unibo.it/fullo/
 *
 * @since 0.71
 * @deprecated 2.1
 * @deprecated {@internal Use function instead is unknown}}
 *
 * @param string $text the text of the link
 * @param int $width the width of the popup window
 * @param int $height the height of the popup window
 * @param string $file the page to open in the popup window
 * @param bool $count the number of links in the db
 */
function links_popup_script($text = 'Links', $width=400, $height=400, $file='links.all.php', $count = true) {
	_deprecated_function( __FUNCTION__, '2.1' );

	if ( $count )
		$counts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->links");

	$javascript = "<a href=\"#\" onclick=\"javascript:window.open('$file?popup=1', '_blank', 'width=$width,height=$height,scrollbars=yes,status=no'); return false\">";
	$javascript .= $text;

	if ( $count )
		$javascript .= " ($counts)";

	$javascript .= "</a>\n\n";
		echo $javascript;
}

/**
 * @since 1.0.1
 * @deprecated 2.1
 * @deprecated Use sanitize_bookmark_field()
 * @see sanitize_bookmark_field()
 *
 * @param object $link
 * @return unknown
 */
function get_linkrating($link) {
	_deprecated_function( __FUNCTION__, '2.1', 'sanitize_bookmark_field()' );
	return sanitize_bookmark_field('link_rating', $link->link_rating, $link->link_id, 'display');
}

/**
 * Gets the name of category by id.
 *
 * @since 0.71
 * @deprecated 2.1
 * @deprecated Use get_category()
 * @see get_category()
 *
 * @param int $id The category to get. If no category supplied uses 0
 * @return string
 */
function get_linkcatname($id = 0) {
	_deprecated_function( __FUNCTION__, '2.1', 'get_category()' );

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

/**
 * Print RSS comment feed link.
 *
 * @since 1.0.1
 * @deprecated 2.5
 * @deprecated Use post_comments_feed_link()
 * @see post_comments_feed_link()
 *
 * @param string $link_text
 */
function comments_rss_link($link_text = 'Comments RSS') {
	_deprecated_function( __FUNCTION__, '2.5', 'post_comments_feed_link()' );
	post_comments_feed_link($link_text);
}

/**
 * Print/Return link to category RSS2 feed.
 *
 * @since 1.2
 * @deprecated 2.5
 * @deprecated Use get_category_feed_link()
 * @see get_category_feed_link()
 *
 * @param bool $echo
 * @param int $cat_ID
 * @return string|null
 */
function get_category_rss_link($echo = false, $cat_ID = 1) {
	_deprecated_function( __FUNCTION__, '2.5', 'get_category_feed_link()' );

	$link = get_category_feed_link($cat_ID, 'rss2');

	if ( $echo )
		echo $link;
	return $link;
}

/**
 * Print/Return link to author RSS feed.
 *
 * @since 1.2
 * @deprecated 2.5
 * @deprecated Use get_author_feed_link()
 * @see get_author_feed_link()
 *
 * @param bool $echo
 * @param int $author_id
 * @return string|null
 */
function get_author_rss_link($echo = false, $author_id = 1) {
	_deprecated_function( __FUNCTION__, '2.5', 'get_author_feed_link()' );

	$link = get_author_feed_link($author_id);
	if ( $echo )
		echo $link;
	return $link;
}

/**
 * Return link to the post RSS feed.
 *
 * @since 1.5
 * @deprecated 2.2
 * @deprecated Use get_post_comments_feed_link()
 * @see get_post_comments_feed_link()
 *
 * @return string
 */
function comments_rss() {
	_deprecated_function( __FUNCTION__, '2.2', 'get_post_comments_feed_link()' );
	return get_post_comments_feed_link();
}

/**
 * An alias of wp_create_user().
 *
 * @since 2.0
 * @deprecated 2.0
 * @deprecated Use wp_create_user()
 * @see wp_create_user()
 *
 * @param string $username The user's username.
 * @param string $password The user's password.
 * @param string $email The user's email (optional).
 * @return int The new user's ID.
 */
function create_user($username, $password, $email) {
	_deprecated_function( __FUNCTION__, '2.0', 'wp_create_user()' );
	return wp_create_user($username, $password, $email);
}

/**
 * Unused function.
 *
 * @deprecated 2.5
*/
function gzip_compression() {
	_deprecated_function( __FUNCTION__, '2.5' );
	return false;
}

/**
 * Retrieve an array of comment data about comment $comment_ID.
 *
 * @since 0.71
 * @deprecated 2.7
 * @deprecated Use get_comment()
 * @see get_comment()
 *
 * @param int $comment_ID The ID of the comment
 * @param int $no_cache Whether to use the cache or not (casted to bool)
 * @param bool $include_unapproved Whether to include unapproved comments or not
 * @return array The comment data
 */
function get_commentdata( $comment_ID, $no_cache = 0, $include_unapproved = false ) {
	_deprecated_function( __FUNCTION__, '2.7', 'get_comment()' );
	return get_comment($comment_ID, ARRAY_A);
}

/**
 * Retrieve the category name by the category ID.
 *
 * @since 0.71
 * @deprecated 2.8
 * @deprecated Use get_cat_name()
 * @see get_cat_name()
 *
 * @param int $cat_ID Category ID
 * @return string category name
 */
function get_catname( $cat_ID ) {
	_deprecated_function( __FUNCTION__, '2.8', 'get_cat_name()' );
	return get_cat_name( $cat_ID );
}

/**
 * Retrieve category children list separated before and after the term IDs.
 *
 * @since 1.2.0
 * @deprecated 2.8
 * @deprecated Use get_term_children()
 * @see get_term_children()
 *
 * @param int $id Category ID to retrieve children.
 * @param string $before Optional. Prepend before category term ID.
 * @param string $after Optional, default is empty string. Append after category term ID.
 * @param array $visited Optional. Category Term IDs that have already been added.
 * @return string
 */
function get_category_children( $id, $before = '/', $after = '', $visited = array() ) {
	_deprecated_function( __FUNCTION__, '2.8', 'get_term_children()' );
	if ( 0 == $id )
		return '';

	$chain = '';
	/** TODO: consult hierarchy */
	$cat_ids = get_all_category_ids();
	foreach ( (array) $cat_ids as $cat_id ) {
		if ( $cat_id == $id )
			continue;

		$category = get_category( $cat_id );
		if ( is_wp_error( $category ) )
			return $category;
		if ( $category->parent == $id && !in_array( $category->term_id, $visited ) ) {
			$visited[] = $category->term_id;
			$chain .= $before.$category->term_id.$after;
			$chain .= get_category_children( $category->term_id, $before, $after );
		}
	}
	return $chain;
}

/**
 * Retrieve the description of the author of the current post.
 *
 * @since 1.5
 * @deprecated 2.8
 * @deprecated Use the_author_meta('description')
 * @see get_the_author_meta()
 *
 * @return string The author's description.
 */
function get_the_author_description() {
	_deprecated_function( __FUNCTION__, '2.8', 'get_the_author_meta(\'description\')' );
	return get_the_author_meta('description');
}

/**
 * Display the description of the author of the current post.
 *
 * @since 1.0.0
 * @deprecated 2.8
 * @deprecated Use the_author_meta('description')
 * @see the_author_meta()
 */
function the_author_description() {
	_deprecated_function( __FUNCTION__, '2.8', 'the_author_meta(\'description\')' );
	the_author_meta('description');
}

/**
 * Retrieve the login name of the author of the current post.
 *
 * @since 1.5
 * @deprecated 2.8
 * @deprecated Use the_author_meta('login')
 * @see get_the_author_meta()
 *
 * @return string The author's login name (username).
 */
function get_the_author_login() {
	_deprecated_function( __FUNCTION__, '2.8', 'get_the_author_meta(\'login\')' );
	return get_the_author_meta('login');
}

/**
 * Display the login name of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8
 * @deprecated Use the_author_meta('login')
 * @see the_author_meta()
 */
function the_author_login() {
	_deprecated_function( __FUNCTION__, '2.8', 'the_author_meta(\'login\')' );
	the_author_meta('login');
}

/**
 * Retrieve the first name of the author of the current post.
 *
 * @since 1.5
 * @deprecated 2.8
 * @deprecated Use the_author_meta('first_name')
 * @see get_the_author_meta()
 *
 * @return string The author's first name.
 */
function get_the_author_firstname() {
	_deprecated_function( __FUNCTION__, '2.8', 'get_the_author_meta(\'first_name\')' );
	return get_the_author_meta('first_name');
}

/**
 * Display the first name of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8
 * @deprecated Use the_author_meta('first_name')
 * @see the_author_meta()
 */
function the_author_firstname() {
	_deprecated_function( __FUNCTION__, '2.8', 'the_author_meta(\'first_name\')' );
	the_author_meta('first_name');
}

/**
 * Retrieve the last name of the author of the current post.
 *
 * @since 1.5
 * @deprecated 2.8
 * @deprecated Use get_the_author_meta('last_name')
 * @see get_the_author_meta()
 *
 * @return string The author's last name.
 */
function get_the_author_lastname() {
	_deprecated_function( __FUNCTION__, '2.8', 'get_the_author_meta(\'last_name\')' );
	return get_the_author_meta('last_name');
}

/**
 * Display the last name of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8
 * @deprecated Use the_author_meta('last_name')
 * @see the_author_meta()
 */
function the_author_lastname() {
	_deprecated_function( __FUNCTION__, '2.8', 'the_author_meta(\'last_name\')' );
	the_author_meta('last_name');
}

/**
 * Retrieve the nickname of the author of the current post.
 *
 * @since 1.5
 * @deprecated 2.8
 * @deprecated Use get_the_author_meta('nickname')
 * @see get_the_author_meta()
 *
 * @return string The author's nickname.
 */
function get_the_author_nickname() {
	_deprecated_function( __FUNCTION__, '2.8', 'get_the_author_meta(\'nickname\')' );
	return get_the_author_meta('nickname');
}

/**
 * Display the nickname of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8
 * @deprecated Use the_author_meta('nickname')
 * @see the_author_meta()
 */
function the_author_nickname() {
	_deprecated_function( __FUNCTION__, '2.8', 'the_author_meta(\'nickname\')' );
	the_author_meta('nickname');
}

/**
 * Retrieve the email of the author of the current post.
 *
 * @since 1.5
 * @deprecated 2.8
 * @deprecated Use get_the_author_meta('email')
 * @see get_the_author_meta()
 *
 * @return string The author's username.
 */
function get_the_author_email() {
	_deprecated_function( __FUNCTION__, '2.8', 'get_the_author_meta(\'email\')' );
	return get_the_author_meta('email');
}

/**
 * Display the email of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8
 * @deprecated Use the_author_meta('email')
 * @see the_author_meta()
 */
function the_author_email() {
	_deprecated_function( __FUNCTION__, '2.8', 'the_author_meta(\'email\')' );
	the_author_meta('email');
}

/**
 * Retrieve the ICQ number of the author of the current post.
 *
 * @since 1.5
 * @deprecated 2.8
 * @deprecated Use get_the_author_meta('icq')
 * @see get_the_author_meta()
 *
 * @return string The author's ICQ number.
 */
function get_the_author_icq() {
	_deprecated_function( __FUNCTION__, '2.8', 'get_the_author_meta(\'icq\')' );
	return get_the_author_meta('icq');
}

/**
 * Display the ICQ number of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8
 * @deprecated Use the_author_meta('icq')
 * @see the_author_meta()
 */
function the_author_icq() {
	_deprecated_function( __FUNCTION__, '2.8', 'the_author_meta(\'icq\')' );
	the_author_meta('icq');
}

/**
 * Retrieve the Yahoo! IM name of the author of the current post.
 *
 * @since 1.5
 * @deprecated 2.8
 * @deprecated Use get_the_author_meta('yim')
 * @see get_the_author_meta()
 *
 * @return string The author's Yahoo! IM name.
 */
function get_the_author_yim() {
	_deprecated_function( __FUNCTION__, '2.8', 'get_the_author_meta(\'yim\')' );
	return get_the_author_meta('yim');
}

/**
 * Display the Yahoo! IM name of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8
 * @deprecated Use the_author_meta('yim')
 * @see the_author_meta()
 */
function the_author_yim() {
	_deprecated_function( __FUNCTION__, '2.8', 'the_author_meta(\'yim\')' );
	the_author_meta('yim');
}

/**
 * Retrieve the MSN address of the author of the current post.
 *
 * @since 1.5
 * @deprecated 2.8
 * @deprecated Use get_the_author_meta('msn')
 * @see get_the_author_meta()
 *
 * @return string The author's MSN address.
 */
function get_the_author_msn() {
	_deprecated_function( __FUNCTION__, '2.8', 'get_the_author_meta(\'msn\')' );
	return get_the_author_meta('msn');
}

/**
 * Display the MSN address of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8
 * @deprecated Use the_author_meta('msn')
 * @see the_author_meta()
 */
function the_author_msn() {
	_deprecated_function( __FUNCTION__, '2.8', 'the_author_meta(\'msn\')' );
	the_author_meta('msn');
}

/**
 * Retrieve the AIM address of the author of the current post.
 *
 * @since 1.5
 * @deprecated 2.8
 * @deprecated Use get_the_author_meta('aim')
 * @see get_the_author_meta()
 *
 * @return string The author's AIM address.
 */
function get_the_author_aim() {
	_deprecated_function( __FUNCTION__, '2.8', 'get_the_author_meta(\'aim\')' );
	return get_the_author_meta('aim');
}

/**
 * Display the AIM address of the author of the current post.
 *
 * @since 0.71
 * @see the_author_meta()
 * @deprecated 2.8
 * @deprecated Use the_author_meta('aim')
 */
function the_author_aim() {
	_deprecated_function( __FUNCTION__, '2.8', 'the_author_meta(\'aim\')' );
	the_author_meta('aim');
}

/**
 * Retrieve the specified author's preferred display name.
 *
 * @since 1.0.0
 * @deprecated 2.8
 * @deprecated Use get_the_author_meta('display_name')
 * @see get_the_author_meta()
 *
 * @param int $auth_id The ID of the author.
 * @return string The author's display name.
 */
function get_author_name( $auth_id = false ) {
	_deprecated_function( __FUNCTION__, '2.8', 'get_the_author_meta(\'display_name\')' );
	return get_the_author_meta('display_name', $auth_id);
}

/**
 * Retrieve the URL to the home page of the author of the current post.
 *
 * @since 1.5
 * @deprecated 2.8
 * @deprecated Use get_the_author_meta('url')
 * @see get_the_author_meta()
 *
 * @return string The URL to the author's page.
 */
function get_the_author_url() {
	_deprecated_function( __FUNCTION__, '2.8', 'get_the_author_meta(\'url\')' );
	return get_the_author_meta('url');
}

/**
 * Display the URL to the home page of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8
 * @deprecated Use the_author_meta('url')
 * @see the_author_meta()
 */
function the_author_url() {
	_deprecated_function( __FUNCTION__, '2.8', 'the_author_meta(\'url\')' );
	the_author_meta('url');
}

/**
 * Retrieve the ID of the author of the current post.
 *
 * @since 1.5
 * @deprecated 2.8
 * @deprecated Use get_the_author_meta('ID')
 * @see get_the_author_meta()
 *
 * @return int The author's ID.
 */
function get_the_author_ID() {
	_deprecated_function( __FUNCTION__, '2.8', 'get_the_author_meta(\'ID\')' );
	return get_the_author_meta('ID');
}

/**
 * Display the ID of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8
 * @deprecated Use the_author_meta('ID')
 * @see the_author_meta()
*/
function the_author_ID() {
	_deprecated_function( __FUNCTION__, '2.8', 'the_author_meta(\'ID\')' );
	the_author_meta('ID');
}

/**
 * Display the post content for the feed.
 *
 * For encoding the html or the $encode_html parameter, there are three possible
 * values. '0' will make urls footnotes and use make_url_footnote(). '1' will
 * encode special characters and automatically display all of the content. The
 * value of '2' will strip all HTML tags from the content.
 *
 * Also note that you cannot set the amount of words and not set the html
 * encoding. If that is the case, then the html encoding will default to 2,
 * which will strip all HTML tags.
 *
 * To restrict the amount of words of the content, you can use the cut
 * parameter. If the content is less than the amount, then there won't be any
 * dots added to the end. If there is content left over, then dots will be added
 * and the rest of the content will be removed.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 0.71
 * @uses apply_filters() Calls 'the_content_rss' on the content before processing.
 * @see get_the_content() For the $more_link_text, $stripteaser, and $more_file
 *		parameters.
 *
 * @deprecated 2.9.0
 * @deprecated Use the_content_feed()
 * @see the_content_feed()
 *
 * @param string $more_link_text Optional. Text to display when more content is available but not displayed.
 * @param int|bool $stripteaser Optional. Default is 0.
 * @param string $more_file Optional.
 * @param int $cut Optional. Amount of words to keep for the content.
 * @param int $encode_html Optional. How to encode the content.
 */
function the_content_rss($more_link_text='(more...)', $stripteaser=0, $more_file='', $cut = 0, $encode_html = 0) {
	_deprecated_function( __FUNCTION__, '2.9', 'the_content_feed' );
	$content = get_the_content($more_link_text, $stripteaser, $more_file);
	$content = apply_filters('the_content_rss', $content);
	if ( $cut && !$encode_html )
		$encode_html = 2;
	if ( 1== $encode_html ) {
		$content = esc_html($content);
		$cut = 0;
	} elseif ( 0 == $encode_html ) {
		$content = make_url_footnote($content);
	} elseif ( 2 == $encode_html ) {
		$content = strip_tags($content);
	}
	if ( $cut ) {
		$blah = explode(' ', $content);
		if ( count($blah) > $cut ) {
			$k = $cut;
			$use_dotdotdot = 1;
		} else {
			$k = count($blah);
			$use_dotdotdot = 0;
		}

		/** @todo Check performance, might be faster to use array slice instead. */
		for ( $i=0; $i<$k; $i++ )
			$excerpt .= $blah[$i].' ';
		$excerpt .= ($use_dotdotdot) ? '...' : '';
		$content = $excerpt;
	}
	$content = str_replace(']]>', ']]&gt;', $content);
	echo $content;
}

/**
 * Strip HTML and put links at the bottom of stripped content.
 *
 * Searches for all of the links, strips them out of the content, and places
 * them at the bottom of the content with numbers.
 *
 * @since 0.71
 * @deprecated 2.9.0
 *
 * @param string $content Content to get links
 * @return string HTML stripped out of content with links at the bottom.
 */
function make_url_footnote( $content ) {
	_deprecated_function( __FUNCTION__, '2.9', '' );
	preg_match_all( '/<a(.+?)href=\"(.+?)\"(.*?)>(.+?)<\/a>/', $content, $matches );
	$links_summary = "\n";
	for ( $i=0; $i<count($matches[0]); $i++ ) {
		$link_match = $matches[0][$i];
		$link_number = '['.($i+1).']';
		$link_url = $matches[2][$i];
		$link_text = $matches[4][$i];
		$content = str_replace( $link_match, $link_text . ' ' . $link_number, $content );
		$link_url = ( ( strtolower( substr( $link_url, 0, 7 ) ) != 'http://' ) && ( strtolower( substr( $link_url, 0, 8 ) ) != 'https://' ) ) ? get_option( 'home' ) . $link_url : $link_url;
		$links_summary .= "\n" . $link_number . ' ' . $link_url;
	}
	$content  = strip_tags( $content );
	$content .= $links_summary;
	return $content;
}

/**
 * Retrieve translated string with vertical bar context
 *
 * Quite a few times, there will be collisions with similar translatable text
 * found in more than two places but with different translated context.
 *
 * In order to use the separate contexts, the _c() function is used and the
 * translatable string uses a pipe ('|') which has the context the string is in.
 *
 * When the translated string is returned, it is everything before the pipe, not
 * including the pipe character. If there is no pipe in the translated text then
 * everything is returned.
 *
 * @since 2.2.0
 * @deprecated 2.9.0
 * @deprecated Use _x()
 * @see _x()
 *
 * @param string $text Text to translate
 * @param string $domain Optional. Domain to retrieve the translated text
 * @return string Translated context string without pipe
 */
function _c( $text, $domain = 'default' ) {
	_deprecated_function( __FUNCTION__, '2.9', '_x()' );
	return translate_with_context( $text, $domain );
}

/**
 * A version of _n(), which supports contexts.
 * Strips everything from the translation after the last bar.
 *
 * @since 2.7.0
 * @deprecated 3.0.0
 * @deprecated Use _nx()
 * @see _nx()
 * @see _n() For parameters.
 * @see _c() For parameters. _c() is deprecated.
 *
 */
function _nc( $single, $plural, $number, $domain = 'default' ) {
	_deprecated_function( __FUNCTION__, '2.9', '_nx()' );
	return before_last_bar( _n( $single, $plural, $number, $domain ) );
}

/**
 * Retrieve all autoload options, or all options if no autoloaded ones exist.
 *
 * @since 1.0.0
 * @deprecated 3.0.0
 * @deprecated Use wp_load_alloptions())
 * @see wp_load_alloptions()
 *
 * @return array List of all options.
 */
function get_alloptions() {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_load_alloptions()' );
	return wp_load_alloptions();
}

/**
 * Retrieve HTML content of attachment image with link.
 *
 * @since 2.0.0
 * @deprecated 2.5.0
 * @deprecated Use wp_get_attachment_link()
 * @see wp_get_attachment_link()
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default is false. Whether to use full size image.
 * @param array $max_dims Optional. Max image dimensions.
 * @param bool $permalink Optional, default is false. Whether to include permalink to image.
 * @return string
 */
function get_the_attachment_link($id = 0, $fullsize = false, $max_dims = false, $permalink = false) {
	_deprecated_function( __FUNCTION__, '2.5', 'wp_get_attachment_link()' );
	$id = (int) $id;
	$_post = & get_post($id);

	if ( ('attachment' != $_post->post_type) || !$url = wp_get_attachment_url($_post->ID) )
		return __('Missing Attachment');

	if ( $permalink )
		$url = get_attachment_link($_post->ID);

	$post_title = esc_attr($_post->post_title);

	$innerHTML = get_attachment_innerHTML($_post->ID, $fullsize, $max_dims);
	return "<a href='$url' title='$post_title'>$innerHTML</a>";
}

/**
 * Retrieve icon URL and Path.
 *
 * @since 2.1.0
 * @deprecated 2.5.0
 * @deprecated Use wp_get_attachment_image_src()
 * @see wp_get_attachment_image_src()
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default to false. Whether to have full image.
 * @return array Icon URL and full path to file, respectively.
 */
function get_attachment_icon_src( $id = 0, $fullsize = false ) {
	_deprecated_function( __FUNCTION__, '2.5', 'wp_get_attachment_image_src()' );
	$id = (int) $id;
	if ( !$post = & get_post($id) )
		return false;

	$file = get_attached_file( $post->ID );

	if ( !$fullsize && $src = wp_get_attachment_thumb_url( $post->ID ) ) {
		// We have a thumbnail desired, specified and existing

		$src_file = basename($src);
		$class = 'attachmentthumb';
	} elseif ( wp_attachment_is_image( $post->ID ) ) {
		// We have an image without a thumbnail

		$src = wp_get_attachment_url( $post->ID );
		$src_file = & $file;
		$class = 'attachmentimage';
	} elseif ( $src = wp_mime_type_icon( $post->ID ) ) {
		// No thumb, no image. We'll look for a mime-related icon instead.

		$icon_dir = apply_filters( 'icon_dir', get_template_directory() . '/images' );
		$src_file = $icon_dir . '/' . basename($src);
	}

	if ( !isset($src) || !$src )
		return false;

	return array($src, $src_file);
}

/**
 * Retrieve HTML content of icon attachment image element.
 *
 * @since 2.0.0
 * @deprecated 2.5.0
 * @deprecated Use wp_get_attachment_image()
 * @see wp_get_attachment_image()
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default to false. Whether to have full size image.
 * @param array $max_dims Optional. Dimensions of image.
 * @return string HTML content.
 */
function get_attachment_icon( $id = 0, $fullsize = false, $max_dims = false ) {
	_deprecated_function( __FUNCTION__, '2.5', 'wp_get_attachment_image()' );
	$id = (int) $id;
	if ( !$post = & get_post($id) )
		return false;

	if ( !$src = get_attachment_icon_src( $post->ID, $fullsize ) )
		return false;

	list($src, $src_file) = $src;

	// Do we need to constrain the image?
	if ( ($max_dims = apply_filters('attachment_max_dims', $max_dims)) && file_exists($src_file) ) {

		$imagesize = getimagesize($src_file);

		if (($imagesize[0] > $max_dims[0]) || $imagesize[1] > $max_dims[1] ) {
			$actual_aspect = $imagesize[0] / $imagesize[1];
			$desired_aspect = $max_dims[0] / $max_dims[1];

			if ( $actual_aspect >= $desired_aspect ) {
				$height = $actual_aspect * $max_dims[0];
				$constraint = "width='{$max_dims[0]}' ";
				$post->iconsize = array($max_dims[0], $height);
			} else {
				$width = $max_dims[1] / $actual_aspect;
				$constraint = "height='{$max_dims[1]}' ";
				$post->iconsize = array($width, $max_dims[1]);
			}
		} else {
			$post->iconsize = array($imagesize[0], $imagesize[1]);
			$constraint = '';
		}
	} else {
		$constraint = '';
	}

	$post_title = esc_attr($post->post_title);

	$icon = "<img src='$src' title='$post_title' alt='$post_title' $constraint/>";

	return apply_filters( 'attachment_icon', $icon, $post->ID );
}

/**
 * Retrieve HTML content of image element.
 *
 * @since 2.0.0
 * @deprecated 2.5.0
 * @deprecated Use wp_get_attachment_image()
 * @see wp_get_attachment_image()
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default to false. Whether to have full size image.
 * @param array $max_dims Optional. Dimensions of image.
 * @return string
 */
function get_attachment_innerHTML($id = 0, $fullsize = false, $max_dims = false) {
	_deprecated_function( __FUNCTION__, '2.5', 'wp_get_attachment_image()' );
	$id = (int) $id;
	if ( !$post = & get_post($id) )
		return false;

	if ( $innerHTML = get_attachment_icon($post->ID, $fullsize, $max_dims))
		return $innerHTML;


	$innerHTML = esc_attr($post->post_title);

	return apply_filters('attachment_innerHTML', $innerHTML, $post->ID);
}

/*
 * Retrieve bookmark data based on ID.
 *
 * @since 2.0.0
 * @deprecated 2.1.0
 * @deprecated Use get_bookmark()
 * @see get_bookmark()
 *
 * @param int $bookmark_id ID of link
 * @param string $output OBJECT, ARRAY_N, or ARRAY_A
 * @return object|array
 */
function get_link($bookmark_id, $output = OBJECT, $filter = 'raw') {
	_deprecated_function( __FUNCTION__, '2.1', 'get_bookmark()' );
	return get_bookmark($bookmark_id, $output, $filter);
}

?>
