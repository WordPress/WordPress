<?php
/**
 * Twenty Fourteen functions and definitions
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 */
if ( ! isset( $content_width ) )
	$content_width = 474; /* pixels */

function twentyfourteen_set_content_width() {
	global $content_width;
	if ( is_page_template( 'full-width-page.php' ) || is_attachment() )
		$content_width = 895;
}
add_action( 'template_redirect', 'twentyfourteen_set_content_width' );

if ( ! function_exists( 'twentyfourteen_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function twentyfourteen_setup() {

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on Twenty Fourteen, use a find and replace
	 * to change 'twentyfourteen' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'twentyfourteen', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails
	 */
	add_theme_support( 'post-thumbnails', array( 'post' ) );

	/**
	 * Adding several sizes for Post Thumbnails
	 */
	add_image_size( 'featured-thumbnail-large', 672, 0 );
	add_image_size( 'featured-thumbnail-featured', 672, 336, true );
	add_image_size( 'featured-thumbnail-formatted', 306, 0 );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Top primary menu', 'twentyfourteen' ),
		'secondary' => __( 'Secondary menu in left sidebar', 'twentyfourteen' )
	) );

	/**
	 * Enable support for Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link', 'gallery' ) );

	/**
	 * This theme allows users to set a custom background.
	 */
	add_theme_support( 'custom-background', apply_filters( 'twentyfourteen_custom_background_args', array(
		'default-color' => 'f5f5f5',
	) ) );
}
endif; // twentyfourteen_setup
add_action( 'after_setup_theme', 'twentyfourteen_setup' );

/**
 * Getter function for Featured Content Plugin.
 *
 */
function twentyfourteen_get_featured_posts() {
	return apply_filters( 'twentyfourteen_get_featured_posts', false );
}

/**
 * A helper conditional function that returns a boolean value
 * So that we can use a condition like
 * if ( twentyfourteen_has_featured_posts( 1 ) )
 *
 */
function twentyfourteen_has_featured_posts( $minimum = 1 ) {
	if ( is_paged() )
		return false;

	$featured_posts = apply_filters( 'twentyfourteen_get_featured_posts', array() );

	return is_array( $featured_posts ) && count( $featured_posts ) > absint( $minimum );
}

/**
 * Register widgetized area and update sidebar with default widgets
 *
 */
function twentyfourteen_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Primary Sidebar', 'twentyfourteen' ),
		'id' => 'sidebar-1',
		'description' => __( 'Main sidebar that appears on the left.', 'twentyfourteen' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
	register_sidebar( array(
		'name' => __( 'Content Sidebar', 'twentyfourteen' ),
		'id' => 'sidebar-2',
		'description' => __( 'Additional sidebar that appears on the right, on single posts and pages.', 'twentyfourteen' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer Widget Area One', 'twentyfourteen' ),
		'id' => 'sidebar-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer Widget Area Two', 'twentyfourteen' ),
		'id' => 'sidebar-4',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer Widget Area Three', 'twentyfourteen' ),
		'id' => 'sidebar-5',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer Widget Area Four', 'twentyfourteen' ),
		'id' => 'sidebar-6',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer Widget Area Five', 'twentyfourteen' ),
		'id' => 'sidebar-7',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
}
add_action( 'widgets_init', 'twentyfourteen_widgets_init' );

/**
 * Register Google fonts for Twenty Fourteen.
 *
 */
function twentyfourteen_fonts() {
	/* translators: If there are characters in your language that are not supported
	   by Lato, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Lato font: on or off', 'twentyfourteen' ) )
		wp_register_style( 'twentyfourteen-lato', '//fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic', array(), null );
}
add_action( 'init', 'twentyfourteen_fonts' );

/**
 * Enqueue scripts and styles
 *
 */
function twentyfourteen_scripts() {
	wp_enqueue_style( 'twentyfourteen-style', get_stylesheet_uri() );

	wp_enqueue_style( 'twentyfourteen-lato' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	if ( is_singular() && wp_attachment_is_image() )
		wp_enqueue_script( 'twentyfourteen-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20130402' );

	wp_enqueue_script( 'twentyfourteen-theme', get_template_directory_uri() . '/js/theme.js', array( 'jquery' ), '20130402', true );
}
add_action( 'wp_enqueue_scripts', 'twentyfourteen_scripts' );

/**
 * Enqueue Google fonts style to admin screen for custom header display.
 *
 */
function twentyfourteen_admin_fonts() {
	wp_enqueue_style( 'twentyfourteen-lato' );
}
add_action( 'admin_print_scripts-appearance_page_custom-header', 'twentyfourteen_admin_fonts' );

/**
 * Implement the Custom Header feature
 *
 */
require( get_template_directory() . '/inc/custom-header.php' );

/**
 * Sets the post excerpt length to 40 words.
 *
 */
function twentyfourteen_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'twentyfourteen_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 */
function twentyfourteen_continue_reading_link() {
	return ' <a href="'. esc_url( get_permalink() ) . '" class="more-link">' . __( 'Read More <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyeleven_continue_reading_link().
 */
function twentyfourteen_auto_excerpt_more( $more ) {
	return ' &hellip;' . twentyfourteen_continue_reading_link();
}
add_filter( 'excerpt_more', 'twentyfourteen_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 */
function twentyfourteen_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= twentyfourteen_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twentyfourteen_custom_excerpt_more' );

if ( ! function_exists( 'twentyfourteen_the_attached_image' ) ) :
/**
 * Prints the attached image with a link to the next attached image.
 *
 * @since Twenty Thirteen 1.0
 *
 * @return void
 */
function twentyfourteen_the_attached_image() {
	$post                = get_post();
	$attachment_size     = apply_filters( 'twentyfourteen_attachment_size', array( 1200, 1200 ) );
	$next_attachment_url = wp_get_attachment_url();

	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the URL
	 * of the next adjacent image in a gallery, or the first image (if we're
	 * looking at the last image in a gallery), or, in a gallery of one, just the
	 * link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}
endif;

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 *
 */
function twentyfourteen_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'sidebar-3' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-4' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-5' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-6' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-7' ) )
		$count++;

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
		case '4':
			$class = 'four';
			break;
		case '5':
			$class = 'five';
			break;
	}

	if ( $class )
		printf( 'class="%s"', $class );
}

/**
 * Gets recent formatted posts that are not featured in FC plugin.
 *
 */
function twentyfourteen_get_recent( $post_format ) {
	$args = array(
		'order' => 'DESC',
		'ignore_sticky_posts' => 1,
		'posts_per_page' => 2,
		'tax_query' => array(
			array(
				'taxonomy' => 'post_format',
				'terms' => array( $post_format ),
				'field' => 'slug',
				'operator' => 'IN',
			),
		),
		'no_found_rows' => true,
	);

	$featured_posts = twentyfourteen_get_featured_posts();

	if ( is_array( $featured_posts ) && ! empty( $featured_posts ) )
		$args['post__not_in'] = wp_list_pluck( $featured_posts, 'ID' );

	return new WP_Query( $args );
}

/**
 * Filter the home page posts, and remove formatted posts visible in the sidebar from it
 *
 */
function twentyfourteen_pre_get_posts( $query ) {
	// Bail if not home, not a query, not main query.
	if ( ! $query->is_main_query() || is_admin() )
		return;

	// Only on the home page
	if ( $query->is_home() ) {
		$exclude_ids = array();

		$videos = twentyfourteen_get_recent( 'post-format-video' );
		$images = twentyfourteen_get_recent( 'post-format-image' );
		$galleries = twentyfourteen_get_recent( 'post-format-gallery' );
		$asides = twentyfourteen_get_recent( 'post-format-aside' );
		$links = twentyfourteen_get_recent( 'post-format-link' );
		$quotes = twentyfourteen_get_recent( 'post-format-quote' );

		foreach ( $videos->posts as $post )
			$exclude_ids[] = $post->ID;

		foreach ( $images->posts as $post )
			$exclude_ids[] = $post->ID;

		foreach ( $galleries->posts as $post )
			$exclude_ids[] = $post->ID;

		foreach ( $asides->posts as $post )
			$exclude_ids[] = $post->ID;

		foreach ( $links->posts as $post )
			$exclude_ids[] = $post->ID;

		foreach ( $quotes->posts as $post )
			$exclude_ids[] = $post->ID;

		$query->set( 'post__not_in', $exclude_ids );
	}
}
add_action( 'pre_get_posts', 'twentyfourteen_pre_get_posts' );

/**
 * Adds custom classes to the array of body classes.
 *
 */
function twentyfourteen_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}
	if ( is_archive() || is_search() || is_home() ) {
		$classes[] = 'list-view';
	}

	return $classes;
}
add_filter( 'body_class', 'twentyfourteen_body_classes' );

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 */
function twentyfourteen_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'twentyfourteen' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'twentyfourteen_wp_title', 10, 2 );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions
 */
require get_template_directory() . '/inc/customizer.php';
