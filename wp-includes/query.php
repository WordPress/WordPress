<?php
/**
 * WordPress Query API
 *
 * The query API attempts to get which part of WordPress the user is on. It
 * also provides functionality for getting URL query information.
 *
 * @link https://developer.wordpress.org/themes/basics/the-loop/ More information on The Loop.
 *
 * @package WordPress
 * @subpackage Query
 */

/**
 * Retrieves the value of a query variable in the WP_Query class.
 *
 * @since 1.5.0
 * @since 3.9.0 The `$default_value` argument was introduced.
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param string $query_var     The variable key to retrieve.
 * @param mixed  $default_value Optional. Value to return if the query variable is not set.
 *                              Default empty string.
 * @return mixed Contents of the query variable.
 */
function get_query_var( $query_var, $default_value = '' ) {
	global $wp_query;
	return $wp_query->get( $query_var, $default_value );
}

/**
 * Retrieves the currently queried object.
 *
 * Wrapper for WP_Query::get_queried_object().
 *
 * @since 3.1.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return WP_Term|WP_Post_Type|WP_Post|WP_User|null The queried object.
 */
function get_queried_object() {
	global $wp_query;
	return $wp_query->get_queried_object();
}

/**
 * Retrieves the ID of the currently queried object.
 *
 * Wrapper for WP_Query::get_queried_object_id().
 *
 * @since 3.1.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return int ID of the queried object.
 */
function get_queried_object_id() {
	global $wp_query;
	return $wp_query->get_queried_object_id();
}

/**
 * Sets the value of a query variable in the WP_Query class.
 *
 * @since 2.2.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param string $query_var Query variable key.
 * @param mixed  $value     Query variable value.
 */
function set_query_var( $query_var, $value ) {
	global $wp_query;
	$wp_query->set( $query_var, $value );
}

/**
 * Sets up The Loop with query parameters.
 *
 * Note: This function will completely override the main query and isn't intended for use
 * by plugins or themes. Its overly-simplistic approach to modifying the main query can be
 * problematic and should be avoided wherever possible. In most cases, there are better,
 * more performant options for modifying the main query such as via the {@see 'pre_get_posts'}
 * action within WP_Query.
 *
 * This must not be used within the WordPress Loop.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param array|string $query Array or string of WP_Query arguments.
 * @return WP_Post[]|int[] Array of post objects or post IDs.
 */
function query_posts( $query ) {
	$GLOBALS['wp_query'] = new WP_Query();
	return $GLOBALS['wp_query']->query( $query );
}

/**
 * Destroys the previous query and sets up a new query.
 *
 * This should be used after query_posts() and before another query_posts().
 * This will remove obscure bugs that occur when the previous WP_Query object
 * is not destroyed properly before another is set up.
 *
 * @since 2.3.0
 *
 * @global WP_Query $wp_query     WordPress Query object.
 * @global WP_Query $wp_the_query Copy of the global WP_Query instance created during wp_reset_query().
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
 *
 * @global WP_Query $wp_query WordPress Query object.
 */
function wp_reset_postdata() {
	global $wp_query;

	if ( isset( $wp_query ) ) {
		$wp_query->reset_postdata();
	}
}

/*
 * Query type checks.
 */

/**
 * Determines whether the query is for an existing archive page.
 *
 * Archive pages include category, tag, author, date, custom post type,
 * and custom taxonomy based archives.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @see is_category()
 * @see is_tag()
 * @see is_author()
 * @see is_date()
 * @see is_post_type_archive()
 * @see is_tax()
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for an existing archive page.
 */
function is_archive() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_archive();
}

/**
 * Determines whether the query is for an existing post type archive page.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 3.1.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param string|string[] $post_types Optional. Post type or array of posts types
 *                                    to check against. Default empty.
 * @return bool Whether the query is for an existing post type archive page.
 */
function is_post_type_archive( $post_types = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_post_type_archive( $post_types );
}

/**
 * Determines whether the query is for an existing attachment page.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.0.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param int|string|int[]|string[] $attachment Optional. Attachment ID, title, slug, or array of such
 *                                              to check against. Default empty.
 * @return bool Whether the query is for an existing attachment page.
 */
function is_attachment( $attachment = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_attachment( $attachment );
}

/**
 * Determines whether the query is for an existing author archive page.
 *
 * If the $author parameter is specified, this function will additionally
 * check if the query is for one of the authors specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param int|string|int[]|string[] $author Optional. User ID, nickname, nicename, or array of such
 *                                          to check against. Default empty.
 * @return bool Whether the query is for an existing author archive page.
 */
function is_author( $author = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_author( $author );
}

/**
 * Determines whether the query is for an existing category archive page.
 *
 * If the $category parameter is specified, this function will additionally
 * check if the query is for one of the categories specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param int|string|int[]|string[] $category Optional. Category ID, name, slug, or array of such
 *                                            to check against. Default empty.
 * @return bool Whether the query is for an existing category archive page.
 */
function is_category( $category = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_category( $category );
}

/**
 * Determines whether the query is for an existing tag archive page.
 *
 * If the $tag parameter is specified, this function will additionally
 * check if the query is for one of the tags specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.3.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param int|string|int[]|string[] $tag Optional. Tag ID, name, slug, or array of such
 *                                       to check against. Default empty.
 * @return bool Whether the query is for an existing tag archive page.
 */
function is_tag( $tag = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_tag( $tag );
}

/**
 * Determines whether the query is for an existing custom taxonomy archive page.
 *
 * If the $taxonomy parameter is specified, this function will additionally
 * check if the query is for that specific $taxonomy.
 *
 * If the $term parameter is specified in addition to the $taxonomy parameter,
 * this function will additionally check if the query is for one of the terms
 * specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param string|string[]           $taxonomy Optional. Taxonomy slug or slugs to check against.
 *                                            Default empty.
 * @param int|string|int[]|string[] $term     Optional. Term ID, name, slug, or array of such
 *                                            to check against. Default empty.
 * @return bool Whether the query is for an existing custom taxonomy archive page.
 *              True for custom taxonomy archive pages, false for built-in taxonomies
 *              (category and tag archives).
 */
function is_tax( $taxonomy = '', $term = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_tax( $taxonomy, $term );
}

/**
 * Determines whether the query is for an existing date archive.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for an existing date archive.
 */
function is_date() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_date();
}

/**
 * Determines whether the query is for an existing day archive.
 *
 * A conditional check to test whether the page is a date-based archive page displaying posts for the current day.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for an existing day archive.
 */
function is_day() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_day();
}

/**
 * Determines whether the query is for a feed.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param string|string[] $feeds Optional. Feed type or array of feed types
 *                                         to check against. Default empty.
 * @return bool Whether the query is for a feed.
 */
function is_feed( $feeds = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_feed( $feeds );
}

/**
 * Is the query for a comments feed?
 *
 * @since 3.0.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for a comments feed.
 */
function is_comment_feed() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_comment_feed();
}

/**
 * Determines whether the query is for the front page of the site.
 *
 * This is for what is displayed at your site's main URL.
 *
 * Depends on the site's "Front page displays" Reading Settings 'show_on_front' and 'page_on_front'.
 *
 * If you set a static page for the front page of your site, this function will return
 * true when viewing that page.
 *
 * Otherwise the same as {@see is_home()}.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for the front page of the site.
 */
function is_front_page() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_front_page();
}

/**
 * Determines whether the query is for the blog homepage.
 *
 * The blog homepage is the page that shows the time-based blog content of the site.
 *
 * is_home() is dependent on the site's "Front page displays" Reading Settings 'show_on_front'
 * and 'page_for_posts'.
 *
 * If a static page is set for the front page of the site, this function will return true only
 * on the page you set as the "Posts page".
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @see is_front_page()
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for the blog homepage.
 */
function is_home() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_home();
}

/**
 * Determines whether the query is for the Privacy Policy page.
 *
 * The Privacy Policy page is the page that shows the Privacy Policy content of the site.
 *
 * is_privacy_policy() is dependent on the site's "Change your Privacy Policy page" Privacy Settings 'wp_page_for_privacy_policy'.
 *
 * This function will return true only on the page you set as the "Privacy Policy page".
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 5.2.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for the Privacy Policy page.
 */
function is_privacy_policy() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_privacy_policy();
}

/**
 * Determines whether the query is for an existing month archive.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for an existing month archive.
 */
function is_month() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_month();
}

/**
 * Determines whether the query is for an existing single page.
 *
 * If the $page parameter is specified, this function will additionally
 * check if the query is for one of the pages specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @see is_single()
 * @see is_singular()
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param int|string|int[]|string[] $page Optional. Page ID, title, slug, or array of such
 *                                        to check against. Default empty.
 * @return bool Whether the query is for an existing single page.
 */
function is_page( $page = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_page( $page );
}

/**
 * Determines whether the query is for a paged result and not for the first page.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for a paged result.
 */
function is_paged() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_paged();
}

/**
 * Determines whether the query is for a post or page preview.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.0.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for a post or page preview.
 */
function is_preview() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_preview();
}

/**
 * Is the query for the robots.txt file?
 *
 * @since 2.1.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for the robots.txt file.
 */
function is_robots() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_robots();
}

/**
 * Is the query for the favicon.ico file?
 *
 * @since 5.4.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for the favicon.ico file.
 */
function is_favicon() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_favicon();
}

/**
 * Is the query for a sitemap?
 *
 * @since 7.1.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for a sitemap.
 */
function is_sitemap(): bool {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '7.1.0' );
		return false;
	}

	return $wp_query->is_sitemap();
}

/**
 * Determines whether the query is for random content.
 *
 * True for a blog home, archive or search request that includes the `random` query
 * variable, for example `example.com/random/` or `example.com/random/category/news/`.
 *
 * @since 7.2.0
 *
 * @see wp_is_random_content_query()
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for random content.
 */
function is_random(): bool {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '7.2.0' );
		return false;
	}

	return $wp_query->is_random();
}

/**
 * Determines whether the query is for a search.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for a search.
 */
function is_search() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_search();
}

/**
 * Determines whether the query is for an existing single post.
 *
 * Works for any post type, except attachments and pages
 *
 * If the $post parameter is specified, this function will additionally
 * check if the query is for one of the Posts specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @see is_page()
 * @see is_singular()
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param int|string|int[]|string[] $post Optional. Post ID, title, slug, or array of such
 *                                        to check against. Default empty.
 * @return bool Whether the query is for an existing single post.
 */
function is_single( $post = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_single( $post );
}

/**
 * Determines whether the query is for an existing single post of any post type
 * (post, attachment, page, custom post types).
 *
 * If the $post_types parameter is specified, this function will additionally
 * check if the query is for one of the Posts Types specified.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @see is_page()
 * @see is_single()
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param string|string[] $post_types Optional. Post type or array of post types
 *                                    to check against. Default empty.
 * @return bool Whether the query is for an existing single post
 *              or any of the given post types.
 */
function is_singular( $post_types = '' ) {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_singular( $post_types );
}

/**
 * Determines whether the query is for a specific time.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for a specific time.
 */
function is_time() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_time();
}

/**
 * Determines whether the query is for a trackback endpoint call.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for a trackback endpoint call.
 */
function is_trackback() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_trackback();
}

/**
 * Determines whether the query is for an existing year archive.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for an existing year archive.
 */
function is_year() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_year();
}

/**
 * Determines whether the query has resulted in a 404 (returns no results).
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is a 404 error.
 */
function is_404() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_404();
}

/**
 * Is the query for an embedded post?
 *
 * @since 4.4.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is for an embedded post.
 */
function is_embed() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return $wp_query->is_embed();
}

/**
 * Determines whether the query is the main query.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 3.3.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool Whether the query is the main query.
 */
function is_main_query() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '6.1.0' );
		return false;
	}

	if ( 'pre_get_posts' === current_filter() ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: 1: pre_get_posts, 2: WP_Query->is_main_query(), 3: is_main_query(), 4: Documentation URL. */
				__( 'In %1$s, use the %2$s method, not the %3$s function. See %4$s.' ),
				'<code>pre_get_posts</code>',
				'<code>WP_Query->is_main_query()</code>',
				'<code>is_main_query()</code>',
				__( 'https://developer.wordpress.org/reference/functions/is_main_query/' )
			),
			'3.7.0'
		);
	}

	return $wp_query->is_main_query();
}

/*
 * The Loop. Post loop control.
 */

/**
 * Determines whether current WordPress query has posts to loop over.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @phpstan-impure
 *
 * @return bool True if posts are available, false if end of the loop.
 */
function have_posts() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		return false;
	}

	return $wp_query->have_posts();
}

/**
 * Determines whether the caller is in the Loop.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.0.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @return bool True if caller is within loop, false if loop hasn't started or ended.
 */
function in_the_loop() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		return false;
	}

	return $wp_query->in_the_loop;
}

/**
 * Rewind the loop posts.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 */
function rewind_posts() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		return;
	}

	$wp_query->rewind_posts();
}

/**
 * Iterate the post index in the loop.
 *
 * @since 1.5.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 */
function the_post() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		return;
	}

	$wp_query->the_post();
}

/*
 * Comments loop.
 */

/**
 * Determines whether current WordPress query has comments to loop over.
 *
 * @since 2.2.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @phpstan-impure
 *
 * @return bool True if comments are available, false if no more comments.
 */
function have_comments() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		return false;
	}

	return $wp_query->have_comments();
}

/**
 * Iterate comment index in the comment loop.
 *
 * @since 2.2.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 */
function the_comment() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		return;
	}

	$wp_query->the_comment();
}

/**
 * Redirect old slugs to the correct permalink.
 *
 * Attempts to find the current slug from the past slugs.
 *
 * @since 2.1.0
 */
function wp_old_slug_redirect() {
	if ( is_404() && '' !== get_query_var( 'name' ) ) {
		// Guess the current post type based on the query vars.
		if ( get_query_var( 'post_type' ) ) {
			$post_type = get_query_var( 'post_type' );
		} elseif ( get_query_var( 'attachment' ) ) {
			$post_type = 'attachment';
		} elseif ( get_query_var( 'pagename' ) ) {
			$post_type = 'page';
		} else {
			$post_type = 'post';
		}

		if ( is_array( $post_type ) ) {
			if ( count( $post_type ) > 1 ) {
				return;
			}
			$post_type = reset( $post_type );
		}

		// Do not attempt redirect for hierarchical post types.
		if ( is_post_type_hierarchical( $post_type ) ) {
			return;
		}

		$id = _find_post_by_old_slug( $post_type );

		if ( ! $id ) {
			$id = _find_post_by_old_date( $post_type );
		}

		/**
		 * Filters the old slug redirect post ID.
		 *
		 * @since 4.9.3
		 *
		 * @param int $id The redirect post ID.
		 */
		$id = apply_filters( 'old_slug_redirect_post_id', $id );

		if ( ! $id ) {
			return;
		}

		$link = get_permalink( $id );

		if ( get_query_var( 'paged' ) > 1 ) {
			$link = user_trailingslashit( trailingslashit( $link ) . 'page/' . get_query_var( 'paged' ) );
		} elseif ( is_embed() ) {
			$link = user_trailingslashit( trailingslashit( $link ) . 'embed' );
		}

		/**
		 * Filters the old slug redirect URL.
		 *
		 * @since 4.4.0
		 *
		 * @param string $link The redirect URL.
		 */
		$link = apply_filters( 'old_slug_redirect_url', $link );

		if ( ! $link ) {
			return;
		}

		wp_redirect( $link, 301 ); // Permanent redirect.
		exit;
	}
}

/**
 * Find the post ID for redirecting an old slug.
 *
 * @since 4.9.3
 * @access private
 *
 * @see wp_old_slug_redirect()
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $post_type The current post type based on the query vars.
 * @return int The Post ID.
 */
function _find_post_by_old_slug( $post_type ) {
	global $wpdb;

	$query = $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta, $wpdb->posts WHERE ID = post_id AND post_type = %s AND meta_key = '_wp_old_slug' AND meta_value = %s", $post_type, get_query_var( 'name' ) );

	/*
	 * If year, monthnum, or day have been specified, make our query more precise
	 * just in case there are multiple identical _wp_old_slug values.
	 */
	if ( get_query_var( 'year' ) ) {
		$query .= $wpdb->prepare( ' AND YEAR(post_date) = %d', get_query_var( 'year' ) );
	}
	if ( get_query_var( 'monthnum' ) ) {
		$query .= $wpdb->prepare( ' AND MONTH(post_date) = %d', get_query_var( 'monthnum' ) );
	}
	if ( get_query_var( 'day' ) ) {
		$query .= $wpdb->prepare( ' AND DAYOFMONTH(post_date) = %d', get_query_var( 'day' ) );
	}

	$key          = md5( $query );
	$last_changed = wp_cache_get_last_changed( 'posts' );
	$cache_key    = "find_post_by_old_slug:$key";
	$cache        = wp_cache_get_salted( $cache_key, 'post-queries', $last_changed );
	if ( false !== $cache ) {
		$id = $cache;
	} else {
		$id = (int) $wpdb->get_var( $query );
		wp_cache_set_salted( $cache_key, $id, 'post-queries', $last_changed );
	}

	return $id;
}

/**
 * Find the post ID for redirecting an old date.
 *
 * @since 4.9.3
 * @access private
 *
 * @see wp_old_slug_redirect()
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $post_type The current post type based on the query vars.
 * @return int The Post ID.
 */
function _find_post_by_old_date( $post_type ) {
	global $wpdb;

	$date_query = '';
	if ( get_query_var( 'year' ) ) {
		$date_query .= $wpdb->prepare( ' AND YEAR(pm_date.meta_value) = %d', get_query_var( 'year' ) );
	}
	if ( get_query_var( 'monthnum' ) ) {
		$date_query .= $wpdb->prepare( ' AND MONTH(pm_date.meta_value) = %d', get_query_var( 'monthnum' ) );
	}
	if ( get_query_var( 'day' ) ) {
		$date_query .= $wpdb->prepare( ' AND DAYOFMONTH(pm_date.meta_value) = %d', get_query_var( 'day' ) );
	}

	$id = 0;
	if ( $date_query ) {
		$query        = $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta AS pm_date, $wpdb->posts WHERE ID = post_id AND post_type = %s AND meta_key = '_wp_old_date' AND post_name = %s" . $date_query, $post_type, get_query_var( 'name' ) );
		$key          = md5( $query );
		$last_changed = wp_cache_get_last_changed( 'posts' );
		$cache_key    = "find_post_by_old_date:$key";
		$cache        = wp_cache_get_salted( $cache_key, 'post-queries', $last_changed );
		if ( false !== $cache ) {
			$id = $cache;
		} else {
			$id = (int) $wpdb->get_var( $query );
			if ( ! $id ) {
				// Check to see if an old slug matches the old date.
				$id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts, $wpdb->postmeta AS pm_slug, $wpdb->postmeta AS pm_date WHERE ID = pm_slug.post_id AND ID = pm_date.post_id AND post_type = %s AND pm_slug.meta_key = '_wp_old_slug' AND pm_slug.meta_value = %s AND pm_date.meta_key = '_wp_old_date'" . $date_query, $post_type, get_query_var( 'name' ) ) );
			}
			wp_cache_set_salted( $cache_key, $id, 'post-queries', $last_changed );
		}
	}

	return $id;
}

/**
 * Determines whether random content redirects are enabled.
 *
 * @since 7.2.0
 *
 * @return bool True if random content redirects are enabled, false otherwise.
 */
function wp_is_random_content_redirect_enabled(): bool {
	/**
	 * Filters whether random content redirects are enabled.
	 *
	 * Random content redirects add rewrite rules for URLs such as `/random/`, so the
	 * rewrite rules need to be flushed after changing the value returned by this filter.
	 *
	 * @since 7.2.0
	 *
	 * @param bool $enabled Whether random content redirects are enabled. Default true.
	 */
	return (bool) apply_filters( 'wp_enable_random_content_redirect', true );
}

/**
 * Determines whether a query is a request for random content.
 *
 * A random content request is a front end, main query for the blog home, an archive,
 * or search results that includes the `random` query variable and at least one
 * `randomable` post type. For example
 * `example.com/random/`, `example.com/random/category/news/` or `example.com/?s=foo&random`.
 *
 * Singular (including embeds) and feed requests are never random content requests,
 * nor are requests using an HTTP method other than GET or HEAD.
 *
 * @since 7.2.0
 *
 * @param WP_Query $query The query to check.
 * @return bool True if the query is a request for random content, false otherwise.
 */
function wp_is_random_content_query( WP_Query $query ): bool {
	if (
		! $query->is_random()
		|| ! $query->is_main_query()
		|| is_admin()
		|| ! wp_is_random_content_redirect_enabled()
	) {
		return false;
	}

	$request_method = isset( $_SERVER['REQUEST_METHOD'] ) && is_string( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( $_SERVER['REQUEST_METHOD'] ) : '';
	if ( 'GET' !== $request_method && 'HEAD' !== $request_method ) {
		return false;
	}

	return array() !== wp_get_random_content_post_types( $query );
}

/**
 * Retrieves the post types eligible for selection by a random content request.
 *
 * These are the post types the query would otherwise return, limited to those
 * registered as `randomable`.
 *
 * @since 7.2.0
 *
 * @see register_post_type()
 *
 * @param WP_Query $query The query to retrieve the post types for.
 * @return string[] Post type names eligible for random selection.
 *
 * @phpstan-return list<string>
 */
function wp_get_random_content_post_types( WP_Query $query ): array {
	$post_types = $query->get( 'post_type' );

	if ( empty( $post_types ) && $query->is_tax() && $query->tax_query instanceof WP_Tax_Query ) {
		// Match WP_Query::get_posts(): searchable post types using any of the queried taxonomies.
		$taxonomies = array_keys( $query->tax_query->queried_terms );
		$post_types = array();

		foreach ( get_post_types( array( 'exclude_from_search' => false ) ) as $post_type ) {
			$object_taxonomies = 'attachment' === $post_type ? get_taxonomies_for_attachments() : get_object_taxonomies( $post_type );
			if ( array_intersect( $taxonomies, $object_taxonomies ) ) {
				$post_types[] = $post_type;
			}
		}
	} elseif ( empty( $post_types ) && ! $query->is_search() ) {
		$post_types = array( 'post' );
	}

	if ( empty( $post_types ) || 'any' === $post_types ) {
		$post_types = get_post_types( array( 'exclude_from_search' => false ) );
	}

	$random_post_types = array();

	foreach ( (array) $post_types as $post_type ) {
		// Plugins may set invalid values on the `post_type` query variable.
		if ( ! is_string( $post_type ) ) {
			continue;
		}

		$post_type_object = get_post_type_object( $post_type );

		if ( $post_type_object && $post_type_object->randomable ) {
			$random_post_types[] = $post_type_object->name;
		}
	}

	return $random_post_types;
}

/**
 * Limits the main query to posts eligible for random content requests.
 *
 * The main query is used to select the random post so the request runs the same
 * query, and the same filters, as the archive it is randomizing. As such, callbacks
 * on the {@see 'pre_get_posts'} action may modify the posts eligible for selection,
 * for example to limit the post types or exclude a category:
 *
 *     add_action(
 *         'pre_get_posts',
 *         static function ( WP_Query $query ) {
 *             if ( wp_is_random_content_query( $query ) ) {
 *                 $query->set( 'category__not_in', array( 1 ) );
 *             }
 *         }
 *     );
 *
 * Only randomable post types and published posts without a password are eligible.
 * The number and order of posts are enforced by {@see wp_random_content_post_limits()} and
 * {@see wp_random_content_posts_orderby()}, so callbacks modifying them on archives do not
 * affect the selection.
 *
 * Added to the {@see 'pre_get_posts'} action by {@see WP::handle_random()} while the
 * random content query runs, so it runs after other callbacks.
 *
 * @since 7.2.0
 *
 * @param WP_Query $query The WP_Query instance (passed by reference).
 */
function wp_random_content_pre_get_posts( $query ): void {
	if ( ! wp_is_random_content_query( $query ) ) {
		return;
	}

	$query->set( 'post_type', wp_get_random_content_post_types( $query ) );
	$query->set( 'post_status', 'publish' );
	$query->set( 'has_password', false );
	$query->set( 'ignore_sticky_posts', true );
	$query->set( 'no_found_rows', true );
	$query->set( 'update_post_meta_cache', false );
	$query->set( 'update_post_term_cache', false );
	// The order and number of posts are enforced by filters, which a suppressed query skips.
	$query->set( 'suppress_filters', false );
}

/**
 * Orders the random content query randomly.
 *
 * Added to the {@see 'posts_orderby'} filter by {@see WP::handle_random()} while the random
 * content query runs. Enforcing the order in SQL prevents {@see 'pre_get_posts'}
 * callbacks setting the order of posts on archives from affecting the selection.
 *
 * @since 7.2.0
 *
 * @param string   $orderby The ORDER BY clause of the query.
 * @param WP_Query $query   The WP_Query instance.
 * @return string The ORDER BY clause of the query.
 */
function wp_random_content_posts_orderby( $orderby, WP_Query $query ): string {
	if ( wp_is_random_content_query( $query ) ) {
		return 'RAND()';
	}

	return (string) $orderby;
}

/**
 * Limits the random content query to a single post.
 *
 * Added to the {@see 'post_limits'} filter by {@see WP::handle_random()} while the random
 * content query runs. Enforcing the limit in SQL prevents {@see 'pre_get_posts'}
 * callbacks setting the number of posts, offset or page on archives from affecting
 * the selection.
 *
 * @since 7.2.0
 *
 * @param string   $limits The LIMIT clause of the query.
 * @param WP_Query $query  The WP_Query instance.
 * @return string The LIMIT clause of the query.
 */
function wp_random_content_post_limits( $limits, WP_Query $query ): string {
	if ( wp_is_random_content_query( $query ) ) {
		return 'LIMIT 0, 1';
	}

	return (string) $limits;
}

/**
 * Prevents the random content query from being split into multiple database queries.
 *
 * Randomly ordered queries are not cached, so there is no benefit to querying the post
 * IDs separately to the post objects. Disabling the split allows the random post to be
 * retrieved in a single database query.
 *
 * @since 7.2.0
 *
 * @param bool     $split_the_query Whether or not to split the query.
 * @param WP_Query $query           The WP_Query instance.
 * @return bool Whether or not to split the query.
 */
function wp_random_content_split_the_query( $split_the_query, WP_Query $query ): bool {
	if ( wp_is_random_content_query( $query ) ) {
		return false;
	}

	return (bool) $split_the_query;
}

/**
 * Redirects random content requests to the randomly selected post.
 *
 * Called by {@see WP::handle_random()} immediately after the main query selects the
 * random post, to avoid unnecessary processing before the redirect. As the redirect
 * is sent before {@see WP::send_headers()}, the headers preventing the redirect being
 * cached or indexed are sent with it.
 *
 * If no post is selected, for example on an empty archive, {@see WP::handle_random()}
 * handles the request as a 404. If the post's permalink is on another site, the
 * request is handled as a regular archive request.
 *
 * @since 7.2.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 */
function wp_random_content_redirect(): void {
	global $wp_query;

	if ( ! wp_is_random_content_query( $wp_query ) || ! $wp_query->post instanceof WP_Post ) {
		return;
	}

	$location = get_permalink( $wp_query->post );

	// Only consider redirecting permitted locations so wp_safe_redirect doesn't redirect to the fallback.
	if ( ! $location || ! wp_validate_redirect( $location, '' ) ) {
		return;
	}

	nocache_headers();

	if ( ! headers_sent() ) {
		header( 'X-Robots-Tag: noindex, follow' );
	}

	if ( wp_safe_redirect( $location, 302 ) ) {
		exit;
	}
}

/**
 * Flushes the rewrite rules when a page or post at the random content base changes.
 *
 * A published page at the random base, for example `example.com/random/`, takes
 * precedence over random content redirects. So does a published post when the
 * permalink structure places posts at the same URL, for example `/%postname%/`,
 * see {@see WP_Rewrite::post_permalinks_share_random_base()}. As the rewrite rules account
 * for the page or post, they need to be regenerated when it is published,
 * unpublished, renamed, moved or deleted.
 *
 * Runs on the {@see 'wp_after_insert_post'} and {@see 'after_delete_post'} actions.
 *
 * @since 7.2.0
 *
 * @see WP_Rewrite::random_rewrite_rules()
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 *
 * @param int          $post_id     Post ID.
 * @param WP_Post      $post        Post object.
 * @param bool         $update      Optional. Whether this is an existing post being updated. Default false.
 * @param WP_Post|null $post_before Optional. Post object before the update, null for new posts. Default null.
 */
function wp_random_content_flush_rewrite_rules_for_post( int $post_id, WP_Post $post, bool $update = false, ?WP_Post $post_before = null ): void {
	global $wp_rewrite;

	if ( ! $wp_rewrite->using_permalinks() || ! wp_is_random_content_redirect_enabled() ) {
		return;
	}

	foreach ( array( $post, $post_before ) as $page ) {
		if (
			$page instanceof WP_Post
			&& $wp_rewrite->get_random_base() === $page->post_name
			&& (
				( 'page' === $page->post_type && 0 === (int) $page->post_parent )
				|| ( 'post' === $page->post_type && $wp_rewrite->post_permalinks_share_random_base() )
			)
		) {
			flush_rewrite_rules( false );
			return;
		}
	}
}

/**
 * Set up global post data.
 *
 * @since 1.5.0
 * @since 4.4.0 Added the ability to pass a post ID to `$post`.
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param WP_Post|object|int $post WP_Post instance or Post ID/object.
 * @return bool True when finished.
 */
function setup_postdata( $post ) {
	global $wp_query;

	if ( ! empty( $wp_query ) && $wp_query instanceof WP_Query ) {
		return $wp_query->setup_postdata( $post );
	}

	return false;
}

/**
 * Generates post data.
 *
 * @since 5.2.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param WP_Post|object|int $post WP_Post instance or Post ID/object.
 * @return array|false Elements of post, or false on failure.
 */
function generate_postdata( $post ) {
	global $wp_query;

	if ( ! empty( $wp_query ) && $wp_query instanceof WP_Query ) {
		return $wp_query->generate_postdata( $post );
	}

	return false;
}
