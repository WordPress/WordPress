<?php
/**
 * Twenty Fourteen functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development
 * and http://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * see http://codex.wordpress.org/Plugin_API
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

/**
 * Sets up the content width value based on the theme's design.
 * @see twentyfourteen_content_width() for template-specific adjustments.
 */
if ( ! isset( $content_width ) )
	$content_width = 474;

if ( ! function_exists( 'twentyfourteen_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support post thumbnails.
 */
function twentyfourteen_setup() {

	/*
	 * Makes Twenty Fourteen available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Twenty Fourteen, use a find and
	 * replace to change 'twentyfourteen' to the name of your theme in all
	 * template files.
	 */
	load_theme_textdomain( 'twentyfourteen', get_template_directory() . '/languages' );

	/*
	* This theme styles the visual editor to resemble the theme style.
	*/
	add_editor_style( array( 'editor-style.css', twentyfourteen_font_url() ) );

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// Enable support for Post Thumbnails.
	add_theme_support( 'post-thumbnails', array( 'post' ) );

	// Adding several sizes for Post Thumbnails.
	add_image_size( 'featured-thumbnail-large', 672, 0 );
	add_image_size( 'featured-thumbnail-featured', 672, 336, true );
	add_image_size( 'featured-thumbnail-formatted', 306, 0 );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary'   => __( 'Top primary menu', 'twentyfourteen' ),
		'secondary' => __( 'Secondary menu in left sidebar', 'twentyfourteen' ),
	) );

	/*
	 * Switches default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery',
	) );

	/*
	 * This theme allows users to set a custom background.
	 */
	add_theme_support( 'custom-background', apply_filters( 'twentyfourteen_custom_background_args', array(
		'default-color' => 'f5f5f5',
	) ) );
}
endif; // twentyfourteen_setup
add_action( 'after_setup_theme', 'twentyfourteen_setup' );

/**
 * Adjusts content_width value for full-width and attachment templates.
 *
 * @return void
 */
function twentyfourteen_content_width() {
	if ( is_page_template( 'full-width-page.php' ) || is_attachment() )
		$GLOBALS['content_width'] = 810;
}
add_action( 'template_redirect', 'twentyfourteen_content_width' );

/**
 * Getter function for Featured Content Plugin.
 */
function twentyfourteen_get_featured_posts() {
	return apply_filters( 'twentyfourteen_get_featured_posts', false );
}

/**
 * A helper conditional function that returns a boolean value
 * So that we can use a condition like
 * if ( twentyfourteen_has_featured_posts( 1 ) )
 */
function twentyfourteen_has_featured_posts( $minimum = 1 ) {
	if ( is_paged() )
		return false;

		$featured_posts = apply_filters( 'twentyfourteen_get_featured_posts', array() );

	return is_array( $featured_posts ) && count( $featured_posts ) > absint( $minimum );
}

/**
 * Registers two widget areas.
 *
 * @return void
 */
function twentyfourteen_widgets_init() {
	require get_template_directory() . '/inc/widgets.php';
	register_widget( 'Twenty_Fourteen_Ephemera_Widget' );

	register_sidebar( array(
		'name'          => __( 'Primary Sidebar', 'twentyfourteen' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Main sidebar that appears on the left.', 'twentyfourteen' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Front Page Sidebar', 'twentyfourteen' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Additional sidebar that appears on the right, on the home page.', 'twentyfourteen' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Content Sidebar', 'twentyfourteen' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'Additional sidebar that appears on the right, on single posts and pages.', 'twentyfourteen' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Widget Area', 'twentyfourteen' ),
		'id'            => 'sidebar-4',
		'description'   => __( 'Appears in the footer section of the site.', 'twentyfourteen' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'twentyfourteen_widgets_init' );

/**
 * Register Lato Google font for Twenty Fourteen.
 *
 * @return void
 */
function twentyfourteen_font_url() {
	$font_url = '';
	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Lato, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Lato font: on or off', 'twentyfourteen' ) )
		$font_url = add_query_arg( 'family', urlencode( 'Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' ), "//fonts.googleapis.com/css" );

	return $font_url;
}

/**
 * Enqueues scripts and styles for front end.
 *
 * @return void
 */
function twentyfourteen_scripts() {

	// Add Lato font, used in the main stylesheet.
	wp_enqueue_style( 'twentyfourteen-lato' );

	// Add Genericons font, used in the main stylesheet.
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/fonts/genericons.css', array(), '3.0' );

	// Loads our main stylesheet.
	wp_enqueue_style( 'twentyfourteen-style', get_stylesheet_uri() );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	if ( is_singular() && wp_attachment_is_image() )
		wp_enqueue_script( 'twentyfourteen-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20130402' );

	if ( is_active_sidebar( 'sidebar-3' ) )
		wp_enqueue_script( 'jquery-masonry' );

	wp_enqueue_script( 'twentyfourteen-theme', get_template_directory_uri() . '/js/theme.js', array( 'jquery' ), '20130820', true );

	// Add Lato font used in the main stylesheet.
	wp_enqueue_style( 'twentyfourteen-lato', twentyfourteen_font_url(), array(), null );
}
add_action( 'wp_enqueue_scripts', 'twentyfourteen_scripts' );

/**
 * Enqueue Google fonts style to admin screen for custom header display.
 *
 * @return void
 */
function twentyfourteen_admin_fonts() {
	wp_enqueue_style( 'twentyfourteen-lato' );
}
add_action( 'admin_print_scripts-appearance_page_custom-header', 'twentyfourteen_admin_fonts' );

/**
 * Sets the post excerpt length to 20 words.
 *
 * @param int $length
 * @return int
 */
function twentyfourteen_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'twentyfourteen_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @return string
 */
function twentyfourteen_continue_reading_link() {
	return ' <a href="'. esc_url( get_permalink() ) . '" class="more-link">' . __( 'Read More <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an
 * ellipsis and twentyeleven_continue_reading_link().
 *
 * @param string $more
 * @return string
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
 * @param string $output
 * @return string
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
		'orderby'        => 'menu_order ID',
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

if ( ! function_exists( 'twentyfourteen_list_authors' ) ) :
/**
 * Prints a list of all site contributors who published at least one post.
 *
 * @return void
 */
function twentyfourteen_list_authors() {
	$contributor_ids = get_users( array(
		'fields'  => 'ID',
		'orderby' => 'post_count',
		'who'     => 'authors',
	) );

	foreach ( $contributor_ids as $contributor_id ) :
		$post_count = count_user_posts( $contributor_id );

		// Move on if user has not published a post (yet).
		if ( ! $post_count )
			continue;
	?>

	<div class="contributor">
		<div class="contributor-info clear">
			<div class="contributor-avatar"><?php echo get_avatar( $contributor_id, 132 ); ?></div>
			<div class="contributor-summary">
				<h2 class="contributor-name"><?php echo get_the_author_meta( 'display_name', $contributor_id ); ?></h2>
				<p class="contributor-bio">
					<?php echo get_the_author_meta( 'description', $contributor_id ); ?>
				</p>
				<a class="contributor-posts-link" href="<?php echo esc_url( get_author_posts_url( $contributor_id ) ); ?>">
					<?php printf( _n( '%d Article', '%d Articles', $post_count, 'twentyfourteen' ), $post_count ); ?>
				</a>
			</div><!-- .contributor-summary -->
		</div><!-- .contributor-info -->
	</div><!-- .contributor -->

	<?php
	endforeach;
}
endif;

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
 *  Extends the default WordPress body classes.
 *
 * Adds body classes to denote:
 * 1. Single or multiple authors.
 * 2. Index views.
 * 3. Full-width content layout.
 *
 * @param array $classes A list of existing body class values.
 * @return array The filtered body class list.
 */
function twentyfourteen_body_classes( $classes ) {
	if ( is_multi_author() )
		$classes[] = 'group-blog';

	if ( is_archive() || is_search() || is_home() )
		$classes[] = 'list-view';

	if ( ( ! is_front_page() && ! is_active_sidebar( 'sidebar-3' ) )
		|| is_page_template( 'full-width-page.php' )
		|| is_page_template( 'contributor-page.php' )
		|| is_attachment() )
		$classes[] = 'full-width';

	return $classes;
}
add_filter( 'body_class', 'twentyfourteen_body_classes' );

/**
 * Creates a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function twentyfourteen_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'twentyfourteen' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'twentyfourteen_wp_title', 10, 2 );

/**
 * Implement the Custom Header feature
 *
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions
 */
require get_template_directory() . '/inc/customizer.php';
