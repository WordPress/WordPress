<?php
/**
 * Twenty Twenty functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

/**
 * Table of Contents:
 * Theme Support
 * Required Files
 * Register Styles
 * Register Scripts
 * Register Menus
 * Custom Logo
 * WP Body Open
 * Register Sidebars
 * Enqueue Block editor assets
 * Classic Editor Style
 * Block editor settings
 */

if ( ! function_exists( 'twentytwenty_theme_support' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function twentytwenty_theme_support() {

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Custom background color.
		add_theme_support(
			'custom-background',
			array(
				'default-color' => 'f5efe0',
			)
		);

		// Set content-width.
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 580;
		}

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// Set post thumbnail size.
		set_post_thumbnail_size( 1200, 9999 );

		// Add custom image sizes.
		add_image_size( 'twentytwenty-fullscreen', 1980, 9999 );

		// Custom logo.
		$logo_id     = get_theme_mod( 'custom_logo' );
		$logo_width  = 120;
		$logo_height = 90;

		// If the retina setting is active, double the recommended width and height.
		if ( get_theme_mod( 'twentytwenty_retina_logo', false ) ) {
			$logo_width  = floor( $logo_width * 2 );
			$logo_height = floor( $logo_height * 2 );
		}

		add_theme_support(
			'custom-logo',
			array(
				'height'      => $logo_height,
				'width'       => $logo_width,
				'flex-height' => true,
				'flex-width'  => true,
				'header-text' => array( 'site-title', 'site-description' ),
			)
		);

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);

		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Twenty Nineteen, use a find and replace
		 * to change 'twentynineteen' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'twentytwenty' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Adds starter content to highlight the theme on fresh sites.
		add_theme_support( 'starter-content', twentytwenty_get_starter_content() );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/*
		 * Adds `async` and `defer` support for scripts registered or enqueued
		 * by the theme.
		 */
		$loader = new TwentyTwenty_Script_Loader();
		add_filter( 'script_loader_tag', [ $loader, 'filter_script_loader_tag' ], 10, 2 );

	}

	add_action( 'after_setup_theme', 'twentytwenty_theme_support' );

}

/**
 * REQUIRED FILES
 * Include required files.
 */
require get_template_directory() . '/inc/template-tags.php';

// Handle SVG icons.
require get_template_directory() . '/classes/class-twentytwenty-svg-icons.php';
require get_template_directory() . '/inc/svg-icons.php';

// Handle Customizer settings.
require get_template_directory() . '/classes/class-twentytwenty-customize.php';

// Require Separator Control class.
require get_template_directory() . '/classes/class-twentytwenty-separator-control.php';

// Custom comment walker.
require get_template_directory() . '/classes/class-twentytwenty-walker-comment.php';

// Custom page walker.
require get_template_directory() . '/classes/class-twentytwenty-walker-page.php';

// Custom script loader class.
require get_template_directory() . '/classes/class-twentytwenty-script-loader.php';

// Custom CSS.
require get_template_directory() . '/inc/custom-css.php';

// Custom starter content to highlight the theme on fresh sites.
require get_template_directory() . '/inc/starter-content.php';

if ( ! function_exists( 'twentytwenty_register_styles' ) ) {
	/**
	 * Register and Enqueue Styles.
	 */
	function twentytwenty_register_styles() {

		$theme_version    = wp_get_theme()->get( 'Version' );
		$css_dependencies = array();

		// By default, only load the Font Awesome fonts if the social menu is in use.
		$load_font_awesome = apply_filters( 'twentytwenty_load_font_awesome', has_nav_menu( 'social' ) );

		if ( $load_font_awesome ) {
			wp_register_style( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.css', false, '5.10.2', 'all' );
			$css_dependencies[] = 'font-awesome';
		}

		wp_enqueue_style( 'twentytwenty-style', get_template_directory_uri() . '/style.css', $css_dependencies, $theme_version );
		wp_style_add_data( 'twentytwenty-style', 'rtl', 'replace' );

		// Add output of Customizer settings as inline style.
		wp_add_inline_style( 'twentytwenty-style', twentytwenty_get_customizer_css( 'front-end' ) );

	}

	add_action( 'wp_enqueue_scripts', 'twentytwenty_register_styles' );

}

if ( ! function_exists( 'twentytwenty_register_scripts' ) ) {
	/**
	 * Register and Enqueue Scripts.
	 */
	function twentytwenty_register_scripts() {

		$theme_version = wp_get_theme()->get( 'Version' );

		if ( ( ! is_admin() ) && is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		wp_enqueue_script( 'twentytwenty-js', get_template_directory_uri() . '/assets/js/index.js', array(), $theme_version, false );
		wp_script_add_data( 'twentytwenty-js', 'async', true );

	}

	add_action( 'wp_enqueue_scripts', 'twentytwenty_register_scripts' );

}

if ( ! function_exists( 'twentytwenty_menus' ) ) {
	/**
	 * Register navigation menus uses wp_nav_menu in five places.
	 */
	function twentytwenty_menus() {

		$locations = array(
			'primary'  => __( 'Desktop Horizontal Menu', 'twentytwenty' ),
			'expanded' => __( 'Desktop Expanded Menu', 'twentytwenty' ),
			'mobile'   => __( 'Mobile Menu', 'twentytwenty' ),
			'footer'   => __( 'Footer Menu', 'twentytwenty' ),
			'social'   => __( 'Social Menu', 'twentytwenty' ),
		);

		register_nav_menus( $locations );
	}

	add_action( 'init', 'twentytwenty_menus' );

}

/**
 * Get the information about the logo.
 *
 * @param string $html The HTML output from get_custom_logo (core function).
 */
function twentytwenty_get_custom_logo( $html ) {

	$logo_id = get_theme_mod( 'custom_logo' );

	if ( ! $logo_id ) {
		return $html;
	}

	$logo = wp_get_attachment_image_src( $logo_id, 'full' );

	if ( $logo ) {
		// For clarity.
		$logo_width  = esc_attr( $logo[1] );
		$logo_height = esc_attr( $logo[2] );

		// If the retina logo setting is active, reduce the width/height by half.
		if ( get_theme_mod( 'retina_logo', false ) ) {
			$logo_width  = floor( $logo_width / 2 );
			$logo_height = floor( $logo_height / 2 );

			$search = array(
				'/width=\"\d+\"/iU',
				'/height=\"\d+\"/iU',
			);

			$replace = array(
				"width=\"{$logo_width}\"",
				"height=\"{$logo_height}\"",
			);

			$html = preg_replace( $search, $replace, $html );
		}
	}

	return $html;

}

add_filter( 'get_custom_logo', 'twentytwenty_get_custom_logo' );

if ( ! function_exists( 'wp_body_open' ) ) {

	/**
	 * Shim for wp_body_open, ensuring backwards compatibility with versions of WordPress older than 5.2.
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}

if ( ! function_exists( 'twentytwenty_skip_link' ) ) {

	/**
	 * Include a skip to content link at the top of the page so that users can bypass the menu.
	 */
	function twentytwenty_skip_link() {
		echo '<a class="skip-link faux-button screen-reader-text" href="#site-content">' . __( 'Skip to the content', 'twentytwenty' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core trusts translations
	}

	add_action( 'wp_body_open', 'twentytwenty_skip_link', 5 );

}

if ( ! function_exists( 'twentytwenty_sidebar_registration' ) ) {

	/**
	 * Register widget areas.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
	 */
	function twentytwenty_sidebar_registration() {

		// Arguments used in all register_sidebar() calls.
		$shared_args = array(
			'before_title'  => '<h2 class="widget-title subheading heading-size-3">',
			'after_title'   => '</h2>',
			'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
			'after_widget'  => '</div></div>',
		);

		// Footer #1.
		register_sidebar(
			array_merge(
				$shared_args,
				array(
					'name'        => __( 'Footer #1', 'twentytwenty' ),
					'id'          => 'footer-one',
					'description' => __( 'Widgets in this area will be displayed in the first column in the footer.', 'twentytwenty' ),
				)
			)
		);

		// Footer #2.
		register_sidebar(
			array_merge(
				$shared_args,
				array(
					'name'        => __( 'Footer #2', 'twentytwenty' ),
					'id'          => 'footer-two',
					'description' => __( 'Widgets in this area will be displayed in the second column in the footer.', 'twentytwenty' ),
				)
			)
		);

	}

	add_action( 'widgets_init', 'twentytwenty_sidebar_registration' );

}

if ( ! function_exists( 'twentytwenty_block_editor_styles' ) ) {

	/**
	 * Enqueue supplemental block editor styles.
	 */
	function twentytwenty_block_editor_styles() {

		$css_dependencies = array();

		// Enqueue the editor styles.
		wp_enqueue_style( 'twentytwenty-block-editor-styles', get_theme_file_uri( '/assets/css/editor-style-block.css' ), $css_dependencies, wp_get_theme()->get( 'Version' ), 'all' );
		wp_style_add_data( 'twentytwenty-block-editor-styles', 'rtl', 'replace' );

		// Add inline style from the Customizer.
		wp_add_inline_style( 'twentytwenty-block-editor-styles', twentytwenty_get_customizer_css( 'block-editor' ) );

	}

	add_action( 'enqueue_block_editor_assets', 'twentytwenty_block_editor_styles', 1, 1 );

}

if ( ! function_exists( 'twentytwenty_classic_editor_styles' ) ) {

	/**
	 * Enqueue classic editor styles.
	 */
	function twentytwenty_classic_editor_styles() {

		$classic_editor_styles = array(
			'/assets/css/editor-style-classic.css',
		);

		add_editor_style( $classic_editor_styles );

	}

	add_action( 'init', 'twentytwenty_classic_editor_styles' );

}

if ( ! function_exists( 'twentytwenty_add_classic_editor_customizer_styles' ) ) {

	/**
	 * Output Customizer Settings in the Classic Editor.
	 * Adds styles to the head of the TinyMCE iframe. Kudos to @Otto42 for the original solution.
	 *
	 * @param array $mce_init TinyMCE styles.
	 */
	function twentytwenty_add_classic_editor_customizer_styles( $mce_init ) {

		$styles = twentytwenty_get_customizer_css( 'classic-editor' );

		if ( ! isset( $mce_init['content_style'] ) ) {
			$mce_init['content_style'] = $styles . ' ';
		} else {
			$mce_init['content_style'] .= ' ' . $styles . ' ';
		}

		return $mce_init;

	}

	add_filter( 'tiny_mce_before_init', 'twentytwenty_add_classic_editor_customizer_styles' );

}

if ( ! function_exists( 'twentytwenty_block_editor_settings' ) ) {

	/**
	 * Block Editor Settings.
	 * Add custom colors and font sizes to the block editor.
	 */
	function twentytwenty_block_editor_settings() {

		// Block Editor Palette.
		$editor_color_palette = array(
			array(
				'name'  => esc_html__( 'Accent Color', 'twentytwenty' ),
				'slug'  => 'accent',
				'color' => twentytwenty_get_color_for_area( 'content', 'accent' ),
			),
			array(
				'name'  => esc_html__( 'Secondary', 'twentytwenty' ),
				'slug'  => 'secondary',
				'color' => twentytwenty_get_color_for_area( 'content', 'secondary' ),
			),
			array(
				'name'  => esc_html__( 'Subtle Background', 'twentytwenty' ),
				'slug'  => 'subtle-background',
				'color' => twentytwenty_get_color_for_area( 'content', 'borders' ),
			),
		);

		// Get the color options.
		$accent_color_options = TwentyTwenty_Customize::get_color_options();

		// Loop over them and construct an array for the editor-color-palette.
		if ( $accent_color_options ) {
			foreach ( $accent_color_options as $color_option_name => $color_option ) {
				$editor_color_palette[] = array(
					'name'  => $color_option['label'],
					'slug'  => $color_option['slug'],
					'color' => get_theme_mod( $color_option_name, $color_option['default'] ),
				);
			}
		}

		// Add the background option.
		$background_color = get_theme_mod( 'background_color' );
		if ( ! $background_color ) {
			$background_color_arr = get_theme_support( 'custom-background' );
			$background_color     = $background_color_arr[0]['default-color'];
		}
		$editor_color_palette[] = array(
			'name'  => __( 'Background Color', 'twentytwenty' ),
			'slug'  => 'background',
			'color' => '#' . $background_color,
		);

		// If we have accent colors, add them to the block editor palette.
		if ( $editor_color_palette ) {
			add_theme_support( 'editor-color-palette', $editor_color_palette );
		}

		// the block editor Font Sizes.
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => _x( 'Small', 'Name of the small font size in the block editor', 'twentytwenty' ),
					'shortName' => _x( 'S', 'Short name of the small font size in the block editor.', 'twentytwenty' ),
					'size'      => 16,
					'slug'      => 'small',
				),
				array(
					'name'      => _x( 'Regular', 'Name of the regular font size in the block editor', 'twentytwenty' ),
					'shortName' => _x( 'M', 'Short name of the regular font size in the block editor.', 'twentytwenty' ),
					'size'      => 18,
					'slug'      => 'regular',
				),
				array(
					'name'      => _x( 'Large', 'Name of the large font size in the block editor', 'twentytwenty' ),
					'shortName' => _x( 'L', 'Short name of the large font size in the block editor.', 'twentytwenty' ),
					'size'      => 24,
					'slug'      => 'large',
				),
				array(
					'name'      => _x( 'Larger', 'Name of the larger font size in the block editor', 'twentytwenty' ),
					'shortName' => _x( 'XL', 'Short name of the larger font size in the block editor.', 'twentytwenty' ),
					'size'      => 32,
					'slug'      => 'larger',
				),
			)
		);

		// If we have a dark background color then add support for dark editor style.
		// We can determine if the background color is dark by checking if the text-color is white.
		if ( '#ffffff' === strtolower( twentytwenty_get_color_for_area( 'content', 'text' ) ) ) {
			add_theme_support( 'dark-editor-style' );
		}

	}

	add_action( 'after_setup_theme', 'twentytwenty_block_editor_settings' );

}

if ( ! function_exists( 'twentytwenty_read_more_tag' ) ) {

	/**
	 * Read More Link
	 * Overwrite default (more ...) tag
	 */
	function twentytwenty_read_more_tag() {
		return sprintf(
			'<a href="%1$s" class="more-link faux-button">%2$s <span class="screen-reader-text">"%3$s"</span></a>',
			esc_url( get_permalink( get_the_ID() ) ),
			esc_html__( 'Continue reading', 'twentytwenty' ),
			get_the_title( get_the_ID() )
		);
	}
	add_filter( 'the_content_more_link', 'twentytwenty_read_more_tag' );

}

if ( ! function_exists( 'twentytwenty_customize_controls_enqueue_scripts' ) ) {
	/**
	 * Enqueues scripts for customizer controls & settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function twentytwenty_customize_controls_enqueue_scripts() {
		$theme_version = wp_get_theme()->get( 'Version' );

		// Add script for color calculations.
		wp_enqueue_script( 'twentytwenty-color-calculations', get_template_directory_uri() . '/assets/js/color-calculations.js', array( 'wp-color-picker' ), $theme_version, false );

		// Add script for controls.
		wp_enqueue_script( 'twentytwenty-customize-controls', get_template_directory_uri() . '/assets/js/customize-controls.js', array( 'twentytwenty-color-calculations', 'customize-controls', 'underscore', 'jquery' ), $theme_version, false );
		wp_localize_script( 'twentytwenty-customize-controls', 'backgroundColors', twentytwenty_get_customizer_color_vars() );
	}

	add_action( 'customize_controls_enqueue_scripts', 'twentytwenty_customize_controls_enqueue_scripts' );
}

if ( ! function_exists( 'twentytwenty_customize_preview_init' ) ) {
	/**
	 * Enqueue scripts for the customizer preview.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function twentytwenty_customize_preview_init() {
		$theme_version = wp_get_theme()->get( 'Version' );

		wp_enqueue_script( 'twentytwenty-customize-preview', get_theme_file_uri( '/assets/js/customize-preview.js' ), array( 'customize-preview', 'jquery' ), $theme_version, true );
		wp_localize_script( 'twentytwenty-customize-preview', 'backgroundColors', twentytwenty_get_customizer_color_vars() );
		wp_localize_script( 'twentytwenty-customize-preview', 'previewElements', twentytwenty_get_elements_array() );
	}

	add_action( 'customize_preview_init', 'twentytwenty_customize_preview_init' );
}

if ( ! function_exists( 'twentytwenty_get_color_for_area' ) ) {
	/**
	 * Get accessible color for an area.
	 *
	 * @since 1.0.0
	 *
	 * @param string $area The area we want to get the colors for.
	 * @param string $context Can be 'text' or 'accent'.
	 * @return string Returns a HEX color.
	 */
	function twentytwenty_get_color_for_area( $area = 'content', $context = 'text' ) {

		// Get the value from the theme-mod.
		$settings = get_theme_mod(
			'accent_accessible_colors',
			array(
				'content'       => array(
					'text'      => '#000000',
					'accent'    => '#cd2653',
					'secondary' => '#6d6d6d',
					'borders'   => '#dcd7ca',
				),
				'header-footer' => array(
					'text'      => '#000000',
					'accent'    => '#cd2653',
					'secondary' => '#6d6d6d',
					'borders'   => '#dcd7ca',
				),
			)
		);

		// If we have a value return it.
		if ( isset( $settings[ $area ] ) && isset( $settings[ $area ][ $context ] ) ) {
			return $settings[ $area ][ $context ];
		}

		// Return false if the option doesn't exist.
		return false;
	}
}

if ( ! function_exists( 'twentytwenty_get_customizer_color_vars' ) ) {

	/**
	 * Returns an array of variables for the customizer preview.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function twentytwenty_get_customizer_color_vars() {
		$colors = array(
			'content'       => array(
				'setting' => 'background_color',
			),
			'header-footer' => array(
				'setting' => 'header_footer_background_color',
			),
		);
		return $colors;
	}
}

if ( ! function_exists( 'twentytwenty_get_elements_array' ) ) {

	/**
	 * Get an array of elements.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	function twentytwenty_get_elements_array() {

		// The array is formatted like this:
		// [key-in-saved-setting][sub-key-in-setting][css-property] = [elements].
		$elements = array(
			'content'       => array(
				'accent'     => array(
					'color'            => array( '.color-accent', '.color-accent-hover:hover', '.has-accent-color', '.has-drop-cap:not(:focus):first-letter', '.wp-block-button.is-style-outline', 'a' ),
					'border-color'     => array( 'blockquote', '.border-color-accent', '.border-color-accent-hover:hover' ),
					'background'       => array( 'button:not(.toggle)', '.button', '.faux-button', '.wp-block-button__link', '.wp-block-file__button', 'input[type="button"]', 'input[type="reset"]', 'input[type="submit"]' ),
					'background-color' => array( '.bg-accent', '.bg-accent-hover:hover', '.has-accent-background-color', '.comment-reply-link', '.edit-comment-link' ),
					'fill'             => array( '.fill-children-accent', '.fill-children-accent *' ),
				),
				'background' => array(
					'color'      => array( 'button', '.button', '.faux-button', '.wp-block-button__link', '.wp-block-file__button', 'input[type="button"]', 'input[type="reset"]', 'input[type="submit"]', '.comment-reply-link', '.edit-comment-link' ),
					'background' => array(),
				),
				'text'       => array(
					'color' => array( 'body', '.entry-title a' ),
				),
				'secondary'  => array(
					'color' => array( 'cite', 'figcaption', '.wp-caption-text', '.post-meta', '.entry-content .wp-block-archives li', '.entry-content .wp-block-categories li', '.entry-content .wp-block-latest-posts li', '.wp-block-latest-comments__comment-date', '.wp-block-latest-posts__post-date', '.wp-block-embed figcaption', '.wp-block-image figcaption', '.wp-block-pullquote cite', '.comment-metadata', '.comment-respond .comment-notes', '.comment-respond .logged-in-as', '.pagination .dots' ),
				),
				'borders'    => array(
					'border-color'        => array( 'pre', 'fieldset', 'input', 'textarea', 'table', 'table *' ),
					'background'          => array( 'caption', 'code', 'code', 'kbd', 'samp' ),
					'border-bottom-color' => array( '.wp-block-table.is-style-stripes' ),
					'border-top-color'    => array( '.wp-block-latest-posts.is-grid li' ),
					'color'               => array( 'hr:not(.is-style-dots):not(.reset-hr)' ),
				),
			),
			'header-footer' => array(
				'accent'     => array(
					'color'      => array( 'body:not(.overlay-header) .primary-menu > li > a', 'body:not(.overlay-header) .primary-menu > li > .icon', '.modal-menu a', '.footer-menu a, .footer-widgets a', '#site-footer .wp-block-button.is-style-outline', '.wp-block-pullquote:before', '.singular .entry-header a', '.archive-header a', '.header-footer-group .color-accent', '.header-footer-group .color-accent-hover:hover' ),
					'background' => array( '.social-icons a', '#site-footer button:not(.toggle)', '#site-footer .button', '#site-footer .faux-button', '#site-footer .wp-block-button__link', '#site-footer .wp-block-file__button', '#site-footer input[type="button"]', '#site-footer input[type="reset"]', '#site-footer input[type="submit"]' ),
				),
				'background' => array(
					'color'      => array( '.social-icons a', '.overlay-header:not(.showing-menu-modal) .header-inner', '.primary-menu ul', '.header-footer-group button', '.header-footer-group .button', '.header-footer-group .faux-button', '.header-footer-group .wp-block-button:not(.is-style-outline) .wp-block-button__link', '.header-footer-group .wp-block-file__button', '.header-footer-group input[type="button"]', '.header-footer-group input[type="reset"]', '.header-footer-group input[type="submit"]' ),
					'background' => array( '#site-header', '#site-footer', '.menu-modal', '.menu-modal-inner', '.search-modal-inner', '.archive-header', '.singular .entry-header', '.singular .featured-media:before', '.wp-block-pullquote:before' ),
				),
				'text'       => array(
					'color'               => array( '.header-footer-group', 'body:not(.overlay-header) #site-header .toggle', '.menu-modal .toggle' ),
					'background'          => array( 'body:not(.overlay-header) .primary-menu ul' ),
					'border-bottom-color' => array( 'body:not(.overlay-header) .primary-menu > li > ul:after' ),
					'border-left-color'   => array( 'body:not(.overlay-header) .primary-menu ul ul:after' ),
				),
				'secondary'  => array(
					'color' => array( '.site-description', '.toggle-inner .toggle-text', '.widget .post-date', '.widget .rss-date', '.widget_archive li', '.widget_categories li', '.widget cite', '.widget_pages li', '.widget_meta li', '.widget_nav_menu li', '.powered-by-wordpress', '.to-the-top', '.singular .entry-header .post-meta', '.singular .entry-header .post-meta a' ),
				),
				'borders'    => array(
					'border-color'        => array( '.header-footer-group pre', '.header-footer-group fieldset', '.header-footer-group input', '.header-footer-group textarea', '.header-footer-group table', '.header-footer-group table *', '.menu-modal nav *', '.footer-widgets-outer-wrapper', '.footer-top' ),
					'background'          => array( '.header-footer-group table caption', 'body:not(.overlay-header) .header-inner .toggle-wrapper::before' ),
					'border-bottom-color' => array( '.wp-block-table.is-style-stripes' ),
					'border-top-color'    => array( '.wp-block-latest-posts.is-grid li' ),
				),
			),
		);

		return apply_filters( 'twentytwenty_get_elements_array', $elements );
	}
}
