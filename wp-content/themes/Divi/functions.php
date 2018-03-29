<?php

if ( ! isset( $content_width ) ) $content_width = 1080;

function et_setup_theme() {
	global $themename, $shortname, $et_store_options_in_one_row, $default_colorscheme;
	$themename = 'Divi';
	$shortname = 'divi';
	$et_store_options_in_one_row = true;

	$default_colorscheme = "Default";

	$template_directory = get_template_directory();

	require_once( $template_directory . '/core/init.php' );

	et_core_setup( get_template_directory_uri() );

	require_once( $template_directory . '/epanel/custom_functions.php' );

	require_once( $template_directory . '/includes/functions/choices.php' );

	require_once( $template_directory . '/includes/functions/sanitization.php' );

	require_once( $template_directory . '/includes/functions/sidebars.php' );

	load_theme_textdomain( 'Divi', $template_directory . '/lang' );

	require_once( $template_directory . '/epanel/core_functions.php' );

	require_once( $template_directory . '/post_thumbnails_divi.php' );

	include( $template_directory . '/includes/widgets.php' );

	register_nav_menus( array(
		'primary-menu'   => esc_html__( 'Primary Menu', 'Divi' ),
		'secondary-menu' => esc_html__( 'Secondary Menu', 'Divi' ),
		'footer-menu'    => esc_html__( 'Footer Menu', 'Divi' ),
	) );

	// don't display the empty title bar if the widget title is not set
	remove_filter( 'widget_title', 'et_widget_force_title' );

	remove_filter( 'body_class', 'et_add_fullwidth_body_class' );

	add_action( 'wp_enqueue_scripts', 'et_add_responsive_shortcodes_css', 11 );

	// Declare theme supports
	add_theme_support( 'title-tag' );

	add_theme_support( 'post-formats', array(
		'video', 'audio', 'quote', 'gallery', 'link'
	) );

	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_theme_support( 'customize-selective-refresh-widgets' );

	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	add_action( 'woocommerce_before_main_content', 'et_divi_output_content_wrapper', 10 );

	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	add_action( 'woocommerce_after_main_content', 'et_divi_output_content_wrapper_end', 10 );

	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

	// deactivate page templates and custom import functions
	remove_action( 'init', 'et_activate_features' );

	remove_action('admin_menu', 'et_add_epanel');

	// Load editor styling
	add_editor_style( 'css/editor-style.css' );
}
add_action( 'after_setup_theme', 'et_setup_theme' );

function et_theme_epanel_reminder(){
	global $shortname, $themename;

	$documentation_url         = 'http://www.elegantthemes.com/gallery/divi/readme.html';
	$documentation_option_name = $shortname . '_2_4_documentation_message';

	if ( false === et_get_option( $shortname . '_logo' ) && false === et_get_option( $documentation_option_name ) ) {
		$message = sprintf(
			et_get_safe_localization( __( 'Welcome to Divi! Before diving in to your new theme, please visit the <a style="color: #fff; font-weight: bold;" href="%1$s" target="_blank">Divi Documentation</a> page for access to dozens of in-depth tutorials.', $themename ) ),
			esc_url( $documentation_url )
		);

		printf(
			'<div class="notice is-dismissible" style="background-color: #6C2EB9; color: #fff; border-left: none;">
				<p>%1$s</p>
			</div>',
			$message
		);

		et_update_option( $documentation_option_name, 'triggered' );
	}
}
add_action( 'admin_init', 'et_theme_epanel_reminder' );

if ( ! function_exists( 'et_divi_fonts_url' ) ) :
function et_divi_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Open Sans, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$open_sans = _x( 'on', 'Open Sans font: on or off', 'Divi' );

	if ( 'off' !== $open_sans ) {
		$font_families = array();

		if ( 'off' !== $open_sans )
			$font_families[] = 'Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => implode( '%7C', $font_families ),
			'subset' => 'latin,latin-ext',
		);
		$fonts_url = add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" );
	}

	return $fonts_url;
}
endif;

function et_divi_load_fonts() {
	$fonts_url = et_divi_fonts_url();
	if ( ! empty( $fonts_url ) )
		wp_enqueue_style( 'divi-fonts', esc_url_raw( $fonts_url ), array(), null );
}
add_action( 'wp_enqueue_scripts', 'et_divi_load_fonts' );

function et_add_home_link( $args ) {
	// add Home link to the custom menu WP-Admin page
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'et_add_home_link' );

function et_divi_load_scripts_styles(){
	global $wp_styles;

	$template_dir = get_template_directory_uri();

	$theme_version = et_get_theme_version();

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	$dependencies_array = array( 'jquery', 'et-jquery-touch-mobile' );

	// load 'jquery-effects-core' if SlideIn/Fullscreen header used or if customizer opened
	if ( is_customize_preview() || 'slide' === et_get_option( 'header_style', 'left' ) || 'fullscreen' === et_get_option( 'header_style', 'left' ) ) {
		$dependencies_array[] = 'jquery-effects-core';
	}

	wp_enqueue_script( 'et-jquery-touch-mobile', $template_dir . '/includes/builder/scripts/jquery.mobile.custom.min.js', array( 'jquery' ), $theme_version, true );
	wp_enqueue_script( 'divi-custom-script', $template_dir . '/js/custom.js', $dependencies_array , $theme_version, true );

	if ( 'on' === et_get_option( 'divi_smooth_scroll', false ) ) {
		wp_enqueue_script( 'smooth-scroll', $template_dir . '/js/smoothscroll.js', array( 'jquery' ), $theme_version, true );
	}

	$et_gf_enqueue_fonts = array();
	$et_gf_heading_font = sanitize_text_field( et_get_option( 'heading_font', 'none' ) );
	$et_gf_body_font = sanitize_text_field( et_get_option( 'body_font', 'none' ) );
	$et_gf_button_font = sanitize_text_field( et_get_option( 'all_buttons_font', 'none' ) );
	$et_gf_primary_nav_font = sanitize_text_field( et_get_option( 'primary_nav_font', 'none' ) );
	$et_gf_secondary_nav_font = sanitize_text_field( et_get_option( 'secondary_nav_font', 'none' ) );
	$et_gf_slide_nav_font = sanitize_text_field( et_get_option( 'slide_nav_font', 'none' ) );

	$site_domain = get_locale();
	$et_one_font_languages = et_get_one_font_languages();

	if ( 'none' != $et_gf_heading_font ) $et_gf_enqueue_fonts[] = $et_gf_heading_font;
	if ( 'none' != $et_gf_body_font ) $et_gf_enqueue_fonts[] = $et_gf_body_font;
	if ( 'none' != $et_gf_button_font ) $et_gf_enqueue_fonts[] = $et_gf_button_font;
	if ( 'none' != $et_gf_primary_nav_font ) $et_gf_enqueue_fonts[] = $et_gf_primary_nav_font;
	if ( 'none' != $et_gf_secondary_nav_font ) $et_gf_enqueue_fonts[] = $et_gf_secondary_nav_font;
	if ( 'none' != $et_gf_slide_nav_font ) $et_gf_enqueue_fonts[] = $et_gf_slide_nav_font;

	if ( isset( $et_one_font_languages[$site_domain] ) ) {
		$et_gf_font_name_slug = strtolower( str_replace( ' ', '-', $et_one_font_languages[$site_domain]['language_name'] ) );
		wp_enqueue_style( 'et-gf-' . $et_gf_font_name_slug, $et_one_font_languages[$site_domain]['google_font_url'], array(), null );
	} else if ( ! empty( $et_gf_enqueue_fonts ) ) {
		foreach ( $et_gf_enqueue_fonts as $single_font ) {
			et_builder_enqueue_font( $single_font );
		}
	}

	/*
	 * Loads the main stylesheet.
	 */
	wp_enqueue_style( 'divi-style', get_stylesheet_uri(), array(), $theme_version );
}
add_action( 'wp_enqueue_scripts', 'et_divi_load_scripts_styles' );

function et_add_mobile_navigation(){
	if ( is_customize_preview() || ( 'slide' !== et_get_option( 'header_style', 'left' ) && 'fullscreen' !== et_get_option( 'header_style', 'left' ) ) ) {
		printf(
			'<div id="et_mobile_nav_menu">
				<div class="mobile_nav closed">
					<span class="select_page">%1$s</span>
					<span class="mobile_menu_bar mobile_menu_bar_toggle"></span>
				</div>
			</div>',
			esc_html__( 'Select Page', 'Divi' )
		);
	}
}
add_action( 'et_header_top', 'et_add_mobile_navigation' );

function et_add_viewport_meta(){
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />';
}
add_action( 'wp_head', 'et_add_viewport_meta' );

function et_maybe_add_scroll_to_anchor_fix() {
	$add_scroll_to_anchor_fix = et_get_option( 'divi_scroll_to_anchor_fix' );

	if ( 'on' === $add_scroll_to_anchor_fix ) {
		echo '<script>
				document.addEventListener( "DOMContentLoaded", function( event ) {
					window.et_location_hash = window.location.hash;
					if ( "" !== window.et_location_hash ) {
						// Prevent jump to anchor - Firefox
						window.scrollTo( 0, 0 );
						var et_anchor_element = document.getElementById( window.et_location_hash.substring( 1 ) );
						window.et_location_hash_style = et_anchor_element.style.display;
						// Prevent jump to anchor - Other Browsers
						et_anchor_element.style.display = "none";
					}
				} );
		</script>';
	}
}
add_action( 'wp_head', 'et_maybe_add_scroll_to_anchor_fix', 9 );

function et_remove_additional_stylesheet( $stylesheet ){
	global $default_colorscheme;
	return $default_colorscheme;
}
add_filter( 'et_get_additional_color_scheme', 'et_remove_additional_stylesheet' );

if ( ! function_exists( 'et_list_pings' ) ) :
function et_list_pings($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?> - <?php comment_excerpt(); ?>
<?php }
endif;

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

function et_add_post_meta_box() {
	// Add Page settings meta box only if it's not disabled for current user
	if ( et_pb_is_allowed( 'page_options' ) ) {
		add_meta_box( 'et_settings_meta_box', esc_html__( 'Divi Page Settings', 'Divi' ), 'et_single_settings_meta_box', 'page', 'side', 'high' );
	}
	add_meta_box( 'et_settings_meta_box', esc_html__( 'Divi Post Settings', 'Divi' ), 'et_single_settings_meta_box', 'post', 'side', 'high' );
	add_meta_box( 'et_settings_meta_box', esc_html__( 'Divi Product Settings', 'Divi' ), 'et_single_settings_meta_box', 'product', 'side', 'high' );
	add_meta_box( 'et_settings_meta_box', esc_html__( 'Divi Project Settings', 'Divi' ), 'et_single_settings_meta_box', 'project', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'et_add_post_meta_box' );

if ( ! function_exists( 'et_pb_portfolio_meta_box' ) ) :
function et_pb_portfolio_meta_box() { ?>
	<div class="et_project_meta">
		<strong class="et_project_meta_title"><?php echo esc_html__( 'Skills', 'Divi' ); ?></strong>
		<p><?php echo get_the_term_list( get_the_ID(), 'project_tag', '', ', ' ); ?></p>

		<strong class="et_project_meta_title"><?php echo esc_html__( 'Posted on', 'Divi' ); ?></strong>
		<p><?php echo get_the_date(); ?></p>
	</div>
<?php }
endif;

if ( ! function_exists( 'et_single_settings_meta_box' ) ) :
function et_single_settings_meta_box( $post ) {
	$post_id = get_the_ID();

	wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' );

	$page_layout = get_post_meta( $post_id, '_et_pb_page_layout', true );

	$side_nav = get_post_meta( $post_id, '_et_pb_side_nav', true );

	$project_nav = get_post_meta( $post_id, '_et_pb_project_nav', true );

	$post_hide_nav = get_post_meta( $post_id, '_et_pb_post_hide_nav', true );
	$post_hide_nav = $post_hide_nav && 'off' === $post_hide_nav ? 'default' : $post_hide_nav;

	$show_title = get_post_meta( $post_id, '_et_pb_show_title', true );

	$page_layouts = array(
		'et_right_sidebar'   => esc_html__( 'Right Sidebar', 'Divi' ),
		'et_left_sidebar'    => esc_html__( 'Left Sidebar', 'Divi' ),
		'et_full_width_page' => esc_html__( 'Full Width', 'Divi' ),
	);

	$layouts = array(
		'light' => esc_html__( 'Light', 'Divi' ),
		'dark'  => esc_html__( 'Dark', 'Divi' ),
	);
	$post_bg_color  = ( $bg_color = get_post_meta( $post_id, '_et_post_bg_color', true ) ) && '' !== $bg_color
		? $bg_color
		: '#ffffff';
	$post_use_bg_color = get_post_meta( $post_id, '_et_post_use_bg_color', true )
		? true
		: false;
	$post_bg_layout = ( $layout = get_post_meta( $post_id, '_et_post_bg_layout', true ) ) && '' !== $layout
		? $layout
		: 'light'; ?>

	<p class="et_pb_page_settings et_pb_page_layout_settings">
		<label for="et_pb_page_layout" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Page Layout', 'Divi' ); ?>: </label>

		<select id="et_pb_page_layout" name="et_pb_page_layout">
		<?php
		foreach ( $page_layouts as $layout_value => $layout_name ) {
			printf( '<option value="%2$s"%3$s>%1$s</option>',
				esc_html( $layout_name ),
				esc_attr( $layout_value ),
				selected( $layout_value, $page_layout, false )
			);
		} ?>
		</select>
	</p>
	<p class="et_pb_page_settings et_pb_side_nav_settings" style="display: none;">
		<label for="et_pb_side_nav" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Dot Navigation', 'Divi' ); ?>: </label>

		<select id="et_pb_side_nav" name="et_pb_side_nav">
			<option value="off" <?php selected( 'off', $side_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
			<option value="on" <?php selected( 'on', $side_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
		</select>
	</p>
	<p class="et_pb_page_settings">
		<label for="et_pb_post_hide_nav" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Hide Nav Before Scroll', 'Divi' ); ?>: </label>

		<select id="et_pb_post_hide_nav" name="et_pb_post_hide_nav">
			<option value="default" <?php selected( 'default', $post_hide_nav ); ?>><?php esc_html_e( 'Default', 'Divi' ); ?></option>
			<option value="no" <?php selected( 'no', $post_hide_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
			<option value="on" <?php selected( 'on', $post_hide_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
		</select>
	</p>

<?php if ( 'post' === $post->post_type ) : ?>
	<p class="et_pb_page_settings et_pb_single_title" style="display: none;">
		<label for="et_single_title" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Post Title', 'Divi' ); ?>: </label>

		<select id="et_single_title" name="et_single_title">
			<option value="on" <?php selected( 'on', $show_title ); ?>><?php esc_html_e( 'Show', 'Divi' ); ?></option>
			<option value="off" <?php selected( 'off', $show_title ); ?>><?php esc_html_e( 'Hide', 'Divi' ); ?></option>
		</select>
	</p>

	<p class="et_divi_quote_settings et_divi_audio_settings et_divi_link_settings et_divi_format_setting et_pb_page_settings">
		<label for="et_post_use_bg_color" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Use Background Color', 'Divi' ); ?></label>
		<input name="et_post_use_bg_color" type="checkbox" id="et_post_use_bg_color" <?php checked( $post_use_bg_color ); ?> />
	</p>

	<p class="et_post_bg_color_setting et_divi_format_setting et_pb_page_settings">
		<input id="et_post_bg_color" name="et_post_bg_color" class="color-picker-hex" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'Divi' ); ?>" value="<?php echo esc_attr( $post_bg_color ); ?>" data-default-color="#ffffff" />
	</p>

	<p class="et_divi_quote_settings et_divi_audio_settings et_divi_link_settings et_divi_format_setting">
		<label for="et_post_bg_layout" style="font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Text Color', 'Divi' ); ?>: </label>
		<select id="et_post_bg_layout" name="et_post_bg_layout">
	<?php
		foreach ( $layouts as $layout_name => $layout_title )
			printf( '<option value="%s"%s>%s</option>',
				esc_attr( $layout_name ),
				selected( $layout_name, $post_bg_layout, false ),
				esc_html( $layout_title )
			);
	?>
		</select>
	</p>
<?php endif;

if ( 'project' === $post->post_type ) : ?>
	<p class="et_pb_page_settings et_pb_project_nav" style="display: none;">
		<label for="et_project_nav" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Project Navigation', 'Divi' ); ?>: </label>

		<select id="et_project_nav" name="et_project_nav">
			<option value="off" <?php selected( 'off', $project_nav ); ?>><?php esc_html_e( 'Hide', 'Divi' ); ?></option>
			<option value="on" <?php selected( 'on', $project_nav ); ?>><?php esc_html_e( 'Show', 'Divi' ); ?></option>
		</select>
	</p>
<?php endif;
}
endif;

function et_divi_post_settings_save_details( $post_id, $post ){
	global $pagenow;

	if ( 'post.php' != $pagenow ) return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	if ( ! isset( $_POST['et_settings_nonce'] ) || ! wp_verify_nonce( $_POST['et_settings_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	if ( isset( $_POST['et_post_use_bg_color'] ) )
		update_post_meta( $post_id, '_et_post_use_bg_color', true );
	else
		delete_post_meta( $post_id, '_et_post_use_bg_color' );

	if ( isset( $_POST['et_post_bg_color'] ) )
		update_post_meta( $post_id, '_et_post_bg_color', sanitize_text_field( $_POST['et_post_bg_color'] ) );
	else
		delete_post_meta( $post_id, '_et_post_bg_color' );

	if ( isset( $_POST['et_post_bg_layout'] ) )
		update_post_meta( $post_id, '_et_post_bg_layout', sanitize_text_field( $_POST['et_post_bg_layout'] ) );
	else
		delete_post_meta( $post_id, '_et_post_bg_layout' );

	if ( isset( $_POST['et_single_title'] ) )
		update_post_meta( $post_id, '_et_pb_show_title', sanitize_text_field( $_POST['et_single_title'] ) );
	else
		delete_post_meta( $post_id, '_et_pb_show_title' );

	if ( isset( $_POST['et_pb_post_hide_nav'] ) )
		update_post_meta( $post_id, '_et_pb_post_hide_nav', sanitize_text_field( $_POST['et_pb_post_hide_nav'] ) );
	else
		delete_post_meta( $post_id, '_et_pb_post_hide_nav' );

	if ( isset( $_POST['et_project_nav'] ) )
		update_post_meta( $post_id, '_et_pb_project_nav', sanitize_text_field( $_POST['et_project_nav'] ) );
	else
		delete_post_meta( $post_id, '_et_pb_project_nav' );

	if ( isset( $_POST['et_pb_page_layout'] ) ) {
		update_post_meta( $post_id, '_et_pb_page_layout', sanitize_text_field( $_POST['et_pb_page_layout'] ) );
	} else {
		delete_post_meta( $post_id, '_et_pb_page_layout' );
	}

	if ( isset( $_POST['et_pb_side_nav'] ) ) {
		update_post_meta( $post_id, '_et_pb_side_nav', sanitize_text_field( $_POST['et_pb_side_nav'] ) );
	} else {
		delete_post_meta( $post_id, '_et_pb_side_nav' );
	}


}
add_action( 'save_post', 'et_divi_post_settings_save_details', 10, 2 );

if ( ! function_exists( 'et_get_one_font_languages' ) ) :
function et_get_one_font_languages() {
	$one_font_languages = array(
		'he_IL' => array(
			'language_name'   => 'Hebrew',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/alefhebrew.css',
			'font_family'     => "'Alef Hebrew', serif",
		),
		'ja' => array(
			'language_name'   => 'Japanese',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/notosansjapanese.css',
			'font_family'     => "'Noto Sans Japanese', serif",
		),
		'ko_KR' => array(
			'language_name'   => 'Korean',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/hanna.css',
			'font_family'     => "'Hanna', serif",
		),
		'ar' => array(
			'language_name'   => 'Arabic',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/lateef.css',
			'font_family'     => "'Lateef', serif",
		),
		'th' => array(
			'language_name'   => 'Thai',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/notosansthai.css',
			'font_family'     => "'Noto Sans Thai', serif",
		),
		'ms_MY' => array(
			'language_name'   => 'Malay',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/notosansmalayalam.css',
			'font_family'     => "'Noto Sans Malayalam', serif",
		),
		'zh_CN' => array(
			'language_name'   => 'Chinese',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/cwtexfangsong.css',
			'font_family'     => "'cwTeXFangSong', serif",
		),
	);

	return $one_font_languages;
}
endif;

function et_divi_customize_register( $wp_customize ) {
	$wp_customize->remove_section( 'title_tagline' );
	$wp_customize->remove_section( 'background_image' );
	$wp_customize->remove_section( 'colors' );
	$wp_customize->register_control_type( 'ET_Divi_Customize_Color_Alpha_Control' );

	wp_register_script( 'wp-color-picker-alpha', get_template_directory_uri() . '/includes/builder/scripts/ext/wp-color-picker-alpha.min.js', array( 'jquery', 'wp-color-picker' ) );

	$option_set_name           = 'et_customizer_option_set';
	$option_set_allowed_values = apply_filters( 'et_customizer_option_set_allowed_values', array( 'module', 'theme' ) );

	$customizer_option_set = '';

	/**
	 * Set a transient,
	 * if 'et_customizer_option_set' query parameter is set to one of the allowed values
	 */
	if ( isset( $_GET[ $option_set_name ] ) && in_array( $_GET[ $option_set_name ], $option_set_allowed_values ) ) {
		$customizer_option_set = $_GET[ $option_set_name ];

		set_transient( 'et_divi_customizer_option_set', $customizer_option_set, DAY_IN_SECONDS );
	}

	if ( '' === $customizer_option_set && ( $et_customizer_option_set_value = get_transient( 'et_divi_customizer_option_set' ) ) ) {
		$customizer_option_set = $et_customizer_option_set_value;
	}

	et_builder_init_global_settings();

	if ( isset( $customizer_option_set ) && 'module' === $customizer_option_set ) {
		// display wp error screen if module customizer disabled for current user
		if ( ! et_pb_is_allowed( 'module_customizer' ) ) {
			wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'Divi' ) );
		}

		$removed_default_sections = array( 'nav', 'static_front_page' );
		foreach ( $removed_default_sections as $default_section ) {
			$wp_customize->remove_section( $default_section );
		}

		et_divi_customizer_module_settings( $wp_customize );
	} else {
		// display wp error screen if theme customizer disabled for current user
		if ( ! et_pb_is_allowed( 'theme_customizer' ) ) {
			wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'Divi' ) );
		}

		et_divi_customizer_theme_settings( $wp_customize );
	}
}
add_action( 'customize_register', 'et_divi_customize_register' );

if ( ! function_exists( 'et_divi_customizer_theme_settings' ) ) :
function et_divi_customizer_theme_settings( $wp_customize ) {
	$site_domain = get_locale();

	$google_fonts = et_builder_get_fonts( array(
		'prepend_standard_fonts' => false,
	) );

	$et_domain_fonts = array(
		'ru_RU' => 'cyrillic',
		'uk' => 'cyrillic',
		'bg_BG' => 'cyrillic',
		'vi' => 'vietnamese',
		'el' => 'greek',
	);

	$et_one_font_languages = et_get_one_font_languages();

	$font_choices = array();
	$font_choices['none'] = array(
		'label' => 'Default Theme Font'
	);

	foreach ( $google_fonts as $google_font_name => $google_font_properties ) {
		if ( isset( $google_font_properties['parent_font'] ) ) {
			$parent_font = $google_font_properties['parent_font'];
			$google_font_properties['character_set'] = $google_fonts[ $parent_font ]['character_set'];
		}

		if ( '' !== $site_domain && isset( $et_domain_fonts[$site_domain] ) && false === strpos( $google_font_properties['character_set'], $et_domain_fonts[$site_domain] ) ) {
			continue;
		}
		$font_choices[ $google_font_name ] = array(
			'label' => $google_font_name,
			'data'  => array(
				'parent_font'    => isset( $google_font_properties['parent_font'] ) ? $google_font_properties['parent_font'] : '',
				'parent_styles'  => isset( $google_font_properties['parent_font'] ) && isset( $google_fonts[$google_font_properties['parent_font']]['styles'] ) ? $google_fonts[$google_font_properties['parent_font']]['styles'] : $google_font_properties['styles'],
				'current_styles' => isset( $google_font_properties['parent_font'] ) && isset( $google_fonts[$google_font_properties['parent_font']]['styles'] ) && isset( $google_font_properties['styles'] ) ? $google_font_properties['styles'] : '',
				'parent_subset'  => isset( $google_font_properties['parent_font'] ) && isset( $google_fonts[$google_font_properties['parent_font']]['character_set'] ) ? $google_fonts[$google_font_properties['parent_font']]['character_set'] : '',
				'standard'       => isset( $google_font_properties['standard'] ) && $google_font_properties['standard'] ? 'on' : 'off',
			)
		);
	}

	$wp_customize->add_panel( 'et_divi_general_settings' , array(
		'title'		=> esc_html__( 'General Settings', 'Divi' ),
		'priority'	=> 1,
	) );

	$wp_customize->add_section( 'title_tagline', array(
		'title'    => esc_html__( 'Site Identity', 'Divi' ),
		'panel' => 'et_divi_general_settings',
	) );

	$wp_customize->add_section( 'et_divi_general_layout' , array(
		'title'		=> esc_html__( 'Layout Settings', 'Divi' ),
		'panel' => 'et_divi_general_settings',
	) );

	$wp_customize->add_section( 'et_divi_general_typography' , array(
		'title'		=> esc_html__( 'Typography', 'Divi' ),
		'panel' => 'et_divi_general_settings',
	) );

	$wp_customize->add_panel( 'et_divi_mobile' , array(
		'title'		=> esc_html__( 'Mobile Styles', 'Divi' ),
		'priority' => 6,
	) );

	$wp_customize->add_section( 'et_divi_mobile_tablet' , array(
		'title'		=> esc_html__( 'Tablet', 'Divi' ),
		'panel' => 'et_divi_mobile',
	) );

	$wp_customize->add_section( 'et_divi_mobile_phone' , array(
		'title'		=> esc_html__( 'Phone', 'Divi' ),
		'panel' => 'et_divi_mobile',
	) );

	$wp_customize->add_section( 'et_divi_mobile_menu' , array(
		'title'		=> esc_html__( 'Mobile Menu', 'Divi' ),
		'panel' => 'et_divi_mobile',
	) );

	$wp_customize->add_section( 'et_divi_general_background' , array(
		'title'		=> esc_html__( 'Background', 'Divi' ),
		'panel' => 'et_divi_general_settings',
	) );

	$wp_customize->add_panel( 'et_divi_header_panel', array(
		'title' => esc_html__( 'Header & Navigation', 'Divi' ),
		'priority' => 2,
	) );

	$wp_customize->add_section( 'et_divi_header_layout' , array(
		'title'		=> esc_html__( 'Header Format', 'Divi' ),
		'panel' => 'et_divi_header_panel',
	) );

	$wp_customize->add_section( 'et_divi_header_primary' , array(
		'title'		=> esc_html__( 'Primary Menu Bar', 'Divi' ),
		'panel' => 'et_divi_header_panel',
	) );

	$wp_customize->add_section( 'et_divi_header_secondary' , array(
		'title'		=> esc_html__( 'Secondary Menu Bar', 'Divi' ),
		'panel' => 'et_divi_header_panel',
	) );

	$wp_customize->add_section( 'et_divi_header_slide' , array(
		'title'		=> esc_html__( 'Slide In & Fullscreen Header Settings', 'Divi' ),
		'panel' => 'et_divi_header_panel',
	) );

	$wp_customize->add_section( 'et_divi_header_fixed' , array(
		'title'		=> esc_html__( 'Fixed Navigation Settings', 'Divi' ),
		'panel' => 'et_divi_header_panel',
	) );

	$wp_customize->add_section( 'et_divi_header_information' , array(
		'title'		=> esc_html__( 'Header Elements', 'Divi' ),
		'panel' => 'et_divi_header_panel',
	) );

	$wp_customize->add_panel( 'et_divi_footer_panel' , array(
		'title'		=> esc_html__( 'Footer', 'Divi' ),
		'priority'	=> 3,
	) );

	$wp_customize->add_section( 'et_divi_footer_layout' , array(
		'title'		=> esc_html__( 'Layout', 'Divi' ),
		'panel' => 'et_divi_footer_panel',
	) );

	$wp_customize->add_section( 'et_divi_footer_widgets' , array(
		'title'		=> esc_html__( 'Widgets', 'Divi' ),
		'panel' => 'et_divi_footer_panel',
	) );

	$wp_customize->add_section( 'et_divi_footer_elements' , array(
		'title'		=> esc_html__( 'Footer Elements', 'Divi' ),
		'panel' => 'et_divi_footer_panel',
	) );

	$wp_customize->add_section( 'et_divi_footer_menu' , array(
		'title'		=> esc_html__( 'Footer Menu', 'Divi' ),
		'panel' => 'et_divi_footer_panel',
	) );

	$wp_customize->add_section( 'et_divi_bottom_bar' , array(
		'title'		=> esc_html__( 'Bottom Bar', 'Divi' ),
		'panel' => 'et_divi_footer_panel',
	) );

	$wp_customize->add_section( 'et_color_schemes' , array(
		'title'       => esc_html__( 'Color Schemes', 'Divi' ),
		'priority'    => 7,
		'description' => esc_html__( 'Note: Color settings set above should be applied to the Default color scheme.', 'Divi' ),
	) );

	$wp_customize->add_panel( 'et_divi_buttons_settings' , array(
		'title'		=> esc_html__( 'Buttons', 'Divi' ),
		'priority'	=> 4,
	) );

	$wp_customize->add_section( 'et_divi_buttons' , array(
		'title'       => esc_html__( 'Buttons Style', 'Divi' ),
		'panel'       => 'et_divi_buttons_settings',
	) );

	$wp_customize->add_section( 'et_divi_buttons_hover' , array(
		'title'       => esc_html__( 'Buttons Hover Style', 'Divi' ),
		'panel'       => 'et_divi_buttons_settings',
	) );

	$wp_customize->add_panel( 'et_divi_blog_settings' , array(
		'title'		=> esc_html__( 'Blog', 'Divi' ),
		'priority'	=> 5,
	) );

	$wp_customize->add_section( 'et_divi_blog_post' , array(
		'title'       => esc_html__( 'Post', 'Divi' ),
		'panel'       => 'et_divi_blog_settings',
	) );

	$wp_customize->add_setting( 'et_divi[post_meta_font_size]', array(
		'default'       => '14',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';

	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[post_meta_font_size]', array(
		'label'	      => esc_html__( 'Meta Text Size', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_meta_height]', array(
		'default'       => '1',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_float_number',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[post_meta_height]', array(
		'label'	      => esc_html__( 'Meta Line Height', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => .8,
			'max'  => 3,
			'step' => .1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_meta_spacing]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[post_meta_spacing]', array(
		'label'	      => esc_html__( 'Meta Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -2,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_meta_style]', array(
		'default'       => '',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_font_style',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[post_meta_style]', array(
		'label'	      => esc_html__( 'Meta Font Style', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'font_style',
		'choices'     => et_divi_font_style_choices(),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_header_font_size]', array(
		'default'       => '30',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[post_header_font_size]', array(
		'label'	      => esc_html__( 'Header Text Size', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 72,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_header_height]', array(
		'default'       => '1',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_float_number',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[post_header_height]', array(
		'label'	      => esc_html__( 'Header Line Height', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0.8,
			'max'  => 3,
			'step' => 0.1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_header_spacing]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_int_number',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[post_header_spacing]', array(
		'label'	      => esc_html__( 'Header Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -2,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_header_style]', array(
		'default'       => '',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_font_style',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[post_header_style]', array(
		'label'	      => esc_html__( 'Header Font Style', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'font_style',
		'choices'     => et_divi_font_style_choices(),
	) ) );

	$wp_customize->add_setting( 'et_divi[boxed_layout]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'et_divi[boxed_layout]', array(
		'label'		=> esc_html__( 'Enable Boxed Layout', 'Divi' ),
		'section'	=> 'et_divi_general_layout',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[content_width]', array(
		'default'       => '1080',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[content_width]', array(
		'label'	      => esc_html__( 'Website Content Width', 'Divi' ),
		'section'     => 'et_divi_general_layout',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 960,
			'max'  => 1920,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[gutter_width]', array(
		'default'       => '3',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[gutter_width]', array(
		'label'	      => esc_html__( 'Website Gutter Width', 'Divi' ),
		'section'     => 'et_divi_general_layout',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 1,
			'max'  => 4,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[use_sidebar_width]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'et_divi[use_sidebar_width]', array(
		'label'		=> esc_html__( 'Use Custom Sidebar Width', 'Divi' ),
		'section'	=> 'et_divi_general_layout',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[sidebar_width]', array(
		'default'       => '21',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[sidebar_width]', array(
		'label'	      => esc_html__( 'Sidebar Width', 'Divi' ),
		'section'     => 'et_divi_general_layout',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 19,
			'max'  => 33,
			'step' => 1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[section_padding]', array(
		'default'       => '4',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[section_padding]', array(
		'label'	      => esc_html__( 'Section Height', 'Divi' ),
		'section'     => 'et_divi_general_layout',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[phone_section_height]', array(
		'default'       => et_get_option( 'tablet_section_height', '50' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[phone_section_height]', array(
		'label'	      => esc_html__( 'Section Height', 'Divi' ),
		'section'     => 'et_divi_mobile_phone',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 150,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[tablet_section_height]', array(
		'default'       => '50',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[tablet_section_height]', array(
		'label'	      => esc_html__( 'Section Height', 'Divi' ),
		'section'     => 'et_divi_mobile_tablet',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 150,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[row_padding]', array(
		'default'       => '2',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[row_padding]', array(
		'label'	      => esc_html__( 'Row Height', 'Divi' ),
		'section'     => 'et_divi_general_layout',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[phone_row_height]', array(
		'default'       => et_get_option( 'tablet_row_height', '30' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[phone_row_height]', array(
		'label'	      => esc_html__( 'Row Height', 'Divi' ),
		'section'     => 'et_divi_mobile_phone',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 150,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[tablet_row_height]', array(
		'default'       => '30',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[tablet_row_height]', array(
		'label'	      => esc_html__( 'Row Height', 'Divi' ),
		'section'     => 'et_divi_mobile_tablet',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 150,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[cover_background]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'et_divi[cover_background]', array(
		'label'		=> esc_html__( 'Stretch Background Image', 'Divi' ),
		'section'	=> 'et_divi_general_background',
		'type'      => 'checkbox',
	) );

	if ( ! is_null( $wp_customize->get_setting( 'background_color' ) ) ) {
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
			'label'		=> esc_html__( 'Background Color', 'Divi' ),
			'section'	=> 'et_divi_general_background',
		) ) );
	}

	if ( ! is_null( $wp_customize->get_setting( 'background_image' ) ) ) {
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'background_image', array(
			'label'		=> esc_html__( 'Background Image', 'Divi' ),
			'section'	=> 'et_divi_general_background',
		) ) );
	}

	// Remove default background_repeat setting and control since native
	// background_repeat field has different different settings
	$wp_customize->remove_setting( 'background_repeat' );
	$wp_customize->remove_control( 'background_repeat' );

	// Re-defined Divi specific background repeat option
	$wp_customize->add_setting( 'background_repeat', array(
		'default'           => apply_filters( 'et_divi_background_repeat_default', 'repeat' ),
		'sanitize_callback' => 'et_sanitize_background_repeat',
		'theme_supports'    => 'custom-background',
		'capability'        => 'edit_theme_options',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( 'background_repeat', array(
		'label'		=> esc_html__( 'Background Repeat', 'Divi' ),
		'section'	=> 'et_divi_general_background',
		'type'      => 'radio',
		'choices'   => et_divi_background_repeat_choices(),
	) );

	$wp_customize->add_control( 'background_position_x', array(
		'label'		=> esc_html__( 'Background Position', 'Divi' ),
		'section'	=> 'et_divi_general_background',
		'type'      => 'radio',
		'choices'    => array(
				'left'       => esc_html__( 'Left', 'Divi' ),
				'center'     => esc_html__( 'Center', 'Divi' ),
				'right'      => esc_html__( 'Right', 'Divi' ),
			),
	) );

	// Remove default background_attachment setting and control since native
	// background_attachment field has different different settings
	$wp_customize->remove_setting( 'background_attachment' );
	$wp_customize->remove_control( 'background_attachment' );

	$wp_customize->add_setting( 'background_attachment', array(
		'default'           => apply_filters( 'et_sanitize_background_attachment_default', 'scroll' ),
		'sanitize_callback' => 'et_sanitize_background_attachment',
		'theme_supports'    => 'custom-background',
		'capability'        => 'edit_theme_options',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( 'background_attachment', array(
		'label'		=> esc_html__( 'Background Position', 'Divi' ),
		'section'	=> 'et_divi_general_background',
		'type'      => 'radio',
		'choices'    => et_divi_background_attachment_choices(),
	) );

	$wp_customize->add_setting( 'et_divi[body_font_size]', array(
		'default'       => '14',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[body_font_size]', array(
		'label'	      => esc_html__( 'Body Text Size', 'Divi' ),
		'section'     => 'et_divi_general_typography',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[body_font_height]', array(
		'default'       => '1.7',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_float_number',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[body_font_height]', array(
		'label'	      => esc_html__( 'Body Line Height', 'Divi' ),
		'section'     => 'et_divi_general_typography',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0.8,
			'max'  => 3,
			'step' => 0.1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[phone_body_font_size]', array(
		'default'       => et_get_option( 'tablet_body_font_size', '14' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[phone_body_font_size]', array(
		'label'	      => esc_html__( 'Body Text Size', 'Divi' ),
		'section'     => 'et_divi_mobile_phone',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[tablet_body_font_size]', array(
		'default'       => et_get_option( 'body_font_size', '14' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[tablet_body_font_size]', array(
		'label'	      => esc_html__( 'Body Text Size', 'Divi' ),
		'section'     => 'et_divi_mobile_tablet',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[body_header_size]', array(
		'default'       => '30',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[body_header_size]', array(
		'label'	      => esc_html__( 'Header Text Size', 'Divi' ),
		'section'     => 'et_divi_general_typography',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 22,
			'max'  => 72,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[body_header_spacing]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_int_number',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[body_header_spacing]', array(
		'label'	      => esc_html__( 'Header Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_general_typography',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -2,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[body_header_height]', array(
		'default'       => '1',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_float_number',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[body_header_height]', array(
		'label'	      => esc_html__( 'Header Line Height', 'Divi' ),
		'section'     => 'et_divi_general_typography',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0.8,
			'max'  => 3,
			'step' => 0.1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[body_header_style]', array(
		'default'       => '',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_font_style',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[body_header_style]', array(
		'label'	      => esc_html__( 'Header Font Style', 'Divi' ),
		'section'     => 'et_divi_general_typography',
		'type'        => 'font_style',
		'choices'     => et_divi_font_style_choices(),
	) ) );

	$wp_customize->add_setting( 'et_divi[phone_header_font_size]', array(
		'default'       => et_get_option( 'tablet_header_font_size', '30' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[phone_header_font_size]', array(
		'label'	      => esc_html__( 'Header Text Size', 'Divi' ),
		'section'     => 'et_divi_mobile_phone',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 22,
			'max'  => 72,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[tablet_header_font_size]', array(
		'default'       => et_get_option( 'body_header_size', '30' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[tablet_header_font_size]', array(
		'label'	      => esc_html__( 'Header Text Size', 'Divi' ),
		'section'     => 'et_divi_mobile_tablet',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 22,
			'max'  => 72,
			'step' => 1
		),
	) ) );

	if ( ! isset( $et_one_font_languages[$site_domain] ) ) {
		$wp_customize->add_setting( 'et_divi[heading_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
			'sanitize_callback' => 'et_sanitize_font_choices',
		) );

		$wp_customize->add_control( new ET_Divi_Select_Option ( $wp_customize, 'et_divi[heading_font]', array(
			'label'		=> esc_html__( 'Header Font', 'Divi' ),
			'section'	=> 'et_divi_general_typography',
			'settings'	=> 'et_divi[heading_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices,
		) ) );

		$wp_customize->add_setting( 'et_divi[body_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
			'sanitize_callback' => 'et_sanitize_font_choices',
		) );

		$wp_customize->add_control( new ET_Divi_Select_Option ( $wp_customize, 'et_divi[body_font]', array(
			'label'		=> esc_html__( 'Body Font', 'Divi' ),
			'section'	=> 'et_divi_general_typography',
			'settings'	=> 'et_divi[body_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) ) );
	}

	$wp_customize->add_setting( 'et_divi[link_color]', array(
		'default'	=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[link_color]', array(
		'label'		=> esc_html__( 'Body Link Color', 'Divi' ),
		'section'	=> 'et_divi_general_typography',
		'settings'	=> 'et_divi[link_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[font_color]', array(
		'default'		=> '#666666',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[font_color]', array(
		'label'		=> esc_html__( 'Body Text Color', 'Divi' ),
		'section'	=> 'et_divi_general_typography',
		'settings'	=> 'et_divi[font_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[header_color]', array(
		'default'		=> '#666666',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[header_color]', array(
		'label'		=> esc_html__( 'Header Text Color', 'Divi' ),
		'section'	=> 'et_divi_general_typography',
		'settings'	=> 'et_divi[header_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[accent_color]', array(
		'default'		=> '#2ea3f2',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[accent_color]', array(
		'label'		=> esc_html__( 'Theme Accent Color', 'Divi' ),
		'section'	=> 'et_divi_general_layout',
		'settings'	=> 'et_divi[accent_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[color_schemes]', array(
		'default'		=> 'none',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_color_scheme',
	) );

	$wp_customize->add_control( 'et_divi[color_schemes]', array(
		'label'		=> esc_html__( 'Color Schemes', 'Divi' ),
		'section'	=> 'et_color_schemes',
		'settings'	=> 'et_divi[color_schemes]',
		'type'		=> 'select',
		'choices'	=> et_divi_color_scheme_choices(),
	) );

	$wp_customize->add_setting( 'et_divi[header_style]', array(
		'default'       => 'left',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_header_style',
	) );

	$wp_customize->add_control( 'et_divi[header_style]', array(
		'label'		=> esc_html__( 'Header Style', 'Divi' ),
		'section'	=> 'et_divi_header_layout',
		'type'      => 'select',
		'choices'	=> et_divi_header_style_choices(),
	) );

	$wp_customize->add_setting( 'et_divi[vertical_nav]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'et_divi[vertical_nav]', array(
		'label'		=> esc_html__( 'Enable Vertical Navigation', 'Divi' ),
		'section'	=> 'et_divi_header_layout',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[vertical_nav_orientation]', array(
		'default'       => 'left',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_left_right',
	) );

	$wp_customize->add_control( 'et_divi[vertical_nav_orientation]', array(
		'label'		=> esc_html__( 'Vertical Menu Orientation', 'Divi' ),
		'section'	=> 'et_divi_header_layout',
		'type'      => 'select',
		'choices'	=> et_divi_left_right_choices(),
	) );

	if ( 'on' === et_get_option( 'divi_fixed_nav', 'on' ) ) {

		$wp_customize->add_setting( 'et_divi[hide_nav]', array(
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
			'sanitize_callback' => 'wp_validate_boolean',
		) );

		$wp_customize->add_control( 'et_divi[hide_nav]', array(
			'label'		=> esc_html__( 'Hide Navigation Until Scroll', 'Divi' ),
			'section'	=> 'et_divi_header_layout',
			'type'      => 'checkbox',
		) );

	} // 'on' === et_get_option( 'divi_fixed_nav', 'on' )

	$wp_customize->add_setting( 'et_divi[show_header_social_icons]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'et_divi[show_header_social_icons]', array(
		'label'		=> esc_html__( 'Show Social Icons', 'Divi' ),
		'section'	=> 'et_divi_header_information',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[show_search_icon]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'et_divi[show_search_icon]', array(
		'label'		=> esc_html__( 'Show Search Icon', 'Divi' ),
		'section'	=> 'et_divi_header_information',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[slide_nav_show_top_bar]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'et_divi[slide_nav_show_top_bar]', array(
		'label'		=> esc_html__( 'Show Top Bar', 'Divi' ),
		'section'	=> 'et_divi_header_slide',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[slide_nav_width]', array(
		'default'       => '320',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[slide_nav_width]', array(
		'label'	      => esc_html__( 'Menu Width', 'Divi' ),
		'section'     => 'et_divi_header_slide',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 280,
			'max'  => 600,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[slide_nav_font_size]', array(
		'default'       => '14',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[slide_nav_font_size]', array(
		'label'	      => esc_html__( 'Menu Text Size', 'Divi' ),
		'section'     => 'et_divi_header_slide',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 12,
			'max'  => 24,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[slide_nav_top_font_size]', array(
		'default'       => '14',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[slide_nav_top_font_size]', array(
		'label'	      => esc_html__( 'Top Bar Text Size', 'Divi' ),
		'section'     => 'et_divi_header_slide',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 12,
			'max'  => 24,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[fullscreen_nav_font_size]', array(
		'default'       => '30',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[fullscreen_nav_font_size]', array(
		'label'	      => esc_html__( 'Menu Text Size', 'Divi' ),
		'section'     => 'et_divi_header_slide',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 12,
			'max'  => 50,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[fullscreen_nav_top_font_size]', array(
		'default'       => '18',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[fullscreen_nav_top_font_size]', array(
		'label'	      => esc_html__( 'Top Bar Text Size', 'Divi' ),
		'section'     => 'et_divi_header_slide',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 12,
			'max'  => 40,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[slide_nav_font_spacing]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_int_number',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[slide_nav_font_spacing]', array(
		'label'	      => esc_html__( 'Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_header_slide',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -1,
			'max'  => 8,
			'step' => 1
		),
	) ) );

	if ( ! isset( $et_one_font_languages[$site_domain] ) ) {
		$wp_customize->add_setting( 'et_divi[slide_nav_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
			'sanitize_callback' => 'et_sanitize_font_choices',
		) );

		$wp_customize->add_control( new ET_Divi_Select_Option ( $wp_customize, 'et_divi[slide_nav_font]', array(
			'label'		=> esc_html__( 'Font', 'Divi' ),
			'section'	=> 'et_divi_header_slide',
			'settings'	=> 'et_divi[slide_nav_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) ) );
	}

	$wp_customize->add_setting( 'et_divi[slide_nav_font_style]', array(
		'default'       => '',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_font_style',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[slide_nav_font_style]', array(
		'label'	      => esc_html__( 'Font Style', 'Divi' ),
		'section'     => 'et_divi_header_slide',
		'type'        => 'font_style',
		'choices'     => et_divi_font_style_choices(),
	) ) );

	$wp_customize->add_setting( 'et_divi[slide_nav_bg]', array(
		'default'		=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[slide_nav_bg]', array(
		'label'		=> esc_html__( 'Background Color', 'Divi' ),
		'section'	=> 'et_divi_header_slide',
		'settings'	=> 'et_divi[slide_nav_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[slide_nav_links_color]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[slide_nav_links_color]', array(
		'label'		=> esc_html__( 'Menu Link Color', 'Divi' ),
		'section'	=> 'et_divi_header_slide',
		'settings'	=> 'et_divi[slide_nav_links_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[slide_nav_links_color_active]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[slide_nav_links_color_active]', array(
		'label'		=> esc_html__( 'Active Link Color', 'Divi' ),
		'section'	=> 'et_divi_header_slide',
		'settings'	=> 'et_divi[slide_nav_links_color_active]',
	) ) );

	$wp_customize->add_setting( 'et_divi[slide_nav_top_color]', array(
		'default'		=> 'rgba(255,255,255,0.6)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[slide_nav_top_color]', array(
		'label'		=> esc_html__( 'Top Bar Text Color', 'Divi' ),
		'section'	=> 'et_divi_header_slide',
		'settings'	=> 'et_divi[slide_nav_top_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[slide_nav_search]', array(
		'default'		=> 'rgba(255,255,255,0.6)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[slide_nav_search]', array(
		'label'		=> esc_html__( 'Search Bar Text Color', 'Divi' ),
		'section'	=> 'et_divi_header_slide',
		'settings'	=> 'et_divi[slide_nav_search]',
	) ) );

	$wp_customize->add_setting( 'et_divi[slide_nav_search_bg]', array(
		'default'		=> 'rgba(0,0,0,0.2)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[slide_nav_search_bg]', array(
		'label'		=> esc_html__( 'Search Bar Background Color', 'Divi' ),
		'section'	=> 'et_divi_header_slide',
		'settings'	=> 'et_divi[slide_nav_search_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[nav_fullwidth]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'et_divi[nav_fullwidth]', array(
		'label'		=> esc_html__( 'Make Full Width', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[hide_primary_logo]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'et_divi[hide_primary_logo]', array(
		'label'		=> esc_html__( 'Hide Logo Image', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[menu_height]', array(
		'default'       => '66',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[menu_height]', array(
		'label'	      => esc_html__( 'Menu Height', 'Divi' ),
		'section'     => 'et_divi_header_primary',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 30,
			'max'  => 300,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[logo_height]', array(
		'default'       => '54',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[logo_height]', array(
		'label'	      => esc_html__( 'Logo Max Height', 'Divi' ),
		'section'     => 'et_divi_header_primary',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 30,
			'max'  => 100,
			'step' => 1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[menu_margin_top]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[menu_margin_top]', array(
		'label'	      => esc_html__( 'Menu Top Margin', 'Divi' ),
		'section'     => 'et_divi_header_primary',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 300,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_font_size]', array(
		'default'       => '14',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[primary_nav_font_size]', array(
		'label'	      => esc_html__( 'Text Size', 'Divi' ),
		'section'     => 'et_divi_header_primary',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 12,
			'max'  => 24,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_font_spacing]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_int_number',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[primary_nav_font_spacing]', array(
		'label'	      => esc_html__( 'Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_header_primary',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -1,
			'max'  => 8,
			'step' => 1
		),
	) ) );

	if ( ! isset( $et_one_font_languages[$site_domain] ) ) {
		$wp_customize->add_setting( 'et_divi[primary_nav_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
			'sanitize_callback' => 'et_sanitize_font_choices',
		) );

		$wp_customize->add_control( new ET_Divi_Select_Option ( $wp_customize, 'et_divi[primary_nav_font]', array(
			'label'		=> esc_html__( 'Font', 'Divi' ),
			'section'	=> 'et_divi_header_primary',
			'settings'	=> 'et_divi[primary_nav_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) ) );
	}

	$wp_customize->add_setting( 'et_divi[primary_nav_font_style]', array(
		'default'       => '',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_font_style',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[primary_nav_font_style]', array(
		'label'	      => esc_html__( 'Font Style', 'Divi' ),
		'section'     => 'et_divi_header_primary',
		'type'        => 'font_style',
		'choices'     => et_divi_font_style_choices(),
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_font_size]', array(
		'default'       => '12',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_fullwidth]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'et_divi[secondary_nav_fullwidth]', array(
		'label'		=> esc_html__( 'Make Full Width', 'Divi' ),
		'section'	=> 'et_divi_header_secondary',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[secondary_nav_font_size]', array(
		'label'	      => esc_html__( 'Text Size', 'Divi' ),
		'section'     => 'et_divi_header_secondary',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 12,
			'max'  => 20,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_font_spacing]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_int_number',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[secondary_nav_font_spacing]', array(
		'label'	      => esc_html__( 'Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_header_secondary',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -1,
			'max'  => 8,
			'step' => 1
		),
	) ) );

	if ( ! isset( $et_one_font_languages[$site_domain] ) ) {
		$wp_customize->add_setting( 'et_divi[secondary_nav_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
			'sanitize_callback' => 'et_sanitize_font_choices',
		) );

		$wp_customize->add_control( new ET_Divi_Select_Option ( $wp_customize, 'et_divi[secondary_nav_font]', array(
			'label'		=> esc_html__( 'Font', 'Divi' ),
			'section'	=> 'et_divi_header_secondary',
			'settings'	=> 'et_divi[secondary_nav_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) ) );
	}

	$wp_customize->add_setting( 'et_divi[secondary_nav_font_style]', array(
		'default'       => '',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_font_style',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[secondary_nav_font_style]', array(
		'label'	      => esc_html__( 'Font Style', 'Divi' ),
		'section'     => 'et_divi_header_secondary',
		'type'        => 'font_style',
		'choices'     => et_divi_font_style_choices(),
	) ) );

	$wp_customize->add_setting( 'et_divi[menu_link]', array(
		'default'		=> 'rgba(0,0,0,0.6)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[menu_link]', array(
		'label'		=> esc_html__( 'Text Color', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'settings'	=> 'et_divi[menu_link]',
	) ) );

	$wp_customize->add_setting( 'et_divi[hide_mobile_logo]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'et_divi[hide_mobile_logo]', array(
		'label'		=> esc_html__( 'Hide Logo Image', 'Divi' ),
		'section'	=> 'et_divi_mobile_menu',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[mobile_menu_link]', array(
		'default'		=> et_get_option( 'menu_link', 'rgba(0,0,0,0.6)' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[mobile_menu_link]', array(
		'label'		=> esc_html__( 'Text Color', 'Divi' ),
		'section'	=> 'et_divi_mobile_menu',
		'settings'	=> 'et_divi[mobile_menu_link]',
	) ) );

	$wp_customize->add_setting( 'et_divi[menu_link_active]', array(
		'default'		=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[menu_link_active]', array(
		'label'		=> esc_html__( 'Active Link Color', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'settings'	=> 'et_divi[menu_link_active]',
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_bg]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[primary_nav_bg]', array(
		'label'		=> esc_html__( 'Background Color', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'settings'	=> 'et_divi[primary_nav_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_dropdown_bg]', array(
		'default'		=> et_get_option( 'primary_nav_bg', '#ffffff' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[primary_nav_dropdown_bg]', array(
		'label'		=> esc_html__( 'Dropdown Menu Background Color', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'settings'	=> 'et_divi[primary_nav_dropdown_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_dropdown_line_color]', array(
		'default'		=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[primary_nav_dropdown_line_color]', array(
		'label'		=> esc_html__( 'Dropdown Menu Line Color', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'settings'	=> 'et_divi[primary_nav_dropdown_line_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_dropdown_link_color]', array(
		'default'		=> et_get_option( 'menu_link', 'rgba(0,0,0,0.7)' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[primary_nav_dropdown_link_color]', array(
		'label'		=> esc_html__( 'Dropdown Menu Text Color', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'settings'	=> 'et_divi[primary_nav_dropdown_link_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_dropdown_animation]', array(
		'default'       => 'fade',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_dropdown_animation',
	) );

	$wp_customize->add_control( 'et_divi[primary_nav_dropdown_animation]', array(
		'label'		=> esc_html__( 'Dropdown Menu Animation', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'type'      => 'select',
		'choices'	=> et_divi_dropdown_animation_choices(),
	) );

	$wp_customize->add_setting( 'et_divi[mobile_primary_nav_bg]', array(
		'default'		=> et_get_option( 'primary_nav_bg', '#ffffff' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[mobile_primary_nav_bg]', array(
		'label'		=> esc_html__( 'Background Color', 'Divi' ),
		'section'	=> 'et_divi_mobile_menu',
		'settings'	=> 'et_divi[mobile_primary_nav_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_bg]', array(
		'default'		=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[secondary_nav_bg]', array(
		'label'		=> esc_html__( 'Background Color', 'Divi' ),
		'section'	=> 'et_divi_header_secondary',
		'settings'	=> 'et_divi[secondary_nav_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_text_color_new]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[secondary_nav_text_color_new]', array(
		'label'		=> esc_html__( 'Text Color', 'Divi' ),
		'section'	=> 'et_divi_header_secondary',
		'settings'	=> 'et_divi[secondary_nav_text_color_new]',
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_dropdown_bg]', array(
		'default'		=> et_get_option( 'secondary_nav_bg', et_get_option( 'accent_color', '#2ea3f2' ) ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[secondary_nav_dropdown_bg]', array(
		'label'		=> esc_html__( 'Dropdown Menu Background Color', 'Divi' ),
		'section'	=> 'et_divi_header_secondary',
		'settings'	=> 'et_divi[secondary_nav_dropdown_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_dropdown_link_color]', array(
		'default'		=> et_get_option( 'secondary_nav_text_color_new', '#ffffff' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[secondary_nav_dropdown_link_color]', array(
		'label'		=> esc_html__( 'Dropdown Menu Text Color', 'Divi' ),
		'section'	=> 'et_divi_header_secondary',
		'settings'	=> 'et_divi[secondary_nav_dropdown_link_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_dropdown_animation]', array(
		'default'       => 'fade',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_dropdown_animation',
	) );

	$wp_customize->add_control( 'et_divi[secondary_nav_dropdown_animation]', array(
		'label'		=> esc_html__( 'Dropdown Menu Animation', 'Divi' ),
		'section'	=> 'et_divi_header_secondary',
		'type'      => 'select',
		'choices'	=> et_divi_dropdown_animation_choices(),
	) );

	// Setting with no control kept for backwards compatbility
	$wp_customize->add_setting( 'et_divi[primary_nav_text_color]', array(
		'default'       => 'dark',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	// Setting with no control kept for backwards compatbility
	$wp_customize->add_setting( 'et_divi[secondary_nav_text_color]', array(
		'default'       => 'light',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	if ( 'on' === et_get_option( 'divi_fixed_nav', 'on' ) ) {
		$wp_customize->add_setting( 'et_divi[hide_fixed_logo]', array(
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
			'sanitize_callback' => 'wp_validate_boolean',
		) );

		$wp_customize->add_control( 'et_divi[hide_fixed_logo]', array(
			'label'		=> esc_html__( 'Hide Logo Image', 'Divi' ),
			'section'	=> 'et_divi_header_fixed',
			'type'      => 'checkbox',
		) );

		$wp_customize->add_setting( 'et_divi[minimized_menu_height]', array(
			'default'       => '40',
			'type'          => 'option',
			'capability'    => 'edit_theme_options',
			'transport'     => 'postMessage',
			'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[minimized_menu_height]', array(
			'label'	      => esc_html__( 'Fixed Menu Height', 'Divi' ),
			'section'     => 'et_divi_header_fixed',
			'type'        => 'range',
			'input_attrs' => array(
				'min'  => 30,
				'max'  => 300,
				'step' => 1
			),
		) ) );

		$wp_customize->add_setting( 'et_divi[fixed_primary_nav_font_size]', array(
			'default'       => et_get_option( 'primary_nav_font_size', '14' ),
			'type'          => 'option',
			'capability'    => 'edit_theme_options',
			'transport'     => 'postMessage',
			'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[fixed_primary_nav_font_size]', array(
			'label'	      => esc_html__( 'Text Size', 'Divi' ),
			'section'     => 'et_divi_header_fixed',
			'type'        => 'range',
			'input_attrs' => array(
				'min'  => 12,
				'max'  => 24,
				'step' => 1
			),
		) ) );

		$wp_customize->add_setting( 'et_divi[fixed_primary_nav_bg]', array(
			'default'		=> et_get_option( 'primary_nav_bg', '#ffffff' ),
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
			'sanitize_callback' => 'et_sanitize_alpha_color',
		) );

		$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[fixed_primary_nav_bg]', array(
			'label'		=> esc_html__( 'Primary Menu Background Color', 'Divi' ),
			'section'	=> 'et_divi_header_fixed',
			'settings'	=> 'et_divi[fixed_primary_nav_bg]',
		) ) );

		$wp_customize->add_setting( 'et_divi[fixed_secondary_nav_bg]', array(
			'default'		=> et_get_option( 'secondary_nav_bg', '#2ea3f2' ),
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
			'sanitize_callback' => 'et_sanitize_alpha_color',
		) );

		$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[fixed_secondary_nav_bg]', array(
			'label'		=> esc_html__( 'Secondary Menu Background Color', 'Divi' ),
			'section'	=> 'et_divi_header_fixed',
			'settings'	=> 'et_divi[fixed_secondary_nav_bg]',
		) ) );

		$wp_customize->add_setting( 'et_divi[fixed_menu_link]', array(
			'default'       => et_get_option( 'menu_link', 'rgba(0,0,0,0.6)' ),
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
			'sanitize_callback' => 'et_sanitize_alpha_color',
		) );

		$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[fixed_menu_link]', array(
			'label'		=> esc_html__( 'Primary Menu Link Color', 'Divi' ),
			'section'	=> 'et_divi_header_fixed',
			'settings'	=> 'et_divi[fixed_menu_link]',
		) ) );

		$wp_customize->add_setting( 'et_divi[fixed_secondary_menu_link]', array(
			'default'       => et_get_option( 'secondary_nav_text_color_new', '#ffffff' ),
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
			'sanitize_callback' => 'et_sanitize_alpha_color',
		) );

		$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[fixed_secondary_menu_link]', array(
			'label'		=> esc_html__( 'Secondary Menu Link Color', 'Divi' ),
			'section'	=> 'et_divi_header_fixed',
			'settings'	=> 'et_divi[fixed_secondary_menu_link]',
		) ) );

		$wp_customize->add_setting( 'et_divi[fixed_menu_link_active]', array(
			'default'       => et_get_option( 'menu_link_active', '#2ea3f2' ),
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
			'sanitize_callback' => 'et_sanitize_alpha_color',
		) );

		$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[fixed_menu_link_active]', array(
			'label'		=> esc_html__( 'Active Primary Menu Link Color', 'Divi' ),
			'section'	=> 'et_divi_header_fixed',
			'settings'	=> 'et_divi[fixed_menu_link_active]',
		) ) );
	}

	$wp_customize->add_setting( 'et_divi[phone_number]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_html_input_text',
	) );

	$wp_customize->add_control( 'et_divi[phone_number]', array(
		'label'		=> esc_html__( 'Phone Number', 'Divi' ),
		'section'	=> 'et_divi_header_information',
		'type'      => 'text',
	) );

	$wp_customize->add_setting( 'et_divi[header_email]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'sanitize_email',
	) );

	$wp_customize->add_control( 'et_divi[header_email]', array(
		'label'		=> esc_html__( 'Email', 'Divi' ),
		'section'	=> 'et_divi_header_information',
		'type'      => 'text',
	) );

	$wp_customize->add_setting( 'et_divi[show_footer_social_icons]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'et_divi[show_footer_social_icons]', array(
		'label'		=> esc_html__( 'Show Social Icons', 'Divi' ),
		'section'	=> 'et_divi_footer_elements',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[footer_columns]', array(
		'default'       => '4',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_footer_column',
	) );

	$wp_customize->add_control( 'et_divi[footer_columns]', array(
		'label'		=> esc_html__( 'Column Layout', 'Divi' ),
		'section'	=> 'et_divi_footer_layout',
		'settings'	=> 'et_divi[footer_columns]',
		'type'		=> 'select',
		'choices'	=> et_divi_footer_column_choices(),
	) );

	$wp_customize->add_setting( 'et_divi[footer_bg]', array(
		'default'		=> '#222222',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[footer_bg]', array(
		'label'		=> esc_html__( 'Footer Background Color', 'Divi' ),
		'section'	=> 'et_divi_footer_layout',
		'settings'	=> 'et_divi[footer_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[widget_header_font_size]', array(
		'default'       => absint( et_get_option( 'body_header_size', '30' ) ) * .6,
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[widget_header_font_size]', array(
		'label'	      => esc_html__( 'Header Text Size', 'Divi' ),
		'section'     => 'et_divi_footer_widgets',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 72,
			'step' => 1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[widget_header_font_style]', array(
		'default'       => et_get_option( 'widget_header_font_style', '' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_font_style',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[widget_header_font_style]', array(
		'label'	      => esc_html__( 'Header Font Style', 'Divi' ),
		'section'     => 'et_divi_footer_widgets',
		'type'        => 'font_style',
		'choices'     => et_divi_font_style_choices(),
	) ) );

	$wp_customize->add_setting( 'et_divi[widget_body_font_size]', array(
		'default'       => et_get_option( 'body_font_size', '14' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[widget_body_font_size]', array(
		'label'	      => esc_html__( 'Body/Link Text Size', 'Divi' ),
		'section'     => 'et_divi_footer_widgets',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[widget_body_line_height]', array(
		'default'       => '1.7',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_float_number',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[widget_body_line_height]', array(
		'label'	      => esc_html__( 'Body/Link Line Height', 'Divi' ),
		'section'     => 'et_divi_footer_widgets',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0.8,
			'max'  => 3,
			'step' => 0.1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[widget_body_font_style]', array(
		'default'       => et_get_option( 'footer_widget_body_font_style', '' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_font_style',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[widget_body_font_style]', array(
		'label'	      => esc_html__( 'Body Font Style', 'Divi' ),
		'section'     => 'et_divi_footer_widgets',
		'type'        => 'font_style',
		'choices'     => et_divi_font_style_choices(),
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_widget_text_color]', array(
		'default'		=> '#fff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[footer_widget_text_color]', array(
		'label'		=> esc_html__( 'Widget Text Color', 'Divi' ),
		'section'	=> 'et_divi_footer_widgets',
		'settings'	=> 'et_divi[footer_widget_text_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_widget_link_color]', array(
		'default'		=> '#fff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[footer_widget_link_color]', array(
		'label'		=> esc_html__( 'Widget Link Color', 'Divi' ),
		'section'	=> 'et_divi_footer_widgets',
		'settings'	=> 'et_divi[footer_widget_link_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_widget_header_color]', array(
		'default'		=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[footer_widget_header_color]', array(
		'label'		=> esc_html__( 'Widget Header Color', 'Divi' ),
		'section'	=> 'et_divi_footer_widgets',
		'settings'	=> 'et_divi[footer_widget_header_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_widget_bullet_color]', array(
		'default'		=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[footer_widget_bullet_color]', array(
		'label'		=> esc_html__( 'Widget Bullet Color', 'Divi' ),
		'section'	=> 'et_divi_footer_widgets',
		'settings'	=> 'et_divi[footer_widget_bullet_color]',
	) ) );

	/* Footer Menu */
	$wp_customize->add_setting( 'et_divi[footer_menu_background_color]', array(
		'default'		=> 'rgba(255,255,255,0.05)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[footer_menu_background_color]', array(
		'label'		=> esc_html__( 'Footer Menu Background Color', 'Divi' ),
		'section'	=> 'et_divi_footer_menu',
		'settings'	=> 'et_divi[footer_menu_background_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_menu_text_color]', array(
		'default'		=> '#bbbbbb',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[footer_menu_text_color]', array(
		'label'		=> esc_html__( 'Footer Menu Text Color', 'Divi' ),
		'section'	=> 'et_divi_footer_menu',
		'settings'	=> 'et_divi[footer_menu_text_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_menu_active_link_color]', array(
		'default'		=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[footer_menu_active_link_color]', array(
		'label'		=> esc_html__( 'Footer Menu Active Link Color', 'Divi' ),
		'section'	=> 'et_divi_footer_menu',
		'settings'	=> 'et_divi[footer_menu_active_link_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_menu_letter_spacing]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[footer_menu_letter_spacing]', array(
		'label'	      => esc_html__( 'Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_footer_menu',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 20,
			'step' => 1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_menu_font_style]', array(
		'default'       => et_get_option( 'footer_footer_menu_font_style', '' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_font_style',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[footer_menu_font_style]', array(
		'label'	      => esc_html__( 'Font Style', 'Divi' ),
		'section'     => 'et_divi_footer_menu',
		'type'        => 'font_style',
		'choices'     => et_divi_font_style_choices(),
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_menu_font_size]', array(
		'default'       => '14',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[footer_menu_font_size]', array(
		'label'	      => esc_html__( 'Font Size', 'Divi' ),
		'section'     => 'et_divi_footer_menu',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1,
		),
	) ) );

	/* Bottom Bar */
	$wp_customize->add_setting( 'et_divi[bottom_bar_background_color]', array(
		'default'		=> 'rgba(0,0,0,0.32)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[bottom_bar_background_color]', array(
		'label'		=> esc_html__( 'Background Color', 'Divi' ),
		'section'	=> 'et_divi_bottom_bar',
		'settings'	=> 'et_divi[bottom_bar_background_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[bottom_bar_text_color]', array(
		'default'		=> '#666666',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[bottom_bar_text_color]', array(
		'label'		=> esc_html__( 'Text Color', 'Divi' ),
		'section'	=> 'et_divi_bottom_bar',
		'settings'	=> 'et_divi[bottom_bar_text_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[bottom_bar_font_style]', array(
		'default'       => et_get_option( 'footer_bottom_bar_font_style', '' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_font_style',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[bottom_bar_font_style]', array(
		'label'	      => esc_html__( 'Font Style', 'Divi' ),
		'section'     => 'et_divi_bottom_bar',
		'type'        => 'font_style',
		'choices'     => et_divi_font_style_choices(),
	) ) );

	$wp_customize->add_setting( 'et_divi[bottom_bar_font_size]', array(
		'default'       => '14',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[bottom_bar_font_size]', array(
		'label'	      => esc_html__( 'Font Size', 'Divi' ),
		'section'     => 'et_divi_bottom_bar',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[bottom_bar_social_icon_size]', array(
		'default'       => '24',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[bottom_bar_social_icon_size]', array(
		'label'	      => esc_html__( 'Social Icon Size', 'Divi' ),
		'section'     => 'et_divi_bottom_bar',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[bottom_bar_social_icon_color]', array(
		'default'		=> '#666666',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[bottom_bar_social_icon_color]', array(
		'label'		=> esc_html__( 'Social Icon Color', 'Divi' ),
		'section'	=> 'et_divi_bottom_bar',
		'settings'	=> 'et_divi[bottom_bar_social_icon_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[disable_custom_footer_credits]', array(
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'et_divi[disable_custom_footer_credits]', array(
		'label'   => esc_html__( 'Disable Footer Credits', 'Divi' ),
		'section' => 'et_divi_bottom_bar',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[custom_footer_credits]', array(
		'default'           => '',
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'et_sanitize_html_input_text',
	) );

	$wp_customize->add_control( 'et_divi[custom_footer_credits]', array(
		'label'    => esc_html__( 'Edit Footer Credits', 'Divi' ),
		'section'  => 'et_divi_bottom_bar',
		'settings' => 'et_divi[custom_footer_credits]',
		'type'     => 'textarea',
	) );

	$wp_customize->add_setting( 'et_divi[all_buttons_font_size]', array(
		'default'       => ET_Global_Settings::get_value( 'all_buttons_font_size', 'default' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[all_buttons_font_size]', array(
		'label'	      => esc_html__( 'Text Size', 'Divi' ),
		'section'     => 'et_divi_buttons',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 12,
			'max'  => 30,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_text_color]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_text_color]', array(
		'label'		=> esc_html__( 'Text Color', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'settings'	=> 'et_divi[all_buttons_text_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_bg_color]', array(
		'default'		=> 'rgba(0,0,0,0)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_bg_color]', array(
		'label'		=> esc_html__( 'Background Color', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'settings'	=> 'et_divi[all_buttons_bg_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_border_width]', array(
		'default'       => ET_Global_Settings::get_value( 'all_buttons_border_width', 'default' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[all_buttons_border_width]', array(
		'label'	      => esc_html__( 'Border Width', 'Divi' ),
		'section'     => 'et_divi_buttons',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_border_color]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_border_color]', array(
		'label'		=> esc_html__( 'Border Color', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'settings'	=> 'et_divi[all_buttons_border_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_border_radius]', array(
		'default'       => ET_Global_Settings::get_value( 'all_buttons_border_radius', 'default' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[all_buttons_border_radius]', array(
		'label'	      => esc_html__( 'Border Radius', 'Divi' ),
		'section'     => 'et_divi_buttons',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 50,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_spacing]', array(
		'default'       => ET_Global_Settings::get_value( 'all_buttons_spacing', 'default' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_int_number',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[all_buttons_spacing]', array(
		'label'	      => esc_html__( 'Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_buttons',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -2,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_font_style]', array(
		'default'       => '',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_font_style',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[all_buttons_font_style]', array(
		'label'	      => esc_html__( 'Button Font Style', 'Divi' ),
		'section'     => 'et_divi_buttons',
		'type'        => 'font_style',
		'choices'     => et_divi_font_style_choices(),
	) ) );

	if ( ! isset( $et_one_font_languages[$site_domain] ) ) {
		$wp_customize->add_setting( 'et_divi[all_buttons_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
			'sanitize_callback' => 'et_sanitize_font_choices',
		) );

		$wp_customize->add_control( new ET_Divi_Select_Option ( $wp_customize, 'et_divi[all_buttons_font]', array(
			'label'		=> esc_html__( 'Buttons Font', 'Divi' ),
			'section'	=> 'et_divi_buttons',
			'settings'	=> 'et_divi[all_buttons_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) ) );
	}

	$wp_customize->add_setting( 'et_divi[all_buttons_icon]', array(
		'default'       => 'yes',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'sanitize_callback' => 'et_sanitize_yes_no',
	) );

	$wp_customize->add_control( 'et_divi[all_buttons_icon]', array(
		'label'		=> esc_html__( 'Add Button Icon', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'type'      => 'select',
		'choices'	=> et_divi_yes_no_choices(),
	) );

	$wp_customize->add_setting( 'et_divi[all_buttons_selected_icon]', array(
		'default'       => '5',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_font_icon',
	) );

	$wp_customize->add_control( new ET_Divi_Icon_Picker_Option ( $wp_customize, 'et_divi[all_buttons_selected_icon]', array(
		'label'	      => esc_html__( 'Select Icon', 'Divi' ),
		'section'     => 'et_divi_buttons',
		'type'        => 'icon_picker',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_icon_color]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_icon_color]', array(
		'label'		=> esc_html__( 'Icon Color', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'settings'	=> 'et_divi[all_buttons_icon_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_icon_placement]', array(
		'default'       => 'right',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_left_right',
	) );

	$wp_customize->add_control( 'et_divi[all_buttons_icon_placement]', array(
		'label'		=> esc_html__( 'Icon Placement', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'type'      => 'select',
		'choices'	=> et_divi_left_right_choices(),
	) );

	$wp_customize->add_setting( 'et_divi[all_buttons_icon_hover]', array(
		'default'       => 'yes',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_yes_no',
	) );

	$wp_customize->add_control( 'et_divi[all_buttons_icon_hover]', array(
		'label'		=> esc_html__( 'Only Show Icon on Hover', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'type'      => 'select',
		'choices'	=> et_divi_yes_no_choices(),
	) );

	$wp_customize->add_setting( 'et_divi[all_buttons_text_color_hover]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_text_color_hover]', array(
		'label'		=> esc_html__( 'Text Color', 'Divi' ),
		'section'	=> 'et_divi_buttons_hover',
		'settings'	=> 'et_divi[all_buttons_text_color_hover]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_bg_color_hover]', array(
		'default'		=> 'rgba(255,255,255,0.2)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_bg_color_hover]', array(
		'label'		=> esc_html__( 'Background Color', 'Divi' ),
		'section'	=> 'et_divi_buttons_hover',
		'settings'	=> 'et_divi[all_buttons_bg_color_hover]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_border_color_hover]', array(
		'default'		=> 'rgba(0,0,0,0)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
		'sanitize_callback' => 'et_sanitize_alpha_color',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_border_color_hover]', array(
		'label'		=> esc_html__( 'Border Color', 'Divi' ),
		'section'	=> 'et_divi_buttons_hover',
		'settings'	=> 'et_divi[all_buttons_border_color_hover]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_border_radius_hover]', array(
		'default'       => ET_Global_Settings::get_value( 'all_buttons_border_radius_hover', 'default' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'absint'
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[all_buttons_border_radius_hover]', array(
		'label'	      => esc_html__( 'Border Radius', 'Divi' ),
		'section'     => 'et_divi_buttons_hover',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 50,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_spacing_hover]', array(
		'default'       => ET_Global_Settings::get_value( 'all_buttons_spacing_hover', 'default' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
		'sanitize_callback' => 'et_sanitize_int_number',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[all_buttons_spacing_hover]', array(
		'label'	      => esc_html__( 'Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_buttons_hover',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -2,
			'max'  => 10,
			'step' => 1
		),
	) ) );
}
endif;

if ( ! function_exists( 'et_divi_customizer_module_settings' ) ) :
function et_divi_customizer_module_settings( $wp_customize ) {
		/* Section: Image */
		$wp_customize->add_section( 'et_pagebuilder_image', array(
			'priority'       => 10,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Image', 'Divi' ),
			'description'    => esc_html__( 'Image Module Settings', 'Divi' ),
		) );

			$wp_customize->add_setting( 'et_divi[et_pb_image-animation]', array(
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
				'sanitize_callback' => 'et_sanitize_image_animation',
			) );

			$wp_customize->add_control( 'et_divi[et_pb_image-animation]', array(
				'label'		=> esc_html__( 'Animation', 'Divi' ),
				'description' => esc_html__( 'This controls default direction of the lazy-loading animation.', 'Divi' ),
				'section'	=> 'et_pagebuilder_image',
				'type'      => 'select',
				'choices'	=> et_divi_image_animation_choices(),
			) );

		/* Section: Gallery */
		$wp_customize->add_section( 'et_pagebuilder_gallery', array(
			'priority'       => 20,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Gallery', 'Divi' ),
		) );

			// Zoom Icon Color
			$wp_customize->add_setting( 'et_divi[et_pb_gallery-zoom_icon_color]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_gallery-zoom_icon_color', 'default' ), // default color should be theme's accent color
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[et_pb_gallery-zoom_icon_color]', array(
				'label'		=> esc_html__( 'Zoom Icon Color', 'Divi' ),
				'section'	=> 'et_pagebuilder_gallery',
				'settings'	=> 'et_divi[et_pb_gallery-zoom_icon_color]',
			) ) );

			// Hover Overlay Color
			$wp_customize->add_setting( 'et_divi[et_pb_gallery-hover_overlay_color]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_gallery-hover_overlay_color', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
				'sanitize_callback' => 'et_sanitize_alpha_color',
			) );

			$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[et_pb_gallery-hover_overlay_color]', array(
				'label'		=> esc_html__( 'Hover Overlay Color', 'Divi' ),
				'section'	=> 'et_pagebuilder_gallery',
				'settings'	=> 'et_divi[et_pb_gallery-hover_overlay_color]',
			) ) );

			// Title Font Size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_gallery-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_gallery-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_gallery-title_font_size]', array(
				'label'	      => esc_html__( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_gallery',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_gallery-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_gallery-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_gallery-title_font_style]', array(
				'label'	      => esc_html__( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_gallery',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// caption font size Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_gallery-caption_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_gallery-caption_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_gallery-caption_font_size]', array(
				'label'	      => esc_html__( 'Caption Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_gallery',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// caption font style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_gallery-caption_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_gallery-caption_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_gallery-caption_font_style]', array(
				'label'	      => esc_html__( 'Caption Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_gallery',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Blurb */
		$wp_customize->add_section( 'et_pagebuilder_blurb', array(
			'priority'       => 30,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Blurb', 'Divi' ),
		) );

			// Header Font Size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_blurb-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blurb-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_blurb-header_font_size]', array(
				'label'	      => esc_html__( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_blurb',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

		/* Section: Tabs */
		$wp_customize->add_section( 'et_pagebuilder_tabs', array(
			'priority'       => 40,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Tabs', 'Divi' ),
		) );

			// Tab Title Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_tabs-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_tabs-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_tabs-title_font_size]', array(
				'label'	      => esc_html__( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_tabs',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Tab Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_tabs-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_tabs-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_tabs-title_font_style]', array(
				'label'	      => esc_html__( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_tabs',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Padding: Range 0 - 50px
			/* If padding is 20px then the content padding is 20px and the tab padding is: { padding: 10px(50%) 20px; }	*/
			$wp_customize->add_setting( 'et_divi[et_pb_tabs-padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_tabs-padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_tabs-padding]', array(
				'label'	      => esc_html__( 'Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_tabs',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

		/* Section: Slider */
		$wp_customize->add_section( 'et_pagebuilder_slider', array(
			'priority'       => 50,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Slider', 'Divi' ),
			// 'description'    => '',
		) );

			// Slider Padding: Top/Bottom Only
			$wp_customize->add_setting( 'et_divi[et_pb_slider-padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_slider-padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_slider-padding]', array(
				'label'	      => esc_html__( 'Top & Bottom Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_slider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 5,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

			// Header Font size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_slider-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_slider-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_slider-header_font_size]', array(
				'label'	      => esc_html__( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_slider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_slider-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_slider-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_slider-header_font_style]', array(
				'label'	      => esc_html__( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_slider',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Content Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_slider-body_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_slider-body_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_slider-body_font_size]', array(
				'label'	      => esc_html__( 'Content Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_slider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Content Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_slider-body_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_slider-body_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_slider-body_font_style]', array(
				'label'	      => esc_html__( 'Content Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_slider',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Testimonial */
		$wp_customize->add_section( 'et_pagebuilder_testimonial', array(
			'priority'       => 60,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Testimonial', 'Divi' ),
		) );

			// Author Name Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_testimonial-author_name_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_testimonial-author_name_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_testimonial-author_name_font_style]', array(
				'label'	      => esc_html__( 'Name Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_testimonial',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Author Details Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_testimonial-author_details_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_testimonial-author_details_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_testimonial-author_details_font_style]', array(
				'label'	      => esc_html__( 'Details Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_testimonial',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Portrait Border Radius
			$wp_customize->add_setting( 'et_divi[et_pb_testimonial-portrait_border_radius]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_testimonial-portrait_border_radius', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_testimonial-portrait_border_radius]', array(
				'label'	      => esc_html__( 'Portrait Border Radius', 'Divi' ),
				'section'     => 'et_pagebuilder_testimonial',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
			) ) );

			// Portrait Width
			$wp_customize->add_setting( 'et_divi[et_pb_testimonial-portrait_width]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_testimonial-portrait_width', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_testimonial-portrait_width]', array(
				'label'	      => esc_html__( 'Image Width', 'Divi' ),
				'section'     => 'et_pagebuilder_testimonial',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
			) ) );

			// Portrait Height
			$wp_customize->add_setting( 'et_divi[et_pb_testimonial-portrait_height]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_testimonial-portrait_height', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_testimonial-portrait_height]', array(
				'label'	      => esc_html__( 'Image Height', 'Divi' ),
				'section'     => 'et_pagebuilder_testimonial',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
			) ) );

		/* Section: Pricing Table */
		$wp_customize->add_section( 'et_pagebuilder_pricing_table', array(
			'priority'       => 70,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Pricing Table', 'Divi' ),
		) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_pricing_tables-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_pricing_tables-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_pricing_tables-header_font_size]', array(
				'label'	      => esc_html__( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_pricing_table',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_pricing_tables-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_pricing_tables-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_pricing_tables-header_font_style]', array(
				'label'	      => esc_html__( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_pricing_table',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Subhead Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_pricing_tables-subheader_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_pricing_tables-subheader_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_pricing_tables-subheader_font_size]', array(
				'label'	      => esc_html__( 'Subheader Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_pricing_table',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Subhead Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_pricing_tables-subheader_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_pricing_tables-subheader_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_pricing_tables-subheader_font_style]', array(
				'label'	      => esc_html__( 'Subheader Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_pricing_table',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Price font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_pricing_tables-price_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_pricing_tables-price_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_pricing_tables-price_font_size]', array(
				'label'	      => esc_html__( 'Price Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_pricing_table',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 100,
					'step' => 1,
				),
			) ) );

			// Price font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_pricing_tables-price_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_pricing_tables-price_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_pricing_tables-price_font_style]', array(
				'label'	      => esc_html__( 'Pricing Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_pricing_table',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Call To Action */
		$wp_customize->add_section( 'et_pagebuilder_call_to_action', array(
			'priority'       => 80,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Call To Action', 'Divi' ),
		) );

			// Header font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_cta-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_cta-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_cta-header_font_size]', array(
				'label'	      => esc_html__( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_call_to_action',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_cta-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_cta-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_cta-header_font_style]', array(
				'label'	      => esc_html__( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_call_to_action',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Padding: Range 0px - 200px
			$wp_customize->add_setting( 'et_divi[et_pb_cta-custom_padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_cta-custom_padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_cta-custom_padding]', array(
				'label'	      => esc_html__( 'Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_call_to_action',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
			) ) );

		/* Section: Audio */
		$wp_customize->add_section( 'et_pagebuilder_audio', array(
			'priority'       => 90,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Audio', 'Divi' ),
		) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_audio-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_audio-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_audio-title_font_size]', array(
				'label'	      => esc_html__( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_audio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_audio-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_audio-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_audio-title_font_style]', array(
				'label'	      => esc_html__( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_audio',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Subhead Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_audio-caption_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_audio-caption_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_audio-caption_font_size]', array(
				'label'	      => esc_html__( 'Subheader Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_audio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Subhead Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_audio-caption_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_audio-caption_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_audio-caption_font_style]', array(
				'label'	      => esc_html__( 'Subheader Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_audio',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Email Optin */
		$wp_customize->add_section( 'et_pagebuilder_subscribe', array(
			'priority'       => 100,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Email Optin', 'Divi' ),
		) );

			// Header font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_signup-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_signup-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_signup-header_font_size]', array(
				'label'	      => esc_html__( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_subscribe',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_signup-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_signup-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_signup-header_font_style]', array(
				'label'	      => esc_html__( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_subscribe',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Padding: Range 0px - 200px
			$wp_customize->add_setting( 'et_divi[et_pb_signup-padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_signup-padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_signup-padding]', array(
				'label'	      => esc_html__( 'Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_subscribe',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
			) ) );

		/* Section: Login */
		$wp_customize->add_section( 'et_pagebuilder_login', array(
			'priority'       => 110,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Login', 'Divi' ),
			// 'description'    => '',
		) );

			// Header font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_login-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_login-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_login-header_font_size]', array(
				'label'	      => esc_html__( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_login',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_login-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_login-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_login-header_font_style]', array(
				'label'	      => esc_html__( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_login',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Padding: Range 0px - 200px
			$wp_customize->add_setting( 'et_divi[et_pb_login-custom_padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_login-custom_padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_login-custom_padding]', array(
				'label'	      => esc_html__( 'Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_login',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
			) ) );

		/* Section: Portfolio */
		$wp_customize->add_section( 'et_pagebuilder_portfolio', array(
			'priority'       => 120,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Portfolio', 'Divi' ),
		) );

			// Zoom Icon Color
			$wp_customize->add_setting( 'et_divi[et_pb_portfolio-zoom_icon_color]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_portfolio-zoom_icon_color', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[et_pb_portfolio-zoom_icon_color]', array(
				'label'		=> esc_html__( 'Zoom Icon Color', 'Divi' ),
				'section'	=> 'et_pagebuilder_portfolio',
				'settings'	=> 'et_divi[et_pb_portfolio-zoom_icon_color]',
			) ) );

			// Hover Overlay Color
			$wp_customize->add_setting( 'et_divi[et_pb_portfolio-hover_overlay_color]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_portfolio-hover_overlay_color', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
				'sanitize_callback' => 'et_sanitize_alpha_color',
			) );

			$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[et_pb_portfolio-hover_overlay_color]', array(
				'label'		=> esc_html__( 'Hover Overlay Color', 'Divi' ),
				'section'	=> 'et_pagebuilder_portfolio',
				'settings'	=> 'et_divi[et_pb_portfolio-hover_overlay_color]',
			) ) );

			// Title Font Size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_portfolio-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_portfolio-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_portfolio-title_font_size]', array(
				'label'	      => esc_html__( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_portfolio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_portfolio-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_portfolio-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_portfolio-title_font_style]', array(
				'label'	      => esc_html__( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_portfolio',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Category font size Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_portfolio-caption_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_portfolio-caption_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_portfolio-caption_font_size]', array(
				'label'	      => esc_html__( 'Caption Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_portfolio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Category Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_portfolio-caption_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_portfolio-caption_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_portfolio-caption_font_style]', array(
				'label'	      => esc_html__( 'Caption Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_portfolio',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Filterable Portfolio */
		$wp_customize->add_section( 'et_pagebuilder_filterable_portfolio', array(
			'priority'       => 130,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Filterable Portfolio', 'Divi' ),
		) );

			// Zoom Icon Color
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-zoom_icon_color]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-zoom_icon_color', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[et_pb_filterable_portfolio-zoom_icon_color]', array(
				'label'		=> esc_html__( 'Zoom Icon Color', 'Divi' ),
				'section'	=> 'et_pagebuilder_filterable_portfolio',
				'settings'	=> 'et_divi[et_pb_filterable_portfolio-zoom_icon_color]',
			) ) );

			// Hover Overlay Color
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-hover_overlay_color]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-hover_overlay_color', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
				'sanitize_callback' => 'et_sanitize_alpha_color',
			) );

			$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[et_pb_filterable_portfolio-hover_overlay_color]', array(
				'label'		=> esc_html__( 'Hover Overlay Color', 'Divi' ),
				'section'	=> 'et_pagebuilder_filterable_portfolio',
				'settings'	=> 'et_divi[et_pb_filterable_portfolio-hover_overlay_color]',
			) ) );

			// Title Font Size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_filterable_portfolio-title_font_size]', array(
				'label'	      => esc_html__( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_filterable_portfolio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_filterable_portfolio-title_font_style]', array(
				'label'	      => esc_html__( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_filterable_portfolio',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Category font size Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-caption_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-caption_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_filterable_portfolio-caption_font_size]', array(
				'label'	      => esc_html__( 'Caption Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_filterable_portfolio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Category Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-caption_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-caption_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_filterable_portfolio-caption_font_style]', array(
				'label'	      => esc_html__( 'Caption Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_filterable_portfolio',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Filters Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-filter_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-filter_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_filterable_portfolio-filter_font_size]', array(
				'label'	      => esc_html__( 'Filters Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_filterable_portfolio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Filters Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-filter_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-filter_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_filterable_portfolio-filter_font_style]', array(
				'label'	      => esc_html__( 'Filters Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_filterable_portfolio',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Bar Counter */
		$wp_customize->add_section( 'et_pagebuilder_bar_counter', array(
			'priority'       => 140,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Bar Counter', 'Divi' ),
		) );

			// Label Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_counters-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_counters-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_counters-title_font_size]', array(
				'label'	      => esc_html__( 'Label Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_bar_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Labels Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_counters-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_counters-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_counters-title_font_style]', array(
				'label'	      => esc_html__( 'Label Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_bar_counter',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Percent Font Size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_counters-percent_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_counters-percent_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_counters-percent_font_size]', array(
				'label'	      => esc_html__( 'Percent Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_bar_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Percent Font Style: : B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_counters-percent_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_counters-percent_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_counters-percent_font_style]', array(
				'label'	      => esc_html__( 'Percent Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_bar_counter',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Bar Padding: Range 0px - 30px (top and bottom padding only)
			$wp_customize->add_setting( 'et_divi[et_pb_counters-padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_counters-padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_counters-padding]', array(
				'label'	      => esc_html__( 'Bar Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_bar_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

			// Bar Border Radius
			$wp_customize->add_setting( 'et_divi[et_pb_counters-border_radius]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_counters-border_radius', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_counters-border_radius]', array(
				'label'	      => esc_html__( 'Bar Border Radius', 'Divi' ),
				'section'     => 'et_pagebuilder_bar_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 80,
					'step' => 1,
				),
			) ) );

		/* Section: Circle Counter */
		$wp_customize->add_section( 'et_pagebuilder_circle_counter', array(
			'priority'       => 150,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Circle Counter', 'Divi' ),
		) );
			// Number Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_circle_counter-number_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_circle_counter-number_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_circle_counter-number_font_size]', array(
				'label'	      => esc_html__( 'Number Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_circle_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Number Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_circle_counter-number_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_circle_counter-number_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_circle_counter-number_font_style]', array(
				'label'	      => esc_html__( 'Number Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_circle_counter',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Title Font Size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_circle_counter-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_circle_counter-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_circle_counter-title_font_size]', array(
				'label'	      => esc_html__( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_circle_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_circle_counter-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_circle_counter-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_circle_counter-title_font_style]', array(
				'label'	      => esc_html__( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_circle_counter',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Number Counter */
		$wp_customize->add_section( 'et_pagebuilder_number_counter', array(
			'priority'       => 160,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Number Counter', 'Divi' ),
		) );

			// Number Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_number_counter-number_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_number_counter-number_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_number_counter-number_font_size]', array(
				'label'	      => esc_html__( 'Number Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_number_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Number Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_number_counter-number_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_number_counter-number_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_number_counter-number_font_style]', array(
				'label'	      => esc_html__( 'Number Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_number_counter',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Title Font Size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_number_counter-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_number_counter-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_number_counter-title_font_size]', array(
				'label'	      => esc_html__( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_number_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_number_counter-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_number_counter-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_number_counter-title_font_style]', array(
				'label'	      => esc_html__( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_number_counter',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Accordion */
		$wp_customize->add_section( 'et_pagebuilder_accordion', array(
			'priority'       => 170,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Accordion', 'Divi' ),
		) );
			// Title Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_accordion-toggle_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_accordion-toggle_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_accordion-toggle_font_size]', array(
				'label'	      => esc_html__( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_accordion',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Accordion Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_accordion-toggle_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_accordion-toggle_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_accordion-toggle_font_style]', array(
				'label'	      => esc_html__( 'Opened Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_accordion',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Inactive Accordion Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_accordion-inactive_toggle_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_accordion-inactive_title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_accordion-inactive_toggle_font_style]', array(
				'label'	      => esc_html__( 'Closed Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_accordion',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Toggle Accordion Icon Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_accordion-toggle_icon_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_accordion-toggle_icon_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_accordion-toggle_icon_size]', array(
				'label'	      => esc_html__( 'Toggle Icon Size', 'Divi' ),
				'section'     => 'et_pagebuilder_accordion',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 16,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Padding: Range 0 - 50px
			/* Padding effects each individual Accordion */
			$wp_customize->add_setting( 'et_divi[et_pb_accordion-custom_padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_accordion-custom_padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_accordion-custom_padding]', array(
				'label'	      => esc_html__( 'Toggle Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_accordion',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

		/* Section: Toggle */
		$wp_customize->add_section( 'et_pagebuilder_toggle', array(
			'priority'       => 180,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Toggle', 'Divi' ),
		) );

			// Title Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_toggle-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_toggle-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_toggle-title_font_size]', array(
				'label'	      => esc_html__( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_toggle',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Toggle Title Font Style
			$wp_customize->add_setting( 'et_divi[et_pb_toggle-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_toggle-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_toggle-title_font_style]', array(
				'label'	      => esc_html__( 'Opened Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_toggle',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Inactive Toggle Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_toggle-inactive_title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_toggle-inactive_title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_toggle-inactive_title_font_style]', array(
				'label'	      => esc_html__( 'Closed Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_toggle',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Open& Close Icon Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_toggle-toggle_icon_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_toggle-toggle_icon_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_toggle-toggle_icon_size]', array(
				'label'	      => esc_html__( 'Toggle Icon Size', 'Divi' ),
				'section'     => 'et_pagebuilder_toggle',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 16,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Padding: Range 0 - 50px
			$wp_customize->add_setting( 'et_divi[et_pb_toggle-custom_padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_toggle-custom_padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_toggle-custom_padding]', array(
				'label'	      => esc_html__( 'Toggle Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_toggle',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

		/* Section: Contact Form */
		$wp_customize->add_section( 'et_pagebuilder_contact_form', array(
			'priority'       => 190,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Contact Form', 'Divi' ),
		) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_contact_form-title_font_size]', array(
				'label'	      => esc_html__( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Header Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_contact_form-title_font_style]', array(
				'label'	      => esc_html__( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Input Field Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-form_field_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-form_field_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_contact_form-form_field_font_size]', array(
				'label'	      => esc_html__( 'Input Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Input Field Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-form_field_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-form_field_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_contact_form-form_field_font_style]', array(
				'label'	      => esc_html__( 'Input Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Input Field Padding: Range 0 - 50px
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_contact_form-padding]', array(
				'label'	      => esc_html__( 'Input Field Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

			// Captcha Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-captcha_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-captcha_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_contact_form-captcha_font_size]', array(
				'label'	      => esc_html__( 'Captcha Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Captcha Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-captcha_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-captcha_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_contact_form-captcha_font_style]', array(
				'label'	      => esc_html__( 'Captcha Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Sidebar */
		$wp_customize->add_section( 'et_pagebuilder_sidebar', array(
			'priority'       => 200,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Sidebar', 'Divi' ),
		) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_sidebar-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_sidebar-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_sidebar-header_font_size]', array(
				'label'	      => esc_html__( 'Widget Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_sidebar',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Header font style
			$wp_customize->add_setting( 'et_divi[et_pb_sidebar-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_sidebar-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_sidebar-header_font_style]', array(
				'label'	      => esc_html__( 'Widget Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_sidebar',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Show/hide Vertical Divider
			$wp_customize->add_setting( 'et_divi[et_pb_sidebar-remove_border]', array(
				'default'		=> ET_Global_Settings::get_checkbox_value( 'et_pb_sidebar-remove_border', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
				'sanitize_callback' => 'wp_validate_boolean',
			) );

			$wp_customize->add_control( 'et_divi[et_pb_sidebar-remove_border]', array(
				'label'		=> esc_html__( 'Remove Vertical Divider', 'Divi' ),
				'section'	=> 'et_pagebuilder_sidebar',
				'type'      => 'checkbox',
			) );

		/* Section: Divider */
		$wp_customize->add_section( 'et_pagebuilder_divider', array(
			'priority'       => 200,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Divider', 'Divi' ),
		) );

			// Show/hide Divider
			$wp_customize->add_setting( 'et_divi[et_pb_divider-show_divider]', array(
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
				'sanitize_callback' => 'wp_validate_boolean',
			) );

			$wp_customize->add_control( 'et_divi[et_pb_divider-show_divider]', array(
				'label'		=> esc_html__( 'Show Divider', 'Divi' ),
				'section'	=> 'et_pagebuilder_divider',
				'type'      => 'checkbox',
			) );

			// Divider Style
			$wp_customize->add_setting( 'et_divi[et_pb_divider-divider_style]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_divider-divider_style', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
				'sanitize_callback' => 'et_sanitize_divider_style',
			) );

			$wp_customize->add_control( 'et_divi[et_pb_divider-divider_style]', array(
				'label'		=> esc_html__( 'Divider Style', 'Divi' ),
				'section'	=> 'et_pagebuilder_divider',
				'settings'	=> 'et_divi[et_pb_divider-divider_style]',
				'type'		=> 'select',
				'choices'	=> et_divi_divider_style_choices(),
			) );

			// Divider Weight
			$wp_customize->add_setting( 'et_divi[et_pb_divider-divider_weight]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_divider-divider_weight', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_divider-divider_weight]', array(
				'label'	      => esc_html__( 'Divider Weight', 'Divi' ),
				'section'     => 'et_pagebuilder_divider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
			) ) );

			// Divider Height
			$wp_customize->add_setting( 'et_divi[et_pb_divider-height]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_divider-height', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_divider-height]', array(
				'label'	      => esc_html__( 'Divider Height', 'Divi' ),
				'section'     => 'et_pagebuilder_divider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
			) ) );

			// Divider Position
			$wp_customize->add_setting( 'et_divi[et_pb_divider-divider_position]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_divider-divider_position', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
				'sanitize_callback' => 'et_sanitize_divider_position',
			) );

			$wp_customize->add_control( 'et_divi[et_pb_divider-divider_position]', array(
				'label'		=> esc_html__( 'Divider Position', 'Divi' ),
				'section'	=> 'et_pagebuilder_divider',
				'settings'	=> 'et_divi[et_pb_divider-divider_position]',
				'type'		=> 'select',
				'choices'	=> et_divi_divider_position_choices(),
			) );

		/* Section: Person */
		$wp_customize->add_section( 'et_pagebuilder_person', array(
			'priority'       => 210,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Person', 'Divi' ),
		) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_team_member-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_team_member-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_team_member-header_font_size]', array(
				'label'	      => esc_html__( 'Name Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_person',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Header font style
			$wp_customize->add_setting( 'et_divi[et_pb_team_member-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_team_member-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_team_member-header_font_style]', array(
				'label'	      => esc_html__( 'Name Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_person',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Subhead Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_team_member-subheader_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_team_member-subheader_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_team_member-subheader_font_size]', array(
				'label'	      => esc_html__( 'Subheader Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_person',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Subhead Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_team_member-subheader_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_team_member-subheader_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_team_member-subheader_font_style]', array(
				'label'	      => esc_html__( 'Subheader Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_person',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Network Icons size: Range 16px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_team_member-social_network_icon_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_team_member-social_network_icon_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_team_member-social_network_icon_size]', array(
				'label'	      => esc_html__( 'Social Network Icon Size', 'Divi' ),
				'section'     => 'et_pagebuilder_person',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 16,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

		/* Section: Blog */
		$wp_customize->add_section( 'et_pagebuilder_blog', array(
			'priority'       => 220,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Blog', 'Divi' ),
		) );

			// Post Title Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_blog-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_blog-header_font_size]', array(
				'label'	      => esc_html__( 'Post Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_blog',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Post Title Font Style
			$wp_customize->add_setting( 'et_divi[et_pb_blog-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_blog-header_font_style]', array(
				'label'	      => esc_html__( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_blog',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Meta Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_blog-meta_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog-meta_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_blog-meta_font_size]', array(
				'label'	      => esc_html__( 'Meta Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_blog',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Meta Field Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_blog-meta_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog-meta_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_blog-meta_font_style]', array(
				'label'	      => esc_html__( 'Meta Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_blog',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Blog Grid */
		$wp_customize->add_section( 'et_pagebuilder_masonry_blog', array(
			'priority'       => 230,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Blog Grid', 'Divi' ),
		) );

			// Post Title Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_blog_masonry-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog_masonry-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_blog_masonry-header_font_size]', array(
				'label'	      => esc_html__( 'Post Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_masonry_blog',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Post Title Font Style
			$wp_customize->add_setting( 'et_divi[et_pb_blog_masonry-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog_masonry-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_blog_masonry-header_font_style]', array(
				'label'	      => esc_html__( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_masonry_blog',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Meta Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_blog_masonry-meta_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog_masonry-meta_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_blog_masonry-meta_font_size]', array(
				'label'	      => esc_html__( 'Meta Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_masonry_blog',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Meta Field Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_blog_masonry-meta_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog_masonry-meta_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_blog_masonry-meta_font_style]', array(
				'label'	      => esc_html__( 'Meta Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_masonry_blog',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Shop */
		$wp_customize->add_section( 'et_pagebuilder_shop', array(
			'priority'       => 240,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Shop', 'Divi' ),
		) );

			// Product Name Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_shop-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_shop-title_font_size]', array(
				'label'	      => esc_html__( 'Product Name Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Product Name Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_shop-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_shop-title_font_style]', array(
				'label'	      => esc_html__( 'Product Name Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Sale Badge Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_shop-sale_badge_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-sale_badge_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_shop-sale_badge_font_size]', array(
				'label'	      => esc_html__( 'Sale Badge Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Sale Badge Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_shop-sale_badge_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-sale_badge_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_shop-sale_badge_font_style]', array(
				'label'	      => esc_html__( 'Sale Badge Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Price Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_shop-price_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-price_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_shop-price_font_size]', array(
				'label'	      => esc_html__( 'Price Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Price Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_shop-price_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-price_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_shop-price_font_style]', array(
				'label'	      => esc_html__( 'Price Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Sale Price Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_shop-sale_price_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-sale_price_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_shop-sale_price_font_size]', array(
				'label'	      => esc_html__( 'Sale Price Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Sale Price Font Style: B / I / TT / U/
			$wp_customize->add_setting( 'et_divi[et_pb_shop-sale_price_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-sale_price_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_shop-sale_price_font_style]', array(
				'label'	      => esc_html__( 'Sale Price Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Countdown */
		$wp_customize->add_section( 'et_pagebuilder_countdown', array(
			'priority'       => 250,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Countdown', 'Divi' ),
		) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_countdown_timer-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_countdown_timer-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_countdown_timer-header_font_size]', array(
				'label'	      => esc_html__( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_countdown',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_countdown_timer-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_countdown_timer-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_countdown_timer-header_font_style]', array(
				'label'	      => esc_html__( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_countdown',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Social Follow */
		$wp_customize->add_section( 'et_pagebuilder_social_follow', array(
			'priority'       => 250,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Social Follow', 'Divi' ),
		) );

			// Follow Button Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_social_media_follow-icon_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_social_media_follow-icon_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_social_media_follow-icon_size]', array(
				'label'	      => esc_html__( 'Follow Font & Icon Size', 'Divi' ),
				'section'     => 'et_pagebuilder_social_follow',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Follow Button Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_social_media_follow-button_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_social_media_follow-button_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_social_media_follow-button_font_style]', array(
				'label'	      => esc_html__( 'Button Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_social_follow',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

		/* Section: Fullwidth Slider */
		$wp_customize->add_section( 'et_pagebuilder_fullwidth_slider', array(
			'priority'       => 270,
			'capability'     => 'edit_theme_options',
			'title'          => esc_html__( 'Fullwidth Slider', 'Divi' ),
		) );

			// Slider Padding: Top/Bottom Only
			$wp_customize->add_setting( 'et_divi[et_pb_fullwidth_slider-padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_fullwidth_slider-padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_fullwidth_slider-padding]', array(
				'label'	      => esc_html__( 'Top & Bottom Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_fullwidth_slider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 5,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_fullwidth_slider-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_fullwidth_slider-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_fullwidth_slider-header_font_size]', array(
				'label'	      => esc_html__( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_fullwidth_slider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_fullwidth_slider-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_fullwidth_slider-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_fullwidth_slider-header_font_style]', array(
				'label'	      => esc_html__( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_fullwidth_slider',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );

			// Content Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_fullwidth_slider-body_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_fullwidth_slider-body_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_fullwidth_slider-body_font_size]', array(
				'label'	      => esc_html__( 'Content Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_fullwidth_slider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Content Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_fullwidth_slider-body_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_fullwidth_slider-body_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
				'sanitize_callback' => 'et_sanitize_font_style',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_fullwidth_slider-body_font_style]', array(
				'label'	      => esc_html__( 'Content Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_fullwidth_slider',
				'type'        => 'font_style',
				'choices'     => et_divi_font_style_choices(),
			) ) );
}
endif;

/**
 * Add action hook to the footer in customizer preview.
 */
function et_customizer_preview_footer_action() {
	if ( is_customize_preview() ) {
		do_action( 'et_customizer_footer_preview' );
	}
}
add_action( 'wp_footer', 'et_customizer_preview_footer_action' );

/**
 * Add container with social icons to the footer in customizer preview.
 * Used to get the icons and append them into the header when user enables the header social icons in customizer.
 */
function et_load_social_icons() {
	echo '<div class="et_customizer_social_icons" style="display:none;">';
		get_template_part( 'includes/social_icons', 'header' );
	echo '</div>';
}
add_action( 'et_customizer_footer_preview', 'et_load_social_icons' );

function et_divi_customize_preview_js() {
	$theme_version = et_get_theme_version();
	wp_enqueue_script( 'divi-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), $theme_version, true );
	wp_localize_script( 'divi-customizer', 'et_main_customizer_data', array(
		'original_footer_credits' => et_get_original_footer_credits(),
	) );
}
add_action( 'customize_preview_init', 'et_divi_customize_preview_js' );

function et_divi_customize_preview_css() {
	$theme_version = et_get_theme_version();

	wp_enqueue_style( 'divi-customizer-controls-styles', get_template_directory_uri() . '/css/theme-customizer-controls-styles.css', array(), $theme_version );
	wp_enqueue_script( 'divi-customizer-controls-js', get_template_directory_uri() . '/js/theme-customizer-controls.js', array( 'jquery' ), $theme_version, true );
	wp_localize_script( 'divi-customizer-controls-js', 'et_divi_customizer_data', array(
		'is_old_wp' => et_pb_is_wp_old_version() ? 'old' : 'new',
	) );
}
add_action( 'customize_controls_enqueue_scripts', 'et_divi_customize_preview_css' );

/**
 * Modifying builder options based on saved Divi values
 * @param array  current builder options values
 * @return array modified builder options values
 */
function et_divi_builder_options( $options ) {
	$options['all_buttons_icon'] = et_get_option( 'all_buttons_icon', 'yes' );

	return $options;
}
add_filter( 'et_builder_options', 'et_divi_builder_options' );

/**
 * Add custom customizer control
 * Check for WP_Customizer_Control existence before adding custom control because WP_Customize_Control is loaded on customizer page only
 *
 * @see _wp_customize_include()
 */
if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Font style control for Customizer
	 */
	class ET_Divi_Font_Style_Option extends WP_Customize_Control {
		public $type = 'font_style';
		public function render_content() {
			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
			</label>
			<?php $current_values = explode('|', $this->value() );
			if ( empty( $this->choices ) )
				return;
			foreach ( $this->choices as $value => $label ) :
				$checked_class = in_array( $value, $current_values ) ? ' et_font_style_checked' : '';
				?>
					<span class="et_font_style et_font_value_<?php echo esc_attr( $value ); echo $checked_class; ?>">
						<input type="checkbox" class="et_font_style_checkbox" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $current_values ) ); ?> />
					</span>
				<?php
			endforeach;
			?>
			<input type="hidden" class="et_font_styles" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
			<?php
		}
	}

	/**
	 * Icon picker control for Customizer
	 */
	class ET_Divi_Icon_Picker_Option extends WP_Customize_Control {
		public $type = 'icon_picker';

		public function render_content() {

		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			et_pb_font_icon_list(); ?>
			<input type="hidden" class="et_selected_icon" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
		</label>
		<?php
		}
	}

	/**
	 * Range-based sliding value picker for Customizer
	 */
	class ET_Divi_Range_Option extends WP_Customize_Control {
		public $type = 'range';

		public function render_content() {
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
			<input type="<?php echo esc_attr( $this->type ); ?>" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> data-reset_value="<?php echo esc_attr( $this->setting->default ); ?>" />
			<input type="number" <?php $this->input_attrs(); ?> class="et-pb-range-input" value="<?php echo esc_attr( $this->value() ); ?>" />
			<span class="et_divi_reset_slider"></span>
		</label>
		<?php
		}
	}

	/**
	 * Custom Select option which supports data attributes for the <option> tags
	 */
	class ET_Divi_Select_Option extends WP_Customize_Control {
		public $type = 'select';

		public function render_content() {
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<select <?php $this->link(); ?>>
				<?php
				foreach ( $this->choices as $value => $attributes ) {
					$data_output = '';

					if ( ! empty( $attributes['data'] ) ) {
						foreach( $attributes['data'] as $data_name => $data_value ) {
							if ( '' !== $data_value ) {
								$data_output .= sprintf( ' data-%1$s="%2$s"',
									esc_attr( $data_name ),
									esc_attr( $data_value )
								);
							}
						}
					}

					echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . $data_output . '>' . esc_html( $attributes['label'] ) . '</option>';
				}
				?>
			</select>
		</label>
		<?php
		}
	}

	/**
	 * Color picker with alpha color support for Customizer
	 */
	class ET_Divi_Customize_Color_Alpha_Control extends WP_Customize_Control {
		public $type = 'et_coloralpha';

		public $statuses;

		public function __construct( $manager, $id, $args = array() ) {
			$this->statuses = array( '' => esc_html__( 'Default', 'Divi' ) );
			parent::__construct( $manager, $id, $args );

			// Printed saved value should always be in lowercase
			add_filter( "customize_sanitize_js_{$id}", array( $this, 'sanitize_saved_value' ) );
		}

		public function enqueue() {
			wp_enqueue_script( 'wp-color-picker-alpha' );
			wp_enqueue_style( 'wp-color-picker' );
		}

		public function to_json() {
			parent::to_json();
			$this->json['statuses'] = $this->statuses;
			$this->json['defaultValue'] = $this->setting->default;
		}

		public function render_content() {}

		public function content_template() {
			?>
			<# var defaultValue = '';
			if ( data.defaultValue ) {
				if ( '#' !== data.defaultValue.substring( 0, 1 ) && 'rgba' !== data.defaultValue.substring( 0, 4 ) ) {
					defaultValue = '#' + data.defaultValue;
				} else {
					defaultValue = data.defaultValue;
				}
				defaultValue = ' data-default-color=' + defaultValue; // Quotes added automatically.
			} #>
			<label>
				<# if ( data.label ) { #>
					<span class="customize-control-title">{{{ data.label }}}</span>
				<# } #>
				<# if ( data.description ) { #>
					<span class="description customize-control-description">{{{ data.description }}}</span>
				<# } #>
				<div class="customize-control-content">
					<input class="color-picker-hex" data-alpha="true" type="text" maxlength="30" placeholder="<?php esc_attr_e( 'Hex Value', 'Divi' ); ?>" {{ defaultValue }} />
				</div>
			</label>
			<?php
		}

		/**
		 * Ensure saved value to be printed in lowercase.
		 * Mismatched case causes broken 4.7 in Customizer. Color Alpha control only saves string.
		 * @param string  saved value
		 * @return string formatted value
		 */
		public function sanitize_saved_value( $value ) {
			return strtolower( $value );
		}
	}

}

function et_divi_add_customizer_css() { 
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( is_admin() && ! is_customize_preview() ) {
			return;
		}

		$post_id        = get_the_ID();
		$is_preview     = is_preview() || ( function_exists( 'is_et_pb_preview' ) && is_et_pb_preview() );

		$disabled_global = 'off' === et_get_option( 'et_pb_static_css_file', 'on' );
		$disabled_post   = $disabled_global || 'off' === get_post_meta( $post_id, '_et_pb_static_css_file', true );
		$forced_inline   = $is_preview || $disabled_global || $disabled_post;
		$unified_styles  = ! $forced_inline;

		$resource_owner = $unified_styles ? 'core' : 'divi';
		$resource_slug  = $unified_styles ? 'unified' : 'customizer';

		if ( $is_preview ) {
			// Don't let previews cause existing saved static css files to be modified.
			$resource_slug .= '-preview';
		}

		$styles_manager    = et_core_page_resource_get( $resource_owner, $resource_slug, $post_id );
		$styles_manager_vb = et_core_page_resource_get( $resource_owner, "{$resource_slug}-vb", $post_id );

		// Make sure we don't output styles when we're not supposed to
		$styles_manager->disabled    = function_exists( 'et_fb_is_enabled' ) && et_fb_is_enabled();
		$styles_manager_vb->disabled = ! $styles_manager->disabled;

		$styles_manager = $styles_manager->disabled ? $styles_manager_vb : $styles_manager;

		$styles_manager->forced_inline = $forced_inline;

		if ( ! $styles_manager->forced_inline && $styles_manager->has_file() ) {
			// Static resource has already been created. No need to continue here.
			return;
		}

		$css_output = array();

		// Detect legacy settings
		$detect_legacy_secondary_nav_color = et_get_option( 'secondary_nav_text_color', 'Light' );
		$detect_legacy_primary_nav_color = et_get_option( 'primary_nav_text_color', 'Dark' );

		if ( $detect_legacy_primary_nav_color == 'Light' ) {
			$legacy_primary_nav_color = '#ffffff';
		} else {
			$legacy_primary_nav_color = 'rgba(0,0,0,0.6)';
		}

		if ( $detect_legacy_secondary_nav_color == 'Light' ) {
			$legacy_secondary_nav_color = '#ffffff';
		} else {
			$legacy_secondary_nav_color = 'rgba(0,0,0,0.7)';
		}

		$body_font_size = absint( et_get_option( 'body_font_size', '14' ) );
		$body_font_height = floatval( et_get_option( 'body_font_height', '1.7' ) );
		$body_header_size = absint( et_get_option( 'body_header_size', '30' ) );
		$body_header_style = et_get_option( 'body_header_style', '', '', true );
		$body_header_spacing = intval( et_get_option( 'body_header_spacing', '0' ) );
		$body_header_height = floatval( et_get_option( 'body_header_height', '1' ) );
		$body_font_color = et_get_option( 'font_color', '#666666' );
		$body_header_color = et_get_option( 'header_color', '#666666' );

		$accent_color = et_get_option( 'accent_color', '#2ea3f2' );
		$link_color = et_get_option( 'link_color', $accent_color );

		$content_width = absint( et_get_option( 'content_width', '1080' ) );
		$large_content_width = intval ( $content_width * 1.25 );
		$use_sidebar_width = et_get_option( 'use_sidebar_width', false );
		$sidebar_width = intval( et_get_option( 'sidebar_width', 21 ) );
		$section_padding = absint( et_get_option( 'section_padding', '4' ) );
		$row_padding = absint( et_get_option( 'row_padding', '2' ) );

		$tablet_header_font_size = absint( et_get_option( 'tablet_header_font_size', $body_header_size ) );
		$tablet_body_font_size = absint( et_get_option( 'tablet_body_font_size', $body_font_size ) );
		$tablet_section_height = absint( et_get_option( 'tablet_section_height', '50' ) );
		$tablet_row_height = absint( et_get_option( 'tablet_row_height', '30' ) );

		$phone_header_font_size = absint( et_get_option( 'phone_header_font_size', $tablet_header_font_size ) );
		$phone_body_font_size = absint( et_get_option( 'phone_body_font_size', $tablet_body_font_size ) );
		$phone_section_height = absint( et_get_option( 'phone_section_height', $tablet_section_height ) );
		$phone_row_height = absint( et_get_option( 'phone_row_height', $tablet_row_height ) );

		$header_style = et_get_option( 'header_style', 'left' );
		$menu_height = absint( et_get_option( 'menu_height', '66' ) );
		$logo_height = absint( et_get_option( 'logo_height', '54' ) );
		$menu_margin_top = absint( et_get_option( 'menu_margin_top', '0' ) );
		$menu_link = et_get_option( 'menu_link', $legacy_primary_nav_color );
		$menu_link_active = et_get_option( 'menu_link_active', '#2ea3f2' );
		$vertical_nav = et_get_option( 'vertical_nav', false );

		$hide_primary_logo = et_get_option( 'hide_primary_logo', 'false' );
		$hide_fixed_logo = et_get_option( 'hide_fixed_logo', 'false' );

		$primary_nav_font_size = absint( et_get_option( 'primary_nav_font_size', '14' ) );
		$primary_nav_font_spacing = intval( et_get_option( 'primary_nav_font_spacing', '0' ) );
		$primary_nav_bg = et_get_option( 'primary_nav_bg', '#ffffff' );
		$primary_nav_font_style = et_get_option( 'primary_nav_font_style', '', '', true );
		$primary_nav_dropdown_bg = et_get_option( 'primary_nav_dropdown_bg', $primary_nav_bg );
		$primary_nav_dropdown_link_color = et_get_option( 'primary_nav_dropdown_link_color', $menu_link );
		$primary_nav_dropdown_line_color = et_get_option( 'primary_nav_dropdown_line_color', $accent_color );

		$mobile_menu_link = et_get_option( 'mobile_menu_link', $menu_link );
		$mobile_primary_nav_bg = et_get_option( 'mobile_primary_nav_bg', $primary_nav_bg );

		$secondary_nav_font_size = absint( et_get_option( 'secondary_nav_font_size', '12' ) );
		$secondary_nav_font_spacing = intval( et_get_option( 'secondary_nav_font_spacing', '0' ) );
		$secondary_nav_font_style = et_get_option( 'secondary_nav_font_style', '', '', true );
		$secondary_nav_text_color_new = et_get_option( 'secondary_nav_text_color_new', $legacy_secondary_nav_color );
		$secondary_nav_bg = et_get_option( 'secondary_nav_bg', et_get_option( 'accent_color', '#2ea3f2' ) );
		$secondary_nav_dropdown_bg = et_get_option( 'secondary_nav_dropdown_bg', $secondary_nav_bg );
		$secondary_nav_dropdown_link_color = et_get_option( 'secondary_nav_dropdown_link_color', $secondary_nav_text_color_new );

		$fixed_primary_nav_font_size = absint( et_get_option( 'fixed_primary_nav_font_size', $primary_nav_font_size ) );
		$fixed_primary_nav_bg = et_get_option( 'fixed_primary_nav_bg', $primary_nav_bg );
		$fixed_secondary_nav_bg = et_get_option( 'fixed_secondary_nav_bg', $secondary_nav_bg );
		$fixed_menu_height = absint( et_get_option( 'minimized_menu_height', '40' ) );
		$fixed_menu_link = et_get_option( 'fixed_menu_link', $menu_link );
		$fixed_menu_link_active = et_get_option( 'fixed_menu_link_active', $menu_link_active );
		$fixed_secondary_menu_link = et_get_option( 'fixed_secondary_menu_link', $secondary_nav_text_color_new );

		$footer_bg = et_get_option( 'footer_bg', '#222222' );
		$footer_widget_link_color = et_get_option( 'footer_widget_link_color', '#fff' );
		$footer_widget_text_color = et_get_option( 'footer_widget_text_color', '#fff' );
		$footer_widget_header_color = et_get_option( 'footer_widget_header_color', $accent_color );
		$footer_widget_bullet_color = et_get_option( 'footer_widget_bullet_color', $accent_color );

		$widget_header_font_size = intval( et_get_option( 'widget_header_font_size', $body_header_size * .6 ) );
		$widget_body_font_size = absint( et_get_option( 'widget_body_font_size', $body_font_size ) );
		$widget_body_line_height = floatval( et_get_option( 'widget_body_line_height', '1.7' ) );

		$button_text_size = absint( et_get_option( 'all_buttons_font_size', '20' ) );
		$button_text_color = et_get_option( 'all_buttons_text_color', '#ffffff' );
		$button_bg_color = et_get_option( 'all_buttons_bg_color', 'rgba(0,0,0,0)' );
		$button_border_width = absint( et_get_option( 'all_buttons_border_width', '2' ) );
		$button_border_color = et_get_option( 'all_buttons_border_color', '#ffffff' );
		$button_border_radius = absint( et_get_option( 'all_buttons_border_radius', '3' ) );
		$button_text_style = et_get_option( 'all_buttons_font_style', '', '', true );
		$button_icon = et_get_option( 'all_buttons_selected_icon', '5' );
		$button_spacing = intval( et_get_option( 'all_buttons_spacing', '0' ) );
		$button_icon_color = et_get_option( 'all_buttons_icon_color', '#ffffff' );
		$button_text_color_hover = et_get_option( 'all_buttons_text_color_hover', '#ffffff' );
		$button_bg_color_hover = et_get_option( 'all_buttons_bg_color_hover', 'rgba(255,255,255,0.2)' );
		$button_border_color_hover = et_get_option( 'all_buttons_border_color_hover', 'rgba(0,0,0,0)' );
		$button_border_radius_hover = absint( et_get_option( 'all_buttons_border_radius_hover', '3' ) );
		$button_spacing_hover = intval( et_get_option( 'all_buttons_spacing_hover', '0' ) );
		$button_icon_size = 1.6 * intval( $button_text_size );

		$slide_nav_show_top_bar = et_get_option( 'slide_nav_show_top_bar', true );
		$slide_nav_bg = et_get_option( 'slide_nav_bg', $accent_color );
		$slide_nav_links_color = et_get_option( 'slide_nav_links_color', '#ffffff' );
		$slide_nav_links_color_active = et_get_option( 'slide_nav_links_color_active', '#ffffff' );
		$slide_nav_top_color = et_get_option( 'slide_nav_top_color', 'rgba(255,255,255,0.6)' );
		$slide_nav_search = et_get_option( 'slide_nav_search', 'rgba(255,255,255,0.6)' );
		$slide_nav_search_bg = et_get_option( 'slide_nav_search_bg', 'rgba(0,0,0,0.2)' );
		$slide_nav_width = intval( et_get_option( 'slide_nav_width', '320' ) );
		$slide_nav_font_style = et_get_option( 'slide_nav_font_style', '', '', true );
		$slide_nav_font_size = intval( et_get_option( 'slide_nav_font_size', '14' ) );
		$slide_nav_top_font_size = intval( et_get_option( 'slide_nav_top_font_size', '14' ) );
		$slide_nav_font_spacing = et_get_option( 'slide_nav_font_spacing', '0' );
		$fullscreen_nav_font_size = intval( et_get_option( 'fullscreen_nav_font_size', '30' ) );
		$fullscreen_nav_top_font_size = intval( et_get_option( 'fullscreen_nav_top_font_size', '18' ) );

		// use different selector for the styles applied directly to body tag while in Visual Builder. Otherwise unwanted styles applied to the Builder interface.
		$body_selector = empty( $_GET['et_fb'] ) ? 'body' : 'body .et_fb_preview_container';

		/* ====================================================
		 * --------->>> BEGIN THEME CUSTOMIZER CSS <<<---------
		 * ==================================================== */
		ob_start();

		if ( 14 !== $body_font_size ) { ?>
			@media only screen and ( min-width: 767px ) {
				<?php echo esc_html( $body_selector ); ?>, .et_pb_column_1_2 .et_quote_content blockquote cite, .et_pb_column_1_2 .et_link_content a.et_link_main_url, .et_pb_column_1_3 .et_quote_content blockquote cite, .et_pb_column_3_8 .et_quote_content blockquote cite, .et_pb_column_1_4 .et_quote_content blockquote cite, .et_pb_blog_grid .et_quote_content blockquote cite, .et_pb_column_1_3 .et_link_content a.et_link_main_url, .et_pb_column_3_8 .et_link_content a.et_link_main_url, .et_pb_column_1_4 .et_link_content a.et_link_main_url, .et_pb_blog_grid .et_link_content a.et_link_main_url, body .et_pb_bg_layout_light .et_pb_post p,  body .et_pb_bg_layout_dark .et_pb_post p { font-size: <?php echo esc_html( $body_font_size ); ?>px; }
				.et_pb_slide_content, .et_pb_best_value { font-size: <?php echo esc_html( intval( $body_font_size * 1.14 ) ); ?>px; }
			}
		<?php } ?>
		<?php if ( '#666666' !== $body_font_color) { ?>
			<?php echo esc_html( $body_selector ); ?> { color: <?php echo esc_html( $body_font_color ); ?>; }
		<?php } ?>
		<?php if ( '#666666' !== $body_header_color ) { ?>
				h1, h2, h3, h4, h5, h6 { color: <?php echo esc_html( $body_header_color ); ?>; }
			<?php } ?>
		<?php if ( 1.7 !== $body_font_height ) { ?>
			<?php echo esc_html( $body_selector ); ?> { line-height: <?php echo esc_html( $body_font_height ); ?>em; }
		<?php } ?>
		<?php if ( $accent_color !== '#2ea3f2' ) { ?>
			.woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button, .woocommerce-message, .woocommerce-error, .woocommerce-info { background: <?php echo esc_html( $accent_color ); ?> !important; }
			#et_search_icon:hover, .mobile_menu_bar:before, .mobile_menu_bar:after, .et_toggle_slide_menu:after, .et-social-icon a:hover, .et_pb_sum, .et_pb_pricing li a, .et_pb_pricing_table_button, .et_overlay:before, .entry-summary p.price ins, .woocommerce div.product span.price, .woocommerce-page div.product span.price, .woocommerce #content div.product span.price, .woocommerce-page #content div.product span.price, .woocommerce div.product p.price, .woocommerce-page div.product p.price, .woocommerce #content div.product p.price, .woocommerce-page #content div.product p.price, .et_pb_member_social_links a:hover, .woocommerce .star-rating span:before, .woocommerce-page .star-rating span:before, .et_pb_widget li a:hover, .et_pb_filterable_portfolio .et_pb_portfolio_filters li a.active, .et_pb_filterable_portfolio .et_pb_portofolio_pagination ul li a.active, .et_pb_gallery .et_pb_gallery_pagination ul li a.active, .wp-pagenavi span.current, .wp-pagenavi a:hover, .nav-single a, .posted_in a { color: <?php echo esc_html( $accent_color ); ?>; }
			.et_pb_contact_submit, .et_password_protected_form .et_submit_button, .et_pb_bg_layout_light .et_pb_newsletter_button, .comment-reply-link, .form-submit .et_pb_button, .et_pb_bg_layout_light .et_pb_promo_button, .et_pb_bg_layout_light .et_pb_more_button, .woocommerce a.button.alt, .woocommerce-page a.button.alt, .woocommerce button.button.alt, .woocommerce-page button.button.alt, .woocommerce input.button.alt, .woocommerce-page input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce-page #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page #content input.button.alt, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .et_pb_contact p input[type="checkbox"]:checked + label i:before { color: <?php echo esc_html( $accent_color ); ?>; }
			.footer-widget h4 { color: <?php echo esc_html( $accent_color ); ?>; }
			.et-search-form, .nav li ul, .et_mobile_menu, .footer-widget li:before, .et_pb_pricing li:before, blockquote { border-color: <?php echo esc_html( $accent_color ); ?>; }
			.et_pb_counter_amount, .et_pb_featured_table .et_pb_pricing_heading, .et_quote_content, .et_link_content, .et_audio_content, .et_pb_post_slider.et_pb_bg_layout_dark, .et_slide_in_menu_container, .et_pb_contact p input[type="radio"]:checked + label i:before { background-color: <?php echo esc_html( $accent_color ); ?>; }
		<?php } ?>
		<?php if ( 1080 !== $content_width ) { ?>
			.container, .et_pb_row, .et_pb_slider .et_pb_container, .et_pb_fullwidth_section .et_pb_title_container, .et_pb_fullwidth_section .et_pb_title_featured_container, .et_pb_fullwidth_header:not(.et_pb_fullscreen) .et_pb_fullwidth_header_container { max-width: <?php echo esc_html( $content_width ); ?>px; }
			.et_boxed_layout #page-container, .et_fixed_nav.et_boxed_layout #page-container #top-header, .et_fixed_nav.et_boxed_layout #page-container #main-header, .et_boxed_layout #page-container .container, .et_boxed_layout #page-container .et_pb_row { max-width: <?php echo esc_html( intval( et_get_option( 'content_width', '1080' ) ) + 160 ); ?>px; }
		<?php } ?>
		<?php if ( $link_color !== '#2ea3f2' ) { ?>
			a { color: <?php echo esc_html( $link_color ); ?>; }
		<?php } ?>
		<?php if ( $primary_nav_bg !== '#ffffff' ) { ?>
			#main-header, #main-header .nav li ul, .et-search-form, #main-header .et_mobile_menu { background-color: <?php echo esc_html( $primary_nav_bg ); ?>; }
		<?php } ?>
		<?php if ( $primary_nav_dropdown_bg !== $primary_nav_bg ) { ?>
			#main-header .nav li ul { background-color: <?php echo esc_html( $primary_nav_dropdown_bg ); ?>; }
		<?php } ?>
		<?php if ( $primary_nav_dropdown_line_color !== $accent_color ) { ?>
			.nav li ul { border-color: <?php echo esc_html( $primary_nav_dropdown_line_color ); ?>; }
		<?php } ?>
		<?php if ( $secondary_nav_bg !== '#2ea3f2' ) { ?>
			#top-header, #et-secondary-nav li ul { background-color: <?php echo esc_html( $secondary_nav_bg ); ?>; }
		<?php } ?>
		<?php if ( $secondary_nav_dropdown_bg !== $secondary_nav_bg ) { ?>
			#et-secondary-nav li ul { background-color: <?php echo esc_html( $secondary_nav_dropdown_bg ); ?>; }
		<?php } ?>
		<?php if ( $secondary_nav_text_color_new !== '#ffffff' ) { ?>
		#top-header, #top-header a { color: <?php echo esc_html( $secondary_nav_text_color_new ); ?>; }
		<?php } ?>
		<?php if ( $secondary_nav_dropdown_link_color !== $secondary_nav_text_color_new ) { ?>
			#et-secondary-nav li ul a { color: <?php echo esc_html( $secondary_nav_dropdown_link_color ); ?>; }
		<?php } ?>
		<?php if ( $menu_link !== 'rgba(0,0,0,0.6)' ) { ?>
			.et_header_style_centered .mobile_nav .select_page, .et_header_style_split .mobile_nav .select_page, .et_nav_text_color_light #top-menu > li > a, .et_nav_text_color_dark #top-menu > li > a, #top-menu a, .et_mobile_menu li a, .et_nav_text_color_light .et_mobile_menu li a, .et_nav_text_color_dark .et_mobile_menu li a, #et_search_icon:before, .et_search_form_container input, span.et_close_search_field:after, #et-top-navigation .et-cart-info { color: <?php echo esc_html( $menu_link ); ?>; }
			.et_search_form_container input::-moz-placeholder { color: <?php echo esc_html( $menu_link ); ?>; }
			.et_search_form_container input::-webkit-input-placeholder { color: <?php echo esc_html( $menu_link ); ?>; }
			.et_search_form_container input:-ms-input-placeholder { color: <?php echo esc_html( $menu_link ); ?>; }
		<?php } ?>
		<?php if ( $primary_nav_dropdown_link_color !== $menu_link ) { ?>
			#main-header .nav li ul a { color: <?php echo esc_html( $primary_nav_dropdown_link_color ); ?>; }
		<?php } ?>
		<?php if ( 12 !== $secondary_nav_font_size || '' !== $secondary_nav_font_style || 0 !== $secondary_nav_font_spacing ) { ?>
			#top-header, #top-header a, #et-secondary-nav li li a, #top-header .et-social-icon a:before {
				<?php if ( 12 !== $secondary_nav_font_size ) { ?>
					font-size: <?php echo esc_html( $secondary_nav_font_size ); ?>px;
				<?php } ?>
				<?php if ( '' !== $secondary_nav_font_style ) { ?>
					<?php echo esc_html( et_pb_print_font_style( $secondary_nav_font_style ) ); ?>
				<?php } ?>
				<?php if ( 0 !== $secondary_nav_font_spacing ) { ?>
					letter-spacing: <?php echo esc_html( $secondary_nav_font_spacing ); ?>px;
				<?php } ?>
			}
		<?php } ?>
		<?php if ( 14 !== $primary_nav_font_size ) { ?>
			#top-menu li a { font-size: <?php echo esc_html( $primary_nav_font_size ); ?>px; }
			body.et_vertical_nav .container.et_search_form_container .et-search-form input { font-size: <?php echo esc_html( $primary_nav_font_size ); ?>px !important; }
		<?php } ?>

		<?php if ( 0 !== $primary_nav_font_spacing || '' !== $primary_nav_font_style ) { ?>
			#top-menu li a, .et_search_form_container input {
				<?php if ( '' !== $primary_nav_font_style ) { ?>
					<?php echo esc_html( et_pb_print_font_style( $primary_nav_font_style ) ); ?>
				<?php } ?>
				<?php if ( 0 !== $primary_nav_font_spacing ) { ?>
					letter-spacing: <?php echo esc_html( $primary_nav_font_spacing ); ?>px;
				<?php } ?>
			}

			.et_search_form_container input::-moz-placeholder {
				<?php if ( '' !== $primary_nav_font_style ) { ?>
					<?php echo esc_html( et_pb_print_font_style( $primary_nav_font_style ) ); ?>
				<?php } ?>
				<?php if ( 0 !== $primary_nav_font_spacing ) { ?>
					letter-spacing: <?php echo esc_html( $primary_nav_font_spacing ); ?>px;
				<?php } ?>
			}
			.et_search_form_container input::-webkit-input-placeholder {
				<?php if ( '' !== $primary_nav_font_style ) { ?>
					<?php echo esc_html( et_pb_print_font_style( $primary_nav_font_style ) ); ?>
				<?php } ?>
				<?php if ( 0 !== $primary_nav_font_spacing ) { ?>
					letter-spacing: <?php echo esc_html( $primary_nav_font_spacing ); ?>px;
				<?php } ?>
			}
			.et_search_form_container input:-ms-input-placeholder {
				<?php if ( '' !== $primary_nav_font_style ) { ?>
					<?php echo esc_html( et_pb_print_font_style( $primary_nav_font_style ) ); ?>
				<?php } ?>
				<?php if ( 0 !== $primary_nav_font_spacing ) { ?>
					letter-spacing: <?php echo esc_html( $primary_nav_font_spacing ); ?>px;
				<?php } ?>
			}
		<?php } ?>

		<?php if ( $menu_link_active !== '#2ea3f2' ) { ?>
			#top-menu li.current-menu-ancestor > a, #top-menu li.current-menu-item > a,
			.et_color_scheme_red #top-menu li.current-menu-ancestor > a, .et_color_scheme_red #top-menu li.current-menu-item > a,
			.et_color_scheme_pink #top-menu li.current-menu-ancestor > a, .et_color_scheme_pink #top-menu li.current-menu-item > a,
			.et_color_scheme_orange #top-menu li.current-menu-ancestor > a, .et_color_scheme_orange #top-menu li.current-menu-item > a,
			.et_color_scheme_green #top-menu li.current-menu-ancestor > a, .et_color_scheme_green #top-menu li.current-menu-item > a { color: <?php echo esc_html( $menu_link_active ); ?>; }
		<?php } ?>
		<?php if ( $footer_bg !== '#222222' ) { ?>
			#main-footer { background-color: <?php echo esc_html( $footer_bg ); ?>; }
		<?php } ?>
		<?php if ( $footer_widget_link_color !== '#fff' ) { ?>
			#footer-widgets .footer-widget a,
			#footer-widgets .footer-widget li a,
			#footer-widgets .footer-widget li a:hover { color: <?php echo esc_html( $footer_widget_link_color ); ?>; }
		<?php } ?>
		<?php if ( $footer_widget_text_color !== '#fff' ) { ?>
			.footer-widget { color: <?php echo esc_html( $footer_widget_text_color ); ?>; }
		<?php } ?>
		<?php if ( $footer_widget_header_color !== '#2ea3f2' ) { ?>
			#main-footer .footer-widget h4 { color: <?php echo esc_html( $footer_widget_header_color ); ?>; }
		<?php } ?>
		<?php if ( $footer_widget_bullet_color !== '#2ea3f2' ) { ?>
			.footer-widget li:before { border-color: <?php echo esc_html( $footer_widget_bullet_color ); ?>; }
		<?php } ?>
		<?php if ( $body_font_size !== $widget_body_font_size ) { ?>
			.footer-widget, .footer-widget li, .footer-widget li a, #footer-info { font-size: <?php echo esc_html( $widget_body_font_size ); ?>px; }
		<?php } ?>
		<?php
			/* Widget */
			et_pb_print_styles_css( array(
				array(
					'key' 		=> 'widget_header_font_style',
					'type' 		=> 'font-style',
					'default' 	=> '',
					'selector' 	=> '.footer-widget h4',
				),
				array(
					'key' 		=> 'widget_body_font_style',
					'type' 		=> 'font-style',
					'default' 	=> '',
					'selector' 	=> '.footer-widget .et_pb_widget div, .footer-widget .et_pb_widget ul, .footer-widget .et_pb_widget ol, .footer-widget .et_pb_widget label',
				),
				array(
					'key' 		=> 'widget_body_line_height',
					'type' 		=> 'line-height',
					'default' 	=> '',
					'selector' 	=> '.footer-widget .et_pb_widget div, .footer-widget .et_pb_widget ul, .footer-widget .et_pb_widget ol, .footer-widget .et_pb_widget label',
				),
			) );

			/* Footer widget bullet fix */
			if ( 1.7 !==  $widget_body_line_height || 14 !== $widget_body_font_size ) {
				// line_height (em) * font_size (px) = line height in px
				$widget_body_line_height_px 		= floatval( $widget_body_line_height ) * intval( $widget_body_font_size );

				// ( line height in px / 2 ) - half of bullet diameter
				$footer_widget_bullet_top 			= ( $widget_body_line_height_px / 2 ) - 3;

				printf( "#footer-widgets .footer-widget li:before { top: %spx; }", esc_html( $footer_widget_bullet_top ) );
			}

			/* Footer Menu */
			et_pb_print_styles_css( array(
				array(
					'key' 		=> 'footer_menu_background_color',
					'type' 		=> 'background-color',
					'default' 	=> 'rgba(255,255,255,0.05)',
					'selector' 	=> '#et-footer-nav'
				),
				array(
					'key' 		=> 'footer_menu_text_color',
					'type' 		=> 'color',
					'default' 	=> '#bbbbbb',
					'selector' 	=> '.bottom-nav, .bottom-nav a, .bottom-nav li.current-menu-item a'
				),
				array(
					'key' 		=> 'footer_menu_active_link_color',
					'type' 		=> 'color',
					'default' 	=> '#bbbbbb',
					'selector' 	=> '#et-footer-nav .bottom-nav li.current-menu-item a'
				),
				array(
					'key' 		=> 'footer_menu_letter_spacing',
					'type' 		=> 'letter-spacing',
					'default' 	=> 0,
					'selector' 	=> '.bottom-nav'
				),
				array(
					'key' 		=> 'footer_menu_font_style',
					'type' 		=> 'font-style',
					'default' 	=> '',
					'selector' 	=> '.bottom-nav a'
				),
				array(
					'key' 		=> 'footer_menu_font_size',
					'type' 		=> 'font-size',
					'default' 	=> 14,
					'selector' 	=> '.bottom-nav, .bottom-nav a'
				),
			) );

			/* Bottom Bar */
			et_pb_print_styles_css( array(
				array(
					'key' 		=> 'bottom_bar_background_color',
					'type' 		=> 'background-color',
					'default' 	=> 'rgba(0,0,0,0.32)',
					'selector' 	=> '#footer-bottom'
				),
				array(
					'key' 		=> 'bottom_bar_text_color',
					'type' 		=> 'color',
					'default' 	=> '#666666',
					'selector' 	=> '#footer-info, #footer-info a'
				),
				array(
					'key' 		=> 'bottom_bar_font_style',
					'type' 		=> 'font-style',
					'default' 	=> '',
					'selector' 	=> '#footer-info, #footer-info a'
				),
				array(
					'key' 		=> 'bottom_bar_font_size',
					'type' 		=> 'font-size',
					'default' 	=> 14,
					'selector' 	=> '#footer-info'
				),
				array(
					'key' 		=> 'bottom_bar_social_icon_size',
					'type' 		=> 'font-size',
					'default' 	=> 24,
					'selector' 	=> '#footer-bottom .et-social-icon a'
				),
				array(
					'key' 		=> 'bottom_bar_social_icon_color',
					'type' 		=> 'color',
					'default' 	=> '#666666',
					'selector' 	=> '#footer-bottom .et-social-icon a'
				),
			) );
		?>
		<?php if ( 'rgba' === substr( $primary_nav_bg, 0, 4 ) ) { ?>
			#main-header { box-shadow: none; }
		<?php } ?>
		<?php if ( 'rgba' === substr( $fixed_primary_nav_bg, 0, 4 ) || ( 'rgba' === substr( $primary_nav_bg, 0, 4 ) && '#ffffff' === $fixed_primary_nav_bg ) ) { ?>
			.et-fixed-header#main-header { box-shadow: none !important; }
		<?php } ?>
		<?php if ( 20 !== $button_text_size || '#ffffff' !== $button_text_color || 'rgba(0,0,0,0)' !== $button_bg_color || 2 !== $button_border_width || '#ffffff' !== $button_border_color || 3 !== $button_border_radius || '' !== $button_text_style || 0 !== $button_spacing ) { ?>
			body .et_pb_button,
			.woocommerce a.button.alt, .woocommerce-page a.button.alt, .woocommerce button.button.alt, .woocommerce-page button.button.alt, .woocommerce input.button.alt, .woocommerce-page input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce-page #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page #content input.button.alt,
			.woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button, .woocommerce-message a.button.wc-forward
			{
				<?php if ( 20 !== $button_text_size ) { ?>
					 font-size: <?php echo esc_html( $button_text_size ); ?>px;
				<?php } ?>
				<?php if ( 'rgba(0,0,0,0)' !== $button_bg_color ) { ?>
					background: <?php echo esc_html( $button_bg_color ); ?>;
				<?php } ?>
				<?php if ( 2 !== $button_border_width ) { ?>
					border-width: <?php echo esc_html( $button_border_width ); ?>px !important;
				<?php } ?>
				<?php if ( '#ffffff' !== $button_border_color ) { ?>
					border-color: <?php echo esc_html( $button_border_color ); ?>;
				<?php } ?>
				<?php if ( 3 !== $button_border_radius ) { ?>
					border-radius: <?php echo esc_html( $button_border_radius ); ?>px;
				<?php } ?>
				<?php if ( '' !== $button_text_style ) { ?>
					<?php echo esc_html( et_pb_print_font_style( $button_text_style ) ); ?>;
				<?php } ?>
				<?php if ( 0 !== $button_spacing ) { ?>
					letter-spacing: <?php echo esc_html( $button_spacing ); ?>px;
				<?php } ?>
			}
			body.et_pb_button_helper_class .et_pb_button,
			.woocommerce.et_pb_button_helper_class a.button.alt, .woocommerce-page.et_pb_button_helper_class a.button.alt, .woocommerce.et_pb_button_helper_class button.button.alt, .woocommerce-page.et_pb_button_helper_class button.button.alt, .woocommerce.et_pb_button_helper_class input.button.alt, .woocommerce-page.et_pb_button_helper_class input.button.alt, .woocommerce.et_pb_button_helper_class #respond input#submit.alt, .woocommerce-page.et_pb_button_helper_class #respond input#submit.alt, .woocommerce.et_pb_button_helper_class #content input.button.alt, .woocommerce-page.et_pb_button_helper_class #content input.button.alt,
			.woocommerce.et_pb_button_helper_class a.button, .woocommerce-page.et_pb_button_helper_class a.button, .woocommerce.et_pb_button_helper_class button.button, .woocommerce-page.et_pb_button_helper_class button.button, .woocommerce.et_pb_button_helper_class input.button, .woocommerce-page.et_pb_button_helper_class input.button, .woocommerce.et_pb_button_helper_class #respond input#submit, .woocommerce-page.et_pb_button_helper_class #respond input#submit, .woocommerce.et_pb_button_helper_class #content input.button, .woocommerce-page.et_pb_button_helper_class #content input.button {
				<?php if ( '#ffffff' !== $button_text_color ) { ?>
					color: <?php echo esc_html( $button_text_color ); ?> !important;
				<?php } ?>
			}
		<?php } ?>
		<?php if ( '5' !== $button_icon || '#ffffff' !== $button_icon_color || 20 !== $button_text_size ) { ?>
			body .et_pb_button:after,
			.woocommerce a.button.alt:after, .woocommerce-page a.button.alt:after, .woocommerce button.button.alt:after, .woocommerce-page button.button.alt:after, .woocommerce input.button.alt:after, .woocommerce-page input.button.alt:after, .woocommerce #respond input#submit.alt:after, .woocommerce-page #respond input#submit.alt:after, .woocommerce #content input.button.alt:after, .woocommerce-page #content input.button.alt:after,
			.woocommerce a.button:after, .woocommerce-page a.button:after, .woocommerce button.button:after, .woocommerce-page button.button:after, .woocommerce input.button:after, .woocommerce-page input.button:after, .woocommerce #respond input#submit:after, .woocommerce-page #respond input#submit:after, .woocommerce #content input.button:after, .woocommerce-page #content input.button:after
			{
				<?php if ( '5' !== $button_icon ) { ?>
					<?php if ( "'" === $button_icon ) { ?>
						content: "<?php echo htmlspecialchars_decode( $button_icon ); ?>";
					<?php } else { ?>
						content: '<?php echo htmlspecialchars_decode( $button_icon ); ?>';
					<?php } ?>
					font-size: <?php echo esc_html( $button_text_size ); ?>px;
				<?php } else { ?>
					font-size: <?php echo esc_html( $button_icon_size ); ?>px;
				<?php } ?>
				<?php if ( '#ffffff' !== $button_icon_color ) { ?>
					color: <?php echo esc_html( $button_icon_color ); ?>;
				<?php } ?>
			}
		<?php } ?>
		<?php if ( '#ffffff' !== $button_text_color_hover || 'rgba(255,255,255,0.2)' !== $button_bg_color_hover || 'rgba(0,0,0,0)' !== $button_border_color_hover || 3 !== $button_border_radius_hover || 0 !== $button_spacing_hover ) { ?>
			body .et_pb_button:hover,
			.woocommerce a.button.alt:hover, .woocommerce-page a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce-page button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce-page input.button.alt:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce-page #respond input#submit.alt:hover, .woocommerce #content input.button.alt:hover, .woocommerce-page #content input.button.alt:hover,
			.woocommerce a.button:hover, .woocommerce-page a.button:hover, .woocommerce button.button:hover, .woocommerce-page button.button:hover, .woocommerce input.button:hover, .woocommerce-page input.button:hover, .woocommerce #respond input#submit:hover, .woocommerce-page #respond input#submit:hover, .woocommerce #content input.button:hover, .woocommerce-page #content input.button:hover
			{
				<?php if ( '#ffffff' !== $button_text_color_hover ) { ?>
					 color: <?php echo esc_html( $button_text_color_hover ); ?> !important;
				<?php } ?>
				<?php if ( 'rgba(255,255,255,0.2)' !== $button_bg_color_hover ) { ?>
					background: <?php echo esc_html( $button_bg_color_hover ); ?> !important;
				<?php } ?>
				<?php if ( 'rgba(0,0,0,0)' !== $button_border_color_hover ) { ?>
					border-color: <?php echo esc_html( $button_border_color_hover ); ?> !important;
				<?php } ?>
				<?php if ( 3 !== $button_border_radius_hover ) { ?>
					border-radius: <?php echo esc_html( $button_border_radius_hover ); ?>px;
				<?php } ?>
				<?php if ( 0 !== $button_spacing_hover ) { ?>
					letter-spacing: <?php echo esc_html( $button_spacing_hover ); ?>px;
				<?php } ?>
			}
		<?php } ?>

		<?php if ( '' !== $body_header_style || 0 !== $body_header_spacing || 1.0 !== $body_header_height) { ?>
				h1, h2, h3, h4, h5, h6, .et_quote_content blockquote p, .et_pb_slide_description .et_pb_slide_title {
					<?php if ( $body_header_style !== '' ) { ?>
						<?php echo esc_html( et_pb_print_font_style( $body_header_style ) ); ?>
					<?php } ?>
					<?php if ( 0 !== $body_header_spacing ) { ?>
						letter-spacing: <?php echo esc_html( $body_header_spacing ); ?>px;
					<?php } ?>

					<?php if ( 1.0 !== $body_header_height ) { ?>
						line-height: <?php echo esc_html( $body_header_height ); ?>em;
					<?php } ?>
				}
		<?php } ?>

		<?php
			/* Blog Meta */
			$et_pb_print_selectors_post_meta = "body.home-posts #left-area .et_pb_post .post-meta, body.archive #left-area .et_pb_post .post-meta, body.search #left-area .et_pb_post .post-meta, body.single #left-area .et_pb_post .post-meta";

			et_pb_print_styles_css( array(
				array(
					'key'      => 'post_meta_height',
					'type'     => 'line-height',
					'default'  => 1,
					'selector' => $et_pb_print_selectors_post_meta,
				),
				array(
					'key'      => 'post_meta_spacing',
					'type'     => 'letter-spacing',
					'default'  => 0,
					'selector' => $et_pb_print_selectors_post_meta,
				),
				array(
					'key'      => 'post_meta_style',
					'type'     => 'font-style',
					'default'  => '',
					'selector' => $et_pb_print_selectors_post_meta,
				),
			) );

			/* Blog Title */
			$et_pb_print_selectors_post_header = "body.home-posts #left-area .et_pb_post h2, body.archive #left-area .et_pb_post h2, body.search #left-area .et_pb_post h2, body.single .et_post_meta_wrapper h1";

			et_pb_print_styles_css( array(
				array(
					'key'      => 'post_header_height',
					'type'     => 'line-height',
					'default'  => 1,
					'selector' => $et_pb_print_selectors_post_header,
				),
				array(
					'key'      => 'post_header_spacing',
					'type'     => 'letter-spacing',
					'default'  => 0,
					'selector' => $et_pb_print_selectors_post_header,
				),
				array(
					'key'      => 'post_header_style',
					'type'     => 'font-style',
					'default'  => '',
					'selector' => $et_pb_print_selectors_post_header,
				),
			) );
		?>
		<?php if ( ! $slide_nav_show_top_bar ) { ?>
			.et_slide_menu_top { display: none; }
		<?php } ?>
		<?php if ( $accent_color !== $slide_nav_bg ) { ?>
			body #page-container .et_slide_in_menu_container { background: <?php echo esc_html( $slide_nav_bg ); ?>; }
		<?php } ?>
		<?php if ( '#ffffff' !== $slide_nav_links_color ) { ?>
			.et_slide_in_menu_container #mobile_menu_slide li span.et_mobile_menu_arrow:before, .et_slide_in_menu_container #mobile_menu_slide li a { color: <?php echo esc_html( $slide_nav_links_color ); ?>; }
		<?php } ?>
		<?php if ( '#ffffff' !== $slide_nav_links_color_active ) { ?>
			.et_slide_in_menu_container #mobile_menu_slide li.current-menu-item span.et_mobile_menu_arrow:before, .et_slide_in_menu_container #mobile_menu_slide li.current-menu-item a { color: <?php echo esc_html( $slide_nav_links_color_active ); ?>; }
		<?php } ?>
		<?php if ( 'rgba(255,255,255,0.6)' !== $slide_nav_top_color ) { ?>
			.et_slide_in_menu_container .et_slide_menu_top, .et_slide_in_menu_container .et_slide_menu_top a, .et_slide_in_menu_container .et_slide_menu_top input { color: <?php echo esc_html( $slide_nav_top_color ); ?>; }
			.et_slide_in_menu_container .et_slide_menu_top .et-search-form input, .et_slide_in_menu_container .et_slide_menu_top .et-search-form button#searchsubmit_header:before { color: <?php echo esc_html( $slide_nav_top_color ); ?>; }
			.et_slide_in_menu_container .et_slide_menu_top .et-search-form input::-webkit-input-placeholder { color: <?php echo esc_html( $slide_nav_top_color ); ?>; }
			.et_slide_in_menu_container .et_slide_menu_top .et-search-form input::-moz-placeholder { color: <?php echo esc_html( $slide_nav_top_color ); ?>; }
			.et_slide_in_menu_container .et_slide_menu_top .et-search-form input:-ms-input-placeholder { color: <?php echo esc_html( $slide_nav_top_color ); ?>; }
			.et_header_style_fullscreen .et_slide_in_menu_container span.mobile_menu_bar.et_toggle_fullscreen_menu:before { color: <?php echo esc_html( $slide_nav_top_color ); ?>; }
			.et_header_style_fullscreen .et_slide_menu_top .et-search-form { border-color: <?php echo esc_html( $slide_nav_top_color ); ?>; }
		<?php } ?>
		<?php if ( 'rgba(255,255,255,0.6)' !== $slide_nav_search ) { ?>
			.et_header_style_slide .et_slide_in_menu_container .et_slide_menu_top .et-search-form input,.et_header_style_slide .et_slide_in_menu_container .et_slide_menu_top .et-search-form button#searchsubmit_header:before { color: <?php echo esc_html( $slide_nav_search ); ?>; }
			.et_header_style_slide .et_slide_in_menu_container .et_slide_menu_top .et-search-form input::-webkit-input-placeholder { color: <?php echo esc_html( $slide_nav_search ); ?>; }
			.et_header_style_slide .et_slide_in_menu_container .et_slide_menu_top .et-search-form input::-moz-placeholder { color: <?php echo esc_html( $slide_nav_search ); ?>; }
			.et_header_style_slide .et_slide_in_menu_container .et_slide_menu_top .et-search-form input:-ms-input-placeholder { color: <?php echo esc_html( $slide_nav_search ); ?>; }
		<?php } ?>
		<?php if ( 'rgba(0,0,0,0.2)' !== $slide_nav_search_bg ) { ?>
			.et_header_style_slide .et_slide_in_menu_container .et_slide_menu_top .et-search-form { background: <?php echo esc_html( $slide_nav_search_bg ); ?> !important; }
		<?php } ?>
		<?php if ( 320 !== $slide_nav_width ) { ?>
			.et_header_style_slide .et_slide_in_menu_container { width: <?php echo esc_html( $slide_nav_width ); ?>px; }
		<?php } ?>
		<?php if ( '' !== $slide_nav_font_style ) { ?>
			.et_slide_in_menu_container, .et_slide_in_menu_container .et-search-field, .et_slide_in_menu_container a, .et_slide_in_menu_container #et-info span { <?php echo esc_html( et_pb_print_font_style( $slide_nav_font_style ) ); ?> }
		<?php } ?>
		<?php if ( 14 !== $slide_nav_font_size ) { ?>
			.et_header_style_slide .et_slide_in_menu_container .et_mobile_menu li a { font-size: <?php echo esc_html( $slide_nav_font_size ); ?>px; }
		<?php } ?>
		<?php if ( 14 !== $slide_nav_top_font_size ) { ?>
			.et_header_style_slide .et_slide_in_menu_container,.et_header_style_slide .et_slide_in_menu_container input.et-search-field,.et_header_style_slide .et_slide_in_menu_container a,.et_header_style_slide .et_slide_in_menu_container #et-info span,.et_header_style_slide .et_slide_menu_top ul.et-social-icons a,.et_header_style_slide .et_slide_menu_top span { font-size: <?php echo esc_html( $slide_nav_top_font_size ); ?>px; }
			.et_header_style_slide .et_slide_in_menu_container .et-search-field::-moz-placeholder { font-size: <?php echo esc_html( $slide_nav_top_font_size ); ?>px; }
			.et_header_style_slide .et_slide_in_menu_container .et-search-field::-webkit-input-placeholder { font-size: <?php echo esc_html( $slide_nav_top_font_size ); ?>px; }
			.et_header_style_slide .et_slide_in_menu_container .et-search-field:-ms-input-placeholder { font-size: <?php echo esc_html( $slide_nav_top_font_size ); ?>px; }
		<?php } ?>
		<?php if ( 30 !== $fullscreen_nav_font_size ) { ?>
			.et_header_style_fullscreen .et_slide_in_menu_container .et_mobile_menu li a { font-size: <?php echo esc_html( $fullscreen_nav_font_size ); ?>px; }
			.et_slide_in_menu_container #mobile_menu_slide li.current-menu-item a, .et_slide_in_menu_container #mobile_menu_slide li a { padding: <?php echo esc_html( $fullscreen_nav_font_size / 2 ); ?>px 0; }
		<?php } ?>
		<?php if ( 18 !== $fullscreen_nav_top_font_size ) { ?>
			.et_header_style_fullscreen .et_slide_in_menu_container,.et_header_style_fullscreen .et_slide_in_menu_container input.et-search-field,.et_header_style_fullscreen .et_slide_in_menu_container a,.et_header_style_fullscreen .et_slide_in_menu_container #et-info span,.et_header_style_fullscreen .et_slide_menu_top ul.et-social-icons a,.et_header_style_fullscreen .et_slide_menu_top span { font-size: <?php echo esc_html( $fullscreen_nav_top_font_size ); ?>px; }
			.et_header_style_fullscreen .et_slide_in_menu_container .et-search-field::-moz-placeholder { font-size: <?php echo esc_html( $fullscreen_nav_top_font_size ); ?>px; }
			.et_header_style_fullscreen .et_slide_in_menu_container .et-search-field::-webkit-input-placeholder { font-size: <?php echo esc_html( $fullscreen_nav_top_font_size ); ?>px; }
			.et_header_style_fullscreen .et_slide_in_menu_container .et-search-field:-ms-input-placeholder { font-size: <?php echo esc_html( $fullscreen_nav_top_font_size ); ?>px; }
		<?php } ?>
		<?php if ( '0' !== $slide_nav_font_spacing ) { ?>
			.et_slide_in_menu_container, .et_slide_in_menu_container .et-search-field { letter-spacing: <?php echo esc_html( $slide_nav_font_spacing ); ?>px; }
			.et_slide_in_menu_container .et-search-field::-moz-placeholder { letter-spacing: <?php echo esc_html( $slide_nav_font_spacing ); ?>px; }
			.et_slide_in_menu_container .et-search-field::-webkit-input-placeholder { letter-spacing: <?php echo esc_html( $slide_nav_font_spacing ); ?>px; }
			.et_slide_in_menu_container .et-search-field:-ms-input-placeholder { letter-spacing: <?php echo esc_html( $slide_nav_font_spacing ); ?>px; }
		<?php } ?>

		@media only screen and ( min-width: 981px ) {
			<?php
				// output the styles below only if not inside the Frontend Builder
				if ( empty( $_GET['et_fb'] ) ) { ?>
				<?php if ( 4 !== $section_padding ) { ?>
					.et_pb_section { padding: <?php echo esc_html( $section_padding ); ?>% 0; }
					.et_pb_section.et_pb_section_first { padding-top: inherit; }
					.et_pb_fullwidth_section { padding: 0; }
				<?php } ?>
				<?php if ( 2 !== $row_padding ) { ?>
					.et_pb_row { padding: <?php echo esc_html( $row_padding ); ?>% 0; }
				<?php } ?>
				<?php if ( 30 !== $body_header_size ) { ?>
					h1 { font-size: <?php echo esc_html( $body_header_size ); ?>px; }
					h2, .product .related h2, .et_pb_column_1_2 .et_quote_content blockquote p { font-size: <?php echo esc_html( intval( $body_header_size * .86 ) ) ; ?>px; }
					h3 { font-size: <?php echo esc_html( intval( $body_header_size * .73 ) ); ?>px; }
					h4, .et_pb_circle_counter h3, .et_pb_number_counter h3, .et_pb_column_1_3 .et_pb_post h2, .et_pb_column_1_4 .et_pb_post h2, .et_pb_blog_grid h2, .et_pb_column_1_3 .et_quote_content blockquote p, .et_pb_column_3_8 .et_quote_content blockquote p, .et_pb_column_1_4 .et_quote_content blockquote p, .et_pb_blog_grid .et_quote_content blockquote p, .et_pb_column_1_3 .et_link_content h2, .et_pb_column_3_8 .et_link_content h2, .et_pb_column_1_4 .et_link_content h2, .et_pb_blog_grid .et_link_content h2, .et_pb_column_1_3 .et_audio_content h2, .et_pb_column_3_8 .et_audio_content h2, .et_pb_column_1_4 .et_audio_content h2, .et_pb_blog_grid .et_audio_content h2, .et_pb_column_3_8 .et_pb_audio_module_content h2, .et_pb_column_1_3 .et_pb_audio_module_content h2, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2 { font-size: <?php echo esc_html( intval( $body_header_size * .6 ) ); ?>px; }
					h5 { font-size: <?php echo esc_html( intval( $body_header_size * .53 ) ); ?>px; }
					h6 { font-size: <?php echo esc_html( intval( $body_header_size * .47 ) ); ?>px; }
					.et_pb_slide_description .et_pb_slide_title { font-size: <?php echo esc_html( intval( $body_header_size * 1.53 ) ); ?>px; }
					.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_pb_column_1_4 .et_pb_audio_module_content h2 { font-size: <?php echo esc_html( intval( $body_header_size * .53 ) ); ?>px; }
				<?php } ?>
			<?php } ?>
			<?php if ( intval( $body_header_size * .6 ) !== $widget_header_font_size ) { ?>
				.footer-widget h4 { font-size: <?php echo esc_html( $widget_header_font_size ); ?>px; }
			<?php } ?>
			<?php if ( 66 !== $menu_height ) { ?>
				.et_header_style_left #et-top-navigation, .et_header_style_split #et-top-navigation  { padding: <?php echo esc_html( round( $menu_height / 2 ) ); ?>px 0 0 0; }
				.et_header_style_left #et-top-navigation nav > ul > li > a, .et_header_style_split #et-top-navigation nav > ul > li > a { padding-bottom: <?php echo esc_html( round ( $menu_height / 2 ) ); ?>px; }
				.et_header_style_split .centered-inline-logo-wrap { width: <?php echo esc_html( $menu_height ); ?>px; margin: -<?php echo esc_html( $menu_height ); ?>px 0; }
				.et_header_style_split .centered-inline-logo-wrap #logo { max-height: <?php echo esc_html( $menu_height ); ?>px; }
				.et_pb_svg_logo.et_header_style_split .centered-inline-logo-wrap #logo { height: <?php echo esc_html( $menu_height ); ?>px; }
				.et_header_style_centered #top-menu > li > a { padding-bottom: <?php echo esc_html( round ( $menu_height * .18 ) ); ?>px; }
				.et_header_style_slide #et-top-navigation, .et_header_style_fullscreen #et-top-navigation { padding: <?php echo esc_html( round( ( $menu_height - 18 ) / 2 ) ); ?>px 0 <?php echo esc_html( round( ( $menu_height - 18 ) / 2 ) ); ?>px 0 !important; }
				<?php if ( ! $vertical_nav ) { ?>
					.et_header_style_centered #main-header .logo_container { height: <?php echo esc_html( $menu_height ); ?>px; }
				<?php } ?>
			<?php } ?>
			<?php if ( 54 !== $logo_height && in_array( $header_style, array( 'left', 'slide', 'fullscreen' ) ) ) { ?>
				#logo { max-height: <?php echo esc_html( $logo_height . '%' ); ?>; }
				.et_pb_svg_logo #logo { height: <?php echo esc_html( $logo_height . '%' ); ?>; }
			<?php } ?>
			<?php if ( 64 !== $logo_height && 'centered' === $header_style ) { ?>
				.et_header_style_centered #logo { max-height: <?php echo esc_html( $logo_height . '%' ); ?>; }
				.et_pb_svg_logo.et_header_style_centered #logo { height: <?php echo esc_html( $logo_height . '%' ); ?>; }
			<?php } ?>
			<?php if ( $vertical_nav && et_get_option( 'logo_height' ) ) { ?>
				#main-header .logo_container { width: <?php echo esc_html( $logo_height . '%' ); ?>; }
				.et_header_style_centered #main-header .logo_container,
				.et_header_style_split #main-header .logo_container { margin: 0 auto; }
			<?php } ?>
			<?php if ( $vertical_nav && 0 !== $menu_margin_top ) { ?>
				.et_vertical_nav #et-top-navigation { margin-top: <?php echo esc_html( $menu_margin_top . 'px' ); ?>;}
			<?php } ?>
			<?php if ( 'false' !== $hide_primary_logo || 'false' !== $hide_fixed_logo ) { ?>
				.et_header_style_centered.et_hide_primary_logo #main-header:not(.et-fixed-header) .logo_container, .et_header_style_centered.et_hide_fixed_logo #main-header.et-fixed-header .logo_container { height: <?php echo esc_html( $menu_height * .18 ); ?>px; }
			<?php } ?>
			<?php if ( 40 !== $fixed_menu_height ) { ?>
				.et_header_style_left .et-fixed-header #et-top-navigation, .et_header_style_split .et-fixed-header #et-top-navigation { padding: <?php echo esc_html( intval( round( $fixed_menu_height / 2 ) ) ); ?>px 0 0 0; }
				.et_header_style_left .et-fixed-header #et-top-navigation nav > ul > li > a, .et_header_style_split .et-fixed-header #et-top-navigation nav > ul > li > a  { padding-bottom: <?php echo esc_html( round( $fixed_menu_height / 2 ) ); ?>px; }
				.et_header_style_centered header#main-header.et-fixed-header .logo_container { height: <?php echo esc_html( $fixed_menu_height ); ?>px; }
				.et_header_style_split .et-fixed-header .centered-inline-logo-wrap { width: <?php echo esc_html( $fixed_menu_height ); ?>px; margin: -<?php echo esc_html( $fixed_menu_height ); ?>px 0;  }
				.et_header_style_split .et-fixed-header .centered-inline-logo-wrap #logo { max-height: <?php echo esc_html( $fixed_menu_height ); ?>px; }
				.et_pb_svg_logo.et_header_style_split .et-fixed-header .centered-inline-logo-wrap #logo { height: <?php echo esc_html( $fixed_menu_height ); ?>px; }
				.et_header_style_slide .et-fixed-header #et-top-navigation, .et_header_style_fullscreen .et-fixed-header #et-top-navigation { padding: <?php echo esc_html( round( ( $fixed_menu_height - 18 ) / 2 ) ); ?>px 0 <?php echo esc_html( round( ( $fixed_menu_height - 18 ) / 2 ) ); ?>px 0 !important; }
			<?php } ?>
			<?php if ( 54 !== $logo_height && 'split' === $header_style ) { ?>
				.et_header_style_split .centered-inline-logo-wrap { width: auto; height: <?php echo esc_html( ( ( intval( $menu_height ) / 100 ) * $logo_height ) + 14 ); ?>px; }
				.et_header_style_split .et-fixed-header .centered-inline-logo-wrap { width: auto; height: <?php echo esc_html( ( ( intval( $fixed_menu_height ) / 100 ) * $logo_height ) + 14 ); ?>px; }
				.et_header_style_split .centered-inline-logo-wrap #logo,
				.et_header_style_split .et-fixed-header .centered-inline-logo-wrap #logo { height: auto; max-height: 100%; }

			<?php } ?>
			<?php if ( $fixed_secondary_nav_bg !== '#2ea3f2' ) { ?>
				.et-fixed-header#top-header, .et-fixed-header#top-header #et-secondary-nav li ul { background-color: <?php echo esc_html( $fixed_secondary_nav_bg ); ?>; }
			<?php } ?>
			<?php if ( $fixed_primary_nav_bg !== $primary_nav_bg ) { ?>
				.et-fixed-header#main-header, .et-fixed-header#main-header .nav li ul, .et-fixed-header .et-search-form { background-color: <?php echo esc_html( $fixed_primary_nav_bg ); ?>; }
			<?php } ?>
			<?php if ( 14 !== $fixed_primary_nav_font_size ) { ?>
				.et-fixed-header #top-menu li a { font-size: <?php echo esc_html( $fixed_primary_nav_font_size ); ?>px; }
			<?php } ?>
			<?php if ( $fixed_menu_link !== 'rgba(0,0,0,0.6)' ) { ?>
				.et-fixed-header #top-menu a, .et-fixed-header #et_search_icon:before, .et-fixed-header #et_top_search .et-search-form input, .et-fixed-header .et_search_form_container input, .et-fixed-header .et_close_search_field:after, .et-fixed-header #et-top-navigation .et-cart-info { color: <?php echo esc_html( $fixed_menu_link ); ?> !important; }
				.et-fixed-header .et_search_form_container input::-moz-placeholder { color: <?php echo esc_html( $fixed_menu_link ); ?> !important; }
				.et-fixed-header .et_search_form_container input::-webkit-input-placeholder { color: <?php echo esc_html( $fixed_menu_link ); ?> !important; }
				.et-fixed-header .et_search_form_container input:-ms-input-placeholder { color: <?php echo esc_html( $fixed_menu_link ); ?> !important; }
			<?php } ?>
			<?php if ( $fixed_menu_link_active !== '#2ea3f2' ) { ?>
				.et-fixed-header #top-menu li.current-menu-ancestor > a,
				.et-fixed-header #top-menu li.current-menu-item > a { color: <?php echo esc_html( $fixed_menu_link_active ); ?> !important; }
			<?php } ?>
			<?php if ( '#ffffff' !== $fixed_secondary_menu_link ) { ?>
				.et-fixed-header#top-header a { color: <?php echo esc_html( $fixed_secondary_menu_link ); ?>; }
			<?php } ?>

			<?php
				/* Blog Meta & Title */
				et_pb_print_styles_css( array(
					array(
						'key'      => 'post_meta_font_size',
						'type'     => 'font-size',
						'default'  => 14,
						'selector' => $et_pb_print_selectors_post_meta,
					),
					array(
						'key'      => 'post_header_font_size',
						'type'     => 'font-size-post-header',
						'default'  => 30,
						'selector' => '',
					),
				) );
			?>
		}
		<?php
		// output the styles below only if not inside the Frontend Builder
		if ( empty( $_GET['et_fb'] ) ) { ?>
			@media only screen and ( min-width: <?php echo esc_html( $large_content_width ); ?>px) {
				.et_pb_row { padding: <?php echo esc_html( intval( $large_content_width * $row_padding / 100 ) ); ?>px 0; }
				.et_pb_section { padding: <?php echo esc_html( intval( $large_content_width * $section_padding / 100 ) ); ?>px 0; }
				.single.et_pb_pagebuilder_layout.et_full_width_page .et_post_meta_wrapper { padding-top: <?php echo esc_html( intval( $large_content_width * $row_padding / 100 * 3 ) ); ?>px; }
				.et_pb_section.et_pb_section_first { padding-top: inherit; }
				.et_pb_fullwidth_section { padding: 0; }
			}
		<?php } ?>

		@media only screen and ( max-width: 980px ) {
			<?php if ( $mobile_primary_nav_bg !== $primary_nav_bg ) { ?>
				#main-header, #main-header .nav li ul, .et-search-form, #main-header .et_mobile_menu { background-color: <?php echo esc_html( $mobile_primary_nav_bg ); ?>; }
			<?php } ?>
			<?php if ( $menu_link !== $mobile_menu_link ) { ?>
				.et_header_style_centered .mobile_nav .select_page, .et_header_style_split .mobile_nav .select_page, .et_mobile_menu li a, .mobile_menu_bar:before, .et_nav_text_color_light #top-menu > li > a, .et_nav_text_color_dark #top-menu > li > a, #top-menu a, .et_mobile_menu li a, #et_search_icon:before, #et_top_search .et-search-form input, .et_search_form_container input, #et-top-navigation .et-cart-info { color: <?php echo esc_html( $mobile_menu_link ); ?>; }
				.et_close_search_field:after { color: <?php echo esc_html( $mobile_menu_link ); ?> !important; }
				.et_search_form_container input::-moz-placeholder { color: <?php echo esc_html( $mobile_menu_link ); ?>; }
				.et_search_form_container input::-webkit-input-placeholder { color: <?php echo esc_html( $mobile_menu_link ); ?>; }
				.et_search_form_container input:-ms-input-placeholder { color: <?php echo esc_html( $mobile_menu_link ); ?>; }
			<?php } ?>
			<?php if ( 14 !== $tablet_body_font_size && $body_font_size !== $tablet_body_font_size ) { ?>
				<?php echo esc_html( $body_selector ); ?>, .et_pb_column_1_2 .et_quote_content blockquote cite, .et_pb_column_1_2 .et_link_content a.et_link_main_url, .et_pb_column_1_3 .et_quote_content blockquote cite, .et_pb_column_3_8 .et_quote_content blockquote cite, .et_pb_column_1_4 .et_quote_content blockquote cite, .et_pb_blog_grid .et_quote_content blockquote cite, .et_pb_column_1_3 .et_link_content a.et_link_main_url, .et_pb_column_3_8 .et_link_content a.et_link_main_url, .et_pb_column_1_4 .et_link_content a.et_link_main_url, .et_pb_blog_grid .et_link_content a.et_link_main_url { font-size: <?php echo esc_html( $tablet_body_font_size ); ?>px; }
				.et_pb_slider.et_pb_module .et_pb_slides .et_pb_slide_content, .et_pb_best_value { font-size: <?php echo esc_html( intval( $tablet_body_font_size * 1.14 ) ); ?>px; }
			<?php } ?>
			<?php if ( 30 !== $tablet_header_font_size && $tablet_header_font_size !== $body_header_size ) { ?>
				h1 { font-size: <?php echo esc_html( $tablet_header_font_size ); ?>px; }
				h2, .product .related h2, .et_pb_column_1_2 .et_quote_content blockquote p { font-size: <?php echo esc_html( intval( $tablet_header_font_size * .86 ) ) ; ?>px; }
				h3 { font-size: <?php echo esc_html( intval( $tablet_header_font_size * .73 ) ); ?>px; }
				h4, .et_pb_circle_counter h3, .et_pb_number_counter h3, .et_pb_column_1_3 .et_pb_post h2, .et_pb_column_1_4 .et_pb_post h2, .et_pb_blog_grid h2, .et_pb_column_1_3 .et_quote_content blockquote p, .et_pb_column_3_8 .et_quote_content blockquote p, .et_pb_column_1_4 .et_quote_content blockquote p, .et_pb_blog_grid .et_quote_content blockquote p, .et_pb_column_1_3 .et_link_content h2, .et_pb_column_3_8 .et_link_content h2, .et_pb_column_1_4 .et_link_content h2, .et_pb_blog_grid .et_link_content h2, .et_pb_column_1_3 .et_audio_content h2, .et_pb_column_3_8 .et_audio_content h2, .et_pb_column_1_4 .et_audio_content h2, .et_pb_blog_grid .et_audio_content h2, .et_pb_column_3_8 .et_pb_audio_module_content h2, .et_pb_column_1_3 .et_pb_audio_module_content h2, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2 { font-size: <?php echo esc_html( intval( $tablet_header_font_size * .6 ) ); ?>px; }
				.et_pb_slider.et_pb_module .et_pb_slides .et_pb_slide_description .et_pb_slide_title { font-size: <?php echo esc_html( intval( $tablet_header_font_size * 1.53 ) ); ?>px; }
				.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_pb_column_1_4 .et_pb_audio_module_content h2 { font-size: <?php echo esc_html( intval( $tablet_header_font_size * .53 ) ); ?>px; }
			<?php } ?>
			<?php if ( 50 !== $tablet_section_height ) { ?>
				.et_pb_section { padding: <?php echo esc_html( $tablet_section_height ); ?>px 0; }
				.et_pb_section.et_pb_section_first { padding-top: inherit; }
				.et_pb_section.et_pb_fullwidth_section { padding: 0; }
			<?php } ?>
			<?php if ( 30 !== $tablet_row_height ) { ?>
				.et_pb_row, .et_pb_column .et_pb_row_inner { padding: <?php echo esc_html( $tablet_row_height ); ?>px 0; }
			<?php } ?>
		}
		@media only screen and ( max-width: 767px ) {
			<?php if ( 14 !== $phone_body_font_size && $phone_body_font_size !== $tablet_body_font_size ) { ?>
				<?php echo esc_html( $body_selector ); ?>, .et_pb_column_1_2 .et_quote_content blockquote cite, .et_pb_column_1_2 .et_link_content a.et_link_main_url, .et_pb_column_1_3 .et_quote_content blockquote cite, .et_pb_column_3_8 .et_quote_content blockquote cite, .et_pb_column_1_4 .et_quote_content blockquote cite, .et_pb_blog_grid .et_quote_content blockquote cite, .et_pb_column_1_3 .et_link_content a.et_link_main_url, .et_pb_column_3_8 .et_link_content a.et_link_main_url, .et_pb_column_1_4 .et_link_content a.et_link_main_url, .et_pb_blog_grid .et_link_content a.et_link_main_url { font-size: <?php echo esc_html( $phone_body_font_size ); ?>px; }
				.et_pb_slider.et_pb_module .et_pb_slides .et_pb_slide_content, .et_pb_best_value { font-size: <?php echo esc_html( intval( $phone_body_font_size * 1.14 ) ); ?>px; }
			<?php } ?>
			<?php if ( 30 !== $phone_header_font_size && $tablet_header_font_size !== $phone_header_font_size ) { ?>
				h1 { font-size: <?php echo esc_html( $phone_header_font_size ); ?>px; }
				h2, .product .related h2, .et_pb_column_1_2 .et_quote_content blockquote p { font-size: <?php echo esc_html( intval( $phone_header_font_size * .86 ) ) ; ?>px; }
				h3 { font-size: <?php echo esc_html( intval( $phone_header_font_size * .73 ) ); ?>px; }
				h4, .et_pb_circle_counter h3, .et_pb_number_counter h3, .et_pb_column_1_3 .et_pb_post h2, .et_pb_column_1_4 .et_pb_post h2, .et_pb_blog_grid h2, .et_pb_column_1_3 .et_quote_content blockquote p, .et_pb_column_3_8 .et_quote_content blockquote p, .et_pb_column_1_4 .et_quote_content blockquote p, .et_pb_blog_grid .et_quote_content blockquote p, .et_pb_column_1_3 .et_link_content h2, .et_pb_column_3_8 .et_link_content h2, .et_pb_column_1_4 .et_link_content h2, .et_pb_blog_grid .et_link_content h2, .et_pb_column_1_3 .et_audio_content h2, .et_pb_column_3_8 .et_audio_content h2, .et_pb_column_1_4 .et_audio_content h2, .et_pb_blog_grid .et_audio_content h2, .et_pb_column_3_8 .et_pb_audio_module_content h2, .et_pb_column_1_3 .et_pb_audio_module_content h2, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2 { font-size: <?php echo esc_html( intval( $phone_header_font_size * .6 ) ); ?>px; }
				.et_pb_slider.et_pb_module .et_pb_slides .et_pb_slide_description .et_pb_slide_title { font-size: <?php echo esc_html( intval( $phone_header_font_size * 1.53 ) ); ?>px; }
				.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_pb_column_1_4 .et_pb_audio_module_content h2 { font-size: <?php echo esc_html( intval( $phone_header_font_size * .53 ) ); ?>px; }
			<?php } ?>
			<?php if ( 50 !== $phone_section_height && $tablet_section_height !== $phone_section_height ) { ?>
				.et_pb_section { padding: <?php echo esc_html( $phone_section_height ); ?>px 0; }
				.et_pb_section.et_pb_section_first { padding-top: inherit; }
				.et_pb_section.et_pb_fullwidth_section { padding: 0; }
			<?php } ?>
			<?php if ( 30 !== $phone_row_height && $tablet_row_height !== $phone_row_height ) { ?>
				.et_pb_row, .et_pb_column .et_pb_row_inner { padding: <?php echo esc_html( $phone_row_height ); ?>px 0; }
			<?php } ?>
		}

	<?php // <<<--------- END THEME CUSTOMIZER CSS --------->>>

		/**
		 * Filter Theme Customizer CSS output.
		 *
		 * @since 3.0.51
		 *
		 * @param string $theme_customizer_css
		 */
		$css_output[] = apply_filters( 'et_divi_theme_customizer_css_output', ob_get_clean() );

		// output responsive css styles for responsive preview in Frontend Builder
		if ( ! empty( $_GET['et_fb'] ) ) {
			ob_start(); ?>
			@media only screen and ( min-width: 981px ) {
				<?php if ( 4 !== $section_padding ) { ?>
					.et_fb_desktop_mode .et_pb_section { padding: <?php echo esc_html( $section_padding ); ?>% 0; }
					.et_fb_desktop_mode .et_pb_section.et_pb_section_first { padding-top: inherit; }
					.et_fb_desktop_mode .et_pb_fullwidth_section { padding: 0; }
				<?php } ?>
				<?php if ( 2 !== $row_padding ) { ?>
					.et_fb_desktop_mode .et_pb_row { padding: <?php echo esc_html( $row_padding ); ?>% 0; }
				<?php } ?>
				<?php if ( 30 !== $body_header_size ) { ?>
					.et_fb_desktop_mode h1 { font-size: <?php echo esc_html( $body_header_size ); ?>px; }
					.et_fb_desktop_mode h2, .et_fb_desktop_mode .product .related h2, .et_fb_desktop_mode .et_pb_column_1_2 .et_quote_content blockquote p { font-size: <?php echo esc_html( intval( $body_header_size * .86 ) ) ; ?>px; }
					.et_fb_desktop_mode h3 { font-size: <?php echo esc_html( intval( $body_header_size * .73 ) ); ?>px; }
					.et_fb_desktop_mode h4, .et_fb_desktop_mode .et_pb_circle_counter h3, .et_fb_desktop_mode .et_pb_number_counter h3, .et_fb_desktop_mode .et_pb_column_1_3 .et_pb_post h2, .et_fb_desktop_mode .et_pb_column_1_4 .et_pb_post h2, .et_fb_desktop_mode .et_pb_blog_grid h2, .et_fb_desktop_mode .et_pb_column_1_3 .et_quote_content blockquote p, .et_fb_desktop_mode .et_pb_column_3_8 .et_quote_content blockquote p, .et_fb_desktop_mode .et_pb_column_1_4 .et_quote_content blockquote p, .et_fb_desktop_mode .et_pb_blog_grid .et_quote_content blockquote p, .et_fb_desktop_mode .et_pb_column_1_3 .et_link_content h2, .et_fb_desktop_mode .et_pb_column_3_8 .et_link_content h2, .et_fb_desktop_mode .et_pb_column_1_4 .et_link_content h2, .et_fb_desktop_mode .et_pb_blog_grid .et_link_content h2, .et_fb_desktop_mode .et_pb_column_1_3 .et_audio_content h2, .et_fb_desktop_mode .et_pb_column_3_8 .et_audio_content h2, .et_fb_desktop_mode .et_pb_column_1_4 .et_audio_content h2, .et_fb_desktop_mode .et_pb_blog_grid .et_audio_content h2, .et_fb_desktop_mode .et_pb_column_3_8 .et_pb_audio_module_content h2, .et_fb_desktop_mode .et_pb_column_1_3 .et_pb_audio_module_content h2, .et_fb_desktop_mode .et_pb_gallery_grid .et_pb_gallery_item h3, .et_fb_desktop_mode .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_fb_desktop_mode .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2 { font-size: <?php echo esc_html( intval( $body_header_size * .6 ) ); ?>px; }
					.et_fb_desktop_mode h5 { font-size: <?php echo esc_html( intval( $body_header_size * .53 ) ); ?>px; }
					.et_fb_desktop_mode h6 { font-size: <?php echo esc_html( intval( $body_header_size * .47 ) ); ?>px; }
					.et_fb_desktop_mode .et_pb_slide_description .et_pb_slide_title { font-size: <?php echo esc_html( intval( $body_header_size * 1.53 ) ); ?>px; }
					.et_fb_desktop_mode .woocommerce ul.products li.product h3, .et_fb_desktop_mode .woocommerce-page ul.products li.product h3, .et_fb_desktop_mode .et_pb_gallery_grid .et_pb_gallery_item h3, .et_fb_desktop_mode .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_fb_desktop_mode .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_fb_desktop_mode .et_pb_column_1_4 .et_pb_audio_module_content h2 { font-size: <?php echo esc_html( intval( $body_header_size * .53 ) ); ?>px; }
				<?php } ?>
			}

			@media only screen and ( min-width: <?php echo esc_html( $large_content_width ); ?>px) {
				.et_fb_desktop_mode .et_pb_row { padding: <?php echo esc_html( intval( $large_content_width * $row_padding / 100 ) ); ?>px 0; }
				.et_fb_desktop_mode .et_pb_section { padding: <?php echo esc_html( intval( $large_content_width * $section_padding / 100 ) ); ?>px 0; }
				.et_fb_desktop_mode .single.et_pb_pagebuilder_layout.et_full_width_page .et_post_meta_wrapper { padding-top: <?php echo esc_html( intval( $large_content_width * $row_padding / 100 * 3 ) ); ?>px; }
				.et_fb_desktop_mode .et_pb_section.et_pb_section_first { padding-top: inherit; }
				.et_fb_desktop_mode .et_pb_fullwidth_section { padding: 0; }
			}

			<?php if ( 14 !== $tablet_body_font_size && $body_font_size !== $tablet_body_font_size ) { ?>
				.et_fb_preview_active.et_fb_preview_active--responsive_preview <?php echo esc_html( $body_selector ); ?>, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_2 .et_quote_content blockquote cite, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_2 .et_link_content a.et_link_main_url, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_3 .et_quote_content blockquote cite, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_3_8 .et_quote_content blockquote cite, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_4 .et_quote_content blockquote cite, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_blog_grid .et_quote_content blockquote cite, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_3 .et_link_content a.et_link_main_url, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_3_8 .et_link_content a.et_link_main_url, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_4 .et_link_content a.et_link_main_url, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_blog_grid .et_link_content a.et_link_main_url { font-size: <?php echo esc_html( $tablet_body_font_size ); ?>px; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_slider.et_pb_module .et_pb_slides .et_pb_slide_content, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_best_value { font-size: <?php echo esc_html( intval( $tablet_body_font_size * 1.14 ) ); ?>px; }
			<?php } ?>
			<?php if ( 30 !== $tablet_header_font_size && $tablet_header_font_size !== $body_header_size ) { ?>
				.et_fb_preview_active.et_fb_preview_active--responsive_preview h1 { font-size: <?php echo esc_html( $tablet_header_font_size ); ?>px; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .product .related h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_2 .et_quote_content blockquote p { font-size: <?php echo esc_html( intval( $tablet_header_font_size * .86 ) ) ; ?>px; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview h3 { font-size: <?php echo esc_html( intval( $tablet_header_font_size * .73 ) ); ?>px; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview h4, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_circle_counter h3, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_number_counter h3, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_3 .et_pb_post h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_4 .et_pb_post h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_blog_grid h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_3 .et_quote_content blockquote p, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_3_8 .et_quote_content blockquote p, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_4 .et_quote_content blockquote p, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_blog_grid .et_quote_content blockquote p, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_3 .et_link_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_3_8 .et_link_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_4 .et_link_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_blog_grid .et_link_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_3 .et_audio_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_3_8 .et_audio_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_4 .et_audio_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_blog_grid .et_audio_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_3_8 .et_pb_audio_module_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_3 .et_pb_audio_module_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_gallery_grid .et_pb_gallery_item h3, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2 { font-size: <?php echo esc_html( intval( $tablet_header_font_size * .6 ) ); ?>px; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_slider.et_pb_module .et_pb_slides .et_pb_slide_description .et_pb_slide_title { font-size: <?php echo esc_html( intval( $tablet_header_font_size * 1.53 ) ); ?>px; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview .woocommerce ul.products li.product h3, .et_fb_preview_active.et_fb_preview_active--responsive_preview .woocommerce-page ul.products li.product h3, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_gallery_grid .et_pb_gallery_item h3, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column_1_4 .et_pb_audio_module_content h2 { font-size: <?php echo esc_html( intval( $tablet_header_font_size * .53 ) ); ?>px; }
			<?php } ?>
			<?php if ( 50 !== $tablet_section_height ) { ?>
				.et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_section { padding: <?php echo esc_html( $tablet_section_height ); ?>px 0; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_section.et_pb_section_first { padding-top: inherit; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_section.et_pb_fullwidth_section { padding: 0; }
			<?php } ?>
			<?php if ( 30 !== $tablet_row_height ) { ?>
				.et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_row, .et_fb_preview_active.et_fb_preview_active--responsive_preview .et_pb_column .et_pb_row_inner { padding: <?php echo esc_html( $tablet_row_height ); ?>px 0; }
			<?php } ?>

			<?php if ( 14 !== $phone_body_font_size && $phone_body_font_size !== $tablet_body_font_size ) { ?>
				.et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview <?php echo esc_html( $body_selector ); ?>, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_2 .et_quote_content blockquote cite, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_2 .et_link_content a.et_link_main_url, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_3 .et_quote_content blockquote cite, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_3_8 .et_quote_content blockquote cite, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_4 .et_quote_content blockquote cite, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_blog_grid .et_quote_content blockquote cite, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_3 .et_link_content a.et_link_main_url, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_3_8 .et_link_content a.et_link_main_url, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_4 .et_link_content a.et_link_main_url, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_blog_grid .et_link_content a.et_link_main_url { font-size: <?php echo esc_html( $phone_body_font_size ); ?>px; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_slider.et_pb_module .et_pb_slides .et_pb_slide_content, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_best_value { font-size: <?php echo esc_html( intval( $phone_body_font_size * 1.14 ) ); ?>px; }
			<?php } ?>
			<?php if ( 30 !== $phone_header_font_size && $tablet_header_font_size !== $phone_header_font_size ) { ?>
				.et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview h1 { font-size: <?php echo esc_html( $phone_header_font_size ); ?>px; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .product .related h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_2 .et_quote_content blockquote p { font-size: <?php echo esc_html( intval( $phone_header_font_size * .86 ) ) ; ?>px; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview h3 { font-size: <?php echo esc_html( intval( $phone_header_font_size * .73 ) ); ?>px; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview h4, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_circle_counter h3, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_number_counter h3, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_3 .et_pb_post h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_4 .et_pb_post h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_blog_grid h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_3 .et_quote_content blockquote p, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_3_8 .et_quote_content blockquote p, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_4 .et_quote_content blockquote p, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_blog_grid .et_quote_content blockquote p, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_3 .et_link_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_3_8 .et_link_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_4 .et_link_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_blog_grid .et_link_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_3 .et_audio_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_3_8 .et_audio_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_4 .et_audio_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_blog_grid .et_audio_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_3_8 .et_pb_audio_module_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_3 .et_pb_audio_module_content h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_gallery_grid .et_pb_gallery_item h3, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2 { font-size: <?php echo esc_html( intval( $phone_header_font_size * .6 ) ); ?>px; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_slider.et_pb_module .et_pb_slides .et_pb_slide_description h2.et_pb_slide_title { font-size: <?php echo esc_html( intval( $phone_header_font_size * 1.53 ) ); ?>px; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .woocommerce ul.products li.product h3, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .woocommerce-page ul.products li.product h3, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_gallery_grid .et_pb_gallery_item h3, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column_1_4 .et_pb_audio_module_content h2 { font-size: <?php echo esc_html( intval( $phone_header_font_size * .53 ) ); ?>px; }
			<?php } ?>
			<?php if ( 50 !== $phone_section_height && $tablet_section_height !== $phone_section_height ) { ?>
				.et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_section { padding: <?php echo esc_html( $phone_section_height ); ?>px 0; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_section.et_pb_section_first { padding-top: inherit; }
				.et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_section.et_pb_fullwidth_section { padding: 0; }
			<?php } ?>
			<?php if ( 30 !== $phone_row_height && $tablet_row_height !== $phone_row_height ) { ?>
				.et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_row, .et_fb_preview_active.et_fb_preview_active--responsive_preview.et_fb_preview_active--responsive_preview--phone_preview .et_pb_column .et_pb_row_inner { padding: <?php echo esc_html( $phone_row_height ); ?>px 0; }
			<?php }
			$css_output[] = ob_get_clean();
		}

		$et_gf_heading_font = sanitize_text_field( et_get_option( 'heading_font', 'none' ) );
		$et_gf_body_font = sanitize_text_field( et_get_option( 'body_font', 'none' ) );
		$et_gf_buttons_font = sanitize_text_field( et_get_option( 'all_buttons_font', 'none' ) );
		$et_gf_primary_nav_font = sanitize_text_field( et_get_option( 'primary_nav_font', 'none' ) );
		$et_gf_secondary_nav_font = sanitize_text_field( et_get_option( 'secondary_nav_font', 'none' ) );
		$et_gf_slide_nav_font = sanitize_text_field( et_get_option( 'slide_nav_font', 'none' ) );
		$site_domain = get_locale();

		$et_one_font_languages = et_get_one_font_languages();

		/* =========================================
		 * --------->>> BEGIN FONTS CSS <<<---------
		 * ========================================= */
		ob_start();

		if ( isset( $et_one_font_languages[$site_domain] ) ) {
			printf( '%s { font-family: %s; }',
				'h1, h2, h3, h4, h5, h6, body, input, textarea, select',
				sanitize_text_field( $et_one_font_languages[$site_domain]['font_family'] )
			);
		} else if ( ! in_array( $et_gf_heading_font, array( '', 'none' ) ) || ! in_array( $et_gf_body_font, array( '', 'none' ) ) || ! in_array( $et_gf_buttons_font, array( '', 'none' ) ) || ! in_array( $et_gf_primary_nav_font, array( '', 'none' ) ) || ! in_array( $et_gf_secondary_nav_font, array( '', 'none' ) ) || ! in_array( $et_gf_slide_nav_font, array( '', 'none' ) ) ) {
			if ( ! in_array( $et_gf_heading_font, array( '', 'none' ) ) ) { ?>
				h1, h2, h3, h4, h5, h6 {
					<?php echo sanitize_text_field( et_builder_get_font_family( $et_gf_heading_font ) ); ?>
				}
			<?php }

			if ( ! in_array( $et_gf_body_font, array( '', 'none' ) ) ) { ?>
				body, input, textarea, select {
					<?php echo sanitize_text_field( et_builder_get_font_family( $et_gf_body_font ) ); ?>
				}
			<?php }

			if ( ! in_array( $et_gf_buttons_font, array( '', 'none' ) ) ) { ?>
				.et_pb_button {
					<?php echo sanitize_text_field( et_builder_get_font_family( $et_gf_buttons_font ) ); ?>
				}
			<?php }

			if ( ! in_array( $et_gf_primary_nav_font, array( '', 'none' ) ) ) { ?>
				#main-header,
				#et-top-navigation {
					<?php echo sanitize_text_field( et_builder_get_font_family( $et_gf_primary_nav_font ) ); ?>
				}
			<?php }

			if ( ! in_array( $et_gf_secondary_nav_font, array( '', 'none' ) ) ) { ?>
				#top-header .container{
					<?php echo sanitize_text_field( et_builder_get_font_family( $et_gf_secondary_nav_font ) ); ?>
				}
			<?php }

			if ( ! in_array( $et_gf_slide_nav_font, array( '', 'none' ) ) ) { ?>
				.et_slide_in_menu_container, .et_slide_in_menu_container .et-search-field{
					<?php echo sanitize_text_field( et_builder_get_font_family( $et_gf_slide_nav_font ) ); ?>
				}
			<?php }
		} // <<<--------- END FONTS CSS --------->>>

		/**
		 * Filter fonts CSS output.
		 *
		 * @since 3.0.51
		 *
		 * @param string $css_output
		 */
		$css_output[] = apply_filters( 'et_divi_fonts_css_output', ob_get_clean() );

		/**
		 * use_sidebar_width might invalidate the use of sidebar_width.
		 * It is placed outside other customizer style so live preview
		 * can invalidate and revalidate it for smoother experience
		 */
		if ( $use_sidebar_width && 21 !== $sidebar_width && 19 <= $sidebar_width && 33 >= $sidebar_width ) {
			$content_width = 100 - $sidebar_width;
			$content_width_percentage = $content_width . '%';
			$sidebar_width_percentage = $sidebar_width . '%';
			$sidebar_width_css        = sprintf(
				'body #page-container #sidebar { width:%2$s; }
				body #page-container #left-area { width:%1$s; }
				.et_right_sidebar #main-content .container:before { right:%2$s !important; }
				.et_left_sidebar #main-content .container:before { left:%2$s !important; }',
				esc_html( $content_width_percentage  ),
				esc_html( $sidebar_width_percentage )
			);

			/**
			 * Filter sidebar width CSS output.
			 *
			 * @since 3.0.51
			 *
			 * @param string $sidebar_width_css
			 */
			$css_output[] = apply_filters( 'et_divi_sidebar_width_css_output', $sidebar_width_css );
		}

		/* ====================================================
		 * --------->>> BEGIN MODULE CUSTOMIZER CSS <<<--------
		 * ==================================================== */
		ob_start();

			/* Gallery */
			et_pb_print_module_styles_css( 'et_pb_gallery', array(
				array(
					'type'		=> 'color',
					'key' 		=> 'zoom_icon_color',
					'selector' 	=> '.et_pb_gallery_image .et_overlay:before',
					'important'	=> true,
				),
				array(
					'type'		=> 'background-color',
					'key' 		=> 'hover_overlay_color',
					'selector' 	=> '.et_pb_gallery_image .et_overlay',
				),
				array(
					'type'		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_gallery_grid .et_pb_gallery_item .et_pb_gallery_title',
				),
				array(
					'type'		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_gallery_grid .et_pb_gallery_item .et_pb_gallery_title',
				),
				array(
					'type'		=> 'font-size',
					'key' 		=> 'caption_font_size',
					'selector' 	=> '.et_pb_gallery .et_pb_gallery_item .et_pb_gallery_caption',
				),
				array(
					'type'		=> 'font-style',
					'key' 		=> 'caption_font_style',
					'selector' 	=> '.et_pb_gallery .et_pb_gallery_item .et_pb_gallery_caption',
				),
			) );

			/* Blurb */
			et_pb_print_module_styles_css( 'et_pb_blurb', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_blurb h4',
				),
			) );

			/* Tabs */
			et_pb_print_module_styles_css( 'et_pb_tabs', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_tabs_controls li',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_tabs_controls li',
				),
				array(
					'type' 		=> 'padding-tabs',
					'key' 		=> 'padding',
					'selector' 	=> '',
				),
			) );

			/* Slider */
			et_pb_print_module_styles_css( 'et_pb_slider', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_slider_fullwidth_off .et_pb_slide_description .et_pb_slide_title',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_slider_fullwidth_off .et_pb_slide_description .et_pb_slide_title',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'body_font_size',
					'selector' 	=> '.et_pb_slider_fullwidth_off .et_pb_slide_content',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'body_font_style',
					'selector' 	=> '.et_pb_slider_fullwidth_off .et_pb_slide_content',
				),
				array(
					'type' 		=> 'padding-slider',
					'key' 		=> 'padding',
					'selector' 	=> '.et_pb_slider_fullwidth_off .et_pb_slide_description',
				),
			) );

			/* Testimonial */
			et_pb_print_module_styles_css( 'et_pb_testimonial', array(
				array(
					'type' 		=> 'border-radius',
					'key' 		=> 'portrait_border_radius',
					'selector' 	=> '.et_pb_testimonial_portrait, .et_pb_testimonial_portrait:before',
				),
				array(
					'type' 		=> 'width',
					'key' 		=> 'portrait_width',
					'selector' 	=> '.et_pb_testimonial_portrait',
				),
				array(
					'type' 		=> 'height',
					'key' 		=> 'portrait_height',
					'selector' 	=> '.et_pb_testimonial_portrait',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'author_name_font_style',
					'selector' 	=> '.et_pb_testimonial_author',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'author_details_font_style',
					'selector' 	=> 'p.et_pb_testimonial_meta',
				),
			) );

			/* Pricing Table */
			et_pb_print_module_styles_css( 'et_pb_pricing_tables', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_pricing_heading h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_pricing_heading h2',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'subheader_font_size',
					'selector' 	=> '.et_pb_best_value',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'subheader_font_style',
					'selector' 	=> '.et_pb_best_value',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'price_font_size',
					'selector' 	=> '.et_pb_sum',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'price_font_style',
					'selector' 	=> '.et_pb_sum',
				),
			) );

			/* Call to Action */
			et_pb_print_module_styles_css( 'et_pb_cta', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_promo h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_promo h2, .et_pb_promo h1',
				),
				array(
					'type' 		=> 'padding-call-to-action',
					'key' 		=> 'custom_padding',
					'selector' 	=> '',
					'important' => true,
				),
			) );

			/* Audio */
			et_pb_print_module_styles_css( 'et_pb_audio', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_audio_module_content h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_audio_module_content h2',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'caption_font_size',
					'selector' 	=> '.et_pb_audio_module p',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'caption_font_style',
					'selector' 	=> '.et_pb_audio_module p',
				),
			) );

			/* Email Optin */
			et_pb_print_module_styles_css( 'et_pb_signup', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_subscribe h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_subscribe h2',
				),
				array(
					'type' 		=> 'padding',
					'key' 		=> 'padding',
					'selector' 	=> '.et_pb_subscribe',
				),
			) );

			/* Login */
			et_pb_print_module_styles_css( 'et_pb_login', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_login h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_login h2',
				),
				array(
					'type' 		=> 'padding-top-bottom',
					'key' 		=> 'custom_padding',
					'selector' 	=> '.et_pb_login',
				),
			) );

			/* Portfolio */
			et_pb_print_module_styles_css( 'et_pb_portfolio', array(
				array(
					'type' 		=> 'color',
					'key' 		=> 'zoom_icon_color',
					'selector' 	=> '.et_pb_portfolio .et_overlay:before, .et_pb_fullwidth_portfolio .et_overlay:before, .et_pb_portfolio_grid .et_overlay:before',
					'important' => true,
				),
				array(
					'type' 		=> 'background-color',
					'key' 		=> 'hover_overlay_color',
					'selector' 	=> '.et_pb_portfolio .et_overlay, .et_pb_fullwidth_portfolio .et_overlay, .et_pb_portfolio_grid .et_overlay',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_portfolio .et_pb_portfolio_item h2, .et_pb_fullwidth_portfolio .et_pb_portfolio_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_portfolio .et_pb_portfolio_item h2, .et_pb_fullwidth_portfolio .et_pb_portfolio_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'caption_font_size',
					'selector' 	=> '.et_pb_portfolio .et_pb_portfolio_item .post-meta, .et_pb_fullwidth_portfolio .et_pb_portfolio_item .post-meta, .et_pb_portfolio_grid .et_pb_portfolio_item .post-meta',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'caption_font_style',
					'selector' 	=> '.et_pb_portfolio .et_pb_portfolio_item .post-meta, .et_pb_fullwidth_portfolio .et_pb_portfolio_item .post-meta, .et_pb_portfolio_grid .et_pb_portfolio_item .post-meta',
				),
			) );

			/* Filterable Portfolio */
			et_pb_print_module_styles_css( 'et_pb_filterable_portfolio', array(
				array(
					'type' 		=> 'color',
					'key' 		=> 'zoom_icon_color',
					'selector' 	=> '.et_pb_filterable_portfolio .et_overlay:before',
				),
				array(
					'type' 		=> 'background-color',
					'key' 		=> 'hover_overlay_color',
					'selector' 	=> '.et_pb_filterable_portfolio .et_overlay',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_filterable_portfolio .et_pb_portfolio_item h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_filterable_portfolio .et_pb_portfolio_item h2',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'caption_font_size',
					'selector' 	=> '.et_pb_filterable_portfolio .et_pb_portfolio_item .post-meta',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'caption_font_style',
					'selector' 	=> '.et_pb_filterable_portfolio .et_pb_portfolio_item .post-meta',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'filter_font_size',
					'selector' 	=> '.et_pb_filterable_portfolio .et_pb_portfolio_filters li',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'filter_font_style',
					'selector' 	=> '.et_pb_filterable_portfolio .et_pb_portfolio_filters li',
				),
			) );

			/* Bar Counter */
			et_pb_print_module_styles_css( 'et_pb_counters', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_counters .et_pb_counter_title',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_counters .et_pb_counter_title',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'percent_font_size',
					'selector' 	=> '.et_pb_counters .et_pb_counter_amount',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'percent_font_style',
					'selector' 	=> '.et_pb_counters .et_pb_counter_amount',
				),
				array(
					'type' 		=> 'border-radius',
					'key' 		=> 'border_radius',
					'selector' 	=> '.et_pb_counters .et_pb_counter_amount, .et_pb_counters .et_pb_counter_container',
				),
				array(
					'type' 		=> 'padding',
					'key' 		=> 'padding',
					'selector' 	=> '.et_pb_counter_amount',
				),
			) );

			/* Circle Counter */
			et_pb_print_module_styles_css( 'et_pb_circle_counter', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'number_font_size',
					'selector' 	=> '.et_pb_circle_counter .percent p',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'number_font_style',
					'selector' 	=> '.et_pb_circle_counter .percent p',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_circle_counter h3',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_circle_counter h3',
				),
			) );

			/* Number Counter */
			et_pb_print_module_styles_css( 'et_pb_number_counter', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'number_font_size',
					'selector' 	=> '.et_pb_number_counter .percent p',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'number_font_style',
					'selector' 	=> '.et_pb_number_counter .percent p',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_number_counter h3',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_number_counter h3',
				),
			) );

			/* Accordion */
			et_pb_print_module_styles_css( 'et_pb_accordion', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'toggle_font_size',
					'selector' 	=> '.et_pb_accordion .et_pb_toggle_title',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'toggle_font_style',
					'selector' 	=> '.et_pb_accordion .et_pb_toggle.et_pb_toggle_open .et_pb_toggle_title',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'inactive_toggle_font_style',
					'selector' 	=> '.et_pb_accordion .et_pb_toggle.et_pb_toggle_close .et_pb_toggle_title',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'toggle_icon_size',
					'selector' 	=> '.et_pb_accordion .et_pb_toggle_title:before',
				),
				array(
					'type' 		=> 'padding',
					'key' 		=> 'custom_padding',
					'selector' 	=> '.et_pb_accordion .et_pb_toggle_open, .et_pb_accordion .et_pb_toggle_close',
				),
			) );

			/* Toggle */
			et_pb_print_module_styles_css( 'et_pb_toggle', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_toggle.et_pb_toggle_item h5',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_toggle.et_pb_toggle_item.et_pb_toggle_open h5',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'inactive_title_font_style',
					'selector' 	=> '.et_pb_toggle.et_pb_toggle_item.et_pb_toggle_close h5',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'toggle_icon_size',
					'selector' 	=> '.et_pb_toggle.et_pb_toggle_item .et_pb_toggle_title:before',
				),
				array(
					'type' 		=> 'padding',
					'key' 		=> 'custom_padding',
					'selector' 	=> '.et_pb_toggle.et_pb_toggle_item',
				),
			) );

			/* Contact Form */
			et_pb_print_module_styles_css( 'et_pb_contact_form', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_contact_form_container .et_pb_contact_main_title',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_contact_form_container .et_pb_contact_main_title',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'form_field_font_size',
					'selector' 	=> '.et_pb_contact_form_container .et_pb_contact p input, .et_pb_contact_form_container .et_pb_contact p textarea',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'form_field_font_style',
					'selector' 	=> '.et_pb_contact_form_container .et_pb_contact p input, .et_pb_contact_form_container .et_pb_contact p textarea',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'captcha_font_size',
					'selector' 	=> '.et_pb_contact_captcha_question',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'captcha_font_style',
					'selector' 	=> '.et_pb_contact_captcha_question',
				),
				array(
					'type' 		=> 'padding',
					'key' 		=> 'padding',
					'selector' 	=> '.et_pb_contact p input, .et_pb_contact p textarea',
				),
			) );

			/* Sidebar */
			et_pb_print_module_styles_css( 'et_pb_sidebar', array(
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_widget_area h4',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_widget_area h4',
				),
			) );

			/* Divider */
			et_pb_print_module_styles_css( 'et_pb_divider', array(
				array(
					'type' 		=> 'border-top-style',
					'key' 		=> 'divider_style',
					'selector' 	=> '.et_pb_space:before',
				),
				array(
					'type' 		=> 'border-top-width',
					'key' 		=> 'divider_weight',
					'selector' 	=> '.et_pb_space:before',
				),
				array(
					'type' 		=> 'height',
					'key' 		=> 'height',
					'selector' 	=> '.et_pb_space',
				),
			) );

			/* Person */
			et_pb_print_module_styles_css( 'et_pb_team_member', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_team_member h4',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_team_member h4',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'subheader_font_size',
					'selector' 	=> '.et_pb_team_member .et_pb_member_position',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'subheader_font_style',
					'selector' 	=> '.et_pb_team_member .et_pb_member_position',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'social_network_icon_size',
					'selector' 	=> '.et_pb_member_social_links a',
				),
			) );

			/* Blog */
			et_pb_print_module_styles_css( 'et_pb_blog', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_posts .et_pb_post h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_posts .et_pb_post h2',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'meta_font_size',
					'selector' 	=> '.et_pb_posts .et_pb_post .post-meta',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'meta_font_style',
					'selector' 	=> '.et_pb_posts .et_pb_post .post-meta',
				),
			) );

			/* Blog Masonry */
			et_pb_print_module_styles_css( 'et_pb_blog_masonry', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_blog_grid .et_pb_post h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_blog_grid .et_pb_post h2',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'meta_font_size',
					'selector' 	=> '.et_pb_blog_grid .et_pb_post .post-meta',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'meta_font_style',
					'selector' 	=> '.et_pb_blog_grid .et_pb_post .post-meta',
				),
			) );

			/* Shop */
			et_pb_print_module_styles_css( 'et_pb_shop', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3',
					'important' => false,
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3',
					'important' => false,
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'sale_badge_font_size',
					'selector' 	=> '.woocommerce span.onsale, .woocommerce-page span.onsale',
					'important' => false,
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'sale_badge_font_style',
					'selector' 	=> '.woocommerce span.onsale, .woocommerce-page span.onsale',
					'important' => true,
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'price_font_size',
					'selector' 	=> '.woocommerce ul.products li.product .price .amount, .woocommerce-page ul.products li.product .price .amount',
					'important' => false,
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'price_font_style',
					'selector' 	=> '.woocommerce ul.products li.product .price .amount, .woocommerce-page ul.products li.product .price .amount',
					'important' => true,
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'sale_price_font_size',
					'selector' 	=> '.woocommerce ul.products li.product .price ins .amount, .woocommerce-page ul.products li.product .price ins .amount',
					'important' => false,
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'sale_price_font_style',
					'selector' 	=> '.woocommerce ul.products li.product .price ins .amount, .woocommerce-page ul.products li.product .price ins .amount',
					'important' => true,
				),
			) );

			/* Countdown */
			et_pb_print_module_styles_css( 'et_pb_countdown_timer', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_countdown_timer .title',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_countdown_timer .title',
				),
			) );

			/* Social */
			et_pb_print_module_styles_css( 'et_pb_social_media_follow', array(
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'button_font_style',
					'selector' 	=> '.et_pb_social_media_follow li a.follow_button',
				),
				array(
					'type' 		=> 'social-icon-size',
					'key' 		=> 'icon_size',
					'selector' 	=> '',
				),
			) );

			/* Fullwidth Slider */
			et_pb_print_module_styles_css( 'et_pb_fullwidth_slider', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_fullwidth_section .et_pb_slide_description .et_pb_slide_title',
					'default' 	=> '46',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_fullwidth_section .et_pb_slide_description .et_pb_slide_title',
					'default' 	=> '',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'body_font_size',
					'selector' 	=> '.et_pb_fullwidth_section .et_pb_slide_content',
					'default' 	=> 16,
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'body_font_style',
					'selector' 	=> '.et_pb_fullwidth_section .et_pb_slide_content',
					'default' 	=> '',
				),
				array(
					'type' 		=> 'padding-slider',
					'key' 		=> 'padding',
					'selector' 	=> '.et_pb_fullwidth_section .et_pb_slide_description',
					'default' 	=> '16',
				),
			) ); // <<<--------- END MODULE CUSTOMIZER CSS --------->>>

			/**
			 * Filter Module Customizer CSS output.
			 *
			 * @since 3.0.51
			 *
			 * @param string $module_customizer_css
			 */
			$css_output[] = apply_filters( 'et_divi_module_customizer_css_output', ob_get_clean() );

			// Give the output to the style manager so a static resource can be created and served.
			$styles_manager->set_data( implode( '\n', $css_output ) );
}
add_action( 'wp', 'et_divi_add_customizer_css' );

/**
 * Outputting saved customizer style settings
 *
 * @return void
 */
function et_pb_print_css( $setting ) {

	// Defaults value
	$defaults = array(
		'key'       => false,
		'selector'  => false,
		'type'      => false,
		'default'   => false,
		'important' => false
	);

	// Parse given settings aginst defaults
	$setting = wp_parse_args( $setting, $defaults );

	if (
		$setting['key']      !== false ||
		$setting['selector'] !== false ||
		$setting['type']     !== false ||
		$setting['settings'] !== false
	) {

		// Some attribute requires !important tag
		if ( $setting['important'] ) {
			$important = "!important";
		} else {
			$important = "";
		}

		// get value
		$value = et_get_option( $setting['key'], $setting['default'] );

		// Output css based on its type
		if ( $value !== false && $value != $setting['default'] ) {
			switch ( $setting['type'] ) {
				case 'font-size':
					printf( '%1$s { font-size: %2$spx %3$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value ),
						$important );
					break;

				case 'font-size-post-header':
					$posts_font_size = intval( $value ) * ( 26 / 30 );
					printf( 'body.home-posts #left-area .et_pb_post h2, body.archive #left-area .et_pb_post h2, body.search #left-area .et_pb_post h2 { font-size:%1$spx }
						body.single .et_post_meta_wrapper h1 { font-size:%2$spx; }',
						esc_html( $posts_font_size ),
						esc_html( $value )
					);
					break;

				case 'font-style':
					printf( '%1$s { %2$s }',
						esc_html( $setting['selector'] ),
						et_pb_print_font_style( $value, $important )
					);
					break;

				case 'letter-spacing':
					printf( '%1$s { letter-spacing: %2$spx %3$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value ),
						$important
					);
					break;

				case 'line-height':
					printf( '%1$s { line-height: %2$sem %3$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value ),
						$important
					);
					break;

				case 'color':
					printf( '%1$s { color: %2$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'background-color':
					printf( '%1$s { background-color: %2$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'border-radius':
					printf( '%1$s { -moz-border-radius: %2$spx; -webkit-border-radius: %2$spx; border-radius: %2$spx; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'width':
					printf( '%1$s { width: %2$spx; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'height':
					printf( '%1$s { height: %2$spx; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'padding':
					printf( '%1$s { padding: %2$spx; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'padding-top-bottom':
					printf( '%1$s { padding: %2$spx 0; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'padding-tabs':
					printf( '%1$s { padding: %2$spx %3$spx; }',
						esc_html( $setting['selector'] ),
						esc_html( ( intval( $value ) * 0.5 ) ),
						esc_html( $value )
					);
					break;

				case 'padding-fullwidth-slider':
					printf( '%1$s { padding: %2$s %3$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value ) . '%',
						'0'
					);
					break;

				case 'padding-slider':
					printf( '%1$s { padding: %2$s %3$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value ) . '%',
						esc_html( ( intval( $value ) / 2 ) ) . '%'
					);
					break;

				case 'social-icon-size':
					$icon_margin 	= intval( $value ) * 0.57;
					$icon_dimension = intval( $value ) * 2;
					?>
					.et_pb_social_media_follow li a.icon{
						margin-right: <?php echo esc_html( $icon_margin ); ?>px;
						width: <?php echo esc_html( $icon_dimension ); ?>px;
						height: <?php echo esc_html( $icon_dimension ); ?>px;
					}

					.et_pb_social_media_follow li a.icon::before{
						width: <?php echo esc_html( $icon_dimension ); ?>px;
						height: <?php echo esc_html( $icon_dimension ); ?>px;
						font-size: <?php echo esc_html( $value ); ?>px;
						line-height: <?php echo esc_html( $icon_dimension ); ?>px;
					}
					<?php
					break;
			}
		}
	}
}

/**
 * Outputting saved customizer style(s) settings
 */
function et_pb_print_styles_css( $settings = array() ) {

	// $settings should be in array
	if ( is_array( $settings ) && ! empty( $settings ) ) {

		// Loop settings
		foreach ( $settings as $setting ) {

			// Print css
			et_pb_print_css( $setting );

		}
	}
}

/**
 * Outputting saved module styles settings. DRY
 *
 * @return void
 */
function et_pb_print_module_styles_css( $section = '', $settings = array() ) {

	// Verify settings
	if ( is_array( $settings ) && ! empty( $settings ) ) {

		// Loop settings
		foreach ( $settings as $setting ) {

			// settings must have these elements: key, selector, default, and type
			if ( ! isset( $setting['key'] ) ||
				! isset( $setting['selector'] ) ||
				! isset( $setting['type'] ) ) {
				continue;
			}

			// Some attributes such as shop requires !important tag
			if ( isset( $setting['important'] ) && true === $setting['important'] ) {
				$important = ' !important';
			} else {
				$important = '';
			}

			// Prepare the setting key
			$key = "{$section}-{$setting['key']}";

			// Get the value
			$value = ET_Global_Settings::get_value( $key );
			$default_value = ET_Global_Settings::get_value( $key, 'default' );

			// Output CSS based on its type
			if ( false !== $value && $default_value !== $value ) {

				switch ( $setting['type'] ) {
					case 'font-size':

						printf( "%s { font-size: %spx%s; }\n", esc_html( $setting['selector'] ), esc_html( $value ), $important );

						// Option with specific adjustment for smaller columns
						$smaller_title_sections = array(
							'et_pb_audio-title_font_size',
							'et_pb_blog-header_font_size',
							'et_pb_cta-header_font_size',
							'et_pb_contact_form-title_font_size',
							'et_pb_login-header_font_size',
							'et_pb_signup-header_font_size',
							'et_pb_slider-header_font_size',
							'et_pb_slider-body_font_size',
							'et_pb_countdown_timer-header_font_size',
						);

						if ( in_array( $key, $smaller_title_sections ) ) {

							// font size coefficient
							switch ( $key ) {
								case 'et_pb_slider-header_font_size':
									$font_size_coefficient = .565217391; // 26/46
									break;

								case 'et_pb_slider-body_font_size':
									$font_size_coefficient = .777777778; // 14/16
									break;

								default:
									$font_size_coefficient = .846153846; // 22/26
									break;
							}

							printf( '.et_pb_column_1_3 %1$s, .et_pb_column_1_4 %1$s { font-size: %2$spx%3$s; }',
								esc_html( $setting['selector'] ),
								esc_html( $value * $font_size_coefficient ),
								$important
							);
						}

						break;

					case 'font-size':
						$value = intval( $value );

						printf( ".et_pb_countdown_timer .title { font-size: %spx; }", esc_html( $value ) );
						printf( ".et_pb_column_3_8 .et_pb_countdown_timer .title, .et_pb_column_1_3 .et_pb_countdown_timer .title, .et_pb_column_1_4 .et_pb_countdown_timer .title { font-size: %spx; }", esc_html( $value * ( 18 / 22 ) ) );
						break;

					case 'font-style':
						printf( "%s { %s }\n", esc_html( $setting['selector'] ), et_pb_print_font_style( $value, $important ) );
						break;

					case 'color':
						printf( "%s { color: %s%s; }\n", esc_html( $setting['selector'] ), esc_html( $value ), $important );
						break;

					case 'background-color':
						printf( "%s { background-color: %s%s; }\n", esc_html( $setting['selector'] ), esc_html( $value ), $important );
						break;

					case 'border-radius':
						printf( "%s { -moz-border-radius: %spx; -webkit-border-radius: %spx; border-radius: %spx; }\n", esc_html( $setting['selector'] ), esc_html( $value ), esc_html( $value ), esc_html( $value ) );
						break;

					case 'width':
						printf( "%s { width: %spx; }\n", esc_html( $setting['selector'] ), esc_html( $value ) );
						break;

					case 'height':
						printf( "%s { height: %spx; }\n", esc_html( $setting['selector'] ), esc_html( $value ) );
						break;

					case 'padding':
						printf( "%s { padding: %spx; }\n", esc_html( $setting['selector'] ), esc_html( $value ) );
						break;

					case 'padding-top-bottom':
						printf( "%s { padding: %spx 0; }\n", esc_html( $setting['selector'] ), esc_html( $value ) );
						break;

					case 'padding-tabs':
						$padding_tab_top_bottom 	= intval( $value ) * 0.133333333;
						$padding_tab_active_top 	= $padding_tab_top_bottom + 1;
						$padding_tab_active_bottom 	= $padding_tab_top_bottom - 1;
						$padding_tab_content 		= intval( $value ) * 0.8;

						// negative result will cause layout issue
						if ( $padding_tab_active_bottom < 0 ) {
							$padding_tab_active_bottom = 0;
						}

						printf(
							".et_pb_tabs_controls li{ padding: %spx %spx %spx; } .et_pb_tabs_controls li.et_pb_tab_active{ padding: %spx %spx; } .et_pb_all_tabs { padding: %spx %spx; }\n",
							esc_html( $padding_tab_active_top ),
							esc_html( $value ),
							esc_html( $padding_tab_active_bottom ),
							esc_html( $padding_tab_top_bottom ),
							esc_html( $value ),
							esc_html( $padding_tab_content ),
							esc_html( $value )
						);
						break;

					case 'padding-slider':
						printf( "%s { padding-top: %s; padding-bottom: %s }\n", esc_html( $setting['selector'] ), esc_html( $value ) . '%', esc_html( $value ) . '%' );

						if ( 'et_pagebuilder_slider_padding' === $key ) {
							printf( '@media only screen and ( max-width: 767px ) { %1$s { padding-top: %2$s; padding-bottom: %2$s; } }', esc_html( $setting['selector'] ), '16%' );
						}
						break;

					case 'padding-call-to-action':
						$value = intval( $value );

						printf( ".et_pb_promo { padding: %spx %spx !important; }", esc_html( $value ), esc_html( $value * ( 60 / 40 ) ) );
						printf( ".et_pb_column_1_2 .et_pb_promo, .et_pb_column_1_3 .et_pb_promo, .et_pb_column_1_4 .et_pb_promo { padding: %spx; }", esc_html( $value ) );
						break;

					case 'social-icon-size':
						$icon_margin 	= intval( $value ) * 0.57;
						$icon_dimension = intval( $value ) * 2;
						?>
						.et_pb_social_media_follow li a.icon{
							margin-right: <?php echo esc_html( $icon_margin ); ?>px;
							width: <?php echo esc_html( $icon_dimension ); ?>px;
							height: <?php echo esc_html( $icon_dimension ); ?>px;
						}

						.et_pb_social_media_follow li a.icon::before{
							width: <?php echo esc_html( $icon_dimension ); ?>px;
							height: <?php echo esc_html( $icon_dimension ); ?>px;
							font-size: <?php echo esc_html( $value ); ?>px;
							line-height: <?php echo esc_html( $icon_dimension ); ?>px;
						}

						.et_pb_social_media_follow li a.follow_button{
							font-size: <?php echo esc_html( $value ); ?>px;
						}
						<?php
						break;

					case 'border-top-style':
						printf( "%s { border-top-style: %s; }\n", esc_html( $setting['selector'] ), esc_html( $value ) );
						break;

					case 'border-top-width':
						printf( "%s { border-top-width: %spx; }\n", esc_html( $setting['selector'] ), esc_html( $value ) );
						break;
				}
			}
		}
	}
}

/**
 * Outputting font-style attributes & values saved by ET_Divi_Font_Style_Option on customizer
 *
 * @return string
 */
function et_pb_print_font_style( $styles = '', $important = '' ) {

	// Prepare variable
	$font_styles = "";

	if ( '' !== $styles && false !== $styles ) {
		// Convert string into array
		$styles_array = explode( '|', $styles );

		// If $important is in use, give it a space
		if ( $important && '' !== $important ) {
			$important = " " . $important;
		}

		// Use in_array to find values in strings. Otherwise, display default text

		// Font weight
		if ( in_array( 'bold', $styles_array ) ) {
			$font_styles .= "font-weight: bold{$important}; ";
		} else {
			$font_styles .= "font-weight: normal{$important}; ";
		}

		// Font style
		if ( in_array( 'italic', $styles_array ) ) {
			$font_styles .= "font-style: italic{$important}; ";
		} else {
			$font_styles .= "font-style: normal{$important}; ";
		}

		// Text-transform
		if ( in_array( 'uppercase', $styles_array ) ) {
			$font_styles .= "text-transform: uppercase{$important}; ";
		} else {
			$font_styles .= "text-transform: none{$important}; ";
		}

		// Text-decoration
		if ( in_array( 'underline', $styles_array ) ) {
			$font_styles .= "text-decoration: underline{$important}; ";
		} else {
			$font_styles .= "text-decoration: none{$important}; ";
		}
	}

	return esc_html( $font_styles );
}

/*
 * Adds color scheme class to the body tag
 */
function et_customizer_color_scheme_class( $body_class ) {
	$color_scheme        = et_get_option( 'color_schemes', 'none' );
	$color_scheme_prefix = 'et_color_scheme_';

	if ( 'none' !== $color_scheme ) $body_class[] = $color_scheme_prefix . $color_scheme;

	return $body_class;
}
add_filter( 'body_class', 'et_customizer_color_scheme_class' );

/*
 * Adds button class to the body tag
 */
function et_customizer_button_class( $body_class ) {
	$button_icon_placement = et_get_option( 'all_buttons_icon_placement', 'right' );
	$button_icon_on_hover = et_get_option( 'all_buttons_icon_hover', 'yes' );
	$button_use_icon = et_get_option( 'all_buttons_icon', 'yes' );
	$button_icon = et_get_option( 'all_buttons_selected_icon', '5' );

	if ( 'left' === $button_icon_placement ) {
		$body_class[] = 'et_button_left';
	}

	if ( 'no' === $button_icon_on_hover ) {
		$body_class[] = 'et_button_icon_visible';
	}

	if ( 'no' === $button_use_icon ) {
		$body_class[] = 'et_button_no_icon';
	}

	if ( '5' !== $button_icon ) {
		$body_class[] = 'et_button_custom_icon';
	}

	$body_class[] = 'et_pb_button_helper_class';

	return $body_class;
}
add_filter( 'body_class', 'et_customizer_button_class' );

function et_load_google_fonts_scripts() {
	$theme_version = et_get_theme_version();

	wp_enqueue_script( 'et_google_fonts', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.js', array( 'jquery' ), $theme_version, true );
}
add_action( 'customize_controls_print_footer_scripts', 'et_load_google_fonts_scripts' );

function et_load_google_fonts_styles() {
	$theme_version = et_get_theme_version();

	wp_enqueue_style( 'et_google_fonts_style', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.css', array(), $theme_version );
}
add_action( 'customize_controls_print_styles', 'et_load_google_fonts_styles' );

if ( ! function_exists( 'et_divi_post_meta' ) ) :
function et_divi_post_meta() {
	$postinfo = is_single() ? et_get_option( 'divi_postinfo2' ) : et_get_option( 'divi_postinfo1' );

	if ( $postinfo ) :
		echo '<p class="post-meta">';
		echo et_pb_postinfo_meta( $postinfo, et_get_option( 'divi_date_format', 'M j, Y' ), esc_html__( '0 comments', 'Divi' ), esc_html__( '1 comment', 'Divi' ), '% ' . esc_html__( 'comments', 'Divi' ) );
		echo '</p>';
	endif;
}
endif;

function et_video_embed_html( $video ) {
	if ( is_single() && 'video' === et_pb_post_format() ) {
		static $post_video_num = 0;

		$post_video_num++;

		// Hide first video in the post content on single video post page
		if ( 1 === $post_video_num ) {
			return '';
		}
	}

	return "<div class='et_post_video'>{$video}</div>";
}

function et_do_video_embed_html(){
	add_filter( 'embed_oembed_html', 'et_video_embed_html' );
}
add_action( 'et_before_content', 'et_do_video_embed_html' );

/**
 * Removes galleries on single gallery posts, since we display images from all
 * galleries on top of the page
 */
function et_delete_post_gallery( $content ) {
	if ( ( is_single() || is_archive() ) && is_main_query() && has_post_format( 'gallery' ) ) :
		$regex = get_shortcode_regex();
		preg_match_all( "/{$regex}/s", $content, $matches );

		// $matches[2] holds an array of shortcodes names in the post
		foreach ( $matches[2] as $key => $shortcode_match ) {
			if ( 'gallery' === $shortcode_match ) {
				$content = str_replace( $matches[0][$key], '', $content );
				break;
			}
		}
	endif;

	return $content;
}
add_filter( 'the_content', 'et_delete_post_gallery' );

function et_divi_post_admin_scripts_styles( $hook ) {
	global $typenow;

	$theme_version = et_get_theme_version();
	$current_screen = get_current_screen();

	if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) return;

	if ( ! isset( $typenow ) ) return;

	if ( in_array( $typenow, array( 'post' ) ) ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'et-admin-post-script', get_template_directory_uri() . '/js/admin_post_settings.js', array( 'jquery' ), $theme_version );
	}

	// Only enqueue on editing screen
	if ( isset( $current_screen->base ) && 'post' === $current_screen->base ) {
		wp_enqueue_style( 'et-meta-box-style', get_template_directory_uri() . '/css/meta-box-styles.css', array(), $theme_version );
	}
}
add_action( 'admin_enqueue_scripts', 'et_divi_post_admin_scripts_styles' );

function et_password_form() {
	$pwbox_id = rand();

	$form_output = sprintf(
		'<div class="et_password_protected_form">
			<h1>%1$s</h1>
			<p>%2$s:</p>
			<form action="%3$s" method="post">
				<p><label for="%4$s">%5$s: </label><input name="post_password" id="%4$s" type="password" size="20" maxlength="20" /></p>
				<p><button type="submit" class="et_submit_button et_pb_button">%6$s</button></p>
			</form>
		</div>',
		esc_html__( 'Password Protected', 'Divi' ),
		esc_html__( 'To view this protected post, enter the password below', 'Divi' ),
		esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ),
		esc_attr( 'pwbox-' . $pwbox_id ),
		esc_html__( 'Password', 'Divi' ),
		esc_html__( 'Submit', 'Divi' )
	);

	$output = sprintf(
		'<div class="et_pb_section et_section_regular">
			<div class="et_pb_row">
				<div class="et_pb_column et_pb_column_4_4">
					%1$s
				</div>
			</div>
		</div>',
		$form_output
	);

	return $output;
}
add_filter( 'the_password_form', 'et_password_form' );

function et_add_wp_version( $classes ) {
	global $wp_version;

	$is_admin_body_class = 'admin_body_class' === current_filter();

	// add 'et-wp-pre-3_8' class if the current WordPress version is less than 3.8
	if ( version_compare( $wp_version, '3.7.2', '<=' ) ) {
		if ( 'body_class' === current_filter() ) {
			$classes[] = 'et-wp-pre-3_8';
		} else {
			$classes .= ' et-wp-pre-3_8';
		}
	} else if ( $is_admin_body_class ) {
		$classes .= ' et-wp-after-3_8';
	}

	if ( $is_admin_body_class ) {
		$classes = ltrim( $classes );
	}

	return $classes;
}
add_filter( 'body_class', 'et_add_wp_version' );
add_filter( 'admin_body_class', 'et_add_wp_version' );

/**
 * Determine whether current primary nav uses transparent nav or not based on primary nav background
 * @return bool
 */
function et_divi_is_transparent_primary_nav() {
	return 'rgba' == substr( et_get_option( 'primary_nav_bg', '#ffffff' ), 0, 4 );
}

function et_layout_body_class( $classes ) {
	$vertical_nav = et_get_option( 'vertical_nav', false );
	if ( et_divi_is_transparent_primary_nav() && ( false === $vertical_nav || '' === $vertical_nav ) ) {
		$classes[] = 'et_transparent_nav';
	}

	// home-posts class is used by customizer > blog to work. It modifies post title and meta
	// of WP default layout (home, archive, single), but should not modify post title and meta of blog module (page as home)
	if ( in_array( 'home', $classes ) && ! in_array( 'page', $classes ) ) {
		$classes[] = 'home-posts';
	}

	if ( true === et_get_option( 'nav_fullwidth', false ) ) {
		if ( true === et_get_option( 'vertical_nav', false ) ) {
			$classes[] = 'et_fullwidth_nav_temp';
		} else {
			$classes[] = 'et_fullwidth_nav';
		}
	}

	if ( true === et_get_option( 'secondary_nav_fullwidth', false ) ) {
		$classes[] = 'et_fullwidth_secondary_nav';
	}

	if ( true === et_get_option( 'vertical_nav', false ) ) {
		$classes[] = 'et_vertical_nav';
		if ( 'right' === et_get_option( 'vertical_nav_orientation', 'left' ) ) {
			$classes[] = 'et_vertical_right';
		}
	} else if ( 'on' === et_get_option( 'divi_fixed_nav', 'on' ) ) {
		$classes[] = 'et_fixed_nav';
	} else if ( 'on' !== et_get_option( 'divi_fixed_nav', 'on' ) ) {
		$classes[] = 'et_non_fixed_nav';
	}

	if ( true === et_get_option( 'vertical_nav', false ) && 'on' === et_get_option( 'divi_fixed_nav', 'on' ) ) {
		$classes[] = 'et_vertical_fixed';
	}

	if ( true === et_get_option( 'boxed_layout', false ) ) {
		$classes[] = 'et_boxed_layout';
	}

	if ( true === et_get_option( 'hide_nav', false ) && ( ! is_singular() || is_singular() && 'no' !== get_post_meta( get_the_ID(), '_et_pb_post_hide_nav', true ) ) ) {
		$classes[] = 'et_hide_nav';
	} else {
		$classes[] = 'et_show_nav';
	}

	if ( true === et_get_option( 'hide_primary_logo', false ) ) {
		$classes[] = 'et_hide_primary_logo';
	}

	if ( true === et_get_option( 'hide_fixed_logo', false ) ) {
		$classes[] = 'et_hide_fixed_logo';
	}

	if ( true === et_get_option( 'hide_mobile_logo', false ) ) {
		$classes[] = 'et_hide_mobile_logo';
	}

	if ( false !== et_get_option( 'cover_background', true ) ) {
		$classes[] = 'et_cover_background';
	}

	$et_secondary_nav_items = et_divi_get_top_nav_items();

	if ( $et_secondary_nav_items->top_info_defined && 'slide' !== et_get_option( 'header_style', 'left' ) && 'fullscreen' !== et_get_option( 'header_style', 'left' ) ) {
		$classes[] = 'et_secondary_nav_enabled';
	}

	if ( $et_secondary_nav_items->two_info_panels && 'slide' !== et_get_option( 'header_style', 'left' ) && 'fullscreen' !== et_get_option( 'header_style', 'left' ) ) {
		$classes[] = 'et_secondary_nav_two_panels';
	}

	if ( $et_secondary_nav_items->secondary_nav && ! ( $et_secondary_nav_items->contact_info_defined || $et_secondary_nav_items->show_header_social_icons ) && 'slide' !== et_get_option( 'header_style', 'left' ) && 'fullscreen' !== et_get_option( 'header_style', 'left' ) ) {
		$classes[] = 'et_secondary_nav_only_menu';
	}

	if ( 'on' === get_post_meta( get_the_ID(), '_et_pb_side_nav', true ) && et_pb_is_pagebuilder_used( get_the_ID() ) ) {
		$classes[] = 'et_pb_side_nav_page';
	}

	if ( true === et_get_option( 'et_pb_sidebar-remove_border', false ) ) {
		$classes[] = 'et_pb_no_sidebar_vertical_divider';
	}

	if ( is_singular( array( 'post', 'page', 'project', 'product' ) ) && 'on' == get_post_meta( get_the_ID(), '_et_pb_post_hide_nav', true ) ) {
		$classes[] = 'et_hide_nav';
	}

	if ( ! et_get_option( 'use_sidebar_width', false ) ) {
		$classes[] = 'et_pb_gutter';
	}

	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
		if ( stristr( $_SERVER['HTTP_USER_AGENT'], "mac" ) ) {
			$classes[] = 'osx';
		} elseif ( stristr( $_SERVER['HTTP_USER_AGENT'], "linux" ) ) {
			$classes[] = 'linux';
		} elseif ( stristr( $_SERVER['HTTP_USER_AGENT'], "windows" ) ) {
			$classes[] = 'windows';
		}
	}

	$gutter_width = '' !== ( $page_custom_gutter = get_post_meta( get_the_ID(), '_et_pb_gutter_width', true ) ) && is_singular() ? $page_custom_gutter :  et_get_option( 'gutter_width', '3' );
	$classes[] = esc_attr( "et_pb_gutters{$gutter_width}" );

	$primary_dropdown_animation = et_get_option( 'primary_nav_dropdown_animation', 'fade' );
	$classes[] = esc_attr( "et_primary_nav_dropdown_animation_{$primary_dropdown_animation}" );

	$secondary_dropdown_animation = et_get_option( 'secondary_nav_dropdown_animation', 'fade' );
	$classes[] = esc_attr( "et_secondary_nav_dropdown_animation_{$secondary_dropdown_animation}" );

	$footer_columns = et_get_option( 'footer_columns', '4' );
	$classes[] = esc_attr( "et_pb_footer_columns{$footer_columns}" );

	$header_style = et_get_option( 'header_style', 'left' );
	$classes[] = esc_attr( "et_header_style_{$header_style}" );

	if ( 'slide' === $header_style || 'fullscreen' === $header_style ) {
		$classes[] = esc_attr( "et_header_style_left" );
		if ( 'fullscreen' === $header_style && ! et_get_option( 'slide_nav_show_top_bar', true ) ) {
			// additional class if top bar disabled in Fullscreen menu
			$classes[] = esc_attr( "et_pb_no_top_bar_fullscreen" );
		}
	}

	$logo = et_get_option( 'divi_logo', '' );
	if ( '.svg' === substr( $logo, -4, 4 ) ) {
		$classes[] = 'et_pb_svg_logo';
	}

	// Add the page builder class.
	if ( et_pb_is_pagebuilder_used( get_the_ID() ) ) {
		$classes[] = 'et_pb_pagebuilder_layout';
	}

	return $classes;
}
add_filter( 'body_class', 'et_layout_body_class' );

if ( ! function_exists( 'et_show_cart_total' ) ) {
	function et_show_cart_total( $args = array() ) {
		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		$defaults = array(
			'no_text' => false,
		);

		$args = wp_parse_args( $args, $defaults );

		$items_number = WC()->cart->get_cart_contents_count();

		printf(
			'<a href="%1$s" class="et-cart-info">
				<span>%2$s</span>
			</a>',
			esc_url( WC()->cart->get_cart_url() ),
			( ! $args['no_text']
				? esc_html( sprintf(
					_nx( '%1$s Item', '%1$s Items', $items_number, 'WooCommerce items number', 'Divi' ),
					number_format_i18n( $items_number )
				) )
				: ''
			)
		);
	}
}

if ( ! function_exists( 'et_divi_get_top_nav_items' ) ) {
	function et_divi_get_top_nav_items() {
		$items = new stdClass;

		$items->phone_number = et_get_option( 'phone_number' );

		$items->email = et_get_option( 'header_email' );

		$items->contact_info_defined = $items->phone_number || $items->email;

		$items->show_header_social_icons = et_get_option( 'show_header_social_icons', false );

		$items->secondary_nav = wp_nav_menu( array(
			'theme_location' => 'secondary-menu',
			'container'      => '',
			'fallback_cb'    => '',
			'menu_id'        => 'et-secondary-nav',
			'echo'           => false,
		) );

		$items->top_info_defined = $items->contact_info_defined || $items->show_header_social_icons || $items->secondary_nav;

		$items->two_info_panels = $items->contact_info_defined && ( $items->show_header_social_icons || $items->secondary_nav );

		return $items;
	}
}

function et_divi_activate_features(){
	define( 'ET_SHORTCODES_VERSION', et_get_theme_version() );

	/* activate shortcodes */
	require_once( get_template_directory() . '/epanel/shortcodes/shortcodes.php' );
}
add_action( 'init', 'et_divi_activate_features' );

require_once( get_template_directory() . '/et-pagebuilder/et-pagebuilder.php' );

function et_divi_sidebar_class( $classes ) {

	// Set Woo shop and taxonomies layout.
	if ( class_exists( 'woocommerce' ) && ( is_woocommerce() && ( is_shop() || is_tax() ) ) ) {
		$page_layout = et_get_option( 'divi_shop_page_sidebar', 'et_right_sidebar' );
	}
	// Set post meta layout which will work for all third party plugins.
	elseif ( false == ( $page_layout = get_post_meta( get_queried_object_id(), '_et_pb_page_layout', true ) ) ) {
		$page_layout = 'et_right_sidebar';
	}

	// Add the page layout class.
	$classes[] = $page_layout;

	// Maybe add the full width portfolio class.
	if ( is_singular( 'project' ) && ( 'et_full_width_page' === $page_layout ) ) {
		$classes[] = 'et_full_width_portfolio_page';
	}

	return $classes;
}
add_filter( 'body_class', 'et_divi_sidebar_class' );

/**
 * Custom body classes for handling customizer preview screen
 * @return array
 */
function et_divi_customize_preview_class( $classes ) {
	if ( is_customize_preview() ) {
		// Search icon state
		if ( ! et_get_option( 'show_search_icon', true ) ) {
			$classes[] = 'et_hide_search_icon';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'et_divi_customize_preview_class' );

function et_modify_shop_page_columns_num( $columns_num ) {
	if ( class_exists( 'woocommerce' ) && is_shop() ) {
		$columns_num = 'et_full_width_page' !== et_get_option( 'divi_shop_page_sidebar', 'et_right_sidebar' )
			? 3
			: 4;
	}

	return $columns_num;
}
add_filter( 'loop_shop_columns', 'et_modify_shop_page_columns_num' );

// WooCommerce

global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) {
	// Prevent Cache Warning From Being Displayed On First Install
	$current_theme_version[ et_get_theme_version() ] = 'ignore' ;
	update_option( 'et_pb_cache_notice', $current_theme_version );

	add_action( 'init', 'et_divi_woocommerce_image_dimensions', 1 );
}

/**
 * Default values for WooCommerce images changed in version 1.3
 * Checks if WooCommerce image dimensions have been updated already.
 */
function et_divi_check_woocommerce_images() {
	if ( 'checked' === et_get_option( 'divi_1_3_images' ) ) return;

	et_divi_woocommerce_image_dimensions();
	et_update_option( 'divi_1_3_images', 'checked' );
}
add_action( 'admin_init', 'et_divi_check_woocommerce_images' );

function et_divi_woocommerce_image_dimensions() {
	$catalog = array(
		'width' 	=> '400',
		'height'	=> '400',
		'crop'		=> 1,
	);

	$single = array(
		'width' 	=> '510',
		'height'	=> '9999',
		'crop'		=> 0,
	);

	$thumbnail = array(
		'width' 	=> '157',
		'height'	=> '157',
		'crop'		=> 1,
	);

	update_option( 'shop_catalog_image_size', $catalog );
	update_option( 'shop_single_image_size', $single );
	update_option( 'shop_thumbnail_image_size', $thumbnail );
}

if ( ! function_exists( 'woocommerce_template_loop_product_thumbnail' ) ):
function woocommerce_template_loop_product_thumbnail() {
	printf( '<span class="et_shop_image">%1$s<span class="et_overlay"></span></span>',
		woocommerce_get_product_thumbnail()
	);
}
endif;

function et_review_gravatar_size( $size ) {
	return '80';
}
add_filter( 'woocommerce_review_gravatar_size', 'et_review_gravatar_size' );


function et_divi_output_content_wrapper() {
	echo '
		<div id="main-content">
			<div class="container">
				<div id="content-area" class="clearfix">
					<div id="left-area">';
}

function et_divi_output_content_wrapper_end() {
	echo '</div> <!-- #left-area -->';

	if (
		( is_product() && 'et_full_width_page' !== get_post_meta( get_the_ID(), '_et_pb_page_layout', true ) )
		||
		( ( is_shop() || is_product_category() || is_product_tag() ) && 'et_full_width_page' !== et_get_option( 'divi_shop_page_sidebar', 'et_right_sidebar' ) )
	) {
		woocommerce_get_sidebar();
	}

	echo '
				</div> <!-- #content-area -->
			</div> <!-- .container -->
		</div> <!-- #main-content -->';
}

function et_add_divi_menu() {
	$core_page = add_menu_page( 'Divi', 'Divi', 'switch_themes', 'et_divi_options', 'et_build_epanel' );

	// Add Theme Options menu only if it's enabled for current user
	if ( et_pb_is_allowed( 'theme_options' ) ) {

		if ( isset( $_GET['page'] ) && 'et_divi_options' === $_GET['page'] && isset( $_POST['action'] ) ) {
			if (
				( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'epanel_nonce' ) )
				||
				( 'reset' === $_POST['action'] && isset( $_POST['_wpnonce_reset'] ) && wp_verify_nonce( $_POST['_wpnonce_reset'], 'et-nojs-reset_epanel' ) )
			) {
				epanel_save_data( 'js_disabled' ); //saves data when javascript is disabled
			}
		}

		add_submenu_page( 'et_divi_options', esc_html__( 'Theme Options', 'Divi' ), esc_html__( 'Theme Options', 'Divi' ), 'manage_options', 'et_divi_options' );
	}
	// Add Theme Customizer menu only if it's enabled for current user
	if ( et_pb_is_allowed( 'theme_customizer' ) ) {
		add_submenu_page( 'et_divi_options', esc_html__( 'Theme Customizer', 'Divi' ), esc_html__( 'Theme Customizer', 'Divi' ), 'manage_options', 'customize.php?et_customizer_option_set=theme' );
	}
	// Add Module Customizer menu only if it's enabled for current user
	if ( et_pb_is_allowed( 'module_customizer' ) ) {
		add_submenu_page( 'et_divi_options', esc_html__( 'Module Customizer', 'Divi' ), esc_html__( 'Module Customizer', 'Divi' ), 'manage_options', 'customize.php?et_customizer_option_set=module' );
	}
	add_submenu_page( 'et_divi_options', esc_html__( 'Role Editor', 'Divi' ), esc_html__( 'Role Editor', 'Divi' ), 'manage_options', 'et_divi_role_editor', 'et_pb_display_role_editor' );
	// Add Divi Library menu only if it's enabled for current user
	if ( et_pb_is_allowed( 'divi_library' ) ) {
		add_submenu_page( 'et_divi_options', esc_html__( 'Divi Library', 'Divi' ), esc_html__( 'Divi Library', 'Divi' ), 'manage_options', 'edit.php?post_type=et_pb_layout' );
	}

	add_action( "load-{$core_page}", 'et_pb_check_options_access' ); // load function to check the permissions of current user
	add_action( "load-{$core_page}", 'et_epanel_hook_scripts' );
	add_action( "admin_print_scripts-{$core_page}", 'et_epanel_admin_js' );
	add_action( "admin_head-{$core_page}", 'et_epanel_css_admin');
	add_action( "admin_print_scripts-{$core_page}", 'et_epanel_media_upload_scripts');
	add_action( "admin_head-{$core_page}", 'et_epanel_media_upload_styles');
}
add_action('admin_menu', 'et_add_divi_menu');

function add_divi_customizer_admin_menu() {
	if ( ! current_user_can( 'customize' ) ) {
		return;
	}

	global $wp_admin_bar;

	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$customize_url = add_query_arg( 'url', urlencode( $current_url ), wp_customize_url() );

	// add Theme Customizer admin menu only if it's enabled for current user
	if ( et_pb_is_allowed( 'theme_customizer' ) ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'appearance',
			'id'     => 'customize-divi-theme',
			'title'  => esc_html__( 'Theme Customizer', 'Divi' ),
			'href'   => $customize_url . '&et_customizer_option_set=theme',
			'meta'   => array(
				'class' => 'hide-if-no-customize',
			),
		) );
	}

	// add Module Customizer admin menu only if it's enabled for current user
	if ( et_pb_is_allowed( 'module_customizer' ) ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'appearance',
			'id'     => 'customize-divi-module',
			'title'  => esc_html__( 'Module Customizer', 'Divi' ),
			'href'   => $customize_url . '&et_customizer_option_set=module',
			'meta'   => array(
				'class' => 'hide-if-no-customize',
			),
		) );
	}
	$wp_admin_bar->remove_menu( 'customize' );
}
add_action( 'admin_bar_menu', 'add_divi_customizer_admin_menu', 999 );

function et_pb_hide_options_menu() {
	// do nothing if theme options should be displayed in the menu
	if ( et_pb_is_allowed( 'theme_options' ) ) {
		return;
	}

	$theme_version = et_get_theme_version();

	wp_enqueue_script( 'divi-custom-admin-menu', get_template_directory_uri() . '/js/menu_fix.js', array( 'jquery' ), $theme_version, true );
}
add_action( 'admin_enqueue_scripts', 'et_pb_hide_options_menu' );

function et_pb_check_options_access() {
	// display wp error screen if theme customizer disabled for current user
	if ( ! et_pb_is_allowed( 'theme_options' ) ) {
		wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'Divi' ) );
	}
}

/**
 * Allowing blog and portfolio module pagination to work in non-hierarchical singular page.
 * Normally, WP_Query based modules wouldn't work in non-hierarchical single post type page
 * due to canonical redirect to prevent page duplication which could lead to SEO penalty.
 *
 * @see redirect_canonical()
 *
 * @return mixed string|bool
 */
function et_modify_canonical_redirect( $redirect_url, $requested_url ) {
	global $post;

	$allowed_shortcodes              = array( 'et_pb_blog', 'et_pb_portfolio' );
	$is_overwrite_canonical_redirect = false;

	// Look for $allowed_shortcodes in content. Once detected, set $is_overwrite_canonical_redirect to true
	foreach ( $allowed_shortcodes as $shortcode ) {
		if ( !empty( $post ) && has_shortcode( $post->post_content, $shortcode ) ) {
			$is_overwrite_canonical_redirect = true;
			break;
		}
	}

	// Only alter canonical redirect in 2 cases:
	// 1) If current page is singular, has paged and $allowed_shortcodes
	// 2) If current page is front_page, has page and $allowed_shortcodes
	if ( ( is_singular() & ! is_home() && get_query_var( 'paged' ) && $is_overwrite_canonical_redirect ) || ( is_front_page() && get_query_var( 'page' ) && $is_overwrite_canonical_redirect ) ) {
		return $requested_url;
	}

	return $redirect_url;
}
add_filter( 'redirect_canonical', 'et_modify_canonical_redirect', 10, 2 );

/**
 * Determines how many related products should be displayed on single product page
 * @param array  related products arguments
 * @return array modified related products arguments
 */
function et_divi_woocommerce_output_related_products_args( $args ) {
	$related_posts = 4; // default number

	if ( is_singular( 'product' ) ) {
		$page_layout = get_post_meta( get_the_ID(), '_et_pb_page_layout', true );

		if ( 'et_full_width_page' !== $page_layout ) {
			$related_posts = 3; // set to 3 if page has sidebar
		}
	}

	// Modify related and up-sell products args
	$args['posts_per_page'] = $related_posts;
	$args['columns']        = $related_posts;

	return $args;
}
add_filter( 'woocommerce_upsell_display_args', 'et_divi_woocommerce_output_related_products_args' );
add_filter( 'woocommerce_output_related_products_args', 'et_divi_woocommerce_output_related_products_args' );

function et_divi_maybe_change_frontend_locale( $locale ) {
	$option_name   = 'divi_disable_translations';
	$theme_options = get_option( 'et_divi' );

	$disable_translations = isset ( $theme_options[ $option_name ] ) ? $theme_options[ $option_name ] : false;

	if ( 'on' === $disable_translations ) {
		return 'en_US';
	}

	return $locale;
}
add_filter( 'locale', 'et_divi_maybe_change_frontend_locale' );

/**
 * Enable Divi gallery override if user activates it
 * @return bool
 */
function et_divi_gallery_layout_enable( $option ) {
	$setting = et_get_option( 'divi_gallery_layout_enable' );

	return ( 'on' === $setting ) ? true : $option;
}
add_filter( 'et_gallery_layout_enable', 'et_divi_gallery_layout_enable' );

function et_pb_get_all_categories() {
	// nonce check will be there..

	$all_cats = get_terms( 'project_category' );
	$all_cats_processed = array();

	foreach( $all_cats as $cat => $cat_data ) {
		$all_cats_processed[] = array(
			'id' => $cat_data->term_id,
			'name' => $cat_data->name,
		);
	}
	die( json_encode( $all_cats_processed ) );
}
add_action( 'wp_ajax_et_pb_get_all_categories', 'et_pb_get_all_categories' );

function et_pb_get_widget_areas_list() {
	// nonce check will be there..
	global $wp_registered_sidebars;
	$widget_areas_processed = array();

	foreach( $wp_registered_sidebars as $id => $options ) {
		$widget_areas_processed[] = array(
			'id' => $id,
			'name' => $options['name'],
		);
	}

	die( json_encode( $widget_areas_processed ) );
}
add_action( 'wp_ajax_et_pb_get_widget_areas_list', 'et_pb_get_widget_areas_list' );

/**
 * Register theme and modules Customizer portability.
 *
 * @since 2.7.0
 *
 * @return bool Always return true.
 */
function et_divi_register_customizer_portability() {
	global $options;

	// Make sure the Portability is loaded.
	et_core_load_component( 'portability' );

	// Load ePanel options.
	et_load_core_options();

	// Exclude ePanel options.
	$exclude = array();

	foreach ( $options as $option ) {
		if ( isset( $option['id'] ) ) {
			$exclude[ $option['id'] ] = true;
		}
	}

	// Register the portability.
	et_core_portability_register( 'et_divi_mods', array(
		'name'    => esc_html__( 'Divi Customizer Settings', 'Divi' ),
		'type'    => 'options',
		'target'  => 'et_divi',
		'exclude' => $exclude,
		'view'    => is_customize_preview(),
	) );
}
add_action( 'admin_init', 'et_divi_register_customizer_portability' );

function et_register_updates_component() {
	et_core_enable_automatic_updates( get_template_directory_uri(), ET_CORE_VERSION );
}
add_action( 'admin_init', 'et_register_updates_component' );

/**
 * Register theme and modules Customizer portability link.
 *
 * @since 2.7.0
 *
 * @return bool Always return true.
 */
function et_divi_customizer_link() {
	if ( is_customize_preview() ) {
		echo et_core_portability_link( 'et_divi_mods', array( 'class' => 'customize-controls-close' ) );
	}
}
add_action( 'customize_controls_print_footer_scripts', 'et_divi_customizer_link' );

/**
 * Added body class to make it possible to identify the Divi theme on frontend
 * @return array
 */
function et_divi_theme_body_class( $classes ) {
	$classes[] = 'et_divi_theme';

	return $classes;
}
add_filter( 'body_class', 'et_divi_theme_body_class' );

if ( ! function_exists( 'et_get_original_footer_credits' ) ) :
function et_get_original_footer_credits() {
	return sprintf( __( 'Designed by %1$s | Powered by %2$s', 'Divi' ), '<a href="http://www.elegantthemes.com" title="Premium WordPress Themes">Elegant Themes</a>', '<a href="http://www.wordpress.org">WordPress</a>' );
}
endif;

if ( ! function_exists( 'et_get_footer_credits' ) ) :
function et_get_footer_credits() {
	$original_footer_credits = et_get_original_footer_credits();

	$disable_custom_credits = et_get_option( 'disable_custom_footer_credits', false );

	if ( $disable_custom_credits ) {
		return '';
	}

	$credits_format = '<%2$s id="footer-info">%1$s</%2$s>';

	$footer_credits = et_get_option( 'custom_footer_credits', '' );

	if ( '' === trim( $footer_credits ) ) {
		return et_get_safe_localization( sprintf( $credits_format, $original_footer_credits, 'p' ) );
	}

	return et_get_safe_localization( sprintf( $credits_format, $footer_credits, 'div' ) );
}
endif;
