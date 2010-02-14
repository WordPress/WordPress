<?php

// Set the content width based on the Theme CSS
if ( ! isset( $content_width ) )
   $content_width = 640;

if ( ! function_exists( 'twentyten_init' ) ) :
function twentyten_init() {
	// Your Changeable header business starts here
	// No CSS, just IMG call
	define( 'HEADER_TEXTCOLOR', '');
	define( 'HEADER_IMAGE', '%s/images/header-1.jpg'); // %s is theme dir uri
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyten_header_image_width',  940 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyten_header_image_height',	198 ) );
	define( 'NO_HEADER_TEXT', true );

	add_custom_image_header( '', 'twentyten_admin_header_style' );
	// and thus ends the changeable header business

	add_custom_background();

	// This theme needs post thumbnails
	add_theme_support( 'post-thumbnails' );

	// We'll be using them for custom header images on posts and pages
	// so we want them to be 940 pixels wide by 198 pixels tall (larger images will be auto-cropped to fit)
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Add default posts and comments RSS feed links to head.
	automatic_feed_links();

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'twentyten', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );
}
endif;
add_action( 'themes_loaded', 'twentyten_init' );

if ( ! function_exists( 'twentyten_admin_header_style' ) ) :
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

// Get the page number
if ( ! function_exists( 'twentyten_get_page_number' ) ) :
function twentyten_get_page_number() {
	if ( get_query_var('paged') )
		return ' | ' . __( 'Page ' , 'twentyten') . get_query_var('paged');
}
endif;

// Echo the page number
if ( ! function_exists( 'twentyten_the_page_number' ) ) :
function twentyten_the_page_number() {
	echo twentyten_get_page_number();
}
endif;

// Control excerpt length
if ( ! function_exists( 'twentyten_excerpt_length' ) ) :
function twentyten_excerpt_length( $length ) {
	return 40;
}
endif;
add_filter( 'excerpt_length', 'twentyten_excerpt_length' );


// Make a nice read more link on excerpts
if ( ! function_exists( 'twentyten_excerpt_more' ) ) :
function twentyten_excerpt_more($more) {
	return '&nbsp;&hellip; <a href="'. get_permalink() . '">' . 'Continue&nbsp;reading&nbsp;<span class="meta-nav">&rarr;</span>' . '</a>';
}
endif;
add_filter( 'excerpt_more', 'twentyten_excerpt_more' );


// Template for comments and pingbacks
if ( ! function_exists( 'twentyten_comment' ) ) :
function twentyten_comment( $comment, $args, $depth ) {
	$GLOBALS ['comment'] = $comment; ?>
	<?php if ( '' == $comment->comment_type ) : ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '<cite class="fn">%s</cite> <span class="says">says:</span>' ), get_comment_author_link() ); ?>
		</div>
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your comment is awaiting moderation.', 'twentyten' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><?php printf( __( '%1$s at %2$s' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'twentyten' ),'  ','' ); ?></div>

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

// Make the Visual Editor styles match the theme's styles
if ( ! function_exists( 'twentyten_editor_style' ) ) :
function twentyten_editor_style( $url ) {
	if ( ! empty( $url ) )
		$url .= ',';

	// Change the path here if using sub-directory
	$url .= trailingslashit( get_stylesheet_directory_uri() ) . 'editor-style.css';

	return $url;
}
endif;
add_filter( 'mce_css', 'twentyten_editor_style' );

// Remove inline styles on gallery shortcode
if ( ! function_exists( 'twentyten_remove_gallery_css' ) ) :
function twentyten_remove_gallery_css() {
	return "\t\t<div class='gallery'>\n\t\t";
}
endif;
add_filter( 'gallery_style', 'twentyten_remove_gallery_css' );

if ( ! function_exists( 'twentyten_cat_list' ) ) :
function twentyten_cat_list() {
	return twentyten_term_list('category', ', ', __('Posted in %s', 'twentyten'), __('Also posted in %s', 'twentyten') );
}
endif;

if ( ! function_exists( 'twentyten_tag_list' ) ) :
function twentyten_tag_list() {
	return twentyten_term_list('post_tag', ', ', __('Tagged %s', 'twentyten'), __('Also tagged %s', 'twentyten') );
}
endif;

if ( ! function_exists( 'twentyten_term_list' ) ) :
function twentyten_term_list($taxonomy, $glue = ', ', $text = '', $also_text = '') {
	global $wp_query, $post;
	$current_term = $wp_query->get_queried_object();
	$terms = wp_get_object_terms($post->ID, $taxonomy);
	// If we're viewing a Taxonomy page.. 
	if ( isset($current_term->taxonomy) && $taxonomy == $current_term->taxonomy ) {
		// Remove the term from display.
		foreach ( (array)$terms as $key => $term ) {
			if ( $term->term_id == $current_term->term_id ) {
				unset($terms[$key]);
				break;
			}
		}
		// Change to Also text as we've now removed something from the terms list.
		$text = $also_text;
	}
	$tlist = array();
	$rel = 'category' == $taxonomy ? 'rel="category"' : 'rel="tag"';
	foreach ( (array)$terms as $term ) {
		$tlist[] = '<a href="' . get_term_link( $term, $taxonomy ) . '" title="' . esc_attr( sprintf( __( 'View all posts in %s', 'twentyten' ), $term->name ) ) . '" ' . $rel . '>' . $term->name . '</a>';
	}
	if ( !empty($tlist) )
		return sprintf($text, join($glue, $tlist));
	return '';
}
endif;

// Register widgetized areas
if ( ! function_exists( 'twentyten_widgets_init' ) ) :
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
