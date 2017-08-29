<?php
/**
 * noteblog Theme Customizer
 *
 * Please browse readme.txt for credits and forking information
 *
 * @package noteblog
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function noteblog_customize_register( $wp_customize ) {

	//get the current color value for accent color
	$color_scheme = noteblog_get_color_scheme();
	//get the default color for current color scheme
	$current_color_scheme = noteblog_current_color_scheme_default_color();

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->remove_control('display_header_text');
	$wp_customize->get_section('header_image')->title = __( 'Front Page Header', 'noteblog' );
	$wp_customize->get_section('colors')->title = __( 'Background Color', 'noteblog' );





function noteblog_customizer_stylesheet() {
	
	wp_enqueue_style( 'noteblog-customizer-css', get_template_directory_uri().'/css/customize-css.css', NULL, NULL, 'all' );
	
}


	$wp_customize->add_section(
		'noteblog_new',
		array(
			'title' => __('Noteblog Pro', 'noteblog'),
			'priority' => 999,
			'description' => __('<a href="https://lighthouseseooptimization.github.io/wordpress/noteblog/" target="_blank"><img src="https://lighthouseseooptimization.github.io/wordpress/noteblog/img/small-info.png"></a><p><strong>Got questions or need help?</strong></p><a href="https://lighthouseseooptimization.github.io/wordpress/noteblog/#contact" target="_blank">Email us here</a> or write to us directly at: Beseenseo@gmail.com <br><br> Upgrade to noteblog Pro to get 30+ more features! ', 'noteblog') . '<a href="https://lighthouseseooptimization.github.io/wordpress/noteblog/" target="_blank">Upgrade now!</a> <br> 

			<p><strong>Extra features:</strong></p>
			<ul>
			<li>Custom Header Title & Tagline</li>
			<li>The Best SEO Plugins</li>
			<li>350 Milliseconds Load Time</li>
			<li>Pefect SEO Optimization</li>
			<li>Show Header On All Pages</li>
			<li>Custom Header Height</li>
			<li>Header Text</li>
			<li>Header Background Image/Color</li>
			<li>Footer Copyright Text</li>
			<li>Navigation Colors</li>
			<li>Front Page Header Colors</li>
			<li>Logo</li>
			<li>Blog Feed Colors</li>
			<li>Post Colors</li>
			<li>Page Colors</li>
			<li>Sidebar Colors</li>
			<li>Background Image</li>
			<li>Footer Colors</li>
			<li>Footer Widgets</li>
			<li>Sidebar Widgets</li>
			<li>Sidebar Template</li>
			<li>Full Width Template</li>
			<li>Global Theme Colors</li>
			<li>Front Page Menu Logo Color</li>
			<li>Front Page Menu Link Color</li>
			<li>Subpage Logo Color</li>
			<li>Navigation Background Color</li>
			<li>Front Page Header Title</li>
			<li>Front Page Header Tagline</li>
			<li>Front Page Header Title Color</li>
			<li>Front Page Header Tagline Color</li>
			<li>Front Page Header Background Image</li>
			<li>Front Page Header Background Color</li>
			<li>Blog Feed: Background Color</li>
			<li>Blog Feed: Text Color</li>
			<li>Blog Feed: Post Date Color</li>
			<li>Blog Feed: Next/Prev Button Color</li>
			<li>Post/Page Byline Color</li>
			<li>Post/Page Headline Color</li>
			<li>Post/Page Paragraph Color</li>
			<li>Post/Page Link Color</li>
			<li>Post/Page Background Color</li>
			<li>Sidebar Background Color</li>
			<li>Sidebar Headline Color</li>
			<li>Sidebar Link Color</li>
			<li>Sidebar Text Color</li>
			<li>Sidebar Widgets</li>
			<li>Footer Copyright Text</li>
			<li>Footer Background Color</li>
			<li>Footer Text Color</li>
			<li>Footer Headline Color</li>
			<li>Footer Link Color</li>
			<li>Footer Border Color</li>
			<li>Unlimited Customer Support</li>
			<li>Free Updates Forever</li>
			</ul>
			<a class="button button-primary" href="https://lighthouseseooptimization.github.io/wordpress/noteblog/" target="_blank">Upgrade now for $32</a>
			',
			)
		);  
	$wp_customize->add_setting('noteblog_options[info]', array(
		'sanitize_callback' => 'noteblog_no_sanitize',
		'type' => 'info_control',
		'capability' => 'edit_theme_options',
		)
	);
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'pro_section', array(
		'section' => 'noteblog_new',
		'settings' => 'noteblog_options[info]',
		'type' => 'textarea',
		'priority' => 109
		) )
	);   

	$wp_customize->add_section(
		'noteblog_contact',
		array(
			'title' => __('Help and Support', 'noteblog'),
			'priority' => 995,
			'description' => __('Have questions or need help? ', 'noteblog') . '<a href="https://lighthouseseooptimization.github.io/wordpress/noteblog/#contact" target="_blank">Email us here</a> or write to us directly at: Beseenseo@gmail.com <br><br> <p><strong>Theme documentation</strong><br> Documentation can be found in the admin sidebar under Appearance > Noteblog</p><br><a href="https://lighthouseseooptimization.github.io/wordpress/noteblog/" target="_blank"><img src="https://lighthouseseooptimization.github.io/wordpress/noteblog/img/small-info.png"></a>',
			)
		);  
	$wp_customize->add_setting('noteblog_contact[info]', array(
		'sanitize_callback' => 'noteblog_no_sanitize',
		'type' => 'info_control',
		'capability' => 'edit_theme_options',
		)
	);
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'contact_section', array(
		'section' => 'noteblog_contact',
		'settings' => 'noteblog_contact[info]',
		'type' => 'textarea',
		'priority' => 105
		) )
	);   



	//Header Background Color setting
	$wp_customize->add_setting( 'header_bg_color', array(
		'default'           => '#1b1b1b',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
		) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_bg_color', array(
		'label'       => __( 'Header Background Color', 'noteblog' ),
		'description' => __( 'Applied to header background.', 'noteblog' ),
		'section'     => 'header_image',
		'settings'    => 'header_bg_color',
		) ) );

	$wp_customize->add_section( 'site_identity' , array(
		'priority'   => 3,
		));

	$wp_customize->add_section( 'header_image' , array(
		'title'      => __('Front Page Header', 'noteblog'),
		'priority'   => 4,
		));

	$wp_customize->add_setting( 'header_image_text_color', array(
		'default'           => '#fff',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
		) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_image_text_color', array(
		'label'       => __( 'Header Image Headline Color', 'noteblog' ),
		'description' => __( 'Choose a color for the header image headline.', 'noteblog' ),
		'priority' 			=> 2,
		'section'     => 'header_image',
		'settings'    => 'header_image_text_color',
		) ) );

	$wp_customize->add_setting( 'header_image_tagline_color', array(
		'default'           => '#fff',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
		) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_image_tagline_color', array(
		'label'       => __( 'Header Image Tagline Color', 'noteblog' ),
		'description' => __( 'Choose a color for the header tagline headline.', 'noteblog' ),
		'section'     => 'header_image',
		'priority'   => 2,
		'settings'    => 'header_image_tagline_color',
		) ) );


	$wp_customize->add_setting( 'hero_image_title', array(
		'type'              => 'theme_mod',
		'sanitize_callback' => 'wp_kses_post',
		'capability'        => 'edit_theme_options',
		'default'  => '',
		) );

	$wp_customize->add_control( 'hero_image_title', array(
		'label'    => __( "Header Image Title", 'noteblog' ),
		'section'  => 'header_images',
		'type'     => 'text',
		'priority' => 1,
		) );

	$wp_customize->add_setting( 'hero_image_subtitle', array(
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'wp_kses_post',
		'default'  => '',
		) );

	$wp_customize->add_control( 'hero_image_subtitle', array(
		'label'    => __( "Header Image Tagline", 'noteblog' ),
		'section'  => 'header_images',
		'type'     => 'text',
		'priority' => 1,
		) );

	$wp_customize->add_section(
		'accent_color_option',
		array(
			'title'     => __('Theme Color','noteblog'),
			'priority'  => 2
			)
		);

	// Add color scheme setting and control.
	$wp_customize->add_setting( 'color_scheme', array(
		'default'           => 'default',
		'sanitize_callback' => 'noteblog_sanitize_color_scheme',
		'transport'         => 'postMessage',
		) );

	$wp_customize->add_control( 'color_scheme', array(
		'label'    => __( 'Predefined Colors', 'noteblog' ),
		'section'  => 'accent_colors_option',
		'type'     => 'select',
		'choices'  => noteblog_get_color_scheme_choices(),
		'priority' => 3,
		) );

	// Add custom accent color.
	$wp_customize->add_setting( 'accent_color', array(
		'default'           => $current_color_scheme[0],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
		) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'accent_color', array(
		'label'       => __( 'Theme Color', 'noteblog' ),
		'description' => __( 'Applied to highlight elements, buttons and much more.', 'noteblog' ),
		'section'     => 'accent_colors_option',
		'settings'    => 'accent_color',
		) ) );

	//Add section for post option
	$wp_customize->add_section(
		'post_options',
		array(
			'title'     => __('Post Options','noteblog'),
			'priority'  => 300
			)
		);
$wp_customize->add_control( 'header_textcolor', array(
		'section'  => 'color_settings',
		) );
	$wp_customize->add_setting('post_display_option', array(
		'default'        => 'post-excerpt',
		'sanitize_callback' => 'noteblog_sanitize_post_display_option',
		'transport'         => 'refresh'
		));

	$wp_customize->add_control('post_display_types', array(
		'label'      => __('How would you like to dipaly a post on post listing page?', 'noteblog'),
		'section'    => 'post_options',
		'settings'   => 'post_display_option',
		'type'       => 'radio',
		'choices'    => array(
			'post-excerpt' => __('Post excerpt','noteblog'),
			'full-post' => __('Full post','noteblog'),            
			),
		));
}
add_action( 'customize_register', 'noteblog_customize_register' );

/**
 * Register color schemes for noteblog.
 *
 * @return array An associative array of color scheme options.
 */
function noteblog_get_color_schemes() {
	return apply_filters( 'noteblog_color_schemes', array(
		'default' => array(
			'label'  => __( 'Default', 'noteblog' ),
			'colors' => array(
				'#4dbf99',			
				),
			),
		'pink'    => array(
			'label'  => __( 'Pink', 'noteblog' ),
			'colors' => array(
				'#FF4081',				
				),
			),
		'orange'  => array(
			'label'  => __( 'Orange', 'noteblog' ),
			'colors' => array(
				'#FF5722',
				),
			),
		'green'    => array(
			'label'  => __( 'Green', 'noteblog' ),
			'colors' => array(
				'#8BC34A',
				),
			),
		'red'    => array(
			'label'  => __( 'Red', 'noteblog' ),
			'colors' => array(
				'#FF5252',
				),
			),
		'yellow'    => array(
			'label'  => __( 'yellow', 'noteblog' ),
			'colors' => array(
				'#FFC107',
				),
			),
		'blue'   => array(
			'label'  => __( 'Blue', 'noteblog' ),
			'colors' => array(
				'#03A9F4',
				),
			),
		) );
}

if(!function_exists('noteblog_current_color_scheme_default_color')):
/**
 * Get the default hex color value for current color scheme
 *
 *
 * @return array An associative array of current color scheme hex values.
 */
function noteblog_current_color_scheme_default_color(){
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );
	
	$color_schemes       = noteblog_get_color_schemes();

	if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
		return $color_schemes[ $color_scheme_option ]['colors'];
	}

	return $color_schemes['default']['colors'];
}
endif; //noteblog_current_color_scheme_default_color

if ( ! function_exists( 'noteblog_get_color_scheme' ) ) :
/**
 * Get the current noteblog color scheme.
 *
 *
 * @return array An associative array of currently set color hex values.
 */
function noteblog_get_color_scheme() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );
	$accent_color = get_theme_mod('accent_color','#4dbf99');
	$color_schemes       = noteblog_get_color_schemes();

	if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
		$color_schemes[ $color_scheme_option ]['colors'] = array($accent_color);
		return $color_schemes[ $color_scheme_option ]['colors'];
	}

	return $color_schemes['default']['colors'];
}
endif; // noteblog_get_color_scheme

if ( ! function_exists( 'noteblog_get_color_scheme_choices' ) ) :
/**
 * Returns an array of color scheme choices registered for noteblog.
 *
 *
 * @return array Array of color schemes.
 */
function noteblog_get_color_scheme_choices() {
	$color_schemes                = noteblog_get_color_schemes();
	$color_scheme_control_options = array();

	foreach ( $color_schemes as $color_scheme => $value ) {
		$color_scheme_control_options[ $color_scheme ] = $value['label'];
	}

	return $color_scheme_control_options;
}
endif; // noteblog_get_color_scheme_choices

if ( ! function_exists( 'noteblog_sanitize_color_scheme' ) ) :
/**
 * Sanitization callback for color schemes.
 *
 *
 * @param string $value Color scheme name value.
 * @return string Color scheme name.
 */
function noteblog_sanitize_color_scheme( $value ) {
	$color_schemes = noteblog_get_color_scheme_choices();

	if ( ! array_key_exists( $value, $color_schemes ) ) {
		$value = 'default';
	}

	return $value;
}
endif; // noteblog_sanitize_color_scheme

if ( ! function_exists( 'noteblog_sanitize_post_display_option' ) ) :
/**
 * Sanitization callback for post display option.
 *
 *
 * @param string $value post display style.
 * @return string post display style.
 */

function noteblog_sanitize_post_display_option( $value ) {
	if ( ! in_array( $value, array( 'post-excerpt', 'full-post' ) ) )
		$value = 'post-excerpt';

	return $value;
}
endif; // noteblog_sanitize_post_display_option
/**
 * Enqueues front-end CSS for color scheme.
 *
 *
 * @see wp_add_inline_style()
 */
function noteblog_color_scheme_css() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );
	
	$color_scheme = noteblog_get_color_scheme();

	$color = array(
		'accent_color'            => $color_scheme[0],
		);

	$color_scheme_css = noteblog_get_color_scheme_css( $color);

	wp_add_inline_style( 'noteblog-style', $color_scheme_css );
}
add_action( 'wp_enqueue_scripts', 'noteblog_color_scheme_css' );

/**
 * Returns CSS for the color schemes.
 *
 * @param array $colors Color scheme colors.
 * @return string Color scheme CSS.
 */
function noteblog_get_color_scheme_css( $colors ) {
	$colors = wp_parse_args( $colors, array(
		'accent_color'            => '',
		) );

	$css = <<<CSS
	/* Color Scheme */

	/* Accent Color */
	a,a:visited,a:active,a:hover,a:focus,#secondary .widget #recentcomments a, #secondary .widget .rsswidget {
		color: {$colors['accent_color']};
	}

	@media (min-width:767px) {
		.dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, .dropdown-menu > .active > a:focus {	    
			background-color: {$colors['accent_color']} !important;
			color:#fff !important;
		}
		.dropdown-menu .current-menu-item.current_page_item a, .dropdown-menu .current-menu-item.current_page_item a:hover, .dropdown-menu .current-menu-item.current_page_item a:active, .dropdown-menu .current-menu-item.current_page_item a:focus {
			background: {$colors['accent_color']} !important;
			color:#fff !important
		}
	}
	@media (max-width:767px) {
		.dropdown-menu .current-menu-item.current_page_item a, .dropdown-menu .current-menu-item.current_page_item a:hover, .dropdown-menu .current-menu-item.current_page_item a:active, .dropdown-menu .current-menu-item.current_page_item a:focus, .dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, .dropdown-menu > .active > a:focus, .navbar-default .navbar-nav .open .dropdown-menu > li.active > a {
			border-left: 3px solid {$colors['accent_color']};
		}
	}
	.btn, .btn-default:visited, .btn-default:active:hover, .btn-default.active:hover, .btn-default:active:focus, .btn-default.active:focus, .btn-default:active.focus, .btn-default.active.focus {
		background: {$colors['accent_color']};
	}
	.cat-links a, .tags-links a {
		color: {$colors['accent_color']};
	}
	.navbar-default .navbar-nav > li > .dropdown-menu > li > a:hover, .navbar-default .navbar-nav > li > .dropdown-menu > li > a:focus {
		color: #fff;
		background-color: {$colors['accent_color']};
	}
	h5.entry-date a:hover {
		color: {$colors['accent_color']};
	}
	#respond input#submit {
		background-color: {$colors['accent_color']};
		background: {$colors['accent_color']};
	}
	blockquote {
		border-left: 5px solid {$colors['accent_color']};
	}
	.entry-title a:hover,.entry-title a:focus{
		color: {$colors['accent_color']};
	}
	.entry-header .entry-meta::after{
		background: {$colors['accent_color']};
	}
	.readmore-btn, .readmore-btn:visited, .readmore-btn:active, .readmore-btn:hover, .readmore-btn:focus {
		background: {$colors['accent_color']};
	}
	.post-password-form input[type="submit"],.post-password-form input[type="submit"]:hover,.post-password-form input[type="submit"]:focus,.post-password-form input[type="submit"]:active,.search-submit,.search-submit:hover,.search-submit:focus,.search-submit:active {
		background-color: {$colors['accent_color']};
		background: {$colors['accent_color']};
		border-color: {$colors['accent_color']};
	}
	.fa {
		color: {$colors['accent_color']};
	}
	.btn-default{
		border-bottom: 1px solid {$colors['accent_color']};
	}
	.btn-default:hover, .btn-default:focus{
		border-bottom: 1px solid {$colors['accent_color']};
		background-color: {$colors['accent_color']};
	}
	.nav-previous:hover, .nav-next:hover{
		border: 1px solid {$colors['accent_color']};
		background-color: {$colors['accent_color']};
	}
	.next-post a:hover,.prev-post a:hover{
		color: {$colors['accent_color']};
	}
	.posts-navigation .next-post a:hover .fa, .posts-navigation .prev-post a:hover .fa{
		color: {$colors['accent_color']};
	}
	#secondary .widget a:hover,	#secondary .widget a:focus{
		color: {$colors['accent_color']};
	}
	#secondary .widget_calendar tbody a {
		background-color: {$colors['accent_color']};
		color: #fff;
		padding: 0.2em;
	}
	#secondary .widget_calendar tbody a:hover{
		background-color: {$colors['accent_color']};
		color: #fff;
		padding: 0.2em;
	}	
CSS;

return $css;
}

if(! function_exists('noteblog_header_bg_color_css' ) ):
/**
* Set the header background color 
*/
function noteblog_header_bg_color_css(){

	?>

	<style type="text/css">
		.site-header { background: <?php echo esc_attr(get_theme_mod( 'header_bg_color')); ?>; }
		.footer-widgets h3 { color: <?php echo esc_attr(get_theme_mod( 'footer_widget_title_colors')); ?>; }
		.site-footer { background: <?php echo esc_attr(get_theme_mod( 'footer_copyright_background_color')); ?>; }
		.footer-widget-wrapper { background: <?php echo esc_attr(get_theme_mod( 'footer_colors')); ?>; }
		.copy-right-section { color: <?php echo esc_attr(get_theme_mod( 'footer_copyright_text_color')); ?>; }
		#secondary h3.widget-title, #secondary h4.widget-title { color: <?php echo esc_attr(get_theme_mod( 'sidebar_headline_colors')); ?>; }
		.secondary-inner { background: <?php echo esc_attr(get_theme_mod( 'sidebar_background_color')); ?>; }
		#secondary .widget a, #secondary .widget a:focus, #secondary .widget a:hover, #secondary .widget a:active, #secondary .widget #recentcomments a, #secondary .widget #recentcomments a:focus, #secondary .widget #recentcomments a:hover, #secondary .widget #recentcomments a:active, #secondary .widget .rsswidget, #secondary .widget .rsswidget:focus, #secondary .widget .rsswidget:hover, #secondary .widget .rsswidget:active { color: <?php echo esc_attr(get_theme_mod( 'sidebar_link_color')); ?>; }
		.navbar-default,.navbar-default li>.dropdown-menu, .navbar-default .navbar-nav .open .dropdown-menu > .active > a, .navbar-default .navbar-nav .open .dr { background-color: <?php echo esc_attr(get_theme_mod( 'navigation_background_color')); ?>; }
		.home .lh-nav-bg-transform li>.dropdown-menu:after { border-bottom-color: <?php echo esc_attr(get_theme_mod( 'navigation_background_color')); ?>; }
		.navbar-default .navbar-nav>li>a, .navbar-default li>.dropdown-menu>li>a, .navbar-default .navbar-nav>li>a:hover, .navbar-default .navbar-nav>li>a:focus, .navbar-default .navbar-nav>li>a:active, .navbar-default .navbar-nav>li>a:visited, .navbar-default .navbar-nav > .open > a, .navbar-default .navbar-nav > .open > a:hover, .navbar-default .navbar-nav > .open > a:focus { color: <?php echo esc_attr(get_theme_mod( 'navigation_text_color')); ?>; }
		.navbar-default .navbar-brand, .navbar-default .navbar-brand:hover, .navbar-default .navbar-brand:focus { color: <?php echo esc_attr(get_theme_mod( 'navigation_logo_color')); ?>; }
		h1.entry-title, .entry-header .entry-title a, .page .container article h2, .page .container article h3, .page .container article h4, .page .container article h5, .page .container article h6, .single article h1, .single article h2, .single article h3, .single article h4, .single article h5, .single article h6, .page .container article h1, .single article h1, .single h2.comments-title, .single .comment-respond h3#reply-title, .page h2.comments-title, .page .comment-respond h3#reply-title { color: <?php echo esc_attr(get_theme_mod( 'headline_color')); ?>; }
		.single .entry-content, .page .entry-content, .single .entry-summary, .page .entry-summary, .page .post-feed-wrapper p, .single .post-feed-wrapper p, .single .post-comments, .page .post-comments, .single .post-comments p, .page .post-comments p, .single .next-article a p, .single .prev-article a p, .page .next-article a p, .page .prev-article a p, .single thead, .page thead { color: <?php echo esc_attr(get_theme_mod( 'post_content_color')); ?>; }
		.page .container .entry-date, .single-post .container .entry-date, .single .comment-metadata time, .page .comment-metadata time { color: <?php echo esc_attr(get_theme_mod( 'author_line_color')); ?>; }
		.top-widgets { background: <?php echo esc_attr(get_theme_mod( 'top_widget_background_color')); ?>; }
		.top-widgets h3 { color: <?php echo esc_attr(get_theme_mod( 'top_widget_title_color')); ?>; }
		.top-widgets, .top-widgets p { color: <?php echo esc_attr(get_theme_mod( 'top_widget_text_color')); ?>; }
		.bottom-widgets { background: <?php echo esc_attr(get_theme_mod( 'bottom_widget_background_color')); ?>; }
		.bottom-widgets h3 { color: <?php echo esc_attr(get_theme_mod( 'bottom_widget_title_color')); ?>; }
		.frontpage-site-title, .frontpage-site-title:hover, .frontpage-site-title:active, .frontpage-site-title:focus { color: <?php echo esc_attr(get_theme_mod( 'header_image_text_color')) ?>; }
		.frontpage-site-description, .frontpage-site-description:focus, .frontpage-site-description:hover, .frontpage-site-description:active { color: <?php echo esc_attr(get_theme_mod( 'header_image_tagline_color')) ?>; }
		.bottom-widgets, .bottom-widgets p { color: <?php echo esc_attr(get_theme_mod( 'bottom_widget_text_color')); ?>; }
		.footer-widgets, .footer-widgets p { color: <?php echo esc_attr(get_theme_mod( 'footer_widget_text_color')); ?>; }
		.home .lh-nav-bg-transform .navbar-nav>li>a, .home .lh-nav-bg-transform .navbar-nav>li>a:hover, .home .lh-nav-bg-transform .navbar-nav>li>a:active, .home .lh-nav-bg-transform .navbar-nav>li>a:focus, .home .lh-nav-bg-transform .navbar-nav>li>a:visited { color: <?php echo esc_attr(get_theme_mod( 'navigation_frontpage_menu_color')); ?>; }
		.home .lh-nav-bg-transform.navbar-default .navbar-brand, .home .lh-nav-bg-transform.navbar-default .navbar-brand:hover, .home .lh-nav-bg-transform.navbar-default .navbar-brand:active, .home .lh-nav-bg-transform.navbar-default .navbar-brand:focus, .home .lh-nav-bg-transform.navbar-default .navbar-brand:hover { color: <?php echo esc_attr(get_theme_mod( 'navigation_frontpage_logo_color')); ?>; }
		#secondary h4.widget-title { background-color: <?php echo esc_attr(get_theme_mod( 'background_elements_color')); ?>; }
		.navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > a:hover, .navbar-default .navbar-nav > .active > a:focus{color: <?php echo esc_attr(get_theme_mod( 'navigation_text_color')); ?>; }
		#secondary, #secondary .widget, #secondary p{color: <?php echo esc_attr(get_theme_mod( 'sidebar_text_color')); ?>; }
		.footer-widgets, .footer-widgets p{color: <?php echo esc_attr(get_theme_mod( 'footer_widget_text_colors')); ?>; }
		.footer-widgets a, .footer-widgets li a{color: <?php echo esc_attr(get_theme_mod( 'footer_widget_link_colors')); ?>; }
		.copy-right-section{border-top: 1px solid <?php echo esc_attr(get_theme_mod( 'footer_copyright_border_color')); ?>; }
		.copy-right-section{border-top: 1px solid <?php echo esc_attr(get_theme_mod( 'footer_copyright_border_color')); ?>; }
		.single .entry-content a, .page .entry-content a, .single .post-comments a, .page .post-comments a, .single .next-article a, .single .prev-article a, .page .next-article a, .page .prev-article a {color: <?php echo esc_attr(get_theme_mod( 'post_link_color')); ?>; }
		.single .post-content, .page .post-content, .single .comments-area, .page .comments-area, .single .post-comments, .page .single-post-content, .single .post-comments .comments-area, .page .post-comments .comments-area, .single .next-article a, .single .prev-article a, .page .next-article a, .page .prev-article a, .page .post-comments {background: <?php echo esc_attr(get_theme_mod( 'post_background_color')); ?>; }
		.article-grid-container article{background: <?php echo esc_attr(get_theme_mod( 'post_feed_post_background')); ?>; }
		.article-grid-container .post-feed-wrapper p{color: <?php echo esc_attr(get_theme_mod( 'post_feed_post_text')); ?>; }
		.post-feed-wrapper .entry-header .entry-title a{color: <?php echo esc_attr(get_theme_mod( 'post_feed_post_headline')); ?>; }
		.article-grid-container h5.entry-date, .article-grid-container h5.entry-date a{color: <?php echo esc_attr(get_theme_mod( 'post_feed_post_date_noimage')); ?>; }
		.article-grid-container .post-thumbnail-wrap .entry-date{color: <?php echo esc_attr(get_theme_mod( 'post_feed_post_date_withimage')); ?>; }
		.blog .next-post a, .blog .prev-post a{background: <?php echo esc_attr(get_theme_mod( 'post_feed_post_button')); ?>; }
		.blog .next-post a, .blog .prev-post a, .blog .next-post a i.fa, .blog .prev-post a i.fa, .blog .posts-navigation .next-post a:hover .fa, .blog .posts-navigation .prev-post a:hover .fa{color: <?php echo esc_attr(get_theme_mod( 'post_feed_post_button_text')); ?>; }
		@media (max-width:767px){	
		.home .lh-nav-bg-transform { background-color: <?php echo esc_attr(get_theme_mod( 'navigation_background_color')); ?> !important; }
		.navbar-default .navbar-nav .open .dropdown-menu>li>a, .navbar-default .navbar-nav .open .dropdown-menu>li>a, .navbar-default .navbar-nav .open .dropdown-menu>li>a,.navbar-default .navbar-nav .open .dropdown-menu>li>a,:focus, .navbar-default .navbar-nav .open .dropdown-menu>li>a,:visited, .home .lh-nav-bg-transform .navbar-nav>li>a, .home .lh-nav-bg-transform .navbar-nav>li>a:hover, .home .lh-nav-bg-transform .navbar-nav>li>a:visited, .home .lh-nav-bg-transform .navbar-nav>li>a:focus, .home .lh-nav-bg-transform .navbar-nav>li>a:active, .navbar-default .navbar-nav .open .dropdown-menu>li>a:active, .navbar-default .navbar-nav .open .dropdown-menu>li>a:focus, .navbar-default .navbar-nav .open .dropdown-menu>li>a:hover, .navbar-default .navbar-nav .open .dropdown-menu>li>a:visited, .navbar-default .navbar-nav .open .dropdown-menu > .active > a, .navbar-default .navbar-nav .open .dropdown-menu > .active > a:hover, .navbar-default .navbar-nav .open .dropdown-menu > .active > a:focus, .navbar-default .navbar-nav .open .dropdown-menu > .active > a:active, .navbar-default .navbar-nav .open .dropdown-menu > .active > a:hover {color: <?php echo esc_attr(get_theme_mod( 'navigation_text_color')); ?>; }
		.home .lh-nav-bg-transform.navbar-default .navbar-brand, .home .lh-nav-bg-transform.navbar-default .navbar-brand:hover, .home .lh-nav-bg-transform.navbar-default .navbar-brand:focus, .home .lh-nav-bg-transform.navbar-default .navbar-brand:active { color: <?php echo esc_attr(get_theme_mod( 'navigation_logo_color')); ?>; }
		.navbar-default .navbar-toggle .icon-bar, .navbar-default .navbar-toggle:focus .icon-bar, .navbar-default .navbar-toggle:hover .icon-bar{ background-color: <?php echo esc_attr(get_theme_mod( 'navigation_text_color')); ?>; }
		.navbar-default .navbar-nav .open .dropdown-menu > li > a {border-left-color: <?php echo esc_attr(get_theme_mod( 'navigation_text_color')); ?>; }
		}
	</style>
	<?php }
	add_action( 'wp_head', 'noteblog_header_bg_color_css' );
	endif;



/**
 * Binds JS listener to make Customizer color_scheme control.
 *
 * Passes color scheme data as colorScheme global.
 *
 */
function noteblog_customize_control_js() {
	wp_enqueue_script( 'noteblog-color-scheme-control', get_template_directory_uri() . '/js/color-scheme-control.js', array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), '20141216', true );
	wp_localize_script( 'noteblog-color-scheme-control', 'colorScheme', noteblog_get_color_schemes() );
}
add_action( 'customize_controls_enqueue_scripts', 'noteblog_customize_control_js' );


/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function noteblog_customize_preview_js() {
	wp_enqueue_script( 'noteblog_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'noteblog_customize_preview_js' );

/**
 * Output an Underscore template for generating CSS for the color scheme.
 *
 * The template generates the css dynamically for instant display in the Customizer
 * preview.
 *
 */

