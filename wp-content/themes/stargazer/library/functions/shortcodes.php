<?php
/**
 * Shortcodes bundled for use with themes.  These shortcodes are not meant to be used with the post content 
 * editor.  Their purpose is to make it easier for users to filter hooks without having to know too much PHP code
 * and to provide access to specific functionality in other (non-post content) shortcode-aware areas.  To use 
 * the shortcodes, a theme must register support for 'hybrid-core-shortcodes'.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register shortcodes. */
add_action( 'init', 'hybrid_add_shortcodes' );

/**
 * Creates new shortcodes for use in any shortcode-ready area.  This function uses the add_shortcode() 
 * function to register new shortcodes with WordPress.
 *
 * @since  0.8.0
 * @access public
 * @return void
 */
function hybrid_add_shortcodes() {

	/* Add theme-specific shortcodes. */
	add_shortcode( 'the-year',   'hybrid_the_year_shortcode' );
	add_shortcode( 'site-link',  'hybrid_site_link_shortcode' );
	add_shortcode( 'wp-link',    'hybrid_wp_link_shortcode' );
	add_shortcode( 'theme-link', 'hybrid_theme_link_shortcode' );
	add_shortcode( 'child-link', 'hybrid_child_link_shortcode' );
}

/**
 * Shortcode to display the current year.
 *
 * @since  0.6.0
 * @access public
 * @return string
 */
function hybrid_the_year_shortcode() {
	return date_i18n( 'Y' );
}

/**
 * Shortcode to display a link back to the site.
 *
 * @since  0.6.0
 * @access public
 * @return string
 */
function hybrid_site_link_shortcode() {
	return hybrid_get_site_link();
}

/**
 * Shortcode to display a link to WordPress.org.
 *
 * @since  0.6.0
 * @access public
 * @return string
 */
function hybrid_wp_link_shortcode() {
	return hybrid_get_wp_link();
}

/**
 * Shortcode to display a link to the parent theme page.
 *
 * @since  0.6.0
 * @access public
 * @return string
 */
function hybrid_theme_link_shortcode() {
	return hybrid_get_theme_link();
}

/**
 * Shortcode to display a link to the child theme's page.
 *
 * @since  0.6.0
 * @access public
 * @return string
 */
function hybrid_child_link_shortcode() {
	return hybrid_get_child_theme_link();
}
