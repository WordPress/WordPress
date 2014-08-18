<?php
/**
 * Additional helper functions that the framework or themes may use.  The functions in this file are functions
 * that don't really have a home within any other parts of the framework.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add extra support for post types. */
add_action( 'init', 'hybrid_add_post_type_support' );

/* Filters the title for untitled posts. */
add_filter( 'the_title', 'hybrid_untitled_post' );

/**
 * This function is for adding extra support for features not default to the core post types.
 * Excerpts are added to the 'page' post type.  Comments and trackbacks are added for the
 * 'attachment' post type.  Technically, these are already used for attachments in core, but 
 * they're not registered.
 *
 * @since 0.8.0
 * @access public
 * @return void
 */
function hybrid_add_post_type_support() {

	/* Add support for excerpts to the 'page' post type. */
	add_post_type_support( 'page', array( 'excerpt' ) );

	/* Add thumbnail support for audio and video attachments. */
	add_post_type_support( 'attachment:audio', 'thumbnail' );
	add_post_type_support( 'attachment:video', 'thumbnail' );
}

/**
 * Checks if a post of any post type has a custom template.  This is the equivalent of WordPress' 
 * is_page_template() function with the exception that it works for all post types.
 *
 * @since  1.2.0
 * @access public
 * @param  string  $template  The name of the template to check for.
 * @return bool               Whether the post has a template.
 */
function hybrid_has_post_template( $template = '' ) {

	/* Assume we're viewing a singular post. */
	if ( is_singular() ) {

		/* Get the queried object. */
		$post = get_queried_object();

		/* Get the post template, which is saved as metadata. */
		$post_template = get_post_meta( get_queried_object_id(), "_wp_{$post->post_type}_template", true );

		/* If a specific template was input, check that the post template matches. */
		if ( !empty( $template) && ( $template == $post_template ) )
			return true;

		/* If no specific template was input, check if the post has a template. */
		elseif ( empty( $template) && !empty( $post_template ) )
			return true;
	}

	/* Return false for everything else. */
	return false;
}

/**
 * The WordPress.org theme review requires that a link be provided to the single post page for untitled 
 * posts.  This is a filter on 'the_title' so that an '(Untitled)' title appears in that scenario, allowing 
 * for the normal method to work.
 *
 * @since  1.6.0
 * @access public
 * @param  string  $title
 * @return string
 */
function hybrid_untitled_post( $title ) {

	if ( empty( $title ) && !is_singular() && in_the_loop() && !is_admin() ) {

		/* Translators: Used as a placeholder for untitled posts on non-singular views. */
		$title = __( '(Untitled)', 'hybrid-core' );
	}

	return $title;
}

/**
 * Retrieves the file with the highest priority that exists.  The function searches both the stylesheet 
 * and template directories.  This function is similar to the locate_template() function in WordPress 
 * but returns the file name with the URI path instead of the directory path.
 *
 * @since  1.5.0
 * @access public
 * @link   http://core.trac.wordpress.org/ticket/18302
 * @param  array  $file_names The files to search for.
 * @return string
 */
function hybrid_locate_theme_file( $file_names ) {

	$located = '';

	/* Loops through each of the given file names. */
	foreach ( (array) $file_names as $file ) {

		/* If the file exists in the stylesheet (child theme) directory. */
		if ( is_child_theme() && file_exists( trailingslashit( get_stylesheet_directory() ) . $file ) ) {
			$located = trailingslashit( get_stylesheet_directory_uri() ) . $file;
			break;
		}

		/* If the file exists in the template (parent theme) directory. */
		elseif ( file_exists( trailingslashit( get_template_directory() ) . $file ) ) {
			$located = trailingslashit( get_template_directory_uri() ) . $file;
			break;
		}
	}

	return $located;
}

/**
 * Converts a hex color to RGB.  Returns the RGB values as an array.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $hex
 * @return array
 */
function hybrid_hex_to_rgb( $hex ) {

	/* Remove "#" if it was added. */
	$color = trim( $hex, '#' );

	/* If the color is three characters, convert it to six. */
        if ( 3 === strlen( $color ) )
		$color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];

	/* Get the red, green, and blue values. */
	$red   = hexdec( $color[0] . $color[1] );
	$green = hexdec( $color[2] . $color[3] );
	$blue  = hexdec( $color[4] . $color[5] );

	/* Return the RGB colors as an array. */
	return array( 'r' => $red, 'g' => $green, 'b' => $blue );
}

/**
 * Function for grabbing a WP nav menu theme location name.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $location
 * @return string
 */
function hybrid_get_menu_location_name( $location ) {

	$locations = get_registered_nav_menus();

	return $locations[ $location ];
}

/**
 * Function for grabbing a dynamic sidebar name.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $sidebar_id
 * @return string
 */
function hybrid_get_sidebar_name( $sidebar_id ) {
	global $wp_registered_sidebars;

	if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) )
		return $wp_registered_sidebars[ $sidebar_id ]['name'];
}

/**
 * Helper function for getting the script/style `.min` suffix for minified files.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function hybrid_get_min_suffix() {
	return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
}
