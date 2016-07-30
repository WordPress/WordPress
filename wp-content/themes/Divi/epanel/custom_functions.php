<?php

// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_theme_support( 'custom-background', apply_filters( 'et_custom_background_args', array() ) );

if ( function_exists( 'add_post_type_support' ) ) {
	add_post_type_support( 'page', 'excerpt' );
}

add_theme_support( 'automatic-feed-links' );

add_action( 'init', 'et_activate_features' );

function et_activate_features(){
	define( 'ET_SHORTCODES_VERSION', et_get_theme_version() );

	/* activate shortcodes */
	require_once TEMPLATEPATH . '/epanel/shortcodes/shortcodes.php';

	/* activate page templates */
	require_once TEMPLATEPATH . '/includes/page_templates/page_templates.php';

	/* import epanel settings */
	require_once TEMPLATEPATH . '/includes/import_settings.php';
}

add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_excerpt', 'do_shortcode' );

if ( ! function_exists( 'et_get_theme_version' ) ) :
function et_get_theme_version() {
	$theme_info = wp_get_theme();

	if ( is_child_theme() ) {
		$theme_info = wp_get_theme( $theme_info->parent_theme );
	}

	$theme_version = $theme_info->display( 'Version' );

	return $theme_version;
}
endif;

if ( ! function_exists( 'et_options_stored_in_one_row' ) ) {

	function et_options_stored_in_one_row(){
		global $et_store_options_in_one_row;

		return isset( $et_store_options_in_one_row ) ? (bool) $et_store_options_in_one_row : false;
	}

}

/**
 * Gets option value from the single theme option, stored as an array in the database
 * if all options stored in one row.
 * Stores the serialized array with theme options into the global variable on the first function run on the page.
 *
 * If options are stored as separate rows in database, it simply uses get_option() function.
 *
 * @param string $option_name Theme option name.
 * @param string $default_value Default value that should be set if the theme option isn't set.
 * @param string $used_for_object "Object" name that should be translated into corresponding "object" if WPML is activated.
 * @return mixed Theme option value or false if not found.
 */
if ( ! function_exists( 'et_get_option' ) ) {

	function et_get_option( $option_name, $default_value = '', $used_for_object = '', $force_default_value = false, $is_global_setting = false, $global_setting_main_name = '', $global_setting_sub_name = '' ){
		global $et_theme_options, $shortname;

		if ( $is_global_setting ) {
			$option_value = '';

			$et_global_setting = get_option( $global_setting_main_name );

			if ( false !== $et_global_setting && isset( $et_global_setting[ $global_setting_sub_name ] ) ) {
				$option_value = $et_global_setting[ $global_setting_sub_name ];
			}
		} else if ( et_options_stored_in_one_row() ) {
			$et_theme_options_name = 'et_' . $shortname;

			if ( ! isset( $et_theme_options ) || isset( $_POST['wp_customize'] ) ) {
				$et_theme_options = get_option( $et_theme_options_name );
			}
			$option_value = isset( $et_theme_options[$option_name] ) ? $et_theme_options[$option_name] : false;
		} else {
			$option_value = get_option( $option_name );
		}

		// option value might be equal to false, so check if the option is not set in the database
		if ( et_options_stored_in_one_row() && ! isset( $et_theme_options[ $option_name ] ) && ( '' != $default_value || $force_default_value ) ) {
			$option_value = $default_value;
		}

		if ( '' != $used_for_object && in_array( $used_for_object, array( 'page', 'category' ) ) && is_array( $option_value ) )
			$option_value = et_generate_wpml_ids( $option_value, $used_for_object );

		return $option_value;
	}

}

if ( ! function_exists( 'et_update_option' ) ) {

	function et_update_option( $option_name, $new_value, $is_new_global_setting = false, $global_setting_main_name = '', $global_setting_sub_name = '' ){
		global $et_theme_options, $shortname;

		if ( $is_new_global_setting && '' !== $global_setting_main_name && '' !== $global_setting_sub_name ) {
			$global_setting = get_option( $global_setting_main_name );

			if ( ! $global_setting ) {
				$global_setting = array();
			}

			$global_setting[ $global_setting_sub_name ] = $new_value;

			$option_name = $global_setting_main_name;
			$new_value   = $global_setting;
		} else if ( et_options_stored_in_one_row() ) {
			$et_theme_options_name = 'et_' . $shortname;

			if ( ! isset( $et_theme_options ) ) $et_theme_options = get_option( $et_theme_options_name );
			$et_theme_options[$option_name] = $new_value;

			$option_name = $et_theme_options_name;
			$new_value = $et_theme_options;
		}

		update_option( $option_name, $new_value );
	}

}

if ( ! function_exists( 'et_delete_option' ) ) {

	function et_delete_option( $option_name ){
		global $et_theme_options, $shortname;

		if ( et_options_stored_in_one_row() ) {
			$et_theme_options_name = 'et_' . $shortname;

			if ( ! isset( $et_theme_options ) ) $et_theme_options = get_option( $et_theme_options_name );

			unset( $et_theme_options[$option_name] );
			update_option( $et_theme_options_name, $et_theme_options );
		} else {
			delete_option( $option_name );
		}
	}

}

add_filter( 'body_class', 'et_browser_body_class' );

function et_browser_body_class($classes) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if($is_lynx) $classes[] = 'lynx';
	elseif($is_gecko) $classes[] = 'gecko';
	elseif($is_opera) $classes[] = 'opera';
	elseif($is_NS4) $classes[] = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) $classes[] = 'ie';
	else $classes[] = 'unknown';

	if($is_iphone) $classes[] = 'iphone';
	return $classes;
}

/*this function allows for the auto-creation of post excerpts*/
if ( ! function_exists( 'truncate_post' ) ) {

	function truncate_post( $amount, $echo = true, $post = '' ) {
		global $shortname;

		if ( '' == $post ) global $post;

		$post_excerpt = '';
		$post_excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );

		if ( 'on' == et_get_option( $shortname . '_use_excerpt' ) && '' != $post_excerpt ) {
			if ( $echo ) echo $post_excerpt;
			else return $post_excerpt;
		} else {
			// get the post content
			$truncate = $post->post_content;

			// remove caption shortcode from the post content
			$truncate = preg_replace( '@\[caption[^\]]*?\].*?\[\/caption]@si', '', $truncate );

			// remove post nav shortcode from the post content
			$truncate = preg_replace( '@\[et_pb_post_nav[^\]]*?\].*?\[\/et_pb_post_nav]@si', '', $truncate );

			// Remove audio shortcode from post content to prevent unwanted audio file on the excerpt
			// due to unparsed audio shortcode
			$truncate = preg_replace( '@\[audio[^\]]*?\].*?\[\/audio]@si', '', $truncate );

			// apply content filters
			$truncate = apply_filters( 'the_content', $truncate );

			// decide if we need to append dots at the end of the string
			if ( strlen( $truncate ) <= $amount ) {
				$echo_out = '';
			} else {
				$echo_out = '...';
				// $amount = $amount - 3;
			}

			// trim text to a certain number of characters, also remove spaces from the end of a string ( space counts as a character )
			$truncate = rtrim( et_wp_trim_words( $truncate, $amount, '' ) );

			// remove the last word to make sure we display all words correctly
			if ( '' != $echo_out ) {
				$new_words_array = (array) explode( ' ', $truncate );
				array_pop( $new_words_array );

				$truncate = implode( ' ', $new_words_array );

				// append dots to the end of the string
				$truncate .= $echo_out;
			}

			if ( $echo ) echo $truncate;
			else return $truncate;
		};
	}

}

if ( ! function_exists( 'et_wp_trim_words' ) ) {

	function et_wp_trim_words( $text, $num_words = 55, $more = null ) {
		if ( null === $more )
			$more = esc_html__( '&hellip;' );
		$original_text = $text;
		$text = wp_strip_all_tags( $text );

		$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
		preg_match_all( '/./u', $text, $words_array );
		$words_array = array_slice( $words_array[0], 0, $num_words + 1 );
		$sep = '';

		if ( count( $words_array ) > $num_words ) {
			array_pop( $words_array );
			$text = implode( $sep, $words_array );
			$text = $text . $more;
		} else {
			$text = implode( $sep, $words_array );
		}

		return $text;
	}

}

/*this function truncates titles to create preview excerpts*/
if ( ! function_exists( 'truncate_title' ) ) {

	function truncate_title( $amount, $echo = true, $post = '' ) {
		if ( $post == '' ) $truncate = get_the_title();
		else $truncate = $post->post_title;

		if ( strlen( $truncate ) <= $amount ) $echo_out = '';
		else $echo_out = '...';

		$truncate = et_wp_trim_words( $truncate, $amount, '' );

		if ( '' != $echo_out ) $truncate .= $echo_out;

		if ( $echo )
			echo $truncate;
		else
			return $truncate;
	}

}


/*this function allows users to use the first image in their post as their thumbnail*/
if ( ! function_exists( 'et_first_image' ) ) {

	function et_first_image() {
		global $post;
		$img = '';
		$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );
		if ( isset( $matches[1][0] ) ) $img = $matches[1][0];

		return trim( $img );
	}

}


/* this function gets thumbnail from Post Thumbnail or Custom field or First post image */
if ( ! function_exists( 'get_thumbnail' ) ) {

	function get_thumbnail($width=100, $height=100, $class='', $alttext='', $titletext='', $fullpath=false, $custom_field='', $post='') {
		if ( $post == '' ) global $post;
		global $shortname;

		$thumb_array['thumb'] = '';
		$thumb_array['use_timthumb'] = true;
		if ($fullpath) $thumb_array['fullpath'] = ''; //full image url for lightbox

		$new_method = true;

		if ( has_post_thumbnail( $post->ID ) ) {
			$thumb_array['use_timthumb'] = false;

			$et_fullpath = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
			$thumb_array['fullpath'] = $et_fullpath[0];
			$thumb_array['thumb'] = $thumb_array['fullpath'];
		}

		if ($thumb_array['thumb'] == '') {
			if ($custom_field == '') $thumb_array['thumb'] = esc_attr( get_post_meta( $post->ID, 'Thumbnail', $single = true ) );
			else {
				$thumb_array['thumb'] = esc_attr( get_post_meta( $post->ID, $custom_field, $single = true ) );
				if ($thumb_array['thumb'] == '') $thumb_array['thumb'] = esc_attr( get_post_meta( $post->ID, 'Thumbnail', $single = true ) );
			}

			if (($thumb_array['thumb'] == '') && ((et_get_option( $shortname.'_grab_image' )) == 'on')) {
				$thumb_array['thumb'] = esc_attr( et_first_image() );
				if ( $fullpath ) $thumb_array['fullpath'] = $thumb_array['thumb'];
			}

			#if custom field used for small pre-cropped image, open Thumbnail custom field image in lightbox
			if ($fullpath) {
				$thumb_array['fullpath'] = $thumb_array['thumb'];
				if ($custom_field == '') $thumb_array['fullpath'] = apply_filters( 'et_fullpath', et_path_reltoabs( esc_attr( $thumb_array['thumb'] ) ) );
				elseif ( $custom_field <> '' && get_post_meta( $post->ID, 'Thumbnail', $single = true ) ) $thumb_array['fullpath'] = apply_filters( 'et_fullpath', et_path_reltoabs( esc_attr( get_post_meta( $post->ID, 'Thumbnail', $single = true ) ) ) );
			}
		}

		return $thumb_array;
	}

}

/* this function prints thumbnail from Post Thumbnail or Custom field or First post image */
if ( ! function_exists( 'print_thumbnail' ) ) {

	function print_thumbnail($thumbnail = '', $use_timthumb = true, $alttext = '', $width = 100, $height = 100, $class = '', $echoout = true, $forstyle = false, $resize = true, $post='', $et_post_id = '' ) {
		if ( is_array( $thumbnail ) ) {
			extract( $thumbnail );
		}

		if ( $post == '' ) global $post, $et_theme_image_sizes;

		$output = '';

		$et_post_id = '' != $et_post_id ? (int) $et_post_id : $post->ID;

		if ( has_post_thumbnail( $et_post_id ) ) {
			$thumb_array['use_timthumb'] = false;

			$image_size_name = $width . 'x' . $height;
			$et_size = isset( $et_theme_image_sizes ) && array_key_exists( $image_size_name, $et_theme_image_sizes ) ? $et_theme_image_sizes[$image_size_name] : array( $width, $height );

			$et_attachment_image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id( $et_post_id ), $et_size );
			$thumbnail = $et_attachment_image_attributes[0];
		} else {
			$thumbnail_orig = $thumbnail;

			$thumbnail = et_multisite_thumbnail( $thumbnail );

			$cropPosition = '';

			$allow_new_thumb_method = false;

			$new_method = true;
			$new_method_thumb = '';
			$external_source = false;

			$allow_new_thumb_method = !$external_source && $new_method && $cropPosition == '';

			if ( $allow_new_thumb_method && $thumbnail <> '' ) {
				$et_crop = get_post_meta( $post->ID, 'et_nocrop', true ) == '' ? true : false;
				$new_method_thumb = et_resize_image( et_path_reltoabs( $thumbnail ), $width, $height, $et_crop );
				if ( is_wp_error( $new_method_thumb ) ) $new_method_thumb = '';
			}

			$thumbnail = $new_method_thumb;
		}

		if ( false === $forstyle ) {
			$output = '<img src="' . esc_url( $thumbnail ) . '"';

			if ($class <> '') $output .= " class='" . esc_attr( $class ) . "' ";

			$dimensions = apply_filters( 'et_print_thumbnail_dimensions', " width='" . esc_attr( $width ) . "' height='" .esc_attr( $height ) . "'" );

			$output .= " alt='" . esc_attr( strip_tags( $alttext ) ) . "'{$dimensions} />";

			if ( ! $resize ) $output = $thumbnail;
		} else {
			$output = $thumbnail;
		}

		if ($echoout) echo $output;
		else return $output;
	}

}

if ( ! function_exists( 'et_new_thumb_resize' ) ) {

	function et_new_thumb_resize( $thumbnail, $width, $height, $alt='', $forstyle = false ){
		global $shortname;

		$new_method = true;
		$new_method_thumb = '';
		$external_source = false;

		$allow_new_thumb_method = !$external_source && $new_method;

		if ( $allow_new_thumb_method && $thumbnail <> '' ) {
			$et_crop = true;
			$new_method_thumb = et_resize_image( $thumbnail, $width, $height, $et_crop );
			if ( is_wp_error( $new_method_thumb ) ) $new_method_thumb = '';
		}

		$thumb = esc_attr( $new_method_thumb );

		$output = '<img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $alt ) . '" width =' . esc_attr( $width ) . ' height=' . esc_attr( $height ) . ' />';

		return ( !$forstyle ) ? $output : $thumb;
	}

}

if ( ! function_exists( 'et_multisite_thumbnail' ) ) {

	function et_multisite_thumbnail( $thumbnail = '' ) {
		// do nothing if it's not a Multisite installation or current site is the main one
		if ( is_main_site() ) return $thumbnail;

		# get the real image url
		preg_match( '#([_0-9a-zA-Z-]+/)?files/(.+)#', $thumbnail, $matches );

		if ( isset( $matches[2] ) ) {
			$file = rtrim( BLOGUPLOADDIR, '/' ) . '/' . str_replace( '..', '', $matches[2] );
			if ( is_file( $file ) ) $thumbnail = str_replace( ABSPATH, trailingslashit( get_site_url( 1 ) ), $file );
			else $thumbnail = '';
		}

		return $thumbnail;
	}

}

if ( ! function_exists( 'et_is_portrait' ) ) {

	function et_is_portrait($imageurl, $post='', $ignore_cfields = false){
		if ( $post == '' ) global $post;

		if ( get_post_meta( $post->ID, 'et_disable_portrait', true ) == 1 ) return false;

		if ( !$ignore_cfields ) {
			if ( get_post_meta( $post->ID, 'et_imagetype', true ) == 'l' ) return false;
			if ( get_post_meta( $post->ID, 'et_imagetype', true ) == 'p' ) return true;
		}

		$imageurl = et_path_reltoabs( et_multisite_thumbnail( $imageurl ) );

		$et_thumb_size = @getimagesize( $imageurl );
		if ( empty( $et_thumb_size ) ) {
			$et_thumb_size = @getimagesize( str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $imageurl ) );
			if ( empty( $et_thumb_size ) ) return false;
		}
		$et_thumb_width = $et_thumb_size[0];
		$et_thumb_height = $et_thumb_size[1];

		$result = ($et_thumb_width < $et_thumb_height) ? true : false;

		return $result;
	}

}

if ( ! function_exists( 'et_path_reltoabs' ) ) {

	function et_path_reltoabs( $imageurl ){
		if ( strpos( strtolower( $imageurl ), 'http://' ) !== false || strpos( strtolower( $imageurl ), 'https://' ) !== false ) return $imageurl;

		if ( strpos( strtolower( $imageurl ), $_SERVER['HTTP_HOST'] ) !== false )
			return $imageurl;
		else {
			$imageurl = esc_url( apply_filters( 'et_path_relative_image', site_url() . '/' ) . $imageurl );
		}

		return $imageurl;
	}

}

if ( ! function_exists( 'in_subcat' ) ) {

	function in_subcat($blogcat,$current_cat='') {
		$in_subcategory = false;

		if (cat_is_ancestor_of( $blogcat, $current_cat ) || $blogcat == $current_cat) $in_subcategory = true;

		return $in_subcategory;
	}

}

if ( ! function_exists( 'show_page_menu' ) ) {

	function show_page_menu($customClass = 'nav clearfix', $addUlContainer = true, $addHomeLink = true){
		global $shortname, $themename, $exclude_pages, $strdepth, $page_menu, $is_footer;

		//excluded pages
		if ( et_get_option( $shortname.'_menupages' ) <> '' ) {
			$exclude_pages = implode( ",", et_get_option( $shortname.'_menupages' ) );
		}

		//dropdown for pages
		$strdepth = '';
		if ( et_get_option( $shortname.'_enable_dropdowns' ) == 'on' ) {
			$strdepth = "depth=".et_get_option( $shortname.'_tiers_shown_pages' );
		}

		if ( $strdepth == '' ) {
			$strdepth = "depth=1";
		}

		if ( $is_footer ) {
			$strdepth = "depth=1";
			$strdepth2 = $strdepth;
		}

		$page_menu = wp_list_pages( "sort_column=".et_get_option( $shortname.'_sort_pages' )."&sort_order=".et_get_option( $shortname.'_order_page' )."&".$strdepth."&exclude=".$exclude_pages."&title_li=&echo=0" );

		if ( $addUlContainer ) echo '<ul class="'.$customClass.'">';
		if (et_get_option( $shortname . '_home_link' ) == 'on' && $addHomeLink) { ?>
				<li <?php if ( is_front_page() || is_home() ) echo 'class="current_page_item"' ?>><a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home', $themename ); ?></a></li>
			<?php };

			echo $page_menu;
		if ( $addUlContainer ) echo '</ul>';
	}

}

if ( ! function_exists( 'show_categories_menu' ) ) {

	function show_categories_menu($customClass = 'nav clearfix', $addUlContainer = true){
		global $shortname, $themename, $category_menu, $exclude_cats, $hide, $strdepth2, $projects_cat;

		//excluded categories
		if (et_get_option( $shortname.'_menucats' ) <> '') $exclude_cats = implode( ",", et_get_option( $shortname.'_menucats' ) );

		//hide empty categories
		if (et_get_option( $shortname.'_categories_empty' ) == 'on') $hide = '1';
		else $hide = '0';

		//dropdown for categories
		$strdepth2 = '';
		if ( et_get_option( $shortname.'_enable_dropdowns_categories' ) == 'on' ) $strdepth2 = "depth=".et_get_option( $shortname.'_tiers_shown_categories' );
		if ( $strdepth2 == '' ) $strdepth2 = "depth=1";

		$args = "orderby=".et_get_option( $shortname.'_sort_cat' )."&order=".et_get_option( $shortname.'_order_cat' )."&".$strdepth2."&exclude=".$exclude_cats."&hide_empty=".$hide."&title_li=&echo=0";

		$categories = get_categories( $args );

		if ( !empty( $categories ) ) {
			$args_array = wp_parse_args( $args );

			if ( isset( $args_array['exclude'] ) && '' !== $args_array['exclude'] ) {
				$args_array['exclude'] = explode( ',', $args_array['exclude'] );
			}

			$category_menu = wp_list_categories( $args_array );
			if ( $addUlContainer ) echo '<ul class="'.$customClass.'">';
				echo $category_menu;
			if ( $addUlContainer ) echo '</ul>';
		}
	}

}

function head_addons(){
	global $shortname, $default_colorscheme;

	if ( apply_filters( 'et_get_additional_color_scheme', et_get_option( $shortname.'_color_scheme' ) ) <> $default_colorscheme ) { ?>
		<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() . '/style-' . et_get_option( $shortname.'_color_scheme' ) . '.css' ); ?>" type="text/css" media="screen" />
	<?php };

	if ( et_get_option( $shortname.'_child_css' ) == 'on' && et_get_option( $shortname.'_child_cssurl' ) <> '' ) { //Enable child stylesheet  ?>
		<link rel="stylesheet" href="<?php echo esc_url( et_get_option( $shortname.'_child_cssurl' ) ); ?>" type="text/css" media="screen" />
	<?php };

	//prints the theme name, version in meta tag
	$theme_info = wp_get_theme();
	echo '<meta content="' . esc_attr( $theme_info->display( 'Name' ) . ' v.' . $theme_info->display( 'Version' ) ) . '" name="generator"/>';

	if ( et_get_option( $shortname . '_custom_colors' ) == 'on' ) et_epanel_custom_colors_css();
}// end function head_addons()

add_action( 'wp_head', 'head_addons', 7 );

function integration_head(){
	global $shortname;
	if ( et_get_option( $shortname . '_integration_head' ) <> '' && et_get_option( $shortname . '_integrate_header_enable' ) == 'on' ) {
		echo et_get_option( $shortname . '_integration_head' );
	}
}

add_action( 'wp_head', 'integration_head', 12 );

function integration_body(){
	global $shortname;
	if ( et_get_option( $shortname . '_integration_body' ) <> '' && et_get_option( $shortname . '_integrate_body_enable' ) == 'on' ) {
		echo et_get_option( $shortname . '_integration_body' );
	}
}

add_action( 'wp_footer', 'integration_body', 12 );

function integration_single_top(){
	global $shortname;
	if ( et_get_option( $shortname . '_integration_single_top' ) <> '' && et_get_option( $shortname . '_integrate_body_enable' ) == 'on' ) {
		echo et_get_option( $shortname . '_integration_single_top' );
	}
}

add_action( 'et_before_post', 'integration_single_top', 12 );

function integration_single_bottom(){
	global $shortname;
	if ( et_get_option( $shortname . '_integration_single_bottom' ) <> '' && et_get_option( $shortname . '_integrate_body_enable' ) == 'on' ) {
		echo et_get_option( $shortname . '_integration_single_bottom' );
	}
}

add_action( 'et_after_post', 'integration_single_bottom', 12 );

/*this function gets page name by its id*/
if ( ! function_exists( 'get_pagename' ) ) {

	function get_pagename( $page_id )
	{
		$page_object = get_page( $page_id );

		return apply_filters( 'the_title', $page_object->post_title, $page_id );
	}

}

/*this function gets category name by its id*/
if ( ! function_exists( 'get_categname' ) ) {

	function get_categname( $cat_id )
	{
		return get_cat_name( $cat_id );
	}

}

/*this function gets category id by its name*/
if ( ! function_exists( 'get_catId' ) ) {

	function get_catId( $cat_name, $taxonomy = 'category' )
	{
		$cat_name_id = is_numeric( $cat_name ) ? (int) $cat_name : (int) get_cat_ID( html_entity_decode( $cat_name, ENT_QUOTES ) );

		// wpml compatibility
		if ( function_exists( 'icl_object_id' ) ) {
			$cat_name_id = (int) icl_object_id( $cat_name_id, $taxonomy, true );
		}

		return $cat_name_id;
	}

}

/*this function gets page id by its name*/
if ( ! function_exists( 'get_pageId' ) ) {

	function get_pageId( $page_name )
	{
		if ( is_numeric( $page_name ) ) {
			$page_id = intval( $page_name );
		} else {
			$page_name = html_entity_decode( $page_name, ENT_QUOTES );
			$page = get_page_by_title( $page_name );
			$page_id = intval( $page->ID );
		}

		// wpml compatibility
		if ( function_exists( 'icl_object_id' ) )
			$page_id = (int) icl_object_id( $page_id, 'page', true );

		return $page_id;
	}

}

/**
 * Transforms an array of posts, pages, post_tags or categories ids
 * into corresponding "objects" ids, if WPML plugin is installed
 *
 * @param array $ids_array Posts, pages, post_tags or categories ids.
 * @param string $type "Object" type.
 * @return array IDs.
 */
if ( ! function_exists( 'et_generate_wpml_ids' ) ) {

	function et_generate_wpml_ids( $ids_array, $type ) {
		if ( function_exists( 'icl_object_id' ) ) {
			$wpml_ids = array();
			foreach ( $ids_array as $id ) {
				$translated_id = icl_object_id( $id, $type, false );
				if ( ! is_null( $translated_id ) ) $wpml_ids[] = $translated_id;
			}
			$ids_array = $wpml_ids;
		}

		return array_map( 'intval', $ids_array );
	}

}

if ( ! function_exists( 'elegant_is_blog_posts_page' ) ) {

	function elegant_is_blog_posts_page() {
		/**
		 * Returns true if static page is set in WP-Admin / Settings / Reading
		 * and Posts page is displayed
		 */

		static $et_is_blog_posts_cached = null;

		if ( null === $et_is_blog_posts_cached ) {
			$et_is_blog_posts_cached = (bool) is_home() && 0 !== intval( get_option( 'page_for_posts', '0' ) );
		}

		return $et_is_blog_posts_cached;
	}

}

// Added for backwards compatibility
if ( ! function_exists( 'elegant_titles' ) ) {

	function elegant_titles() {
		if ( ! function_exists( 'wp_get_document_title' ) ) {
			wp_title();
		} else {
			echo wp_get_document_title();
		}
	}

}

if ( ! function_exists( '_wp_render_title_tag' ) && ! function_exists( 'et_add_title_tag_back_compat' ) ) {

	/**
	 * Manually add <title> tag in head for WordPress 4.1 below for backward compatibility
	 * Title tag is automatically added for WordPress 4.1 above via theme support
	 * @return void
	 */
	function et_add_title_tag_back_compat() {
		?>
		<title><?php wp_title( '-', true, 'right' ); ?></title>
		<?php
	}

	add_action( 'wp_head', 'et_add_title_tag_back_compat' );
}

/*this function controls the meta titles display*/
if ( ! function_exists( 'elegant_titles_filter' ) ) {

	function elegant_titles_filter( $custom_title ) {
		global $shortname, $themename;
		$custom_title = '';
		$sitename = get_bloginfo( 'name' );
		$site_description = get_bloginfo( 'description' );
		#if the title is being displayed on the homepage
		if ( ( is_home() || is_front_page() ) && ! elegant_is_blog_posts_page() ) {
			if ( 'on' === et_get_option( $shortname . '_seo_home_title' ) ) {
				$custom_title = et_get_option( $shortname . '_seo_home_titletext' );
			} else {
				$seo_home_type = et_get_option( $shortname . '_seo_home_type' );
				$seo_home_separate = et_get_option( $shortname . '_seo_home_separate' );
				if ( $seo_home_type == 'BlogName | Blog description' ) {
					$custom_title = $sitename . esc_html( $seo_home_separate ) . $site_description;
				}
				if ( $seo_home_type == 'Blog description | BlogName') {
					$custom_title = $site_description . esc_html( $seo_home_separate ) . $sitename;
				}
				if ( $seo_home_type == 'BlogName only') {
					$custom_title = $sitename;
				}
			}
		}
		#if the title is being displayed on single posts/pages
		if ( ( ( is_single() || is_page() ) && ! is_front_page() ) || elegant_is_blog_posts_page() ) {
			global $wp_query;
			$postid = elegant_is_blog_posts_page() ? intval( get_option( 'page_for_posts' ) ) : $wp_query->post->ID;
			$key = et_get_option( $shortname . '_seo_single_field_title' );
			$exists3 = get_post_meta( $postid, '' . $key . '', true );
			if ( 'on' === et_get_option( $shortname . '_seo_single_title' ) && '' !== $exists3 ) {
				$custom_title = $exists3;
			} else {
				$seo_single_type = et_get_option( $shortname . '_seo_single_type' );
				$seo_single_separate = et_get_option( $shortname . '_seo_single_separate' );
				$page_title = single_post_title( '', false );
				if ( $seo_single_type == 'BlogName | Post title' ) {
					$custom_title = $sitename . esc_html( $seo_single_separate ) . $page_title;
				}
				if ( $seo_single_type == 'Post title | BlogName' ) {
					$custom_title = $page_title . esc_html( $seo_single_separate ) . $sitename;
				}
				if ( $seo_single_type == 'Post title only' ) {
					$custom_title = $page_title;
				}
			}
		}
		#if the title is being displayed on index pages (categories/archives/search results)
		if ( is_category() || is_archive() || is_search() || is_404() ) {
			$page_title = '';
			$seo_index_type = et_get_option( $shortname . '_seo_index_type' );
			$seo_index_separate = et_get_option( $shortname . '_seo_index_separate' );
			if ( is_category() || is_tag() || is_tax() ) {
				$page_title = single_term_title( '', false );
			} else if ( is_post_type_archive() ) {
				$page_title = post_type_archive_title( '', false );
			} else if ( is_author() ) {
				$page_title = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
			} else if ( is_date() ) {
				$page_title = esc_html__( 'Archives', $themename );
			} else if ( is_search() ) {
				$page_title = sprintf( esc_html__( 'Search results for "%s"', $themename ), esc_attr( get_search_query() ) );
			} else if ( is_404() ) {
				$page_title = esc_html__( '404 Not Found', $themename );
			}
			if ( $seo_index_type == 'BlogName | Category name' ) {
				$custom_title = $sitename . esc_html( $seo_index_separate ) . $page_title;
			}
			if ( $seo_index_type == 'Category name | BlogName') {
				$custom_title = $page_title . esc_html( $seo_index_separate ) . $sitename;
			}
			if ( $seo_index_type == 'Category name only') {
				$custom_title = $page_title;
			}
		}
		$custom_title = wp_strip_all_tags( $custom_title );
		return $custom_title;
	}

}
add_filter( 'pre_get_document_title', 'elegant_titles_filter' );

/*this function controls the meta description display*/
if ( ! function_exists( 'elegant_description' ) ) {

	function elegant_description() {
		// Don't use ePanel SEO if WordPress SEO or All In One SEO Pack plugins are active
		if ( class_exists( 'WPSEO_Frontend' ) || class_exists( 'All_in_One_SEO_Pack' ) ) {
			return;
		}

		global $shortname, $themename;

		#homepage descriptions
		if ( et_get_option( $shortname.'_seo_home_description' ) == 'on' && ( ( is_home() || is_front_page() ) && ! elegant_is_blog_posts_page() ) ) {
			echo '<meta name="description" content="' . esc_attr( et_get_option( $shortname.'_seo_home_descriptiontext' ) ) .'" />';
		}

		#single page descriptions
		if ( et_get_option( $shortname.'_seo_single_description' ) == 'on' && ( is_single() || is_page() || elegant_is_blog_posts_page() ) ) {
			global $wp_query;

			if ( isset( $wp_query->post->ID ) || elegant_is_blog_posts_page() ) {
				$postid = elegant_is_blog_posts_page() ? intval( get_option( 'page_for_posts' ) ) : $wp_query->post->ID;
			}

			$key2 = et_get_option( $shortname.'_seo_single_field_description' );

			if ( isset( $postid ) ) $exists = get_post_meta( $postid, ''.$key2.'', true );

			if ( $exists !== '' ) {
				echo '<meta name="description" content="' . esc_attr( $exists ) . '" />';
			}
		}

		#index descriptions
		$seo_index_description = et_get_option( $shortname.'_seo_index_description' );
		if ( $seo_index_description == 'on' ) {
			$is_pre_4_4 = version_compare( $GLOBALS['wp_version'], '4.4', '<' );
			$description_added = false;

			if ( is_category() ) {
				remove_filter( 'term_description', 'wpautop' );
				$cat = get_query_var( 'cat' );
				$exists2 = category_description( $cat );

				if ( $exists2 !== '' ) {
					echo '<meta name="description" content="' . esc_attr( $exists2 ) . '" />';
					$description_added = true;
				}
			}

			if ( is_archive() && ! $description_added ) {
				$description_text = $is_pre_4_4 ? sprintf( esc_html__( 'Currently viewing archives from %1$s', $themename ),
					wp_title( '', false, '' )
				) : get_the_archive_title();

				printf( '<meta name="description" content="%1$s" />',
					esc_attr( $description_text )
				);

				$description_added = true;
			}

			if ( is_search() && ! $description_added ) {
				$description_text = $is_pre_4_4 ? wp_title( '', false, '' ) : sprintf(
					esc_html__( 'Search Results for: %s', $themename ),
					get_search_query()
				);

				echo '<meta name="description" content="' . esc_attr( $description_text ) . '" />';
				$description_added = true;
			}
		}
	}

}

/*this function controls the meta keywords display*/
if ( ! function_exists( 'elegant_keywords' ) ) {

	function elegant_keywords() {
		// Don't use ePanel SEO if WordPress SEO or All In One SEO Pack plugins are active
		if ( class_exists( 'WPSEO_Frontend' ) || class_exists( 'All_in_One_SEO_Pack' ) ) {
			return;
		}

		global $shortname;

		#homepage keywords
		if ( et_get_option( $shortname.'_seo_home_keywords' ) == 'on' && ( ( is_home() || is_front_page() ) && ! elegant_is_blog_posts_page() ) ) {
			echo '<meta name="keywords" content="' . esc_attr( et_get_option( $shortname.'_seo_home_keywordstext' ) ) . '" />';
		}

		#single page keywords
		if ( et_get_option( $shortname.'_seo_single_keywords' ) == 'on' ) {
			global $wp_query;
			if ( isset( $wp_query->post->ID ) || elegant_is_blog_posts_page() ) {
				$postid = elegant_is_blog_posts_page() ? intval( get_option( 'page_for_posts' ) ) : $wp_query->post->ID;
			}

			$key3 = et_get_option( $shortname.'_seo_single_field_keywords' );

			if (isset( $postid )) $exists4 = get_post_meta( $postid, ''.$key3.'', true );

			if ( isset( $exists4 ) && $exists4 !== '' ) {
				if ( is_single() || is_page() || elegant_is_blog_posts_page() ) echo '<meta name="keywords" content="' . esc_attr( $exists4 ) . '" />';
			}
		}
	}

}

/*this function controls canonical urls*/
if ( ! function_exists( 'elegant_canonical' ) ) {

	function elegant_canonical() {
		// Don't use ePanel SEO if WordPress SEO or All In One SEO Pack plugins are active
		if ( class_exists( 'WPSEO_Frontend' ) || class_exists( 'All_in_One_SEO_Pack' ) ) {
			return;
		}

		global $shortname;

		#homepage urls
		if ( et_get_option( $shortname.'_seo_home_canonical' ) == 'on' && is_home() && ! elegant_is_blog_posts_page() ) {
			echo '<link rel="canonical" href="'. esc_url( home_url() ).'" />';
		}

		#single page urls
		if ( et_get_option( $shortname.'_seo_single_canonical' ) == 'on' ) {
			global $wp_query;
			if ( isset( $wp_query->post->ID ) || elegant_is_blog_posts_page() ) {
				$postid = elegant_is_blog_posts_page() ? intval( get_option( 'page_for_posts' ) ) : $wp_query->post->ID;
			}

			if ( ( is_single() || is_page() || elegant_is_blog_posts_page() ) && ! is_front_page() ) {
				echo '<link rel="canonical" href="' . esc_url( get_permalink( $postid ) ) . '" />';
			}
		}

		#index page urls
		if ( et_get_option( $shortname.'_seo_index_canonical' ) == 'on' ) {
			$current_page_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			if ( is_archive() || is_category() || is_search() ) echo '<link rel="canonical" href="'. esc_url( $current_page_url ).'" />';
		}
	}

}

add_action( 'wp_head', 'add_favicon' );

function add_favicon(){
	global $shortname;

	$faviconUrl = et_get_option( $shortname.'_favicon' );
	if ( $faviconUrl <> '' ) {
		echo '<link rel="shortcut icon" href="'.esc_url( $faviconUrl ).'" />';
	}
}

add_action( 'init', 'et_create_images_temp_folder' );

function et_create_images_temp_folder(){
	#clean et_temp folder once per week
	if ( false !== $last_time = get_option( 'et_schedule_clean_images_last_time' ) ) {
		$timeout = 86400 * 7;
		if ( ( $timeout < ( time() - $last_time ) ) && '' != get_option( 'et_images_temp_folder' ) ) et_clean_temp_images( get_option( 'et_images_temp_folder' ) );
	}

	if ( false !== get_option( 'et_images_temp_folder' ) ) return;

	$uploads_dir = wp_upload_dir();
	$destination_dir = ( false === $uploads_dir['error'] ) ? path_join( $uploads_dir['basedir'], 'et_temp' ) : null;

	if ( ! wp_mkdir_p( $destination_dir ) ) update_option( 'et_images_temp_folder', '' );
	else {
		update_option( 'et_images_temp_folder', preg_replace( '#\/\/#', '/', $destination_dir ) );
		update_option( 'et_schedule_clean_images_last_time', time() );
	}
}

if ( ! function_exists( 'et_clean_temp_images' ) ) {

	function et_clean_temp_images( $directory ){
		$dir_to_clean = @ opendir( $directory );

		if ( $dir_to_clean ) {
			while (($file = readdir( $dir_to_clean ) ) !== false ) {
				if ( substr( $file, 0, 1 ) == '.' )
					continue;
				if ( is_dir( $directory.'/'.$file ) )
					et_clean_temp_images( path_join( $directory, $file ) );
				else
					@ unlink( path_join( $directory, $file ) );
			}
			closedir( $dir_to_clean );
		}

		#set last time cleaning was performed
		update_option( 'et_schedule_clean_images_last_time', time() );
	}

}

add_filter( 'update_option_upload_path', 'et_update_uploads_dir' );

function et_update_uploads_dir( $upload_path ){
	#check if we have 'et_temp' folder within $uploads_dir['basedir'] directory, if not - try creating it, if it's not possible $destination_dir = null

	$destination_dir = '';
	$uploads_dir = wp_upload_dir();
	$et_temp_dir = path_join( $uploads_dir['basedir'], 'et_temp' );

	if ( is_dir( $et_temp_dir ) || ( false === $uploads_dir['error'] && wp_mkdir_p( $et_temp_dir ) ) ) {
		$destination_dir = $et_temp_dir;
		update_option( 'et_schedule_clean_images_last_time', time() );
	}

	update_option( 'et_images_temp_folder', preg_replace( '#\/\/#', '/', $destination_dir ) );

	return $upload_path;
}

if ( ! function_exists( 'et_resize_image' ) ) {

	function et_resize_image( $thumb, $new_width, $new_height, $crop ){
		/*
		 * Fixes the issue with x symbol between width and height values in the filename.
		 * For instance, sports-400x400.jpg file results in 'image not found' in getimagesize() function.
		 */
		$thumb = str_replace( '%26%23215%3B', 'x', rawurlencode( $thumb ) );
		$thumb = rawurldecode( $thumb );

		if ( is_ssl() ) $thumb = preg_replace( '#^http://#', 'https://', $thumb );
		$info = pathinfo( $thumb );
		$ext = $info['extension'];
		$name = wp_basename( $thumb, ".$ext" );
		$is_jpeg = false;
		$site_uri = apply_filters( 'et_resize_image_site_uri', site_url() );
		$site_dir = apply_filters( 'et_resize_image_site_dir', ABSPATH );

		// If multisite, not the main site, WordPress version < 3.5 or ms-files rewriting is enabled ( not the fresh WordPress installation, updated from the 3.4 version )
		if ( is_multisite() && ! is_main_site() && ( ! function_exists( 'wp_get_mime_types' ) || get_site_option( 'ms_files_rewriting' ) ) ) {
			//Get main site url on multisite installation

			switch_to_blog( 1 );
			$site_uri = site_url();
			restore_current_blog();
		}

		/*
		 * If we're dealing with an external image ( might be the result of Grab the first image function ),
		 * return original image url
		 */
		if ( false === strpos( $thumb, $site_uri ) )
			return $thumb;

		if ( 'jpeg' == $ext ) {
			$ext = 'jpg';
			$name = preg_replace( '#.jpeg$#', '', $name );
			$is_jpeg = true;
		}

		$suffix = "{$new_width}x{$new_height}";

		$destination_dir = '' != get_option( 'et_images_temp_folder' ) ? preg_replace( '#\/\/#', '/', get_option( 'et_images_temp_folder' ) ) : null;

		$matches = apply_filters( 'et_resize_image_site_dir', array(), $site_dir );
		if ( !empty( $matches ) ) {
			preg_match( '#'.$matches[1].'$#', $site_uri, $site_uri_matches );
			if ( !empty( $site_uri_matches ) ) {
				$site_uri = str_replace( $matches[1], '', $site_uri );
				$site_uri = preg_replace( '#/$#', '', $site_uri );
				$site_dir = str_replace( $matches[1], '', $site_dir );
				$site_dir = preg_replace( '#\\\/$#', '', $site_dir );
			}
		}

		#get local name for use in file_exists() and get_imagesize() functions
		$localfile = str_replace( apply_filters( 'et_resize_image_localfile', $site_uri, $site_dir, et_multisite_thumbnail( $thumb ) ), $site_dir, et_multisite_thumbnail( $thumb ) );

		$add_to_suffix = '';
		if ( file_exists( $localfile ) ) $add_to_suffix = filesize( $localfile ) . '_';

		#prepend image filesize to be able to use images with the same filename
		$suffix = $add_to_suffix . $suffix;
		$destfilename_attributes = '-' . $suffix . '.' . strtolower( $ext );

		$checkfilename = ( '' != $destination_dir && null !== $destination_dir ) ? path_join( $destination_dir, $name ) : path_join( dirname( $localfile ), $name );
		$checkfilename .= $destfilename_attributes;

		if ( $is_jpeg ) $checkfilename = preg_replace( '#.jpg$#', '.jpeg', $checkfilename );

		$uploads_dir = wp_upload_dir();
		$uploads_dir['basedir'] = preg_replace( '#\/\/#', '/', $uploads_dir['basedir'] );

		if ( null !== $destination_dir && '' != $destination_dir && apply_filters( 'et_enable_uploads_detection', true ) ) {
			$site_dir = trailingslashit( preg_replace( '#\/\/#', '/', $uploads_dir['basedir'] ) );
			$site_uri = trailingslashit( $uploads_dir['baseurl'] );
		}

		#check if we have an image with specified width and height

		if ( file_exists( $checkfilename ) ) return str_replace( $site_dir, trailingslashit( $site_uri ), $checkfilename );

		$size = @getimagesize( $localfile );
		if ( !$size ) return new WP_Error( 'invalid_image_path', esc_html__( 'Image doesn\'t exist' ), $thumb );
		list($orig_width, $orig_height, $orig_type) = $size;

		#check if we're resizing the image to smaller dimensions
		if ( $orig_width > $new_width || $orig_height > $new_height ) {
			if ( $orig_width < $new_width || $orig_height < $new_height ) {
				#don't resize image if new dimensions > than its original ones
				if ( $orig_width < $new_width ) $new_width = $orig_width;
				if ( $orig_height < $new_height ) $new_height = $orig_height;

				#regenerate suffix and appended attributes in case we changed new width or new height dimensions
				$suffix = "{$add_to_suffix}{$new_width}x{$new_height}";
				$destfilename_attributes = '-' . $suffix . '.' . $ext;

				$checkfilename = ( '' != $destination_dir && null !== $destination_dir ) ? path_join( $destination_dir, $name ) : path_join( dirname( $localfile ), $name );
				$checkfilename .= $destfilename_attributes;

				#check if we have an image with new calculated width and height parameters
				if ( file_exists( $checkfilename ) ) return str_replace( $site_dir, trailingslashit( $site_uri ), $checkfilename );
			}

			#we didn't find the image in cache, resizing is done here
			$et_image_editor = wp_get_image_editor( $localfile );

			if ( ! is_wp_error( $et_image_editor ) ) {
				$et_image_editor->resize( $new_width, $new_height, $crop );

				// generate correct file name/path
				$et_new_image_name = $et_image_editor->generate_filename( $suffix, $destination_dir );

				do_action( 'et_resize_image_before_save', $et_image_editor, $et_new_image_name );

				$et_image_editor->save( $et_new_image_name );

				// assign new image path
				$result = $et_new_image_name;
			} else {
				// assign a WP_ERROR ( WP_Image_Editor instance wasn't created properly )
				$result = $et_image_editor;
			}

			if ( ! is_wp_error( $result ) ) {
				// transform local image path into URI

				if ( $is_jpeg ) $thumb = preg_replace( '#.jpeg$#', '.jpg', $thumb );

				$site_dir = str_replace( '\\', '/', $site_dir );
				$result = str_replace( '\\', '/', $result );
				$result = str_replace( '//', '/', $result );
				$result = str_replace( $site_dir, trailingslashit( $site_uri ), $result );
			}

			#returns resized image path or WP_Error ( if something went wrong during resizing )
			return $result;
		}

		#returns unmodified image, for example in case if the user is trying to resize 800x600px to 1920x1080px image
		return $thumb;
	}

}

add_action( 'pre_get_posts', 'et_custom_posts_per_page' );

function et_custom_posts_per_page( $query = false ) {
	global $shortname;

	if ( is_admin() ) {
		return;
	}

	if ( ! is_a( $query, 'WP_Query' ) || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_category ) {
		$query->set( 'posts_per_page', (int) et_get_option( $shortname . '_catnum_posts', '5' ) );
	} elseif ( $query->is_tag ) {
		$query->set( 'posts_per_page', (int) et_get_option( $shortname . '_tagnum_posts', '5' ) );
	} elseif ( $query->is_search ) {
		if ( isset( $_GET['et_searchform_submit'] ) ) {
			$postTypes = array();
			if ( !isset( $_GET['et-inc-posts'] ) && !isset( $_GET['et-inc-pages'] ) ) $postTypes = array('post');
			if ( isset( $_GET['et-inc-pages'] ) ) $postTypes = array('page');
			if ( isset( $_GET['et-inc-posts'] ) ) $postTypes[] = 'post';
			$query->set( 'post_type', $postTypes );

			if ( isset( $_GET['et-month-choice'] ) && $_GET['et-month-choice'] != 'no-choice' ) {
				$et_year = substr( $_GET['et-month-choice'], 0, 4 );
				$et_month = substr( $_GET['et-month-choice'], 4, strlen( $_GET['et-month-choice'] ) - 4 );

				$query->set( 'year', absint( $et_year ) );
				$query->set( 'monthnum', absint( $et_month ) );
			}

			if ( isset( $_GET['et-cat'] ) && $_GET['et-cat'] != 0 )
				$query->set( 'cat', absint( $_GET['et-cat'] ) );
		}
		$query->set( 'posts_per_page', (int) et_get_option( $shortname . '_searchnum_posts', '5' ) );
	} elseif ( $query->is_archive ) {
		$posts_number = (int) et_get_option( $shortname . '_archivenum_posts', '5' );

		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$posts_number = (int) et_get_option( $shortname . '_woocommerce_archive_num_posts', '9' );
		}

		$query->set( 'posts_per_page', $posts_number );
	}
}

add_filter( 'default_hidden_meta_boxes', 'et_show_hidden_metaboxes', 10, 2 );

function et_show_hidden_metaboxes( $hidden, $screen ){
	# make custom fields and excerpt meta boxes show by default
	if ( 'post' == $screen->base || 'page' == $screen->base )
		$hidden = array(
			'slugdiv',
			'trackbacksdiv',
			'commentstatusdiv',
			'commentsdiv',
			'authordiv',
			'revisionsdiv',
		);

	return $hidden;
}

add_filter( 'widget_title', 'et_widget_force_title' );

function et_widget_force_title( $title ){
	#add an empty title for widgets ( otherwise it might break the sidebar layout )
	if ( $title == '' ) $title = ' ';

	return $title;
}

//modify the comment counts to only reflect the number of comments minus pings
if( version_compare( phpversion(), '4.4', '>=' ) ) add_filter( 'get_comments_number', 'et_comment_count', 0, 2 );

function et_comment_count( $count, $post_id ) {
	$is_doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX ? true : false;

	if ( ! is_admin() || $is_doing_ajax ) {
		global $id;
		$post_id = $post_id ? $post_id : $id;
		$get_comments = get_comments( array('post_id' => $post_id, 'status' => 'approve') );
		$comments_by_type = separate_comments( $get_comments );
		return count( $comments_by_type['comment'] );
	} else {
		return $count;
	}
}

add_action( 'admin_init', 'et_theme_check_clean_installation' );

function et_theme_check_clean_installation(){
	add_action( 'admin_notices', 'et_theme_epanel_reminder' );
}

if ( ! function_exists( 'et_theme_epanel_reminder' ) ) {

	function et_theme_epanel_reminder(){
		global $shortname, $themename, $current_screen;

		if ( false === et_get_option( $shortname . '_logo' ) && 'appearance_page_core_functions' != $current_screen->id ) {
			printf( et_get_safe_localization( __( '<div class="updated"><p>This is a fresh installation of %1$s theme. Don\'t forget to go to <a href="%2$s">ePanel</a> to set it up. This message will disappear once you have clicked the Save button within the <a href="%2$s">theme\'s options page</a>.</p></div>', $themename ) ), wp_get_theme(), admin_url( 'themes.php?page=core_functions.php' ) );
		}
	}

}

add_filter( 'body_class', 'et_add_fullwidth_body_class' );

function et_add_fullwidth_body_class( $classes ){
	$fullwidth_view = false;

	if ( is_page_template( 'page-full.php' ) ) $fullwidth_view = true;

	if ( is_page() || is_single() ) {
		$et_ptemplate_settings = get_post_meta( get_queried_object_id(), 'et_ptemplate_settings', true );
		$fullwidth = isset( $et_ptemplate_settings['et_fullwidthpage'] ) ? (bool) $et_ptemplate_settings['et_fullwidthpage'] : false;

		if ( $fullwidth ) $fullwidth_view = true;
	}

	if ( is_single() && 'on' == get_post_meta( get_queried_object_id(), '_et_full_post', true ) ) $fullwidth_view = true;

	$classes[] = apply_filters( 'et_fullwidth_view_body_class', $fullwidth_view ) ? 'et_fullwidth_view' : 'et_includes_sidebar';

	return $classes;
}

function et_add_responsive_shortcodes_css(){
	global $shortname;

	if ( 'on' == et_get_option( $shortname . '_responsive_shortcodes', 'on' ) )
		wp_enqueue_style( 'et-shortcodes-responsive-css', ET_SHORTCODES_DIR . '/css/shortcodes_responsive.css', false, ET_SHORTCODES_VERSION, 'all' );
}

/**
 * Loads theme settings
 *
 */
if ( ! function_exists( 'et_load_core_options' ) ) {

	function et_load_core_options() {
		global $shortname;
		require_once get_template_directory() . esc_attr( "/options_{$shortname}.php" );
	}

}

/**
 * Adds custom css option content to <head>
 *
 */
function et_add_custom_css() {
	global $shortname;

	$custom_css = et_get_option( "{$shortname}_custom_css" );

	if ( false === $custom_css || '' == $custom_css ) return;

	/**
	 * The theme doesn't strip slashes from custom css, when saving to the database,
	 * so it does that before outputting the code on front-end
	 */
	echo '<style type="text/css" id="et-custom-css">' . "\n" . stripslashes( $custom_css ) . "\n" . '</style>';
}

add_action( 'wp_head', 'et_add_custom_css', 100 );

if ( ! function_exists( 'et_get_google_fonts' ) ) :

	/**
 * Returns the list of popular google fonts
 *
 */
	function et_get_google_fonts() {
		$google_fonts = array(
			'Open Sans'             => array(
				'styles' 		=> '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
				'character_set' => 'latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic',
				'type'			=> 'sans-serif',
			),
			'Oswald'                => array(
				'styles' 		=> '400,300,700',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'sans-serif',
			),
			'Droid Sans'            => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin',
				'type'			=> 'sans-serif',
			),
			'Lato'                  => array(
				'styles' 		=> '400,100,100italic,300,300italic,400italic,700,700italic,900,900italic',
				'character_set' => 'latin',
				'type'			=> 'sans-serif',
			),
			'Open Sans Condensed'   => array(
				'styles' 		=> '300,300italic,700',
				'character_set' => 'latin,cyrillic-ext,latin-ext,greek-ext,greek,vietnamese,cyrillic',
				'type'			=> 'sans-serif',
			),
			'PT Sans'               => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin,latin-ext,cyrillic',
				'type'			=> 'sans-serif',
			),
			'Ubuntu'                => array(
				'styles' 		=> '400,300,300italic,400italic,500,500italic,700,700italic',
				'character_set' => 'latin,cyrillic-ext,cyrillic,greek-ext,greek,latin-ext',
				'type'			=> 'sans-serif',
			),
			'PT Sans Narrow'        => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin,latin-ext,cyrillic',
				'type'			=> 'sans-serif',
			),
			'Yanone Kaffeesatz'     => array(
				'styles' 		=> '400,200,300,700',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'sans-serif',
			),
			'Roboto Condensed'      => array(
				'styles' 		=> '400,300,300italic,400italic,700,700italic',
				'character_set' => 'latin,cyrillic-ext,latin-ext,greek-ext,cyrillic,greek,vietnamese',
				'type'			=> 'sans-serif',
			),
			'Source Sans Pro'       => array(
				'styles' 		=> '400,200,200italic,300,300italic,400italic,600,600italic,700,700italic,900,900italic',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'sans-serif',
			),
			'Nunito'                => array(
				'styles' 		=> '400,300,700',
				'character_set' => 'latin',
				'type'			=> 'sans-serif',
			),
			'Francois One'          => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'sans-serif',
			),
			'Roboto'                => array(
				'styles' 		=> '400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic',
				'character_set' => 'latin,cyrillic-ext,latin-ext,cyrillic,greek-ext,greek,vietnamese',
				'type'			=> 'sans-serif',
			),
			'Raleway'               => array(
				'styles' 		=> '400,100,200,300,600,500,700,800,900',
				'character_set' => 'latin',
				'type'			=> 'sans-serif',
			),
			'Arimo'                 => array(
				'styles' 		=> '400,400italic,700italic,700',
				'character_set' => 'latin,cyrillic-ext,latin-ext,greek-ext,cyrillic,greek,vietnamese',
				'type'			=> 'sans-serif',
			),
			'Cuprum'                => array(
				'styles' 		=> '400,400italic,700italic,700',
				'character_set' => 'latin,latin-ext,cyrillic',
				'type'			=> 'sans-serif',
			),
			'Play'                  => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin,cyrillic-ext,cyrillic,greek-ext,greek,latin-ext',
				'type'			=> 'sans-serif',
			),
			'Dosis'                 => array(
				'styles' 		=> '400,200,300,500,600,700,800',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'sans-serif',
			),
			'Abel'                  => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'sans-serif',
			),
			'Droid Serif'           => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Arvo'                  => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Lora'                  => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Rokkitt'               => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'PT Serif'              => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin,cyrillic',
				'type'			=> 'serif',
			),
			'Bitter'                => array(
				'styles' 		=> '400,400italic,700',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'serif',
			),
			'Merriweather'          => array(
				'styles' 		=> '400,300,900,700',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Vollkorn'              => array(
				'styles' 		=> '400,400italic,700italic,700',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Cantata One'           => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'serif',
			),
			'Kreon'                 => array(
				'styles' 		=> '400,300,700',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Josefin Slab'          => array(
				'styles' 		=> '400,100,100italic,300,300italic,400italic,600,700,700italic,600italic',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Playfair Display'      => array(
				'styles' 		=> '400,400italic,700,700italic,900italic,900',
				'character_set' => 'latin,latin-ext,cyrillic',
				'type'			=> 'serif',
			),
			'Bree Serif'            => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'serif',
			),
			'Crimson Text'          => array(
				'styles' 		=> '400,400italic,600,600italic,700,700italic',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Old Standard TT'       => array(
				'styles' 		=> '400,400italic,700',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Sanchez'               => array(
				'styles' 		=> '400,400italic',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'serif',
			),
			'Crete Round'           => array(
				'styles' 		=> '400,400italic',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'serif',
			),
			'Cardo'                 => array(
				'styles' 		=> '400,400italic,700',
				'character_set' => 'latin,greek-ext,greek,latin-ext',
				'type'			=> 'serif',
			),
			'Noticia Text'          => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin,vietnamese,latin-ext',
				'type'			=> 'serif',
			),
			'Judson'                => array(
				'styles' 		=> '400,400italic,700',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Lobster'               => array(
				'styles' 		=> '400',
				'character_set' => 'latin,cyrillic-ext,latin-ext,cyrillic',
				'type'			=> 'cursive',
			),
			'Unkempt'               => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Changa One'            => array(
				'styles' 		=> '400,400italic',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Special Elite'         => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Chewy'                 => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Comfortaa'             => array(
				'styles' 		=> '400,300,700',
				'character_set' => 'latin,cyrillic-ext,greek,latin-ext,cyrillic',
				'type'			=> 'cursive',
			),
			'Boogaloo'              => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Fredoka One'           => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Luckiest Guy'          => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Cherry Cream Soda'     => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Lobster Two'           => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Righteous'             => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'cursive',
			),
			'Squada One'            => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Black Ops One'         => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'cursive',
			),
			'Happy Monkey'          => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'cursive',
			),
			'Passion One'           => array(
				'styles' 		=> '400,700,900',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'cursive',
			),
			'Nova Square'           => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Metamorphous'          => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'cursive',
			),
			'Poiret One'            => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext,cyrillic',
				'type'			=> 'cursive',
			),
			'Bevan'                 => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Shadows Into Light'    => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'The Girl Next Door'    => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Coming Soon'           => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Dancing Script'        => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Pacifico'              => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Crafty Girls'          => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Calligraffitti'        => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Rock Salt'             => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Amatic SC'             => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Leckerli One'          => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Tangerine'             => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Reenie Beanie'         => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Satisfy'               => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Gloria Hallelujah'     => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Permanent Marker'      => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Covered By Your Grace' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Walter Turncoat'       => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Patrick Hand'          => array(
				'styles' 		=> '400',
				'character_set' => 'latin,vietnamese,latin-ext',
				'type'			=> 'cursive',
			),
			'Schoolbell'            => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Indie Flower'          => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
		);

		return apply_filters( 'et_google_fonts', $google_fonts );
	}

endif;

if ( ! function_exists( 'et_get_websafe_font_stack' ) ) :

	/**
	 * Determines a websafe font stack, using font type
	 *
	 */
	function et_get_websafe_font_stack( $type = 'sans-serif' ) {
		$font_stack = '';

		switch ( $type ) {
			case 'sans-serif':
				$font_stack = 'Helvetica, Arial, Lucida, sans-serif';
				break;
			case 'serif':
				$font_stack = 'Georgia, "Times New Roman", serif';
				break;
			case 'cursive':
				$font_stack = 'cursive';
				break;
		}

		return $font_stack;
	}

endif;

if ( ! function_exists( 'et_gf_attach_font' ) ) :

	/**
	 * Attaches Google Font to given css elements
	 *
	 */
	function et_gf_attach_font( $et_gf_font_name, $elements ) {
		$google_fonts = et_get_google_fonts();

		printf( '%s { font-family: \'%s\', %s; }',
			esc_html( $elements ),
			esc_html( $et_gf_font_name ),
			et_get_websafe_font_stack( $google_fonts[$et_gf_font_name]['type'] )
		);
	}

endif;

if ( ! function_exists( 'et_gf_enqueue_fonts' ) ) :

	/**
	 * Enqueues Google Fonts
	 *
	 */
	function et_gf_enqueue_fonts( $et_gf_font_names ) {
		global $shortname;

		if ( ! is_array( $et_gf_font_names ) || empty( $et_gf_font_names ) ) return;

		$google_fonts = et_get_google_fonts();
		$protocol = is_ssl() ? 'https' : 'http';

		foreach ( $et_gf_font_names as $et_gf_font_name ) {
			$google_font_character_set = $google_fonts[$et_gf_font_name]['character_set'];

			// By default, only latin and latin-ext subsets are loaded, all available subsets can be enabled in ePanel
			if ( 'false' == et_get_option( "{$shortname}_gf_enable_all_character_sets", 'false' ) ) {
				$latin_ext = '';
				if ( false !== strpos( $google_fonts[$et_gf_font_name]['character_set'], 'latin-ext' ) )
				$latin_ext = ',latin-ext';

				$google_font_character_set = "latin{$latin_ext}";
			}

			$query_args = array(
				'family' => sprintf( '%s:%s',
					str_replace( ' ', '+', $et_gf_font_name ),
					apply_filters( 'et_gf_set_styles', $google_fonts[$et_gf_font_name]['styles'], $et_gf_font_name )
				),
				'subset' => apply_filters( 'et_gf_set_character_set', $google_font_character_set, $et_gf_font_name ),
			);

			$et_gf_font_name_slug = strtolower( str_replace( ' ', '-', $et_gf_font_name ) );
			wp_enqueue_style( 'et-gf-' . $et_gf_font_name_slug, esc_url( add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ) ), array(), null );
		}
	}

endif;
