<?php
/**
 * Functions for handling JavaScript in the framework.  Themes can add support for the 
 * 'hybrid-core-scripts' feature to allow the framework to handle loading the stylesheets into 
 * the theme header or footer at an appropriate time.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register Hybrid Core scripts. */
add_action( 'wp_enqueue_scripts', 'hybrid_register_scripts', 0 );

/* Load Hybrid Core scripts. */
add_action( 'wp_enqueue_scripts', 'hybrid_enqueue_scripts', 5 );

/**
 * Registers JavaScript files for the framework.  This function merely registers scripts with WordPress using
 * the wp_register_script() function.  It does not load any script files on the site.  If a theme wants to register 
 * its own custom scripts, it should do so on the 'wp_enqueue_scripts' hook.
 *
 * @since  1.2.0
 * @access public
 * @return void
 */
function hybrid_register_scripts() {

	/* Supported JavaScript. */
	$supports = get_theme_support( 'hybrid-core-scripts' );

	/* Get the minified suffix. */
	$suffix = hybrid_get_min_suffix();

	/* Register the 'mobile-toggle' script if the current theme supports 'mobile-toggle'. */
	if ( isset( $supports[0] ) && in_array( 'mobile-toggle', $supports[0] ) )
		wp_register_script( 'mobile-toggle', esc_url( trailingslashit( HYBRID_JS ) . "mobile-toggle{$suffix}.js" ), array( 'jquery' ), '20130528', true );
}

/**
 * Tells WordPress to load the scripts needed for the framework using the wp_enqueue_script() function.
 *
 * @since  1.2.0
 * @access public
 * @return void
 */
function hybrid_enqueue_scripts() {

	/* Supported JavaScript. */
	$supports = get_theme_support( 'hybrid-core-scripts' );

	/* Load the comment reply script on singular posts with open comments if threaded comments are supported. */
	if ( is_singular() && get_option( 'thread_comments' ) && comments_open() )
		wp_enqueue_script( 'comment-reply' );

	/* Load the 'mobile-toggle' script if the current theme supports 'mobile-toggle'. */
	if ( isset( $supports[0] ) && in_array( 'mobile-toggle', $supports[0] ) )
		wp_enqueue_script( 'mobile-toggle' );
}
