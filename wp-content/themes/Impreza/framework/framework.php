<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Themes Framework
 *
 * Should be included in global context.
 */
global $us_template_directory, $us_stylesheet_directory, $us_template_directory_uri, $us_stylesheet_directory_uri;
$us_template_directory = get_template_directory();
$us_stylesheet_directory = get_stylesheet_directory();
// Removing protocols for better compatibility with caching plugins and services
$us_template_directory_uri = str_replace( array( 'http:', 'https:' ), '', get_template_directory_uri() );
$us_stylesheet_directory_uri = str_replace( array( 'http:', 'https:' ), '', get_stylesheet_directory_uri() );

if ( ! defined( 'US_THEMENAME' ) OR ! defined( 'US_THEMEVERSION' ) ) {
	$us_theme = wp_get_theme();
	if ( is_child_theme() ) {
		$us_theme = wp_get_theme( $us_theme->get( 'Template' ) );
	}
	if ( ! defined( 'US_THEMENAME' ) ) {
		define( 'US_THEMENAME', $us_theme->get( 'Name' ) );
	}
	if ( ! defined( 'US_THEMEVERSION' ) ) {
		define( 'US_THEMEVERSION', $us_theme->get( 'Version' ) );
	}
	unset( $us_theme );
}

if ( ! isset( $us_theme_supports ) ) {
	$us_theme_supports = array();
}

// Upsolution helper functions
require $us_template_directory . '/framework/functions/helpers.php';

// Theme Options
require $us_template_directory . '/framework/functions/theme-options.php';

// Performing fallback compatibility and migrations when needed
require $us_template_directory . '/framework/functions/migration.php';

// Load shortcodes
require $us_template_directory . '/framework/functions/shortcodes.php';

// UpSolution Header definitions
require $us_template_directory . '/framework/functions/header.php';

// UpSolution Layout definitions
require $us_template_directory . '/framework/functions/layout.php';

// Breadcrumbs function
require $us_template_directory . '/framework/functions/breadcrumbs.php';

// Post formats
require $us_template_directory . '/framework/functions/post.php';

// Custom Post types
require $us_template_directory . '/framework/functions/post-types.php';

// Page Meta Tags
require $us_template_directory . '/framework/functions/meta-tags.php';

// Menu and it's custom markup
require $us_template_directory . '/framework/functions/menu.php';
// Comments custom markup
require $us_template_directory . '/framework/functions/comments.php';
// wp_link_pages both next and numbers usage
require $us_template_directory . '/framework/functions/pagination.php';

// Sidebars init
require $us_template_directory . '/framework/functions/widget_areas.php';

// Plugins activation
if ( is_admin() ) {
	// Admin specific functions
	require $us_template_directory . '/framework/admin/functions/functions.php';
	require $us_template_directory . '/framework/admin/functions/updater.php';
	require $us_template_directory . '/framework/admin/functions/theme-updater.php';
} else {
	// Frontent CSS and JS enqueue
	require $us_template_directory . '/framework/functions/enqueue.php';
}

// Widgets
require $us_template_directory . '/framework/functions/widgets.php';
add_filter( 'widget_text', 'do_shortcode' );

if ( is_admin() ) {
	// Theme Dashboard page
	require $us_template_directory . '/framework/admin/functions/dashboard.php';
	// Addons
	require $us_template_directory . '/framework/admin/functions/addons.php';
	// Demo Import
	require $us_template_directory . '/framework/admin/functions/demo-import.php';
}

if ( defined( 'DOING_AJAX' ) AND DOING_AJAX ) {
	require $us_template_directory . '/framework/functions/ajax/blog.php';
	require $us_template_directory . '/framework/functions/ajax/portfolio.php';
	require $us_template_directory . '/framework/functions/ajax/cform.php';
	require $us_template_directory . '/framework/functions/ajax/cart.php';
	require $us_template_directory . '/framework/functions/ajax/user_info.php';
}

// Including plugins support files
if ( ! isset( $us_theme_supports['plugins'] ) ) {
	$us_theme_supports['plugins'] = array();
}
foreach ( $us_theme_supports['plugins'] AS $us_plugin_name => $us_plugin_path ) {
	if ( $us_plugin_path === NULL ) {
		continue;
	}
	include $us_template_directory . $us_plugin_path;
}

/**
 * Theme Setup
 */
add_action( 'after_setup_theme', 'us_theme_setup' );
function us_theme_setup() {
	global $content_width;

	if ( ! isset( $content_width ) ) {
		$content_width = 1500;
	}
	add_theme_support( 'automatic-feed-links' );

	add_theme_support( 'post-formats', array( 'video', 'gallery', 'audio', 'image', 'quote', 'link' ) );

	// Add post thumbnail functionality
	add_theme_support( 'post-thumbnails' );

	/**
	 * Dev note: you can overload theme's image sizes config using filter 'us_config_image-sizes'
	 */
	$tnail_sizes = us_config( 'image-sizes', array() );
	foreach ( $tnail_sizes as $size_name => $size ) {
		add_image_size( $size_name, $size['width'], $size['height'], $size['crop'] );
	}

	// Excerpt length
	add_filter( 'excerpt_length', 'us_excerpt_length', 100 );
	function us_excerpt_length( $length ) {
		$excerpt_length = us_get_option( 'excerpt_length' );
		if ( $excerpt_length === NULL ) {
			return $length;
		} elseif ( $excerpt_length === '' ) {
			// If not set, showing the full excerpt
			return 9999;
		} else {
			return intval( $excerpt_length );
		}
	}

	// Remove [...] from excerpt
	add_filter( 'excerpt_more', 'us_excerpt_more' );
	function us_excerpt_more( $more ) {
		return '...';
	}

	// Theme localization
	us_maybe_load_theme_textdomain();
}

if ( ! defined( 'ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS' ) ) {
	define( 'ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', TRUE );
}

if ( ! function_exists( 'us_wp_title' ) ) {
	add_filter( 'wp_title', 'us_wp_title' );
	function us_wp_title( $title ) {
		if ( is_front_page() ) {
			return get_bloginfo( 'name' );
		} else {
			return trim( $title );
		}
	}
}

