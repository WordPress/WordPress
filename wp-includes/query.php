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
 * Is query requesting an archive page.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool True if page is archive.
 */
function is_archive() {
	global $wp_query;

	return $wp_query->is_archive;
}

/**
 * Is query requesting an attachment page.
 *
 * @since 2.0.0
 * @uses $wp_query
 *
 * @return bool True if page is attachment.
 */
function is_attachment() {
	global $wp_query;

	return $wp_query->is_attachment;
}

/**
 * Is query requesting an author page.
 *
 * If the $author parameter is specified then the check will be expanded to
 * include whether the queried author matches the one given in the parameter.
 * You can match against integers and against strings.
 *
 * If matching against an integer, the ID should be used of the author for the
 * test. If the $author is an ID and matches the author page user ID, then
 * 'true' will be returned.
 *
 * If matching against strings, then the test will be matched against both the
 * nickname and user nicename and will return true on success.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param string|int $author Optional. Is current page this author.
 * @return bool True if page is author or $author (if set).
 */
function is_author ($author = '') {
	global $wp_query;

	if ( !$wp_query->is_author )
		return false;

	if ( empty($author) )
		return true;

	$author_obj = $wp_query->get_queried_object();

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
 * Whether current page query contains a category name or given category name.
 *
 * The category list can contain category IDs, names, or category slugs. If any
 * of them are part of the query, then it will return true.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param string|array $category Optional.
 * @return bool
 */
function is_category ($category = '') {
	global $wp_query;

	if ( !$wp_query->is_category )
		return false;

	if ( empty($category) )
		return true;

	$cat_obj = $wp_query->get_queried_object();

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
 * Whether the current page query has the given tag slug or contains tag.
 *
 * @since 2.3.0
 * @uses $wp_query
 *
 * @param string|array $slug Optional. Single tag or list of tags to check for.
 * @return bool
 */
function is_tag( $slug = '' ) {
	global $wp_query;

	if ( !$wp_query->is_tag )
		return false;

	if ( empty( $slug ) )
		return true;

	$tag_obj = $wp_query->get_queried_object();

	$slug = (array) $slug;

	if ( in_array( $tag_obj->slug, $slug ) )
		return true;

	return false;
}

/**
 * Whether the current query is for the given taxonomy and/or term.
 *
 * If no taxonomy argument is set, returns true if any taxonomy is queried.
 * If the taxonomy argument is passed but no term argument, returns true
 *    if the taxonomy or taxonomies in the argument are being queried.
 * If both taxonomy and term arguments are passed, returns true
 *    if the current query is for a term contained in the terms argument
 *    which has a taxonomy contained in the taxonomy argument.
 *
 * @since 2.5.0
 * @uses $wp_query
 *
 * @param string|array $taxonomy Optional. Taxonomy slug or slugs to check in current query.
 * @param int|array|string $term. Optional. A single or array of, The term's ID, Name or Slug
 * @return bool
 */
function is_tax( $taxonomy = '', $term = '' ) {
	global $wp_query, $wp_taxonomies;

	$queried_object = $wp_query->get_queried_object();
	$tax_array = array_intersect(array_keys($wp_taxonomies), (array) $taxonomy);
	$term_array = (array) $term;

	if ( !$wp_query->is_tax )
		return false;

	if ( empty( $taxonomy ) )
		return true;

	if ( empty( $term ) ) // Only a Taxonomy provided
		return isset($queried_object->taxonomy) && count( $tax_array ) && in_array($queried_object->taxonomy, $tax_array);

	return isset($queried_object->term_id) &&
			count(array_intersect(
				array($queried_object->term_id, $queried_object->name, $queried_object->slug),
				$term_array
			));
}

/**
 * Whether the current URL is within the comments popup window.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_comments_popup() {
	global $wp_query;

	return $wp_query->is_comments_popup;
}

/**
 * Whether current URL is based on a date.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_date() {
	global $wp_query;

	return $wp_query->is_date;
}

/**
 * Whether current blog URL contains a day.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_day() {
	global $wp_query;

	return $wp_query->is_day;
}

/**
 * Whether current page query is feed URL.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_feed() {
	global $wp_query;

	return $wp_query->is_feed;
}

/**
 * Whether current page query is comment feed URL.
 *
 * @since 3.0.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_comment_feed() {
	global $wp_query;

	return $wp_query->is_comment_feed;
}

/**
 * Whether current page query is the front of the site.
 *
 * @since 2.5.0
 * @uses is_home()
 * @uses get_option()
 *
 * @return bool True, if front of site.
 */
function is_front_page() {
	// most likely case
	if ( 'posts' == get_option('show_on_front') && is_home() )
		return true;
	elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') && is_page(get_option('page_on_front')) )
		return true;
	else
		return false;
}

/**
 * Whether current page view is the blog homepage.
 *
 * This is the page which is showing the time based blog content of your site
 * so if you set a static page for the front page of your site then this will
 * only be true on the page which you set as the "Posts page" in Reading Settings.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool True if blog view homepage.
 */
function is_home() {
	global $wp_query;

	return $wp_query->is_home;
}

/**
 * Whether current page query contains a month.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_month() {
	global $wp_query;

	return $wp_query->is_month;
}

/**
 * Whether query is page or contains given page(s).
 *
 * Calls the function without any parameters will only test whether the current
 * query is of the page type. Either a list or a single item can be tested
 * against for whether the query is a page and also is the value or one of the
 * values in the page parameter.
 *
 * The parameter can contain the page ID, page title, or page name. The
 * parameter can also be an array of those three values.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param mixed $page Either page or list of pages to test against.
 * @return bool
 */
function is_page ($page = '') {
	global $wp_query;

	if ( !$wp_query->is_page )
		return false;

	if ( empty($page) )
		return true;

	$page_obj = $wp_query->get_queried_object();

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
 * Whether query contains multiple pages for the results.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_paged() {
	global $wp_query;

	return $wp_query->is_paged;
}

/**
 * Whether the current page was created by a plugin.
 *
 * The plugin can set this by using the global $plugin_page and setting it to
 * true.
 *
 * @since 1.5.0
 * @global bool $plugin_page Used by plugins to tell the query that current is a plugin page.
 *
 * @return bool
 */
function is_plugin_page() {
	global $plugin_page;

	if ( isset($plugin_page) )
		return true;

	return false;
}

/**
 * Whether the current query is preview of post or page.
 *
 * @since 2.0.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_preview() {
	global $wp_query;

	return $wp_query->is_preview;
}

/**
 * Whether the current query post is robots.
 *
 * @since 2.1.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_robots() {
	global $wp_query;

	return $wp_query->is_robots;
}

/**
 * Whether current query is the result of a user search.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_search() {
	global $wp_query;

	return $wp_query->is_search;
}

/**
 * Whether the current page query is single page.
 *
 * The parameter can contain the post ID, post title, or post name. The
 * parameter can also be an array of those three values.
 *
 * This applies to other post types, attachments, pages, posts. Just means that
 * the current query has only a single object.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @param mixed $post Either post or list of posts to test against.
 * @return bool
 */
function is_single($post = '') {
	global $wp_query;

	if ( !$wp_query->is_single )
		return false;

	if ( empty( $post) )
		return true;

	$post_obj = $wp_query->get_queried_object();

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
 * Whether is single post, is a page, or is an attachment.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_singular() {
	global $wp_query;

	return $wp_query->is_singular;
}

/**
 * Whether the query contains a time.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_time() {
	global $wp_query;

	return $wp_query->is_time;
}

/**
 * Whether the query is a trackback.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_trackback() {
	global $wp_query;

	return $wp_query->is_trackback;
}

/**
 * Whether the query contains a year.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool
 */
function is_year() {
	global $wp_query;

	return $wp_query->is_year;
}

/**
 * Whether current page query is a 404 and no results for WordPress query.
 *
 * @since 1.5.0
 * @uses $wp_query
 *
 * @return bool True, if nothing is found matching WordPress Query.
 */
function is_404() {
	global $wp_query;

	return $wp_query->is_404;
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
class WP_Query {

	/**
	 * Query string
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	var $query;

	/**
	 * Query search variables set by the user.
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
	 * Resets query flags to false.
	 *
	 * The query flags are what page info WordPress was able to figure out.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function init_query_flags() {
		$this->is_single = false;
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
		$this->is_paged = false;
		$this->is_admin = false;
		$this->is_attachment = false;
		$this->is_singular = false;
		$this->is_robots = false;
		$this->is_posts_page = false;
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
	function parse_query($query ) {
		if ( !empty($query) || !isset($this->query) ) {
			$this->init();
			if ( is_array($query) )
				$this->query_vars = $query;
			else
				parse_str($query, $this->query_vars);
			$this->query = $query;
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
		} elseif ( !empty($qv['s']) ) {
			$this->is_search = true;
		} else {
		// Look for archive queries.  Dates, categories, authors.

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

			if ( empty($qv['cat']) || ($qv['cat'] == '0') ) {
				$this->is_category = false;
			} else {
				if ( strpos($qv['cat'], '-') !== false ) {
					$this->is_category = false;
				} else {
					$this->is_category = true;
				}
			}

			if ( '' != $qv['category_name'] ) {
				$this->is_category = true;
			}

			if ( !is_array($qv['category__in']) || empty($qv['category__in']) ) {
				$qv['category__in'] = array();
			} else {
				$qv['category__in'] = array_map('absint', $qv['category__in']);
				$this->is_category = true;
			}

			if ( !is_array($qv['category__not_in']) || empty($qv['category__not_in']) ) {
				$qv['category__not_in'] = array();
			} else {
				$qv['category__not_in'] = array_map('absint', $qv['category__not_in']);
			}

			if ( !is_array($qv['category__and']) || empty($qv['category__and']) ) {
				$qv['category__and'] = array();
			} else {
				$qv['category__and'] = array_map('absint', $qv['category__and']);
				$this->is_category = true;
			}

			if (  '' != $qv['tag'] )
				$this->is_tag = true;

			$qv['tag_id'] = absint($qv['tag_id']);
			if (  !empty($qv['tag_id']) )
				$this->is_tag = true;

			if ( !is_array($qv['tag__in']) || empty($qv['tag__in']) ) {
				$qv['tag__in'] = array();
			} else {
				$qv['tag__in'] = array_map('absint', $qv['tag__in']);
				$this->is_tag = true;
			}

			if ( !is_array($qv['tag__not_in']) || empty($qv['tag__not_in']) ) {
				$qv['tag__not_in'] = array();
			} else {
				$qv['tag__not_in'] = array_map('absint', $qv['tag__not_in']);
			}

			if ( !is_array($qv['tag__and']) || empty($qv['tag__and']) ) {
				$qv['tag__and'] = array();
			} else {
				$qv['tag__and'] = array_map('absint', $qv['tag__and']);
				$this->is_category = true;
			}

			if ( !is_array($qv['tag_slug__in']) || empty($qv['tag_slug__in']) ) {
				$qv['tag_slug__in'] = array();
			} else {
				$qv['tag_slug__in'] = array_map('sanitize_title', $qv['tag_slug__in']);
				$this->is_tag = true;
			}

			if ( !is_array($qv['tag_slug__and']) || empty($qv['tag_slug__and']) ) {
				$qv['tag_slug__and'] = array();
			} else {
				$qv['tag_slug__and'] = array_map('sanitize_title', $qv['tag_slug__and']);
				$this->is_tag = true;
			}

			if ( empty($qv['taxonomy']) || empty($qv['term']) ) {
				$this->is_tax = false;
				foreach ( $GLOBALS['wp_taxonomies'] as $taxonomy => $t ) {
					if ( $t->query_var && isset($qv[$t->query_var]) && '' != $qv[$t->query_var] ) {
						$qv['taxonomy'] = $taxonomy;
						$qv['term'] = $qv[$t->query_var];
						$this->is_tax = true;
						break;
					}
				}
			} else {
				$this->is_tax = true;
			}

			if ( empty($qv['author']) || ($qv['author'] == '0') ) {
				$this->is_author = false;
			} else {
				$this->is_author = true;
			}

			if ( '' != $qv['author_name'] ) {
				$this->is_author = true;
			}

			if ( ($this->is_date || $this->is_author || $this->is_category || $this->is_tag || $this->is_tax) )
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
				$qv['post_type'] = array_map('sanitize_user', $qv['post_type'], array(true));
			else
				$qv['post_type'] = sanitize_user($qv['post_type'], true);
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
		global $wpdb, $user_ID;

		do_action_ref_array('pre_get_posts', array(&$this));

		// Shorthand.
		$q = &$this->query_vars;

		$q = $this->fill_query_vars($q);

		// First let's clear some variables
		$distinct = '';
		$whichcat = '';
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

		if ( !isset($q['caller_get_posts']) )
			$q['caller_get_posts'] = false;

		if ( !isset($q['suppress_filters']) )
			$q['suppress_filters'] = false;

		if ( !isset($q['cache_results']) )
			$q['cache_results'] = true;

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
		$search = apply_filters_ref_array('posts_search', array( $search, &$this ) );

		// Category stuff

		if ( empty($q['cat']) || ($q['cat'] == '0') ||
				// Bypass cat checks if fetching specific posts
				$this->is_singular ) {
			$whichcat = '';
		} else {
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
					$q['category__in'] = array_merge($q['category__in'], get_term_children($cat, 'category'));
				} else {
					$q['category__not_in'][] = $cat;
					$q['category__not_in'] = array_merge($q['category__not_in'], get_term_children($cat, 'category'));
				}
			}
			$q['cat'] = implode(',', $req_cats);
		}

		if ( !empty($q['category__in']) ) {
			$join = " INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) ";
			$whichcat .= " AND $wpdb->term_taxonomy.taxonomy = 'category' ";
			$include_cats = "'" . implode("', '", $q['category__in']) . "'";
			$whichcat .= " AND $wpdb->term_taxonomy.term_id IN ($include_cats) ";
		}

		if ( !empty($q['category__not_in']) ) {
			$cat_string = "'" . implode("', '", $q['category__not_in']) . "'";
			$whichcat .= " AND $wpdb->posts.ID NOT IN ( SELECT tr.object_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy = 'category' AND tt.term_id IN ($cat_string) )";
		}

		// Category stuff for nice URLs
		if ( '' != $q['category_name'] && !$this->is_singular ) {
			$q['category_name'] = implode('/', array_map('sanitize_title', explode('/', $q['category_name'])));
			$reqcat = get_category_by_path($q['category_name']);
			$q['category_name'] = str_replace('%2F', '/', urlencode(urldecode($q['category_name'])));
			$cat_paths = '/' . trim($q['category_name'], '/');
			$q['category_name'] = sanitize_title(basename($cat_paths));

			$cat_paths = '/' . trim(urldecode($q['category_name']), '/');
			$q['category_name'] = sanitize_title(basename($cat_paths));
			$cat_paths = explode('/', $cat_paths);
			$cat_path = '';
			foreach ( (array) $cat_paths as $pathdir )
				$cat_path .= ( $pathdir != '' ? '/' : '' ) . sanitize_title($pathdir);

			//if we don't match the entire hierarchy fallback on just matching the nicename
			if ( empty($reqcat) )
				$reqcat = get_category_by_path($q['category_name'], false);

			if ( !empty($reqcat) )
				$reqcat = $reqcat->term_id;
			else
				$reqcat = 0;

			$q['cat'] = $reqcat;

			$join = " INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) ";
			$whichcat = " AND $wpdb->term_taxonomy.taxonomy = 'category' ";
			$in_cats = array($q['cat']);
			$in_cats = array_merge($in_cats, get_term_children($q['cat'], 'category'));
			$in_cats = "'" . implode("', '", $in_cats) . "'";
			$whichcat .= "AND $wpdb->term_taxonomy.term_id IN ($in_cats)";
			$groupby = "{$wpdb->posts}.ID";
		}

		// Tags
		if ( '' != $q['tag'] ) {
			if ( strpos($q['tag'], ',') !== false ) {
				$tags = preg_split('/[,\s]+/', $q['tag']);
				foreach ( (array) $tags as $tag ) {
					$tag = sanitize_term_field('slug', $tag, 0, 'post_tag', 'db');
					$q['tag_slug__in'][] = $tag;
				}
			} else if ( preg_match('/[+\s]+/', $q['tag']) || !empty($q['cat']) ) {
				$tags = preg_split('/[+\s]+/', $q['tag']);
				foreach ( (array) $tags as $tag ) {
					$tag = sanitize_term_field('slug', $tag, 0, 'post_tag', 'db');
					$q['tag_slug__and'][] = $tag;
				}
			} else {
				$q['tag'] = sanitize_term_field('slug', $q['tag'], 0, 'post_tag', 'db');
				$q['tag_slug__in'][] = $q['tag'];
			}
		}

		if ( !empty($q['category__in']) || !empty($q['meta_key']) || !empty($q['tag__in']) || !empty($q['tag_slug__in']) ) {
			$groupby = "{$wpdb->posts}.ID";
		}

		if ( !empty($q['tag__in']) && empty($q['cat']) ) {
			$join = " INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) ";
			$whichcat .= " AND $wpdb->term_taxonomy.taxonomy = 'post_tag' ";
			$include_tags = "'" . implode("', '", $q['tag__in']) . "'";
			$whichcat .= " AND $wpdb->term_taxonomy.term_id IN ($include_tags) ";
			$reqtag = is_term( $q['tag__in'][0], 'post_tag' );
			if ( !empty($reqtag) )
				$q['tag_id'] = $reqtag['term_id'];
		}

		if ( !empty($q['tag_slug__in']) && empty($q['cat']) ) {
			$join = " INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) INNER JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id) ";
			$whichcat .= " AND $wpdb->term_taxonomy.taxonomy = 'post_tag' ";
			$include_tags = "'" . implode("', '", $q['tag_slug__in']) . "'";
			$whichcat .= " AND $wpdb->terms.slug IN ($include_tags) ";
			$reqtag = get_term_by( 'slug', $q['tag_slug__in'][0], 'post_tag' );
			if ( !empty($reqtag) )
				$q['tag_id'] = $reqtag->term_id;
		}

		if ( !empty($q['tag__not_in']) ) {
			$tag_string = "'" . implode("', '", $q['tag__not_in']) . "'";
			$whichcat .= " AND $wpdb->posts.ID NOT IN ( SELECT tr.object_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy = 'post_tag' AND tt.term_id IN ($tag_string) )";
		}

		// Tag and slug intersections.
		$intersections = array('category__and' => 'category', 'tag__and' => 'post_tag', 'tag_slug__and' => 'post_tag', 'tag__in' => 'post_tag', 'tag_slug__in' => 'post_tag');
		$tagin = array('tag__in', 'tag_slug__in'); // These are used to make some exceptions below
		foreach ( $intersections as $item => $taxonomy ) {
			if ( empty($q[$item]) ) continue;
			if ( in_array($item, $tagin) && empty($q['cat']) ) continue; // We should already have what we need if categories aren't being used

			if ( $item != 'category__and' ) {
				$reqtag = is_term( $q[$item][0], 'post_tag' );
				if ( !empty($reqtag) )
					$q['tag_id'] = $reqtag['term_id'];
			}

			if ( in_array( $item, array('tag_slug__and', 'tag_slug__in' ) ) )
				$taxonomy_field = 'slug';
			else
				$taxonomy_field = 'term_id';

			$q[$item] = array_unique($q[$item]);
			$tsql = "SELECT p.ID FROM $wpdb->posts p INNER JOIN $wpdb->term_relationships tr ON (p.ID = tr.object_id) INNER JOIN $wpdb->term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) INNER JOIN $wpdb->terms t ON (tt.term_id = t.term_id)";
			$tsql .= " WHERE tt.taxonomy = '$taxonomy' AND t.$taxonomy_field IN ('" . implode("', '", $q[$item]) . "')";
			if ( !in_array($item, $tagin) ) { // This next line is only helpful if we are doing an and relationship
				$tsql .= " GROUP BY p.ID HAVING count(p.ID) = " . count($q[$item]);
			}
			$post_ids = $wpdb->get_col($tsql);

			if ( count($post_ids) )
				$whichcat .= " AND $wpdb->posts.ID IN (" . implode(', ', $post_ids) . ") ";
			else {
				$whichcat = " AND 0 = 1";
				break;
			}
		}

		// Taxonomies
		if ( $this->is_tax ) {
			if ( '' != $q['taxonomy'] ) {
				$taxonomy = $q['taxonomy'];
				$tt[$taxonomy] = $q['term'];
			} else {
				foreach ( $GLOBALS['wp_taxonomies'] as $taxonomy => $t ) {
					if ( $t->query_var && '' != $q[$t->query_var] ) {
						$tt[$taxonomy] = $q[$t->query_var];
						break;
					}
				}
			}

			$terms = get_terms($taxonomy, array('slug' => $tt[$taxonomy], 'hide_empty' => !is_taxonomy_hierarchical($taxonomy)));

			if ( is_wp_error($terms) || empty($terms) ) {
				$whichcat = " AND 0 ";
			} else {
				foreach ( $terms as $term ) {
					$term_ids[] = $term->term_id;
					if ( is_taxonomy_hierarchical($taxonomy) ) {
						$children = get_term_children($term->term_id, $taxonomy);
						$term_ids = array_merge($term_ids, $children);
					}
				}
				$post_ids = get_objects_in_term($term_ids, $taxonomy);
				if ( !is_wp_error($post_ids) && !empty($post_ids) ) {
					$whichcat .= " AND $wpdb->posts.ID IN (" . implode(', ', $post_ids) . ") ";
					if ( empty($post_type) ) {
						$post_type = 'any';
						$post_status_join = true;
					} elseif ( in_array('attachment', (array)$post_type) ) {
						$post_status_join = true;
					}
					if ( empty($q['post_status']) )
						$q['post_status'] = 'publish';
				} else {
					$whichcat = " AND 0 ";
				}
			}
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

		$where .= $search . $whichcat . $whichauthor . $whichmimetype;

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
			$edit_others_cap = $post_type_object->cap->edit_other_posts;
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

		// postmeta queries
		if ( ! empty($q['meta_key']) || ! empty($q['meta_value']) )
			$join .= " JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
		if ( ! empty($q['meta_key']) )
			$where .= $wpdb->prepare(" AND $wpdb->postmeta.meta_key = %s ", $q['meta_key']);
		if ( ! empty($q['meta_value']) ) {
			if ( empty($q['meta_compare']) || ! in_array($q['meta_compare'], array('=', '!=', '>', '>=', '<', '<=')) )
				$q['meta_compare'] = '=';

			$where .= $wpdb->prepare("AND $wpdb->postmeta.meta_value {$q['meta_compare']} %s ", $q['meta_value']);
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
		}

		// Announce current selection parameters.  For use by caching plugins.
		do_action( 'posts_selection', $where . $groupby . $orderby . $limits . $join );

		// Filter again for the benefit of caching plugins.  Regular plugins should use the hooks above.
		if ( !$q['suppress_filters'] ) {
			$where		= apply_filters_ref_array( 'posts_where_request',	array( $where, &$this ) );
			$groupby	= apply_filters_ref_array( 'posts_groupby_request',		array( $groupby, &$this ) );
			$join		= apply_filters_ref_array( 'posts_join_request',	array( $join, &$this ) );
			$orderby	= apply_filters_ref_array( 'posts_orderby_request',		array( $orderby, &$this ) );
			$distinct	= apply_filters_ref_array( 'posts_distinct_request',	array( $distinct, &$this ) );
			$fields		= apply_filters_ref_array( 'posts_fields_request',		array( $fields, &$this ) );
			$limits		= apply_filters_ref_array( 'post_limits_request',		array( $limits, &$this ) );
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
		if ( $this->is_home && $page <= 1 && is_array($sticky_posts) && !empty($sticky_posts) && !$q['caller_get_posts'] ) {
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

		if ( $this->is_category ) {
			$cat = $this->get('cat');
			$category = &get_category($cat);
			if ( is_wp_error( $category ) )
				return NULL;
			$this->queried_object = &$category;
			$this->queried_object_id = (int) $cat;
		} elseif ( $this->is_tag ) {
			$tag_id = $this->get('tag_id');
			$tag = &get_term($tag_id, 'post_tag');
			if ( is_wp_error( $tag ) )
				return NULL;
			$this->queried_object = &$tag;
			$this->queried_object_id = (int) $tag_id;
		} elseif ( $this->is_tax ) {
			$tax = $this->get('taxonomy');
			$slug = $this->get('term');
			$term = &get_terms($tax, array( 'slug' => $slug, 'hide_empty' => false ) );
			if ( is_wp_error($term) || empty($term) )
				return NULL;
			$term = $term[0];
			$this->queried_object = $term;
			$this->queried_object_id = $term->term_id;
		} elseif ( $this->is_posts_page ) {
			$this->queried_object = & get_page(get_option('page_for_posts'));
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

		$query = "SELECT post_id FROM $wpdb->postmeta, $wpdb->posts WHERE ID = post_id AND meta_key = '_wp_old_slug' AND meta_value='" . $wp_query->query_vars['name'] . "'";

		// if year, monthnum, or day have been specified, make our query more precise
		// just in case there are multiple identical _wp_old_slug values
		if ( '' != $wp_query->query_vars['year'] )
			$query .= " AND YEAR(post_date) = '{$wp_query->query_vars['year']}'";
		if ( '' != $wp_query->query_vars['monthnum'] )
			$query .= " AND MONTH(post_date) = '{$wp_query->query_vars['monthnum']}'";
		if ( '' != $wp_query->query_vars['day'] )
			$query .= " AND DAYOFMONTH(post_date) = '{$wp_query->query_vars['day']}'";

		$id = (int) $wpdb->get_var($query);

		if ( !$id )
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
		$pages[0] = $post->post_content;
		$multipage = 0;
	}

	do_action_ref_array('the_post', array(&$post));

	return true;
}
?>
