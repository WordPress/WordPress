<?php


function the_permalink() {
	echo apply_filters('the_permalink', get_permalink());
}


/**
 * Conditionally adds a trailing slash if the permalink structure
 * has a trailing slash, strips the trailing slash if not
 * @global object Uses $wp_rewrite
 * @param $string string a URL with or without a trailing slash
 * @param $type_of_url string the type of URL being considered (e.g. single, category, etc) for use in the filter
 * @return string
 */
function user_trailingslashit($string, $type_of_url = '') {
	global $wp_rewrite;
	if ( $wp_rewrite->use_trailing_slashes )
		$string = trailingslashit($string);
	else
		$string = untrailingslashit($string);

	// Note that $type_of_url can be one of following:
	// single, single_trackback, single_feed, single_paged, feed, category, page, year, month, day, paged
	$string = apply_filters('user_trailingslashit', $string, $type_of_url);
	return $string;
}


function permalink_anchor($mode = 'id') {
	global $post;
	switch ( strtolower($mode) ) {
		case 'title':
			$title = sanitize_title($post->post_title) . '-' . $post->ID;
			echo '<a id="'.$title.'"></a>';
			break;
		case 'id':
		default:
			echo '<a id="post-' . $post->ID . '"></a>';
			break;
	}
}


function get_permalink($id = 0, $leavename=false) {
	$rewritecode = array(
		'%year%',
		'%monthnum%',
		'%day%',
		'%hour%',
		'%minute%',
		'%second%',
		$leavename? '' : '%postname%',
		'%post_id%',
		'%category%',
		'%author%',
		$leavename? '' : '%pagename%',
	);

	$post = &get_post($id);

	if ( empty($post->ID) ) return FALSE;

	if ( $post->post_type == 'page' )
		return get_page_link($post->ID, $leavename);
	elseif ($post->post_type == 'attachment')
		return get_attachment_link($post->ID);

	$permalink = get_option('permalink_structure');

	if ( '' != $permalink && !in_array($post->post_status, array('draft', 'pending')) ) {
		$unixtime = strtotime($post->post_date);

		$category = '';
		if ( strpos($permalink, '%category%') !== false ) {
			$cats = get_the_category($post->ID);
			if ( $cats )
				usort($cats, '_usort_terms_by_ID'); // order by ID
			$category = $cats[0]->slug;
			if ( $parent=$cats[0]->parent )
				$category = get_category_parents($parent, FALSE, '/', TRUE) . $category;

			// show default category in permalinks, without
			// having to assign it explicitly
			if ( empty($category) ) {
				$default_category = get_category( get_option( 'default_category' ) );
				$category = is_wp_error( $default_category ) ? '' : $default_category->slug; 
			}
		}

		$author = '';
		if ( strpos($permalink, '%author%') !== false ) {
			$authordata = get_userdata($post->post_author);
			$author = $authordata->user_nicename;
		}

		$date = explode(" ",date('Y m d H i s', $unixtime));
		$rewritereplace =
		array(
			$date[0],
			$date[1],
			$date[2],
			$date[3],
			$date[4],
			$date[5],
			$post->post_name,
			$post->ID,
			$category,
			$author,
			$post->post_name,
		);
		$permalink = get_option('home') . str_replace($rewritecode, $rewritereplace, $permalink);
		$permalink = user_trailingslashit($permalink, 'single');
		return apply_filters('post_link', $permalink, $post);
	} else { // if they're not using the fancy permalink option
		$permalink = get_option('home') . '/?p=' . $post->ID;
		return apply_filters('post_link', $permalink, $post);
	}
}

// get permalink from post ID
function post_permalink($post_id = 0, $deprecated = '') {
	return get_permalink($post_id);
}

// Respects page_on_front.  Use this one.
function get_page_link($id = false, $leavename = false) {
	global $post;

	$id = (int) $id;
	if ( !$id )
		$id = (int) $post->ID;

	if ( 'page' == get_option('show_on_front') && $id == get_option('page_on_front') )
		$link = get_option('home');
	else
		$link = _get_page_link( $id , $leavename );

	return apply_filters('page_link', $link, $id);
}

// Ignores page_on_front.  Internal use only.
function _get_page_link( $id = false, $leavename = false ) {
	global $post, $wp_rewrite;

	if ( !$id )
		$id = (int) $post->ID;
	else
		$post = &get_post($id);

	$pagestruct = $wp_rewrite->get_page_permastruct();

	if ( '' != $pagestruct && isset($post->post_status) && 'draft' != $post->post_status ) {
		$link = get_page_uri($id);
		$link = ( $leavename ) ? $pagestruct : str_replace('%pagename%', $link, $pagestruct);
		$link = get_option('home') . "/$link";
		$link = user_trailingslashit($link, 'page');
	} else {
		$link = get_option('home') . "/?page_id=$id";
	}

	return apply_filters( '_get_page_link', $link, $id );
}

function get_attachment_link($id = false) {
	global $post, $wp_rewrite;

	$link = false;

	if (! $id) {
		$id = (int) $post->ID;
	}

	$object = get_post($id);
	if ( $wp_rewrite->using_permalinks() && ($object->post_parent > 0) && ($object->post_parent != $id) ) {
		$parent = get_post($object->post_parent);
		if ( 'page' == $parent->post_type )
			$parentlink = _get_page_link( $object->post_parent ); // Ignores page_on_front
		else
			$parentlink = get_permalink( $object->post_parent );
		if ( is_numeric($object->post_name) || false !== strpos(get_option('permalink_structure'), '%category%') )
			$name = 'attachment/' . $object->post_name; // <permalink>/<int>/ is paged so we use the explicit attachment marker
		else
			$name = $object->post_name;
		if (strpos($parentlink, '?') === false)
			$link = trailingslashit($parentlink) . $name . '/';
	}

	if (! $link ) {
		$link = get_bloginfo('url') . "/?attachment_id=$id";
	}

	return apply_filters('attachment_link', $link, $id);
}

function get_year_link($year) {
	global $wp_rewrite;
	if ( !$year )
		$year = gmdate('Y', time()+(get_option('gmt_offset') * 3600));
	$yearlink = $wp_rewrite->get_year_permastruct();
	if ( !empty($yearlink) ) {
		$yearlink = str_replace('%year%', $year, $yearlink);
		return apply_filters('year_link', get_option('home') . user_trailingslashit($yearlink, 'year'), $year);
	} else {
		return apply_filters('year_link', get_option('home') . '/?m=' . $year, $year);
	}
}

function get_month_link($year, $month) {
	global $wp_rewrite;
	if ( !$year )
		$year = gmdate('Y', time()+(get_option('gmt_offset') * 3600));
	if ( !$month )
		$month = gmdate('m', time()+(get_option('gmt_offset') * 3600));
	$monthlink = $wp_rewrite->get_month_permastruct();
	if ( !empty($monthlink) ) {
		$monthlink = str_replace('%year%', $year, $monthlink);
		$monthlink = str_replace('%monthnum%', zeroise(intval($month), 2), $monthlink);
		return apply_filters('month_link', get_option('home') . user_trailingslashit($monthlink, 'month'), $year, $month);
	} else {
		return apply_filters('month_link', get_option('home') . '/?m=' . $year . zeroise($month, 2), $year, $month);
	}
}

function get_day_link($year, $month, $day) {
	global $wp_rewrite;
	if ( !$year )
		$year = gmdate('Y', time()+(get_option('gmt_offset') * 3600));
	if ( !$month )
		$month = gmdate('m', time()+(get_option('gmt_offset') * 3600));
	if ( !$day )
		$day = gmdate('j', time()+(get_option('gmt_offset') * 3600));

	$daylink = $wp_rewrite->get_day_permastruct();
	if ( !empty($daylink) ) {
		$daylink = str_replace('%year%', $year, $daylink);
		$daylink = str_replace('%monthnum%', zeroise(intval($month), 2), $daylink);
		$daylink = str_replace('%day%', zeroise(intval($day), 2), $daylink);
		return apply_filters('day_link', get_option('home') . user_trailingslashit($daylink, 'day'), $year, $month, $day);
	} else {
		return apply_filters('day_link', get_option('home') . '/?m=' . $year . zeroise($month, 2) . zeroise($day, 2), $year, $month, $day);
	}
}

function get_feed_link($feed = '') {
	global $wp_rewrite;

	$permalink = $wp_rewrite->get_feed_permastruct();
	if ( '' != $permalink ) {
		if ( false !== strpos($feed, 'comments_') ) {
			$feed = str_replace('comments_', '', $feed);
			$permalink = $wp_rewrite->get_comment_feed_permastruct();
		}

		if ( get_default_feed() == $feed )
			$feed = '';

		$permalink = str_replace('%feed%', $feed, $permalink);
		$permalink = preg_replace('#/+#', '/', "/$permalink");
		$output =  get_option('home') . user_trailingslashit($permalink, 'feed');
	} else {
		if ( empty($feed) )
			$feed = get_default_feed();

		if ( false !== strpos($feed, 'comments_') )
			$feed = str_replace('comments_', 'comments-', $feed);

		$output = get_option('home') . "/?feed={$feed}";
	}

	return apply_filters('feed_link', $output, $feed);
}

function get_post_comments_feed_link($post_id = '', $feed = '') {
	global $id;

	if ( empty($post_id) )
		$post_id = (int) $id;

	if ( empty($feed) )
		$feed = get_default_feed();

	if ( '' != get_option('permalink_structure') ) {
		$url = trailingslashit( get_permalink($post_id) ) . 'feed';
		if ( $feed != get_default_feed() )
			$url .= "/$feed";
		$url = user_trailingslashit($url, 'single_feed');
	} else {
		$type = get_post_field('post_type', $post_id);
		if ( 'page' == $type )
			$url = get_option('home') . "/?feed=$feed&amp;page_id=$post_id";
		else
			$url = get_option('home') . "/?feed=$feed&amp;p=$post_id";
	}

	return apply_filters('post_comments_feed_link', $url);
}

/** post_comments_feed_link() - Output the comment feed link for a post.
 *
 * Prints out the comment feed link for a post.  Link text is placed in the
 * anchor.  If no link text is specified, default text is used.  If no post ID
 * is specified, the current post is used.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5
 *
 * @param string Descriptive text
 * @param int Optional post ID.  Default to current post.
 * @return string Link to the comment feed for the current post
*/
function post_comments_feed_link( $link_text = '', $post_id = '', $feed = '' ) {
	$url = get_post_comments_feed_link($post_id, $feed);
	if ( empty($link_text) )
		$link_text = __('Comments Feed');

	echo "<a href='$url'>$link_text</a>";
}

function get_author_feed_link( $author_id, $feed = '' ) {
	$author_id = (int) $author_id;
	$permalink_structure = get_option('permalink_structure');

	if ( empty($feed) )
		$feed = get_default_feed();

	if ( '' == $permalink_structure ) {
		$link = get_option('home') . '?feed=rss2&amp;author=' . $author_id;
	} else {
		$link = get_author_posts_url($author_id);
		$link = trailingslashit($link) . user_trailingslashit('feed', 'feed');
	}

	$link = apply_filters('author_feed_link', $link);

	return $link;
}

/** get_category_feed_link() - Get the feed link for a given category
 *
 * Returns a link to the feed for all post in a given category.  A specific feed can be requested
 * or left blank to get the default feed.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5
 *
 * @param int $cat_id ID of a category
 * @param string $feed Feed type
 * @return string Link to the feed for the category specified by $cat_id
*/
function get_category_feed_link($cat_id, $feed = '') {
	$cat_id = (int) $cat_id;

	$category = get_category($cat_id);

	if ( empty($category) || is_wp_error($category) )
		return false;

	if ( empty($feed) )
		$feed = get_default_feed();

	$permalink_structure = get_option('permalink_structure');

	if ( '' == $permalink_structure ) {
		$link = get_option('home') . "?feed=$feed&amp;cat=" . $cat_id;
	} else {
		$link = get_category_link($cat_id);
		if( $feed == get_default_feed() )
			$feed_link = 'feed';
		else
			$feed_link = "feed/$feed";

		$link = trailingslashit($link) . user_trailingslashit($feed_link, 'feed');
	}

	$link = apply_filters('category_feed_link', $link, $feed);

	return $link;
}

function get_tag_feed_link($tag_id, $feed = '') {
	$tag_id = (int) $tag_id;

	$tag = get_tag($tag_id);

	if ( empty($tag) || is_wp_error($tag) )
		return false;

	$permalink_structure = get_option('permalink_structure');

	if ( empty($feed) )
		$feed = get_default_feed();

	if ( '' == $permalink_structure ) {
		$link = get_option('home') . "?feed=$feed&amp;tag=" . $tag->slug;
	} else {
		$link = get_tag_link($tag->term_id);
		if ( $feed == get_default_feed() )
			$feed_link = 'feed';
		else
			$feed_link = "feed/$feed";
		$link = $link . user_trailingslashit($feed_link, 'feed');
	}

	$link = apply_filters('tag_feed_link', $link, $feed);

	return $link;
}

function get_search_feed_link($search_query = '', $feed = '') {
	if ( empty($search_query) )
		$search = attribute_escape(get_search_query());
	else
		$search = attribute_escape(stripslashes($search_query));

	if ( empty($feed) )
		$feed = get_default_feed();

	$link = get_option('home') . "?s=$search&amp;feed=$feed";

	$link = apply_filters('search_feed_link', $link);

	return $link;
}

function get_search_comments_feed_link($search_query = '', $feed = '') {
	if ( empty($search_query) )
		$search = attribute_escape(get_search_query());
	else
		$search = attribute_escape(stripslashes($search_query));

	if ( empty($feed) )
		$feed = get_default_feed();

	$link = get_option('home') . "?s=$search&amp;feed=comments-$feed";

	$link = apply_filters('search_feed_link', $link);

	return $link;
}

function get_edit_post_link( $id = 0 ) {
	if ( !$post = &get_post( $id ) )
		return;

	switch ( $post->post_type ) :
	case 'page' :
		if ( !current_user_can( 'edit_page', $post->ID ) )
			return;
		$file = 'page';
		$var  = 'post';
		break;
	case 'attachment' :
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return;
		$file = 'media';
		$var  = 'attachment_id';
		break;
	default :
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return;
		$file = 'post';
		$var  = 'post';
		break;
	endswitch;
	
	return apply_filters( 'get_edit_post_link', get_bloginfo( 'wpurl' ) . "/wp-admin/$file.php?action=edit&amp;$var=$post->ID", $post->ID );
}

function edit_post_link( $link = 'Edit This', $before = '', $after = '' ) {
	global $post;

	if ( $post->post_type == 'page' ) {
		if ( !current_user_can( 'edit_page', $post->ID ) )
			return;
	} else {
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return;
	}

	$link = '<a href="' . get_edit_post_link( $post->ID ) . '" title="' . __( 'Edit post' ) . '">' . $link . '</a>';
	echo $before . apply_filters( 'edit_post_link', $link, $post->ID ) . $after;
}

function get_edit_comment_link( $comment_id = 0 ) {
	$comment = &get_comment( $comment_id );
	$post = &get_post( $comment->comment_post_ID );

	if ( $post->post_type == 'page' ) {
		if ( !current_user_can( 'edit_page', $post->ID ) )
			return;
	} else {
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return;
	}

	$location = get_bloginfo( 'wpurl' ) . '/wp-admin/comment.php?action=editcomment&amp;c=' . $comment->comment_ID;
	return apply_filters( 'get_edit_comment_link', $location );
}

function edit_comment_link( $link = 'Edit This', $before = '', $after = '' ) {
	global $comment, $post;

	if ( $post->post_type == 'attachment' ) {
	} elseif ( $post->post_type == 'page' ) {
		if ( !current_user_can( 'edit_page', $post->ID ) )
			return;
	} else {
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return;
	}

	$link = '<a href="' . get_edit_comment_link( $comment->comment_ID ) . '" title="' . __( 'Edit comment' ) . '">' . $link . '</a>';
	echo $before . apply_filters( 'edit_comment_link', $link, $comment->comment_ID ) . $after;
}

// Navigation links

function get_previous_post($in_same_cat = false, $excluded_categories = '') {
	return get_adjacent_post($in_same_cat, $excluded_categories);
}

function get_next_post($in_same_cat = false, $excluded_categories = '') {
	return get_adjacent_post($in_same_cat, $excluded_categories, false);
}

function get_adjacent_post($in_same_cat = false, $excluded_categories = '', $previous = true) {
	global $post, $wpdb;

	if( empty($post) || !is_single() || is_attachment() )
		return null;

	$current_post_date = $post->post_date;

	$join = '';
	$posts_in_ex_cats_sql = '';
	if ( $in_same_cat || !empty($excluded_categories) ) {
		$join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";

		if ( $in_same_cat ) {
			$cat_array = wp_get_object_terms($post->ID, 'category', 'fields=ids');
			$join .= " AND tt.taxonomy = 'category' AND tt.term_id IN (" . implode($cat_array, ',') . ')';
		}

		$posts_in_ex_cats_sql = "AND tt.taxonomy = 'category'";
		if ( !empty($excluded_categories) ) {
			$excluded_categories = array_map('intval', explode(' and ', $excluded_categories));
			if ( !empty($cat_array) ) {
				$excluded_categories = array_diff($excluded_categories, $cat_array);
				$posts_in_ex_cats_sql = '';
			}

			if ( !empty($excluded_categories) ) {
				$posts_in_ex_cats_sql = " AND tt.taxonomy = 'category' AND tt.term_id NOT IN (" . implode($excluded_categories, ',') . ')';
			}
		}
	}

	$adjacent = $previous ? 'previous' : 'next';
	$op = $previous ? '<' : '>';
	$order = $previous ? 'DESC' : 'ASC';

	$join  = apply_filters( "get_{$adjacent}_post_join", $join, $in_same_cat, $excluded_categories );
	$where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = 'post' AND p.post_status = 'publish' $posts_in_ex_cats_sql", $current_post_date), $in_same_cat, $excluded_categories );
	$sort  = apply_filters( "get_{$adjacent}_post_sort", "ORDER BY p.post_date $order LIMIT 1" );

	return $wpdb->get_row("SELECT p.* FROM $wpdb->posts AS p $join $where $sort");
}

function previous_post_link($format='&laquo; %link', $link='%title', $in_same_cat = false, $excluded_categories = '') {
	adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, true);
}

function next_post_link($format='%link &raquo;', $link='%title', $in_same_cat = false, $excluded_categories = '') {
	adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, false);
}

function adjacent_post_link($format, $link, $in_same_cat = false, $excluded_categories = '', $previous = true) {
	if ( $previous && is_attachment() )
		$post = & get_post($GLOBALS['post']->post_parent);
	else
		$post = get_adjacent_post($in_same_cat, $excluded_categories, $previous);

	if ( !$post )
		return;

	$title = $post->post_title;

	if ( empty($post->post_title) )
		$title = $previous ? __('Previous Post') : __('Next Post');

	$title = apply_filters('the_title', $title, $post);
	$string = '<a href="'.get_permalink($post).'">';
	$link = str_replace('%title', $title, $link);
	$link = $string . $link . '</a>';

	$format = str_replace('%link', $link, $format);

	echo $format;
}

function get_pagenum_link($pagenum = 1) {
	global $wp_rewrite;

	$pagenum = (int) $pagenum;

	$request = remove_query_arg( 'paged' );

	$home_root = parse_url(get_option('home'));
	$home_root = ( isset($home_root['path']) ) ? $home_root['path'] : '';
	$home_root = preg_quote( trailingslashit( $home_root ), '|' );

	$request = preg_replace('|^'. $home_root . '|', '', $request);
	$request = preg_replace('|^/+|', '', $request);

	if ( !$wp_rewrite->using_permalinks() || is_admin() ) {
		$base = trailingslashit( get_bloginfo( 'home' ) );

		if ( $pagenum > 1 ) {
			$result = add_query_arg( 'paged', $pagenum, $base . $request );
		} else {
			$result = $base . $request;
		}
	} else {
		$qs_regex = '|\?.*?$|';
		preg_match( $qs_regex, $request, $qs_match );

		if ( !empty( $qs_match[0] ) ) {
			$query_string = $qs_match[0];
			$request = preg_replace( $qs_regex, '', $request );
		} else {
			$query_string = '';
		}

		$request = preg_replace( '|page/\d+/?$|', '', $request);
		$request = preg_replace( '|^index\.php|', '', $request);
		$request = ltrim($request, '/');

		$base = trailingslashit( get_bloginfo( 'url' ) );

		if ( $wp_rewrite->using_index_permalinks() && ( $pagenum > 1 || '' != $request ) )
			$base .= 'index.php/';

		if ( $pagenum > 1 ) {
			$request = ( ( !empty( $request ) ) ? trailingslashit( $request ) : $request ) . user_trailingslashit( 'page/' . $pagenum, 'paged' );
		}

		$result = $base . $request . $query_string;
	}

	$result = apply_filters('get_pagenum_link', $result);

	return $result;
}

function get_next_posts_page_link($max_page = 0) {
	global $paged;

	if ( !is_single() ) {
		if ( !$paged )
			$paged = 1;
		$nextpage = intval($paged) + 1;
		if ( !$max_page || $max_page >= $nextpage )
			return get_pagenum_link($nextpage);
	}
}

function next_posts($max_page = 0) {
	echo clean_url(get_next_posts_page_link($max_page));
}

function next_posts_link($label='Next Page &raquo;', $max_page=0) {
	global $paged, $wp_query;
	if ( !$max_page ) {
		$max_page = $wp_query->max_num_pages;
	}
	if ( !$paged )
		$paged = 1;
	$nextpage = intval($paged) + 1;
	if ( (! is_single()) && (empty($paged) || $nextpage <= $max_page) ) {
		echo '<a href="';
		next_posts($max_page);
		echo '">'. preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label) .'</a>';
	}
}

function get_previous_posts_page_link() {
	global $paged;

	if ( !is_single() ) {
		$nextpage = intval($paged) - 1;
		if ( $nextpage < 1 )
			$nextpage = 1;
		return get_pagenum_link($nextpage);
	}
}

function previous_posts() {
	echo clean_url(get_previous_posts_page_link());
}

function previous_posts_link($label='&laquo; Previous Page') {
	global $paged;
	if ( (!is_single())	&& ($paged > 1) ) {
		echo '<a href="';
		previous_posts();
		echo '">'. preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label) .'</a>';
	}
}

function posts_nav_link($sep=' &#8212; ', $prelabel='&laquo; Previous Page', $nxtlabel='Next Page &raquo;') {
	global $wp_query;
	if ( !is_singular() ) {
		$max_num_pages = $wp_query->max_num_pages;
		$paged = get_query_var('paged');

		//only have sep if there's both prev and next results
		if ($paged < 2 || $paged >= $max_num_pages) {
			$sep = '';
		}

		if ( $max_num_pages > 1 ) {
			previous_posts_link($prelabel);
			echo preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $sep);
			next_posts_link($nxtlabel);
		}
	}
}

?>
