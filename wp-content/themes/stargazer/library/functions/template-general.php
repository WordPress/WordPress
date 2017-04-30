<?php
/**
 * General template functions.  These functions are for use throughout the theme's various template files.  
 * Their main purpose is to handle many of the template tags that are currently lacking in core WordPress.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Outputs the link back to the site.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_site_link() {
	echo hybrid_get_site_link();
}

/**
 * Returns a link back to the site.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_site_link() {
	return sprintf( '<a class="site-link" href="%s" rel="home">%s</a>', esc_url( home_url() ), get_bloginfo( 'name' ) );
}

/**
 * Displays a link to WordPress.org.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_wp_link() {
	echo hybrid_get_wp_link();
}

/**
 * Returns a link to WordPress.org.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_wp_link() {
	return sprintf( '<a class="wp-link" href="http://wordpress.org" title="%s">%s</a>', esc_attr__( 'State-of-the-art semantic personal publishing platform', 'hybrid-core' ), __( 'WordPress', 'hybrid-core' ) );
}

/**
 * Displays a link to the parent theme URI.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_theme_link() {
	echo hybrid_get_theme_link();
}

/**
 * Returns a link to the parent theme URI.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_theme_link() {
	$theme = wp_get_theme( get_template() );
	$uri   = $theme->get( 'ThemeURI' );
	$name  = $theme->display( 'Name', false, true );

	/* Translators: Theme name. */
	$title = sprintf( __( '%s WordPress Theme', 'hybrid-core' ), $name );

	return sprintf( '<a class="theme-link" href="%s" title="%s">%s</a>', esc_url( $uri ), esc_attr( $title ), $name );
}

/**
 * Displays a link to the child theme URI.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_child_theme_link() {
	echo hybrid_get_child_theme_link();
}

/**
 * Returns a link to the child theme URI.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_child_theme_link() {

	if ( !is_child_theme() )
		return '';

	$theme = wp_get_theme();
	$uri   = $theme->get( 'ThemeURI' );
	$name  = $theme->display( 'Name', false, true );

	/* Translators: Theme name. */
	$title = sprintf( __( '%s WordPress Theme', 'hybrid-core' ), $name );

	return sprintf( '<a class="child-link" href="%s" title="%s">%s</a>', esc_url( $uri ), esc_attr( $title ), $name );
}

/**
 * Gets the "blog" (posts page) page URL.  `home_url()` will not always work for this because it 
 * returns the front page URL.  Sometimes the blog page URL is set to a different page.  This 
 * function handles both scenarios.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_blog_url() {
	$blog_url = '';

	if ( 'posts' === get_option( 'show_on_front' ) )
		$blog_url = home_url();

	elseif ( 0 < ( $page_for_posts = get_option( 'page_for_posts' ) ) )
		$blog_url = get_permalink( $page_for_posts );

	return $blog_url;
}

/**
 * Outputs the site title. 
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function hybrid_site_title() {
	echo hybrid_get_site_title();
}

/**
 * Returns the linked site title wrapped in an `<h1>` tag.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_site_title() {

	if ( $title = get_bloginfo( 'name' ) )
		$title = sprintf( '<h1 %s><a href="%s" rel="home">%s</a></h1>', hybrid_get_attr( 'site-title' ), home_url(), $title );

	return apply_filters( 'hybrid_site_title', $title );
}

/**
 * Outputs the site description.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function hybrid_site_description() {
	echo hybrid_get_site_description();
}

/**
 * Returns the site description wrapped in an `<h2>` tag.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_site_description() {

	if ( $desc = get_bloginfo( 'description' ) )
		$desc = sprintf( '<h2 %s>%s</h2>', hybrid_get_attr( 'site-description' ), $desc );

	return apply_filters( 'hybrid_site_description', $desc );
}

/**
 * Outputs the loop title.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_loop_title() {
	echo hybrid_get_loop_title();
}

/**
 * Gets the loop title.  This function should only be used on archive-type pages, such as archive, blog, and 
 * search results pages.  It outputs the title of the page.
 *
 * @link   http://core.trac.wordpress.org/ticket/21995
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_loop_title() {

	$loop_title = '';

	if ( is_home() && !is_front_page() )
		$loop_title = get_post_field( 'post_title', get_queried_object_id() );

	elseif ( is_category() ) 
		$loop_title = single_cat_title( '', false );

	elseif ( is_tag() )
		$loop_title = single_tag_title( '', false );

	elseif ( is_tax() )
		$loop_title = single_term_title( '', false );

	elseif ( is_author() )
		$loop_title = hybrid_single_author_title( '', false );

	elseif ( is_search() )
		$loop_title = hybrid_search_title( '', false );

	elseif ( is_post_type_archive() )
		$loop_title = post_type_archive_title( '', false );

	elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
		$loop_title = hybrid_single_minute_hour_title( '', false );

	elseif ( get_query_var( 'minute' ) )
		$loop_title = hybrid_single_minute_title( '', false );

	elseif ( get_query_var( 'hour' ) )
		$loop_title = hybrid_single_hour_title( '', false );

	elseif ( is_day() )
		$loop_title = hybrid_single_day_title( '', false );

	elseif ( get_query_var( 'w' ) )
		$loop_title = hybrid_single_week_title( '', false );

	elseif ( is_month() )
		$loop_title = single_month_title( ' ', false );

	elseif ( is_year() )
		$loop_title = hybrid_single_year_title( '', false );

	elseif ( is_archive() )
		$loop_title = hybrid_single_archive_title( '', false );

	return apply_filters( 'hybrid_loop_title', $loop_title );
}

/**
 * Outputs the loop description.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_loop_description() {
	echo hybrid_get_loop_description();
}

/**
 * Gets the loop description.  This function should only be used on archive-type pages, such as archive, blog, and 
 * search results pages.  It outputs the description of the page.
 *
 * @link   http://core.trac.wordpress.org/ticket/21995
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_loop_description() {

	$loop_desc = '';

	if ( is_home() && !is_front_page() )
		$loop_desc = get_post_field( 'post_content', get_queried_object_id(), 'raw' );

	elseif ( is_category() )
		$loop_desc = get_term_field( 'description', get_queried_object_id(), 'category', 'raw' );

	elseif ( is_tag() )
		$loop_desc = get_term_field( 'description', get_queried_object_id(), 'post_tag', 'raw' );

	elseif ( is_tax() )
		$loop_desc = get_term_field( 'description', get_queried_object_id(), get_query_var( 'taxonomy' ), 'raw' );

	elseif ( is_author() )
		$loop_desc = get_the_author_meta( 'description', get_query_var( 'author' ) );

	elseif ( is_search() )
		$loop_desc = sprintf( __( 'You are browsing the search results for &#8220;%s&#8221;', 'hybrid-core' ), get_search_query() );

	elseif ( is_post_type_archive() )
		$loop_desc = get_post_type_object( get_query_var( 'post_type' ) )->description;

	elseif ( is_time() )
		$loop_desc = __( 'You are browsing the site archives by time.', 'hybrid-core' );

	elseif ( is_day() )
		$loop_desc = sprintf( __( 'You are browsing the site archives for %s.', 'hybrid-core' ), hybrid_single_day_title( '', false ) );

	elseif ( is_month() )
		$loop_desc = sprintf( __( 'You are browsing the site archives for %s.', 'hybrid-core' ), single_month_title( ' ', false ) );

	elseif ( is_year() )
		$loop_desc = sprintf( __( 'You are browsing the site archives for %s.', 'hybrid-core' ), hybrid_single_year_title( '', false ) );

	elseif ( is_archive() )
		$loop_desc = __( 'You are browsing the site archives.', 'hybrid-core' );

	return apply_filters( 'hybrid_loop_description', $loop_desc );
}

/**
 * Retrieve the general archive title.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function hybrid_single_archive_title( $prefix = '', $display = true ) {

	$title = $prefix . __( 'Archives', 'hybrid-core' );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the author archive title.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function hybrid_single_author_title( $prefix = '', $display = true ) {

	$title = $prefix . get_the_author_meta( 'display_name', get_query_var( 'author' ) );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the year archive title.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function hybrid_single_year_title( $prefix = '', $display = true ) {

	$title = $prefix . get_the_date( _x( 'Y', 'yearly archives date format', 'hybrid-core' ) );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the week archive title.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function hybrid_single_week_title( $prefix = '', $display = true ) {

	/* Translators: 1 is the week number and 2 is the year. */
	$title = $prefix . sprintf( __( 'Week %1$s of %2$s', 'hybrid-core' ), get_the_time( _x( 'W', 'weekly archives date format', 'hybrid-core' ) ), get_the_time( _x( 'Y', 'yearly archives date format', 'hybrid-core' ) ) );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the day archive title.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function hybrid_single_day_title( $prefix = '', $display = true ) {

	$title = $prefix . get_the_date( _x( 'F j, Y', 'daily archives date format', 'hybrid-core' ) );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the hour archive title.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function hybrid_single_hour_title( $prefix = '', $display = true ) {

	$title = $prefix . get_the_time( _x( 'g a', 'hour archives time format', 'hybrid-core' ) );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the minute archive title.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function hybrid_single_minute_title( $prefix = '', $display = true ) {

	/* Translators: Minute archive title. %s is the minute time format. */
	$title = $prefix . sprintf( __( 'Minute %s', 'hybrid-core' ), get_the_time( _x( 'i', 'minute archives time format', 'hybrid-core' ) ) );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the minute + hour archive title.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function hybrid_single_minute_hour_title( $prefix = '', $display = true ) {

	$title = $prefix . get_the_time( _x( 'g:i a', 'minute and hour archives time format', 'hybrid-core' ) );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the search results title.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function hybrid_search_title( $prefix = '', $display = true ) {

	/* Translators: %s is the search query. The HTML entities are opening and closing curly quotes. */
	$title = $prefix . sprintf( __( 'Search results for &#8220;%s&#8221;', 'hybrid-core' ), get_search_query() );

	if ( false === $display )
		return $title;

	echo $title;
}

/**
 * Retrieve the 404 page title.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $prefix
 * @param  bool    $display
 * @return string
 */
function hybrid_404_title( $prefix = '', $display = true ) {

	$title = $prefix . __( '404 Not Found', 'hybrid-core' );

	if ( false === $display )
		return $title;

	echo $title;
}
