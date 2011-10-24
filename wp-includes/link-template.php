<?php
/**
 * WordPress Link Template Functions
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Display the permalink for the current post.
 *
 * @since 1.2.0
 * @uses apply_filters() Calls 'the_permalink' filter on the permalink string.
 */
function the_permalink() {
	echo apply_filters('the_permalink', get_permalink());
}

/**
 * Retrieve trailing slash string, if blog set for adding trailing slashes.
 *
 * Conditionally adds a trailing slash if the permalink structure has a trailing
 * slash, strips the trailing slash if not. The string is passed through the
 * 'user_trailingslashit' filter. Will remove trailing slash from string, if
 * blog is not set to have them.
 *
 * @since 2.2.0
 * @uses $wp_rewrite
 *
 * @param string $string URL with or without a trailing slash.
 * @param string $type_of_url The type of URL being considered (e.g. single, category, etc) for use in the filter.
 * @return string
 */
function user_trailingslashit($string, $type_of_url = '') {
	global $wp_rewrite;
	if ( $wp_rewrite->use_trailing_slashes )
		$string = trailingslashit($string);
	else
		$string = untrailingslashit($string);

	// Note that $type_of_url can be one of following:
	// single, single_trackback, single_feed, single_paged, feed, category, page, year, month, day, paged, post_type_archive
	$string = apply_filters('user_trailingslashit', $string, $type_of_url);
	return $string;
}

/**
 * Display permalink anchor for current post.
 *
 * The permalink mode title will use the post title for the 'a' element 'id'
 * attribute. The id mode uses 'post-' with the post ID for the 'id' attribute.
 *
 * @since 0.71
 *
 * @param string $mode Permalink mode can be either 'title', 'id', or default, which is 'id'.
 */
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

/**
 * Retrieve full permalink for current post or post ID.
 *
 * @since 1.0.0
 *
 * @param int $id Optional. Post ID.
 * @param bool $leavename Optional, defaults to false. Whether to keep post name or page name.
 * @return string
 */
function get_permalink($id = 0, $leavename = false) {
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

	if ( is_object($id) && isset($id->filter) && 'sample' == $id->filter ) {
		$post = $id;
		$sample = true;
	} else {
		$post = &get_post($id);
		$sample = false;
	}

	if ( empty($post->ID) )
		return false;

	if ( $post->post_type == 'page' )
		return get_page_link($post->ID, $leavename, $sample);
	elseif ( $post->post_type == 'attachment' )
		return get_attachment_link($post->ID);
	elseif ( in_array($post->post_type, get_post_types( array('_builtin' => false) ) ) )
		return get_post_permalink($post->ID, $leavename, $sample);

	$permalink = get_option('permalink_structure');

	$permalink = apply_filters('pre_post_link', $permalink, $post, $leavename);

	if ( '' != $permalink && !in_array($post->post_status, array('draft', 'pending', 'auto-draft')) ) {
		$unixtime = strtotime($post->post_date);

		$category = '';
		if ( strpos($permalink, '%category%') !== false ) {
			$cats = get_the_category($post->ID);
			if ( $cats ) {
				usort($cats, '_usort_terms_by_ID'); // order by ID
				$category = $cats[0]->slug;
				if ( $parent = $cats[0]->parent )
					$category = get_category_parents($parent, false, '/', true) . $category;
			}
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
		$permalink = home_url( str_replace($rewritecode, $rewritereplace, $permalink) );
		$permalink = user_trailingslashit($permalink, 'single');
	} else { // if they're not using the fancy permalink option
		$permalink = home_url('?p=' . $post->ID);
	}
	return apply_filters('post_link', $permalink, $post, $leavename);
}

/**
 * Retrieve the permalink for a post with a custom post type.
 *
 * @since 3.0.0
 *
 * @param int $id Optional. Post ID.
 * @param bool $leavename Optional, defaults to false. Whether to keep post name.
 * @param bool $sample Optional, defaults to false. Is it a sample permalink.
 * @return string
 */
function get_post_permalink( $id = 0, $leavename = false, $sample = false ) {
	global $wp_rewrite;

	$post = &get_post($id);

	if ( is_wp_error( $post ) )
		return $post;

	$post_link = $wp_rewrite->get_extra_permastruct($post->post_type);

	$slug = $post->post_name;

	$draft_or_pending = isset($post->post_status) && in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) );

	$post_type = get_post_type_object($post->post_type);

	if ( !empty($post_link) && ( !$draft_or_pending || $sample ) ) {
		if ( ! $leavename ) {
			if ( $post_type->hierarchical )
				$slug = get_page_uri($id);
			$post_link = str_replace("%$post->post_type%", $slug, $post_link);
		}
		$post_link = home_url( user_trailingslashit($post_link) );
	} else {
		if ( $post_type->query_var && ( isset($post->post_status) && !$draft_or_pending ) )
			$post_link = add_query_arg($post_type->query_var, $slug, '');
		else
			$post_link = add_query_arg(array('post_type' => $post->post_type, 'p' => $post->ID), '');
		$post_link = home_url($post_link);
	}

	return apply_filters('post_type_link', $post_link, $post, $leavename, $sample);
}

/**
 * Retrieve permalink from post ID.
 *
 * @since 1.0.0
 *
 * @param int $post_id Optional. Post ID.
 * @param mixed $deprecated Not used.
 * @return string
 */
function post_permalink( $post_id = 0, $deprecated = '' ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '1.3' );

	return get_permalink($post_id);
}

/**
 * Retrieve the permalink for current page or page ID.
 *
 * Respects page_on_front. Use this one.
 *
 * @since 1.5.0
 *
 * @param int $id Optional. Post ID.
 * @param bool $leavename Optional, defaults to false. Whether to keep page name.
 * @param bool $sample Optional, defaults to false. Is it a sample permalink.
 * @return string
 */
function get_page_link( $id = false, $leavename = false, $sample = false ) {
	global $post;

	$id = (int) $id;
	if ( !$id )
		$id = (int) $post->ID;

	if ( 'page' == get_option('show_on_front') && $id == get_option('page_on_front') )
		$link = home_url('/');
	else
		$link = _get_page_link( $id , $leavename, $sample );

	return apply_filters('page_link', $link, $id, $sample);
}

/**
 * Retrieve the page permalink.
 *
 * Ignores page_on_front. Internal use only.
 *
 * @since 2.1.0
 * @access private
 *
 * @param int $id Optional. Post ID.
 * @param bool $leavename Optional. Leave name.
 * @param bool $sample Optional. Sample permalink.
 * @return string
 */
function _get_page_link( $id = false, $leavename = false, $sample = false ) {
	global $post, $wp_rewrite;

	if ( !$id )
		$id = (int) $post->ID;
	else
		$post = &get_post($id);

	$draft_or_pending = in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) );

	$link = $wp_rewrite->get_page_permastruct();

	if ( !empty($link) && ( ( isset($post->post_status) && !$draft_or_pending ) || $sample ) ) {
		if ( ! $leavename ) {
			$link = str_replace('%pagename%', get_page_uri($id), $link);
		}

		$link = home_url($link);
		$link = user_trailingslashit($link, 'page');
	} else {
		$link = home_url("?page_id=$id");
	}

	return apply_filters( '_get_page_link', $link, $id );
}

/**
 * Retrieve permalink for attachment.
 *
 * This can be used in the WordPress Loop or outside of it.
 *
 * @since 2.0.0
 *
 * @param int $id Optional. Post ID.
 * @return string
 */
function get_attachment_link($id = false) {
	global $post, $wp_rewrite;

	$link = false;

	if ( ! $id)
		$id = (int) $post->ID;

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

		if ( strpos($parentlink, '?') === false )
			$link = user_trailingslashit( trailingslashit($parentlink) . $name );
	}

	if ( ! $link )
		$link = home_url( "/?attachment_id=$id" );

	return apply_filters('attachment_link', $link, $id);
}

/**
 * Retrieve the permalink for the year archives.
 *
 * @since 1.5.0
 *
 * @param int|bool $year False for current year or year for permalink.
 * @return string
 */
function get_year_link($year) {
	global $wp_rewrite;
	if ( !$year )
		$year = gmdate('Y', current_time('timestamp'));
	$yearlink = $wp_rewrite->get_year_permastruct();
	if ( !empty($yearlink) ) {
		$yearlink = str_replace('%year%', $year, $yearlink);
		return apply_filters('year_link', home_url( user_trailingslashit($yearlink, 'year') ), $year);
	} else {
		return apply_filters('year_link', home_url('?m=' . $year), $year);
	}
}

/**
 * Retrieve the permalink for the month archives with year.
 *
 * @since 1.0.0
 *
 * @param bool|int $year False for current year. Integer of year.
 * @param bool|int $month False for current month. Integer of month.
 * @return string
 */
function get_month_link($year, $month) {
	global $wp_rewrite;
	if ( !$year )
		$year = gmdate('Y', current_time('timestamp'));
	if ( !$month )
		$month = gmdate('m', current_time('timestamp'));
	$monthlink = $wp_rewrite->get_month_permastruct();
	if ( !empty($monthlink) ) {
		$monthlink = str_replace('%year%', $year, $monthlink);
		$monthlink = str_replace('%monthnum%', zeroise(intval($month), 2), $monthlink);
		return apply_filters('month_link', home_url( user_trailingslashit($monthlink, 'month') ), $year, $month);
	} else {
		return apply_filters('month_link', home_url( '?m=' . $year . zeroise($month, 2) ), $year, $month);
	}
}

/**
 * Retrieve the permalink for the day archives with year and month.
 *
 * @since 1.0.0
 *
 * @param bool|int $year False for current year. Integer of year.
 * @param bool|int $month False for current month. Integer of month.
 * @param bool|int $day False for current day. Integer of day.
 * @return string
 */
function get_day_link($year, $month, $day) {
	global $wp_rewrite;
	if ( !$year )
		$year = gmdate('Y', current_time('timestamp'));
	if ( !$month )
		$month = gmdate('m', current_time('timestamp'));
	if ( !$day )
		$day = gmdate('j', current_time('timestamp'));

	$daylink = $wp_rewrite->get_day_permastruct();
	if ( !empty($daylink) ) {
		$daylink = str_replace('%year%', $year, $daylink);
		$daylink = str_replace('%monthnum%', zeroise(intval($month), 2), $daylink);
		$daylink = str_replace('%day%', zeroise(intval($day), 2), $daylink);
		return apply_filters('day_link', home_url( user_trailingslashit($daylink, 'day') ), $year, $month, $day);
	} else {
		return apply_filters('day_link', home_url( '?m=' . $year . zeroise($month, 2) . zeroise($day, 2) ), $year, $month, $day);
	}
}

/**
 * Display the permalink for the feed type.
 *
 * @since 3.0.0
 *
 * @param string $anchor The link's anchor text.
 * @param string $feed Optional, defaults to default feed. Feed type.
 */
function the_feed_link( $anchor, $feed = '' ) {
	$link = '<a href="' . esc_url( get_feed_link( $feed ) ) . '">' . $anchor . '</a>';
	echo apply_filters( 'the_feed_link', $link, $feed );
}

/**
 * Retrieve the permalink for the feed type.
 *
 * @since 1.5.0
 *
 * @param string $feed Optional, defaults to default feed. Feed type.
 * @return string
 */
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
		$output =  home_url( user_trailingslashit($permalink, 'feed') );
	} else {
		if ( empty($feed) )
			$feed = get_default_feed();

		if ( false !== strpos($feed, 'comments_') )
			$feed = str_replace('comments_', 'comments-', $feed);

		$output = home_url("?feed={$feed}");
	}

	return apply_filters('feed_link', $output, $feed);
}

/**
 * Retrieve the permalink for the post comments feed.
 *
 * @since 2.2.0
 *
 * @param int $post_id Optional. Post ID.
 * @param string $feed Optional. Feed type.
 * @return string
 */
function get_post_comments_feed_link($post_id = 0, $feed = '') {
	$post_id = absint( $post_id );

	if ( ! $post_id )
		$post_id = get_the_ID();

	if ( empty( $feed ) )
		$feed = get_default_feed();

	if ( '' != get_option('permalink_structure') ) {
		if ( 'page' == get_option('show_on_front') && $post_id == get_option('page_on_front') )
			$url = _get_page_link( $post_id );
		else
			$url = get_permalink($post_id);

		$url = trailingslashit($url) . 'feed';
		if ( $feed != get_default_feed() )
			$url .= "/$feed";
		$url = user_trailingslashit($url, 'single_feed');
	} else {
		$type = get_post_field('post_type', $post_id);
		if ( 'page' == $type )
			$url = home_url("?feed=$feed&amp;page_id=$post_id");
		else
			$url = home_url("?feed=$feed&amp;p=$post_id");
	}

	return apply_filters('post_comments_feed_link', $url);
}

/**
 * Display the comment feed link for a post.
 *
 * Prints out the comment feed link for a post. Link text is placed in the
 * anchor. If no link text is specified, default text is used. If no post ID is
 * specified, the current post is used.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5.0
 *
 * @param string $link_text Descriptive text.
 * @param int $post_id Optional post ID.  Default to current post.
 * @param string $feed Optional. Feed format.
 * @return string Link to the comment feed for the current post.
*/
function post_comments_feed_link( $link_text = '', $post_id = '', $feed = '' ) {
	$url = get_post_comments_feed_link($post_id, $feed);
	if ( empty($link_text) )
		$link_text = __('Comments Feed');

	echo apply_filters( 'post_comments_feed_link_html', "<a href='$url'>$link_text</a>", $post_id, $feed );
}

/**
 * Retrieve the feed link for a given author.
 *
 * Returns a link to the feed for all posts by a given author. A specific feed
 * can be requested or left blank to get the default feed.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5.0
 *
 * @param int $author_id ID of an author.
 * @param string $feed Optional. Feed type.
 * @return string Link to the feed for the author specified by $author_id.
*/
function get_author_feed_link( $author_id, $feed = '' ) {
	$author_id = (int) $author_id;
	$permalink_structure = get_option('permalink_structure');

	if ( empty($feed) )
		$feed = get_default_feed();

	if ( '' == $permalink_structure ) {
		$link = home_url("?feed=$feed&amp;author=" . $author_id);
	} else {
		$link = get_author_posts_url($author_id);
		if ( $feed == get_default_feed() )
			$feed_link = 'feed';
		else
			$feed_link = "feed/$feed";

		$link = trailingslashit($link) . user_trailingslashit($feed_link, 'feed');
	}

	$link = apply_filters('author_feed_link', $link, $feed);

	return $link;
}

/**
 * Retrieve the feed link for a category.
 *
 * Returns a link to the feed for all posts in a given category. A specific feed
 * can be requested or left blank to get the default feed.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5.0
 *
 * @param int $cat_id ID of a category.
 * @param string $feed Optional. Feed type.
 * @return string Link to the feed for the category specified by $cat_id.
*/
function get_category_feed_link($cat_id, $feed = '') {
	return get_term_feed_link($cat_id, 'category', $feed);
}

/**
 * Retrieve the feed link for a term.
 *
 * Returns a link to the feed for all posts in a given term. A specific feed
 * can be requested or left blank to get the default feed.
 *
 * @since 3.0
 *
 * @param int $term_id ID of a category.
 * @param string $taxonomy Optional. Taxonomy of $term_id
 * @param string $feed Optional. Feed type.
 * @return string Link to the feed for the term specified by $term_id and $taxonomy.
*/
function get_term_feed_link( $term_id, $taxonomy = 'category', $feed = '' ) {
	global $wp_rewrite;

	$term_id = ( int ) $term_id;

	$term = get_term( $term_id, $taxonomy  );

	if ( empty( $term ) || is_wp_error( $term ) )
		return false;

	if ( empty( $feed ) )
		$feed = get_default_feed();

	$permalink_structure = get_option( 'permalink_structure' );

	if ( '' == $permalink_structure ) {
		if ( 'category' == $taxonomy ) {
			$link = home_url("?feed=$feed&amp;cat=$term_id");
		}
		elseif ( 'post_tag' == $taxonomy ) {
			$link = home_url("?feed=$feed&amp;tag=$term->slug");
		} else {
			$t = get_taxonomy( $taxonomy );
			$link = home_url("?feed=$feed&amp;$t->query_var=$term->slug");
		}
	} else {
		$link = get_term_link( $term_id, $term->taxonomy );
		if ( $feed == get_default_feed() )
			$feed_link = 'feed';
		else
			$feed_link = "feed/$feed";

		$link = trailingslashit( $link ) . user_trailingslashit( $feed_link, 'feed' );
	}

	if ( 'category' == $taxonomy )
		$link = apply_filters( 'category_feed_link', $link, $feed );
	elseif ( 'post_tag' == $taxonomy )
		$link = apply_filters( 'category_feed_link', $link, $feed );
	else
		$link = apply_filters( 'taxonomy_feed_link', $link, $feed, $taxonomy );


	return $link;
}

/**
 * Retrieve permalink for feed of tag.
 *
 * @since 2.3.0
 *
 * @param int $tag_id Tag ID.
 * @param string $feed Optional. Feed type.
 * @return string
 */
function get_tag_feed_link($tag_id, $feed = '') {
	return get_term_feed_link($tag_id, 'post_tag', $feed);
}

/**
 * Retrieve edit tag link.
 *
 * @since 2.7.0
 *
 * @param int $tag_id Tag ID
 * @param string $taxonomy Taxonomy
 * @return string
 */
function get_edit_tag_link( $tag_id, $taxonomy = 'post_tag' ) {
	return apply_filters( 'get_edit_tag_link', get_edit_term_link( $tag_id, $taxonomy ) );
}

/**
 * Display or retrieve edit tag link with formatting.
 *
 * @since 2.7.0
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @param int|object $tag Tag object or ID
 * @return string HTML content.
 */
function edit_tag_link( $link = '', $before = '', $after = '', $tag = null ) {
	$link = edit_term_link( $link, '', '', false, $tag );
	echo $before . apply_filters( 'edit_tag_link', $link ) . $after;
}

/**
 * Retrieve edit term url.
 *
 * @since 3.1.0
 *
 * @param int $term_id Term ID
 * @param string $taxonomy Taxonomy
 * @param string $object_type The object type
 * @return string
 */
function get_edit_term_link( $term_id, $taxonomy, $object_type = '' ) {
	$tax = get_taxonomy( $taxonomy );
	if ( !current_user_can( $tax->cap->edit_terms ) )
		return;

	$term = get_term( $term_id, $taxonomy );

	$args = array(
		'action' => 'edit',
		'taxonomy' => $taxonomy,
		'tag_ID' => $term->term_id,
	);

	if ( $object_type )
		$args['post_type'] = $object_type;

	$location = add_query_arg( $args, admin_url( 'edit-tags.php' ) );

	return apply_filters( 'get_edit_term_link', $location, $term_id, $taxonomy, $object_type );
}

/**
 * Display or retrieve edit term link with formatting.
 *
 * @since 3.1.0
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @param object $term Term object
 * @return string HTML content.
 */
function edit_term_link( $link = '', $before = '', $after = '', $term = null, $echo = true ) {
	if ( is_null( $term ) ) {
		$term = get_queried_object();
	}

	$tax = get_taxonomy( $term->taxonomy );
	if ( !current_user_can($tax->cap->edit_terms) )
		return;

	if ( empty( $link ) )
		$link = __('Edit This');

	$link = '<a href="' . get_edit_term_link( $term->term_id, $term->taxonomy ) . '" title="' . $link . '">' . $link . '</a>';
	$link = $before . apply_filters( 'edit_term_link', $link, $term->term_id ) . $after;

	if ( $echo )
		echo $link;
	else
		return $link;
}

/**
* Retrieve permalink for search.
*
* @since  3.0.0
* @param string $query Optional. The query string to use. If empty the current query is used.
* @return string
*/
function get_search_link( $query = '' ) {
	global $wp_rewrite;

	if ( empty($query) )
		$search = get_search_query( false );
	else
		$search = stripslashes($query);

	$permastruct = $wp_rewrite->get_search_permastruct();

	if ( empty( $permastruct ) ) {
		$link = home_url('?s=' . urlencode($search) );
	} else {
		$search = urlencode($search);
		$search = str_replace('%2F', '/', $search); // %2F(/) is not valid within a URL, send it unencoded.
		$link = str_replace( '%search%', $search, $permastruct );
		$link = home_url( user_trailingslashit( $link, 'search' ) );
	}

	return apply_filters( 'search_link', $link, $search );
}

/**
 * Retrieve the permalink for the feed of the search results.
 *
 * @since 2.5.0
 *
 * @param string $search_query Optional. Search query.
 * @param string $feed Optional. Feed type.
 * @return string
 */
function get_search_feed_link($search_query = '', $feed = '') {
	global $wp_rewrite;
	$link = get_search_link($search_query);

	if ( empty($feed) )
		$feed = get_default_feed();

	$permastruct = $wp_rewrite->get_search_permastruct();

	if ( empty($permastruct) ) {
		$link = add_query_arg('feed', $feed, $link);
	} else {
		$link = trailingslashit($link);
		$link .= "feed/$feed/";
	}

	$link = apply_filters('search_feed_link', $link, $feed, 'posts');

	return $link;
}

/**
 * Retrieve the permalink for the comments feed of the search results.
 *
 * @since 2.5.0
 *
 * @param string $search_query Optional. Search query.
 * @param string $feed Optional. Feed type.
 * @return string
 */
function get_search_comments_feed_link($search_query = '', $feed = '') {
	global $wp_rewrite;

	if ( empty($feed) )
		$feed = get_default_feed();

	$link = get_search_feed_link($search_query, $feed);

	$permastruct = $wp_rewrite->get_search_permastruct();

	if ( empty($permastruct) )
		$link = add_query_arg('feed', 'comments-' . $feed, $link);
	else
		$link = add_query_arg('withcomments', 1, $link);

	$link = apply_filters('search_feed_link', $link, $feed, 'comments');

	return $link;
}

/**
 * Retrieve the permalink for a post type archive.
 *
 * @since 3.1.0
 *
 * @param string $post_type Post type
 * @return string
 */
function get_post_type_archive_link( $post_type ) {
	global $wp_rewrite;
	if ( ! $post_type_obj = get_post_type_object( $post_type ) )
		return false;

	if ( ! $post_type_obj->has_archive )
		return false;

	if ( get_option( 'permalink_structure' ) && is_array( $post_type_obj->rewrite ) ) {
		$struct = ( true === $post_type_obj->has_archive ) ? $post_type_obj->rewrite['slug'] : $post_type_obj->has_archive;
		if ( $post_type_obj->rewrite['with_front'] )
			$struct = $wp_rewrite->front . $struct;
		else
			$struct = $wp_rewrite->root . $struct;
		$link = home_url( user_trailingslashit( $struct, 'post_type_archive' ) );
	} else {
		$link = home_url( '?post_type=' . $post_type );
	}

	return apply_filters( 'post_type_archive_link', $link, $post_type );
}

/**
 * Retrieve the permalink for a post type archive feed.
 *
 * @since 3.1.0
 *
 * @param string $post_type Post type
 * @param string $feed Optional. Feed type
 * @return string
 */
function get_post_type_archive_feed_link( $post_type, $feed = '' ) {
	$default_feed = get_default_feed();
	if ( empty( $feed ) )
		$feed = $default_feed;

	if ( ! $link = get_post_type_archive_link( $post_type ) )
		return false;
	$post_type_obj = get_post_type_object( $post_type );
	if ( $post_type_obj->rewrite['feeds'] && get_option( 'permalink_structure' ) ) {
		$link = trailingslashit($link);
		$link .= 'feed/';
		if ( $feed != $default_feed )
			$link .= "$feed/";
	} else {
		$link = add_query_arg( 'feed', $feed, $link );
	}

	return apply_filters( 'post_type_archive_feed_link', $link, $feed );
}

/**
 * Retrieve edit posts link for post.
 *
 * Can be used within the WordPress loop or outside of it. Can be used with
 * pages, posts, attachments, and revisions.
 *
 * @since 2.3.0
 *
 * @param int $id Optional. Post ID.
 * @param string $context Optional, defaults to display. How to write the '&', defaults to '&amp;'.
 * @return string
 */
function get_edit_post_link( $id = 0, $context = 'display' ) {
	if ( !$post = &get_post( $id ) )
		return;

	if ( 'display' == $context )
		$action = '&amp;action=edit';
	else
		$action = '&action=edit';

	$post_type_object = get_post_type_object( $post->post_type );
	if ( !$post_type_object )
		return;

	if ( !current_user_can( $post_type_object->cap->edit_post, $post->ID ) )
		return;

	return apply_filters( 'get_edit_post_link', admin_url( sprintf($post_type_object->_edit_link . $action, $post->ID) ), $post->ID, $context );
}

/**
 * Display edit post link for post.
 *
 * @since 1.0.0
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @param int $id Optional. Post ID.
 */
function edit_post_link( $link = null, $before = '', $after = '', $id = 0 ) {
	if ( !$post = &get_post( $id ) )
		return;

	if ( !$url = get_edit_post_link( $post->ID ) )
		return;

	if ( null === $link )
		$link = __('Edit This');

	$post_type_obj = get_post_type_object( $post->post_type );
	$link = '<a class="post-edit-link" href="' . $url . '" title="' . esc_attr( $post_type_obj->labels->edit_item ) . '">' . $link . '</a>';
	echo $before . apply_filters( 'edit_post_link', $link, $post->ID ) . $after;
}

/**
 * Retrieve delete posts link for post.
 *
 * Can be used within the WordPress loop or outside of it, with any post type.
 *
 * @since 2.9.0
 *
 * @param int $id Optional. Post ID.
 * @param string $deprecated Not used.
 * @param bool $force_delete Whether to bypass trash and force deletion. Default is false.
 * @return string
 */
function get_delete_post_link( $id = 0, $deprecated = '', $force_delete = false ) {
	if ( ! empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '3.0' );

	if ( !$post = &get_post( $id ) )
		return;

	$post_type_object = get_post_type_object( $post->post_type );
	if ( !$post_type_object )
		return;

	if ( !current_user_can( $post_type_object->cap->delete_post, $post->ID ) )
		return;

	$action = ( $force_delete || !EMPTY_TRASH_DAYS ) ? 'delete' : 'trash';

	$delete_link = add_query_arg( 'action', $action, admin_url( sprintf( $post_type_object->_edit_link, $post->ID ) ) );

	return apply_filters( 'get_delete_post_link', wp_nonce_url( $delete_link, "$action-{$post->post_type}_{$post->ID}" ), $post->ID, $force_delete );
}

/**
 * Retrieve edit comment link.
 *
 * @since 2.3.0
 *
 * @param int $comment_id Optional. Comment ID.
 * @return string
 */
function get_edit_comment_link( $comment_id = 0 ) {
	$comment = &get_comment( $comment_id );

	if ( !current_user_can( 'edit_comment', $comment->comment_ID ) )
		return;

	$location = admin_url('comment.php?action=editcomment&amp;c=') . $comment->comment_ID;
	return apply_filters( 'get_edit_comment_link', $location );
}

/**
 * Display or retrieve edit comment link with formatting.
 *
 * @since 1.0.0
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @return string|null HTML content, if $echo is set to false.
 */
function edit_comment_link( $link = null, $before = '', $after = '' ) {
	global $comment;

	if ( !current_user_can( 'edit_comment', $comment->comment_ID ) )
		return;

	if ( null === $link )
		$link = __('Edit This');

	$link = '<a class="comment-edit-link" href="' . get_edit_comment_link( $comment->comment_ID ) . '" title="' . esc_attr__( 'Edit comment' ) . '">' . $link . '</a>';
	echo $before . apply_filters( 'edit_comment_link', $link, $comment->comment_ID ) . $after;
}

/**
 * Display edit bookmark (literally a URL external to blog) link.
 *
 * @since 2.7.0
 *
 * @param int $link Optional. Bookmark ID.
 * @return string
 */
function get_edit_bookmark_link( $link = 0 ) {
	$link = get_bookmark( $link );

	if ( !current_user_can('manage_links') )
		return;

	$location = admin_url('link.php?action=edit&amp;link_id=') . $link->link_id;
	return apply_filters( 'get_edit_bookmark_link', $location, $link->link_id );
}

/**
 * Display edit bookmark (literally a URL external to blog) link anchor content.
 *
 * @since 2.7.0
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @param int $bookmark Optional. Bookmark ID.
 */
function edit_bookmark_link( $link = '', $before = '', $after = '', $bookmark = null ) {
	$bookmark = get_bookmark($bookmark);

	if ( !current_user_can('manage_links') )
		return;

	if ( empty($link) )
		$link = __('Edit This');

	$link = '<a href="' . get_edit_bookmark_link( $bookmark ) . '" title="' . esc_attr__( 'Edit Link' ) . '">' . $link . '</a>';
	echo $before . apply_filters( 'edit_bookmark_link', $link, $bookmark->link_id ) . $after;
}

// Navigation links

/**
 * Retrieve previous post that is adjacent to current post.
 *
 * @since 1.5.0
 *
 * @param bool $in_same_cat Optional. Whether post should be in a same category.
 * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
 * @return mixed Post object if successful. Null if global $post is not set. Empty string if no corresponding post exists.
 */
function get_previous_post($in_same_cat = false, $excluded_categories = '') {
	return get_adjacent_post($in_same_cat, $excluded_categories);
}

/**
 * Retrieve next post that is adjacent to current post.
 *
 * @since 1.5.0
 *
 * @param bool $in_same_cat Optional. Whether post should be in a same category.
 * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
 * @return mixed Post object if successful. Null if global $post is not set. Empty string if no corresponding post exists.
 */
function get_next_post($in_same_cat = false, $excluded_categories = '') {
	return get_adjacent_post($in_same_cat, $excluded_categories, false);
}

/**
 * Retrieve adjacent post.
 *
 * Can either be next or previous post.
 *
 * @since 2.5.0
 *
 * @param bool $in_same_cat Optional. Whether post should be in a same category.
 * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
 * @param bool $previous Optional. Whether to retrieve previous post.
 * @return mixed Post object if successful. Null if global $post is not set. Empty string if no corresponding post exists.
 */
function get_adjacent_post( $in_same_cat = false, $excluded_categories = '', $previous = true ) {
	global $post, $wpdb;

	if ( empty( $post ) )
		return null;

	$current_post_date = $post->post_date;

	$join = '';
	$posts_in_ex_cats_sql = '';
	if ( $in_same_cat || ! empty( $excluded_categories ) ) {
		$join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";

		if ( $in_same_cat ) {
			$cat_array = wp_get_object_terms($post->ID, 'category', array('fields' => 'ids'));
			$join .= " AND tt.taxonomy = 'category' AND tt.term_id IN (" . implode(',', $cat_array) . ")";
		}

		$posts_in_ex_cats_sql = "AND tt.taxonomy = 'category'";
		if ( ! empty( $excluded_categories ) ) {
			if ( ! is_array( $excluded_categories ) ) {
				// back-compat, $excluded_categories used to be IDs separated by " and "
				if ( strpos( $excluded_categories, ' and ' ) !== false ) {
					_deprecated_argument( __FUNCTION__, '3.3', sprintf( __( 'Use commas instead of %s to separate excluded categories.' ), "'and'" ) );
					$excluded_categories = explode( ' and ', $excluded_categories );
				} else {
					$excluded_categories = explode( ',', $excluded_categories );
				}
			}

			$excluded_categories = array_map( 'intval', $excluded_categories );

			if ( ! empty( $cat_array ) ) {
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
	$where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish' $posts_in_ex_cats_sql", $current_post_date, $post->post_type), $in_same_cat, $excluded_categories );
	$sort  = apply_filters( "get_{$adjacent}_post_sort", "ORDER BY p.post_date $order LIMIT 1" );

	$query = "SELECT p.* FROM $wpdb->posts AS p $join $where $sort";
	$query_key = 'adjacent_post_' . md5($query);
	$result = wp_cache_get($query_key, 'counts');
	if ( false !== $result )
		return $result;

	$result = $wpdb->get_row("SELECT p.* FROM $wpdb->posts AS p $join $where $sort");
	if ( null === $result )
		$result = '';

	wp_cache_set($query_key, $result, 'counts');
	return $result;
}

/**
 * Get adjacent post relational link.
 *
 * Can either be next or previous post relational link.
 *
 * @since 2.8.0
 *
 * @param string $title Optional. Link title format.
 * @param bool $in_same_cat Optional. Whether link should be in a same category.
 * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
 * @param bool $previous Optional, default is true. Whether to display link to previous or next post.
 * @return string
 */
function get_adjacent_post_rel_link($title = '%title', $in_same_cat = false, $excluded_categories = '', $previous = true) {
	if ( $previous && is_attachment() && is_object( $GLOBALS['post'] ) )
		$post = & get_post($GLOBALS['post']->post_parent);
	else
		$post = get_adjacent_post($in_same_cat,$excluded_categories,$previous);

	if ( empty($post) )
		return;

	if ( empty($post->post_title) )
		$post->post_title = $previous ? __('Previous Post') : __('Next Post');

	$date = mysql2date(get_option('date_format'), $post->post_date);

	$title = str_replace('%title', $post->post_title, $title);
	$title = str_replace('%date', $date, $title);
	$title = apply_filters('the_title', $title, $post->ID);

	$link = $previous ? "<link rel='prev' title='" : "<link rel='next' title='";
	$link .= esc_attr( $title );
	$link .= "' href='" . get_permalink($post) . "' />\n";

	$adjacent = $previous ? 'previous' : 'next';
	return apply_filters( "{$adjacent}_post_rel_link", $link );
}

/**
 * Display relational links for the posts adjacent to the current post.
 *
 * @since 2.8.0
 *
 * @param string $title Optional. Link title format.
 * @param bool $in_same_cat Optional. Whether link should be in a same category.
 * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
 */
function adjacent_posts_rel_link($title = '%title', $in_same_cat = false, $excluded_categories = '') {
	echo get_adjacent_post_rel_link($title, $in_same_cat, $excluded_categories = '', true);
	echo get_adjacent_post_rel_link($title, $in_same_cat, $excluded_categories = '', false);
}

/**
 * Display relational links for the posts adjacent to the current post for single post pages.
 *
 * This is meant to be attached to actions like 'wp_head'. Do not call this directly in plugins or theme templates.
 * @since 3.0.0
 *
 */
function adjacent_posts_rel_link_wp_head() {
	if ( !is_singular() || is_attachment() )
		return;
	adjacent_posts_rel_link();
}

/**
 * Display relational link for the next post adjacent to the current post.
 *
 * @since 2.8.0
 *
 * @param string $title Optional. Link title format.
 * @param bool $in_same_cat Optional. Whether link should be in a same category.
 * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
 */
function next_post_rel_link($title = '%title', $in_same_cat = false, $excluded_categories = '') {
	echo get_adjacent_post_rel_link($title, $in_same_cat, $excluded_categories = '', false);
}

/**
 * Display relational link for the previous post adjacent to the current post.
 *
 * @since 2.8.0
 *
 * @param string $title Optional. Link title format.
 * @param bool $in_same_cat Optional. Whether link should be in a same category.
 * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
 */
function prev_post_rel_link($title = '%title', $in_same_cat = false, $excluded_categories = '') {
	echo get_adjacent_post_rel_link($title, $in_same_cat, $excluded_categories = '', true);
}

/**
 * Retrieve boundary post.
 *
 * Boundary being either the first or last post by publish date within the constraints specified
 * by $in_same_cat or $excluded_categories.
 *
 * @since 2.8.0
 *
 * @param bool $in_same_cat Optional. Whether returned post should be in a same category.
 * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
 * @param bool $start Optional. Whether to retrieve first or last post.
 * @return object
 */
function get_boundary_post( $in_same_cat = false, $excluded_categories = '', $start = true ) {
	global $post;

	if ( empty($post) || ! is_single() || is_attachment() )
		return null;

	$cat_array = array();
	if( ! is_array( $excluded_categories ) )
		$excluded_categories = explode( ',', $excluded_categories );

	if ( $in_same_cat || ! empty( $excluded_categories ) ) {
		if ( $in_same_cat )
			$cat_array = wp_get_object_terms( $post->ID, 'category', array( 'fields' => 'ids' ) );

		if ( ! empty( $excluded_categories ) ) {
			$excluded_categories = array_map( 'intval', $excluded_categories );
			$excluded_categories = array_diff( $excluded_categories, $cat_array );

			$inverse_cats = array();
			foreach ( $excluded_categories as $excluded_category )
				$inverse_cats[] = $excluded_category * -1;
			$excluded_categories = $inverse_cats;
		}
	}

	$categories = implode( ',', array_merge( $cat_array, $excluded_categories ) );

	$order = $start ? 'ASC' : 'DESC';

	return get_posts( array('numberposts' => 1, 'category' => $categories, 'order' => $order, 'update_post_term_cache' => false, 'update_post_meta_cache' => false) );
}

/**
 * Display previous post link that is adjacent to the current post.
 *
 * @since 1.5.0
 *
 * @param string $format Optional. Link anchor format.
 * @param string $link Optional. Link permalink format.
 * @param bool $in_same_cat Optional. Whether link should be in a same category.
 * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
 */
function previous_post_link($format='&laquo; %link', $link='%title', $in_same_cat = false, $excluded_categories = '') {
	adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, true);
}

/**
 * Display next post link that is adjacent to the current post.
 *
 * @since 1.5.0
 *
 * @param string $format Optional. Link anchor format.
 * @param string $link Optional. Link permalink format.
 * @param bool $in_same_cat Optional. Whether link should be in a same category.
 * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
 */
function next_post_link($format='%link &raquo;', $link='%title', $in_same_cat = false, $excluded_categories = '') {
	adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, false);
}

/**
 * Display adjacent post link.
 *
 * Can be either next post link or previous.
 *
 * @since 2.5.0
 *
 * @param string $format Link anchor format.
 * @param string $link Link permalink format.
 * @param bool $in_same_cat Optional. Whether link should be in a same category.
 * @param array|string $excluded_categories Optional. Array or comma-separated list of excluded category IDs.
 * @param bool $previous Optional, default is true. Whether to display link to previous or next post.
 */
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

	$title = apply_filters('the_title', $title, $post->ID);
	$date = mysql2date(get_option('date_format'), $post->post_date);
	$rel = $previous ? 'prev' : 'next';

	$string = '<a href="'.get_permalink($post).'" rel="'.$rel.'">';
	$link = str_replace('%title', $title, $link);
	$link = str_replace('%date', $date, $link);
	$link = $string . $link . '</a>';

	$format = str_replace('%link', $link, $format);

	$adjacent = $previous ? 'previous' : 'next';
	echo apply_filters( "{$adjacent}_post_link", $format, $link );
}

/**
 * Retrieve links for page numbers.
 *
 * @since 1.5.0
 *
 * @param int $pagenum Optional. Page ID.
 * @return string
 */
function get_pagenum_link($pagenum = 1) {
	global $wp_rewrite;

	$pagenum = (int) $pagenum;

	$request = remove_query_arg( 'paged' );

	$home_root = parse_url(home_url());
	$home_root = ( isset($home_root['path']) ) ? $home_root['path'] : '';
	$home_root = preg_quote( trailingslashit( $home_root ), '|' );

	$request = preg_replace('|^'. $home_root . '|', '', $request);
	$request = preg_replace('|^/+|', '', $request);

	if ( !$wp_rewrite->using_permalinks() || is_admin() ) {
		$base = trailingslashit( get_bloginfo( 'url' ) );

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

		$request = preg_replace( "|$wp_rewrite->pagination_base/\d+/?$|", '', $request);
		$request = preg_replace( '|^index\.php|', '', $request);
		$request = ltrim($request, '/');

		$base = trailingslashit( get_bloginfo( 'url' ) );

		if ( $wp_rewrite->using_index_permalinks() && ( $pagenum > 1 || '' != $request ) )
			$base .= 'index.php/';

		if ( $pagenum > 1 ) {
			$request = ( ( !empty( $request ) ) ? trailingslashit( $request ) : $request ) . user_trailingslashit( $wp_rewrite->pagination_base . "/" . $pagenum, 'paged' );
		}

		$result = $base . $request . $query_string;
	}

	$result = apply_filters('get_pagenum_link', $result);

	return $result;
}

/**
 * Retrieve next posts page link.
 *
 * Backported from 2.1.3 to 2.0.10.
 *
 * @since 2.0.10
 *
 * @param int $max_page Optional. Max pages.
 * @return string
 */
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

/**
 * Display or return the next posts page link.
 *
 * @since 0.71
 *
 * @param int $max_page Optional. Max pages.
 * @param boolean $echo Optional. Echo or return;
 */
function next_posts( $max_page = 0, $echo = true ) {
	$output = esc_url( get_next_posts_page_link( $max_page ) );

	if ( $echo )
		echo $output;
	else
		return $output;
}

/**
 * Return the next posts page link.
 *
 * @since 2.7.0
 *
 * @param string $label Content for link text.
 * @param int $max_page Optional. Max pages.
 * @return string|null
 */
function get_next_posts_link( $label = null, $max_page = 0 ) {
	global $paged, $wp_query;

	if ( !$max_page )
		$max_page = $wp_query->max_num_pages;

	if ( !$paged )
		$paged = 1;

	$nextpage = intval($paged) + 1;

	if ( null === $label )
		$label = __( 'Next Page &raquo;' );

	if ( !is_single() && ( $nextpage <= $max_page ) ) {
		$attr = apply_filters( 'next_posts_link_attributes', '' );
		return '<a href="' . next_posts( $max_page, false ) . "\" $attr>" . preg_replace('/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label) . '</a>';
	}
}

/**
 * Display the next posts page link.
 *
 * @since 0.71
 * @uses get_next_posts_link()
 *
 * @param string $label Content for link text.
 * @param int $max_page Optional. Max pages.
 */
function next_posts_link( $label = null, $max_page = 0 ) {
	echo get_next_posts_link( $label, $max_page );
}

/**
 * Retrieve previous posts page link.
 *
 * Will only return string, if not on a single page or post.
 *
 * Backported to 2.0.10 from 2.1.3.
 *
 * @since 2.0.10
 *
 * @return string|null
 */
function get_previous_posts_page_link() {
	global $paged;

	if ( !is_single() ) {
		$nextpage = intval($paged) - 1;
		if ( $nextpage < 1 )
			$nextpage = 1;
		return get_pagenum_link($nextpage);
	}
}

/**
 * Display or return the previous posts page link.
 *
 * @since 0.71
 *
 * @param boolean $echo Optional. Echo or return;
 */
function previous_posts( $echo = true ) {
	$output = esc_url( get_previous_posts_page_link() );

	if ( $echo )
		echo $output;
	else
		return $output;
}

/**
 * Return the previous posts page link.
 *
 * @since 2.7.0
 *
 * @param string $label Optional. Previous page link text.
 * @return string|null
 */
function get_previous_posts_link( $label = null ) {
	global $paged;

	if ( null === $label )
		$label = __( '&laquo; Previous Page' );

	if ( !is_single() && $paged > 1 ) {
		$attr = apply_filters( 'previous_posts_link_attributes', '' );
		return '<a href="' . previous_posts( false ) . "\" $attr>". preg_replace( '/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label ) .'</a>';
	}
}

/**
 * Display the previous posts page link.
 *
 * @since 0.71
 * @uses get_previous_posts_link()
 *
 * @param string $label Optional. Previous page link text.
 */
function previous_posts_link( $label = null ) {
	echo get_previous_posts_link( $label );
}

/**
 * Return post pages link navigation for previous and next pages.
 *
 * @since 2.8
 *
 * @param string|array $args Optional args.
 * @return string The posts link navigation.
 */
function get_posts_nav_link( $args = array() ) {
	global $wp_query;

	$return = '';

	if ( !is_singular() ) {
		$defaults = array(
			'sep' => ' &#8212; ',
			'prelabel' => __('&laquo; Previous Page'),
			'nxtlabel' => __('Next Page &raquo;'),
		);
		$args = wp_parse_args( $args, $defaults );

		$max_num_pages = $wp_query->max_num_pages;
		$paged = get_query_var('paged');

		//only have sep if there's both prev and next results
		if ($paged < 2 || $paged >= $max_num_pages) {
			$args['sep'] = '';
		}

		if ( $max_num_pages > 1 ) {
			$return = get_previous_posts_link($args['prelabel']);
			$return .= preg_replace('/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $args['sep']);
			$return .= get_next_posts_link($args['nxtlabel']);
		}
	}
	return $return;

}

/**
 * Display post pages link navigation for previous and next pages.
 *
 * @since 0.71
 *
 * @param string $sep Optional. Separator for posts navigation links.
 * @param string $prelabel Optional. Label for previous pages.
 * @param string $nxtlabel Optional Label for next pages.
 */
function posts_nav_link( $sep = '', $prelabel = '', $nxtlabel = '' ) {
	$args = array_filter( compact('sep', 'prelabel', 'nxtlabel') );
	echo get_posts_nav_link($args);
}

/**
 * Retrieve comments page number link.
 *
 * @since 2.7.0
 *
 * @param int $pagenum Optional. Page number.
 * @return string
 */
function get_comments_pagenum_link( $pagenum = 1, $max_page = 0 ) {
	global $post, $wp_rewrite;

	$pagenum = (int) $pagenum;

	$result = get_permalink( $post->ID );

	if ( 'newest' == get_option('default_comments_page') ) {
		if ( $pagenum != $max_page ) {
			if ( $wp_rewrite->using_permalinks() )
				$result = user_trailingslashit( trailingslashit($result) . 'comment-page-' . $pagenum, 'commentpaged');
			else
				$result = add_query_arg( 'cpage', $pagenum, $result );
		}
	} elseif ( $pagenum > 1 ) {
		if ( $wp_rewrite->using_permalinks() )
			$result = user_trailingslashit( trailingslashit($result) . 'comment-page-' . $pagenum, 'commentpaged');
		else
			$result = add_query_arg( 'cpage', $pagenum, $result );
	}

	$result .= '#comments';

	$result = apply_filters('get_comments_pagenum_link', $result);

	return $result;
}

/**
 * Return the link to next comments page.
 *
 * @since 2.7.1
 *
 * @param string $label Optional. Label for link text.
 * @param int $max_page Optional. Max page.
 * @return string|null
 */
function get_next_comments_link( $label = '', $max_page = 0 ) {
	global $wp_query;

	if ( !is_singular() || !get_option('page_comments') )
		return;

	$page = get_query_var('cpage');

	$nextpage = intval($page) + 1;

	if ( empty($max_page) )
		$max_page = $wp_query->max_num_comment_pages;

	if ( empty($max_page) )
		$max_page = get_comment_pages_count();

	if ( $nextpage > $max_page )
		return;

	if ( empty($label) )
		$label = __('Newer Comments &raquo;');

	return '<a href="' . esc_url( get_comments_pagenum_link( $nextpage, $max_page ) ) . '" ' . apply_filters( 'next_comments_link_attributes', '' ) . '>'. preg_replace('/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label) .'</a>';
}

/**
 * Display the link to next comments page.
 *
 * @since 2.7.0
 *
 * @param string $label Optional. Label for link text.
 * @param int $max_page Optional. Max page.
 */
function next_comments_link( $label = '', $max_page = 0 ) {
	echo get_next_comments_link( $label, $max_page );
}

/**
 * Return the previous comments page link.
 *
 * @since 2.7.1
 *
 * @param string $label Optional. Label for comments link text.
 * @return string|null
 */
function get_previous_comments_link( $label = '' ) {
	if ( !is_singular() || !get_option('page_comments') )
		return;

	$page = get_query_var('cpage');

	if ( intval($page) <= 1 )
		return;

	$prevpage = intval($page) - 1;

	if ( empty($label) )
		$label = __('&laquo; Older Comments');

	return '<a href="' . esc_url( get_comments_pagenum_link( $prevpage ) ) . '" ' . apply_filters( 'previous_comments_link_attributes', '' ) . '>' . preg_replace('/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label) .'</a>';
}

/**
 * Display the previous comments page link.
 *
 * @since 2.7.0
 *
 * @param string $label Optional. Label for comments link text.
 */
function previous_comments_link( $label = '' ) {
	echo get_previous_comments_link( $label );
}

/**
 * Create pagination links for the comments on the current post.
 *
 * @see paginate_links()
 * @since 2.7.0
 *
 * @param string|array $args Optional args. See paginate_links().
 * @return string Markup for pagination links.
*/
function paginate_comments_links($args = array()) {
	global $wp_rewrite;

	if ( !is_singular() || !get_option('page_comments') )
		return;

	$page = get_query_var('cpage');
	if ( !$page )
		$page = 1;
	$max_page = get_comment_pages_count();
	$defaults = array(
		'base' => add_query_arg( 'cpage', '%#%' ),
		'format' => '',
		'total' => $max_page,
		'current' => $page,
		'echo' => true,
		'add_fragment' => '#comments'
	);
	if ( $wp_rewrite->using_permalinks() )
		$defaults['base'] = user_trailingslashit(trailingslashit(get_permalink()) . 'comment-page-%#%', 'commentpaged');

	$args = wp_parse_args( $args, $defaults );
	$page_links = paginate_links( $args );

	if ( $args['echo'] )
		echo $page_links;
	else
		return $page_links;
}

/**
 * Retrieve shortcut link.
 *
 * Use this in 'a' element 'href' attribute.
 *
 * @since 2.6.0
 *
 * @return string
 */
function get_shortcut_link() {
	$link = "javascript:
			var d=document,
			w=window,
			e=w.getSelection,
			k=d.getSelection,
			x=d.selection,
			s=(e?e():(k)?k():(x?x.createRange().text:0)),
			f='" . admin_url('press-this.php') . "',
			l=d.location,
			e=encodeURIComponent,
			u=f+'?u='+e(l.href)+'&t='+e(d.title)+'&s='+e(s)+'&v=4';
			a=function(){if(!w.open(u,'t','toolbar=0,resizable=1,scrollbars=1,status=1,width=720,height=570'))l.href=u;};
			if (/Firefox/.test(navigator.userAgent)) setTimeout(a, 0); else a();
			void(0)";

	$link = str_replace(array("\r", "\n", "\t"),  '', $link);

	return apply_filters('shortcut_link', $link);
}

/**
 * Retrieve the home url for the current site.
 *
 * Returns the 'home' option with the appropriate protocol, 'https' if
 * is_ssl() and 'http' otherwise. If $scheme is 'http' or 'https', is_ssl() is
 * overridden.
 *
 * @package WordPress
 * @since 3.0.0
 *
 * @uses get_home_url()
 *
 * @param  string $path   (optional) Path relative to the home url.
 * @param  string $scheme (optional) Scheme to give the home url context. Currently 'http', 'https'.
 * @return string Home url link with optional path appended.
*/
function home_url( $path = '', $scheme = null ) {
	return get_home_url(null, $path, $scheme);
}

/**
 * Retrieve the home url for a given site.
 *
 * Returns the 'home' option with the appropriate protocol, 'https' if
 * is_ssl() and 'http' otherwise. If $scheme is 'http' or 'https', is_ssl() is
 * overridden.
 *
 * @package WordPress
 * @since 3.0.0
 *
 * @param  int $blog_id   (optional) Blog ID. Defaults to current blog.
 * @param  string $path   (optional) Path relative to the home url.
 * @param  string $scheme (optional) Scheme to give the home url context. Currently 'http', 'https'.
 * @return string Home url link with optional path appended.
*/
function get_home_url( $blog_id = null, $path = '', $scheme = null ) {
	$orig_scheme = $scheme;

	if ( !in_array( $scheme, array( 'http', 'https' ) ) )
		$scheme = is_ssl() && !is_admin() ? 'https' : 'http';

	if ( empty( $blog_id ) || !is_multisite() )
		$url = get_option( 'home' );
	else
		$url = get_blog_option( $blog_id, 'home' );

	if ( 'http' != $scheme )
		$url = str_replace( 'http://', "$scheme://", $url );

	if ( !empty( $path ) && is_string( $path ) && strpos( $path, '..' ) === false )
		$url .= '/' . ltrim( $path, '/' );

	return apply_filters( 'home_url', $url, $path, $orig_scheme, $blog_id );
}

/**
 * Retrieve the site url for the current site.
 *
 * Returns the 'site_url' option with the appropriate protocol, 'https' if
 * is_ssl() and 'http' otherwise. If $scheme is 'http' or 'https', is_ssl() is
 * overridden.
 *
 * @package WordPress
 * @since 2.6.0
 *
 * @uses get_site_url()
 *
 * @param string $path Optional. Path relative to the site url.
 * @param string $scheme Optional. Scheme to give the site url context. Currently 'http', 'https', 'login', 'login_post', or 'admin'.
 * @return string Site url link with optional path appended.
*/
function site_url( $path = '', $scheme = null ) {
	return get_site_url(null, $path, $scheme);
}

/**
 * Retrieve the site url for a given site.
 *
 * Returns the 'site_url' option with the appropriate protocol, 'https' if
 * is_ssl() and 'http' otherwise. If $scheme is 'http' or 'https', is_ssl() is
 * overridden.
 *
 * @package WordPress
 * @since 3.0.0
 *
 * @param int $blog_id (optional) Blog ID. Defaults to current blog.
 * @param string $path Optional. Path relative to the site url.
 * @param string $scheme Optional. Scheme to give the site url context. Currently 'http', 'https', 'login', 'login_post', or 'admin'.
 * @return string Site url link with optional path appended.
*/
function get_site_url( $blog_id = null, $path = '', $scheme = null ) {
	// should the list of allowed schemes be maintained elsewhere?
	$orig_scheme = $scheme;
	if ( !in_array( $scheme, array( 'http', 'https' ) ) ) {
		if ( ( 'login_post' == $scheme || 'rpc' == $scheme ) && ( force_ssl_login() || force_ssl_admin() ) )
			$scheme = 'https';
		elseif ( ( 'login' == $scheme ) && force_ssl_admin() )
			$scheme = 'https';
		elseif ( ( 'admin' == $scheme ) && force_ssl_admin() )
			$scheme = 'https';
		else
			$scheme = ( is_ssl() ? 'https' : 'http' );
	}

	if ( empty( $blog_id ) || !is_multisite() )
		$url = get_option( 'siteurl' );
	else
		$url = get_blog_option( $blog_id, 'siteurl' );

	if ( 'http' != $scheme )
		$url = str_replace( 'http://', "{$scheme}://", $url );

	if ( !empty( $path ) && is_string( $path ) && strpos( $path, '..' ) === false )
		$url .= '/' . ltrim( $path, '/' );

	return apply_filters( 'site_url', $url, $path, $orig_scheme, $blog_id );
}

/**
 * Retrieve the url to the admin area for the current site.
 *
 * @package WordPress
 * @since 2.6.0
 *
 * @param string $path Optional path relative to the admin url.
 * @param string $scheme The scheme to use. Default is 'admin', which obeys force_ssl_admin() and is_ssl(). 'http' or 'https' can be passed to force those schemes.
 * @return string Admin url link with optional path appended.
*/
function admin_url( $path = '', $scheme = 'admin' ) {
	return get_admin_url(null, $path, $scheme);
}

/**
 * Retrieve the url to the admin area for a given site.
 *
 * @package WordPress
 * @since 3.0.0
 *
 * @param int $blog_id (optional) Blog ID. Defaults to current blog.
 * @param string $path Optional path relative to the admin url.
 * @param string $scheme The scheme to use. Default is 'admin', which obeys force_ssl_admin() and is_ssl(). 'http' or 'https' can be passed to force those schemes.
 * @return string Admin url link with optional path appended.
*/
function get_admin_url( $blog_id = null, $path = '', $scheme = 'admin' ) {
	$url = get_site_url($blog_id, 'wp-admin/', $scheme);

	if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
		$url .= ltrim($path, '/');

	return apply_filters('admin_url', $url, $path, $blog_id);
}

/**
 * Retrieve the url to the includes directory.
 *
 * @package WordPress
 * @since 2.6.0
 *
 * @param string $path Optional. Path relative to the includes url.
 * @return string Includes url link with optional path appended.
*/
function includes_url($path = '') {
	$url = site_url() . '/' . WPINC . '/';

	if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
		$url .= ltrim($path, '/');

	return apply_filters('includes_url', $url, $path);
}

/**
 * Retrieve the url to the content directory.
 *
 * @package WordPress
 * @since 2.6.0
 *
 * @param string $path Optional. Path relative to the content url.
 * @return string Content url link with optional path appended.
*/
function content_url($path = '') {
	$url = WP_CONTENT_URL;
	if ( 0 === strpos($url, 'http') && is_ssl() )
		$url = str_replace( 'http://', 'https://', $url );

	if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
		$url .= '/' . ltrim($path, '/');

	return apply_filters('content_url', $url, $path);
}

/**
 * Retrieve the url to the plugins directory or to a specific file within that directory.
 * You can hardcode the plugin slug in $path or pass __FILE__ as a second argument to get the correct folder name.
 *
 * @package WordPress
 * @since 2.6.0
 *
 * @param string $path Optional. Path relative to the plugins url.
 * @param string $plugin Optional. The plugin file that you want to be relative to - i.e. pass in __FILE__
 * @return string Plugins url link with optional path appended.
*/
function plugins_url($path = '', $plugin = '') {

	$mu_plugin_dir = WPMU_PLUGIN_DIR;
	foreach ( array('path', 'plugin', 'mu_plugin_dir') as $var ) {
		$$var = str_replace('\\' ,'/', $$var); // sanitize for Win32 installs
		$$var = preg_replace('|/+|', '/', $$var);
	}

	if ( !empty($plugin) && 0 === strpos($plugin, $mu_plugin_dir) )
		$url = WPMU_PLUGIN_URL;
	else
		$url = WP_PLUGIN_URL;

	if ( 0 === strpos($url, 'http') && is_ssl() )
		$url = str_replace( 'http://', 'https://', $url );

	if ( !empty($plugin) && is_string($plugin) ) {
		$folder = dirname(plugin_basename($plugin));
		if ( '.' != $folder )
			$url .= '/' . ltrim($folder, '/');
	}

	if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
		$url .= '/' . ltrim($path, '/');

	return apply_filters('plugins_url', $url, $path, $plugin);
}

/**
 * Retrieve the site url for the current network.
 *
 * Returns the site url with the appropriate protocol, 'https' if
 * is_ssl() and 'http' otherwise. If $scheme is 'http' or 'https', is_ssl() is
 * overridden.
 *
 * @package WordPress
 * @since 3.0.0
 *
 * @param string $path Optional. Path relative to the site url.
 * @param string $scheme Optional. Scheme to give the site url context. Currently 'http', 'https', 'login', 'login_post', or 'admin'.
 * @return string Site url link with optional path appended.
*/
function network_site_url( $path = '', $scheme = null ) {
	global $current_site;

	if ( !is_multisite() )
		return site_url($path, $scheme);

	$orig_scheme = $scheme;
	if ( !in_array($scheme, array('http', 'https')) ) {
		if ( ( 'login_post' == $scheme || 'rpc' == $scheme ) && ( force_ssl_login() || force_ssl_admin() ) )
			$scheme = 'https';
		elseif ( ('login' == $scheme) && ( force_ssl_admin() ) )
			$scheme = 'https';
		elseif ( ('admin' == $scheme) && force_ssl_admin() )
			$scheme = 'https';
		else
			$scheme = ( is_ssl() ? 'https' : 'http' );
	}

	$url = $scheme . '://' . $current_site->domain . $current_site->path;

	if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
		$url .= ltrim($path, '/');

	return apply_filters('network_site_url', $url, $path, $orig_scheme);
}

/**
 * Retrieve the home url for the current network.
 *
 * Returns the home url with the appropriate protocol, 'https' if
 * is_ssl() and 'http' otherwise. If $scheme is 'http' or 'https', is_ssl() is
 * overridden.
 *
 * @package WordPress
 * @since 3.0.0
 *
 * @param  string $path   (optional) Path relative to the home url.
 * @param  string $scheme (optional) Scheme to give the home url context. Currently 'http', 'https'.
 * @return string Home url link with optional path appended.
*/
function network_home_url( $path = '', $scheme = null ) {
	global $current_site;

	if ( !is_multisite() )
		return home_url($path, $scheme);

	$orig_scheme = $scheme;

	if ( !in_array($scheme, array('http', 'https')) )
		$scheme = is_ssl() && !is_admin() ? 'https' : 'http';

	$url = $scheme . '://' . $current_site->domain . $current_site->path;

	if ( !empty( $path ) && is_string( $path ) && strpos( $path, '..' ) === false )
		$url .= ltrim( $path, '/' );

	return apply_filters( 'network_home_url', $url, $path, $orig_scheme);
}

/**
 * Retrieve the url to the admin area for the network.
 *
 * @package WordPress
 * @since 3.0.0
 *
 * @param string $path Optional path relative to the admin url.
 * @param string $scheme The scheme to use. Default is 'admin', which obeys force_ssl_admin() and is_ssl(). 'http' or 'https' can be passed to force those schemes.
 * @return string Admin url link with optional path appended.
*/
function network_admin_url( $path = '', $scheme = 'admin' ) {
	if ( ! is_multisite() )
		return admin_url( $path, $scheme );

	$url = network_site_url('wp-admin/network/', $scheme);

	if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
		$url .= ltrim($path, '/');

	return apply_filters('network_admin_url', $url, $path);
}

/**
 * Retrieve the url to the admin area for the current user.
 *
 * @package WordPress
 * @since 3.0.0
 *
 * @param string $path Optional path relative to the admin url.
 * @param string $scheme The scheme to use. Default is 'admin', which obeys force_ssl_admin() and is_ssl(). 'http' or 'https' can be passed to force those schemes.
 * @return string Admin url link with optional path appended.
*/
function user_admin_url( $path = '', $scheme = 'admin' ) {
	$url = network_site_url('wp-admin/user/', $scheme);

	if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
		$url .= ltrim($path, '/');

	return apply_filters('user_admin_url', $url, $path);
}

/**
 * Retrieve the url to the admin area for either the current blog or the network depending on context.
 *
 * @package WordPress
 * @since 3.1.0
 *
 * @param string $path Optional path relative to the admin url.
 * @param string $scheme The scheme to use. Default is 'admin', which obeys force_ssl_admin() and is_ssl(). 'http' or 'https' can be passed to force those schemes.
 * @return string Admin url link with optional path appended.
*/
function self_admin_url($path = '', $scheme = 'admin') {
	if ( is_network_admin() )
		return network_admin_url($path, $scheme);
	elseif ( is_user_admin() )
		return user_admin_url($path, $scheme);
	else
		return admin_url($path, $scheme);
}

/**
 * Get the URL to the user's dashboard.
 *
 * If a user does not belong to any site, the global user dashboard is used.  If the user belongs to the current site,
 * the dashboard for the current site is returned. If the user cannot edit the current site, the dashboard to the user's
 * primary blog is returned.
 *
 * @since 3.1.0
 *
 * @param int $user_id User ID
 * @param string $path Optional path relative to the dashboard. Use only paths known to both blog and user admins.
 * @param string $scheme The scheme to use. Default is 'admin', which obeys force_ssl_admin() and is_ssl(). 'http' or 'https' can be passed to force those schemes.
 * @return string Dashboard url link with optional path appended.
 */
function get_dashboard_url( $user_id, $path = '', $scheme = 'admin' ) {
	$user_id = (int) $user_id;

	$blogs = get_blogs_of_user( $user_id );
	if ( ! is_super_admin() && empty($blogs) ) {
		$url = user_admin_url( $path, $scheme );
	} elseif ( ! is_multisite() ) {
		$url = admin_url( $path, $scheme );
	} else {
		$current_blog = get_current_blog_id();
		if ( $current_blog  && ( is_super_admin( $user_id ) || in_array( $current_blog, array_keys( $blogs ) ) ) ) {
			$url = admin_url( $path, $scheme );
		} else {
			$active = get_active_blog_for_user( $user_id );
			if ( $active )
				$url = get_admin_url( $active->blog_id, $path, $scheme );
			else
				$url = user_admin_url( $path, $scheme );
		}
	}

	return apply_filters( 'user_dashboard_url', $url, $user_id, $path, $scheme);
}

/**
 * Get the URL to the user's profile editor.
 *
 * @since 3.1.0
 *
 * @param int $user User ID
 * @param string $scheme The scheme to use. Default is 'admin', which obeys force_ssl_admin() and is_ssl(). 'http' or 'https' can be passed to force those schemes.
 * @return string Dashboard url link with optional path appended.
 */
function get_edit_profile_url( $user, $scheme = 'admin' ) {
	$user = (int) $user;

	if ( is_user_admin() )
		$url = user_admin_url( 'profile.php', $scheme );
	elseif ( is_network_admin() )
		$url = network_admin_url( 'profile.php', $scheme );
	else
		$url = get_dashboard_url( $user, 'profile.php', $scheme );

	return apply_filters( 'edit_profile_url', $url, $user, $scheme);
}

/**
 * Output rel=canonical for singular queries.
 *
 * @package WordPress
 * @since 2.9.0
*/
function rel_canonical() {
	if ( !is_singular() )
		return;

	global $wp_the_query;
	if ( !$id = $wp_the_query->get_queried_object_id() )
		return;

	$link = get_permalink( $id );
	echo "<link rel='canonical' href='$link' />\n";
}

/**
 * Return a shortlink for a post, page, attachment, or blog.
 *
 * This function exists to provide a shortlink tag that all themes and plugins can target. A plugin must hook in to
 * provide the actual shortlinks. Default shortlink support is limited to providing ?p= style links for posts.
 * Plugins can short-circuit this function via the pre_get_shortlink filter or filter the output
 * via the get_shortlink filter.
 *
 * @since 3.0.0.
 *
 * @param int $id A post or blog id. Default is 0, which means the current post or blog.
 * @param string $context Whether the id is a 'blog' id, 'post' id, or 'media' id. If 'post', the post_type of the post is consulted. If 'query', the current query is consulted to determine the id and context. Default is 'post'.
 * @param bool $allow_slugs Whether to allow post slugs in the shortlink. It is up to the plugin how and whether to honor this.
 * @return string A shortlink or an empty string if no shortlink exists for the requested resource or if shortlinks are not enabled.
 */
function wp_get_shortlink($id = 0, $context = 'post', $allow_slugs = true) {
	// Allow plugins to short-circuit this function.
	$shortlink = apply_filters('pre_get_shortlink', false, $id, $context, $allow_slugs);
	if ( false !== $shortlink )
		return $shortlink;

	global $wp_query;
	$post_id = 0;
	if ( 'query' == $context && is_single() ) {
		$post_id = $wp_query->get_queried_object_id();
	} elseif ( 'post' == $context ) {
		$post = get_post($id);
		$post_id = $post->ID;
	}

	$shortlink = '';

	// Return p= link for posts.
	if ( !empty($post_id) && '' != get_option('permalink_structure') ) {
		$post = get_post($post_id);
		if ( isset($post->post_type) && 'post' == $post->post_type )
			$shortlink = home_url('?p=' . $post->ID);
	}

	return apply_filters('get_shortlink', $shortlink, $id, $context, $allow_slugs);
}

/**
 *  Inject rel=shortlink into head if a shortlink is defined for the current page.
 *
 *  Attached to the wp_head action.
 *
 * @since 3.0.0
 *
 * @uses wp_get_shortlink()
 */
function wp_shortlink_wp_head() {
	$shortlink = wp_get_shortlink( 0, 'query' );

	if ( empty( $shortlink ) )
		return;

	echo "<link rel='shortlink' href='" . esc_url( $shortlink ) . "' />\n";
}

/**
 * Send a Link: rel=shortlink header if a shortlink is defined for the current page.
 *
 * Attached to the wp action.
 *
 * @since 3.0.0
 *
 * @uses wp_get_shortlink()
 */
function wp_shortlink_header() {
	if ( headers_sent() )
		return;

	$shortlink = wp_get_shortlink(0, 'query');

	if ( empty($shortlink) )
		return;

	header('Link: <' . $shortlink . '>; rel=shortlink', false);
}

/**
 * Display the Short Link for a Post
 *
 * Must be called from inside "The Loop"
 *
 * Call like the_shortlink(__('Shortlinkage FTW'))
 *
 * @since 3.0.0
 *
 * @param string $text Optional The link text or HTML to be displayed. Defaults to 'This is the short link.'
 * @param string $title Optional The tooltip for the link. Must be sanitized. Defaults to the sanitized post title.
 * @param string $before Optional HTML to display before the link.
 * @param string $after Optional HTML to display after the link.
 */
function the_shortlink( $text = '', $title = '', $before = '', $after = '' ) {
	global $post;

	if ( empty( $text ) )
		$text = __('This is the short link.');

	if ( empty( $title ) )
		$title = the_title_attribute( array( 'echo' => FALSE ) );

	$shortlink = wp_get_shortlink( $post->ID );

	if ( !empty( $shortlink ) ) {
		$link = '<a rel="shortlink" href="' . esc_url( $shortlink ) . '" title="' . $title . '">' . $text . '</a>';
		$link = apply_filters( 'the_shortlink', $link, $shortlink, $text, $title );
		echo $before, $link, $after;
	}
}

?>
