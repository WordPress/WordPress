<?php
/**
 * Hybrid Core - A WordPress theme development framework.
 *
 * Hybrid Core is a framework for developing WordPress themes.  The framework allows theme developers
 * to quickly build themes without having to handle all of the "logic" behind the theme or having to code 
 * complex functionality for features that are often needed in themes.  The framework does these things 
 * for developers to allow them to get back to what matters the most:  developing and designing themes.  
 * The framework was built to make it easy for developers to include (or not include) specific, pre-coded 
 * features.  Themes handle all the markup, style, and scripts while the framework handles the logic.
 *
 * Hybrid Core is a modular system, which means that developers can pick and choose the features they 
 * want to include within their themes.  Many files are only loaded if the theme registers support for the 
 * feature using the add_theme_support( $feature ) function within their theme.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write 
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package   HybridCore
 * @version   2.1.0-dev
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2014, Justin Tadlock
 * @link      http://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( !class_exists( 'Hybrid' ) ) {

	/**
	 * The Hybrid class launches the framework.  It's the organizational structure behind the entire framework. 
	 * This class should be loaded and initialized before anything else within the theme is called to properly use 
	 * the framework.  
	 *
	 * After parent themes call the Hybrid class, they should perform a theme setup function on the 
	 * 'after_setup_theme' hook with a priority of 10.  Child themes should add their theme setup function on
	 * the 'after_setup_theme' hook with a priority of 11.  This allows the class to load theme-supported features
	 * at the appropriate time, which is on the 'after_setup_theme' hook with a priority of 12.
	 *
	 * Note that while it is possible to extend this class, it's not usually recommended unless you absolutely 
	 * know what you're doing and expect your sub-class to break on updates.  This class often gets modifications 
	 * between versions.
	 *
	 * @since  0.7.0
	 * @access public
	 */
	class Hybrid {

		/**
		 * Constructor method for the Hybrid class.  This method adds other methods of the class to 
		 * specific hooks within WordPress.  It controls the load order of the required files for running 
		 * the framework.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		function __construct() {
			global $hybrid;

			/* Set up an empty class for the global $hybrid object. */
			$hybrid = new stdClass;

			/* Define framework, parent theme, and child theme constants. */
			add_action( 'after_setup_theme', array( $this, 'constants' ), 1 );

			/* Load the core functions/classes required by the rest of the framework. */
			add_action( 'after_setup_theme', array( $this, 'core' ), 2 );

			/* Initialize the framework's default actions and filters. */
			add_action( 'after_setup_theme', array( $this, 'default_filters' ), 3 );

			/* Handle theme supported features. */
			add_action( 'after_setup_theme', array( $this, 'theme_support' ), 12 );

			/* Load framework includes. */
			add_action( 'after_setup_theme', array( $this, 'includes' ), 13 );

			/* Load the framework extensions. */
			add_action( 'after_setup_theme', array( $this, 'extensions' ), 14 );

			/* Language functions and translations setup. */
			add_action( 'after_setup_theme', array( $this, 'i18n' ), 25 );

			/* Load admin files. */
			add_action( 'wp_loaded', array( $this, 'admin' ) );
		}

		/**
		 * Defines the constant paths for use within the core framework, parent theme, and child theme.  
		 * Constants prefixed with 'HYBRID_' are for use only within the core framework and don't 
		 * reference other areas of the parent or child theme.
		 *
		 * @since  0.7.0
		 * @access public
		 * @return void
		 */
		function constants() {

			/* Sets the framework version number. */
			define( 'HYBRID_VERSION', '2.1.0' );

			/* Sets the path to the parent theme directory. */
			define( 'THEME_DIR', get_template_directory() );

			/* Sets the path to the parent theme directory URI. */
			define( 'THEME_URI', get_template_directory_uri() );

			/* Sets the path to the child theme directory. */
			define( 'CHILD_THEME_DIR', get_stylesheet_directory() );

			/* Sets the path to the child theme directory URI. */
			define( 'CHILD_THEME_URI', get_stylesheet_directory_uri() );

			/* Sets the path to the core framework directory. */
			if ( !defined( 'HYBRID_DIR' ) )
				define( 'HYBRID_DIR', trailingslashit( THEME_DIR ) . basename( dirname( __FILE__ ) ) );

			/* Sets the path to the core framework directory URI. */
			if ( !defined( 'HYBRID_URI' ) )
				define( 'HYBRID_URI', trailingslashit( THEME_URI ) . basename( dirname( __FILE__ ) ) );

			/* Sets the path to the core framework admin directory. */
			define( 'HYBRID_ADMIN', trailingslashit( HYBRID_DIR ) . 'admin' );

			/* Sets the path to the core framework classes directory. */
			define( 'HYBRID_CLASSES', trailingslashit( HYBRID_DIR ) . 'classes' );

			/* Sets the path to the core framework extensions directory. */
			define( 'HYBRID_EXTENSIONS', trailingslashit( HYBRID_DIR ) . 'extensions' );

			/* Sets the path to the core framework functions directory. */
			define( 'HYBRID_FUNCTIONS', trailingslashit( HYBRID_DIR ) . 'functions' );

			/* Sets the path to the core framework languages directory. */
			define( 'HYBRID_LANGUAGES', trailingslashit( HYBRID_DIR ) . 'languages' );

			/* Sets the path to the core framework images directory URI. */
			define( 'HYBRID_IMAGES', trailingslashit( HYBRID_URI ) . 'images' );

			/* Sets the path to the core framework CSS directory URI. */
			define( 'HYBRID_CSS', trailingslashit( HYBRID_URI ) . 'css' );

			/* Sets the path to the core framework JavaScript directory URI. */
			define( 'HYBRID_JS', trailingslashit( HYBRID_URI ) . 'js' );
		}

		/**
		 * Loads the core framework files.  These files are needed before loading anything else in the 
		 * framework because they have required functions for use.  Many of the files run filters that 
		 * theme authors may wish to remove in their theme setup functions.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		function core() {

			/* Load the core framework functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'core.php' );

			/* Load the context-based functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'context.php' );

			/* Load the core framework internationalization functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'i18n.php' );

			/* Load the framework customize functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'customize.php' );

			/* Load the framework filters. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'filters.php' );

			/* Load the <head> functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'head.php' );

			/* Load media-related functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'media.php' );

			/* Load the metadata functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'meta.php' );

			/* Load the sidebar functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'sidebars.php' );

			/* Load the scripts functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'scripts.php' );

			/* Load the styles functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'styles.php' );

			/* Load the utility functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'utility.php' );
		}

		/**
		 * Loads both the parent and child theme translation files.  If a locale-based functions file exists
		 * in either the parent or child theme (child overrides parent), it will also be loaded.  All translation 
		 * and locale functions files are expected to be within the theme's '/languages' folder, but the 
		 * framework will fall back on the theme root folder if necessary.  Translation files are expected 
		 * to be prefixed with the template or stylesheet path (example: 'templatename-en_US.mo').
		 *
		 * @since  1.2.0
		 * @access public
		 * @return void
		 */
		function i18n() {
			global $hybrid;

			/* Get parent and child theme textdomains. */
			$parent_textdomain = hybrid_get_parent_textdomain();
			$child_textdomain  = hybrid_get_child_textdomain();

			/* Load theme textdomain. */
			$hybrid->textdomain_loaded[ $parent_textdomain ] = load_theme_textdomain( $parent_textdomain );

			/* Load child theme textdomain. */
			$hybrid->textdomain_loaded[ $child_textdomain ] = is_child_theme() ? load_child_theme_textdomain( $child_textdomain ) : false;

			/* Load the framework textdomain. */
			$hybrid->textdomain_loaded['hybrid-core'] = hybrid_load_framework_textdomain( 'hybrid-core' );

			/* Load empty textdomain mofiles for extensions (these will be overwritten). */
			if ( current_theme_supports( 'breadcrumb-trail' ) ) load_textdomain( 'breadcrumb-trail', '' );
			if ( current_theme_supports( 'post-stylesheets' ) ) load_textdomain( 'post-stylesheets', '' );
			if ( current_theme_supports( 'theme-layouts'    ) ) load_textdomain( 'theme-layouts',    '' );

			/* Get the user's locale. */
			$locale = get_locale();

			/* Locate a locale-specific functions file. */
			$locale_functions = locate_template( array( "languages/{$locale}.php", "{$locale}.php" ) );

			/* If the locale file exists and is readable, load it. */
			if ( !empty( $locale_functions ) && is_readable( $locale_functions ) )
				require_once( $locale_functions );
		}

		/**
		 * Removes theme supported features from themes in the case that a user has a plugin installed
		 * that handles the functionality.
		 *
		 * @since  1.3.0
		 * @access public
		 * @return void
		 */
		function theme_support() {

			/* Adds core WordPress HTML5 support. */
			add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );

			/* Remove support for the the Breadcrumb Trail extension if the plugin is installed. */
			if ( function_exists( 'breadcrumb_trail' ) || class_exists( 'Breadcrumb_Trail' ) )
				remove_theme_support( 'breadcrumb-trail' );

			/* Remove support for the the Cleaner Gallery extension if the plugin is installed. */
			if ( function_exists( 'cleaner_gallery' ) || class_exists( 'Cleaner_Gallery' ) )
				remove_theme_support( 'cleaner-gallery' );

			/* Remove support for the the Get the Image extension if the plugin is installed. */
			if ( function_exists( 'get_the_image' ) || class_exists( 'Get_The_Image' ) )
				remove_theme_support( 'get-the-image' );

			/* Remove support for the Featured Header extension if the class exists. */
			if ( class_exists( 'Featured_Header' ) )
				remove_theme_support( 'featured-header' );

			/* Remove support for the Random Custom Background extension if the class exists. */
			if ( class_exists( 'Random_Custom_Background' ) )
				remove_theme_support( 'random-custom-background' );
		}

		/**
		 * Loads the framework files supported by themes and template-related functions/classes.  Functionality 
		 * in these files should not be expected within the theme setup function.
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		function includes() {

			/* Load the HTML attributes functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'attr.php' );

			/* Load the template functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'template.php' );

			/* Load the comments functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'template-comments.php' );

			/* Load the general template functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'template-general.php' );

			/* Load the media template functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'template-media.php' );

			/* Load the post template functions. */
			require_once( trailingslashit( HYBRID_FUNCTIONS ) . 'template-post.php' );

			/* Load the media meta class. */
			require_once( trailingslashit( HYBRID_CLASSES ) . 'hybrid-media-meta.php' );

			/* Load the media grabber class. */
			require_once( trailingslashit( HYBRID_CLASSES ) . 'hybrid-media-grabber.php' );

			/* Load the theme settings functions if supported. */
			require_if_theme_supports( 'hybrid-core-theme-settings', trailingslashit( HYBRID_FUNCTIONS ) . 'settings.php' );

			/* Load the shortcodes if supported. */
			require_if_theme_supports( 'hybrid-core-shortcodes', trailingslashit( HYBRID_FUNCTIONS ) . 'shortcodes.php' );

			/* Load the template hierarchy if supported. */
			require_if_theme_supports( 'hybrid-core-template-hierarchy', trailingslashit( HYBRID_FUNCTIONS ) . 'template-hierarchy.php' );

			/* Load the post format functionality if post formats are supported. */
			require_if_theme_supports( 'post-formats', trailingslashit( HYBRID_FUNCTIONS ) . 'post-formats.php' );

			/* Load the deprecated functions if supported. */
			require_if_theme_supports( 'hybrid-core-deprecated', trailingslashit( HYBRID_FUNCTIONS ) . 'deprecated.php' );
		}

		/**
		 * Load extensions (external projects).  Extensions are projects that are included within the 
		 * framework but are not a part of it.  They are external projects developed outside of the 
		 * framework.  Themes must use add_theme_support( $extension ) to use a specific extension 
		 * within the theme.  This should be declared on 'after_setup_theme' no later than a priority of 11.
		 *
		 * @since  0.7.0
		 * @access public
		 * @return void
		 */
		function extensions() {

			/* Load the Breadcrumb Trail extension if supported. */
			require_if_theme_supports( 'breadcrumb-trail', trailingslashit( HYBRID_EXTENSIONS ) . 'breadcrumb-trail.php' );

			/* Load the Cleaner Gallery extension if supported. */
			require_if_theme_supports( 'cleaner-gallery', trailingslashit( HYBRID_EXTENSIONS ) . 'cleaner-gallery.php' );

			/* Load the Get the Image extension if supported. */
			require_if_theme_supports( 'get-the-image', trailingslashit( HYBRID_EXTENSIONS ) . 'get-the-image.php' );

			/* Load the Cleaner Caption extension if supported. */
			require_if_theme_supports( 'cleaner-caption', trailingslashit( HYBRID_EXTENSIONS ) . 'cleaner-caption.php' );

			/* Load the Loop Pagination extension if supported. */
			require_if_theme_supports( 'loop-pagination', trailingslashit( HYBRID_EXTENSIONS ) . 'loop-pagination.php' );

			/* Load the Theme Layouts extension if supported. */
			require_if_theme_supports( 'theme-layouts', trailingslashit( HYBRID_EXTENSIONS ) . 'theme-layouts.php' );

			/* Load the Post Stylesheets extension if supported. */
			require_if_theme_supports( 'post-stylesheets', trailingslashit( HYBRID_EXTENSIONS ) . 'post-stylesheets.php' );

			/* Load the Featured Header extension if supported. */
			require_if_theme_supports( 'featured-header', trailingslashit( HYBRID_EXTENSIONS ) . 'featured-header.php' );

			/* Load the Random Custom Background extension if supported. */
			require_if_theme_supports( 'random-custom-background', trailingslashit( HYBRID_EXTENSIONS ) . 'random-custom-background.php' );
		}

		/**
		 * Load admin files for the framework.
		 *
		 * @since  0.7.0
		 * @access public
		 * @return void
		 */
		function admin() {

			/* Check if in the WordPress admin. */
			if ( is_admin() ) {

				/* Load the main admin file. */
				require_once( trailingslashit( HYBRID_ADMIN ) . 'admin.php' );

				/* Load the theme settings feature if supported. */
				require_if_theme_supports( 'hybrid-core-theme-settings', trailingslashit( HYBRID_ADMIN ) . 'theme-settings.php' );
			}
		}

		/**
		 * Adds the default framework actions and filters.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		function default_filters() {
			global $wp_embed;

			/* Remove bbPress theme compatibility if current theme supports bbPress. */
			if ( current_theme_supports( 'bbpress' ) )
				remove_action( 'bbp_init', 'bbp_setup_theme_compat', 8 );

			/* Move the WordPress generator to a better priority. */
			remove_action( 'wp_head', 'wp_generator' );
			add_action( 'wp_head', 'wp_generator', 1 );

			/* Make text widgets shortcode aware. */
			add_filter( 'widget_text', 'do_shortcode' );

			/* Don't strip tags on single post titles. */
			remove_filter( 'single_post_title', 'strip_tags' );

			/* Use same default filters as 'the_content' with a little more flexibility. */
			add_filter( 'hybrid_loop_description', array( $wp_embed, 'run_shortcode' ),   5 );
			add_filter( 'hybrid_loop_description', array( $wp_embed, 'autoembed'     ),   5 );
			add_filter( 'hybrid_loop_description',                   'wptexturize',       10 );
			add_filter( 'hybrid_loop_description',                   'convert_smilies',   15 );
			add_filter( 'hybrid_loop_description',                   'convert_chars',     20 );
			add_filter( 'hybrid_loop_description',                   'wpautop',           25 );
			add_filter( 'hybrid_loop_description',                   'do_shortcode',      30 );
			add_filter( 'hybrid_loop_description',                   'shortcode_unautop', 35 );

			/* Filters for the audio transcript. */
			add_filter( 'hybrid_audio_transcript', 'wptexturize',   10 );
			add_filter( 'hybrid_audio_transcript', 'convert_chars', 20 );
			add_filter( 'hybrid_audio_transcript', 'wpautop',       25 );
		}
	}
}
