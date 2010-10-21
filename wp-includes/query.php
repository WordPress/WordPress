<?php
/**
 * WordPress Query API
 *
 * The query API attempts to get which part of WordPress to the user is on. It
 * also provides functionality to getting URL query information.
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
function &query_posts($query) {
	unset($GLOBALS['wp_query']);
	$GLOBALS['wp_query'] =& new WP_Query();
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
	unset($GLOBALS['wp_query']);
	$GLOBALS['wp_query'] =& $GLOBALS['wp_the_query'];
	wp_reset_postdata();
}

/**
 * After looping through a separate query, this function restores
 * the $post global to the current post in the main query
 *
 * @since 3.0.0
 * @uses $wp_query
 */
function wp_reset_postdata() {
	global $wp_query;
	if ( !empty($wp_query->post) ) {
		$GLOBALS['post'] = $wp_query->post;
		setup_postdata($wp_query->post);
	}
}

/*
 * Query type checks.
 */

/**
 * Is the query for an archive page?
 *
 * Month, Year, Category, Author, ...
 *
 * If the $post_types parameter is specified, this function will additionally
 * check if the query is for exactly one of the post types specified. If a plugin
 * is causing multiple post types to appear in the query, specifying a post type
 * will cause this check to return false.
 *
 * @see WP_Query::is_archive()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param mixed $post_types Optional. Post type or array of post types
 * @return bool
 */
function is_archive( $post_types = '' ) {
	global $wp_query;

	return $wp_query->is_archive( $post_types );
}

/**
 * Is the query for a post type archive page?
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

	return $wp_query->is_post_type_archive( $post_types );
}

/**
 * Is the query for an attachment page?
 *
 * @see WP_Query::is_attachment()
 * @since 2.0.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_attachment() {
	global $wp_query;

	return $wp_query->is_attachment();
}

/**
 * Is the query for an author archive page?
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

	return $wp_query->is_author( $author );
}

/**
 * Is the query for a category archive page?
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

	return $wp_query->is_category( $category );
}

/**
 * Is the query for a tag archive page?
 *
 * If the $tag parameter is specified, this function will additionally
 * check if the query is for one of the tags specified.
 *
 * @see WP_Query::is_tag()
 * @since 2.3.0
 * @uses $wp_query
 *
 * @param mixed $slug Optional. Tag slug or array of slugs.
 * @return bool
 */
function is_tag( $slug = '' ) {
	global $wp_query;

	return $wp_query->is_tag( $slug );
}

/**
 * Is the query for a taxonomy archive page?
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
	global $wp_query, $wp_taxonomies;

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

	return $wp_query->is_comments_popup();
}

/**
 * Is the query for a date archive?
 *
 * @see WP_Query::is_date()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_date() {
	global $wp_query;

	return $wp_query->is_date();
}

/**
 * Is the query for a day archive?
 *
 * @see WP_Query::is_day()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_day() {
	global $wp_query;

	return $wp_query->is_day();
}

/**
 * Is the query for a feed?
 *
 * @see WP_Query::is_feed()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_feed() {
	global $wp_query;

	return $wp_query->is_feed();
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

	return $wp_query->is_home();
}

/**
 * Is the query for a month archive?
 *
 * @see WP_Query::is_month()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_month() {
	global $wp_query;

	return $wp_query->is_month();
}

/**
 * Is the query for a single Page?
 *
 * If the $page parameter is specified, this function will additionally
 * check if the query is for one of the Pages specified.
 *
 * @see is_single()
 * @see is_singular()
 *
 * @see WP_Query::is_single()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param mixed $page Page ID, title, slug, or array of Page IDs, titles, and slugs.
 * @return bool
 */
function is_page( $page = '' ) {
	global $wp_query;

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

	return $wp_query->is_search();
}

/**
 * Is the query for a single post?
 *
 * If the $post parameter is specified, this function will additionally
 * check if the query is for one of the Posts specified.
 *
 * Can also be used for attachments or any other post type except pages.
 *
 * @see is_page()
 * @see is_singular()
 *
 * @see WP_Query::is_single()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param mixed $post Post ID, title, slug, or array of Post IDs, titles, and slugs.
 * @return bool
 */
function is_single( $post = '' ) {
	global $wp_query;

	return $wp_query->is_single( $post );
}

/**
 * Is the query for a single post of any post type (post, attachment, page, ... )?
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

	return $wp_query->is_trackback();
}

/**
 * Is the query for a specific year?
 *
 * @see WP_Query::is_year()
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_year() {
	global $wp_query;

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

	return $wp_query->is_404();
}

/*
 * The Loop.  Post loop control.
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
class WP_Query extends WP_Object_Query {

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
	 * The current post ID.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var int
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
	 * Amount of posts if limit clause was not used.
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
		$this->parse_query('');
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
		);

		foreach ( $keys as $key ) {
			if ( !isset($array[$key]))
				$array[$key] = '';
		}

		$array_keys = array('category__in', 'category__not_in', 'category__and', 'post__in', 'post__not_in',
			'tag__in', 'tag__not_in', 'tag__and', 'tag_slug__in', 'tag_slug__and');

		foreach ( $array_keys as $key ) {
			if ( !isset($array[$key]))
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
	 * @param string|array $query
	 */
	function parse_query($query) {
		if ( !empty($query) || !isset($this->query) ) {
			$this->init();
			$this->query = $this->query_vars = wp_parse_args($query);
		}

		$this->query_vars = $this->fill_query_vars($this->query_vars);
		$qv = &$this->query_vars;

		if ( ! empty($qv['robots']) )
			$this->is_robots = true;

		$qv['p'] =  absint($qv['p']);
		$qv['page_id'] =  absint($qv['page_id']);
		$qv['year'] = absint($qv['year']);
		$qv['monthnum'] = absint($qv['monthnum']);
		$qv['day'] = absint($qv['day']);
		$qv['w'] = absint($qv['w']);
		$qv['m'] = absint($qv['m']);
		$qv['paged'] = absint($qv['paged']);
		$qv['cat'] = preg_replace( '|[^0-9,-]|', '', $qv['cat'] ); // comma separated list of positive or negative integers
		$qv['pagename'] = trim( $qv['pagename'] );
		$qv['name'] = trim( $qv['name'] );
		if ( '' !== $qv['hour'] ) $qv['hour'] = absint($qv['hour']);
		if ( '' !== $qv['minute'] ) $qv['minute'] = absint($qv['minute']);
		if ( '' !== $qv['second'] ) $qv['second'] = absint($qv['second']);

		// Compat.  Map subpost to attachment.
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
		// Look for archive queries.  Dates, categories, authors, search, post type archives.

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
					$this->is_day = true;
					$this->is_date = true;
				}
			}

			if ( $qv['monthnum'] ) {
				if ( ! $this->is_date ) {
					$this->is_month = true;
					$this->is_date = true;
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

			$this->parse_tax_query( $qv );

			$this->parse_meta_query( $qv );

			if ( empty($qv['author']) || ($qv['author'] == '0') ) {
				$this->is_author = false;
			} else {
				$this->is_author = true;
			}

			if ( '' != $qv['author_name'] )
				$this->is_author = true;

			if ( !empty( $qv['post_type'] ) && ! is_array( $qv['post_type'] ) ) {
				$post_type_obj = get_post_type_object( $qv['post_type'] );
				if ( is_array( $post_type_obj->rewrite ) && $post_type_obj->rewrite['archive'] )
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
			$_query = wp_parse_args($query);
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
			$this->queried_object =& get_page_by_path($qv['pagename']);
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

		if ( !empty($qv['post_status']) )
			$qv['post_status'] = preg_replace('|[^a-z0-9_,-]|', '', $qv['post_status']);

		if ( $this->is_posts_page && ( ! isset($qv['withcomments']) || ! $qv['withcomments'] ) )
			$this->is_comment_feed = false;

		$this->is_singular = $this->is_single || $this->is_page || $this->is_attachment;
		// Done correcting is_* for page_on_front and page_for_posts

		if ( '404' == $qv['error'] )
			$this->set_404();

		if ( !empty($query) )
			do_action_ref_array('parse_query', array(&$this));
	}

	/*
	 * Populates the 'tax_query' property
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
		} else {
			foreach ( $GLOBALS['wp_taxonomies'] as $taxonomy => $t ) {
				if ( $t->query_var && !empty( $q[$t->query_var] ) ) {
					$tax_query_defaults = array(
						'taxonomy' => $taxonomy,
						'field' => 'slug',
						'operator' => 'IN'
					);

					if ( $t->rewrite['hierarchical'] ) {
						$q[$t->query_var] = basename($q[$t->query_var]);
						if ( $taxonomy == $q['taxonomy'] )
							$q['term'] = basename($q['term']);
					}

					$term = str_replace( ' ', '+', $q[$t->query_var] );

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
		}

		// Category stuff
		if ( !empty($q['cat']) && '0' != $q['cat'] && !$this->is_singular ) {
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
				} else {
					$q['category__not_in'][] = $cat;
				}
			}
			$q['cat'] = implode(',', $req_cats);
		}

		if ( !empty($q['category__in']) ) {
			$tax_query[] = array(
				'taxonomy' => 'category',
				'terms' => $q['category__in'],
				'operator' => 'IN',
				'field' => 'term_id'
			);
		}

		if ( !empty($q['category__not_in']) ) {
			$tax_query[] = array(
				'taxonomy' => 'category',
				'terms' => $q['category__not_in'],
				'operator' => 'NOT IN',
				'field' => 'term_id'
			);
		}

		// Tag stuff
		if ( !empty($qv['tag_id']) ) {
			$tax_query[] = array(
				'taxonomy' => 'post_tag',
				'terms' => $qv['tag_id'],
				'operator' => 'IN',
				'field' => 'term_id'
			);
		}

		if ( !empty($q['tag__in']) ) {
			$tax_query[] = array(
				'taxonomy' => 'post_tag',
				'terms' => $q['tag__in'],
				'operator' => 'IN',
				'field' => 'term_id'
			);
		}

		if ( !empty($q['tag__not_in']) ) {
			$tax_query[] = array(
				'taxonomy' => 'post_tag',
				'terms' => $q['tag__not_in'],
				'operator' => 'NOT IN',
				'field' => 'term_id'
			);
		}

		$q['tax_query'] = $tax_query;

		foreach ( $q['tax_query'] as $query ) {
			if ( 'IN' == $query['operator'] ) {
				switch ( $query['taxonomy'] ) {
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
	function &get_posts() {
		global $wpdb, $user_ID, $_wp_using_ext_object_cache;

		do_action_ref_array('pre_get_posts', array(&$this));

		// Shorthand.
		$q = &$this->query_vars;

		$q = $this->fill_query_vars($q);

		// First let's clear some variables
		$distinct = '';
		$whichauthor = '';
		$whichmimetype = '';
		$where = '';
		$limits = '';
		$join = '';
		$search = '';
		$groupby = '';
		$fields = "$wpdb->posts.*";
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
			if ( $_wp_using_ext_object_cache )
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

		// If a month is specified in the querystring, load that month
		if ( $q['m'] ) {
			$q['m'] = '' . preg_replace('|[^0-9]|', '', $q['m']);
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

		if ( '' !== $q['hour'] )
			$where .= " AND HOUR($wpdb->posts.post_date)='" . $q['hour'] . "'";

		if ( '' !== $q['minute'] )
			$where .= " AND MINUTE($wpdb->posts.post_date)='" . $q['minute'] . "'";

		if ( '' !== $q['second'] )
			$where .= " AND SECOND($wpdb->posts.post_date)='" . $q['second'] . "'";

		if ( $q['year'] )
			$where .= " AND YEAR($wpdb->posts.post_date)='" . $q['year'] . "'";

		if ( $q['monthnum'] )
			$where .= " AND MONTH($wpdb->posts.post_date)='" . $q['monthnum'] . "'";

		if ( $q['day'] )
			$where .= " AND DAYOFMONTH($wpdb->posts.post_date)='" . $q['day'] . "'";

		// If we've got a post_type AND its not "any" post_type.
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
			$q['name'] = sanitize_title($q['name']);
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
				$q['pagename'] = str_replace('%2F', '/', urlencode(urldecode($q['pagename'])));
				$page_paths = '/' . trim($q['pagename'], '/');
				$q['pagename'] = sanitize_title(basename($page_paths));
				$q['name'] = $q['pagename'];
				$where .= " AND ($wpdb->posts.ID = '$reqpage')";
				$reqpage_obj = get_page($reqpage);
				if ( is_object($reqpage_obj) && 'attachment' == $reqpage_obj->post_type ) {
					$this->is_attachment = true;
					$post_type = $q['post_type'] = 'attachment';
					$this->is_page = true;
					$q['attachment_id'] = $reqpage;
				}
			}
		} elseif ( '' != $q['attachment'] ) {
			$q['attachment'] = str_replace('%2F', '/', urlencode(urldecode($q['attachment'])));
			$attach_paths = '/' . trim($q['attachment'], '/');
			$q['attachment'] = sanitize_title(basename($attach_paths));
			$q['name'] = $q['attachment'];
			$where .= " AND $wpdb->posts.post_name = '" . $q['attachment'] . "'";
		}

		if ( $q['w'] )
			$where .= ' AND ' . _wp_mysql_week( "`$wpdb->posts`.`post_date`" ) . " = '" . $q['w'] . "'";

		if ( intval($q['comments_popup']) )
			$q['p'] = absint($q['comments_popup']);

		// If an attachment is requested by number, let it supercede any post number.
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

		if ( is_numeric($q['post_parent']) )
			$where .= $wpdb->prepare( " AND $wpdb->posts.post_parent = %d ", $q['post_parent'] );

		if ( $q['page_id'] ) {
			if  ( ('page' != get_option('show_on_front') ) || ( $q['page_id'] != get_option('page_for_posts') ) ) {
				$q['p'] = $q['page_id'];
				$where = " AND {$wpdb->posts}.ID = " . $q['page_id'];
			}
		}

		// If a search pattern is specified, load the posts that match
		if ( !empty($q['s']) ) {
			// added slashes screw with quote grouping when done early, so done later
			$q['s'] = stripslashes($q['s']);
			if ( !empty($q['sentence']) ) {
				$q['search_terms'] = array($q['s']);
			} else {
				preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $q['s'], $matches);
				$q['search_terms'] = array_map('_search_terms_tidy', $matches[0]);
			}
			$n = !empty($q['exact']) ? '' : '%';
			$searchand = '';
			foreach( (array) $q['search_terms'] as $term ) {
				$term = addslashes_gpc($term);
				$search .= "{$searchand}(($wpdb->posts.post_title LIKE '{$n}{$term}{$n}') OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}'))";
				$searchand = ' AND ';
			}
			$term = esc_sql($q['s']);
			if ( empty($q['sentence']) && count($q['search_terms']) > 1 && $q['search_terms'][0] != $q['s'] )
				$search .= " OR ($wpdb->posts.post_title LIKE '{$n}{$term}{$n}') OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";

			if ( !empty($search) ) {
				$search = " AND ({$search}) ";
				if ( !is_user_logged_in() )
					$search .= " AND ($wpdb->posts.post_password = '') ";
			}
		}

		// Allow plugins to contextually add/remove/modify the search section of the database query
		$search = apply_filters_ref_array('posts_search', array( $search, &$this ) );

		// Taxonomies
		if ( !empty( $q['tax_query'] ) ) {
			if ( empty($post_type) ) {
				$post_type = 'any';
				$post_status_join = true;
			} elseif ( in_array('attachment', (array) $post_type) ) {
				$post_status_join = true;
			}

			$where .= $this->get_tax_sql( $q['tax_query'], "$wpdb->posts.ID" );

			// Back-compat
			if ( !empty( $ids ) ) {
				$cat_query = wp_list_filter( $q['tax_query'], array( 'taxonomy' => 'category' ) );
				if ( !empty( $cat_query ) ) {
					$cat_query = reset( $cat_query );
					$cat = get_term_by( $cat_query['field'], $cat_query['terms'][0], 'category' );
					if ( $cat ) {
						$this->set('cat', $cat->term_id);
						$this->set('category_name', $cat->slug);
					}
				}
			}

			unset( $ids );
		}

		if ( !empty($q['meta_key']) ) {
			$groupby = "{$wpdb->posts}.ID";
		}

		// Author/user stuff

		if ( empty($q['author']) || ($q['author'] == '0') ) {
			$whichauthor = '';
		} else {
			$q['author'] = (string)urldecode($q['author']);
			$q['author'] = addslashes_gpc($q['author']);
			if ( strpos($q['author'], '-') !== false ) {
				$eq = '!=';
				$andor = 'AND';
				$q['author'] = explode('-', $q['author']);
				$q['author'] = (string)absint($q['author'][1]);
			} else {
				$eq = '=';
				$andor = 'OR';
			}
			$author_array = preg_split('/[,\s]+/', $q['author']);
			$_author_array = array();
			foreach ( $author_array as $key => $_author )
				$_author_array[] = "$wpdb->posts.post_author " . $eq . ' ' . absint($_author);
			$whichauthor .= ' AND (' . implode(" $andor ", $_author_array) . ')';
			unset($author_array, $_author_array);
		}

		// Author stuff for nice URLs

		if ( '' != $q['author_name'] ) {
			if ( strpos($q['author_name'], '/') !== false ) {
				$q['author_name'] = explode('/', $q['author_name']);
				if ( $q['author_name'][ count($q['author_name'])-1 ] ) {
					$q['author_name'] = $q['author_name'][count($q['author_name'])-1]; // no trailing slash
				} else {
					$q['author_name'] = $q['author_name'][count($q['author_name'])-2]; // there was a trailling slash
				}
			}
			$q['author_name'] = sanitize_title($q['author_name']);
			$q['author'] = get_user_by('slug', $q['author_name']);
			if ( $q['author'] )
				$q['author'] = $q['author']->ID;
			$whichauthor .= " AND ($wpdb->posts.post_author = " . absint($q['author']) . ')';
		}

		// MIME-Type stuff for attachment browsing

		if ( isset($q['post_mime_type']) && '' != $q['post_mime_type'] ) {
			$table_alias = $post_status_join ? $wpdb->posts : '';
			$whichmimetype = wp_post_mime_type_where($q['post_mime_type'], $table_alias);
		}

		$where .= $search . $whichauthor . $whichmimetype;

		if ( empty($q['order']) || ((strtoupper($q['order']) != 'ASC') && (strtoupper($q['order']) != 'DESC')) )
			$q['order'] = 'DESC';

		// Order by
		if ( empty($q['orderby']) ) {
			$q['orderby'] = "$wpdb->posts.post_date " . $q['order'];
		} elseif ( 'none' == $q['orderby'] ) {
			$q['orderby'] = '';
		} else {
			// Used to filter values
			$allowed_keys = array('author', 'date', 'title', 'modified', 'menu_order', 'parent', 'ID', 'rand', 'comment_count');
			if ( !empty($q['meta_key']) ) {
				$allowed_keys[] = $q['meta_key'];
				$allowed_keys[] = 'meta_value';
				$allowed_keys[] = 'meta_value_num';
			}
			$q['orderby'] = urldecode($q['orderby']);
			$q['orderby'] = addslashes_gpc($q['orderby']);
			$orderby_array = explode(' ', $q['orderby']);
			$q['orderby'] = '';

			foreach ( $orderby_array as $i => $orderby ) {
				// Only allow certain values for safety
				if ( ! in_array($orderby, $allowed_keys) )
					continue;

				switch ( $orderby ) {
					case 'menu_order':
						break;
					case 'ID':
						$orderby = "$wpdb->posts.ID";
						break;
					case 'rand':
						$orderby = 'RAND()';
						break;
					case $q['meta_key']:
					case 'meta_value':
						$orderby = "$wpdb->postmeta.meta_value";
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

				$q['orderby'] .= (($i == 0) ? '' : ',') . $orderby;
			}

			// append ASC or DESC at the end
			if ( !empty($q['orderby']))
				$q['orderby'] .= " {$q['order']}";

			if ( empty($q['orderby']) )
				$q['orderby'] = "$wpdb->posts.post_date ".$q['order'];
		}

		if ( is_array($post_type) ) {
			$post_type_cap = 'multiple_post_type';
		} else {
			$post_type_object = get_post_type_object ( $post_type );
			if ( !empty($post_type_object) )
				$post_type_cap = $post_type_object->capability_type;
			else
				$post_type_cap = $post_type;
		}

		$exclude_post_types = '';
		$in_search_post_types = get_post_types( array('exclude_from_search' => false) );
		if ( ! empty( $in_search_post_types ) )
			$exclude_post_types .= $wpdb->prepare(" AND $wpdb->posts.post_type IN ('" . join("', '", $in_search_post_types ) . "')");

		if ( 'any' == $post_type ) {
			$where .= $exclude_post_types;
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

		if ( !empty($post_type_object) ) {
			$post_type_cap = $post_type_object->capability_type;
			$edit_cap = $post_type_object->cap->edit_post;
			$read_cap = $post_type_object->cap->read_post;
			$edit_others_cap = $post_type_object->cap->edit_others_posts;
			$read_private_cap = $post_type_object->cap->read_private_posts;
		} else {
			$edit_cap = 'edit_' . $post_type_cap;
			$read_cap = 'read_' . $post_type_cap;
			$edit_others_cap = 'edit_others_' . $post_type_cap . 's';
			$read_private_cap = 'read_private_' . $post_type_cap . 's';
		}

		if ( isset($q['post_status']) && '' != $q['post_status'] ) {
			$statuswheres = array();
			$q_status = explode(',', $q['post_status']);
			$r_status = array();
			$p_status = array();
			$e_status = array();
			if ( $q['post_status'] == 'any' ) {
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
					$statuswheres[] = "($wpdb->posts.post_author = $user_ID " .  "AND (" . join( ' OR ', $r_status ) . "))";
				else
					$statuswheres[] = "(" . join( ' OR ', $r_status ) . ")";
			}
			if ( !empty($p_status) ) {
				if ( !empty($q['perm'] ) && 'readable' == $q['perm'] && !current_user_can($read_private_cap) )
					$statuswheres[] = "($wpdb->posts.post_author = $user_ID " .  "AND (" . join( ' OR ', $p_status ) . "))";
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

			if ( is_admin() ) {
				// Add protected states that should show in the admin all list.
				$admin_all_states = get_post_stati( array('protected' => true, 'show_in_admin_all_list' => true) );
				foreach ( (array) $admin_all_states as $state )
					$where .= " OR $wpdb->posts.post_status = '$state'";
			}

			if ( is_user_logged_in() ) {
				// Add private states that are limited to viewing by the author of a post or someone who has caps to read private states.
				$private_states = get_post_stati( array('private' => true) );
				foreach ( (array) $private_states as $state )
					$where .= current_user_can( $read_private_cap ) ? " OR $wpdb->posts.post_status = '$state'" : " OR $wpdb->posts.post_author = $user_ID AND $wpdb->posts.post_status = '$state'";
			}

			$where .= ')';
		}

		if ( !empty( $q['meta_query'] ) ) {
			list( $meta_join, $meta_where ) = $this->get_meta_sql( $q['meta_query'], $wpdb->posts, 'ID', $wpdb->postmeta, 'post_id' );
			$join .= $meta_join;
			$where .= $meta_where;
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
			if ( empty($page) )
				$page = 1;

			if ( empty($q['offset']) ) {
				$pgstrt = '';
				$pgstrt = ($page - 1) * $q['posts_per_page'] . ', ';
				$limits = 'LIMIT ' . $pgstrt . $q['posts_per_page'];
			} else { // we're ignoring $page and using 'offset'
				$q['offset'] = absint($q['offset']);
				$pgstrt = $q['offset'] . ', ';
				$limits = 'LIMIT ' . $pgstrt . $q['posts_per_page'];
			}
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

		$orderby = $q['orderby'];

		$pieces = array( 'where', 'groupby', 'join', 'orderby', 'distinct', 'fields', 'limits' );

		// Apply post-paging filters on where and join.  Only plugins that
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
			$clauses = apply_filters_ref_array( 'posts_clauses', array( compact( $pieces ), &$this ) );
			foreach ( $pieces as $piece )
				$$piece = isset( $clauses[ $piece ] ) ? $clauses[ $piece ] : '';
		}

		// Announce current selection parameters.  For use by caching plugins.
		do_action( 'posts_selection', $where . $groupby . $orderby . $limits . $join );

		// Filter again for the benefit of caching plugins.  Regular plugins should use the hooks above.
		if ( !$q['suppress_filters'] ) {
			$where		= apply_filters_ref_array( 'posts_where_request',		array( $where, &$this ) );
			$groupby	= apply_filters_ref_array( 'posts_groupby_request',		array( $groupby, &$this ) );
			$join		= apply_filters_ref_array( 'posts_join_request',		array( $join, &$this ) );
			$orderby	= apply_filters_ref_array( 'posts_orderby_request',		array( $orderby, &$this ) );
			$distinct	= apply_filters_ref_array( 'posts_distinct_request',	array( $distinct, &$this ) );
			$fields		= apply_filters_ref_array( 'posts_fields_request',		array( $fields, &$this ) );
			$limits		= apply_filters_ref_array( 'post_limits_request',		array( $limits, &$this ) );

			// Filter all clauses at once, for convenience
			$clauses = apply_filters_ref_array( 'posts_clauses_request', array( compact( $pieces ), &$this ) );
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

		$this->request = " SELECT $found_rows $distinct $fields FROM $wpdb->posts $join WHERE 1=1 $where $groupby $orderby $limits";
		if ( !$q['suppress_filters'] )
			$this->request = apply_filters_ref_array('posts_request', array( $this->request, &$this ) );

		$this->posts = $wpdb->get_results($this->request);
		// Raw results filter.  Prior to status checks.
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

		if ( !$q['no_found_rows'] && !empty($limits) ) {
			$found_posts_query = apply_filters_ref_array( 'found_posts_query', array( 'SELECT FOUND_ROWS()', &$this ) );
			$this->found_posts = $wpdb->get_var( $found_posts_query );
			$this->found_posts = apply_filters_ref_array( 'found_posts', array( $this->found_posts, &$this ) );
			$this->max_num_pages = ceil($this->found_posts / $q['posts_per_page']);
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

			if ( $this->is_preview && current_user_can( $edit_cap, $this->posts[0]->ID ) )
				$this->posts[0] = apply_filters_ref_array('the_preview', array( $this->posts[0], &$this ));
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
					// Increment the sticky offset.  The next sticky will be placed at this offset.
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
				$stickies__in = implode(',', array_map( 'absint', $sticky_posts ));
				// honor post type(s) if not set to any
				$stickies_where = '';
				if ( 'any' != $post_type && '' != $post_type ) {
					if ( is_array( $post_type ) ) {
						$post_types = join( "', '", $post_type );
					} else {
						$post_types = $post_type;
					}
					$stickies_where = "AND $wpdb->posts.post_type IN ('" . $post_types . "')";
				}

				$stickies = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE $wpdb->posts.ID IN ($stickies__in) $stickies_where" );
				foreach ( $stickies as $sticky_post ) {
					// Ignore sticky posts the current user cannot read or are not published.
					if ( 'publish' != $sticky_post->post_status )
						continue;
					array_splice($this->posts, $sticky_offset, 0, array($sticky_post));
					$sticky_offset++;
				}
			}
		}

		if ( !$q['suppress_filters'] )
			$this->posts = apply_filters_ref_array('the_posts', array( $this->posts, &$this ) );

		$this->post_count = count($this->posts);

		// Sanitize before caching so it'll only get done once
		for ( $i = 0; $i < $this->post_count; $i++ ) {
			$this->posts[$i] = sanitize_post($this->posts[$i], 'raw');
		}

		if ( $q['cache_results'] )
			update_post_caches($this->posts, $post_type, $q['update_post_term_cache'], $q['update_post_meta_cache']);

		if ( $this->post_count > 0 ) {
			$this->post = $this->posts[0];
		}

		return $this->posts;
	}

	/**
	 * Set up the next post and iterate current post index.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return object Next post.
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
	function &query($query) {
		$this->parse_query($query);
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

		$this->queried_object = NULL;
		$this->queried_object_id = 0;

		$tax_query = $this->get('tax_query');

		if ( !empty( $tax_query ) ) {
			$query = reset( $tax_query );

			if ( 'term_id' == $query['field'] )
				$term = get_term( reset( $query['terms'] ), $query['taxonomy'] );
			else
				$term = get_term_by( $query['field'], reset( $query['terms'] ), $query['taxonomy'] );

			if ( $term && ! is_wp_error($term) )  {
				$this->queried_object = $term;
				$this->queried_object_id = $term->term_id;
			}
		} elseif ( $this->is_posts_page ) {
			$page_for_posts = get_option('page_for_posts');
			$this->queried_object = & get_page( $page_for_posts );
			$this->queried_object_id = (int) $this->queried_object->ID;
		} elseif ( $this->is_single && !is_null($this->post) ) {
			$this->queried_object = $this->post;
			$this->queried_object_id = (int) $this->post->ID;
		} elseif ( $this->is_page && !is_null($this->post) ) {
			$this->queried_object = $this->post;
			$this->queried_object_id = (int) $this->post->ID;
		} elseif ( $this->is_author ) {
			$author_id = (int) $this->get('author');
			$author = get_userdata($author_id);
			$this->queried_object = $author;
			$this->queried_object_id = $author_id;
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
	 * PHP4 type constructor.
	 *
	 * Sets up the WordPress query, if parameter is not empty.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $query URL query string.
	 * @return WP_Query
	 */
	function WP_Query($query = '') {
		if ( ! empty($query) ) {
			$this->query($query);
		}
	}

	/**
 	 * Is the query for an archive page?
 	 *
 	 * Month, Year, Category, Author, ...
 	 *
	 * If the $post_types parameter is specified, this function will additionally
	 * check if the query is for exactly one of the post types specified. If a plugin
	 * is causing multiple post types to appear in the query, specifying a post type
	 * will cause this check to return false.
	 *
 	 * @since 3.1.0
 	 *
	 * @param mixed $post_types Optional. Post type or array of post types
 	 * @return bool
 	 */
	function is_archive( $post_types ) {
		if ( empty( $post_types ) || !$this->is_archive )
			return (bool) $this->is_archive;

		if ( ! isset( $this->posts[0] ) )
			return false;

		$post = $this->posts[0];

		return in_array( $post->post_type, (array) $post_types );
	}

	/**
	 * Is the query for a post type archive page?
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $post_types Optional. Post type or array of posts types to check against.
	 * @return bool
	 */
	function is_post_type_archive( $post_types = '' ) {
		if ( empty( $post_types ) || !$this->is_post_type_archive )
			return (bool) $this->is_post_type_archive;

		if ( ! isset( $this->posts[0] ) )
			return false;

		$post = $this->posts[0];

		return in_array( $post->post_type, (array) $post_types );
	}

	/**
	 * Is the query for an attachment page?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_attachment() {
		return (bool) $this->is_attachment;
	}

	/**
	 * Is the query for an author archive page?
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
	 * Is the query for a category archive page?
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
	 * Is the query for a tag archive page?
	 *
	 * If the $tag parameter is specified, this function will additionally
	 * check if the query is for one of the tags specified.
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $slug Optional. Tag slug or array of slugs.
	 * @return bool
	 */
	function is_tag( $slug = '' ) {
		if ( !$this->is_tag )
			return false;

		if ( empty( $slug ) )
			return true;

		$tag_obj = $this->get_queried_object();

		$slug = (array) $slug;

		if ( in_array( $tag_obj->slug, $slug ) )
			return true;

		return false;
	}

	/**
	 * Is the query for a taxonomy archive page?
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

		if ( empty( $term ) ) // Only a Taxonomy provided
			return isset( $queried_object->taxonomy ) && count( $tax_array ) && in_array( $queried_object->taxonomy, $tax_array );

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
	 * Is the query for a date archive?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_date() {
		return (bool) $this->is_date;
	}


	/**
	 * Is the query for a day archive?
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
	 * @return bool
	 */
	function is_feed() {
		return (bool) $this->is_feed;
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
	 * Is the query for a month archive?
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	function is_month() {
		return (bool) $this->is_month;
	}

	/**
	 * Is the query for a single Page?
	 *
	 * If the $page parameter is specified, this function will additionally
	 * check if the query is for one of the Pages specified.
	 *
	 * @see WP_Query::is_single()
	 * @see WP_Query::is_singular()
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $page Page ID, title, slug, or array of Page IDs, titles, and slugs.
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
	 * Is the query for a single post?
	 *
	 * If the $post parameter is specified, this function will additionally
	 * check if the query is for one of the Posts specified.
	 *
	 * Can also be used for attachments or any other post type except pages.
	 *
	 * @see WP_Query::is_page()
	 * @see WP_Query::is_singular()
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $post Post ID, title, slug, or array of Post IDs, titles, and slugs.
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
	 * Is the query for a single post of any post type (post, attachment, page, ... )?
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
	 * Is the query for a specific year?
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

		wp_redirect($link, '301'); // Permanent redirect
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
function setup_postdata($post) {
	global $id, $authordata, $day, $currentmonth, $page, $pages, $multipage, $more, $numpages;

	$id = (int) $post->ID;

	$authordata = get_userdata($post->post_author);

	$day = mysql2date('d.m.y', $post->post_date, false);
	$currentmonth = mysql2date('m', $post->post_date, false);
	$numpages = 1;
	$page = get_query_var('page');
	if ( !$page )
		$page = 1;
	if ( is_single() || is_page() || is_feed() )
		$more = 1;
	$content = $post->post_content;
	if ( strpos( $content, '<!--nextpage-->' ) ) {
		if ( $page > 1 )
			$more = 1;
		$multipage = 1;
		$content = str_replace("\n<!--nextpage-->\n", '<!--nextpage-->', $content);
		$content = str_replace("\n<!--nextpage-->", '<!--nextpage-->', $content);
		$content = str_replace("<!--nextpage-->\n", '<!--nextpage-->', $content);
		$pages = explode('<!--nextpage-->', $content);
		$numpages = count($pages);
	} else {
		$pages = array( $post->post_content );
		$multipage = 0;
	}

	do_action_ref_array('the_post', array(&$post));

	return true;
}
?>
