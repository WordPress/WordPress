<?php
/**
 * nuThemes functions and definitions
 *
 * @package Theme Meme
 */

/*-----------------------------------------------------------------------------------*/
/*  OptionTree admin panel integration.
/* ----------------------------------------------------------------------------------*/
add_filter( 'ot_show_pages', '__return_false' );
add_filter( 'ot_show_new_layout', '__return_false' );
add_filter( 'ot_theme_mode', '__return_true' );
load_template( trailingslashit( get_template_directory() ) . 'option-tree/ot-loader.php' );


/*-----------------------------------------------------------------------------------*/
/*  Set the content width based on the theme's design and stylesheet.
/* ----------------------------------------------------------------------------------*/
if ( ! isset( $content_width ) )
	$content_width = 650; /* pixels */

/* Adjust $content_width it depending on the temaplte used. -----------------*/
function themememe_content_width() {
	global $content_width;
    
	if ( ! is_active_sidebar( 'sidebar-1' ) || is_page_template( 'full-width-page.php' ) )
		$content_width = 995;
}
add_action( 'template_redirect', 'themememe_content_width' );    


/*-----------------------------------------------------------------------------------*/
/*  Sets up theme defaults and registers support for various WordPress features.
/* ----------------------------------------------------------------------------------*/
if ( ! function_exists( 'themememe_setup' ) ) :
function themememe_setup() {

	/* Make theme available for translation. ------------------------------------*/
	// load_theme_textdomain( 'themememe', get_template_directory() . '/languages' );

	/* Add default posts and comments RSS feed links to head. -------------------*/
	add_theme_support( 'automatic-feed-links' );

	/* Enable support for Post Thumbnails on posts and pages. -------------------*/
	add_theme_support( 'post-thumbnails' );

	/* This theme uses wp_nav_menu() in one location. ---------------------------*/
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'themememe' ),
	) );
}
endif;
add_action( 'after_setup_theme', 'themememe_setup' );


/*-----------------------------------------------------------------------------------*/
/*  Register widgetized area and update sidebar with default widgets.
/* ----------------------------------------------------------------------------------*/
function themememe_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'themememe' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer #1', 'themememe' ),
		'id'            => 'sidebar-2',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer #2', 'themememe' ),
		'id'            => 'sidebar-3',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer #3', 'themememe' ),
		'id'            => 'sidebar-4',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer #4', 'themememe' ),
		'id'            => 'sidebar-5',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'themememe_widgets_init' );


/*-----------------------------------------------------------------------------------*/
/*  Count the number of footer sidebars to enable dynamic classes for the footer.
/* ----------------------------------------------------------------------------------*/
function themememe_extra_col_class() {
	$count = 0;

	if ( is_active_sidebar( 'sidebar-2' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-3' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-4' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-5' ) )
		$count++;

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'col-sm-12 widget-area';
			break;
		case '2':
			$class = 'col-sm-6 widget-area';
			break;
		case '3':
			$class = 'col-sm-4 widget-area';
			break;
		case '4':
			$class = 'col-sm-3 widget-area';
			break;
	}

	if ( $class )
		echo 'class="' . $class . '"';
}

/*  Site title/logo
/* ------------------------------------ */
if ( ! function_exists( 'themememe_site_title' ) ) {
	function themememe_site_title() {
		// Text or image?
		if ( ot_get_option('custom-logo') ) {
			$logo = '<img src="'.ot_get_option('custom-logo').'" alt="'.esc_attr(get_bloginfo('name', 'display')).'">';
		} else {
			$logo = get_bloginfo('name');
		}
		
		$link = '<a href="'.esc_url(home_url('/')).'" rel="home">'.$logo.'</a>';
		
		if ( is_front_page() || is_home() ) {
			$sitename = '<h1 class="site-title">'.$link.'</h1>'."\n";
		} else {
			$sitename = '<div class="site-title">'.$link.'</div>'."\n";
		}
		
		return $sitename;
	}
}

/*-----------------------------------------------------------------------------------*/
/*  Returns the Google font stylesheet URL, if available.
/* ----------------------------------------------------------------------------------*/
function themememe_fonts_url() {
	$fonts_url = '';

	/* Raleway. -------------------------------------------------------------------*/
	$raleway = _x( 'on', 'Raleway font: on or off', 'themememe' );

	/* Noto Serif. ----------------------------------------------------------------*/
	$noto_serif = _x( 'on', 'Noto Serif font: on or off', 'themememe' );

	if ( 'off' !== $raleway || 'off' !== $noto_serif ) {
		$font_families = array();

		if ( 'off' !== $raleway )
			$font_families[] = 'Raleway:400,500,700,900';

		if ( 'off' !== $noto_serif )
			$font_families[] = 'Noto Serif:400,700,400italic,700italic';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);
		$fonts_url = add_query_arg( $query_args, "//fonts.googleapis.com/css" );
	}

	return $fonts_url;
}

/*-----------------------------------------------------------------------------------*/
/*  Enqueue scripts and styles
/* ----------------------------------------------------------------------------------*/

/* Enqueue scripts. ---------------------------------------------------------*/
function themememe_scripts() {
	wp_enqueue_script( 'dropkick', get_template_directory_uri() . '/js/jquery.dropkick.min.js', array( 'jquery' ), '', false );
	wp_enqueue_script( 'flexslider', get_template_directory_uri() . '/js/jquery.flexslider.min.js', array( 'jquery' ), '', false );
	wp_enqueue_script( 'themememe-scripts', get_template_directory_uri() . '/js/scripts.js', array( 'jquery' ), '', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'themememe_scripts' );


/* Enqueue styles. ----------------------------------------------------------*/
function themememe_styles() {
	wp_enqueue_style( 'themememe-base', get_template_directory_uri() . '/css/base.css' );
	wp_enqueue_style( 'themememe-icons', get_template_directory_uri().'/css/font-awesome.min.css' );
	wp_enqueue_style( 'themememe-fonts', themememe_fonts_url() );
	wp_enqueue_style( 'themememe-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'themememe_styles' );


/*-----------------------------------------------------------------------------------*/
/*  Actions
/* ----------------------------------------------------------------------------------*/

/* Script for no-js / js class. ---------------------------------------------*/
function themememe_html_js_class() {
	echo '<script>document.documentElement.className = document.documentElement.className.replace("no-js","js");</script>'. "\n";
}
add_action( 'wp_head', 'themememe_html_js_class', 1 );


/* IE js header. ------------------------------------------------------------*/
function themememe_ie_js_header() {
	echo '<!--[if lt IE 9]>'. "\n";
	echo '<script src="' . esc_url( get_template_directory_uri() . '/js/ie/html5.js' ) . '"></script>'. "\n";
	echo '<script src="' . esc_url( get_template_directory_uri() . '/js/ie/selectivizr.js' ) . '"></script>'. "\n";
	echo '<![endif]-->'. "\n";
}
add_action( 'wp_head', 'themememe_ie_js_header' );


/* IE js footer. ------------------------------------------------------------*/
function themememe_ie_js_footer() {
	echo '<!--[if lt IE 9]>'. "\n";
	echo '<script src="' . esc_url( get_template_directory_uri() . '/js/ie/respond.js' ) . '"></script>'. "\n";
	echo '<![endif]-->'. "\n";
}
add_action( 'wp_footer', 'themememe_ie_js_footer', 20 );


/*-----------------------------------------------------------------------------------*/
/*  Filters
/* ----------------------------------------------------------------------------------*/

/* Show a home link. --------------------------------------------------------*/
function themememe_page_menu_args( $args ) {
	$args['show_home'] = true;
	$args['menu_class'] = 'clearfix menu-bar';
	return $args;
}
add_filter( 'wp_page_menu_args', 'themememe_page_menu_args' );


/* Adds custom classes to the array of body classes. ------------------------*/
function themememe_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() )
		$classes[] = 'group-blog';

	//  Browser detection
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if ( $is_lynx ) $classes[] = 'lynx';
	elseif ( $is_gecko ) $classes[] = 'gecko';
	elseif ( $is_opera ) $classes[] = 'opera';
	elseif ( $is_NS4 ) $classes[] = 'ns4';
	elseif ( $is_safari ) $classes[] = 'safari';
	elseif ( $is_chrome ) $classes[] = 'chrome';
	elseif ( $is_IE ) {
		$browser = $_SERVER['HTTP_USER_AGENT'];
		$browser = substr( "$browser", 25, 8);
		if ( $browser == "MSIE 7.0" ) {
			$classes[] = 'ie7';
			$classes[] = 'ie';
		} elseif ( $browser == "MSIE 6.0" ) {
			$classes[] = 'ie6';
			$classes[] = 'ie';
		} elseif ( $browser == "MSIE 8.0" ) {
			$classes[] = 'ie8';
			$classes[] = 'ie';
		} elseif ( $browser == "MSIE 9.0" ) {
			$classes[] = 'ie9';
			$classes[] = 'ie';
		} else {
			$classes[] = 'ie';
		}
	}
	else $classes[] = 'unknown';

	if( $is_iphone ) $classes[] = 'iphone';

	return $classes;
}
add_filter( 'body_class', 'themememe_body_classes' );


/* Filters wp_title to print a neat <title> tag. ----------------------------*/
function themememe_wp_title( $title, $sep ) {
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
		$title .= " $sep " . sprintf( __( 'Page %s', 'themememe' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'themememe_wp_title', 10, 2 );

/* Returns a "Continue Reading" link for  excerpts. -------------------------*/
function themememe_continue_reading_link() {
	return ' <p class="more-link"><a href="'. esc_url( get_permalink() ) . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'themememe' ) . '</a></p>';
}


/* Adds a pretty "Continue Reading" link to defined excerpts. ---------------*/
function themememe_custom_excerpt( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= '&hellip; ' . themememe_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'themememe_custom_excerpt' );


/* Replaces "[...]" with an ellipsis ----------------------------------------*/
function themememe_auto_excerpt_more( $output ) {
	$output = '';
	$output .= '&hellip; ' . themememe_continue_reading_link();
	return $output;
}
add_filter( 'excerpt_more', 'themememe_auto_excerpt_more' );


/* Sets the post excerpt length to maximum 40 words. ------------------------*/
function themememe_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'themememe_excerpt_length' );


/* Add shortcode support to text widget. ------------------------------------*/
add_filter( 'widget_text', 'do_shortcode' );


/* Add responsive container to embeds. --------------------------------------*/
function themememe_embed_html( $html ) {
	return '<div class="video-container">' . $html . '</div>';
}
add_filter( 'embed_oembed_html', 'themememe_embed_html', 10, 3 );
add_filter( 'video_embed_html', 'themememe_embed_html' ); // Jetpack


/* Upscale cropped thumbnails. -----------------------------------------------*/
function themememe_thumbnail_upscale( $default, $orig_w, $orig_h, $new_w, $new_h, $crop ){
	if ( !$crop ) return null; // let the wordpress default function handle this

	$aspect_ratio = $orig_w / $orig_h;
	$size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

	$crop_w = round($new_w / $size_ratio);
	$crop_h = round($new_h / $size_ratio);

	$s_x = floor( ($orig_w - $crop_w) / 2 );
	$s_y = floor( ($orig_h - $crop_h) / 2 );

	return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
}
add_filter( 'image_resize_dimensions', 'themememe_thumbnail_upscale', 10, 6 );


/* Add wmode transparent to media embeds. ------------------------------------*/
function themememe_embed_wmode_transparent( $html, $url, $attr ) {
	if ( strpos( $html, "<embed src=" ) !== false )
	   { return str_replace('</param><embed', '</param><param name="wmode" value="opaque"></param><embed wmode="opaque" ', $html); }
	elseif ( strpos ( $html, 'feature=oembed' ) !== false )
	   { return str_replace( 'feature=oembed', 'feature=oembed&wmode=opaque', $html ); }
	else
	   { return $html; }
}
add_filter( 'embed_oembed_html', 'themememe_embed_wmode_transparent', 10, 3 );


/* Filter post_gallery to display gallery as slideshow. ----------------------*/
function themememe_post_gallery( $output, $attr) {
	global $post, $wp_locale;

	static $instance = 0;
	$instance++;

	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	// exception for Jetpack galleries
	if ( isset( $attr['type'] ) ) {
		return;
	}
  
	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'li',
		'icontag'    => 'div',
		'captiontag' => 'div',
		'columns'    => 3,
		'size'       => array(650,350),
		'include'    => '',
		'exclude'    => ''
	), $attr));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$selector = "slider-{$instance}";
	$captiontag = tag_escape($captiontag);

	$output .= "<div id='{$selector}' class='flexslider slider-{$id}'>";

	$i = 0;
	$output .= "<ul class='slides'>";
	foreach ( $attachments as $id => $attachment ) {
		$itemclass = ($i==0) ? 'item active' : 'item';
		$link = wp_get_attachment_link($id, $size, true, false);

		$output .= "<{$itemtag} class='{$itemclass}'>";
		$output .= "$link";

		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "
				<{$captiontag} class='flex-caption'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
		}
		$output .= "</{$itemtag}>";
		$i++;
	}
	$output .= "</ul>";

	$output .= "</div>";
	return $output;
}
add_filter( 'post_gallery', 'themememe_post_gallery', 10, 2 );


/* Remove gallery inline styling. --------------------------------------------*/
add_filter( 'use_default_gallery_style', '__return_false' );


/* Add custom class to comment avatar. ---------------------------------------*/
function themememe_avatar_class($class) {
	$class = str_replace("class='avatar", "class='comment-avatar ", $class) ;
	return $class;
}
add_filter( 'get_avatar', 'themememe_avatar_class' );


/*-----------------------------------------------------------------------------------*/
/*  Includes
/* ----------------------------------------------------------------------------------*/
define('THEMEMEME_PATH', get_template_directory() );

/* Customizer support. ------------------------------------------------------*/
require THEMEMEME_PATH . '/inc/customizer.php';

/* Custom template tags for this theme. -------------------------------------*/
require THEMEMEME_PATH . '/inc/template-tags.php';

/* Theme Options. -----------------------------------------------------------*/
require THEMEMEME_PATH . '/inc/theme-options.php';