<?php
/**
 * Twenty8teen functions and definitions
 *
 * @package Twenty8teen
 */

$twenty8teen_widget_classes = null;

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 * Priority 0 to make it available to lower priority callbacks.
 * It is a separate function so it can be at a different priority and removed easily.
 * @global int $content_width
 */
function twenty8teen_content_width() {
	$adjust = is_active_sidebar( 'side-widget-area' ) ? 0.75 : 1;
	$max = intval( $adjust * 1800 );
	$large = intval( get_option( 'large_size_w', $max ) );
	$wide = $large ? min( $large, $max ) : $max;

	$GLOBALS['content_width'] = apply_filters( 'twenty8teen_content_width', $wide );
}
add_action( 'after_setup_theme', 'twenty8teen_content_width', 0 );

/**
 * Set up the theme.
 */
if ( ! function_exists( 'twenty8teen_setup' ) ) :
	function twenty8teen_setup() {
		global $content_width;

		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Twenty8teen, use a find and replace
		 * to change 'twenty8teen' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'twenty8teen' );

		$default_colors = twenty8teen_default_colors();

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array(
			'comment-list',
			'comment-form',
			'gallery',
			'caption',
			'search-form',
			'script',
			'style',
			'navigation-widgets',
		) );
		add_theme_support( 'custom-background',
			apply_filters( 'twenty8teen_custom_background_args', array(
				'default-color' => $default_colors['background_color'],
				'default-image' => '',
				'wp-head-callback' => 'twenty8teen_custom_background_style',
		) ) );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'custom-logo', array(
			'header-text' => array( 'site-title' ),
		) );
		add_theme_support( 'custom-header',
			apply_filters( 'twenty8teen_custom_header_args', array(
				'default-image'       => '',
				'default-text-color'  => $default_colors['header_textcolor'],
				'width'               => round( $content_width / 0.675, -1 ),
				'height'              => round( $content_width * 0.325, -1 ),
				'flex-width'          => true,
				'flex-height'         => true,
				'wp-head-callback'    => 'twenty8teen_header_text_style',
		) ) );

		add_editor_style( array( 'css/editor-style.css', twenty8teen_fonts_url() ) );

		register_nav_menus( array(
			'mainmenu'   => esc_html__( 'Main Menu', 'twenty8teen' ),
			'secondmenu' => esc_html__( 'Secondary Menu', 'twenty8teen' ),
		) );

		if ( is_customize_preview() ) {
			// Define and register starter content to showcase the theme on new sites.
			$starter_content = array(
				'widgets' => array(
					'header-widget-area' => array(
						'site_branding' => array( 'twenty8teen-template-part', array(
							'title' => 'Site Branding', 'part' => 'site-branding',
							'align' => 'center',
						) ),
						'header_img' => array( 'twenty8teen-template-part', array(
							'title' => 'Header Image', 'part' => 'header-image', 'align' => '',
							'class' => array( 'width-full' ),
						) ),
						'main_nav' => array( 'twenty8teen-template-part', array(
							'title' => 'Main Nav', 'part' => 'main-nav', 'align' => 'center',
							'class' => array( 'swap-color' ),
						) ),
					),
					'content-widget-area' => array(
						'loop' => array( 'twenty8teen-loop-part', array(
							'title' => 'Entry Header, Featured Image, Entry Content, Entry Footer, Post Navigation, Comments',
							'part' => array(
								'entry-header', 'featured-image', 'entry-content', 'entry-footer',
								'post-navigation', 'comments', '',
							),
							'align' => array( '', 'center', '', '', '', '', '' ),
						) ),
						'pagination' => array( 'twenty8teen-template-part', array(
							'title' => 'Posts Pagination', 'part' => 'posts-pagination',
							'align' => 'center',
						) ),
					),
					'side-widget-area' => array( 'search',
						'view_selector' => array( 'twenty8teen-template-part', array(
							'title' => 'View Selector', 'part' => 'view-selector',
							'align' => '',
						) ),
					),
					'footer-widget-area' => array(
						'site_copyright' => array( 'twenty8teen-template-part', array(
							'title' => 'Site Copyright', 'part' => 'site-copyright',
							'align' => 'center',
						) ),
						'jump_top' => array( 'twenty8teen-template-part', array(
							'title' => 'Jump To Top', 'part' => 'jump-to-top',
							'align' => 'right',
						) ),
					),
				),

				// Set some theme mods.
				'theme_mods' => array(
					'show_full_content' => false,
					'show_as_cards' => true,
					'show_header_identimage' => 'repeating-conic',
					'show_entry_header_identimage' => 'none',
					'show_featured_identimage' => 'radial',
				),

			);

			// * Filters Twenty8teen array of starter content.
			// * @param array $starter_content Array of starter content.
			$starter_content = apply_filters( 'twenty8teen_starter_content', $starter_content );
			add_theme_support( 'starter-content', $starter_content );
		}

	}
endif;
add_action( 'after_setup_theme', 'twenty8teen_setup' );

/**
 * Register the widget areas.
 */
function twenty8teen_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Header', 'twenty8teen' ),
		'id'            => 'header-widget-area',
		'description'   => esc_html__( 'Displayed at the top. If empty, the logo, title, description, and menu are shown.', 'twenty8teen' ),
		'before_widget' => '<div class="%2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Content', 'twenty8teen' ),
		'id'            => 'content-widget-area',
		'description'   => esc_html__( 'Displayed below Header. If empty, the content is shown.', 'twenty8teen' ),
		'before_widget' => '<div class="%2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'twenty8teen' ),
		'id'            => 'side-widget-area',
		'description'   => esc_html__( 'Displayed on one side of the Content. If empty, nothing is output.', 'twenty8teen' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer', 'twenty8teen' ),
		'id'            => 'footer-widget-area',
		'description'   => esc_html__( 'Displayed at the bottom. If empty, the site copyright is shown.', 'twenty8teen' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_widget( 'Twenty8teen_template_part_Widget' );
	register_widget( 'Twenty8teen_loop_part_Widget' );
}
add_action( 'widgets_init', 'twenty8teen_widgets_init' );

/**
 * Add the filter for each option in the conditional presets, except those excluded.
 */
function twenty8teen_add_conditional_preset_filters() {
	$combined = get_theme_support( 'twenty8teen_conditional_presets' );
	$exclude = apply_filters( 'twenty8teen_add_conditional_preset_filters',
 		array( 'page_conditional_presets' ) );
	foreach( $combined[0] as $mod => $val ) {
		if ( ! in_array( $mod, $exclude ) ) {
			add_filter( 'theme_mod_'.$mod, 'twenty8teen_preset_theme_mod' );
		}
	}
}

/**
 * Remove the filter for each option in the conditional presets.
 */
function twenty8teen_remove_conditional_preset_filters() {
	$combined = get_theme_support( 'twenty8teen_conditional_presets' );
	foreach( $combined[0] as $mod => $val ) {
		remove_filter( 'theme_mod_'.$mod, 'twenty8teen_preset_theme_mod' );
	}
}

/**
 * Set up for conditional presets after the query has been done.
 */
function twenty8teen_conditional_presets() {
	$presets = apply_filters( 'twenty8teen_conditional_presets', array() );
	$combined = array();
	foreach( $presets as $preset ) {
		$values = twenty8teen_option_preset( 'option_presets', $preset );
		$combined = array_merge( $combined, $values );
	}
	if ( count( $combined ) ) {
		add_theme_support( 'twenty8teen_conditional_presets', $combined );
		twenty8teen_add_conditional_preset_filters();
		do_action( 'twenty8teen_found_conditional_presets', $presets, $combined );
	}
}
add_action( 'wp', 'twenty8teen_conditional_presets' );

/**
 * Enqueue the styles and scripts for the front end and block editor.
 */
function twenty8teen_enqueue() {
	$booleans = twenty8teen_default_booleans();

	wp_enqueue_style( 'twenty8teen-fonts', twenty8teen_fonts_url(), array(), null );
	if ( get_theme_mod( 'show_icons', $booleans['show_icons'] ) ) {
		wp_enqueue_style( 'twenty8teen-auticons', get_template_directory_uri() .
			'/css/auticons/auticons' . (is_rtl()?'-rtl':'') . '.css', array(), '20190129' );
	}

	if ( 'wp_enqueue_scripts' === current_action() ) {
		$theme = wp_get_theme();
		$version = $theme->parent() ? $theme->parent()->get( 'Version' ) : $theme->get( 'Version' );
		wp_enqueue_style( 'twenty8teen-style', get_template_directory_uri() .
			'/style.css', array(), $version );
		if ( is_child_theme() ) {
			wp_enqueue_style( get_stylesheet() . '-style', get_stylesheet_uri(),
				array( 'twenty8teen-style' ), $theme->get( 'Version' ) );
		}

		wp_enqueue_script( 'twenty8teen-iframe-fix',
			get_template_directory_uri() . '/js/iframe-fix.js', array('jquery'), '20181226', true );
		wp_enqueue_script( 'twenty8teen-mouse-xy', get_template_directory_uri() . '/js/mouse-xy.js', 
			$booleans['use_prefixfree_script'] ? array( 'prefixfree-vars' ) : array(), '20200614', true );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
	else if ( is_admin() && ! is_customize_preview() ) {
		wp_enqueue_style( 'twenty8teen-editor', get_template_directory_uri() .
			'/css/editor-style.css', false, '20250303', 'all' );
		wp_add_inline_style( 'twenty8teen-editor', twenty8teen_dynamic_rules( true ) );

		// The following code is not a separate function because it _should_ be temporary.
		/* nodeInserted trick is from http://www.backalleycoder.com/2012/04/25/i-want-a-damnodeinserted/ 	*/
		$init_script = <<<JS
( function() {
	var insertListener = function(event) {
		if (event.animationName == "nodeInserted") {
			var el = document.querySelector('.editor-styles-wrapper, body:not(.wp-admin)') || document;
			if (el) {el.classList.add("%s");}
			el = document.querySelector('.wp-block-post-content, .editor-styles-wrapper .block-editor-writing-flow');
			if (el) {el.classList.add("%s");}
		}
	};
	document.addEventListener("animationstart", insertListener, false);
} )();
JS;
		$cards = get_theme_mod( 'show_as_cards', $booleans['show_as_cards'] ) ? ' cards' : '';
		$mainclasses = twenty8teen_area_classes( 'main', 'site-main', false );
		$contentclasses =	twenty8teen_area_classes( 'content', 'content-area' . $cards, false );
		$mainclasses = str_replace( ' ', '", "', $mainclasses );
		$contentclasses = str_replace( ' ', '", "', $contentclasses );
		$script = sprintf( $init_script, $mainclasses, $contentclasses );
		wp_register_script( 'twenty8teen-class-add', '',);
		wp_enqueue_script( 'twenty8teen-class-add' );
		wp_add_inline_script( 'twenty8teen-class-add', $script );

	}

	if ( $booleans['use_prefixfree_script'] ) {
		wp_enqueue_script( 'prefixfree', get_template_directory_uri() .
			'/js/prefixfree.js', array(), '1.0.7.3' );
		wp_enqueue_script( 'prefixfree-jquery', get_template_directory_uri() .
			'/js/prefixfree.jquery.js', array( 'prefixfree' ), '1.0.7' );
		wp_enqueue_script( 'prefixfree-vars', get_template_directory_uri() .
			'/js/prefixfree.vars.js', array( 'prefixfree' ), '1.0.7' );
		wp_enqueue_script( 'conic-gradient', get_template_directory_uri() .
			'/js/conic-gradient.js', array( 'prefixfree' ), '1.0', true );
	}
}
add_action( 'wp_enqueue_scripts', 'twenty8teen_enqueue' );
add_action( 'enqueue_block_assets', 'twenty8teen_enqueue' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function twenty8teen_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'twenty8teen_pingback_header' );

/**
 * Supply the defaults for font options.
 */
function twenty8teen_default_fonts() {
	return apply_filters( 'twenty8teen_default_fonts',
		array( 'body' => 'Convergence', 'titles' => 'Amarante' ) );
}

/**
 * Generate custom Google font URL
 */
function twenty8teen_fonts_url( $name_only = array() ) {
	$fonts_url = '';
	$query_args = array();
	if ( empty( $name_only ) ) {
		$defaults = twenty8teen_default_fonts();
		$font_families = array_merge( $defaults, get_theme_mod( 'google_fonts', $defaults ) );
	}
	else {
		$font_families = array_unique( (array) $name_only );
		$query_args['text'] = urlencode(
			join( '', array_unique( str_split( join( '', $name_only ) ) ) )
		);
	}
	$font_families = array_map( 'esc_attr', array_unique( array_filter( $font_families ) ) );
	if ( ! empty( $font_families ) ) {
		$query_args['family'] = urlencode( implode( '|', $font_families ) );
		$query_args['display'] = 'fallback';
		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}
	return esc_url_raw( apply_filters( 'twenty8teen_fonts_url', $fonts_url ) );
}

/**
 * Generate style rules for the fonts chosen.
 */
function twenty8teen_font_rules( $for_editor = false ) {
	$defaults = twenty8teen_default_fonts();
	$font_families = array_merge( $defaults, get_theme_mod( 'google_fonts', $defaults ) );
	$font_families = array_map( 'esc_attr', array_filter( $font_families ) );
	$css = isset( $font_families['body'] ) ?
		' :root { --body_font_family: "' . $font_families['body'] . '", sans-serif; }'
		: '';
	$css .= isset( $font_families['titles'] ) ?
		' :root { --titles_font_family: "' . $font_families['titles'] . '", serif; }'
		: '';
	return apply_filters( 'twenty8teen_font_rules', $css, $font_families, $for_editor );
}

/**
 * Supply the defaults for color options.
 */
function twenty8teen_default_colors() {
	$defaults = array(
		'header_textcolor' => '#6b0000',
		'background_color' => '#fef8ee',
		'accent_color' => '#d2b48c',
		'body_textcolor' => '#5e4422',
		'link_color' => '#1666f0',
	);
	return apply_filters( 'twenty8teen_default_colors', $defaults );
}

/**
 * Styles the background images, used as wp-head-callback for custom-background support.
 */
function twenty8teen_custom_background_style() {
	$style = array();
	$background = set_url_scheme( get_background_image() );
	$style['background-image'] = $background ? 'url("' . esc_url_raw( $background ) . '")' : "";

	// Background Position.
	$position_x = get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );
	if ( ! in_array( $position_x, array( 'left', 'center', 'right' ), true ) ) {
		$position_x = 'left';
	}
	$position_y = get_theme_mod( 'background_position_y', get_theme_support( 'custom-background', 'default-position-y' ) );
	if ( ! in_array( $position_y, array( 'top', 'center', 'bottom' ), true ) ) {
		$position_y = 'top';
	}
	$style['background-position'] = "$position_x $position_y";

	// Background Size.
	$size = get_theme_mod( 'background_size', get_theme_support( 'custom-background', 'default-size' ) );
	if ( ! in_array( $size, array( 'auto', 'contain', 'cover' ), true ) ) {
		$size = 'auto';
	}
	$style['background-size'] = $size;

	// Background Repeat.
	$repeat = get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );
	if ( ! in_array( $repeat, array( 'repeat-x', 'repeat-y', 'repeat', 'no-repeat' ), true ) ) {
		$repeat = 'repeat';
	}
	$style['background-repeat'] = $repeat;

	// Background Attachment.
	$attachment = get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );
	if ( 'fixed' !== $attachment ) {
		$attachment = 'scroll';
	}
	$style['background-attachment'] = $attachment;

	// Filter array to allow multiple backgrounds.
	$style = apply_filters( 'twenty8teen_custom_background_style', $style );

	if ( ! $style['background-image'] ) {
		if ( is_customize_preview() ) {
			echo '<style id="custom-background-css"></style>';
		}
		return;
	}

	?>
	<style id="custom-background-css">
		body.custom-background {
	<?php
		foreach( $style as $property => $value ) {
			echo "\t$property : $value;\n";
		}
	?>	}
	</style>
	<?php
}

/**
 * Styles the header text, used as wp-head-callback for custom-header support.
 */
function twenty8teen_header_text_style() {
	if ( ! display_header_text() ) : // Has the text been hidden? ?>
	<style>
		.site-title,
		.site-description { /* hide in an accessible way */
			position: absolute;
			clip: rect(1px, 1px, 1px, 1px);
		}
	</style>
	<?php endif;
}

/**
 * Generate the style rules for the dynamic options.
 */
function twenty8teen_dynamic_rules( $for_editor = false ) {
	global $content_width;
	$defaults = twenty8teen_default_colors();
	$ident_defaults = twenty8teen_default_identimages();
	$size_defaults = twenty8teen_default_sizes();
	$css = '';

	foreach ( $defaults as $option => $default ) {
		$color = get_theme_mod( $option, $default );
		$color = ( 'blank' === $color ) ? 'var(--body_textcolor)' : $color;
		$css .= ' --' . esc_attr( $option ) . ': ' . maybe_hash_hex_color( $color ) . ';';
	}
	$alpha = twenty8teen_sanitize_float( get_theme_mod( 'identimage_alpha',
		$ident_defaults['identimage_alpha'] ) );
	$css .= ' --identimage_alpha: ' . $alpha . ';';
	$adjust = twenty8teen_sanitize_float( get_theme_mod( 'font_size_adjust',
		$size_defaults['font_size_adjust'] ) );
	$adjust = ( $adjust <= 0.1 ) ? '1' : $adjust * 2;
	$css .= ' --font_size_adjust: ' . $adjust . ';';
	$css = ':root { ' . $css . ' }'; // This will override html in editor.
	$css .= twenty8teen_font_rules( $for_editor );

	if ( $for_editor ) {
		$css .= ".wp-block {max-width: $content_width" . "px}" . strip_tags( wp_get_custom_css() );
	}
	else {
		$css .= twenty8teen_img_size_rules();
	}
	return $css;
}

/**
 * Output the styles for the dynamic options.
 */
function twenty8teen_dynamic_style() {
	$css = twenty8teen_dynamic_rules();
	if ( $css ) :
?>
	<style>
	<?php echo $css; ?>
	</style>
<?php
	endif;
}
add_action( 'wp_head', 'twenty8teen_dynamic_style', 8 );

/**
 * Generate CSS for each defined imgsize.
 */
function twenty8teen_img_size_rules( $fallback = 0 ) {
	global $content_width;
	$fallback = $fallback ? min( absint( $fallback ), $content_width ) : $content_width;
	$phi = 0.618;
	$sizes = wp_get_additional_image_sizes();
	$size_names = get_intermediate_image_sizes();
	$css = '';
	foreach ( $size_names as $imgsize ) {
		if ( isset( $sizes[$imgsize] ) ) {
			$w = $sizes[$imgsize]['width'];
			$h = $sizes[$imgsize]['height'];
			$crop = $sizes[$imgsize]['crop'];
		}
		else {
			$w = get_option( $imgsize . '_size_w', $fallback );
			$h = get_option( $imgsize . '_size_h', $fallback * $phi );
			$crop = get_option( $imgsize . '_crop', false );
		}
		$w = absint( $w ) ? absint( $w ) : $fallback;
		$h = absint( $h ) ? absint( $h ) : $fallback * $phi;
		if ( $crop ) {
			$percent = $h / $w;
		}
		else {
		// Use Golden Ratio, mostly.
			$percent = $w >= $h ? $phi : 1.382;
			list( $w, $h ) = wp_constrain_dimensions( $w, $w * $percent, $w, $h );
		}
		$css .= '
		.wrapped-media-size-' . sanitize_html_class( $imgsize ) . ' {
			width: ' . $w . 'px;
			height: 0; padding-bottom: ' . round( $percent * 100, 2 ) . '%;	}
		';
	}
	return $css;
}

/**
 * Supply the defaults for size options.
 */
function twenty8teen_default_sizes() {
	$defaults = array(
		'featured_size_archives' => 'large',
		'featured_size_single' => 'medium',
		'excerpt_length' => 55,
		'font_size_adjust' => 0.54,
	);
	return apply_filters( 'twenty8teen_default_sizes', $defaults );
}

/**
 * Supply the defaults for boolean options.
 */
function twenty8teen_default_booleans() {
	$defaults = array(
		'show_full_content' => false,
		'show_header' => true,
		'show_vignette' => true,
		'show_icons' => false,
		'show_as_cards' => false,
		'switch_sidebar' => false,
		'show_header_imagebehind' => true,
		'start_in_tableview' => false,
		'show_sidebar' => true,
		'use_posttype_parts' => false,
		'use_prefixfree_script' => true,
	);
	return apply_filters( 'twenty8teen_default_booleans', $defaults );
}

/**
 * Supply the defaults for identimage options.
 */
function twenty8teen_default_identimages() {
	$defaults = array(
		'show_header_identimage' => 'repeating-conic',
		'show_entry_header_identimage' => 'linear',
		'show_featured_identimage' => 'none',
		'identimage_alpha' => 0.4,
		'featured_image_classes' => 'border-outset',
	);
	return apply_filters( 'twenty8teen_default_identimages', $defaults );
}

/**
 * Supply the defaults for area class options.
 */
function twenty8teen_default_area_classes() {
	$defaults = array(
		'header' => '',
		'main' => '',
		'content' => '',
		'comments' => 'font-smaller',
		'sidebar' => '',
		'widgets' => 'semi-white box',
		'footer' => '',
	);
	return apply_filters( 'twenty8teen_default_area_classes', $defaults );
}

/**
 * Get the preset values for the chosen option preset.
 */
function twenty8teen_option_preset( $mod_name, $which ) {
	$which = empty( $which ) ? 'none' : sanitize_text_field( $which );
	$values = array();
	switch ( $which ) {
		case 'defaults':
			$values = array_merge( twenty8teen_default_colors(),
				array( 'google_fonts' => twenty8teen_default_fonts() ),
				twenty8teen_default_booleans(),
				twenty8teen_default_identimages(), twenty8teen_default_sizes(),
				array( 'area_classes' => twenty8teen_default_area_classes() ),
				array( 'header_image' => '',
					'background_image' => '',
					'background_size' => 'auto',
					'background_repeat' => 'repeat',
					'background_attachment' => 'scroll',
					'custom_logo' => '',
				)
			);
			break;
		case 'noise_background_image':
			$values = array(
				'background_image' => get_template_directory_uri() . '/images/noise.png',
			);
			break;
		case 'dark':
			$values = array(
				'body_textcolor' => '#f9f7f7', 'background_color' => '#444244',
				'header_textcolor' => '#bf9a07', 'accent_color' => '#542f32',
				'link_color' => '#75e5dc', 'identimage_alpha' => 0.15,
				'area_classes' => array(
					'sidebar' => 'semi-black',
					'widgets' => 'box',
				),
			);
			break;
		case 'darkpurple':
			$values = array(
				'body_textcolor' => '#f2f9f3', 'background_color' => '#250444',
				'header_textcolor' => '#bf9a07', 'accent_color' => '#7a4267',
				'link_color' => '#a0e5df', 'identimage_alpha' => 0.2,
			);
			break;
		case 'sunny':
			$values = array(
				'header_textcolor' => '2616ce', 'background_color' => 'f9f9ea',
				'accent_color' => '#efeb94', 'body_textcolor' => '#6f18cc',
				'link_color' => '#137aef',
				'google_fonts' => array( 'body' => 'Poppins', 'titles' => 'Macondo Swash Caps' ),
				'font_size_adjust' => '0.54',
				'show_full_content' => false, 'show_vignette' => true,
				'show_icons' => false, 'show_header_identimage' => 'repeating-conic',
				'show_entry_header_identimage' => 'radial',
				'show_featured_identimage' => 'repeating-conic',
				'identimage_alpha' => '0.5', 'featured_size_archives' => 'large',
				'featured_size_single' => 'large',
				'area_classes' => array(
					'header' => 'rays', 'main' => '',
					'content' => '',
					'comments' => 'font-smaller box semi-white',
					'sidebar' => '',
					'widgets' => 'box semi-white swap-color',
					'footer' => 'swap-color' ),
			);
			break;
		case 'light':
			$values = array(
				'header_textcolor' => '065b2b', 'background_color' => 'ffffff',
				'accent_color' => '#2bcae2', 'body_textcolor' => '#541233',
				'link_color' => '#ba0d74',
				'google_fonts' => array( 'body' => 'Nobile', 'titles' => 'Original Surfer' ),
				'font_size_adjust' => '0.52',
				'show_full_content' => false, 'show_vignette' => false,
				'show_icons' => false, 'show_header_identimage' => 'repeating-conic',
				'show_entry_header_identimage' => 'none',
				'show_featured_identimage' => 'repeating-linear',
				'identimage_alpha' => '0.6',
				'featured_size_archives' => 'medium', 'featured_size_single' => 'medium',
				'area_classes' => array(
					'header' => '', 'main' => '',
					'content' => '', 'comments' => 'font-smaller',
					'sidebar' => '', 'widgets' => 'box noise',
					'footer' => 'font-smaller semi-white semi-black swap-color' ),
			);
			break;
		case 'old newspaper':
			$values = array(
				'header_textcolor' => '633029', 'background_color' => 'ededed',
				'accent_color' => '#efeeda', 'body_textcolor' => '#563a33',
				'link_color' => '#112ebf',
				'google_fonts' => array( 'body' => 'IM Fell Double Pica', 'titles' => 'Rye' ),
				'font_size_adjust' => '0.5',
				'show_full_content' => false, 'show_vignette' => true,
				'show_icons' => false, 'show_entry_header_identimage' => 'none',
				'identimage_alpha' => '0.8', 'featured_size_archives' => 'thumbnail',
				'featured_size_single' => 'medium',
				'area_classes' => array(
					'header' => 'font-larger noise',
					'main' => 'noise',
					'comments' => 'font-smaller',
					'footer' => 'noise' ),
			);
			break;
		case 'wide_round_fonts':
			$values = array( 'google_fonts' => array( 'body' => 'Short Stack', 'titles' => 'Gravitas One' ),
				'font_size_adjust' => '0.52', );
			break;
		case 'handwriting_fonts':
			$values = array( 'google_fonts' => array( 'body' => 'Architects Daughter', 'titles' => 'Rock Salt' ),
				'font_size_adjust' => '0.56', );
			break;
		case 'crisp_fonts':
			$values = array( 'google_fonts' => array( 'body' => 'Nobile', 'titles' => 'Prosto One' ),
				'font_size_adjust' => '0.5', );
			break;
		case 'Happy Monkey, Baumans':
			$values = array( 'google_fonts' => array( 'body' => 'Happy Monkey', 'titles' => 'Baumans' ),
				'font_size_adjust' => '0.51', );
			break;
		case 'casual_fonts':
			$values = array( 'google_fonts' => array( 'body' => 'McLaren', 'titles' => 'Cherry Cream Soda', ),
				'font_size_adjust' => '0.5', );
	}
	$all = get_theme_mod( $mod_name, array() );
	if ( isset( $all[$which] ) ) {
		$values = array();
		// Expand any array values from saved preset.
		foreach ($all[$which] as $akey => $avalue) {
			if ( preg_match( '/([^[]*)\[([^\]]+)\]/', $akey, $matches ) ) {
				$avalue = array( $matches[2] => $avalue );
				$values[$matches[1]] = isset( $values[$matches[1]] ) ?
					array_merge( $values[$matches[1]], $avalue ) :
					$avalue;
			}
			else {
				$values[$akey] = $avalue;
			}
		}
	}
	return apply_filters( 'twenty8teen_option_preset', $values, $which );
}

/**
 * Custom filters for this theme
 */
require get_template_directory() . '/inc/filters.php';

/**
 * Custom template tags for this theme
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions
 */
require get_template_directory() . '/inc/theme-customizer.php';

/**
 * Custom widgets
 */

/**
 * Set the global to the classes chosen for this widget.
 */
function twenty8teen_widget_set_classes( $template_file, $classes ) {
	global $twenty8teen_widget_classes;
	$twenty8teen_widget_classes =
		apply_filters( 'twenty8teen_widget_set_classes', $classes, $template_file );
}

/**
 * Get the classes chosen for this widget.
 */
function twenty8teen_widget_get_classes( $add = '', $echo = false ) {
	global $twenty8teen_widget_classes;
	if ( ! empty( $twenty8teen_widget_classes ) ) {
		$add .= ' ' . $twenty8teen_widget_classes;
	}
	$add = esc_attr( trim( $add ) );
	if ( $echo ) {
		echo $add ? ( 'class="' . $add . '"' ) : '';
	}
	return $add;
}

require get_template_directory() . '/inc/widgets.php';
