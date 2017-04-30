<?php
/**
 * Functions for loading template parts.  These functions are helper functions or more flexible functions 
 * than what core WordPress currently offers with template part loading.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Loads a post content template based off the post type and/or the post format.  This functionality is 
 * not feasible with the WordPress get_template_part() function, so we have to rely on some custom logic 
 * and locate_template().
 *
 * Note that using this function assumes that you're creating a content template to handle attachments. 
 * This filter must be removed since we're bypassing the WP template hierarchy and focusing on templates 
 * specific to the content.
 *
 * @since  1.6.0
 * @access public
 * @return string
 */
function hybrid_get_content_template() {

	/* Set up an empty array and get the post type. */
	$templates = array();
	$post_type = get_post_type();

	/* Assume the theme developer is creating an attachment template. */
	if ( 'attachment' === $post_type ) {
		remove_filter( 'the_content', 'prepend_attachment' );

		$mime_type = get_post_mime_type();

		list( $type, $subtype ) = false !== strpos( $mime_type, '/' ) ? explode( '/', $mime_type ) : array( $mime_type, '' );

		$templates[] = "content-attachment-{$type}.php";
		$templates[] = "content/attachment-{$type}.php";
	}

	/* If the post type supports 'post-formats', get the template based on the format. */
	if ( post_type_supports( $post_type, 'post-formats' ) ) {

		/* Get the post format. */
		$post_format = get_post_format() ? get_post_format() : 'standard';

		/* Template based off post type and post format. */
		$templates[] = "content-{$post_type}-{$post_format}.php";
		$templates[] = "content/{$post_type}-{$post_format}.php";

		/* Template based off the post format. */
		$templates[] = "content-{$post_format}.php";
		$templates[] = "content/{$post_format}.php";
	}

	/* Template based off the post type. */
	$templates[] = "content-{$post_type}.php";
	$templates[] = "content/{$post_type}.php";

	/* Fallback 'content.php' template. */
	$templates[] = 'content.php';
	$templates[] = 'content/content.php';

	/* Allow devs to filter the content template hierarchy. */
	$templates = apply_filters( 'hybrid_content_template_hierarchy', $templates );

	/* Apply filters and return the found content template. */
	include( apply_filters( 'hybrid_content_template', locate_template( $templates, false, false ) ) );
}

/**
 * A function for loading a menu template.  This works similar to the WordPress `get_*()` template functions. 
 * It's purpose is for loading a menu template part.  This function looks for menu templates within the 
 * `menu` sub-folder or the root theme folder.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function hybrid_get_menu( $name = '' ) {

	$templates = array();

	if ( '' !== $name ) {
		$templates[] = "menu-{$name}.php";
		$templates[] = "menu/{$name}.php";
	}

	$templates[] = 'menu.php';
	$templates[] = 'menu/menu.php';

	locate_template( $templates, true );
}

/**
 * This is a replacement function for the WordPress `get_header()` function. The reason for this function 
 * over the core function is because the core function does not provide the functionality needed to properly 
 * implement what's needed, particularly the ability to add header templates to a sub-directory.  
 * Technically, there's a workaround for that using the `get_header` hook, but it requires keeping a 
 * an empty `header.php` template in the theme's root, which will get loaded every time a header template 
 * gets loaded.  That's kind of nasty hack, which leaves us with this function.  This is the **only** 
 * clean solution currently possible.
 *
 * This function maintains compatibility with the core `get_header()` function.  It does so in two ways: 
 * 1) The `get_header` hook is properly fired and 2) The core naming convention of header templates 
 * (`header-$name.php` and `header.php`) is preserved and given a higher priority than custom templates.
 *
 * @link http://core.trac.wordpress.org/ticket/15086
 * @link http://core.trac.wordpress.org/ticket/18676
 *
 * @since  2.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function hybrid_get_header( $name = null ) {

	do_action( 'get_header', $name ); // Core WordPress hook

	$templates = array();

	if ( '' !== $name ) {
		$templates[] = "header-{$name}.php";
		$templates[] = "header/{$name}.php";
	}

	$templates[] = 'header.php';
	$templates[] = 'header/header.php';

	locate_template( $templates, true );
}

/**
 * This is a replacement function for the WordPress `get_footer()` function. The reason for this function 
 * over the core function is because the core function does not provide the functionality needed to properly 
 * implement what's needed, particularly the ability to add footer templates to a sub-directory.  
 * Technically, there's a workaround for that using the `get_footer` hook, but it requires keeping a 
 * an empty `footer.php` template in the theme's root, which will get loaded every time a footer template 
 * gets loaded.  That's kind of nasty hack, which leaves us with this function.  This is the **only** 
 * clean solution currently possible.
 *
 * This function maintains compatibility with the core `get_footer()` function.  It does so in two ways: 
 * 1) The `get_footer` hook is properly fired and 2) The core naming convention of footer templates 
 * (`footer-$name.php` and `footer.php`) is preserved and given a higher priority than custom templates.
 *
 * @link http://core.trac.wordpress.org/ticket/15086
 * @link http://core.trac.wordpress.org/ticket/18676
 *
 * @since  2.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function hybrid_get_footer( $name = null ) {

	do_action( 'get_footer', $name ); // Core WordPress hook

	$templates = array();

	if ( '' !== $name ) {
		$templates[] = "footer-{$name}.php";
		$templates[] = "footer/{$name}.php";
	}

	$templates[] = 'footer.php';
	$templates[] = 'footer/footer.php';

	locate_template( $templates, true );
}

/**
 * This is a replacement function for the WordPress `get_sidebar()` function. The reason for this function 
 * over the core function is because the core function does not provide the functionality needed to properly 
 * implement what's needed, particularly the ability to add sidebar templates to a sub-directory.  
 * Technically, there's a workaround for that using the `get_sidebar` hook, but it requires keeping a 
 * an empty `sidebar.php` template in the theme's root, which will get loaded every time a sidebar template 
 * gets loaded.  That's kind of nasty hack, which leaves us with this function.  This is the **only** 
 * clean solution currently possible.
 *
 * This function maintains compatibility with the core `get_sidebar()` function.  It does so in two ways: 
 * 1) The `get_sidebar` hook is properly fired and 2) The core naming convention of sidebar templates 
 * (`sidebar-$name.php` and `sidebar.php`) is preserved and given a higher priority than custom templates.
 *
 * @link http://core.trac.wordpress.org/ticket/15086
 * @link http://core.trac.wordpress.org/ticket/18676
 *
 * @since  2.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function hybrid_get_sidebar( $name = null ) {

	do_action( 'get_sidebar', $name ); // Core WordPress hook

	$templates = array();

	if ( '' !== $name ) {
		$templates[] = "sidebar-{$name}.php";
		$templates[] = "sidebar/{$name}.php";
	}

	$templates[] = 'sidebar.php';
	$templates[] = 'sidebar/sidebar.php';

	locate_template( $templates, true );
}
