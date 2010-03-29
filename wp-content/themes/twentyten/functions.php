<?php
/**
 * TwentyTen functions and definitions
 *
 * Sets up the theme and provides some helper functions used
 * in other parts of the theme.  All functions are pluggable
 *
 * @package WordPress
 * @subpackage Twenty Ten
 * @since 3.0.0
 */

/**
 * Set the content width based on the Theme CSS.  Can be overriden
 *
 * Used in attachment.php to set the width of images.  Should
 * be equal to the width set for .onecolumn #content in style.css
 */
if ( ! isset( $content_width ) )
	$content_width = 640;

if ( ! function_exists( 'twentyten_init' ) ) :
/**
 * Set up defaults for our theme.
 *
 * Sets up theme defaults and tells wordpress that this is a
 * theme that will take advantage of Post Thumbnails, Custom
 * Background, Nav Menus and automatic feed links.  To
 * override any of the settings in a child theme, create your
 * own twentyten_init function
 *
 * @uses add_theme_support()
 */
function twentyten_init() {
	// This theme allows users to set a custom background
	add_custom_background();

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// This theme needs post thumbnails
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu()
	add_theme_support( 'nav-menus' );

	// We'll be using them for custom header images on posts and pages
	// so we want them to be 940 pixels wide by 198 pixels tall (larger images will be auto-cropped to fit)
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'twentyten', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// Your Changeable header business starts here
	// No CSS, just IMG call
	define( 'HEADER_TEXTCOLOR', '' );
	define( 'HEADER_IMAGE', '%s/images/headers/forestfloor.jpg' ); // %s is theme dir uri

	// Add a filter to twentyten_header_image_width and twentyten_header_image_height to change these values
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyten_header_image_width',  940 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyten_header_image_height',	198 ) );

	define( 'NO_HEADER_TEXT', true );

	add_custom_image_header( '', 'twentyten_admin_header_style' );
	// and thus ends the changeable header business

	// Default custom headers.  %s is a placeholder for the theme template directory

	register_default_headers( array (
		'berries' => array (
			'url' => '%s/images/headers/berries.jpg',
			'thumbnail_url' => '%s/images/headers/berries-thumbnail.jpg',
			'description' => __( 'Berries', 'twentyten' )
		),
		'cherryblossom' => array (
			'url' => '%s/images/headers/cherryblossoms.jpg',
			'thumbnail_url' => '%s/images/headers/cherryblossoms-thumbnail.jpg',
			'description' => __( 'Cherry Blossoms', 'twentyten' )
		),
		'concave' => array (
			'url' => '%s/images/headers/concave.jpg',
			'thumbnail_url' => '%s/images/headers/concave-thumbnail.jpg',
			'description' => __( 'Concave', 'twentyten' )
		),
		'fern' => array (
			'url' => '%s/images/headers/fern.jpg',
			'thumbnail_url' => '%s/images/headers/fern-thumbnail.jpg',
			'description' => __( 'Fern', 'twentyten' )
		),
		'forestfloor' => array (
			'url' => '%s/images/headers/forestfloor.jpg',
			'thumbnail_url' => '%s/images/headers/forestfloor-thumbnail.jpg',
			'description' => __( 'Forest Floor', 'twentyten' )
		),
		'inkwell' => array (
			'url' => '%s/images/headers/inkwell.jpg',
			'thumbnail_url' => '%s/images/headers/inkwell-thumbnail.jpg',
			'description' => __( 'Inkwell', 'twentyten' )
		),
		'path' => array (
			'url' => '%s/images/headers/path.jpg',
			'thumbnail_url' => '%s/images/headers/path-thumbnail.jpg',
			'description' => __( 'Path', 'twentyten' )
		),
		'sunset' => array (
			'url' => '%s/images/headers/sunset.jpg',
			'thumbnail_url' => '%s/images/headers/sunset-thumbnail.jpg',
			'description' => __( 'Sunset', 'twentyten' )
		)
	) );
}
endif;
add_action( 'after_setup_theme', 'twentyten_init' );

if ( ! function_exists( 'twentyten_admin_header_style' ) ) :
/**
 * Callback to style the header image inside the admin
 */
function twentyten_admin_header_style() {
?>
<style type="text/css">
#headimg {
	height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
	width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
}
#headimg h1, #headimg #desc {
	display: none;
}
</style>
<?php
}
endif;

if ( ! function_exists( 'twentyten_get_page_number' ) ) :
/**
 * Returns the page number currently being browsed
 *
 * Returns a vertical bar followed by page and the page
 * number.  Is pluggable
 *
 * @retun string
 */
function twentyten_get_page_number() {
	if ( get_query_var( 'paged' ) )
		return ' | ' . __( 'Page ' , 'twentyten' ) . get_query_var( 'paged' );
}
endif;

if ( ! function_exists( 'twentyten_the_page_number' ) ) :
/**
 * Echos the page number being browsed
 *
 * @uses twentyten_get_page_number
 *
 */
function twentyten_the_page_number() {
	echo twentyten_get_page_number();
}
endif;

if ( ! function_exists( 'twentyten_excerpt_length' ) ) :
/**
 * Sets the excerpt length to 40 charachters.  Is pluggable
 *
 * @return int
 */
function twentyten_excerpt_length( $length ) {
	return 40;
}
endif;
add_filter( 'excerpt_length', 'twentyten_excerpt_length' );

if ( ! function_exists( 'twentyten_excerpt_more' ) ) :
/**
 * Sets the read more link for excerpts to something pretty
 *
 * @return string
 *
 */
function twentyten_excerpt_more( $more ) {
	return '&nbsp;&hellip; <a href="'. get_permalink() . '">' . __('Continue&nbsp;reading&nbsp;<span class="meta-nav">&rarr;</span>', 'twentyten') . '</a>';
}
endif;
add_filter( 'excerpt_more', 'twentyten_excerpt_more' );

if ( ! function_exists( 'twentyten_comment' ) ) :
/**
 * Template for comments and pingbacks
 *
 * Used as a callback by wp_list_comments for displaying the
 * comments.  Is pluggable
 *
 */
function twentyten_comment( $comment, $args, $depth ) {
	$GLOBALS ['comment'] = $comment; ?>
	<?php if ( '' == $comment->comment_type ) : ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '<cite class="fn">%s</cite> <span class="says">says:</span>', 'twentyten' ), get_comment_author_link() ); ?>
		</div>
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your comment is awaiting moderation.', 'twentyten' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><?php printf( __( '%1$s at %2$s', 'twentyten' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'twentyten' ),'  ','' ); ?></div>

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div>
	</div>

	<?php else : ?>
	<li class="post pingback">
		<p><?php _e( 'Pingback: ', 'twentyten' ); ?><?php comment_author_link(); ?><?php edit_comment_link ( __('edit', 'twentyten'), '&nbsp;&nbsp;', '' ); ?></p>
	<?php endif;
}
endif;

if ( ! function_exists( 'twentyten_remove_gallery_css' ) ) :
/**
 * Remove inline styles on gallery shortcode
 *
 * @return string
 */
function twentyten_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
endif;
add_filter( 'gallery_style', 'twentyten_remove_gallery_css' );

if ( ! function_exists( 'twentyten_cat_list' ) ) :
/**
 * Returns the list of categories
 *
 * Returns the list of categories based on if we are or are
 * not browsing a category archive page.
 *
 * @uses twentyten_term_list
 *
 * @return string
 */
function twentyten_cat_list() {
	return twentyten_term_list( 'category', ', ', __( 'Posted in %s', 'twentyten' ), __( 'Also posted in %s', 'twentyten' ) );
}
endif;

if ( ! function_exists( 'twentyten_tag_list' ) ) :
/**
 * Returns the list of tags
 *
 * Returns the list of tags based on if we are or are not
 * browsing a tag archive page
 *
 * @uses twentyten_term_list
 *
 * @return string
 */
function twentyten_tag_list() {
	return twentyten_term_list( 'post_tag', ', ', __( 'Tagged %s', 'twentyten' ), __( 'Also tagged %s', 'twentyten' ) );
}
endif;

if ( ! function_exists( 'twentyten_term_list' ) ) :
/**
 * Returns the list of taxonomy items in multiple ways
 *
 * Returns the list of taxonomy items differently based on
 * if we are browsing a term archive page or a different
 * type of page.  If browsing a term archive page and the
 * post has no other taxonomied terms, it returns empty
 *
 * @return string
 */
function twentyten_term_list( $taxonomy, $glue = ', ', $text = '', $also_text = '' ) {
	global $wp_query, $post;
	$current_term = $wp_query->get_queried_object();
	$terms = wp_get_object_terms( $post->ID, $taxonomy );
	// If we're viewing a Taxonomy page..
	if ( isset( $current_term->taxonomy ) && $taxonomy == $current_term->taxonomy ) {
		// Remove the term from display.
		foreach ( (array) $terms as $key => $term ) {
			if ( $term->term_id == $current_term->term_id ) {
				unset( $terms[$key] );
				break;
			}
		}
		// Change to Also text as we've now removed something from the terms list.
		$text = $also_text;
	}
	$tlist = array();
	$rel = 'category' == $taxonomy ? 'rel="category"' : 'rel="tag"';
	foreach ( (array) $terms as $term ) {
		$tlist[] = '<a href="' . get_term_link( $term, $taxonomy ) . '" title="' . esc_attr( sprintf( __( 'View all posts in %s', 'twentyten' ), $term->name ) ) . '" ' . $rel . '>' . $term->name . '</a>';
	}
	if ( ! empty( $tlist ) )
		return sprintf( $text, join( $glue, $tlist ) );
	return '';
}
endif;

if ( ! function_exists( 'twentyten_widgets_init' ) ) :
/**
 * Register widgetized areas
 *
 * @uses register_sidebar
 */
function twentyten_widgets_init() {
	// Area 1
	register_sidebar( array (
		'name' => 'Primary Widget Area',
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area' , 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => "</li>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2
	register_sidebar( array (
		'name' => 'Secondary Widget Area',
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area' , 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => "</li>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 3
	register_sidebar( array (
		'name' => 'First Footer Widget Area',
		'id' => 'first-footer-widget-area',
		'description' => __( 'The first footer widget area' , 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => "</li>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 4
	register_sidebar( array (
		'name' => 'Second Footer Widget Area',
		'id' => 'second-footer-widget-area',
		'description' => __( 'The second footer widget area' , 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => "</li>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 5
	register_sidebar( array (
		'name' => 'Third Footer Widget Area',
		'id' => 'third-footer-widget-area',
		'description' => __( 'The third footer widget area' , 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => "</li>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 6
	register_sidebar( array (
		'name' => 'Fourth Footer Widget Area',
		'id' => 'fourth-footer-widget-area',
		'description' => __( 'The fourth footer widget area' , 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => "</li>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

}
endif;
add_action( 'init', 'twentyten_widgets_init' );
