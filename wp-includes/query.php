<?php
/**
 * WordPress Query API
 *
 * The query API attempts to get which part of WordPress the user is on. It
 * also provides functionality for getting URL query information.
 *
 * @link http://codex.wordpress.org/The_Loop More information on The Loop.
 *
 * @package WordPress
 * @subpackage Query
 */

/**
 * Retrieve variable in the WP_Query class.
 *
 * @see WP_Query::get()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param string $var The variable key to retrieve.
 * @return mixed
 */
function get_query_var($var) {
	global $wp_query;

	return $wp_query->get($var);
}

/**
 * Retrieve the currently-queried object. Wrapper for $wp_query->get_queried_object()
 *
 * @uses WP_Query::get_queried_object
 *
 * @since 3.1.0
 * @access public
 *
 * @return object
 */
function get_queried_object() {
	global $wp_query;
	return $wp_query->get_queried_object();
}

/**
 * Retrieve ID of the current queried object. Wrapper for $wp_query->get_queried_object_id()
 *
 * @uses WP_Query::get_queried_object_id()
 *
 * @since 3.1.0
 * @access public
 *
 * @return int
 */
function get_queried_object_id() {
	global $wp_query;
	return $wp_query->get_queried_object_id();
}

/**
 * Set query variable.
 *
 * @see WP_Query::set()
 * @since 2.2.0
 * @uses $wp_query
 *
 * @param string $var Query variable key.
 * @param mixed $value
 * @return null
 */
function set_query_var($var, $value) {
	global $wp_query;

	return $wp_query->set($var, $value);
}

/**
 * Set up The Loop with query parameters.
 *
 * This will override the current WordPress Loop and shouldn't be used more than
 * once. This must not be used within the WordPress Loop.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param string $query
 * @return array List of posts
 */
function query_posts($query) {
	$GLOBALS['wp_query'] = new WP_Query();
	return $GLOBALS['wp_query']->query($query);
}

/**
 * Destroy the previous query and set up a new query.
 *
 * This should be used after {@link query_posts()} and before another {@link
 * query_posts()}. This will remove obscure bugs that occur when the previous
 * wp_query object is not destroyed properly before another is set up.
 *
 * @since 2.3.0
 * @uses $wp_query
 */
function wp_reset_query() {
	$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'];
	wp_reset_postdata();
}

/**
 * After looping through a separate query, this function restores
 * the $post global to the current post in the main query.
 *
 * @since 3.0.0
 * @uses $wp_query
 */
function wp_reset_postdata() {
	global $wp_query;
	$wp_query->reset_postdata();
}

/*
 * Query type checks.
 */

/**
 * Is the query for an existing archive page?
 *
 * Month, Year, Category, Author, Post Type archive...
 *
 * @see WP_Query::is_archive()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_archive() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_archive();
}

/**
 * Is the query for an existing post type archive page?
 *
 * @see WP_Query::is_post_type_archive()
 * @since 3.1.0
 * @uses $wp_query
 *
 * @param mixed $post_types Optional. Post type or array of posts types to check against.
 * @return bool
 */
function is_post_type_archive( $post_types = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_post_type_archive( $post_types );
}

/**
 * Is the query for an existing attachment page?
 *
 * @see WP_Query::is_attachment()
 * @since 2.0.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_attachment() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_attachment();
}

/**
 * Is the query for an existing author archive page?
 *
 * If the $author parameter is specified, this function will additionally
 * check if the query is for one of the authors specified.
 *
 * @see WP_Query::is_author()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param mixed $author Optional. User ID, nickname, nicename, or array of User IDs, nicknames, and nicenames
 * @return bool
 */
function is_author( $author = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_author( $author );
}

/**
 * Is the query for an existing category archive page?
 *
 * If the $category parameter is specified, this function will additionally
 * check if the query is for one of the categories specified.
 *
 * @see WP_Query::is_category()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param mixed $category Optional. Category ID, name, slug, or array of Category IDs, names, and slugs.
 * @return bool
 */
function is_category( $category = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_category( $category );
}

/**
 * Is the query for an existing tag archive page?
 *
 * If the $tag parameter is specified, this function will additionally
 * check if the query is for one of the tags specified.
 *
 * @see WP_Query::is_tag()
 * @since 2.3.0
 * @uses $wp_query
 *
 * @param mixed $tag Optional. Tag ID, name, slug, or array of Tag IDs, names, and slugs.
 * @return bool
 */
function is_tag( $tag = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_tag( $tag );
}

/**
 * Is the query for an existing taxonomy archive page?
 *
 * If the $taxonomy parameter is specified, this function will additionally
 * check if the query is for that specific $taxonomy.
 *
 * If the $term parameter is specified in addition to the $taxonomy parameter,
 * this function will additionally check if the query is for one of the terms
 * specified.
 *
 * @see WP_Query::is_tax()
 * @since 2.5.0
 * @uses $wp_query
 *
 * @param mixed $taxonomy Optional. Taxonomy slug or slugs.
 * @param mixed $term Optional. Term ID, name, slug or array of Term IDs, names, and slugs.
 * @return bool
 */
function is_tax( $taxonomy = '', $term = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_tax( $taxonomy, $term );
}

/**
 * Whether the current URL is within the comments popup window.
 *
 * @see WP_Query::is_comments_popup()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_comments_popup() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_comments_popup();
}

/**
 * Is the query for an existing date archive?
 *
 * @see WP_Query::is_date()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_date() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_date();
}

/**
 * Is the query for an existing day archive?
 *
 * @see WP_Query::is_day()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_day() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_day();
}

/**
 * Is the query for a feed?
 *
 * @see WP_Query::is_feed()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param string|array $feeds Optional feed types to check.
 * @return bool
 */
function is_feed( $feeds = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_feed( $feeds );
}

/**
 * Is the query for a comments feed?
 *
 * @see WP_Query::is_comments_feed()
 * @since 3.0.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_comment_feed() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_comment_feed();
}

/**
 * Is the query for the front page of the site?
 *
 * This is for what is displayed at your site's main URL.
 *
 * Depends on the site's "Front page displays" Reading Settings 'show_on_front' and 'page_on_front'.
 *
 * If you set a static page for the front page of your site, this function will return
 * true when viewing that page.
 *
 * Otherwise the same as @see is_home()
 *
 * @see WP_Query::is_front_page()
 * @since 2.5.0
 * @uses is_home()
 * @uses get_option()
 *
 * @return bool True, if front of site.
 */
function is_front_page() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_front_page();
}

/**
 * Is the query for the blog homepage?
 *
 * This is the page which shows the time based blog content of your site.
 *
 * Depends on the site's "Front page displays" Reading Settings 'show_on_front' and 'page_for_posts'.
 *
 * If you set a static page for the front page of your site, this function will return
 * true only on the page you set as the "Posts page".
 *
 * @see is_front_page()
 *
 * @see WP_Query::is_home()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool True if blog view homepage.
 */
function is_home() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_home();
}

/**
 * Is the query for an existing month archive?
 *
 * @see WP_Query::is_month()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_month() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_month();
}

/**
 * Is the query for an existing single page?
 *
 * If the $page parameter is specified, this function will additionally
 * check if the query is for one of the pages specified.
 *
 * @see is_single()
 * @see is_singular()
 *
 * @see WP_Query::is_page()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param mixed $page Page ID, title, slug, or array of such.
 * @return bool
 */
function is_page( $page = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_page( $page );
}

/**
 * Is the query for paged result and not for the first page?
 *
 * @see WP_Query::is_paged()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_paged() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_paged();
}

/**
 * Is the query for a post or page preview?
 *
 * @see WP_Query::is_preview()
 * @since 2.0.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_preview() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_preview();
}

/**
 * Is the query for the robots file?
 *
 * @see WP_Query::is_robots()
 * @since 2.1.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_robots() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_robots();
}

/**
 * Is the query for a search?
 *
 * @see WP_Query::is_search()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_search() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_search();
}

/**
 * Is the query for an existing single post?
 *
 * Works for any post type, except attachments and pages
 *
 * If the $post parameter is specified, this function will additionally
 * check if the query is for one of the Posts specified.
 *
 * @see is_page()
 * @see is_singular()
 *
 * @see WP_Query::is_single()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param mixed $post Post ID, title, slug, or array of such.
 * @return bool
 */
function is_single( $post = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_single( $post );
}

/**
 * Is the query for an existing single post of any post type (post, attachment, page, ... )?
 *
 * If the $post_types parameter is specified, this function will additionally
 * check if the query is for one of the Posts Types specified.
 *
 * @see is_page()
 * @see is_single()
 *
 * @see WP_Query::is_singular()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param mixed $post_types Optional. Post Type or array of Post Types
 * @return bool
 */
function is_singular( $post_types = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_singular( $post_types );
}

/**
 * Is the query for a specific time?
 *
 * @see WP_Query::is_time()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_time() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_time();
}

/**
 * Is the query for a trackback endpoint call?
 *
 * @see WP_Query::is_trackback()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_trackback() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_trackback();
}

/**
 * Is the query for an existing year archive?
 *
 * @see WP_Query::is_year()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_year() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_year();
}

/**
 * Is the query a 404 (returns no results)?
 *
 * @see WP_Query::is_404()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_404() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1' );
		return false;
	}

	return $wp_query->is_404();
}

/**
 * Is the query the main query?
 *
 * @since 3.3.0
 *
 * @return bool
 */
function is_main_query() {
	if ( 'pre_get_posts' === current_filter() ) {
		$message = sprintf( __( 'In <code>%1$s</code>, use the <code>%2$s</code> method, not the <code>%3$s</code> function. See %4$s.' ),
			'pre_get_posts', 'WP_Query::is_main_query()', 'is_main_query()', __( 'http://codex.wordpress.org/Function_Reference/is_main_query' ) );
		_doing_it_wrong( __FUNCTION__, $message, '3.7' );
	}

	global $wp_query;
	return $wp_query->is_main_query();
}

/*
 * The Loop. Post loop control.
 */

/**
 * Whether current WordPress query has results to loop over.
 *
 * @see WP_Query::have_posts()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function have_posts() {
	global $wp_query;

	return $wp_query->have_posts();
}

/**
 * Whether the caller is in the Loop.
 *
 * @since 2.0.0
 * @uses $wp_query
 *
 * @return bool True if caller is within loop, false if loop hasn't started or ended.
 */
function in_the_loop() {
	global $wp_query;

	return $wp_query->in_the_loop;
}

/**
 * Rewind the loop posts.
 *
 * @see WP_Query::rewind_posts()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return null
 */
function rewind_posts() {
	global $wp_query;

	return $wp_query->rewind_posts();
}

/**
 * Iterate the post index in the loop.
 *
 * @see WP_Query::the_post()
 * @since 1.5.0
 * @uses $wp_query
 */
function the_post() {
	global $wp_query;

	$wp_query->the_post();
}

/*
 * Comments loop.
 */

/**
 * Whether there are comments to loop over.
 *
 * @see WP_Query::have_comments()
 * @since 2.2.0
 * @uses $wp_query
 *
 * @return bool
 */
function have_comments() {
	global $wp_query;
	return $wp_query->have_comments();
}

/**
 * Iterate comment index in the comment loop.
 *
 * @see WP_Query::the_comment()
 * @since 2.2.0
 * @uses $wp_query
 *
 * @return object
 */
function the_comment() {
	global $wp_query;
	return $wp_query->the_comment();
}

/*
 * WP_Query
 */

/**
 * The WordPress Query class.
 *
 * @link http://codex.wordpress.org/Function_Reference/WP_Query Codex page.
 *
 * @since 1.5.0
 */
class WP_Query {

	/**
	 * Query vars set by the user
	 *
	 * @since 1.5.0
	 * @access public
	 * @var array
	 */
	var $query;

	/**
	 * Query vars, after parsing
	 *
	 * @since 1.5.0
	 * @access public
	 * @var array
	 */
	var $query_vars = array();

	/**
	 * Taxonomy query, as passed to get_tax_sql()
	 *
	 * @since 3.1.0
	 * @access public
	 * @var object WP_Tax_Query
	 */
	var $tax_query;

	/**
	 * Metadata query container
	 *
	 * @since 3.2.0
	 * @access public
	 * @var object WP_Meta_Query
	 */
	var $meta_query = false;

	/**
	 * Date query container
	 *
	 * @since 3.7.0
	 * @access public
	 * @var object WP_Date_Query
	 */
	var $date_query = false;

	/**
	 * Holds the data for a single object that is queried.
	 *
	 * Holds the contents of a post, page, category, attachment.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var object|array
	 */
	var $queried_object;

	/**
	 * The ID of the queried object.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var int
	 */
	var $queried_object_id;

	/**
	 * Get post database query.
	 *
	 * @since 2.0.1
	 * @access public
	 * @var string
	 */
	var $request;

	/**
	 * List of posts.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var array
	 */
	var $posts;

	/**
	 * The amount of posts for the current query.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var int
	 */
	var $post_count = 0;

	/**
	 * Index of the current item in the loop.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var int
	 */
	var $current_post = -1;

	/**
	 * Whether the loop has started and the caller is in the loop.
	 *
	 * @since 2.0.0
	 * @access public
	 * @var bool
	 */
	var $in_the_loop = false;

	/**
	 * The current post.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var WP_Post
	 */
	var $post;

	/**
	 * The list of comments for current post.
	 *
	 * @since 2.2.0
	 * @access public
	 * @var array
	 */
	var $comments;

	/**
	 * The amount of comments for the posts.
	 *
	 * @since 2.2.0
	 * @access public
	 * @var int
	 */
	var $comment_count = 0;

	/**
	 * The index of the comment in the comment loop.
	 *
	 * @since 2.2.0
	 * @access public
	 * @var int
	 */
	var $current_comment = -1;

	/**
	 * Current comment ID.
	 *
	 * @since 2.2.0
	 * @access public
	 * @var int
	 */
	var $comment;

	/**
	 * The amount of found posts for the current query.
	 *
	 * If limit clause was not used, equals $post_count.
	 *
	 * @since 2.1.0
	 * @access public
	 * @var int
	 */
	var $found_posts = 0;

	/**
	 * The amount of pages.
	 *
	 * @since 2.1.0
	 * @access public
	 * @var int
	 */
	var $max_num_pages = 0;

	/**
	 * The amount of comment pages.
	 *
	 * @since 2.7.0
	 * @access public
	 * @var int
	 */
	var $max_num_comment_pages = 0;

	/**
	 * Set if query is single post.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_single = false;

	/**
	 * Set if query is preview of blog.
	 *
	 * @since 2.0.0
	 * @access public
	 * @var bool
	 */
	var $is_preview = false;

	/**
	 * Set if query returns a page.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_page = false;

	/**
	 * Set if query is an archive list.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_archive = false;

	/**
	 * Set if query is part of a date.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_date = false;

	/**
	 * Set if query contains a year.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_year = false;

	/**
	 * Set if query contains a month.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_month = false;

	/**
	 * Set if query contains a day.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_day = false;

	/**
	 * Set if query contains time.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_time = false;

	/**
	 * Set if query contains an author.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_author = false;

	/**
	 * Set if query contains category.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_category = false;

	/**
	 * Set if query contains tag.
	 *
	 * @since 2.3.0
	 * @access public
	 * @var bool
	 */
	var $is_tag = false;

	/**
	 * Set if query contains taxonomy.
	 *
	 * @since 2.5.0
	 * @access public
	 * @var bool
	 */
	var $is_tax = false;

	/**
	 * Set if query was part of a search result.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_search = false;

	/**
	 * Set if query is feed display.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_feed = false;

	/**
	 * Set if query is comment feed display.
	 *
	 * @since 2.2.0
	 * @access public
	 * @var bool
	 */
	var $is_comment_feed = false;

	/**
	 * Set if query is trackback.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_trackback = false;

	/**
	 * Set if query is blog homepage.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_home = false;

	/**
	 * Set if query couldn't found anything.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_404 = false;

	/**
	 * Set if query is within comments popup window.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_comments_popup = false;

	/**
	 * Set if query is paged
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_paged = false;

	/**
	 * Set if query is part of administration page.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var bool
	 */
	var $is_admin = false;

	/**
	 * Set if query is an attachment.
	 *
	 * @since 2.0.0
	 * @access public
	 * @var bool
	 */
	var $is_attachment = false;

	/**
	 * Set if is single, is a page, or is an attachment.
	 *
	 * @since 2.1.0
	 * @access public
	 * @var bool
	 */
	var $is_singular = false;

	/**
	 * Set if query is for robots.
	 *
	 * @since 2.1.0
	 * @access public
	 * @var bool
	 */
	var $is_robots = false;

	/**
	 * Set if query contains posts.
	 *
	 * Basically, the homepage if the option isn't set for the static homepage.
	 *
	 * @since 2.1.0
	 * @access public
	 * @var bool
	 */
	var $is_posts_page = false;

	/**
	 * Set if query is for a post type archive.
	 *
	 * @since 3.1.0
	 * @access public
	 * @var bool
	 */
	var $is_post_type_archive = false;

	/**
	 * Stores the ->query_vars state like md5(serialize( $this->query_vars ) ) so we know
	 * whether we have to re-parse because something has changed
	 *
	 * @since 3.1.0
	 * @access private
	 */
	var $query_vars_hash = false;

	/**
	 * Whether query vars have changed since the initial parse_query() call. Used to catch modifications to query vars made
	 * via pre_get_posts hooks.
	 *
	 * @since 3.1.1
	 * @access private
	 */
	var $query_vars_changed = true;

	/**
	 * Set if post thumbnails are cached
	 *
	 * @since 3.2.0
	 * @access public
	 * @var bool
	 */
	 var $thumbnails_cached = false;

	/**
	 * Cached list of search stopwords.
	 *
	 * @since 3.7.0
	 * @var array
	 */
	private $stopwords;

	/**
	 * Resets query flags to false.
	 *
	 * The query flags are what page info WordPress was able to figure out.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function init_query_flags() {
		$this->is_single = false;
		$this->is_preview = false;
		$this->is_page = false;
		$this->is_archive = false;
		$this->is_date = false;
		$this->is_year = false;
		$this->is_month = false;
		$this->is_day = false;
		$this->is_time = false;
		$this->is_author = false;
		$this->is_category = false;
		$this->is_tag = false;
		$this->is_tax = false;
		$this->is_search = false;
		$this->is_feed = false;
		$this->is_comment_feed = false;
		$this->is_trackback = false;
		$this->is_home = false;
		$this->is_404 = false;
		$this->is_comments_popup = false;
		$this->is_paged = false;
		$this->is_admin = false;
		$this->is_attachment = false;
		$this->is_singular = false;
		$this->is_robots = false;
		$this->is_posts_page = false;
		$this->is_post_type_archive = false;
	}

	/**
	 * Initiates object properties and sets default values.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	function init() {
		unset($this->posts);
		unset($this->query);
		$this->query_vars = array();
		unset($this->queried_object);
		unset($this->queried_object_id);
		$this->post_count = 0;
		$this->current_post = -1;
		$this->in_the_loop = false;
		unset( $this->request );
		unset( $this->post );
		unset( $this->comments );
		unset( $this->comment );
		$this->comment_count = 0;
		$this->current_comment = -1;
		$this->found_posts = 0;
		$this->max_num_pages = 0;
		$this->max_num_comment_pages = 0;

		$this->init_query_flags();
	}

	/**
	 * Reparse the query vars.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	function parse_query_vars() {
		$this->parse_query();
	}

	/**
	 * Fills in the query variables, which do not exist within the parameter.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param array $array Defined query variables.
	 * @return array Complete query variables with undefined ones filled in empty.
	 */
	function fill_query_vars($array) {
		$keys = array(
			'error'
			, 'm'
			, 'p'
			, 'post_parent'
			, 'subpost'
			, 'subpost_id'
			, 'attachment'
			, 'attachment_id'
			, 'name'
			, 'static'
			, 'pagename'
			, 'page_id'
			, 'second'
			, 'minute'
			, 'hour'
			, 'day'
			, 'monthnum'
			, 'year'
			, 'w'
			, 'category_name'
			, 'tag'
			, 'cat'
			, 'tag_id'
			, 'author'
			, 'author_name'
			, 'feed'
			, 'tb'
			, 'paged'
			, 'comments_popup'
			, 'meta_key'
			, 'meta_value'
			, 'preview'
			, 's'
			, 'sentence'
			, 'fields'
			, 'menu_order'
		);

		foreach ( $keys as $key ) {
			if ( !isset($array[$key]) )
				$array[$key] = '';
		}

		$array_keys = array( 'category__in', 'category__not_in', 'category__and', 'post__in', 'post__not_in',
			'tag__in', 'tag__not_in', 'tag__and', 'tag_slug__in', 'tag_slug__and', 'post_parent__in', 'post_parent__not_in',
			'author__in', 'author__not_in' );

		foreach ( $array_keys as $key ) {
			if ( !isset($array[$key]) )
				$array[$key] = array();
		}
		return $array;
	}

	/**
	 * Parse a query string and set query type booleans.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string|array $query Optional query.
	 */
	function parse_query( $query =  '' ) {
		if ( ! empty( $query ) ) {
			$this->init();
			$this->query = $this->query_vars = wp_parse_args( $query );
		} elseif ( ! isset( $this->query ) ) {
			$this->query = $this->query_vars;
		}

		$this->query_vars = $this->fill_query_vars($this->query_vars);
		$qv = &$this->query_vars;
		$this->query_vars_changed = true;

		if ( ! empty($qv['robots']) )
			$this->is_robots = true;

		$qv['p'] =  absint($qv['p']);
		$qv['page_id'] =  absint($qv['page_id']);
		$qv['year'] = absint($qv['year']);
		$qv['monthnum'] = absint($qv['monthnum']);
		$qv['day'] = absint($qv['day']);
		$qv['w'] = absint($qv['w']);
		$qv['m'] = preg_replace( '|[^0-9]|', '', $qv['m'] );
		$qv['paged'] = absint($qv['paged']);
		$qv['cat'] = preg_replace( '|[^0-9,-]|', '', $qv['cat'] ); // comma separated list of positive or negative integers
		$qv['author'] = preg_replace( '|[^0-9,-]|', '', $qv['author'] ); // comma separated list of positive or negative integers
		$qv['pagename'] = trim( $qv['pagename'] );
		$qv['name'] = trim( $qv['name'] );
		if ( '' !== $qv['hour'] ) $qv['hour'] = absint($qv['hour']);
		if ( '' !== $qv['minute'] ) $qv['minute'] = absint($qv['minute']);
		if ( '' !== $qv['second'] ) $qv['second'] = absint($qv['second']);
		if ( '' !== $qv['menu_order'] ) $qv['menu_order'] = absint($qv['menu_order']);

		// Fairly insane upper bound for search string lengths.
		if ( ! empty( $qv['s'] ) && strlen( $qv['s'] ) > 1600 )
			$qv['s'] = '';

		// Compat. Map subpost to attachment.
		if ( '' != $qv['subpost'] )
			$qv['attachment'] = $qv['subpost'];
		if ( '' != $qv['subpost_id'] )
			$qv['attachment_id'] = $qv['subpost_id'];

		$qv['attachment_id'] = absint($qv['attachment_id']);

		if ( ('' != $qv['attachment']) || !empty($qv['attachment_id']) ) {
			$this->is_single = true;
			$this->is_attachment = true;
		} elseif ( '' != $qv['name'] ) {
			$this->is_single = true;
		} elseif ( $qv['p'] ) {
			$this->is_single = true;
		} elseif ( ('' !== $qv['hour']) && ('' !== $qv['minute']) &&('' !== $qv['second']) && ('' != $qv['year']) && ('' != $qv['monthnum']) && ('' != $qv['day']) ) {
			// If year, month, day, hour, minute, and second are set, a single
			// post is being queried.
			$this->is_single = true;
		} elseif ( '' != $qv['static'] || '' != $qv['pagename'] || !empty($qv['page_id']) ) {
			$this->is_page = true;
			$this->is_single = false;
		} else {
		// Look for archive queries. Dates, categories, authors, search, post type archives.

			if ( !empty($qv['s']) ) {
				$this->is_search = true;
			}

			if ( '' !== $qv['second'] ) {
				$this->is_time = true;
				$this->is_date = true;
			}

			if ( '' !== $qv['minute'] ) {
				$this->is_time = true;
				$this->is_date = true;
			}

			if ( '' !== $qv['hour'] ) {
				$this->is_time = true;
				$this->is_date = true;
			}

			if ( $qv['day'] ) {
				if ( ! $this->is_date ) {
					$date = sprintf( '%04d-%02d-%02d', $qv['year'], $qv['monthnum'], $qv['day'] );
					if ( $qv['monthnum'] && $qv['year'] && ! wp_checkdate( $qv['monthnum'], $qv['day'], $qv['year'], $date ) ) {
						$qv['error'] = '404';
					} else {
						$this->is_day = true;
						$this->is_date = true;
					}
				}
			}

			if ( $qv['monthnum'] ) {
				if ( ! $this->is_date ) {
					if ( 12 < $qv['monthnum'] ) {
						$qv['error'] = '404';
					} else {
						$this->is_month = true;
						$this->is_date = true;
					}
				}
			}

			if ( $qv['year'] ) {
				if ( ! $this->is_date ) {
					$this->is_year = true;
					$this->is_date = true;
				}
			}

			if ( $qv['m'] ) {
				$this->is_date = true;
				if ( strlen($qv['m']) > 9 ) {
					$this->is_time = true;
				} else if ( strlen($qv['m']) > 7 ) {
					$this->is_day = true;
				} else if ( strlen($qv['m']) > 5 ) {
					$this->is_month = true;
				} else {
					$this->is_year = true;
				}
			}

			if ( '' != $qv['w'] ) {
				$this->is_date = true;
			}

			$this->query_vars_hash = false;
			$this->parse_tax_query( $qv );

			foreach ( $this->tax_query->queries as $tax_query ) {
				if ( 'NOT IN' != $tax_query['operator'] ) {
					switch ( $tax_query['taxonomy'] ) {
						case 'category':
							$this->is_category = true;
							break;
						case 'post_tag':
							$this->is_tag = true;
							break;
						default:
							$this->is_tax = true;
					}
				}
			}
			unset( $tax_query );

			if ( empty($qv['author']) || ($qv['author'] == '0') ) {
				$this->is_author = false;
			} else {
				$this->is_author = true;
			}

			if ( '' != $qv['author_name'] )
				$this->is_author = true;

			if ( !empty( $qv['post_type'] ) && ! is_array( $qv['post_type'] ) ) {
				$post_type_obj = get_post_type_object( $qv['post_type'] );
				if ( ! empty( $post_type_obj->has_archive ) )
					$this->is_post_type_archive = true;
			}

			if ( $this->is_post_type_archive || $this->is_date || $this->is_author || $this->is_category || $this->is_tag || $this->is_tax )
				$this->is_archive = true;
		}

		if ( '' != $qv['feed'] )
			$this->is_feed = true;

		if ( '' != $qv['tb'] )
			$this->is_trackback = true;

		if ( '' != $qv['paged'] && ( intval($qv['paged']) > 1 ) )
			$this->is_paged = true;

		if ( '' != $qv['comments_popup'] )
			$this->is_comments_popup = true;

		// if we're previewing inside the write screen
		if ( '' != $qv['preview'] )
			$this->is_preview = true;

		if ( is_admin() )
			$this->is_admin = true;

		if ( false !== strpos($qv['feed'], 'comments-') ) {
			$qv['feed'] = str_replace('comments-', '', $qv['feed']);
			$qv['withcomments'] = 1;
		}

		$this->is_singular = $this->is_single || $this->is_page || $this->is_attachment;

		if ( $this->is_feed && ( !empty($qv['withcomments']) || ( empty($qv['withoutcomments']) && $this->is_singular ) ) )
			$this->is_comment_feed = true;

		if ( !( $this->is_singular || $this->is_archive || $this->is_search || $this->is_feed || $this->is_trackback || $this->is_404 || $this->is_admin || $this->is_comments_popup || $this->is_robots ) )
			$this->is_home = true;

		// Correct is_* for page_on_front and page_for_posts
		if ( $this->is_home && 'page' == get_option('show_on_front') && get_option('page_on_front') ) {
			$_query = wp_parse_args($this->query);
			// pagename can be set and empty depending on matched rewrite rules. Ignore an empty pagename.
			if ( isset($_query['pagename']) && '' == $_query['pagename'] )
				unset($_query['pagename']);
			if ( empty($_query) || !array_diff( array_keys($_query), array('preview', 'page', 'paged', 'cpage') ) ) {
				$this->is_page = true;
				$this->is_home = false;
				$qv['page_id'] = get_option('page_on_front');
				// Correct <!--nextpage--> for page_on_front
				if ( !empty($qv['paged']) ) {
					$qv['page'] = $qv['paged'];
					unset($qv['paged']);
				}
			}
		}

		if ( '' != $qv['pagename'] ) {
			$this->queried_object = get_page_by_path($qv['pagename']);
			if ( !empty($this->queried_object) )
				$this->queried_object_id = (int) $this->queried_object->ID;
			else
				unset($this->queried_object);

			if  ( 'page' == get_option('show_on_front') && isset($this->queried_object_id) && $this->queried_object_id == get_option('page_for_posts') ) {
				$this->is_page = false;
				$this->is_home = true;
				$this->is_posts_page = true;
			}
		}

		if ( $qv['page_id'] ) {
			if  ( 'page' == get_option('show_on_front') && $qv['page_id'] == get_option('page_for_posts') ) {
				$this->is_page = false;
				$this->is_home = true;
				$this->is_posts_page = true;
			}
		}

		if ( !empty($qv['post_type']) ) {
			if ( is_array($qv['post_type']) )
				$qv['post_type'] = array_map('sanitize_key', $qv['post_type']);
			else
				$qv['post_type'] = sanitize_key($qv['post_type']);
		}

		if ( ! empty( $qv['post_status'] ) ) {
			if ( is_array( $qv['post_status'] ) )
				$qv['post_status'] = array_map('sanitize_key', $qv['post_status']);
			else
				$qv['post_status'] = preg_replace('|[^a-z0-9_,-]|', '', $qv['post_status']);
		}

		if ( $this->is_posts_page && ( ! isset($qv['withcomments']) || ! $qv['withcomments'] ) )
			$this->is_comment_feed = false;

		$this->is_singular = $this->is_single || $this->is_page || $this->is_attachment;
		// Done correcting is_* for page_on_front and page_for_posts

		if ( '404' == $qv['error'] )
			$this->set_404();

		$this->query_vars_hash = md5( serialize( $this->query_vars ) );
		$this->query_vars_changed = false;

		do_action_ref_array('parse_query', array(&$this));
	}

	/*
	 * Parses various taxonomy related query vars.
	 *
	 * @access protected
	 * @since 3.1.0
	 *
	 * @param array &$q The query variables
	 */
	function parse_tax_query( &$q ) {
		if ( ! empty( $q['tax_query'] ) && is_array( $q['tax_query'] ) ) {
			$tax_query = $q['tax_query'];
		} else {
			$tax_query = array();
		}

		if ( !empty($q['taxonomy']) && !empty($q['term']) ) {
			$tax_query[] = array(
				'taxonomy' => $q['taxonomy'],
				'terms' => array( $q['term'] ),
				'field' => 'slug',
			);
		}

		foreach ( get_taxonomies( array() , 'objects' ) as $taxonomy => $t ) {
			if ( 'post_tag' == $taxonomy )
				continue;	// Handled further down in the $q['tag'] block

			if ( $t->query_var && !empty( $q[$t->query_var] ) ) {
				$tax_query_defaults = array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
				);

 				if ( isset( $t->rewrite['hierarchical'] ) && $t->rewrite['hierarchical'] ) {
					$q[$t->query_var] = wp_basename( $q[$t->query_var] );
				}

				$term = $q[$t->query_var];

				if ( strpos($term, '+') !== false ) {
					$terms = preg_split( '/[+]+/', $term );
					foreach ( $terms as $term ) {
						$tax_query[] = array_merge( $tax_query_defaults, array(
							'terms' => array( $term )
						) );
					}
				} else {
					$tax_query[] = array_merge( $tax_query_defaults, array(
						'terms' => preg_split( '/[,]+/', $term )
					) );
				}
			}
		}

		// Category stuff
		if ( !empty($q['cat']) && '0' != $q['cat'] && !$this->is_singular && $this->query_vars_changed ) {
			$q['cat'] = ''.urldecode($q['cat']).'';
			$q['cat'] = addslashes_gpc($q['cat']);
			$cat_array = preg_split('/[,\s]+/', $q['cat']);
			$q['cat'] = '';
			$req_cats = array();
			foreach ( (array) $cat_array as $cat ) {
				$cat = intval($cat);
				$req_cats[] = $cat;
				$in = ($cat > 0);
				$cat = abs($cat);
				if ( $in ) {
					$q['category__in'][] = $cat;
					$q['category__in'] = array_merge( $q['category__in'], get_term_children($cat, 'category') );
				} else {
					$q['category__not_in'][] = $cat;
					$q['category__not_in'] = array_merge( $q['category__not_in'], get_term_children($cat, 'category') );
				}
			}
			$q['cat'] = implode(',', $req_cats);
		}

		if ( ! empty( $q['category__and'] ) && 1 === count( (array) $q['category__and'] ) ) {
			$q['category__and'] = (array) $q['category__and'];
			if ( ! isset( $q['category__in'] ) )
				$q['category__in'] = array();
			$q['category__in'][] = absint( reset( $q['category__and'] ) );
			unset( $q['category__and'] );
		}

		if ( ! empty( $q['category__in'] ) ) {
			$q['category__in'] = array_map( 'absint', array_unique( (array) $q['category__in'] ) );
			$tax_query[] = array(
				'taxonomy' => 'category',
				'terms' => $q['category__in'],
				'field' => 'term_id',
				'include_children' => false
			);
		}

		if ( ! empty($q['category__not_in']) ) {
			$q['category__not_in'] = array_map( 'absint', array_unique( (array) $q['category__not_in'] ) );
			$tax_query[] = array(
				'taxonomy' => 'category',
				'terms' => $q['category__not_in'],
				'operator' => 'NOT IN',
				'include_children' => false
			);
		}

		if ( ! empty($q['category__and']) ) {
			$q['category__and'] = array_map( 'absint', array_unique( (array) $q['category__and'] ) );
			$tax_query[] = array(
				'taxonomy' => 'category',
				'terms' => $q['category__and'],
				'field' => 'term_id',
				'operator' => 'AND',
				'include_children' => false
			);
		}

		// Tag stuff
		if ( '' != $q['tag'] && !$this->is_singular && $this->query_vars_changed ) {
			if ( strpos($q['tag'], ',') !== false ) {
				$tags = preg_split('/[,\r\n\t ]+/', $q['tag']);
				foreach ( (array) $tags as $tag ) {
					$tag = sanitize_term_field('slug', $tag, 0, 'post_tag', 'db');
					$q['tag_slug__in'][] = $tag;
				}
			} else if ( preg_match('/[+\r\n\t ]+/', $q['tag']) || !empty($q['cat']) ) {
				$tags = preg_split('/[+\r\n\t ]+/', $q['tag']);
				foreach ( (array) $tags as $tag ) {
					$tag = sanitize_term_field('slug', $tag, 0, 'post_tag', 'db');
					$q['tag_slug__and'][] = $tag;
				}
			} else {
				$q['tag'] = sanitize_term_field('slug', $q['tag'], 0, 'post_tag', 'db');
				$q['tag_slug__in'][] = $q['tag'];
			}
		}

		if ( !empty($q['tag_id']) ) {
			$q['tag_id'] = absint( $q['tag_id'] );
			$tax_query[] = array(
				'taxonomy' => 'post_tag',
				'terms' => $q['tag_id']
			);
		}

		if ( !empty($q['tag__in']) ) {
			$q['tag__in'] = array_map('absint', array_unique( (array) $q['tag__in'] ) );
			$tax_query[] = array(
				'taxonomy' => 'post_tag',
				'terms' => $q['tag__in']
			);
		}

		if ( !empty($q['tag__not_in']) ) {
			$q['tag__not_in'] = array_map('absint', array_unique( (array) $q['tag__not_in'] ) );
			$tax_query[] = array(
				'taxonomy' => 'post_tag',
				'terms' => $q['tag__not_in'],
				'operator' => 'NOT IN'
			);
		}

		if ( !empty($q['tag__and']) ) {
			$q['tag__and'] = array_map('absint', array_unique( (array) $q['tag__and'] ) );
			$tax_query[] = array(
				'taxonomy' => 'post_tag',
				'terms' => $q['tag__and'],
				'operator' => 'AND'
			);
		}

		if ( !empty($q['tag_slug__in']) ) {
			$q['tag_slug__in'] = array_map('sanitize_title_for_query', array_unique( (array) $q['tag_slug__in'] ) );
			$tax_query[] = array(
				'taxonomy' => 'post_tag',
				'terms' => $q['tag_slug__in'],
				'field' => 'slug'
			);
		}

		if ( !empty($q['tag_slug__and']) ) {
			$q['tag_slug__and'] = array_map('sanitize_title_for_query', array_unique( (array) $q['tag_slug__and'] ) );
			$tax_query[] = array(
				'taxonomy' => 'post_tag',
				'terms' => $q['tag_slug__and'],
				'field' => 'slug',
				'operator' => 'AND'
			);
		}

		$this->tax_query = new WP_Tax_Query( $tax_query );

		do_action( 'parse_tax_query', $this );
	}

	/**
	 * Generate SQL for the WHERE clause based on passed search terms.
	 *
	 * @since 3.7.0
	 *
	 * @global type $wpdb
	 * @param array $q Query variables.
	 */
	protected function parse_search( &$q ) {
		global $wpdb;

		$search = '';

		// added slashes screw with quote grouping when done early, so done later
		$q['s'] = stripslashes( $q['s'] );
		if ( empty( $_GET['s'] ) && $this->is_main_query() )
			$q['s'] = urldecode( $q['s'] );
		// there are no line breaks in <input /> fields
		$q['s'] = str_replace( array( "\r", "\n" ), '', $q['s'] );
		$q['search_terms_count'] = 1;
		if ( ! empty( $q['sentence'] ) ) {
			$q['search_terms'] = array( $q['s'] );
		} else {
			if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $q['s'], $matches ) ) {
				$q['search_terms_count'] = count( $matches[0] );
				$q['search_terms'] = $this->parse_search_terms( $matches[0] );
				// if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence
				if ( empty( $q['search_terms'] ) || count( $q['search_terms'] ) > 9 )
					$q['search_terms'] = array( $q['s'] );
			} else {
				$q['search_terms'] = array( $q['s'] );
			}
		}

		$n = ! empty( $q['exact'] ) ? '' : '%';
		$searchand = '';
		$q['search_orderby_title'] = array();
		foreach ( $q['search_terms'] as $term ) {
			$term = like_escape( esc_sql( $term ) );
			if ( $n )
				$q['search_orderby_title'][] = "$wpdb->posts.post_title LIKE '%$term%'";

			$search .= "{$searchand}(($wpdb->posts.post_title LIKE '{$n}{$term}{$n}') OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}'))";
			$searchand = ' AND ';
		}

		if ( ! empty( $search ) ) {
			$search = " AND ({$search}) ";
			if ( ! is_user_logged_in() )
				$search .= " AND ($wpdb->posts.post_password = '') ";
		}

		return $search;
	}

	/**
	 * Check if the terms are suitable for searching.
	 *
	 * Uses an array of stopwords (terms) that are excluded from the separate
	 * term matching when searching for posts. The list of English stopwords is
	 * the approximate search engines list, and is translatable.
	 *
	 * @since 3.7.0
	 *
	 * @param array Terms to check.
	 * @return array Terms that are not stopwords.
	 */
	protected function parse_search_terms( $terms ) {
		$strtolower = function_exists( 'mb_strtolower' ) ? 'mb_strtolower' : 'strtolower';
		$checked = array();

		$stopwords = $this->get_search_stopwords();

		foreach ( $terms as $term ) {
			// keep before/after spaces when term is for exact match
			if ( preg_match( '/^".+"$/', $term ) )
				$term = trim( $term, "\"'" );
			else
				$term = trim( $term, "\"' " );

			// Avoid single A-Z.
			if ( ! $term || ( 1 === strlen( $term ) && preg_match( '/^[a-z]$/i', $term ) ) )
				continue;

			if ( in_array( call_user_func( $strtolower, $term ), $stopwords, true ) )
				continue;

			$checked[] = $term;
		}

		return $checked;
	}

	/**
	 * Retrieve stopwords used when parsing search terms.
	 *
	 * @since 3.7.0
	 *
	 * @return array Stopwords.
	 */
	protected function get_search_stopwords() {
		if ( isset( $this->stopwords ) )
			return $this->stopwords;

		/* translators: This is a comma-separated list of very common words that should be excluded from a search,
		 * like a, an, and the. These are usually called "stopwords". You should not simply translate these individual
		 * words into your language. Instead, look for and provide commonly accepted stopwords in your language.
		 */
		$words = explode( ',', _x( 'about,an,are,as,at,be,by,com,for,from,how,in,is,it,of,on,or,that,the,this,to,was,what,when,where,who,will,with,www',
			'Comma-separated list of search stopwords in your language' ) );

		foreach( $words as $word ) {
			$word = trim( $word, "\r\n\t " );
			if ( $word )
				$stopwords[] = $word;
		}

		/**
		 * Filter stopwords used when parsing search terms.
		 *
		 * @since 3.7.0
		 *
		 * @param array $stopwords Stopwords.
		 */
		$this->stopwords = apply_filters( 'wp_search_stopwords', $stopwords );
		return $this->stopwords;
	}

	/**
	 * Generate SQL for the ORDER BY condition based on passed search terms.
	 *
	 * @global wpdb $wpdb
	 * @param array $q Query variables.
	 * @return string ORDER BY clause.
	 */
	protected function parse_search_order( &$q ) {
		global $wpdb;

		$search_orderby = '';

		if ( $q['search_terms_count'] > 1 ) {
			$num_terms = count( $q['search_orderby_title'] );
			$search_orderby_s = like_escape( esc_sql( $q['s'] ) );

			$search_orderby = '(CASE ';
			// sentence match in 'post_title'
			$search_orderby .= "WHEN $wpdb->posts.post_title LIKE '%{$search_orderby_s}%' THEN 1 ";

			// sanity limit, sort as sentence when more than 6 terms
			// (few searches are longer than 6 terms and most titles are not)
			if ( $num_terms < 7 ) {
				// all words in title
				$search_orderby .= 'WHEN ' . implode( ' AND ', $q['search_orderby_title'] ) . ' THEN 2 ';
				// any word in title, not needed when $num_terms == 1
				if ( $num_terms > 1 )
					$search_orderby .= 'WHEN ' . implode( ' OR ', $q['search_orderby_title'] ) . ' THEN 3 ';
			}

			// sentence match in 'post_content'
			$search_orderby .= "WHEN $wpdb->posts.post_content LIKE '%{$search_orderby_s}%' THEN 4 ";
			$search_orderby .= 'ELSE 5 END)';
		} else {
			// single word or sentence search
			$search_orderby = reset( $q['search_orderby_title'] ) . ' DESC';
		}

		return $search_orderby;
	}

	/**
	 * Sets the 404 property and saves whether query is feed.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	function set_404() {
		$is_feed = $this->is_feed;

		$this->init_query_flags();
		$this->is_404 = true;

		$this->is_feed = $is_feed;
	}

	/**
	 * Retrieve query variable.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $query_var Query variable key.
	 * @return mixed
	 */
	function get($query_var) {
		if ( isset($this->query_vars[$query_var]) )
			return $this->query_vars[$query_var];

		return '';
	}

	/**
	 * Set query variable.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $query_var Query variable key.
	 * @param mixed $value Query variable value.
	 */
	function set($query_var, $value) {
		$this->query_vars[$query_var] = $value;
	}

	/**
	 * Retrieve the posts based on query variables.
	 *
	 * There are a few filters and actions that can be used to modify the post
	 * database query.
	 *
	 * @since 1.5.0
	 * @access public
	 * @uses do_action_ref_array() Calls 'pre_get_posts' hook before retrieving posts.
	 *
	 * @return array List of posts.
	 */
	function get_posts() {
		global $wpdb;

		$this->parse_query();

		do_action_ref_array('pre_get_posts', array(&$this));

		// Shorthand.
		$q = &$this->query_vars;

		// Fill again in case pre_get_posts unset some vars.
		$q = $this->fill_query_vars($q);

		// Parse meta query
		$this->meta_query = new WP_Meta_Query();
		$this->meta_query->parse_query_vars( $q );

		// Set a flag if a pre_get_posts hook changed the query vars.
		$hash = md5( serialize( $this->query_vars ) );
		if ( $hash != $this->query_vars_hash ) {
			$this->query_vars_changed = true;
			$this->query_vars_hash = $hash;
		}
		unset($hash);

		// First let's clear some variables
		$distinct = '';
		$whichauthor = '';
		$whichmimetype = '';
		$where = '';
		$limits = '';
		$join = '';
		$search = '';
		$groupby = '';
		$fields = '';
		$post_status_join = false;
		$page = 1;

		if ( isset( $q['caller_get_posts'] ) ) {
			_deprecated_argument( 'WP_Query', '3.1', __( '"caller_get_posts" is deprecated. Use "ignore_sticky_posts" instead.' ) );
			if ( !isset( $q['ignore_sticky_posts'] ) )
				$q['ignore_sticky_posts'] = $q['caller_get_posts'];
		}

		if ( !isset( $q['ignore_sticky_posts'] ) )
			$q['ignore_sticky_posts'] = false;

		if ( !isset($q['suppress_filters']) )
			$q['suppress_filters'] = false;

		if ( !isset($q['cache_results']) ) {
			if ( wp_using_ext_object_cache() )
				$q['cache_results'] = false;
			else
				$q['cache_results'] = true;
		}

		if ( !isset($q['update_post_term_cache']) )
			$q['update_post_term_cache'] = true;

		if ( !isset($q['update_post_meta_cache']) )
			$q['update_post_meta_cache'] = true;

		if ( !isset($q['post_type']) ) {
			if ( $this->is_search )
				$q['post_type'] = 'any';
			else
				$q['post_type'] = '';
		}
		$post_type = $q['post_type'];
		if ( !isset($q['posts_per_page']) || $q['posts_per_page'] == 0 )
			$q['posts_per_page'] = get_option('posts_per_page');
		if ( isset($q['showposts']) && $q['showposts'] ) {
			$q['showposts'] = (int) $q['showposts'];
			$q['posts_per_page'] = $q['showposts'];
		}
		if ( (isset($q['posts_per_archive_page']) && $q['posts_per_archive_page'] != 0) && ($this->is_archive || $this->is_search) )
			$q['posts_per_page'] = $q['posts_per_archive_page'];
		if ( !isset($q['nopaging']) ) {
			if ( $q['posts_per_page'] == -1 ) {
				$q['nopaging'] = true;
			} else {
				$q['nopaging'] = false;
			}
		}
		if ( $this->is_feed ) {
			$q['posts_per_page'] = get_option('posts_per_rss');
			$q['nopaging'] = false;
		}
		$q['posts_per_page'] = (int) $q['posts_per_page'];
		if ( $q['posts_per_page'] < -1 )
			$q['posts_per_page'] = abs($q['posts_per_page']);
		else if ( $q['posts_per_page'] == 0 )
			$q['posts_per_page'] = 1;

		if ( !isset($q['comments_per_page']) || $q['comments_per_page'] == 0 )
			$q['comments_per_page'] = get_option('comments_per_page');

		if ( $this->is_home && (empty($this->query) || $q['preview'] == 'true') && ( 'page' == get_option('show_on_front') ) && get_option('page_on_front') ) {
			$this->is_page = true;
			$this->is_home = false;
			$q['page_id'] = get_option('page_on_front');
		}

		if ( isset($q['page']) ) {
			$q['page'] = trim($q['page'], '/');
			$q['page'] = absint($q['page']);
		}

		// If true, forcibly turns off SQL_CALC_FOUND_ROWS even when limits are present.
		if ( isset($q['no_found_rows']) )
			$q['no_found_rows'] = (bool) $q['no_found_rows'];
		else
			$q['no_found_rows'] = false;

		switch ( $q['fields'] ) {
			case 'ids':
				$fields = "$wpdb->posts.ID";
				break;
			case 'id=>parent':
				$fields = "$wpdb->posts.ID, $wpdb->posts.post_parent";
				break;
			default:
				$fields = "$wpdb->posts.*";
		}

		if ( '' !== $q['menu_order'] )
			$where .= " AND $wpdb->posts.menu_order = " . $q['menu_order'];

		// The "m" parameter is meant for months but accepts datetimes of varying specificity
		if ( $q['m'] ) {
			$where .= " AND YEAR($wpdb->posts.post_date)=" . substr($q['m'], 0, 4);
			if ( strlen($q['m']) > 5 )
				$where .= " AND MONTH($wpdb->posts.post_date)=" . substr($q['m'], 4, 2);
			if ( strlen($q['m']) > 7 )
				$where .= " AND DAYOFMONTH($wpdb->posts.post_date)=" . substr($q['m'], 6, 2);
			if ( strlen($q['m']) > 9 )
				$where .= " AND HOUR($wpdb->posts.post_date)=" . substr($q['m'], 8, 2);
			if ( strlen($q['m']) > 11 )
				$where .= " AND MINUTE($wpdb->posts.post_date)=" . substr($q['m'], 10, 2);
			if ( strlen($q['m']) > 13 )
				$where .= " AND SECOND($wpdb->posts.post_date)=" . substr($q['m'], 12, 2);
		}

		// Handle the other individual date parameters
		$date_parameters = array();

		if ( '' !== $q['hour'] )
			$date_parameters['hour'] = $q['hour'];

		if ( '' !== $q['minute'] )
			$date_parameters['minute'] = $q['minute'];

		if ( '' !== $q['second'] )
			$date_parameters['second'] = $q['second'];

		if ( $q['year'] )
			$date_parameters['year'] = $q['year'];

		if ( $q['monthnum'] )
			$date_parameters['monthnum'] = $q['monthnum'];

		if ( $q['w'] )
			$date_parameters['week'] = $q['w'];

		if ( $q['day'] )
			$date_parameters['day'] = $q['day'];

		if ( $date_parameters ) {
			$date_query = new WP_Date_Query( array( $date_parameters ) );
			$where .= $date_query->get_sql();
		}
		unset( $date_parameters, $date_query );

		// Handle complex date queries
		if ( ! empty( $q['date_query'] ) ) {
			$this->date_query = new WP_Date_Query( $q['date_query'] );
			$where .= $this->date_query->get_sql();
		}


		// If we've got a post_type AND it's not "any" post_type.
		if ( !empty($q['post_type']) && 'any' != $q['post_type'] ) {
			foreach ( (array)$q['post_type'] as $_post_type ) {
				$ptype_obj = get_post_type_object($_post_type);
				if ( !$ptype_obj || !$ptype_obj->query_var || empty($q[ $ptype_obj->query_var ]) )
					continue;

				if ( ! $ptype_obj->hierarchical || strpos($q[ $ptype_obj->query_var ], '/') === false ) {
					// Non-hierarchical post_types & parent-level-hierarchical post_types can directly use 'name'
					$q['name'] = $q[ $ptype_obj->query_var ];
				} else {
					// Hierarchical post_types will operate through the
					$q['pagename'] = $q[ $ptype_obj->query_var ];
					$q['name'] = '';
				}

				// Only one request for a slug is possible, this is why name & pagename are overwritten above.
				break;
			} //end foreach
			unset($ptype_obj);
		}

		if ( '' != $q['name'] ) {
			$q['name'] = sanitize_title_for_query( $q['name'] );
			$where .= " AND $wpdb->posts.post_name = '" . $q['name'] . "'";
		} elseif ( '' != $q['pagename'] ) {
			if ( isset($this->queried_object_id) ) {
				$reqpage = $this->queried_object_id;
			} else {
				if ( 'page' != $q['post_type'] ) {
					foreach ( (array)$q['post_type'] as $_post_type ) {
						$ptype_obj = get_post_type_object($_post_type);
						if ( !$ptype_obj || !$ptype_obj->hierarchical )
							continue;

						$reqpage = get_page_by_path($q['pagename'], OBJECT, $_post_type);
						if ( $reqpage )
							break;
					}
					unset($ptype_obj);
				} else {
					$reqpage = get_page_by_path($q['pagename']);
				}
				if ( !empty($reqpage) )
					$reqpage = $reqpage->ID;
				else
					$reqpage = 0;
			}

			$page_for_posts = get_option('page_for_posts');
			if  ( ('page' != get_option('show_on_front') ) || empty($page_for_posts) || ( $reqpage != $page_for_posts ) ) {
				$q['pagename'] = sanitize_title_for_query( wp_basename( $q['pagename'] ) );
				$q['name'] = $q['pagename'];
				$where .= " AND ($wpdb->posts.ID = '$reqpage')";
				$reqpage_obj = get_post( $reqpage );
				if ( is_object($reqpage_obj) && 'attachment' == $reqpage_obj->post_type ) {
					$this->is_attachment = true;
					$post_type = $q['post_type'] = 'attachment';
					$this->is_page = true;
					$q['attachment_id'] = $reqpage;
				}
			}
		} elseif ( '' != $q['attachment'] ) {
			$q['attachment'] = sanitize_title_for_query( wp_basename( $q['attachment'] ) );
			$q['name'] = $q['attachment'];
			$where .= " AND $wpdb->posts.post_name = '" . $q['attachment'] . "'";
		}


		if ( intval($q['comments_popup']) )
			$q['p'] = absint($q['comments_popup']);

		// If an attachment is requested by number, let it supersede any post number.
		if ( $q['attachment_id'] )
			$q['p'] = absint($q['attachment_id']);

		// If a post number is specified, load that post
		if ( $q['p'] ) {
			$where .= " AND {$wpdb->posts}.ID = " . $q['p'];
		} elseif ( $q['post__in'] ) {
			$post__in = implode(',', array_map( 'absint', $q['post__in'] ));
			$where .= " AND {$wpdb->posts}.ID IN ($post__in)";
		} elseif ( $q['post__not_in'] ) {
			$post__not_in = implode(',',  array_map( 'absint', $q['post__not_in'] ));
			$where .= " AND {$wpdb->posts}.ID NOT IN ($post__not_in)";
		}

		if ( is_numeric( $q['post_parent'] ) ) {
			$where .= $wpdb->prepare( " AND $wpdb->posts.post_parent = %d ", $q['post_parent'] );
		} elseif ( $q['post_parent__in'] ) {
			$post_parent__in = implode( ',', array_map( 'absint', $q['post_parent__in'] ) );
			$where .= " AND {$wpdb->posts}.post_parent IN ($post_parent__in)";
		} elseif ( $q['post_parent__not_in'] ) {
			$post_parent__not_in = implode( ',',  array_map( 'absint', $q['post_parent__not_in'] ) );
			$where .= " AND {$wpdb->posts}.post_parent NOT IN ($post_parent__not_in)";
		}

		if ( $q['page_id'] ) {
			if  ( ('page' != get_option('show_on_front') ) || ( $q['page_id'] != get_option('page_for_posts') ) ) {
				$q['p'] = $q['page_id'];
				$where = " AND {$wpdb->posts}.ID = " . $q['page_id'];
			}
		}

		// If a search pattern is specified, load the posts that match.
		if ( ! empty( $q['s'] ) )
			$search = $this->parse_search( $q );

		/**
		 * Filter the search SQL that is used in the WHERE clause of WP_Query.
		 *
		 * @since 3.0.0
		 *
		 * @param string   $search Search SQL for WHERE clause.
		 * @param WP_Query $this   The current WP_Query object.
		 */
		$search = apply_filters_ref_array( 'posts_search', array( $search, &$this ) );

		// Taxonomies
		if ( !$this->is_singular ) {
			$this->parse_tax_query( $q );

			$clauses = $this->tax_query->get_sql( $wpdb->posts, 'ID' );

			$join .= $clauses['join'];
			$where .= $clauses['where'];
		}

		if ( $this->is_tax ) {
			if ( empty($post_type) ) {
				// Do a fully inclusive search for currently registered post types of queried taxonomies
				$post_type = array();
				$taxonomies = wp_list_pluck( $this->tax_query->queries, 'taxonomy' );
				foreach ( get_post_types( array( 'exclude_from_search' => false ) ) as $pt ) {
					$object_taxonomies = $pt === 'attachment' ? get_taxonomies_for_attachments() : get_object_taxonomies( $pt );
					if ( array_intersect( $taxonomies, $object_taxonomies ) )
						$post_type[] = $pt;
				}
				if ( ! $post_type )
					$post_type = 'any';
				elseif ( count( $post_type ) == 1 )
					$post_type = $post_type[0];

				$post_status_join = true;
			} elseif ( in_array('attachment', (array) $post_type) ) {
				$post_status_join = true;
			}
		}

		// Back-compat
		if ( !empty($this->tax_query->queries) ) {
			$tax_query_in_and = wp_list_filter( $this->tax_query->queries, array( 'operator' => 'NOT IN' ), 'NOT' );
			if ( !empty( $tax_query_in_and ) ) {
				if ( !isset( $q['taxonomy'] ) ) {
					foreach ( $tax_query_in_and as $a_tax_query ) {
						if ( !in_array( $a_tax_query['taxonomy'], array( 'category', 'post_tag' ) ) ) {
							$q['taxonomy'] = $a_tax_query['taxonomy'];
							if ( 'slug' == $a_tax_query['field'] )
								$q['term'] = $a_tax_query['terms'][0];
							else
								$q['term_id'] = $a_tax_query['terms'][0];

							break;
						}
					}
				}

				$cat_query = wp_list_filter( $tax_query_in_and, array( 'taxonomy' => 'category' ) );
				if ( ! empty( $cat_query ) ) {
					$cat_query = reset( $cat_query );

					if ( ! empty( $cat_query['terms'][0] ) ) {
						$the_cat = get_term_by( $cat_query['field'], $cat_query['terms'][0], 'category' );
						if ( $the_cat ) {
							$this->set( 'cat', $the_cat->term_id );
							$this->set( 'category_name', $the_cat->slug );
						}
						unset( $the_cat );
					}
				}
				unset( $cat_query );

				$tag_query = wp_list_filter( $tax_query_in_and, array( 'taxonomy' => 'post_tag' ) );
				if ( ! empty( $tag_query ) ) {
					$tag_query = reset( $tag_query );

					if ( ! empty( $tag_query['terms'][0] ) ) {
						$the_tag = get_term_by( $tag_query['field'], $tag_query['terms'][0], 'post_tag' );
						if ( $the_tag )
							$this->set( 'tag_id', $the_tag->term_id );
						unset( $the_tag );
					}
				}
				unset( $tag_query );
			}
		}

		if ( !empty( $this->tax_query->queries ) || !empty( $this->meta_query->queries ) ) {
			$groupby = "{$wpdb->posts}.ID";
		}

		// Author/user stuff

		if ( ! empty( $q['author'] ) && $q['author'] != '0' ) {
			$q['author'] = addslashes_gpc( '' . urldecode( $q['author'] ) );
			$authors = array_unique( array_map( 'intval', preg_split( '/[,\s]+/', $q['author'] ) ) );
			foreach ( $authors as $author ) {
				$key = $author > 0 ? 'author__in' : 'author__not_in';
				$q[$key][] = abs( $author );
			}
			$q['author'] = implode( ',', $authors );
		}

		if ( ! empty( $q['author__not_in'] ) ) {
			$author__not_in = implode( ',', array_map( 'absint', array_unique( (array) $q['author__not_in'] ) ) );
			$where .= " AND {$wpdb->posts}.post_author NOT IN ($author__not_in) ";
		} elseif ( ! empty( $q['author__in'] ) ) {
			$author__in = implode( ',', array_map( 'absint', array_unique( (array) $q['author__in'] ) ) );
			$where .= " AND {$wpdb->posts}.post_author IN ($author__in) ";
		}

		// Author stuff for nice URLs

		if ( '' != $q['author_name'] ) {
			if ( strpos($q['author_name'], '/') !== false ) {
				$q['author_name'] = explode('/', $q['author_name']);
				if ( $q['author_name'][ count($q['author_name'])-1 ] ) {
					$q['author_name'] = $q['author_name'][count($q['author_name'])-1]; // no trailing slash
				} else {
					$q['author_name'] = $q['author_name'][count($q['author_name'])-2]; // there was a trailing slash
				}
			}
			$q['author_name'] = sanitize_title_for_query( $q['author_name'] );
			$q['author'] = get_user_by('slug', $q['author_name']);
			if ( $q['author'] )
				$q['author'] = $q['author']->ID;
			$whichauthor .= " AND ($wpdb->posts.post_author = " . absint($q['author']) . ')';
		}

		// MIME-Type stuff for attachment browsing

		if ( isset( $q['post_mime_type'] ) && '' != $q['post_mime_type'] )
			$whichmimetype = wp_post_mime_type_where( $q['post_mime_type'], $wpdb->posts );

		$where .= $search . $whichauthor . $whichmimetype;

		if ( empty($q['order']) || ((strtoupper($q['order']) != 'ASC') && (strtoupper($q['order']) != 'DESC')) )
			$q['order'] = 'DESC';

		// Order by
		if ( empty($q['orderby']) ) {
			$orderby = "$wpdb->posts.post_date " . $q['order'];
		} elseif ( 'none' == $q['orderby'] ) {
			$orderby = '';
		} elseif ( $q['orderby'] == 'post__in' && ! empty( $post__in ) ) {
			$orderby = "FIELD( {$wpdb->posts}.ID, $post__in )";
		} elseif ( $q['orderby'] == 'post_parent__in' && ! empty( $post_parent__in ) ) {
			$orderby = "FIELD( {$wpdb->posts}.post_parent, $post_parent__in )";
		} else {
			// Used to filter values
			$allowed_keys = array('name', 'author', 'date', 'title', 'modified', 'menu_order', 'parent', 'ID', 'rand', 'comment_count');
			if ( !empty($q['meta_key']) ) {
				$allowed_keys[] = $q['meta_key'];
				$allowed_keys[] = 'meta_value';
				$allowed_keys[] = 'meta_value_num';
			}
			$q['orderby'] = urldecode($q['orderby']);
			$q['orderby'] = addslashes_gpc($q['orderby']);

			$orderby_array = array();
			foreach ( explode( ' ', $q['orderby'] ) as $i => $orderby ) {
				// Only allow certain values for safety
				if ( ! in_array($orderby, $allowed_keys) )
					continue;

				switch ( $orderby ) {
					case 'menu_order':
						$orderby = "$wpdb->posts.menu_order";
						break;
					case 'ID':
						$orderby = "$wpdb->posts.ID";
						break;
					case 'rand':
						$orderby = 'RAND()';
						break;
					case $q['meta_key']:
					case 'meta_value':
						if ( isset( $q['meta_type'] ) ) {
							$meta_type = $this->meta_query->get_cast_for_type( $q['meta_type'] );
							$orderby = "CAST($wpdb->postmeta.meta_value AS {$meta_type})";
						} else {
							$orderby = "$wpdb->postmeta.meta_value";
						}
						break;
					case 'meta_value_num':
						$orderby = "$wpdb->postmeta.meta_value+0";
						break;
					case 'comment_count':
						$orderby = "$wpdb->posts.comment_count";
						break;
					default:
						$orderby = "$wpdb->posts.post_" . $orderby;
				}

				$orderby_array[] = $orderby;
			}
			$orderby = implode( ',', $orderby_array );

			if ( empty( $orderby ) )
				$orderby = "$wpdb->posts.post_date ".$q['order'];
			else
				$orderby .= " {$q['order']}";
		}

		// Order search results by relevance only when another "orderby" is not specified in the query.
		if ( ! empty( $q['s'] ) ) {
			$search_orderby = '';
			if ( ! empty( $q['search_orderby_title'] ) && ( empty( $q['orderby'] ) && ! $this->is_feed ) || ( isset( $q['orderby'] ) && 'relevance' === $q['orderby'] ) )
				$search_orderby = $this->parse_search_order( $q );

			/**
			 * Filter the ORDER BY used when ordering search results.
			 *
			 * @since 3.7.0
			 *
			 * @param string   $search_orderby The ORDER BY clause.
			 * @param WP_Query $this           The current WP_Query instance.
			 */
			$search_orderby = apply_filters( 'posts_search_orderby', $search_orderby, $this );
			if ( $search_orderby )
				$orderby = $orderby ? $search_orderby . ', ' . $orderby : $search_orderby;
		}

		if ( is_array( $post_type ) && count( $post_type ) > 1 ) {
			$post_type_cap = 'multiple_post_type';
		} else {
			if ( is_array( $post_type ) )
				$post_type = reset( $post_type );
			$post_type_object = get_post_type_object( $post_type );
			if ( empty( $post_type_object ) )
				$post_type_cap = $post_type;
		}

		if ( 'any' == $post_type ) {
			$in_search_post_types = get_post_types( array('exclude_from_search' => false) );
			if ( empty( $in_search_post_types ) )
				$where .= ' AND 1=0 ';
			else
				$where .= " AND $wpdb->posts.post_type IN ('" . join("', '", $in_search_post_types ) . "')";
		} elseif ( !empty( $post_type ) && is_array( $post_type ) ) {
			$where .= " AND $wpdb->posts.post_type IN ('" . join("', '", $post_type) . "')";
		} elseif ( ! empty( $post_type ) ) {
			$where .= " AND $wpdb->posts.post_type = '$post_type'";
			$post_type_object = get_post_type_object ( $post_type );
		} elseif ( $this->is_attachment ) {
			$where .= " AND $wpdb->posts.post_type = 'attachment'";
			$post_type_object = get_post_type_object ( 'attachment' );
		} elseif ( $this->is_page ) {
			$where .= " AND $wpdb->posts.post_type = 'page'";
			$post_type_object = get_post_type_object ( 'page' );
		} else {
			$where .= " AND $wpdb->posts.post_type = 'post'";
			$post_type_object = get_post_type_object ( 'post' );
		}

		$edit_cap = 'edit_post';
		$read_cap = 'read_post';

		if ( ! empty( $post_type_object ) ) {
			$edit_others_cap = $post_type_object->cap->edit_others_posts;
			$read_private_cap = $post_type_object->cap->read_private_posts;
		} else {
			$edit_others_cap = 'edit_others_' . $post_type_cap . 's';
			$read_private_cap = 'read_private_' . $post_type_cap . 's';
		}

		$user_id = get_current_user_id();

		if ( ! empty( $q['post_status'] ) ) {
			$statuswheres = array();
			$q_status = $q['post_status'];
			if ( ! is_array( $q_status ) )
				$q_status = explode(',', $q_status);
			$r_status = array();
			$p_status = array();
			$e_status = array();
			if ( in_array('any', $q_status) ) {
				foreach ( get_post_stati( array('exclude_from_search' => true) ) as $status )
					$e_status[] = "$wpdb->posts.post_status <> '$status'";
			} else {
				foreach ( get_post_stati() as $status ) {
					if ( in_array( $status, $q_status ) ) {
						if ( 'private' == $status )
							$p_status[] = "$wpdb->posts.post_status = '$status'";
						else
							$r_status[] = "$wpdb->posts.post_status = '$status'";
					}
				}
			}

			if ( empty($q['perm'] ) || 'readable' != $q['perm'] ) {
				$r_status = array_merge($r_status, $p_status);
				unset($p_status);
			}

			if ( !empty($e_status) ) {
				$statuswheres[] = "(" . join( ' AND ', $e_status ) . ")";
			}
			if ( !empty($r_status) ) {
				if ( !empty($q['perm'] ) && 'editable' == $q['perm'] && !current_user_can($edit_others_cap) )
					$statuswheres[] = "($wpdb->posts.post_author = $user_id " . "AND (" . join( ' OR ', $r_status ) . "))";
				else
					$statuswheres[] = "(" . join( ' OR ', $r_status ) . ")";
			}
			if ( !empty($p_status) ) {
				if ( !empty($q['perm'] ) && 'readable' == $q['perm'] && !current_user_can($read_private_cap) )
					$statuswheres[] = "($wpdb->posts.post_author = $user_id " . "AND (" . join( ' OR ', $p_status ) . "))";
				else
					$statuswheres[] = "(" . join( ' OR ', $p_status ) . ")";
			}
			if ( $post_status_join ) {
				$join .= " LEFT JOIN $wpdb->posts AS p2 ON ($wpdb->posts.post_parent = p2.ID) ";
				foreach ( $statuswheres as $index => $statuswhere )
					$statuswheres[$index] = "($statuswhere OR ($wpdb->posts.post_status = 'inherit' AND " . str_replace($wpdb->posts, 'p2', $statuswhere) . "))";
			}
			foreach ( $statuswheres as $statuswhere )
				$where .= " AND $statuswhere";
		} elseif ( !$this->is_singular ) {
			$where .= " AND ($wpdb->posts.post_status = 'publish'";

			// Add public states.
			$public_states = get_post_stati( array('public' => true) );
			foreach ( (array) $public_states as $state ) {
				if ( 'publish' == $state ) // Publish is hard-coded above.
					continue;
				$where .= " OR $wpdb->posts.post_status = '$state'";
			}

			if ( $this->is_admin ) {
				// Add protected states that should show in the admin all list.
				$admin_all_states = get_post_stati( array('protected' => true, 'show_in_admin_all_list' => true) );
				foreach ( (array) $admin_all_states as $state )
					$where .= " OR $wpdb->posts.post_status = '$state'";
			}

			if ( is_user_logged_in() ) {
				// Add private states that are limited to viewing by the author of a post or someone who has caps to read private states.
				$private_states = get_post_stati( array('private' => true) );
				foreach ( (array) $private_states as $state )
					$where .= current_user_can( $read_private_cap ) ? " OR $wpdb->posts.post_status = '$state'" : " OR $wpdb->posts.post_author = $user_id AND $wpdb->posts.post_status = '$state'";
			}

			$where .= ')';
		}

		if ( !empty( $this->meta_query->queries ) ) {
			$clauses = $this->meta_query->get_sql( 'post', $wpdb->posts, 'ID', $this );
			$join .= $clauses['join'];
			$where .= $clauses['where'];
		}

		// Apply filters on where and join prior to paging so that any
		// manipulations to them are reflected in the paging by day queries.
		if ( !$q['suppress_filters'] ) {
			$where = apply_filters_ref_array('posts_where', array( $where, &$this ) );
			$join = apply_filters_ref_array('posts_join', array( $join, &$this ) );
		}

		// Paging
		if ( empty($q['nopaging']) && !$this->is_singular ) {
			$page = absint($q['paged']);
			if ( !$page )
				$page = 1;

			if ( empty($q['offset']) ) {
				$pgstrt = ($page - 1) * $q['posts_per_page'] . ', ';
			} else { // we're ignoring $page and using 'offset'
				$q['offset'] = absint($q['offset']);
				$pgstrt = $q['offset'] . ', ';
			}
			$limits = 'LIMIT ' . $pgstrt . $q['posts_per_page'];
		}

		// Comments feeds
		if ( $this->is_comment_feed && ( $this->is_archive || $this->is_search || !$this->is_singular ) ) {
			if ( $this->is_archive || $this->is_search ) {
				$cjoin = "JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) $join ";
				$cwhere = "WHERE comment_approved = '1' $where";
				$cgroupby = "$wpdb->comments.comment_id";
			} else { // Other non singular e.g. front
				$cjoin = "JOIN $wpdb->posts ON ( $wpdb->comments.comment_post_ID = $wpdb->posts.ID )";
				$cwhere = "WHERE post_status = 'publish' AND comment_approved = '1'";
				$cgroupby = '';
			}

			if ( !$q['suppress_filters'] ) {
				$cjoin = apply_filters_ref_array('comment_feed_join', array( $cjoin, &$this ) );
				$cwhere = apply_filters_ref_array('comment_feed_where', array( $cwhere, &$this ) );
				$cgroupby = apply_filters_ref_array('comment_feed_groupby', array( $cgroupby, &$this ) );
				$corderby = apply_filters_ref_array('comment_feed_orderby', array( 'comment_date_gmt DESC', &$this ) );
				$climits = apply_filters_ref_array('comment_feed_limits', array( 'LIMIT ' . get_option('posts_per_rss'), &$this ) );
			}
			$cgroupby = ( ! empty( $cgroupby ) ) ? 'GROUP BY ' . $cgroupby : '';
			$corderby = ( ! empty( $corderby ) ) ? 'ORDER BY ' . $corderby : '';

			$this->comments = (array) $wpdb->get_results("SELECT $distinct $wpdb->comments.* FROM $wpdb->comments $cjoin $cwhere $cgroupby $corderby $climits");
			$this->comment_count = count($this->comments);

			$post_ids = array();

			foreach ( $this->comments as $comment )
				$post_ids[] = (int) $comment->comment_post_ID;

			$post_ids = join(',', $post_ids);
			$join = '';
			if ( $post_ids )
				$where = "AND $wpdb->posts.ID IN ($post_ids) ";
			else
				$where = "AND 0";
		}

		$pieces = array( 'where', 'groupby', 'join', 'orderby', 'distinct', 'fields', 'limits' );

		// Apply post-paging filters on where and join. Only plugins that
		// manipulate paging queries should use these hooks.
		if ( !$q['suppress_filters'] ) {
			$where		= apply_filters_ref_array( 'posts_where_paged',	array( $where, &$this ) );
			$groupby	= apply_filters_ref_array( 'posts_groupby',		array( $groupby, &$this ) );
			$join		= apply_filters_ref_array( 'posts_join_paged',	array( $join, &$this ) );
			$orderby	= apply_filters_ref_array( 'posts_orderby',		array( $orderby, &$this ) );
			$distinct	= apply_filters_ref_array( 'posts_distinct',	array( $distinct, &$this ) );
			$limits		= apply_filters_ref_array( 'post_limits',		array( $limits, &$this ) );
			$fields		= apply_filters_ref_array( 'posts_fields',		array( $fields, &$this ) );

			// Filter all clauses at once, for convenience
			$clauses = (array) apply_filters_ref_array( 'posts_clauses', array( compact( $pieces ), &$this ) );
			foreach ( $pieces as $piece )
				$$piece = isset( $clauses[ $piece ] ) ? $clauses[ $piece ] : '';
		}

		// Announce current selection parameters. For use by caching plugins.
		do_action( 'posts_selection', $where . $groupby . $orderby . $limits . $join );

		// Filter again for the benefit of caching plugins. Regular plugins should use the hooks above.
		if ( !$q['suppress_filters'] ) {
			$where		= apply_filters_ref_array( 'posts_where_request',		array( $where, &$this ) );
			$groupby	= apply_filters_ref_array( 'posts_groupby_request',		array( $groupby, &$this ) );
			$join		= apply_filters_ref_array( 'posts_join_request',		array( $join, &$this ) );
			$orderby	= apply_filters_ref_array( 'posts_orderby_request',		array( $orderby, &$this ) );
			$distinct	= apply_filters_ref_array( 'posts_distinct_request',	array( $distinct, &$this ) );
			$fields		= apply_filters_ref_array( 'posts_fields_request',		array( $fields, &$this ) );
			$limits		= apply_filters_ref_array( 'post_limits_request',		array( $limits, &$this ) );

			// Filter all clauses at once, for convenience
			$clauses = (array) apply_filters_ref_array( 'posts_clauses_request', array( compact( $pieces ), &$this ) );
			foreach ( $pieces as $piece )
				$$piece = isset( $clauses[ $piece ] ) ? $clauses[ $piece ] : '';
		}

		if ( ! empty($groupby) )
			$groupby = 'GROUP BY ' . $groupby;
		if ( !empty( $orderby ) )
			$orderby = 'ORDER BY ' . $orderby;

		$found_rows = '';
		if ( !$q['no_found_rows'] && !empty($limits) )
			$found_rows = 'SQL_CALC_FOUND_ROWS';

		$this->request = $old_request = "SELECT $found_rows $distinct $fields FROM $wpdb->posts $join WHERE 1=1 $where $groupby $orderby $limits";

		if ( !$q['suppress_filters'] ) {
			$this->request = apply_filters_ref_array( 'posts_request', array( $this->request, &$this ) );
		}

		if ( 'ids' == $q['fields'] ) {
			$this->posts = $wpdb->get_col( $this->request );
			$this->post_count = count( $this->posts );
			$this->set_found_posts( $q, $limits );

			return $this->posts;
		}

		if ( 'id=>parent' == $q['fields'] ) {
			$this->posts = $wpdb->get_results( $this->request );
			$this->post_count = count( $this->posts );
			$this->set_found_posts( $q, $limits );

			$r = array();
			foreach ( $this->posts as $post )
				$r[ $post->ID ] = $post->post_parent;

			return $r;
		}

		$split_the_query = ( $old_request == $this->request && "$wpdb->posts.*" == $fields && !empty( $limits ) && $q['posts_per_page'] < 500 );
		$split_the_query = apply_filters( 'split_the_query', $split_the_query, $this );

		if ( $split_the_query ) {
			// First get the IDs and then fill in the objects

			$this->request = "SELECT $found_rows $distinct $wpdb->posts.ID FROM $wpdb->posts $join WHERE 1=1 $where $groupby $orderby $limits";

			$this->request = apply_filters( 'posts_request_ids', $this->request, $this );

			$ids = $wpdb->get_col( $this->request );

			if ( $ids ) {
				$this->posts = $ids;
				$this->set_found_posts( $q, $limits );
				_prime_post_caches( $ids, $q['update_post_term_cache'], $q['update_post_meta_cache'] );
			} else {
				$this->posts = array();
			}
		} else {
			$this->posts = $wpdb->get_results( $this->request );
			$this->set_found_posts( $q, $limits );
		}

		// Convert to WP_Post objects
		if ( $this->posts )
			$this->posts = array_map( 'get_post', $this->posts );

		// Raw results filter. Prior to status checks.
		if ( !$q['suppress_filters'] )
			$this->posts = apply_filters_ref_array('posts_results', array( $this->posts, &$this ) );

		if ( !empty($this->posts) && $this->is_comment_feed && $this->is_singular ) {
			$cjoin = apply_filters_ref_array('comment_feed_join', array( '', &$this ) );
			$cwhere = apply_filters_ref_array('comment_feed_where', array( "WHERE comment_post_ID = '{$this->posts[0]->ID}' AND comment_approved = '1'", &$this ) );
			$cgroupby = apply_filters_ref_array('comment_feed_groupby', array( '', &$this ) );
			$cgroupby = ( ! empty( $cgroupby ) ) ? 'GROUP BY ' . $cgroupby : '';
			$corderby = apply_filters_ref_array('comment_feed_orderby', array( 'comment_date_gmt DESC', &$this ) );
			$corderby = ( ! empty( $corderby ) ) ? 'ORDER BY ' . $corderby : '';
			$climits = apply_filters_ref_array('comment_feed_limits', array( 'LIMIT ' . get_option('posts_per_rss'), &$this ) );
			$comments_request = "SELECT $wpdb->comments.* FROM $wpdb->comments $cjoin $cwhere $cgroupby $corderby $climits";
			$this->comments = $wpdb->get_results($comments_request);
			$this->comment_count = count($this->comments);
		}

		// Check post status to determine if post should be displayed.
		if ( !empty($this->posts) && ($this->is_single || $this->is_page) ) {
			$status = get_post_status($this->posts[0]);
			$post_status_obj = get_post_status_object($status);
			//$type = get_post_type($this->posts[0]);
			if ( !$post_status_obj->public ) {
				if ( ! is_user_logged_in() ) {
					// User must be logged in to view unpublished posts.
					$this->posts = array();
				} else {
					if  ( $post_status_obj->protected ) {
						// User must have edit permissions on the draft to preview.
						if ( ! current_user_can($edit_cap, $this->posts[0]->ID) ) {
							$this->posts = array();
						} else {
							$this->is_preview = true;
							if ( 'future' != $status )
								$this->posts[0]->post_date = current_time('mysql');
						}
					} elseif ( $post_status_obj->private ) {
						if ( ! current_user_can($read_cap, $this->posts[0]->ID) )
							$this->posts = array();
					} else {
						$this->posts = array();
					}
				}
			}

			if ( $this->is_preview && $this->posts && current_user_can( $edit_cap, $this->posts[0]->ID ) )
				$this->posts[0] = get_post( apply_filters_ref_array( 'the_preview', array( $this->posts[0], &$this ) ) );
		}

		// Put sticky posts at the top of the posts array
		$sticky_posts = get_option('sticky_posts');
		if ( $this->is_home && $page <= 1 && is_array($sticky_posts) && !empty($sticky_posts) && !$q['ignore_sticky_posts'] ) {
			$num_posts = count($this->posts);
			$sticky_offset = 0;
			// Loop over posts and relocate stickies to the front.
			for ( $i = 0; $i < $num_posts; $i++ ) {
				if ( in_array($this->posts[$i]->ID, $sticky_posts) ) {
					$sticky_post = $this->posts[$i];
					// Remove sticky from current position
					array_splice($this->posts, $i, 1);
					// Move to front, after other stickies
					array_splice($this->posts, $sticky_offset, 0, array($sticky_post));
					// Increment the sticky offset. The next sticky will be placed at this offset.
					$sticky_offset++;
					// Remove post from sticky posts array
					$offset = array_search($sticky_post->ID, $sticky_posts);
					unset( $sticky_posts[$offset] );
				}
			}

			// If any posts have been excluded specifically, Ignore those that are sticky.
			if ( !empty($sticky_posts) && !empty($q['post__not_in']) )
				$sticky_posts = array_diff($sticky_posts, $q['post__not_in']);

			// Fetch sticky posts that weren't in the query results
			if ( !empty($sticky_posts) ) {
				$stickies = get_posts( array(
					'post__in' => $sticky_posts,
					'post_type' => $post_type,
					'post_status' => 'publish',
					'nopaging' => true
				) );

				foreach ( $stickies as $sticky_post ) {
					array_splice( $this->posts, $sticky_offset, 0, array( $sticky_post ) );
					$sticky_offset++;
				}
			}
		}

		if ( !$q['suppress_filters'] )
			$this->posts = apply_filters_ref_array('the_posts', array( $this->posts, &$this ) );

		// Ensure that any posts added/modified via one of the filters above are
		// of the type WP_Post and are filtered.
		if ( $this->posts ) {
			$this->post_count = count( $this->posts );

			$this->posts = array_map( 'get_post', $this->posts );

			if ( $q['cache_results'] )
				update_post_caches($this->posts, $post_type, $q['update_post_term_cache'], $q['update_post_meta_cache']);

			$this->post = reset( $this->posts );
		} else {
			$this->post_count = 0;
			$this->posts = array();
		}

		return $this->posts;
	}

	/**
	 * Set up the amount of found posts and the number of pages (if limit clause was used)
	 * for the current query.
	 *
	 * @since 3.5.0
	 * @access private
	 */
	function set_found_posts( $q, $limits ) {
		global $wpdb;

		// Bail if posts is an empty array. Continue if posts is an empty string,
		// null, or false to accommodate caching plugins that fill posts later.
		if ( $q['no_found_rows'] || ( is_array( $this->posts ) && ! $this->posts ) )
			return;

		if ( ! empty( $limits ) )
			$this->found_posts = $wpdb->get_var( apply_filters_ref_array( 'found_posts_query', array( 'SELECT FOUND_ROWS()', &$this ) ) );
		else
			$this->found_posts = count( $this->posts );

		$this->found_posts = apply_filters_ref_array( 'found_posts', array( $this->found_posts, &$this ) );

		if ( ! empty( $limits ) )
			$this->max_num_pages = ceil( $this->found_posts / $q['posts_per_page'] );
	}

	/**
	 * Set up the next post and iterate current post index.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return WP_Post Next post.
	 */
	function next_post() {

		$this->current_post++;

		$this->post = $this->posts[$this->current_post];
		return $this->post;
	}

	/**
	 * Sets up the current post.
	 *
	 * Retrieves the next post, sets up the post, sets the 'in the loop'
	 * property to true.
	 *
	 * @since 1.5.0
	 * @access public
	 * @uses $post
	 * @uses do_action_ref_array() Calls 'loop_start' if loop has just started
	 */
	function the_post() {
		global $post;
		$this->in_the_loop = true;

		if ( $this->current_post == -1 ) // loop has just started
			do_action_ref_array('loop_start', array(&$this));

		$post = $this->next_post();
		setup_postdata($post);
	}

	/**
	 * Whether there are more posts available in the loop.
	 *
	 * Calls action 'loop_end', when the loop is complete.
	 *
	 * @since 1.5.0
	 * @access public
	 * @uses do_action_ref_array() Calls 'loop_end' if loop is ended
	 *
	 * @return bool True if posts are available, false if end of loop.
	 */
	function have_posts() {
		if ( $this->current_post + 1 < $this->post_count ) {
			return true;
		} elseif ( $this->current_post + 1 == $this->post_count && $this->post_count > 0 ) {
			do_action_ref_array('loop_end', array(&$this));
			// Do some cleaning up after the loop
			$this->rewind_posts();
		}

		$this->in_the_loop = false;
		return false;
	}

	/**
	 * Rewind the posts and reset post index.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	function rewind_posts() {
		$this->current_post = -1;
		if ( $this->post_count > 0 ) {
			$this->post = $this->posts[0];
		}
	}

	/**
	 * Iterate current comment index and return comment object.
	 *
	 * @since 2.2.0
	 * @access public
	 *
	 * @return object Comment object.
	 */
	function next_comment() {
		$this->current_comment++;

		$this->comment = $this->comments[$this->current_comment];
		return $this->comment;
	}

	/**
	 * Sets up the current comment.
	 *
	 * @since 2.2.0
	 * @access public
	 * @global object $comment Current comment.
	 * @uses do_action() Calls 'comment_loop_start' hook when first comment is processed.
	 */
	function the_comment() {
		global $comment;

		$comment = $this->next_comment();

		if ( $this->current_comment == 0 ) {
			do_action('comment_loop_start');
		}
	}

	/**
	 * Whether there are more comments available.
	 *
	 * Automatically rewinds comments when finished.
	 *
	 * @since 2.2.0
	 * @access public
	 *
	 * @return bool True, if more comments. False, if no more posts.
	 */
	function have_comments() {
		if ( $this->current_comment + 1 < $this->comment_count ) {
			return true;
		} elseif ( $this->current_comment + 1 == $this->comment_count ) {
			$this->rewind_comments();
		}

		return false;
	}

	/**
	 * Rewind the comments, resets the comment index and comment to first.
	 *
	 * @since 2.2.0
	 * @access public
	 */
	function rewind_comments() {
		$this->current_comment = -1;
		if ( $this->comment_count > 0 ) {
			$this->comment = $this->comments[0];
		}
	}

	/**
	 * Sets up the WordPress query by parsing query string.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $query URL query string.
	 * @return array List of posts.
	 */
	function query( $query ) {
		$this->init();
		$this->query = $this->query_vars = wp_parse_args( $query );
		return $this->get_posts();
	}

	/**
	 * Retrieve queried object.
	 *
	 * If queried object is not set, then the queried object will be set from
	 * the category, tag, taxonomy, posts page, single post, page, or author
	 * query variable. After it is set up, it will be returned.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return object
	 */
	function get_queried_object() {
		if ( isset($this->queried_object) )
			return $this->queried_object;

		$this->queried_object = null;
		$this->queried_object_id = 0;

		if ( $this->is_category || $this->is_tag || $this->is_tax ) {
			$tax_query_in_and = wp_list_filter( $this->tax_query->queries, array( 'operator' => 'NOT IN' ), 'NOT' );

			$query = reset( $tax_query_in_and );

			if ( 'term_id' == $query['field'] )
				$term = get_term( reset( $query['terms'] ), $query['taxonomy'] );
			elseif ( $query['terms'] )
				$term = get_term_by( $query['field'], reset( $query['terms'] ), $query['taxonomy'] );

			if ( ! empty( $term ) && ! is_wp_error( $term ) )  {
				$this->queried_object = $term;
				$this->queried_object_id = (int) $term->term_id;

				if ( $this->is_category )
					_make_cat_compat( $this->queried_object );
			}
		} elseif ( $this->is_post_type_archive ) {
			$post_type = $this->get( 'post_type' );
			if ( is_array( $post_type ) )
				$post_type = reset( $post_type );
			$this->queried_object = get_post_type_object( $post_type );
		} elseif ( $this->is_posts_page ) {
			$page_for_posts = get_option('page_for_posts');
			$this->queried_object = get_post( $page_for_posts );
			$this->queried_object_id = (int) $this->queried_object->ID;
		} elseif ( $this->is_singular && !is_null($this->post) ) {
			$this->queried_object = $this->post;
			$this->queried_object_id = (int) $this->post->ID;
		} elseif ( $this->is_author ) {
			$this->queried_object_id = (int) $this->get('author');
			$this->queried_object = get_userdata( $this->queried_object_id );
		}

		return $this->queried_object;
	}

	/**
	 * Retrieve ID of the current queried object.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return int
	 */
	function get_queried_object_id() {
		$this->get_queried_object();

		if ( isset($this->queried_object_id) ) {
			return $this->queried_object_id;
		}

		return 0;
	}

	/**
	 * Constructor.
	 *
	 * Sets up the WordPress query, if parameter is not empty.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $query URL query string.
	 * @return WP_Query
	 */
	function __construct($query = '') {
		if ( ! empty($query) ) {
			$this->query($query);
		}
	}

	/**
 	 * Is the query for an existing archive page?
 	 *
 	 * Month, Year, Category, Author, Post Type archive...
	 *
 	 * @since 3.1.0
 	 *
 	 * @return bool
 	 */
	function is_archive() {
		return (bool) $this->is_archive;
	}

	/**
	 * Is the query for an existing post type archive page?
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $post_types Optional. Post type or array of posts types to check against.
	 * @return bool
	 */
	function is_post_type_archive( $post_types = '' ) {
		if ( empty( $post_types ) || ! $this->is_post_type_archive )
			return (bool) $this->is_post_type_archive;

		$post_type = $this->get( 'post_type' );
		if ( is_array( $post_type ) )
			$post_type = reset( $post_type );
		$post_type_object = get_post_type_object( $post_type );

		return in_array( $post_type_object->name, (array) $post_types );
	}

	/**
	 * Is the query for an existing attachment page?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_attachment() {
		return (bool) $this->is_attachment;
	}

	/**
	 * Is the query for an existing author archive page?
	 *
	 * If the $author parameter is specified, this function will additionally
	 * check if the query is for one of the authors specified.
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $author Optional. User ID, nickname, nicename, or array of User IDs, nicknames, and nicenames
	 * @return bool
	 */
	function is_author( $author = '' ) {
		if ( !$this->is_author )
			return false;

		if ( empty($author) )
			return true;

		$author_obj = $this->get_queried_object();

		$author = (array) $author;

		if ( in_array( $author_obj->ID, $author ) )
			return true;
		elseif ( in_array( $author_obj->nickname, $author ) )
			return true;
		elseif ( in_array( $author_obj->user_nicename, $author ) )
			return true;

		return false;
	}

	/**
	 * Is the query for an existing category archive page?
	 *
	 * If the $category parameter is specified, this function will additionally
	 * check if the query is for one of the categories specified.
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $category Optional. Category ID, name, slug, or array of Category IDs, names, and slugs.
	 * @return bool
	 */
	function is_category( $category = '' ) {
		if ( !$this->is_category )
			return false;

		if ( empty($category) )
			return true;

		$cat_obj = $this->get_queried_object();

		$category = (array) $category;

		if ( in_array( $cat_obj->term_id, $category ) )
			return true;
		elseif ( in_array( $cat_obj->name, $category ) )
			return true;
		elseif ( in_array( $cat_obj->slug, $category ) )
			return true;

		return false;
	}

	/**
	 * Is the query for an existing tag archive page?
	 *
	 * If the $tag parameter is specified, this function will additionally
	 * check if the query is for one of the tags specified.
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $tag Optional. Tag ID, name, slug, or array of Tag IDs, names, and slugs.
	 * @return bool
	 */
	function is_tag( $tag = '' ) {
		if ( ! $this->is_tag )
			return false;

		if ( empty( $tag ) )
			return true;

		$tag_obj = $this->get_queried_object();

		$tag = (array) $tag;

		if ( in_array( $tag_obj->term_id, $tag ) )
			return true;
		elseif ( in_array( $tag_obj->name, $tag ) )
			return true;
		elseif ( in_array( $tag_obj->slug, $tag ) )
			return true;

		return false;
	}

	/**
	 * Is the query for an existing taxonomy archive page?
	 *
	 * If the $taxonomy parameter is specified, this function will additionally
	 * check if the query is for that specific $taxonomy.
	 *
	 * If the $term parameter is specified in addition to the $taxonomy parameter,
	 * this function will additionally check if the query is for one of the terms
	 * specified.
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $taxonomy Optional. Taxonomy slug or slugs.
	 * @param mixed $term. Optional. Term ID, name, slug or array of Term IDs, names, and slugs.
	 * @return bool
	 */
	function is_tax( $taxonomy = '', $term = '' ) {
		global $wp_taxonomies;

		if ( !$this->is_tax )
			return false;

		if ( empty( $taxonomy ) )
			return true;

		$queried_object = $this->get_queried_object();
		$tax_array = array_intersect( array_keys( $wp_taxonomies ), (array) $taxonomy );
		$term_array = (array) $term;

		// Check that the taxonomy matches.
		if ( ! ( isset( $queried_object->taxonomy ) && count( $tax_array ) && in_array( $queried_object->taxonomy, $tax_array ) ) )
			return false;

		// Only a Taxonomy provided.
		if ( empty( $term ) )
			return true;

		return isset( $queried_object->term_id ) &&
			count( array_intersect(
				array( $queried_object->term_id, $queried_object->name, $queried_object->slug ),
				$term_array
			) );
	}

	/**
	 * Whether the current URL is within the comments popup window.
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_comments_popup() {
		return (bool) $this->is_comments_popup;
	}

	/**
	 * Is the query for an existing date archive?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_date() {
		return (bool) $this->is_date;
	}

	/**
	 * Is the query for an existing day archive?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_day() {
		return (bool) $this->is_day;
	}

	/**
	 * Is the query for a feed?
	 *
	 * @since 3.1.0
	 *
	 * @param string|array $feeds Optional feed types to check.
	 * @return bool
	 */
	function is_feed( $feeds = '' ) {
		if ( empty( $feeds ) || ! $this->is_feed )
			return (bool) $this->is_feed;
		$qv = $this->get( 'feed' );
		if ( 'feed' == $qv )
			$qv = get_default_feed();
		return in_array( $qv, (array) $feeds );
	}

	/**
	 * Is the query for a comments feed?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_comment_feed() {
		return (bool) $this->is_comment_feed;
	}

	/**
	 * Is the query for the front page of the site?
	 *
	 * This is for what is displayed at your site's main URL.
	 *
	 * Depends on the site's "Front page displays" Reading Settings 'show_on_front' and 'page_on_front'.
	 *
	 * If you set a static page for the front page of your site, this function will return
	 * true when viewing that page.
	 *
	 * Otherwise the same as @see WP_Query::is_home()
	 *
	 * @since 3.1.0
	 * @uses is_home()
	 * @uses get_option()
	 *
	 * @return bool True, if front of site.
	 */
	function is_front_page() {
		// most likely case
		if ( 'posts' == get_option( 'show_on_front') && $this->is_home() )
			return true;
		elseif ( 'page' == get_option( 'show_on_front') && get_option( 'page_on_front' ) && $this->is_page( get_option( 'page_on_front' ) ) )
			return true;
		else
			return false;
	}

	/**
	 * Is the query for the blog homepage?
	 *
	 * This is the page which shows the time based blog content of your site.
	 *
	 * Depends on the site's "Front page displays" Reading Settings 'show_on_front' and 'page_for_posts'.
	 *
	 * If you set a static page for the front page of your site, this function will return
	 * true only on the page you set as the "Posts page".
	 *
	 * @see WP_Query::is_front_page()
	 *
	 * @since 3.1.0
	 *
	 * @return bool True if blog view homepage.
	 */
	function is_home() {
		return (bool) $this->is_home;
	}

	/**
	 * Is the query for an existing month archive?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_month() {
		return (bool) $this->is_month;
	}

	/**
	 * Is the query for an existing single page?
	 *
	 * If the $page parameter is specified, this function will additionally
	 * check if the query is for one of the pages specified.
	 *
	 * @see WP_Query::is_single()
	 * @see WP_Query::is_singular()
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $page Page ID, title, slug, or array of such.
	 * @return bool
	 */
	function is_page( $page = '' ) {
		if ( !$this->is_page )
			return false;

		if ( empty( $page ) )
			return true;

		$page_obj = $this->get_queried_object();

		$page = (array) $page;

		if ( in_array( $page_obj->ID, $page ) )
			return true;
		elseif ( in_array( $page_obj->post_title, $page ) )
			return true;
		else if ( in_array( $page_obj->post_name, $page ) )
			return true;

		return false;
	}

	/**
	 * Is the query for paged result and not for the first page?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_paged() {
		return (bool) $this->is_paged;
	}

	/**
	 * Is the query for a post or page preview?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_preview() {
		return (bool) $this->is_preview;
	}

	/**
	 * Is the query for the robots file?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_robots() {
		return (bool) $this->is_robots;
	}

	/**
	 * Is the query for a search?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_search() {
		return (bool) $this->is_search;
	}

	/**
	 * Is the query for an existing single post?
	 *
	 * Works for any post type, except attachments and pages
	 *
	 * If the $post parameter is specified, this function will additionally
	 * check if the query is for one of the Posts specified.
	 *
	 * @see WP_Query::is_page()
	 * @see WP_Query::is_singular()
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $post Post ID, title, slug, or array of such.
	 * @return bool
	 */
	function is_single( $post = '' ) {
		if ( !$this->is_single )
			return false;

		if ( empty($post) )
			return true;

		$post_obj = $this->get_queried_object();

		$post = (array) $post;

		if ( in_array( $post_obj->ID, $post ) )
			return true;
		elseif ( in_array( $post_obj->post_title, $post ) )
			return true;
		elseif ( in_array( $post_obj->post_name, $post ) )
			return true;

		return false;
	}

	/**
	 * Is the query for an existing single post of any post type (post, attachment, page, ... )?
	 *
	 * If the $post_types parameter is specified, this function will additionally
	 * check if the query is for one of the Posts Types specified.
	 *
	 * @see WP_Query::is_page()
	 * @see WP_Query::is_single()
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $post_types Optional. Post Type or array of Post Types
	 * @return bool
	 */
	function is_singular( $post_types = '' ) {
		if ( empty( $post_types ) || !$this->is_singular )
			return (bool) $this->is_singular;

		$post_obj = $this->get_queried_object();

		return in_array( $post_obj->post_type, (array) $post_types );
	}

	/**
	 * Is the query for a specific time?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_time() {
		return (bool) $this->is_time;
	}

	/**
	 * Is the query for a trackback endpoint call?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_trackback() {
		return (bool) $this->is_trackback;
	}

	/**
	 * Is the query for an existing year archive?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_year() {
		return (bool) $this->is_year;
	}

	/**
	 * Is the query a 404 (returns no results)?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_404() {
		return (bool) $this->is_404;
	}

	/**
	 * Is the query the main query?
	 *
	 * @since 3.3.0
	 *
	 * @return bool
	 */
	function is_main_query() {
		global $wp_the_query;
		return $wp_the_query === $this;
	}

	/**
	 * After looping through a nested query, this function
	 * restores the $post global to the current post in this query.
	 *
	 * @since 3.7.0
	 *
	 * @return bool
	 */
	function reset_postdata() {
		if ( ! empty( $this->post ) ) {
			$GLOBALS['post'] = $this->post;
			setup_postdata( $this->post );
		}
	}
}

/**
 * Redirect old slugs to the correct permalink.
 *
 * Attempts to find the current slug from the past slugs.
 *
 * @since 2.1.0
 * @uses $wp_query
 * @uses $wpdb
 *
 * @return null If no link is found, null is returned.
 */
function wp_old_slug_redirect() {
	global $wp_query;
	if ( is_404() && '' != $wp_query->query_vars['name'] ) :
		global $wpdb;

		// Guess the current post_type based on the query vars.
		if ( get_query_var('post_type') )
			$post_type = get_query_var('post_type');
		elseif ( !empty($wp_query->query_vars['pagename']) )
			$post_type = 'page';
		else
			$post_type = 'post';

		if ( is_array( $post_type ) ) {
			if ( count( $post_type ) > 1 )
				return;
			$post_type = array_shift( $post_type );
		}

		// Do not attempt redirect for hierarchical post types
		if ( is_post_type_hierarchical( $post_type ) )
			return;

		$query = $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta, $wpdb->posts WHERE ID = post_id AND post_type = %s AND meta_key = '_wp_old_slug' AND meta_value = %s", $post_type, $wp_query->query_vars['name']);

		// if year, monthnum, or day have been specified, make our query more precise
		// just in case there are multiple identical _wp_old_slug values
		if ( '' != $wp_query->query_vars['year'] )
			$query .= $wpdb->prepare(" AND YEAR(post_date) = %d", $wp_query->query_vars['year']);
		if ( '' != $wp_query->query_vars['monthnum'] )
			$query .= $wpdb->prepare(" AND MONTH(post_date) = %d", $wp_query->query_vars['monthnum']);
		if ( '' != $wp_query->query_vars['day'] )
			$query .= $wpdb->prepare(" AND DAYOFMONTH(post_date) = %d", $wp_query->query_vars['day']);

		$id = (int) $wpdb->get_var($query);

		if ( ! $id )
			return;

		$link = get_permalink($id);

		if ( !$link )
			return;

		wp_redirect( $link, 301 ); // Permanent redirect
		exit;
	endif;
}

/**
 * Set up global post data.
 *
 * @since 1.5.0
 *
 * @param object $post Post data.
 * @uses do_action_ref_array() Calls 'the_post'
 * @return bool True when finished.
 */
function setup_postdata( $post ) {
	global $id, $authordata, $currentday, $currentmonth, $page, $pages, $multipage, $more, $numpages;

	$id = (int) $post->ID;

	$authordata = get_userdata($post->post_author);

	$currentday = mysql2date('d.m.y', $post->post_date, false);
	$currentmonth = mysql2date('m', $post->post_date, false);
	$numpages = 1;
	$multipage = 0;
	$page = get_query_var('page');
	if ( ! $page )
		$page = 1;
	if ( is_single() || is_page() || is_feed() )
		$more = 1;
	$content = $post->post_content;
	if ( false !== strpos( $content, '<!--nextpage-->' ) ) {
		if ( $page > 1 )
			$more = 1;
		$content = str_replace( "\n<!--nextpage-->\n", '<!--nextpage-->', $content );
		$content = str_replace( "\n<!--nextpage-->", '<!--nextpage-->', $content );
		$content = str_replace( "<!--nextpage-->\n", '<!--nextpage-->', $content );
		// Ignore nextpage at the beginning of the content.
		if ( 0 === strpos( $content, '<!--nextpage-->' ) )
			$content = substr( $content, 15 );
		$pages = explode('<!--nextpage-->', $content);
		$numpages = count($pages);
		if ( $numpages > 1 )
			$multipage = 1;
	} else {
		$pages = array( $post->post_content );
	}

	do_action_ref_array('the_post', array(&$post));

	return true;
}
