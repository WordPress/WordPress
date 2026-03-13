<?php
/**
 * Twenty Minutes functions and definitions
 *
 * @package Twenty Minutes
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */

if ( ! function_exists( 'twenty_minutes_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function twenty_minutes_setup() {
	global $twenty_minutes_content_width;
	if ( ! isset( $twenty_minutes_content_width ) )
		$twenty_minutes_content_width = 680;

	load_theme_textdomain( 'twenty-minutes', get_template_directory() . '/languages' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'wp-block-styles');
	add_theme_support( 'align-wide' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'custom-header', array(
		'default-text-color' => false,
		'header-text' => false,
	) );
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 100,
		'flex-height' => true,
	) );
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'twenty-minutes' ),
	) );
	add_theme_support( 'custom-background', array(
		'default-color' => 'ffffff'
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );
	/*
	 * Enable support for Post Formats.
	 */
	add_theme_support( 'post-formats', array('image','video','gallery','audio',) );
	
	add_editor_style( 'editor-style.css' );

	global $pagenow;

    if ( is_admin() && 'themes.php' === $pagenow && isset( $_GET['activated'] ) && current_user_can( 'manage_options' ) ) {
        add_action('admin_notices', 'twenty_minutes_deprecated_hook_admin_notice');
    }
}
endif; // twenty_minutes_setup
add_action( 'after_setup_theme', 'twenty_minutes_setup' );

function twenty_minutes_the_breadcrumb() {
    echo '<div class="breadcrumb my-3">';

    if (!is_home()) {
        echo '<a class="home-main align-self-center" href="' . esc_url(home_url()) . '">';
        bloginfo('name');
        echo "</a>";

        if (is_category() || is_single()) {
            the_category(' , ');
            if (is_single()) {
                echo '<span class="current-breadcrumb mx-3">' . esc_html(get_the_title()) . '</span>';
            }
        } elseif (is_page()) {
            echo '<span class="current-breadcrumb mx-3">' . esc_html(get_the_title()) . '</span>';
        }
    }

    echo '</div>';
}

function twenty_minutes_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'twenty-minutes' ),
		'description'   => __( 'Appears on blog page sidebar', 'twenty-minutes' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Page Sidebar', 'twenty-minutes' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Add widgets here to appear in your sidebar on pages.', 'twenty-minutes' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Sidebar 3', 'twenty-minutes' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'twenty-minutes' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Shop Page Sidebar', 'twenty-minutes' ),
		'description'   => __( 'Appears on shop page', 'twenty-minutes' ),
		'id'            => 'woocommerce_sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar(array(
        'name'          => __('Single Product Sidebar', 'twenty-minutes'),
        'description'   => __('Sidebar for single product pages', 'twenty-minutes'),
		'id'            => 'woocommerce-single-sidebar',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

	$twenty_minutes_widget_areas = get_theme_mod('twenty_minutes_footer_widget_areas', '4');
	for ($twenty_minutes_i=1; $twenty_minutes_i <= 4; $twenty_minutes_i++) {
		register_sidebar( array(
			'name'          => __( 'Footer Widget ', 'twenty-minutes' ) . $twenty_minutes_i,
			'id'            => 'footer-' . $twenty_minutes_i,
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
}
add_action( 'widgets_init', 'twenty_minutes_widgets_init' );

// Change number of products per row to 4
add_filter('loop_shop_columns', 'twenty_minutes_loop_columns');
if (!function_exists('twenty_minutes_loop_columns')) {
    function twenty_minutes_loop_columns() {
        $colm = get_theme_mod('twenty_minutes_products_per_row', 4); // Default to 4 if not set
        return $colm;
    }
}

// Use the customizer setting to set the number of products per page
function twenty_minutes_products_per_page($cols) {
    $cols = get_theme_mod('twenty_minutes_products_per_page', 9); // Default to 9 if not set
    return $cols;
}
add_filter('loop_shop_per_page', 'twenty_minutes_products_per_page', 9);


function twenty_minutes_scripts() {
	wp_enqueue_style( 'bootstrap-css', esc_url(get_template_directory_uri())."/css/bootstrap.css" );
	wp_enqueue_style( 'twenty-minutes-style', get_stylesheet_uri() );
	wp_style_add_data('twenty-minutes-style', 'rtl', 'replace');
	wp_enqueue_style( 'owl.carousel-css', esc_url(get_template_directory_uri())."/css/owl.carousel.css" );
	wp_enqueue_style( 'twenty-minutes-responsive', esc_url(get_template_directory_uri())."/css/responsive.css" );
	wp_enqueue_style( 'twenty-minutes-default', esc_url(get_template_directory_uri())."/css/default.css" );
	wp_enqueue_script( 'bootstrap-js', esc_url(get_template_directory_uri()). '/js/bootstrap.js', array('jquery') );
	wp_enqueue_script( 'owl.carousel-js', esc_url(get_template_directory_uri()). '/js/owl.carousel.js', array('jquery') );
	wp_enqueue_script( 'twenty-minutes-theme', esc_url(get_template_directory_uri()) . '/js/theme.js' );
	wp_enqueue_style( 'font-awesome-css', esc_url(get_template_directory_uri())."/css/fontawesome-all.css" );

	require get_parent_theme_file_path( '/inc/color-scheme/custom-color-control.php' );
	wp_add_inline_style( 'twenty-minutes-style',$twenty_minutes_color_scheme_css );
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Font family
	$twenty_minutes_headings_font = esc_html(get_theme_mod('twenty_minutes_headings_fonts'));
	$twenty_minutes_body_font = esc_html(get_theme_mod('twenty_minutes_body_fonts'));

	if ($twenty_minutes_headings_font) {
	    wp_enqueue_style('twenty-minutes-headings-fonts', 'https://fonts.googleapis.com/css?family=' . urlencode($twenty_minutes_headings_font));
	} else {
	    wp_enqueue_style('opensans', 'https://fonts.googleapis.com/css?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800');
	}

	if ($twenty_minutes_body_font) {
	    wp_enqueue_style('poppins', 'https://fonts.googleapis.com/css?family=' . urlencode($twenty_minutes_body_font));
	} else {
	    wp_enqueue_style('twenty-minutes-source-body', 'https://fonts.googleapis.com/css?family=Poppins:Poppins:0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900');
	}

}
add_action( 'wp_enqueue_scripts', 'twenty_minutes_scripts' );

function twenty_minutes_setup_theme() {
	// Footer Link
	define('TWENTY_MINUTES_FOOTER_LINK',__('https://www.theclassictemplates.com/products/free-twenty-minutes-wordpress-template','twenty-minutes'));

	if ( ! defined( 'TWENTY_MINUTES_PRO_NAME' ) ) {
		define( 'TWENTY_MINUTES_PRO_NAME', __( 'About Twenty Minutes', 'twenty-minutes' ));
	}
	if ( ! defined( 'TWENTY_MINUTES_THEME_PAGE' ) ) {
	define('TWENTY_MINUTES_THEME_PAGE',__('https://www.theclassictemplates.com/collections/best-wordpress-templates','twenty-minutes'));
	}
	if ( ! defined( 'TWENTY_MINUTES_SUPPORT' ) ) {
	define('TWENTY_MINUTES_SUPPORT',__('https://wordpress.org/support/theme/twenty-minutes','twenty-minutes'));
	}
	if ( ! defined( 'TWENTY_MINUTES_REVIEW' ) ) {
	define('TWENTY_MINUTES_REVIEW',__('https://wordpress.org/support/theme/twenty-minutes/reviews/#new-post','twenty-minutes'));
	}
	if ( ! defined( 'TWENTY_MINUTES_PRO_DEMO' ) ) {
	define('TWENTY_MINUTES_PRO_DEMO',__('https://live.theclassictemplates.com/demo/twenty-minutes','twenty-minutes'));
	}
	if ( ! defined( 'TWENTY_MINUTES_PREMIUM_PAGE' ) ) {
	define('TWENTY_MINUTES_PREMIUM_PAGE',__('https://www.theclassictemplates.com/products/twenty-minutes-wordpress-template','twenty-minutes'));
	}
	if ( ! defined( 'TWENTY_MINUTES_THEME_DOCUMENTATION' ) ) {
	define('TWENTY_MINUTES_THEME_DOCUMENTATION',__('https://live.theclassictemplates.com/demo/docs/twenty-minutes-free/','twenty-minutes'));
	}
	if ( ! defined( 'TWENTY_MINUTES_BUNDLE_PAGE' ) ) {
		define('TWENTY_MINUTES_BUNDLE_PAGE',__('https://www.theclassictemplates.com/products/wordpress-theme-bundle','twenty-minutes'));
	}
}
add_action( 'after_setup_theme', 'twenty_minutes_setup_theme' );

/**
 * Webfont Loader.
 */
require_once get_theme_file_path( 'inc/wptt-webfont-loader.php' );

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
 * Customizer additions.
 */
require get_template_directory() . '/inc/upgrade-to-pro.php';

/**
 * Google Fonts
 */
require get_template_directory() . '/inc/gfonts.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

// select
require get_template_directory() . '/inc/select/category-dropdown-custom-control.php';

/**
 * Theme Info Page.
 */
require get_template_directory() . '/inc/addon.php';

/**
 * Load TGM.
 */
require get_template_directory() . '/inc/tgm/tgm.php';

/* Starter Content */
	add_theme_support( 'starter-content', array(
		'widgets' => array(
			'footer-1' => array(
				'categories',
			),
			'footer-2' => array(
				'archives',
			),
			'footer-3' => array(
				'meta',
			),
			'footer-4' => array(
				'search',
			),
		),
    ));

// logo
if ( ! function_exists( 'twenty_minutes_the_custom_logo' ) ) :
/**
 * Displays the optional custom logo.
 *
 * Does nothing if the custom logo is not available.
 *
 */
function twenty_minutes_the_custom_logo() {
	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}
}
endif;

/*radio button sanitization*/
function twenty_minutes_sanitize_choices( $input, $setting ) {
    global $wp_customize;
    $control = $wp_customize->get_control( $setting->id );
    if ( array_key_exists( $input, $control->choices ) ) {
        return $input;
    } else {
        return $setting->default;
    }
}

if ( ! function_exists( 'twenty_minutes_sanitize_integer' ) ) {
	function twenty_minutes_sanitize_integer( $input ) {
		return (int) $input;
	}
}

/* Activation Notice */
function twenty_minutes_deprecated_hook_admin_notice() {
    $twenty_minutes_theme = wp_get_theme();
    $twenty_minutes_dismissed = get_user_meta( get_current_user_id(), 'twenty_minutes_dismissable_notice', true );
    if ( !$twenty_minutes_dismissed) { ?>
        <div class="getstrat updated notice notice-success is-dismissible notice-get-started-class">
            <div class="admin-image">
                <img src="<?php echo esc_url(get_stylesheet_directory_uri()) .'/screenshot.png'; ?>" />
            </div>
            <div class="admin-content" >
                <h1><?php 
				/* translators: 1: Theme name, 2: Theme version. */
				printf( esc_html__( 'Welcome to %1$s %2$s', 'twenty-minutes' ), esc_html($twenty_minutes_theme->get( 'Name' )), esc_html($twenty_minutes_theme->get( 'Version' ))); ?>
                </h1>
                <p><?php _e('Get Started With Theme By Clicking On Getting Started.', 'twenty-minutes'); ?></p>
                <div style="display: grid;">
                    <a class="admin-notice-btn button button-hero upgrade-pro" target="_blank" href="<?php echo esc_url( TWENTY_MINUTES_PREMIUM_PAGE ); ?>"><?php esc_html_e('Upgrade Pro', 'twenty-minutes') ?><i class="dashicons dashicons-cart"></i></a>
                    <a class="admin-notice-btn button button-hero" href="<?php echo esc_url( admin_url( 'themes.php?page=twenty-minutes' )); ?>"><?php esc_html_e( 'Get started', 'twenty-minutes' ) ?><i class="dashicons dashicons-backup"></i></a>
                    <a class="admin-notice-btn button button-hero" target="_blank" href="<?php echo esc_url( TWENTY_MINUTES_THEME_DOCUMENTATION ); ?>"><?php esc_html_e('Free Doc', 'twenty-minutes') ?><i class="dashicons dashicons-visibility"></i></a>
                    <a  class="admin-notice-btn button button-hero" target="_blank" href="<?php echo esc_url( TWENTY_MINUTES_PRO_DEMO ); ?>"><?php esc_html_e('View Demo', 'twenty-minutes') ?><i class="dashicons dashicons-awards"></i></a>
                </div>
            </div>
        </div>
    <?php }
}