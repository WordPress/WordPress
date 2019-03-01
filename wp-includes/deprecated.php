<?php
/**
 * Deprecated functions from past WordPress versions. You shouldn't use these
 * functions and look for the alternatives instead. The functions will be
 * removed in a later version.
 *
 * @package WordPress
 * @subpackage Deprecated
 */

/*
 * Deprecated functions come here to die.
 */

/**
 * Retrieves all post data for a given post.
 *
 * @since 0.71
 * @deprecated 1.5.1 Use get_post()
 * @see get_post()
 *
 * @param int $postid Post ID.
 * @return array Post data.
 */
function get_postdata($postid) {
	_deprecated_function( __FUNCTION__, '1.5.1', 'get_post()' );

	$post = get_post($postid);

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
 * Use The Loop instead.
 *
 * @link https://codex.wordpress.org/The_Loop
 *
 * @since 1.0.1
 * @deprecated 1.5.0
 */
function start_wp() {
	global $wp_query;

	_deprecated_function( __FUNCTION__, '1.5.0', __('new WordPress Loop') );

	// Since the old style loop is being used, advance the query iterator here.
	$wp_query->next_post();

	setup_postdata( get_post() );
}

/**
 * Returns or prints a category ID.
 *
 * @since 0.71
 * @deprecated 0.71 Use get_the_category()
 * @see get_the_category()
 *
 * @param bool $echo Optional. Whether to echo the output. Default true.
 * @return int Category ID.
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
 * Prints a category with optional text before and after.
 *
 * @since 0.71
 * @deprecated 0.71 Use get_the_category_by_ID()
 * @see get_the_category_by_ID()
 *
 * @param string $before Optional. Text to display before the category. Default empty.
 * @param string $after  Optional. Text to display after the category. Default empty.
 */
function the_category_head( $before = '', $after = '' ) {
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
 * Prints a link to the previous post.
 *
 * @since 1.5.0
 * @deprecated 2.0.0 Use previous_post_link()
 * @see previous_post_link()
 *
 * @param string $format
 * @param string $previous
 * @param string $title
 * @param string $in_same_cat
 * @param int    $limitprev
 * @param string $excluded_categories
 */
function previous_post($format='%', $previous='previous post: ', $title='yes', $in_same_cat='no', $limitprev=1, $excluded_categories='') {

	_deprecated_function( __FUNCTION__, '2.0.0', 'previous_post_link()' );

	if ( empty($in_same_cat) || 'no' == $in_same_cat )
		$in_same_cat = false;
	else
		$in_same_cat = true;

	$post = get_previous_post($in_same_cat, $excluded_categories);

	if ( !$post )
		return;

	$string = '<a href="'.get_permalink($post->ID).'">'.$previous;
	if ( 'yes' == $title )
		$string .= apply_filters('the_title', $post->post_title, $post->ID);
	$string .= '</a>';
	$format = str_replace('%', $string, $format);
	echo $format;
}

/**
 * Prints link to the next post.
 *
 * @since 0.71
 * @deprecated 2.0.0 Use next_post_link()
 * @see next_post_link()
 *
 * @param string $format
 * @param string $next
 * @param string $title
 * @param string $in_same_cat
 * @param int $limitnext
 * @param string $excluded_categories
 */
function next_post($format='%', $next='next post: ', $title='yes', $in_same_cat='no', $limitnext=1, $excluded_categories='') {
	_deprecated_function( __FUNCTION__, '2.0.0', 'next_post_link()' );

	if ( empty($in_same_cat) || 'no' == $in_same_cat )
		$in_same_cat = false;
	else
		$in_same_cat = true;

	$post = get_next_post($in_same_cat, $excluded_categories);

	if ( !$post	)
		return;

	$string = '<a href="'.get_permalink($post->ID).'">'.$next;
	if ( 'yes' == $title )
		$string .= apply_filters('the_title', $post->post_title, $post->ID);
	$string .= '</a>';
	$format = str_replace('%', $string, $format);
	echo $format;
}

/**
 * Whether user can create a post.
 *
 * @since 1.5.0
 * @deprecated 2.0.0 Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $blog_id Not Used
 * @param int $category_id Not Used
 * @return bool
 */
function user_can_create_post($user_id, $blog_id = 1, $category_id = 'None') {
	_deprecated_function( __FUNCTION__, '2.0.0', 'current_user_can()' );

	$author_data = get_userdata($user_id);
	return ($author_data->user_level > 1);
}

/**
 * Whether user can create a post.
 *
 * @since 1.5.0
 * @deprecated 2.0.0 Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $blog_id Not Used
 * @param int $category_id Not Used
 * @return bool
 */
function user_can_create_draft($user_id, $blog_id = 1, $category_id = 'None') {
	_deprecated_function( __FUNCTION__, '2.0.0', 'current_user_can()' );

	$author_data = get_userdata($user_id);
	return ($author_data->user_level >= 1);
}

/**
 * Whether user can edit a post.
 *
 * @since 1.5.0
 * @deprecated 2.0.0 Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $post_id
 * @param int $blog_id Not Used
 * @return bool
 */
function user_can_edit_post($user_id, $post_id, $blog_id = 1) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'current_user_can()' );

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
 * @since 1.5.0
 * @deprecated 2.0.0 Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $post_id
 * @param int $blog_id Not Used
 * @return bool
 */
function user_can_delete_post($user_id, $post_id, $blog_id = 1) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'current_user_can()' );

	// right now if one can edit, one can delete
	return user_can_edit_post($user_id, $post_id, $blog_id);
}

/**
 * Whether user can set new posts' dates.
 *
 * @since 1.5.0
 * @deprecated 2.0.0 Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $blog_id Not Used
 * @param int $category_id Not Used
 * @return bool
 */
function user_can_set_post_date($user_id, $blog_id = 1, $category_id = 'None') {
	_deprecated_function( __FUNCTION__, '2.0.0', 'current_user_can()' );

	$author_data = get_userdata($user_id);
	return (($author_data->user_level > 4) && user_can_create_post($user_id, $blog_id, $category_id));
}

/**
 * Whether user can delete a post.
 *
 * @since 1.5.0
 * @deprecated 2.0.0 Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $post_id
 * @param int $blog_id Not Used
 * @return bool returns true if $user_id can edit $post_id's date
 */
function user_can_edit_post_date($user_id, $post_id, $blog_id = 1) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'current_user_can()' );

	$author_data = get_userdata($user_id);
	return (($author_data->user_level > 4) && user_can_edit_post($user_id, $post_id, $blog_id));
}

/**
 * Whether user can delete a post.
 *
 * @since 1.5.0
 * @deprecated 2.0.0 Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $post_id
 * @param int $blog_id Not Used
 * @return bool returns true if $user_id can edit $post_id's comments
 */
function user_can_edit_post_comments($user_id, $post_id, $blog_id = 1) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'current_user_can()' );

	// right now if one can edit a post, one can edit comments made on it
	return user_can_edit_post($user_id, $post_id, $blog_id);
}

/**
 * Whether user can delete a post.
 *
 * @since 1.5.0
 * @deprecated 2.0.0 Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $post_id
 * @param int $blog_id Not Used
 * @return bool returns true if $user_id can delete $post_id's comments
 */
function user_can_delete_post_comments($user_id, $post_id, $blog_id = 1) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'current_user_can()' );

	// right now if one can edit comments, one can delete comments
	return user_can_edit_post_comments($user_id, $post_id, $blog_id);
}

/**
 * Can user can edit other user.
 *
 * @since 1.5.0
 * @deprecated 2.0.0 Use current_user_can()
 * @see current_user_can()
 *
 * @param int $user_id
 * @param int $other_user
 * @return bool
 */
function user_can_edit_user($user_id, $other_user) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'current_user_can()' );

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
 * @deprecated 2.1.0 Use get_bookmarks()
 * @see get_bookmarks()
 *
 * @param string $cat_name Optional. The category name to use. If no match is found uses all.
 * @param string $before Optional. The html to output before the link.
 * @param string $after Optional. The html to output after the link.
 * @param string $between Optional. The html to output between the link/image and its description. Not used if no image or $show_images is true.
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
	_deprecated_function( __FUNCTION__, '2.1.0', 'get_bookmarks()' );

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
 * @deprecated 2.1.0 Use wp_list_bookmarks()
 * @see wp_list_bookmarks()
 *
 * @param string $category The category to use.
 * @param string $args
 * @return string|null
 */
function wp_get_linksbyname($category, $args = '') {
	_deprecated_function(__FUNCTION__, '2.1.0', 'wp_list_bookmarks()');

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
 *     $links = get_linkobjectsbyname( 'fred' );
 *     foreach ( $links as $link ) {
 *      	echo '<li>' . $link->link_name . '</li>';
 *     }
 *
 * @since 1.0.1
 * @deprecated 2.1.0 Use get_bookmarks()
 * @see get_bookmarks()
 *
 * @param string $cat_name The category name to use. If no match is found uses all.
 * @param string $orderby The order to output the links. E.g. 'id', 'name', 'url', 'description', or 'rating'.
 *		Or maybe owner. If you start the name with an underscore the order will be reversed. You can also
 *		specify 'rand' as the order which will return links in a random order.
 * @param int $limit Limit to X entries. If not specified, all entries are shown.
 * @return array
 */
function get_linkobjectsbyname($cat_name = "noname" , $orderby = 'name', $limit = -1) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'get_bookmarks()' );

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
 *
 *     $links = get_linkobjects(1);
 *     if ($links) {
 *     	foreach ($links as $link) {
 *     		echo '<li>'.$link->link_name.'<br />'.$link->link_description.'</li>';
 *     	}
 *     }
 *
 * Fields are:
 *
 * - link_id
 * - link_url
 * - link_name
 * - link_image
 * - link_target
 * - link_category
 * - link_description
 * - link_visible
 * - link_owner
 * - link_rating
 * - link_updated
 * - link_rel
 * - link_notes
 *
 * @since 1.0.1
 * @deprecated 2.1.0 Use get_bookmarks()
 * @see get_bookmarks()
 *
 * @param int $category The category to use. If no category supplied uses all
 * @param string $orderby the order to output the links. E.g. 'id', 'name', 'url',
 *		'description', or 'rating'. Or maybe owner. If you start the name with an
 *		underscore the order will be reversed. You can also specify 'rand' as the
 *		order which will return links in a random order.
 * @param int $limit Limit to X entries. If not specified, all entries are shown.
 * @return array
 */
function get_linkobjects($category = 0, $orderby = 'name', $limit = 0) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'get_bookmarks()' );

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
 * @deprecated 2.1.0 Use get_bookmarks()
 * @see get_bookmarks()
 *
 * @param string $cat_name The category name to use. If no match is found uses all
 * @param string $before The html to output before the link
 * @param string $after The html to output after the link
 * @param string $between The html to output between the link/image and its description. Not used if no image or show_images is true
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
	_deprecated_function( __FUNCTION__, '2.1.0', 'get_bookmarks()' );

	get_linksbyname($cat_name, $before, $after, $between, $show_images, $orderby, $show_description, true, $limit, $show_updated);
}

/**
 * Gets the links associated with category n and display rating stars/chars.
 *
 * @since 0.71
 * @deprecated 2.1.0 Use get_bookmarks()
 * @see get_bookmarks()
 *
 * @param int $category The category to use. If no category supplied uses all
 * @param string $before The html to output before the link
 * @param string $after The html to output after the link
 * @param string $between The html to output between the link/image and its description. Not used if no image or show_images == true
 * @param bool $show_images Whether to show images (if defined).
 * @param string $orderby The order to output the links. E.g. 'id', 'name', 'url',
 *		'description', or 'rating'. Or maybe owner. If you start the name with an
 *		underscore the order will be reversed. You can also specify 'rand' as the
 *		order which will return links in a random order.
 * @param bool $show_description Whether to show the description if show_images=false/not defined.
 * @param int $limit Limit to X entries. If not specified, all entries are shown.
 * @param int $show_updated Whether to show last updated timestamp
 */
function get_links_withrating($category = -1, $before = '', $after = '<br />', $between = " ", $show_images = true,
							  $orderby = 'id', $show_description = true, $limit = -1, $show_updated = 0) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'get_bookmarks()' );

	get_links($category, $before, $after, $between, $show_images, $orderby, $show_description, true, $limit, $show_updated);
}

/**
 * Gets the auto_toggle setting.
 *
 * @since 0.71
 * @deprecated 2.1.0
 *
 * @param int $id The category to get. If no category supplied uses 0
 * @return int Only returns 0.
 */
function get_autotoggle($id = 0) {
	_deprecated_function( __FUNCTION__, '2.1.0' );
	return 0;
}

/**
 * Lists categories.
 *
 * @since 0.71
 * @deprecated 2.1.0 Use wp_list_categories()
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
 * @return false|null
 */
function list_cats($optionall = 1, $all = 'All', $sort_column = 'ID', $sort_order = 'asc', $file = '', $list = true, $optiondates = 0,
				   $optioncount = 0, $hide_empty = 1, $use_desc_for_title = 1, $children=false, $child_of=0, $categories=0,
				   $recurse=0, $feed = '', $feed_image = '', $exclude = '', $hierarchical=false) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'wp_list_categories()' );

	$query = compact('optionall', 'all', 'sort_column', 'sort_order', 'file', 'list', 'optiondates', 'optioncount', 'hide_empty', 'use_desc_for_title', 'children',
		'child_of', 'categories', 'recurse', 'feed', 'feed_image', 'exclude', 'hierarchical');
	return wp_list_cats($query);
}

/**
 * Lists categories.
 *
 * @since 1.2.0
 * @deprecated 2.1.0 Use wp_list_categories()
 * @see wp_list_categories()
 *
 * @param string|array $args
 * @return false|null|string
 */
function wp_list_cats($args = '') {
	_deprecated_function( __FUNCTION__, '2.1.0', 'wp_list_categories()' );

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
 * Deprecated method for generating a drop-down of categories.
 *
 * @since 0.71
 * @deprecated 2.1.0 Use wp_dropdown_categories()
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
 * @return string
 */
function dropdown_cats($optionall = 1, $all = 'All', $orderby = 'ID', $order = 'asc',
		$show_last_update = 0, $show_count = 0, $hide_empty = 1, $optionnone = false,
		$selected = 0, $exclude = 0) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'wp_dropdown_categories()' );

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
 * Lists authors.
 *
 * @since 1.2.0
 * @deprecated 2.1.0 Use wp_list_authors()
 * @see wp_list_authors()
 *
 * @param bool $optioncount
 * @param bool $exclude_admin
 * @param bool $show_fullname
 * @param bool $hide_empty
 * @param string $feed
 * @param string $feed_image
 * @return null|string
 */
function list_authors($optioncount = false, $exclude_admin = true, $show_fullname = false, $hide_empty = true, $feed = '', $feed_image = '') {
	_deprecated_function( __FUNCTION__, '2.1.0', 'wp_list_authors()' );

	$args = compact('optioncount', 'exclude_admin', 'show_fullname', 'hide_empty', 'feed', 'feed_image');
	return wp_list_authors($args);
}

/**
 * Retrieves a list of post categories.
 *
 * @since 1.0.1
 * @deprecated 2.1.0 Use wp_get_post_categories()
 * @see wp_get_post_categories()
 *
 * @param int $blogid Not Used
 * @param int $post_ID
 * @return array
 */
function wp_get_post_cats($blogid = '1', $post_ID = 0) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'wp_get_post_categories()' );
	return wp_get_post_categories($post_ID);
}

/**
 * Sets the categories that the post id belongs to.
 *
 * @since 1.0.1
 * @deprecated 2.1.0
 * @deprecated Use wp_set_post_categories()
 * @see wp_set_post_categories()
 *
 * @param int $blogid Not used
 * @param int $post_ID
 * @param array $post_categories
 * @return bool|mixed
 */
function wp_set_post_cats($blogid = '1', $post_ID = 0, $post_categories = array()) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'wp_set_post_categories()' );
	return wp_set_post_categories($post_ID, $post_categories);
}

/**
 * Retrieves a list of archives.
 *
 * @since 0.71
 * @deprecated 2.1.0 Use wp_get_archives()
 * @see wp_get_archives()
 *
 * @param string $type
 * @param string $limit
 * @param string $format
 * @param string $before
 * @param string $after
 * @param bool $show_post_count
 * @return string|null
 */
function get_archives($type='', $limit='', $format='html', $before = '', $after = '', $show_post_count = false) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'wp_get_archives()' );
	$args = compact('type', 'limit', 'format', 'before', 'after', 'show_post_count');
	return wp_get_archives($args);
}

/**
 * Returns or Prints link to the author's posts.
 *
 * @since 1.2.0
 * @deprecated 2.1.0 Use get_author_posts_url()
 * @see get_author_posts_url()
 *
 * @param bool $echo
 * @param int $author_id
 * @param string $author_nicename Optional.
 * @return string|null
 */
function get_author_link($echo, $author_id, $author_nicename = '') {
	_deprecated_function( __FUNCTION__, '2.1.0', 'get_author_posts_url()' );

	$link = get_author_posts_url($author_id, $author_nicename);

	if ( $echo )
		echo $link;
	return $link;
}

/**
 * Print list of pages based on arguments.
 *
 * @since 0.71
 * @deprecated 2.1.0 Use wp_link_pages()
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
	_deprecated_function( __FUNCTION__, '2.1.0', 'wp_link_pages()' );

	$args = compact('before', 'after', 'next_or_number', 'nextpagelink', 'previouspagelink', 'pagelink', 'more_file');
	return wp_link_pages($args);
}

/**
 * Get value based on option.
 *
 * @since 0.71
 * @deprecated 2.1.0 Use get_option()
 * @see get_option()
 *
 * @param string $option
 * @return string
 */
function get_settings($option) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'get_option()' );

	return get_option($option);
}

/**
 * Print the permalink of the current post in the loop.
 *
 * @since 0.71
 * @deprecated 1.2.0 Use the_permalink()
 * @see the_permalink()
 */
function permalink_link() {
	_deprecated_function( __FUNCTION__, '1.2.0', 'the_permalink()' );
	the_permalink();
}

/**
 * Print the permalink to the RSS feed.
 *
 * @since 0.71
 * @deprecated 2.3.0 Use the_permalink_rss()
 * @see the_permalink_rss()
 *
 * @param string $deprecated
 */
function permalink_single_rss($deprecated = '') {
	_deprecated_function( __FUNCTION__, '2.3.0', 'the_permalink_rss()' );
	the_permalink_rss();
}

/**
 * Gets the links associated with category.
 *
 * @since 1.0.1
 * @deprecated 2.1.0 Use wp_list_bookmarks()
 * @see wp_list_bookmarks()
 *
 * @param string $args a query string
 * @return null|string
 */
function wp_get_links($args = '') {
	_deprecated_function( __FUNCTION__, '2.1.0', 'wp_list_bookmarks()' );

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
 * @deprecated 2.1.0 Use get_bookmarks()
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
	_deprecated_function( __FUNCTION__, '2.1.0', 'get_bookmarks()' );

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
				$title .= ' ('.__('Last updated') . ' ' . date(get_option('links_updated_date_format'), $row->link_updated_f + (get_option('gmt_offset') * HOUR_IN_SECONDS)) . ')';

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
 * @since 1.0.1
 * @deprecated 2.1.0 Use wp_list_bookmarks()
 * @see wp_list_bookmarks()
 *
 * @param string $order Sort link categories by 'name' or 'id'
 */
function get_links_list($order = 'name') {
	_deprecated_function( __FUNCTION__, '2.1.0', 'wp_list_bookmarks()' );

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
 * @since 0.71
 * @deprecated 2.1.0
 *
 * @param string $text the text of the link
 * @param int $width the width of the popup window
 * @param int $height the height of the popup window
 * @param string $file the page to open in the popup window
 * @param bool $count the number of links in the db
 */
function links_popup_script($text = 'Links', $width=400, $height=400, $file='links.all.php', $count = true) {
	_deprecated_function( __FUNCTION__, '2.1.0' );
}

/**
 * Legacy function that retrieved the value of a link's link_rating field.
 *
 * @since 1.0.1
 * @deprecated 2.1.0 Use sanitize_bookmark_field()
 * @see sanitize_bookmark_field()
 *
 * @param object $link Link object.
 * @return mixed Value of the 'link_rating' field, false otherwise.
 */
function get_linkrating( $link ) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'sanitize_bookmark_field()' );
	return sanitize_bookmark_field('link_rating', $link->link_rating, $link->link_id, 'display');
}

/**
 * Gets the name of category by id.
 *
 * @since 0.71
 * @deprecated 2.1.0 Use get_category()
 * @see get_category()
 *
 * @param int $id The category to get. If no category supplied uses 0
 * @return string
 */
function get_linkcatname($id = 0) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'get_category()' );

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
 * @deprecated 2.5.0 Use post_comments_feed_link()
 * @see post_comments_feed_link()
 *
 * @param string $link_text
 */
function comments_rss_link($link_text = 'Comments RSS') {
	_deprecated_function( __FUNCTION__, '2.5.0', 'post_comments_feed_link()' );
	post_comments_feed_link($link_text);
}

/**
 * Print/Return link to category RSS2 feed.
 *
 * @since 1.2.0
 * @deprecated 2.5.0 Use get_category_feed_link()
 * @see get_category_feed_link()
 *
 * @param bool $echo
 * @param int $cat_ID
 * @return string
 */
function get_category_rss_link($echo = false, $cat_ID = 1) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'get_category_feed_link()' );

	$link = get_category_feed_link($cat_ID, 'rss2');

	if ( $echo )
		echo $link;
	return $link;
}

/**
 * Print/Return link to author RSS feed.
 *
 * @since 1.2.0
 * @deprecated 2.5.0 Use get_author_feed_link()
 * @see get_author_feed_link()
 *
 * @param bool $echo
 * @param int $author_id
 * @return string
 */
function get_author_rss_link($echo = false, $author_id = 1) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'get_author_feed_link()' );

	$link = get_author_feed_link($author_id);
	if ( $echo )
		echo $link;
	return $link;
}

/**
 * Return link to the post RSS feed.
 *
 * @since 1.5.0
 * @deprecated 2.2.0 Use get_post_comments_feed_link()
 * @see get_post_comments_feed_link()
 *
 * @return string
 */
function comments_rss() {
	_deprecated_function( __FUNCTION__, '2.2.0', 'get_post_comments_feed_link()' );
	return esc_url( get_post_comments_feed_link() );
}

/**
 * An alias of wp_create_user().
 *
 * @since 2.0.0
 * @deprecated 2.0.0 Use wp_create_user()
 * @see wp_create_user()
 *
 * @param string $username The user's username.
 * @param string $password The user's password.
 * @param string $email    The user's email.
 * @return int The new user's ID.
 */
function create_user($username, $password, $email) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'wp_create_user()' );
	return wp_create_user($username, $password, $email);
}

/**
 * Unused function.
 *
 * @deprecated 2.5.0
 */
function gzip_compression() {
	_deprecated_function( __FUNCTION__, '2.5.0' );
	return false;
}

/**
 * Retrieve an array of comment data about comment $comment_ID.
 *
 * @since 0.71
 * @deprecated 2.7.0 Use get_comment()
 * @see get_comment()
 *
 * @param int $comment_ID The ID of the comment
 * @param int $no_cache Whether to use the cache (cast to bool)
 * @param bool $include_unapproved Whether to include unapproved comments
 * @return array The comment data
 */
function get_commentdata( $comment_ID, $no_cache = 0, $include_unapproved = false ) {
	_deprecated_function( __FUNCTION__, '2.7.0', 'get_comment()' );
	return get_comment($comment_ID, ARRAY_A);
}

/**
 * Retrieve the category name by the category ID.
 *
 * @since 0.71
 * @deprecated 2.8.0 Use get_cat_name()
 * @see get_cat_name()
 *
 * @param int $cat_ID Category ID
 * @return string category name
 */
function get_catname( $cat_ID ) {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_cat_name()' );
	return get_cat_name( $cat_ID );
}

/**
 * Retrieve category children list separated before and after the term IDs.
 *
 * @since 1.2.0
 * @deprecated 2.8.0 Use get_term_children()
 * @see get_term_children()
 *
 * @param int $id Category ID to retrieve children.
 * @param string $before Optional. Prepend before category term ID.
 * @param string $after Optional, default is empty string. Append after category term ID.
 * @param array $visited Optional. Category Term IDs that have already been added.
 * @return string
 */
function get_category_children( $id, $before = '/', $after = '', $visited = array() ) {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_term_children()' );
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
 * Retrieves all category IDs.
 *
 * @since 2.0.0
 * @deprecated 4.0.0 Use get_terms()
 * @see get_terms()
 *
 * @link https://codex.wordpress.org/Function_Reference/get_all_category_ids
 *
 * @return object List of all of the category IDs.
 */
function get_all_category_ids() {
	_deprecated_function( __FUNCTION__, '4.0.0', 'get_terms()' );

	if ( ! $cat_ids = wp_cache_get( 'all_category_ids', 'category' ) ) {
		$cat_ids = get_terms( 'category', array('fields' => 'ids', 'get' => 'all') );
		wp_cache_add( 'all_category_ids', $cat_ids, 'category' );
	}

	return $cat_ids;
}

/**
 * Retrieve the description of the author of the current post.
 *
 * @since 1.5.0
 * @deprecated 2.8.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @return string The author's description.
 */
function get_the_author_description() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_the_author_meta(\'description\')' );
	return get_the_author_meta('description');
}

/**
 * Display the description of the author of the current post.
 *
 * @since 1.0.0
 * @deprecated 2.8.0 Use the_author_meta()
 * @see the_author_meta()
 */
function the_author_description() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'the_author_meta(\'description\')' );
	the_author_meta('description');
}

/**
 * Retrieve the login name of the author of the current post.
 *
 * @since 1.5.0
 * @deprecated 2.8.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @return string The author's login name (username).
 */
function get_the_author_login() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_the_author_meta(\'login\')' );
	return get_the_author_meta('login');
}

/**
 * Display the login name of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8.0 Use the_author_meta()
 * @see the_author_meta()
 */
function the_author_login() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'the_author_meta(\'login\')' );
	the_author_meta('login');
}

/**
 * Retrieve the first name of the author of the current post.
 *
 * @since 1.5.0
 * @deprecated 2.8.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @return string The author's first name.
 */
function get_the_author_firstname() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_the_author_meta(\'first_name\')' );
	return get_the_author_meta('first_name');
}

/**
 * Display the first name of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8.0 Use the_author_meta()
 * @see the_author_meta()
 */
function the_author_firstname() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'the_author_meta(\'first_name\')' );
	the_author_meta('first_name');
}

/**
 * Retrieve the last name of the author of the current post.
 *
 * @since 1.5.0
 * @deprecated 2.8.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @return string The author's last name.
 */
function get_the_author_lastname() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_the_author_meta(\'last_name\')' );
	return get_the_author_meta('last_name');
}

/**
 * Display the last name of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8.0 Use the_author_meta()
 * @see the_author_meta()
 */
function the_author_lastname() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'the_author_meta(\'last_name\')' );
	the_author_meta('last_name');
}

/**
 * Retrieve the nickname of the author of the current post.
 *
 * @since 1.5.0
 * @deprecated 2.8.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @return string The author's nickname.
 */
function get_the_author_nickname() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_the_author_meta(\'nickname\')' );
	return get_the_author_meta('nickname');
}

/**
 * Display the nickname of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8.0 Use the_author_meta()
 * @see the_author_meta()
 */
function the_author_nickname() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'the_author_meta(\'nickname\')' );
	the_author_meta('nickname');
}

/**
 * Retrieve the email of the author of the current post.
 *
 * @since 1.5.0
 * @deprecated 2.8.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @return string The author's username.
 */
function get_the_author_email() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_the_author_meta(\'email\')' );
	return get_the_author_meta('email');
}

/**
 * Display the email of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8.0 Use the_author_meta()
 * @see the_author_meta()
 */
function the_author_email() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'the_author_meta(\'email\')' );
	the_author_meta('email');
}

/**
 * Retrieve the ICQ number of the author of the current post.
 *
 * @since 1.5.0
 * @deprecated 2.8.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @return string The author's ICQ number.
 */
function get_the_author_icq() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_the_author_meta(\'icq\')' );
	return get_the_author_meta('icq');
}

/**
 * Display the ICQ number of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8.0 Use the_author_meta()
 * @see the_author_meta()
 */
function the_author_icq() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'the_author_meta(\'icq\')' );
	the_author_meta('icq');
}

/**
 * Retrieve the Yahoo! IM name of the author of the current post.
 *
 * @since 1.5.0
 * @deprecated 2.8.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @return string The author's Yahoo! IM name.
 */
function get_the_author_yim() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_the_author_meta(\'yim\')' );
	return get_the_author_meta('yim');
}

/**
 * Display the Yahoo! IM name of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8.0 Use the_author_meta()
 * @see the_author_meta()
 */
function the_author_yim() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'the_author_meta(\'yim\')' );
	the_author_meta('yim');
}

/**
 * Retrieve the MSN address of the author of the current post.
 *
 * @since 1.5.0
 * @deprecated 2.8.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @return string The author's MSN address.
 */
function get_the_author_msn() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_the_author_meta(\'msn\')' );
	return get_the_author_meta('msn');
}

/**
 * Display the MSN address of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8.0 Use the_author_meta()
 * @see the_author_meta()
 */
function the_author_msn() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'the_author_meta(\'msn\')' );
	the_author_meta('msn');
}

/**
 * Retrieve the AIM address of the author of the current post.
 *
 * @since 1.5.0
 * @deprecated 2.8.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @return string The author's AIM address.
 */
function get_the_author_aim() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_the_author_meta(\'aim\')' );
	return get_the_author_meta('aim');
}

/**
 * Display the AIM address of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8.0 Use the_author_meta('aim')
 * @see the_author_meta()
 */
function the_author_aim() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'the_author_meta(\'aim\')' );
	the_author_meta('aim');
}

/**
 * Retrieve the specified author's preferred display name.
 *
 * @since 1.0.0
 * @deprecated 2.8.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @param int $auth_id The ID of the author.
 * @return string The author's display name.
 */
function get_author_name( $auth_id = false ) {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_the_author_meta(\'display_name\')' );
	return get_the_author_meta('display_name', $auth_id);
}

/**
 * Retrieve the URL to the home page of the author of the current post.
 *
 * @since 1.5.0
 * @deprecated 2.8.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @return string The URL to the author's page.
 */
function get_the_author_url() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_the_author_meta(\'url\')' );
	return get_the_author_meta('url');
}

/**
 * Display the URL to the home page of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8.0 Use the_author_meta()
 * @see the_author_meta()
 */
function the_author_url() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'the_author_meta(\'url\')' );
	the_author_meta('url');
}

/**
 * Retrieve the ID of the author of the current post.
 *
 * @since 1.5.0
 * @deprecated 2.8.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @return string|int The author's ID.
 */
function get_the_author_ID() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'get_the_author_meta(\'ID\')' );
	return get_the_author_meta('ID');
}

/**
 * Display the ID of the author of the current post.
 *
 * @since 0.71
 * @deprecated 2.8.0 Use the_author_meta()
 * @see the_author_meta()
 */
function the_author_ID() {
	_deprecated_function( __FUNCTION__, '2.8.0', 'the_author_meta(\'ID\')' );
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
 * @since 0.71
 *
 * @deprecated 2.9.0 Use the_content_feed()
 * @see the_content_feed()
 *
 * @param string $more_link_text Optional. Text to display when more content is available but not displayed.
 * @param int $stripteaser Optional. Default is 0.
 * @param string $more_file Optional.
 * @param int $cut Optional. Amount of words to keep for the content.
 * @param int $encode_html Optional. How to encode the content.
 */
function the_content_rss($more_link_text='(more...)', $stripteaser=0, $more_file='', $cut = 0, $encode_html = 0) {
	_deprecated_function( __FUNCTION__, '2.9.0', 'the_content_feed()' );
	$content = get_the_content($more_link_text, $stripteaser);

	/**
	 * Filters the post content in the context of an RSS feed.
	 *
	 * @since 0.71
	 *
	 * @param string $content Content of the current post.
	 */
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
	_deprecated_function( __FUNCTION__, '2.9.0', '' );
	preg_match_all( '/<a(.+?)href=\"(.+?)\"(.*?)>(.+?)<\/a>/', $content, $matches );
	$links_summary = "\n";
	for ( $i = 0, $c = count( $matches[0] ); $i < $c; $i++ ) {
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
 * @deprecated 2.9.0 Use _x()
 * @see _x()
 *
 * @param string $text Text to translate
 * @param string $domain Optional. Domain to retrieve the translated text
 * @return string Translated context string without pipe
 */
function _c( $text, $domain = 'default' ) {
	_deprecated_function( __FUNCTION__, '2.9.0', '_x()' );
	return before_last_bar( translate( $text, $domain ) );
}

/**
 * Translates $text like translate(), but assumes that the text
 * contains a context after its last vertical bar.
 *
 * @since 2.5.0
 * @deprecated 3.0.0 Use _x()
 * @see _x()
 *
 * @param string $text Text to translate
 * @param string $domain Domain to retrieve the translated text
 * @return string Translated text
 */
function translate_with_context( $text, $domain = 'default' ) {
	_deprecated_function( __FUNCTION__, '2.9.0', '_x()' );
	return before_last_bar( translate( $text, $domain ) );
}

/**
 * Legacy version of _n(), which supports contexts.
 *
 * Strips everything from the translation after the last bar.
 *
 * @since 2.7.0
 * @deprecated 3.0.0 Use _nx()
 * @see _nx()
 *
 * @param string $single The text to be used if the number is singular.
 * @param string $plural The text to be used if the number is plural.
 * @param int    $number The number to compare against to use either the singular or plural form.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 * @return string The translated singular or plural form.
 */
function _nc( $single, $plural, $number, $domain = 'default' ) {
	_deprecated_function( __FUNCTION__, '2.9.0', '_nx()' );
	return before_last_bar( _n( $single, $plural, $number, $domain ) );
}

/**
 * Retrieve the plural or single form based on the amount.
 *
 * @since 1.2.0
 * @deprecated 2.8.0 Use _n()
 * @see _n()
 */
function __ngettext() {
	_deprecated_function( __FUNCTION__, '2.8.0', '_n()' );
	$args = func_get_args();
	return call_user_func_array('_n', $args);
}

/**
 * Register plural strings in POT file, but don't translate them.
 *
 * @since 2.5.0
 * @deprecated 2.8.0 Use _n_noop()
 * @see _n_noop()
 */
function __ngettext_noop() {
	_deprecated_function( __FUNCTION__, '2.8.0', '_n_noop()' );
	$args = func_get_args();
	return call_user_func_array('_n_noop', $args);

}

/**
 * Retrieve all autoload options, or all options if no autoloaded ones exist.
 *
 * @since 1.0.0
 * @deprecated 3.0.0 Use wp_load_alloptions())
 * @see wp_load_alloptions()
 *
 * @return array List of all options.
 */
function get_alloptions() {
	_deprecated_function( __FUNCTION__, '3.0.0', 'wp_load_alloptions()' );
	return wp_load_alloptions();
}

/**
 * Retrieve HTML content of attachment image with link.
 *
 * @since 2.0.0
 * @deprecated 2.5.0 Use wp_get_attachment_link()
 * @see wp_get_attachment_link()
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default is false. Whether to use full size image.
 * @param array $max_dims Optional. Max image dimensions.
 * @param bool $permalink Optional, default is false. Whether to include permalink to image.
 * @return string
 */
function get_the_attachment_link($id = 0, $fullsize = false, $max_dims = false, $permalink = false) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'wp_get_attachment_link()' );
	$id = (int) $id;
	$_post = get_post($id);

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
 * @deprecated 2.5.0 Use wp_get_attachment_image_src()
 * @see wp_get_attachment_image_src()
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default to false. Whether to have full image.
 * @return array Icon URL and full path to file, respectively.
 */
function get_attachment_icon_src( $id = 0, $fullsize = false ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'wp_get_attachment_image_src()' );
	$id = (int) $id;
	if ( !$post = get_post($id) )
		return false;

	$file = get_attached_file( $post->ID );

	if ( !$fullsize && $src = wp_get_attachment_thumb_url( $post->ID ) ) {
		// We have a thumbnail desired, specified and existing

		$src_file = wp_basename($src);
	} elseif ( wp_attachment_is_image( $post->ID ) ) {
		// We have an image without a thumbnail

		$src = wp_get_attachment_url( $post->ID );
		$src_file = & $file;
	} elseif ( $src = wp_mime_type_icon( $post->ID ) ) {
		// No thumb, no image. We'll look for a mime-related icon instead.

		$icon_dir = apply_filters( 'icon_dir', get_template_directory() . '/images' );
		$src_file = $icon_dir . '/' . wp_basename($src);
	}

	if ( !isset($src) || !$src )
		return false;

	return array($src, $src_file);
}

/**
 * Retrieve HTML content of icon attachment image element.
 *
 * @since 2.0.0
 * @deprecated 2.5.0 Use wp_get_attachment_image()
 * @see wp_get_attachment_image()
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default to false. Whether to have full size image.
 * @param array $max_dims Optional. Dimensions of image.
 * @return false|string HTML content.
 */
function get_attachment_icon( $id = 0, $fullsize = false, $max_dims = false ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'wp_get_attachment_image()' );
	$id = (int) $id;
	if ( !$post = get_post($id) )
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
 * @deprecated 2.5.0 Use wp_get_attachment_image()
 * @see wp_get_attachment_image()
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default to false. Whether to have full size image.
 * @param array $max_dims Optional. Dimensions of image.
 * @return false|string
 */
function get_attachment_innerHTML($id = 0, $fullsize = false, $max_dims = false) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'wp_get_attachment_image()' );
	$id = (int) $id;
	if ( !$post = get_post($id) )
		return false;

	if ( $innerHTML = get_attachment_icon($post->ID, $fullsize, $max_dims))
		return $innerHTML;

	$innerHTML = esc_attr($post->post_title);

	return apply_filters('attachment_innerHTML', $innerHTML, $post->ID);
}

/**
 * Retrieves bookmark data based on ID.
 *
 * @since 2.0.0
 * @deprecated 2.1.0 Use get_bookmark()
 * @see get_bookmark()
 *
 * @param int    $bookmark_id ID of link
 * @param string $output      Optional. Type of output. Accepts OBJECT, ARRAY_N, or ARRAY_A.
 *                            Default OBJECT.
 * @param string $filter      Optional. How to filter the link for output. Accepts 'raw', 'edit',
 *                            'attribute', 'js', 'db', or 'display'. Default 'raw'.
 * @return object|array Bookmark object or array, depending on the type specified by `$output`.
 */
function get_link( $bookmark_id, $output = OBJECT, $filter = 'raw' ) {
	_deprecated_function( __FUNCTION__, '2.1.0', 'get_bookmark()' );
	return get_bookmark($bookmark_id, $output, $filter);
}

/**
 * Performs esc_url() for database or redirect usage.
 *
 * @since 2.3.1
 * @deprecated 2.8.0 Use esc_url_raw()
 * @see esc_url_raw()
 *
 * @param string $url The URL to be cleaned.
 * @param array $protocols An array of acceptable protocols.
 * @return string The cleaned URL.
 */
function sanitize_url( $url, $protocols = null ) {
	_deprecated_function( __FUNCTION__, '2.8.0', 'esc_url_raw()' );
	return esc_url_raw( $url, $protocols );
}

/**
 * Checks and cleans a URL.
 *
 * A number of characters are removed from the URL. If the URL is for displaying
 * (the default behaviour) ampersands are also replaced. The 'clean_url' filter
 * is applied to the returned cleaned URL.
 *
 * @since 1.2.0
 * @deprecated 3.0.0 Use esc_url()
 * @see esc_url()
 *
 * @param string $url The URL to be cleaned.
 * @param array $protocols Optional. An array of acceptable protocols.
 * @param string $context Optional. How the URL will be used. Default is 'display'.
 * @return string The cleaned $url after the {@see 'clean_url'} filter is applied.
 */
function clean_url( $url, $protocols = null, $context = 'display' ) {
	if ( $context == 'db' )
		_deprecated_function( 'clean_url( $context = \'db\' )', '3.0.0', 'esc_url_raw()' );
	else
		_deprecated_function( __FUNCTION__, '3.0.0', 'esc_url()' );
	return esc_url( $url, $protocols, $context );
}

/**
 * Escape single quotes, specialchar double quotes, and fix line endings.
 *
 * The filter {@see 'js_escape'} is also applied by esc_js().
 *
 * @since 2.0.4
 * @deprecated 2.8.0 Use esc_js()
 * @see esc_js()
 *
 * @param string $text The text to be escaped.
 * @return string Escaped text.
 */
function js_escape( $text ) {
	_deprecated_function( __FUNCTION__, '2.8.0', 'esc_js()' );
	return esc_js( $text );
}

/**
 * Legacy escaping for HTML blocks.
 *
 * @deprecated 2.8.0 Use esc_html()
 * @see esc_html()
 *
 * @param string       $string        String to escape.
 * @param string       $quote_style   Unused.
 * @param false|string $charset       Unused.
 * @param false        $double_encode Whether to double encode. Unused.
 * @return string Escaped `$string`.
 */
function wp_specialchars( $string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false ) {
	_deprecated_function( __FUNCTION__, '2.8.0', 'esc_html()' );
	if ( func_num_args() > 1 ) { // Maintain back-compat for people passing additional arguments.
		$args = func_get_args();
		return call_user_func_array( '_wp_specialchars', $args );
	} else {
		return esc_html( $string );
	}
}

/**
 * Escaping for HTML attributes.
 *
 * @since 2.0.6
 * @deprecated 2.8.0 Use esc_attr()
 * @see esc_attr()
 *
 * @param string $text
 * @return string
 */
function attribute_escape( $text ) {
	_deprecated_function( __FUNCTION__, '2.8.0', 'esc_attr()' );
	return esc_attr( $text );
}

/**
 * Register widget for sidebar with backward compatibility.
 *
 * Allows $name to be an array that accepts either three elements to grab the
 * first element and the third for the name or just uses the first element of
 * the array for the name.
 *
 * Passes to wp_register_sidebar_widget() after argument list and backward
 * compatibility is complete.
 *
 * @since 2.2.0
 * @deprecated 2.8.0 Use wp_register_sidebar_widget()
 * @see wp_register_sidebar_widget()
 *
 * @param string|int $name            Widget ID.
 * @param callable   $output_callback Run when widget is called.
 * @param string     $classname       Optional. Classname widget option. Default empty.
 * @param mixed      $params ,...     Widget parameters.
 */
function register_sidebar_widget($name, $output_callback, $classname = '') {
	_deprecated_function( __FUNCTION__, '2.8.0', 'wp_register_sidebar_widget()' );
	// Compat
	if ( is_array($name) ) {
		if ( count($name) == 3 )
			$name = sprintf($name[0], $name[2]);
		else
			$name = $name[0];
	}

	$id = sanitize_title($name);
	$options = array();
	if ( !empty($classname) && is_string($classname) )
		$options['classname'] = $classname;
	$params = array_slice(func_get_args(), 2);
	$args = array($id, $name, $output_callback, $options);
	if ( !empty($params) )
		$args = array_merge($args, $params);

	call_user_func_array('wp_register_sidebar_widget', $args);
}

/**
 * Serves as an alias of wp_unregister_sidebar_widget().
 *
 * @since 2.2.0
 * @deprecated 2.8.0 Use wp_unregister_sidebar_widget()
 * @see wp_unregister_sidebar_widget()
 *
 * @param int|string $id Widget ID.
 */
function unregister_sidebar_widget($id) {
	_deprecated_function( __FUNCTION__, '2.8.0', 'wp_unregister_sidebar_widget()' );
	return wp_unregister_sidebar_widget($id);
}

/**
 * Registers widget control callback for customizing options.
 *
 * Allows $name to be an array that accepts either three elements to grab the
 * first element and the third for the name or just uses the first element of
 * the array for the name.
 *
 * Passes to wp_register_widget_control() after the argument list has
 * been compiled.
 *
 * @since 2.2.0
 * @deprecated 2.8.0 Use wp_register_widget_control()
 * @see wp_register_widget_control()
 *
 * @param int|string $name Sidebar ID.
 * @param callable $control_callback Widget control callback to display and process form.
 * @param int $width Widget width.
 * @param int $height Widget height.
 */
function register_widget_control($name, $control_callback, $width = '', $height = '') {
	_deprecated_function( __FUNCTION__, '2.8.0', 'wp_register_widget_control()' );
	// Compat
	if ( is_array($name) ) {
		if ( count($name) == 3 )
			$name = sprintf($name[0], $name[2]);
		else
			$name = $name[0];
	}

	$id = sanitize_title($name);
	$options = array();
	if ( !empty($width) )
		$options['width'] = $width;
	if ( !empty($height) )
		$options['height'] = $height;
	$params = array_slice(func_get_args(), 4);
	$args = array($id, $name, $control_callback, $options);
	if ( !empty($params) )
		$args = array_merge($args, $params);

	call_user_func_array('wp_register_widget_control', $args);
}

/**
 * Alias of wp_unregister_widget_control().
 *
 * @since 2.2.0
 * @deprecated 2.8.0 Use wp_unregister_widget_control()
 * @see wp_unregister_widget_control()
 *
 * @param int|string $id Widget ID.
 */
function unregister_widget_control($id) {
	_deprecated_function( __FUNCTION__, '2.8.0', 'wp_unregister_widget_control()' );
	return wp_unregister_widget_control($id);
}

/**
 * Remove user meta data.
 *
 * @since 2.0.0
 * @deprecated 3.0.0 Use delete_user_meta()
 * @see delete_user_meta()
 *
 * @param int $user_id User ID.
 * @param string $meta_key Metadata key.
 * @param mixed $meta_value Metadata value.
 * @return bool True deletion completed and false if user_id is not a number.
 */
function delete_usermeta( $user_id, $meta_key, $meta_value = '' ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'delete_user_meta()' );
	global $wpdb;
	if ( !is_numeric( $user_id ) )
		return false;
	$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);

	if ( is_array($meta_value) || is_object($meta_value) )
		$meta_value = serialize($meta_value);
	$meta_value = trim( $meta_value );

	$cur = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s", $user_id, $meta_key) );

	if ( $cur && $cur->umeta_id )
		do_action( 'delete_usermeta', $cur->umeta_id, $user_id, $meta_key, $meta_value );

	if ( ! empty($meta_value) )
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s AND meta_value = %s", $user_id, $meta_key, $meta_value) );
	else
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s", $user_id, $meta_key) );

	clean_user_cache( $user_id );
	wp_cache_delete( $user_id, 'user_meta' );

	if ( $cur && $cur->umeta_id )
		do_action( 'deleted_usermeta', $cur->umeta_id, $user_id, $meta_key, $meta_value );

	return true;
}

/**
 * Retrieve user metadata.
 *
 * If $user_id is not a number, then the function will fail over with a 'false'
 * boolean return value. Other returned values depend on whether there is only
 * one item to be returned, which be that single item type. If there is more
 * than one metadata value, then it will be list of metadata values.
 *
 * @since 2.0.0
 * @deprecated 3.0.0 Use get_user_meta()
 * @see get_user_meta()
 *
 * @param int $user_id User ID
 * @param string $meta_key Optional. Metadata key.
 * @return mixed
 */
function get_usermeta( $user_id, $meta_key = '' ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'get_user_meta()' );
	global $wpdb;
	$user_id = (int) $user_id;

	if ( !$user_id )
		return false;

	if ( !empty($meta_key) ) {
		$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);
		$user = wp_cache_get($user_id, 'users');
		// Check the cached user object
		if ( false !== $user && isset($user->$meta_key) )
			$metas = array($user->$meta_key);
		else
			$metas = $wpdb->get_col( $wpdb->prepare("SELECT meta_value FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s", $user_id, $meta_key) );
	} else {
		$metas = $wpdb->get_col( $wpdb->prepare("SELECT meta_value FROM $wpdb->usermeta WHERE user_id = %d", $user_id) );
	}

	if ( empty($metas) ) {
		if ( empty($meta_key) )
			return array();
		else
			return '';
	}

	$metas = array_map('maybe_unserialize', $metas);

	if ( count($metas) == 1 )
		return $metas[0];
	else
		return $metas;
}

/**
 * Update metadata of user.
 *
 * There is no need to serialize values, they will be serialized if it is
 * needed. The metadata key can only be a string with underscores. All else will
 * be removed.
 *
 * Will remove the metadata, if the meta value is empty.
 *
 * @since 2.0.0
 * @deprecated 3.0.0 Use update_user_meta()
 * @see update_user_meta()
 *
 * @param int $user_id User ID
 * @param string $meta_key Metadata key.
 * @param mixed $meta_value Metadata value.
 * @return bool True on successful update, false on failure.
 */
function update_usermeta( $user_id, $meta_key, $meta_value ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'update_user_meta()' );
	global $wpdb;
	if ( !is_numeric( $user_id ) )
		return false;
	$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);

	/** @todo Might need fix because usermeta data is assumed to be already escaped */
	if ( is_string($meta_value) )
		$meta_value = stripslashes($meta_value);
	$meta_value = maybe_serialize($meta_value);

	if (empty($meta_value)) {
		return delete_usermeta($user_id, $meta_key);
	}

	$cur = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s", $user_id, $meta_key) );

	if ( $cur )
		do_action( 'update_usermeta', $cur->umeta_id, $user_id, $meta_key, $meta_value );

	if ( !$cur )
		$wpdb->insert($wpdb->usermeta, compact('user_id', 'meta_key', 'meta_value') );
	elseif ( $cur->meta_value != $meta_value )
		$wpdb->update($wpdb->usermeta, compact('meta_value'), compact('user_id', 'meta_key') );
	else
		return false;

	clean_user_cache( $user_id );
	wp_cache_delete( $user_id, 'user_meta' );

	if ( !$cur )
		do_action( 'added_usermeta', $wpdb->insert_id, $user_id, $meta_key, $meta_value );
	else
		do_action( 'updated_usermeta', $cur->umeta_id, $user_id, $meta_key, $meta_value );

	return true;
}

/**
 * Get users for the site.
 *
 * For setups that use the multisite feature. Can be used outside of the
 * multisite feature.
 *
 * @since 2.2.0
 * @deprecated 3.1.0 Use get_users()
 * @see get_users()
 *
 * @global wpdb $wpdb    WordPress database abstraction object.
 *
 * @param int $id Site ID.
 * @return array List of users that are part of that site ID
 */
function get_users_of_blog( $id = '' ) {
	_deprecated_function( __FUNCTION__, '3.1.0', 'get_users()' );

	global $wpdb;
	if ( empty( $id ) ) {
		$id = get_current_blog_id();
	}
	$blog_prefix = $wpdb->get_blog_prefix($id);
	$users = $wpdb->get_results( "SELECT user_id, user_id AS ID, user_login, display_name, user_email, meta_value FROM $wpdb->users, $wpdb->usermeta WHERE {$wpdb->users}.ID = {$wpdb->usermeta}.user_id AND meta_key = '{$blog_prefix}capabilities' ORDER BY {$wpdb->usermeta}.user_id" );
	return $users;
}

/**
 * Enable/disable automatic general feed link outputting.
 *
 * @since 2.8.0
 * @deprecated 3.0.0 Use add_theme_support()
 * @see add_theme_support()
 *
 * @param bool $add Optional, default is true. Add or remove links. Defaults to true.
 */
function automatic_feed_links( $add = true ) {
	_deprecated_function( __FUNCTION__, '3.0.0', "add_theme_support( 'automatic-feed-links' )" );

	if ( $add )
		add_theme_support( 'automatic-feed-links' );
	else
		remove_action( 'wp_head', 'feed_links_extra', 3 ); // Just do this yourself in 3.0+
}

/**
 * Retrieve user data based on field.
 *
 * @since 1.5.0
 * @deprecated 3.0.0 Use get_the_author_meta()
 * @see get_the_author_meta()
 *
 * @param string    $field User meta field.
 * @param false|int $user Optional. User ID to retrieve the field for. Default false (current user).
 * @return string The author's field from the current author's DB object.
 */
function get_profile( $field, $user = false ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'get_the_author_meta()' );
	if ( $user ) {
		$user = get_user_by( 'login', $user );
		$user = $user->ID;
	}
	return get_the_author_meta( $field, $user );
}

/**
 * Retrieves the number of posts a user has written.
 *
 * @since 0.71
 * @deprecated 3.0.0 Use count_user_posts()
 * @see count_user_posts()
 *
 * @param int $userid User to count posts for.
 * @return int Number of posts the given user has written.
 */
function get_usernumposts( $userid ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'count_user_posts()' );
	return count_user_posts( $userid );
}

/**
 * Callback used to change %uXXXX to &#YYY; syntax
 *
 * @since 2.8.0
 * @access private
 * @deprecated 3.0.0
 *
 * @param array $matches Single Match
 * @return string An HTML entity
 */
function funky_javascript_callback($matches) {
	return "&#".base_convert($matches[1],16,10).";";
}

/**
 * Fixes JavaScript bugs in browsers.
 *
 * Converts unicode characters to HTML numbered entities.
 *
 * @since 1.5.0
 * @deprecated 3.0.0
 *
 * @global $is_macIE
 * @global $is_winIE
 *
 * @param string $text Text to be made safe.
 * @return string Fixed text.
 */
function funky_javascript_fix($text) {
	_deprecated_function( __FUNCTION__, '3.0.0' );
	// Fixes for browsers' JavaScript bugs.
	global $is_macIE, $is_winIE;

	if ( $is_winIE || $is_macIE )
		$text =  preg_replace_callback("/\%u([0-9A-F]{4,4})/",
					"funky_javascript_callback",
					$text);

	return $text;
}

/**
 * Checks that the taxonomy name exists.
 *
 * @since 2.3.0
 * @deprecated 3.0.0 Use taxonomy_exists()
 * @see taxonomy_exists()
 *
 * @param string $taxonomy Name of taxonomy object
 * @return bool Whether the taxonomy exists.
 */
function is_taxonomy( $taxonomy ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'taxonomy_exists()' );
	return taxonomy_exists( $taxonomy );
}

/**
 * Check if Term exists.
 *
 * @since 2.3.0
 * @deprecated 3.0.0 Use term_exists()
 * @see term_exists()
 *
 * @param int|string $term The term to check
 * @param string $taxonomy The taxonomy name to use
 * @param int $parent ID of parent term under which to confine the exists search.
 * @return mixed Get the term id or Term Object, if exists.
 */
function is_term( $term, $taxonomy = '', $parent = 0 ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'term_exists()' );
	return term_exists( $term, $taxonomy, $parent );
}

/**
 * Determines whether the current admin page is generated by a plugin.
 *
 * Use global $plugin_page and/or get_plugin_page_hookname() hooks.
 * 
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/ 
 * Conditional Tags} article in the Theme Developer Handbook.
 * 
 * @since 1.5.0
 * @deprecated 3.1.0
 *
 * @global $plugin_page
 *
 * @return bool
 */
function is_plugin_page() {
	_deprecated_function( __FUNCTION__, '3.1.0'  );

	global $plugin_page;

	if ( isset($plugin_page) )
		return true;

	return false;
}

/**
 * Update the categories cache.
 *
 * This function does not appear to be used anymore or does not appear to be
 * needed. It might be a legacy function left over from when there was a need
 * for updating the category cache.
 *
 * @since 1.5.0
 * @deprecated 3.1.0
 *
 * @return bool Always return True
 */
function update_category_cache() {
	_deprecated_function( __FUNCTION__, '3.1.0'  );

	return true;
}

/**
 * Check for PHP timezone support
 *
 * @since 2.9.0
 * @deprecated 3.2.0
 *
 * @return bool
 */
function wp_timezone_supported() {
	_deprecated_function( __FUNCTION__, '3.2.0' );

	return true;
}

/**
 * Displays an editor: TinyMCE, HTML, or both.
 *
 * @since 2.1.0
 * @deprecated 3.3.0 Use wp_editor()
 * @see wp_editor()
 *
 * @param string $content       Textarea content.
 * @param string $id            Optional. HTML ID attribute value. Default 'content'.
 * @param string $prev_id       Optional. Unused.
 * @param bool   $media_buttons Optional. Whether to display media buttons. Default true.
 * @param int    $tab_index     Optional. Unused.
 * @param bool   $extended      Optional. Unused.
 */
function the_editor($content, $id = 'content', $prev_id = 'title', $media_buttons = true, $tab_index = 2, $extended = true) {
	_deprecated_function( __FUNCTION__, '3.3.0', 'wp_editor()' );

	wp_editor( $content, $id, array( 'media_buttons' => $media_buttons ) );
}

/**
 * Perform the query to get the $metavalues array(s) needed by _fill_user and _fill_many_users
 *
 * @since 3.0.0
 * @deprecated 3.3.0
 *
 * @param array $ids User ID numbers list.
 * @return array of arrays. The array is indexed by user_id, containing $metavalues object arrays.
 */
function get_user_metavalues($ids) {
	_deprecated_function( __FUNCTION__, '3.3.0' );

	$objects = array();

	$ids = array_map('intval', $ids);
	foreach ( $ids as $id )
		$objects[$id] = array();

	$metas = update_meta_cache('user', $ids);

	foreach ( $metas as $id => $meta ) {
		foreach ( $meta as $key => $metavalues ) {
			foreach ( $metavalues as $value ) {
				$objects[$id][] = (object)array( 'user_id' => $id, 'meta_key' => $key, 'meta_value' => $value);
			}
		}
	}

	return $objects;
}

/**
 * Sanitize every user field.
 *
 * If the context is 'raw', then the user object or array will get minimal santization of the int fields.
 *
 * @since 2.3.0
 * @deprecated 3.3.0
 *
 * @param object|array $user The User Object or Array
 * @param string $context Optional, default is 'display'. How to sanitize user fields.
 * @return object|array The now sanitized User Object or Array (will be the same type as $user)
 */
function sanitize_user_object($user, $context = 'display') {
	_deprecated_function( __FUNCTION__, '3.3.0' );

	if ( is_object($user) ) {
		if ( !isset($user->ID) )
			$user->ID = 0;
		if ( ! ( $user instanceof WP_User ) ) {
			$vars = get_object_vars($user);
			foreach ( array_keys($vars) as $field ) {
				if ( is_string($user->$field) || is_numeric($user->$field) )
					$user->$field = sanitize_user_field($field, $user->$field, $user->ID, $context);
			}
		}
		$user->filter = $context;
	} else {
		if ( !isset($user['ID']) )
			$user['ID'] = 0;
		foreach ( array_keys($user) as $field )
			$user[$field] = sanitize_user_field($field, $user[$field], $user['ID'], $context);
		$user['filter'] = $context;
	}

	return $user;
}

/**
 * Get boundary post relational link.
 *
 * Can either be start or end post relational link.
 *
 * @since 2.8.0
 * @deprecated 3.3.0
 *
 * @param string $title Optional. Link title format.
 * @param bool $in_same_cat Optional. Whether link should be in a same category.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @param bool $start Optional, default is true. Whether to display link to first or last post.
 * @return string
 */
function get_boundary_post_rel_link($title = '%title', $in_same_cat = false, $excluded_categories = '', $start = true) {
	_deprecated_function( __FUNCTION__, '3.3.0' );

	$posts = get_boundary_post($in_same_cat, $excluded_categories, $start);
	// If there is no post stop.
	if ( empty($posts) )
		return;

	// Even though we limited get_posts to return only 1 item it still returns an array of objects.
	$post = $posts[0];

	if ( empty($post->post_title) )
		$post->post_title = $start ? __('First Post') : __('Last Post');

	$date = mysql2date(get_option('date_format'), $post->post_date);

	$title = str_replace('%title', $post->post_title, $title);
	$title = str_replace('%date', $date, $title);
	$title = apply_filters('the_title', $title, $post->ID);

	$link = $start ? "<link rel='start' title='" : "<link rel='end' title='";
	$link .= esc_attr($title);
	$link .= "' href='" . get_permalink($post) . "' />\n";

	$boundary = $start ? 'start' : 'end';
	return apply_filters( "{$boundary}_post_rel_link", $link );
}

/**
 * Display relational link for the first post.
 *
 * @since 2.8.0
 * @deprecated 3.3.0
 *
 * @param string $title Optional. Link title format.
 * @param bool $in_same_cat Optional. Whether link should be in a same category.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 */
function start_post_rel_link($title = '%title', $in_same_cat = false, $excluded_categories = '') {
	_deprecated_function( __FUNCTION__, '3.3.0' );

	echo get_boundary_post_rel_link($title, $in_same_cat, $excluded_categories, true);
}

/**
 * Get site index relational link.
 *
 * @since 2.8.0
 * @deprecated 3.3.0
 *
 * @return string
 */
function get_index_rel_link() {
	_deprecated_function( __FUNCTION__, '3.3.0' );

	$link = "<link rel='index' title='" . esc_attr( get_bloginfo( 'name', 'display' ) ) . "' href='" . esc_url( user_trailingslashit( get_bloginfo( 'url', 'display' ) ) ) . "' />\n";
	return apply_filters( "index_rel_link", $link );
}

/**
 * Display relational link for the site index.
 *
 * @since 2.8.0
 * @deprecated 3.3.0
 */
function index_rel_link() {
	_deprecated_function( __FUNCTION__, '3.3.0' );

	echo get_index_rel_link();
}

/**
 * Get parent post relational link.
 *
 * @since 2.8.0
 * @deprecated 3.3.0
 *
 * @param string $title Optional. Link title format. Default '%title'.
 * @return string
 */
function get_parent_post_rel_link( $title = '%title' ) {
	_deprecated_function( __FUNCTION__, '3.3.0' );

	if ( ! empty( $GLOBALS['post'] ) && ! empty( $GLOBALS['post']->post_parent ) )
		$post = get_post($GLOBALS['post']->post_parent);

	if ( empty($post) )
		return;

	$date = mysql2date(get_option('date_format'), $post->post_date);

	$title = str_replace('%title', $post->post_title, $title);
	$title = str_replace('%date', $date, $title);
	$title = apply_filters('the_title', $title, $post->ID);

	$link = "<link rel='up' title='";
	$link .= esc_attr( $title );
	$link .= "' href='" . get_permalink($post) . "' />\n";

	return apply_filters( "parent_post_rel_link", $link );
}

/**
 * Display relational link for parent item
 *
 * @since 2.8.0
 * @deprecated 3.3.0
 *
 * @param string $title Optional. Link title format. Default '%title'.
 */
function parent_post_rel_link( $title = '%title' ) {
	_deprecated_function( __FUNCTION__, '3.3.0' );

	echo get_parent_post_rel_link($title);
}

/**
 * Add the "Dashboard"/"Visit Site" menu.
 *
 * @since 3.2.0
 * @deprecated 3.3.0
 *
 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance.
 */
function wp_admin_bar_dashboard_view_site_menu( $wp_admin_bar ) {
	_deprecated_function( __FUNCTION__, '3.3.0' );

	$user_id = get_current_user_id();

	if ( 0 != $user_id ) {
		if ( is_admin() )
			$wp_admin_bar->add_menu( array( 'id' => 'view-site', 'title' => __( 'Visit Site' ), 'href' => home_url() ) );
		elseif ( is_multisite() )
			$wp_admin_bar->add_menu( array( 'id' => 'dashboard', 'title' => __( 'Dashboard' ), 'href' => get_dashboard_url( $user_id ) ) );
		else
			$wp_admin_bar->add_menu( array( 'id' => 'dashboard', 'title' => __( 'Dashboard' ), 'href' => admin_url() ) );
	}
}

/**
 * Checks if the current user belong to a given site.
 *
 * @since MU (3.0.0)
 * @deprecated 3.3.0 Use is_user_member_of_blog()
 * @see is_user_member_of_blog()
 *
 * @param int $blog_id Site ID
 * @return bool True if the current users belong to $blog_id, false if not.
 */
function is_blog_user( $blog_id = 0 ) {
	_deprecated_function( __FUNCTION__, '3.3.0', 'is_user_member_of_blog()' );

	return is_user_member_of_blog( get_current_user_id(), $blog_id );
}

/**
 * Open the file handle for debugging.
 *
 * @since 0.71
 * @deprecated 3.4.0 Use error_log()
 * @see error_log()
 *
 * @link https://secure.php.net/manual/en/function.error-log.php
 *
 * @param string $filename File name.
 * @param string $mode     Type of access you required to the stream.
 * @return false Always false.
 */
function debug_fopen( $filename, $mode ) {
	_deprecated_function( __FUNCTION__, '3.4.0', 'error_log()' );
	return false;
}

/**
 * Write contents to the file used for debugging.
 *
 * @since 0.71
 * @deprecated 3.4.0 Use error_log()
 * @see error_log()
 *
 * @link https://secure.php.net/manual/en/function.error-log.php
 *
 * @param mixed  $fp     Unused.
 * @param string $string Message to log.
 */
function debug_fwrite( $fp, $string ) {
	_deprecated_function( __FUNCTION__, '3.4.0', 'error_log()' );
	if ( ! empty( $GLOBALS['debug'] ) )
		error_log( $string );
}

/**
 * Close the debugging file handle.
 *
 * @since 0.71
 * @deprecated 3.4.0 Use error_log()
 * @see error_log()
 *
 * @link https://secure.php.net/manual/en/function.error-log.php
 *
 * @param mixed $fp Unused.
 */
function debug_fclose( $fp ) {
	_deprecated_function( __FUNCTION__, '3.4.0', 'error_log()' );
}

/**
 * Retrieve list of themes with theme data in theme directory.
 *
 * The theme is broken, if it doesn't have a parent theme and is missing either
 * style.css and, or index.php. If the theme has a parent theme then it is
 * broken, if it is missing style.css; index.php is optional.
 *
 * @since 1.5.0
 * @deprecated 3.4.0 Use wp_get_themes()
 * @see wp_get_themes()
 *
 * @return array Theme list with theme data.
 */
function get_themes() {
	_deprecated_function( __FUNCTION__, '3.4.0', 'wp_get_themes()' );

	global $wp_themes;
	if ( isset( $wp_themes ) )
		return $wp_themes;

	$themes = wp_get_themes();
	$wp_themes = array();

	foreach ( $themes as $theme ) {
		$name = $theme->get('Name');
		if ( isset( $wp_themes[ $name ] ) )
			$wp_themes[ $name . '/' . $theme->get_stylesheet() ] = $theme;
		else
			$wp_themes[ $name ] = $theme;
	}

	return $wp_themes;
}

/**
 * Retrieve theme data.
 *
 * @since 1.5.0
 * @deprecated 3.4.0 Use wp_get_theme()
 * @see wp_get_theme()
 *
 * @param string $theme Theme name.
 * @return array|null Null, if theme name does not exist. Theme data, if exists.
 */
function get_theme( $theme ) {
	_deprecated_function( __FUNCTION__, '3.4.0', 'wp_get_theme( $stylesheet )' );

	$themes = get_themes();
	if ( is_array( $themes ) && array_key_exists( $theme, $themes ) )
		return $themes[ $theme ];
	return null;
}

/**
 * Retrieve current theme name.
 *
 * @since 1.5.0
 * @deprecated 3.4.0 Use wp_get_theme()
 * @see wp_get_theme()
 *
 * @return string
 */
function get_current_theme() {
	_deprecated_function( __FUNCTION__, '3.4.0', 'wp_get_theme()' );

	if ( $theme = get_option( 'current_theme' ) )
		return $theme;

	return wp_get_theme()->get('Name');
}

/**
 * Accepts matches array from preg_replace_callback in wpautop() or a string.
 *
 * Ensures that the contents of a `<pre>...</pre>` HTML block are not
 * converted into paragraphs or line-breaks.
 *
 * @since 1.2.0
 * @deprecated 3.4.0
 *
 * @param array|string $matches The array or string
 * @return string The pre block without paragraph/line-break conversion.
 */
function clean_pre($matches) {
	_deprecated_function( __FUNCTION__, '3.4.0' );

	if ( is_array($matches) )
		$text = $matches[1] . $matches[2] . "</pre>";
	else
		$text = $matches;

	$text = str_replace(array('<br />', '<br/>', '<br>'), array('', '', ''), $text);
	$text = str_replace('<p>', "\n", $text);
	$text = str_replace('</p>', '', $text);

	return $text;
}


/**
 * Add callbacks for image header display.
 *
 * @since 2.1.0
 * @deprecated 3.4.0 Use add_theme_support()
 * @see add_theme_support()
 *
 * @param callable $wp_head_callback Call on the {@see 'wp_head'} action.
 * @param callable $admin_head_callback Call on custom header administration screen.
 * @param callable $admin_preview_callback Output a custom header image div on the custom header administration screen. Optional.
 */
function add_custom_image_header( $wp_head_callback, $admin_head_callback, $admin_preview_callback = '' ) {
	_deprecated_function( __FUNCTION__, '3.4.0', 'add_theme_support( \'custom-header\', $args )' );
	$args = array(
		'wp-head-callback'    => $wp_head_callback,
		'admin-head-callback' => $admin_head_callback,
	);
	if ( $admin_preview_callback )
		$args['admin-preview-callback'] = $admin_preview_callback;
	return add_theme_support( 'custom-header', $args );
}

/**
 * Remove image header support.
 *
 * @since 3.1.0
 * @deprecated 3.4.0 Use remove_theme_support()
 * @see remove_theme_support()
 *
 * @return null|bool Whether support was removed.
 */
function remove_custom_image_header() {
	_deprecated_function( __FUNCTION__, '3.4.0', 'remove_theme_support( \'custom-header\' )' );
	return remove_theme_support( 'custom-header' );
}

/**
 * Add callbacks for background image display.
 *
 * @since 3.0.0
 * @deprecated 3.4.0 Use add_theme_support()
 * @see add_theme_support()
 *
 * @param callable $wp_head_callback Call on the {@see 'wp_head'} action.
 * @param callable $admin_head_callback Call on custom background administration screen.
 * @param callable $admin_preview_callback Output a custom background image div on the custom background administration screen. Optional.
 */
function add_custom_background( $wp_head_callback = '', $admin_head_callback = '', $admin_preview_callback = '' ) {
	_deprecated_function( __FUNCTION__, '3.4.0', 'add_theme_support( \'custom-background\', $args )' );
	$args = array();
	if ( $wp_head_callback )
		$args['wp-head-callback'] = $wp_head_callback;
	if ( $admin_head_callback )
		$args['admin-head-callback'] = $admin_head_callback;
	if ( $admin_preview_callback )
		$args['admin-preview-callback'] = $admin_preview_callback;
	return add_theme_support( 'custom-background', $args );
}

/**
 * Remove custom background support.
 *
 * @since 3.1.0
 * @deprecated 3.4.0 Use add_custom_background()
 * @see add_custom_background()
 *
 * @return null|bool Whether support was removed.
 */
function remove_custom_background() {
	_deprecated_function( __FUNCTION__, '3.4.0', 'remove_theme_support( \'custom-background\' )' );
	return remove_theme_support( 'custom-background' );
}

/**
 * Retrieve theme data from parsed theme file.
 *
 * @since 1.5.0
 * @deprecated 3.4.0 Use wp_get_theme()
 * @see wp_get_theme()
 *
 * @param string $theme_file Theme file path.
 * @return array Theme data.
 */
function get_theme_data( $theme_file ) {
	_deprecated_function( __FUNCTION__, '3.4.0', 'wp_get_theme()' );
	$theme = new WP_Theme( wp_basename( dirname( $theme_file ) ), dirname( dirname( $theme_file ) ) );

	$theme_data = array(
		'Name' => $theme->get('Name'),
		'URI' => $theme->display('ThemeURI', true, false),
		'Description' => $theme->display('Description', true, false),
		'Author' => $theme->display('Author', true, false),
		'AuthorURI' => $theme->display('AuthorURI', true, false),
		'Version' => $theme->get('Version'),
		'Template' => $theme->get('Template'),
		'Status' => $theme->get('Status'),
		'Tags' => $theme->get('Tags'),
		'Title' => $theme->get('Name'),
		'AuthorName' => $theme->get('Author'),
	);

	foreach ( apply_filters( 'extra_theme_headers', array() ) as $extra_header ) {
		if ( ! isset( $theme_data[ $extra_header ] ) )
			$theme_data[ $extra_header ] = $theme->get( $extra_header );
	}

	return $theme_data;
}

/**
 * Alias of update_post_cache().
 *
 * @see update_post_cache() Posts and pages are the same, alias is intentional
 *
 * @since 1.5.1
 * @deprecated 3.4.0 Use update_post_cache()
 * @see update_post_cache()
 *
 * @param array $pages list of page objects
 */
function update_page_cache( &$pages ) {
	_deprecated_function( __FUNCTION__, '3.4.0', 'update_post_cache()' );

	update_post_cache( $pages );
}

/**
 * Will clean the page in the cache.
 *
 * Clean (read: delete) page from cache that matches $id. Will also clean cache
 * associated with 'all_page_ids' and 'get_pages'.
 *
 * @since 2.0.0
 * @deprecated 3.4.0 Use clean_post_cache
 * @see clean_post_cache()
 *
 * @param int $id Page ID to clean
 */
function clean_page_cache( $id ) {
	_deprecated_function( __FUNCTION__, '3.4.0', 'clean_post_cache()' );

	clean_post_cache( $id );
}

/**
 * Retrieve nonce action "Are you sure" message.
 *
 * Deprecated in 3.4.1 and 3.5.0. Backported to 3.3.3.
 *
 * @since 2.0.4
 * @deprecated 3.4.1 Use wp_nonce_ays()
 * @see wp_nonce_ays()
 *
 * @param string $action Nonce action.
 * @return string Are you sure message.
 */
function wp_explain_nonce( $action ) {
	_deprecated_function( __FUNCTION__, '3.4.1', 'wp_nonce_ays()' );
	return __( 'Are you sure you want to do this?' );
}

/**
 * Display "sticky" CSS class, if a post is sticky.
 *
 * @since 2.7.0
 * @deprecated 3.5.0 Use post_class()
 * @see post_class()
 *
 * @param int $post_id An optional post ID.
 */
function sticky_class( $post_id = null ) {
	_deprecated_function( __FUNCTION__, '3.5.0', 'post_class()' );
	if ( is_sticky( $post_id ) )
		echo ' sticky';
}

/**
 * Retrieve post ancestors.
 *
 * This is no longer needed as WP_Post lazy-loads the ancestors
 * property with get_post_ancestors().
 *
 * @since 2.3.4
 * @deprecated 3.5.0 Use get_post_ancestors()
 * @see get_post_ancestors()
 *
 * @param WP_Post $post Post object, passed by reference (unused).
 */
function _get_post_ancestors( &$post ) {
	_deprecated_function( __FUNCTION__, '3.5.0' );
}

/**
 * Load an image from a string, if PHP supports it.
 *
 * @since 2.1.0
 * @deprecated 3.5.0 Use wp_get_image_editor()
 * @see wp_get_image_editor()
 *
 * @param string $file Filename of the image to load.
 * @return resource The resulting image resource on success, Error string on failure.
 */
function wp_load_image( $file ) {
	_deprecated_function( __FUNCTION__, '3.5.0', 'wp_get_image_editor()' );

	if ( is_numeric( $file ) )
		$file = get_attached_file( $file );

	if ( ! is_file( $file ) ) {
		/* translators: %s: file name */
		return sprintf( __( 'File &#8220;%s&#8221; doesn&#8217;t exist?' ), $file );
	}

	if ( ! function_exists('imagecreatefromstring') )
		return __('The GD image library is not installed.');

	// Set artificially high because GD uses uncompressed images in memory.
	wp_raise_memory_limit( 'image' );

	$image = imagecreatefromstring( file_get_contents( $file ) );

	if ( ! is_resource( $image ) ) {
		/* translators: %s: file name */
		return sprintf( __( 'File &#8220;%s&#8221; is not an image.' ), $file );
	}

	return $image;
}

/**
 * Scale down an image to fit a particular size and save a new copy of the image.
 *
 * The PNG transparency will be preserved using the function, as well as the
 * image type. If the file going in is PNG, then the resized image is going to
 * be PNG. The only supported image types are PNG, GIF, and JPEG.
 *
 * Some functionality requires API to exist, so some PHP version may lose out
 * support. This is not the fault of WordPress (where functionality is
 * downgraded, not actual defects), but of your PHP version.
 *
 * @since 2.5.0
 * @deprecated 3.5.0 Use wp_get_image_editor()
 * @see wp_get_image_editor()
 *
 * @param string $file Image file path.
 * @param int $max_w Maximum width to resize to.
 * @param int $max_h Maximum height to resize to.
 * @param bool $crop Optional. Whether to crop image or resize.
 * @param string $suffix Optional. File suffix.
 * @param string $dest_path Optional. New image file path.
 * @param int $jpeg_quality Optional, default is 90. Image quality percentage.
 * @return mixed WP_Error on failure. String with new destination path.
 */
function image_resize( $file, $max_w, $max_h, $crop = false, $suffix = null, $dest_path = null, $jpeg_quality = 90 ) {
	_deprecated_function( __FUNCTION__, '3.5.0', 'wp_get_image_editor()' );

	$editor = wp_get_image_editor( $file );
	if ( is_wp_error( $editor ) )
		return $editor;
	$editor->set_quality( $jpeg_quality );

	$resized = $editor->resize( $max_w, $max_h, $crop );
	if ( is_wp_error( $resized ) )
		return $resized;

	$dest_file = $editor->generate_filename( $suffix, $dest_path );
	$saved = $editor->save( $dest_file );

	if ( is_wp_error( $saved ) )
		return $saved;

	return $dest_file;
}

/**
 * Retrieve a single post, based on post ID.
 *
 * Has categories in 'post_category' property or key. Has tags in 'tags_input'
 * property or key.
 *
 * @since 1.0.0
 * @deprecated 3.5.0 Use get_post()
 * @see get_post()
 *
 * @param int $postid Post ID.
 * @param string $mode How to return result, either OBJECT, ARRAY_N, or ARRAY_A.
 * @return WP_Post|null Post object or array holding post contents and information
 */
function wp_get_single_post( $postid = 0, $mode = OBJECT ) {
	_deprecated_function( __FUNCTION__, '3.5.0', 'get_post()' );
	return get_post( $postid, $mode );
}

/**
 * Check that the user login name and password is correct.
 *
 * @since 0.71
 * @deprecated 3.5.0 Use wp_authenticate()
 * @see wp_authenticate()
 *
 * @param string $user_login User name.
 * @param string $user_pass User password.
 * @return bool False if does not authenticate, true if username and password authenticates.
 */
function user_pass_ok($user_login, $user_pass) {
	_deprecated_function( __FUNCTION__, '3.5.0', 'wp_authenticate()' );
	$user = wp_authenticate( $user_login, $user_pass );
	if ( is_wp_error( $user ) )
		return false;

	return true;
}

/**
 * Callback formerly fired on the save_post hook. No longer needed.
 *
 * @since 2.3.0
 * @deprecated 3.5.0
 */
function _save_post_hook() {}

/**
 * Check if the installed version of GD supports particular image type
 *
 * @since 2.9.0
 * @deprecated 3.5.0 Use wp_image_editor_supports()
 * @see wp_image_editor_supports()
 *
 * @param string $mime_type
 * @return bool
 */
function gd_edit_image_support($mime_type) {
	_deprecated_function( __FUNCTION__, '3.5.0', 'wp_image_editor_supports()' );

	if ( function_exists('imagetypes') ) {
		switch( $mime_type ) {
			case 'image/jpeg':
				return (imagetypes() & IMG_JPG) != 0;
			case 'image/png':
				return (imagetypes() & IMG_PNG) != 0;
			case 'image/gif':
				return (imagetypes() & IMG_GIF) != 0;
		}
	} else {
		switch( $mime_type ) {
			case 'image/jpeg':
				return function_exists('imagecreatefromjpeg');
			case 'image/png':
				return function_exists('imagecreatefrompng');
			case 'image/gif':
				return function_exists('imagecreatefromgif');
		}
	}
	return false;
}

/**
 * Converts an integer byte value to a shorthand byte value.
 *
 * @since 2.3.0
 * @deprecated 3.6.0 Use size_format()
 * @see size_format()
 *
 * @param int $bytes An integer byte value.
 * @return string A shorthand byte value.
 */
function wp_convert_bytes_to_hr( $bytes ) {
	_deprecated_function( __FUNCTION__, '3.6.0', 'size_format()' );

	$units = array( 0 => 'B', 1 => 'KB', 2 => 'MB', 3 => 'GB', 4 => 'TB' );
	$log   = log( $bytes, KB_IN_BYTES );
	$power = (int) $log;
	$size  = pow( KB_IN_BYTES, $log - $power );

	if ( ! is_nan( $size ) && array_key_exists( $power, $units ) ) {
		$unit = $units[ $power ];
	} else {
		$size = $bytes;
		$unit = $units[0];
	}

	return $size . $unit;
}

/**
 * Formerly used internally to tidy up the search terms.
 *
 * @since 2.9.0
 * @access private
 * @deprecated 3.7.0
 *
 * @param string $t Search terms to "tidy", e.g. trim.
 * @return string Trimmed search terms.
 */
function _search_terms_tidy( $t ) {
	_deprecated_function( __FUNCTION__, '3.7.0' );
	return trim( $t, "\"'\n\r " );
}

/**
 * Determine if TinyMCE is available.
 *
 * Checks to see if the user has deleted the tinymce files to slim down
 * their WordPress installation.
 *
 * @since 2.1.0
 * @deprecated 3.9.0
 *
 * @return bool Whether TinyMCE exists.
 */
function rich_edit_exists() {
	global $wp_rich_edit_exists;
	_deprecated_function( __FUNCTION__, '3.9.0' );

	if ( ! isset( $wp_rich_edit_exists ) )
		$wp_rich_edit_exists = file_exists( ABSPATH . WPINC . '/js/tinymce/tinymce.js' );

	return $wp_rich_edit_exists;
}

/**
 * Old callback for tag link tooltips.
 *
 * @since 2.7.0
 * @access private
 * @deprecated 3.9.0
 *
 * @param int $count Number of topics.
 * @return int Number of topics.
 */
function default_topic_count_text( $count ) {
	return $count;
}

/**
 * Formerly used to escape strings before inserting into the DB.
 *
 * Has not performed this function for many, many years. Use wpdb::prepare() instead.
 *
 * @since 0.71
 * @deprecated 3.9.0
 *
 * @param string $content The text to format.
 * @return string The very same text.
 */
function format_to_post( $content ) {
	_deprecated_function( __FUNCTION__, '3.9.0' );
	return $content;
}

/**
 * Formerly used to escape strings before searching the DB. It was poorly documented and never worked as described.
 *
 * @since 2.5.0
 * @deprecated 4.0.0 Use wpdb::esc_like()
 * @see wpdb::esc_like()
 *
 * @param string $text The text to be escaped.
 * @return string text, safe for inclusion in LIKE query.
 */
function like_escape($text) {
	_deprecated_function( __FUNCTION__, '4.0.0', 'wpdb::esc_like()' );
	return str_replace( array( "%", "_" ), array( "\\%", "\\_" ), $text );
}

/**
 * Determines if the URL can be accessed over SSL.
 *
 * Determines if the URL can be accessed over SSL by using the WordPress HTTP API to access
 * the URL using https as the scheme.
 *
 * @since 2.5.0
 * @deprecated 4.0.0
 *
 * @param string $url The URL to test.
 * @return bool Whether SSL access is available.
 */
function url_is_accessable_via_ssl( $url ) {
	_deprecated_function( __FUNCTION__, '4.0.0' );

	$response = wp_remote_get( set_url_scheme( $url, 'https' ) );

	if ( !is_wp_error( $response ) ) {
		$status = wp_remote_retrieve_response_code( $response );
		if ( 200 == $status || 401 == $status ) {
			return true;
		}
	}

	return false;
}

/**
 * Start preview theme output buffer.
 *
 * Will only perform task if the user has permissions and template and preview
 * query variables exist.
 *
 * @since 2.6.0
 * @deprecated 4.3.0
 */
function preview_theme() {
	_deprecated_function( __FUNCTION__, '4.3.0' );
}

/**
 * Private function to modify the current template when previewing a theme
 *
 * @since 2.9.0
 * @deprecated 4.3.0
 * @access private
 *
 * @return string
 */
function _preview_theme_template_filter() {
	_deprecated_function( __FUNCTION__, '4.3.0' );
	return '';
}

/**
 * Private function to modify the current stylesheet when previewing a theme
 *
 * @since 2.9.0
 * @deprecated 4.3.0
 * @access private
 *
 * @return string
 */
function _preview_theme_stylesheet_filter() {
	_deprecated_function( __FUNCTION__, '4.3.0' );
	return '';
}

/**
 * Callback function for ob_start() to capture all links in the theme.
 *
 * @since 2.6.0
 * @deprecated 4.3.0
 * @access private
 *
 * @param string $content
 * @return string
 */
function preview_theme_ob_filter( $content ) {
	_deprecated_function( __FUNCTION__, '4.3.0' );
	return $content;
}

/**
 * Manipulates preview theme links in order to control and maintain location.
 *
 * Callback function for preg_replace_callback() to accept and filter matches.
 *
 * @since 2.6.0
 * @deprecated 4.3.0
 * @access private
 *
 * @param array $matches
 * @return string
 */
function preview_theme_ob_filter_callback( $matches ) {
	_deprecated_function( __FUNCTION__, '4.3.0' );
	return '';
}

/**
 * Formats text for the rich text editor.
 *
 * The {@see 'richedit_pre'} filter is applied here. If $text is empty the filter will
 * be applied to an empty string.
 *
 * @since 2.0.0
 * @deprecated 4.3.0 Use format_for_editor()
 * @see format_for_editor()
 *
 * @param string $text The text to be formatted.
 * @return string The formatted text after filter is applied.
 */
function wp_richedit_pre($text) {
	_deprecated_function( __FUNCTION__, '4.3.0', 'format_for_editor()' );

	if ( empty( $text ) ) {
		/**
		 * Filters text returned for the rich text editor.
		 *
		 * This filter is first evaluated, and the value returned, if an empty string
		 * is passed to wp_richedit_pre(). If an empty string is passed, it results
		 * in a break tag and line feed.
		 *
		 * If a non-empty string is passed, the filter is evaluated on the wp_richedit_pre()
		 * return after being formatted.
		 *
		 * @since 2.0.0
		 * @deprecated 4.3.0
		 *
		 * @param string $output Text for the rich text editor.
		 */
		return apply_filters( 'richedit_pre', '' );
	}

	$output = convert_chars($text);
	$output = wpautop($output);
	$output = htmlspecialchars($output, ENT_NOQUOTES, get_option( 'blog_charset' ) );

	/** This filter is documented in wp-includes/deprecated.php */
	return apply_filters( 'richedit_pre', $output );
}

/**
 * Formats text for the HTML editor.
 *
 * Unless $output is empty it will pass through htmlspecialchars before the
 * {@see 'htmledit_pre'} filter is applied.
 *
 * @since 2.5.0
 * @deprecated 4.3.0 Use format_for_editor()
 * @see format_for_editor()
 *
 * @param string $output The text to be formatted.
 * @return string Formatted text after filter applied.
 */
function wp_htmledit_pre($output) {
	_deprecated_function( __FUNCTION__, '4.3.0', 'format_for_editor()' );

	if ( !empty($output) )
		$output = htmlspecialchars($output, ENT_NOQUOTES, get_option( 'blog_charset' ) ); // convert only < > &

	/**
	 * Filters the text before it is formatted for the HTML editor.
	 *
	 * @since 2.5.0
	 * @deprecated 4.3.0
	 *
	 * @param string $output The HTML-formatted text.
	 */
	return apply_filters( 'htmledit_pre', $output );
}

/**
 * Retrieve permalink from post ID.
 *
 * @since 1.0.0
 * @deprecated 4.4.0 Use get_permalink()
 * @see get_permalink()
 *
 * @param int|WP_Post $post_id Optional. Post ID or WP_Post object. Default is global $post.
 * @return string|false
 */
function post_permalink( $post_id = 0 ) {
	_deprecated_function( __FUNCTION__, '4.4.0', 'get_permalink()' );

	return get_permalink( $post_id );
}

/**
 * Perform a HTTP HEAD or GET request.
 *
 * If $file_path is a writable filename, this will do a GET request and write
 * the file to that path.
 *
 * @since 2.5.0
 * @deprecated 4.4.0 Use WP_Http
 * @see WP_Http
 *
 * @param string      $url       URL to fetch.
 * @param string|bool $file_path Optional. File path to write request to. Default false.
 * @param int         $red       Optional. The number of Redirects followed, Upon 5 being hit,
 *                               returns false. Default 1.
 * @return bool|string False on failure and string of headers if HEAD request.
 */
function wp_get_http( $url, $file_path = false, $red = 1 ) {
	_deprecated_function( __FUNCTION__, '4.4.0', 'WP_Http' );

	@set_time_limit( 60 );

	if ( $red > 5 )
		return false;

	$options = array();
	$options['redirection'] = 5;

	if ( false == $file_path )
		$options['method'] = 'HEAD';
	else
		$options['method'] = 'GET';

	$response = wp_safe_remote_request( $url, $options );

	if ( is_wp_error( $response ) )
		return false;

	$headers = wp_remote_retrieve_headers( $response );
	$headers['response'] = wp_remote_retrieve_response_code( $response );

	// WP_HTTP no longer follows redirects for HEAD requests.
	if ( 'HEAD' == $options['method'] && in_array($headers['response'], array(301, 302)) && isset( $headers['location'] ) ) {
		return wp_get_http( $headers['location'], $file_path, ++$red );
	}

	if ( false == $file_path )
		return $headers;

	// GET request - write it to the supplied filename
	$out_fp = fopen($file_path, 'w');
	if ( !$out_fp )
		return $headers;

	fwrite( $out_fp,  wp_remote_retrieve_body( $response ) );
	fclose($out_fp);
	clearstatcache();

	return $headers;
}

/**
 * Whether SSL login should be forced.
 *
 * @since 2.6.0
 * @deprecated 4.4.0 Use force_ssl_admin()
 * @see force_ssl_admin()
 *
 * @param string|bool $force Optional Whether to force SSL login. Default null.
 * @return bool True if forced, false if not forced.
 */
function force_ssl_login( $force = null ) {
	_deprecated_function( __FUNCTION__, '4.4.0', 'force_ssl_admin()' );
	return force_ssl_admin( $force );
}

/**
 * Retrieve path of comment popup template in current or parent template.
 *
 * @since 1.5.0
 * @deprecated 4.5.0
 *
 * @return string Full path to comments popup template file.
 */
function get_comments_popup_template() {
	_deprecated_function( __FUNCTION__, '4.5.0' );

	return '';
}

/**
 * Determines whether the current URL is within the comments popup window.
 * 
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/ 
 * Conditional Tags} article in the Theme Developer Handbook.
 * 
 * @since 1.5.0
 * @deprecated 4.5.0
 *
 * @return bool
 */
function is_comments_popup() {
	_deprecated_function( __FUNCTION__, '4.5.0' );

	return false;
}

/**
 * Display the JS popup script to show a comment.
 *
 * @since 0.71
 * @deprecated 4.5.0
 */
function comments_popup_script() {
	_deprecated_function( __FUNCTION__, '4.5.0' );
}

/**
 * Adds element attributes to open links in new tabs.
 *
 * @since 0.71
 * @deprecated 4.5.0
 *
 * @param string $text Content to replace links to open in a new tab.
 * @return string Content that has filtered links.
 */
function popuplinks( $text ) {
	_deprecated_function( __FUNCTION__, '4.5.0' );
	$text = preg_replace('/<a (.+?)>/i', "<a $1 target='_blank' rel='external'>", $text);
	return $text;
}

/**
 * The Google Video embed handler callback.
 *
 * Deprecated function that previously assisted in turning Google Video URLs
 * into embeds but that service has since been shut down.
 *
 * @since 2.9.0
 * @deprecated 4.6.0
 *
 * @return string An empty string.
 */
function wp_embed_handler_googlevideo( $matches, $attr, $url, $rawattr ) {
	_deprecated_function( __FUNCTION__, '4.6.0' );

	return '';
}

/**
 * Retrieve path of paged template in current or parent template.
 *
 * @since 1.5.0
 * @deprecated 4.7.0 The paged.php template is no longer part of the theme template hierarchy.
 *
 * @return string Full path to paged template file.
 */
function get_paged_template() {
	_deprecated_function( __FUNCTION__, '4.7.0' );

	return get_query_template( 'paged' );
}

/**
 * Removes the HTML JavaScript entities found in early versions of Netscape 4.
 *
 * Previously, this function was pulled in from the original
 * import of kses and removed a specific vulnerability only
 * existent in early version of Netscape 4. However, this
 * vulnerability never affected any other browsers and can
 * be considered safe for the modern web.
 *
 * The regular expression which sanitized this vulnerability
 * has been removed in consideration of the performance and
 * energy demands it placed, now merely passing through its
 * input to the return.
 *
 * @since 1.0.0
 * @deprecated 4.7.0 Officially dropped security support for Netscape 4.
 *
 * @param string $string
 * @return string
 */
function wp_kses_js_entities( $string ) {
	_deprecated_function( __FUNCTION__, '4.7.0' );

	return preg_replace( '%&\s*\{[^}]*(\}\s*;?|$)%', '', $string );
}

/**
 * Sort categories by ID.
 *
 * Used by usort() as a callback, should not be used directly. Can actually be
 * used to sort any term object.
 *
 * @since 2.3.0
 * @deprecated 4.7.0 Use wp_list_sort()
 * @access private
 *
 * @param object $a
 * @param object $b
 * @return int
 */
function _usort_terms_by_ID( $a, $b ) {
	_deprecated_function( __FUNCTION__, '4.7.0', 'wp_list_sort()' );

	if ( $a->term_id > $b->term_id )
		return 1;
	elseif ( $a->term_id < $b->term_id )
		return -1;
	else
		return 0;
}

/**
 * Sort categories by name.
 *
 * Used by usort() as a callback, should not be used directly. Can actually be
 * used to sort any term object.
 *
 * @since 2.3.0
 * @deprecated 4.7.0 Use wp_list_sort()
 * @access private
 *
 * @param object $a
 * @param object $b
 * @return int
 */
function _usort_terms_by_name( $a, $b ) {
	_deprecated_function( __FUNCTION__, '4.7.0', 'wp_list_sort()' );

	return strcmp( $a->name, $b->name );
}

/**
 * Sort menu items by the desired key.
 *
 * @since 3.0.0
 * @deprecated 4.7.0 Use wp_list_sort()
 * @access private
 *
 * @global string $_menu_item_sort_prop
 *
 * @param object $a The first object to compare
 * @param object $b The second object to compare
 * @return int -1, 0, or 1 if $a is considered to be respectively less than, equal to, or greater than $b.
 */
function _sort_nav_menu_items( $a, $b ) {
	global $_menu_item_sort_prop;

	_deprecated_function( __FUNCTION__, '4.7.0', 'wp_list_sort()' );

	if ( empty( $_menu_item_sort_prop ) )
		return 0;

	if ( ! isset( $a->$_menu_item_sort_prop ) || ! isset( $b->$_menu_item_sort_prop ) )
		return 0;

	$_a = (int) $a->$_menu_item_sort_prop;
	$_b = (int) $b->$_menu_item_sort_prop;

	if ( $a->$_menu_item_sort_prop == $b->$_menu_item_sort_prop )
		return 0;
	elseif ( $_a == $a->$_menu_item_sort_prop && $_b == $b->$_menu_item_sort_prop )
		return $_a < $_b ? -1 : 1;
	else
		return strcmp( $a->$_menu_item_sort_prop, $b->$_menu_item_sort_prop );
}

/**
 * Retrieves the Press This bookmarklet link.
 *
 * @since 2.6.0
 * @deprecated 4.9.0
 *
 */
function get_shortcut_link() {
	_deprecated_function( __FUNCTION__, '4.9.0' );

	$link = '';

	/**
	 * Filters the Press This bookmarklet link.
	 *
	 * @since 2.6.0
	 * @deprecated 4.9.0
	 *
	 * @param string $link The Press This bookmarklet link.
	 */
	return apply_filters( 'shortcut_link', $link );
}

/**
* Ajax handler for saving a post from Press This.
*
* @since 4.2.0
* @deprecated 4.9.0
*/
function wp_ajax_press_this_save_post() {
	_deprecated_function( __FUNCTION__, '4.9.0' );
	if ( is_plugin_active( 'press-this/press-this-plugin.php' ) ) {
		include( WP_PLUGIN_DIR . '/press-this/class-wp-press-this-plugin.php' );
		$wp_press_this = new WP_Press_This_Plugin();
		$wp_press_this->save_post();
	} else {
		wp_send_json_error( array( 'errorMessage' => __( 'The Press This plugin is required.' ) ) );
	}
}

/**
* Ajax handler for creating new category from Press This.
*
* @since 4.2.0
* @deprecated 4.9.0
*/
function wp_ajax_press_this_add_category() {
	_deprecated_function( __FUNCTION__, '4.9.0' );
	if ( is_plugin_active( 'press-this/press-this-plugin.php' ) ) {
		include( WP_PLUGIN_DIR . '/press-this/class-wp-press-this-plugin.php' );
		$wp_press_this = new WP_Press_This_Plugin();
		$wp_press_this->add_category();
	} else {
		wp_send_json_error( array( 'errorMessage' => __( 'The Press This plugin is required.' ) ) );
	}
}
