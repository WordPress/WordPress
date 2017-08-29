<?php
/**
 * noteblog functions and definitions
 * Please browse readme.txt for credits and forking information
 *
 * @package noteblog
 */


if ( ! function_exists( 'noteblog_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */


function noteblog_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on noteblog, use a find and replace
	 * to change 'noteblog' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'noteblog', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 604, 270);
	add_image_size( 'noteblog-full-width', 1038, 576, true );
	

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus( array(
            'primary' => esc_html__( 'Top Primary Menu', 'noteblog' ),
    ) );
	
	
	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'noteblog_custom_background_args', array(
		'default-color' => 'f5f5f5',
		'default-image' => '',
		) ) );

}


endif; // noteblog_setup
add_action( 'after_setup_theme', 'noteblog_setup' );

/**
 * Sets the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 *
 */
function noteblog_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'noteblog_content_width', 640 );
}
add_action( 'after_setup_theme', 'noteblog_content_width', 0 );


/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */

function noteblog_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'noteblog' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Widgets here will appear in your sidebar', 'noteblog' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<div class="sidebar-headline-wrapper"><div class="widget-title-lines"></div><h4 class="widget-title">',
		'after_title'   => '</h4></div>',
		) );
}
add_action( 'widgets_init', 'noteblog_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function noteblog_scripts ( $in_footer ) {
	wp_enqueue_style( 'bootstrap', get_template_directory_uri().'/css/bootstrap.css',true );  

	wp_enqueue_style( 'noteblog-style', get_stylesheet_uri() );

	wp_enqueue_style( 'font-awesome', get_template_directory_uri().'/font-awesome/css/font-awesome.min.css',true );   
	
	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.js',array('jquery'),'',true);  

	wp_enqueue_script( 'noteblog-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array('jquery'), '20130115', true );

	wp_enqueue_script( 'html5shiv', get_template_directory_uri().'/js/html5shiv.js', array(),'3.7.3',false );
	wp_script_add_data( 'html5shiv', 'conditional', 'lt IE 9' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}
add_action( 'wp_enqueue_scripts', 'noteblog_scripts' );


/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';


/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';


/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';


/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';


/**
 * Load custom nav walker
 */
if(!class_exists('wp_bootstrap_navwalker')){
	require get_template_directory() . '/inc/navwalker.php';
}


function noteblog_google_fonts() {
	$query_args = array(

		'family' => 'Merriweather:700,700i|Lato:400,400italic,600'
		);
    wp_enqueue_style( 'noteblog-googlefonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );
}

add_action('wp_enqueue_scripts', 'noteblog_google_fonts');


function noteblog_new_excerpt_more( $more ) {
	if ( is_admin() ) {return $more;}$link = sprintf( '',esc_url( get_permalink( get_the_ID() ) ));
	return ' &hellip; ' . $link;

}
add_filter( 'excerpt_more', 'noteblog_new_excerpt_more' );




/**
*
* Custom Logo in the top menu
*
**/

function noteblog_logo() {
	add_theme_support('custom-logo', array(
		'size' => 'noteblog-logo',
		'width'                  => 250,
		'height'                 => 50,
		'flex-height'            => true,
		));
}

add_action('after_setup_theme', 'noteblog_logo');


/**
*
* New Footer Widgets
*
**/

function noteblog_footer_widget_left_init() {

	register_sidebar( array(
		'name' => esc_html__('Footer Widget left', 'noteblog'),
		'id' => 'footer_widget_left',
		'description'   => esc_html__( 'Widgets here will appear in your footer', 'noteblog' ),
		'before_widget' => '<div class="footer-widgets">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
		) );
}
add_action( 'widgets_init', 'noteblog_footer_widget_left_init' );

function noteblog_footer_widget_middle_init() {

	register_sidebar( array(
		'name' => esc_html__('Footer Widget middle', 'noteblog'),
		'id' => 'footer_widget_middle',
		'description'   => esc_html__( 'Widgets here will appear in your footer', 'noteblog' ),
		'before_widget' => '<div class="footer-widgets">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
		) );
}
add_action( 'widgets_init', 'noteblog_footer_widget_middle_init' );

function noteblog_footer_widget_right_init() {

	register_sidebar( array(
		'name' => esc_html__('Footer Widget right', 'noteblog'),
		'id' => 'footer_widget_right',
		'before_widget' => '<div class="footer-widgets">',
		'description'   => esc_html__( 'Widgets here will appear in your footer', 'noteblog' ),
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
		) );
}
add_action( 'widgets_init', 'noteblog_footer_widget_right_init' );



/**
*
* Admin styles
*
**/
function noteblog_load_custom_wp_admin_style( $hook ) {
	if ( 'appearance_page_about-noteblog' !== $hook ) {
		return;
	}
	wp_enqueue_style( 'noteblog-custom-admin-css', get_template_directory_uri() . '/css/admin.css', false, '1.0.0' );
}
add_action( 'admin_enqueue_scripts', 'noteblog_load_custom_wp_admin_style' );

add_action( 'customize_controls_print_styles', 'noteblog_customizer_stylesheet' );

