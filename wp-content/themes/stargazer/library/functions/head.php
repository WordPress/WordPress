<?php
/**
 * Functions for outputting common site data in the `<head>` area of a site.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Adds common theme items to <head>. */
add_action( 'wp_head', 'hybrid_meta_charset',  0 );
add_action( 'wp_head', 'hybrid_doctitle',      0 );
add_action( 'wp_head', 'hybrid_meta_viewport', 1 );
add_action( 'wp_head', 'hybrid_meta_template', 1 );
add_action( 'wp_head', 'hybrid_link_pingback', 3 );

/* Filter the WordPress title. */
add_filter( 'wp_title', 'hybrid_wp_title', 1, 3 );

/**
 * Generates the relevant template info.  Adds template meta with theme version.  Uses the theme 
 * name and version from style.css.
 * filter hook.
 *
 * @since  0.4.0
 * @access public
 * @return void
 */
function hybrid_meta_template() {
	$theme    = wp_get_theme( get_template() );
	$template = sprintf( '<meta name="template" content="%s %s" />' . "\n", esc_attr( $theme->get( 'Name' ) ), esc_attr( $theme->get( 'Version' ) ) );

	echo apply_filters( 'hybrid_meta_template', $template );
}

/**
 * Adds the meta charset to the header.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_meta_charset() {
	printf( '<meta charset="%s" />' . "\n", get_bloginfo( 'charset' ) );
}

/**
 * Adds the title to the header.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_doctitle() {
	printf( "<title>%s</title>\n", wp_title( ':', false ) );
}

/**
 * Adds the meta viewport to the header.
 *
 * @since  2.0.0
 * @access public
 */
function hybrid_meta_viewport() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";
}

/**
 * Adds the pingback link to the header.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function hybrid_link_pingback() {
	if ( 'open' === get_option( 'default_ping_status' ) )
		printf( '<link rel="pingback" href="%s" />' . "\n", get_bloginfo( 'pingback_url' ) );
}

/**
 * Filters the `wp_title` output early.
 *
 * @since  2.0.0
 * @access publc
 * @param  string  $title
 * @param  string  $separator
 * @param  string  $seplocation
 * @return string
 */
function hybrid_wp_title( $doctitle, $separator, $seplocation ) {

	if ( is_front_page() )
		$doctitle = get_bloginfo( 'name' ) . $separator . ' ' . get_bloginfo( 'description' );

	elseif ( is_home() || is_singular() )
		$doctitle = single_post_title( '', false );

	elseif ( is_category() ) 
		$doctitle = single_cat_title( '', false );

	elseif ( is_tag() )
		$doctitle = single_tag_title( '', false );

	elseif ( is_tax() )
		$doctitle = single_term_title( '', false );

	elseif ( is_post_type_archive() )
		$doctitle = post_type_archive_title( '', false );

	elseif ( is_author() )
		$doctitle = hybrid_single_author_title( '', false );

	elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
		$doctitle = hybrid_single_minute_hour_title( '', false );

	elseif ( get_query_var( 'minute' ) )
		$doctitle = hybrid_single_minute_title( '', false );

	elseif ( get_query_var( 'hour' ) )
		$doctitle = hybrid_single_hour_title( '', false );

	elseif ( is_day() )
		$doctitle = hybrid_single_day_title( '', false );

	elseif ( get_query_var( 'w' ) )
		$doctitle = hybrid_single_week_title( '', false );

	elseif ( is_month() )
		$doctitle = single_month_title( ' ', false );

	elseif ( is_year() )
		$doctitle = hybrid_single_year_title( '', false );

	elseif ( is_archive() )
		$doctitle = hybrid_single_archive_title( '', false );

	elseif ( is_search() )
		$doctitle = hybrid_search_title( '', false );

	elseif ( is_404() )
		$doctitle = hybrid_404_title( '', false );

	/* If the current page is a paged page. */
	if ( ( ( $page = get_query_var( 'paged' ) ) || ( $page = get_query_var( 'page' ) ) ) && $page > 1 )
		/* Translators: 1 is the page title. 2 is the page number. */
		$doctitle = sprintf( __( '%1$s Page %2$s', 'hybrid-core' ), $doctitle . $separator, number_format_i18n( absint( $page ) ) );

	/* Trim separator + space from beginning and end. */
	$doctitle = trim( strip_tags( $doctitle ), "{$separator} " );

	return $doctitle;
}
